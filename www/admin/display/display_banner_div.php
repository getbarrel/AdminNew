<?
///////////////////////////////////////////////////////////////////
//
// 제목 : 전시관리 > 배너관리 > 배너분류관리 - 이현우(2013-05-20)
//
///////////////////////////////////////////////////////////////////
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");
include_once("../display/display.lib.php");


$db = new Database;
$banner_div = $_GET["banner_div"];

$category = "
<script  id='dynamic'></script>
<script>
/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;


/*	Create Root node	*/
	var rootnode = new TreeNode(\"배너분류\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('배너분류','000000000000000',-1,'', 1, 1)\";
	rootnode.expanded = true;
";


$db->query("SELECT * FROM ".TBL_SHOP_BANNER_DIV." where depth in(0,1)");
$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{
	$db->fetch($i);
	if ($db->dt["depth"] == 0){
		$category = $category.PrintNode($db);	
	}else if($db->dt["depth"] == 1){
		$category = $category.PrintGroupNode($db);
	}	
}
$disp_radio = makeRadioTag($arr_display_div_disp, "disp", $div_disp);
$menu_disp_radio = makeRadioTag($arr_display_div_disp, "menu_disp", $div_menu_disp);

$category = $category."
	tree.addNode(rootnode);
	tree.draw();
	tree.nodes[0].select();
</script>";


$Contents = "
<script language='JavaScript' src='../include/cTree.js'></script>
<script language='JavaScript' src='display.js'></script>
<script language='JavaScript' src='../include/manager.js'></script>
<style>
.textbox_noneBorder {border:0;}
</style>
		<table cellpadding=0 cellspacing=0 border=0 width='100%'>

		<tr>
		    <td align='left' colspan=3> ".GetTitleNavigation("분류설정", "전시관리 > 분류설정")."</td>
		</tr>
		<tr>
			<td valign=top width=236>
			<div id=TREE_BAR >
				<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 width=100% style='border:3px solid #d8d8d8'>
				<tr>
					<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle nowrap>
						<form name='category_order' method='get' action='categoryorder.php' target='calcufrm'>
						<input type='hidden' name='this_depth' value=''>
						<input type='hidden' name='div_ix' value=''>
						<input type='hidden' name='mode' value=''>
						<input type='hidden' name='view' value=''><!--innerview-->						
						<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
						<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cursor:hand' alt='분류 아래로 이동' align=absmiddle>
						</td>
						<td width=190 valign=middle>
						<span class=small><!--분류선택후 이동버튼 클릭--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
						</form>
					</td>
				</tr>
				<tr><form>
					<td colspan=2 width=200 height=400 valign=top style='overflow:auto;padding:0 10px 10px 10px;'>
					<div style=\"width:200px;height:418px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
					".$category."
					</div>
					</td>
				</tr></form>
				</table>
			</div>
			</td >
			<td width='*' style='padding:5px;'>
			</td>


			<!-- 분류 수정 -->
			<td valign=top width='100%' align='left' style=''>
				<div class='tab' style='width:100%;height:38px;margin:0px;'>
					<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('edit_category','tab_01')\" style='padding-left:20px;padding-right:20px;'>
									<!--input type='radio' name='category_mode' id='category_mode_edit' value='edit' onclick='CategoryMode(this.value)' checked><label for='category_mode_edit' style='font-weight:bold'>선택된 분류 수정</label-->
									선택된 분류 수정
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('add_subcategory','tab_02')\" style='padding-left:20px;padding-right:20px;'>
									<!--input type='radio' name='category_mode' id='category_mode_add' value='add' onclick='CategoryMode(this.value)' ><label for='category_mode_add' style='font-weight:bold'> 분류 추가</label-->
									분류 추가
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<!--table id='tab_03' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('input_addfield','tab_03')\">분류별 부가정보 필드 정의</td>
								<th class='box_03'></th>
							</tr>
							</table-->
						</td>
						<td class='btn'>

						</td>
					</tr>
					</table>
				</div>

				<div id='edit_category' style='display:block;'>
				<form name='thisCategoryform' method=\"post\"  action='display_banner_div.save.php' target='calcufrm' style='display:inline;'>
				<input type='hidden' name='mode' value='update'>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='div_ix' value=''>
				<input type='hidden' name='banner_div' value='".$banner_div."'>				
				<table cellpadding=0 cellspacing=0 width=97% class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>위치</b></td>
					<td class='input_box_item' nowrap>
						<input type='text' class='textbox_noneBorder' name='this_category' maxlength=100 validation=true title='선택된 분류'>
					</td>
				</tr>				
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류명 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						<input type='text' name='div_name' class='textbox'>
					</td>
				</tr>				
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류 사용  <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						".$disp_radio."
						&nbsp;
					</td>
				</tr>	
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>메뉴 노출  <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						".$menu_disp_radio."
						&nbsp;<div class=small style='padding:4px 0px'>사용시 좌측 '전시관리'에 메뉴가 노출되어 편리하게 관리할 수 있음<br>대분류만 사용가능 합니다.</div>
					</td>
				</tr>	
				<tr>
					<td colspan=2 align=center bgcolor='#ffffff' height='40'> <input type=image src='../image/b_save.gif' border=0 align=absmiddle ></td>
				</tr>
				</table>
				</form>
				</div>
				
				<!-- 분류 추가 -->
				<div id='add_subcategory' style='display:none;'>
				<form name='subCategoryform' method=\"post\"  action='display_banner_div.save.php' target='calcufrm' style='display:inline;'>
				<input type='hidden' name='mode' value='insert'>
				<input type='hidden' name='sub_depth' value=''>
				<input type='hidden' name='div_ix' value=''>
				<input type='hidden' name='parent_div_ix' value=''>
				<input type='hidden' name='banner_div' value='".$banner_div."'>
				<table cellpadding=0 cellspacing=0 border=0 width=97% class='input_table_box'>
				<col width='20%'>
				<col width='*'>
			 			
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류명 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						<input type='text' name='div_name' class='textbox'>
					</td>
				</tr>				
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류 사용  <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						<input type='radio' name='disp' value='1' checked><label for=''> 사용</label>
						<input type='radio' name='disp' value='0' ><label for=''> 미사용</label>
						&nbsp;
					</td>
				</tr>	
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>메뉴 노출  <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						<input type='radio' name='menu_disp' value='1' ><label for=''> 사용</label>
						<input type='radio' name='menu_disp' value='0' checked><label for=''> 미사용</label>
						&nbsp;<div class=small style='padding:4px 0px'>사용시 좌측 '전시관리'에 메뉴가 노출되어 편리하게 관리할 수 있음<br>대분류만 사용가능 합니다.</div>
					</td>
				</tr>	
				<tr>
					<td colspan=2 align=center bgcolor='#ffffff' height='40'> <input type=image src='../image/b_save.gif' border=0 align=absmiddle ></td>
				</tr>
					</table>
				</form>
				</div>
			</td>
		</tr>
		";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= "
		<tr height=10>
			<td colspan=3>
			".HelpBox("분류 관리 ", $help_text)."
			</td>
		</tr>
		</table>
		<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0 ></iframe>";


if($view == "innerview"){
	$P = new popLayOut;
	$addScript = "
	<script src='../include/rightmenu.js'></script>\n".$Script;
	$P->addScript = $addScript; /**/
	$P->OnloadFunction = "";
	$P->title = "";//text_button('#', " ♣ 분류 구성");
	$P->strLeftMenu = "";
	$Contents ="
		<div id='category_area'>
			<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
				<tr>
					<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle>
						<form name='category_order' method='get' action='categoryorder.php' target='calcufrm'>
						<input type='hidden' name='this_depth' value=''>
						<input type='hidden' name='div_ix' value=''>
						<input type='hidden' name='mode' value=''>
						<input type='hidden' name='view' value='innerview'>
						<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
						<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cursor:hand' alt='분류 아래로 이동' align=absmiddle>
						<span class=small>분류선택후 이동버튼 클릭</span>
						</form>
					</td>
				</tr>
				<tr>
					<td width=200 height=400 valign=top style='overflow:auto;padding:0 10 10 10;'>
					<form>
						<div style=\"width:200px;height:420px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
						$category
						</div>
					</form>
					</td>
				</tr>
				</table>
		</div>

		<script>alert(document.getElementById('category_area').innerHTML);parent.document.getElementById('TREE_BAR').innerHTML = document.getElementById('category_area').innerHTML;</script>";

	$P->strContents = $Contents;
	$P->Navigation = "";
	$P->PrintLayOut();
	exit;
}else{

$P = new LayOut;
$addScript = "
<script src='../include/rightmenu.js'></script>\n".$Script;
$P->addScript = $addScript; /**/
$P->title = "";//text_button('#', " ♣ 분류 구성");
$P->strLeftMenu = display_menu();


$P->strContents = $Contents;
$P->Navigation = "프로모션/전시 > ".getMenuPath($db,$_SERVER["REQUEST_URI"]);
$P->title = getMenuPath($db,$_SERVER["REQUEST_URI"]);
$P->PrintLayOut();
}
/*
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



$mstring ="
	<html>
	<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
	<body>
	<div id='category_top_view_area'>
	".$db->dt[category_top_view]."
	</div>
	</body>
	</html>
	<script>
	parent.document.forms['thisCategoryform'].category_use.checked = $category_use;
	parent.iView.document.body.innerHTML = document.getElementById('category_top_view_area').innerHTML;
	</script>";
*/
//echo $db->total;
function PrintRootNode($div_name){

	$div_name = str_replace("\"","&quot;",$div_name);
	$div_name = str_replace("'","&#39;",$div_name);

	$vPrintRootNode = "var rootnode = new TreeNode('$div_name', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($mdb){

	$div_name = $mdb->dt[div_name];
	$div_ix				= $mdb->dt[div_ix];
	$depth				= $mdb->dt[depth];
	$div_disp			= $mdb->dt["disp"];
	$div_menu_disp	= $mdb->dt["menu_disp"];
 
	$div_name = str_replace("\"","&quot;",$div_name);
	$div_name = str_replace("'","&#39;",$div_name);

//	if ($div_ix == $mcid){
//		$expandstring = "true";
//	}else{
//		$expandstring = "false";
//	}

	return "	var node$div_ix = new TreeNode('$div_name', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$div_ix.expanded = true;
	node$div_ix.action = \"setCategory('$div_name','$div_ix',$depth, $div_disp, $div_menu_disp)\";
	rootnode.addNode(node$div_ix);\n\n";
}

function PrintGroupNode($mdb)
{
	$div_name = $mdb->dt[div_name];
	$mcid				= $mdb->dt[div_ix];
	$depth				= $mdb->dt[depth];
	$div_disp			= $mdb->dt["disp"];
	$div_menu_disp	= $mdb->dt["menu_disp"];
	$div_parent_dix_ix		= $mdb->dt["parent_div_ix"];
	
	global $div_ix;
	$Parentdepth = $depth - 1;
	
	if ($depth ==1){
		$ParentNodeCode = "node$div_parent_dix_ix";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$div_parent_dix_ix";
	}

	$div_name = str_replace("\"","&quot;",$div_name);
	$div_name = str_replace("'","&#39;",$div_name);
	$mstring =  "		var groupnode$mcid = new TreeNode('$div_name ',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	if ($mcid == $div_ix || $_GET["view_depth"] > $depth ){
		$mstring .=  "	groupnode$mcid.expanded = true;\n";
	}


	$mstring .=  "	groupnode$mcid.tooltip = '$div_name';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.action = \"setCategory('$div_name','$div_ix',$depth, $div_disp, $div_menu_disp)\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";

	return $mstring;
}


?>