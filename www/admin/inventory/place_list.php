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

if($select_company_id){
	$where .= " and cd.company_id = '".$select_company_id."' ";
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

$sql = "SELECT 
			count(*) AS total
		FROM 
		inventory_place_info as pi
		left join ".TBL_COMMON_COMPANY_DETAIL." as cd on (pi.company_id = cd.company_id)
		left join ".TBL_COMMON_MEMBER_DETAIL." as cu on (pi.com_person = cu.code)
		$where  limit $start, $max ";

$db->query($sql);
$db->fetch();
$total = $db->dt[total];

$sql = "SELECT 
			pi.*,
			cd.com_name,
			AES_DECRYPT(UNHEX(cu.name),'".$db->ase_encrypt_key."') as name
		FROM 
		inventory_place_info as pi
		left join ".TBL_COMMON_COMPANY_DETAIL." as cd on (pi.company_id = cd.company_id)
		left join ".TBL_COMMON_MEMBER_DETAIL." as cu on (pi.com_person = cu.code)
		$where  limit $start, $max ";

$db->query($sql);

if(is_array($place_type)) {
	for($i=0;$i<count($place_type);$i++) {
		$add_page_query="&place_type[]=".$place_type[$i];
	}
}
$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&select_company_id=$select_company_id&search_type=$search_type&search_text=$search_text".$add_page_query,"");
$mstring = "<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
<table width='100%' cellpadding=0 cellspacing=0>
	<tr>
	    <td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("창고관리", "재고관리 > 창고관리")."</td>
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
							<td class='box_02' onclick=\"document.location.href='place_list.php'\">창고등록</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_01'  ".($list_type == "byapply" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='inventory_order.php'\">우선순위조정</td>
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
				<td class='input_box_title'>이동 보관창고/장소</td>
								<td class='input_box_item'>
									".SelectEstablishment($select_company_id,"select_company_id","select","false","onChange=\"loadPlace(this,'move_pi_ix')\" ")."
								</td>
					<td class='input_box_title'>  <b>창고구분</b>  </td>
					<td class='input_box_item'>
						".getInventoryPlaces($place_type,"checkbox")."
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
										<option value='place_name' ".CompareReturnValue("place_name",$search_type).">창고명</option>
										<option value='place_phone' ".CompareReturnValue("place_phone",$search_type).">전화번호</option>
										<option value='place_fax' ".CompareReturnValue("place_fax",$search_type).">FAX</option>
									</select>
								</td>
								<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox' value='".$search_text."' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
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
	<col width='5%'>
	<col width='8%'>
	<col width='8%'>
	<col width='14%'>
	<col width='14%'>
	<col width='8%'>
	<col width='8%'>
	<col width='10%'>
	<col width='8%'>
	<col width='8%'>
	<col width='50'>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>순번</td>
		<td class='m_td'>창고구분</td>
		<td class='m_td'>창고위치</td>
		<td class='m_td'>사업장명</td>
		<td class='m_td'>창고명</td>
		<td class='m_td'>담당자명</td>
		<td class='m_td'>전화번호</td>
		<td class='m_td'>FAX</td>
		<td class='m_td'>사용여부</td>
		<td class='m_td'>우선순위</td>
		<td class='e_td'>관리</td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		$mstring .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td '>".$inventory_places[$db->dt[place_type]]."</td>
					<td class='list_box_td list_bg_gray'>".($db->dt[customer_position] == "O" ? "해외":"국내")."</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[com_name]."</td>
					<td class='list_box_td point' style='padding-left:10px; text-align:left;'><a href='place.add.php?pi_ix=".$db->dt[pi_ix]."&act=update'>".$db->dt[place_name]."</a></td>
					
					<td class='list_box_td list_bg_gray'>".$db->dt[name]."</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[place_tel]."</td>
					<td class='list_box_td'>".$db->dt[place_fax]."</td>
					<td class='list_box_td list_bg_gray'>".($db->dt[disp] == "Y" ? "사용":"미사용")."</td>
					<td class='list_box_td list_bg_gray'>".($db->dt[exit_order])."</td>
					<td class='list_box_td ' nowrap>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$mstring .="<a href='place.add.php?pi_ix=".$db->dt[pi_ix]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$mstring .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$mstring .="<a href=\"javascript:DeleteSectionInfo('delete', '".$db->dt[pi_ix]."');\" ><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$mstring .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$mstring .="
					</td>
				</tr>";
	}
	//$mstring .=	"</table>";
	//$mstring .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$mstring .= "
				<tr height=50><td colspan=11 align=center style='padding-top:10px;'>등록된 창고가 없습니다.</td></tr>";
}
$mstring .= "</table>";
$mstring .= "<table width=100%>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$mstring .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'><a href='place.add.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td></tr>";
}else{
	$mstring .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'><a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td></tr>";
}
$mstring .="</table><br>";
$Contents = $mstring;

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >귀사에서 관리하는 창고를 등록 관리하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >창고 정보를 수정하시고자 할때는 수정 버튼 또는 창고명을 클릭하시면 수정하실수 있습니다</td></tr>
</table>
";


$help_text = HelpBox("창고관리", $help_text);
$Contents .= $help_text;

$Script = "<script language='javascript' src='company.add.js'></script>
<script language='javascript' >

 function DeleteSectionInfo(act, pi_ix){
 	if(confirm('해당창고 정보를 정말로 삭제하시겠습니까?')){
 		window.frames['iframe_act'].location.href='place.act.php?act=delete&pi_ix='+pi_ix;
 	}
}
</script>
";


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
$P->Navigation = "재고관리 > 거래업체관리 > 창고관리";
$P->title = "창고관리";
$P->strContents = $Contents;
$P->PrintLayOut();


?>