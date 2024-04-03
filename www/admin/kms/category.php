<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");

$db = new Database;

if($cid){
	$selected_cid = $cid;
}

//print_r($admin_config);
$Contents = "
<script>
/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;


/*	Create Root node	000000000000000 */
	var rootnode = new TreeNode(\"디자인카테고리\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('디자인카테고리','000000000000000',-1,'')\";
	rootnode.expanded = true;
";
	

$db->query("SELECT * FROM kms_mycategory where depth in(0,1,2,3) and uid = '".$admininfo[charger_ix]."'  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");


$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);
	
	if ($db->dt["depth"] == 0){
		$Contents = $Contents.PrintNode($db);
	}else if($db->dt["depth"] == 1){
		$Contents = $Contents.PrintGroupNode($db);
	}else if($db->dt["depth"] == 2){
		$Contents = $Contents.PrintGroupNode($db);
	}else if($db->dt["depth"] == 3){
		$Contents = $Contents.PrintGroupNode($db);
	}else if($db->dt["depth"] == 4){
		$Contents = $Contents.PrintGroupNode($db);
	}else if($db->dt["depth"] == 5){
		$Contents = $Contents.PrintGroupNode($db);
	}
}

$Contents = $Contents."
	tree.addNode(rootnode);
</script>


";


$Contents .= "

		<table cellpadding=0 cellspacing=0 border=0 width='95%'>
		<!--tr>
		    <td align='left' colspan=4 style='padding-bottom:20px;'> ".GetTitleNavigation("디자인분류관리", "디자인관리 > 디자인분류관리")."</td>
		</tr-->		
		<tr>
			<td width=300 valign=top style='padding:10px 0px 0px 10px'>			
			".colorCirCleBoxStart('silver',200)."
			<table cellpadding=0 cellspacing=0 border=0>
			<tr><form name='category_order' method='get' action='categoryorder.php' target='calcufrm'>
				<td style='padding-left:10px;padding-top:3px;' valign=middle>				
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='mode' value=''>
				<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동'>				
				<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cursor:hand' alt='분류 아래로 이동'>			
				</td>
			</tr></form>
			<tr><form>
				<td width=200 height=300 valign=top style='overflow:auto;padding:10px;padding-top:0px;'>
				".colorCirCleBoxStart('#ffffff',200)."				
				
				<div id=TREE_BAR style=\"width:200px;height:320px;overflow:auto;margin:1;\">
				<script>		
				tree.draw();
				tree.nodes[0].select();
				</script>
				</div>				
				".colorCirCleBoxEnd('#ffffff')."
				</td>
			</tr></form>
			</table>
			".colorCirCleBoxEnd('silver')."
			</td >
			<td style='padding:15px;'>
			
			</td>
			<td valign=top width='100%'>	
				<input type='radio' name='category_mode' id='category_mode_edit' value='edit' onclick='CategoryMode(this.value)' checked><label for='category_mode_edit' style='font-weight:bold'>선택된 카테고리 수정</label>
				<input type='radio' name='category_mode' id='category_mode_add' value='add' onclick='CategoryMode(this.value)' ><label for='category_mode_add' style='font-weight:bold'> 분류 및 페이지 추가</label>
				<div id='edit_category' style='display:block;'>
				<form name='thisCategoryform' method=\"post\" enctype='multipart/form-data' action='category.save.php' target='calcufrm' onsubmit='return CheckFormValue(this);'>
				<input type='hidden' name='mode' value=''>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='category_top_view' value=''>
				<table cellpadding=3 cellspacing=0 border=0 width=100%>				
				
				<tr>
					<td width=170 nowrap oncontextmenu='init2();return false;'>선택된 분류</td>
					<td width=100% nowrap>
						<input type='text' name='this_category' validation=true title='선택된 분류'> 
						<input type='checkbox' name='category_use' id='category_use_id' value=1 ><label for='category_use_id'> 사용</label>						
						<td>
				</tr>
				<tr>
					<td width=170 nowrap >파일경로</td>
					<td width=100% nowrap>
						<input type='text' name='path' validation=true urltype=true style='ime-mode:disabled;' title='파일경로'> 					
						<td>
				</tr>
				<tr>
					<td width=170 nowrap >기본링크 (표시URL)</td>
					<td width=100% nowrap>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type='text' name='basic_link' urltype=true style='ime-mode:disabled;' size=30 title='기본링크'></td>
							<td style='padding-left:10px;'><div id='down'></div></td>
						</tr>
					</table>
					<td>
				</tr>
				
				<tr>
					<td width=170 nowrap >선택된 분류 링크</td>
					<td width=100% id='category_link' nowrap>
						왼쪽 분류에서 카테고리를 선택해주세요
					<td>
				</tr>
				
				
				<tr>
					<td colspan=2 align=left> <input type='checkbox' name='sub_cartegory_delete' id='sub_cartegory_delete_id'value='1' > <label for='sub_cartegory_delete_id'>하부카테고리 모두삭제</label> <img src='../image/bt_category_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"CategorySave(document.thisCategoryform,'del');\"> <img src='../image/bt_category_modify.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"CategorySave(document.thisCategoryform,'modify');\"> </td>
				</tr>";
