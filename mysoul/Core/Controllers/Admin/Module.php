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



class Module extends \Soulcms\Common
{

    private $dir;
    private $form;
    private $jname = ['case', 'class', 'extends', 'new', 'var'];

    public function __construct(...$params) {
        parent::__construct(...$params);

        $this->dir = mys_safe_replace(\Soulcms\Service::L('input')->get('dir'));

        $menu = [
            '内容模块' => [\Soulcms\Service::L('Router')->class.'/index', 'fa fa-cogs'],
            '模块配置' => ['hide:'.\Soulcms\Service::L('Router')->class.'/edit', 'fa fa-cog'],
            '推荐位配置' => ['hide:'.\Soulcms\Service::L('Router')->class.'/flag_edit', 'fa fa-flag'],
            '重建表单' => ['ajax:module/form_init_index', 'fa fa-refresh'],
            'help' => [57]
        ];

        if (strpos(\Soulcms\Service::L('Router')->method, 'form') !== false) {
            unset($menu['help']);
            $menu['模块'.$this->dir.'表单'] = [\Soulcms\Service::L('Router')->class.'/form_index{dir='.$this->dir.'}', 'fa fa-list'];
            $menu['添加表单'] = ['add:module/form_add{dir='.$this->dir.'}', 'fa fa-plus', '500px', '310px'];
            $menu['表单配置'] = ['hide:'.\Soulcms\Service::L('Router')->class.'/form_edit', 'fa fa-cog'];
            $menu['help'] = [98];
        }

        \Soulcms\Service::V()->assign('menu', \Soulcms\Service::M('auth')->_admin_menu($menu));

        // 表单验证配置
        $this->form = [
            'name' => [
                'name' => '表单名称',
                'rule' => [
                    'empty' => mys_lang('表单名称不能为空')
                ],
                'filter' => [],
                'length' => '200'
            ],
            'table' => [
                'name' => '表单别名',
                'rule' => [
                    'empty' => mys_lang('表单别名不能为空'),
                    'table' => mys_lang('表单别名格式不正确'),
                ],
                'filter' => [],
                'length' => '200'
            ],
        ];
    }


    // 安装模块
    public function install() {

        $dir = mys_safe_replace(\Soulcms\Service::L('input')->get('dir'));
        $type = (int)\Soulcms\Service::L('input')->get('type');

        !preg_match('/^[a-z]+$/U', $dir) && $this->_json(0, mys_lang('模块目录[%s]格式不正确', $dir));
        in_array($dir, $this->jname) && $this->_json(0, mys_lang('模块目录[%s]名称是系统保留名称，请重命名', $dir));

        $path = mys_get_app_dir($dir);
        !is_dir($path) && $this->_json(0, mys_lang('模块目录[%s]不存在', $path));

        // 对当前模块属性判断
        $cfg = require $path.'Config/App.php';
        !$cfg && $this->_json(0, mys_lang('文件[%s]不存在', 'App/'.ucfirst($dir).'/Config/App.php'));

        $cfg['share'] = $type ? 0 : 1;

        $rt = \Soulcms\Service::M('Module')->install($dir, $cfg);
        \Soulcms\Service::M('cache')->sync_cache(''); // 自动更新缓存
        $this->_json($rt['code'], $rt['msg']);
    }

    // 卸载模块
    public function uninstall() {

        $dir = mys_safe_replace(\Soulcms\Service::L('input')->get('dir'));
        !preg_match('/^[a-z]+$/U', $dir) && $this->_json(0, mys_lang('模块目录[%s]格式不正确', $dir));

        $path = mys_get_app_dir($dir);
        !is_dir($path) && $this->_json(0, mys_lang('模块目录[%s]不存在', $path));

        $cfg = require $path.'Config/App.php';
        !$cfg && $this->_json(0, mys_lang('文件[%s]不存在', 'App/'.ucfirst($dir).'/Config/App.php'));

        $rt = \Soulcms\Service::M('Module')->uninstall($dir, $cfg);
        \Soulcms\Service::M('cache')->sync_cache(''); // 自动更新缓存
        $this->_json($rt['code'], $rt['msg']);

    }

