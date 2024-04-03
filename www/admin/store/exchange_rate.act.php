<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{

	$sql = "update common_exchange_rate set is_use='0' 		";
	$db->query($sql);
	/*
	$sql = "insert into common_exchange_rate set
				er_ix='".$er_ix."',
				usd='".$usd."',
				jpy='".$jpy."',
				cny='".$cny."',
				eur='".$eur."',
				is_use='1',
				regdate=NOW()
				";
	*/
	$sql = "	insert into common_exchange_rate (er_ix,usd,jpy,cny,eur,is_use,regdate) values ('$er_ix','$usd','$jpy','$cny','$eur','1',NOW())";
	$db->sequences = "COMMON_EXCHANGE_RATE_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('환율정보가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	$sql = "update common_exchange_rate set
				usd='".$usd."',
				jpy='".$jpy."',
				cny='".$cny."',
				eur='".$eur."',
				is_use='".$is_use."'
				where er_ix='".$er_ix."'
				";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('환율정보가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){

	$sql = "delete from common_exchange_rate where er_ix='$er_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('환율정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

?>
