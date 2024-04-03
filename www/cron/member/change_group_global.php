<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-08-28
 * Time: 오후 4:12
 */
@set_time_limit(0);
include $_SERVER['DOCUMENT_ROOT']."/class/database.class";
include_once $_SERVER['DOCUMENT_ROOT']."/include/global_util.php";
$db = new database;
$mall_ix = '20bd04dac38084b2bafdd6d78cd596b2';

$checkMode = true;//운영시 true

$sql = "select mall_data_root from shop_shopinfo where mall_ix = '".$mall_ix."' and mall_div = 'B' ";
$db->query($sql);
$db->fetch();
$mall_data_root = $db->dt['mall_data_root'];
$rootPath = $_SERVER["DOCUMENT_ROOT"].$mall_data_root."/_logs/member_group_change/";
if(!is_dir($rootPath)){
    mkdir($rootPath, 0777);
    chmod($rootPath,0777);
}
$logMonth = date('Ym');
$logPath = $rootPath.$logMonth;
if(!is_dir($logPath)){
    mkdir($logPath, 0777);
    chmod($logPath,0777);
}

$now_day = date('j');


//관리자 계정의 경우 처리 하지 않는다.
$mem_type = "F";//해외 회원
# 회원 그룹 중 레벨관리 허용된 회원 그룹 중 가장 낮은 그룹 순으로 정보를 가져온다
$sql = "select * from shop_groupinfo where all_disp = '1' and mall_ix = '".$mall_ix."' and gp_type = '".$now_day."' order by gp_level desc";
$db->query($sql);
$groups = $db->fetchall();

if(!empty($groups) && is_array($groups)){
    foreach($groups as $key=>$val){

        $member = "select 
                    cu.code 
                  from 
                    common_user cu 
                  left join 
                    common_member_detail cmd on cu.code = cmd.code 
                  where 
                    cmd.gp_ix = '".$val['gp_ix']."' 
                  and 
                    cu.mem_type = '".$mem_type."'
                  and
                    (date_format(cmd.level_change_date,'%Y-%m-%d') = '0000-00-00' or date_format(cmd.level_change_date,'%Y-%m-%d') < '".date('Y-m-d')."')
                  ";

        $sql = "select 
                  sum(od.pt_dcprice) as order_sum,o.user_code,o.buserid as user_id
                from 
                  shop_order o 
                left join 
                  shop_order_detail od on o.oid = od.oid 
                where 
                  o.user_code in ($member)
                and 
                  od.mall_ix = '".$mall_ix."'                  
                and 
                  od.status in ('BF')
                group by o.user_code                
                 ";

        $sql2 = "SELECT
                    decimal_price AS order_sum,
                    CODE as user_code,
                    id as user_id
                FROM
                    member_payment_info
                WHERE
                    CODE IN ($member)
                AND 
                  mall_ix = '".$mall_ix."' 
                
        ";

        $sql3 = "select 
                  sum(order_sum) as order_sum, user_code, user_id 
                from 
                  ($sql UNION ALL $sql2) as a
                group by user_code                
                ";


        $db->query($sql3);
        $order_users = $db->fetchall();

        if(!empty($order_users) && is_array($order_users)){
            foreach($order_users as $k => $v){

                $sql = "select cmd.gp_ix as org_gp_ix, sg.gp_name as org_gp_name, sg.gp_level as org_gp_level from common_member_detail cmd left join shop_groupinfo sg on cmd.gp_ix = sg.gp_ix where cmd.code = '".$v['user_code']."' ";
                $db->query($sql);
                $db->fetch();
                $org_gp_ix = $db->dt['org_gp_ix'];
                $org_gp_name = $db->dt['org_gp_name'];
                $org_gp_level = $db->dt['org_gp_level'];


                $sql = "select 
                          gp_ix ,gp_name,gp_level
                        from 
                          shop_groupinfo 
                        where 
                          all_disp = '1' 
                        and 
                          mall_ix = '".$mall_ix."' 
                        and gp_type = '".$now_day."'
                        and '".$v['order_sum']."' between order_price and ed_order_price
                         ";

                $db->query($sql);
                if($db->total){
                    $db->fetch();
                    $gp_ix = $db->dt['gp_ix'];
                    $gp_name = $db->dt['gp_name'];
                    $gp_level = $db->dt['gp_level'];

                    //동일 등급 및 낮은 등급으로 는 전환 되지 않도록 처리
                    if($org_gp_level <= $gp_level){
                        continue;
                    }

                    $log_message = "ID:".$v['user_id'].", 이전그룹:".$org_gp_name."(".$org_gp_ix.")(".$org_gp_level."), 변경그룹:".$gp_name."(".$gp_ix.")(".$gp_level.")";


                    $update = " ,gp_ix = '".$gp_ix."' ";

                    $sql = "select cmd.* from common_member_detail cmd left join shop_groupinfo sg on cmd.gp_ix = sg.gp_ix where cmd.code = '".$v['user_code']."' and cmd.gp_ix = '".$gp_ix."' ";
                    $db->query($sql);
                    if(!$db->total){

                        $sql = "update common_member_detail set level_change_date = NOW() $update where code = '".$v['user_code']."' and gp_ix !='".$gp_ix."' ";
                        if($checkMode) {
                            $db->query($sql);
                        }

                        $sql = "select * from shop_group_benefits where gp_ix = '".$gp_ix."' ";
                        $db->query($sql);
                        $benefits = $db->fetchall();

                        $input_mileage_info = ", 마일리지:";
                        $input_coupon_info = ", 쿠폰:";
                        if(!empty($benefits) && is_array($benefits)){
                            foreach($benefits as $item){
                                if($item['benefit_type'] == 'M'){
                                    //마일리지 지급
                                    $mileage_data[uid] = $v['user_code'];
                                    $mileage_data[type] = 8;
                                    $mileage_data[mileage] = abs($item['benefit_value']);
                                    $mileage_data[message] = "회원 등급 상승에 따른 적립금 지급";
                                    $mileage_data[state_type] = "add";
                                    $mileage_data[save_type] = 'mileage';
                                    if($checkMode) {
                                        InsertMileageInfo($mileage_data);
                                    }
                                    $input_mileage_info .=" [".abs($item['benefit_value'])."]";
                                }else if($item['benefit_type'] == 'C'){
                                    //쿠폰지급
                                    if($checkMode) {
                                        regist_cupon($item['benefit_value'], $v['user_code']);
                                    }
                                    $input_coupon_info .=" [".$item['benefit_value']."]";
                                }
                            }
                        }


                        $log_message = $log_message . $input_mileage_info . $input_coupon_info . "\r\n";

                        $file_name = "en_member_group_change_" . $org_gp_ix . "_" . $gp_ix . "_" . date('Ymd') . ".log";
                        $fp = fopen($logPath . "/" . $file_name, "a+");
                        fwrite($fp, $log_message);
                        fclose($fp);
                    }
                }
            }
        }
    }
}