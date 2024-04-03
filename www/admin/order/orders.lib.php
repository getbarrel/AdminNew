<?
function returnDeliveryPrice($oid,$company_id,$order) {
	$mdb=new Database;
	
	$sql="SELECT product_price FROM shop_order_price WHERE oid='".$oid."' AND payment_status='G' ";
	$mdb->query($sql);
	$mdb->fetch();
	$payment_price=$mdb->dt["product_price"];

	if($payment_price>0) {
		$sql = "SELECT distinct delivery_price, delivery_pay_type FROM shop_order_delivery WHERE oid='".$oid."' and order_delivery_policy = '2'  ";
		//echo $sql;
		//$mdb->debug = true;
		$mdb->query($sql);
		$company_info = $mdb->fetchall();
		//$mdb->debug=true;
		
		
		for($j=0;$j < count($company_info);$j++) {
			$delivery_price=$company_info[$j][delivery_price];
			$delivery_pay_type=$company_info[$j][delivery_pay_type];

		}
	}

}


function get_order_user_info($ucode){

	$vdate = date("Ymd", time());
	$v30ago = date("Y-m-d", time()+86400*30);

	$db = new Database;

	$sql = "SELECT
		cu.last as user_last,ifnull((select count(*) as cnt from shop_order where user_code='".$ucode."' and date_format(date,'%Y%m%d') between '".$v30ago."' and '".$vdate."' ),0) as user_order_cnt
	FROM common_user cu where cu.code='".$ucode."'";
	$db->query($sql);

	return $db->fetch();
}

function getOrderRecipientInfo($data){
	
	$mdb = new Database;
	
	$return["recipient"]="";
	$return["recipient_str"]="";
	$return["recipient_height"]=0;
	$return["recipient_width"]=230;
	
	$sql="SELECT odd.* FROM shop_order_detail_deliveryinfo odd , shop_order_detail od where odd.odd_ix = od.odd_ix and od.oid = '".$data[oid]."' group by odd.odd_ix ";
	$mdb->query($sql);
	if($mdb->total){
		$deliveryinfo = $mdb->fetchall();
		foreach($deliveryinfo as $key => $val){
			if($key==0)		$return["recipient"]=$val[rname];
			else				$return["recipient_str"] .= "<br/>-----------------------<br/>";
			
			$return["recipient_str"].= "수신자 <br/>성명 : ".wel_masking_seLen($val[rname], 1, 1)."<br/>연락처 : ".$val[rtel]."<br/>핸드폰 : ".$val[rmobile]."<br/>이메일 : ".$val[rmail];
			$return["recipient_height"]+=90;
		}
	}

	if(count($deliveryinfo) > 1 )	$return["recipient"] = $return["recipient"]." 외 ".(count($deliveryinfo)-1);

	return $return;
}

