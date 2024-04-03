<?
///////////////////////////////////////////////////////////////////
//
// 제목 : 뱅크다 등록처리 : 이현우(2013-05-13)
//
///////////////////////////////////////////////////////////////////
//ini_set("display_errors",1);
//error_reporting(E_ALL);
include("../class/layout.class");

exit;
$db = new Database;

if(!$admininfo[mall_ix]){
$sql = "select mall_data_root, mall_type, mall_ix,mall_ename from shop_shopinfo where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();			

$admininfo[mall_data_root] = $db->dt[mall_data_root];
$admininfo[admin_level] = 9;
$admininfo[language] = 'korea';
$admininfo[mall_type] = $db->dt[mall_type];
$admininfo[mall_ix] = $db->dt[mall_ix];
$admin_config[mall_data_root] = $db->dt[mall_data_root];

}


$db->dbcon = mysql_connect("118.217.181.188","forbiz","vhqlwm2011") or $db->error();
mysql_select_db("mallstory_service",$db->dbcon) or $db->error();

$act = $_POST["act"];
$directAccess = "y";
$service_type = "standard";
$partner_id = "mallstory";
$Command = "update";
$partner_name = "(주)포비즈코리아";

// 계좌추가
if ($act == "insert"){
	// 뱅크다 가입자 조회
	$member_seq = $_POST["member_seq"];
	if ($member_seq){
		$sql = "SELECT bankda_userid, bankda_userpw FROM bankda_member  WHERE mall_ix = '".$admininfo[mall_ix]."'";
		$db->query($sql);
		$db->fetch(0);				
		$bankda_userid = $db->dt["bankda_userid"];
		$bankda_userpw = $db->dt["bankda_userpw"];
	}else{
		echo "<script>alert('이용자가 아닙니다. 먼저 이용자 가입을 해주시기 바랍니다.'); location.href='bankda.php'; </script>";
		exit;
	}
	$bkacctno		= $_POST["Bkacctno"];
	$Bkjukyo		= $_POST["Bkjukyo"];
	$bkcode		= $_POST["sel_bank"];
	$bkdiv			= $_POST["business_gubun"];	
	$Bkpass		= $_POST["Bkpass"];
	$webid			= $_POST["BkinternetId"];
	$webpw		= $_POST["BkinternetPw"];

	if ($bkdiv=="P"){
		$Mjumin_1 = $_POST["jumin1"];
		$Mjumin_2 = $_POST["jumin2"];
	}else if ($bkdiv=="C"){
		$Bjumin_1 = $_POST["business_num1"];
		$Bjumin_2 = $_POST["business_num2"];
		$Bjumin_3 = $_POST["business_num3"];
	}

	$post_data = "directAccess=$directAccess";
	$post_data.="&service_type=$service_type";
	$post_data.="&partner_id=$partner_id";
	$post_data.="&Command=$Command";
	$post_data.="&user_id=$bankda_userid";
	$post_data.="&user_pw=$bankda_userpw";
	$post_data.="&bkdiv=$bkdiv";
	$post_data.="&bkcode=$bkcode";
	$post_data.="&bkacctno=".str_replace("-","",$bkacctno);
	$post_data.="&bkacctpno_pw=$Bkpass";
	$post_data.="&Mjumin_1=$Mjumin_1";
	$post_data.="&Mjumin_2=0000000";
	$post_data.="&Bjumin_1=$Bjumin_1";
	$post_data.="&Bjumin_2=$Bjumin_2";
	$post_data.="&Bjumin_3=$Bjumin_3";
	$post_data.="&webid=$BkinternetId";
	$post_data.="&webpw=$BkinternetPw";
	 	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_URL, "https://ssl.bankda.com/partnership/user/account_add.php"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			
	$result = curl_exec($ch);
	$result = iconv("euc-kr","utf-8",$result);
	
	if(!curl_errno($ch) && $result == "ok"){	
		$info = curl_getinfo($ch);	 
		curl_close($ch);		 
		$sql = "INSERT INTO bankda_member_bank (seq, member_seq, Bkacctno, bank_code, bUse,  regdate) VALUES";
		$sql.=" ('', ".$member_seq.", '$Bkacctno', '$bkcode', 'Y', now() ) ";
		$db->query($sql);
		if (!$db->result){
			echo "DB 등록에 실패하였습니다.";
		}else{
			echo "<script>alert('계좌추가 되었습니다.'); location.href='bankda.php'; </script>";
		}
	}else{
		echo "err : ".curl_errno($ch)." ".$result;
		//echo "<script>alert('".$result."');</script>";
		exit;
	}
}

