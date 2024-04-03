<?
include("./class/layout.class");


$db = new Database;

if ($act == "send_mail"){




	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");	
	
	//echo (count($mails));
	$sql = "insert into ".TBL_SHOP_TMP." (mall_ix, design_tmp) values ";
	$sql .= " ( '".$admininfo[mall_ix]."', '$content') ";
	$db->query($sql);
	
	$db->query("select design_tmp as content from ".TBL_SHOP_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");
	$db->fetch();
	$content = $db->dt[content];
	$db->query("delete from ".TBL_SHOP_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");
	$normal_cnt=0;
    $abnormal_cnt=0;
	for($i=0;$i<count($mails);$i++){
		//echo $mails[$i];
		list($name, $mem_id, $mail) = split("[|]",$mails[$i],3);

		$mail_info[mem_name] = $name;
		$mail_info[mem_mail] = $mail;
		$mail_info[mem_id] = $id;

		$mail_subject = $mail_info[mem_name]." 님, ".$subject;

			/*	원래로직
			if(SendMail($mail_info, $mail_subject, $content,"","","Y")){//SendMail 함수에 전달인자 값이 추가되었기에 여기서도 추가해야 사용자한테 발송됨 kbk 13/09/17
				$normal_cnt++;
			}else{
				$abnormal_cnt++;
			}
			*/
		


		//	직원 및 회원이 아닌 사람에게 메일 발송 금지 (id와 이메일 주소로 검색해서 내용이 없으면 안보낸다
			if(count($mails) > "0") {

				if($db->dbms_type == "oracle"){
					$db->query("SELECT CU.id, CUD.mail, CUD.pcs, CUD.info,	 CUD.sms FROM ".TBL_COMMON_USER." AS CU LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." AS CUD ON CU.code = CUD.code WHERE CU.id = '".trim($mem_id)."' AND AES_DECRYPT(CUD.mail,'".$db->ase_encrypt_key."') = '".addslashes(trim($mail))."'" );
				}else{
					$db->query("SELECT CU.id, CUD.mail, CUD.pcs, CUD.info,	 CUD.sms FROM ".TBL_COMMON_USER." AS CU LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." AS CUD ON CU.code = CUD.code WHERE CU.id = '".trim($mem_id)."' AND AES_DECRYPT(UNHEX(CUD.mail),'".$db->ase_encrypt_key."') = '".addslashes(trim($mail))."'" );
				}

				$db->fetch();
				$wel_memberChk = $db->dt;



				if($wel_memberChk[id] == $mem_id) {
					if(SendMail($mail_info, $mail_subject, $content,"","","Y")){//SendMail 함수에 전달인자 값이 추가되었기에 여기서도 추가해야 사용자한테 발송됨 kbk 13/09/17
						$normal_cnt++;
					}else{
						$abnormal_cnt++;
					}
				} else {
					$abnormal_cnt++;
				}
			}
		//	//직원 및 회원이 아닌 사람에게 메일 발송 금지


	}

	echo("<script language='javascript'>alert('정상적으로 메일이 발송되었습니다. 정상 : ".$normal_cnt." , 비정상 : ".$abnormal_cnt."');</script>");
	echo("<script language='javascript'>self.close();</script>");
	
}

?>