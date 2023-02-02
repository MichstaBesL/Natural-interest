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
.top_title{
    margin: 15px 0 45px 0px;
}
.gwrggt{
    font-size: 2.4rem;
font-weight: 500;
border-bottom: 1px solid #dbdbdb!important;
}

.sggwg{
    line-height: inherit;
}
.nr{
    margin-top: 15px;
}
.pagegwgr2r3 .imgws {
  margin-bottom: 30px;
}
</style>
<body>


    <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
    <div class="ghkgioriog0 w85" style="margin-top: 40px;margin-bottom: 40px;">
        <div class="top_title"><span>Published</span></div>
        <div class="totuowitu79">
            <?php $list_return = $this->list_tag("action=module module=MOD_DIR catid=$catid order=displayorder more=1 num=1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
            <div class="sgjgowr79">
                <div style="display: flex;justify-content: center;align-items: center;">
                    <img src="<?php echo mys_get_file($t['thumb']); ?>" style="width: 60%;" alt="" onclick="javascript:window.location.href='<?php echo $t['url']; ?>'">
                </div>
            </div>
            <div class="lfet4wt4 sagwebv9879">
                <div>
                    <p class="stitlewjfo gwrggt"><?php echo $t['title']; ?>  </p>
                    <div>
                        <?php echo $t['lbynr']; ?>
                    </div>
                    
                    <a target="_blank" class="fkyweuiwg" href="<?php echo $t['url']; ?>">To read more</a>
                </div>
            </div>
            <?php } } ?>
        </div>
        <div style="margin: 80px 0;width: 100%;"></div>
        <div class="pagegwgr2r3">
            <div class="lisgeit1">
                <?php $list_return = $this->list_tag("action=module module=MOD_DIR catid=$catid order=updatetime more=1 page=1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <div class="item sagjoeg_item1">
                    <div class="imgws"><a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a></div>
                    <a href="<?php echo $t['url']; ?>" class="title sggwg" target="_blank"><?php echo $t['title']; ?></a>
                    <p class="nr"><?php echo $t['title']; ?></p>
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
            def:3,
            bj:6
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
        for (let i = 0; i < (bj+def); i++) {
            if(zP_item[i]){
                zP_item[i].style.display="block"
            }
        }
    }
</script>