<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');


if(!$update_kind){
	$update_kind = "display";
}

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

if($mode == "search"){

	if($company_id != ""){
		$sWhere .= "and p.admin = '".$company_id."' ";
	}
	if($search_text != ""){
		$sWhere .= "and p.".$search_type." LIKE '%".$search_text."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

	if($state2 != ""){
		$sWhere .= " and p.state = ".$state2." ";
	}
	if($brand2 != ""){
		$sWhere .= " and brand = ".$brand2."";
	}

	if($brand_name != ""){
		$sWhere .= " and brand_name LIKE '%".$brand_name."%' ";
	}
}


$db = new Database;


$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=4> ".GetTitleNavigation("카테고리 미등록상품", "소셜커머스 > 카테고리 미등록상품")."</td>
			</tr>
			<tr>
			    <td align='left' colspan=4 style='padding-bottom:15px;'>
			    	<div class='tab'>
							<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='tab_01'  >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='product_list.php'\">카테고리별 상품목록</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='tab_02' class='on'>
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='product_list_noncategory.php'\">카테고리 미등록상품</td>
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
			</tr>
			 <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='sprice' value='0' />
	<input type='hidden' name='eprice' value='1000000' />
	<tr>
		<td colspan=2>
			<table class='box_shadow' style='width:100%;'  cellpadding='0' cellspacing='0' border='0' ><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:0px'>
						<table cellpadding=4 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>
							";
							if($admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
								$Contents .=	"
							<tr>
								<td class='search_box_title'> 입점업체</td>
								<td class='search_box_item'>".CompanyList2($company_id,"")."</td>
								<td class='search_box_title'> 브랜드</td>
								<td class='search_box_item'><!--input type='text' class='textbox2' name='brand_name'-->".BrandListSelect($brand, $cid)."</td>
							</tr>
							";
							}//".BrandListSelect4("","")."
								$Contents .=	"
							<tr>
								<td class='search_box_title'> 진열</td>
								<td class='search_box_item'>
								<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
								<input type='radio' name='disp'  id='disp_5' value='5' ".ReturnStringAfterCompare($disp, "5", " checked")."><label for='disp_5'>노출함</label>
								<input type='radio' name='disp'  id='disp_6' value='6' ".ReturnStringAfterCompare($disp, "6", " checked")."><label for='disp_6'>노출안함</label>
								</td>
								<td class='search_box_title'> 판매및 상태값</td>
								<td class='search_box_item'>
									<select name='state2' class='small' style='font-size:12px; height:22px'>
										<option value=''>상태값선택</option>
										<option value='1' ".ReturnStringAfterCompare($state2, "1", " selected").">판매중</option>
										<option value='0' ".ReturnStringAfterCompare($state2, "0", " selected").">일시품절</option>
										<option value='6' ".ReturnStringAfterCompare($state2, "6", " selected").">등록신청중</option>
										<!--option value='7' ".ReturnStringAfterCompare($state2, "7", " selected").">수정신청중</option-->
									</select>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'>   검색어  </td>
								<td align=left valign='top' style='padding-right:5px;margin-top:1px;'>
									<table cellpadding=0 cellspacing=0>
										<tr>
											<td><select name='search_type'  style=\"font-size:12px;height:22px;\">
														<option value='pname' ".CompareReturnValue("pname",$search_type).">상품명</option>
														<option value='pcode' ".CompareReturnValue("pcode",$search_type).">상품코드</option>
														<option value='id' ".CompareReturnValue("id",$search_type).">상품코드(key)</option>
														</select>
														</td>
											<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox2' value='' clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><!--onclick='findNames();'  --><br>
											<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
												<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
													<tr height=20>
														<td width=100%  style='padding:0 0 0 5'>
															<table width=100% cellpadding=0 cellspacing=0 border=0>
																<tr>
																	<td class='p11 ls1'>검색어 자동완성</td>
																	<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:pointer;padding:0 10 0 0' align=right>닫기</td>
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
								<td class='search_box_title'> 목록갯수</td>
								<td class='search_box_item'><select name=max style=\"font-size:12px;height: 22px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
								<option value='5' ".CompareReturnValue(5,$max).">5</option>
								<option value='10' ".CompareReturnValue(10,$max).">10</option>
								<option value='20' ".CompareReturnValue(20,$max).">20</option>
								<option value='50' ".CompareReturnValue(50,$max).">50</option>
								<option value='100' ".CompareReturnValue(100,$max).">100</option>
								</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>
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
		<td colspan=2 align=center style='padding:10px 0'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
		</form>
	</tr>
	<tr>
			<td valign=top style='padding-top:33px;'>";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_list>
			";
$innerview = "
			<table cellpadding=0 cellspacing=0 bgcolor=gray width=100% class='list_table_box'>
			<tr bgcolor='#fffff' align=center height=30px valign=middle>
			<form name=listform method=post action='goods_batch.act.php' onsubmit='return SelectUpdate(this)'  target='act'>
			<!--input type=hidden class=nonborder id='cpid' name=cpid[] value=''-->
			<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
			<input type='hidden' id='pid' value=''>
			<input type='hidden' name='act' value='select_delete'>
			<input type='hidden' name='type' value='nonecategory'>
				<td width='3%' class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>

				<td width='*' class=m_td>제품정보</td>
				<td width='7%' class=m_td>판매상태</td>
				<td width='7%' class=m_td>진열</td>
				<td width='9%' class=m_td>공급가</td>
				<td width='9%' class=m_td>소비자가</td>
				<td width='9%' class=m_td>판매가</td>
				<td width='13%' class=m_td><a href='product_list.php?orderby=date&company_id=$company_id'>날짜</a></td>
				<td width='18%' class=e_td>관리</td>
			</tr>";

if($admininfo[admin_level] == 9){
		//if($company_id){
		//	$addWhere = "and admin ='".$company_id."'";
		//}

	//카테고리 미등록상품 shop_product 테이블에서 체크 변경
		$sql = "select count(*) as total
						from ".TBL_SNS_PRODUCT." p, ".TBL_COMMON_COMPANY_DETAIL." ccd, ".TBL_COMMON_SELLER_DELIVERY." csd
						where reg_category = 'N' and ccd.company_id = p.admin and p.product_type IN ('4','5','6')  $addWhere $sWhere";

		$db->query($sql);
		$db->fetch();
		$total = $db->dt[total];
		//echo "total:".$total;
		$sql = "select p.id, p.pcode,  p.state, p.disp,  p.brand_name, p.pname, p.coprice, p.listprice, p.sellprice, p.regdate,p.vieworder,ccd.com_name, p.regdate, case when vieworder = 0 then 100000 else vieworder end as vieworder2
						from ".TBL_SNS_PRODUCT." p, ".TBL_COMMON_COMPANY_DETAIL." ccd, ".TBL_COMMON_SELLER_DELIVERY." csd
						where reg_category = 'N' and ccd.company_id = p.admin and p.product_type IN ('4','5','6') $addWhere $sWhere limit $start, $max ";
		//echo $sql;
		$db->query($sql);
	}else{

		$sql = "select count(*) as total
						from ".TBL_SNS_PRODUCT." p, ".TBL_COMMON_COMPANY_DETAIL." ccd, ".TBL_COMMON_SELLER_DELIVERY." csd
						where reg_category = 'N' and ccd.company_id = '".$admininfo[company_id]."'
						and ccd.company_id = p.admin and ccd.company_id = csd.company_id and p.product_type IN ('4','5','6')  $addWhere $sWhere ";

		$db->query($sql);
		$db->fetch();
		$total = $db->dt[total];

		$sql = "select p.id, p.pcode, p.state, p.disp, p.brand_name, p.pname, p.coprice, p.listprice, p.sellprice, p.regdate,p.vieworder,ccd.com_name,  p.regdate, case when vieworder = 0 then 100000 else vieworder end as vieworder2
				from ".TBL_SNS_PRODUCT." p, ".TBL_COMMON_COMPANY_DETAIL." ccd, ".TBL_COMMON_SELLER_DELIVERY." csd
				where reg_category = 'N' and ccd.company_id = '".$admininfo[company_id]."'
				and ccd.company_id = p.admin and ccd.company_id = csd.company_id and p.product_type IN ('4','5','6')  $addWhere $sWhere limit $start, $max";
		//echo $sql;
		$db->query($sql);
	}
//echo $sql;

$search_query = "&mode=$mode&view=innerview&product_type=$product_type&sprice=$sprice&eprice=$eprice&company_id=$company_id&brand=$brand&disp=$disp&state2=$state2&one_commission=$one_commission&search_type=$search_type&search_text=$search_text&max=$max&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD";

$str_page_bar = page_bar($total, $page,$max, $search_query);

if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=100><td colspan=9 align=center> 등록된 제품이 없습니다.</td></tr>
				";
}else{
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$db->dt[id].".gif")){
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s"))) {
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s");
		}else{
			$img_str = "../image/no_img.gif";
		}

	$innerview .= "	<tr>
					<td class='list_box_td list_bg_gray'><input type=checkbox class=nonborder id='cpid' name=select_pid[] value='".$db->dt[id]."'></td>
					<td class='list_box_td point'>
						<table cellpadding=1 cellspacing=0 width='100%'>
						<tr>
							<td width=60 rowspan=2><img src='".$img_str."' width=50 height=50></td>
							<td width='*' align='left'><span style='color:gray' class='small'>".getCategoryPathByAdmin($db->dt[cid], 4)."</span></td>
						</tr>
						<tr>
							<td align='left'>";
	$innerview .= "<a href='goods_input.php?id=".$db->dt[id]."&mode=$mode&nset=$nset&page=$page&cid2=$cid2&depth=$depth&company_id=$company_id&brand2=$brand2&max=$max&state2=$state2&disp=$disp&search_type=$search_type&search_text=$search_text' target='_blank'><b> [".$db->dt[brand_name]."] ".$db->dt[pname]."</b></a>

							</td>
						</tr>
						</table>
					</td>
					<td class='list_box_td list_bg_gray'>";
						if($db->dt[state] == 1){
							$innerview .= "판매중";

						}else if($db->dt[state] == 6){
							$innerview .= "등록신청중";
						}else if($db->dt[state] == 7){
							$innerview .= "수정신청중";
						}else if($db->dt[state] == 0){
							$innerview .= "일시품절중";
						}

