<?php namespace Soulcms\Controllers\Member;

class Home extends \Soulcms\Member\Module
{

	// 内容列表
	public function index() {
		$this->_Member_List();
	}

	public function add() {
		$this->_Member_Add();
	}

	public function edit() {
		$this->_Member_Edit();
	}

	public function del() {
		$this->_Member_Del();
	}
}
