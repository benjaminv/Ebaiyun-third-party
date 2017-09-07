<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

if (empty($_REQUEST['act']))
{
    $act = 'list';
}
else
{
	$act=$_REQUEST['act'];
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */
$cache_id = $_CFG['lang'] . '-' . $act . '-' . $_SESSION['user_rank'];
$cache_id = sprintf('%X', crc32($cache_id));
if ($act == 'list')
{
	assign_template();
//	if (!$smarty->is_cached('lottery.dwt', $cache_id))
	{
		$smarty->assign('page_title',   "活动专区");    // 页面标题
   		$smarty->assign('ur_here',      "活动专区");  // 当前位置
		$smarty->assign('user_id', "-1");
		$smarty->assign('totalwin',     gettotallu());  // 当前位置
		if ($_SESSION['user_id'] > 0)
    	{
            $sql = "SELECT is_validated,email FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id='".$_SESSION['user_id'] ."'";
            $row = $GLOBALS['db']->getRow($sql);
            $smarty->assign('hash', $row['is_validated']);
			
			$email_name = substr($row['email'],strpos($row['email'],'@')+1);
			if($email_name == 'gmail.com')
			{
				$email_url  = 'http://gmail.com';
			}
			else if($email_name == 'hotmail.com')
			{
				$email_url  = 'http://www.hotmail.com';
			}
			else
			{
				$email_url  = 'http://mail.'. $email_name;
			}
			$smarty->assign('email_url', $email_url);

        	$smarty->assign('user_info', get_user_info());
			$smarty->assign('user_id', $_SESSION['user_id']);
			$smarty->assign('user_name', $_SESSION['user_name']);
			$smarty->assign('credit', getusercredit($_SESSION['user_id']));
			$sql="select * from ".$GLOBALS['ecs']->table('lottery_user')." where applystatus=0 and user_id='".$_SESSION['user_id']."' limit 1";
			$row = $GLOBALS['db']->getRow($sql);
			if($row)
			{
				$smarty->assign('fetch_rewards', "lottery.php?act=fetch_rewards&amp;id=".$row['id']);
			}
    	}		
		$smarty->assign('lotterylist1',  getlotterylist());
/* 		$smarty->assign('lotterylist2',  getlotterylist(2));
		$smarty->assign('lotterylist3',  getlotterylist(3)); */	
		$smarty->assign('winlist',  	getwinlist());	
		$smarty->assign('helps',           get_shop_help());       // 网店帮助
		    /* links */
    $links = index_get_links();
    $smarty->assign('img_links',       $links['img']);
    $smarty->assign('txt_links',       $links['txt']);
    $smarty->assign('data_dir',        DATA_DIR);       // 数据目录
	}
	$smarty->assign('nav', '5');
	$smarty->display('lottery.dwt');//, $cache_id);

}
elseif ($_REQUEST['act'] == 'try')
{	
	require(ROOT_PATH . 'includes/cls_json.php');
	$json   = new JSON;
	$result = array('error' => 0, 'message' => '', 'content' => '', 'id' => 0, 'credit' => 0,'win'=>0);
	$_REQUEST['cmt'] = json_str_iconv($_REQUEST['cmt']);
	$cmt  = $json->decode($_REQUEST['cmt']);
	$id = $cmt->id;
	$pro = $cmt->pro;
	$winnum = $cmt->winnum;
	$user_id = $cmt->user_id;
	$point = $cmt->point;
	
	if($pro <= 0 || $winnum <= 0 || $winnum > $pro || $user_id < 1)
	{
	    $result['error']   = 1;
        $result['message'] = "服务器感到很大压力,出问题了.";
	}
	else
	{
		//检测用户是否拥有足够的积分
		if(checkuserpoint($user_id, $point) == "-1")
		{
		    $result['error']   = -1;
			$content = "<br/><br/><br/><font size=\"+5\" color=\"#0033CC\">你的积分不足拉,快去挣取积分吧...</font><br/><br/><br/><br/>";
		}
		else
		{
			srand(seed());
			$randnum = rand(1, $pro);			
			if($randnum == $winnum)//中奖了
			{	
				//先检查库存,再减少库存
				$num = ReduceNumber($id, $user_id);
				if($num == "0")
				{
					$randnum = $randnum + 1;
					//$content = "你的号码:".$randnum."<br/>";
					$result['error']   = 1;
					$content = "没有中奖唉~";
				}
				else
				{
					//$content = "你的号码:".$randnum."<br/>";
					$result['error']   = 0;	
					$result['wid']   = $num;		
					$content = "恭喜您中奖了!!写下您获奖感言索取奖品吧..<br/>
					";
				}
				clear_cache_files('lottery');
			}
			else
			{
				//$content = "你的号码:".$randnum."<br/>";
				$result['error']   = 1;
				$content = "没有中奖唉~";
			}
			$content .="<br/>";
			$sql = "update ".$GLOBALS['ecs']->table('lottery')." set click=click+1 where id='".$id."'";
			$GLOBALS['db']->query($sql);
		}
		$result['credit'] = getusercredit($user_id);
	//	$result['content'] = $content;
		$result['message'] = $content;
		$result['id'] = $id;
	}
	die($json->encode($result));
}
elseif ($_REQUEST['act'] == 'view')
{	
	$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);
	$size = 10;
	$record_count = intval(gettotallu());
	$page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;
	if ($page < 1)
    {
        $page = 1;
    }
	if ($page > $page_count)
    {
        $page = $page_count;
    }
	$pager = get_pager('lottery.php', array('act'=>'view'), $record_count, $page, $size);
	$smarty->assign('pager', $pager);
	assign_template();
	$smarty->assign('page_title',   "活动专区免费大奖等你拿");    // 页面标题
   	$smarty->assign('ur_here',      "获奖名单");  // 当前位置
	$smarty->assign('winlist',  	getwinlist($size, $page));

	$smarty->display('lottery_winlist.dwt');
}
elseif ($_REQUEST['act'] == 'fetch_rewards')
{	
	$page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);
	$size = 10;
	$record_count = intval(gettotallu());
	$page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;
	if ($page < 1)
    {
        $page = 1;
    }
	if ($page > $page_count)
    {
        $page = $page_count;
    }
	$pager = get_pager('lottery.php', array('act'=>'view'), $record_count, $page, $size);
	$smarty->assign('pager', $pager);
	assign_template();
	$smarty->assign('page_title',   "活动专区-免费大奖等你拿");    // 页面标题
   	$smarty->assign('ur_here',      "提交获奖感言");  // 当前位置
	$smarty->assign('winlist',  	getwinlist($size, $page));
	
	$id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);	
	if($id > 0)
	{
		$user_id = $_SESSION['user_id'];
		$sql="select * from ".$GLOBALS['ecs']->table('lottery_user')." where id='$id' and user_id='$user_id' and applystatus=0";
		$row = $GLOBALS['db']->getRow($sql);
		if($row)
		{
		//	if($row['speech'] == "" || $row['speech'] == "无" || $row['address']=="")
			{
			//	if($row['address']=="")
				{
					$sql = "select lu.*,l.shop_price,l.goods_id,(select goods_name from ".$GLOBALS['ecs']->table('goods')." as g where g.goods_id=l.goods_id) as goods_name ".
						",(select goods_thumb from ".$GLOBALS['ecs']->table('goods')." as g where g.goods_id=l.goods_id) as goods_thumb ".
						",(select user_name from ".$GLOBALS['ecs']->table('users')." as u where u.user_id=lu.user_id) as user_name ".
						" from ".$GLOBALS['ecs']->table('lottery_user') . "as lu left join " .$GLOBALS['ecs']->table('lottery') . " as l on l.id=lu.lid where lu.id='$id' limit 1";
					$row = $GLOBALS['db']->getRow($sql);
					if($row)
					{
						$smarty->assign('goods_name',      $row['goods_name']);
						$smarty->assign('goods_thumb',     get_image_path($row['goods_id'], $row['goods_thumb'], true));
						$smarty->assign('shop_price',      $row['shop_price']);
						$smarty->assign('goods_id',        $row['goods_id']);
						$smarty->assign('speech',      "1");
						$smarty->assign('id',      $id);
						$smarty->assign('goods_url',      build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']));
						
						/* 取得国家列表、商店所在国家、商店所在国家的省列表 */
						$smarty->assign('country_list',       get_regions());
						$smarty->assign('province_list',      get_regions(1, 1));
						$smarty->assign('city_list',          get_regions(2, 0));
						$smarty->assign('district_list',      get_regions(3, 0));
					}
				}
			}
		}
	}		

	$smarty->display('lottery_winlist.dwt');
}
elseif ($_REQUEST['act'] == 'submitspeech')
{
    require(ROOT_PATH . 'includes/cls_json.php');
	$json   = new JSON;
	$result = array('error' => 0, 'message' => '', 'content' => '提交失败1.0！');
	$_REQUEST['cmt'] = json_str_iconv($_REQUEST['cmt']);
	$cmt  = $json->decode($_REQUEST['cmt']);

	$user_id = $_SESSION['user_id'];
	$id       = $cmt->id;

	$sql="select applystatus from ".$GLOBALS['ecs']->table('lottery_user')." where id=".$id;
	$applystatus = $GLOBALS['db']->getOne($sql);	
	if($applystatus == 1)
	{
		$result = array('error' => 0, 'message' => '', 'content' => '已提交，请勿重复提交！');
	}
	else
	{
		if(submitmyspeech($cmt, $user_id))
		{
			$result = array('error' => 1, 'message' => '', 'content' => '提交成功！请到“我的订单”查询中奖产品.');
		}
		else
		{
			$result = array('error' => 0, 'message' => '', 'content' => '提交失败！');
		}
	}

	die($json->encode($result));
}


