<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include("../order/orders.lib.php");

$db = new Database;
$master_db = new Database;
$master_db->master_db_setting();


$Script = "
<script language='javascript' src='../order/refund_price_give.js'></script>
<script type='text/javascript'>
<!--

//-->

function showSelfArea(obj){
	if($(obj).val() == ''){
		$('.regist_self_accnts').show();
		$('select[name=refund_bank_code]').attr('validation', 'true');
		$('input[name=refund_bank_number]').attr('validation', 'true');
		$('input[name=refund_bank_owner]').attr('validation', 'true');
	}else{
		$('.regist_self_accnts').hide();
		$('select[name=refund_bank_code]').attr('validation', 'false');
		$('input[name=refund_bank_number]').attr('validation', 'false');
		$('input[name=refund_bank_owner]').attr('validation', 'false');
	}
}
</script>";



$sql = "SELECT company_id FROM common_company_detail where com_type='A' ";
$db->query($sql);
$db->fetch();
$a_company_id=$db->dt[company_id];

$sql = "SELECT o.user_code as user_id, o.buserid, o.bname, o.refund_method, 
            AES_DECRYPT(UNHEX(o.refund_bank),'".$db->ase_encrypt_key."') as refund_bank, 
            AES_DECRYPT(UNHEX(o.refund_bank_name),'".$db->ase_encrypt_key."') as refund_bank_name, odd.rname, gi.gp_name 
        FROM shop_order o 
        left join shop_order_detail_deliveryinfo odd on (o.oid=odd.oid and order_type='1') 
		left join common_member_detail cmd on o.user_code = cmd.code
		left join shop_groupinfo gi on cmd.gp_ix = gi.gp_ix
       where o.oid='".$oid."' ";
$db->query($sql);
$db->fetch();
$order_info = $db->dt;


//현제 주문의 결제 현황
$sql="SELECT 
	method,
	case method when '".ORDER_METHOD_CART_COUPON."' then '0' when '".ORDER_METHOD_CARD."' then '1' when '".ORDER_METHOD_ICHE."' then '2' 
	when '".ORDER_METHOD_VBANK."' then '3' when '".ORDER_METHOD_BANK."' then '4' when '".ORDER_METHOD_MOBILE."' then '5' 
	when '".ORDER_METHOD_PHONE."' then '6' when '".ORDER_METHOD_SAVEPRICE."' then '7' when '".ORDER_METHOD_RESERVE."' then '8'
	when '".ORDER_METHOD_NPAY."' then '6' when '".ORDER_METHOD_KAKAOPAY."' then '6' when '".ORDER_METHOD_TOSS."' then '6' when '".ORDER_METHOD_PAYCO."' then '6'
	else '9' end as vieworder,
	SUM(case when pay_type='F' then -tax_price else tax_price end) as tax_price,
	SUM(case when pay_type='F' then -tax_free_price else tax_free_price end) as tax_free_price,
	SUM(case when pay_type='F' then -payment_price else payment_price end) as payment_price
FROM 
	shop_order_payment
WHERE
	oid = '".$oid."'
AND 
	pay_status= '".ORDER_STATUS_INCOM_COMPLETE."' 
GROUP BY
	method
HAVING 
	payment_price > 0
ORDER by 
	vieworder asc";
$db->query($sql);
$pay_info = $db->fetchall("object");


list($refund_info["bank_code"],$refund_info["bank_number"]) = explode("|",$order_info["refund_bank"]);
$refund_info["bank_owner"] = $order_info["refund_bank_name"];

if($order_info[user_id]!=""){
	//and b.use_yn='Y'  고객이 사용안하는 것도 노출시키기!
	$sql="SELECT bank_ix, ucode, bank_code, use_yn, is_basic, regdate, editdate,
				AES_DECRYPT(UNHEX(bank_name),'".$db->ase_encrypt_key."') as bank_name,
				AES_DECRYPT(UNHEX(bank_number),'".$db->ase_encrypt_key."') as bank_number,
				AES_DECRYPT(UNHEX(bank_owner),'".$db->ase_encrypt_key."') as bank_owner 
			FROM shop_user_bankinfo b  WHERE b.ucode ='".$order_info[user_id]."' ";
	$db->query($sql);
	$user_bankinfo = $db->fetchall("object");
}



