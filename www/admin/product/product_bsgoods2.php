<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include_once("buyingService.lib.php");

//print_r($_GET);
$db = new Database;
$db2 = new Database;
/*if($_SESSION["mode"] == "search"){
	$mode = "search";
}*/


//echo roundBetterUp(12345678,-2);
$sql = "select * from shop_buyingservice_info order by regdate desc limit 1 ";

$db->query ($sql);

if($db->total){
	$db->fetch();

	$exchange_rate = $db->dt[exchange_rate];
	$bs_basic_air_shipping = $db->dt[bs_basic_air_shipping];
	$bs_add_air_shipping = $db->dt[bs_add_air_shipping];

	$bs_duty = $db->dt[bs_duty];
	$bs_supertax_rate = $db->dt[bs_supertax_rate];
	$clearance_fee = $db->dt[clearance_fee];
}

/*
if(!$update_kind){
	$update_kind = "display";
}
*/
if($before_update_kind){
	$update_kind = $before_update_kind;
}
//echo $_COOKIE["update_kind"];
if($_COOKIE["bs_goodsinfo_update_kind"]){
	$update_kind = $_COOKIE["bs_goodsinfo_update_kind"];
}else if(!$update_kind){
	$update_kind = "display";
}






if($mode == "search"){
	
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
			$cut_num = 9;
			break;
	}


	if($admininfo[admin_level] == 9){
		$where = "where p.id Is NOT NULL and p.product_type = 1 and p.id = r.pid   ";
	}else{
		$where = "where p.id Is NOT NULL and p.product_type = 1 and p.id = r.pid and admin ='".$admininfo[company_id]."'  ";
	}

	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}
	if($company_id != ""){
		$where = $where."and p.admin = '".$company_id."' ";

	}
	if($search_text != ""){
		if($search_type == "sellprice"){
			$where = $where."and ".$search_type." = '".trim($search_text)."' ";
		}else{
			$where = $where."and ".$search_type." LIKE '%".trim($search_text)."%' ";
		}
	}else{
		if($search_type == "bimg" && $search_text == ""){
			$where .= "and ".$search_type." = '' ";
		}
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

	if($bs_site != ""){
		$where .= " and p.bs_site = '".$bs_site."'";
	}

	if($currency_ix != ""){
		$where .= " and p.currency_ix = '".$currency_ix."'";
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

	if($brand_name != ""){
		$where .= " and p.brand_name LIKE '%".trim($brand_name)."%' ";
	}

	if($cid2 != ""){
		//session_register("cid");
		//session_register("depth");
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}
	$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where  ";
	//echo $sql;
	$db2->query($sql);

}else{
	if ($cid2 == ""){
		if($admininfo[admin_level] == 9){
			$addWhere = "Where p.id = r.pid  and p.product_type = 1 ";
			if($company_id != ""){
				$addWhere .= " and admin ='".$company_id."'";
			}


			$db2->query("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  $addWhere ");
		}else{
			$db2->query("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  where  p.id = r.pid and admin ='".$admininfo[company_id]." '");
		}


	}else{
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
				$cut_num = 9;
				break;
		}
		if($admininfo[admin_level] == 9){
			$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  where p.id = r.pid and r.cid LIKE '".substr($cid2,0,$cut_num)."%' ";

			$db2->query($sql);

		}else{
			$db2->query("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  where p.id = r.pid and r.cid LIKE '".substr($cid2,0,$cut_num)."%' and admin ='".$admininfo[company_id]."' ");
		}

	}
}

$db2->fetch();

$total = $db2->dt[total];

if($max == ""){
	$max = 10; //페이지당 갯수
}else{
	$max = $max;
}


if($total <= $pageging_info["product_bsgoods2.php"]["page"]){
	unset($pageging_info);
	session_unregister("pageging_info");
	$page = 1;
	//echo $pageging_info["product_bsgoods2.php"]["page"];
	//exit;
}

if ($page == ''){
	if($pageging_info["product_bsgoods2.php"]["page"] != ""){
		$page  = $pageging_info["product_bsgoods2.php"]["page"];
		$start = ($page - 1) * $max;
	}else{		
		$page  = 1;
		$start = 0;
	}
	if($pageging_info["product_bsgoods2.php"]["nset"] != ""){
		$nset  = $pageging_info["product_bsgoods2.php"]["nset"];
	}else{
		$nset  = 1;
	}
}else{
	$start = ($page - 1) * $max;
}


//echo $page;
//exit;
//http://dev.mallstory.com/admin/product/product_bsgoods2.php?mode=search&cid2=&depth=&bsmode=list&cid0_1=&cid1_1=&cid2_1=&cid3_1=&bs_site=&company_id=&brand_name=&disp=&state2=&search_type=pname&search_text=&max=10&x=64&y=21
$search_query = "&mode=$mode&view=innerview&cid2=$cid2&depth=$depth&bsmode=$bsmode&cid0_1=$cid0_1&cid1_1=$cid1_1&cid2_1=$cid2_1&cid3_1=$cid3_1&bs_site=$bs_site&company_id=$company_id&brand_name=$brand_name&disp=$disp&state2=$state2&search_type=$search_type&search_text=$search_text&max=$max";

if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&orderby=$orderby&ordertype=$ordertype".$search_query);
}else{
	$str_page_bar = page_bar($total, $page,$max, "&orderby=$orderby&ordertype=$ordertype".$search_query);
	//echo $total.":::".$page."::::".$max."<br>";
}

