<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");

include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] < 9){
	header("Location:/admin/seller/");
}

if($_GET["sdate"] && $_GET["edate"]){
	$sdate = $_GET["sdate"];
	$edate = $_GET["edate"];
}else{
	$sdate = date("Y-m-d");
	$edate = date("Y-m-d");
}

$shmop = new Shared("reserve_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$reserve_data = $shmop->getObjectForKey("reserve_rule");
$reserve_data = unserialize(urldecode($reserve_data));

//print_r($reserve_data);


if(!$update_kind){
	$update_kind = "display";
}

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
$db2 = new Database;
/*if($_SESSION["mode"] == "search"){
	$mode = "search";
}*/

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
		$where = "where p.id Is NOT NULL and p.id = r.pid and r.basic = 1 AND p.product_type NOT IN ('".implode("','",$sns_product_type)."')  ";
	}else{
		$where = "where p.id Is NOT NULL and p.id = r.pid and r.basic = 1 AND p.product_type NOT IN ('".implode("','",$sns_product_type)."') and admin ='".$admininfo[company_id]."'  ";
	}

	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}
	if($company_id != ""){
		$where = $where."and p.admin = '".$company_id."' ";

	}
	if($search_text != ""){
		$where = $where."and p.".$search_type." LIKE '%".trim($search_text)."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

//echo $where;
	if($state2 != ""){
		//session_register("state");
		$where = $where." and p.state = ".$state2." ";
	}
	if($brand != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand."";
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

	if($sdate != "" && $edate != ""){
		$where .= " and  date_format(p.regdate,'%Y-%m-%d') between  $sdate and $edate ";
	}

	$sql = "SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where  ";
	//echo $sql;
	$db2->query($sql);

}else{
	if ($cid2 == ""){
		if($admininfo[admin_level] == 9){
			$addWhere = "Where p.id = r.pid AND p.product_type NOT IN ('".implode("','",$sns_product_type)."')  ";
			if($company_id != ""){
				$addWhere .= " and admin ='".$company_id."'";
			}


			$db2->query("SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  $addWhere ");
		}else{
			$sql = "SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  where  p.id = r.pid AND p.product_type NOT IN ('".implode("','",$sns_product_type)."') and admin ='".$admininfo[company_id]." '";
			$db2->query($sql);
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
			$sql = "SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r  where p.id = r.pid AND p.product_type NOT IN ('".implode("','",$sns_product_type)."') and r.basic = 1 and r.cid LIKE '".substr($cid2,0,$cut_num)."%' ";

			$db2->query($sql);

		}else{
			$sql = "SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid AND p.product_type NOT IN ('".implode("','",$sns_product_type)."') and r.basic = 1 and r.cid LIKE '".substr($cid2,0,$cut_num)."%' and admin ='".$admininfo[company_id]."' ";

			$db2->query($sql);
		}

	}
}

$total = $db2->total;


$vdate = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()-84600);
$voneweekago = date("Ymd", time()-84600*7);
$vtwoweekago = date("Ymd", time()-84600*14);
$vfourweekago = date("Ymd", time()-84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));


if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&sprice=$sprice&eprice=$eprice&state2=$state2&disp=$disp&brand_name=$brand_name&cid2=$cid2&depth=$depth&company_id=$company_id&event=$event&best=$best&sale=$sale&wnew=$wnew&mnew=$mnew");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype");
	//echo $total.":::".$page."::::".$max."<br>";
}

$Contents =	"
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("수동주문", "상품관리 > 수동주문")."</td>
	</tr>

	";

