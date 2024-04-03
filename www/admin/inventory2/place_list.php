<?
include("../class/layout.class");
include("inventory.lib.php");

if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;


$where = " where 1=1  ";
if($return_position != ""){
	$where .= " and return_position = '".$return_position."'  ";
}

if($sdate && $edate){
	$where .= " and DATE_FORMAT(regdate,'%Y%m%d') between ".$sdate." and ".$edate." ";
}

if($search_text && $search_type){
	$where .= " and $search_type LIKE '%".$search_text."%' ";
}

if(is_array($place_type)){
	for($i=0;$i < count($place_type);$i++){
		if($place_type[$i]){
			if($place_type_str == ""){
				$place_type_str .= "'".$place_type[$i]."'";
			}else{
				$place_type_str .= ", '".$place_type[$i]."' ";
			}
		}
	}

	if($place_type_str != ""){
		$where .= "and place_type in ($place_type_str) ";
	}
}else{
	if($place_type){
		$where .= "and place_type = '$place_type' ";
	}
}


$db->query("SELECT count(*) as total FROM inventory_place_info $where ");
$db->fetch();
$total = $db->dt[total];

$sql = "SELECT * FROM inventory_place_info $where  limit $start , $max ";
//echo $sql;
$db->query($sql);


$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max","");
$mstring = "<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
<table width='100%' cellpadding=0 cellspacing=0>
	<tr>
	    <td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("보관장소관리", "재고관리 > 보관장소관리")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=8 style='padding-bottom:14px;'>
	    <div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>
						<table id='tab_00'  ".($list_type == "" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='place_list.php'\">보관장소 등록</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_01'  ".($list_type == "byapply" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='inventory_order.php'\">보관장소 우선순위조정</td>
							<th class='box_03'></th>
						</tr>
						</table>
						

					</td>
					<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>";
					
$mstring .= "
					</td>
				</tr>
				</table>
			</div>
	    </td>
	</tr>
	<tr >
		<td colspan=2>
					
			<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
				<col width='150' >
				<col width='*' >
				<col width='150' >
				<col width='*' >
				<tr>
					<td class='input_box_title'>  <b>보관장소 유형</b>  </td>
					<td class='input_box_item' >
						<input type='checkbox' name='place_type[]' id='place_type_1' value='1' ".CompareReturnValue("1",$place_type,' checked')."  ><label for='place_type_1'>창고</label>
						<input type='checkbox' name='place_type[]' id='place_type_2' value='2' ".CompareReturnValue("2",$place_type,' checked')." ><label for='place_type_2'>선반</label>
						<input type='checkbox' name='place_type[]' id='place_type_2' value='3' ".CompareReturnValue("3",$place_type,' checked')." ><label for='place_type_3'>공장</label>
						<input type='checkbox' name='place_type[]' id='place_type_3' value='4' ".CompareReturnValue("4",$place_type,' checked')."><label for='place_type_4'>외주공장</label>
						<input type='checkbox' name='place_type[]' id='place_type_9' value='9' ".CompareReturnValue("9",$place_type,' checked')." ><label for='place_type_9'>기타</label>
					</td>
					<td class='input_box_title'> <b>반품지정창고</b>   </td>
						<td class='input_box_item'>
						<input type='radio' name='return_position' id='return_position_' value='' ".($return_position == ""  ? "checked":"")." ><label for='return_position_'>전체</label>
						<input type='radio' name='return_position' id='return_position_y' value='Y' ".($return_position == "Y"  ? "checked":"")." ><label for='return_position_y'>사용</label>
						<input type='radio' name='return_position' id='return_position_n' value='N' ".($return_position == "N"  ? "checked":"")." ><label for='return_position_n'>사용안함</label>
						</td>
				</tr>";
				
				$mstring .=	"
				<tr>
					<td class='input_box_title'>  <b>검색어</b>  </td>
					<td class='input_box_item' valign='top' style='padding-right:5px;padding-top:7px;'>
						<table cellpadding=0 cellspacing=0>
							<tr>
								<td>
									<select name='search_type'  style=\"font-size:12px;height:22px;\">
										<option value=''>전체보기</option>
										<option value='place_name'>보관장소명</option>
										<option value='place_phone'>전화번호</option>
										<option value='place_fax'>FAX</option>
									</select>
								</td>
								<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox' value='' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
								<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
									<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
										<tr height=20>
											<td width=100%  style='padding:0 0 0 5'>
												<table width=100% cellpadding=0 cellspacing=0 border=0>
													<tr>
														<td class='p11 ls1'>검색어 자동완성</td>
														<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:hand;padding:0 10 0 0' align=right>닫기</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr height=100% >
											<td valign=top bgColor=#efefef style='padding:0 6 5 6' colspan=2>
												<table width=100% height=100% bgcolor=#ffffff>
													<tr>
														<td valign=top >
														<div style='POSITION: absolute; overflow-y:auto;HEIGHT: 120px;' id='search_data_area'>
															<TABLE id=search_table style='table-layout:fixed;'  width=100% cellSpacing=0 cellPadding=1 bgColor=#ffffff border=0>
															<TBODY id=search_table_body></TBODY>
															</TABLE>
														<div>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									</DIV>
								</td>
								<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
							</tr>
						</table>
					</td>
					<td class='input_box_title'><b>목록갯수</b></td>
					<td class='input_box_item'>
						<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
							<option value='5' ".CompareReturnValue(5,$max).">5</option>
							<option value='10' ".CompareReturnValue(10,$max).">10</option>
							<option value='20' ".CompareReturnValue(20,$max).">20</option>
							<option value='50' ".CompareReturnValue(50,$max).">50</option>
							<option value='100' ".CompareReturnValue(100,$max).">100</option>
						</select> <span class='small'>한페이지에 보여질 갯수를 선택해주세요</span>
					</td>
				</tr>";
if(false){
$mstring .=	"
				<tr height=27>
					  <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>등록일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);'".CompareReturnValue("1",$regdate,"checked")."></td>
					  <td class='search_box_item' align=left style='padding-left:5px;' colspan=3>
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
							<col width=70>
							<col width=20>
							<col width=70>
							<col width=*>
							<tr>
								<TD nowrap>
								<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
								<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
								<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
								<SELECT name=FromDD></SELECT> 일 -->
								</TD>
								<TD align=center> ~ </TD>
								<TD nowrap>
								<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
								<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
								<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
								<SELECT name=ToDD></SELECT> 일 -->
								</TD>
								<TD style='padding:0px 10px'>
									<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
									<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
									<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
									<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
									<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
									<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
									<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
								</TD>
							</tr>
						</table>
					  </td>
					</tr>";
}
$mstring .=	"
			</table>
					
		</td>
	</tr>
	<tr >
		<td colspan=8 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>				
	</tr>
