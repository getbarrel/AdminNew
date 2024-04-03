<?
include("../class/layout.class");
include("../inventory/inventory.lib.php");

$db = new Database;
$db1 = new Database;
$db2 = new Database;
$db3 = new Database;
$odb = new Database;
$tdb = new Database;

//print_r($_SESSION["admininfo"]);

$sql = "select wm.*, pi.company_id as move_company_id, pi.place_name as move_place_name, ps.section_name as move_section_name
		from inventory_warehouse_move wm 
		left join  inventory_place_info pi on wm.move_pi_ix = pi.pi_ix
		left join  inventory_place_section ps on wm.move_ps_ix = ps.ps_ix	
		where wm_ix='$wm_ix' ";

//"SELECT * FROM inventory_warehouse_move wm left join WHERE wm_ix='$wm_ix'"

//$db->query($sql);
//$db->fetch();

if($db->total && false){
	$act = "update";
	$move_status = $db->dt[status];
	$move_company_id = $db->dt[move_company_id];
	$now_company_id = $db->dt[now_company_id];
	$charger_name = $db->dt[charger_name];
	$apply_date = $db->dt[apply_date];
	$apply_charger_ix = $db->dt[apply_charger_ix];
	//echo $_SESSION["admininfo"]["charger_ix"].":::".$db->dt[charger_ix];
	if($_SESSION["admininfo"]["charger_ix"] == $db->dt[charger_ix]){
		if($move_status == ""){
			$info_type = "select";
		}else{
			$info_type = "text";
		}
	}else{
		$info_type = "text";
	}
	
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

//echo $_SESSION["admininfo"]["charger_name"];

$Contents ="
<form  name='input_frm' method='post' onsubmit=\"return CheckWarehouseMove(this)\" action='./manual_purchase.act.php'  ><!-- target='act'-->
<input type=hidden name=act value='".$act."'>
<input type=hidden name=mmode value='".$mmode."'>
<input type='hidden' name='wm_ix' id='wm_ix' value='".$wm_ix."'>
<input type='hidden' id='code' value=''>
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("수동수주서 작성", "수동수주서 > 수동수주서 작성")."</td>
</tr>

<tr >
	<td colspan=2 width='100%' valign=top style='padding-top:3px;'>";

$est_delivery_zip = explode("-", $db1->dt[est_delivery_zip]);
$est_tel = explode("-", $db1->dt[est_tel]);
$est_mobile = explode("-", $db1->dt[est_mobile]);

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
							
							<td class='input_box_title' ><b>수주서 요청일</b> <img src='".$required3_path."'></td>
							<td class='input_box_item' >
								<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=*>
									<tr>
										<TD nowrap>";
										if($info_type == "text"){
											$Contents .= "".$apply_date."
											<input type='hidden' name='apply_date' class='textbox' value='".$apply_date."'  validation='true' title='요청일'  >";
										}else{
											$Contents .= "
											<input type='text' name='apply_date' class='textbox' value='".$apply_date."' style='".($info_type == "text" ? "border:0px;":"")."height:20px;width:70px;text-align:center;' id='end_datepicker' validation='true' title='요청일' ".($info_type == "text" ? "readonly ":"").">";
										}
										$Contents .= "
										</TD>
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

		$Contents .= "".selectType('2',"14",$db->dt[h_type],'h_type',$info_type,true).""; // type_div : 5 인건 창고이동

		$Contents .= "
							</td>
						</tr>
						<tr height='30'>
							<td class='input_box_title' ><b>매출처 <img src='".$required3_path."'></b></td>
							<td class='input_box_item'>
								".SelectSupplyCompany($ci_ix,"ci_ix","select", "false", 2,$type='estimate_order')."
							</td>
							<td class='input_box_title' ><b>ID 검색</b></td>
							<td class='input_box_item' id='member_idy'>
								
							</td>
						</tr>
						<tr height='30'>
							<td class='input_box_title' ><b>사업자번호</b></td>
							<td class='input_box_item'>
							<input type=text class='textbox numeric' name='com_number1' value='".$com_number1." ' id='com_number1' maxlength='3' style='width:50px;text-align:center;'> - 
							<input type=text class='textbox numeric' name='com_number2' value='".$com_number2." ' id='com_number2' maxlength='2' style='width:50px;text-align:center;'> - 
							<input type=text class='textbox numeric' name='com_number3' value='".$com_number3." ' id='com_number3' maxlength='5' style='width:50px;text-align:center;'>
							</td>
							<td class='input_box_title' ><b>여신한도</b></td>
							<td class='input_box_item' ><!-- common_sellelr_detail.loan_price 여신한도 -->
							<input type=text class='textbox numeric' name='loan_price' value='".$db->dt[loan_price]." ' id=loan_price style='width:100px;text-align:center;' readonly> 원
							</td>
						</tr>
						<tr height='30'>
							<td class='input_box_title' ><b>대표번화번호</b></td>
							<td class='input_box_item' >
								<input type=text class='textbox numeric' name='com_phone1' value='".$com_phone1." ' id='com_phone1' maxlength='3' style='width:50px;text-align:center;'> - 
							<input type=text class='textbox numeric' name='com_phone2' value='".$com_phone2." ' id='com_phone2' maxlength='4' style='width:50px;text-align:center;'> - 
							<input type=text class='textbox numeric' name='com_phone3' value='".$com_phone3." ' id='com_phone3' maxlength='4' style='width:50px;text-align:center;'>
							</td>
							<td class='input_box_title' ><b>담당자 핸드폰번호</b></td>
							<td class='input_box_item' >
							<input type=text class='textbox numeric' name='com_mobile1' value='".$com_mobile1." ' id='com_mobile1' maxlength='3' style='width:50px;text-align:center;'> - 
							<input type=text class='textbox numeric' name='com_mobile2' value='".$com_mobile2." ' id='com_mobile2' maxlength='4' style='width:50px;text-align:center;'> - 
							<input type=text class='textbox numeric' name='com_mobile3' value='".$com_mobile3." ' id='com_mobile3' maxlength='4' style='width:50px;text-align:center;'>
							</td>
						</tr>
						<tr>
							<td class='input_box_title'> <b>사업자 주소  <img src='".$required3_path."'> </b>    </td>
							<td class='input_box_item' colspan=3>
								<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
								<div id='input_address_area' ><!--style='display:none;'-->
								<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
									<col width='120px'>
									<col width='*'>
									<tr>
										<td height=26>
											<input type='text' class='textbox' name='com_zip' id='com_zip' size='15' maxlength='15' value='".$db->dt[com_zip]."' readonly>
										</td>
										<td style='padding:1px 0 0 5px;'>
											<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
										</td>
									</tr>
									<tr>
										<td colspan=2 height=26>
											<input type=text name='com_addr1'  id='com_addr1' value='".$db->dt[com_addr1]."' size=50 class='textbox'  style='width:450px'>
										</td>
									</tr>
									<tr>
										<td colspan=2 height=26>
											<input type=text name='com_addr2'  id='com_addr2'  value='".$db->dt[com_addr2]."' size=70 class='textbox'  style='width:450px'> (상세주소)
										</td>
									</tr>
									</table>
								</div>
							</td>
						</tr>
						<tr bgcolor=#ffffff height=110>
							<td class='input_box_title' ><b>기타요청사항</b></td>
							<td class='input_box_item'  colspan=3>";
								if($info_type == "text"){
									$Contents .= "".$db->dt[etc]."<input type=hidden class='textbox' name='etc' value='".$db->dt[etc]." ' id=order_etc style='width:90%'>";
								}else{
									$Contents .= "
									<textarea type=text class='textbox' name='seller_message' value='".$db->dt[etc]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[etc]."</textarea>";
								}
								$Contents .= "
							</td>
						</tr>
				</table>";
$Contents .= "
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
	<td colspan='2' height=300 style='vertical-align:top;'>";


$Contents .= "
		<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
			<col width='15%'>
			<col width='35%'>
			<col width='15%'>
			<col width='35%'>
			<tr height='30'>
				<td class='input_box_title' ><b>결제상태</b></td>
				<td class='input_box_item' colspan='3'>
					<input type='radio' name='type' id='type_".ORDER_STATUS_INCOM_READY."' value='".ORDER_STATUS_INCOM_READY."' ".(($type == ORDER_STATUS_INCOM_READY ) ? ' checked':'')." ><label for='type_".ORDER_STATUS_INCOM_READY."'> ".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label>&nbsp;&nbsp;
					<input type='radio' name='type' id='type_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".(($type == ORDER_STATUS_INCOM_COMPLETE) ? ' checked':'')." checked><label for='type_".ORDER_STATUS_INCOM_COMPLETE."'>  ".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label>&nbsp;&nbsp;
					<input type='radio' name='type' id='type_".ORDER_STATUS_DEFERRED_PAYMENT."' value='".ORDER_STATUS_DEFERRED_PAYMENT."' ".(($type == ORDER_STATUS_DEFERRED_PAYMENT ) ? ' checked':'')."  ><label for='type_".ORDER_STATUS_DEFERRED_PAYMENT."'>  ".getOrderStatus(ORDER_STATUS_DEFERRED_PAYMENT)."</label>
				</td>
			</tr>
			<tr height='30' id='order_method' class='order_method'>
				<td class='input_box_title' ><b>결제타입</b></td>
				<td class='input_box_item' colspan='3'>

					<input type='radio' name='method' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".(($method == ORDER_METHOD_BANK || $method == "") ? ' checked':'')." ><label for='method_".ORDER_METHOD_BANK."'>".getMethodStatus(ORDER_METHOD_BANK)."</label>&nbsp;&nbsp;

					<input type=radio name='method' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."'><label for='method_".ORDER_METHOD_VBANK."'> ".getMethodStatus(ORDER_METHOD_VBANK)."</label>&nbsp;&nbsp;

					<input type=radio name='method' id='method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."'><label for='method_".ORDER_METHOD_CASH."'> ".getMethodStatus(ORDER_METHOD_CASH)."</label>&nbsp;&nbsp;

					<input type=radio name='method' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."'><label for='method_".ORDER_METHOD_CARD."'> ".getMethodStatus(ORDER_METHOD_CARD)."</label>&nbsp;&nbsp;
				</td>
			</tr>
			<tr height='30' id='bank' class='bank'>
				<td class='input_box_title'>무통장계좌 <img src='".$required3_path."'></td>
				<td class='input_box_item' >
				".BankInfo($bank_ix,'false')."
				</td>
				<td class='input_box_title' ><b>무통장 입금예정일</b></td>
				<td class='input_box_item' >
					<input type=text class='textbox numeric' name='bank_input_date' value='".$db->dt[bank_input_date]." ' id=bank_input_date style='width:100px;text-align:center;'>
				</td>
			</tr>

			<tr height='30' id='vbank' class='vbank' style='display : none'>
				<td class='input_box_title'>가상계좌 정보 <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>
				예금주 <input type=text class='textbox ' name='vbank_holder' value='' id='vbank_holder' style='width:100px'> &nbsp;&nbsp;
				은행명 <input type=text class='textbox' name='vbank_name' value='' id='vbank_name' style='width:150px'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				가상계좌 <input type=text class='textbox numeric' name='vbank_num1' value='' id='vbank_num1' style='width:100px'> - 
						<input type=text class='textbox numeric' name='vbank_num2' value='' id='vbank_num2' style='width:100px'> -
						<input type=text class='textbox numeric' name='vbank_num3' value='' id='vbank_num3' style='width:100px'>
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
					카드사명 <input type=text class='textbox numeric' name='card_name' value='' id=card_name style='width:100px'> &nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					카드번호 <input type=text class='textbox numeric' name='card_num1' value='' id='card_num1' style='width:100px'> - 
							<input type=text class='textbox numeric' name='card_num2' value='' id='card_num2' style='width:100px'> -
							<input type=text class='textbox numeric' name='card_num3' value='' id='card_num3' style='width:100px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					카드유효기간 <input type=text class='textbox numeric' name='card_expiry1' value='' id=card_expiry1 style='width:50px'> / 
							<input type=text class='textbox numeric' name='card_expiry2' value='' id=card_expiry2 style='width:50px'>

				</td>
			</tr>
			<tr height='30' id='move_status_tr'>
				<td class='input_box_title'>배송타입 <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>
					<input type=radio name='delivery_method' id='delivery_method_TE' value='TE' checked><label for='delivery_method_TE'> 택배</label>&nbsp;&nbsp;
					<input type=radio name='delivery_method' id='delivery_method_QU' value='QU'><label for='delivery_method_QU'> 퀵서비스</label>&nbsp;&nbsp;
					<input type=radio name='delivery_method' id='delivery_method_TR' value='TR'><label for='delivery_method_TR'> 화물(트럭)</label>&nbsp;&nbsp;
					<input type=radio name='delivery_method' id='delivery_method_SE' value='SE'><label for='delivery_method_SE'> 방문수령</label>&nbsp;&nbsp;
					<input type=radio name='delivery_method' id='delivery_method_DI' value='DI'><label for='delivery_method_DI'> 직배송(회사)</label>&nbsp;&nbsp;
				</td>		
			</tr>
			<tr height='30' id='move_status_tr'>
				<td class='input_box_title'>배송비 <img src='".$required3_path."'></td>
				<td class='input_box_item' >
					<input type=radio name='delivery_basic_policy' id='delivery_basic_policy_1' value='1' checked><label for='delivery_basic_policy_1'> 선불</label>&nbsp;&nbsp;
					<input type=text class='textbox numeric' name='delivery_price' value='".$db->dt[delivery_price]." ' id=delivery_price style='width:100px'> 원&nbsp;&nbsp;
					<input type=radio name='delivery_basic_policy' id='delivery_basic_policy_2' value='2'><label for='delivery_basic_policy_2'> 착불</label>&nbsp;&nbsp;
					<input type=radio name='delivery_basic_policy' id='delivery_basic_policy_3' value='3'><label for='delivery_basic_policy_3'> 무료</label>&nbsp;&nbsp;
				</td>
				<td class='input_box_title' ><b>배송예정일</b></td>
				<td class='input_box_item' >
					<input type=text class='textbox numeric' name='shipping_date' value='".$db->dt[shipping_date]." ' id=shipping_date style='width:100px;text-align:center;'>
				</td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap> 증빙서류 정보</td>
				<td class='input_box_item' nowrap colspan='3'>
					<table width='100%' border='0'>
						<tr>
							<td><input type='radio' name='voucher_div' id='voucher_div_1' style='border:0px;' value='1' ><label for='voucher_div_1'> 개인소득공제</label>
							</td>
						</tr>
						<tr>
							<td>
								<table width='600' border='0' style='padding-left:10px;'>
								<tr>
									<td width='20%'>
										<input type='radio' name='voucher_num_div' id='voucher_num_div_1' style='border:0px;' value='1' ><label for='voucher_num_div_1'> 휴대폰번호입력</label>
									</td>
									<td width='*'>
										<input type='text' class='textbox' name='voucher_phone1' id='voucher_phone1' size='3' maxlength='3' value='".$voucher_phone1."' style='width:30px;'> -
										<input type='text' class='textbox' name='voucher_phone2' id='voucher_phone2' size='4' maxlength='4' value='".$voucher_phone2."'  style='width:40px;'> -
										<input type='text' class='textbox' name='voucher_phone3' id='voucher_phone3' size=4' maxlength='4' value='".$voucher_phone3."' style='width:40px;'>
									</td>
									<td width='30%'>
										사용자명 <input type='text' class='textbox' name='phone_voucher_name' id='phone_voucher_name'  value='".$db->dt[phone_voucher_name]."'  style='width:60px;'>
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
										사용자명 <input type='text' class='textbox' name='card_voucher_name' id='card_voucher_name' value='".$db->dt[card_voucher_name]."'  style='width:60px;'>
									</td>
								</tr-->
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<input type='radio' name='voucher_div' id='voucher_div_2' style='border:0px;' value='2' ><label for='voucher_div_2'> 지출증빙</label>
								<input type='text' class='textbox' name='expense_num1' id='expense_num1' size='5' maxlength='5' value='".$expense_num1."' style='width:40px;'> -
								<input type='text' class='textbox' name='expense_num2' id='expense_num2' size='5' maxlength='5' value='".$expense_num2."' style='width:40px;'> -
								<input type='text' class='textbox' name='expense_num3' id='expense_num3' size='5' maxlength='5' value='".$expense_num3."' style='width:40px;'>
							</td>
						</tr>
						<tr>
							<td>
								<input type='radio' name='voucher_div' id='voucher_div_3' style='border:0px;' value='3' checked><label for='voucher_div_3'> 세금계산서 발급(등록되어 있는 사업자로 발급)</label>
							</td>
						</tr>
						<tr>
							<td>
								<table width='100%' border='0' style='padding-left:10px;'>
								<tr>
									<td>
										<input type='radio' name='certificate_yn' id='certificate_yn_1' style='border:0px;' value='1' ><label for='certificate_yn_1'> 결제완료 후 즉시 발급</label>
									</td>
								</tr>
								<tr>
									<td>
										<input type='radio' name='certificate_yn' id='certificate_yn_2' style='border:0px;' value='2' checked><label for='certificate_yn_2'> 기간별 발급(관리자 수동발급)</label>
									</td>
								</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
</tr>
";



$Contents .= "
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
					<td class='input_box_title' ><b>주문자 아이디</b></td>
					<td class='input_box_item'>
						<input type='text' size='25' name='member_id' id='member_id' class='textbox' value='' validation='false' title='주문자아이디'>
					</td>
					<td class='input_box_title' ><b>주문자 이름 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' >
						<input type='text' size='25' name='rname' id='rname' class='textbox' value='' validation='ture' title='주문자 이름'>
					</td>
				</tr>

				<tr height='30'>
					<td class='input_box_title' ><b>주문자 메일</b></td>
					<td class='input_box_item'>
						<input type='text' size='25' name='r_mail' id='r_mail' class='textbox' value='' validation='ture' title='주문자 메일'>
					</td>
					
					<td class='input_box_title' ><b>수취인 이름 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' >
						<input type='text' size='25' name='bname' id='bname' class='textbox' value='' validation='ture' title='주문자 이름'>
						<input type='radio' name='new_info' id='new_info_1' value='1' title='신규입력'> <label for='new_info_1'> 신규입력 </label> 
						<input type='radio' name='new_info' id='new_info_2' value='2' title='주문정보와 동일'> <label for='new_info_2'> 주문정보와 동일 </label> 
					</td>
				</tr>
				<tr height='30'>
					<td class='input_box_title' ><b>주문자 전화</b></td>
					<td class='input_box_item'>
						<input type='text' size='5' maxlength='3' name='tel1' id='tel1' class='textbox' value='' validation='false' title='주문자 전화'> - 
						<input type='text' size='5' maxlength='4' name='tel2' id='tel2' class='textbox' value='' validation='false' title='주문자 전화'> - 
						<input type='text' size='5' maxlength='4' name='tel3' id='tel3' class='textbox' value='' validation='false' title='주문자 전화'>
					</td>
					
					<td class='input_box_title' ><b>수취인 전화</b></td>
					<td class='input_box_item' >
						<input type='text' size='5' maxlength='3' name='bmember_phone1' id='bmember_phone1' class='textbox' value='' validation='false' title='수취인 핸드폰'> - 
						<input type='text' size='5' maxlength='4' name='bmember_phone2' id='bmember_phone2' class='textbox' value='' validation='false' title='수취인 핸드폰'> - 
						<input type='text' size='5' maxlength='4' name='bmember_phone3' id='bmember_phone3' class='textbox' value='' validation='false' title='수취인 핸드폰'>
					</td>
				</tr>
				<tr height='30'>
					<td class='input_box_title' ><b>주문자 핸드폰 <img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
						<input type='text' size='5' maxlength='3' name='pcs1' id='pcs1' class='textbox' value='' validation='ture' title='주문자 핸드폰'> - 
						<input type='text' size='5' maxlength='4' name='pcs2' id='pcs2' class='textbox' value='' validation='ture' title='주문자 핸드폰'> - 
						<input type='text' size='5' maxlength='4' name='pcs3' id='pcs3' class='textbox' value='' validation='ture' title='주문자 핸드폰'>
					</td>
					<td class='input_box_title' ><b>수취인 핸드폰 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' >
						<input type='text' size='5' maxlength='3' name='bmember_pcs1' id='bmember_pcs1' class='textbox' value='' validation='ture' title='수취인 핸드폰'> - 
						<input type='text' size='5' maxlength='4' name='bmember_pcs2' id='bmember_pcs2' class='textbox' value='' validation='ture' title='수취인 핸드폰'> - 
						<input type='text' size='5' maxlength='4' name='bmember_pcs3' id='bmember_pcs3' class='textbox' value='' validation='ture' title='수취인 핸드폰'>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> <b>배달주소  <img src='".$required3_path."'> </b></td>
					<td class='input_box_item' colspan=3>
						<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
						
						<div id='input_address_area' ><!--style='display:none;'-->
						<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
							<col width='120px'>
							<col width='100px'>
							<col width='*'>
							<tr>
								<td height=26>
									<input type='text' class='textbox' name='zip1' id='zip1' size='5' maxlength='5' value='".$db->dt[zip1]."' readonly> -
									<input type='text' class='textbox' name='zip2' id='zip2' size='5' maxlength='5' value='".$db->dt[zip2]."' readonly>
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
									<input type=text name='addr1'  id='addr1' value='".$db->dt[addr1]."' size=50 class='textbox'  style='width:450px'>
								</td>
							</tr>
							<tr>
								<td height=26 colspan='3'>
									<input type=text name='addr2'  id='addr2'  value='".$db->dt[addr2]."' size=70 class='textbox'  style='width:450px'> (상세주소)
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
							$Contents .= "".$db->dt[etc]."<input type=hidden class='textbox' name='etc' value='".$db->dt[etc]." ' id=order_etc style='width:90%'>";
						}else{
							$Contents .= "
							<textarea type=text class='textbox' name='delivery_message' value='".$db->dt[delivery_message]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[delivery_message]."</textarea>";
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
";

$Contents .= "
<tr>
	<td  height='25' style='padding:10px 0px;'>
		<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>수동수주 상품</b>
	</td>
	<td align=right>";
	if($move_status == "" || $move_status == "MA"){
		$Contents .= "<a href=\"javascript:PopGoodsSelect();\"><img src='../images/".$admininfo["language"]."/btc_goods_add.gif' border='0' align='absmiddle'  style='cursor:pointer;'></a>";
	}
	$Contents .= "
	</td>
</tr>
<tr>
	<td colspan='2' height=300 style='vertical-align:top;'>
	";

$Contents .= "
	<table cellpadding=3 cellspacing=0 border=0 bgcolor=#ffffff width=100% onselect='return false;' align=center class='list_table_box' id='warehouse_move_apply_list'>
				<col width=4% >
				<col width=6% >
				<col width='*' >
				<col width=3% >
				<col width=3% >
				<col width=6% >
				<col width=5% >

				<col width=7% >
				<col width=7% >
				<col width=7% >

				<col width=5% >
				<col width=9% >
				<col width=9% >
				<col width=5% >
				<col width=5% >
				<tr align=center height=30 style='font-weight:bold;' >";

	if($move_status == "" || $move_status == "MA"){
		$Contents .= "
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.input_frm)'></td>";
	}
	
		$Contents .= "
					<td class=m_td >품목코드</td>
					<td class=m_td >품목정보</td>
					<td class=m_td >단위</td>
					<td class=m_td >규격</td>
					<td class=m_td >부가세</td>
					<td class=m_td >재고</td>
					<td class=m_td  nowrap>도매단가<br>(VAT 별도)</td>
					<td class=m_td  >도매공급가<br>(VAT 포함)</td>
					<td class=m_td >세액</td>
					<td class='m_td'  nowrap>수량</td>
					<td class='m_td inner_warehouse_move' nowrap>개별단가<br>(VAT 별도)</td>
					<td class='m_td inner_warehouse_move' nowrap>공급가액<br>(VAT 포함)</td>
					<td class='m_td' >세액</td>
					<td class='m_td >판매가</td>
					<td class='e_td'  nowrap>할인률</td>
				</tr>
	";


