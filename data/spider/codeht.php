<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=GB2312">
        <title></title>
    </head>
    <body>
        
        <?php
        require ("DeCode.php");
        
         if(!isset($_SESSION['name']))
             {
             Header("Location:set.html");
             }

        else {


             //$code=$_REQUEST['code'];
             if (!file_exists("code.xml"))
                 {
                   $code = $_REQUEST['code'];
                   $code = encrypt("differ",$code);
                   $cfg = new DomDocument('1.0','utf-8');
                   $Spider = $cfg->appendChild($cfg->createElement('Spider'));
                   $IntiUser = $Spider->appendChild($cfg->createElement('code'));
	           $IntiUser->appendChild($cfg->createTextNode($code));
                   $cfg->save("code.xml");

                 }
                 elseif (file_exists("code.xml"))
                     {
                     
                        $string = file_get_contents("code.xml");
		        if (PHP_VERSION >= '5.0')
		{
			$xml = new DomDocument('1.0','utf-8');
			$xml->loadXML($string);

			$code = $xml->getElementsByTagName('Spider')->item(0)->childNodes->item(0)->nodeValue;

		}
		else
		{
			$xml = domxml_open_file($string);

			$code = $xml->get_elements_by_tagname('Spider')->get_elements_by_tagname('code');

		}
                if($code)
                    {
                   $code=decrypt("differ", $code);
                    echo("您已设置接入码，接入码是：".$code."</br>"."修改请在下面输入，不修改请关闭本页");

                    }
		
		
                     }

        }

        
        ?>
        <form name="form1" method="post" action="differcode.php">
          <label>接入码：
            <input type="text" name="code">
          </label>
          <p>
            <label>
            <input type="submit" name="Submit" value="提交">
            </label>
          </p>
        </form>

    </body>
</html>
