<?
include("../class/layout.class");

include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

$db = new Database;
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
function eventDelete(coupon_ix){
	if(confirm(language_data['sns_sp_coupon.list.php']['A'][language]))
	{//'해당 쿠폰을 정말로 삭제하시겠습니까?'
		window.frames['act'].location.href= 'sp_coupon.act.php?act=delete&coupon_ix='+coupon_ix;
		//document.getElementById('act').src= 'sp_coupon.act.php?act=delete&coupon_ix='+coupon_ix;
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
	onLoad('$sDate','$eDate','$sDate2','$eDate2');";

if($regdate != "1"){
$Script .= "
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;";
}
if($visitdate != "1"){
$Script .= "
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;	";
}
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




function onLoad(FromDate, ToDate,FromDate2, ToDate2) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate2);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate2);

	init_date(FromDate,ToDate,FromDate2, ToDate2);

}

function change_cate(obj,element,pid,cname) {
	var cid=obj.value;
	var ch_type=obj.getAttribute('ch_type');
	if(pid!='') var txt='&pid='+pid;
	else var txt='';
	$('iframe[name=act]').attr('src','change_coupon_cate.php?cid='+cid+'&element='+element+'&ch_type='+ch_type+'&cname='+cname+txt);
}
</script>";


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("스페셜쿠폰 관리", "소셜커머스 > 스페셜쿠폰 관리")."</td>
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
				<td class='box_05'  valign=top style='padding:1px'>
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
					<col width='120px'>
					<col width='*'>
					<col width='120px'>
					<col width='*'>
				<form name=searchmember method='get' ><!--SubmitX(this);'-->
					<tr>
					  <td class='search_box_title'>쿠폰검색 </td>
					  <td class='search_box_item'>
					  <select name=cid style='width:80px;' onChange=\"change_cate(this,'searchmember','','pid')\" ch_type='A'>
							<!--option value='event_title' ".CompareReturnValue("name",$cid,"selected").">이벤트/기획전제목</option-->
							<option value=''>지역선택</option>
							".load_coupon_category($cid)."
					  </select>
					  <select name=pid style='width:400px;'>
							<option value=''>쿠폰선택</option>
							".load_coupon_category2($cid,$pid)."
					  </select>
					  <!--input type=text name='pid' value='".$pid."' style='width:30%' -->
					  </td>
					</tr>
					<tr>
					  <td class='search_box_title'>표시여부 </td>
					  <td class='search_box_item'>
					  <input type=radio name='disp_yn' value='1' id='disp_y'  ".CompareReturnValue("1",$disp_yn,"checked")."><label for='disp_y'>표시</label><input type=radio name='disp_yn' value='0' id='disp_n' ".CompareReturnValue("0",$disp_yn,"checked")."><label for='disp_n'>표시안함</label>
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
					<tr>
					  <td class='search_box_title'><label for='regdate'>시작일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);'></td>
					  <td class='search_box_item'>
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
						<tr>
							<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
							<TD style='padding: 0 5px;'  align=center> ~ </TD>
							<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
							<TD style='padding-left:10px; vertical-align:middle;'>
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
					<tr>
					  <td class='search_box_title'><label for='visitdate'>종료일자</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);'></td>
					  <td class='search_box_item'>
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
						<tr>
							<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
							<TD style='padding: 0 5px;'  align=center> ~ </TD>
							<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>
							<TD style='padding-left:10px; vertical-align:middle;'>
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
		<tr height=60>
			<td style='padding:0 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
		</tr>
		</form>
		<tr>
			<td>
			".PrintEventList()."
			</td>
		</tr>
		";
$mstring .="</table>";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>스페셜쿠폰 추가</b>를 원하시면 스페셜쿠폰 추가버튼을 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간이 만료된 쿠폰은 자동으로 노출이 종료됩니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


//$help_text = HelpBox("이벤트/기획전 관리", $help_text);
$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>스페셜쿠폰 관리</b></td></tr></table>", $help_text,120);

