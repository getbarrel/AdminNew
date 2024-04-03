<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-10-01
 * Time: 오후 5:03
 */
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

if($_POST['act'] == 'update'){
    $update_bool = false;
    //변경 전 주문 데이터 체크
    $sql = "select 
            od_ix , oid, status
          from 
            shop_order_detail 
          where 
            oid = '".$_POST['oid']."' 
          and 
            od_ix = '".$_POST['od_ix']."'
          and 
            pid = '".$_POST['org_pid']."'
          and 
            gid = '".$_POST['org_gid']."'
          and 
            option_id = '".$_POST['org_option_id']."'  
          and
            status = '".ORDER_STATUS_EXCHANGE_READY."'
            
            ";

    $db->query($sql);
    if($db->total){
        $db->fetch();
        $od_ix = $db->dt['od_ix'];
        $oid = $db->dt['oid'];
        $status = $db->dt['status'];
        //변경전 주문데이터 값이 일치 할때 변경하고자 하는 정보로 업데이트 진행
        //옵션 코드와, 품목코드를 기준으로 상품 기본 정보 추출 작업 진행

        $sql = "select id as option_id,pid,option_div,option_gid from shop_product_options_detail where id = '".$_POST['option_id']."' and option_gid = '".$_POST['gid']."' ";
        $db->query($sql);
        if($db->total){
            $db->fetch();
            $pid = $db->dt['pid'];
            $option_name = $db->dt['option_div'];
            $option_gid = $db->dt['option_gid'];
            $option_id = $db->dt['option_id'];

            $sql = "select gu_ix from inventory_goods_unit where gid = '".$option_gid."' ";
            $db->query($sql);
            if($db->total) {
                $db->fetch();
                $gu_ix = $db->dt['gu_ix'];


                $sql = "select pname from shop_product where id = '" . $pid . "' ";
                $db->query($sql);
                if ($db->total) {
                    $db->fetch();
                    $pname = $db->dt['pname'];

                    $sql = "update 
                              shop_order_detail 
                            set 
                                pname = '" . $pname . "',
                                pid = '" . $pid . "',
                                gid = '" . $option_gid . "',
                                gu_ix = '".$gu_ix."',
                                option_id = '".$option_id."',
                                option_text = '".$option_name."'
                            where
                                od_ix = '".$od_ix."'
                            ";
                    $result = $db->query($sql);

                    if($result){
                        $ADMIN_MESSAGE = $_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")";
                        $STATUS_MESSAGE = "교환상품 정보 변경진행 (pid : ".$_POST['org_pid']." gid : ".$_POST['org_gid']." option_id : ".$_POST['org_option_id']."";
                        set_order_status($oid,$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$od_ix,$pid,'',"","",'');
                        sellingCntUpdate($option_gid);
                        $update_bool = true;
                    }
                }
            }
        }
    }

    if($update_bool === true){
        echo "<script>alert('교환 상품 정보가 변경되었습니다.');top.opener.location.reload();top.self.close();</script>";
        exit;
    }else{
        echo "<script>alert('정보변경에 실패 되었습니다. 다시 시도해 주세요.');top.location.reload();</script>";
        exit;
    }
}