/*
$sql = "select g.cid,g.gname, g.gcode, g.admin, g.input_price, g.basic_sellprice , g.ci_ix, g.pi_ix, pi.place_name,  ifnull(sum(ips.stock),0) as stock, gi.* 
		from inventory_goods g 
		right join inventory_goods_item gi  on g.gid =gi.gid
		left join  inventory_place_info pi on g.pi_ix = pi.pi_ix
		left join  inventory_product_stockinfo ips on gi.gid = ips.gid
		$where    
		 $stock_where 
		 group by gi.gid , gi.gi_ix, ips.pi_ix
		 $orderbyString 
		 LIMIT $start, $max
		 ";
*/

/*
	$sql = "select data.*, 
		(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name
		from 
			(select g.cid,g.gname, g.gid, g.gcode, g.admin, gu.buying_price, gu.sellprice , g.ci_ix, wmd.pi_ix, wmd.ps_ix, pi.place_name, ps.section_name, wm.wm_ix, wmd.wmd_ix, wm.apply_date, wm.move_pi_ix , wm.move_ps_ix , wmd.apply_cnt , wmd.delivery_cnt,  wmd.unit, wmd.standard, pi.company_id, ips.vdate, ips.expiry_date, sum(ips.stock) as stock
		from inventory_warehouse_move wm 
		left join inventory_warehouse_move_detail wmd on wm.wm_ix = wmd.wm_ix
		left join inventory_goods g on wmd.gid = g.gid 
		right join inventory_goods_unit gu  on g.gid =gu.gid and wmd.unit = gu.unit 
		right join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit		
		right join  inventory_place_info pi on ips.company_id = pi.company_id and ips.pi_ix = pi.pi_ix 
		left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
		where wm.wm_ix = '".$wm_ix."'
		group by g.gid , gu.unit, ips.pi_ix
		 ) data
		 ";


$db->query($sql);
$order_goods_total = $db->total;
*/
//echo $order_goods_total;

