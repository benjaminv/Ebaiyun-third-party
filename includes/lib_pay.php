<?php
function alipay_pay(){
	echo 123;die;
	$parameter = array(
		"service" => "mobile.securitypay.pay",
		"partner" => "2088521415688244",
		"seller_id" => "2088521415688244",
		"payment_type"	=> 1,
		"notify_url"	=> 'http://mall.ebaiyun.net/order_alipay.php',
		"out_trade_no"	=> 1,
		"subject"	=> '520',
		"total_fee"	=> 11,
		"body"	=> 'test',
		"_input_charset"	=> 'utf-8'
	);
	$para_filter = paraFilter($parameter);
	$para_filter=argSort($para_filter);
	$mysign_t = buildRequestMysign($para_filter,'3ig7od8pmzqi5szxs29qe9g069b27afd');
	$str=createLinkstring($para_filter)."&sign=".$mysign_t."&sign_type=MD5";
	$para_filter['sign'] = $mysign_t;
	$para_filter['sign_type'] = 'MD5';
	echo json_encode(array('data'=>$str));	
}
function weixin_pay(){
	$settings['wxapp_appId'] = 'wx70fab9881656789a';
	$configs['wxapp_pay_paySignKey'] = 'MSDFGas1d2gh11FNH1DFBn32dsfg3sSD';
	$configs['wxapp_pay_mchId'] = 1485178892;
	$package = array();
	$package['appid'] = $settings['wxapp_appId'];
	$package['mch_id'] = $configs['wxapp_pay_mchId'];
	$package['nonce_str'] = random(8);
	$package['body'] = 123123;
	$package['out_trade_no'] = random(2);
	$package['total_fee'] = 1*100;
	$package['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
	$package['notify_url'] = 'http://xly.xiarikui.cn/a.php'; 
	$package['product_id'] = 123123;
	$package['trade_type'] = 'APP';
	ksort($package, SORT_STRING);
	$string1 = '';
	foreach((array)$package as $key => $v) {
		$string1 .= "{$key}={$v}&";
	}
	$string1 .= "key=".$configs['wxapp_pay_paySignKey'];
	$package['sign'] = strtoupper(md5($string1));
	  $xml = "<xml>";  
	foreach ($package as $key=>$val)
	{
		 if (is_numeric($val))
		 {
			$xml.="<".$key.">".$val."</".$key.">"; 
		 }
		 else
			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
	}
	$xml.="</xml>"; 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/unifiedorder");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	// post提交方式
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	$data = curl_exec($ch);
	curl_close($ch);
	$string="";
	if (! empty($data)) {
		$xml = @simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
		//echo '<pre>';print_r($xml);die;
		$prepayid = $xml->prepay_id;
		$jsApiParameters = array();
		$jsApiParameters['appid'] = $settings['wxapp_appId'];
		$jsApiParameters['partnerid'] =(string)$configs['wxapp_pay_mchId'];
		$ntime = time();
		$jsApiParameters['prepayid']=(string)$prepayid;
		$jsApiParameters['package']='Sign=WXPay';
		$jsApiParameters['timestamp'] = (string)$ntime;
		$jsApiParameters['noncestr'] = random(8);
		ksort($jsApiParameters, SORT_STRING);
		foreach ($jsApiParameters as $key => $v) {
			$string .= "{$key}={$v}&";
		}
		$string .= "key=".$configs['wxapp_pay_paySignKey'];
		$jsApiParameters['sign'] = strtoupper(md5($string));
		echo json_encode(array('data'=>$jsApiParameters));	
	}else{
		return false;		
	}
}
function random($length, $nc = 0) {
	$random= rand(1, 9);
	for($index=1;$index<$length;$index++)
	{
		$random=$random.rand(1, 9);
	}
	return $random;
}
function verifyReturn($alipay_safepid,$md5){
	if(empty($_GET)) {//判断POST来的数组是否为空
		return false;
	}
	else {
		//生成签名结果
		$isSign = getSignVeryfy($_GET, $_GET["sign"],$md5);
		//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
		$responseTxt = 'true';
		if (! empty($_GET["notify_id"])) {$responseTxt = getResponse($_GET["notify_id"],$alipay_safepid);}
		//写日志记录
		//if ($isSign) {
		//	$isSignStr = 'true';
		//}
		//else {
		//	$isSignStr = 'false';
		//}
		//$log_text = "responseTxt=".$responseTxt."\n return_url_log:isSign=".$isSignStr.",";
		//$log_text = $log_text.createLinkString($_GET);
		//logResult($log_text);
		//验证
		//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
		//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
		if (preg_match("/true$/i",$responseTxt) && $isSign) {
			return true;
		} else {
			return false;
		}
	}
}
function verifyNotify($alipay_safepid,$md5){
	if(empty($_POST)) {//判断POST来的数组是否为空
		return false;
	}
	else {
		//生成签名结果
		$isSign = getSignVeryfy($_POST, $_POST["sign"],$md5);
		//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
		$responseTxt = 'true';
		if (! empty($_POST["notify_id"])) {$responseTxt = getResponse($_POST["notify_id"],$alipay_safepid);}
		//写日志记录
		//if ($isSign) {
		//	$isSignStr = 'true';
		//}
		//else {
		//	$isSignStr = 'false';
		//}
		//$log_text = "responseTxt=".$responseTxt."\n notify_url_log:isSign=".$isSignStr.",";
		//$log_text = $log_text.createLinkString($_POST);
		//logResult($log_text);
		//验证
		//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
		//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
		if (preg_match("/true$/i",$responseTxt) && $isSign) {
			return true;
		} else {
			return false;
		}
	}
}
function getResponse($notify_id,$alipay_safepid) {
	$transport = strtolower('http');
	$partner = trim($alipay_safepid);
	$veryfy_url = '';
	if($transport == 'https') {
	//	$veryfy_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	}
	else {
	}
	$veryfy_url = 'http://notify.alipay.com/trade/notify_query.do?';
	$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
	$responseTxt = http_get($veryfy_url);
	return $responseTxt;
}
function createLinkstring($para) {
	$arg  = "";
	foreach($para as $key => $v) {
		$arg .= "{$key}={$v}&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);
	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
	return $arg;
}
function paraFilter($para) {
	$para_filter = array();
	while (list ($key, $val) = each ($para)) {
		if($key == "sign" || $key == "sign_type" || $val == "")continue;
		else	$para_filter[$key] = $para[$key];
	}
	return $para_filter;
}
function argSort($para) {
	ksort($para);
	reset($para);
	return $para;
}
function getSignVeryfy($para_temp, $sign,$md5) {
	//除去待签名参数数组中的空值和签名参数
	$para_filter = paraFilter($para_temp);
	//对待签名参数数组排序
	$para_sort = argSort($para_filter);
	//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	$prestr = createLinkstring($para_sort);
	$isSgin = false;
	$isSgin = md5Verify($prestr,  $sign,$md5);
	return $isSgin;
}
/**
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * @param $key 私钥
 * return 签名结果
 */
function md5Sign($prestr, $key) {
	$prestr = $prestr . $key;
	return md5($prestr);
}
/**
 * 验证签名
 * @param $prestr 需要签名的字符串
 * @param $sign 签名结果
 * @param $key 私钥
 * return 签名结果
 */
function md5Verify($prestr, $sign, $key) {
	$prestr = $prestr . $key;
	$mysgin = md5($prestr);
	if($mysgin == $sign) {
		return true;
	}
	else {
		return false;
	}
}
function buildRequestMysign($para_sort,$md5) {
	//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	$prestr = createLinkstring($para_sort);
			$mysign = md5Sign($prestr, $md5);
	return $mysign;
}