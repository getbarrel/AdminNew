<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include("./translation_excel_input.lib.php");
 
if($max == ""){
$max = 10; //페이지당 갯수
}

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}


$db = new Database;
$db2 = new Database;
 

if(!$up_mode){
	$up_mode="download";
}

$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=4 > ".GetTitleNavigation("번역엑셀등록", "번역관리 > 번역엑셀등록")."</td>
			</tr>";

$Contents .=	"
			<tr>
				<td align='left' colspan=4 style='padding-bottom:15px;'>
					<div class='tab'>
							<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									 
									<table id='tab_02' ".($up_mode=="download" ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='?up_mode=download'\">샘플다운로드</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='tab_02' ".($up_mode=="new_upload" ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='?up_mode=new_upload'\">번역항목 엑셀등록</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='tab_03' ".($up_mode=="make_file" ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='?up_mode=make_file'\">번역파일 생성</td>
										<th class='box_03'></th>
									</tr>
									</table>

								</td>
								<td class='btn'>

								</td>
							</tr>
							</table>
						</div>
				</td>
			</tr>";

if($up_mode == "download"){
$Contents .=	"
			 <tr>
			 	<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:2px 5px 2px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 샘플다운로드</b> <!--span class='small red'> 대량등록에 필요한 코드를 다운받아서 사용하시면 됩니다. ☞ <a href='product_input_excel_2003.act.php?act=ect_code_down'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a></span--> </div>")."</td>
			 </tr>
			 <tr>
			 	<td colspan=3>
			 	<form name='excel_input_form' method='post' action='translation_excel_input.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''><!--iframe_act-->

			 	<input type='hidden' name='act' value='sample_excel_down'>
			 	<input type='hidden' name='cid' value=''>
			 	<input type='hidden' name='depth' value=''>
				<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
					<col width=20%>
					<col width=30%>
					<col width=20%>
					<col width=30%>
					<!--tr height=30 align=center>
						<td class='input_box_title'  ><b>카테고리명 선택</b></td>
						<td class='input_box_item'  id='select_category_path3' align=left style='padding-left:10px;' colspan=3>전체 <span class=small style='padding-left:30px;'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span></td>
					</tr-->
					<tr height=30 align=center>
						<td class='input_box_title'><b>번역 구분</b></td>
						<td class='input_box_item' colspan=3>
							".getTranslationType($trans_type,"onchange=\"document.location.href='?text_div=".$_GET["text_div"]."&trans_type='+this.value\" ")."
						</td> 

					</tr>

					<!--tr height=30 align=center style='display:none;'>
						<td class='input_box_title' ><b>상품고시 추가정보</b></td>
						<td class='input_box_item' colspan=3>
						<div id='mandatory_info_zone'>
						<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='mandatory_info_input' class='mandatory_info_input' opt_idx=0 style='margin-bottom:10px'>
							<col width='20%'>
							<col width='30%'>
							<col width='20%'>
							<col width='30%'>
							<tr height=25 bgcolor='#ffffff' align=center>
								<td bgcolor=\"#efefef\" class=small>항목</td>
								<td bgcolor=\"#efefef\" class=small> 내용</td>
								<td bgcolor=\"#efefef\" class=small> 항목</td>
								<td bgcolor=\"#efefef\" class=small> 내용</td>
							</tr>
							<tr bgcolor='#ffffff' align=center>
								<td colspan=4 id='mandatory_info_td'>";


	$Contents .= "
										<table width=100% id='mandatory_info' class='mandatory_info_basic' mandatory_info_cnt='0' cellspacing=0 cellpadding=0 >
											<col width='20%'>
											<col width='30%'>
											<col width='20%'>
											<col width='30%'>
											<tr align='center'>
												<td height='30'>
													<input type='hidden' id='mandatory_info_pmi_ix_a' class='' name='mandatory_info[0][pmi_ix]' value='' />
													<input type='hidden' id='mandatory_info_pmi_code_a' class='' name='mandatory_info[0][pmi_code]' value='' />
													<input type=text id='mandatory_info_title_a' class='textbox' name='mandatory_info[0][pmi_title]' style='width:90%;vertical-align:middle' value='' title='' validation='false'>
												</td>
												<td>
													<input type=text id='mandatory_info_desc_a' class='textbox' name='mandatory_info[0][pmi_desc]' style='width:85%' value=''>
												</td>
												<td>
													<input type='hidden' id='mandatory_info_pmi_ix_b' class='' name='mandatory_info[1][pmi_ix]' value='' />
													<input type='hidden' id='mandatory_info_pmi_code_b' class='' name='mandatory_info[1][pmi_code]' value='' />
													<input type=text id='mandatory_info_title_b' class='textbox' name='mandatory_info[1][pmi_title]' style='width:90%;vertical-align:middle' value='' title='' validation='false'>
												</td>
												<td>
													<input type=text id='mandatory_info_desc_b' class='textbox' name='mandatory_info[1][pmi_desc]' style='width:85%' value=''>
													<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.mandatory_info').length > 1){document.getElementById('mandatory_info_td').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/}else{clearInputBox('mandatory_info');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
												</td>
											</tr>
											<tr>
												<td height='13' colspan='2'>
													<div class='mandatory_info_comment_0 small' style='padding:0px 20px;text-align:right;'></div>
												</td>
												<td colspan='2'>
													<div class='mandatory_info_comment_1 small' style='padding:0px 20px;text-align:right;'></div>
												</td>
											</tr>
										</table>";

$Contents .= "
									</td>

								</tr>
							</table>
						</div>
						</td>
					</tr-->
					</table>
					<table width='100%' border=0 cellpadding=0 cellspacing=1 >
					<tr height=20>
						<td style='padding:6px;line-height:140%;' colspan=2><img src='../image/emo_3_15.gif' border=0 align=absmiddle> 분류를 선택하신후 샘플 다운로드 버튼을 클릭하시면 해당포맷에 맞는 엑셀 샘플파일이 다운로드 됩니다. </td></tr>
					<tr height=30>
						<td colspan=2 style='padding:10px 0px;' align=center>
							<a href='trans_sample_.xls'>
							<img src='../images/".$admininfo["language"]."/b_excel_sample_down.gif' border=0>
							</a>
							<!--input type=image src='../images/".$admininfo["language"]."/b_excel_sample_down.gif' border=0>
							<a href='?act=make_language_file'>파일생성</a-->
						</td>
					</tr>
				</table>
				</form>
			 	</td>
			 </tr>
			 </table>";
}else if($up_mode == "make_file"){

$Contents .= "
	<table width='90%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=6> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b  class=blk>언어목록</b></div>")."</td>
	  </tr>
	  <tr height=5><td colspan=6 ></td></tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <col width='15%'>
	  <col width='*'>
	  <col width='20%'>
	  <col width='20%'>
	  <col width='20%'>
	  <col width='20%'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 언어명</td>
	    <td class='m_td'> 언어코드</td> 
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";
$db = new MySQL;

$db->query("SELECT * FROM global_language where disp = 1 ");


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

	$Contents .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".$db->dt[language_name]."</td>
		    <td class='list_box_td point'>".$db->dt[language_code]."</td> 
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .= "
		    	<a href=\"javascript:makeLanguageFile('".$db->dt[language_code]."')\"><img src='../images/".$admininfo["language"]."/btn_create_languagepack.gif' border=0 align=absmiddle></a>";
			}else{
				$Contents .= "
		    	<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle></a>";
			}

			if($admininfo["charger_id"]=="forbiz"){
				$Contents .= " <a href=\"javascript:ChangeTempletLanguage('".$db->dt[language_code]."')\"><img src='../images/".$admininfo["language"]."/btn_auto_change_templet.gif' border=0 align=absmiddle></a> ";
			}
			

			/*
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$Contents .= "
	    		<a href=\"javascript:deleteLanguageInfo('delete','".$db->dt[language_ix]."')\"><img src='../images/".$admininfo["language"]."/btn_auto_change_templet.gif' border=0></a>";
			}else{
			$Contents .= "
	    		<a href=\"javascript:alert('삭제권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btn_auto_change_templet.gif' border=0></a>";
			}
			*/
	$Contents .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=5>등록된 언어가 없습니다. </td>
		  </tr>";
}
$Contents .= " 

	  </table>";

}else{

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || true){
		if($admininfo[mall_type] == "BW"){
			$download_excel_file = "batch_product_upload_example_wholesale.xls";
		}else{
			if($_SERVER["HTTP_HOST"] == "academyprice.s2.mallstory.com"){
				$download_excel_file = "batch_product_upload_example_academyprice.xls";
			}else {
				$download_excel_file = "batch_product_upload_example.xls";
			}
		}
$Contents .=	"


			 <!--tr>
			 	<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:2px 5px 2px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 등록카테고리</b>&nbsp;&nbsp;&nbsp;<b id='select_category_path1'>전체 <span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span></b> <a href='".$download_excel_file."'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a><- 대량등록엑셀샘플 파일이 업데이트(2012-08-12) 되었습니다. 파일을 다시 다운받아서 사용해주시기 바랍니다.</div>")."</td>
			 </tr-->
			 <tr>
			 	<td colspan=3>
			 	<form name='excel_input_form' method='post' action='translation_excel_input.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''><!--iframe_act-->

			 	<input type='hidden' name='act' value='".($up_mode == "new_upload" ? "new_excel_input":"excel_input")."'>
			 	<input type='hidden' name='cid' value=''>
			 	<input type='hidden' name='depth' value=''>
				<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box' style='table-layout:fixed;'>
					<col width=20%>
					<col width=30%>
					<col width=20%>
					<col width=30%>

					<tr height=30 align=center ".($up_mode == "new_upload" ? "style='display:none'":"").">
						<td class='input_box_title'  ><b>카테고리명 선택</b></td>
						<td class='input_box_item'  id='select_category_path3' align=left style='padding-left:10px;' colspan=3>전체 <span class=small style='padding-left:30px;'><!--선택된 카테고리가 없습니다. 좌측 카테고리에서 선택해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span></td>
					</tr>
					<tr height=30 align=center ".($up_mode == "new_upload" ? "style='display:none'":"").">
						<td class='input_box_title'>입점업체</td>
						<td class='input_box_item'>
							".CompanyList2($company_id,"")."
						</td>
						<td class='input_box_title'>브랜드</td>
						<td class='input_box_item' >".BrandListSelect($brand, $cid)."</td>
					</tr>";
if($up_mode == "new_upload"){
/*
$Contents .=	"
					<tr height=30 align=center ".($up_mode == "new_upload" ? "style='display:none'":"").">
						<td class='input_box_title'>상품정보고시 분류 <a href =\"JavaScript:PoPWindow('./reg_guide.php',703,652,'comparewindow');\" ><img src ='../images/".$_SESSION["admininfo"]["language"]."/product_guide.gif' align=absmiddle></a></td>
						<td class='input_box_item' colspan=3>
							<select name='mandatory_type_1' id='mandatory_select_1' onchange='MandatoryChange(1)'>
							<option value='' ".($mi_code == "" ? "selected":"").">상품분류를 선택해주세요</option>";

						$sql = "select * from shop_mandatory_info where is_use = '1' order by mi_code ASC";
						$db->query($sql);
						$mandatory_array = $db->fetchall();
						for($i=0;$i<count($mandatory_array);$i++){
							$Contents .="
							<option value='".$mandatory_array[$i][mi_code]."' ".($mandatory_array[$i][mi_code] == $mi_code ? "selected":"").">".$mandatory_array[$i][mandatory_name]."</option>";
						}
	$Contents .="
						</select>
						<input type='hidden' name='mandatory_type_2' id='mandatory_type_2' value='1'>
						</td>

					</tr>";
*/
}
$Contents .=	"<tr height=30 align=center>
						<td class='input_box_title'><b>번역 구분</b></td>
						<td class='input_box_item' colspan=3>
							".getTranslationType($trans_type,"")."
						</td> 

					</tr>
					<tr height=30 align=center>
						<td class='input_box_title' ><b>엑셀파일 입력</b>  </td>
						<td class='input_box_item' colspan=3><input type=file class='textbox' name='excel_file' style='height:22px;width:90%' validation=true title='엑셀파일 입력'></td>
					</tr> 
					</table>
					<table width='100%' border=0 cellpadding=0 cellspacing=1 >
					<tr height=20>
						<td style='padding:6px;line-height:140%;' colspan=2><img src='../image/emo_3_15.gif' border=0 align=absmiddle>엑셀정보등록시 <b>A</b>:구분, <b>B</b>:파일경로, <b>C</b>:파일명, <b>D</b>:trans_key, <b>E</b>:한글문구, <b>F</b>:번역문구 항목으로 맞추어서 등록해주세요.
						<br/><img src='../image/emo_3_15.gif' border=0 align=absmiddle><b>A</b>:구분, <b>B</b>:파일경로, <b>C</b>:파일명 는 필수가 아닙니다.</td></tr>
					<tr height=30><td colspan=2 style='padding:10px 0px;' align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0></td></tr>
				</table>
				</form>
			 	</td>
			 </tr>";
}