$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->Navigation = "소셜커머스 > 스페셜쿠폰 > 스페셜쿠폰 리스트";
$P->title = "스페셜쿠폰 리스트";
$P->strLeftMenu = sns_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintEventList(){
	global $db, $mdb, $page, $search_type,$search_text,$disp_yn;
	global $cid,$pid,$FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,$vFromYY,$vFromMM,$vFromDD,$vToYY,$vToDD,$vToMM;
	global $auth_delete_msg, $admininfo ;


	$where = " where coupon_ix <> '' ";

	if($disp_yn == "1"){
		$where .= " and disp =  '1' ";
	}else if($disp_yn == "0"){
		$where .= " and disp = '0' ";
	}

	if($cid != ""){
		$where .= " and cid='$cid' ";
	}

	if($pid != ""){
		$where .= " and pid='$pid' ";
	}

	$startDate = $FromYY.$FromMM.$FromDD;



	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  coupon_use_sdate between  $startDate and $endDate ";
	}

	$vstartDate = $vFromYY.$vFromMM.$vFromDD;


	$vendDate = $vToYY.$vToMM.$vToDD;

	if($vstartDate != "" && $vendDate != ""){
		$where .= " and  coupon_use_edate between  $vstartDate and $vendDate ";
	}
	$sql = "select * from ".TBL_SHOP_SP_COUPON." $where";
	$mdb->query($sql);
	$total = $mdb->total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=0 cellspacing=0 border=0 width=100% bgcolor=silver class='list_table_box'>";
	$mString .= "
	<col width='*'>
	<col width='25%'>
	<col width='7%'>
	<col width='10%'>
	<col width='7%'>
	<tr height='30' align=center>
		<td class=s_td>스페셜쿠폰 제목</td>
		<td class=m_td>사용기간</td>
		<td class=m_td>표시</td>
		<td class=m_td>등록일</td>
		<td class=e_td>관리</td>
		</tr>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=5 align=center>스페셜쿠폰 내역이 존재 하지 않습니다.</td></tr>";
		$mString .= "
		</table>
			<table cellpadding=0 cellspacing=0 border=0 width=100% style='padding-top:10px'>
				<tr bgcolor=#ffffff >
					<td align=right><a href='sp_coupon.write.php'><img src='../images/".$admininfo["language"]."/b_sp_couponadd.gif' border=0 ></a></td>
				</tr>
			<table>
		<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>";

	}else{

		$db->query("select * from ".TBL_SHOP_SP_COUPON." $where  order by  regdate desc limit $start , $max");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			//$no = $no + 1;

			$mString .= "<tr height=30>
			<td class='list_box_td list_bg_gray' style='text-align:left; padding-left:10px;'><a href='sp_coupon.write.php?coupon_ix=".$db->dt[coupon_ix]."'>- ".$db->dt[coupon_title]."</a></td>
			<td class='list_box_td'>".ChangeDate($db->dt[coupon_use_sdate])." ~ ".ChangeDate($db->dt[coupon_use_edate])."</td>
			<td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ? "표시":"표시하지 않음")."</td>
			<td class='list_box_td'>".str_replace("-",".",substr($db->dt[regdate],0,10))."</td>
			<td class='list_box_td list_bg_gray'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$mString .= "<a href=\"JavaScript:eventDelete('".$db->dt[coupon_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$mString .= "<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$mString .= "
			</td>
			</tr>

			";
		}
	$mString .= "</table>";
	$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%>";
		//$mString .= "<tr height=1 bgcolor=silver><td colspan=5></td></tr>";
		$mString .= "<tr bgcolor=#ffffff height=50>
					<td colspan=3 align=left>".page_bar($total, $page, $max,  "&max=$max&cid=$cid&pid=$pid&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD&disp_yn=$disp_yn","")."</td>
					<td colspan=2 align=right>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$mString .= "<a href='sp_coupon.write.php'><img src='../images/".$admininfo["language"]."/b_sp_couponadd.gif' border=0 ></a>";
	}
		$mString .= "</td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}


?>
