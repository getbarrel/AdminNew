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
		$sql = "UPDATE ".TBL_SHOP_ORDER." SET
						bname='$bname', mem_group ='$mem_group', bmail='$bmail', btel='$btel', bmobile = '$bmobile',
						rname='$rname', rmail='$rmail', rtel='$rtel', rmobile = '$rmobile', zip='$zip', addr='$addr',bank_input_date='$bank_input_date'
						WHERE oid='$oid'";
		$db->query($sql);
	}else{
		$sql = "UPDATE ".TBL_SHOP_ORDER." SET
						bname='$bname', mem_group ='$mem_group', bmail='$bmail', btel='$btel', bmobile = '$bmobile',
						rname='$rname', rmail='$rmail', rtel='$rtel', rmobile = '$rmobile', zip='$zip', addr='$addr',bank_input_date='$bank_input_date'
						WHERE oid='$oid'";
		$db->query($sql);
	}

	echo("<script>alert('주문정보가 정상적으로 수정되었습니다.');parent.location.reload();</script>");
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
				/*
				$db->query("insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, status, status_message, regdate ) values ('','$oid','$status','',NOW())");
				$db->query("SELECT os_ix FROM ".TBL_SHOP_ORDER_STATUS." WHERE os_ix=LAST_INSERT_ID()");
				$db->fetch();
				$os_ix = $db->dt[os_ix];

				$os_ix_str = ",os_ix='".$os_ix."'";
				*/
				if($status == ORDER_STATUS_INCOM_COMPLETE){
					$db->query("select oid, uid, rmobile from ".TBL_SHOP_ORDER."  WHERE oid='".$oid."'");
					$db->fetch();
					$order = $db->dt;

					$sql = "select oid, hotcon_event_id, hotcon_pcode, status, pcnt from ".TBL_SHOP_ORDER_DETAIL." od WHERE oid='".$oid."'";
					//echo $sql;
					$db->query($sql);
					for($i=0;$i < $db->total;$i++){

						$db->fetch($i);
						//echo $db->dt[hotcon_pcode];
						if($db->dt[hotcon_event_id] && $db->dt[hotcon_pcode]){

							if($db->dt[status] != $status){
								CallHotCon($order[uid], $order[oid], $db->dt[pid], $db->dt[hotcon_event_id], $db->dt[hotcon_pcode], $db->dt[pcnt], $order[rmobile]);
						//		echo "test";
							}
						}
					}
				}
			//	exit;

			}

			if ($status == ORDER_STATUS_DELIVERY_ING){

				$db->query("select * from ".TBL_SHOP_ORDER."  WHERE oid='".$oid."'");
				$order = $db->fetch();

				$db->query("select * from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				sendMessageByStep('good_send_sucess', $mail_info);
			}else if($status == ORDER_STATUS_INCOM_COMPLETE){
				$db->query("select * from ".TBL_SHOP_ORDER."  WHERE oid='".$oid."'");
				$order = $db->fetch();

				$db->query("select * from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;


				sendMessageByStep('payment_bank_apply', $mail_info);
			}else if ($status == ORDER_STATUS_CANCEL_COMPLETE){// 취소일경우
				$db->query("select * from ".TBL_SHOP_ORDER."  WHERE oid='".$oid."'");
				$order = $db->fetch();
				//$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");
				//$db->query("UPDATE shop_baymoney_info  Set state = '".BAYMONEY_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

				$udb = new Database;
				/*
				$db->query("select * from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'");

				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					if($db->dt[option1] != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$db->dt[option1]."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$db->dt[pcnt]." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] +1) > $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'S' ";
							}
						}

					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$db->dt[pcnt]." $stock_update WHERE id = '".$db->dt[pid]."'");
				}
				*/
				$db->query("select * from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				sendMessageByStep('order_cancel', $mail_info);

				echo("<script>alert('카드결제일 경우 [승인취소] 작업도 해주세요.');</script>");
			}else if ($status == ORDER_STATUS_REFUND_COMPLETE){// 환불완료일때
				/*$db->query("select * from ".TBL_SHOP_ORDER."  WHERE oid='".$oid."'");
				$order = $db->fetch();*/
				//$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");
				//$db->query("UPDATE shop_baymoney_info  Set state = '".BAYMONEY_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

				//$udb = new Database;
				/*
				$db->query("select * from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'");

				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					if($db->dt[option1] != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$db->dt[option1]."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$db->dt[pcnt]." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] +1) > $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'S' ";
							}
						}

					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$db->dt[pcnt]." $stock_update WHERE id = '".$db->dt[pid]."'");
				}
				*/
				/*$db->query("select * from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'");
				$order_details = $db->fetchall();

				$mail_info[mem_name] = $bname;
				$mail_info[mem_mail] = $bmail;
				$mail_info[mem_id] = $bname;
				$mail_info[mem_mobile] = $bmobile;

				sendMessageByStep('order_cancel', $mail_info);*/

				echo("<script>alert('카드결제일 경우 [승인취소] 작업도 해주세요.');</script>");
			}
		//}






		if ($product_status_change){// 상품상태 변경 체크시 각각의 상품에 대한 상태 변경이 된다
			if($deliverycode){
				$deliverycode_string = " ,  invoice_no='$deliverycode',quick = '$quick' " ;
			}

			$udb = new Database;

			for($i=0;$i < count($od_ix);$i++){
				$db->query("select * from ".TBL_SHOP_ORDER_DETAIL." where  od_ix ='".$od_ix[$i]."' ");//oid='$oid' and
				$db->fetch();
				$pid = $db->dt[pid];
				$oid = $db->dt[oid];
				$ptprice = $db->dt[ptprice];
				if($db->dt[status] != $status && $status == ORDER_STATUS_DELIVERY_COMPLETE){
					$dc_date_str = " , dc_date = NOW() ";
					$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_COMPLETE."' WHERE oid='$oid' and state = '".RESERVE_STATUS_READY."' and pid = '$pid' ");
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_DELIVERY_ING){
					$di_date_str = " , di_date = NOW() ";
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_CANCEL_COMPLETE){
					// 주문취소 완료시 적립금 적립 취소
					//
					$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid='$oid' and pid = '$pid' and state = '".RESERVE_STATUS_READY."' ");

					if($db->dt[option1] != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock,po.option_div,pos.option_name from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$db->dt[option1]."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);

							if(($udb->dt[option_stock] +1) > $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'S' ";
							}
							$option_text = $udb->dt[option_name]." : ".$udb->dt[option_div];
							$option_id = $udb->dt[id];
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$db->dt[pcnt]." WHERE id = '".$udb->dt[id]."'");
						}
					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$db->dt[pcnt]." $stock_update WHERE id = '".$db->dt[pid]."'");
					if($_SESSION["layout_config"]["mall_use_inventory"] == "Y"){
						$udb->query("insert into inventory_input_history (pid,pname,oid,input_type,input_msg,input_company,input_owner,input_totalsize,regdate) values ('".$db->dt[pid]."','".$db->dt[pname]."','".$oid."','CC','주문취소 및 반품','1','".$_SESSION["admininfo"]["charger_id"]."','".$db->dt[pcnt]."',NOW())");
						$udb->query("select h_ix from inventory_input_history where h_ix = LAST_INSERT_ID()");
						$udb->fetch();
						$sql = "insert into inventory_input_history_detail (hix,pid,option_name,input_inventory,input_size) values ('".$udb->dt[h_ix]."','".$db->dt[pid]."','".$option_text."',(select inventory_info from inventory_output_history where oid = '".$oid."' and pid = '".$pid."' and option_id = '".$option_id."'),'".$db->dt[pcnt]."')";
						$udb->query($sql);
					}

					$udb->query("update ".TBL_SHOP_ORDER." set order_cancel_price = order_cancel_price + ".$ptprice." where oid = '$oid' ");
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_RETURN_COMPLETE){
					// 주문취소 완료시 적립금 적립 취소
					//
					$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid='$oid' and pid = '$pid' and state = '".RESERVE_STATUS_READY."' ");

					if($db->dt[option1] != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock,po.option_div,pos.option_name from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$db->dt[option1]."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);

							if(($udb->dt[option_stock] +1) > $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'S' ";
							}
							$option_text = $udb->dt[option_name]." : ".$udb->dt[option_div];
							$option_id = $udb->dt[id];
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$db->dt[pcnt]." WHERE id = '".$udb->dt[id]."'");
						}
					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$db->dt[pcnt]." $stock_update WHERE id = '".$db->dt[pid]."'");
					if($_SESSION["layout_config"]["mall_use_inventory"] == "Y"){
						$udb->query("insert into inventory_input_history (pid,pname,oid,input_type,input_msg,input_company,input_owner,input_totalsize,regdate) values ('".$db->dt[pid]."','".$db->dt[pname]."','".$oid."','CC','주문취소 및 반품','1','".$_SESSION["admininfo"]["charger_id"]."','".$db->dt[pcnt]."',NOW())");
						$udb->query("select h_ix from inventory_input_history where h_ix = LAST_INSERT_ID()");
						$udb->fetch();
						$sql = "insert into inventory_input_history_detail (hix,pid,option_name,input_inventory,input_size) values ('".$udb->dt[h_ix]."','".$db->dt[pid]."','".$option_text."',(select inventory_info from inventory_output_history where oid = '".$oid."' and pid = '".$pid."' and option_id = '".$option_id."'),'".$db->dt[pcnt]."')";
						$udb->query($sql);
					}

					$udb->query("update ".TBL_SHOP_ORDER." set order_return_price = order_return_price + ".$ptprice." where oid = '$oid' ");
				}else{
					$dc_date_str = "";
				}
				$db->query("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status = '$status' $deliverycode_string $dc_date_str $di_date_str WHERE od_ix ='".$od_ix[$i]."' ");//oid='$oid' and
				$status_message = $admininfo[charger]."(".$admininfo[charger_id].")";

				if($status != ORDER_STATUS_INCOM_READY || $status != ORDER_STATUS_INCOM_COMPLETE){
					$db->query("insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, pid, status, status_message, company_id,quick,invoice_no, regdate ) values ('','$oid','".$pid."','$status','$status_message','".$admininfo[company_id]."','$quick','$deliverycode',NOW())");
				}else{
					$dc_date_str = "";
				}
				$db->query("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status = '$status' $deliverycode_string $dc_date_str $di_date_str WHERE od_ix ='".$od_ix[$i]."' ");//oid='$oid' and
				$status_message = $admininfo[charger]."(".$admininfo[charger_id].")";

				/*if($status != ORDER_STATUS_INCOM_READY || $status != ORDER_STATUS_INCOM_COMPLETE){
					$db->query("insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, pid, status, status_message, company_id, regdate ) values ('','$oid','".$pid."','$status','$status_message','".$admininfo[company_id]."',NOW())");
				}*/
			}

			if($status == ORDER_STATUS_CANCEL_COMPLETE){
					$sql = "select
					sum(case when oid='$oid' then 1 else 0 end) as whole_total ,
					sum(case when company_id ='".$admininfo[company_id]."' then 1 else 0 end) as admin_total ,
					sum(case when company_id != '".$admininfo[company_id]."' then 1 else 0 end) as etc_total ,
					sum(case when status = '$status' then 1 else 0 end) as status_total
					from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'";
					$db->query($sql);
					$db->fetch();
					$whole_total = $db->dt[whole_total];
					$admin_total = $db->dt[admin_total];
					$etc_total = $db->dt[etc_total];
					$status_total = $db->dt[status_total];

					if(count($od_ix) == $whole_total || $whole_total == $status_total){
						$udb->query("UPDATE ".TBL_SHOP_ORDER." SET status = '$status'  WHERE oid ='".$oid."' ");
						$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");
					}
			}

		}
	}else if($admininfo[admin_level] == 8){


			if($deliverycode){
				$deliverycode_string = " ,  invoice_no='$deliverycode',quick = '$quick' " ;
			}

			$udb = new Database;

			for($i=0;$i < count($od_ix);$i++){
				//echo ("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET invoice_no='$deliverycode' WHERE oid='$oid' and pid ='".$pid[$i]."' ");
				$db->query("select * from ".TBL_SHOP_ORDER_DETAIL." where  od_ix ='".$od_ix[$i]."' ");//oid='$oid' and
				$db->fetch();
				$pid = $db->dt[pid];
				$ptprice = $db->dt[ptprice];
				if($db->dt[status] != $status && $status == ORDER_STATUS_DELIVERY_COMPLETE){
					$dc_date_str = " , dc_date = NOW() ";
					$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_COMPLETE."' WHERE oid='$oid' and state = '".RESERVE_STATUS_READY."' and pid = '$pid' ");
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_DELIVERY_ING){
					$di_date_str = " , di_date = NOW() ";
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_CANCEL_COMPLETE){
					// 주문취소 완료시 적립금 적립 취소
					//
					$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid='$oid' and pid = '$pid' and state = '".RESERVE_STATUS_READY."' ");

					if($db->dt[option1] != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$db->dt[option1]."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$db->dt[pcnt]." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] +1) > $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'S' ";
							}
						}
					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$db->dt[pcnt]." $stock_update WHERE id = '".$db->dt[pid]."'");
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_RETURN_COMPLETE){
					// 주문취소 완료시 적립금 적립 취소
					//
					$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid='$oid' and pid = '$pid' and state = '".RESERVE_STATUS_READY."' ");

					if($db->dt[option1] != 0){
						// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
						$udb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$db->dt[option1]."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
						if($udb->total){
							$udb->fetch($i);
							$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$db->dt[pcnt]." WHERE id = '".$udb->dt[id]."'");
							if(($udb->dt[option_stock] +1) > $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'N' ";
							}else if(($udb->dt[option_stock] +1) < $udb->dt[option_safestock]){
								$stock_update = " , option_stock_yn = 'S' ";
							}
						}
					}
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$db->dt[pcnt]." $stock_update WHERE id = '".$db->dt[pid]."'");
					$udb->query("update ".TBL_SHOP_ORDER." set order_return_price = order_return_price + ".$ptprice." where oid = '$oid' ");
				}else if($db->dt[status] != $status && $status == ORDER_STATUS_REFUND_APPLY){
					$db2->query("select * from ".TBL_SHOP_ORDER_DETAIL." where  od_ix ='".$od_ix[$i]."' ");

					$order_details = $db2->fetchall();
					$mall_info = getcominfo();
					sendMessageByStep('return_order', $mail_info);

				}else{
					$dc_date_str = "";
				}

				$db->query("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status = '$status' $deliverycode_string $dc_date_str $di_date_str WHERE od_ix ='".$od_ix[$i]."' ");	//oid='$oid' and
				$status_message = $admininfo[charger]."(".$admininfo[charger_id].")";

				$db->query("insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, pid, status, status_message, company_id,quick,invoice_no, regdate ) values ('','$oid','".$pid."','$status','$status_message','".$admininfo[company_id]."','$quick','$deliverycode',NOW())");

				if($status != $bstatus && $status == ORDER_STATUS_DELIVERY_COMPLETE){
					$db->query("update ".TBL_SHOP_RESERVE_INFO." set state = '".RESERVE_STATUS_COMPLETE."' WHERE oid='$oid' and pid ='".$pid."' and state = '".RESERVE_STATUS_READY."' ");
				}
			}

			if($status == ORDER_STATUS_CANCEL_COMPLETE){
					$sql = "select
					sum(case when oid='$oid' then 1 else 0 end) as whole_total ,
					sum(case when company_id ='".$admininfo[company_id]."' then 1 else 0 end) as admin_total ,
					sum(case when company_id != '".$admininfo[company_id]."' then 1 else 0 end) as etc_total ,
					sum(case when status = '$status' then 1 else 0 end) as status_total
					from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'";
					$db->query($sql);
					$db->fetch();
					$whole_total = $db->dt[whole_total];
					$admin_total = $db->dt[admin_total];
					$etc_total = $db->dt[etc_total];
					$status_total = $db->dt[status_total];

					if(count($od_ix) == $whole_total || $whole_total == $status_total){
						$udb->query("UPDATE ".TBL_SHOP_ORDER." SET status = '$status'  WHERE oid ='".$oid."' ");
						$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

					}
			}


	}
