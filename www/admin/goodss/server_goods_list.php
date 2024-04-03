<?
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include("./goodss.lib.php");
$install_path = "../../include/";
include("SOAP/Client.php");
include("../logstory/class/sharedmemory.class");
//print_r($admininfo);
$script_time[start] = time();
//$imagedata1 = process_using_gd_or_something($imagedata1);
//echo $imagedata1;
//echo "<img src={$imagedata1} >";

$shmop = new Shared("goodss_service_check");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey("N","goodss_service_check");


$goodss_service_check = $shmop->getObjectForKey("goodss_service_check");
//$goodss_service_check = unserialize(urldecode($goodss_service_check));
//echo "goodss_service_check:".$goodss_service_check;

$script_time[goodss_service_check] = time();



if($max == ""){
	$max = 10; //페이지당 갯수
}else{
	$max = $max;
}

/*
if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}
*/

if ($page == ''){
	if($pageging_info["server_goods_list.php"]["page"] != ""){
		$page  = $pageging_info["server_goods_list.php"]["page"];
		$start = ($page - 1) * $max;
	}else{
		$page  = 1;
		$start = 0;
	}
	if($pageging_info["server_goods_list.php"]["nset"] != ""){
		$nset  = $pageging_info["server_goods_list.php"]["nset"];
	}else{
		$nset  = 1;
	}
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;

//echo $hostserver;

		//$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ");

		//$db->fetch();
		//$hostserver = $db->dt[hostserver];
		//echo $hostserver;
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

if(round($total/$max)  <= $pageging_info["server_goods_list.php"]["page"]){
	unset($pageging_info);
	session_unregister("pageging_info");
	$page = 1;

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
	//echo $pageging_info["product_bsgoods.php"]["page"];
	//exit;
}


//echo $page.":::".$total."::".$max;
if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&sprice=$sprice&eprice=$eprice&state2=$state2&disp=$disp&company_id=$company_id&goodss_cid=$goodss_cid&goodss_depth=$goodss_depth");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype");
	//echo $total.":::".$page."::::".$max."<br>";
}

$Contents =	"
<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);'  >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='goodss_cid' value='$goodss_cid'>
	<input type='hidden' name='goodss_depth' value='$goodss_depth'>
	<input type='hidden' name='co_type' value='$co_type' />
	<input type='hidden' name='co_goods' value='' />
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4 > ".GetTitleNavigation("공유상품관리", "상점관리 > 공유상품관리")."</td>
	</tr>";
$script_time[html_print_start] = time();
$Contents .=	"
	<tr>
	    <td align='left' colspan=2 style='padding-bottom:15px;'>
	    <div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>";

			$Contents .=	"
						<!--table id='tab_01' >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='goods_list.php?co_type=&chs_ix=".$_GET["chs_ix"]."'\">상품목록</td>
							<th class='box_03'></th>
						</tr>
						</table-->
						<table id='tab_02' >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='goods_list.php?co_type=co_goods&chs_ix=".$_GET["chs_ix"]."'\">가져온 도매상품</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_04' class='on'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='server_goods_list.php?co_type=co_goods_server_mylist&chs_ix=".$chs_ix."'\">도매상품 목록</td>
							<th class='box_03'></th>
						</tr>
						</table>";

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

							<!--tr height='30'>
								<td class='search_box_title'>  <b>선택된 카테고리</b>  </td>
								<td class='search_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($goodss_cid, $goodss_depth)."(".$total."개)":getCategoryPathByAdmin($goodss_cid, $goodss_depth)."(".$total."개) <b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
							</tr-->
							<tr>
								<td class='search_box_title' width='150'><b>카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'>".getGoodssCategoryList("대분류", "cid0_1", "onChange=\"loadGoodsCategory(this,'cid1_1',2)\" validation=false title='대분류' ", 0, $goodss_cid)."</td>
											<td style='padding-right:5px;'>".getGoodssCategoryList("중분류", "cid1_1", "onChange=\"loadGoodsCategory(this,'goodss_cid_1',2)\" validation=false title='중분류'", 1, $goodss_cid)."</td>
											<td style='padding-right:5px;'>".getGoodssCategoryList("소분류", "goodss_cid_1", "onChange=\"loadGoodsCategory(this,'cid3_1',2)\" validation=false title='소분류'", 2, $goodss_cid)."</td>
											<td>".getGoodssCategoryList("세분류", "cid3_1", "onChange=\"loadGoodsCategory(this,'goodss_cid',2)\" title='세분류'", 3, $goodss_cid)."</td>
										</tr>
									</table>
								</td>
							</tr>
							";
							$script_time[getGoodssCategoryList_end] = time();
							$Contents .=	"
							<tr>
								<td class='search_box_title'><b>도매업체</b></td>
								<td class='search_box_item'>";
								$Contents .=	"<select name='company_id'>";
								$Contents .=	"<option value=''>등록한 도매업체</option>";
								for($i=0;$i < count($userable_service_infos);$i++){
									$userable_service_info = (array)$userable_service_infos[$i];
									$Contents .=	"<option value='".$userable_service_info["service_code"]."'  ".($userable_service_info["service_code"] == $company_id ? "selected":"").">".$userable_service_info["com_name"]."</option>";
								}
								$Contents .=	"</select>
								</td>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item'><input type='text' name='brand_name' class='textbox' value='$brand_name'></td>
							</tr>";


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
											<td>
											<select name='search_type'  style=\"font-size:12px;height:22px;\">
											<option value='' >검색항목</option>
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
								<td class='search_box_item'><select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&goodss_depth=$goodss_depth&view=innerview&max='+this.value\"-->
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

	</tr>