$Contents .=	"
	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='sprice' value='0' />
	<input type='hidden' name='eprice' value='1000000' />
	<tr height=150>
		<td colspan=2 >
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:1px'>
						<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>
							<tr>
								<td class='search_box_title'><b>카테고리선택</b></td>
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
							if($admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
								$Contents .=	"
							<tr>
								<td class='search_box_title'><b>입점업체</b></td>
								<td class='search_box_item'>".CompanyList2($company_id,"")."</td>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item'>".BrandListSelect($brand, $cid)."</td>
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
								<td class='search_box_title'><b>판매상태</b></td>
								<td class='search_box_item'>
									<select name='state2' style='font-size:12px;'>
										<option value=''>상태값선택</option>
										<option value='1' ".ReturnStringAfterCompare($state2, "1", " selected").">판매중</option>
										<option value='0' ".ReturnStringAfterCompare($state2, "0", " selected").">일시품절</option>
										<option value='6' ".ReturnStringAfterCompare($state2, "6", " selected").">등록신청중</option>
										<option value='7' ".ReturnStringAfterCompare($state2, "7", " selected").">수정신청중</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>검색어</b></td>
								<td class='search_box_item'>
									<table cellpadding=0 cellspacing=0 >
										<tr >
											<td>
											<select name='search_type'  style=\"font-size:12px;height:20px;\">
												<option value='pname'>상품명</option>
												<option value='pcode'>상품코드</option>
												<option value='id'>상품코드(key)</option>
											</select>
											</td>
											<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox1' value='".$search_text."' onclick='findNames();'  clickbool='false' style='height:16px;FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
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
								<td class='search_box_title'><b>목록갯수</b></td>
								<td class='search_box_item'>
									<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
									<option value='5' ".CompareReturnValue(5,$max).">5</option>
									<option value='10' ".CompareReturnValue(10,$max).">10</option>
									<option value='20' ".CompareReturnValue(20,$max).">20</option>
									<option value='50' ".CompareReturnValue(50,$max).">50</option>
									<option value='100' ".CompareReturnValue(100,$max).">100</option>
									</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
								</td>
							</tr>
							<tr height=27>
								<td class='search_box_title'><b>등록일자</b></td>
								<td class='search_box_item' colspan=3 >
								".search_date('sdate','edate',$sdate,$edate)."
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
	</tr>";

$Contents .=	"
	<tr>
		<td valign=top style='padding-top:33px;'>";

$Contents .= "
		</td>
		<form name=listform method=post action='goods_batch.act.php' onsubmit='return SelectUpdate(this)'  target='iframe_act' ><!--onsubmit='return CheckDelete(this)' -->
		<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
		<input type='hidden' id='pid' value=''>
		<input type='hidden' name='act' value='update'>
		<input type='hidden' name='search_act_total' value='$total'>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "<ul class='total_cnt_area' >
					<li class='front'>상품수 : ".number_format($total)." 개</li>
					<li class='back'>".$str_page_bar."</li>
				  </ul>

			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='3%'>
				<!--col width='10%'-->
				<col width='60px'>
				<col width='*'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='10%'>
				<col width='10%'>
				<col width='10%'>
				<col width='7%'>
				<tr bgcolor='#cccccc' align=center height=30>
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<!--td class=m_td>상품코드</td>
					<td class=m_td>이미지</td-->
					<td class=m_td colspan=2>상품정보</td>
					<td class=m_td>판매상태</td>
					<td class=m_td>진열</td>
					<td class=m_td>공급가</td>
					<td class=m_td>적립금</td>
					<td class=m_td>소비자가</td>
					<td class=m_td>판매가</td>
					<td class=e_td>관리</td>
				</tr>";



if($orderby != "" && $ordertype != ""){
	$orderbyString = " order by $orderby $ordertype ";
}else{
	$orderbyString = " order by p.regdate desc ";
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
	$where = " AND p.product_type NOT IN ('".implode("','",$sns_product_type)."') ";
	if($search_text != ""){
		$where .= "and p.".$search_type." LIKE '%".trim($search_text)."%' ";
	}

	if($sprice && $eprice){
		$where .= "and sellprice between $sprice and $eprice ";
	}

	if($status_where){
		$where .= " and ($status_where) ";
	}
	if($brand != ""){
		$where .= " and brand = ".$brand."";
	}

	if($brand_name != ""){
		$where .= " and p.brand_name LIKE '%".$brand_name."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

	if($state2 != ""){
		$where .= " and state = ".$state2."";
	}


	if($cid2 != ""){
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}

	if($sdate != "" && $edate != ""){
		$where .= " and  date_format(p.regdate,'%Y%m%d') between  $sdate and $edate ";
	}
	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$addWhere = "and admin ='".$company_id."'";
		}else{
			unset($addWhere);
		}
		$sql = "SELECT distinct p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name,
		p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate, p.reserve, p.reserve_rate,p.reserve_yn,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
		where c.company_id = p.admin and p.id = r.pid and r.basic = 1 $addWhere $where
		$orderbyString
		LIMIT $start, $max";
//		echo nl2br($sql);
		$db->query($sql);
	}else{
		$sql = "SELECT distinct p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name,
		p.company, p.pcode, p.coprice, p.listprice,p.disp, p.editdate, p.reserve, p.reserve_rate,p.reserve_yn,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
		where c.company_id = p.admin and p.id = r.pid and r.basic = 1  and admin ='".$admininfo[company_id]."' $where
		$orderbyString
		LIMIT $start, $max";


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

			$sql = "SELECT distinct (p.id) as id, p.pname, p.brand,p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name,
			p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate, p.reserve, p.reserve_rate,p.reserve_yn,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
			where c.company_id = p.admin and p.id = r.pid AND p.product_type NOT IN ('".implode("','",$sns_product_type)."') and r.basic = 1 $where $addWhere $orderbyString LIMIT $start, $max";
			//echo $sql;
			$db->query($sql);
		}else{
			$sql = "SELECT distinct (p.id) as id ,p.brand, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name,
			p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate, p.reserve, p.reserve_rate,p.reserve_yn,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
			where c.company_id = p.admin and p.id = r.pid AND p.product_type NOT IN ('".implode("','",$sns_product_type)."') and r.basic = 1 and admin ='".$admininfo[company_id]."' $where $orderbyString LIMIT $start, $max";


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
			$sql = "SELECT distinct (p.id) as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,c.com_name, r.cid, p.search_keyword,state, p.brand, p.brand_name,
				p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate,  p.reserve_rate,p.reserve_yn,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
				where c.company_id = p.admin and p.id = r.pid AND p.product_type NOT IN ('".implode("','",$sns_product_type)."') and r.cid = '".$cid2."' $where $orderbyString LIMIT $start, $max";

		//	echo $sql;

			$db->query($sql);
		}else{
			$sql = "SELECT distinct (p.id) as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,c.com_name,  r.cid, p.search_keyword,state, p.brand, p.brand_name,
				p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate,  p.reserve_rate,p.reserve_yn,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
				where c.company_id = p.admin and p.id = r.pid AND p.product_type NOT IN ('".implode("','",$sns_product_type)."') and r.cid = '".$cid2."' and admin ='".$admininfo[company_id]."' $where $orderbyString LIMIT $start, $max";

				//echo $sql;
				$db->query($sql);

				//echo "test".$db->total;

		}
	}
}
if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=10 align=center> 등록된 상품이 없습니다. <!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."--></td></tr>";

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

	$innerview .= "<tr bgcolor='#ffffff'>
						<td bgcolor='#efefef' align=center><input type=checkbox class=nonborder id='cpid' name='select_pid[]' value='".$db->dt[id]."'></td>
						<!--td bgcolor='#ffffff' align=center nowrap>
						".$db->dt[pcode]."
						</td-->
						<td bgcolor='#ffffff' align=center style='padding:5px 5px' ><a href='goods_input.php?id=".$db->dt[id]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], $LargeImageSize)."'  ><img src='".$img_str."' width=50 height=50 style='border:1px solid #efefef'></a></td>
						<td bgcolor='#ffffff' align=left style='line-height:140%;'>
						".($db->dt[pcode] != "" ? "상품코드 : <b>".$db->dt[pcode]."</b><br>":"")." ".getCategoryPathByAdmin($db->dt[cid], 4)."<br>
						<a href='goods_input.php?id=".$db->dt[id]."&mode=$mode&nset=$nset&page=$page&cid2=$cid2&depth=$depth&company_id=$company_id&brand2=$brand2&max=$max&state2=$state2&disp=$disp&search_type=$search_type&search_text=".trim($search_text)."&onew=$onew&best=$best&sale=$sale&event=$event&wnew=$wnew&mnew=$mnew' target='_blank'><b> ".($db->dt[brand_name] ? "[".$db->dt[brand_name]."]":"")." ".$db->dt[pname]."</b></a>

					</td>
					<td bgcolor='#efefef' align=center class='small'>";
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
					<td align=center class='small'>";

						if($db->dt[disp] == 1){
							$innerview .= "진열함";
						}else if($db->dt[disp] == 0){
							$innerview .= "진열안함";
						}

