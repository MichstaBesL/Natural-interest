<?php namespace Soulcms\Controllers;

/**
 * 二次开发时可以修改本文件，不影响升级覆盖
 */

class Comment extends \Soulcms\Home\Comment
{

    public function index() {
        parent::_Index();
    }

    public function post() {
        parent::_Post();
    }


    public function op() {
        parent::_Op();
    }

    // 格式化评论内容，方便二次开发和重写
    public function _safe_replace($data) {

        $value = $_POST['data']['content'];
        if (isset($_POST['editorValue']) && $_POST['editorValue']) {
            $value = $_POST['editorValue'];
        }

        return str_replace(['<p><br/></p>'], '', $value);
    }
}
