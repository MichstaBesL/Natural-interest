<?php namespace Soulcms\Controllers\Api;

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
 * http://www.tianruixinxi.com
 *
 * 本文件是框架系统文件，二次开发时不建议修改本文件
 *
 * */



// 快捷登录接口
class Oauth extends \Soulcms\Common
{

    /**
     * 快捷登录
     */
    public function index() {

        $name = mys_safe_replace(\Soulcms\Service::L('input')->get('name'));
        $type = mys_safe_replace(\Soulcms\Service::L('input')->get('type'));
        $action = mys_safe_replace(\Soulcms\Service::L('input')->get('action'));

        // 非授权登录时必须验证登录状态
        if ($type != 'login' && !$this->uid) {
            $this->_msg(0, mys_lang('你还没有登录'));
        }

        // 请求参数
        $appid = $this->member_cache['oauth'][$name]['id'];
        $appkey = $this->member_cache['oauth'][$name]['value'];
        $callback_url = ROOT_URL.'index.php?s=api&c=oauth&m=index&action=callback&name='.$name.'&type='.$type;

        switch ($name) {

            case 'weixin':

                if ($action == 'callback') {
                    // 表示回调返回
                    if (isset($_REQUEST['code'])) {
                        // 获取access_token
                        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appkey.'&code='.$_REQUEST['code'].'&grant_type=authorization_code';
                        $token = json_decode(mys_catcher_data($url), true);
                        !$token && exit($this->_msg(0, mys_lang('无法获取到远程信息')));
                        $token['errmsg'] && $this->_msg(0, $token['errmsg']);
                        // 获取用户信息
                        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$token['access_token'].'&openid='.$token['openid'];
                        $user = json_decode(mys_catcher_data($url), true);
                        !$user && exit($this->_msg(0, mys_lang('无法获取到用户信息')));
                        $user['errmsg'] && $this->_msg(0, $user['errmsg']);
                        $rt = \Soulcms\Service::M('member')->insert_oauth($this->uid, $type, [
                            'oid' => $token['openid'],
                            'oauth' => 'weixin',
                            'avatar' => $user['headimgurl'],
                            'nickname' => mys_emoji2html($user['nickname']),
                            'expire_at' => SYS_TIME,
                            'access_token' => 0,
                            'refresh_token' => $token['refresh_token'],
                        ]);
                        if (!$rt['code']) {
                            $this->_msg(0, $rt['msg']);exit;
                        } else {
                            mys_redirect($rt['msg']);
                        }
                    } else {
                        $this->_msg(0, mys_lang('回调参数code不存在'));exit;
                    }
                } else {
                    // 跳转授权页面
                    $url = 'https://open.weixin.qq.com/connect/qrconnect?appid='.$appid.'&redirect_uri='.urlencode($callback_url).'&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect';
                    mys_redirect($url);
                }
                break;

            case 'qq':

                define("CLASS_PATH", FCPATH."ThirdParty/Qq/");
                require FCPATH.'ThirdParty/Qq/QC.class.php';
                $qc = new \QC();
                if ($action == 'callback') {
                    // 表示回调返回
                    if (isset($_REQUEST['code'])) {
                        $rt = $qc->qq_callback($appid, $appkey, $callback_url, $_REQUEST['code']);
                        if (is_array($rt)) {
                            // 回调成功
                            $open = $qc->get_openid($rt['access_token']);
                            !is_array($open) && exit($this->_msg(0, $open)); // 获取失败
                            $user = $qc->init($appid, $rt['access_token'], $open['openid'])->get_user_info();
                            if (is_array($user)) {
                                // 入库oauth表
                                $rt = \Soulcms\Service::M('member')->insert_oauth($this->uid, $type, [
                                    'oid' => $open['openid'],
                                    'oauth' => 'qq',
                                    'avatar' => $user['figureurl_qq_2'] ? $user['figureurl_qq_2'] : $user['figureurl_qq_1'],
                                    'nickname' => mys_emoji2html($user['nickname']),
                                    'expire_at' => SYS_TIME,
                                    'access_token' => 0,
                                    'refresh_token' => $rt['refresh_token'],
                                ]);
                                if (!$rt['code']) {
                                    $this->_msg(0, $rt['msg']);exit;
                                } else {
                                    mys_redirect($rt['msg']);
                                }
                            } else {
                                $this->_msg(0, mys_lang('获取QQ用户信息失败: '.$user));exit;
                            }
                        } else {
                            $this->_msg(0, $rt);exit;
                        }
                    } else {
                        $this->_msg(0, mys_lang('回调参数code不存在'));exit;
                    }
                } else {
                    // 跳转授权页面
                    $rt = $qc->qq_login($appid, $callback_url);
                    !$rt = $this->_msg(0, mys_lang('授权执行失败'));
                }
                break;


            case 'weibo':

                define("WB_AKEY", $appid);
                define("WB_SKEY", $appkey);
                require FCPATH.'ThirdParty/Weibo/saetv2.ex.class.php';
                $o = new \SaeTOAuthV2(WB_AKEY, WB_SKEY);
                if ($action == 'callback') {
                    // 表示回调返回
                    if (isset($_REQUEST['code'])) {
                        $keys = [];
                        $keys['code'] = $_REQUEST['code'];
                        $keys['redirect_uri'] = $callback_url;
                        $token = $o->getAccessToken('code', $keys);
                        if (is_array($token)) {
                            // 回调成功
                            $c = new \SaeTClientV2(WB_AKEY, WB_SKEY, $token['access_token']);
                            $user = $c->show_user_by_id($token['uid']); //根据ID获取用户等基本信息
                            if ($user) {
                                // 入库oauth表
                                $rt = \Soulcms\Service::M('member')->insert_oauth($this->uid, $type, [
                                    'oid' => $token['uid'],
                                    'oauth' => 'weibo',
                                    'avatar' => $user['avatar_large'] ? $user['avatar_large'] : $user['profile_image_url'],
                                    'nickname' => mys_emoji2html($user['name']),
                                    'expire_at' => SYS_TIME,
                                    'access_token' => 0,
                                    'refresh_token' => '',
                                ]);
                                if (!$rt['code']) {
                                    $this->_msg(0, $rt['msg']);exit;
                                } else {
                                    mys_redirect($rt['msg']);
                                }
                            } else {
                                $this->_msg(0, mys_lang('获取微博用户信息失败'));exit;
                            }
                        } else {
                            // 回调失败
                            $this->_msg(0, $token);exit;
                        }
                    } else {
                        $this->_msg(0, mys_lang('回调参数code不存在'));exit;
                    }
                } else {
                    // 跳转授权页面
                    mys_redirect($o->getAuthorizeURL($callback_url));
                }
                break;

            case 'wechat':
                // 微信公众号
                if (!mys_is_app('weixin')) {
                    $this->_msg(0, mys_lang('没有安装微信应用插件'));
                }
                //

                \Soulcms\Service::C()->init_file('weixin');
                $rt = \Soulcms\Service::M('user', 'weixin')->qrcode_bang($this->member);
                if (!$rt['code']) {
                    $this->_msg(0, $rt['msg']);
                } else {
                    // 获取返回页面
                    $url = \Soulcms\Service::L('Security')->xss_clean($_GET['back'] ? urldecode($_GET['back']) : $_SERVER['HTTP_REFERER']);
                    if (!$url || strpos($url, 'login') !== false ) {
                        $url = $this->uid ? mys_member_url('account/oauth') : mys_member_url('home/index');
                    }

                    \Soulcms\Service::V()->admin();
                    \Soulcms\Service::V()->assign([
                        'back_url' => $url,
                        'qrcode_url' => $rt['msg'],
                        'notify_url' => '/index.php?s=api&c=oauth&m=wxbang&ep='.$rt['data']['action_info']['scene']['scene_str'],
                    ]);
                    \Soulcms\Service::V()->display('api_weixin_bang.html');
                }
                // 跳转微信公众号
                break;

            default:
                exit('未定义的接口');

        }
    }

