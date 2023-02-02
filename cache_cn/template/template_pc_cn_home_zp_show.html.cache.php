<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $meta_title; ?></title>
</head>
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/style.css">
<script src="<?php echo THEME_PATH; ?>skin/js/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/swiper-bundle.min.css"/>
<script src="<?php echo THEME_PATH; ?>skin/js/jquery.min.js"></script>
<script src="<?php echo THEME_PATH; ?>skin/js/style.js"></script>
<style>
    .nr{color: #919191;}
    .swiper-slide{height: 100px;overflow: hidden;}
    .swiper-slide img{min-height: 90px;}
    .colse::before{
        content: "";
        background: url(<?php echo THEME_PATH; ?>skin/images/close.png);
        height: 100%;
        width: 100%;
        display: inline-block;
        background-size: 100%;
    }
    .uptext,.next{display: inline-block;height: 20px;width: 20px;position: fixed;
        top: 50%;margin-top: -10px;font-size: 24px;}
    .uptext img,.next img{width: 100%;}
    .uptext {
  left: 25px;
}
    .next{right: 25px;}
    .colse{
        width: 25px;
        height: 25px;
    }
    .maxsgf .gjgiooigw{
        margin-left: 20px;
    }
</style>
<body style="padding-top:0;">
    <span class="uptext"><a href="<?php echo $prev_page['url']; ?>"><img src="<?php echo THEME_PATH; ?>skin/images/l.png" alt=""></a></span>
    <span class="next"><a href="<?php echo $next_page['url']; ?>"><img src="<?php echo THEME_PATH; ?>skin/images/r.png" alt=""></a></span>



    <div class="maxsgf w85">
        <a href="javascript:history.back(-1)" class="colse"></a>
        <div class="gjgiooigw">
            <div>
                <p class="title"><?php echo $mingcheng; ?></p>
                <div class="nr">
                    <?php echo $jieshao; ?>
                    <p class="jfisogjewg" style="margin-top: 20px;"><?php echo $shifoukeshou; ?></p>
                </div>
            </div>
            <div class="nwekjrwer">
                <?php echo $content; ?>
            </div>
            <div class="fjgow4t">
                <p class="nr swgegwgr">更多图片(3)</p>
                <div style="position:relative">
                    <div class="swiper mySwiper" style="width: 85%;margin-left: 0px;">
                        <div class="swiper-wrapper">
                            <?php if (is_array($tuji)) { $count=count($tuji);foreach ($tuji as $i=>$c) { ?>
                            <div class="swiper-slide"><a href="<?php echo mys_get_file($c['file']); ?>" target="_blank"><img src="<?php echo mys_get_file($c['file']); ?>" alt=""></a></div>
                            <?php } } ?>
                        </div>
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                <script>
                    var swiper = new Swiper(".mySwiper", {
                        slidesPerView: 4,
                        spaceBetween: 30,
                        freeMode: true,
                        navigation: {
                            nextEl: ".swiper-button-next",
                            prevEl: ".swiper-button-prev",
                        },
                    });
                  </script>
            </div>
        </div>
        <div class="imgsdg" style="height: 394px;display: flex;
        align-items: center;
        justify-content: center;">
        <?php $filerwer = array_slice($tuji , 0, 1);  if (is_array($filerwer)) { $count=count($filerwer);foreach ($filerwer as $i=>$c) { ?>
            <a target="_blank" href="<?php echo mys_get_file($c['file']); ?>">
                <img id="ScrollImg" class="imgsgovet" src="<?php echo mys_get_file($c['file']); ?>">
            </a>
        <?php } } ?>
        </div>
    </div>
</html>
<script>
    var img = document.getElementById('ScrollImg')
    function addMouseWheel(obj, fn, preventDefault) {
        //添加绑定
        if (window.navigator.userAgent.toLowerCase().indexOf("firefox") != -1) {
            obj.addEventListener("DOMMouseScroll", fnWheel, false);
        } else {
            obj.onmousewheel = fnWheel;
        }
        function fnWheel(ev) {
            var oEvent = ev || event;
            var bDown = true;//下
            if (oEvent.wheelDelta) {//ie chrome
                console.log(oEvent.wheelDelta)
                bDown = oEvent.wheelDelta > 0 ? false : true;
            } else {//ff

                if (oEvent.detail > 0) {
                    var width = img.clientWidth;
                    // img.setAttribute("style","width:"+width * 1.1+"px")
                    img.style.width = width * 1.1+"px";
                } else if (oEvent.detail < 0) {
                    var width = img.clientWidth;
                    if (width > 400) {
                        img.style.width = width * 0.9+"px";
                    }
                }
                console.log(oEvent.detail)
                bDown = oEvent.detail > 0 ? true : false;
            }
            (typeof fn == "function") && fn(bDown);
            if (preventDefault) {
                oEvent.preventDefault && oEvent.preventDefault();
                return false;
            }
        }
    }
    addMouseWheel(img)
    function zoomsf(){
        var delta = 0;
        if (!event) event = window.event;
        if (event.wheelDelta) {
            delta = event.wheelDelta / 120;
            if (window.opera) delta = -delta;
        } else if (event.detail) {
            delta = -event.detail / 3;
        }
        if (delta > 0) {
            var width = img.width;
            img.width = width * 1.1;
        } else if (delta < 0) {
            var width = img.width;
            if (width > 400) {
                img.width = width * 0.9;
            }
        }
    }
</script>