<?
include("../class/layout.class");

$db = new Database;
$db2 = new Database;
$db3 = new Database;
$db4 = new Database;
$tdb = new Database;

$CartBool = false;
session_register("CartBool");

/****************** 입력정보 기존소스활용하기위하여 기존꺼 사용 - 대신 세션에는 안담아도됨... 한페이지에서 다처리함  ****************************/

if($ucode != ""){
$sql = "SELECT cmd.name, cu.id, g.gp_name 
			FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd,".TBL_SHOP_GROUPINFO." g 
			WHERE cmd.gp_ix = g.gp_ix and cu.code = cmd.code and cu.code = '$ucode'";
$db->query($sql);
$db->fetch();
}
if($mall_ix == ""){
	$mall_ix = "d02b37324dd0b08f6bc0f3847673e7d5";
}else{
	$mall_ix = $mall_ix;
}

$order[mall_ix] = $mall_ix;
$order[type] = $type;
$order[name] = $db->dt[name];
$order[gp_name] = $db->dt[gp_name];
$order[id] = $db->dt[id];
$order[code] = $ucode;
$order[cart_key] = $cart_key;
$order[name_a] = $name_a;
$order[tel1_a] = $tel1_a;
$order[tel2_a] = $tel2_a;
$order[tel3_a] = $tel3_a;
$order[mail_a] = $mail_a;
$order[name_b] = $name_b;
$order[tel1_b] = $tel1_b;
$order[tel2_b] = $tel2_b;
$order[tel3_b] = $tel3_b;
$order[pcs1_a] = $pcs1_a;
$order[pcs2_a] = $pcs2_a;
$order[pcs3_a] = $pcs3_a;
$order[pcs1_b] = $pcs1_b;
$order[pcs2_b] = $pcs2_b;
$order[pcs3_b] = $pcs3_b;
$order[mail_b] = $mail_b;
$order[zipcode1] = $zipcode1;
$order[zipcode2] = $zipcode2;
$order[zipcode1_b] = $zipcode1_b;
$order[zipcode2_b] = $zipcode2_b;
$order[addr1] = $addr1;
$order[addr2] = $addr2;
$order[addr1_b] = $addr1_b;
$order[addr2_b] = $addr2_b;

$order[pay_method] = $payment_div;
$order[bank] = $bank;
$order[bank_input_name] = $input_name;
$order[msg1] = $msg1;
$order[msg2] = $msg2;
$order[carttype] = $carttype;
$order[reserve_price] = $reserve_price;
$order[recomm_phone] = $recomm_phone;
$order[recomm_name] = $recomm_name;

$order[delivery_total_price] = $delivery_total_price;
//새로추가한거

$order[paytoreserve] = $paytoreserve;
$order[gift_id] = $gift_id;


	if($cupon_no && !$baymoney_price){
		$order[cupon_regist_ix] = $cupon_regist_ix;
		$order[use_cupon_price] = str_replace(",","",$use_cupon_price);
		//$order[use_cupon_code] = $use_cupon_code;
		$order[use_cupon_code] = $cupon_no;
		$order[use_cupon_detail_price] = $cupon_detail_price;
	}else{
		$order[cupon_regist_ix] ="";
		$order[use_cupon_price] = 0;
		$order[use_cupon_code] = "";
		$order[use_cupon_detail_price] = "";
	}
	
	if($choice_sale == "1"){
		$order[paytobaymoney] = "";
		$order[baymoney_price] = "";
	}else if($choice_sale == "2"){
		$order[cupon_regist_ix] ="";
		$order[use_cupon_price] = 0;
		$order[use_cupon_code] = "";
		$order[use_cupon_detail_price] = "";
		$order[reserve_price];
	}
//print_r($order);

