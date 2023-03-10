<?php namespace Soulcms;

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




// 公共类
abstract class Common extends \CodeIgniter\Controller
{

    private static $instance;

    private $load_init;

    public $uid;
    public $admin;
    public $member;
    public $module;
    public $member_cache;
    public $member_authid;

    public $site; // 网站id信息
    public $site_info; // 网站配置信息
    public $site_domain; // 全部站点域名
    public $is_hcategory; // 模块不使用栏目

    public $session; // 网站session对象
    public $is_mobile; // 是否移动端
    public $temp; // 临时数据存储

    protected $is_module_init; // 防止模块重复初始化
    protected $cmf_version; // 版本信息


    /**
     * 初始化共享控制器
     */
    public function __construct(...$params)
    {
        //parent::initController(...$params);

        self::$instance =& $this;

        if (defined('IS_INSTALL')) {
            return;
        }

        // 站点配置
        if (is_file(WRITEPATH.'config/site.php')) {
            $this->site_info = require WRITEPATH.'config/site.php';
            foreach ($this->site_info as $id => $t) {
                !$t['SITE_DOMAIN'] && $t['SITE_DOMAIN'] = DOMAIN_NAME;
                $this->site[$id] = $id;
                $this->site_info[$id] = $t;
                $this->site_info[$id]['SITE_ID'] = $id;
                $this->site_info[$id]['SITE_URL'] = mys_http_prefix($t['SITE_DOMAIN'].'/');
                $this->site_info[$id]['SITE_MURL'] = mys_http_prefix(($t['SITE_MOBILE'] ? $t['SITE_MOBILE'] : $t['SITE_DOMAIN']).'/');
                $this->site_info[$id]['SITE_IS_MOBILE'] = $t['SITE_MOBILE'] ? 1 : 0;
            }
        } else {
            //!defined('IS_NOT_301') && define('IS_NOT_301', 1);
            $this->site_info[1] = [
                'SITE_ID' => 1,
                'SITE_URL' => mys_http_prefix(DOMAIN_NAME.'/'),
                'SITE_MURL' => mys_http_prefix(DOMAIN_NAME.'/'),
            ];
        }

        // 版本
        if (!is_file(CMSPATH.'Config/Version.php')) {
            define('CMF_VERSION', '开发版');
            $this->cmf_version = [
                'id' => 1,
                'name' => 'SOULCMS',
                'version' => '开发版',
            ];
        } else {
            $this->cmf_version = require CMSPATH.'Config/Version.php';
            define('CMF_VERSION', $this->cmf_version['version']);
        }

        $client = []; // 电脑域名对应的手机域名
        if (is_file(WRITEPATH.'config/domain_client.php')) {
            $client = require WRITEPATH.'config/domain_client.php';
        }

        $this->site_domain = []; // 全网域名对应的站点id
        if (is_file(WRITEPATH.'config/domain_site.php')) {
            $this->site_domain = require WRITEPATH.'config/domain_site.php';
        }

        // 站点id
        !defined('SITE_ID') && define('SITE_ID', 1);

        // 判断是否是网站的"其他域名"
        if (!IS_API && !IS_ADMIN && in_array(DOMAIN_NAME, $this->site_info[SITE_ID]['SITE_DOMAINS'])) {
            // 当前域名既不是手机域名也不是电脑域名就301定向网站的域名
            mys_domain_301(!$this->_is_mobile() ? $this->site_info[SITE_ID]['SITE_URL'] : $this->site_info[SITE_ID]['SITE_MURL']);
        }

        // 开启自动跳转手机端(api、admin、member不跳转)
        if (!IS_API // api不跳转
            && !IS_ADMIN // 后台不跳转
            && !IS_MEMBER // 会员中心不跳
            && !defined('IS_NOT_301') // 定义禁止301不跳
            && $client // 没有客户端不跳
            && $this->site_info[SITE_ID]['SITE_MOBILE'] // 没有绑定移动端域名不跳
            //&& !in_array(DOMAIN_NAME, $client) // 当前域名不存在于客户端中时
            && $this->site_info[SITE_ID]['SITE_AUTO'] // 开启自动识别跳转
        ) {
            if (isset($_COOKIE['is_mobile'])) {
                // 表示来自切换,不跳转
                $is_mobile = false;
            } else {
                if ($this->_is_mobile()) {
                    // 这是移动端
                    if (isset($client[DOMAIN_NAME])) {
                        // 表示这个域名属于电脑端,需要跳转到移动端
                        if (IS_DEV) {
                            $this->_admin_msg(1, mys_lang('当前终端属于移动端，正在自动跳转到移动端网页'), mys_http_prefix($client[DOMAIN_NAME].'/'));
                        } else {
                            mys_domain_301(mys_http_prefix($client[DOMAIN_NAME].'/'));
                        }
                    }
                } else {
                    // 这是电脑端
                    if (in_array(DOMAIN_NAME, $client)) {
                        // 表示这个域名属于移动端,需要跳转到pc
                        if (IS_DEV) {
                            $this->_admin_msg(1, mys_lang('当前终端属于电脑端，正在自动跳转到电脑端网页'), $this->site_info[SITE_ID]['SITE_URL']);
                        } else {
                            mys_domain_301($this->site_info[SITE_ID]['SITE_URL']);
                        }
                    }
                }
            }
        }

        // 客户端识别
        $this->is_mobile = defined('IS_MOBILE') ? 1 : (IS_ADMIN ? 0 : $this->_is_mobile());

        // 后台域名
        define('ADMIN_URL', mys_http_prefix(DOMAIN_NAME.'/'));

        // 站点共享变量
        define('SITE_URL', $this->site_info[SITE_ID]['SITE_URL']);
        define('SITE_MURL', $this->site_info[SITE_ID]['SITE_MURL']);
        define('SITE_NAME', $this->site_info[SITE_ID]['SITE_NAME']);
        define('SITE_LOGO', $this->site_info[SITE_ID]['SITE_LOGO']);
        define('SITE_IS_MOBILE', $this->site_info[SITE_ID]['SITE_IS_MOBILE']);
        define('SITE_IS_MOBILE_HTML', (int)$this->site_info[SITE_ID]['SITE_IS_MOBILE_HTML']);
        define('SITE_THEME', strlen($this->site_info[SITE_ID]['SITE_THEME']) ? $this->site_info[SITE_ID]['SITE_THEME'] : 'default');
        define('SITE_SEOJOIN', strlen($this->site_info[SITE_ID]['SITE_SEOJOIN']) ? $this->site_info[SITE_ID]['SITE_SEOJOIN'] : '_');
        define('SITE_REWRITE', (int)$this->site_info[SITE_ID]['SITE_REWRITE']);
        define('SITE_TEMPLATE', strlen($this->site_info[SITE_ID]['SITE_TEMPLATE']) ? $this->site_info[SITE_ID]['SITE_TEMPLATE'] : 'default');
        define('SITE_LANGUAGE', strlen($this->site_info[SITE_ID]['SITE_LANGUAGE']) ? $this->site_info[SITE_ID]['SITE_LANGUAGE'] : 'zh-cn');
        define('SITE_TIME_FORMAT', strlen($this->site_info[SITE_ID]['SITE_TIME_FORMAT']) ? $this->site_info[SITE_ID]['SITE_TIME_FORMAT'] : 'Y-m-d H:i:s');

        // 全局URL
        define('ROOT_URL', $this->site_info[1]['SITE_URL']); // 主站URL
        define('LANG_PATH', ROOT_URL.'config/language/'.SITE_LANGUAGE.'/'); // 语言包

        define('THEME_PATH', (SYS_THEME_ROOT ? SITE_URL : ROOT_URL).'static/'); // 系统风格
        define('ROOT_THEME_PATH', ROOT_URL.'static/'); // 系统风格绝对路径

        if (strpos(SITE_THEME, '/') !== false) {
            // 远程资源
            define('HOME_THEME_PATH', SITE_THEME); // 站点风格
            define('MOBILE_THEME_PATH', SITE_THEME); // 移动端站点风格
        } else {
            // 本地资源
            define('HOME_THEME_PATH', (SYS_THEME_ROOT ? SITE_URL : ROOT_URL).'static/'.SITE_THEME.'/'); // 站点风格
            if (!defined('IS_MOBILE') && ($this->_is_mobile() && $this->site_info[SITE_ID]['SITE_AUTO'])) {
                // 当开启自适应移动端，没有绑定域名时
                define('MOBILE_THEME_PATH', SITE_URL.'mobile/static/'.SITE_THEME.'/'); // 移动端站点风格
            } else {
                define('MOBILE_THEME_PATH', SITE_MURL.'static/'.SITE_THEME.'/'); // 移动端站点风格
            }
        }

        // 本地附件上传目录和地址
        if (SYS_ATTACHMENT_PATH
            && (strpos(SYS_ATTACHMENT_PATH, '/') === 0 || strpos(SYS_ATTACHMENT_PATH, ':') !== false)
            && is_dir(SYS_ATTACHMENT_PATH)) {
            // 相对于根目录
            // 附件上传目录
            define('SYS_UPLOAD_PATH', rtrim(SYS_ATTACHMENT_PATH, DIRECTORY_SEPARATOR).'/');
            // 附件访问URL
            define('SYS_UPLOAD_URL', trim(SYS_ATTACHMENT_URL, '/').'/');
        } else {
            // 在当前网站目录
            $path = trim(SYS_ATTACHMENT_PATH ? SYS_ATTACHMENT_PATH : 'uploadfile', '/');
            // 附件上传目录
            define('SYS_UPLOAD_PATH', ROOTPATH.$path.'/');
            // 附件访问URL
            define('SYS_UPLOAD_URL', ROOT_URL.$path.'/');
        }

        // 设置终端模板
        $is_auto_mobile_page = 0;
        if (defined('IS_CLIENT')) {
            // 存在自定义终端
            define('CLIENT_URL', mys_http_prefix($this->get_cache('site', SITE_ID, 'client', IS_CLIENT)) . '/');
            \Soulcms\Service::V()->init(defined('IS_CLIENT_TPL') && IS_CLIENT_TPL ? IS_CLIENT_TPL : IS_CLIENT);
        } elseif (defined('IS_MOBILE') || ($this->_is_mobile() && $this->site_info[SITE_ID]['SITE_AUTO'])) {
            // 移动端模板 // 开启自动识别移动端
            \Soulcms\Service::V()->init('mobile');
            $is_auto_mobile_page = 1;
        } else {
            // 默认情况下pc模板
            define('CLIENT_URL', SITE_URL);
            \Soulcms\Service::V()->init('pc');
        }

        // 用户系统
        $this->member_cache = $this->get_cache('member');
        if (\Soulcms\Service::IS_PC() && isset($this->member_cache['domain'][SITE_ID]['domain'])
            && $this->member_cache['domain'][SITE_ID]['domain']) {
            // 电脑端绑定域名时
            define('MEMBER_URL', mys_http_prefix($this->member_cache['domain'][SITE_ID]['domain'].'/'));
        } elseif (!\Soulcms\Service::IS_PC() && isset($this->member_cache['domain'][SITE_ID]['mobile_domain'])
            && $this->member_cache['domain'][SITE_ID]['mobile_domain']) {
            // 移动端绑定域名时
            define('MEMBER_URL', mys_http_prefix($this->member_cache['domain'][SITE_ID]['mobile_domain'].'/'));
        } else {
            // 默认域名
            define('MEMBER_URL', (\Soulcms\Service::IS_PC() ? SITE_URL : SITE_MURL).(defined('MEMBER_PAGE') && MEMBER_PAGE ? MEMBER_PAGE : 'index.php?s=member'));
        }

        // 加载UCSSO客户端
        /*
        if ($this->member_cache['config']['ucsso'] && is_file(ROOTPATH.'api/ucsso/config.php')) {
            include ROOTPATH.'api/ucsso/client.php';
        }*/

        // 网站常量
        define('SITE_ICP', $this->get_cache('site', SITE_ID, 'config', 'SITE_ICP'));
        define('SITE_TONGJI', $this->get_cache('site', SITE_ID, 'config', 'SITE_TONGJI'));
        define('SITE_COPYRIGHT', $this->get_cache('site', SITE_ID, 'config', 'SITE_COPYRIGHT'));
        define('SITE_TEL', $this->get_cache('site', SITE_ID, 'config', 'SITE_TEL'));
        define('SITE_PHONE', $this->get_cache('site', SITE_ID, 'config', 'SITE_PHONE'));
        define('SITE_QQ', $this->get_cache('site', SITE_ID, 'config', 'SITE_QQ'));
        define('SITE_EMAIL', $this->get_cache('site', SITE_ID, 'config', 'SITE_EMAIL'));
        define('SITE_ADDRESS', $this->get_cache('site', SITE_ID, 'config', 'SITE_ADDRESS'));
        
        // 默认登录时间
        define('SITE_LOGIN_TIME', $this->member_cache['config']['logintime'] ? max(intval($this->member_cache['config']['logintime']), 500) : 36000);
        // 定义交易变量
        define('SITE_SCORE', mys_lang($this->member_cache['pay']['score'] ? $this->member_cache['pay']['score'] : '金币'));
        define('SITE_EXPERIENCE', mys_lang($this->member_cache['pay']['experience'] ? $this->member_cache['pay']['experience'] : '经验'));

        // 验证api提交认证
        if (\Soulcms\Service::L('input')->request('appid')
            && \Soulcms\Service::L('input')->request('appsecret')) {
            define('IS_API_HTTP', 1);
            $this->_api_auth();
            // 获取当前的登录记录
            $auth = \Soulcms\Service::L('input')->request('api_auth_code');
            if ($auth) {
                // 通过接口的post认证
                $this->uid = (int)\Soulcms\Service::L('input')->get('api_auth_uid');
                $this->member = \Soulcms\Service::M('member')->get_member($this->uid);
                // 表示登录成功
                if (!$this->member) {
                    // 不存在的账号
                    $this->_json(0, mys_lang('api_auth_uid 账号不存在'));
                } elseif (md5($this->member['passowrd'].$this->member['salt']) != $auth) {
                    $this->_json(0, mys_lang('登录超时，请重新登录'));
                }
            }
        } else {
            define('IS_API_HTTP', 0);
            $this->uid = (int)\Soulcms\Service::M('member')->member_uid();
            $this->member = \Soulcms\Service::M('member')->get_member($this->uid);
            if (!$this->member) {
                $this->uid = 0;
            }
            // 验证账号cookie的有效性
            if ($this->member && !\Soulcms\Service::M('member')->check_member_cookie($this->member)) {
                $this->uid = 0;
                $this->member = [];
            }
        }

        // 判断网站是否关闭
        if (!IS_ADMIN && $this->site_info[SITE_ID]['SITE_CLOSE'] && (!$this->member || !$this->member['is_admin'])) {
            $this->_msg(0, $this->get_cache('site', SITE_ID, 'config', 'SITE_CLOSE_MSG'));
        }

        if (IS_ADMIN) {
            // 开启session
            $this->session();
            // 后台登录判断
            $this->admin = \Soulcms\Service::M('auth')->is_admin_login($this->member);
            \Soulcms\Service::V()->admin();
            \Soulcms\Service::V()->assign([
                'admin' => $this->admin,
                'is_ajax' => \Soulcms\Service::L('input')->get('is_ajax'),
                'is_mobile' => $this->_is_mobile() ? 1 : 0,
            ]);
            // 权限判断
            $uri = \Soulcms\Service::L('Router')->uri();
            if (!$this->_is_admin_auth($uri)) {
                // 无权限操作
                list($a, $action) = explode('_',\Soulcms\Service::L('Router')->method);
                !$action && $action = $a;
                // 获取操作名称
                switch ($action) {
                    case 'add':
                        $name = mys_lang('【增】');
                        break;
                    case 'edit':
                        $name = mys_lang('【改】');
                        break;
                    case 'del':
                        $name = mys_lang('【删】');
                        break;
                    default:
                        $name = mys_lang('【使用】');
                        break;
                }
                $this->_admin_msg(0, mys_lang('没有%s权限（#%s）', $name, $uri));
            }
        }

        // 判断是否存在授权登录
        if (!IS_ADMIN && $code = \Soulcms\Service::L('input')->get_cookie('admin_login_member')) {
            list($uid, $adminid) = explode('-', $code);
            $uid = (int)$uid;
            if ($this->uid != $uid) {
                $admin = \Soulcms\Service::M('member')->table('member')->get((int)$adminid);
                if ($this->session()->get('admin_login_member_code') == md5($uid.$admin['id'].$admin['password'])) {
                    $this->uid = $uid;
                    $this->member = \Soulcms\Service::M('member')->get_member($this->uid);
                }
            }
        }

        if (IS_MEMBER) {
            // 开启session
            $this->session();
            // 登录状态验证
            if (!$this->member && !in_array(\Soulcms\Service::L('Router')->class, ['register', 'login', 'api', 'pay'])) {
                IS_API_HTTP && $this->_json(0, mys_lang('无法获取到登录用户信息'));
                // 会话超时，请重新登录
                \Soulcms\Service::L('Router')->go_member_login(mys_now_url());
                exit;
            }
            // 判断用户状态
            if ($this->member && !in_array(\Soulcms\Service::L('Router')->class, ['register', 'login', 'api'])) {
                $this->_member_option(0);
            }

            \Soulcms\Service::V()->assign(\Soulcms\Service::L('Seo')->member(\Soulcms\Service::L('cache')->get('menu-member')));
            //\Soulcms\Service::V()->assign([ ]);
        }

        // 用户权限id
        $this->member_authid = $this->member ? $this->member['authid'] : [0];

        // 初始化处理
        \Soulcms\Service::M('member')->init_member($this->member);

        if (!IS_ADMIN && !IS_API) {
            // 判断网站访问权限
            if (!IS_MEMBER && !mys_member_auth($this->member_authid, $this->member_cache['auth_site'][SITE_ID]['home'])) {
                $this->_msg(0, mys_lang('您的用户组无权限访问站点'));
            }
            // 账户被锁定
            if ($this->member && $this->member['is_lock'] && !in_array(\Soulcms\Service::L('Router')->class, ['register', 'login', 'api'])) {
                if (mys_is_app('login') && $this->member['is_lock'] == 2) {
                    // 被插件锁定
                    if (APP_DIR != 'login') {
                        $this->_msg(0, mys_lang('账号被锁定'), mys_url('login/home/index'));
                    }
                } else {
                    $this->_msg(0, mys_lang('账号被锁定'));
                }
            }
        }

        \Soulcms\Service::V()->assign([
            'member' => $this->member,
        ]);

        // 附加程序初始化文件
        if (is_file(MYPATH.'Init.php')) {
            require MYPATH.'Init.php';
        }

        // 插件目录初始化
        APP_DIR && $this->init_file(APP_DIR);

        // 分站id
        !defined('SITE_FID') && define('SITE_FID', 0);

        // 挂钩点 程序初始化之后
        \Soulcms\Hooks::trigger('cms_init');
    }

