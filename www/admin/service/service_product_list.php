<?
include("../class/layout.class");
include_once("service.lib.php");
include("service_category.lib.php");
$db = new Database;
$db2 = new Database;
$db3 = new Database;
//print_r($pageging_info);
//echo getMySellerList($admininfo[charger_ix]);

//auth(8);
/*
if($cid == "" && $page == ""){
	$cid = $SS_CID;
	$depth = $SS_DEPTH;
	$nset = $SS_NSET;
	//$page = $SS_PAGE;
}else{
	$SS_CID = $cid;
	$SS_DEPTH = $depth;
	$SS_NSET = $nset;
	$SS_PAGE = $page;
	session_register("SS_CID");
	session_register("SS_DEPTH");
	session_register("SS_NSET");
	session_register("SS_PAGE");
}
*/
//echo $cid .":::".$depth;
//if($cid == "000000000000000") $cid = "";
//if($depth == "") $depth = "0";
//print_r($admininfo);

if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	if($pageging_info["product_list.php"]["page"] != ""){
		$page  = $pageging_info["product_list.php"]["page"];
	}else{
		$page  = 1;
	}
	if($pageging_info["product_list.php"]["nset"] != ""){
		$nset  = $pageging_info["product_list.php"]["nset"];
	}else{
		$nset  = 1;
	}
}else{
	$start = ($page - 1) * $max;
}

if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
	$birDate = $birYY.$birMM.$birDD;
}

$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));


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
	/*if($admininfo[admin_level] == 9){
		$where = "where c.company_id = p.admin and p.id Is NOT NULL and p.id = r.pid and r.basic = 1 and r.cid LIKE '".substr($cid,0,$cut_num)."%' AND p.product_type NOT IN (".implode(',',$sns_product_type).")";
	}else{
		$where = "where c.company_id = p.admin and p.id Is NOT NULL and p.id = r.pid and r.basic = 1 and admin ='".$admininfo[company_id]."' and r.cid LIKE '".substr($cid,0,$cut_num)."%' AND p.product_type NOT IN (".implode(',',$sns_product_type).") ";
	}*/
	$where = "where p.id Is NOT NULL and p.id = r.pid and r.basic = 1 and r.cid LIKE '".substr($cid,0,$cut_num)."%' AND p.product_type NOT IN (".implode(',',$sns_product_type).")";

	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}

	if($search_text != ""){
		$where .= "and p.".$search_type." LIKE '%".trim($search_text)."%' ";
	}

	/*if($sprice && $eprice){
		$where .= "and sellprice between $sprice and $eprice ";
	}

	if($company_id != ""){
		$where .= " and p.admin = '".$company_id."'";
	}*/

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

	if($product_type != ""){
		$where .= " and p.product_type = '".$product_type."'";
	}

	/*if($one_commission){
		$where .= " and p.one_commission = '".$one_commission."'";
	}*/

//echo $state;
	if($state2 != ""){
		//session_register("state");
		$where = $where." and p.state = ".$state2." ";
	}
	/*if($brand != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand."";
	}

	if($brand_name != ""){
		$where .= " and brand_name LIKE '%".trim($brand_name)."%' ";
	}*/

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  date_format(p.regdate,'%Y%m%d') between  $startDate and $endDate ";
	}

	if($cid2 != ""){
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}

	/*if($admininfo[mem_type] == "MD"){
		$where .= " and c.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
	}*/
	$sql = "SELECT count(*) as total FROM service_product p, service_product_relation r $where  ";
	//echo $sql;
	$db2->query($sql);

}else{
	if ($cid == ""){
		if($admininfo[admin_level] == 9){
			$addWhere = "Where p.id = r.pid and r.basic = 1 ";
			if($company_id != ""){
				$addWhere .= "";
			}

			/*if($admininfo[mem_type] == "MD"){
				$addWhere .= " and c.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}*/



			$db2->query("SELECT count(*) as total FROM service_product p, service_product_relation r $addWhere AND p.product_type NOT IN (".implode(',',$sns_product_type).") ");
		}else{
			$db2->query("SELECT count(*) as total FROM service_product p, service_product_relation r where p.id = r.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).") ");
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
		$where = " where p.id = r.pid ";
		if($admininfo[admin_level] == 9){

			/*if($admininfo[mem_type] == "MD"){
				$where .= " and c.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}*/

			$sql = "SELECT count(*) as total FROM service_product p, service_product_relation r
					$where and r.cid LIKE '".substr($cid,0,$cut_num)."%'
					AND p.product_type NOT IN (".implode(',',$sns_product_type).") ";

			$db2->query($sql);

		}else{
			$sql = "SELECT count(*) as total FROM service_product p, service_product_relation r
					$where and r.cid LIKE '".substr($cid,0,$cut_num)."%' AND p.product_type NOT IN (".implode(',',$sns_product_type).") ";
			$db2->query($sql);
		}

	}
}
$db2->fetch();
$total = $db2->dt[total];

$search_query = "&mode=$mode&view=innerview&product_type=$product_type&cid2=$cid2&depth=$depth&sprice=$sprice&eprice=$eprice&cid0_1=$cid0_1&cid1_1=$cid1_1&cid2_1=$cid2_1&cid3_1=$cid3_1&company_id=$company_id&brand=$brand&disp=$disp&state2=$state2&one_commission=$one_commission&search_type=$search_type&search_text=$search_text&max=$max&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD";

if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&orderby=$orderby&ordertype=$ordertype".$search_query);
}else{
	$str_page_bar = page_bar($total, $page,$max, "&orderby=$orderby&ordertype=$ordertype".$search_query);
	//echo $total.":::".$page."::::".$max."<br>";
}

