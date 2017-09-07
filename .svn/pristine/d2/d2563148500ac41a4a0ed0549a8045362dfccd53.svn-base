<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title></title>
    </head>
    <body>
        <form id="form1" name="form1" method="post" action="esapi-ec.php" >
          <label>
 <!-- 测试接口地址 ：http://differcom.gnway.cc:86/ecshopnew/data/spider/esapi-ec.php -->
  </label>
            <p>------------------------订单列表查询--------------------------------------------------</p>
  <p>
    <label>订单列表查询post参数，接入码和secret默认123456 </label>
  </p>
  <p>
  订单状态（OrderStatus）：  <input name="OrderStatus" type="text" id="OrderStatus" value="1" /></br>
  返回数量（ PageSize）： <input name="PageSize" type="text" id="PageSize" value="20" /></br>
   返回的页数（Page）： <input name="Page" type="text" id="Page" value="1" /></br>
   接入码： <input name="uCode" type="text" id="uCode" value="123456" /></br>
   mType： <input name="mType" type="text" id="mType" value="mOrderSearch" /></br>
   时间戳： <input name="TimeStamp" type="text" id="TimeStamp" value="<?php echo(strtotime("now")) ?>" /></br>
  sign：  <input name="Sign" type="text" id="Sign" value="<?php echo( strtoupper(md5( "123456mTypemOrderSearchTimeStamp" . strtotime("now") . "uCode123456123456"))) ?>" /></br>
  </p>
  <p>
    <label>
    <input type="submit" name="Submit" value="查询订单列表" />
    </label>
  </p>
  
</form>
        <p>------------------------以上订单列表查询--------------------------------------------------</p>
        
         <p>------------------------订单详细接口--------------------------------------------------</p>
<form id="form2" name="form2" method="post" action="esapi-ec.php">
  <label>订单详细查询<br />
  <br />
  <br />
  输入订单号：
  <input name="OrderNO" type="text" id="OrderNO"  value=""/>
  </label>
  <p>
    <input name="uCode" type="hidden" id="uCode" value="123456" />
    <input name="mType" type="hidden" id="mType" value="mGetOrder" />
        <input name="TimeStamp" type="hidden" id="TimeStamp" value="<?php echo(strtotime("now")) ?>" />
    <input name="Sign" type="hidden" id="Sign" value="<?php echo( strtoupper(md5( "123456mTypemGetOrderTimeStamp" . strtotime("now") . "uCode123456123456"))) ?>" />
 
  </p>
  <p>
    <label>
        <input type="submit" name="Submit2" value="查询订单详细" onClick="test()"/>
    </label>
  </p>
</form>
         <p>------------------------以上订单详细接口--------------------------------------------------</p>
        <form id="form3" name="form3" method="post" action="esapi-ec.php">
  <label><br />
  <label>查询单品<br />
  <br />
  <br />
  输入商品编号：
  <input name="OuterID" type="text" id="OuterID"  />
  </label>
  </label>
  <p>
       <input name="uCode" type="hidden" id="uCode" value="123456" />
    <input name="mType" type="hidden" id="mType" value="mGetGoods" />
    <input name="TimeStamp" type="hidden" id="TimeStamp" value="<?php echo(strtotime("now")) ?>" />
    <input name="Sign" type="hidden" id="Sign" value="<?php echo( strtoupper(md5( "123456mTypemGetGoodsTimeStamp" . strtotime("now") . "uCode123456123456"))) ?>" />
    <input name="PageSize" type="hidden" id="PageSize" value="0" />
     <input name="Page" type="hidden" id="Page" value="0" />
  </p>
  <p><label>
        <input type="submit" name="Submit3" value="提交" />
    </label></p>
</form>
        <form id="form3" name="form3" method="post" action="esapi-ec.php">
  <label><br />
  <label>查询单品<br />
  <br />
  <br />
  模糊查询商品名称：
  <input name="GoodsName" type="text" id="GoodsName" />
  </label>
  </label>
  <p>
       <input name="uCode" type="hidden" id="uCode" value="123456" />
    <input name="mType" type="hidden" id="mType" value="mGetGoods" />
    <input name="TimeStamp" type="hidden" id="TimeStamp" value="<?php echo(strtotime("now")) ?>" />
    <input name="Sign" type="hidden" id="Sign" value="<?php echo( strtoupper(md5( "123456mTypemGetGoodsTimeStamp" . strtotime("now") . "uCode123456123456"))) ?>" />
    <input name="PageSize" type="hidden" id="PageSize" value="10" />
     <input name="Page" type="hidden" id="Page" value="1" />
  </p>
  <p><label>
        <input type="submit" name="Submit3" value="提交" />
    </label></p>
</form>
        
        <form id="form5" name="form5" method="post" action="esapi-ec.php">
  <label><br />
  <label>查询商品GoodsType=Onsale<br />
  <br />
  <br />
  查询在售商品：
  <input name="GoodsType" type="text" id="GoodsType" value="Onsale"/>
  </label>
  </label>
  <p>
       <input name="uCode" type="hidden" id="uCode" value="123456" />
    <input name="mType" type="hidden" id="mType" value="mGetGoods" />
    <input name="TimeStamp" type="hidden" id="TimeStamp" value="<?php echo(strtotime("now")) ?>" />
    <input name="Sign" type="hidden" id="Sign" value="<?php echo( strtoupper(md5( "123456mTypemGetGoodsTimeStamp" . strtotime("now") . "uCode123456123456"))) ?>" />
    <input name="PageSize" type="hidden" id="PageSize" value="4" />
     <input name="Page" type="hidden" id="Page" value="1" />
  </p>
  <p><label>
        <input type="submit" name="Submit10110" value="提交" />
    </label></p>
    </form>
  

    <?php
       
            
                      	//$s=	strpos('海王大厦b503','广东');
                      	//echo strpos("广东123343423423","广东");
                      	
                      	if(strpos("广东海王大厦","广东")===0)
                      {
                      	$adress = 1;
                      	}
                      	else 
                      	{
                      		$adress = 2;
                      		}

                      	echo $adress;
    ?>
    </body>
</html>
