<?

//[S] 컬럼추가

columUpdate();

function columUpdate()
{
	$db = new MySQL;

	$sql = "SHOW COLUMNS FROM `reseller_policy` LIKE 'rsl_div'";
	$db->query($sql);
	if($db->total == 0){
		$sql = "ALTER TABLE `reseller_policy`  
				ADD COLUMN `rsl_div` ENUM('R','M') DEFAULT 'R'   NOT NULL  COMMENT '리셀러구분 (R:리셀러, M:매니저)' AFTER `rsl_ix`
		";
		$db->query($sql);
	}
	$sql = "SHOW COLUMNS FROM `reseller_policy` LIKE 'rsl_manager'";
	$db->query($sql);
	if($db->total == 0){
		$sql = "ALTER TABLE `reseller_policy`   
				ADD COLUMN `rsl_manager` INT(10) NULL  COMMENT '매니저코드' AFTER `rsl_ix`
		";
		$db->query($sql);
	}
	$sql = "SHOW COLUMNS FROM `reseller_bank` LIKE 'redate'";
	$db->query($sql);
	if($db->total == 1){
		$sql = "ALTER TABLE `reseller_bank`
				CHANGE `redate` `regdate` DATETIME NULL  COMMENT '등록일';
		";
		$db->query($sql);
	}
}

//[E] 컬럼추가


//[S] 리셀러 데이터 로드

function resellerShared($act, $data="")
{
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");

	$shmop = new Shared("reseller_rule");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();

	if($act == "select"){
		$reseller_data = $shmop->getObjectForKey("reseller_rule");
		$reseller_data = unserialize(urldecode($reseller_data));
	}else if($act == "insert"){
		$reseller_data = urlencode(serialize($data));
		$shmop->SetFilePath();
		$shmop->setObjectForKey($reseller_data,"reseller_rule");
	}else{
		return false;
	}

	return $reseller_data;
}

//[E] 리셀러 데이터 로드


//[S] 리셀러 코드 생성

function generateCode()
{
	$db = new MySQL;

	$sql = "SELECT rsl_ix FROM reseller_policy WHERE rsl_code = '".$_SESSION['user']['code']."' LIMIT 1";
	$db->query($sql);
	$db->fetch();

	$resellerCode = $_SERVER["HTTP_HOST"]."/banner.link.php?rsl_ix=".$db->dt[rsl_ix]."&pid=상품코드(10자리)";

	return $resellerCode;
}

//[E] 리셀러 코드 생성


//[S]리셀러 정보 (쿠키)

function resellerCookie()
{
	if(!empty($_COOKIE["rsl_info"])){
		//리셀러 정보가 이미 쿠키에 있다면
		$rslInfo = urldecode($_COOKIE["rsl_info"]);
		$rslInfo = unserialize($rslInfo);
		echo "<pre>";
		//print_r($rslInfo);
		foreach($rslInfo as $key => $val){
			$now = date("Y-m-d H:i:s");
			$date = date("Y-m-d H:i:s", $val["period"]);
			if($now > $date){
				//현재시간이 쿠키에 저장된 시간보다 크다면 쿠키의 relseller 데이터 삭제
				unset($rslInfo[$key]);
			}
		}
	}else{
		$rslInfo = array();
	}

	return $rslInfo;
}

//[E]리셀러 정보 (쿠키)


//[S]리셀러 정보 주문저장

function resellerOrder($od_ix)
{
	$db = new MySQL;

	if(count(resellerCookie()) > 0){
		//쿠키에 리셀러 배열이 존재한다면

		$sql = "SELECT
					pid
				FROM
					shop_order_detail
				WHERE
					od_ix = '".$od_ix."'
				LIMIT 1";

		$db->query($sql);
		$db->fetch();
		$pid = $db->dt[pid];
		
		$rslInfo = resellerCookie();

		foreach($rslInfo as $key => $val){
			if($pid == $key){
				//order_detail의 pid == 쿠키의 pid && 현재시간 <= 쿠키의 시간
				$sql = "UPDATE
							shop_order_detail
						SET
							rsl_ix = '".$val["rsl_ix"]."'
						WHERE
							od_ix = '".$od_ix."'
				";
				$db->query($sql);
			}
		}
	}
}

