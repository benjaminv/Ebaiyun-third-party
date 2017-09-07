<?php

/**
*	亿百云用户注册商城接口
*/

define('IN_ECS', TRUE);
require('../includes/init.php');
if (!isset($_GET['username']) || empty($_GET['username'])) {
	echo 0;
	exit;
}
$user_name = $_GET['username'];
if (isset($_GET['integral']) && !empty($_GET['integral'])) {
	$integral_amount = (int)$_GET['integral'];
	if ($integral_amount > 0) {
		$sql = 'select * from ' . $GLOBALS['ecs']->table('users') . ' where user_name="' . $user_name . '"';
		$user_id = $GLOBALS['db']->getOne($sql);
		log_account_change($user_id, 0, 0, $integral_amount, $integral_amount, '亿佰云代理赠送积分');
	}
}
?>