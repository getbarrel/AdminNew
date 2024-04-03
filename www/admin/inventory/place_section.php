<?
include_once("../class/layout.class");
include_once("inventory.lib.php");

$db = new Database;
$pdb = new Database;

if($max == ""){
	$max = 20; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "add"  || $info_type == "" ) ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='place_section.php?info_type=add'>보관장소등록</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "order" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

						$Contents01 .= "<a href='place_section_order.php?info_type=order'>우선순위조정</a>";

						$Contents01 .= "
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
	  </tr>
	</table>
";

$Contents01 .= "<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
<input type='hidden' name='mode' value='search' />
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<col width='15%' />
	<col width='30%' />
	<col width='*' />
  <tr >
	<td align='left' colspan=3> ".GetTitleNavigation("보관장소", "기초정보관리 > 보관장소 ")."</td>
  </tr>
  <tr>
	<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>보관장소 리스트</b></div>")."</td>
  </tr>
  </table>
<table width='100%' cellpadding=0 cellspacing=0>
";

$Contents01 .= "
	<tr >
		<td colspan=2>
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
					<input type='radio' name='section_type' id='section_type_' value='' ".($section_type == "" ? "checked":"")." ><label for='section_type_'> 전체</label>
						<input type='radio' name='section_type' id='section_type_G' value='G' ".($section_type == "G"? "checked":"")." ><label for='section_type_G'> 일반장소</label>
						<input type='radio' name='section_type' id='section_type_S' value='S' ".($section_type == "S" ? "checked":"")." ><label for='section_type_S'> 입고 보관장소
						</label>
						<input type='radio' name='section_type' id='section_type_D' value='D' ".($section_type == "D"  ? "checked":"")." ><label for='section_type_D'> 출고 보관장소</label>
					</td>
				</tr>";

				$Contents01 .=	"
				<tr>
					<td class='input_box_title'>  <b>검색어</b>  </td>
					<td class='input_box_item' valign='top' style='padding-right:5px;padding-top:7px;'>
						<table cellpadding=0 cellspacing=0>
							<tr>
								<td>
									<select name='search_type'  style=\"font-size:12px;height:22px;\">
										<option value=''>전체보기</option>
										<option value='section_name' ".CompareReturnValue("section_name",$search_type).">보관장소명</option>
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
$Contents01 .=	"
			</table>
		</td>
	</tr>
	<tr >
		<td colspan=8 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
</table>
</form>";

//$ContentsDesc01 =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

//$Contents02 =colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>보관장소목록</b></div>");
$Contents02 .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
		<col width='5%'>
		<col width='15%'>
		<col width='15%'>
		<col width='15'>
		<col width='15%'>
		<col width='10%'>
		<col width='10%'>
		<col width='10%'>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 순번</td>
		<td class='m_td'> 사업장명</td>
	    <td class='m_td'> 창고명</td>
		<td class='m_td'> 보관장소명</td>
		<td class='m_td'> 보관장소 타입</td>
	    <td class='m_td'> 기본여부</td>
		<td class='m_td'> 사용여부</td>
	    <td class='e_td'> 관리</td>
	  </tr>";

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

$sql = "SELECT count(pi.pi_ix) as cnt
FROM inventory_place_section ps,
inventory_place_info pi,
common_company_detail as ccd
where ps.pi_ix = pi.pi_ix
and pi.company_id  = ccd.company_id
$where order by ps.regdate, ps.section_name asc";

$db->query($sql);
$db->fetch();
$total = $db->dt[cnt];

$sql = "SELECT ps.*,pi.place_name, ccd.com_name
FROM inventory_place_section ps,
inventory_place_info pi,
common_company_detail as ccd
where ps.pi_ix = pi.pi_ix
and pi.company_id  = ccd.company_id
$where order by ps.regdate, ps.section_name  limit $start, $max  ";

$db->query($sql);

if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&view_type=$view_type&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&et_company_id=$et_company_id&pi_ix=$pi_ix&section_type=$section_type","");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype&view_type=$view_type","");
	//echo $total.":::".$page."::::".$max."<br>";
}


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$no = $total - ($page - 1) * $max - $i;
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".($no)."</td>
		    <td class='list_box_td'>".$db->dt[com_name]."</td>
			<td class='list_box_td'>".$db->dt[place_name]."</td>
			<td class='list_box_td point'>".$db->dt[section_name]."</td>
			<td class='list_box_td'>".$SECTION_TYPE[$db->dt[section_type]]."</td>
			<td class='list_box_td '>".($db->dt[is_basic] == "1" ?  "기본장소":"사용자추가")."</td>
			<td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>			
		    <td class='list_box_td list_bg_gray'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "<a href='./section.add.php?ps_ix=".$db->dt[ps_ix]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 title='수정하기'></a> ";
			}else{
				$Contents02 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 title='수정하기'></a> ";
			}
		    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
		    	$Contents02 .= "<a href=\"javascript:DeleteSectionInfo('delete','".$db->dt[ps_ix]."')\"><img src='../images/korea/btc_del.gif' border=0 title='삭제'></a>";
			}else{
				$Contents02 .= "<a href=\"".$auth_delete_msg."\"><img src='../images/korea/btc_del.gif' border=0 title='삭제'></a>";
			}
	$Contents02 .= "
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=8>등록된 보관장소가 없습니다. </td>
		  </tr>";
}

$Contents02 .= "

	  </table>";



$Contents03 = "
	<div class='tab'>
		<table class='s_org_tab'>
			<tr>
				<td class='tab'>";
