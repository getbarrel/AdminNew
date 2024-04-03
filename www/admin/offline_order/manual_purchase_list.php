<?
include("../class/layout.class");
include("../inventory/inventory.lib.php");
include("../logstory/class/sharedmemory.class");

$goods_setup_info = getBasicSellerSetup($admininfo[company_id]."_goods_multi_price_setup");

$db = new Database;

if($type_div  == ""){
	$type_div  = '14';
}

$db->query("select * from sellertool_site_info where disp='1' ");
$sell_order_from=$db->fetchall();


if($mpt_ix!=""){

	$shmop = new Shared("manual_purchase_tmp");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$data = $shmop->getObjectForKey($mpt_ix);
	$data = unserialize($data);

	extract($data);

	$sql="SELECT loan_price,noaccept_price FROM common_company_detail WHERE company_id='$ci_ix'";
	$db->query($sql);
	$db->fetch();
	$loan_price = $db->dt[loan_price];
	$noaccept_price = $db->dt[noaccept_price];
	$remain_loan_price = $db->dt[loan_price]-$db->dt[noaccept_price];

	$act = "insert";
	$info_type = "select";
	$charger_name = $_SESSION["admininfo"]["charger"];
	$apply_charger_ix = $_SESSION["admininfo"]["charger_ix"];

	//echo $move_company_id;
}else{
	$act = "insert";
	$info_type = "select";
//	$move_status = "MA";
	$charger_name = $_SESSION["admininfo"]["charger"];
	$apply_date = date("Ymd");
	$apply_charger_ix = $_SESSION["admininfo"]["charger_ix"];
	//$now_company_id = $_SESSION["admininfo"]["company_id"];
}


if(empty($order_from)){
	if($pre_type=="inventory"){
		$order_from="self";
	}else{
		$order_from="offline";
	}
}

//echo $_SESSION["admininfo"]["charger_name"];

$Contents ="
<form  name='input_frm' method='post' onsubmit=\"return checkInputForm(this)\" action='../offline_order/manual_purchase.act.php' target='act' >
<input type=hidden name=act value='".$act."'>
<input type=hidden name=mmode value='".$mmode."'>
<input type='hidden' name='_mpt_ix' id='mpt_ix' value='".$_GET[mpt_ix]."'>

<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("수동수주서 작성", "수동수주서 > 수동수주서 작성")."</td>
</tr>

<tr >
	<td colspan=2 width='100%' valign=top style='padding-top:3px;'>";

$vdate = date("Y-m-d", time());
$today = date("Y-m-d", time());
$tommorw = date("Y-m-d", time()+84600);
$vyesterday = date("Y-m-d", time()-84600);
$voneweekago = date("Y-m-d", time()-84600*7);
$vtwoweekago = date("Y-m-d", time()-84600*14);
$vfourweekago = date("Y-m-d", time()-84600*28);
$vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

$Contents .= "

	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center height='120px'>
		<col width='50%'>
		<col width='50%'>

		<tr>
			<td colspan='2' height='25' style='padding:5px 0px;'>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>수주서 정보입력</b>
			</td>
		</tr>
		<tr>
			<td colspan='2' style='padding:0px 0px;'>
				<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
						<col width='15%'>
						<col width='35%'>
						<col width='15%'>
						<col width='35%'>
						<tr height='30'>
							<td class='input_box_title' ><b>판매처 / 수주서 요청일</b> <img src='".$required3_path."'></td>
							<td class='input_box_item' >
								<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
									<col width=120>
									<col width=*>
									<tr>
										<td>
											<select name='order_from' style='height:25px;'>
												<option value='self' ".CompareReturnValue('self',$order_from,' selected').">자체쇼핑몰</option>
												<option value='offline' ".CompareReturnValue('offline',$order_from,' selected').">오프라인 영업</option>
												<option value='pos' ".CompareReturnValue('pos',$order_from,' selected').">POS</option>";
												/*
												for($i=0;$i<count($sell_order_from);$i++){
													$Contents .= "
													<option value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' selected').">".$sell_order_from[$i][site_name]."</option>";
												}
												*/

											$Contents .= "
											</select>
										</td>
										<td nowrap>";
										if($info_type == "text"){
											$Contents .= "".$apply_date."
											<input type='hidden' name='apply_date' class='textbox' value='".$apply_date."'  validation='true' title='요청일'  >";
										}else{
											$Contents .= "
											<input type='text' name='apply_date' class='textbox' value='".$apply_date."' style='".($info_type == "text" ? "border:0px;":"")."height:20px;width:70px;text-align:center;' id='end_datepicker' validation='true' title='요청일' ".($info_type == "text" ? "readonly ":"").">";
										}
										$Contents .= "
										</td>
									</tr>
								</table>
							</td>
							<td class='input_box_title' ><b>수주서 요청자</b> <img src='".$required3_path."'></td>
							<td class='input_box_item' >
								".CompayChargerSearch($_SESSION["admininfo"]["company_id"] ,$apply_charger_ix,"",$info_type)."
							</td>
						</tr>
						<tr height='30'>
							<td class='input_box_title' ><b>작성자</b> <img src='".$required3_path."'></td>
							<td class='input_box_item' >  ".$charger_name." </td>
							<td class='input_box_title'>출고 유형 <img src='".$required3_path."'> </td>
							<td class='input_box_item' >";

		$Contents .= "".selectType('2', $type_div ,$h_type,'h_type',$info_type,"true").""; // type_div : 5 인건 창고이동

		$Contents .= "
							</td>
						</tr>
						<tr height='30'>
							<td class='input_box_title' ><b>매출처 <img src='".$required3_path."'></b></td>
							<td class='input_box_item'>
								".SelectSupplyCompany($ci_ix,"ci_ix","select", "true", 2,'estimate_order')."
							</td>
							<td class='input_box_title' ><b>회원 ID</b></td>
							<td class='input_box_item' ><!--id='member_idy'-->
								<input type='hidden' size='25' name='rname' id='rname' class='textbox' value='".$rname."' validation='true' title='주문자 이름'>
								<input type='hidden' size='25' name='r_mail' id='r_mail' class='textbox' value='".$r_mail."' validation='false' title='주문자 메일'>

								<!--input type='hidden' size='5' maxlength='3' name='tel1' id='tel1' class='textbox' value='".$tel1."' validation='false' title='주문자 전화'>
								<input type='hidden' size='5' maxlength='4' name='tel2' id='tel2' class='textbox' value='".$tel2."' validation='false' title='주문자 전화'>
								<input type='hidden' size='5' maxlength='4' name='tel3' id='tel3' class='textbox' value='".$tel3."' validation='false' title='주문자 전화'>
								<input type='hidden' size='5' maxlength='3' name='pcs1' id='pcs1' class='textbox' value='".$pcs1."' validation='false' title='주문자 핸드폰'>
								<input type='hidden' size='5' maxlength='4' name='pcs2' id='pcs2' class='textbox' value='".$pcs2."' validation='false' title='주문자 핸드폰'>
								<input type='hidden' size='5' maxlength='4' name='pcs3' id='pcs3' class='textbox' value='".$pcs3."' validation='false' title='주문자 핸드폰'-->

								<input type='hidden' class='textbox' name='com_zip' id='com_zip' size='15' maxlength='15' value='".$com_zip."' validation='false' title='사업자주소 우편번호' readonly>
								<input type='hidden' name='com_addr1'  id='com_addr1' value='".$com_addr1."' validation='false' title='사업자 주소' size=50 class='textbox'  style='width:450px'>
								<input type='hidden' name='com_addr2'  id='com_addr2'  value='".$com_addr2."' validation='false' title='사업자 주소' size=70 class='textbox'  style='width:450px'>

								<input type='text' size='25' name='member_id' id='member_id' class='textbox' value='".$member_id."' validation='false' title='주문자아이디'>
							</td>
						</tr>
						<tr height='30'>
							<td class='input_box_title' ><b>사업자번호</b></td>
							<td class='input_box_item'>
								<input type=text class='textbox numeric' name='com_number1' value='".$com_number1." ' id='com_number1' maxlength='3' style='width:50px;text-align:center;'> - 
								<input type=text class='textbox numeric' name='com_number2' value='".$com_number2." ' id='com_number2' maxlength='2' style='width:50px;text-align:center;'> - 
								<input type=text class='textbox numeric' name='com_number3' value='".$com_number3." ' id='com_number3' maxlength='5' style='width:50px;text-align:center;'>
							</td>
							<td class='input_box_title' ><b>여신한도 / 잔여한도</b></td>
							<td class='input_box_item' ><!-- common_sellelr_detail.loan_price 여신한도 -->
								<span id='loan_price_text'>".number_format($loan_price)."</span> 원  &nbsp;/&nbsp;
								<span id='remain_loan_price_text_1' class='red'>".number_format($remain_loan_price)." 원</span> &nbsp;&nbsp;&nbsp;
								<img src='../images/".$admininfo["language"]."/btn_noaccept_modify.gif' border=0 align=absmiddle onclick=\"if($('#ci_ix').val()!=''){window.open('/admin/basic/seller.add.php?info_type=basic&company_id='+$('#ci_ix').val(),'')}else{alert('매출처를 먼저 선택해주세요.');}\" style='cursor:pointer' /> 
							</td>
						</tr>
						<tr height='30'>
							<td class='input_box_title' ><b>대표번화번호</b></td>
							<td class='input_box_item' >
								<!--input type=text class='textbox numeric' name='com_phone1' value='".$com_phone1." ' id='com_phone1' maxlength='3' style='width:50px;text-align:center;'> - 
								<input type=text class='textbox numeric' name='com_phone2' value='".$com_phone2." ' id='com_phone2' maxlength='4' style='width:50px;text-align:center;'> - 
								<input type=text class='textbox numeric' name='com_phone3' value='".$com_phone3." ' id='com_phone3' maxlength='4' style='width:50px;text-align:center;'-->

								<input type=text class='textbox numeric' name='tel1' value='".$tel1." ' id='tel1' maxlength='3' style='width:50px;text-align:center;'> - 
								<input type=text class='textbox numeric' name='tel2' value='".$tel2." ' id='tel2' maxlength='4' style='width:50px;text-align:center;'> - 
								<input type=text class='textbox numeric' name='tel3' value='".$tel3." ' id='tel3' maxlength='4' style='width:50px;text-align:center;'>
							</td>
							<td class='input_box_title' ><b>담당자 핸드폰번호</b></td>
							<td class='input_box_item' >
								<!--input type=text class='textbox numeric' name='com_mobile1' value='".$com_mobile1." ' id='com_mobile1' maxlength='3' style='width:50px;text-align:center;'> - 
								<input type=text class='textbox numeric' name='com_mobile2' value='".$com_mobile2." ' id='com_mobile2' maxlength='4' style='width:50px;text-align:center;'> - 
								<input type=text class='textbox numeric' name='com_mobile3' value='".$com_mobile3." ' id='com_mobile3' maxlength='4' style='width:50px;text-align:center;'-->

								<input type=text class='textbox numeric' name='pcs1' value='".$pcs1." ' id='pcs1' maxlength='3' style='width:50px;text-align:center;'> - 
								<input type=text class='textbox numeric' name='pcs2' value='".$pcs2." ' id='pcs2' maxlength='4' style='width:50px;text-align:center;'> - 
								<input type=text class='textbox numeric' name='pcs3' value='".$pcs3." ' id='pcs3' maxlength='4' style='width:50px;text-align:center;'>
							</td>
						</tr>
				</table>";
