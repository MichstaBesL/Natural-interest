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
    .stitlewjfo{
        margin-bottom: 15px;
        font-size: 2.4rem;
        font-weight: 500;
    }
.top_title{
    margin: 25px 0 45px 0px;
}
.lfet4wt4 {
  justify-content: inherit;
}
.nr{
    margin-top: 15px;
}
.stitlewjfo{
    color: #2f2f2f;
}
.sggwg {
  line-height: 2.7rem;
}
</style>
<body>



    <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>

    <div class="ghkgioriog0 w85" style="margin-top: 40px;margin-bottom: 40px;">
        <div class="top_title"><span>Information</span></div>
        <div class="totuowitu79">
            <?php $list_return = $this->list_tag("action=module module=MOD_DIR catid=$catid order=updatetime num=0,1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
            <div class="lfet4wt4">
                <div>
                    <a href="<?php echo $t['url']; ?>"><p class="stitlewjfo"><?php echo $t['title']; ?> </p></a>
                    <p class="nr"><?php echo $t['lbmsnr']; ?></p>
                    <a target="_blank" class="fkyweuiwg" href="<?php echo $t['url']; ?>">To read more</a>
                </div>
            </div>
            <div class="sgjgowr79">
                <div>
                    <img src="<?php echo mys_get_file($t['thumb']); ?>" alt="">
                </div>
            </div>
            <?php } } ?>
        </div>
        <div class="xxx w85" style="margin: 50px 0;"></div>
        <div class="pagegwg2">
            <div class="lisgeit1">
                <?php $list_return = $this->list_tag("action=module module=MOD_DIR catid=$catid order=updatetime page=1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <div class="item sagjoeg_item1">
                    <div class="imgws"><a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a></div>
                    <a href="<?php echo $t['url']; ?>" class="title sggwg" target="_blank"><p><?php echo $t['title']; ?></p></a>
                    <p class="nr"><?php echo $t['lbmsnr']; ?></p>
                </div>
                <?php } } ?>
                
            </div>
            <div class="lisgeit1_btn ck">
                <span>To view more</span>
            </div>
        </div>
    </div>

    <!-- footer -->
    <div class="xxx w85"></div>
    <?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
</body>
</html>
<script>
    let sjfiog = [
        {
            cent_class:"lisgeit1",
            cbtn_class:"lisgeit1_btn",
            zp_item:"sagjoeg_item1",
            def:6,
            bj:9
        }
    ]
    for (let i = 0; i < sjfiog.length; i++) {
        let {cent_class,cbtn_class,def,bj,zp_item} = sjfiog[i]
        let cent_class_obj = document.getElementsByClassName(cent_class)[0]
        let cbtn_class_obj = document.getElementsByClassName(cbtn_class)[0]
        let zp_item_s = cent_class_obj.getElementsByClassName(zp_item)
        for (let i = 0; i < zp_item_s.length; i++) {
            zp_item_s[i].style.display="none"
        }
        for (let i = 0; i < def; i++) {
            if (zp_item_s[i]) {
                zp_item_s[i].style.display="block"
            }
        }
        cbtn_class_obj.setAttribute("onclick","shows('"+cent_class+"','"+def+"','"+bj+"','"+zp_item+"')")
        
    }
    function shows(obj,def,bj,zp_item){
        let obj_html = document.getElementsByClassName(obj)[0]
        let zP_item = obj_html.getElementsByClassName(zp_item)
        let son_num = zP_item.length
        for (let i = 0; i < (parseInt(bj)+parseInt(def)); i++) {
            if(zP_item[i]){
                zP_item[i].style.display="block"
            }
        }
    }
</script>