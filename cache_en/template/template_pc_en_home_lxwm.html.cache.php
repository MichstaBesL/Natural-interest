<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $meta_title; ?></title>
</head>
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/style.css">
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/swiper-bundle.min.css">
<script src="<?php echo THEME_PATH; ?>skin/js/jquery.min.js"></script>
<script src="<?php echo THEME_PATH; ?>skin/js/swiper-bundle.min.js"></script>
<script src="<?php echo THEME_PATH; ?>skin/js/style.js"></script>
<style>
    .jgjoirlewt45{
        width: 100%;
    }
    .gjgeotjo34 table tr td{
        font-size: 14px;
    }
    .gjgeotjo34 input{
        width: 90%;
        line-height: 35px;
        outline: none;
        border-radius: 0;
        border: 1px solid #000;
    }
    .gjgeotjo34 textarea{
        border-radius: 0;
        border: 1px solid #000;
    }
</style>
<body>

        <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>

    <div class="gjeowueo1d4cy7y9 w85">
        <div class="pagerr1">
            <div class="sgjgioiolw54">
                <div>
                    <?php echo html_entity_decode($cat['content']); ?>
                </div>
            </div>
            <div class="gjiorwtuo45">
                <div>
                    <a target="_blank" href="<?php echo mys_thumb($cat['thumb']); ?>"><img src="<?php echo mys_thumb($cat['thumb']); ?>" alt=""></a>
                </div>
            </div>
        </div>
        <div class="xxx" style="margin: 45px 0;"></div>
        <div class="pagerr2">
            <p class="fsjajggejibh3q324" style="font-size: 18px;">Receive data</p>
            <div class="jgjoirlewt45">
                <div class="gjgeotjo34">
                    
    <!-- 系统关键js(所有自建模板必须引用) -->
    <script type="text/javascript">var assets_path = '<?php echo THEME_PATH; ?>assets/';var is_mobile_cms = '<?php echo \Soulcms\Service::IS_MOBILE(); ?>';</script>
    <script src="<?php echo LANG_PATH; ?>lang.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/layer/layer.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/js/cms.js" type="text/javascript"></script>
    <!-- 系统关键js结束 -->   
                    <?php extract(mys_get_form_post_value('wzly')) ?>
                    <form action="" class="message"  method="post" name="myform" id="myform">
                        <?php echo $form; ?> 
                        <input name="data[title]" type="hidden" value="网站留言"> 
                        <table border="0">
                            <tr>
                                <td valign="top">The name:</td>
                                <td><input name="data[xingming]" type="text"></td>
                            </tr>
                            <tr>
                                <td valign="top">Email:</td>
                                <td><input name="data[chuixiang]" type="text"></td>
                            </tr><tr>
                                <td valign="top">Phone:</td>
                                <td><input name="data[dianhua]" type="text"></td>
                            </tr>
                            <tr>
                                <td valign="top">Data:</td>
                                <td><textarea name="data[ziliao]" id="" cols="30" rows="10"></textarea></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>*Enter your information and you will receive news and spreadsheets from the gallery</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input class="ntbsfe" onclick="mys_ajax_submit('<?php echo $post_url; ?>', 'myform', '2000', '<?php echo $rt_url; ?>')" type="button" value="Confirm"></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div>
                    <a target="_blank" href="<?php echo mys_thumb($cat['lanmutu']); ?>"><img src="<?php echo mys_thumb($cat['lanmutu']); ?>" alt=""></a>
                </div>
            </div>
        </div>
    </div>
    

    <!-- footer -->
    <div class="xxx w85"></div>
    <?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
</body>
</html>