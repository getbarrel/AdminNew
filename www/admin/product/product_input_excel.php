<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include("../product/product_input_excel.lib.php");

if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;
$db2 = new Database;


if($admininfo[admin_level] != '9'){	//셀러 대량상품 등록시 기본배송정책 미설정시 배송정책 페이지로 이동 2014-07-17 이학봉
	$sql = "select * from shop_delivery_template where company_id = '".$admininfo[company_id]."' and is_basic_template = '1' and product_sell_type = 'R'";
	$db->query($sql);
	if(!$db->total){
		echo "<script language='JavaScript'>alert('대량상품등록을 위해서는 기본 배송정책 설정이 필요합니다. ');document.location.href='../seller/seller_delivery_info.php'; </script>";
		exit;
	}
}

if(!$up_mode){
	$up_mode="new_upload";
}

$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
				<td align='left' colspan=4 > ".GetTitleNavigation("대량상품등록", "상품관리 > 대량상품등록")."</td>
			</tr>
			<tr>
				<td align='left' colspan=4 style='padding-bottom:15px;'>
					<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_02' ".($up_mode=="new_upload" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?up_mode=new_upload'\">신규 대량상품등록(상품정보고시 분류 포함)</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($up_mode=="download" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?up_mode=download'\">샘플다운로드 (상품정보고시 분류 포함) </td>
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
				<td colspan=3>
					<table width='100%' cellpadding=0 cellspacing=0 border='0'>
						<tr>
							<td  height='25' bgcolor=#ffffff>
								<img src='../images/dot_org.gif' align=absmiddle> <b >샘플다운로드</b> 
								대량등록에 필요한 코드를 다운받아서 사용하시면 됩니다.
							</td>
						</tr>
						<tr>
							<td  height='25' style='padding:5px 0px;' bgcolor=#ffffff>
								<img src='../images/dot_org.gif' align=absmiddle> <b >현재 귀사의 코드는 </b> [ ".$admininfo[company_id]." ] 입니다.
							</td>
						</tr>
					</table>
					<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
						<col width=20%>
						<col width=30%>
						<col width=20%>
						<col width=30%>
						<tr height=30 align=center>
							<td class='input_box_title'><b>상품등록 양식</b></td>
							<td class='input_box_item' id='select_category_path3' align=left style='padding-left:10px;' colspan=3>";
							if($admininfo["com_type"] == "A"){
							$Contents .="
								<img src='../images/".$admininfo["language"]."/btn_mgr_basic.gif' align=absmiddle onclick=\"location.href='batch_products_upload_excel_v0.3.xlsx'\" style='cursor:pointer;'>
								<!-- img src='../images/".$admininfo["language"]."/btn_mgr_wms.gif' align=absmiddle onclick=\"location.href='batch_products_upload_excel_wms.xls'\" style='cursor:pointer;'> 
								<img src='../images/".$admininfo["language"]."/btn_seller.gif' align=absmiddle onclick=\"location.href='batch_products_upload_excel_seller.xls'\" style='cursor:pointer;' -->";
							}else{
							$Contents .="
								<!-- img src='../images/".$admininfo["language"]."/btn_seller.gif' align=absmiddle onclick=\"location.href='batch_products_upload_excel_seller.xls'\" style='cursor:pointer;' -->";
							}