$Contents =	"
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("구매대행 상품관리", "상품관리 > 구매대행 상품관리")."</td>
	</tr>

	<tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".($bsmode=="reg" || $bsmode=="" ? "class='on'":"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?bsmode=reg'\">구매대행 상품 등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($bsmode=="list" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?bsmode=list'\">구매대행 상품 리스트</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='buyingServiceInfo.php'\">구매대행 환율/수수료 관리</td>
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
if($bsmode=="reg" || $bsmode==""){
$Contents .=	"
	<form name='search_form' method='get' action='product_bsgoods2.act.php' onsubmit='return CheckFormValue(this);' target=bsframe>
	<input type='hidden' name='bs_act' value=''>
	<input type='hidden' name='cid2' id='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='bsmode' value='$bsmode'>
	<tr>
		<td colspan=2>
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:0px'>
					<div style='z-index:-1;position:absolute;width:100%;text-align:center;' id='parent_save_loading'>
					<div style='width:100%;height:100px;display:block;position:relative;z-index:10px;text-align:center;padding-top:150px;' id='save_loading'></div>
					</div>
						<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
							<col width='15%' />
							<col width='35%' />
							<col width='15%' />
							<col width='35%' />
							<tr>
								<td class='input_box_title'>등록 카테고리선택</td>
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
							<tr bgcolor=#ffffff >
								<td class='input_box_title'> 구매대행 사이트</td>
								<td class='input_box_item' >
									".getBuyingServiceSiteInfo($bs_site)."
									
									<span class=small></span>
									<div id='organization_img_area' ></div>
								</td>
								<td class='input_box_title'> 환율타입</td>
								<td class='input_box_item' >
									".getBuyingServiceCurrencyInfo($currency_ix)."
									
									<span class=small></span>
									<div id='organization_img_area' ></div>
								</td>

							</tr>
							<tr>
								<td class='input_box_title'>기본 URL </td>
								<td class='input_box_item' colspan=3 style='padding:5px;'>
								<table cellpadding=0 cellspacing=0 >
									<tr>
										<td>
										<div style='border:1px solid silver;width:630px;'><input type='hidden' id='search_type' value='bs_site'>
										<input type='text' id=search_texts name='list_url' onkeyup='findNames();' onclick='findNames();' style='width:600px;border:0px;margin-right:5px;' class='textbox' value=''>
										<img src='/admin/images/ico_arrow_down.png' border=0 onclick='findNames();' style='margin-top:5px;cursor:pointer;'> 
										</div>
										</td>
										<td style='padding-left:5px;'>
										<input type=checkbox name='bs_favorite' value='1' id='bs_favorite'><label for='bs_favorite'>즐겨찾기저장</label>
										</td>
									</tr>
								</table>
								<DIV id=popup style='display: none; width: 630px; POSITION: absolute; height: 150px; backgorund-color: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
								<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef style='width: 630px;'>													
									<tr height=100% >
										<td valign=top bgColor=#efefef style='' colspan=2>
											<table cellpadding=0 cellspacing=0 width=100% height=100% style='margin:0 0px 5px 0px;height: 120px;' bgcolor=#ffffff>
												<tr>
													<td valign=top >
													<div style='POSITION: absolute; overflow-y:auto;height: 120px;' id='search_data_area'>
														<TABLE id=search_table style='table-layout:fixed;width:100%;' cellSpacing=0 cellPadding=0 bgColor=#ffffff border=0>
														<TBODY id=search_table_body></TBODY>
														</TABLE>
													<div>
													</td>
												</tr>
											</table>

										</td>
									</tr>
									<tr height=24>
										<td width=100%  style='padding:0 0 0 5px'>
										<table width=100% cellpadding=0 cellspacing=0 border=0>
										<tr>
											<td class='p11 ls1'>구매대행 즐겨찾기 URL 자동완성</td>
											<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:pointer;padding:0 10px 0 0' align=right>닫기</td>
										</tr>
										</table>
										</td>
									</tr>
								</table>
								</DIV>
								<!--http://www.barneys.com/Main%20Floor%20Shoes/MAIN04,default,sc.html-->
								<!--
								특정카테고리에 해당하는 상품 리스트 정보를 입력하시면 해당 카테고리 페이징 목록이 분석됩니다.
								-->
								<!--div style='cursor:pointer;' onclick=\"$('#search_texts').val($(this).html())\">http://www.gymboree.com/shop/dept_category.jsp?FOLDER%3C%3Efolder_id=2534374305975775&ASSORTMENT%3C%3East_id=1408474395917465&bmUID=1329527746993</div-->
								<br> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."<br>
								</td>
							</tr>
							<tr>
								<td class='input_box_title'>검색대상페이지</td>
								<td class='input_box_item'>
									<input type='text' name='start' id='start' style='width:50px;' class='textbox' > ~ <input type='text' name='end' id='end' style='width:50px;' class='textbox' > <input type='checkbox' name='this_page_order' id='this_page_order' value='1' checked><label for='this_page_order' title='체크시 마지막 페이지부터 상품이 등록됩니다. '>역순으로 등록</label> <!--span class=small>* 체크시 역순으로 등록합니다. </span-->
								</td>
								<td class='input_box_title'>현재검색페이지 </td>
								<td class='input_box_item'>
									<input type='text' name='this_pagenum' id='this_pagenum' style='width:100px;' class='textbox' >
								</td>
							</tr>
							<tr>
								<td class='input_box_title'>현재검색 URL </td>
								<td class='input_box_item' colspan=3 >
								<input type='text' name='this_url' id='this_url' style='width:98%;' class='textbox' >
								</td>
							</tr>
							<tr bgcolor=#ffffff >
								<td class='input_box_title'> 통관타입</td>
								<td class='input_box_item'>
									<input type=radio name='clearance_type' value='1' ><label for='clearance_type_1'>목록통관</label>
									<input type=radio name='clearance_type' value='0' checked><label for='clearance_type_0'>일반통관</label>
									<input type=radio name='clearance_type' value='9' ><label for='clearance_type_9'>국내배송</label>
								</td>
								<td class='input_box_title'> 구매대행수수료율</td>
								<td class='input_box_item'><input type=text class='textbox' name='bs_fee_rate' id='bs_fee_rate' value='0' style='width:100px;text-align:right;padding-right:3px;'> %</td>
							  </tr>
							  <tr>
									<td class='input_box_title'>예상무게</td>
									<td class='input_box_item'><input type=text class='textbox' name='bs_air_wt' id='bs_air_wt'  value='1' style='width:100px;text-align:right;padding-right:3px;'> 파운드</td>
									<td class='input_box_title'>중복상품처리</td>
									<td class='input_box_item'>
										<input type='radio' name='dupe_process' id='dupe_process_update' value='update' ><label for='dupe_process_update'>UPDATE</label>
										<input type='radio' name='dupe_process' id='dupe_process_skip' value='skip' checked><label for='dupe_process_skip'>SKIP</label>
									</td>
								</tr>
								<tr>
									<td class='input_box_title'>검색여부</td>
									<td class='input_box_item' >
										<input type='radio' name='search_status' id='search_status_y' value='Y' checked><label for='search_status_y'>검색</label>
										<input type='radio' name='search_status' id='search_status_n' value='N'><label for='search_status_n'>정지</label>
									</td>
									<td class='input_box_title'>전시여부</td>
									<td class='input_box_item' >
										<input type='radio' name='disp' id='disp_y' value='1' checked><label for='disp_y'>등록즉시 노출</label>
										<input type='radio' name='disp' id='disp_n' value='0'><label for='disp_n'>등록후 수동노출</label>
									</td>
								</tr>
								<tr>
									<td class='input_box_title'>등록상품확인</td>
									<td class='input_box_item' style='padding:5px 5px;'>
										<input type='radio' name='reg_goods_view' id='reg_goods_view_y' value='Y' checked><label for='reg_goods_view_y'>등록즉시 확인</label>
										<input type='radio' name='reg_goods_view' id='reg_goods_view_n' value='N'><label for='reg_goods_view_n'>상품등록 종료 후 일괄 확인</label><br>
										 ※ <span  class='small' style='padding-left:0px;'> 상품등록 종료 후 일괄확인을 선택하시면 상품등록이 더 빨라집니다. </span>
									</td>
									<td class='input_box_title'><label for='usable_round'>가격반올림</label><input type='checkbox' name='usable_round' id='usable_round' value='Y' onclick='UsableRound(this)'></td>
									<td class='input_box_item' >
									<select name='round_precision' id='round_precision' disabled>
										<!--option value=''>반올림단위</option-->
										<option value='2'>100자리</option>
										<option value='3'>1000자리</option>
										<option value='4'>10000자리</option>
									</select>
										<!--input type='radio' name='round_type' id='round_type_0' value='0' checked><label for='round_type_0'>반올림없음</label-->
										<input type='radio' class='round_type' name='round_type' id='round_type_1' value='round' disabled checked><label for='round_type_1'>반올림</label>
										<input type='radio' class='round_type' name='round_type' id='round_type_2' value='floor' disabled><label for='round_type_2'>버림</label>
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
	<tr>
		<td height=50 colspan=2 align=center>
		<img  src='../images/".$admininfo["language"]."/btn_page_analysis.gif' class=vm onclick=\"checkSearchFrom(document.search_form, 'search_list')\" title=\"리스트페이지 목록 검색\" style=\"cursor:pointer;\">
		<img  src='../images/".$admininfo["language"]."/btn_buyservice_goods_get.gif' class=vm onclick=\"checkSearchFrom(document.search_form, 'get_goods')\" title=\"상품정보 가져오기\" style=\"cursor:pointer;\">
		<!--input type=image src='../image/bt_search.gif' border=0 align=absmiddle--><!--btn_inquiry.gif--></td>
	</tr>
	</form>";
}else{
$Contents .=	"
	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='bsmode' value='$bsmode'>
	<tr>
		<td colspan=2>
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:0px'>
						<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
							<col width='15%' />
							<col width='35%' />
							<col width='15%' />
							<col width='35%' />
							<tr>
								<td class='input_box_title'>  선택된 카테고리  </td>
								<td class='input_box_item' colspan=3 ><b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> <!--로 검색된 결과 입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O')." ")."</b></div></td>
							</tr>
							<tr>
								<td class='input_box_title'>카테고리선택</td>
								<td class='input_box_item' colspan=3 >
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
							<tr bgcolor=#ffffff >
						    <td class='input_box_title'> 구매대행 사이트  </td>
						    <td class='input_box_item'>
							".getBuyingServiceSiteInfo($bs_site)."
								<!--select name='bs_site' style='font-size:12px;'>
									<option value='' >구매대행 사이트</option>
									<option value='saksfifthavenue' >www.saksfifthavenue.com </option>
									<option value='bloomingdales' >www1.bloomingdales.com</option>
									<option value='macys' >www1.macys.com</option>
									<option value='barneys' >www.barneys.com</option>
									<option value='nordstrom' >shop.nordstrom.com</option>";

$Contents .=	"
									
								</select-->
								<span class=small></span>
								<div id='organization_img_area' ></div>
						    </td>
							<td class='input_box_title'> 환율타입</td>
								<td class='input_box_item' >
									".getBuyingServiceCurrencyInfo($currency_ix, "search")."
									
									<span class=small></span>
									<div id='organization_img_area' ></div>
								</td>
						    <!--td class='input_box_title'>브랜드</td>
							<td class='input_box_item'><input type='text' name='brand_name' class='textbox' ></td-->
						  </tr>
							";
							if($admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
								$Contents .=	"
							<!--tr>
								<td class='input_box_title'>입점업체</td>
								<td class='input_box_item'>".CompanyList2($company_id,"")."</td>
								<td class='input_box_title'>브랜드</td>
								<td class='input_box_item'><input type='text' name='brand_name' class='textbox' ></td>
							</tr-->
							";
							}//".BrandListSelect4("","")."
								$Contents .=	"
							<tr>
								<td class='input_box_title'>진열</td>
								<td class='input_box_item'>
									<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
									<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
									<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
								</td>
								<td class='input_box_title'>판매및 상태값</td>
								<td class='input_box_item'>
									<select name='state2' class='small' style='font-size:12px;'>
										<option value=''>상태값선택</option>
										<option value='1' ".ReturnStringAfterCompare($state2, "1", " selected").">판매중</option>
										<option value='0' ".ReturnStringAfterCompare($state2, "0", " selected").">일시품절</option>
										<option value='6' ".ReturnStringAfterCompare($state2, "6", " selected").">등록신청중</option>
										<option value='7' ".ReturnStringAfterCompare($state2, "7", " selected").">수정신청중</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class='input_box_title'>  검색어  </td>
								<td class='input_box_item'>
									<table cellpadding=0 cellspacing=0>
										<tr>
											<td><select name='search_type'  style=\"font-size:12px;height:22px;\">
												<option value='pname' ".ReturnStringAfterCompare($search_type, "pname", " selected").">상품명</option>
												<option value='pcode' ".ReturnStringAfterCompare($search_type, "pcode", " selected").">상품코드</option>
												<option value='id' ".ReturnStringAfterCompare($search_type, "id", " selected").">상품코드(key)</option>
												<option value='bimg' ".ReturnStringAfterCompare($search_type, "bimg", " selected").">상품이미지</option>
												<option value='sellprice' ".ReturnStringAfterCompare($search_type, "sellprice", " selected").">판매가</option>
												
												</select>
											</td>
											<td style='padding-left:5px;'>
											<INPUT id=search_texts  class='textbox' value='".$search_text."' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
											<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
												<table cellSpacing=0 cellPadding=0 border=0 width=100% bgColor=#efefef>
													<tr height=20>
														<td width=100%  style='padding:0 0 0 5px'>
															<table width=100% cellpadding=0 cellspacing=0 border=0>
																<tr>
																	<td class='p11 ls1'>검색어 자동완성</td>
																	<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:pointer;padding:0 10 0 0' align=right>닫기</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
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
								<td class='input_box_title'>목록갯수</td>
								<td class='input_box_item'>
									<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
										<option value='5' ".CompareReturnValue(5,$max).">5</option>
										<option value='10' ".CompareReturnValue(10,$max).">10</option>
										<option value='20' ".CompareReturnValue(20,$max).">20</option>
										<option value='50' ".CompareReturnValue(50,$max).">50</option>
										<option value='100' ".CompareReturnValue(100,$max).">100</option>
									</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span>
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
	<tr>
		<td height=50 colspan=2 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
	</form>";
}
$Contents .=	"
	<tr>
		<td valign=top style='padding-top:33px;'>";

