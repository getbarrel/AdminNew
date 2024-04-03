<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");

$db = new Database;


$Contents = "
<script>
/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;


/*	Create Root node	*/
	var rootnode = new TreeNode(\"견적카테고리\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('견적카테고리','000000000000000',-1)\";
	rootnode.expanded = true;
";


$db->query("SELECT * FROM ".TBL_SHOP_ESTIMATE_CATEGORY." where depth in(0,1,2,3)  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");


$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);

	if ($db->dt["depth"] == 0){
		$Contents = $Contents.PrintNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 1){
		$Contents = $Contents.PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 2){
		$Contents = $Contents.PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 3){
		$Contents = $Contents.PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 4){
		$Contents = $Contents.PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 5){
		$Contents = $Contents.PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}
}

$Contents = $Contents."
	tree.addNode(rootnode);
</script>


";


$Contents .= "

		<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr >
			<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("견적카테고리", "견적센터 > 견적카테고리 ")."</td>
		</tr>
		<!--tr><td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 견적 카테고리 관리</b></div>")."</td></tr-->
		<tr>
			<td width=300 valign=top>

			<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
			<tr><form name='category_order' method='get' action='categoryorder.php' target='calcufrm'>
				<td style='padding-left:10px;padding-top:3px;' valign=middle>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='mode' value=''>
				<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:pointer' alt='분류 위로 이동'>
				<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cursor:pointer' alt='분류 아래로 이동'>
				</td>
			</tr></form>
			<tr><form>
				<td width=200 height=400 valign=top style='overflow:auto;padding:0 10px 10px 10px;'>

				<div id=TREE_BAR style=\"width:200px;height:420px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
				<script>
				tree.draw();
				tree.nodes[0].select();
				</script>
				</div>

				</td>
			</tr></form>
			</table>

			</td >
			<td style='padding:20px;'>

			</td>
			<td valign=top width='100%'>
				<div class='tab' style='height:45px;'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('edit_category','tab_01')\" style='padding-left:20px;padding-right:20px;'>
									<!--input type='radio' name='category_mode' id='category_mode_edit' value='edit' onclick='CategoryMode(this.value)' checked><label for='category_mode_edit' style='font-weight:bold'>선택된 카테고리 수정</label-->
									선택된 카테고리 수정
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('add_subcategory','tab_02')\" style='padding-left:20px;padding-right:20px;'>
									<!--input type='radio' name='category_mode' id='category_mode_add' value='add' onclick='CategoryMode(this.value)' ><label for='category_mode_add' style='font-weight:bold'> 카테고리 추가</label-->
									카테고리 추가
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<!--table id='tab_03' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('recent_use_after','tab_03')\" style='padding-left:20px;padding-right:20px;'>최근사용후기</td>
								<th class='box_03'></th>
							</tr>
							</table-->
						</td>
						<td class='btn'>

						</td>
					</tr>
					</table>
				</div>
				<div id='edit_category' style='display:block;clear:both;width:100%;'>
				<form name='thisCategoryform' method=\"post\" enctype='multipart/form-data' action='category.save.php' target='calcufrm'>
				<input type='hidden' name='mode' value=''>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='category_top_view' value=''>
				<table width=100% cellpadding=0 cellspacing=0 class='search_table_box'>
				<tr>
					<td width=170 nowrap oncontextmenu='init2();return false;' class='leftmenu3 search_box_title' >  선택된 카테고리</td>
					<td width=* style='padding-left:5px;' nowrap><input type='text' name='this_category' class='textbox'> <input type='checkbox'  name='category_use' id='category_use_id' value=1 ><label for='category_use_id'> 카테고리사용</label></td>
				</tr>
				<!--tr>
					<td >접근권한</td>
					<td style='padding-left:10px;'>
					<input type='checkbox' name='auth' id='nonemember'><label for='nonemember'>비회원
					<input type='checkbox' name='auth' id='manager'><label for='manager'>관리자
					<input type='checkbox' name='auth' id='member1'><label for='member1'>정회원
					<input type='checkbox' name='auth' id='member2'><label for='member2'>준회원
					</td>
				</tr-->
				<tr bgcolor=#ffffff>
					<td class='leftmenu3 search_box_title' >  카테고리이미지 추가</td>
					<td class='search_box_item' style='padding-left:5px;'><input type='file' class='textbox' name='category_img' style='padding:3px;'></td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='leftmenu3 search_box_title' >  좌측카테고리이미지 추가</td>
					<td class='search_box_item' style='padding-left:5px;'><input type='file' class='textbox' name='leftcategory_img' style='padding:3px;'></td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='leftmenu3 search_box_title' >  서브이미지 추가</td>
					<td class='search_box_item' style='padding-left:5px;'><input type='file' class='textbox' name='sub_img' style='padding:3px;'></td>
				</tr>
				</table>
				<table cellpadding=5 cellspacing=1 width=100% >
				<tr>
					<td colspan=2 align=right> <input type='checkbox' name='sub_cartegory_delete' id='sub_cartegory_delete_id'value='1' > <label for='sub_cartegory_delete_id'>하부카테고리 모두삭제</label> <img src='../images/".$admininfo["language"]."/bt_category_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"CategorySave(document.thisCategoryform,'del');\"> <img src='../images/".$admininfo["language"]."/bt_category_modify.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"CategorySave(document.thisCategoryform,'modify');\"> </td>
				</tr>
				</table>
				</form>
				<!--div style='display:none' id='category_top_view_area'></div-->
				</div>

				<div id='add_subcategory' style='display:none;clear:both;width:100%;'>
				<form name='subCategoryform' method=\"post\" enctype='multipart/form-data' action='category.save.php' target='calcufrm'>
				<input type='hidden' name='mode' value=''>
				<input type='hidden' name='sub_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_cid' value=''>
				<!--하부카테고리 추가<br>
				<input type='' name='sub_category'> <img src='../image/bt_ok.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"SubCategorySave(document.subCategoryform,'insert');\">  <br>
				-->
				<table cellpadding=0 width=100% cellspacing=0 class='search_table_box'>
				<tr>
					<td width=170 nowrap class='leftmenu3 search_box_title' >  선택된 카테고리</td>
					<td class='search_box_item' width='*' id='selected_category' style='padding-left:5px;font-weight:bold;font-size:11px;letter-spacing:-1px;' nowrap>미선택 --> 왼쪽카테고리에서 추가하시고자 하는 카테고리를 선택해주세요</td>
				</tr>
				<tr>
					<td nowrap class='leftmenu3 search_box_title' >  하부카테고리 추가</td>
					<td class='search_box_item' style='padding-left:5px;' nowrap><input type='text' name='sub_category' class='textbox'> <input type='checkbox' name='category_use' value=1> 카테고리사용</td>
				</tr>
				<tr>
					<td class='leftmenu3 search_box_title' >  카테고리이미지 추가</td>
					<td class='search_box_item' style='padding-left:5px;'><input type='file' class='textbox' name='category_img' style='padding:3px;'></td>
				</tr>
				<tr>
					<td class='leftmenu3 search_box_title' >  좌측 카테고리이미지 추가</td>
					<td class='search_box_item' style='padding-left:5px;'><input type='file' class='textbox' name='leftcategory_img' style='padding:3px;'></td>
				</tr>
				<tr>
					<td class='leftmenu3 search_box_title' >  서브이미지 추가</td>
					<td class='search_box_item' style='padding-left:5px;'><input type='file' class='textbox' name='sub_img' style='padding:3px;'></td>
				</tr>
				</table>
				<table cellpadding=5 cellspacing=1 width=100% >
				<tr>
					<td colspan=2 align=right> <img src='../images/".$admininfo["language"]."/bt_category_add.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"SubCategorySave(document.subCategoryform,'insert');\"></td>
				</tr>
				</table>
				</form>
				</div>
				<br>



			</td>
			<td style='padding-left:10px;'>
			</td>
		</tr>
		<tr height=30>
			<td colspan=2>

			</td>
		</tr>";

		$help = "
				<img src='../image/emo_3_15.gif' border=0 align=absmiddle> <b>카테고리 추가 :</b> <br>
				상단에 <input type='radio' name='category_mode1' id='category_mode_add1' value='add' ><label for='category_mode_add1' style='font-weight:bold'> 카테고리 추가</label> 를 선택하신후 좌측 상품 카테고리에서 추가하기 원하는 카테고리를  선택하신후  <b>카테고리 추가</b> <input type='text' name='sub_category1'> 에 카테고리를 입력하신후 <img src='../image/bt_category_add.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.<br><br>

				<img src='../image/emo_3_15.gif' border=0 align=absmiddle> <b>카테고리 수정 :</b> <br>
				상단에 <input type='radio' name='category_mode1' id='category_mode_add1' value='add' ><label for='category_mode_add1' style='font-weight:bold'> 선택된 카테고리 수정 </label> 를 선택하신후 좌측 상품 카테고리에서 추가하기 원하는 카테고리를  선택하신후  <b>선택된 카테고리 </b> <input type='text' name='sub_category1'> 에 카테고리를 입력하신후 <img src='../image/bt_category_modify.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.<br><br>

				<img src='../image/emo_3_15.gif' border=0 align=absmiddle> <b>카테고리 삭제 :</b> <br>
				상단에 <input type='radio' name='category_mode1' id='category_mode_add1' value='add' ><label for='category_mode_add1' style='font-weight:bold'> 선택된 카테고리 수정 </label> 를 선택하신후 좌측 상품 카테고리에서 추가하기 원하는 카테고리를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.<br>
				하부카테고리를 포함해서 삭제하고 싶으신경우에는 <input type='checkbox' name='sub_cartegory_delete1' id='sub_cartegory_delete_id1'value='1' > <label for='sub_cartegory_delete_id1' style='font-weight:bold'>하부카테고리 모두삭제</label> 를 선택한후 <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.
				<br><br>
				<!--
				카테고리 사용유무<br>
				<input type=radio name='y'>사용 <input type=radio name='n'>미사용<br><br>
				카테고리이미지 추가<br>
				<input type=file name='category_img'><br><br>
				서브이미지 추가<br>
				<input type=file name='sub_img'><br><br>
				-->";
