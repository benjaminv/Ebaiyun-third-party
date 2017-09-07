<?php

require ("DeCode.php");
//header("location:Spider.html");
if ($_REQUEST['act'] == 'login')
    {
      
    if (file_exists("Login.xml"))//Login.xml文件存在
	{
               
		$string = file_get_contents("Login.xml");
		if (PHP_VERSION >= '5.0')
		{
                        
			$xml = new DomDocument('1.0','utf-8');
			$xml->loadXML($string);

			$user = $xml->getElementsByTagName('Spider')->item(0)->childNodes->item(0)->nodeValue;
			$pwd  = $xml->getElementsByTagName('Spider')->item(0)->childNodes->item(1)->nodeValue;
                       
                       
		}
		else
		{
			$xml = domxml_open_file($string);

			$user = $xml->get_elements_by_tagname('Spider')->get_elements_by_tagname('UserName');
			$pwd = $xml->get_elements_by_tagname('Spider')->get_elements_by_tagname('Password');
		}
		$loginname = DeCode($user,'D','differ');
		$loginpwd = DeCode($pwd,'D','differ');
                
                $name=$_REQUEST['name'];
                $pwd=$_REQUEST["pwd"];
                if($loginname == $name && $loginpwd == $pwd)
                    {

                   
                     session_start();
                     $_SESSION['name']=$name;
                      header("location:codeht.php");
                    }
                     else
                    {
                      echo ("<div style=\"padding-top:50px;text-align:center;\">" .
			  "<p style=\"margin:0 auto; text-align:left; border:1px #a1b8d8 solid; padding-left:40px; " .
			  " background:#d8e3f3 18px center; width:555px; line-height:38px; color:#4b4b4b; font-size:12px; " .
			  " font-family:Verdana;\">用户名或密码错误!  <a href=set.html>返回</a></p></div>");

                    }
                   

                
	}
        else
        {
        $user = $_REQUEST['name'];
	$pwd  = $_REQUEST['pwd'];
	$user = DeCode($user,'E','differ');
	$pwd  = DeCode($pwd,'E','differ');

	$cfg = new DomDocument('1.0','utf-8');
	$Spider = $cfg->appendChild($cfg->createElement('Spider'));
	$IntiUser = $Spider->appendChild($cfg->createElement('UserName'));
	$IntiUser->appendChild($cfg->createTextNode($user));
	$IntiPwd = $Spider->appendChild($cfg->createElement('Password'));
	$IntiPwd->appendChild($cfg->createTextNode($pwd));

	$cfg->save("Login.xml");
        

        echo ("<div style=\"padding-top:50px;text-align:center;\">" .
		  "<p style=\"margin:0 auto; text-align:left; border:1px #a1b8d8 solid; padding-left:40px; " .
		  " background:#d8e3f3 18px center; width:555px; line-height:38px; color:#4b4b4b; font-size:12px; " .
		  " font-family:Verdana;\">保存成功! (第一登录的帐号和密码将作为以后登录使用。) <a href=Spider.html>接入码设置</a></p></div>
                ");
    








        }
       
        

    }

?>