$Contents .= "
		</td>
		<form name=listform method=post action='goods_batch.act.php' onsubmit='return SelectUpdate(this)' target='act'><!--onsubmit='return CheckDelete(this)' target='act'-->
				<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
				<input type='hidden' id='pid' value=''>
				<input type='hidden' name='act' value='update'>
				<input type='hidden' name='search_act_total' value='$total'>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td height=30 align=left>
					상품수 : ".number_format($total)." 개
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>";
if(false){
$innerview .= "
				<tr>

				<td height=30 align=left colspan=2> <!--a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a-->
				<b class=small>판매가격순</b>
				<a href='product_bsgoods2.php?orderby=sellprice&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "sellprice" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='높은가격순'></a>
				<a href='product_bsgoods2.php?orderby=sellprice&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "sellprice" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='낮은가격순'></a> |
				<!--b class=small>적립금</b>
				<a href='product_bsgoods2.php?orderby=reserve&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "reserve" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='높은적립금순'></a>
				<a href='product_bsgoods2.php?orderby=reserve&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "reserve" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='낮은적립금순'></a> |-->
				<b class=small>상품명</b>
				<a href='product_bsgoods2.php?orderby=pname&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "pname" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='가나다순'></a>
				<a href='product_bsgoods2.php?orderby=pname&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "pname" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='가나다역순'></a>
				<b class=small>등록일자</b>
				<a href='product_bsgoods2.php?orderby=r.regdate&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".((($orderby == "r.regdate" && $ordertype ==  "desc") || ($orderby == "" && $ordertype ==  "")) ? "on":"off").".gif' border=0 align=absmiddle title='최근등록순'></a>
				<a href='product_bsgoods2.php?orderby=r.regdate&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "r.regdate" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='등록순'></a>
				<b class=small>정렬순</b>
				<a href='product_bsgoods2.php?orderby=vieworder2&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "vieworder2" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='역순'></a>
				<a href='product_bsgoods2.php?orderby=vieworder2&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "vieworder2" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='순'></a>
				</td>
				</tr>";
}
$innerview .= "
			</table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='3%'>
				<col width='*'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='12%'>
				<col width='7%'>
				<tr bgcolor='#cccccc' align=center height=30>
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>".OrderByLink("상품명", "pname", $ordertype)."</td>
					<td class=m_td>판매상태</td>
					<td class=m_td>환율타입</td>
					<td class=m_td>Orgin 판매가</td>
					<td class=m_td>진열</td>
					<td class=m_td>공급가</td>
					<td class=m_td>".OrderByLink("소비자가", "listprice", $ordertype)."</td>
					<td class=m_td>".OrderByLink("판매가격", "sellprice", $ordertype)."</td>
					<td class=m_td>".OrderByLink("등록일자", "regdate", $ordertype)." ".OrderByLink("수정일자", "editdate", $ordertype)."</td>
					<td class=e_td>관리</td>
				</tr>";