if($up_mode == "new_upload"){
$Contents .= "<tr>
						<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 업로드 엑셀정보 </b>&nbsp;</div>")."</td>
					</tr>
			<tr>
			 	<td colspan=3 align=left style='padding-bottom:10px;'><div style='width:1400px;height:800px;overflow:auto;'>".MakeUploadExcelData()."</div></td>
			 </tr>
			 <tr>
			 	<td colspan=3 align=center style='padding-bottom:10px;'><img src='../image/goods_d_btn1.gif' alt='상품등록하기' onclick='TranslationUploadExcelReg();' style='cursor:pointer;'/></div></div></td>
			 </tr>

			 ";
}
$Contents .= "
			 
			<tr>
			<td valign=top style='padding-top:33px;'>";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_list>
			
			</td>
			</tr>
		</table>
			";
 
} // up_mode == upload 일때


$Contents = $Contents.$innerview ."
			 

			";
 


if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><script src='../js/jquery-1.8.3.js'></script><body>$innerview</body></html>";
	//<script src='../js/jquery-1.8.3.js'></script> combobox() 사용때문에 추가 kbk 13/04/16

	//$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "<Script language='javascript'>
		parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
		parent.document.getElementById('select_category_path1').innerHTML='".$inner_category_path."';
		parent.document.getElementById('select_category_path2').innerHTML='".$inner_category_path."(".$total."개) ';
		parent.document.getElementById('select_category_path3').innerHTML='".$inner_category_path."';
		parent.document.forms['excel_input_form'].cid.value = '".$cid."';
		parent.document.forms['excel_input_form'].depth.value = '".$depth."';
		parent.LargeImageView();
		parent.unblockLoadingBox();
		</Script>";
}else{
	$P = new LayOut();
	$P->strLeftMenu = global_menu("/admin",$category_str);
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='translation_input_excel.js'></script> ";
	$P->Navigation = "글로벌 > 번역관리 > 번역엑셀등록";
	$P->title = "번역엑셀등록";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}




