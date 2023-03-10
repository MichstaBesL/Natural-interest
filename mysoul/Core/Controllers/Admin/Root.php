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



// 管理员
class Root extends \Soulcms\Table
{
    public $role;

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        \Soulcms\Service::V()->assign([
            'menu' => \Soulcms\Service::M('auth')->_admin_menu(
                [
                    '管理员' => [\Soulcms\Service::L('Router')->class.'/index', 'fa fa-user'],
                    '添加' => [\Soulcms\Service::L('Router')->class.'/add', 'fa fa-plus'],
                    '修改' => ['hide:'.\Soulcms\Service::L('Router')->class.'/edit', 'fa fa-edit'],
                    '登录记录' => ['hide:root/login_index', 'fa fa-calendar'],
                ]
            ),
        ]);
        $this->name = mys_lang('管理员');
        $this->role = \Soulcms\Service::M('auth')->get_role_all();
    }

    public function index() {

        $p = [
            'rid' => intval($_GET['rid']),
            'keyword' => mys_safe_replace($_GET['keyword']),
        ];
        $where = [];
        $p['keyword'] && $where[] = '`username` LIKE "%'.$p['keyword'].'%"';
        $p['rid'] && $where[] = '`uid` IN (select uid from `'.\Soulcms\Service::M()->dbprefix('admin_role_index').'` where roleid='.$p['rid'].')';

        $this->_init([
            'table' => 'admin',
            'join_list' => ['member', 'member.id=admin.uid', 'left'],
            'order_by' => 'uid desc',
            'where_list' => implode(' AND ', $where),
        ]);
        list($a, $data) = $this->_List($p);
        if ($data['list']) {
            foreach ($data['list'] as $i => $t) {
                $role = \Soulcms\Service::M()->db->table('admin_role_index')->where('uid', $t['uid'])->get()->getResultArray();
                if ($role) {
                    foreach ($role as $r) {
                        $data['list'][$i]['role'][$r['roleid']] = $this->role[$r['roleid']]['name'];
                    }
                }
            }
        }
        \Soulcms\Service::V()->assign('p', $p);
        \Soulcms\Service::V()->assign('list', $data['list']);
        \Soulcms\Service::V()->display('root_index.html');
    }

    // 登录记录
    public function login_index() {

        $uid = (int)\Soulcms\Service::L('input')->get('id');
        $this->_init([
            'table' => 'admin_login',
            'order_by' => 'logintime desc',
            'where_list' => 'uid='.$uid,
        ]);
        list($a, $data) = $this->_List(['uid' => $uid]);

        \Soulcms\Service::V()->assign([
            'list' => $data['list'],
            'user' => mys_member_info($uid),
        ]);
        \Soulcms\Service::V()->display('root_login.html');exit;

    }

    public function ck_index() {

        $name = mys_safe_replace($_GET['name']);
        $data = \Soulcms\Service::M()->db->table('member')->where('username', $name)->get()->getRowArray();
        $this->_json($data ? 0 : 1, 'ok');
    }

    public function add() {

        if (IS_AJAX_POST) {
            $post = \Soulcms\Service::L('input')->post('data');
            $name = mys_safe_replace($post['username']);
            !$name && $this->_json(0, mys_lang('账号不能为空'), ['field' => 'username']);
            !$post['role'] && $this->_json(0, mys_lang('至少要选择一个角色组'), ['field' => 'role']);
            $data = \Soulcms\Service::M()->db->table('member')->where('username', $name)->get()->getRowArray();
            if (!$data) {
                // 注册账号
                if (!\Soulcms\Service::L('Form')->check_email($post['email'])) {
                    $this->_json(0, mys_lang('邮箱格式不正确'), ['field' => 'email']);
                } elseif (!\Soulcms\Service::L('Form')->check_phone($post['phone'])) {
                    $this->_json(0, mys_lang('手机号码格式不正确'), ['field' => 'phone']);
                } elseif (empty($post['password'])) {
                    $this->_json(0, mys_lang('密码必须填写'), ['field' => 'password']);
                }
                $rt = \Soulcms\Service::M('member')->register(0, [
                    'username' => $post['username'],
                    'phone' => $post['phone'],
                    'email' => $post['email'],
                    'name' => $post['name'],
                    'password' => mys_safe_password($post['password']),
                ]);
                if (!$rt['code']) {
                    // 注册失败
                    $this->_json(0, $rt['msg'], ['field' => $rt['data']['field']]);
                }
                $data = $rt['data'];
            }
            $rt = \Soulcms\Service::M()->table('admin')->insert([
                'uid' => $data['id'],
                'setting' => '',
                'usermenu' => '',
            ]);
            !$rt['code'] && $this->_json(0, $rt['msg']);
            foreach ($post['role'] as $t) {
                \Soulcms\Service::M()->table('admin_role_index')->insert([
                    'uid' => $data['id'],
                    'roleid' => $t,
                ]);
            }
            \Soulcms\Service::M()->table('member_data')->update($data['id'], ['is_admin' => 1]);
            $this->_json(1, mys_lang('操作成功'));
        }

        \Soulcms\Service::V()->assign([
            'post_url' =>\Soulcms\Service::L('Router')->url('root/add'),
            'reply_url' =>\Soulcms\Service::L('Router')->url('root/index'),
        ]);
        \Soulcms\Service::V()->display('root_add.html');
    }

    public function edit() {

        $id = intval(\Soulcms\Service::L('input')->get('id'));
        $data = \Soulcms\Service::M()->db->table('admin')->where('uid', $id)->get()->getRowArray();
        !$data && $this->_admin_msg(0, mys_lang('账号不存在'));

        $member = \Soulcms\Service::M()->db->table('member')->where('id', $data['uid'])->get()->getRowArray();
        !$member && $this->_admin_msg(0, mys_lang('账号不存在'));

        if (IS_AJAX_POST) {
            $post = \Soulcms\Service::L('input')->post('data');
            !$post['role'] && $this->_json(0, mys_lang('至少要选择一个角色组'), ['field' => 'role']);
            if (!\Soulcms\Service::L('Form')->check_email($post['email'])) {
                $this->_json(0, mys_lang('邮箱格式不正确'), ['field' => 'email']);
            } elseif (!\Soulcms\Service::L('Form')->check_phone($post['phone'])) {
                $this->_json(0, mys_lang('手机号码格式不正确'), ['field' => 'phone']);
            } elseif (\Soulcms\Service::M()->db->table('member')->where('id<>'. $member['id'])->where('email', $post['email'])->countAllResults()) {
                $this->_json(0, mys_lang('邮箱%s已经注册', $post['email']), ['field' => 'email']);
            } elseif (\Soulcms\Service::M()->db->table('member')->where('id<>'. $member['id'])->where('phone', $post['phone'])->countAllResults()) {
                $this->_json(0, mys_lang('手机号码%s已经注册', $post['phone']), ['field' => 'phone']);
            }
            \Soulcms\Service::M()->table('member')->update($member['id'], [
                'phone' => $post['phone'],
                'email' => $post['email'],
                'name' => $post['name'],
            ]);
            $post['password'] && \Soulcms\Service::M('member')->edit_password($member, $post['password']);
            \Soulcms\Service::M()->db->table('admin_role_index')->where('uid', $member['id'])->delete();
            foreach ($post['role'] as $t) {
                \Soulcms\Service::M()->table('admin_role_index')->replace([
                    'uid' => $member['id'],
                    'roleid' => $t,
                ]);
            }
            $this->_json(1, mys_lang('操作成功'));
        }

        $data = $data + $member;
        $role = \Soulcms\Service::M()->db->table('admin_role_index')->where('uid', $data['uid'])->get()->getResultArray();
        if ($role) {
            foreach ($role as $r) {
                $data['role'][$r['roleid']] = $this->role[$r['roleid']]['name'];
            }
        }
        \Soulcms\Service::V()->assign([
            'edit' => 1,
            'data' => $data,
            'post_url' =>\Soulcms\Service::L('Router')->url('root/add'),
            'reply_url' =>\Soulcms\Service::L('Router')->url('root/index'),
        ]);
        \Soulcms\Service::V()->display('root_add.html');
    }

    // 后台删除url内容
    public function del() {

        $ids = \Soulcms\Service::L('input')->get_post_ids();
        in_array(1, $ids) && $this->_json(0, mys_lang('创始人账号不能删除'));
        in_array($this->uid, $ids) && $this->_json(0, mys_lang('不能删除自己'));

        foreach ($ids as $u) {
            \Soulcms\Service::M()->db->table('admin')->where('uid', $u)->delete();
            \Soulcms\Service::M()->db->table('admin_login')->where('uid', $u)->delete();
            \Soulcms\Service::M()->db->table('admin_role_index')->where('uid', $u)->delete();
            \Soulcms\Service::M()->table('member_data')->update($u, ['is_admin' => 0]);
        }
        $this->_json(1, mys_lang('操作成功'));
    }

}