if($orderby != "" && $ordertype != ""){
	$orderbyString = " group by p.id   order by $orderby $ordertype ";
}else{
	$orderbyString = " group by p.id   order by p.regdate desc ";
}

if($mode == "search"){
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
	$where = "";
	if($search_text != ""){
		if($search_type == "sellprice"){
			$where = $where."and ".$search_type." = '".trim($search_text)."' ";
		}else{
			$where = $where."and ".$search_type." LIKE '%".trim($search_text)."%' ";
		}
	}else{
		if($search_type == "bimg" && $search_text == ""){
			$where .= "and ".$search_type." = '' ";
		}
	}

	if($sprice && $eprice){
		$where .= "and sellprice between $sprice and $eprice ";
	}

	if($status_where){
		$where .= " and ($status_where) ";
	}
	if($brand2 != ""){
		$where .= " and brand = ".trim($brand2)."";
	}

	if($brand_name != ""){
		$where .= " and p.brand_name LIKE '%".trim($brand_name)."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".trim($disp);
	}

	if($bs_site != ""){
		$where .= " and p.bs_site = '".trim($bs_site)."'";
	}

	if($currency_ix != ""){
		$where .= " and p.currency_ix = '".trim($currency_ix)."'";
	}

	if($state2 != ""){
		$where .= " and state = ".trim($state2)."";
	}


	if($cid2 != ""){
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}
	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$addWhere = "and admin ='".trim($company_id)."'";
		}else{
			unset($addWhere);
		}
		$sql = "SELECT  p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name, p.bs_goods_url,
		p.company, p.pcode, p.coprice, p.listprice, icons,p.disp, p.editdate, p.reserve, p.reserve_rate,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.currency_ix
		FROM ".TBL_SHOP_PRODUCT." p right join  ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid , ".TBL_COMMON_COMPANY_DETAIL." c
		where c.company_id = p.admin and p.product_type = 1  $addWhere $where $orderbyString LIMIT $start, $max";
		//echo $sql;
		$db->query($sql);
	}else{
		$sql = "SELECT  p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name, p.bs_goods_url,
		p.company, p.pcode, p.coprice, p.listprice, icons,p.disp, p.editdate, p.reserve, p.reserve_rate,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2
		FROM ".TBL_SHOP_PRODUCT." p right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid , ".TBL_COMMON_COMPANY_DETAIL." c
		where c.company_id = p.admin and p.product_type = 1 and admin ='".$admininfo[company_id]."' $where $orderbyString LIMIT $start, $max";


		$db->query($sql);
	}
	//echo $sql;
}else{

	if ($cid2 == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";
			}else{
				unset($addWhere);
			}
			//$tmp_sql = "create temporary table ".TBL_LOGSTORY_BYPAGE."_tmp ENGINE = MEMORY select vdate, pageid, ncnt, nduration from ".TBL_SHOP_PRODUCT_RELATION." where vdate = '$vdate' ";

			$sql = "SELECT p.id as id, p.pname, p.brand,p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name, p.bs_goods_url,
			p.company, p.pcode, p.coprice, p.listprice,  icons,p.disp, p.editdate, p.reserve, p.reserve_rate,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.currency_ix
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
			where c.company_id = p.admin and p.product_type = 1 and p.id = r.pid $where $addWhere $orderbyString LIMIT $start, $max";
			//echo $sql;
			$db->query($sql);
		}else{
			$sql = "SELECT p.id as id ,p.brand, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name, p.bs_goods_url,
			p.company, p.pcode, p.coprice, p.listprice, icons, p.disp, p.editdate, p.reserve, p.reserve_rate,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.currency_ix
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
			where c.company_id = p.admin and p.product_type = 1 and p.id = r.pid and admin ='".$admininfo[company_id]."' $where $orderbyString LIMIT $start, $max";


			//echo $sql;
			$db->query($sql);
		}

		//echo $sql;
	}else{
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

		if($admininfo[admin_level] == 9){
			$sql = "SELECT p.id as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,c.com_name, r.cid, p.search_keyword,state, p.brand, p.brand_name, p.bs_goods_url,
				p.company, p.pcode, p.coprice, p.listprice,  icons,p.disp, p.editdate,  p.reserve_rate,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.currency_ix
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
				where c.company_id = p.admin and p.product_type = 1 and p.id = r.pid and r.cid = '".$cid2."' $where $orderbyString LIMIT $start, $max";

			//echo $sql;

			$db->query($sql);
		}else{
			$sql = "SELECT p.id as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,c.com_name,  r.cid, p.search_keyword,state, p.brand, p.brand_name, p.bs_goods_url,
				p.company, p.pcode, p.coprice, p.listprice, icons,p.disp, p.editdate,  p.reserve_rate,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.currency_ix
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
				where c.company_id = p.admin and p.product_type = 1 and p.id = r.pid and r.cid = '".$cid2."' and admin ='".$admininfo[company_id]."' $where $orderbyString LIMIT $start, $max";

				//echo $sql;
				$db->query($sql);

				//echo "test".$db->total;

		}
	}
}

