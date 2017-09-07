<?php
/**
 * 取消未支付订单
 * 计划任务 *\/5 * * * * /data/bin/task_order.sh
#!/bin/bash
php order_cancel.php
 */
define('IN_ECS', true);

define('ROOT_PATH_API', str_replace('task', '', str_replace('\\', '/', dirname(__FILE__))));
require(ROOT_PATH_API . '/includes/init.php');//由于使用命令执行脚本，so,引入此初始文件必须使用绝对路径
require(ROOT_PATH . '/includes/lib_order.php');

$now_time = time();

cancel_order_24h(24);
cancel_order_half_h(0.5);

/*
 *  取消订单
 * 【普通】订单
 *  超时24h后 已确认-未支付-未发货
 */
function cancel_order_24h($hour)
{
    global $now_time;
    global $db;

    $time_24h = 3600 * $hour;
    $differ_time = $now_time - $time_24h;//现在支付的时间戳

    //    $sql = "update ecs_order_info set order_status=2 where order_status=1 and pay_status=0 and shipping_status=0 and add_time<=$differ_time";
    //    $db->query($sql);

    $sql = "select order_id,order_sn from ecs_order_info where order_status in(0,1) and pay_status=0 and shipping_status=0 and add_time<=$differ_time";
    $res = $db->query($sql);
    while ($row_orders = $db->fetchRow($res))
    {
        //取消订单
        update_order($row_orders['order_id'], array('order_status' => 2, 'to_buyer' => '订单付款超时自动取消'));
        //恢复库存
        change_order_goods_storage($row_orders['order_id'], false, 1);
    }
    unset($res);
}

/*
 *  取消订单
 * 【活动】订单
 *  超时0.5h后 已确认-未支付-未发货
 */
function cancel_order_half_h($hour)
{
    global $now_time;
    global $db;

    $time_half_h = 3600 * $hour ;
    $differ_time = $now_time - $time_half_h;//现在支付的时间戳

    //    $sql = "update ecs_order_info set order_status=2 where extension_id>0 and order_status=1 and pay_status=0 and shipping_status=0 and add_time<=$differ_time";
    //    $db->query($sql);

    $sql = "select order_id,order_sn from ecs_order_info where extension_id>0 and order_status in(0,1) and pay_status=0 and shipping_status=0 and add_time<=$differ_time";
    $res2 = $db->query($sql);
    while ($row_orders = $db->fetchRow($res2))
    {
        //取消订单
        update_order($row_orders['order_id'], array('order_status' => 2, 'to_buyer' => '订单付款超时自动取消'));
        //恢复库存
        change_order_goods_storage($row_orders['order_id'], false, 1);
    }
    unset($res2);
}
