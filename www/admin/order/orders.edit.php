<?
include("../class/layout.class");
include("../order/orders.lib.php");

$odb = new Database;
$db = new Database;

$Contents = "

<table width='100%'>
<tr>
    <td align='left'> ".GetTitleNavigation("주문정보수정", "매출관리 > 주문정보수정 ")."</td>
</tr>
</table>";


$sql = "SELECT p.*,rr.m_tid FROM shop_order_payment p left join receipt_result rr on (p.oid=rr.oid and p.receipt_yn='Y') 
WHERE p.oid = '".$oid."' and p.pay_type = 'G' and pay_status='IC' and method in ('".ORDER_METHOD_BANK."','".ORDER_METHOD_CARD."','".ORDER_METHOD_PHONE."','".ORDER_METHOD_VBANK."','".ORDER_METHOD_ICHE."','".ORDER_METHOD_MOBILE."','".ORDER_METHOD_NOPAY."','".ORDER_METHOD_ASCROW."') "; 
$odb->query($sql);
$payment = $odb->fetchall("object");

$sql = "SELECT o.*,
AES_DECRYPT(UNHEX(refund_bank),'".$db->ase_encrypt_key."') as refund_bank1,
AES_DECRYPT(UNHEX(refund_bank_name),'".$db->ase_encrypt_key."') as refund_bank_name1,
od.order_from, od.rfid,  date_format(od.ic_date,'%Y-%m-%d') as ic_date, od.mall_ix,
op.tid, op.settle_module
FROM ".TBL_SHOP_ORDER." o inner join ".TBL_SHOP_ORDER_DETAIL." as od on (o.oid = od.oid) left join shop_order_payment op on (o.oid = op.oid)
WHERE o.oid = '".$oid."' limit 0,1";
$odb->query($sql);
$odb->fetch();

