<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2020-01-29
 * Time: 오후 5:59
 */
include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";

$db = new database();

if($_GET['seq']){
    $sql = "select * from shop_product_update_in_log where idx = '".$_GET['seq']."'  ";
    //echo $sql;
    $db->query($sql);
    $db->fetch();
    $post_data = json_decode(urldecode($db->dt['data']),true);

    print_r($post_data);



}