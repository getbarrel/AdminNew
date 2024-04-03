<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include("../order/orders.lib.php");

$db = new Database;
$sdb = new Database;
$master_db = new Database;
$master_db->master_db_setting();

if(empty($apply_status) && empty($act)){
	echo " 잘못된 접근입니다.";
	exit;
}


if($act=="claim_update"){
	
	$Contents = "<form name='claim_update_frm' method='POST' action='orders.act.php' onsubmit='return CheckFormValue(this)' target='act'>
		<input type='hidden' name='act' value='claim_update'>
		<input type='hidden' name='oid' value='".$oid."'>
		<input type='hidden' name='claim_group' value='".$claim_group."'>
		<input type='hidden' name='apply_status' value='".$apply_status."'>";
	
	$where = " WHERE od.oid = '".$oid."' and od.claim_group='".$claim_group."' ";
	$disabled = "disabled";
}else{

	if($act=="claim_confirm"){
		$Contents = "<form name='claim_apply_frm' method='POST' action='orders.act.php' onsubmit='return CheckFormValue(this)' target='act'>
		<input type='hidden' name='act' value='claim_apply'>";
	}else{
		$Contents = "<form name='claim_apply_frm' method='POST' action='../order/claim_apply.php' onsubmit='return CheckFormValue(this)'>
		<input type='hidden' name='act' value='claim_confirm'>";
	}

	$Contents .= "
	<input type='hidden' name='apply_all' value='".$apply_all."'>
	<input type='hidden' name='od_ix_str' value='".$od_ix_str."'>
	<input type='hidden' name='apply_status' value='".$apply_status."'>";

	$where = " WHERE od.od_ix in ('".str_replace("|","','",$od_ix_str)."') ";
}

		$sql = "SELECT
			od.*, od.ptprice - od.pt_dcprice as total_sale_price, odd.delivery_dcprice ,
			case when claim_delivery_od_ix != 0 then od.claim_delivery_od_ix else od.od_ix end as vieworder,
			case when claim_delivery_od_ix != 0 then 'Y' else 'N' end as claim_apply_product_yn,
			odd.dt_ix
		FROM ".TBL_SHOP_ORDER_DETAIL." od left join shop_order_delivery odd on (od.ode_ix=odd.ode_ix)
		$where 
		order by vieworder , od.od_ix ";
		$db->query($sql);
		$product_info=$db->fetchall();

        $currency_unit = check_currency_unit($product_info[0]['mall_ix']);
        if($currency_unit == 'USD'){
            $decimals_value = 2;
        }else{
            $decimals_value = 0;
        }

		if($apply_status=="EA"){

			/* 회원 할인 정책으로 인한 세션 생성! */
			$b_mall_data_root = $_SESSION["layout_config"]["mall_data_root"];
			$b_gp_ix = $_SESSION["user"]["gp_ix"];
			$b_sale_rate = $_SESSION["user"]["sale_rate"];

			$sql="SELECT 
					mg.sale_rate, mg.gp_ix, mg.wholesale_dc, mg.retail_dc, mg.selling_type
				FROM 
					shop_order o, common_member_detail cmd, ".TBL_SHOP_GROUPINFO." mg
				WHERE
					o.user_code=cmd.code and cmd.gp_ix=mg.gp_ix and o.oid = '".$product_info[0]["oid"]."' ";

			$db->query($sql);
			$db->fetch("object");

			$_SESSION["layout_config"]["mall_data_root"] = $_SESSION["admininfo"]["mall_data_root"];
			$_SESSION["user"]["gp_ix"] = $db->dt["gp_ix"];

			if($db->dt["selling_type"]=="R") {
				if($db->dt["retail_dc"]){
					$_SESSION["user"]["sale_rate"] = $db->dt["retail_dc"];
				}else{
					$_SESSION["user"]["sale_rate"] = '0';
				}
			} else {
				if($db->dt["wholesale_dc"]){
					$_SESSION["user"]["sale_rate"] = $db->dt["wholesale_dc"];
				}else{
					$_SESSION["user"]["sale_rate"] = '0';
				}
			}
		}

		$Contents .= "
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
						<table border='0' width='100%' cellspacing='1' cellpadding='0' >
							<tr>
								<td>
									<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b>신청정보</b></div>
								</td>
							</tr>
							<tr>
								<td>";
							
							//취소요청/승인
							if($apply_status=='CA'){

								$Contents .= "
									<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='table-layout:fixed;' >
										<col width='20%'>
										<col width='30%'>
										<col width='20%'>
										<col width='30%'>
										<tr>
											<td class='input_box_title'>  상세사유</td>
											<td class='input_box_item' >";
											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='reason_code' value='".$reason_code."'>
												".fetch_order_status_div('DR','CA',"title",$reason_code);
											}else{
												$Contents .= "
													<select name='reason_code'  class='' style='font-size:12px;' validation='true' title='상세사유' >";
														$Contents .= "<option value='' >취소사유</option>";
															foreach($order_select_status_div['A']['DR']['CA'] as $key => $val){
																$Contents .= "<option value='".$key."' >".$val[title]."</option>";
															}
														$Contents .= "
													</select>";
											}
											$Contents .= "
											</td>
											<td class='input_box_title'>  신청자</td>
											<td class='input_box_item' >";
											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='c_type' value='".$c_type."'>
												".($c_type == "B" ?  "구매자": "")."
												".($c_type == "S" ?  "판매자": "")."
												".($c_type == "M" ?  "담당MD": "");
											}else{
												$Contents .= "
												<input type='radio' name='c_type' id='c_type_b' value='B' checked><label for='c_type_b'>구매자</label>
												<input type='radio' name='c_type' id='c_type_s' value='S' ><label for='c_type_s'>판매자</label>
												<input type='radio' name='c_type' id='c_type_m' value='M' ><label for='c_type_m'>담당MD</label>";
											}
											$Contents .= "
											</td>
										</tr>
										<tr>
											<td class='input_box_title'>  기타사유</td>
											<td class='input_box_item' colspan='3'>";
											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='msg' value='".$msg."'>
												".$msg."";
											}else{
												$Contents .= "<input type='text' name='msg' style='width:98%;' validation='true' title='기타사유' />";
											}
											$Contents .= "
											</td>
										</tr>
										<tr>
											<td class='input_box_title'> 취소처리상태</td>
											<td class='input_box_item' colspan='3'>";
											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='status' value='".$status."'>
												".getOrderStatus($status);
											}else{
												$Contents .= "
												<input type='radio' name='status' id='status_".ORDER_STATUS_CANCEL_APPLY."' value='".ORDER_STATUS_CANCEL_APPLY."' checked><label for='status_".ORDER_STATUS_CANCEL_APPLY."'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label>
												<input type='radio' name='status' id='status_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' ><label for='status_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label>";
											}
											$Contents .= "
											</td>
										</tr>
									</table>
									
									<table border='0' width='100%' cellspacing='1' cellpadding='0' style='margin-top:25px;' style='table-layout:fixed;'>
										<tr>
											<td>
												<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b>취소상품정보</b></div>
											</td>
										</tr>
									</table>
									<table width='100%' border='0' cellpadding='0' cellspacing='1' class='input_table_box' style='table-layout:fixed;'>
										<tr height='30' bgcolor='#efefef' align=center>
											<td width='*' class='m_td'><b>상품명</b></td>
											<td width='12%' class='m_td'><b>옵션<br/>/판매단가(할인가)</b></td>
											<td width='5%' class='m_td'><b>수량</b></td>
											<td width='5%' class='e_td' ><b>취소수량</b></td>
											<td width='10%' class='m_td'><b>상품가격</br>/할인액</br>/적립금</b></td>
											<td width='18%' class='m_td'><b>실결제금액</b></td>
											<td width='7%' class='m_td'><b>배송비</b></td>
											<td width='7%' class='m_td'><b>처리상태</b></td>
											".($admininfo[admin_level]==9 && false ? "<td width='7%' class='m_td' ><b>출고처리</b></td>" :"")."
										</tr>";


	for($i = 0; $i < count($product_info); $i++)
	{

        $admin_config["currency_unit"] = check_currency_unit($product_info[$i]['mall_ix']);

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
		
		$discount_info = $currency_display[$admin_config["currency_unit"]]["front"].number_format($product_info[$i][total_sale_price],$decimals_value).$currency_display[$admin_config["currency_unit"]]["back"];
		
		if($dc_etc_str!=""){
			$discount_info.=" <label class='helpcloud' help_width='".$dc_etc_width."' help_height='".$dc_etc_height."' help_html='".$dc_etc_str."'><img src='../images/icon/q_icon.png' align=''></label>";
		}
		
		if($dc_coupon_str!=""){
			$discount_info.=" <label class='helpcloud' help_width='".$dc_coupon_width."' help_height='".$dc_coupon_height."' help_html='".$dc_coupon_str."'><img src='../images/".$admininfo[language]."/s_use_coupon.gif' align=''></label>";
		}

										$Contents .= "
										<tr height='30' align='center'>
											<td bgColor='#ffffff'>
												<table width='100%'>
													<tr>
														<td align='center' width='70'>
														<a href='../product/goods_input.php?id=".$product_info[$i][pid]."'  title='".$LargeImageSize."' target='_blank' ><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $product_info[$i][pid], "m" , $product_info[$i])."'  onerror=\"this.src='".$admin_config[mall_data_root]."/images/noimg_52.gif'\" width=50 style='margin:5px;'></a><br/>";

														if($product_info[$i][product_type]=='21'||$product_info[$i][product_type]=='31'){
															$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($product_info[$i][product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$product_info[$i][product_type].".gif' align='absmiddle' ></label> ";
														}
														if($product_info[$i][stock_use_yn]=='Y'){
															$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
														}
											$Contents .= "
														</td>
														<td style='padding:5px 0 5px 0;line-height:130%' align='left'>";

												if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
													$Contents .= "<a href=\"javascript:PoPWindow('../seller/company.add.php?company_id=".$product_info[$i][company_id]."&mmode=pop',960,600,'brand')\"><b>".($product_info[$i][company_name] ? $product_info[$i][company_name]:"-")."</b></a><br>";
												}

												if(in_array($product_info[$i][product_type],$sns_product_type)){
													$Contents .= "<a href=\"/sns/shop/goods_view.php?id=".$product_info[$i][pid]."\" target=_blank>".$product_info[$i][pname]."</a>";
												}else{
													$Contents .= "<a href=\"/shop/goods_view.php?id=".$product_info[$i][pid]."\" target=_blank>";
													if($product_info[$i][product_type]=='99'||$product_info[$i][product_type]=='21'||$product_info[$i][product_type]=='31'){
														$Contents .= "<b class='".($product_info[$i][product_type]=='99' ? "red" : "blue")."' >".$product_info[$i][pname]."</b><br/><strong>".$product_info[$i][set_name]."<br /></strong>".$product_info[$i][sub_pname];
													}else{
														$Contents .= $product_info[$i][pname];
													}
													$Contents .= "</a>";
												}

											$Contents .= "
														</td>
													</tr>
												</table>
											</td>
											<td bgColor='#ffffff' align=left>
												<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
													<tr>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
															".strip_tags($product_info[$i][option_text])."".($product_info[$i][option_price] != '' ? " + ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($product_info[$i][option_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."":"")."
														</td>
													</tr>
													<tr>
														<td align='center' valign='middle'>
															".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($product_info[$i][psprice]+$product_info[$i][option_price],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
												</table>
											</td>
											<td bgColor='#ffffff'>".$product_info[$i][pcnt]." 개</td>
											<td bgColor='#ffffff'>";
												if($act=="claim_confirm"){
													$Contents .= "<input type='hidden' name='apply_cnt[".$product_info[$i][od_ix]."]' value='".$apply_cnt[$product_info[$i][od_ix]]."'>
													".$apply_cnt[$product_info[$i][od_ix]]." 개";
												}else{
													$Contents .= "
													<select name='apply_cnt[".$product_info[$i][od_ix]."]'>";
														for($j=$product_info[$i][pcnt];$j>0;$j--){
															$Contents .= "<option value='".$j."'>".$j."</option>";
														}
													$Contents .= "
													</select>";
												}
											$Contents .= "
											</td>
											<td bgColor='#ffffff' align=left>
												<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
													<tr>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
															".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($product_info[$i][pt_dcprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
													<tr>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
															".$discount_info."
														</td>
													</tr>
													<tr>
														<td align='center' valign='middle'>
															".number_format($product_info[$i][reserve] * $product_info[$i][pcnt],$decimals_value)." P
														</td>
													</tr>
												</table>
											</td>
											<td bgColor='#ffffff' align=left>
												<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
													<tr>
														<td class='m_td' style='border-bottom:1px solid #c5c5c5;border-right:1px solid #c5c5c5'>판매가</td>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5;'>
															 ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($product_info[$i][pt_dcprice],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
													<tr>
														<td class='m_td' style='border-bottom:1px solid #c5c5c5;border-right:1px solid #c5c5c5' width='40%'>공급가</td>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5;'>
															".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format(round($product_info[$i][pt_dcprice])/1.1,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
													<tr>
														<td class='m_td' style='border-right:1px solid #c5c5c5'>세액</td>
														<td align='center' valign='middle' style=''>
															".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($product_info[$i][pt_dcprice]-round($product_info[$i][pt_dcprice])/1.1,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
												</table>
											</td>";
											
											$delivery_pay_type = getDeliveryPayType($product_info[$i][delivery_pay_method]);		//배송비 결제수단 텍스트 리턴
											$delivery_method = getDeliveryMethod($product_info[$i][delivery_method]);			//배송방법 텍스트 리턴

											//배송비 분리 시작 2014-05-21 이학봉
											if($product_info[$i][delivery_package] == 'N' && $product_info[$i][delivery_pay_method] == '1'){	//묶음이거나 선불일겨우 (착불은 개별로 처리)
												
												//|| $b_product_type != $product_info[$i][product_type] || $b_factory_info_addr_ix != $product_info[$i][factory_info_addr_ix] || $b_delivery_type  != $product_info[$i][delivery_type]
												if($bori_company_id != $product_info[$i][company_id] ){
													$sql = "SELECT 
															COUNT(DISTINCT(od.od_ix)) AS com_cnt
														FROM 
															".TBL_SHOP_ORDER." o,
															".TBL_SHOP_ORDER_DETAIL." od
														where 
															o.oid = od.oid 
															and o.oid = '".$product_info[$i][oid]."' 
															and od.ori_company_id='".$product_info[$i][ori_company_id]."'
															and od.delivery_type = '".$product_info[$i][delivery_type]."'
															and od.delivery_package = 'N'
															and od.delivery_method = '".$product_info[$i][delivery_method]."'
															and od.delivery_pay_method = '".$product_info[$i][delivery_pay_method]."'
															and od.delivery_addr_use = '".$product_info[$i][delivery_addr_use]."'
															and od.factory_info_addr_ix = '".$product_info[$i][factory_info_addr_ix]."'
															".str_replace("WHERE","and",$where)." 
															";

													$db->query($sql);//$od_db는 상단에서 선언
													$db->fetch();
													$com_cnt=$db->dt["com_cnt"];
													
													$Contents .="<td bgColor='#ffffff' class='' align=center style='line-height:140%;' rowspan='".$com_cnt."'>
														".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($product_info[$i][delivery_dcprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."<br>
														".($product_info[$i][delivery_type] == '1' ? "통합배송":"입점업체배송")."<br>
														".$delivery_method."<br>
														".($product_info[$i][delivery_addr_use] == '1' ? "출고지별 배송<br>".getFactoryAddrName($product_info[$i][factory_info_addr_ix])."<br>":"")."
														".$delivery_pay_type."<br>
														".($product_info[$i][delivery_package] == 'N' ? "묶음배송":"")."</td>";
												}

												$bori_company_id = $product_info[$i][company_id];

											}else{

												$sql = "SELECT 
															COUNT(DISTINCT(od.od_ix)) AS com_cnt
														FROM 
															".TBL_SHOP_ORDER." o,
															".TBL_SHOP_ORDER_DETAIL." od
														where 
															o.oid = od.oid 
															and o.oid = '".$product_info[$i][oid]."'
															and od.pid = '".$product_info[$i][pid]."'
															and od.ori_company_id='".$product_info[$i][ori_company_id]."'
															and od.delivery_type = '".$product_info[$i][delivery_type]."'
															and od.delivery_package = 'Y'
															and od.delivery_method = '".$product_info[$i][delivery_method]."'
															and od.delivery_pay_method = '".$product_info[$i][delivery_pay_method]."'
															and od.delivery_addr_use = '".$product_info[$i][delivery_addr_use]."'
															and od.factory_info_addr_ix = '".$product_info[$i][factory_info_addr_ix]."'
															".str_replace("WHERE","and",$where)." 
															";

													$db->query($sql);//$od_db는 상단에서 선언
													$db->fetch();
													$com_cnt=$db->dt["com_cnt"];

												if(ch_set_product_bool($product_info[$i][option_kind])){		//세트상품일경우

													if($b_oid != $product_info[$i][oid] || $bproduct_id != $product_info[$i][pid]){

														$Contents .="<td bgColor='#ffffff' class='' align=center style='line-height:140%;' ".($b_oid != $product_info[$i][oid] || $bproduct_id != $product_info[$i][pid] ? "rowspan='".$com_cnt."'":"").">
															".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($product_info[$i][delivery_dcprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."<br>
															".($product_info[$i][delivery_type] == '1' ? "통합배송":"입점업체배송")."<br>
															".$delivery_method."<br>
															".($product_info[$i][delivery_addr_use] == '1' ? "출고지별 배송<br>".getFactoryAddrName($product_info[$i][factory_info_addr_ix])."<br>":"")."
															".$delivery_pay_type."<br>
															<img src='../images/".$admininfo["language"]."/delivery_policy_1.gif' title='개별배송상품'></td>";
													}

												}else{			//일반상품
													if($b_oid != $product_info[$i][oid] || $bproduct_id != $product_info[$i][pid] || $bset_group != $product_info[$i][set_group]){// rowspan='".$com_cnt."'
														$Contents .="<td bgColor='#ffffff' class='' align=center style='line-height:140%;'>
															".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($product_info[$i][delivery_dcprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."<br>
															".($product_info[$i][delivery_type] == '1' ? "통합배송":"입점업체배송")."<br>
															".$delivery_method."<br>
															".($product_info[$i][delivery_addr_use] == '1' ? "출고지별 배송<br>".getFactoryAddrName($product_info[$i][factory_info_addr_ix])."<br>":"")."
															".$delivery_pay_type."<br>
															<img src='../images/".$admininfo["language"]."/delivery_policy_1.gif' title='개별배송상품'>
															</td>";
													}
												}
											}

											$Contents .= "
											<td bgColor='#ffffff' align=center style='line-height:130%'>";

											$Contents .= getOrderStatus($product_info[$i][status]);

											if($product_info[$i]["admin_message"]!="") {
													$Contents .="<br><b>".$product_info[$i]["admin_message"]."</b>";
											}

											if($product_info[$i][di_date] && $product_info[$i][status] == "DI"){
												$Contents .= "<br />(".substr($product_info[$i][di_date],0,10).")";
											}

											if($product_info[$i][dc_date] && $product_info[$i][status] == "DC"){
												$Contents .= "<br />(".substr($product_info[$i][dc_date],0,10).")";
											}

											if(trim($product_info[$i][ac_date])){
												$Contents .= "<br /><a href=\"/admin/order/accounts_detail.php?ac_ix=".$product_info[$i][ac_ix]."\" target=_blank>(".substr($product_info[$i][ac_date],0,4)."-".substr($product_info[$i][ac_date],4,2)."-".substr($product_info[$i][ac_date],6,2).")</a> ";
											}

											if(($product_info[$i][invoice_no] || $product_info[$i][quick]) && ($product_info[$i][status] == "DI" || $product_info[$i][status] == "DC" || $product_info[$i][status] == "BF")){
												$Contents .= "<br><a href=\"javascript:searchGoodsFlow('".$product_info[$i][quick]."', '".str_replace("-","",$product_info[$i][invoice_no])."')\">".codeName($product_info[$i][quick])." </a> ";
											}

											if($admininfo[admin_level]==9){
												$Contents .= "<br/>".getOrderStatus($product_info[$i][delivery_status]);
											}


											$Contents .="
											</td>";

										$Contents .= "
									</tr>";
	}
								$Contents .= "
								</table>";

							}else{//교환및반품

								if($act=="claim_update"){
									$sql="SELECT 
										c_type,
										reason_code,
										os.status_message
									FROM 
										shop_order_status os 
									WHERE
										os.oid = '".$oid."'
									and
										os.od_ix in (select od_ix from shop_order_detail where oid='".$oid."' and claim_group=".$claim_group.")
									and 
										os.status='".$apply_status."'
									ORDER BY os.regdate DESC LIMIT 0,1";

									$db->query($sql);
									$db->fetch();
									$reason_code = $db->dt[reason_code];
									$c_type = $db->dt[c_type];
									$msg = explode("]",$db->dt[status_message]);
									array_shift($msg);
									$msg = implode("]",$msg);
								}

								$Contents .= "
									<table border='0' width='100%' cellspacing='1' cellpadding='0' class='input_table_box' style='table-layout:fixed;'>
										<col width='20%'>
										<col width='30%'>
										<col width='20%'>
										<col width='30%'>
										<tr>
											<td class='input_box_title'>  상세사유</td>
											<td class='input_box_item' >";

											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='reason_code' value='".$reason_code."'>
												".fetch_order_status_div('DC',$apply_status,"title",$reason_code);
											}else{
												$Contents .= "
													<select name='reason_code'  class='' style='font-size:12px;' validation='true' title='상세사유' >
													<option value='' >".($apply_status=='EA'?"교환":"반품")."사유</option>";
														foreach($order_select_status_div['A']['DC'][$apply_status] as $key => $val){
															$Contents .= "<option value='".$key."' ".($key==$reason_code ? "selected" : "").">".$val[title]."</option>";
														}
													$Contents .= "
												</select>";
											}
											$Contents .= "
											</td>
											<td class='input_box_title'>  신청자</td>
											<td class='input_box_item'>";
											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='c_type' value='".$c_type."'>
												".($c_type == "B" ?  "구매자": "")."
												".($c_type == "S" ?  "판매자": "")."
												".($c_type == "M" ?  "담당MD": "");
											}else{
												$Contents .= "
												<input type='radio' name='c_type' id='c_type_b' value='B' ".($c_type=="" || $c_type=="B" ? "checked" : "")."><label for='c_type_b'>구매자</label>
												<input type='radio' name='c_type' id='c_type_s' value='S' ".($c_type=="S" ? "checked" : "")."><label for='c_type_s'>판매자</label>
												<input type='radio' name='c_type' id='c_type_m' value='M' ".($c_type=="M" ? "checked" : "")."><label for='c_type_m'>담당MD</label>";
											}
											$Contents .= "
											</td>
										</tr>
										<tr>
											<td class='input_box_title'> 기타사유</td>
											<td class='input_box_item' colspan='3'>";
											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='msg' value='".$msg."'>
												".$msg."";
											}else{
												$Contents .= "<input type='text' name='msg' value='".$msg."' style='width:98%;' validation='true' title='기타사유' />";
											}
											$Contents .= "
											</td>
										</tr>";

										if($act!="claim_update"){
											$Contents .= "
											<tr >
												<td class='input_box_title'> ".($apply_status=='EA'?"교환":"반품")."처리상태</td>
												<td class='input_box_item' colspan='3'>";
												if($act=="claim_confirm"){
													$Contents .= "
													<input type='hidden' name='status' value='".$status."'>
													".getOrderStatus($status);
												}else{
													if($apply_status=='EA'){
														$Contents .= "
														<input type='radio' name='status' id='status_".ORDER_STATUS_EXCHANGE_APPLY."' value='".ORDER_STATUS_EXCHANGE_APPLY."' checked><label for='status_".ORDER_STATUS_EXCHANGE_APPLY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label>
														<input type='radio' name='status' id='status_".ORDER_STATUS_EXCHANGE_ING."' value='".ORDER_STATUS_EXCHANGE_ING."' ><label for='status_".ORDER_STATUS_EXCHANGE_ING."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label>";
													}else{
														$Contents .= "
														<input type='radio' name='status' id='status_".ORDER_STATUS_RETURN_APPLY."' value='".ORDER_STATUS_RETURN_APPLY."'><label for='status_".ORDER_STATUS_RETURN_APPLY."'>".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</label>
														<input type='radio' name='status' id='status_".ORDER_STATUS_RETURN_ING."' value='".ORDER_STATUS_RETURN_ING."' checked><label for='status_".ORDER_STATUS_RETURN_ING."'>".getOrderStatus(ORDER_STATUS_RETURN_ING)."</label>";
													}
												}
												$Contents .= "
												</td>
											</tr>";
										}
									$Contents .= "
									</table>
									
									<table border='0' width='100%' cellspacing='1' cellpadding='0' style='margin-top:25px;' style='table-layout:fixed;'>
										<tr>
											<td>
												<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b>".($apply_status=='EA'?"교환":"반품")."상품정보</b></div>
											</td>
										</tr>
									</table>
									<table width='100%' border='0' cellpadding='0' cellspacing='1' class='input_table_box' style='table-layout:fixed;'>
										<tr height='30' bgcolor='#efefef' align=center>
											".($apply_status=='EA' ? "<td width='9%' class='m_td'><b>구분</b></td>" : "")."
											<td width='*' class='m_td'><b>상품명</b></td>
											<td width='15%' class='m_td'><b>옵션/판매단가(할인가)</b></td>
											<td width='5%' class='m_td'><b>수량</b></td>
											".($act!="claim_update" ? "<td width='6%' class='e_td' ><b>".($apply_status=='EA'?"교환":"반품")."수량</b></td>" : "")."
											<td width='10%' class='m_td small'><b>상품가격<br/>/할인액<br/>/적립금</b></td>
											<td width='13%' class='m_td'><b>실결제금액</b></td>
											<td width='8%' class='m_td'><b>배송비</b></td>
											<td width='8%' class='m_td'><b>처리상태</b></td>
										</tr>";


	for($i = 0; $i < count($product_info); $i++)
	{

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

		$discount_info = $currency_display[$admin_config["currency_unit"]]["front"].number_format($product_info[$i][total_sale_price],$decimals_value).$currency_display[$admin_config["currency_unit"]]["back"];
		
		if($dc_etc_str!=""){
			$discount_info.=" <label class='helpcloud' help_width='".$dc_etc_width."' help_height='".$dc_etc_height."' help_html='".$dc_etc_str."'><img src='../images/icon/q_icon.png' align=''></label>";
		}
		
		if($dc_coupon_str!=""){
			$discount_info.=" <label class='helpcloud' help_width='".$dc_coupon_width."' help_height='".$dc_coupon_height."' help_html='".$dc_coupon_str."'><img src='../images/".$admininfo[language]."/s_use_coupon.gif' align=''></label>";
		}

										$Contents .= "
										<tr height='30' align='center'>";

											if($apply_status=="EA"){
												if($act=="claim_update"){
													
													if($product_info[$i][exchange_delivery_type]){
														$exchange_delivery_type = $product_info[$i][exchange_delivery_type];
													}

													$Contents .= "
													<td bgColor='#ffffff'>";
														if($product_info[$i][claim_apply_product_yn]=="Y"){
															$Contents .= "요청상품";
														}else{
															$Contents .= "배송상품";
														}
													$Contents .= "
													</td>";
												}else{
													$Contents .= "
													<td bgColor='#ffffff'>
														요청상품 ";
														if($act=="claim_confirm"){
														}else{
															$Contents .= "
															<input type='button' value='동일상품' class='same_product_click' od_ix='".$product_info[$i][od_ix]."' pid='".$product_info[$i][pid]."' pname='".$product_info[$i][pname]."' option_text='".strip_tags($product_info[$i][option_text])."' pcnt='".$product_info[$i][pcnt]."' delivery_package ='".$product_info[$i][delivery_package]."' style='width:64px' />";
                                                            if(false) {
                                                                $sql = "select * from shop_product_options where pid = '" . $product_info[$i][pid] . "' and option_use='1' ";
                                                                $db->query($sql);

                                                                if ($db->total) {
                                                                    $Contents .= "
																<input type='button' value='다른옵션' class='different_option_click' pid='" . $product_info[$i][pid] . "' od_ix='" . $product_info[$i][od_ix] . "' delivery_package ='" . $product_info[$i][delivery_package] . "' style='width:64px' />";
                                                                }
                                                            }
															$Contents .= "
															<!--input type='button' value='타상품' class='different_product_click' od_ix='".$product_info[$i][od_ix]."' company_id='".$product_info[$i][company_id]."' surtax_yorn='".$product_info[$i][surtax_yorn]."' pcnt='".$product_info[$i][pcnt]."' style='width:64px' /-->";
														}
													$Contents .= "
													</td>";
												}
											}

											$Contents .= "
											<td bgColor='#ffffff'>
												<table width='100%'>
													<tr>
														<td align='center' width='70'>
														<a href='../product/goods_input.php?id=".$product_info[$i][pid]."'  title='".$LargeImageSize."' target='_blank' ><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $product_info[$i][pid], "m" , $product_info[$i])."'  onerror=\"this.src='".$admin_config[mall_data_root]."/images/noimg_52.gif'\" width=50 style='margin:5px;'></a><br/>";

														if($product_info[$i][product_type]=='21'||$product_info[$i][product_type]=='31'){
															$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($product_info[$i][product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$product_info[$i][product_type].".gif' align='absmiddle' ></label> ";
														}
														if($product_info[$i][stock_use_yn]=='Y'){
															$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
														}
											$Contents .= "
														</td>
														<td style='padding:5px 0 5px 0;line-height:130%' align='left'>";

												if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
													$Contents .= "<a href=\"javascript:PoPWindow('../seller/company.add.php?company_id=".$product_info[$i][company_id]."&mmode=pop',960,600,'brand')\"><b>".($product_info[$i][company_name] ? $product_info[$i][company_name]:"-")."</b></a><br>";
												}

												if(in_array($product_info[$i][product_type],$sns_product_type)){
													$Contents .= "<a href=\"/sns/shop/goods_view.php?id=".$product_info[$i][pid]."\" target=_blank>".$product_info[$i][pname]."</a>";
												}else{
													$Contents .= "<a href=\"/shop/goods_view.php?id=".$product_info[$i][pid]."\" target=_blank>";
													if($product_info[$i][product_type]=='99'||$product_info[$i][product_type]=='21'||$product_info[$i][product_type]=='31'){
														$Contents .= "<b class='".($product_info[$i][product_type]=='99' ? "red" : "blue")."' >".$product_info[$i][pname]."</b><br/><strong>".$product_info[$i][set_name]."<br /></strong>".$product_info[$i][sub_pname];
													}else{
														$Contents .= $product_info[$i][pname];
													}
													$Contents .= "</a>";
												}

											$Contents .= "
														</td>
													</tr>
												</table>
											</td>
											<td bgColor='#ffffff' align=left>
												<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
													<tr>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
															".strip_tags($product_info[$i][option_text])."".($product_info[$i][option_price] != '' ? " + ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($product_info[$i][option_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."":"")."
														</td>
													</tr>
													<tr>
														<td align='center' valign='middle'>
															".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($product_info[$i][psprice]+$product_info[$i][option_price],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
												</table>
											</td>
											<td bgColor='#ffffff'>".$product_info[$i][pcnt]." 개</td>";

											if($act!="claim_update"){
												$Contents .= "
												<td bgColor='#ffffff'>";
													if($act=="claim_confirm"){
														$Contents .= "<input type='hidden' name='apply_cnt[".$product_info[$i][od_ix]."]' value='".$apply_cnt[$product_info[$i][od_ix]]."'>
														".$apply_cnt[$product_info[$i][od_ix]]." 개";
													}else{
														$Contents .= "
														<select name='apply_cnt[".$product_info[$i][od_ix]."]'>";
															for($j=$product_info[$i][pcnt];$j>0;$j--){
																$Contents .= "<option value='".$j."'>".$j."</option>";
															}
														$Contents .= "
														</select>";
													}
												$Contents .= "
												</td>";
											}
											$Contents .= "
											<td bgColor='#ffffff' align=left>
												<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
													<tr>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
															".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($product_info[$i][pt_dcprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
													<tr>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
															".$discount_info."
														</td>
													</tr>
													<tr>
														<td align='center' valign='middle'>
															".number_format($product_info[$i][reserve] * $product_info[$i][pcnt],$decimals_value)." P
														</td>
													</tr>
												</table>
											</td>
											<td bgColor='#ffffff' align=left>
												<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
													<tr>
														<td class='m_td' style='border-bottom:1px solid #c5c5c5;border-right:1px solid #c5c5c5'>판매가</td>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5;'>
															 ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($product_info[$i][pt_dcprice],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
													<tr>
														<td class='m_td' style='border-bottom:1px solid #c5c5c5;border-right:1px solid #c5c5c5' width='40%'>공급가</td>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5;'>
															".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format(round($product_info[$i][pt_dcprice])/1.1,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
													<tr>
														<td class='m_td' style='border-right:1px solid #c5c5c5'>세액</td>
														<td align='center' valign='middle' style=''>
															".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($product_info[$i][pt_dcprice]-round($product_info[$i][pt_dcprice])/1.1,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
												</table>
											</td>";

											$delivery_pay_type = getDeliveryPayType($product_info[$i][delivery_pay_method]);		//배송비 결제수단 텍스트 리턴
											$delivery_method = getDeliveryMethod($product_info[$i][delivery_method]);			//배송방법 텍스트 리턴

											//배송비 분리 시작 2014-05-21 이학봉
											if($product_info[$i][delivery_package] == 'N' && $product_info[$i][delivery_pay_method] == '1'){	//묶음이거나 선불일겨우 (착불은 개별로 처리)

												//|| $b_product_type != $product_info[$i][product_type] || $b_factory_info_addr_ix != $product_info[$i][factory_info_addr_ix] || $b_delivery_type  != $product_info[$i][delivery_type]
												if($bori_company_id != $product_info[$i][company_id] ){
													$sql = "SELECT 
															COUNT(DISTINCT(od.od_ix)) AS com_cnt
														FROM 
															".TBL_SHOP_ORDER." o,
															".TBL_SHOP_ORDER_DETAIL." od
														where 
															o.oid = od.oid 
															and o.oid = '".$product_info[$i][oid]."' 
															and od.ori_company_id='".$product_info[$i][ori_company_id]."'
															and od.delivery_type = '".$product_info[$i][delivery_type]."'
															and od.delivery_package = 'N'
															and od.delivery_method = '".$product_info[$i][delivery_method]."'
															and od.delivery_pay_method = '".$product_info[$i][delivery_pay_method]."'
															and od.delivery_addr_use = '".$product_info[$i][delivery_addr_use]."'
															and od.factory_info_addr_ix = '".$product_info[$i][factory_info_addr_ix]."'
															".str_replace("WHERE","and",$where)." 
															";

													$db->query($sql);//$od_db는 상단에서 선언
													$db->fetch();
													$com_cnt=$db->dt["com_cnt"];
													
													$Contents .="<td bgColor='#ffffff' class='' align=center style='line-height:140%;' rowspan='".$com_cnt."'>
														".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($product_info[$i][delivery_dcprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."<br>
														".($product_info[$i][delivery_type] == '1' ? "통합배송":"입점업체배송")."<br>
														".$delivery_method."<br>
														".($product_info[$i][delivery_addr_use] == '1' ? "출고지별 배송<br>".getFactoryAddrName($product_info[$i][factory_info_addr_ix])."<br>":"")."
														".$delivery_pay_type."<br>
														".($product_info[$i][delivery_package] == 'N' ? "묶음배송":"")."</td>";
												}

												$bori_company_id = $product_info[$i][company_id];

											}else{

												$sql = "SELECT 
															COUNT(DISTINCT(od.od_ix)) AS com_cnt
														FROM 
															".TBL_SHOP_ORDER." o,
															".TBL_SHOP_ORDER_DETAIL." od
														where 
															o.oid = od.oid 
															and o.oid = '".$product_info[$i][oid]."'
															and od.pid = '".$product_info[$i][pid]."'
															and od.ori_company_id='".$product_info[$i][ori_company_id]."'
															and od.delivery_type = '".$product_info[$i][delivery_type]."'
															and od.delivery_package = 'Y'
															and od.delivery_method = '".$product_info[$i][delivery_method]."'
															and od.delivery_pay_method = '".$product_info[$i][delivery_pay_method]."'
															and od.delivery_addr_use = '".$product_info[$i][delivery_addr_use]."'
															and od.factory_info_addr_ix = '".$product_info[$i][factory_info_addr_ix]."'
															".str_replace("WHERE","and",$where)." 
															";

													$db->query($sql);//$od_db는 상단에서 선언
													$db->fetch();
													$com_cnt=$db->dt["com_cnt"];

												if(ch_set_product_bool($product_info[$i][option_kind])){		//세트상품일경우

													if($b_oid != $product_info[$i][oid] || $bproduct_id != $product_info[$i][pid]){

														$Contents .="<td bgColor='#ffffff' class='' align=center style='line-height:140%;' ".($b_oid != $product_info[$i][oid] || $bproduct_id != $product_info[$i][pid] ? "rowspan='".$com_cnt."'":"").">
															".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($product_info[$i][delivery_dcprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."<br>
															".($product_info[$i][delivery_type] == '1' ? "통합배송":"입점업체배송")."<br>
															".$delivery_method."<br>
															".($product_info[$i][delivery_addr_use] == '1' ? "출고지별 배송<br>".getFactoryAddrName($product_info[$i][factory_info_addr_ix])."<br>":"")."
															".$delivery_pay_type."<br>
															<img src='../images/".$admininfo["language"]."/delivery_policy_1.gif' title='개별배송상품'></td>";
													}

												}else{			//일반상품
													if($b_oid != $product_info[$i][oid] || $bproduct_id != $product_info[$i][pid] || $bset_group != $product_info[$i][set_group]){// rowspan='".$com_cnt."'
														$Contents .="<td bgColor='#ffffff' class='' align=center style='line-height:140%;'>
															".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($product_info[$i][delivery_dcprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."<br>
															".($product_info[$i][delivery_type] == '1' ? "통합배송":"입점업체배송")."<br>
															".$delivery_method."<br>
															".($product_info[$i][delivery_addr_use] == '1' ? "출고지별 배송<br>".getFactoryAddrName($product_info[$i][factory_info_addr_ix])."<br>":"")."
															".$delivery_pay_type."<br>
															<img src='../images/".$admininfo["language"]."/delivery_policy_1.gif' title='개별배송상품'>
															</td>";
													}
												}
											}
											
											$Contents .="
											<td bgColor='#ffffff' align=center style='line-height:130%'>";

											$Contents .= getOrderStatus($product_info[$i][status]);

											if($product_info[$i]["admin_message"]!="") {
													$Contents .="<br><b>".$product_info[$i]["admin_message"]."</b>";
											}

											if($product_info[$i][di_date] && $product_info[$i][status] == "DI"){
												$Contents .= "<br />(".substr($product_info[$i][di_date],0,10).")";
											}

											if($product_info[$i][dc_date] && $product_info[$i][status] == "DC"){
												$Contents .= "<br />(".substr($product_info[$i][dc_date],0,10).")";
											}

											if(trim($product_info[$i][ac_date])){
												$Contents .= "<br /><a href=\"/admin/order/accounts_detail.php?ac_ix=".$product_info[$i][ac_ix]."\" target=_blank>(".substr($product_info[$i][ac_date],0,4)."-".substr($product_info[$i][ac_date],4,2)."-".substr($product_info[$i][ac_date],6,2).")</a> ";
											}

											if(($product_info[$i][invoice_no] || $product_info[$i][quick]) && ($product_info[$i][status] == "DI" || $product_info[$i][status] == "DC" || $product_info[$i][status] == "BF")){
												$Contents .= "<br><a href=\"javascript:searchGoodsFlow('".$product_info[$i][quick]."', '".str_replace("-","",$product_info[$i][invoice_no])."')\">".codeName($product_info[$i][quick])." </a> ";
											}

											if($admininfo[admin_level]==9){
												$Contents .= "<br/>".getOrderStatus($product_info[$i][delivery_status]);
											}


											$Contents .="
											</td>";

										$Contents .= "
										</tr>";
									
									//교환 요청시 교환재배송상품선택하는폼


									if($apply_status=="EA" && $act!="claim_update"){
										$Contents .= "
										<tr align='center' >
											<td bgColor='#ffffff' height='50' rowspan='".count($ec_product_pid[$product_info[$i][od_ix]])."'>
												배송상품
											</td>";

											if($act=="claim_confirm"){
												
												if($product_info[$i]['buyer_type']==2){//buyer_type (1:소매,2:도매)
													$o_select_price='o.option_wholesale_listprice AS option_listprice, o.option_wholesale_price AS option_price';
													$select_price = 'wholesale_price as listprice, wholesale_sellprice as psprice ';
													$select_commission='wholesale_commission';
												}else{
													$o_select_price='o.option_listprice, o.option_price';
													$select_price = 'listprice, sellprice as psprice';
													$select_commission='commission';
												}
												

												//ec_product_select_type, ec_product_pid, ec_product_opd_id, ec_product_cnt 변수가 넘어옴!
												foreach($ec_product_select_type[$product_info[$i][od_ix]] as $key => $value){
													
													$tmp_data = array();
													$coupon_total_dc_price=0;

													if($key!=0){
														$Contents .= "</tr><tr>";
													}
										
													if($value=="D"){//다른상품일떄!!

														/*!!!!!!!!!!상품 옵션정보 및 할인정보 미리 만들기!!!!!!!!!!*/
														
														if($ec_product_pid[$product_info[$i][od_ix]]!=$product_info[$i]["pid"]){
															//옵션정보
															$sql="SELECT 
																	pr.cid , c.depth ,p.id as pid,p.product_type,p.stock_use_yn,p.admin as company_id,p.pname,p.coprice, ".$select_price." ,
																	p.brand as brand_code, p.brand_name, p.pcode, p.paper_pname, p.trade_admin as trade_company, p.surtax_yorn, p.barcode, p.md_code,
																	p.one_commission, case when p.one_commission='Y' then p.".$select_commission." else csd.".$select_commission." end as commission,
																	ccd.com_name as company_name, ".$ec_product_cnt[$product_info[$i][od_ix]][$key]." as claim_apply_cnt
																FROM 
																	shop_product p
																	left join shop_product_relation pr on (p.id=pr.pid and pr.basic='1')
																	left join shop_category_info c on (c.cid=pr.cid) 
																	left join common_company_detail ccd on (ccd.company_id=p.admin)
																	left join common_seller_delivery csd on (csd.company_id=ccd.company_id)
																WHERE
																	p.id = '".$ec_product_pid[$product_info[$i][od_ix]][$key]."' ";
															$db->query($sql);
															$db->fetch("object");
															$tmp_data = $db->dt;
														}else{
															$tmp_data=$product_info[$i];
														}

														$options = explode("-",$ec_product_opd_id[$product_info[$i][od_ix]][$key]);
														
														$options_text="";
														$pname="";
														$sub_pname="";
														$option_kind="";
														$pcode="";
														$coprice="";
														$listprice="";
														$barcode="";
														$sellprice="";
														$option_price="";

														for($o=0;$o<count($options);$o++){
															if($options[$o]){
																$sql = "select o.option_div,ot.option_name, ot.option_kind, option_code, $o_select_price, o.option_coprice, option_barcode 
																			from shop_product_options_detail o,shop_product_options ot 
																			where id = '".$options[$o]."' and o.opn_ix = ot.opn_ix";
																$sdb->query($sql);
																$sdb->fetch("object");
																
																
																if($sdb->dt[option_kind] == "x2" || $sdb->dt[option_kind] == "s2"){
																	$pname = $db->dt[pname]." - ".$sdb->dt[option_name];
																	$options_text .= $sdb->dt[option_div]."";
																}else if($sdb->dt[option_kind] != "r"){//옵션이 한개만 등록되는 것을 방지 kbk 12/04/12
																	if($sdb->dt[option_price] > 0 && $sdb->dt[option_kind] != "b"){
																		$options_text .= $sdb->dt[option_name]." : ".$sdb->dt[option_div]."(".number_format($sdb->dt[option_price],$decimals_value).")<br>";
																	}else{
																		 $options_text .= $sdb->dt[option_name]." : ".$sdb->dt[option_div]."<br>";
																	}
																}
																
																if($sdb->dt[option_kind] == "b" || $sdb->dt[option_kind] == "a" || $sdb->dt[option_kind] == "x" || $sdb->dt[option_kind] == "c" || $sdb->dt[option_kind] == "x2" || $sdb->dt[option_kind] == "s2"){
		
																	$sub_pname = $sdb->dt[option_div];
																	if($sdb->dt[option_kind] == "b"){
																		$option_kind = "";
																	}else{
																		$option_kind = $sdb->dt[option_kind];
																	}
																	$pcode = $sdb->dt[option_code];
																	$coprice = $sdb->dt[option_coprice];
																	if($sdb->dt[option_listprice] == 0){
																		$listprice = $sdb->dt[option_price];
																	}else{
																		$listprice = $sdb->dt[option_listprice];
																	}
																	$barcode = $sdb->dt[option_barcode];
																	$sellprice = $sdb->dt[option_price];
					
																}else if($sdb->dt[option_kind] == "s" || $sdb->dt[option_kind] == "p" || $sdb->dt[option_kind] == "c1" || $sdb->dt[option_kind] == "c2" || $sdb->dt[option_kind] == "i1" || $sdb->dt[option_kind] == "i2"){
																	$option_price += $sdb->dt[option_price];
																}
															}
														}

														$tmp_data["ec_select_type"] = $value;
														$tmp_data["claim_discount_type"]="array";

														if($options_text !='')		$tmp_data["option_text"]=$options_text;
														if($pname !='')				$tmp_data["pname"]=$pname;
														if($sub_pname !='')			$tmp_data["sub_pname"]=$sub_pname;
														if($option_kind !='')		$tmp_data["option_kind"]=$option_kind;
														if($pcode !='')				$tmp_data["pcode"]=$pcode;
														if($coprice !='')			$tmp_data["coprice"]=$coprice;
														if($listprice !='')			$tmp_data["listprice"]=$listprice;
														if($barcode !='')			$tmp_data["barcode"]=$barcode;
														if($sellprice !='')			$tmp_data["psprice"]=$sellprice;
														if($option_price !='')		$tmp_data["option_price"]=$option_price;
														

														//할인정보
														$goods_infos=array();
														$discount_desc=array();
														$discount_item=array();

														$goods_infos[$tmp_data["pid"]][pid] = $tmp_data["pid"];
														$goods_infos[$tmp_data["pid"]][amount] = $tmp_data["claim_apply_cnt"];
														$goods_infos[$tmp_data["pid"]][cid] = $tmp_data["cid"];
														$goods_infos[$tmp_data["pid"]][depth] = $tmp_data["depth"];
														$discount_info_array = DiscountRult($goods_infos, $tmp_data["cid"], $tmp_data["depth"], $tmp_data["claim_apply_cnt"]);
														
														$discount_item = $discount_info_array[$tmp_data["pid"]]; 
														if(is_array($discount_item)){
															$_dcprice = $tmp_data["psprice"];
															foreach($discount_item as $_key => $_item){ 
																$_item["standard_price"]=$_dcprice;//기준금액
																if($_item[discount_value_type] == "1"){ // %
																	//$_dcprice = $_dcprice*(100 - $_item[discount_value])/100;
																	$_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]) ;
																	$_item["discount_price"]=($_item["standard_price"]-$_dcprice)*$tmp_data["claim_apply_cnt"];//할인된금액( *수량)
																}else if($_item[discount_value_type] == "2"){// 원
																	$_dcprice = $_dcprice - $_item[discount_value];
																	$_item["discount_price"]=$_item[discount_value]*$pcount;//할인된금액( *수량)
																}

																$discount_desc[] = $_item;
															}
															$dcprice = $_dcprice;
														}else{
															$dcprice = $tmp_data["psprice"];
														}
														
														$tmp_data["dcprice"]=$dcprice;
														$tmp_data["discount_desc"]=$discount_desc;
	
														$discount_info_text = $currency_display[$admin_config["currency_unit"]]["front"].number_format(($tmp_data[psprice]-$tmp_data[dcprice])*$tmp_data["claim_apply_cnt"],$decimals_value).$currency_display[$admin_config["currency_unit"]]["back"];
														
														if($tmp_data[psprice]-$tmp_data[dcprice] > 0){
															$discount_info_text .= "
															<label class='helpcloud' help_width='140' help_height='55' help_html='".viewSaleDetail($tmp_data["discount_desc"],"return")."'><img src='../images/icon/q_icon.png' align=''></label>";
														}

														//재고정보불러오기
														if($tmp_data["stock_use_yn"]=="Y" && $tmp_data["pcode"]!=''){
															$db->query("select gid, gu_ix from inventory_goods_unit where gu_ix ='".$tmp_data["pcode"]."'");
															$db->fetch();
															$tmp_data["gid"] = $db->dt["gid"];
															$tmp_data["gu_ix"] = $db->dt["gu_ix"];
														}

													}else{
														$product_info[$i]["claim_discount_type"]="db";
														$product_info[$i]["claim_apply_cnt"]=$apply_cnt[$product_info[$i][od_ix]];
														$product_info[$i]["ec_select_type"]=$value;
														$tmp_data=$product_info[$i];

														$discount_info_text = $discount_info;
														
														if($product_info[$i][pcnt]!=$apply_cnt[$product_info[$i][od_ix]]){
															if(fetch_order_status_div('DC',$apply_status,"type",$reason_code)=="B"){
																$Coupondata["oid"]=$product_info[$i][oid];
																$Coupondata["od_ix"]=$product_info[$i][od_ix];
																$CouponReturn = orderUseCouponReturnCheck($Coupondata,$apply_cnt[$product_info[$i][od_ix]]);
																$coupon_total_dc_price=$CouponReturn["coupon_total_dc_price"];

																$discount_info_text = $currency_display[$admin_config["currency_unit"]]["front"].number_format($coupon_total_dc_price,$decimals_value).$currency_display[$admin_config["currency_unit"]]["back"];
																
																if($dc_etc_str!=""){
																	$discount_info_text.=" <label class='helpcloud' help_width='".$dc_etc_width."' help_height='".$dc_etc_height."' help_html='".$dc_etc_str."'><img src='../images/icon/q_icon.png' align=''></label>";
																}

																if($CouponReturn["coupon_str"]!=""){
																	$discount_info_text.=" <label class='helpcloud' help_width='".$CouponReturn["coupon_width"]."' help_height='".$CouponReturn["coupon_height"]."' help_html='".$CouponReturn["coupon_str"]."'><img src='../images/".$admininfo[language]."/s_use_coupon.gif' align=''></label>";
																}
																
																if($coupon_total_dc_price > 0){
																	$tmp_data["claim_discount_type"]="cupon";
																	$product_info[$i]["claim_discount_type"]="cupon";
																	//$tmp_data["discount_desc"]=$CouponReturn["coupon_dc_info"];
																}else{
																	$tmp_data["claim_discount_type"]="cupon";
																}
															}
														}
													}

													$ea_pdata[$product_info[$i][od_ix]][$key]=$tmp_data;
													
													$Contents .= "
													<td bgColor='#ffffff'>
														<table width='100%'>
															<tr>
																<td align='center' width='70'>

																<input type='hidden' name='ea_pdata[".$product_info[$i][od_ix]."][".$key."]' value='".urlencode(json_encode($tmp_data))."' />

																<a href='../product/goods_input.php?id=".$tmp_data[pid]."'  title='".$LargeImageSize."' target='_blank' ><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $tmp_data[pid], "m" , $tmp_data)."'  onerror=\"this.src='".$admin_config[mall_data_root]."/images/noimg_52.gif'\" width=50 style='margin:5px;'></a><br/>";

																if($tmp_data[product_type]=='21'||$tmp_data[product_type]=='31'){
																	$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($tmp_data[product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$tmp_data[product_type].".gif' align='absmiddle' ></label> ";
																}
																if($tmp_data[stock_use_yn]=='Y'){
																	$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
																}
													$Contents .= "
																</td>
																<td style='padding:5px 0 5px 0;line-height:130%' align='left'>";

														if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
															$Contents .= "<a href=\"javascript:PoPWindow('../seller/company.add.php?company_id=".$tmp_data[company_id]."&mmode=pop',960,600,'company_add')\"><b>".($tmp_data[company_name] ? $tmp_data[company_name]:"-")."</b></a><br>";
														}

														if(in_array($tmp_data[product_type],$sns_product_type)){
															$Contents .= "<a href=\"/sns/shop/goods_view.php?id=".$tmp_data[pid]."\" target=_blank>".$tmp_data[pname]."</a>";
														}else{
															$Contents .= "<a href=\"/shop/goods_view.php?id=".$tmp_data[pid]."\" target=_blank>".$tmp_data[pname]."</a>";
														}

													$Contents .= "
																</td>
															</tr>
														</table>
													</td>
													<td bgColor='#ffffff' align=left>
														<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
															<tr>
																<td align='left' valign='middle' style='border-bottom:1px solid #c5c5c5'>
																	".strip_tags($tmp_data[option_text])."".($tmp_data[option_price] != '' ? " + ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($tmp_data[option_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."":"")."
																</td>
															</tr>
															<tr>
																<td align='center' valign='middle'>
																	".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($tmp_data["psprice"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
																</td>
															</tr>
														</table>
													</td>
													<td bgColor='#ffffff' align='center' colspan='2'>".$tmp_data["claim_apply_cnt"]." 개</td>
													<td bgColor='#ffffff' align='left'>
														<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
															<tr>
																<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
																	".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format(($tmp_data[dcprice]+$tmp_data["option_price"])*$tmp_data["claim_apply_cnt"] - $coupon_total_dc_price,$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."
																</td>
															</tr>
															<tr>
																<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
																	".$discount_info_text."
																</td>
															</tr>
															<tr>
																<td align='center' valign='middle'>
																	0 P
																</td>
															</tr>
														</table>
													</td>
													<td bgColor='#ffffff' align=left>
														<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
															<tr>
																<td class='m_td' style='border-bottom:1px solid #c5c5c5;border-right:1px solid #c5c5c5'>판매가</td>
																<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5;'>
																	 ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format(($tmp_data[dcprice]+$tmp_data["option_price"])*$tmp_data["claim_apply_cnt"] - $coupon_total_dc_price,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
																</td>
															</tr>
															<tr>
																<td class='m_td' style='border-bottom:1px solid #c5c5c5;border-right:1px solid #c5c5c5' width='40%'>공급가</td>
																<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5;'>
																	".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format(round(($tmp_data[dcprice]+$tmp_data["option_price"])*$tmp_data["claim_apply_cnt"] - $coupon_total_dc_price)/1.1,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
																</td>
															</tr>
															<tr>
																<td class='m_td' style='border-right:1px solid #c5c5c5'>세액</td>
																<td align='center' valign='middle' style=''>
																	".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format(($tmp_data[dcprice]+$tmp_data["option_price"])*$tmp_data["claim_apply_cnt"]-round(($tmp_data[dcprice]+$tmp_data["option_price"])*$tmp_data["claim_apply_cnt"])/1.1,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
																</td>
															</tr>
														</table>
													</td>
													<td bgColor='#ffffff' align=center style='line-height:130%' colspan='2'>
														비고
													</td>";
												}

											}else{
												$Contents .= "
												<td bgColor='#ffffff' colspan='".($admininfo[admin_level]==9 ? "8" : "7")."'>
													<table width='100%' height='50' border=0 cellpadding=0 cellspacing=0 id='ea_product_table_".$product_info[$i][od_ix]."'>
														<tr id='ea_product_no_select_".$product_info[$i][od_ix]."'>
															<td align='center' valign='middle' colspan='5' height='50'>
																<select validation='true' title='교환할 상품' style='display:none;'><option></option></select>
																교환할 상품을 선택해 주세요. 
															</td>
														</tr>
													</table>
												</td>";
											}
										$Contents .= "
										</tr>";
									}
									
	}							

								$Contents .= "
									</table>";

								if($act=="claim_update"){
									$sql="SELECT 
										*
									FROM 
										shop_order_detail_deliveryinfo odd 
									WHERE
										odd.odd_ix in (select odd_ix from shop_order_detail where oid='".$oid."' and claim_group=".$claim_group.")
									and
										odd.order_type in ('3','4')
									LIMIT 0,1";
									$db->query($sql);
									$return_delivery_info = $db->fetch();
								}

								$Contents .= "
									<table border='0' width='100%' cellspacing='1' cellpadding='0' style='margin-top:25px;' style='table-layout:fixed;'>
										<tr>
											<td>
												<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b>".($apply_status=='EA'?"교환":"반품")."배송정보</b></div>
											</td>
										</tr>
									</table>
									<table width='100%' border='0' cellpadding='0' cellspacing='1' class='input_table_box' style='table-layout:fixed;'>
										<tr>
											<td class='input_box_title'>  구매자 ".($apply_status=='EA'?"교환":"반품")."발송여부</td>
											<td class='input_box_item' colspan='3'>";
											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='send_yn' value='".$send_yn."'>
												".($send_yn == "N" ?  "아직 ".($apply_status=='EA'?"교환":"반품")."상품을 보내지 않았습니다.": "")."
												".($send_yn == "Y" ?  "이미 ".($apply_status=='EA'?"교환":"반품")."상품을 발송하였습니다.": "");
											}else{
												$Contents .= "
												<input type='radio' name='send_yn' id='send_yn_n' value='N' ".($return_delivery_info["send_yn"]=="N" ? "checked" : "")." $disabled><label for='send_yn_n'>아직 ".($apply_status=='EA'?"교환":"반품")."상품을 보내지 않았습니다.</label>
												<input type='radio' name='send_yn' id='send_yn_y' value='Y' ".($return_delivery_info["send_yn"]=="Y" || $return_delivery_info["send_yn"]==""? "checked" : "")." $disabled><label for='send_yn_y'>이미 ".($apply_status=='EA'?"교환":"반품")."상품을 발송하였습니다.</label>";
											}
											$Contents .= "
											</td>
										</tr>
										<tr class='send_yn_n' height=50>
											<td class='input_box_title'>  ".($apply_status=='EA'?"교환":"반품")."발송방법</td>
											<td class='input_box_item' colspan='3' style='line-height:170%;'>";
											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='send_type' value='".$send_type."'>
												".($send_type == "2" ?  "지정택배방문요청(셀러업체와 계약된 택배업체 방문수령 수거)": "")."
												".($send_type == "1" ?  "직접발송(구매자께서 개별로 배송할 경우)": "");
											}else{
												$Contents .= "
												<input type='radio' name='send_type' id='send_type_2' value='2' ".($return_delivery_info["send_type"]=="2" || $return_delivery_info["send_type"]==""? "checked" : "")." $disabled><label for='send_type_2'>지정택배방문요청(셀러업체와 계약된 택배업체 방문수령 수거)</label><br/>
												<input type='radio' name='send_type' id='send_type_1' value='1' ".($return_delivery_info["send_type"]=="1"? "checked" : "")." $disabled><label for='send_type_1'>직접발송(구매자께서 개별로 배송할 경우)</label>";
											}
											$Contents .= "
											</td>
										</tr>
										<tr class='send_yn_y'>
											<td class='input_box_title'>  ".($apply_status=='EA'?"교환":"반품")."발송정보</td>
											<td class='input_box_item' colspan='3'>";
											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='delivery_method' value='".$delivery_method."'>
												<input type='hidden' name='quick' value='".$quick."'>
												<input type='hidden' name='deliverycode' value='".$deliverycode."'>
												".DeliveryMethod("delivery_method",$delivery_method,"","text")." 
												".deliveryCompanyList($quick,"text","",$admininfo[company_id])."  
												".$deliverycode;
											}else{
												$Contents .= "
												".DeliveryMethod("delivery_method",$return_delivery_info["delivery_method"],"","select")." 
												".deliveryCompanyList($return_delivery_info["quick"],"select","",$admininfo[company_id])." 
												<input type='text' name='deliverycode' class=textbox style='width:70px' value='".$return_delivery_info["invoice_no"]."' validation=false title='송장번호'>";
											}
											$Contents .= "
											</td>
										</tr>
										<tr class='send_yn_y'>
											<td class='input_box_title'>  ".($apply_status=='EA'?"교환":"반품")."발송시 배송비</td>
											<td class='input_box_item' colspan='3' >";
											if($act=="claim_confirm"){
												$Contents .= "
												<input type='hidden' name='delivery_pay_type' value='".$delivery_pay_type."'>
												".($delivery_pay_type == "1" ?  "선불": "")."
												".($delivery_pay_type == "2" ?  "착불(착불배송비가 배송비 배송비보다 높을경우 추가비용이 발생할수 있습니다)": "");
											}else{
												$Contents .= "
												<input type='radio' name='delivery_pay_type' id='delivery_pay_type_1' value='1' ".($return_delivery_info["delivery_pay_type"]=="1" || $return_delivery_info["delivery_pay_type"]=="" ? "checked" : "")." $disabled><label for='delivery_pay_type_1'>선불</label><br/>
												<input type='radio' name='delivery_pay_type' id='delivery_pay_type_2' value='2' ".($return_delivery_info["delivery_pay_type"]=="2" ? "checked" : "")." $disabled><label for='delivery_pay_type_2'>착불(착불배송비가 배송비 배송비보다 높을경우 추가비용이 발생할수 있습니다)</label>";
											}
											$Contents .= "
											</td>
										</tr>";

										if($act=="claim_update"){
											$sql="SELECT 
												*
											FROM 
												shop_order_detail_deliveryinfo odd 
											WHERE
												odd.odd_ix in (select odd_ix from shop_order_detail where oid='".$oid."' and claim_group=".$claim_group.")
											and
												odd.order_type ='2'
											LIMIT 0,1";
											$db->query($sql);
											$delivery_info = $db->fetch();
											$delivery_info_odd_ix = $delivery_info["odd_ix"];
											list($zip1,$zip2) = explode("-", $delivery_info["zip"]);
											$rname = $delivery_info["rname"];
											$rtel=$delivery_info["rtel"];
											$addr1=$delivery_info["addr1"];
											$addr2=$delivery_info["addr2"];
											$delivery_msg=$delivery_info["msg"];
										}else{
                                            if($act=="claim_confirm"){

                                            }else {
                                                $sql="SELECT rname,rtel,rmobile,zip,addr1,addr2 FROM shop_order_detail_deliveryinfo WHERE odd_ix in ( select odd_ix from shop_order_detail where od_ix in ('".str_replace("|","','",$od_ix_str)."')) limit 0,1 ";
                                                $db->query($sql);
                                                $db->fetch();

                                                list($zip1,$zip2) = explode("-", $db->dt[zip]);
                                                $rname=$db->dt[rname];
                                                $rtel=$db->dt[rtel];
                                                $addr1=$db->dt[addr1];
                                                $addr2=$db->dt[addr2];
                                            }
										}

										if($apply_status=='EA'){

											if(! empty($zip2)){
												$return_zip_all = $zip1."-".$zip2;
											}else{
												$return_zip_all = $zip1;
											}

											$Contents .= "
											<tr height=165>
												<td class='input_box_title'>  교환재배송상품 배송주소</td>
												<td class='input_box_item' colspan='3' >
													<table border='0' cellpadding='2' cellspacing='0' style='table-layout:fixed;width:100%' class='table_addr'>";
													if($act=="claim_confirm"){
														$Contents .= "
														<col width='60px'>
														<col width='*'>
														<tr height=24>
															<td align='center'>
																이&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;름 :
															</td>
															<td>
																<input type='hidden' name='rname' value='".$rname."'>
																".$rname."
															</td>
														</tr>
														<tr height=24>
															<td align='center'>
																주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소 :
															</td>
															<td>
																<input type='hidden' name='zip1' value='".$zip1."'>
																<input type='hidden' name='zip2' value='".$zip2."'>
																<input type='hidden' name='addr1' value='".$addr1."'>
																<input type='hidden' name='addr2' value='".$addr2."'>
																[".$return_zip_all."] ".$addr1." ".$addr2."
															</td>
														</tr>
														<tr height=24>
															<td align='center'>
																전달사항 :
															</td>
															<td>
																<input type='hidden' name='delivery_msg' value='".$delivery_msg."'>
																".$delivery_msg."
															</td>
														</tr>
														<tr height=24>
															<td align='center'>
																전화번호 :
															</td>
															<td>
																<input type='hidden' name='rtel' value='".$rtel."'>
																".$rtel."
															</td>
														</tr>
														";
													}else{
														$Contents .= "
														<col width='60px'>
														<col width='120px'>
														<col width='*'>
														<tr height=24>
															<td align='center'>
																이&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;름 :
															</td>
															<td colspan=2>
																".($act=="claim_update" ? "<input type='hidden' name='delivery_info_odd_ix' value='".$delivery_info_odd_ix."' >" : "")."
																<input type='text' class='textbox' name='rname' id='rname' value='".$rname."' >
															</td>
														</tr>
														<tr height=24>
															<td rowspan='3' align='center'>
																주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소 :
															</td>
															<td >
																<input type='text' class='textbox' name='zip1' id='zipcode1' size='5' maxlength='3' value='".$zip1."' readonly style='width:40px;' />
															</td>
															<td style='padding:1px 0 0 5px;'>
																<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1');\" style='cursor:pointer;vertical-align:middle;' align='absmiddle' /> 
															</td>
														</tr>
														<tr height=24>
															<td colspan=2>
																<input type=text name='addr1'  id='addr1' value='".$addr1."' size=50 class='textbox'  style='width:75%' validation=true title='교환발송 주소' readonly>
															</td>
														</tr>
														<tr height=24>
															<td colspan=2>
																<input type=text name='addr2'  id='addr2'  value='".$addr2."' size=70 class='textbox' validation=true title='교환발송 상세주소' style='width:75%'> (상세주소)
															</td>
														</tr>
														<tr height=24>
															<td align='center'>
																전달사항 :
															</td>
															<td colspan=2>
																<input type='text' class='textbox' name='delivery_msg' id='delivery_msg' value='".$delivery_msg."' style='width:75%'>
															</td>
														</tr>
														<tr height=24>
															<td align='center'>
																전화번호 :
															</td>
															<td colspan=2>
																<input type='text' class='textbox' name='rtel' id='rtel' value='".$rtel."' >
															</td>
														</tr>
														";
													}
													$Contents .= "
													</table>
												</td>
											</tr>";
										}
										
										if($act=="claim_update"){
											$return_delivery_info_odd_ix = $return_delivery_info["odd_ix"];
											list($return_zip1,$return_zip2) = explode("-", $return_delivery_info["zip"]);
											$return_rname = $return_delivery_info["rname"];
											$return_rtel=$return_delivery_info["rtel"];
											$return_addr1=$return_delivery_info["addr1"];
											$return_addr2=$return_delivery_info["addr2"];
											$return_delivery_msg=$return_delivery_info["msg"];
										}else{
											if($act!="claim_confirm"){
												$sql="select * from shop_delivery_address where addr_ix in (SELECT dt.exchange_info_addr_ix FROM shop_delivery_template dt, ".TBL_SHOP_ORDER_DETAIL." od WHERE od.dt_ix=dt.dt_ix and od.od_ix in ('".str_replace('|',"','",$od_ix_str)."')) ";
												$db->query($sql);
												if($db->total) {
													$db->fetch();
													list($return_zip1,$return_zip2)=explode("-",$db->dt[zip_code]);
													$return_rname=$db->dt[addr_name];
													$return_addr1=$db->dt[address_1];
													$return_addr2=$db->dt[address_2];
													$return_rtel=$db->dt[addr_phone];
												}
											}
										}

				if(! empty($return_zip2)){
					$return_zip_all = $return_zip1."-".$return_zip2;
				}else{
					$return_zip_all = $return_zip1;
				}

				$Contents .= "		<tr class='send_yn_n'>
											<td class='input_box_title'> 
												<span class='send_type_2'>
													".($apply_status=='EA'?"교환":"반품")."요청상품 수거주소 ".($act=="claim_confirm" || $apply_status=="RA" || $act=="claim_update" ? "" : "<br/>
													<!--input type='checkbox' id='same_addr' /><label for='same_addr'>상동</label-->" )."
												</span>
												<span class='send_type_1'>".($apply_status=='EA'?"교환":"반품")."요청상품 보낼주소</span>
											</td>
											<td class='input_box_item' colspan='3' style='padding:10px;'>
												<table border='0' cellpadding='2' cellspacing='0' style='table-layout:fixed;width:100%' class='table_return_addr'>";
												if($act=="claim_confirm"){
													$Contents .= "
													<col width='60px'>
													<col width='*'>
													<tr height=24>
														<td align='right'>
															이&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;름 :
														</td>
														<td>
															<input type='hidden' name='return_rname' value='".$return_rname."' >
															&nbsp;".$return_rname."
														</td>
													</tr>
													<tr height=24>
														<td align='right'>
															주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소 :
														</td>
														<td>
															<input type='hidden' name='return_zip1' value='".$return_zip1."'>
															<input type='hidden' name='return_zip2' value='".$return_zip2."'>
															<input type='hidden' name='return_addr1' value='".$return_addr1."'>
															<input type='hidden' name='return_addr2' value='".$return_addr2."'>
															&nbsp;[".$return_zip_all."] ".$return_addr1." ".$return_addr2."
														</td>
													</tr>
													<tr height=24>
														<td align='right'>
															전달사항 :
														</td>
														<td>
															<input type='hidden' name='return_delivery_msg' value='".$return_delivery_msg."'>
															&nbsp;".$return_delivery_msg."
														</td>
													</tr>
													<tr height=24>
														<td align='right'>
															전화번호 :
														</td>
														<td>
															<input type='hidden' name='return_rtel' value='".$return_rtel."'>
															&nbsp;".$return_rtel."
														</td>
													</tr>";
												}else{
													$Contents .= "
													<col width='60px'>
													<col width='120px'>
													<col width='*'>
													<tr height=24>
														<td align='center'>
															이&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;름 :
														</td>
														<td colspan=2>
															".($act=="claim_update" ? "<input type='hidden' name='return_delivery_info_odd_ix' value='".$return_delivery_info_odd_ix."' >" : "")."
															<input type='text' class='textbox' name='return_rname' id='return_rname' value='".$return_rname."' com='".$return_rname."' user='".$rname."'>
														</td>
													</tr>
													<tr height=24>
														<td rowspan='3' align='center'>
															주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소 :
														</td>
														<td>
															<input type='text' class='textbox' name='return_zip1' id='return_zip1' size='5' maxlength='3' value='".$return_zip1."' com='".$return_zip1."' user='".$zip1."' readonly style='width:40px;'>
														</td>
														<td style='padding:1px 0 0 5px;'>
															<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('5');\" style='cursor:pointer;vertical-align:middle;' align='absmiddle' /> 
														</td>
													</tr>
													<tr height=24>
														<td colspan=2>
															<input type=text name='return_addr1'  id='return_addr1' value='".$return_addr1."' com='".$return_addr1."' user='".$addr1."' size=50 class='textbox'  style='width:75%' validation=false title='".($apply_status=='EA'?"교환":"반품")." 보낼 곳 주소' readonly>
														</td>
													</tr>
													<tr height=24>
														<td colspan=2>
															<input type=text name='return_addr2'  id='return_addr2'  value='".$return_addr2."' com='".$return_addr2."' user='".$addr2."' size=70 class='textbox'  validation=false title='".($apply_status=='EA'?"교환":"반품")." 보낼 곳 상세주소' style='width:75%'> (상세주소)
														</td>
													</tr>
													<tr height=24>
														<td align='center'>
															전달사항 :
														</td>
														<td colspan=2>
															<input type='text' class='textbox' name='return_delivery_msg' id='return_delivery_msg' value='".$return_delivery_msg."' style='width:75%'>
														</td>
													</tr>
													<tr height=24>
														<td align='center'>
															전화번호 :
														</td>
														<td colspan=2>
															<input type='text' class='textbox' name='return_rtel' id='return_rtel' value='".$return_rtel."' com='".$return_rtel."' user='".$rtel."'>
														</td>
													</tr>";
												}
												$Contents .= "
												</table>
											</td>
										</tr>";
										if($apply_status=="EA"){
											$Contents .= "
											<tr>
												<td class='input_box_title'> 교환재배송상품발송</td>
												<td class='input_box_item' colspan='3'>";
													if($act=="claim_confirm"){
														$Contents .= "
														<input type='hidden' name='exchange_delivery_type' value='".$exchange_delivery_type."'>
														".($exchange_delivery_type == "I" ?  "교환요청상품입고후발송":"")."
														".($exchange_delivery_type == "C" ?  "맞교환 발송": "")."
														".($exchange_delivery_type == "F" ?  "선발송": "");
													}else{
														$Contents .= "
														<input type='radio' name='exchange_delivery_type' id='exchange_delivery_type_i' value='I' ".($exchange_delivery_type=="I" || $exchange_delivery_type==""? "checked" : "")."><label for='exchange_delivery_type_i'>교환요청상품입고후발송</label>
														<input type='radio' name='exchange_delivery_type' id='exchange_delivery_type_c' value='C' ".($exchange_delivery_type=="C" ? "checked" : "")."><label for='exchange_delivery_type_c'>맞교환 발송</label>
														<input type='radio' name='exchange_delivery_type' id='exchange_delivery_type_f' value='F' ".($exchange_delivery_type=="F" ? "checked" : "")."><label for='exchange_delivery_type_f'>선발송</label> ";
													}
													$Contents .= "
												</td>
											</tr>";
										}
									$Contents .= "
									</table>";
							}


							if($act=="claim_confirm"){
								
								
								if($apply_status=="CA"){
									$claim_fault_type = fetch_order_status_div('DR','CA',"type",$reason_code);
								}else{
									$claim_fault_type = fetch_order_status_div('DC',$apply_status,"type",$reason_code);
								}

								for($i=0,$j=0; $i < count($product_info); $i++){
									if($apply_status=="EA"){//교환요청일때만
										foreach($ea_pdata[$product_info[$i][od_ix]] as $key => $val){
											$tmp_product_info[$j] = $val;
											$tmp_product_info[$j][claim_type]=substr($apply_status,0,1);//클래임종류
											$tmp_product_info[$j][claim_group]="99";//클래임그룹임시로 99로 동일!
											$tmp_product_info[$j][claim_fault_type]=$claim_fault_type;//클래임책임자
											$tmp_product_info[$j][claim_apply_yn]="N";//배송상품

											$j++;
										}
									}else{

										if($product_info[$i][pcnt]!=$apply_cnt[$product_info[$i][od_ix]]){
											if($claim_fault_type=="B"){
												$Coupondata["oid"]=$product_info[$i][oid];
												$Coupondata["od_ix"]=$product_info[$i][od_ix];
												$CouponReturn = orderUseCouponReturnCheck($Coupondata,$apply_cnt[$product_info[$i][od_ix]]);

												if($CouponReturn["coupon_total_dc_price"] > 0){//취소 및 반품은 교환과 반대!!
													$product_info[$i]["claim_discount_type"]="cupon";
													//$product_info[$i]["discount_desc"]=$CouponReturn["coupon_dc_info"];
												}
											}else{
												$product_info[$i]["claim_discount_type"]="cupon";
											}
										}
									}
									
									
									$product_info[$i][claim_type]=substr($apply_status,0,1);
									$product_info[$i][claim_group]="99";//클래임그룹임시로 99로 동일!
									$product_info[$i][claim_fault_type]=$claim_fault_type;//클래임책임자
									$product_info[$i][claim_apply_yn]="Y";//요청상품
									$product_info[$i][claim_apply_cnt]=$apply_cnt[$product_info[$i][od_ix]];//요청상품수량
									
									//아래 shop_order 에 환불방법 셀랙트 하기 위해서!
									if($product_info[$i][oid])	$oid=$product_info[$i][oid];
								}

								//print_r($tmp_product_info);
								//exit;

								for($i = 0; $i < count($tmp_product_info); $i++){
									array_push($product_info,$tmp_product_info[$i]);
								}

								//clameChangePriceCalculate 은 lib.function.php

								$resulte = clameChangePriceCalculate($product_info,$return_zip1);

								if($_SESSION["admininfo"]["charger_id"]=="forbiz"){
									//echo "<pre>";
									//print_r($resulte);
								}

								if($resulte["price"] > 0){// + 이면 환불 - 이면 추가결제
									$payment_type="refund"; //refund or add
								}else{
									$payment_type="add";
								}


								$Contents .= "
								<table border='0' width='100%' cellspacing='1' cellpadding='0' style='margin-top:25px;' style='table-layout:fixed;'>
									<tr>
										<td>
											<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b>환불비용내역</b></div>
										</td>
									</tr>
								</table>
								<table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;' >
									<col width='20%'>
									<col width='20%'>
									<col width='*'>
									<tr>
										<td class='input_box_title'>  주문".($apply_status=='EA'?"변동":"취소")."금액</td>
										<td class='input_box_item' style='text-align:right;padding-right:5px;'>
											<b>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($resulte["product"]["product_price"]+$resulte["delivery"]["delivery_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</b>
										</td>
										<td class='input_box_item'>
											<b>상품금액 ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($resulte["product"]["product_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]." + 배송비 ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($resulte["delivery"]["delivery_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</b>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>  할인".($apply_status=='EA'?"변동":"취소")."금액</td>
										<td class='input_box_item' style='text-align:right;padding-right:5px;'>
											".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($resulte["product"]["dc_price"]*-1,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
										</td>
										<td class='input_box_item'>
											".$resulte["product"]["dc_price_coment"]."
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>  쿠폰변동금액</td>
										<td class='input_box_item' style='text-align:right;padding-right:5px;'>
											".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($resulte["product"]["change_coupon_dcprice"]*-1,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
										</td>
										<td class='input_box_item'>
											".$resulte["product"]["change_coupon_coment"]."
										</td>
									</tr>
									<tr>
										<td class='input_box_title'> ".($apply_status=='CA'?"취소":"추가")."배송비</td>
										<td class='input_box_item' style='text-align:right;padding-right:5px;'>
											".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($resulte["delivery"]["claim_delivery_price"] + $resulte["delivery"]["change_delivery_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
										</td>
										<td class='input_box_item'>
											".$resulte["delivery"]["claim_coment"]."
										</td>
									</tr>
									<tr>
										<td class='input_box_title'> 환불예상금액</td>
										<td class='input_box_item' style='text-align:right;padding-right:5px;'>
											".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($resulte["price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
										</td>
										<td class='input_box_item'>
										</td>
									</tr>
								</table>

								<table border='0' width='100%' cellspacing='1' cellpadding='0' style='margin-top:25px;' style='table-layout:fixed;'>
									<tr>
										<td>
											<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b>실 <span class='payment_type_add' ".($payment_type == "refund" ? "style='display:none;'" : "").">결제</span><span class='payment_type_refund' ".($payment_type == "add" ? "style='display:none;'" : "").">환불</span>비용금액</b> <span class='small red'> - 는 추가 결제 금액입니다.</span></div>
										</td>
									</tr>
								</table>
								<table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;' >
									<col width='20%'>
									<col width='20%'>
									<col width='*'>";
						
									/*
									$db->query("SELECT refund_method,refund_bank_name,refund_bank from shop_order where oid = '".$oid."' ");
									$db->fetch();
									".getMethodStatus($db->dt[refund_method])."
									".($db->dt[refund_bank_name]!="" ? "환금 통장 정보 : ". $db->dt[refund_bank_name]." " : "")."
									".($db->dt[refund_bank]!="" ? "[ ".$db->dt[refund_bank]." ]" : "")."
									*/

									$sql="SELECT method,(case when pay_type='F' then -payment_price else payment_price end) as payment_price FROM shop_order_payment WHERE oid='".$oid."' and pay_status ='IC' group by method having payment_price > 0";
									$db->query($sql);
									$remain_method = $db->fetchall("object");
									
									$refund_str="";
									foreach($remain_method as $val){
										$refund_str .= ", ".getMethodStatus($val["method"]);
									}

									$Contents .= "
									<tr class='payment_type_refund' ".($payment_type == "add" ? "style='display:none;'" : "").">
										<td class='input_box_title'>  환불방법</td>
										<td class='input_box_item' colspan='2'>
											".substr($refund_str,1)."
										</td>
									</tr>";
									

									//delivery_pay_type 선불,착불일때 차후 추가작업하기!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
									if(($send_yn=="N" && $send_type=="1") || ($send_yn=="Y" && $delivery_pay_type=="1")){ //보내지 안았고직접발송 과 보내고 선불일시 추가 배송비 X

										/* 프론트 방식
										if($resulte["delivery"]["delivery_dc_price"] < 0){
											$resulte["price"] -= $resulte["delivery"]["delivery_dc_price"];
											$resulte["tax_price"] -= $resulte["delivery"]["delivery_dc_price"];
											$resulte["delivery"]["delivery_dc_price"]=0;
										}
										*/

										if($resulte["delivery"]["delivery_dc_price"] < 0){
											$Contents .= "
											<script type='text/javascript'>
											<!--
												$(document).ready(function(){
													$('#total_apply_delivery_price').val(0);
													change_total_apply_delivery_price($('#total_apply_delivery_price'));
												});
											//-->
											</script>";
										}
										
									}

									$Contents .= "
									<tr>
										<td class='input_box_title'>  <span class='payment_type_add' ".($payment_type == "refund" ? "style='display:none;'" : "").">결제</span><span class='payment_type_refund' ".($payment_type == "add" ? "style='display:none;'" : "").">환불</span>금액</td>
										<td class='input_box_item' style='text-align:right;padding-right:5px;'>
											<input type='hidden' name='oid' value='".$oid."'>
											<input type='hidden' name='payment_type' id='payment_type' value='".$payment_type."'>
											<input type='hidden' name='total_apply_price' id='total_apply_price' value='".$resulte["price"]."'>
											<input type='hidden' name='total_apply_product_price' value='".$resulte["product"]["product_dc_price"]."'>
											<input type='hidden' name='total_apply_tax_price' id='total_apply_tax_price' value='".$resulte["tax_price"]."'>
											<input type='hidden' name='total_apply_tax_free_price' value='".$resulte["tax_free_price"]."'>
											<b>".$currency_display[$admin_config["currency_unit"]]["front"]."<span id='total_apply_price_text'>".number_format($resulte["price"],$decimals_value)."</span> ".$currency_display[$admin_config["currency_unit"]]["back"]."</b>
										</td>
										<td class='input_box_item'>
											<b>
												상품금액 ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($resulte["product"]["product_dc_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]." 
												+ 배송비 ";
												
												//전체 취소일때
												if($apply_all=="Y"){
													foreach($resulte[delivery] as $key => $d_price_info){
														if(!in_array($key,array("org_delivery_price","delivery_price","delivery_dc_price","claim_delivery_price","change_delivery_price"))){
															$Contents .= "
															<input type='hidden' name='apply_all_delivery_price[".$key."]' value='".$d_price_info[org_delivery_price]."'>";
														}
													}

													$Contents .= "
													<input type='hidden' size='6' class='textbox numeric' id='total_apply_delivery_price' name='total_apply_delivery_price' value='".$resulte["delivery"]["delivery_dc_price"]."'> ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($resulte["delivery"]["delivery_dc_price"],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
												}else{
													$Contents .= "<input type='text' size='6' class='textbox numeric' id='total_apply_delivery_price' name='total_apply_delivery_price' value='".$resulte["delivery"]["delivery_dc_price"]."' total_apply_product_price='".$resulte["product"]["product_dc_price"]."' total_apply_tax_price='".$resulte["tax_price"]."' delivery_dc_price='".$resulte["delivery"]["delivery_dc_price"]."'> 원";
												}
												
												
												/*
												if($apply_status=="EA"){
													$Contents .= "<input type='text' size='6' class='textbox numeric' id='total_apply_delivery_price' name='total_apply_delivery_price' value='".$resulte["delivery"]["delivery_dc_price"]."' total_apply_product_price='".$resulte["product"]["product_dc_price"]."' total_apply_tax_price='".$resulte["tax_price"]."' delivery_dc_price='".$resulte["delivery"]["delivery_dc_price"]."'> 원";
												}else{
													$Contents .= "<input type='hidden' name='total_apply_delivery_price' value='".$resulte["delivery"]["delivery_dc_price"]."' > ".number_format($resulte["delivery"]["delivery_dc_price"])."원";
												}
												*/
											$Contents .= "
											</b>
										</td>
									</tr>
									<tr>
										<td class='input_box_item payment_type_add' ".($payment_type == "refund" ? "style='display:none;'" : "")." colspan='3'>
											* 추가결제내역
										</td>
										<td class='input_box_item payment_type_refund' ".($payment_type == "add" ? "style='display:none;'" : "")." colspan='3'>
											* 환불 방법은 카드/실시간계좌는 자동처리되며, 기타 결제방법으로 결제하신 고객님은 예치금 혹은 무통장으로 송금처리 됩니다.
										</td>
									</tr>
								</table>";
							}

							$Contents .= "
								</td>
							</tr>
						</table>
			</td>
  		</tr>
		<tr>
			<td colspan=2 align=center style='padding:10px 0px;'>
			<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle><!--btn_inquiry.gif-->
			</td>
		</tr>
  		</table>
</form>";

if($apply_status=="EA"){
	/* 회원 할인 정책으로 인한 세션 복원! */

	$_SESSION["layout_config"]["mall_data_root"] = $b_mall_data_root;
	$_SESSION["user"]["gp_ix"] = $b_gp_ix;
	$_SESSION["user"]["sale_rate"] = $b_sale_rate;
}

$P = new ManagePopLayOut();
$P->addScript = "<script language='javascript' src='../order/claim_apply.js?ver=1.0'></script>";
$P->Navigation = "주문관리 > ".strip_tags(getOrderStatus($apply_status))."하기";
$P->NaviTitle = strip_tags(getOrderStatus($apply_status))."하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();