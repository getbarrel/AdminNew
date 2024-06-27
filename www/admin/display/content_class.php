<?
include("../class/layout.class");
//include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");

$db = new Database;
$sql = "select            
			  config_value as front_url
			from
			  shop_mall_config
		    where
			mall_ix = '".$_SESSION['admininfo']['mall_ix']."'
			and config_name = 'front_url'";

$db->query($sql);
$db->fetch();
$front_url = $db->dt['front_url'];

$language_list = getTranslationType("","","array");

$display_yn_hidden = "display:none;";
$content = "
<script  id='dynamic'></script>
<script language='javascript' src='./color/jscolor.js'></script>
<script>
jscolor.presets.default = {
	width: 141,               // make the picker a little narrower
	position: 'right',        // position it to the right of the target
	previewPosition: 'right', // display color preview on the right
	previewSize: 40,          // make the color preview bigger
	palette: [
		'#000000', '#7d7d7d', '#870014', '#ec1c23', '#ff7e26',
		'#fef100', '#22b14b', '#00a1e7', '#3f47cc', '#a349a4',
		'#ffffff', '#c3c3c3', '#b87957', '#feaec9', '#ffc80d',
		'#eee3af', '#b5e61d', '#99d9ea', '#7092be', '#c8bfe7',
	],
};
/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;

	var rootnode = new TreeNode(\"배럴컨텐츠\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setContent('','000000000000000',-1,'','','','','','','','','','')\";
	rootnode.expanded = true;
";

$db->query("SELECT * FROM shop_content_class where depth in(0,1,2,3,4)  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);

	if ($db->dt["depth"] == 0){
		$content = $content.PrintNode($db);
	}else if($db->dt["depth"] == 1){
		$content = $content.PrintGroupNode($db);
	}else if($db->dt["depth"] == 2){
		$content = $content.PrintGroupNode($db);
	}else if($db->dt["depth"] == 3){
		$content = $content.PrintGroupNode($db);
	}else if($db->dt["depth"] == 4){
		$content = $content.PrintGroupNode($db);
	}else if($db->dt["depth"] == 5){
		$content = $content.PrintGroupNode($db);
	}
}

$content = $content."
	tree.addNode(rootnode);
	tree.draw();
	tree.nodes[0].select();
	
</script>";


$Contents = "
<script language='JavaScript' src='../include/cTree.js'></script>
<script language='JavaScript' src='content_class.js'></script>
<script language='JavaScript' src='../include/manager.js'></script>
<script type='text/javascript' src='../colorpicker/farbtastic.js'></script>
<link rel='stylesheet' href='../colorpicker/farbtastic.css' type='text/css' />

		<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
		    <td align='left' colspan=3> ".GetTitleNavigation("컨텐츠관리", "컨텐츠관리 > ")."</td>
		</tr>
		<tr>
			<td valign=top width='100%' align='left' style=''>
				<div class='tab' style='width:100%;height:38px;margin:0px;'>
					<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('edit_content','tab_01')\" style='padding-left:20px;padding-right:20px;'>
									분류 수정
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('add_content','tab_02')\" style='padding-left:20px;padding-right:20px;'>
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
										<form name='content_order' method='get' action='contentorder.php' target='calcufrm'>
										<input type='hidden' name='this_depth' value=''>
										<input type='hidden' name='cid' value=''>
										<input type='hidden' name='mode' value=''>
										<input type='hidden' name='view' value=''><!--innerview-->
										<img src='../image/t.gif' onclick='order_up(document.content_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
										<img src='../image/b.gif' onclick='order_down(document.content_order)' style='cur sor:hand' alt='분류 아래로 이동' align=absmiddle>
										</td>
										<td width=190 valign=middle>
										<span class=small><!--분류선택후 이동버튼 클릭--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
										</form>
									</td>
								</tr>
								<tr><form>
									<td colspan=2 width=200 height=400 valign=top style='overflow:auto;padding:0 10px 10px 10px;'>
									<div style=\"width:200px;height:418px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
									$content
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
				<div id='edit_content' style='display:block;'>
				<form name='thisContentform' method=\"post\" action='content.save.php' target='calcufrm' style='display:inline;'><!--target='calcufrm'-->
				<input type='hidden' name='mode' value='modify'>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_mode' value='edit_content'>
				<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
				<col width='12%'>
				<col width='30%'>
				<col width='12%'>
				<col width='38%'>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>위치 </b></td>
					<td width=* class='input_box_item'  nowrap colspan='3'>
						<div id='selected_content_1' >미선택 --> 왼쪽 분류에서 컨텐츠를 선택해주세요. </div>
					</td>
				</tr>

				<tr bgcolor=#ffffff height=130px>
					<td class='input_box_title' nowrap> 	<b>분류명 </b><img src='".$required3_path."'></td>
					<td class='input_box_item' nowrap>";

						$Contents .= "<table>";
						$Contents .= "<col width=70px><col width=*>";
						$Contents .= "<tr height=28><td>국문</td><td><input type='text' class='textbox' name='cname' maxlength=40 validation=true></td></tr>";
						$Contents .= "<tr height=28><td>영문</td><td><input type='text' class='textbox' name='global_cname' maxlength=40></td></tr>";
						$Contents .= "</table>

					</td>
					<td class='input_box_title' nowrap> 	<b>설정 </b></td>
					<td class='input_box_item' nowrap >
						진하게<input type='checkbox' name='b_preface' id='b_preface'>
						기울기<input type='checkbox' name='i_preface' id='i_preface'>
						밑줄<input type='checkbox' name='u_preface' id='u_preface'><br>
						색상코드 <input type='text' name='c_preface' id='c_preface' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_preface ? "#000000":$c_preface)."'>
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap> 	<b>URL</b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='text' class='textbox' name='content_link' id='content_link' size=100px>&nbsp;<input type='checkbox' name='content_link_yn' id='content_link_yn'>url연동 제외
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap> 	<b>프론트 노출</b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='radio' name='content_view' id='content_view_1' value='1' checked><label for='content_view_1'> 노출</label>
						<input type='radio' name='content_view' id='content_view_0' value='0'><label for='content_view_0'> 미노출</label> * 노출 설정 하여도, 상위 카테고리 미노출 설정 시 노출되지 않음
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap> 	<b>사용유무</b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='radio' name='content_use' id='content_use_1' value='1' checked><label for='content_use_1'> 사용</label>
						<input type='radio' name='content_use' id='content_use_0' value='0'><label for='content_use_0'> 미사용</label> * 미사용 처리 시 하위 분류 생성 불가, 프론트 노출되지 않음
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap> 	<b>타입</b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='radio' name='content_type' id='content_type_1' value='1' checked><label for='content_type_1'> 기획전</label>
						<input type='radio' name='content_type' id='content_type_2' value='2'><label for='content_type_2'> 스타일</label>
						<input type='radio' name='content_type' id='content_type_3' value='3'><label for='content_type_3'> 프로필</label>
						<input type='radio' name='content_type' id='content_type_4' value='4'><label for='content_type_4'> 리뷰</label> * 카테고리 여부가 N인 경우에만 적용 됩니다.
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap> 	<b>전체리스트숨김</b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='radio' name='content_list_use' id='content_list_use_1' value='1'><label for='content_list_use_1'> 사용</label>
						<input type='radio' name='content_list_use' id='content_list_use_0' value='0' checked><label for='content_list_use_1'> 미사용</label> * 사용 처리 시 전체 리스트에 노출되지 않음
					</td>
				</tr>
				</table><br>
				
				<table cellpadding=0 cellspacing=0 width=100% style='padding-top:20px'>
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> ";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D") || true){
$Contents .= "		<input type='checkbox' name='sub_content_delete' id='sub_content_delete_id'value='1' > <label for='sub_cartegory_delete_id'>하부분류 모두삭제</label>
					<img src='../images/".$admininfo["language"]."/bt_category_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"content_del(document.thisContentform);\">
					<script>
					function content_del(frm){
						var select = confirm(frm.cname.value + '을(를) 삭제하시겠습니까?');
						if(select){
							ContentSave(frm,'delete');
						}else{
							return false;
						}
					}
					</script>
					";
}
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$Contents .= "
					<img src='../images/".$admininfo["language"]."/bt_category_modify.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"thisContentSave(document.thisContentform,'modify');\">";
}
$Contents .= "
					</td>
				</tr>
				</table>
				</form>
				</div>";