$Contents .="
							</td>
						</tr> 
						<tr height=30 align=center style='display:none;'>
							<td class='input_box_title'  ><b>상품고시 정보 코드</b></td>
							<td class='input_box_item' id='select_category_path3' align=left style='padding-left:10px;' colspan=3>
								<img src='../images/".$admininfo["language"]."/btn_quick_view.gif' align=absmiddle onclick=\"JavaScript:PoPWindow3('./reg_guide.php',703,652,'comparewindow');\" style='cursor:pointer;'> 
								<a href='product_input_excel_2003.act.php?act=mandatory_down'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a>
							</td>
						</tr>
						<tr height=30 align=center style='display:none;'>
							<td class='input_box_title'  ><b>기타 코드</b></td>
							<td class='input_box_item' id='select_category_path3' align=left style='padding-left:10px;' colspan=3>
								<a href='product_input_excel_2003.act.php?act=ect_code_down'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a>
								<span class='blu'>* 카테고리,MD,원산지,제조사,브랜드 코드를 다운로드 받을수 있습니다.</span>
							</td>
						</tr>
					</table>
					<table width='100%' border=0 cellpadding=0 cellspacing=1 >
						<tr height=20>
							<td style='padding:6px;line-height:140%;' colspan=2>
								<img src='../image/emo_3_15.gif' border=0 align=absmiddle> 
								상품정보고시 분류를 선택하신후 샘플 다운로드 버튼을 클릭하시면 해당포맷에 맞는 엑셀 샘플파일이 다운로드 됩니다.
							</td>
						</tr>
					</table>

				</td>
			</tr>";
}else{

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || true){

		if($admininfo[mall_type] == "BW"){
			$download_excel_file = "batch_product_upload_example_wholesale.xls";
		}else{
			$download_excel_file = "batch_product_upload_example.xls";
		}

$Contents .="
			<tr>
				<td colspan=3>

				<form name='excel_input_form' method='post' action='product_input_excel_2003.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''><!--iframe_act-->
				<input type='hidden' name='act' value='".($up_mode == "new_upload" ? "new_excel_input":"excel_input")."'>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='depth' value=''>
				<input type='hidden' name='page_type' value='input'>
				<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
				<col width=18%>
				<col width=*>";
    /*
	if($_SESSION["admin_config"][front_multiview] == "Y"){
	$Contents .= "
	<tr>
		<td class='input_box_title' > 프론트 전시 구분</td>
		<td class='input_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
	</tr>";
	}
	*/
	$Contents .= "";

if($admininfo[admin_level] == '9' && false){
$Contents .="
				<tr height=30 align=center>
					<td class='input_box_title'>셀러선택</td>
					<td class='input_box_item'>
						".companyAuthList($company_id , "validation=true title='셀러업체' ")."
					</td>
				</tr>";

}else{
$Contents .="	<input type='hidden' name='company_id' id='company_id' value='".$admininfo[company_id]."'";
}

$Contents .="
				<tr height=30 align=center>
					<td class='input_box_title' ><b>엑셀파일 입력</b></td>
					<td class='input_box_item'>
						<input type=file class='textbox' name='excel_file' style='height:22px;width:200px;' validation=true title='엑셀파일 입력'>
						* 	batch_product_upload_example.xls ( 엑셀 저장시 97~03년 양식으로 저장하시고 등록하세요.)
					</td>
				</tr>
				<tr height=30 align=center>
					<td class='input_box_title' ><b>상품 이미지 입력</b></td>
					<td class='input_box_item'>
						<input type=file class='textbox' name='goods_img_file' style='height:22px;width:200px;' validation=false filetype='zip' title='상품이미지 입력'>
						* batch_goods_image.zip ( zip 파일로 압축하여 저장하세요.)
					</td>
				</tr>
				</table>
				<table width='100%' border=0 cellpadding=0 cellspacing=1>
				<tr height=20>
					<td style='padding:6px;line-height:140%;' colspan=2>
						<div>
						<ol>
							<li>
								<img src='../image/emo_3_15.gif' border=0 align=absmiddle>
								엑셀정보에는 <b>' (따옴표)</b>는 사용하실수 없습니다. <b> 엑셀정보내에 카테고리 정보를 등록해 놓으면 해당 카테고리로 상품이 자동등록됩니다.</b><!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."-->
							</li>
							<li style='padding-left:20px;'>
								
								배송정책은 <b>기본 정책</b>으로 자동 설정되며, 개별로 등록시에는  <b>상품일괄수정 > 배송정책 </b>에서 손쉽게 변경할수 있습니다.
							</li>
							<li style='padding-left:20px;'>
								
								카테고리 미등록시  <b>상품등록 > 미분류상품</b>에서 손쉽게 변경할수 있습니다.
							</li>
						</ol>
						</div>

					</td>
				</tr>
				<tr height=30>
					<td colspan=2 style='padding:10px 0px;' align=center>
						<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>
					</td>
				</tr>
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
				<td colspan=3 align=left style='padding-bottom:10px;'><div style='width:1200px;height:300px;overflow:auto;'>".MakeUploadExcelData()."</div></td>
			</tr>
			<tr>
				<td colspan=3 align=center style='padding-bottom:10px;'><img src='../image/goods_d_btn1.gif' alt='상품등록하기' onclick=\"UploadExcelGoodsReg('input');\" style='cursor:pointer;'/></div></div></td>
			</tr>";
}

