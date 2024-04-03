<?
include("../class/layout.class");
include("./inventory.lib.php");
$title = "출고처등록";

$db = new Database;

//echo $company_code;
if($ci_ix == ""){

	$act = "insert";
	$customer_type = "D";
}else{
	$db->query("SELECT * FROM inventory_customer_info WHERE ci_ix = '".$ci_ix."' and customer_type = 'D'");

	$act = "update";

	$db->fetch();



	$customer_phone = explode("-",$db->dt[customer_phone]);
	$customer_fax = explode("-",$db->dt[customer_fax]);
	$customer_type =$db->dt[customer_type];


	$basic_info = $db->dt;


	$db->query("SELECT * FROM inventory_company_detail WHERE ci_ix = '".$ci_ix."' ");
	$db->fetch();

	$com_zip = explode("-",$db->dt[com_zip]);
	$com_phone = explode("-",$db->dt[com_phone]);
	$com_fax = explode("-",$db->dt[com_fax]);

	$charger_phone = explode("-",$db->dt[charger_phone]);
	$charger_mobile = explode("-",$db->dt[charger_mobile]);

	$company_info = $db->dt;
}



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='padding: 0px 0px 5px 0px'>
	  <col width=18% />
	  <col width=32% />
	  <col width=18% />
	  <col width=32% />
	  <tr>
	    <td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("$title", "재고관리 > $title")."</td>
	</tr>
	  <tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;' onclick='set_testdata();'><img src='../image/title_head.gif' ><b> 출고처 정보</b></div>")."</td>
	  </tr>
</table><br>

