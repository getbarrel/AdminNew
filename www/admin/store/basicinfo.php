<?
include("../class/layout.class");
include("../buyingservice/buying.lib.php");


if($info_type == ""){
	$info_type = "basic";
}
$act = "update";

$db = new Database;
$db2 = new Database;

/*
사업자 정보 추가 작업 kbk 13/04/13
opening_time varchar(255) null default '' comment '몰운영시간텍스트';
cs_phone varchar(15) null default '' comment 'cs전화번호';
return_zip varchar(7) null default '' comment '반품우편번호';
return_addr1 varchar(255) null default '' comment '반품주소';
return_addr2 varchar(255) null default '' comment '반품상세주소';
return_disp enum('Y','N') not null default 'N' comment '반품주소노출여부';
return_use enum('Y','N') not null default 'N' comment '반품주소사용여부';
officer_name varchar(20) null default '' comment '개인정보책임자';
officer_email varchar(150) null default '' comment '개인정보책임자이메일';
*/

$company_id = $admininfo[company_id];

//echo $company_id;
if($info_type == "basic" || $info_type == ""){
	$sql = "SELECT 
				ccd.*,
				csd.*,
				ccw.commercial_disp,ccw.ca_country,ccw.ca_code,ccw.sc_code,ccw.floor,ccw.line,ccw.no,
				ccw.tel as ws_tel ,ccw.charge_phone as ws_charge_phone,ccw.kakao_phone,ccw.kakao_id,ccw.facebook,ccw.twitter,ccw.qq,ccw.wechat, ccd.cs_phone, csd.cs_phone as seller_cs_phone
			FROM 
				common_company_detail ccd,
				common_seller_detail csd 
				left join common_company_wholesale ccw on (csd.company_id=ccw.company_id) 
			where 
				ccd.company_id = csd.company_id 
				and ccd.company_id = '".$admininfo[company_id]."'";

}else if($info_type == "person_info"){

	$sql = "SELECT * FROM common_company_detail ccd, common_seller_detail csd where ccd.company_id = csd.company_id and ccd.company_id = '".$admininfo[company_id]."'";

}else if($info_type == "shop_info"){

	$sql = "SELECT * FROM common_company_detail ccd, common_seller_detail csd where ccd.company_id = csd.company_id and ccd.company_id = '".$admininfo[company_id]."'";

}
$db->query($sql);
$db->fetch();
$cs_phone = explode("-",$db->dt[cs_phone]);

if(count($cs_phone) < 3){
	$re_cs_phone = $cs_phone;
	$cs_phone[0] = "";
	$cs_phone[1] = $re_cs_phone[0];
	$cs_phone[2] = $re_cs_phone[1];
}

$shipping_phone = explode("-",$db->dt[shipping_phone]);


if(count($shipping_phone) < 3){
	$re_shipping_phone = $shipping_phone;
	$shipping_phone[0] = "";
	$shipping_phone[1] = $re_shipping_phone[0];
	$shipping_phone[2] = $re_shipping_phone[1];
}

$seller_type	= $db->dt[seller_type];
$seller_type_array = explode("|",$seller_type);

if(is_array($seller_type_array)){
	$checked_1 = (in_array('1',$seller_type_array) ? "checked":"");
}
if(is_array($seller_type_array)){
	$checked_2 = (in_array('2',$seller_type_array) ? "checked":"");
}
if(is_array($seller_type_array)){
	$checked_3 = (in_array('3',$seller_type_array) ? "checked":"");
}
if(is_array($seller_type_array)){
	$checked_4 = (in_array('4',$seller_type_array) ? "checked":"");
}
if(is_array($seller_type_array)){
	$checked_5 = (in_array('5',$seller_type_array) ? "checked":"");
}

$com_number = explode("-",$db->dt[com_number]);
$corporate_number = explode("-",$db->dt[corporate_number]);
$com_phone = explode("-",$db->dt[com_phone]);
if(count($com_phone) < 3){
	$re_com_phone = $com_phone;
	$com_phone[0] = "";
	$com_phone[1] = $re_com_phone[0];
	$com_phone[2] = $re_com_phone[1];
}

$com_fax = explode("-",$db->dt[com_fax]);
$com_mobile = explode("-",$db->dt[com_mobile]);
$customer_phone = explode("-",$db->dt[customer_phone]);
$customer_mobile = explode("-",$db->dt[customer_mobile]);
$tax_person_phone = explode("-",$db->dt[tax_person_phone]);
$tax_person_mobile = explode("-",$db->dt[tax_person_mobile]);

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td align='left' colspan=4 > ".GetTitleNavigation("사업자정보", "상점관리 > 쇼핑몰 환경설정 > 사업자정보")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'>
		<div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "basic" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=basic&company_id=".$company_id."'>사업자 정보</a></td>
						<th class='box_03'></th>
					</tr>
					</table>

					<table id='tab_03' ".($info_type == "person_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >
						<a href='?info_type=person_info&company_id=".$company_id."'>담당자 정보</a></td>
						<th class='box_03'></th>
					</tr>
					</table>";
					if($admininfo[mall_type] != "H"){
					$Contents01 .= "
					<table id='tab_03' ".($info_type == "shop_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >
							<a href='?info_type=shop_info&company_id=".$company_id."'>상점 기본정보</a>
						</td>
						<th class='box_03'></th>
					</tr>
					</table>";
					}
					$Contents01 .= "
				</td>
				<td style='width:600px;text-align:right;vertical-align:bottom;padding:0 0 10px 0'>
				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
</table>";

