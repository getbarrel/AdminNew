<?
include("../class/layout.class");


	$fordb = new Database();


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

	$where = "where menu_code is not null ";
	if($menu_div != ""){
		$where .= " and menu_div = '".$menu_div."' ";
	}

	if($search_text != ""){
		$where .= " and (menu_name LIKE '%".$search_text."%' )";
	}

	$sql = "Select count(*) as total from admin_menus $where ";
	$fordb->query($sql);
	$fordb->fetch();
	$total = $fordb->dt[total];

	$sql = "Select * from admin_menus $where order by menu_div, view_order asc, regdate asc limit $start, $max";


	$fordb->query($sql);

$SearchForm = "<form name='search_frm' method='GET'><input type='hidden' name='menu_div' value='$menu_div'>
				<table>
					<tr>
						<td><input type='text' class=textbox name='search_text' ></td>
						<td><input type='image' src='../images/".$admininfo['language']."/btn_search.gif' align=absmiddle></td>
					</tr>
				</table></form>";


	$Contents .= "
<table cellpadding=0 cellspacing=0 width=100%  STYLE='TABLE-LAYOUT:fixed'>
	 
	<tr >
		<td align='left' colspan=11 > ".GetTitleNavigation("메뉴관리", "상점관리 > 메뉴관리 ")."</td>
	</tr>
	<tr >
		<td align='left' colspan=11 style='padding-bottom:10px;'> ".$SearchForm."</td>
	</tr>
	 <tr>
	    <td align='left' colspan=11 style='padding-bottom:15px;'>
	    <div class='tab'>
			<table class='s_org_tab' width='100%'>

			<tr>
				<td class='tab' width='73%'>
					<table id='tab_01' ".($menu_div == "" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?text_div='>전체</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					".getMenuGroupTab($menu_div)."
				</td>
				<td style='text-align:right;vertical-align:top;padding:0px 0px 10px 10px'>
					<a href='./menu_div.php'><img src='../images/".$admininfo["language"]."/btn_gnb_control.gif' style='vertical-align:middle;'></a>
					<a href='./auth_templet.php'><img src='../images/".$admininfo["language"]."/btn_templet_control.gif' style='vertical-align:middle;'></a>
				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>
	</table>
	<form name='page_frm' action='menu.act.php' method='POST' target='act'><input type=hidden name=act value='updates'>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;min-width:750px;'>
	<col width='4%'>
	<col width='8%'>
	<col width='*'>
	<col width='13%'>
	<col width='5%'>
	<col width='5%'>
	<col width='5%'>
	<col width='5%'>
	<col width='5%'>
	<col width='5%'>
	<col width='5%'>
	<col width='20%'>
	<col width='6%'>
	<tr height=25 align=center>
		<td class='s_td'><input type='checkbox' name='menu_code_all' onclick='fixAll(document.page_frm)'></td>
		<td class='m_td small' >노출순서</td>
		<td class='m_td' >메뉴명</td>
		<td class='m_td small' >메뉴분류</td>
		<td class='m_td small' nowrap>홈빌더</td>
		<td class='m_td small' nowrap>소호형</td>
		<td class='m_td small' nowrap>비즈형</td>
		<td class='m_td small' nowrap>도매형</td>
		<td class='m_td small' nowrap>오픈마켓<br>(스탠다드)</td>
		<td class='m_td small' nowrap>오픈마켓<br>(Enterprise)</td>
		<td class='m_td small' >접근권한노출</td>
		<td  class='m_td'>메뉴위치/메뉴경로</td>
		<td  class='e_td' >복사</td>
	</tr>\n";
	for($i=0;$i<$fordb->total;$i++){
		$fordb->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		$menu_name = str_replace("\"","&quot;",$fordb->dt[menu_name]);
		$menu_name = str_replace("'","&#39;",$menu_name);
		//$page_ko_name = str_replace("'","\'",$page_ko_name);

		$Contents .= "<tr height=40 bgcolor=#ffffff  id='Report$i'>
		<td class='list_box_td' align=center>
		<input type='checkbox' value='".$fordb->dt[menu_code]."' name='menu_info[".$i."][menu_code]' id='menu_code'>
		<input type='text' value='".$fordb->dt[menu_code]."'>
		</td>
		<td class='list_box_td list_bg_gray'>
			<input type='hidden' value='".$fordb->dt[view_order]."' name='menu_info[".$i."][b_view_order]' id='b_view_order' >
			<input type='text' value='".$fordb->dt[view_order]."' class='textbox'  name='menu_info[".$i."][view_order]' id='view_order' style='margin-top:3px;width:70%;height:20px;vertical-align:middle;'>
		</td>
		<td class='list_box_td point' align=left title='".urldecode($fordb->dt[vurl])."' style='padding:4px 4px' wrap>
			<input type='text' value='".$fordb->dt[menu_name]."' class='textbox' name='menu_info[".$i."][menu_name]' id='menu_name' style='margin-top:3px;width:90%;height:20px;vertical-align:middle;'>
		</td>
		<td class='list_box_td list_bg_gray' style='padding:3px;' >
			".getMenuGroup($i,$fordb->dt[menu_div])."<br>
			<input type='text' value='".$fordb->dt[menu_param]."' class='textbox' name='menu_info[".$i."][menu_param]' id='menu_param' style='margin-top:3px;height:20px;vertical-align:middle;width:85px;'><!--".$fordb->dt[menu_div]."-->
		</td>
		<td class='list_box_td' >
		<input type='checkbox' value='Y' name='menu_info[".$i."][use_home]' id='use_home' ".($fordb->dt[use_home] == "Y" ? "checked":"")." onclick=\"$(this).parent().parent().find('#menu_code').attr('checked','checked');\">
		</td>
		<td class='list_box_td list_bg_gray' >
		<input type='checkbox' value='Y' name='menu_info[".$i."][use_soho]' id='use_soho' ".($fordb->dt[use_soho] == "Y" ? "checked":"")." onclick=\"$(this).parent().parent().find('#menu_code').attr('checked','checked');\">
		</td>
		<td class='list_box_td' >
		<input type='checkbox' value='".$fordb->dt[use_business]."' name='menu_info[".$i."][use_business]' id='use_business' ".($fordb->dt[use_business] == "Y" ? "checked":"")." onclick=\"$(this).parent().parent().find('#menu_code').attr('checked','checked');\">
		</td>
		<td class='list_box_td' >
		<input type='checkbox' value='".$fordb->dt[use_wholesale]."' name='menu_info[".$i."][use_wholesale]' id='use_wholesale' ".($fordb->dt[use_wholesale] == "Y" ? "checked":"")." onclick=\"$(this).parent().parent().find('#menu_code').attr('checked','checked');\">
		</td>
		<td class='list_box_td list_bg_gray'>
		<input type='checkbox' value='".$fordb->dt[use_openmarket]."' name='menu_info[".$i."][use_openmarket]' id='use_openmarket' ".($fordb->dt[use_openmarket] == "Y" ? "checked":"")." onclick=\"$(this).parent().parent().find('#menu_code').attr('checked','checked');\">
		</td>
		<td class='list_box_td list_bg_gray'>
		<input type='checkbox' value='".$fordb->dt[use_enterprise]."' name='menu_info[".$i."][use_enterprise]' id='use_enterprise' ".($fordb->dt[use_enterprise] == "Y" ? "checked":"")." onclick=\"$(this).parent().parent().find('#menu_code').attr('checked','checked');\">
		</td>
		<td class='list_box_td ' >
		<input type='checkbox' value='Y' name='menu_info[".$i."][disp_auth]' id='disp_auth' ".($fordb->dt[disp_auth] == "Y" ? "checked":"")." onclick=\"$(this).parent().parent().find('#menu_code').attr('checked','checked');\" >
		</td>
		<td class='list_box_td list_bg_gray' style='padding:3px;' wrap>
			<input type='text' onfocus=\"this.style.border = '3px solid #efefef'\" onblur=\"this.style.border = '1px solid #efefef'\" value='".$fordb->dt[menu_link]."' name='menu_info[".$i."][menu_link]' class='textbox'  style='margin-top:3px;width:90%;height:20px;vertical-align:middle;' >
			<input type='text' onfocus=\"this.style.border = '3px solid #efefef'\" onblur=\"this.style.border = '1px solid #efefef'\" value='".$fordb->dt[menu_path]."' name='menu_info[".$i."][menu_path]' class='textbox'  style='margin-top:3px;width:90%;height:20px;vertical-align:middle;' >
		</td>
		<td class='list_box_td' >";
         if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
            $Contents .= "
		      <input type='checkbox' value='Y' name='menu_info[".$i."][menu_copy]' id='menu_copy' style='vertical-align:middle;'><a href=\"javascript:deleteMenu('".$fordb->dt[menu_code]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' style='vertical-align:middle;'></a>
            ";
        }else{
            $Contents .= "
              <a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0></a>
            ";
        }
        $Contents .= "
		</td>
		</tr>\n";



	}
	$Contents .= "</table>\n";
	$Contents .= "<ul class='paging_area' >
						<li class='front'>".page_bar($total, $page, $max,$query_string."&menu_div=".$menu_div,"")."</li>
						<li class='back'>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") || $admininfo[charger_id] == "forbiz"){
	$Contents .= "<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;margin:10px 0px;' >";
}

	$Contents .= "
						</li>
					  </ul>";

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
/*	$help_text = "
	<table>
		<tr>
			<td style='line-height:150%'>
			- 메뉴 관리는 관리자의 기능에 따른 메뉴명을 수정하거나 노출 위치를 수정할수 있는 기능입니다. <br>

			</td>
		</tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

	$Contents .= HelpBox("메뉴 관리", $help_text);


$Script = "
<script language='javascript'>
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

	function deleteMenu(menu_code){
		if(confirm('해당메뉴정보를 정말로 삭제하시겠습니까?')){
			window.frames['act'].location.href='menu.act.php?act=delete&menu_code='+menu_code;
		}
	}
</script>
";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 메뉴 관리 > 메뉴 관리";
$P->title = "메뉴 관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


function getMenuGroup($i, $selected=""){
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM admin_menu_div
			where disp=1 ";

	$mdb->query($sql);

	$mstring = "<select name='menu_info[".$i."][menu_div]' id='menu_div' >";
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
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM admin_menu_div where disp=1 order by vieworder asc ";

	$mdb->query($sql);

	if($mdb->total){
		for($i=0;$i < $mdb->total && $i < 6;$i++){
			$mdb->fetch($i);

			$mstring .= "
					<table id='tab_01' ".($mdb->dt[div_name] == $selected ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?menu_div=".$mdb->dt[div_name]."' class=small>".$mdb->dt[gnb_name]."</a></td>
						<th class='box_03'></th>
					</tr>
					</table>";
		}

		$mstring .= "<div style='padding:0px 0px 4px 30px'><select name='menu_div' id='menu_div' style='border:1px solid silver;padding:1px;margin-left:4px;' onchange=\"document.location.href='?menu_div='+this.value\">";
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
