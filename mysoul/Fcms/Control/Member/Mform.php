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




// 内容模块表单操作类 基于 Ftable
class Mform extends \Soulcms\Table
{
    public $cid; // 内容id
    public $form; // 表单信息
    public $is_verify; // 判断是否来自审核控制器
    protected $is_add_menu = 1; //允许有添加菜单

    public function __construct(...$params) {
        parent::__construct(...$params);
        // 初始化模块
        $this->_module_init(APP_DIR);
        // 判断表单是否操作
        $this->form = $this->module['form'][\Soulcms\Service::L('Router')->class];
        if (!$this->form) {
            $this->_msg(0, mys_lang('模块表单【%s】不存在', \Soulcms\Service::L('Router')->class));
        } elseif (!$this->form['setting']['is_member']) {
            $this->_msg(0, mys_lang('模块表单【%s】没有管理内容的权限', \Soulcms\Service::L('Router')->class));
        }
        // 支持附表存储
        $this->is_data = 1;
        // 模板前缀(避免混淆)
        $this->tpl_prefix = 'mform_';
        // 单独模板命名
        $this->tpl_name = $this->form['table'];
        // 模块显示名称
        $this->name = mys_lang('内容模块[%s]表单（%s）', APP_DIR, $this->form['name']);
        // 获取父级内容
        $this->cid = intval(\Soulcms\Service::L('input')->get('cid'));
        if ($this->cid) {
            $this->index = $this->content_model->get_row( $this->cid);
            if ($this->index) {
                if ($this->index['uid'] != $this->uid) {
                    $this->_msg(0, mys_lang('模块表单【%s】父内容[%s]不是你创建', $this->form['name'], $this->cid));
                }
            } else {
                $this->_msg(0, mys_lang('模块表单【%s】父内容[%s]不存在', $this->form['name'], $this->cid));
            }
        } else {
            $this->_msg(0, mys_lang('模块表单【%s】没有cid参数', $this->form['name']));
        }

        $sysfield = ['inputtime', 'inputip', 'displayorder', 'author'];
        // 初始化数据表
        $this->_init([
            'field' => $this->form['field'],
            'table' => mys_module_table_prefix(APP_DIR).'_form_'.$this->form['table'],
            'sys_field' => $sysfield,
            'date_field' => 'inputtime',
            'show_field' => 'title',
            'list_field' => $this->form['setting']['list_field'],
            'order_by' => 'inputtime desc',
            'where_list' => 'cid='. $this->cid, // 自定义条件，显示本内容的表单
        ]);
        $this->edit_where = $this->delete_where = 'cid='. $this->cid;
        // 是否有验证码
        $this->is_post_code = mys_member_auth(
            $this->member_authid,
            $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['code']
        );

        // 写入模板
        \Soulcms\Service::V()->assign([
            'mform' => $this->form,
            'index' => $this->index,
            'field' => $this->init['field'],
            'form_url' => mys_member_url(APP_DIR.'/'.$this->form['table'].'/index', ['cid' =>  $this->cid]),
            'is_verify' => $this->is_verify,
        ]);
    }

    // ========================

    // 查看列表
    protected function _Member_List() {

        list($tpl) = $this->_List(['cid' => $this->cid]);

        $del = 1;
        if (!$this->is_hcategory) {
            $cat = $this->_module_member_category($this->module['category'], $this->module['dirname'], 'del');
            if (!isset($cat[$this->index['catid']])) {
                $del = 0;
            }
        } else {
            $this->content_model->_hcategory_member_del_auth();
        }

        \Soulcms\Service::V()->assign([
            'p' => ['cid' => $this->cid],
            'is_delete' => $del,
        ]);
        \Soulcms\Service::V()->display($tpl);
    }

    // 添加内容
    protected function _Member_Add() {

        if (!$this->is_hcategory) {
            // 走栏目权限
            $category = $this->_module_member_category($this->module['category'], $this->module['dirname'], 'add');
            if (!$category[$this->index['catid']]) {
                $this->_msg(0, mys_lang('当前栏目(%s)没有发布权限', (int)$this->index['catid']));
            }
        } else {
            // 不走栏目权限，走自定义权限
            $this->content_model->_hcategory_member_add_auth();
        }

        list($tpl) = $this->_Post(0);

        \Soulcms\Service::V()->assign([
            'form' =>  mys_form_hidden(),
        ]);
        \Soulcms\Service::V()->display($tpl);
    }

    // 修改内容
    protected function _Member_Edit() {

        if (!$this->is_hcategory) {
            // 走栏目权限
            $category = $this->_module_member_category($this->module['category'], $this->module['dirname'], 'edit');
            if (!$category[$this->index['catid']]) {
                $this->_msg(0, mys_lang('当前栏目(%s)没有修改权限', (int)$this->index['catid']));
            }
        } else {
            // 不走栏目权限，走自定义权限
            $this->content_model->_hcategory_member_edit_auth();
        }

        $id = intval(\Soulcms\Service::L('input')->get('id'));
        list($tpl, $data) = $this->_Post($id);

        if (!$data) {
            $this->_msg(0, mys_lang('数据不存在: '.$id));
        } elseif ($this->cid != $data['cid']) {
            $this->_msg(0, mys_lang('cid不匹配'));
        }

        \Soulcms\Service::V()->assign([
            'form' =>  mys_form_hidden(),
        ]);
        \Soulcms\Service::V()->display($tpl);
    }