$Contents .= "
				<div id='add_content' style='display:none;'>
				<form name='subContentform' method=\"post\" action='content.save.php' target='calcufrm' style='display:inline;'>
				<input type='hidden' name='mode' value='insert'>
				<input type='hidden' name='sub_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_cid' value=''>

				<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box'>
				<col width='12%'>
				<col width='30%'>
				<col width='12%'>
				<col width='38%'>
				<tr>
					<td width=170 class='input_box_title' nowrap>  <b>위치 </b></td>
					<td width=* class='input_box_item'  nowrap colspan='3'>
						<div id='selected_content_2' >미선택 --> 왼쪽 분류에서 컨텐츠를 선택해주세요. </div>
					</td>
				</tr>
				<tr bgcolor=#ffffff height=130px>
					<td class='input_box_title' nowrap> 	<b>분류명 </b><img src='".$required3_path."'></td>
					<td class='input_box_item' nowrap>
						";
 
						$Contents .= "<table>";
						$Contents .= "<col width=70px><col width=*>";
						$Contents .= "<tr height=28><td>국문</td><td><input type='text' class='textbox' name='cname' maxlength=40 validation=true></td></tr>";
						$Contents .= "<tr height=28><td>영문</td><td><input type='text' class='textbox' name='global_cname' maxlength=40></td></tr>";
						$Contents .= "</table>

					</td>
					<td class='input_box_title' nowrap'> 	<b>설정 </b></td>
					<td class='input_box_item' nowrap >
						진하게<input type='checkbox' name='b_preface'>
						기울기<input type='checkbox' name='i_preface'>
						밑줄<input type='checkbox' name='u_preface'><br>
						색상코드 <input type='text' name='c_preface' id='c_preface' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_preface ? "#000000":$c_preface)."'>
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap> 	<b>URL</b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='text' class='textbox' name='content_link' size=100px>&nbsp;<input type='checkbox' name='content_link_yn'>url연동 제외
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap> 	<b>프론트 노출</b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='radio' name='content_view' id='content_view_1' value='1' checked><label for='content_view_1'> 노출</label>
						<input type='radio' name='content_view' id='content_view_0' value='0'><label for='content_view_0'> 미노출</label> * 노출 설정 하여도, 상위 카테고리 미노출 설정 시 노출되지 않음
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap> 	<b>사용유무</b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='radio' name='content_use' id='content_use_1' value='1' checked><label for='content_use_1'> 사용</label>
						<input type='radio' name='content_use' id='content_use_0' value='0'><label for='content_use_0'> 미사용</label> * 미사용 처리 시 하위 분류 생성 불가, 프론트 노출되지 않음
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap> 	<b>타입</b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='radio' name='content_type' id='content_type_1' value='1' checked><label for='content_type_1'> 기획전</label>
						<input type='radio' name='content_type' id='content_type_2' value='2'><label for='content_type_2'> 스타일</label>
						<input type='radio' name='content_type' id='content_type_3' value='3'><label for='content_type_3'> 프로필</label>
						<input type='radio' name='content_type' id='content_type_4' value='4'><label for='content_type_4'> 리뷰</label> * 카테고리 여부가 N인 경우에만 적용 됩니다.
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap> 	<b>전체리스트숨김</b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='radio' name='content_list_use' id='content_list_use_1' value='1'><label for='content_list_use_1'> 사용</label>
						<input type='radio' name='content_list_use' id='content_list_use_0' value='0' checked><label for='content_list_use_1'> 미사용</label> * 사용 처리 시 전체 리스트에 노출되지 않음
					</td>
				</tr>
				</table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$Contents .= "
				<table cellpadding=5 cellspacing=1 width=97% >
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> <img src='../images/".$admininfo["language"]."/bt_category_add.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"SubContentSave(document.subContentform,'insert');\"></td>
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
</table>
		";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= "
		<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0></iframe>";