if($move_status == "" || $move_status == "MA"){
	$goods_select_str = " ondblclick=\"javascript:PopGoodsSelect();\"  class='helpcloud' help_height='35' help_html='더블클릭시 품목을 선택할 수 있는 창이 노출되게 됩니다.' style='cursor:pointer;'  ";
}else{
	$goods_select_str = "";
}


if($db->total){

	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

			$ci_ix = $db->dt[ci_ix];
			$gid = $db->dt[gid];
			$gname = $db->dt[gname];
			$gname_str .= $db->dt[gname];
			$order_cnt    = $db->dt[order_cnt];
			$buying_price = $db->dt[buying_price];

			$place_name = $db->dt[place_name];
			$section_name = $db->dt[section_name];
			$company_name = $db->dt[company_name];
			$apply_cnt = $db->dt[apply_cnt];
			$sellprice = $db->dt[sellprice];
			$amount  = $db->dt[amount];
			$unit = $db->dt[unit];
			$stock = $db->dt[stock];
			$wmd_ix = $db->dt[wmd_ix];

			$apply_cnt_sum += $apply_cnt;
			$sellprice_sum += $sellprice;
			$amount_sum += $amount;
			
			$totalprice = $order_cnt*$buying_price;
			$order_totalprice = $order_totalprice + $totalprice;
			//$coper = $coprice / $sellprice * 100;

			//$db->query("SELECT listprice, sellprice,  admin,company, state FROM ".TBL_SHOP_PRODUCT." WHERE id='$pid'");
			//$db->fetch();


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "c"))) {
				$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "c");
			}else{
				$img_str = "../image/no_img.gif";
			}