/*	문제없으면 추후 삭제
	if ($status == ORDER_STATUS_DELIVERY_ING){
		$db->query("select * from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'");
		$order_details = $db->fetchall();

		$mail_info[mem_name] = $bname;
		$mail_info[mem_mail] = $bmail;
		$mail_info[mem_id] = $bname;
		$mail_info[mem_mobile] = $bmobile;

		sendMessageByStep('good_send_sucess', $mail_info);
	}else if ($status == ORDER_STATUS_CANCEL_COMPLETE){
		$db->query("UPDATE ".TBL_SHOP_RESERVE_INFO."  Set state = '".RESERVE_STATUS_ORDER_CANCEL."' WHERE oid = '$oid'");

		$udb = new Database;
		$db->query("select * from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$udb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set stock = stock + ".$db->dt[pcnt]." WHERE id = '".$db->dt[id]."'");

			if($db->dt[option1] != 0){
				// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
				$udb->query("select po.id from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$db->dt[option1]."' and pos.opn_ix = po.opn_ix and pos.option_kind = 'b'");
				if($udb->total){
					$udb->fetch($i);
					$udb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$db->dt[pcnt]." WHERE id = '".$udb->dt[id]."'");
				}
			}
		}


		$order_details = $db->fetchall();

		$mail_info[mem_name] = $bname;
		$mail_info[mem_mail] = $bmail;
		$mail_info[mem_id] = $bname;
		$mail_info[mem_mobile] = $bmobile;


		sendMessageByStep('order_cancel', $mail_info);

		echo("<script>alert('카드결제일 경우 [승인취소] 작업도 해주세요.');</script>");
	}
*/
	// 해당 주문에 대한 상태 정보를 저장한다.




	//echo("<script>location.href = 'orders.list.php?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."';</script>");
	echo("<script>parent.location.reload();</script>");
}