if($view == "innerview"){
	$P = new popLayOut;
	$addScript = "
	<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
	<script src='../include/rightmenu.js'></script>\n".$Script;
	$P->addScript = $addScript; /**/
	$P->OnloadFunction = "";
	$P->title = "";
	$P->strLeftMenu = "";
	$Contents ="
		<div id='content_area'>
			<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
				<tr>
					<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle>
						<form name='content_order' method='get' action='contentorder.php' target='calcufrm'>
						<input type='hidden' name='this_depth' value=''>
						<input type='hidden' name='cid' value=''>
						<input type='hidden' name='mode' value=''>
						<input type='hidden' name='view' value='innerview'>
						<img src='../image/t.gif' onclick='order_up(document.content_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
						<img src='../image/b.gif' onclick='order_down(document.content_order)' style='cursor:hand' alt='분류 아래로 이동' align=absmiddle>
						<span class=small>분류선택후 이동버튼 클릭</span>
						</form>
					</td>
				</tr>
				<tr>
					<td width=200 height=400 valign=top style='overflow:auto;padding:0 10 10 10;'>
					<form>
						<div style=\"width:200px;height:420px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
						$content
						</div>
					</form>
					</td>
				</tr>
				</table>
		</div>

		<script>alert(document.getElementById('content_area').innerHTML);parent.document.getElementById('TREE_BAR').innerHTML = document.getElementById('content_area').innerHTML;</script>";

	$P->strContents = $Contents;
	$P->Navigation = "";
	$P->PrintLayOut();
	exit;
}else{

$P = new LayOut;
$addScript = "
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
<script src='../include/rightmenu.js'></script>\n
<SCRIPT type='text/javascript'>

function setcolorpicker(div_id,input_id){
	
	$('#'+div_id).farbtastic('#'+input_id);		//색상표선택
	$('#'+div_id).css('display','');

}

function department_del(dp_ix){
	$('#department_row_'+dp_ix).remove();
}

function person_del(code){
	$('#row_'+code).remove();
}

function contentProduct(frm){
    var cid = frm.cid.value; 
    var depth = frm.this_depth.value;
    if(cid){
        PoPWindow('content_product.php?cid='+cid+'&depth='+depth,850,550,'content_product');
    }else{
        alert('카테고리를 선택해 주세요')
    }
}

</SCRIPT>
".$Script;
$P->addScript = $addScript; /**/
$P->OnloadFunction = ""; //showSubMenuLayer('storeleft'); MenuHidden(false);
$P->title = "";//text_button('#', " ♣ 분류 구성"); 
$P->strLeftMenu = display_menu();


$P->strContents = $Contents;
$P->Navigation = "컨텐츠관리 > 컨텐츠관리 > 컨텐츠 분류관리";
$P->title = "컨텐츠 분류관리";
$P->PrintLayOut();
}