if($payment_div == "after_bank"){
	if($receipt_type == "1"){
		$order[taxsheet_yn] = "Y";
		$order[receipt_y] = "N";
		$order[sc_name] = $sc_name;
		$order[sc_number] = $sc_number;
		$order[sc_ceo] = $sc_ceo;
		$order[sc_zip] = $sc_zip1."-".$sc_zip2;
		$order[sc_addr] = $sc_addr1." ".$sc_addr2;
		$order[sc_tel] = $sc_tel1."-".$sc_tel2."-".$sc_tel3;
		$order[sc_pcs] = $sc_pcs1."-".$sc_pcs2."-".$sc_pcs3;
		$order[sc_mail] = $sc_mail;
		$order[sc_damdang] = $sc_damdang;
		$order[receipt_type] = $receipt_type;
	}else if($receipt_type == "2"){
		$order[receipt_y] = "Y";
		$order[taxsheet_yn] = "N";
		$order[sc_name] = $sc_name;
		$order[sc_number] = $sc_number;
		$order[receipt_type] = $receipt_type;
	}
}else{
	$order[taxsheet_yn] = "N";
	$order[receipt_y] = "N";
}

if($order[gift_id] != ""){
	$db->query("select * from ".TBL_SHOP_PRODUCT." where id = '".$order[gift_id]."' ");
	$db->fetch();
	if($db->total){
		$db->fetch();
		$pname = str_replace("\"","&quot;",$db->dt[pname]);
		$pname = str_replace("'","&#39;",$pname);

		$brand_name = str_replace("\"","&quot;",$db2->dt[brand_name]);
		$brand_name = str_replace("'","&#39;",$brand_name);

		$sql = "select commission, company_name,quick from common_company_detail where company_id = '".$db->dt[admin]."'";
		$db3->query($sql);
		$db3->fetch();
		$company_name = $db3->dt[company_name];
		$sql = "insert into shop_cart (cart_ix,cart_key,cid,product_type, pname,mem_ix,pcount,sellprice,option_price,id,reserve,options,coprice,pcode,select_option_id,totalprice,option_yn,
					options_text,company_id,company_name,brand,brand_name,stock,delivery_company,one_commission,commission,free_delivery_yn,free_delivery_count,quick,surtax_yorn,stock_use_yn,hotcon_event_id, hotcon_pcode,warehouse_pcode,barcode,  regdate) 
					values 
					('','".$order[cart_key]."','','6','".$pname."','".$order[code]."','1','0','0','".$order[gift_id]."','0','','".$db->dt[coprice]."',
					'".$db->dt[pcode]."','".$db->dt[select_option_id]."','0','','','".$db->dt[admin]."','".$company_name."','".$db->dt[brand]."','".$brand_name."','".$db->dt[stock]."','".$db->dt[delivery_company]."','".$db->dt[one_commission]."','','','','".$db3->dt[quick]."','".$db->dt[surtax_yorn]."','".$db->dt[stock_use_yn]."','".$db->dt[hotcon_event_id]."','".$db->dt[hotcon_pcode]."','".$db->dt[warehouse_pcode]."','".$db->dt[barcode]."', NOW())";
		$db3->query($sql);
	}
}
$order[oid] = date("YmdHi")."-".rand(1000, 9999);//date("YmdHi"."-".rand(1000, 9999));

	$where = " where cart_key = '".$order[cart_key]."' ";
	$group = " group by cart_key";



$sql = "select sum(totalprice) as cart_totalprice from shop_cart $where and delivery_company in ('WE','MI','') $groupby";
;
$db->query($sql);
$db->fetch();

$cart_totalprice = $db->dt[cart_totalprice];
$order[product_total_price] = $cart_totalprice;
$sql = "select region_name_text,region_name_price from shop_region_delivery ";
$db->query($sql);

for($i=0;$i<$db->total;$i++){
	$db->fetch($i);

	$region_name_text = explode(",",$db->dt[region_name_text]);
	//print_r($region_name_text);
	//exit;
	for($j=0;$j<count($region_name_text);$j++){
		
		if(substr_count($addr1_b,$region_name_text[$j]) > 0){
			$region_delivery = $db->dt[region_name_price];
		}
	}
	//echo $region_delivery;
	//exit;
}
$order[delivery] = $order[delivery] + $region_delivery;


