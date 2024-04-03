<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2020-02-04
 * Time: 오전 11:29
 */
@set_time_limit(0);
include $_SERVER['DOCUMENT_ROOT']."/class/database.class";

$db = new database;

$domain = str_replace('www.','',$_SERVER['HTTP_HOST']);
if(substr($_SERVER['HTTP_HOST'],0,2) !='m.'){
    $sql = "select mall_ix from ".TBL_SHOP_SHOPINFO." where mall_domain = '{$domain}'";
}else{
    $sql = "select mall_ix from ".TBL_SHOP_SHOPINFO." where mall_mobile_domain = '{$domain}'";
}
$db->query($sql);
if($db->total){
    $db->fetch();
    $mall_ix = $db->dt['mall_ix'];
}


$sql = "SELECT config_value FROM shop_mall_privacy_setting where mall_ix = '".$mall_ix."' and config_name = 'member_connect_delete_day'";
$db->query($sql);
$db->fetch();
$connectDeleteDay = $db->dt['config_value'];

//회원 접속 로그 기록 유지 기간 없을때 180일
if (empty($connectDeleteDay) || $connectDeleteDay <= 0) {
    $connectDeleteDay = 180;
}

$delete_log_time = date('Y-m-d H:i:s', strtotime("- " . $connectDeleteDay . " days"));

$sql = "delete from common_member_connect_log where connect_time < '".$delete_log_time."' ";

$db->query($sql);
