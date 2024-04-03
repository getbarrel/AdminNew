<?
include("../class/layout.class");

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
function popupDelete(popup_ix){
	if(confirm(language_data['popup.list.php']['A'][language]))
	{//'해당 팝업를 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'
		//document.frames('act').location.href= 'popup.act.php?act=delete&popup_ix='+popup_ix;
		location.href= '../display/popup.act.php?act=delete&popup_ix='+popup_ix;
	}


}
function ChangeUseSdate(frm){
	if(frm.is_use_sdate.checked){
		$('#use_sdate_start').prop('disabled',false);
		$('#use_sdate_end').prop('disabled',false);	
	}else{
		$('#use_sdate_start').val('').prop('disabled',true);
		$('#use_sdate_end').val('').prop('disabled',true);
	}
}

function ChangeUseEdate(frm){
	if(frm.is_use_edate.checked){
		$('#use_edate_start').prop('disabled',false);
		$('#use_edate_end').prop('disabled',false);
	}else{
		$('#use_edate_start').val('').prop('disabled',true);
		$('#use_edate_end').val('').prop('disabled',true);
	}
}

function init(){

	var frm = document.searchmember; ";

if($is_use_sdate != "1"){
$Script .= "
	$('#use_sdate_start').val('').attr('disabled','disabled');
	$('#use_sdate_end').val('').attr('disabled','disabled');
	";
}
if($is_use_edate != "1"){
$Script .= "
	$('#use_edate_start').val('').attr('disabled','disabled');
	$('#use_edate_end').val('').attr('disabled','disabled');	";
}
$Script .= "
}
 
</script>";

if($disp_yn=="") {
	$disp_yn="all";
}

$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center >
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("팝업관리", "전시관리 > 팝업관리  ")."</td>
		</tr>
		<tr>
			<td>";
		$mstring .= "
		<form name=searchmember method='get' ><!--SubmitX(this);'-->
		<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:0px'>
				
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>				
					<col width='15%'>
					<col width='*'>";
					if($_SESSION["admin_config"][front_multiview] == "Y"){
					$mstring .= "
					<tr>
						<td class='search_box_title' > 프론트 전시 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
					}
					$mstring .= "
					<tr >
					  <td class='search_box_title'>조건검색 </td>
					  <td class='search_box_item'>
						  <select name=search_type>
								<option value='popup_title' ".CompareReturnValue("name",$search_type,"selected").">팝업제목</option>
						  </select>
						  <input type=text name='search_text' class='textbox' value='".$search_text."' style='width:50%; vertical-align:top;' >
					  </td>
					</tr>
					<tr>
					  <td class='search_box_title'>표시여부 </td>
					  <td class='search_box_item'>
						  <input type=radio name='disp_yn' value='all' id='disp_a'  ".CompareReturnValue("all",$disp_yn,"checked")."><label for='disp_a'>전체</label>
						  <input type=radio name='disp_yn' value='1' id='disp_y'  ".CompareReturnValue("1",$disp_yn,"checked")."><label for='disp_y'>표시</label><input type=radio name='disp_yn' value='0' id='disp_n' ".CompareReturnValue("0",$disp_yn,"checked")."><label for='disp_n'>표시안함</label>
					  </td>
					</tr>
					";
 
		 $mstring .= "
					<tr >
					  <td class='search_box_title'><label for='is_use_sdate'>시작일자</label><input type='checkbox' name='is_use_sdate' id='is_use_sdate' value='1' onclick='ChangeUseSdate(document.searchmember);' ".CompareReturnValue("1",$is_use_sdate,"checked")."></td>
					  <td class='search_box_item'>
					  ".search_date('use_sdate_start','use_sdate_end',$use_sdate_start,$use_sdate_end,'N','D')."	";
 
		$mstring .= "
					  </td>
					</tr>
					<tr height=27>
					  <td class='search_box_title'><label for='is_use_edate'>종료일자</label><input type='checkbox' name='is_use_edate' id='is_use_edate' value='1' onclick='ChangeUseEdate(document.searchmember);' ".CompareReturnValue("1",$is_use_edate,"checked")."></td>
					  <td class='search_box_item'>
					   ".search_date('use_edate_start','use_edate_end',$use_edate_start,$use_edate_end,'N','D')."";
