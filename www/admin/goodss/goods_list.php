<?
//include("$DOCUMENT_ROOT/admin/class/admin.page.class");
include("$DOCUMENT_ROOT/admin/product/category.lib.php");
include("../class/layout.class");
include("./goodss.lib.php");
include("../logstory/class/sharedmemory.class");
$install_path = "../../include/";
include("SOAP/Client.php");
$shmop = new Shared("goodss_service_check");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey("N","goodss_service_check");


$goodss_service_check = $shmop->getObjectForKey("goodss_service_check");
//$goodss_service_check = unserialize(urldecode($goodss_service_check));
//echo "goodss_service_check:".$goodss_service_check;



$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
// server.php 의 namespace 와 일치해야함
$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

## 한글 인자의 경우 에러가 나므로 인코딩함.


$service_infos = (array)$soapclient->call("getUsableServiceInfo",$params = array("mall_ix"=> $admininfo[mall_ix]),	$options);
	//echo $co_goodsinfo;

$useable_service = (array)$service_infos[useable_service];
$userable_service_infos = (array)$service_infos[userable_service_infos];
$script_time[getUsableServiceInfo] = time();
//print_r($userable_service_info);
$shmop = new Shared("myservice_info");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey($useable_service,"useable_service");

$script_time[useable_service] = time();

if(count($useable_service) > 0){
		$paginginfo[start] = $start;
		$paginginfo[max] = $max;
		$paginginfo[page] = $page;

		//print_r($paginginfo);
		//exit;
	
		foreach($_GET as $key => $value){
			$search_rules[$key]= $value;//urlencode($value);
		}
		//print_r($search_rules);
		$co_goodsinfo = $soapclient->call("getCoGoodsByServer",$params = array("useable_service"=> $useable_service, "search_rules"=> $search_rules,  "paginginfo"=> $paginginfo),	$options);
		$script_time[getCoGoodsByServer] = time();
		//print_r($co_goodsinfo);
		//exit;
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

//print_r($_GET);
$db = new MySQL;
$db2 = new MySQL;
/*if($_SESSION["mode"] == "search"){
	$mode = "search";
}*/
/*
if($co_type == "co_goods"){
	$co_type_str .= " and p.co_goods = '1'"; // 공유 하기를 원하는 자사의 상품
}else if($co_type == "co_goods_local"){
	$co_type_str .= " and p.co_goods = '2'"; // 다른 입점업체 등록된 상품
}else{
	$co_type_str .= " and p.co_goods = '2'"; // 다른 입점업체 등록된 상품
}
*/
$co_type_str .= " and p.co_goods = '2'"; // 다른 입점업체 등록된 상품

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
		$where = "where p.id Is NOT NULL and p.id = r.pid  and r.basic = 1 and admin ='".$admininfo[company_id]."'  ";
	}else{
		$where = "where p.id Is NOT NULL and p.id = r.pid  and r.basic = 1 and admin ='".$admininfo[company_id]."'  ";
	}

	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}
	if($co_company_id != ""){
		//session_register("company_id");
		$where = $where."and p.co_company_id = '".$co_company_id."' ";

	}
	if($search_text != ""){
		//session_register("search_type");
		//session_register("search_text");
		$where = $where."and p.".$search_type." LIKE '%".$search_text."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

	if($co_type_str){
		$where .= $co_type_str ;
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
		$where .= " and brand_name LIKE '%".$brand_name."%' ";
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

		if($admininfo[admin_level] == 9){
			$addWhere = "Where p.id = r.pid  and r.basic = 1  $co_type_str ";
			if($company_id != ""){
				$addWhere .= " and admin ='".$company_id."'";
			}else{
				//$addWhere .= " and admin ='".$admininfo[company_id]."'";
			}


			$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  and r.basic = 1  $addWhere ";
		}else{
			$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  and r.basic = 1 where  admin ='".$admininfo[company_id]."' ";
		}
	
		$db2->query($sql);

//0000056658
}

//echo nl2br($sql);
$db2->fetch();
$total = $db2->dt[total];

if($before_update_kind){
	$update_kind = $before_update_kind;
}
//echo $_COOKIE["update_kind"];
if($_COOKIE["goodss_goods_list"]){
	$update_kind = $_COOKIE["goodss_goods_list"];
}else if(!$update_kind){
	$update_kind = "bs_goods_stock";
}


if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&sprice=$sprice&eprice=$eprice&state2=$state2&disp=$disp&brand_name=$brand_name&cid2=$cid2&depth=$depth&co_type=$co_type&co_company_id=$co_company_id");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype&co_type=$co_type");
	//echo $total.":::".$page."::::".$max."<br>";
}

