<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include("excel_out_columsinfo.php");

$db = new Database;

if($excel_type == "delivery"){
	$excel_name = "배송엑셀 ";
	$act = "delivery_cookie_setting";
	$sql = "select delivery_excel_info1 as order_excel_info1, delivery_excel_info2 as order_excel_info2, delivery_excel_checked as order_excel_checked
			from ".TBL_COMMON_SELLER_DETAIL."
			where company_id = '".$admininfo[company_id]."'";
}else{
	$excel_name = "주문엑셀 ";
	$act = "cookie_setting";
	$sql = "select order_excel_info1, order_excel_info2, order_excel_checked
			from ".TBL_COMMON_SELLER_DETAIL."
			where company_id = '".$admininfo[company_id]."'";
}

$db->query($sql);
$db->fetch();

$Contents = "<!--img src='../images/0.gif' width='880' height='1'-->
<table class='layer_box' border=0 cellpadding=0 cellspacing=0 style='width:880px;display:block;' >
	<col width='11px'>
	<col width='*'>
	<col width='11px'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02' ></td>
		<th class='box_03'></th>
	</tr>

	<tr>
		<th class='box_04' style='vertical-align:top'></th>
		<td class='box_05' rowspan=2 valign=top style='padding:5px 15px 5px 5px;font-weight:bold;font-size:12px;line-height:150%;text-align:left;' >

		<table cellpadding=0 cellspacing=0 border=0 width='820' align=center style='margin:0px 0 0 20px;background-color:#ffffff;'>
		<tr>
			<td style='padding:20px 0px;'>
			<table width=100% border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef;padding:3px 0px;'><b style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;'> <img src='/admin/v3/images/common/arrow_icon02.gif' align=absmiddle> $excel_name 설정하기</b></td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>

			<tr>
			<td valign=top width='810'>
				<form name='excel_out_frm' method=\"post\" enctype='multipart/form-data' action='excel_out.act.php' onsubmit='return CheckValue(this);' target='act' >
				<input type='hidden' name='act' value='".$act."'><!--excel_out-->
				<input type='hidden' name='order_excel_info1' value=''>

					<ul id='excel_sortlist'>";
//for($i=0;$i < count($colums);$i++){
//
//print_r($colums);
//for ( reset($colums); $key = key($colums); next($colums) ){
//print_r(unserialize(str_replace("\\\"","\"",$_COOKIE[_excel_sortlist])));


//$_erpid = str_replace("\\\"","\"",$_POST["erpid"]);

//print_r($_COOKIE[_excel_sortlist]);
//echo $db->dt[order_excel_info1];
if($db->dt[order_excel_info1]){
$sortlist = explode(",",unserialize($db->dt[order_excel_info1]));
$check_colums = unserialize(stripslashes($db->dt[order_excel_checked]));
//echo $db->dt[order_excel_checked];
//print_r($sortlist);
	for($i=0;$i < count($sortlist);$i++){
		//echo $sortlist[$i];
		//echo $check_colums[$sortlist[$i]];
			$Contents .= "<li class='ui-state-default leftmenu' id='".$sortlist[$i]."'  style='float:left;height:25px;width:125px;padding:4px 0px 0px 3px;margin:1px;'>
							<table width=125 border=0>
							<col width=10>
							<col width=25>
							<col width=*>
							<tr>
								<td >

								</td>
								<td>
								<input type='checkbox' id='colums' style='cursor:pointer;' name='colums[".$sortlist[$i]."]' value='".$sortlist[$i]."'  validation='false' title='".$colums[$sortlist[$i]][title]."' ".($check_colums[$sortlist[$i]] ? "checked":"").">
								</td>
								<td style='padding-left:0px; font-size:11px;'>
								<label for='_colums_".$sortlist[$i]."'>".$colums[$sortlist[$i]][title]."</label>
								</td>
							</tr>
							</table>
							</div>
						</li>
						";
	}
}else{
	foreach($colums as $key => $value){
			$Contents .= "<li class='ui-state-default leftmenu' id='".$key."' style='float:left;height:25px;width:125px;padding:4px 0px 0px 3px;margin:1px;' >
							<table width=125 border=0>
							<col width=10>
							<col width=25>
							<col width=*>
							<tr>
								<td>

								</td>
								<td>
								<input type='checkbox' id='colums' style='cursor:pointer;' name='colums[".$key."]' value='".$key."'  validation='false' title='".$value[title]."' ".$value[checked].">
								</td>
								<td style='padding-left:0px;'>
									<label for='_colums_".$key."'>".$value[title]."</label>
								</td>
							</tr>
							</table>
						</li>";
	}
}


$Contents .= "</ul>";


/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' style='width:780px;' >
	<col width=8>
	<col width=*>
	<tr>
		<td valign=top><img src='/admin/image/icon_list.gif' ></td>
		<td class='small' ><b>엑셀필드 순서 정의</b> : 앞쪽부터 주문번호, 주문일자 ... 등으로 엑셀에 출력됩니다 순서를 바꾸시길 원하시면 마우스로 드래그해서 순서를 변경하시기 바랍니다 </td>
	</tr>
	<tr>
		<td valign=top><img src='/admin/image/icon_list.gif' ></td>
		<td class='small' >엑셀에 출력되길 원하는 필드만 선택하시면 됩니다 </td>
	</tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

/*
$help_text = "
<ul>
	<li>
		<img src=\"/admin/image/icon_list.gif\" align=absmiddle>
		<b>엑셀필드 순서 정의</b> : 앞쪽부터 주문번호, 주문일자 ... 등으로 엑셀에 출력됩니다 순서를 바꾸시길 원하시면 마우스로 드래그해서 순서를 변경하시기 바랍니다
	</li>
	<li>
		<img src=\"/admin/image/icon_list.gif\" align=absmiddle >
		엑셀에 출력되길 원하는 필드만 선택하시면 됩니다
	</li>
</ul>
";
*/

$help_text = HelpBox("주문내역저장하기", $help_text);
$Contents .="	<tr>
					<td align=center style='padding:30px 0px '>
					 <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' >

					</td>
				</tr>
				<tr>
					<td style='padding-bottom:30px;'>$help_text 	</td>
				</tr>
				</table>
				</form>";



$Contents .= "
		<iframe name='calcufrm' src='' width=0 height=0></iframe>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>
		";



$Script = "
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
$P->layout_display = false;
$P->Navigation = "주문관리 > 교환 신청사유";
$P->NaviTitle = "교환 신청사유";
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