$innerview .= "					</td>
					<td bgcolor='#efefef' align=center>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td bgcolor='#ffffff' align=center style='line-height:150%;'>";
if($db->dt[reserve_yn] == "Y"){
	$innerview .= "		<b>개별적용</b><br>";
}else{
	$innerview .= "		<b>전체정책</b><br>";
}
if ($db->dt[reserve_yn] == "Y"){
	$innerview .= "		".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[reserve])." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
}else{
		$innerview .= "		".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[sellprice]*$reserve_data[goods_reserve_rate] /100)." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
	}
$innerview .= "
					</td>
					<td bgcolor='#efefef' align=center nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[listprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td bgcolor='#ffffff' align=center nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td bgcolor='#efefef' align=center nowrap>
						<table>

							<!--tr>
								<td><a href=\"javascript:CopyData(document.forms['listform'], '".$db->dt[id]."','".$db->dt[pname]."','".$admininfo[admin_level]."');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle title=\" ' ".strip_tags($db->dt[pname])." ' 에 대한 정보를 수정합니다.  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."\"></a></td>
							</tr-->

							<tr>
								<td>";
                                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                                    $innerview .= "
                                    <a href='manual_order.cart.php?act=add&id=".$db->dt[id]."&pcount=1' ><img src='../images/".$admininfo["language"]."/bts_cart.gif' align=absmiddle border=0></a>";
                                }else{
                                    $innerview .= "
                                    <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/bts_cart.gif' align=absmiddle border=0></a>";
                                }
                                $innerview .= "
                                </td>
							</tr>
						</table>
					</td>

				</tr>";
	}
}
	$innerview .= "</table>
				<table width='100%'>
				<tr height=30>
					<td width=210>

					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
				<tr height=30><td colspan=2 align=right></td></tr>
				</table>

				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>
		<!--iframe id='act' src='' width=0 height=0></iframe-->
			";


