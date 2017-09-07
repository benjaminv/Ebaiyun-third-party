
function checkuserstatus(N,J,K){
    if(N == 1){
        lotteryWindowMessageWait(J,1);
    }else{
        var G ='';
        if(N == -1){
            G = '很抱歉，你的积分不足~';
        }else if(N == -2){
           var G = new Array('注册邮箱被验证后，才可以参加本次活动',K);
        }else if(N == -3){
            G = '很抱歉，只有通过邮箱验证的用户才可以参加本次活动';
        }
        lotteryWindowMessage(G,N);
    }
}

function lotteryResponse(result)
{

    result = eval('('+result+')');
    lotteryWindowMessage(result,result.error);


}


/*抽奖弹窗提示*/
function lotteryWindowMessage(G,N) {
	var E = document.getElementById('alertFram')
	
    var A = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    var D = 0;
    D = Math.min(document.body.clientHeight, document.documentElement.clientHeight);
    if (D == 0) {
        D = Math.max(document.body.clientHeight, document.documentElement.clientHeight)
    }
    var B = document.documentElement.clientWidth || document.body.clientWidth;
    var F = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
    var C = (100+A + (D - 300) / 2) + "px";
    var I = (B - 200) / 2 + "px";
	if(!E)
	{
    var J = document.createElement("DIV");
    J.id = "shield";
    J.style.position = "absolute";
    J.style.left = "0px";
    J.style.top = "0px";
    J.style.width = "100%";
    J.style.height = ((document.documentElement.clientHeight > document.documentElement.scrollHeight) ? document.documentElement.clientHeight: document.documentElement.scrollHeight) + "px";
    J.style.background = "#333";
    J.style.textAlign = "center";
    J.style.zIndex = "10000";
    J.style.filter = "alpha(opacity=0)";
    J.style.opacity = 0;
    var E = document.createElement("DIV");
    E.id = "alertFram";
    E.style.position = "absolute";
    E.style.left = I;
    E.style.top = C;
    if (G) {
        E.style.marginLeft = "-290px"
    } else {
        E.style.marginLeft = "-290px"
    }
    E.style.width = "";
    E.style.height = "";
    E.style.background = "";
    E.style.textAlign = "left";
    E.style.lineHeight = "150px";
    E.style.zIndex = "10001";
	}
    if (N == 1) {
   //     if(G.content['type'] == 1){
            strHtml = '<div id="pop_box"><div id="pop_title"><div id="pop_title_txt">很可惜</div><div id="pop_tilte_close"><a href="javascript:doOk();">关闭</a></div></div><div id="pop_body"><div id="pop_body_txt_line"><font color="#000000">'+G.message+'</font></div><div id="pop_body_goods_line" style="line-height:18px;"><font style="font-size:14px; color:#ff0000;"></font></div><div id="pop_body_btn_line"><table width="100%" border="0" align="center"><tr><td align="center"><a href="javascript:doOk();">继续抽奖</a></td><td align="center"><a href="account.php" target="_blank">去赚取更多积分</a></td></tr></table></div></div></div>'
   /*     }else if (G.content['type'] == 2){
            strHtml = '<div id="pop_box"><div id="pop_title"><div id="pop_title_txt">很可惜</div><div id="pop_tilte_close"><a href="javascript:doOk();">关闭</a></div></div><div id="pop_body"><div id="pop_body_txt_line"><font color="#000000">'+G.message+'</font></div><div id="pop_body_goods_line" style="line-height:18px;"><font style="font-size:14px; color:#ff0000;">'+G.content['info']+'</font></div><div id="pop_body_btn_line"><table width="100%" border="0" align="center"><tr><td align="center"><a href="lottery.php?act=list">继续抽奖</a></td><td align="center"><a href="lottery.php?act=share">去获取更多幸运点</a></td></tr></table></div></div></div>'
        }*/
    } else if (N == 2){
        strHtml = '<div id="pop_box"><div id="pop_title"><div id="pop_title_txt">很可惜</div><div id="pop_tilte_close"><a href="javascript:doOk();">关闭</a></div></div><div id="pop_body"><div id="pop_body_txt_line"><font color="#000000">'+G.message+'</font></div><div id="pop_body_goods_line" style="line-height:18px;"><font style="font-size:14px; color:#ff0000;">'+ G.content +'</font></div><div id="pop_body_btn_line"><table width="100%" border="0" align="center"><tr><td align="center"><a href="account.php" target="_blank">去赚取更多积分</a></td></tr></table></div></div></div>'
    }else if (N == 0){
    //    if (G.content['type'] == '1'){
            strHtml = '<div id="pop_box"><div id="pop_title"><div id="pop_title_txt">恭喜你</div><div id="pop_tilte_close"><a href="lottery.php?act=fetch_rewards&amp;id='+G.wid+';">关闭</a></div></div><div id="pop_body"><div id="pop_body_txt_line"><font color="#FF0000">'+G.message+'</font></div><div id="pop_body_btn_line"><table width="100%" border="0" align="center"><tr> <td align="center"><a href="lottery.php?act=fetch_rewards&amp;id='+G.wid+'">快去领取礼物</a></td></tr></table></div></div></div>'
    /*    }else if (G.content['type'] == '2'){
            strHtml = '<div id="pop_box"><div id="pop_title"><div id="pop_title_txt">恭喜你</div><div id="pop_tilte_close"><a href="javascript:doOk();">关闭</a></div></div><div id="pop_body"><div id="pop_body_txt_line"><font color="#FF0000">'+G.message+'</font></div><div id="pop_body_goods_line" style="line-height:18px;"><table width="70%" border="0" align="center" cellpadding="0" cellspacing="0"><tr><td width="100%" height="30" align="center" valign="bottom"><font color="#cc0000">￥'+G.content['type_money']+'元现金券已经冲入你的账户</font></td></tr><tr><td width="100%" align="center" valign="center">有效期至：'+G.content['end_date']+'</td></tr></table></div><div id="pop_body_btn_line"><table width="100%" border="0" align="center"><tr> <td align="center"><a href="user.php?act=bonus">查看账户</a></td><td align="center"><a href="lottery.php?act=list">继续抽奖</a></td></tr></table></div></div></div>'
        }
*/
    }else if (N == -4){
        strHtml = '<div id="pop_box"><div id="pop_title"><div id="pop_title_txt">验证邮件发送成功</div><div id="pop_tilte_close"><a href="javascript:doOk();">关闭</a></div></div><div id="pop_body"><div id="pop_body_txt_line"><font color="#000000">'+G[0].message+'</font></div><div id="pop_body_btn_line"><table width="100%" border="0" align="center"><tr><td align="center"><a href="'+G[1]+'">去'+G[0].content+'</a></td><td align="center"><a href="javascript:doOk();">关闭</a></td></tr></table></div></div></div>'
    }
    else if (N == -3){
        strHtml = '<div id="pop_boxs" style="width:356px" ><div id="pop_title"><div id="pop_title_txt">提示</div><div id="pop_tilte_close"><a href="javascript:doOk();">关闭</a></div></div><div id="pop_body"><div id="pop_body_txt_line"><font color="#000000">'+G+'</font></div><div id="pop_body_btn_line"><table width="100%" border="0" align="center"><tr><td align="center"><a href="user.php">登录</a></td><td align="center"><a href="javascript:doOk();">关闭</a></td></tr></table></div></div></div>'
    }
    else if (N == -2){
        strHtml = '<div id="pop_box"><div id="pop_title"><div id="pop_title_txt">提示</div><div id="pop_tilte_close"><a href="javascript:doOk();">关闭</a></div></div><div id="pop_body"><div id="pop_body_txt_line"><font color="#000000">'+G[0]+'</font></div><div id="pop_body_btn_line"><table width="100%" border="0" align="center"><tr><td align="center"><a href="'+G[1]+'" target="_blank">去验证邮箱</a></td><td align="center"><a href="javascript:doOk();">关闭</a></td></tr></table></div></div></div>'
    }else if (N == -1){
        strHtml = '<div id="pop_box"><div id="pop_title"><div id="pop_title_txt">已经没有抽奖机会</div><div id="pop_tilte_close"><a href="javascript:doOk();">关闭</a></div></div><div id="pop_body"><div id="pop_body_txt_line"><font color="#000000">很抱歉，你的积分不足~</font></div><div id="pop_body_btn_line_a"><table width="100%" border="0" align="center"><tr><td align="center"><a href="account.php" target="_blank">轻松点完3个广告可获得20积分</a></td></tr></table></div></div></div>'
    }
  //  alert(strHtml);return;
    E.innerHTML = strHtml;
    document.body.appendChild(E);
    document.body.appendChild(J);
    this.setOpacity = function(M, L) {
        M.style.filter = "alpha(opacity=50)"
    };
    var H = 0;
    this.doAlpha = function() {
        if (++H > 30) {
            clearInterval(K);
            return 0
        }
        setOpacity(J, H)
    };
    var K = setInterval("doAlpha()", 1);
    this.doOk = function() {

        document.body.removeChild(E);
        document.body.removeChild(J);
        document.body.onselectstart = function() {
            return true
        };
        document.body.oncontextmenu = function() {
            return true
        }
     //   if (N == 0 || N == 1 || N == 2){
		 if (N == 0 || N == 2){
            window.location.reload();
        }
    };
    document.body.onselectstart = function() {
        return false
    };
    document.body.oncontextmenu = function() {
        return false
    }
}

