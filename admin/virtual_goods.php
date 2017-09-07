<?php

/**
 * ECSHOP 虚拟团购管理程序
 * ============================================================================
 * * 版权所有 2005-2012 热风科技，并保留所有权利。
 * 演示地址: http://palenggege.com  开发QQ:497401495    paleng
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: sunlizhi $
 * $Id: virtual_goods.php 17217 2015-7-20 06:29:08Z sunlizhi $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
require_once(ROOT_PATH . '/includes/lib_virtual_goods.php');
$image = new cls_image($_CFG['bgcolor']);
$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');


/* 显示商圈列表 */
if($_REQUEST['act'] == 'district'){
        $smarty->assign('full_page',    1);
	$list  = get_district_list();
	$city = get_city_list();
	$smarty->assign('ur_here', $_LANG['supplier_district_manage']);
        $smarty->assign('action_link',      array('text'=>$_LANG['add_district'], 'href'=>'virtual_goods.php?act=add_district'));
	$smarty -> assign('city' ,$city);
        $smarty->assign('district_list',    $list['item']);
        $smarty->assign('filter',       $list['filter']);
        $smarty->assign('record_count', $list['record_count']);
        $smarty->assign('page_count',   $list['page_count']);
	$smarty -> display('virtual_goods_district.htm');
}

/* 查询商圈 */
if ($_REQUEST['act'] == 'query_district')
{
    $list = get_district_list();

    $smarty->assign('district_list',    $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('virtual_goods_district.htm'), '',
        array('filter'=>$list['filter'], 'page_count'=>$list['page_count']));
}

/* 修改商圈模板赋值 */
if($_REQUEST['act'] == 'edit_district' || $_REQUEST['act'] == 'add_district'){
   $district_id = empty($_REQUEST['district_id'])? 0 : intval($_REQUEST['district_id']);
   $region = get_region_list();
   $smarty-> assign('region',$region);
   $sql = "select a.district_id, a.district_name,a.province,a.city,a.county,a.sort,a.is_show, b.region_name as city_name, c.region_name as county_name from " .$GLOBALS['ecs']->table('virtual_goods_district').
           " as a left join ".$GLOBALS['ecs']->table('region')." as b on b.region_id = a.city".
           " left join ".$GLOBALS['ecs']->table('region')." as c on c.region_id = a.county  where district_id = $district_id";
   $district=  $GLOBALS['db'] -> getRow($sql);
   
   $smarty -> assign('district',$district);
   $smarty -> assign('district_id',$district_id);
   $smarty -> display('virtual_district_info.htm');
}


/* 添加商圈模板赋值 */
if($_REQUEST['act'] == 'insert_district'){
    $district_id = empty($_REQUEST['district_id'])? 0 : intval($_REQUEST['district_id']);
    $province = empty($_REQUEST['add_province'])? '' : intval($_REQUEST['add_province']);
    $city = empty($_REQUEST['add_city'])? '' : intval($_REQUEST['add_city']);
    $county = empty($_REQUEST['add_county']) ? '' : intval($_REQUEST['add_county']);
    $is_show = empty($_REQUEST['is_show'])? '' : intval($_REQUEST['is_show']);
    $district_name = empty($_REQUEST['district_name']) ? '' : trim($_REQUEST['district_name']);
    $sort = empty($_REQUEST['sort'])? '1' : inval($_REQUEST['$sort']);
    if(!$district_id) {
      $sql = "INSERT INTO ".$GLOBALS['ecs']->table('virtual_goods_district')." (district_name, province, city,county,sort,is_show)  VALUES ('$district_name','$province','$city','$county','$sort','$is_show') ";
    }else{
      $sql = "UPDATE ".$GLOBALS['ecs']->table('virtual_goods_district')."  set district_name = '$district_name', province = '$province',city ='$city',county='$county', sort='$sort',is_show='$is_show' where district_id = $district_id";
    }
    $GLOBALS['db'] -> query($sql);
    
    if ($GLOBALS['db'] ->errno() == 0)
    {
         $link[0] = array('text' => $_LANG['continue_add_district'] , 'href' => 'virtual_goods.php?act=add_district');
         $link[1] = array('text' => $_LANG['back_district_list'], 'href' => 'virtual_goods.php?act=district');
	sys_msg($district_id?$_LANG['mes_edit_district_success']:$_LANG['mes_add_district_success'], 0, $link);
    }
    else
    {
        make_json_error($GLOBALS['db']->errorMsg());
    }
    
}

/* 获取区域列表 */
if($_REQUEST['act'] == 'get_region_list'){
    $region_id = intval($_REQUEST['region_id']);
    $sql = "select region_id, region_name from ".$GLOBALS['ecs']->table('region')." where parent_id = '$region_id'";
    $region = $GLOBALS['db'] -> getAll($sql);
    echo json_encode($region);
}

/* 根据城市id 获得县级列表 */
if($_REQUEST['act'] == 'selectCounty'){
    $sql = "select distinct county,region_name from ".$GLOBALS['ecs']->table('virtual_goods_district'). "as d left join (select region_id,region_name from" .$GLOBALS['ecs']->table('region'). ") as r on r.region_id = d.county where city = '{$_REQUEST['city']}'";
    $area = $GLOBALS['db'] -> getAll($sql);
    echo json_encode($area);
}

/* ajax修改是否显示 */       
if($_REQUEST['act']=='toggle_is_show'){
    check_authz_json('goods_manage'); //权限判断
    $district_id = intval($_POST['id']);
    $is_show        = intval($_POST['val']);
    $sql = "update ".$GLOBALS['ecs']->table('virtual_goods_district')." set is_show = '$is_show' where district_id = $district_id";
    if( $GLOBALS['db'] -> query($sql)){
        make_json_result($is_show);
    }
}

/* ajax修改商圈名称 */  
if($_REQUEST['act']=='edit_district_name'){
    check_authz_json('goods_manage');
    $district_id = intval($_POST['id']);
    $district_name  = json_str_iconv(trim($_POST['val']));
    $sql = "update ".$GLOBALS['ecs']->table('virtual_goods_district')." set district_name = '$district_name' where district_id = $district_id";
    if( $GLOBALS['db'] -> query($sql)){
        make_json_result($district_name);
    }
}

/* ajax修改排序 */  
if($_REQUEST['act']=='edit_sort'){
    check_authz_json('goods_manage');
    $district_id = intval($_POST['id']);
    $sort  = json_str_iconv(intval(trim($_REQUEST['val'])));
    $sql = "update ".$GLOBALS['ecs']->table('virtual_goods_district')." set sort = '$sort' where district_id = $district_id";
    if( $GLOBALS['db'] -> query($sql)){
        make_json_result($sort);
    }
}

/* ajax删除商圈  */  
if($_REQUEST['act']=='remove_district'){
    $district_id = intval($_REQUEST['id']);
    $sql = "delete from ".$GLOBALS['ecs']->table('virtual_goods_district')." where district_id = $district_id";
    $GLOBALS['db'] -> query($sql);
if ($GLOBALS['db'] ->errno() == 0)
    {
        $url = 'virtual_goods.php?act=query_district&' . str_replace('act=remove_district', '', $_SERVER['QUERY_STRING']);

        ecs_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error($GLOBALS['db']->errorMsg());
    }
}

if($_REQUEST['act']=='batch_district'){
       if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
            {
                sys_msg($_LANG['error_choose_district'], 1);
            }

            foreach ($_POST['checkboxes'] AS $key => $id)
            {
                $sql = "delete from ".$GLOBALS['ecs']->table('virtual_goods_district')." where district_id = $id";
                $GLOBALS['db'] -> query($sql);
            }
        $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'virtual_goods.php?act=district');
        sys_msg($_LANG['batch_handle_ok'], 0, $lnk);
}


/* 获得区域树型结构数据  */  
if($_REQUEST['act'] == 'get_district_tree'){
    $sql = "select distinct city from ".$GLOBALS['ecs']->table('virtual_goods_district');
    $city = $GLOBALS['db']->getAll($sql);  
    $arr = array();
    
    foreach($city as $k=>$v){
        $sql = "select region_id, parent_id, region_name from ".$GLOBALS['ecs']->table('region')." where region_id = $v[city]";
        $a =  $GLOBALS['db']->getRow($sql);  
        $arr[$k]['id'] = $a['region_id'];
        $arr[$k]['pId'] = $a['parent_id'];
        $arr[$k]['name'] = $a['region_name'];
        $arr[$k]['open'] = true;
    }
    $sql = "select distinct county from ".$GLOBALS['ecs']->table('virtual_goods_district');
    $county = $GLOBALS['db']->getAll($sql);  
    foreach($county as $k=>$v){
        $sql = "select region_id, parent_id, region_name from ".$GLOBALS['ecs']->table('region')." where region_id = $v[county]";
        $a =  $GLOBALS['db']->getRow($sql);  
        $arr[count($city)+$k]['id'] = $a['region_id'];
        $arr[count($city)+$k]['pId'] = $a['parent_id'];
        $arr[count($city)+$k]['name'] = $a['region_name'];
    } 
    
    echo json_encode($arr);
}


/**
 * 验证同区域是否已有此商圈
 */
if($_REQUEST['act'] == 'validate_district'){
        $province = $_REQUEST['province'];
        $city = $_REQUEST['city'];
        $county = $_REQUEST['county'];
        $district_name = $_REQUEST['district_name'];
        $sql = "select district_id from ".$GLOBALS['ecs']->table('virtual_goods_district')." where province = '$province' and city = '$city' and county='$county' and district_name='$district_name'";
        $res = $GLOBALS['db']->getOne($sql);
        if($res){
            echo json_encode(0);
        }else{
            echo json_encode(1);
        }
        
}

/**
 * 获得商圈列表
 * @param int city 市id 
 * @param int county 县id
 * @param string keyword 查询关键字
 * @return array
 */
function get_district_list()
{
    
       /* 查询条件 */
    $filter['city']    =  intval($_REQUEST['city']);
    $filter['county'] =  intval($_REQUEST['county']);
    $filter['keyword']    = trim($_REQUEST['keyword']);
    
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keyword'] = json_str_iconv($filter['keyword']);
        
    }
    $filter['sort_by']     = empty($_REQUEST['sort_by'])     ? 'district_id' : trim($_REQUEST['sort_by']);
    $filter['sort']  = empty($_REQUEST['sort'])  ? 'DESC' : trim($_REQUEST['sort']);
 
    $where  = (!empty($filter['city'])) ? " AND city = '" . $filter['city'] . "' " : '';
    $where .= (!empty($filter['keyword'])) ? " AND district_name like '%" . $filter['keyword'] . "%' " : '';
    $where .= ($filter['county'] !='') ? " AND county = '" . $filter['county'] . "' " : '';

    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('virtual_goods_district') . " WHERE 1 $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);
    $start  = ($filter['page'] - 1) * $filter['page_size'];

    /* 查询 */
    $sql = "SELECT *".
            " FROM ".$GLOBALS['ecs']->table('virtual_goods_district').
            " WHERE 1 ".$where.
            " ORDER BY $filter[sort_by] $filter[sort] ".
            " LIMIT $start, $filter[page_size]";
    $all = $GLOBALS['db']->getAll($sql);   

    foreach($all as $k=>$v){
       $all[$k]['area_name'] = get_region_name($v['province']).'-'.get_region_name($v['city']).'-'.get_region_name($v['county']);
    }
    return array('item' => $all, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
    
/*********************虚拟商品列表*********************/
/*------------------------------------------------------ */
//-- 商品列表，商品回收站
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list' || $_REQUEST['act'] == 'trash')
{
    admin_priv('goods_manage');

    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $code   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    $suppliers_id = isset($_REQUEST['suppliers_id']) ? (empty($_REQUEST['suppliers_id']) ? '' : trim($_REQUEST['suppliers_id'])) : '';
    $is_on_sale = isset($_REQUEST['is_on_sale']) ? ((empty($_REQUEST['is_on_sale']) && $_REQUEST['is_on_sale'] === 0) ? '' : trim($_REQUEST['is_on_sale'])) : '';

    $handler_list = array();
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'img'=>'icon_send_bonus.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'img'=>'icon_add.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'img'=>'icon_output.gif');

    if ($_REQUEST['act'] == 'list' && isset($handler_list[$code]))
    {
        $smarty->assign('add_handler',      $handler_list[$code]);
    }

    /* 供货商名 */
    $suppliers_list_name = suppliers_list_name();
    $suppliers_exists = 0;
    $smarty->assign('is_on_sale', $is_on_sale);
    $smarty->assign('suppliers_id', $suppliers_id);
    $smarty->assign('suppliers_list_name', $suppliers_list_name);
    unset($suppliers_list_name, $suppliers_exists);
    if(intval($_REQUEST['supp'])>0){
		/* 代码增加_start  By www.68ecshop.com */
		$supplier_list_name=array();
		$sql_supplier = "select supplier_id, supplier_name FROM " . $GLOBALS['ecs']->table("supplier") . " where status='1' order by supplier_id ";
		$res_supplier = $db->query($sql_supplier);
		while($row_supplier=$db->fetchRow($res_supplier))
		{
			$supplier_list_name[$row_supplier['supplier_id']] = $row_supplier['supplier_name'];
		}
		//$suppliers_exists = count($supplier_list_name)>0 ? 1 : 0;
		$smarty->assign('suppliers_exists', 1);//$suppliers_exists);
		$smarty->assign('suppliers_list_name', $supplier_list_name);
		$supplier_status_list =array('0'=>'未审核', '1'=>'审核通过', '-1'=>'审核未通过');
		$smarty->assign('supplier_status_list', $supplier_status_list);
		/* 代码增加_end  By www.68ecshop.com */
    }else{
    	// 入驻商商品列表不显示添加新商品
    	$action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'virtual_goods.php?act=list', 'text' => $_LANG['01_goods_list']);
    	$smarty->assign('action_link',  $action_link);
    }
    /* 模板赋值 */
    $goods_ur = array('' => $_LANG['01_goods_list'], 'virtual_card'=>$_LANG['50_virtual_card_list']);
    $ur_here = ($_REQUEST['supp']) ? $_LANG['supp_virtual_goods_list'] : $_LANG['virtual_goods_list'];
    $smarty->assign('ur_here', $ur_here);
    $smarty->assign('code',     $code);
    $smarty->assign('cat_list',     cat_list(0, $cat_id));
    $smarty->assign('brand_list',   get_brand_list());
    $smarty->assign('intro_list',   get_intro_list());
    $smarty->assign('lang',         $_LANG);
    $smarty->assign('list_type',    $_REQUEST['act'] == 'list' ? 'goods' : 'trash');
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);
    $city = get_city_list();
    $smarty -> assign('city' ,$city);
    $suppliers_list = suppliers_list_info(' is_check = 1 ');
    $suppliers_list_count = count($suppliers_list);
    $smarty->assign('suppliers_list', ($suppliers_list_count == 0 ? 0 : $suppliers_list)); // 取供货商列表

    $goods_list = goods_list($_REQUEST['act'] == 'list' ? 0 : 1, ($_REQUEST['act'] == 'list') ? (($code == '') ? 1 : 0) : -1);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('full_page',    1);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();
    $smarty->assign('specifications', $specifications);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('virtual_goods_list.htm');
}

