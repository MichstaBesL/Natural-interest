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



// 联动菜单
class Linkage extends \Soulcms\Common
{

	public function index() {

		\Soulcms\Service::V()->assign([
			'list' => \Soulcms\Service::M('Linkage')->table('linkage')->getAll(),
			'dt_data' => [
                1 => '导入省级',
                2 => '导入省市',
                3 => '导入省市县',
            ],
            'menu' =>  \Soulcms\Service::M('auth')->_admin_menu(
                [
                    '联动菜单' => ['linkage/index', 'fa fa-columns'],
                    '创建菜单' => ['add:linkage/add', 'fa fa-plus'],
                    '修改' => ['hide:linkage/edit', 'fa fa-edit'],
                    '数据管理' => ['hide:linkage/list_index', 'fa fa-table'],
                    'help' => [354],
                ]
            ),
		]);
		\Soulcms\Service::V()->display('linkage_index.html');
	}

	public function add() {

		if (IS_AJAX_POST) {
			$data = \Soulcms\Service::L('input')->post('data');
			$this->_validation(0, $data);
			\Soulcms\Service::L('input')->system_log('创建联动菜单('.$data['name'].')');
			$rt = \Soulcms\Service::M('Linkage')->create($data);
			!$rt['code'] && $this->_json(0, $rt['msg']);
            \Soulcms\Service::M('cache')->sync_cache('linkage', '', 1); // 自动更新缓存
			exit($this->_json(1, mys_lang('操作成功')));
		}

		\Soulcms\Service::V()->assign([
			'form' => mys_form_hidden()
		]);
		\Soulcms\Service::V()->display('linkage_add.html');
		exit;
	}

	public function edit() {

		$id = intval(\Soulcms\Service::L('input')->get('id'));
		$data = \Soulcms\Service::M('Linkage')->table('linkage')->get($id);
		!$data && $this->_admin_msg(0, mys_lang('联动菜单（%s）不存在', $id));

		if (IS_AJAX_POST) {
			$data = \Soulcms\Service::L('input')->post('data');
			$this->_validation($id, $data);
			$rt = \Soulcms\Service::M('Linkage')->table('linkage')->update($id, $data);
			!$rt['code'] && $this->_json(0, $rt['msg']);
			\Soulcms\Service::L('input')->system_log('修改联动菜单('.$data['name'].')');
            \Soulcms\Service::M('cache')->sync_cache('linkage', '', 1); // 自动更新缓存
			exit($this->_json(1, mys_lang('操作成功')));
		}

		\Soulcms\Service::V()->assign([
			'data' => $data,
			'form' => mys_form_hidden()
		]);
		\Soulcms\Service::V()->display('linkage_add.html');exit;
	}

	public function import() {
		
		$id = (int)\Soulcms\Service::L('input')->get('id');
		$code = (int)\Soulcms\Service::L('input')->get('code');
		!is_file(APPPATH.'Config/Linkage/'.$code.'.php') && $this->_json(0, mys_lang('数据文件不存在无法导入'));

		// 清空数据
		$table = 'linkage_data_'.$id;
		\Soulcms\Service::M('Linkage')->query('TRUNCATE `'.\Soulcms\Service::M('Linkage')->dbprefix($table).'`');
		$count = 0;

		// 开始导入
		$data = require APPPATH.'Config/Linkage/'.$code.'.php';
		foreach ($data as $t) {
			$rt = \Soulcms\Service::M('Linkage')->table($table)->insert($t);
			if ($rt['code']) {
				$count++;
			}
		}

        \Soulcms\Service::M('cache')->sync_cache('linkage', '', 1); // 自动更新缓存
		$this->_json(1, mys_lang('共%s条数据，导入成功%s条', mys_count($data), $count));
	}
	
	
	public function del() {

		$ids = \Soulcms\Service::L('input')->get_post_ids();
		!$ids && exit($this->_json(0, mys_lang('你还没有选择呢')));

		$rt = \Soulcms\Service::M('Linkage')->delete_all($ids);
		!$rt['code'] && exit($this->_json(0, $rt['msg']));

        \Soulcms\Service::M('cache')->sync_cache('linkage', '', 1); // 自动更新缓存
		\Soulcms\Service::L('input')->system_log('批量联动菜单: '. @implode(',', $ids));

		exit($this->_json(1, mys_lang('操作成功'), ['ids' => $ids]));
	}

