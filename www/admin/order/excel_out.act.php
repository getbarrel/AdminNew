<?
include("../class/layout.class");

//print_r($_POST);

if($act == "cookie_setting" || $act == "delivery_cookie_setting"){
	
	$db = new Database;
	if($act == "cookie_setting"){
		$sql = "update ".TBL_COMMON_SELLER_DETAIL." Set 
				order_excel_info1 = '".serialize($order_excel_info1)."',
				order_excel_info2='".serialize($order_excel_info2)."',
				order_excel_checked='".serialize($colums)."'
				where company_id = '".$admininfo[company_id]."'";
	}else{
				$sql = "update ".TBL_COMMON_SELLER_DETAIL." Set 
				delivery_excel_info1 = '".serialize($order_excel_info1)."',
				delivery_excel_info2='".serialize($order_excel_info2)."',
				delivery_excel_checked='".serialize($colums)."'
				where company_id = '".$admininfo[company_id]."'";

	}
	//echo $sql;
	//exit;
	$db->query($sql);
	
/*	
	setcookie("_excel_sortlist",serialize($excel_sortlist), time()+3600000,"/",$HTTP_HOST);
	setcookie("_excel_sortlist2",serialize($excel_sortlist2), time()+3600000,"/",$HTTP_HOST);
	setcookie("_colums_checked",serialize($colums), time()+3600000,"/",$HTTP_HOST);
	*/
//	print_r($excel_sortlist);
//	print_r($excel_sortlist2);
//	print_r($colums);
	//echo "주문내역 저장하기 설정정보가 정상적으로 저장되었습니다.";
	if($act == "cookie_setting"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('주문 엑셀정보 설정이 정상적으로 저장되었습니다.');parent.document.location.reload();</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배송 엑셀정보 설정이 정상적으로 저장되었습니다.');parent.document.location.reload();</script>";
	}
	exit;
}

if($act == "delete_setting" ){
	$db = new Database;

	//$sql = "delete from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='inventory_excel_".$info_type."'   ";
	if($excel_type == "delivery"){
		$sql = "update ".TBL_COMMON_SELLER_DETAIL." set 
					delivery_excel_info1 = '', delivery_excel_info2 = '', delivery_excel_checked = ''
				where company_id = '".$admininfo[company_id]."'";
	}else{
		$sql = "update ".TBL_COMMON_SELLER_DETAIL." set 
					order_excel_info1 = '', order_excel_info2 = '', order_excel_checked = ''
				where company_id = '".$admininfo[company_id]."'";

	}
	//echo $sql;
	$db->query($sql);

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert(' 정상적으로 삭제되었습니다.');parent.document.location.reload();</script>";

}

if($act == "default_setting"){
	//setcookie("_excel_sortlist","", time()-100,"/",$HTTP_HOST);
	//setcookie("_excel_sortlist2","", time()-100,"/",$HTTP_HOST);
	//setcookie("_colums_checked","", time()-100,"/",$HTTP_HOST);
	
	echo "<script>parent.document.location.reload();</script>";
}