$innerview .= "					</td>
					<td class='list_box_td'>";

						if($db->dt[disp] == 1){
							$innerview .= "진열함";
						}else if($db->dt[disp] == 0){
							$innerview .= "진열안함";
						}

$innerview .= "					</td>
					<td class='list_box_td list_bg_gray'>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td' nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[listprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td list_bg_gray' nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td'>".$db->dt[regdate]."<br><br>".$db->dt[company_name]."</td>
					<td class='list_box_td list_bg_gray' nowrap>
						<a href='goods_input.php?mode=copy&id=".$db->dt[id]."'><img src='../images/".$admininfo["language"]."/btc_copy.gif' border=0 align=absmiddle ></a>
						<a href='goods_input.php?id=".$db->dt[id]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>
						<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"deleteProduct('delete_excel','".$db->dt[id]."','&type=nonecategory')\">
					</td>

				</tr>
				";

	}
}
	$innerview .= "</table>
					<table width='100%'>
						<tr>
							<td height=30>";
	if(checkMenuAuth(md5("/admin/sns/goods_input.php"),"D") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$innerview .= "<input type=image src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle >";
	}
				$innerview .= "
							</td>
							<td align=right>".$str_page_bar."</td></tr></table>

				";

$Contents = $Contents.$innerview ."

							</td>
						</tr>
					</table>

			";