/*
$mode = 'search';
include "product_query.php";
		if($mode == "search"){
			$str_page_bar = page_bar_search($total, $page, $max, "&max=$max&company_id=$company_id&cid=$cid&depth=$depth");
		}else{
			$str_page_bar = page_bar($total, $page,$max, "&max=$max&company_id=$company_id&cid=$cid&depth=$depth");
		}

$Contents .= "
			<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 상품 리스트 :</b>&nbsp;<b id='select_category_path2'>전체(".number_format($total)."개)</b></div>")."</td>
			</tr>
			<tr>
				<td valign=top style='padding-top:33px;'></td>
				<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

				$innerview = "
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
							<table cellpadding=0 cellspacing=0>
								<tr>
									<td>".CompanyList($company_id,"","max",$max."&up_mode=".$up_mode)."</td>
									<td style='padding-left:5px;'>
										<select style='height:20px;' name=max onchange=\"window.frames['act'].location.href='".$HTTP_URL."?up_mode=".$up_mode."&view=innerview&company_id=".$company_id."&max='+this.value\">
										<option value='10' ".CompareReturnValue(10,$max).">10</option>
										<option value='20' ".CompareReturnValue(20,$max).">20</option>
										<option value='50' ".CompareReturnValue(50,$max).">50</option>
										<option value='100' ".CompareReturnValue(100,$max).">100</option>
										</select> 씩 보기
									</td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
					</td>
					<td align=right></td>
				</tr>
				</table>

				<form name=listform method=post action='product_input_excel.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
				<input type='hidden' name='act' value='select_delete'>
				<table cellpadding=2 cellspacing=0 bgcolor=gray width=100%  class='list_table_box'>
				<col width='5%' >
				<col width='10%' >
				<col width='*'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='12%'>
				<col width='12%'>
				<col width='7%'>
				<tr bgcolor='#ffffff' align=center height=30>
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>등록일</td>
					<td class=m_td>상품정보</td>
					<td class=m_td>상품상세페이지</td>
					<td class=m_td>판매상태</td>
					<td class=m_td>공급가</td>
					<td class=m_td>도매가/할인가</td>
					<td class=m_td>소매가/할인가</td>
					<td class=e_td>관리</td>
				</tr>";

		if($db->total == 0){
			$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=9 align=center> 등록된 제품이 없습니다.</td></tr>";
		}else{
			for ($i = 0; $i < $db->total; $i++)
			{
				$db->fetch($i);

				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s", $db->dt);

				switch($db->dt[state]){	
					case '1':
						$state = "판매중";
					break;
					case '0':
						$state = "일시품절";
					break;
					case '2':
						$state = "판매중지";
					break;
					case '7':
						$state = "수정대기상품";
					break;
					case '6':
						$state = "승인대기";
					break;
					case '8':
						$state = "승인거부";
					break;
					case '9':
						$state = "판매금지";
					break;
				}

				if($db->dt[basicinfo] != ""){
					$basicinfo_state = "정상";
				}else{
					$basicinfo_state = "파일없음";
				}

	$innerview .= "	<tr bgcolor='#ffffff'>
						<td class='list_box_td list_bg_gray'><input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$db->dt[id]."'></td>
						<td class='list_box_td list_bg_gray' nowrap>".$db->dt[regdate]."<br>".$db->dt[company_name]."</td>
						<td class='list_box_td point' style='text-align:left;line-height:140%;'>
							<table>
								<tr>
									<td><a href='/shop/goods_view.php?id=".$db->dt[id]."' target='_blank' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], $LargeImageSize, $db->dt)."'><img src='".$img_str."' width=50 height=50></a></td>
									<td>
										".getCategoryPathByAdmin($db->dt[cid], 4)."<br>
										<a href='goods_input.php?id=".$db->dt[id]."'><b>".$db->dt[pname]."</b>(".$db->dt[pcode].")</a>
									</td>
								</tr>
							</table>
						</td>
						<td align=center class='small'>".$basicinfo_state."</td>
						<td align=center class='small'>".$state."</td>
						<td class='list_box_td list_bg_gray'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[coprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>

						<td class='list_box_td list_bg_gray'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[wholesale_price],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]." / ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[wholesale_sellprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>

						<td class='list_box_td list_bg_gray'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[listprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]." / ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[sellprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>

						<td class='list_box_td ' >";
							
								if(checkMenuAuth(md5("/admin/product/goods_input.php"),"U")){
									$innerview .= "
									<a href='goods_input.php?id=".$db->dt[id]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>";
								}else{
									$innerview .= "
									<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>";
								}

								if(checkMenuAuth(md5("/admin/product/goods_input.php"),"D")){
									$innerview .= "
									<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"deleteProduct('delete_excel','".$db->dt[id]."')\">";
								}else{
									$innerview .= "
									<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle ></a>";
								}
				$innerview .= "
						
						</td>
					</tr>";

			}
		}
$innerview .= "	</table>
				<table width='100%'>
					<tr height=30>";
						if(checkMenuAuth(md5("/admin/product/goods_input.php"),"D")){
							$innerview .= "<td><input type=image src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></td>";
						}else{
							//$innerview .= "<td><a href=\"".$auth_delete_msg."\"><img src='../image/bt_all_del.gif' border=0 align=absmiddle ></a></td>";
						}
$innerview .= "
					<td align=right>".$str_page_bar."</td></tr>
				</table>
				</form>
				";
*/
} // up_mode == upload 일때