$Contents =	"
<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
<input type='hidden' name='mode' value='search'>
<input type='hidden' name='act' value='update'>
<input type='hidden' name='cid2' value='$cid2'>
<input type='hidden' name='depth' value='$depth'>
<input type='hidden' name='co_type' value='$co_type' />
<input type='hidden' name='co_goods' value='' />
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("공유상품관리", "상점관리 > 공유 상품일괄 관리")."</td>
	</tr>";
if(false){
$Contents .=	"
	<tr>
	    <td align='left' colspan=8 style='padding-bottom:10px;'> ".getHostServer($chs_ix)."</td>
	</tr>";
}
$Contents .=	"
	<tr>
	    <td align='left' colspan=2 style='padding-bottom:15px;'>
	    <div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>";
			if($co_type == "" || $co_type == "co_goods"  || $co_type == "co_goods_server_mylist" ){
			$Contents .=	"
						<!--table id='tab_01'  ".($co_type == "" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='goods_list.php?co_type=&chs_ix=".$_GET["chs_ix"]."'\">상품목록</td>
							<th class='box_03'></th>
						</tr>
						</table-->
						<table id='tab_02' class='on'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='goods_list.php?co_type=co_goods&chs_ix=".$_GET["chs_ix"]."'\">가져온 도매상품</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_04' ".($co_type == "co_goods_server_mylist" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='server_goods_list.php?co_type=co_goods_server_mylist&chs_ix=".$chs_ix."'\">도매상품 목록</td>
							<th class='box_03'></th>
						</tr>
						</table>";
			}else{
			$Contents .=	"
						<table id='tab_03' ".($co_type == "co_goods_local" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='goods_list.php?co_type=co_goods_local&chs_ix=".$_GET["chs_ix"]."'\">공유된 상품목록</td>
							<th class='box_03'></th>
						</tr>
						</table>

						<table id='tab_05' ".($co_type == "co_goods_server" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='server_goods_list.php?co_type=co_goods_server&chs_ix=".$_GET["chs_ix"]."'\">서버 판매공유 상품목록</td>
							<th class='box_03'></th>
						</tr>
						</table>";
			}
			$Contents .=	"
					</td>
					<td style='vertical-align:bottom;padding:0px 0px 10px 4px;'>";
if($co_type == "co_goods"){
	$Contents .= " <!--공유하기를 원하는 상품입니다. 공슈상품 클릭시 서버에 공유하실수 있습니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." "; // 공유 하기를 원하는 자사의 상품
}else if($co_type == "co_goods_local"){
	$Contents .= " <!--귀사의 쇼핑몰에 등록된 입점업체의 공유 상품입니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')." ";
}
$Contents .= "
					</td>
				</tr>
				</table>
			</div>
	    </td>
	</tr>
	<tr>
		<td colspan=5 style='vertical-align:bottom;padding:0px;'>";

$Contents .="
		</td>
	</tr>
	
	<tr>
		<td colspan=2>
		
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:3'>
						<table cellpadding=0 cellspacing=0 width=100% class='search_table_box'>
							<col width='15%' />
							<col width='35%' />
							<col width='15%' />
							<col width='35%' />
							<tr>
								<td class='search_box_title'>  선택된 카테고리  </td>
								<td class='search_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":getCategoryPathByAdmin($cid2, $depth)."(".$total."개) <b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
							</tr>
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
							if($admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
								$Contents .=	"
							<tr>
								<td class='search_box_title'>도매업체</td>
								<td class='search_box_item'>";
								$Contents .=	"<select name='co_company_id'>";
								$Contents .=	"<option value=''>등록한 도매업체</option>";
								for($i=0;$i < count($userable_service_infos);$i++){
									$userable_service_info = (array)$userable_service_infos[$i];
									$Contents .=	"<option value='".$userable_service_info["service_code"]."'  ".($userable_service_info["service_code"] == $company_id ? "selected":"").">".$userable_service_info["com_name"]."</option>";
								}
								$Contents .=	"</select>
								</td>
								<td class='search_box_title'>브랜드</td>
								<td class='search_box_item'><input type='text' name='brand_name' class=textbox value='$brand_name'></td>
							</tr>
							";
							}//".BrandListSelect4("","")."
								$Contents .=	"
							<tr>
								<td class='search_box_title'>진열</td>
								<td class='search_box_item'>
								<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
								<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
								<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
								</td>
								<td class='search_box_title'>판매및 상태값</td>
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
								<td class='search_box_title'>  검색어  </td>
								<td class='search_box_item' align=left valign='top' style='padding-right:5px;padding-top:1px;'>
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
								<td class='search_box_title'>목록갯수</td>
								<td class='search_box_item'><select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
								<option value='5' ".CompareReturnValue(5,$max).">5</option>
								<option value='10' ".CompareReturnValue(10,$max).">10</option>
								<option value='20' ".CompareReturnValue(20,$max).">20</option>
								<option value='50' ".CompareReturnValue(50,$max).">50</option>
								<option value='100' ".CompareReturnValue(100,$max).">100</option>
								</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
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
	<tr height=20>
		<td colspan=2 align=center style='padding-top:20px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
		
	</tr>
</table>
</form>
<form name=listform method=post action='./goods_list.act.php' onsubmit='return GoodssSelectUpdate(this)' target='iframe_act'><!--onsubmit='return CheckDelete(this)' target='iframe_act'-->
<input type='hidden' name='act' value='stock_update'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<input type='hidden' id='pid' value=''>
<table cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td valign=top >";

$Contents .= "

		</td>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
				<tr height=30>
					<td height=30 align=left>";
if(checkMenuAuth(md5("/admin/product/goods_input.php"),"D") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$innerview .= "<a href=\"JavaScript:SelectDeleteWholeSaleGooods(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a>";
}
$innerview .= "
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
			</table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box' >
				<col width='3%'>
				<col width='*'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='15%'>
				<col width='7%'>
				<tr bgcolor='#cccccc' align=center >
					<td class=s_td style='padding:5px 0px;'><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>제품정보</td>
					<td class=m_td>공유여부</td>
					<td class=m_td>판매상태</td>
					<td class=m_td>진열</td>
					<td class=m_td>공급가</td>
					<td class=m_td>정가</td>
					<td class=m_td>판매가</td>
					<td class=m_td>도매상품 등록일자</td>
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
		$where .= "and p.".$search_type." LIKE '%".$search_text."%' ";
	}

	if($co_company_id != ""){
		//session_register("company_id");
		$where = $where."and p.co_company_id = '".$co_company_id."' ";

	}

	if($status_where){
		$where .= " and ($status_where) ";
	}
	if($brand2 != ""){
		$where .= " and brand = ".$brand2."";
	}

	if($brand_name != ""){
		$where .= " and brand_name LIKE '%".$brand_name."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

	if($co_type_str){
		$where .= $co_type_str ;
	}

	if($state2 != ""){
		$where .= " and state = ".$state2."";
	}


	if($cid2 != ""){
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}
	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$addWhere = "and admin ='".$company_id."'";
		}else{
			unset($addWhere);
		}
		//echo $company_id;
		$sql = "SELECT p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,p.co_pid, c.com_name,  r.cid,  p.search_keyword,state,
		p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, p.co_goods, p.etc2, p.etc10
		FROM ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  and r.basic = 1 , ".TBL_COMMON_COMPANY_DETAIL." c
		where c.company_id = p.admin and p.admin is not null $addWhere $where $co_type_str $orderbyString LIMIT $start, $max";
		//echo $sql;
		$db->query($sql);
	}else{
		$sql = "SELECT p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder, p.co_pid, c.com_name,  r.cid,  p.search_keyword,state,
		p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2, p.co_goods, p.etc2, p.etc10
		FROM ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  and r.basic = 1 , ".TBL_COMMON_COMPANY_DETAIL." c
		where c.company_id = p.admin and admin ='".$admininfo[company_id]."' $where $co_type_str $orderbyString LIMIT $start, $max";


		$db->query($sql);
	}
	//echo $sql;
}else{

	if ($cid2 == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";
			}else{
				$addWhere = " and admin ='".$admininfo[company_id]."' "; // 상품공유의 경우는 자기 자신의 상품만 공유 할수 있다.
				$addWhere = "";
			}
		}else{
			$addWhere = "and admin ='".$admininfo[company_id]."'";
			$addWhere = "";
		}

		$sql = "SELECT p.id as id ,p.brand, p.pname, p.sellprice, p.regdate,p.vieworder, p.co_pid, c.com_name,  r.cid, p.search_keyword,state,
		p.company, p.pcode, p.coprice, p.listprice,   p.disp, p.editdate, p.reserve, p.reserve_rate,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2, p.co_goods, p.co_pid, p.etc2, p.etc10
		FROM ".TBL_SHOP_PRODUCT." p  left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  and r.basic = 1 ,  ".TBL_COMMON_COMPANY_DETAIL." c
		where c.company_id = p.admin and p.admin is not null
		$where
		$addWhere
		$co_type_str
		$orderbyString
		LIMIT $start, $max";
		// r.cid,
		//right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid  and r.basic = 1

		//echo nl2br($sql);
		$db->query($sql);


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
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";
			}else{
				unset($addWhere);
			}
		}else{
			$addWhere = "and admin ='".$admininfo[company_id]."'";
		}

		$sql = "SELECT p.id as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder, p.co_pid, r.cid, p.search_keyword,state, p.brand,
			p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate,  p.reserve_rate, p.co_pid, 
			case when vieworder = 0 then 100000 else vieworder end as vieworder2, p.co_goods, p.etc2, p.etc10
			FROM ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid and r.basic = 1
			and r.cid = '".$cid2."'  , ".TBL_COMMON_COMPANY_DETAIL." c
			where c.company_id = p.admin and r.cid = '".$cid2."' $addWhere $where $co_type_str $orderbyString LIMIT $start, $max";

			//echo $sql;
			$db->query($sql);
	}
}