//echo $sql;
if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=11 align=center> 등록된 제품이 없습니다.</td></tr>";

}else{
	$currencys = getBuyingServiceCurrencyInfo("", "array");
	//print_r($currencys);
	$goods_infos = $db->fetchall();
	for ($i = 0; $i < count($goods_infos); $i++)
	{
		//$db->fetch($i);

		$sql = "select * from shop_product_buyingservice_priceinfo where pid = '".$goods_infos[$i][id]."' order by regdate desc limit 1 ";

		$db->query ($sql);

		if($db->total){
			$db->fetch();
			$buyservice_price_info = $db->dt;

		//	echo (float)$duty;
		}

		//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$goods_infos[$i][id].".gif")){
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "s"))) {
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "s");
		}else{
			$img_str = "../image/no_img.gif";
		}

	$innerview .= "<tr bgcolor='#ffffff'>
						<td class='list_box_td list_bg_gray'><input type=checkbox class=nonborder id='cpid' name=select_pid[] value='".$goods_infos[$i][id]."'><!--input type=hidden class=nonborder id='cpid' name=cpid[] value='".$goods_infos[$i][id]."'--></td>
						<td class='list_box_td point' align=left style='padding:5px;'>
							<table cellpadding=2 cellspacing=0 width='100%' style='text-align:left;'>
							<tr>
								<td width=60 rowspan=2><img src='".$img_str."' width=50 height=50></td>
								<td width='*'><span style='color:gray' >".getCategoryPathByAdmin($goods_infos[$i][cid], 4)."</span></td>
							</tr>
							<tr>
								<td>";
		$innerview .= "<a href='goods_input.php?id=".$goods_infos[$i][id]."&mode=$mode&nset=$nset&page=$page&cid2=$cid2&depth=$depth&company_id=$company_id&brand2=$brand2&max=$max&state2=$state2&disp=$disp&search_type=$search_type&search_text=$search_text' target='_blank'><b> ".($goods_infos[$i][brand_name] ? "[".$goods_infos[$i][brand_name]."]":"")." ".$goods_infos[$i][pname]." (".$goods_infos[$i][pcode].")</b></a>
					<br><a href='".$goods_infos[$i][bs_goods_url]."' class=small target=_blank><b class=blu><img src='../images/".$admininfo["language"]."/btn_buy_agency.gif' align=absmiddle style='padding:5px 0;'></b></a>
								</td>
							</tr>

							<tr>
								<td nowrap>

								</td>
							</tr>
							</table>
					</td>
					<td class='list_box_td list_bg_gray'>";
						if($goods_infos[$i][state] == 1){
							$innerview .= "판매중";

						}else if($goods_infos[$i][state] == 6){
							$innerview .= "등록신청중";
						}else if($goods_infos[$i][state] == 7){
							$innerview .= "수정신청중";
						}else if($goods_infos[$i][state] == 0){
							$innerview .= "일시품절중";
						}

