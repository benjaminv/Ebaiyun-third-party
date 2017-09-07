<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of sqlconnect
 *
 * @author Administrator
 */

date_default_timezone_set('asia/shanghai');

			

class sql {
    /* 初始化数据库类 */
    function sql_db() {
    	define('PATH', str_replace("data/spider/sqlconnect.php", '', str_replace('\\', '/', __FILE__)));
			include(PATH . '/data/config.php');
    	$DB_SERVER = $db_host;
    	$DB_SERVER_USERNAME = $db_user;
    	$DB_SERVER_PASSWORD = $db_pass;
    	$DB_DATABASE = $db_name;

        $conn=mysql_connect($DB_SERVER,$DB_SERVER_USERNAME,$DB_SERVER_PASSWORD);
        mysql_query("set names 'utf8'");
        if (!$conn) {
            die('Could not connect: ' . mysql_error().";;".$DB_SERVER ."");
        }
        else {
            $db=mysql_select_db($DB_DATABASE,$conn);
            if($db!==true) {
                echo '';
            }
            else {
                return $db;
            }
        }
        mysql_close($conn);
    }
    function sql_result($sql) {
        $result=mysql_query($sql);
        if($result!==false) {


            return $result;


        }
        else {
            echo("");

        }
    }
    function sql_array($sql) {
        $result=mysql_query($sql);
        if($result!==false) {

            $row=mysql_fetch_array($result);
            return $row;
        }
        else {
            echo("");

        }



    }
    function sql_rows($sql) {
        $result=mysql_query($sql);
        $rows=mysql_num_rows($result);
        if($result!==false) {


            return $rows;


        }
        else {
            echo("");

        }

    }
    function sql_update($sql) {
        $result=mysql_query($sql);
        $num=0;
        $num=mysql_affected_rows();

        if($num!==-1) {
           return $num;
        }
        else {
             return $num;

        }
    }

function sql_insert($sql) {
        $result=mysql_query($sql);
       
        $num=0;
        $num=mysql_affected_rows();
        

        if($num!==-1) {
           return $num;

        }
        else {
             return $num;

        }
    }




}











?>