</table>
</form>
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
	<col width=5%>
	<col width='15%'>
	<col width='*'>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>보관장타입</td>
		<td class='m_td'>보관장소명</td>
		<td class='m_td'>전화번호</td>
		<td class='m_td'>FAX</td>
		<td class='m_td'>사용여부</td>
		<td class='m_td'>반품창고</td>
		<td class='m_td'>입출고 우선순위</td>
		<td class='e_td'>관리</td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		$mstring .="<tr height=32 align=center>
					<td class='list_box_td'>".($i+1)."</td>
					<td class='list_box_td list_bg_gray'>".$inventory_places[$db->dt[place_type]]."</td>
					<td class='list_box_td point' style='padding-left:10px; text-align:left;'><a href='place.add.php?pi_ix=".$db->dt[pi_ix]."&act=update'>".$db->dt[place_name]."</a></td>
					<td class='list_box_td list_bg_gray'>".$db->dt[place_tel]."</td>
					<td class='list_box_td'>".$db->dt[place_fax]."</td>
					<td class='list_box_td list_bg_gray'>".($db->dt[disp] == "Y" ? "사용함":"사용안함")."</td>
					<td class='list_box_td '>".($db->dt[return_position] == "Y" ? "지정":"미지정")."</td>
					<td class='list_box_td list_bg_gray'>".($db->dt[exit_order])."</td>
					<td class='list_box_td ' nowrap>";
			$mstring .="
					<a href='place.add.php?pi_ix=".$db->dt[pi_ix]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
					<a href='place.act.php?pi_ix=".$db->dt[pi_ix]."&act=delete'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
					</td>
				</tr>";
	}
	$mstring .=	"</table>";
	$mstring .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$mstring .= "
				<tr height=50><td colspan=5 align=center style='padding-top:10px;'>등록된 보관장소가 없습니다.</td></tr>
				<tr hegiht=1><td colspan=5  class='td_underline'></td></tr>";
}

	$mstring .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'><a href='place.add.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td></tr>";

$mstring .="</table><br>";
$Contents = $mstring;

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >귀사에서 관리하는 보관장소를 등록 관리하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >보관장소 정보를 수정하시고자 할때는 수정 버튼 또는 보관장소명을 클릭하시면 수정하실수 있습니다</td></tr>
</table>
";


$help_text = HelpBox("보관장소관리", $help_text);
$Contents .= $help_text;

$Script = "<script language='javascript' src='company.add.js'></script>";
/*
$P = new AdminPage();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
echo $P->AdminFrame();
*/
$P = new LayOut;
$P->addScript = "$Script";
$P->strLeftMenu = inventory_menu();
$P->Navigation = "재고관리 > 거래업체관리 > 보관장소관리";
$P->title = "보관장소관리";
$P->strContents = $Contents;
$P->PrintLayOut();


?>