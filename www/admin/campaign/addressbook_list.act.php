<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");


$db = new Database;
// 조건절 셋팅

	$unserialize_search_value = unserialize(urldecode($search_searialize_value));
	extract($unserialize_search_value);
	$where = " where ab_ix != '' and ab.group_ix = abg.group_ix and ab.company_id = '".$admininfo[company_id]."' ";
	//if(!$search_parent_group_ix){
	//	$search_parent_group_ix = $parent_group_ix;
	//}

	if($search_parent_group_ix != "" && $search_group_ix == ""){
		$where .= " and (abg.group_ix = '".$search_parent_group_ix."' or abg.parent_group_ix = '".$search_parent_group_ix."') ";
	}else if($search_parent_group_ix != "" && $search_group_ix != ""){
		$where .= " and abg.parent_group_ix = '".$search_parent_group_ix."' ";
	}

	if($search_group_ix != ""){
		$where .= " and abg.group_ix = '".$search_group_ix."' ";
	}

	if($mail_yn == "1"){
		$where .= " and mail_yn =  '1' ";
	}else if($mail_yn == "0"){
		$where .= " and mail_yn =  '0' ";
	}

	if($sms_yn == "1"){
		$where .= " and sms_yn =  '1' ";
	}else if($sms_yn == "0"){
		$where .= " and sms_yn =  '0' ";
	}


	if($search_type != "" && $search_text != ""){

		$where .= " and $search_type LIKE  '%$search_text%' ";

	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  MID(replace(ab.regdate,'-',''),1,8) between  $startDate and $endDate ";
	}

	$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	$vendDate = $vToYY.$vToMM.$vToDD;

	if($vstartDate != "" && $vendDate != ""){
		$where .= " and  MID(replace(regdate,'-',''),1,8) between  $vstartDate and $vendDate ";
	}



