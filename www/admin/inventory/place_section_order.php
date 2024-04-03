<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include("inventory.lib.php");


$db = new Database;
$pdb = new Database;

if($max == ""){
	$max = 1000; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if($selected !=''){
	$where =" and ps.pi_ix=".$selected." ";
}

if($pi_ix){
	$where .= " and ps.pi_ix = '".$pi_ix."' ";
}

if($section_type !=""){
	$where .= " and ps.section_type = '".$section_type."' ";
}

if($search_type){
	if($search_type == "section_name"){
		$where .= " and ps.section_name like '%".$search_text."%'";
	}
}

if($orderby == "section_name"){
	$order_str = "order by ps.section_name ".$orderbytype.", exit_order, ps.regdate";
}else if($orderby == "exit_order"){
	$order_str = "order by exit_order ".$orderbytype.",  ps.section_name asc, ps.regdate";
}else{
	$order_str = "order by exit_order, ps.regdate, ps.section_name asc";
}

$sql = "SELECT count(pi.pi_ix) as cnt
FROM inventory_place_section ps,
inventory_place_info pi,
common_company_detail as ccd
where ps.pi_ix = pi.pi_ix
and pi.company_id  = ccd.company_id
$where  ";

$db->query($sql);
$db->fetch();
$total = $db->dt[cnt];
//echo $total;

$sql = "SELECT ps.*,pi.place_name, ccd.com_name
FROM inventory_place_section ps,
inventory_place_info pi,
common_company_detail as ccd
where ps.pi_ix = pi.pi_ix
and pi.company_id  = ccd.company_id
$where   
$order_str
limit $start, $max  ";

//echo nl2br($sql);

$db->query($sql);

if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&view_type=$view_type&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype&view_type=$view_type","");
	//echo $total.":::".$page."::::".$max."<br>";
}



$Contents .=	"<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
			
			<td valign=top style='padding:0px;padding-top:0px;' id='product_orderarea'>			
			";
$innerview = "			
			<table width='100%' cellpadding=0 cellspacing=0 border=0>			
			<col width=10%>
			<col width=60%>
			<col width=15%>
			<col width=15%>
			<tr>
			    <td align='left' colspan=4 > ".GetTitleNavigation("보관장소 우선순위", "상점관리 > 보관장소 우선순위")."</td>
			</tr>";

if($mmode == "pop"){
$innerview .= "	
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;'>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>".$pname." 창고 우선순위 </b>
				</td>
			</tr>";
}else{
$innerview .= "	
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "add" ) ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='place_section.php?info_type=add'>보관장소등록</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "order"  || $info_type == "" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

						$innerview .= "<a href='place_section_order.php?info_type=order'>보관장소 우선순위조정</a>";

						$innerview .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	  </tr>";
}



$innerview .= "</table>";

$innerview .= "<table width='100%' cellpadding=0 cellspacing=0>
	<tr >
		<td colspan=2><form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
				<col width='150' >
				<col width='*' >
				<col width='150' >
				<col width='*' >
				<tr>
				<td class='input_box_title'>창고</td>
					<td class='input_box_item'>
						".SelectEstablishment($et_company_id,"et_company_id",'select',"false","  onChange=\"loadPlace(this,'pi_ix')\" ")."
						".SelectInventoryInfo($et_company_id, $pi_ix,'pi_ix','select','false', "onChange=\"loadPlaceSection(this,'ps_ix')\"  ")."									
					</td>
					<td class='input_box_title'>  <b>보관장소 타입</b>  </td>
					<td class='input_box_item'>
					<input type='radio' name='section_type' id='section_type_' value='' ".($place_info[section_type] == "" ? "checked":"")." ><label for='section_type_'> 전체</label>
						<input type='radio' name='section_type' id='section_type_G' value='G' ".($place_info[section_type] == "G"? "checked":"")." ><label for='section_type_G'> 일반장소</label>
						<input type='radio' name='section_type' id='section_type_S' value='S' ".($place_info[section_type] == "S" ? "checked":"")." ><label for='section_type_S'> 입고 보관장소
						</label>
						<input type='radio' name='section_type' id='section_type_D' value='D' ".($place_info[section_type] == "D"  ? "checked":"")." ><label for='section_type_D'> 출고 보관장소</label>
					</td>
				</tr>";

				$innerview .=	"
				<tr>
					<td class='input_box_title'>  <b>검색어</b>  </td>
					<td class='input_box_item' valign='top' style='padding-right:5px;padding-top:7px;'>
						<table cellpadding=0 cellspacing=0>
							<tr>
								<td>
									<select name='search_type'  style=\"font-size:12px;height:22px;\">
										<option value=''>전체보기</option>
										<option value='section_name'>보관장소명</option>
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
$innerview .=	"
			</table>
		</td>
	</tr>
	<tr >
		<td colspan=8 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
