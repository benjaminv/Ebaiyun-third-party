<?php

/**
 * 抽奖商品管理
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
//require_once(ROOT_PATH . 'includes/lib_goods.php');
//require_once(ROOT_PATH . 'includes/lib_order.php');

/* 检查权限 */
//admin_priv('group_by');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 抽奖商品列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 模板赋值 */
    $smarty->assign('full_page',    1);
    $smarty->assign('ur_here',      "抽奖商品列表");
    $smarty->assign('action_link',  array('href' => 'wj_lottery.php?act=add', 'text' => "添加抽奖商品"));

    $list = wj_lottery_list();

    $smarty->assign('wj_lottery_list',  $list['item']);
    $smarty->assign('filter',           $list['filter']);
    $smarty->assign('record_count',     $list['record_count']);
    $smarty->assign('page_count',       $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('wj_lottery_list.htm');
}
elseif ($_REQUEST['act'] == 'list_lg')
{
    /* 模板赋值 */
    $smarty->assign('full_page',    1);
    $smarty->assign('ur_here',      "抽奖商品列表");
    $smarty->assign('action_link',  array('href' => 'wj_lottery.php?act=add', 'text' => "添加抽奖商品"));

    $lg_id = intval($_REQUEST['id']);
	if ($lg_id <= 0)
    {
           die('invalid param');
    }
	$list = wj_lottery_lg_list($lg_id);

    $smarty->assign('wj_lottery_list',  $list['item']);
    $smarty->assign('filter',           $list['filter']);
    $smarty->assign('record_count',     $list['record_count']);
    $smarty->assign('page_count',       $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('wj_lottery_list.htm');
}
elseif ($_REQUEST['act'] == 'query')
{
    $list = wj_lottery_list();

    $smarty->assign('wj_lottery_list', $list['item']);
    $smarty->assign('filter',         $list['filter']);
    $smarty->assign('record_count',   $list['record_count']);
    $smarty->assign('page_count',     $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('wj_lottery_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加/编辑抽奖商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    /* 初始化/取得抽奖商品信息 */
    if ($_REQUEST['act'] == 'add')
    {
        $wj_lottery = array(
            'id'  => 0,
			'total' =>0
        );
    }
    else
    {
        $wj_lottery_id = intval($_REQUEST['id']);
        if ($wj_lottery_id <= 0)
        {
            die('invalid param');
        }
        $wj_lottery = wj_lottery_info($wj_lottery_id);
		$wj_lottery['proper'] = "(".(floatval(1.00/$wj_lottery['pro'])*100)."%)";
    }
    $smarty->assign('wj_lottery', $wj_lottery);
	

    /* 模板赋值 */
    $smarty->assign('ur_here', "抽奖商品");
    $smarty->assign('action_link', list_link($_REQUEST['act'] == 'add'));
    $smarty->assign('cat_list', cat_list());
    $smarty->assign('brand_list', get_brand_list());
//	$smarty->assign('lg_list', get_lottery_group_list());

    /* 显示模板 */
    assign_query_info();
    $smarty->display('wj_lottery_info.htm');
}

/*------------------------------------------------------ */
//-- 添加/编辑抽奖商品的提交
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] =='insert_update')
{
    /* 取得抽奖商品id */
    $wj_lottery_id = intval($_POST['id']);
    if (isset($_POST['finish']) || isset($_POST['start']) || isset($_POST['restart']))
    {
        if ($wj_lottery_id <= 0)
        {
            sys_msg("您要操作的抽奖产品不存在", 1);
        }
    }
	if (isset($_POST['finish']))
    {

        /* 结束团购活动，修改结束时间为当前时间 */
        $sql = "UPDATE " . $ecs->table('lottery') .
                " SET status=2 " .
                "WHERE id = '$wj_lottery_id' LIMIT 1";
        $db->query($sql);

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(
            array('href' => 'wj_lottery.php?act=list', 'text' => "抽奖列表")
        );
        sys_msg("成功结束抽奖商品", 0, $links);
    }
	elseif (isset($_POST['start']))
    {
		$goods_id = intval($_POST['goods_id']);
        if ($goods_id <= 0)
        {
            sys_msg("您没有选择商品！");
        }
		$id = islottery($goods_id);
		if($id)
		{
			sys_msg("该商品已加入抽奖,请选择其他商品！");
		}
        $sql = "UPDATE " . $ecs->table('lottery') .
                " SET status=1 " .
                "WHERE id = '$wj_lottery_id' LIMIT 1";
        $db->query($sql);
        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(
            array('href' => 'wj_lottery.php?act=list', 'text' => "抽奖列表")
        );
        sys_msg("成功加入抽奖商品", 0, $links);
    }
	elseif (isset($_POST['restart']))
    {

		$goods_id = intval($_POST['goods_id']);
        if ($goods_id <= 0)
        {
            sys_msg("您没有选择商品！");
        }
		$id = islottery($goods_id);
		if($id)
		{
			sys_msg("该商品已经加入抽奖,请选择其他商品！");
		}
		$total = intval($_REQUEST['total']);
		$outnum = intval($_REQUEST['outnum']);
		if($total <= $outnum)
		{
			sys_msg("该商品已经被抽奖完了,不能再重新抽奖！");
		}
        $sql = "UPDATE " . $ecs->table('lottery') .
                " SET status=1 ,total='" .$total."'".
                "WHERE id = '$wj_lottery_id' LIMIT 1";
        $db->query($sql);
        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(
            array('href' => 'wj_lottery.php?act=list', 'text' => "抽奖列表")
        );
        sys_msg("成功重新加入抽奖商品", 0, $links);
    }
	else
	{
        /* 保存信息 */
        $goods_id = intval($_POST['goods_id']);
        if ($goods_id <= 0)
        {
            sys_msg("您没有选择商品！");
        }
//		$id = islottery($goods_id);
//		if($id)
//		{
//			sys_msg("该商品已上架,请选择其他商品！");
//		}

        $point = intval($_REQUEST['point']);
		if($point<=0)
		{
			sys_msg("消耗积分应该是整数！");
		}
		$pro = intval($_REQUEST['pro']);
		if($pro<=0)
		{
			sys_msg("中奖概率应该是整数！");
		}
		$winnum = intval($_REQUEST['winnum']);
		if($winnum<=0)
		{
			sys_msg("中奖号码应该在[1,".$pro."]之间！");
		}
		if($winnum < 1 || $winnum > $pro)
		{
			sys_msg("中奖号码应该在[1,".$pro."]之间！");
		}
		$total = intval($_REQUEST['total']);
		if($total<=0)
		{
			sys_msg("抽奖数量应该大于1！");
		}
		$outnum = intval($_REQUEST['outnum']);
		$lg_id = intval($_REQUEST['lg_id']);
		if($lg_id<=0)
		{
			sys_msg("请先选择抽奖组别！");
		}
		$status = intval($_POST['status']);
		if($status == 1)
		{
		    sys_msg("商品正在进行抽奖,不能修改,请先结束抽奖！");
		}
		$shop_price = $_POST['shop_price'];
		if(empty($shop_price))
		{
			sys_msg("商品价格不能为空！");
		}
        $wj_lottery = array(
            'point'   => $point,
            'pro'   => $_POST['pro'],
            'remark'   => $_POST['remark'],
            'goods_id'   => $goods_id,
            'shop_price'    => $shop_price,
        	'begin' => 0, // 开始
        	'end' => strtotime($_POST['end']), // 结束
			'lg' => $lg_id,
			'status' => $status,
			'winnum'=>$winnum,
			'total' =>$total,
			'outnum' =>$outnum
        );

        /* 清除缓存 */
        clear_cache_files();


        /* 保存数据 */
        if ($wj_lottery_id > 0)
        {
            /* update */
            $db->autoExecute($ecs->table('lottery'), $wj_lottery, 'UPDATE', "id = '$wj_lottery_id'");

            /* log */
     //       admin_log(addslashes($goods_name) . '[' . $wj_lottery_id . ']', 'edit', 'wj_lottery');

            /* todo 更新活动表 */

            /* 提示信息 */
            $links = array(
                array('href' => 'wj_lottery.php?act=list&' . list_link_postfix(), 'text' => "返回抽奖商品列表。")
            );
            sys_msg("编辑抽奖商品成功。", 0, $links);
        }
        else
        {
            /* insert */
            $db->autoExecute($ecs->table('lottery'), $wj_lottery, 'INSERT');

            /* log */
       //     admin_log(addslashes($goods_name), 'add', 'wj_lottery');

            /* 提示信息 */
            $links = array(
                array('href' => 'wj_lottery.php?act=add', 'text' => "继续添加抽奖商品。"),
                array('href' => 'wj_lottery.php?act=list', 'text' => "返回抽奖商品列表。")
            );
            sys_msg("添加抽奖商品成功。", 0, $links);
        }
	}
}

/*------------------------------------------------------ */
//-- 批量删除抽奖商品
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch_drop')
{
    if (isset($_POST['checkboxes']))
    {
        $del_count = 0; //初始化删除数量
        foreach ($_POST['checkboxes'] AS $key => $id)
        {

                $sql = "DELETE FROM " . $GLOBALS['ecs']->table('lottery') .
                        " WHERE id = '$id' LIMIT 1";
                $GLOBALS['db']->query($sql, 'SILENT');

        //        admin_log(addslashes($wj_lottery['goods_name']) . '[' . $id . ']', 'remove', 'wj_lottery');
                $del_count++;
          }

        /* 如果删除了抽奖商品，清除缓存 */
        if ($del_count > 0)
        {
            clear_cache_files();
        }

        $links[] = array('text' => "返回列表", 'href'=>'wj_lottery.php?act=list');
        sys_msg(sprintf("删除抽奖商品成功", $del_count), 0, $links);
    }
    else
    {
        $links[] = array('text' => $_LANG['back_list'], 'href'=>'wj_lottery.php?act=list');
        sys_msg("没有选择抽奖商品", 0, $links);
    }
}

/*------------------------------------------------------ */
//-- 搜索商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'search_goods')
{
  //  check_authz_json('group_by');

    include_once(ROOT_PATH . 'includes/cls_json.php');

    $json   = new JSON;
    $filter = $json->decode($_GET['JSON']);
    $arr    = get_goods_list($filter);

    make_json_result($arr);
}

/*------------------------------------------------------ */
//-- 搜索组别
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'search_lg')
{
  //  check_authz_json('group_by');

    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json   = new JSON;
    $arr    = get_lottery_group_list();

    make_json_result($arr);
}

