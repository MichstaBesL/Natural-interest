<?php namespace Soulcms\Controllers\Member;

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



class Recharge extends \Soulcms\Common
{

    /**
     * 在线充值
     */
    public function index() {
        define('FC_PAY', 1);
        $value = max(floatval(\Soulcms\Service::L('input')->get('value')), floatval($this->member_cache['pay']['min']));
        \Soulcms\Service::V()->assign([
            'payfield' => mys_payform('recharge', $value ? $value : '', '', '', 1),
        ]);
        \Soulcms\Service::V()->display('recharge_index.html');
    }

}
