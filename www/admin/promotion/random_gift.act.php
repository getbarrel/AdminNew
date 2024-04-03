<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-12-06
 * Time: 오후 3:06
 */
include $_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class";



if($act == 'insert'){

    $total_cnt = 0;
    if(is_array($issued_quantity)){
        foreach($issued_quantity as $key=>$val){
            $total_cnt = $total_cnt+(int)$val;
        }
    }
    if(is_array($mileage_value)){
        foreach($mileage_value as $key=>$val){
            $total_cnt = $total_cnt+(int)$val;
        }
    }

    if($total_cnt != $create_cnt){
        echo "<script>parent.$.unblockUI();alert('발행 수량이 변조 되었습니다. 다시 등록 바랍니다.');</script>";
        exit;
    }

    if($percentage > 100){
        echo "<script>parent.$.unblockUI();alert('* 상품권 노출 확률은 100을 넘길 수 없습니다.');</script>";
        exit;
    }

    if($create_cnt > 10000){
        echo "<script>parent.$.unblockUI();alert('* 최대 발행 가능 수량은 10000 건 입니다.');</script>";
        exit;

    }

    if(isset($_FILES) && $_FILES['gift_img']['size'] > 0){
        $filePath = $_SERVER["DOCUMENT_ROOT"]."".$_SESSION['admin_config']['mall_data_root']."/images/gift_coupon/";
        $urlPath = $_SESSION['admin_config']['mall_data_root']."/images/gift_coupon/";
        if(!is_dir($filePath)){
            mkdir($filePath);
            chmod($filePath,0777);
        }

        $gift_img_extension = getFileExtension($_FILES['gift_img']);
        $gift_img_name = md5(uniqid(rand(), true)) . "." . $gift_img_extension;
        $gift_img_org = $_FILES['gift_img']['name'];

        if ($_FILES['gift_img']['size'] > 0){
            move_uploaded_file($_FILES['gift_img']['tmp_name'], $filePath.$gift_img_name);
        }
        $fullUrl = $filePath.$gift_img_name;
    }

    $sql = "insert into
              shop_gift_random_certificate
              (gc_ix,gift_certificate_name,create_cnt,gift_start_date,gift_end_date,memo,is_use,percentage,gift_file_name,gift_file_path,
              editdate,regdate)
             values
              ('','$gift_certificate_name','$create_cnt','$gift_start_date','$gift_end_date','$memo','$is_use','$percentage','$gift_img_org','$fullUrl',
              NOW(),NOW())";
    $db->query($sql);


    $db->query("SELECT gc_ix FROM shop_gift_random_certificate WHERE gc_ix = LAST_INSERT_ID()");
    $db->fetch();
    $gc_ix = $db->dt[gc_ix];

    $length = 16;
    $pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    if(is_array($coupon_seq)){
        foreach($coupon_seq as $key=>$val){

            if($publish_ix[$val] && $issued_quantity[$val] > 0 ){
                //쿠폰 등록 등록 수량은 $issued_quantity[$val]

                for($i=0; $i < $issued_quantity[$val]; $i++){
                    $coupon_num = "";

                    for($z=0; $z < $length; $z++){
                        $coupon_num .= $pattern{rand(0,35)};
                    }
                    $sql = "insert into 
                          shop_gift_random_certificate_detail
                          (gcd_ix,gc_ix,gift_code,gift_type,gift_value,gift_change_state) 
                        values
                          ('','".$gc_ix."','".$coupon_num."','C','".$publish_ix[$val]."','0')";

                    $db->query($sql);
                }

            }
        }
    }


    if(is_array($mileage_seq)){
        foreach($mileage_seq as $key=>$val){
            if($mileage[$val] && $mileage_value[$val] > 0 ){
                //쿠폰 등록 등록 수량은 $issued_quantity[$val]

                for($i=0; $i < $mileage_value[$val]; $i++){
                    $coupon_num = "";

                    for($z=0; $z < $length; $z++){
                        $coupon_num .= $pattern{rand(0,35)};
                    }
                    $sql = "insert into 
                          shop_gift_random_certificate_detail
                          (gcd_ix,gc_ix,gift_code,gift_type,gift_value,gift_change_state) 
                        values
                          ('','".$gc_ix."','".$coupon_num."','M','".$mileage[$val]."','0')";

                    $db->query($sql);
                }

            }
        }
    }

    echo "<script>alert('상품권 정보가 정상적으로 등록되었습니다.');parent.document.location.href='../promotion/random_gift_list.php';</script>";
    exit;
}