$db->query("select idx from shop_icon where disp = 1 order by idx");
if($db->total){
	$icon_list = $db->fetchall();
}
$Contents =	"<table border=0 cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=4> ".GetTitleNavigation("상품리스트", "상품관리 > 상품리스트")."</td>
			</tr>
			<!--tr>
			    <td align='left' colspan=4 style='padding-bottom:15px;'>
			    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='product_list.php'\">카테고리별 상품목록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='product_list_noncategory.php'\">카테고리 미등록상품</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('design_help','tab_03')\">도움말</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >탭 메뉴 4</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_05' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >탭 메뉴 5</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_06' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >탭 메뉴 6</td>
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
			</tr-->


			 <!--tr><td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 선택된 카테고리</b> :&nbsp;<b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div>")."</td></tr-->
			 <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' ><!--input type='hidden' name='view' value='innerview'-->
			 <input type='hidden' name='mode' value='search'>
			 <input type='hidden' name='cid2' value='$cid2'>
			 <input type='hidden' name='depth' value='$depth'>
			 <input type='hidden' name='sprice' value='0' />
       <input type='hidden' name='eprice' value='1000000' />
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
								<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
									<col width=17%>
									<col width=33%>
									<col width=17%>
									<col width=33%>
									<!--tr>
										<td class='search_box_title' >  선택된 카테고리  </td>
										<td class='search_box_item'  colspan=3>  <b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div>  </td>
									</tr-->
									<tr>
										<td class='search_box_title' >카테고리선택</td>
										<td class='search_box_item' colspan=3>
											<table border=0 cellpadding=0 cellspacing=0>
												<tr>
													<td style='padding-right:5px;'>".getServiceCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
													<td style='padding-right:5px;'>".getServiceCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
													<td style='padding-right:5px;'>".getServiceCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
													<td>".getServiceCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
												</tr>
											</table>
										</td>
									</tr>
									";

				/*$Contents .=	"		<tr>
										<td class='search_box_title'>브랜드</td>
										<td class='search_box_item' colspan=3>".BrandListSelect($brand, $cid)."</td>

									</tr>";*/

				if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
				$Contents .=	"<tr>
										<!--td class='search_box_title'>입점업체</td>
										<td class='search_box_item'>
											".CompanyList2($company_id,"")."
										</td-->
										<td class='search_box_title' >상품타입</td>
										<td class='search_box_item' colspan='3'>
											<select name='product_type'>
												<option value=''>전체</option>
												<option value='0' ".ReturnStringAfterCompare($product_type, "0", " selected").">일반 상품</option>
												<option value='1' ".ReturnStringAfterCompare($product_type, "1", " selected").">해외구매대행 상품</option>
												<option value='2' ".ReturnStringAfterCompare($product_type, "2", " selected").">최저가 경매상품</option>
												<option value='3' ".ReturnStringAfterCompare($product_type, "3", " selected").">하트콘 상품</option>
											</select>
											<!--input type='radio' name='product_type'  id='product_type_' value='' ".ReturnStringAfterCompare($product_type, "", " checked")."><label for='product_type_'>전체</label>
											<input type='radio' name='product_type'  id='product_type_0' value='0' ".ReturnStringAfterCompare($product_type, "0", " checked")."><label for='product_type_0'>일반</label>
											<input type='radio' name='product_type'  id='product_type_1' value='1' ".ReturnStringAfterCompare($product_type, "1", " checked")."><label for='product_type_1'>해외구매대행</label>
											<input type='radio' name='product_type'  id='product_type_2' value='2' ".ReturnStringAfterCompare($product_type, "2", " checked")."><label for='product_type_2'>최저가경매</label>
											<input type='radio' name='product_type'  id='product_type_3' value='3' ".ReturnStringAfterCompare($product_type, "3", " checked")."><label for='product_type_2'>하트콘</label>
											</td>
											<td bgcolor='#efefef' align=left style='padding:0px 0px 0px 10px;font-weight:bold'>개별수수료</td>
											<td >
											<input type='radio' name='one_commission'  id='one_commission_' value='' ".ReturnStringAfterCompare($one_commission, "", " checked")."><label for='disp_'>전체</label>
											<input type='radio' name='one_commission'  id='one_commission_Y' value='Y' ".ReturnStringAfterCompare($one_commission, "Y", " checked")."><label for='one_commission_Y'>사용</label>
											<input type='radio' name='one_commission'  id='one_commission_N' value='N' ".ReturnStringAfterCompare($one_commission, "N", " checked")."><label for='one_commission_N'>사용안함</label-->
										</td>
									</tr>";
				}
				$Contents .=	"<tr>
										<td class='search_box_title' >진열</td>
										<td class='search_box_item' >
										<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
										<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
										<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
										</td>
										<td class='search_box_title' >판매상태</td>
										<td class='search_box_item' >
											<select name='state2'>
												<option value=''>상태값선택</option>
												<option value='1'>판매중</option>
												<option value='0'>일시품절</option>
												<option value='6'>등록신청중</option>
											</select>
										</td>
									</tr>
									";
