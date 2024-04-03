<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
//auth(8);
//echo phpinfo();
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
		$where = "where p.id = r.pid and r.basic = 1 and p.id Is NOT NULL and p.admin ='".$company_id."' AND p.product_type NOT IN (".implode(',',$sns_product_type).")  ";
	}else{
		$where = "where p.id = r.pid and r.basic = 1 and p.id Is NOT NULL AND p.product_type NOT IN (".implode(',',$sns_product_type).")  ";
	}
}else{
	$where = "where p.id = r.pid and p.id Is NOT NULL and p.admin ='".$admininfo[company_id]."' AND p.product_type NOT IN (".implode(',',$sns_product_type).") ";
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
	if($brand != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand."";
	}

	if($brand_name != ""){
		$where .= " and brand_name LIKE '%".$brand_name."%' ";
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
		$sql = "SELECT count(*) as total
						FROM ".TBL_SHOP_PRODUCT_RELATION." r , ".TBL_SHOP_PRODUCT." p
						$where $stock_where and r.cid LIKE '".substr($cid2,0,$cut_num)."%' ";

}else{
		$sql = "select count(*) as total
						from ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r
						$where $stock_where ";
}
//echo $sql;
$db->query($sql);
$db->fetch();
$total = $db->dt[total];
//	echo $db->total;
	//exit;


if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page, $max, "&max=$max&mode=search&company_id=$company_id&stock_status=$stock_status&cid2=$cid2&depth=$depth&brand_name=$brand_name&disp=$disp&state2=$state2&brand=$brand&search_type=$search_type&search_text=$search_text","");
}else{
	$str_page_bar = page_bar($total, $page, $max, "&max=$max","");
}


$Contents =	"
<script  id='dynamic'></script>
<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
			    <td align='left' colspan=4 style='padding-bottom:10px;'> ".GetTitleNavigation("빠른상품재고관리", "상품관리 > 빠른상품재고관리")."</td>
			</tr>
			<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='sprice' value='0' />
			<input type='hidden' name='eprice' value='1000000' />
			<tr height=150>
				<td colspan=2>
					<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:2px'>
								<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='search_table_box'>
									<col width=15%>
									<col width=35%>
									<col width=15%>
									<col width=35%>
									<!--tr>
										<td height='25' class='search_box_title'>  선택된 카테고리  </td>
										<td align=left colspan=3><b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
									</tr-->
									<tr>
										<td class='search_box_title'>카테고리선택</td>
										<td class='search_box_item' colspan=3>
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
							if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
							$Contents .=	"
							<tr>
								<td class='search_box_title'><b>입점업체</b></td>
								<td class='search_box_item'>".CompanyList2($company_id,"")."</td>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item'>".BrandListSelect($brand, $cid)."</td>
							</tr>
							";
							}else{
							$Contents .=	"
							<tr>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item' colspan=3>".BrandListSelect($brand, $cid)."</td>
							</tr>
							";
							}
								$Contents .=	"
									<tr>
										<td class='search_box_title'>진열</td>
										<td class='search_box_item'>
										<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
										<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
										<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
										</td>
										<td class='search_box_title'>판매상태</td>
										<td class='search_box_item' >
											<select name='state2' class='small' style='font-size:12px;'>
												<option value=''>상태값선택</option>
												<option value='1' ".ReturnStringAfterCompare($state2, "1", " selected").">판매중</option>
												<option value='0' ".ReturnStringAfterCompare($state2, "0", " selected").">일시품절</option>
												<option value='6' ".ReturnStringAfterCompare($state2, "6", " selected").">등록신청중</option>
												<!--option value='7' ".ReturnStringAfterCompare($state2, "7", " selected").">수정신청중</option-->
											</select>
										</td>
									</tr>
									<tr>
										<td class='search_box_title'>  검색어  </td>
										<td class='search_box_item'  align=left valign='top' style='padding-right:5px;margin-top:3px;'>
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td><select name='search_type'  style=\"font-size:12px;height:20px;\">
																<option value='pname'>상품명</option>
																<option value='pcode'>상품코드</option>
																<option value='id'>상품코드(key)</option>
																</select>
																</td>
													<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox1' value='".$search_text."' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
													<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
														<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
															<tr height=20>
																<td width=100%  style='padding:0 0 0 5px'>
																	<table width=100% cellpadding=0 cellspacing=0 border=0>
																		<tr>
																			<td class='p11 ls1'>검색어 자동완성</td>
																			<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:pointer;padding:0 10px 0 0' align=right>닫기</td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr height=100% >
																<td valign=top bgColor=#efefef style='padding:0 6px 5px 6px' colspan=2>
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
										<td class='search_box_title'>목록갯수</td>
										<td class='search_box_item'><select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
										<option value='5' ".CompareReturnValue(5,$max).">5</option>
										<option value='10' ".CompareReturnValue(10,$max).">10</option>
										<option value='20' ".CompareReturnValue(20,$max).">20</option>
										<option value='50' ".CompareReturnValue(50,$max).">50</option>
										<option value='100' ".CompareReturnValue(100,$max).">100</option>
										</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
										</td>
									</tr>
									<tr>
										<td class='search_box_title'><b>재고상태</b></td>
										<td class='search_box_item' align=left >
										<input type='radio' name='stock_status' value='whole' id='owhole' ".CompareReturnValue("whole","$stock_status"," checked")."><label for='owhole'>전체</label>
										<input type='radio' name='stock_status' value='soldout' id='osoldout' ".CompareReturnValue("soldout","$stock_status"," checked")."><label for='osoldout'>품절</label>
										<input type='radio' name='stock_status' value='shortage' id='oshortage' ".CompareReturnValue("shortage","$stock_status"," checked")."><label for='oshortage'>부족</label>
										<input type='radio' name='stock_status' value='surplus' id='osurplus' ".CompareReturnValue("surplus","$stock_status"," checked")."><label for='osurplus'>여유</label>
										</td>
										<td class='search_box_title'></td>
										<td >

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
				<td colspan=2 align=center style='padding:10px 0px'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
				</form>
			</tr>
			<tr>
			    <td align='right' colspan=4 style='padding:10px 0 10px 0;'> ".$str_page_bar."</td>
			</tr>
			<tr>

			<td valign=top style='padding-top:33px;'>";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_stock>
			";