</table>
</form>

<form name=listform method=post action='server_goods_list.act.php' onsubmit='return getGoodssProduct(this)' target='iframe_act'><!--onsubmit='return CheckDelete(this)' target='iframe_act'-->
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<input type='hidden' name='act' value='b2b_goods_regs'>
<input type='hidden' id='goodss_pid' value=''>
<input type='hidden' name='chs_ix'  id='chs_ix' value='".$chs_ix."'>
<table cellpadding=0 cellspacing=0 width='100%'>
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
				<col width='9%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='15%'>

				<tr bgcolor='#cccccc' align=center>
					<td class=s_td height='30'><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>제품정보</td>
					<td class=m_td>도매처</td>
					<td class=m_td>판매상태</td>
					<td class=m_td>진열</td>
					<td class=m_td>공급가</td>
					<td class=m_td>정가</td>
					<td class=e_td>판매가</td>
					<td class=e_td>도매상품 등록일자</td>
					<!--td class=e_td>관리</td-->
				</tr>";



if(count($co_goods) == 0){
	if($mode == "search"){
		$innerview .= "<tr bgcolor=#ffffff height=100><td bgcolor='#efefef' align=center></td><td colspan=8 align=center> 검색결과가 존재하지 않습니다.</td></tr>";
	}else{
		$innerview .= "<tr bgcolor=#ffffff height=100><td bgcolor='#efefef' align=center></td><td colspan=8 align=center> <b>자동연동 서비스</b> 신청된 도매업체 상품만 노출되게 됩니다. 서비스 신청을 원하시면 <a href='/admin/goodss/b2b_company.php'><u>도매업체 리스트</u></a>로 이동해서 신청해주세요 .</td></tr>";
	}

}else{
	//echo count($co_goods);
	for ($i = 0; $i < count($co_goods); $i++)
	{
		//print_r($co_goods[$i]);
		$co_goods[$i] = (array)$co_goods[$i];
		if($co_goods[$i][bimg]){
			$img_str = $co_goods[$i][bimg];
		}else{
			$img_str = "../image/no_img.gif";
		}

	$innerview .= "<tr bgcolor='#ffffff'>
						<td class='list_box_td list_bg_gray' align=center title='".$co_goods[$i][admin]."-".$co_goods[$i][pid]."'>
							<input type=checkbox class=nonborder id='goodss_pid' name=goodss_pid[] value='".$co_goods[$i][pid]."' ".($co_goods[$i][admin] == $admininfo[company_id] ? "disabled title='자신의 상품은 복사 하실 수 없습니다.' ":"")." >
							<!--input type=hidden class=nonborder id='cpid' name=cpid[] value='".$co_goods[$i][pid]."'-->
						</td>
						<td class='list_box_td point' align=center >
							<table cellpadding=1 cellspacing=0 width='100%' style='margin:10px 0px;'>
							<tr>
								<td width=60 rowspan=5><a href='#' class='screenshot'  rel='".$img_str."' ><img src='".$img_str."' width=50 height=50 style='border:1px solid silver;'></a></td>
								<td align='left'><span style='color:gray' class='small'>".$co_goods[$i][category_text]."</span></td>
							</tr>
							<tr>
								<td align='left' style='line-height:140%;padding:4px 4px;'>";
		$innerview .= "<!--a href='../product/goods_input.php?id=".$co_goods[$i][pid]."&mode=$mode&nset=$nset&page=$page&goodss_cid=$goodss_cid&goodss_depth=$goodss_depth&company_id=$company_id&brand2=$brand2&max=$max&state2=$state2&disp=$disp&search_type=$search_type&search_text=$search_text' target='_blank'-->
										<b> ".($co_goods[$i][brand_name] ? "[".$co_goods[$i][brand_name]."]":"")." ".$co_goods[$i][pname]."</b>

										<!--/a--><br>
										(".$co_goods[$i][pcode].")
								</td>
							</tr>

							<tr>
								<td nowrap>

								</td>
							</tr>
							</table>
					</td>
					<td class='list_box_td list_bg_gray' align=center class='small'>".$co_goods[$i][com_name]."</td>
					<td class='list_box_td'  align=center class='small'>";
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
					<td class='list_box_td list_bg_gray' align=center class='small'>";

						if($co_goods[$i][disp] == 1){
							$innerview .= "진열함";
						}else if($co_goods[$i][disp] == 0){
							$innerview .= "진열안함";
						}

$innerview .= "					</td>
					<td class='list_box_td'  align=center>
					".number_format($co_goods[$i][coprice])." 원
					</td>
					<td class='list_box_td list_bg_gray' align=center nowrap>
					".number_format($co_goods[$i][listprice])." 원
					</td>
					<td class='list_box_td' align=center nowrap>
					".number_format($co_goods[$i][sellprice])." 원
					</td>
					<td class='list_box_td' align=center nowrap>
					등록일자 :".$co_goods[$i][etc10]."<br />
					최종수정일자 : ".$co_goods[$i][etc2]."
					</td>
					<!--td class='list_box_td list_bg_gray' align=center style='display:none;' nowrap>
						<table align=center>
							<tr>
								<td><a href='/shop/goods_view.php?cid=".$co_goods[$i][cid]."&id=".$co_goods[$i][pid]."&goodss_depth=3&b_ix=".$co_goods[$i][brand]."' target='_blank'><img src='../images/".$admininfo["language"]."/btn_preview.gif'></a></td>
							</tr>
						</table>
					</td-->

				</tr>

				";
	}
}
$innerview .= "</table>";
$innerview .= "<table width=100%>";
$innerview .= "<tr><td colspan=9 align=right>".$str_page_bar."</td></tr>";
$innerview .= "</table>";
/*
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
							<input type='radio' name='apply_data' id='apply_data_2' value='2'><label for='apply_data_2'><b>검색 상품</b> > <b>".($search_text == "" ? getCategoryPathByAdmin($goodss_cid, $goodss_depth)."(".$total."개)":getCategoryPathByAdmin($goodss_cid, $goodss_depth)."(".$total."개) <b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></label> 을(를)
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




				";
*/
$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>


			";


		$help_text = "
		<div style='z-index:-1;position:absolute;width:100%;text-align:center;' id='select_update_parent_save_loading'>
		<div style='width:100%;height:200px;display:block;position:relative;z-index:10px;text-align:center;padding-top:60px;' id='select_update_save_loading'></div>
		</div>

		<div id='batch_update_b2b_goods_reg' ".($update_kind == "" || $update_kind == "b2b_goods_reg" ? "style='display:block'":"style='display:none'")." >
		<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b class=blk>도매상품 등록하기 </b> <span class=small style='color:gray'>상품/재고 확인을 하고자 하는 상품을 검색/선택 후 재고확인 버튼을 클릭해주세요.  </span></div>
			<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
			<col width='16%'>
			<col width='34%'>
			<col width='16%'>
			<col width='34%'>
			<tr>
				<td class='input_box_title'> <b>품절상태 </b></td>
				<td class='input_box_item' >
				<input type='radio' name='sc_state' id='sc_state_1' value='1' checked><label for='sc_state_1'><b>판매중</b></label>
				<input type='radio' name='sc_state' id='sc_state_0' value='0' ><label for='sc_state_0'> <b>일시품절</b></label>
				</td>
				<td class='input_box_title'> <b>진열상태 </b></td>
				<td class='input_box_item' >
				<input type='radio' name='sc_disp' id='sc_disp_1' value='1' checked><label for='sc_disp_1'><b>노출함</b> </label>
				<input type='radio' name='sc_disp' id='sc_disp_0' value='0' ><label for='sc_disp_0'><b>노출안함</b></label>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'>중복상품처리</td>
				<td class='input_box_item'>
					<input type='radio' name='dupe_process' id='dupe_process_skip' value='skip' checked><label for='dupe_process_skip'>SKIP</label>
					<input type='radio' name='dupe_process' id='dupe_process_update' value='update' ><label for='dupe_process_update'>UPDATE</label>
				</td>
				<td class='input_box_title'><label for='usable_round'>가격반올림</label><input type='checkbox' name='usable_round' id='usable_round' value='Y' onclick='UsableRound(this)' ".($usable_round == "Y" ? "checked":"")."></td>
				<td class='input_box_item'><input type=hidden name='b_round_precision' id='b_round_precision'  value='".$round_precision."' >
					<select name='round_precision' id='round_precision' ".($usable_round == "N" || $usable_round == "" ? "disabled":"").">
						<option value='2' ".($round_precision == "2" ? "selected":"").">100자리</option>
						<option value='3' ".($round_precision == "3" ? "selected":"").">1000자리</option>
						<option value='4' ".($round_precision == "4" ? "selected":"").">10000자리</option>
					</select>
					<input type=hidden name='b_round_type' id='b_round_type' value='".$round_type."' >
					<input type='radio' name='round_type' id='round_type_1' value='round' ".($usable_round == "N" || $usable_round == ""  ? "disabled":"")." ".(($round_type == "round" || $round_type == "") ? "checked":"")."><label for='round_type_1'>반올림</label>
					<input type='radio' name='round_type' id='round_type_2' value='floor' ".($usable_round == "N" || $usable_round == ""  ? "disabled":"")."  ".($round_type == "floor" ? "checked":"")."><label for='round_type_2'>버림</label>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>등록 카테고리 </b></td>
				<td class='input_box_item' colspan=3>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td style='padding-right:5px;'>".getCategoryList3("대분류", "c_cid0", "onChange=\"loadChangeCategory(this,'c_cid1',2)\" validation=true title='대분류' ", 0, $cid2)."</td>
						<td style='padding-right:5px;'>".getCategoryList3("중분류", "c_cid1", "onChange=\"loadChangeCategory(this,'c_cid2',2)\" title='중분류'", 1, $cid2)."</td>
						<td style='padding-right:5px;'>".getCategoryList3("소분류", "c_cid2", "onChange=\"loadChangeCategory(this,'c_cid3',2)\" title='소분류'", 2, $cid2)."</td>
						<td>".getCategoryList3("세분류", "c_cid3", "onChange=\"loadChangeCategory(this,'c_cid',2)\" title='세분류'", 3, $cid2)."<input type=hidden name='c_cid' ><input type=hidden name='c_depth'></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'>마진설정</td>
				<td class='input_box_item' colspan=3 style='padding:10px 5px;'>
					<table cellpadding=0 cellspacing=0 >
						<col width='130px'>
						<col width='300px'>
						<tr>
							<td>
								<input type=radio name='price_setting' id='price_setting_9' value='9' onclick=\"$('#price_setting_1_zone').hide();$('#price_setting_2_zone').hide();$('#price_setting_9_zone').show();\" ".($price_setting == "9" || $price_setting == "" ? "checked":"")."><label for='price_setting_9'>권장판매가 사용</label><br>
								<input type=radio name='price_setting' id='price_setting_1' value='1' onclick=\"$('#price_setting_1_zone').show();$('#price_setting_2_zone').hide();$('#price_setting_9_zone').hide();\" ".($price_setting == "1"  ? "checked":"")."><label for='price_setting_1'>공급가 * 마진</label><br>
								<input type=radio name='price_setting' id='price_setting_2' value='2' onclick=\"$('#price_setting_1_zone').hide();$('#price_setting_2_zone').show();$('#price_setting_9_zone').hide();\" ".($price_setting == "2" ? "checked":"")."><label for='price_setting_2'>공급가 * 배수</label>
							</td>
							<td style='border:1px solid silver;padding:7px 0px;' class='input_box_td point'>
								<table cellpadding=0 cellspacing=0  width=400 height=60 border=0 class='point' id='price_setting_1_zone' style='display:none;'>
									<col width='100px'>
									<col width='20px'>
									<col width='100px'>
									<col width='20px'>
									<col width='100px'>
									<tr style='text-align:center;font-weight:bold;'>
										<td>공급원가 </td><td> * </td><td>마진(%)</td><td>=</td><td>판매가</td>
									</tr>
									<tr style='text-align:center;'>
										<td>##,### 원</td><td>*</td><td><input type=text class=textbox name='margin_percent' style='width:60px;' value='' ></td><td>=</td><td>##,### 원</td>
									</tr>
								</table>
								<table cellpadding=0 cellspacing=0  width=400 height=60 border=0 class='point' id='price_setting_2_zone' style='display:none;'>
									<col width='100px'>
									<col width='20px'>
									<col width='100px'>
									<col width='20px'>
									<col width='100px'>
									<tr style='text-align:center;font-weight:bold;'>
										<td>공급원가 </td><td> * </td><td>배수</td><td>=</td><td>판매가</td>
									</tr>
									<tr style='text-align:center;'>
										<td>##,### 원</td><td>*</td><td><input type=text class=textbox name='margin_cross' style='width:60px;' value='' ></td><td>=</td><td>##,### 원</td>
									</tr>
								</table>
								<table cellpadding=0 cellspacing=0  width=400 height=60 border=0 id='price_setting_9_zone' >
									<col width='*'>
									<tr style='text-align:center;font-weight:bold;' >
										<td >도매업체에서 제공하는 권장 판매가를 적용합니다. </td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			";
			if($admininfo[mall_use_multishop] && $admininfo[admin_level] == 9 && $admininfo[admin_id] == 'forbiz'){
			$help_text .=	"
			<tr>
				<td class='input_box_title'>입점업체</td>
				<td class='input_box_item' colspan=3>
				".CompanyList2($company_id,"")."
				※ <span  class='small' style='padding-left:0px;'> 입점업체가 선택되면 해당 상품이 선택된 입점업체로 등록되게 됩니다. </span>
				</td>
			</tr>
			";
			}
		$help_text .=	"
			<!--tr>
				<td class='input_box_title'> <b>이미지 </b></td>
				<td class='input_box_item' style='padding:5px;'>
				<input type='checkbox' name='img_update' id='img_update' value='Y' ><label for='img_update'>이미지 정보를 업데이트 합니다.</label><br>
				</td>
			</tr-->
			</table>
			<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
				<tr><td height=50 colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/btn_goods_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
			</table>
		</div>
		";


		$select = "
		<select name='update_type' >
			<option value='2'>선택한 상품 전체에</option>
			<option value='1'>검색한 상품 전체에</option>
		</select>

		<input type='radio' name='update_kind' id='update_kind_stock' value='b2b_goods_reg' ".($update_kind == "b2b_goods_reg" || $update_kind == "" ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_b2b_goods_reg');\"><label for='update_kind_stock' class=blk>도매상품 등록하기</label>
		<!--input type='radio' name='update_kind' id='update_kind_coupon' value='coupon' ".CompareReturnValue("coupon",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_coupon');\"><label for='update_kind_coupon'><!--쿠폰 일괄지급 ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'N')."--> </label-->";

		$Contents .= "".HelpBox($select, $help_text,'270')."</form>";