if(false){
$Contents .=	"

									<tr>
										<td class='search_box_title'>가격  </td>
										<td class='search_box_item'colspan=3>
											<table border=0>
								                       	<tr height=10>
												<td width=90 align=left  style='font-weight:normal;font-size:11px;'>
													\\0
												</td>
												<td width=15 align=center>  </td>
												<td width=110 align=right  style='font-weight:normal;font-size:11px;'>
													\\1,000,000
												</td>
											</tr>
											<tr>
												<td colspan=3>
												<div style='position:relative;'>
													<div id='track1' style='position:relative;width:204px;height:15px;background: url(../images/price_bg.gif) no-repeat;background-position:6px 0px;'>
												    <div style='position:absolute'>
												    <div id='handle1' style='position:relative;width:5px;height:15px;cursor:move;top:10px;cursor:pointer;' ><img src='../images/point.gif'></div>
												    </div>
												    <div style='position:absolute'>
												    <div id='handle11' style='position:relative;width:5px;height:20px;cursor:move;top:10px;left:200px;cursor:pointer;'><img src='../images/point.gif'></div>
												  </div>
												 </div>
												  </td>
											<!--/tr>
											<tr height=50-->
												<td width=90 align=right style='' valign=middle>
													<table cellpadding=0 width=80 style='background-color:#efefef;border-right:1px solid silver;border-bottom:1px solid silver;'>
												    		<tr><td style='padding:0px 3px' align=right><div id='debug1' style='padding-top: 2px;font-weight:normal;color:#000000'>\\0</div></td></tr>
												    	</table>				</td>
												<td width=15 align=center> - </td>
												<td width=90 align=left>
													<table cellpadding=0 width=80 style='background-color:#efefef;border-right:1px solid silver;border-bottom:1px solid silver;text-align:right'>
												    		<tr><td style='padding:0px 3px' align=right><div id='debug11' style='padding-top: 2px;font-weight:normal;color:#000000'>\\1,000,000</div></td></tr>
												    	</table>
												</td>
											</tr>
											</table>
											<script type='text/javascript' language='javascript'>
										  // <![CDATA[
										    new Control.Slider('handle1','track1',{
										      onSlide:function(v){\$('debug1').innerHTML='\\\\'+FormatNumber(Math.ceil(v*1000000))},
										      onChange:function(v){\$('debug1').innerHTML='\\\\'+FormatNumber(Math.ceil(v*1000000));document.search_form.sprice.value=Math.ceil(v*1000000);}});

										     new Control.Slider('handle11','track1',{
										        onSlide:function(v){\$('debug11').innerHTML='\\\\' + FormatNumber(Math.ceil(v*1000000))},
										        onChange:function(v){\$('debug11').innerHTML='\\\\'+ FormatNumber(Math.ceil(v*1000000));document.search_form.eprice.value=Math.ceil(v*1000000);},sliderValue:1});
										  // ]]>
										                        </script>
										</td>
									</tr>";
}
$Contents .=	"

									<tr>
										<td class='search_box_title'>  검색어  </td>
										<td class='search_box_item'>
											<table cellpadding=0 cellspacing=0>
											<tr>
												<td><select name='search_type'  style=\"font-size:12px;\">
													<option value='pname'>상품명</option>
													<!--option value='pcode'>상품코드</option>
													<option value='id'>상품코드(키)</option-->
													</select></td>
												<td style='padding-left:5px;'>
												<INPUT id=search_texts class='textbox1' value=''  autocomplete='off'  clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><!--onkeyup='findNames();'  onclick='findNames();' onFocusOut='clearNames()' --><br>

												<DIV id=popup style='DISPLAY: none; WIDTH: 230px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
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

											</tr>
											</table>
										</td>
										<td class='search_box_title'>목록갯수</td>
										<td class='search_box_item'>
										<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
										<option value='2' ".CompareReturnValue(2,$max).">2</option>
										<option value='5' ".CompareReturnValue(5,$max).">5</option>
										<option value='10' ".CompareReturnValue(10,$max).">10</option>
										<option value='20' ".CompareReturnValue(20,$max).">20</option>
										<option value='50' ".CompareReturnValue(50,$max).">50</option>
										<option value='100' ".CompareReturnValue(100,$max).">100</option>
										</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."
										</span></td>
									</tr>
									<tr height=27>
									  <td class='search_box_title'><label for='regdate'>등록일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_form);' ".CompareReturnValue("1",$regdate,"checked")."></td>
									  <td class='search_box_item' colspan=3 >
										<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
											<tr>
												<TD width=210 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY style='width:57px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM style='width:43px;'></SELECT> 월 <SELECT name=FromDD style='width:43px;'></SELECT> 일 </TD>
												<TD width=14 align=left>~</TD>
												<TD width=210 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY style='width:57px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM style='width:43px;'></SELECT> 월 <SELECT name=ToDD style='width:43px;'></SELECT> 일</TD>
												<TD width='*'>
													<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
													<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
													<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
													<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
													<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
													<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
													<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
												</TD>
											</tr>
										</table>
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
			<tr><td height='50' colspan=2 align=center style='padding:10px 0px'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle> <!--btn_inquiry.gif--></td></td>
			</form>
			<tr>
			<td valign=top>";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_list>
			";


