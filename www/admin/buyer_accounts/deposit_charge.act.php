<?
include("../class/layout.class");
$db = new database;

if($act == "deposit_cancel"){
	$sql = "select * from shop_deposit_charge_info where history_ix  = '".$history_ix."' ";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		/*예치금 처리 관련 데이터 JK160804*/
		$deposit_data = array();

		$deposit_data['user_code'] = $db->dt['uid'];
		$deposit_data['history_ix'] = $history_ix;
		$deposit_data['oid'] = $db->dt["oid"];
		$deposit_data['deposit'] = $db->dt["deposit"];
		$deposit_data['history_type'] = 2;
		$deposit_data['etc'] = "예치금 신청 취소";
		$deposit_data['charger_ix'] = $_SESSION[admininfo][charger_ix];
		
		if(function_exists(DepositManagement)){
			DepositManagement($deposit_data);
			echo 1;//성공
		}else{
			echo 0;		
		}
		
	}else{
		echo 0;//실패
	}
}


if($act == "deposit_in_complete"){
	$sql = "select * from shop_deposit_charge_info where history_ix  = '".$history_ix."' ";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		/*예치금 처리 관련 데이터 JK160804*/
		$deposit_data = array();

		$deposit_data['user_code'] = $db->dt['uid'];
		$deposit_data['history_ix'] = $history_ix;		
		$deposit_data['oid'] = $db->dt["oid"];
		$deposit_data['deposit'] = $db->dt["deposit"];
		$deposit_data['history_type'] = 3;
		$deposit_data['etc'] = "예치금 입금완료";
		$deposit_data['charger_ix'] = $_SESSION[admininfo][charger_ix];
		$deposit_data['use_type'] = "P";
		
		if(function_exists(DepositManagement)){
			DepositManagement($deposit_data);
			echo 1;//성공
		}else{
			echo 0;		
		}
		
	}else{
		echo 0;//실패
	}	
}


if($act == "deposit_w_cancel"){
	$sql = "select * from shop_deposit_charge_info where history_ix  = '".$history_ix."' ";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		/*예치금 처리 관련 데이터 JK160804*/
		$deposit_data = array();

		$deposit_data['user_code'] = $db->dt['uid'];
		$deposit_data['oid'] = $db->dt['oid'];
		$deposit_data['history_ix'] = $history_ix;		
		$deposit_data['deposit'] = $db->dt["deposit"];
		$deposit_data['history_type'] = 6;
		$deposit_data['etc'] = "예치금 출금취소";
		$deposit_data['charger_ix'] = $_SESSION[admininfo][charger_ix];
		$deposit_data['use_type'] = "P";
		
		if(function_exists(DepositManagement)){
			DepositManagement($deposit_data);
			echo 1;//성공
		}else{
			echo 0;		
		}
		
	}else{
		echo 0;//실패
	}

}


if($act == "deposit_w_complete"){
	$sql = "select * from shop_deposit_charge_info where history_ix  = '".$history_ix."' ";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		/*예치금 처리 관련 데이터 JK160804*/
		$deposit_data = array();

		$deposit_data['user_code'] = $db->dt['uid'];
		$deposit_data['oid'] = $db->dt['oid'];
		$deposit_data['history_ix'] = $history_ix;		
		$deposit_data['deposit'] = $db->dt["deposit"];
		$deposit_data['history_type'] = 7;
		$deposit_data['etc'] = "예치금 출금 확정";
		$deposit_data['charger_ix'] = $_SESSION[admininfo][charger_ix];

		
		if(function_exists(DepositManagement)){
			DepositManagement($deposit_data);
			echo 1;//성공
		}else{
			echo 0;		
		}
		
	}else{
		echo 0;//실패
	}

}

//관리자에서 입금 처리 할때 사용 JK160830
if($act == "deposit_insert"){
	/*예치금 처리 관련 데이터 JK160804*/
	$deposit_data = array();
	
	if($use_type == "P"){
		$history_type = 3;//입금완료
	}else if ($use_type == "W"){
		$history_type = 4;//사용완료
	}
	$deposit_data['charger_ix'] = $_SESSION['admininfo']['charger_ix'];
	$deposit_data['user_code'] = $uid;
	$deposit_data['oid'] = date("YmdHi")."-".rand(1000, 9999);;
	$deposit_data['deposit'] = $deposit;
	$deposit_data['use_type'] = $use_type;
	$deposit_data['history_type'] = $history_type;
	$deposit_data['etc'] = "[관리자 시스템]".$etc;

	
	if(function_exists(DepositManagement)){
		$return = DepositManagement($deposit_data);
	}
	
	if($return == 'fail'){
		echo "<script>alert('등록 실패');top.location.reload();</script>";
	}else{
		echo "<script>alert('등록 성공');top.location.reload();</script>";
	} 
	exit;
}

if($act == "deposit_select_update"){

	if($update_type == '1'){	//검색한 회원
		
		if($search_searialize_value){
			$unserialize_search_value = unserialize(urldecode($search_searialize_value));
			extract($unserialize_search_value);
		}
		
		
		include "./deposit_where.php";
		
		$sql = "select history_ix from shop_deposit_charge_info $where";
		$db->query ($sql);
		$history_ix = $db->fetchall();
		
		for($i=0;$i<count($history_ix);$i++){
			$history_ix[$i] = $history_ix[$i][history_ix];
		}
	}else if($update_type == '2'){	//선택한 회원
		$history_ix = $history_ix;
	}

	$s_cnt = 0;
	$f_cnt = 0;
	for($i=0; $i < count($history_ix); $i++){
		$sql = "select * from shop_deposit_charge_info where history_ix  = '".$history_ix[$i]."' ";
		$db->query($sql);
		if($db->total){
			$db->fetch();
			/*예치금 처리 관련 데이터 JK160804*/
			$deposit_data = array();

			$deposit_data['user_code'] = $db->dt['uid'];
			$deposit_data['oid'] = $db->dt['oid'];
			$deposit_data['history_ix'] = $history_ix[$i];		
			$deposit_data['deposit'] = $db->dt["deposit"];
			$deposit_data['history_type'] = $change_history_type;
			$deposit_data['etc'] = $etc;
			$deposit_data['charger_ix'] = $_SESSION[admininfo][charger_ix];
			if($change_history_type == "6"){
				$deposit_data['use_type'] = "P";
			}

			if(function_exists(DepositManagement)){
				$return = DepositManagement($deposit_data);
			}
			
			if($return == 'fail'){
				$f_cnt ++;
			}else{
				$s_cnt ++;
			} 			
		}else{
			$f_cnt ++;
		}
	}

	echo "<script>alert('등록 성공".$s_cnt." 건 등록실패 ".$f_cnt."건 처리 완료');top.location.reload();</script>";
//	print_r($history_ix);
//	print_r($_POST);
	exit;
}
?>