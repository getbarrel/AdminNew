<?
include("../class/layout.class");


if(!$auth_templet_ix){
	echo "<script lanuage='javascript'>alert('권한템플릿을 선택하신후 상세권한 관리를 하실 수 있습니다.');document.location.href='auth_templet.php'</script>";
	exit;
}
	$db = new Database();
	$db2 = new Database();

	$max = 200; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

	$sql = "Select auth_templet_name from  admin_auth_templet where auth_templet_ix = '".$auth_templet_ix."'  ";
	$db->query($sql);
	$db->fetch();
	$auth_templet_name = $db->dt[auth_templet_name];

	$where = "where am.disp_auth = 'Y'  ";
	if($menu_div != ""){
		$where .= " and am.menu_div = '".$menu_div."' ";
	}

	if($_COOKIE["auth_view_type"] == ""){
		//$auth_view_type_where = " and (auth_read = 'Y' or auth_write_update = 'Y' or auth_delete = 'Y' or auth_delete = 'Y')  ";
	}
	//몰타입에 따른 권한템플릿 리스트 노출 관리 1305070  JK
	/*
	if($admininfo[charger_id] == "forbiz"){
		$type_where = " ";
	}else{
	*/
		if($admininfo[mall_type] == "H"){
			$type_where = " and am.use_home = 'Y' ";//홈페이지 빌더
		}else if($admininfo[mall_type] == "F"){
			$type_where = "  and am.use_soho = 'Y'"; //무료형에대한 메뉴구분 정보가 없슴 (소호형 정보로 대체 함)
		}else if($admininfo[mall_type] == "R"){
			$type_where = "  "; //임대형에대한 메뉴구분 정보가 없슴
		}else if($admininfo[mall_type] == "S"){
			$type_where = "  "; //독립형에대한 메뉴구분 정보가 없슴
		}else if($admininfo[mall_type] == "B"){
			$type_where = " and am.use_business = 'Y'"; //입점형에대한 메뉴구분 정보가 없슴 (비즈형 정보로 대체 함)
		}else if($admininfo[mall_type] == "BW"){
			$type_where = " and am.use_wholesale = 'Y'"; //비즈도매형에대한 메뉴구분 정보가 없슴 (도매형 정보로 대체 함)
		}else if($admininfo[mall_type] == "O"){
			$type_where = " and am.use_openmarket = 'Y'"; //오픈마켓
		}else if($admininfo[mall_type] == "E"){
			$type_where = " and am.use_enterprise = 'Y'"; //오픈마켓 형
		}else{
			$type_where = " "; //오픈마켓 형
		}
		//상점 타입 (H:홈페이지, F:무료형, R:임대형, S:독립형, B:입점형,BW:비즈도매형, O:오픈마켓형)
	//}
	$sql = "Select count(*) as total 
			from admin_menus am 
			left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code and auth_templet_ix = '$auth_templet_ix' 
			$where $auth_view_type_where $type_where";

	//echo nl2br($sql);
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];

	$sql = "Select am.*, aatd.auth_read, aatd.auth_write_update, aatd.auth_delete , aatd.auth_excel
			from admin_menus am 
			left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code and auth_templet_ix = '".$auth_templet_ix."'
			$where  $auth_view_type_where $type_where

			order by am.menu_div, am.view_order asc, am.regdate asc 
			limit $start, $max";
	//echo nl2br($sql);
	$db->query($sql);


	$Contents .= "<form name='page_frm' action='auth_templet_detail.act.php' method='POST' target='act'><input type=hidden name=act value='updates'><input name='auth_templet_ix' type='hidden' value='".$auth_templet_ix."'>
	<table cellpadding=0 cellspacing=0 width=100%  STYLE='TABLE-LAYOUT:fixed' >
	<col width='40'>
	<col width='*'>
	<col width='100'>
	<col width='110'>
	<col width='130'>
	<col width='110'>
	<col width='110'>
	<tr >
		<td align='left' colspan=7 style='padding-bottom:10px;'> ".GetTitleNavigation("권한 템플릿 상세권한관리", "상점관리 > 권한 템플릿 상세권한관리 ")."</td>
	</tr>
	<tr >
		<td align='left' colspan=7 style='padding-bottom:10px;'> <img src='../images/dot_org.gif' align=absmiddle> <b>".$auth_templet_name."</b> 에 해당하는 상세 권한 설정입니다.</td>
	</tr>
	 <tr>
	    <td align='left' colspan=7 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab' width=100%>
			<tr>
				<td class='tab' >
					<table id='tab_01' ".($menu_div == "" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?menu_div=&auth_templet_ix=".$auth_templet_ix."'>전체</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					".getMenuGroupTab($menu_div)."
				</td>
				<td style='width:160px;text-align:right;vertical-align:bottom;padding:5px 0 10px 0;".$display_yn."'>
					<input type=checkbox name='auth_view_type' id='auth_view_type' value='ALL' onclick='CheckAuthViewType();' ".($_COOKIE["auth_view_type"] == "ALL" ? "checked":"")."><label for='auth_view_type' style='font-weight:bold;'>권한없는 메뉴 함께보기</lebal>
				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100%  STYLE='TABLE-LAYOUT:fixed;min-width:850px;' class='list_table_box'>
	<col width='40'>
	<col width='*'>
	<col width='120'>
	<col width='90'>
	<col width='120'>
	<col width='130'>
	<col width='90'>
	<tr height=25 align=center>
		<td align=center class='s_td'><input type='checkbox' name='menu_code_all' id='menu_code_all'  onclick=\"CheckedAll('menu_code_all', 'menu_code')\"></td>
		<td align=center class='m_td' >메뉴위치</td>
		<td align=center class='m_td' >메뉴분류</td>
		<td align=center class='m_td' ><input type='checkbox' value='Y' name='auth_read_all' id='auth_read_all' onclick=\"CheckedAll('auth_read_all', 'auth_read')\"><label for='auth_read_all'>읽기 권한</label> </td>
		<td align=center class='m_td' ><input type='checkbox' value='Y' name='auth_write_update_all' id='auth_write_update_all' onclick=\"CheckedAll('auth_write_update_all', 'auth_write_update')\"><label for='auth_write_update_all'>쓰기/수정 권한</label> </td>
		<td align=center class='m_td small' nowrap>
			<input type='checkbox' value='Y' name='auth_excel_all' id='auth_excel_all' onclick=\"CheckedAll('auth_excel_all', 'auth_excel')\"><label for='auth_excel_all' >엑셀다운로드 권한</label> 
		</td>
		<td align=center class='e_td' ><input type='checkbox' value='Y' name='auth_read_all' id='auth_delete_all' onclick=\"CheckedAll('auth_delete_all', 'auth_delete')\"><label for='auth_delete_all'>삭제 권한</label></td>
	</tr>\n";
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		$menu_name = str_replace("\"","&quot;",$db->dt[menu_name]);
		$menu_name = str_replace("'","&#39;",$menu_name);
		//$page_ko_name = str_replace("'","\'",$page_ko_name);
		//$navis = explode(">",$db->dt[menu_name]);

		//$db2->query("update admin_menus set menu_name = '".(count($navis[1]) >= 1 ? trim($navis[1]):trim($navis[0]))."' where menu_code = '".$db->dt[menu_code]."' ");

		$Contents .= "<tr height=40 bgcolor=#ffffff onclick=\"spoit(this)\"  id='Report$i' ><!--onclick=\"$(this).find('input[type=checkbox]').attr('checked','checked');\"-->
		<td class='list_box_td list_bg_gray' >
		<input type='checkbox' value='".$db->dt[menu_code]."' name='set_menu_info[".$i."][menu_code]' id='menu_code_".$db->dt[menu_code]."' class='menu_code'>
		</td>
		<td class='list_box_td point'  title='".urldecode($db->dt[vurl])."' style='text-align:left;padding:4px 4px' wrap>
			".(!$menu_name ? "<span style='color:silver'>페이지 명이 입력되지 않았습니다</span>":$db->dt[menu_name]."<br><div class='small'><a href='".$db->dt[menu_link]."' target=_blank>".$db->dt[menu_link]."</a>")."</div>
			
		</td>
		<td class='list_box_td list_bg_gray' >
			".$db->dt[menu_div]."
		</td>
		<td class='list_box_td '>
		<input type='checkbox' value='Y' name='set_menu_info[".$i."][auth_read]' class='auth_read'  id='auth_read_".$db->dt[menu_code]."' auth='".$db->dt[auth_read]."' onclick=\"$('#menu_code_".$db->dt[menu_code]."').attr('checked','checked');\" ".($db->dt[auth_read] == "Y" ? "checked":"").">
		</td>
		<td class='list_box_td list_bg_gray'>
		<input type='checkbox' value='Y' name='set_menu_info[".$i."][auth_write_update]' class='auth_write_update' id='auth_write_update_".$db->dt[menu_code]."' auth='".$db->dt[auth_write_update]."'  onclick=\"$('#menu_code_".$db->dt[menu_code]."').attr('checked','checked');\"  ".($db->dt[auth_write_update] == "Y" ? "checked":"").">
		</td>
		
		<td class='list_box_td'>
		<input type='checkbox' value='Y' name='set_menu_info[".$i."][auth_excel]' class='auth_excel' id='auth_excel_".$db->dt[menu_code]."' auth='".$db->dt[auth_excel]."'  onclick=\"$('#menu_code_".$db->dt[menu_code]."').attr('checked','checked');\"  ".($db->dt[auth_excel] == "Y" ? "checked":"").">
		</td>
		<td class='list_box_td list_bg_gray'>
		<input type='checkbox' value='Y' name='set_menu_info[".$i."][auth_delete]' class='auth_delete' id='auth_delete_".$db->dt[menu_code]."' auth='".$db->dt[auth_delete]."'  onclick=\"$('#menu_code_".$db->dt[menu_code]."').attr('checked','checked');\"  ".($db->dt[auth_delete] == "Y" ? "checked":"").">
		</td>
		</tr>";

	//	$pageview01 = $pageview01 + returnZeroValue($db->dt[visit_cnt]);


	}
	//$Contents .= "<tr height=50><td colspan=2></td><td colspan=5 align='right' >&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td></tr>";

	$Contents .= "</table>\n";
	$Contents .= "<ul class='paging_area' >
						<li class='front'></li>
						<li class='back'>".page_bar($total, $page, $max,"&".$_SERVER["QUERY_STRING"],"")."</li>
					  </ul>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){

	if($admininfo[mall_type] == "F"){
		$sql = "SELECT * FROM admin_auth_templet where use_soho = 'Y' order by auth_templet_level asc ";
	}else if($admininfo[mall_type] == "B"){
		$sql = "SELECT * FROM admin_auth_templet where use_biz = 'Y' order by auth_templet_level asc  ";
	}else if($admininfo[mall_type] == "O"){
		$sql = "SELECT * FROM admin_auth_templet where use_openmarket = 'Y' order by auth_templet_level asc  ";
	}else{
		$sql = "SELECT * FROM admin_auth_templet  order by auth_templet_level asc ";
	}
	//echo $sql;
	$db->query($sql);






		$Contents .= "<div style='width:100%;padding:10px 0px;' align=right>
							<input type='checkbox' name='all_menu' id='all_menu' value='1' ><label for='all_menu'>다른메뉴 모두적용</lebel>
							<select name='add_auth_templet_ix'>
								<option value=''>전체메뉴</option>";
if($db->total){
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
		$Contents .= "<option value='".$db->dt[auth_templet_ix]."'>".$db->dt[auth_templet_name]."</option>";
	}
}

		$Contents .= "</select>
							<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
							</div>";
	}

	$Contents .= "</form>";
