<?

set_time_limit(9999999);

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");

//30분마다!

$db = new Database();

$sql = "select mall_ix,mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();

$admininfo[mall_ix] = $db->dt[mall_ix];
$admininfo[mall_data_root] = $db->dt[mall_data_root];
$admininfo[admin_level] = 9;
$admininfo[language] = 'korea';
$admininfo[mall_type] = $db->dt[mall_type];
$admin_config[mall_data_root] = $db->dt[mall_data_root];

$array_pid = array();

//1. 재고
$sql="select od.pid from shop_order_detail od left join common_company_detail ccd on (od.company_id=ccd.company_id) where ccd.halfclub_code!='' and od.di_date between DATE_ADD(NOW(), INTERVAL -40 MINUTE) and NOW() and od.stock_use_yn='Y' group by od.pid";
$db->query($sql);
if($db->total){
	$pinfo=$db->fetchall("object");
	foreach($pinfo as $pi){
		array_push($array_pid,$pi[pid]);
	}
}

//2. 수정 및 판매기간
$sql="select p.id from shop_product p left join common_company_detail ccd on (p.admin=ccd.company_id) where

	ccd.halfclub_code!=''

	and

	( 
		p.editdate >= DATE_ADD(NOW(), INTERVAL -40 MINUTE)

		OR

		p.regdate >= DATE_ADD(NOW(), INTERVAL -40 MINUTE)

		OR 
		
		if(p.is_sell_date = '1',p.sell_priod_sdate >= DATE_ADD(NOW(), INTERVAL -40 MINUTE), 0 )

		OR
		
		if(p.is_sell_date = '1',p.sell_priod_edate >= DATE_ADD(NOW(), INTERVAL -40 MINUTE), 0 ) 

	) ";
$db->query($sql);
$pinfo=array();
if($db->total){
	$pinfo=$db->fetchall("object");
	foreach($pinfo as $pi){
		array_push($array_pid,$pi[id]);
	}
}


$result = array_unique($array_pid);

$pinfo=array();
// 관리자가 상품을 수동으로 보낸 상품만 자동으로 보내기!
$sql="select pid from sellertool_regist_relation where pid in ('".implode("','",$result)."') and site_code='halfclub' group by pid";
$db->query($sql);
$pinfo=$db->fetchall("object");

$OAL = new OpenAPI('halfclub');
$OAL->lib->set_error_type("return");
if(count($pinfo) > 0){
	foreach($pinfo as $info){
		$resulte = $OAL->lib->registGoods($info["pid"],'');
		//echo $pid."|".$resulte->message."<br/>";
		//syslog(1,"SELLERTOOL - " . $pid . "|" . $resulte->message);
	}
}






$array_pid = array();

//1. 재고
$sql="select od.pid from shop_order_detail od left join common_company_detail ccd on (od.company_id=ccd.company_id) where ccd.company_id in('362ed8ee1cba4cc34f80aa5529d2fbcd','c72e0d088cbfdd30452ca85472739747') and od.di_date between DATE_ADD(NOW(), INTERVAL -40 MINUTE) and NOW() and od.stock_use_yn='Y' group by od.pid";
$db->query($sql);
if($db->total){
	$pinfo=$db->fetchall("object");
	foreach($pinfo as $pi){
		array_push($array_pid,$pi[pid]);
	}
}

//2. 수정 및 판매기간
$sql="select p.id from shop_product p left join common_company_detail ccd on (p.admin=ccd.company_id) where
	
	ccd.company_id in('362ed8ee1cba4cc34f80aa5529d2fbcd','c72e0d088cbfdd30452ca85472739747')

	and

	( 
		p.editdate >= DATE_ADD(NOW(), INTERVAL -40 MINUTE)

		OR

		p.regdate >= DATE_ADD(NOW(), INTERVAL -40 MINUTE)

		OR 
		
		if(p.is_sell_date = '1',p.sell_priod_sdate >= DATE_ADD(NOW(), INTERVAL -40 MINUTE), 0 )

		OR
		
		if(p.is_sell_date = '1',p.sell_priod_edate >= DATE_ADD(NOW(), INTERVAL -40 MINUTE), 0 ) 

	) ";

$db->query($sql);
$pinfo=array();
if($db->total){
	$pinfo=$db->fetchall("object");
	foreach($pinfo as $pi){
		array_push($array_pid,$pi[id]);
	}
}

$pinfo = array_unique($array_pid);

$OAL = new OpenAPI('auction');
if(count($pinfo) > 0){
	foreach($pinfo as $pid){
		$resulte = $OAL->lib->registGoods($pid,'');
		//echo $pid."|".$resulte->message."<br/>";
		//syslog(1,"SELLERTOOL - " . $pid . "|" . $resulte->message);
	}
}

?>