$help_text = "
<div style='z-index:-1;position:absolute;' id='select_update_parent_save_loading'>
<div style='width:100%;height:200px;display:block;position:relative;z-index:10;text-align:center;padding-top:60px;' id='select_update_save_loading'></div>
</div>
<div id='batch_update_display' ".($update_kind == "display" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b>판매/진열 상태 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span></div>
	<table width='100%' border=0 class='input_table_box'>
	<tr>
		<td class='input_box_title' width=160> <b>판매상태 </b></td>
		<td class='input_box_item' width='*'>
		<input type='radio' name='c_state' id='c_state_0' value='0'><label for='c_state_0'>일시품절</label><input type='radio' name='c_state' id='c_state_1' value='1' checked><label for='c_state_1'>판매중</label><input type='radio' name='c_state' id='c_state_6' value='6'><label for='c_state_6'>등록신청중</label>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>진열상태 </b></td>
		<td class='input_box_item'>
		<input type='radio' name='c_disp' id='c_disp_6' value='6'><label for='c_disp_6'>진열안함</label><input type='radio' name='c_disp' id='c_disp_5' value='5' checked><label for='c_disp_5'>진열함</label>
		</td>
	</tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr><td height=50 colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table>
</div>
<div id='batch_update_category' ".($update_kind == "category" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b>상품 카테고리 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</span></div>
	<table width='100%' border=0 class='input_table_box'>
	<tr>
		<td class='input_box_title' width=160> <b>변경 형태 </b></td>
		<td class='input_box_item' width='*'>
		<input type='radio' name='category_change_type' id='category_change_type_1' value='1' checked><label for='category_change_type_1'>카테고리 추가</label><input type='radio' name='category_change_type' id='category_change_type_2' value='2'><label for='category_change_type_2'>기본카테고리 변경(없으면 추가)</label>
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
<div id='batch_update_goodsdelete' ".($update_kind == "goodsdelete" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b>상품정보 삭제</b> <span class=small style='color:gray'><!--삭제하시고자 하는 상품을 선택후 삭제 버튼을 클릭해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</span></div>
	<table width='100%' border=0 class='input_table_box'>
	<tr>
		<td class='input_box_title' width=160> <b>변경 형태 </b></td>
		<td class='input_box_item' width='*'>

		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>변경 카테고리 </b></td>
		<td class='input_box_item'>

		</td>
	</tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr><td height=50 colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table>