/*------------------------------------------------------ */
//-- 添加新商品 编辑商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'copy')
{	

	// 代码增加_start_derek20150129admin_goods  www.68ecshop.com

	include_once(ROOT_PATH . '/includes/Pinyin.php');

	// 代码增加_end_derek20150129admin_goods  www.68ecshop.com

    $is_add = $_REQUEST['act'] == 'add'; // 添加还是编辑的标识
    $is_copy = $_REQUEST['act'] == 'copy'; //是否复制
    $code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    $supplier_id = empty($_REQUEST['supplier_id'])?0:intval($_REQUEST['supplier_id']);
    $code=$code=='virual_good' ? 'virual_good': '';

	if (isset($_CFG['supplier_addbest']))
	{
		$smarty->assign('is_addbest', $_CFG['supplier_addbest']);
	}
	if (isset($_CFG['supplier_editgoods']))
	{
		$smarty->assign('is_editgoods', $_CFG['supplier_editgoods']);
	}
	if (isset($_CFG['supplier_secondadd']))
	{
		$smarty->assign('is_secondadd', $_CFG['supplier_secondadd']);
	}
    
    /* 供货商名 */
    $suppliers_list_name = suppliers_list_name();
    $suppliers_exists = 1;
    if (empty($suppliers_list_name))
    {
        $suppliers_exists = 0;
    }
    $smarty->assign('suppliers_exists', $suppliers_exists);
    $smarty->assign('suppliers_list_name', $suppliers_list_name);
    unset($suppliers_list_name, $suppliers_exists);

    /* 如果是安全模式，检查目录是否存在 */
    if (ini_get('safe_mode') == 1 && (!file_exists('../' . IMAGE_DIR . '/'.date('Ym')) || !is_dir('../' . IMAGE_DIR . '/'.date('Ym'))))
    {
        if (@!mkdir('../' . IMAGE_DIR . '/'.date('Ym'), 0777))
        {
            $warning = sprintf($_LANG['safe_mode_warning'], '../' . IMAGE_DIR . '/'.date('Ym'));
            $smarty->assign('warning', $warning);
        }
    }

    /* 如果目录存在但不可写，提示用户 */
    elseif (file_exists('../' . IMAGE_DIR . '/'.date('Ym')) && file_mode_info('../' . IMAGE_DIR . '/'.date('Ym')) < 2)
    {
        $warning = sprintf($_LANG['not_writable_warning'], '../' . IMAGE_DIR . '/'.date('Ym'));
        $smarty->assign('warning', $warning);
    }
    
    if ($is_add)
    {
        /* 默认值 */
        $last_choose = array(0, 0);
        if (!empty($_COOKIE['ECSCP']['last_choose']))
        {
            $last_choose = explode('|', $_COOKIE['ECSCP']['last_choose']);
        }
        $goods = array(
            'goods_id'      => 0,
            'goods_desc'    => '',
            'cat_id'        => $last_choose[0],
            'brand_id'      => $last_choose[1],
            'is_on_sale'    => '1',
            'is_alone_sale' => '1',
            'is_shipping' => '0',
            'other_cat'     => array(), // 扩展分类
            'goods_type'    => 0,       // 商品类型
            'shop_price'    => 0,
            'promote_price' => 0,
            'market_price'  => 0,
            'integral'      => 0,
            'goods_number'  => $_CFG['default_storage'],
            'warn_number'   => 1,
            'promote_start_date' => local_date('Y-m-d'),
            'promote_end_date'   => local_date('Y-m-d', local_strtotime('+1 month')),
            'goods_weight'  => 0,
            'give_integral' => -1,
            'rank_integral' => -1
        );

        if ($code != '')
        {
            $goods['goods_number'] = 0;
        }

        /* 关联商品 */
        $link_goods_list = array();
        $sql = "DELETE FROM " . $ecs->table('link_goods') .
                " WHERE (goods_id = 0 OR link_goods_id = 0)" .
                " AND admin_id = '$_SESSION[admin_id]'";
        $db->query($sql);

        /* 组合商品 */
        $group_goods_list = array();
        $sql = "DELETE FROM " . $ecs->table('group_goods') .
                " WHERE parent_id = 0 AND admin_id = '$_SESSION[admin_id]'";
        $db->query($sql);

        /* 关联文章 */
        $goods_article_list = array();
        $sql = "DELETE FROM " . $ecs->table('goods_article') .
                " WHERE goods_id = 0 AND admin_id = '$_SESSION[admin_id]'";
        $db->query($sql);

        /* 属性 */
        $sql = "DELETE FROM " . $ecs->table('goods_attr') . " WHERE goods_id = 0";
        $db->query($sql);

        /* 图片列表 */
        $img_list = array();
    }
    else
    {
		/*
		 *702460594
		 *
		 *
		 *查询输出条形码
		 */
		$sql = "SELECT * FROM". $ecs->table('bar_code') ."WHERE goods_id ='$_REQUEST[goods_id]'";
		$bar_code =$db->getAll($sql);
	
        /* 商品信息 */
        $sql = "SELECT * FROM " . $ecs->table('goods') . " WHERE goods_id = '$_REQUEST[goods_id]'";
        $goods = $db->getRow($sql);

		// 代码增加_start_derek20150129admin_goods  www.68ecshop.com
		
		$r_b_id = $db->getOne("select brand_name from ".$ecs->table('brand')." where brand_id=".$goods['brand_id']);
		$goods['brand_name'] = $r_b_id;
		$smarty->assign('brand_name_val',$goods['brand_name']);
		
		// 代码增加_end_derek20150129admin_goods  www.68ecshop.com

        /* 虚拟卡商品复制时, 将其库存置为0*/
        if ($is_copy && $code != '')
        {
            $goods['goods_number'] = 0;
        }

        if (empty($goods) === true)
        {
            /* 默认值 */
            $goods = array(
                'goods_id'      => 0,
                'goods_desc'    => '',
                'cat_id'        => 0,
                'is_on_sale'    => '1',
                'is_alone_sale' => '1',
                'is_shipping' => '0',
                'other_cat'     => array(), // 扩展分类
                'goods_type'    => 0,       // 商品类型
                'shop_price'    => 0,
                'promote_price' => 0,
                'market_price'  => 0,
                'integral'      => 0,
                'goods_number'  => 1,
                'warn_number'   => 1,
                'promote_start_date' => local_date('Y-m-d'),
                'promote_end_date'   => local_date('Y-m-d', gmstr2tome('+1 month')),
                'goods_weight'  => 0,
                'give_integral' => -1,
                'rank_integral' => -1
            );
        }

        /* 获取商品类型存在规格的类型 */
        $specifications = get_goods_type_specifications();
        $goods['specifications_id'] = $specifications[$goods['goods_type']];
        $_attribute = get_goods_specifications_list($goods['goods_id']);
        $goods['_attribute'] = empty($_attribute) ? '' : 1;

        /* 根据商品重量的单位重新计算 */
        if ($goods['goods_weight'] > 0)
        {
            $goods['goods_weight_by_unit'] = ($goods['goods_weight'] >= 1) ? $goods['goods_weight'] : ($goods['goods_weight'] / 0.001);
        }

        if (!empty($goods['goods_brief']))
        {
            //$goods['goods_brief'] = trim_right($goods['goods_brief']);
            $goods['goods_brief'] = $goods['goods_brief'];
        }
        if (!empty($goods['keywords']))
        {
            //$goods['keywords']    = trim_right($goods['keywords']);
            $goods['keywords']    = $goods['keywords'];
        }

        /* 如果不是促销，处理促销日期 */
        if (isset($goods['is_promote']) && $goods['is_promote'] == '0')
        {
            unset($goods['promote_start_date']);
            unset($goods['promote_end_date']);
        }
        else
        {
            $goods['promote_start_date'] = local_date('Y-m-d', $goods['promote_start_date']);
            $goods['promote_end_date'] = local_date('Y-m-d', $goods['promote_end_date']);
        }

	$goods['buymax_start_date'] = local_date('Y-m-d', $goods['buymax_start_date']);
	$goods['buymax_end_date'] = local_date('Y-m-d', $goods['buymax_end_date']);
        $goods['valid_date'] = local_date('Y-m-d', $goods['valid_date']);

        /* 如果是复制商品，处理 */
        if ($_REQUEST['act'] == 'copy')
        {
            // 商品信息
            $goods['goods_id'] = 0;
            $goods['goods_sn'] = '';
            $goods['goods_name'] = '';
            $goods['goods_img'] = '';
            $goods['goods_thumb'] = '';
            $goods['original_img'] = '';

            // 扩展分类不变

            // 关联商品
            $sql = "DELETE FROM " . $ecs->table('link_goods') .
                    " WHERE (goods_id = 0 OR link_goods_id = 0)" .
                    " AND admin_id = '$_SESSION[admin_id]'";
            $db->query($sql);

            $sql = "SELECT '0' AS goods_id, link_goods_id, is_double, '$_SESSION[admin_id]' AS admin_id" .
                    " FROM " . $ecs->table('link_goods') .
                    " WHERE goods_id = '$_REQUEST[goods_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $db->autoExecute($ecs->table('link_goods'), $row, 'INSERT');
            }

            $sql = "SELECT goods_id, '0' AS link_goods_id, is_double, '$_SESSION[admin_id]' AS admin_id" .
                    " FROM " . $ecs->table('link_goods') .
                    " WHERE link_goods_id = '$_REQUEST[goods_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $db->autoExecute($ecs->table('link_goods'), $row, 'INSERT');
            }

            // 配件
            $sql = "DELETE FROM " . $ecs->table('group_goods') .
                    " WHERE parent_id = 0 AND admin_id = '$_SESSION[admin_id]'";
            $db->query($sql);

            $sql = "SELECT 0 AS parent_id, goods_id, goods_price, '$_SESSION[admin_id]' AS admin_id " .
                    "FROM " . $ecs->table('group_goods') .
                    " WHERE parent_id = '$_REQUEST[goods_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $db->autoExecute($ecs->table('group_goods'), $row, 'INSERT');
            }

            // 关联文章
            $sql = "DELETE FROM " . $ecs->table('goods_article') .
                    " WHERE goods_id = 0 AND admin_id = '$_SESSION[admin_id]'";
            $db->query($sql);

            $sql = "SELECT 0 AS goods_id, article_id, '$_SESSION[admin_id]' AS admin_id " .
                    "FROM " . $ecs->table('goods_article') .
                    " WHERE goods_id = '$_REQUEST[goods_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $db->autoExecute($ecs->table('goods_article'), $row, 'INSERT');
            }

            // 图片不变

            // 商品属性
            $sql = "DELETE FROM " . $ecs->table('goods_attr') . " WHERE goods_id = 0";
            $db->query($sql);

            $sql = "SELECT 0 AS goods_id, attr_id, attr_value, attr_price " .
                    "FROM " . $ecs->table('goods_attr') .
                    " WHERE goods_id = '$_REQUEST[goods_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $db->autoExecute($ecs->table('goods_attr'), addslashes_deep($row), 'INSERT');
            }
        }

        // 扩展分类
        $other_cat_list = array();
        $sql = "SELECT cat_id FROM " . $ecs->table('goods_cat') . " WHERE goods_id = '$_REQUEST[goods_id]'";
        $goods['other_cat'] = $db->getCol($sql);
        foreach ($goods['other_cat'] AS $cat_id)
        {
            $other_cat_list[$cat_id] = cat_list(0, $cat_id);
        }
        $smarty->assign('other_cat_list', $other_cat_list);

        $link_goods_list    = get_linked_goods($goods['goods_id']); // 关联商品
        $group_goods_list   = get_group_goods($goods['goods_id']); // 配件
        $goods_article_list = get_goods_articles($goods['goods_id']);   // 关联文章

        /* 商品图片路径 */
        if (isset($GLOBALS['shop_id']) && ($GLOBALS['shop_id'] > 10) && !empty($goods['original_img']))
        {
            $goods['goods_img'] = get_image_path($_REQUEST['goods_id'], $goods['goods_img']);
            $goods['goods_thumb'] = get_image_path($_REQUEST['goods_id'], $goods['goods_thumb'], true);
            $goods['original_img'] = get_image_path($_REQUEST['goods_id'], $goods['original_img']);
        }

        /* 图片列表 */
        $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods[goods_id]'";
        $img_list = $db->getAll($sql);

        /* 格式化相册图片路径 */
        if (isset($GLOBALS['shop_id']) && ($GLOBALS['shop_id'] > 0))
        {
            foreach ($img_list as $key => $gallery_img)
            {
                $gallery_img[$key]['img_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], false, 'gallery');
                $gallery_img[$key]['thumb_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], true, 'gallery');
            }
        }
        else
        {
            foreach ($img_list as $key => $gallery_img)
            {
                $gallery_img[$key]['thumb_url'] = '../' . (empty($gallery_img['thumb_url']) ? $gallery_img['img_url'] : $gallery_img['thumb_url']);
            }
        }

		$cat_list = cat_list1(0, $selected, false);
		$smarty->assign('goods_cat_name', $cat_list[$goods['cat_id']]['cat_name']);
    }

    if($is_add)
	{
		$cats_old_zhyh =array();
		$smarty->assign('is_add_zhyh', 1);
	}
	else
	{
		$smarty->assign('is_add_zhyh', 0);
		$sql_cats_zhyh="select * from ". $ecs->table('supplier_goods_cat') ." where goods_id='$goods[goods_id]' ";
		$res_old_zhyh = $db->query($sql_cats_zhyh);
		while ($row_old_zhyh = $db->fetchRow($res_old_zhyh))
		{
			$cats_old_zhyh[]=$row_old_zhyh['cat_id'];
		}
	}
	

   
        /* 获取区域列表 city*/
        $sql = "select distinct city,region_name from " . $ecs->table('virtual_goods_district') ." as d left join (select region_id,region_name from ". $ecs->table('region') ." ) as r on r.region_id = d.city";
        $city = $db->getAll($sql);
        $smarty->assign('city',$city);
        
            /* 获取商圈 */
        $sql = "select d.district_id,v.district_name from ".$GLOBALS['ecs']->table('virtual_district')." as d 
            left join ".$GLOBALS['ecs']->table('goods')." as g on  d.goods_id = g.goods_id
            left join ".$GLOBALS['ecs']->table('virtual_goods_district')." as v on v.district_id = d.district_id
            where d.goods_id = '{$_REQUEST['goods_id']}' and d.supplier_id = $supplier_id";
        $district = $db->getAll($sql);
        $district_ids = '';
        foreach($district as $key => $value){
           $district_ids = $district_ids.$value['district_id']. ',';
        }
     
        $smarty->assign('district_ids',substr($district_ids,0,strlen($district_ids)-1));
        $smarty->assign('district',$district);
       
    /* 拆分商品名称样式 */
    $goods_name_style = explode('+', empty($goods['goods_name_style']) ? '+' : $goods['goods_name_style']);

    /* 创建 html editor */
   create_html_editor('goods_desc', htmlspecialchars($goods['goods_desc'])); /* 修改 by www.68ecshop.com 百度编辑器 */

    /* 模板赋值 */
    $action_link = $supplier_id?array('href' => 'virtual_goods.php?act=list&extension_code=virtual_good&supp=1' , 'text' => '返回商品列表'):array('href' => 'virtual_goods.php?act=list&extension_code=virtual_good' , 'text' => '返回商品列表');
    $smarty->assign('code',    $code);
    $smarty->assign('ur_here', $is_add ? $_LANG['add_virtual_goods'] : ($_REQUEST['act'] == 'edit' ? $_LANG['edit_virtual_goods'] : $_LANG['copy_virtuale_goods']));
    $smarty->assign('action_link', $action_link);
    $smarty->assign('goods', $goods);
    $smarty->assign('goods_name_color', $goods_name_style[0]);
    $smarty->assign('goods_name_style', $goods_name_style[1]);
    $smarty->assign('cat_list', cat_list(0, $goods['cat_id']));
	// 代码修改_start_derek20150129admin_goods  www.68ecshop.com
    $smarty->assign('goods_cat_id', $goods['cat_id']);
    
    $smarty->assign('brand_list', get_brand_list(true));
	
	// 代码修改_start_derek20150129admin_goods  www.68ecshop.com
    $smarty->assign('unit_list', get_unit_list());
    $smarty->assign('user_rank_list', get_user_rank_list());
    $smarty->assign('weight_unit', $is_add ? '1' : ($goods['goods_weight'] >= 1 ? '1' : '0.001'));
    $smarty->assign('cfg', $_CFG);
    $smarty->assign('form_act', $is_add ? 'insert' : ($_REQUEST['act'] == 'edit' ? 'update' : 'insert'));
    if ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
    {
        $smarty->assign('is_add', true);
    }
    if(!$is_add)
    {
        $smarty->assign('member_price_list', get_member_price_list($_REQUEST['goods_id']));
    }
    $smarty->assign('link_goods_list', $link_goods_list);
    $smarty->assign('group_goods_list', $group_goods_list);
    $smarty->assign('goods_article_list', $goods_article_list);
    $smarty->assign('img_list', $img_list);
    $smarty->assign('goods_type_list', goods_type_list($goods['goods_type']));
    $smarty->assign('gd', gd_version());
    $smarty->assign('thumb_width', $_CFG['thumb_width']);
    $smarty->assign('thumb_height', $_CFG['thumb_height']);
    $smarty->assign('goods_attr_html', build_attr_html($goods['goods_type'], $goods['goods_id'],$bar_code));
    $volume_price_list = '';
    if(isset($_REQUEST['goods_id']))
    {
    $volume_price_list = get_volume_price_list($_REQUEST['goods_id']);
    }
    if (empty($volume_price_list))
    {
        $volume_price_list = array('0'=>array('number'=>'','price'=>''));
    }
    
    $smarty->assign('volume_price_list', $volume_price_list);
    /* 显示商品信息页面 */
    assign_query_info();
        $smarty->display('virtual_goods_info.htm');
}