if($info_type == "basic" || $info_type == ""){

	$com_zip = explode("-",$db->dt[com_zip]);
	$return_zip = explode("-",$db->dt[return_zip]);
	$seller_date = explode(" ",$db->dt[seller_date]);

	$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>거래처 등록</b>
			</td>
		</tr>
	</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
		<colgroup>
		<col width='16%' />
		<col width='34%' style='padding:0px 0px 0px 10px'/>
		<col width='16%' />
		<col width='34%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr>
		<td class='input_box_title'> <b>사업자 코드 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
		<input type=text name='company_code' value='".$db->dt[company_code]."' class='textbox'  style='width:80px' validation='true' title='사업자 코드'>
		</td>
		<td class='input_box_title'> <b>쇼핑몰 시작일</b></td>
		<td class='input_box_item'><input type=text  id='seller_date' name='seller_date' value='".$seller_date[0]."' class='textbox'  style='width:80px' validation='false' title='쇼핑몰 시작일'></td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>사업자 구분 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type='radio' id='seller_division_1' name='seller_division' value='1' ".($db->dt[seller_division] == "1" ? "checked":"")."> <label for='seller_division_1'>일반</label> &nbsp;&nbsp;
			<input type='radio' id='seller_division_2' name='seller_division' value='2' ".($db->dt[seller_division] == "2" ? "checked":"")."> <label for='seller_division_2'>가맹점</label> &nbsp;&nbsp;
		</td>
		<td class='input_box_title'> <b>국내외 구분 <img src='".$required3_path."'> </b>   </td>
		<td class='input_box_item'>
			<input type='radio' name='nationality' id='nationality_1' value='I' ".($db->dt[nationality] == "I" ? "checked":"")."> <label for='nationality_1'>국내</label> &nbsp;&nbsp;
			<input type='radio' name='nationality' id='nationality_2' value='O' ".($db->dt[nationality] == "O" ? "checked":"")."> <label for='nationality_2'>해외</label> &nbsp;&nbsp;
			<input type='radio' name='nationality' id='nationality_3' value='D' ".($db->dt[nationality] == "D" ? "checked":"")."> <label for='nationality_3'>기타</label> &nbsp;&nbsp;
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'><b>사업장 유형 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item' colspan='3'>
			<input type='checkbox' id = 'sales_vendor' name='seller_type[]' value='1' $checked_1> <label for='sales_vendor'>국내매출</label> &nbsp;
			<input type='checkbox' id = 'supply_vendor' name='seller_type[]' value='2' $checked_2> <label for='supply_vendor'>국내매입</label> &nbsp;
			<input type='checkbox' id = 'oversea_sales' name='seller_type[]' value='3' $checked_3> <label for='oversea_sales'>해외수출</label> &nbsp;
			<input type='checkbox' id = 'oversea_supply' name='seller_type[]' value='4' $checked_4> <label for='oversea_supply'>해외수입</label> &nbsp;
			<input type='checkbox' id = 'outsourcing' name='seller_type[]' value='5' $checked_5> <label for='outsourcing'>외주물류창고</label>
		</td>

	</tr>
	<tr>
	    <td class='input_box_title'> <b>사업자 유형 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item' colspan='3'>
			<input type='radio' id='com_div_R' name='com_div' value='R' ".($db->dt[com_div] == "R" ? "checked":"")."> <label for='com_div_R'>법인사업자</label> &nbsp;&nbsp;
			<input type='radio' id='com_div_P' name='com_div' value='P' ".($db->dt[com_div] == "P" ? "checked":"")."> <label for='com_div_P'>개인사업자</label> &nbsp;&nbsp;
			<input type='radio' id='com_div_S' name='com_div' value='S' ".($db->dt[com_div] == "S" ? "checked":"")."> <label for='com_div_S'>간이과세자</label> &nbsp;&nbsp;
			<input type='radio' id='com_div_E' name='com_div' value='E' ".($db->dt[com_div] == "E" ? "checked":"")."> <label for='com_div_E'>면세사업자</label> &nbsp;&nbsp;
			<!--<input type='radio' name='com_div' value='I' ".($db->dt[com_div] == "I" ? "checked":"")."> <label for='com_div_p'>수출입업체</label> &nbsp;&nbsp;-->
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>사업자명 (상호명) <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type=text name='com_name' id='com_name' value='".$db->dt[com_name]."' class='textbox'  style='width:150px' validation='true' title='상호명'>
		</td>
	    <td class='input_box_title'> <b>대표자명 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'><input type=text  id='com_ceo' name='com_ceo' value='".$db->dt[com_ceo]."' class='textbox'  style='width:60px' validation='true' title='대표자명'></td>
	</tr>
	<tr>
		<td class='input_box_title'><b>사업자번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='com_number_1' id='com_number_1' value='".$com_number[0]."' maxlength=3 style='width:20px'  class='textbox' com_numeric=true validation='true' title='사업자번호'> -
			<input type=text name='com_number_2' id='com_number_2' value='".$com_number[1]."' maxlength=2 style='width:20px' class='textbox' com_numeric=true validation='true' title='사업자번호'> -
			<input type=text name='com_number_3' id='com_number_3' value='".$com_number[2]."' maxlength=5 style='width:40px' class='textbox' com_numeric=true validation='true' title='사업자번호'>
			<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
		</td>
		<td class='input_box_title'><b>법인번호</b></td>
		<td class='input_box_item'>
			<input type=text name='corporate_number_1' id='corporate_number_1' value='".$corporate_number[0]."' maxlength=6 style='width:60px'  class='textbox' com_numeric=true validation='true' title='법인번호'> -
			<input type=text name='corporate_number_2' id='corporate_number_2' value='".$corporate_number[1]."' maxlength=7 style='width:70px' class='textbox' com_numeric=true validation='true' title='법인번호'> 
			<div style='display:inline;padding:2px;' class=small>예) XXXXXX-XXXXXXX</div>
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>업태 <img src='".$required3_path."'> </b>   </td>
		<td class='input_box_item'><input type=text name='com_business_status' value='".$db->dt[com_business_status]."' class='textbox'  style='width:250px' validation='false' title='업태'></td>
	     <td class='input_box_title' > <b>업종 <img src='".$required3_path."'> </b>   </td>
		<td class='input_box_item'><input type=text name='com_business_category' value='".$db->dt[com_business_category]."' class='textbox'  style='width:250px' validation='false' title='업종'></td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>물류창고사용여부 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type='radio' id='is_wharehouse_11' name='is_wharehouse' value='1'  ".($db->dt[is_wharehouse] == 1 ? "checked":"")."> <label for='is_wharehouse_11'>사용</label> &nbsp;&nbsp;
			<input type='radio' id='is_wharehouse_22' name='is_wharehouse' value='0' ".($db->dt[is_wharehouse] == 0 ? "checked":"")." > <label for='is_wharehouse_22'>미사용</label> &nbsp;&nbsp;
		</td>
		<td class='input_box_title'> <b>통신판매업 번호</b></td>
		<td class='input_box_item'>
			<input type='text' class='textbox' name='online_business_number' value='".$db->dt[online_business_number]."' style='width:250px' title='통신판매업 번호' validation='false'>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>전화번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<div style='float:left' class='p_type_3'>
				<input type=text class='textbox p_type_3' name='com_phone1' value='".$com_phone[0]."' maxlength=3 size=3 validation='true' title='전화' numeric=true> -
				<input type=text class='textbox p_type_3' name='com_phone2' value='".$com_phone[1]."' maxlength=4 size=5 validation='true' title='전화' numeric=true> -
				<input type=text class='textbox p_type_3' name='com_phone3' value='".$com_phone[2]."' maxlength=4 size=5 validation='true' title='전화' numeric=true>
			</div>
			<div style='float:left; display:none' class='p_type_2'>
				<input type=text class='textbox p_type_2' name='com_phone2' value='".$com_phone[1]."' maxlength=4 size=5 validation='true' title='전화' numeric=true> -
				<input type=text class='textbox p_type_2' name='com_phone3' value='".$com_phone[2]."' maxlength=4 size=5 validation='true' title='전화' numeric=true>
			</div>
			<div style='float:left'>
				<input type='radio' name='com_phon_type' id='com_phon_type_0' value='0' validation=false title='대표전화 타입' ".($com_phone[0] != "" ? "checked":"")."><label for='com_phon_type_0'>지역번호포함</label> &nbsp;&nbsp;
				<input type='radio' name='com_phon_type' id='com_phon_type_1' value='1' validation=false title='대표전화 타입' ".($com_phone[0] == "" ? "checked":"")."><label for='com_phon_type_1'>지역번호불포함</label>
			</div>
		</td>
		<td class='input_box_title'> <b>핸드폰 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='com_mobile_1' id='com_mobile_1' value='".$com_mobile[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='true' title='핸드폰'> -
			<input type=text name='com_mobile_2' id='com_mobile_2' value='".$com_mobile[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='핸드폰'> -
			<input type=text name='com_mobile_3' id='com_mobile_3' value='".$com_mobile[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='핸드폰'>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>이메일 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='이메일' email=true>
		</td>
		<td class='input_box_title'> <b>팩스</b></td>
		<td class='input_box_item'>
			<input type=text name='com_fax_1' id='com_fax_1' value='".$com_fax[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='false' title='팩'> -
			<input type=text name='com_fax_2' id='com_fax_2' value='".$com_fax[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='false' title='팩스'> -
			<input type=text name='com_fax_3' id='com_fax_3' value='".$com_fax[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='false' title='팩스'>
		</td>
	</tr>
	<tr height=90>
		<td class='input_box_title'> <b>주소 <img src='".$required3_path."'> </b>    </td>
		<td class='input_box_item' colspan=3>
			<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
			<div id='input_address_area' ><!--style='display:none;'-->
			<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='70px'>
				<col width='*'>
				<tr>
					<td height=26>
						<input type='text' class='textbox' name='com_zip' id='zip_b_1' style='width:60px' maxlength='15' value='".$db->dt[com_zip]."' readonly>
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[com_addr1]."' size=50 class='textbox'  style='width:300px'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[com_addr2]."' size=70 class='textbox'  style='width:300px'> (상세주소)
					</td>
				</tr>
			</table>
		</div>
		</td>
	</tr>";

	$ws_tel = explode("-",$db->dt[ws_tel]);
	$ws_charge_phone = explode("-",$db->dt[ws_charge_phone]);
	$kakao_phone = explode("-",$db->dt[kakao_phone]);