$Contents .= "
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr >
	<td colspan=2 width='100%' valign=top style='padding-top:3px;'>
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center height='120px'>
		<col width='50%'>
		<col width='50%'>
		<tr>
			<td colspan='2' height='25' style='padding:5px 0px;'>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>배송지 정보</b>
			</td>
		</tr>
		<tr>
			<td colspan='2' style='padding:0px 0px;'>
				<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
				<col width='15%'>
				<col width='35%'>
				<col width='15%'>
				<col width='35%'>
				<tr height='30'>
					<td class='input_box_title' ><b>수취인 이름 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' colspan='3'>
						<input type='text' size='25' name='bname' id='bname' class='textbox' value='".$bname."' validation='true' title='수취인 이름'>
						<input type='radio' name='new_info' id='new_info_1' value='1' title='신규입력'> <label for='new_info_1'> 신규입력 </label> 
						<input type='radio' name='new_info' id='new_info_2' value='2' title='주문정보와 동일'> <label for='new_info_2'> 주문정보와 동일 </label>
						<input type='radio' name='new_info' id='new_info_3' value='3' title='납품처 검색'> <label for='new_info_3'> 납품처 검색 </label> 
					</td>
					</td>
				</tr>
				<tr height='30'>
					<td class='input_box_title' ><b>수취인 전화</b></td>
					<td class='input_box_item' >
						<input type='text' size='5' maxlength='3' name='bmember_phone1' id='bmember_phone1' class='textbox' value='".$bmember_phone1."' validation='false' title='수취인 핸드폰'> - 
						<input type='text' size='5' maxlength='4' name='bmember_phone2' id='bmember_phone2' class='textbox' value='".$bmember_phone2."' validation='false' title='수취인 핸드폰'> - 
						<input type='text' size='5' maxlength='4' name='bmember_phone3' id='bmember_phone3' class='textbox' value='".$bmember_phone3."' validation='false' title='수취인 핸드폰'>
					</td>
					<td class='input_box_title' ><b>수취인 핸드폰 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' >
						<input type='text' size='5' maxlength='3' name='bmember_pcs1' id='bmember_pcs1' class='textbox' value='".$bmember_pcs1."' validation='true' title='수취인 핸드폰'> - 
						<input type='text' size='5' maxlength='4' name='bmember_pcs2' id='bmember_pcs2' class='textbox' value='".$bmember_pcs2."' validation='true' title='수취인 핸드폰'> - 
						<input type='text' size='5' maxlength='4' name='bmember_pcs3' id='bmember_pcs3' class='textbox' value='".$bmember_pcs3."' validation='true' title='수취인 핸드폰'>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> <b>배달주소  <img src='".$required3_path."'> </b></td>
					<td class='input_box_item' colspan=3>
						<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
						<div id='input_address_area' ><!--style='display:none;'-->
						<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
							<col width='80px'>
							<col width='100px'>
							<col width='*'>
							<tr>
								<td height=26>
									<input type='text' class='textbox' name='zip1' id='zipcode1' size='7' maxlength='7' value='".$db->dt[zip]."' validation='true' title='배달주소 우편번호' readonly>
								</td>
								<td style='padding:1px 0 0 5px;'>
									<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1');\" style='cursor:pointer;'>
								</td>
								<td>
									<input type ='checkbox' name='com_addr' id='com_addr' value=''> *<label for='com_addr'> 사업자 주소와 동일 </label>
								</td>
							</tr>
							<tr>
								<td height=26 colspan='3'>
									<input type=text name='addr1'  id='addr1' value='".$addr1."' size=50 class='textbox' validation='true' title='배달주소' style='width:450px'>
								</td>
							</tr>
							<tr>
								<td height=26 colspan='3'>
									<input type=text name='addr2'  id='addr2'  value='".$addr2."' size=70 class='textbox' validation='false' title='배달주소' style='width:450px'> (상세주소)
								</td>
							</tr>
						</table>
						</div>
					</td>
				</tr>
				<tr bgcolor=#ffffff height=110>
					<td class='input_box_title' ><b>배송 요청사항</b></td>
					<td class='input_box_item'  colspan=3>";
						if($info_type == "text"){
							$Contents .= "".$etc."<input type=hidden class='textbox' name='etc' value='".$etc." ' id=order_etc style='width:90%'>";
						}else{
							$Contents .= "
							<textarea type=text class='textbox' name='delivery_message' style='width:98%;height:85px;padding:2px;'>".$delivery_message."</textarea>";
						}
						$Contents .= "
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
	</td>
</tr>


<tr>
	<td  height='25' style='padding:10px 0px;'>
		<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>결제 정보 입력</b>
	</td>
	<td align=right>";
	if($move_status == "" || $move_status == "MA"){
		//$Contents .= "<a href='#'><img src='../images/".$admininfo["language"]."/btc_goods_add.gif' border='0' align='absmiddle' title='거래처수정'  style='cursor:pointer;'></a>";
	}
	$Contents .= "
	</td>
</tr>

