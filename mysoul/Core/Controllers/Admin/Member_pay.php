<?php namespace Soulcms\Controllers\Admin;

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



class Member_pay extends \Soulcms\Common
{
    public function index() {

        if (IS_AJAX_POST) {
            $post = \Soulcms\Service::L('input')->post('data', true);
            $user = \Soulcms\Service::M()->db->table('member')->where('username', $post['username'])->get()->getRowArray();
            if (!$user) {
                $this->_json(0, mys_lang('账号[%s]不存在', $post['username']), ['field' => 'username']);
            } elseif (!$post['value']) {
                $this->_json(0, mys_lang('金额值未填写'), ['field' => 'value']);
            }  elseif (!$post['unit']) {
                $this->_json(0, mys_lang('充值类型未选择'), ['field' => 'unit']);
            }  elseif (!$post['note']) {
                $this->_json(0, mys_lang('备注说明未填写'), ['field' => 'note']);
            }
            if ($post['unit'] == 1) {
                // 金币
                if ($user['score'] + $post['value'] < 0) {
                    $this->_json(0, mys_lang('账号余额不足'), ['field' => 'value']);
                }
                // 付款方的钱
                $rt = \Soulcms\Service::M('member')->add_score($user['id'], $post['value'], $post['note']);
                !$rt['code'] && $this->_json(0, $rt['msg']);
                $this->_json(1, mys_lang('充值%s成功', SITE_SCORE.$post['value']));
            } else {
                if ($user['money'] + $post['value'] < 0) {
                    $this->_json(0, mys_lang('账号余额不足'), ['field' => 'value']);
                }
                // 付款方的钱
                $rt = \Soulcms\Service::M('Pay')->add_money($user['id'], $post['value']);
                !$rt['code'] && $this->_json(0, $rt['msg']);
                // 增加到交易流水
                $rt = \Soulcms\Service::M('Pay')->add_paylog([
                    'uid' => $user['id'],
                    'username' => $user['username'],
                    'touid' => $user['id'],
                    'tousername' => $user['username'],
                    'mid' => 'system',
                    'title' => mys_lang('后台充值'),
                    'value' => $post['value'],
                    'type' => 'system',
                    'status' => 1,
                    'result' => $post['note'],
                    'paytime' => SYS_TIME,
                    'inputtime' => SYS_TIME,
                ]);
                !$rt['code'] && $this->_json(0, $rt['msg']);

                $call = [
                    'uid' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'value' => $post['value'],
                    'url' => \Soulcms\Service::L('Router')->member_url('paylog/show', ['id' => $rt['code']]),
                    'result' => $post['note'],
                ];
                // 通知
                \Soulcms\Service::L('Notice')->send_notice('pay_admin', $call);
                // 钩子
                \Soulcms\Hooks::trigger('pay_admin_after', $call);

                $this->_json(1, mys_lang('充值%s成功', 'RMB'.$post['value']));
            }
        }

        $fdata = \Soulcms\Service::L('Field')->sys_field(['username']);
        $fdata['username']['name'] = mys_lang('充值账号');

        \Soulcms\Service::V()->assign([
            'form' => mys_form_hidden(),
            'menu' => \Soulcms\Service::M('auth')->_admin_menu(
                [
                    '用户充值' => ['member_pay/index', 'fa fa-rmb'],
                    'help' => [ 600 ],
                ]
            ),
            'myfield' => \Soulcms\Service::L('Field')->toform(0, $fdata),
        ]);
        \Soulcms\Service::V()->display('member_pay.html');
    }


}
