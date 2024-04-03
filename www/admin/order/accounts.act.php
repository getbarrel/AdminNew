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

if($act == "account_confirm"){
			if($admininfo[mem_type] == "MD"){
				$addWhere .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}

			$db->query("SELECT * FROM ".TBL_SHOP_ORDER_DETAIL." od WHERE ac_ix='".$ac_ix."' and status = '".ORDER_STATUS_ACCOUNT_READY."' $addWhere ");
			$accounts = $db->fetchall();
			for($j=0;$j < count($accounts);$j++){

				$sql = "UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status = '".ORDER_STATUS_ACCOUNT_COMPLETE."' WHERE ac_ix ='".$ac_ix."' and od_ix = '".$accounts[$j][od_ix]."' ";
				//echo $sql."<br>";
				$db->query($sql);
				$sql = "insert into shop_order_status(os_ix,oid,pid, status,status_message,regdate) values ('','".$accounts[$j][oid]."','".$accounts[$j][pid]."','".ORDER_STATUS_ACCOUNT_COMPLETE."','정산확인',NOW())";
				$db->sequences = "SHOP_ORDER_STATUS_SEQ";
				$db->query($sql);
			}
			//$db->fetch();

			$sql = "update  ".TBL_SHOP_ACCOUNTS." set status = '".ORDER_STATUS_ACCOUNT_COMPLETE."' WHERE ac_ix ='".$ac_ix."'  ";
			$db->query($sql);
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정산확인이 정상적으로 수행되었습니다.');parent.document.location.reload();</script>");
}