    // 模块管理
    public function index() {

        $list = [];
        $local = mys_dir_map(mys_get_app_list(), 1); // 搜索本地模块
        foreach ($local as $dir) {
            if (is_file(mys_get_app_dir($dir).'Config/App.php')) {
                $key = strtolower($dir);
                $cfg = require mys_get_app_dir($dir).'Config/App.php';
                if ($cfg['type'] == 'module' || $cfg['ftype'] == 'module') {
                    if (isset($cfg['hlist']) && $cfg['hlist']) {
                        // 不在列表显示
                        continue;
                    }
                    $cfg['dirname'] = $key;
                    $list[$key] = $cfg;
                }
            }
        }

        $my = [];
        $module = \Soulcms\Service::M('Module')->All(); // 库中已安装模块
        if ($module) {
            foreach ($module as $t) {
                $dir = $t['dirname'];
                if ($list[$dir]) {
                    $t['key'] = $t['key'];
                    $t['name'] = $list[$dir]['name'];
                    $t['mtype'] = $list[$dir]['mtype'];
                    $t['system'] = $list[$dir]['system'];
                    $t['version'] = $list[$dir]['version'];
                    $site = mys_string2array($t['site']);
                    $t['install'] = isset($site[SITE_ID]) && $site[SITE_ID] ? 1 : 0;
                    $t['site'] = mys_count($site);
                    $my[$dir] = $t;
                    unset($list[$dir]);
                }
            }
        }

        \Soulcms\Service::V()->assign([
            'my' => $my,
            'list' => $list,
        ]);
        \Soulcms\Service::V()->display('module_list.html');
    }

    // 排序
    public function displayorder_edit() {

        // 查询数据
        $id = (int)\Soulcms\Service::L('input')->get('id');
        $row = \Soulcms\Service::M('Module')->table('module')->get($id);
        !$row && $this->_json(0, mys_lang('数据#%s不存在', $id));

        $value = (int)\Soulcms\Service::L('input')->get('value');
        $rt = \Soulcms\Service::M('Module')->table('module')->save($id, 'displayorder', $value);
        !$rt['code'] && $this->_json(0, $rt['msg']);

        \Soulcms\Service::M('cache')->sync_cache(''); // 自动更新缓存
        \Soulcms\Service::L('input')->system_log('修改模块('.$row['dirname'].')的排序值为'.$value);
        $this->_json(1, mys_lang('操作成功'));
    }

    // 隐藏或者启用
    public function hidden_edit() {

        $id = (int)\Soulcms\Service::L('input')->get('id');
        $row = \Soulcms\Service::M('Module')->table('module')->get($id);
        !$row && $this->_json(0, mys_lang('数据#%s不存在', $id));

        $v = $row['disabled'] ? 0 : 1;
        \Soulcms\Service::M('Module')->table('module')->update($id, ['disabled' => $v]);

        \Soulcms\Service::M('cache')->sync_cache(''); // 自动更新缓存
        exit($this->_json(1, mys_lang($v ? '模块已被禁用' : '模块已被启用'), ['value' => $v]));
    }

