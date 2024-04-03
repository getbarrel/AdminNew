<?

//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("sellertool.lib.php");
include("../openapi/openapi.lib.php");
include("../class/layout.class");



$goods_input_type = "sellertool";



$script_time[start] = time();
//print_r($_GET);
$db = new Database;
$db2 = new Database;


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

$script_time[count_start] = time();
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
		$where = "where p.id Is NOT NULL and p.id = r.pid and r.basic = 1 and p.is_delete = '0' ";
	}else{
		$where = "where p.id Is NOT NULL and p.id = r.pid and r.basic = 1 and admin ='".$admininfo[company_id]."' and p.is_delete = '0' ";
	}

	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}
	if($company_id != ""){
		$where = $where."and p.admin = '".$company_id."' ";

	}

	if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
		//다중검색 시작 2014-04-10 이학봉
		if($search_text != ""){
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n
				$search_array = explode("\n",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$where .= "and ( ";

				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$where .= ")";
			}else{
				$where .= " and ".$search_type." = '".trim($search_text)."'";
			}
		}

	}else{	//검색어 단일검색
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
    $startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  date_format(p.regdate,'%Y%m%d') between  $startDate and $endDate ";
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
			$addWhere = "Where p.id = r.pid  and r.basic = 1";
			if($company_id != ""){
				$addWhere .= " and admin ='".$company_id."' ";
			}


			$db2->query("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  $addWhere and p.is_delete = '0' ");
		}else{
			$db2->query("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  where  p.id = r.pid and admin ='".$admininfo[company_id]."' and p.is_delete = '0' ");
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
			$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  where p.id = r.pid and r.basic = 1 and r.cid LIKE '".substr($cid2,0,$cut_num)."%' ";

			$db2->query($sql);

		}else{
			$db2->query("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  where p.id = r.pid and r.basic = 1 and r.cid LIKE '".substr($cid2,0,$cut_num)."%' and admin ='".$admininfo[company_id]."' ");
		}

	}
}

$db2->fetch();

$total = $db2->dt[total];

$script_time[count_end] = time();
if($max == ""){
	$max = 40; //페이지당 갯수
}else{
	$max = $max;
}


//이게 뭐하는 코드인지는 모르겠으나 현재 이코드로인해 페이징에 문제가 생김 JK161116
/*
if(round($total/$max)  <= $pageging_info["product_bsgoods.php"]["page"]){
	unset($pageging_info);
	session_unregister("pageging_info");
	$page = 1;
	//echo $pageging_info["product_bsgoods.php"]["page"];
	//exit;
}
*/


if ($page == ''){
	if($pageging_info["product_bsgoods.php"]["page"] != ""){
		$page  = $pageging_info["product_bsgoods.php"]["page"];
		$start = ($page - 1) * $max;
	}else{		
		$page  = 1;
		$start = 0;
	}
	if($pageging_info["product_bsgoods.php"]["nset"] != ""){
		$nset  = $pageging_info["product_bsgoods.php"]["nset"];
	}else{
		$nset  = 1;
	}
}else{
	$start = ($page - 1) * $max;
}