    // 批量保存排序值
    protected function _Member_Order() {
        $this->_Display_Order(
            intval(\Soulcms\Service::L('input')->get('id')),
            intval(\Soulcms\Service::L('input')->get('value'))
        );
    }

    // 删除内容
    protected function _Member_Del() {

        if (!$this->is_hcategory) {
            $cat = $this->_module_member_category($this->module['category'], $this->module['dirname'], 'del');
            if (!isset($cat[$this->index['catid']])) {
                $this->_json(0, mys_lang('当前栏目没有删除权限'));
            }
        } else {
            $this->content_model->_hcategory_member_del_auth();
        }

        $this->_Del(
            \Soulcms\Service::L('input')->get_post_ids(),
            null,
            function ($rows) {
                // 对应删除提醒
                foreach ($rows as $t) {
                    \Soulcms\Service::M('member')->delete_admin_notice(MOD_DIR.'/'.$this->form['table'].'_verify/edit:cid/'.$t['cid'].'/id/'.$t['id'], SITE_ID);// clear
                    \Soulcms\Service::L('cache')->clear('module_'.MOD_DIR.'_from_'.$this->form['table'].'_show_id_'.$t['id']);
                }
            },
            \Soulcms\Service::M()->dbprefix($this->init['table'])
        );
    }

    // ===========================

    /**
     * 获取内容
     * $id      内容id,新增为0
     * */
    protected function _Data($id = 0) {

        $row = $this->content_model->get_form_row($id, $this->form['table']);
        if (!$row) {
            return [];
        }

        return $row;
    }

    // 格式化保存数据 保存之前
    protected function _Format_Data($id, $data, $old) {

        // 默认数据
        $data[0]['uid'] = (int)$data[1]['uid'];
        $data[1]['cid'] = $data[0]['cid'] =  $this->cid;
        $data[1]['catid'] = $data[0]['catid'] = (int)$this->index['catid'];

        if (!$id) {
            // 发布时

            // 判断日发布量
            $day_post = $this->_member_value(
                $this->member_authid,
                $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['day_post']
            );
            if ($this->uid && $day_post
                && \Soulcms\Service::M()->db
                    ->table($this->init['table'])
                    ->where('uid', $this->uid)
                    ->where('DATEDIFF(from_unixtime(inputtime),now())=0')
                    ->countAllResults() >= $day_post) {
                $this->_json(0, mys_lang('每天发布数量不能超过%s个', $day_post));
            }

            // 判断发布总量
            $total_post = $this->_member_value(
                $this->member_authid,
                $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['total_post']
            );
            if ($this->uid && $total_post
                && \Soulcms\Service::M()->db
                    ->table($this->init['table'])
                    ->where('uid', $this->uid)
                    ->countAllResults() >= $total_post) {
                $this->_json(0, mys_lang('发布数量不能超过%s个', $total_post));
            }

            // 审核状态
            $is_verify = mys_member_auth(
                $this->member_authid,
                $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['verify']
            );
            $data[1]['status'] = $is_verify ? 0 : 1;

            // 默认数据
            $data[0]['uid'] = $data[1]['uid'] = (int)$this->member['uid'];
            $data[1]['author'] = $this->member['username'];
            $data[1]['cid'] = $data[0]['cid'] =  $this->cid;
            $data[1]['catid'] = $data[0]['catid'] = (int)$this->index['catid'];
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
                // 保存之后
                //审核通知
                if ($data[1]['status']) {
                    // 增减金币
                    $score = $this->_member_value($this->member_authid, $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['score']);
                    $score && \Soulcms\Service::M('member')->add_score($this->member['uid'], $score, mys_lang('%s: %s发布', MODULE_NAME, $this->form['name']), $this->index['curl']);
                    // 增减经验
                    $exp = $this->_member_value($this->member_authid, $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['exp']);
                    $exp && \Soulcms\Service::M('member')->add_experience($this->member['uid'], $exp, mys_lang('%s: %s发布', MODULE_NAME, $this->form['name']), $this->index['curl']);
                } else {
                    \Soulcms\Service::M('member')->admin_notice(SITE_ID, 'content', $this->member, mys_lang('%s: %s提交内容审核', MODULE_NAME, $this->form['name']), MOD_DIR.'/'.$this->form['table'].'_verify/edit:cid/'. $this->cid.'/id/'.$id, SITE_ID);
                }
                //更新total字段
                $this->content_model->update_form_total( $this->cid, $this->form['table']);
            }
        );
    }

}
