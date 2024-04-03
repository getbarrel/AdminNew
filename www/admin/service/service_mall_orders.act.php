<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

$db = new Database;
$db2 = new Database;
if ($act == "orderinfo_update"){
	$bname = trim($bname);
	$bmail = trim($bmail);
	$btel  = trim($btel);
	$rname = trim($rname);
	$rmail = trim($rmail);
	$rtel  = trim($rtel);
	$zip   = "$zipcode1-$zipcode2";
	$addr  = trim($addr);
	$bank  = trim($bank);
	$bmobile = trim($bmobile);
	$rmobile = trim($rmobile);

	if($admininfo[admin_level] == 9){
		$sql = "UPDATE service_mall_order SET bank_input_date='$bank_input_date'
						WHERE oid='$oid'";
		$db->query($sql);
	}else{
		$sql = "UPDATE service_mall_order SET bank_input_date='$bank_input_date'
						WHERE oid='$oid'";
		$db->query($sql);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('주문정보가 정상적으로 수정되었습니다.');parent.location.reload();location.href='about:blank';</script>");
}



if ($act == "update")
{
	$bname = trim($bname);
	$bmail = trim($bmail);
	$btel  = trim($btel);
	$rname = trim($rname);
	$rmail = trim($rmail);
	$rtel  = trim($rtel);
	$zip   = "$zipcode1-$zipcode2";
	$addr  = trim($addr);
	$bank  = trim($bank);
	$bmobile = trim($bmobile);
	$rmobile = trim($rmobile);


	if($admininfo[admin_level] == 9){
		//echo $bstatus ."!=". $status;
		//exit;
		//if($bstatus != $status){ // 현재상태정보와 변경상태가 틀릴경우만 상태변경정보 추가

			if($status == ORDER_STATUS_INCOM_READY || $status == ORDER_STATUS_INCOM_COMPLETE){
					if($status == ORDER_STATUS_INCOM_COMPLETE){
							$db->query("select oid, mall_ix, company_id, bank_input_date from service_mall_order  WHERE oid='".$oid."'");
							$db->fetch();
							$order = $db->dt;
							
							if($order[bank_input_date] == ""){
								$db->query("update service_mall_order set bank_input_date = '".date("Ymd")."' WHERE oid='".$oid."'");
								$db->fetch();
							}
		
							$sql = "select oid, status from service_mall_order_detail od WHERE oid='".$oid."'";
							//echo $sql;
							$db->query($sql);
							
					}
			}

			if ($status == ORDER_STATUS_DELIVERY_ING){

				$db->query("select * from service_mall_order  WHERE oid='".$oid."'");
				$order = $db->fetch();

				$db->query("select * from service_mall_order_detail WHERE oid='$oid'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				//sendMessageByStep('good_send_sucess', $mail_info);
			}else if($status == ORDER_STATUS_INCOM_COMPLETE){
				$db->query("select * from service_mall_order  WHERE oid='".$oid."'");
				$order = $db->fetch();

				$db->query("select * from service_mall_order_detail WHERE oid='$oid'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;


				//sendMessageByStep('payment_bank_apply', $mail_info);
			}else if ($status == ORDER_STATUS_CANCEL_COMPLETE){// 취소일경우
				$db->query("select * from service_mall_order  WHERE oid='".$oid."'");
				$order = $db->fetch();
				//$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");
				//$db->query("UPDATE shop_baymoney_info  Set state = '".BAYMONEY_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

				$udb = new Database;
				/*
				$db->query("select * from service_mall_order_detail WHERE oid='$oid'");

				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					if($select_option_id != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$select_option_id."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$pcnt." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] +1) > $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'S' ";
							}
						}

					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$pcnt." $stock_update WHERE id = '".$db->dt[pid]."'");
				}
				*/
				$db->query("select * from service_mall_order_detail WHERE oid='$oid'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				//sendMessageByStep('order_cancel', $mail_info);

				echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['orders.act.php']['A'][language]);</script>");//카드결제일 경우 [승인취소] 작업도 해주세요.
			}else if ($status == ORDER_STATUS_REFUND_COMPLETE){// 환불완료일때
				/*$db->query("select * from service_mall_order  WHERE oid='".$oid."'");
				$order = $db->fetch();*/
				//$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");
				//$db->query("UPDATE shop_baymoney_info  Set state = '".BAYMONEY_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

				//$udb = new Database;
				/*
				$db->query("select * from service_mall_order_detail WHERE oid='$oid'");

				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					if($select_option_id != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$select_option_id."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$pcnt." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] +1) > $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'S' ";
							}
						}

					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$pcnt." $stock_update WHERE id = '".$db->dt[pid]."'");
				}
				*/
				/*$db->query("select * from service_mall_order_detail WHERE oid='$oid'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				sendMessageByStep('order_cancel', $mail_info);*/

				echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['orders.act.php']['A'][language]);</script>");//카드결제일 경우 [승인취소] 작업도 해주세요.
			}
		//}






		if ($product_status_change){// 상품상태 변경 체크시 각각의 상품에 대한 상태 변경이 된다
			/*if($deliverycode){
				$deliverycode_string = " ,  invoice_no='$deliverycode',quick = '$quick' " ;
			}*/

			$udb = new Database;
			$db->debug = true;
			$udb->debug = true;

			for($i=0;$i < count($od_ix);$i++){
				$sql = "select * from service_mall_order_detail where  od_ix ='".$od_ix[$i]."' ";
				$db->query($sql);//oid='$oid' and
				$db->fetch();
				
				$order_detail_info = $db->dt;
				
				$pid = $db->dt[pid];
				$pname = $db->dt[pname];
				$oid = $db->dt[oid];
				$si_ix = $db->dt[si_ix];//service_info의 인덱스키 값
				$parent_service_code = $db->dt[parent_service_code];//서비스 종류 대분류
				$service_code = $db->dt[service_code];//서비스 종류 중분류
				$ptprice = $db->dt[ptprice];
				$apply_type = $db->dt[apply_type];//서비스 신청 구분 N:신규 E:연장 C:변경
				$period = $db->dt[period];//서비스 신청 기간
				
				if($db->dt[status] != $status && $status == ORDER_STATUS_CANCEL_COMPLETE){
					
					
				
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_INCOM_COMPLETE){//입금확인 시 서비스 상태 변경 kbk
					if($apply_type=="N") {//신규
						$sdate=date('Y-m-d H:i:s');
						$edate = date('Y-m-d H:i:s',strtotime('+'.$period.' day'));
						
						$sql="UPDATE service_mall_ing SET si_status = 'SI', set_status = 'SC', sm_sdate='".$sdate."',sm_edate='".$edate."'  WHERE si_ix='".$si_ix."' ";
						$db->query($sql);
					} else if($apply_type=="E") {//연장
						$sql="SELECT UNIX_TIMESTAMP(sm_edate) AS out_end_date FROM service_mall_ing WHERE si_ix='".$si_ix."' ";
						$db->query($sql);
						$db->fetch();
						$out_end_date=$db->dt["out_end_date"];
						$sdate=date('Y-m-d H:i:s');
						$now_date=time();
						if($now_date>=$out_end_date) {
							$edate=date("Y-m-d H:i:s",strtotime("+".$period." day"));
						} else {
							$end_timestamp=$out_end_date+(strtotime("+".$period." day") - time());
							$edate=date("Y-m-d H:i:s",$end_timestamp);
						}
						$sql="UPDATE service_mall_ing SET sm_edate='".$edate."' WHERE si_ix='".$si_ix."' ";
						$db->query($sql);
					}
					
				} else if($db->dt[status] != $status && $status == ORDER_STATUS_CANCEL_COMPLETE) {//주문 취소 시 서비스 상태 변경 kbk
					if($db->dt[status]==ORDER_STATUS_INCOM_COMPLETE) {
						if($apply_type=="N") {//신규
							$sql="UPDATE service_mall_ing SET si_status='CC' WHERE si_ix='".$si_ix."' ";
							$db->query($sql);
						} else if($apply_type=="E") {//연장
							$sql="SELECT period FROM service_mall_order_detail od WHERE od_ix ='".$od_ix[$i]."' AND od.status='IC' ";
							$db->query($sql);
							$db->fetch();
							$pre_period=$db->dt["period"];
							$pre_edate=date('Y-m-d H:i:s',strtotime('-'.$pre_period.' day'));
							$sql="UPDATE service_mall_ing SET end_date='".$pre_edate."' WHERE si_ix='".$si_ix."' ";
							$db->query($sql);
						}
					} else if($db->dt[status]==ORDER_STATUS_INCOM_READY) {
						
					}
				}else{
					$dc_date_str = "";
				}


				$db->query("UPDATE service_mall_order_detail SET status = '$status' WHERE od_ix ='".$od_ix[$i]."' ");//oid='$oid' and
				$status_message = $admininfo[charger]."(".$admininfo[charger_id].")";

				if($status != ORDER_STATUS_INCOM_READY || $status != ORDER_STATUS_INCOM_COMPLETE){
					$db->query("insert into service_mall_order_status (os_ix, oid,pid, status, status_message, regdate ) values ('','$oid','".$pid."','$status','$status_message',NOW())");
				}else{
					$dc_date_str = "";
				}
			}
			//print_r($order_detail_info);
			//	exit;
			if($status == ORDER_STATUS_CANCEL_COMPLETE){
					$sql = "select
					sum(case when oid='$oid' then 1 else 0 end) as whole_total ,
					sum(case when company_id ='".$admininfo[company_id]."' then 1 else 0 end) as admin_total ,
					sum(case when company_id != '".$admininfo[company_id]."' then 1 else 0 end) as etc_total ,
					sum(case when status = '$status' then 1 else 0 end) as status_total
					from service_mall_order_detail WHERE oid='$oid'";
					$db->query($sql);
					$db->fetch();
					$whole_total = $db->dt[whole_total];
					$admin_total = $db->dt[admin_total];
					$etc_total = $db->dt[etc_total];
					$status_total = $db->dt[status_total];

					if(count($od_ix) == $whole_total || $whole_total == $status_total){
						$udb->query("UPDATE service_mall_order SET status = '$status'  WHERE oid ='".$oid."' ");
						//$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");
					}
			}

		}
	}else if($admininfo[admin_level] == 8){

			$udb = new Database;

			for($i=0;$i < count($od_ix);$i++){
				//echo ("UPDATE service_mall_order_detail SET invoice_no='$deliverycode' WHERE oid='$oid' and pid ='".$pid[$i]."' ");
				$db->query("select * from service_mall_order_detail where  od_ix ='".$od_ix[$i]."' ");//oid='$oid' and
				$db->fetch();
				$pid = $db->dt[pid];
				$ptprice = $db->dt[ptprice];

				if($db->dt[status] != $status && $status == ORDER_STATUS_DELIVERY_COMPLETE){
					$dc_date_str = " , dc_date = NOW() ";
					//$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_COMPLETE."' WHERE oid='$oid' and state = '".RESERVE_STATUS_READY."' and pid = '$pid' ");
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_DELIVERY_ING){
					$di_date_str = " , di_date = NOW() ";
					/*if($di_date == ""){
						if ($stock_use_yn == "Y"){
							$db->query("UPDATE ".TBL_SHOP_PRODUCT."  Set sell_ing_cnt = sell_ing_cnt - ".$pcnt.", stock = stock - ".$pcnt."  WHERE id = '".$pid."'");
						}
					}
					if($select_option_id != 0){
						if($stock_use_yn == "Y"){
								$db->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = option_sell_ing_cnt - ".$pcnt.", option_stock = option_stock - ".$pcnt."  where pid = '$pid' and id ='$select_option_id' ");
								$db->query("SELECT option_stock , option_safestock FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid = '$pid' and id ='$select_option_id' ");
								if($db->total){
										$db->fetch();
				
										if($db->dt[option_stock] <= 0){
											$db->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'N' where id ='$pid'"); // 품절
										}else if($db->dt[option_stock] < $db->dt[option_safestock]){
											$db->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'R' where id ='$pid'");  // 재고 부족
										}
								}
						}
					}*/
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_CANCEL_COMPLETE){
					// 주문취소 완료시 적립금 적립 취소
					//
					/*$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid='$oid' and pid = '$pid' and state = '".RESERVE_STATUS_READY."' ");

					if($select_option_id != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$select_option_id."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							// 취소완료 일때는 판매진행중인 상품의 옵션 수량은 차감하고 실재 재고 수량은 늘려준다.
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_sell_ing_cnt = option_sell_ing_cnt - ".$pcnt." WHERE id = '".$udb->dt[id]."'");
							//판매진행중 재고가 감소되기때문에 실재 재고와는 상관 없음 2012.02.27 shs
						}
					}
					// 취소완료 일때는 판매진행중인 수량은 차감하고 실재 재고 수량은 늘려준다.
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set sell_ing_cnt = sell_ing_cnt - ".$pcnt." $stock_update WHERE id = '".$db->dt[pid]."'");
					*/
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_RETURN_COMPLETE){
					// 주문취소 완료시 적립금 적립 취소
					//
					/*$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid='$oid' and pid = '$pid' and state = '".RESERVE_STATUS_READY."' ");

					if($select_option_id != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$select_option_id."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$pcnt." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] + $pcnt) <= 0){
								$stock_update = " , option_stock_yn = 'N' "; // 품절
							}else if(($udb->dt[option_stock]  + $pcnt ) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'R' ";  // 여유
							}
						}
					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  set stock = stock + ".$pcnt." $stock_update WHERE id = '".$db->dt[pid]."'");
					$udb->query("update service_mall_order set order_return_price = order_return_price + ".$ptprice." where oid = '$oid' ");
					*/
				/*}else if($db->dt[status] != $status && $status == ORDER_STATUS_REFUND_APPLY){
					$db2->query("select * from service_mall_order_detail where  od_ix ='".$od_ix[$i]."' ");

					$order_details = $db2->fetchall();
					$mall_info = getcominfo();
					sendMessageByStep('return_order', $mail_info);
				*///주석 처리 함 kbk 12/02/28
				}else{
					$dc_date_str = "";
				}

				$db->query("UPDATE service_mall_order_detail SET status = '$status' WHERE od_ix ='".$od_ix[$i]."' ");	//oid='$oid' and
				$status_message = $admininfo[charger]."(".$admininfo[charger_id].")";

				$db->query("insert into service_mall_order_status (os_ix, oid,pid, status, status_message, regdate ) values ('','$oid','".$pid."','$status','$status_message',NOW())");

				/*if($status != $bstatus && $status == ORDER_STATUS_DELIVERY_COMPLETE){
					$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_COMPLETE."' WHERE oid='$oid' and pid ='".$pid."' and state = '".RESERVE_STATUS_READY."' ");
				}*/
			}

			if($status == ORDER_STATUS_CANCEL_COMPLETE){
					$sql = "select
					sum(case when oid='$oid' then 1 else 0 end) as whole_total ,
					sum(case when company_id ='".$admininfo[company_id]."' then 1 else 0 end) as admin_total ,
					sum(case when company_id != '".$admininfo[company_id]."' then 1 else 0 end) as etc_total ,
					sum(case when status = '$status' then 1 else 0 end) as status_total
					from service_mall_order_detail WHERE oid='$oid'";
					$db->query($sql);
					$db->fetch();
					$whole_total = $db->dt[whole_total];
					$admin_total = $db->dt[admin_total];
					$etc_total = $db->dt[etc_total];
					$status_total = $db->dt[status_total];

					if(count($od_ix) == $whole_total || $whole_total == $status_total){
						$udb->query("UPDATE service_mall_order SET status = '$status'  WHERE oid ='".$oid."' ");
						//$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

					}
			}


	}



	//echo("<script>location.href = 'orders.list.php?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."';</script>");
	echo("<script>parent.location.reload();location.href='about:blank';</script>");
}

if ($act == "stateupdate"){
	$db->query("UPDATE service_mall_order SET status='".ORDER_STATUS_RETURN_COMPLETE."' WHERE oid='$oid'");

//	$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO."  (id,uid,oid,pid,ptprice,payprice,reserve,state,regdate) VALUES ('','".$user[code]."','$oid','$pid','$sum','".($sum-$order[reserve_price])."','-".$order[reserve_price]."','4',NOW())");
	//$db->query("Update ".TBL_SHOP_RESERVE_INFO."  set state = '5' where oid = '$oid'"); // state :  5 -> 반품;

	/*$db->query("SELECT * FROM ".TBL_SHOP_RESERVE_INFO."  WHERE oid = '$oid' and state = '".RESERVE_STATUS_RETURN."'");
	if($db->total == 0){
		$db->query("SELECT * FROM ".TBL_SHOP_RESERVE_INFO."  WHERE oid = '$oid'");
		$db->fetch();
		$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO."  (id,uid,oid,pid,ptprice,payprice,reserve,state,regdate) VALUES ('','".$db->dt[uid]."','".$db->dt[oid]."','".$db->dt[pid]."','0','0','-".$db->dt[reserve]."','5',NOW())");
	}*/

	// 해당 주문에 대한 상태 정보를 저장한다.
	$db->query("insert into service_mall_order_status (os_ix, oid,pid, status, status_message, regdate ) values ('','$oid','".$pid."','$status','카드구매',NOW())");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('반품처리가 완료되었습니다.');document.location.href='service_mall_orders.list.php'</script>");
}

if ($act == "select_status_update"){
	$db3 = new Database;
	if($admininfo[admin_level] == 9){

		for($j=0;$j < count($oid);$j++){
			//echo $oid[$j]."<br />";
			$sql="select * from shop_order where oid='".$oid[$j]."'";
			$db3->query($sql);
			$db3->fetch();

			$bname = $db3->dt["bname"];
			$bank  = $db3->dt["bank"];

			if($status == ORDER_STATUS_INCOM_READY || $status == ORDER_STATUS_INCOM_COMPLETE){
						/*
				$db->query("insert into service_mall_order_status (os_ix, oid, status, status_message, regdate ) values ('','$oid','$status','',NOW())");
				$db->query("SELECT os_ix FROM service_mall_order_status WHERE os_ix=LAST_INSERT_ID()");
				$db->fetch();
				$os_ix = $db->dt[os_ix];

				$os_ix_str = ",os_ix='".$os_ix."'";
				*/
				if($status == ORDER_STATUS_INCOM_COMPLETE){
					$db->query("select oid, mall_ix, company_id from service_mall_order  WHERE oid='".$oid[$j]."'");
					$db->fetch();
					$order = $db->dt;

					$sql = "select oid, status from service_mall_order_detail od WHERE oid='".$oid[$j]."'";
					//echo $sql;
					$db->query($sql);
					for($i=0;$i < $db->total;$i++){

						$db->fetch($i);
						//echo $db->dt[hotcon_pcode];
						/*if($db->dt[hotcon_event_id] && $db->dt[hotcon_pcode]){

							if($db->dt[status] != $status){
								CallHotCon($order[uid], $order[oid], $db->dt[pid], $db->dt[hotcon_event_id], $db->dt[hotcon_pcode], $db->dt[pcnt], $order[rmobile]);
						//		echo "test";
							}
						}*/
					}
				}
			//	exit;

			}

			if ($status == ORDER_STATUS_DELIVERY_ING){

				$db->query("select * from service_mall_order  WHERE oid='".$oid[$j]."'");
				$order = $db->fetch();

				$db->query("select * from service_mall_order_detail WHERE oid='".$oid[$j]."'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				//sendMessageByStep('good_send_sucess', $mail_info);
			}else if($status == ORDER_STATUS_INCOM_COMPLETE){
				$db->query("select * from service_mall_order  WHERE oid='".$oid[$j]."'");
				$order = $db->fetch();

				$db->query("select * from service_mall_order_detail WHERE oid='".$oid[$j]."'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;


				//sendMessageByStep('payment_bank_apply', $mail_info);
			}else if ($status == ORDER_STATUS_CANCEL_COMPLETE){// 취소일경우
				$db->query("select * from service_mall_order  WHERE oid='".$oid[$j]."'");
				$order = $db->fetch();
				//$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");
				//$db->query("UPDATE shop_baymoney_info  Set state = '".BAYMONEY_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

				$udb = new Database;
				/*
				$db->query("select * from service_mall_order_detail WHERE oid='$oid'");

				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					if($select_option_id != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$select_option_id."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$pcnt." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] +1) <= 0){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'R' ";
							}
						}

					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$pcnt." $stock_update WHERE id = '".$db->dt[pid]."'");
				}
				*/
				$db->query("select * from service_mall_order_detail WHERE oid='".$oid[$j]."'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				//sendMessageByStep('order_cancel', $mail_info);

				echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['orders.act.php']['A'][language]);</script>");//'카드결제일 경우 [승인취소] 작업도 해주세요.'
			}else if ($status == ORDER_STATUS_REFUND_COMPLETE){// 환불완료일때
				/*$db->query("select * from service_mall_order  WHERE oid='".$oid."'");
				$order = $db->fetch();*/
				//$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");
				//$db->query("UPDATE shop_baymoney_info  Set state = '".BAYMONEY_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

				//$udb = new Database;
				/*
				$db->query("select * from service_mall_order_detail WHERE oid='$oid'");

				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					if($select_option_id != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$select_option_id."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$pcnt." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] +1) <= 0){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'R' ";
							}
						}

					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$pcnt." $stock_update WHERE id = '".$db->dt[pid]."'");
				}
				*/
				/*$db->query("select * from service_mall_order_detail WHERE oid='$oid'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				sendMessageByStep('order_cancel', $mail_info);*/

				//echo("<script>alert('카드결제일 경우 [승인취소] 작업도 해주세요.');</script>");
				echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['orders.act.php']['A'][language]);</script>");//'카드결제일 경우 [승인취소] 작업도 해주세요.'
			}

			// 상품에 대한 업데이트

			//if($status == ORDER_STATUS_DELIVERY_COMPLETE) {
				//$deliverycode_string = " , quick = '18' " ;
			//}
			// 송장 밑 배송업체에 대한 업데이트는 안함

			$udb = new Database;
			$sql="select od_ix from shop_order_detail where oid='".$oid[$j]."'";
			$db3->query($sql);
			$od_ix=$db3->fetchall();

			for($i=0;$i < count($od_ix);$i++){
				$db->query("select * from service_mall_order_detail where  od_ix ='".$od_ix[$i]["od_ix"]."' ");//oid='$oid' and
				$db->fetch();
				$order_detail_info = $db->dt;
				$pid = $db->dt[pid];
				$oid_d = $db->dt[oid];
				$ptprice = $db->dt[ptprice];
				
				if($db->dt[status] != $status && $status == ORDER_STATUS_DELIVERY_COMPLETE){
					
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_DELIVERY_ING){
					
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_CANCEL_COMPLETE){
					
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_RETURN_COMPLETE){

					
				}else{
					$dc_date_str = "";
				}
				$db->query("UPDATE service_mall_order_detail SET status = '$status' WHERE od_ix ='".$od_ix[$i]["od_ix"]."' ");//oid='$oid' and
				$status_message = $admininfo[charger]."(".$admininfo[charger_id].")";

				/*if($status != ORDER_STATUS_INCOM_READY || $status != ORDER_STATUS_INCOM_COMPLETE){
					$db->query("insert into service_mall_order_status (os_ix, oid, pid, status, status_message, company_id, regdate ) values ('','$oid','".$pid."','$status','$status_message','".$admininfo[company_id]."',NOW())");
				}*/
			}

			if($status == ORDER_STATUS_CANCEL_COMPLETE){
					$sql = "select
					sum(case when oid='".$oid[$j]."' then 1 else 0 end) as whole_total ,
					sum(case when company_id ='".$admininfo[company_id]."' then 1 else 0 end) as admin_total ,
					sum(case when company_id != '".$admininfo[company_id]."' then 1 else 0 end) as etc_total ,
					sum(case when status = '$status' then 1 else 0 end) as status_total
					from service_mall_order_detail WHERE oid='".$oid[$j]."'";
					$db->query($sql);
					$db->fetch();
					$whole_total = $db->dt[whole_total];
					$admin_total = $db->dt[admin_total];
					$etc_total = $db->dt[etc_total];
					$status_total = $db->dt[status_total];

					if(count($od_ix) == $whole_total || $whole_total == $status_total){
						$udb->query("UPDATE service_mall_order SET status = '$status'  WHERE oid ='".$oid[$j]."' ");
						
					}
			}

		}
	}else if($admininfo[admin_level] == 8){
		for($j=0;$j < count($oid);$j++){
			$sql="select * from shop_order where oid='".$oid[$j]."'";
			$db3->query($sql);
			$db3->fetch();

			$bname = $db3->dt["bname"];
			$bank  = $db3->dt["bank"];

			if($status == ORDER_STATUS_INCOM_READY || $status == ORDER_STATUS_INCOM_COMPLETE){
						/*
				$db->query("insert into service_mall_order_status (os_ix, oid, status, status_message, regdate ) values ('','$oid','$status','',NOW())");
				$db->query("SELECT os_ix FROM service_mall_order_status WHERE os_ix=LAST_INSERT_ID()");
				$db->fetch();
				$os_ix = $db->dt[os_ix];

				$os_ix_str = ",os_ix='".$os_ix."'";
				*/
				if($status == ORDER_STATUS_INCOM_COMPLETE){
					$db->query("select oid, code from service_mall_order  WHERE oid='".$oid[$j]."'");
					$db->fetch();
					$order = $db->dt;

					$sql = "select oid, status from service_mall_order_detail od WHERE oid='".$oid[$j]."'";
					//echo $sql;
					$db->query($sql);
					for($i=0;$i < $db->total;$i++){

						$db->fetch($i);
					}
				}
			//	exit;

			}

			if ($status == ORDER_STATUS_DELIVERY_ING){

				$db->query("select * from service_mall_order  WHERE oid='".$oid[$j]."'");
				$order = $db->fetch();

				$db->query("select * from service_mall_order_detail WHERE oid='".$oid[$j]."'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				//sendMessageByStep('good_send_sucess', $mail_info);
			}else if($status == ORDER_STATUS_INCOM_COMPLETE){
				$db->query("select * from service_mall_order  WHERE oid='".$oid[$j]."'");
				$order = $db->fetch();

				$db->query("select * from service_mall_order_detail WHERE oid='".$oid[$j]."'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;


				//sendMessageByStep('payment_bank_apply', $mail_info);
			}else if ($status == ORDER_STATUS_CANCEL_COMPLETE){// 취소일경우
				$db->query("select * from service_mall_order  WHERE oid='".$oid[$j]."'");
				$order = $db->fetch();
				//$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");
				//$db->query("UPDATE shop_baymoney_info  Set state = '".BAYMONEY_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

				$udb = new Database;
				/*
				$db->query("select * from service_mall_order_detail WHERE oid='$oid'");

				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					if($select_option_id != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$select_option_id."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$pcnt." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] +1) > $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'R' ";
							}
						}

					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$pcnt." $stock_update WHERE id = '".$db->dt[pid]."'");
				}
				*/
				$db->query("select * from service_mall_order_detail WHERE oid='".$oid[$j]."'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				//sendMessageByStep('order_cancel', $mail_info);

				//echo("<script>alert('카드결제일 경우 [승인취소] 작업도 해주세요.');</script>");
				echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['orders.act.php']['A'][language]);</script>");//'카드결제일 경우 [승인취소] 작업도 해주세요.'
			}else if ($status == ORDER_STATUS_REFUND_COMPLETE){// 환불완료일때
				/*$db->query("select * from service_mall_order  WHERE oid='".$oid."'");
				$order = $db->fetch();*/
				//$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");
				//$db->query("UPDATE shop_baymoney_info  Set state = '".BAYMONEY_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

				//$udb = new Database;
				/*
				$db->query("select * from service_mall_order_detail WHERE oid='$oid'");

				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					if($select_option_id != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$select_option_id."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$pcnt." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] +1) > $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'R' ";
							}
						}

					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$pcnt." $stock_update WHERE id = '".$db->dt[pid]."'");
				}
				*/
				/*$db->query("select * from service_mall_order_detail WHERE oid='$oid'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				sendMessageByStep('order_cancel', $mail_info);*/

				//echo("<script>alert('카드결제일 경우 [승인취소] 작업도 해주세요.');</script>");
				echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['orders.act.php']['A'][language]);</script>");//'카드결제일 경우 [승인취소] 작업도 해주세요.'
			}

			// 상품에 대한 업데이트

			//if($status == ORDER_STATUS_DELIVERY_COMPLETE) {
				//$deliverycode_string = " , quick = '18' " ;
			//}
			// 송장 밑 배송업체에 대한 업데이트는 안함

			$udb = new Database;
			$sql="select od_ix from shop_order_detail where oid='".$oid[$j]."'";
			$db3->query($sql);
			$od_ix=$db3->fetchall();

			for($i=0;$i < count($od_ix);$i++){
				$db->query("select * from service_mall_order_detail where  od_ix ='".$od_ix[$i]["od_ix"]."' ");//oid='$oid' and
				$db->fetch();
				$pid = $db->dt[pid];
				$oid = $db->dt[oid];
				$ptprice = $db->dt[ptprice];

				$db->query("UPDATE service_mall_order_detail SET status = '$status' WHERE od_ix ='".$od_ix[$i]["od_ix"]."' ");//oid='$oid' and
				$status_message = $admininfo[charger]."(".$admininfo[charger_id].")";

			}
		}
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert(' 선택된 주문에 대한 상태가 정상적으로 변경되었습니다..');parent.document.location.reload();</script>";

	//print_r($_POST);
	/*if($admininfo[admin_level] == 9){
		for($i=0;$i < count($oid);$i++){
			// 주문 테이블의 상태를 변경하기전에 이전상태를 저장
			$status_message = $admininfo[charger]."(".$admininfo[charger_id].")";
			if($status == ORDER_STATUS_INCOM_READY || $status == ORDER_STATUS_INCOM_COMPLETE){


				// 상품 상태를 변경 -- 입금예정과 입금 확인 상태만 배송상태 남김
				$db->query("UPDATE service_mall_order SET status='$status' WHERE oid='".$oid[$i]."'");
				$db->query("insert into service_mall_order_status (os_ix, oid, status, status_message, regdate ) values ('','".$oid[$i]."','$status','일괄배송처리',NOW())");

				if($status == ORDER_STATUS_INCOM_COMPLETE){
					$db->query("select oid, uid, rmobile from service_mall_order  WHERE oid='".$oid[$i]."'");
					$db->fetch();
					$order = $db->dt;

					$db->query("select oid, hotcon_event_id, hotcon_pcode, pcnt from service_mall_order_detail od WHERE oid='".$oid[$i]."'");
					for($i=0;$i < $db->total;$i++){
						$db->fetch($i);
						if($db->dt[hotcon_event_id] && $db->dt[hotcon_pcode]){
							CallHotCon($order[uid], $order[oid], $db->dt[pid], $db->dt[hotcon_event_id], $db->dt[hotcon_pcode], $db->dt[pcnt], $order[rmobile]);
						}
					}
				}

			}
			if($status == ORDER_STATUS_DELIVERY_READY){
				$db->query("select oid, pid from service_mall_order_detail  WHERE oid='".$oid[$i]."' and status in ('".ORDER_STATUS_INCOM_COMPLETE."')");
				$o_details = $db->fetchall();
				for($j=0;$j < count($o_details);$j++){
					$db->query("UPDATE service_mall_order_detail SET status='".$status."' WHERE oid='".$o_details[$j][oid]."' and pid='".$o_details[$j][pid]."' ");
					$db->query("insert into service_mall_order_status (os_ix, oid, pid, status, status_message, regdate ) values ('','".$o_details[$j][oid]."','".$o_details[$j][pid]."','$status','$status_message',NOW())");
				}
			}else{
				$db->query("UPDATE service_mall_order_detail SET status='".$status."' WHERE oid='".$oid[$i]."' ");
			}

		}
	}else if($admininfo[admin_level] == 8){
		for($i=0;$i<count($oid);$i++){
			// 주문 테이블의 상태를 변경하기전에 이전상태를 저장

			if($status == ORDER_STATUS_INCOM_READY || $status == ORDER_STATUS_INCOM_COMPLETE){
				// 상품 상태를 변경 -- 입금예정과 입금 확인 상태만 배송상태 남김
				//$db->query("UPDATE service_mall_order SET status='$status' WHERE oid='".$oid[$i]."'");
			}
			if($status == ORDER_STATUS_DELIVERY_READY){
				$db->query("select oid, pid from service_mall_order_detail  WHERE oid='".$oid[$i]."' and status in ('".ORDER_STATUS_INCOM_COMPLETE."') and company_id ='".$admininfo[company_id]."'");
				$o_details = $db->fetchall();

				for($j=0;$j < count($o_details);$j++){
					$db->query("UPDATE service_mall_order_detail SET status='".$status."' WHERE oid='".$o_details[$j][oid]."' and pid='".$o_details[$j][pid]."' ");
					$db->query("insert into service_mall_order_status (os_ix, oid, pid, status, status_message, regdate ) values ('','".$o_details[$j][oid]."','".$o_details[$j][pid]."','$status','$status_message',NOW())");
				}
			//}else{
			//	$db->query("UPDATE service_mall_order_detail SET status='".$status."' WHERE oid='".$oid[$i]."' ");
			}

		}
	}


	echo "<script language='javascript'>alert(' 선택된 주문에 대한 상태가 정상적으로 변경되었습니다..');parent.document.location.reload();</script>";

	//header("Location:../product_list.php");
	*/
}


if ($act == "delete"){
	$db->query("DELETE FROM service_mall_order WHERE oid='$oid'");
	$db->query("DELETE FROM service_mall_order_detail WHERE oid='$oid'");
	$db->query("DELETE FROM service_mall_order_status WHERE oid='$oid'");
	$db->query("DELETE FROM service_mall_order_memo WHERE oid='$oid' ");
	echo("<script>top.location.href = 'service_mall_orders.list.php?page=$page';</script>");
}



if ($act == "send_mail"){
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");


	/*$db->query("Select bmail, bname, uid FROM service_mall_order WHERE oid = '".$oid."'");
	$db->fetch();

	$mail_info[mem_name] = $db->dt[bname];
	$mail_info[mem_mail] = $db->dt[bmail];
	$mail_info[mem_id] = $db->dt[bname];
	$email_card_contents_basic = "요청하신 견적서입니다";

	copy("http://".$HTTP_HOST."../order/taxbill.php?mode=excel&oid=".$oid."&uid=".$db->dt[uid]."",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/taxbill.xls");

	$subject = " ".$mail_info[mem_name]." 님, 요청하신 견적서 입니다..";
	SendMail($mail_info, $subject,$email_card_contents_basic,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/taxbill.xls");


	//echo $mail_info[mem_mail];
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 메일이 발송되었습니다.');</script>");
	echo("<script>self.close();</script>");*/
}

function EscrowAct(){
	require("EscrowLib.php");



	/*########################
	            인스턴스 생성
	########################*/

	$escrow = new Escrow;



	/*########################################
	                  배송/반품 공통 정보 설정
	########################################*/

	$escrow->inipayhome = "/usr/local/INIpay41"; 	// 이니페이 지불시스템 설치 절대 경로(반드시 절대 경로로 입력하시기 바랍니다.)
	$escrow->mid = "hanatest01";					// 상점 아이디
	$escrow->EscrowType=$EscrowType;          		// 에스크로 타입
	$escrow->hanatid = $hanatid;	         		// 하나은행 거래 아이디
	$escrow->invno = $invno;				// 운송장 번호
	$escrow->adminID = $adminID;				// 등록자 ID
	$escrow->adminName = $adminName;			// 등록자 성명
	$escrow->regdate = date("Ymd");				// 등록요청 일자
	$escrow->regtime = date("His");				// 등록요청 시간
}
?>