function getOrderMethodInfo($data){
	global $currency_display,$admin_config;
	$mdb = new Database;
	

	$return["method_str"]="";
	$return["method_height"]=0;
	$return["method_width"]=160;
	$return["method_pay_info"]="";
	$return["total_pay_price"]=0;
	$return["total_real_pay_price"]=0;//적립금,포인트 제외한금액
	$return["receipt"]="";

	if($data[user_id]!=''){
		
		$sql="SELECT bank_ix, ucode, bank_code, use_yn, is_basic, regdate, editdate,
					AES_DECRYPT(UNHEX(bank_name),'".$mdb->ase_encrypt_key."') as bank_name,
					AES_DECRYPT(UNHEX(bank_number),'".$mdb->ase_encrypt_key."') as bank_number,
					AES_DECRYPT(UNHEX(bank_owner),'".$mdb->ase_encrypt_key."') as bank_owner 
				FROM shop_user_bankinfo b WHERE b.ucode='".$data[user_id]."' and b.use_yn='Y' and b.is_basic='1' ";
		$mdb->query($sql);

		if($mdb->total){
			$mdb->fetch();
			$return['refund_bank_code'] = $mdb->dt["bank_code"];
			$return['refund_bank_number'] = $mdb->dt["bank_number"];
			$return['refund_bank_owner'] = $mdb->dt["bank_owner"];
		}else{
			$return['refund_bank_code'] = "NO_REFUND_DATA";
			$return['refund_bank_number'] = "";
			$return['refund_bank_owner'] = "";
		}
	}else{
		list($return['refund_bank_code'],$return['refund_bank_number1']) = explode("|",$data["refund_bank1"]);
		$return['refund_bank_owner'] = $data["refund_bank_name1"];
		if(empty($return['refund_bank_code'])){
			$return['refund_bank_code'] = "NO_REFUND_DATA";
		}
	}

	$card_yn = false;
	$receipt_yn = false;
	$taxsheet_yn = false;

    $currency_unit = check_currency_unit($data['mall_ix']);
    if($currency_unit == 'USD'){
        $decimals_value = 2;
    }else{
        $decimals_value = 0;
    }
	$sql="SELECT * FROM shop_order_payment WHERE oid='".$data[oid]."' and pay_type ='G' ";
	$mdb->query($sql);
	if($mdb->total){
		$order_payment = $mdb->fetchall();
		foreach($order_payment as $key => $val){
			if($key!=0){
				$return["method_str"] .= "<br/>";
				$return["method"] .="|";
			}
			$return["method"] .= $val[method];
			$return["method_str"] .= getMethodStatus($val[method])." : ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($val[payment_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."";
			$return["method_height"]+=15;
			
			$return["method_pay_info"] .= "<br/>".getMethodStatus($val[method])." : ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($val[payment_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."";

			if($val[method] == ORDER_METHOD_BANK){
				$tmp_str = "<br/> 임금계좌 : ".$val[bank]." 입금자명 : ".$val[bank_input_name];
				$return["method_pay_info"] .= $tmp_str;
				$return["method_str"] .= $tmp_str;
				$return["method_height"]+=30;
			}elseif($val[method] == ORDER_METHOD_VBANK || $val[method] == ORDER_METHOD_ASCROW){
				//$tmp_str = "<br/> 가상계좌정보 : ".$val[vb_info] ." 입금일 : ".$val[bank_input_date];
				$tmp_str = "<br/> 입금일 : ".$val[bank_input_date];
				$return["method_pay_info"] .= $tmp_str;
				if($val['receipt_yn'] == 'Y'){
					$receipt_code = '';
					if($val['receipt_code'] == '0'){
						$receipt_code = "소득공제용(개인)";
					}else if($val['receipt_code'] == '1'){
						$receipt_code = "지출증빙용(기업)";
					}
					if($receipt_code != ''){
						$return["method_pay_info"] .= "<br/>".$receipt_code." / ".$val['receipt_info'];
					}
				}
				$return["method_str"] .= $tmp_str;
				if($return["method_width"] < 270){
					$return["method_width"]=270;
				}
				$return["method_height"]+=30;
			}elseif($val[method] == ORDER_METHOD_CARD){
				//$tmp_str = "<br/> 카드 : ".$val[card_info];
				$tmp_str = "<br/> ".$val[memo]." : ".$val[card_info];
				$return["method_pay_info"] .= $tmp_str;
				$return["method_str"] .= $tmp_str;
				$return["method_height"]+=15;
			}elseif($val[method] == ORDER_METHOD_CART_COUPON){
				$tmp_str = "<br/> ".str_replace("|","<br/>",$val[memo]);
				$return["method_pay_info"] .= $tmp_str;
				$return["method_str"] .= $tmp_str;
				if($return["method_width"] < 270){
					$return["method_width"]=270;
				}
				$return["method_height"]+=40;
			}elseif($val[method] == ORDER_METHOD_DELIVERY_COUPON){
				$tmp_str = "<br/> ".str_replace("|","<br/>",$val[memo]);
				$return["method_pay_info"] .= $tmp_str;
				$return["method_str"] .= $tmp_str;
				if($return["method_width"] < 270){
					$return["method_width"]=270;
				}
				$return["method_height"]+=40;
			}

			if($val["pay_status"]=="IC"){
				if($val["pay_type"]=="F"){
					$return["total_pay_price"]-=$val["payment_price"];
					if($val[method] != ORDER_METHOD_RESERVE && $val[method] != ORDER_METHOD_SAVEPRICE && $val[method] != ORDER_METHOD_CART_COUPON && $val[method] != ORDER_METHOD_DELIVERY_COUPON){
						$return["total_real_pay_price"]-=$val["payment_price"];
					}
				}else{
					$return["total_pay_price"]+=$val["payment_price"];
					if($val[method] != ORDER_METHOD_RESERVE && $val[method] != ORDER_METHOD_SAVEPRICE && $val[method] != ORDER_METHOD_CART_COUPON && $val[method] != ORDER_METHOD_DELIVERY_COUPON){
						$return["total_real_pay_price"]+=$val["payment_price"];
					}
				}
			}
			
			if($return["method"]!=ORDER_METHOD_RESERVE){
				if($val["method"]==ORDER_METHOD_CARD){
					$card_yn = true;
					//$tmp_receipt[] ="카드"; //<img src='../images/icon/receipt.gif' style='cursor:pointer' align='absmiddle' onclick=\"".getReceipt($val)."\"/>
					$tmp_receipt[] =$val["memo"]; //<img src='../images/icon/receipt.gif' style='cursor:pointer' align='absmiddle' onclick=\"".getReceipt($val)."\"/>
				}elseif($val["receipt_yn"]=="Y"){
					$receipt_yn = true;
				}else if($val["taxsheet_yn"]=="Y"){
					$taxsheet_yn = true;
				}
			}
		}

		if($receipt_yn){
			//$sql="SELECT r.* FROM receipt r left join receipt_result rr on (r.order_no=rr.oid) WHERE r.order_no='".$data[oid]."' order by regdate desc limit 0,1";
			$sql="SELECT r.* FROM receipt r WHERE r.order_no='".$data[oid]."' limit 0,1";
			$mdb->query($sql);
			$mdb->fetch();
			if($mdb->dt[m_useopt]=="1")		$receipt_name="지출증빙";
			else							$receipt_name="소득공제";
			$tmp_receipt[] = $receipt_name;//<img src='../images/icon/receipt.gif' style='cursor:pointer' align='absmiddle' onclick=\"".getReceipt($val)."\"/> 
		}
		
		if($taxsheet_yn){
			$tmp_receipt[] = "세금&계산서";
		}

		if(!$card_yn && !$receipt_yn && !$taxsheet_yn){
			$tmp_receipt[] = "미발급";
		}
	}
	
	if(count($tmp_receipt) > 0){
		$return["receipt"] = implode(",",$tmp_receipt);
	}

	return $return;
}