//echo $page;
//exit;
//http://dev.mallstory.com/admin/product/product_bsgoods.php?mode=search&cid2=&depth=&&cid0_1=&cid1_1=&cid2_1=&cid3_1=&bs_site=&company_id=&brand_name=&disp=&state2=&search_type=pname&search_text=&max=10&x=64&y=21
$search_query = "&mode=$mode&view=innerview&cid2=$cid2&depth=$depth&bsmode=$bsmode&cid0_1=$cid0_1&cid1_1=$cid1_1&cid2_1=$cid2_1&cid3_1=$cid3_1&bs_site=$bs_site&company_id=$company_id&brand_name=$brand_name&disp=$disp&state2=$state2&search_type=$search_type&search_text=$search_text&max=$max&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&mult_search_use=$mult_search_use";

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
		<td align='left' colspan=4> ".GetTitleNavigation("제휴사 상품연동", "제휴사연동 > 제휴사 상품연동")."</td>
	</tr>

	<tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
						
							<table id='tab_02' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'>상품 검색</td>
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
								<td class='input_box_item' ><b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> <!--로 검색된 결과 입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O')." ")."</b></div></td>
			     
                                <td class='search_box_title'>입점업체</td>
								<td class='search_box_item'>
									".CompanyList($company_id,"")."
								</td>
                            
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
						  </tr>
							";
							if($admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
								$Contents .=	"
							<!--tr>
								<td class='input_box_title'>입점업체</td>
								<td class='input_box_item'>".CompanyList($company_id,"")."</td>
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
								<td class='input_box_title'>  검색어 <input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
								<label for='mult_search_use'>(다중검색 체크)</label> </td>
								<td class='input_box_item'>
									<table cellpadding=0 cellspacing=0>
										<tr>
											<td><select name='search_type'  id='search_type' style=\"font-size:12px;height:22px;\">
												<option value='pname' ".ReturnStringAfterCompare($search_type, "pname", " selected").">상품명</option>
												<option value='pcode' ".ReturnStringAfterCompare($search_type, "pcode", " selected").">상품코드</option>
												<option value='p.id' ".ReturnStringAfterCompare($search_type, "p.id", " selected").">상품코드(key)</option>
												<option value='bimg' ".ReturnStringAfterCompare($search_type, "bimg", " selected").">상품이미지</option>
												<option value='sellprice' ".ReturnStringAfterCompare($search_type, "sellprice", " selected").">판매가</option>
												
												</select>
											</td>
											<td style='padding-left:5px;'>
												<div id='search_text_input_div'>
													<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
												</div>
												<div id='search_text_area_div' style='display:none;'>
													<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
												</div>
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
										<option value='40' ".CompareReturnValue(40,$max).">40</option>
										<option value='50' ".CompareReturnValue(50,$max).">50</option>
										<option value='100' ".CompareReturnValue(100,$max).">100</option>
									</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span>
								</td>
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
	<tr>
		<td height=50 colspan=2 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
	</form>
    </table>
    <form name=listform method=post action='sellertool.goods.regist.to.market.act.php' onsubmit='return SelectUpdate(this)' ".($_SERVER["REMOTE_ADDR"] == '221.151.188.11' ? "target=''" : "target='act'")." ><!--onsubmit='return CheckDelete(this)' target='act'-->
				<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
				<input type='hidden' id='pid' value=''>
				<input type='hidden' name='act' value='regist'>";

$Contents .= "<table cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td valign=top style='padding-top:33px;'>";
//TODO:폼 수정
$Contents .= "
		</td>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";


$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
				<tr>
					
					<td height=30 align=left>";
if(checkMenuAuth(md5("sellertool_goods_input.php"),"D") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$innerview .= "<a href=\"JavaScript:SelectDeleteBuyingServiceGooods(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a>";
}
$innerview .= "
					상품수 : ".number_format($total)." 개";

$innerview .= "
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>";
if(false){
$innerview .= "
				<tr>

				<td height=30 align=left colspan=2> <!--a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a-->
				<b class=small>판매가격순</b>
				<a href='product_bsgoods.php?orderby=sellprice&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "sellprice" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='높은가격순'></a>
				<a href='product_bsgoods.php?orderby=sellprice&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "sellprice" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='낮은가격순'></a> |
				<!--b class=small>적립금</b>
				<a href='product_bsgoods.php?orderby=reserve&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "reserve" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='높은적립금순'></a>
				<a href='product_bsgoods.php?orderby=reserve&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "reserve" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='낮은적립금순'></a> |-->
				<b class=small>상품명</b>
				<a href='product_bsgoods.php?orderby=pname&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "pname" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='가나다순'></a>
				<a href='product_bsgoods.php?orderby=pname&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "pname" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='가나다역순'></a>
				<b class=small>등록일자</b>
				<a href='product_bsgoods.php?orderby=r.regdate&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".((($orderby == "r.regdate" && $ordertype ==  "desc") || ($orderby == "" && $ordertype ==  "")) ? "on":"off").".gif' border=0 align=absmiddle title='최근등록순'></a>
				<a href='product_bsgoods.php?orderby=r.regdate&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "r.regdate" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='등록순'></a>
				<b class=small>정렬순</b>
				<a href='product_bsgoods.php?orderby=vieworder2&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "vieworder2" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='역순'></a>
				<a href='product_bsgoods.php?orderby=vieworder2&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "vieworder2" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='순'></a>
				</td>
				</tr>";
}
$innerview .= "
			</table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='3%'>
				<col width='*'>
                <col width='22%'>
				<col width='8%'> 
				<col width='8%'>
				<col width='7%'>
				<col width='7%'>
				<col width='12%'>
				<col width='7%'>
				<tr bgcolor='#cccccc' align=center height=40>
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>".OrderByLink("상품명", "pname", $ordertype)."</td>
                    <td class=m_td>연동기록</td>
					<td class=m_td>판매상태<br>진열</td> 
					<td class=m_td>공급가</td>
					<td class=m_td>".OrderByLink("소비자가", "listprice", $ordertype)."</td>
					<td class=m_td>".OrderByLink("판매가격", "sellprice", $ordertype)."</td>
					<td class=m_td>".OrderByLink("등록일자", "regdate", $ordertype)." ".OrderByLink("수정일자", "editdate", $ordertype)."</td>
					<td class=e_td>관리</td>
				</tr>";

$script_time[query_start] = time();

if($orderby != "" && $ordertype != ""){
	$orderbyString = " order by $orderby $ordertype ";
}else{
	$orderbyString = " order by p.regdate_desc ";
}

$where = " and p.is_delete = '0' ";

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
	
	if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
		//다중검색 시작 2014-04-10 이학봉
		if($search_text != ""){
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n
				$search_array = explode("\n",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$where .= "and ( ";

				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$where .= ")";
			}else{
				$where .= " and ".$search_type." = '".trim($search_text)."'";
			}
		}

	}else{	//검색어 단일검색
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
	}

	if($sprice && $eprice){
		$where .= "and sellprice between $sprice and $eprice ";
	}
    if($company_id != ""){
		$where = $where."and p.admin = '".$company_id."' ";

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
	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$addWhere = "and admin ='".trim($company_id)."'";
		}else{
			unset($addWhere);
		}
		$sql = "SELECT  p.id, p.product_type, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name, p.bs_goods_url,
		p.company, p.pcode, p.coprice, p.listprice, icons,p.disp, p.editdate, p.reserve, p.reserve_rate,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.currency_ix
		FROM ".TBL_SHOP_PRODUCT." p USE INDEX (regdate_desc) right join  ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid and r.basic = 1 , ".TBL_COMMON_COMPANY_DETAIL." c
		where c.company_id = p.admin $addWhere $where $orderbyString LIMIT $start, $max";
		//echo $sql;
		$db->query($sql);
	}else{
		$sql = "SELECT  p.id, p.product_type, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name, p.bs_goods_url,
		p.company, p.pcode, p.coprice, p.listprice, icons,p.disp, p.editdate, p.reserve, p.reserve_rate,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2
		FROM ".TBL_SHOP_PRODUCT." p USE INDEX (regdate_desc) right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid and r.basic = 1, ".TBL_COMMON_COMPANY_DETAIL." c
		where c.company_id = p.admin and admin ='".$admininfo[company_id]."' $where $orderbyString LIMIT $start, $max";


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

			$sql = "SELECT p.id as id, p.product_type, p.pname, p.brand,p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name, p.bs_goods_url,
			p.company, p.pcode, p.coprice, p.listprice,  icons,p.disp, p.editdate, p.reserve, p.reserve_rate,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.currency_ix
			FROM ".TBL_SHOP_PRODUCT." p USE INDEX (regdate_desc), ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
			where c.company_id = p.admin and p.id = r.pid and r.basic = 1 $where $addWhere $orderbyString LIMIT $start, $max";
			//echo $sql;
			$db->query($sql);
		}else{
			$sql = "SELECT p.id as id ,p.product_type, p.brand, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name, p.bs_goods_url,
			p.company, p.pcode, p.coprice, p.listprice, icons, p.disp, p.editdate, p.reserve, p.reserve_rate,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.currency_ix
			FROM ".TBL_SHOP_PRODUCT." p USE INDEX (regdate_desc), ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
			where c.company_id = p.admin and p.id = r.pid and r.basic = 1 and admin ='".$admininfo[company_id]."' $where $orderbyString LIMIT $start, $max";


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
			$sql = "SELECT p.id as id, p.product_type, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,c.com_name, r.cid, p.search_keyword,state, p.brand, p.brand_name, p.bs_goods_url,
				p.company, p.pcode, p.coprice, p.listprice,  icons,p.disp, p.editdate,  p.reserve_rate,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.currency_ix
				FROM ".TBL_SHOP_PRODUCT." p USE INDEX (regdate_desc), ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
				where c.company_id = p.admin and p.id = r.pid and r.basic = 1 and r.cid = '".$cid2."' $where $orderbyString LIMIT $start, $max";

			//echo $sql;

			$db->query($sql);
		}else{
			$sql = "SELECT p.id as id, p.product_type, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,c.com_name,  r.cid, p.search_keyword,state, p.brand, p.brand_name, p.bs_goods_url,
				p.company, p.pcode, p.coprice, p.listprice, icons,p.disp, p.editdate,  p.reserve_rate,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.currency_ix
				FROM ".TBL_SHOP_PRODUCT." p USE INDEX (regdate_desc), ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
				where c.company_id = p.admin and p.id = r.pid and r.basic = 1 and r.cid = '".$cid2."' and admin ='".$admininfo[company_id]."' $where $orderbyString LIMIT $start, $max";

				//echo $sql;
				$db->query($sql);

				//echo "test".$db->total;

		}
	}
}
$script_time[query_end] = time();
if($admininfo[admin_id] == "forbiz"){
	//echo nl2br($sql);
}