$origin_currency_unit = $admin_config["currency_unit"];
$admin_config["currency_unit"] = check_currency_unit($odb->dt['mall_ix']);
if($admin_config["currency_unit"] == 'USD'){
	$decimals_value = 2;
}else{
    $decimals_value = 0;
}
$Contents = $Contents."
      <div id='TG_order_edit' style='position: relative;width:100%;'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td>
				<table border='0' width='100%' cellspacing='0' cellpadding='0' style='width:100%;'>
					<tr>
						<td>
							<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>간략주문정보</b></div>
						</td>
						<td align='right' style='display: none;'>
							<img src='../images/".$admininfo["language"]."/btn_print.gif' style='cursor:pointer' align='absmiddle' onclick=\"PopSWindow('../order/orders.read.php?oid=".$odb->dt[oid]."&mmode=print',960,600)\"/>";

							if(is_array($payment)){
								foreach($payment as $key => $val){

									$Contents .= " ". getPgReceipt($val);
									
									if($key==0)		$Contents .= " ". getReceipt($val);
								}
							}

								if($odb->dt[status]=='IR'){
							$Contents .= "
							<input type='button' onclick=\"PoPWindow('../order/receipt_view.php?oid=".$odb->dt[oid]."&view_type=transaction',680,800,'receipt_view');\" value='거래명세서' />";
								}else{
							$Contents .= "
							<input type='button' onclick=\"PoPWindow('../order/receipt_view.php?oid=".$odb->dt[oid]."&view_type=transaction',680,800,'receipt_view');\" value='거래명세서' />
							<input type='button' onclick=\"PoPWindow('../order/receipt_view.php?oid=".$odb->dt[oid]."',680,800,'receipt_view');\" value='일반영수증' />";
								}

						$Contents .= "
								
						</td>
					</tr>
				</table>
				<form name='order_info_edit' method='post' onSubmit='return CheckFormValue(this)'  action='orders.act.php' target='act'>
				<input type=hidden name=oid value='$oid'>
				<input type=hidden name=act value='orderinfo_update'>
				<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;'>
				<col width='15%' />
				<col width='35%' />
				<col width='15%' />
				<col width='35%' />
					<tr height=25 bgcolor='#ffffff' >
						<td class='input_box_title'>주문번호</td>
						<td class='input_box_item'>&nbsp;<b class='blue'>".$odb->dt[oid]."</b> <b class='org'>".getOrderFromName($odb->dt[order_from])."</b> ".($odb->dt[rfid] ? str_replace(array("전체 > ","검색엔진 > "),"",getRefererCategoryPath2($odb->dt[rfid], 4)):$odb->dt[rfid]."")."</td>
						<td class='input_box_title'>주문일자</td>
						<td class='input_box_item'>&nbsp;".$odb->dt[order_date]."</td>
					</tr>
					<tr bgcolor='#ffffff'>
						<td class='input_box_title' >주문자이름/그룹명</td>
						<td class='input_box_item'>
							&nbsp;".$odb->dt[bname]." / ".($odb->dt[buserid] ? "(".$odb->dt[buserid].")" : "").$odb->dt[mem_group]." ".($odb->dt[user_code] ? " <img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow('../member/member_cti.php?code=".$odb->dt[user_code]."&mem_ix=".$odb->dt[mem_ix]."&con_view=member',1280,800,'member_view')\" style='cursor:pointer;' alt='고객상담' title='고객상담'/>" :"")."
						</td>
						<td class='input_box_title'>주문자메일</td>
						<td class='input_box_item'>
							&nbsp;<input type='text' size=25 name='bmail' class='textbox' value='".$odb->dt[bmail]."' validation='false' title='주문자메일'>
							".(trim($odb->dt[bmail]) !=""? "&nbsp;<img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?mail='+$('input[name=bmail]').val()+'&name=".$odb->dt[bname]."&user_id=".$odb->dt[buserid]."',550,535,'sendmail')\" style='cursor:pointer;' title='이메일발송'>" : "")."
						</td>
					</tr>
					<tr bgcolor='#ffffff' >
						<!--td class='input_box_title'>주문자전화</td>
						<td class='input_box_item'>
							&nbsp;<input type='text' size=25 name='btel' class='textbox' value='".$odb->dt[btel]."' validation='false' title='주문자전화'>
						</td-->
						<td class='input_box_title'>주문자핸드폰</td>
						<td class='input_box_item' colspan='3'>
							&nbsp;<input type='text' size=25 name='bmobile' class='textbox' value='".$odb->dt[bmobile]."' validation='false' title='주문자핸드폰'>
							".(trim(str_replace('-','',$odb->dt[bmobile])) !=""? "&nbsp;<img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('/admin/member/member_sns_pop.php?pcs='+$('input[name=bmobile]').val()+'&name=".urlencode($odb->dt[bname])."&user_id=".$odb->dt[buserid]."&oid=".$odb->dt[oid]."',710,570,'sendsms')\" style='cursor:pointer;' title='SMS보내기'>" : "")."
						</td>
					</tr>";

					$sql="
						select
							odd.odd_ix,odd.order_type,odd.rname,odd.rtel,odd.rmobile,odd.rmail,odd.zip,odd.addr1,odd.addr2,
							odd.country,odd.city,odd.state,odd.msg,
							odd.r_first_name,odd.r_last_name,odd.r_first_kana,odd.r_last_kana
						from
							shop_order_detail_deliveryinfo odd
						where
							odd.oid='".$odb->dt[oid]."' and order_type='1'
						union
						select
							odd.odd_ix,odd.order_type,odd.rname,odd.rtel,odd.rmobile,odd.rmail,odd.zip,odd.addr1,odd.addr2,
							odd.country,odd.city,odd.state,odd.msg,
							odd.r_first_name,odd.r_last_name,odd.r_first_kana,odd.r_last_kana
						from
							shop_order_detail_deliveryinfo odd , shop_order_detail od 
						where
							odd.odd_ix=od.odd_ix and od.oid='".$odb->dt[oid]."' 
						group by odd.odd_ix";

					$db->query($sql);
					$deliveryinfo= $db->fetchall("object");

					$Contents .= "
					<tr bgcolor='#ffffff'>
						<td class='input_box_title'>수취인정보</td>
						<td class='input_box_item' colspan='3' style='padding:5px;'>
							<div class='tab' style='width:100%;height:38px;margin:0px;'>
								<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
								<tr>
									<td class='b_tab'>
										<table id='d_tab_01' class='on' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"showDeliveryInfos(1);\" style='padding-left:20px;padding-right:20px;'>
												정상
											</td>
											<th class='box_03'></th>
										</tr>
										</table>
										<table id='d_tab_04'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"showDeliveryInfos(4);\" style='padding-left:20px;padding-right:20px;'>
												수거
											</td>
											<th class='box_03'></th>
										</tr>
										</table>
										<table id='d_tab_02'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"showDeliveryInfos(2);\" style='padding-left:20px;padding-right:20px;'>
												교환
											</td>
											<th class='box_03'></th>
										</tr>
										</table>
										<table id='d_tab_03'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"showDeliveryInfos(3);\" style='padding-left:20px;padding-right:20px;'>
												반품(역배송)
											</td>
											<th class='box_03'></th>
										</tr>
										</table>
									</td>
									<td class='btn'>
									</td>
								</tr>
								</table>
							</div>

							<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;'>
								<col width='90px' />
								<col width='35%' />
								<col width='90px' />
								<col width='45%' />";
								
								foreach($deliveryinfo as $key => $val){
									$Contents .= "
									<tr class='deliveryinfo_contents".$val[order_type]."' ".($val[order_type] == 1 ? "" : "style='display:none;'").">
										<td class='m_td' ".($key!=0 ? "style='border-top:2px solid #c5c5c5;'":"").">배송<b class='small' style='color:red;font-weight:bold;'>(".$val[odd_ix].")</b> </td>
										<td class='input_box_item' ".($key!=0 ? "style='border-top:2px solid #c5c5c5;'":"").">
											".getOrderDetailDeliveryType($val[order_type])." 
											<input type='button' value='배송요구사항' onclick=\"if( $('#di_mp_tr_".$key."').is(':visible') ){ $('#di_mp_tr_".$key."').hide();}else{ $('#di_mp_tr_".$key."').show();} \" />
										</td>";

										$zipcode = explode("-",$val[zip]);
										$Contents .= "
										<td class='m_td' ".($key!=0 ? "style='border-top:2px solid #c5c5c5;'":"").">배송요구사항</td>
										<td class='input_box_item'  ".($key!=0 ? "style='border-top:2px solid #c5c5c5;'":"")." >
											<input type='text' name='deliveryinfo[".$val[odd_ix]."][msg]' class='textbox' value='".$val[msg]."' style='height:20px;width:80%;text-align:left;'>
										</td>
									</tr>
									<tr class='deliveryinfo_contents".$val[order_type]."' ".($val[order_type] == 1 ? "" : "style='display:none;'").">
										<td class='m_td'>수취인정보</td>
										<td class='input_box_item' style='padding:3px' rowrap>
											<span class='helpcloud' help_width='45' help_height='15' help_html='이름'>
												<input type='text' name='deliveryinfo[".$val[odd_ix]."][rname]' class='textbox' value='".$val[rname]."' style='height:20px;width:45px;text-align:center;margin-top:3px;'>
											</span>
											<span class='helpcloud' help_width='55' help_height='15' help_html='이메일'>
												<input type='text' name='deliveryinfo[".$val[odd_ix]."][rmail]' class='textbox' value='".$val[rmail]."' style='height:20px;width:135px;margin-top:3px;'>
											</span><br/>
											<span class='helpcloud' help_width='70' help_height='15' help_html='전화번호'><input type='text' name='deliveryinfo[".$val[odd_ix]."][rtel]' class='textbox' value='".$val[rtel]."' style='height:20px;width:90px;margin-top:3px;'></span>
											<span class='helpcloud' help_width='55' help_height='15' help_html='핸드폰'><input type='text' name='deliveryinfo[".$val[odd_ix]."][rmobile]' class='textbox' value='".$val[rmobile]."' style='height:20px;width:90px;margin-top:3px;'></span>

											&nbsp;<img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('/admin/member/member_sns_pop.php?pcs='+$(this).prev().find('input').val()+'&name=".urlencode($odb->dt[bname])."&user_id=".$odb->dt[buserid]."&oid=".$odb->dt[oid]."',710,570,'sendsms')\" style='cursor:pointer;' title='SMS보내기'>

										</td>
										<td class='m_td'>
											배송주소<br><img src='../images/".$admininfo["language"]."/btn_search_address.gif' align=absmiddle style='cursor:pointer;' onClick=\"zipcode('9','zip_td_".$key."')\">
										</td>
										<td class='input_box_item' style='padding:3px;' id='zip_td_".$key."'>
											<input type='text' name='deliveryinfo[".$val[odd_ix]."][zipcode1]' id='zipcode1' size='7' maxlength='7' class='textbox' readonly value='".$val[zip]."'>
											<input type='text' name='deliveryinfo[".$val[odd_ix]."][addr1]' id='addr1' class='textbox' value=\"".$val[addr1]."\" validation='false' title='배송주소1' style='width:180px;height:20px;' readonly>
											<br/>
											<input type='text' name='deliveryinfo[".$val[odd_ix]."][addr2]' id='addr2' class='textbox' value=\"".$val[addr2]."\" validation='false' title='배송주소2' style='width:275px;margin-top:3px;height:20px;'>
											<br/>
											<input type='text' name='deliveryinfo[".$val[odd_ix]."][country]' class='textbox' value='".$val[country]."' title='country' >
											<input type='text' name='deliveryinfo[".$val[odd_ix]."][city]' class='textbox' value='".$val[city]."' title='city' >
											<input type='text' name='deliveryinfo[".$val[odd_ix]."][state]' class='textbox' value='".$val[state]."' title='state' >
										</td>
									</tr>";

									$Contents .= "
									<tr id='di_mp_tr_".$key."' style='display:none;' class='deliveryinfo_contents".$val[order_type]."' ".($val[order_type] == 1 ? "" : "style='display:none;'").">
										<td class='m_td' >상품정보</td>
										<td class='input_box_item' style='padding:3px' colspan='3'>";

											$sql="select od_ix,brand_name,pname,set_name,sub_pname,option_text,msgbyproduct,due_date from shop_order_detail where oid='".$odb->dt[oid]."' and odd_ix = '".$val[odd_ix]."' ".($_SESSION["admininfo"]["admin_level"]!=9 ? " and company_id='".$_SESSION["admininfo"]["company_id"]."' " : "")." ";
											$db->query($sql);
											$deliveryinfo[$key]["product"] = $db->fetchall("object");
											if(is_array($deliveryinfo[$key]["product"])){
												foreach($deliveryinfo[$key]["product"] as $dpinfo){
													$Contents .= "
													<table border='0' cellspacing='0' cellpadding='0' style='float:left;min-width:350px;'>
														<tr height='30'>
															<td rowspan='2' width='20'>
																▷
															</td>
															<td>
																<span class='helpcloud' help_width='80' help_height='15' help_html='배송예정일'>
																	<img src='../images/".$admininfo["language"]."/calendar_icon.gif' align='absmiddle'>
																	<input type='text' name='deliveryinfo[".$val[odd_ix]."][product][".$dpinfo[od_ix]."][due_date]' class='textbox point_color due_datepicker' value='".$dpinfo[due_date]."' style='height:20px;width:70px;text-align:center;' id='due_datepicker_".$dpinfo[od_ix]."'>
																</span>
															</td>
															<td>
																&nbsp; [".$dpinfo[brand_name]."] ".$dpinfo[pname]." ".$dpinfo[set_name]." ".$dpinfo[sub_pname]." ".strip_tags($dpinfo[option_text])."
															</td>
														</tr>
														<tr height='30'>
															<td colspan='2'>
																<span class='helpcloud' help_width='130' help_height='15' help_html='상품별배송요구사항'>
																	<input type='text' name='deliveryinfo[".$val[odd_ix]."][product][".$dpinfo[od_ix]."][msg]' class='textbox' value='".$dpinfo[msgbyproduct]."' style='height:20px;width:90%;'>
																</span>
															</td>
														</tr>
													</table>
													";
												}
											}

										$Contents .= "

										</td>
									</tr>";
								}
							
							$Contents .= "
							</table>
						</td>
					</tr>
				</table>
				<table width=100%>
					<tr>
						<td align=right>
						<!--<a href='javascript:history.back();'><img src='../images/".$admininfo["language"]."/btn_back.gif' border='0' align=absmiddle></a>--> ";
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
							$Contents .= "<input type=image src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 style='cursor:pointer;' align=absmiddle> ";
						}
					$Contents .= "
						</td>
					</tr>
				</table>
				</form>

				<div style='height:20px;'></div>

				<table width='100%'>
					<tr>
						<td align='left'>
							<div class='tab' style='width:100%;height:38px;margin:0px;'>
								<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
								<tr>
									<td class='tab'>
										<table id='tab_01' class='on' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"$('td.tab').find('table').removeClass('on');$('#tab_01').addClass('on');$('.goods_area').show();\" style='padding-left:20px;padding-right:20px;'>
												전체
											</td>
											<th class='box_03'></th>
										</tr>
										</table>
										<table id='tab_02'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"$('td.tab').find('table').removeClass('on');$('#tab_02').addClass('on');$('.goods_area').hide();$('.bs_general,.general').show();\" style='padding-left:20px;padding-right:20px;'>
												정상
											</td>
											<th class='box_03'></th>
										</tr>
										</table>
										<table id='tab_03'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"$('td.tab').find('table').removeClass('on');$('#tab_03').addClass('on');$('.goods_area').hide();$('.claim,.incom_after_cancel,.incom_befor_cancel').show();\" style='padding-left:20px;padding-right:20px;'>
												취소/교환/반품
											</td>
											<th class='box_03'></th>
										</tr>
										</table>
									</td>
									<td class='btn'>
									</td>
								</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<a id='order_memo'></a>";

				$Contents .= "
				".OrderGoodsList($odb, "bs_general")."
				".OrderGoodsList($odb, "general")."
				".OrderGoodsList($odb, "claim")."
				".OrderGoodsList($odb, "incom_after_cancel")."
				".OrderGoodsList($odb, "incom_befor_cancel")."
				<div style='height:20px;'></div>

				<table border='0' cellspacing='0' cellpadding='0' width='100%'  bordercolor='#black'>
					<tr>
						<td>
							<form name='order_memo_frm' method=post action='orders_memo.act.php' onsubmit='return orders_memo_submit()' target='iframe_act'>
								<input type='hidden' name='act' value='memo_insert'>
								<input type='hidden' name='oid' value='$oid'>
								<input type='hidden' name='ucode' value='".$odb->dt[user_code]."'>
								<input type='hidden' name='om_ix' value=''>
								<input type='hidden' name='order_date' value='".$odb->dt[order_date]."'>
								<input type='hidden' name='order_from' value='".$odb->dt[order_from]."'>
								<table width=100% cellspacing='0'>
								<tr>
									<td>
										<img src='../images/dot_org.gif'  align='absmiddle'> <b class='middle_title'>주문 상담내역</b>
									</td>";
									if($_SESSION['admininfo']['admin_level'] == '9'){
										$Contents .= "
									<td align='right'>
										<a href=\"javascript:PopSWindow('/admin/bbsmanage/board_category.php?mmode=pop&bm_ix=1',950,500,'bbsmanage')\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_setup.gif' border='0'></a>
									</td>";
									}
									$Contents .= "
								</tr>
								<tr>
									<td style='padding-top:10px;padding-left:10px;' bgcolor='#F8F9FA' colspan='2'>
										<table style='font-weight:bold;'>
											<tr>
												<td>
													<input type='hidden' name='b_memo_state' id='b_memo_state' value=''/>
													처리상태 :
													<select name='memo_state'>";
														//$memo_state_array 는 constants.php 에 있음
														foreach($memo_state_array as $key => $val){
															$Contents .= "<option value='".$key."'>".$val."</option>";
														}
													$Contents .= "
													</select>&nbsp;&nbsp;
												</td>
												<td>
													담당자 지정 :
												</td>
												<td>
													".MDSelect($_SESSION["admininfo"]["charger_ix"])."
												</td>
												<td>
													&nbsp;&nbsp;콜 처리유형 :
													<select name='call_type' validation='true' title='콜 처리유형'>
														<option value=''>선택</option>";
														//$memo_call_type_array 는 constants.php 에 있음
														foreach($memo_call_type_array as $key => $val){
															$Contents .= "<option value='".$key."'>".$val."</option>";
														}
													$Contents .= "
													</select>&nbsp;&nbsp;
												</td>
												<td>";
													//주문분류는 게시판의 분류의 키bm_ix = '1'을 이용하여 구연함!
													$sql = "SELECT div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
													FROM ".TBL_BBS_MANAGE_DIV."
													where bm_ix = '1' and div_depth = 1
													group by div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
													order by view_order asc, div_depth asc,div_ix asc ";
													$db->query($sql);
													$bbs_divs = $db->fetchall();

													$Contents .= "
													&nbsp;&nbsp; 분류 :
													<select name='bbs_div' onChange=\"bbsloadCategory(this,'sub_bbs_div',1)\" align='absmiddle' validation='true' title='상담 분류' >
															<option value=''>분류선택</option>";
													for($d=0;$d<count($bbs_divs);$d++){
														$Contents .= "<option value=".$bbs_divs[$d][div_ix].">".$bbs_divs[$d][div_name]."</option>";
													}
													$Contents .= "
													</select>
													<span id='sub_cate_table' style='display:none;'>
														<select name='sub_bbs_div'>
															<option value=''>서브분류선택</option>
														</select>
													</span>&nbsp;&nbsp;
												</td>
												
											</tr>
											<tr>
												<td colspan='5'>
													<input type=radio name='user_mood_state' id='user_mood_state_5' value='5'><label class='helpcloud' help_width='45' help_height='15' help_html='기쁨' for='user_mood_state_5'> <img src='../images/icon/mood_state_5.png' align='absmiddle' /></label>
													<input type=radio name='user_mood_state' id='user_mood_state_4' value='4'><label class='helpcloud' help_width='45' help_height='15' help_html='양호' for='user_mood_state_4'> <img src='../images/icon/mood_state_4.png' align='absmiddle' /></label>
													<input type=radio name='user_mood_state' id='user_mood_state_3' value='3' checked><label class='helpcloud' help_width='45' help_height='15' help_html='보통' for='user_mood_state_3'> <img src='../images/icon/mood_state_3.png' align='absmiddle' /></label>
													<input type=radio name='user_mood_state' id='user_mood_state_2' value='2'><label class='helpcloud' help_width='45' help_height='15' help_html='불만' for='user_mood_state_2'> <img src='../images/icon/mood_state_2.png' align='absmiddle' /></label>
													<input type=radio name='user_mood_state' id='user_mood_state_1' value='1'><label class='helpcloud' help_width='70' help_height='15' help_html='매우불만' for='user_mood_state_1'> <img src='../images/icon/mood_state_1.png' align='absmiddle' /></label> &nbsp;&nbsp;

													<input type=checkbox name='urgency_yn' id='urgency_yn' value='Y'><label for='urgency_yn'> 긴급처리상담</label> &nbsp;&nbsp;
													<input type=checkbox name='call_action_yn' id='call_action_yn' value='Y' onclick='click_call_action_yn()'><label for='call_action_yn'> 전화응대필요</label>
												</td>
											</tr>
										</table>
										<table  id='call_action_area' style='display:none;font-weight:bold;'>
											<tr>
												<td>
													전화응대날짜 : <img src='../images/".$admininfo["language"]."/calendar_icon.gif' align='absmiddle'> <input type='text' name='call_action_date' class='textbox point_color' value='".$call_action_date."' style='height:20px;width:70px;text-align:center;' id='memo_datepicker'>&nbsp;&nbsp;
													전화응대시간 : <input type='text' name='call_action_time' class='textbox' value='".$call_action_time."' style='height:20px;width:100px;' > (TEXT로 입력해주세요)&nbsp;&nbsp;
													전화응대시간 :
													<select name='call_action_state'>
														<option value='0'>대기중</option>
														<option value='1'>완료</option>
													</select>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td bgcolor='#F8F9FA' style='padding:10px' colspan='2'><textarea style='height:50px;width:97%;' wrap='off'  basci_message=true name='memo' ></textarea></td>
								</tr>
								<tr><td bgcolor='#F8F9FA' align=right style='padding:10px;' colspan='2'>";
								if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
									$Contents .= "
									<input type=image src='../images/".$admininfo["language"]."/btn_counsel_save.gif' id='save_btn' border=0 align=absmiddle></td></tr>";
								}else{
									$Contents .= "
									<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_counsel_save.gif' id='save_btn' border=0 align=absmiddle></a></td></tr>";
								}
								$Contents .= "
								<tr>
									<td bgcolor='D0D0D0' height='1' colspan='2'></td>
								</tr>
								</table>
							</form>
							<table width=100%>
							<tr>
								<td align=right style='padding-top:10px;' id='design_history_area'>
								".PrintOrderMemo($oid)."
								</td>
							</tr>
							</table>
						</td>
					</tr>
				</table>
				</div>

				<table border='0' cellspacing='1' cellpadding='0' width='100%' style='margin-top:30px;'>
                <tr>
					<td bgcolor='#F8F9FA' style='padding:10px;'>
					<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>주문정보</b></div>
					<table border='0' width='100%' cellspacing='1' cellpadding='0'>
						<tr height=30 bgcolor=''>
							<td style='padding:0;text-align:left;' > <b>* 주문자/수취인정보</b></td>
						</tr>
						<tr>
							<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;'>
								<col width='15%' />
								<col width='35%' />
								<col width='15%' />
								<col width='35%' />
									<tr height=25 bgcolor='#ffffff' >
										<td class='input_box_title'>주문번호</td>
										<td class='input_box_item'>&nbsp;<b class='blue'>".$odb->dt[oid]."</b> <b class='org'>".getOrderFromName($odb->dt[order_from])."</b></td>
										<td class='input_box_title'>주문일자</td>
										<td class='input_box_item'>&nbsp;".$odb->dt[order_date]."</td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class='input_box_title' >주문자명/회원등급</td>
										<td class='input_box_item'>
											&nbsp;".$odb->dt[b_first_name]." ".$odb->dt[b_last_name]." / ".($odb->dt[buserid] ? "(".$odb->dt[buserid].")" : "").$odb->dt[mem_group]." ".($odb->dt[user_code] ? " <img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow('../member/member_cti.php?code=".$odb->dt[user_code]."&mem_ix=".$odb->dt[mem_ix]."&con_view=member',1280,800,'member_view')\" style='cursor:pointer;' alt='고객상담' title='고객상담'/>" :"")."
										</td>
										<td class='input_box_title'>주문자메일</td>
										<td class='input_box_item'>
											&nbsp;".$odb->dt[bmail]." ".(trim($odb->dt[bmail]) !=""? "&nbsp;<img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?mail=".$odb->dt[bmail]."&name=".$odb->dt[bname]."&user_id=".$odb->dt[buserid]."',550,535,'sendmail')\" style='cursor:pointer;' title='이메일발송'>" : "")."
										</td>
									</tr>
									<tr bgcolor='#ffffff' >
										<!--td class='input_box_title'>주문자전화</td>
										<td class='input_box_item'>
											&nbsp;".$odb->dt[btel]."
										</td-->
										<td class='input_box_title'>주문자핸드폰</td>
										<td class='input_box_item'>
											&nbsp;".$odb->dt[bmobile]."
											".(trim(str_replace('-','',$odb->dt[bmobile])) !=""? "&nbsp;<img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('/admin/member/member_sns_pop.php?pcs=".$odb->dt[bmobile]."&name=".urlencode($odb->dt[bname])."&user_id=".$odb->dt[buserid]."&oid=".$odb->dt[oid]."',710,570,'sendsms')\" style='cursor:pointer;' title='SMS보내기'>" : "")."
										</td>
										<td class='input_box_title'>통관고유번호</td>
										<td class='input_box_item'>
											&nbsp;".$odb->dt[customs_clearance_number]."
										</td>
									</tr>
									<!--tr bgcolor='#ffffff' >
										<td class='input_box_title'>통관고유번호</td>
										<td class='input_box_item' colspan='3'>
											&nbsp;".$odb->dt[customs_clearance_number]."
										</td>
									</tr-->
									<tr bgcolor='#ffffff'>
										<td class='input_box_title'>수취인정보</td>
										<td class='input_box_item' colspan='3' style='padding:5px;'>
											<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;'>
												<col width='8%'/>
												<col width='12%'/>
												<col width='8%'/>
												<col width='12%'/>
												<col width='8%'/>
												<col width='12%'/>
												<col width='8%'/>
												<col width='12%'/>";

												foreach($deliveryinfo as $key => $val){
													$Contents .= "
													<tr>
														<td class='m_td' ".($key!=0 ? "style='border-top:2px solid #c5c5c5;'":"")." >배송타입</td>
														<td class='input_box_item' ".($key!=0 ? "style='border-top:2px solid #c5c5c5;'":"").">
															".getOrderDetailDeliveryType($val[order_type])." <input type='button' value='배송요구사항' onclick=\"if( $('#di_p_tr_".$key."').is(':visible') ){ $('#di_p_tr_".$key."').hide();}else{ $('#di_p_tr_".$key."').show();} \" />
														</td>";
														$Contents .= "
														<td class='m_td' ".($key!=0 ? "style='border-top:2px solid #c5c5c5;'":"")." >배송요구사항</td>
														<td class='input_box_item'  ".($key!=0 ? "style='border-top:2px solid #c5c5c5;'":"")." >
															&nbsp;".$val[msg]."
														</td>
														<td class='m_td' ".($key!=0 ? "style='border-top:2px solid #c5c5c5;'":"").">
															배송주소
														</td>
														<td class='input_box_item' colspan='3' ".($key!=0 ? "style='border-top:2px solid #c5c5c5;'":"").">
															".($val[country] !='' ? "country : ". $val[country]."<br>" : "")."	
															".($val[city] !='' ? "city : ". $val[city]."<br>" : "")."	
															".($val[state] !='' ? "state : ". $val[state]."<br>" : "")."	
															[".$val[zip]."] ".$val[addr1]." ".$val[addr2]."
														</td>
													</tr>
													<tr>
														<td class='m_td'>이름</td>
														<td class='input_box_item' style='padding:3px' rowrap>
															".$val[rname]."
														</td>
														<td class='m_td'>메일</td>
														<td class='input_box_item' style='padding:3px' rowrap>
															".$val[rmail]." ".(trim($val[rmail]) !=""? "&nbsp;<img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?mail=".$val[rmail]."&name=".$odb->dt[rname]."',550,535,'sendmail')\" style='cursor:pointer;' title='이메일발송'>" : "")."
														</td>
														<td class='m_td'>전화번호</td>
														<td class='input_box_item' style='padding:3px;'>
															".$val[rtel]."
														</td>
														<td class='m_td'>핸드폰</td>
														<td class='input_box_item' style='padding:3px;'>
															".$val[rmobile]." ".(trim(str_replace('-','',$val[rmobile])) !=""? "&nbsp;<img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('/admin/member/member_sns_pop.php?pcs=".$val[rmobile]."&name=".urlencode($val[rname])."&oid=".$odb->dt[oid]."',710,570,'sendsms')\" style='cursor:pointer;' title='SMS보내기'>" : "")."
														</td>
													</tr>
													<tr id='di_p_tr_".$key."' style='display:none;' >
														<td class='m_td' >상품정보</td>
														<td class='input_box_item' style='padding:3px 3px 3px 8px;' colspan='7'>";
															if(is_array($deliveryinfo[$key]["product"])){
																foreach($deliveryinfo[$key]["product"] as $dpinfo){
																	$Contents .= "
																	<table border='0' cellspacing='0' cellpadding='0' style='float:left;min-width:350px;'>
																	<tr height='35'>
																		<td width='20'>
																			▷
																		</td>
																		<td nowrap>
																			<span class='helpcloud' help_width='80' help_height='15' help_html='배송예정일'>
																				<img src='../images/".$admininfo["language"]."/calendar_icon.gif' align='absmiddle'>
																				<input type='text' name='deliveryinfo[".$val[odd_ix]."][product][".$dpinfo[od_ix]."][due_date]' class='textbox' value='".$dpinfo[due_date]."' style='height:20px;width:70px;text-align:center;' disabled>
																			</span>
																		</td>
																		<td>
																			&nbsp; [".$dpinfo[brand_name]."] ".$dpinfo[pname]." ".$dpinfo[set_name]." ".$dpinfo[sub_pname]." ".strip_tags($dpinfo[option_text])."
																			".($dpinfo[msgbyproduct]!="" ? "<br/>&nbsp; <span class='small'>상품별배송요구사항 : ".$dpinfo[msgbyproduct]."</span>" : "")."
																		</td>
																	</tr>
																	</table>";
																}
															}

														$Contents .= "
														</td>
													</tr>";
												}
											$Contents .= "
											</table>
										</td>
									</tr>
								</table>

								<table border='0' width='100%' cellspacing='1' cellpadding='0'>
								<tr height=30 bgcolor=''>
									<td style='padding:0;text-align:left;' > <b>* 주문결제정보</b></td>
								</tr>
								<tr>
									<td >
										<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;'>
										<col width='15%' />
										<col width='35%' />
										<col width='15%' />
										<col width='35%' />";

						if($admininfo[admin_level] == 9){

								$method_info = getOrderMethodInfo($odb->dt);
								$method_=$method_info["method"];
								$method_str=$method_info["method_str"];
								$method_width=$method_info["method_width"];
								$method_height=$method_info["method_height"];
								$method_pay_info=$method_info["method_pay_info"];
								$total_real_pay_price=$method_info["total_real_pay_price"];

								$method = "<label class='helpcloud' help_width='".$method_width."' help_height='".$method_height."' help_html='".$method_str."'>".getMethodStatus($method_,"img")."</label> <span style=''>".$method_pay_info."</span>";

								$db->query("select
										(product_price+delivery_price-saveprice) as real_payment_price,
										expect_order_price
									from (
										select
											sum(case when payment_status='G' then expect_product_price + expect_delivery_price else '0' end) as expect_order_price,
											sum(case when payment_status='F' then -product_price else product_price end) as product_price,
											sum(case when payment_status='F' then -delivery_price else delivery_price end) as delivery_price,
											sum(case when payment_status='F' then -reserve else reserve end) as reserve,
											sum(case when payment_status='F' then -point else point end) as point,
											sum(case when payment_status='F' then -saveprice else saveprice end) as saveprice
										 from shop_order_price where oid='".$oid."'
									 ) p ");
								$order_price=$db->fetch();
								
								$exchange_rate_payment_price = getOrderExchangeRatePaymentPrice($odb->dt);

								$Contents .= "
										<tr>
											<td class='input_box_title'>결제방법</td>
											<td class='input_box_item' style='line-height:140%;'>&nbsp;".getPaymentAgentType($odb->dt[payment_agent_type],'img')." / ".$method."</td>
											<td class='input_box_title'>입금확인일</td>
											<td class='input_box_item'>&nbsp;".$odb->dt[ic_date]."</td>
										</tr>

										<tr>
											<td class='input_box_title'><b>주문 금액</b></td>
											<td class='input_box_item'>&nbsp; ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($order_price[expect_order_price],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td class='input_box_title'><b>총 결제금액</b></td>
											<td class='input_box_item'>&nbsp; ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($total_real_pay_price,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."  ".($exchange_rate_payment_price > 0 ? " <b>[ ".number_format($exchange_rate_payment_price,$decimals_value)." ]</b> " : "")."</td>
										</tr>";


								$sql="select dc_type,sum(dc_price) as dc_price from shop_order_detail_discount where oid='".$oid."' group by dc_type";
								$db->query($sql);


								$product_discount="";
								$delivery_discount="";
								//dc_type 할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)
								if($db->total){
									$dc_info=$db->fetchall();
									foreach($dc_info as $dc){

										if($dc[dc_type]=="DCP" || $dc[dc_type]=="DE"){
											$delivery_discount .=" > ".$_DISCOUNT_TYPE[$dc[dc_type]]." : ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($dc[dc_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."";
										}else{
											$product_discount .=" > ".$_DISCOUNT_TYPE[$dc[dc_type]]." : ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($dc[dc_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."";
										}
									}
								}
								
								//[".number_format($odb->dt[org_product_price])."원 -> ".number_format($odb->dt[product_price])."원]
								//[".number_format($odb->dt[org_delivery_price])."원 -> ".number_format($odb->dt[delivery_price])."원]

								if($product_discount!="")	$discount_product_str = "<b>상품  :</b> ".substr($product_discount,3);
								if($delivery_discount!="")	$discount_delivery_str = "<b>배송비  :</b> ".substr($delivery_discount,3);
								if($discount_product_str!="" && $discount_delivery_str!="")		$discount_delivery_str = "<br/>".$discount_delivery_str;

								if($discount_product_str!="" || $discount_delivery_str!=""){
									$Contents .= "
										<tr>
											<td class='input_box_title'><b>할인상세내역</b></td>
											<td class='input_box_item'style='line-height:140%;padding:3px;' colspan='3'>
												".$discount_product_str."
												".$discount_delivery_str."
											</td>
										</tr>";

								}

								$Contents .= "
										</table>
										<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;margin-top:10px;'>
										<col width='*' />
										<col width='12%' />
										<col width='12%' />
										<col width='12%' />
										<col width='12%' />
										<col width='12%' />
										<col width='12%' />
										<col width='12%' />
											<tr>
												<td class='input_box_title' rowspan='2'><b>구분</b></td>
												<td class='input_box_title' colspan='3'><b>주문상세내역</b></td>
												<td class='input_box_title' colspan='4'><b>결제상세내역</b></td>
											</tr>
											<tr>
												<td class='input_box_title'><b>합계</b></td>
												<td class='input_box_title'><b>상품금액</b></td>
												<td class='input_box_title'><b>배송금액</b></td>
												<td class='input_box_title'><b>합계</b></td>
												<td class='input_box_title'><b>PG+무통장</b></td>
												<td class='input_box_title'><b>예치금</b></td>
												<td class='input_box_title'><b>적립금</b></td>
											</tr>";

										$sql="select 
												payment_status, 
												(expect_product_price + expect_delivery_price) as expect_total_price,
												expect_product_price,
												expect_delivery_price,
												(pg_payment_price + saveprice_payment_price + reserve_payment_price) as total_payment_price,
												pg_payment_price,
												saveprice_payment_price,
												reserve_payment_price,
												cartcoupon_payment_price
										from (
											select
												payment_status,
												ifnull(expect_product_price,0) as expect_product_price,
												ifnull(expect_delivery_price,0) as expect_delivery_price,
												SUM(case when pay_status='IC' and method not in ('".ORDER_METHOD_SAVEPRICE."','".ORDER_METHOD_RESERVE."','".ORDER_METHOD_DELIVERY_COUPON."') then payment_price else 0 end) as pg_payment_price,
												SUM(case when pay_status='IC' and method in ('".ORDER_METHOD_SAVEPRICE."') then payment_price else 0 end) as saveprice_payment_price,
												SUM(case when pay_status='IC' and method in ('".ORDER_METHOD_RESERVE."') then payment_price else 0 end) as reserve_payment_price,
												SUM(case when pay_status='IC' and method in ('".ORDER_METHOD_CART_COUPON."') then payment_price else 0 end) as cartcoupon_payment_price
											from 
												shop_order_price p left join shop_order_payment p2 on (p.oid=p2.oid and p.payment_status=p2.pay_type)
											where 
												p.oid='".$oid."' 
											group by pay_type
											order by op_ix 
										) pay
										
										";

										$db->query($sql);

										if($db->total){
											for($i=0;$i<$db->total;$i++){
												$db->fetch($i);

												if($db->dt[payment_status]=='G'){
													$p_status=1;
													$p_title="주문결제";
												}elseif($db->dt[payment_status]=='A'){
													$p_status=1;
													$p_title="추가";
												}elseif($db->dt[payment_status]=='F'){
													$p_status=-1;
													$p_title="환불";
												}else{
													$p_status=0;
													$p_title="-";
												}

								$Contents .= "<tr>
												<td class='input_box_title'><b>".$p_title." </b></td>
												<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[expect_total_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
												<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[expect_product_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
												<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[expect_delivery_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
												<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[total_payment_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
												<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[pg_payment_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
												<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[saveprice_payment_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
												<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[reserve_payment_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
												
											</tr>";
											}
										}else{
								$Contents .= "<tr>
												<td class='input_box_item' colspan='7' style='text-align:center;'>사용 내역이 없습니다.</td>
											</tr>";
										}
								$Contents .= "
											</table>
										</td>
									</tr>
									</table><br>";

						}

					$Contents .= "
								<table border='0' width='100%' cellspacing='1' cellpadding='0'>
									<tr height=30 bgcolor=''>
										<td style='padding:0;text-align:left;' > <b>* 상태변경 내역</b></td>
									</tr>
								</table>
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;'>
								<tr>
									<td class='input_box_title'>주문상태 변경내역</td>
									<td class='input_box_item' colspan='3' style='padding:10px 0 10px 10px'>
									<div style='width:100%;height:200px;overflow:auto;'>";

									//2012-07-30 홍진영 orders.read.php와 같도록
									if($admininfo[admin_level] == 9){
										$db->query("select os.regdate, os.status, os.status_message, os.pid, c.com_name,os.invoice_no,os.quick,admin_message from ".TBL_SHOP_ORDER_STATUS." os left join ".TBL_COMMON_COMPANY_DETAIL." c on os.company_id = c.company_id where os.oid ='$oid'	 order by os_ix asc");
									}else if($admininfo[admin_level] == 8){

										$db->query("select os.regdate, os.status, os.status_message, os.pid, c.com_name,os.invoice_no,os.quick,admin_message from ".TBL_SHOP_ORDER_STATUS." os left join ".TBL_COMMON_COMPANY_DETAIL." c on os.company_id = c.company_id left join ".TBL_SHOP_PRODUCT." p on os.pid = p.id  where os.oid ='$oid' and p.admin ='".$admininfo[company_id]."'	order by os_ix asc");
									}

									for($j = 0; $j < $db->total; $j++)
									{
										$db->fetch($j);
										$Contents .= "<span class=small>".$db->dt[regdate]." ".getOrderStatus($db->dt[status])."  ".($db->dt[pid] ? "(상품코드:".$db->dt[pid].")":"")." <span style='color:blue'>".($db->dt[invoice_no].":" ? codeName($db->dt[quick]).":":"")." ".($db->dt[invoice_no] ? $db->dt[invoice_no]:"")."</span> ".($db->dt[com_name] ? "- 수정업체:".$db->dt[com_name]."":"")."".($db->dt[status_message] ? " - <b>".$db->dt[status_message]."</b>" : "")."".($db->dt[admin_message]!="" ? " - " . $db->dt[admin_message] : "")."</span><br>";
									}


				$Contents .= "		</div>
									</td>
								</tr>
								<tr height=60 bgcolor='white'>
									<td class='input_box_title'>전달사항</td>
									<td class='input_box_item' colspan=3 style='padding-left:5px; '>".nl2br($odb->dt[msg])."</td>
								</tr>
								</table>
							</td>
						</tr>
						";

					if($admininfo[mall_use_multishop] && $admininfo[admin_level] ==  9){
					$Contents .= "
						<tr height=30>
							<td class='small' style='padding:3px;line-height:150%'>
							<table width=100%>
								<tr>
									<td>
										<!-- - 입점업체 상품 모두가 상태변경이 되었을때 상태변경을 하시면 됩니다.-->
										
									</td>
									<td align=right>
									<!--<a href='javascript:history.back();'><img src='../images/".$admininfo["language"]."/btn_back.gif' border='0' align=absmiddle></a>--> ";
									$admininfo["admin_level"];
									/*
									if($admininfo[admin_level] == 9 && $admininfo["language"] == 'korea' ){
										if($admininfo[sattle_module] == "inicis"){
											$Contents .= " <a href='https://iniweb.inicis.com/' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_inisis.gif' align=absmiddle border=0  ></a>";
										}else if($admininfo[sattle_module] == "allthegate"){
											$Contents .= " <a href='https://www.allthegate.com/login/r_login.jsp' target='_blank'><img src='../images//btn_pg_admin.gif' align=absmiddle border=0  ></a>";
										}else if($admininfo[sattle_module] == "lgdacom"){
											$Contents .= " <a href='http://pgweb.lgdacom.net' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_lgdacom.gif' align=absmiddle border=0  ></a>";
										}else if($admininfo[sattle_module] == "kcp"){
											$Contents .= " <a href='https://admin.kcp.co.kr' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_kcp.gif' align=absmiddle border=0  ></a>";
										}else if($admininfo[sattle_module] == "nicepay"){
											$Contents .= " <a href='http://home.nicepay.co.kr/homepg/main.jsp' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_nicepay.gif' align=absmiddle border=0  ></a>";
										}
									}*/
								$Contents .= "
									</td>
								</tr>
							</table>
							</td>
						</tr>";
					}
					$Contents .= "

						</table>
						</td>
					</tr>
				</table>
				</td>
				</tr>
			</table>
		</div>
	</td>
</tr>
";


$Contents .= "
</table>
</div>
";

$admin_config["currency_unit"] = $origin_currency_unit;

//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


//$help_text = HelpBox("주문내역수정", $help_text);
$Contents .= $help_text;


$Contents = $Contents."
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$Script = "
<script language='javascript' >

function click_call_action_yn(){
	if($('#call_action_yn').is(':checked')){
		$('#call_action_area').show();
	}else{
		$('#call_action_area').hide();
	}
}

function memoModify(oid, om_ix,div_depth,parent_div_ix,memo_div,memo_state,charger, charger_ix,urgency_yn,call_type,call_action_yn,call_action_date,call_action_time,call_action_state,user_mood_state){

	var frm = document.order_memo_frm;

	if(div_depth==2){
		$('select[name=bbs_div]').val(parent_div_ix);
		window.frames['iframe_act'].location.href='/bbs/category.load.php?form=order_memo_frm&trigger=' + parent_div_ix + '&depth=1&target=sub_bbs_div&value='+ memo_div;
	}else{
		$('select[name=bbs_div]').val(memo_div);
	}
	
	$('select[name=call_type]').val(call_type);

 	frm.act.value = 'memo_update';
 	frm.om_ix.value = om_ix;

 	$('textarea[name=memo]').text($('#memo_'+om_ix).html().replace(/<br\s?\/?>/g,'').replace(/<BR\s?\/?>/g,'\\r'));

	$('select[name=memo_state]').val(memo_state);
	$('#b_memo_state').val(memo_state);

	frm.md_name.value = charger;
	frm.md_code.value = charger_ix;

	if(urgency_yn=='Y'){
		frm.urgency_yn.checked=true;
	}else{
		frm.urgency_yn.checked=false;
	}

	$('[name=user_mood_state][value='+user_mood_state+']').attr('checked',true);

	if(call_action_yn=='Y'){
		frm.call_action_yn.checked=true;
		frm.call_action_date.value = call_action_date;
		frm.call_action_time.value = call_action_time;
		$('select[name=call_action_time]').val(call_action_time);
	}else{
		frm.call_action_yn.checked=false;
		frm.call_action_date.value = '';
		frm.call_action_time.value = '';
		$('select[name=call_action_time]').val(0);
	}

	click_call_action_yn()
}

function bbsloadCategory(sel,target, depth) {

	var trigger = sel.options[sel.selectedIndex].value;	// 첫번째 selectbox의 선택된 텍스트
	var form = sel.form.name;
	window.frames['iframe_act'].location.href='/bbs/category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}

function memoDelete(oid, om_ix){
	if(confirm(language_data['orders.edit.php']['A'][language])){//해당 상담내역을 정말로 삭제 하시겠습니까?
		window.frames['iframe_act'].location.href='orders_memo.act.php?act=memo_delete&oid='+oid+'&om_ix='+om_ix;
	}
}


//콤마표현 없는 정수만입력
function onlyEditableNumber(obj){
 var str = obj.value;
 str = new String(str);
 var Re = /[^0-9]/g;
 str = str.replace(Re,'');
 obj.value = str;
}


$(document).ready(function (){
	$('#memo_datepicker').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
		}
	});

	$('.due_datepicker').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
		}
	});

});

function showDeliveryInfos(type){
	$('.b_tab').find('table').removeClass('on');
	$('#d_tab_0'+type).addClass('on');
	$('.deliveryinfo_contents1').hide();
	$('.deliveryinfo_contents2').hide();
	$('.deliveryinfo_contents3').hide();
	$('.deliveryinfo_contents4').hide();
	$('.deliveryinfo_contents'+type).show();
}
</script>
<style type='text/css'>
a img {
	border: none;
}

.messagebox_contents {
	position: absolute;
	padding: .5em;
	background: #e3e3e3;
	border: 1px solid;
}
</style>
";

if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script."<script language='javascript' src='../order/orders.js'></script>";
	//$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
	$P->OnloadFunction = "";	//init();
	$P->strLeftMenu = member_menu();
	$P->Navigation = "주문관리 > 주문정보수정";
	$P->title = "주문정보수정";
    $P->NaviTitle = "주문정보수정";
	$P->strContents = $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	if($view_type == "offline_order"){
		$P->strLeftMenu = offline_order_menu();
	}else{
		$P->strLeftMenu = order_menu();
	}
	$P->addScript = $Script."<script language='javascript' src='../order/orders.js'></script>";
	$P->Navigation = "주문관리 > 주문정보수정";
	$P->title = "주문정보수정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}



function OrderGoodsList($odb, $list_type = "general"){
	global $admininfo , $admin_config, $currency_display,$sns_product_type;
	global $auth_write_msg, $auth_update_msg, $auth_delete_msg,$order_select_status_div,$decimals_value;

	$db = new Database;
	$oddb = new Database;

	$addWhere = " and od.status in ('".implode("','",getStatusByType($list_type))."') ";

	if($list_type == "bs_general"){
		$addWhere .=  " and product_type in (1,2) ";
	}else if($list_type == "general"){
		$addWhere .=  " and product_type not in (1,2) ";
	}

	if($admininfo[admin_level] == 9){
		if($admininfo[mem_type] == "MD"){
			$addWhere .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}
	}else if($admininfo[admin_level] == 8){
		$addWhere .= "  and od.company_id = '".$admininfo[company_id]."'";
	}

	$sql = "SELECT
		od.*, od.ptprice - od.pt_dcprice as total_sale_price , odd.delivery_dcprice, odd.ode_ix
	FROM ".TBL_SHOP_ORDER_DETAIL." od left join shop_order_delivery odd on (od.ode_ix=odd.ode_ix)
	WHERE od.oid = '".$odb->dt[oid]."' $addWhere order by od.ode_ix";

	$oddb->query($sql);

	if($oddb->total == 0){
		return ;
	}

$Contents .= "<div class='goods_area ".$list_type."'>";

if($list_type == "bs_general"){
	$Contents .= "<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>주문상품정보(해외정상)</b></div>";
}else if($list_type == "general"){
	$Contents .= "<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>주문상품정보(정상)</b></div>";
}else if($list_type == "claim"){
	$Contents .= "<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>주문/클레임</b></div>";
}else if($list_type == "incom_after_cancel"){
	$Contents .= "<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>입금후 취소</b></div>";
}else if($list_type == "incom_befor_cancel"){
	$Contents .= "<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>입금전 취소</b></div>";
}


if($odb->dt[buserid] == 'smlee890'){
	$test = 'act';
}else{
    $test = 'act';
}

$Contents .= "
<form name='order_edit_".$list_type."' method='post' onSubmit=\"return orderStatusUpdate(this,".$admininfo[admin_level].")\"  action='orders.goods_list.act.php' target='".$test."'>
<input type=hidden name='oid[]' value='".$odb->dt[oid]."'>
<input type=hidden name='act' value='select_status_update'>
<input type=hidden name='update_type' value='2'>
<input type=hidden name='pre_type' value='order_edit'>
<input type=hidden id='od_ix' value=''>
<input type=hidden name='escrow_yn' value='".$odb->dt[escrow_yn]."'>
<input type=hidden name='tno' value='".$odb->dt[tid]."'>
<input type=hidden name='settle_module' value='".$odb->dt[settle_module]."'>
<table width='100%' border='0' cellpadding='0' cellspacing='1' bgcolor=silver>";
if($list_type == "bs_general" || $list_type == "general"){
	$Contents .= "
			<tr bgcolor='#ffffff' height=40>
			<td style='padding:0 0 0 0px'>
			<table border=0>
			<tr><td>";

	if($list_type == "general"){
		$Contents .= "
						<select name='status' onchange='ViewdeliveryCodeInputBox($(this).val(),$(\"[name=status]\").index($(this)),".$admininfo[admin_level].");'>
							<option value='' >상태변경</option>";
		if($odb->dt[status]=='IR'){
			if($admininfo[admin_level] ==  9){
				$Contents .= "
								<!--option value='".ORDER_STATUS_INCOM_READY."' >".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option-->
								<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
								<option value='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE)."</option>";
			}
		}else{
			if($admininfo[admin_level] ==  9){
				$Contents .= "
								<!--option value='".ORDER_STATUS_INCOM_READY."' >".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option-->
								<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>";
			}
				$Contents .= "
								<!--option value='".ORDER_STATUS_WAREHOUSING_STANDYBY."' >".getOrderStatus(ORDER_STATUS_WAREHOUSING_STANDYBY)."</option-->
								<option value='".ORDER_STATUS_DELIVERY_READY."' >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
								<option value='".ORDER_STATUS_DELIVERY_ING."' >".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>";
			if($admininfo[admin_level] ==  9){
				$Contents .= "
								<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' >".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>";
			}

				$Contents .= "
								<option value='".ORDER_STATUS_CANCEL_APPLY."' >".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</option>";

				$Contents .= "
								<!--option value='".ORDER_STATUS_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option-->
								<option value='".ORDER_STATUS_SOLDOUT_CANCEL."' >".getOrderStatus(ORDER_STATUS_SOLDOUT_CANCEL)."</option>";

				$Contents .= "
								<option value='".ORDER_STATUS_RETURN_APPLY."' >".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</option>
								<option value='".ORDER_STATUS_EXCHANGE_APPLY."' >".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</option>";
		}
			$Contents .= "</select>";

	}else if($list_type == "bs_general"){

		$Contents .= "
						<select name='status' onchange='ViewdeliveryCodeInputBox($(this).val(),$(\"[name=status]\").index($(this)),".$admininfo[admin_level].");'>
							<option value='' >상태변경</option>";
		if($odb->dt[status]=='IR'){
			if($admininfo[admin_level] ==  9){
				$Contents .= "
								<!--option value='".ORDER_STATUS_INCOM_READY."' >".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option-->
								<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>";
			}
			$Contents .= "
								<option value='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE)."</option>";
		}else{
			if($admininfo[admin_level] ==  9){
				$Contents .= "
								<!--option value='".ORDER_STATUS_INCOM_READY."' >".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option-->
								<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>";
			}
				$Contents .= "
								<option value='' >=============</option>
								<option value='".ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY."' >".getOrderStatus(ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY)."</option>
								<option value='".ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING."' >".getOrderStatus(ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING)."</option>
								<option value='".ORDER_STATUS_AIR_TRANSPORT_READY."' >".getOrderStatus(ORDER_STATUS_AIR_TRANSPORT_READY)."</option>
								<option value='".ORDER_STATUS_AIR_TRANSPORT_ING."' >".getOrderStatus(ORDER_STATUS_AIR_TRANSPORT_ING)."</option>
								<option value='' >=============</option>
								<!--option value='".ORDER_STATUS_WAREHOUSING_STANDYBY."' >".getOrderStatus(ORDER_STATUS_WAREHOUSING_STANDYBY)."</option-->
								<option value='".ORDER_STATUS_DELIVERY_READY."' >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
								<option value='".ORDER_STATUS_DELIVERY_ING."' >".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>";
			if($admininfo[admin_level] ==  9){
				$Contents .= "
								<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' >".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>";
			}
				$Contents .= "
								<option value='".ORDER_STATUS_CANCEL_APPLY."' >".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</option>";
				$Contents .= "
								<!--option value='".ORDER_STATUS_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option-->
								<!--option value='".ORDER_STATUS_SOLDOUT_CANCEL."' >".getOrderStatus(ORDER_STATUS_SOLDOUT_CANCEL)."</option-->
								<option value='".ORDER_STATUS_RETURN_APPLY."' >".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</option>
								<option value='".ORDER_STATUS_EXCHANGE_APPLY."' >".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</option>";
		}
			$Contents .= "</select>";
	}

	$Contents = $Contents."
			</td>
			<td>
				<select name='reason_code' class='reason_code' id='reason_code_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' style='display:none;'  disabled/>";
					$return_data=fetch_order_status_div('IR','CA',"title","all");
					for($r=0;$r<count($return_data);$r++){
						$Contents .= "<option value='".key($return_data[$r])."'>".$return_data[$r][key($return_data[$r])]."</option>";
					}
				$Contents .= "
				</select>
			</td>
			<td><span class='helpcloud' help_height='50' help_width=310 help_html='<b>관리자용 메모</b> <br>관리자끼리 볼수 있는 메모입니다.<br>예)품절취소, 배송지연'>	<input type='text' name='od_message' class=textbox size=30 /></span></td>";

	//deliveryCompanyList('',"select","style='display:none'") 기존 배송업체 불러오는 함수 2014-07-25 이학봉 교체 ㄷ
	$Contents = $Contents."
			<td>
				".DeliveryMethod("delivery_method",$ddb->dt[delivery_method],"style='display:none'","select")."
			</td>
			<td>
				".deliveryCompanyList2("quick","id='quick' style='display:none'",$_SESSION[admininfo][company_id])."
			</td>
			<td><div class='deliverycode' style='display:none'>송장번호 : <input type='text' name='deliverycode' class=textbox size=15></div></td>
			<!--td id='exchangeChoice' style='display:none'><a href=\"javascript:PoPWindow('exchange_product.php?oid=".$odb->dt[oid]."',520,600,'exchange_product')\">교환상품선택</a></td-->

			<td align='right'>
				<span class='helpcloud' help_height='50' help_width=310 help_html='<b>정보변경</b> <br>정보변경을 체크하시면 상태정보 외에 택배사, 송장번호, 상태변경메모 등을 변경하실수 있습니다.'>
					<input type=checkbox id='status_info_change' value=1><label for='status_info_change'>정보변경</label>
				</span>
				";

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents .= " <input type=image src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 style='cursor:pointer;'>";
				}else{
					$Contents .= " <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 style='cursor:pointer;'></a>";
				}

				$Contents .= "
				<span class=small><!--( 수정하기 버튼을 누르면 체크된 상품에 대한 상태가 변경됩니다.)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>
			</td>";
	$Contents = $Contents."
		</tr>
	</table>

			".printStatusInfo($odb->dt[oid])."
			</td>
		</tr>";
}
	$Contents .= "
	<tr>
		<td bgcolor='silver'>
			<table border='0' width='100%' cellspacing='0' cellpadding='2'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0' class='input_table_box'>
										<tr height='30' bgcolor='#efefef' align=center>";
											if($list_type == "bs_general" || $list_type == "general"){
												$Contents .= "
												<td width='30px' class='s_td'  ><input type=checkbox  name='all_fix' onclick='fixAll(document.order_edit_".$list_type.")' ></td>";
											}
											$Contents .= "
											<td width='*' colspan=2 class='m_td'><b>상품명</b></td>
											<td width='10%' class='m_td small'><b>옵션/판매단가<br>(할인가)</b></td>
											<td width='5%' class='m_td'><b>수량<br>(색상)</b></td>
											<td width='12%' class='m_td small'><b>상품가격/할인액/적립금</b></td>
											<td width='13%' class='m_td'><b>실결제금액</b></td>
											<td width='8%' class='m_td'><b>배송비&방법</b></td>
											<td width='6%' class='m_td'><b>처리상태</b></td>
											".($admininfo[admin_level]==9 ? "<td width='6%' class='m_td' ><b>출고처리</b></td>" :"");
											if($list_type == "claim" || $list_type == "incom_after_cancel"){
												$Contents .= "<td width='6%' class='m_td' ><b>환불상태</b></td>";
											}
										$Contents .= "
											<td width='9%' class='e_td' ><b>관리</b></td>
										</tr>";

	for($j = 0; $j < $oddb->total; $j++)
	{
		$oddb->fetch($j);

		$sql="select * from shop_order_detail_discount where od_ix='".$oddb->dt[od_ix]."' ";
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

		$discount_info = $currency_display[$admin_config["currency_unit"]]["front"].number_format($oddb->dt[total_sale_price],$decimals_value).$currency_display[$admin_config["currency_unit"]]["back"];

		if($dc_etc_str!=""){
			$discount_info.=" <label class='helpcloud' help_width='".$dc_etc_width."' help_height='".$dc_etc_height."' help_html='".$dc_etc_str."'><img src='../images/icon/q_icon.png' align=''></label>";
		}

		if($dc_coupon_str!=""){
			$discount_info.=" <label class='helpcloud' help_width='".$dc_coupon_width."' help_height='".$dc_coupon_height."' help_html='".$dc_coupon_str."'><img src='../images/".$admininfo[language]."/s_use_coupon.gif' align=''></label>";
		}

		$delivery_pay_type = getDeliveryPayType($oddb->dt[delivery_pay_method]);		//배송비 결제수단 텍스트 리턴
		$delivery_method = getDeliveryMethod($oddb->dt[delivery_method]);			//배송방법 텍스트 리턴

		$Contents .= "
										<tr height='30' align='center'>";
									if($list_type == "bs_general" || $list_type == "general"){

										$Contents .= "
											<td bgColor='#ffffff' ><input type=checkbox name='od_ix[]' id='od_ix' class='od_ix' onclick='checkSet($(this));' od_status='".$oddb->dt[status]."' od_refund_status='".$oddb->dt[refund_status]."' set_group='".$oddb->dt[set_group]."' company_id='".$oddb->dt[company_id]."' ea_check='".$oddb->dt[ori_company_id]."|".$oddb->dt[delivery_type]."|".$oddb->dt[delivery_package]."|".$oddb->dt[delivery_method]."|".$oddb->dt[delivery_pay_method]."|".$oddb->dt[delivery_addr_use]."|".$oddb->dt[factory_info_addr_ix].( $oddb->dt[delivery_package]=="Y" ? "|".$oddb->dt[pid] : "")."' value='".$oddb->dt[od_ix]."' ></td>";
									}
										$Contents .= "
											<td bgColor='#ffffff' colspan='2'>
												<table width='100%'>
													<tr>
														<td align='center' width='70'>
														<!-- a href='../product/goods_input.php?id=".$oddb->dt[pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $oddb->dt[pid], 'm', $oddb->dt)."'  title='".$LargeImageSize."'><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $oddb->dt[pid], "m" , $oddb->dt)."' width=50 style='margin:5px;'></a -->
														<a href='../product/goods_input.php?id=".$oddb->dt[pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/addimgNew", $oddb->dt[pid], 'slist', $oddb->dt)."'  title='".$LargeImageSize."'><img src='".PrintImage($admin_config[mall_data_root]."/images/addimgNew", $oddb->dt[pid], "list" , $oddb->dt)."' width=50 style='margin:5px;'></a><br/>";

														if($oddb->dt[product_type]=='21'||$oddb->dt[product_type]=='31'){
															$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($oddb->dt[product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$oddb->dt[product_type].".gif' align='absmiddle' ></label> ";
														}
														if($oddb->dt[stock_use_yn]=='Y'){
															$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
														}
											$Contents .= "
														</td>";

        												if($oddb->dt[status] != ORDER_STATUS_SOLDOUT_CANCEL && ($oddb->dt[gid] != '' && ($oddb->dt[gid] == $_GET['soldout_gid']))){
        													$soldout_color = 'background-color:#efd9d9;';
														}else{
                                                            $soldout_color = '';
														}

		$Contents .= "
														<td style='padding:5px 0 5px 0;line-height:130%; $soldout_color' align='left'>";
															if($oddb->dt[co_oid]!=''){
																$Contents .= "<span style='font-weight:bold;' class='small org'>".$oddb->dt[co_oid];
															}

															if($oddb->dt[co_od_ix]!=''){
																$Contents .= " <span style='font-weight:bold;' class='small blue'>| ".$oddb->dt[co_od_ix]."</span><br/>";
															}else{
																if($oddb->dt[co_oid]!=''){
																	$Contents .= "</span><br/>";
																}
															}

															$Contents .= "
															<span style='color:#007DB7;font-weight:bold;' class='small'>".$oddb->dt[od_ix]."|".$oddb->dt[gid]."</span> <b class='small' style='color:red;font-weight:bold;'>배송(".$oddb->dt[odd_ix].")</b><br/>";
														
												if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){

													if($bcompany_id != $oddb->dt[company_id]){
														$seller_info_str= GET_SELLER_INFO($oddb->dt[company_id]);
													}

													$Contents .= "<a href=\"javascript:PoPWindow('../seller/seller_company.php?company_id=".$oddb->dt[company_id]."&mmode=pop',960,600,'brand')\" class='helpcloud' help_width='230' help_height='80' help_html='".$seller_info_str."' ><b>".($oddb->dt[company_name] ? $oddb->dt[company_name]:"-")."</b></a><br>";
												}

												if(in_array($oddb->dt[product_type],$sns_product_type)){
													$Contents .= "<a href=\"/sns/shop/goods_view.php?id=".$oddb->dt[pid]."\" target=_blank>[".$oddb->dt[company_name]."] ".$oddb->dt[pname]."</a>";
												}else{
													$Contents .= "<a href=\"/shop/goods_view.php?id=".$oddb->dt[pid]."\" target=_blank>";
													if($oddb->dt[product_type]=='99'||$oddb->dt[product_type]=='21'||$oddb->dt[product_type]=='31'){
														$Contents .= "<b class='".($oddb->dt[product_type]=='99' ? "red" : "blue")."' >[".$oddb->dt[company_name]."] ".$oddb->dt[pname]."</b><br/><strong>".$oddb->dt[set_name]."<br /></strong>".$oddb->dt[sub_pname];
													}else{
														$Contents .= "[".$oddb->dt[brand_name]."] ".$oddb->dt[pname];
													}
													$Contents .= "</a>";
												}
											
											$Contents .= "
																<br/><input type='button' value='상담내역리스트 추가' onclick=\"$('textarea[name=memo]').html($('textarea[name=memo]').html() + '[".$oddb->dt[brand_name]."] ".$oddb->dt[pname].($oddb->dt[pcode] ? "(".$oddb->dt[pcode].")" : "").(strip_tags($oddb->dt[option_text]) ? " ".strip_tags($oddb->dt[option_text])."" : "")." ') \" />
														</td>
													</tr>
												</table>
											</td>
											<td bgColor='#ffffff' align=left>
												<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
													<tr>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
															".strip_tags($oddb->dt[option_text])."".($oddb->dt[option_price] != '' ? " + ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($oddb->dt[option_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."":"")."
														</td>
													</tr>
													<tr>
														<td align='center' valign='middle'>
															".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($oddb->dt[psprice]+$oddb->dt[option_price],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
												</table>
											</td>
											<td bgColor='#ffffff'>".$oddb->dt[pcnt]." 개<br>(".$oddb->dt[add_info].")</td>
											<td bgColor='#ffffff' align=left>
												<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
													<tr>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
															".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($oddb->dt[pt_dcprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
													<tr>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5'>
															".$discount_info."
														</td>
													</tr>
													<tr>
														<td align='center' valign='middle'>
															".number_format($oddb->dt[reserve],$decimals_value)." P
														</td>
													</tr>
												</table>
											</td>
											<td bgColor='#ffffff' align=left>
												<table width='100%' height='87' border=0 cellpadding=0 cellspacing=0>
													<tr>
														<td class='m_td' style='border-bottom:1px solid #c5c5c5;background-color:#fff;'>판매가</td>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5;'>
															 ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($oddb->dt[pt_dcprice],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
													<tr>
														<td class='m_td' style='border-bottom:1px solid #c5c5c5;background-color:#fff;' width='40%'>공급가</td>
														<td align='center' valign='middle' style='border-bottom:1px solid #c5c5c5;'>
															".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format(round($oddb->dt[pt_dcprice])/1.1,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
													<tr>
														<td class='m_td' style='background-color:#fff;'>세액</td>
														<td align='center' valign='middle' style=''>
															".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($oddb->dt[pt_dcprice]-round($oddb->dt[pt_dcprice])/1.1,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
														</td>
													</tr>
												</table>
											</td>";
											
											//배송비 분리 시작 2014-05-21 이학봉
											if($b_ode_ix != $oddb->dt[ode_ix] ){
												$sql = "SELECT 
													COUNT(DISTINCT(od.od_ix)) AS com_cnt
												FROM 
													".TBL_SHOP_ORDER." o,
													".TBL_SHOP_ORDER_DETAIL." od
												where 
													o.oid = od.oid 
													and o.oid = '".$oddb->dt[oid]."' 
													and od.ode_ix='".$oddb->dt[ode_ix]."'
													$addWhere 
													";

												$db->query($sql);//$od_db는 상단에서 선언
												$db->fetch();
												$com_cnt=$db->dt["com_cnt"];

												$Contents .="<td bgColor='#ffffff' class='' align=center style='line-height:140%;' rowspan='".$com_cnt."'>
															".$currency_display[$admin_config["currency_unit"]]["front"].number_format($oddb->dt[delivery_dcprice],$decimals_value).$currency_display[$admin_config["currency_unit"]]["back"]."
															</td>";
											}
											//배송비 분리 끝 2014-05-21 이학봉

											$Contents .="
											<td bgColor='#ffffff' align=center style='line-height:130%'>";

											$Contents .= getOrderStatus($oddb->dt[status]);

											if($oddb->dt["admin_message"]!="") {
													$Contents .="<br><b>".$oddb->dt["admin_message"]."</b>";
											}

											if($oddb->dt[di_date] && $oddb->dt[status] == "DI"){
												$Contents .= "<br />(".substr($oddb->dt[di_date],0,10).")";
											}

											if($oddb->dt[dc_date] && $oddb->dt[status] == "DC"){
												$Contents .= "<br />(".substr($oddb->dt[dc_date],0,10).")";
											}

											if(trim($oddb->dt[ac_date])){
												$Contents .= "<br /><a href=\"/admin/seller_accounts/accounts_detail.php?ac_ix=".$oddb->dt[ac_ix]."\" target=_blank>(".substr($oddb->dt[ac_date],0,4)."-".substr($oddb->dt[ac_date],4,2)."-".substr($oddb->dt[ac_date],6,2).")</a>";
											}

											if(($oddb->dt[invoice_no] || $oddb->dt[quick])){

												if(substr_count($oddb->dt[invoice_no],",") > 0){

													$explode_invoice_no = explode(",",$oddb->dt[invoice_no]);
													$Contents .= "
													<br><a style='cursor:pointer;' onclick=\"$('#invoice_no_table_".$oddb->dt[od_ix]."').toggle();\">".codeName($oddb->dt[quick])."<br/>(".$explode_invoice_no[0]."외 ".(count($explode_invoice_no)-1).")</a>
													<table border=1 id='invoice_no_table_".$oddb->dt[od_ix]."' class='invoice_no_table' style='position:absolute; right:110px;width:100px;margin-top:-40px;display:none;' cellpadding=1  cellspacing=0>
														<col width=3><col width=*><col width=13><col width=*><col width=3>
														<tr>
															<th class='box_01'></th>
															<td class='box_02' colspan=3></td>
															<th class='box_03'></th>
														</tr>
														<tr>
															<th class='box_04'></th>
															<td class='box_05 ' colspan=3 style='line-height:130%;padding:5px;'>";
																foreach($explode_invoice_no as $invoice_no){
																	$Contents .= "
																	<div style='cursor:pointer;' onclick=\"searchGoodsFlow('".$oddb->dt[quick]."', '".str_replace("-","",$invoice_no)."');\">".$invoice_no."</div>";
																}
															$Contents .= "
															</td>
															<th class='box_06'></th>
														</tr>
														<tr>
															<th class='box_07'></th>
															<td class='box_02' colspan=3></td>
															<th class='box_09'></th>
														</tr>
													</table>
													";
                                                    $Contents .= "
                                                    <br>
                                                    <a style='cursor:pointer;color:red;font-weight:bold;' onclick=\"$('#invoice_no_table_".$explode_invoice_no[0]."').toggle();\">
                                                        ".getGoodsflowStatusdiv(getGoodsflowStatus('front', $oddb->dt[oid]."_".$explode_invoice_no[0], $oddb->dt[order_from]))."
                                                        <br/>(".$explode_invoice_no[0]."외 ".(count($explode_invoice_no)-1).")
                                                    </a>
                                                    <table border=1 id='invoice_no_table_".$explode_invoice_no[0]."' class='invoice_no_table' style='position:absolute;width:100px;right:110px;margin-top:-30px;display:none;' cellpadding=1  cellspacing=0>
                                                        <col width=3><col width=*><col width=13><col width=*><col width=3>
                                                        <tr>
                                                            <th class='box_01'></th>
                                                            <td class='box_02' colspan=3></td>
                                                            <th class='box_03'></th>
                                                        </tr>
                                                        <tr>
                                                            <th class='box_04'></th>
                                                            <td class='box_05 ' colspan=3 style='line-height:130%;padding:5px;'>";
                                                    foreach($explode_invoice_no as $invoice_no){
                                                        $Contents .= getGoodsflowStatus('admin', $oddb->dt[oid]."_".$invoice_no, $oddb->dt[order_from]);
                                                    }
                                                    $Contents .= "
                                                            </td>
                                                            <th class='box_06'></th>
                                                        </tr>
                                                        <tr>
                                                            <th class='box_07'></th>
                                                            <td class='box_02' colspan=3></td>
                                                            <th class='box_09'></th>
                                                        </tr>
                                                    </table>
                                                    ";

												}else{
													//$Contents .= "<br><a href=\"javascript:searchGoodsFlow('".$oddb->dt[quick]."', '".str_replace("-","",$oddb->dt[invoice_no])."')\">".codeName($oddb->dt[quick])."<br/>(".$oddb->dt[invoice_no].")</a> ";
                                                    $Contents .= "<br>".codeName($oddb->dt[quick])."<br/> ";
                                                    //$quick_div = codeName($oddb->dt[quick])."<br/>(".$oddb->dt[invoice_no].")";
                                                    // 굿스플로 연동 상태 global_util
                                                    //$Contents .= getGoodsflowStatusdiv(getGoodsflowStatus('front', $oddb->dt[oid]."_".$oddb->dt[invoice_no], $oddb->dt[order_from]));
                                                    $Contents .= getGoodsflowStatus('admin', $oddb->dt[oid]."_".$oddb->dt[invoice_no], $oddb->dt[order_from], 'style="color:red;font-weight:bold;"');
												}
											}

											$Contents .="
											</td>";

									if($admininfo[admin_level]==9){
										$Contents .= "<td bgColor='#ffffff'>".getOrderStatus($oddb->dt[delivery_status])."</td>";
									}
									if($list_type == "claim" || $list_type == "incom_after_cancel"){
										$Contents .= "
										<td bgColor='#ffffff'>".getOrderStatus($oddb->dt[refund_status])."</td>";
									}
										$Contents .= "
									<td bgColor='#ffffff'>
										<table>
											<tr>
												<td valign='center' align='center'>
													<img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$odb->dt[user_code]."&od_ix=".$oddb->dt[od_ix]."',500,380,'sendsms')\" style='cursor:pointer;' title='SMS보내기'>
												</td>
												<td align='center'>
													<span ".($oddb->dt[pcnt] > 1 && $oddb->dt[refund_status]=="" ? "style='display:inline;'":"style='display:none;'")."><img src='../images/".$admininfo["language"]."/btn_order_split.gif' align=absmiddle onclick=\"PoPWindow3('./order_detail_separation.php?mmode=pop&od_ix=".$oddb->dt[od_ix]."',1000,600,'order_detail_separation')\" style='cursor:pointer;' /></span>";
										if($list_type == "claim"|| $list_type == "incom_after_cancel"){
											if($oddb->dt[refund_status]=='FA' && $_SESSION["admininfo"]["admin_level"] > 8){
												$Contents .= "<img src='../images/".$admininfo["language"]."/btn_part_cancel.gif' align=absmiddle onclick=\"ShowModalWindow('refund_price_give.php?pay_type=pg&oid=".$odb->dt[oid]."',1000,1000,'refund_price_give');\" style='cursor:pointer;' /> ";
											}
										}


										$deny_general_yn=false;
										$sql="SELECT * FROM shop_order_status WHERE oid='".$odb->dt[oid]."' and od_ix='".$oddb->dt[od_ix]."' ";
										$db->query($sql);
										$status_history=$db->fetchall();

										if($list_type == "general"){
											if($db->total){
												foreach($status_history as $history){
													if(($history[status]=='DR'||$history[status]=='DD') && $history[reason_code]){
														$deny_general_yn=true;
													}
												}
											}
										}

										$Contents .= "
												</td>
											</tr>
											<tr>
												<td colspan='2' align='center'>";
												if($list_type == "claim"||$list_type == "incom_after_cancel" || $list_type == "incom_befor_cancel" || ($list_type == "general" && $deny_general_yn)){
                                                    if( !($list_type == "claim" && $oddb->dt[status]=="ER") ) {
                                                        $Contents .= "<img src='../images/" . $admininfo["language"] . "/btn_claim_info.gif' align=absmiddle onclick=\"$('#od_ix_detail_" . $oddb->dt[od_ix] . "').toggle();\" style='cursor:pointer;' />
													";
                                                    }else if($list_type == "claim" && $oddb->dt[status]=="ER"){
                                                    	if(substr_count($_SESSION['admininfo']['admin_id'],'forbiz') > 0 || true) {
                                                            $Contents .= "<input type='button' value='교환상품변경' onclick=\"PoPWindow3('./order_change_product.php?mmode=pop&oid=" . $odb->dt[oid] . "&od_ix=" . $oddb->dt[od_ix] . "',600,600,'order_change_product')\"  />";
                                                        }
													}
												}
										$Contents .= "
													".($oddb->dt[product_type] == "1" ? "<a href=\"javascript:PoPWindow3('./order_siteinfo.php?mmode=pop&oid=".$odb->dt[oid]."&od_ix=".$oddb->dt[od_ix]."',600,600,'order_siteinfo')\"'><img src='../images/".$admininfo["language"]."/btn_order_siteinfo.gif' align=absmiddle></a>":"")."
												</td>
											</tr>
										</table>";

								$Contents .= "
									</td>
								</tr>";

			$bproduct_id = $oddb->dt[pid];
			$bset_group = $oddb->dt[set_group];
			$b_product_type = $oddb->dt[product_type];
			$b_factory_info_addr_ix  = $oddb->dt[factory_info_addr_ix];
			$b_delivery_type  = $oddb->dt[delivery_type];
			$bcompany_id = $oddb->dt[company_id];
			$b_ode_ix = $oddb->dt[ode_ix];

if($list_type == "claim"){

	$info_appliy="";
	$info_deny="";
	$info_complete="";
	$info_appliy_responsible="";
	$info_history="";
	$exchange_deliveryinfo="";
	$return_deliveryinfo="";
	$appliy_name="";

	if($db->total){
		foreach($status_history as $history){

			$order_status_exchange = array(ORDER_STATUS_EXCHANGE_APPLY,ORDER_STATUS_EXCHANGE_DENY,ORDER_STATUS_EXCHANGE_ING,ORDER_STATUS_EXCHANGE_DELIVERY,ORDER_STATUS_EXCHANGE_ACCEPT,ORDER_STATUS_EXCHANGE_DEFER,ORDER_STATUS_EXCHANGE_IMPOSSIBLE,ORDER_STATUS_EXCHANGE_COMPLETE);

			if(in_array($oddb->dt[status],$order_status_exchange)){

				$sql="SELECT * FROM shop_order_detail_deliveryinfo WHERE oid='".$odb->dt[oid]."' and od_ix='".$oddb->dt[od_ix]."' and order_type='2' ";
				$db->query($sql);
				$exchange_deliveryinfo=$db->fetch();

				$sql="SELECT * FROM shop_order_detail_deliveryinfo WHERE oid='".$odb->dt[oid]."' and od_ix='".$oddb->dt[od_ix]."' and order_type in ('3','4') ";
				$db->query($sql);
				$return_deliveryinfo=$db->fetch();

				if($history[status]=='EA'){
					$info_appliy=$history;
					if($info_appliy[c_type]=="B"){
						$appliy_name="구매자";
					}elseif($info_appliy[c_type]=="S"){
						$appliy_name="판매자";
					}elseif($info_appliy[c_type]=="M"){
						$appliy_name="담당MD";
					}else{
						$appliy_name="-";
					}

					if($order_select_status_div["F"]["DC"]["EA"][$info_appliy[reason_code]][type]=="B"){
						$info_appliy_responsible="구매자책임";
					}elseif($order_select_status_div["F"]["DC"]["EA"][$info_appliy[reason_code]][type]=="S"){
						$info_appliy_responsible="판매자책임";
					}else{
						$info_appliy_responsible="책임자없음";
					}
				}elseif($history[status]=='EC'){
					$info_complete=$history;
				}

				$info_history[]=$history;

			}else{

				$sql="SELECT * FROM shop_order_detail_deliveryinfo WHERE oid='".$odb->dt[oid]."' and od_ix='".$oddb->dt[od_ix]."' and order_type in ('3','4') ";
				$db->query($sql);
				$return_deliveryinfo=$db->fetch();

				if($history[status]=='RA'){
					$info_appliy=$history;
					if($info_appliy[c_type]=="B"){
						$appliy_name="구매자";
					}elseif($info_appliy[c_type]=="S"){
						$appliy_name="판매자";
					}elseif($info_appliy[c_type]=="M"){
						$appliy_name="담당MD";
					}else{
						$appliy_name="-";
					}

					if($order_select_status_div["F"]["DC"]["RA"][$info_appliy[reason_code]][type]=="B"){
						$info_appliy_responsible="구매자책임";
					}elseif($order_select_status_div["F"]["DC"]["RA"][$info_appliy[reason_code]][type]=="S"){
						$info_appliy_responsible="판매자책임";
					}else{
						$info_appliy_responsible="책임자없음";
					}
				}elseif($history[status]=='RC'){
					$info_complete=$history;
				}

				$info_history[]=$history;
			}
		}
	}

	$Contents .= "			<tr id='od_ix_detail_".$oddb->dt[od_ix]."' style='display:none;'>
										<td colspan=11 bgColor='#ffffff' style='padding:10px;'>
											<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
												<col width='25%'/>
												<col width='25%'/>
												<col width='25%'/>
												<col width='25%'/>
												<tr>
													<td class='input_box_title'>신청자/클레임책임</td>
													<td class='input_box_item'>".$appliy_name." / ".$info_appliy_responsible."</td>
													<td class='input_box_title'>신청일자</td>
													<td class='input_box_item'>".$info_appliy[regdate]."</td>
												</tr>
												<tr>
													<td class='input_box_title'>신청상세사유</td>
													<td class='input_box_item' style='' colspan='3'>".$info_appliy[status_message]."</td>
												</tr>
												<tr>
													<td class='input_box_title'>수거지 주소</td>
													<td class='input_box_item' style='' colspan='3'>[".$return_deliveryinfo[zip]."] ".$return_deliveryinfo[addr1]." ".$return_deliveryinfo[addr2]."</td>
												</tr>";
										if(is_array($exchange_deliveryinfo)){
											$Contents .= "
												<tr>
													<td class='input_box_title'>교환발송주소</td>
													<td class='input_box_item' style='' colspan='3'>[".$exchange_deliveryinfo[zip]."] ".$exchange_deliveryinfo[addr1]." ".$exchange_deliveryinfo[addr2]."</td>
												</tr>";
										}
										$Contents .= "
												<tr>
													<td class='input_box_title'>발송여부</td>
													<td class='input_box_item'>".($return_deliveryinfo[send_yn]=="Y"?"발송":"미발송")."</td>
													<td class='input_box_title'>발송방법</td>
													<td class='input_box_item'>".($return_deliveryinfo[send_type]=="1"?"직접발송":"지정택배요청")."</td>
												</tr>
												<tr>
													<td class='input_box_title'>발송정보</td>
													<td class='input_box_item'>".DeliveryMethod("",$return_deliveryinfo[delivery_method],"","text")." ".deliveryCompanyList($return_deliveryinfo[quick],"text")." ".$return_deliveryinfo[invoice_no]."</td>
													<td class='input_box_title'>추가배송비</td>
													<td class='input_box_item'>".number_format($return_deliveryinfo[add_delivery_price],$decimals_value)."</td>
												</tr>
												<tr>
													<td class='input_box_title'>추가배송비결제방법</td>
													<td class='input_box_item'>".getMethodStatus($return_deliveryinfo[payment_method])."</td>
													<td class='input_box_title'>배송비결제여부</td>
													<td class='input_box_item'>".($return_deliveryinfo[payment_yn]=="Y"?"입금확인":"입금예정")."</td>
												</tr>
												<tr>
													<td class='input_box_title'>반품/교환처리히스토리</td>
													<td class='input_box_item' colspan='3' style='padding:10px;'>";
											if(is_array($info_history)){
												foreach($info_history as  $i_h){
													$Contents .= $i_h[regdate]." - ".getOrderStatus($i_h[status])." -".$i_h[status_message]." - ".$i_h[admin_message]."<br/>";
												}
											}
							$Contents .= "	</td>
												</tr>
												<tr>
													<td class='input_box_title'>처리확정일자</td>
													<td class='input_box_item'>".$info_complete[regdate]."</td>
													<td class='input_box_title'>담당자</td>
													<td class='input_box_item'>".$info_complete[admin_message]."</td>
												</tr>
											</table>
										</td>
									</tr>";





}elseif($list_type == "incom_after_cancel" || $list_type == "incom_befor_cancel" || ($list_type == "general" && $deny_general_yn)){

	$info_appliy="";
	$info_deny="";
	$info_complete="";
	$info_appliy_responsible="";
	$appliy_name="";
	$info_history="";

	if($list_type == "incom_after_cancel" || $deny_general_yn){
		if($db->total){
			foreach($status_history as $history){
				if(($history[status]=='CA'||$history[status]=='CC') && $history[reason_code]){
					$info_appliy=$history;

					if($info_appliy[c_type]=="B"){
						$appliy_name="구매자";
					}elseif($info_appliy[c_type]=="S"){
						$appliy_name="판매자";
					}elseif($info_appliy[c_type]=="M"){
						$appliy_name="담당MD";
					}else{
						$appliy_name="-";
					}

					if($order_select_status_div["F"]["IC"]["CA"][$info_appliy[reason_code]][type]=="B"){
						$info_appliy_responsible="구매자책임";
					}elseif($order_select_status_div["F"]["IC"]["CA"][$info_appliy[reason_code]][type]=="S"){
						$info_appliy_responsible="판매자책임";
					}else{
						$info_appliy_responsible="책임자없음";
					}

				}elseif(($history[status]=='DR'||$history[status]=='DD') && $history[reason_code] ){
					$info_deny=$history;
				}elseif($history[status]=='CC'){
					$info_complete=$history;
				}
				$info_history[]=$history;
			}
		}
	}elseif($list_type == "incom_befor_cancel"){
		if($db->total){
			foreach($status_history as $history){
				if($history[status]=='IB'){
					$info_appliy=$history;
					if($order_select_status_div["F"]["IC"]["CA"][$info_appliy[reason_code]][type]=="B"){
						$info_appliy_responsible="구매자책임";
					}elseif($order_select_status_div["F"]["IC"]["CA"][$info_appliy[reason_code]][type]=="S"){
						$info_appliy_responsible="판매자책임";
					}else{
						$info_appliy_responsible="책임자없음";
					}
					$info_complete=$history;
				}
			}
		}
	}

	$Contents .= "			<tr id='od_ix_detail_".$oddb->dt[od_ix]."' style='display:none;'>
										<td bgColor='#ffffff' colspan='".($list_type == "incom_after_cancel" ? "11" : "10")."' style='padding:10px;'>
											<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
												<col width='25%'/>
												<col width='25%'/>
												<col width='25%'/>
												<col width='25%'/>
												<tr>
													<td class='input_box_title'>신청자/취소책임</td>
													<td class='input_box_item'>".$appliy_name." / ".$info_appliy_responsible."</td>
													<td class='input_box_title'>신청일자</td>
													<td class='input_box_item'>".$info_appliy[regdate]."</td>
												</tr>
												<tr>
													<td class='input_box_title'>신청상세사유</td>
													<td class='input_box_item' style='' colspan='3'>".$info_appliy[status_message]."</td>
												</tr>";
								if($deny_general_yn){
								$Contents .= "
												<tr>
													<td class='input_box_title'>취소거부상세사유</td>
													<td class='input_box_item'>".$info_deny[status_message]."</td>
													<td class='input_box_title'>취소거부일자</td>
													<td class='input_box_item'>".$info_deny[regdate]."</td>
												</tr>";
								}
								if($list_type == "incom_after_cancel"){
								$Contents .= "
												<tr>
													<td class='input_box_title'>처리히스토리</td>
													<td class='input_box_item' colspan='3' style='padding:10px;'>";
											if(is_array($info_history)){
												foreach($info_history as  $i_h){
													$Contents .= $i_h[regdate]." - ".getOrderStatus($i_h[status])." -".$i_h[status_message]." - ".$i_h[admin_message]."<br/>";
												}
											}
							$Contents .= "	</td>
												</tr>";
								}
							$Contents .= "
												<tr>
													<td class='input_box_title'>처리확정일자</td>
													<td class='input_box_item'>".$info_complete[regdate]."</td>
													<td class='input_box_title'>담당자</td>
													<td class='input_box_item'>".$info_complete[admin_message]."</td>
												</tr>
											</table>
										</td>
									</tr>";
}

//$Contents .="			<tr><td colspan='".($_SESSION["admininfo"]["admin_level"] == 9 ? ($list_type == "incom_after_cancel" || $list_type == "claim" ? "10" : "10") : ($list_type == "incom_after_cancel" || $list_type == "claim" ? "9" : "9"))."' class=dot-x></td></tr>";

	}

    $Contents = $Contents."
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

<!-- 수정마침 -->

				</td>
			  </tr>
			</table>
        </form>
	</div>";

	return $Contents;
}

function PrintOrderMemo($oid){
	global $admininfo, $page, $nset, $QUERY_STRING,$memo_state_array,$memo_call_type_array;
	$mdb = new Database;
	$sdb = new Database;

	include("../logstory/class/sharedmemory.class");
	$shmop = new Shared("delay_order_process_rule");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$delay_rule = $shmop->getObjectForKey("delay_order_process_rule");
	$delay_rule = unserialize(urldecode($delay_rule));

	$sql = "select count(*) as total from shop_order_memo where oid ='$oid'    ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];


	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>
				<col width='40px'>
				<col width='6%'>
				<col width='70px'>
				<col width='*'>
				<col width='6%'>
				<col width='6%'>
				<!--col width='6%'-->
				<col width='7%'>
				<col width='8%'>
				<!--col width='10%'>
				<col width='10%'-->
				<col width='8%'>
				<tr align=center bgcolor=#efefef height=30>
					<td class=s_td>알림</td>
					<td class=m_td>분류</td>
					<td class=m_td>고객상태</td>
					<td class=m_td>상담내용</td>
					<td class=m_td>콜처리유형</td>
					<td class=m_td>접수자</td>
					<!--td class=m_td>처리담당자</td-->
					<td class=m_td>접수일</td>
					<td class=m_td>처리상태</td>
					<!--td class=m_td>전화응대</td>
					<td class=m_td>총 처리시간</td-->
					<td class=e_td>관리</td>
				</tr>";
	if ($total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=12 align=center><!--주문 상담내역이  존재 하지 않습니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</td></tr>";
	}else{
		
		//limit $start, $max
		if($mdb->dbms_type == "oracle"){
			$mdb->query("select case when d.div_depth = 2 then (select d2.div_name from bbs_manage_div d2 where d2.div_ix = d.parent_div_ix)||'>'||d.div_name else d.div_name end as div_name,d.parent_div_ix,d.div_depth,m.* from shop_order_memo m, bbs_manage_div d where m.oid ='$oid' and m.memo_div=d.div_ix(+) order by m.regdate desc ");
		}else{
			$mdb->query("select case when d.div_depth = 2 then concat((select d2.div_name from bbs_manage_div d2 where d2.div_ix = d.parent_div_ix),'>',d.div_name) else d.div_name end as div_name,d.parent_div_ix,d.div_depth,m.* from shop_order_memo m left join bbs_manage_div d on (m.memo_div=d.div_ix) where m.oid ='$oid' order by m.regdate desc");
		}


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			//$memo_state_array 는 constants.php 에 있음

			$reg_datetime=explode(" ",$mdb->dt[regdate]);
			$reg_date=explode("-",$reg_datetime[0]);
			$reg_time=explode(":",$reg_datetime[1]);
			$reg_mktime=mktime($reg_time[0],$reg_time[1],$reg_time[2],$reg_date[1],$reg_date[2],$reg_date[0]);

			$complete_datetime=explode(" ",$mdb->dt[complete_date]);
			$complete_date=explode("-",$complete_datetime[0]);
			$complete_time=explode(":",$complete_datetime[1]);
			$complete_mktime=mktime($complete_time[0],$complete_time[1],$complete_time[2],$complete_date[1],$complete_date[2],$complete_date[0]);

			if($mdb->dt[urgency_yn]=="Y")
				$alarm_img_str="<img src='../images/icon/alarm_danger.gif'>";
			elseif($delay_rule["omr_omc_yn"]=="Y"&&($delay_rule["omr_omc_day"]!="" && $reg_mktime < (time()-(86400*$delay_rule["omr_omc_day"]))))
				$alarm_img_str="<img src='../images/icon/alarm_warning.gif'>";
			else
				$alarm_img_str="";

			if($mdb->dt[call_action_yn]=="Y")
				$call_action="필요(".($mdb->dt[call_action_state]=="1"?"완료":"대기중").")".($mdb->dt[call_action_date]!="" ? "<br/>".$mdb->dt[call_action_date]:"").($mdb->dt[call_action_time]!="" ? "<br/>".$mdb->dt[call_action_time]:"");
			else
				$call_action="불필요";

			$sql="select
						'counselor' as mem_type,
						ccd.mem_code,
						scd.dp_name,
						cd.duty_name,
						AES_DECRYPT(UNHEX(ccd.com_tel),'".$sdb->ase_encrypt_key."') as com_tel,
						AES_DECRYPT(UNHEX(ccd.mail),'".$sdb->ase_encrypt_key."') as mail
					from
						common_member_detail ccd
						left join ".TBL_SHOP_COMPANY_DEPARTMENT." scd on (ccd.department = scd.dp_ix)
						left join ".TBL_SHOP_COMPANY_DUTY." cd on (ccd.duty = cd.cu_ix)
					where ccd.code='".$mdb->dt[counselor_ix]."' and code!=''
					union
					select
						'charger' as mem_type,
						ccd.mem_code,
						scd.dp_name,
						cd.duty_name,
						AES_DECRYPT(UNHEX(ccd.com_tel),'".$sdb->ase_encrypt_key."') as com_tel,
						AES_DECRYPT(UNHEX(ccd.mail),'".$sdb->ase_encrypt_key."') as mail
					from
						common_member_detail ccd
						left join ".TBL_SHOP_COMPANY_DEPARTMENT." scd on (ccd.department = scd.dp_ix)
						left join ".TBL_SHOP_COMPANY_DUTY." cd on (ccd.duty = cd.cu_ix)
					where ccd.code='".$mdb->dt[charger_ix]."' and code!='' ";
			$sdb->query($sql);

			$counselor_info=$sdb->fetchall("object");

			$counselor_bool=false;
			$charger_bool=false;
			if(is_array($counselor_info)){
				foreach($counselor_info as $ci){
					$com_tel=explode("-",$mdb->dt[com_tel]);

					if($ci["mem_type"]=="counselor"){
						$counselor_str="<span style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='직원(코드) : ".$mdb->dt[counselor]." (".$ci["mem_code"].") <br/>부서 : ".$mdb->dt[dp_name]."<br/>직책 : ".$mdb->dt[duty_name]." <br/>회사내선 : ".$com_tel[3]." <br/>이메일 : ".$mdb->dt["mail"]."' />".$mdb->dt[counselor]."</span>";
						$counselor_bool=true;
					}

					if($ci["mem_type"]=="charger"){
						$charger_str="<span style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='직원(코드) : ".$mdb->dt[charger]." (".$ci["mem_code"].") <br/>부서 : ".$mdb->dt[dp_name]."<br/>직책 : ".$mdb->dt[duty_name]." <br/>회사내선 : ".$com_tel[3]." <br/>이메일 : ".$mdb->dt["mail"]."' />".$mdb->dt[charger]."</span>";
						$charger_bool=true;
					}

				}
			}

			if(!$counselor_bool)		$counselor_str=$mdb->dt[counselor];
			if(!$charger_bool)			$charger_str=$mdb->dt[charger];

			$mString .= "<tr height=45 bgcolor=#ffffff align=center>
			<td bgcolor='#ffffff'>".$alarm_img_str."</td>
			<td class='list_box_td list_bg_gray'>".$mdb->dt[div_name]."</td>
			<td bgcolor='#ffffff'><img src='../images/icon/mood_state_".$mdb->dt[user_mood_state].".png' /></td>
			<td class='list_box_td point' align=left style='text-align:left;padding:10px;' style='word-break:break-all' id='memo_".$mdb->dt[om_ix]."' >".nl2br($mdb->dt[memo])."</td>
			<td class='list_box_td'list_bg_gray>".$memo_call_type_array[$mdb->dt[call_type]]."</td>
			<td class='list_box_td'>".$counselor_str."</td>
			<!--td class='list_box_td list_bg_gray'>".$charger_str."</td-->
			<td class='list_box_td'>".str_replace(" ","<br/>",$mdb->dt[regdate])."</td>
			<td class='list_box_td list_bg_gray'>".$memo_state_array[$mdb->dt[memo_state]]."<br/>".str_replace(" ","<br/>",$mdb->dt[memo_state_change_date])."</td>
			<!--td class='list_box_td'>".$call_action."</td>
			<td class='list_box_td list_bg_gray'>".($mdb->dt[memo_state]=="4"? round(($complete_mktime-$reg_mktime)/86400,1)."일" : "-")."</td-->
			<td class='list_box_td' align=center nowrap>";
			if($mdb->dt[charger_ix] == $_SESSION["admininfo"]["charger_ix"] || $mdb->dt[counselor_ix] == $_SESSION["admininfo"]["charger_ix"]){
				$mString .= "
					<img  src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 style='cursor:pointer;' onclick=\"memoModify('".$mdb->dt[oid]."','".$mdb->dt[om_ix]."','".$mdb->dt[div_depth]."','".$mdb->dt[parent_div_ix]."','".$mdb->dt[memo_div]."','".$mdb->dt[memo_state]."','".$mdb->dt[charger]."','".$mdb->dt[charger_ix]."','".$mdb->dt[urgency_yn]."','".$mdb->dt[call_type]."','".$mdb->dt[call_action_yn]."','".$mdb->dt[call_action_date]."','".$mdb->dt[call_action_time]."','".$mdb->dt[call_action_state]."','".$mdb->dt[user_mood_state]."')\">
					<img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 style='cursor:pointer;' onclick=\"memoDelete('".$mdb->dt[oid]."','".$mdb->dt[om_ix]."')\">";
			}
			$mString .= "
			</td>
			</tr>
			";
		}

		//$mString .= "<tr bgcolor=#ffffff height=40><td colspan=8 align=left><a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../image/bt_all_del.gif' border=0 align=absmiddle ></a></td></tr>";
	}

	//$query_string = str_replace("nset=$nset&page=$page&","",$QUERY_STRING) ;
	//echo $query_string;
	//$mString .= "<tr height=50 bgcolor=#ffffff><td colspan=6 align=left>".page_bar($total, $page, $max,"&".$query_string,"")."</td></tr>
	$mString .= "</table>";

	return $mString;
}


function printStatusInfo($oid){
	return false;
	$mdb = new Database;

	$mdb->query("select * from ".TBL_SHOP_ORDER_STATUS." where oid = '$oid' ");

	for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);
		$mstring .= $mdb->dt[regdate] ." - ". getOrderStatus($mdb->dt[status])."<br>";
	}
	return $mstring;
}


?>