<tr>
	<td colspan='2' height=300 style='vertical-align:top;'>
		<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
			<col width='15%'>
			<col width='35%'>
			<col width='15%'>
			<col width='35%'>
			<tr height='30'>
				<td class='input_box_title' ><b>결제상태</b></td>
				<td class='input_box_item' colspan='3'>
					<input type='radio' name='type' id='type_".ORDER_STATUS_INCOM_READY."' value='".ORDER_STATUS_INCOM_READY."' ".(($type == ORDER_STATUS_INCOM_READY ) ? ' checked':'')." ><label for='type_".ORDER_STATUS_INCOM_READY."'> ".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label>&nbsp;&nbsp;
					<input type='radio' name='type' id='type_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".(($type == ORDER_STATUS_INCOM_COMPLETE ) ? ' checked':'')." ><label for='type_".ORDER_STATUS_INCOM_COMPLETE."'>  ".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label>&nbsp;&nbsp;
					<input type='radio' name='type' id='type_".ORDER_STATUS_DEFERRED_PAYMENT."' value='".ORDER_STATUS_DEFERRED_PAYMENT."' ".(($type == ORDER_STATUS_DEFERRED_PAYMENT || $type =="") ? ' checked':'')."  ><label for='type_".ORDER_STATUS_DEFERRED_PAYMENT."'>  ".getOrderStatus(ORDER_STATUS_DEFERRED_PAYMENT)."</label>
				</td>
			</tr>
			<tr height='30' id='order_method' class='order_method'>
				<td class='input_box_title' ><b>결제타입</b></td>
				<td class='input_box_item' colspan='3'>

					<input type='radio' name='method' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".(($method == ORDER_METHOD_BANK || $method == "") ? ' checked':'')." ><label for='method_".ORDER_METHOD_BANK."'>".getMethodStatus(ORDER_METHOD_BANK)."</label>&nbsp;&nbsp;

					<input type=radio name='method' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".(($method == ORDER_METHOD_VBANK) ? ' checked':'')."><label for='method_".ORDER_METHOD_VBANK."'> ".getMethodStatus(ORDER_METHOD_VBANK)."</label>&nbsp;&nbsp;

					<input type=radio name='method' id='method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".(($method == ORDER_METHOD_CASH) ? ' checked':'')."><label for='method_".ORDER_METHOD_CASH."'> ".getMethodStatus(ORDER_METHOD_CASH)."</label>&nbsp;&nbsp;

					<input type=radio name='method' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".(($method == ORDER_METHOD_CARD) ? ' checked':'')."><label for='method_".ORDER_METHOD_CARD."'> ".getMethodStatus(ORDER_METHOD_CARD)."</label>&nbsp;&nbsp;
				</td>
			</tr>
			<tr height='30' id='bank' class='bank'>
				<td class='input_box_title'>무통장계좌 <img src='".$required3_path."'></td>
				<td class='input_box_item' >
				".BankInfo($bank_ix,'true')."
				</td>
				<td class='input_box_title' ><b>무통장 입금예정일</b></td>
				<td class='input_box_item' >
					<input type=text class='textbox numeric' name='bank_input_date' value='".$bank_input_date." ' id=bank_input_date style='width:100px;text-align:center;'>
				</td>
			</tr>

			<tr height='30' id='vbank' class='vbank' style='display : none'>
				<td class='input_box_title'>가상계좌 정보 <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>
				예금주 <input type=text class='textbox ' name='vbank_holder' value='".$vbank_holder."' id='vbank_holder' style='width:100px' > &nbsp;&nbsp;
				은행명 <input type=text class='textbox' name='vbank_name' value='".$vbank_name."' id='vbank_name' style='width:150px'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				가상계좌 <input type=text class='textbox numeric' name='vbank_num1' value='".$vbank_num1."' id='vbank_num1' style='width:100px'> - 
						<input type=text class='textbox numeric' name='vbank_num2' value='".$vbank_num2."' id='vbank_num2' style='width:100px'> -
						<input type=text class='textbox numeric' name='vbank_num3' value='".$vbank_num3."' id='vbank_num3' style='width:100px'>
				</td>
			</tr>

			<!--
			<tr height='30' id='cash' class='cash'  style='display : none'>
				<td class='input_box_title'>현금 정보 <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>
				현금
				</td>
			</tr>-->

			<tr height='30' id='card' class='card'  style='display : none'>
				<td class='input_box_title'>카드결제 정보 <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>
					카드사명 <input type=text class='textbox numeric' name='card_name' value='".$card_name."' id=card_name style='width:100px'> &nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					카드번호 <input type=text class='textbox numeric' name='card_num1' value='".$card_num1."' id='card_num1' style='width:100px'> - 
							<input type=text class='textbox numeric' name='card_num2' value='".$card_num2."' id='card_num2' style='width:100px'> -
							<input type=text class='textbox numeric' name='card_num3' value='".$card_num3."' id='card_num3' style='width:100px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					카드유효기간 <input type=text class='textbox numeric' name='card_expiry1' value='".$card_expiry1."' id=card_expiry1 style='width:50px'> / 
							<input type=text class='textbox numeric' name='card_expiry2' value='".$card_expiry2."' id=card_expiry2 style='width:50px'>

				</td>
			</tr>
			<tr height='30' id='move_status_tr'>
				<td class='input_box_title'>배송타입 <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>
					<input type=radio name='delivery_method' id='delivery_method_1' value='1' ".(($delivery_method == "1" || $delivery_method == "") ? ' checked':'')."><label for='delivery_method_1'> 택배</label>&nbsp;&nbsp;
					<input type=radio name='delivery_method' id='delivery_method_2' value='2' ".(($delivery_method == "2") ? ' checked':'')."><label for='delivery_method_2'> 화물(트럭)</label>&nbsp;&nbsp;
					<input type=radio name='delivery_method' id='delivery_method_3' value='3' ".(($delivery_method == "3") ? ' checked':'')."><label for='delivery_method_3'> 직배송(회사)</label>&nbsp;&nbsp;
					<input type=radio name='delivery_method' id='delivery_method_4' value='4' ".(($delivery_method == "4") ? ' checked':'')."><label for='delivery_method_4'> 방문수령</label>&nbsp;&nbsp;
				</td>		
			</tr>
			<tr height='30' id='move_status_tr'>
				<td class='input_box_title'>배송비 <img src='".$required3_path."'></td>
				<td class='input_box_item' >
					<input type=radio name='delivery_basic_policy' id='delivery_basic_policy_1' value='1' ".(($delivery_basic_policy == "1" || $delivery_basic_policy == "") ? ' checked':'')."><label for='delivery_basic_policy_1'> 선불</label>&nbsp;&nbsp;
					<input type=text class='textbox numeric' name='delivery_price' value='".($_GET[mpt_ix] !="" ? $delivery_price : "0")." ' id=delivery_price style='width:100px'> 원&nbsp;&nbsp;
					<input type=radio name='delivery_basic_policy' id='delivery_basic_policy_2' value='2' ".(($delivery_basic_policy == "2") ? ' checked':'')."><label for='delivery_basic_policy_2'> 착불</label>&nbsp;&nbsp;
					<!--input type=radio name='delivery_basic_policy' id='delivery_basic_policy_3' value='3' ".(($delivery_basic_policy == "3") ? ' checked':'')."><label for='delivery_basic_policy_3'> 무료</label>&nbsp;&nbsp;-->
				</td>
				<td class='input_box_title' ><b>발송예정일</b></td>
				<td class='input_box_item' >
					<input type=text class='textbox numeric' name='shipping_date' value='".$shipping_date." ' id=shipping_date style='width:100px;text-align:center;'>
				</td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap> 증빙서류 정보</td>
				<td class='input_box_item' nowrap colspan='3'>
					<table width='100%' border='0'>
						<tr>
							<td><input type='radio' name='voucher_div' id='voucher_div_1' style='border:0px;' value='1' ".(($voucher_div == "1") ? ' checked':'')." ><label for='voucher_div_1'> 개인소득공제</label>
							</td>
						</tr>
						<tr>
							<td>
								<table width='600' border='0' style='padding-left:10px;'>
								<tr>
									<td width='20%'>
										<input type='radio' name='voucher_num_div' id='voucher_num_div_1' style='border:0px;' value='1'  ".(($voucher_num_div == "1") ? ' checked':'')."><label for='voucher_num_div_1'> 휴대폰번호입력</label>
									</td>
									<td width='*'>
										<input type='text' class='textbox' name='voucher_phone1' id='voucher_phone1' size='3' maxlength='3' value='".$voucher_phone1."' style='width:30px;'> -
										<input type='text' class='textbox' name='voucher_phone2' id='voucher_phone2' size='4' maxlength='4' value='".$voucher_phone2."'  style='width:40px;'> -
										<input type='text' class='textbox' name='voucher_phone3' id='voucher_phone3' size=4' maxlength='4' value='".$voucher_phone3."' style='width:40px;'>
									</td>
									<td width='30%'>
										사용자명 <input type='text' class='textbox' name='phone_voucher_name' id='phone_voucher_name'  value='".$phone_voucher_name."'  style='width:60px;'>
									</td>
								</tr>
								<!--tr>
									<td>
										<input type='radio' name='voucher_num_div' id='voucher_num_div_2' style='border:0px;' value='2' ><label for='voucher_num_div_2'> 현금영수증 카드번호</label>
									</td>
									<td>
										<input type='text' class='textbox' name='voucher_card1' id='voucher_card1' size='5' maxlength='5' value='".$voucher_card1."' style='width:30px;'> -
										<input type='text' class='textbox' name='voucher_card2' id='voucher_card2' size='5' maxlength='5' value='".$voucher_card2."' style='width:40px;'> -
										<input type='text' class='textbox' name='voucher_card3' id='voucher_card3' size='5' maxlength='5' value='".$voucher_card3."' style='width:40px;'> - 
										<input type='text' class='textbox' name='voucher_card4' id='voucher_card4' size='5' maxlength='5' value='".$voucher_card4."' style='width:40px;'>
									</td>
									<td>
										사용자명 <input type='text' class='textbox' name='card_voucher_name' id='card_voucher_name' value='".$card_voucher_name."'  style='width:60px;'>
									</td>
								</tr-->
								</table>
							</td>
						</tr>
						<!--
						<tr>
							<td>
								<input type='radio' name='voucher_div' id='voucher_div_2' style='border:0px;' value='2' ".(($voucher_div == "2") ? ' checked':'')."><label for='voucher_div_2'> 지출증빙</label>
								<input type='text' class='textbox' name='expense_num1' id='expense_num1' size='5' maxlength='5' value='".$expense_num1."' style='width:40px;'> -
								<input type='text' class='textbox' name='expense_num2' id='expense_num2' size='5' maxlength='5' value='".$expense_num2."' style='width:40px;'> -
								<input type='text' class='textbox' name='expense_num3' id='expense_num3' size='5' maxlength='5' value='".$expense_num3."' style='width:40px;'>
							</td>
						</tr>-->
						<tr>
							<td>
								<input type='radio' name='voucher_div' id='voucher_div_3' style='border:0px;' value='3' ".(($voucher_div == "3" || $voucher_div == "") ? ' checked':'')."><label for='voucher_div_3'> 세금계산서 발급(등록되어 있는 사업자로 발급)</label>
							</td>
						</tr>
						<tr>
							<td>
								<table width='100%' border='0' style='padding-left:10px;'>
								<tr>
									<td>
										<input type='radio' name='certificate_yn' id='certificate_yn_1' style='border:0px;' value='1' ".(($certificate_yn == "1") ? ' checked':'')."><label for='certificate_yn_1'> 결제완료 후 즉시 발급</label>
									</td>
								</tr>
								<tr>
									<td>
										<input type='radio' name='certificate_yn' id='certificate_yn_2' style='border:0px;' value='2' ".(($certificate_yn == "2" || $certificate_yn == "") ? ' checked':'')."><label for='certificate_yn_2'> 기간별 발급(관리자 수동발급)</label>
									</td>
								</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
</tr>";

$Contents .= "
<tr>
	<td  height='25' style='padding:10px 0px;'>
		<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>수동수주 상품</b>
	</td>
	<td align=right>
		<select name='wms_discount_type' id='wms_discount_type' style=''>
			<option value='retail' ".($wms_discount_type == 'retail'?'selected':'').">소매</option>
			<option value='whole' ".($wms_discount_type == 'whole'?'selected':'').">도매</option>
		</select>
		<select name='retail_wms_discount_type' id='retail_wms_discount_type' style='display:none;'>
			<option value='' ".($retail_wms_discount_type == ''?'selected':'').">선택해주세요</option>
			<option value='a' ".($retail_wms_discount_type == 'a'?'selected':'').">".$goods_setup_info[batch][rate][R][a_name]."</option>
			<option value='b' ".($retail_wms_discount_type == 'b'?'selected':'').">".$goods_setup_info[batch][rate][R][b_name]."</option>
			<option value='c' ".($retail_wms_discount_type == 'c'?'selected':'').">".$goods_setup_info[batch][rate][R][c_name]."</option>
			<option value='d' ".($retail_wms_discount_type == 'd'?'selected':'').">".$goods_setup_info[batch][rate][R][d_name]."</option>
			<option value='e' ".($retail_wms_discount_type == 'e'?'selected':'').">".$goods_setup_info[batch][rate][R][e_name]."</option>
		</select>
		<select name='whole_wms_discount_type' id='whole_wms_discount_type' style='display:none;'>
			<option value='' ".($whole_wms_discount_type == ''?'selected':'').">선택해주세요</option>
			<option value='a' ".($whole_wms_discount_type == 'a'?'selected':'').">".$goods_setup_info[batch][rate][W][a_name]."</option>
			<option value='b' ".($whole_wms_discount_type == 'b'?'selected':'').">".$goods_setup_info[batch][rate][W][b_name]."</option>
			<option value='c' ".($whole_wms_discount_type == 'c'?'selected':'').">".$goods_setup_info[batch][rate][W][c_name]."</option>
			<option value='d' ".($whole_wms_discount_type == 'd'?'selected':'').">".$goods_setup_info[batch][rate][W][d_name]."</option>
			<option value='e' ".($whole_wms_discount_type == 'e'?'selected':'').">".$goods_setup_info[batch][rate][W][e_name]."</option>
		</select>
		<img src='../images/btn/btn_order_relation01.gif' alt='일괄 변경' onclick=\"discount_type_apply('all')\" align='absmiddle' style='cursor:pointer;'> &nbsp;&nbsp;&nbsp;
		
	";
	if($move_status == "" || $move_status == "MA"){
		$Contents .= "<a href=\"javascript:PopGoodsSelect();\"><img src='../images/".$admininfo["language"]."/btc_goods_add.gif' border='0' align='absmiddle'  style='cursor:pointer;'></a>
		<input type=text class='textbox number' value='바코드 입력&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' id='barcode' onclick=\"Submit_bool=false;$(this).val('')\">";
	}
	$Contents .= "
	</td>
</tr>
<tr>
	<td colspan='2'  style='vertical-align:top;'>";