$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
			<tr>
				<!--td>상품수 : ".number_format($total)." 개</td-->
				<td align=right colspan=2>".$str_page_bar."</td>
			</tr>
			<tr>

				<td align=left height=30> ";
if(checkMenuAuth(md5("/admin/product/goods_input.php"),"D") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$innerview .= "<a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a>";
}

$innerview .= "	</td>
				<td align=right>
				<b class=small>판매순</b>
				<a href='product_list.php?orderby=order_cnt&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "order_cnt" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='높은판매수순'></a>
				<a href='product_list.php?orderby=order_cnt&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "order_cnt" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='낮은판매수순'></a> |
				<b class=small>조회순</b>
				<a href='product_list.php?orderby=view_cnt&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "view_cnt" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='높은조회수순'></a>
				<a href='product_list.php?orderby=view_cnt&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "view_cnt" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='낮은조회수순'></a> |
				<b class=small>판매가격순</b>
				<a href='product_list.php?orderby=sellprice&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "sellprice" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='높은가격순'></a>
				<a href='product_list.php?orderby=sellprice&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "sellprice" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='낮은가격순'></a> |
				<b class=small>적립금순</b>
				<a href='product_list.php?orderby=reserve&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "reserve" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='높은적립금순'></a>
				<a href='product_list.php?orderby=reserve&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "reserve" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='낮은적립금순'></a> |
				<b class=small>상품명순</b>
				<a href='product_list.php?orderby=pname&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "pname" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='가나다순'></a>
				<a href='product_list.php?orderby=pname&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "pname" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='가나다역순'></a>
				<b class=small>등록일자순</b>
				<a href='product_list.php?orderby=r.regdate&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".((($orderby == "r.regdate" && $ordertype ==  "desc") || ($orderby == "" && $ordertype ==  "")) ? "on":"off").".gif' border=0 align=absmiddle title='최근등록순'></a>
				<a href='product_list.php?orderby=r.regdate&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "r.regdate" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='등록순'></a>
				<b class=small>이름정렬순</b>
				<a href='product_list.php?orderby=vieworder2&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "vieworder2" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='역순'></a>
				<a href='product_list.php?orderby=vieworder2&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "vieworder2" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='순'></a>


				</td>
				</tr>
				</table>
			<form name=listform method=post action='service_product_list.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
			<input type='hidden' name='act' value='select_delete'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='product_type' value='$product_type'>
			<input type='hidden' name='max' value='$max'>

			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
			<tr bgcolor='#ffffff' align=center height=30>
				<td width='5%' class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
				<!--td width='10%' class=m_td>상품코드</td-->
				<td width='10%' class=m_td>이미지</td>
				<td width='37%' class=m_td>상품명</td>
				<td width='*' class=m_td>가격</td>
				<!--td width='10%' class=m_td></td-->
				<td width='23%' class=e_td>관리/<a href='service_product_list.php?state2=$state2&disp=$disp&brand_name=$brand_name&cid2=$cid2&depth=$depth'>날짜</a></td>
			</tr>";



if($orderby != "" && $ordertype != ""){
	$orderbyString = " order by $orderby $ordertype ";
}else{
	$orderbyString = " order by r.regdate desc ";
}