</table>
</form>
		<table width='100%' cellpadding=0 cellspacing=0>
		<tr height=38>
			<td colspan=4>
			<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>";
		$innerview .= "
							<table id='tab_00' ".($selected == '' ? "class='on'":"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='place_section_order.php'>전체 창고</a></td>
								<th class='box_03'></th>
							</tr>
							</table>";

		$pdb->query("SELECT distinct(ps.pi_ix), pi.place_name FROM inventory_place_section ps, inventory_place_info pi where ps.pi_ix = pi.pi_ix  order by pi.place_name asc ");

		for($j=0;$j < $pdb->total;$j++){
			$pdb->fetch($j);

			$innerview .= "
							<table id='tab_0".$j."' ".($selected == $pdb->dt[pi_ix] ? "class='on'":"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='place_section_order.php?selected=".$pdb->dt[pi_ix]."'>".$pdb->dt[place_name]."</a></td>
								<th class='box_03'></th>
							</tr>
							</table>";
		}
			$innerview .= "
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>";
$innerview .= "</table>";
$innerview .= "
			<form name=vieworderform method=post action='./place_section.act.php' target='act'><!-- target='act'-->
			<input type=hidden name='act' value='".($pid != "" ? "product_exit_order_change":"exit_order_change")."'>
			<input type=hidden name='pid' value='".$pid."'>
			<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
				<col style='width:7%;'>
				<col style='width:10%;'>
				<col style='width:10%;'>
				<col style='width:*;'>
				<col style='width:10%;'>
				<col style='width:10%;'>
				<col style='width:10%;'>
				<col style='width:15%;'>
			  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
				<td class='s_td'> ".OrderByLink("우선순위", "exit_order", $ordertype)."</td>
				<td class='m_td'> 사업장명</td>
				<td class='m_td'> 창고명</td>
				<td class='m_td'> ".OrderByLink("보관장소명", "section_name", $ordertype)."</td>
				<td class='m_td'> 보관장소 타입</td>
				<td class='m_td'> 기본여부</td>
				<td class='m_td'> 사용여부</td>
				<td class='e_td'> 관리</td>
			  </tr>
			</table>
			
			<table cellpadding=2 cellspacing=0 width=100% onselectstart='return false;' ondragstart='return false;' id='product_order_table' class='list_table_box'><!--frame=hsides rules=rows-->
				<col style='width:7%;'>
				<col style='width:10%;'>
				<col style='width:10%;'>
				<col style='width:*;'>
				<col style='width:10%;'>
				<col style='width:10%;'>
				<col style='width:10%;'>
				<col style='width:15%;'>
			";
	
	
if($total){
	for($i=0;$i < $total;$i++){
	$db->fetch($i);
	$no = $total - ($page - 1) * $max - $i;
	$innerview .= "
		  <tr bgcolor=#ffffff height=30 align=center onclick=\"spoit(this)\" id='".$db->dt[ps_ix]."' style='cursor:pointer;'>
		    <td class='list_box_td list_bg_gray'>".$db->dt[exit_order]."</td>
		    <td class='list_box_td'>".$db->dt[com_name]."</td>
			<td class='list_box_td'>".$db->dt[place_name]."</td>
			<td class='list_box_td point'>".$db->dt[section_name]."</td>
			<td class='list_box_td'>".$SECTION_TYPE[$db->dt[section_type]]."</td>
			<td class='list_box_td '>".($db->dt[is_basic] == "1" ?  "기본장소":"사용자추가")."</td>
			<td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>			
		    <td class='list_box_td list_bg_gray'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$innerview .= "<a href='./section.add.php?ps_ix=".$db->dt[ps_ix]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$innerview .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}
		    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
		    	$innerview .= "<a href=\"javascript:DeleteSectionInfo('delete','".$db->dt[ps_ix]."')\"><img src='../image/btc_del.gif' border=0></a>";
			}else{
				$innerview .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0></a>";
			}
	$innerview .= "
			<input type=hidden name=sno[] value='".$db->dt[ps_ix]."'>
			<input type=hidden name=sort[".$i."] value='".$db->dt[exit_order]."'> 
		    </td>
		  </tr>";
	}
}else{
	$innerview .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=8>등록된 보관장소가 없습니다. </td>
		  </tr>";
}

