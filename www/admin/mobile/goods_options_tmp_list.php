<?
include("../class/mobilelayout.class");
include("../../class/database.class");
include("../include/admin.util.php");


$Script = "<script language='javascript' >

 function DeleteOptionTmpInfo(opnt_ix){
 	if(confirm('해당 임시옵션 정보를 정말로 삭제 하시겠습니까?')){
		window.frames['act'].location.href='../product/goods_options_input.act.php?act=delete&opnt_ix='+opnt_ix;
 	}
}
</script>
<style type='text/css'>
	.goods_input_header{padding:0 0 0 8px ;background:#ebebeb;border-bottom:2px solid #d3d3d3;}
	.goods_input_header:after{content:'';clear:both;display:block;} 
	.goods_input_header span{padding-left:9px;background:url('./images/li_bg.gif') 0 center no-repeat;background-size:3px 3px;font-size:15px;font-weight:bold;}
	.goods_input_header tr td {height:46px;}
</style>";

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

$where=" where (basic='Y' or charger_ix='".$_SESSION["admininfo"]["charger_ix"]."' )";

$db->query("SELECT count(*) as total FROM shop_product_options_tmp $where ");
$db->fetch();
$total = $db->dt[total];


$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max","");

$db->query("SELECT * FROM shop_product_options_tmp $where order by regdate desc LIMIT $start,$max");

$mstring = "
<div class='goods_input_header'>
	<table border='0' cellpadding='0' cellspacing='0' width=100%'>
	<col width='50%' />
	<col width='50%' />
		<tr>
			<td><span>자주쓰는옵션 리스트</span></td>
			<td align='right'>
			</td>
		</tr>
	</table>
</div>
<div style='clear:both;height:10px;'></div>
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
	<col width='10%'>
	<col width='*'>
	<col width='15%'>
	<col width=10%>
	<col width=25%>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>옵션명</td>
		<td class='m_td'>옵션종류</td>
		<td class='m_td'>사용<br/>여부</td>
		<td class='e_td'>관리</td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		
		if($db->dt[option_kind] == "s"){
			$option_kind_str = "선택옵션";
		}else if($db->dt[option_kind] == "p"){
			$option_kind_str = "가격추가옵션";
		}
		
		/*
		if($db->dt[option_type] == "9"){
			$option_type_str = "기본옵션";
		}else if($db->dt[option_type] == "1"){
			$option_type_str = "가격추가옵션";
		}
		*/

		$mstring .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td point'>".$db->dt[option_name]." ".($db->dt[basic]=="Y" ? "(기본제공)" : "")."</td>
					<td class='list_box_td list_bg_gray'>".$option_kind_str."</td>
					<td class='list_box_td'>".($db->dt[disp] ? "사용":"사용안함")."</td>
					<td class='list_box_td list_bg_gray' >";
			if(($db->dt[basic]=="Y" && $_SESSION["admininfo"]["admin_level"]==9) || $db->dt[charger_ix]==$_SESSION["admininfo"]["charger_ix"]){
				$mstring .="
					<a href=\"./goods_options_input.php?opnt_ix=".$db->dt[opnt_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
					<a href=\"javascript:DeleteOptionTmpInfo('".$db->dt[opnt_ix]."')\"><!--a href='goods_options_input.act.php?opnt_ix=".$db->dt[opnt_ix]."&act=delete'--><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$mstring .="
					</td>
				</tr>";
	}
	$mstring .=	"</table>";
	$mstring .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$mstring .= "
				<tr height=50><td colspan=7 align=center style='padding:30px 0px;'>등록된 임시 옵션정보가 없습니다.</td></tr>";
}

$mstring .="</table>";
$mstring .= "
	<table cellpadding=1 cellspacing=0 width=100% >
		<tr hegiht=30>
			<td align=center>".$str_page_bar."</td>
		</tr>
		<tr>
			<td align='center' style='padding-top:10px;'>
				<img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"document.location.href='goods_options_input.php'\">
				<img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"document.location.href='goods_options_tmp.php'\">
			</td>
		</tr>
	</table><br>";
$Contents = $mstring;



$P = new MobileLayOut();
$P->addScript = $Script;
//$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "";
$P->layout_display = false;
echo $P->PrintLayOut();

?>