$Contents .= "
	<table cellpadding=3 cellspacing=0 border=0 bgcolor=#ffffff width=100% onselect='return false;' align=center class='list_table_box' id='warehouse_move_apply_list'>
				<col width='30px' >
				<col width='*' >
				<col width=5% >
				<col width=4% >
				<col width=7% >
				<col width=7% >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<col width=5% >
				<col width=9% >
				<col width=8% >
				<col width=9% >
				<col width=5% >
				<tr align=center height=30 style='font-weight:bold;' >
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.input_frm)'></td>
					<td class=m_td>품목정보</td>
					<td class=m_td>부가세</td>
					<td class=m_td>재고</td>
					<td class=m_td nowrap>단가<br>(VAT 별도)</td>
					<td class=m_td>단가<br>(VAT 포함)</td>
					<td class=m_td>세액</td>
					<td class='m_td'>최근단가</td>
					<td class='m_td' nowrap>수량</td>
					<td class='m_td inner_warehouse_move' nowrap>개별단가<br>(VAT 별도)</td>
					<td class='m_td inner_warehouse_move' nowrap>공급가액<br>(VAT 포함)</td>
					<td class='m_td'>세액</td>
					<td class='m_td'>판매가</td>
					<td class='e_td' nowrap>할인률</td>
				</tr>
	";


if($move_status == "" || $move_status == "MA"){
	$goods_select_str = " ondblclick=\"javascript:PopGoodsSelect();\"  style='cursor:pointer;'  ";//class='helpcloud' help_height='35' help_html='더블클릭시 품목을 선택할 수 있는 창이 노출되게 됩니다.' 
}else{
	$goods_select_str = "";
}

//print_r($manual_orderinfo);