$innerview .= "</td>
					<td class='list_box_td'>
					".$currencys[$goods_infos[$i][currency_ix]][currency_type_name]."
					</td>
					<td class='list_box_td list_bg_gray'>
					 ".$currency_display[$currencys[$goods_infos[$i][currency_ix]][basic_currency]]["front"]." ".$buyservice_price_info[orgin_price]." ".$currency_display[$currencys[$goods_infos[$i][currency_ix]][basic_currency]]["back"]."
					</td>
					<td align=center class='small'>";

						if($goods_infos[$i][disp] == 1){
							$innerview .= "진열함";
						}else if($goods_infos[$i][disp] == 0){
							$innerview .= "진열안함";
						}

$innerview .= "</td>
					
					<td class='list_box_td list_bg_gray'>
					".number_format($goods_infos[$i][coprice])." 원
					</td>
					<td class='list_box_td ' nowrap>
					".number_format($goods_infos[$i][listprice])." 원
					</td>
					<td class='list_box_td list_bg_gray' align=center nowrap>
					".number_format($goods_infos[$i][sellprice])." 원
					</td>
					<td class='list_box_td ' align=center style='padding:0px 4px;line-height:130%;' nowrap>
					등록 : ".$goods_infos[$i][regdate]."<br>";
					if($orderby == "editdate"){
					$innerview .= "<b>수정 : ".$goods_infos[$i][editdate]."</b>";
					}else{
					$innerview .= "수정 : ".$goods_infos[$i][editdate];
					}
					$innerview .= "
					</td>
					<td class='list_box_td list_bg_gray'  nowrap>
						<table align=center>
							<!--tr>
								<td><a href=\"javascript:CopyData(document.forms['listform'], '".$goods_infos[$i][id]."','".$goods_infos[$i][pname]."','".$admininfo[admin_level]."');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle title=\" ' ".strip_tags($goods_infos[$i][pname])." ' 에 대한 정보를 수정합니다.\"></a></td>
							</tr-->
							<tr>
								<td ><a href='/shop/goods_view.php?cid=".$goods_infos[$i][cid]."&id=".$goods_infos[$i][id]."&depth=3&b_ix=".$goods_infos[$i][brand]."' target='_blank'><img src='../images/".$admininfo["language"]."/btn_preview.gif'></a></td>
							</tr>
						</table>
					</td>

				</tr>";
	}
}
	$innerview .= "</table>
				<table width='100%'>
				<tr>
					<td height=30 align=right>".$str_page_bar."</td>
				</tr>
				<tr height=30><td colspan=2 align=right></td></tr>
				</table>

				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
			";

if($bsmode == "list"){
		$help_text = "
		<div style='z-index:-1;position:absolute;width:100%;text-align:center;' id='select_update_parent_save_loading'>
		<div style='width:100%;height:200px;display:block;position:relative;z-index:10px;text-align:center;padding-top:60px;' id='select_update_save_loading'></div>
		</div>
		<div id='batch_update_display' ".($update_kind == "display" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
		<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b>판매/진열 상태 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span></div>
			<table width='100%' cellpadding=0 cellspacing=0 border=0  class='input_table_box'>
			<col width='160px'>
			<col width='*'>
			<tr height=30>
				<td class='input_box_title'> <b>판매상태 </b></td>
				<td class='input_box_item'>
				<input type='radio' name='c_state' id='c_state_0' value='0'><label for='c_state_0'>일시품절</label><input type='radio' name='c_state' id='c_state_1' value='1' checked><label for='c_state_1'>판매중</label><input type='radio' name='c_state' id='c_state_6' value='6'><label for='c_state_6'>등록신청중</label>
				</td>
			</tr>
			<tr height=30>
				<td class='input_box_title'> <b>진열상태 </b></td>
				<td class='input_box_item'>
				<input type='radio' name='c_disp' id='c_disp_0' value='0'><label for='c_disp_0'>노출안함</label><input type='radio' name='c_disp' id='c_disp_1' value='1' checked><label for='c_disp_1'>노출함</label>
				</td>
			</tr>
			</table>
			<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
				<tr><td height=50 colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
			</table>
		</div>
		<div id='batch_update_category' ".($update_kind == "category" ? "style='display:block'":"style='display:none'")." >
		<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b>상품 카테고리 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</span></div>
			<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
			<col width='160px'>
			<col width='*'>
			<tr>
				<td class='input_box_title'> <b>변경 형태 </b></td>
				<td class='input_box_item'>
				<input type='radio' name='category_change_type' id='category_change_type_1' value='1' checked><label for='category_change_type_1'>카테고리 추가</label>
				<input type='radio' name='category_change_type' id='category_change_type_2' value='2'><label for='category_change_type_2'><!--c-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</label>
				<input type='radio' name='category_change_type' id='category_change_type_3' value='3'><label for='category_change_type_3'>기본카테고리 변경(기본카테고리외 삭제)</label>

				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>변경 카테고리 </b></td>
				<td class='input_box_item'>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td style='padding-right:5px;'>".getCategoryList3("대분류", "c_cid0", "onChange=\"loadChangeCategory(this,'c_cid1',2)\" title='대분류' ", 0, $cid2)."</td>
						<td style='padding-right:5px;'>".getCategoryList3("중분류", "c_cid1", "onChange=\"loadChangeCategory(this,'c_cid2',2)\" title='중분류'", 1, $cid2)."</td>
						<td style='padding-right:5px;'>".getCategoryList3("소분류", "c_cid2", "onChange=\"loadChangeCategory(this,'c_cid3',2)\" title='소분류'", 2, $cid2)."</td>
						<td>".getCategoryList3("세분류", "c_cid3", "onChange=\"loadChangeCategory(this,'c_cid',2)\" title='세분류'", 3, $cid2)."<input type=hidden name='c_cid'><input type=hidden name='c_depth'></td>
					</tr>
				</table>
				</td>
			</tr>
			</table>
			<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
				<tr><td height=50 colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
			</table>
		</div>
		<div id='batch_update_bs_goods_stock' ".($update_kind == "bs_goods_stock" ? "style='display:block'":"style='display:none'")." >
		<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b><!--구매대행 상품 정보/재고확인-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')."</b> <span class=small style='color:gray'><!--상품/재고 확인을 하고자 하는 상품을 검색/선택 후 재고확인 버튼을 클릭해주세요.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'P')."</span></div>
			<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
			<col width='160px'>
			<col width='*'>
			<tr>
				<td class='input_box_title'> <b>처리방법 </b></td>
				<td class='input_box_item' style='padding:5px;'>
				<input type='radio' name='sc_state' id='sc_state_0' value='0' checked><label for='sc_state_0'><!--<b>품절/판매불가</b>로 확인된 상품을 <b>일시품절</b>로 처리합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G')."</label><br>
				<input type='radio' name='sc_state' id='sc_state_9' value='9' ><label for='sc_state_9'><!--<b>품절/판매불가</b>로 확인된 상품을 <b>삭제</b>로 처리합니다.(상품 이미지 정보 및 관련 모든 정보가 삭제됩니다.)-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H')."</label>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>진열상태 </b></td>
				<td class='input_box_item' style='padding:5px;'>
				<input type='radio' name='sc_disp' id='sc_disp_0' value='0' checked><label for='sc_disp_0'><!--<b>품절/판매불가</b>로 확인된 상품을 <b>노출안함</b>로 처리합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I')."</label><br>
				<input type='radio' name='sc_disp' id='sc_disp_1' value='1' ><label for='sc_disp_1'><!--<b>품절/판매불가</b>로 확인된 상품을 <b>노출함</b>로 처리합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J')."</label>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>이미지 </b></td>
				<td class='input_box_item' style='padding:5px;'>
				<input type='checkbox' name='img_update' id='img_update' value='Y' ><label for='img_update'>이미지 정보를 업데이트 합니다.</label><br>
				</td>
			</tr>
			</table>
			<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
				<tr><td height=50 colspan=4 align=center><input type=image src='../image/btn_bsgoods_update.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
			</table>
		</div>
		";


		$select = "
		<select name='update_type' >
			<option value='2'>선택한 상품 전체에</option>
			<option value='1'>검색한 상품 전체에</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind_display' value='display' ".CompareReturnValue("display",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_display');\"><label for='update_kind_display'><!--판매/진열 상태 일괄 변경-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K')."</label>
		<input type='radio' name='update_kind' id='update_kind_category' value='category' ".CompareReturnValue("category",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_category');\"><label for='update_kind_category'><!--상품 카테고리 변경-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'L')."</label>
		<input type='radio' name='update_kind' id='update_kind_stock' value='bs_goods_stock' ".CompareReturnValue("bs_goods_stock",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_bs_goods_stock');\"><label for='update_kind_stock'><!--구매대행 상품 정보/재고 업데이트-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'M')."</label>
		<!--input type='radio' name='update_kind' id='update_kind_coupon' value='coupon' ".CompareReturnValue("coupon",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_coupon');\"><label for='update_kind_coupon'><!--쿠폰 일괄지급 ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'N')."--> </label-->";

		$Contents .= "".HelpBox($select, $help_text,'750')."</form>";
}

