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



class Site_client extends \Soulcms\Common
{
	public function index() {

		if (IS_AJAX_POST) {
		    $save = [];
		    $post = \Soulcms\Service::L('input')->post('data', true);
		    if ($post) {
                foreach ($post as $i => $t) {
                    if (isset($t['name'])) {
                        if (!preg_match('/^[a-z]+/i', $t['name'])) {
                            $this->_json(0, mys_lang('终端目录必须是英文字母'));
                        } elseif (!$t['name']) {
                            $this->_json(0, mys_lang('终端目录必须填写'));
                        }
                        $save[$i]['name'] = $t['name'];
                    } else {
                        if (!$t['domain']) {
                            $this->_json(0, mys_lang('域名必须填写'));
                        } elseif (strpos($t['domain'], '//') !== false) {
                            $this->_json(0, mys_lang('域名只能填写纯域名，不能加http://'));
                        }
                        $save[$i-1]['domain'] = $t['domain'];
                    }
                }
            }

			$rt = \Soulcms\Service::M('Site')->save_config(
			    SITE_ID,
                'client',
                $save
            );
            !is_array($rt) && $this->_json(0, mys_lang('网站终端(#%s)不存在', SITE_ID));
            \Soulcms\Service::M('cache')->sync_cache('');
			\Soulcms\Service::L('input')->system_log('设置网站自定义终端参数');
			exit($this->_json(1, mys_lang('操作成功')));
		}

		$page = intval(\Soulcms\Service::L('input')->get('page'));
		$data = \Soulcms\Service::M('Site')->config(SITE_ID);
        list($module, $domain) = \Soulcms\Service::M('Site')->domain();

		\Soulcms\Service::V()->assign([
			'page' => $page,
			'data' => $data['client'],
			'form' => mys_form_hidden(['page' => $page]),
			'menu' => \Soulcms\Service::M('auth')->_admin_menu(
                [
                    '网站终端' => ['site_client/index', 'fa fa-cog'],
                    'help' => [478],
                ]
            ),
            'pc_domain' => $domain['site_domain'],
            'mobile_domain' => $domain['mobile_domain'],
		]);
		\Soulcms\Service::V()->display('site_client.html');
	}

	
}
