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
   .top_title {
  margin: 15px 0 45px 0px;
}
.gjwegiowger1445{
    line-height: 34px;
}
.gjwoigjre89 > div:nth-child(2) > div {
  padding-left: 100px!important;
}
</style>
<body>
    <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>


    <div class="gwjgsoflekgg97 w85" style="margin-top: 40px;">
        <div class="hits" style="height: 26px;margin-bottom: -19px;">
            <div style="float:right;">
                <a href="#gy" class="active">关于天趣</a>
                <a href="#tq">天趣14年</a>
                <a href="#cb">创办人</a>
            </div>
        </div>
        <div id="gy" class="top_title"><span>关于天趣</span></div>
        <div class="gjgorigoreq314">
            <div><video controls style="width: 100%;object-fit: cover;" poster="src.jpg" src="<?php echo mys_get_file(mys_block('gytqsp')); ?>"></video></div>
            <div class="sjgoigjle">
                <div class="gjsjgogew214" style="height: 171px;">
                    <div>
                        <?php echo html_entity_decode($cat['content']); ?>
                    </div>
                </div>
            </div>
            <div class="jsfgegwg_btn1 ck">
                <span>了解更多</span>
            </div>


            <div class="top_title" style="margin: 40px 0;"><span>天趣14年</span></div>
            <span class="ssb" id="tq"></span>
            <div class="qqquwiojgwoeg">
                <div class="listjetoewr">
                    <ul class="gjwegiowger1445">
                        <?php $list_return = $this->list_tag("action=module module=tuwen catid=30 order=displayorder"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                        <li <?php if ($t['title']==mys_block('nianfen')) { ?>class="actives"<?php } ?>><?php echo $t['title']; ?></li>
                        <?php } } ?>
                    </ul>
                </div>
                <div class="right_cosnet">
                    <div class="sgjjiojfklgwrug  agwewgrgyjytu565">
                        <?php $list_return = $this->list_tag("action=module module=MOD_DIR catid=$catid order=updatetime page=1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                        <div class="items">
                            <p class="titles" style="font-size: 1.5rem;font-weight: 500;"><?php echo $t['title']; ?></p>
                            <div>
                                <?php echo $t['lbmsnr']; ?>
                            </div>
                        </div>
                        <?php } } ?>
                    </div>
                    <div class="asfdgr5345_btn1 ck" style="text-align: left;">
                        <span>展开更多</span>
                    </div>
                </div>
            </div>
            <!-- dfsfwegwegrgwrggwegweg -->
            <div class="gjgowggtekr98">
                <span class="ssb" id="cb"></span>
                <div style="margin: 30px 0;">
                    <div class="top_title"><span>创办人</span></div>
                </div>
                <div class="gjwoigjre89">
                    <div>
                        <img src="<?php echo mys_get_file($cat['lanmutu']); ?>" alt="">
                    </div>
                    <div style="display: flex;align-items: center;">
                        <div class="gjwoit07t6">
                            <?php echo html_entity_decode($cat['neirong']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .gjwoigjre89 > div:nth-child(2) > div p{
            font-size: 14px;
        }
    </style>
    <script>
        let gjsjgogew214 = document.getElementsByClassName("gjsjgogew214")[0]
        let hfehgig = gjsjgogew214.clientHeight
        let def = (hfehgig/2)+10
        gjsjgogew214.setAttribute("style","height:"+def+"px;")
        let jsfgegwg_btn1 = document.getElementsByClassName("jsfgegwg_btn1")[0]
        jsfgegwg_btn1.onclick=function(){
            gjsjgogew214.setAttribute("style","height:"+hfehgig+"px;")
        }

        let agwewgrgyjytu565 = document.getElementsByClassName("agwewgrgyjytu565")[0]
        let itemsfe = agwewgrgyjytu565.getElementsByClassName("items")
        let def_item_num = 6
        for (let i = 0; i < itemsfe.length; i++) {
            itemsfe[i].setAttribute("style","display:none")
            itemsfe[i].setAttribute("data-issh","false")
        }
        for (let i = 0; i < def_item_num; i++) {
            itemsfe[i].setAttribute("style","display:block")
            itemsfe[i].setAttribute("data-issh","true")
        }
        let asfdgr5345_btn1 = document.getElementsByClassName("asfdgr5345_btn1")[0]
        let bjjiogw = 6
        let afjofjew = bjjiogw
        asfdgr5345_btn1.onclick=function(){
            for (let i = 0; i < itemsfe.length; i++) {
                itemsfe[i].getAttribute("data-issh")=="true"?afjofjew++:'';
            }
            for (let i = 0; i < afjofjew; i++) {
                if(itemsfe[i]){
                    itemsfe[i].setAttribute("style","display:block")
                }
            }
        }
    </script>

    <!-- footer -->
    <div class="xxx w85"></div>
    <?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
</body>
</html>