$Contents .= "
<!--a href=\"javascript:alert(document.frames['bs_search_frame'].location);\">url 정보 보기</a>
		<IFRAME id=bs_search_frame name=bs_search_frame src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME-->";
		if($_SERVER["HTTP_HOST"] == "dev.forbiz.co.kr"){
		$Contents .= "<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=800 height=600 scrolling=no ></IFRAME>";
		}else{
		$Contents .= "<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>";
		}
		
		$Contents .= "
		<!--iframe name='act' src='' width=800 height=200 frameBorder=0 ></iframe-->";

//[{"bsi_ix":"62","exchange_type":"1","exchange_rate":"1170","bs_basic_air_shipping":"6","bs_add_air_shipping":"1.2","bs_duty":"8","bs_supertax_rate":"10","clearance_fee":"5500","usable_round":"Y","round_precision":"0","round_type":"round","disp":"1","regdate":"2010-02-10 06:58:55"}]
$Script = "
<script language='javascript'>

$(document).ready(function() {
	//
	$('#currency_ix').change(function(){
		
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'json','currency_ix': $('#currency_ix').val()},
			url: 'buyingServiceInfo.act.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				//alert(111);
			},  
			success: function(bs_info){ 
				//(bs_info);
				//$('#');
				if(bs_info.usable_round == 'Y'){
					$('#usable_round').attr('checked',true);
					
					//alert(bs_info.round_precision);
					//$('#round_precision option:eq('+bs_info.round_precision+')').attr('selected',true);
					$('#round_precision').attr('disabled',false);
					$('#round_precision').val(bs_info.round_precision);
					$('#bs_fee_rate').val(bs_info.bs_fee_rate);

					$('.round_type').each(function(){
						//alert($(this).val());
						$(this).attr('disabled',false);
						if($(this).val() == bs_info.round_type){
							$(this).attr('checked',true);
						}
					});
					
					
				}else if(bs_info.usable_round == 'N'){
					$('#usable_round').attr('checked',false);
				}
			} 
		}); 

	});


});
function UsableRound(obj){
	//alert(obj.checked);
	if(obj.checked){
		$('#round_precision').attr('disabled',false);
		$('input[name=round_type]').attr('disabled',false);		
	}else{
		$('#round_precision').attr('disabled',true);
		$('input[name=round_type]').attr('disabled',true);		
	}
}

