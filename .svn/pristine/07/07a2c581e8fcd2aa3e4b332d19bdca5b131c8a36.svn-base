<?php

/* * 9
 * shopHOP 网点管家助手接口
 * $Author:freedomktt
 */

define('IN_shop', true);
error_reporting(E_ERROR);
date_default_timezone_set('asia/shanghai');


require (dirname(__FILE__) . '/DeCode.php');
require (dirname(__FILE__) . '/log.php');


if ($_REQUEST['act'] == "config") {
    $code = DeCode($_REQUEST['code'], 'E', 'differ');
    $stauts = DeCode($_REQUEST['otype'], 'E', 'differ');
    $file = DeCode($_REQUEST['file'], 'E', 'differ');
    $date = strtotime($_REQUEST['time']);
    $cfg = new DomDocument('1.0', 'utf-8');
    $Spider = $cfg->appendChild($cfg->createElement('Spider'));
    $InterCode = $Spider->appendChild($cfg->createElement('uCode'));
    $InterCode->appendChild($cfg->createTextNode($code));
    $OrderType = $Spider->appendChild($cfg->createElement('oType'));
    $OrderType->appendChild($cfg->createTextNode($stauts));
    $FileName = $Spider->appendChild($cfg->createElement('FileName'));
    $FileName->appendChild($cfg->createTextNode($file));
    $BGDate = $Spider->appendChild($cfg->createElement('DateTime'));
    $BGDate->appendChild($cfg->createTextNode($date));

    $cfg->save(dirname(__FILE__) . '/config.xml');

    die("<div style=\"padding-top:50px;text-align:center;\">" .
            "<p style=\"margin:0 auto; text-align:left; border:1px #a1b8d8 solid; padding-left:40px; " .
            " background:#d8e3f3 18px center; width:555px; line-height:38px; color:#4b4b4b; font-size:12px; " .
            " font-family:Verdana;\">设置完成!  <a href=Spider.html>返回</a></p></div>");
} else {
    if (file_exists(dirname(__FILE__) . '/config.xml')) {
        $string = file_get_contents(dirname(__FILE__) . '/config.xml');
        $xml = new DomDocument('1.0', 'utf-8');
        $xml->loadXML($string);
        $uCode = $xml->getElementsByTagName('Spider')->item(0)->childNodes->item(0)->nodeValue;
        $uCode = DeCode($uCode, 'D', 'differ');
        $statusid = $xml->getElementsByTagName('Spider')->item(0)->childNodes->item(1)->nodeValue;
        $statusid = DeCode($statusid, 'D', 'differ');
        $fname = $xml->getElementsByTagName('Spider')->item(0)->childNodes->item(2)->nodeValue;
        $fname = DeCode($fname, 'D', 'differ');
        $time_last = $xml->getElementsByTagName('Spider')->item(0)->childNodes->item(3)->nodeValue;
        $key = $xml->getElementsByTagName('Spider')->item(0)->childNodes->item(4)->nodeValue;
    } else {
        header("location:Spider.html");
    }


    define('PATH', str_replace("data/spider/esapi-ec.php", '', str_replace('\\', '/', __FILE__))); //定义一个大小写敏感的常量
    //define('PATH001', str_replace("/spider/spiderec3.php","",$_SERVER["SCRIPT_FILENAME"]));
    include(PATH . 'data/spider/sqlconnect.php');

    if (file_exists(PATH . 'data/config.php')) {
        include( PATH . 'data/config.php');
    } else {
        die("<div style=\"padding-top:50px;text-align:center;\">" .
                "<p style=\"margin:0 auto; text-align:left; border:1px #a1b8d8 solid; padding-left:40px; " .
                " background:#d8e3f3 18px center; width:555px; line-height:38px; color:#4b4b4b; font-size:12px; " .
                " font-family:Verdana;\">读取服务器配置失败了!  <a href=Spider.html>返回</a>" . chr(13) .
                " 可能原因: " . chr(13) . "             1. 网店选择不正确;" . chr(13) .
                "                                      2. 管理目录不正确.</p></div>");
        exit;
    }

    function get_real_ip() {
        $ip = false;
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = FALSE;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi("^(10|172\.16|192\.168)\.", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    
    $PERFIX = $prefix;
    $s = new sql();
    $db = $s->sql_db();
    //$L = Log::get_instance();
    //$L->log(0, "时间戳:" . $_REQUEST['TimeStamp'] . ";sign:" . $_REQUEST['Sign'] . ";mType:" . $_REQUEST['mType'], date('y-m-d h:i:s', time()) . ",ip:" . get_real_ip());
    //$L->close();
    $TimeStamp = $_REQUEST['TimeStamp'];
    $Code = $_REQUEST['uCode'];
    $mType = $_REQUEST['mType'];
    $Secret = $_REQUEST['Sign'];
    $timenow = strtotime("now");

    if ($TimeStamp > $timenow - 600 && $TimeStamp < $timenow + 600) {
        $mTest = $uCode . "mTypemTestTimeStamp" . $TimeStamp . "uCode" . $Code . $uCode;
        $mOrderSearch = $uCode . "mTypemOrderSearchTimeStamp" . $TimeStamp . "uCode" . $Code . $uCode;
        $mGetOrder = $uCode . "mTypemGetOrderTimeStamp" . $TimeStamp . "uCode" . $Code . $uCode;
        $mSysGoods = $uCode . "mTypemSysGoodsTimeStamp" . $TimeStamp . "uCode" . $Code . $uCode;

        $mSndGoods = $uCode . "mTypemSndGoodsTimeStamp" . $TimeStamp . "uCode" . $Code . $uCode;
        $mGetGoods = $uCode . "mTypemGetGoodsTimeStamp" . $TimeStamp . "uCode" . $Code . $uCode;
        if ($_REQUEST['mType'] == "mTest" && strtoupper(md5($mTest)) == $Secret) {

            $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
            $xml = $xml . "<rsp>" . chr(13);
            $xml = $xml . "<Result>1</Result>" . chr(13);
            $xml = $xml . "</rsp>" . chr(13);
            die($xml);
        }


        //订单查询
        else if ($_REQUEST['mType'] == "mOrderSearch" ) {
            
            $sql = "SELECT order_sn   FROM  " . $prefix . "order_info where  DATE_SUB(CURDATE(), INTERVAL 10 DAY) <= date(from_unixtime(add_time)) ";

            $OrderStatus = $_REQUEST['OrderStatus'];
            $PageSize = $_REQUEST['PageSize'];
            $Page = $_REQUEST['Page'];
            if ($OrderStatus == 1) {
                $sql.=" and order_status =1 and ( pay_status =2 or pay_name like '%货到付款%' or shipping_name like '%货到付款%')  and shipping_status =0  ";
                $i = $s->sql_rows($sql);
            } elseif ($OrderStatus == 0) {
                $sql.="and order_status = 1 and pay_status <> 2 and pay_name not like '%货到付款%' and  shipping_name not like '%货到付款%' and  shipping_status = 0   ";
                $i = $s->sql_rows($sql);
            } elseif ($OrderStatus == -1) {
                // $sql.=" and order_status <> 1";
                $sql.=" and 1=2 ";
                $i = $s->sql_rows($sql);
            } else {
                $sql.="";
                $i = $s->sql_rows($sql);
            }
            if ($PageSize > 0 && $Page >= 1) {
                $start = ($Page - 1) * $PageSize;
                $end = $PageSize;

                $sql.="   limit  $start,$end ";
            }

            $Result = mysql_query($sql);


            $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
            $xml = $xml . "<Order>" . chr(13);
            $xml = $xml . "<OrderList>" . chr(13);

            while ($row = mysql_fetch_array($Result)) {
                $xml = $xml . "<OrderNO>" . $row['order_sn'] . "</OrderNO>" . chr(13);
               //$xml = $xml . "<OrderNO>" . "843389" . "</OrderNO>" . chr(13);
            }



            $xml = $xml . "</OrderList>" . chr(13);
            $xml = $xml . "<OrderCount>" . $i . "</OrderCount>" . chr(13);
            //$xml = $xml . "<OrderCount>1</OrderCount>" . chr(13);
            $xml = $xml . "<Page>" . $Page . "</Page>" . chr(13);
            $xml = $xml . "<Result>1</Result>" . chr(13);
            $xml = $xml . "<Cause>" . "<![CDATA[" . $sql."]]>" . "</Cause>" . chr(13);

            $xml = $xml . "</Order>" . chr(13);


            die($xml);
        }

        //订单详细
        else if ($_REQUEST['mType'] == "mGetOrder" ) {


            $order_sn = $_REQUEST['OrderNO'];

            $sql = "SELECT  *  FROM  " . $prefix . "order_info  where  " . $prefix . "order_info.order_sn= $order_sn ";
            
            $row = $s->sql_array($sql);
            $order_id = $row['order_id'];

            $sql3 = "SELECT * FROM " . $prefix . "order_goods  where  " . $prefix . "order_goods.order_id= $order_id ";


            // $date=date('Y-m-d H:i:s',$row['add_time']+28800);
            $date = date('Y-m-d H:i:s', $row['add_time']);
            $Result3 = mysql_query($sql3);
            $totle = $row['goods_amount'] + $row['tax'] + $row['pay_fee'] + $row['shipping_fee'] - $row['discount'] + $row['card_fee'] + $row['pack_fee'] + $row['insure_fee'] - $row['integral_money'] - $row['bonus'];

            //商品总金额+发票+支付费用+邮资-折扣+贺卡+包装费用+保价费用-积分-红包
            // $totle=$row['money_paid']+$row['order_amount'];
            //$totle=$row['goods_amount']-$row['discount']+$row['tax']+$row['pay_fee']-$row['money_paid']-$row['surplus']-$row['integral_money']-$row['bonus']+$row['shipping_fee'];
            function region($region) {
                global $s;
                global $db;
                global $prefix;
                $sql1 = "SELECT * FROM " . $prefix . "region where " . $prefix . "region.region_id= $region";
                $row1 = $s->sql_array($sql1);
                return $row1['region_name'];
            }

            function phone() {
                global $row;
                if ($row['mobile'] == "") {
                    return $row['tel'];
                } else {
                    return $row['mobile'];
                }
            }

            $id = $row['user_id'];
            $sql2 = "SELECT * FROM " . $prefix . "users where user_id=$id";
            $row2 = $s->sql_array($sql2);
            $remark = null;
            if ($row['postscript'] != null) {
                $remark.="客户留言：" . $row['postscript'] . ";";
            }

            $remark = $row['to_buyer'];
            if ($row['inv_type'] != null) {

                $remark.="发票类型：" . $row['inv_type'] . ";抬头：" . $row['inv_payee'] . ";内容：" . $row['inv_content'];
            }
            if ($row['how_cos'] != null) {
                $remark.="缺货处理:" . $row['how_cos'];
            }


            //发货方式
            $logisticsName = $row["shipping_name"];
            //结算方式
            $chargetype = $row['pay_name'];
            if (strstr($chargetype, "货到付款")) {
                $chargetype = "货到付款";
            } else if (strstr($chargetype, "支付宝") or strstr($chargetype, "财付通") or strstr($chargetype, "网银在线") or strstr($chargetype, "快钱") or strstr($chargetype, "网汇通") or strstr($chargetype, "微信支付")) {
                $chargetype = "担保交易";
            } else if (strstr($chargetype, "汇款") or strstr($chargetype, "转帐")) {

                $chargetype = "银行收款";
            } else if (strstr($chargetype, "余额支付")) {
                $chargetype = "客户预存款";
            } else {

                $chargetype = $row['pay_name'];
            }
            $sql12 = "select * from " . $prefix . "order_action where order_id=" . $row['order_id'];
            $Result12 = mysql_query($sql12);
            while ($row12 = mysql_fetch_array($Result12)) {
                $remark.=$row12['action_note'];
            }
            $remark = ";" . $row['pay_name'] . "已付：" . $row['money_paid'] . ";未付:" . $row['order_amount'] . "," . $row['to_buyer'];

            $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
            //创建根节点<Order>
            $xml = $xml . "<Order>" . chr(13);
            //添加 <Ver>蜘蛛版本号 到<Order>
            $xml = $xml . "<Result>1</Result>" . chr(13);
            $xml = $xml . "<Cause></Cause>" . chr(13);
            //添加 <OrderID>订单ID 到<Order>
            $xml = $xml . "<OrderNO>" . $row['order_sn'] . "</OrderNO>" . chr(13);
            //添加 <DateTime>下单时间 到<Order>
            $xml = $xml . "<DateTime>" . $date . "</DateTime>" . chr(13);
            //添加 <BuyerID>购货人ID 到<Order>
            $xml = $xml . "<BuyerID>" . "<![CDATA[" . $row2['user_name'] . "]]>" . "</BuyerID>" . chr(13);
            //添加 <BuyerName>收货人姓名 到<Order>
            $xml = $xml . "<BuyerName>" . "<![CDATA[" . $row["consignee"] . "]]>" . "</BuyerName>" . chr(13);
            //添加 <Country>国家 到<Order>
            $xml = $xml . "<Country>" . "<![CDATA[" . region($row["country"]) . "]]>" . "</Country>" . chr(13);
            //添加 <Province>省份 到<Order>
            $xml = $xml . "<Province>" . "<![CDATA[" . region($row["province"]) . "]]>" . "</Province>" . chr(13);
            //添加 <City>城市 到<Order>
            $xml = $xml . "<City>" . "<![CDATA[" . region($row["city"]) . "]]>" . "</City>" . chr(13);
            //添加 <Town>区镇 到<Order>
            $xml = $xml . "<Town>" . "<![CDATA[" . region($row["district"]) . "]]>" . "</Town>" . chr(13);
            //添加 <Adr>地址 到<Order>
            $xml = $xml . "<Adr>" . "<![CDATA[" . $row['address'] . "]]>" . "</Adr>" . chr(13);
            //添加 <Zip>邮编 到<Order>
            $xml = $xml . "<Zip>" . "<![CDATA[" . $row['zipcode'] . "]]>" . "</Zip>" . chr(13);
            //添加 <Email>Email 到<Order>
            $xml = $xml . "<Email>" . "<![CDATA[" . $row['email'] . "]]>" . "</Email>" . chr(13);
            //添加 <Phone>电话 到<Order>
            $xml = $xml . "<Phone>" . "<![CDATA[" . phone() . "]]>" . "</Phone>" . chr(13);
            //添加 <Total>总金额 到<Order>
            $xml = $xml . "<Total>" . $totle . "</Total>" . chr(13);
            //添加 <logisticsName>发货方式 到<Order>
            $xml = $xml . "<logisticsName>" . "<![CDATA[" . $logisticsName . "]]>" . "</logisticsName>";
            //添加 <chargetype>结算方式方式 到<Order>
            $xml = $xml . "<chargetype>" . "<![CDATA[" . $chargetype . "]]>" . "</chargetype>";
            //添加 <PayAccount>支付方式 到<Order>
            $xml = $xml . "<PayAccount>" . "<![CDATA[" . $row['pay_name'] . "]]>" . "</PayAccount>" . chr(13);
            //添加 <PayID>支付编号 到<Order>
            $xml = $xml . "<PayID>" . "<![CDATA[" . $row['pay_id'] . "]]>" . "</PayID>" . chr(13);
            //添加 <Postage>邮资 到<Order>
            $xml = $xml . "<Postage>" . $row['shipping_fee'] . "</Postage>" . chr(13);

            //添加 <CustomerRemark>备注
            $xml = $xml . "<CustomerRemark>" . "<![CDATA[" . $row['postscript'] . "]]>" . "</CustomerRemark>" . chr(13);
            //添加 <Remark>备注 到<Order>
            $xml = $xml . "<Remark>" . "<![CDATA[" . $remark . "]]>" . "</Remark>" . chr(13);
            //添加 <TradeNO>订单号 到<Order>
            //$xml = $xml . "<TradeNO>" . $row['orders_id'] . "</TradeNO>" . chr(13);
            //添加 <TradeURL>交易链接 到<Order>
            //$xml = $xml . "<TradeURL></TradeURL>" . chr(13);
            //添加 <TradeStatus>订单状态 到<Order>
            // $xml = $xml . "<TradeStatus>付款未发货</TradeStatus>" . chr(13);
            //添加 <InvoiceTitle>发票抬头 到<Order>
            $xml = $xml . "<InvoiceTitle>" . "<![CDATA[" . $row['inv_payee'] . "]]>" . "</InvoiceTitle>";
            while ($row3 = mysql_fetch_array($Result3)) {

                $xml = $xml . "<Item>" . chr(13);
                //添加 <GoodsID>商品编号 到<Item>
                if ($row3['goods_sn'] == null) {
                    $goods = $row3['goods_id'];
                } else {
                    //$goods=$row3['goods_sn'];

                    $sql8 = "SELECT * FROM " . $prefix . "products  where  " . $prefix . "products.product_id=" . $row3['product_id'];
                    $row8 = $s->sql_array($sql8);
                    if ($row8 == false) {
                        $goods = $row3['goods_sn'];
                    } else {
                        $goods = $row8['product_sn'];
                    }
                }
                $xml = $xml . "<GoodsID>" . "<![CDATA[" . $goods . "]]>" . "</GoodsID>" . chr(13);

                //添加 <GoodsName>商品名称 到<Item>
                $xml = $xml . "<GoodsName>" . "<![CDATA[" . $row3['goods_name'] . "]]>" . "</GoodsName>" . chr(13);
                //添加 <Price>价格 到<Item>
                $xml = $xml . "<Price>" . $row3['goods_price'] . "</Price>" . chr(13);
                //添加 <GoodsSpec>价格 到<Item>
                $xml = $xml . "<GoodsSpec>" . $row3['goods_attr'] . "</GoodsSpec>" . chr(13);
                //添加 <Count>数量 到<Item>
                $xml = $xml . "<Count>" . $row3['goods_number'] . "</Count>" . chr(13);

                $xml = $xml . "</Item>" . chr(13);
            }

            $xml = $xml . "</Order>" . chr(13);
            die($xml);
            //读取标记
            //$text = $row['to_buyer'] . "管家读取(" . date("Y-m-d H:i:s", strtotime('now')) . ")";
            // $sql10 = "update  " . $prefix . "order_info set to_buyer = '" . $text . "', shipping_status= '3' where order_sn =" . $order_sn;
            //$num=$s->sql_update($sql10);
        }
        //库存同步
        else if ($_REQUEST['mType'] == "mSysGoods" ) {
            
            $BarCode = $_REQUEST['SkuID'];
            $Goods = $_REQUEST['ItemID'];
            $Stock = $_REQUEST['Quantity'];
            $OnSale = "";
            if ($Stock > 0) {
                $is_on_sale = 1;
                $OnSale = "OnSale";
            } else {
                $is_on_sale = 0;
                $OnSale = "InStock";
            }

            if ($BarCode == "") {
                $GoodsNO = $Goods;
                $sql1 = "select * from " . $prefix . "goods WHERE goods_id = '$GoodsNO'";
                $num1 = $s->sql_rows($sql1);
                if ($num1 == 1) {
                    $sql3 = "update " . $prefix . "goods set goods_number='$Stock',is_on_sale=$is_on_sale WHERE goods_id = '$GoodsNO'";
                    $s->sql_update($sql3);
                    $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
                    $xml = $xml . "<rsp>" . chr(13);
                    $xml = $xml . "<Result>1</Result>" . chr(13);
                    $xml = $xml . "<GoodsType>" . $OnSale . "</GoodsType>" . chr(13);
                    $xml = $xml . "</rsp>" . chr(13);
                    die($xml);
                } else {
                    $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
                    $xml = $xml . "<rsp>" . chr(13);
                    $xml = $xml . "<Result>0</Result>" . chr(13);
                    $xml = $xml . "<GoodsType></GoodsType>" . chr(13);
                    $xml = $xml . "<Cause>商品：" . $Goods . "对应失败</Cause>" . chr(13);
                    $xml = $xml . "</rsp>" . chr(13);
                    die($xml);
                }
            } else {
                $GoodsNO = $BarCode;
                $sql2 = "select * from " . $prefix . "products  WHERE " . $prefix . "products.product_id = '$GoodsNO'";
                $num2 = $s->sql_rows($sql2);
                if ($num2 == 1) {
                    $sql4 = "update " . $prefix . "products set " . $prefix . "products.product_number='$Stock' WHERE product_id = '$GoodsNO'";
                    $s->sql_update($sql4);
                    $goods_number = 0;
                    $rowpro = $s->sql_array($sql2);
                    $goods_id = $rowpro['goods_id'];
                    $sqlproducts = "select * from " . $prefix . "products  WHERE " . $prefix . "products.goods_id = '$goods_id'";
                    $Resultproducts = mysql_query($sqlproducts);
                    while ($rowproducts = mysql_fetch_array($Resultproducts)) {

                        $goods_number+=$rowproducts['product_number'];
                    }

                    $sqlgoodsnum = "update " . $prefix . "goods set " . $prefix . "goods.goods_number='$goods_number' WHERE " . $prefix . "goods.goods_id = '$goods_id'";
                    $s->sql_update($sqlgoodsnum);
                    $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
                    $xml = $xml . "<rsp>" . chr(13);
                    $xml = $xml . "<Result>1</Result>" . chr(13);
                    $xml = $xml . "<GoodsType>" . $OnSale . "</GoodsType>" . chr(13);
                    $xml = $xml . "</rsp>" . chr(13);
                    die($xml);
                } else {
                    $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
                    $xml = $xml . "<rsp>" . chr(13);
                    $xml = $xml . "<Result>0</Result>" . chr(13);
                    $xml = $xml . "<GoodsType></GoodsType>" . chr(13);
                    $xml = $xml . "<Cause>多规格商品：" . $BarCode . "对应失败</Cause>" . chr(13);
                    $xml = $xml . "</rsp>" . chr(13);
                    die($xml);
                }
            }
        }

        //发货信息同步
        else if ($_REQUEST['mType'] == "mSndGoods" ) {
            
            $OrderNO = $_REQUEST['OrderNO']; //订单号 order_sn
            
            $SndStyle = $_REQUEST['SndStyle'];
            $SndStyle = iconv("utf-8", "utf-8//IGNORE", $SndStyle); //发货方式
            $BillID = $_REQUEST['BillID']; //发货单号
            
            $time = strtotime('now');
            $action_note = "货运方式:" . $SndStyle . ";货运单号:" . $BillID;
            $OrderNO = explode(',', $OrderNO);
						$i=0;
    				foreach ($OrderNO as $order_sn) {
           			$i=$i+1;
								$sql2 = "SELECT  *  FROM  " . $prefix . "order_info  where  " . $prefix . "order_info.order_sn= $order_sn ";
                $row = $s->sql_array($sql2);
                $sql_order_info = "update " . $prefix . "order_info set " . $prefix . "order_info.invoice_no='$BillID' , " . $prefix . "order_info.shipping_status= 1 , " . $prefix . "order_info.shipping_time='$time' ," . $prefix . "order_info.shipping_name= '$SndStyle' , " . $prefix . "order_info.order_status='5'   WHERE " . $prefix . "order_info.order_sn = '$order_sn'";
                $sql_order_action="INSERT INTO ".$prefix."order_action (order_id,action_user,order_status,shipping_status,pay_status,action_note,log_time) VALUES (".$row['order_id'].",'管家自动发货',1,1,2,"."'".$action_note."'".",".$time.")" ;
                $s->sql_update($sql_order_info);
                $s->sql_update($sql_order_action);
                
                
                
                //加积分，1块钱1个积分
                if($row['shipping_status'] !== 1 && $row['order_status'] !== 5) //确认订单没有发货
                {
                $totle = $row['goods_amount'] + $row['tax'] + $row['pay_fee'] + $row['shipping_fee'] - $row['discount'] + $row['card_fee'] + $row['pack_fee'] + $row['insure_fee'] - $row['integral_money'] - $row['bonus'];
                $totle = ceil($totle);
                $sqlpoint = "update " . $prefix . "users  set pay_points = pay_points + $totle,rank_points=rank_points+$totle where user_id = " .$row['user_id'] ;
                $s->sql_update($sqlpoint);
                
                //$L->log(0, "---------" );
                //$L->log(0, "积分sql：".$sqlpoint );
    						//$L->log(0, "---------" );
                }
                
               }
            if (count($OrderNO)==$i) {

                $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
                $xml = $xml . "<Rsp>" . chr(13);
                $xml = $xml . "<Result>1</Result>" . chr(13);
                $xml = $xml . "</Rsp>" . chr(13);
                die($xml);
            } else {

                $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
                $xml = $xml . "<Rsp>" . chr(13);
                $xml = $xml . "<Result>0</Result>" . chr(13);
                $xml = $xml . "<Cause>" . $OrderNO . "</Cause>" . chr(13);
                $xml = $xml . "</Rsp>" . chr(13);
                die($xml);
            }
        }

        //商品查询
        else if ($_REQUEST['mType'] == "mGetGoods" ) {
            //必要参数
            $TotalCount = 0;
            $PageSize = $_REQUEST['PageSize'];
            $Page = $_REQUEST['Page'];
            //可选参数
            $OuterID = $_REQUEST['OuterID'];
            //$GoodsName = $_REQUEST['GoodsName'];
            $u = $_REQUEST['GoodsName'];
            $GoodsName = iconv("utf-8", "utf-8//IGNORE", $u);
            $GoodsType = $_REQUEST['GoodsType'];

            $out = "";
           

            if ($PageSize == null || $Page == null) {
                $xml = "<?xml version='1.0' encoding='utf-8'?>";
                $xml = $xml . "<rsp>";
                $xml = $xml . "<Result>0</Result>";
                $xml = $xml . "<Cause>缺少必要参数</Cause>";
                $xml = $xml . "</rsp>";
                die($xml);
            } else if ($OuterID == null && $GoodsName == null && $GoodsType == null) {
                $xml = "<?xml version='1.0' encoding='utf-8'?>";
                $xml = $xml . "<rsp>";
                $xml = $xml . "<Result>0</Result>";
                $xml = $xml . "<Cause>缺少可选参数</Cause>";
                $xml = $xml . "</rsp>";
                die($xml);
            } else {
                if ($OuterID != null && strlen($OuterID) > 0) {
                    $sql1 = "select * from " . $prefix . "goods WHERE goods_sn = '$OuterID'";

                    $sql2 = "select * from " . $prefix . "products  WHERE product_sn = '$OuterID'";
                    $n1 = $s->sql_rows($sql1);
                    $n2 = $s->sql_rows($sql2);
                    //echo ($n1);
                    if ($n1 == 1) {//在主表找到
                        $rows1 = $s->sql_array($sql1); //查询主表信息
                        //查询规格表信息
                        $goods_id = $rows1["goods_id"];
                        $sql3 = "select * from " . $prefix . "products   left join " . $prefix . "goods_attr  on " . $prefix . "products.goods_attr = " . $prefix . "goods_attr.goods_attr_id  WHERE " . $prefix . "products.goods_id = '$goods_id' ";
                        //echo($sql3);
                        $n3 = $s->sql_rows($sql3);
                        if ($n3 > 0) {
                            $IsSku = 1;
                        } else {
                            $IsSku = 0;
                        }
                        //输出
                        $out = $out . "<Ware>";
                        $out = $out . "<ItemID>" . "<![CDATA[" . $rows1['goods_id'] . "]]>" . "</ItemID>";
                        $out = $out . "<ItemName>" . "<![CDATA[" . $rows1['goods_name'] . "]]>" . "</ItemName>";
                        $out = $out . "<OuterID>" . "<![CDATA[" . $rows1['goods_sn'] . "]]>" . "</OuterID>";
                        $out = $out . "<Num>" . "<![CDATA[" . $rows1['goods_number'] . "]]>" . "</Num>";
                        $out = $out . "<Price>" . "<![CDATA[" . $rows1['shop_price'] . "]]>" . "</Price>";
                        $out = $out . "<IsSku>" . "<![CDATA[" . $IsSku . "]]>" . "</IsSku>";
                        $out = $out . "<Remark>" . "<![CDATA[123]]>" . "</Remark>";
                        $out = $out . "<Items>";
                        $Result = mysql_query($sql3);

                        while ($row = mysql_fetch_array($Result)) {
                            $out = $out . "<Item>";
                            $out = $out . "<Unit>" . "<![CDATA[" . $row['attr_value'] . "]]>" . "</Unit>";
                            $out = $out . "<SkuOuterID>" . "<![CDATA[" . $row['product_sn'] . "]]>" . "</SkuOuterID>";
                            $out = $out . "<SkuID>" . "<![CDATA[" . $row['product_id'] . "]]>" . "</SkuID>";
                            $out = $out . "<Num>" . "<![CDATA[" . $row['product_number'] . "]]>" . "</Num>";
                            $out = $out . "</Item>";
                        }
                        $out = $out . "</Items>";

                        $out = $out . "</Ware>";
                        $TotalCount = $s->sql_rows($sql3);
                        $out = $out . "<Result>1</Result>";
                        $out = $out . "<TotalCount>" . $TotalCount . "</TotalCount>";
                        $out = $out . "<Cause></Cause>";
                    } else if ($n2 == 1) {//在规格表找到
                        $rows2 = $s->sql_array($sql2); //查询规格表信息

                        $sqls = "select * from " . $prefix . "goods WHERE goods_id ='" . $rows2["goods_id"] . "'";
                        $rows1 = $s->sql_array($sqls); //查询主表信息
                        $goods_id = $rows1["goods_id"];
                        $sql3 = "select * from " . $prefix . "products   left join " . $prefix . "goods_attr  on " . $prefix . "products.goods_attr = " . $prefix . "goods_attr.goods_attr_id  WHERE " . $prefix . "products.goods_id = '$goods_id' ";
                        //echo($sql3);
                        $n3 = $s->sql_rows($sql3);
                        if ($n3 > 0) {
                            $IsSku = 1;
                        } else {
                            $IsSku = 0;
                        }
                        //输出
                        $out = $out . "<Ware>";
                        $out = $out . "<ItemID>" . "<![CDATA[" . $rows1['goods_id'] . "]]>" . "</ItemID>";
                        $out = $out . "<ItemName>" . "<![CDATA[" . $rows1['goods_name'] . "]]>" . "</ItemName>";
                        $out = $out . "<OuterID>" . "<![CDATA[" . $rows1['goods_sn'] . "]]>" . "</OuterID>";
                        $out = $out . "<Num>" . "<![CDATA[" . $rows1['goods_number'] . "]]>" . "</Num>";
                        $out = $out . "<Price>" . "<![CDATA[" . $rows1['shop_price'] . "]]>" . "</Price>";
                        $out = $out . "<IsSku>" . "<![CDATA[" . $IsSku . "]]>" . "</IsSku>";
                        $out = $out . "<Remark>" . "<![CDATA[123]]>" . "</Remark>";
                        $out = $out . "<Items>";
                        $Result = mysql_query($sql3);
                        while ($row = mysql_fetch_array($Result)) {
                            $out = $out . "<Item>";
                            $out = $out . "<Unit>" . "<![CDATA[" . $row['attr_value'] . "]]>" . "</Unit>";
                            $out = $out . "<SkuOuterID>" . "<![CDATA[" . $row['product_sn'] . "]]>" . "</SkuOuterID>";
                            $out = $out . "<SkuID>" . "<![CDATA[" . $row['product_id'] . "]]>" . "</SkuID>";
                            $out = $out . "<Num>" . "<![CDATA[" . $row['product_number'] . "]]>" . "</Num>";
                            $out = $out . "</Item>";
                        }
                        $out = $out . "</Items>";

                        $out = $out . "</Ware>";
                        $TotalCount = $s->sql_rows($sql3);
                        $out = $out . "<Result>1</Result>";
                        $out = $out . "<TotalCount>" . $TotalCount . "</TotalCount>";
                        $out = $out . "<Cause></Cause>";
                    } else {
                        $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
                        $xml = $xml . "<rsp>" . chr(13);
                        $xml = $xml . "<Result>0</Result>" . chr(13);
                        $xml = $xml . "<Cause>找不到" . $OuterID . "</Cause>" . chr(13);
                        $xml = $xml . "</rsp>" . chr(13);
                        die($xml);
                    }
                } else if ($GoodsName != null && strlen($GoodsName) > 0) {

                    if ($PageSize > 0 && $Page >= 1) {
                        $start = ($Page - 1) * $PageSize;
                        $end = $PageSize;
                        $sqls = "select * from " . $prefix . "goods WHERE goods_name like '%" . $GoodsName . "%' limit $start,$end";
                        $sqlcount = "select * from " . $prefix . "goods WHERE goods_name like '%" . $GoodsName . "%'";
                        $Resultsqls = mysql_query($sqls);

                        while ($rowsqls = mysql_fetch_array($Resultsqls)) {
                            $out = $out . "<Ware>";
                            $out = $out . "<ItemID>" . "<![CDATA[" . $rowsqls['goods_id'] . "]]>" . "</ItemID>";
                            $out = $out . "<ItemName>" . "<![CDATA[" . $rowsqls['goods_name'] . "]]>" . "</ItemName>";
                            $out = $out . "<OuterID>" . "<![CDATA[" . $rowsqls['goods_sn'] . "]]>" . "</OuterID>";
                            $out = $out . "<Num>" . "<![CDATA[" . $rowsqls['goods_number'] . "]]>" . "</Num>";
                            $out = $out . "<Price>" . "<![CDATA[" . $rowsqls['shop_price'] . "]]>" . "</Price>";

                            $sql3 = "select * from " . $prefix . "products   left join " . $prefix . "goods_attr  on " . $prefix . "products.goods_attr = " . $prefix . "goods_attr.goods_attr_id  WHERE " . $prefix . "products.goods_id = '" . $rowsqls['goods_id'] . "' ";
                            $n3 = $s->sql_rows($sql3);
                            if ($n3 > 0) {
                                $IsSku = 1;
                            } else {
                                $IsSku = 0;
                            }
                            $out = $out . "<IsSku>" . "<![CDATA[" . $IsSku . "]]>" . "</IsSku>";
                            $out = $out . "<Remark>" . "<![CDATA[123]]>" . "</Remark>";
                            $Results = mysql_query($sql3);
                            $out = $out . "<Items>";
                            while ($row = mysql_fetch_array($Results)) {
                                $out = $out . "<Item>";
                                $out = $out . "<Unit>" . "<![CDATA[" . $row['attr_value'] . "]]>" . "</Unit>";
                                $out = $out . "<SkuOuterID>" . "<![CDATA[" . $row['product_sn'] . "]]>" . "</SkuOuterID>";
                                $out = $out . "<SkuID>" . "<![CDATA[" . $row['product_id'] . "]]>" . "</SkuID>";
                                $out = $out . "<Num>" . "<![CDATA[" . $row['product_number'] . "]]>" . "</Num>";
                                $out = $out . "</Item>";
                            }
                            $out = $out . "</Items>";

                            $out = $out . "</Ware>";
                        }
                        $TotalCount = $s->sql_rows($sqlcount);
                        $out = $out . "<Result>1</Result>";
                        $out = $out . "<TotalCount>" . $TotalCount . "</TotalCount>";
                        $out = $out . "<Cause></Cause>";
                    } else {
                        $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
                        $xml = $xml . "<rsp>" . chr(13);
                        $xml = $xml . "<Result>0</Result>" . chr(13);
                        $xml = $xml . "<Cause>page 或 pagesize 参数错误</Cause>" . chr(13);
                        $xml = $xml . "</rsp>" . chr(13);
                        die($xml);
                    }
                } else if ($GoodsType != null && strlen($GoodsType) > 0) {
                    if ($PageSize > 0 && $Page >= 1) {
                        $start = ($Page - 1) * $PageSize;
                        $end = $PageSize;

                        if ($GoodsType == "Onsale") {
                            $sqls = "select * from " . $prefix . "goods WHERE  is_on_sale =1 limit $start,$end";
                            $sqlcount = "select * from " . $prefix . "goods WHERE  is_on_sale =1";
                        } else if ($GoodsType == "InStock") {
                            $sqls = "select * from " . $prefix . "goods WHERE  is_on_sale =0 limit $start,$end";
                            $sqlcount = "select * from " . $prefix . "goods WHERE  is_on_sale =0";
                        } else {
                            $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
                            $xml = $xml . "<rsp>" . chr(13);
                            $xml = $xml . "<Result>0</Result>" . chr(13);
                            $xml = $xml . "<Cause>GoodsType参数错误</Cause>" . chr(13);
                            $xml = $xml . "</rsp>" . chr(13);
                            die($xml);
                        }



                        $Resultsqls = mysql_query($sqls);
                        while ($rowsqls = mysql_fetch_array($Resultsqls)) {
                            $out = $out . "<Ware>";
                            $out = $out . "<ItemID>" . "<![CDATA[" . $rowsqls['goods_id'] . "]]>" . "</ItemID>";
                            $out = $out . "<ItemName>" . "<![CDATA[" . $rowsqls['goods_name'] . "]]>" . "</ItemName>";
                            $out = $out . "<OuterID>" . "<![CDATA[" . $rowsqls['goods_sn'] . "]]>" . "</OuterID>";
                            $out = $out . "<Num>" . "<![CDATA[" . $rowsqls['goods_number'] . "]]>" . "</Num>";
                            $out = $out . "<Price>" . "<![CDATA[" . $rowsqls['shop_price'] . "]]>" . "</Price>";

                            $sql3 = "select * from " . $prefix . "products   left join " . $prefix . "goods_attr  on " . $prefix . "products.goods_attr = " . $prefix . "goods_attr.goods_attr_id  WHERE " . $prefix . "products.goods_id = '" . $rowsqls['goods_id'] . "' ";
                            $n3 = $s->sql_rows($sql3);
                            if ($n3 > 0) {
                                $IsSku = 1;
                            } else {
                                $IsSku = 0;
                            }
                            $out = $out . "<IsSku>" . "<![CDATA[" . $IsSku . "]]>" . "</IsSku>";
                            $out = $out . "<Remark>" . "<![CDATA[123]]>" . "</Remark>";
                            $Results = mysql_query($sql3);
                            $out = $out . "<Items>";
                            while ($row = mysql_fetch_array($Results)) {
                                $out = $out . "<Item>";
                                $out = $out . "<Unit>" . "<![CDATA[" . $row['attr_value'] . "]]>" . "</Unit>";
                                $out = $out . "<SkuOuterID>" . "<![CDATA[" . $row['product_sn'] . "]]>" . "</SkuOuterID>";
                                $out = $out . "<SkuID>" . "<![CDATA[" . $row['product_id'] . "]]>" . "</SkuID>";
                                $out = $out . "<Num>" . "<![CDATA[" . $row['product_number'] . "]]>" . "</Num>";
                                $out = $out . "</Item>";
                            }
                            $out = $out . "</Items>";

                            $out = $out . "</Ware>";
                        }
                        $TotalCount = $s->sql_rows($sqlcount);
                        $out = $out . "<Result>1</Result>";
                        $out = $out . "<TotalCount>" . $TotalCount . "</TotalCount>";
                        $out = $out . "<Cause></Cause>";
                    } else {
                        $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
                        $xml = $xml . "<rsp>" . chr(13);
                        $xml = $xml . "<Result>0</Result>" . chr(13);
                        $xml = $xml . "<Cause>page 或 pagesize 参数错误</Cause>" . chr(13);
                        $xml = $xml . "</rsp>" . chr(13);
                        die($xml);
                    }
                }
            }
            $xml = "<?xml version='1.0' encoding='utf-8'?>";
            $xml = $xml . "<Goods>";
            $xml = $xml . $out;

            $xml = $xml . "</Goods>";
            echo($xml);
        } else {
            $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
            $xml = $xml . "<rsp>" . chr(13);
            $xml = $xml . "<Result>0</Result>" . chr(13);
            //$xml = $xml . "<Cause>sign error or mtype error</Cause>" . chr(13);
            $xml = $xml . "<Cause>sign error or mtype error</Cause>" . chr(13);
            $xml = $xml . "</rsp>" . chr(13);
            die($xml);
        }
    } else {
        $xml = "<?xml version='1.0' encoding='utf-8'?>" . chr(13);
        $xml = $xml . "<rsp>" . chr(13);
        $xml = $xml . "<Result>0</Result>" . chr(13);
        $xml = $xml . "<Cause>TimeStamp error</Cause>" . chr(13);
        $xml = $xml . "</rsp>" . chr(13);
        die($xml);
    }
}
?>