if($mode == "search"){
$where = " AND p.product_type NOT IN (".implode(',',$sns_product_type).")";
	if($search_text != ""){
		$where .= "and p.".$search_type." LIKE '%".trim($search_text)."%' ";
	}

	if($sprice && $eprice){
		$where .= "and sellprice between $sprice and $eprice ";
	}

	if($status_where){
		$where .= " and ($status_where) ";
	}

	/*if($brand != ""){
		$where .= " and brand = ".$brand."";
	}

	if($brand_name != ""){
		$where .= " and brand_name LIKE '%".trim($brand_name)."%' ";
	}*/

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

	/*if($one_commission){
		$where .= " and p.one_commission = '".$one_commission."'";
	}

	if($company_id != ""){
		$where .= " and p.admin = '".$company_id."'";
	}*/

	if($state2 != ""){
		$where .= " and state = ".$state2."";
	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  date_format(p.regdate,'%Y%m%d') between  $startDate and $endDate ";
	}
//	$where .= " and r.cid LIKE '".substr($cid,0,$cut_num)."%' ";
	if($product_type != ""){
		$where .= " and p.product_type = '".$product_type."'";
	}

	if($cid2 != ""){
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}
	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			//$addWhere = "and admin ='".$company_id."'";
		}else{
			unset($addWhere);
		}

		/*if($admininfo[mem_type] == "MD"){
			$addWhere .= " and c.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}*/

		$sql = "SELECT p.id, p.pname, p.sellprice, p.regdate,p.vieworder, r.cid,  p.search_keyword,state,
		p.coprice, p.disp, p.editdate, p.reserve, p.reserve_rate, listprice,p.one_commission, p.commission,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2
		FROM service_product p, service_product_relation r
		where p.id = r.pid and r.basic = '1' $addWhere AND p.product_type NOT IN (".implode(',',$sns_product_type).") $where $orderbyString LIMIT $start, $max";
		//echo $sql;
		$db->query($sql);
	}else{
		$sql = "SELECT p.id, p.pname, p.sellprice, p.regdate,p.vieworder,r.cid,  p.search_keyword,state,
		p.coprice,  p.disp, p.editdate, p.reserve, p.reserve_rate, listprice,p.one_commission, p.commission,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2
		FROM service_product p, service_product_relation r
		where p.id = r.pid and r.basic = '1' AND p.product_type NOT IN (".implode(',',$sns_product_type).") $where $orderbyString LIMIT $start, $max";


		$db->query($sql);
	}



}else{


	if ($cid2 == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				//$addWhere = "and admin ='".$company_id."'";
			}else{
				unset($addWhere);
			}

			/*if($admininfo[mem_type] == "MD"){
				$addWhere .= " and c.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}*/
			//$tmp_sql = "create temporary table ".TBL_LOGSTORY_BYPAGE."_tmp ENGINE = MEMORY select vdate, pageid, ncnt, nduration from service_product_relation where vdate = '$vdate' ";

			$sql = "SELECT p.id , p.pname, p.sellprice, p.regdate,p.vieworder,r.cid,  p.search_keyword,state,
			p.coprice,   p.disp, p.editdate, p.reserve, p.reserve_rate, listprice,p.one_commission, p.commission,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			FROM service_product p, service_product_relation r
			where p.id = r.pid and r.basic = '1'
			AND p.product_type NOT IN (".implode(',',$sns_product_type).")
			".$addWhere."
			".$orderbyString." LIMIT $start, $max";
			//echo nl2br($sql);
			$db->query($sql);
		}else{
			$sql = "SELECT p.id , p.pname, p.sellprice, p.regdate,p.vieworder,  r.cid,  p.search_keyword,state,
			p.coprice, p.disp, p.editdate, p.reserve, p.reserve_rate, listprice,p.one_commission, p.commission,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			FROM service_product p, service_product_relation r
			where p.id = r.pid and r.basic = '1' AND p.product_type NOT IN (".implode(',',$sns_product_type).") $orderbyString LIMIT $start, $max";
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
			if($admininfo[mem_type] == "MD"){
				//$addWhere .= " and c.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}

			$sql = "SELECT distinct (p.id) as id, p.pname, p.sellprice, p.regdate,p.vieworder, r.cid, p.search_keyword,state,
				p.coprice,   p.disp, p.editdate,  p.reserve_rate, listprice,p.one_commission, p.commission,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2
				FROM service_product p, service_product_relation r
				where p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' AND p.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere $orderbyString LIMIT $start, $max";

			//echo $sql;

			$db->query($sql);
		}else{
			$sql = "SELECT distinct (p.id) as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,  r.cid, p.search_keyword,state, p.coprice,   p.disp, p.editdate,  p.reserve_rate, listprice,p.one_commission, p.commission,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2
				FROM service_product p, service_product_relation r
				where p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' AND p.product_type NOT IN (".implode(',',$sns_product_type).") $orderbyString LIMIT $start, $max";

				//echo $sql;
				$db->query($sql);

				//echo "test".$db->total;

		}


	}

}
//echo $sql;
if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=150><td colspan=5 align=center> 등록된 상품이 없습니다.</td></tr>";
}else{

	//$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=8 align=center> $sql</td></tr>";
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/service_product", $db->dt[id], "s"))){
			$img_str = PrintImage($admin_config[mall_data_root]."/images/service_product", $db->dt[id], "s");
		}else{
			$img_str = "../image/no_img.gif";
		}
		/*if($layout_config[mall_use_inventory] == "Y" && $admininfo["mall_type"] != "O"){
			$db3->query("select h.pi_ix,h.place_name from inventory_place_info h , shop_product p where h.pi_ix = p.inventory_info and id = '".$db->dt[id]."' ");
			if($db3->total){
				$db3->fetch();
				$i_ix = $db3->dt[i_ix];
				$inventory_name = $db3->dt[inventory_name];
			}else{
				$i_ix = "";
				$inventory_name = "미등록";
			}
		}*/
	$innerview .= "<tr bgcolor='#ffffff'>
					<td class='list_box_td list_bg_gray' bgcolor='#efefef' align=center><input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$db->dt[id]."'></td>
					<td class='list_box_td' align=center >";
					if($admininfo[mall_use_multishop]){
						if($db->dt[state] == 1){
							$innerview .= "<div id='state_txt_".$db->dt[id]."'><a href='service_product_list.act.php?act=state_update&pid=".$db->dt[id]."&state=".$db->dt[state]."'   target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_sell.gif' align=absmiddle></a></div>";
						}else if($db->dt[state] == 6){
							$innerview .= "	<span style='color:red;font-weight:bold;'>[등록신청중]</span>";
						}else if($db->dt[state] == 0){
							$innerview .= "<div id='state_txt_".$db->dt[id]."'><a href='service_product_list.act.php?act=state_update&pid=".$db->dt[id]."&state=".$db->dt[state]."'   target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_sold_out.gif' align=absmiddle></a></div>";
						}

						if($db->dt[disp] == 1){
							$innerview .= "<div id='disp_txt_".$db->dt[id]."'><a href='service_product_list.act.php?act=disp_update&pid=".$db->dt[id]."&disp=".$db->dt[disp]."'  target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_off_view.gif' align=absmiddle></a></div>";
						}else if($db->dt[disp] == 0){
							$innerview .= "<div id='disp_txt_".$db->dt[id]."'><a href='service_product_list.act.php?act=disp_update&pid=".$db->dt[id]."&disp=".$db->dt[disp]."'   target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_on_view.gif' align=absmiddle></a></div>";
						}
					}


	$innerview .= "	<br><a href='goods_input.php?id=".$db->dt[id]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/service_product", $db->dt[id], $LargeImageSize)."'  ><img src='".$img_str."' width=50 height=50></a><br>
					<!--input type=\"checkbox\" name='disp".$db->dt[id]."' class=nonborder id=disp_id value=1 ".($db->dt[disp] == 1 ? " checked":"")."><label for=\"disp_id\" >표시</label-->
					</td>
					<td class='list_box_td point' style='padding:6px 0px;'>
						<table cellpadding=0 cellspacing=0 width='100%'>
						<tr>
							<td style='padding:0px 0px 0px 5px'>
							<table cellpadding=0 cellspacing=0 >
							<tr>
								<td > <span style='color:gray' >".getCategoryPathByAdmin($db->dt[cid], 4)."</span></td>
								<td style='padding:0px 4px'><a href='service_goods_input.php?id=".$db->dt[id]."' target='_blank'><img src='../images/".$admininfo["language"]."/btn_edit_new.gif' align=absmiddle></a></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td align=left style='padding:0px 0px 0px 5px'>";
			$innerview .= "	<a href='service_goods_input.php?id=".$db->dt[id]."' target='_self'><div style='font-weight:bold;font-size:12px;padding:5px 0px'><b> ".($db->dt[brand_name] ? "[".$db->dt[brand_name]."]":"")."  ".$db->dt[pname]."</b></div></a>
							".($db->dt["new"] == 1 ? "<img src='".$admin_config[mall_data_root]."/images/icon/icon_new.gif' border=0 align=absmiddle>":"")."
							".($db->dt["hot"] == 1 ? "<img src='".$admin_config[mall_data_root]."/images/icon/icon_hot.gif' border=0 align=absmiddle>":"")."
							".($db->dt["event"] == 1 ? "<img src='".$admin_config[mall_data_root]."/images/icon/icon_event.gif' border=0 align=absmiddle>":"")."
							</td>
						</tr>

						<tr>
							<td nowrap>
								<table border=0 cellpadding=2 cellspacing=0 width='100%'>
								<col width=20%>
								<col width=80%>
								<!--tr height=25>
										<td align=left style='padding:0px 0px 0px 5px'>상품상태</td>
										<td >
										<table cellpadding='0' cellspacing='0' border='0' >
											<tr>
											<td>".($admininfo[charger_id] == "forbiz" ? SellState("state_".$db->dt[id], $db->dt[state]):"")."
											</td>
											</tr>
										</table>
										</td>
								</tr-->
								<tr height=25>
									<td align=left style='padding:0px 0px 0px 5px'>	검색어 : </td>
									<td align=left style='padding-right:5px;'><input type=text class=textbox2 id='search_keyword".$db->dt[id]."' name='search_keyword".$db->dt[id]."' style='width:95%' value='".$db->dt[search_keyword]."'></td>
								</tr>
								<tr height=25>
									<td align=left style='padding:0px 0px 0px 5px'> 적립금 : </td>
									<td align=lef>
									<table cellpadding='0' cellspacing='0' border='0'>
										<tr>
										<td width='93' align=left>
											<input type=text class=textbox id='reserve".$db->dt[id]."' name='reserve".$db->dt[id]."' style='text-align:right;width:80px; padding-right:3px;' onkeypress='num_check()'   onkeyup='this.value=FormatNumber3(this.value);' value='".$db->dt[reserve]."'>
										</td>
										<td style='padding:0px 0px 0px 0px'>
											<select name='reserve_rate".$db->dt[id]."' style='' onchange=\"if(this.form.sellprice".$db->dt[id].".value == ''){alert(language_data['product_list.php']['A'][language]);}else{this.form.reserve".$db->dt[id].".value=Round2(filterNum(this.form.sellprice".$db->dt[id].".value) * this.value/100,1,1);}\">
												<option value=0 ".CompareReturnValue(0,$db->dt[reserve_rate]," selected").">0%</option>
												<option value='0.5' ".CompareReturnValue(0.5,$db->dt[reserve_rate]," selected").">0.5%</option>
												<option value=1 ".CompareReturnValue(1,$db->dt[reserve_rate]," selected").">1%</option>
												<option value='1.5' ".CompareReturnValue(1.5,$db->dt[reserve_rate]," selected").">1.5%</option>
												<option value='2' ".CompareReturnValue(2,$db->dt[reserve_rate]," selected").">2%</option>
												<option value='2.5' ".CompareReturnValue(2.5,$db->dt[reserve_rate]," selected").">2.5%</option>
												<option value=3 ".CompareReturnValue(3,$db->dt[reserve_rate]," selected").">3%</option>
												<option value=5 ".CompareReturnValue(5,$db->dt[reserve_rate]," selected").">5%</option>
												<option value=6 ".CompareReturnValue(6,$db->dt[reserve_rate]," selected").">6%</option>
												<option value=10 ".CompareReturnValue(10,$db->dt[reserve_rate]," selected").">10%</option>
											</select>
										</td>
										</tr>
									</table>
									</td>
								</tr>
								";

