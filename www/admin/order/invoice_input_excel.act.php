<?php
	include("../class/layout.class");
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
	include("../lib/imageResize.lib.php");
	include '../product/Excel/reader.php';
	include("../inventory/inventory.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");

	$removestring = array("\n", "\t");

	$ADMIN_MESSAGE = $_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")";

	if($admininfo[company_id] == ""){
		echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='../'</script>";
		exit;
	}

	$db = new Database;
	//$db2 = new Database;

	if($act == "check_excel_input"){
		
		if ($excel_file_size > 0){
			copy($excel_file, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
		}
		
		$data = new Spreadsheet_Excel_Reader();

		$data->read($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
		
		echo "<script type='text/javascript'>
		<!--
			if(confirm('총 ".($data->sheets[0]['numRows'] - 1)."건의 주문을 ".($update_status=='DI' ? "배송중 상태로 변경" : "송장번호 입력")." 하시겠습니까? ')){
				var frm = parent.document.excel_input_form;
				frm.act.value='excel_input';
				frm.submit();
			}
		//-->
		</script>";
	}


	if ($act == "excel_input"){

		$data = new Spreadsheet_Excel_Reader();

		$data->read($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);

		error_reporting(E_ALL ^ E_NOTICE);
		$shift_num = 0;

		//$db->debug = true;
		$order_check_bool = true;

		$fail_cnt=0;
		$success_cnt=0;
		$od_ix_array = array();

		for ($x = 2; $x <= $data->sheets[0]['numRows']; $x++) {
			//////////////////////////// 데이터를 가져옴 //////////////////////////////////

			$oid = trim($data->sheets[0]['cells'][$x][1+$shift_num]);
			$od_ix = trim($data->sheets[0]['cells'][$x][2+$shift_num]);
			$delivery_company = trim($data->sheets[0]['cells'][$x][3+$shift_num]);
			$deliverycode = trim($data->sheets[0]['cells'][$x][4+$shift_num]);


			//echo "테스트 입니다.";
			//exit;

			//echo $oid."::".$od_ix.":::".$delivery_company.":::".$deliverycode;
			//exit;
			if(!$oid){
				$result_status .= "<span style='color:red'>[실패]</span> 주문번호가 입력되지 않았습니다..<br>";
				$order_check_bool = false;
			}else{
				if(strlen($oid) != 20){
					$result_status .= "<span style='color:red'>[실패]</span> 주문번호가 정확하지 않았습니다..".strlen($oid)."<br>";
					$order_check_bool = false;
				}
			}

			if(!$od_ix){
				$result_status = "<span style='color:red'>[실패]</span> 주문상세코드가 입력되지 않았습니다..<br>";
				$order_check_bool = false;
			}

			if(!$delivery_company){
				$result_status .= "<span style='color:red'>[실패]</span> 택배사 코드가 입력되지 않았습니다..<br>";
				$order_check_bool = false;
			}

			if(!$deliverycode){
				$result_status .= "<span style='color:red'>[실패]</span> 운송장 번호가 입력되지 않았습니다..<br>";
				$order_check_bool = false;
			}

			if( ($admininfo[admin_level] != 9 && ($oid != "" && $delivery_company != "" && $deliverycode != "")) || ($admininfo[admin_level] == 9 && $oid != "")){

				if($admininfo[admin_level] == 9){
					if($position == 'overseas'){
						$sql="select * from ".TBL_SHOP_ORDER_DETAIL." where oid='".$oid."' and od_ix = '".$od_ix."' and status in('".ORDER_STATUS_AIR_TRANSPORT_READY."') ";
					}else{
						$sql="select * from ".TBL_SHOP_ORDER_DETAIL." where oid='".$oid."' and od_ix = '".$od_ix."' and status in('".ORDER_STATUS_DELIVERY_READY."') ";
					}

				}else{
					if($position == 'overseas'){
						$sql="select * from ".TBL_SHOP_ORDER_DETAIL." where oid='".$oid."' and od_ix = '".$od_ix."' and status in('".ORDER_STATUS_AIR_TRANSPORT_READY."') and company_id = '".$admininfo[company_id]."' ";
					}else{
						$sql="select * from ".TBL_SHOP_ORDER_DETAIL." where oid='".$oid."' and od_ix = '".$od_ix."' and status in('".ORDER_STATUS_DELIVERY_READY."')  and company_id = '".$admininfo[company_id]."'  ";
					}
				}

				$db->query($sql);
				$order_details = $db->fetchall();

				if($db->total){

					//echo $sql;
					//exit;
					for($i=0;$i < count($order_details);$i++){

						$sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix='".$order_details[$i][od_ix]."'  ";
						$db->query($sql);
						$db->fetch();
						$pid = $db->dt[pid];
						$status = $db->dt[status];
						$company_id = $db->dt[company_id];
						
						if($update_status==ORDER_STATUS_DELIVERY_ING){
							$update_str=" status = '".ORDER_STATUS_DELIVERY_ING."' , di_date=NOW(), ";
							$msg="엑셀 송장번호입력 후 배송중 처리";
						}else{
							$update_str="";
							$msg="엑셀 송장번호입력 처리";
						}

						if($position == 'overseas'){
							if($status != ORDER_STATUS_AIR_TRANSPORT_READY){
								$result_status .= "<span style='color:red'>[실패]</span> 항공배송준비중이 아닙니다. ".$status." ::: ".ORDER_STATUS_AIR_TRANSPORT_READY."<br>";
								$order_check_bool = false;
							}
						}else{
							if($status != ORDER_STATUS_DELIVERY_READY){
								$result_status .= "<span style='color:red'>[실패]</span> 배송준비중이 아닙니다. ".$status." ::: ".ORDER_STATUS_DELIVERY_READY." <br>";
								$order_check_bool = false;
							}
						}

						if($admininfo[admin_level] <9){
							if($company_id != $admininfo[company_id]){
								$result_status .= "<span style='color:red'>[실패]</span> 해당업체의 주문정보가 아닙니다. ".$company_id." ::: ".$admininfo[company_id]."<br>";
								$order_check_bool = false;
							}
						}

                        // 주문정보의 현재상태(배송준비중)는 위에서 따로 조건처리 해주기 때문에 조건에 포함시키지 않는다.
                        if($order_check_bool && $update_status == ORDER_STATUS_DELIVERY_ING){
                            $goodsflow_quick = $delivery_company;
                            $goodsflow_invoice_no = $deliverycode;

                            if($delivery_company != '40'){
                                if(function_exists('sellerToolUpdateOrderStatus')){
                                    $goodsflow_result = sellerToolUpdateOrderStatus(ORDER_STATUS_DELIVERY_ING, $od_ix, '', true, $goodsflow_quick, $goodsflow_invoice_no);

                                    if($goodsflow_result != 'success'){
                                        $result_status .= "<span style='color:red'>[실패]</span> 굿스플로 연동에 실패했습니다. ".$oid." ::: ".$od_ix."<br>";
                                        $order_check_bool = false;

                                        $FailResult[$f][oid] = $oid;
                                        $FailResult[$f][od_ix] = $od_ix;
                                        $FailResult[$f][delivery_company] = $delivery_company;
                                        $FailResult[$f][deliverycode] = $deliverycode;
                                    }
                                }
                            }
                        }
						
						if($order_check_bool){

								$order_detail_info = $db->dt;

								$od_ix_array[] = $order_details[$i][od_ix];

								if($status == ORDER_STATUS_AIR_TRANSPORT_READY){//항공배송준비중

									$sql="update ".TBL_SHOP_ORDER_DETAIL." set
										status = '".ORDER_STATUS_AIR_TRANSPORT_ING."',
										quick = '".$delivery_company."',
										invoice_no = '".$deliverycode."',
										".$update_str."
										update_date = NOW()
										where od_ix='".$order_details[$i][od_ix]."' ";

								}else{
									$sql="update ".TBL_SHOP_ORDER_DETAIL." set
										quick = '".$delivery_company."',
										invoice_no = '".$deliverycode."',
										".$update_str."
										update_date = NOW()
										where od_ix='".$order_details[$i][od_ix]."' ";
								}
								$db->query($sql);


								if($status == ORDER_STATUS_AIR_TRANSPORT_READY){//항공배송준비중
									set_order_status($order_details[$i][oid],$status,"항공배송중 출고",$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"",$delivery_company,$deliverycode);
								}else{
									if($update_status==ORDER_STATUS_DELIVERY_ING){
										set_order_status($order_details[$i][oid],$update_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"",$delivery_company,$deliverycode);
									}else{
										set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"",$delivery_company,$deliverycode);
									}
								}

								// 없어서 추가 2012-09-21 홍진영
								if($update_status==ORDER_STATUS_DELIVERY_ING){

									//제휴사 주문 상태 연동
									$sellertool_result = "success";
									if(function_exists('sellerToolUpdateOrderStatus')){
										$sellertool_result = sellerToolUpdateOrderStatus(ORDER_STATUS_DELIVERY_ING,$order_details[$i][od_ix]);
									
										if( $sellertool_result != "success" ){
											//실패시 다시 전 상태로
											$sql="update ".TBL_SHOP_ORDER_DETAIL." set
											update_date = NOW()
											,status= '".$order_details[$i][status]."'
											,delivery_status= '".$order_details[$i][delivery_status]."'
											where od_ix='".$order_details[$i][od_ix]."'";
											$db->query($sql);

											$result_status .= "<span style='color:red'>[실패]</span> 제휴 연동 실패 재시도 바랍니다.<br>";
											$fail_cnt++;
										}else{
											$result_status .= "<span style='color:blue'>[성공]</span> 처리완료<br>";
											$success_cnt++;
										}
									}else{
										$result_status .= "<span style='color:blue'>[성공]</span> 처리완료<br>";
										$success_cnt++;
									}

									if( $sellertool_result == "success" ){
										UpdateProductCnt_complete($order_details[$i]);
									}
								}else{
									$result_status .= "<span style='color:blue'>[성공]</span> 처리완료<br>";
									$success_cnt++;
								}
						}else{
							$fail_cnt++;
						}
					}

				}else{
					$result_status .= "<span style='color:red'>[실패]</span> 해당 주문이 없습니다.<br>";
					$fail_cnt++;
				}

				if($rownum > 500){
					//exit;
				}
			}

			$result_inner .= "<tr height=25 bgcolor=#ffffff align=center><td class='point'>".$oid."</td><td >".$od_ix."</td><td >".$delivery_company."</td><td >".$deliverycode."</td><td  class='point' style='text-align:left;padding:10px;line-height:140%;'>".$result_status."</td></tr>";
			unset($result_status);
			$order_check_bool = true;

			/////////////////////////////////////////////////////////////////////////////////
			$rownum++;
		}

		if(!empty($result_inner)){
			$result_top = "<table width=100%  border=0 style='padding-bottom:10px;'>
									<tr height=25><td style='border-bottom:2px solid #efefef'>	<img src='../images/dot_org.gif' align=absmiddle> <b>일괄송장입력 결과 [성공 : <span class='blue'>".$success_cnt."</span>건 실패 : <span class='red'>".$fail_cnt."</span>건]</b></td></tr>
									<tr height=25>	<td ><table width=100%  border=0 cellpadding=0 cellspacing=1 class='list_table_box'><tr height=25 bgcolor=#ffffff align=center><td class='s_td'>주문번호</td><td class='s_td'>주문상세코드</td><td class='m_td'>택배사 코드</td><td class='m_td'>운송장번호</td><td class='e_td'>상태</td></tr>";
			$result_bottom = "</table></td></tr></table>";

			$result_str = $result_top.$result_inner.$result_bottom;

			//$result_str = str_replace("'","\'",$result_str);
			//echo $result_str;
			echo "<body><div id='excel_process_result'>$result_str</div><body>";

			echo("<script src='/admin/js/jquery-1.8.3.js'></script><script language='javascript' src='../js/message.js.php'></script><script>show_alert('일괄 송장입력 처리가 정상적으로 처리되었습니다');$('#result_area', parent.document).html($('#excel_process_result').html());</script>");
			
			if($update_status==ORDER_STATUS_DELIVERY_ING){
				$sql="select DISTINCT oid, odd_ix from ".TBL_SHOP_ORDER_DETAIL." where od_ix in ('".implode("','",$od_ix_array)."') $and_company_id";
				$db->query($sql);
				$order_infos = $db->fetchall("object");

				for($i=0;$i < count($order_infos);$i++){

					//$db->query("select * from ".TBL_SHOP_ORDER." WHERE oid='".$order_infos[$i][oid]."'");
					$db->query("select od.invoice_no, od.pname, od.quick , odd.oid, odd.rname, odd.rmobile, concat(odd.addr1,' ',odd.addr2) as addr, odd.msg , date_format(o.order_date,'%Y-%m-%d') as order_date, o.bname, o.bmail, o.bmobile from shop_order_detail_deliveryinfo odd left join shop_order o on (odd.oid=o.oid) left join shop_order_detail od on (o.oid=od.oid) WHERE odd.oid='".$order_infos[$i][oid]."' and odd.odd_ix='".$order_infos[$i][odd_ix]."' and od.od_ix in ('".implode("','",$od_ix_array)."') ");
					$order = "";
					$order = $db->fetch();

					$db->query("select *, pid as id from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$order_infos[$i][oid]."' and status= '".ORDER_STATUS_DELIVERY_ING."' AND od_ix in ('".implode("','",$od_ix_array)."') and product_type not in ('77') $and_company_id");
					$order_details="";
					$order_details = $db->fetchall("object");

					if($order_details[0][order_from] == 'self'){
						$mail_info[mem_name] = $order[bname];
						$mail_info[mem_mail] = $order[bmail];
						$mail_info[mem_id] = $order[bname];
						$mail_info[mem_mobile] = $order[bmobile];
						$mail_info[invoice] = $order[invoice_no];
						$mail_info[pname] = mb_substr($order[pname],0,15,"utf-8").(count($order_details)>1 ? " 외 ".(count($order_details)-1)."건" : "");
						$mail_info[quick] = deliveryCompany($order[quick]);
						$mail_info[msg_code]	=	'0301'; // MSG 발송코드 0301 : 상품발송
						sendMessageByStep('admin_ms_email_good_send_sucess', $mail_info);
					}
				}
			}

		}else{
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('엑셀파일 형식을 다시 확인해주세요. 처리할 목록이 없습니다.');parent.document.location.reload();</script>");
		}

	}