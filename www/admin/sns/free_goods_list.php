<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

if(!$update_kind){
	$update_kind = "display";
}

if($max == ""){
	$max = 10; //페이지당 갯수
}else{
	$max = $max;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;
$db2 = new Database;
/*if($_SESSION["mode"] == "search"){
	$mode = "search";
}*/

$sql = "SELECT fp.fp_ix FROM ".TBL_SNS_FREEPRODUCT." fp LEFT JOIN ".TBL_SNS_FREEPRODUCT_DIV." fpd on fp.div_ix = fpd.div_ix ";
//echo $sql;
$db->query($sql);


$total = $db->total;

	$sql = "SELECT fp.*, fpd.div_name FROM ".TBL_SNS_FREEPRODUCT." fp LEFT JOIN ".TBL_SNS_FREEPRODUCT_DIV." fpd on fp.div_ix = fpd.div_ix LIMIT $start, $max ";
	//echo $sql;
	$db->query($sql);
	$coupon_list = array();
	$coupon_list = $db->fetchall();
	$no = $total-$start;


	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&list_type=$list_type&div_ix=$div_ix");

$Contents =	"
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("무료쿠폰 목록", "소셜커머스 > 무료쿠폰 목록")."</td>
	</tr>

	";

$Contents .=	"
	<tr>
		<td valign=top style='padding-top:33px;'>";

$Contents .= "
		</td>
		<form name=listform method=post action='goods_batch.act.php' onsubmit='return SelectUpdate(this)' target='iframe_act2' ><!--onsubmit='return CheckDelete(this)' -->
		<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
		<input type='hidden' name='act' value='update'>
		<input type='hidden' name='search_act_total' value='$total'>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
				<tr height=30>
					<td align=left>
					<img src='../images/".$admininfo["language"]."/btn_free_group.gif' onClick='location.href=\"free_goods_category.php\";' style='cursor:pointer;' />
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
			</table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='15%'>
				<col width='*'>
				<col width='10%'>
				<col width='20%'>
				<col width='7%'>
				<col width='7%'>
				<col width='15%'>
				<tr bgcolor=#efefef align=center>
					<td class='s_td' height='30'>분류</td>
					<td class='m_td'>제목</td>
					<td class='m_td''>표시</td>
					<td class='m_td'>노출기간</td>
					<td class='m_td'>다운건수</td>
					<td class='m_td'>남은수량</td>
					<td class='e_td'>관리</td>
				</tr>";




if(is_array($coupon_list)){
	foreach($coupon_list as $_key=>$_val)
	{
		$innerview .= "<tr height='30'>
					<td class='list_box_td list_bg_gray'>".$_val['div_name']."</td>
					<td class='list_box_td '>".$_val['fp_title']."</td>
					<td class='list_box_td list_bg_gray'>".($_val['disp'] == 1 ? "표시":"표시안함")."</td>
					<td class='list_box_td'>".date("Y-m-d", $_val['fp_sdate'])." ~ ".date("Y-m-d", $_val['fp_edate'])."</td>
					<td class='list_box_td list_bg_gray' nowrap>".freeproduc_count($_val['fp_ix'])."</td>
					<td class='list_box_td' nowrap>".$_val['fp_stok']."</td>
					<td class='list_box_td list_bg_gray' align=center>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$innerview .= "<a href='javascript:void(0);' onclick='location.href=\"./free_goods_write.php?fp_ix=".$_val[fp_ix]."\"'><img src='../images/".$admininfo["language"]."/btc_modify.gif'></a> ";
				}else{
					$innerview .= "<a href=\"".$auth_update_msg."\" ><img src='../images/".$admininfo["language"]."/btc_modify.gif'></a> ";
				}

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$innerview .= "<a href='javascript:void(0);' onclick='location.href=\"./free_goods.act.php?act=delete&fp_ix=".$_val[fp_ix]."\"'><img src='../images/".$admininfo["language"]."/btc_del.gif'></a> ";
				}else{
					$innerview .= "<a href=\"".$auth_delete_msg."\" ><img src='../images/".$admininfo["language"]."/btc_del.gif'></a>";
				}

				$innerview .= "
					</td>
				</tr>
				";
	$no--;
	}
} else {
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=8 align=center> 등록된 제품이 없습니다.</td></tr>";
	$innerview = $innerview."<tr><td colspan=8 class='dot-x'></td></tr>";

}
	$innerview .= "</table>
				<table width='100%'>
				<tr height=30>
					<td width=210>

					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
				<tr height=30>
					<td width=210>

					</td>
					<td align=right>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$innerview .= "<a href='./free_goods_write.php'><img src='../images/".$admininfo["language"]."/btn_reg.gif'></a>";
		}


		$innerview .= "</td>
				</tr>
				<tr height=30><td colspan=2 align=right></td></tr>
				</table>

				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>
		<!--iframe id='act' src='' width=0 height=0></iframe-->
			";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small'>
	<col width=8>
	<col width=*>
	<tr>
		<td valign=top><img src='/admin/image/icon_list.gif'></td><td class='small'>검색을 통하여 쿠폰번호를 매칭하여 사용된 쿠폰상태로 변경 합니다. </td>
	</tr>
</table>
";*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$Contents .= HelpBox("SNS 발행쿠폰관리", $help_text);

$Script = "
	<script Language='JavaScript' src='./coupon_list.js'></script>\n

";


$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
	$Script .= "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script>";

	$P = new LayOut();
	$P->strLeftMenu = sns_menu();
	$P->addScript = $Script;
	$P->Navigation = "소셜커머스 > 무료쿠폰 > 무료쿠폰 목록";
	$P->title = "무료쿠폰 목록";
	$P->strContents = $Contents;
	$P->jquery_use = false;

	$P->PrintLayOut();

function freeproduc_count($fp_ix)	{
	$db = new Database;
	$sql =" SELECT count(*) as fp_cnt FROM ".TBL_SNS_FREEGOODSHISTORY." WHERE fp_ix = '".$fp_ix."' ";
	$db->query($sql);
	$db->fetch();

	return $db->dt['fp_cnt'];
}
?>