<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

if($act == "update"){
	$start_date = $FromYY."-".$FromMM."-".$FromDD;
	$end_date = $ToYY."-".$ToMM."-".$ToDD;
	if($db->dbms_type == "oracle"){
		$start_date = "to_date('$start_date','yyyy-mm-dd')";
		$end_date = "to_date('$end_date','yyyy-mm-dd')";
		$db->query("update shop_order_gift set prod_name = '$prod_name' ,start_date =$start_date, end_date = $end_date, amount= '$amount',limit_amount='$limit_amount' where uid_= '$uid'");
	}else{
		$db->query("update shop_order_gift set prod_name = '$prod_name' ,start_date ='$start_date',end_date = '$end_date',amount= '$amount',limit_amount='$limit_amount' where uid= '$uid'");
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');top.location.href='order_gift.list.php'</script>";
}

if($act == "insert"){

	$start_date = $FromYY."-".$FromMM."-".$FromDD;
	$end_date = $ToYY."-".$ToMM."-".$ToDD;

	$db->sequences = "SHOP_ORDER_GIFT_SEQ";
	if($db->dbms_type == "oracle"){
		$start_date = "to_date('$start_date','yyyy-mm-dd')";
		$end_date = "to_date('$end_date','yyyy-mm-dd')";
		$db->query("insert into shop_order_gift (uid_,start_date,end_date,amount,limit_amount,prod_name,ip,regdate) values ('',$start_date,$end_date,'$amount','$limit_amount','$prod_name','".$_SERVER["REMOTE_ADDR"]."',NOW()) ");
	}else{
		$db->query("insert into shop_order_gift (uid,start_date,end_date,amount,limit_amount,prod_name,ip,regdate) values ('','$start_date','$end_date','$amount','$limit_amount','$prod_name','".$_SERVER["REMOTE_ADDR"]."',NOW()) ");
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 등록되었습니다.');top.location.href='order_gift.list.php'</script>";
}

if($act == "delete"){

	if($db->dbms_type == "oracle"){
		$db->query("delete from shop_order_gift where uid_='$uid' ");
	}else{
		$db->query("delete from shop_order_gift where uid='$uid' ");
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');top.location.href='order_gift.list.php'</script>";
}

?>