$Contents01 .= "
	<!--
	<tr>
		<td class='input_box_title'> <b>도매상권 사용 <img src='".$required3_path."'> </b>    </td>
		<td class='input_box_item' colspan=3>
			<input type='radio' id='commercial_disp_n' name='commercial_disp' value='N'  ".($db->dt[commercial_disp] == "N" || $db->dt[commercial_disp] == "" ? "checked":"")."> <label for='commercial_disp_n'>미사용</label> &nbsp;&nbsp;
			<input type='radio' id='commercial_disp_y' name='commercial_disp' value='Y' ".($db->dt[commercial_disp] == 'Y' ? "checked":"")." > <label for='commercial_disp_y'>사용</label> 
		</td>
	</tr>
	-->
	<tr class='wholesale_tr wholesale_validation' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>상권선택 <img src='".$required3_path."'> </b>    </td>
		<td class='input_box_item'>
			".getCommercialCountry($db->dt[ca_country],"select","onchange=\"window.frames['act'].location.href='../buyingservice/commercial_area.soapload.php?trigger='+this.value+'&target=ca_code&form=edit_form'\"")." ".getSoapCommercialAreaInfo($db->dt[ca_country],$db->dt[ca_code],"onchange=\"window.frames['act'].location.href='../buyingservice/shopping_center.soapload.php?trigger='+this.value+'&target=sc_code&form=edit_form'\"")."
		</td>
		<td class='input_box_title'> <b>상가선택 <img src='".$required3_path."'> </b>    </td>
		<td class='input_box_item'>
			".getSoapShoppingCenter($db->dt[sc_code],"select","onchange=\"window.frames['act'].location.href='../buyingservice/shopping_center_info.soapload.php?trigger='+this.value+'&target=floor&form=edit_form'\"")."
		</td>
	</tr>
	<tr class='wholesale_tr wholesale_validation' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>층/라인/호수 선택 <img src='".$required3_path."'> </b>    </td>
		<td class='input_box_item' colspan='3'>
			".getSoapShoppingCenterFloorInfo($db->dt[sc_code],$db->dt[floor])." 
			".getSoapShoppingCenterLineInfo($db->dt[sc_code],$db->dt[line])." 
			".getSoapShoppingCenterNoInfo($db->dt[sc_code],$db->dt[no])."
		</td>
	</tr>
	<tr class='wholesale_tr wholesale_validation' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>매장 전화번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='ws_tel_1' id='ws_tel_1' value='".$ws_tel[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='true' title='매장 전화번호'> -
			<input type=text name='ws_tel_2' id='ws_tel_2' value='".$ws_tel[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='매장 전화번호'> -
			<input type=text name='ws_tel_3' id='ws_tel_3' value='".$ws_tel[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='매장 전화번호'>
		</td>
		<td class='input_box_title'> <b>담당자 핸드폰 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='ws_charge_phone_1' id='ws_charge_phone_1' value='".$ws_charge_phone[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='true' title='담당자 핸드폰'> -
			<input type=text name='ws_charge_phone_2' id='ws_charge_phone_2' value='".$ws_charge_phone[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='담당자 핸드폰'> -
			<input type=text name='ws_charge_phone_3' id='ws_charge_phone_3' value='".$ws_charge_phone[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='담당자 핸드폰'>
		</td>
	</tr>
	<tr class='wholesale_tr' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>카카오톡 핸드폰번호 </b></td>
		<td class='input_box_item'>
			<input type=text name='kakao_phone_1' id='kakao_phone_1' value='".$kakao_phone[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='false' title='카카오톡 핸드폰번호'> -
			<input type=text name='kakao_phone_2' id='kakao_phone_2' value='".$kakao_phone[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='false' title='카카오톡 핸드폰번호'> -
			<input type=text name='kakao_phone_3' id='kakao_phone_3' value='".$kakao_phone[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='false' title='카카오톡 핸드폰번호'>
		</td>
		<td class='input_box_title'> <b>카카오톡 ID </b></td>
		<td class='input_box_item'>
			<input type=text name='kakao_id' id='kakao_id' value='".$db->dt[kakao_id]."' style='width:130px' class='textbox' validation='false' title='카카오톡 ID'>
		</td>
	</tr>
	<tr class='wholesale_tr' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>페이스북 </b></td>
		<td class='input_box_item'>
			<input type=text name='facebook' id='facebook' value='".$db->dt[facebook]."' style='width:130px' class='textbox' validation='false' title='페이스북'>
		</td>
		<td class='input_box_title'> <b>트위터 </b></td>
		<td class='input_box_item'>
			<input type=text name='twitter' id='twitter' value='".$db->dt[twitter]."' style='width:130px' class='textbox' validation='false' title='트위터'>
		</td>
	</tr>
	<tr class='wholesale_tr' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>QQ(중국) </b></td>
		<td class='input_box_item'>
			<input type=text name='qq' id='qq' value='".$db->dt[qq]."' style='width:130px' class='textbox' validation='false' title='QQ(중국)'>
		</td>
		<td class='input_box_title'> <b>WeChat </b></td>
		<td class='input_box_item'>
			<input type=text name='wechat' id='wechat' value='".$db->dt[wechat]."' style='width:130px' class='textbox' validation='false' title='WeChat'>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'><b>사업자 인감도장 </b></td>";

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."/company_stamp_".$company_id.".gif")){
			$stamp_bool="false";
		}else{
			$stamp_bool="false";
		}

$Contents01 .= "<td class='input_box_item' colspan=3>
			<table cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<td width='320'>
					<input type=file name='stamp_file' size=70 class='textbox'  style='width:300px' validation='$stamp_bool' title='사업자 인감도장'>
				</td>";

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."/company_stamp_".$company_id.".gif")){
$Contents01 .= "<td width='*' style='padding:5px 0px;' class='company_stamp'>
					<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/company_stamp_".$company_id.".gif' width=50>
					<img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"company_stamp_del('".$company_id."')\">
				</td>";
		}
