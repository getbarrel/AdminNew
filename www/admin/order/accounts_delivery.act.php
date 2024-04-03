<?
include("../class/layout.class");
//print_r($_POST);
//exit;
$db = new Database;
$udb = new Database;

if($act == "account_info_update"){
	
	//echo ("UPDATE ".TBL_SHOP_SHOPINFO." SET account_priod = '$account_priod' WHERE mall_ix='".$admininfo[mall_ix]."'");
	$db->query("UPDATE ".TBL_SHOP_SHOPINFO." SET account_priod = '$account_priod' WHERE mall_ix='".$admininfo[mall_ix]."'");
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정산정보가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

if($act == "accounts_delivery"){
	
		
		$account_priod_str = " and date_format(od.dc_date,'%Y%m%d') <= $eDate"; 	
		
		
	

		if($admininfo[admin_level] == 9){
			

				$sql = "create temporary table shop_delivery_account_tmp ENGINE = MEMORY 
					select o.oid , od.company_id ,  ccd.com_name as company_name,o.delivery_price  , od.one_delivery_price ,  delivery_free_price,  sum(od.ptprice) as sum_ptprice, count(*) as cnt
				 from shop_order o, shop_order_detail od , ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd
				where o.delivery_price != 0 and o.oid = od.oid and od.company_id = ccd.company_id and csd.company_id = ccd.company_id
				and od.delivery_type = 'CD' and od.status = 'AC' and ccd.company_id = '".$company_id."' $account_priod_str
				group by o.oid, od.company_id 
				having  sum(od.ptprice) < delivery_free_price
				order by od.company_id asc, date desc";
				$db->query($sql);
				$sql = "select  oid , company_id ,  company_name,delivery_price  , delivery_free_price,  sum(one_delivery_price)as one_delivery_price ,   sum(sum_ptprice) as sum_ptprice, count(cnt) as cnt
								from shop_delivery_account_tmp
								group by company_id ";
					
		}else if($admininfo[admin_level] == 8){
			
			$sql = "create temporary table shop_delivery_account_tmp ENGINE = MEMORY 
					select o.oid , od.company_id ,  ccd.com_name as company_name,o.delivery_price  , od.one_delivery_price ,  delivery_free_price,  sum(od.ptprice) as sum_ptprice, count(*) as cnt
				 from shop_order o, shop_order_detail od , ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd
				where o.delivery_price != 0 and o.oid = od.oid and od.company_id = ccd.company_id and csd.company_id = ccd.company_id
				and od.delivery_type = 'CD' and od.status = 'AC' and od.company_id = '".$admininfo[company_id]."'	$account_priod_str 
				group by o.oid, od.company_id 
				having  sum(od.ptprice) < delivery_free_price
				order by od.company_id asc, date desc";
			//	echo $sql;
				$db->query($sql);
				$sql = "select  oid , company_id ,  company_name,delivery_price  , delivery_free_price,  sum(one_delivery_price)as one_delivery_price ,   sum(sum_ptprice) as sum_ptprice, count(cnt) as cnt
								from shop_delivery_account_tmp
								group by company_id ";
		}
		
		$db->query($sql);
		//echo $sql;
		
		$accounts = $db->fetchall();
		
		//print_r($accounts);
		//exit;
		//echo count($accounts);
		for($i=0;$i < count($accounts);$i++){
			$dac_price = $accounts[$i][one_delivery_price];
			//echo $dac_price;
			//exit;
			// 위 정산정보를 정산테이블에 입력한다
			$sql = "insert into shop_deliveryprice_accounts (dac_ix,oid, company_id,dac_date,dac_cnt,sum_ptprice, dac_price,status, regdate) 
				values
				('','".$accounts[$i][oid]."','".$accounts[$i][company_id]."','".$eDate."','".$accounts[$i][cnt]."','".$accounts[$i][sum_ptprice]."','".$dac_price."','".ORDER_STATUS_ACCOUNT_READY."',NOW())";
			$db->query($sql);
			//echo $sql;
			$db->query("SELECT dac_ix FROM shop_deliveryprice_accounts WHERE dac_ix=LAST_INSERT_ID() ");
			$db->fetch();
			$dac_ix = $db->dt[0];
			
			$db->query("insert into ".TBL_SHOP_CASH_INFO."(c_ix,company_id,ac_ix,cash,status,etc,regdate) values('','".$accounts[$i][company_id]."','$dac_ix','".($accounts[$i][one_delivery_price])."',".CASH_STATUS_INCOME_COMPLETE.",'배송비 정산 시스템적립',NOW())");
			
			if($admininfo[admin_level] == 9){			
					$sql = "select o.oid , od.dc_date,  o.date, od.company_id ,  ccd.com_name as company_name,o.delivery_price  , 
					case when  sum(od.ptprice) < delivery_free_price then od.one_delivery_price else 0 end as one_delivery_price , delivery_free_price,  sum(od.ptprice) as sum_ptprice, count(*) as cnt
				 from shop_order o, shop_order_detail od , ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd
				where o.delivery_price != 0 and o.oid = od.oid and od.company_id = ccd.company_id and csd.company_id = ccd.company_id
				and od.delivery_type = 'CD' and od.status = 'AC' and od.company_id = '".$company_id."'		
				group by od.oid
				order by od.company_id asc, date desc";
				
			}else if($admininfo[admin_level] == 8){			
					$sql = "select o.oid , od.dc_date,  o.date, od.company_id ,  ccd.com_name as company_name,o.delivery_price  , 
					case when  sum(od.ptprice) < delivery_free_price then od.one_delivery_price else 0 end as one_delivery_price ,   delivery_free_price,  sum(od.ptprice) as sum_ptprice, count(*) as cnt
				 from shop_order o, shop_order_detail od , ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd
				where o.delivery_price != 0 and o.oid = od.oid and od.company_id = ccd.company_id and csd.company_id = ccd.company_id
				and od.delivery_type = 'CD' and od.status = 'AC' and od.company_id = '".$admininfo[company_id]."'		
				group by od.oid
				order by od.company_id asc, date desc";
				
			}
			
			//exit;
			$db->query($sql);
			$accounts_detail = $db->fetchall();
			
			for($j=0;$j < count($accounts_detail);$j++){
				$sql = "UPDATE ".TBL_SHOP_ORDER." SET dac_ix= '$dac_ix', dac_date = '".$eDate."' WHERE oid='".$accounts_detail[$j][oid]."'  ";
				//echo $sql;
				$db->query($sql);					
				
				$db->query("insert into ".TBL_SHOP_ORDER_STATUS."(os_ix,oid, status,status_message, company_id, regdate) values('','".$accounts_detail[$j][oid]."','AR','배송비 정산예정','".$admininfo[company_id]."', NOW())");				
				
				
			}
			//exit;
			//
			//$db->query($sql);
			//echo $accounts[$i][account_total] .":::".$accounts[$i][tax_total];
		}
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배송비 정산이 정상적으로 수행되었습니다.');parent.document.location.href='accounts_delivery.php';self.close();</script>");
	
}

if($act == "select_accounts_update"){
	

	for($i=0;$i < count($company_id);$i++){
	$sql = "SELECT c.com_name as company_name,p.admin as company_id,bank_name,bank_number,bank_owner ,sum(od.pcnt) as sell_cnt, sum(od.ptprice) as sell_total_ptprice,sum(od.coprice*od.pcnt) as sell_total_coprice, sum(sk_point) as sk_point,
			sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, sum(o.delivery_price) as shipping_price, od.regdate as order_com_date,avg(od.commission) as avg_commission, count(*) as order_cnt
			FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid left join ".TBL_COMMON_COMPANY_DETAIL." c on p.admin = c.company_id
			WHERE  od.status = 'DC' and od.company_id is not null and p.admin is not null  and c.company_id = '".$company_id[$i]."' and date_format(od.dc_date,'%Y%m%d') <= $endDate   group by admin " ; 
			
	
	//echo $sql;
	$db->query($sql); 
	}
}

if($act == "initialize"){
	if($admininfo[admin_level] == 9){
		for($i=0;$i < count($ac_ix);$i++){
			$db->query("SELECT * FROM ".TBL_SHOP_ORDER_DETAIL." WHERE ac_ix='".$ac_ix[$i]."' and status = 'AC' ");
			
			for($j=0;$j < $db->total;$j++){
				$db->fetch($j);
				$sql = "UPDATE ".TBL_SHOP_ORDER_DETAIL." SET ac_ix= '', ac_date = '', status = 'DC' WHERE ac_ix ='".$ac_ix[$i]."' and od_ix = '".$db->dt[od_ix]."' ";
				//echo $sql."<br>";
				$udb->query($sql);	
				$sql = "insert into shop_order_status(oid,pid, status,status_message,regdate) values ('".$db->dt[oid]."','".$db->dt[pid]."','AI','정산초기화',NOW())";
				$udb->query($sql);	
			}
			//$db->fetch();
						
			$sql = "delete from ".TBL_SHOP_ACCOUNTS." WHERE ac_ix ='".$ac_ix[$i]."'  ";
			//echo $sql."<br>";
			$db->query($sql);
			$sql = "UPDATE ".TBL_SHOP_ORDER." SET status = 'DC' WHERE oid = '".$db->dt[oid]."' ";
			$db->query($sql);
		}
		
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 정산이 초기화 되었습니다.');parent.document.location.reload();</script>";
	}
}
?>