if($act == "account"){
//echo "<pre>";
//print_r ($_REQUEST);
	if($admininfo[admin_level] == 9){
		if(is_array($company_id)){
			$company_id_str = " and c.company_id in ('".join("','",$company_id)."') ";
		}else if($company_id != ""){
			$company_id_str = " and c.company_id = '".$company_id."' ";
		}

		if($admininfo[mem_type] == "MD"){
			$company_id_str .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}
	}else if($admininfo[admin_level] == 8){
		$company_id_str = " and c.company_id = '".$admininfo[company_id]."' ";
	}
		$account_priod_str = " and date_format(od.dc_date,'%Y%m%d') <= $VISITORDATE";

		/*$sql = "SELECT o.oid, p.admin as company_id ,count(od.pcnt) as sell_cnt, sum(od.ptprice) as sell_total_ptprice,sum(od.ptprice*(100-od.commission)/100) as		sell_total_coprice,
				sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, sum(od.delivery_price) as shipping_price, od.regdate as order_com_date,avg(od.commission) as avg_commission
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od, ".TBL_SHOP_ORDER." o
				where p.id = od.pid and o.oid = od.oid  and od.status in ('".ORDER_STATUS_DELIVERY_COMPLETE."') $company_id_str
				$account_priod_str  group by admin   "; //and o.status in ('DC')*/
		if($db->dbms_type == "oracle"){
			$sql = "SELECT o.oid, c.company_id as company_id ,
				count(od.pcnt) as sell_cnt, 
			
				sum(if(od.delivery_type = '2',od.delivery_price,'0')) as shipping_price,
				sum(if(od.account_type = '1' or od.account_type = '',od.ptprice,od.coprice)) as sell_total_ptprice,
				sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(100-od.commission)/100,od.coprice*(100-od.commission)/100)) as sell_total_coprice,
				sum(if(o.method = 1,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as card_ptprice,
				sum(if(o.method = 5 or o.method = 8 or o.method = 0 or o.method = 4 ,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as bank_ptprice,
				sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100)) as commission_price,

				sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt,
				od.regdate as order_com_date,
				avg(od.commission) as avg_commission
				FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid
				left join ".TBL_COMMON_COMPANY_DETAIL." c on od.company_id = c.company_id
				left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
				where od.status in ('".ORDER_STATUS_DELIVERY_COMPLETE."') $company_id_str
				$account_priod_str  group by c.company_id,o.oid,od.regdate  "; //and o.status in ('DC')
		}else{
			$sql = "SELECT o.oid,
				c.company_id as company_id ,
				count(od.pcnt) as sell_cnt, 
				sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt,

				sum(if(od.delivery_type = '2',od.delivery_price,'0')) as shipping_price,
				sum(if(od.account_type = '1' or od.account_type = '',od.ptprice,od.coprice)) as sell_total_ptprice,
				sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(100-od.commission)/100,od.coprice*(100-od.commission)/100)) as sell_total_coprice,
				sum(if(o.method = 1,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as card_ptprice,
				sum(if(o.method = 5 or o.method = 8 or o.method = 0 or o.method = 4 ,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as bank_ptprice,
				sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100)) as commission_price,

				od.regdate as order_com_date,
				avg(od.commission) as avg_commission
				FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid
				left join ".TBL_COMMON_COMPANY_DETAIL." c on od.company_id = c.company_id
				left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
				where od.status in ('".ORDER_STATUS_DELIVERY_COMPLETE."') and od.account_type != '3' $company_id_str
				$account_priod_str  group by od.company_id   "; //and o.status in ('DC')
		}

	
		$db->query($sql);

		$accounts = $db->fetchall();

		//print_r($accounts);
		//exit;
		//echo count($accounts);
		for($i=0;$i < count($accounts);$i++){
			$ac_price = $accounts[$i][shipping_price] + $accounts[$i][sell_total_ptprice] - $accounts[$i][commission_price];
			//echo $ac_price;
			//exit;
			// 위 정산정보를 정산테이블에 입력한다
			$sql = "insert into ".TBL_SHOP_ACCOUNTS." (ac_ix,company_id,ac_date,month, week, ac_cnt,ac_price,sell_total_price,sell_fee,card_fee,pre_shipping_cnt, shipping_fee,status, regdate,bank_ptprice,card_ptprice)
				values
				('','".$accounts[$i][company_id]."','".$eDate."','".substr($eDate,4,2)."','".$week."','".$accounts[$i][sell_cnt]."','".$ac_price."','".$accounts[$i][sell_total_ptprice]."','".$accounts[$i][commission_price]."','".$accounts[$i][card_fee]."','".$accounts[$i][pre_shipping_cnt]."','".$accounts[$i][shipping_price]."','".ORDER_STATUS_ACCOUNT_READY."',NOW(),'".$accounts[$i][bank_ptprice]."','".$accounts[$i][card_ptprice]."')";
	
			$db->sequences = "SHOP_ACCOUNTS_SEQ";
			$db->query($sql);

			$db->query("SELECT ac_ix FROM ".TBL_SHOP_ACCOUNTS." WHERE ac_ix=LAST_INSERT_ID() ");
			$db->fetch();
			$ac_ix = $db->dt[0];

			$db->sequences = "SHOP_CASH_INFO_SEQ";
			$db->query("insert into ".TBL_SHOP_CASH_INFO."(c_ix,company_id,ac_ix,cash,status,etc,regdate) values('','".$accounts[$i][company_id]."','$ac_ix','".($accounts[$i][sell_total_coprice])."','$status','시스템적립',NOW())");//+$accounts[$i][shipping_price]

			// 정산정보를 바탕으로 상품 상세 정보의 상태를 변경해준다
			/*$sql = "SELECT od.oid,od.pid, od.pname, od.reserve, ac_ix,pcnt, psprice, ptprice, od.option_text, po.option_etc1, od.status , od.coprice,od.commission,od.regdate
				FROM ".TBL_SHOP_ORDER_DETAIL." od left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po on od.option1 = po.id left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
				WHERE p.admin = '".$accounts[$i][company_id]."' and od.status = '".ORDER_STATUS_DELIVERY_COMPLETE."' $account_priod_str order by od.oid";*/
			$sql = "SELECT od.oid,od.pid, od.pname, od.reserve, ac_ix,pcnt, psprice, ptprice, od.option_text, po.option_etc1, od.status , od.coprice,od.commission,od.regdate
				FROM ".TBL_SHOP_ORDER_DETAIL." od left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po on od.option1 = po.id left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
				WHERE od.company_id = '".$accounts[$i][company_id]."' and od.status = '".ORDER_STATUS_DELIVERY_COMPLETE."' $account_priod_str order by od.oid";
			//echo $sql;
			//exit;
			$db->query($sql);
			$accounts_detail = $db->fetchall();
			//print_r($accounts_detail);
			$j=0;
			$b_oid = "";
			for($j=0;$j < count($accounts_detail);$j++){

				//$db->query("SELECT os_ix FROM ".TBL_SHOP_ORDER_STATUS." WHERE os_ix=LAST_INSERT_ID()");
				//$db->fetch();
				//$os_ix = $db->dt[0];
				$db->query("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET ac_ix= '$ac_ix', ac_date = '".$eDate."', status = '".ORDER_STATUS_ACCOUNT_READY."' $deliverycode_string WHERE oid='".$accounts_detail[$j][oid]."' and pid ='".$accounts_detail[$j][pid]."' ");

				$db->sequences = "SHOP_ORDER_STATUS_SEQ";
				$db->query("insert into ".TBL_SHOP_ORDER_STATUS."(os_ix,oid,pid, status,status_message, company_id, regdate) values('','".$accounts_detail[$j][oid]."','".$accounts_detail[$j][pid]."','".ORDER_STATUS_ACCOUNT_READY."','정산대기','".$admininfo[company_id]."', NOW())");
				//echo "j:".$j." ";
				if($b_oid != $accounts_detail[$j][oid]){

					$db->sequences = "SHOP_ORDER_STATUS_SEQ";
					$db->query("insert into ".TBL_SHOP_ORDER_STATUS."(os_ix,oid,pid, status,status_message, company_id, regdate) values('','".$accounts_detail[$j][oid]."','','".ORDER_STATUS_ACCOUNT_READY."','정산대기','".$admininfo[company_id]."', NOW())");

					if($db->dbms_type == "oracle"){
						$os_ix = $db->last_insert_id;
					}else{
						$db->query("SELECT os_ix FROM ".TBL_SHOP_ORDER_STATUS." WHERE os_ix=LAST_INSERT_ID()");
						$db->fetch();
						$os_ix = $db->dt[os_ix];
					}
				//	echo ("update ".TBL_SHOP_ORDER." set   status = 'AC', os_ix = '$os_ix'  where status = 'DC' and oid = '".$accounts_detail[$j][oid]."' ");
					$db->query("update ".TBL_SHOP_ORDER." set   status = '".ORDER_STATUS_ACCOUNT_READY."', os_ix = '$os_ix'  where status = '".ORDER_STATUS_DELIVERY_COMPLETE."' and oid = '".$accounts_detail[$j][oid]."' ");
					$b_oid = $accounts_detail[$j][oid];
				}

			}
			//exit;
			//
			//$db->query($sql);
			//echo $accounts[$i][account_total] .":::".$accounts[$i][tax_total];
		}
//	}else{
//		$db->query("update shop_accounts set taxbill_yn = 'Y' where ac_ix = '$f_ac_ix'");
//	}
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정산이 정상적으로 수행되었습니다.');parent.document.location.reload();</script>");

}

