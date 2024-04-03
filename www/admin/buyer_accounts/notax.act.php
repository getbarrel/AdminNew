<?
include("../class/layout.class");

$db = new Database;

if($act=="receipt_apply"){

	$db->query("UPDATE shop_order_payment SET receipt_yn = 'Y' WHERE oid ='".$oid."' and method not in ('".ORDER_METHOD_RESERVE."','".ORDER_METHOD_CARD."','".ORDER_METHOD_MOBILE."') ");

	$db->query("insert into receipt(order_no,order_type,m_useopt,m_number,id,rname,receipt_yn,regdate) values('$oid','1','$m_useopt','$m_number','$id','$rname','Y',NOW())");

	if($m_useopt==0){
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('소득공제신청이 정상적으로  처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>");
	}elseif($m_useopt==1){
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('지출증빙신청이 정상적으로  처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>");
	}
	exit;
}

if($act=="taxbill_apply"){
	

	$db->query("UPDATE shop_order_payment SET taxsheet_yn = 'Y', tax_com_name='".$tax_com_name."', tax_com_ceo='".$tax_com_ceo."', tax_com_number='".$tax_com_number."' WHERE oid ='".$oid."' and method not in ('".ORDER_METHOD_RESERVE."','".ORDER_METHOD_CARD."','".ORDER_METHOD_MOBILE."') ");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('세금&계산서신청이 정상적으로  처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>");

	exit;
}

if($act=="tax_affairs"){

	if(is_array($oid)){
		for($i=0;$i < count($oid);$i++){
			if($oid[$i] != ""){
				if($oid_str == ""){
					$oid_str .= "'".$oid[$i]."'";
				}else{
					$oid_str .= ",'".$oid[$i]."' ";
				}
			}
		}
		if($oid_str != ""){
			$where = "WHERE oid in ($oid_str) ";
		}
	}else{
		if($oid){
			$where = "WHERE oid = '$oid' ";
		}
	}

	$db->query("UPDATE shop_order_payment SET tax_affairs_yn = '$tax_affairs_yn' ".$where." and method !='".ORDER_METHOD_RESERVE."' ");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('세무신고완료가 정상적으로  처리 되었습니다.');parent.document.location.reload();</script>");
	exit;
}

if($act=="report_price"){

	if($nar_ix!="0"){
		$db->query("UPDATE shop_notax_affairs_report SET report_price = '".$report_price[($nar_ix)]."' , update_charger_ix= '".$admininfo[charger_ix]."' , updatedate = NOW()  WHERE nar_ix ='".$nar_ix."' ");
	}else{

		$db->query("insert into shop_notax_affairs_report (nar_ix,sdate,edate,payment_price,save_price,refund_price,total_price,report_price,charger_ix,regdate) values('','$sdate','$edate','$payment_price','$save_price','$refund_price','$total_price','".$report_price[($nar_ix)]."','".$admininfo[charger_ix]."',NOW())");
			
		$db->query("select nar_ix from shop_notax_affairs_report where nar_ix = LAST_INSERT_ID()  ");
		$db->fetch();
		$nar_ix = $db->dt[nar_ix];

		$opay_ix_str=str_replace(',',"','",$opay_ix_str);
		$db->query("update shop_order_payment set nar_ix ='".$nar_ix."' where opay_ix in  ('".$opay_ix_str."') ");
	}


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('실 세무신고액 수정이 정상적으로 처리 되었습니다. ');parent.document.location.reload();</script>");
	exit;
}

?>

