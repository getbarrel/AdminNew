<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

//[S] 리셀러 데이터 로드
@include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
$reseller_data = resellerShared("select");
//[E] 리셀러 데이터 로드

if($act == "addReseller"){

	$datas = array(
		"rsl_div" => $rsl_div, // 매니저 : M, 리셀러 : R
		"rsl_code" => $code,
		"rsl_ok" => "y",
		"incentive_type" => "y",
		"incentive_rate" => $reseller_data[incentive_rate_manager],
		"incentive_way" => $reseller_data[incentive_way]
	);

	resellerAdd($datas);

	$bankDatas = array(
		"bank_name" => $bank_name,
		"bank_owner" => $bank_owner,
		"bank_number" => $bank_number,
		"rsl_code" => $code,
	);

	resellerDataUpdate("reseller_bank", $bankDatas);

	echo "
		<script type='text/javascript'>
			alert('매니저 적용이 정상적으로 처리되었습니다.');
			top.window.close();
			opener.parent.location.reload();
		</script>
	";

	/* ajax 필요없음 2016.4.8
	if($result == true){
		echo "Y";
	}else{
		echo "N";
	}

	exit;
	*/
}

if($act == "costChange"){

	if($update_type == 1){

		include "reseller_query.php";

		$rslArr = $db->fetchall();

		for($i=0; $i<count($rslArr); $i++){
			$rsl_ix[] = $rslArr[$i]['rsl_ix'];
		}

	}

	if(count($rsl_ix) < 1){
		echo "
			<script type='text/javascript'>
				alert('선택된 회원이 없습니다.');
				history.back(-1);
			</script>
		";
		exit;
	}

	for($i=0; $i<count($rsl_ix); $i++){

		$updateDatas["incentive_rate"] = $incentive_rate;
		$updateWhere = " rsl_ix = '".$rsl_ix[$i]."' ";

		resellerDataUpdate("reseller_policy", $updateDatas, "update", $updateWhere);

	}

	echo "
		<script type='text/javascript'>
			alert('정산처리 되었습니다.');
			history.back(-1);
		</script>
	";

}

if($act == "calculate"){

	if($update_type == 1){

		include "account_query.php";

		$acArr = $db->fetchall();

		for($i=0; $i<count($acArr); $i++){
			$ac_ix[] = $acArr[$i]['ac_ix'];
		}

	}

	if(count($ac_ix) < 1){
		echo "
			<script type='text/javascript'>
				alert('선택된 회원이 없습니다.');
				history.back(-1);
			</script>
		";
		exit;
	}

	for($i=0; $i<count($ac_ix); $i++){

		$updateDatas["status"] = "AC";
		$updateDatas["ac_day"] = date("Y-m-d");
		$updateWhere = " ac_ix = '".$ac_ix[$i]."' ";

		resellerDataUpdate("reseller_accounts", $updateDatas, "update", $updateWhere);

	}

	echo "
		<script type='text/javascript'>
			alert('정산처리 되었습니다.');
			history.back(-1);
		</script>
	";

}

?>