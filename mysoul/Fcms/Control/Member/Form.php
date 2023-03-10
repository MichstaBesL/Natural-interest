<?php namespace Soulcms\Member;

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


// 网站表单操作类 基于 Ftable
class Form extends \Soulcms\Table
{
    protected $form;
    protected $_is_post;
    protected $_is_edit;
    protected $_is_delete;

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        // 判断表单是否操作
        $cache = \Soulcms\Service::L('cache')->get('form-'.SITE_ID);
        $this->form = $cache[\Soulcms\Service::L('Router')->class];
        !$this->form && $this->_admin_msg(0, mys_lang('网站表单【%s】不存在', \Soulcms\Service::L('Router')->class));
        // 支持附表存储
        $this->is_data = 1;
        // 模板前缀(避免混淆)
        $this->tpl_prefix = 'form_';
        // 单独模板命名
        $this->tpl_name = $this->form['table'];
        // 表单显示名称
        $this->name = mys_lang('网站表单（%s）', $this->form['name']);
        $sysfield = ['inputtime', 'displayorder'];
        // 初始化数据表
        $this->_init([
            'table' => SITE_ID.'_form_'.$this->form['table'],
            'field' => $this->form['field'],
            'sys_field' => $sysfield,
            'date_field' => 'inputtime',
            'show_field' => 'title',
            'list_field' => $this->form['setting']['list_field'],
            'order_by' => 'inputtime desc',
            'where_list' => 'uid='.$this->uid,
        ]);
        $this->edit_where = $this->delete_where = 'uid='.$this->uid;
        // 无权限发布表单
        if (!mys_member_auth(
            $this->member_authid,
            $this->member_cache['auth_site'][SITE_ID]['form'][$this->form['table']]['add'])
        ) {
            $this->_is_post = 0;
        } else {
            $this->_is_post = 1;
        }
        // 修改权限
        if (!mys_member_auth(
            $this->member_authid,
            $this->member_cache['auth_site'][SITE_ID]['form'][$this->form['table']]['edit'])
        ) {
            $this->_is_edit = 0;
        } else {
            $this->_is_edit = 1;
        }
        // 删除权限
        if (!mys_member_auth(
            $this->member_authid,
            $this->member_cache['auth_site'][SITE_ID]['form'][$this->form['table']]['del'])
        ) {
            $this->_is_delete = 0;
        } else {
            $this->_is_delete = 1;
        }
        // 是否有验证码
        $this->is_post_code = mys_member_auth(
            $this->member_authid,
            $this->member_cache['auth_site'][SITE_ID]['form'][$this->form['table']]['code']
        );

