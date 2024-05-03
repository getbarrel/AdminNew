<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

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

$Script = "<script language='javascript'>

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
}

function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate');
	ChangeVisitDate(frm);
	
}

function init_date(FromDate,ToDate) {
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
	}else{
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
	}

}




function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);

	init_date(FromDate,ToDate);

}
</script>";


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("사용자 접속 로그", "회원관리 > 사용자 접속 로그 ")."</td>
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
				<td class='box_05'  valign=top >
				<form name=searchmember method='get' >
				<table width='100%' border='0' cellspacing='0' cellpadding='0' class='search_table_box'>
					<col width='20%'>
					<col width='*'>
					<tr height=27>
					  <td class='search_box_title' >조건검색 </td>
					  <td class='search_box_item'>
						<table cellpadding=0 cellspacing=0 width='100%'>
							<col width='70px'>
							<col width='*'>
							<tr>
								<td>
								  <select name=search_type>
									<option value='mem_id' ".CompareReturnValue("mem_id",$search_type,"selected").">접속 ID</option>
									<option value='ip' ".CompareReturnValue("ip",$search_type,"selected").">접속 IP</option>
								  </select>
								</td>
								<td style='padding:0px 3px;'><input type=text name='search_text' class='textbox' value='".$search_text."' style='width:50%;' ></td>
							</tr>
						</table>
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
					<tr height=27>
					  <td class='search_box_title'><label for='regdate'>검색일자</label><input type='checkbox' name='regdate' id='regdate' value='1' ".($regdate==1 ? "checked":"")." onclick='ChangeRegistDate(document.searchmember);'></td>
					  <td class='search_box_item'>
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
							<tr>
								<TD width=220 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
								<TD width=20 align=center> ~ </TD>
								<TD width=220 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
								<TD>
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
					<tr height=27 style='display:none'>
					  <td class='search_box_title'><label for='visitdate'>종료일자</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);'></td>
					  <td class='search_box_item'>
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
							<tr>
								<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
								<TD width=20 align=center> ~ </TD>
								<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>
								<TD>
									<a href=\"javascript:select_date('$voneweekago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
									<a href=\"javascript:select_date('$v15ago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
									<a href=\"javascript:select_date('$vonemonthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
									<a href=\"javascript:select_date('$v2monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
									<a href=\"javascript:select_date('$v3monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
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
		</table>";
		$mstring .= "
			</td>
		</tr>
		<tr>
			<td style='padding:10px 0px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
		</tr>
		</form>
		<tr>
			<td>
			".PrintLogList()."
			</td>
		</tr>";
$mstring .="</table>";

		 $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>사용자 접속 로그</b></td><td></td></tr></table></div>", $help_text,170)."</div>";
$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->Navigation = "회원관리 > 개인정보관리 > 사용자 접속 로그";
$P->title = "사용자 접속 로그";
$P->strLeftMenu = member_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintLogList(){
	global $db, $mdb, $page, $search_type,$search_text,$disp_yn;
	global $FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,$vFromYY,$vFromMM,$vFromDD,$vToYY,$vToDD,$vToMM;



	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$where = array();

	if($search_type != "" && $search_text != ""){
		$where[] = "$search_type LIKE  '%$search_text%'";
	}

	$startDate = $FromYY.$FromMM.$FromDD;



	$endDate = $ToYY.$ToMM.$ToDD;


	if($startDate != "" && $endDate != ""){
		$where[] = "log_date between  $startDate and $endDate";
	}

	$where = (count($where))	?	" WHERE ".implode(" AND ", $where):' ';

	$sql = "select COUNT(*) as total from member_log $where ";

	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];

	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% bgcolor=silver class='list_table_box'>";
	$mString = $mString."
	<tr align=center bgcolor=#efefef height=25>
		<td class=s_td width='10%'>번호</td>
		<td class=m_td width='15%'>구분</td>
		<td class=m_td width='15%'>접속 ID</td>
		<td class=m_td width='15%'>로그시간</td>
		<td class=m_td width='15%'>로그정보</td>
		<td class=m_td width='15%'>접속방법</td>
		<td class=e_td width='15%'>접속 IP</td>
	</tr>";
	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=7 align=center>로그 내역이 존재 하지 않습니다.</td></tr>";
	}else{

		$db->query("select * from member_log  $where order by  log_date desc limit $start, $max");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			$no = $no + 1;

			if($db->dt[log_div] == "I")
			{
				$log_div = "로그인성공";
				$log_gubun = "로그인";
			}
			else if($db->dt[log_div] == "O")
			{
				$log_div = "로그아웃";
				$log_gubun = "로그인";
			}
			else if($db->dt[log_div] == "N")
			{
				$log_div = "로그인실패";
				$log_gubun = "로그인";
			}
			else if($db->dt[log_div] == "S")
			{
				$log_div = "성공";
				$log_gubun = "회원정보";
			}
			else if($db->dt[log_div] == "F")
			{
				$log_div = "실패";
				$log_gubun = "회원정보";
			}

			if($db->dt[gubun] == "P")
			{
				$gubun = "PC";
			}
			else if($db->dt[gubun] == "M")
			{
				$gubun = "Mobile";
			}

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td class='list_box_td list_bg_gray' >$no</td>
			<td class='list_box_td point'>".$log_gubun."</td>
			<td class='list_box_td point'>".$db->dt[mem_id]."</td>
			<td class='list_box_td'>".$db->dt[log_date]."</td>
			<td class='list_box_td list_bg_gray'>".$log_div."</td>
			<td class='list_box_td list_bg_gray'>".$gubun."</td>
			<td class='list_box_td point'>".$db->dt[ip]."</td>
			</tr>
			";
		}


	}


	$mString .= "</table>";
	$mString .= "<ul class='paging_area' >
						<li class='front'>".page_bar($total, $page, $max,  "&max=$max&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD&disp_yn=$disp_yn","")."</li>
						<li class='back'></li>
					  </ul>";

	return $mString;
}


?>