/*------------------------------------------------------ */
//-- 插入商品 更新商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    $code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);

    /* 是否处理缩略图 */
    $proc_thumb = (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0)? false : true;
    if ($code == 'virtual_card')
    {
        admin_priv('virualcard'); // 检查权限
    }
    else
    {
        admin_priv('goods_manage'); // 检查权限
    }
	if(empty($_POST['goods_name']))
	{
		sys_msg("商品名称不能为空");
	}
    /* 检查货号是否重复 */
    if ($_POST['goods_sn'])
    {
        $sql = "SELECT COUNT(*) FROM " . $ecs->table('goods') .
                " WHERE goods_sn = '$_POST[goods_sn]' AND is_delete = 0 AND goods_id <> '$_POST[goods_id]'";
        if ($db->getOne($sql) > 0)
        {
            sys_msg($_LANG['goods_sn_exists'], 1, array(), false);
        }
    }

    /* 检查图片：如果有错误，检查尺寸是否超过最大值；否则，检查文件类型 */
    if (isset($_FILES['goods_img']['error'])) // php 4.2 版本才支持 error
    {
        // 最大上传文件大小
        $php_maxsize = ini_get('upload_max_filesize');
        $htm_maxsize = '2M';

        // 商品图片
        if ($_FILES['goods_img']['error'] == 0)
        {
            if (!$image->check_img_type($_FILES['goods_img']['type']))
            {
                sys_msg($_LANG['invalid_goods_img'], 1, array(), false);
            }
        }
        elseif ($_FILES['goods_img']['error'] == 1)
        {
            sys_msg(sprintf($_LANG['goods_img_too_big'], $php_maxsize), 1, array(), false);
        }
        elseif ($_FILES['goods_img']['error'] == 2)
        {
            sys_msg(sprintf($_LANG['goods_img_too_big'], $htm_maxsize), 1, array(), false);
        }

        // 商品缩略图
        if (isset($_FILES['goods_thumb']))
        {
            if ($_FILES['goods_thumb']['error'] == 0)
            {
                if (!$image->check_img_type($_FILES['goods_thumb']['type']))
                {
                    sys_msg($_LANG['invalid_goods_thumb'], 1, array(), false);
                }
            }
            elseif ($_FILES['goods_thumb']['error'] == 1)
            {
                sys_msg(sprintf($_LANG['goods_thumb_too_big'], $php_maxsize), 1, array(), false);
            }
            elseif ($_FILES['goods_thumb']['error'] == 2)
            {
                sys_msg(sprintf($_LANG['goods_thumb_too_big'], $htm_maxsize), 1, array(), false);
            }
        }

        // 相册图片

		/* 代码增加_start   By www.ecshop68.com */
		if($_FILES['img_url']['error'])
		{
		/* 代码增加_end   By www.ecshop68.com */

        foreach ($_FILES['img_url']['error'] AS $key => $value)
        {
            if ($value == 0)
            {
                if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
                {
                    sys_msg(sprintf($_LANG['invalid_img_url'], $key + 1), 1, array(), false);
                }
            }
            elseif ($value == 1)
            {
                sys_msg(sprintf($_LANG['img_url_too_big'], $key + 1, $php_maxsize), 1, array(), false);
            }
            elseif ($_FILES['img_url']['error'] == 2)
            {
                sys_msg(sprintf($_LANG['img_url_too_big'], $key + 1, $htm_maxsize), 1, array(), false);
            }
        }

		/* 代码增加_start   By www.ecshop68.com */
		}
		/* 代码增加_end   By www.ecshop68.com */

    }
    /* 4.1版本 */
    else
    {
        // 商品图片
        if ($_FILES['goods_img']['tmp_name'] != 'none')
        {
            if (!$image->check_img_type($_FILES['goods_img']['type']))
            {

                sys_msg($_LANG['invalid_goods_img'], 1, array(), false);
            }
        }

        // 商品缩略图
        if (isset($_FILES['goods_thumb']))
        {
            if ($_FILES['goods_thumb']['tmp_name'] != 'none')
            {
                if (!$image->check_img_type($_FILES['goods_thumb']['type']))
                {
                    sys_msg($_LANG['invalid_goods_thumb'], 1, array(), false);
                }
            }
        }

        // 相册图片
        foreach ($_FILES['img_url']['tmp_name'] AS $key => $value)
        {
            if ($value != 'none')
            {
                if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
                {
                    sys_msg(sprintf($_LANG['invalid_img_url'], $key + 1), 1, array(), false);
                }
            }
        }
    }

    /* 插入还是更新的标识 */
    $is_insert = $_REQUEST['act'] == 'insert';

    /* 处理商品图片 */
    $goods_img        = '';  // 初始化商品图片
    $goods_thumb      = '';  // 初始化商品缩略图
    $original_img     = '';  // 初始化原始图片
    $old_original_img = '';  // 初始化原始图片旧图

    // 如果上传了商品图片，相应处理
    if (($_FILES['goods_img']['tmp_name'] != '' && $_FILES['goods_img']['tmp_name'] != 'none') or (($_POST['goods_img_url'] != $_LANG['lab_picture_url'] && $_POST['goods_img_url'] != 'http://') && $is_url_goods_img = 1))
    {
        if ($_REQUEST['goods_id'] > 0)
        {
            /* 删除原来的图片文件 */
            $sql = "SELECT goods_thumb, goods_img, original_img " .
                    " FROM " . $ecs->table('goods') .
                    " WHERE goods_id = '$_REQUEST[goods_id]'";
            $row = $db->getRow($sql);
            if ($row['goods_thumb'] != '' && is_file('../' . $row['goods_thumb']))
            {
                @unlink('../' . $row['goods_thumb']);
            }
            if ($row['goods_img'] != '' && is_file('../' . $row['goods_img']))
            {
                @unlink('../' . $row['goods_img']);
            }
            if ($row['original_img'] != '' && is_file('../' . $row['original_img']))
            {
                /* 先不处理，以防止程序中途出错停止 */
                //$old_original_img = $row['original_img']; //记录旧图路径
            }
            /* 清除原来商品图片 */
            if ($proc_thumb === false)
            {
                get_image_path($_REQUEST[goods_id], $row['goods_img'], false, 'goods', true);
                get_image_path($_REQUEST[goods_id], $row['goods_thumb'], true, 'goods', true);
            }
        }

        if (empty($is_url_goods_img))
        {
            $original_img   = $image->upload_image($_FILES['goods_img']); // 原始图片
        }
        elseif ($_POST['goods_img_url'])
        {
            
            if(preg_match('/(.jpg|.png|.gif|.jpeg)$/',$_POST['goods_img_url']) && copy(trim($_POST['goods_img_url']), ROOT_PATH . 'temp/' . basename($_POST['goods_img_url'])))
            {
                  $original_img = 'temp/' . basename($_POST['goods_img_url']);
            }
            
        }

        if ($original_img === false)
        {
            sys_msg($image->error_msg(), 1, array(), false);
        }
        $goods_img      = $original_img;   // 商品图片

        /* 复制一份相册图片 */
        /* 添加判断是否自动生成相册图片 */
        if ($_CFG['auto_generate_gallery'])
        {
            $img        = $original_img;   // 相册图片
            $pos        = strpos(basename($img), '.');
            $newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
            if (!copy('../' . $img, '../' . $newname))
            {
                sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
            }
            $img        = $newname;

            $gallery_img    = $img;
            $gallery_thumb  = $img;
        }

        // 如果系统支持GD，缩放商品图片，且给商品图片和相册图片加水印
        if ($proc_thumb && $image->gd_version() > 0 && $image->check_img_function($_FILES['goods_img']['type']) || $is_url_goods_img)
        {

            if (empty($is_url_goods_img))
            {
                // 如果设置大小不为0，缩放图片
                if ($_CFG['image_width'] != 0 || $_CFG['image_height'] != 0)
                {
                    $goods_img = $image->make_thumb('../'. $goods_img , $GLOBALS['_CFG']['image_width'],  $GLOBALS['_CFG']['image_height']);
                    if ($goods_img === false)
                    {
                        sys_msg($image->error_msg(), 1, array(), false);
                    }
                }

                /* 添加判断是否自动生成相册图片 */
                if ($_CFG['auto_generate_gallery'])
                {
                    $newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
                    if (!copy('../' . $img, '../' . $newname))
                    {
                        sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
                    }
                    $gallery_img        = $newname;
                }

                // 加水印
                if (intval($_CFG['watermark_place']) > 0 && !empty($GLOBALS['_CFG']['watermark']))
                {
                    if ($image->add_watermark('../'.$goods_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
                    {
                        sys_msg($image->error_msg(), 1, array(), false);
                    }
                    /* 添加判断是否自动生成相册图片 */
                    if ($_CFG['auto_generate_gallery'])
                    {
                        if ($image->add_watermark('../'. $gallery_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
                        {
                            sys_msg($image->error_msg(), 1, array(), false);
                        }
                    }
                }
            }

            // 相册缩略图
            /* 添加判断是否自动生成相册图片 */
            if ($_CFG['auto_generate_gallery'])
            {
                if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0)
                {
                    $gallery_thumb = $image->make_thumb('../' . $img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
                    if ($gallery_thumb === false)
                    {
                        sys_msg($image->error_msg(), 1, array(), false);
                    }
                }
            }

        }
        /* 取消该原图复制流程 */
        // else
        // {
        //     /* 复制一份原图 */
        //     $pos        = strpos(basename($img), '.');
        //     $gallery_img = dirname($img) . '/' . $image->random_filename() . // substr(basename($img), $pos);
        //     if (!copy('../' . $img, '../' . $gallery_img))
        //     {
        //         sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
        //     }
        //     $gallery_thumb = '';
        // }
    }


    // 是否上传商品缩略图
    if (isset($_FILES['goods_thumb']) && $_FILES['goods_thumb']['tmp_name'] != '' &&
        isset($_FILES['goods_thumb']['tmp_name']) &&$_FILES['goods_thumb']['tmp_name'] != 'none')
    {
        // 上传了，直接使用，原始大小
        $goods_thumb = $image->upload_image($_FILES['goods_thumb']);
        if ($goods_thumb === false)
        {
            sys_msg($image->error_msg(), 1, array(), false);
        }
    }
    else
    {
        // 未上传，如果自动选择生成，且上传了商品图片，生成所略图
        if ($proc_thumb && isset($_POST['auto_thumb']) && !empty($original_img))
        {
            // 如果设置缩略图大小不为0，生成缩略图
            if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0)
            {
                $goods_thumb = $image->make_thumb('../' . $original_img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
                if ($goods_thumb === false)
                {
                    sys_msg($image->error_msg(), 1, array(), false);
                }
            }
            else
            {
                $goods_thumb = $original_img;
            }
        }
    }


    /* 删除下载的外链原图 */
    if (!empty($is_url_goods_img))
    {
        unlink(ROOT_PATH . $original_img);
        empty($newname) || unlink(ROOT_PATH . $newname);
        $url_goods_img = $goods_img = $original_img = htmlspecialchars(trim($_POST['goods_img_url']));
    }

    /* 如果没有输入商品货号则自动生成一个商品货号 */
    if (empty($_POST['goods_sn']))
    {
        $max_id     = $is_insert ? $db->getOne("SELECT MAX(goods_id) + 1 FROM ".$ecs->table('goods')) : $_REQUEST['goods_id'];
        $goods_sn   = generate_goods_sn($max_id);
    }
    else
    {
        $goods_sn   = $_POST['goods_sn'];
    }

    /* 处理虚拟商品商品数据 */
    $shop_price = !empty($_POST['shop_price']) ? $_POST['shop_price'] : 0;
    $market_price = !empty($_POST['market_price']) ? $_POST['market_price'] : 0;
    $promote_price = !empty($_POST['promote_price']) ? floatval($_POST['promote_price'] ) : 0;
    $is_promote = empty($promote_price) ? 0 : 1;
    $zhekou = ($promote_price == 0 ? 10.0 : (number_format(($promote_price/$shop_price),2))*10);
    $promote_start_date = ($is_promote && !empty($_POST['promote_start_date'])) ? local_strtotime($_POST['promote_start_date']) : 0;
    $promote_end_date = ($is_promote && !empty($_POST['promote_end_date'])) ? local_strtotime($_POST['promote_end_date']) : 0;
    $goods_weight = !empty($_POST['goods_weight']) ? $_POST['goods_weight'] * $_POST['weight_unit'] : 0;
    $is_best = isset($_POST['is_best']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $is_hot = isset($_POST['is_hot']) ? 1 : 0;
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
    $is_alone_sale = isset($_POST['is_alone_sale']) ? 1 : 0;
    $is_shipping = isset($_POST['is_shipping']) ? 1 : 0;
    $goods_number = isset($_POST['goods_number']) ? $_POST['goods_number'] : 0;
    $warn_number = isset($_POST['warn_number']) ? $_POST['warn_number'] : 0;
    $goods_type = isset($_POST['goods_type']) ? $_POST['goods_type'] : 0;
    $give_integral = isset($_POST['give_integral']) ? intval($_POST['give_integral']) : '-1';
    $rank_integral = isset($_POST['rank_integral']) ? intval($_POST['rank_integral']) : '-1';
    $suppliers_id = isset($_POST['suppliers_id']) ? intval($_POST['suppliers_id']) : '0';

    $cost_price = !empty($_POST['cost_price']) ? $_POST['cost_price'] : 0;
    $goods_name_style = $_POST['goods_name_color'] . '+' . $_POST['goods_name_style'];

    $catgory_id = empty($_POST['cat_id']) ? '' : intval($_POST['cat_id']);
    $brand_id = empty($_POST['brand_id']) ? '' : intval($_POST['brand_id']);

    $goods_thumb = (empty($goods_thumb) && !empty($_POST['goods_thumb_url']) && goods_parse_url($_POST['goods_thumb_url'])) ? htmlspecialchars(trim($_POST['goods_thumb_url'])) : $goods_thumb;
    $goods_thumb = (empty($goods_thumb) && isset($_POST['auto_thumb']))? $goods_img : $goods_thumb;

    $buymax = !empty($_POST['buymax']) ? floatval($_POST['buymax'] ) : 0;
    $is_buy = empty($buymax) ? 0 : 1;
    $buymax_start_date = ($is_buy && !empty($_POST['buymax_start_date'])) ? local_strtotime($_POST['buymax_start_date']) : 0;
    $buymax_end_date = ($is_buy && !empty($_POST['buymax_end_date'])) ? local_strtotime($_POST['buymax_end_date']) : 0;
    $valid_date = !empty($_POST['valid_date']) ? local_strtotime($_POST['valid_date']) : 0;
    $supplier_status = empty($_POST['supplier_status'])?'0':$_POST['supplier_status'];
    /* 获得虚拟商品商圈列表 */
    $district_list_ids = $_POST['district_list_ids'];
    $district_list_ids_arr =  explode(',', $district_list_ids);

    /* 入库 */
    if ($is_insert)
    {
        $sql = "INSERT INTO " . $ecs->table('goods') . " (goods_name, goods_name_style, goods_sn, " .
                           "cat_id, brand_id, shop_price, market_price, is_promote, zhekou, promote_price, " .
                           "promote_start_date, promote_end_date, is_buy,buymax,buymax_start_date,buymax_end_date,goods_img, goods_thumb, original_img, keywords, goods_brief, " .
                           "seller_note, goods_weight, goods_number, warn_number, integral, give_integral, is_best, is_new, is_hot, is_real, " .
                           "is_on_sale, is_alone_sale, is_shipping, goods_desc, add_time, last_update, goods_type, extension_code, rank_integral, cost_price, valid_date,is_virtual)" .
                       "VALUES ('$_POST[goods_name]', '$goods_name_style', '$goods_sn', '$catgory_id', " .
                           "'$brand_id', '$shop_price', '$market_price', '$is_promote', '$zhekou', '$promote_price', ".
                           "'$promote_start_date', '$promote_end_date', '$is_buy','$buymax','$buymax_start_date','$buymax_end_date', '$goods_img', '$goods_thumb', '$original_img', ".
                           "'$_POST[keywords]', '$_POST[goods_brief]', '$_POST[seller_note]', '$goods_weight', '$goods_number',".
                           " '$warn_number', '$_POST[integral]', '$give_integral', '$is_best', '$is_new', '$is_hot', 0, '$is_on_sale', '$is_alone_sale', $is_shipping, ".
                           " '$_POST[goods_desc]', '" . gmtime() . "', '". gmtime() ."', '$goods_type', '$code', '$rank_integral', '$cost_price','$valid_date','1')";
    }
    else
    {
        /* 如果有上传图片，删除原来的商品图 */
        $sql = "SELECT goods_thumb, goods_img, original_img " .
                    " FROM " . $ecs->table('goods') .
                    " WHERE goods_id = '$_REQUEST[goods_id]'";
        $row = $db->getRow($sql);
        if ($proc_thumb && $goods_img && $row['goods_img'] && !goods_parse_url($row['goods_img']))
        {
            @unlink(ROOT_PATH . $row['goods_img']);
            @unlink(ROOT_PATH . $row['original_img']);
        }

        if ($proc_thumb && $goods_thumb && $row['goods_thumb'] && !goods_parse_url($row['goods_thumb']))
        {
            @unlink(ROOT_PATH . $row['goods_thumb']);
        }

		if ($_REQUEST['supplier_status'] == '1')
		{
			$is_on_sale = '1';
		}
		else
		{
			$is_on_sale = '0';
		}

        $sql = "UPDATE " . $ecs->table('goods') . " SET " .
                "goods_name = '$_POST[goods_name]', " .
                "goods_name_style = '$goods_name_style', " .
                "goods_sn = '$goods_sn', " .
                "cat_id = '$catgory_id', " .
                "brand_id = '$brand_id', " .
                "shop_price = '$shop_price', " .
                "market_price = '$market_price', " .
                "is_promote = '$is_promote', " .
		"zhekou = '$zhekou', " .
                "promote_price = '$promote_price', " .
                "promote_start_date = '$promote_start_date', " .
		"is_buy = '$is_buy', " .
		"buymax = '$buymax', " .
		"buymax_start_date = '$buymax_start_date', " .
		"buymax_end_date = '$buymax_end_date', " .
                "suppliers_id = '$suppliers_id', " .
                "valid_date = '$valid_date', " .
		"cost_price = '$cost_price',".
                "supplier_status = '$supplier_status',".
                "promote_end_date = '$promote_end_date', ";
        /* 如果有上传图片，需要更新数据库 */
        if ($goods_img)
        {
            $sql .= "goods_img = '$goods_img', original_img = '$original_img', ";
        }
        if ($goods_thumb)
        {
            $sql .= "goods_thumb = '$goods_thumb', ";
        }
        if ($code != '')
        {
            $sql .= "is_real=0, extension_code='$code', ";
        }
        $sql .= "keywords = '$_POST[keywords]', " .
                "goods_brief = '$_POST[goods_brief]', " .
                "seller_note = '$_POST[seller_note]', " .
		"supplier_status='$_POST[supplier_status]', supplier_status_txt = '$_POST[supplier_status_txt]', ".
                "goods_weight = '$goods_weight'," .
                "goods_number = '$goods_number', " .
                "warn_number = '$warn_number', " .
                "integral = '$_POST[integral]', " .
                "give_integral = '$give_integral', " .
                "rank_integral = '$rank_integral', " .
                "is_best = '$is_best', " .
                "is_new = '$is_new', " .
                "is_hot = '$is_hot', " .
                "is_on_sale = '$is_on_sale', " .
                "is_alone_sale = '$is_alone_sale', " .
                "is_shipping = '$is_shipping', " .
                "goods_desc = '$_POST[goods_desc]', " .
                "last_update = '". gmtime() ."', ".
                "goods_type = '$goods_type' " .
                "WHERE goods_id = '$_REQUEST[goods_id]' LIMIT 1";
    }
    $db->query($sql);
	/* 商品编号 */
    $goods_id = $is_insert ? $db->insert_id() : $_REQUEST['goods_id'];

       /* 存入商圈表 */
   
   $sql = "delete from ".$ecs->table('virtual_district')." where goods_id = '$goods_id' and supplier_id = '$_REQUEST[supplier_id]'";
   $db->query($sql);   //先删除对应goods_id下的商圈   

   foreach($district_list_ids_arr as $k=>$v){
        $sql = "INSERT INTO " . $ecs->table('virtual_district') . " (district_id, goods_id, supplier_id)" .
                "VALUES ('$v','$goods_id','$_REQUEST[supplier_id]')";
    $db->query($sql);
   } 
    
	if($goods_number>0)
	{
		$sms_on = $_CFG['sms_goods_stockout'];
		
		$dispose = "SELECT email, tel, is_dispose FROM " .$ecs->table('booking_goods'). " WHERE  goods_id = '$_REQUEST[goods_id]'";
        $email   = $db->getAll($dispose);
		$msg_goods_name = $_POST[goods_name];
		$msg_goods_id = $_REQUEST[goods_id];
		$msg_goods_url = build_uri('goods', array('gid'=>$msg_goods_id), $msg_goods_name);
	    $msg_goods_url = $GLOBALS['ecs']->url(). $msg_goods_url;
		$email_content = '您登记的商品 '. $msg_goods_name .' 已经到货，您可点击下面链接直接进入商品页面浏览或购买！<br><a href="'. $msg_goods_url.'">'.$msg_goods_url.'</a>';
		$sms_content   = sprintf($_CFG['sms_goods_stockout_tpl'],$msg_goods_name,$msg_goods_url,$_CFG['sms_sign']);
		 for($i=0;$i<count($email);$i++)
		{
			if($email[$i]['is_dispose']==0)
			{
				send_mail('', $email[$i]['email'], '您登记的商品'. $msg_goods_name .'已经到货', $email_content, 1);
				$sql = "UPDATE " . $ecs->table('booking_goods') . " SET is_dispose = 1 WHERE  goods_id = '$_REQUEST[goods_id]'";
                $db->query($sql);
			}
		}
		
		if($sms_on>0)
		{
			$a=$_SESSION;
			include_once('../send.php');
			foreach($a as $key=>$value)//sms.php会清空SESSION 先将SESSION存到变量中然后遍历出来
			{
				$_SESSION[$key]=$value;
			}
			for($i=0;$i<count($email);$i++)
			{
				if($email[$i]['is_dispose']==0)
				{
					$r=sendSMS($email[$i]['tel'], $sms_content);
					if($r==true)
					{
						$sql = "UPDATE " . $ecs->table('booking_goods') . " SET is_dispose = 2 WHERE  goods_id = '$_REQUEST[goods_id]'";
						$db->query($sql);
					}	
					else
					{
						$sql = "UPDATE " . $ecs->table('booking_goods') . " SET is_dispose = 3 WHERE  goods_id = '$_REQUEST[goods_id]'";
						$db->query($sql);
					}
				}			    
									
			} 
		}	
	}

    
		/* 代码增加_start  By  www.68ecshop.com */
	if ($is_insert)
	{
		$dir_clear  = get_dir('category', $catgory_id);
		$prefix_clear = "category-".$catgory_id;
		clearhtml_dir(ROOT_PATH.$dir_clear, $prefix_clear);
	}
	else
	{
		clearhtml_file('goods', $catgory_id, $goods_id);
	}
	/* 代码增加_end  By  www.68ecshop.com */
	
	/* 代码增加_start  Byjdy    为了便于新手朋友修改，这里采用增加代码的方法来修改，没有采用修改代码的方法 */
	 $sql = "UPDATE " .$ecs->table('goods'). " SET is_catindex = '$_REQUEST[is_catindex]' WHERE goods_id = '$goods_id' LIMIT 1";
	 $db->query($sql);  
         
	 /*存入条形码*/
	if($_POST['txm_shu'] && $_POST['tiaoxingm']){//如果txm_shu 和 tiaoxingm存在 就存入  不存在就不执行
		if(isset($_POST['txm_shu']) && isset($_POST['tiaoxingm']) || (empty($_POST['txm_shu'])) && (empty($_POST['tiaoxingm'])) ){
			$type = $_POST['txm_shu'];
			$bar_code = $_POST['tiaoxingm'];
			$db->query("DELETE FROM" .$ecs->table('bar_code')."WHERE goods_id ='$goods_id'");//根据商品ID清空数据
			foreach($type as $key=>$value){
				foreach($bar_code as $k=>$v){
					$arr['bar_code'] = $v;
					$arr['taypes'] = $value;
					$arr['goods_id'] = $goods_id;
					if($key == $k){
						$sql = "INSERT INTO " . $ecs->table('bar_code') . " (goods_id, taypes, bar_code) " .
						"VALUES ('$arr[goods_id]', '$arr[taypes]','$arr[bar_code]')";//插入数据
						$name = $db->query($sql);					
					}	
				}			
			}
		}else{
			$db->query("DELETE FROM" .$ecs->table('bar_code')."WHERE goods_id ='$goods_id'");//根据商品ID清空数据
		}
		
	}else{
		$db->query("DELETE FROM" .$ecs->table('bar_code')."WHERE goods_id ='$goods_id'");//根据商品ID清空数据
	}
	/* 代码增加_end  Byjdy */

    /* 记录日志 */
    if ($is_insert)
    {
        admin_log($_POST['goods_name'], 'add', 'goods');
    }
    else
    {
        admin_log($_POST['goods_name'], 'edit', 'goods');
    }

    /* 处理属性 */
    if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (empty($_POST['attr_id_list']) && empty($_POST['attr_value_list'])))
    {
        // 取得原有的属性值
        $goods_attr_list = array();

        $keywords_arr = explode(" ", $_POST['keywords']);

        $keywords_arr = array_flip($keywords_arr);
        if (isset($keywords_arr['']))
        {
            unset($keywords_arr['']);
        }

        $sql = "SELECT attr_id, attr_index FROM " . $ecs->table('attribute') . " WHERE cat_id = '$goods_type'";

        $attr_res = $db->query($sql);

        $attr_list = array();

        while ($row = $db->fetchRow($attr_res))
        {
            $attr_list[$row['attr_id']] = $row['attr_index'];
        }

        $sql = "SELECT g.*, a.attr_type
                FROM " . $ecs->table('goods_attr') . " AS g
                    LEFT JOIN " . $ecs->table('attribute') . " AS a
                        ON a.attr_id = g.attr_id
                WHERE g.goods_id = '$goods_id'";

        $res = $db->query($sql);

        while ($row = $db->fetchRow($res))
        {
            $goods_attr_list[$row['attr_id']][$row['attr_value']] = array('sign' => 'delete', 'goods_attr_id' => $row['goods_attr_id']);
        }
        // 循环现有的，根据原有的做相应处理
        if(isset($_POST['attr_id_list']))
        {
            foreach ($_POST['attr_id_list'] AS $key => $attr_id)
            {
                $attr_value = $_POST['attr_value_list'][$key];
                $attr_price = $_POST['attr_price_list'][$key];
				$attr_price = ($attr_price>=0) ? $attr_price : 0;
                if (!empty($attr_value))
                {
                    if (isset($goods_attr_list[$attr_id][$attr_value]))
                    {
                        // 如果原来有，标记为更新
                        $goods_attr_list[$attr_id][$attr_value]['sign'] = 'update';
                        $goods_attr_list[$attr_id][$attr_value]['attr_price'] = $attr_price;
                    }
                    else
                    {
                        // 如果原来没有，标记为新增
                        $goods_attr_list[$attr_id][$attr_value]['sign'] = 'insert';
                        $goods_attr_list[$attr_id][$attr_value]['attr_price'] = $attr_price;
                    }
                    $val_arr = explode(' ', $attr_value);
                    foreach ($val_arr AS $k => $v)
                    {
                        if (!isset($keywords_arr[$v]) && $attr_list[$attr_id] == "1")
                        {
                            $keywords_arr[$v] = $v;
                        }
                    }
                }
            }
        }
        $keywords = join(' ', array_flip($keywords_arr));

        $sql = "UPDATE " .$ecs->table('goods'). " SET keywords = '$keywords' WHERE goods_id = '$goods_id' LIMIT 1";

        $db->query($sql);

        /* 插入、更新、删除数据 */
        foreach ($goods_attr_list as $attr_id => $attr_value_list)
        {
            foreach ($attr_value_list as $attr_value => $info)
            {
                if ($info['sign'] == 'insert')
                {
                    $sql = "INSERT INTO " .$ecs->table('goods_attr'). " (attr_id, goods_id, attr_value, attr_price)".
                            "VALUES ('$attr_id', '$goods_id', '$attr_value', '$info[attr_price]')";
                }
                elseif ($info['sign'] == 'update')
                {
                    $sql = "UPDATE " .$ecs->table('goods_attr'). " SET attr_price = '$info[attr_price]' WHERE goods_attr_id = '$info[goods_attr_id]' LIMIT 1";
                }
                else
                {
                    $sql = "DELETE FROM " .$ecs->table('goods_attr'). " WHERE goods_attr_id = '$info[goods_attr_id]' LIMIT 1";
                }
                $db->query($sql);
            }
        }
    }

    /* 处理会员价格 */
    if (isset($_POST['user_rank']) && isset($_POST['user_price']))
    {
        handle_member_price($goods_id, $_POST['user_rank'], $_POST['user_price']);
    }

    /* 处理优惠价格 */
    if (isset($_POST['volume_number']) && isset($_POST['volume_price']))
    {
        $temp_num = array_count_values($_POST['volume_number']);
        foreach($temp_num as $v)
        {
            if ($v > 1)
            {
                sys_msg($_LANG['volume_number_continuous'], 1, array(), false);
                break;
            }
        }
        handle_volume_price($goods_id, $_POST['volume_number'], $_POST['volume_price']);
    }

    /* 处理扩展分类 */
    if (isset($_POST['other_cat']))
    {
        handle_other_cat($goods_id, array_unique($_POST['other_cat']));
    }

    if ($is_insert)
    {
        /* 处理关联商品 */
        handle_link_goods($goods_id);

        /* 处理组合商品 */
        handle_group_goods($goods_id);

        /* 处理关联文章 */
        handle_goods_article($goods_id);
    }

    /* 重新格式化图片名称 */
    $original_img = reformat_image_name('goods', $goods_id, $original_img, 'source');
    $goods_img = reformat_image_name('goods', $goods_id, $goods_img, 'goods');
    $goods_thumb = reformat_image_name('goods_thumb', $goods_id, $goods_thumb, 'thumb');
	
    if ($goods_img !== false)
    {
        $db->query("UPDATE " . $ecs->table('goods') . " SET goods_img = '$goods_img' WHERE goods_id='$goods_id'");
    }

    if ($original_img !== false)
    {
        $db->query("UPDATE " . $ecs->table('goods') . " SET original_img = '$original_img' WHERE goods_id='$goods_id'");
    }

    if ($goods_thumb !== false)
    {
        $db->query("UPDATE " . $ecs->table('goods') . " SET goods_thumb = '$goods_thumb' WHERE goods_id='$goods_id'");
    }

    /* 如果有图片，把商品图片加入图片相册 */
    if (isset($img))
    {
        /* 重新格式化图片名称 */
        if (empty($is_url_goods_img))
        {
            $img = reformat_image_name('gallery', $goods_id, $img, 'source');
            $gallery_img = reformat_image_name('gallery', $goods_id, $gallery_img, 'goods');
        }
        else
        {
            $img = $url_goods_img;
            $gallery_img = $url_goods_img;
        }

        $gallery_thumb = reformat_image_name('gallery_thumb', $goods_id, $gallery_thumb, 'thumb');
        $sql = "INSERT INTO " . $ecs->table('goods_gallery') . " (goods_id, img_url, img_desc, thumb_url, img_original) " .
                "VALUES ('$goods_id', '$gallery_img', '', '$gallery_thumb', '$img')";
        $db->query($sql);
    }

    /* 处理相册图片 */
    handle_gallery_image($goods_id, $_FILES['img_url'], $_POST['img_desc'], $_POST['img_file']);

    /* 编辑时处理相册图片描述 */
    if (!$is_insert && isset($_POST['old_img_desc']))
    {
        foreach ($_POST['old_img_desc'] AS $img_id => $img_desc)
        {
            $sql = "UPDATE " . $ecs->table('goods_gallery') . " SET img_desc = '$img_desc' WHERE img_id = '$img_id' LIMIT 1";
            $db->query($sql);
        }
    }

    /* 不保留商品原图的时候删除原图 */
    if ($proc_thumb && !$_CFG['retain_original_img'] && !empty($original_img))
    {
        $db->query("UPDATE " . $ecs->table('goods') . " SET original_img='' WHERE `goods_id`='{$goods_id}'");
        $db->query("UPDATE " . $ecs->table('goods_gallery') . " SET img_original='' WHERE `goods_id`='{$goods_id}'");
        @unlink('../' . $original_img);
        @unlink('../' . $img);
    }

	/* 代码增加_start  By  www.68ecshop.com */
	if ($_REQUEST['act'] == 'update')
	{	
		sendsms_pricecut($goods_id);		
	}
    /* 代码增加_end  By  www.68ecshop.com */

    /* 记录上一次选择的分类和品牌 */
    setcookie('ECSCP[last_choose]', $catgory_id . '|' . $brand_id, gmtime() + 86400);
    /* 清空缓存 */
    clear_cache_files();
    $supp = (empty($_REQUEST['supplier_id'])||$_REQUEST['supplier_id']==0)?'':'&supp=1';
    /* 提示页面 */
    $link = array();
	if($is_insert)
	 {
		$link[0] = array('href' => 'virtual_goods.php?act=list&extension_code=virtual_good'.$supp , 'text' => '返回商品列表');
	 }
	 else
	{
		$link[0] = array('href' => 'virtual_goods.php?act=list&extension_code=virtual_good'.$supp, 'text' => '返回商品列表');
	 }





    sys_msg($is_insert ? $_LANG['add_goods_ok'] : $_LANG['edit_goods_ok'], 0, $link);
}

/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch')
{
    $code = empty($_REQUEST['extension_code'])? '' : trim($_REQUEST['extension_code']);

    /* 取得要操作的商品编号 */
    $goods_id = !empty($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;

    if (isset($_POST['type']))
    {
        /* 放入回收站 */
        if ($_POST['type'] == 'trash')
        {
            /* 检查权限 */
            admin_priv('remove_back');

            update_goods($goods_id, 'is_delete', '1');

            /* 记录日志 */
            admin_log('', 'batch_trash', 'goods');
        }
        /* 上架 */
        elseif ($_POST['type'] == 'on_sale')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_on_sale', '1');
        }

        /* 下架 */
        elseif ($_POST['type'] == 'not_on_sale')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_on_sale', '0');
        }

        /* 设为精品 */
        elseif ($_POST['type'] == 'best')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_best', '1');
        }

        /* 取消精品 */
        elseif ($_POST['type'] == 'not_best')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_best', '0');
        }

        /* 设为新品 */
        elseif ($_POST['type'] == 'new')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_new', '1');
        }

        /* 取消新品 */
        elseif ($_POST['type'] == 'not_new')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_new', '0');
        }

        /* 设为热销 */
        elseif ($_POST['type'] == 'hot')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_hot', '1');
        }

        /* 取消热销 */
        elseif ($_POST['type'] == 'not_hot')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_hot', '0');
        }

        /* 转移到分类 */
        elseif ($_POST['type'] == 'move_to')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'cat_id', $_POST['target_cat']);
        }

        /* 转移到供货商 */
        elseif ($_POST['type'] == 'suppliers_move_to')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'suppliers_id', $_POST['suppliers_id']);
        }

        /* 还原 */
        elseif ($_POST['type'] == 'restore')
        {
            /* 检查权限 */
            admin_priv('remove_back');

            update_goods($goods_id, 'is_delete', '0');

            /* 记录日志 */
            admin_log('', 'batch_restore', 'goods');
        }
        /* 删除 */
        elseif ($_POST['type'] == 'drop')
        {
            /* 检查权限 */
            admin_priv('remove_back');

            delete_goods($goods_id);

            /* 记录日志 */
            admin_log('', 'batch_remove', 'goods');
        }
		
			/* 审核通过 */
        elseif ($_POST['type'] == 'pass_audit')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'supplier_status', '1');
        }
		/* 未审核 */
        elseif ($_POST['type'] == 'not_audit')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'supplier_status', '0');
        }
		/* 审核未通过 */
        elseif ($_POST['type'] == 'not_pass_audit')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'supplier_status', '-1');
        }



		
    }
	

    /* 清除缓存 */
    clear_cache_files();

    if ($_POST['type'] == 'drop' || $_POST['type'] == 'restore')
    {
        $link[] = array('href' => 'goods.php?act=trash', 'text' => $_LANG['11_goods_trash']);
    }
    else
    {
        if($_REQUEST['supp'])
		{
			$link[] = list_link(true, $code, $_REQUEST['supp']);
		}
		else
		{
			$link[] = list_link(true, $code);
		}
    }
    sys_msg($_LANG['batch_handle_ok'], 0, $link);
}