".get_sales_vendor($basic_info,$required3_path)."

	  <table width='100%' cellpadding=5 cellspacing=0 border='0' align='left' class='sale_agency_info' ".($basic_info[customer_div] != "9" || $basic_info[customer_div] == "" ? "style='display:none;'":"").">
	  <col width=18% />
	  <col width=32% />
	  <col width=18% />
	  <col width=32% />
	  <tr>
	    <td align='left' colspan=4 style='padding:30px 0px 5px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 사업자 정보</b></div>")."</td>
	  </tr>
	 </table>
	  <table width='100%' cellpadding=5 cellspacing=1 border='0' bgcolor=silver style='border-collapse:separate; border-spacing:1px;".($basic_info[customer_div] != "9" || $basic_info[customer_div] == "" ? "display:none;":"")."' class='sale_agency_info input_table_box' >
	  <col width=18% />
	  <col width=32% />
	  <col width=18% />
	  <col width=32% />
	  <tr>
	    <td class='input_box_title'> <b>사업자번호 </b></td>
		<td class='input_box_item'>
		<input type=text name='com_number' value='".$company_info[com_number]."' class='textbox'  style='width:120px' validation='false' title='사업자번호'>
		<div style='display:inline;padding:2px;'>예) 214-000-00000</div>
		</td>
		<td class='input_box_title'> <b>기업형태 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='com_div' value='P' validation=false title='거래처형태' ".($company_info[com_div] == "P" || $company_info[com_div] == "" ? "checked":"").">개인 &nbsp;&nbsp;
			<input type='radio' name='com_div' value='R' validation='false' title='거래처형태' ".($company_info[com_div] == "R"  ? "checked":"").">법인</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>사업자명 </b></td>
		<td  class='input_box_item'>
		<input type=text name='com_name' value='".$company_info[com_name]."' class='textbox'  style='width:200px' validation='false' title='사업자명'>
		</td>
	    <td class='input_box_title'> <b>업태 </b>   </td>
		<td  class='input_box_item'><input type=text name='com_business_status' value='".$company_info[com_business_status]."' class='textbox'  style='width:200px' validation='false' title='업태'></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>대표자명 </b>   </td>
		<td class='input_box_item'><input type=text name='com_ceo' value='".$company_info[com_ceo]."' class='textbox'  style='width:200px' validation='false' title='대표자명'></td>
	    <td class='input_box_title'> <b>업종 </b>   </td>
		<td class='input_box_item'><input type=text name='com_business_category' value='".$company_info[com_business_category]."' class='textbox'  style='width:200px' validation='false' title='업종'></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>대표전화 </b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='com_phone1' value='".$com_phone[0]."' maxlength=3 size=3 validation='false' title='대표전화' numeric=true> -
			<input type=text class='textbox' name='com_phone2' value='".$com_phone[1]."' maxlength=4 size=5 validation='false' title='대표전화' numeric=true> -
			<input type=text class='textbox' name='com_phone3' value='".$com_phone[2]."' maxlength=4 size=5 validation='false' title='대표전화' numeric=true>
		</td>
	    <td class='input_box_title'> <b>대표팩스</b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='com_fax1' value='".$com_fax[0]."' maxlength=3 size=3> -
			<input type=text class='textbox' name='com_fax2' value='".$com_fax[1]."' maxlength=4 size=5> -
			<input type=text class='textbox' name='com_fax3' value='".$com_fax[2]."' maxlength=4 size=5>
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>홈페이지</b></td>
		<td class='input_box_item'><input type=text name='homepage' value='".$company_info[homepage]."' class='textbox'  style='width:200px'></td>
	    <td class='input_box_title'> <b>대표이메일 </b></td>
		<td class='input_box_item'><input type=text name='com_email' value='".trim($company_info[com_email])."' class='textbox'  style='width:200px' validation='false' title='대표이메일' email=true></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>회사 주소</b>    </td>
	    <td class='input_box_item' colspan=3 style='padding:3px 0px 3px 5px'>
	    	<!--".($company_info[com_zip] == "" ? "":"[".$company_info[com_zip]."]")." ".$company_info[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
	    	<div id='input_address_area' ><!--style='display:none;'-->
	    	<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='120px'>
				<col width='*'>
				<tr height=27>
					<td>
						<input type='text' class='textbox' name='com_zip1' id='com_zip1' size='5' maxlength='3' value='".$com_zip[0]."' readonly> -
						<input type='text' class='textbox' name='com_zip2' id='com_zip2' size='5' maxlength='3' value='".$com_zip[1]."' readonly>
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('3');\" style='cursor:pointer;'>
					</td>
				</tr>
				<tr height=27>
					<td colspan=2>
						<input type=text name='com_addr1'  id='com_addr1' value='".$company_info[com_addr1]."' size=50 class='textbox'  style='width:75%'>
					</td>
				</tr>
				<tr height=27>
					<td colspan=2>
						<input type=text name='com_addr2'  id='com_addr2'  value='".$company_info[com_addr2]."' size=70 class='textbox'  style='width:450px'> (상세주소)
					</td>
				</tr>
				</table>
	    	</div>
	    	</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>회사 위치정보</b>    </td>
	    <td class='input_box_item' colspan=3 style='padding:3px 0px 3px 5px'>
	    	<div id='input_address_area' >
	    	<table border='0' cellpadding='0' cellspacing='0' >
				<col width='155px'>
				<col width='155px'>
				<col width='155px'>
				<col width='*'>
				<tr>
					<td>
						<select style='width:150px;'>
							<option value=''>상가</option>
						</select>
					</td>
					<td >
						<select style='width:150px;'>
							<option value=''>층</option>
						</select>
					</td>
					<td >
						<select style='width:150px;'>
							<option value=''>라인</option>
						</select>
					</td>
					<td >
						<input type=text class='textbox' name='store_name'>
					</td>
				</tr>
				</table>
	    	</div>
	    	</td>
	  </tr>
	  </table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='sale_agency_info' ".($basic_info[customer_div] != "9" || $basic_info[customer_div] == "" ? "style='display:none;'":"").">
	  <col width=18%>
	  <col width=32%>
	  <col width=18%>
	  <col width=32%>
	  <tr>
	    <td align='left' colspan=4 style='padding:30px 0px 5px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 담당자 정보</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box sale_agency_info' ".($basic_info[customer_div] != "9" || $basic_info[customer_div] == "" ? "style='display:none;'":"")." >
	  <col width=18%>
	  <col width=32%>
	  <col width=18%>
	  <col width=32%>
	  <tr>
	    <td class='input_box_title'> <b>담당자명 </b></td>
		<td class='input_box_item'><input type=text name='charger' value='".$company_info[charger]."' class='textbox'  style='width:200px' validation='false' title='담당자명' ></td>
		<td class='input_box_title'> <b>담당자 이메일 </b></td>
		<td class='input_box_item'><input type=text name='charger_email' value='".trim($company_info[charger_email])."' class='textbox'  style='width:200px' validation='false' title='담당자 이메일' email=true></td>

	  </tr>
	  <tr >
	    <td class='input_box_title'> <b>전화번호 </b></td>
		<td class='input_box_item'>
			<input type=text name='charger_phone1' value='".$charger_phone[0]."' maxlength=3 size=3  class='textbox' validation='false' title='대표전화번호전화' numeric='true'> -
			<input type=text name='charger_phone2' value='".$charger_phone[1]."' maxlength=4 size=5 class='textbox' validation='false' title='대표전화번호전화' numeric='true'> -
			<input type=text name='charger_phone3' value='".$charger_phone[2]."' maxlength=4 size=5 class='textbox' validation='false' title='전화번호' numeric='true'>
		</td>
	    <td class='input_box_title'> <b>휴대폰</b></td>
		<td class='input_box_item'>
			<input type=text name='charger_mobile1' value='".$charger_mobile[0]."' maxlength=3 size=3 class='textbox' > -
			<input type=text name='charger_mobile2' value='".$charger_mobile[1]."' maxlength=4 size=5 class='textbox' > -
			<input type=text name='charger_mobile3' value='".$charger_mobile[2]."' maxlength=4 size=5 class='textbox' >
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>부서 </b></td>
		<td class='input_box_item'><input type=text name='charger_department' value='".$company_info[charger_department]."' class='textbox'  style='width:200px' validation='false' title='부서' ></td>
		<td class='input_box_title'> <b>직급 </b></td>
		<td class='input_box_item'><input type=text name='charger_position' value='".$company_info[charger_position]."' class='textbox'  style='width:200px' validation='false' title='직급' ></td>
	  </tr>
	  </table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='sale_agency_info' ".($basic_info[customer_div] != "9" || $basic_info[customer_div] == "" ? "style='display:none;'":"").">
	<col width='18%' />
	<col width='32%' />
	<col width='18%' />
	<col width='32%' />
	  <tr>
	    <td align='left' colspan=4 style='padding:30px 0px 5px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 거래은행 / 거래일</b></div>")."</td>
	  </tr>
	 </table>
	 <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box sale_agency_info' ".($basic_info[customer_div] != "9" || $basic_info[customer_div] == "" ? "style='display:none;'":"")."' >
	 <col width='18%' />
	<col width='32%' />
	<col width='18%' />
	<col width='32%' />
	  <tr>
	    <td class='input_box_title''> <b>예금주</b>    </td>
		<td class='input_box_item'><input type=text name='bank_owner' value='".$company_info[bank_owner]."' class='textbox'  style='width:200px'></td>
	    <td class='input_box_title'> <b>거래은행</b>    </td>
		<td class='input_box_item'><input type=text name='bank_name' value='".$company_info[bank_name]."' class='textbox'  style='width:200px'></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>계좌번호</b>    </td>
		<td class='input_box_item' colspan=3><input type=text name='bank_number' value='".$company_info[bank_number]."' class='textbox'  style='width:200px'></td>
	    <!--td align=left style='padding:0 0 0 30'> 거래계약일    </td>
		<td><input type=text name='business_day' value='".$company_info[business_day]."' class='textbox'  style='width:200px'></td-->
	  </tr>
	  </table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=right><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table>";
}else{
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=right><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a></td></tr>
</table>";
}

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='edit_form' action='vendor.act.php' method='post' onsubmit='return CheckFormValue(document.edit_form)' enctype='multipart/form-data'><input name='act' type='hidden' value='$act'><input type=hidden name=customer_type value='".$customer_type."'><input name='ci_ix' type='hidden' value='".$basic_info[ci_ix]."'>";
//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents = $Contents."<tr><td>";
//$Contents = $Contents.ShadowBox($Contents01);
$Contents = $Contents.$Contents01."<br>";
//$Contents = $Contents.$ContentsDesc01;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=20><td></td></tr>";
$Contents = $Contents."<tr><td>";
//$Contents = $Contents.ShadowBox($Contents02);
$Contents = $Contents.$Contents02;
//$Contents = $Contents.$ContentsDesc02;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc02."</td></tr>";
$Contents = $Contents."<tr height=20><td></td></tr>";
$Contents = $Contents."<tr><td>";
//$Contents = $Contents.ShadowBox($Contents03);
$Contents .= $Contents03;
//$Contents = $Contents.$ContentsDesc03;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc03."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr></form>";
$Contents = $Contents."</table >";




