<?php

/**
 * 抽奖组别管理
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
//-- 抽奖组别列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 模板赋值 */
    $smarty->assign('full_page',    1);
    $smarty->assign('ur_here',      "中奖用户列表");

    $list = wj_lottery_user_list();

    $smarty->assign('wj_lottery_user_list',  $list['item']);
    $smarty->assign('filter',           $list['filter']);
    $smarty->assign('record_count',     $list['record_count']);
    $smarty->assign('page_count',       $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('wj_lottery_user_list.htm');
}

// 参与抽奖列表
if ($_REQUEST['act'] == 'list_lu')
{
    /* 模板赋值 */
    $smarty->assign('full_page',    1);
    $smarty->assign('ur_here',      "参与抽奖用户列表");

    $l_id = intval($_REQUEST['id']);
	if ($l_id <= 0)
    {
           die('invalid param');
    }
	$list = wj_lottery_user_l_list($l_id);

    $smarty->assign('wj_lottery_user_list',  $list['item']);
    $smarty->assign('filter',           $list['filter']);
    $smarty->assign('record_count',     $list['record_count']);
    $smarty->assign('page_count',       $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('wj_lottery_user_list.htm');
}

elseif ($_REQUEST['act'] == 'query')
{
    $list = wj_lottery_user_list();

    $smarty->assign('wj_lottery_user_list', $list['item']);
    $smarty->assign('filter',         $list['filter']);
    $smarty->assign('record_count',   $list['record_count']);
    $smarty->assign('page_count',     $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('wj_lottery_user_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加/编辑抽奖组别
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit')
{

    $wj_lottery_user_id = intval($_REQUEST['id']);
    if ($wj_lottery_user_id <= 0)
    {
        die('invalid param');
    }
    $wj_lottery_user = wj_lottery_user_info($wj_lottery_user_id);

    $smarty->assign('wj_lottery_user', $wj_lottery_user);

    /* 模板赋值 */
    $smarty->assign('ur_here', "抽奖组别");
    $smarty->assign('action_link', list_link($_REQUEST['act'] == 'add'));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('wj_lottery_user_info.htm');
}

/*------------------------------------------------------ */
//-- 添加/编辑抽奖组别的提交
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] =='insert_update')
{
    $wj_lottery_user_id = intval($_POST['id']);
	if ($wj_lottery_user_id <= 0)
    {
        sys_msg("您要操作的抽奖用户不存在", 1);
    }
	if (isset($_POST['prize']))
	{
		/* 结束团购活动，修改结束时间为当前时间 */
        $sql = "UPDATE " . $ecs->table('lottery_user') .
                " SET status=1 " .
                "WHERE id = '$wj_lottery_user_id' LIMIT 1";
        $db->query($sql);

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(
            array('href' => 'wj_lottery_user.php?act=list', 'text' => "中奖用户列表")
        );
        sys_msg("领奖成功", 0, $links);
	}
	elseif (isset($_POST['noprize']))
	{
		/* 结束团购活动，修改结束时间为当前时间 */
        $sql = "UPDATE " . $ecs->table('lottery_user') .
                " SET status=0 " .
                "WHERE id = '$wj_lottery_user_id' LIMIT 1";
        $db->query($sql);

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(
            array('href' => 'wj_lottery_user.php?act=list', 'text' => "中奖用户列表")
        );
        sys_msg("设为未领奖成功", 0, $links);
	}
	else
	{
		$wj_lottery_user = array(
            'lid'   => $_POST['lid'],
            'remark'   => $_POST['remark'],
            'user_id'    => $_POST['user_id'],
			'status' => $_POST['status']
        );
        clear_cache_files();
        $db->autoExecute($ecs->table('lottery_user'), $wj_lottery_user, 'UPDATE', "id = '$wj_lottery_user_id'");
        $links = array(
                array('href' => 'wj_lottery_user.php?act=list&' . list_link_postfix(), 'text' => "返回中奖用户列表。")
            );
            sys_msg("编辑备注成功。", 0, $links);
	}
}

// 设置中奖
elseif ($_REQUEST['act'] == 'set_lottery')
{

	$wj_lottery_user_id = intval($_REQUEST['user_id']); // 参与用户id
	$wj_lottery_lid     = intval($_REQUEST['lid']); // 抽奖id
	if ($wj_lottery_user_id <= 0 || $wj_lottery_lid <= 0)
	{
		die('invalid param');
	}
	/* 结束团购活动，修改结束时间为当前时间 */
    $sql = "UPDATE " . $ecs->table('lottery_user') . " SET status=1 " .
                "WHERE user_id = '$wj_lottery_user_id' AND lid = '$wj_lottery_lid' LIMIT 1";
    $db->query($sql);

    /* 清除缓存 */
    clear_cache_files();

    /* 提示信息 */
    $links = array(
        array('href' => 'wj_lottery_user.php?act=list', 'text' => "中奖用户列表")
    );
    sys_msg("设置中奖成功", 0, $links);
}

/*------------------------------------------------------ */
//-- 批量删除抽奖组别
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch_drop')
{
    if (isset($_POST['checkboxes']))
    {
        $del_count = 0; //初始化删除数量
        foreach ($_POST['checkboxes'] AS $key => $id)
        {

                $sql = "DELETE FROM " . $GLOBALS['ecs']->table('lottery_user') .
                        " WHERE id = '$id' LIMIT 1";
                $GLOBALS['db']->query($sql, 'SILENT');

        //        admin_log(addslashes($wj_lottery_user['goods_name']) . '[' . $id . ']', 'remove', 'wj_lottery_user');
                $del_count++;
          }

        /* 如果删除了抽奖组别，清除缓存 */
        if ($del_count > 0)
        {
            clear_cache_files();
        }

        $links[] = array('text' => "返回列表", 'href'=>'wj_lottery_user.php?act=list');
        sys_msg(sprintf("删除抽奖组别成功", $del_count), 0, $links);
    }
    else
    {
        $links[] = array('text' => $_LANG['back_list'], 'href'=>'wj_lottery_user.php?act=list');
        sys_msg("没有选择抽奖组别", 0, $links);
    }
}

elseif ($_REQUEST['act'] == 'remove')
{
//    check_authz_json('group_by');

    $id = intval($_GET['id']);


    /* 删除抽奖组别 */
    $sql = "DELETE FROM " . $ecs->table('lottery_user') . " WHERE id = '$id' LIMIT 1";
    $db->query($sql);

 //   admin_log(addslashes($wj_lottery_user['goods_name']) . '[' . $id . ']', 'remove', 'wj_lottery_user');

    clear_cache_files();

    $url = 'wj_lottery_user.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*
 * 取得抽奖组别列表  -- 中奖列表
 * @return   array
 */
function wj_lottery_user_list()
{
    $result = get_filter();
    if ($result === false)
    {

        $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT COUNT(DISTINCT lid ) FROM " . $GLOBALS['ecs']->table('lottery_user');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
	

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 查询 */
        $sql = "SELECT lu.*,l.goods_id,l.shop_price,l.point,l.pro,l.winnum ".
				" ,(select goods_name from" . $GLOBALS['ecs']->table('goods') ." as g where g.goods_id=l.goods_id) as goods_name " .
				" ,(select user_name from" . $GLOBALS['ecs']->table('users') ." as u where u.user_id=lu.user_id) as user_name " .
                "FROM " . $GLOBALS['ecs']->table('lottery_user') .
				" as lu left join " .$GLOBALS['ecs']->table('lottery'). " AS l ON lu.lid = l.id WHERE lu.status = 1 ORDER BY lu.$filter[sort_by] $filter[sort_order] ".
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
		if($row['status'] == "0")
		{
		    $row['status_name'] = "未中奖";
		}
		elseif($row['status'] == "1")
		{
			$row['status_name'] = "已中奖";
		}
		$list[] = $row;
    }
    $arr = array('item' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
/**
 *获取某一抽奖活动中奖的用户列表
*/
function wj_lottery_user_l_list($lid)
{

        $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('lottery_user')." where lid='".$lid."'";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
	

        /* 分页大小 */
        $filter = page_and_size($filter);
        /* 查询 */
        $sql = "SELECT lu.*,l.goods_id,l.shop_price,l.point,l.pro,l.winnum ".
				" ,(select goods_name from" . $GLOBALS['ecs']->table('goods') ." as g where g.goods_id=l.goods_id) as goods_name " .
				" ,(select user_name from" . $GLOBALS['ecs']->table('users') ." as u where u.user_id=lu.user_id) as user_name " .
                "FROM " . $GLOBALS['ecs']->table('lottery_user') .
				" as lu left join " .$GLOBALS['ecs']->table('lottery'). " AS l ON lu.lid = l.id WHERE lu.lid = '$lid' ORDER BY lu.$filter[sort_by] $filter[sort_order] ".
                " LIMIT ". $filter['start'] .", $filter[page_size]";

    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
		$row['proper'] = floatval(1.00/$row['pro'])*100;
		if($row['status'] == "0")
		{
		    $row['status_name'] = "未中奖";
		}
		elseif($row['status'] == "1")
		{
			$row['status_name'] = "中奖";
		}
		$list[] = $row;
    }
    $arr = array('item' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 取得某商品的抽奖组别
 * @param   int     $goods_id   商品id
 * @return  array
 */
function wj_lottery_user_info($id)
{
    $sql = "SELECT  lu.*,l.goods_id,l.shop_price,l.point,l.pro,l.winnum ".
			" ,(select goods_name from" . $GLOBALS['ecs']->table('goods') ." as g where g.goods_id=l.goods_id) as goods_name " .
			" FROM " . $GLOBALS['ecs']->table('lottery_user') .
            " as lu left join " .$GLOBALS['ecs']->table('lottery'). " AS l ON lu.lid = l.id WHERE lu.id = '$id' ";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 列表链接
 * @param   bool    $is_add         是否添加（插入）
 * @return  array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true)
{
    $href = 'wj_lottery_user.php?act=list';
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }

    return array('href' => $href, 'text' => "抽奖组别列表");
}

?>