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


/**
 * 附件模型
 */

class Attachment extends \Soulcms\Model {

    private $siteid;

    public function __construct(...$params) {
        parent::__construct(...$params);
        $this->siteid = SITE_ID;
    }

    // 验证用户权限
    public function check($member, $siteid) {
        
        $this->member = $member;
        $this->siteid = $siteid;

        if ($member['is_admin']) {
            return mys_return_data(1); // 管理员不验证
        } elseif (!\Soulcms\Service::C()->_member_auth_value($this->member ? $this->member['authid'] : [0], 'uploadfile')) {
            return mys_return_data(0, mys_lang('您的用户组不允许上传文件'));
        }
        
        return mys_return_data(1);
    }
    
    // 附件归属
    public function handle($uid, $related, $data) {

        if (!SYS_ATTACHMENT_DB) {
            return;
        }

        $uid = intval($uid);
        $member = mys_member_info($uid);

        // 删除文件
        if ($data['del'] && $related && $related != 'rand') {
            $delete = $this->table('attachment')->where_in('id', $data['del'])->getAll();
            if ($delete) {
                foreach ($delete as $row) {
                    if ($related == $row['related']) {
                        $this->file_delete($member, $row['id']);
                    }
                }
            }
        }

        // 新增归档
        if ($data['add']) {
            foreach ($data['add'] as $id) {
                $id = is_numeric($id) ? intval($id) : 0;
                if (!$id) {
                    continue;
                }
                $t = $this->table('attachment_unused')->get($id);
                if ($t) {
                    // 更新主索引表
                    $this->table('attachment')->update($id, array(
                        'uid' => $t['uid'],
                        'author' => $t['author'],
                        'tableid' => 0,
                        'related' => $related
                    ));
                    $this->table('attachment_data')->insert(array(
                        'id' => $t['id'],
                        'uid' => $t['uid'],
                        'remote' => $t['remote'],
                        'author' => $t['author'],
                        'related' => $related,
                        'fileext' => $t['fileext'],
                        'filesize' => $t['filesize'],
                        'filename' => $t['filename'],
                        'inputtime' => $t['inputtime'],
                        'attachment' => $t['attachment'],
                        'attachinfo' => $t['attachinfo'],
                    ));
                    // 删除未使用附件
                    $this->table('attachment_unused')->delete($id);
                }
            }
        }

        
    }

    // 删除内容关联的文件
    public function cid_delete($member, $cid, $related) {

        if (!$cid) {
            return;
        } elseif (!IS_ADMIN && !$member) {
            return;
        }

        $indexs = $this->table('attachment')->where('related', $related.'-'.$cid)->getAll();
        if ($indexs) {
            foreach ($indexs as $i) {
                $this->file_delete($member, intval($i['id']));
            }
        }
    }

    // 删除用户的全部关联的文件
    public function uid_delete($uid) {

        if (!$uid ) {
            return;
        }

        $indexs = $this->table('attachment')->where('uid', $uid)->getAll();
        if ($indexs) {
            foreach ($indexs as $i) {
                $this->file_delete($uid, intval($i['id']));
            }
        }
    }

    // 删除内容关联的文件
    public function id_delete($member, $ids, $related) {

        if (!$member || !$ids) {
            return;
        }

        foreach ($ids as $id) {
            $indexs = $this->table('attachment')->where('related', $related.'-'.$id)->getAll();
            if ($indexs) {
                foreach ($indexs as $i) {
                    $this->file_delete($member, intval($i['id']));
                }
            }
        }

    }
    
