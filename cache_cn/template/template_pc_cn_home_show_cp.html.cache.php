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
    .centseret .gjioio{
        justify-content: inherit;
    }
    .top_title{
        margin: 53px 0 37px 0px;
    }
    body{height: auto;}
    .agwegtehjhnhes .jz_center4 .zp_item .psdf .imgsfd img {
    width: 100%;
    height: 275px;
    }
    .agwegtehjhnhes .jz_center4 .zp_item:nth-child(1) .psdf .imgsfd img {
        height: auto!important;
}
.agwegtehjhnhes .jz_center4 .zp_item {
  width: 32%;margin-right: 2%;
}
.agwegtehjhnhes .jz_center4 .zp_item:nth-child(3n+1){
  padding-right: 0;
  margin-right:0 ;
}
.agwegtehjhnhes .jz_center4 .zp_item .psdf {
  padding-right: 0;
}
</style>
<body>
<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>


    <div class="zl_show_center w85">
        <span class="ssb"  id="zz"></span>
        <div class="zl_sh_top">
            <p class="title"><?php echo $title; ?></p>
            <div>
                <span class="nr"><?php echo $xqybtms; ?></span>
                <div class="son_nav">
                    <ul>
                        <li class="active"><a href="#zz">概覽</a></li>
                        <li><a href="#zp">作品</a></li>
                        <li><a href="#xc">現場</a></li>
                        <li><a href="#sp">視頻</a></li>
                        <li><a href="#wz">文章</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="centseret">
            <div class="gjioio">
                <div style="width: 90%;">
                    <div>
                        <?php echo $zhuneirong; ?>
                    </div>
                    <div class="xz">
                        <a href="<?php echo mys_get_file($thumb); ?>" download> <svg style="width: 20px;display: inline-block;
                            height: 20px;
                            position: relative;
                            top: 6px;" t="1651819108635" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1978" width="200" height="200"><path d="M498.346667 824.32L201.386667 527.36c-11.946667-11.946667-3.413333-34.133333 13.653333-34.133333H375.466667c11.946667 0 20.48-8.533333 20.48-20.48V54.613333c0-11.946667 8.533333-20.48 20.48-20.48h189.44c11.946667 0 20.48 8.533333 20.48 20.48v418.133334c0 11.946667 8.533333 20.48 20.48 20.48h160.426666c18.773333 0 27.306667 22.186667 13.653334 34.133333L525.653333 824.32c-6.826667 6.826667-20.48 6.826667-27.306666 0zM916.48 989.866667H107.52c-18.773333 0-35.84-15.36-35.84-35.84 0-18.773333 15.36-35.84 35.84-35.84h810.666667c18.773333 0 35.84 15.36 35.84 35.84-1.706667 20.48-17.066667 35.84-37.546667 35.84z" fill="#8a8a8a" p-id="1979"></path></svg> 下載新聞稿</a>
                        <p><a href="<?php echo mys_get_file($thumb); ?>" download><?php echo $xzywbt; ?></a></p>
                    </div>
                </div>
            </div>
            <div class="right_imgs">
                <img src="<?php echo mys_get_file($neirongyetu); ?>" alt="">
                <div class="text_c wgwrrhhh">
                    <p><?php echo $nrytbt; ?></p>
                </div>
            </div>
        </div>
        <div class="fgoshgoizp">
            <span class="ssb" id="zp"></span>
            <div class="top_title" style="margin-top: 37px;"><span>作品</span></div>
            <div class="zp_list jz_center1">
                <?php $list_return = $this->list_tag("action=module module=zp IN_id=$zpgl order=displayorder"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <div class="zp_item">
                    <div class="psdf">
                        <div class="imgsfd"><a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a></div>
                        <div class="text_c">
                            <a href="<?php echo $t['url']; ?>"><p><?php echo $t['title']; ?></p></a>
                        </div>
                    </div>
                </div>
                <?php } } ?>
            </div>
            <div class="jz1_center_btn1 ck">
                <span>查看更多</span>
            </div>
        </div>
        
        <div class="fgoshgoizp">
            <span class="ssb"  id="xc"></span>
            <div class="top_title"><span>現場</span></div>
            <div class="zp_list jz_center2">
                <?php if (is_array($xctj)) { $count=count($xctj);foreach ($xctj as $i=>$c) { ?>
                <div class="zp_item">
                    <div class="psdf">
                        <div class="imgsfd"><img src="<?php echo mys_get_file($c['file']); ?>" alt=""></div>
                        <!-- <div class="text_c">
                            <p>朱達誠 《美學沉思1》，焦墨紙本，100x192cm，2017</p>
                        </div> -->
                    </div>
                </div>
                <?php } } ?>
            </div>
            <div class="jz1_center_btn2 ck">
                <span>查看更多</span>
            </div>
        </div>

        <!-- 視頻 -->
        <div class="agwegtehjhnhes">
            <span class="ssb" id="sp"></span>
            <div class="top_title"><span>視頻</span></div>
            <div class="zp_list jz_center3">
                <?php if ($spgl) { ?>
                <?php $list_return = $this->list_tag("action=module module=zp IN_id=$spgl more=1 order=displayorder"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <div class="zp_item">
                    <div class="psdf">
                        <div class="imgsfd">
                            <video id="video" poster="<?php echo mys_get_file($t['thumb']); ?>" controls src="<?php echo mys_get_file($t['spwj']); ?>"></video>
                        </div>
                        <div>
                            <p class="title"><?php echo $t['title']; ?></p>
                            <p class="nr"> <?php echo $t['spms']; ?></p>
                        </div>
                    </div>
                </div>
                <?php } } ?>
                <?php } ?>
                
            </div>
            <div class="jz1_center_btn3 ck" style="margin-top: 10px;">
                <span>查看更多</span>
            </div>
        </div>


        <!-- 文章 -->
        <div class="agwegtehjhnhes" style="margin-bottom: 70px;">
            <span class="ssb" id="wz"></span>
            <div class="top_title"><span>文章</span></div>
            <div class="zp_list jz_center4">
                <?php if ($wzgl) { ?>
                <?php $list_return = $this->list_tag("action=module module=tuwen IN_id=$wzgl more=1 order=displayorder"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <div class="zp_item">
                    <div class="psdf">
                        <div class="imgsfd">
                            <a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a>
                        </div>
                        <div class="gjrgoigr">
                            <div style="margin-left: 70px;width: 100%;padding-right: 3rem;">
                                <a href="<?php echo $t['url']; ?>"><p class="title"><?php echo $t['title']; ?></p></a>
                                <p class="nr"> <?php echo $t['description']; ?></p>
                                <p style="margin-top: 40px;"><a href="<?php echo $t['url']; ?>" class="gd">閱讀更多</a></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } } ?>
                <?php } ?>
            </div>
            <div class="jz1_center_btn4 ck" style="margin-top: 0px;">
                <span>查看更多</span>
            </div>

