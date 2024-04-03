<?

ini_set("memory_limit","-1");
set_time_limit(9999999);

include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
$P = new msLayOut("000000000000000");

$db = new MySQL();
$db2 = new MySQL();
$db3 = new MySQL();

$shmop = new Shared("b2c_mileage_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"]."/data/arounz_data/_shared/";
$shmop->SetFilePath();
$b2c_reserve_data = $shmop->getObjectForKey("b2c_mileage_rule");
$b2c_reserve_data = unserialize(urldecode($b2c_reserve_data));

$shmop = new Shared("b2b_mileage_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"]."/data/arounz_data/_shared/";
$shmop->SetFilePath();
$b2b_reserve_data = $shmop->getObjectForKey("b2b_mileage_rule");
$b2b_reserve_data = unserialize(urldecode($b2b_reserve_data));

$sql = "SELECT code, mem_type, mileage
		FROM common_user
";
$db->query($sql);
$list = $db->fetchall();

foreach($list as $li){
	$code = $li[code];
	$mem_type = $li[mem_type];
	$mileage = $li[mileage];
	$etc_txt = "기간만료로 인한 적립금 소멸";

	if($mem_type == "C"){
		$reserve_data = $b2c_reserve_data;
	}else if($mem_type == "M"){
		$reserve_data = $b2c_reserve_data;
	}else{
		$reserve_data = $b2c_reserve_data;
	}

	$cancel_year = $reserve_data[cancel_year];	//자동소멸 년	
	$cancel_month = $reserve_data[cancel_month];	//자동소멸 월
	$order_year = $reserve_data[order_year];	//최근주문 년	
	$order_month = $reserve_data[order_month];	//최근주문 월
	$humen_mileage = $reserve_data[order_member_mileage];	//휴면회원

	$cancel_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")."-".$cancel_year." years"));
	$cancel_date = date("Y-m-d H:i:s", strtotime(date($cancel_date)."-".$cancel_month." month"));

	$order_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")."-".$order_year." years"));
	$order_date = date("Y-m-d H:i:s", strtotime(date($order_date)."-".$order_month." month"));


	//2년전까지 적립된 적립금
	$reserve_sql = "SELECT SUM(reserve) as reserve
					FROM shop_reserve_info
					WHERE
						uid = '".$code."'
						AND state IN ('1')
						AND regdate < '".$cancel_date."'
					LIMIT 1
	";
	$db3->query($reserve_sql);
	$db3->fetch();
	$year_reserve = $db3->dt[reserve];
	//2년전까지 적립된 적립금

	//총 사용한 적립금
	$reserve_sql = "SELECT SUM(reserve) as reserve
					FROM shop_reserve_info
					WHERE
						uid = '".$code."'
						AND state = '2'
					LIMIT 1
	";
	$db3->query($reserve_sql);
	$db3->fetch();
	$use_reserve = $db3->dt[reserve];
	//총 사용한 적립금

	if($year_reserve > $use_reserve){
		$delete_reserve = $year_reserve - $use_reserve;
		InsertReserveInfo($code,"","","",$delete_reserve,"2","21",$etc_txt,'mileage',"");
		echo " [".$code."] " . number_format($delete_reserve) . "원 차감";
		echo "<br/>";
	}
}

//회원탈퇴로 인한 소멸
$sql = "SELECT code FROM common_dropmember";

$db->query($sql);
$list = $db->fetchall();

foreach($list as $li){

	$code = $li[code];
	
	$reserve_sql = "SELECT SUM(CASE WHEN state = 2 THEN -reserve WHEN state = 0 THEN 0 ELSE reserve END) AS reserve
					FROM shop_reserve_info
					WHERE uid = '".$code."'
					LIMIT 1
	";
	$db3->query($reserve_sql);
	$db3->fetch();
	$delete_reserve = $db3->dt[reserve];

	if($delete_reserve > 0){
		InsertReserveInfo($code,"","","",$delete_reserve,"2","21","회원탈퇴로 인한 자동 소멸",'mileage',"","delete");
	}	
}
//회원탈퇴로 인한 소멸

/*
	특정회원만 적립금이 두번 지급되는 경우가 있는데 원인을 알수가 없음
	임시로 shop_reserve_info에 uid , reserve , DATE_FORMAT(regdate, '%Y%m%d') 로 GROUP BY를 걸어서
	하루에 2번 지급된 경우 내역 삭제처리
	16.07.22 pys
*/

$sql = "SELECT t1.*
		FROM
			(SELECT
				reserve_id ,
				uid , 
				COUNT(*) AS cnt
			FROM shop_reserve_info
			WHERE
				(etc = '기간만료로 인한 적립금 소멸'
				OR etc = '회원탈퇴로 인한 자동 소멸')
				AND regdate >= '".date('Y-m-d')." 00:00:00'
				AND regdate <= '".date('Y-m-d')." 23:59:59'
			GROUP BY
				DATE_FORMAT(regdate, '%Y%m%d') ,
				uid ,
				reserve
			) AS t1
		WHERE t1.cnt > 1
";
$db->query($sql);
$reserves = $db->fetchall();
$i = 1;

foreach($reserves as $key => $val){

	$reserve_id = $val["reserve_id"];

	$db->query("DELETE FROM shop_reserve WHERE reserve_id = '".$reserve_id."'");
	$db->query("DELETE FROM shop_reserve_info WHERE reserve_id = '".$reserve_id."'");

	$sql = "SELECT ifnull(sum(case when r.state = '2' then -reserve else reserve end),0) as history_reserve
			FROM shop_reserve as r 
			WHERE
				r.auto_cancel = 'N'
				AND r.state in ('2','1')
				AND r.uid = '".$val['uid']."'
	";
	$db->query($sql);
	$db->fetch();

	$user_sql = "update ".TBL_COMMON_USER." set mileage = '".$db->dt[history_reserve]."' where code = '".$val['uid']."'";
	$db->query($user_sql);

	echo "[".$i."] 아이디 : ".$reserve_id;
	echo "<br/>";
	$i++;
}

/* 임시처리 끝 */

?>