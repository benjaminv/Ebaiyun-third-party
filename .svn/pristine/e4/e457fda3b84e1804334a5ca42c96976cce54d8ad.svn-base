<?php

/**
 * wj
 * ============================================================================
 * 版权所有 2005-2009 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: wj_award_type.php 16881 2009-12-14 09:19:16Z liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');

if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'edit';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 套餐列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit')
{

    $smarty->assign('point1', wj_award_type(1));
	$smarty->assign('point2', wj_award_type(2));
	$smarty->assign('point3', wj_award_type(3));
    $smarty->assign('ur_here', "积分设置");
  
    assign_query_info();
    $smarty->display('wj_award_type.htm');
	
}

/*------------------------------------------------------ */
//-- 添加/编辑套餐的提交
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] =='insert_update')
{
    		//插入/修改套餐商品
			$sql = "delete from ". $GLOBALS['ecs']->table('wj_award_type') ;
			$GLOBALS['db']->query($sql);

			$point1 = $_POST['point1'];
			$point2 = $_POST['point2'];
			$point3 = $_POST['point3'];
			$sql = "insert into ". $GLOBALS['ecs']->table('wj_award_type'). " (type,value) values('1','".$point1."')";
			$GLOBALS['db']->query($sql);
			$sql = "insert into ". $GLOBALS['ecs']->table('wj_award_type'). " (type,value) values('2','".$point2."')";
			$GLOBALS['db']->query($sql);
			$sql = "insert into ". $GLOBALS['ecs']->table('wj_award_type'). " (type,value) values('3','".$point3."')";
			$GLOBALS['db']->query($sql);
			$links = array(
			array('href' => 'wj_award_type.php?act=edit' , 'text' => "编辑成功。")
				);
			$showmsg="积分设置。";
			sys_msg($showmsg, 0, $links);


}

function wj_award_type($type)
{
	$sql = "select value from ".$GLOBALS['ecs']->table('wj_award_type'). " where type=$type limit 1";
	if(!$GLOBALS['db']->getOne($sql))
	{
		if($type == 1)
		{
			return 50; //登录
		}
		if($type == 2)
		{
			return 50;//广告
		}
		if($type == 3)
		{
			return 5;//评论
		}
	}
    return $GLOBALS['db']->getOne($sql);

}
?>