/*------------------------------------------------------ */
//-- 显示图片
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'show_image')
{

    if (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0)
    {
        $img_url = $_GET['img_url'];
    }
    else
    {
        if (strpos($_GET['img_url'], 'http://') === 0)
        {
            $img_url = $_GET['img_url'];
        }
        else
        {
            $img_url = '../' . $_GET['img_url'];
        }
    }
    $smarty->assign('img_url', $img_url);
    $smarty->display('goods_show_image.htm');
}

/*------------------------------------------------------ */
//-- 修改商品名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_name')
{
    check_authz_json('goods_manage');

    $goods_id   = intval($_POST['id']);
    $goods_name = json_str_iconv(trim($_POST['val']));

    if ($exc->edit("goods_name = '$goods_name', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result(stripslashes($goods_name));
    }
}

/*------------------------------------------------------ */
//-- 修改商品货号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_sn')
{
    check_authz_json('goods_manage');

    $goods_id = intval($_POST['id']);
    $goods_sn = json_str_iconv(trim($_POST['val']));

    /* 检查是否重复 */
    if (!$exc->is_only('goods_sn', $goods_sn, $goods_id))
    {
        make_json_error($_LANG['goods_sn_exists']);
    }
    $sql="SELECT goods_id FROM ". $ecs->table('products')."WHERE product_sn='$goods_sn'";
    if($db->getOne($sql))
    {
        make_json_error($_LANG['goods_sn_exists']);
    }
    if ($exc->edit("goods_sn = '$goods_sn', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result(stripslashes($goods_sn));
    }
}

elseif ($_REQUEST['act'] == 'check_goods_sn')
{
    check_authz_json('goods_manage');

    $goods_id = intval($_REQUEST['goods_id']);
    $goods_sn = htmlspecialchars(json_str_iconv(trim($_REQUEST['goods_sn'])));

    /* 检查是否重复 */
    if (!$exc->is_only('goods_sn', $goods_sn, $goods_id))
    {
        make_json_error($_LANG['goods_sn_exists']);
    }
    if(!empty($goods_sn))
    {
        $sql="SELECT goods_id FROM ". $ecs->table('products')."WHERE product_sn='$goods_sn'";
        if($db->getOne($sql))
        {
            make_json_error($_LANG['goods_sn_exists']);
        }
    }
    make_json_result('');
}
elseif ($_REQUEST['act'] == 'check_products_goods_sn')
{
    check_authz_json('goods_manage');

    $goods_id = intval($_REQUEST['goods_id']);
    $goods_sn = json_str_iconv(trim($_REQUEST['goods_sn']));
    $products_sn=explode('||',$goods_sn);
    if(!is_array($products_sn))
    {
        make_json_result('');
    }
    else
    {
        foreach ($products_sn as $val)
        {
            if(empty($val))
            {
                 continue;
            }
            if(is_array($int_arry))
            {
                if(in_array($val,$int_arry))
                {
                     make_json_error($val.$_LANG['goods_sn_exists']);
                }
            }
            $int_arry[]=$val;
            if (!$exc->is_only('goods_sn', $val, '0'))
            {
                make_json_error($val.$_LANG['goods_sn_exists']);
            }
            $sql="SELECT goods_id FROM ". $ecs->table('products')."WHERE product_sn='$val'";
            if($db->getOne($sql))
            {
                make_json_error($val.$_LANG['goods_sn_exists']);
            }
        }
    }
    /* 检查是否重复 */
    make_json_result('');
}

/*------------------------------------------------------ */
//-- 修改商品价格
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_price')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $goods_price    = floatval($_POST['val']);
    $price_rate     = floatval($_CFG['market_price_rate'] * $goods_price);
	
	$sql_zk = "SELECT promote_price FROM " . $ecs->table('goods') . " WHERE goods_id = " . $goods_id;
	$promote_price = $db->getOne($sql_zk);
	if ($promote_price == 0)
	{
		$zhekou = 10.0;
	}
	else
	{
		$zhekou = (number_format(($promote_price/$goods_price),2))*10;
	}

    if ($goods_price < 0 || $goods_price == 0 && $_POST['val'] != "$goods_price")
    {
        make_json_error($_LANG['shop_price_invalid']);
    }
    else
    {
        if ($exc->edit("zhekou = '$zhekou', shop_price = '$goods_price', market_price = '$price_rate', last_update=" .gmtime(), $goods_id))
        {
		/* 代码增加_start  By  www.68ecshop.com */
		sendsms_pricecut($goods_id);			
		/* 代码增加_end  By  www.68ecshop.com */

            clear_cache_files();
            make_json_result(number_format($goods_price, 2, '.', ''));
        }
    }
}

/*------------------------------------------------------ */
//-- 修改商品库存数量
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_number')
{
    check_authz_json('goods_manage');

    $goods_id   = intval($_POST['id']);
    $goods_num  = intval($_POST['val']);


		if($goods_num>0)
	{
		$sms_on = $_CFG['sms_goods_stockout'];
		
		$dispose = "SELECT email, tel, is_dispose FROM " .$ecs->table('booking_goods'). " WHERE  goods_id = '$goods_id'";
        $email   = $db->getAll($dispose);
		$gname = "SELECT goods_name FROM " .$ecs->table('goods'). " WHERE  goods_id = '$goods_id'";
		$msg_goods_name = $db->getOne($gname);
		$msg_goods_id = $goods_id;
		$msg_goods_url = build_uri('goods', array('gid'=>$msg_goods_id), $msg_goods_name);
	    $msg_goods_url = $GLOBALS['ecs']->url(). $msg_goods_url;
		$email_content = '您登记的商品 '. $msg_goods_name .' 已经到货，您可点击下面链接直接进入商品页面浏览或购买！<br><a href="'. $msg_goods_url.'">'.$msg_goods_url.'</a>';
		$sms_content   = sprintf($_CFG['sms_goods_stockout_tpl'],$msg_goods_name,$msg_goods_url,$_CFG['sms_sign']);
		//$sms_content   = '您登记的商品 '. $msg_goods_name .' 已经到货，您可点击下面链接直接进入商品页面浏览或购买！'.$msg_goods_url.'';
		 for($i=0;$i<count($email);$i++)
		{
			if($email[$i]['is_dispose']==0)
			{
				send_mail('', $email[$i]['email'], '您登记的商品'. $msg_goods_name .'已经到货', $email_content, 1);
				$sql = "UPDATE " . $ecs->table('booking_goods') . " SET is_dispose = 1 WHERE  goods_id = '$goods_id'";
                $db->query($sql);
			}
		}

		if($sms_on>0)
		{
			$a=$_SESSION;
			include_once('../send.php');
			foreach($a as $key=>$value)//sms.php会清空SESSION 先将SESSION存到变量中然后遍历出来
			{
				$_SESSION[$key]=$value;
			}
			for($i=0;$i<count($email);$i++)
			{
				if($email[$i]['is_dispose']==0)
				{
					$r=sendSMS($email[$i]['tel'], $sms_content);
					if($r==true)
					{
						$sql = "UPDATE " . $ecs->table('booking_goods') . " SET is_dispose = 2 WHERE  goods_id = '$goods_id'";
						$db->query($sql);
					}	
					else
					{
						$sql = "UPDATE " . $ecs->table('booking_goods') . " SET is_dispose = 3 WHERE  goods_id = '$goods_id'";
						$db->query($sql);
					}
				}			    					
			} 
		}	
	}
	
	
    if($goods_num < 0 || $goods_num == 0 && $_POST['val'] != "$goods_num")
    {
        make_json_error($_LANG['goods_number_error']);
    }

    if(check_goods_product_exist($goods_id) == 1)
    {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['cannot_goods_number']);
    }

    if ($exc->edit("goods_number = '$goods_num', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($goods_num);
    }
	
}

/*------------------------------------------------------ */
//-- 修改上架状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_on_sale')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $on_sale        = intval($_POST['val']);

	/* 代码增加_start  By  www.68ecshop.com */
	$sql="select supplier_id,supplier_status from ". $ecs->table('goods') ." where goods_id='$goods_id' ";
	$supplier_row =$db->getRow($sql);
	if ($supplier_row['supplier_id']>0 && $supplier_row['supplier_status'] <=0 )
	{
		make_json_error('对不起，该商品还未审核通过！不能上架！');
	}
	/* 代码增加_end  By  www.68ecshop.com */

    if ($exc->edit("is_on_sale = '$on_sale', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($on_sale);
    }
}

/*------------------------------------------------------ */
//-- 修改精品推荐状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_best')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $is_best        = intval($_POST['val']);

    if ($exc->edit("is_best = '$is_best', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($is_best);
    }
}

/*------------------------------------------------------ */
//-- 修改新品推荐状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_new')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $is_new         = intval($_POST['val']);

    if ($exc->edit("is_new = '$is_new', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($is_new);
    }
}

/*------------------------------------------------------ */
//-- 修改热销推荐状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_hot')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $is_hot         = intval($_POST['val']);

    if ($exc->edit("is_hot = '$is_hot', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($is_hot);
    }
}
/*------------------------------------------------------ */
//-- 修改商品审核状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_status')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $supplier_status = intval($_POST['val']);

    if ($exc->edit("supplier_status = '$supplier_status', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($supplier_status);
    }
}

/*------------------------------------------------------ */
//-- 修改商品排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $sort_order     = intval($_POST['val']);

    if ($exc->edit("sort_order = '$sort_order', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($sort_order);
    }
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $is_delete = empty($_REQUEST['is_delete']) ? 0 : intval($_REQUEST['is_delete']);
    $code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    
    
	if(intval($_REQUEST['supp'])>0){
		/* 代码增加_start  By www.68ecshop.com */
		$supplier_list_name=array();
		$sql_supplier = "select supplier_id, supplier_name FROM " . $GLOBALS['ecs']->table("supplier") . " where status='1' order by supplier_id ";
		$res_supplier = $db->query($sql_supplier);
		while($row_supplier=$db->fetchRow($res_supplier))
		{
			$supplier_list_name[$row_supplier['supplier_id']] = $row_supplier['supplier_name'];
		}
		//$suppliers_exists = count($supplier_list_name)>0 ? 1 : 0;
		$smarty->assign('suppliers_exists', 1);//$suppliers_exists);
		$smarty->assign('suppliers_list_name', $supplier_list_name);
		$supplier_status_list =array('0'=>'未审核', '1'=>'审核通过', '-1'=>'审核未通过');
		$smarty->assign('supplier_status_list', $supplier_status_list);
		/* 代码增加_end  By www.68ecshop.com */
    }
    
    
    $goods_list = virtual_goods_list($is_delete, ($code=='') ? 1 : 0);

    $handler_list = array();
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'img'=>'icon_send_bonus.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'img'=>'icon_add.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'img'=>'icon_output.gif');

    if (isset($handler_list[$code]))
    {
        $smarty->assign('add_handler',      $handler_list[$code]);
    }
    $smarty->assign('code',         $code);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('list_type',    $is_delete ? 'trash' : 'goods');
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();
    $smarty->assign('specifications', $specifications);

    $tpl = $is_delete ? 'goods_trash.htm' : 'virtual_goods_list.htm';

    make_json_result($smarty->fetch($tpl), '',
        array('filter' => $goods_list['filter'], 'page_count' => $goods_list['page_count']));
}

