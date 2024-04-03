<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/category.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");

$db = new Database;



$Contents .= "

		<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr><td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 카테고리 디자인 관리</b></div>")."</td></tr>
		<tr>
			<td width=300 valign=top >	
			<table cellpadding=0 cellspacing=0>
			<tr><td><input type=radio name='category_design' value='design1.htm'> 카테고리 디자인 1</td></tr>		
			<tr>
				<td onclick='return false'>
				".makeCategoryByTemplet($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete], "design1.htm")."
				</td>
			</table>
			</td >
			<td style='padding-left:20px;' valign=top>
			<table cellpadding=0 cellspacing=0>
			<tr><td><input type=radio name='category_design' value='design2.htm'> 카테고리 디자인 2</td></tr>		
			<tr>
				<td onclick='return false'>
				".makeCategoryByTemplet($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete], "design2.htm")."
				</td>
			</table>
			</td>
			<td valign=top width='100%'>	
			".LeftTextMenu_style1()."
			</td>
			<td style='padding-left:10px;'>
			</td>
		</tr>
		<tr>
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
		<iframe name='calcufrm' src='' width=0 height=0></iframe>";


$LO = new LayOut;
$addScript = 
"
<script language='JavaScript' src='../webedit/webedit.js'></script>\n
<script language='JavaScript' src='../include/manager.js'></script>\n
<script language='JavaScript' src='../include/cTree.js'></script>\n
<script language='JavaScript' src='category.js'></script>\n
<script src='../include/rightmenu.js'></script>\n".$Script;
$LO->addScript = $addScript;
$LO->OnloadFunction = ""; //showSubMenuLayer('storeleft');
$LO->title = "";//text_button('#', " ♣ 카테고리 구성");
$LO->strLeftMenu = product_menu();
$LO->strContents = $Contents;
$LO->PrintLayOut();

echo "
<SCRIPT type=text/javascript>
<!--//
// 메뉴의 좌측에 들어갈 타이틀을 입력하세요
eyesys_title='LCS'
// 타이틀에 그라데이션 효과를 보여줄 배경색을 설정 하세요
eyesys_titlecol1='white' // 아래쪽 색상
eyesys_titlecol2='white' // 위쪽 색상
// 타이틀의 글자 색상
eyesys_titletext='gray'
// 메뉴와 각 항목의 배경색
eyesys_bg='#ffffff'
// 마우스를 대었을때의 배경색
eyesys_bgov='#006699'
// 메뉴의 색상
eyesys_cl='#000000'
// 마우스 오버시 메뉴 색상
eyesys_clov='white'
// 메뉴의 가로크기
eyesys_width=123
eyesys_init()

// 아래 방법으로 메뉴를 설정 합니다
// eyesys_item(제목,아이콘,URL)
// 아이콘을 사용하지 않으려면 null 을 입력 합니다
//Eyesys_item('Copy (ctrl+C)',null,'JavaScript:parent.CopySpread();parent.sysmen.hide();');
//Eyesys_item('Paste (ctrl+v)',null,'JavaScript:parent.PasteSpread();parent.sysmen.hide();');
//Eyesys_item('Erase (delete)',null,'javascript:parent.EraseSpread();parent.sysmen.hide();');
//Eyesys_item('Delete line',null,'');
//Eyesys_item('All clear',null,'');


eyesys_close()

//document.write ('<pre>'+strContextMenu+'</pre>');

//-->
</SCRIPT>";

//echo $db->total;
function PrintRootNode($cname){
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


	return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
	//	groupnode$cid.expanded = $expandstring;
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth)\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}


?>