//[E]리셀러 정보 주문저장


//[S] 리셀러 회원 등록

function registResellerMember($code, $recom_id)
{
	$db = new MySQL;

	$reseller_data = resellerShared("select");

	if($reseller_data[rsl_use] == "y"){
		//리셀러 사용시

		if($_COOKIE[rsl_id]){
			//쿠키에 리셀러 정보가 심어져 있다면
			$rsl_id = $_COOKIE[rsl_id];
			$flowin_type = $_COOKIE[flowin_type];
		}else{
			$rsl_id = $recom_id;
			$flowin_type = 3;
		}

		$flowin_url = $_COOKIE[flowin_url];

		if($rsl_id){
			//리셀러 아이디가 존재할때

			$sql = "SELECT code as rsl_code FROM ".TBL_COMMON_USER." INNER JOIN ".TBL_COMMON_MEMBER_DETAIL." using (code) WHERE id='".$rsl_id."' LIMIT 1";
			$db->query($sql);
			$db->fetch();
			$rsl_code = $db->dt[rsl_code];

			$db->query("SELECT id from reseller_dropmember where rsl_code='".$rsl_code."' ");

			if(dropReseller($rsl_code)){
				//탈퇴한 리셀러가 아니라면

				if($rsl_code){

					//[S] 유입자 정보 데이터 생성
					$datas = array(
						"rsl_code" => $rsl_code,
						"flowin_code" => $code,
						"flowin_url" => $flowin_url,
						"flowin_type" => $flowin_type
					);
					//[E] 유입자 정보 데이터 생성

					resellerDataUpdate("reseller_flowin_detail", $datas); // 테이블명, 저장할 데이터

				}
			}
		}
	}
}

//[E] 리셀러 회원 등록


//[S] 정산 입력