/*
					   $mstring .= "
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
					</table>";
*/
					$mstring .= "
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
			<tr height=60>
				<td style='padding:10px 0px;' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
			</tr>
		</table>
		</form>";
		$mstring .= "
			</td>
		</tr>
		<tr>
			<td>
			".PrintPopupList()."
			</td>
		</tr>";




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
";
//	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


//$help_text = HelpBox("팝업 관리", $help_text);
$help_text = HelpBox("팝업 관리", $help_text,100);
$mstring .="<tr>
			<td>
			".$help_text."
			</td>
		</tr>";
$mstring .="</table>";
$Contents = $mstring;

if($agent_type == "M"){
	$P = new LayOut();
	$P->addScript = "\n".$Script;
	$P->OnloadFunction = "init();";
	$P->Navigation = "모바일샵 > 모바일 팝업관리";
	$P->title = "모바일 팝업관리";
	$P->strLeftMenu = mshop_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else if($popup_position == "A"){
	$P = new LayOut();
	$P->addScript = "\n".$Script;
	$P->OnloadFunction = "init();";
	$P->Navigation = "셀러공지사항 > 관리자 팝업관리";
	$P->title = "관리자 팝업관리";
	$P->strLeftMenu = seller_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = "\n".$Script;
	$P->OnloadFunction = "init();";
	$P->Navigation = "프로모션/전시 > 팝업관리";
	$P->title = "팝업관리";
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
function PrintPopupList(){
	global $db, $mdb, $page, $search_type,$search_text,$disp_yn;
	global $use_edate_start,$use_edate_end,$use_sdate_start,$use_sdate_end;
	global $auth_delete_msg, $admininfo;
	global $popup_position , $agent_type,$mall_ix;

	$max = 20;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$where = " where popup_ix <> '0' ";

	if($disp_yn == "1"){
		$where .= " and disp =  '1' ";
	}else if($disp_yn == "0"){
		$where .= " and disp = '0' ";
	}
	if($agent_type){
		$where .= " and popup_position = 'M' ";
	}else if($popup_position){
		$where .= " and popup_position = '".$popup_position."' ";
	}else{
		$where .= " and popup_position = 'F' ";
	}

	if($search_type != "" && $search_text != ""){
		$where .= " and $search_type LIKE  '%$search_text%' ";
	}

	if($mall_ix){
		$where .= " and mall_ix = '".$mall_ix."' ";
	}

	//$startDate = $FromYY.$FromMM.$FromDD;
	//$endDate = $ToYY.$ToMM.$ToDD;


	if($use_sdate_start != "" && $use_sdate_end != ""){
		$where .= " and  popup_use_sdate between  '".$use_sdate_start." 00:00:00' and '".$use_sdate_end." 23:59:59' ";
		//$where .= " and  date_format(popup_use_sdate, '%Y-%m-%d') between  '$use_sdate_start' and '$use_sdate_end' ";
	}

	//$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	//$vendDate = $vToYY.$vToMM.$vToDD;


	if($use_edate_start != "" && $use_edate_end != ""){
		$where .= " and  popup_use_edate between  '".$use_edate_start." 00:00:00' and '".$use_edate_end." 23:59:59' ";
		//$where .= " and  date_format(popup_use_edate, '%Y-%m-%d') between  '$use_edate_start' and '$use_edate_end' ";
	}
	$sql = "select * from ".TBL_SHOP_POPUP." $where ";
	//echo nl2br($sql);
	$mdb->query($sql);
	$total = $mdb->total;

	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box'>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=30>
					<td class=s_td width=5%>번호</td>
					".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td' width=10%> 프론트전시</td>":"")."
					<td class=s_td width='30%'>팝업 제목</td>
					<td class=m_td width='25%'>사용기간</td>
					<td class=m_td width='7%'>표시</td>
					<td class=m_td width='10%'>등록일</td>
					<td class=e_td width='28%'>관리</td>
					</tr>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=7 align=center>팝업 내역이 존재 하지 않습니다.</td></tr>
							</table>";
		$mString .= "<table width=100%><tr bgcolor=#ffffff ><td colspan=5 style='padding:5px 0px;' align=right>
			<a href=\"javascript:PoPWindow3('../display/popup.write.php?mmode=pop&agent_type=".$agent_type."&popup_position=".$popup_position."',1200,800,'popup_write')\">
		<img src='../images/".$admininfo["language"]."/b_popupadd.gif' border=0 ></a></td></tr></table>";
		$mString .= " ";
	}else{

		$db->query("select * from ".TBL_SHOP_POPUP."  $where order by  regdate desc limit $start, $max");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$no = $total - ($page - 1) * $max - $i;
			//$no = $no + 1;

			$mString = $mString."<tr height=30 >
			<td class='list_box_td '>".$no."</td>";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$mString .= "
		    <td class='list_box_td list_bg_gray'>".GetDisplayDivision($db->dt[mall_ix], "text")."</td>";
}
	$mString .= "
			<td class='list_box_td point' style='text-align:left;padding-left:15px;'><a href=\"javascript:PoPWindow3('../display/popup.write.php?mmode=pop&popup_ix=".$db->dt[popup_ix]."&agent_type=".$agent_type."&popup_position=".$popup_position."',1200,800,'popup_write')\">- ".$db->dt[popup_title]."</a></td>
			<td class='list_box_td'>".$db->dt[popup_use_sdate]." ~ ".$db->dt[popup_use_edate]."</td>
			<td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ? "표시":"표시안함")."</td>
			<td class='list_box_td'>".str_replace("-",".",substr($db->dt[regdate],0,10))."</td>
			<td class='list_box_td list_bg_gray' nowrap>";

	$mString .= "<a href=\"JavaScript:PoPWindow('../display/pop.php?no=".$db->dt[popup_ix]."','".$db->dt[popup_width]."','".$db->dt[popup_height]."','pop_".$db->dt[popup_ix]."')\"><img  src='../images/".$admininfo["language"]."/btn_popup_view.gif' border=0></a> ";
	
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		//$mString .= "<a href='../display/popup.write.php?mode=copy&popup_ix=".$db->dt[popup_ix]."'><img src='../images/".$admininfo["language"]."/btn_copy.gif' border=0 alt='복사'></a> ";
		//$mString .= "<a href='../display/popup.write.php?popup_ix=".$db->dt[popup_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0   alt='수정' title='수정'></a> ";

		$mString .= "<a href=\"javascript:PoPWindow3('../display/popup.write.php?mode=copy&mmode=pop&popup_ix=".$db->dt[popup_ix]."&agent_type=".$agent_type."&popup_position=".$popup_position."',1200,800,'popup_write')\"><img src='../images/".$admininfo["language"]."/btn_copy.gif' border=0 ></a> ";
		$mString .= "<a href=\"javascript:PoPWindow3('../display/popup.write.php?mmode=pop&popup_ix=".$db->dt[popup_ix]."&agent_type=".$agent_type."&popup_position=".$popup_position."',1200,800,'popup_write')\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 ></a> ";
	}else{
	$mString .= "<a href=\"".$auth_update_msg."\"><img  src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>";
	}
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
	$mString .= "<a href=\"JavaScript:popupDelete('".$db->dt[popup_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
	}else{
	$mString .= "<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
	}
	$mString .= "
			</td>
			</tr>
			";
		}
		$mString .= "</table>
					<table cellpadding=0 cellspacing=0 border=0 width=100% >
					<tr height=50 bgcolor=#ffffff>
					<td colspan=3 align=left>".page_bar($total, $page, $max,  "&max=$max&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD&disp_yn=$disp_yn","")."</td>
					<td colspan=2 style='padding:5px 0px;' align=right>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		//$mString .= "<a href='../display/popup.write.php?popup_position=".$popup_position."'><img src='../images/".$admininfo["language"]."/b_popupadd.gif' border=0 ></a>";
		$mString .= "<a href=\"javascript:PoPWindow3('../display/popup.write.php?mmode=pop&agent_type=".$agent_type."&popup_position=".$popup_position."',1200,800,'popup_write')\"><img src='../images/".$admininfo["language"]."/b_popupadd.gif' border=0 ></a> ";
		}
		$mString .= "</td>
				</tr>";
				$mString .= "</table>";
	}


	

	return $mString;
}


?>
