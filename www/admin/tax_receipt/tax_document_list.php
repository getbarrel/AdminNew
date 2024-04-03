<?
include("../class/layout.class");

$db = new Database;
$db2 = new Database;
$mdb = new Database;

$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($FromYY == ""){


//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");

}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

}

if ($vFromYY == ""){

	$sDate2 = date("Y/m/d", $before10day);
	$eDate2 = date("Y/m/d");

	$startDate2 = date("Ymd", $before10day);
	$endDate2 = date("Ymd");
}else{

	$sDate2 = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate2 = $vToYY."/".$vToMM."/".$vToDD;
	$startDate2 = $vFromYY.$vFromMM.$vFromDD;
	$endDate2 = $vToYY.$vToMM.$vToDD;
}

$Script = "<script language='javascript'>
function popupDelete(popup_ix){
	if(confirm(language_data['popup.list.php']['A'][language]))
	{//'해당 팝업를 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'
		//document.frames('act').location.href= 'popup.act.php?act=delete&popup_ix='+popup_ix;
		window.frames['act'].location.href= 'popup.act.php?act=delete&popup_ix='+popup_ix;
	}


}
function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
	}else{
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	}
}
/*
function ChangeVisitDate(frm){
	if(frm.visitdate.checked){
		frm.vFromYY.disabled = false;
		frm.vFromMM.disabled = false;
		frm.vFromDD.disabled = false;
		frm.vToYY.disabled = false;
		frm.vToMM.disabled = false;
		frm.vToDD.disabled = false;
	}else{
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;
	}
}*/

function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate','$sDate2','$eDate2');";

if($regdate != "1"){
$Script .= "
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;";
}/*
if($visitdate != "1"){
$Script .= "
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;	";
}*/
$Script .= "
}

function init_date(FromDate,ToDate,FromDate2, ToDate2) {
	var frm = document.searchmember;


	for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate.substring(8,10))
			frm.FromDD.options[i].selected=true
	}


	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate.substring(8,10))
			frm.ToDD.options[i].selected=true
	}



/*
	for(i=0; i<frm.vFromYY.length; i++) {
		if(frm.vFromYY.options[i].value == FromDate2.substring(0,4))
			frm.vFromYY.options[i].selected=true
	}
	for(i=0; i<frm.vFromMM.length; i++) {
		if(frm.vFromMM.options[i].value == FromDate2.substring(5,7))
			frm.vFromMM.options[i].selected=true
	}
	for(i=0; i<frm.vFromDD.length; i++) {
		if(frm.vFromDD.options[i].value == FromDate2.substring(8,10))
			frm.vFromDD.options[i].selected=true
	}


	for(i=0; i<frm.vToYY.length; i++) {
		if(frm.vToYY.options[i].value == ToDate2.substring(0,4))
			frm.vToYY.options[i].selected=true
	}
	for(i=0; i<frm.vToMM.length; i++) {
		if(frm.vToMM.options[i].value == ToDate2.substring(5,7))
			frm.vToMM.options[i].selected=true
	}
	for(i=0; i<frm.vToDD.length; i++) {
		if(frm.vToDD.options[i].value == ToDate2.substring(8,10))
			frm.vToDD.options[i].selected=true
	}

*/
}