//echo $move_status;
			

$Contents .="
				<tr height=30 depth=1  >";
	if($move_status == "" || $move_status == "MA"){
		$Contents .= "
					<td align=center>
						
						<input type=checkbox class='nonborder select_gid' id='select_gid'  name=manual_orderinfo[".$i."][select_gid] value='".$db->dt[gid]."'>
					</td>";
	}
		$Contents .= "
					<td align=center id='gid_text'>".$db->dt[gid]." <input type=hidden class='nonborder' id='wmd_ix'  name=manual_orderinfo[".$i."][wmd_ix] value='".$db->dt[wmd_ix]."'> </td>
					<td style='padding:3px;' nowrap>
						<b id='gname'>$gname </b><input type=hidden class='textbox numeric' name='manual_orderinfo[".$i."][gid]' id='gid' value='$gid'>
					</td>
					<td align=center><span  id='unit_text'>".getUnit($db->dt[unit], "basic_unit","","text")."</span><input type=hidden class='textbox numeric' name='manual_orderinfo[".$i."][unit]' id='unit' value='$unit'> </td>
					<td align=center><span  id='standard_text'>".$db->dt[standard]."</span><input type=hidden class='textbox numeric' name='manual_orderinfo[".$i."][standard]' id='standard' value='$standard'> </td>					
					
					<!--td align=center><span  id='vdate_text'>".$db->dt[vdate]."</span><input type=hidden class='textbox numeric' name='manual_orderinfo[".$i."][vdate]' id='vdate' value='".$db->dt[vdate]."'> </td-->
					<td align=center><span  id='expiry_date_text'>".$db->dt[expiry_date]."</span><input type=hidden class='textbox numeric' name='manual_orderinfo[".$i."][expiry_date]' id='expiry_date' value='".$db->dt[expiry_date]."'> </td>
					<td style='text-align:center;'>
						<span class='stock'  id='stock'>".$stock."</span>
					</td>
					<td style='text-align:center;'  id='wholesale_price' >
						
					</td>
					<td class='inner_warehouse_move point' style='text-align:center;'>
						<input type=text class='textbox numeric sellprice' name='manual_orderinfo[".$i."][sellprice]'  id='sellprice' value='".$sellprice."'    size=8 title='수량'  > 
					</td>
					<td class='inner_warehouse_move'  style='text-align:center;'>
						<input type=text class='textbox numeric amount' name='manual_orderinfo[".$i."][amount]'  id='amount' value='".$amount."'  size=8 title='입고수량'  ".($move_status == "MI" ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ")."  > 
					</td>
					<td align=center id='total_price'>".number_format($totalprice)."</td>
				</tr>";
	}

	$Contents = str_replace("<!--apply_cnt_sum-->",$apply_cnt_sum,$Contents);
	$Contents = str_replace("<!--delivery_cnt_sum-->",$delivery_cnt_sum,$Contents);
	$Contents = str_replace("<!--amount_sum-->",$amount_sum,$Contents);

	
}else{
//$Contents .="<tr height=50><td colspan=7 align=center>창고이동 요청품목 내역이  존재 하지 않습니다.</td></tr>";
$Contents .="
				<tr height=30 depth=1 ".$goods_select_str."   >
					<td align=center><input type=checkbox class='nonborder select_gid' id='select_gid'  name=manual_orderinfo[0][select_gid] value='".$db->dt[gid]."'></td>
					<td align=center id='gid_text'>".$db->dt[customer_name]."</td>
					<td style='padding:3px;' nowrap>
						<b id='gname'>$gname </b><input type=hidden class='textbox numeric' name='manual_orderinfo[0][gid]' id='gid' value='$gid'>
					</td>
					<td align=center><span id='unit_text'>".$db->dt[unit_text]."</span><input type=hidden class='textbox numeric' name='manual_orderinfo[0][unit]' id='unit' value='$unit'> </td>
					<td align=center><span id='standard_text'>".$db->dt[standard]."</span><input type=hidden class='textbox numeric' name='manual_orderinfo[0][standard]' id='standard' value='$standard'> </td>

					<td align=center><span id='surtax_text'></span></td>
					<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[0][surtax_div]' id='surtax_div' value='' title='부가세'>

					<td style='text-align:center;'>
						<span class='stock' id='td_stock'>".$stock."</span>
					</td>
					<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[0][stock]' id='stock' value='' title='재고'>

					<td style='text-align:center;' id='td_wholesale_price' >
						
					</td>
					<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[0][wholesale_price]' id='wholesale_price' value='' title='도매단가vat 별도'>

					<td style='text-align:center;' id='td_psprice' >
						
					</td>
					<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[0][psprice]' id='psprice' value='' title='도매공급가 vat 포함'>

					<td style='text-align:center;' id='td_tax_price' >
						
					</td>
					<input type=hidden class='textbox point_color numeric amount' name='manual_orderinfo[0][tax_price]' id='tax_price' value='' title='세액'>
					
					<td class='inner_warehouse_move' style='text-align:center;' id='td_pcount'>
						<input type=text class='textbox point_color numeric amount' name='manual_orderinfo[0][pcount]' onkeyup='calcurate_maginrate(this)' id='pcount' value='$amount' size=4 title='수량'> 
					</td>

					<td class='inner_warehouse_move ' style='text-align:center;'>
						<input type=text class='textbox point_color numeric sellprice' name='manual_orderinfo[0][sellprice]' onkeyup='calcurate_maginrate(this)' id='sellprice' value='$sellprice' size=10 title='단가(vat 별도)'> 원
					</td>

					<td class='inner_warehouse_move ' style='text-align:center;' id='td_total_price'>
						
					</td>
					<input type=hidden class='textbox numeric sellprice' name='manual_orderinfo[0][total_price]' onkeyup='calcurate_maginrate(this)' id='total_price' value='$total_price' size=10 title='공급가액(vat 포함)'>

					<td class='inner_warehouse_move' style='text-align:center;' id='td_order_tax'>
						
					</td>
					<input type=hidden class='textbox numeric amount' name='manual_orderinfo[0][order_tax]' onkeyup='calcurate_maginrate(this)' id='order_tax' value=''title='세액'>
					
					<td align=center class='inner_warehouse_move' style='text-align:center;' id='td_discount'>
						".number_format($discount)."
					</td>
					<input type=hidden class='textbox numeric amount' name='manual_orderinfo[0][discount]' onkeyup='calcurate_maginrate(this)' id='discount' value='$discount'title='할인률'>
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
								<td align='right' style='font-size:12px;font-weight:bold;'>
								<!--품목금액  : <span class='blk' style='font-size:12px;font-family:arial;'>".number_format($order_totalprice)."</span> 원 +
								배송비 : <span class='blk' style='font-size:12px;font-family:arial;'>".number_format($total_delivery_price)."</span> 원 = -->
								총 주문금액 : <span class='blk' style='font-size:12px;font-family:arial;' id='sum_total_price'>".number_format($order_totalprice + $total_delivery_price)."</span> 원</td>
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
		$Contents .= " <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'>";
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
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>

<script language='JavaScript' >
function filterNum(str) {
re = /^$|,/g;

// '$' and ',' 입력 제거
	if(str){
		//return str.replace(re, '');
	}
}

$(document).ready(function (){
	
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
		
		}else if(value == '4'){
			// 가상계좌

			$('#bank').css('display','none');
			$('#vbank').css('display','');
			//$('#cash').css('display','none');
			$('#card').css('display','none');
		}else if(value == '10'){
			//현금
			$('#bank').css('display','none');
			$('#vbank').css('display','none');
			//$('#cash').css('display','');
			$('#card').css('display','none');

			$('#bank').attr('disabled','true');
			$('#vbank').attr('disabled','true');
			$('#card').attr('disabled','true');

		}else if(value == '1'){
			//카드결제
			$('#bank').css('display','none');
			$('#vbank').css('display','none');
			//$('#cash').css('display','none');
			$('#card').css('display','');
		}
	
	
	});

	$('#com_addr').click(function (){
		var value = $('#com_addr').attr('checked');
		 if(value == 'checked'){
			var com_zip_array = $('#com_zip').val().split('-'); 
			$('#zip1').val(com_zip_array[0]);
			$('#zip2').val(com_zip_array[1]);

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
		}else{
			$('#order_method').css('display','');
			$('#bank').css('display','');
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


});

function calcurate_maginrate(){

	var tbody = $('#warehouse_move_apply_list tbody');  	
	var total_rows = tbody.find('tr[depth^=1]').length;  
	
	var thisRow = '';
	
	if($.browser.msie){
		var thisRow = tbody.find('tr[depth^=1]:first');  
	}else{
		var thisRow = tbody.find('tr[depth^=1]:first');  
	}
	if(total_rows == '1'){ 
		
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
			}

			set_totalprice();

        });

		
			if($.browser.msie){
			 // var newRow = tbody.find('tr[depth^=1]');  
			}else{
			 // var newRow = tbody.find('tr[depth^=1]');  
			}
			
	
			//공급가 할인가

	}
}

