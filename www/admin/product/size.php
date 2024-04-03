<?
include("../class/layout.class");

include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");

$db = new Database;

$category = "
<script  id='dynamic'></script>
<script>
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;

	var rootnode = new TreeNode(\"분류\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('분류','000000000000000',-1,'','','','','','')\";
	rootnode.expanded = true;
";

$db->query("SELECT * FROM shop_goods_size_info where depth in(0,1,2,3,4) order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{
	$db->fetch($i);

	if ($db->dt["depth"] == 0){
		$category = $category.PrintNode($db);
	}else if($db->dt["depth"] == 1){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 2){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 3){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 4){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 5){
		$category = $category.PrintGroupNode($db);
	}
}
$category = $category."
	tree.addNode(rootnode);
	tree.draw();
	tree.nodes[0].select();
</script>";
$Contents = "
<script language='JavaScript' src='../include/cTree.js'></script>
<script language='JavaScript' src='size.js'></script>
<script language='JavaScript' src='../include/manager.js'></script>
<script type='text/javascript' src='../colorpicker/farbtastic.js'></script>
<link rel='stylesheet' href='../colorpicker/farbtastic.css' type='text/css' />

		<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
			<td valign=top width='100%' align='left' style=''>
				<div class='tab' style='width:100%;height:38px;margin:0px;'>
					<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('edit_category','tab_01')\" style='padding-left:20px;padding-right:20px;'>
									분류 수정
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('add_subcategory','tab_02')\" style='padding-left:20px;padding-right:20px;'>
									분류 추가
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td class='btn'>
						</td>
					</tr>
					</table>
				</div>
			<table cellpadding=0 cellspacing=0 width=100% >
			<tr>
				<td width='15%' align='left'  valign='top'>
					<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
					<tr>
						<td valign=top width=236>
							<div id=TREE_BAR >
								<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 width=100% style='border:3px solid #d8d8d8'>
								<tr>
									<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle nowrap>
										<form name='size_order' method='get' action='sizeorder.php' target='calcufrm'>
										<input type='hidden' name='this_depth' value=''>
										<input type='hidden' name='cid' value=''>
										<input type='hidden' name='mode' value=''>
										<input type='hidden' name='view' value=''><!--innerview-->
										<img src='../image/t.gif' onclick='order_up(document.size_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
										<img src='../image/b.gif' onclick='order_down(document.size_order)' style='cur sor:hand' alt='분류 아래로 이동' align=absmiddle>
										</td>
										<td width=190 valign=middle>
										<span class=small>중분류 카테고리만 변경이 가능합니다.</span>
										</form>
									</td>
								</tr>
								<tr><form>
									<td colspan=2 width=200 height=400 valign=top style='overflow:auto;padding:0 10px 10px 10px;'>
									<div style=\"width:200px;height:418px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
									$category
									</div>
									</td>
								</tr></form>
								</table>
							</div>
						</td >
					</tr>
					</table>
				</td>
				<td style='padding-left:13px;'></td>
				<td width='82%' align='right' valign='top'>";
$Contents .= "
				<div id='edit_category' style='display:block;'>
				<form name='thisCategoryform' method=\"post\" action='size.save.php' target='calcufrm' style='display:inline;' enctype='multipart/form-data'><!--target='calcufrm'-->
				<input type='hidden' name='mode' value='modify'>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_mode' value='edit_category'>
				<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
				<col width='18%'>
				<col width='32%'>
				<col width='18%'>
				<col width='32%'>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>위치 <img src='".$required3_path."'></b></td>
					<td width=* class='input_box_item'  nowrap colspan='3'>
						<div id='selected_category_1' >미선택 --> 왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요.</div>
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류명 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap><input type='text' class='textbox' name='title' id='title' maxlength=40 validation=true title='선택된 분류'></td>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'><b>분류사용 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap style='padding:5px;'>
						<input type='radio' name='size_use' id='size_use_id' value=1 checked /><label for='size_use_id'> 사용</label>&nbsp;
						<input type='radio' name='size_use' id='size_use_id_0' value=0 /><label for='size_use_id_0'> 미사용</label>&nbsp;
					</td>
				</tr>
				
				</table><br>
				
				<table cellpadding=0 cellspacing=0 width=100% class='input_table_box' >
				<col width='18%'>
				<col width='*'>
				<tr>
					<td class='input_box_title'> 	<b>타이틀 이모티콘<br>(20 x 18) </b></td>
					<td class='input_box_item' nowrap>
					    <input type='file' name='title_img' class='textbox' style='width:50%;height:25px;'><div id='show_title' style='width:40%;'></div>
						<input type='hidden' name='title_img_old' value=''>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 	<b>PC 사이즈이미지 </b></td>
					<td class='input_box_item' nowrap>
					    <input type='file' name='contents_pc' class='textbox' style='width:50%;height:25px;'><div id='show_pc' style='width:40%;'></div>
						<input type='hidden' name='contents_pc_old' value=''>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 	<b>Mobile 사이즈이미지 </b></td>
					<td class='input_box_item' nowrap>
					    <input type='file' name='contents_mo' class='textbox' style='width:50%;height:25px;'><div id='show_mo' style='width:40%;'></div>
						<input type='hidden' name='contents_mo_old' value=''>
					</td>
				</tr>

				
				
				</table>
				<br>
				<table cellpadding=0 cellspacing=0 width=100% style='padding-top:20px'>
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> ";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D") || true){
$Contents .= "		<input type='checkbox' name='sub_cartegory_delete' id='sub_cartegory_delete_id'value='1' > <label for='sub_cartegory_delete_id'>하부분류 모두삭제</label>
					<img src='../images/".$admininfo["language"]."/bt_category_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"category_del(document.thisCategoryform);\">
					<script>
					function category_del(frm){
						if (frm.cid.value.length < 1){
							alert('삭제 하시고자 하는 상품카테고리를 선택해 주세요');
							return false;
						}
						var select = confirm(frm.title.value + '을(를) 삭제하시겠습니까?');
						if(select){
							CategorySave(frm,'del');
						}else{
							return false;
						}
					}
					</script>
					";
}
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$Contents .= "
					<img src='../images/".$admininfo["language"]."/bt_category_modify.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"thisCategorySave(document.thisCategoryform,'modify');\">";
}
$Contents .= "
					</td>
				</tr>
				</table>
				</form>
				</div>";

