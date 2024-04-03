<?

//정산(정상)조건
//교환배송상품발송예정은 확정이 아니기 때문에 정산X 
/*
$AC_NORMARL_QUERY=" 
	(od.status != '".ORDER_STATUS_EXCHANGE_READY."' and (od.accounts_status not in ('".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') OR od.accounts_status is null) and (od.refund_status != '".ORDER_STATUS_REFUND_COMPLETE."' OR od.refund_status is null))
AND 
	case od.ac_delivery_type 
		when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date 
		when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date 
		when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date 
		when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date 
		when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date 
	else
		od.dc_date 
	end
		is not null ";
*/

//AND od.delivery_policy !='9' 2014-08-14 다이소에서 교환신청 상품만 정산태우고 환불 프로세스X
$AC_NORMARL_QUERY=" 
	od.status not in ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_EXCHANGE_READY."') 
AND
	od.ac_ix='0'
AND 
	case od.ac_delivery_type 
		when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date 
		when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date 
		when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date 
		when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date 
		when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date 
	else
		od.dc_date 
	end
		is not null
AND 
	od.delivery_policy !='9' ";

//정산(반품)조건
//$AC_REFUND_QUERY=" od.refund_ac_ix ='0' and od.accounts_status in ('".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') and (od.refund_status = '".ORDER_STATUS_REFUND_COMPLETE."' OR od.status='".ORDER_STATUS_EXCHANGE_COMPLETE."') ";

//2014-08-14 다이소에서 교환신청 상품만 정산태우고 환불 프로세스X, 반품시 추가결제 안타게끔 처리한다고함!
$AC_REFUND_QUERY=" 
	case od.ac_delivery_type 
		when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date 
		when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date 
		when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date 
		when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date 
		when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date 
	else
		od.dc_date 
	end
		is not null
AND 
	od.status not in ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_EXCHANGE_COMPLETE."')
AND
	od.refund_ac_ix ='0' 
AND
	od.refund_status = '".ORDER_STATUS_REFUND_COMPLETE."' ";


function shop_accounts_insert($info){

	$adb = new Database;
	
	$sql="insert into shop_accounts(ac_ix,company_id,ac_date,ac_title,ac_info,ac_type,account_method,surtax_yorn,p_sell_price,p_expect_price,p_fee_price,p_dc_allotment_price,p_ac_price,d_sell_price,d_expect_price,d_dc_allotment_price,d_ac_price,ac_price,status,regdate) values('','".$info["company_id"]."','".$info["ac_date"]."','".$info["ac_title"]."','".$info["ac_info"]."','".$info["ac_type"]."','".$info["account_method"]."','".$info["surtax_yorn"]."','".$info["p_sell_price"]."','".$info["p_expect_price"]."','".$info["p_fee_price"]."','".$info["p_dc_allotment_price"]."','".$info["p_ac_price"]."','".$info["d_sell_price"]."','".$info["d_expect_price"]."','".$info["d_dc_allotment_price"]."','".$info["d_ac_price"]."','".$info["ac_price"]."','".$info["status"]."',NOW())";
	$adb->query($sql);

	$sql="select ac_ix from shop_accounts where ac_ix = LAST_INSERT_ID() ";
	$adb->query($sql);
	$adb->fetch();

	return $adb->dt[ac_ix];
}



?>