if(count($manual_orderinfo)){
	for($i=0;$i<count($manual_orderinfo);$i++){
		
		
		$sql = "select data.*, 
		(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name ,
		(select psprice/1.1 as lately_price from shop_order_detail od where od.pcode = data.gu_ix  and order_from = 'offline' and oid in (select oid  from shop_order where user_com_id= '".$ci_ix."' ) order by regdate limit 1) as lately_price 
		from 
			(select g.cid,g.gname, g.gid, g.gcode, g.admin, g.ci_ix, g.surtax_div, ips.pi_ix, pi.place_name, ips.ps_ix,  pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit , gu.buying_price, gu.wholesale_price, gu.sellprice, ips.vdate, ips.expiry_date,gu.offline_wholesale_price ,gu.gu_ix
			from inventory_goods g 
			right join inventory_goods_unit gu on g.gid =gu.gid
			left join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
			left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
			left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
			where gu.gid = '".$manual_orderinfo[$i][gid]."' and gu.unit = '".$manual_orderinfo[$i][unit]."'
			 group by g.gid , gu.unit, ips.pi_ix, ips.expiry_date
		) data";
		$db->query($sql);
		$db->fetch();
		

		if($surtax_div == '3' || $surtax_div == '4'){
			$wholesale_price = $db->dt[wholesale_price];
			$tax_price = 0;
			$order_tax = 0;
			$total_price = $manual_orderinfo[$i][sellprice] * $manual_orderinfo[$i][pcount];
			$discount = floor((1-$manual_orderinfo[$i][sellprice]/$db->dt[wholesale_price])*100);
		}else{
			$wholesale_price = $db->dt[wholesale_price] - floor($db->dt[wholesale_price]/11);
			$tax_price = floor($db->dt[wholesale_price]/11);
			$order_tax = floor($manual_orderinfo[$i][sellprice]/10*$manual_orderinfo[$i][pcount]);
			$total_price = $manual_orderinfo[$i][sellprice] + ($manual_orderinfo[$i][sellprice]/10* $manual_orderinfo[$i][pcount]);
			$discount = floor((1-$manual_orderinfo[$i][sellprice]/$wholesale_price)*100);
		}

		$Contents .="
				<tr height=30 depth=1 >
					<td align=center>
						<input type=checkbox class='nonborder select_gid' id='select_gid'  name='manual_orderinfo[$i][select_gid]' value='".$db->dt[gid]."|".$db->dt[unit]."'>
						<input type='hidden'  class='textbox numeric' name='manual_orderinfo[$i][gid]' id='gid' value='".$db->dt[gid]."' >
						<input type=hidden class='textbox numeric' name='manual_orderinfo[$i][unit]' id='unit' value='".$db->dt[unit]."'>
						<input type=hidden class='textbox numeric' name='manual_orderinfo[$i][standard]' id='standard' value='".$db->dt[standard]."'>
						<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[$i][surtax_div]' id='surtax_div' value='".$db->dt[surtax_div]."' title='부가세'>
						<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[$i][stock]' id='stock' value='".$db->dt[stock]."' title='재고'>
						<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[$i][wholesale_price]' id='wholesale_price' value='".$wholesale_price."' title='단가vat 별도'>
						<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[$i][psprice]' id='psprice' value='".$db->dt[wholesale_price]."' title='단가 vat 포함'>
						<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[$i][tax_price]' id='tax_price' value='".$tax_price."' title='세액'>
						<input type=hidden class='textbox numeric sellprice' name='manual_orderinfo[$i][total_price]' id='total_price' value='".$total_price."' size=10 title='공급가액(vat 포함)'>
						<input type=hidden class='textbox numeric amount' name='manual_orderinfo[$i][order_tax]' id='order_tax' value='".$order_tax."' title='세액'>
						<input type=hidden class='textbox numeric amount' name='manual_orderinfo[$i][discount]' id='discount' value='".$discount."' title='할인률'>
					</td>
					<td style='padding:3px;' nowrap>
						<span id='gid_text'>".$db->dt[gid]."</span> <span id='unit_text'>".getUnit($db->dt[unit], "basic_unit","","text")."</span> <br/>
						<b id='gname'>".$db->dt[gname]."</b> <br/>
						<span id='standard_text'>".$db->dt[standard]."</span>
					</td>
					<td align=center><span id='surtax_text'>".getSurTaxDiv($db->dt[surtax_div], "surtax_div","","text")."</span></td>
					<td align=center><span class='stock' id='td_stock'>".$db->dt[stock]."</span></td>
					<td align=center id='td_wholesale_price'>".number_format($wholesale_price)." 원</td>
					<td align=center id='td_psprice'>".number_format($db->dt[wholesale_price])." 원</td>
					<td align=center id='td_tax_price'>".number_format($tax_price)." 원</td>
					<td align=center id='td_lately_price' >".($db->dt[lately_price] ? number_format($db->dt[lately_price])." 원" : "-")."</td>
					<td align=center class='inner_warehouse_move' id='td_pcount'>
						<input type=text class='textbox point_color numeric amount' name='manual_orderinfo[$i][pcount]' onkeyup=\"calcurate_maginrate($(this).closest('tr'))\" validation='true' id='pcount' value='".$manual_orderinfo[$i][pcount]."' size=4 title='수량'> 
					</td>
					<td align=center class='inner_warehouse_move ' nowrap>
						<input type=text class='textbox point_color numeric sellprice' name='manual_orderinfo[$i][sellprice]' onkeyup=\"calcurate_maginrate($(this).closest('tr'))\" validation='true' id='sellprice' value='".$manual_orderinfo[$i][sellprice]."' size=10 title='단가(vat 별도)'> 원
					</td>
					<td align=center class='inner_warehouse_move' id='td_total_price'>".number_format($total_price)." 원</td>
					<td align=center class='inner_warehouse_move' id='td_order_tax'>".number_format($order_tax)." 원</td>
					<td align=center class='inner_warehouse_move' id='td_order_price'>".number_format($total_price-$order_tax)." 원</td>
					<td align=center class='inner_warehouse_move' id='td_discount'>".$discount."%</td>
				</tr>";
	}
}else{

	$Contents .="
				<tr height=30 depth=1 ".$goods_select_str."   >
					<td align=center>
						<input type=checkbox class='nonborder select_gid' id='select_gid'  name='manual_orderinfo[0][select_gid]' value=''>
						<input type='hidden'  class='textbox numeric' name='manual_orderinfo[0][gid]' id='gid' value='' >
						<input type=hidden class='textbox numeric' name='manual_orderinfo[0][unit]' id='unit' value=''>
						<input type=hidden class='textbox numeric' name='manual_orderinfo[0][standard]' id='standard' value=''>
						<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[0][surtax_div]' id='surtax_div' value='' title='부가세'>
						<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[0][stock]' id='stock' value='' title='재고'>
						<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[0][wholesale_price]' id='wholesale_price' value='' title='단가vat 별도'>
						<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[0][psprice]' id='psprice' value='' title='단가 vat 포함'>
						<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[0][tax_price]' id='tax_price' value='' title='세액'>
						<input type=hidden class='textbox numeric sellprice' name='manual_orderinfo[0][total_price]' id='total_price' value='' size=10 title='공급가액(vat 포함)'>
						<input type=hidden class='textbox numeric amount' name='manual_orderinfo[0][order_tax]' id='order_tax' value='' title='세액'>
						<input type=hidden class='textbox numeric amount' name='manual_orderinfo[0][discount]' id='discount' value='' title='할인률'>
					</td>
					<td style='padding:3px;' nowrap>
						<span id='gid_text'></span> <span id='unit_text'></span> <br/>
						<b id='gname'></b> <br/>
						<span id='standard_text'></span>
					</td>
					<td align=center><span id='surtax_text'></span></td>
					<td align=center><span class='stock' id='td_stock'></span></td>
					<td align=center id='td_wholesale_price'></td>
					<td align=center id='td_psprice'></td>
					<td align=center id='td_tax_price'></td>
					<td align=center id='td_lately_price' ></td>
					<td align=center class='inner_warehouse_move' id='td_pcount'>
						<input type=text class='textbox point_color numeric amount' name='manual_orderinfo[0][pcount]' onkeyup=\"calcurate_maginrate($(this).closest('tr'))\" validation='true' id='pcount' value='' size=4 title='수량'> 
					</td>
					<td align=center class='inner_warehouse_move ' nowrap>
						<input type=text class='textbox point_color numeric sellprice' name='manual_orderinfo[0][sellprice]' onkeyup=\"calcurate_maginrate($(this).closest('tr'))\" validation='true' id='sellprice' value='' size=10 title='단가(vat 별도)'> 원
					</td>
					<td align=center class='inner_warehouse_move' id='td_total_price'></td>
					<td align=center class='inner_warehouse_move' id='td_order_tax'></td>
					<td align=center class='inner_warehouse_move' id='td_order_price'></td>
					<td align=center class='inner_warehouse_move' id='td_discount'></td>
				</tr>";

}


$Contents .="
			</table>
			<table cellpadding=0 cellspacing=0 border=0 width=100%>
				<tr>
					<td colspan=4 align=left>
						<table cellpadding=0>
							<tr height=40>
								<td>";
								if($move_status == "" || $move_status == "MA"){
									$Contents .="<img src='../images/".$admininfo["language"]."/btc_select_goods_delete.gif' border='0' align='absmiddle' onclick='checkDelete()' style='cursor:pointer;'>";
								}
								$Contents .="
								</td>
							</tr>
						</table>
					</td>
					<td colspan=4>
						<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
							<tr>
								<td align='right' style='font-size:12px;font-weight:bold;' class='red'>
									잔여여신 : <span class='blk red' style='font-size:12px;font-family:arial;' id='remain_loan_price_text_2'>".number_format($remain_loan_price)." 원</span> 
								</td>
							</tr>
							<tr>
								<td align='right' style='font-size:12px;font-weight:bold;'>
								<!--품목금액  : <span class='blk' style='font-size:12px;font-family:arial;'>".number_format($order_totalprice)."</span> 원 +
								배송비 : <span class='blk' style='font-size:12px;font-family:arial;'>".number_format($total_delivery_price)."</span> 원 = -->
								총 주문금액 : <span class='blk' style='font-size:12px;font-family:arial;' id='sum_total_price'>".number_format($order_totalprice)."</span> 원</td>
							</tr>
						</table><!--f:buttonSection-->
					</td>
				</tr>
			</table> 
";


$Contents .= "

	";


$Contents .= "
	</td>
</tr>
<tr height=20>
	<td colspan='2' style='padding:3px;' align=center>";
	//if($order_goods_total == 0){
	//	$Contents .= " <img  src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"alert('창고이동 요청하시고자 하는 품목이 한개 이상 선택되어야 합니다.\/n 창고이동 요청예정품목에서 창고이동 요청하시고자 하는 품목을 선택해주세요 ');\">";
	//}else{
		$Contents .= " <img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"Submit_bool=true;$('[name=act]').val('tmp_insert');$('form[name=input_frm]').submit();\">";
		$Contents .= " <img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"Submit_bool=true;$('form[name=input_frm]').submit();\">";
	//}
	$Contents .= "
	</td>
</tr>
</table>
</form>
		";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td >수동수주서 작성은 이동창고가 다를 경우 별도로 작성을 하셔야 합니다..</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td >이동하고자 하는 정보와 품목의 수량정보를 입력하신후 저장버튼을 눌러 요청대장의 작성을 완료 하실 수 있습니다. </td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td ><u>내부창고</u> 이동의 경우 자동으로 이동출고 와 이동입고에 대한 기록이 남으며 재고정보도 즉시 이동되게 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td ><u>외부창고</u> 이동의 경우 보관장소는 자동으로 현재창고의 경우 출고 보관장소 가 이동창고의 경우는 입고 보관장소가 자동 선택되게 됩니다. </td></tr>
</table>
";



$Contents .= HelpBox("창고 이동요청", $help_text,"100");

$Script = "
<!--link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' /-->
<!--script Language='JavaScript' type='text/javascript' src='placesection.js'></script-->

<script language='JavaScript' >
function filterNum(str) {
re = /^$|,/g;

// '$' and ',' 입력 제거
	if(str){
		//return str.replace(re, '');
	}
}

function discount_type_change(){
	wms_discount_type = $('#wms_discount_type').val();

	if(wms_discount_type=='whole'){
		$('#whole_wms_discount_type').show();
		$('#retail_wms_discount_type').hide();
		$('#retail_wms_discount_type').val('')
	}else{
		$('#retail_wms_discount_type').show();
		$('#whole_wms_discount_type').hide();
		$('#whole_wms_discount_type').val('')
	}
}

function discount_type_apply(apply_type,gid_unit){
	var ajax_bool=false;
	var GID_UNIT = {};
	var wms_discount_type, wms_discount_value;
	var tmp_tr_obj;

	if(apply_type=='all'){
		$(\"input[type=checkbox][name*='[select_gid]']\").not(\"[value='']\").closest('tr').each(function(i){
			ajax_bool=true;
			GID_UNIT[i]=$(this).find('#select_gid').val();
		});
	}else if(apply_type=='one'){
		ajax_bool=true;
		GID_UNIT[0]=gid_unit;
	}
	
	wms_discount_type = $('#wms_discount_type').val();
	wms_discount_value = $('#'+wms_discount_type+'_wms_discount_type').val();
	
	/*
	if(!wms_discount_value){
		ajax_bool=false;
		return false;
	}
	*/

	if(GID_UNIT.length > 0){
		ajax_bool=false;
		return false;
	}

	if(ajax_bool){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'get_goods_mult_price', 'wms_discount_type': wms_discount_type, 'wms_discount_value':wms_discount_value , 'gid_unit':GID_UNIT},
			url: '../offline_order/manual_purchase.act.php',  
			dataType: 'json', 
			error: function(x, o, e){
				 alert(x.status + ' : '+ o +' : '+e);
			},
			success: function(infos){
				$.each(infos,function(i,info){
					tmp_tr_obj = $(\"input[type=checkbox][name*='[select_gid]'][value='\"+info.gid_unit+\"']\").closest('tr');
					if(info.price=='X'){
						tmp_tr_obj.find('#td_wholesale_price').html('No Data');
						tmp_tr_obj.find('#wholesale_price').val(0);
						
						tmp_tr_obj.find('#td_psprice').html('No Data');
						tmp_tr_obj.find('#psprice').val(0);
						
						tmp_tr_obj.find('#sellprice').val(0);

						tmp_tr_obj.find('#td_tax_price').html('No Data');
						tmp_tr_obj.find('#tax_price').val('0');
					}else{
						math_wholesale_price(tmp_tr_obj,tmp_tr_obj.find('#surtax_div').val(),info.price);
					}
					calcurate_maginrate(tmp_tr_obj);
				});
			}
		});
	}


}
$(document).ready(function (){
	
	discount_type_apply('all');
	discount_type_change();

	$('#wms_discount_type').change(function(){
		discount_type_change();
	});

	var type_value = $('input[name=type]:checked').val();
	if(type_value == 'DP'){
		$('#order_method').css('display','none');
		$('#bank').css('display','none');

		$('#bank_ix').attr('validation','false');
	}else{
		$('#order_method').css('display','');
		$('#bank').css('display','');
		$('#bank_ix').attr('validation','true');
	}

	var value = $('input[name=method]:checked').val();
	if( value == '0'){
		if(type_value != 'DP'){
			//무통장입금
			$('#bank').css('display','');
			$('#vbank').css('display','none');
			$('#cash').css('display','none');
			$('#card').css('display','none');
				
			$('tr bank').attr('disabled',true);
			$('#vbank').attr('disabled',true);
			$('#card').attr('disabled',true);
			
			$('#bank_ix').attr('validation','true');
		}
	}else if(value == '4'){
		// 가상계좌

		$('#bank').css('display','none');
		$('#vbank').css('display','');
		//$('#cash').css('display','none');
		$('#card').css('display','none');
		$('#bank_ix').attr('validation','false');
	}else if(value == '10'){
		//현금
		$('#bank').css('display','none');
		$('#vbank').css('display','none');
		//$('#cash').css('display','');
		$('#card').css('display','none');

		$('#bank').attr('disabled','true');
		$('#vbank').attr('disabled','true');
		$('#card').attr('disabled','true');

		$('#bank_ix').attr('validation','false');
	}else if(value == '1'){
		//카드결제
		$('#bank').css('display','none');
		$('#vbank').css('display','none');
		//$('#cash').css('display','none');
		$('#card').css('display','');
		$('#bank_ix').attr('validation','false');
	}

	$('input[name=new_info]').click(function (){
		var info_value = $(this).val();
		var rname = $('#rname').val();
		if(info_value == '2'){
		
			$('#bname').val($('#rname').val());

			$('#bmember_phone1').val($('#tel1').val());
			$('#bmember_phone2').val($('#tel2').val());
			$('#bmember_phone3').val($('#tel3').val());
			
			$('#bmember_pcs1').val($('#pcs1').val());
			$('#bmember_pcs2').val($('#pcs2').val());
			$('#bmember_pcs3').val($('#pcs3').val());
		}else if(info_value == '3'){

			if($('#ci_ix').val() !=''){
				PopSWindow('../member_addr_search.php?code='+$('#ci_ix').val(),985,300,'addr_search');
			}else{
				alert('매출처를 먼저 선택해주세요.');
			}

			/*
			if($('#code').val() !=''){
				PopSWindow('../member_addr_search.php?code='+$('#code').val(),985,300,'addr_search');
			}else{
				alert('매출처에 셀러회원이 있을때만 사용가능합니다.');
			}*/
		}else{
			$('#bname').val('');

			$('#bmember_phone1').val('');
			$('#bmember_phone2').val('');
			$('#bmember_phone3').val('');
			
			$('#bmember_pcs1').val('');
			$('#bmember_pcs2').val('');
			$('#bmember_pcs3').val('');
		}
	});


	$('input[name=method]').click(function (){
		var value = $(this).val();

		if( value == '0'){
			//무통장입금
			$('#bank').css('display','');
			$('#vbank').css('display','none');
			$('#cash').css('display','none');
			$('#card').css('display','none');
				
			$('tr bank').attr('disabled',true);
			$('#vbank').attr('disabled',true);
			$('#card').attr('disabled',true);

			$('#bank_ix').attr('validation','true');
		}else if(value == '4'){
			// 가상계좌

			$('#bank').css('display','none');
			$('#vbank').css('display','');
			//$('#cash').css('display','none');
			$('#card').css('display','none');

			$('#bank_ix').attr('validation','false');
		}else if(value == '10'){
			//현금
			$('#bank').css('display','none');
			$('#vbank').css('display','none');
			//$('#cash').css('display','');
			$('#card').css('display','none');

			$('#bank').attr('disabled','true');
			$('#vbank').attr('disabled','true');
			$('#card').attr('disabled','true');
			
			$('#bank_ix').attr('validation','false');
		}else if(value == '1'){
			//카드결제
			$('#bank').css('display','none');
			$('#vbank').css('display','none');
			//$('#cash').css('display','none');
			$('#card').css('display','');

			$('#bank_ix').attr('validation','false');
		}
	
	
	});

	$('#com_addr').click(function (){
		var value = $('#com_addr').attr('checked');
		 if(value == 'checked'){
			var com_zip_array = $('#com_zip').val(); 
			$('#zipcode1').val(com_zip_array);
			//$('#zip2').val(com_zip_array[1]);

			$('#addr1').val($('#com_addr1').val());
			$('#addr2').val($('#com_addr2').val());
		 }else{
			$('#zip1').val('');
			$('#zip2').val('');

			$('#addr1').val('');
			$('#addr2').val('');
		 
		 }
	});

	$('input[name=type]').click(function (){
		var value=$(this).val();
		if(value == 'DP'){
			$('#order_method').css('display','none');
			$('#bank').css('display','none');

			$('#bank_ix').attr('validation','false')
		}else{
			$('#order_method').css('display','');
			$('#bank').css('display','');
			$('#bank_ix').attr('validation','true')
		}
	});
	

	if($('#type_DP').attr('checked')){
		//$('#order_method').css('display','none');
		//$('#bank').css('display','none');
	}

	$('input[name=voucher_div]').click(function (){
		var value = $(this).val();
		
		if(value == '1'){
			$('input[name=certificate_yn]').attr('checked',false);
		
		}else if(value == '3'){
			$('input[name=voucher_num_div]').attr('checked',false);
		}else if(value == '2'){
			$('input[name=certificate_yn]').attr('checked',false);
			$('input[name=voucher_num_div]').attr('checked',false);
		}
	
	});

	$('input[name=voucher_num_div]').click(function (){
		var check_voucher_div = $('#voucher_div_1').attr('checked');

		if(!check_voucher_div){
			alert('개인소득공제를 선택해 주세요.');
			$('input[name=voucher_num_div]').attr('checked',false);
		}
	
	});

	$('input[name=certificate_yn]').click(function (){
		var check_voucher_div = $('#voucher_div_3').attr('checked');

		if(!check_voucher_div){
			alert('세금계산서 발급을 선택해 주세요.');
			$('input[name=certificate_yn]').attr('checked',false);
		}
	
	});

	
	$('#barcode').keypress(function(e){
		if(e.keyCode==13){
			BarcodeGoodsSelect($(this));
		}
	})

});

