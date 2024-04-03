<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] < 9){
	header("Location:/admin/store/company.add.php");
}

if($admininfo[admin_id] == "forbiz"){
	//print_r($admininfo);
//	echo $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
}

$shmop = new Shared("b2b_mileage_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$reserve_data = $shmop->getObjectForKey("b2b_mileage_rule");
$reserve_data = unserialize(urldecode($reserve_data));
//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "b2c_mileage"  || $info_type == "" ) ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='mileage_rule.php?info_type=b2c_mileage'>B2C 마일리지 설정</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>

					<table id='tab_03' ".(($info_type == "b2b_mileage" ) ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='b2b_mileage_rule.php?info_type=b2b_mileage'>B2B 마일리지 설정</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>


				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	  </tr>
	</table>
";

////////////////////////////////////////////////////////////////////////////

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>마일리지 지급 정책</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>마일리지 적립범위 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='mileage_use_yn' id='mileage_use_y' value='Y' ".($reserve_data[mileage_use_yn] == "Y" ? "checked":"")."> <label for='mileage_use_y'>전체 사용 </label>
		<input type='radio' name='mileage_use_yn' id='mileage_use_n' value='N' ".($reserve_data[mileage_use_yn] =="N" || $reserve_data[mileage_use_yn] == "" ? "checked":"")."> <label for='mileage_use_n'>본사 상품만 사용</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50'>
		<td class='input_box_title'> <b>마일리지 기본설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='line-height:150%;padding:5px 0px 5px 5px;'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed; padding:0px;'>
			<tr height=27>
				<td>
					<input type='radio' name='mileage_info_use' id='once_mileage_info_use_y' value='Y'  ".($reserve_data[mileage_info_use] == "Y" || $reserve_data[mileage_info_use] == ""  ? "checked":"")." > <label for='once_mileage_info_use_y'> 전체사용</label>
				</td>
			</tr>
			<tr height=27>
				<td style='padding-left:20px;'>
					- 웹사이트 상품 금액의  <input type=text class='textbox' name='goods_mileage_rate' value='".$reserve_data[goods_mileage_rate]."' style='width:60px;' validation='true' title='웹사이트 상품적립금 기본설정'>  % 를 적립합니다.  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H', $reserve_data, "reserve_data")." 
				</td>
			</tr>
			<tr height=27>
				<td style='padding-left:20px;'>
					- 모바일 상품 금액의  <input type=text class='textbox' name='goods_mobile_mileage_rate' value='".$reserve_data[goods_mobile_mileage_rate]."' style='width:60px;' validation='true' title='모바일 상품적립금 기본설정'>  % 를 적립합니다.  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H', $reserve_data, "reserve_data")." 
				</td>
			</tr>
			<tr height=27>
				<td style='padding-left:20px;'>
					<span class=blue>* 신규 상품 등록시 기본으로 적용되며, 상품별로 개별 설정시 본 설정은 적용되지 않습니다.".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
				</td>
			</tr>
			<tr height=27>
				<td>
					<input type='radio' name='mileage_info_use' id='mileage_info_use_p' value='P'  ".($reserve_data[mileage_info_use] == "P"? "checked":"")." > 
					<label for='mileage_info_use_p'> 결제수단별 사용(중복결제시 결제금액이 높은 수단으로 적립됩니다.)</label>
				</td>
			</tr>
			<tr height=27>
				<td style='padding-left:20px;'>
					<table width='99%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
					<col width='12%' />
					<col width='12%' />
					<col width='12%' />
					<col width='12%' />
					<col width='12%' />
					<col width='12%' />
					<col width='12%' />
					<col width='12%' />
					<tr align='center' height='30'>
						<td class='s_td'>구분</td>
						<td class='m_td'><input type='radio' name='basic_rate' id='basic_rate_1' value='1' style='position:relative;top:-2px;' ".($reserve_data[basic_rate] == "1" || $reserve_data[basic_rate] == ""? "checked":"")."> <label for='basic_rate_1'>현금</label></td>
						<td class='m_td'><input type='radio' name='basic_rate' id='basic_rate_2' value='2' style='position:relative;top:-2px;' ".($reserve_data[basic_rate] == "2" ? "checked":"")."><label for='basic_rate_2'>무통장</label></td>
						<td class='m_td'><input type='radio' name='basic_rate' id='basic_rate_3' value='3' style='position:relative;top:-2px;' ".($reserve_data[basic_rate] == "3" ? "checked":"")."><label for='basic_rate_3'>가상계좌</label></td>
						<td class='m_td'><input type='radio' name='basic_rate' id='basic_rate_4' value='4' style='position:relative;top:-2px;' ".($reserve_data[basic_rate] == "4" ? "checked":"")."><label for='basic_rate_4'>실시간계좌</label></td>
						<td class='m_td'><input type='radio' name='basic_rate' id='basic_rate_5' value='5' style='position:relative;top:-2px;' ".($reserve_data[basic_rate] == "5" ? "checked":"")."><label for='basic_rate_5'>카드</label></td>
						<td class='m_td'><input type='radio' name='basic_rate' id='basic_rate_6' value='6' style='position:relative;top:-2px;' ".($reserve_data[basic_rate] == "6" ? "checked":"")."><label for='basic_rate_6'>휴대폰</label></td>
						<td class='e_td'><input type='radio' name='basic_rate' id='basic_rate_7' value='7' style='position:relative;top:-2px;' ".($reserve_data[basic_rate] == "7" ? "checked":"")."><label for='basic_rate_7'>예치금</label></td>
					</tr>
					<tr align='center' height='30'>
						<td class='list_box_td list_bg_gray'>웹사이트</td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='goods_mileage_rate_1' value='".$reserve_data[goods_mileage_rate_1]."' style='width:70%;'> </td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='goods_mileage_rate_2' value='".$reserve_data[goods_mileage_rate_2]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='goods_mileage_rate_3' value='".$reserve_data[goods_mileage_rate_3]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='goods_mileage_rate_4' value='".$reserve_data[goods_mileage_rate_4]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='goods_mileage_rate_5' value='".$reserve_data[goods_mileage_rate_5]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='goods_mileage_rate_6' value='".$reserve_data[goods_mileage_rate_6]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='goods_mileage_rate_7' value='".$reserve_data[goods_mileage_rate_7]."' style='width:70%;'></td>
					</tr>
					<tr align='center' height='30'>
						<td class='list_box_td list_bg_gray'>모바일</td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='mobile_mileage_rate_1' value='".$reserve_data[mobile_mileage_rate_1]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='mobile_mileage_rate_2' value='".$reserve_data[mobile_mileage_rate_2]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='mobile_mileage_rate_3' value='".$reserve_data[mobile_mileage_rate_3]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='mobile_mileage_rate_4' value='".$reserve_data[mobile_mileage_rate_4]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='mobile_mileage_rate_5' value='".$reserve_data[mobile_mileage_rate_5]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='mobile_mileage_rate_6' value='".$reserve_data[mobile_mileage_rate_6]."' style='width:70%;'></td>
						<td class='list_box_td'><input type='text' class='textbox integer numeric' name='mobile_mileage_rate_7' value='".$reserve_data[mobile_mileage_rate_7]."' style='width:70%;'></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr height=27>
				<td>
					<input type='radio' name='mileage_info_use' id='mileage_info_use_n' value='N'  ".($reserve_data[mileage_info_use] == "N"? "checked":"")." > 
					<label for='mileage_info_use_n'> 미사용</label>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'>
			<b>회원가입 적립금 설정 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			신규 회원가입시  <input type=text class='textbox' name='join_mileage_rate' value='".$reserve_data[join_mileage_rate]."' style='width:60px;' validation='true' title='회원가입 적립금 설정'> 원을 적립합니다.</span> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I' ,$reserve_data ,"reserve_data")."<br>
			
		</td>
	</tr>

	</table>";

////////////////////////////////////////////////////////////////////////////

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='20%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>마일리지 지급일정 관리</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<!--tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>지급자동 사용 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type='radio' name='mileage_auto_yn' id='mileage_auto_y' value='Y' ".($reserve_data[mileage_auto_yn] == "Y" ? "checked":"")."><label for='mileage_auto_y'> 사용 </label>
		<input type='radio' name='mileage_auto_yn' id='mileage_auto_n' value='N' ".($reserve_data[mileage_auto_yn] =="N" || $reserve_data[mileage_auto_yn] ==""? "checked":"")."> <label for='mileage_auto_n'>  미사용 * 미사용시 관리자가 직접 수정 처리합니다. </label></td>
	</tr-->
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title' rowspan='2'> <b>마일리지 적립일 상세설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table width='100%' cellpadding=4 cellspacing=0 border='0' align='left' style='table-layout:fixed; padding:5px;'>
			<tr>
				<td>
					<input type='radio' name='mileage_add_setup' id='mileage_add_setup' value='C'  ".($reserve_data[mileage_add_setup] == "C" || $reserve_data[mileage_add_setup] == ""  ? "checked":"")." > 구매확정일<!--로 부터 
						<select name='order_compalte_time' style='width:80px;'>
							<option value='0' ".CompareReturnValue("0",$reserve_data[order_compalte_time],"selected").">즉시</option>
							<option value='1' ".CompareReturnValue("1",$reserve_data[order_compalte_time],"selected").">1일</option>
							<option value='2' ".CompareReturnValue("2",$reserve_data[order_compalte_time],"selected").">2일</option>
							<option value='3' ".CompareReturnValue("3",$reserve_data[order_compalte_time],"selected").">3일</option>
						</select> (후) '적립완료' 처리 합니다.-->
				</td>
			</tr>";
/*
$Contents01 .= "
			<tr>
				<td>
					<input type='radio' name='mileage_add_setup' id='mileage_add_setup' value='S'  ".($reserve_data[mileage_add_setup] == "S"   ? "checked":"")." > 배송완료일<!--로 부터 
						<select name='order_shipping_time' style='width:80px;'>
							<option value='0'>즉시</option>";
						for($i=1; $i<=15; $i++){
							$Contents01 .= "	<option value='".$i."' ".CompareReturnValue($i,$reserve_data[order_shipping_time],"selected").">".$i."일</option>";
						}
			$Contents01 .= "
						</select> (후) '적립완료' 처리 합니다.-->
				</td>
			</tr>";
*/
$Contents01 .= "
			</table>
		</td>
	</tr>
</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='20%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>마일리지 사용정책</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'> <b>마일리지 사용제한 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table width='100%' cellpadding=4 cellspacing=0 border='0' align='left' style='table-layout:fixed; padding:5px;'>
			<col width='100%' />
			<tr>
				<td > 
					- 일반 상품 구매 합계액이  <input type=text class='textbox' name='total_order_price' value='".$reserve_data[total_order_price]."' style='width:60px;' validation='true' title='Mileage In-Use제한 설정'> 원 이상 상품 구매시사용 가능(무제한이일 경우 0원입력)
				</td>
			</tr>
			<!--<tr>
				<td>
					- 서비스 상품 구매 합계액이  <input type=text class='textbox' name='service_total_order_price' value='".$reserve_data[service_total_order_price]."' style='width:60px;' validation='true' title='Mileage In-Use제한 설정'> 원 이상 상품 구매시사용 가능(무제한이일 경우 0원 입력)
				</td>
			</tr>-->
			<tr>
				<td> 
					<span class=blue>* 신규 상품 등록시 기본으로 적용되며, 상품별로 개별 설정시 본 설정은 적용되지 않습니다.".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
				<td>
			</tr>
			<tr>
				<td>
					- 보유 적림금이   <input type=text class='textbox' name='min_mileage_price' value='".$reserve_data[min_mileage_price]."' style='width:60px;' validation='true' title='Mileage In-Use제한 설정'> 원 이상일때 상품 구매시 사용 가능(제한이 없을경우 0입력)
				</td>
			</tr>
			<tr>
				<td > 
					<input type=radio name='mileage_one_use_type'  id='once_mileage_one_use_type_1' value='1' ".($reserve_data[mileage_one_use_type] == "1" ? "checked":"")." > 1회 사용한도 최대  <input type=text class='textbox' name='use_mileage_max' value='".$reserve_data[use_mileage_max]."' style='width:60px;' id='mileage__one_use_type' ".($reserve_data[mileage_one_use_type] == "1" ? "validation='true'":"validation='false'")." title='Mileage 1회 In-Use 한도'> 원  까지만 사용 가능 * 0원일 경우 전액적립금 사용 가능 합니다.
				</td>
			</tr>
			<tr>
				<td>
					<input type=radio name='mileage_one_use_type' id='once_mileage_one_use_type_2' value='2' ".($reserve_data[mileage_one_use_type] == "2" ? "checked":"")." > 1회 사용한도 상품 구매 합계액의 <input type=text class='textbox' name='max_goods_sum_rate' value='".$reserve_data[max_goods_sum_rate]."' style='width:60px;' id='max_goods_sum_rate' ".($reserve_data[mileage_one_use_type] == "2" ? "validation='true'":"validation='false'")." title='Mileage 1회 In-Use 한도'>%  까지만 사용 가능 * 100%시 전액 적립금 사용 가능합니다.
				</td>
			</tr>
			</table>
		</td>
	</tr>

	
</table>";

////////////////////////////////////////////////////////////////////////////

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='20%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>마일리지 사용 및 소멸기간 설정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>마일리지 사용순서 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='date_asc' id='date_asc_a' value='A' ".($reserve_data[date_asc] == "A" || $reserve_data[date_asc] == "" ? "checked":"")."><label for='date_asc_a'> 과거 적립일 순 </label>
		<!--input type='radio' name='date_asc' id='date_asc_d' value='D' ".($reserve_data[date_asc] =="D" ? "checked":"")."><label for='date_asc_d'> 최근 적립일 순</label--></td>
	</tr>

	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'> <b>마일리지 자동 소멸 기간<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			적립일로 부터 &nbsp;&nbsp;
			<select name='cancel_year' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=10; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$reserve_data[cancel_year],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 년  &nbsp;&nbsp;
			<select name='cancel_month' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=12; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$reserve_data[cancel_month],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 개월 지난 미사용 마일리지 자동 소멸
		</td>
	</tr>
	
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'>
			<b>휴면 회원 자동 소멸 기간<img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			최근 주문일로부터 &nbsp;&nbsp;
			<select name='order_year' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=10; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$reserve_data[order_year],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 년  &nbsp;&nbsp;
			<select name='order_month' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=12; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$reserve_data[order_month],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 개월 지난 회원으로 마일리지가 &nbsp;&nbsp;
			<input type='text' name='order_member_mileage' value='".$reserve_data[order_member_mileage]."' style='width:70px;'>&nbsp;&nbsp; 미만인 회원은 휴면회원으로 간주하여 회사는 해당 회원의 적립된 마일리지를 회수 합니다.
		</td>
	</tr>
	
</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px;'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='mileage_rule.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>
<input type='hidden' name='act' value='b2b_mileage'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";


  $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .=  HelpBox("마일리지/포인트 관리", $help_text, 100);

$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 마일리지/포인트 관리 > 마일리지/포인트설정";
$P->title = "마일리지/포인트설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>