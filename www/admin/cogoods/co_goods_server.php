<?
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include("./co_goods.lib.php");
$install_path = "../../include/";
include("SOAP/Client.php");

//print_r($admininfo);

//$imagedata1 = process_using_gd_or_something($imagedata1);
//echo $imagedata1;
//echo "<img src={$imagedata1} >";

if($max == ""){
	$max = 10; //페이지당 갯수
}else{
	$max = $max;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;

//echo $hostserver;
if($hostserver){
		//$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ");

		//$db->fetch();
		//$hostserver = $db->dt[hostserver];
		//echo $hostserver;
		$soapclient = new SOAP_Client("http://".$hostserver."/admin/cogoods/api/");
		// server.php 의 namespace 와 일치해야함
		$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

		## 한글 인자의 경우 에러가 나므로 인코딩함.

		$paginginfo[start] = $start;
		$paginginfo[max] = $max;
		$paginginfo[page] = $page;

		foreach($_GET as $key => $value){
			$search_rules[$key]= urlencode($value);
		}
		//print_r($search_rules);
		$co_goodsinfo = $soapclient->call("getCoGoodsByServer",$params = array("co_type"=> $co_type,"company_id"=> $admininfo[company_id], "search_rules"=> $search_rules,  "paginginfo"=> $paginginfo),	$options);

		//print_r($co_goodsinfo);

		$co_goodsinfo = (array)$co_goodsinfo;
		//print_r($co_goodsinfo);
		$co_goodsinfo = (array)$co_goodsinfo;
		$co_goods = $co_goodsinfo[goods];
		//print_r($co_goods);
		$total = $co_goodsinfo[total];
		if(!$total){
			$total = 0;
		}
		if($co_goodsinfo[page]){
			$page = $co_goodsinfo[page];
		}
		if($co_goodsinfo[max]){
			$max = $co_goodsinfo[max];
		}

}
//echo $page.":::".$total."::".$max;
if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&sprice=$sprice&eprice=$eprice&state2=$state2&disp=$disp&brand_name=$brand_name&cid2=$cid2&depth=$depth");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype");
	//echo $total.":::".$page."::::".$max."<br>";
}

$Contents =	"
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4 > ".GetTitleNavigation("공유상품관리", "상점관리 > 공유상품관리")."</td>
	</tr>";

$Contents .=	"
	<tr>
	    <td align='left' colspan=8 style='padding-bottom:10px;'> ".getHostServer($chs_ix)."</td>
	</tr>";

