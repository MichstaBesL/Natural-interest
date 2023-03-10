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
 *
 *
 * 本文件是框架系统文件，二次开发时不建议修改本文件
 *
 * */



// 同步登陆接口
class Sso extends \Soulcms\Common
{

	public function index() {


        switch (\Soulcms\Service::L('input')->get('action')) {

            case 'logout': // 前台退出登录

                header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
                \Soulcms\Service::L('input')->set_cookie('member_uid', 0, -30000);
                \Soulcms\Service::L('input')->set_cookie('admin_login_member', 0, -30000);
                \Soulcms\Service::L('input')->set_cookie('member_cookie', 0, -30000);
                break;

            case 'login': // 前台同步登录

                $code = mys_authcode(\Soulcms\Service::L('input')->get('code'), 'DECODE');
                !$code && $this->_jsonp(0, '解密失败');

                list($uid, $salt) = explode('-', $code);
                (!$uid || !$salt) && $this->_jsonp(0, '格式错误');

                $member = \Soulcms\Service::M()->db->table('member')->select('password,salt')->where('id', $uid)->get()->getRowArray();
                !$member && $this->_jsonp(0, '账号不存在');
                if ($salt != $member['salt']) {
                    $this->_jsonp(0, '账号验证失败');
                }

                header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
                $expire = \Soulcms\Service::L('input')->get('remember') ? 8640000 : SITE_LOGIN_TIME;
                \Soulcms\Service::L('input')->set_cookie('member_uid', $uid, $expire);
                \Soulcms\Service::L('input')->set_cookie('member_cookie', substr(md5(SYS_KEY.$member['password']), 5, 20), $expire);

                break;

            case 'alogin': // 后台登录授权

                $code = mys_authcode(\Soulcms\Service::L('input')->get('code'), 'DECODE');
                !$code && $this->_jsonp(0, '解密失败');

                list($uid, $password) = explode('-', $code);

                $admin = \Soulcms\Service::L('cache')->get_data('admin_login_member');
                !$admin && $this->_jsonp(0, '缓存失败');

                $mycode = md5($admin['id'].$admin['password']);
                $password != $mycode && $this->_jsonp(0, '验证失败');

                // 存储状态
                header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
                \Soulcms\Service::L('input')->set_cookie('admin_login_member', $uid.'-'.$admin['id'], 3600);
                $this->session()->setTempdata('admin_login_member_code', md5($uid.$admin['id'].$admin['password']), 3600);
                break;

            case 'slogin': // 后台登录其他站点

                $code = mys_authcode(\Soulcms\Service::L('input')->get('code'), 'DECODE');
                !$code && $this->_jsonp(0, '解密失败');

                list($uid, $password) = explode('-', $code);

                $admin = \Soulcms\Service::L('cache')->init('', 'site')->get('admin_login_site');
                !$admin && $this->_jsonp(0, '缓存失败');

                $mycode = md5($admin['uid'].$admin['password']);
                $password != $mycode && $this->_jsonp(0, '验证失败');

                // 存储状态
                header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
                \Soulcms\Service::L('input')->set_cookie('admin_login_member', 0, -3600);
                $this->session()->set('admin_login_member_code', 0, -3600);
                $this->session()->set('uid', $uid);
                $this->session()->set('admin', $uid);
                \Soulcms\Service::L('input')->set_cookie('member_uid', $uid, SITE_LOGIN_TIME);
                \Soulcms\Service::L('input')->set_cookie('member_cookie', substr(md5(SYS_KEY . $admin['password']), 5, 20), SITE_LOGIN_TIME);
                break;

        }

        $this->_jsonp(1, 'ok');

		exit;
	}

}