//echo nl2br($sql);

if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td bgcolor='#efefef' align=center></td><td colspan=9 align=center> 등록된 제품이 없습니다.</td></tr>";

}else{

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		
		//if(file_exists(PrintImage("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/product", $db->dt[id], "m"))){
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "m");
		//}else{
		//	$img_str = "../image/no_img.gif";
		//}

	$innerview .= "<tr bgcolor='#ffffff'>
						<td class='list_box_td list_bg_gray' align=center title='".$db->dt[id]."' alt='".$db->dt[id]."'>
						<input type=checkbox class=nonborder id='goodss_pid' name='goodss_pid[]' value='".$db->dt[id]."' >
						</td>
						<td class='list_box_td point' style='text-align:left;padding:5px 5px;'>
						<table cellpadding=1 cellspacing=0 width='100%'>
							<tr>
								<td width=60 rowspan=5><img src='".$img_str."' width=50 height=50 style='border:1px solid silver;'></td>
								<td><span style='color:gray' class='small'>".getCategoryPathByAdmin($db->dt[cid], 4)."</span></td>
							</tr>
							<tr>
								<td>";
		$innerview .= "<a href='../product/goods_input.php?id=".$db->dt[id]."&mode=$mode&nset=$nset&page=$page&cid2=$cid2&depth=$depth&company_id=$company_id&brand2=$brand2&max=$max&state2=$state2&disp=$disp&search_type=$search_type&search_text=$search_text' target='_blank'>
										<b> ".($db->dt[brand_name] ? "[".$db->dt[brand_name]."]":"")." ".$db->dt[pname]." ".($db->dt[pcode] ? "(".$db->dt[pcode].")":"")."</b>
										</a><br> [".$db->dt[co_pid]."]

								</td>
							</tr>

							<tr>
								<td nowrap>

								</td>
							</tr>
						</table>
					</td>
					<td class='list_box_td list_bg_gray' align=center class='small'>";

						if($db->dt[co_goods] == 1){
							$innerview .= "<b style='color:red;'>공유상품</b>";
						}else if($db->dt[co_goods] == 2){
							$innerview .= "<b>도매상품</b>";
						}else if($db->dt[co_goods] == 0){
							$innerview .= "-";
						}