$Contents = $Contents.$innerview ."
			<form name=vieworderform method=post action='./order.act.php'>
			<input type='hidden' name='vieworder'>
			<input type='hidden' name='_vieworder'>
			<input type='hidden' name='pid'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='category_load' value='$category_load'>
			<input type='hidden' name='depth' value='$depth'>
			</form>

			</td>
			</tr>
		</table>

			";
if($up_mode=='upload'||$up_mode==''){
	$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
}else{
	$category_str ="";
}

if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><script src='../js/jquery-1.8.3.js'></script><body>$innerview</body></html>";
	//<script src='../js/jquery-1.8.3.js'></script> combobox() 사용때문에 추가 kbk 13/04/16

	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "<Script language='javascript'>
		parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
		parent.document.getElementById('select_category_path2').innerHTML='".$inner_category_path."(".$total."개) ';
		parent.document.forms['excel_input_form'].cid.value = '".$cid."';
		parent.document.forms['excel_input_form'].depth.value = '".$depth."';
		parent.LargeImageView();
		parent.unblockLoadingBox();
		</Script>";
}else{
	$P = new LayOut();
	$P->strLeftMenu = product_menu("/admin",$category_str);
	$P->addScript = "<!--script Language='JavaScript' src='../include/zoom.js'></script--><script Language='JavaScript' src='product_input_excel.js'></script><script Language='JavaScript' src='../product/goods_mandatory_info.js'></script>";
	$P->Navigation = "상품관리 > 상품등록 > 대량상품등록";
	$P->title = "대량상품등록";
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