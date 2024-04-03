<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include("buying.lib.php");


$db = new Database;


$Contents =	"
			<form name='excel_input_form' method='post' action='invoice_input_excel.act.php' enctype='multipart/form-data' target='iframe_act' onsubmit='return CheckFormValue(this)' >
			 	<input type='hidden' name='act' value='excel_input'>
			 	<input type='hidden' name='cid' value=''>
			 	<input type='hidden' name='depth' value=''>
			<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=4> ".GetTitleNavigation("사입대량등록", "사입관리 > 사입신청관리 > 사입대량등록")."</td>
			</tr>";

$Contents .= "
			<tr>
				<td align='left' colspan=4 style='padding-bottom:20px;'>
					<div class='tab'>
						<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='tab_01' class='on'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?acc_view_type='\">사입대량등록</td>
											<th class='box_03'></th>
										</tr>
									</table>
									<table id='tab_02' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='buying_list.php'\">사입신청목록</td>
											<th class='box_03'></th>
										</tr>
									</table>

								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			 <tr>
			 	<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'> 샘플을 다운로드 받아 작성해주시기 바랍니다.<a href='batch_buyinginfo_upload_example.xls'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a></b></div>")."</td>
			 </tr>
			 <tr>
				<td align='left' colspan=3 style='padding-bottom:14px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'>
				  <col width=15% >
				  <col width=35%>
				  <col width=15%>
				  <col width=35%>
				  <tr bgcolor=#ffffff >
					<td class='input_box_title'> 사입자 정보 </td>
					<td class='input_box_item'>
						<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=hidden name='mem_ix' id='mem_ix' value=''".$buyingservice_apply_info[mem_ix]."'' style='width:100px;'></td>
							<td><input type=text class='textbox' id='buying_mem_name' name='buying_mem_name' value='".$buyingservice_apply_info[buying_mem_name]."' style='width:100px;' onclick=\"PoPWindow('./member_search.php?code=".$db->dt[code]."',600,380,'member_search')\" readonly></td>
							<td style='padding-left:5px;'><img src='../v3/images/".$admininfo["language"]."/btn_member_search.gif' align=absmiddle onclick=\"PoPWindow('./member_search.php?code=".$db->dt[code]."',600,380,'sendsms')\"  style='cursor:pointer;'></td>
						</tr>
					</table>
					</td>
					<td class='input_box_title'> 사입 신청일 </td>
					<td class='input_box_item'>
						<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
							<col width=70>
							<col width=*>
							<tr>
								<TD nowrap>
								<input type=text class='textbox' name='apply_date' id='apply_date'  value='".$buyingservice_apply_info[apply_date]."' style='width:95%;'>
								</TD>
								
								<TD style='padding:0px 10px'>
									<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
									<a href=\"javascript:select_date('$tommorw','$today',1);\">내일</a>
									<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
									<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
								</TD>
							</tr>
						</table>
						</td>
				  </tr>
				  <tr>
						<td class='input_box_title'>  <b>처리상태</b>  </td>
						<td class='input_box_item' colspan=3>";
							$Contents .= "<select name='buying_status'>";
							$Contents .= "<option value=''>사입 처리상태</option>";
						foreach($_buyingservice_status as $key => $value){
							$Contents .= "<option value='".$key."' ".ReturnStringAfterCompare($key, $buyingservice_apply_info["buying_status"], " selected").">". $value."</option>";
						}
							$Contents .= "</select>";
				$Contents .= "
						</td>
					</tr>
					<tr height=28 bgcolor=#ffffff align=center>
						<td class='input_box_title'><b>엑셀파일 입력</b>  </td>
						<td class='input_box_item' colspan=3  align=left style='padding-left:5px;'><input type=file class='textbox' name='excel_file' style='width:90%' validation=true title='엑셀파일 입력'></td>
					</tr>
					
				  </table>
				</td>
			</tr>
			<tr height=28 bgcolor=#ffffff align=center>
				<td class='input_box_item' style='text-align:center;padding:5px 5px;'><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0></td>
			</tr>
			 <tr>
				<td style='padding:30px 0px 0px 0px'>
				<table width=100%  border=0>
					<tr height=25>
						<td style='border-bottom:2px solid #efefef'>
						<img src='../images/dot_org.gif' align=absmiddle> <b class=blk>샘플작성 참고사항</b>
						</td>
					</tr>
					<tr height=25>
						<td >
						엑셀정보에는 \" 따옴표를 사용하실 수 없습니다.
						</td>
					</tr>
					<tr height=25>
						<td >
						<table width=100%  border=0 cellpadding=0 cellspacing=1 class='list_table_box'>
							<tr height=25 bgcolor=#ffffff align=center>
								<td class='s_td'>도매처</td>
								<td class='m_td'>장기명</td>
								<td class='m_td'>색상</td>
								<td class='m_td'>사이즈</td>
								<td class='m_td'>수량</td>
								<td class='m_td'>도매가</td>
								<td class='m_td'>대납금신청금</td>
								<td class='e_td'>반품교환</td>
							</tr>
							<tr height=25 bgcolor=#ffffff align=center>
								<td class='point'>도매처명</td>
								<td >양포켓 블라우스</td>
								<td >분홍색</td>
								<td >FREE</td>
								<td >2</td>
								<td >20000</td>
								<td >20000</td>
								<td class='point'>반품</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr height=25>
						<td style='line-height:140%;padding:10px 0px 0px 0px'>
						1. 도매처 : 도매처 이름을 정확히 입력해주세요<br>
						2. 장기명 : 사입을 원하는 상품의 장기명을 입력해주세요<br>
						3. 색상 : 해당상품의 색상을 입력해주세요. <br>
						4. 사이즈 : 사이즈를 입력해주세요.<br>
						5. 수량 : 수량을 입력해주세요.<br>
						6. 도매가 : 도매가를 입력해주세요.<br>

						</td>
					</tr>
					
				</table>
				</td>
			 </tr>
			 </table>
			 </form>";


$Contents .=	"";

$Script = "<script Language='JavaScript' >

function UploadExcel(){

}

</script>";
//$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";

if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";

	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "<Script language='javascript'>
		parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
		parent.document.getElementById('select_category_path1').innerHTML='".$inner_category_path."';
		parent.document.getElementById('select_category_path2').innerHTML='".$inner_category_path."(".$total."개) ';
		parent.document.getElementById('select_category_path3').innerHTML='".$inner_category_path."';
		parent.document.forms['excel_input_form'].cid.value = '".$cid."';
		parent.document.forms['excel_input_form'].depth.value = '".$depth."';
		</Script>";
}else{
	$P = new LayOut();
	$P->strLeftMenu = buyingservice_menu("/admin",$category_str);
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script>".$Script;
	$P->Navigation = "사입관리 > 사입신청 > 사입대량등록";
	$P->title = "사입대량등록";
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