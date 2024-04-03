<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

if(empty($opay_ix)){
	echo " 잘못된 접근입니다.";
	exit;
}

$Script = "
<script type='text/javascript'>
<!--
	$(document).ready(function(){
		pay_status_click($('input[name=pay_status]:checked'));
		method_click($('input[name=method]:checked'));

		$('input[name=pay_status]').click(function(){
			pay_status_click($(this));
		});

		$('input[name=method]').click(function(){
			method_click($(this));
		});

		$('#payment_reserve').click(function(){
			payment_reserve_click($(this));
		});
	})

	function payment_reserve_click(obj){
		if(Number(obj.attr('payment_price')) > Number(obj.attr('mileage'))){
			alert('보유 마일리지가 결제금액보다 작아서 결제하실수 없습니다.');
			obj.attr('checked',false);
		}else{
			$('input[name=pay_status][value=IC]').attr('checked',true);
		}
	}

	function pay_status_click(obj){
		$('.tr_pay_status').hide();
		$('.tr_pay_status_'+obj.val()).show();
		if(obj.val()!='IR' && obj.val()!='IC'){
			$('.tr_method').hide();
			$('.tr_pay_status').find('input[type=radio]').attr('checked',false);
			//$('.tr_pay_status').find('select').val('');
			$('.tr_pay_status').find('checkbox').attr('checked',false);
		}
	}

	function method_click(obj){
		$('.tr_method').hide();
		$('.tr_method_'+obj.val()).show();

		//$('.tr_pay_status').find('select').val('');
		$('.tr_pay_status').find('checkbox').attr('checked',false);
	}

	function addPriceCheck(frm){
		if(CheckFormValue(frm)){
			if(confirm('입력하신 정보로 처리를 하시겠습니까?')){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
//-->
</script>";



$db = new database();

$bank_info=print_shop_bank();

$sql="SELECT op.*,o.user_code,cu.mileage FROM shop_order_payment op, shop_order o left join common_user cu on (o.user_code=cu.code) where op.oid=o.oid and op.opay_ix ='".$opay_ix."' ";
$db->query($sql);
$db->fetch();

$Contents = "<form name='add_price_frm' method='POST' action='add_price.act.php' onsubmit='return addPriceCheck(this)' target='iframe_act'>
<input type='hidden' name='act' value='payment_update'>
<input type='hidden' name='opay_ix' value='".$opay_ix."'>
<input type='hidden' name='oid' value='".$db->dt["oid"]."'>
<input type='hidden' name='payment_price' value='".$db->dt["payment_price"]."'>
<input type='hidden' name='user_code' value='".$db->dt["user_code"]."'>

		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
						<table border='0' width='100%' cellspacing='1' cellpadding='0' >
							<tr>
								<td >
									<table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;' >
										<col width='150px'>
										<col width='*'>
										<tr>
											<td class='input_box_title'> 추가비용 결제상태 </td>
											<td class='input_box_item'>";
												if($db->dt["pay_status"]==ORDER_STATUS_INCOM_READY){
													$Contents .= "
													<input type='radio' name='pay_status'  id='pay_status_".ORDER_STATUS_INCOM_READY."' value='".ORDER_STATUS_INCOM_READY."' ".CompareReturnValue(ORDER_STATUS_INCOM_READY,$db->dt["pay_status"],' checked')." ><label for='pay_status_".ORDER_STATUS_INCOM_READY."'>".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label>
													<input type='radio' name='pay_status'  id='pay_status_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$db->dt["pay_status"],' checked')." ><label for='pay_status_".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label>
													<input type='radio' name='pay_status'  id='pay_status_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' value='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE,$db->dt["pay_status"],' checked')." ><label for='pay_status_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE)."</label>
													<input type='radio' name='pay_status'  id='pay_status_".ORDER_STATUS_LOSS."' value='".ORDER_STATUS_LOSS."' ".CompareReturnValue(ORDER_STATUS_LOSS,$db->dt["pay_status"],' checked')." ><label for='pay_status_".ORDER_STATUS_LOSS."'>".getOrderStatus(ORDER_STATUS_LOSS)."</label>";
												}elseif($db->dt["pay_status"]==ORDER_STATUS_INCOM_COMPLETE){
													$Contents .= "
													<input type='radio' name='pay_status'  id='pay_status_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$db->dt["pay_status"],' checked')." ><label for='pay_status_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label>
													<input type='radio' name='pay_status'  id='pay_status_".ORDER_STATUS_LOSS."' value='".ORDER_STATUS_LOSS."' ".CompareReturnValue(ORDER_STATUS_LOSS,$db->dt["pay_status"],' checked')." ><label for='pay_status_".ORDER_STATUS_LOSS."'>".getOrderStatus(ORDER_STATUS_LOSS)."</label>";
												}else{
													$Contents .= getOrderStatus($db->dt["pay_status"]);
												}
											$Contents .= "
											</td>
										</tr>
										<tr class='tr_pay_status tr_pay_status_".ORDER_STATUS_INCOM_READY." tr_pay_status_".ORDER_STATUS_INCOM_COMPLETE."' style='display:none;'>
											<td class='input_box_title'> 추가비용 결제타입 </td>
											<td class='input_box_item' style='padding:5px;'>";
												
												if($db->dt["pay_status"]==ORDER_STATUS_INCOM_READY){

													if($db->dt["user_code"]!=""){
														$Contents .= "
														<input type='radio' name='method' id='method_".ORDER_METHOD_RESERVE."' value='".ORDER_METHOD_RESERVE."' ".CompareReturnValue(ORDER_METHOD_RESERVE,$db->dt["method"],' checked')." ><label for='method_".ORDER_METHOD_RESERVE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_RESERVE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_RESERVE.".gif' align='absmiddle'></label>";
													}

													$Contents .= "
													<input type='radio' name='method' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$db->dt["method"],' checked')." ><label for='method_".ORDER_METHOD_BANK."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='radio' name='method' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$db->dt["method"],' checked')." ><label for='method_".ORDER_METHOD_CARD."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CARD)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CARD.".gif' align='absmiddle'></label>&nbsp;
													<input type='radio' name='method' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$db->dt["method"],' checked')." ><label for='method_".ORDER_METHOD_VBANK."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='radio' name='method' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$db->dt["method"],' checked')." ><label for='method_".ORDER_METHOD_ICHE."' class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle'></label>&nbsp;
													<input type='radio' name='method' id='method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$db->dt["method"],' checked')." ><label for='method_".ORDER_METHOD_CASH."' class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle'></label>
													<!--input type='radio' name='method' id='method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$db->dt["method"],' checked')." ><label for='method_".ORDER_METHOD_SAVEPRICE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_SAVEPRICE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_SAVEPRICE.".gif' align='absmiddle'></label>&nbsp;-->

													<div class='tr_method tr_method_".ORDER_METHOD_BANK."' style='display:none;margin-top:7px;'>
														<select name='bank'>
															<option value=''>계좌를 선택해주요.</option>";

															for($i=0;$i<count($bank_info);$i++){

																$b_info = $bank_info[$i][bank_name]." ".$bank_info[$i][bank_number]." ".$bank_info[$i][bank_owner];

																$Contents .= "<option value='".$b_info."' ".CompareReturnValue($b_info,$db->dt["bank"],' selected').">".$bank_info[$i][bank_name]." ".$bank_info[$i][bank_number]."</option>";
															}
														$Contents .= "
														</select>
													</div>
													<div class='tr_method tr_method_".ORDER_METHOD_RESERVE."' style='display:none;margin-top:7px;'>
														결제금액 : ".number_format($db->dt["payment_price"])."원 / <input type='checkbox' name='payment_reserve' id='payment_reserve' value='Y' payment_price='".$db->dt["payment_price"]."' mileage='".$db->dt["mileage"]."' ><label for='payment_reserve'>결제하기(보유마일리지 : ".number_format($db->dt["mileage"])."원)</label>
													</div>";
												}else{
													$Contents .= getMethodStatus($db->dt["method"]);
												}
											$Contents .= "
											</td>
										</tr>
										<tr>
											<td class='input_box_title'> 비고 </td>
											<td class='input_box_item'>
												<input type='text' name='memo' class='textbox' id='memo' value='".$db->dt["memo"]."' style='width:90%'>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
			</td>
  		</tr>
		<tr>
			<td colspan=2 align=center style='padding:10px 0px;'>
			<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle>
			</td>
		</tr>
  		</table>
</form>";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "주문관리 > 추가비용 결제상태변경하기";
$P->NaviTitle = "추가비용 결제상태변경하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();