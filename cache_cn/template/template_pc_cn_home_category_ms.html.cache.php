<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $meta_title; ?></title>
</head>
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/style.css">
<script src="<?php echo THEME_PATH; ?>skin/js/jquery.min.js"></script>
<script src="<?php echo THEME_PATH; ?>skin/js/style.js"></script>
<style>
    .lefrwt > div{
        padding-right: 50px;
    }
    .hits{
        margin-bottom: 25px;
    }
    .fwgwrt > div{
        width: 80%;
    }
    .top_title{
        margin: 15px 0 35px 0px;
    }
    .lefrwt p{
        font-size: 14px;
        line-height: 35px;
    }
    .fwgwrt a:hover{text-decoration: underline;}
    .fwgwrt p {
  margin-bottom: 0px;
}
.top_title{
    margin-top: 0;
}
</style>
<body>



    <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>

    <div class="centrretrn w85">
        <div class="hits" style="height: 26.6px;margin-bottom: 0;">
            <div style="float:right;">
                <a href="#" class="active">简介</a>
                <?php $list_return = $this->list_tag("action=category module=share pid=$cat[id]"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?> 
                <a href="#y<?php echo $key; ?>"><?php echo $t['name']; ?></a>
                <?php } } ?>
            </div>
        </div>
        <div class="top_title"><span>简介</span></div>
        <div class="page1">
            <span class="ssb" id="ys"></span>
            <div class="lefrwt">
                <div>
                    <?php echo html_entity_decode($cat['content']); ?>
                </div>
            </div>
            <div class="rightjkg">
                <div>
                    <p class="dafhiogw"><?php echo $cat['biaoti']; ?></p>
                    <div><?php echo html_entity_decode($cat['neirong']); ?></div>
                </div>
            </div>
        </div>
        <div class="top_title wrwghrh" style="margin-top: 35px;"><span>所有课程</span></div>
        <?php $list_return = $this->list_tag("action=category module=share pid=11"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?> 
        <?php if (($key+1)%2==0) { ?>
        <div class="page2" style="margin-bottom: 70px;">
            <span class="sbb" id="y<?php echo $key; ?>"></span>
            <div class="aggrg4et">
                <img src="<?php echo mys_thumb($t['thumb']); ?>" alt="">
            </div>
            <div class="fwgwrt">
                <div>
                    <p><img style="width: 250px;" src="<?php echo mys_thumb($t['lanmutu']); ?>" alt=""></p>
                    <p class="ccdf"></p>
                    <div class="nr"><p><?php echo html_entity_decode($t['content']); ?></p></div>
                    <br>
                    <?php $list_return_cc = $this->list_tag("action=module module=news catid=$t[id] order=updatetime num=7  return=cc"); if ($list_return_cc) extract($list_return_cc, EXTR_OVERWRITE); $count_cc=count($return_cc); if (is_array($return_cc)) { foreach ($return_cc as $key_cc=>$cc) {  $is_first=$key_cc==0 ? 1 : 0;$is_last=$count_cc==$key_cc+1 ? 1 : 0; ?>
                    <a href="<?php echo $cc['url']; ?> "><p><?php echo $cc['title']; ?> </p></a>
                    <?php } } ?>
                    <br>
                    <a href="#">查看更多</a>
                </div>
            </div>
            
        </div>
        <?php } else { ?>
        <div class="page2" style="margin-bottom: 70px;">
            <span class="sbb" id="y<?php echo $key; ?>"></span>
            <div class="fwgwrt">
                <div>
                    <p><img style="width: 250px;" src="<?php echo mys_thumb($t['lanmutu']); ?>" alt=""></p>
                    <p class="ccdf"></p>
                    <div class="nr"><p><?php echo html_entity_decode($t['content']); ?></p></div>
                    <br>
                    <?php $list_return_cc = $this->list_tag("action=module module=news catid=$t[id] order=updatetime num=7  return=cc"); if ($list_return_cc) extract($list_return_cc, EXTR_OVERWRITE); $count_cc=count($return_cc); if (is_array($return_cc)) { foreach ($return_cc as $key_cc=>$cc) {  $is_first=$key_cc==0 ? 1 : 0;$is_last=$count_cc==$key_cc+1 ? 1 : 0; ?>
                    <a href="<?php echo $cc['url']; ?> "><p><?php echo $cc['title']; ?> </p></a>
                    <?php } } ?>
                    <br>
                    <a href="#">查看更多</a>
                </div>
            </div>
            <div class="aggrg4et">
                <img src="<?php echo mys_thumb($t['thumb']); ?>" alt="">
            </div>
        </div>
        <?php } ?>
        <?php } } ?>
    </div>

    <!-- footer -->
    <div class="xxx w85"></div>
    <?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
</body>
</html>