if($act == 'update'){
    if($percentage > 100){
        echo "<script>parent.$.unblockUI();alert('* 상품권 노출 확률은 100을 넘길 수 없습니다.');</script>";
        exit;
    }

    if(isset($_FILES) && $_FILES['gift_img']['size'] > 0){
        $filePath = $_SERVER["DOCUMENT_ROOT"]."".$_SESSION['admin_config']['mall_data_root']."/images/gift_coupon/";
        $urlPath = $_SESSION['admin_config']['mall_data_root']."/images/gift_coupon/";
        if(!is_dir($filePath)){
            mkdir($filePath);
            chmod($filePath,0777);
        }

        $gift_img_extension = getFileExtension($_FILES['gift_img']);
        $gift_img_name = md5(uniqid(rand(), true)) . "." . $gift_img_extension;
        $gift_img_org = $_FILES['gift_img']['name'];

        if ($_FILES['gift_img']['size'] > 0){
            unlink($_SERVER['DOCUMENT_ROOT'].$b_gift_img);
            move_uploaded_file($_FILES['gift_img']['tmp_name'], $filePath.$gift_img_name);
        }
    }

    $sql = "update 
              shop_gift_random_certificate 
            set 
              gift_certificate_name = '".$gift_certificate_name."',
              memo = '".$memo."',
              gift_start_date = '".$gift_start_date."',
              gift_end_date = '".$gift_end_date."',
              is_use = '".$is_use."',
              percentage = '".$percentage."',
              gift_file_name = '".$gift_img_org."',
              gift_file_path = '".$urlPath.$gift_img_name."',
              editdate = NOW()
            where
              gc_ix ='".$gc_ix."'
            ";
    $db->query($sql);
    echo "<script>alert('상품권 정보가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
    exit;
}

if($act == "delete"){

    $sql = "update shop_gift_random_certificate set status = 'N' where gc_ix='$gc_ix' and status = 'Y'";
    $db->query($sql);

    $sql = "update shop_gift_random_certificate_detail set status = 'N' where gc_ix='$gc_ix' and status = 'Y'";
    $db->query($sql);

    echo "<script>alert(\"상품권 정보가 정상적으로 삭제 되었습니다. \");parent.document.location.reload();</script>";
    exit;
}

if($act == "detail_delete"){
    $sql = "update shop_gift_random_certificate_detail set status = 'N' where gcd_ix='$gcd_ix' and status = 'Y'";
    $db->query($sql);

    echo "<script>alert(\"상품권 정보가 정상적으로 삭제 되었습니다. \");parent.document.location.reload();</script>";
    exit;
}


if($act == "delete_detail_selected"){
    if(! empty($search_searialize_value) && $update_type == 1){
        $unserialize_search_value = unserialize(urldecode($search_searialize_value));
        extract($unserialize_search_value);
    }

    if($update_type == 1){


        $where = "where gc.gc_ix = '".$gc_ix."' and gcd.status = 'Y' ";

        if($gift_change_state != ""){
            $where .= " and gcd.gift_change_state = $gift_change_state ";
        }

        if($search_text != ""){
            if($search_type != ""){
                if($search_type == "gcd.gift_code"){
                    $search_text = str_replace("-","",$search_text);
                    $where .= " and $search_type LIKE '%".trim($search_text)."%' ";

                }else if($search_type == "cmd.name"){
                    $where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
                }else{
                    $where .= " and $search_type LIKE '%".trim($search_text)."%' ";
                }
            }else{
                $where .= " and (gcd.gift_code LIKE '%".str_replace("-","",trim($search_text))."%' or AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' or gcd.member_id LIKE '%$search_text%') ";
            }
        }


        if($regdate == 1 && ($startDate != "" && $endDate != "")){
            $where .= " and  gcd.use_date between $startDate and $endDate ";
        }


        $sql = "select 
          gcd.gcd_ix
        from 
          shop_gift_random_certificate gc 
        left join 
          shop_gift_random_certificate_detail gcd on gc.gc_ix = gcd.gc_ix
        left JOIN 
          common_member_detail cmd on cmd.code = gcd.user_code
        $where";
        $db->query($sql);
        $lists = $db->fetchall("object");
    }else{
        $sql = "select gcd.gcd_ix 
					from shop_gift_random_certificate_detail gcd where gcd_ix in ('".implode("','",$ix)."')";
        $db->query($sql);
        $lists = $db->fetchall("object");
    }

    if(is_array($lists)){
        foreach($lists as $k => $v){
            $sql = "update shop_gift_random_certificate_detail set status = 'N' where gcd_ix='$v[gcd_ix]' and status = 'Y'";
            $db->query($sql);
        }
    }

    echo "<script>alert(\"상품권 정보가 정상적으로 삭제 되었습니다. \");top.document.location.href='./random_gift_issue_history.php?gc_ix=".$gc_ix."';</script>";
    exit;
}

