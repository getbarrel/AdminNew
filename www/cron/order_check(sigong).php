#!/usr/local/bin/php
<?
include("/home/sigongweb/public_html/class/mysql_shell.class");

$db = new Database;
$mdb = new Database;
$idb = new Database;
$today = date('Y-m-d H:i:s');
//배송중 상태로 4일경과시 배송완료로 상태값 변경 프로세서 #!/usr/local/bin/php
//$sql = "select count(*) as total from mallstory_order_status where DATE_ADD(regdate,INTERVAL 7 DAY)  <= '".$today."' and status = 'DI' ";

$sql = "select mall_dc_interval,mall_cc_interval from mallstory_shopinfo where mall_div = 'B' ";
$db->query($sql);
$db->fetch();
$sql = "select oid,od_ix,pid from mallstory_order_detail where DATE_ADD(di_date,INTERVAL ".$db->dt[mall_dc_interval]." DAY)  <= '".$today."' and status = 'DI' ";

$mdb->query($sql);

for($i=0;$i<$mdb->total;$i++){
	$mdb->fetch($i);
	$sql = "update mallstory_order_detail set status = 'DC', dc_date = NOW() where od_ix = '".$mdb->dt[od_ix]."'";
	$idb->query($sql);

	$sql = "insert into mallstory_order_status(oid,pid, status,status_message,regdate) values ('".$mdb->dt[oid]."','".$mdb->dt[pid]."','DC','".$db->dt[mall_dc_interval]."일경과 자동변경',NOW())";
	$idb->query($sql);
	$sql = "update mallstory_reserve_info set state = '1' where pid = '".$mdb->dt[pid]."' and oid = '".$mdb->dt[oid]."' and state = '0' ";
	$idb->query($sql);
}

//주문접수 후 7일간 입금이 안될수 자동 주문취소 프로세서
//$sql = "select count(*) as total from mallstory_order_status where  DATE_ADD(regdate,INTERVAL 7 DAY)  <= '".$today."' and status = 'IR' ";
//$db->query($sql);
//$db->fetch();

$sql = "select od_ix, oid, pid from mallstory_order_detail where DATE_ADD(regdate,INTERVAL ".$db->dt[mall_cc_interval]." DAY)  <= '".$today."' and status = 'IR' order by regdate desc ";
$mdb->query($sql);
	for($i=0;$i<$mdb->total;$i++){
		$mdb->fetch($i);

		$sql = "update mallstory_order_detail set status = 'CC' where od_ix = '".$mdb->dt[od_ix]."' ";
		//echo $sql."<br>";

		$idb->query($sql);
		//$sql = "update mallstory_order set status = '".ORDER_STATUS_CANCEL_COMPLETE."' where oid = '".$mdb->dt[oid]."'";
		//echo $sql."<br>";
		//$idb->query($sql);
		$sql = "insert into mallstory_order_status(oid,status,status_message,regdate) values ('".$mdb->dt[oid]."','CC','".$db->dt[mall_cc_interval]."일경과 system 자동취소',NOW())";
		//echo $sql."<br>";
		$idb->query($sql);

		$sql = "update mallstory_reserve_info set state = '9' where oid = '".$mdb->dt[oid]."'";
		$idb->query($sql);

		//$db->query("update ".TBL_MALLSTORY_RESERVE_INFO." set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid='".$mdb->dt[oid]."' and pid = '".$mdb->dt[pid]."' and state = '".RESERVE_STATUS_READY."' ");
		//echo $sql."<br>";
		//exit;
	}

//주문접수후 2일경과후 입금이 안될시 문자메세지 발송
/*$sql = "select count(*) as total from mallstory_order_status where  DATE_ADD(regdate,INTERVAL 2 DAY)  <= '".$today."' and status = 'IR' ";
$db->query($sql);
$db->fetch();
$cominfo = getcominfo();
$sdb = new Database;
$s = new SMS();
$s->send_phone = $cominfo[com_phone];
$s->send_name = $cominfo[com_name];
$s->admin_mode = true;
$sql = "select oid from mallstory_order_status where DATE_ADD(regdate,INTERVAL 2 DAY)  <= '".$today."' and status = 'IR' ";
$mdb->query($sql);
	for($i=0;$i<$db->dt[total];$i++){
		$mdb->fetch($i);

		$sql = "select bmobile,bname from mallstory_order where oid = '".$mdb->dt[oid]."'";
		$idb->query($sql);
		$idb->fetch();
		$s->dest_phone = str_replace("-","",$idb->dt[bmobile]);
		$s->dest_name = "$db->dt[bname]";
		$s->msg_body =$sms_contents;

		$s->sendbyone($cominfo);
	}*/
?>