	// 验证数据
	private function _validation($id, $data) {
		// 表单验证配置
		$config = [
			'name' => [
				'name' => '名称',
				'rule' => [
					'empty' => mys_lang('名称不能为空')
				],
				'filter' => [],
				'length' => '200'
			],
			'code' => [
				'name' => '别名',
				'rule' => [
					'empty' => mys_lang('别名不能为空'),
					'table' => mys_lang('别名格式不正确'),
				],
				'filter' => [],
				'length' => '200'
			],
		];
		list($data, $return) = \Soulcms\Service::L('Form')->validation($data, $config);
		$return && exit($this->_json(0, $return['error'], ['field' => $return['name']]));
		\Soulcms\Service::M('Linkage')->table('linkage')->is_exists($id, 'code', $data['code']) && exit($this->_json(0, mys_lang('别名已经存在'), ['field' => 'code']));
	}

	public function displayorder_edit() {

		// 查询数据
		$id = (int)\Soulcms\Service::L('input')->get('id');
		$key = (int)\Soulcms\Service::L('input')->get('key');
		$row = \Soulcms\Service::M('Linkage')->table('linkage_data_'.$key)->get($id);
		!$row && $this->_json(0, mys_lang('数据#%s不存在', $id));

		$value = (int)\Soulcms\Service::L('input')->get('value');
		$rt = \Soulcms\Service::M('Linkage')->table('linkage_data_'.$key)->save($id, 'displayorder', $value);
		!$rt['code'] && $this->_json(0, $rt['msg']);

        \Soulcms\Service::M('cache')->sync_cache('linkage', '', 1); // 自动更新缓存
		\Soulcms\Service::L('input')->system_log('修改联动菜单值('.$row['name'].')的排序值为'.$value);
		$this->_json(1, mys_lang('操作成功'));
	}

	// 隐藏或者启用
	public function hidden_edit() {

		$id = (int)\Soulcms\Service::L('input')->get('id');
		$key = (int)\Soulcms\Service::L('input')->get('key');
		$row = \Soulcms\Service::M('Linkage')->table('linkage_data_'.$key)->get($id);
		!$row && $this->_json(0, mys_lang('数据#%s不存在', $id));

		$i = intval(\Soulcms\Service::L('input')->get('id'));
		$v = $row['hidden'] ? 0 : 1;
		\Soulcms\Service::M('Linkage')->table('linkage_data_'.$key)->update($id, ['hidden' => $v]);

        \Soulcms\Service::M('cache')->sync_cache('linkage', '', 1); // 自动更新缓存
		\Soulcms\Service::L('input')->system_log('修改联动菜单状态: '. $i);
		exit($this->_json(1, mys_lang($v ? '此菜单已被隐藏' : '此菜单已被启用'), ['value' => $v]));

	}

	public function list_del() {

		$ids = \Soulcms\Service::L('input')->get_post_ids();
		$key = (int)\Soulcms\Service::L('input')->get('key');
		!$ids && exit($this->_json(0, mys_lang('你还没有选择呢')));

		$rt = \Soulcms\Service::M('Linkage')->delete_list_all($key, $ids);
		!$rt['code'] && exit($this->_json(0, $rt['msg']));

        \Soulcms\Service::M('cache')->sync_cache('linkage', '', 1); // 自动更新缓存
		\Soulcms\Service::L('input')->system_log('批量删除联动菜单: '. @implode(',', $ids));

		exit($this->_json(1, mys_lang('操作成功'), ['ids' => $ids]));
	}

