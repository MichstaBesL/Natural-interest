<?php namespace Soulcms\Field;


class Image extends \Soulcms\Library\A_Field {

    /**
     * 构造函数
     */
    public function __construct(...$params) {
        parent::__construct(...$params);
        $this->close_xss = 1; // 关闭xss验证
        $this->fieldtype = ['TEXT' => ''];
        $this->defaulttype = 'TEXT';
    }

    /**
     * 字段相关属性参数
     *
     * @param	array	$value	值
     * @return  string
     */
    public function option($option) {



        return ['
      
			<div class="form-group">
                    <label class="col-md-2 control-label">'.mys_lang('文件大小').'</label>
                    <div class="col-md-9">
						<label><input id="field_default_value" type="text" class="form-control" value="'.$option['size'].'" name="data[setting][option][size]"></label>
						<span class="help-block">'.mys_lang('单位MB').'</span>
                    </div>
                </div>
            <div class="form-group">
				<label class="col-md-2 control-label">'.mys_lang('上传数量').'</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" value="'.$option['count'].'" name="data[setting][option][count]"></label>
					<span class="help-block">'.mys_lang('每次最多上传的文件数量').'</span>
				</div>
			</div>
			<div class="form-group hidden">
				<label class="col-md-2 control-label">'.mys_lang('扩展名').'</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="40" name="data[setting][option][ext]" value="jpg,gif,png,jpeg"></label>
				</div>
			</div>'.$this->attachment(isset($option['attachment']) ? $option['attachment'] : 0).'',

            '<div class="form-group">
			<label class="col-md-2 control-label">'.mys_lang('控件宽度').'</label>
			<div class="col-md-9">
				<label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
				<span class="help-block">'.mys_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
			</div>
		</div>'];
    }

    /**
     * 字段输出
     */
    public function output($value) {
        return mys_string2array($value);
    }

    /**
     * 获取附件id
     */
    public function get_attach_id($value) {
        return mys_string2array($value);
    }

    /**
     * 字段入库值
     */
    public function insert_value($field) {

        $data = \Soulcms\Service::L('Field')->post[$field['fieldname']];

        \Soulcms\Service::L('Field')->data[$field['ismain']][$field['fieldname']] = mys_array2string($data);
    }

    /**
     * 附件处理
     */
    public function attach($data, $_data) {

        $data = mys_string2array($data);
        $_data = mys_string2array($_data);

        if (!isset($_data)) {
            $_data = array();
        }
        if (!isset($data)) {
            $data = array();
        }

        // 新旧数据都无附件就跳出
        if (!$data && !$_data) {
            return NULL;
        }

        // 新旧数据都一样时表示没做改变就跳出
        if ($data === $_data) {
            return NULL;
        }

        // 当无新数据且有旧数据表示删除旧附件
        if (!$data && $_data) {
            return array(
                array(),
                $_data
            );
        }

        // 当无旧数据且有新数据表示增加新附件
        if ($data && !$_data) {
            return array(
                $data,
                array()
            );
        }

        // 剩下的情况就是删除旧文件增加新文件

        // 新旧附件的交集，表示固定的
        $intersect = @array_intersect($data, $_data);

        return array(
            @array_diff($data, $intersect), // 固有的与新文件中的差集表示新增的附件
            @array_diff($_data, $intersect), // 固有的与旧文件中的差集表示待删除的附件
        );
    }


    /**
     * 字段表单输入
     *
     * @return  string
     */
    public function input($field, $value = '')
    {

        // 字段禁止修改时就返回显示字符串
        if ($this->_not_edit($field, $value)) {
            return $this->show($field, $value);
        }

        // 字段存储名称
        $name = $field['fieldname'];

        // 字段显示名称
        $text = ($field['setting']['validate']['required'] ? '<span class="required" aria-required="true"> * </span>' : '') . $field['name'];
        // 表单宽度设置
        $width = \Soulcms\Service::_is_mobile() ? '100%' : ($field['setting']['option']['width'] ? $field['setting']['option']['width'] : '100%');

        // 字段提示信息
        $tips = ($name == 'title' && APP_DIR) || $field['setting']['validate']['tips'] ? '<span class="help-block" id="mys_' . $field['fieldname'] . '_tips">' . $field['setting']['validate']['tips'] . '</span>' : '';

        $count = intval($field['setting']['option']['count']);
        $size = intval($field['setting']['option']['size']);

        $p = IS_ADMIN ? mys_authcode([
            'size' => intval($field['setting']['option']['size']),
            'exts' => $field['setting']['option']['ext'],
            'attachment' => $field['setting']['option']['attachment'],
        ], 'ENCODE') : 0;
        $url = '/index.php?s=api&c=file&siteid=' . SITE_ID . '&m=upload&p=' . $p . '&fid=' . $field['id'];

        // 显示模板
        $i = 0;
        $tpl = '';
        if ($value) {
            $value = mys_string2array($value);
            if ($value) {
                foreach ($value as $id) {
                    $file = \Soulcms\Service::C()->get_attachment($id);
                    if ($file) {
                        $tpl.= '<div id="image-'.$name.'-'.$id.'" class="dz-preview dz-processing dz-success dz-complete dz-image-preview">';
                        $tpl.=     '<div class="dz-image">';
                        $tpl.=        ' <img data-dz-thumbnail="" src="'.mys_thumb($id, 110, 110).'">';
                        $tpl.=     '</div>';
                        $tpl.=    ' <div class="dz-details">';
                        $tpl.=    '     <div class="dz-size"><span data-dz-size=""><strong>'.mys_format_file_size($file['filesize']).'</strong></span></div>';
                        $tpl.=     '</div>';
                        $tpl.=     '<a class="dz-remove" href="javascript:mys_delete_image_'.$name.'('.$id.');" title="'.mys_lang('删除图片').'">';
                        $tpl.=      '   <i class="fa fa-times-circle"></i>';
                        $tpl.=    ' </a>';
                        $tpl.=    '<input type="hidden" name="data['.$name.'][]" value="'.$id.'" />';
                        $tpl.= '</div>';
                        $i++;
                    }
                }
            }
        }
        $ucount = $count - $i;

        // 表单输出
        $str = '
			 <div class="dropzone dropzone-file-area" id="my-dropzone-'.$name.'" style="width:'.$width.(is_numeric($width) ? 'px' : '').';">
                     
                            </div>
		';

        if (!defined('POSCMS_FIELD_IMAGES')) {
            $str.= '
				
			<link href="'.ROOT_THEME_PATH.'assets/global/plugins/dropzone/dropzone.min.css" rel="stylesheet" type="text/css" />
			
			<script src="'.ROOT_THEME_PATH.'assets/global/plugins/dropzone/dropzone.min.js" type="text/javascript"></script>
			<script src="'.ROOT_THEME_PATH.'assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
			
			';
            define('POSCMS_FIELD_IMAGES', 1);//防止重复加载JS
        }

        $ext = !$field['setting']['option']['ext'] || $field['setting']['option']['ext'] == '*' ? '' : 'acceptFileTypes: /(\.|\/)('.str_replace(',', '|', $field['setting']['option']['ext']).')$/i,';

        $str.= '
		
<script type="text/javascript">

$(function() {
    $("#my-dropzone-'.$name.'").sortable();
    Dropzone.autoDiscover = false;
    $("#my-dropzone-'.$name.'").dropzone({ 
        addRemoveLinks:true,
        maxFiles:'.$ucount.',//一次性上传的文件数量上限
        maxFilesize: '.$size.', //MB
        acceptedFiles: "image/*",
        dictMaxFilesExceeded: "'.mys_lang("您最多只能上传%s张图片", $count).'",
        dictResponseError: \'文件上传失败!\',
        dictInvalidFileType: "你不能上传该类型文件,文件类型只能是*.jpg,*.gif,*.png。",
        dictFallbackMessage:"浏览器不受支持",
        dictFileTooBig:"文件过大上传文件最大支持.",
        url: "'.$url.'",
        init: function() {
            //res为服务器响应回来的数据
            this.on("success", function(file, res) {
 
                var rt = JSON.parse(res);
                //将json字符串转换成json对象
     
                //res为dropzone.js返回的图片路经
                //file.path = res;
                 
                if(rt.code){
                    var input = \'<input type="hidden" name="data['.$name.'][]" value="\'+rt.id+\'" />\';
                    $(file.previewElement).append(input);
 
                }else{
                    mys_tips(0, rt.msg);
                    file.previewElement.classList.remove("dz-success");
                    file.previewElement.classList.add("dz-error");
                    for (var _iterator7 = file.previewElement.querySelectorAll("[data-dz-errormessage]"), _isArray7 = true, _i7 = 0, _iterator7 = _isArray7 ? _iterator7 : _iterator7[Symbol.iterator]();;) {
                            var _ref6;
                            if (_isArray7) {
                                if (_i7 >= _iterator7.length) break;
                                _ref6 = _iterator7[_i7++];
                            } else {
                                _i7 = _iterator7.next();
                                if (_i7.done) break;
                                _ref6 = _i7.value;
                            }

                            var node = _ref6;
                            node.textContent = rt.msg;
                        }
                }
                 
 
                 
            });
 
        }
     });
    $("#my-dropzone-'.$name.'").append("'.addslashes($tpl).'");
});
function mys_delete_image_'.$name.'(e) {
  $("#image-'.$name.'-"+e).remove();
}
</script>
		
		';


        // 输出最终表单显示
        return $this->input_format($name, $text, $str.$tips);
    }

    /**
     * 字段表单显示
     *
     * @param	string	$field	字段数组
     * @param	array	$value	值
     * @return  string
     */
    public function show($field, $value = null) {

        $html = '';
        $value = mys_string2array($value);
        if ($value) {
            $html.= '<div class="row">';
            foreach ($value as $id) {
                $html.= '<div class="col-sm-3 col-md-2">';
                $html.= '<a href="javascript:mys_preview_image(\''.mys_get_file($id).'\');" class="thumbnail">';
                $html.= '<img src="'.mys_thumb($id, 200, 200).'" style="width:100%">';
                $html.= '</a>';
                $html.= '</div>';
            }
            $html.= '</div>';
        }

        return $this->input_format($field['fieldname'], $field['name'], $html);
    }

}