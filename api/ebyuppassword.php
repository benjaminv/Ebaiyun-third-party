<?php

/**
*	亿百云用户修改密码接口
*/

define('IN_ECS', TRUE);
require('../includes/init.php');
$data = getParam();
if ($data) {
	//先判断当期用户是否在商城，不在先写入注册
	$sql = 'SELECT user_id, user_name, password ' .
                ' FROM ' .$ecs->table('users') .
                " WHERE user_name = '" . $data['username'] . "' and mobile_phone='".$data['userphone']."'";

    $row = $db->GetRow($sql);
    if (!$row) {
    	$result = $GLOBALS['user']->add_user($data['username'], $data['password'], $data['userphone']);
    } else {
    	//修改密码
		$result = $GLOBALS['user']->edit_user(array(
			'username' => $data['username'], 'password' =>$data['password']
		));
    }
	
	if ($result) {echo 1;die;}
	echo 0;die;

}
//参数获取
function getParam() {
	$res = array();
	if (isset($_GET['username']) && !empty($_GET['username'])) {
		$res['username'] = $_GET['username'];
	}
	if (isset($_GET['password']) && !empty($_GET['password'])) {
		$res['password'] = $_GET['password'];
	}
	if (isset($_GET['userphone']) && !empty($_GET['userphone'])) {
		$res['userphone'] = $_GET['userphone'];
	}
	if (count($res) != 3) {
		return false;
	}
	return $res;
}
?>