function Category()
{
$mdb = new Database;

	global $id;

$m_string = "
<script language='JavaScript' src='../include/manager.js'></script>
<script language='JavaScript' src='../include/Tree.js'></script>
<script>

/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = 'black';
	tree.bgColor = 'white';
	tree.borderWidth = 0;


/*	Create Root node	*/
	var rootnode = new TreeNode('상품카테고리', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');
	rootnode.action = \"setCategory('product category','000000000000000',-1,'".$id."')\";
	rootnode.expanded = true;";

$mdb->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $mdb->total;
for ($i = 0; $i < $mdb->total; $i++)
{

	$mdb->fetch($i);

	if ($mdb->dt["depth"] == 0){
		$m_string = $m_string.PrintNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
	}else if($mdb->dt["depth"] == 1){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
	}else if($mdb->dt["depth"] == 2){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
	}else if($mdb->dt["depth"] == 3){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
	}else if($mdb->dt["depth"] == 4){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
	}
}

	$m_string = $m_string."tree.addNode(rootnode);";

$m_string = $m_string."
</script>
<form>
<div id=TREE_BAR style='margin:5px;'>
<script>
tree.draw();
tree.nodes[0].select();
</script>
</div>
</form>";

return $m_string;
}




function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);


	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($cname,$cid,$depth)
{
	global $id;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('$cname','$cid',$depth,'$id')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$mcid,$depth)
{
	global $id,$cid;
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
	$cname = str_replace("'","&#39;",$cname);

	return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.expanded = $expandstring;
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}

?>