if ($act == "stateupdate"){
	$db->query("UPDATE ".TBL_SHOP_ORDER." SET status='".ORDER_STATUS_RETURN_COMPLETE."' WHERE oid='$oid'");

//	$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO."  (id,uid,oid,pid,ptprice,payprice,reserve,state,regdate) VALUES ('','".$user[code]."','$oid','$pid','$sum','".($sum-$order[reserve_price])."','-".$order[reserve_price]."','4',NOW())");
	//$db->query("Update ".TBL_SHOP_RESERVE_INFO."  set state = '5' where oid = '$oid'"); // state :  5 -> 반품;

	$db->query("SELECT * FROM ".TBL_SHOP_RESERVE_INFO."  WHERE oid = '$oid' and state = '".RESERVE_STATUS_RETURN."'");
	if($db->total == 0){
		$db->query("SELECT * FROM ".TBL_SHOP_RESERVE_INFO."  WHERE oid = '$oid'");
		$db->fetch();
		$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO."  (id,uid,oid,pid,ptprice,payprice,reserve,state,regdate) VALUES ('','".$db->dt[uid]."','".$db->dt[oid]."','".$db->dt[pid]."','0','0','-".$db->dt[reserve]."','5',NOW())");
	}

	// 해당 주문에 대한 상태 정보를 저장한다.
	$db->query("insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, status, status_message, regdate ) values ('','$oid','$status','카드구매',NOW())");

	echo("<script>alert('반품처리가 완료되었습니다.');document.location.href='orders.list.php'</script>");
}