// 계좌수정
if ($act == "bankUpd"){
	// 뱅크다 가입자 조회
	$member_seq = $_POST["member_seq"];
	$member_bank_seq = $_POST["member_bank_seq"];
 
	if ($member_seq){
		$sql = "SELECT bankda_userid, bankda_userpw FROM bankda_member  WHERE mall_ix = '".$admininfo[mall_ix]."'";
		$db->query($sql);
		$db->fetch(0);				
		$bankda_userid = $db->dt["bankda_userid"];
		$bankda_userpw = $db->dt["bankda_userpw"];
	}else{
		echo "<script>alert('이용자가 아닙니다. 먼저 이용자 가입을 해주시기 바랍니다.'); location.href='bankda.php'; </script>";
		exit;
	}
	$bkacctno		= $_POST["Bkacctno"];
	$Bkjukyo		= $_POST["Bkjukyo"];
	$bkcode		= $_POST["sel_bank"];
	$bkdiv			= $_POST["business_gubun"];	
	$Bkpass		= $_POST["Bkpass"];
	$webid			= $_POST["BkinternetId"];
	$webpw		= $_POST["BkinternetPw"];

	if ($bkdiv=="P"){
		$Mjumin_1 = $_POST["jumin1"];
		$Mjumin_2 = $_POST["jumin2"];
	}else if ($bkdiv=="C"){
		$Bjumin_1 = $_POST["business_num1"];
		$Bjumin_2 = $_POST["business_num2"];
		$Bjumin_3 = $_POST["business_num3"];
	}

	$post_data = "directAccess=$directAccess";
	$post_data.="&service_type=$service_type";
	$post_data.="&partner_id=$partner_id";
	$post_data.="&Command=$Command";
	$post_data.="&user_id=$bankda_userid";
	$post_data.="&user_pw=$bankda_userpw";
	$post_data.="&bkdiv=$bkdiv";
	$post_data.="&bkcode=$bkcode";
	$post_data.="&bkacctno=".str_replace("-","",$bkacctno);
	$post_data.="&bkacctpno_pw=$Bkpass";
	$post_data.="&Mjumin_1=$Mjumin_1";
	$post_data.="&Mjumin_2=0000000";
	$post_data.="&Bjumin_1=$Bjumin_1";
	$post_data.="&Bjumin_2=$Bjumin_2";
	$post_data.="&Bjumin_3=$Bjumin_3";
	$post_data.="&webid=$BkinternetId";
	$post_data.="&webpw=$BkinternetPw";

	//echo $post_data;
	//exit;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_URL, "https://ssl.bankda.com/partnership/user/account_fix.php"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			
	$result = curl_exec($ch);
	$result = iconv("euc-kr","utf-8",$result);
	
	if($result == "ok"){	
		$info = curl_getinfo($ch);	 
		curl_close($ch);		 
		$sql = "UPDATE bankda_member_bank SET Bkacctno='".str_replace("-","",$bkacctno)."', bank_code='$bkcode', regdate=now() WHERE member_bank_seq = '$member_bank_seq' ";
		$db->query($sql);
		if (!$db->result){
			echo "DB 수정에 실패하였습니다.";
		}else{
			echo "<script>alert('계좌 수정 되었습니다.'); location.href='bankda.php'; </script>";
		}
	}else{
		//echo "err : ".curl_errno($ch)." ".$result;
		echo "<script>alert('".$result."'); location.href='bankda.php'; </script>";
		exit;
	}
}