$innerview .= "

								</table>
							</td>
						</tr>

						</table>
					</td>
					<td class='list_box_td list_bg_gray' bgcolor='#efefef' align=center>
						<table cellpadding=3 cellspacing=0 align=center>
							<tr ".($admininfo[mall_type] == "O" ? "style='display:none;'":"")."><td align=left style='padding:0px 0px 0px 5px'>구매단가(공급가) </td><td>:</td><td>".$currency_display[$admin_config["currency_unit"]]["front"]."  <input type=text class=textbox2 size=10 id='coprice".$db->dt[id]."' name='coprice".$db->dt[id]."' style='text-align:right; padding-right:3px;' value='".number_format($db->dt[coprice],0)."'> ".$currency_display[$admin_config["currency_unit"]]["back"]."</td></tr>
							<tr><td align=left style='padding:0px 0px 0px 5px'>정가 </td><td>:</td><td> ".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class=textbox2 size=10 id='listprice".$db->dt[id]."' name='listprice".$db->dt[id]."' style='text-align:right; padding-right:3px;' value='".number_format($db->dt[listprice],0)."'> ".$currency_display[$admin_config["currency_unit"]]["back"]."</td></tr>
							<tr><td align=left style='padding:0px 0px 0px 5px'>판매가(할인가) </td><td>:</td><td> ".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class=textbox2 size=10 id='sellprice".$db->dt[id]."' name='sellprice".$db->dt[id]."' style='text-align:right; padding-right:3px;' value='".number_format($db->dt[sellprice],0)."'> ".$currency_display[$admin_config["currency_unit"]]["back"]."</td></tr>";
