<?php namespace Soulcms\Controllers\Admin;

/**
 * 二次开发时可以修改本文件，不影响升级覆盖
 */

class Comment_verify extends \Soulcms\Admin\Comment
{

    public function index() {
        $this->_Admin_List();
    }

    public function edit() {
        $this->_Admin_Edit();
    }

    public function show_index() {
        $this->_Admin_Show();
    }

    public function del() {
        $this->_Admin_Del();
    }
    
    public function status_index() {
        $this->_Admin_Status();
    }
    
}