function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($mdb){
	
	$cdb = new Database;

	$cid			= $mdb->dt[cid];
	$depth			= $mdb->dt[depth];
	$cname			= $mdb->dt[cname];
	$global_cname	= $mdb->dt[global_cname];
	$b_preface		= $mdb->dt[b_preface];
	$i_preface		= $mdb->dt[i_preface];
	$u_preface		= $mdb->dt[u_preface];
	$c_preface		= $mdb->dt[c_preface];
	$content_link	= $mdb->dt[content_link];
	$content_link_yn= $mdb->dt[content_link_yn];
	$content_use	= $mdb->dt[content_use];
	$content_view	= $mdb->dt[content_view];
	$content_type	= $mdb->dt[content_type];
    $content_list_use = $mdb->dt[content_list_use];

	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname			= str_replace("\"","&quot;",$cname);
	$cname			= str_replace("'","&#39;",$cname);
	
	$global_cname	= str_replace("\"","&quot;",$global_cname);
	$global_cname	= str_replace("'","&#39;",$global_cname);

	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setContent('$cname', '$cid', $depth, '$global_cname', '$b_preface', '$i_preface', '$u_preface', '$c_preface', '$content_link', '$content_link_yn', '$content_use', '$content_view', '$content_type', '$content_list_use')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($mdb)
{
	global $cid;

	$mcid			= $mdb->dt[cid];
	$depth			= $mdb->dt[depth];
	$cname			= $mdb->dt[cname];
	$global_cname	= $mdb->dt[global_cname];
	$b_preface		= $mdb->dt[b_preface];
	$i_preface		= $mdb->dt[i_preface];
	$u_preface		= $mdb->dt[u_preface];
	$c_preface		= $mdb->dt[c_preface];
	$content_link	= $mdb->dt[content_link];
	$content_link_yn= $mdb->dt[content_link_yn];
	$content_use	= $mdb->dt[content_use];
	$content_view	= $mdb->dt[content_view];
	$content_type	= $mdb->dt[content_type];
    $content_list_use = $mdb->dt[content_list_use];

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

	$cname			= str_replace("\"","&quot;",$cname);
	$cname			= str_replace("'","&#39;",$cname);
	
	$global_cname	= str_replace("\"","&quot;",$global_cname);
	$global_cname	= str_replace("'","&#39;",$global_cname);


	$mstring =  "		var groupnode$mcid = new TreeNode('$cname ',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	if ($mcid == $cid || (substr($mcid,0,($depth)*3) == substr($_GET["cid"],0,($depth)*3) && $_GET["depth"] > $depth )  || (substr($mcid,0,($depth+1)*3) == substr($_GET["cid"],0,($depth+1)*3)) ){//
		$mstring .=  "	groupnode$mcid.expanded = true;\n";
	}


	$mstring .=  "	groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.action = \"setContent('$cname', '$mcid', $depth, '$global_cname', '$b_preface', '$i_preface', '$u_preface', '$c_preface', '$content_link', '$content_link_yn', '$content_use', '$content_view', '$content_type', '$content_list_use')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";

	return $mstring;
}


?>