$Contents .= "
		<tr><td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:20px;'><img src='../image/ic_help.gif' align=absmiddle> <b style='color:0074ba'>카테고리 관리하기</b><br><br>$help</div>")."</td></tr>
		</table>
		<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0 style='display:none;'></iframe>";


$P = new LayOut;
$addScript =
"
<script language='JavaScript' src='../include/manager.js'></script>\n
<script language='JavaScript' src='../include/cTree.js'></script>\n
<script language='JavaScript' src='category.js'></script>\n
<script src='../include/rightmenu.js'></script>\n".$Script;
$P->addScript = $addScript;
$P->OnloadFunction = "";
$P->strLeftMenu = estimate_menu();
$P->strContents = $Contents;
$P->Navigation = "견적센타 > 견적카테고리 관리";
$P->title = "견적카테고리 관리";
$P->PrintLayOut();


//echo $db->total;
function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);

	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($cname,$cid,$depth)
{
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname = str_replace("\"","&quot;",$cname);

	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('$cname','$cid',$depth)\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$mcid,$depth)
{
//	global $cid;
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

	if ($cid == $mcid){
		$expandstring = "true";
	}else{
		$expandstring = "false";
	}

	$cname = str_replace("\"","&quot;",$cname);

	return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
	//	groupnode$cid.expanded = $expandstring;
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth)\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}


?>