<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
</head>
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/style.css">
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/swiper-bundle.min.css"/>
<script src="<?php echo THEME_PATH; ?>skin/js/jquery.min.js"></script>
<script src="<?php echo THEME_PATH; ?>skin/js/style.js"></script>
<script src="<?php echo THEME_PATH; ?>skin/js/swiper-bundle.min.js"></script>
<style>
    .swiper-slide img{
        height: 550px;
    }
    .top_title {
        margin: 57px 0 37px 0px;
    }
    .news .news_list .news_item .nrss * {   
        padding-right: 15px;
    }
    #login{font-size: 1.4rem!important;}
    #login {
    height: 380px;
    width: 50%;
    top: 50%;
    left: 50%;
    margin-left: -25%;
    margin-top: -190px;
    }
    #login .cnet {
  width: 70%;
}
</style>
<body>
    <!-- 系统关键js(所有自建模板必须引用) -->
    <script type="text/javascript">var assets_path = '<?php echo THEME_PATH; ?>assets/';var is_mobile_cms = '<?php echo \Soulcms\Service::IS_MOBILE(); ?>';</script>
    <script src="<?php echo LANG_PATH; ?>lang.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/layer/layer.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/js/cms.js" type="text/javascript"></script>
    <!-- 系统关键js结束 -->   
    <div id="login" style="display: none;">
        <div class="cnet">
            <img class="logo" src="<?php echo THEME_PATH; ?>skin/images/sss.png" alt="">
            <p class="ms">歡迎訪問天趣藝術官方網站，請提供您的郵箱用以登陸。</p>
            <?php extract(mys_get_form_post_value('yxtj')) ?>
            <form action="" class="message formsfd"  method="post" name="myform" id="myform">
                <?php echo $form; ?> 
                <input name="data[title]" type="hidden" value="首页邮箱提交">    
                <input class="sr" type="text" name="data[youxiang]" placeholder="請輸入您的郵箱">
                <input class="btn" type="button" value="發送" onclick="mys_ajax_submit('<?php echo $post_url; ?>', 'myform', '2000', '<?php echo $rt_url; ?>')">
                <!-- onclick="tj()"  -->
                <div class="ts">
                    <ul class="ts1234">
                        <li>@163.com</li>
                        <li>@126.com</li>
                        <li>@qq.com</li>
                        <li>@sohu.com</li>
                        <li>@sina.com</li>
                    </ul>
                </div>
            </form>
            <p class="tg">輸入郵箱后您將收到來自天趣藝術的新聞及展訊，或<br>您也可以選擇&nbsp;<a style="cursor: pointer;" onclick="yc()">跳&nbsp;過</a>。</p>
        </div>
    </div>

    <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>

    <div class="swipter_max w85">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php $list_return = $this->list_tag("action=module module=nr catid=1 order=displayorder"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <div class="swiper-slide">
                    <a class="kkk" href="#"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt="">
                    <div class="nr">
                        <p class="title"><?php echo $t['title']; ?></p>
                        <?php echo $t['neirong']; ?>
                    </div>
                    </a>
                </div>
                <?php } } ?>
            </div>
        </div>
    </div>
    <script>
        var swiper = new Swiper(".mySwiper", {
          spaceBetween: 0,
          centeredSlides: true,
          loop:true,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
        //   pagination: {
        //     el: ".swiper-pagination",
        //     clickable: true,
        //   },
        });
      </script>

    <div class="cnsdkf w85">
        <div class="top_title"><span>展覽</span></div>
        <div class="item_list">
            <?php $list_return = $this->list_tag("action=module module=news catid=1 order=displayorder num=3"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
            <div class="item">
                <div>
                    <div class="img"><a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a></div>
                    <a href="<?php echo $t['url']; ?>"><p class="title"><?php echo $t['title']; ?></p></a>
                    <div class="nr">
                        <?php echo $t['lbmsnr']; ?>
                    </div>
                </div>
            </div>
            <?php } } ?>
        </div>
    </div>
    <div class="news w85">
        <div class="top_title news_title"><span>資訊</span></div>
        <div class="news_list">
            <?php $list_return = $this->list_tag("action=module module=news catid=12 order=displayorder more=1 num=3"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
            <div class="news_item">
                <div>
                    <div class="img">
                        <a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a>
                    </div>
                    <div class="nrss">
                        <p class="title"><?php echo mys_strcut($t['title'], 22, '...'); ?></p>
                        <?php echo $t['lbmsnr']; ?>
                    </div>
                </div>
            </div>
            <?php } } ?>
        </div>
    </div>
    <div class="fsgwgbg">
        <div class="w85" style="position: relative;">
            <div class="swiper mySwiper2222">
                <div class="swiper-wrapper">
                    <?php $list_return = $this->list_tag("action=module module=tuwen catid=24 order=displayorder more=1 num=6"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                    <div class="swiper-slide">
                        <div class="fjsgog_list">
                            <div class="gjg_item">
                                <div>
                                    <a href="<?php echo $t['url']; ?>"><p class="title"><?php echo $t['title']; ?></p></a>
                                    <p class="nr"><?php echo $t['description']; ?></p>
                                </div>
                                <div class="img">
                                    <a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } } ?>
                    
                </div>
                
            </div>
            <div class="swiper-button-next"></div>
            <script>
                var swiper = new Swiper(".mySwiper2222", {
                  spaceBetween: 75,
                  slidesPerView: 3,
                  centeredSlides: true,
                  loop:true,
                  autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                  },
                  navigation: {
                    nextEl: ".swiper-button-next",
                  },
                });
              </script>
              <style>
                  .fsgwgbg .fjsgog_list .gjg_item .img img {
                    min-width: 400px;
                    height: 300px;
                    }
              </style>
        </div>
    </div>
    <?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
</body>
</html>