if($act == "excel_out"){
	include("excel_out_columsinfo.php");
	print_r($_POST);
	
	exit;
	header( "Content-type: application/vnd.ms-excel" ); 
	header( "Content-Disposition: attachment; filename=order_list.xls" );
	header( "Content-Description: Generated Data" ); 
	
	$sortlist = unserialize(stripslashes($_COOKIE[_excel_sortlist]));	
	$check_colums = unserialize(stripslashes($_COOKIE[_colums_checked]));
	
	
	$db1 = new Database;
	$odb = new Database;
	
	
	$where = "WHERE o.status <> '' ";
	
	
	if ($oid != "")		$where .= "and oid = '$oid' ";
	if ($bname != "")	$where .= "and bname = '$bname' ";
	if ($rname != "")	$where .= "and rname = '$rname' ";
	if ($vFromYY != "")	$where .= "and date_format(date,'%Y%m%d') between $startDate and $endDate ";
	
	if(is_array($type)){
		
		for($i=0;$i < count($type);$i++){		
			
			if($type_str == ""){
				$type_str .= "'".$type[$i]."'";
			}else{	
				$type_str .= ",'".$type[$i]."'";
			}
		
		}
		
		if($type_str){
			$where .= "and o.status in ($type_str) ";
		}
	}else{		
		//$status = getOrderStatus($type)
		if($status){
			$where .= "and o.status = '$type'";
		}
	
	}
	
	if($admininfo[admin_level] == 9){
		if($admincode != ""){
			$where .= " and o.oid = od.oid and od.pid = p.id and  p.admin = '".$admincode."'";
		}else{
			$where .= " and o.oid = od.oid and  od.pid = p.id ";
		}
	}else if($admininfo[admin_level] == 8){	
		$where .= " and o.oid = od.oid and  od.pid = p.id and p.admin = '".$admininfo[company_id]."'";
	}
	
	//echo ("SELECT * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where  ORDER BY date DESC");		
	$db1->query("SELECT * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where  ORDER BY date DESC");		
		
	$total = $db1->total;	
	//echo ("SELECT o.oid, uid, o.bname, o.rname, tid, o.status, method, total_price, payment_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where GROUP BY o.oid ORDER BY date DESC ");
	$sql = "SELECT o.oid, uid, o.bname, o.bmobile, o.btel, o.bmail, o.rname, o.rmobile, o.rtel, o.rmail, tid, o.status, method, total_price, payment_price, date, p.pname, addr, zip, msg, rtel, rmobile 
			FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where ORDER BY date DESC ";

	$db1->query($sql);
	
	if($db1->total){
		//echo "주문자명\t수취인명\t수취인주소\t우편번호\t전화번호\t이동통신\t상품명\t배송메시지\n";
		$mstring = "";
		
		for($i=0;$i < count($sortlist);$i++){
			$mstring .= $colums[$sortlist[$i]][title]."\t";
		}
		echo $mstring."\n";
		
		for ($i = 0; $i < $db1->total; $i++)
		{
			$db1->fetch($i);
			
			/*
			if ($db1->dt[status] == "0")	$status = "처리대기";
			if ($db1->dt[status] == "1") 	$status = "결제완료";
			if ($db1->dt[status] == "2")	$status = "발송완료";
			if ($db1->dt[status] == "3")	$status = "배송완료";
			if ($db1->dt[status] == "6")	$status = "반품요청";
			if ($db1->dt[status] == "7")	$status = "반품완료";
			if ($db1->dt[status] == "9")	$status = "주문취소";
			*/
			$status = getOrderStatus($db1->dt[status]);
	
			if ($db1->dt[method] == "1")
			{
				if($db1->dt[bank] == ""){
					$method = "카드결제";
				}else{
					$method = $db1->dt[bank];
				}
			}elseif($db1->dt[method] == "0"){
				$method = "계좌입금";
			}elseif($db1->dt[method] == "2"){
				$method = "전화결제";			
			}
	
	
			$psum = number_format($db1->dt[total_price]);
	
			$Obj = str_replace("-","",$db1->dt[oid]);
	
			if ($db1->dt[status] == "3")
			{
				$delete = "<a href=\"javascript:alert('[처리완료] 기록은 삭제할 수 없습니다.');\"><img src='../image/btc_del.gif' border=0></a>";
			}
			elseif ($db1->dt[status] != "9" && $db1->dt[method] == "1")
			{
				$delete = "<a href=\"javascript:alert('[카드결제]는 [승인취소]와 [주문취소] 처리를 먼저한 후 삭제해주세요.');\"><img src='../image/btc_del.gif' border=0></a>";
			}
			else
			{
				$delete = "<a href=\"javascript:act('delete','$Obj');\"><img src='../image/btc_del.gif' border=0></a>";
			}
			
			//주문자명,수취인명,수취인주소,우편번호,전화번호,이동통신,상품명,배송메시지 $method."\t".
			$selected_colums = "";
			for($j=0;$j < count($sortlist);$j++){				
					$selected_colums .= $db1->dt[$colums[$sortlist[$j]][value]]."\t";
				
			}
			echo $selected_colums."\n";
			//echo $db1->dt[bname]."\t".$db1->dt[rname]."\t".$db1->dt[addr]."\t".$db1->dt[zip]."\t".$db1->dt[rtel]."\t".$db1->dt[rmobile]."\t".$db1->dt[pname]."\t".$db1->dt[msg]."\n";
	
		}
	}

}

?>