/**
 *获取抽奖商品
 *$lg 所在组别
*/
function getlotterylist($type = 0)
{
	if($type > 0)
	{
	    $sql = "SELECT l.*,(select goods_name from ".$GLOBALS['ecs']->table('goods')." as g where g.goods_id=l.goods_id) as goods_name ".
			",(select goods_thumb from ".$GLOBALS['ecs']->table('goods')." as g where g.goods_id=l.goods_id) as goods_thumb ".
			"FROM ".$GLOBALS['ecs']->table('lottery'). " as l left join ".$GLOBALS['ecs']->table('lottery_group')." as lg on l.lg=lg.id where l.status=1 and  (l.total-l.outnum)>0 and lg.type='".$type."'";
	}
	else
	{
	    $sql = "SELECT l.*,(select goods_name from ".$GLOBALS['ecs']->table('goods')." as g where g.goods_id=l.goods_id) as goods_name ".
			",(select goods_thumb from ".$GLOBALS['ecs']->table('goods')." as g where g.goods_id=l.goods_id) as goods_thumb ".
			"FROM ".$GLOBALS['ecs']->table('lottery'). " as l left join ".$GLOBALS['ecs']->table('lottery_group')." as lg on l.lg=lg.id  where l.status=1 and  (l.total-l.outnum)>0";
	}
	$res = $GLOBALS['db']->getAll($sql);
    $lottery_list = array();
    foreach ($res AS $row)
    {
        $row['shop_price']   = price_format($row['shop_price']);
		$row['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
		$row['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
		$row['proper'] = round(floatval(1.00/$row['pro'])*100,3);
		$lottery_list[] = $row;
    }
    return $lottery_list;
}
/**
 *获取中奖列表
*/
function getwinlist($size = 3, $page = 1)
{
	$sql = "select lu.*,l.goods_id,l.shop_price,(select goods_name from ".$GLOBALS['ecs']->table('goods')." as g where g.goods_id=l.goods_id) as goods_name ".
		",(select goods_thumb from ".$GLOBALS['ecs']->table('goods')." as g where g.goods_id=l.goods_id) as goods_thumb ".
		",(select user_name from ".$GLOBALS['ecs']->table('users')." as u where u.user_id=lu.user_id) as user_name ".
		" from ".$GLOBALS['ecs']->table('lottery_user') . "as lu left join " .$GLOBALS['ecs']->table('lottery') . " as l on l.id=lu.lid where lu.status=1 order by lu.id desc ";
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    $win_list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
		$row['wintime'] = local_date("Y-m-d H:i:s", $row['wintime']);
		$row['gettime'] = local_date("Y-m-d H:i:s", $row['gettime']);
		$row['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
		$row['speech'] = $row['speech'] == "" ? "无" : $row['speech'];
		$win_list[] = $row;
	}
	return $win_list;
}
/**
 *支付后，减少库存,加入参与抽奖列表,返回0表示抽奖完毕了  -- 支付后回调此方法 -- aix
*/
function ReduceNumber($id, $user_id)
{
	//先检查库存是否正确
	$sql = "select 1 from ".$GLOBALS['ecs']->table('lottery')." where id='".$id."' and (total-outnum)>0 and status=1";
	$num = $GLOBALS['db']->getOne($sql);
	if($num)
	{
		$sql = "update ".$GLOBALS['ecs']->table('lottery')." set outnum=outnum+1 where id='".$id."'";
		$GLOBALS['db']->query($sql); // 更新参与人数
		$sql = "insert into ".$GLOBALS['ecs']->table('lottery_user')." (lid, user_id, status, remark,speech,wintime) values('".$id."','".$user_id."','0','','无','".local_strtotime(date("Y-m-d H:i:s", time()+8*3600))."')";
		$GLOBALS['db']->query($sql); // 新增参与用户记录
		$id = $GLOBALS['db']->insert_id();
		//是否还有抽奖数量
		$sql = "select 1 from ".$GLOBALS['ecs']->table('lottery')." where id='".$id."' and total=outnum";
		$num = $GLOBALS['db']->getOne($sql);
		if($num)
		{
			$sql = "update ".$GLOBALS['ecs']->table('lottery')." set status=2 where id='".$id."'";
			$GLOBALS['db']->query($sql);
		}
		return $id;
	}
	else
	{
		return "0";
	}
	
}
/**
 *检测用户是否拥有足够的积分
*/
function checkuserpoint($user_id, $point)
{
	$sql = "select 1 from ".$GLOBALS['ecs']->table('users')." where user_id='$user_id' and pay_points>=$point";
	$num = $GLOBALS['db']->getOne($sql);
	if($num)
	{	
		//足够的话要减少积分
		$sql = "update ".$GLOBALS['ecs']->table('users')." set pay_points=pay_points-$point where user_id='$user_id'";
		$GLOBALS['db']->query($sql);
		return "1";
	}
	return "-1";
}
/**
 *获取用户拥有的积分
*/
function getusercredit($user_id)
{
	$sql = "select pay_points from ".$GLOBALS['ecs']->table('users')." where user_id='$user_id'";
	return $GLOBALS['db']->getOne($sql);
}
/**
 *获取中奖用户总数
*/
function gettotallu()
{
	$sql = "select count(*) from ".$GLOBALS['ecs']->table('lottery_user');
	return $GLOBALS['db']->getOne($sql);
}
/**
 *提交获奖感言
*/
function submitmyspeech($cmt, $user_id)
{
	require_once(ROOT_PATH . 'includes/lib_order.php');
	
	$id       = $cmt->id;
	$speech   = $cmt->speech;
	$address  = $cmt->address;
	$tel      = $cmt->tel;
	$name     = $cmt->name;
	$province = $cmt->province;
	$city     = $cmt->city;
	$district = $cmt->district;
	$email    = $cmt->email;
	$goods_id = $cmt->goods_id;
	
	//下订单
	$order_sn = get_order_sn();
	$shipping_id = 8;
	$shipping_name = "圆通快递";
	$shipping_fee = 0.00;
	$order_amount = $shipping_fee;
	$pay_id = 1;
	$pay_name = "余额支付";
	$sql = 'INSERT INTO '.$GLOBALS['ecs']->table('order_info').' (`order_sn`, `user_id`,`consignee`, `country`, `province`,`city`, `district`, `address`,  '.
		   '`tel`, `email`,`shipping_id`, `shipping_name`,`pay_id`, `pay_name`, `how_oos`, `goods_amount`,'.
		   ' `shipping_fee`,`order_amount`,`referer`, `add_time`) VALUES ("'.
			$order_sn.'",'.$user_id.',"'.$name.'",1,'.$province.','.$city.','.$district.',"'.$address.'","'.$tel.'","'.$email.'",'.$shipping_id.',"'.$shipping_name.'",'.
		   $pay_id.',"'.$pay_name.'","等待所有商品备齐后再发",0.00,'.$shipping_fee.','.$order_amount.',"本站",'.gmtime().')';
	$GLOBALS['db']->query($sql);
	
	$sql = 'select max(order_id) from '.$GLOBALS['ecs']->table('order_info');
	$order_id = $GLOBALS['db']->getOne($sql);
		
	$sql = 'select * from '.$GLOBALS['ecs']->table('goods').' where goods_id = '.$goods_id;
	$goods = $GLOBALS['db']->getRow($sql); //获奖商品信息
		
	$sql = 'INSERT INTO '.$GLOBALS['ecs']->table('order_goods').' (`order_id`, `goods_id`, `goods_name`, `goods_sn`, `market_price`, `goods_price`, `is_real`) VALUES ('.
			   $order_id.','.$goods_id.',"'.$goods['goods_name'].'（抽奖获奖品）","'.$goods['goods_sn'].'",'.$goods['market_price'].',0.00,1)';
	$GLOBALS['db']->query($sql);
	
	$province = get_region_name($province);
	$city     = get_region_name($city);
	$district = get_region_name($district);
	$address  = $name.','.$address.','.$email;
	$address1 = $province.$city.$district;
	//修改获奖状态为已申请
	$sql = "update ".$GLOBALS['ecs']->table('lottery_user').
	" set speech='$speech', address1='$address1',address='$address', tel='$tel' ,order_id = '$order_id',gettime='".gmtime()."',ip = '".real_ip().
	"',applystatus=1 where id='$id' and user_id='$user_id'";
	$GLOBALS['db']->query($sql);
	
	return "1";
}
//seed用户自定义函数以微秒作为种子
function seed() 
{
list($msec, $sec) = explode(' ', microtime());
return (float) $sec;
}

function index_get_links()
{
    $sql = 'SELECT link_logo, link_name, link_url FROM ' . $GLOBALS['ecs']->table('friend_link') . ' ORDER BY show_order';
    $res = $GLOBALS['db']->getAll($sql);

    $links['img'] = $links['txt'] = array();

    foreach ($res AS $row)
    {
        if (!empty($row['link_logo']))
        {
            $links['img'][] = array('name' => $row['link_name'],
                                    'url'  => $row['link_url'],
                                    'logo' => $row['link_logo']);
        }
        else
        {
            $links['txt'][] = array('name' => $row['link_name'],
                                    'url'  => $row['link_url']);
        }
    }

    return $links;
}

?>
