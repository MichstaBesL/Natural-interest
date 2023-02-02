<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $meta_title; ?></title>
</head>
<link rel="stylesheet" href="<?php echo THEME_PATH; ?>skin/css/style.css">
<script src="<?php echo THEME_PATH; ?>skin/js/style.js"></script>

<style>
    .hits{
        margin-bottom: 25px;
    }
    .page input{
        border: 1px solid #000;
        border-radius: 0;
    }
    .zl_centerr .top_title {
        margin: 50px 0 37px 0;
}
.swiper-slide img {
  height: 550px;
}
</style>
<body>
<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
    <div class="zl_centerr w85">
        <div class="hits" style="height: 26.6px;margin-bottom: 0;">
            <div style="float:right;">
                <?php $list_return = $this->list_tag("action=category module=share id=1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <a href="<?php echo $t['url']; ?>" <?php if ($t['id']==$cat['id']) { ?>class="active"<?php } ?>><?php echo $t['name']; ?></a>
                <?php } } ?>
                <?php if ($cat['child']) { ?>
                <?php $list_return = $this->list_tag("action=category module=share pid=$catid"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a>
                <?php } } ?>
                <?php } else {  if ($cat['pid']!=0) { ?>
                <?php $list_return = $this->list_tag("action=category module=share pid=$cat[pid]"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <a href="<?php echo $t['url']; ?>"<?php if (IS_SHARE && in_array($catid, $t['catids'])) { ?>class='active'<?php } ?>><?php echo $t['name']; ?></a>
                <?php } } ?>  
                <?php }  } ?>
            </div>
        </div>
        <div class="top_title" style="margin-top: 0;"><span>Are on display</span></div>
        <div class="swipter_max w85" style="margin-bottom: 20px;width: 100%;">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <a class="kkk" href="#"><img src="<?php echo mys_thumb($cat['lanmutu']); ?>" alt="">
                        <div class="nr">
                            <p class="title"><?php echo $cat['biaoti']; ?></p>
                            <p>
                                <?php echo $cat['neirong']; ?>
                            </p>
                        </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var swiper = new Swiper(".mySwiper", {
              spaceBetween: 0,
              centeredSlides: true,
              loop:true,
              autoplay: {
                delay: 2500,
                disableOnInteraction: false,
              },
            //   pagination: {
            //     el: ".swiper-pagination",
            //     clickable: true,
            //   },
            });
          </script>
        
        <div class="top_title"><span>Review past</span></div>
        <div class="zl_center">
            <ul class="zl_ul">
                <?php $list_return = $this->list_tag("action=module module=MOD_DIR catid=$catid order=updatetime page=1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <li class="zl_list">
                    <div class="zl_item">
                        <div>
                            <div class="img"><a href="<?php echo $t['url']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a></div>
                            <a href="<?php echo $t['url']; ?>"><p class="title"><?php echo $t['title']; ?></p></a>
                            <div class="nr">
                                <?php echo $t['lbmsnr']; ?>
                            </div>
                        </div>
                    </div>
                </li>
                <?php } } ?>
                

            </ul>
        </div>
        <div style="clear: both;"></div>
        <div class="page">
            <?php echo $pages; ?>
        </div>
    </div>
    <div class="w85 xxx" ></div>
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>
</body>
<script>
window.onload=function(){
        let zl_center = document.getElementsByClassName("zl_center")[0]
        let vdgsf = zl_center.getElementsByClassName("zl_list")
        let zl_center_ww = zl_center.clientWidth
        let sum = 0
        let i =0
        for (let index = 0; index < vdgsf.length; index++) {
            let img = vdgsf[index].getElementsByTagName("img")[0]
            vdgsf[index].setAttribute("style","width:"+img.clientWidth+"px;")
            sum += vdgsf[index].getElementsByTagName("img")[0].clientWidth
            if((index+1)%3==0){
                let namf_w = ((zl_center_ww -sum) / 2)-2
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
        let home_nav = document.getElementsByClassName("topsfd")[0]
        window.onscroll= function(){
            //变量t是滚动条滚动时，距离顶部的距离
            var t = document.documentElement.scrollTop||document.body.scrollTop;
            var scrollup = document.getElementById('scrollup');
            //当滚动到距离顶部200px时，返回顶部的锚点显示
            if(t>=150){
                home_nav.setAttribute("style","box-shadow: #ccc 0 0 3px 1px;")
                // home_nav.style.top="50%";
            }else{          //恢复正
                home_nav.removeAttribute("style")
            }
        }
    }
</script>
</html>