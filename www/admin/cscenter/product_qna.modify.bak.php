<?
include("../class/layout.class");


$db = new Database;

/*$sql = "select cmd.name, p.pname, p.sellprice, pq.*
		from ".TBL_SHOP_PRODUCT_QNA." pq, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_PRODUCT." p
		where pq.ucode = cu.code and cu.code = cmd.code and pq.pid = p.id and bbs_ix = '".$bbs_ix."' ";*/
$sql = "select * from ".TBL_SHOP_PRODUCT_QNA." where bbs_ix = '".$bbs_ix."' ";
$db->query($sql);

if($db->total){
	$db->fetch();
	$act = "update";
	$pname = $db->dt[pname];
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "s"))){
		$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "s");//$admin_config[mall_data_root]."/images/product/s_".$db->dt[pid].".gif";
	}else{
		$img_str = "../image/no_img.gif";
	}
}else{
	$act = "insert";
}

$Script="
<script language=javascript>
	function bbs_response_templet(selectbox,id){
		$('#'+id).val(selectbox.val())
	}
</script>
";

$Contents = "

		<table cellpadding=0 cellspacing=0 border=0 width='100%' >
		<tr>
			<td valign=top width='100%' style='padding:0px;'>
				<form name='product_qna_frm' method=\"post\" enctype='multipart/form-data' action='product_qna.act.php' onsubmit='return CheckFormValue(this);'>
				<input type='hidden' name='act' value='$act'>
				<input type='hidden' name='bbs_ix' value='$bbs_ix'>
				<input type='hidden' name='page_type' value='$page_type'>
				<table cellpadding=3 cellspacing=0 border=0 width=100%>
				<col width=100>
				<col width=*>
				<tr height=40>
					<td align='left' colspan=2> ".GetTitleNavigation("상품 Q&A 수정하기", "고객센타 > 상품 Q&A 수정하기", false)."</td>
				</tr>
				<tr height=1><td colspan=2 class='dot-x'></td></tr>
				<tr height=120>
					<td colspan=2 align=left style='padding:3px 0px;'>
					<table width=100% cellpadding=0 cellspacing=0 bgcolor=#efefef>
						<col width=100>
						<col width=*>
						<tr bgcolor=#ffffff>
							<td align=center>
							<img src='".$img_str."' width=50>
							</td>
							<td style='line-height:140%;vertical-align:top;'>
							<b>$pname</b><br>
							가격 : ".get_product_price($db->dt[pid])." 원
							</td>
						</tr>
					</table>
					</td>
				</tr>
				</table>

				<table border='0' width='100%' cellpadding=5 cellspacing=1 bgcolor=silver  class='list_table_box'>
				<tr height=30 bgcolor=#ffffff>
					<td bgcolor=#efefef align=center nowrap>문의분류</td>
					<td colspan=3 nowrap>
						<input type='radio' name='bbs_div' id='bbs_div_s' ".($db->dt[bbs_div] == 'S'?'checked':'')."><label for='bbs_div_s'>배송</label>&nbsp;&nbsp;
						<input type='radio' name='bbs_div' id='bbs_div_p' ".($db->dt[bbs_div] == 'P'?'checked':'')."><label for='bbs_div_p'>상품</label>&nbsp;&nbsp;
						<input type='radio' name='bbs_div' id='bbs_div_r' ".($db->dt[bbs_div] == 'R'?'checked':'')."><label for='bbs_div_r'>환불</label>&nbsp;&nbsp;
						<input type='radio' name='bbs_div' id='bbs_div_c' ".($db->dt[bbs_div] == 'C'?'checked':'')."><label for='bbs_div_c'>취소</label>&nbsp;&nbsp;
						<input type='radio' name='bbs_div' id='bbs_div_m' ".($db->dt[bbs_div] == 'M'?'checked':'')."><label for='bbs_div_m'>회원</label>&nbsp;&nbsp;
						<input type='radio' name='bbs_div' id='bbs_div_e' ".($db->dt[bbs_div] == 'E'?'checked':'')."><label for='bbs_div_e'>기타</label>&nbsp;&nbsp;
					</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td bgcolor=#efefef align=center nowrap>제목</td>
					<td colspan=3 nowrap>
						<input type='text' style='padding:2px;width:60%' class=textbox name='bbs_subject' value='".$db->dt[bbs_subject]."' validation=true title='제목'>
					</td>
				</tr>
				<tr height=110 bgcolor=#ffffff>
					<td bgcolor=#efefef align=center nowrap >내용</td>
					<td colspan=3 nowrap style='padding:2px;'>
					<textarea name='bbs_contents' style='padding:2px;font-size:12px;color:#000000;height:100px;width:99%;' validation=true title='내용'>".$db->dt[bbs_contents]."</textarea>
					</td>
				</tr>

				<tr height=30 bgcolor=#ffffff>
					<td bgcolor=#efefef align=center nowrap >답변 제목</td>
					<td colspan=3 nowrap>
						<input type='text' name='bbs_response_title' class=textbox style='width:70%' value='".($db->dt[bbs_response_title] ? $db->dt[bbs_response_title]:"답변입니다.")."' validation=false title='답변제목'>
						<input type='checkbox' name='bbs_re_bool' id='bbs_re_bool' value='Y' ".(($db->dt[bbs_re_bool] == "Y") ? "checked":"checked")."><label for='bbs_re_bool'>답변사용여부</label>
					</td>
				</tr>
				<tr height=110 bgcolor=#ffffff>
					<td bgcolor=#efefef align=center nowrap >답변내용</td>
					<td colspan=3 nowrap  style='padding:2px;'>
					<textarea name='bbs_response' id='bbs_response' style='padding:2px;font-size:12px;color:#000000;height:100px;width:99%' validation=false title='답변내용'>".$db->dt[bbs_response]."</textarea>
					</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td bgcolor=#efefef align=center nowrap >등록일자</td>
					<td nowrap>
						<input type='text' style='padding:2px;width:150px' class=textbox name='regdate' value='".$db->dt[regdate]."' validation=true title='등록일자' readonly>
					</td>
					<td bgcolor=#efefef align=center nowrap >답변 템플릿 선택</td>
					<td nowrap >
						".bbs_response_templet_selectbox('bbs_response',"P_Q&A")."
					</td>
				</tr>

				</table>
				<table cellpadding=3 cellspacing=0 border=0 width=100%>
				<tr height=70 bgcolor=#ffffff>
					<td colspan=2 align=center>";
                    //if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                        $Contents.="<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle  >";
                    //}else{
                    //    $Contents.="<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle  ></a>";
                    //}
                    $Contents.="
					</td>
				</tr>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>상품문의 답변</b> : 답변여부를 클락하시고 답변 내용을 입력하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >답변을 하신후에도 답변 여부를 체크를 해제하시면 답변이 노출되지 않습니다</td></tr>

