<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>

<div class="module-content">
    
    <iframe name="contentright" id="module-content-right" src="<?php echo $url; ?>&cache=<?php echo SYS_TIME; ?>" url="<?php echo $url; ?>&cache=<?php echo SYS_TIME; ?>" frameborder="false" scrolling="auto" style="border:none; margin-bottom:0px;" width="100%" height="auto" allowtransparency="true"></iframe>

</div>

<script type="text/javascript">

    function McLink(dir, url) {

        // 延迟提示
        //var admin_loading = layer.load(2, { time: 10000 });
		$('.iframe_menu_a a').removeClass('on');
		$('#iframe_menu_a_'+dir+' a').addClass('on');


        //$('.my-left-content li span').removeClass('selected');
        //$('.my-left-content li').removeClass('active open');

       /// $('.my-left-content2 li span').removeClass('selected');
        //$('.my-left-content2 li').removeClass('active open');

        //$('.left-content-'+dir+'  ').addClass('active open');
       // $('.left-content-'+dir+'  span').addClass('selected');


        $("#module-content-right").attr('src', url);
        $("#module-content-right").attr("url", url);
    }

    function wSize(){
        var str=getWindowSize();
        var strs= new Array(); //定义一数组
        strs=str.toString().split(","); //字符分割
        var heights = strs[0]-30,Body = $('body');
        $('#module-content-right').height(heights);
    }

    var getWindowSize = function(){
        return ["Height","Width"].map(function(name){
            return window["inner"+name] ||
                    document.compatMode === "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ]
        });
    }
    window.onload = function (){
        if(!+"\v1" && !document.querySelector) { // for IE6 IE7
            document.body.onresize = resize;
        } else {
            window.onresize = resize;
        }
        function resize() {
            wSize();
            return false;
        }
    }
    $(function(){
        wSize();
        document.documentElement.style.overflowY = 'hidden'
    });
</script>
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>