    // 删除文件
    public function file_delete($member, $id) {

        if (!$member) {
            return mys_return_data(0, mys_lang('必须登录之后才能删除文件'));
        } elseif (!$id) {
            return mys_return_data(0, mys_lang('文件id不存在'));
        }

        $index = $this->table('attachment')->get($id);
        if (!$index) {
            return mys_return_data(0, mys_lang('文件记录不存在'));
        } elseif ($index['uid'] && $member != $index['uid']) {
            return mys_return_data(0, mys_lang('不能删除他人的文件'));
        }

        $table = $index['related'] ? 'attachment_data' : 'attachment_unused';

        // 获取文件信息
        $info = $this->table($table)->get($id);
        if (!$info) {
            $this->table('attachment')->delete($id);
            return mys_return_data(0, mys_lang('文件数据不存在'));
        }

        $rt = $this->table('attachment')->delete($id);
        if (!$rt['code']) {
            return mys_return_data(0, $rt['msg']);
        }

        // 删除记录
        $this->table($table)->delete($id);

        // 开始删除文件
        $storage = new \Soulcms\Library\Storage(\Soulcms\Service::C());
        $storage->delete($this->get_attach_info($info['remote']), $info['attachment']);

        // 删除缓存
        if (in_array($info['fileext'], ['png', 'jpeg', 'jpg', 'gif'])) {
            list($cache_path) = mys_thumb_path();
            mys_dir_delete($cache_path.md5($id).'/', true);
        }


        return mys_return_data(1, mys_lang('删除成功'));
    }
    
    // 附件归档存储
    public function save_data($data, $related = '') {

        if (!$this->member) {
            $this->member = [
                'id'=> 0,
                'username' => 'guest',
            ];

        }

        // 按uid散列分表
        $tid = (int)substr((string)$this->member['id'], -1, 1);
        $related = $related ? $related : (SYS_ATTACHMENT_DB ? '' : 'rand');

        // 入库索引表
        $rt = $this->table('attachment')->insert([
            'uid' => (int)$this->member['id'],
            'author' => $this->member['username'],
            'siteid' => $this->siteid,
            'related' => $related,
            'tableid' => $tid,
            'download' => 0,
            'filesize' => $data['size'],
            'fileext' => $data['ext'],
            'filemd5' => $data['md5'] ? $data['md5'] : 0,
        ]);
        if (!$rt['code']) {
            // 入库失败
            @unlink($data['path']);
            return $rt;
        }
        $id = $rt['code'];
        if (strpos($related, 'ueditor:') === 0 ? 0 : SYS_ATTACHMENT_DB) {
            // 归档存储
            $rt = $this->table('attachment_unused')->insert([
                'id' => $id,
                'uid' => (int)$this->member['id'],
                'author' => $this->member['username'],
                'siteid' => $this->siteid,
                'remote' => $data['remote'],
                'fileext' => $data['ext'],
                'filename' => $data['name'],
                'filesize' => $data['size'],
                'inputtime' => SYS_TIME,
                'attachment' => $data['file'],
                'attachinfo' => $_SERVER['HTTP_REFERER'],
            ]);
        } else {
            // 随机存储
            $rt = $this->table('attachment_data')->insert([
                'id' => $id,
                'uid' => (int)$this->member['id'],
                'author' => $this->member['username'],
                'related' => $related,
                'filename' => $data['name'],
                'fileext' => $data['ext'],
                'filesize' => $data['size'],
                'attachment' => $data['file'],
                'remote' => $data['remote'],
                'attachinfo' => mys_array2string($data['info']),
                'attachment' => $data['file'],
                'inputtime' => SYS_TIME,
            ]);
        }
        
        // 入库失败 无主键id 返回msg为准
        if ($rt['msg']) {
            // 删除附件索引
            @unlink($data['path']);
            $this->table('attachment')->delete($id);
            return mys_return_data(0, $rt['msg']);
        }

        // 统计附件插件
        if (mys_is_app('mfile')) {
            \Soulcms\Service::M('mfile', 'mfile')->update_member($this->member, $data['size']);
        }

        return mys_return_data($id, 'ok');
    }

    // 附件存储信息
    public function get_attach_info($id = 0) {

        $remote = \Soulcms\Service::C()->get_cache('attachment');
        if ($remote[$id]) {
            return $remote[$id];
        }

        return [
            'id' => 0,
            'url' => SYS_UPLOAD_URL,
            'type' => 0,
            'value' => [
                'path' => SYS_UPLOAD_PATH
            ]
        ];

    }

    // 远程附件缓存
    public function cache($site = SITE_ID) {

        $data = $this->table('attachment_remote')->getAll();
        $cache = [];
        if ($data) {
            foreach ($data as $t) {
                $t['url'] = trim($t['url'], '/').'/';
                $t['value'] = mys_string2array($t['value']);
                $t['value'] = $t['value'][intval($t['type'])];
                $cache[$t['id']] = $t;
            }
        }

        \Soulcms\Service::L('cache')->set_file('attachment', $cache);
    }
}