function resellerAccounts($od_ix)
{
	$db = new MySQL;

	$sql = "SELECT oid, incentive_ix FROM reseller_incentive WHERE od_ix = '".$od_ix."' LIMIT 1";
	$db->query($sql);

	if($db->total != 1){
		// 해당 od_ix가 이미 정산 되지 않았다면
		$reseller_data = resellerShared("select");
	
		if($reseller_data[rsl_use] == "y"){
/*
			$sql = "SELECT uid, payment_price FROM shop_order WHERE oid ='".$oid."' LIMIT 1";
			$db->query($sql);
			$db->fetch();
			$flowin_code = $db->dt[uid];
			$payment_price = $db->dt[payment_price];

			$sql = "SELECT rsl_code FROM reseller_flowin_detail WHERE flowin_code = '".$flowin_code."' LIMIT 1";
			$db->query($sql);
			$db->fetch();
			$rsl_code = $db->dt[rsl_code];
*/

			//[S] order_detail 정보 셀렉트
			$sql = "SELECT oid, rsl_ix FROM shop_order_detail where od_ix = '".$od_ix."' and rsl_ix > 0 LIMIT 1";
			$db->query($sql);
			$db->fetch();
			$rsl_ix = $db->dt[rsl_ix];
			$oid = $db->dt[oid];
			//[E] order_detail 정보 셀렉트

			if($db->total){

				$sql = "SELECT rsl_code FROM reseller_policy WHERE rsl_ix = '".$rsl_ix."' LIMIT 1";
				$db->query($sql);
				$db->fetch();
				$rsl_code = $db->dt[rsl_code];

				if($db->total){
					//유입 회원일때

					if(dropReseller($rsl_code)){
						//탈퇴한 리셀러가 아니라면

						$this_status = " AND status IN ('BF') "; // 정산 데이터 추가할 status

						$sql = "SELECT
									rsl_div,
									rsl_manager,
									incentive_rate
								FROM
									reseller_policy
								WHERE
									rsl_code = '".$rsl_code."'
								LIMIT 1";
						$db->query($sql);
						$db->fetch();

						$rsl_div = $db->dt[rsl_div]; // 리셀러 구분 (R:리셀러, M:매니저)
						$rsl_manager = $db->dt[rsl_manager]; // 매니저 코드
						$incentive_rate = $db->dt[incentive_rate]; // 수수료율

						//[S] 정산 데이터 생성
						$datas = array(
							"incentive_type" => 2,
							"oid" => $oid
						);
						//[E] 정산 데이터 생성

						//[S] order_detail 정보 셀렉트
						$sql = "SELECT o.user_code, d.od_ix, d.ptprice, d.pt_dcprice FROM shop_order_detail d INNER JOIN shop_order o ON d.oid = o.oid where d.od_ix = '".$od_ix."' LIMIT 1";
						$db->query($sql);
						$db->fetch();
						//[E] order_detail 정보 셀렉트

						if($reseller_data[pay_method] == 1){
							//정산방법 -> 매출액
							$pay_price = $db->dt[ptprice];
						}else{
							//정산방법 -> 실 결제금액
							$pay_price = $db->dt[pt_dcprice];
						}

						$incentive = round($pay_price * $incentive_rate / 100);

						$datas["od_ix"] = $db->dt[od_ix];
						$datas["flowin_code"] = $db->dt[user_code];
						$datas["rsl_code"] = $rsl_code;
						$datas["incentive"] = $incentive;
						$datas["incentive_rate"] = $incentive_rate;

						resellerDataUpdate("reseller_incentive", $datas); // 테이블명, 저장할 데이터

						//[S] 리셀러일때 매니저의 정산 데이터도 저장
						if($rsl_div == "R"){
							$sql = "SELECT rsl_code, incentive_rate FROM reseller_policy WHERE rsl_ix = '".$rsl_manager."' LIMIT 1";
							$db->query($sql);
							$db->fetch();
							$rsl_code = $db->dt[rsl_code];
							$manager_incentive_rate = $reseller_data["incentive_rate"] - $incentive_rate;

							$incentive_total_rate =  $reseller_data["incentive_rate"]; // 리셀러와 매니저 수수료율의 합
							$incentive_total = round($pay_price * $incentive_total_rate / 100); // 총 수수료
							$manager_incentive = $incentive_total - $incentive; // 매니저 수수료 = 총 수수료 - 리셀러 수수료

							$datas["rsl_code"] = $rsl_code;
							$datas["incentive"] = $manager_incentive;
							$datas["incentive_rate"] = $manager_incentive_rate;

							resellerDataUpdate("reseller_incentive", $datas); // 테이블명, 저장할 데이터
						}
						//[E] 리셀러일때 매니저의 정산 데이터도 저장

					}
				}
			}
		}
	}
}

//[E] 정산 입력


//[S] 리셀러 데이터 저장

function resellerDataUpdate($table, $datas, $act="insert", $where="")
{
	$db = new MySQL;

	$sql = $act." ".$table." SET ";

	$i = 1;
	$j = 1;

	foreach($datas as $key => $value){
		$sql .= $key." = '".$value."',";
	}

	if($act == "insert"){
		$sql .= " regdate = NOW() ";
	}else if($act == "update"){
		$sql = substr($sql, 0, -1);
		$sql .= " WHERE ".$where;
	}

	$db->query($sql);

	return $db->insert_id();
}

//[E] 리셀러 데이터 저장


//[S] 리셀러 저장

function resellerAdd($datas)
{
	$db = new MySQL;

	//[S] 선택된 회원이 리셀러에 존재하는지 -> 존재한다면 return false
	$sql = "SELECT rsl_ix FROM reseller_policy WHERE rsl_code = '".$datas['rsl_code']."'";
	$db->query($sql);
	$total = $db->total;
	//[E] 선택된 회원이 리셀러에 존재하는지 -> 존재한다면 return false

	if($total > 0){
		return false;
	}else{
	
		$sql = " INSERT INTO reseller_policy SET ";

		foreach($datas as $key => $value){
			$sql .= $key." = '".$value."',";
		}

		$sql .= " regdate = NOW() ";
		
		$db->query($sql);

		return true;
	}
}

