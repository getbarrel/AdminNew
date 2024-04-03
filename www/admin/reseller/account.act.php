<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;


if($act == "account_all")
{

	$sql = "select * from reseller_accounts where ac_ix='".$ac_ix."' ";
	$db->query($sql);
	$db->fetch();


	$last_incentive = $db->dt[last_incentive];
	$way = $db->dt[way];
	$rsl_code = $db->dt[rsl_code];
	

	if($way == 1){
		$db->query("insert into ".TBL_SHOP_RESERVE_INFO." (uid,reserve,state,etc,regdate) values ('".$rsl_code."','".$last_incentive."','1','리셀러 인센티브',NOW()) ");//적립금 1이 RESERVE_STATUS_COMPLETE
		$reserve_id = mysql_insert_id();
	}elseif($way == 2){
		//?? 예치금은?
	}
	
	$sql = "insert into reseller_accounts_info (ac_ix,price,way,regdate,id) values('".$ac_ix."','".$last_incentive."','".$way."',NOW(),'".$reserve_id."')";
	$db->query($sql);

	$sql = "update reseller_accounts set ac_price='".$last_incentive."' , status='AC' where ac_ix='".$ac_ix."' ";
	$db->query($sql);


	echo("<script>history.back();</script>");
}

if($act == "list_account")
{
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");

	$shmop = new Shared("reseller_rule");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$reseller_rule = $shmop->getObjectForKey("reseller_rule");
	$reseller_rule = unserialize(urldecode($reseller_rule));

	$sql = "select ri.rsl_code,sum(ri.incentive) as sum_incentive, rp.incentive_way  from reseller_incentive ri inner join reseller_policy rp using(rsl_code) where ac_ix is null GROUP by rsl_code ";
	$db->query($sql);

	$account_info = $db->fetchall("object");
	$account_info_count = count($account_info);

	for ($i = 0; $i < $account_info_count; $i++){
		
		$rsl_code = $account_info[$i]["rsl_code"];
		$sum_incentive =  $account_info[$i]["sum_incentive"];
		$incentive_way =  $account_info[$i]["incentive_way"];
		$incentive_day = $reseller_rule[incentive_day];
		
		$month = date('m');
		$year = date('Y');
		$mkdate = mktime(6, 28, 31, $month+1, 8, $year);
		$mkYM = date('Y-m-',$mkdate);
		$ac_day = $mkYM.$incentive_day;

		if($incentive_way == 3){
			$fee_rate = 3.3;
			$last_incentive = $sum_incentive * 3.3 / 100;
		}else{
			$fee_rate = 0;
			$last_incentive = $sum_incentive;
		}
		

		$sql = "insert into reseller_accounts (rsl_code,ac_day,sum_incentive,fee_rate,last_incentive,regdate,way) values('".$rsl_code."','".$ac_day."','".$sum_incentive."','".$fee_rate."','".$last_incentive."',NOW(),'".$incentive_way."')";
		$db->query($sql);

		$ac_ix = mysql_insert_id();
		
		$sql = "update reseller_incentive set ac_ix='".$ac_ix."' where ac_ix is null and rsl_code='".$rsl_code."' ";
		$db->query($sql);

	}

	echo("<script>history.back();</script>");
}

if($act == "account_modify")
{

	if($status=='AC'){

		$sql = "update reseller_accounts set ac_price='".$ac_price."', note='".$note."' where ac_ix='".$ac_ix."'";
		$db->query($sql);

		if($way==3){//현금 지급일때
		
			$price = $ac_price - $ac_price_befor ;
			
			$sql = " insert into reseller_incentive (rsl_code,flowin_code,regdate,incentive,incentive_type) values('".$rsl_code."','00000000000000000000000000000000',NOW(),'".$price."','3') ";
			$db->query($sql);

		}
	
	}elseif($status=='AR'){

		$sql = "update reseller_accounts set last_incentive='".$last_incentive."', note='".$note."' where ac_ix='".$ac_ix."'";
		$db->query($sql);

	}


	echo("<script>alert('정상적으로 수정되었습니다.');self.close();</script>");
}

?>