$innerview = "
			<form name=stockfrm method=post action='goods_stock.act.php' target='act'>
			<input type='hidden' name='act' value='update'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='depth' value='$depth'>
			<table cellpadding=0 cellspacing=0 bgcolor=#efefef width='100%' border=0 class='list_table_box'>
			<tr align=center height=25>
				<td width='*' class=m_td>상품정보</td>
				<td width='40%' class=m_td>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' >
						<tr >
							<td width='40%' align=center bgcolor=#efefef >옵션이름</td>
							<td width='20%' align=center bgcolor=#efefef >입고수량</td>
							<td width='20%' align=center bgcolor=#efefef >재고</td>
							<td width='20%' align=center bgcolor=#efefef >안전재고</td>
						</tr>
					</table>
				</td>
				<td width='5%' class=m_td>진열</td>
				<td width='8%' class=e_td>관리</td>

			</tr>
			";

if($orderby == "date"){
	$orderbyString = "order by p.regdate desc, vieworder2 asc,  id desc";
}else{
	$orderbyString = "order by vieworder2 asc, p.regdate desc, id desc";
}

if ($cid2){
		$sql = "SELECT p.id, r.cid, p.pcode, p.pname, p.sellprice, p.regdate,p.vieworder,p.disp, stock, safestock, case when vieworder = 0 then 100000 else vieworder end as vieworder2
						FROM ".TBL_SHOP_PRODUCT_RELATION." r , ".TBL_SHOP_PRODUCT." p
						$where $stock_where and r.cid LIKE '".substr($cid2,0,$cut_num)."%' $orderbyString LIMIT $start, $max";

}else{
		$sql = "select p.id,  r.cid, p.pcode, p.pname, p.sellprice, p.regdate,p.vieworder,p.disp, stock, safestock, case when vieworder = 0 then 100000 else vieworder end as vieworder2
						from ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r
						$where $stock_where $orderbyString LIMIT $start, $max";
}