/*------------------------------------------------------ */
//-- 放入回收站
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    $goods_id = intval($_REQUEST['id']);

    /* 检查权限 */
    check_authz_json('remove_back');

    if ($exc->edit("is_delete = 1", $goods_id))
    {
        clear_cache_files();
        $goods_name = $exc->get_name($goods_id);

        admin_log(addslashes($goods_name), 'trash', 'goods'); // 记录日志

        $url = 'goods.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

        ecs_header("Location: $url\n");
        exit;
    }
}

/*------------------------------------------------------ */
//-- 还原回收站中的商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'restore_goods')
{
    $goods_id = intval($_REQUEST['id']);

    check_authz_json('remove_back'); // 检查权限

    $exc->edit("is_delete = 0, add_time = '" . gmtime() . "'", $goods_id);
    clear_cache_files();

    $goods_name = $exc->get_name($goods_id);

    admin_log(addslashes($goods_name), 'restore', 'goods'); // 记录日志

    $url = 'goods.php?act=query&' . str_replace('act=restore_goods', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 彻底删除商品
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_goods')
{
    // 检查权限
    check_authz_json('remove_back');

    // 取得参数
    $goods_id = intval($_REQUEST['id']);
    if ($goods_id <= 0)
    {
        make_json_error('invalid params');
    }

    /* 取得商品信息 */
    $sql = "SELECT goods_id, goods_name, is_delete, is_real, goods_thumb, goods_desc, " . //代码修改  By  www.ecshop68.com 
                "goods_img, original_img " .
            "FROM " . $ecs->table('goods') .
            " WHERE goods_id = '$goods_id'";
    $goods = $db->getRow($sql);
    if (empty($goods))
    {
        make_json_error($_LANG['goods_not_exist']);
    }

    if ($goods['is_delete'] != 1)
    {
        make_json_error($_LANG['goods_not_in_recycle_bin']);
    }

	/* 代码增加_start  By  www.ecshop68.com */
	$pattern = "/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"]/";
    preg_match_all($pattern, $goods['goods_desc'], $DesImgs );
	$domain_www_ecshop68_com = $ecs->get_domain();
	$fullurl_www_ecshop68_com = $ecs->url();
	$mainurl_www_ecshop68_com = str_replace($domain_www_ecshop68_com, "", $fullurl_www_ecshop68_com);
	$mainurl_www_ecshop68_com = str_replace("/", "\/", $mainurl_www_ecshop68_com);
	//echo $mainurl_www_ecshop68_com;
	//echo "<pre>";
	//print_r($DesImgs[1] );
	//echo "</pre>";	
	foreach ($DesImgs[1] AS $img_del_www_ecshop68_com)
	{
		//echo $img_del_www_ecshop68_com."<br />";		
		//$final_img_del_www_ecshop68_com = preg_replace("/". $mainurl_www_ecshop68_com ."/i", "../", $img_del_www_ecshop68_com, 1);
		//echo $final_img_del_www_ecshop68_com."<br />";
		//删除商品描述的图片
		$stt = explode('../../',$img_del_www_ecshop68_com);
		foreach($stt as $str){
			}
		@unlink($str);

	//	@unlink($final_img_del_www_ecshop68_com);
	}
	//exit;
	/* 代码增加_end  By  www.ecshop68.com */

    /* 删除商品图片和轮播图片 */
    if (!empty($goods['goods_thumb']))
    {
        @unlink('../' . $goods['goods_thumb']);
    }
    if (!empty($goods['goods_img']))
    {
        @unlink('../' . $goods['goods_img']);
    }
    if (!empty($goods['original_img']))
    {
        @unlink('../' . $goods['original_img']);
    }
    /* 删除商品 */
    $exc->drop($goods_id);

    /* 删除商品的货品记录 */
    $sql = "DELETE FROM " . $ecs->table('products') .
            " WHERE goods_id = '$goods_id'";
    $db->query($sql);
	
	 /* 删除条形码 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('bar_code') .
            " WHERE goods_id = '$goods_id'" ;
    $db->query($sql);
	
    /* 记录日志 */
    admin_log(addslashes($goods['goods_name']), 'remove', 'goods');

    /* 删除商品相册 */
    $sql = "SELECT img_url, thumb_url, img_original " .
            "FROM " . $ecs->table('goods_gallery') .
            " WHERE goods_id = '$goods_id'";
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        if (!empty($row['img_url']))
        {
            @unlink('../' . $row['img_url']);
        }
        if (!empty($row['thumb_url']))
        {
            @unlink('../' . $row['thumb_url']);
        }
        if (!empty($row['img_original']))
        {
            @unlink('../' . $row['img_original']);
        }
    }

    $sql = "DELETE FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);

    /* 删除相关表记录 */
    $sql = "DELETE FROM " . $ecs->table('collect_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('goods_article') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('goods_attr') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('goods_cat') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('member_price') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('group_goods') . " WHERE parent_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('group_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('link_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('link_goods') . " WHERE link_goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('tag') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('comment') . " WHERE comment_type = 0 AND id_value = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('collect_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('booking_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('goods_activity') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);

    /* 如果不是实体商品，删除相应虚拟商品记录 */
    if ($goods['is_real'] != 1)
    {
        $sql = "DELETE FROM " . $ecs->table('virtual_card') . " WHERE goods_id = '$goods_id'";
        if (!$db->query($sql, 'SILENT') && $db->errno() != 1146)
        {
            die($db->error());
        }
    }

    clear_cache_files();
    $url = 'goods.php?act=query&' . str_replace('act=drop_goods', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");

    exit;
}
/*条形码拼接*/
elseif($_REQUEST['act'] == 'get_txm')
{
	
	$good_id = $_GET['goods_id'];//商品id
	$id = $_GET['id'];//属性上级id
	$value = $_GET['value'];//属性值
	$sql = "SELECT attr_id FROM" . $ecs->table('attribute') ."WHERE cat_id='$id' AND attr_txm=1";
	$con =	$db->getAll($sql);
	$array_txm = array();
	if(count($con)>0){
		foreach($con as $k => $v){
			if(isset($_GET['attr_'.$v['attr_id']]) && !empty($_GET['attr_'.$v['attr_id']])){
				$array_txm[$v['attr_id']] = $_GET['attr_'.$v['attr_id']];
			}
		}
	}
	
	$stre = '';
	switch (count($con))
    {	
        case '1'://属性值是1 的时候
			if(count($array_txm) == count($con))
			{
				foreach($array_txm  as $value)
				{
					$arr = explode(',',$value);//用,号切割字符串
					$str = array_filter($arr);// 去除数组中的空值
					$attr = array_unique($str);//去除数组中重复的值
					$brr[] = $attr;
					foreach($brr as $value){
						foreach($value as $val){
							$sst[] = $val;
						}
					}
				}
			}
			 else
			{
				make_json_result("");
			}
            break;
		case '2'://属性值是2的时候
			if(count($array_txm) == count($con))
			{
				foreach($array_txm  as $value){
					$arr = explode(',',$value);//用,号切割字符串
					$str = array_filter($arr);// 去除数组中的空值
					$attr = array_unique($str);//去除数组中重复的值
					$brr[] = $attr;
				}
				$add = array_pop($brr);//弹出数组最后一个值
				foreach($brr as $value){
					foreach($value as $v){
						foreach($add as $val){
							$sst[] = $v.'+'.$val;
						}
					}
				}
			}
			 else
			{
				make_json_result("");
			}
			break;
		case '3'://属性值是3的时候
			if(count($array_txm) == count($con))
			{
				foreach($array_txm as $value){
					$arr = explode(',',$value);//用,号切割字符串
					$str = array_filter($arr);// 去除数组中的空值
					$attr = array_unique($str);//去除数组中重复的值
					$brr[] = $attr;
				}
				$add = array_pop($brr);//弹出数组最后一个
				$ass = array_pop($brr);//弹出数组最有一个
				foreach($brr as $value){
					foreach($value as $val){
						foreach($add as $a){
							foreach($ass as $s){
								$sst[] = $val.'+'.$a.'+'.$s;
							}
						}
					}
				}
			}
			 else
			{
				make_json_result("");
			}
			break;
		case '4':
			if(count($array_txm) == count($con))
			{
				foreach($array_txm as $value){
					$arr = explode(',',$value);//用,号切割字符串
					$str = array_filter($arr);// 去除数组中的空值
					$attr = array_unique($str);//去除数组中重复的值
					$brr[] = $attr;
				}
				$add = array_pop($brr);//弹出数组最后一个
				$ass = array_pop($brr);//弹出数组最后一个
				$aww = array_pop($brr);//弹出数组最后一个
				foreach($brr as $value){
					foreach($value as $valu){
						foreach($add as $val){
							foreach($ass as $va){
								foreach($aww as $v){
									$sst[] =$valu.'+'.$val.'+'.$va.'+'.$v;
								}
							}
						}
					}
				}
			}
			 else
			{
				make_json_result("");
			}
			break;
		case '5':
			if(count($array_txm) == count($con))
			{
				foreach($array_txm as $value){
					$arr =explode(',',$value);//用,号切割字符串
					$str = array_filter($arr);// 去除数组中的空值
					$attr = array_unique($str);//去除数组中重复的值
					$brr[] = $attr;
				}
				$add = array_pop($brr);//弹出数组最后一个
				$ass = array_pop($brr);//弹出数组最后一个
				$aqq = array_pop($brr);//弹出数组最后一个
				$aee = array_pop($brr);//弹出数组最后一个
				foreach($brr as $value){
					foreach($value as $value){
						foreach($add as $valu){
							foreach($ass as $val){
								foreach($aqq as $va){
									foreach($aee as $v){
										$sst[] = $value.'+'.$valu.'+'.$val.'+'.$va.'+'.$v;
									}
								}
							}
						}
					}
				}
			}
			 else
			{
				make_json_result("");
			}
			break;
		case '6':
			if(count($array_txm) == count($con))
			{
				foreach($array_txm as $value){
					$arr = explode(',',$value);//用,号切割字符串
					$str = array_filter($arr);// 去除数组中的空值
					$attr = array_unique($str);//去除数组中重复的值
					$brr[] = $attr;
				}
				$add = array_pop($brr);//弹出数组最后一个
				$aqq = array_pop($brr);//弹出数组最后一个
				$ass = array_pop($brr);//弹出数组最后一个
				$aww = array_pop($brr);//弹出数组最后一个
				$aee = array_pop($brr);//弹出数组最后一个
				foreach($brr as $value){
					foreach($value as $value){
						foreach($add as $valu){
							foreach($aqq as $val){
								foreach($ass as $va){
									foreach($aww as $v){
										foreach($aee as $values){
											$sst[] = $value.'+'.$valu.'+'.$val.'+'.$va.'+'.$v.'+'.$values;
										}
									}
								}
							}
						}
					}
				}
			}
			 else
			{
				make_json_result("");
			}
			break;
	}

	foreach($sst as $key=>$value){
	
		$stre .='<tr><td class="label">条形码</td><td><input type="hidden" name="txm_shu[]" value='.$value.'>'.$value.'<td/><td><input type="text" name="tiaoxingm[]" value=""></td></tr>';
	}
	make_json_result($stre);
}

/*------------------------------------------------------ */
//-- 切换商品类型
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'get_attr')
{
    check_authz_json('goods_manage');

    $goods_id   = empty($_GET['goods_id']) ? 0 : intval($_GET['goods_id']);
    $goods_type = empty($_GET['goods_type']) ? 0 : intval($_GET['goods_type']);

    $content    = build_attr_html($goods_type, $goods_id,$bar_code);

    make_json_result($content);
}

/*------------------------------------------------------ */
//-- 删除图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_image')
{
    check_authz_json('goods_manage');

    $img_id = empty($_REQUEST['img_id']) ? 0 : intval($_REQUEST['img_id']);

    /* 删除图片文件 */
    $sql = "SELECT img_url, thumb_url, img_original " .
            " FROM " . $GLOBALS['ecs']->table('goods_gallery') .
            " WHERE img_id = '$img_id'";
    $row = $GLOBALS['db']->getRow($sql);

    if ($row['img_url'] != '' && is_file('../' . $row['img_url']))
    {
        @unlink('../' . $row['img_url']);
    }
    if ($row['thumb_url'] != '' && is_file('../' . $row['thumb_url']))
    {
        @unlink('../' . $row['thumb_url']);
    }
    if ($row['img_original'] != '' && is_file('../' . $row['img_original']))
    {
        @unlink('../' . $row['img_original']);
    }

    /* 删除数据 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('goods_gallery') . " WHERE img_id = '$img_id' LIMIT 1";
    $GLOBALS['db']->query($sql);

    clear_cache_files();
    make_json_result($img_id);
}

/*------------------------------------------------------ */
//-- 搜索商品，仅返回名称及ID
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'get_goods_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    $arr = get_goods_list($filters);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value' => $val['goods_id'],
                        'text' => $val['goods_name'],
                        'data' => $val['shop_price']);
    }

    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 把商品加入关联
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_link_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $linked_array   = $json->decode($_GET['add_ids']);
    $linked_goods   = $json->decode($_GET['JSON']);
    $goods_id       = $linked_goods[0];
    $is_double      = $linked_goods[1] == true ? 0 : 1;

    foreach ($linked_array AS $val)
    {
        if ($is_double)
        {
            /* 双向关联 */
            $sql = "INSERT INTO " . $ecs->table('link_goods') . " (goods_id, link_goods_id, is_double, admin_id) " .
                    "VALUES ('$val', '$goods_id', '$is_double', '$_SESSION[admin_id]')";
            $db->query($sql, 'SILENT');
        }

        $sql = "INSERT INTO " . $ecs->table('link_goods') . " (goods_id, link_goods_id, is_double, admin_id) " .
                "VALUES ('$goods_id', '$val', '$is_double', '$_SESSION[admin_id]')";
        $db->query($sql, 'SILENT');
    }

    $linked_goods   = get_linked_goods($goods_id);
    $options        = array();

    foreach ($linked_goods AS $val)
    {
        $options[] = array('value'  => $val['goods_id'],
                        'text'      => $val['goods_name'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($options);
}

/*------------------------------------------------------ */
//-- 删除关联商品
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_link_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $drop_goods     = $json->decode($_GET['drop_ids']);
    $drop_goods_ids = db_create_in($drop_goods);
    $linked_goods   = $json->decode($_GET['JSON']);
    $goods_id       = $linked_goods[0];
    $is_signle      = $linked_goods[1];

    if (!$is_signle)
    {
        $sql = "DELETE FROM " .$ecs->table('link_goods') .
                " WHERE link_goods_id = '$goods_id' AND goods_id " . $drop_goods_ids;
    }
    else
    {
        $sql = "UPDATE " .$ecs->table('link_goods') . " SET is_double = 0 ".
                " WHERE link_goods_id = '$goods_id' AND goods_id " . $drop_goods_ids;
    }
    if ($goods_id == 0)
    {
        $sql .= " AND admin_id = '$_SESSION[admin_id]'";
    }
    $db->query($sql);

    $sql = "DELETE FROM " .$ecs->table('link_goods') .
            " WHERE goods_id = '$goods_id' AND link_goods_id " . $drop_goods_ids;
    if ($goods_id == 0)
    {
        $sql .= " AND admin_id = '$_SESSION[admin_id]'";
    }
    $db->query($sql);

    $linked_goods = get_linked_goods($goods_id);
    $options      = array();

    foreach ($linked_goods AS $val)
    {
        $options[] = array(
                        'value' => $val['goods_id'],
                        'text'  => $val['goods_name'],
                        'data'  => '');
    }

    clear_cache_files();
    make_json_result($options);
}

/*------------------------------------------------------ */
//-- 增加一个配件
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add_group_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $fittings   = $json->decode($_GET['add_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];
    $price      = $arguments[1];

    foreach ($fittings AS $val)
    {
        $sql = "INSERT INTO " . $ecs->table('group_goods') . " (parent_id, goods_id, goods_price, admin_id) " .
                "VALUES ('$goods_id', '$val', '$price', '$_SESSION[admin_id]')";
        $db->query($sql, 'SILENT');
    }

    $arr = get_group_goods($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['goods_id'],
                        'text'      => $val['goods_name'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 删除一个配件
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'drop_group_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $fittings   = $json->decode($_GET['drop_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];
    $price      = $arguments[1];

    $sql = "DELETE FROM " .$ecs->table('group_goods') .
            " WHERE parent_id='$goods_id' AND " .db_create_in($fittings, 'goods_id');
    if ($goods_id == 0)
    {
        $sql .= " AND admin_id = '$_SESSION[admin_id]'";
    }
    $db->query($sql);

    $arr = get_group_goods($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['goods_id'],
                        'text'      => $val['goods_name'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 搜索文章
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'get_article_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters =(array) $json->decode(json_str_iconv($_GET['JSON']));

    $where = " WHERE cat_id > 0 ";
    if (!empty($filters['title']))
    {
        $keyword  = trim($filters['title']);
        $where   .=  " AND title LIKE '%" . mysql_like_quote($keyword) . "%' ";
    }

    $sql        = 'SELECT article_id, title FROM ' .$ecs->table('article'). $where.
                  'ORDER BY article_id DESC LIMIT 50';
    $res        = $db->query($sql);
    $arr        = array();

    while ($row = $db->fetchRow($res))
    {
        $arr[]  = array('value' => $row['article_id'], 'text' => $row['title'], 'data'=>'');
    }

    make_json_result($arr);
}

/*------------------------------------------------------ */
//-- 添加关联文章
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add_goods_article')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $articles   = $json->decode($_GET['add_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];

    foreach ($articles AS $val)
    {
        $sql = "INSERT INTO " . $ecs->table('goods_article') . " (goods_id, article_id, admin_id) " .
                "VALUES ('$goods_id', '$val', '$_SESSION[admin_id]')";
        $db->query($sql);
    }

    $arr = get_goods_articles($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['article_id'],
                        'text'      => $val['title'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 删除关联文章
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_goods_article')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $articles   = $json->decode($_GET['drop_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];

    $sql = "DELETE FROM " .$ecs->table('goods_article') . " WHERE " . db_create_in($articles, "article_id") . " AND goods_id = '$goods_id'";
    $db->query($sql);

    $arr = get_goods_articles($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['article_id'],
                        'text'      => $val['title'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 货品列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'product_list')
{
    admin_priv('goods_manage');
    $supp = isset($_REQUEST['supp']) && $_REQUEST['supp']>0 ? '&supp=1' : '';
    
    /* 是否存在商品id */
    if (empty($_GET['goods_id']))
    {
        $link[] = array('href' => 'virtual_goods.php?act=list&extension_code=virtual_good'.$supp, 'text' => $_LANG['cannot_found_goods']);
        sys_msg($_LANG['cannot_found_goods'], 1, $link);
    }
    else
    {
        $goods_id = intval($_GET['goods_id']);
    }

    /* 取出商品信息 */
    $sql = "SELECT goods_sn, goods_name, goods_type, shop_price FROM " . $ecs->table('goods') . " WHERE goods_id = '$goods_id'";
    $goods = $db->getRow($sql);
    if (empty($goods))
    {
        $link[] = array('href' => 'virtual_goods.php?act=list&extension_code=virtual_good'.$supp, 'text' => $_LANG['01_goods_list']);
        sys_msg($_LANG['cannot_found_goods'], 1, $link);
    }
    $smarty->assign('sn', sprintf($_LANG['good_goods_sn'], $goods['goods_sn']));
    $smarty->assign('price', sprintf($_LANG['good_shop_price'], $goods['shop_price']));
    $smarty->assign('goods_name', sprintf($_LANG['products_title'], $goods['goods_name']));
    $smarty->assign('goods_sn', sprintf($_LANG['products_title_2'], $goods['goods_sn']));


    /* 获取商品规格列表 */
    $attribute = get_goods_specifications_list($goods_id);
    if (empty($attribute))
    {
        $link[] = array('href' => 'virtual_goods.php?act=edit&extension_code=virtual_good&goods_id=' . $goods_id, 'text' => $_LANG['edit_goods']);
        sys_msg($_LANG['not_exist_goods_attr'], 1, $link);
    }
    foreach ($attribute as $attribute_value)
    {
        //转换成数组
        $_attribute[$attribute_value['attr_id']]['attr_values'][] = $attribute_value['attr_value'];
        $_attribute[$attribute_value['attr_id']]['attr_id'] = $attribute_value['attr_id'];
        $_attribute[$attribute_value['attr_id']]['attr_name'] = $attribute_value['attr_name'];
    }
    $attribute_count = count($_attribute);

    $smarty->assign('attribute_count',          $attribute_count);
    $smarty->assign('attribute_count_3',        ($attribute_count + 3));
    $smarty->assign('attribute',                $_attribute);
    $smarty->assign('product_sn',               $goods['goods_sn'] . '_');
    $smarty->assign('product_number',           $_CFG['default_storage']);

    /* 取商品的货品 */
    $product = product_list($goods_id, '');
    $smarty->assign('supp',      empty($_REQUEST['supp'])?"":  intval($_REQUEST['supp']));
    $smarty->assign('ur_here',      $_LANG['18_product_list']);
    $smarty->assign('action_link',  array('href' => 'virtual_goods.php?act=list&extension_code=virtual_good'.$supp, 'text' => $_LANG['01_goods_list']));
    $smarty->assign('product_list', $product['product']);
    $smarty->assign('product_null', empty($product['product']) ? 0 : 1);
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);
    $smarty->assign('goods_id',     $goods_id);
    $smarty->assign('filter',       $product['filter']);
    $smarty->assign('full_page',    1);

    /* 显示商品列表页面 */
    assign_query_info();

    $smarty->display('virtual_product_info.htm');
}

/*------------------------------------------------------ */
//-- 货品排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'product_query')
{
    /* 是否存在商品id */
    if (empty($_REQUEST['goods_id']))
    {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['cannot_found_goods']);
    }
    else
    {
        $goods_id = intval($_REQUEST['goods_id']);
    }

    /* 取出商品信息 */
    $sql = "SELECT goods_sn, goods_name, goods_type, shop_price FROM " . $ecs->table('goods') . " WHERE goods_id = '$goods_id'";
    $goods = $db->getRow($sql);
    if (empty($goods))
    {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['cannot_found_goods']);
    }
    $smarty->assign('sn', sprintf($_LANG['good_goods_sn'], $goods['goods_sn']));
    $smarty->assign('price', sprintf($_LANG['good_shop_price'], $goods['shop_price']));
    $smarty->assign('goods_name', sprintf($_LANG['products_title'], $goods['goods_name']));
    $smarty->assign('goods_sn', sprintf($_LANG['products_title_2'], $goods['goods_sn']));


    /* 获取商品规格列表 */
    $attribute = get_goods_specifications_list($goods_id);
    if (empty($attribute))
    {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['cannot_found_goods']);
    }
    foreach ($attribute as $attribute_value)
    {
        //转换成数组
        $_attribute[$attribute_value['attr_id']]['attr_values'][] = $attribute_value['attr_value'];
        $_attribute[$attribute_value['attr_id']]['attr_id'] = $attribute_value['attr_id'];
        $_attribute[$attribute_value['attr_id']]['attr_name'] = $attribute_value['attr_name'];
    }
    $attribute_count = count($_attribute);

    $smarty->assign('attribute_count',          $attribute_count);
    $smarty->assign('attribute',                $_attribute);
    $smarty->assign('attribute_count_3',        ($attribute_count + 3));
    $smarty->assign('product_sn',               $goods['goods_sn'] . '_');
    $smarty->assign('product_number',           $_CFG['default_storage']);

    /* 取商品的货品 */
    $product = product_list($goods_id, '');

    $smarty->assign('ur_here', $_LANG['18_product_list']);
    $smarty->assign('action_link', array('href' => 'goods.php?act=list', 'text' => $_LANG['01_goods_list']));
    $smarty->assign('product_list',  $product['product']);
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);
    $smarty->assign('goods_id',    $goods_id);
    $smarty->assign('filter',       $product['filter']);

    /* 排序标记 */
    $sort_flag  = sort_flag($product['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('product_info.htm'), '',
        array('filter' => $product['filter'], 'page_count' => $product['page_count']));
}

/*------------------------------------------------------ */
//-- 货品删除
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'product_remove')
{
    /* 检查权限 */
    check_authz_json('remove_back');

    /* 是否存在商品id */
    if (empty($_REQUEST['id']))
    {
        make_json_error($_LANG['product_id_null']);
    }
    else
    {
        $product_id = intval($_REQUEST['id']);
    }

    /* 货品库存 */
    $product = get_product_info($product_id, 'product_number, goods_id');

    /* 删除货品 */
    $sql = "DELETE FROM " . $ecs->table('products') . " WHERE product_id = '$product_id'";
    $result = $db->query($sql);
    if ($result)
    {
        /* 修改商品库存 */
        if (update_goods_stock($product['goods_id'], $product_number - $product['product_number']))
        {
            //记录日志
            admin_log('', 'update', 'goods');
        }

        //记录日志
        admin_log('', 'trash', 'products');

        $url = 'goods.php?act=product_query&' . str_replace('act=product_remove', '', $_SERVER['QUERY_STRING']);

        ecs_header("Location: $url\n");
        exit;
    }
}

/*------------------------------------------------------ */
//-- 修改货品价格
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_product_sn')
{
    check_authz_json('goods_manage');

    $product_id       = intval($_POST['id']);
    $product_sn       = json_str_iconv(trim($_POST['val']));
    $product_sn       = ($_LANG['n_a'] == $product_sn) ? '' : $product_sn;

    if (check_product_sn_exist($product_sn, $product_id))
    {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['exist_same_product_sn']);
    }

    /* 修改 */
    $sql = "UPDATE " . $ecs->table('products') . " SET product_sn = '$product_sn' WHERE product_id = '$product_id'";
    $result = $db->query($sql);
    if ($result)
    {
        clear_cache_files();
        make_json_result($product_sn);
    }
}

/*------------------------------------------------------ */
//-- 修改货品库存
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_product_number')
{
    check_authz_json('goods_manage');

    $product_id       = intval($_POST['id']);
    $product_number       = intval($_POST['val']);

    /* 货品库存 */
    $product = get_product_info($product_id, 'product_number, goods_id');

    /* 修改货品库存 */
    $sql = "UPDATE " . $ecs->table('products') . " SET product_number = '$product_number' WHERE product_id = '$product_id'";
    $result = $db->query($sql);
    if ($result)
    {
        /* 修改商品库存 */
        if (update_goods_stock($product['goods_id'], $product_number - $product['product_number']))
        {
            clear_cache_files();
            make_json_result($product_number);
        }
    }
}

/*------------------------------------------------------ */
//-- 货品添加 执行
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'product_add_execute')
{
    admin_priv('goods_manage');

    $product['goods_id']        = intval($_POST['goods_id']);
    $product['attr']            = $_POST['attr'];
    $product['product_sn']      = $_POST['product_sn'];
    $product['product_number']  = $_POST['product_number'];

    /* 是否存在商品id */
    if (empty($product['goods_id']))
    {
        sys_msg($_LANG['sys']['wrong'] . $_LANG['cannot_found_goods'], 1, array(), false);
    }

    /* 判断是否为初次添加 */
    $insert = true;
    if (product_number_count($product['goods_id']) > 0)
    {
        $insert = false;
    }

    /* 取出商品信息 */
    $sql = "SELECT goods_sn, goods_name, goods_type, shop_price FROM " . $ecs->table('goods') . " WHERE goods_id = '" . $product['goods_id'] . "'";
    $goods = $db->getRow($sql);
    if (empty($goods))
    {
        sys_msg($_LANG['sys']['wrong'] . $_LANG['cannot_found_goods'], 1, array(), false);
    }

    /*  */
    foreach($product['product_sn'] as $key => $value)
    {
        //过滤
        $product['product_number'][$key] = empty($product['product_number'][$key]) ? (empty($_CFG['use_storage']) ? 0 : $_CFG['default_storage']) : trim($product['product_number'][$key]); //库存

        //获取规格在商品属性表中的id
        foreach($product['attr'] as $attr_key => $attr_value)
        {
            /* 检测：如果当前所添加的货品规格存在空值或0 */
            if (empty($attr_value[$key]))
            {
                continue 2;
            }

            $is_spec_list[$attr_key] = 'true';

            $value_price_list[$attr_key] = $attr_value[$key] . chr(9) . ''; //$key，当前

            $id_list[$attr_key] = $attr_key;
        }
        $goods_attr_id = handle_goods_attr($product['goods_id'], $id_list, $is_spec_list, $value_price_list);

        /* 是否为重复规格的货品 */
        $goods_attr = sort_goods_attr_id_array($goods_attr_id);
        $goods_attr = implode('|', $goods_attr['sort']);
        if (check_goods_attr_exist($goods_attr, $product['goods_id']))
        {
            continue;
            //sys_msg($_LANG['sys']['wrong'] . $_LANG['exist_same_goods_attr'], 1, array(), false);
        }
        //货品号不为空
        if (!empty($value))
        {
            /* 检测：货品货号是否在商品表和货品表中重复 */
            if (check_goods_sn_exist($value))
            {
                continue;
                //sys_msg($_LANG['sys']['wrong'] . $_LANG['exist_same_goods_sn'], 1, array(), false);
            }
            if (check_product_sn_exist($value))
            {
                continue;
                //sys_msg($_LANG['sys']['wrong'] . $_LANG['exist_same_product_sn'], 1, array(), false);
            }
        }

        /* 插入货品表 */
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table('products') . " (goods_id, goods_attr, product_sn, product_number)  VALUES ('" . $product['goods_id'] . "', '$goods_attr', '$value', '" . $product['product_number'][$key] . "')";
        if (!$GLOBALS['db']->query($sql))
        {
            continue;
            //sys_msg($_LANG['sys']['wrong'] . $_LANG['cannot_add_products'], 1, array(), false);
        }

        //货品号为空 自动补货品号
        if (empty($value))
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('products') . "
                    SET product_sn = '" . $goods['goods_sn'] . "g_p" . $GLOBALS['db']->insert_id() . "'
                    WHERE product_id = '" . $GLOBALS['db']->insert_id() . "'";
            $GLOBALS['db']->query($sql);
        }

        /* 修改商品表库存 */
        $product_count = product_number_count($product['goods_id']);
        if (update_goods($product['goods_id'], 'goods_number', $product_count))
        {
            //记录日志
            admin_log($product['goods_id'], 'update', 'goods');
        }
    }

    clear_cache_files();
        $supp = empty($_REQUEST['supp'])?'':'&supp=1';
    /* 返回 */
    if ($insert)
    {
         $link[] = array('href' => 'virtual_goods.php?act=add&extension_code=virtual_good', 'text' => $_LANG['03_goods_add']);
         $link[] = array('href' => 'virtual_goods.php?act=list&extension_code=virtual_good', 'text' => $_LANG['01_goods_list']);
         $link[] = array('href' => 'virtual_goods.php?act=product_list&goods_id=' . $product['goods_id'], 'text' => $_LANG['18_product_list']);
    }
    else
    {
         $link[] = array('href' => 'virtual_goods.php?act=list&extension_code=virtual_good'.$supp, 'text' => $_LANG['01_goods_list']);
         $link[] = array('href' => 'virtual_goods.php?act=edit&extension_code=virtual_good&goods_id=' . $product['goods_id'], 'text' => $_LANG['edit_goods']);
         $link[] = array('href' => 'virtual_goods.php?act=product_list&goods_id=' . $product['goods_id'], 'text' => $_LANG['18_product_list']);
    }
    sys_msg($_LANG['save_products'], 0, $link);
}