$db3->query("select oid from ".TBL_SHOP_ORDER." where oid = '".$order[oid]."'");
if (!$db3->total){	
	
	if($CartBool){
		$CartBool = false;
		session_register("CartBool");
	?>
		<script language="javascript">
			alert("주문이 이미 완료 되었습니다. 감사합니다.");
			location.href = "/shop/cart.php";
		</script>
	<?
		exit;	
	}else{

		$db->query("select * from shop_cart $where order by company_id desc");

		if ($order[name]){
			$oname = $order[name];
		}else{
			$oname = "Guest";
		}
		
		//$oid    = date("YmdHi")."-".rand(1000, 9999);
		$oid      = $order[oid];
		$uid    = $order[code];
		$bname  = str_replace(' ','',$order[name_a]);
		$btel   = $order[tel1_a]."-".$order[tel2_a]."-".$order[tel3_a];
		$bmobile = $order[pcs1_a]."-".$order[pcs2_a]."-".$order[pcs3_a];
		$bmail  = $order[mail_a];
		$rname  = str_replace(' ','',$order[name_b]);
		$rtel   = $order[tel1_b]."-".$order[tel2_b]."-".$order[tel3_b];
		$rmobile = $order[pcs1_b]."-".$order[pcs2_b]."-".$order[pcs3_b];
		$rmail  = $order[mail_b];
		$rzip    = $order[zipcode1_b]."-".$order[zipcode2_b];
		$raddr   = $order[addr1_b].$order[addr2_b];
		$msg1    = trim($order[msg1]);
		$bank   = $order[bank];
		$mall_ix   = $order[mall_ix];
		$reserve_price = $order[reserve_price];
		$paytoreserve = $order[paytoreserve];
		$bank_input_name = $order[bank_input_name];
		$status = ORDER_STATUS_INCOM_READY;
		//echo $db->total;
		//exit;
		
		for($i=0;$i<$db->total;$i++){
			
			$db->fetch($i);
			
			if(true){				
				$pid     = $db->dt[id];	//$id;				
				$product_type = $db->dt[product_type];		
				$pcode = $db->dt[pcode];				
				$pname = $db->dt[pname];	
				$hotcon_event_id = $db->dt[hotcon_event_id];	
				$hotcon_pcode = $db->dt[hotcon_pcode];	
				$warehouse_pcode = $db->dt[warehouse_pcode];	
				$barcode = $db->dt[barcode];	
				$pname  = str_replace("'","''",$db->dt[pname]);
				$count    = $db->dt[pcount];
				$psprice = $db->dt[sellprice];
				$option_price = $db->dt[option_price];
				$coprice = $db->dt[coprice];
				$ptprice = $psprice * $count;
				$options = unserialize(urldecode($db->dt[options]));
				$select_option_text = $db->dt[options_text];
				$select_option_id = $db->dt[select_option_id];
				$option_price = $db->dt[option_price];
				$option_coprice = $db->dt[option_coprice];
				$madeorder_text = $db->dt[madeorder_text];
				$company_id = $db->dt[company_id];
				$company_name = $db->dt[company_name];
				$delivery_company = $db->dt[delivery_company];
				$one_commission = $db->dt[one_commission];
				$commission = $db->dt[commission];
				$surtax_yorn = $db->dt[surtax_yorn];
				$inventory_info = $db->dt[inventory_info];
				$cid = $db->dt[cid];
				//$delivery_price = $db->dt[delivery_price];
				//$company_deliveryprice = $db->dt[company_deliveryprice];
				$reserve = $db->dt[reserve];
				$admin = $db->dt[company_id];
				$stock_use_yn = $db->dt[stock_use_yn];
				$sum += $ptprice;
				
				$pname = str_replace("\"","&quot;",$pname);
				$pname = str_replace("'","&#39;",$pname);

				$db2->query("SELECT company FROM ".TBL_SHOP_PRODUCT." WHERE id='".$pid."' ");
				$db2->fetch();
				$company = $db2->dt[company];			
				
				$select_option_text = str_replace("\"","&quot;",$select_option_text);
				$select_option_text = str_replace("'","&#39;",$select_option_text);
				if($bcompany_id != $company_id){
						$db2->query("insert into ".TBL_SHOP_ORDER_DELIVERY." (ode_ix , oid,company_id,company_total,delivery_price,delivery_pay_type,regdate) values ('','$oid','$company_id',(select count(*) from shop_cart $where and company_id = '".$company_id."' group by company_id),'".getDeliveryPrice($company_id,$delivery_company,0,$order[cart_key])."',(select if(delivery_policy = 1,(select delivery_policy from common_company_detail where admin_level = 9),delivery_basic_policy) from common_company_detail where company_id = '".$company_id."'),NOW())");
				}
				
				if($reserve_price < $ptprice){
					// 새로 바뀔 테이블
					$sql = "insert into ".TBL_SHOP_ORDER_DETAIL."
									(mall_ix,od_ix,oid,pid,pcode, product_type, pname,option1,option_text,option_price,option_coprice, madeorder_text, pcnt,coprice,psprice,ptprice,reserve,use_coupon,use_coupon_code, company_id,company_name, purchase_name,one_commission,commission,surtax_yorn,stock_use_yn,hotcon_event_id, hotcon_pcode,warehouse_pcode,barcode, regdate) 
									values
									('".$mall_ix."','','$oid','$pid','$pcode','$product_type','$pname','$select_option_id','$select_option_text','$option_price','$option_coprice','".$madeorder_text."','$count','$coprice','$psprice','$ptprice','$reserve','".$order[use_cupon_detail_price][$pid]."','".$order[use_cupon_code][$pid]."','$company_id','$company_name','$company','$one_commission','$commission','$surtax_yorn','$stock_use_yn','$hotcon_event_id', '$hotcon_pcode', '$warehouse_pcode', '$barcode',NOW())"; 
					$db2->query($sql);
										
					// 공통으로 사용
					$reserve_price = "";
					if($stock_use_yn == "Y"){
						$db2->query("update ".TBL_SHOP_PRODUCT." set sell_ing_cnt= sell_ing_cnt + ".$count.", order_cnt = order_cnt + ".$count." where id ='$pid'");
					}else{
						$db2->query("update ".TBL_SHOP_PRODUCT." set order_cnt = order_cnt + ".$count." where id ='$pid'");
					}
				}else{
					// 새로 바뀔 테이블
					$sql = "insert into ".TBL_SHOP_ORDER_DETAIL."
									(mall_ix,od_ix,oid,pid,pcode, product_type, pname,option1,option_text, option_price,option_coprice,madeorder_text,pcnt,coprice,psprice,ptprice,reserve,use_coupon,use_coupon_code, company_id,company_name, purchase_name,one_commission,commission,surtax_yorn,stock_use_yn, hotcon_event_id, hotcon_pcode,warehouse_pcode,barcode,regdate) 
									values
									('".$mall_ix."','','$oid','$pid','$pcode','$product_type','$pname','$select_option_id','$select_option_text','$option_price','$option_coprice','$madeorder_text','$count','$coprice','$psprice','$ptprice','$reserve','".$order[use_cupon_detail_price][$pid]."','".$order[use_cupon_code][$pid]."','$company_id','$company_name','$company','$one_commission','$commission','$surtax_yorn','$stock_use_yn','$hotcon_event_id', '$hotcon_pcode', '$warehouse_pcode', '$barcode', NOW())"; 					
					$db2->query($sql);
					
					$reserve_price = $reserve_price-$ptprice;
					if($stock_use_yn == "Y"){
						$db2->query("update ".TBL_SHOP_PRODUCT." set sell_ing_cnt= sell_ing_cnt + ".$count.", order_cnt = order_cnt + ".$count." where id ='$pid'");
					}else{
						$db2->query("update ".TBL_SHOP_PRODUCT." set order_cnt = order_cnt + ".$count." where id ='$pid'");
					}
				}
				if($reserve > 0){ 					
					$db2->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','$oid','$pid','$ptprice','".($ptprice-$reserve_price-$saveprice-$mem_sale_price)."','".($reserve*$count)."','0','".$pname." 구매시 적립금액 ',NOW())");
				}
				if($stock_use_yn == "Y"){
					$db2->query("update ".TBL_SHOP_PRODUCT_OPTION." set option_sell_ing_cnt = option_sell_ing_cnt + ".$count." where pid = '$pid' and id ='$select_option_id' ");
					
					
				
					$db2->query("SELECT option_sell_ing_cnt, option_stock , option_safestock FROM ".TBL_SHOP_PRODUCT_OPTION." WHERE pid = '$pid' and id ='$select_option_id' ");
					if($db2->total){
						$db2->fetch();
						
						if(($db2->dt[option_stock] - $db2->dt[option_sell_ing_cnt]) == 0){
							$db2->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'N' where id ='$pid'");
						}else if($db2->dt[option_stock] < $db2->dt[option_safestock]){
							$db2->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'R' where id ='$pid'");
						}
					}
				}
				
				if($order[use_cupon_code][$pid]){
					//echo("update ".TBL_SHOP_CUPON_REGIST." set use_oid = '".$oid."', use_pid = '".$pid."', use_yn = 1, usedate = NOW() where regist_ix = '".$order[use_cupon_code][$pid]."'");
					$db2->query("update ".TBL_SHOP_CUPON_REGIST." set use_oid = '".$oid."', use_pid = '".$pid."', use_yn = 1, usedate = NOW() where regist_ix = '".$order[use_cupon_code][$pid]."'");
				}
				if($order["code"] != ""){
					$output_owner = $order[name]."(".$order[id].")";
				}else{
					$output_owner = $bname;
				}
				
				if($_SESSION["layout_config"]["mall_use_inventory"] == "Y"){
				// 배송중 처리시 재고 차감 프로세스로 변경 그 이전상태에서는 sell_ing_cnt 값을 가지고 처리
				/*
					if($select_option_id){
						for($j=0;$j<count($options);$j++){
							$sql = "insert into inventory_output_history(pid,pname,oid,output_msg,output_saler,output_status,option_id,option_text,inventory_info,output_totalsize,output_owner,date) values ('".$pid."','".$pname."','".$oid."','상품판매','1','1','".$options[$j]."','".strip_tags($select_option_text)."','".$inventory_info."','".$count."','".$output_owner."',NOW())";
							$db4->query($sql);
						}
					}else{
						$sql = "insert into inventory_output_history(pid,pname,oid,output_msg,output_saler,output_status,option_id,option_text,inventory_info,output_totalsize,output_owner,date) values ('".$pid."','".$pname."','".$oid."','상품판매','1','1','".$options."','".strip_tags($select_option_text)."','".$inventory_info."','".$count."','".$output_owner."',NOW())";
						$db4->query($sql);
					}*/
				}
				$bcompany_id = $company_id;
				//$bl->CommerceLogic($uid,6,$cid, $pid,$count,$psprice);
				
				unset($pid);
				unset($pname);
				unset($count);
				unset($psprice);
				unset($classname);
				unset($reserve);
			
				unset($ptprice);
				unset($saveprice);
				unset($coprice);
				unset($mem_sale_price);
				unset($company_id);
				
			}else{
				$pid     = $key;
				$pname   = $value[0];
				$count    = $value[1];
				$psprice = $value[2];
				$classname = $value[3];
				$reserve = $value[4];			
			
				$option = $value[6];
				$coprice = $value[7];
				$cid = $value[8];
				$saveprice = $value[9];
				$recomm_reserve = $value[10];
				$mem_sale_price = $value[11];
				
				$ptprice = $psprice * $count;
				
				$CART2[$pid] = array($pname, $count, $psprice,$pid,$reserve,$option,$coprice,$cid,$saveprice,$recomm_reserve,$mem_sale_price);
				
				unset($pid);
				unset($pname);
				unset($count);
				unset($psprice);
				unset($classname);
				unset($reserve);
				
				unset($ptprice);
				unset($option);
				unset($saveprice);
				unset($recomm_reserve);
				unset($cid);
				unset($coprice);
				unset($mem_sale_price);
				unset($company_id);
			}
			
		}
		if($order[gp_name] != ""){
			$mem_group = $order[gp_name];
		}else{
			$mem_group = "비회원";
		}

		if($order[pay_method] == "after_bank"){
			$method = "6";
		}
			
		$sql = "insert into ".TBL_SHOP_ORDER." 
			    (oid,uid,bname,mem_group,btel,bmobile,bmail,rname,rtel,rmobile,rmail,zip,addr,msg,date,static_date,method,bank,bank_input_name,tid,authcode,status,delivery_price, delivery_method, use_cupon_code,use_cupon_price,use_reserve_price,total_price,payment_price,taxsheet_yn,receipt_y) 
			    values
			    ('$oid','$uid','$bname','$mem_group','$btel','$bmobile','$bmail','$rname','$rtel','$rmobile','$rmail','$rzip','$raddr','$msg1',NOW(),".date("Ymd").",'$method','$bank','$bank_input_name','','','$status','".$order[delivery_total_price]."','".$order[delivery_method]."','$use_cupon_code','".$order[use_cupon_price]."','".$order[reserve_price]."','$sum','".($sum-$order[reserve_price]-$order[use_cupon_price])."','".$order[taxsheet_yn]."','".$order[receipt_y]."')";			
	
		$db2->query($sql);

		if($order[receipt_y] == "Y"){
			if($order[receipt_type] == "2"){
				$Gubun_cd = "1";
			}else{
				$Gubun_cd = "0";
			}
			$sql="insert into ".TBL_SHOP_RECEIPT." (order_no,m_useopt,m_number,id,receipt_yn,regdate) values('$oid','".$Gubun_cd."','".$order[sc_number]."','".$order[id]."','N',".date("Ymd").")";
			$db2->query($sql);
		}
		
		if($order[reserve_price] > 0 && $order[reserve_price] != ""){
			$db2->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','$oid','$pid','$sum','".($sum-$order[reserve_price]-$saveprice-$total_mem_sale_price)."','-".$order[reserve_price]."','2','무통장 후 적립금 차감액',NOW())");
		}
		
		// 해당 주문에 대한 상태 정보를 저장한다.
		
		$db2->query("insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, status, status_message, regdate ) values ('','$oid','$status','무통장입금결제',NOW())");
		$db2->query("SELECT os_ix FROM ".TBL_SHOP_ORDER_STATUS." WHERE os_ix=LAST_INSERT_ID()");
		$db2->fetch();
		$os_ix = $db2->dt[os_ix];
		$db2->query("UPDATE ".TBL_SHOP_ORDER." SET os_ix='".$os_ix."' WHERE oid='$oid' ");
		$db2->query("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status='$status' WHERE oid='$oid' ");
		$db2->query("UPDATE ".TBL_COMMON_USER." set recent_order_date = NOW() where code = '".$order[code]."' ");
		$db2->query("UPDATE shop_estimates set est_status = 2 where est_id = '".$order[cart_key]."' ");
		
	}
	$CartBool = true;
	session_register("CartBool");
	$sql = "delete from shop_cart $where ";
	$db->query($sql);
	
	echo "<script>parent.document.location.href='./orders.edit.php?oid=$oid&pid=$pid';</script>";
	
}else{
?>
	<script language="javascript">
		alert("주문이 이미 완료 되었습니다. 감사합니다.");
		location.href = "/";
	</script>
<?
	session_unregister("order");
	exit;

}


?>
