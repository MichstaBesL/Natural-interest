<?php namespace Soulcms\Controllers\Admin;

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



class Api extends \Soulcms\Common
{

    // 添加后台面板页面
    public function add_main_table() {

        $table = mys_safe_filename($_GET['table']);
        $tables = $this->_main_table();
        if (!$tables[$table]) {
            $this->_json(0, mys_lang('自定义面板[%s]不存在', $table));
        }

        $this->_json(1, \Soulcms\Service::M('auth')->edit_main_table($table, $tables[$table]));
    }

    // 跳转首页
    public function gohome() {
        mys_redirect('index.php');
    }


    // 测试字段回调方法
    public function field_call() {

        $call = mys_safe_replace(\Soulcms\Service::L('input')->get('name'));
        if (!$call) {
            $this->_json(0, mys_lang('没有填写函数方法'));
        }

        if (strpos($call, '_') === 0) {
            if (method_exists(\Soulcms\Service::L('form'), $call)) {
                $this->_json(1, mys_lang('定义成功'));
            } else {
                $this->_json(0, 'form类方法【'.$call.'】未定义');
            }
        } else {
            if (function_exists($call)) {
                $this->_json(1, mys_lang('定义成功'));
            } else {
                $this->_json(0, '函数【'.$call.'】未定义');
            }
        }
    }

    // 通知跳转
    public function notice() {

        $id = (int)\Soulcms\Service::L('input')->get('id');
        $data = \Soulcms\Service::M()->table('admin_notice')->get($id);
        !$data && $this->_admin_msg(0, mys_lang('该数据不存在'));

        // 权限判断
        if (!isset($this->admin['roleid'][1])) {
            if ($data['to_uid'] && $data['to_uid'] != $this->uid) {
                $this->_admin_msg(0, mys_lang('您无权限执行'));
            } elseif ($data['to_rid'] && !isset($this->admin['roleid'][$data['to_rid']])) {
                $this->_admin_msg(0, mys_lang('您无权限执行'));
            }
        }

        list($uri, $param) = explode(':', $data['uri']);
        $url = ADMIN_URL.\Soulcms\Service::L('Router')->url($uri);
        $param && $url.= '&'.http_build_query(mys_rewrite_decode($param, '/'));

        // 标记为已经查看
        if (!$data['status']) {
            \Soulcms\Service::M()->table('admin_notice')->update($id, array(
                'status' => 1,
                'op_uid' => $this->uid,
                'op_username' => $this->admin['username'],
            ));
        }

        mys_redirect($url, 'refresh');
    }

	// 修改资料
	public function my() {

		$color = ['default', 'blue', 'red', 'green', 'dark', 'yellow'];
        $target = [0 => mys_lang('内链'), 1 => mys_lang('外链')];

		if (IS_AJAX_POST) {
            if (!\Soulcms\Service::L('form')->check_captcha('code')) {
                $this->_json(0, mys_lang('验证码不正确'), ['field' => 'code']);
            }
			$menu = [];
			$data = \Soulcms\Service::L('input')->post('data');
			if ($data['usermenu']) {
				foreach ($data['usermenu']['name'] as $id => $v) {
					$v && $data['usermenu']['url'][$id] && $menu[$id] = [
						'name' => $v,
						'url' => $data['usermenu']['url'][$id],
						'color' => $data['usermenu']['color'][$id],
                        'target' => $data['usermenu']['target'][$id],
					];
				}
			}
			// 修改密码
			$password = mys_safe_password(\Soulcms\Service::L('input')->post('password'));
			$password && \Soulcms\Service::M('member')->edit_password($this->member, $password);

			\Soulcms\Service::M()->db->table('admin')->where('uid', $this->admin['id'])->update([
				'usermenu' => mys_array2string($menu)
            ]);
			\Soulcms\Service::M()->db->table('member_data')->where('id', $this->admin['id'])->update([
				'is_admin' => 1
            ]);

			$this->_json(1, mys_lang('操作成功'));
		}

        $select = '';
        foreach ($color as $t) {
            $select.= '<option value="'.$t.'">'.$t.'</option>';
        }

        $select2 = '';
        foreach ($target as $i => $t) {
            $select2.= '<option value="'.$i.'">'.$t.'</option>';
        }

		\Soulcms\Service::V()->assign([
			'menu' => \Soulcms\Service::M('auth')->_admin_menu(
				[
					'资料修改' => ['api/my', 'fa fa-user'],
					'登录记录' => ['root/login_index{id='.$this->uid.'}', 'fa fa-calendar'],
				]
			),
            'color' => $color,
            'target' => $target,
            'select_color' => $select,
            'select_target' => $select2,
		]);
		\Soulcms\Service::V()->display('api_my.html');exit;
	}

