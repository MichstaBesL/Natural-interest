<div class="topsfd">
    <header class="header w85">
    <div class="gjiworg214"><div>
        <?php $list_return = $this->list_tag("action=module module=nr catid=2 order=displayorder more=1"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
        <a href="<?php echo $t['lianjie']; ?>" class="fsdfgw3" data-url="<?php echo $t['lianjie']; ?>"><?php echo $t['title']; ?></a> &nbsp; 
        <?php } } ?>
        <!-- activee -->
        <script>
            let fsdfgw3 = document.getElementsByClassName("fsdfgw3") 
            let url = window.location.href
            for (let i = 0; i < fsdfgw3.length; i++) {
                let htmlflerg = fsdfgw3[i].getAttribute("data-url")
                if(url.search(htmlflerg) != -1){
                    fsdfgw3[i].setAttribute("class","fsdfgw3 activee")
                }
            }
        </script>
        <!-- <a href="#">繁體</a> -->
    </div></div>
        <div class="logo"><img src="<?php echo SITE_LOGO; ?>" alt=""></div>
        <nav class="nav">
            <ul class="nav_list">
                <li class="<?php if ($indexc) { ?> active <?php } ?>"><a href="/">主頁</a></li>
                <?php $list_return = $this->list_tag("action=category module=share pid=0"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?> 
                <li class="<?php if (IS_SHARE && in_array($catid, $t['catids'])) { ?> active<?php } ?>"><a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a></li>
                <?php } } ?>
            </ul>
        </nav>
    </header>
</div>