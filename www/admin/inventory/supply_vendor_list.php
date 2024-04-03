<?
include("../class/layout.class");
include ("../basic/company.lib.php");
//auth(9);

$db = new Database;
$mdb = new Database;
$rdb = new Database;
//$db->debug = true;//페이지에서 오류가 없는데 쿼리를 찍기에 주석처리함 kbk 13/02/04
$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");
if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "입고처 리스트";
}else{
	$menu_name = "입고처 리스트";
}

$info_type = "seller_list";

$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($FromYY == ""){

//	$sDate = date("Y-m-d");
	$sDate = date("Ymd", $before10day);
	$eDate = date("Ymd");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

}

if ($vFromYY == ""){

	$sDate2 = date("Ymd", $before10day);
	$eDate2 = date("Ymd");

	$startDate2 = date("Ymd", $before10day);
	$endDate2 = date("Ymd");

}else{

	$sDate2 = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate2 = $vToYY."/".$vToMM."/".$vToDD;
	$startDate2 = $vFromYY.$vFromMM.$vFromDD;
	$endDate2 = $vToYY.$vToMM.$vToDD;

}

if ($birYY == ""){

	$sDate3 = date("Ymd");
	$eDate3 = date("Ymd");

	$startDate3 = date("Ymd");
	$endDate3 = date("Ymd");
}else{

	$sDate3 = $birYY."/".$birMM."/".$birDD;
	$eDate3 = "none";
	$startDate3 = $birYY.$birMM.$birDD;
	$endDate3 = "none";
	$birDate = $birYY.$birMM.$birDD;
}



	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}


if(empty($seller_type) && $mode!="search") $seller_type[0] = "2";	//국내매입 입고처 구분값 지정

include "../basic/seller_query.php";

$Script = "<script language='javascript' src='../include/DateSelect.js'></script>\n
<script language='javascript' src='../basic/company.add.js'></script>\n";
$Script .= "
<script language='javascript'>

function clearAll(frm){
		for(i=0;i < frm.code.length;i++){
				frm.code[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.code.length;i++){
				frm.code[i].checked = true;
		}
}

function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
	//input_check_num();
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
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
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

	//$('#end_timepicker').timepicker();
});

function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.sdate.disabled = false;
		frm.edate.disabled = false;
/*
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
*/
	}else{
		frm.sdate.disabled = true;
		frm.edate.disabled = true;
/*
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
*/
	}
}

function init(){
//alert(1);
	var frm = document.search_seller;
//	onLoad('$sDate','$eDate');";

if($regdate != "1"){ 
	$Script .= "

	frm.sdate.disabled = true;
	frm.edate.disabled = true;";
}

$Script .= "
}

";

$Script .= "
function DeleteCompanySeller(company_id){
 	if(confirm('거래처를 정말로 삭제하시겠습니까?.')){
		window.frames['iframe_act'].location.href= '../basic/seller.act.php?act=delete&company_id='+company_id;
 	}
}

</script>";

$Contents = "
<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>
<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("입고처 리스트", "재고관리 > 입고처관리 ")."</td>
  </tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'> 
			<div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='tab_01' class='on' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='supply_vendor_list.php'\">입고처 리스트</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td class='btn'>
					<table id='tab_01' class='' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='sales_vendor_list.php'\">출고처 리스트</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
			</tr>
			</table>	
		</div>
		</td>
	</tr>
  <tr>
  	<td>";