/*------------------------------------------------------ */
//-- 货品批量操作
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch_product')
{
    /* 定义返回 */
    $link[] = array('href' => 'goods.php?act=product_list&goods_id=' . $_POST['goods_id'], 'text' => $_LANG['item_list']);

    /* 批量操作 - 批量删除 */
    if ($_POST['type'] == 'drop')
    {
        //检查权限
        admin_priv('remove_back');

        //取得要操作的商品编号
        $product_id = !empty($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;
        $product_bound = db_create_in($product_id);

        //取出货品库存总数
        $sum = 0;
        $goods_id = 0;
        $sql = "SELECT product_id, goods_id, product_number FROM  " . $GLOBALS['ecs']->table('products') . " WHERE product_id $product_bound";
        $product_array = $GLOBALS['db']->getAll($sql);
        if (!empty($product_array))
        {
            foreach ($product_array as $value)
            {
                $sum += $value['product_number'];
            }
            $goods_id = $product_array[0]['goods_id'];

            /* 删除货品 */
            $sql = "DELETE FROM " . $ecs->table('products') . " WHERE product_id $product_bound";
            if ($db->query($sql))
            {
                //记录日志
                admin_log('', 'delete', 'products');
            }

            /* 修改商品库存 */
            if (update_goods_stock($goods_id, -$sum))
            {
                //记录日志
                admin_log('', 'update', 'goods');
            }

            /* 返回 */
            sys_msg($_LANG['product_batch_del_success'], 0, $link);
        }
        else
        {
            /* 错误 */
            sys_msg($_LANG['cannot_found_products'], 1, $link);
        }
    }

    /* 返回 */
    sys_msg($_LANG['no_operation'], 1, $link);
}

/*------------------------------------------------------ */
//-- AJAX获取商品分类
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'ajax_category')
{
	$cat_list = cat_list1(0, $selected, false);

	$json = cat_list_to_json_string($cat_list, $goods['cat_id']);

	print $json;
}