if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=11 align=center> 등록된 제품이 없습니다.</td></tr>";

}else{
	
	//print_r($currencys);
	$goods_infos = $db->fetchall();
        
	for ($i = 0; $i < count($goods_infos); $i++)
	{
		//$db->fetch($i);
        
        //연동기록 가져오기 시작
		// 2016-11-24 CJmall 상품코드추가 chpark
        $sql = "select *,
				(select sellertool_value from sellertool_reponse where shop_value = pid and shop_key = 'pid' and site_code = 'cjmall') as result_pno_cjmall
				from sellertool_regist_relation where pid = '".$goods_infos[$i][id]."'  group by site_code order by regist_date DESC 
				";
        $db->query($sql);
        if($db->total){
            $regist_info = $db->fetchAll();
        }else{
            $regist_info = NULL;
        }
        //연동기록 가져오기 끝
        
		/*
		$sql = "select * from shop_product_buyingservice_priceinfo where pid = '".$goods_infos[$i][id]."' order by regdate desc limit 1 ";

		$db->query ($sql);

		if($db->total){
			$db->fetch();
			$buyservice_price_info = $db->dt;

		//	echo (float)$duty;
		}
		*/

		//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "s"))) {
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "s");
		//}else{
		//	$img_str = "../image/no_img.gif";
		//}

	$innerview .= "<tr bgcolor='#ffffff'>
						<td class='list_box_td list_bg_gray'>
						<input type=checkbox class=nonborder id='cpid' name=select_pid[] value='".$goods_infos[$i][id]."'><!--input type=hidden class=nonborder id='cpid' name=cpid[] value='".$goods_infos[$i][id]."'--></td>
						<td class='list_box_td point' align=left style='padding:5px;font-weight:normal'>
							<table cellpadding=2 cellspacing=0 width='100%' style='text-align:left;'>
							<tr>
								<td width=60 rowspan=2><img src='".$img_str."' width=50 height=50></td>
								<td width='*'><span style='color:gray;font-weight:normal' >".getCategoryPathByAdmin($goods_infos[$i][cid], 4)."</span></td>
							</tr>
							<tr>
								<td style='line-height:150%;'>";
		$innerview .= "<a href='../product/goods_input.php?id=".$goods_infos[$i][id]."&mode=$mode&nset=$nset&page=$page&cid2=$cid2&depth=$depth&company_id=$company_id&brand2=$brand2&max=$max&state2=$state2&disp=$disp&search_type=$search_type&search_text=$search_text' target='_blank'>  ".($goods_infos[$i][brand_name] ? "[".$goods_infos[$i][brand_name]."]":"")." ".$goods_infos[$i][pname]." (".$goods_infos[$i][pcode].")</a> <br> ".$goods_infos[$i][com_name]."<br>".$goods_infos[$i][id]." ";

		if($goods_infos[$i][product_type] == 1){
		$innerview .= "
					<br><a href='".$goods_infos[$i][bs_goods_url]."' class=small target=_blank><b class=blu><img src='../images/".$admininfo["language"]."/btn_buy_agency.gif' align=absmiddle style='padding:5px 0;'></b></a>";
		}

		$innerview .= "
								</td>
							</tr>

							<tr>
								<td nowrap>

								</td>
							</tr>
							</table>
					</td>";
     $innerview .= "<td class='list_box_td' style='text-align:left;padding-left:10px;'>";
     
     /**
      * 연동기록 마지막 하나만 표시하도록 수정
      */
	
     if(!empty($regist_info)){
        for($z=0; $z < count($regist_info); $z++){
			if($regist_info[$z][result_code] == "200"){
				$msg = "성공";
				$highlight = false;
			}else{
				$msg = "실패";
				$highlight = true;
			}
			if($regist_info[$z][update_date]){
			$reg_date = new DateTime($regist_info[$z][update_date]);
			}else{
			$reg_date = new DateTime($regist_info[$z][regist_date]);
			}
			if($highlight){
				$innerview .= "<font color=red title='".$regist_info[$z]["result_msg"]."'>".$regist_info[$z][site_code]." ".$msg."(".date_format($reg_date,"y-m-d H:i").")</font><br/>";
			}else{
				if($regist_info[$z][site_code] == 'cjmall'){ // 2016-11-24 CJmall 상품코드추가 chpark
					$innerview .= $regist_info[$z][site_code]."   - ".$msg." - <b>".$regist_info[$z][result_pno_cjmall]."</b> (".date_format($reg_date,"y-m-d H:i").")<br/>";
				}else{
					$innerview .= $regist_info[$z][site_code]."   - ".$msg." - <b>".$regist_info[$z][result_pno]."</b> (".date_format($reg_date,"y-m-d H:i").")<br/>";
				}
			}
        }
     }
	 
     
     $innerview .= "</td>
					<td class='list_box_td list_bg_gray' style='line-height:150%;'>";
						if($goods_infos[$i][state] == 1){
							$innerview .= "판매중";

						}else if($goods_infos[$i][state] == 6){
							$innerview .= "등록신청중";
						}else if($goods_infos[$i][state] == 7){
							$innerview .= "수정신청중";
						}else if($goods_infos[$i][state] == 0){
							$innerview .= "일시품절중";
						}

$innerview .= "<br>";

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
							<tr>
								<td>";
								if(checkMenuAuth(md5("/admin/sellertool/sellertool_goods_input.php"),"D") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
										$innerview .= "<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"deleteProduct('delete','".$goods_infos[$i][id]."','&cid=$cid&depth=$depth')\">";
								}else{
										$innerview .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle ></a>";
								}
								$innerview .= "
								<!--a href=\"javascript:CopyData(document.forms['listform'], '".$goods_infos[$i][id]."','".$goods_infos[$i][pname]."','".$admininfo[admin_level]."');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle title=\" ' ".strip_tags($goods_infos[$i][pname])." ' 에 대한 정보를 수정합니다.\"></a-->
								</td>
							</tr>
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
					
					<td height=30 align=left>";
if(checkMenuAuth(md5("/admin/sellertool/sellertool_goods_input.php"),"D") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$innerview .= "<a href=\"JavaScript:SelectDeleteBuyingServiceGooods(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a>";
}

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	$innerview .= "
				<a href='product_list.excel2.php?".$_SERVER["QUERY_STRING"]."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align=absmiddle ></a>";
}

