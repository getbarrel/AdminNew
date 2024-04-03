<?
//include("$DOCUMENT_ROOT/admin/class/admin.page.class");
include("../class/layout.class");
//auth(8);


$db = new Database;


$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=4> ".GetTitleNavigation("일괄송장입력", "상품관리 > 일괄송장입력")."</td>
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
											<td class='box_02' onclick=\"document.location.href='?acc_view_type='\">일괄송장입력</td>
											<th class='box_03'></th>
										</tr>
									</table>
									<table id='tab_02' >
										<tr>
											<th class='box_01'></th>";
						if($position=='overseas'){
							$Contents .= "
											<td class='box_02' onclick=\"document.location.href='air_transport_ready.php?fix_type_type=excel'\">항공상품준비중 상품목록</td>";
						}else{
							$Contents .= "
											<td class='box_02' onclick=\"document.location.href='delivery_ready.php'\">배송준비중 상품목록</td>";
						}
$Contents .= "
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
			 	<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'> 상단 상품준비중 상품목록에서 엑셀 다운로드 후 택배사에 등록후 사용하실수 있습니다. <b class='red'>주문순번</b>과 <b class='red'>주문상세번호</b>와 혼동하시지 마세요. <br/> 액셀 파일 양식에 주문데이타 작성시 <b class='red'>첫행에 입력된 데이타</b>들은 변경처리가 되지 않습니다.</div>")."</td>
			 </tr>
			 <tr>
			 	<td colspan=3>

				<form name='excel_input_form' method='post' action='../order/invoice_input_excel.act.php' enctype='multipart/form-data' target='iframe_act' onsubmit='return CheckFormValue(this)' >
			 	<input type='hidden' name='act' value='check_excel_input'>
				<input type='hidden' name='position' value='$position'>
				<input type='hidden' name='op_admin_level' value='".$admininfo[admin_level]."'>
				<input type='hidden' name='op_company_id' value='".$admininfo[company_id]."'>


				<table width='100%' border=0 cellpadding=0 cellspacing=1 bgcolor=silver class='search_table_box'>
					<tr height=28 bgcolor=#ffffff align=center>
						<td bgcolor=#efefef width=20%><b>송장번호업데이트</b></td>
						<td id='select_category_path3' align=left style='padding-left:10px;'>송장번호가 등록되어 있는 주문에도 다시 업데이트 합니다. </td>
						<td rowspan=4 width=20%>
						<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>
						<!--img src='../images/".$admininfo["language"]."/b_save.gif' onclick=\"alert('일괄 송장입력(국내) 기능을 업데이트 중입니다. 잠시만 기다려 주세요. 예상시간 오전 11:30 분.');\" border=0-->
						</td>
					</tr>
					<tr height=28 bgcolor=#ffffff  align=center>
						<td bgcolor=#efefef><b>처리상태</b>  </td>
						<td align=left style='padding-left:10px;'>";

							if($view_type == 'inventory'){
								$Contents .= "
								<input type='radio' name='update_status' id='status_' value='' checked ><label for='status_'>송장번호입력</label>";
							}else{
								$Contents .= "
								<input type='radio' name='update_status' id='status_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' checked><label for='status_".ORDER_STATUS_DELIVERY_ING."' >송장번호입력 후 배송중</label>
								<input type='radio' name='update_status' id='status_' value=''  ><label for='status_'>송장번호입력</label>";
							}
							
						$Contents .= "
						</td>
					</tr>
					<tr height=28 bgcolor=#ffffff  align=center>
						<td bgcolor=#efefef><b>엑셀파일 입력</b>  </td>
						<td align=left style='padding-left:10px;'><input type=file class='textbox' name='excel_file' style='width:90%' validation=true title='엑셀파일 입력' filetype='excel2003'></td>
					</tr>
					<tr height=28 bgcolor=#ffffff  align=center>
						<td bgcolor=#efefef><b>주의 사항</b></td>
						<td align=left style='padding-left:10px;'><span style='color:red'>엑셀파일은 Excel 97 - 2003 통합문서로 저장된 문서만 사용 가능합니다. </span></td>
					</tr>
				</table>
				</form>
			 	</td>
			 </tr>
			 <tr>
				<td style='padding:30px 0px 0px 0px'>
				<div id='result_area' style='padding-bottom:10px;'></div>
				<table width=100%  border=0>
					<col width='50%' />
					<col width='50%' />
					<tr height=25>
						<td style='border-bottom:2px solid #efefef' colspan='2'>
						<img src='../images/dot_org.gif' align=absmiddle> <b>샘플작성 참고사항</b>
						</td>
					</tr>
					<tr height=25>
						<td colspan='2'>
						엑셀정보에는 \" 따옴표를 사용하실 수 없습니다.
						</td>
					</tr>
					<tr height=25>
						<td colspan='2'>
						<table width=100%  border=0 cellpadding=0 cellspacing=1 class='list_table_box'>
							<tr height=25 bgcolor=#ffffff align=center>
								<td class='s_td'>주문번호</td>
								<td class='m_td'>주문상세번호</td>
								<td class='m_td'>택배사 코드</td>
								<td class='e_td'>운송장번호</td>
							</tr>
							<tr height=25 bgcolor=#ffffff align=center>
								<td >201103102018-12312</td>
								<td class='point'>1429832 </td>
								<td >1 </td>
								<td class='point'>1234123113455</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr height=25>
						<td style='line-height:140%;padding:10px 0px 0px 0px' colspan='2'>

						1. 주문번호 : 주문번호를 – 포함하여 정확히 입력해 주세요. ( A 열에 입력해 주세요)<br>
						2. 주문상세 : 주문상세번호를 – 포함하여 정확히 입력해 주세요. ( B 열에 입력해 주세요)<br>
						3. 택배사 코드 : 아래 택배사 코드표를 참고하시어 입력해 주시기 바랍니다. ( C 열에 입력해 주세요)<br>
						4. 운송장번호 : 운송장 번호를 정확히 입력해 주세요.( D 열에 입력해 주세요)<br>
						5. 일괄송장등록은 배송준비중인 상품에 대해서만 일괄 업데이트 됩니다. <br>

						</td>
					</tr>
					<tr height=25>
						<td style='line-height:140%;padding:10px 0px 0px 20px'>
						".deliveryCompanyList("","table")."
						</td>
						<!--td align='left' valign='top'>
							<table width='250'  border=0 cellpadding=0 cellspacing=1 class='list_table_box'>
								<tr height=25 bgcolor=#ffffff >
									<td width='150' style='padding-left:5px;'>택배</td>
									<td width='100' align=center>TE</td>
								</tr>
								<tr height=25 bgcolor=#ffffff >
									<td style='padding-left:5px;'>퀵서비스</td>
									<td align=center>QU</td>
								</tr>
								<tr height=25 bgcolor=#ffffff >
									<td style='padding-left:5px;'>용달or개인트럭</td>
									<td align=center>TR</td>
								</tr>
								<tr height=25 bgcolor=#ffffff >
									<td style='padding-left:5px;'>직접방문</td>
									<td align=center>SE</td>
								</tr>
								<tr height=25 bgcolor=#ffffff >
									<td style='padding-left:5px;'>직배송</td>
									<td align=center>DI</td>
								</tr>
							</table>
						</td-->
					</tr>
				</table>
				</td>
			 </tr>
			 </table>";


$Contents .=	"";


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

	if($view_type == 'inventory'){
		$P->strLeftMenu = inventory_menu("/admin",$category_str);
	}else{
		$P->strLeftMenu = order_menu("/admin",$category_str);
	}
	$P->addScript = "";
	$P->Navigation = "주문관리 > 일괄송장입력";
	$P->title = "일괄송장입력";
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