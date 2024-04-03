<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include_once("brand.lib.php");

//auth(8);
$download_excel_file = "../product/company_batch_sample.xls";

$db = new Database;
$db2 = new Database;

$Contents =	"
			<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
				<td align='left' colspan=4> ".GetTitleNavigation("재고상품 대량 등록/수정", "재고관리 > 재고상품 대량 등록/수정")."</td>
			</tr>
			<tr>
				<td align='left' colspan=4 style='padding-bottom:15px;'>
					<div class='tab'>
						<table class='s_org_tab'>
							<tr>
								<td class='tab'>

									<table id='tab_01'  ".($info_type == "list" || $info_type == "" ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02'  ><a href='company_list.php?mmode=".$mmode."&info_type=list'>제조사 리스트</a></td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='tab_02' ".($info_type == "add" ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' ><a href='company.php?mmode=".$mmode."&info_type=add'>제조사 등록</a></td>
										<th class='box_03'></th>
									</tr>
									</table>
					
									<table id='tab_04' ".($info_type == "category" ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' ><a href='company_div.php?mmode=".$mmode."&info_type=category'>제조사 분류관리</a></td>
										<th class='box_03'></th>
									</tr>
									</table>
									
									<table id='tab_05' ".($info_type == "batch" ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' ><a href='company_batch.php?mmode=".$mmode."&info_type=batch'>제조사 일괄등록</a></td>
										<th class='box_03'></th>
									</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>";
$Contents .= "
			<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'> 샘플을 참고해서 엑셀문서를 만들어 주세요.</b></div>")."</td>
			</tr>
			<tr>
				<td colspan=3>
				<form name='excel_input_form' method='post' action='company_batch.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' >
				<input type='hidden' name='act' value='excel_input'>
				<!--input type='hidden' name='position' value='overseas'-->
				<table width='100%' border=0 cellpadding=0 cellspacing=1 bgcolor=silver>
					<tr height=28 bgcolor=#ffffff align=center>
						<td bgcolor=#efefef><b>엑셀파일 대량<span class='blue'>등록</span></b>  </td>
						<td align=left style='padding-left:10px;'><input type=file class='textbox' name='excel_file' style='width:90%' validation=true title='엑셀파일 입력'></td>
						<td width=20%><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0></td>
					</tr>
				</table>
				</form>
				</td>
			</tr>
			<!--
			<tr>
				<td colspan=3>
				<form name='excel_input_form' method='post' action='brand_batch.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' >
				<input type='hidden' name='act' value='excel_update'>
				<table width='100%' border=0 cellpadding=0 cellspacing=1 bgcolor=silver style='margin-top:10px;'>
					<tr height=28 bgcolor=#ffffff align=center>
						<td bgcolor=#efefef><b>엑셀파일 대량<span class='red'>수정</span></b>  </td>
						<td align=left style='padding-left:10px;'><input type=file class='textbox' name='excel_file' style='width:90%' validation=true title='엑셀파일 입력'></td>
						<td width=20%><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0></td>
					</tr>
				</table>
				<br/>
				* 엑셀파일 대량수정시 주의사항 : <span class='red'>품목리스트에서 엑셀을 다운 받으시면 단위정보 단위는 수정 하지 말아주세요.</span>
				</form>
				</td>
			</tr>-->
			<tr>
				<td style='padding:30px 0px 0px 0px'>
				<table width=100%  border=0>
					<tr height=25>
						<td style='border-bottom:2px solid #efefef'>
						<img src='../images/dot_org.gif' align=absmiddle> <b>샘플작성 참고사항</b>
						</td>
					</tr>
					<tr height=25>
						<td >
						엑셀정보에는 ' 따옴표를 사용하실 수 없습니다.
						</td>
					</tr>
					<tr height=25>
						<td style='line-height:140%;padding:10px 0px 0px 0px'>
						<font color='red'>** 엑셀 문서는 엑셀97~2003 통합문서로 저장해 주세요.(확장자 xls)</font> 
						<a href='".$download_excel_file."'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a>
						<br><br>
						1. 제조사명 <br><br>
						   - 기준이 되는 코드입니다.<br><br>
						2. 제조사코드  <br><br>
						   - 제조사코드를 입력하시면 됩니다.<br><br>
						3. 사용유무  <br><br>
						   - 사용 : 1 미사용 : 0 숫자만 입력하시면 됩니다.<br><br>
						4. 신청상태  <br><br>
						   - 승인거부 : 0 승인 : 1 신청중 : 2 신청보류 : 3 숫자만 입력하시면 됩니다.<br><br>
						5. 상품카테고리 <img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"$('#cid_list').toggle();\"  style='cursor:pointer;'> <br><br>
						   - 코드를 찾아 정확히 넣어주세요. 예): 001001000000000|001002000000000|001004000000000<br>
						   - '|' 로 구분해주세요.<br><br>
						   <div id='cid_list' style='display:none;'>
								<table style='width:300px;'>
									<tr><td>상품 분류명</td><td>분류코드</td></tr><br>";

								$db->query("select * from shop_category_info where category_use='1' order by cid , vlevel1 , vlevel2 , vlevel3 , vlevel4 , vlevel5");
								for($i=0;$i<$db->total;$i++){
									$db->fetch($i);
									$Contents .= "<tr><td>".str_repeat('&nbsp;&nbsp;',($db->dt[depth]+1))." ".$db->dt[cname]."</td><td>".$db->dt[cid]."</td></tr>";
								}
				$Contents .= "
								</table>
								 <br><br>
							</div>
						6. 제조사 분류코드 <img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"$('#div_list').toggle();\"  style='cursor:pointer;'> <br><br>
						   -제조사 코드를 찾아 정확히 넣어주세요.<br><br>
						   <div id='div_list' style='display:none;'>
								<table style='width:300px;'>
									<tr><td>제조사 분류명</td><td>분류코드</td></tr><br>";
								$db->query("select * from shop_company_div where depth='1' order by parent_cd_ix , cd_ix , depth ASC");
								for($i=0;$i<$db->total;$i++){
									$db->fetch($i);
									$Contents .= "<tr><td> ".$db->dt[div_name]."</td><td>".$db->dt[cd_ix]."</td></tr>";
									$sql = "select * from shop_company_div where parent_cd_ix = '".$db->dt[cd_ix]."'";
									$db2->query($sql);
									$parent_array = $db2->fetchall();
									if(count($parent_array) > 0){
										for($j = 0;$j<count($parent_array);$j++){
											$Contents .= "<tr><td>".str_repeat('&nbsp;&nbsp;',($db->dt[depth]+1))." ".$parent_array[$j][div_name]."</td><td>".$parent_array[$j][cd_ix]."</td></tr>";
										}
									}
								}
				$Contents .= "
								</table>
								<br><br>
							</div>
						7. 제조사 간략설명  <br><br>
						   - 제조사 간략설명을 입력하시면 됩니다.<br><br>
						8. 제조사 우편코드 <img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"company_zipcode('7')\" style='cursor:pointer;'> <br><br>
						   - 제조사 우편코드를 입력하시면 됩니다.<br><br>
						9. 제조사 상세주소1  <img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"company_zipcode('7')\" style='cursor:pointer;'>  <br><br>
						   - 제조사 상세주소1을 입력하시면 됩니다.<br><br>
						10. 제조사 상세주소2  <img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"company_zipcode('7')\" style='cursor:pointer;'>  <br><br>
						   - 제조사 상세주소2을 입력하시면 됩니다.<br><br>
				</table>
				</td>
			</tr>
			</table>";
$Contents .=	"";


//$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
$script = "
<script Language='JavaScript'>
function company_zipcode(type){
	var zip = window.open('../member/zipcode.php?type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}
</script>
";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->strLeftMenu = product_menu();
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='product_input_excel.js'></script>".$script;
	$P->Navigation = "상품관리 > 제조사 대량 등록/수정";
	$P->NaviTitle = "제조사 대량 등록/수정";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->strLeftMenu = product_menu();
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='product_input_excel.js'></script>".$script;
	$P->Navigation = "상품관리 > 제조사 대량 등록/수정";
	$P->title = "제조사 대량 등록/수정";
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