$sql="SELECT *,pcnt as claim_apply_cnt, 'Y' as claim_apply_yn, SUBSTR(status,1,1) as claim_type, od_ix as vieworder
	FROM  shop_order_detail WHERE oid = '".$oid."' AND refund_status='".ORDER_STATUS_REFUND_APPLY."' 
	UNION
	SELECT *,pcnt as claim_apply_cnt, 'N' as claim_apply_yn, SUBSTR(status,1,1) as claim_type, claim_delivery_od_ix as vieworder
	FROM  shop_order_detail WHERE oid = '".$oid."' AND claim_delivery_od_ix in (SELECT od_ix FROM shop_order_detail WHERE  oid = '".$oid."' AND refund_status='".ORDER_STATUS_REFUND_APPLY."' AND status not in ('".ORDER_STATUS_EXCHANGE_READY."','".ORDER_STATUS_SETTLE_READY."') )
	and status != '".ORDER_STATUS_SETTLE_READY."'

	ORDER BY claim_group,vieworder,ode_ix";

$db->query($sql);
$product_info=$db->fetchall("object");

$currency_unit = check_currency_unit($product_info[0]['mall_ix']);
if($currency_unit == 'USD'){
    $decimals_value = 2;
}else{
    $decimals_value = 0;
}


$Contents = "<form name='refund_price_give_frm' method='post' action='orders.act.php' onsubmit='return refundCheck(this)' target='act'>
	<input type='hidden' name='act' value='part_cancel'>
	<input type='hidden' name='oid' value='".$oid."'>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
						<table border='0' width='100%' cellspacing='1' cellpadding='0' >
							<tr>
								<td>
									<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b>주문정보</b></div>
								</td>
							</tr>
							<tr>
								<td>
									<table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;' >
										<col width='*'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>
										<tr>
											<td class='m_td' style='text-align:center'>  주문번호</td>
											<td class='m_td' style='text-align:center'>  판매처</td>
											<td class='m_td' style='text-align:center'>  주문자</td>
											<td class='m_td' style='text-align:center'>  수취인</td>
										</tr>
										<tr>
											<td class='input_box_item' style='text-align:center'>
												".$oid."
											</td>
											<td class='input_box_item' style='text-align:center'>
												".getOrderFromName($product_info[0][order_from])."";

											if($product_info[0][co_oid]!=''){
												$Contents .= "<br/>( " . $product_info[0][co_oid] . " )";
											}

											$Contents .= "
											</td>
											<td class='input_box_item' style='text-align:center'>
												".$order_info[bname].( $order_info[buserid] ? "(<span class='small'>".$order_info[buserid]."</span>)<br>(".$order_info[gp_name].")" : "(<span class='small'>비회원</span>)")."
											</td>
											<td class='input_box_item' style='text-align:center'>
												".$order_info[rname]."
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<div style='padding:15px 5px 5px 5px'><img src='../images/dot_org.gif' align='absmiddle'> <b>실결제금액</b></div>
								</td>
							</tr>
							<tr>
								<td>
									<table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;' >
										<col width='*'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>
										<tr>
											<td class='m_td' style='text-align:center'>  결제방법</td>
											<td class='m_td' style='text-align:center'>  과세금액</td>
											<td class='m_td' style='text-align:center'>  면세금액</td>
											<td class='m_td' style='text-align:center'>  총결제금액</td>
										</tr>";
										$refund_bank_bool=false;
										$cart_coupon_bool=false;
										if ( ! empty( $pay_info) ) {
											foreach($pay_info as $key => $pi){
												
												if($pi["method"]==ORDER_METHOD_VBANK || $pi["method"]==ORDER_METHOD_ICHE || $pi["method"]==ORDER_METHOD_BANK || $pi["method"]==ORDER_METHOD_ASCROW){
													$refund_bank_bool=true;
													if($pi["method"]==ORDER_METHOD_VBANK || $pi["method"]==ORDER_METHOD_ASCROW)	$refund_validation="true";
													else																		$refund_validation="false";
												}

												if($pi["method"]==ORDER_METHOD_CART_COUPON){
													$cart_coupon_bool=true;
													$cart_tax_price = $pi["tax_price"];
													$cart_tax_free_price = $pi["tax_free_price"];
													$cart_payment_price += $pi["payment_price"];
												}

												$total_tax_price += $pi["tax_price"];
												$total_tax_free_price += $pi["tax_free_price"];
												$total_payment_price += $pi["payment_price"];
												$max_method_price[$pi["method"]] += $pi["payment_price"];
	
												$Contents .= "
												<tr>
													<td class='input_box_item' style='text-align:center'>
														".getMethodStatus($pi["method"])."
													</td>
													<td class='input_box_item' style='text-align:center'>
														".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($pi["tax_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
													</td>
													<td class='input_box_item' style='text-align:center'>
														".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($pi["tax_free_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
													</td>
													<td class='input_box_item' style='text-align:center'>
														".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($pi["payment_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
													</td>
												</tr>";
												
											}
										}

									$Contents .= "
										<tr>
											<td class='input_box_item' style='text-align:center'>
												합계
											</td>
											<td class='input_box_item' style='text-align:center'>
												".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($total_tax_price,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
											</td>
											<td class='input_box_item' style='text-align:center'>
												".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($total_tax_free_price,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
											</td>
											<td class='input_box_item' style='text-align:center'>
												".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($total_payment_price,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<div style='padding:15px 5px 5px 5px'><img src='../images/dot_org.gif' align='absmiddle'> <b>상품정보</b></div>
								</td>
							</tr>
							<tr>
								<td>
									<table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;' >
										<col width='*'>
										<col width='8%'>
										<col width='8%'>
										<col width='10%'>
										<col width='6%'>
										<col width='10%'>
										<col width='10%'>
										<col width='8%'>
										<col width='12%'>
										<tr>
											<td class='m_td' style='text-align:center'>  상품명/옵션</td>
											<td class='m_td' style='text-align:center'>  과세구분</td>
											<td class='m_td' style='text-align:center'>  환불상태</td>
											<td class='m_td' style='text-align:center'>  판매가</td>
											<td class='m_td' style='text-align:center'>  수량</td>
											<td class='m_td' style='text-align:center'>  결제금액<br/>(할인금액)</td>
											<td class='m_td' style='text-align:center'>  환불상품금액<br/>(변동금액)</td>
											<td class='m_td' style='text-align:center'>  배송비</td>
											<td class='m_td' style='text-align:center'>  환불배송비<br/>(변동금액)</td>
										</tr>";
							
							//로스판 계산!
							for($i=0;$i<count($product_info);$i++){
								$comp_row_cnt[$product_info[$i][company_id]]++;
								$ode_ix_row_cnt[$product_info[$i][ode_ix]]++;
								$claim_group_row_cnt[$product_info[$i][claim_group]]++;
							}

							//환불금액가지고오기!
							$refund_data = clameChangePriceCalculate($product_info);

							for($i=0;$i<count($product_info);$i++){
								
								if($product_info[$i][claim_apply_yn]=="N")		$sign=-1;
								else															$sign=1;
								
								 if(in_array($product_info[$i][product_type],$sns_product_type)){
									$folder_name = "sns";
								 }else{
									$folder_name = "product";
								}

								$sql="select * from shop_order_detail_discount where od_ix='".$product_info[$i][od_ix]."' ";
								$db->query($sql);
								if($db->total){
									$dc_info = $db->fetchall("object");
								}else{
									$dc_info = "";
								}

								$dc_coupon_info = getOrderDetailCouponDcInfo($dc_info);
								$dc_coupon_str=$dc_coupon_info["coupon_str"];
								$dc_coupon_width=$dc_coupon_info["coupon_width"];
								$dc_coupon_height=$dc_coupon_info["coupon_height"];
								
								$dc_etc_info = getOrderDetailEtcDcInfo($dc_info);
								$dc_etc_str=$dc_etc_info["etc_str"];
								$dc_etc_width=$dc_etc_info["etc_width"];
								$dc_etc_height=$dc_etc_info["etc_height"];

								$discount_info = "";
			
								if($dc_etc_str!=""){
									$discount_info.=" <label class='helpcloud' help_width='".$dc_etc_width."' help_height='".$dc_etc_height."' help_html='".$dc_etc_str."'><img src='../images/icon/q_icon.png' align=''></label>";
								}
								
								if($dc_coupon_str!=""){
									$discount_info.=" <label class='helpcloud' help_width='".$dc_coupon_width."' help_height='".$dc_coupon_height."' help_html='".$dc_coupon_str."'><img src='../images/".$admininfo[language]."/s_use_coupon.gif' align=''></label>";
								}



			$Contents .= "<tr>
									<td class='input_box_item'>
										<TABLE style='text-align:left;'>
											<TR>
												<TD align='center'>
												<a href='../".$folder_name."/goods_input.php?id=".$product_info[$i][pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $product_info[$i][pid], "m", $product_info[$i])."'  width=50 style='margin:5px;'></a><br/>";
												
												if($product_info[$i][product_type]=='21'||$product_info[$i][product_type]=='31'){
													$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($product_info[$i][product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$product_info[$i][product_type].".gif' align='absmiddle' ></label> ";
												}
												if($product_info[$i][company_id]==$a_company_id){
													$Contents .= "<label class='helpcloud' help_width='70' help_height='15' help_html='본사상품'><img src='../images/".$admininfo[language]."/s_admin_product.gif' align='absmiddle' ></label> ";
												}
												if($product_info[$i][stock_use_yn]=='Y'){
												$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
												}

							$Contents .= "
												</TD>
												<td width='5'>
												</td>
												<TD style='line-height:140%'>";

						if($b_company_id != $product_info[$i][company_id]){
							$seller_info_str= GET_SELLER_INFO($product_info[$i][company_id]);
						}

						if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
							$Contents .= "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='".$seller_info_str."'>".($product_info[$i][company_name] ? $product_info[$i][company_name]:"-")."</b> <img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow('../seller/seller_company.php?company_id=".$product_info[$i][company_id]."&mmode=pop',960,600,'brand');\"  style='cursor:pointer;'><br>";
						}

						if($product_info[$i][product_type]=='99'||$product_info[$i][product_type]=='21'||$product_info[$i][product_type]=='31'){
							$Contents .= "<b class='".($product_info[$i][product_type]=='99' ? "red" : "blue")."' >".$product_info[$i][pname]."</b><br/><strong>".$product_info[$i][set_name]."<br /></strong>".$product_info[$i][sub_pname];
						}else{
							$Contents .= $product_info[$i][pname];
						}

						if(strip_tags($product_info[$i][option_text])){
							$Contents .= "<br/> ▶ ".strip_tags($product_info[$i][option_text]);
						}

						$Contents .="
												</TD>
											</TR>
										</TABLE>
									</td>";

									switch($product_info[$i][surtax_yorn]){
										case 'N':
											$surtax_yorn = "과세";
										break;
										case 'Y':
											$surtax_yorn = "면세";
										break;
										case 'P':
											$surtax_yorn = "영세";
										break;
									}

									$Contents .="
									<td class='input_box_item' style='text-align:center'>".$surtax_yorn."</td>
									<td class='input_box_item' style='text-align:center'>".getOrderStatus($product_info[$i][refund_status])."</td>
									<td class='input_box_item' style='text-align:center'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($product_info[$i][psprice]+$product_info[$i][option_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
									<td class='input_box_item' style='text-align:center'>".$product_info[$i][pcnt]." 개</td>
									<td class='input_box_item' style='text-align:center'>
										".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($product_info[$i][pt_dcprice]*$sign,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]." ".$discount_info."
										<br/>(".$currency_display[$admin_config["currency_unit"]]["front"]."".(($product_info[$i][pt_dcprice]-$product_info[$i][ptprice])*$sign)."".$currency_display[$admin_config["currency_unit"]]["back"].")
									</td>
									<td class='input_box_item' style='text-align:center'>";
										if($product_info[$i][claim_apply_yn]=="Y"){
											$Contents .="<input type='hidden' name='od_ix[]' value='".$product_info[$i][od_ix]."'/>";
										}

										$Contents .=$currency_display[$admin_config["currency_unit"]]["front"].number_format($product_info[$i][pt_dcprice]*$sign,$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."<br/>
										(".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format(($refund_data["product"][$product_info[$i][od_ix]]["change_coupon_dcprice"])*$sign,$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."";

										if($refund_data["product"][$product_info[$i][od_ix]]["change_coupon_coment"]!=""){
											$Contents .="<img src='../v3/images/".$admininfo[language]."/btn_search.gif' align='absmiddle' class='helpcloud' help_width='200'  help_html='".$refund_data["product"][$product_info[$i][od_ix]]["change_coupon_coment"]."' style='cursor:pointer;'>";
										}
										$Contents .=")
									</td>";
									
									if($b_ode_ix!=$product_info[$i][ode_ix]){
										
										$sql="select * from shop_order_delivery where ode_ix='".$product_info[$i][ode_ix]."' ";
										$db->query($sql);
										$delivery_info = $db->fetch();

										$Contents .="
										<td class='input_box_item' style='text-align:center' rowspan='".$ode_ix_row_cnt[$product_info[$i][ode_ix]]."'>
											".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($delivery_info[delivery_dcprice],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
										</td>";
									}

									if($b_claim_group!=$product_info[$i][claim_group]){
										$Contents .="
										<td class='input_box_item' style='text-align:center' rowspan='".$claim_group_row_cnt[$product_info[$i][claim_group]]."'>";
											
											/*
											if($refund_data["delivery"][$product_info[$i][company_id]]["claim_coment"]!=""){
												$Contents .="<img src='../v3/images/".$admininfo[language]."/btn_search.gif' align='absmiddle' class='helpcloud' help_width='200'  help_html='".$refund_data["delivery"][$product_info[$i][company_id]]["claim_coment"]."' style='cursor:pointer;'>";
											}
											*/
										
											$sql="select * from shop_order_claim_delivery where oid='".$product_info[$i][oid]."' and claim_group='".$product_info[$i][claim_group]."' ";
											$db->query($sql);
											if($db->total){
												$db->fetch();
												$refund_delivery=$db->dt;
												
												$claim_total_delivery_price += $refund_delivery[delivery_price];

                                                if($admin_config["currency_unit"] == 'USD'){
                                                    $refund_delivery_price = $refund_delivery[delivery_price];
                                                }else{
                                                    $refund_delivery_price = intval($refund_delivery[delivery_price]);
                                                }

											}else{
												$ERROR_BOOL=true;
											}

										$Contents .="
											<input type='text' name='refund_delivery_price[".$refund_delivery[ocde_ix]."]' class='textbox number' value='".$refund_delivery_price."' maxPrice='".$refund_data["delivery"][$product_info[$i][company_id]]["delivery_price"]."' style='width:40px' /> 

										</td>";
									}

								$Contents .="
								</tr>";
								
								$b_ode_ix=$product_info[$i][ode_ix];
								$b_claim_group=$product_info[$i][claim_group];
								$b_company_id=$product_info[$i][company_id];
							}

							$Contents .= "
								<tr>
									<td class='input_box_item' colspan='5' style='padding:5px;line-height:200%'>";

										if($refund_bank_bool){

											if(count($user_bankinfo) > 0){
												$Contents .= "환불 계좌 정보 : <select name='user_bank_ix' onchange='showSelfArea(this);'>";

												foreach($user_bankinfo as $ub_info){
													$Contents .= "<option value='".$ub_info[bank_ix]."' ".($ub_info[is_basic] =="1" ? "selected" : "").">".$ub_info[bank_name]." ".$ub_info[bank_number]." ".$ub_info[bank_owner]."</option>";
												}

												$Contents .= "<option value=''>관리자 직접 입력</option>
															</select>

															<div class='regist_self_accnts' style='display:none;'>
																환불 계좌 은행 : <select name='refund_bank_code' validation='false' title='환불 계좌 은행'>
																<option value=''>은행 선택</option>";
												foreach($arr_banks_name as $key => $banks_name){
													$Contents .= "<option value='".$key."' ".($refund_info["bank_code"]==$key ? "selected" : "").">".$banks_name."</option>";
												}

												$Contents .= "</select> <br/>
																환불 계좌 번호 : <input type='text' name='refund_bank_number' class='textbox number' value='".$refund_info["bank_number"]."' style='width:120px' validation='false' title='환불 계좌 번호' /> 
																입금자명 : <input type='text' name='refund_bank_owner' class='textbox' value='".$refund_info["bank_owner"]."' style='width:60px' validation='false' title='입금자명'/>
																</div>
												";
											}else{
												if(empty($refund_info["bank_code"])){
													$Contents .= "<b class='red'> 기본환불계좌 정보가 없습니다.</b> <br/>";
												}

												$Contents .= "환불 계좌 은행 : <select name='refund_bank_code' validation='".$refund_validation."' title='환불 계좌 은행'>
																<option value=''>은행 선택</option>";
												foreach($arr_banks_name as $key => $banks_name){
													$Contents .= "<option value='".$key."' ".($refund_info["bank_code"]==$key ? "selected" : "").">".$banks_name."</option>";
												}

												$Contents .= "</select> <br/>";

												$Contents .= "환불 계좌 번호 : <input type='text' name='refund_bank_number' class='textbox number' value='".$refund_info["bank_number"]."' style='width:120px' validation='".$refund_validation."' title='환불 계좌 번호' /> 
												입금자명 : <input type='text' name='refund_bank_owner' class='textbox' value='".$refund_info["bank_owner"]."' style='width:60px' validation='".$refund_validation."' title='입금자명'/>";
											}
										}

									$Contents .= "
									</td>
									<td class='input_box_item' style='text-align:center'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($refund_data["product"]["product_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
									<td class='input_box_item' style='text-align:center'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($refund_data["product"]["product_dc_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
									<td class='input_box_item' style='text-align:center'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($refund_data["delivery"]["org_delivery_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
									<td class='input_box_item' style='text-align:center'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($claim_total_delivery_price,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
								</tr>
							</table>";


                            $Contents .= "
                            <div style='padding:15px 5px 5px 5px'><img src='../images/dot_org.gif' align='absmiddle'> <b>실환불수단</b></div>
                            <table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;margin-top:2px;' >
							<tr>
								<td class='input_box_item' style='padding:10px;'>
                                    <table  border='0' width='100%' cellpadding='3' >
                                    <col width='90px;'/>
                                    <col width='*'/>
                                    <tr>
                                        <td>환불 수단 : </td>
                                        <td>
                                            <select name='refund_method'>
                                                <option value=''>선택</option>
                                                <option value='1'>현금</option>
                                                <option value='2'>적립금</option>
                                            </select>    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>환불 날짜 : </td>
                                        <td>".select_date('refund_date',date('Y-m-d'))."</td>
                                    </tr>
                                    </table>
                                </td>
                            </tr>
                            </table>";


							//$tmp_tax_price=$refund_data["tax_price"];
							//$tmp_tax_free_price=$refund_data["tax_free_price"];


							$tmp_tax_price=$refund_data["product"]["tax_price"]+$claim_total_delivery_price-$cart_tax_price;
							$tmp_tax_free_price=$refund_data["product"]["tax_free_price"]-$cart_tax_tree_price-$cart_tax_free_price;

							$total_refund_price = $refund_data["product"]["product_dc_price"] + $claim_total_delivery_price;

							if($_SESSION["admininfo"]["charger_id"]=="forbiz"){
								//echo "<pre>";
								//print_r($refund_data);
							}
							
							$Contents .= "
								<table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;margin-top:20px;' >
									<tr>
										<td class='input_box_item' style='padding:10px;'>
											<table border='0' width='100%'>
												<col width='130px;'/>
												<col width='120px;'/>
												<col width='*'/>";

											if($cart_coupon_bool){
												$Contents .= "
												<tr>
													<td class='input_box_item' style='text-align:right;' colspan='3'>
														장바구니 쿠폰 
														<input type='checkbox' id='cart_coupon_refund' name='cart_coupon_refund' value='Y' checked/><label for='cart_coupon_refund'>차감</label> <input type='checkbox' id='cart_coupon_give' name='cart_coupon_give' value='Y'/><label for='cart_coupon_give'>쿠폰돌려주기</label>
													</td>
												</tr>";
											}
												$Contents .= "
												<tr>
													<td class='input_box_item'>과세상품 환불합계 </td>
													<td class='input_box_item' style='text-align:right;'>
														<input type='text' name='total_refund_tax_product_price' id='total_refund_tax_product_price' class='textbox number point_color' value='".($refund_data["product"]["tax_price"])."' style='width:60px'  readonly /> ".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"]." =
													</td>
													<td class='input_box_item' style='text-align:right;'>";
														if ( ! empty($pay_info) ) {

															foreach($pay_info as $key => $val){

																if($tmp_tax_price > 0){
																	$b_tmp_tax_price = $tmp_tax_price;
																	$tmp_tax_price -= min(array($refund_data["product"]["tax_price"],$val["tax_price"]));

																	if($tmp_tax_price > 0){
																		$tax_product_price = min(array($refund_data["product"]["tax_price"],$val["tax_price"]));
																	}else{
																		$tax_product_price = $b_tmp_tax_price;
																		$tmp_tax_price=0;
																	}
																}else{
																	$tax_product_price=0;
																}
																
																$refund_data["product"]["tax_price"]-=$tax_product_price;
																$pay_info[$key]["tax_price"]-= $tax_product_price;
																$tmp_method_price[$val["method"]] += $tax_product_price;

																if($val["method"]==ORDER_METHOD_CART_COUPON){

																	if($refund_data["product"]["tax_price"] > $cart_tax_price){
																		$tmp_cart_tax_price = $cart_tax_price;
																	}else{
																		$tmp_cart_tax_price = $refund_data["product"]["tax_price"];
																	}

																	$Contents .= "<input type='hidden' name='tax_product_price[".$val["method"]."]' id='tax_product_price_".$val["method"]."' class='textbox number' value='".$tmp_cart_tax_price."' data='".$tmp_cart_tax_price."' style='width:60px' method='".$val["method"]."' />";
																}else{
																	if($key!=0)			$Contents .= " + ";
																	$Contents .= getMethodStatus($val["method"])." <input type='text' name='tax_product_price[".$val["method"]."]' id='tax_product_price_".$val["method"]."' class='textbox number' value='".$tax_product_price."' style='width:60px' method='".$val["method"]."' /> ".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"]."";
																}
															}
														}
														
													$Contents .= "
													</td>
												</tr>
												<tr>
													<td class='input_box_item' style='border-bottom:1px solid gray;'>면세상품 환불합계 </td>
													<td class='input_box_item' style='text-align:right;border-bottom:1px solid gray;'>
														<input type='text' name='total_refund_tax_free_product_price' id='total_refund_tax_free_product_price' class='textbox number point_color' value='".($refund_data["product"]["tax_free_price"])."' style='width:60px'  readonly /> ".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"]." =
													</td>
													<td class='input_box_item' style='text-align:right;border-bottom:1px solid gray;'>";
														if ( ! empty($pay_info) ) {
															foreach($pay_info as $key => $val){
																
																if($tmp_tax_free_price > 0){
																	$b_tmp_tax_free_price = $tmp_tax_free_price;
																	$tmp_tax_free_price -= min(array($refund_data["product"]["tax_free_price"],$val["tax_free_price"]));

																	if($tmp_tax_free_price > 0){
																		$tax_free_product_price = min(array($refund_data["product"]["tax_free_price"],$val["tax_free_price"]));
																	}else{
																		$tax_free_product_price = $b_tmp_tax_free_price;
																		$tmp_tax_free_price=0;
																	}
																}else{
																	$tax_free_product_price=0;
																}
																
																$refund_data["product"]["tax_free_price"]-=$tax_free_product_price;
																$pay_info[$key]["tax_free_price"]-= $tax_free_product_price;
																$tmp_method_price[$val["method"]] += $tax_free_product_price;
																
																if($val["method"]==ORDER_METHOD_CART_COUPON){

																	if($refund_data["product"]["tax_free_price"] > $cart_tax_tree_price){
																		$tmp_cart_tax_price = $cart_tax_tree_price;
																	}else{
																		$tmp_cart_tax_price = $refund_data["product"]["tax_free_price"];
																	}

																	$Contents .= "<input type='hidden' name='tax_free_product_price[".$val["method"]."]' id='tax_free_product_price_".$val["method"]."'  class='textbox number' value='".$cart_tax_tree_price."' data='".$cart_tax_tree_price."' style='width:60px' method='".$val["method"]."' />";
																}else{
																	if($key!=0)			$Contents .= " + ";
																	$Contents .= getMethodStatus($val["method"])." <input type='text' name='tax_free_product_price[".$val["method"]."]' id='tax_free_product_price_".$val["method"]."'  class='textbox number' value='".$tax_free_product_price."' style='width:60px' method='".$val["method"]."' /> ".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"]."";
																}
															}
														}
													$Contents .= "
													</td>
												</tr>
												<tr>
													<td class='input_box_item' style='border-bottom:1px solid gray;'>배송비 환불합계</td>
													<td class='input_box_item' style='text-align:right;border-bottom:1px solid gray;'>
														<input type='text' name='total_refund_delivery_price' id='total_refund_delivery_price' class='textbox number point_color' value='".$claim_total_delivery_price."' style='width:60px' readonly /> ".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"]." =
													</td>
													<td class='input_box_item' style='text-align:right;border-bottom:1px solid gray;'>";
														
														$tmp_tax_price+=$cart_tax_price;

														if ( ! empty($pay_info) ) {
															foreach($pay_info as $key => $val){
																
																if($tmp_tax_price > 0){
																	$b_tmp_tax_price = $tmp_tax_price;
																	$tmp_tax_price -= min(array($claim_total_delivery_price,$val["tax_price"]));

																	if($tmp_tax_price > 0){
																		$delivery_price = min(array($claim_total_delivery_price,$val["tax_price"]));
																	}else{
																		$delivery_price = $b_tmp_tax_price;
																		$tmp_tax_price=0;
																	}
																}else{
																	$delivery_price=0;
																}
																
																$claim_total_delivery_price-=$delivery_price;
																$pay_info[$key]["tax_price"]-= $delivery_price;
																$tmp_method_price[$val["method"]] += $delivery_price;
																
																if($val["method"]==ORDER_METHOD_CART_COUPON){
																	$Contents .= "<input type='hidden' name='delivery_price[".$val["method"]."]' id='delivery_price_".$val["method"]."' class='textbox number' value='0' data='0' style='width:60px' method='".$val["method"]."' />";
																}else{
																	if($key!=0)			$Contents .= " + ";
																	$Contents .= getMethodStatus($val["method"])." <input type='text' name='delivery_price[".$val["method"]."]' id='delivery_price_".$val["method"]."' class='textbox number' value='".$delivery_price."' style='width:60px' method='".$val["method"]."' /> ".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"]."";
																}
															}
														}
													$Contents .= "
													</td>
												</tr>
												<tr>
													<td class='input_box_item'>총합계</td>
													<td class='input_box_item' style='text-align:right;'>
														<input type='text' name='total_refund_price' id='total_refund_price' class='textbox number point_color' value='".$total_refund_price."' style='width:60px'  readonly/> ".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"]." =
													</td>
													<td class='input_box_item' style='text-align:right;'>";
														if ( ! empty($pay_info) ) {
															foreach($pay_info as $key => $val){
																if($val["method"]==ORDER_METHOD_CART_COUPON){
																	$Contents .= "<input type='hidden' name='refund_price[".$val["method"]."]' id='refund_price_".$val["method"]."' maxMethodPrice='".$max_method_price[$val["method"]]."' class='textbox number point_color' value='".$cart_payment_price."' style='width:60px' readonly/>";
																}else{
																	if($key!=0)			$Contents .= " + ";
																	$Contents .= getMethodStatus($val["method"])." <input type='text' name='refund_price[".$val["method"]."]' id='refund_price_".$val["method"]."' maxMethodPrice='".$max_method_price[$val["method"]]."' class='textbox number point_color' value='".$tmp_method_price[$val["method"]]."' style='width:60px' readonly/> ".$currency_display[$admin_config["currency_unit"]]["front"]."".$currency_display[$admin_config["currency_unit"]]["back"]."";
																}
															}
														}
													$Contents .= "
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>";
							
							$Contents .= "
								</td>
							</tr>
						</table>
						
                        <div style='float:right'> 
                            <span style='color:red;'>* 부분 취소의 경우 적립금 환불 불가, 금액 조정 금지</span>
                        </div>
			</td>
  		</tr>";

        if($ERROR_BOOL){

            $Contents .= "
		<tr>
			<td colspan=2 align=center style='padding:10px 0px;'>
					배송비 환불데이터가 존제하지 않습니다. FORBIZ 에 문의 바랍니다.			
			</td>
		</tr>";

        }else {

            $Contents .= "
            <tr>
                <td colspan=2 align=center style='padding:10px 0px;'>
                        <img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle onclick=\"$('form[name=refund_price_give_frm]').submit();\" style='cursor:pointer;' />
                        
                    
                </td>
            </tr>
            <tr>
                <td colspan=2 align=right style='padding:10px 0px;'>                        
                        <input type='checkbox' name='direct_pg' id='direct_pg' value='Y'  /><label for='direct_pg' >PG 수동 처리</label>                    
                </td>
            </tr>
            ";
        }
$Contents .= "
  	</table>
</form>";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "주문관리 > 환불하기";
$P->NaviTitle = "환불하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();