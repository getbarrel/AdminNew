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
    /*$sql = "select * from shop_content_player_subject where subject = '".$_POST['subject']."' ";
    $db->query($sql);
    if($db->total){
        echo "<script>alert('이미 등록된 종목명 입니다.');top.location.reload();</script>";
        exit;
    }*/

    $sql = "insert into shop_content_player_subject SET 
              subject = '".$_POST['subject']."',
              sort = '".$_POST['sort']."',
              disp = '".$_POST['disp']."',
              worker_ix = '".$_SESSION["admininfo"]["charger_ix"]."',
              regdate = NOW()
          ";
    $db->query($sql);
    echo "<script>alert('등록되었습니다.'); top.location.reload();</script>";
    exit;
}

if($act == 'update'){
    /*$sql = "select * from shop_content_player_subject where subject = '".$_POST['subject']."' ";
    $db->query($sql);
    if($db->total){
        echo "<script>alert('이미 등록된 종목명 입니다.');top.location.reload();</script>";
        exit;
    }*/

    $sql = "update shop_content_player_subject SET 
              subject = '".$_POST['subject']."',
              sort = '".$_POST['sort']."',
              disp = '".$_POST['disp']."',
              worker_ix = '".$_SESSION["admininfo"]["charger_ix"]."',
              upddate = NOW() where
              idx = '".$_POST['idx']."'
          ";
    $db->query($sql);
    echo "<script>alert('수정되었습니다.'); top.location.reload();</script>";
    exit;
}

if($act == 'delete'){
    $sql = "delete from shop_content_player_subject where idx = '".$_GET['idx']."' ";
    $db->query($sql);
    echo "<script>alert('삭제 되었습니다.'); top.location.reload();</script>";
    exit;
}