$Contents01 .= "</tr>
			</table>
		</td>
	</tr>
	<tr height=100>
		<td class='input_box_title'> <b>기타사항</b></td>
		<td class='input_box_item' style='padding:10px;' colspan='3'>
			 
					<textarea type=text class='textbox' name='seller_message' style='width:99%;height:85px;padding:2px;'>".$db->dt[seller_message]."</textarea>
				 
		</td>
	</tr>";

	if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$Contents01 .= "
	<!--
	<tr>
		<td class='input_box_title'> <b>입점업체승인</b></td>
		<td class='input_box_item' colspan=3>
			<input type=radio name='seller_auth' id='seller_auth_N' value='N' ".CompareReturnValue("N",$db->dt[seller_auth],"checked")." ".CompareReturnValue("",$db->dt[seller_auth],"checked")."><label for='seller_auth_N'>승인대기</label>
			<input type=radio name='seller_auth' id='seller_auth_Y' value='Y'  ".CompareReturnValue("Y",$db->dt[seller_auth],"checked")." checked><label for='seller_auth_Y'>승인</label>
			<input type=radio name='seller_auth' id='seller_auth_X' value='X' ".CompareReturnValue("X",$db->dt[seller_auth],"checked")."><label for='seller_auth_X'>승인거부</label>
			 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span>
		</td>
	</tr>
	-->
	<!--입점업체 승인후에 사용자 등록이 가능합니다. --> 
	";
	}

$Contents01 .= "
	</table>";

$Contents01 .= "
	<ul class='paging_area' >
		<li class='front' style='padding-bottom:30px;'><img src='../image/emo_3_15.gif' align=absmiddle>  <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span> </li>
		<li class='back'></li>
	</ul>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>쇼핑몰 운영정보</b>
			</td>
		</tr>
	</table>";

//글로벌 설정 정보 DB 추가 하지 않고 파일에 있는 내용 획득하여 확인
$path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_config";
$companyInfo = require($path.'/company.php');

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>운영시간    </td>
		<td class='input_box_item' colspan='3'>
			<table cellpadding='0' cellspacing='0' border='0' width='100%' style='margin:5px 0px;'>
				<col width='350'>
				<col width='30'>
				<col width='*'>
				<tr>
					<td>
						<textarea name='opening_time' class='textbox' id='opening_time' title='운영시간' validation='false' style='width:300px;height:40px;'>".$db->dt[opening_time]."</textarea>
					</td>
					<td><font color='#F00F0F'>*예)</font></td>
					<td><font color='#F00F0F'>평일 10시~18시<br />토요일,일요일,공휴일은 쉽니다.(줄바꿈이 프론트에 적용되어 보여집니다.)</font></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>글로벌 운영시간    </td>
		<td class='input_box_item' colspan='3'>
			<table cellpadding='0' cellspacing='0' border='0' width='100%' style='margin:5px 0px;'>
				<col width='350'>
				<col width='30'>
				<col width='*'>
				<tr>
					<td>
						<textarea name='global_opening_time' class='textbox' id='global_opening_time' title='글로벌 운영시간' validation='false' style='width:300px;height:40px;'>".$companyInfo['global_opening_time']."</textarea>
					</td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=34>
		<td class='input_box_title'> <b>C/S 전화번호 </td>
		<td class='input_box_item'>
			<div style='float:left' class='c_type_3'>
				<input type=text class='textbox c_type_3' name='cs_phone1' value='".$cs_phone[0]."' maxlength=3 size=3 validation='false' title='전화' numeric=true> -
				<input type=text class='textbox c_type_3' name='cs_phone2' value='".$cs_phone[1]."' maxlength=4 size=5 validation='false' title='전화' numeric=true> -
				<input type=text class='textbox c_type_3' name='cs_phone3' value='".$cs_phone[2]."' maxlength=4 size=5 validation='false' title='전화' numeric=true>
			</div>
			<div style='float:left; display:none' class='c_type_2'>
				<input type=text class='textbox c_type_2' name='cs_phone2' value='".$cs_phone[1]."' maxlength=4 size=5 validation='false' title='전화' numeric=true> -
				<input type=text class='textbox c_type_2' name='cs_phone3' value='".$cs_phone[2]."' maxlength=4 size=5 validation='false' title='전화' numeric=true>
			</div>
			<div style='float:left'>
				<input type='radio' name='cs_phone_type' id='cs_phone_type_0' value='0' validation=false title='대표전화 타입' ".($cs_phone[0] != "" ? "checked":"")."><label for='cs_phone_type_0'>지역번호포함</label> &nbsp;&nbsp;
				<input type='radio' name='cs_phone_type' id='cs_phone_type_1' value='1' validation=false title='대표전화 타입' ".($cs_phone[0] == "" ? "checked":"")."><label for='cs_phone_type_1'>지역번호불포함</label>
			</div>
		</td>
	    <td class='input_box_title'> <b>배송문의 전화번호    </td>
		<td class='input_box_item'>
			<div style='float:left' class='s_type_3'>
				<input type=text class='textbox s_type_3' name='shipping_phone_1' value='".$shipping_phone[0]."' maxlength=3 size=3 validation='false' title='전화' numeric=true> -
				<input type=text class='textbox s_type_3' name='shipping_phone_2' value='".$shipping_phone[1]."' maxlength=4 size=5 validation='false' title='전화' numeric=true> -
				<input type=text class='textbox s_type_3' name='shipping_phone_3' value='".$shipping_phone[2]."' maxlength=4 size=5 validation='false' title='전화' numeric=true>
			</div>
			<div style='float:left; display:none' class='s_type_2'>
				<input type=text class='textbox s_type_2' name='shipping_phone_2' value='".$shipping_phone[1]."' maxlength=4 size=5 validation='false' title='전화' numeric=true> -
				<input type=text class='textbox s_type_2' name='shipping_phone_3' value='".$shipping_phone[2]."' maxlength=4 size=5 validation='false' title='전화' numeric=true>
			</div>
			<div style='float:left'>
				<input type='radio' name='ch_phone_type' id='ch_phone_type_0' value='0' validation=false title='대표전화 타입' ".($shipping_phone[0] != "" ? "checked":"")."><label for='ch_phone_type_0'>지역번호포함</label> &nbsp;&nbsp;
				<input type='radio' name='ch_phone_type' id='ch_phone_type_1' value='1' validation=false title='대표전화 타입' ".($shipping_phone[0] == "" ? "checked":"")."><label for='ch_phone_type_1'>지역번호불포함</label>
			</div>
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>개인정보관리자  </b></td>
		<td class='input_box_item'>
		<input type=text name='officer_name' value='".$db->dt[officer_name]."' class='textbox'  style='width:50px' validation='false' title='개인정보관리자'>
		</td>
	    <td class='input_box_title'> <b>개인정보관리자이메일  </b>   </td>
		<td class='input_box_item'><input type=text name='officer_email' value='".$db->dt[officer_email]."' class='textbox'  style='width:250px' validation='false' title='개인정보관리자이메일' mail=true></td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>글로벌 C/S 전화번호  </b></td>
		<td class='input_box_item'>
		<input type=text name='global_cs_phone' value='".$companyInfo['global_cs_phone']."' class='textbox'  style='width:250px' validation='false' title='글로벌 C/S 전화번호'>
		</td>
	    <td class='input_box_title'> <b>글로벌 C/S 이메일  </b>   </td>
		<td class='input_box_item'><input type=text name='global_cs_email' value='".$companyInfo[global_cs_email]."' class='textbox'  style='width:250px' validation='false' title='글로벌 C/S 이메일 ' mail=true></td>
	</tr>
	</table>";

}

