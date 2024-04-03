<?
include("../../class/database.class");
include("../include/admin.util.php");


$db = new Database;

if ($act == "insert"){

	$mc_mail_text = $content;


	$sql = "insert into ".TBL_SHOP_MAILSEND_CONFIG."
		    (mc_ix,mc_code,mc_title,mc_mail_title,mc_mail_text,mc_sms_text,mc_mail_usersend_yn,mc_mail_adminsend_yn,disp,regdate)
		    values
		    ('$mc_ix','$mc_code','$mc_title','$mc_mail_title','$mc_mail_text','$mc_sms_text','$mc_mail_usersend_yn','$mc_mail_adminsend_yn','$disp',NOW())";
	//echo $sql;


	$db->query($sql);
	$db->query("SELECT mc_ix, mc_code, mc_mail_text FROM ".TBL_SHOP_MAILSEND_CONFIG." WHERE mc_ix=LAST_INSERT_ID()");
	$db->fetch();

//	echo $content;

	$fp = fopen($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/mail/ms_mail_".$db->dt[mc_code].".htm","w");
	fwrite($fp,$db->dt[mc_mail_text]);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 추가되었습니다..');</script>");
	echo("<script>location.href = 'display.php?';</script>");
}


if ($act == "update"){

	$mc_mail_text = $content;


	$sql = "update ".TBL_SHOP_MAILSEND_CONFIG." set
		    mc_code='$mc_code',mc_title='$mc_title',mc_mail_title='$mc_mail_title',mc_mail_text='$mc_mail_text',mc_sms_text='$mc_sms_text',
			mc_mail_usersend_yn='$mc_mail_usersend_yn',
			mc_mail_adminsend_yn='$mc_mail_adminsend_yn',disp='$disp',regdate=NOW()
		    where mc_ix='$mc_ix' ";



	//echo $sql;
	$db->query($sql);
	$db->query("SELECT mc_ix, mc_code, mc_mail_text FROM ".TBL_SHOP_MAILSEND_CONFIG."  where mc_ix='$mc_ix'  ");
	$db->fetch();


	$fp = fopen($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/mail/ms_mail_".$mc_code.".htm","w");
	fwrite($fp,$db->dt[mc_mail_text]);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'dispaly.php';</script>");
}

if ($act == "delete"){
	$db->query("delete from ".TBL_SHOP_MAILSEND_CONFIG." where mc_ix = '$mc_ix' ");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');</script>");
	echo("<script>location.href = '/admin/member/member.php';</script>");
}


?>