if($act == "select_accounts_update"){

//if($company_name != "") $where .= " and c.company_name LIKE '%$company_name%'";
	if($admininfo[mem_type] == "MD"){
		$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
	}
	for($i=0;$i < count($company_id);$i++){
	$sql = "SELECT c.company_name,p.admin as company_id,bank_name,bank_number,bank_owner ,sum(od.pcnt) as sell_cnt, sum(od.ptprice) as sell_total_ptprice,sum(od.coprice*od.pcnt) as sell_total_coprice,
			sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, sum(od.delivery_price) as shipping_price, od.regdate as order_com_date,avg(od.commission) as avg_commission, count(*) as order_cnt
			FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid
			left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
			left join ".TBL_COMMON_COMPANY_DETAIL." c on p.admin = c.company_id
			left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
			WHERE  od.status = '".ORDER_STATUS_DELIVERY_COMPLETE."' and od.company_id is not null and p.admin is not null
			and c.company_id = '".$company_id[$i]."' and date_format(od.dc_date,'%Y%m%d') <= $endDate   $addWhere
			group by admin " ;
			//".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_COMMON_SELLER_DETAIL." csd

	//echo $sql;
	$db->query($sql);
	}
}

if($act == "initialize"){
	if($admininfo[admin_level] == 9){
		for($i=0;$i < count($ac_ix);$i++){
			$db->query("SELECT * FROM ".TBL_SHOP_ORDER_DETAIL." WHERE ac_ix='".$ac_ix[$i]."' and status = '".ORDER_STATUS_ACCOUNT_COMPLETE."' ");

			for($j=0;$j < $db->total;$j++){
				$db->fetch($j);
				$sql = "UPDATE ".TBL_SHOP_ORDER_DETAIL." SET ac_ix= '', ac_date = '', status = '".ORDER_STATUS_DELIVERY_COMPLETE."' WHERE ac_ix ='".$ac_ix[$i]."' and od_ix = '".$db->dt[od_ix]."' ";
				//echo $sql."<br>";
				$udb->query($sql);
				$sql = "insert into shop_order_status(oid,pid, status,status_message,regdate) values ('".$db->dt[oid]."','".$db->dt[pid]."','".ORDER_STATUS_ACCOUNT_INITIALLIZE."','정산초기화',NOW())";
				$db->sequences = "SHOP_ORDER_STATUS_SEQ";
				$udb->query($sql);
			}
			//$db->fetch();

			$sql = "delete from ".TBL_SHOP_ACCOUNTS." WHERE ac_ix ='".$ac_ix[$i]."'  ";
			//echo $sql."<br>";
			$db->query($sql);
			$sql = "UPDATE ".TBL_SHOP_ORDER." SET status = '".ORDER_STATUS_DELIVERY_COMPLETE."' WHERE oid = '".$db->dt[oid]."' ";
			$db->query($sql);
		}

		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 정산이 초기화 되었습니다.');parent.document.location.reload();</script>";
	}
}
?>