$Contents .= "
				<div id='add_subcategory' style='display:none;'>
				<form name='subCategoryform' method=\"post\" action='size.save.php' target='calcufrm' style='display:inline;'>
				<input type='hidden' name='mode' value='insert'>
				<input type='hidden' name='sub_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_cid' value=''>

				<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr>
					<td width=170 class='input_box_title' nowrap>  <b>위치 <img src='".$required3_path."'></b></td>
					<td width=* class='input_box_item'  nowrap colspan='3'>
						<div id='selected_category_3' >미선택 --> 왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요.</div>
					</td>
				</tr>
				<tr >
					<td class='input_box_title' nowrap>  <b>분류명 <img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
						<input type='text' class='textbox' name='title' maxlength=40 validation=true title='분류명'>
					</td>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'><b>분류사용 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap style='padding:5px;'>
						<input type='radio' name='size_use' id='size_use_id' value=1 checked><label for='category_use_id_'> 사용</label>&nbsp;
						<input type='radio' name='size_use' id='size_use_id_0' value=0 ><label for='category_use_id_10'> 미사용</label>&nbsp;
					</td>
				</tr>
				</table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$Contents .= "
				<table cellpadding=5 cellspacing=1 width=97% >
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> <img src='../images/".$admininfo["language"]."/bt_category_add.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"SubCategorySave(document.subCategoryform,'insert');\"></td>
				</tr>
				</table>";
}
$Contents .= "
				</form>
				</div>";
$Contents .= "
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= "<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0></iframe>";

