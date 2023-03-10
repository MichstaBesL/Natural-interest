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


class Input
{

    private $ip_address;

    // get post解析
    public function request($name, $xss = false) {
        $value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : (isset($_POST[$name]) ? $_POST[$name] : (isset($_GET[$name]) ? $_GET[$name] : false));
        return $xss ? $this->xss_clean($value) : $value;
    }
    
    // post解析
    public function post($name, $xss = false) {
        $value = isset($_POST[$name]) ? $_POST[$name] : false;
        return $xss ? $this->xss_clean($value) : $value;
    }

    // get解析
    public function get($name = '', $xss = false) {
        $value = !$name ? $_GET : (isset($_GET[$name]) ? $_GET[$name] : false);
        return $xss ? $this->xss_clean($value) : $value;
    }

    // 通过post格式化ids
    public function get_post_ids($name = 'ids') {

        $in = [];
        $ids = self::post($name);
        if (!$ids) {
            return $in;
        }

        foreach ($ids as $i) {
            $i && $in[] = (int)$i;
        }

        return $in;
    }
    
    public function set_cookie($name, $value = '', $expire = '') {

        $response = \Config\Services::response();
        $response->setcookie($name, $value, $expire);
        $response->send();
    }
    
    public function get_cookie($name) {
        $name = mys_safe_replace($name);
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : false;
    }

    // 获取访客ip地址
    public function ip_address() {

        if ($this->ip_address)
        {
            return $this->ip_address;
        }

        if (getenv('HTTP_CLIENT_IP')) {
            $client_ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
            $client_ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR', true)) {
            $client_ip = getenv('REMOTE_ADDR', true);
        } else {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        
        // 验证规范
        if (!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $client_ip)) {
            $client_ip = '';
        }

        $this->ip_address = $client_ip ? $client_ip : \Config\Services::request(null, true)->getIPAddress();
        $this->ip_address = str_replace([",", '(', ')', ',', chr(13), PHP_EOL], '', $this->ip_address);
        $this->ip_address = trim($this->ip_address);

        return $this->ip_address;
    }
    
    // ip转为实际地址
    public function ip2address($ip) {
        return \Soulcms\Service::L('ip')->address($ip);
    }

    // 当前ip实际地址
    public function ip_address_info() {
       return \Soulcms\Service::L('ip')->address($this->ip_address());
    }
	
	// 安全过滤
	public function get_user_agent() {
		$str = \Config\Services::request(null, true)->getUserAgent();
		return \Soulcms\Service::L('Security')->xss_clean($str);
	}

    /**
     * 后台日志
     */
    public function system_log($action, $insert = 0) {

        if (!$insert) {
            // 是否开启日志
            if (!SYS_LOG || !IS_ADMIN) {
                return NULL;
            }
        }

        $data = array(
            'ip' => $this->ip_address(),
            'uid' => (int)\Soulcms\Service::C()->admin['uid'],
            'time' => SYS_TIME,
            'action' => addslashes($action),
            'username' => \Soulcms\Service::C()->admin['username'] ? \Soulcms\Service::C()->admin['username'] : '未登录',
        );

        $path = WRITEPATH.'log/'.date('Ym', SYS_TIME).'/';
        $file = $path.date('d', SYS_TIME).'.php';
        !is_dir($path) && mys_mkdirs($path);

        file_put_contents($file, PHP_EOL.mys_array2string($data), FILE_APPEND);
    }

    // 服务器ip地址
    public function server_ip() {

        if (isset($_SERVER['SERVER_ADDR'])
            && $_SERVER['SERVER_ADDR']
            && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
            return $_SERVER['SERVER_ADDR'];
        }

        return gethostbyname($_SERVER['HTTP_HOST']);
    }

    // 后台分页
    public function page($url, $total, $dir = '') {

        $config = require CMSPATH.'Config/Apage.php';

        $config['base_url'] = $url.'&page={page}';
        $config['per_page'] = SYS_ADMIN_PAGESIZE;
        $config['total_rows'] = $total;

        return \Soulcms\Service::L('page')->initialize($config)->create_links();
    }

    // Ftable分页
    public function table_page($url, $total, $config, $size) {

        $config['base_url'] = $url;
        $config['per_page'] = $size;
        $config['total_rows'] = $total;

        return \Soulcms\Service::L('page')->initialize($config)->create_links();
    }


    /**
     * XSS Clean
     *
     * @param	string|string[]	$str		Input data
     * @param 	bool		$is_image	Whether the input is an image
     * @return	string
     */
    public function xss_clean($str, $is_image = FALSE)
    {
        return \Soulcms\Service::L('Security')->xss_clean($str, $is_image);
    }

}