function set_totalprice(pid){
	var total_price = 0;
	var sumprice = 0;

	$('input[id=total_price]').each(function(){
		var total_price = $(this).val()

		if(total_price > 0){
		sumprice += parseInt(total_price);

		$('#sum_total_price').html(FormatNumber(sumprice)+' 원');
		}
	});
	
}

function zipcode(type) {
	var zip = window.open('../member/zipcode.php?type='+type,'','width=440,height=300,scrollbars=yes,status=no');
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
		dateFormat: 'yymmdd',
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

	if($.browser.msie){
		var thisRow = tbody.find('tr[depth^=1]:last');  
	}else{
		var thisRow = tbody.find('tr[depth^=1]:last');  
	}   

	$('.select_gid').each(function(){
		if($(this).attr('checked') == 'checked'){
			var total_rows = tbody.find('tr[depth^=1]').length;  
			if(total_rows > 1){
				$(this).parent().parent().remove();
			}else{
				thisRow = $(this).parent().parent();
				thisRow.find('#gid_text').html('');   
				thisRow.find('#gid').val('');
				thisRow.find('#gname').html('');
				thisRow.find('#unit').val('');
				thisRow.find('#unit_text').html('');
				thisRow.find('#standard_text').html('');
				thisRow.find('#buying_price').html('');
				thisRow.find('#company_name').html('');
				thisRow.find('#place_name').html('');
				thisRow.find('#section_name').html('');

				thisRow.find('#vdate_text').html('');
				thisRow.find('#expiry_date_text').html('');

				thisRow.find('#wholesale_price').html('');
				thisRow.find('#tax_price').html('');
				thisRow.find('#psprice').html('');
				thisRow.find('#stock').html('');

				thisRow.find('#gid').attr('name','manual_orderinfo[0][gid]');
				thisRow.find('#unit').attr('name','manual_orderinfo[0][unit]');
				thisRow.find('#order_cnt').attr('name','manual_orderinfo[0][order_cnt]');
				//thisRow.find('#buying_price').attr('name','manual_orderinfo[0][buying_price]');
				//thisRow.find('#buying_price').attr('name','manual_orderinfo[0][buying_price]');
				thisRow.find('#standard').attr('name','manual_orderinfo[0][standard]');
				thisRow.find('#pi_ix').attr('name','manual_orderinfo[0][pi_ix]');
				thisRow.find('#ps_ix').attr('name','manual_orderinfo[0][ps_ix]');

				thisRow.find('#vdate').attr('name','manual_orderinfo[0][vdate]');
				thisRow.find('#expiry_date').attr('name','manual_orderinfo[0][expiry_date]');

				thisRow.find('#sellprice').attr('name','manual_orderinfo[0][sellprice]');
				thisRow.find('#order_tax').attr('name','manual_orderinfo[0][order_tax]');
				thisRow.find('#pcount').attr('name','manual_orderinfo[0][pcount]');

			}
		}
	});		
}

