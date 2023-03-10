<?php namespace Soulcms\Library;

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


/**
 * 附件类
 */
class Upload
{

    private $error;
    private $notallowed;

    /**
     * 构造函数
     */
    public function __construct(...$params) {
        // 返回错误信息
        $this->error = [
            "SUCCESS",
            mys_lang("文件大小超出 upload_max_filesize 限制"),
            mys_lang("文件大小超出 MAX_FILE_SIZE 限制"),
            mys_lang("文件未被完整上传"),
            mys_lang("没有文件被上传"),
            mys_lang("上传文件为空"),
            "ERROR_TMP_FILE" => mys_lang("临时文件错误"),
            "ERROR_TMP_FILE_NOT_FOUND" => mys_lang("找不到临时文件"),
            "ERROR_SIZE_EXCEED" => mys_lang("文件大小超出网站限制"),
            "ERROR_TYPE_NOT_ALLOWED" => mys_lang("文件类型不允许"),
            "ERROR_SYSTEM_TYPE_NOT_ALLOWED" => mys_lang("文件类型被系统禁止上传"),
            "ERROR_CREATE_DIR" => mys_lang("目录创建失败"),
            "ERROR_DIR_NOT_WRITEABLE" => mys_lang("目录没有写权限"),
            "ERROR_FILE_MOVE" => mys_lang("文件保存时出错"),
            "ERROR_FILE_NOT_FOUND" => mys_lang("找不到上传文件"),
            "ERROR_WRITE_CONTENT" => mys_lang("写入文件内容错误"),
            "ERROR_UNKNOWN" => mys_lang("未知错误"),
            "ERROR_DEAD_LINK" => mys_lang("链接不可用"),
            "ERROR_HTTP_LINK" => mys_lang("链接不是http链接"),
            "ERROR_ATTACH_TYPE" => mys_lang("未知的存储类型"),
            "ERROR_HTTP_CONTENTTYPE" => mys_lang("链接contentType不正确")
        ];
        // 禁止以下文件上传
        $this->notallowed = ['php', 'asp', 'jsp', 'aspx', 'exe', 'sh'];
    }

    // 安全验证
    private function _safe_check($file_ext, $data) {

        // 检查系统保留文件格式
        if (in_array($file_ext, $this->notallowed)) {
            return mys_return_data(0, $this->error['ERROR_SYSTEM_TYPE_NOT_ALLOWED']);
        }

        // 验证扩展名格式
        if (!preg_match('/^[a-z0-9]+$/i', $file_ext)) {
            return mys_return_data(0, mys_lang('此文件扩展名不安全，禁止上传'));
        }

        // 验证伪装图片
        if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $data = strtolower($data);
            if (strpos($data, '<?php') !== false) {
                return mys_return_data(0, mys_lang('此文件不安全，禁止上传'));
            } elseif (strpos($data, 'eval(') !== false) {
                return mys_return_data(0, mys_lang('此文件不安全，禁止上传'));
            } elseif (strpos($data, '.php') !== false) {
                return mys_return_data(0, mys_lang('此文件不安全，禁止上传'));
            } elseif (strpos($data, 'base64_decode(') !== false) {
                return mys_return_data(0, mys_lang('此文件不安全，禁止上传'));
            }
        }