	public function pid_edit() {

		$ids = \Soulcms\Service::L('input')->get_post_ids();
		$key = (int)\Soulcms\Service::L('input')->get('key');
		$pid = (int)\Soulcms\Service::L('input')->post('pid');
		!$ids && exit($this->_json(0, mys_lang('你还没有选择呢')));

		$rt = \Soulcms\Service::M('Linkage')->edit_pid_all($key, $pid, $ids);
		!$rt['code'] && exit($this->_json(0, $rt['msg']));

        \Soulcms\Service::M('cache')->sync_cache('linkage', '', 1); // 自动更新缓存
		\Soulcms\Service::L('input')->system_log('批量更改联动菜单分类: '. @implode(',', $ids));

		exit($this->_json(1, mys_lang('操作成功'), ['ids' => $ids]));
	}

	public function list_index() {

		$key = (int)\Soulcms\Service::L('input')->get('key');
		$pid = (int)\Soulcms\Service::L('input')->get('pid');

		$link = \Soulcms\Service::M('Linkage')->table('linkage')->get($key);
		!$link && $this->_admin_msg(0, mys_lang('联动菜单不存在'));

		if (\Soulcms\Service::M('Linkage')->counts('linkage_data_'.$key) > 3000) {
			$select = '<input type="text" class="form-control" name="pid" placeholder="'.mys_lang('输入所属Id号').'"> ';
		} else {
			$select = \Soulcms\Service::L('Tree')->select_linkage(
				\Soulcms\Service::M('Linkage')->getList($link),
				$pid,
				'name="pid"',
				mys_lang('顶级菜单')
			);
		}

		\Soulcms\Service::V()->assign([
			'key' => $key,
			'pid' => $pid,
			'list' => \Soulcms\Service::M('Linkage')->getList($link, $pid),
			'select' => $select,
            'menu' =>  \Soulcms\Service::M('auth')->_admin_menu(
                [
                    '联动菜单' => ['linkage/index', 'fa fa-columns'],
                    '数据管理' => ['hide:linkage/list_index', 'fa fa-table'],
                    '修改' => ['hide:linkage/list_edit', 'fa fa-edit'],
                    '添加' => ['add:linkage/list_add{key='.$key.'&pid=0}', 'fa fa-plus'],
                    'help' => [354],
                ]
            ),
		]);
		\Soulcms\Service::V()->display('linkage_list_index.html');
	}

	public function list_add() {

		$pid = (int)\Soulcms\Service::L('input')->get('pid');
		$key = (int)\Soulcms\Service::L('input')->get('key');
		$link = \Soulcms\Service::M('Linkage')->table('linkage')->get($key);
		!$link && $this->_admin_msg(0, mys_lang('联动菜单不存在'));
		
		if (IS_AJAX_POST) {
			$data = \Soulcms\Service::L('input')->post('data');
			$rt = \Soulcms\Service::M('Linkage')->add_list($key, $data);
			!$rt['code'] && $this->_json(0, $rt['msg']);
            \Soulcms\Service::M('cache')->sync_cache('linkage', '', 1); // 自动更新缓存
			\Soulcms\Service::L('input')->system_log('创建联动菜单('.$data['name'].')');
			exit($this->_json(1, $rt['msg']));
		}

		$select = '';
		if ($pid) {
			$top = \Soulcms\Service::M('Linkage')->table('linkage_data_'.$key)->get($pid);
			if ($top) {
				$select = '<input type="hidden" name="data[pid]" value="'.$pid.'">';
				$select.= '<p class="form-control-static"> '.$top['name'].' </p>';
			}
		}

		!$select && $select = \Soulcms\Service::L('Tree')->select_linkage(
			\Soulcms\Service::M('Linkage')->getList($link),
			$pid,
			'name="data[pid]"',
			mys_lang('顶级菜单')
		);

		\Soulcms\Service::V()->assign([
			'form' => mys_form_hidden(),
			'select' => $select
		]);
		\Soulcms\Service::V()->display('linkage_list_add.html');
		exit;
	}

