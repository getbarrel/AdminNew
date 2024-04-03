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
		window.frames['act'].location.href= 'popup.act.php?act=delete&popup_ix='+popup_ix;
	}


}

function ChangeUsedate(thisObj){
	if($(thisObj).is(':checked')){
		$('#use_date_start').attr('disabled',false);
		$('#use_date_end').attr('disabled',false);
	}else{
		$('#use_date_start').val('').attr('disabled',true);
		$('#use_date_end').val('').attr('disabled',true);
	}
}


function init(){

	var frm = document.searchmember; ";

if($is_use_date != "1"){
$Script .= "
	$('#use_date_start').val('').attr('disabled',true);
	$('#use_date_end').val('').attr('disabled',true);
	";
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
			<td align='left' colspan=6 > ".GetTitleNavigation("셀러 공문서 동의", "셀러설정 관리 > 셀러 공문서 동의")."</td>
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
					$mstring .= "
					<tr >
					  <td class='search_box_title'>조건검색 </td>
					  <td class='search_box_item'>
						  <select name=search_type>
								<option value='popup_title' ".CompareReturnValue("popup_title",$search_type,"selected").">제목</option>
						  </select>
						  <input type=text name='search_text' class='textbox' value='".$search_text."' style='width:50%; vertical-align:top;' >
					  </td>
					</tr>
					<tr>
					  <td class='search_box_title'>진행상태 </td>
					  <td class='search_box_item'>
						  <input type=radio name='popup_status' value='2' id='process_ing'".CompareReturnValue("2",$popup_status,"checked")."><label for='process_ing'>진행중</label>
						  <input type=radio name='popup_status' value='1' id='process_start'  ".CompareReturnValue("1",$popup_status,"checked")."><label for='process_start'>진행예정</label>
						  <input type=radio name='popup_status' value='0' id='process_end' ".CompareReturnValue("0",$popup_status,"checked")."><label for='process_end'>진행완료</label>
					  </td>
					</tr>
					";

 
		$mstring .= "
					<tr height=27>
					  <td class='search_box_title'>
						  <select name=search_type_date style='width: 100px;'>
								<option value='regdate' ".CompareReturnValue("regdate",$search_type_date,"selected").">등록일  </option>
								<option value='popup_use_sdate' ".CompareReturnValue("popup_use_sdate",$search_type_date,"selected").">시작일  </option>
								<option value='popup_use_edate' ".CompareReturnValue("popup_use_edate",$search_type_date,"selected").">종료일  </option>
						  </select>
						<input type='checkbox' name='is_use_date' value='1' onclick='ChangeUsedate(this);' ".CompareReturnValue('1',$is_use_date,' checked').">
					  </td>
					  <td class='search_box_item'>
					   ".search_date('use_date_start','use_date_end',$use_date_start,$use_date_end,'N','D')."";
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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ></td></tr>
</table>
";
//	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


//$help_text = HelpBox("팝업 관리", $help_text);
$help_text = HelpBox("셀러 공문/동의서", $help_text,100);
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
	$P->Navigation = "셀러설정 관리 > 공문/동의서";
	$P->title = "셀러 공문/동의서";
	$P->strLeftMenu = seller_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else if($popup_position == "A"){
	$P = new LayOut();
	$P->addScript = "\n".$Script;
	$P->OnloadFunction = "init();";
	$P->Navigation = "셀러설정 관리 > 공문/동의서";
	$P->title = "셀러 공문/동의서";
	$P->strLeftMenu = seller_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = "\n".$Script;
	$P->OnloadFunction = "init();";
	$P->Navigation = "셀러설정 관리 > 공문/동의서";
	$P->title = "셀러 공문/동의서";
	$P->strLeftMenu = seller_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
function PrintPopupList(){
	global $db, $mdb, $page, $search_type,$search_text,$disp_yn, $popup_status;
	global $use_date_end,$use_date_start, $search_type_date;
	global $auth_delete_msg, $admininfo;
	global $popup_position , $agent_type;

	$max = 20;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$where = " where popup_ix <> '0' ";

	if($popup_status != "" && $popup_status == "1"){

		$where .= " and popup_status =  '1' ";
	}else if($popup_status != "" && $popup_status == "0"){

		$where .= " and popup_status = '0' ";
	}else if($popup_status != "" && $popup_status == "2"){
		$where .= " and popup_status = '2' ";
	}


	if($search_type != "" && $search_text != ""){
		$where .= " and $search_type LIKE  '%$search_text%' ";
	}

	//$startDate = $FromYY.$FromMM.$FromDD;
	//$endDate = $ToYY.$ToMM.$ToDD;


	if($use_date_start != "" && $use_date_end != ""){
		//$where .= " and  $search_type_date between  '$use_date_start' and '$use_date_end' ";
		$where .= " and  $search_type_date between  '$use_date_start 00:00:00' and '$use_date_end 23:59:59' ";
	}

	//$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	//$vendDate = $vToYY.$vToMM.$vToDD;

	$sql = "select * from seller_official_popup $where ";
	$mdb->query($sql);

	$total = $mdb->total;

	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box'>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=30>
					<td class=s_td width=5%>번호</td>
					<td class=s_td width='20%'>제목</td>
					<td class=m_td width='10%'>구분</td>
					<td class=m_td width='7%'>진행 상태</td>
					<td class=m_td width='10%'>시작일</td>
					<td class=m_td width='10%'>종료일</td>
					<td class=m_td width='10%'>등록일</td>
					<td class=e_td width='20%'>".($admininfo["admin_level"]==9 ? "관리" : "확인여부")."</td>";
    $mString .= "
					</tr>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=8 align=center>팝업 내역이 존재 하지 않습니다.</td></tr>
							</table>";
		$mString .= "<table width=100%><tr bgcolor=#ffffff ><td colspan=5 style='padding:5px 0px;' align=right>
			<a href=\"javascript:PoPWindow3('./seller_official_document.write.php?mmode=pop&agent_type=".$agent_type."&popup_position=".$popup_position."',1200,800,'popup_write')\"><input type=\"button\" value=\"문서 등록\" onclick=\"if( $('#di_mp_tr_0').is(':visible') ){ $('#di_mp_tr_0').hide();}else{ $('#di_mp_tr_0').show();} \" style='width:100px; height:40px;'></a></td></tr></table>";
		$mString .= " ";
	}else{

		$db->query("select * from seller_official_popup $where order by regdate desc limit $start, $max");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$no = $total - ($page - 1) * $max - $i;
			//$no = $no + 1;

			$mString = $mString."<tr height=30 >
			<td class='list_box_td '>".$no."</td>";
			if($admininfo["admin_level"]==9) {
                $mString .= "
				<td class='list_box_td' style='text-align:left;padding-left:15px;'><a href=\"javascript:PoPWindow3('./seller_official_document.write.php?mode=copy&mmode=pop&popup_ix=" . $db->dt[popup_ix] . "&agent_type=" . $agent_type . "&popup_position=" . $popup_position . "',1200,800,'popup_write')\">" . $db->dt[popup_title] . "</a></td>";
            }else{
                $mString .= "
				<td class='list_box_td' style='text-align:left;padding-left:15px;'><a href=\"JavaScript:PoPWindow('./seller_official_document.pop.php?no=".$db->dt[popup_ix]."','".$db->dt[popup_width]."','".$db->dt[popup_height]."','pop_".$db->dt[popup_ix]."')\">" . $db->dt[popup_title] . "</a></td>";
			}
	$mString .= "
			<td class='list_box_td'>".($db->dt[popup_div] == "2" ? "공문서":"동의서")."</td>
			<td class='list_box_td'>";

		if($db->dt[popup_status] == '0'){
			$mString .=	"진행완료";
		} elseif($db->dt[popup_status] == '1'){
			$mString .=	"진행예정";
		} elseif($db->dt[popup_status] == '2'){
			$mString .=	"진행중";
		}

	$mString .=	"</td>
			<td class='list_box_td'>".substr($db->dt[popup_use_sdate], 0, 10)."</td>
			<td class='list_box_td' nowrap>".substr($db->dt[popup_use_edate], 0, 10)."
			</td>
			<td class='list_box_td'>".substr($db->dt[regdate], 0, 10)."</td>
			<td class='list_box_td'>";

        if($admininfo["admin_level"]==9 || true){
            $mString .= "<a href=\"JavaScript:PoPWindow('./seller_official_document.result.php?popup_ix=" . $db->dt[popup_ix] . "',1500,700,'popup_write')\"><img  src='../images/" . $admininfo["language"] . "/btn_result.gif' border=0></a> ";
            $mString .= "<a href=\"JavaScript:PoPWindow('./seller_official_document.pop.php?no=" . $db->dt[popup_ix] . "','" . $db->dt[popup_width] . "','" . $db->dt[popup_height] . "','pop_" . $db->dt[popup_ix] . "')\"><img  src='../images/" . $admininfo["language"] . "/btn_preview.gif' border=0></a> ";

            if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "U")) {
                //$mString .= "<a href='../display/popup.write.php?mode=copy&popup_ix=".$db->dt[popup_ix]."'><img src='../images/".$admininfo["language"]."/btn_copy.gif' border=0 alt='복사'></a> ";
                //$mString .= "<a href='../display/popup.write.php?popup_ix=".$db->dt[popup_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0   alt='수정' title='수정'></a> ";

                $mString .= "<a href=\"javascript:PoPWindow3('./seller_official_document.write.php?mmode=pop&popup_ix=" . $db->dt[popup_ix] . "&agent_type=" . $agent_type . "&popup_position=" . $popup_position . "',1200,800,'popup_write')\"><img src='../images/" . $admininfo["language"] . "/bts_modify.gif' border=0 ></a> ";
            } else {
                $mString .= "<a href=\"" . $auth_update_msg . "\"><img  src='../images/" . $admininfo["language"] . "/bts_modify.gif' border=0></a>";
            }
        }else{
            $sql = "select * from seller_official_popup_result opr where opr.charger_ix = '".$admininfo[charger_ix]."' and opr.popup_ix = '".$db->dt[popup_ix]."' ";
            $mdb->query($sql);
            $mdb->fetch();
            if($mdb->dt[popup_confirm] == '1'){
                $mString .= "동의 " .$mdb->dt[popup_confirm_date];
            } elseif($mdb->dt[popup_confirm] == '0'){
                $mString .= "미동의 " .$mdb->dt[popup_confirm_date];
            } elseif($mdb->dt[popup_confirm]!="1" || $mdb->dt[popup_confirm]!="0"){
                $mString .= "미확인";
            }
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
		$mString .= "<a href=\"javascript:PoPWindow3('./seller_official_document.write.php?mmode=pop&agent_type=".$agent_type."&popup_position=".$popup_position."',1200,800,'popup_write')\"><input type=\"button\" value=\"문서 등록\" onclick=\"if( $('#di_mp_tr_0').is(':visible') ){ $('#di_mp_tr_0').hide();}else{ $('#di_mp_tr_0').show();} \" style='width:100px; height:40px;'></a> ";
		}
		$mString .= "</td>
				</tr>";
				$mString .= "</table>";
	}


	

	return $mString;
}


?>