    /**
     * 插件目录初始化文件
     */
    public function init_file($namespace) {

        if (!$namespace) {
            return;
        }

        $file = mys_get_app_dir($namespace).'Config/Init.php';
        if (in_array($file, $this->load_init)) {
            return;
        }

        if (is_file($file)) {
            $this->load_init[] = $file;
            require_once $file;
        }
    }

    /**
     * 开启session
     */
    public function session() {

        if ($this->session) {
            return $this->session;
        }

        $this->session = \Config\Services::session();
        $this->session->start();

        return $this->session;
    }

    /**
     * 缓存页面
     */
    protected function cachePage(int $time) {
        if (isset($this->site_info[SITE_ID]['SITE_CLOSE']) && $this->site_info[SITE_ID]['SITE_CLOSE']) {
            // 网站关闭状态时不进行缓存页面
            return;
        } elseif (!$this->site_info[SITE_ID]['SITE_MOBILE'] && $this->site_info[SITE_ID]['SITE_AUTO']) {
            // 没有绑定移动端域名，开启了自动识别，不进行缓存
            return;
        }
        parent::cachePage($time);
    }

    /**
     * 读取缓存
     */
    public function get_cache(...$params) {
        return \Soulcms\Service::L('cache')->get(...$params);
    }

    // 初始化模块
    public function _module_init($dirname = '', $siteid = SITE_ID) {

        !$dirname && $dirname = APP_DIR;

        if ($this->is_module_init == $dirname.'-'.$siteid) {
            // 防止模块重复初始化
            return;
        }

        $this->is_module_init = $dirname.'-'.$siteid;

        // 判断模块是否安装在站点中
        $cache = \Soulcms\Service::L('cache')->get('module-'.$siteid);
        $this->module = [];
        if ($dirname == 'share' || (isset($cache[$dirname]) && $cache[$dirname])) {
            $this->module = \Soulcms\Service::L('cache')->get('module-'.$siteid.'-'.$dirname);
        }

        // 判断模块是否存在
        if (!$this->module) {
            $dirname == 'share' && IS_ADMIN && $this->_admin_msg(0, mys_lang('系统未安装共享模块，无法使用栏目'));
            IS_ADMIN && $this->_admin_msg(0, mys_lang('模块【%s】不存在', $dirname));
            $this->_msg(0, mys_lang('模块【%s】不存在', $dirname));
            return;
        }

        // 无权限访问模块
        if (!IS_ADMIN && !IS_MEMBER
            && !mys_member_auth($this->member_authid, $this->member_cache['auth_module'][$siteid][$dirname]['home'])) {
            $this->_msg(0, mys_lang('您的用户组无权限访问模块'));
            return;
        }

        // 初始化数据表
        $this->content_model = \Soulcms\Service::M('Content', $dirname);
        $this->content_model->_init($dirname, $siteid);

        // 兼容老版本
        define('MOD_DIR', $dirname);
        define('IS_SHARE', $this->module['share']);
        define('IS_COMMENT', $this->module['comment']);
        define('MODULE_URL', IS_SHARE ? '/' : $this->module['url']); // 共享模块没有模块url
        define('MODULE_NAME', $this->module['name']);

        $this->content_model->is_hcategory = $this->is_hcategory = isset($this->module['config']['hcategory']) && $this->module['config']['hcategory'];

        // 设置模板到模块下
        !IS_SHARE && \Soulcms\Service::V()->module(MOD_DIR);

        // 初始化加载
        $this->init_file(MOD_DIR);
    }

