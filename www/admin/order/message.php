<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include("excel_out_columsinfo.php");

$db = new Database;

if($type == "deny"){
	$title = "거부사유";
	$navigation = "주문관리 > 거부사유";
}elseif($type == "return_apply"){
	$title = "반품 요청사유";
	$navigation = "주문관리 > 반품 요청사유";
}else{
	$title = "교환 신청사유";
	$navigation = "주문관리 > 교환 요청사유";
}

if($type == "deny"){
	$sql = "select status_message, regdate from ".TBL_SHOP_ORDER_STATUS."
				where oid = '".$oid."' and pid = '".$pid."' and status in('".ORDER_STATUS_EXCHANGE_DENY."','".ORDER_STATUS_RETURN_DENY."') ";
	//echo $sql;
	$db->query($sql);
	$db->fetch();
}elseif($type == "return_apply"){
	$sql = "select return_reason, return_reason_detail, ea_date  from ".TBL_SHOP_ORDER_DETAIL."
						where od_ix = '".$od_ix."'";
	//echo $sql;
	$db->query($sql);
	$db->fetch();

	if($db->dt[return_reason] == 1){
		$return_reason = "불량";
	}else if($db->dt[return_reason] == 2){
		$return_reason = "오배송";
	}else{
		$return_reason = "기타";
	}
}else{
	$sql = "select return_reason, return_reason_detail, ea_date  from ".TBL_SHOP_ORDER_DETAIL."
						where od_ix = '".$od_ix."'";
	//echo $sql;
	$db->query($sql);
	$db->fetch();

	if($db->dt[return_reason] == 1){
		$return_reason = "불량";
	}else if($db->dt[return_reason] == 2){
		$return_reason = "오배송";
	}else{
		$return_reason = "기타";
	}
}

if($type == "deny"){
	$Contents = "
		<table cellpadding=0 cellspacing=0 border=0 width='100%' align=center style='margin:0px 0 0 0px;background-color:#ffffff;'>
		<tr >
			<td align='left' colspan=2> ".GetTitleNavigation($title, $navigation, false)."</td>
		</tr>
		<tr>
			<td valign=top style='padding:10px;'>
			<table border='0' width='100%' cellspacing='1' cellpadding='2'  class='input_table_box'>
				<tr height='100' bgcolor=#ffffff>
					<td class='input_box_title' align='left' >거부사유</td>
					<td class='input_box_item'  align='left' valign=top style='line-height:130%;padding:10px;'>&nbsp;".nl2br($db->dt[status_message])."</td>
				</tr>
				<tr height='23' bgcolor=#ffffff>
					<td class='input_box_title' align='left' >거부일자</td>
					<td class='input_box_item'  align='left'>&nbsp;".$db->dt[regdate]."</td>
				</tr>
			</table>
			</td>

		</tr>
		</table>
		";
}else{
	$Contents = "<!--img src='../images/0.gif' width='880' height='1'-->

		<table cellpadding=0 cellspacing=0 border=0 width='100%' align=center style='margin:0px 0 0 0px;background-color:#ffffff;'>
		<tr >
			<td align='left' colspan=2> ".GetTitleNavigation($title, $navigation, false)."</td>
		</tr>
		<tr>
			<td valign=top style='padding:10px;'>
			<table border='0' width='100%' cellspacing='1' cellpadding='2'  class='input_table_box'>
				<tr height='23' bgcolor=#ffffff>
					<td class='input_box_title' width=120 align='left' >교환 이유</td>
					<td class='input_box_item' align='left'>&nbsp;".$return_reason."</td>
				</tr>
				<tr height='100' bgcolor=#ffffff>
					<td class='input_box_title' align='left' >교환 상세 이유</td>
					<td class='input_box_item'  align='left' valign=top style='line-height:130%;padding:10px;'>&nbsp;".nl2br($db->dt[return_reason_detail])."</td>
				</tr>
				<tr height='23' bgcolor=#ffffff>
					<td class='input_box_title' align='left' >교환 요청일자</td>
					<td class='input_box_item'  align='left'>&nbsp;".$db->dt[ea_date]."</td>
				</tr>
			</table>
			</td>

		</tr>
		</table>

		";
}


$Script = "
<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
<script type='text/javascript' src='../js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='../js/ui/jquery.ui.core.js'></script>
<script type='text/javascript'>
$(document).ready(function() {
	$('#excel_sortlist').sortable();
	$('#excel_sortlist').disableSelection();
});

