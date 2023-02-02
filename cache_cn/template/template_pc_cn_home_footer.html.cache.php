<footer class="footer w85" style="position: relative;">
    <div>
        <p><?php echo SITE_COPYRIGHT; ?></p>
        <p><?php echo SITE_ICP; ?></p>
        <?php echo mys_block('footernr'); ?>
    </div>
    <div class="fx">
        <?php $list_return = $this->list_tag("action=module module=nr catid=3 order=displayorder"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
        <a href="<?php echo $t['title']; ?>"><img src="<?php echo mys_get_file($t['thumb']); ?>" alt=""></a>
        <?php } } ?>
    </div>
</footer>

<div class="hd">
    <div class="top backtop"  onclick="$('html,body').stop().animate({scrollTop:0},600)"></div>
    <div class="bottom backbotm"></div>
</div>
<script src="<?php echo THEME_PATH; ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script>
    let sjfioe2 = document.getElementsByClassName("backbotm")[0]
    let body_h = document.body.clientHeight
    sjfioe2.setAttribute("onclick","$('html,body').stop().animate({scrollTop:"+body_h+"},600)")
    // let sjfioe = document.getElementsByClassName("backtop")[0]
    // var timer = null;
    // sjfioe.onclick = function(){
    //     cancelAnimationFrame(timer);
    //     timer = requestAnimationFrame(function fn(){
    //         var oTop = document.body.scrollTop || document.documentElement.scrollTop;
    //         if(oTop > 0){
    //             scrollTo(0,oTop-150);
    //             timer = requestAnimationFrame(fn);
    //         }else{
    //             cancelAnimationFrame(timer);
    //         } 
    //     });
    // }
    // let sjfioe2 = document.getElementsByClassName("backbotm")[0]
    // var timer2 = null;
    // sjfioe2.onclick = function(){
    //     cancelAnimationFrame(timer2);
    //     timer2 = requestAnimationFrame(function fn(){
    //         var oTop = document.body.scrollTop || document.documentElement.scrollTop;
    //         let body_h = document.body.clientHeight
    //         if(oTop < body_h){
    //             scrollTo(body_h,oTop+150);
    //             timer2 = requestAnimationFrame(fn);
    //         }else{
    //             cancelAnimationFrame(timer2);
    //         } 
    //     });
    // }
</script>