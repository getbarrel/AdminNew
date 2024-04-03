<?
include("../class/layout.class");


if($act == "order_detail_update"){
	/*
	$sql = "select * from shop_order_detail where od_ix = '$od_ix'";
	//echo $sql;

	$db->query($sql);
	$sp_order_details = $db->fetchall();
	$sp_order_detail = $sp_order_details[0];

	for($i=0;$i < count($pcnt);$i++){
		if($i == 0){
			$sql = "update shop_order_detail set			
			oid='".$sp_order_detail[oid]."',
			rfid='".$sp_order_detail[rfid]."',
			buyer_type='".$sp_order_detail[buyer_type]."',
			order_from='".$sp_order_detail[order_from]."',
			cid='".$sp_order_detail[cid]."',
			set_pid='".$sp_order_detail[set_pid]."',
			pid='".$sp_order_detail[pid]."',
			pcode='".$sp_order_detail[pcode]."',
			barcode='".$sp_order_detail[barcode]."',
			product_type='".$sp_order_detail[product_type]."',
			pname='".$sp_order_detail[pname]."',
			paper_pname='".$sp_order_detail[paper_pname]."',
			bc_ix='".$sp_order_detail[bc_ix]."',
			option1='".$sp_order_detail[option1]."',
			option_text='".$sp_order_detail[option_text]."',
			option_etc='".$sp_order_detail[option_etc]."',
			option_kind='".$sp_order_detail[option_kind]."',
			option_price='".$sp_order_detail[option_price]."',
			pcnt='".$pcnt[$i]."',
			coprice='".$sp_order_detail[coprice]."',
			psprice='".$sp_order_detail[psprice]."',
			ptprice='".($sp_order_detail[psprice]*$pcnt[$i])."',
			reserve='".$sp_order_detail[reserve]."',
			use_coupon='".$sp_order_detail[use_coupon]."',
			use_coupon_code='".$sp_order_detail[use_coupon_code]."',
			msgbyproduct='".$sp_order_detail[msgbyproduct]."',
			status='".$sp_order_detail[status]."',
			delivery_status='".$sp_order_detail[delivery_status]."',
			refund_status='".$sp_order_detail[refund_status]."',
			admin_message='".$sp_order_detail[admin_message]."',
			coupon_sdate='".$sp_order_detail[coupon_sdate]."',
			coupon_edate='".$sp_order_detail[coupon_edate]."',
			dispathpoint='".$sp_order_detail[dispathpoint]."',
			odd_ix='".$sp_order_detail[odd_ix]."',
			delivery_method='".$sp_order_detail[delivery_method]."',
			quick='".$sp_order_detail[quick]."',
			invoice_no='".$sp_order_detail[invoice_no]."',
			delivery_price='".$sp_order_detail[delivery_price]."',
			delivery_type='".$sp_order_detail[delivery_type]."',
			delivery_company='".$sp_order_detail[delivery_company]."',
			company_id='".$sp_order_detail[company_id]."',
			company_name='".$sp_order_detail[company_name]."',
			one_commission='".$sp_order_detail[one_commission]."',
			commission='".$sp_order_detail[commission]."',
			surtax_yorn='".$sp_order_detail[surtax_yorn]."',
			stock_use_yn='".$sp_order_detail[stock_use_yn]."',
			ic_date='".$sp_order_detail[ic_date]."',
			di_date='".$sp_order_detail[di_date]."',
			dc_date='".$sp_order_detail[dc_date]."'
			where od_ix = '".$od_ix."' ";

			$db->query($sql);

			$sql = "insert into ".TBL_SHOP_ORDER_STATUS." 
						(os_ix, oid, pid, status, status_message, admin_message, company_id,quick,invoice_no, regdate ) 
						values 
						('','".$sp_order_detail[oid]."','".$sp_order_detail[pid]."','".$sp_order_detail[status]."','[".$od_ix."] 주문상세정보 분리  ','".$admininfo[charger]."(".$admininfo[charger_id].")','".$sp_order_detail[company_id]."','".$sp_order_detail[quick]."','".$sp_order_detail[deliverycode]."',NOW())";

			$db->query($sql);        

		}else{
			$sql = "insert into shop_order_detail set
			od_ix='',
			oid='".$sp_order_detail[oid]."',
			rfid='".$sp_order_detail[rfid]."',
			buyer_type='".$sp_order_detail[buyer_type]."',
			order_from='".$sp_order_detail[order_from]."',
			cid='".$sp_order_detail[cid]."',
			set_pid='".$sp_order_detail[set_pid]."',
			pid='".$sp_order_detail[pid]."',
			pcode='".$sp_order_detail[pcode]."',
			barcode='".$sp_order_detail[barcode]."',
			product_type='".$sp_order_detail[product_type]."',
			pname='".$sp_order_detail[pname]."',
			paper_pname='".$sp_order_detail[paper_pname]."',
			bc_ix='".$sp_order_detail[bc_ix]."',
			option1='".$sp_order_detail[option1]."',
			option_text='".$sp_order_detail[option_text]."',
			option_etc='".$sp_order_detail[option_etc]."',
			option_kind='".$sp_order_detail[option_kind]."',
			option_price='".$sp_order_detail[option_price]."',
			pcnt='".$pcnt[$i]."',
			coprice='".$sp_order_detail[coprice]."',
			psprice='".$sp_order_detail[psprice]."',
			ptprice='".($sp_order_detail[psprice]*$pcnt[$i])."',
			reserve='".$sp_order_detail[reserve]."',
			use_coupon='',
			use_coupon_code='',
			msgbyproduct='".$sp_order_detail[msgbyproduct]."',
			status='".$sp_order_detail[status]."',
			delivery_status='".$sp_order_detail[delivery_status]."',
			refund_status='".$sp_order_detail[refund_status]."',
			admin_message='".$sp_order_detail[admin_message]."',
			coupon_sdate='',
			coupon_edate='',
			dispathpoint='".$sp_order_detail[dispathpoint]."',
			odd_ix='".$sp_order_detail[odd_ix]."',
			delivery_method='".$sp_order_detail[delivery_method]."',
			quick='".$sp_order_detail[quick]."',
			invoice_no='".$sp_order_detail[invoice_no]."',
			delivery_price='".$sp_order_detail[delivery_price]."',
			delivery_type='".$sp_order_detail[delivery_type]."',
			delivery_company='".$sp_order_detail[delivery_company]."',
			company_id='".$sp_order_detail[company_id]."',
			company_name='".$sp_order_detail[company_name]."',
			one_commission='".$sp_order_detail[one_commission]."',
			commission='".$sp_order_detail[commission]."',
			surtax_yorn='".$sp_order_detail[surtax_yorn]."',
			stock_use_yn='".$sp_order_detail[stock_use_yn]."',
			ic_date='".$sp_order_detail[ic_date]."',
			di_date='".$sp_order_detail[di_date]."',
			dc_date='".$sp_order_detail[dc_date]."',
			regdate='".$sp_order_detail[regdate]."' ";
			//regdate=NOW() -> regdate='".$sp_order_detail[regdate]."' 바꿈 (매출집계시 기준일자 틀려짐) 20131010 Hong
			$db->query($sql);


			$sql = "insert into ".TBL_SHOP_ORDER_STATUS." 
						(os_ix, oid, pid, status, status_message, admin_message, company_id,quick,invoice_no, regdate ) 
						values 
						('','".$sp_order_detail[oid]."','".$sp_order_detail[pid]."','".$sp_order_detail[status]."','[".$od_ix."] 주문상세정보 복제  ','".$admininfo[charger]."(".$admininfo[charger_id].")','".$sp_order_detail[company_id]."','".$sp_order_detail[quick]."','".$sp_order_detail[deliverycode]."',NOW())";

			$db->query($sql);        

		}
		
		

		//echo nl2br($sql)."<br><br>";
	}
	//exit;
	$sql = "select count(*) as total from shop_order_detail where oid = '".$sp_order_detail[oid]."' and company_id = '".$sp_order_detail[company_id]."' ";
	//echo $sql;

	$db->query($sql);
	$db->fetch();
	$company_total = $db->dt[total];

	$sql = "update shop_order_delivery set company_total = '".$company_total."' where oid = '".$sp_order_detail[oid]."' and company_id = '".$sp_order_detail[company_id]."' ";
	//echo $sql;
	//exit;
	$db->query($sql);
	*/
	
	for($i=1;$i < count($pcnt);$i++){
		orderSeparate($od_ix,$pcnt[$i]);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('주문정보 분리가 정상적으로 처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();/**/</script>");
}



	
?>