// 이용자 등록
if ($act=="user_add"){
	$user_id		= $_POST["user_id"];
	$user_pw		= $_POST["user_pw"];
	$user_name	= $_POST["user_name"];
	$user_tel		= $_POST["user_tel"];
	$user_email	= $_POST["user_email"];

	$post_data = "directAccess=$directAccess";
	$post_data.="&service_type=$service_type";
	$post_data.="&partner_id=$partner_id";
	$post_data.="&partner_name=".iconv("utf-8","euc-kr",$partner_name);
	$post_data.="&user_id=$user_id";
	$post_data.="&user_pw=$user_pw";
	$post_data.="&user_name=".iconv("utf-8","euc-kr",$user_name);
	$post_data.="&user_tel=$user_tel";
	$post_data.="&user_email=$user_email";
	$post_data.="&accea=1";


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	//curl_setopt($ch, CURLOPT_HEADER, "Content-type:application/x-www-form-urlencoded;charset=UTF-8"); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_URL, "https://ssl.bankda.com/partnership/user/user_join_prs.php"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	
	$result = curl_exec($ch);
	$result = iconv("euc-kr","utf-8",$result);
	
	
	if(!curl_errno($ch) && $result == "ok"){
		$info = curl_getinfo($ch);	 		
		curl_close($ch);
		$sql = "INSERT INTO bankda_member (seq, mall_ix, mall_div, mall_domain, bankda_userid, bankda_username, bankda_userpw, regdate) VALUES";
		$sql.=" ('', '".$admininfo[mall_ix]."', '".$admininfo[mall_div]."', '".$admininfo[company_name]."', '".$user_id."', '".$user_name."', '".$user_pw."', now()) ";
		$db->query($sql);
		if (!$db->result){
			echo "<script>alert('DB 등록에 실패하였습니다.');</script>";
		}else{
			echo "<script>alert('이용자 등록 되었습니다.'); location.href='bankda.php'; </script>";
		}
	}else{
		echo "<script>alert('".$result."'); location.href='bankda.php'; </script>";
		exit;
	}
}

// 이용자삭제
if ($act=="userDel"){
	$user_id		= $_POST["user_id"];
	$user_pw		= $_POST["user_pw"];

	$post_data = "directAccess=$directAccess";
	$post_data.="&service_type=$service_type";
	$post_data.="&partner_id=$partner_id";	
	$post_data.="&user_id=$user_id";
	$post_data.="&user_pw=$user_pw";
	$post_data.="&command=excute";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	//curl_setopt($ch, CURLOPT_HEADER, "Content-type:application/x-www-form-urlencoded;charset=UTF-8"); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_URL, "https://ssl.bankda.com/partnership/user/user_withdraw.php"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	 	
	$result = curl_exec($ch);
	$result = iconv("euc-kr","utf-8",$result);
 	
	if(substr_count($result,"ok")>0){
		$info = curl_getinfo($ch);	 		
		curl_close($ch);
		$sql = "DELETE FROM bankda_member WHERE bankda_userid='$user_id' AND bankda_userpw = '$user_pw' ";
		$db->query($sql);
		if (!$db->result){
			echo "<script>alert('삭제에 실패하였습니다.');</script>";
		}else{
			echo "<script>alert('이용자 삭제 되었습니다.'); location.href='bankda.php'; </script>";
		}
	}else{
		echo "<script>alert('".$result."');  location.href='bankda.php'; </script>";
		 
		exit;
	}
}