function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	if(dType == 1){
		for(i=0; i<frm.FromYY.length; i++) {
			if(frm.FromYY.options[i].value == FromDate.substring(0,4))
				frm.FromYY.options[i].selected=true
		}
		for(i=0; i<frm.FromMM.length; i++) {
			if(frm.FromMM.options[i].value == FromDate.substring(5,7))
				frm.FromMM.options[i].selected=true
		}
		for(i=0; i<frm.FromDD.length; i++) {
			if(frm.FromDD.options[i].value == FromDate.substring(8,10))
				frm.FromDD.options[i].selected=true
		}


		for(i=0; i<frm.ToYY.length; i++) {
			if(frm.ToYY.options[i].value == ToDate.substring(0,4))
				frm.ToYY.options[i].selected=true
		}
		for(i=0; i<frm.ToMM.length; i++) {
			if(frm.ToMM.options[i].value == ToDate.substring(5,7))
				frm.ToMM.options[i].selected=true
		}
		for(i=0; i<frm.ToDD.length; i++) {
			if(frm.ToDD.options[i].value == ToDate.substring(8,10))
				frm.ToDD.options[i].selected=true
		}
	}/*else{
		for(i=0; i<frm.vFromYY.length; i++) {
			if(frm.vFromYY.options[i].value == FromDate.substring(0,4))
				frm.vFromYY.options[i].selected=true
		}
		for(i=0; i<frm.vFromMM.length; i++) {
			if(frm.vFromMM.options[i].value == FromDate.substring(5,7))
				frm.vFromMM.options[i].selected=true
		}
		for(i=0; i<frm.vFromDD.length; i++) {
			if(frm.vFromDD.options[i].value == FromDate.substring(8,10))
				frm.vFromDD.options[i].selected=true
		}


		for(i=0; i<frm.vToYY.length; i++) {
			if(frm.vToYY.options[i].value == ToDate.substring(0,4))
				frm.vToYY.options[i].selected=true
		}
		for(i=0; i<frm.vToMM.length; i++) {
			if(frm.vToMM.options[i].value == ToDate.substring(5,7))
				frm.vToMM.options[i].selected=true
		}
		for(i=0; i<frm.vToDD.length; i++) {
			if(frm.vToDD.options[i].value == ToDate.substring(8,10))
				frm.vToDD.options[i].selected=true
		}
	}*/

}




function onLoad(FromDate, ToDate,FromDate2, ToDate2) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
/*	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate2);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate2);*/

	init_date(FromDate,ToDate,FromDate2, ToDate2);

}
</script>";

if($disp_yn=="") {
	$disp_yn="all";
}

$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center >
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("기장자료등록리스트", "세무관리 > 세무기장관리 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=".urlencode("몰스토리동영상메뉴얼_팝업등록(090108)_config.xml")."',800,517,'manual_view')\"  title='팝업등록 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a>")."</td>
		</tr>
		<tr>
			<td>";
		$mstring .= "
		<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:5px 5px 5px 5px'>
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
				<form name=searchmember method='get' ><!--SubmitX(this);'-->
					<col width='15%'>
					<col width='*'>
					<tr >
					  <td class='search_box_title'>조건검색 </td>
					  <td class='search_box_item'>
						  <select name=search_type>
								<option value='popup_title' ".CompareReturnValue("company_name",$search_type,"selected").">상호명</option>
						  </select>
						  <input type=text name='search_text' class='textbox' value='".$search_text."' style='width:50%; vertical-align:top;' >
					  </td>
					</tr>
					<tr >
					  <td class='search_box_title'>담당자명 </td>
					  <td class='search_box_item'>
						  <input type=text name='charger' class='textbox' value='' style='width:20%; vertical-align:top;' >
					  </td>
					</tr>
					<tr>
					  <td class='search_box_title'>처리상태 </td>
					  <td class='search_box_item'>
						  <input type=radio name='status' value='' id='disp_a'  ".CompareReturnValue("",$status,"checked")."><label for='disp_a'>전체</label>
						  <input type=radio name='status' value='Y' id='disp_y'  ".CompareReturnValue("Y",$status,"checked")."><label for='disp_y'>처리완료</label><input type=radio name='status' value='N' id='disp_n' ".CompareReturnValue("N",$status,"checked")."><label for='disp_n'>미처리</label>
					  </td>
					</tr>
					";

		$vdate = date("Ymd", time());
		$today = date("Y/m/d", time());
		$vyesterday = date("Y/m/d", time()-84600);
		$voneweekago = date("Y/m/d", time()-84600*7);
		$vtwoweekago = date("Y/m/d", time()-84600*14);
		$vfourweekago = date("Y/m/d", time()-84600*28);
		$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
		$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
		$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
		$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
		$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

		 $mstring .= "
					<tr >
					  <td class='search_box_title'><label for='regdate'>업로드일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
					  <td class='search_box_item'>
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
						<tr>
							<TD width=17% nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
							<TD width=3% align=center> ~ </TD>
							<TD width=22% nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
							<TD width='*'>
								<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
								<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
								<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
								<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
								<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
							</TD>
						</tr>
					</table>
					  </td>
					</tr>
					<!--tr height=27>
					  <td class='search_box_title'><label for='visitdate'>종료일자</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);' ".CompareReturnValue("1",$visitdate,"checked")."></td>
					  <td class='search_box_item'>
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
						<tr>
							<TD width=17% nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
							<TD width=3% align=center> ~ </TD>
							<TD width=22% nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>
							<TD width='*'>
								<a href=\"javascript:select_date('$voneweekago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
								<a href=\"javascript:select_date('$v15ago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
								<a href=\"javascript:select_date('$vonemonthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
								<a href=\"javascript:select_date('$v2monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
								<a href=\"javascript:select_date('$v3monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
							</TD>
						</tr>
					</table>
					  </td>
					</tr-->

					</table>
				</td>
				<th class='box_06'></th>
			</tr>
			<tr>
				<th class='box_07'></th>
				<td class='box_08'></td>
				<th class='box_09'></th>
			</tr>
		</table>";
		$mstring .= "
			</td>
		</tr>
		<tr height=60>
			<td style='padding:10px 0px;' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
		</tr>
		</form>
		<tr>
			<td>
			".PrintEventList()."
			</td>
		</tr>";
