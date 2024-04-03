<?
include("$DOCUMENT_ROOT/class/mysql.class");

$db = new MySQL();

$sql="select 
		cmd.code,
		(select date_format(s.end_date,'%Y%m%d') as end_date from service_info s where s.code=cmd.code and s.s_kind = 'MS' and s.s_type='M' and s.si_status = 'SI' order by s.end_date desc limit 0,1) as end_date
	from
		common_member_detail cmd where cmd.gp_ix = '2' ";

$db->query($sql);
$members = $db->fetchall();




$fp=fopen('./premium_member_upgrade.txt', 'a');

$text="--------------------------------------------------( ".date('Y-m-d')." START )--------------------------------------------------";
fwrite($fp,$text."\r\n");

$now = date('Ymd');
foreach ($members as $member) {
	if($member['end_date'] < $now ){
		$sql="update common_member_detail set gp_ix ='1' where code='".$member['code']."' ";
		$db->query($sql);

		$text="회원코드 [".$member[code]."] ";
		fwrite($fp,$text."\r\n");

	}
}
$text="--------------------------------------------------( ".date('Y-m-d')." END )--------------------------------------------------";
fwrite($fp,$text."\r\n");

fclose($fp);

exit;

/*
$db->query("select * from shop_groupinfo where disp='1' order by gp_level desc");
$groupinfos = $db->fetchall();


foreach ($members as $member) {

	foreach ($groupinfos as $groupinfo) {
		
		//2013-11-27::1
		if(compare_date($member[gp_change_date],$groupinfo[keep_priod])) {//유지기간 이내일때
	
			if($member[gp_level] > $groupinfo[gp_level]) { //등업만( 맴버 level 이 비교하는 그룹level이 낮을때만

				if( compare_price($groupinfo[priod],$groupinfo[order_price],$member[m1_total_price],$member[m3_total_price],$member[m6_total_price])){// 등업 선정기준에 맞을때

					$db->query("update common_member_detail set gp_ix='".$groupinfo[gp_ix]."', gp_change_date=NOW() where code='".$member[code]."'");
					give_cupon($groupinfo[mem_group_levelup_coupon],$member[code]);

					$text="회원코드 [".$member[code]."] , 전 그룹레벨 [".$member[gp_level]."] , 변경된 그룹레벨 [".$groupinfo[gp_level]."] , 쿠폰지급 O ";
					fwrite($fp,$text."\r\n");

				}else{
					continue;
				}
			}else{

				continue;
			}

		}else{// 유지기간 초과

			if(compare_price($groupinfo[priod],$groupinfo[order_price],$member[m1_total_price],$member[m3_total_price],$member[m6_total_price])){ //그룹 level의 회원 level보다 높던 낮던 상관없이 조건에 맞는 등급체인지
				$db->query("update common_member_detail set gp_ix='".$groupinfo[gp_ix]."', gp_change_date=NOW() where code='".$member[code]."'");

				if($member[gp_level] > $groupinfo[gp_level]) {//등업시에만 쿠폰 지급
					give_cupon($groupinfo[mem_group_levelup_coupon],$member[code]);
				}

				$text="회원코드 [".$member[code]."] , 전 그룹레벨 [".$member[gp_level]."] , 변경된 그룹레벨 [".$groupinfo[gp_level]."] , 쿠폰지급 X ";
				fwrite($fp,$text."\r\n");

				continue;

			}else{
				continue;
			}
		}
	}
}
*/

$text="--------------------------------------------------( ".date('Y-m-d')." END )--------------------------------------------------";
fwrite($fp,$text."\r\n");

fclose($fp);

// 1. 기간이 산정기간 이내이면 등업만 고려
// 2. 기간이 산정기간을 초과했으면 등업, 강등 고려
// 3. 특정 등급에 해당 될경우 아래 등급은 고려하지 않아도 됨 , exit for; 또는 break 구문등을 이용해서 foreach 문 탈출
// 4. 회원등급은 작은게 높은 등급으로 정리
// 5.

