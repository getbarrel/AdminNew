<?
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

$db = new Database;
// 조건절 셋팅

//	echo $search_searialize_value;
$unserialize_search_value = unserialize(urldecode($search_searialize_value));
//print_r ($unserialize_search_value);
//exit;
extract($unserialize_search_value); 
$where = " where di_ix != '' and di.group_ix = dig.group_ix ";
	//if(!$search_parent_group_ix){
	//	$search_parent_group_ix = $parent_group_ix;
	//}
		
	if($search_parent_group_ix != "" && $search_group_ix == ""){
		$where .= " and (dig.group_ix = '".$search_parent_group_ix."' or dig.parent_group_ix = '".$search_parent_group_ix."') ";
	}else if($search_parent_group_ix != "" && $search_group_ix != ""){
		$where .= " and dig.parent_group_ix = '".$search_parent_group_ix."' ";
	}
	
	if($search_group_ix != ""){
		$where .= " and dig.group_ix = '".$search_group_ix."' ";
	}
		
	if($mail_yn == "Y"){	
		$where .= " and mail_yn =  '1' ";
	}else if($mail_yn == "N"){	
		$where .= " and mail_yn =  '0' ";
	}
	
	if($sms_yn == "Y"){	
		$where .= " and sms_yn =  '1' ";
	}else if($sms_yn == "N"){
		$where .= " and sms_yn =  '0' ";
	}
	
	
	if($search_type != "" && $search_text != ""){	

		$where .= " and $search_type LIKE  '%$search_text%' ";

	}
	
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
		
	if($startDate != "" && $endDate != ""){	
		$where .= " and  MID(replace(di.regdate,'-',''),1,8) between  $startDate and $endDate ";
	}
	
	$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	$vendDate = $vToYY.$vToMM.$vToDD;
		
	if($vstartDate != "" && $vendDate != ""){	
		$where .= " and  MID(replace(regdate,'-',''),1,8) between  $vstartDate and $vendDate ";
	}



//************택배그룹변경 *************
if ($update_kind == "group"){
//	echo $search_searialize_value;
	if(!$update_group_ix){
		$update_group_ix = $parent_update_group_ix;
	}
	
	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($di_ix);$i++){
			$sql = "update delivery_info set group_ix = '$update_group_ix' where di_ix = '".$di_ix[$i]."' ";
			$db->query($sql);
		}
	//	echo("<script>alert('선택회원 전체의 그룹변경이 완료되었습니다.');parent.document.location.reload();</script>");
	}else{// 검색회원일때
			
			//$db->query("INSERT INTO ".TBL_MALLSTORY_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			
			
			$sql = "update delivery_info di, delivery_group dig set di.group_ix = '$update_group_ix' 
							$where  ";
			/*
			$sql = "update delivery_info set group_ix = '$update_group_ix' 
							$where  ";
							*/
			//echo $sql;
			$db->query($sql);
			
			//echo("<script>alert('검색회원 전체의 그룹정보 변경이 완료되었습니다.');parent.document.location.reload();</script>");
	}
	exit;
	//echo("<script>top.location.href = 'baymoney.pop.php?di_ix=$uid';</script>");
	
}

if ($update_kind == "status"){
//	echo $search_searialize_value;
	//print_r($_POST);
	//exit;
	//$db->bebug = true;
	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($di_ix);$i++){
			$sql = "update delivery_info set status = '".$change_status."' where di_ix = '".$di_ix[$i]."' ";
			//echo $sql;
			$db->query($sql);
		}
		echo("<script>alert('선택회원 전체의 접수상태 변경이 완료되었습니다.');parent.document.location.reload();</script>");
	}else{// 검색회원일때
			
			//$db->query("INSERT INTO ".TBL_MALLSTORY_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			
			
			$sql = "update delivery_info di, delivery_group dig set di.status = '".$change_status."' 
							$where  ";
			/*
			$sql = "update delivery_info set group_ix = '$update_group_ix' 
							$where  ";
							*/
			//echo $sql;
			$db->query($sql);
			
			echo("<script>alert('검색회원 전체의 접수상태 변경이 완료되었습니다.');parent.document.location.reload();</script>");
	}
	//echo("<script>top.location.href = 'baymoney.pop.php?di_ix=$uid';</script>");
	
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
	$s->send_time = $sDateTime;
	
			
	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($ab_ix);$i++){
				$sql = "select mobile, user_name
							from mallstory_addressbook where ab_ix ='".$ab_ix[$i]."' and sms_yn = '1' ";
			//echo $sql;				
				$db->query($sql);
				$db->fetch();
			
				$mc_sms_text = str_replace("{mem_name}",$db->dt[user_name],$mc_sms_text);
				
				//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
				$s->dest_phone = str_replace("-","",$db->dt["mobile"]);
				$s->dest_name = $db->dt["user_name"];
				$s->msg_body =$mc_sms_text;
				
				$s->sendbyone($admininfo);
			
		}
		echo("<script>alert('선택회원에게 SMS 가 정상적으로 발송되었습니다');</script>");
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
			//$db->query("INSERT INTO ".TBL_MALLSTORY_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			$sql = "select count(*) as total
							from mallstory_addressbook ab, mallstory_addressbook_group abg $where  ";
			//echo $sql;
			$db->query($sql);
			$db->fetch();
			$total = $db->dt[total];
			
			
			$sql = "select mobile, user_name
							from mallstory_addressbook ab, mallstory_addressbook_group abg $where and sms_yn = '1' ORDER BY ab.regdate DESC limit $start,$sms_max  ";
			//echo $sql;				
			$db->query($sql);
			
			
			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);
				
				$mc_sms_text = str_replace("{mem_name}",$db->dt[user_name],$mc_sms_text);
				
				//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
				$s->dest_phone = str_replace("-","",$db->dt["mobile"]);
				$s->dest_name = $db->dt["user_name"];
				$s->msg_body =$mc_sms_text;
				
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



