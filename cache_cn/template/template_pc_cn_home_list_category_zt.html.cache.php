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
    .pagesfe2 > div,.pagesfe1 > div{
        align-items: center;
    }
    .zl_centerr .top_title{
        margin-bottom: 35px;
    }
    .hits{
        margin-bottom: 5px;
    }
    .qtdfjsl ul li .fergyer2{
        overflow: hidden;
        height: 500px;
    }
    .qtdfjsl ul li img{
        width: 100%;
        height: auto!important;
        min-height: 100%!important;
    }
</style>
<body>


    <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
    <div class="zl_centerr w85">
        <div class="hits" style="height: 20px;">
            <div style="float:right;">
                <a href="#ky" class="active">抗疫</a>
                <a href="#tln">她力量</a>
                <a href="#qt">其它</a>
            </div>
        </div>
        <div class="top_title"><span>抗疫</span></div>
        <div style="clear: both;"></div>
        <div class="dffegwet" style="background: none;">
            <?php $list_return = $this->list_tag("action=category module=share id=23"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?> 
            <?php $list_return = $this->list_tag("action=module module=MOD_DIR catid=$t[id] order=displayorder num=1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
            <div id="ky" class="pagesfe2">
                <div style="width: 60%;">
                    <div style="width: 80%;margin: 0 auto;">
                        <a href="<?php echo $t['url']; ?>"><p class="title" style="font-size: 22px;"><?php echo $t['title']; ?></p></a>
                        <div class="nr">
                            <p><?php echo $t['description']; ?></p>
                        </div>
                        <div class="gjsogggw"  ><a href="<?php echo $t['url']; ?>">阅读更多</a></div>
                    </div>
                </div>
                <div class="sgjweogw12" style="width: 40%;">
                    <a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a>
                </div>
            </div>
            <?php } } ?>
            <?php } } ?>
            <div class="top_title" style="margin: 55px 0 40px 0;"><span>她力量</span></div>
            <span class="sbb" id="tln"></span>
            <?php $list_return = $this->list_tag("action=category module=share id=24"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?> 
            <?php $list_return = $this->list_tag("action=module module=MOD_DIR catid=$t[id] order=displayorder num=1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
            <div class="pagesfe1">
                <div style="width: 60%;">
                    <div style="width: 80%;">
                        <a href="<?php echo $t['url']; ?>"><p class="title" style="font-size: 22px;"><?php echo $t['title']; ?></p></a>
                        <div class="nr">
                            <p><?php echo $t['description']; ?></p>
                        </div>
                        <div class="gjsogggw"><a href="<?php echo $t['url']; ?>">查看全部</a></div>
                    </div>
                </div>
                <div class="sgjweogw12" style="width: 40%;">
                    <a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a>
                </div>
            </div>
            <?php } } ?>
            <?php } } ?>
        </div>
        <div class="top_title" style="margin: 40px 0;"><span>其它</span></div>
        <span class="sbb"  id="qt"></span>
        <div class="qtdfjsl">
            <ul class="zl_center123123">
                <?php $list_return = $this->list_tag("action=category module=share id=25"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?> 
                <?php $list_return = $this->list_tag("action=module module=MOD_DIR catid=$t[id] order=displayorder page=1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <li class="zl_list12313"><a href="<?php echo $t['url']; ?>">
                    <div class="fergyer2"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></div>
                    <div>
                        <p class="title"><?php echo $t['title']; ?></p>
                        <p class="nr"><?php echo $t['description']; ?></p>
                    </div>
                </a></li>
                <?php } } ?>
                <?php } } ?>
            </ul>
        </div>
        <div class="r123center_btn3 ck">
            <span>查看更多</span>
        </div>
    </div>
<style>
    .qtdfjsl ul li{overflow: hidden;}
</style>
    <div class="xxx w85"></div>
    <?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
</body>
</html>
<!-- <script>
    let zl_center = document.getElementsByClassName("zl_center123123")[0]
    let vdgsf = zl_center.getElementsByClassName("zl_list12313")
    let zl_center_ww = zl_center.clientWidth
    let sum = 0
    let i =0
    for (let index = 0; index < vdgsf.length; index++) {
        let img = vdgsf[index].getElementsByTagName("img")[0]
        vdgsf[index].setAttribute("style","width:"+img.clientWidth+"px;")
        sum += vdgsf[index].getElementsByTagName("img")[0].clientWidth
        if((index+1)%3==0){
            let namf_w = (zl_center_ww -sum) / 2
            for(var s=0;s<3;s++){
                let styles = vdgsf[i-1].getAttribute("style")
                vdgsf[i-1].setAttribute("style",styles+"margin: 0px "+namf_w+"px;")
                console.log(styles)
            }
            console.log(namf_w)
            sum=0
            
        }
        i++
    }
</script> -->
<script>
    let sjfiog = [
        {
            cent_class:"zl_center123123",
            cbtn_class:"r123center_btn3",
            zp_item:"zl_list12313",
            def:6,
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