<?
include("../class/layout.class");

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

$db->query("SELECT * FROM inventory_customer_info where customer_type = 'E'");
$total = $db->total;

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max","");
$db->query("SELECT * FROM inventory_customer_info where customer_type = 'E' order by is_basic, regdate LIMIT $start, $max");
$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	<tr>
	    <td align='left' colspan=7 style='padding-bottom:10px;'> ".GetTitleNavigation("입고처관리", "기초정보 관리 > 입고처관리")."</td>
	</tr>
	<!--tr>
		<td align='left' colspan=7 style='padding-bottom:10px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> 입고처 리스트</div>")."</td>
	</tr-->
<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
	<col width=7%>
	<col width='*'>
	<col width=20%>
	<col width=20%>
	<col width=20%>
	<col width=15%>
	<col width=15%>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>입고처구분</td>
		<td class='m_td'>입고처명</td>
		<td class='m_td'>전화</td>
		<td class='m_td'>팩스</td>
		<td class='e_td'>관리</td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		//$phone = explode("-",$db->dt[customer_phone]);
		//$fax = explode("-",$db->dt[customer_fax]);
		if($db->dt[customer_div] == 1){
			$customer_div = "자사입고처";
		}else if($db->dt[customer_div] == 9){
			$customer_div = "타사입고처";
		}else{
			$customer_div = "-";
		}


		$mstring .="<tr>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td' style='text-align:left; padding-left:10px'><a href='supply_vendor.add.php?ci_ix=".$db->dt[ci_ix]."'>".$customer_div."</a></td>
					<td class='list_box_td point'>".$db->dt[customer_name]."</td>
					<td class='list_box_td'>".$db->dt[customer_phone]."</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[customer_fax]."</td>
					<td class='list_box_td' style='padding:4px 0px;' nowrap>";
			$mstring .="<a href='supply_vendor.add.php?ci_ix=".$db->dt[ci_ix]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
			if($db->dt[is_basic] != "Y"){
				$mstring .=" <a href='vendor.act.php?ci_ix=".$db->dt[ci_ix]."&act=delete&customer_div=E'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$mstring .="
					</td>
				</tr>";
	}
		$mstring .=	"</table>";
		$mstring .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$mstring .= "<tr height=50><td colspan=7 align=center style='padding-top:10px;'>등록된 입고처가 없습니다.</td></tr>
				<tr hegiht=1><td colspan=7  class='td_underline'></td></tr>";
}

	$mstring .= "<tr hegiht=40><td colspan=4>".$str_page_bar."</td><td colspan=2 align=right style='padding-top:10px;'><a href='supply_vendor.add.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a></td></tr>";

$mstring .="</table><br>";
$Contents = $mstring;

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >귀사에서 관리하는 입고처를 등록 관리하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >입고처 정보를 수정하시고자 할때는 수정 버튼 또는 업체명을 클릭하시면 수정하실수 있습니다</td></tr>
</table>
";


$help_text = HelpBox("입고처처관리", $help_text);
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
$P->Navigation = "재고관리 > 기초정보 관리 > 입고처관리";
$P->title = "입고처관리";
$P->strContents = $Contents;
$P->PrintLayOut();




?>