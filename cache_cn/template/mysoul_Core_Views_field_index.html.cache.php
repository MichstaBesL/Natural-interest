<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
<div class="note note-danger">
    <p><a href="javascript:mys_update_cache_all();"><?php echo mys_lang('更改数据之后请更新下全站缓存'); ?></a></p>
</div>

<div class="right-card-box">
<form class="form-horizontal" role="form" id="myform">
    <?php echo mys_form_hidden(); ?>
    <div class="table-scrollable">
        <table class="table table-striped table-bordered fc-head-table table-hover table-checkable">
            <thead>
        <tr class="heading">
            <?php if (\Soulcms\Service::C()->_is_admin_auth('del')) { ?>
            <th class="myselect">
                <label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                    <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                    <span></span>
                </label>
            </th>
            <?php } ?>
            <th width="60" style="text-align:center"> <?php echo mys_lang('排序'); ?> </th>
            <th style="text-align: center;font-size: 15px;" width="60">Id</th>
            <th><?php echo mys_lang('字段'); ?></th>
            <th width="90"><?php echo mys_lang('类别'); ?></th>
            <th width="70" style="text-align: center"><?php echo mys_lang('系统'); ?></th>
            <th width="70" style="text-align: center"><?php echo mys_lang('主表'); ?></th>
            <th width="80"  style="text-align: center"><?php echo mys_lang('XSS过滤'); ?></th>
            <th width="70" style="text-align: center"><?php echo mys_lang('前端'); ?></th>
            <th width="70" style="text-align: center"><?php echo mys_lang('可用'); ?></th>
            <th width="100"></th>
        </tr>
        </thead>
        <tbody>
        <?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
        <tr class="odd gradeX" id="mys_row_<?php echo $t['id']; ?>">
            <?php if (\Soulcms\Service::C()->_is_admin_auth('del')) { ?>
            <td class="myselect">
                <label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                    <?php if ($t['issystem']) { ?>
                    <input type="checkbox" class="" disabled name="" value="" />
                    <?php } else { ?>
                    <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $t['id']; ?>" />
                    <?php } ?>
                    <span></span>
                </label>
            </td>
            <?php } ?>
            <td style="text-align:center"> <input type="text" onblur="mys_ajax_save(this.value, '<?php echo mys_url('field/option', ['rname' => $rname, 'rid' => $rid, 'op' => 'save', 'id'=>$t['id']]); ?>', 'displayorder')" value="<?php echo $t['displayorder']; ?>" class="displayorder form-control input-sm input-inline input-mini"> </td>
            <td style="text-align: center;font-size: 15px;"><?php echo $t['id']; ?></td>
            <td> <?php echo $t['spacer'];  echo $t['name']; ?> / <?php echo $t['fieldname']; ?></td>
            <td><?php echo $t['fieldtype']; ?></td>
            <td style="text-align: center;font-size: 15px;"><?php if ($t['issystem']) { ?><i class="fa fa-check-circle"></i><?php } else { ?><i class="fa fa-times-circle"></i><?php } ?></td>
            <td style="text-align: center;font-size: 15px;"><?php if ($t['ismain']) { ?><i class="fa fa-check-circle"></i><?php } else { ?><i class="fa fa-times-circle"></i><?php } ?></td>
            <td style="text-align:center">
                <a href="javascript:;" onclick="mys_ajax_open_close(this, '<?php echo mys_url('field/option', ['rname' => $rname, 'rid' => $rid, 'op' => 'xss', 'id'=>$t['id']]); ?>', 1);" class="badge badge-<?php if ($t['setting']['validate']['xss']) { ?>no<?php } else { ?>yes<?php } ?>"><i class="fa fa-<?php if ($t['setting']['validate']['xss']) { ?>times<?php } else { ?>check<?php } ?>"></i></a>
            </td>
            <td style="text-align:center">
                <a href="javascript:;" onclick="mys_ajax_open_close(this, '<?php echo mys_url('field/option', ['rname' => $rname, 'rid' => $rid, 'op' => 'member','id'=>$t['id']]); ?>', 0);" class="badge badge-<?php if (!$t['ismember']) { ?>no<?php } else { ?>yes<?php } ?>"><i class="fa fa-<?php if (!$t['ismember']) { ?>times<?php } else { ?>check<?php } ?>"></i></a>
            </td>
            <td style="text-align:center">
                <a href="javascript:;" onclick="mys_ajax_open_close(this, '<?php echo mys_url('field/option', ['rname' => $rname, 'rid' => $rid, 'op' => 'disabled','id'=>$t['id']]); ?>', 1);" class="badge badge-<?php if ($t['disabled']) { ?>no<?php } else { ?>yes<?php } ?>"><i class="fa fa-<?php if ($t['disabled']) { ?>times<?php } else { ?>check<?php } ?>"></i></a>
            </td>
            <td>
                <?php if (\Soulcms\Service::C()->_is_admin_auth('edit')) { ?><a href="<?php echo mys_url('field/edit', ['rname' => $rname, 'rid' => $rid, 'id'=>$t['id']]); ?>" class="btn btn-xs green"> <i class="fa fa-edit"></i> <?php echo mys_lang('修改'); ?> </a><?php } ?>
            </td>
        </tr>
        <?php } } ?>
        </tbody>
    </table>
    </div>

    <?php if (\Soulcms\Service::C()->_is_admin_auth('del')) { ?>
    <table class="table table-footer table-checkable ">
        <tbody>
        <tr>
            <td class="myleft myselect" style="padding-left: 7px !important;">
                <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                    <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                    <span></span>
                </label>
                <button type="button" onclick="mys_ajax_option('<?php echo mys_url('field/del', ['rname' => $rname, 'rid' => $rid]); ?>', '<?php echo mys_lang('你确定要删除它们吗？'); ?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> 删除</button>

            </td>
            <td class="myright">
            </td>
        </tr>
        </tbody>
    </table>
    <?php } ?>
</form>
</div>
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>