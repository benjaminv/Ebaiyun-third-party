<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/lib_pay.php');
if($_POST['act']=='alipay'){
	alipay_pay();
}else{
	weixin_pay();
}
?>