    // 模块配置
    public function edit() {

        $id = (int)\Soulcms\Service::L('input')->get('id');
        $data = \Soulcms\Service::M()->table('module')->get($id);
        !$data && $this->_admin_msg(0, mys_lang('数据#%s不存在', $id));

        // 格式转换
        $data['site'] = mys_string2array($data['site']);
        $data['setting'] = mys_string2array($data['setting']);

        // 判断站点
        !$data['site'][SITE_ID] && $this->_admin_msg(0, mys_lang('当前站点尚未安装'));

        // 默认显示字段
        !$data['setting']['list_field'] && $data['setting']['list_field'] = [
            'title' => [
                'use' => 1,
                'name' => mys_lang('主题'),
                'func' => 'title',
                'width' => 0,
                'order' => 1,
            ],
            'catid' => [
                'use' => 1,
                'name' => mys_lang('栏目'),
                'func' => 'catid',
                'width' => 120,
                'order' => 2,
            ],
            'author' => [
                'use' => 1,
                'name' => mys_lang('作者'),
                'func' => 'author',
                'width' => 100,
                'order' => 3,
            ],
            'updatetime' => [
                'use' => 1,
                'name' => mys_lang('更新时间'),
                'func' => 'datetime',
                'width' => 160,
                'order' => 4,
            ],
        ];
        // 默认显示字段
        !$data['setting']['comment_list_field'] && $data['setting']['comment_list_field'] = [
            'content' => [
                'use' => 1,
                'name' => mys_lang('评论'),
                'func' => 'comment',
                'width' => 0,
                'order' => 1,
            ],
            'author' => [
                'use' => 1,
                'name' => mys_lang('作者'),
                'func' => 'author',
                'width' => 100,
                'order' => 3,
            ],
            'inputtime' => [
                'use' => 1,
                'name' => mys_lang('评论时间'),
                'func' => 'datetime',
                'width' => 160,
                'order' => 4,
            ],
        ];


        if (IS_AJAX_POST) {
            $post = \Soulcms\Service::L('input')->post('data', true);
            $rt = \Soulcms\Service::M('Module')->config($data, $post);
            if ($rt['code']) {
                \Soulcms\Service::M('cache')->sync_cache(''); // 自动更新缓存
                $this->_json(1, '操作成功');
            } else {
                $this->_json(0, $rt['msg']);
            }
        }

        // 主表字段
        $field = \Soulcms\Service::M()->db->table('field')
            ->where('disabled', 0)
            ->where('ismain', 1)
            ->where('relatedname', 'module')
            ->where('relatedid', $id)
            ->orderBy('displayorder ASC,id ASC')
            ->get()->getResultArray();
        $sys_field = \Soulcms\Service::L('Field')->sys_field(['id', 'catid', 'author', 'inputtime', 'updatetime']);
        sort($sys_field);
        $field = mys_array2array($sys_field, $field);

        // 评论字段
        $comment_field = \Soulcms\Service::M()->db->table('field')
            ->where('disabled', 0)
            ->where('ismain', 1)
            ->where('relatedname', 'comment-module-'.$data['dirname'])
            ->orderBy('displayorder ASC,id ASC')
            ->get()->getResultArray();
        $sys_field = \Soulcms\Service::L('Field')->sys_field(['content', 'author', 'inputtime']);
        $comment_field = mys_array2array($sys_field, $comment_field);


        $page = intval(\Soulcms\Service::L('input')->get('page'));
        $config = require mys_get_app_dir($data['dirname']).'Config/App.php';

        if (!$data['site'][SITE_ID]['title']) {
            $data['site'][SITE_ID]['title'] = $config['name'];
        }

        \Soulcms\Service::V()->assign([
            'page' => $page,
            'data' => $data,
            'site' => $data['site'][SITE_ID],
            'form' => mys_form_hidden(['page' => $page]),
            'field' => $field,
            'is_hcategory' => isset($config['hcategory']) && $config['hcategory'],
            'comment_field' => $comment_field,
        ]);
        \Soulcms\Service::V()->display('module_edit.html');
    }

    // 推荐位
    public function flag_edit() {

        $id = (int)\Soulcms\Service::L('input')->get('id');
        $data = \Soulcms\Service::M('Module')->table('module')->get($id);
        !$data && $this->_admin_msg(0, mys_lang('数据#%s不存在', $id));

        // 格式转换
        $data['setting'] = mys_string2array($data['setting']);

        if (IS_AJAX_POST) {
            $post = \Soulcms\Service::L('input')->post('flag', true);
            $rt = \Soulcms\Service::M('Module')->config($data, null, [
                'flag' => $post,
            ]);
            if ($rt['code']) {
                \Soulcms\Service::M('cache')->sync_cache(''); // 自动更新缓存
                $this->_json(1, '操作成功');
            } else {
                $this->_json(0, $rt['msg']);
            }
        }

        \Soulcms\Service::V()->assign([
            'flag' => $data['setting']['flag'],
            'form' => mys_form_hidden(),
        ]);
        \Soulcms\Service::V()->display('module_flag.html');
    }

    // 模块表单
    public function form_index() {

        \Soulcms\Service::V()->assign([
            'list' => \Soulcms\Service::M()->table('module_form')->where('module', $this->dir)->getAll(),
        ]);
        \Soulcms\Service::V()->display('module_form.html');
    }

    // 创建模块表单
    public function form_add() {

        if (IS_AJAX_POST) {
            $data = \Soulcms\Service::L('input')->post('data', true);
            $this->_validation(0, $data);
            \Soulcms\Service::L('input')->system_log('创建模块['.$this->dir.']表单('.$data['name'].')');
            $rt = \Soulcms\Service::M('Module')->create_form($this->dir, $data);
            if ($rt['code']) {
                \Soulcms\Service::M('cache')->sync_cache(''); // 自动更新缓存
                $this->_json(1, mys_lang('操作成功，请刷新后台页面'));
            } else {
                $this->_json(0, $rt['msg']);
            }
        }

        \Soulcms\Service::V()->assign([
            'form' => mys_form_hidden()
        ]);
        \Soulcms\Service::V()->display('module_form_add.html');
        exit;
    }