$mstring .="</table>";
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>팝업 추가</b>를 원하시면 팝업 추가버튼을 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 <u>팝업는 </u> 사용으로 되어 있는 팝업만 메인에서 자동으로 노출됩니다.  </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >작업을 하실때는 표시여부를 <u>표시하지 않음</u>으로 설정한후 작업이 완료되면 다시 표시로 변경하시면 메인에 노출되게 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >작업하신 파일을 노출하기전 미리 확인하시길 원하시면 <b>팝업 미리보기</b> 버튼을 클릭하시면 확인하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>기간이 만료된 팝업</u>는 <u>자동으로 노출이 종료</u>됩니다</td></tr>
</table>
";*/
	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


//$help_text = HelpBox("팝업 관리", $help_text);
$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>기장자료등록리스트</b></td><td style='padding:0px 0px 3px 3px'><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_팝업등록(090108)_config.xml',800,517,'manual_view')\"  title='팝업등록 동영상 메뉴얼입니다' style='cursor:pointer;'><img src='../image/movie_manual.gif' width=26 height=20 style='position:absolute;top:-1px;'></a></td></tr></table>", $help_text,100);
$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->Navigation = "세무서비스 > 세무기장관리";
$P->title = "기장자료등록리스트";
$P->strLeftMenu = tax_receipt();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintEventList(){
	global $db,$db2, $mdb, $page, $search_type,$search_text,$disp_yn,$status;
	global $FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,$vFromYY,$vFromMM,$vFromDD,$vToYY,$vToDD,$vToMM;
	global $auth_delete_msg, $admininfo;


	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$where = " where re_ix <> '0' ";

	if($disp_yn == "1"){
		$where .= " and disp =  '1' ";
	}else if($disp_yn == "0"){
		$where .= " and disp = '0' ";
	}
	
	if($status == "Y"){
		$where .= " and status = 'Y'";
	}else if($status == "N"){
		$where .= " and status = 'N'";
	}
	
	if($search_type != "" && $search_text != ""){
		$where .= " and $search_type LIKE  '%$search_text%' ";
	}

	$startDate = $FromYY.$FromMM.$FromDD;



	$endDate = $ToYY.$ToMM.$ToDD;


	if($startDate != "" && $endDate != ""){
		$where .= " and  popup_use_sdate between  $startDate and $endDate ";
	}

	$vstartDate = $vFromYY.$vFromMM.$vFromDD;


	$vendDate = $vToYY.$vToMM.$vToDD;

/*
	if($vstartDate != "" && $vendDate != ""){
		$where .= " and  popup_use_edate between  $vstartDate and $vendDate ";
	}*/
	$sql = "select * from tax_receipt_input $where ";
	$mdb->query($sql);
	$total = $mdb->total;

	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box'>";
	$mString = $mString."
		<tr align=center bgcolor=#efefef height=30>
			<td class=s_td width='1%'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)' /></td>
			<td class=m_td width='5%'>순번</td>
			<td class=m_td width='10%'>상호명</td>
			<td class=m_td width='10%'>업로드일</td>
			<td class=m_td width='10%'>구분</td>
			<td class=m_td width='10%'>분기</td>
			<td class=m_td width='10%'>등록자료</td>
			<td class=e_td width='10%'>담당부서</td>
			<td class=e_td width='10%'>담당자</td>
			<td class=e_td width='10%'>상세보기</td>
		</tr>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=10 align=center>기장자료가 존재 하지 않습니다.</td></tr>";
		$mString .= "<tr><td colspan=10 class='dot-x'></td></tr>";
		//$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right><a href='popup.write.php'><img src='../images/".$admininfo["language"]."/b_popupadd.gif' border=0 ></a></td></tr>";
	}else{

		$db->query("select * from tax_receipt_input  $where order by  regdate desc limit $start, $max");
		
		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$no = $total - ($page - 1) * $max - $i;
			
			$sql = "select * from shop_company_department where dp_ix= '".$db->dt[department]."' ";
			$db2->query($sql);
			$db2->fetch();
			$dp_name = $db2->dt[dp_name];
			//$no = $no + 1;
			if($db->dt[receipt_div] == '1'){
				$receipt_div = "신용카드";
			}else if($db->dt[receipt_div] == '2'){
				$receipt_div = "현금영수증";
			}else{
				$receipt_div = "-";
			}
			
			if($db->dt[status] == "Y"){
				$status = " 처리완료";
			}else{
				$status = " 미처리";
			}
			
			if($db->dt[tax_file]){
				$tax_file = "I/S";
			}else{
				$tax_file = "";
			}
			if($db->dt[income_bill]){
				$income_bill = "B/S";
			}else{
				$income_bill = "";
			}
			
			$input_data = $tax_file.",".$income_bill;
			
			$mString = $mString."<tr height=30 >
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[re_ix]."'></td>
			<td class='list_box_td'>".$no."</td>
			<td class='list_box_td'>".$db->dt[company_name]."</td>
			<td class='list_box_td'>".$db->dt[regdate]."</td>
			<td class='list_box_td'>".$receipt_div."</td>
			<td class='list_box_td'>".$db->dt[quarter]."분기</td>
			<td class='list_box_td'>".$input_data."</td>
			<td class='list_box_td'>".$dp_name."</td>
			<td class='list_box_td'>".$status."</td>
			<td class='list_box_td list_bg_gray'>
			<a href=\"javascript:PopSWindow('tax_document_input.php?code=".$db->dt[code]."',700,600,'tax_document_input')\" style='cursor:pointer' >수정하기</a>
			</td>
			</tr>
			";
		}

		$mString .= "</table>
					<table cellpadding=0 cellspacing=0 border=0 width=100% >
					<tr height=50 bgcolor=#ffffff>
					<td colspan=3 align=left>".page_bar($total, $page, $max,  "&max=$max&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD&disp_yn=$disp_yn","")."</td>
					<td colspan=2 align=right>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		//$mString .= "<a href='popup.write.php'><img src='../images/".$admininfo["language"]."/b_popupadd.gif' border=0 ></a>";
		}
		$mString .= "</td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}


?>
