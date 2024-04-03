<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-08-09
 * Time: 오후 1:38
 */
include("../../class/layout.class");

if($_POST['act'] == 'insert'){
    $sql = "select * from shop_product_filter where filter_type = '".$_POST['filter_type']."' and (filter_code = '".$_POST['filter_code']."' or filter_name = '".$_POST['filter_name']."') ";
    $db->query($sql);
    if($db->total){
        echo "<script>alert('중복된 필터 정보가 존재 합니다. ')</script>";
        exit;
    }

    $path = $_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"]['mall_data_root']."/images/filter/";
    $webPath = $_SESSION["admin_config"]['mall_data_root']."/images/filter/";
    if(!is_dir($path)){
        mkdir($path, 0777);
    }

    $sql = "insert shop_product_filter set
                filter_type = '".$_POST['filter_type']."',
                filter_code = '".$_POST['filter_code']."',
                filter_name = '".$_POST['filter_name']."',
				filter_sort = '".$_POST['filter_sort']."',
                disp = '".$_POST['disp']."',
                filter_img_path = '".$webPath."',
                filter_img_pc = '".$_FILES["filter_img_pc"]["name"]."',
                filter_img_mobile = '".$_FILES["filter_img_mobile"]["name"]."',
                reg_name = '".$_SESSION['admininfo']['charger']."',
                reg_id = '".$_SESSION['admininfo']['charger_id']."',
                editdate = NOW(),
                regdate = NOW() ";

    $result = $db->query($sql);

    if ($result){
        $idx = mysql_insert_id();

        if ($_FILES["filter_img_pc"]["size"] > 0){
            move_uploaded_file($_FILES["filter_img_pc"]["tmp_name"], $path.$idx."_pc.gif");
        }
        if ($_FILES["filter_img_mobile"]["size"] > 0){
            move_uploaded_file($_FILES["filter_img_mobile"]["tmp_name"], $path.$idx."_mobile.gif");
        }

    }

    echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('필터 등록이 완료 되었습니다.','parent_reload');</script>";
    exit;
}

if($_POST['act'] == 'update'){
    $sql = "select * from shop_product_filter where filter_type = '".$_POST['filter_type']."' and (filter_code = '".$_POST['filter_code']."' or filter_name = '".$_POST['filter_name']."') 
        and idx != '".$_POST['idx']."'
    ";
    $db->query($sql);
    if($db->total){
        echo "<script>alert('중복된 필터 정보가 존재 합니다. ')</script>";
        exit;
    }

    $path = $_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"]['mall_data_root']."/images/filter/";
    $webPath = $_SESSION["admin_config"]['mall_data_root']."/images/filter/";
    if(!is_dir($path)){
        mkdir($path, 0777);
    }

    $sql = "update shop_product_filter set
                filter_type = '".$_POST['filter_type']."',
                filter_code = '".$_POST['filter_code']."',
                filter_name = '".$_POST['filter_name']."',
				filter_sort = '".$_POST['filter_sort']."',
                disp = '".$_POST['disp']."',
                filter_img_path = '".$webPath."',
                filter_img_pc = '".$_FILES["filter_img_pc"]["name"]."',
                filter_img_mobile = '".$_FILES["filter_img_mobile"]["name"]."',
                reg_name = '".$_SESSION['admininfo']['charger']."',
                reg_id = '".$_SESSION['admininfo']['charger_id']."',
                editdate = NOW()
            where
                idx = '".$_POST['idx']."' ";

    $result = $db->query($sql);

    if ($result){
        $idx = $_POST['idx'];

        if ($_FILES["filter_img_pc"]["size"] > 0){
            move_uploaded_file($_FILES["filter_img_pc"]["tmp_name"], $path.$idx."_pc.gif");
        }
        if ($_FILES["filter_img_mobile"]["size"] > 0){
            move_uploaded_file($_FILES["filter_img_mobile"]["tmp_name"], $path.$idx."_mobile.gif");
        }

    }

    echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('필터 수정이 완료 되었습니다.','parent_reload');</script>";
    exit;
}

if($_POST['act'] == 'delete'){

    $db->query("delete from shop_product_filter where idx = '$idx' ");

    if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"][mall_data_root]."/images/filter/".$idx."_pc.gif")){
        unlink($_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"][mall_data_root]."/images/filter/".$idx."_pc.gif");
    }
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"][mall_data_root]."/images/filter/".$idx."_mobile.gif")){
        unlink($_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"][mall_data_root]."/images/filter/".$idx."_mobile.gif");
    }

    echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('필터 정보가 삭제 되었습니다.','parent_reload');</script>";
    exit;
}