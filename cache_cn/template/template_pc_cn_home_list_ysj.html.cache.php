<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $meta_title; ?></title>
</head>
<script src="<?php echo THEME_PATH; ?>skin/js/jquery.min.js"></script>
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/style.css">
<script src="<?php echo THEME_PATH; ?>skin/js/style.js"></script>
<style>
    .page1,.page2{display: block;background: none;}
    .hits {
    margin-bottom: 5px;
    }
    .zl_centerr .top_title {
    margin-top: 0;
    margin-bottom: 45px;
    }
    /* 列表间距固定 */
    /* .list_ggg .itme{
        width: auto;
        margin-right: 90px;
    }
    .list_ggg .itme:nth-child(3n){
        margin-right: 0;
    } */
    .list_ggg .itme>div{
        height: 300px;
        overflow: hidden;
    }
    .list_ggg .itme>div img{
        width: 100%!important;
        height: auto!important;
        min-height: 100%;
    }
    .list_ggg .itme {
    width: 30%;
    margin-right: 5%;
    }
    .list_ggg .itme:nth-child(3n){
        margin-right: 0;
    }
    .list_ggg .itme p{
        font-size: 14px;
    }
</style>
<body>



<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
    <div class="zl_centerr w85">
        <div class="hits" style="height: 20px;">
            <div style="float:right;">
                <a href="#qb" class="active">全部</a>
                <?php $list_return = $this->list_tag("action=category module=share pid=8"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?> 
                <a href="#q<?php echo $key; ?>"><?php echo $t['name']; ?></a>
                <?php } } ?>
            </div>
        </div>
        <div class="page1">
            <span class="sbb" id="qb"></span>
            <div class="top_title"><span>書廊萎淅家</span></div>
            <div class="sf_lists">
                <div class="list_ggg list1">
                    <?php $list_return = $this->list_tag("action=module module=MOD_DIR  catid=$catid  order=updatetime num=10"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                    <div class="list1_item itme">
                        <div><a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a></div>
                        <a href="<?php echo $t['url']; ?>"><p><?php echo $t['title']; ?></p></a>
                    </div>
                    <?php } } ?>
                </div>
            </div>
            <div class="list1_btn ck">
                <span>查看更多</span>
            </div>
        </div>
        <?php $list_return = $this->list_tag("action=category module=share pid=8"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?> 
        <div class="page1">
            <span class="sbb" id="q<?php echo $key; ?>"></span>
            <div class="top_title"><span><?php echo $t['name']; ?></span></div>
            <div class="sf_lists">
                <div class="list_ggg list1">
                    <?php $list_return = $this->list_tag("action=module module=MOD_DIR  catid=$t[id] order=updatetime num=10"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                    <div class="list1_item itme">
                        <div><a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a></div>
                        <a href="<?php echo $t['url']; ?>"><p><?php echo $t['title']; ?></p></a>
                    </div>
                    <?php } } ?>
                </div>
            </div>
            <div class="list1_btn ck">
                <span>查看更多</span>
            </div>
        </div>
        <?php } } ?>
    </div>

    <div class="xxx w85"></div>
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
</body>
</html>
<script>
    let sjfiog = [
        {
            cent_class:"list1",
            cbtn_class:"list1_btn",
            zp_item:"list1_item",
            def:6,
            bj:6
        },
        {
            cent_class:"list2",
            cbtn_class:"list2_btn",
            zp_item:"list2_item",
            def:3,
            bj:3
        },
        {
            cent_class:"list3",
            cbtn_class:"list3_btn",
            zp_item:"list3_item",
            def:3,
            bj:3
        },
        {
            cent_class:"list4",
            cbtn_class:"list4_btn",
            zp_item:"list4_item",
            def:3,
            bj:3
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
            if(zp_item_s[i]){
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