/**
 * 列表链接
 * @param   bool    $is_add         是否添加（插入）
 * @param   string  $extension_code 虚拟商品扩展代码，实体商品为空
 * @param   int     $supp           是否入驻商商品，自营商品默认为空，增加此参数修改提示链接不正确
 * @return  array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true, $extension_code = '', $supp = '')
{
    
    if(!empty($supp))
	{
		$href = 'virtual_goods.php?act=list&extension_code=virtual_good&supp=1';
	}
	else
	{
		$href = 'virtual_goods.php?act=list&extension_code=virtual_good';
	}
	
	if (!empty($extension_code))
    {
        $href .= '&extension_code=' . $extension_code;
    }
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }

    if ($extension_code == 'virtual_card')
    {
        $text = $GLOBALS['_LANG']['50_virtual_card_list'];
    }
    else
    {
        if(!empty($supp))
		{
			$text = $GLOBALS['_LANG']['02_supplier_goods_list'];
		}
		else
		{
			$text = $GLOBALS['_LANG']['01_goods_list'];
		}
    }

    return array('href' => $href, 'text' => $text);
}

/**
 * 添加链接
 * @param   string  $extension_code 虚拟商品扩展代码，实体商品为空
 * @return  array('href' => $href, 'text' => $text)
 */
function add_link($extension_code = '')
{
    $href = 'virtual_goods.php?act=add';
    if (!empty($extension_code))
    {
        $href .= '&extension_code=' . $extension_code;
    }

    if ($extension_code == 'virtual_card')
    {
        $text = $GLOBALS['_LANG']['51_virtual_card_add'];
    }
    else
    {
        $text = $GLOBALS['_LANG']['03_goods_add'];
    }

    return array('href' => $href, 'text' => $text);
}

