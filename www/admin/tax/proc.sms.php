<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");

session_start();

$db = new Database;

	$message = $_POST[to_message];
	$snd_no = $_POST[snd_no];
	$rcv_no = $_POST[rcv_no];

	//echo $message."|".$snd_no."|".$rcv_no;

	$cominfo = getcominfo();
	$sdb = new Database;
	$s = new SMS();
	$s->send_phone = str_replace("-","",$snd_no);;
	$s->send_name = $cominfo[com_name];
	

	$s->dest_phone = str_replace("-","",$rcv_no);
	$s->dest_name = "$name";
	$s->msg_body =	$message;
		
	$s->sendbyone($admininfo);
		
	echo("<script language='javascript'>alert('정상적으로SMS가 발송되었습니다.');</script>");
?>