if($admininfo[mall_use_multishop]){
$innerview .= "
							<tr><td align=left style='padding:0px 0px 0px 5px'>개별수수료 </td><td>:</td><td align=center><b>".($db->dt[one_commission] == "Y" ? "사용":"사용안함")."</b></td></tr>
							<tr><td align=left style='padding:0px 0px 0px 5px'>수수료</td><td>:</td><td align=center><b>".$db->dt[commission]." %</b></tr>";
						}
$innerview .= "
						</table>
					</td>
					<td class='list_box_td' style='padding:10px;text-align:center;' nowrap>
						<table align=center>
							<tr><input type='hidden' id='h_pname_".$db->dt[id]."' name='h_pname_".$db->dt[id]."' value=\"".$db->dt[pname]."\" />";
if(checkMenuAuth(md5("/admin/product/goods_input.php"),"C") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
						$innerview .= "<td><a href='service_goods_input.php?mode=copy&id=".$db->dt[id]."'><img src='../images/".$admininfo["language"]."/btc_copy.gif' border=0 align=absmiddle ></a></td>";
}else{
						$innerview .= "<td><a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btc_copy.gif' border=0 align=absmiddle title=\" ' ".$db->dt[pname]." '  에 대한 정보를 수정합니다.\"></a></td>";
}
if(checkMenuAuth(md5("/admin/product/goods_input.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						$innerview .= "<td><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle title=\" ' ".$db->dt[pname]." '  에 대한 정보를 수정합니다.\" onclick=\"CopyData('listform', '".$db->dt[id]."');\" style='cursor:hand;'></td>";
}else{
						$innerview .= "<td><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle title=\" ' ".$db->dt[pname]." '  에 대한 정보를 수정합니다.\"></a></td>";
}
if(checkMenuAuth(md5("/admin/product/goods_input.php"),"D") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						$innerview .= "<td><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"deleteProduct('delete','".$db->dt[id]."','&cid=$cid&depth=$depth')\"></td>";
}else{
						$innerview .= "<td><a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle ></a></td>";
}
$innerview .= "
							</tr>
							<!--tr><td colspan=3><a href=\"javascript:PoPWindow('product_after.php?id=".$db->dt[id]."',650,700,'output_pop')\"><img src='../images/".$admininfo["language"]."/btc_photo.gif' border=0 align=absmiddle style='cursor:pointer' border=0></a></td></tr-->";
		/*if($admin_config[mall_use_inventory] == "Y" && $admininfo["mall_type"] != "O"){
			$innerview .= "<tr>
							<td colspan=3>
								<table border=0 cellpadding=0 cellspacing=0>
									<tr>
										<td>";

										$innerview .= "
										<a href=\"javascript:PoPWindow3('../inventory/input_pop.php?id=".$db->dt[id]."&i_ix=".$i_ix."',650,700,'input_pop')\"><img src='../images/".$admininfo["language"]."/btn_input.gif'></a>
										<a href=\"javascript:PoPWindow3('../inventory/delivery_pop.php?id=".$db->dt[id]."&i_ix=".$i_ix."',650,700,'output_pop')\"><img src='../images/".$admininfo["language"]."/btn_output.gif'></a>";
										$innerview .= "
										<a href=\"javascript:PoPWindow('after_pop.php?pid=".$db->dt[id]."',850,500,'after_pop')\"><img src='../images/".$admininfo["language"]."/btc_photo.gif'></a>
										</td>
									</tr>
								</table>
							</td>
							</tr>";
		}*/
					$innerview .= "</table><br>
					등록일자 : ".$db->dt[regdate]."<br><br>
					최종수정일자 : ".$db->dt[editdate]."<br><br> ";
					/*if($admininfo[mall_use_multishop]){
					$innerview .= "공급업체 : ".$db->dt[com_name];
					}
					if($layout_config[mall_use_inventory] == "Y" && $admininfo["mall_type"] != "O"){
					$innerview .= "<br><br>보관장소 : ".$inventory_name;
					}*/
	$innerview .= "</td>
				</tr>";

	}
}
	$innerview .= "</table>";