//echo $sql;
$db->query($sql);
/*
$pids = $db->fetchall();

$sql = "select p.id, p.pcode, p.pname, p.sellprice, p.regdate,p.vieworder,p.disp, stock, safestock, case when vieworder = 0 then 100000 else vieworder end as vieworder2
				from ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r
				$where and p.id in('".join("','",$pids)."')  ";
				echo $sql;
$db->query($sql);
*/
if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=5 align=center> 해당되는  상품이 없습니다.</td></tr>";
}else{

	$before_pid = "";

	for ($i = 0; $i < $db->total; $i++)
	{

		$db->fetch($i);

		//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$db->dt[id].".gif")){
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s"))) {
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s");
		}else{
			$img_str = "../image/no_img.gif";
		}


	$innerview .= "<tr bgcolor='#ffffff'>
					<td class='list_box_td point' style='line-height:150%;padding:0px 0px 0px 10px;text-align:left;' >
					<input type=hidden name='pid[]' value='".$db->dt[id]."'>
						<table width=100%>
							<col width='55px'>
							<col width='*'>
							
							<tr>
								<td><img src='".$img_str."' width=50 height=50 style='border:1px solid silver;'></td>
								<td style='text-align:left;line-height:140%;'><span style='color:gray' >".getCategoryPathByAdmin($db->dt[cid], 4)."</span><br>
								<a href='goods_input.php?id=".$db->dt[id]."' target='_blank'><b>".$db->dt[pname]."</b></a><br>
								(".($db->dt[pcode] ? $db->dt[pcode]:$db->dt[id]).")
								</td>
							</tr>
						</table>
					</td>
					<td class='list_box_td' style='padding:0px'>".PrintStockByOption($db)."</td>

					<td class='list_box_td' align=center>
						<input type=checkbox name='dispaly".$db->dt[id]."' value=1 ".($db->dt[disp] == 1 ? " checked":"")." >
					</td>
					<td class='list_box_td list_bg_gray' nowrap>
						<table align=center cellpadding=0 cellspacing=0>
							<tr>
								<td>";
								if(checkMenuAuth(md5("/admin/product/goods_input.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						$innerview .= "<a href='goods_input.php?id=".$db->dt[id]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>";
								}else{
						$innerview .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>";
								}
						$innerview .= "
								</td>
							</tr>
							<!--tr><td><a href=\"JavaScript:PrintWindow('./product_make_order_print.php?id=".$db->dt[id]."',1120,730,'print_stock')\">생산지시서 발행</a></td></tr-->
							<!--tr>
							<td><img src='../images/".$admininfo["language"]."/bt_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"deleteProduct('delete','".$db->dt[id]."')\"></td></tr-->
						</table>
					</td>
				</tr>
				";
	}

}
	$innerview .= "</table>
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=30><td>".($stock_status == "shortage" ? "<a href=\"javascript:PrintWindow('./print_stock.php?$QUERY_STRING',700,900,'print_stock')\">재고 내역서 출력</a>":"")."</td>
					<td align=right nowrap>".$str_page_bar."</td></tr>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$innerview .= "
					<tr>
						<td colspan=2 align=center style='padding:10px 0px;'><input type='image' src='../images/".$admininfo["language"]."/b_edit.gif' border=0></td>
					</tr>";
}
$innerview .= "
				</table></form>";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<!--iframe name='act' src='' width=0 height=0></iframe-->
			";
/*
$help_text = "- 각 해당상품의 옵션의 입고 수량을 입력하시면 재고가 자동으로 조정됩니다.<br>
		- 수정된 재고는 아래의 변경 버튼을 클릭하시면 저장됩니다..<br>
		- 옵션 항목의 재고가 부족, 품절일 경우도 리스트에 각 상태에 따라 출력되게 됩니다.<br>
		- 재고 상태 검색시 카테고리에 등록되어 있지 않은 상품은 나오지 않습니다";
		*/

	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents .= HelpBox("상품 재고관리", $help_text);

//$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";


if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_stock').innerHTML = document.body.innerHTML;
	//parent.document.getElementById('select_category_path1').innerHTML='".$inner_category_path."';
	</Script>";
}else{
	$Script = "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<script Language='JavaScript' src='goods_stock.js'></script>
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script>
	<script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//alert(target);
	//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	//document.getElementById('act').src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>";

	$P = new LayOut();
	$P->strLeftMenu = product_menu("/admin",$category_str);
	$P->addScript = $Script;
	$P->Navigation = "상품관리 > 상품수정 > 빠른상품재고관리";
	$P->title = "빠른상품재고관리";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnloadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}