// 계좌삭제
if ($act=="bankDel"){
	$Bkacctno = $_POST["Bkacctno"];
	$testmode = $_POST["testmode"];
	if ($testmode != "Y"){
		$sql = "SELECT bankda_userid, bankda_userpw FROM bankda_member a, bankda_member_bank b WHERE a.seq = b.member_seq AND  b.Bkacctno = '$Bkacctno' ";
		$db->query($sql);
		$db->fetch();	
		$bankda_userid = $db->dt["bankda_userid"];
		$bankda_userpw = $db->dt["bankda_userpw"];
	}

	$post_data = "directAccess=$directAccess";
	$post_data.="&service_type=$service_type";
	$post_data.="&partner_id=$partner_id";
	$post_data.="&user_id=$bankda_userid";
	$post_data.="&user_pw=$bankda_userpw";
	$post_data.="&bkacctno=".str_replace("-","",$Bkacctno);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_URL, "https://ssl.bankda.com/partnership/user/renovation.php"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);	

	$result = curl_exec($ch);
	$result = iconv("euc-kr","utf-8",$result);
	
 
	if($result == "ok"){
		$info = curl_getinfo($ch);	 		
		curl_close($ch);
		$sql = "DELETE FROM bankda_member_bank WHERE Bkacctno='$Bkacctno'  ";
		$db->query($sql);
		if (!$db->result){
			echo "<script>alert('DB 등록에 실패하였습니다.');</script>";
		}else{
			echo "<script>alert('계좌삭제 되었습니다.'); location.href='bankda.php'; </script>";
		}
	}else{
		echo $result;
		exit;
	}
}

// 주문 처리상태 업데이트 : IR -> IC
if ($act=="statusUpd"){
	$no = $_POST["no"];
	$bkname = $_POST["bkname"];
	$bkinput = $_POST["bkinput"];
	$bkdate = $_POST["bkdate"];
	$bkjukyo = $_POST["bkjukyo"];

	$db_local = new Database;

	$sql = "
	SELECT o.oid AS odno, 
			 ((SELECT SUM(ptprice) AS price FROM shop_order_detail WHERE oid=o.oid)-o.use_cupon_price-use_reserve_price+delivery_price) AS price, 
			 (SELECT id FROM common_user WHERE code=o.uid) AS member_id, 
			 'NA' AS register_num, o.bname AS member_name, 
			 o.bank AS bank_name, 
			 o.bank_input_name AS payer_name,
			 substring(replace(o.date,'-',''),1,8) AS order_date
	 FROM shop_order o 
	 WHERE o.status='IR' AND o.method='0' ";
	// echo $sql;
		//exit;
	 $db_local->query($sql);
	 $incom_ready_infos = $db_local->fetchall();
	 if (count($incom_ready_infos)){
		 for ($i=0; $i<count($incom_ready_infos); $i++){
			$incom_ready_info = $incom_ready_infos[$i];
			$odno = $incom_ready_info[odno];
			$price = $incom_ready_info[price];
			$bank_name = $incom_ready_info[bank_name];
			$bank_input_name = $incom_ready_info[bank_input_name];
			$order_date = $incom_ready_info[order_date];

			$bank_name = str_replace("-","",$bank_name);
			//echo $odno." ,".$price." ,".$bank_name." ,".$bank_input_name." ,".$order_date."<BR>";
			//echo "# ".$bkinput." ,".$bkname." ,".$bkinput." ,".$bkjukyo." ,".substr(str_replace("-","",$bkdate),0,8)."<BR>";
			if ($price == $bkinput && substr_count($bank_name,$bkname)>0 ){
				//echo $odno." ,".$price." ,".$bank_name." ,".$bank_input_name." ,".substr(str_replace("-","",$bkdate),0,8)."<BR>";

				// 주문상태 변경
				$sql = "UPDATE ".TBL_SHOP_ORDER." SET status='IC', bank_input_date='".substr(str_replace("-","",$bkdate),0,8)."' WHERE oid='".$odno."' and status = 'IR' ";
				$db_local->query($sql);
				//echo $sql."<BR>";

				$sql = "UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status='IC' WHERE oid='".$odno."' and status = 'IR'  ";
				$db_local->query($sql);
				//echo $sql."<BR>";

				$sql = "INSERT INTO ".TBL_SHOP_ORDER_STATUS." (oid, pid, status, status_message, admin_message, company_id,quick,invoice_no, regdate ) ";
				$sql.=" SELECT oid, pid, 'IC', '뱅크다 입금확인', '', company_id, '', invoice_no, now() ";
				$sql.="   FROM ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$odno."' ";
				$db_local->query($sql);
				//echo $sql."<BR>";							
				//exit;

				// 뱅크다 계좌입금목록에 주문정보 업데이트
				$db = new Database;
				$db->dbcon = mysql_connect("118.217.181.188","forbiz","vhqlwm2011") or $db->error();
				mysql_select_db("mallstory_service",$db->dbcon) or $db->error();

				$sql = "UPDATE TBLBANK SET oid='".$odno."' WHERE Bkid=".$no;
				$db->query($sql);
				echo "<script>alert('일치하는 주문건이 확인되어 해당주문을 입금확인 처리하였습니다.'); location.href='bankda.php'; </script>";
				exit;
			}			
		 }

	 }else{
		 $sql = "UPDATE TBLBANK SET maching_cnt = maching_cnt + 1 WHERE Bkid=".$no;
		
		$db->query($sql);
		
		 echo "<script>alert('일치하는 주문정보가 없습니다.'); location.href='bankda.php'; </script>";
		 exit;
	 }
	 
	 echo "<script>alert('일치하는 주문정보가 없습니다.'); location.href='bankda.php'; </script>";
}