function BarcodeGoodsSelect(obj){
	
	if($('#ci_ix').val()!=''){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'get_goods_barcode', 'ci_ix':$('#ci_ix').val(), 'barcode':obj.val()},
			url: '../offline_order/manual_purchase.act.php',  
			dataType: 'json', 
			error: function(x, o, e){
				 alert(x.status + ' : '+ o +' : '+e);
			},
			success: function(data){
				if(data !=null && data.gid!=null){
					GoodsInsert(data);
				}else{
					alert('검색된 품목이 없습니다.');
				}
			}
		});
		obj.val('');
	}else{
		alert('매출처를 먼저 선택해주세요.');
		obj.val('');
	}
}

function GoodsSelect(gid, gname, unit,unit_text, standard, buying_price, company_name, place_name, section_name, pi_ix, ps_ix, vdate, expiry_date,stock, wholesale_price, sellprice,surtax_div,surtax_text,lately_price){
	
	var data = {};
	data['gid']=gid;
	data['gname']=gname;
	data['unit']=unit;
	data['unit_text']=unit_text;
	data['standard']=standard;
	data['buying_price']=buying_price;
	data['company_name']=company_name;
	data['place_name']=place_name;
	data['section_name']=section_name;
	data['pi_ix']=pi_ix;
	data['ps_ix']=ps_ix;
	data['vdate']=vdate;
	data['expiry_date']=expiry_date;
	data['stock']=stock;
	data['wholesale_price']=wholesale_price;
	data['sellprice']=sellprice;
	data['surtax_div']=surtax_div;
	data['surtax_text']=surtax_text;
	data['lately_price']=lately_price;
	
	GoodsInsert(data);
}

function math_wholesale_price(obj,surtax_div,wholesale_price){
	if( surtax_div == '3' || surtax_div == '4'){
		obj.find('#td_wholesale_price').html(FormatNumber(wholesale_price)+' 원');
		obj.find('#wholesale_price').val(wholesale_price);
		
		obj.find('#td_psprice').html(FormatNumber(wholesale_price)+' 원');
		obj.find('#psprice').val(wholesale_price);
		
		obj.find('#sellprice').val(wholesale_price);

		obj.find('#td_tax_price').html('0 원');
		obj.find('#tax_price').val('0');
	}else{
		obj.find('#td_wholesale_price').html(FormatNumber(wholesale_price - Math.floor(wholesale_price/11))+' 원');
		obj.find('#wholesale_price').val(wholesale_price - Math.floor(wholesale_price/11));

		obj.find('#td_psprice').html(FormatNumber(wholesale_price)+' 원');
		obj.find('#psprice').val(wholesale_price);
		
		obj.find('#sellprice').val(wholesale_price - Math.floor(wholesale_price/11));

		obj.find('#td_tax_price').html(FormatNumber(Math.floor(wholesale_price/11))+' 원');
		obj.find('#tax_price').val(Math.floor(wholesale_price/11));
	}
}


function GoodsInsert(data){

	var gid=data.gid;
	var gname=data.gname;
	var unit=data.unit;
	var unit_text=data.unit_text;
	var standard=data.standard;
	var buying_price=data.buying_price;
	var company_name=data.company_name;
	var place_name=data.place_name;
	var section_name=data.section_name;
	var pi_ix=data.pi_ix;
	var ps_ix=data.ps_ix;
	var vdate=data.vdate;
	var expiry_date=data.expiry_date;
	var stock=data.stock;
	if(data.stock!=null){
		var stock=data.stock;
	}else{
		var stock='0';
	}
	var wholesale_price=data.wholesale_price;
	var sellprice=data.sellprice;
	var surtax_div=data.surtax_div;
	var surtax_text=data.surtax_text;
	var lately_price=data.lately_price;


   var tbody = $('#warehouse_move_apply_list tbody');  	
   var total_rows = tbody.find('tr[depth^=1]').length;  
   var rows = tbody.find('tr[depth^=1]').length;  

	if($.browser.msie){
		var thisRow = tbody.find('tr[depth^=1]:last');  
	}else{
		var thisRow = tbody.find('tr[depth^=1]:last');  
	}
	if(thisRow.find('#gid_text').html() == ''){ 
		thisRow.find('#select_gid').val(gid+'|'+unit);
		thisRow.find('#gid_text').html(gid);
		thisRow.find('#gid').val(gid);
		thisRow.find('#gname').html(gname);

		thisRow.find('#unit').val(unit);
		thisRow.find('#unit_text').html(unit_text);
	
		thisRow.find('#surtax_div').val(surtax_div);
		thisRow.find('#surtax_text').html(surtax_text);


		thisRow.find('#pi_ix').val(pi_ix);
		thisRow.find('#ps_ix').val(ps_ix);
		thisRow.find('#standard_text').html(standard);
		thisRow.find('#standard').val(standard);
		
		thisRow.find('#td_stock').html(stock);
		thisRow.find('#stock').val(stock);
		
		thisRow.find('#pcount').val('1');

		if(lately_price){
			thisRow.find('#td_lately_price').html(FormatNumber(lately_price)+' 원');
		}else{
			thisRow.find('#td_lately_price').html('-');
		}

		/*
		thisRow.find('#company_name').html(company_name);
		thisRow.find('#place_name').html(place_name);
		thisRow.find('#section_name').html(section_name);
		thisRow.find('#expiry_date').val(expiry_date);
		thisRow.find('#expiry_date_text').html(expiry_date);
		thisRow.find('#vdate').val(vdate);
		thisRow.find('#vdate_text').html(vdate);
		thisRow.find('#buying_price').html(buying_price);
		*/

		discount_type_apply('one',gid+'|'+unit);

	}else{

		if($.browser.msie){
		  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
		}else{
		  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
		}
		
		newRow.find('#select_gid').attr('name','manual_orderinfo['+(total_rows)+'][select_gid]');
		newRow.find('#gid').attr('name','manual_orderinfo['+(total_rows)+'][gid]');
		newRow.find('#unit').attr('name','manual_orderinfo['+(total_rows)+'][unit]');
		newRow.find('#surtax_div').attr('name','manual_orderinfo['+(total_rows)+'][surtax_div]');
		newRow.find('#stock').attr('name','manual_orderinfo['+(total_rows)+'][stock]');
		newRow.find('#wholesale_price').attr('name','manual_orderinfo['+(total_rows)+'][wholesale_price]');
		newRow.find('#psprice').attr('name','manual_orderinfo['+(total_rows)+'][psprice]');
		newRow.find('#tax_price').attr('name','manual_orderinfo['+(total_rows)+'][tax_price]');
		newRow.find('#pcount').attr('name','manual_orderinfo['+(total_rows)+'][pcount]');
		newRow.find('#sellprice').attr('name','manual_orderinfo['+(total_rows)+'][sellprice]');
		newRow.find('#total_price').attr('name','manual_orderinfo['+(total_rows)+'][total_price]');
		newRow.find('#order_tax').attr('name','manual_orderinfo['+(total_rows)+'][order_tax]');
		newRow.find('#discount').attr('name','manual_orderinfo['+(total_rows)+'][discount]');
		newRow.find('#standard').attr('name','manual_orderinfo['+(total_rows)+'][standard]');
		
		newRow.find('#select_gid').val(gid+'|'+unit);
		newRow.find('#gid_text').html(gid);   
		newRow.find('#gid').val(gid);
		newRow.find('#gname').html(gname);
		newRow.find('#unit').val(unit);
		newRow.find('#unit_text').html(unit_text);

		newRow.find('#surtax_div').val(surtax_div);
		newRow.find('#surtax_text').html(surtax_text);

		newRow.find('#standard_text').html(standard);

		newRow.find('#td_stock').html(stock);
		newRow.find('#stock').val(stock);

		if(lately_price){
			newRow.find('#td_lately_price').html(FormatNumber(lately_price)+' 원');
		}else{
			newRow.find('#td_lately_price').html('-');
		}

		newRow.find('#pcount').val('1');

		//newRow.find('#sellprice').val('');
		//newRow.find('#td_discount').html('0 %');
		//newRow.find('#discount').val('');
		
		discount_type_apply('one',gid+'|'+unit);
	}
}