if($view == "innerview"){
	$P = new popLayOut;
	$addScript = "
	<script src='../include/rightmenu.js'></script>\n".$Script;
	$P->addScript = $addScript;
	$P->OnloadFunction = "";
	$P->title = "";
	$P->strLeftMenu = "";
	$Contents ="
		<div id='category_area'>
			<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
				<tr>
					<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle>
						<form name='size_order' method='get' action='sizeorder.php' target='calcufrm'>
						<input type='hidden' name='this_depth' value=''>
						<input type='hidden' name='cid' value=''>
						<input type='hidden' name='mode' value=''>
						<input type='hidden' name='view' value='innerview'>
						<!-- <img src='../image/t.gif' onclick='order_up(document.size_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
						<img src='../image/b.gif' onclick='order_down(document.size_order)' style='cursor:hand' alt='분류 아래로 이동' align=absmiddle> -->
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
<script src='../include/rightmenu.js'></script>\n
".$Script;
$P->addScript = $addScript;
$P->OnloadFunction = "";
$P->title = "";
$P->strLeftMenu = product_menu();

$P->strContents = $Contents;
$P->Navigation = "상품관리 > 상품분류관리 > 상품사이즈관리";
$P->title = "상품사이즈관리";
$P->PrintLayOut();
}

function PrintNode($mdb){
	
	$cdb = new Database;

	//$title			= $mdb->dt[title]."(".substr($mdb->dt[cid],0,3).")";
	$title			= $mdb->dt[title];
	$size_use		= $mdb->dt[size_use];
	$cid			= $mdb->dt[cid];
	$depth			= $mdb->dt[depth];

	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$title = str_replace("\"","&quot;",$title);
	$title = str_replace("'","&#39;",$title);

	$cdb->query("select * from shop_category_auth where cid = '".$cid."'");
	$cdb->fetch();
	for($i=$depth;$i>=0;$i--){

		$org_cid = substr($cid,0,3+(3*$i));
		$org_cid =$org_cid."000000000000";
		$for_cid = substr($org_cid,0,15);

		$sql = "select * from shop_category_auth where cid = '".$for_cid."' ";
		$cdb->query($sql);
		$cdb->fetch();
	}

	return "	var node$cid = new TreeNode('$title', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('$title','$cid', $depth, '$size_use','','','','')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($mdb)
{

	global $cid;

	$title			= $mdb->dt[title];
	$size_use		= $mdb->dt[size_use];
	$mcid			= $mdb->dt[cid];
	$depth			= $mdb->dt[depth];
	$title_img		= $mdb->dt[title_img];
	$contents_pc	= $mdb->dt[contents_pc];
	$contents_mo	= $mdb->dt[contents_mo];

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

	$title = str_replace("\"","&quot;",$title);
	$title = str_replace("'","&#39;",$title);

	if ($depth ==1){
		$ParentNodeCode = "node$parent_cid";

		$title_t = str_replace("\"","&quot;",$title);
		$title_t = str_replace("'","&#39;",$title)."(".substr($mcid,0,6).")";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==4){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==5){
		$ParentNodeCode = "groupnode$parent_cid";
	}
	
	if($depth == 1){
		$mstring =  "		var groupnode$mcid = new TreeNode('$title_t ',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	} else {
		$mstring =  "		var groupnode$mcid = new TreeNode('$title ',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	}
	if ($mcid == $cid || (substr($mcid,0,($depth)*3) == substr($_GET["cid"],0,($depth)*3) && $_GET["depth"] > $depth )  || (substr($mcid,0,($depth+1)*3) == substr($_GET["cid"],0,($depth+1)*3)) ){//
		$mstring .=  "	groupnode$mcid.expanded = true;\n";
	}

	$mstring .=  "	groupnode$mcid.tooltip = '$title';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.action = \"setCategory('$title','$mcid', $depth, '$size_use','$title_img','$contents_pc','$contents_mo')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";

	return $mstring;
}


?>