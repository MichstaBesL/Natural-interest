$(document).ready(function(){
    login_init()
    hove_box()
})
// window.onload=function(){
//     hove_box()
// }
function tj(){
    $("#login").attr("style","display:none")
}
function yc(){
    $("#login").attr("style","display:none")
}
function login_init(){
    $("#login").attr("style","display:;")
    $(".sr").focus(()=>{
        $(".ts1234").attr("style","height:200px")
    })
    $(".ts1234 li").click(function(){
        let value = this.textContent
        $(".sr").val($(".sr").val()+""+value)
        $(".ts1234").attr("style","height:0px")
    })
}
function hove_box(){
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


