<?php namespace Soulcms\Library;

/* *
 *
 * Copyright [2019]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 *
 * 本文件是框架系统文件，二次开发时不建议修改本文件
 *
 * */


/**
 * 用户通知处理
 */

class Notice {

    /**
     * 发送通知动作（按用户设置）
     *
     * $name    动作名称
     * $data    传入参数
     */
    public function send_notice($name, $data) {

        if (!\Soulcms\Service::C()->member_cache['notice'][$name]) {
            return; // 没有配置通知
        }
        // 当前的信息变量
        $data['sys_time'] = SYS_TIME;
        $data['ip_address'] = \Soulcms\Service::L('input')->ip_address_info();
        // 加入队列并执行
        $rt = \Soulcms\Service::M('cron')->add_cron(SITE_ID, 'notice', [
            'name' => $name,
            'data' => $data,
            'config' => \Soulcms\Service::C()->member_cache['notice'][$name],
        ]);
        if (!$rt['code']) {
            log_message('error', '通知任务注册失败：'.$rt['msg']);
        }

        return;
    }

    /**
     * 发送通知动作（按自定义位置设置）
     *
     * $name    动作名称
     * $data    传入参数
     */
    public function send_notice_user($name, $uid, $data, $config) {

        if (!$config) {
            return; // 没有配置通知
        }

        $data['uid'] = $uid;
        // 当前的信息变量
        $data['sys_time'] = SYS_TIME;
        $data['ip_address'] = \Soulcms\Service::L('input')->ip_address_info();
        // 加入队列并执行
        $rt = \Soulcms\Service::M('cron')->add_cron(SITE_ID, 'notice', [
            'name' => $name,
            'data' => $data,
            'config' => $config,
        ]);
        if (!$rt['code']) {
            log_message('error', '通知任务注册失败：'.$rt['msg']);
        }

        return;
    }

    // 来至队列中执行
    public function cron_notice($siteid, $value) {

        $error = [];
        if (!$value['data']['uid']) {
            return [['用户uid参数为空，任务不能执行'], $value];
        }

        // 微信通知
        if ($value['config']['weixin']) {
            $rt = $this->_get_tpl_content($siteid, $value['name'], 'weixin', $value['data']);
            if (!$rt['code']) {
                $error[] = $rt['msg'];
            } else {
                $xml = $this->_xml_array($rt['msg']);
                if (!$xml || !isset($xml['xml']) || !$xml['xml']) {
                    $error[] = 'xml解析失败，检查文件格式是否正确：'.$value['name'].'.html';
                } else {
                    $content = $xml['xml'];
                    $rt = \Soulcms\Service::M('member')->wexin_template($value['data']['uid'], $content['id'], $content['param'], $content['url']);
                    if (!$rt['code']) {
                        $error[] = '微信消息执行错误：'.$rt['msg'];
                    } else {
                        // 成功
                        unset($value['config']['weixin']);
                    }
                }
            }

        }

        // 短信通知
        if ($value['config']['mobile']) {
            $phone = $value['data']['phone'];
            if (!$phone) {
                $member = mys_member_info($value['data']['uid']);
                $phone = $member['phone'];
            }
            if (!$phone) {
                $error[] = 'phone参数为空，不能发送短信';
            } else {
                $rt = $this->_get_tpl_content($siteid, $value['name'], 'mobile', $value['data']);
                if (!$rt['code']) {
                    $error[] = $rt['msg'];
                } else {
                    $content = $rt['msg'];
                    $rt = \Soulcms\Service::M('member')->sendsms_text($phone, $content);
                    if (!$rt['code']) {
                        $error[] = '短信通知执行错误：'.$rt['msg'];
                    } else {
                        // 成功
                        unset($value['config']['mobile']);
                    }
                }
            }

        }

        // 站内消息通知
        if ($value['config']['notice']) {
            $rt = $this->_get_tpl_content($siteid, $value['name'], 'mobile', $value['data']);
            if (!$rt['code']) {
                $error[] = $rt['msg'];
            } else {
                $content = $rt['msg'];
                \Soulcms\Service::M('member')->notice($value['data']['uid'], max((int)$value['data']['type'], 1), $content, $value['data']['url']);
                // 成功
                unset($value['config']['notice']);
            }
        }

        // 邮件通知
        if ($value['config']['email']) {
            $email = $value['data']['email'];
            if (!$email) {
                $member = mys_member_info($value['data']['uid']);
                $email = $member['email'];
            }
            if (!$email) {
                $error[] = 'email参数为空，不能发送邮件';
            } else {
                $rt = $this->_get_tpl_content($siteid, $value['name'], 'email', $value['data']);
                if (!$rt['code']) {
                    $error[] = $rt['msg'];
                } else {
                    $title = '';
                    $content = $rt['msg'];
                    if (preg_match('/<title>(.+)<\/title>/U', $content, $mt)) {
                        $title = $mt[1];
                        $content = str_replace($mt[0], '', $content);
                    }
                    $rt = \Soulcms\Service::M('member')->sendmail($email, $title ? $title : '通知', $content);
                    if (!$rt['code']) {
                        $error[] = '邮件发送失败：'.$rt['msg'];
                    } else {
                        // 成功
                        unset($value['config']['email']);
                    }
                }

            }
        }

        return [$error, $error ? $value : []];
    }

    // 获取通知模板内容
    private function _get_tpl_content($siteid, $name, $type, $data) {


        if ($siteid > 1) {
            $my = \Soulcms\Service::L('html')->get_webpath($siteid, 'site', 'config/notice/'.$type.'/'.$name.'.html');
            $my = is_file($my) ? file_get_contents($my) : '';
        } else {
            $my = '';
        }

        $content = $my ? $my : file_get_contents(ROOTPATH.'config/notice/'.$type.'/'.$name.'.html');
        if (!$content) {
            return mys_return_data(0, '模板不存在【config/notice/'.$type.'/'.$name.'.html】');
        }

        ob_start();
        extract($data, EXTR_PREFIX_SAME, 'data');
        $file = \Soulcms\Service::V()->code2php($content);
        require $file;
        $code = ob_get_clean();

        return mys_return_data(1, $code);
    }

    // xml转换数组
    private function _xml_array($xml) {

        $reg = "/<(\\w+)[^>]*?>(.*?)<\\/\\1>/Us";
        if(preg_match_all($reg, $xml, $matches))
        {
            $count = mys_count($matches[0]);
            $arr = array();
            for($i = 0; $i < $count; $i++)
            {
                $key = $matches[1][$i];
                $val = $this->_xml_array( $matches[2][$i] );  // 递归
                if(array_key_exists($key, $arr))
                {
                    if(is_array($arr[$key]))
                    {
                        if(!array_key_exists(0,$arr[$key]))
                        {
                            $arr[$key] = array($arr[$key]);
                        }
                    }else{
                        $arr[$key] = array($arr[$key]);
                    }
                    $arr[$key][] = $val;
                }else{
                    $arr[$key] = $val;
                }
            }
            return $arr;
        }else{
            return $xml;
        }

    }

}

