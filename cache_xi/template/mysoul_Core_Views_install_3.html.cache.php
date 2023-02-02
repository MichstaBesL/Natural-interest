<?php if ($fn_include = $this->_include("install/header.html")) include($fn_include); ?>

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-laptop font-green"></i>
            <span class="caption-subject font-green">安装结果</span>
        </div>
    </div>
    <div class="portlet-body " style="padding: 50px;">
        <div class="scroller" style="height:300px" data-rail-visible="1">


            <?php if ($error) { ?>
            <div class="scroller" style="height:300px" data-rail-visible="1">
                <?php echo nl2br($error); ?>
            </div>
            <?php } else { ?>
            <div class="row">
                <div class="col-md-4 text-right" style="padding: 50px;">
                    <i class="fa fa-check-circle font-green" style="font-size: 80px;"></i>
                </div>
                <div class="col-md-8 text-left font-green" style="padding-top:35px;font-size: 30px;">
                    安装完成
                </div>
            </div>
            <?php } ?>

    </div>
    </div>
    <div class="portlet-title" style="min-height: 18px;">
    </div>
    <div class="portlet-body text-center">
        <?php if ($error) { ?>
        <a href="<?php echo $pre_url; ?>" class="btn btn-success"> 返回上一步 </a>
        <?php } else { ?>
        <a href="admin.php?c=login" class="btn btn-success"> 登录网址后台 </a>
        <?php } ?>
    </div>
</div>
<?php if ($fn_include = $this->_include("install/footer.html")) include($fn_include); ?>