//[E] 리셀러 저장


//[S] 정산신청 크론

function resellerCalculate()
{
	$db = new MySQL;

	$reseller_data = resellerShared("select");

	$calculateDay = str_pad($reseller_data["incentive_day"],"2","0",STR_PAD_LEFT);
	$calculateDate = date("Y-m-").$calculateDay;

	if(date("d") == $calculateDay){
	
		$sql = "SELECT
					r.rsl_code,
					r.incentive_rate,
					p.incentive_way,
					sum(incentive) as incentive
				FROM
					reseller_incentive r
				INNER JOIN
					reseller_policy p
				ON
					r.rsl_code = p.rsl_code
				WHERE
					r.ac_ix IS NULL
				GROUP BY
					r.rsl_code";

		$db->query($sql);
		$calculs = $db->fetchall();

		for($i=0; $i<count($calculs); $i++){

			//[S] 등록 데이터 생성
			$datas = array(
				"rsl_code" => $calculs[$i][rsl_code],
				"sum_incentive" => $calculs[$i][incentive],
				"last_incentive" => $calculs[$i][incentive],
				"fee_rate" => $calculs[$i][incentive_rate],
				"status" => "AR",
				"way" => $calculs[$i][incentive_way]
			);
			//[E] 등록 데이터 생성

			$ac_ix = resellerDataUpdate("reseller_accounts", $datas); // 테이블명, 데이터

			//[S] 정산예정 -> ac_ix 입력
			$updateDatas["ac_ix"] = $ac_ix;
			$updateWhere = " rsl_code = '".$calculs[$i][rsl_code]."' AND ac_ix IS NULL ";

			resellerDataUpdate("reseller_incentive", $updateDatas, "update", $updateWhere);
			//[E] 정산예정 -> ac_ix 입력

			echo "정산입력 (ac_ix : ".$ac_ix.")";
			echo "<br/>";

		}
	}
}

//[E] 정산신청 크론


//[S] 무통장 계좌정보

function getBankInfo()
{
	global $arr_banks_name;

	$str = "";
	
	$str .= "<select name='bank_name' validation='true' style='width:150px;' class='custom-select'>";
	$str .= "	<option value=''>선택해주세요.</option>";

		foreach($arr_banks_name as $key => $val){
			$str .= "
				<option value='".$arr_banks_name[$key]."'>".$arr_banks_name[$key]."</option>
			";
		}

	$str .= "</select>";

	return $str;

}

//[E] 무통장 계좌정보


//[S] 매니저 아이디 검사

function managerCheck($id)
{
	$db = new MySQL;

	$sql = "SELECT code FROM common_user WHERE id = '".$id."' LIMIT 1";
	$db->query($sql);
	$db->fetch();
	$code = $db->dt[code];

	$sql = "SELECT rsl_ix FROM reseller_policy WHERE rsl_code = '".$code."' AND rsl_div = 'M' LIMIT 1";
	$db->query($sql);
	$db->fetch();

	if($db->total > 0){
		echo $db->dt[rsl_ix];
	}else{
		echo false;
	}
}

//[E] 매니저 아이디 검사


//[S] 탈퇴한 리셀러 체크

function dropReseller($code)
{
	$db = new MySQL;

	$sql = "SELECT id FROM reseller_dropmember WHERE rsl_code='".$code."' LIMIT 1";
	$db->query($sql);

	if($db->total > 0){
		// 탈퇴한 회원이 존재하면 false
		return false;
	}else{
		// 탈퇴한 회원이 존재하지 않으면 true;
		return true;
	}
}

//[E] 탈퇴한 리셀러 체크


//[S] 프론트 매니저코드 셀렉트

function getManagerCode()
{
	$db = new MySQL;
	
	$sql = "SELECT rsl_ix FROM reseller_policy WHERE rsl_code = '".$_SESSION['user']['code']."' LIMIT 1";
	$db->query($sql);
	$db->fetch();
	$rsl_manager = $db->dt[rsl_ix];
	
	return $rsl_manager;
}

//[E] 프론트 매니저코드 셀렉트


?>