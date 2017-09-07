<?php

/**
 * 抽奖组别管理
 * ============================================================================
 * 版权所有 2005-2009 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: wj_lottery_group.php 16881 2009-12-14 09:19:16Z liubo $
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
    $smarty->assign('ur_here',      "抽奖组别列表");
//    $smarty->assign('action_link',  array('href' => 'wj_lottery_group.php?act=add', 'text' => "添加抽奖组别"));

    $list = wj_lottery_group_list();

    $smarty->assign('wj_lottery_group_list',  $list['item']);
    $smarty->assign('filter',           $list['filter']);
    $smarty->assign('record_count',     $list['record_count']);
    $smarty->assign('page_count',       $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('wj_lottery_group_list.htm');
}

elseif ($_REQUEST['act'] == 'query')
{
    $list = wj_lottery_group_list();

    $smarty->assign('wj_lottery_group_list', $list['item']);
    $smarty->assign('filter',         $list['filter']);
    $smarty->assign('record_count',   $list['record_count']);
    $smarty->assign('page_count',     $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('wj_lottery_group_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加/编辑抽奖组别
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    /* 初始化/取得抽奖组别信息 */
    if ($_REQUEST['act'] == 'add')
    {
        $wj_lottery_group = array(
            'id'  => 0
        );
    }
    else
    {
        $wj_lottery_group_id = intval($_REQUEST['id']);
        if ($wj_lottery_group_id <= 0)
        {
            die('invalid param');
        }
        $wj_lottery_group = wj_lottery_group_info($wj_lottery_group_id);

    }
    $smarty->assign('wj_lottery_group', $wj_lottery_group);

    /* 模板赋值 */
    $smarty->assign('ur_here', "抽奖组别");
    $smarty->assign('action_link', list_link($_REQUEST['act'] == 'add'));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('wj_lottery_group_info.htm');
}

/*------------------------------------------------------ */
//-- 添加/编辑抽奖组别的提交
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] =='insert_update')
{
    /* 取得抽奖组别id */
    $wj_lottery_group_id = intval($_POST['id']);

        /* 保存信息 */

        $lg_name = $_POST['lg_name'];
		if(empty($lg_name))
		{
			sys_msg("组别名称不能为空！");
		}
		$lg_pro = $_POST['lg_pro'];
		if(empty($lg_pro))
		{
			sys_msg("中奖概率不能为空！");
		}
       

        $wj_lottery_group = array(
            'lg_name'   => $lg_name,
            'lg_remark'   => $_POST['remark'],
            'lg_pro'   => $lg_pro
        );

        /* 清除缓存 */
        clear_cache_files();

        /* 保存数据 */
        if ($wj_lottery_group_id > 0)
        {
            /* update */
            $db->autoExecute($ecs->table('lottery_group'), $wj_lottery_group, 'UPDATE', "id = '$wj_lottery_group_id'");

            /* log */
     //       admin_log(addslashes($goods_name) . '[' . $wj_lottery_group_id . ']', 'edit', 'wj_lottery_group');

            /* todo 更新活动表 */

            /* 提示信息 */
            $links = array(
                array('href' => 'wj_lottery_group.php?act=list&' . list_link_postfix(), 'text' => "返回抽奖组别列表。")
            );
            sys_msg("编辑抽奖组别成功。", 0, $links);
        }
        else
        {
            /* insert */
            $db->autoExecute($ecs->table('lottery_group'), $wj_lottery_group, 'INSERT');

            /* log */
       //     admin_log(addslashes($goods_name), 'add', 'wj_lottery_group');

            /* 提示信息 */
            $links = array(
                array('href' => 'wj_lottery_group.php?act=add', 'text' => "继续添加抽奖组别。"),
                array('href' => 'wj_lottery_group.php?act=list', 'text' => "返回抽奖组别列表。")
            );
            sys_msg("添加抽奖组别成功。", 0, $links);
        }

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

                $sql = "DELETE FROM " . $GLOBALS['ecs']->table('lottery_group') .
                        " WHERE id = '$id' LIMIT 1";
                $GLOBALS['db']->query($sql, 'SILENT');

        //        admin_log(addslashes($wj_lottery_group['goods_name']) . '[' . $id . ']', 'remove', 'wj_lottery_group');
                $del_count++;
          }

        /* 如果删除了抽奖组别，清除缓存 */
        if ($del_count > 0)
        {
            clear_cache_files();
        }

        $links[] = array('text' => "返回列表", 'href'=>'wj_lottery_group.php?act=list');
        sys_msg(sprintf("删除抽奖组别成功", $del_count), 0, $links);
    }
    else
    {
        $links[] = array('text' => $_LANG['back_list'], 'href'=>'wj_lottery_group.php?act=list');
        sys_msg("没有选择抽奖组别", 0, $links);
    }
}

elseif ($_REQUEST['act'] == 'remove')
{
//    check_authz_json('group_by');

    $id = intval($_GET['id']);


    /* 删除抽奖组别 */
    $sql = "DELETE FROM " . $ecs->table('lottery_group') . " WHERE id = '$id' LIMIT 1";
    $db->query($sql);

 //   admin_log(addslashes($wj_lottery_group['goods_name']) . '[' . $id . ']', 'remove', 'wj_lottery_group');

    clear_cache_files();

    $url = 'wj_lottery_group.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*
 * 取得抽奖组别列表
 * @return   array
 */
function wj_lottery_group_list()
{
    $result = get_filter();
    if ($result === false)
    {

        $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('lottery_group');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
	

        /* 分页大小 */
        $filter = page_and_size($filter);
		$sql = "select count(*) as c from ". $GLOBALS['ecs']->table('lottery_group');
		if($GLOBALS['db']->getOne($sql) == 0)
		{
			$sql = "insert into ". $GLOBALS['ecs']->table('lottery_group') . " (lg_name, lg_pro,type) values('一等奖',1000,1),('二等奖',100,2),('三等奖',10,3)";
			$GLOBALS['db']->query($sql);
		}

        /* 查询 */
        $sql = "SELECT * ,(select count(*) from ". $GLOBALS['ecs']->table('lottery') . " as l where l.lg=lg.id) as num ".
                "FROM " . $GLOBALS['ecs']->table('lottery_group') .
                " as lg ORDER BY $filter[sort_by] $filter[sort_order] ".
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
		$row['pro'] = floatval(1.00/$row['lg_pro'])*100;
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
function wj_lottery_group_info($id)
{
    $sql = "SELECT * ".
			" FROM " . $GLOBALS['ecs']->table('lottery_group') .
            " WHERE id = '$id' ";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 列表链接
 * @param   bool    $is_add         是否添加（插入）
 * @return  array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true)
{
    $href = 'wj_lottery_group.php?act=list';
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }

    return array('href' => $href, 'text' => "抽奖组别列表");
}

?>