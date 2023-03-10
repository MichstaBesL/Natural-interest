
// 加入购物车
function mys_add_cart(fid, id, thumb, url) {
    $.get("/index.php?is_ajax=1&s=store&c=cart&m=add&id="+id+'&fid='+fid+'&thumb='+thumb+'&url='+url, function(data){
        mys_cmf_tips(data.code, data.msg);
        if (data.code) {
            $('#mys_cart_nums').html(data.data);
        }
    }, 'json');
}

// 显示购物车数量
function mys_cart_nums() {
    $.ajax({
        type: "GET",
        url: '/index.php?s=store&c=cart&m=nums',
        dataType: "jsonp",
        success: function (json) {
            if (json.code) {
                $('#mys_cart_nums').html(json.data);
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            //alert(HttpRequest.responseText);
        }
    });
}
// 删除购物车
function mys_cart_del(id) {
    $.ajax({
        type: "GET",
        url: '/index.php?s=store&c=cart&m=del&id='+id,
        dataType: "jsonp",
        success: function (json) {
            mys_cmf_tips(json.code, json.msg);
            if (json.code) {
                $('#mys_row_'+id).remove();
                $('#mys_cart_nums').html(json.data);
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            mys_cmf_tips(0, lang['error']);
        }
    });
}


function mys_mall_update_cart() {
    $.get("/index.php?is_ajax=1&s=store&c=cart&&m=show", function(data){
        if (data.code) {
            $('#mys_cart_show_html').html(data.msg);
        } else {
            $('#mys_cart_show_html').html('');
        }
    }, 'jsonp');
}


function mys_mall_table_more(id) {
    var hi = $('.mys_table_'+id).is(":hidden");
    $('.mys_table_more').hide();
    if (hi) {
        $('.mys_table_'+id).show();
    }
}


// 加入购物车并提示选择属性
function mys_select_cart(mid, fid, id) {
    $.get("/index.php?is_ajax=1&s=store&c=cart&m=select&id="+id+'&fid='+fid+'&mid='+mid, function(data){
        if (data.code == 1) {
            $('#mys_cart_nums').html(data.data);
        } else if (data.code == 2) {
            var url = data.data;
            layer.open({
                type: 1,
                title: '商品规格',
                fix:true,
                scrollbar: false,
                shadeClose: true,
                shade: 0,
                area: ['600px', '350px'],
                btn: ['加入购物车', '取消'],
                yes: function(index, layero){
                    var sku = $('#mys_sku_value').val();
                    if (!sku) {
                        mys_cmf_tips(0, '商品规格未选择');
                        return;
                    }
                    // 延迟加载
                    var loading = layer.load(2, {
                        shade: [0.3,'#fff'], //0.1透明度的白色背景
                        time: 5000
                    });
                    $.ajax({type: "GET",dataType:"json", url: url+'&sku='+sku,
                        success: function(json) {
                            layer.close(loading);
                            mys_cmf_tips(json.code, json.msg);
                            $('#mys_cart_nums').html(json.data);
                            return false;
                        },
                        error: function(HttpRequest, ajaxOptions, thrownError) {
                            layer.closeAll('loading');
                            alert(HttpRequest.responseText);
                        }
                    });
                    return false;
                },
                content: data.msg
            });
            return;
        }
        mys_cmf_tips(data.code, data.msg);
    }, 'json');
}

// 商品收藏
function mys_mall_favorite(dir, id) {
    $.get("/index.php?is_ajax=1&s=api&app="+dir+"&c=module&m=favorite&id="+id, function(data){
        mys_cmf_tips(data.code, data.msg);
        if (data.code) {
            if (data.data == 1) {
                // 收藏
                $('#mys_mylike_'+id).addClass('mylike');
            } else {
                // 取消
                $('#mys_mylike_'+id).removeClass('mylike');
            }
        }
    }, 'json');
}

// ajax提交登录或者注册
function mys_mall_login_member(form) {

    $.ajax({
        type: "POST",
        dataType: "json",
        url: 'index.php?s=member&c=login',
        data: $("#"+form).serialize(),
        success: function(json) {
            if (json.code == 1) {
                var oss_url = json.data.sso;
                // 发送同步登录信息
                for(var i in oss_url){
                    //alert(oss_url[i]);
                    $.ajax({
                        type: "GET",
                        url:oss_url[i],
                        dataType: "jsonp",
                        success: function(json){
                        },
                        error: function(HttpRequest, ajaxOptions, thrownError){
                        }
                    });
                }
                mys_cmf_tips(1, '登录成功');
                setTimeout("window.location.reload(true)", 3000)
            } else {
                mys_cmf_tips(0, json.msg);
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            alert('error: '+HttpRequest.responseText);
        }
    });
}