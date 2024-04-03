<?
include("../class/layout.class");
include ("company.lib.php");
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
	$menu_name = "거래처 일괄등록";
}else{
	$menu_name = "거래처 일괄등록";
}

if($max ==""){
	$max = 15; //페이지당 갯수
}
	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}


include "seller_query.php";

$Script = "<script language='javascript' src='../include/DateSelect.js'></script>\n
<script language='javascript' src='./company.add.js'></script>\n";
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

";

$Script .= "
function deleteMemberInfo(act, code){
 	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
 	}
}
</script>";


$Contents = "


<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("거래처 리스트", "기초정보관리 > 거래처관리 ")."</td>
  </tr>
	<tr>
			    <td align='left' colspan=4 style='padding-bottom:20px;'> 
			    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='seller.list.php'\">거래처 리스트</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'  class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='seller.lump.php'\">거래처 일괄등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<!--<table id='tab_03' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='member.lump.php'\">일괄등록하기</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >탭 메뉴 4</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_05' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >탭 메뉴 5</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_06' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >탭 메뉴 6</td>
								<th class='box_03'></th>
							</tr>
							</table-->
						</td>
						<td class='btn'>						
							
						</td>
					</tr>
					</table>	
				</div>
			    </td>
			</tr>
  <tr>
  	<td>";
$Contents .= "
<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:2px 5px 2px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 거래처 일괄등록</b>&nbsp;&nbsp;&nbsp;<b id='select_category_path1'>전체 <span class=small><!--선택된 카테고리가 없습니다. 좌측 카테고리에서 선택해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span></b> <a href='basic_seller_list.xls'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a><- 대량등록엑셀샘플 파일이 업데이트(2012-08-12) 되었습니다. 파일을 다시 다운받아서 사용해주시기 바랍니다.</div>")."</td>
	 </tr>
	 <tr>
		<td colspan=3>
		<form name='excel_input_form' method='post' action='seller.lump.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''>
		<!--form name='excel_input_form' method='post' action='product_input_excel_2003.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)'-->
		<input type='hidden' name='act' value='excel_input'>
		<input type='hidden' name='cid' value=''>
		<input type='hidden' name='depth' value=''>
		<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
			<col width=17%>
							<col width=33%>
							<col width=17%>
							<col width=33%>
			<tr height=30 align=center>
				<td class='input_box_title' ><b>엑셀파일 입력</b>  </td>
				<td class='input_box_item' colspan=3><input type=file class='textbox' name='excel_file' style='height:22px;width:90%' validation=true title='엑셀파일 입력'></td>
			</tr>
			</table>
			<table width='100%' border=0 cellpadding=0 cellspacing=1 >
			<tr height=20>
				<td style='padding:6px;line-height:140%;' colspan=2><img src='../image/emo_3_15.gif' border=0 align=absmiddle><!-- 엑셀정보에는 <b>' (따옴표)</b>는 사용하실수 없습니다. <b>엑셀정보에 카테고리를 지정</b>하시면 카테고리를 선택 안하셔도 해당 카테고리로 상품이 등록 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</td></tr>
			<tr height=30><td colspan=2 style='padding:10px 0px;' align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0></td></tr>
		</table>
		</form>
		</td>
	 </tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>";

$Contents .= "
    </td>
  </tr>
</table><br></form>";

$Contents .= "
<form name='list_frm'>
<input type='hidden' name='code[]' id='code'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
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
				<a href='customer.add.php?company_id=".$db->dt[company_id]."&info_type=basic'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
				";
				}else if($db->dt[com_type] == "C"){
					$Contents .="
					<a href='seller.add.php?company_id=".$db->dt[company_id]."&info_type=basic'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
					";
				}else{
				 $Contents .="
				<a href='company.add.php?company_id=".$db->dt[company_id]."&info_type=basic'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
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
			if($create_auth){
			     $Contents .= "
        	     <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[person]."',500,380,'sendsms')\">
        	     <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[person]."',550,535,'sendmail')\">
                 ";
            }else{
                $Contents .= "
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle></a>
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle></a>
                 ";
            }
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				if($admininfo[mall_type] == "O"){

					//$Contents .=($db->dt[recommend] != "Y" ? "<a href=\"javascript:RecommendCompany('".$db->dt[company_id]."', 'Y')\"><img src='../images/".$admininfo["language"]."/btn_recommend.gif' border=0 align='absmiddle'></a>" : "")."";
				}
			}
			$Contents .= "
    </td>
  </tr>";
	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='16' align='center'>등록된 데이타가 없습니다.</td>
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

$Contents .= HelpBox("거래처 관리", $help_text,'70');

$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";
$P->strLeftMenu = basic_menu();
$P->Navigation = "기초정보관리 > 거래처 관리 > $menu_name";
$P->title = "거래처 리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>