    // 微信公众号绑定
    public function wxbang() {

        if (!$this->uid) {
            $ep = mys_safe_replace($_GET['ep']);
            $rt = \Soulcms\Service::M()->table('member_oauth')->where('refresh_token', $ep)->where('uid>0')->where('oauth', 'wechat')->getRow();
            if ($rt) {
                // 为他登录
                // 存储cookie
                $this->uid = $rt['uid'];
                $this->member = \Soulcms\Service::M('member')->get_member($this->uid);
                \Soulcms\Service::M('member')->save_cookie($this->member);

                $this->_json(1, 'ok', [
                    'sso' => \Soulcms\Service::M('member')->sso($this->member, 1)
                ]);
            }
        } else {
            $rt = \Soulcms\Service::M()->table('member_oauth')->where('uid', $this->uid)->where('oauth', 'wechat')->getRow();
        }

        if (!$rt) {
            $this->_json(0, '');
        } else {
            // 绑定成功更新头像
            list($cache_path) = mys_avatar_path();
            if (!is_file($cache_path.$this->uid.'.jpg')) {
                // 没有头像下载头像
                $img = mys_catcher_data($rt['avatar']);
                if (strlen($img) > 20) {
                    @file_put_contents($cache_path.$this->uid.'.jpg', $img);
                }
            }
            $this->_json($rt['access_token'] ? 0 : 1, 'ok', $rt);
        }
    }

    // 微信公众号登录
    public function wxmp() {

        exit;
    }

    // 微信小程序登录
    public function xcx() {


    }
}