$help_text = "
	<table>
		<tr>
			<td style='line-height:150%' class=small>
			<img src='/admin/image/icon_list.gif' align=absmiddle><b>카테고리 수정</b> : 수정하고자 하는 카테고리를 먼저 선택하신후 내용을 수정하신후 카테고리 수정 버튼을 클릭하시면 됩니다.<br>
			<img src='/admin/image/icon_list.gif' align=absmiddle><b>카테고리 추가</b> : 추가를 원하시는  카테고리를 먼저 선택하신후 상단에 분류및 페이지 추가 버튼을 클릭하신후 정보입력후 카테고리 추가  버튼을 클릭하시면 됩니다.<br>
			</td>
		</tr>
	</table>
	";
$help_text = "
<table cellpadding=3 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top style='line-height:150%'><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>지식분류 수정</b> : 수정하고자 하는 카테고리를 먼저 선택하신후 내용을 수정하신후 카테고리 수정 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top style='line-height:150%'><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>지식분류 추가</b> : 추가를 원하시는  카테고리를 먼저 선택하신후 상단에 분류및 페이지 추가 버튼을 클릭하신후 정보입력후 카테고리 추가  버튼을 클릭하시면 됩니다</td></tr>
	
</table>
";
	
	$help_text = HelpBox("지식분류관리", $help_text);						
$Contents .="		<tr>
					<td colspan=2>$help_text 	</td>
				</tr>
				</table>
				</form>";
		
$Contents .="			
				<!--div style='display:none' id='category_top_view_area'></div-->
				</div>
				
				<div id='add_subcategory' style='display:none;'>
				<form name='subCategoryform' method=\"post\" enctype='multipart/form-data' action='category.save.php' target='calcufrm' onsubmit='return CheckFormValue(this);'>
				<input type='hidden' name='mode' value=''>
				<input type='hidden' name='sub_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_cid' value=''>
				<!--하부카테고리 추가<br>
				<input type='' name='sub_category'> <img src='../image/bt_ok.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"SubCategorySave(document.subCategoryform,'insert');\">  <br>				
				-->
				<table cellpadding=5 cellspacing=0 border=0 width=100%>
				<tr>
					<td width=120 nowrap>선택된 분류</td>
					<td width='290' id='selected_category' style='font-weight:bold;'  class=small nowrap>미선택 --> 왼쪽카테고리에서 추가하시고자 하는 카테고리를 선택해주세요<td>
				</tr>
				<tr>
					<td nowrap>하부분류명</td>
					<td nowrap><input type='text' name='sub_category' validation=true title='하부분류명'> <input type='checkbox' name='category_use' value=1> 사용<td>
				</tr>
				
				";
	
$Contents .="		<tr>
					<td colspan=2>".$help_text."</td>
				</tr>
				<!--tr>
					<td>좌측 카테고리이미지 추가</td>
					<td><input type='file' name='leftcategory_img'><td>
				</tr>
				<tr>
					<td>서브이미지 추가</td>
					<td><input type='file' name='sub_img'><td>
				</tr-->				
				<tr>
					<td colspan=2 align=right> <img src='../image/bt_category_add.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"SubCategorySave(document.subCategoryform,'insert');\"></td>
				</tr>
				</table>	
				</form>
				</div>
				<br>";

				
				
$Contents .= "					
			</td>
			<td style='padding-left:10px;'>
			</td>
		</tr>
		";
		
		
$Contents .= "			
		</table>		
		<iframe name='calcufrm' src='' width=0 height=0></iframe>";



$LO = new popLayOut;



$addScript = "
<script language='JavaScript' src='../webedit/webedit.js'></script>\n
<script language='JavaScript' src='../include/manager.js'></script>\n
<script language='JavaScript' src='../include/cTree.js'></script>\n
<script language='JavaScript' src='category.js'></script>\n
<script src='../include/rightmenu.js'></script>\n".$Script;

$Contents = "<div style='padding:20 0 0 20'>".$Contents."</div>";

$LO->addScript = $addScript;
$LO->OnloadFunction = "";
$LO->strContents = $Contents;
$LO->Navigation = "HOME > 디자인관리 > 디자인분류관리";
$LO->PrintLayOut();



$mstring ="
	<html>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
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
	$bbs_name = $mdb->dt[bbs_name];
	
	

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
		node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type','$bbs_name')\";
		rootnode.addNode(node$cid);\n\n";
	}else{
		$mstring .=  "
		node$cid.expanded = true;
		node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type','$bbs_name')\";
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
	$bbs_name = $mdb->dt[bbs_name];

	
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
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth, '$category_display_type','$bbs_name')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
		
	return $mstring;
}


?>