</div>
";


$select = "
<select name='update_type' >
					<option value='1'>검색한 상품 전체에</option>
					<option value='2' selected>선택한 상품 전체에</option>
				</select>
				<input type='radio' name='update_kind' id='update_kind_display' value='display' ".CompareReturnValue("display",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_display');\"><label for='update_kind_display'>판매/진열 상태 일괄 변경</label>
				<input type='radio' name='update_kind' id='update_kind_category' value='category' ".CompareReturnValue("category",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_category');\"><label for='update_kind_category'>상품 카테고리 변경</label>
				<input type='radio' name='update_kind' id='update_kind_goodsdelete' value='goodsdelete' ".CompareReturnValue("goodsdelete",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_goodsdelete');\"><label for='update_kind_goodsdelete'>상품정보삭제</label>
				";
if(checkMenuAuth(md5("/admin/sns/goods_input.php"),"D") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$Contents .= "".HelpBox($select, $help_text,'550')."</form>";
}
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >카테고리 미등록 상품이란 ? 기본 제품정보는 등록이 됐으나 카테고리 등록이 되지 않음 상품을 말합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >제품명 또는 수정버튼을 클릭하시고 카테고리 등록 탭에서 원하는 카테고리를 클릭하시면 자동으로 등록됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >카테고리가 등록되고 나면 카테고리 미등록상품 페이지에서 상품은 삭제되게 됩니다</td></tr>
</table>
";*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$Script = "
<script  id='dynamic'></script>

<script language='javascript'>

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_display','batch_update_category','batch_update_goodsdelete'); //,'batch_update_sms','batch_update_coupon'

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}
</script>

";

$Contents .= HelpBox("카테고리 미등록 상품", $help_text, 200);

//$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";

if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";

	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "<Script>
		parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	//	parent.document.getElementById('select_category_path1').innerHTML='".$inner_category_path."';
	//	parent.document.getElementById('select_category_path2').innerHTML='".$inner_category_path."(".$total."개) ';
	//	parent.document.getElementById('select_category_path3').innerHTML='".$inner_category_path."';
	//	parent.document.forms['excel_input_form'].cid.value = '".$cid."';
	//	parent.document.forms['excel_input_form'].depth.value = '".$depth."';
		</Script>";
}else{
$Script .= "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<script Language='JavaScript' src='product_input.js'></script>
	<script Language='JavaScript' src='product_list.js'></script>
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script>
	<script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		var depth = sel.getAttribute('depth');

		//dynamic.src = './category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
		window.frames['act'].location.href = './category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	function loadChangeCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		var depth = sel.getAttribute('depth');

		//dynamic.src = './category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = './category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>";

	$P = new LayOut();
	$P->strLeftMenu = sns_menu("/admin",$category_str);
	$P->addScript = $Script;
	$P->Navigation = "소셜커머스 > 상품리스트 > 카테고리 미등록상품";
	$P->title = "카테고리 미등록상품";
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

$mdb->query("SELECT * FROM ".TBL_SNS_CATEGORY_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

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
<div id=TREE_BAR style='margin:5;'>
<script>
tree.draw();
tree.nodes[0].select();
</script>
</div>
</form>";

return $m_string;
}




function PrintRootNode($cname){
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

	return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.expanded = $expandstring;
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}

?>