<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("./inventory.lib.php");
//auth(8);
//print_r($admininfo);
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

if($admininfo[admin_level] == 9){
	if($company_id){
		$where = "where p.id Is NOT NULL and p.id = r.pid and p.stock_use_yn = 'Y' and p.admin ='".$company_id."' ";
	}else{
		$where = "where p.id Is NOT NULL and p.id = r.pid and p.stock_use_yn = 'Y'  ";
	}
}else{
	$where = "where p.id Is NOT NULL and p.id = r.pid  and p.stock_use_yn = 'Y' and p.admin ='".$admininfo[company_id]."' ";
}

	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}

	if($search_text != ""){
		$where = $where."and p.".$search_type." LIKE '%".$search_text."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

//echo $state;
	if($state2 != ""){
		//session_register("state");
		$where = $where." and p.state = ".$state2." ";
	}
	if($brand2 != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand2."";
	}

	/*if($brand_name != ""){
		$where .= " and brand_name LIKE '%".$brand_name."%' ";
	}*/

	if($brand != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand."";
	}

	if($cid2 != ""){
		//session_register("cid");
		//session_register("depth");
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}


if($stock_status == "soldout"){
	$stock_where = "and (stock = 0 or option_stock_yn = 'N') ";
}else if($stock_status == "shortage"){
	$stock_where = "and (stock < safestock or option_stock_yn = 'R') ";
}else if($stock_status == "surplus"){
	$stock_where = "and (stock > safestock or option_stock_yn = 'Y')";
}

switch ($depth){
	case 0:
		$cut_num = 3;
		break;
	case 1:
		$cut_num = 6;
		break;
	case 2:
		$cut_num = 9;
		break;
	case 3:
		$cut_num = 12;
		break;
	case 4:
		$cut_num = 15;
		break;
}

if ($cid2){
	$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%' ";
}
		
$sql = "select count(*) as total
				from ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r
				$where $stock_where ";

//echo $sql;
$db->query($sql);
$db->fetch();
$total = $db->dt[total];
//	echo $db->total;
	//exit;


if($orderby == "date"){
	$orderbyString = "order by p.regdate desc, vieworder2 asc,  id desc";
}else{
	$orderbyString = "order by vieworder2 asc, p.regdate desc, id desc";
}

if($mode == "excel"){
	$sql = "select p.id,  r.cid, p.pcode, p.pname, p.sellprice, p.coprice, p.regdate,p.vieworder,p.disp, p.surtax_yorn, po.opn_ix, stock, safestock, pi.place_name, option_name, option_div,option_code,option_price, option_stock, p.sell_ing_cnt, option_sell_ing_cnt, option_safestock,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			from  ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_PRODUCT." p  
			left join inventory_place_info pi on p.inventory_info = pi.pi_ix
			left join ".TBL_SHOP_PRODUCT_OPTIONS." po on p.id = po.pid and po.option_kind = 'b' 
			left join	".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on po.opn_ix = pod.opn_ix 
			$where $stock_where $orderbyString  ";
}else{
	$sql = "select p.id,  r.cid, p.pcode, p.pname, p.sellprice, p.coprice, p.regdate,p.vieworder,p.disp, p.surtax_yorn, p.sell_ing_cnt, stock, safestock, pi.place_name,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			from  ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_PRODUCT." p  left join inventory_place_info pi on p.inventory_info = pi.pi_ix
			$where $stock_where $orderbyString LIMIT $start, $max";
}

//echo nl2br($sql);
//exit;
$db->query($sql);

$goods_infos = $db->fetchall();

if($mode == "excel"){
	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	
	date_default_timezone_set('Asia/Seoul');
	
	$accounts_plan_priceXL = new PHPExcel();
	
	// 속성 정의
	$accounts_plan_priceXL->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('A' . 1, "번호");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('B' . 1, "상품코드");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('C' . 1, "카테고리");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('D' . 1, "상품명");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('E' . 1, "과세여부");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('F' . 1, "옵션이름");	
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('G' . 1, "옵션구분");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('H' . 1, "옵션코드");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('I' . 1, "보관장소");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('J' . 1, "재고");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('K' . 1, "출고예정재고");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('L' . 1, "안전재고");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('M' . 1, "진열");

	$before_pid = "";

	for ($i = 0; $i < count($goods_infos); $i++)
	{
		if(file_exists(PrintImage($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "m"))){
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "m");
		}else{
			$img_str = "../image/no_img.gif";
		}

		$accounts_plan_priceXL->getActiveSheet()->setCellValue('A' . ($i + 2), ($i + 1));
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('B' . ($i + 2), $goods_infos[$i][id]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('C' . ($i + 2), getCategoryPathByAdmin($goods_infos[$i][cid], 4));
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('D' . ($i + 2), $goods_infos[$i][pname]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('E' . ($i + 2), ($goods_infos[$i][surtax_yorn] == "Y" ? "면세(비과세)":"과세"));

		//PrintStockByOption($goods_infos[$i]);
	
		if($goods_infos[$i][opn_ix]){
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('F' . ($i + 2), $goods_infos[$i][option_name]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('G' . ($i + 2), $goods_infos[$i][option_div]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('H' . ($i + 2), $goods_infos[$i][option_code]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('I' . ($i + 2), $goods_infos[$i][place_name]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('J' . ($i + 2), $goods_infos[$i][option_stock]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('K' . ($i + 2), $goods_infos[$i][option_sell_ing_cnt]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('L' . ($i + 2), $goods_infos[$i][option_safestock]);

		}else{
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('F' . ($i + 2), $goods_infos[$i][option_name]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('G' . ($i + 2), $goods_infos[$i][option_div]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('H' . ($i + 2), $goods_infos[$i][pcode]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('I' . ($i + 2), $goods_infos[$i][place_name]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('J' . ($i + 2), $goods_infos[$i][stock]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('K' . ($i + 2), $goods_infos[$i][sell_ing_cnt]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('L' . ($i + 2), $goods_infos[$i][safestock]);
		}
		
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('M' . ($i + 2), ($goods_infos[$i][disp] == 1 ? "노출함":"노출안함"));

		
		
		$place_name = $goods_infos[$i][place_name];

		

	}

	// 첫번째 시트 선택
	$accounts_plan_priceXL->setActiveSheetIndex(0);
	
	// 너비조정
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="stock_report.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($accounts_plan_priceXL, 'Excel5');
	$objWriter->save('php://output');

	exit;
}
//print_r($_SERVER);
if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page, $max, "&".$_SERVER["QUERY_STRING"],"");
}else{
	$str_page_bar = page_bar($total, $page, $max, "&max=$max","");
}


$Contents =	"
<script  id='dynamic'></script>
<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
			    <td align='left' colspan=4 > ".GetTitleNavigation("재고현황", "재고관리 > 재고현황")."</td>
			</tr>
			<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='sprice' value='0' />
			<input type='hidden' name='eprice' value='1000000' />
			<tr height=150>
				<td colspan=2>
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:0px'>
								<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
									<col width='150' >
									<col width='*' >
									<col width='150' >
									<col width='*' >
									<tr>
										<td class='input_box_title'>  <b>선택된 카테고리</b>  </td>
										<td class='input_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
									</tr>
									<tr>
										<td class='input_box_title'><b>카테고리선택</b></td>
										<td class='input_box_item' colspan=3>
											<table border=0 cellpadding=0 cellspacing=0>
												<tr>
													<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
													<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
													<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
													<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
												</tr>
											</table>
										</td>
									</tr>
									";
									if($admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
										$Contents .=	"
									<tr>
										<td class='input_box_title'><b>입점업체</b></td>
										<td class='input_box_item'>".CompanyList2($company_id,"")."</td>
										<td class='input_box_title'><b>브랜드</b></td>
										<td class='input_box_item'>".BrandListSelect($brand, $cid)."<!--input type='text' name='brand_name'--></td>
									</tr>
									";
									}//".BrandListSelect4("","")."
										$Contents .=	"
									<tr>
										<td class='input_box_title'><b>진열</b></td>
										<td class='input_box_item'>
										<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
										<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
										<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
										</td>
										<td class='input_box_title'><b>판매상태</b></td>
										<td class='input_box_item'>
											<select name='state2' style='font-size:12px;height:22px;'>
												<option value=''>상태값선택</option>
												<option value='1' ".ReturnStringAfterCompare($state2, "1", " selected").">판매중</option>
												<option value='0' ".ReturnStringAfterCompare($state2, "0", " selected").">일시품절</option>
												<option value='6' ".ReturnStringAfterCompare($state2, "6", " selected").">등록신청중</option>
												<option value='7' ".ReturnStringAfterCompare($state2, "7", " selected").">수정신청중</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>  <b>검색어</b>  </td>
										<td class='input_box_item' valign='top' style='padding-right:5px;padding-top:7px;'>
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td><select name='search_type'  style=\"font-size:12px;height:22px;\">
																<option value='pname'>상품명</option>
																<option value='pcode'>상품코드</option>
																<option value='id'>상품코드(key)</option>
																</select>
																</td>
													<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox' value='' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
													<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
														<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
															<tr height=20>
																<td width=100%  style='padding:0 0 0 5'>
																	<table width=100% cellpadding=0 cellspacing=0 border=0>
																		<tr>
																			<td class='p11 ls1'>검색어 자동완성</td>
																			<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:hand;padding:0 10 0 0' align=right>닫기</td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr height=100% >
																<td valign=top bgColor=#efefef style='padding:0 6 5 6' colspan=2>
																	<table width=100% height=100% bgcolor=#ffffff>
																		<tr>
																			<td valign=top >
																			<div style='POSITION: absolute; overflow-y:auto;HEIGHT: 120px;' id='search_data_area'>
																				<TABLE id=search_table style='table-layout:fixed;'  width=100% cellSpacing=0 cellPadding=1 bgColor=#ffffff border=0>
																				<TBODY id=search_table_body></TBODY>
																				</TABLE>
																			<div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
														</DIV>
													</td>
													<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
												</tr>
											</table>
										</td>
										<td class='input_box_title'><b>목록갯수</b></td>
										<td class='input_box_item'><select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
										<option value='5' ".CompareReturnValue(5,$max).">5</option>
										<option value='10' ".CompareReturnValue(10,$max).">10</option>
										<option value='20' ".CompareReturnValue(20,$max).">20</option>
										<option value='50' ".CompareReturnValue(50,$max).">50</option>
										<option value='100' ".CompareReturnValue(100,$max).">100</option>
										</select> <span class='small'>한페이지에 보여질 갯수를 선택해주세요</span>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'><b>재고상태</b></td>
										<td class='input_box_item' >
										<input type='radio' name='stock_status' value='whole' id='owhole' ".CompareReturnValue("whole","$stock_status"," checked")."><label for='owhole'>전체</label>
										<input type='radio' name='stock_status' value='soldout' id='osoldout' ".CompareReturnValue("soldout","$stock_status"," checked")."><label for='osoldout'>품절</label>
										<input type='radio' name='stock_status' value='shortage' id='oshortage' ".CompareReturnValue("shortage","$stock_status"," checked")."><label for='oshortage'>부족</label>
										<input type='radio' name='stock_status' value='surplus' id='osurplus' ".CompareReturnValue("surplus","$stock_status"," checked")."><label for='osurplus'>여유</label>
										</td>
										<td class='input_box_title'></td>
										<td class='input_box_item' >

										</td>
									</tr>
								</table>
							</td>
							<th class='box_06'></th>
						</tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08'></td>
							<th class='box_09'></th>
						</tr>
					</table>
				</td>
			</tr>
			<tr >
				<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
				</form>
			</tr>
			<tr>
			    <td align='right' colspan=4 style='padding:5px 0 5px 0;'> ";
//if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	$Contents .= "<a href='stock_report.php?".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
//	}				
$Contents .= "
				</td>
			</tr>
			<tr>

			<td valign=top style='padding-top:33px;'>";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_stock>
			";
$innerview = "
			<form name=stockfrm method=post action='product_stock.act.php' target='act'>
			<input type='hidden' name='act' value='update'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='depth' value='$depth'>
			<table cellpadding=0 cellspacing=0  width='100%' class='list_table_box'>
			<col width='7%'>
			<col width='*%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='15%'>
			<tr align=center height=30>
				<td class=s_td>상품코드</td>
				<td class=m_td>이미지/상품명</td>
				<td class=m_td>과세여부</td>
				<td class=m_td>옵션이름</td>
				<td class=m_td>옵션코드</td>
				<td class=m_td>보관장소</td>
				<td class=m_td>입고일</td>
				<td class=m_td>입고가(기준)</td>
				<td class=m_td>재고</td>
				<td class=m_td>출고예정재고</td>
				<td class=m_td >안전재고</td>
				<td class=m_td>진열</td>
				<td class=e_td>입출고</td>
			</tr>
			";

if(count($goods_infos) == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=12 align=center> 해당되는  상품이 없습니다.</td></tr>";
}else{

	$before_pid = "";

	for ($i = 0; $i < count($goods_infos); $i++)
	{

		//$db->fetch($i);

		/*if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$goods_infos[$i][id].".gif")){
			$img_str = "".$admin_config[mall_data_root]."/images/product/s_".$goods_infos[$i][id].".gif";
		}else{
			$img_str = "../image/no_img.gif";
		}*/
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "m"))){
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "m");
		}else{
			$img_str = "../image/no_img.gif";
		}
		
		$place_name = $goods_infos[$i][place_name];

		

	$innerview .= "<tr height=35>
					<td class='list_box_td list_bg_gray' nowrap><!--a href='/pinfo.php?id=".$goods_infos[$i][id]."'-->".($goods_infos[$i][pcode] ? $goods_infos[$i][id]:$goods_infos[$i][id])."<!--/a--><input type=hidden name='pid[]' value='".$goods_infos[$i][id]."'></td>
					<td class='list_box_td point' style='padding:5px 10px;' nowrap>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td width='40' align=center style='padding:0px 5px;'><img src='".$img_str."' width=50 height=50 style='border:1px solid #eaeaea' align=absmiddle></td>
							<td  class='list_box_td'style='text-align:left; padding-left:5px;line-height:150%;'>
							<span class='small'>".getCategoryPathByAdmin($goods_infos[$i][cid], 4)."</span><br>
							<a href=\"javascript:PoPWindow3('../product/goods_input.php?mmode=pop&id=".$goods_infos[$i][id]."',970,800,'goods_info')\"><b>".$goods_infos[$i][pname]."</b></a>
							<!--a href=\"javascript:PoPWindow3('stock_output_detail.php?idx=".$goods_infos[$i][o_ix]."&pid=".$goods_infos[$i][id]."&company_code=".$goods_infos[$i][inventory_info]."',820,800,'input_detail_pop')\"><b>".$goods_infos[$i][pname]."</b></a-->
							</td>
						</tr>
					</table>
					</td>
					<td class='list_box_td list_bg_gray' title='".$goods_infos[$i][surtax_yorn]."' nowrap>
					 ".($goods_infos[$i][surtax_yorn] == "Y" ? "면세(비과세)":"과세")."
					</td>
					<td colspan=8 height=1 bgcolor=#ffffff>".PrintStockByOption($goods_infos[$i])."</td>
					<!--td class='list_box_td'>".$place_name."</td-->
					<td class='list_box_td list_bg_gray'>".($goods_infos[$i][disp] == 1 ? "노출함":"노출안함")."</td>
					<td class='list_box_td' align=center style='padding:5px;' nowrap>
						<table border=0 cellpadding=0 cellspacing=0 align=center>
							<tr>
								<td>";

								$innerview .= "
								<a href=\"javascript:PoPWindow3('../inventory/input_pop.php?id=".$goods_infos[$i][id]."&i_ix=".$i_ix."',800,700,'input_pop')\"><img src='../images/".$admininfo["language"]."/btn_input.gif'></a>
								<a href=\"javascript:PoPWindow3('../inventory/delivery_pop.php?id=".$goods_infos[$i][id]."&i_ix=".$i_ix."',900,700,'output_pop')\"><img src='../images/".$admininfo["language"]."/btn_output.gif'></a>
								<a href=\"javascript:PoPWindow3('../inventory/order_pop.php?id=".$goods_infos[$i][id]."&i_ix=".$i_ix."',800,700,'order_pop')\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a><br>
								<a href=\"javascript:PoPWindow3('../inventory/inventory_order.php?pid=".$goods_infos[$i][id]."&mmode=pop',800,700,'order_pop')\">상품별 출고창고 우선순위 지정</a>
								";
								$innerview .= "
								</td>
							</tr>
						</table>
					</td>
				</tr>
				";
	}

}
	$innerview .= "</table>
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=40><td>".($stock_status == "shortage" ? "<a href=\"javascript:PrintWindow('./print_stock.php?$QUERY_STRING',700,900,'print_stock')\">재고 내역서 출력</a>":"")."</td>
					<td align=right nowrap>".$str_page_bar."</td></tr>

				</table></form>";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<iframe name='act' src='' width=0 height=0></iframe>
			";

$help_text = "- 각 상품별 및 옵션별로 재고현황을 보실 수 있습니다.<br>
		- 수정된 재고는 아래의 변경 버튼을 클릭하시면 저장됩니다..<br>
		- 옵션 항목의 재고가 부족, 품절일 경우도 리스트에 각 상태에 따라 출력되게 됩니다.<br>
		- 재고 상태 검색시 카테고리에 등록되어 있지 않은 상품은 나오지 않습니다";

$Contents .= HelpBox("재고현황", $help_text);

//$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";


if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_stock').innerHTML = document.body.innerHTML;
	parent.document.getElementById('select_category_path1').innerHTML='".$inner_category3_path."';
	</Script>";
}else{
	$Script = "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<!-- 스크립트 에러 발생으로 주석처리함 kbk -->
	<!--script Language='JavaScript' src='product_stock.js'></script>
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script-->
	<!-- 스크립트 에러 발생으로 주석처리함 kbk -->
	<script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var depth = sel.getAttribute('depth');

	//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	document.getElementById('act').src='../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>";

	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 실시간 재고현황";
	$P->title = "실시간 재고현황";
	$P->strContents = $Contents;
	
	
	
	$P->PrintLayOut();
}

/*
function PrintStockByOption($db){

	$mdb = new Database;

	$sql = "select id, option_div,option_price, option_m_price, option_d_price, option_a_price, option_useprice, option_stock, option_safestock,option_etc1 from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a, ".TBL_SHOP_PRODUCT_OPTIONS." b  where b.option_kind = 'b' and b.pid = '".$db->dt[id]."' and a.opn_ix = b.opn_ix order by id asc";
	$mdb->query($sql);

	$mString = "<table cellpadding=0 cellspacing=0 width=100% height=65 style='table-layout : fixed' border=0>
						<col width='33%' >
						<col width='33%' >
						<col width='33%' >";



	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>비회원가</td><td>회원가</td><td>딜러가</td><td>대리점가</td><td >재고</td><td >안전재고</td></tr>";
	$mString .=  "<input type=hidden id='_option_stock".$db->dt[id]."' value=0>";
	if ($mdb->total == 0){
		$mString .= "<td bgcolor='#efefef'  align=center>-</td>
			<td bgcolor='#ffffff' align=center>".$db->dt[stock]."</td>
			<td bgcolor='#efefef' align=center>".$db->dt[safestock]."</td>";
	}else{
		$i=0;
		for($i=0;$i<$mdb->total;$i++){
			$mdb->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff>
			<td bgcolor='#efefef' align=center>".$mdb->dt[option_div]."</td>
			<td align=center bgcolor='#ffffff' >".$mdb->dt[option_stock]."</td>
			<td align=center bgcolor='#efefef' >".$mdb->dt[option_safestock]."</td>
			</tr>
			";
		}
		$mString = $mString."<tr height=27 bgcolor=#ffffff>
			<td bgcolor='#efefef' align=center><b>총계</b></td>

			<td bgcolor='#ffffff' align=center>".$db->dt[stock]."</td>
			<td bgcolor='#efefef' align=center>".$db->dt[safestock]."</td>
			</tr>";
	}

	$mString = $mString."</table>";

	return $mString;
}
*/


?>