//************E-mail 발송 *************
if ($update_kind == "sendemail"){	
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");	
	
	$cominfo = getcominfo();
	$db = new Database;

	$sql = "insert into ".TBL_MALLSTORY_TMP." (mall_ix, design_tmp) values ";
	$sql .= " ( '".$admininfo[mall_ix]."', '$mail_content') ";
	$db->query($sql);
	
	$db->query("select design_tmp as mail_content from ".TBL_MALLSTORY_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");
	$db->fetch();
	$mail_content = $db->dt[mail_content];
	$db->query("delete from ".TBL_MALLSTORY_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");
			
	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($ab_ix);$i++){
				$sql = "select mobile, user_name, email, SUBSTRING_INDEX(email,'@',-1) AS ns
							from mallstory_addressbook where ab_ix ='".$ab_ix[$i]."' and mail_yn = '1' ";
			//echo $sql;				
				$db->query($sql);
				$db->fetch();
			
				$mail_subject = str_replace("{mem_name}",$db->dt[user_name],$email_subject);
				
				if (ValidateDNS($db->dt['ns'])){				
					$mail_info[mem_name] = $db->dt[user_name];
					$mail_info[mem_mail] = $db->dt[email];
					$mail_info[mem_id] = $db->dt[id];
					$mail_info[mail_cc] = $mail_cc;
				
					
					if (SendMail($mail_info, $mail_subject,$mail_content,"")){
						$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일이 발송되었습니다.<br>";
					}else{
						$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일 발송이 실패했습니다.<br>";
					}	
				
				//echo $message;
				}else{
					echo "DNS ERROR";	
				}
			
		}
		echo("<script>alert('선택회원에게 E-mail이 정상적으로 발송되었습니다');</script>");
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
			//$db->query("INSERT INTO ".TBL_MALLSTORY_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			$sql = "select count(*) as total
							from mallstory_addressbook ab, mallstory_addressbook_group abg $where  ";
			//echo $sql;
			$db->query($sql);
			$db->fetch();
			$total = $db->dt[total];
			
			
			$sql = "select mobile, user_name, email, SUBSTRING_INDEX(email,'@',-1) AS ns
							from mallstory_addressbook ab, mallstory_addressbook_group abg $where and mail_yn = '1' ORDER BY ab.regdate DESC limit $start,$email_max  ";
			//echo $sql;				
			$db->query($sql);
			
			$send_cnt =0;
			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);
			
				$mail_subject = str_replace("{mem_name}",$db->dt[user_name],$email_subject);
				
				if (ValidateDNS($db->dt['ns'])){				
					$mail_info[mem_name] = $db->dt[user_name];
					$mail_info[mem_mail] = $db->dt[email];
					$mail_info[mem_id] = $db->dt[id];
					$mail_info[mail_cc] = $mail_cc;
				
					
					if (SendMail($mail_info, $mail_subject,$mail_content,"")){
						$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일이 발송되었습니다.<br>";
					}else{
						$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일 발송이 실패했습니다.<br>";
					}	
					$send_cnt++;
					//echo $message;
				}else{
					echo "DNS ERROR";	
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



//CallHotCon($order[uid], $$order[oid], $db->dt[pid], $hotcon_event_id, $hotcon_pcode, 1, $order[rmobile]);
function ValidateDNS($host)
{
	return (checkdnsrr($host, ANY))? true: false;
}		
	
?>