/*
	$Contents .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
	if ($pageview01 == 0){
		$Contents .= "<tr height=150 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
	}
	$Contents .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
	$Contents .= "<tr height=25 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
	$Contents .= "</table>\n";
*/
	/*$help_text = "
	<table>
		<tr>
			<td style='line-height:150%'>
			- 권한 템플릿 상세권한관리란? 사용자 타입에 따라서 메뉴접근권한 타입을 별도 관리 하는 기능<br>
			- 다른 메뉴접근권한을 갖는 관리자 타입을 생성하기 위해서는 별도의 권한 템플릿을 생성하셔야 합니다.

			</td>
		</tr>
	</table>
	";*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


	$Contents .= HelpBox("권한 템플릿 상세권한관리", $help_text);


$Script = "<script language='javascript' src='../js/table_changeorder.js'></script>
<script language='javascript'>
function CheckedAll(obj_id, target_obj_ix){
	
	if($('#'+obj_id).is(':checked')){
	
			$('.'+target_obj_ix).each(function(){
				if($(this).is(':checked')){
					$(this).attr('checked','');
				}else{
					$(this).attr('checked','checked');
				}
			})
	}else{
		$('.'+target_obj_ix).each(function(){
			$(this).attr('checked',false);
		})
	}

}


function clearAll(frm){
		for(i=0;i < frm.menu_code.length;i++){
				frm.menu_code[i].checked = false;
		}
}
function checkAll(frm){
	for(i=0;i < frm.menu_code.length;i++){
				frm.menu_code[i].checked = true;
		}
}
function fixAll(frm){
	if (!frm.menu_code_all.checked){
		clearAll(frm);
		frm.menu_code_all.checked = false;

	}else{
		checkAll(frm);
		frm.menu_code_all.checked = true;
	}
}

function CheckAuthViewType(){
	
	if($('#auth_view_type').attr('checked') == 'checked' || $('#auth_view_type').attr('checked') == true){				
		$.cookie('auth_view_type', 'ALL', {expires:1,domain:document.domain, path:'/', secure:0});		
	}else{		
		$.cookie('auth_view_type', '', {expires:1,domain:document.domain, path:'/', secure:0});
	}

	document.location.reload();
}
</script>
";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 메뉴 관리 > 권한 템플릿 상세권한관리";
$P->title = "권한 템플릿 상세권한관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


function getMenuGroup($i, $selected=""){
	$mdb = new Database;



	$sql = 	"SELECT *
			FROM admin_menu_div
			where disp=1";

	$mdb->query($sql);

	$mstring = "<select name='set_menu_info[".$i."][menu_div]' id='menu_div' >";
	$mstring .= "<option value=''>1차분류</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[div_name] == $selected){
				$mstring .= "<option value='".$mdb->dt[div_name]."' selected>".$mdb->dt[div_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[div_name]."'>".$mdb->dt[div_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}



function getMenuGroupTab($selected=""){
	global $auth_templet_ix, $admininfo;
	$mdb = new Database;

	//몰타입에 따른 권한템플릿 리스트 노출 관리 1305070  JK
	if($admininfo[charger_id] == "forbiz"){
		$type_where = " ";
	}else{
		if($admininfo[mall_type] == "H"){
			$type_where = " and gnb_use_home = 'Y' ";//홈페이지 빌더
		}else if($admininfo[mall_type] == "F"){
			$type_where = "  and gnb_use_soho = 'Y'"; //무료형에대한 메뉴구분 정보가 없슴 (소호형 정보로 대체 함)
		}else if($admininfo[mall_type] == "R"){
			$type_where = "  "; //임대형에대한 메뉴구분 정보가 없슴
		}else if($admininfo[mall_type] == "S"){
			$type_where = "  "; //독립형에대한 메뉴구분 정보가 없슴
		}else if($admininfo[mall_type] == "B"){
			$type_where = " and gnb_use_biz = 'Y'"; //입점형에대한 메뉴구분 정보가 없슴 (비즈형 정보로 대체 함)
		}else if($admininfo[mall_type] == "BW"){
			$type_where = " and gnb_use_wholesale = 'Y'"; //비즈도매형에대한 메뉴구분 정보가 없슴 (도매형 정보로 대체 함)
		}else if($admininfo[mall_type] == "O"){
			$type_where = " and gnb_use_openmarket = 'Y'"; //오픈마켓 
		}else if($admininfo[mall_type] == "E"){
			$type_where = " and gnb_use_enterprise = 'Y'"; //오픈마켓 형
		}else{
			$type_where = " "; //오픈마켓 형
		}
		//상점 타입 (H:홈페이지, F:무료형, R:임대형, S:독립형, B:입점형,BW:비즈도매형, O:오픈마켓형)
	}

	$sql = 	"SELECT *
			FROM admin_menu_div
			where disp=1 $type_where order by vieworder asc ";

	$mdb->query($sql);

	if($mdb->total){
		for($i=0;$i < $mdb->total && $i < 10;$i++){
			$mdb->fetch($i);

			$mstring .= "
					<table id='tab_01' ".($mdb->dt[div_name] == $selected ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?menu_div=".$mdb->dt[div_name]."&auth_templet_ix=".$auth_templet_ix."' class=small>".$mdb->dt[gnb_name]."</a></td>
						<th class='box_03'></th>
					</tr>
					</table>";
		}

		$mstring .= "<div style='padding:0px 0px 0px 20px'><select name='menu_div' id='menu_div' style='margin-left:5px;border:1px solid silver;padding:1px;' onchange=\"document.location.href='?menu_div='+this.value+'&auth_templet_ix='+".$auth_templet_ix."\">";
		$mstring .= "<option value=''>메뉴분류</option>";
		if($mdb->total){
			for($i=$i;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[div_name] == $selected){
					$mstring .= "<option value='".$mdb->dt[div_name]."' selected>".$mdb->dt[gnb_name]."</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[div_name]."'>".$mdb->dt[gnb_name]."</option>";
				}
			}

		}
		$mstring .= "</select></div>";



	}

	return $mstring;
}
?>