    /**
     * 统一返回json格式并退出程序
     */
    public function _json($code, $msg, $data = []){

        echo mys_array2string(mys_return_data($code, $msg, $data));exit;
    }

    /**
     * 统一返回jsonp格式并退出程序
     */
    public function _jsonp($code, $msg, $data = []){

        $callback = mys_safe_replace(\Soulcms\Service::L('input')->get('callback'));
        !$callback && $callback = 'callback';

        if (IS_API_HTTP) {
            echo mys_array2string(mys_return_data($code, $msg, $data));exit;
        } else {
            echo $callback.'('.mys_array2string(mys_return_data($code, $msg, $data)).')';exit;
        }
    }

    /**
     * 加载数组配置文件
     */
    public function _require_array($file) {

        if (!is_file($file)) {
            return [];
        }

        $array = require $file;

        return $array;
    }

    /**
     * 后台提示信息
     */
    public function _admin_msg($code, $msg, $url = '', $time = 3) {

        if (\Soulcms\Service::L('input')->get('callback')) {
            $this->_jsonp($code, $msg);
        } elseif ((\Soulcms\Service::L('input')->get('is_ajax') || IS_API_HTTP || IS_AJAX)) {
            $this->_json($code, $msg);
        }

        $burl = $url ? $url : (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'php?') !== false ? $_SERVER['HTTP_REFERER'] : '');

