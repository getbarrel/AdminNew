<?
include("../../class/database.class");

session_start();

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
	
	$target_mails = split("[,]",$target_mails,3);
	
	if(is_array($target_mails)){
		for($i=0;$i<count($target_mails);$i++){
			//echo $mails[$i];
			
			
			$mail_info[mem_mail] = $target_mails[$i];
			
		
			SendMail($mail_info, $mail_subject,$content,"");
			
		}
	}else{
		$mail_info[mem_mail] = $target_mails;
			
		
		SendMail($mail_info, $mail_subject,$content,"");
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('정상적으로 메일이 발송되었습니다.');</script>");
	echo("<script language='javascript'>self.close();</script>");
	
}

?>