if($info_type == "shop_info"){

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>상점 기본정보</b>
			</td>
		</tr>
	</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'>
	<col width='20%' />
	<col width='80%' />
	<tr bgcolor=#ffffff height=34>
		<td class='input_box_title'> <b>상점 이름 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type=text name='shop_name' value='".$db->dt[shop_name]."' class='textbox'  style='width:350px' validation=true title='상점 이름'></td>
	</tr>
	<tr bgcolor=#ffffff height=104>
		<td class='input_box_title'> <b>상점 설명</b>   </td>
		<td class='input_box_item'><textarea name='shop_desc'  style='width:600px;height:70px;padding:2px;' validation=false title='상점 설명'>".$db->dt[shop_desc]."</textarea></td>
	</tr>
	<tr bgcolor=#ffffff height=84>
		<td class='input_box_title'> <b>상점 로고 </b>  </td>
		<td class='input_box_item' style='padding:10px;'>
		<input type=file name='shop_logo_img' size=70 class='textbox'  style='width:300px'> 권장 사이즈 119 * 74";

if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$admininfo[company_id].".gif")){
$Contents01 .= "<br>
				<div class='shop_logo_'>
					<img src='".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$admininfo[company_id].".gif'  align='absmiddle' style='margin:10px 10px 10px 0px; vertical-align:middle;'> 
					<img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"shop_logo_del('shop_logo_','".$admininfo[company_id]."')\" style='vertical-align:middle; cursor:pointer;'>
				</div>
				";
		}

$Contents01 .= "
		</td>
	</tr>
	<tr bgcolor=#ffffff height=134>
		<td class='input_box_title'> <b>상점 이미지 </b>  </td>
		<td class='input_box_item' style='padding:10px;'>
		<input type=file name='shop_img' size=70 class='textbox'  style='width:300px'> 권장 사이즈 660 * 260";

if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$admininfo[company_id].".gif")){
$Contents01 .= "<br>
				<div class='shop_'>
				<img src='".$admin_config[mall_data_root]."/images/shopimg/shop_".$admininfo[company_id].".gif' align='absmiddle' style='margin:10px 10px 10px 0px;'> 
				<img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"shop_logo_del('shop_','".$admininfo[company_id]."')\" style='vertical-align:middle; cursor:pointer;'>
				</div>
				";
		}

$Contents01 .= "
		</td>
	</tr>
	<tr bgcolor=#ffffff height=84>
		<td class='input_box_title'> <b>상점 위치 </b>  </td>
		<td class='input_box_item' style='padding:10px 5px;'>
		<input type=file name='shop_lo' size=70 class='textbox'  style='width:300px'> 권장 사이즈 305 * 264";

if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_lo_".$admininfo[company_id].".gif")){
$Contents01 .= "<br>
				<div class='shop_lo_'>
				<img src='".$admin_config[mall_data_root]."/images/shopimg/shop_lo_".$admininfo[company_id].".gif' width=150 height=77 align='absmiddle' style='margin:10px;'> 
				<img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"shop_logo_del('shop_lo_','".$admininfo[company_id]."')\" style='vertical-align:middle; cursor:pointer;'>
				</div>
				";
		}

$Contents01 .= "
		</td>
	</tr>";