function GoodsSelect(gid, gname, unit,unit_text, standard, buying_price, company_name, place_name, section_name, pi_ix, ps_ix, vdate, expiry_date,stock, wholesale_price, sellprice,surtax_div,surtax_text){
	
   var tbody = $('#warehouse_move_apply_list tbody');  	
   var total_rows = tbody.find('tr[depth^=1]').length;  
   var rows = tbody.find('tr[depth^=1]').length;  

	if($.browser.msie){
		var thisRow = tbody.find('tr[depth^=1]:last');  
	}else{
		var thisRow = tbody.find('tr[depth^=1]:last');  
	}
	if(thisRow.find('#gid_text').html() == ''){ 
		
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

		if( surtax_div == '3' || surtax_div == '4'){

			thisRow.find('#td_wholesale_price').html(FormatNumber(wholesale_price)+' 원');
			thisRow.find('#wholesale_price').val(wholesale_price);
			
			thisRow.find('#td_psprice').html(FormatNumber(wholesale_price)+' 원');
			thisRow.find('#psprice').val(wholesale_price);

			thisRow.find('#td_tax_price').html('0 원');
			thisRow.find('#tax_price').val('0');

		}else{

			thisRow.find('#td_wholesale_price').html(FormatNumber(wholesale_price - Math.floor(wholesale_price/11))+' 원');
			thisRow.find('#wholesale_price').val(wholesale_price - Math.floor(wholesale_price/11));

			thisRow.find('#td_psprice').html(FormatNumber(wholesale_price)+' 원');
			thisRow.find('#psprice').val(wholesale_price);

			thisRow.find('#td_tax_price').html(FormatNumber(Math.floor(wholesale_price/11))+' 원');
			thisRow.find('#tax_price').val(Math.floor(wholesale_price/11));
		}
		
	}else{

		
		if($.browser.msie){
		  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
		}else{
		  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
		}
		
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

		if( surtax_div == '3' || surtax_div == '4'){
			newRow.find('#td_wholesale_price').html(FormatNumber(wholesale_price)+' 원');
			newRow.find('#wholesale_price').val(wholesale_price);
			
			newRow.find('#td_psprice').html(FormatNumber(wholesale_price)+' 원');
			newRow.find('#psprice').val(wholesale_price);

			newRow.find('#td_tax_price').html('0 원');
			newRow.find('#tax_price').val('0');
		}else{
			newRow.find('#td_wholesale_price').html(FormatNumber(wholesale_price - Math.floor(wholesale_price/11))+' 원');
			newRow.find('#wholesale_price').val(wholesale_price - Math.floor(wholesale_price/11));
			
			newRow.find('#td_psprice').html(FormatNumber(wholesale_price)+' 원');
			newRow.find('#psprice').val(wholesale_price);

			newRow.find('#td_tax_price').html(FormatNumber(Math.floor(wholesale_price/11))+' 원');
			newRow.find('#tax_price').val(Math.floor(wholesale_price/11));
			
		}
		newRow.find('#sellprice').val('');
		newRow.find('#pcount').val('');
		newRow.find('#td_discount').html('0 %');
		newRow.find('#discount').val('');

		
	}

	var stock_sum = 0;
	$('.stock').each(function(){
		stock_sum += parseInt($(this).html());
	});
	$('#stock_sum').html(stock_sum);
	
	/*
	var apply_cnt_sum = 0;
	$('.apply_cnt').each(function(){
		apply_cnt_sum += parseInt($(this).val());
	});
	$('#apply_cnt_sum').html(apply_cnt_sum);

	var sellprice_sum = 0;
	$('.sellprice').each(function(){
		sellprice_sum += parseInt($(this).val());
	});
	$('#sellprice_sum').html(sellprice_sum);

	var amount_sum = 0;
	$('.amount').each(function(){
		amount_sum += parseInt($(this).val());
	});
	$('#amount_sum').html(amount_sum);
	*/
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

function PopGoodsSelect(){
	//alert($('select[name^=now_company_id]').val());
	ShowModalWindow('../inventory/goods_select.php?page_type=',1000,800,'goods_select');
	//PoPWindow('../inventory/goods_select.php?page_type=',1000,800,'goods_select');
}

function CheckWarehouseMove(obj){
	
	$('#move_company_id').attr('disabled',false);
	$('#now_ps_ix').attr('disabled',false);
	$('#move_ps_ix').attr('disabled',false);
	var check_bool = true;

	if(CheckFormValue(obj)){
		$('.sellprice').each(function(){
			if($(this).val() > $(this).attr('apply_cnt')){
				$.unblockUI;
				alert($(this).val()+'::::'+$(this).attr('apply_cnt')+'출고수량이 요청수량보다 많습니다. 입력값을 확인해주세요');
				check_bool = false;
				return false;
			}
		});
		$('.amount').each(function(){
			if($(this).val() > $(this).attr('sellprice')){
				$.unblockUI;
				alert('입고수량이 출고수량보다 많습니다. 입력값을 확인해주세요');
				check_bool = false;
				return false;
			}
		});
		return check_bool;
	}else{
		if($('select#h_type option:selected').val() == 'IW'){
			$('#move_company_id').attr('disabled',true);
		}else{
			$('#now_ps_ix').attr('disabled',true);
			$('#move_ps_ix').attr('disabled',true);
		}
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
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->strLeftMenu = offline_order_menu();
	$P->strContents = $Contents;
	$P->Navigation = "견적서관리 > 수동수주서 > 수동수주서 작성";
	$P->title = "수동수주서 작성";
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