function checkSearchFrom(frm, bs_act){
	/*
	if(!CheckFormValue(frm)){
		return false;
	}
	*/
	if(bs_act == 'get_goods' || bs_act == 'search_list'){
		document.getElementById('parent_save_loading').style.zIndex = '1';
		with (document.getElementById('save_loading').style){

			width = '100%';
			height = '100px';
			backgroundColor = '#ffffff';
			filter = 'Alpha(Opacity=70)';
			opacity = '0.8';
		}

		var obj = document.createElement('div');
		with (obj.style){
			position = 'relative';
			zIndex = 100;
		}
		obj.id = 'loadingbar';
		if(bs_act == 'get_goods'){
			obj.innerHTML = \"<img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 상품정보를 가져오는 중입니다..\";
		}else{
			obj.innerHTML = \"<img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 페이지를 분석 중입니다..\";
		}
		document.getElementById('save_loading').appendChild(obj);

		document.getElementById('save_loading').style.display = 'block';
		//return false;
	}
	//return false;

	if(frm.search_status[0].checked){
		if(act == 'get_goods' && (frm.this_pagenum.value == '-' || frm.this_pagenum.value == '0')){
			frm.this_pagenum.value = frm.start.value;
			frm.this_url.value = frm.list_url.value;
		}
		frm.bs_act.value = bs_act;
		//alert(bs_act);
		if(bs_act == 'get_goods'){
			//alert(frm.bs_site.value);
			if(frm.cid2.value.length < 1){
				alert(language_data['product_bsgoods2.php']['C'][language]);
				//'등록카테고리가 선택되지 않았습니다. 등록카테고리 지정후 상품 가져오기를 실행해주세요'
				frm.cid0_1.focus();
				document.getElementById('save_loading').style.display = 'none';
					obj.innerHTML = \"\";

			}

			if(frm.bs_site.value.length < 1){
				alert(language_data['product_bsgoods2.php']['A'][language]);
				//'구매대행 사이트를 지정해주세요'
				frm.bs_site.focus();
				document.getElementById('save_loading').style.display = 'none';
				obj.innerHTML = \"\";
				return false;
			}

			if(frm.currency_ix.value.length < 1){
				alert('환율타입을 선택해주세요');
				frm.currency_ix.focus();
				document.getElementById('save_loading').style.display = 'none';
				obj.innerHTML = \"\";
				return false;
			}

			if(frm.list_url.value.length < 1){
				alert(language_data['product_bsgoods2.php']['B'][language]);
				//'기본 URL 을 입력해주세요 (구매대행 사이트의 카테고리별 상품 리스트페이지 입니다)'
				frm.list_url.focus();
				document.getElementById('save_loading').style.display = 'none';
				obj.innerHTML = \"\";
				return false;
			}

			if(frm.start.value.length < 1 || frm.end.value.length < 1 || frm.this_pagenum.value.length < 1){
				alert('구매대행 사이트 카테고리별 상품리스트 페이지 분석후 상품등록을 해주세요');
				return false;
			}

			
				frm.submit();
			

		}else{
			if(frm.cid2.value.length < 1){
				alert(language_data['product_bsgood3s.php']['C'][language]);
				//'등록카테고리가 선택되지 않았습니다. 등록카테고리 지정후 상품 가져오기를 실행해주세요'
				frm.cid0_1.focus();
				document.getElementById('save_loading').style.display = 'none';
					obj.innerHTML = \"\";

			}
			if(frm.bs_site.value.length < 1){
				alert(language_data['product_bsgoods2.php']['A'][language]);
				//'구매대행 사이트를 지정해주세요'
				frm.bs_site.focus();
				document.getElementById('save_loading').style.display = 'none';
				obj.innerHTML = \"\";
				return false;
			}
			//alert(frm.list_url.value.length < 1);
			if(parseInt(frm.list_url.value.length) < 1){
				alert(language_data['product_bsgoods2.php']['B'][language]);
				//'기본 URL 을 입력해주세요 (구매대행 사이트의 카테고리별 상품 리스트페이지 입니다)'
				frm.list_url.focus();
				document.getElementById('save_loading').style.display = 'none';
				obj.innerHTML = \"\";
				return false;
			}

			if(frm.list_url.value.indexOf(frm.bs_site.value) == -1){
				alert(language_data['product_bsgoods2.php']['D'][language]);
				//'기본 URL 이 선택하신 구매대행 사이트와 맞는지 다시 한번 확인해주세요'
				frm.list_url.focus();
				document.getElementById('save_loading').style.display = 'none';
				obj.innerHTML = \"\";
				return false;
			}
			frm.submit();
		}
	}else{
		alert(language_data['product_bsgoods2.php']['E'][language]);
		//'검색 정지중입니다.'
		unloading();
	}
}


function unloading(){

	parent.document.getElementById('parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('loadingbar').innerHTML ='';
	parent.document.getElementById('save_loading').innerHTML ='';
	parent.document.getElementById('save_loading').style.display = 'none';
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_display','batch_update_category','batch_update_bs_goods_stock'); //,'batch_update_sms','batch_update_coupon'

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
			$.cookie('bs_goodsinfo_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}

function SelectUpdateLoading(){
		document.getElementById('select_update_parent_save_loading').style.zIndex = '1';
		with (document.getElementById('select_update_save_loading').style){

			width = '100%';
			height = '173px';
			backgroundColor = '#ffffff';
			filter = 'Alpha(Opacity=70)';
			opacity = '0.8';
		}

		var obj = document.createElement('div');
		with (obj.style){
			position = 'relative';
			zIndex = 100;
		}
		obj.id = 'select_update_loadingbar';

		obj.innerHTML = \"<img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 상품재고를 확인중입니다..\";

		document.getElementById('select_update_save_loading').appendChild(obj);

		document.getElementById('select_update_save_loading').style.display = 'block';
}

function select_update_unloading(){

	parent.document.getElementById('select_update_parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('select_update_loadingbar').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').style.display = 'none';
}

</script>
";
///http://www.bodenusa.com/en-US/Baby-Trousers-Jeans/72075-KHK/Baby-Khaki-Anchors-Baby-Boarders.html
//Pretty Applique T-shirt
//bodenusa_71180-WHT

//$Contents .= HelpBox("구매대행 상품관리", $help_text);
$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
if($view == "innerview"){
	$pageging_info["product_bsgoods2.php"]["page"] = $page;
	$pageging_info["product_bsgoods2.php"]["nset"] = $nset;
	
	session_register("pageging_info");

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid2, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	try{
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> <!--로 검색된 결과 입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O')."	 ")."\" ;
	}catch(e){}
	parent.document.search_form.cid2.value ='$cid2';
	parent.document.search_form.depth.value ='$depth';

	</Script>";
}else{
	$Script .= "<Script Language='JavaScript' src='buyingService_ajax.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<script Language='JavaScript' src='product_input.js'></script>
	<script Language='JavaScript' src='product_list.js'></script>
	<script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		var depth = sel.getAttribute('depth');
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//document.getElementById('act').src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	function loadChangeCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		var depth = sel.getAttribute('depth');

		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
		//document.getElementById('act').src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>";

	$P = new LayOut();
	$P->strLeftMenu = product_menu();
	$P->addScript = $Script;
	$P->Navigation = "상품관리 > 구매대행 > 상품스크래핑";
	$P->title = "상품스크래핑";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}
?>