$Script = "<script language='javascript' src='company.add.js'></script>
<script language='javascript'>
function zipcode() {
	var zip = window.open('../member/zipcode.php?type=3','','width=440,height=300,scrollbars=yes,status=no');
}

function ChangeAddress(obj){
	if(obj.checked){
		document.getElementById('input_address_area').style.display = 'block';
	}else{
		document.getElementById('input_address_area').style.display = 'none';
	}
}

function set_testdata(){
	var frm = document.edit_form;

	frm.customer_name.value = '자사몰';
	frm.customer_phone1.value = '02';
	frm.customer_phone2.value = '2058';
	frm.customer_phone3.value = '2214';
	frm.customer_fax1.value = '02';
	frm.customer_fax2.value = '2058';
	frm.customer_fax3.value = '2215';
	frm.customer_msg.value = 'www.mallstory.com 쇼핑몰 입니다.';

	frm.com_number.value = '214-10-09837';
	frm.com_name.value = '포비즈';
	frm.com_business_status.value = '소프트웨어개발';
	frm.com_business_category.value = '서비스';
	frm.com_ceo.value = '안수진';
	frm.com_phone1.value = '02';
	frm.com_phone2.value = '2058';
	frm.com_phone3.value = '2214';
	frm.com_fax1.value = '02';
	frm.com_fax2.value = '2058';
	frm.com_fax3.value = '2215';

	frm.homepage.value = 'http://www.mallstory.com';
	frm.com_email.value = 'ceo@forbiz.co.kr';
	frm.com_zip1.value = '137';
	frm.com_zip2.value = '130';
	frm.com_addr1.value = '서울 서초구 양재동  ';
	frm.com_addr2.value = '16-3 번지 윤화빌딩 6층  ';

	frm.charger.value = '신훈식';
	frm.charger_email.value = 'tech@forbiz.co.kr';
	frm.charger_phone1.value = '070';
	frm.charger_phone2.value = '8730';
	frm.charger_phone3.value = '3542';
	frm.charger_mobile1.value = '010';
	frm.charger_mobile2.value = '5203';
	frm.charger_mobile3.value = '1074';
	frm.charger_department.value = '개발팀';
	frm.charger_position.value = '실장';

	frm.bank_owner.value = '안수진';
	frm.bank_name.value = '신한은행';
	frm.bank_number.value = '374-02-136503';
}
</script>
";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = inventory_menu();
$P->strContents = $Contents;
$P->Navigation = "재고관리 > 기초정보 관리 > $title";
$P->title = "$title";
echo $P->PrintLayOut();


?>