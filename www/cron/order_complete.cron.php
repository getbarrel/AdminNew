<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2020-02-12
 * Time: 오후 4:25
 */
@set_time_limit(0);
include $_SERVER["DOCUMENT_ROOT"]."/class/database.class";
include $_SERVER["DOCUMENT_ROOT"]."/include/global_util.php";

$db = new database;


$domain = str_replace('www.','',$_SERVER['HTTP_HOST']);
if(substr($domain, 0, 2) == "m.") {
    $domain=substr($domain, 2);
}
$sql = "select * from ".TBL_SHOP_SHOPINFO." where mall_domain = '{$domain}' LIMIT 1";
$db->query($sql);
$db->fetch();
$fetch_shop_info = $db->dt;


$today = date('Y-m-d');




/***********************************************************************
//
//	자동구매확정 처리 작업 추가 20131009 Hong
//
 ***********************************************************************/
$sql = "select
			*
		from
			shop_mall_config
		where
			mall_ix = '".$fetch_shop_info[mall_ix]."'
		and
			config_name = 'check_order_day'
		and 
			config_value is not null";
$db->query($sql);

$db->fetch();
$check_order_day = $db->dt[config_value];

$order_details=array();

$sql="select od.*,o.user_code from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and DATE_ADD(od.dc_date,INTERVAL ".$check_order_day." DAY)  <= '".$today."' and od.status = '".ORDER_STATUS_DELIVERY_COMPLETE."' LIMIT 2000 ";
$db->query($sql);

$order_details = $db->fetchall("object");

for($i=0;$i < count($order_details);$i++){

    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_BUY_FINALIZED."' , update_date = NOW(), bf_date = NOW() where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
    $db->query($sql);

    echo "자동구매확정 " . $order_details[$i][oid] ." ::: ". $order_details[$i][od_ix]."<br/>";
    set_order_status($order_details[$i][oid],ORDER_STATUS_BUY_FINALIZED,$msg,"".$check_order_day."일 경과 시스템 자동구매확정",$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);



    if(!empty($order_details[$i][user_code])){

        //New 마일리지 시스템 JK160323
        $sql = "select mg.selling_type from ".TBL_COMMON_MEMBER_DETAIL." cmd left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix where cmd.code = '".$order_details[$i][user_code]."' ";
        $db->query($sql);
        $db->fetch();

        if($db->dt[selling_type] == 'R'){
            $Shared_file = "b2c_mileage_rule";	//마일리지 설정값 파일명
            $com_type = 'b2c';
        }else if($db->dt[selling_type] == 'W'){
            $Shared_file = "b2b_mileage_rule";
            $com_type = 'b2b';
        }else{
            $Shared_file = "b2c_mileage_rule";	//마일리지 설정값 파일명
            $com_type = 'b2c';
        }

        $reserve_data = getBasicSellerSetup($Shared_file);

        if($reserve_data[mileage_add_setup] == 'C'){
            $state_type = 'add';
            $message = $order_details[$i]['pname']." 구매시 적립금액";

            /*신규 포인트,마일리지 접립 함수 JK 160405*/
            $mileage_data[uid] = $order_details[$i][user_code];
            $mileage_data[type] = 1;
            $mileage_data[mileage] = $order_details[$i][reserve];
            $mileage_data[message] = $message;
            $mileage_data[state_type] = 'add';
            $mileage_data[save_type] = 'mileage';
            $mileage_data[oid] = $order_details[$i][oid];
            $mileage_data[od_ix] = $order_details[$i][od_ix];
            $mileage_data[pid] = $order_details[$i][pid];
            $mileage_data[ptprice] = $order_details[$i][ptprice];
            $mileage_data[payprice] = $order_details[$i][pt_dcprice];
            InsertMileageInfo($mileage_data);


        }
        //끝
    }
}