$Contents01 .= "
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
	<tr height=40>
		<td colspan=4 style='padding:10px 0px 50px 20px'>
			<img src='../image/emo_3_15.gif' align=absmiddle> <span class='small'><!--미니샵 및 상점소개 페이지에서 노출되는 정보입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </span>
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
		<col width='20%' />
		<col width='30%' />
		<col width='20%' />
		<col width='30%' />
	<tr>
		<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 입점업체 프로모션 정보</b></div>")."</td>
	</tr>
	<tr>
		 <td  colspan='4' style='padding:0px;'>";

	$gdb = new Database;
	$gdb->query("SELECT * FROM shop_minishop_product_group WHERE company_id='".$admininfo[company_id]."' order by group_code asc ");

	for($i=0;$i < 3;$i++){
	$gdb->fetch($i);

	if($i==0)				$group_name="셀러 추천상품";
	elseif($i==1)			$group_name="셀러 인기상품";
	elseif($i==2)			$group_name="셀러 특가상품";
	else						$group_name="-";

$Contents01 .= "
		<div id='group_info_area".$i."' group_code='".($i+1)."'>
		<div style='padding:10px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>".$group_name."<input type='hidden' name='group_name[".($i+1)."]' id='group_name_".($i+1)."' value='".$group_name."'></b> <!--a onclick=\"add_table()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle></a--> ".($i == 0 ? "":"<!--a onclick=\"del_table('group_info_area".$i."',".($i+1).");\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle></a-->")."</div>

		<table width='100%' border='0' cellpadding='5' cellspacing='1' bgcolor='#E9E9E9' class='search_table_box'>
		<col width='20%'>
		<col width='*'>
		<!--tr>
			<td class='search_box_title'><b>메인 상품그룹명</b></td>
			<td class='search_box_item'>
			<input type='text' class='textbox' name='group_name[".($i+1)."]' id='group_name_".($i+1)."' size=50 value=\"".$gdb->dt[group_name]."\"> 상품그룹 이미지 등록을 하지 않은경우 노출됩니다.
			</td>
		</tr-->
		<tr>
			<!--td class='search_box_title'><b>전시여부</b></td>
			<td class='search_box_item'>
			<input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_y' size=50 value='Y' style='border:0px;' ".($gdb->dt[use_yn] == "Y" || $gdb->dt[use_yn] == ""? "checked":"")."><label for='use_".($i+1)."_y'> 전시</label>
			<input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_n' size=50 value='N' style='border:0px;' ".($gdb->dt[use_yn] == "N" ? "checked":"")."><label for='use_".($i+1)."_n'> 전시 하지 않음</label>
			</td-->
			<td class='search_box_title'><b>상품노출갯수</b></td>
			<td class='search_box_item'>
				<input type='hidden' name='use_yn[".($i+1)."]'  value='Y'>
			<input type='text' class='textbox' name='product_cnt[".($i+1)."]' id='product_cnt_".($i+1)."' size=10 value='".$gdb->dt[product_cnt]."'>
			</td>
			</tr>
		<!--tr>
			<td class='search_box_title'><b>상품그룹 이미지</b></td>
			<td class='search_box_item' style='padding:10px'>
			<input type='file' class='textbox' name='group_img[".($i+1)."]' id='group_img' size=50 value=''> <input type='checkbox' name='group_img_del[".($i+1)."]' id='group_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_img_del_".($i+1)."'>그룹이미지 삭제</label><br>
			<div style='padding:5px 5px 5px 0px;height:90px;width:90%;overflow:auto' id='group_img_area_".($i+1)."'>";
	if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/main/".$img_pre_text."main_group_".($i+1).".gif")){
		$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/main/".$img_pre_text."main_group_".($i+1).".gif'>";
	}

	$Contents01 .= "</div><br>
							<span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
							</td>
						</tr>
						<tr>
							<td class='search_box_title'><b>상품그룹이미지링크</b></td>
							<td class='search_box_item'>
							<input type='text' class='textbox' name='group_link[".($i+1)."]' id='group_link_".($i+1)."' size=50 value='".$gdb->dt[group_link]."'>
							</td>
						</tr>
						<tr>
							<td class='search_box_title'><b>상품그룹 배너 이미지</b></td>
							<td class='search_box_item' style='padding:10px'>
							<input type='file' class='textbox' name='group_banner_img[".($i+1)."]' id='group_banner_img' size=50 value=''> <input type='checkbox' name='group_banner_img_del[".($i+1)."]' id='group_banner_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_banner_img_del_".($i+1)."'>그룹 배너이미지 삭제</label><br>
							<div style='padding:5px 5px 5px 0px;height:90px;width:90%;overflow:auto' id='group_banner_img_area_".($i+1)."'>";
	if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/main/".$img_pre_text."main_group_banner_".($i+1).".gif")){
		$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/main/".$img_pre_text."main_group_banner_".($i+1).".gif'>";
	}

	$Contents01 .= "						</div><br>
							<span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
							</td>
						</tr>
						<tr>
							<td class='search_box_title'><b>전시타입</b></td>
							<td class='search_box_item' style='padding:10px 5px;'>
							<div style='float:left;text-align:center;width:130px;'>
								<img src='../images/".$admininfo["language"]."/g_5.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_0').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_0' value='0' style='border:0px;' ".($gdb->dt[display_type] == "0" ? "checked":"")."><label for='display_type_".($i+1)."_0'>기본형(5EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:130px;'>
								<img src='../images/".$admininfo["language"]."/g_4.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_1').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_1' value='1' style='border:0px;' ".($gdb->dt[display_type] == "1" ? "checked":"")."><label for='display_type_".($i+1)."_1'>기본형(4EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:130px;'>
								<img src='../images/".$admininfo["language"]."/g_3.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_2').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_2' value='2' style='border:0px;' ".($gdb->dt[display_type] == "2" ? "checked":"")."><label for='display_type_".($i+1)."_2'>기본형2(3EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:140px;'>
								<img src='../images/".$admininfo["language"]."/slide_4.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_3').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_3' value='3' style='border:0px;' ".($gdb->dt[display_type] == "3" ? "checked":"")."><label for='display_type_".($i+1)."_3' class='small'>슬라이드형(4EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:135px;display:none;'>
								<img src='../images/".$admininfo["language"]."/g_16.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_4').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_4' value='4' style='border:0px;' ".($gdb->dt[display_type] == "4" ? "checked":"")."><label for='display_type_".($i+1)."_4'>기본형4(1/*EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:135px;'>
								<img src='../images/".$admininfo["language"]."/g_17.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_5').checked = true;\"><br>
							  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_5' value='5' style='border:0px;' ".($gdb->dt[display_type] == "5" ? "checked":"")."><label for='display_type_".($i+1)."_5'>기본형(4EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:135px;'>
							  <img src='../images/".$admininfo["language"]."/g_24.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_6').checked = true;\"><br>
							  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_6' value='6' style='border:0px;' ".($gdb->dt[display_type] == "6" ? "checked":"")."><label for='display_type_".($i+1)."_6'>기본형(2/4EA 배열)</label>
							  </div>
							  </td>
							</tr-->
							<tr>
							  <td class='search_box_title'><b>전시상품</b></td>
							  <td class='search_box_item' style='padding:10px 10px;'>
							   <div style='padding-bottom:10px;'>
								  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_m'>수동등록</label>
								  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_a' size=50 value='A' style='border:0px;' ".($gdb->dt[goods_display_type] == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_a'>자동등록</label><br>
							  </div>
							  <div id='goods_manual_area_".($i+1)."' style='".($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == ""  ? "display:block;":"display:none;")."'>
								  <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,".($i+1).",'productList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
								  <div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >".relationEventGroupProductList(($i+1), "clipart",$page_type)."</div>
								  <div style='width:100%;float:left;'>
								  </div>
							  </div>
							  <div style='padding:0px 0px;".($gdb->dt[goods_display_type] == "A" ? "display:block;":"display:none;")."' id='goods_auto_area_".($i+1)."'>
								<a href=\"javascript:PoPWindow3('/admin/display/category_select.php?mmode=pop&group_code=".($i+1)."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_select_category.gif' border=0 align=absmiddle></a><br>
								<table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
									<col width=100%>
									<tr>
										<td style='padding-top:5px;'>";

												$Contents01 .= PrintCategoryRelation(($i+1),$page_type);

						$Contents01 .= "	</td>
									</tr>
									<tr><td style='padding-bottom:5px;'>카테고리 선택하기를 클릭해서 자동 노출하고자 하는 카테고리를 지정 하실 수 있습니다.</td></tr>
								</table>
								<div style='padding:5px 0px;'>
								선택한 카테고리 내의 상품을
								<select name='display_auto_type[".($i+1)."]'>
									<option value='order_cnt' ".($gdb->dt[display_auto_type] == "order_cnt" ? "selected":"").">구매수순</option>
									<option value='view_cnt' ".($gdb->dt[display_auto_type] == "view_cnt" ? "selected":"").">클릭수순</option>
									<option value='sellprice' ".($gdb->dt[display_auto_type] == "sellprice" ? "selected":"").">최저가순</option>
									<option value='regdate' ".($gdb->dt[display_auto_type] == "regdate" ? "selected":"").">최근등록순</option>
									<option value='wish_cnt' ".($gdb->dt[display_auto_type] == "wish_cnt" ? "selected":"").">찜한순</option>
									<option value='after_score' ".($gdb->dt[display_auto_type] == "after_score" ? "selected":"").">후기순위</option>
								</select>
								으로 노출 합니다.
								</div>
								</div>
								</td>
							</tr>
						</table>
						</div>";
		}
	$Contents01 .= "</td>
		  </tr>
		</table>
";

$Contents01 .= "
	<ul class='paging_area' >
		<li class='front' style='padding-bottom:30px;'><img src='../image/emo_3_15.gif' align=absmiddle>  <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </li>
		<li class='back'></li>
	</ul>";
}

