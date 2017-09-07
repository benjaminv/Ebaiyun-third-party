<?php

/**
*	亿百云用户注册商城接口
*/

define('IN_ECS', TRUE);
require('../includes/init.php');
$data = getParam();
if ($data) {
	//写入注册
	$result = $GLOBALS['user']->add_user($data['username'], $data['password'], $data['userphone']);
	if ($result) {
		echo 1;
		// 设置推荐人
		$sql = 'select * from ' . $GLOBALS['ecs']->table('users') . ' where user_name="' . $data['username'] . '"';
		$user_id = $GLOBALS['db']->getOne($sql);
		$sql = 'select * from ' . $GLOBALS['ecs']->table('users') . ' where user_name="' . $data['parent_user'] . '"';
		$parent_id = $GLOBALS['db']->getOne($sql);
		$sql = 'UPDATE ' . $GLOBALS['ecs']->table('users') . ' SET parent_id = ' . $parent_id . ' WHERE user_id = ' . $user_id;
		$GLOBALS['db']->query($sql);
		/* 注册送积分 */
		if(! empty($GLOBALS['_CFG']['register_points']))
		{
			log_account_change($user_id, 0, 0, $GLOBALS['_CFG']['register_points'], $GLOBALS['_CFG']['register_points'], $GLOBALS['_LANG']['register_points']);
		}
		die;
	}
	echo 0;die;

}
//参数获取
function getParam() {
	$res = array();
	if (isset($_GET['username']) && !empty($_GET['username'])) {
		$res['username'] = $_GET['username'];
	}
	if (isset($_GET['userphone']) && !empty($_GET['userphone'])) {
		$res['userphone'] = $_GET['userphone'];
	}
	if (isset($_GET['password']) && !empty($_GET['password'])) {
		$res['password'] = $_GET['password'];
	}
	if (isset($_GET['parent_user']) && !empty($_GET['parent_user'])) {
		$res['parent_user'] = $_GET['parent_user'];
	}
	if (count($res) < 3) {
		return false;
	}
	return $res;
}
?>