/**
 * 检查图片网址是否合法
 *
 * @param string $url 网址
 *
 * @return boolean
 */
function goods_parse_url($url)
{
    $parse_url = @parse_url($url);
    return (!empty($parse_url['scheme']) && !empty($parse_url['host']));
}

/**
 * 保存某商品的优惠价格
 * @param   int     $goods_id    商品编号
 * @param   array   $number_list 优惠数量列表
 * @param   array   $price_list  价格列表
 * @return  void
 */
function handle_volume_price($goods_id, $number_list, $price_list)
{
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('volume_price') .
           " WHERE price_type = '1' AND goods_id = '$goods_id'";
    $GLOBALS['db']->query($sql);


    /* 循环处理每个优惠价格 */
    foreach ($price_list AS $key => $price)
    {
        /* 价格对应的数量上下限 */
        $volume_number = $number_list[$key];

        if (!empty($price))
        {
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('volume_price') .
                   " (price_type, goods_id, volume_number, volume_price) " .
                   "VALUES ('1', '$goods_id', '$volume_number', '$price')";
            $GLOBALS['db']->query($sql);
        }
    }
}

/**
 * 修改商品库存
 * @param   string  $goods_id   商品编号，可以为多个，用 ',' 隔开
 * @param   string  $value      字段值
 * @return  bool
 */
function update_goods_stock($goods_id, $value)
{
    if ($goods_id)
    {
        /* $res = $goods_number - $old_product_number + $product_number; */
        $sql = "UPDATE " . $GLOBALS['ecs']->table('goods') . "
                SET goods_number = goods_number + $value,
                    last_update = '". gmtime() ."'
                WHERE goods_id = '$goods_id'";
        $result = $GLOBALS['db']->query($sql);

        /* 清除缓存 */
        clear_cache_files();

        return $result;
    }
    else
    {
        return false;
    }
}


/* 代码增加_start  BY  www.68ecshop.com */

function sendsms_pricecut($goods_id)
{
	include_once('../send.php');
	$min_price_arr = get_min_price($goods_id);
	if($min_price_arr['goods_name'] != '')
	{
		$min_price = $min_price_arr['min_price'];
		$goods_name = $min_price_arr['goods_name'];
		$goods_url = build_uri('goods', array('gid'=>$goods_id), $goods_name);
		$goods_url = $GLOBALS['ecs']->url(). $goods_url;
		$sql="select pricecut_id, mobile,email from ". $GLOBALS['ecs']->table('pricecut') ." where goods_id='$goods_id' and price >= '$min_price' and status=0 ";
		$list= $GLOBALS['db']->getAll($sql);
		if($list)
		{
			for($i = 0;$i < count($list); $i++)
			{
				if($list[$i]['email'] != '')
				{
					$content1 = '您关注的商品 '. $goods_name .' 已经降价，您可点击下面链接直接进入商品页面浏览或购买！<br><a href="'. $goods_url .'">'.$goods_url.'</a>';
					send_mail($_CFG['shop_name'], $list[$i]['email'], '您关注的商品'. $goods_name .'已经降价', $content1, 1);
				} 
				
				//开启降价给客户发短信
				if($GLOBALS['_CFG']['sms_pricecut'] == 1)
				{
					//降价通知短信内容
					$pricecut_content = sprintf($GLOBALS['_CFG']['sms_pricecut_tpl'],$goods_name,$goods_url,$GLOBALS['_CFG']['sms_sign']);
					if($list[$i]['mobile'] != '')
					{
						$res = sendSMS($list[$i]['mobile'],$pricecut_content);
						if($res == true)
						{
							$sql = "UPDATE " . $GLOBALS['ecs']->table('pricecut') . " SET status = 2 WHERE pricecut_id = '" . $list[$i]['pricecut_id'] . "'";
							$GLOBALS['db']->query($sql);
						}
						else
						{
							$sql = "UPDATE " . $GLOBALS['ecs']->table('pricecut') . " SET status = 1 WHERE pricecut_id = '" . $list[$i]['pricecut_id'] . "'";
							$GLOBALS['db']->query($sql);
						}
					}
				}
			} 
		}
	}
}

function get_min_price ($goods_id)
{
     $sql="SELECT g.goods_name, g.goods_type, g.promote_price, g.promote_start_date, g.promote_end_date, ".
				" g.shop_price, a.attr_id,a.attr_name FROM ". $GLOBALS['ecs']->table('goods') .
				" AS g left join ". $GLOBALS['ecs']->table('attribute') ." AS a on g.cat_id=a.cat_id ".
				" WHERE g.goods_id=$goods_id";
	 $res = $GLOBALS['db']->query($sql);
	 $min_price_attr = 0;
	 $arr = array();
	 while($row=$GLOBALS['db']->fetchRow($res))
	 {      
		    $shop_price = $row['shop_price'];
			$promote_price =$row['promote_price'];
			$promote_start_date =$row['promote_start_date'];
			$promote_end_date = $row['promote_end_date'];
			$goods_name = $row['goods_name'];
			$sql = "select min(attr_price) from ". $GLOBALS['ecs']->table('goods_attr') ." where goods_id='$goods_id' and attr_id='$row[attr_id]' ";
			$min_price_temp =$GLOBALS['db']->getOne($sql);
			$min_price_temp = !empty($min_price_temp) ? $min_price_temp : 0;
			$min_price_attr  +=  $min_price_temp;
	 }	 
	 $promote_price = bargain_price($promote_price, $promote_start_date, $promote_end_date);
	 if ($promote_price && $promote_price < $shop_price)
	 {
		 $shop_price = $promote_price;
	 }
	 $arr['min_price']  = $shop_price + $min_price_attr;
	 $arr['goods_name'] = $goods_name;
	 if($arr)
	 {
		 return $arr; 
	 }
}

function bargain_price($price, $start, $end)
{
    if ($price == 0)
    {
        return 0;
    }
    else
    {
        $time = gmtime();
        if ($time >= $start && $time <= $end)
        {
            return $price;
        }
        else
        {
            return 0;
        }
    }
}
/* 代码增加_end  BY  www.68ecshop.com */

/**
 * 将商品分类列表转换成符合zTree标准的JSON格式
 */
function cat_list_to_json($cat_list, $selected = 0)
{
	include_once(ROOT_PATH . 'includes/Pinyin.php');

	$json = '';

	foreach ($cat_list as $k => $cat)
	{
		$id = $cat['cat_id'];
		$pId = $cat['parent_id'];
		$name = $cat['cat_name'];
		//$open = true;

		$name_pinyin = Pinyin($name, 1, 1).$name;

		$json = sprintf('%s{id: %s, pId: %s, name: "%s", name_pinyin: "%s"},', $json, $id, $pId, $name, $name_pinyin);
	}

	$json = sprintf('[%s]', $json);

	return $json;
}

/**
 * 将商品分类列表转换成符合zTree标准的JSON字符串格式
 */
function cat_list_to_json_string($cat_list, $selected = 0)
{
	include_once(ROOT_PATH . 'includes/Pinyin.php');

	$tree = array();

	foreach ($cat_list as $k => $cat)
	{
		$id = $cat['cat_id'];
		$pId = $cat['parent_id'];
		$name = $cat['cat_name'];
		//$open = true;

		$name_pinyin = Pinyin($name, 'utf-8', 1).$name;

		$node = array("id"=>$id, "pId"=>$pId, "name"=>$name, "name_pinyin"=>$name_pinyin);

		array_push($tree, $node);

	}

	return json_encode($tree);

}

if($_REQUEST['act']=='getCounty'){
    $city_id = $_REQUEST['id'];
    $sql = "select distinct county,region_name from ".$ecs->table('virtual_goods_district')." as d left join (select region_id,region_name from ".$ecs->table('region').") as r on r.region_id = d.county where city = '{$city_id}'";
    $county = $db -> getAll($sql);
    echo json_encode($county); 
    }
    
if($_REQUEST['act']=='getDistrict'){
    $city_id = $_REQUEST['city_id'];
    $county_id = $_REQUEST['county_id'];
    $sql = "select distinct district_id,district_name from ".$ecs->table('virtual_goods_district')." where city = '{$city_id}' and county = '{$county_id}'";
    $district = $db -> getAll($sql);
    echo json_encode($district); 
    }
   

/* 根据城市id 获得县级列表 */
if($_REQUEST['act'] == 'selectDistrict'){
    $sql = "select district_id, district_name from ".$GLOBALS['ecs']->table('virtual_goods_district'). " where county = '{$_REQUEST['county']}'";
    $area = $GLOBALS['db'] -> getAll($sql);
    echo json_encode($area);
}
?>