<?
include("../../class/database.class");
include("../include/admin.util.php");


$db = new Database;


if ($act == "insert"){

	$mc_mail_text=str_replace("&quot;","\"",$mc_mail_text);
	$mc_mail_text=str_replace("&#39;","\'",$mc_mail_text);

	if($db->dbms_type == "oracle"){
		$sql = "insert into ".TBL_SHOP_MAILSEND_CONFIG."
		    (mc_ix,mc_code,mc_title,mc_mail_title,mc_mail_text,mc_sms_text,mc_mail_usersend_yn,mc_mail_adminsend_yn,mc_sms_usersend_yn,mc_sms_adminsend_yn,kakao_alim_talk_template_code,disp,regdate)
		    values
		    ('$mc_ix','$mc_code','$mc_title','$mc_mail_title',:contents,'$mc_sms_text','$mc_mail_usersend_yn','$mc_mail_adminsend_yn','$mc_sms_usersend_yn','$mc_sms_adminsend_yn','$kakao_alim_talk_template_code','$disp',NOW())";
	//echo $sql;
		$db->too_big_data["key"] = ":contents";
		$db->too_big_data["val"] = $mc_mail_text;
	}else{
		$sql = "insert into ".TBL_SHOP_MAILSEND_CONFIG."
		    (mc_ix,mc_code,mc_title,mc_mail_title,mc_mail_text,mc_sms_text,mc_mail_usersend_yn,mc_mail_adminsend_yn,mc_sms_usersend_yn,mc_sms_adminsend_yn,kakao_alim_talk_template_code,disp,regdate, kakao_alim_talk_btn_code)
		    values
		    ('$mc_ix','$mc_code','$mc_title','$mc_mail_title','$mc_mail_text','$mc_sms_text','$mc_mail_usersend_yn','$mc_mail_adminsend_yn','$mc_sms_usersend_yn','$mc_sms_adminsend_yn','$kakao_alim_talk_template_code','$disp',NOW(), '$kakao_alim_talk_btn_code')";
	}

	$db->query($sql);
	$db->query("SELECT mc_ix, mc_code, mc_mail_text FROM ".TBL_SHOP_MAILSEND_CONFIG." WHERE mc_ix=LAST_INSERT_ID()");
	$db->fetch();

//	echo $content;

 	if (!is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/_message")) {
        mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/_message", 0777);
        chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/_message", 0777);
    }

	$fp = fopen($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/_message/ms_email_".$db->dt[mc_code].".htm","w");
	fwrite($fp,$db->dt[mc_mail_text]);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 추가되었습니다..');</script>");
	//echo("<script>location.href = 'mail.manage2.php?mc_ix=".$db->dt[mc_ix]."';</script>");
	echo("<script>location.href = 'mail.manage.php?mc_ix=".$db->dt[mc_ix]."';</script>");
}


if ($act == "update"){

	$mc_mail_text=str_replace("&quot;","\"",$mc_mail_text);
	$mc_mail_text=str_replace("&#39;","\'",$mc_mail_text);

	if($db->dbms_type == "oracle"){
		$sql = "update ".TBL_SHOP_MAILSEND_CONFIG." set
		    mc_code='$mc_code',mc_title='$mc_title',mc_mail_title='$mc_mail_title',mc_mail_text=:contents ,mc_sms_text='$mc_sms_text',
			mc_mail_usersend_yn='$mc_mail_usersend_yn',
			mc_mail_adminsend_yn='$mc_mail_adminsend_yn',
			mc_sms_usersend_yn='$mc_sms_usersend_yn',
			mc_sms_adminsend_yn='$mc_sms_adminsend_yn',
			kakao_alim_talk_template_code='$kakao_alim_talk_template_code',
			disp='$disp',regdate=NOW()
		    where mc_ix='$mc_ix' ";

		$db->too_big_data["key"] = ":contents";
		$db->too_big_data["val"] = $mc_mail_text;
	}else{
		$sql = "update ".TBL_SHOP_MAILSEND_CONFIG." set
		    mc_code='$mc_code',mc_title='$mc_title',mc_mail_title='$mc_mail_title',mc_mail_text='$mc_mail_text',mc_sms_text='$mc_sms_text',
			mc_mail_usersend_yn='$mc_mail_usersend_yn',
			mc_mail_adminsend_yn='$mc_mail_adminsend_yn',
			mc_sms_usersend_yn='$mc_sms_usersend_yn',
			mc_sms_adminsend_yn='$mc_sms_adminsend_yn',
			kakao_alim_talk_template_code='$kakao_alim_talk_template_code',
			kakao_alim_talk_btn_code='$kakao_alim_talk_btn_code',
			disp='$disp',regdate=NOW()
		    where mc_ix='$mc_ix' ";
	}


	//echo $sql;
	$db->query($sql);
	$db->query("SELECT mc_ix, mc_code, mc_mail_text FROM ".TBL_SHOP_MAILSEND_CONFIG."  where mc_ix='$mc_ix'  ");
	$db->fetch();


    if (!is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/_message")) {
        mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/_message", 0777);
        chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/_message", 0777);
    }

    $fp = fopen($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/_message/ms_email_".$db->dt[mc_code].".htm","w");
    fwrite($fp,$db->dt[mc_mail_text]);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'mail.manage2.php?mc_ix=$mc_ix';</script>");
	echo("<script>location.href = 'mail.manage.php?mc_ix=$mc_ix';</script>");
}

if ($act == "delete"){
	$db->query("delete from ".TBL_SHOP_MAILSEND_CONFIG." where mc_ix = '$mc_ix' ");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');</script>");
	echo("<script>location.href = '/admin/member/member.php';</script>");
}


?>