$Contents .= "
<form name=search_seller method='get'><!--SubmitX(this);'-->
<input type='hidden' name='mode' value='search'>
<input type='hidden' name=mc_ix value='".$mc_ix."'>
<input type='hidden' name='cid2' value='$cid2'>
<input type='hidden' name='depth' value='$depth'>
<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top style='padding:5px 0px 5px 0px'>
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
		<col width='12%'>
		<col width='*'>
		<tr height=27>
		  <td class='search_box_title' >검색 </td>
		  <td class='search_box_item' colspan=4>
				<table cellpadding=0 cellspacing=0 width=100%>
					<col width='80'>
					<col width='*'>
					<tr>
						<td>
						<select name=search_type>
								<option value='' ".CompareReturnValue("",$search_type,"selected")."> 선택 </option>
								<option value='company' ".CompareReturnValue("company",$search_type,"selected")."> 담당사업장 </option>
								<option value='top_company' ".CompareReturnValue("top_company",$search_type,"selected")."> 본사담당자 </option>
								<option value='company_code' ".CompareReturnValue("company_code",$search_type,"selected")."> 업체코드 </option>
								<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected")."> 사업자명 </option>
						</select>
						</td>
						<td>
							<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:20%;font-size:12px;padding:1px;' >
						</td>
					</tr>
				</table>
			</td>
		</tr>";
		if($_SESSION["admininfo"]["mallstory_version"] != "service"){
			$Contents .= "
			<tr height=27>
				<td class='input_box_title'> <b>담당사업장</b></td>
				<td class='search_box_item' colspan='4'>
					<table border=0 cellpadding=0 cellspacing=0>
						<tr>
							<td style='padding-right:5px;'>
							".getCompanyList("본사", "cid0_1", "onChange=\"loadCategory('cid0_1','cid1_1','company')\" title='선택' ", '5', $cid2,'company')."</td>
							<td style='padding-right:5px;'>
							".getCompanyList("선택", "cid1_1", "onChange=\"loadCategory('cid1_1','cid2_1','company')\" title='선택'", '15', $cid2,'company')."</td>
							<td style='padding-right:5px;'>
							".getCompanyList("선택", "cid2_1", "onChange=\"loadCategory('cid2_1','cid3_1','company')\" title='선택'", '25', $cid2,'company')."</td>
							<td>".getCompanyList("선택", "cid3_1", "onChange=\"loadCategory('cid3_1','','company')\" title='선택'", '35', $cid2,'company')."</td>
						</tr>
					</table>
				</td>
		  </tr>";
		}
	  $Contents .= "
	  <tr>
	  <td class='input_box_title'> <b>담당자</b></td>
		<td class='search_box_item' colspan='4'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>";
					
				if($_SESSION["admininfo"]["mallstory_version"] == "service"){
					$Contents .= "
					<td style='padding-right:5px;'>".get_person($com_group,$department,$position,$duty,$company_id,$com_person)."</td>";
				}else{
					$Contents .= "
					<td style='padding-right:5px;'>
					".getgroup1($com_group, "onChange=\"loadDepartment('com_group','department')\" title='본부선택' ",'true')."</td>
					<td style='padding-right:5px;'>
					".getdepartment($department,'','true')."</td>
					<td style='padding-right:5px;'>
					".getposition($position,'','true')."</td>
					<td>".getduty($duty,'','true')."</td>
					<td style='padding-left:5px;'>".get_person($com_group,$department,$position,$duty,$company_id,$com_person)."</td>";
				}
					$Contents .= "
				</tr>
			</table>
		 </td>
	  </tr>
		<tr height=27>
			  <td class='search_box_title' >거래처유형 </td>
			  <td class='search_box_item' colspan=4>
				<!--<input type=checkbox name='seller_type[]' value='a' id='seller_type_a'  ".CompareReturnValue("a",$seller_type,"checked")."><label for='seller_type_a'> 전체</label>&nbsp;&nbsp;체크박스라서 전체가 필요없음 kbk 13/08/09-->
			   <input type=checkbox name='seller_type[]' value='1' id='seller_type_1'  ".CompareReturnValue("1",$seller_type,"checked")."><label for='seller_type_1'> 국내 매출</label>&nbsp;&nbsp;
			   <input type=checkbox name='seller_type[]' value='2' id='seller_type_2'  ".CompareReturnValue("2",$seller_type,"checked")."><label for='seller_type_2'> 국내 매입</label>&nbsp;&nbsp;
			   <!--input type=checkbox name='seller_type[]' value='3' id='seller_type_3'  ".CompareReturnValue("3",$seller_type,"checked")."><label for='seller_type_3'> 해외 수출</label>&nbsp;&nbsp;
			   <input type=checkbox name='seller_type[]' value='4' id='seller_type_4'  ".CompareReturnValue("4",$seller_type,"checked")."><label for='seller_type_4'> 해외 수입</label>&nbsp;&nbsp;
			   <input type=checkbox name='seller_type[]' value='5' id='seller_type_5'  ".CompareReturnValue("4",$seller_type,"checked")."><label for='seller_type_5'> 외주 물류창고</label>&nbsp;&nbsp; -->
			  </td>
			</tr>
		 <tr height=27>
		  <td class='search_box_title' >국내외 구분 </td>
		  <td class='search_box_item'  colspan='4'>
			<input type=radio name='nationality' value='' id='nationality_'  ".CompareReturnValue("",$nationality,"checked")."><label for='nationality_'> 전체</label>&nbsp;&nbsp;
		   <input type=radio name='nationality' value='I' id='nationality_I'  ".CompareReturnValue("I",$nationality,"checked")."><label for='nationality_I'> 국내</label>&nbsp;&nbsp;
		   <input type=radio name='nationality' value='O' id='nationality_O'  ".CompareReturnValue("O",$nationality,"checked")."><label for='nationality_O'> 해외</label>&nbsp;&nbsp;
		   <input type=radio name='nationality' value='D' id='nationality_D'  ".CompareReturnValue("D",$nationality,"checked")."><label for='nationality_D'> 기타</label>&nbsp;&nbsp;
		  </td>
		</tr>
		<tr>
			<td class='search_box_title'>사업자 유형</td>
			<td class='search_box_item'  colspan='4'>
			   <input type=radio name='com_div' value='' id='com_div_'  ".CompareReturnValue("",$com_div,"checked")."><label for='com_div_'> 전체 </label>&nbsp;&nbsp;
			   <input type=radio name='com_div' value='R' id='com_div_1'  ".CompareReturnValue("R",$com_div,"checked")."><label for='com_div_1'> 법인사업자 </label>&nbsp;&nbsp;
			   <input type=radio name='com_div' value='P' id='com_div_2'  ".CompareReturnValue("P",$com_div,"checked")."><label for='com_div_2'> 개인사업자 </label>&nbsp;&nbsp;
			   <input type=radio name='com_div' value='S' id='com_div_3' ".CompareReturnValue("S",$com_div,"checked")."><label for='com_div_3'> 간이사업자 </label>&nbsp;&nbsp;
			   <input type=radio name='com_div' value='E' id='com_div_4' ".CompareReturnValue("E",$com_div,"checked")."><label for='com_div_4'> 면세 </label>&nbsp;&nbsp;
			   <input type=radio name='com_div' value='I' id='com_div_5' ".CompareReturnValue("I",$com_div,"checked")."><label for='com_div_5'> 수출입업체 </label>&nbsp;&nbsp;
			  </td>
		 </tr>
		<tr>
			<td class='search_box_title'>거래처 등급</td>
			<td class='search_box_item'  colspan='4'>
			   <input type=radio name='seller_level' value='' id='seller_level_'  ".CompareReturnValue("",$seller_level,"checked")."><label for='seller_level_'> 전체 </label>&nbsp;&nbsp;
			   <input type=radio name='seller_level' value='1' id='seller_level_1'  ".CompareReturnValue("1",$seller_level,"checked")."><label for='seller_level_1'> 우호 </label>&nbsp;&nbsp;
			   <input type=radio name='seller_level' value='2' id='seller_level_2'  ".CompareReturnValue("2",$seller_level,"checked")."><label for='seller_level_2'> 양호 </label>&nbsp;&nbsp;
			   <input type=radio name='seller_level' value='3' id='seller_level_3' ".CompareReturnValue("3",$seller_level,"checked")."><label for='seller_level_3'> 보통 </label>&nbsp;&nbsp;
			   <input type=radio name='seller_level' value='4' id='seller_level_4' ".CompareReturnValue("4",$seller_level,"checked")."><label for='seller_level_4'> 위험 </label>&nbsp;&nbsp;
			   <input type=radio name='seller_level' value='5' id='seller_level_5' ".CompareReturnValue("5",$seller_level,"checked")."><label for='seller_level_5'> 블랙리스트 </label>&nbsp;&nbsp;
			  </td>
		  </tr>
		    ";

		$vdate = date("Ymd", time());
		$today = date("Ymd", time());
		$vyesterday = date("Ymd", time()-84600);
		$voneweekago = date("Ymd", time()-84600*7);
		$vtwoweekago = date("Ymd", time()-84600*14);
		$vfourweekago = date("Ymd", time()-84600*28);
		$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
		$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
		$v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
		$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
		$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

	 $Contents .= "
				<tr height=27>
				  <td class='search_box_title' ><label for='regdate'>거래처 등록일</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_seller);' ".CompareReturnValue("1",$regdate,"checked")."></td>
				  <td class='search_box_item'  colspan=4 >
					<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
						<col width=100>
						<col width=20>
						<col width=100>
						<col width=*>
						<tr>
							<TD nowrap>
							<input type='text' name='sdate' class='textbox point_color' value='".$sdate."' style='height:20px;width:100px;text-align:center;' id='start_datepicker'>
							<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
							<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
							<SELECT name=FromDD></SELECT> 일 -->
							</TD>
							<TD align=center> ~ </TD>
							<TD nowrap>
							<input type='text' name='edate' class='textbox point_color' value='".$edate."' style='height:20px;width:100px;text-align:center;' id='end_datepicker'>
							<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
							<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
							<SELECT name=ToDD></SELECT> 일 -->
							</TD>
							<TD style='padding:0px 10px'>
								<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
								<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
								<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
								<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
								<a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
								<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
								<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
							</TD>
						</tr>
					</table>
				  </td>
				</tr>
				</table>
			</td>
			<th class='box_06'></th>
		</tr>
		<tr>
			<th class='box_07'></th>
			<td class='box_08'></td>
			<th class='box_09'></th>
		</tr>
	</table>
    </td>
  </tr>
  <tr height=50>
    	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle ></td>
    </tr>