	public function list_edit() {

		$id = (int)\Soulcms\Service::L('input')->get('id');
		$key = (int)\Soulcms\Service::L('input')->get('key');

		$link = \Soulcms\Service::M('Linkage')->table('linkage')->get($key);
		!$link && $this->_admin_msg(0, mys_lang('联动菜单不存在'));

		$data = \Soulcms\Service::M('Linkage')->table('linkage_data_'.$key)->get($id);
		!$data && $this->_admin_msg(0, mys_lang('联动菜单数据#%s不存在', $id));

        $field = \Soulcms\Service::M('Linkage')->get_fields($key);

		if (IS_AJAX_POST) {
			$post = \Soulcms\Service::L('input')->post('data');
			$post['name'] = trim($post['name']);
			if (!$post['name']) {
				$this->_json(0, mys_lang('名称不能为空'));
			} elseif (!$post['cname']) {
				$this->_json(0, mys_lang('别名不能为空'));
			} else if (\Soulcms\Service::M()->db->table('linkage_data_'.$key)->where('id<>', $id)->where('cname', $post['cname'])->countAllResults()) {
				$this->_json(0, mys_lang('别名已经存在'));
			}
            $update = [
                'pid' => $post['pid'],
                'name' => $post['name'],
                'cname' => $post['cname'],
            ];
			if ($field) {
                list($save, $return, $attach) = \Soulcms\Service::L('form')->validation($post, null, $field, $data);
                // 输出错误
                $return && $this->_json(0, $return['error'], ['field' => $return['name']]);
                $update = mys_array22array($update, $save[1]);
            }
			$rt = \Soulcms\Service::M('Linkage')->table('linkage_data_'.$key)->update($id, $update);
			!$rt['code'] && $this->_json(0, $rt['msg']);
            // 附件归档
            SYS_ATTACHMENT_DB && $attach && \Soulcms\Service::M('Attachment')->handle(
                $this->member['id'],
                \Soulcms\Service::M()->dbprefix('linkage_data_'.$key),
                $attach
            );
            \Soulcms\Service::M('cache')->sync_cache('linkage', '', 1); // 自动更新缓存
			\Soulcms\Service::L('input')->system_log('修改联动菜单('.$post['name'].')');
			exit($this->_json(1, mys_lang('操作成功')));
		}

		\Soulcms\Service::V()->assign([
			'form' => mys_form_hidden(),
			'data' => $data,
            'myfield' => \Soulcms\Service::L('field')->toform($this->uid, $field, $data),
			'select' => $this->_select($link, $data['pid']),
            'menu' =>  \Soulcms\Service::M('auth')->_admin_menu(
                [
                    '联动菜单' => ['linkage/index', 'fa fa-columns'],
                    '数据管理' => ['hide:linkage/list_index', 'fa fa-table'],
                    '修改' => ['hide:linkage/list_edit', 'fa fa-edit'],
                    '添加' => ['add:linkage/list_add{key='.$key.'&pid=0}', 'fa fa-plus'],
                    'help' => [354],
                ]
            ),
            'reply_url' => mys_url('linkage/list_index', ['key'=> $key]),
		]);
		\Soulcms\Service::V()->display('linkage_list_edit.html');
		exit;
	}

	private function _select($link, $pid) {

		if (\Soulcms\Service::M('Linkage')->counts('linkage_data_'.$link['id']) > 3000) {
			$select = '<input type="text" class="form-control" name="data[pid]" value="'.$pid.'"> ';
			$select.= '<span class="help-block"> '.mys_lang('这是上级分类的Id号').' </span>';
		} else {
			$select = \Soulcms\Service::L('Tree')->select_linkage(\Soulcms\Service::M('Linkage')->getList($link), $pid, 'name="data[pid]"', mys_lang('顶级菜单'));
		}

		return $select;
	}

}