function CheckValue(frm){
	var params = $('#excel_sortlist').sortable('toArray');
	$('input[name=order_excel_info1]').val(params);
	//alert(params);
	return true;
}
</script>
<style>
  #excel_sortlist {
      list-style-type:none;
      margin:0;
      padding:0;

   }
   #excel_sortlist li {
     margin:1
     padding:0 0 0 10;
     cursor:move;
     width:445px;
     display:inline;
     border:1px solid #c0c0c0;
   }

   #excel_sortlist2 div {
     margin:1
     padding:0 0 0 10;
     cursor:move;
     width:445px;
     display:inline;
     border:1px solid #c0c0c0;
   }

</style>";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = $navigation;
$P->NaviTitle = $title;
$P->strContents = $Contents;
echo $P->PrintLayOut();

/*
$P = new popLayOut;
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->strContents = $Contents;
$P->Navigation = "HOME > 주문관리 > 주문내역저장하기";
$P->PrintLayOut();
*/


$mstring ="
	<html>
	<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
	<body >
	<div id='category_top_view_area' >
	".$db->dt[category_top_view]."
	</div>
	</body>
	</html>
	<script>
	parent.document.forms['thisCategoryform'].category_use.checked = $category_use;
	parent.iView.document.body.innerHTML = document.getElementById('category_top_view_area').innerHTML;
	</script>";

//echo $db->total;
function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);

	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($mdb){
	global $selected_cid;
	$cname = $mdb->dt[cname];
	$cid = $mdb->dt[cid];
	$depth = $mdb->dt[depth];
	$category_display_type = $mdb->dt[category_display_type];



	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname = str_replace("\"","&quot;",$cname);

//	if ($cid == $mcid){
//		$expandstring = "true";
//	}else{
//		$expandstring = "false";
//	}

//	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');

	if($mdb->dt[category_display_type] == "P"){
		$mstring =  "		var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
	}else if($mdb->dt[category_display_type] == "F"){
		$mstring =  "		var node$cid = new TreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	}else{
		$mstring =  "		var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');\n";
	}


	if(substr($selected_cid,0,($depth+1)*3) == substr($cid,0,($depth+1)*3)){
		$mstring .=  "
		node$cid.expanded = true;
		node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type')\";
		rootnode.addNode(node$cid);\n\n";
	}else{
		$mstring .=  "
		node$cid.expanded = false;
		node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type')\";
		rootnode.addNode(node$cid);\n\n";
	}

	return $mstring;
}

function PrintGroupNode($mdb)
{
	$cname = $mdb->dt[cname];
	$mcid = $mdb->dt[cid];
	$depth = $mdb->dt[depth];
	$category_display_type = $mdb->dt[category_display_type];

	global $cid;
	$cid1 = substr($mcid,0,3);
	$cid2 = substr($mcid,3,3);
	$cid3 = substr($mcid,6,3);
	$cid4 = substr($mcid,9,3);
	$cid5 = substr($mcid,12,3);

	$Parentdepth = $depth - 1;

	if ($depth+1 == 1){
		$cid1 = "000";
	}else if($depth+1 == 2){
		$cid2 = "000";
	}else if($depth+1 == 3){
		$cid3 = "000";
	}else if($depth+1 == 4){
		$cid4 = "000";
	}else if($depth+1 == 5){
		$cid5 = "000";
	}

	$parent_cid = "$cid1$cid2$cid3$cid4$cid5";

	if ($depth ==1){
		$ParentNodeCode = "node$parent_cid";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==4){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==5){
		$ParentNodeCode = "groupnode$parent_cid";
	}



	$cname = str_replace("\"","&quot;",$cname);
	if($mdb->dt[category_display_type] == "P"){
		$mstring =  "		var groupnode$mcid = new TreeNode('$cname', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
	}else if($mdb->dt[category_display_type] == "B"){
		$mstring =  "		var groupnode$mcid = new TreeNode('$cname', '../resources/Common_TreeNode_BBS.gif', '../resources/Common_TreeNode_BBS.gif');\n";
	}else{
		$mstring =  "		var groupnode$mcid = new TreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	}

	if ($mcid == $cid){
		$mstring .=  "	groupnode$cid.expanded = true;\n";
	//	$mstring .=  "	groupnode$cid.select = true;\n";
	}


	$mstring .=  "	groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth, '$category_display_type')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";

	return $mstring;
}


?>