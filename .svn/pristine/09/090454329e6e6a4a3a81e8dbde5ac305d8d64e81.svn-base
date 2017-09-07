<?php

/**
 * ECSHOP 文章内容
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: article.php 17069 2010-03-26 05:28:01Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

$smarty->assign('navigator_list',        get_navigator($ctype, $catlist));  //自定义导航栏


$_REQUEST['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$article_id     = $_REQUEST['id'];
if(isset($_REQUEST['cat_id']) && $_REQUEST['cat_id'] < 0)
{
    $article_id = $db->getOne("SELECT article_id FROM " . $ecs->table('article') . " WHERE cat_id = '".intval($_REQUEST['cat_id'])."' ");
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */
$smarty->assign('ur_here',      "邀请朋友");  // 当前位置
$cache_id = sprintf('%X', crc32($_REQUEST['id'] . '-' . $_CFG['lang']));
$user_id = $_SESSION['user_id'];
$smarty->assign('userid', $user_id);
$smarty->assign('weburl', 'user.php?act=register');
$smarty->display('account.dwt');
?>