if ($act == "select_status_update"){
	//print_r($_POST);
	if($admininfo[admin_level] == 9){
		for($i=0;$i < count($oid);$i++){
			// 주문 테이블의 상태를 변경하기전에 이전상태를 저장
			$status_message = $admininfo[charger]."(".$admininfo[charger_id].")";
			if($status == ORDER_STATUS_INCOM_READY || $status == ORDER_STATUS_INCOM_COMPLETE){


				// 상품 상태를 변경 -- 입금예정과 입금 확인 상태만 배송상태 남김
				$db->query("UPDATE ".TBL_SHOP_ORDER." SET status='$status' WHERE oid='".$oid[$i]."'");
				$db->query("insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, status, status_message, regdate ) values ('','".$oid[$i]."','$status','일괄배송처리',NOW())");

				if($status == ORDER_STATUS_INCOM_COMPLETE){
					$db->query("select oid, uid, rmobile from ".TBL_SHOP_ORDER."  WHERE oid='".$oid[$i]."'");
					$db->fetch();
					$order = $db->dt;

					$db->query("select oid, hotcon_event_id, hotcon_pcode, pcnt from ".TBL_SHOP_ORDER_DETAIL." od WHERE oid='".$oid[$i]."'");
					for($i=0;$i < $db->total;$i++){
						$db->fetch($i);
						if($db->dt[hotcon_event_id] && $db->dt[hotcon_pcode]){
							CallHotCon($order[uid], $order[oid], $db->dt[pid], $db->dt[hotcon_event_id], $db->dt[hotcon_pcode], $db->dt[pcnt], $order[rmobile]);
						}
					}
				}

			}
			if($status == ORDER_STATUS_DELIVERY_READY){
				$db->query("select oid, pid from ".TBL_SHOP_ORDER_DETAIL."  WHERE oid='".$oid[$i]."' and status in ('".ORDER_STATUS_INCOM_COMPLETE."')");
				$o_details = $db->fetchall();
				for($j=0;$j < count($o_details);$j++){
					$db->query("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status='".$status."' WHERE oid='".$o_details[$j][oid]."' and pid='".$o_details[$j][pid]."' ");
					$db->query("insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, pid, status, status_message, regdate ) values ('','".$o_details[$j][oid]."','".$o_details[$j][pid]."','$status','$status_message',NOW())");
				}
			}else{
				$db->query("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status='".$status."' WHERE oid='".$oid[$i]."' ");
			}

		}
	}else if($admininfo[admin_level] == 8){
		for($i=0;$i<count($oid);$i++){
			// 주문 테이블의 상태를 변경하기전에 이전상태를 저장

			if($status == ORDER_STATUS_INCOM_READY || $status == ORDER_STATUS_INCOM_COMPLETE){
				// 상품 상태를 변경 -- 입금예정과 입금 확인 상태만 배송상태 남김
				//$db->query("UPDATE ".TBL_SHOP_ORDER." SET status='$status' WHERE oid='".$oid[$i]."'");
			}
			if($status == ORDER_STATUS_DELIVERY_READY){
				$db->query("select oid, pid from ".TBL_SHOP_ORDER_DETAIL."  WHERE oid='".$oid[$i]."' and status in ('".ORDER_STATUS_INCOM_COMPLETE."') and company_id ='".$admininfo[company_id]."'");
				$o_details = $db->fetchall();

				for($j=0;$j < count($o_details);$j++){
					$db->query("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status='".$status."' WHERE oid='".$o_details[$j][oid]."' and pid='".$o_details[$j][pid]."' ");
					$db->query("insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, pid, status, status_message, regdate ) values ('','".$o_details[$j][oid]."','".$o_details[$j][pid]."','$status','$status_message',NOW())");
				}
			//}else{
			//	$db->query("UPDATE ".TBL_SHOP_ORDER_DETAIL." SET status='".$status."' WHERE oid='".$oid[$i]."' ");
			}

		}
	}


	echo "<script language='javascript'>alert(' 선택된 주문에 대한 상태가 정상적으로 변경되었습니다..');parent.document.location.reload();</script>";

	//header("Location:../product_list.php");
}