function calcurate_maginrate(obj_tr){

	var tbody = $('#warehouse_move_apply_list tbody');  	
	var total_rows = tbody.find('tr[depth^=1]').length;  

	//var thisRow = tbody.find('tr[depth^=1]:first');
	var thisRow = obj_tr;
	
	if(total_rows == '1' || true){ 
		
		var wholesale_price = thisRow.find('#wholesale_price').val();
		var surtax_div = thisRow.find('#surtax_div').val();

		//공급가 할인가
		if(thisRow.find('#sellprice').val() < 0)	{
			var sellprice = 0;
		}else{
			var sellprice = thisRow.find('#sellprice').val();
		}
		
		//수량
		if(thisRow.find('#pcount').val() == 0)	{
			var pcount = 1;				//기본 수량 1로 합니다.
			thisRow.find('#pcount').val(pcount);
		}else{
			var pcount = thisRow.find('#pcount').val();
		}

		//세액
		if(thisRow.find('#order_tax').val() < 0)	{
			var order_tax = 0;
		}else{
			var  order_tax = thisRow.find('#order_tax').val();
		}


		if(surtax_div == '3' || surtax_div == '4'){
			if( thisRow.find('#sellprice').val() > 0){	//세액계산
				thisRow.find('#order_tax').val('0');
				thisRow.find('#td_order_tax').html('0 원');
			}

			if(pcount > 1){
				thisRow.find('#order_tax').val('0');
				thisRow.find('#td_order_tax').html('0 원');

				//thisRow.find('#sellprice').val(sellprice * pcount);
				thisRow.find('#total_price').val(parseInt(sellprice) * pcount);
				thisRow.find('#td_total_price').html(FormatNumber(parseInt(sellprice) * pcount)+' 원');
			}

			if(wholesale_price > 0){
				thisRow.find('#discount').val(Math.floor((1-sellprice/wholesale_price)*100));
				thisRow.find('#td_discount').html(FormatNumber(Math.floor((1-sellprice/wholesale_price)*100))+'%');
			}

			thisRow.find('#total_price').val((parseInt(sellprice)) * pcount);
			thisRow.find('#td_total_price').html(FormatNumber((parseInt(sellprice)) * pcount)+' 원');
			thisRow.find('#td_order_price').html(FormatNumber((parseInt(sellprice)) * pcount)+' 원');

		}else{
			if( thisRow.find('#sellprice').val() > 0){	//세액계산
				thisRow.find('#order_tax').val(Math.floor(sellprice/10));
				thisRow.find('#td_order_tax').html(FormatNumber(Math.floor(sellprice/10))+' 원');
			}

			if(pcount > 1){
				thisRow.find('#order_tax').val(Math.floor(sellprice/10*pcount));
				thisRow.find('#td_order_tax').html(FormatNumber(Math.floor(sellprice/10*pcount))+' 원');

				//thisRow.find('#sellprice').val(sellprice * pcount);
				thisRow.find('#total_price').val((parseInt(sellprice) + parseInt(Math.floor(sellprice/10))) * pcount);
				thisRow.find('#td_total_price').html(FormatNumber((parseInt(sellprice) + parseInt(Math.floor(sellprice/10))) * pcount)+' 원');
			}

			if(wholesale_price > 0){
				thisRow.find('#discount').val(Math.floor((1-sellprice/wholesale_price)*100));
				thisRow.find('#td_discount').html(FormatNumber(Math.floor((1-sellprice/wholesale_price)*100))+'%');
			}

			thisRow.find('#total_price').val((parseInt(sellprice) + parseInt(Math.floor(sellprice/10))) * pcount);
			thisRow.find('#td_total_price').html(FormatNumber((parseInt(sellprice) + parseInt(Math.floor(sellprice/10))) * pcount)+' 원');
			thisRow.find('#td_order_price').html(FormatNumber((parseInt(sellprice)) * pcount)+' 원');
		}

		set_totalprice();

	}else{

		 $('#warehouse_move_apply_list tr').each(function () {

			// alert($(this).find('#sellprice').val());

			if($(this).find('#sellprice').val() < 0)	{
				var sellprice = 0;
			}else{
				var sellprice = $(this).find('#sellprice').val();
			}
			var wholesale_price = $(this).find('#wholesale_price').val();
			var surtax_div = $(this).find('#surtax_div').val();
				
			//수량
			if($(this).find('#pcount').val() == 0)	{
				var pcount = 1;				//기본 수량 1로 합니다.
				$(this).find('#pcount').val(pcount);
			}else{
				var pcount = $(this).find('#pcount').val();
			}

			//세액
			if($(this).find('#order_tax').val() < 0)	{
				var order_tax = 0;
			}else{
				var  order_tax = $(this).find('#order_tax').val();
			}
			
			if(surtax_div == '3' || surtax_div == '4'){

				if( $(this).find('#sellprice').val() > 0){	//세액계산
					$(this).find('#order_tax').val('0');
					$(this).find('#td_order_tax').html('0 원');
				}

				if(pcount > 1){
					$(this).find('#order_tax').val('0');
					$(this).find('#td_order_tax').html('0 원');

					//newRow.find('#sellprice').val(sellprice * pcount);
					$(this).find('#total_price').val(parseInt(sellprice) * pcount);
					$(this).find('#td_total_price').html(FormatNumber((parseInt(sellprice)) * pcount)+' 원');
				}

				if(wholesale_price > 0){
					$(this).find('#discount').val(Math.floor((1-sellprice/wholesale_price)*100));
					$(this).find('#td_discount').html(Math.floor((1-sellprice/wholesale_price)*100)+'%');
				}

				$(this).find('#total_price').val((parseInt(sellprice)) * pcount);
				$(this).find('#td_total_price').html(FormatNumber((parseInt(sellprice)) * pcount)+' 원');
				$(this).find('#td_order_price').html(FormatNumber((parseInt(sellprice)) * pcount)+' 원');
				
			}else{
				if( $(this).find('#sellprice').val() > 0){	//세액계산
					$(this).find('#order_tax').val(Math.floor(sellprice/10));
					$(this).find('#td_order_tax').html(FormatNumber(Math.floor(sellprice/10))+' 원');
				}

				if(pcount > 1){
					$(this).find('#order_tax').val(Math.floor(sellprice/10*pcount));
					$(this).find('#td_order_tax').html(FormatNumber(Math.floor(sellprice/10*pcount))+' 원');

					//newRow.find('#sellprice').val(sellprice * pcount);
					$(this).find('#total_price').val((parseInt(sellprice) + parseInt(Math.floor(sellprice/10))) * pcount);
					$(this).find('#td_total_price').html(FormatNumber((parseInt(sellprice) + parseInt(Math.floor(sellprice/10))) * pcount)+' 원');
				}

				if(wholesale_price > 0){
					$(this).find('#discount').val(Math.floor((1-sellprice/wholesale_price)*100));
					$(this).find('#td_discount').html(Math.floor((1-sellprice/wholesale_price)*100)+'%');
				}

				$(this).find('#total_price').val((parseInt(sellprice) + parseInt(Math.floor(sellprice/10))) * pcount);
				$(this).find('#td_total_price').html(FormatNumber((parseInt(sellprice) + parseInt(Math.floor(sellprice/10))) * pcount)+' 원');
				$(this).find('#td_order_price').html(FormatNumber((parseInt(sellprice)) * pcount)+' 원');
			}
        });

		
			if($.browser.msie){
			 // var newRow = tbody.find('tr[depth^=1]');  
			}else{
			 // var newRow = tbody.find('tr[depth^=1]');  
			}
			
	set_totalprice();
			//공급가 할인가

	}
}

function set_totalprice(pid){
	var total_price = 0;
	var sumprice = 0;

	$('input[id^=total_price]').each(function(){
		var total_price = $(this).val()

		if(total_price > 0){
		sumprice += parseInt(total_price);

		
		}
		$('#sum_total_price').html(FormatNumber(sumprice));
	});
	
}

function zipcode(type) {
	var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function sum_order_totalprice(){
	//b_delivery_price = Number($('#b_delivery_price').val());
	a_delivery_price = Number($('#a_delivery_price').val());
	//b_tax = Number($('#b_tax').val());
	a_tax = Number($('#a_tax').val());
	//b_commission = Number($('#b_commission').val());
	a_commission = Number($('#a_commission').val());

	order_totalprice = Number($('#order_totalprice').text());

	etc_price = a_delivery_price+a_tax+a_commission;

	order_pttotalprice = order_totalprice+etc_price;

	$('#etc_price').text(etc_price)
	$('#order_pttotalprice').val(order_pttotalprice)
}

$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){

		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+3d');
		}
	}

	});


	$(\"#end_datepicker\").datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'
	});

	$(\"#bank_input_date\").datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'
	});

		$(\"#shipping_date\").datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'
	});


});

function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#priod\").val(FromDate);
}
 

function input_text(){
	if($('#msg2').attr('rel') == 'first'){
		$('#msg2').val('');
		$('#msg2').attr('rel','');
	}
}


function clearAll(frm){
		$('.select_gid').each(function(){
			$(this).attr('checked',false);
		});
		/*
		for(i=0;i < frm.gid.length;i++){
				frm.gid[i].checked = false;
		}
		*/
}

