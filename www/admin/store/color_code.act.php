<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-07-18
 * Time: 오후 4:23
 */
include("../../class/layout.class");

$db = new Database;

if($act == 'insert'){
    $sql = "select * from shop_color_code where color_code = '".$_POST['color_code']."' ";
    $db->query($sql);
    if($db->total){
        echo "<script>alert('이미 등록된 코드 입니다.');top.location.reload();</script>";
        exit;
    }

    $sql = "insert into shop_color_code SET 
              color_code = '".$_POST['color_code']."',
              color_code_name = '".$_POST['color_code_name']."',
              regdate = NOW()
          ";
    $db->query($sql);
    echo "<script>alert('등록되었습니다.'); top.location.reload();</script>";
    exit;
}

if($act == 'delete'){
    $sql = "delete from shop_color_code where idx = '".$_GET['idx']."' ";
    $db->query($sql);
    echo "<script>alert('삭제 되었습니다.'); top.location.reload();</script>";
    exit;
}