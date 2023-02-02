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
    .page1, .page2{display: block;}
    .page2{background: none;}
    .zl_centerr .top_title {
    margin-top: 50px;
    margin-bottom: 50px;
    }
    .zl_centerr .hits a{margin: 0;}

    /*  */
    /* .list_ggg .itme{
        width: auto;
        margin-right: 80px;
    }
    .list_ggg .itme:nth-child(3n){
        margin-right: 0;
    } */
    .page1 .list_ggg .itme>div{
        height: 300px;
        overflow: hidden;
    }
   .page1 .list_ggg .itme>div img{
        width: 100%!important;
        height: auto!important;
        min-height: 100%;
    }
   .page1 .list_ggg .itme {
    width: 30%;
    margin-right: 5%;
    }
    .page1 .list_ggg .itme:nth-child(3n){
        margin-right: 0;
    }
    .son_nav {
  float: right;
}
.son_nav ul li {
  float: left;
  margin: 0 10px;
  color: #B2B2B2;
}
.son_nav ul .active a {
  color: black;
}
.son_nav ul li a {
  color: #B2B2B2;
}
.hits {
  margin-bottom: 100px;
}
.nfskjgjgewro p{font-size: 14px;margin-bottom:0;line-height: 35px;}
.wwwww .itme > div:nth-child(2)>div{margin-left: 130px;}
.page2 .list_ggg .itme{margin-right: 5%;width: 30%;}
.page2 .list_ggg .itme:nth-child(3n){margin-right: 0;}
.page3 .list_ggg .itme:nth-child(1){margin-right: 0;}
.page3 .list_ggg .itme{margin-right: 4%;}
.page3 .list_ggg .itme:nth-child(2){margin-right: 4%;}
.page3 .list_ggg .itme:nth-child(3){margin-right: 0;}
.page3 .list_ggg .itme:nth-child(2n-1){margin-right: 0;}
</style>
<body>



<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
    <div class="zl_centerr w85">
        <div class="hits" style="height: 20px;">
            <div class="zl_sh_top">
                <p class="title"><?php echo $title; ?></p>
                <div>
                    <span class="nr"><?php echo $ywmc; ?></span>
                    <div class="son_nav">
                        <ul>
                            <li class="active"><a href="#zz">簡介</a></li>
                            <li><a href="#zp">作品</a></li>
                            <li><a href="#xc">展覽</a></li>
                            <li><a href="#sp">視頻</a></li>
                            <li><a href="#wz">文章</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
        <span class="ssb" id="zz"></span>
        <div class="top_dsjfjge" style="margin-top: 50px;">
            <div class="nfskjgjgewro">
                <?php echo $content; ?>
            </div>
            <div class="gjiohhjiwre" style="padding-right:0;padding-left: 2%;">
                <img src="<?php echo mys_get_file($xqyt); ?>" alt="" style="width: 100%;">
                <p style="font-style: oblique;"><?php echo $tupianbiaoti; ?></p>
            </div>
        </div>
        <div class="page1">
            <div class="top_title"><span>作品</span></div>
            <span class="ssb" id="zp"></span>
            <div class="sf_lists">
                <div class="list_ggg list1">
                    <?php if ($zpgl) { ?>
                    <?php $list_return = $this->list_tag("action=module module=zp IN_id=$zpgl more=1 order=displayorder num=12"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                    <div class="list1_item itme">
                        <div><a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a></div>
                        <a href="<?php echo $t['url']; ?>"><p><?php echo $t['title']; ?></p></a>
                    </div>
                    <?php } } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="list1_btn ck">
                <span>查看更多</span>
            </div>
        </div>
        <div class="page2">
            <div class="top_title"><span>展览</span></div>
            <span class="ssb" id="xc"></span>
            <div class="sf_lists">
                <div class="list_ggg list2">
                    <?php if ($zlgl) { ?>
                    <?php $list_return = $this->list_tag("action=module module=news IN_id=$zlgl more=1 order=displayorder num=6"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                    <div class="list2_item itme">
                        <a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a>
                        <a href="<?php echo $t['url']; ?>"><p><?php echo $t['title']; ?></p></a>
                    </div>
                    <?php } } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="list2_btn ck">
                <span>查看更多</span>
            </div>
        </div>
        <div class="page3">
            <div class="top_title"><span>视频</span></div>
            <span class="ssb" id="sp"></span>
            <div class="sf_lists">
                <div class="list_ggg list3 sp">
                    <?php if ($spgl) { ?>
                    <?php $list_return = $this->list_tag("action=module module=zp IN_id=$spgl catid=7 more=1 order=displayorder num=7"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                    <div class="list3_item itme">
                        <video style="object-fit: cover;" controls poster="src.jpg" src="<?php echo mys_get_file($t['spwj']); ?>"></video>
                        <p class="title"><?php echo $t['title']; ?></p>
                        <p class="nr"><?php echo $t['spms']; ?></p>
                    </div>
                    <?php } } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="list3_btn ck">
                <span>查看更多</span>
            </div>
        </div>
        <div class="page4">
            <div class="top_title"><span>文章</span></div>
            <span class="ssb" id="wz"></span>
            <div class="sf_lists">
                <div class="list_ggg list4 wwwww">
                    
                    <?php if ($wzgl) { ?>
                    <?php $list_return = $this->list_tag("action=module module=tuwen IN_id=$wzgl catid=16 more=1 order=displayorder num=6"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                    <div class="list4_item itme">
                        <div><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></div>
                        <div>
                            <div>
                                <a href="<?php echo $t['url']; ?>"><p class="title"><?php echo $t['title']; ?></p></a>
                                <p class="nr"><?php echo $t['description']; ?></p>
                                <br>
                                <a class="gdsdf" href="<?php echo $t['url']; ?>">阅读更多</a>
                            </div>
                        </div>
                    </div>
                    <?php } } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="list4_btn ck">
                <span>查看更多</span>
            </div>
        </div>
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
            def:1,
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
        for (let i = 0; i < (bj+def); i++) {
            if(zP_item[i]){
                zP_item[i].style.display="block"
            }
        }
    }
</script>