<?
include("../class/layout.class");

$db = new Database;

if($act=="payment_update"){
	
	$sql="UPDATE shop_order_payment SET 
			".($pay_status=="IC" ? "ic_date=NOW()," : "")."
			pay_status='".$pay_status."',
			method='".$method."',
			bank='".$bank."',
			memo='".$memo."'
		WHERE opay_ix='".$opay_ix."'";
	$db->query($sql);

	if($pay_status=="IC"){
		
		if($payment_reserve=="Y"){//적립금 결제시!
			InsertReserveInfo($user_code,$oid,'','',$payment_price,'2','22',"추가비용 적립금결제",'mileage',$admininfo);
			
		
			/*신규 포인트,마일리지 접립 함수 JK 160405*/
			$mileage_data[uid] = $user_code;
			$mileage_data[type] = 1;
			$mileage_data[mileage] = $payment_price;
			$mileage_data[message] = '추가비용 적립금결제';
			$mileage_data[state_type] = 'add';
			$mileage_data[save_type] = 'mileage';
			$mileage_data[oid] = $oid;
			InsertMileageInfo($mileage_data);


		}

		$sql="SELECT * FROM shop_order_payment WHERE opay_ix='".$opay_ix."'";
		$db->query($sql);
		$db->fetch("object");
		$pay_info=$db->dt;

		/*
		$sql="SELECT * FROM shop_order_delivery WHERE oid='".$pay_info["oid"]."' and ori_company_id='".$pay_info["claim_group"]."' and delivery_policy='9' ";
		$db->query($sql);
		$db->fetch("object");
		$d_info=$db->dt;

		if($d_info["delivery_price"] > 0){
			$sql="UPDATE shop_order_delivery set delivery_dcprice='".$d_info["delivery_price"]."' WHERE ode_ix='".$d_info["ode_ix"]."'";
			$db->query($sql);
			table_order_price_data_creation($oid,"","",'A','D',0,$d_info["delivery_price"],"추가비용 결제",0,0,0);
		}

		$product_price=$pay_info["payment_price"]-$d_info["delivery_price"];
		*/
		
		$product_price=$pay_info["payment_price"];

		if($payment_reserve=="Y"){
			$reserve=$payment_price;
		}

		//예치금은 상품으로만!!
		table_order_price_data_creation($oid,"","",'A','P',0,$product_price,"추가비용 결제",$reserve,0,0);

	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>");
	exit;
}

?>