        if (!$code && $burl && strpos($burl, '&isback=') === false && strpos($burl, 'c=home&m=home') === false) {
            // 存在URL返回提示
            $burl = str_replace(array('&isback=', '&iscode=', $msg), '&', $burl).'&isback='.$msg.'&iscode='.$code;
            // 避免重复定向
            if (urldecode(mys_now_url()) != urldecode($burl)) {
                mys_redirect($burl, 'auto');
            }
        }

        if (!$url) {
            $backurl = $_SERVER['HTTP_REFERER'];
            strpos(mys_now_url(), $backurl) === 0 && $backurl = '';
        } else {
            $backurl = $url;
        }

        // 不存在URL时进入提示页面
        \Soulcms\Service::V()->assign([
            'msg' => $msg,
            'url' => \Soulcms\Service::L('input')->xss_clean($url),
            'time' => $time,
            'mark' => $code,
            'backurl' => \Soulcms\Service::L('input')->xss_clean($backurl),
            'is_msg_page' => 1,
        ]);

        \Soulcms\Service::V()->display('msg.html', 'admin');
        exit;
    }

    /**
     * 前台提示信息
     */
    public function _msg($code, $msg, $url = '', $time = 3) {

        if (\Soulcms\Service::L('input')->get('callback')) {
            $this->_jsonp($code, $msg);
        } elseif ((\Soulcms\Service::L('input')->get('is_ajax') || IS_API_HTTP || IS_AJAX)) {
            $this->_json($code, $msg);
        }

        if (!$url) {
            $backurl = $_SERVER['HTTP_REFERER'];
            (!$backurl || $backurl == mys_now_url() ) && $backurl = SITE_URL;
        } else {
            $backurl = $url;
        }

        \Soulcms\Service::V()->assign([
            'msg' => $msg,
            'url' => $url,
            'time' => $time,
            'mark' => $code,
            'code' => $code,
            'backurl' => $backurl,
            'meta_title' => SITE_NAME
        ]);
        \Soulcms\Service::V()->display('msg.html');
        !defined('SC_HTML_FILE') && exit();
    }


    /**
     * 附件信息
     */
    public function get_attachment($id) {

        $id = (int)$id;
        if (!$id) {
            return null;
        }

        $data = \Soulcms\Service::L('cache')->init()->get('attach-info-'.$id);
        if ($data) {
            return $data;
        }

        $data = \Soulcms\Service::M()->db->table('attachment')->where('id', $id)->get()->getRowArray();
        if (!$data) {
            return null;
        } elseif ($data['related']) {
            $info = \Soulcms\Service::M()->db->table('attachment_data')->where('id', $id)->get()->getRowArray();
        } else {
            $info = \Soulcms\Service::M()->db->table('attachment_unused')->where('id', $id)->get()->getRowArray();
        }

        if (!$info) {
            if ($data['related']) {
                $info = \Soulcms\Service::M()->db->table('attachment_unused')->where('id', $id)->get()->getRowArray();
            }
            if (!$info) {
                return null;
            }
        }

        // 合并变量
        $info = $data + $info;

        $info['file'] = SYS_UPLOAD_PATH.$info['attachment'];
        // 文件真实地址
        if ($info['remote']) {
            $remote = $this->get_cache('attachment', $info['remote']);
            if (!$remote) {
                // 远程地址无效
                $info['url'] = $info['file'] = '远程附件的配置已经不存在';
                return $info;
            } else {
                $info['file'] = $remote['value']['path'].$info['attachment'];
            }
        }

        // 附件属性信息
        $info['attachinfo'] = mys_string2array($info['attachinfo']);

        $info['url'] = mys_get_file_url($info);

        if (SYS_CACHE && SYS_CACHE_ATTACH) {
            \Soulcms\Service::L('cache')->init()->save('attach-info-'.$id, $info, SYS_CACHE_ATTACH * 3600);
        }

        return $info;
    }

    /**
     * 引用404页面
     */
    protected function goto_404_page($msg) {

        IS_API_HTTP && exit($this->_json(0, $msg));

        // 调试模式下不进行404状态码
        if (!CI_DEBUG) {
            http_response_code(404);
        }

        \Soulcms\Service::V()->assign([
            'msg' => $msg,
            'meta_title' => mys_lang('你访问的页面不存在')
        ]);
        \Soulcms\Service::V()->display('404.html');
        !defined('SC_HTML_FILE') && exit();
    }

    /**
     * 生成静态时的跳转提示
     */
    protected function _html_msg($code, $msg, $url = '') {
        \Soulcms\Service::V()->assign([
            'msg' => $msg,
            'url' => $url,
            'mark' => $code
        ]);
        \Soulcms\Service::V()->display('html_msg.html', 'admin');exit;
    }

    /**
     * 后台登录判断
     */
    protected function _is_admin_login() {
        return \Soulcms\Service::M('auth')->_is_admin_login();
    }

    /**
     * 后台登录判断
     */
    public function _member_option($call = 1)
    {
        // 有用户组来获取最终的强制权限
        $this->member_cache['config']['complete'] = $this->member_cache['config']['mobile'] = $this->member_cache['config']['avatar'] = 0;
        if ($this->member['authid']) {
            foreach ($this->member['authid'] as $aid) {
                // 强制完善资料
                if (!$this->member_cache['config']['complete']) {
                    $this->member_cache['config']['complete'] = (int)$this->member_cache['auth'][$aid]['complete'];
                }
                // 强制手机认证
                if (!$this->member_cache['config']['mobile']) {
                    $this->member_cache['config']['mobile'] = (int)$this->member_cache['auth'][$aid]['mobile'];
                }
                // 强制头像上传
                if (!$this->member_cache['config']['avatar']) {
                    $this->member_cache['config']['avatar'] = (int)$this->member_cache['auth'][$aid]['avatar'];
                }
            }
        }

        (IS_API_HTTP || IS_POST) && $call = 1;

        if (!$this->member['is_verify']) {
            // 审核
            $call && $this->_json(0, mys_lang('账号还没有通审核'));
            mys_redirect(\Soulcms\Service::L('Router')->member_url('api/verify'), 'auto');
        } elseif ($this->member_cache['config']['complete']
            && !$this->member['is_complete']
            &&\Soulcms\Service::L('Router')->class != 'account') {
            // 强制完善资料
            $call && $this->_json(0, mys_lang('账号必须完善资料'));
            mys_redirect(\Soulcms\Service::L('Router')->member_url('account/index'), 'auto');
        } elseif ($this->member_cache['config']['mobile']
            && !$this->member['is_mobile']
            &&\Soulcms\Service::L('Router')->class != 'account') {
            // 强制手机认证
            $call && $this->_json(0, mys_lang('账号必须手机认证'));
            mys_redirect(\Soulcms\Service::L('Router')->member_url('account/mobile'), 'auto');
        } elseif ($this->member_cache['config']['avatar']
            && !$this->member['is_avatar']
            &&\Soulcms\Service::L('Router')->class != 'account') {
            // 强制头像上传
            $call && $this->_json(0, mys_lang('账号必须上传头像'));
            mys_redirect(\Soulcms\Service::L('Router')->member_url('account/avatar'), 'auto');
        }
    }

    /**
     * 获取用户组权限的积分及统计参数累计
     */
    public function _member_auth_value($authid, $name) {

        if (!$authid || !$name) {
            return 0;
        }

        $val = 0;
        foreach ($authid as $aid) {
            isset($this->member_cache['auth'][$aid][$name]) && $this->member_cache['auth'][$aid][$name] && $val+= (int)$this->member_cache['auth'][$aid][$name];
        }

        return $val;

    }

    /**
     * 获取网站用户的积分及统计参数累计
     */
    public function _member_value($authid, $value) {

        if (!$authid || !$value) {
            return 0;
        }

        $val = 0;
        foreach ($authid as $aid) {
            isset($value[$aid]) && $value[$aid] && $val+= (int)$value[$aid];
        }

        return $val;

    }

    /**
     * 获取模块栏目的积分及统计参数累计
     */
    public function _module_member_value($catid, $dir, $auth, $authid = [0]) {

        $value = $this->member_cache['auth_module'][SITE_ID][$dir]['category'][$catid][$auth];
        if (!$value) {
            return 0;
        }

        return $this->_member_value($authid, $value);
    }

    /**
     * 判断模块栏目是否具有用户操作权限
     */
    public function _module_member_category($category, $dir, $auth) {

        if (!$category) {
            return [];
        }

        foreach ($category as $id => $t) {
            // 筛选可发布的栏目权限
            if (!$t['child']) {
                if ($t['mid'] != $dir) {
                    // 模块不符合 排除
                    unset($category[$id]);
                } elseif (!mys_member_auth($this->member_authid, $this->member_cache['auth_module'][SITE_ID][$dir]['category'][$t['id']][$auth])) {
                    // 用户的的权限判断
                    unset($category[$id]);
                }
            }
        }

        return $category;
    }

    /**
     * 判断后台uri是否具有操作权限
     */
    public function _is_admin_auth($uri = '') {
        return \Soulcms\Service::M('auth')->_is_admin_auth($uri);
    }

    /**
     * 是否移动端访问访问
     */
    public function _is_mobile() {

        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
            return true;
        } elseif (isset ($_SERVER['HTTP_USER_AGENT'])) {
            // 判断手机发送的客户端标志,兼容性有待提高
            $clientkeywords = [
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            ];
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Api认证匹配
     */
    protected function _api_auth() {

        $appid = (int)\Soulcms\Service::L('input')->request('appid');
        $appsecret = (string)\Soulcms\Service::L('input')->request('appsecret');

        // 格式验证
        if (!mys_is_app('httpapi')) {
            $this->_json(0, '没有安装[API接口]应用');
        } elseif (!$appid || !$appsecret) {
            $this->_json(0, 'AppID和AppSecret值为空');
        } elseif (strtoupper($this->get_cache('api_auth', $appid)) != strtoupper($appsecret)) {
            $this->_json(0, 'AppID和AppSecret值不匹配');
        }

    }

    /**
     * 插件的clink值
     */
    protected function _app_clink()
    {
        $data = [];
        if (is_file(APPPATH.'Config/Clink.php')) {
            $data = require APPPATH.'Config/Clink.php';
        }
        $local = mys_dir_map(APPSPATH, 1);
        foreach ($local as $dir) {
            if (is_file(APPSPATH.$dir.'/install.lock') && is_file(APPSPATH.$dir.'/Config/Clink.php')) {
                $_clink = require APPSPATH.ucfirst($dir).'/Config/Clink.php';
                if ($_clink) {
                    // 需要判断此模块是否得到插件的授权
                    $data = $data + $_clink;
                }
            }
        }

        return $data;
    }

    /**
     * 插件的cbottom值
     */
    protected function _app_cbottom()
    {
        $data = [];
        if (is_file(APPPATH.'Config/Cbottom.php')) {
            $data = require APPPATH.'Config/Cbottom.php';
        }
        $local = mys_dir_map(mys_get_app_list(), 1);
        foreach ($local as $dir) {
            $path = mys_get_app_dir($dir);
            if (is_file($path.'install.lock') && is_file($path.'Config/Cbottom.php')) {
                $_clink = require $path.'Config/Cbottom.php';
                if ($_clink) {
                    $data = $data + $_clink;
                }
            }
        }

        return $data;
    }

    /**
     * 获取可用后table区域
     */
    protected function _main_table()
    {
        // 默认的
        $data = [
            'couts' => '数据统计',
            'notice' => '通知提醒',
            'share_category' => '共享栏目',
        ];

        if (is_file(MYPATH.'/Config/Main.php')) {
            $_data = require MYPATH.'/Config/Main.php';
            $_data && $data = mys_array22array($data, $_data);
        }

        // 执行插件自己的缓存程序
        $local = mys_dir_map(mys_get_app_list(), 1);
        foreach ($local as $dir) {
            $path = mys_get_app_dir($dir);
            if (is_file($path.'install.lock')
                && is_file($path.'Config/Main.php')) {
                $_data = require $path.'Config/Main.php';
                $_data && $cache = mys_array22array($data, $_data);
            }
        }

        return $data;
    }

    /**
     * 官方短信接口查询
     */
    protected function _api_sms_info() {

        $uid = (int)\Soulcms\Service::L('input')->get('uid');
        $key = mys_safe_replace(\Soulcms\Service::L('input')->get('key'));
        if (!$uid || !$key) {
            $this->_json(0, mys_lang('uid或者key不能为空'));
        }

        $url = "";
        $data = mys_catcher_data($url);

        $this->_json(1, $data);
    }

    // 版本检查
    protected function _api_version_cmf() {
        $json = mys_catcher_data('');
        exit($json);
    }

    // 版本检查
    protected function _api_version_cms() {

        if (is_file(MYPATH.'Config/Version.php')) {
            $v = require MYPATH.'Config/Version.php';
            $json = mys_catcher_data('');
            exit($json);
        }

        $this->_json(0, '');
    }

    // 搜索帮助
    protected function _api_search_help() {

        $kw = mys_safe_replace(\Soulcms\Service::L('input')->get('kw'));
        $url = '';
        \Soulcms\Service::V()->assign([
            'url' => $url,
        ]);
        \Soulcms\Service::V()->display('cloud_online.html');
    }

    /**
     * Get the CI singleton
     */
    public static function &get_instance()
    {
        return self::$instance;
    }

}