$Script = "<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>
<script language='javascript'>
$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});



function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

function unloading(){

	parent.document.getElementById('parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('loadingbar').innerHTML ='';
	parent.document.getElementById('save_loading').innerHTML ='';
	parent.document.getElementById('save_loading').style.display = 'none';
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_display','batch_update_category','batch_update_reserve'); //,'batch_update_sms','batch_update_coupon'

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


//$Contents .= HelpBox("수동주문", $help_text);
$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid2, $depth);
	echo "
	<Script>
	//alert(document.body.innerHTML);
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	try{
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	}catch(e){}
	parent.document.search_form.cid2.value ='$cid2';
	parent.document.search_form.depth.value ='$depth';

	</Script>";
}else{
	$Script .= "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<!--script Language='JavaScript' src='product_input.js'></script--><!--2011.06.18 없는게 정상 주석처리후 확인필요-->
	<script Language='JavaScript' src='product_list.js'></script>
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script>
	<script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var depth = sel.getAttribute('depth');
		//alert(depth);
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//alert(1);
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	function loadChangeCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;
		var depth = sel.getAttribute('depth');

		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>";

	$P = new LayOut();
	$P->strLeftMenu = order_menu();
	$P->addScript = $Script;
	$P->Navigation = "상품관리 > 수동주문 > 상품리스트";
	$P->title = "상품리스트";
	$P->strContents = $Contents;
	$P->jquery_use = false;

	$P->PrintLayOut();
}
?>