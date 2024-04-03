<?
/*
셀러등급설정 common_seller_group 

1. 매출액과 등급점수에 따라서 셀러 등급 자동변경

자동설정 사용유무 : is_auto_yn  = 1:사용 0:미사용
셀러자동설정일(시작일) : setup_date 
산정기간 : period = 1개월
유지기간 : keep_period = 2 개월
회원그룹할인율 : group_type = 1:매출액과 등급 동시 2:매출액만 3:등급점수만 
*/


include("$DOCUMENT_ROOT/class/mysql.class");

$db = new MySQL();

$sql = "select * from common_seller_group where 1 limit 0,1";
$db->query($sql);
$db->fetch();

$status = unserialize($db->dt[status]);			//매출적용상태값	(입금확인,배송준비중,배송지연,배송중,배송완료,거래완료)
$setup_date = $db->dt[setup_date];				//셀러자동설정일(시작일)
$period = $db->dt[period];						//산정기간 : 셀러자동설정일로부터 3개월 산정
$keep_period = $db->dt[keep_period];					//유지기간	2개월
$group_type = $db->dt[group_type];					//회원그룹할인율 ()


$sql = "select 
			csd.company_id,
			csd.sg_ix,
			csd.penalty,
			date_format(csd.sg_change_date,'%Y-%m-%d') as sg_change_date,
			(
			select 
				sum(sod.pt_dcprice)
			from 
				shop_order_detail sod
			
			where 
				sod.company_id = ccd.company_id
				and sod.status in ('DC','AR','AC')
				and dc_date between DATE_ADD(CURDATE(), INTERVAL '-".$period."' MONTH) and now()
			) as total_price
		from
			common_company_detail as ccd 
			inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
		where
			ccd.com_type = 'S'";


$db->query($sql);
$sellers = $db->fetchall();

$fp = fopen('/home/daiso/www/cron/sellergroup_upgrade.txt', 'w+');

$text="--------------------------------------------------( ".date('Y-m-d')." START )--------------------------------------------------";
fwrite($fp,$text."\r\n");

$sql = "select * from common_seller_group where  is_auto_yn = '1' order by sg_ix ASC";		//is_use_yn = '1' and
$db->query($sql);
$groupinfos = $db->fetchall();


foreach($sellers as $member){

	foreach($groupinfos as $groupinfo) {
		
		//2013-11-27::1	회원그룹할인율 : group_type = 1:매출액과 등급 동시 2:매출액만 3:등급점수만 
		if(compare_date($member[sg_change_date],$groupinfo[keep_period])) {//유지기간 이내일때

			//echo $member[sg_ix]." ==> ".$groupinfo[sg_ix]."<br>";

			if($member[sg_ix] < $groupinfo[sg_ix]) { //등업만( 맴버 level 이 비교하는 그룹level이 낮을때만
				
				if($group_type == '1'){	//매출과 등급 동시 체크
					if( compare_price($member[total_price],$groupinfo[st_price],$groupinfo[ed_price]) && compare_panelty($member[penalty],$groupinfo[st_point],$groupinfo[ed_point])){

						$sql = "update common_seller_detail set sg_ix='".$groupinfo[sg_ix]."', sg_change_date=NOW() where company_id='".$member[company_id]."'";
						$db->query($sql);

						$text="셀러코드 [".$member[company_id]."] , 전 그룹레벨 [".$member[sg_ix]."] , 변경된 그룹레벨 [".$groupinfo[sg_ix]."] ";
						fwrite($fp,$text."\r\n");
					}
				
				}else if($group_type == '2'){	//매출만 체크
					if( compare_price($member[total_price],$groupinfo[st_price],$groupinfo[ed_price])){

						$sql = "update common_seller_detail set sg_ix='".$groupinfo[sg_ix]."', sg_change_date=NOW() where company_id='".$member[company_id]."'";
						$db->query($sql);

						$text="셀러코드 [".$member[company_id]."] , 전 그룹레벨 [".$member[sg_ix]."] , 변경된 그룹레벨 [".$groupinfo[sg_ix]."]";
						fwrite($fp,$text."\r\n");
					}
				}else if($group_type == '3'){	//등급점수만 체크
					
					if(compare_panelty($member[penalty],$groupinfo[st_point],$groupinfo[ed_point])){
						$sql = "update common_seller_detail set sg_ix='".$groupinfo[sg_ix]."', sg_change_date=NOW() where company_id='".$member[company_id]."'";
						//echo nl2br($sql)."<br><br>";

						$db->query($sql);

						$text="셀러코드 [".$member[company_id]."] , 전 그룹레벨 [".$member[sg_ix]."] , 변경된 그룹레벨 [".$groupinfo[sg_ix]."]";
						fwrite($fp,$text."\r\n");
					}
				}else{
					continue;
				}

			}else{

				continue;
			}

		}else{// 유지기간 초과

			if($group_type == '1'){	//매출과 등급 동시 체크
				if( compare_price($member[total_price],$groupinfo[st_price],$groupinfo[ed_price]) && compare_panelty($member[penalty],$groupinfo[st_point],$groupinfo[ed_point])){

					$sql = "update common_seller_detail set sg_ix='".$groupinfo[sg_ix]."', sg_change_date=NOW() where company_id='".$member[company_id]."'";
					$db->query($sql);

					$text="셀러코드 [".$member[company_id]."] , 전 그룹레벨 [".$member[sg_ix]."] , 변경된 그룹레벨 [".$groupinfo[sg_ix]."] ";
					fwrite($fp,$text."\r\n");
				}
				
			}else if($group_type == '2'){	//매출만 체크
				if( compare_price($member[total_price],$groupinfo[st_price],$groupinfo[ed_price])){

					$sql = "update common_seller_detail set sg_ix='".$groupinfo[sg_ix]."', sg_change_date=NOW() where company_id='".$member[company_id]."'";
					$db->query($sql);

					$text="셀러코드 [".$member[company_id]."] , 전 그룹레벨 [".$member[sg_ix]."] , 변경된 그룹레벨 [".$groupinfo[sg_ix]."]";
					fwrite($fp,$text."\r\n");
				}
			}else if($group_type == '3'){	//등급점수만 체크
				if(compare_panelty($member[penalty],$groupinfo[st_point],$groupinfo[ed_point])){

					$sql = "update common_seller_detail set sg_ix='".$groupinfo[sg_ix]."', sg_change_date=NOW() where company_id='".$member[company_id]."'";
					//echo nl2br($sql)."<br><br>";
					$db->query($sql);

					$text="셀러코드 [".$member[company_id]."] , 전 그룹레벨 [".$member[sg_ix]."] , 변경된 그룹레벨 [".$groupinfo[sg_ix]."]";
					fwrite($fp,$text."\r\n");
				}
			}else{
				continue;
			}
		}
	}
}

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

function compare_panelty($panelty,$s_panelty,$e_panelty){
	
	//echo "$panelty"." :: "."$s_panelty"." :: "."$e_panelty"."<br>";
	if($panelty > $s_panelty && $panelty < $e_panelty){
		return true;
	}else{
		return false;
	}

}

function compare_price($order_price,$s_price,$e_price){

	if($order_price > $s_price && $order_price < $e_price){
		return true;
	}else{
		return false;
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