$Contents .=	"
	<tr>
	    <td align='left' colspan=2 style='padding-bottom:15px;'>
	    <div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>";
			if($co_type == "" || $co_type == "co_goods"  || $co_type == "co_goods_server_mylist" ){
			$Contents .=	"
						<table id='tab_01'  ".($co_type == "" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='co_goods.php?co_type=&chs_ix=".$_GET["chs_ix"]."'\">상품목록</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_02' ".($co_type == "co_goods" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='co_goods.php?co_type=co_goods&chs_ix=".$_GET["chs_ix"]."'\">내 공유상품 목록</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_04' ".($co_type == "co_goods_server_mylist" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='co_goods_server.php?co_type=co_goods_server_mylist&chs_ix=".$chs_ix."'\">서버에 공유한 상품목록</td>
							<th class='box_03'></th>
						</tr>
						</table>";
			}else{
			$Contents .=	"
						<table id='tab_03' ".($co_type == "co_goods_local" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='co_goods.php?co_type=co_goods_local&chs_ix=".$_GET["chs_ix"]."'\">공유된 상품목록</td>
							<th class='box_03'></th>
						</tr>
						</table>

						<table id='tab_05' ".($co_type == "co_goods_server" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='co_goods_server.php?co_type=co_goods_server&chs_ix=".$_GET["chs_ix"]."'\">서버 판매공유 상품목록</td>
							<th class='box_03'></th>
						</tr>
						</table>";
			}
			$Contents .=	"
					</td>
					<td style='vertical-align:bottom;padding:0px 0px 10px 4px;'>";

$Contents .= "<!--서버에 판매 공유된 상품목록입니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."
					</td>
				</tr>
				</table>
			</div>
	    </td>
	</tr>

	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);'  target='iframe_act'>
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='act' value='update'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='co_type' value='$co_type' />
	<input type='hidden' name='co_goods' value='' />
	<tr>
		<td colspan=2>
			<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:10'>
						<table cellpadding=0 cellspacing=0 border=0 width=100% class='search_table_box'>
							<col widht='15%' />
							<col widht='35%' />
							<col widht='15%' />
							<col widht='35%' />

							<tr height='30'>
								<td class='search_box_title'>  <b>선택된 카테고리</b>  </td>
								<td class='search_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":getCategoryPathByAdmin($cid2, $depth)."(".$total."개) <b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
							</tr>
							<!--tr>
								<td width='150' bgcolor='#efefef' align=center><b>카테고리선택</b></td>
								<td colspan=3>
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
							<tr><td colspan=4 class='dot-x'></td></tr-->
							";
							if($admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
								$Contents .=	"
							<tr>
								<td class='search_box_title'><b>입점업체</b></td>
								<td class='search_box_item'>".CompanyList2($company_id,"")."</td>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item'><input type='text' name='brand_name' value='$brand_name'></td>
							</tr>
							";
							}//".BrandListSelect4("","")."
								$Contents .=	"
							<tr>
								<td class='search_box_title'><b>진열</b></td>
								<td class='search_box_item'>
								<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
								<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
								<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
								</td>
								<td class='search_box_title'><b>판매및 상태값</b></td>
								<td class='search_box_item'>
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
								<td class='search_box_title'bgcolor='#efefef' align=center>  <b>검색어</b> </td>
								<td class='search_box_item'  valign='top' style='padding-right:5px;padding-top:1px;'>
									<table cellpadding=0 cellspacing=0>
										<tr>
											<td><select name='search_type'  style=\"font-size:12px;height:20px;\">
														<option value='pname' ".($search_type == "pname" ? "selected":"").">상품명</option>
														<option value='pcode' ".($search_type == "pcode" ? "selected":"").">상품코드</option>
														<option value='id' ".($search_type == "id" ? "selected":"").">상품코드(key)</option>
														</select>
														</td>
											<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox' value='$search_text' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
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
								<td class='search_box_title'><b>목록갯수</b></td>
								<td class='search_box_item'><select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
								<option value='5' ".CompareReturnValue(5,$max).">5</option>
								<option value='10' ".CompareReturnValue(10,$max).">10</option>
								<option value='20' ".CompareReturnValue(20,$max).">20</option>
								<option value='50' ".CompareReturnValue(50,$max).">50</option>
								<option value='100' ".CompareReturnValue(100,$max).">100</option>
								</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>
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
		<td colspan=2 align=center style='padding:10px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
		</form>
	</tr>
	<tr>
		<td valign=top >";

$Contents .= "
		</td>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
				<tr height=30>
					<td align=left>
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
			</table>
			<table cellpadding=2 cellspacing=0 width=100% class='list_table_box'>
				<col width='3%'>
				<col width='*'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='10%'>
				<col width='10%'>
				<col width='10%'>
				<col width='7%'>
				<tr bgcolor='#cccccc' align=center>
				<form name=listform method=post action='co_goods_server.act.php' onsubmit='return SelectUpdate(this)' target='iframe_act'><!--onsubmit='return CheckDelete(this)' target='iframe_act'-->
				<input type='hidden' name='act' value='update'>
				<input type='hidden' id='company_id_pid' value=''>
				<input type='hidden' name='chs_ix'  id='chs_ix' value='".$chs_ix."'>
					<td class=s_td height='30'><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>제품정보</td>
					<td class=m_td>공유여부</td>
					<td class=m_td>판매상태</td>
					<td class=m_td>진열</td>
					<td class=m_td>공급가</td>
					<td class=m_td>정가</td>
					<td class=m_td>판매가</td>
					<td class=e_td>관리</td>
				</tr>";



if(count($co_goods) == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td bgcolor='#efefef' align=center></td><td colspan=9 align=center> 등록된 제품이 없습니다.</td></tr>
								";

}else{
	//echo count($co_goods);
	for ($i = 0; $i < count($co_goods); $i++)
	{
		//print_r($co_goods[$i]);
		$co_goods[$i] = (array)$co_goods[$i];
		if($co_goods[$i][bimg]){
			$img_str = $co_goods[$i][simg];
		}else{
			$img_str = "../image/no_img.gif";
		}

	$innerview .= "<tr bgcolor='#ffffff'>
						<td bgcolor='#efefef' align=center title='".$co_goods[$i][admin]."-".$co_goods[$i][pid]."'>
							<input type=checkbox class=nonborder id='company_id_pid' name=company_id_pid[] value='".$co_goods[$i][admin]."-".$co_goods[$i][pid]."' ".($co_goods[$i][admin] == $admininfo[company_id] ? "disabled title='자신의 상품은 복사 하실 수 없습니다.' ":"")." >
							<input type=hidden class=nonborder id='cpid' name=cpid[] value='".$co_goods[$i][admin]."-".$co_goods[$i][pid]."'>
						</td>
						<td bgcolor='#ffffff' align=center>
						<table cellpadding=1 cellspacing=0 width='100%'>
						<tr>
							<td width=60 rowspan=5><img src='".$img_str."' width=50 height=50></td>
							<td align='left'><span style='color:gray' class='small'>".getCategoryPathByAdmin($co_goods[$i][cid], 4)."</span></td>
						</tr>
						<tr>
							<td align='left' style='line-height:140%;padding:4px 4px;'>";
	$innerview .= "<a href='../product/goods_input.php?id=".$co_goods[$i][pid]."&mode=$mode&nset=$nset&page=$page&cid2=$cid2&depth=$depth&company_id=$company_id&brand2=$brand2&max=$max&state2=$state2&disp=$disp&search_type=$search_type&search_text=$search_text' target='_blank'>
									<b> ".($co_goods[$i][brand_name] ? "[".$co_goods[$i][brand_name]."]":"")." ".$co_goods[$i][pname]."</b>

									</a><br>
									".$co_goods[$i][com_name]."
							</td>
						</tr>

						<tr>
							<td nowrap>

							</td>
						</tr>
						</table>
					</td>
					<td bgcolor='#efefef' align=center class='small'>";

						if($co_goods[$i][co_goods] == 1){
							$innerview .= "<b style='color:red;'>공유상품</b>";
						}else if($co_goods[$i][co_goods] == 0){
							$innerview .= "-";
						}

$innerview .= "
					</td>
					<td bgcolor='#ffffff' align=center class='small'>";
						if($co_goods[$i][state] == 1){
							$innerview .= "판매중";

						}else if($co_goods[$i][state] == 6){
							$innerview .= "등록신청중";
						}else if($co_goods[$i][state] == 7){
							$innerview .= "수정신청중";
						}else if($co_goods[$i][state] == 0){
							$innerview .= "일시품절중";
						}

$innerview .= "					</td>
					<td bgcolor='#efefef' align=center class='small'>";

						if($co_goods[$i][disp] == 1){
							$innerview .= "진열함";
						}else if($co_goods[$i][disp] == 0){
							$innerview .= "진열안함";
						}

$innerview .= "					</td>
					<td bgcolor='#ffffff' align=center>
					".number_format($co_goods[$i][coprice])." 원
					</td>
					<td bgcolor='#efefef' align=center nowrap>
					".number_format($co_goods[$i][listprice])." 원
					</td>
					<td bgcolor='#ffffff' align=center nowrap>
					".number_format($co_goods[$i][sellprice])." 원
					</td>
					<td bgcolor='#efefef' align=center nowrap>
						<table>

							<!--tr>
								<td><a href=\"javascript:CopyData(document.forms['listform'], '".$co_goods[$i][pid]."','".$co_goods[$i][pname]."','".$admininfo[admin_level]."');\"><img src='../images/".$admininfo["language"]."/btn_modify.gif' border=0 align=absmiddle title=\" ' ".strip_tags($co_goods[$i][pname])." ' 에 대한 정보를 수정합니다.\"></a></td>
							</tr-->

							<tr>
								<td><a href='/shop/goods_view.php?cid=".$co_goods[$i][cid]."&id=".$co_goods[$i][pid]."&depth=3&b_ix=".$co_goods[$i][brand]."' target='_blank'><img src='../images/".$admininfo["language"]."/btn_preview.gif'></a></td>
							</tr>
						</table>
					</td>

				</tr>
				</tr>
				";
	}
}
	$innerview .= "
				<tr height=50 bgcolor='#ffffff'>
					<td bgcolor='#efefef' align=center></td>
					<td colspan=9 align=right>".$str_page_bar."</td>
				</tr>
				<tr height=30 bgcolor='#ffffff'>
					<td bgcolor='#efefef' align=center></td>
					<td colspan=9 align=right style='padding:0px;'>
						<table width='100%' cellpadding=15 cellspacing=0 style='border:7px solid #efefef;border-left:0px;' bgcolor=#efefef>
						<tr bgcolor=#ffffff>
							<td style='line-height:150%' align='left'>
							<img src='/admin/images/dot_org.gif'> <b>공유 상품일괄 관리</b><br>
							<input type='radio' name='apply_data' id='apply_data_1' value='1' checked><label for='apply_data_1'><b>선택된 상품</b></label>
							<input type='radio' name='apply_data' id='apply_data_2' value='2'><label for='apply_data_2'><b>검색 상품</b> > <b>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":getCategoryPathByAdmin($cid2, $depth)."(".$total."개) <b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></label> 을(를)
							<input type='radio' name='co_goods_server' id='co_goods_server_1' value='1'><label for='co_goods_server_1'><b>공유된 상품 삭제하기</b></label>
							<input type='radio' name='co_goods_server' id='co_goods_server_2' value='2' checked><label for='co_goods_server_2'><b>공유된 상품 가져오기</b></label>";

							$innerview .= "
							으로 설정을 합니다.<br>
							</td>
						</tr>
						<tr><td align=center></td></tr>
					</table>
					</td>
				</tr>
				</table>
				<table width='100%' border=0>
				<tr height=20>
					<td align=center></td>
				</tr>
				<tr height=30>
					<td align=center>
					<img type=image src='../images/".$admininfo["language"]."/bt_modify.gif' border=0 align=center onclick='co_goods_modify(document.listform)' style='cursor:hand;'>
					</td>
				</tr>
				<tr height=20><td colspan=2 align=right></td></tr>
				</table>



				</form>
				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>

		<iframe name='act' src='' width=0 height=0></iframe>
			";
			/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small'>
	<col width=8>
	<col width=*>
	<tr>
		<td valign=top><img src='/admin/image/icon_list.gif'></td><td class='small'>공유판매 하고자 하는 상품을 선택후에 수정버튼을 눌러주세요</td>
	</tr>
	<tr>
		<td valign=top><img src='/admin/image/icon_list.gif'></td><td class='small'>공유했던 상품을 판매중지 하고자 할때는 공유안함을 선택하신다음 수정버튼을 눌러주세요</td>
	</tr>
</table>
";*/
$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= HelpBox("공유 상품일괄 관리", $help_text);
$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid2, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	parent.document.search_form.cid2.value ='$cid2';
	parent.document.search_form.depth.value ='$depth';
	</Script>";
}else{
	$Script = "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<!-- 스크립트 에러 발생으로 주석처리함 kbk -->
	<!--script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script-->
	<!-- 스크립트 에러 발생으로 주석처리함 kbk -->
	<script Language='JavaScript' type='text/javascript'>
	function co_goods_modify(frm){

		if(document.getElementById('apply_data_1').checked){
			var pid_check_bool = false;
			for(i=0;i < frm.company_id_pid.length;i++){
					if(frm.company_id_pid[i].checked){
						pid_check_bool = true;
					}
			}

			if(!pid_check_bool){
				alert('상품을 1개 이상 선택하셔야 합니다.');
				return;
			}

			//frm.action = 'co_goods.act.php';
			//frm.target='iframe_act';
			frm.submit();
		}else{
			var frm2 = document.search_form;
			for(i=0;i < frm.co_goods.length;i++){
				if(frm.co_goods[i].checked){
					frm2.co_goods.value = frm.co_goods[i].value;
				}
			}
			//frm2.action = 'co_goods.act.php';
			//frm2.target='iframe_act';
			frm2.submit();
		}

	}

	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var depth = sel.depth;

	dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}

	function clearAll(frm){
			for(i=0;i < frm.company_id_pid.length;i++){
					frm.company_id_pid[i].checked = false;
			}
	}
	function checkAll(frm){
	    for(i=0;i < frm.company_id_pid.length;i++){
					frm.company_id_pid[i].checked = true;
			}
	}
	function fixAll(frm){
		if (!frm.all_fix.checked){
			clearAll(frm);
			frm.all_fix.checked = false;

		}else{
			checkAll(frm);
			frm.all_fix.checked = true;
		}
	}

	</script>";

	$P = new LayOut();
	$P->strLeftMenu = cogoods_menu();
	$P->addScript = $Script;
	$P->Navigation = "공유상품관리 > 공유상품가져오기";
	$P->title = "공유상품가져오기";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}
?>