//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

//$Contents .= HelpBox("공유 상품일괄 관리", $help_text);
$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
if($view == "innerview"){
	$pageging_info["server_goods_list.php"]["page"] = $page;
	$pageging_info["server_goods_list.php"]["nset"] = $nset;

	session_register("pageging_info");

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($goodss_cid, $goodss_depth);
	echo "
	<Script>
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	//parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	parent.document.search_form.goodss_cid.value ='$goodss_cid';
	parent.document.search_form.goodss_depth.value ='$goodss_depth';
	parent.LargeImageView();//추가 kbk 13/09/30
	</Script>";
}else{
	$Script = "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<!-- 스크립트 에러 발생으로 주석처리함 kbk -->
	<!--script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script-->
	<!-- 스크립트 에러 발생으로 주석처리함 kbk -->
	<script Language='JavaScript' type='text/javascript'>
function getGoodssProduct(frm){
	SelectUpdateLoading();

	if(frm.update_type.value == 1){
		if(parseInt(frm.search_searialize_value.value.length) <= 58){
			alert(language_data['product_list.js']['K'][language]);	//'검색상품 전체에 대한 적용은 검색후 가능합니다.'
			select_update_unloading();
			return false;
		}

		if(confirm(language_data['product_list.js']['G'][language])){//'검색상품 전체에 정보변경을 하시겠습니까?'
			return true;
		}else{
			select_update_unloading();
			return false;
		}
	}else{
		var pid_checked_bool = false;
		var pid_obj=document.getElementsByName('goodss_pid[]');//kbk
		for(i=0;i < pid_obj.length;i++){
			if(pid_obj[i].checked){
				pid_checked_bool = true;
			}
		}
		if(!pid_checked_bool){
			alert(language_data['product_list.js']['H'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
			select_update_unloading();
			return false;
		}
	}

	//return false;
	frm.act.value = 'b2b_goods_regs';
	return true;
	//frm.submit();

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

	obj.innerHTML = \"<img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 상품을 가져오는 중입니다..\";

	document.getElementById('select_update_save_loading').appendChild(obj);

	document.getElementById('select_update_save_loading').style.display = 'block';
}


function select_update_unloading(){

	parent.document.getElementById('select_update_parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('select_update_loadingbar').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').style.display = 'none';
}

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


	function co_goods_modify(frm){

		if(document.getElementById('apply_data_1').checked){
			var pid_check_bool = false;
			for(i=0;i < frm.goodss_pid.length;i++){
					if(frm.goodss_pid[i].checked){
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


	function ChangeUpdateForm(selected_id){
		var area = new Array('batch_update_display','batch_update_category','batch_update_b2b_goods_reg'); //,'batch_update_sms','batch_update_coupon'

		for(var i=0; i<area.length; ++i){
			if(area[i]==selected_id){
				document.getElementById(selected_id).style.display = 'block';
				$.cookie('bs_goodsinfo_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
			}else{
				document.getElementById(area[i]).style.display = 'none';
			}
		}
	}

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

	function loadGoodsCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//alert(sel.getAttribute('depth'));

		//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
		window.frames['act'].location.href = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}

	function clearAll(frm){
			for(i=0;i < frm.goodss_pid.length;i++){
					frm.goodss_pid[i].checked = false;
			}
	}
	function checkAll(frm){
	    for(i=0;i < frm.goodss_pid.length;i++){
					frm.goodss_pid[i].checked = true;
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
	$P->strLeftMenu = goodss_menu();
	$P->addScript = $Script;
	$P->Navigation = "상품리스트 > 도매상품 리스트";
	$P->title = "도매상품 리스트";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "";
	}
	$P->PrintLayOut();
}
$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	print_r($script_time);
}
?>