//************회원그룹변경 *************
if ($update_kind == "group"){
//	echo $search_searialize_value;
	if(!$update_group_ix){
		$update_group_ix = $parent_update_group_ix;
	}

	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($ab_ix);$i++){
			$sql = "update shop_addressbook set group_ix = '$update_group_ix' where ab_ix = '".$ab_ix[$i]."' ";
			$db->query($sql);
		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체의 그룹변경이 완료되었습니다.');parent.document.location.reload();</script>");
	}else{// 검색회원일때

			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");


			$sql = "update shop_addressbook ab, shop_addressbook_group abg set ab.group_ix = '$update_group_ix'
							$where  ";
			/*
			$sql = "update shop_addressbook set group_ix = '$update_group_ix'
							$where  ";
							*/
			//echo $sql;
			$db->query($sql);

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색회원 전체의 그룹정보 변경이 완료되었습니다.');parent.document.location.reload();</script>");
	}
	//echo("<script>top.location.href = 'baymoney.pop.php?ab_ix=$uid';</script>");

}
//************회원 메일,SMS수신변경***********
if ($update_kind == "receive"){


//	echo $search_searialize_value;
	if(!$update_group_ix){
		$update_group_ix = $parent_update_group_ix;
	}

	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($ab_ix);$i++){
			$sql = "update shop_addressbook set mail_yn = '$mail_yn', sms_yn = '$sms_yn' where ab_ix = '".$ab_ix[$i]."' ";
			$db->query($sql);

		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체의 수신정보가 변경되었습니다.');parent.document.location.reload();</script>");
	}else{// 검색회원일때

			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");


			$sql = "update shop_addressbook set mail_yn = '$mail_yn', sms_yn = '$sms_yn'
							$where  ";
			/*
			$sql = "update shop_addressbook set group_ix = '$update_group_ix'
							$where  ";
							*/
			//echo $sql;
			$db->query($sql);

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색회원 전체의 수신정보가 변경되었습니다.');parent.document.location.reload();</script>");
	}
	//echo("<script>top.location.href = 'baymoney.pop.php?ab_ix=$uid';</script>");

}
//************비회원 일괄삭제***********
if ($update_kind == "nojoin"){


//	echo $search_searialize_value;
	if(!$update_group_ix){
		$update_group_ix = $parent_update_group_ix;
	}

	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($ab_ix);$i++){
			$sql = "delete FROM shop_addressbook where mbjoin = '0'";
			$db->query($sql);

		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('비회원 일괄 삭제되었습니다.');parent.document.location.reload();</script>");
	}else{// 검색회원일때

			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");


			$sql = "update shop_addressbook set mail_yn = '$mail_yn', sms_yn = '$sms_yn'
							$where  ";
			/*
			$sql = "update shop_addressbook set group_ix = '$update_group_ix'
							$where  ";
							*/
			//echo $sql;
			$db->query($sql);

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색회원 전체의 그룹정보 변경이 완료되었습니다.');parent.document.location.reload();</script>");
	}
	//echo("<script>top.location.href = 'baymoney.pop.php?ab_ix=$uid';</script>");

}
//************SMS 발송 *************
if ($update_kind == "sms"){

	$cominfo = getcominfo();
	$sdb = new Database;
	$s = new SMS();
	$s->send_phone = $send_phone;
	$s->send_name = $cominfo[com_name];
	$s->admin_mode = true;
	$s->send_type = $send_type;	

	$s->send_date = substr($sFromYY,2,2).$sFromMM.$sFromDD;
	$s->send_title	=	$lms_title;

	//echo $send_type."<br />".substr($sFromYY,2,2).$sFromMM.$sFromDD."<br />".$sDateTime;
	//exit;

	
	//예약발송 시간
	if($send_time_type	==	1){
		$send_time	=	$send_time_sms." ".$send_time_hour.":".$send_time_minite.":00";
		$s->send_time = $send_time;
	}

	if($mms_file){
//		echo 1;
		$t_dir = $_SERVER["DOCUMENT_ROOT"]."/admin/member/mms/upload"; // 업로드된 파일을 저장할 디렉토리
		$url_dir = "/admin/member/mms/upload"; // 업로드된 파일을 저장할 디렉토리
		$s_sm = $mms_file_name; // 실제 파일명
		
		$ret = move_uploaded_file($mms_file, "$t_dir/$s_sm"); // 파일 지정한 업로드폴더로 이동

		if($ret) {
			$file_source = $url_dir."/".$s_sm;
			$file_loc = array();
			$file_loc[] = "http://".$_SERVER['HTTP_HOST'].$file_source;
		} else {
			
		}
		$s->mms_file = $file_loc;
	}
		
	if($update_type == 2){// 선택회원일때

		for($i=1; $i <count($ab_ix);$i++){
				$sql = "select mobile, user_name
							from shop_addressbook where ab_ix ='".$ab_ix[$i]."' and sms_yn = '1' ";
			
				$db->query($sql);
				$db->fetch();

				$mc_sms_text = str_replace("{mem_name}",$db->dt[user_name],$sms_text);

				//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
				$s->dest_phone		=	str_replace("-","",$db->dt["mobile"]);
				$s->dest_name		=	$db->dt["user_name"];
				$s->msg_body		=	$mc_sms_text;
				$s->sms_send_type	=	$sms_send_type;
				$s->send_name		=	$_SESSION['admininfo']['charger'];
				$s->send_title	=	"";
				$s->send_type = "1";
				$s->sendbyone($admininfo);
					

		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원에게 SMS 가 정상적으로 발송되었습니다');</script>");
	}else{// 검색회원일때
			if(!$sms_max){
				$sms_max = 100;
			}
			if ($sms_send_page == ''){
				$start = 0;
				$sms_send_page  = 1;
			}else{
				$start = ($sms_send_page - 1) * $sms_max;
			}
			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			$sql = "select count(*) as total
							from shop_addressbook ab, shop_addressbook_group abg $where  ";
			//echo $sql;
			$db->query($sql);
			$db->fetch();
			$total = $db->dt[total];


			$sql = "select mobile, user_name
							from shop_addressbook ab, shop_addressbook_group abg $where and sms_yn = '1' ORDER BY ab.regdate DESC limit $start,$sms_max  ";
			//echo $sql;
			$db->query($sql);


			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);

				$mc_sms_text = str_replace("{mem_name}",$db->dt[user_name],$sms_text);

				//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
				$s->dest_phone = str_replace("-","",$db->dt["mobile"]);
				$s->dest_name = $db->dt["user_name"];
				$s->msg_body =$mc_sms_text;
				$s->sms_send_type	=	$sms_send_type;
				$s->send_name		=	$_SESSION['admininfo']['charger'];
				$s->sendbyone($admininfo);

			}

			if($total > ($start+$sms_max)){
				echo("<script>
				parent.document.getElementById('sended_sms_cnt').innerHTML = '".($start+$sms_max)."';
				parent.document.getElementById('remainder_sms_cnt').innerHTML = '".($total-($start+$sms_max))."';
				if(!parent.document.forms['list_frm'].stop.checked){
					parent.document.forms['list_frm'].sms_send_page.value = ".($sms_send_page+1).";
					parent.document.forms['list_frm'].submit();
				}
				</script>");
			}else{
				echo("<script>
				parent.document.getElementById('sended_sms_cnt').innerHTML = '".($total)."';
				parent.document.getElementById('remainder_sms_cnt').innerHTML = '0';
				alert('".$total." 건의 SMS 가 정상적으로 발송되었습니다 ');
				</script>");
			}
	}
	//echo("<script>top.location.href = 'baymoney.pop.php?ab_ix=$uid';</script>");

}

//************비회원 SMS 발송 *************
if ($update_kind == "member"){
	
	//선택된 비회원
	$ab_ix	=	array();
	//추가된 전화번호
	$nmb_ix	=	array();
	foreach($mem_ix as $val){
	  if(!stristr($val,'-')){
	   $ab_ix[]	=	$val;
	  }else{
		$nmb_ix[]	=	$val;
	  }
	}
	
	$cominfo = getcominfo();
	$sdb = new Database;
	$s = new SMS();
	$s->send_phone = $send_phone;
	$s->send_name = $cominfo[com_name];
	$s->admin_mode = true;
	$s->send_type = $send_type;	
	
	//echo $send_type."<br />".substr($sFromYY,2,2).$sFromMM.$sFromDD."<br />".$sDateTime;
	//exit;
	
	//예약발송 시간
	if($nmb_send_time_type	==	1){
		$send_time	=	$send_time_nmb." ".$send_time_hour_nmb.":".$send_time_minite_nmb.":00";
		$s->send_time = $send_time;
	}

	if(count($ab_ix) != 0){// 선택회원일때

		for($i=0; $i <count($ab_ix);$i++){
				$sql = "select mobile, user_name
							from shop_addressbook where ab_ix ='".$ab_ix[$i]."' and sms_yn = '1' ";
			//echo $sql;
				$db->query($sql);
				$db->fetch();

				$mc_sms_text = str_replace("{mem_name}",$db->dt[user_name],$nmb_text);

				//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
				$s->dest_phone = str_replace("-","",$db->dt["mobile"]);
				$s->dest_name = $db->dt["user_name"];
				$s->msg_body =$mc_sms_text;
				$s->sms_send_type	=	$sms_send_type;
				$s->send_name		=	$_SESSION['admininfo']['charger'];
				$s->sendbyone($admininfo);
			
		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원에게 SMS 가 정상적으로 발송되었습니다');</script>");
	}else if(count($nmb_ix) != 0){
		
		for($i=0; $i <count($nmb_ix);$i++){
					
				$mc_sms_text = str_replace("{mem_name}",'',$nmb_text);

				//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
				$s->dest_phone = str_replace("-","",$nmb_ix[$i]);
				$s->dest_name = '';
				$s->msg_body =$mc_sms_text;
				$s->sms_send_type	=	$sms_send_type;
				$s->send_name		=	$_SESSION['admininfo']['charger'];
				$s->sendbyone($admininfo);

			

		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원에게 SMS 가 정상적으로 발송되었습니다');</script>");
	
	}
}



//************E-mail 발송 *************
if ($update_kind == "sendemail"){
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

	$cominfo = getcominfo();
	$db = new Database;
	$idb = new Database;

	$sql = "insert into ".TBL_SHOP_TMP." (mall_ix, design_tmp) values ";
	$sql .= " ( '".$admininfo[mall_ix]."', '$mail_content') ";
	$db->query($sql);

	$db->query("select design_tmp as mail_content from ".TBL_SHOP_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");
	$db->fetch();
	$mail_content = $db->dt[mail_content];
	$db->query("delete from ".TBL_SHOP_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");

	if($save_mail){
		$mail_info[mail_content] = $mail_content;
		$mail_info[mail_subject] = $email_subject;
		$mail_info[mail_ix] = $_POST[mail_ix];

		$mail_ix = mail_box("insert", $mail_info);
	}else{
		$mail_ix = $_POST[mail_ix];
	}

	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($ab_ix);$i++){
				$sql = "select ab_ix, mobile, user_name, email, SUBSTRING_INDEX(email,'@',-1) AS ns
							from shop_addressbook where ab_ix ='".$ab_ix[$i]."' and mail_yn = '1' ";
				//echo nl2br($sql);
				//exit;
				$db->query($sql);
				$db->fetch();

				$mail_subject = str_replace("{mem_name}",$db->dt[user_name],$email_subject);

				if (ValidateDNS($db->dt['ns'])){
					$mail_info[mem_name] = $db->dt[user_name];
					$mail_info[mem_mail] = $db->dt[email];
					$mail_info[mem_id] = $db->dt[id];
					$mail_info[mail_cc] = $mail_cc;

					$check_key = md5(uniqid());
					$mail_content = str_replace("{check_key}",$check_key,$mail_content);
					$__mail_content = $mail_content."<P style='DISPLAY: none'><IMG src='http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key'></P>";

					if (SendMail($mail_info, $mail_subject,$__mail_content,"")){
						//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일이 발송되었습니다.<br>";
						
						$sql = "insert into shop_mailling_history(mh_ix,mail_ix,ab_ix,sended_mail, check_key, regdate) values ('','".$mail_ix."','".$db->dt[ab_ix]."','".$db->dt[email]."','$check_key', NOW())";
						
						$idb->query($sql);
						//echo $sql;
						//exit;
		
					}else{
						//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일 발송이 실패했습니다.<br>";
						$sql = "insert into shop_mailling_history(mh_ix,mail_ix,ab_ix,sended_mail, check_key, is_error, error_text, regdate) values ('','".$mail_ix."','".$db->dt[ab_ix]."','".$db->dt[email]."','$check_key','1', 'SEND_ERROR', NOW())";
						//echo $sql;
						$idb->query($sql);
					}

				//echo $message;
				}else{
					//echo "DNS ERROR";
					$sql = "insert into shop_mailling_history(mh_ix,mail_ix,ab_ix,sended_mail, check_key, is_error, error_text, regdate) values ('','".$mail_ix."','".$db->dt[ab_ix]."','".$db->dt[email]."','$check_key','1', 'DNS_ERROR', NOW())";
				}

		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원에게 E-mail이 정상적으로 발송되었습니다');</script>");
	}else{// 검색회원일때
			if(!$email_max){
				$email_max = 100;
			}
			if ($email_send_page == ''){
				$start = 0;
				$email_send_page  = 1;
			}else{
				$start = ($email_send_page - 1) * $email_max;
			}
			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			$sql = "select count(*) as total
							from shop_addressbook ab, shop_addressbook_group abg $where  ";
			//echo nl2br($sql);
			//exit;
			$db->query($sql);
			$db->fetch();
			$total = $db->dt[total];


			$sql = "select ab.ab_ix, mobile, user_name, email, SUBSTRING_INDEX(email,'@',-1) AS ns
							from shop_addressbook ab, shop_addressbook_group abg $where and mail_yn = '1' ORDER BY ab.regdate DESC limit $start,$email_max  ";
			//echo nl2br($sql);
			//exit;
			$db->query($sql);
			$maillings = $db->fetchall2("object");
			$send_cnt =0;
			for($i=0;$i < count($maillings);$i++){
				

				$mail_subject = str_replace("{mem_name}",$maillings[$i][user_name],$email_subject);

				if (ValidateDNS($maillings[$i]['ns'])){
					$mail_info[mem_name] = $maillings[$i][user_name];
					$mail_info[mem_mail] = $maillings[$i][email];
					$mail_info[mem_id] = $maillings[$i][id];
					$mail_info[mail_cc] = $mail_cc;

					$check_key = md5(uniqid());
					$mail_content = str_replace("{check_key}",$check_key,$mail_content);
					$__mail_content = $mail_content."<P style='DISPLAY: none'><IMG src='http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key'></P>";

					if (SendMail($mail_info, $mail_subject,$__mail_content,"")){
						//$message = "<b>".$mail_info[mem_name]." :::".$mail_info[mem_mail]."</b> 님의 이메일이 발송되었습니다.<br>";
						
						
						$sql = "insert into shop_mailling_history(mh_ix,mail_ix,ab_ix,sended_mail, check_key, regdate) values ('','".$mail_ix."','".$maillings[$i][ab_ix]."','".$maillings[$i][email]."','$check_key', NOW())";
						//echo $sql;
						$db->query($sql);

					}else{
						//$message = "<b>".$mail_info[mem_name]." :::".$mail_info[mem_mail]."</b> 님의 이메일 발송이 실패했습니다.<br>";
							$sql = "insert into shop_mailling_history (mh_ix,mail_ix,ab_ix,sended_mail, check_key, is_error, error_text, regdate) values ('','".$mail_ix."','".$maillings[$i][ab_ix]."','".$maillings[$i][email]."','$check_key', '1','SEND_ERROR', NOW())";
							//echo $sql;
							$db->query($sql);
					}
					$send_cnt++;
					//echo $message;
				}else{
						$sql = "insert into shop_mailling_history(mh_ix,mail_ix,ab_ix,sended_mail, check_key, regdate, is_error, error_text) values ('','".$mail_ix."','".$maillings[$i][ab_ix]."','".$maillings[$i][email]."','$check_key', NOW(),'1', 'DNS ERROR')";
						//echo $sql;
						$db->query($sql);
						//echo "DNS ERROR";
				}

			}

			if($total > ($start+$email_max)){
				echo("<script>
				parent.document.getElementById('sended_email_cnt').innerHTML = '".($start+$email_max)."';
				parent.document.getElementById('remainder_email_cnt').innerHTML = '".($total-($start+$email_max))."';
				if(!parent.document.forms['list_frm'].stop.checked){
					parent.document.forms['list_frm'].email_send_page.value = ".($email_send_page+1).";
					parent.document.forms['list_frm'].submit();
				}
				</script>");
			}else{
				if($send_cnt){
					echo("<script>
					parent.document.getElementById('sended_email_cnt').innerHTML = '".($send_cnt)."';
					parent.document.getElementById('remainder_email_cnt').innerHTML = '0';
					alert('".$send_cnt." 건의 이메일이 정상적으로 발송되었습니다 ');
					</script>");
				}else{
					echo("<script>
					parent.document.getElementById('sended_email_cnt').innerHTML = '".($send_cnt)."';
					parent.document.getElementById('remainder_email_cnt').innerHTML = '0';
					alert('발송대상이 존재하지 않습니다. 메일링 수신거부 회원은 메일링 대상이 아닙니다. ');
					</script>");
				}
			}
	}
	//echo("<script>top.location.href = 'baymoney.pop.php?ab_ix=$uid';</script>");

}



//************하트콘 발송 *************
if ($update_kind == "hotcon"){
//	echo $search_searialize_value;
	if(!$update_group_ix){
		$update_group_ix = $parent_update_group_ix;
	}

	$oid  = md5(uniqid(rand()));

	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($ab_ix);$i++){
				$sql = "select mobile, user_name
							from shop_addressbook where ab_ix ='".$ab_ix[$i]."' and sms_yn = '1' ";
			//echo $sql;
				$db->query($sql);
				$db->fetch();

				//echo $db->dt["mobile"];
				$message = CallHotCon($db->dt["user_name"], $oid, $hotcon_pcode, $hotcon_event_id, $hotcon_pcode, 1, str_replace("-","",$db->dt["mobile"]));
				//echo iconv("CP949","utf-8",$message);


		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원에게 하트콘이 정상적으로 발송되었습니다');</script>");
	}else{// 검색회원일때
			if(!$hotcon_max){
				$hotcon_max = 100;
			}
			if ($hotcon_send_page == ''){
				$start = 0;
				$hotcon_send_page  = 1;
			}else{
				$start = ($hotcon_send_page - 1) * $hotcon_max;
			}
			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			$sql = "select count(*) as total
							from shop_addressbook ab, shop_addressbook_group abg $where  ";
			//echo $sql;
			$db->query($sql);
			$db->fetch();
			$total = $db->dt[total];


			$sql = "select mobile, user_name
							from shop_addressbook ab, shop_addressbook_group abg $where and sms_yn = '1' ORDER BY ab.regdate DESC limit $start,$hotcon_max  ";
			//echo $sql;
			$db->query($sql);


			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);

				CallHotCon($db->dt["user_name"], $oid, $hotcon_pcode, $hotcon_event_id, $hotcon_pcode, 1, str_replace("-","",$db->dt["mobile"]));

			}

			if($total > ($start+$hotcon_max)){
				echo("<script>
				parent.document.getElementById('sended_hotcon_cnt').innerHTML = '".($start+$hotcon_max)."';
				parent.document.getElementById('remainder_hotcon_cnt').innerHTML = '".($total-($start+$hotcon_max))."';
				if(!parent.document.forms['list_frm'].stop.checked){
					parent.document.forms['list_frm'].hotcon_send_page.value = ".($hotcon_send_page+1).";
					parent.document.forms['list_frm'].submit();
				}
				</script>");
			}else{
				echo("<script>
				parent.document.getElementById('sended_hotcon_cnt').innerHTML = '".($total)."';
				parent.document.getElementById('remainder_hotcon_cnt').innerHTML = '0';
				alert('".$total." 건의 하트콘이 정상적으로 발송되었습니다 ');
				</script>");
			}
	}

}


//CallHotCon($order[uid], $$order[oid], $db->dt[pid], $hotcon_event_id, $hotcon_pcode, 1, $order[rmobile]);
function ValidateDNS($host)
{
	return (checkdnsrr($host, ANY))? true: false;
}

?>