$innerview .= "
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
				
				<tr height=30><td colspan=2 align=right></td></tr>
				</table>";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
			";


		$help_text = "
		<div style='z-index:-1;position:absolute;width:100%;text-align:center;' id='select_update_parent_save_loading'>
		<div style='width:100%;height:200px;display:block;position:relative;z-index:10px;text-align:center;padding-top:60px;' id='select_update_save_loading'></div>
		</div>
		
		<div id='batch_update_bs_goods_stock'>
		<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b> 마켓등록 설정</b></span></div>
			<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
			<col width='160px'>
			<col width='*'>
			<tr>
				<td class='input_box_title'> <b>제휴사 선택 </b></td>
				<td class='input_box_item' style='padding:5px;'>
				
				".getSellerToolSiteInfo($site_code," validation='true' title='제휴사' onChange=\"loadGoodsOption(this,'add_info',2)\" ")." <span class=small>제휴사를 선택하시면 등록된 부가정보 옵션이 노출됩니다.</span>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>등록옵션 선택 </b></td>
				<td class='input_box_item' style='padding:5px;'>
				".getSellerToolAddInfo($site_code)." 
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>작업타입 </b></td>
				<td class='input_box_item' style='padding:5px;'>
					<input type='radio' name='work_type' id='work_type_porduct' value='product' checked><label for='work_type_porduct'>상품등록/수정</label>
					<input type='radio' name='work_type' id='work_type_stock' value='stock'><label for='work_type_stock'>재고수정(현재인터파크전용)</label>
				</td>
			</tr>
			</table>
			<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                $help_text .= "            
				<tr><td height=50 colspan=4 align=center><input type=image id='btn_reg' src='../image/btn_bsgoods_update.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>";
            }else{
                $help_text .= "
                <tr><td height=50 colspan=4 align=center><a href=\"".$auth_write_msg."\"><img id='btn_reg' src='../image/btn_bsgoods_update.gif' border=0 style='cursor:pointer;border:0px;' ></a></td></tr>";                
            }
            $help_text .= "                                            
			</table>
		</div>
		";


		$select = "
		<select name='update_type' >
			<option value='2'>선택한 상품 전체</option>
			<option value='1'>검색한 상품 전체</option>
		</select>
        <span> 등록하기</span>";
		

		$Contents .= "".HelpBox($select, $help_text,'180')."</form>";


