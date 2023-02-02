<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/style.css">
<script src="<?php echo THEME_PATH; ?>skin/js/jquery.min.js"></script>
<script src="<?php echo THEME_PATH; ?>skin/js/style.js"></script>
<style>
    .xg a:hover{text-decoration: underline;}
    .contentss .title {
  margin-top: 0;
}
.glsffiepger {
  margin-top: 0;
}
.contentss{margin-top: 40px;}
</style>
<body>



    <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
    <div class="gjgwgog w85">
        <h2 class="title" style="font-size: 2.5rem;"><?php echo $title; ?></h2>
        <p class="nr"><?php echo $lbmsnr; ?></p>
        <div class="contentss">
            <div class="rightsff">
                <p class="title">相关文章</p>
                <ul class="xg">
                    <?php $list_return = $this->list_tag("action=module module=MOD_DIR catid=$catid order=updatetime num=5"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                    <li><a href="<?php echo $t['url']; ?>"><?php echo $t['title']; ?></a></li>
                    <?php } } ?>
                </ul>
                <div class="xxx" style="margin-top: 30px;"></div>
                <p class="title">相開展覽</p>
                <ul class="xg">
                    <?php $list_return = $this->list_tag("action=module module=news catid=1 more=1 order=displayorder num=5"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                    <li><a href="<?php echo $t['url']; ?>"><?php echo $t['title']; ?></a></li>
                    <?php } } ?>
                </ul>
            </div>
            <style>
                .glsffiepger p{margin-bottom: 20px;}
            </style>
            <div class="glsffiepger">
                <div style="padding-right: 40px;">
                    <?php echo $zhuneirong; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="xxx"></div>
    <?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
    <div class="hd">
        <div class="top backtop"></div>
        <div class="bottom backbotm"></div>
    </div>
</body>
<script>
     let sjfioe = document.getElementsByClassName("backtop")[0]
    var timer = null;
    sjfioe.onclick = function(){
        cancelAnimationFrame(timer);
        timer = requestAnimationFrame(function fn(){
            var oTop = document.body.scrollTop || document.documentElement.scrollTop;
            if(oTop > 0){
                scrollTo(0,oTop-150);
                timer = requestAnimationFrame(fn);
            }else{
                cancelAnimationFrame(timer);
            } 
        });
    }
    let sjfioe2 = document.getElementsByClassName("backbotm")[0]
    var timer2 = null;
    sjfioe2.onclick = function(){
        cancelAnimationFrame(timer2);
        timer2 = requestAnimationFrame(function fn(){
            var oTop = document.body.scrollTop || document.documentElement.scrollTop;
            let body_h = document.body.clientHeight
            if(oTop < body_h){
                scrollTo(body_h,oTop+150);
                timer2 = requestAnimationFrame(fn);
            }else{
                cancelAnimationFrame(timer2);
            } 
        });
    }
</script>
</html>