//echo $act;
if ($_GET["act"]=="CronBankdaOrderUpdate"){
	
		$db = new Database;
		$db->dbcon = mysql_connect("118.217.181.188","forbiz","vhqlwm2011") or $db->error();
		mysql_select_db("mallstory_service",$db->dbcon) or $db->error();

		$arr_gubun = array("P"=>"개인", "C"=>"법인");

		$max = 100;
		if ($page == ''){
			$start = 0;
			$page  = 1;
		}else{
			$start = ($page - 1) * $max;
		}

		// 뱅크다 가입자 목록
		$sql = "SELECT * FROM bankda_member  WHERE mall_ix = '".$admininfo[mall_ix]."' ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();
		$bankda_member_arr = $db->dt;
		$member_seq = $bankda_member_arr[seq];
		$member_cnt = $db->total;

		if ($member_seq){
			// 뱅크다 계좌 목록
			$sql = "SELECT * FROM bankda_member_bank  WHERE member_seq = ".$member_seq;	
			$db->query($sql);
			$bankda_account_arr = $db->fetchall();
			$member_account_cnt = $db->total;
			if ($member_account_cnt){
				// 뱅크다 거래 목록
				$sql = "SELECT * FROM bankda_member_bank bmb, TBLBANK bl WHERE bmb.member_seq = ".$member_seq." AND bmb.Bkacctno = bl.Bkacctno and oid is null ORDER BY bl.Bkid DESC ";
				$db->query($sql);
				$list_cnt = $db->total;

				$sql = "SELECT * FROM bankda_member_bank bmb, TBLBANK bl WHERE bmb.member_seq = ".$member_seq." AND bmb.Bkacctno = bl.Bkacctno and oid is null ORDER BY bl.Bkid DESC limit $start,$max ";
				$db->query($sql);
				$bankda_list_arr = $db->fetchall();	

				for ($i=0; $i < count($bankda_list_arr); $i++){
					$incom_bankinfo["no"] = $bankda_list_arr[$i][Bkid];
					$incom_bankinfo["bkname"] = $bankda_list_arr[$i][Bkname]." ".$bankda_list_arr[$i][Bkacctno];
					$incom_bankinfo["bkinput"] = $bankda_list_arr[$i][Bkinput];
					$incom_bankinfo["bkdate"] = $bankda_list_arr[$i][regdate];
					$incom_bankinfo["bkjukyo"] = $bankda_list_arr[$i][Bkjukyo];
					//echo $i."<br>";
					//print_r($incom_bankinfo);
					BankdaOrderUpdate($incom_bankinfo);
					//	FnOrderStatusUpd(".$bankda_list_arr[$i][Bkid].", '".$bankda_list_arr[$i][Bkname]." ".$bankda_list_arr[$i][Bkacctno]."', ".$bankda_list_arr[$i][Bkinput].", '".$bankda_list_arr[$i][regdate]."', '".$bankda_list_arr[$i][Bkjukyo]."')\" style='background:#eeeeee'>";
				}
			}
		}

}