if($info_type == "person_info"){

if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O" || $admininfo[mall_type] == "H" || $admininfo[mall_type] == "E"){

	$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>운영 담당자 정보</b>
			</td>
		</tr>
	</table>";
	$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr>
	    <td class='input_box_title'> <b>담당자명 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item' colspan='3'>
		<input type=text name='customer_name' value='".$db->dt[customer_name]."' class='textbox'  style='width:200px' validation='true' title='담당자명'>
		</td>
	</tr>

	<tr>
	    <td class='input_box_title'> <b>전화번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='customer_phone_1' value='".$customer_phone[0]."' maxlength=3 com_numeric=true style='width:20px;' title='전화번호'> -
			<input type=text class='textbox' name='customer_phone_2' value='".$customer_phone[1]."' maxlength=4 com_numeric=true style='width:30px;' title='전화번호'> -
			<input type=text class='textbox' name='customer_phone_3' value='".$customer_phone[2]."' maxlength=4 com_numeric=true style='width:30px;' title='전화번호'>
		</td>
	    <td class='input_box_title'> <b>핸드폰번호 <img src='".$required3_path."'> </b>   </td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='customer_mobile_1' value='".$customer_mobile[0]."' maxlength=4 com_numeric=true style='width:20px;' title='핸드폰번호'> -
			<input type=text class='textbox' name='customer_mobile_2' value='".$customer_mobile[1]."' maxlength=4 com_numeric=true style='width:30px;' title='핸드폰번호'> -
			<input type=text class='textbox' name='customer_mobile_3' value='".$customer_mobile[2]."' maxlength=4 com_numeric=true style='width:30px;' title='핸드폰번호'>
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>이메일 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
		<input type=text name='customer_mail' value='".$db->dt[customer_mail]."' class='textbox'  style='width:200px' email='true' title='이메일'>
		</td>
	    <td class='input_box_title'> <b>직급/직책</b>   </td>
		<td class='input_box_item'><input type=text  id='' name='customer_position' value='".$db->dt[customer_position]."' class='textbox'  style='width:100px' validation='false' title='직급/직책'></td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>기타사항</b></td>
		<td class='input_box_item' style='padding:10px;' colspan='3'> 
			<textarea type=text class='textbox' value='".$db->dt[customer_message]."'  name='customer_message' style='width:97%;height:85px;padding:10px;'>".$db->dt[customer_message]."</textarea>
		</td>
	</tr>
	</table><br>";

	$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>회계 담당자 정보</b>
			</td>
		</tr>
	</table>";
	$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr>
	    <td class='input_box_title'> <b>담당자명 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='tax_person_name' value='".$db->dt[tax_person_name]."' class='textbox'  style='width:50px' validation='true' title='담당자명'>
		</td>
		 <td class='input_box_item' colspan='2'> <input type='checkbox' id='check_all' value='all'> 상기내용과 동일시 체크박스에 클릭하세요. </td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>전화번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='tax_person_phone_1' value='".$tax_person_phone[0]."' maxlength=3 com_numeric=true style='width:20px;' title='전화번호'> -
			<input type=text class='textbox' name='tax_person_phone_2' value='".$tax_person_phone[1]."' maxlength=4 com_numeric=true style='width:30px;' title='전화번호'> -
			<input type=text class='textbox' name='tax_person_phone_3' value='".$tax_person_phone[2]."' maxlength=4 com_numeric=true style='width:30px;' title='전화번호'>
		</td>
	    <td class='input_box_title'> <b>핸드폰번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='tax_person_mobile_1' value='".$tax_person_mobile[0]."' maxlength=3 com_numeric=true style='width:20px;' title='핸드폰번호'> -
			<input type=text class='textbox' name='tax_person_mobile_2' value='".$tax_person_mobile[1]."' maxlength=4 com_numeric=true style='width:30px;' title='핸드폰번호'> -
			<input type=text class='textbox' name='tax_person_mobile_3' value='".$tax_person_mobile[2]."' maxlength=4 com_numeric=true style='width:30px;' title='핸드폰번호'>
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>이메일 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
		<input type=text name='tax_person_mail' value='".$db->dt[tax_person_mail]."' class='textbox'  style='width:200px' email=true title='이메일'>
		</td>
	    <td class='input_box_title'> <b>직급/직책</b>   </td>
		<td class='input_box_item'><input type=text  id='' name='tax_person_position' value='".$db->dt[tax_person_position]."' class='textbox'  style='width:100px' validation='false' title='직급/직책'></td>
	</tr>
	<tr>
		  <td class='input_box_title'> <b>기타사항</b></td>
		<td class='input_box_item' style='padding:10px;' colspan='3'>
			<textarea type=text class='textbox' value='".$db->dt[tax_person_message]."'  name='tax_person_message' style='width:97%;height:85px;padding:10px;'>".$db->dt[tax_person_message]."</textarea>
		</td>
	</tr>
	</table>
	<script language='javascript'>
		$('#check_all').click(function(){
			if($('#check_all').is(':checked') == true){
				$('input[name=tax_person_name]').val($('input[name=customer_name]').val());
				$('input[name=tax_person_phone_1]').val($('input[name=customer_phone_1]').val());
				$('input[name=tax_person_phone_2]').val($('input[name=customer_phone_2]').val());
				$('input[name=tax_person_phone_3]').val($('input[name=customer_phone_3]').val());
				$('input[name=tax_person_mobile_1]').val($('input[name=customer_mobile_1]').val());
				$('input[name=tax_person_mobile_2]').val($('input[name=customer_mobile_2]').val());
				$('input[name=tax_person_mobile_3]').val($('input[name=customer_mobile_3]').val());
				$('input[name=tax_person_mail]').val($('input[name=customer_mail]').val());
				$('input[name=tax_person_position]').val($('input[name=customer_position]').val());
				$('textarea[name=tax_person_message]').val($('textarea[name=customer_message]').val());
			}
		});
	
	</script>
	
	";
}
$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;' class=small>
		<img src='../image/emo_3_15.gif' align=absmiddle> 해당거래처와의 기본 계좌 정보를 입력합니다.
	</td>
</tr>
</table>";

}

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table>";
}
if($company_id != "" && $info_type == "delivery_info"){
$ButtonString .= "<script type='text/javascript'>
	window.onload = function(){
		deliveryTypeView(".$db->dt[delivery_policy].");
	}
</script>";
}

$Contents = "<form name='edit_form' action='./company.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='iframe_act'><!--target='iframe_act' -->
<input name='act' type='hidden' value='$act'>
<input name='info_type' type='hidden' value='$info_type'>
<input name='company_id' type='hidden' value='".$admininfo[company_id]."'>";
$Contents = $Contents."<table width='100%' border=0>";
//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</table></form><br><br>";

$Script = "<!--script language='javascript' src='company.add.js'></script-->
<script type='text/javascript' src='/admin/js/ms_productSearch.js'></script>
<script language='javascript'>


