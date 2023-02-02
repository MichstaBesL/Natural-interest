<?php
//站点域名对应语言配置
$domain = [
    //'域名' => '语言简写'，
    'en.tmcms92.com' => 'en', // 英文站点 域名对于表前缀 en.test.com => en
    'www.tmcms92.com' => 'cn', // 中文站点 域名对于表前缀 cn.test.com => cn
    //'xi.test.com' => 'xi', // 西文站点 域名对于表前缀 xi.test.com => xi
    //'jp.test.com' => 'jp', // 日文站点 域名对于表前缀 jp.test.com => jp
];

// 数据库配置
$db_info = [
    'hostname' => '127.0.0.1',  // 数据库地址
    'username' => 'tmcms92_com',        // 数据库用户名
    'password' => 'tmcms92_com',     // 数据库密码
    'database' => 'tmcms92_com',        // 数据库名称
];

$get_host = $_SERVER['HTTP_HOST']; //获取当前请求域名
if (isset($domain[$get_host]) == false || $domain[$get_host] == '') {
    exit('域名对应语言未配置 请前往lang文件中配置');
}

// 循环域名
foreach ($domain as $key => $value) {
    // 判断域名环境
    if ($get_host == $key) {
        define('WEBPATH', dirname(__FILE__).'/');  // 当前站点目录
        define('WRITEPATH', WEBPATH.'cache_'.$value.'/'); // 缓存文件存储目录
        define('DIY_LANG', $value); // 语言
        define('DB_HOSTNAME', $db_info['hostname']); // 定义数据库地址
        define('DB_USERNAME', $db_info['username']); // 定义数据库用户名
        define('DB_PASSWORD', $db_info['password']); // 定义数据库密码
        define('DB_DATABASE', $db_info['database']); // 定义数据库名称
        define('DB_PREFIX', 'mys_'.$value.'_');      //定义表前缀

        // 停止循环
        break;
    }
}
?>