/*抽奖等待弹窗提示*/
function lotteryWindowMessageWait(G,N) {
    var A = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    var D = 0;
    D = Math.min(document.body.clientHeight, document.documentElement.clientHeight);
    if (D == 0) {
        D = Math.max(document.body.clientHeight, document.documentElement.clientHeight)
    }
    var B = document.documentElement.clientWidth || document.body.clientWidth;
    var F = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
    var C = (100+A + (D - 300) / 2) + "px";
    var I = (B - 200) / 2 + "px";
    var J = document.createElement("DIV");
    J.id = "shield";
    J.style.position = "absolute";
    J.style.left = "0px";
    J.style.top = "0px";
    J.style.width = "100%";
    J.style.height = ((document.documentElement.clientHeight > document.documentElement.scrollHeight) ? document.documentElement.clientHeight: document.documentElement.scrollHeight) + "px";
    J.style.background = "#333";
    J.style.textAlign = "center";
    J.style.zIndex = "10000";
    J.style.filter = "alpha(opacity=0)";
    J.style.opacity = 0;
    var E = document.createElement("DIV");
    E.id = "alertFram";
    E.style.position = "absolute";
    E.style.left = I;
    E.style.top = C;
    if (G) {
        E.style.marginLeft = "-290px"
    } else {
        E.style.marginLeft = "-290px"
    }
    E.style.width = "";
    E.style.height = "";
    E.style.background = "";
    E.style.textAlign = "left";
    E.style.lineHeight = "150px";
    E.style.zIndex = "10001";
    strHtml = '<div id="pop_box"><div id="pop_title"><div id="pop_title_txt">正在抽奖...</div><div id="pop_tilte_close"><a href="javascript:doOk();">关闭</a></div></div><div id="pop_body"><div id="pop_body_txt_line"><font color="#000000">正在抽奖，请稍后...</font></div><div id="pop_body_btn_line"><table width="100%" border="0" align="center"><tr><td align="center"><a href="javascript:doOk();">关闭</a></td></tr></table></div></div></div>'

    E.innerHTML = strHtml
    if (N == 1) {
 /*       $.ajax({
            type: "GET",
            url: "lottery.php?act=do_lottery",
            cache: false,
            data: 'lottery_id=' + G + "&m="+ Math.random(),
            success:function(result){
                doOk();
                lotteryResponse(result);
                }
        });
 */
    }
    document.body.appendChild(E);
    document.body.appendChild(J);

    this.setOpacity = function(M, L) {
        M.style.filter = "alpha(opacity=50)"
    };
    var H = 0;
    this.doAlpha = function() {
        if (++H > 30) {
            clearInterval(K);
            return 0
        }
        setOpacity(J, H)
    };
    var K = setInterval("doAlpha()", 1);
    this.doOk = function() {
   //     $("select").each(function() {
  //          $(this)[0].disabled = false
  //      });
        document.body.removeChild(E);
        document.body.removeChild(J);
        document.body.onselectstart = function() {
            return true
        };
        document.body.oncontextmenu = function() {
            return true
        }
    };
    document.body.onselectstart = function() {
        return false
    };
    document.body.oncontextmenu = function() {
        return false
    }

}