function BankdaOrderUpdate($incom_bankinfo){
	global $admininfo;

	
	$no = $incom_bankinfo["no"];
	$bkname = $incom_bankinfo["bkname"];
	$bkinput = $incom_bankinfo["bkinput"];
	$bkdate = $incom_bankinfo["bkdate"];
	$bkjukyo = $incom_bankinfo["bkjukyo"];

	
	$db_local = new Database;

	$debug = false;

	$sql = "
	SELECT o.oid AS odno, 
			 payment_price AS price, 
			 (SELECT id FROM common_user WHERE code=o.uid) AS member_id, 
			 'NA' AS register_num, o.bname AS member_name, 
			 o.bank AS bank_name, 
			 o.bank_input_name AS payer_name,
			 substring(replace(o.date,'-',''),1,8) AS order_date
	 FROM shop_order o 
	 WHERE o.method='0' and o.bank_input_name = '".$bkjukyo."' and o.status='IR'   "; // 
	 //((SELECT SUM(ptprice) AS price FROM shop_order_detail WHERE oid=o.oid)-o.use_cupon_price-use_reserve_price+delivery_price) AS price, 
	// echo $sql;
		//exit;
	 $db_local->query($sql);
	 $incom_ready_infos = $db_local->fetchall();
	 if (count($incom_ready_infos)){
		 for ($j=0; $j<count($incom_ready_infos); $j++){
			$incom_ready_info = $incom_ready_infos[$j];
			$odno = $incom_ready_info[odno];
			$price = $incom_ready_info[price];
			$bank_name = str_replace("국민 ","국민은행 ",$incom_ready_info[bank_name]);
			$bank_input_name = $incom_ready_info[payer_name];
			$order_date = $incom_ready_info[order_date];

			$bank_name = str_replace("-","",$bank_name);
			//echo $odno." ,".$price." ,".$bank_name." ,".$bank_input_name." ,".$order_date."<BR>";
			//echo "# ".$bkinput." ,".$bkname." ,".$bkinput." ,".$bkjukyo." ,".substr(str_replace("-","",$bkdate),0,8)."<BR>";
			//echo $no.":::".$odno.":::<b ".($price == $bkinput ? "style='color:red;'":"").">".$price."==".$bkinput."</b>:::<b ".($bank_input_name == $bkjukyo ? "style='color:red;'":"").">bank_input_name:".$bank_input_name."==".$bkjukyo."</b>::bank_name:<b style='color:blue;'>".$bank_name."==".$bkname."</b>:::".substr_count($bank_name,$bkname)."<br>";
			if ($price == $bkinput && substr_count($bank_name,$bkname)>0 && $bank_input_name == $bkjukyo){
				//echo $odno." ,".$price." ,".$bank_name." ,".$bank_input_name." ,".substr(str_replace("-","",$bkdate),0,8)."<BR>";

				// 주문상태 변경
				$sql = "UPDATE ".TBL_SHOP_ORDER." SET status='IC', bank_input_date='".substr(str_replace("-","",$bkdate),0,8)."' WHERE oid='".$odno."' and status = 'IR'  ";
				if($debug){
					echo nl2br($sql)."<BR><br>";
				}else{
					$db_local->query($sql);
				}
				

				$sql = "UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status='IC' WHERE oid='".$odno."' and status = 'IR' ";
				if($debug){
					echo nl2br($sql)."<BR><br>";
				}else{
					$db_local->query($sql);
				}
				//echo nl2br($sql)."<BR><br>";

				$sql = "select * from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$odno."' and status in ('IR') ";
				//echo $sql."<br>";
				$db_local->query($sql);
				if($db_local->total){
						$sql = "INSERT INTO ".TBL_SHOP_ORDER_STATUS." (oid, pid, status, status_message, admin_message, company_id,quick,invoice_no, regdate ) ";
						$sql.=" SELECT oid, pid, 'IC', '뱅크다 입금확인', '', company_id, '', invoice_no, now() ";
						$sql.="   FROM ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$odno."' ";
						if($debug){
							echo nl2br($sql)."<BR><br>";
						}else{
							$db_local->query($sql);
						}
						//echo nl2br($sql)."<BR><br>";							
						//exit;
				

						// 뱅크다 계좌입금목록에 주문정보 업데이트
						$db = new Database;
						$db->dbcon = mysql_connect("118.217.181.188","forbiz","vhqlwm2011") or $db->error();
						mysql_select_db("mallstory_service",$db->dbcon) or $db->error();

						$sql = "UPDATE TBLBANK SET oid='".$odno."', maching_cnt = maching_cnt + 1, matching_date = NOW(), recent_editdate = NOW() WHERE Bkid='".$no."' and oid is null ";
						if($debug){
							echo nl2br($sql)."<BR><br>";
						}else{
							$db->query($sql);
						}
						//echo "입금확인완료 ".$odno." :::::".$price.":::::".$bank_name.":::::".$bank_input_name."::::".$order_date."<br><br>\n";
						//echo "<script>alert('일치하는 주문건이 확인되어 해당주문을 입금확인 처리하였습니다.'); location.href='bankda.php'; </script>";
						//exit;

						$path = $_SERVER["DOCUMENT_ROOT"]."".$admininfo[mall_data_root]."/_logs/";
						$write = date('Y-m-d H:i:s')." 입금확인완료 ".$odno." :::::".$price.":::::".$bank_name.":::::".$bank_input_name."::::".$order_date."\n\r";
						$write .= $sql."\n\r\n\r\n\r";
						echo $write."<br>";		
						if(!is_dir($path)){
							mkdir($path, 0777);
							chmod($path,0777);
						}else{
							//chmod($path,0777);
						}


						$fp = fopen($_SERVER["DOCUMENT_ROOT"].$admininfo[mall_data_root]."/_logs/bankda.txt","a+");
						fwrite($fp,$write);
						fclose($fp);
				}else{

						$db = new Database;
						$db->dbcon = mysql_connect("118.217.181.188","forbiz","vhqlwm2011") or $db->error();
						mysql_select_db("mallstory_service",$db->dbcon) or $db->error();

						$sql = "UPDATE TBLBANK SET oid='".$odno."', maching_cnt = maching_cnt + 1, matching_date = NOW(), recent_editdate = NOW() WHERE Bkid='".$no."' and oid is null ";
						if($debug){
							echo nl2br($sql)."<BR><br>";
							//$db->query($sql);
						}else{
							$db->query($sql);
						}

						$path = $_SERVER["DOCUMENT_ROOT"]."".$admininfo[mall_data_root]."/_logs/";
						$write = date('Y-m-d H:i:s')." 기 업데이트된 정보 ".$odno." :::::".$price.":::::".$bank_name.":::::".$bank_input_name."::::".$order_date."\n\r";
						$write .= $sql."\n\r\n\r\n\r";
						echo $write."<br>";
						if(!is_dir($path)){
							mkdir($path, 0777);
							chmod($path,0777);
						}else{
							//chmod($path,0777);
						}


						$fp = fopen($_SERVER["DOCUMENT_ROOT"].$admininfo[mall_data_root]."/_logs/bankda.txt","a+");
						fwrite($fp,$write);
						fclose($fp);
					//$db_local->fetch();
					//echo "status:".$db_local->dt[status]."<br>";
				}

				
			}			
		 }

	 }else{
		 $sql = "UPDATE TBLBANK SET maching_cnt = maching_cnt + 1, recent_editdate = NOW() WHERE Bkid=".$no;
		if($debug){
		//	echo nl2br($sql)."<BR><br>";
		}else{
			$db->query($sql);
		}
		 
		 //echo "<script>alert('일치하는 주문정보가 없습니다.'); location.href='bankda.php'; </script>";
		 //exit;
	 }
	 
	 //echo "<script>alert('일치하는 주문정보가 없습니다.'); location.href='bankda.php'; </script>";

}
?>