$innerview .= "
				<table width='100%'><tr>
					<td height=30>";
if(checkMenuAuth(md5("/admin/product/goods_input.php"),"D") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
	$innerview .= "<a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a> ";
}
if(checkMenuAuth(md5("/admin/product/goods_input.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$innerview .= "<a href=\"JavaScript:GoodsSelectUpdate(document.forms['listform']);\"><img src='../images/".$admininfo["language"]."/bt_all_modify.gif' border=0 align=absmiddle style='cursor:pointer;'></a> ";
}
//if($admininfo[charger_id] == "forbiz"){
	$innerview .= "
				<a href='product_list_excel2003.php?".$_SERVER["QUERY_STRING"]."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align=absmiddle ></a>";
//}
	$innerview .= "
				</td><td align=right>".$str_page_bar."</td></tr></table>
				</form>
				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<form name=saveform method=get action='./service_product_list.act.php' target='iframe_act'>
				<input type='hidden' name='act' value='update_one'>
				<input type='hidden' name='pid'>
				<input type='hidden' name='pcode'>
				<input type='hidden' name='disp'>
				<input type='hidden' name='reserve'>

				<input type='hidden' name='reserve_rate'>

				<input type='hidden' name='search_keyword'>
				<input type='hidden' name='coprice'>
				<input type='hidden' name='sellprice' >
				<input type='hidden' name='listprice' >
				<input type='hidden' name='state' >

			</form>

			";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle height='25'><img src='/admin/image/icon_list.gif' align='absmiddle' boder='0'></td><td class='small' >리스트에서 기본적인 정보를 수정하실수 있습니다</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' align='absmiddle' boder='0' ></td><td class='small' >개별정보를 수정후 <img src='../image/btc_modify.gif' align=absmiddle> 버튼를 클릭하시면 해당 상품만을 수정하실수 있습니다</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' align='absmiddle' boder='0' ></td><td class='small' >리스트의 여러상품을 수정후 <img src='../image/bt_all_modify.gif' align=absmiddle> 버튼를 클릭하시면 해당 리스트에 보여지는 전체 상품을 수정하실수 있습니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B',$admininfo, 'admininfo');


$Contents .= HelpBox("서비스상품리스트", $help_text,'100');

$Script = "<script language='javascript' src='../include/DateSelect.js'></script>\n
	<Script Language='JavaScript' src='/admin/js/autocomplete.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<script Language='JavaScript' src='service_product_input.js'></script>
	<script Language='JavaScript' src='service_product_list.js'></script>
	<script src='../js/scriptaculous.js' type='text/javascript'></script>
	<script  id='dynamic'></script>";
$Script .= "
<script language='javascript'>

function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
	}else{
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	}
}


function init(){

	var frm = document.search_form;
	onLoad('$sDate','$eDate');";

if($regdate != "1"){
$Script .= "
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;";
}

$Script .= "
}

</script>";
//$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";

if($view == "innerview"){
	$pageging_info["product_list.php"]["page"] = $page;
	$pageging_info["product_list.php"]["nset"] = $nset;

	session_register("pageging_info");

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>

<body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid2, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	//parent.document.search_form.cid.value ='$cid';
	parent.document.search_form.depth.value ='$depth';
	parent.LargeImageView();
	</Script>";
}else{

	$P = new LayOut();
	$P->strLeftMenu = service_menu("/admin",$category_str);
	$P->OnloadFunction = "init();";
	$P->addScript = $Script;
	$P->Navigation = "서비스관리 > 서비스상품리스트";
	$P->title = "서비스상품리스트";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}



function SellState($select_name, $vstate){
	global $admininfo;

	if($admininfo[admin_level] == 9){
		$mstring = "
		<Select name='$select_name' style='vertical-align:middle;width:139px;border:1px solid silver;'>
			<option value=0 ".($vstate == 0 ? "selected":"").">일시품절</option>
			<option value=1 ".(($vstate == 1 || $vstate == "") ? "selected":"").">판매중</option>";
		/*if($admininfo[mall_use_multishop]){
		$mstring .= "<option value=6 ".($vstate == 6 ? "selected":"").">입점업체 등록신청</option>";
		}*/
		$mstring .= "</Select>";
	}else if($admininfo[admin_level] == 8){
		$mstring = "
		<Select name=state style='vertical-align:middle'>
			<option value=0 ".($vstate == 0 ? "selected":"").">일시품절</option>";
		if ($vstate == 1 ){
		$mstring .= "<option value=1 ".($vstate == 1 ? "selected":"").">판매중</option>";
		}
		/*if($admininfo[mall_use_multishop]){
		$mstring .= "<option value=6 ".(($vstate == 6 || $vstate == "") ? "selected":"").">입점업체 등록신청</option>";
		}*/
		$mstring .= "</Select>";
	}
	return $mstring;
}


function getServiceCategoryList3($category_text ="기본카테고리 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	//$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	$tb="service_category_info";
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}




	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler style='width:140px;font-size:12px;'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler validation=false  style='width:140px;font-size:12px;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}

?>
