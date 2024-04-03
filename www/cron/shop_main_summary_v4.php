<?php
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/include/commerce.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/include/util.php");

$slave_db = new Database;
$slave_db->slave_db_setting();


$vdate = date("Ymd", time());
$today = date("Ymd", time());
$firstday = date("Y-m-d", time()-84600*date("w"));
$lastday = date("Y-m-d", time()+84600*(6-date("w")));

//--------------------------------------------------------------------------------------------------------------------------------------------------//

/******************************
	데시보드 데이터 산출
******************************/

// 날짜
$sdate = date('Y-m-01');
$edate = date('Y-m-t');
$toDay = date('Y-m-d');
$yesterDay = date('Y-m-d', strtotime("-1 days"));

// 주문 통계 조회
$sql = "SELECT `od`.`status`, COUNT(*) as cnt 
FROM `shop_order` AS `o`
JOIN `shop_order_detail` AS `od` ON `o`.`oid` = `od`.`oid`
WHERE o.order_date BETWEEN '{$sdate} 00:00:00' AND '{$edate} 23:59:59'
AND `od`.`status` IN ('IC', 'DR', 'DC', 'CA', 'RA', 'EA')
AND `od`.`claim_delivery_od_ix` = 0
GROUP BY `od`.`status`";

$db->query($sql);

$orderState = array(
	'IC' => 0,
	'DR' => 0,
	'DC' => 0,
	'CA' => 0,
	'RA' => 0,
	'EA' => 0
);

if($db->total > 0) {
	for($i=0; $i < $db->total; $i++) {
		// IC DR DC
		$db->fetch($i);

		if(!empty($db->dt)) {
			$orderState[$db->dt['status']] += $db->dt['cnt'];
		}
	}
}

// 상품 통계 조회
$sql = "SELECT `state`, count(*) AS cnt FROM `shop_product` GROUP BY `state`";
$db->query($sql);

$productState = array(0, 0, 0);

if($db->total > 0) {
	for($i=0; $i < $db->total; $i++) {
		// IC DR DC
		$db->fetch($i);

		if(!empty($db->dt)) {
			$productState[$db->dt['state']] += $db->dt['cnt'];
		}
	}
}

// QNA 통계 조회
$qnaStat = array(
	'pQna' => 0,
	'sQna' => 0
);

$sql = "SELECT COUNT(*) AS `cnt` FROM `shop_product_qna` WHERE `bbs_re_cnt` = 0";
$db->query($sql);
$db->fetch();
if(!empty($db->dt)) {
    $qnaStat['pQna'] += $db->dt['cnt'];
}

$sql = "SELECT COUNT(*) AS `cnt` FROM `bbs_qna` WHERE `status` = 1";
$db->query($sql);
$db->fetch();
if(!empty($db->dt)) {
    $qnaStat['sQna'] += $db->dt['cnt'];
}

// 회원, 주문, 환불 통계 조회
$moreStat = array(
	'regCnt' => 0,
	'sleepCnt' => 0,
	'orderCnt' => array('W' => 0, 'M' => 0),
	'refundCnt' => array('W' => 0, 'M' => 0)
);

// 전일 가입
$sql = "SELECT COUNT(*) AS `cnt` FROM `common_user` WHERE date BETWEEN '{$yesterDay} 00:00:00' AND '{$yesterDay} 23:59:59'";
$db->query($sql);
$db->fetch();
if(!empty($db->dt)) {
	$moreStat['regCnt'] += $db->dt['cnt'];
}

// 전일 탈퇴
$sql = "SELECT COUNT(*) AS `cnt` FROM `common_dropmember` WHERE dropdate BETWEEN '{$yesterDay} 00:00:00' AND '{$yesterDay} 23:59:59'";
$db->query($sql);
$db->fetch();
if(!empty($db->dt)) {
	$moreStat['sleepCnt'] += $db->dt['cnt'];
}

// 전일 주문수
$sql = "SELECT `o`.`payment_agent_type`, COUNT(*) AS cnt
FROM `shop_order` AS `o`
JOIN `shop_order_detail` AS `od` ON `o`.`oid` = `od`.`oid`
WHERE `o`.`status` = 'IC'
AND regdate BETWEEN '{$yesterDay} 00:00:00' AND '{$yesterDay} 23:59:59'
GROUP BY `o`.`payment_agent_type`";
$db->query($sql);
if($db->total > 0) {
	for($i=0; $i < $db->total; $i++) {
		$db->fetch($i);
		$moreStat['orderCnt'][$db->dt['payment_agent_type']] += $db->dt['cnt'];
	}
}

// 전일 환불수
$sql = "SELECT `o`.`payment_agent_type`, COUNT(*) AS cnt
FROM `shop_order` AS `o`
JOIN `shop_order_detail` AS `od` ON `o`.`oid` = `od`.`oid`
WHERE `od`.`refund_status` = 'FA'
AND fa_date BETWEEN '{$yesterDay} 00:00:00' AND '{$yesterDay} 23:59:59'
GROUP BY `o`.`payment_agent_type`";
$db->query($sql);
if($db->total > 0) {
	for($i=0; $i < $db->total; $i++) {
		$db->fetch($i);
		$moreStat['refundCnt'][$db->dt['payment_agent_type']] += $db->dt['cnt'];
	}
}

// 일별 매출
$dailySales = array();

for($i = 1; $i <= date('t'); $i++) {
	$dailySales[] = array($i . '일', 0);
}

$sql = "SELECT day(ic_date) AS mday, SUM(pt_dcprice) AS price
FROM `shop_order_detail`
WHERE ic_date BETWEEN '{$sdate} 00:00:00' AND '{$edate} 23:59:59'
AND ic_date IS NOT NULL 
GROUP BY `mday`";
$db->query($sql);

if($db->total > 0) {
	for($i=0; $i < $db->total; $i++) {
		$db->fetch($i);
		$idx = ($db->dt['mday'] - 1);
		$dailySales[$idx][1] = intval($db->dt['price']);
	}
}


// 공유메모리 데이타 작성
$data = urlencode(serialize(array(
	'orderState' => $orderState
	, 'productState' => $productState
	, 'qnaStat' => $qnaStat
	, 'moreStat' => $moreStat
	, 'dailySales' => $dailySales
)));

$shmop = new Shared("shop_main_summary_v4");

$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$mall_data_root."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey($data,"shop_main_summary_v4");