$innerview .= "

	  </table>";


/*
if($db->total == 0){
	$innerview = $innerview."<tr bgcolor=#ffffff height=50><td colspan=8 align=center> 등록된 보관장소가 없습니다.</td></tr>";
}else{			
	$total = $db->total;
	for ($i = 0; $i < $db->total; $i++)
	{

		$db->fetch($i);
	
	$innerview .= "<tr height=30 class='dot_xx'  onclick=\"spoit(this)\" id='".$db->dt[pi_ix]."' style='cursor:pointer;'>					
					<td class='list_box_td ' style='text-align:center;'><b>".$db->dt[exit_order]."</b></td>
					<td class='list_box_td list_bg_gray'>".$inventory_places[$db->dt[place_type]]."</td>
					<td class='list_box_td' style='padding-left:10px'><b>".$db->dt[place_name]."</b></td>
					<td class='list_box_td list_bg_gray' align=center>".$db->dt[place_tel]."</td>
					<td class='list_box_td'>".$db->dt[place_fax]."</td>
					<td class='list_box_td list_bg_gray' align=center>".($db->dt[return_position] == "Y" ? "지정":"미지정")."</td>
					<td class='list_box_td'>
					".$db->dt[regdate]."
					<input type=hidden name=sno[] value='".$db->dt[pi_ix]."'>
					<input type=hidden name=sort[] value='".$db->dt[minusorder]."'> 
					</td>
					
				</tr>
				";
	
	}
}	
*/
	$innerview .= "	
				
				
				<table width='100%'>
					<tr height=50 bgcolor=#ffffff><td colspan=8 align=center><input type=checkbox name='change_all' id='change_all' value='1'><label for='change_all'>노출순서 재조정</label> <input type=image src='../image/b_save.gif' border=0 align=absmiddle></td></tr>
					
				</table></form>
				
				";
	
$Contents = $Contents.$innerview ."			
			
			</td>
			</tr>
		</table>
			";

$help_text = "
	<table>
		<tr>
			<td style='line-height:150%' class=small>
			<img src='../image/icon_list.gif' align=absmiddle>보관장소 우선순위는 재고가 소진될때 어느보관장소의 재고를 먼저 소진할지 지정해주는 메뉴입니다.<br>
			<img src='../image/icon_list.gif' align=absmiddle>우선순위를 변경하시고자 할때 <b>↑ ↓ 방향키</b>를 눌러서 이동하신후 <b>저장</b>버튼을 누르시면 저장됩니다<br>
			</td>
		</tr>
	</table>
	";
$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >보관장소 우선순위는 재고가 소진될때 어느보관장소의 재고를 먼저 소진할지 지정해주는 메뉴입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >우선순위를 변경하시고자 할때 <b>↑ ↓ 방향키</b>를 눌러서 이동하신후 <b>저장</b>버튼을 누르시면 저장됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >상품별로 지정하기 위해선 상품리스트에서 '보관장소우선순위' 버튼을 클릭하시고 설정하면 됩니다.</td></tr>
</table>
";

$Contents .= HelpBox("창고 우선순위", $help_text, 171);




if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='inventory_order.js'></script>";
	$P->Navigation = "재고관리 > 창고 우선순위";	
	$P->NaviTitle = "창고 우선순위";
	$P->strLeftMenu = inventory_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='inventory_order.js'></script>";
	$P->Navigation = "재고관리 > 창고 우선순위";
	$P->title = "창고 우선순위";
	$P->strContents = $Contents;
	$P->PrintLayOut();
}





?>