    // 修改模块表单
    public function form_edit() {

        $id = intval(\Soulcms\Service::L('input')->get('id'));
        $data = \Soulcms\Service::M()->table('module_form')->get($id);
        !$data && $this->_admin_msg(0, mys_lang('模块表单（%s）不存在', $id));

        $data['setting'] = mys_string2array($data['setting']);
        !$data['setting']['list_field'] && $data['setting']['list_field'] = [
            'title' => [
                'use' => 1,
                'name' => mys_lang('主题'),
                'func' => 'title',
                'width' => 0,
                'order' => 1,
            ],
            'author' => [
                'use' => 1,
                'name' => mys_lang('作者'),
                'func' => 'author',
                'width' => 100,
                'order' => 2,
            ],
            'inputtime' => [
                'use' => 1,
                'name' => mys_lang('录入时间'),
                'func' => 'datetime',
                'width' => 160,
                'order' => 3,
            ],
        ];

        if (IS_AJAX_POST) {
            $data = \Soulcms\Service::L('input')->post('data', true);
            \Soulcms\Service::M('Module')->table('module_form')->update($id,
                [
                    'name' => $data['name'],
                    'setting' => mys_array2string($data['setting'])
                ]
            );

            \Soulcms\Service::M('cache')->sync_cache(''); // 自动更新缓存
            \Soulcms\Service::L('input')->system_log('修改模块['.$this->dir.']表单('.$data['name'].')配置');
            exit($this->_json(1, mys_lang('操作成功')));
        }

        // 主表字段
        $field = \Soulcms\Service::M()->db->table('field')
            ->where('disabled', 0)
            ->where('ismain', 1)
            ->where('relatedname', 'mform-'.$this->dir)
            ->where('relatedid', $id)
            ->orderBy('displayorder ASC,id ASC')
            ->get()->getResultArray();
        $sys_field = \Soulcms\Service::L('Field')->sys_field(['id', 'author', 'inputtime']);
        sort($sys_field);

        $page = intval(\Soulcms\Service::L('input')->get('page'));

        \Soulcms\Service::V()->assign([
            'data' => $data,
            'page' => $page,
            'form' => mys_form_hidden(['page' => $page]),
            'field' => mys_array2array($sys_field, $field),
        ]);
        \Soulcms\Service::V()->display('module_form_edit.html');
    }

    // 删除表单
    public function form_del() {

        $ids = \Soulcms\Service::L('input')->get_post_ids();
        !$ids && exit($this->_json(0, mys_lang('你还没有选择呢')));

        $rt = \Soulcms\Service::M('Module')->delete_form($ids);
        !$rt['code'] && exit($this->_json(0, $rt['msg']));

        \Soulcms\Service::M('cache')->sync_cache(''); // 自动更新缓存
        \Soulcms\Service::L('input')->system_log('批量删除模块表单: '. @implode(',', $ids));

        exit($this->_json(1, mys_lang('操作成功'), ['ids' => $ids]));
    }

    // 验证数据
    private function _validation($id, $data) {

        list($data, $return) = \Soulcms\Service::L('Form')->validation($data, $this->form);
        $return && exit($this->_json(0, $return['error'], ['field' => $return['name']]));
        \Soulcms\Service::M()->table('module_form')->where('module', $this->dir)->is_exists($id, 'table', $data['table']) && exit($this->_json(0, mys_lang('数据表名称已经存在'), ['field' => 'table']));
    }


    // 表单初始化
    public function form_init_index() {

        $data = \Soulcms\Service::M()->table('module_form')->getAll();
        !$data && $this->_json(0, mys_lang('没有任何可用表单'));

        $ct = $file = 0;
        foreach ($data as $t) {
            $par = \Soulcms\Service::M()->dbprefix(mys_module_table_prefix($t['module'], SITE_ID)); // 父级表
            if (!\Soulcms\Service::M()->is_table_exists($par)) {
                continue; // 当前站点没有安装
            }
            $rt = \Soulcms\Service::M('Module')->create_form_file($t['module'], $t['table'], 1);
            !$rt['code'] && $this->_json(0, $rt['msg']);
            $file+= (int)$rt['msg'];
            $ct++;
            // 创建统计字段
            $fname = $t['table']."_total";
            if (!\Soulcms\Service::M()->db->fieldExists($fname, $par)) {
                \Soulcms\Service::M()->db->simpleQuery("ALTER TABLE `{$par}` ADD `{$fname}` INT(10) UNSIGNED NULL DEFAULT '0' COMMENT '表单".$t['name']."统计' , ADD INDEX (`".$fname."`) ;");
            }
        }

        \Soulcms\Service::M('cache')->sync_cache(''); // 自动更新缓存
        $this->_json(1, mys_lang('本站点共（%s）个表单，重建（%s）个文件', $ct, $file));
    }


}