$Contents03 .= "
					<table id='tab_00' ".($selected == '' ? "class='on'":"")." >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='place_section.php'>전체 창고</a></td>
						<th class='box_03'></th>
					</tr>
					</table>";

$pdb->query("SELECT distinct(ps.pi_ix), pi.place_name FROM inventory_place_section ps, inventory_place_info pi where ps.pi_ix = pi.pi_ix  order by pi.place_name asc ");

for($j=0;$j < $pdb->total;$j++){
	$pdb->fetch($j);

	$Contents03 .= "
					<table id='tab_0".$j."' ".($selected == $pdb->dt[pi_ix] ? "class='on'":"")." >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='place_section.php?selected=".$pdb->dt[pi_ix]."'>".$pdb->dt[place_name]."</a></td>
						<th class='box_03'></th>
					</tr>
					</table>";
}
	$Contents03 .= "
				</td>
			</tr>
		</table>
	</div>";

$mstring .= "<table width=100%>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$mstring .= "<tr hegiht=40 align='right'><td colspan=5>".$str_page_bar."</td></tr>
	<tr>
	<td colspan=1 align=right style='padding-top:10px;'><a href='section.add.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td></tr>";
}else{
	$mstring .= "<tr hegiht=40 align='right'><td colspan=5>".$str_page_bar."</td></tr>
	<tr><td colspan=1 align=right style='padding-top:10px;'><a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td></tr>";
}
$mstring .="</table><br>";
//$Contents = $mstring;


$Contents = $Contents."<table width='100%' border=0 cellpadding=0 cellspacing=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";

$Contents = $Contents."<form name='section_frm' action='place_section.act.php' method='post' onsubmit='return CheckFormValue(this)'  target='act' style='display:inline;'><!--enctype='multipart/form-data'-->
<input name='act' type='hidden' value=''>
<input name='ps_ix' type='hidden' value=''>";
$Contents = $Contents."<tr><td>".$Contents03."<br></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$mstring."<br></td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td   >창고 내에 보관장소를 구분하여 관리 하실 수 있습니다..</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' > </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' > </td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= HelpBox("보관장소", $help_text, 60);

 $Script = "<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
 <script language='javascript'>
 function updatesectionInfo(ps_ix,pi_ix,section_type,section_name,is_basic,disp){

 	var frm = document.section_frm;

 	frm.act.value = 'update';
 	frm.ps_ix.value = ps_ix;
	//alert(pi_ix);
	$('#pi_ix').val(pi_ix);
	$('input:radio[name=section_type]:input[value='+section_type+']').attr('checked',true);
	/*
	for(i=0;i < frm.pi_ix.length;i++){
 		if(frm.pi_ix[i].value == pi_ix){
 			frm.pi_ix[i].selected = true;
 		}
 	}
	*/

 	frm.section_name.value = section_name;

 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
}

 function DeleteSectionInfo(act, ps_ix){
 	if(confirm('해당보관장소 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.section_frm;
 		frm.act.value = act;
 		frm.ps_ix.value = ps_ix;
 		frm.submit();
 	}
}



function valueCheck(this_, obj_name){
	//alert(this_.id);
	//alert($(this_).val());
	
		$.post('place_section.act.php', {
		  value: $('#'+this_.id).val(),
		  act: 'value_check_jquery',
		  value_name:this_.id
		}, function(data){
			//alert(data);
			
			if(data == '300') {
				$('#'+this_.id+'_check_text').css('color','#00B050').html('사용 가능한 '+obj_name+' 입니다.');
				$('#'+this_.id+'_flag').val('1');
				$('#'+this_.id).attr('dup_check','true');
				//alert($('#'+this_.id).attr('dup_check'));
			} else if(data == '130') {
				$('#'+this_.id+'_check_text').css('color','#FF5A00').html(''+obj_name+'는 3자이상 입력해 주세요.');//16자 이하로 
				$('#'+this_.id+'_flag').val('0');
				$('#'+this_.id).attr('dup_check','false');
				return false;
			} else if(data == '120') {
				$('#'+this_.id+'_check_text').css('color','#FF5A00').html('이미 사용중인 '+obj_name+' 입니다.');
				$('#'+this_.id+'_flag').val('0');
				$('#'+this_.id).attr('dup_check','false');
				return false;
			} else if(data == '110') {
				$('#'+this_.id+'_check_text').css('color','#FF5A00').html('첫글자는 영문으로, 다음은 영문(소문자)과 숫자의 조합만 가능합니다.');
				$('#'+this_.id+'_flag').val('0');
				$('#'+this_.id).attr('dup_check','false');
				return false;
			} else {
				$('#'+this_.id+'_check_text').css('color','#FF5A00').html('이미 사용중인 '+obj_name+' 입니다.');
				//$('#'+this_.id+'_check_text').html(data);
				$('#'+this_.id+'_flag').val('0');
				$('#'+this_.id).attr('dup_check','false');
				return false;
			}
			
		});
}
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = inventory_menu();
$P->Navigation = "재고관리 > 기초정보관리 > 보관장소";
$P->title = "보관장소";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*
CREATE TABLE  `dev`.`inventory_place_section` (
`ps_ix` INT( 8 ) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  '인덱스',
`pi_ix` INT( 6 ) NOT NULL COMMENT  '창고인덱스',
`section_name` VARCHAR( 200 ) NOT NULL COMMENT  '보관장소명',
`disp` CHAR( 1 ) NOT NULL COMMENT  '사용여부',
`regdate` DATETIME NOT NULL COMMENT  '등록일',
INDEX (  `pi_ix` )
) ENGINE = MYISAM COMMENT =  '보관장소정보';
*/
?>