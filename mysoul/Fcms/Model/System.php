<?php namespace Soulcms\Model;

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


// 系统模型 - 后台
class System extends \Soulcms\Model
{
    public $config = [

        'SYS_DEBUG'	=> '调试器开关',
        'SYS_EMAIL' => '系统收件邮箱，用于接收系统信息',
        'SYS_ADMIN_CODE' => '后台登录验证码开关',
        'SYS_ADMIN_LOG' => '后台操作日志开关',
        'SYS_AUTO_FORM' => '自动存储表单数据',
        'SYS_ADMIN_PAGESIZE' => '后台数据分页显示数量',

        'SYS_CAT_RNAME' => '栏目目录允许重复',
        'SYS_PAGE_RNAME' => '自定义页面目录允许重复',

        'SYS_KEY' => '安全密匙',
        'SYS_CSRF'	=> '开启跨站验证',
        'SYS_HTTPS'	=> 'https模式',
        'SYS_ADMIN_LOGINS'	=> '登录失败N次后，系统将锁定登录',
        'SYS_ADMIN_LOGIN_TIME'	=> '登录失败锁定后在x分钟内禁止登录',

        'SYS_ATTACHMENT_DB'	    => '附件归属开启模式',
        'SYS_ATTACHMENT_PATH'	=> '附件上传路径',
        'SYS_ATTACHMENT_URL'	=> '附件访问地址',
        'SYS_BDMAP_API'	=> '百度地图API',
        'SYS_THEME_ROOT'	=> '风格目录引用作用域',

        'SYS_FIELD_THUMB_ATTACH'	=> '缩略图字段存储策略',
        'SYS_FIELD_CONTENT_ATTACH'	=> '内容字段存储策略',

    ];
    
    
    /**
	 * 保存配置文件
	 */
    public function save_config($system, $data) {

        foreach ($this->config as $name => $s) {
            isset($data[$name]) && $system[$name] = $data[$name];
        }

        \Soulcms\Service::L('config')->file(WRITEPATH.'config/system.php', '系统配置文件', 32)->to_require_one(
            $this->config,
            $system
        );
    }

}