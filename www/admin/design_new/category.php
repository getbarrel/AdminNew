<?php
	include("../class/layout.class");
	include("../webedit/webedit.lib.php");
	include("../class/LayoutXml/LayoutXml.class");
	//include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
	//include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");
	
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
	
// 	if($admin_config[mall_page_type] == "MI"){
// 		$sql = "SELECT * FROM ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth in(0,1,2,3)  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
// 	}else{
// 		if($admininfo[mall_type] == "H"){
// 			$sql = "SELECT * FROM ".TBL_SHOP_LAYOUT_INFO." where depth in(0,1,2,3) and cid NOT LIKE '001%' and cid NOT LIKE '006%' and cid NOT LIKE '012%' and cid NOT LIKE '011%' and cid NOT LIKE '015%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";
// 		}else{
// 			$sql = "SELECT * FROM ".TBL_SHOP_LAYOUT_INFO." where depth in(0,1,2,3)  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";
// 		}
// 		//$sql = "SELECT * FROM ".TBL_SHOP_LAYOUT_INFO." where depth in(0,1,2,3)  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";
// 	}
	////////////////////////////////
	$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml";
	$layoutXml = new LayoutXml($layoutXmlPath);
	$layouts = $layoutXml->simpleXml->xpath("/layouts/layout");
	$total = count($layoutXml->layouts);
	xsort($layoutXml->layouts, 'vlevelf', SORT_ASC);
	
	
	for ($i = 0; $i < $total; $i++)
	{
		if(($layoutXml->layouts[$i]->pcode != "basic")){
			if ($layoutXml->layouts[$i]->depth == 0){
				$Contents = $Contents.PrintNode($layoutXml->layouts[$i]);
			}else {
				$Contents = $Contents.PrintGroupNode($layoutXml->layouts[$i]);
			}
		}
	}
	/////////////////////////////////
// 	$db->query($sql);
	
	
// 	$total = $db->total;
// 	for ($i = 0; $i < $db->total; $i++)
// 	{
	
// 		$db->fetch($i);
	