</table>
";

	$help_text = HelpBox("상품문의수정", $help_text);
$Contents .="		<tr bgcolor=#ffffff>
					<td colspan=2>$help_text 	</td>
				</tr>
				</table>
				</form>
				";

$Contents .="


				<br>";



$Contents .= "
			</td>
			<td style='padding-bottom:1px;'>
			</td>
		</tr>
		";


$Contents .= "
		</table>
		<iframe name='calcufrm' src='' width=0 height=0></iframe>";




$addScript = "
<script language='JavaScript' src='../webedit/webedit.js'></script>\n
<script language='JavaScript' src='../include/manager.js'></script>\n
<script language='JavaScript' src='../include/cTree.js'></script>\n
<script src='../include/rightmenu.js'></script>\n".$Script;





$P = new ManagePopLayOut();
$P->addScript = $addScript;
$P->Navigation = "고객센타 > 1:1 상담내역";
$P->NaviTitle = "1:1 상담내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();



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
	parent.document.forms['product_qna_frm'].category_use.checked = $category_use;
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

function get_product_price($pid) {
	//global $db;
	$db2=new Database;
	$sql="SELECT sellprice FROM ".TBL_SHOP_PRODUCT." WHERE id='".$pid."' ";
	$db2->query($sql);
	$db2->fetch();
	return number_format($db2->dt["sellprice"]);
}
?>