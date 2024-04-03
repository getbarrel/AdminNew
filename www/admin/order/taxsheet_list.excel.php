<?
///////////////// CREATE EXCEL FILE METHOD ///////////////// 

///////////////////// END //////////////////////////
include("../class/layout.class");

header('Pragma: public');
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
header('Content-Type: text/plain; charset=UTF-8');
header('Content-Transfer-Encoding: binary');
header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
header("Content-type: application/x-msexcel");                    // This should work for the rest
header('Content-Disposition: attachment; filename='.iconv("utf-8","CP949","세금계산서리스트").'.xls');

$db = new Database;
$db2 = new Database;
$mdb = new Database;
//$where = " where code != '' and mm.gp_ix = mg.gp_ix and gp_level != 0 ";
	if ($orderby != "" || $ordertype != ""){
		$orderby_str = " order by com_number, list_type, $orderby $ordertype ";
	}else{
		$orderby_str = " order by com_number, list_type, regdate desc ";
	}

	if($tax_yn == "Y"){
		$where1 .= " and taxsheet_yn = 'Y' ";
		$where2 .= " and tax_yn = 'Y' ";
	}else if($tax_yn == "C"){
		$where1 .= " and taxsheet_yn = 'C' ";
		$where2 .= " and tax_yn = 'C' ";
	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
		
	if($startDate != "" && $endDate != ""){	
		$where1 .= " and  MID(replace(o.date,'-',''),1,8) between  $startDate and $endDate ";
		$where2 .= " and  MID(replace(regdate,'-',''),1,8) between  $startDate and $endDate ";
	}

	if($search_text != ""){
		$where1 .= " and $search_type LIKE '%$search_text%' ";
		if($search_type == "oid"){
			$where2 .= " and sid LIKE '%$search_text%' ";
		}else{
			$where2 .= " and $search_type LIKE '%$search_text%' ";
		}
	}

	$sql = "select cu.code as code,'1' as list_type, com_number as com_number, ccd.com_name as com_name, ccd.com_ceo as com_ceo, ccd.com_zip as com_zip, ccd.com_addr1 as com_addr1 , ccd.com_addr2 as com_addr2, com_business_status as com_business_status, com_business_category as com_business_category, com_mail as mail, cmd.pcs as pcs , payment_price as payment_price, o.delivery_price as delivery_price, cmd.name as name,cu.id as id,o.oid as oid,o.date as regdate ,o.taxsheet_yn as tax_yn ,'1' as order_type,
	count(*) as order_detail_cnt , 
	sum(case when od.status in ('CC','RC','DC','FC','AR','AC','AA') then 1 else 0 end) as complet_cnt,
	sum(case when od.status in ('CC','RC','FC') then 1 else 0 end) as cancel_cnt, 
	sum(case when od.status in ('IR','IC','DR','DI','EA','EI','ED','EC','FA','RA','RI','RD','CA') then 1 else 0 end) as ing_cnt
	from shop_order o, common_user cu, common_member_detail cmd, common_company_detail ccd , shop_order_detail od
	where taxsheet_yn in ('Y','C') and o.oid = od.oid 
	and o.uid = cu.code and cu.code = cmd.code and cu.company_id = ccd.company_id
	and replace(ccd.com_number,'-','') != '' 
	$where1
	group by o.oid having cancel_cnt != order_detail_cnt
	union
	select  cu.code as code,'2' as list_type, com_number as com_number, ccd.com_name as com_name, ccd.com_ceo as com_ceo, ccd.com_zip as com_zip, com_addr1 as com_addr1 , com_addr2 as com_addr2, com_business_status as com_business_status, com_business_category as com_business_category, mail as mail, pcs as pcs , payment_price as payment_price,'0' as delivery_price, cmd.name as name, cu.id as id, o.sid as oid,o.regdate as regdate ,o.tax_yn as tax_yn,'2' as order_type, 1 as order_detail_cnt , 1 as complet_cnt , 0 as cancel_cnt, 0 as ing_cnt
	from shop_regular_member o, common_user cu, common_member_detail cmd, common_company_detail ccd ,
	where tax_yn in ('Y','C') and o.method in (0,1) and o.code = cu.code  and cu.code = cmd.code and cu.company_id = ccd.company_id
	and replace(ccd.com_number,'-','') != '' and status = '2' $where2
	$orderby_str  ";


	$db->query($sql);
	$excel_datas = $db->fetchall();
xlsBOF();

xlsWriteLabel(0,0,"번호");
xlsWriteLabel(0,1,"이름(ID)");
xlsWriteLabel(0,2,"사업자번호");
xlsWriteLabel(0,3,"상호");
xlsWriteLabel(0,4,"대표자");
xlsWriteLabel(0,5,"주소");
xlsWriteLabel(0,6,"업태");
xlsWriteLabel(0,7,"종목");
xlsWriteLabel(0,8,"구매상품");
xlsWriteLabel(0,9,"공급가액");
xlsWriteLabel(0,10,"세액");
xlsWriteLabel(0,11,"총액");
xlsWriteLabel(0,12,"이메일");
xlsWriteLabel(0,13,"휴대폰");
xlsWriteLabel(0,14,"요청일자");
xlsWriteLabel(0,15,"비고");
xlsWriteLabel(0,16,"주문상품");
xlsWriteLabel(0,17,"취소/반품");
xlsWriteLabel(0,18,"주문완료");
xlsWriteLabel(0,19,"처리중");


//------ row 1 data ------ 
for($i=0, $j=0;$i < count($excel_datas);$i++,$j++){
	//$db->fetch($i);

	
	if($excel_datas[$i][order_type] == "1"){
		$sql = "select pname,count(*) as total_count,sum(ptprice) as ptprice from shop_order_detail where oid = '".$excel_datas[$i][oid]."' and status in ('".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_APPLY."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')  group by oid";
		$mdb->query($sql);
		$mdb->fetch();
		$payment_price = $mdb->dt[ptprice];
		$pname = $mdb->dt[pname]." 외 ".($mdb->dt[total_count]-1)."건";
	}else{
		$pname = "정회원결제";
		$payment_price = $excel_datas[$i][payment_price];
	}

	$total_price = $payment_price+$excel_datas[$i][delivery_price];
	$total_coprice = round($total_price/1.1,0);
	$tax_price = $total_price-round($total_price/1.1,0);

	xlsWriteLabel(($j+1), 0, ($i+1));
	xlsWriteLabel(($j+1), 1, $excel_datas[$i][name]."(".$excel_datas[$i][id].")");
	xlsWriteLabel(($j+1), 2, $excel_datas[$i][com_number]);
	xlsWriteLabel(($j+1), 3, $excel_datas[$i][com_name]);
	xlsWriteLabel(($j+1), 4, $excel_datas[$i][com_ceo]);
	xlsWriteLabel(($j+1), 5, "[".$excel_datas[$i][com_zip]."] ".$excel_datas[$i][com_addr1]." ".$excel_datas[$i][com_addr2]);
	xlsWriteLabel(($j+1), 6, $excel_datas[$i][com_business_category]);
	xlsWriteLabel(($j+1), 7, $excel_datas[$i][com_business_status]);
	xlsWriteLabel(($j+1), 8, $pname);	
	xlsWriteNumber(($j+1), 9, $total_coprice);
	xlsWriteNumber(($j+1), 10, $tax_price);
	xlsWriteNumber(($j+1), 11, $total_price);
	xlsWriteLabel(($j+1), 12, $excel_datas[$i][mail]);
	xlsWriteLabel(($j+1), 13, $excel_datas[$i][pcs]);
	xlsWriteLabel(($j+1), 14, $excel_datas[$i][regdate]);
	xlsWriteLabel(($j+1), 15, "-");
	xlsWriteNumber(($j+1), 16, $excel_datas[$i][order_detail_cnt]);
	xlsWriteNumber(($j+1), 17, $excel_datas[$i][cancel_cnt]);
	xlsWriteNumber(($j+1), 18, $excel_datas[$i][complet_cnt]-$excel_datas[$i][cancel_cnt]);
	xlsWriteNumber(($j+1), 19, $excel_datas[$i][ing_cnt]);

	$sum_total_coprice += $total_coprice;
	$sum_tax_price += $tax_price;
	$sum_total_price += $total_price;

	$g_sum_total_coprice += $total_coprice;
	$g_sum_tax_price += $tax_price;
	$g_sum_total_price += $total_price;

	if($excel_datas[$i][list_type] != $excel_datas[$i+1][list_type] || $excel_datas[$i][com_number] != $excel_datas[$i+1][com_number]){
		$j++;
		xlsWriteLabel(($j+1), 8, "소계");	
		xlsWriteNumber(($j+1), 9, $sum_total_coprice);
		xlsWriteNumber(($j+1), 10, $sum_tax_price);
		xlsWriteNumber(($j+1), 11, $sum_total_price);
		$j++;
		$sum_total_coprice = "";
		$sum_tax_price = "";
		$sum_total_price = "";
/*
		if($excel_datas[$i][list_type] != $excel_datas[$i+1][list_type] && $excel_datas[$i][com_number] != $excel_datas[$i+1][com_number]){
			//$j++;
			xlsWriteLabel(($j+1), 8, "소계");	
			xlsWriteNumber(($j+1), 9, $g_sum_total_coprice);
			xlsWriteNumber(($j+1), 10, $g_sum_tax_price);
			xlsWriteNumber(($j+1), 11, $g_sum_total_price);
			$j++;

			$g_sum_total_coprice = "";
			$g_sum_tax_price = "";
			$g_sum_total_price = "";
		}
*/
	}

	$b_list_type = $excel_datas[$i][list_type];
	$b_com_number = $excel_datas[$i][com_number];


	unset($total_price);
	unset($total_coprice);
	unset($tax_price);
	unset($payment_price);
	unset($pname);
	
}

xlsEOF();

?>