	// 加入菜单
	public function menu() {

		$url = urldecode(mys_safe_replace(\Soulcms\Service::L('input')->get('v')));
		$arr = parse_url($url);
		$queryParts = explode('&', $arr['query']);
		$params = [];
		foreach ($queryParts as $param) {
			$item = explode('=', $param);
			$params[$item[0]] = $item[1];
		}
		// 基础uri
		$uri = ($params['s'] ? $params['s'].'/' : '').($params['c'] ? $params['c'] : 'home').'/'.($params['m'] ? $params['m'] : 'index');
		// 查询名称
		$menu = \Soulcms\Service::M()->db->table('admin_menu')->select('name')->like('uri', $uri)->get()->getRowArray();
		$name = $menu ? $menu['name'] : '未知名称';
		// 替换URL

		$admin = \Soulcms\Service::M()->db->table('admin')->where('uid', $this->uid)->get()->getRowArray();
		if ($admin) {
			$menu = mys_string2array($admin['usermenu']);
			foreach ($menu as $t) {
				$t['url'] == $url && $this->_json(1, mys_lang('已经存在'));
			}
			$menu[] = array(
				'name' => $name,
				'url' => $url,
			);
			\Soulcms\Service::M()->db->table('admin')->where('uid', $this->uid)->update(array(
					'usermenu' => mys_array2string($menu)
				)
			);
			$this->_json(1, mys_lang('操作成功'));
		}

		$this->_json(0, mys_lang('加入失败'));
	}

	// 执行更新缓存
	public function cache() {

        $name = mys_safe_replace($_GET['id']);
        \Soulcms\Service::M('cache')->$name();

        exit($this->_json(1, mys_lang('更新完成'), 0));

    }


	// 执行清空缓存数据
	public function cache_clear() {

        \Soulcms\Service::M('cache')->update_data_cache();
        exit($this->_json(1, mys_lang('前台数据缓存已被更新')));
	}

	// 执行更新缓存
	public function cache_update() {

        \Soulcms\Service::M('cache')->update_cache();
        exit($this->_json(1, mys_lang('更新完成')));
	}

	// 执行重建模块索引
	public function cache_search() {

        \Soulcms\Service::M('cache')->update_search_index();
        exit($this->_json(1, mys_lang('更新完成')));
	}

	// 执行重建模块索引
	public function cache_site_config() {

        \Soulcms\Service::M('cache')->update_search_index();
        exit($this->_json(1, mys_lang('更新完成')));
	}

	/**
	 * 生成安全码
	 */
	public function syskey() {
		echo 'PHPCMF'.strtoupper(substr((md5(SYS_TIME)), rand(0, 10), 13));exit;
	}

	/**
	 * 生成来路随机字符
	 */
	public function referer() {
		$s = strtoupper(base64_encode(md5(SYS_TIME).md5(rand(0, 2015).md5(rand(0, 2015)))).md5(rand(0, 2009)));
		echo str_replace('=', '', substr($s, 0, 42));exit;
	}

	// 域名检查
	public function domain() {

	    $html = '';
	    $post = \Soulcms\Service::L('input')->post('data');
		if ($post) {
		    $my = [];
            foreach ($this->site_domain as $name => $sid) {
                if ($sid == SITE_ID) {
                    unset($this->site_domain[$name]);
                }
            }
		    foreach ($post as $name => $t) {
		        if (!$t) {
		            continue;
                }
		        if ($name == 'site_domains') {
		            $v = explode(',', str_replace([chr(13), PHP_EOL], ',', $t));
                    if ($v) {
                        foreach ($v as $t) {
                            $t && $my[] = $t;
                            $this->site_domain[$t] && $html.= '<p>'.$t.' 已经存在于其他站点</p>';
                        }
                    }
                } else {
                    $my[] = $t;
                    $this->site_domain[$t] && $html.= $t.' 已经存在于其他站点';
                }
            }
            $my && count($my) != count(array_unique($my)) && $html.= '<p>当前配置项存在重复域名</p>';
            $html && exit($html);
        }

		exit('ok');
	}

	// 统计
	public function mtotal() {

		$t1 = $t2 = $t3 = $t4 = $t5 =0;
		$dir = mys_safe_filename(\Soulcms\Service::L('input')->get('dir'));
		if (is_dir(APPSPATH.ucfirst($dir))) {
			$t1 = \Soulcms\Service::M()->db->table(SITE_ID.'_'.$dir.'_index')->where('status=9')->where('DATEDIFF(from_unixtime(inputtime),now())=0')->countAllResults();
			$t2 = \Soulcms\Service::M()->db->table(SITE_ID.'_'.$dir.'_index')->where('status=9')->countAllResults();
			$t3 = \Soulcms\Service::M()->db->table(SITE_ID.'_'.$dir.'_verify')->countAllResults();
			$t4 = \Soulcms\Service::M()->db->table(SITE_ID.'_'.$dir.'_recycle')->where('uid', $this->uid)->countAllResults();
			$t5 = \Soulcms\Service::M()->db->table(SITE_ID.'_'.$dir.'_time')->where('uid', $this->uid)->countAllResults();
		}
		echo '$("#'.$dir.'_today").html('.$t1.');';
		echo '$("#'.$dir.'_all").html('.$t2.');';
		echo '$("#'.$dir.'_verify").html('.$t3.');';
		echo '$("#'.$dir.'_recycle").html('.$t4.');';
		echo '$("#'.$dir.'_timing").html('.$t5.');';
		exit;
	}
	
