<?php
include("../../class/database.class");
$db = new Database;
if ($act == 'insert')
{
	
	$sql = $sql."INSERT INTO category_info (cid, depth, vlevel1, vlevel2, vlevel3, vlevel4, vlevel5, cname, catimg, subimg, category_use, regdate) ";
	$sql = $sql." values('', '$depth', '$vlevel1', '$vlevel2', '$vlevel3', $vlevel4, $vlevel5, $cname, '$catimg', '$subimg', '$category_use', '$regdate') ";
	
	$db->query($sql);
	$db->fetch();
}

if($act == 'delete_relation'){
    $sql = "select * from shop_product_relation where cid = '".$_POST['cid']."' and pid = '".$_POST['pid']."' ";
    $db->query($sql);
    $return_data['success'] = '';
    $return_data['msg'] = '';
    if($db->total){
        $db->fetch();
        $basic = $db->dt['basic'];
        $rid = $db->dt['rid'];
        if($basic == '1'){
            $return_data['success'] = false;
            $return_data['msg'] = '기본 카테고리는 제거할 수 없습니다.';
        }else{
            $sql = "select count(*) as cnt from shop_product_relation where pid = '".$_POST['pid']."' ";
            $db->query($sql);
            $db->fetch();
            $cnt = $db->dt['cnt'];
            if($cnt > 1){
                $sql = "delete from shop_product_relation where rid = '".$rid."' ";
                $db->query($sql);
                $return_data['success'] = true;
                $return_data['msg'] = '카테고리 매칭이 해제 되었습니다.';
            }else{
                $return_data['success'] = false;
                $return_data['msg'] = '단일 매칭 카테고리는 제거할 수 없습니다.';
            }
        }
    }else{
        $return_data['success'] = false;
        $return_data['msg'] = '매칭 대상이 존재하지 않습니다.';
    }

    echo json_encode($return_data);
    exit;
}

if($act == 'delete_relation_all'){

    if($_POST['type'] == 'all'){
        $sql = "select * from shop_product_relation where cid = '".$_POST['cid']."'  ";

        $db->query($sql);
        $return_data['s_cnt'] = 0;
        $return_data['f_cnt'] = 0;
        if($db->total){
            $relationArray = $db->fetchall();

            if(is_array($relationArray)){
                foreach($relationArray as $key=>$val){
                    $sql = "select * from shop_product where id = '".$val['pid']."' and is_delete = '0' ";
                    $db->query($sql);
                    if($db->total) {
                        if ($val['basic'] == '1') {
                            $return_data['f_cnt']++;
                        } else {
                            $sql = "select count(*) as cnt from shop_product_relation where pid = '" . $val['pid'] . "' ";
                            $db->query($sql);
                            $db->fetch();
                            $cnt = $db->dt['cnt'];
                            if ($cnt > 1) {
                                $sql = "delete from shop_product_relation where rid = '" . $val['rid'] . "' ";
                                $db->query($sql);
                                $return_data['s_cnt']++;
                            } else {
                                $return_data['f_cnt']++;
                            }
                        }
                    }else{
                        //존재하지 않은 상품이거나 삭제되 상품인 경우 매칭에서 제거 처리 한다
                        $sql = "delete from shop_product_relation where rid = '" . $val['rid'] . "' ";
                        $db->query($sql);
                    }
                }
            }

        }
    }else {
        if (is_array($_POST['lists']) && count($_POST['lists']) > 0) {

            $ridStr = implode(',', $_POST['lists']);
            $where = " and rid in (" . $ridStr . ")";

            $sql = "select * from shop_product_relation where cid = '" . $_POST['cid'] . "' $where";

            $db->query($sql);
            $return_data['s_cnt'] = 0;
            $return_data['f_cnt'] = 0;
            if ($db->total) {
                $relationArray = $db->fetchall();

                if (is_array($relationArray)) {
                    foreach ($relationArray as $key => $val) {
                        $sql = "select * from shop_product where id = '" . $val['pid'] . "' and is_delete = '0' ";
                        $db->query($sql);
                        if ($db->total) {
                            if ($val['basic'] == '1') {
                                $return_data['f_cnt']++;
                            } else {
                                $sql = "select count(*) as cnt from shop_product_relation where pid = '" . $val['pid'] . "' ";
                                $db->query($sql);
                                $db->fetch();
                                $cnt = $db->dt['cnt'];
                                if ($cnt > 1) {
                                    $sql = "delete from shop_product_relation where rid = '" . $val['rid'] . "' ";
                                    $db->query($sql);
                                    $return_data['s_cnt']++;
                                } else {
                                    $return_data['f_cnt']++;
                                }
                            }
                        } else {
                            //존재하지 않은 상품이거나 삭제되 상품인 경우 매칭에서 제거 처리 한다
                            $sql = "delete from shop_product_relation where rid = '" . $val['rid'] . "' ";
                            $db->query($sql);
                        }
                    }
                }

            } else {
                $return_data['f_cnt']++;
            }
        }
    }

    echo json_encode($return_data);
    exit;
}