/*------------------------------------------------------ */
//-- 删除抽奖商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
//    check_authz_json('group_by');

    $id = intval($_GET['id']);


    /* 删除抽奖商品 */
    $sql = "DELETE FROM " . $ecs->table('lottery') . " WHERE id = '$id' LIMIT 1";
    $db->query($sql);

 //   admin_log(addslashes($wj_lottery['goods_name']) . '[' . $id . ']', 'remove', 'wj_lottery');

    clear_cache_files();

    $url = 'wj_lottery.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}


function wj_lottery_lg_list($lg_id)
{
 
        $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('lottery') . " where lg='". $lg_id ."'";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
		/* 分页大小 */
        $filter = page_and_size($filter);
	
	$sql = "select * ".
	" ,(select goods_name from" . $GLOBALS['ecs']->table('goods') ." as c where c.goods_id=tg.goods_id) as goods_name " .
	" ,(select lg_name from ". $GLOBALS['ecs']->table('lottery_group') . " as lg where lg.id=tg.lg) as lg_name ".
	"from " . $GLOBALS['ecs']->table('lottery') . "  as tg where lg='".$lg_id."' ORDER BY $filter[sort_by] $filter[sort_order] ".
                " LIMIT ". $filter['start'] .", $filter[page_size]";
	$res = $GLOBALS['db']->query($sql);
    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
		$row['proper'] = floatval(1.00/$row['pro'])*100;
		if($row['status'] == "0")
		{
		    $row['status_name'] = "未开始";
		}
		elseif($row['status'] == "1")
		{
			$row['status_name'] = "正在进行";
		}
		else
		{
		    $row['status_name'] = "已结束";
		}
		$list[] = $row;
    }
    $arr = array('item' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
/*
 * 取得抽奖商品列表
 * @return   array
 */
function wj_lottery_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keyword']      = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if ($_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

		$where = (!empty($filter['keyword'])) ? " where goods_id in ( select goods_id from ".$GLOBALS['ecs']->table('goods')." where goods_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%')" : '';

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('lottery') .
                " $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
	

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 查询 */
        $sql = "SELECT * ".
		        " ,(select goods_name from" . $GLOBALS['ecs']->table('goods') ." as c where c.goods_id=tg.goods_id) as goods_name " .
				" ,(select lg_name from ". $GLOBALS['ecs']->table('lottery_group') . " as lg where lg.id=tg.lg) as lg_name ".
                "FROM " . $GLOBALS['ecs']->table('lottery') .
                " as tg $where ".
                " ORDER BY $filter[sort_by] $filter[sort_order] ".
                " LIMIT ". $filter['start'] .", $filter[page_size]";

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
		$row['proper'] = floatval(1.00/$row['pro'])*100;
		$row['remain'] = $row['total'] - $row['outnum'];
		if($row['status'] == "0")
		{
		    $row['status_name'] = "未开始";
		}
		elseif($row['status'] == "1")
		{
			$row['status_name'] = "正在进行";
		}
		else
		{
		    $row['status_name'] = "已结束";
		}
		$list[] = $row;
    }
    $arr = array('item' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 取得某商品的抽奖商品
 * @param   int     $goods_id   商品id
 * @return  array
 */
function wj_lottery_info($id)
{
    $sql = "SELECT ag.*,(select goods_name from ".$GLOBALS['ecs']->table('goods')." as g where g.goods_id=ag.goods_id) as goods_name ".
			" ,(select lg_name from ". $GLOBALS['ecs']->table('lottery_group') . " as lg where lg.id=ag.lg) as lg_name ".
			" FROM " . $GLOBALS['ecs']->table('lottery') .
            " ag WHERE ag.id = '$id' ";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 列表链接
 * @param   bool    $is_add         是否添加（插入）
 * @return  array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true)
{
    $href = 'wj_lottery.php?act=list';
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }

    return array('href' => $href, 'text' => "抽奖商品列表");
}

/**
 * 返回商品原价
 */
function getGoodsShopPirce($goods_id)
{
    $sql = "select shop_price from ". $GLOBALS['ecs']->table('goods') . " where goods_id='" . $goods_id ."'";
	$row = $GLOBALS['db']->getRow($sql);
	return $row['shop_price'];
	
}

//判断该商品是否已上架
function islottery($goods_id)
{
	$sql = "select id from ".$GLOBALS['ecs']->table('lottery')." where goods_id='".$goods_id."' and status=1";
	return  $GLOBALS['db']->getOne($sql);
}
function get_lottery_group_list()
{
    $sql = 'SELECT id,lg_name,lg_pro FROM ' . $GLOBALS['ecs']->table('lottery_group') . ' ORDER BY id';
    $res = $GLOBALS['db']->getAll($sql);

    $lottery_group_list = array();
    foreach ($res AS $row)
    {
        $lottery_group_list[] = $row;
    }
    return $lottery_group_list;
}

?>