<!-- 相关艺术家 -->
            <div class="gjiohgjklgmre">
                <div class="otp_titl" style="font-size: 1.3rem;line-height: 45px;
                border-bottom: 1px solid #4e4e4e;
                margin-bottom: 25px;"><p>相關藝術家</p></div>
                <div class="lisfeg">
                    <?php $list_return = $this->list_tag("action=module module=tuwen catid=17 order=updatetime num=5"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                    <div class="item">
                        <div><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></div>
                        <div><p><?php echo $t['title']; ?></p></div>
                    </div>
                    <?php } } ?>
                </div>
            </div>
        </div>
<style>
    .lisfeg{display:flex;flex-flow: wrap;}
    .lisfeg .item{width: 20.5%;display: flex;align-items: center;font-weight: 600;font-size: 1.3rem;margin-right: 2%;}
    .lisfeg .item:last-child{margin-right: 0;width: auto;}
    .lisfeg .item img{width: 60px;height: 60px;}
    .lisfeg .item div:first-child{margin-right: 20px;}
</style>

  <div class="xxx w85"></div>
    <?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
    
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
        let sjfiog = [
            {
                cent_class:"jz_center1",
                cbtn_class:"jz1_center_btn1",
                zp_item:"zp_item",
                def:3,
                bj:6
            },
            {
                cent_class:"jz_center2",
                cbtn_class:"jz1_center_btn2",
                zp_item:"zp_item",
                def:2,
                bj:4
            },
            {
                cent_class:"jz_center3",
                cbtn_class:"jz1_center_btn3",
                zp_item:"zp_item",
                def:3,
                bj:2
            },
            {
                cent_class:"jz_center4",
                cbtn_class:"jz1_center_btn4",
                zp_item:"zp_item",
                def:4,
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
            cbtn_class_obj.setAttribute("onclick","shows('"+cent_class+"','"+parseInt(def)+"','"+parseInt(bj)+"','"+zp_item+"')")
            
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
    
</html>