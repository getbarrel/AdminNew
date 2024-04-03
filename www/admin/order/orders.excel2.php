<?
include("../class/layout.class");


header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-Disposition: attachment; filename=order_list.xls" );
header( "Content-Description: Generated Data" ); 

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
		if($company_id != ""){
			$where .= " and o.oid = od.oid and od.pid = p.id and  p.admin = '".$company_id."'";
		}else{
			$where .= " and o.oid = od.oid and  od.pid = p.id ";
		}
	}else if($admininfo[admin_level] == 8){	
		$where .= " and o.oid = od.oid and  od.pid = p.id and p.admin = '".$admininfo[company_id]."'";
	}
	//$admininfo[company_id]

/*	
	if($admininfo[admin_level] == 9){
		if($admincode == ""){
			
			$db1->query("SELECT oid FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p $where GROUP BY oid ORDER BY date DESC");			
						
		
			$total = $db1->total;	
			echo ("SELECT oid, uid, bname, o.rname, tid, status, method, total_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p $where GROUP BY oid ORDER BY date DESC ");
			$db1->query("SELECT oid, uid, bname, o.rname, tid, status, method, total_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p $where GROUP BY oid ORDER BY date DESC ");
		}else{
			$db1->query("SELECT oid FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p $where GROUP BY oid ORDER BY date DESC");			
						
		
			$total = $db1->total;	
			$db1->query("SELECT oid, uid, bname,o.rname,  tid, status, method, total_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p $where GROUP BY oid ORDER BY date DESC ");
		}
	}else if($admininfo[admin_level] == 8){
		$db1->query("SELECT * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where GROUP BY o.oid ORDER BY date DESC");		
	
		$total = $db1->total;	
		
		$db1->query("SELECT o.oid, uid, o.bname, o.rname, tid, o.status, method, total_price, payment_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where GROUP BY o.oid ORDER BY date DESC ");
	}
*/
//echo ("SELECT * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where  ORDER BY date DESC");		
$db1->query("SELECT * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where  ORDER BY date DESC");		
	
$total = $db1->total;	
//echo ("SELECT o.oid, uid, o.bname, o.rname, tid, o.status, method, total_price, payment_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where GROUP BY o.oid ORDER BY date DESC ");
$db1->query("SELECT o.oid, uid, o.bname, o.rname, tid, o.status, method, total_price, payment_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where ORDER BY date DESC ");
	
if($db1->total){
	echo "주문자명\t수취인명\t수취인주소\t우편번호\t전화번호\t이동통신\t상품명\t배송메시지\n";
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
			$delete = "<a href=\"javascript:alert(language_data['orders.excel2.php']['A'][language]);\"><img src='../image/btc_del.gif' border=0></a>";//'[처리완료] 기록은 삭제할 수 없습니다.'
		}
		elseif ($db1->dt[status] != "9" && $db1->dt[method] == "1")
		{
			$delete = "<a href=\"javascript:alert(language_data['orders.excel2.php']['B'][language]);\"><img src='../image/btc_del.gif' border=0></a>";//'[카드결제]는 [승인취소]와 [주문취소] 처리를 먼저한 후 삭제해주세요.'
		}
		else
		{
			$delete = "<a href=\"javascript:act('delete','$Obj');\"><img src='../image/btc_del.gif' border=0></a>";
		}
		
		//주문자명,수취인명,수취인주소,우편번호,전화번호,이동통신,상품명,배송메시지 $method."\t".
		
		echo $db1->dt[bname]."\t".$db1->dt[rname]."\t".$db1->dt[addr]."\t".$db1->dt[zip]."\t".$db1->dt[rtel]."\t".$db1->dt[rmobile]."\t".$db1->dt[pname]."\t".$db1->dt[msg]."\n";

	}
}



?>