function zipcode(type) {
	var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function category_del(group_code, el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory_'+group_code);
	obj.deleteRow(idx);
	var cObj=\$('input[name=basic]');
	if(cObj.length == null){
		//cObj[0].checked = true; // 0이 나오지 null이 나오지 않음 kbk
	}else{
		for(var i=0;i<cObj.length;i++){
			if(cObj[i].checked){
				return true;
				break;
			}else{
				cObj[0].checked = true;
			}
		}
	}
	//cate.splice(idx,1);
}

function ChangeAddress(obj){
	if(obj.checked){
		document.getElementById('input_address_area').style.display = '';
	}else{
		document.getElementById('input_address_area').style.display = 'none';
	}
}

function same_com_addr() {
	var fm=document.edit_form;
	fm.return_zip1.value=fm.com_zip1.value;
	fm.return_zip2.value=fm.com_zip2.value;
	fm.return_addr1.value=fm.com_addr1.value;
	fm.return_addr2.value=fm.com_addr2.value;
}
$( document ).ready(function() {
  
	var com = $('input[name=com_phone1]').val();
	
	if(com == ''){
		$('.p_type_2').show();
		$('.p_type_2').attr('disabled',false);
		$('.p_type_2').attr('validation',true);

		$('.p_type_3').hide();
		$('.p_type_3').attr('disabled',true);
		$('.p_type_3').attr('validation',false);
	}else{
		$('.p_type_3').show();
		$('.p_type_3').attr('disabled',false);
		$('.p_type_3').attr('validation',true);

		$('.p_type_2').hide();
		$('.p_type_2').attr('disabled',true);
		$('.p_type_2').attr('validation',false);
	}


	$('#com_phon_type_1').click(function(){
		$('.p_type_2').show();
		$('.p_type_2').attr('disabled',false);
		$('.p_type_2').attr('validation',true);

		$('.p_type_3').hide();
		$('.p_type_3').attr('disabled',true);
		$('.p_type_3').attr('validation',false);
	})
	$('#com_phon_type_0').click(function(){
		$('.p_type_3').show();
		$('.p_type_3').attr('disabled',false);
		$('.p_type_3').attr('validation',true);

		$('.p_type_2').hide();
		$('.p_type_2').attr('disabled',true);
		$('.p_type_2').attr('validation',false);
	})

	
	var cs_phon = $('input[name=cs_phone1]').val();
	
	if(cs_phon == ''){
		$('.c_type_2').show();
		$('.c_type_2').attr('disabled',false);
		$('.c_type_2').attr('validation',true);

		$('.c_type_3').hide();
		$('.c_type_3').attr('disabled',true);
		$('.c_type_3').attr('validation',false);
	}else{
		$('.c_type_3').show();
		$('.c_type_3').attr('disabled',false);
		$('.c_type_3').attr('validation',true);

		$('.c_type_2').hide();
		$('.c_type_2').attr('disabled',true);
		$('.c_type_2').attr('validation',false);
	}

	$('#cs_phone_type_1').click(function(){
		$('.c_type_2').show();
		$('.c_type_2').attr('disabled',false);
		$('.c_type_2').attr('validation',true);

		$('.c_type_3').hide();
		$('.c_type_3').attr('disabled',true);
		$('.c_type_3').attr('validation',false);
	})
	$('#cs_phone_type_0').click(function(){
		$('.c_type_3').show();
		$('.c_type_3').attr('disabled',false);
		$('.c_type_3').attr('validation',true);

		$('.c_type_2').hide();
		$('.c_type_2').attr('disabled',true);
		$('.c_type_2').attr('validation',false);
	})
	
	
	var ch_phon = $('input[name=shipping_phone_1]').val();
	
	if(ch_phon == ''){
		$('.s_type_2').show();
		$('.s_type_2').attr('disabled',false);
		$('.s_type_2').attr('validation',true);

		$('.s_type_3').hide();
		$('.s_type_3').attr('disabled',true);
		$('.s_type_3').attr('validation',false);
	}else{
		$('.s_type_3').show();
		$('.s_type_3').attr('disabled',false);
		$('.s_type_3').attr('validation',true);

		$('.s_type_2').hide();
		$('.s_type_2').attr('disabled',true);
		$('.s_type_2').attr('validation',false);
	}

	$('#ch_phone_type_1').click(function(){
		$('.s_type_2').show();
		$('.s_type_2').attr('disabled',false);
		$('.s_type_2').attr('validation',true);

		$('.s_type_3').hide();
		$('.s_type_3').attr('disabled',true);
		$('.s_type_3').attr('validation',false);
	})
	$('#ch_phone_type_0').click(function(){
		$('.s_type_3').show();
		$('.s_type_3').attr('disabled',false);
		$('.s_type_3').attr('validation',true);

		$('.s_type_2').hide();
		$('.s_type_2').attr('disabled',true);
		$('.s_type_2').attr('validation',false);
	})


});
function company_stamp_del(company_id){
	$.ajax({
		url: './company.act.php',
		type: 'get',
		dataType: 'html',
		data: ({act: 'stamp_del',
				company_id: company_id
		}),
		success: function(result){
			console.log(result);
			if(result == 'Y'){
				alert('삭제되었습니다.');
				$('.company_stamp').html('');
			}else{
				alert('데이터처리실패');
			}
		}
	});
}
function shop_logo_del(img_name,company_id){
	$.ajax({
		url: './company.act.php',
		type: 'get',
		dataType: 'html',
		data: ({act: 'shop_logo_del',
				company_id: company_id,
				img_name: img_name
		}),
		success: function(result){
			console.log(result);
			if(result == 'Y'){
				alert('삭제되었습니다.');
				$('.'+img_name).html('');
			}else{
				alert('데이터처리실패');
			}
		}
	});
}

$(document).ready(function() {

	if($('input[name=commercial_disp]:checked').val()=='Y'){
		$('.wholesale_validation').each(function(){
			$(this).find('select,input').each(function(){
				$(this).attr('validation','true');
			})
		})
	}else{
		$('.wholesale_validation').each(function(){
			$(this).find('select,input').each(function(){
				$(this).attr('validation','false');
			})
		})
	}

	$('input[name=commercial_disp]').click(function(){
		if($(this).val()=='Y'){
			$('.wholesale_tr').show();
			$('.wholesale_validation').each(function(){
				$(this).find('select,input').each(function(){
					$(this).attr('validation','true');
				})
			})
			
		}else{
			$('.wholesale_tr').hide();
			$('.wholesale_validation').each(function(){
				$(this).find('select,input').each(function(){
					$(this).attr('validation','false');
				})
			})
		}
	})

	$('#seller_date').datepicker({
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: 'Kalender',
	});
});

</script>
";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	$P->Navigation = "상점관리 > 쇼핑몰 환경설정 > 기본정보 설정";
	$P->title = "기본정보 설정";
	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	$P->Navigation = "상점관리 > 쇼핑몰 환경설정 > 기본정보 설정";
	$P->title = "기본정보 설정";
	echo $P->PrintLayOut();
}

function relationEventGroupProductList($group_code, $disp_type="",$page_type="M"){
	global $start,$page, $orderby, $admin_config, $erpid, $admininfo;

	$max = 105;

	$company_id = $admininfo[company_id];

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new MySQL;

	$sql = "SELECT COUNT(*) FROM ".TBL_SHOP_PRODUCT." p, shop_minishop_product_relation erp where p.id = erp.pid and group_code = '$group_code' and p.disp = 1 AND erp.company_id='".$company_id."' ";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[0];

	$sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, mpr_ix, erp.vieworder, erp.group_code, p.brand_name
					FROM ".TBL_SHOP_PRODUCT." p, shop_minishop_product_relation erp
					where p.id = erp.pid and group_code = '$group_code' and p.disp = 1 AND erp.company_id='".$company_id."' order by erp.vieworder asc limit $start,$max";
	$db->query($sql);

	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script>'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $db->dt['id'], 'c');
				$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$db->dt['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}


function PrintCategoryRelation($group_code,$page_type="M"){
	global $db ,$admininfo, $admininfo ;

	$company_id = $admininfo[company_id];

	$sql = "select c.cid,c.cname,c.depth, r.mcr_ix, r.regdate  from shop_minishop_category_relation r, ".TBL_SHOP_CATEGORY_INFO." c where group_code = '".$group_code."' and c.cid = r.cid AND r.company_id='".$company_id."' ";
	//echo $sql."<br><br>";
	$db->query($sql);

	if ($db->total == 0){
		$mString .= "<table cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."' >
								<col width=5>
								<col width=*>
								<col width=100>
							  </table>";
	}else{
		$i=0;
		$mString = "<table width=100% border=0 cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."'>";
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='category[".$group_code."][]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<!--td class='table_td_white small' width='50'><input type='radio' name='basic[".$group_code."]' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."></td-->
				<td class='table_td_white small ' width='*'>".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</td>
				<td class='table_td_white' width='100' align=right><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(".$group_code.",this.parentNode.parentNode)' style='cursor:pointer;' /></td>
				</tr>";
		}
		$mString .= "</table>";
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";


	return $mString;
}

?>