$Contents .= "
<!--a href=\"javascript:alert(document.frames['bs_search_frame'].location);\">url 정보 보기</a>
		<IFRAME id=bs_search_frame name=bs_search_frame src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME-->";
		if($_SERVER["HTTP_HOST"] == "dev.dcgworld.com" || $admininfo[admin_id] == "forbiz"){
		$Contents .= "<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=800 height=600 scrolling=no ></IFRAME>";
		}else{
		$Contents .= "<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>";
		}
		
		$Contents .= "
		<!--iframe name='act' src='' width=800 height=200 frameBorder=0 ></iframe-->";

//[{"bsi_ix":"62","exchange_type":"1","exchange_rate":"1170","bs_basic_air_shipping":"6","bs_add_air_shipping":"1.2","bs_duty":"8","bs_supertax_rate":"10","clearance_fee":"5500","usable_round":"Y","round_precision":"0","round_type":"round","disp":"1","regdate":"2010-02-10 06:58:55"}]
$Script = "
<script Language='JavaScript' src='sellertool.js'></script>
<script language='javascript' src='../include/DateSelect.js'></script>
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


	//다중검색어 시작 2014-04-10 이학봉

	$('input[name=mult_search_use]').click(function (){
		var value = $(this).attr('checked');

		if(value == 'checked'){
			$('#search_text_input_div').css('display','none');
			$('#search_text_area_div').css('display','');
			
			$('#search_text_area').attr('disabled',false);
			$('#search_texts').attr('disabled',true);
		}else{
			$('#search_text_input_div').css('display','');
			$('#search_text_area_div').css('display','none');

			$('#search_text_area').attr('disabled',true);
			$('#search_texts').attr('disabled',false);
		}
	});

	var mult_search_use = $('input[name=mult_search_use]:checked').val();
		
	if(mult_search_use == '1'){
		$('#search_text_input_div').css('display','none');
		$('#search_text_area_div').css('display','');

		$('#search_text_area').attr('disabled',false);
		$('#search_texts').attr('disabled',true);
	}else{
		$('#search_text_input_div').css('display','');
		$('#search_text_area_div').css('display','none');

		$('#search_text_area').attr('disabled',true);
		$('#search_texts').attr('disabled',false);
	}

	//다중검색어 끝 2014-04-10 이학봉

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
				alert(language_data['product_bsgoods.php']['C'][language]);
				//'등록카테고리가 선택되지 않았습니다. 등록카테고리 지정후 상품 가져오기를 실행해주세요'
				frm.cid0_1.focus();
				document.getElementById('save_loading').style.display = 'none';
					obj.innerHTML = \"\";

			}

			if(frm.bs_site.value.length < 1){
				alert(language_data['product_bsgoods.php']['A'][language]);
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
				alert(language_data['product_bsgoods.php']['B'][language]);
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
				alert(language_data['product_bsgoods.php']['C'][language]);
				//'등록카테고리가 선택되지 않았습니다. 등록카테고리 지정후 상품 가져오기를 실행해주세요'
				frm.cid0_1.focus();
				document.getElementById('save_loading').style.display = 'none';
					obj.innerHTML = \"\";

			}
			if(frm.bs_site.value.length < 1){
				alert(language_data['product_bsgoods.php']['A'][language]);
				//'구매대행 사이트를 지정해주세요'
				frm.bs_site.focus();
				document.getElementById('save_loading').style.display = 'none';
				obj.innerHTML = \"\";
				return false;
			}
			//alert(frm.list_url.value.length < 1);
			if(parseInt(frm.list_url.value.length) < 1){
				alert(language_data['product_bsgoods.php']['B'][language]);
				//'기본 URL 을 입력해주세요 (구매대행 사이트의 카테고리별 상품 리스트페이지 입니다)'
				frm.list_url.focus();
				document.getElementById('save_loading').style.display = 'none';
				obj.innerHTML = \"\";
				return false;
			}

			if(frm.list_url.value.indexOf(frm.bs_site.value) == -1){
				alert(language_data['product_bsgoods.php']['D'][language]);
				//'기본 URL 이 선택하신 구매대행 사이트와 맞는지 다시 한번 확인해주세요'
				frm.list_url.focus();
				document.getElementById('save_loading').style.display = 'none';
				obj.innerHTML = \"\";
				return false;
			}
			frm.submit();
		}
	}else{
		alert(language_data['product_bsgoods.php']['E'][language]);
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
        document.getElementById('btn_reg').disabled = true;
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

		obj.innerHTML = \"<img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 상품을 등록중입니다..\";

		document.getElementById('select_update_save_loading').appendChild(obj);

		document.getElementById('select_update_save_loading').style.display = 'block';
}

