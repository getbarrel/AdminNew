<?php
	include("../class/layout.class");
	include '../include/phpexcel/Classes/PHPExcel.php';
	
	if ($vToYY == ""){
		$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
		$vdate = date("Ymd", time());
		$today = date("Ymd", time());
	
		$sDate = date("Y/m/d", time()-84600*(date("d")-1));
		$eDate = date("Y/m/d", time()+84600*(31-date("d")));
	
		$startDate = date("Ymd", time()-84600*(date("d")-1));
		$endDate = date("Ymd", time()+84600*(31-date("d")));
	
		$eDate = date("Y/m/t",strtotime('-1 month'));
		$endDate = date("Ymt",strtotime('-1 month'));
	
	}else{
		$vdate = date("Ymd", time());
		$today = date("Ymd", time());
	
		$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
		$eDate = $vToYY."/".$vToMM."/".$vToDD;
		$startDate = $vFromYY.$vFromMM.$vFromDD;
		$endDate = $vToYY.$vToMM.$vToDD;
	}
	
	
	
	$db = new Database;
	
	$where=" AND p.product_type NOT IN ('4','5','6') ";

	if ($vFromYY != ""){
		$where .= "and date_format(od.dc_date,'%Y%m%d') <= $endDate ";
	}else{
		$where .= "and date_format(od.dc_date,'%Y%m%d') <= $endDate ";
	}
	
	
	if($admininfo[admin_level] == 9){
	
	
		if($company_id != "") $where .= " and c.company_id='$company_id' ";
	$sql = "SELECT c.com_name,p.admin as company_id,bank_name,bank_number,bank_owner ,
			sum(od.pcnt) as sell_cnt,

			sum(if(od.account_type = '1' or od.account_type = '',od.ptprice,od.coprice)) as sell_total_ptprice,
			sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(100-od.commission)/100,od.coprice*(100-od.commission)/100)) as sell_total_coprice,
			sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, 
			sum(if(od.delivery_type = '2',od.delivery_price,'0')) as shipping_price,
			sum(if(o.method = 1,if(od.account_type = 1 or od.account_type = '', od.ptprice,od.coprice),'')) as card_ptprice,
			sum(if(o.method = 5 or o.method = 8 or o.method = 0 or o.method = 4 ,if(od.account_type = 1 or od.account_type = '',od.ptprice,od.coprice),'')) as bank_ptprice,
			sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100)) as commission_price,

			od.regdate as order_com_date,
			avg(od.commission) as avg_commission, 
			count(*) as order_cnt
			FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
			left join ".TBL_COMMON_COMPANY_DETAIL." c on p.admin = c.company_id
			left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
			WHERE  od.status = 'DC' and od.account_type != '3' and od.company_id is not null and p.admin is not null  $where group by admin " ; // and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate
	}else if($admininfo[admin_level] == 8){
		$where .= " and c.company_id = '".$admininfo[company_id]."'";
	
		$sql = "SELECT c.com_name,p.admin as company_id,bank_name,bank_number,bank_owner ,
			sum(od.pcnt) as sell_cnt, 

			sum(if(od.account_type = '1' or od.account_type = '',od.ptprice,od.coprice)) as sell_total_ptprice,
			sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(100-od.commission)/100,od.coprice*(100-od.commission)/100)) as sell_total_coprice,
			sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, 
			sum(if(od.delivery_type = '2',od.delivery_price,'0')) as shipping_price,
			sum(if(o.method = 1,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as card_ptprice,
			sum(if(o.method = 5 or o.method = 8 or o.method = 0 or o.method = 4 ,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as bank_ptprice,
			sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100)) as commission_price,

			od.regdate as order_com_date,avg(od.commission) as avg_commission, count(*) as order_cnt
			FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
			left join ".TBL_COMMON_COMPANY_DETAIL." c on p.admin = c.company_id
			left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
			WHERE  od.status = 'DC' and od.account_type != '3' and od.company_id is not null and p.admin is not null  $where group by admin " ; // and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate
	}
	
	$db->query($sql);
	
	//error_reporting(E_ALL);
	
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	
	date_default_timezone_set('Asia/Seoul');
	
	$accounts_plan_priceXL = new PHPExcel();
	
	// 속성 정의
	$accounts_plan_priceXL->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");

								 
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('A' . 1, "번호");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('B' . 1, "업체명");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('C' . 1, "현금");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('D' . 1, "카드");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('E' . 1, "배송비");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('F' . 1, "정산기준금액");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('G' . 1, "정산수수료");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('H' . 1, "정산금액");

	for ($i = 0; $i < $db->total; $i++){
		$db->fetch($i);
		
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('A' . ($i + 2), ($i + 1));
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('B' . ($i + 2), $db->dt[com_name]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('C' . ($i + 2), $db->dt[bank_ptprice]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('D' . ($i + 2), $db->dt[card_ptprice]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('E' . ($i + 2), $db->dt[shipping_price]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('F' . ($i + 2), $db->dt[sell_total_ptprice]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('G' . ($i + 2), $db->dt[commission_price]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('H' . ($i + 2), $db->dt[shipping_price]+$db->dt[sell_total_ptprice]-$db->dt[commission_price]);
		
	}
	
	$accounts_plan_priceXL->getActiveSheet()->setTitle('정산예정내역');
	
	// 첫번째 시트 선택
	$accounts_plan_priceXL->setActiveSheetIndex(0);
	
	// 너비조정
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="accounts_plan_price.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($accounts_plan_priceXL, 'Excel5');
	$objWriter->save('php://output');