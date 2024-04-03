<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-08-28
 * Time: 오후 2:45
 */
include("../../class/database.class");

$db = new Database;

if($_POST['act'] == 'update'){

    $sql = "delete from shop_group_benefits where gp_ix = '".$_POST['gp_ix']."' ";
    $db->query($sql);

    if($_POST['mileage'] > 0){
        $sql = "insert into shop_group_benefits SET 
              gp_ix = '".$_POST['gp_ix']."',
              benefit_type = 'M',
              benefit_value = '".$_POST['mileage']."',
              regdate = NOW() ";
        $db->query($sql);
    }

    if(is_array($_POST['publish_ix']) && count($_POST['publish_ix'])){
        foreach($_POST['publish_ix'] as $publish_ix){
            if(!empty($publish_ix)){
                $sql = "insert into shop_group_benefits SET 
                  gp_ix = '".$_POST['gp_ix']."',
                  benefit_type = 'C',
                  benefit_value = '".$publish_ix."',
                  regdate = NOW() ";
                $db->query($sql);
            }
        }
    }

    echo "<script>alert('설정 완료 되었습니다.'); top.location.reload();</script>";
    exit;
}