function select_update_unloading(){
    document.getElementById('btn_reg').disabled = false;
	parent.document.getElementById('select_update_parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('select_update_loadingbar').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').style.display = 'none';
}
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
</script>
";
///http://www.bodenusa.com/en-US/Baby-Trousers-Jeans/72075-KHK/Baby-Khaki-Anchors-Baby-Boarders.html
//Pretty Applique T-shirt
//bodenusa_71180-WHT

//$Contents .= HelpBox("구매대행 상품관리", $help_text);
$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
if($view == "innerview"){
	$pageging_info["product_bsgoods.php"]["page"] = $page;
	$pageging_info["product_bsgoods.php"]["nset"] = $nset;
	
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
	$Script .= "
	<script Language='JavaScript' src='../include/zoom.js'></script>
	<script Language='JavaScript' src='sellertool.js'></script>
	<script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}

	function loadGoodsOption(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//document.write('goods_addinfo.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
		//document.getElementById('act').src = 'goods_addinfo.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = 'goods_addinfo.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}


	
	/*
	function loadChangeCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
		//document.getElementById('act').src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	*/
	</script>";

	$P = new LayOut();
	$P->strLeftMenu = sellertool_menu();
	$P->OnloadFunction = "init();";
	$P->addScript = $Script;
	$P->Navigation = "제휴사연동 > 제휴사 상품연동";
	$P->title = "제휴사 상품연동";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}


$script_time[end] = time();
if($admininfo[admin_id] == "forbiz"){
	//print_r($script_time);
}
?>