</table>
</form>
";

$Contents .= "
<form name='list_frm'>
<input type='hidden' name='code[]' id='code'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr>
		<td colspan=1>
		</td>
		<td align='right' colspan=3 style='padding:5px 0 5px 0;'>
		<a href=\"javascript:PoPWindow3('seller.report.php?mmode=pop&info_type=".$info_type."&".$QUERY_STRING."',1200,800,'stock_report')\"> <img src='../images/".$admininfo["language"]."/btn_report_print.gif'></a>
		<a href='?mmode=pop'> </a> ";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."&info_type=seller_list' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$Contents .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= " <a href='seller.list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
		$Contents .= "
		</td>
	</tr>
</table>";


$Contents .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='2%' align='center' class=s_td ><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='2%' align='center' class='m_td' ><font color='#000000'><b>순번</b></font></td>
    <td width='3%' align='center' class='m_td' ><font color='#000000'><b>등급</b></font></td>
    <td width='5%' align='center' class='m_td' ><font color='#000000'><b>거래시작일</b></font></td>
    <td width='5%' align='center' class=m_td ><font color='#000000'><b>업체코드</b></font></td>
    <!--<td width='10%' align='center' class=m_td ><font color='#000000'><b>거래처유형</b></font></td>-->

	<td width='5%' align='center' class=m_td ><font color='#000000'><b>담당사업장</b></font></td>
	<td width='5%' align='center' class=m_td ><font color='#000000'><b>담당자</b></font></td>

    <td width='7%' align='center' class=m_td ><font color='#000000'><b>사업자명</b></font></td>
	<td width='6%' align='center' class=m_td ><font color='#000000'><b>사업자유형<br></b></font></td>
	<td width='4%' align='center' class=m_td ><font color='#000000'><b>국내외<br>구분</b></font></td>
    <td width='6%' align='center' class=m_td ><font color='#000000'><b>대표전화</b></font></td>
	<td width='5%' align='center' class=m_td ><font color='#000000'><b>대표이메일</b></font></td>
	<td width='6%' align='center' class=m_td ><font color='#000000'><b>담당자<br>전화번호</b></font></td>
	<td width='6%' align='center' class=m_td ><font color='#000000'><b>담당자<br>핸드폰번호</b></font></td>
    <td width='7%' align='center' class=e_td ><font color='#000000'><b>관리</b></font></td>
  </tr>";

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}else{
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}

		$mdb->fetch(0);
		$gp_name = $mdb->dt[gp_name];

        if($db->dt[is_id_auth] != "Y"){
            $is_id_auth = "미인증";
        }else{
            $is_id_auth = "";
        }

        switch($db->dt[authorized]){
        case "Y":
            $authorized = "승인";
            break;
        case "N":
            $authorized = "승인대기";
            break;
        case "X":
            $authorized = "승인거부";
            break;
        default:
            $authorized = "알수없음";
            break;
        }

        switch($db->dt[com_div]){
        case "P":
            $com_div = "개인(일반사업자)";
            break;
        case "R":
            $com_div = "법인";
            break;
        case "S":
            $com_div = "간이과세자";
            break;
        case "E":
            $com_div = "면세과세자";
            break;
        case "I":
            $com_div = "수출입업자";
            break;
        }

		$seller_array = explode("|",$db->dt[seller_type]);


		if(is_array($seller_array)){
			foreach($seller_array as $key => $value){
				if($value == "1"){
					$seller_type = "국내매출";
				}
				if($value == "2"){
					$seller_type .= "| 국내매입";
				}
				if($value == "3"){
					$seller_type .= "| 해외수출";
				}
				if($value == "4"){
					$seller_type .= "| 해외수입";
				}
				if($value == "5"){
					$seller_type .= "| 외주물류";
				}
			}
		}
		switch($db->dt[nationality]){
			case "I":
				$nationality = "국내";
			break;
			case "O":
				$nationality = "해외";
			break;
			case "D":
				$nationality = "기타";
			break;
		}

		switch($db->dt[seller_level]){
			case "1":
				$seller_level = "우호";
			break;
			case "2":
				$seller_level = "양호";
			break;
			case "3":
				$seller_level = "보통";
			break;
			case "4":
				$seller_level = "위험";
			break;
			case "5":
				$seller_level = "블랙리스트";
			break;
		}

		if($db->dt[seller_date]){
			$seller_array = explode (" ",$db->dt[seller_date]);
			$seller_date = $seller_array[0];
		}else{
			$seller_date = "-";
		}
		
		if($db->dt[company_id]){
		
			$sql = "select relation_code from common_company_relation where		company_id = '".$db->dt[company_id]."'";
			$rdb->query($sql);
			$relation_array = $rdb->fetch();
			$relation_code = $relation_array[relation_code];
		}

		$person_code = $db->dt[person];

        $Contents = $Contents."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' style='padding:0px 5px;'>".$seller_level."</td>
            <td class='list_box_td' nowrap>".$seller_date."</td>
            <td class='list_box_td' >".$db->dt[company_code]."</td>
           <!-- <td class='list_box_td point' nowrap>".$seller_type."</td>-->

			<td class='list_box_td' >".getCompanyname($relation_code,'5')."</a></td>
			<td class='list_box_td' >".personName($person_code)."</a></td>

            <td class='list_box_td' >".$db->dt[com_name]."</a></td>
            <td class='list_box_td' >".$com_div."</font></td>
            <td class='list_box_td' >".$nationality."</td>
			<td class='list_box_td' >".$db->dt[com_phone]."</a></td>
            <td class='list_box_td' >".$db->dt[com_email]."</font></td>
            <td class='list_box_td' >".$db->dt[customer_phone]."</td>
			<td class='list_box_td' >".$db->dt[customer_mobile]."</td>
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";
			
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$type = array('BR','BP','BO');
				if(in_array($db->dt[com_type],$type)){
				$Contents .="
				<a href=\"javascript:PoPWindow3('/admin/basic/seller.add.php?mmode=pop&company_id=".$db->dt[company_id]."&info_type=basic',1200,800,'seller_add')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
				";
				}else if($db->dt[com_type] == "C"){
					$Contents .="
					<a href=\"javascript:PoPWindow3('/admin/basic/seller.add.php?mmode=pop&company_id=".$db->dt[company_id]."&info_type=basic',1200,800,'seller_add')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
					";
				}else{
				 $Contents .="
				<a href=\"javascript:PoPWindow3('/admin/basic/seller.add.php?mmode=pop&company_id=".$db->dt[company_id]."&info_type=basic',1200,800,'seller_add')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
				";
				}
			}else{
				$Contents .="
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
					";
			}
			

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .="
					<a href=\"JavaScript:DeleteCompanySeller('".$db->dt[company_id]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
			}else{
				$Contents .="
					<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
			}

			$Contents .= "
    </td>
  </tr>";
	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='15' align='center'>등록된 데이타가 없습니다.</td>
  </tr>";
}

$Contents .= "
</table>
</form>
<table width=100%>
<tr>
	<td align='right'>".$str_page_bar."</td>

</tr>
</table>";

$Contents .= "<table width=100%>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents .= "<tr hegiht=40><td colspan=1 align=right style='padding-top:10px;'><a href=\"javascript:PoPWindow3('/admin/basic/seller.add.php?mmode=pop&info_type=basic',1200,800,'seller_add')\"><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td></tr>";
}else{
	$Contents .= "<tr hegiht=40><td colspan=1 align=right style='padding-top:10px;'><a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td></tr>";
}

$Contents .="</table><br>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원정보를 백업하기 위해서는 회원정보 검색후 엑셀로 저장 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다</td></tr>
</table>
";

//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("입고처 관리", $help_text,'70');

$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = inventory_menu();
$P->Navigation = "재고관리 > 기초정보관리 > $menu_name";
$P->title = "입고처 리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>