// 		if ($db->dt["depth"] == 0){
// 			$Contents = $Contents.PrintNode($db);
// 		}else if($db->dt["depth"] == 1){
// 			$Contents = $Contents.PrintGroupNode($db);
// 		}else if($db->dt["depth"] == 2){
// 			$Contents = $Contents.PrintGroupNode($db);
// 		}else if($db->dt["depth"] == 3){
// 			$Contents = $Contents.PrintGroupNode($db);
// 		}else if($db->dt["depth"] == 4){
// 			$Contents = $Contents.PrintGroupNode($db);
// 		}else if($db->dt["depth"] == 5){
// 			$Contents = $Contents.PrintGroupNode($db);
// 		}
// 	}
	
	$Contents = $Contents."
		tree.addNode(rootnode);
	
	</script>
	
	
	";
	
	
	$Contents .= "
	<table class='layer_box' border=0 cellpadding=0 cellspacing=0 style='width:850px;height:466px;display:block;' >
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
			<td class='box_05' rowspan=2 valign=top style='padding:15px 15px 5px 5px;font-size:12px;line-height:150%;text-align:left;' >
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
	
					<div id=TREE_BAR style=\"width:200px;height:320px;overflow:auto;margin:5px;\">
					<script>
					tree.draw();
					tree.nodes[0].select();
					//alert(document.getElementById('TREE_BAR').innerHTML);
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
							<input type='text' class='textbox' name='this_category' validation=true title='선택된 분류'>
							<input type='checkbox' name='category_use' id='category_use_id' value=1 ><label for='category_use_id'> 사용</label>
							<td>
					</tr>
					<tr>
						<td width=170 nowrap >파일경로</td>
						<td width=100% nowrap>
							<input type='text' class='textbox' name='path' validation=true urltype=true style='ime-mode:disabled;' title='파일경로'>
							<td>
					</tr>
					<tr>
						<td width=170 nowrap >기본링크 (표시URL)</td>
						<td width=100% nowrap>
						<table cellpadding=0 cellspacing=0>
							<tr>
								<td><input type='text' class='textbox' name='basic_link' urltype=true style='ime-mode:disabled;' size=30 title='기본링크'></td>
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
						<td width=170 nowrap oncontextmenu='init2();return false;'>분류 타입</td>
						<td width=100% nowrap>
							<input type='radio' name='category_display_type' id='category_display_type_folder' value='F' onclick=\"document.getElementById('bbs_select').style.display='none'; \"><label for='category_display_type_folder'>분류(폴더)</label>
							<input type='radio' name='category_display_type' id='category_display_type_html' value='P' onclick=\"document.getElementById('bbs_select').style.display='none'; \"><label for='category_display_type_html'> 페이지(html)</label>
							<input type='radio' name='category_display_type' id='category_display_type_bbs' value='B' onclick=\"document.getElementById('bbs_select').style.display='block'; \"><label for='category_display_type_bbs'> 게시판</label>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=small></span>
						<td>
					</tr>
					<tr>
						<td width=170 nowrap oncontextmenu='init2();return false;'>전체레이아웃 관리적용</td>
						<td width=100% nowrap>
							<input type='checkbox' name='is_layout_apply' id='is_layout_apply_Y' value='Y' ><label for='is_layout_apply_Y'> 적용</label>
						<td>
					</tr>
					<tr id='bbs_select' style='display:none;'>
						<td >게시판 선택 </td>
						<td>".BoardSelect($page_name)."<td>
					</tr>
					<!--tr>
						<td >접근권한</td>
						<td>
						<input type='checkbox' name='auth' id='nonemember'><label for='nonemember'>비회원
						<input type='checkbox' name='auth' id='manager'><label for='manager'>관리자
						<input type='checkbox' name='auth' id='member1'><label for='member1'>정회원
						<input type='checkbox' name='auth' id='member2'><label for='member2'>준회원
						<td>
					</tr-->
					<!--tr>
						<td >좌측카테고리이미지 추가</td>
						<td><input type='file' name='leftcategory_img'><td>
					</tr>
					<tr>
						<td >서브이미지 추가</td>
						<td><input type='file' name='sub_img'><td>
					</tr-->
	
					<tr>
						<td colspan=2 align=right> ";
	
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
	$Contents .= "		<input type='checkbox' name='sub_cartegory_delete' id='sub_cartegory_delete_id'value='1' > <label for='sub_cartegory_delete_id'>하부카테고리 모두삭제</label>
						<img src='../images/".$admininfo["language"]."/bt_category_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"CategorySave(document.thisCategoryform,'del');\">";
	}
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents .= "
						<img src='../images/".$admininfo["language"]."/bt_category_modify.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"CategorySave(document.thisCategoryform,'modify');\">";
	}
	
	$Contents .= "		<!--<img src='../image/bt_category_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"CategorySave(document.thisCategoryform,'del');\">
						<img src='../image/bt_category_modify.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"CategorySave(document.thisCategoryform,'modify');\"> --></td>
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
	<table cellpadding=2 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>카테고리 수정</b> : 수정하고자 하는 카테고리를 먼저 선택하신후 내용을 수정하신후 카테고리 수정 버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>카테고리 추가</b> : 추가를 원하시는  카테고리를 먼저 선택하신후 상단에 분류및 페이지 추가 버튼을 클릭하신후 정보입력후 카테고리 추가  버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >카테고리 구성정보를 입력하시면 기본레이아웃을 바탕으로 레이이아웃 기본정보가 입력되며 팔일또한 생성됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>기본링크 (표시URL)</b> 에  추가하신 페이지에서 사용하고자 하는 URL을 입력하세요 예) /page.php </td></tr>
	</table>
	";
	
		$help_text = HelpBox("디자인분류관리", $help_text);
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
					<input type='text' name='sub_cid' value=''>
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
						<td nowrap><input type='text' class='textbox' name='sub_category' validation=true title='하부분류명'> <input type='checkbox' name='category_use' value=1> 사용<td>
					</tr>
					<tr>
						<td nowrap >파일경로</td>
						<td  nowrap>
							<input type='text' class='textbox' name='path' validation=true urltype=true style='ime-mode:disabled;' title='파일경로'>
							<td>
					</tr>
					<tr>
						<td nowrap oncontextmenu='init2();return false;'>분류 타입</td>
						<td nowrap>
							<input type='radio' name='category_display_type' id='category_display_type_folder' value='F' onclick=\"document.getElementById('bbs_select2').style.display='none'; \" checked><label for='category_display_type_folder'>분류(폴더)</label>
							<input type='radio' name='category_display_type' id='category_display_type_html' value='P' onclick=\"document.getElementById('bbs_select2').style.display='none'; \"><label for='category_display_type_html'> 페이지(html)</label>
							<input type='radio' name='category_display_type' id='category_display_type_bbs' value='B' onclick=\"document.getElementById('bbs_select2').style.display='block'; \"><label for='category_display_type_bbs'> 게시판</label>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=small></span>
						<td>
					</tr>
					<tr id='bbs_select2' style='display:none;'>
						<td >게시판 선택 </td>
						<td>".BoardSelect($page_name)."<td>
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
						<td colspan=2 align=right> <img src='../images/".$admininfo["language"]."/bt_category_add.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"SubCategorySave(document.subCategoryform,'insert');\"></td>
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
	</table>";
	
	
	
	$P = new popLayOut;
	
	
	
	$addScript = "
	<script language='JavaScript' src='../webedit/webedit.js'></script>\n
	<script language='JavaScript' src='../include/manager.js'></script>\n
	<script language='JavaScript' src='../include/cTree.js'></script>\n
	<script language='JavaScript' src='category.js'></script>\n
	<script src='../include/rightmenu.js'></script>\n".$Script;
	
	$Contents = "<div style='padding:20 0 0 20'>".$Contents."</div>";
	
	$P->addScript = $addScript;
	$P->OnloadFunction = "";
	$P->strContents = $Contents;
	$P->Navigation = "디자인관리 > 디자인분류관리";