function checkAll(frm){
       	$('.select_gid').each(function(){
			$(this).attr('checked','checked');
		});		
}
function fixAll(frm){
	//alert(frm.all_fix.checked);
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function checkDelete(){
   var tbody = $('#warehouse_move_apply_list tbody');  	
   var total_rows = tbody.find('tr[depth^=1]').length;  
   var thisRow = '';


	var thisRow = tbody.find('tr[depth^=1]:last');  


	$('.select_gid').each(function(){
		if($(this).attr('checked') == 'checked'){
			var total_rows = tbody.find('tr[depth^=1]').length;  
			if(total_rows > 1){
				$(this).parent().parent().remove();
			}else{

				thisRow = $(this).parent().parent();
				thisRow.find('#select_gid').val('');
				thisRow.find('#gid_text').html('');   
				thisRow.find('#gid').val('');
				thisRow.find('#gname').html('');
				thisRow.find('#unit').val('');
				thisRow.find('#unit_text').html('');

				thisRow.find('#surtax_text').html('');
				thisRow.find('#surtax_div').val('');

				thisRow.find('#td_stock').html('');
				thisRow.find('#stock').val('');

				thisRow.find('#td_wholesale_price').html('');
				thisRow.find('#wholesale_price').val('');

				thisRow.find('#td_psprice').html('');
				thisRow.find('#psprice').val('');

				thisRow.find('#td_tax_price').html('');
				thisRow.find('#tax_price').val('');

				//thisRow.find('#td_pcount').html('');
				thisRow.find('#pcount').val('');
				thisRow.find('#sellprice').val('');

				thisRow.find('#td_total_price').html('');
				thisRow.find('#total_price').val('');

				thisRow.find('#td_order_tax').html('');
				thisRow.find('#order_tax').val('');

				thisRow.find('#td_discount').html('');
				thisRow.find('#discount').val('');

				thisRow.find('#gid').attr('name','manual_orderinfo[0][gid]');
				thisRow.find('#unit').attr('name','manual_orderinfo[0][unit]');
				thisRow.find('#standard').attr('name','manual_orderinfo[0][standard]');

				thisRow.find('#surtax_div').attr('name','manual_orderinfo[0][surtax_div]');
				thisRow.find('#stock').attr('name','manual_orderinfo[0][stock]');

				thisRow.find('#wholesale_price').attr('name','manual_orderinfo[0][wholesale_price]');

				thisRow.find('#psprice').attr('name','manual_orderinfo[0][psprice]');

				thisRow.find('#tax_price').attr('name','manual_orderinfo[0][tax_price]');

				thisRow.find('#pcount').attr('name','manual_orderinfo[0][pcount]');

				thisRow.find('#sellprice').attr('name','manual_orderinfo[0][sellprice]');
				thisRow.find('#total_price').attr('name','manual_orderinfo[0][total_price]');

				thisRow.find('#order_tax').attr('name','manual_orderinfo[0][order_tax]');

				thisRow.find('#discount').attr('name','manual_orderinfo[0][discount]');


			}
		}
	});		
}
 
function changeMoveStatus(obj){
	if(obj.value == 'MO'){
		$('.apply_cnt').each(function(){
			$(this).attr('readonly','true');
			$(this).css('border','0px');
		});

		$('.sellprice').each(function(){
			$(this).attr('validation','true');
			$(this).attr('readonly',false);
		});
	}else if(obj.value == 'MA'){
		$('.apply_cnt').each(function(){
			$(this).attr('readonly','false');
			$(this).css('border','1px solid silver');
		});

		$('.sellprice').each(function(){
			$(this).attr('validation','false');
			$(this).attr('readonly','true');
		});
	}else if(obj.value == 'MI'){
		$('.sellprice').each(function(){
			$(this).attr('readonly','true');
			$(this).css('border','0px');
		});
	}
}

/*
function ChangeType(val){
	if(val == 'IW'){
		//var table_width = $('#warehouse_move_apply_list').prop('offsetWidth');
		$('#move_company_id').attr('disabled',true);
		$('.inner_warehouse_move').css('display','none');
		$('#move_status_tr').css('display','none');
	}else{
		$('#move_company_id').attr('disabled',false);
		$('.inner_warehouse_move').css('display','inline');
		$('#move_status_tr').css('display','inline');
	}
}

$(function() {
	$('#now_company_id').change(function(){
		if($('select#h_type option:selected').val() == 'IW'){
			$('#now_pi_ix').change();
			$('#move_company_id').val($('#now_company_id').val()).change();
			$('#move_pi_ix').change();
			
			//loadPlace($('#move_company_id option:selected'),'move_pi_ix');
		}else if($('select#h_type option:selected').val() == ''){
			$('#now_company_id').val('');			
			alert('창고이동 유형을 먼저 선택해주세요');
			$('#now_pi_ix').change();
		}
	});
});



$(document).ready(function() {
	if($('select#h_type option:selected').val() == 'IW'){
		$('#move_company_id').attr('disabled',true);
		$('.inner_warehouse_move').css('display','none');
		$('#move_status_tr').css('display','none');
		
	}
});
*/

function PopGoodsSelect(){
	//alert($('select[name^=now_company_id]').val());
	if($('#ci_ix').val()!=''){
		ShowModalWindow('../inventory/goods_select.php?page_type=&user_com_id='+$('#ci_ix').val(),1200,800,'goods_select');
	}else{
		//ShowModalWindow('../inventory/goods_select.php?page_type=',1200,800,'goods_select');
		alert('매출처를 먼저 선택해주세요.');
	}
	//PoPWindow('../inventory/goods_select.php?page_type=',1000,800,'goods_select');
}

Submit_bool=true;
function checkInputForm(frm){
	if(!Submit_bool){
		return false;
	}
	
	if($(\"input[type=checkbox][name*='[select_gid]'][value='']\").length > 0){
		alert('품목을 하나이상 선택하셔야 합니다.');
		return false;
	}else{
		if($(\"input[type=hidden][name*='[psprice]'][value='0']\").length > 0){
			alert('단가정보가 없는 품목이 존재합니다. 품목가격을 수정하신후 다시 시도해주세요.');
			return false;
		}
	}

	if(!CheckFormValue(frm)){
		return false;
	}
}


function receipt_view() {
	if($('input[id^=receipt_1]').attr('checked')) {
		$('#confirm_no').attr('disabled',false);
		$('#choose_0').attr('disabled',false);
		$('#choose_1').attr('disabled',false);
		$('#choose_0').attr('checked',true);
	}
	if($('input[id^=receipt_2]').attr('checked')) {
		$('#confirm_no').attr('disabled',true);
		$('#choose_0').attr('disabled',true);
		$('#choose_1').attr('disabled',true);
		$('#choose_0').attr('checked',false);
		$('#choose_1').attr('checked',false);
		
	}
}


</Script>
";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->strLeftMenu = offline_order_menu();
	$P->addScript = $Script;
	$P->Navigation = "견적서관리 > 수동수주서 > 수동수주서 작성 ";
	$P->NaviTitle = "수동수주서 작성 ";
	$P->strContents = $Contents;
	$P->jquery_use = false;

	$P->PrintLayOut();
}else{
	$P = new LayOut;
	$P->addScript = $Script;
	$P->OnloadFunction = "";//MenuHidden(false);
	//$P->prototype_use = false;
	//$P->jquery_use = true;
	
	$P->strContents = $Contents;
	if($pre_type=="inventory"){
		$P->strLeftMenu = inventory_menu();
		$P->Navigation = "WMS/구매 > 출고관리 > 예외출고";
		$P->title = "예외출고";
	}else{
		$P->strLeftMenu = offline_order_menu();
		$P->Navigation = "견적서관리 > 수동수주서 > 수동수주서 작성";
		$P->title = "수동수주서 작성";
	}
	
	$P->PrintLayOut();
}
function getCompanyCartAdmin($company_id,$delivery_company, $cart_key){
	global $user;
	$where = " cart_key = '$cart_key'";
	if($delivery_company == "MI"){
		$delivery_company_where = " and (c.delivery_company ='MI' or c.delivery_company = '') ";
	}else{
		$delivery_company_where = " and c.delivery_company = '$delivery_company' ";
	}
	$mdb = new Database;
	$admin_delievery_policy = getTopDeliveryPolicy($mdb);

	$sql = "select c.*,
			p.delivery_package,
			if(p.delivery_policy =1,
				(select if(delivery_policy = 1,'".$admin_delievery_policy[delivery_price]."',delivery_price) from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '$company_id' )
			,delivery_price) as delivery_price,
			(select if(delivery_policy = 1,'".$admin_delievery_policy[delivery_basic_policy]."',delivery_basic_policy) from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '".$company_id."') as delivery_basic_policy
			from shop_cart c,shop_product p
			where $where and c.id = p.id and company_id = '".$company_id."'
			and c.delivery_company='$delivery_company'
			order by c.regdate desc ";//정렬이 delivery_price 인 것을 regdate 로 바꿈 kbk 11.10.10

	$mdb->query($sql);
	return $mdb->fetchall();
}
function giftRelation($total_price){
	global $db;

	$sql = "select * from shop_product where $total_price >= startprice and $total_price < endprice and product_type = '6' limit 4";
	$db->query($sql);

	$gift_product = $db->fetchall();

	return $gift_product;
}

function getScName($sc_code){
	global $db;

	$db->query("select sc_nm from shop_comm_sc where sc_code = '$sc_code' ");
	$db->fetch();

	return $db->dt[sc_nm];
}

/*


CREATE TABLE IF NOT EXISTS `inventory_warehouse_move` (
  `wm_ix` int(10) unsigned AUTO_INCREMENT COMMENT '이동요청키값',
  `apply_charger_ix` varchar(32) DEFAULT NULL COMMENT '요청 담당자',
  `apply_charger_name` varchar(50) DEFAULT NULL COMMENT '요청 담당자 이름',
  `apply_date` varchar(10) DEFAULT NULL COMMENT '이동 요청일자',
  `wm_delivery_date` varchar(10) DEFAULT NULL COMMENT '이동 출고일자',
  `wm_entering_date` varchar(10) NOT NULL COMMENT '이동 입고일자',
  `move_pi_ix` int(6) unsigned DEFAULT NULL COMMENT '창고키',
  `move_ps_ix` int(6) unsigned DEFAULT NULL COMMENT '보관장소키',
  `status` varchar(2) DEFAULT NULL COMMENT '상태',
  `charger_ix` varchar(32) DEFAULT NULL COMMENT '작성자',
  `charger_name` varchar(50) DEFAULT NULL COMMENT '작성자 이름',
  `etc` varchar(255) DEFAULT NULL COMMENT '기타필드',
  `al_ix` int(10) NOT NULL COMMENT '결제라인',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY (`wm_ix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='창고이동내역';


CREATE TABLE IF NOT EXISTS `inventory_warehouse_move_detail` (
  `wmd_ix` int(10) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `wm_ix` int(10) unsigned  COMMENT '이동요청키값',
  `pi_ix` int(6) unsigned DEFAULT NULL COMMENT '창고키',
  `ps_ix` int(6) unsigned DEFAULT NULL COMMENT '보관장소키',
  `gid` int(10) unsigned zerofill DEFAULT NULL COMMENT '품목아이디',
  `gname` varchar(255) DEFAULT NULL COMMENT '이동 품목명',
  `unit` varchar(100) DEFAULT NULL COMMENT '단위',
  `standard` varchar(100) DEFAULT NULL COMMENT '규격',
  `apply_cnt` int(8) DEFAULT NULL COMMENT '이동요청수량',
  `delivery_cnt` int(8) DEFAULT NULL COMMENT '이동 출고수량',
  `amount` int(8) DEFAULT NULL COMMENT '이동 입고수량',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY (`wmd_ix`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='창고이동 상세정보'  ;



create table inventory_order (
loid varchar(20) not null,
order_charger varchar(255) null default null,
limit_priod varchar(10) null default null,
ci_ix varchar(255) null default null,
incom_company_charger varchar(255) null default null,
total_price int(10) null default 0,
total_add_price int(10) null default 0,
status varchar(2) default null ,
etc varchar(255) default null ,
regdate datetime not null,
primary key(loid));

*/

?>