function PrintStockByOption($db){

	$mdb = new Database;

	//$sql = "select id, option_div,option_price, option_m_price, option_d_price, option_a_price, option_useprice, option_stock, option_safestock,option_etc1, option_code from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a, ".TBL_SHOP_PRODUCT_OPTIONS." b  where b.option_kind = 'b' and b.pid = '".$db->dt[id]."' and a.opn_ix = b.opn_ix order by id asc";
	$sql = "select id, option_div,option_price, option_stock, option_safestock,option_etc1, option_code from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a, ".TBL_SHOP_PRODUCT_OPTIONS." b  where b.option_kind = 'b' and b.pid = '".$db->dt[id]."' and a.opn_ix = b.opn_ix order by id asc";
	$mdb->query($sql);

	$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100% bgcolor=#ffffff border=0>";



	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>비회원가</td><td>회원가</td><td>딜러가</td><td>대리점가</td><td >재고</td><td >안전재고</td></tr>";
	$mString .=  "<input type=hidden id='_option_stock".$db->dt[id]."' value=0>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height='65'>
			<td width='40%'  bgcolor='#ffffff'  align=center> - </td>
			<td width='20%' class='list_box_td list_bg_gray'>
				<input type=text class='textbox' name='incom".$db->dt[id]."' value='0' onkeyup=\"changeStock('".$db->dt[id]."');\" size=6>
			</td>
			<td width='20%' bgcolor='#ffffff' align=center>
				<input type=text class='textbox' name='stock".$db->dt[id]."' value='".$db->dt[stock]."' size=6><input type=hidden name='bstock".$db->dt[id]."' value='".$db->dt[stock]."' size=6>
			</td>
			<td width='20%' class='list_box_td list_bg_gray'>
				<input type=text class='textbox' name='safestock".$db->dt[id]."' value='".$db->dt[safestock]."' size=5>
			</td>
			</tr>";
	}else{
		$i=0;
		for($i=0;$i<$mdb->total;$i++){
			$mdb->fetch($i);
			$mString = $mString."<tr bgcolor=#ffffff>
			<td width='40%' height=40 bgcolor='#ffffff' align=center>
				".$mdb->dt[option_div]." ".($mdb->dt[option_code] != "" ? "(".$mdb->dt[option_code].")":"")."
			</td>
			<td width='20%' align=center bgcolor='#efefef' >
				<input type=text class='textbox' value='0' id='_option_incom".$db->dt[id]."' name='option_incom".$db->dt[id]."_".$mdb->dt[id]."' onkeyup=\"changeStockByOption('".$db->dt[id]."', '".$mdb->dt[id]."');calcurateStockByOption('".$db->dt[id]."');\"  size=6>
			</td>
			<td width='20%' align=center bgcolor='#ffffff' >
				<input type=text class='textbox' id='_option_stock".$db->dt[id]."' name='option_stock".$db->dt[id]."_".$mdb->dt[id]."'  value='".$mdb->dt[option_stock]."' onkeyup=\"calcurateStockByOption('".$db->dt[id]."');\" size=6>
				<input type=hidden name='option_bstock".$db->dt[id]."_".$mdb->dt[id]."' value='".$mdb->dt[option_stock]."' size=6>
			</td>
			<td width='20%' align=center bgcolor='#efefef' >
				<input type=text class='textbox' id='_option_safestock".$db->dt[id]."' name='option_safestock".$db->dt[id]."_".$mdb->dt[id]."'' value='".$mdb->dt[option_safestock]."' onkeyup=\"calcurateSafeStockByOption('".$db->dt[id]."');\" size=6>
			</td>
			<!--td align=center>
				<a href=JavaScript:deleteOption('delete','".$mdb->dt[id]."','$pid')><img  src='../image/si_remove.gif' border=0></a>
			</td-->
			</tr>
			";
		}

		$mString .= "<td width='40%' bgcolor='#ffffff' style='border-top:1px solid silver;padding:5px 0px 5px 0px;' align=center>총계</td>
			<td width='20%' class='list_box_td list_bg_gray' style='border-top:1px solid silver;padding:5px 0px 5px 0px;' ><!--input type=text class='textbox' name='incom".$db->dt[id]."' value='0' onkeyup=\"changeStock('".$db->dt[id]."');\" size=6 readonly--></td>
			<td width='20%' bgcolor='#ffffff' align=center style='border-top:1px solid silver;padding:5px 0px 5px 0px;' ><input type=text class='textbox' name='stock".$db->dt[id]."' value='".$db->dt[stock]."' size=6 readonly><input type=hidden name='bstock".$db->dt[id]."' value='".$db->dt[stock]."' size=6 readonly></td>
			<td width='20%' class='list_box_td list_bg_gray' style='border-top:1px solid silver;padding:5px 0px 5px 0px;' ><input type=text class='textbox' name='safestock".$db->dt[id]."' value='".$db->dt[safestock]."' onkeyup=\"changeSafeStockByOption('".$db->dt[id]."');\" size=6 readonly></td>";
	}

	$mString = $mString."</table>";

	return $mString;
}



?>