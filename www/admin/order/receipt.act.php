<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

/*
$db->query("delete from receipt where order_no = '".$oid."' ");

echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('삭제되었습니다.');parent.document.location.reload();</script>";
*/


if($act=="mail_send"){
	include_once($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
	
	$mail_contents = str_replace("\\'","\"",$mail_contents);
	//echo $mail_contents;
	//exit;
	$mail_info[mem_name] = $user_name;
	$mail_info[mem_mail] = $user_mail;

	SendMail($mail_info, "주문번호 ".$oid." 의 ".($view_type=="transaction" ? "거래명세서" : "일반영수증")." 내역입니다.",$mail_contents,"");

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 메일전송되었습니다.');</script>";
}

?>