$innerview .= "
					</td>
					<td class='list_box_td' align=center class='small'>";
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
					<td class='list_box_td list_bg_gray' align=center class='small'>";

						if($db->dt[disp] == 1){
							$innerview .= "진열함";
						}else if($db->dt[disp] == 0){
							$innerview .= "진열안함";
						}

$innerview .= "					</td>
					<td class='list_box_td' align=center>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td list_bg_gray' align=center nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[listprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td' align=center nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td' align=center nowrap>
					등록일자 :".$db->dt[etc10]."<br />
					최종수정일자 : ".$db->dt[etc2]."
					</td>
					<td class='list_box_td list_bg_gray'  align=center nowrap>
						<table align=center>

							<!--tr>
								<td><a href=\"javascript:CopyData(document.forms['listform'], '".$db->dt[id]."','".$db->dt[pname]."','".$admininfo[admin_level]."');\"><img src='../images/".$admininfo["language"]."/btn_modify.gif' border=0 align=absmiddle title=\" ' ".strip_tags($db->dt[pname])." ' 에 대한 정보를 수정합니다.\"></a></td>
							</tr-->

							<tr>
								<td><a href='/shop/goods_view.php?cid=".$db->dt[cid]."&id=".$db->dt[id]."&depth=3&b_ix=".$db->dt[brand]."' target='_blank'><img src='../images/".$admininfo["language"]."/btn_preview.gif'></a></td>
							</tr>
						</table>
					</td>

				</tr>
				";
	}
}
$innerview .= "</table>";
$innerview .= "<table width=100%>
				<tr height=20>
					<td height=30 align=left>";
