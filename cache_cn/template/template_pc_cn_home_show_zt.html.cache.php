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
.nr{color: black;}
.zl_centerr .hits .active {
  color: #000;
}
.nr {
  color: #AEACAA;
  font-size: 1.1rem;
  font-weight: 200;
  line-height: 2.5rem;
  letter-spacing: 1.34px;
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
.zl_sh_top .nr {
  color: #AEACAA;
  font-size: 1.1rem;
  font-weight: 200;
  line-height: 2.5rem;
  letter-spacing: 1.34px;
}
.dffegwet .nr p {
  margin-bottom: 25px;line-height: 35px;font-size: 14px;margin-bottom: 25px;
}
.qtdfjsl ul li {
  overflow: hidden;
}
.imgsdfweg{height: 500px;
overflow: hidden;}
.qtdfjsl ul li img {
  min-height: 500px;
  height: auto;
  width: 100%;
}
.zl_centerr .hits a{margin: 0;}
.pagesfe1 .nr p{color: black;}
.son_nav ul li:last-child{margin-right: 0;}
</style>
<body>



    <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
    <div id="jj" class="zl_centerr w85">
        <div class="hits">
            <div class="zl_sh_top">
                <p class="title"><?php echo $title; ?></p>
                <div>
                    <span class="nr"><?php echo $ywbt; ?></span>
                    <div class="son_nav">
                        <ul>
                            <li class="active"><a href="#jj">简介</a></li>
                            <li><a href="#xy">序言</a></li>
                            <li><a href="#zl">展览</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
        <div class="dffegwet" style="background: none;">
            <div class="pagesfe1" style="height: 420px;">
                <div style="width: 60%;">
                    <div style="width: 90%;margin: 0 auto;margin-left: 0;">
                        <!-- <p class="title" style="font-size: 22px;">自2020年初以来,新冠肺炎蔓延全球。 </p> -->
                        <div class="nr">
                            <?php echo $content; ?>
                        </div>
                    </div>
                </div>
                <div class="sgjweogw12" style="width: 40%;display: block;padding-bottom: 40px;">
                    <div class="gjkgijwjiwgjr" style="display: flex;align-items: center;overflow: hidden;height: 100%;">
                        <img src="<?php echo mys_get_file($thumb); ?>" alt="">
                    </div>
                    <p style="font-style: oblique;margin-top: 10px;text-align: center;">
                        <?php echo $nrtxbt; ?></p>
                </div>
            </div>
        </div>
        <div class="top_title" style="margin: 40px 0;"><span>序言</span></div>
        <span class="sbb" id="xy"></span>
        <div class="sgnwgogjiog">
            <img src="<?php echo mys_get_file($xuyantu); ?>" alt="">
            <div style="display: flex;margin-top: 25px;">
                <div style="width: 48%;margin-right: 4%;">
                    <?php echo $xyznr; ?>
                </div>
                <div style="width: 48%;">
                    <?php echo $xyynr; ?>
                </div>

                
            </div>
        </div>
        <div class="top_title" style="margin: 40px 0;margin-top: 50px;"><span>「抗疫」腺上展</span></div>
        <span class="sbb" id="zl"></span>
        <div class="qtdfjsl">
            <ul class="zl_center123123">
                <?php $list_return = $this->list_tag("action=module module=tuwen catid=24 order=displayorder num=24"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <li class="zl_list12313"><a href="<?php echo $t['url']; ?>">
                    <div class="imgsdfweg"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></div>
                    <div>
                        <p class="title"><?php echo $t['title']; ?></p>
                        <p class="nr"><?php echo $t['description']; ?></p>
                    </div>
                </a></li>
                <?php } } ?>
            </ul>
        </div>
        <div class="r123center_btn3 ck">
            <span>查看更多</span>
        </div>
    </div>

    <div class="xxx w85"></div>
    <?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
</body>
</html>
<!-- <script>
    window.onload=function(){
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
    }
</script> -->
<script>
    let sjfiog = [
        {
            cent_class:"zl_center123123",
            cbtn_class:"r123center_btn3",
            zp_item:"zl_list12313",
            def:9,
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

    // window.onload=function(){
    //     let zl_center = document.getElementsByClassName("qtdfjsl")[0]
    //     let vdgsf = zl_center.getElementsByClassName("zl_list12313")
    //     let zl_center_ww = zl_center.clientWidth
    //     let sum = 0
    //     let i =0
    //     for (let index = 0; index < vdgsf.length; index++) {
    //         let img = vdgsf[index].getElementsByTagName("img")[0]
    //         vdgsf[index].setAttribute("style","width:"+img.clientWidth+"px;")
    //         sum += vdgsf[index].getElementsByTagName("img")[0].clientWidth
    //         if((index+1)%3==0){
    //             let namf_w = ((zl_center_ww -sum) / 2)-2
    //             for(var s=0;s<3;s++){
    //                 let styles = vdgsf[i-1].getAttribute("style")
    //                 vdgsf[i-1].setAttribute("style",styles+"margin: 0px "+namf_w+"px;")
    //                 console.log(styles)
    //             }
    //             console.log(namf_w)
    //             sum=0
                
    //         }
    //         i++
    //     }
    // }
</script>