        return mys_return_data(1, 'ok');
    }

    /**
     * 上传文件
     */
    public function upload_file($config) {

        $file = isset($_FILES[$config['form_name']]) ? $_FILES[$config['form_name']] : null;

        if (!$file) {
            return mys_return_data(0, $this->error['ERROR_TMP_FILE_NOT_FOUND']);
        } else if (isset($file['error']) && $file['error']) {
            return mys_return_data(0, $this->_error_msg($file['error']));
        } else if (!file_exists($file['tmp_name'])) {
            return mys_return_data(0, $this->error['ERROR_TMP_FILE_NOT_FOUND']);
        } else if (!is_uploaded_file($file['tmp_name'])) {
            return mys_return_data(0, $this->error['ERROR_TMPFILE']);
        }

        $name = substr(md5(SYS_TIME), rand(0, 20), 15); // 随机新名字
        $file_ext = $this->_file_ext($file['name']); // 扩展名
        $file_name = $this->_file_name($file['name']); // 文件实际名字

        // 安全验证
        $rt = $this->_safe_check($file_ext, file_get_contents($file["tmp_name"]));
        if (!$rt['code']) {
            return mys_return_data(0, $rt['msg']);
        }

        if ($file['size'] > $config['file_size']) {
            // 文件大小限制
            return mys_return_data(0, $this->error['ERROR_SIZE_EXCEED']. ' '.($config['file_size']/1024/1024).'MB');
        } elseif ($config['file_exts'][0] != '*' && !in_array($file_ext, $config['file_exts'])) {
            // 检查是文件格式
            return mys_return_data(0, $this->error['ERROR_TYPE_NOT_ALLOWED'] . $file_ext);
        }

        // 保存目录名称
        if (isset($config['save_path']) && $config['save_path']) {
            $path = $config['save_path'];
        } else {
            $path = isset($config['path']) && $config['path'] ? $config['path'].'/' : date('Ym', SYS_TIME).'/';
        }

        $file_path = $path.$name.'.'.$file_ext;

        // 开始上传存储文件
        $rt = $this->save_file('upload', $file["tmp_name"], $file_path, $config['attachment']);
        if (!$rt['code']) {
            return mys_return_data(0, $rt['msg']);
        }

        $url = $config['attachment']['url'].$file_path;
        $md5 = $rt['data']['md5'];

        // 如果是图片先获取图片尺寸
        $info = [];
        list($info['width'], $info['height']) = @getimagesize($config['attachment']['value']['path'].$file_path);

        // 文件预览
        $preview = mys_file_preview_html($url);

        return mys_return_data(1, 'ok', [
            'ext' => $file_ext,
            'url' => $url,
            'md5' => $md5,
            'file' => $file_path,
            'size' => $file['size'],
            'path' => $config['attachment']['value']['path'].$file_path,
            'name' => $file_name,
            'info' => $info,
            'remote' => $config['attachment']['id'],
            'preview' => $preview,
        ]);
    }

    /**
     * 更新文件
     */
    public function update_file($config) {

        $file = isset($_FILES[$config['form_name']]) ? $_FILES[$config['form_name']] : null;

        if (!$file) {
            return mys_return_data(0, $this->error['ERROR_TMP_FILE_NOT_FOUND']);
        } else if (isset($file['error']) && $file['error']) {
            return mys_return_data(0, $this->_error_msg($file['error']));
        } else if (!file_exists($file['tmp_name'])) {
            return mys_return_data(0, $this->error['ERROR_TMP_FILE_NOT_FOUND']);
        } else if (!is_uploaded_file($file['tmp_name'])) {
            return mys_return_data(0, $this->error['ERROR_TMPFILE']);
        }

        $file_ext = $this->_file_ext($file['name']); // 扩展名
        // 安全验证
        $rt = $this->_safe_check($file_ext, file_get_contents($file["tmp_name"]));
        if (!$rt['code']) {
            return mys_return_data(0, $rt['msg']);
        }

        // 检查是文件格式
        if ($config['file_exts'][0] != '*' && !in_array($file_ext, $config['file_exts'])) {
            return mys_return_data(0, $this->error['ERROR_TYPE_NOT_ALLOWED'] . $file_ext);
        }

        if (!(move_uploaded_file($file["tmp_name"], $config['file_name']) || !is_file($config['file_name']))) {
            return mys_return_data(0, $this->error['ERROR_FILE_MOVE']);
        }

        return mys_return_data(1, 'ok');
    }

    /**
     * 下载文件
     */
    public function down_file($config) {

        $data = mys_catcher_data($config['url']);
        if (!$data) {
            log_message('error', '服务器无法下载文件：'.$config['url']);
            return mys_return_data(0, mys_lang('文件下载失败'));
        }

        $name = substr(md5(SYS_TIME), rand(0, 20), 15); // 随机新名字
        $file_ext = $this->_file_ext($config['url']); // 扩展名

        // 安全验证
        $rt = $this->_safe_check($file_ext, $data);
        if (!$rt['code']) {
            return mys_return_data(0, $rt['msg']);
        }

        $file_name = $this->_file_name($config['url']); // 文件实际名字
        if (!$file_ext) {
            log_message('error', '无法获取文件扩展名：'.$config['url']);
            return mys_return_data(0, mys_lang('无法获取文件扩展名'));
        }

        // 保存目录名称
        $path = isset($config['path']) && $config['path'] ? $config['path'].'/' : date('Ym', SYS_TIME).'/';
        $file_path = $path.$name.'.'.$file_ext;

        // 开始上传存储文件
        $rt = $this->save_file('content', $data, $file_path, $config['attachment']);
        if (!$rt['code']) {
            return mys_return_data(0, $rt['msg']);
        }

        // 上传成功
        $url = $config['attachment']['url'].$file_path;

        // 如果是图片先获取图片尺寸
        $info = [];
        list($info['width'], $info['height']) = @getimagesize($config['attachment']['value']['path'].$file_path);

        // 文件预览
        $preview = mys_file_preview_html($url);
        return mys_return_data(1, 'ok', [
            'ext' => $file_ext,
            'url' => $url,
            'md5' => md5($data),
            'file' => $file_path,
            'size' => (int)$rt['data']['size'],
            'path' => $config['attachment']['value']['path'].$file_path,
            'name' => $file_name,
            'info' => $info,
            'remote' => $config['attachment']['id'],
            'preview' => $preview,
        ]);
    }

    //
    public function base64_image($config) {

        $data = $config['content'];

        $name = substr(md5(SYS_TIME), rand(0, 20), 15); // 随机新名字
        $file_ext = $config['ext'] ? $config['ext'] : 'jpg'; // 扩展名
        $file_name = 'base64_image'; // 文件实际名字


        // 安全验证
        $rt = $this->_safe_check($file_ext, $data);
        if (!$rt['code']) {
            return mys_return_data(0, $rt['msg']);
        }

        // 保存目录名称
        $path = isset($config['path']) && $config['path'] ? $config['path'].'/' : date('Ym', SYS_TIME).'/';
        $file_path = $path.$name.'.'.$file_ext;

        // 开始上传存储文件
        $rt = $this->save_file('content', $data, $file_path, $config['attachment']);
        if (!$rt['code']) {
            return mys_return_data(0, $rt['msg']);
        }

        // 上传成功
        $url = $config['attachment']['url'].$file_path;

        // 如果是图片先获取图片尺寸
        $info = [];
        list($info['width'], $info['height']) = @getimagesize($config['attachment']['value']['path'].$file_path);

        // 文件预览
        $preview = mys_file_preview_html($url);
        return mys_return_data(1, 'ok', [
            'ext' => $file_ext,
            'url' => $url,
            'md5' => md5($data),
            'file' => $file_path,
            'size' => $rt['data']['size'],
            'path' => $config['attachment']['value']['path'].$file_path,
            'name' => $file_name,
            'info' => $info,
            'remote' => $config['attachment']['id'],
            'preview' => $preview,
        ]);
    }

    /**
     * 存储文件
     */
    public function save_file($type, $data, $file_path, $attachment, $watermark = 0) {

        // 按照附件存储类型来保存文件
        $storage = new \Soulcms\Library\Storage();
        return $storage->upload($type == 'upload' ? 1 : 0, $data, $file_path, $attachment, $watermark);
    }

    /**
     * 上传错误
     */
    private function _error_msg($code) {
        return !$this->error[$code] ? $this->error["ERROR_UNKNOWN"] : $this->error[$code];
    }

    /**
     * 获取文件名
     */
    private function _file_name($name) {
        strpos($name, '/') !== false && $name = trim(strrchr($name, '/'), '/');
        return substr($name, 0, strrpos($name, '.'));
    }

    /**
     * 获取文件扩展名
     */
    private function _file_ext($name) {
        return str_replace('.', '', trim(strtolower(strrchr($name, '.')), '.'));
    }

}