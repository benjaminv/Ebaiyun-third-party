<?php
define('IN_ECS', true);
@set_time_limit(300);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);


$gid = intval($_GET['gid']);

$ghost_count=intval($_GET['ghost_count']);

$url=$_GET['id'];
$id =  GetGoodsID($url);

$cnum = intval($_GET['cnum']);

$iscomment = 1;

if(!$id){
	$smarty->assign ( 'gid', $gid );
	$smarty->display ( 'gettaobaogoods2.html' );
}else{
	
		$json = file_get_contents_curl("https://hws.alicdn.com/cache/wdetail/5.0/?id={$id}");
		$data = json_decode($json,1);
	
	    $userNumId = $data['data']['seller']['userNumId'];

        $comment_list = getEvalution($userNumId,$id,$cnum);
	
	
	
		
			
			if($comment_list){
				$t = time();
				foreach($comment_list as $k=>$c){
					$t = $t-832*$k;
					$db->query("insert into {$ecs->table('comment')} (id_value,content,comment_rank,add_time,user_name,status) 
					value ($gid,'{$c['feedback']}',5,'{$t}','{$c['nick']}',1)");
					//createOrder($gid,$t,$c['nick']);
				}


             if($ghost_count>0){   
			$db->query("update".$ecs->table('goods')."set ghost_count='$ghost_count' where goods_id='$gid'" );
			 }
			//$link [] = array ('href' => 'goods.php?act=edit&goods_id='.$gid,'text' => '继续编辑');
			sys_msg ( '提取成功', 0, $link );
	
	
	
	}else{
		sys_msg ( '提取失败', 0, $link );
	}
}

function getImg($url) {
	global $image,$_CFG;

	//判断是否$url前面有http
	$qz = substr($url, 0, 2);
	if(strtolower($qz) == '//'){
		$url = 'https:'.$url;
	}
	
	$fileName = ROOT_PATH.'/images/taobao/';
	$arr = explode('.',$url);
	$ext = end($arr);
	$uniq = md5($url);//设置一个唯一id
	$name = $fileName.$uniq.'.'.$ext; //图像保存的名称和路径
	$img = file_get_contents_curl($url);
	file_put_contents($name,$img);
	$thumb_url = $image->make_thumb($name, $_CFG['thumb_width'],  $_CFG['thumb_height']);
	$img_url = $image->make_thumb($name , $_CFG['image_width'],  $_CFG['image_height']);
	$img_original = $image->make_thumb($name);
	$img_original = reformat_image_name('gallery', $gid, $img_original, 'source');
    $img_url = reformat_image_name('gallery', $gid, $img_url, 'goods');
	$thumb_url = reformat_image_name('gallery_thumb', $gid, $thumb_url, 'thumb');
	return array('source'=>$img_original,'goods'=>$img_url,'thumb'=>$thumb_url);
	//return $name;
}

function createOrder($gid,$t,$nick){
	$order = array();
	$order['add_time']     = $t-7*86400;//购买时间直接倒数7天
	$order['order_status'] = OS_CONFIRMED;
	$order['confirm_time'] = $t-7*86400;
	$order['pay_status']   = PS_PAYED;
	$order['pay_time']     = $t-7*86400;
	$order['shipping_status']   =2;
	$order['order_amount'] = 0;
	$order['order_sn'] = get_order_sn(); //获取新订单号
	$order['tb_nick'] = $nick;
    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order, 'INSERT');
	$new_order_id = $GLOBALS['db']->insert_id();
	$goods = $GLOBALS['db']->getRow("select * from ".$GLOBALS['ecs']->table('goods')." where goods_id=$gid");
	$sql = "INSERT INTO " . $GLOBALS['ecs']->table('order_goods') . "( " .
		"order_id, goods_id, goods_name, goods_sn, goods_number, market_price,
		goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id) value ".
		"({$new_order_id},$gid,'{$goods['goods_name']}','{$goods['goods_sn']}',1,0,0,'',0,'',0,0,0)";
    $GLOBALS['db']->query($sql);
	return true;
}
function get_order_sn(){
    mt_srand((double) microtime() * 1000000);
    return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

function getEvalution($userNumId,$id,$cnum=20)
{
    $allpage = round($cnum/20);
    if ($allpage >= 1)
    {
       	
        for ($i = 1; $i <= $allpage; $i++) 
        {   


		$reviews_url="http://rate.tmall.com/list_detail_rate.htm?itemId={$id}&spuId=0&sellerId={$userNumId}&order=1";
         $pageContents = '';
        $reviews_url  = str_replace('currentPage', '', $reviews_url);
        $reviews_url  = $reviews_url . "&currentPage=$i";
        $pageContents = file_get_contents($reviews_url);
        $pageContents = iconv('GB2312', 'UTF-8', $pageContents);
        preg_match_all('/,\"rateContent\"\:\"(.*?)\",\"/i', $pageContents, $match1);
        preg_match_all('/displayUserNick\"\:\"(.*?)\",\"/i', $pageContents, $match2);
        preg_match_all('/rateDate\"\:\"(.*?)\",\"/i', $pageContents, $match3);

       
		$a[$i]=$match1[1];
		$b[$i]=$match2[1];
		$nick=$b[1];
		$comment=$a[1];

	
		
		if($i>1){
		$comment=array_merge($comment,$a[$i]) ;
		$nick=array_merge($nick,$b[$i]) ;
		}

        }



     for($j=0;$j<count($comment);$j++){

         $comment_list[$j]['nick']=$nick[$j];
		 $comment_list[$j]['feedback']=$comment[$j];

	 }
        return  $comment_list;
    }
}



function GetGoodsID($Url)
{
    $b = (explode("&", $Url));
    foreach ($b as $v) {
        if (stristr($v, "id=")) {
            $str = $v . ">";
            ereg("id=(.*)>", $str, $c);
            $reslt = $c[1];
            return $reslt;
            break;
        }
    }
}
function Getselerdid($Url)
{
    $tmall_content =file_get_contents_curl($Url);
    ereg("sellerId:\"(.*)\",shopId:", $tmall_content, $c);
    return $c[1];
}


function file_get_contents_curl($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
	//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10); 
	$dxycontent = curl_exec($ch); 
	return $dxycontent;
}