	// api
	public function icon() {
		\Soulcms\Service::V()->display('api_icon.html');exit;
	}

	// 常用配置
	public function config() {

		\Soulcms\Service::V()->display('api_config.html');
        exit;
	}

	// phpinfo
	public function phpinfo() {

		phpinfo();
		exit;
	}

	// 邮件发送测试
	public function email_test() {

		!SYS_EMAIL && $this->_json(0, mys_lang('系统邮箱没有设置'));

		$id = intval(\Soulcms\Service::L('input')->get('id'));
		$data = \Soulcms\Service::M()->table('mail_smtp')->get($id);
		!$data && $this->_json(0, mys_lang('数据#%s不存在', $id));

		$dmail = \Soulcms\Service::L('email')->set([
			'host' => $data['host'],
			'user' => $data['user'],
			'pass' => $data['pass'],
			'port' => $data['port'],
			'from' => $data['user']
		]);

		if ($dmail->send(SYS_EMAIL, 'test', 'test for '.SITE_NAME)) {
			$this->_json(1, mys_lang('测试成功'));
		} else {
			$this->_json(0, $dmail->error());
		}
	}

	/**
	 * 预览移动端网站
	 */
	public function site() {

        $id = intval(\Soulcms\Service::L('input')->get('id'));
        !$this->site_info[$id] && $this->_admin_msg(0, mys_lang('站点不存在'));
        !$this->admin && $this->_admin_msg(0, mys_lang('你还没有登录'));

        // 判断站点权限
        \Soulcms\Service::L('cache')->init('', 'site')->save('admin_login_site', $this->admin, 300);
        $this->_msg(1, mys_lang('正在切换到【%s】...', $this->site_info[$id]['SITE_NAME']).'<script src="'.$this->site_info[$id]['SITE_URL'].'index.php?s=api&c=sso&action=slogin&code='.mys_authcode($this->admin['uid'].'-'.md5($this->admin['uid'].$this->admin['password']), 'ENCODE').'"></script>', $this->site_info[$id]['SITE_URL'].SELF, 0);
        exit;
    }

	/**
	 * 预览移动端网站
	 */
	public function mobile() {

        \Soulcms\Service::V()->assign([
            'url' => SITE_MURL,
        ]);
        \Soulcms\Service::V()->display('api_mobile.html');exit;
    }

	/**
	 * 水印图片预览
	 */
	public function preview() {

	    $data = $_GET['data'];

        $data['source_image'] = WRITEPATH.'preview.png';
        $data['dynamic_output'] = true;

        $rt = \Soulcms\Service::L('Image')->watermark($data);
        if (!$rt) {
            echo \Soulcms\Service::L('Image')->display_errors();
        }
        exit;
    }

	/**
	 * 测试远程附件
	 */
	public function test_attach() {

	    $data = \Soulcms\Service::L('input')->post('data');
        $type = intval($data['type']);
        $value = $data['value'][$type];
        if (!$value) {
            $this->_json(0, mys_lang('参数不存在'));
        }

        $rt = \Soulcms\Service::L('upload')->save_file(
            'content',
            'this is phpcmf file-test',
            'test/test.txt',
            [
                'id' => 0,
                'url' => $data['url'],
                'type' => $type,
                'value' => $value,
            ]
        );

        if (!$rt['code']) {
            $this->_json(0, $rt['msg']);
        } elseif (strpos(mys_catcher_data($rt['data']['url']), 'phpcmf') !== false) {
            $this->_json(1, mys_lang('测试成功'));
        }

        $this->_json(0, mys_lang('无法访问到附件: %s', $rt['data']['url']));
    }

	/**
	 * 测试短信验证码
	 */
	public function test_mobile() {

	    $data = \Soulcms\Service::L('input')->post('data');

        $method = 'my_sendsms_code';
        if (function_exists($method)) {
            $rt =  call_user_func_array($method, [
                $data['mobile'],
                rand(10000, 99999),
                $data['third'],
            ]);
            $this->_json($rt['code'], $rt['msg']);
        } else {
            $this->_json(0, mys_lang('你没有定义第三方短信接口: '. $method));
        }

    }