// 	$P->Navigation = "디자인관리 > 디자인분류관리";
	$P->PrintLayOut();
	
	
	
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

	function PrintRootNode($cname){
	
		$cname = str_replace("\"","&quot;",$cname);
		$cname = str_replace("'","\'",$cname);
	
		$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
		rootnode.expanded = true;\n\n";
	
		return $vPrintRootNode;
	}
	
	//echo $db->total;
// 	function PrintRootNode($cname){
	
// 		$cname = str_replace("\"","&quot;",$cname);
	
// 		$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
// 				rootnode.expanded = true;\n\n";
	
// 		return $vPrintRootNode;
// 	}

	function PrintNode($layout){
		global $selected_cid;
		$cname = $layout->cname;
		$cid = $layout->cid;
		$depth = $layout->depth;
		$category_display_type = $layout->category_display_type;
	
		$cid1 = substr($cid,0,3);
		$cid2 = substr($cid,3,3);
		$cid3 = substr($cid,6,3);
		$cid4 = substr($cid,9,3);
		$cid5 = substr($cid,12,3);
	
		$cname = str_replace("\"","&quot;",$cname);
		$cname = str_replace("'","\'",$cname);
	
		//	if ($cid == $mcid){
		//		$expandstring = "true";
		//	}else{
		//		$expandstring = "false";
		//	}
	
		//	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	
		if($layout->category_display_type == "P"){
			$mstring =  "		var node$cid = new TreeNode('$cname ', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
		}else if($layout->category_display_type == "B"){
			$mstring =  "		var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_BBS.gif', '../resources/Common_TreeNode_BBS.gif');\n";
		}else if($layout->category_display_type == "F"){
			$mstring =  "		var node$cid = new TreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
		}else{
			$mstring =  "		var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');\n";
		}
	
		$cname = str_replace("'","\'",$cname);
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
	
// 	function PrintNode($mdb){
// 		global $selected_cid;
// 		$cname = $mdb->dt[cname];
// 		$cid = $mdb->dt[cid];
// 		$depth = $mdb->dt[depth];
// 		$bbs_name = $mdb->dt[bbs_name];
// 		$category_display_type = $mdb->dt[category_display_type];
	
	
	
// 		$cid1 = substr($cid,0,3);
// 		$cid2 = substr($cid,3,3);
// 		$cid3 = substr($cid,6,3);
// 		$cid4 = substr($cid,9,3);
// 		$cid5 = substr($cid,12,3);
	
// 		$cname = str_replace("\"","&quot;",$cname);
	
// 	//	if ($cid == $mcid){
// 	//		$expandstring = "true";
// 	//	}else{
// 	//		$expandstring = "false";
// 	//	}
	
// 	//	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	
// 		if($mdb->dt[category_display_type] == "P"){
// 			$mstring =  "		var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
// 		}else if($mdb->dt[category_display_type] == "F"){
// 			$mstring =  "		var node$cid = new TreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
// 		}else{
// 			$mstring =  "		var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');\n";
// 		}
	
	
// 		if(substr($selected_cid,0,($depth+1)*3) == substr($cid,0,($depth+1)*3)){
// 			$mstring .=  "
// 			node$cid.expanded = true;
// 			node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type','$bbs_name')\";
// 			rootnode.addNode(node$cid);\n\n";
// 		}else{
// 			$mstring .=  "
// 			node$cid.expanded = false;
// 			node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type','$bbs_name')\";
// 			rootnode.addNode(node$cid);\n\n";
// 		}
	
// 		return $mstring;
// 	}
	
	function PrintGroupNode($layout)
	{
		$cname = $layout->cname;
		$mcid = $layout->cid;
		// 		echo("cid  : " . $mcid);
		// 		echo("<br />");
		$depth = $layout->depth;
		$category_display_type = $layout->category_display_type;
	
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
		$cname = str_replace("'","\'",$cname);
	
		if($layout->category_display_type == "P"){
			$mstring =  "		var groupnode$mcid = new TreeNode('$cname', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
		}else if($layout->category_display_type == "B"){
			$mstring =  "		var groupnode$mcid = new TreeNode('$cname', '../resources/Common_TreeNode_BBS.gif', '../resources/Common_TreeNode_BBS.gif');\n";
		}else{
			$mstring =  "		var groupnode$mcid = new TreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
		}
	
		if ($mcid == $cid){
			$mstring .=  "	groupnode$cid.expanded = true;\n";
			//	$mstring .=  "	groupnode$cid.select = true;\n";
		}
	
		$cname = str_replace("'","\'",$cname);
		$mstring .=  "	groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth, '$category_display_type')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
			
		return $mstring;
	}
	
	
// 	function PrintGroupNode($mdb)
// 	{
// 		$cname = $mdb->dt[cname];
// 		$mcid = $mdb->dt[cid];
// 		$depth = $mdb->dt[depth];
// 		$category_display_type = $mdb->dt[category_display_type];
// 		$bbs_name = $mdb->dt[bbs_name];
	
	
// 		global $cid;
// 		$cid1 = substr($mcid,0,3);
// 		$cid2 = substr($mcid,3,3);
// 		$cid3 = substr($mcid,6,3);
// 		$cid4 = substr($mcid,9,3);
// 		$cid5 = substr($mcid,12,3);
	
// 		$Parentdepth = $depth - 1;
	
// 		if ($depth+1 == 1){
// 			$cid1 = "000";
// 		}else if($depth+1 == 2){
// 			$cid2 = "000";
// 		}else if($depth+1 == 3){
// 			$cid3 = "000";
// 		}else if($depth+1 == 4){
// 			$cid4 = "000";
// 		}else if($depth+1 == 5){
// 			$cid5 = "000";
// 		}
	
// 		$parent_cid = "$cid1$cid2$cid3$cid4$cid5";
	
// 		if ($depth ==1){
// 			$ParentNodeCode = "node$parent_cid";
// 		}else if($depth ==2){
// 			$ParentNodeCode = "groupnode$parent_cid";
// 		}else if($depth ==3){
// 			$ParentNodeCode = "groupnode$parent_cid";
// 		}else if($depth ==4){
// 			$ParentNodeCode = "groupnode$parent_cid";
// 		}else if($depth ==5){
// 			$ParentNodeCode = "groupnode$parent_cid";
// 		}
	
	
	
// 		$cname = str_replace("\"","&quot;",$cname);
// 		if($mdb->dt[category_display_type] == "P"){
// 			$mstring =  "		var groupnode$mcid = new TreeNode('$cname', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
// 		}else if($mdb->dt[category_display_type] == "B"){
// 			$mstring =  "		var groupnode$mcid = new TreeNode('$cname', '../resources/Common_TreeNode_BBS.gif', '../resources/Common_TreeNode_BBS.gif');\n";
// 		}else{
// 			$mstring =  "		var groupnode$mcid = new TreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
// 		}
	
// 		if ($mcid == $cid){
// 			$mstring .=  "	groupnode$cid.expanded = true;\n";
// 		//	$mstring .=  "	groupnode$cid.select = true;\n";
// 		}
	
	
// 		$mstring .=  "	groupnode$mcid.tooltip = '$cname';
// 			groupnode$mcid.id ='nodeid$mcid';
// 			groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth, '$category_display_type','$bbs_name')\";
// 			$ParentNodeCode.addNode(groupnode$mcid);\n\n";
	
// 		return $mstring;
// 	}


?>
