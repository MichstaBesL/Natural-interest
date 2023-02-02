<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $meta_title; ?></title>
</head>
<script src="<?php echo THEME_PATH; ?>skin/js/jquery.min.js"></script>
<script src="<?php echo THEME_PATH; ?>skin/js/style.js"></script>
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/style.css">
<style>
    .pagesfe1 ,.pagesfe2{
    display: flex;
    height: 650px;
    overflow: hidden;
    }
    .gjgjioigr23r3 a:last-child{margin-right: .5rem;}
    .title{line-height: 2.7rem;}
</style>
<body>


    <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
    <div class="zl_centerr w85">
            <div class="gjgjioigr23r3" style="text-align: right;">
                <?php $list_return = $this->list_tag("action=category module=share pid=$cat[id]"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?> 
                <a href="#t<?php echo $key; ?>" class="<?php if ($key==0) { ?>active<?php } ?>"><?php echo $t['name']; ?></a>
                <?php } } ?>
            </div>
        <div class="hits" style="height: 20px;">
            <div class="top_title"><span>特别策划</span></div>
        </div>
        <div style="clear: both;"></div>
        <div id="tq" class="dffegwet">
            <?php $list_return = $this->list_tag("action=category module=share pid=$cat[id]"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?> 
            <div class="pagesfe<?php if (($key+1)%2==1) { ?>1<?php } else { ?>2<?php } ?>">
                <div>
                    <div style="width: 60%;margin: 25px auto;padding: 60px 0;">
                        <a href="<?php echo $t['url']; ?>"><img class="title_img" src="<?php echo mys_thumb($t['thumb']); ?>" alt=""></a>
                        <p class="xian"></p>
                        <div class="nr">
                            <?php echo html_entity_decode($t['content']); ?>
                        </div>
                        <div class="lsigwigjejg">
                            <ul>
                                <?php $list_return_cc = $this->list_tag("action=module module=MOD_DIR catid=$t[id] order=updatetime num=7  return=cc"); if ($list_return_cc) extract($list_return_cc, EXTR_OVERWRITE); $count_cc=count($return_cc); if (is_array($return_cc)) { foreach ($return_cc as $key_cc=>$cc) {  $is_first=$key_cc==0 ? 1 : 0;$is_last=$count_cc==$key_cc+1 ? 1 : 0; ?>
                                <li><a href="<?php echo $cc['url']; ?>"><?php echo $cc['title']; ?></a></li>
                                <?php } } ?>

                            </ul>
                        </div>
                        <div class="gjsogggw"  id="dt"><a href="<?php echo $t['url']; ?>">查看全部</a></div>
                    </div>
                </div>
                <div class="sgjweogw12">
                    <a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_thumb($t['lanmutu']); ?>" alt=""></a>
                </div>
            </div>
            <?php } } ?>
        </div>
    </div>

    <div class="xxx w85"></div>
    <?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
</body>
</html>