function compare_date($date,$month){
	if($date =='0000-00-00'){
		return true;
		exit;
	}
	$today=date('Y-m-d');
	$sub_date = (strtotime("$today") - strtotime("$date"))/3600/24/30;

	if ($sub_date < $month){
		//echo $sub_date ."--". $month;
		return true;//유지기간 이내일때
	 }else{
		return false;
	}
}

function compare_price($priod,$order_price,$m1_total_price,$m3_total_price,$m6_total_price){
	switch ($priod){
	case "1":
		if($order_price < $m1_total_price){
			return true;
		}else{
			return false;
		}
	  break;

	case "3":
		if($order_price < $m3_total_price){
			return true;
		}else{
			return false;
		}
	  break;

	case "6":
		if($order_price < $m6_total_price){
			return true;
		}else{
			return false;
		}
	  break;
	}
}

function give_cupon($publish_ix,$code){
global $db;

	$sql = "Select publish_ix,use_date_type,publish_date_differ,publish_type,publish_date_type ,regist_date_type, regist_date_differ, use_sdate, use_edate
			from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix = '".$publish_ix."'";
	$db->query($sql);
	$db->fetch();
	$publish_ix = $db->dt[publish_ix];

	if($db->dt[use_date_type] == 1){
		if($db->dt[publish_date_type] == 1){
			$publish_year = date("Y") + $db->dt[publish_date_differ];
		}else{
			$publish_year = date("Y");
		}
		if($db->dt[publish_date_type] == 2){
			$publish_month = date("m") + $db->dt[publish_date_differ];
		}else{
			$publish_month = date("m");
		}
		if($db->dt[publish_date_type] == 3){
			$publish_day = date("d") + $db->dt[publish_date_differ];
		}else{
			$publish_day = date("d");
		}
		$use_sdate = time();
		$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);

	}else if($db->dt[use_date_type] == 2){
		if($db->dt[regist_date_type] == 1){
			$regist_year = date("Y") + $db->dt[regist_date_differ];
		}else{
			$regist_year = date("Y");
		}
		if($db->dt[regist_date_type] == 2){
			$regist_month = date("m") + $db->dt[regist_date_differ];
		}else{
			$regist_month = date("m");
		}
		if($db->dt[regist_date_type] == 3){
			$regist_day = date("d") + $db->dt[regist_date_differ];
		}else{
			$regist_day = date("d");
		}
		$use_sdate = time();
		$use_date_limit = mktime(0,0,0,$regist_month,$regist_day,$regist_year);
	}else if($db->dt[use_date_type] == 3){
		$use_sdate = mktime(0,0,0,substr($db->dt[use_sdate],4,2),substr($db->dt[use_sdate],6,2),substr($db->dt[use_sdate],0,4));
		$use_date_limit = mktime(0,0,0,substr($db->dt[use_edate],4,2),substr($db->dt[use_edate],6,2),substr($db->dt[use_edate],0,4));

	}

	if($db->dt[publish_type] == "1" || $db->dt[publish_type] == "2"){
		$use_sdate = date("Ymd",$use_sdate);
		$use_date_limit = date("Ymd",$use_date_limit);

		$db->query("Select publish_ix from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix' and mem_ix = '".$code."' ");// 등급이 떨어젔다가 다시 후에 다시 쿠폰발급 받을수 있지 않나요? //누군가 주석 해놓은 것을 다시 풀어놓음. 중복 등록되는 현상이 발생해서.. kbk 13/03/21

		if(!$db->total){//누군가 주석 해놓은 것을 다시 풀어놓음. 중복 등록되는 현상이 발생해서.. kbk 13/03/21
			$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST." (regist_ix,publish_ix,mem_ix,open_yn,use_yn,use_sdate, use_date_limit, regdate)
					values ('','".$publish_ix."','".$code."','1','0','$use_sdate','$use_date_limit',NOW())";

			$db->query($sql2);
		}//누군가 주석 해놓은 것을 다시 풀어놓음. 중복 등록되는 현상이 발생해서.. kbk 13/03/21
	}
}

?>