if(checkMenuAuth(md5("/admin/product/goods_input.php"),"D") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$innerview .= "<a href=\"JavaScript:SelectDeleteWholeSaleGooods(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a>";
}
$innerview .= "
					</td>
					<td colspan=2 align=right>".$str_page_bar."</td></tr>
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
		<div id='batch_update_display' ".($update_kind == "display" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
		<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b>판매/진열 상태 변경</b> <span class=small style='color:gray'>변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요 </span></div>
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
		<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b>도매 상품 정보/재고확인</b> <span class=small style='color:gray'>상품/재고 확인을 하고자 하는 상품을 검색/선택 후 재고확인 버튼을 클릭해주세요.</span></div>
			<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
			<col width='160px'>
			<col width='*'>
			<tr>
				<td class='input_box_title'> <b>처리방법 </b></td>
				<td class='input_box_item' style='padding:5px;'>
				<input type='radio' name='sc_state' id='sc_state_0' value='0' checked><label for='sc_state_0'><b>품절/판매불가</b>로 확인된 상품을 <b>일시품절</b>로 처리합니다.</label><br>
				<input type='radio' name='sc_state' id='sc_state_9' value='9' ><label for='sc_state_9'><b>품절/판매불가</b>로 확인된 상품을 <b>삭제</b>로 처리합니다.(상품 이미지 정보 및 관련 모든 정보가 삭제됩니다.)</label>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>진열상태 </b></td>
				<td class='input_box_item' style='padding:5px;'>
				<input type='radio' name='sc_disp' id='sc_disp_0' value='0' checked><label for='sc_disp_0'><b>품절/판매불가</b>로 확인된 상품을 <b>노출안함</b>로 처리합니다</label><br>
				<input type='radio' name='sc_disp' id='sc_disp_1' value='1' ><label for='sc_disp_1'><b>품절/판매불가</b>로 확인된 상품을 <b>노출함</b>로 처리합니다.</label>
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
		</select>";
		if(false){
		$select .= "
		<input type='radio' name='update_kind' id='update_kind_display' value='display' ".($update_kind == "display" ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_display');\"><label for='update_kind_display'>판매/진열 상태 일괄 변경<!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K')."--></label>
		<input type='radio' name='update_kind' id='update_kind_category' value='category' ".CompareReturnValue("category",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_category');\"><label for='update_kind_category'>상품 카테고리 변경<!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'L')."--></label>";
		}
		$select .= "
		<input type='radio' name='update_kind' id='update_kind_stock' value='bs_goods_stock' ".CompareReturnValue("bs_goods_stock",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_bs_goods_stock');\"><label for='update_kind_stock'>도매 상품 정보/재고 업데이트</label>
		";

		$Contents .= "".HelpBox($select, $help_text,'650')."</form>";



//$Contents .= HelpBox("공유 상품일괄 관리", $help_text);
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
	<script Language='JavaScript' src='../product/product_list.js'></script>\n
	<script Language='JavaScript' src='goodss_list.js'></script>\n";

	$P = new LayOut();
	$P->strLeftMenu = goodss_menu();
	$P->addScript = $Script;
	if($co_type == "co_goods_local"){
	$P->Navigation = "공유상품관리 > 공유상품가져오기";
	$P->title = "공유상품가져오기";
	}else{
	$P->Navigation = "상품리스트 > 가져온 도매상품";
	$P->title = "가져온 도매상품";
	}
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}
?>