    /**
     * 微博授权更新
     */
    public function weibo() {

        // 请求参数
        $cache = $this->get_cache('site', SITE_ID, 'weibo');
        $callback_url = ADMIN_URL.mys_url('api/weibo');

        define("WB_AKEY", $cache['key']);
        define("WB_SKEY", $cache['secret']);

        require FCPATH.'ThirdParty/Weibo/saetv2.ex.class.php';
        $o = new \SaeTOAuthV2(WB_AKEY, WB_SKEY);

        // 表示回调返回
        if (isset($_REQUEST['code']) && $_REQUEST['code']) {
            $keys = [];
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = $callback_url;
            $token = $o->getAccessToken('code', $keys);
            if (is_array($token)) {
                // 回调成功
                $c = new \SaeTClientV2(WB_AKEY, WB_SKEY, $token['access_token']);
                //根据ID获取用户等基本信息
                $user = $c->show_user_by_id($token['uid']);
                if ($user) {
                    // 存储
                    $save = [
                        'avatar' => $user['profile_image_url'],
                        'nickname' => mys_emoji2html($user['name']),
                        'expire_at' => SYS_TIME + $token['expires_in'],
                        'access_token' => $token['access_token'],
                    ];
                    \Soulcms\Service::L('cache')->init_file('weibo')->set_file(SITE_ID, $save);
                    $this->_admin_msg(1, mys_lang('更新授权成功'));
                } else {
                    $this->_admin_msg(0, mys_lang('获取微博用户信息失败'));exit;
                }
            } else {
                // 回调失败
                $this->_admin_msg(0, $token);exit;
            }
        } else {
            // 跳转授权页面
            mys_redirect($o->getAuthorizeURL($callback_url));
        }

    }

	/**
	 * 导出 字段设置
	 */
	public function export_field() {

        $table = mys_safe_replace(\Soulcms\Service::L('input')->get('table'));
        !$table && $this->_json(0, '表【'.$table.'】不存在');

        if (IS_AJAX_POST) {

            $post = \Soulcms\Service::L('input')->post('data');
            !$post && $this->_json(0, mys_lang('存储内容不正确'));

            \Soulcms\Service::M('Table')->save_export_field_name($table, $post);
            $this->_json(1, mys_lang('操作成功'));
        }

        $field = \Soulcms\Service::M('Table')->get_export_field_name($table, 1);
        !$field && $this->_json(0, '表【'.$table.'】的字段不存在');

        \Soulcms\Service::V()->assign([
            'field' => $field,
            'export_url' =>\Soulcms\Service::L('Router')->url('api/export_list', ['sql'=> $_GET['sql'], 'table' =>$table]),
        ]);
        \Soulcms\Service::V()->display('api_export_field.html');
        exit;
    }

	/**
	 * 导出
	 */
	public function export_list() {

        $sql = str_replace('+', ' ', mys_authcode(urldecode($_GET['sql'])));
        $db = \Soulcms\Service::M()->db->query($sql);
        $list = $db ? $db->getResultArray() : [];
        $table = mys_safe_replace(\Soulcms\Service::L('input')->get('table'));
        $field = \Soulcms\Service::M('Table')->get_export_field_name($table, 1);

        \Soulcms\Service::V()->assign([
            'list' => $list,
            'field' => $field,
        ]);
        \Soulcms\Service::V()->display('api_export_list.html');
        exit;
    }

	/**
	 * 显示用户资料
	 */
	public function member() {

		$name = mys_safe_replace(\Soulcms\Service::L('input')->get('name'));
		$data = \Soulcms\Service::M('member')->get_member(0, $name);
		!$data && $this->_json(0, mys_lang('此账号%s不存在', $name));

		\Soulcms\Service::V()->assign([
			'm' => $data,
		]);
		\Soulcms\Service::V()->display('api_show_member.html');
		exit;
	}

    /**
     * 测试目录是否可用
     */
    public function test_dir() {

        $v = \Soulcms\Service::L('input')->get('v');
        if (!$v) {
            $this->_json(0, mys_lang('目录为空'));
        } elseif (strpos($v, ' ') === 0) {
            $this->_json(0, mys_lang('不能用空格开头'));
        }
        $path = mys_get_dir_path($v);
        if (is_dir($path)) {
            $this->_json(1, mys_lang('目录正常'));
        } else {
            $this->_json(0, mys_lang('目录[%s]不存在', $path));
        }

    }

    // 短信接口查询
    public function sms_info() {
        exit($this->_api_sms_info());
    }

    // 版本检查
    public function version_cmf() {
        exit($this->_api_version_cmf());
    }

    // 版本检查
    public function version_cms() {
        exit($this->_api_version_cms());
    }

    // 搜索帮助
    public function search_help() {
        exit($this->_api_search_help());
    }

    public function count_total() {


    }

}