if ($act == "delete"){
	$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE oid='$oid'");
	$db->query("DELETE FROM ".TBL_SHOP_ORDER_DETAIL." WHERE oid='$oid'");
	$db->query("DELETE FROM ".TBL_SHOP_ORDER_STATUS." WHERE oid='$oid'");
	$db->query("DELETE FROM shop_order_memo WHERE oid='$oid' ");
	echo("<script>top.location.href = 'orders.list.php?page=$page';</script>");
}



if ($act == "send_mail"){
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");


	$db->query("Select bmail, bname, uid FROM ".TBL_SHOP_ORDER." WHERE oid = '".$oid."'");
	$db->fetch();

	$mail_info[mem_name] = $db->dt[bname];
	$mail_info[mem_mail] = $db->dt[bmail];
	$mail_info[mem_id] = $db->dt[bname];
	$email_card_contents_basic = "요청하신 견적서입니다";

	copy("http://".$HTTP_HOST."../order/taxbill.php?mode=excel&oid=".$oid."&uid=".$db->dt[uid]."",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/taxbill.xls");

	$subject = " ".$mail_info[mem_name]." 님, 요청하신 견적서 입니다..";
	SendMail($mail_info, $subject,$email_card_contents_basic,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/taxbill.xls");


	//echo $mail_info[mem_mail];
	echo("<script>alert('정상적으로 메일이 발송되었습니다.');</script>");
	echo("<script>self.close();</script>");
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