function getPgReceipt($data,$return="popup"){

	if($return=="popup"){
		if($data[settle_module]=="inicis" || $data[settle_module]=="m_inicis"){
			if($data["method"]==ORDER_METHOD_CARD || $data["method"]==ORDER_METHOD_ICHE){
				$return_data = "<input type='button' value='PG영수증' onclick=\"window.open('https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid=".$data[tid]."&noMethod=1','showreceipt','width=430,height=700');\" style='text-align:right;' />";
			}
		}
	}

	return $return_data;
}

function getReceipt($data,$return="popup"){

	if($data["receipt_yn"]=="Y"){
		if($data[settle_module]=="inicis" || $data[settle_module]=="m_inicis"){
			$return_data = "<input type='button' value='현금영수증' onclick=\"window.open('https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid=".$data["m_tid"]."&clpaymethod=22','showreceipt','width=380,height=540');\" style='text-align:right;' />";
		}
	}elseif($data["taxsheet_yn"]=="Y"){
		if($data["tax_ix"]!="0"){
			$return_data = "<input type='button' value='세금계산서영수증' onclick=\"window.open('/admin/tax/popbill_pop.php?act=print_url&com_number=".$_SESSION['shopcfg']['biz_no']."&ix=".$data["tax_ix"]."&userid=".$_SESSION['layout_config']['popbill_id']."', 'print_url', 'width=785,height=800,resizeble=yes');\" style='text-align:right;' />";
		}elseif($data["bill_ix"]!="0"){
			$return_data = "<input type='button' value='계산서영수증' onclick=\"window.open('/admin/tax/popbill_pop.php?act=print_url&com_number=".$_SESSION['shopcfg']['biz_no']."&ix=".$data["bill_ix"]."&userid=".$_SESSION['layout_config']['popbill_id']."', 'print_url', 'width=785,height=800,resizeble=yes');\" style='text-align:right;' />";
		}else{
			$return_data = "<input type='button' value='세금계산서영수증' onclick=\"alert('발급대기중입니다.');\" style='text-align:right;' />";
		}
	}

	return $return_data;
}

function getOrderDetailCouponDcInfo($data){
	global $_DISCOUNT_TYPE;
	//dc_type 할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)

	if(is_array($data)){
		$return["coupon_str"]="";
		$return["coupon_height"]=0;
		$return["coupon_width"]=300;
		$return["coupon_total_dc_price"]=0;

		$i=0;
		foreach($data as $dc){
			if($dc[dc_type]=="CP" || $dc[dc_type]=="SCP" ){
				if($i!=0) $return["coupon_str"] .= "-----------------------------------------------<br/>";
				$return["coupon_str"] .= $_DISCOUNT_TYPE[$dc[dc_type]]." : ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($dc[dc_price])."".$currency_display[$admin_config["currency_unit"]]["back"]."<br/>".str_replace("|","<br/>",$dc[dc_msg])."<br/>";
				$return["coupon_height"]+=70;
				$i++;

				$return["coupon_total_dc_price"]+=$dc[dc_price];
			}
		}
	}
	return $return;
}

function getOrderDetailEtcDcInfo($data){
	global $_DISCOUNT_TYPE;
	//dc_type 할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)

	if(is_array($data)){
		$return["etc_str"]="";
		$return["etc_height"]=0;
		$return["etc_width"]=200;
		
		foreach($data as $dc){
			if($dc[dc_type]!="CP" && $dc[dc_type]!="SCP" && $dc[dc_type]!="DCP"){
				$return["etc_str"] .= $_DISCOUNT_TYPE[$dc[dc_type]]." : ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($dc[dc_price])."".$currency_display[$admin_config["currency_unit"]]["back"]."<br/>";
				$return["etc_height"]+=15;
			}
		}
	}
	return $return;
}


function getOrderExchangeRatePaymentPrice($data){
	$mdb = new Database;

	$sql="SELECT exchange_rate_payment_price FROM shop_order_payment WHERE oid='".$data[oid]."' and pay_type ='G' and method='".ORDER_METHOD_PAYPAL."' ";
	$mdb->query($sql);
	if($mdb->total){
		$mdb->fetch();
		return $mdb->dt['exchange_rate_payment_price'];
	}else{
		return 0;
	}
}

?>