        \Soulcms\Service::V()->assign([
            'field' => $this->init['field'],
            'form_list' => $cache,
            'form_name' => $this->form['name'],
            'form_table' => $this->form['table'],
            'is_delete' => $this->_is_delete,
            'is_post' => $this->_is_post,
            'is_edit' => $this->_is_edit,
            'is_post_code' => $this->is_post_code,
        ]);


    }

    // 添加表单内容
    protected function _Member_Add() {
        list($tpl) = $this->_Post(0);
        \Soulcms\Service::V()->display($tpl);
    }

    // 修改表单内容
    protected function _Member_Edit() {
        $id = intval(\Soulcms\Service::L('input')->get('id'));
        list($tpl, $data) = $this->_Post($id);
        !$data && $this->_msg(0, mys_lang('数据不存在: '.$id));
        \Soulcms\Service::V()->display($tpl);
    }

    // 查看表单列表
    protected function _Member_List() {
        list($tpl) = $this->_List();
        \Soulcms\Service::V()->display($tpl);
    }

    // 删除表单内容
    protected function _Member_Del() {
        $this->_Del(
            \Soulcms\Service::L('input')->get_post_ids(),
            null,
            function ($rows) {
                // 对应删除提醒
                foreach ($rows as $t) {
                    \Soulcms\Service::M('member')->delete_admin_notice('form/'.$this->form['table'].'_verify/edit:id/'.$t['id'], SITE_ID);
                    \Soulcms\Service::M('member')->delete_admin_notice('form/'.$this->form['table'].'/edit:id/'.$t['id'], SITE_ID);
                    \Soulcms\Service::L('cache')->clear('from_'.$this->form['table'].'_show_id_'.$t['id']);
                }

            },
            \Soulcms\Service::M()->dbprefix($this->init['table'])
        );
    }


    // 后台批量保存排序值
    protected function _Member_Order() {
        $this->_Display_Order(
            intval(\Soulcms\Service::L('input')->get('id')),
            intval(\Soulcms\Service::L('input')->get('value'))
        );
    }

    /**
     * 获取内容
     * $id      内容id,新增为0
     * */
    protected function _Data($id = 0) {

        $data = parent::_Data($id);
        if ($data && $data['uid'] != $this->uid) {
            return [];
        }

        return $data;
    }

    // 格式化保存数据 保存之前
    protected function _Format_Data($id, $data, $old) {


        // 审核状态
        $is_verify = mys_member_auth(
            $this->member_authid,
            $this->member_cache['auth_site'][SITE_ID]['form'][$this->form['table']]['verify']
        );
        $data[1]['status'] = $is_verify ? 0 : 1;


        // 新增数据
        if (!$old) {
            // 判断日发布量
            $day_post = $this->_member_value(
                $this->member_authid,
                $this->member_cache['auth_site'][SITE_ID]['form'][$this->form['table']]['day_post']
            );
            if ($day_post && \Soulcms\Service::M()->db
                    ->table($this->init['table'])
                    ->where('uid', $this->uid)
                    ->where('DATEDIFF(from_unixtime(inputtime),now())=0')
                    ->countAllResults() >= $day_post) {
                $this->_json(0, mys_lang('每天发布数量不能超过%s个', $day_post));
            }

            // 判断发布总量
            $total_post = $this->_member_value(
                $this->member_authid,
                $this->member_cache['auth_site'][SITE_ID]['form'][$this->form['table']]['total_post']
            );
            if ($total_post && \Soulcms\Service::M()->db
                    ->table($this->init['table'])
                    ->where('uid', $this->uid)
                    ->countAllResults() >= $total_post) {
                $this->_json(0, mys_lang('发布数量不能超过%s个', $total_post));
            }

            // 默认数据
            $data[0]['uid'] = $data[1]['uid'] = (int)$this->member['uid'];
            $data[1]['author'] = $this->member['username'];
            $data[1]['inputip'] = \Soulcms\Service::L('input')->ip_address();
            $data[1]['inputtime'] = SYS_TIME;
            $data[1]['tableid'] = 0;
            $data[1]['displayorder'] = 0;
        }


        return $data;
    }

    /**
     * 保存内容
     * $id      内容id,新增为0
     * $data    提交内容数组,留空为自动获取
     * $func    格式化提交的数据
     * */
    protected function _Save($id = 0, $data = [], $old = [], $func = null, $func2 = null) {

        return parent::_Save($id, $data, $old, null,
            function ($id, $data, $old) {
                if (!$old) {
                    // 首次 发布

                    // 挂钩点
                    \Soulcms\Hooks::trigger('form_post_after', $data);

                    // 提醒通知
                    if ($this->form['setting']['notice']['use']) {
                        if ($this->form['setting']['notice']['username']) {
                            $user = mys_member_username_info($this->form['setting']['notice']['username']);
                            if (!$user) {
                                log_message('error', '网站表单【'.$this->form['name'].'】已开启通知提醒，但通知人账号['.$this->form['setting']['notice']['username'].']有误');
                            } else {
                                \Soulcms\Service::L('Notice')->send_notice_user('form_'.$this->form['table'].'_post', $user['id'], mys_array2array($data[1], $data[0]), $this->form['setting']['notice']);
                            }
                        } else {
                            log_message('error', '网站表单【'.$this->form['name'].'】已开启通知提醒，但未设置通知人');
                        }
                    }

                }
                if (!$data[1]['status']) {
                    // 审核
                    \Soulcms\Service::M('member')->admin_notice(SITE_ID, 'content', $this->member, mys_lang('%s提交审核', $this->form['name']), 'form/'.$this->form['table'].'_verify/edit:id/'.$data[1]['id'], SITE_ID);
                    $this->_json(1, mys_lang('操作成功，等待管理员审核'), ['url' => $this->form['setting']['rt_url']]);
                }
                $this->_json(1, mys_lang('操作成功'));
            }
        );
    }

}
