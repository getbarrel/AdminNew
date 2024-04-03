<?
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include("./goodss.lib.php");
$install_path = "../../include/";
include("SOAP/Client.php");
include("../logstory/class/sharedmemory.class");
//print_r($admininfo);

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





if($max == ""){
	if($list_view_type == "catalog"){
		$max = 30; //페이지당 갯수
	}else{
		$max = 10; //페이지당 갯수
	}
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
//if($hostserver){
		//$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ");

		//$db->fetch();
		//$hostserver = $db->dt[hostserver];
		//echo $hostserver;
		$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
		// server.php 의 namespace 와 일치해야함
		$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

		## 한글 인자의 경우 에러가 나므로 인코딩함.

		/*
		$service_infos = (array)$soapclient->call("getUsableServiceInfo",$params = array("mall_ix"=> $admininfo[mall_ix]),	$options);
			//echo $co_goodsinfo;

		$useable_service = (array)$service_infos[useable_service];
		$userable_service_infos = (array)$service_infos[userable_service_infos];
		*/

		$allServiceInfo = (array)$soapclient->call("allServiceInfo",$params = array("mall_ix"=> $admininfo[mall_ix]),	$options);
		//print_r($allServiceInfo);

		$shmop = new Shared("myservice_info");
		$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
		$shmop->SetFilePath();
		$shmop->setObjectForKey($useable_service,"useable_service");



		$paginginfo[start] = $start;
		$paginginfo[max] = $max;
		$paginginfo[page] = $page;

		//print_r($paginginfo);
		//exit;
		foreach($_GET as $key => $value){
			$search_rules[$key]= $value;//urlencode($value);
		}
		//print_r($search_rules);
		$co_goodsinfo = $soapclient->call("getCoGoodsByServer",$params = array("useable_service"=> "", "search_rules"=> $search_rules,  "paginginfo"=> $paginginfo),	$options);

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






//}
//echo $page.":::".$total."::".$max;
if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&list_view_type=$list_view_type&eprice=$eprice&state2=$state2&disp=$disp&company_id=$company_id&goodss_cid=$goodss_cid&goodss_depth=$goodss_depth&list_view_type=$list_view_type");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype&list_view_type=$list_view_type");
	//echo $total.":::".$page."::::".$max."<br>";
}

$Contents =	"<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);'  >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='list_view_type' value='$list_view_type'>

	<input type='hidden' name='goodss_cid' value='$goodss_cid'>
	<input type='hidden' name='goodss_depth' value='$goodss_depth'>
	<input type='hidden' name='co_type' value='$co_type' />
	<input type='hidden' name='co_goods' value='' />
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4 > ".GetTitleNavigation("도매상품 미리보기", "서비스소개 > 도매상품 미리보기")."</td>
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

							<tr height='30'>
								<td class='search_box_title'>  <b>선택된 카테고리</b>  </td>
								<td class='search_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($goodss_cid, $goodss_depth)."(".$total."개)":getCategoryPathByAdmin($goodss_cid, $goodss_depth)."(".$total."개) <b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
							</tr>
							<tr>
								<td class='search_box_title' ><b>카테고리선택</b></td>
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

							$Contents .=	"
							<tr>
								<td class='search_box_title'><b>도매업체</b></td>
								<td class='search_box_item' colspan=3>";
								$Contents .=	"<select name='company_id'>";
								$Contents .=	"<option value=''>등록한 도매업체</option>";
								/*
								for($i=0;$i < count($userable_service_infos);$i++){
									$userable_service_info = (array)$userable_service_infos[$i];
									$Contents .=	"<option value='".$userable_service_info["service_code"]."'  ".($userable_service_info["service_code"] == $company_id ? "selected":"").">".$userable_service_info["com_name"]."</option>";
								}
								*/
								//print_r(count($allServiceInfo));
								for($i=0;$i < count($allServiceInfo);$i++){
									$Info=(array)$allServiceInfo[$i];
									$Contents .=	"<option value='".$Info["service_code"]."'  ".($Info["service_code"] == $company_id ? "selected":"").">".$Info["com_name"]."</option>";
								}
								$Contents .=	"</select>
								</td>
								<!--td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item'><input type='text' name='brand_name' class='textbox' value='$brand_name'></td-->
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
								<option value='30' ".CompareReturnValue(30,$max).">30</option>
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
					<a href='?list_view_type=list'><img type=image src='../images/".$admininfo["language"]."/btn_view_list.gif' border=0></a>  <a href='?list_view_type=catalog'><img type=image src='../images/".$admininfo["language"]."/btn_view_catalog.gif' border=0> </a>
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
			</table>";
if($list_view_type == "catalog"){
	for ($i = 0; $i < count($co_goods); $i++)
	{
		//print_r($co_goods[$i]);
		$co_goods[$i] = (array)$co_goods[$i];
		if($co_goods[$i][bimg]){
			$img_str = $co_goods[$i][bimg];
		}else{
			$img_str = "../image/no_img.gif";
		}

		$innerview .= "<div style='float:left;margin:3px 3px 3px 3px;' title='".$co_goods[$i][pname]."'><a href='http://www.goodss.co.kr/shop/goods_view.php?id=".$co_goods[$i][pid]."' class='screenshot'  rel='".$img_str."'  target=_blank><img src='".$img_str."' width=190 height=190 style='border:1px solid silver;'></a></div>";
	}
}else{
$innerview .= "

			<form name=listform method=post action='server_goods_list.act.php' onsubmit='return SelectUpdate(this)' target='iframe_act'><!--onsubmit='return CheckDelete(this)' target='iframe_act'-->
			<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
			<input type='hidden' name='act' value='b2b_goods_reg'>
			<input type='hidden' id='goodss_pid' value=''>
			<input type='hidden' name='chs_ix'  id='chs_ix' value='".$chs_ix."'>
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
				<col width='7%'>
				<tr bgcolor='#cccccc' align=center>
					<td class=s_td height='30'>번호</td>
					<td class=m_td>제품정보</td>
					<td class=m_td>도매처</td>
					<td class=m_td>판매상태</td>
					<td class=m_td>진열</td>
					<td class=m_td>공급가</td>
					<td class=m_td>정가</td>
					<td class=m_td>판매가</td>
					<td class=m_td>도매상품 등록일자</td>
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
				$no = $total - ($page - 1) * $max - $i;

				$co_goods[$i] = (array)$co_goods[$i];
				if($co_goods[$i][bimg]){
					$img_str = $co_goods[$i][bimg];
				}else{
					$img_str = "../image/no_img.gif";
				}

			$innerview .= "<tr bgcolor='#ffffff'>
								<td class='list_box_td list_bg_gray' align=center title='".$co_goods[$i][admin]."-".$co_goods[$i][pid]."'>
									<b>".$no."</b>
								</td>
								<td class='list_box_td point' align=center >
									<table cellpadding=1 cellspacing=0 width='100%' style='margin:10px 0px;'>
									<tr>
										<td width=60 rowspan=5><a href='http://www.goodss.co.kr/shop/goods_view.php?id=".$co_goods[$i][pid]."' class='screenshot'  rel='".$img_str."' target=_blank><img src='".$img_str."' width=50 height=50 style='border:1px solid silver;'></a></td>
										<td align='left'><span style='color:gray' class='small'>".$co_goods[$i][category_text]."</span></td>
									</tr>
									<tr>
										<td align='left' style='line-height:140%;padding:4px 4px;'>";
				$innerview .= "<a href='http://www.goodss.co.kr/shop/goods_view.php?id=".$co_goods[$i][pid]."' target='_blank'>
												<b> ".($co_goods[$i][brand_name] ? "[".$co_goods[$i][brand_name]."]":"")." ".$co_goods[$i][pname]."</b>

												</a><br>
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
							<td class='list_box_td list_bg_gray' align=center nowrap>
								<table align=center>

									<!--tr>
										<td><a href=\"javascript:CopyData(document.forms['listform'], '".$co_goods[$i][pid]."','".$co_goods[$i][pname]."','".$admininfo[admin_level]."');\"><img src='../images/".$admininfo["language"]."/btn_modify.gif' border=0 align=absmiddle title=\" ' ".strip_tags($co_goods[$i][pname])." ' 에 대한 정보를 수정합니다.\"></a></td>
									</tr-->

									<tr>
										<td><a href='/shop/goods_view.php?cid=".$co_goods[$i][cid]."&id=".$co_goods[$i][pid]."&goodss_depth=3&b_ix=".$co_goods[$i][brand]."' target='_blank'><img src='../images/".$admininfo["language"]."/btn_preview.gif'></a></td>
									</tr>
								</table>
							</td>

						</tr>

						";
			}
		}
		$innerview .= "</table>";
}

		$innerview .= "<table width=100%>";
		$innerview .= "<tr><td colspan=9 align=right>".$str_page_bar."</td></tr>";
		$innerview .= "</table>";
$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>";


//$Contents .= HelpBox("공유 상품일괄 관리", $help_text);
$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($goodss_cid, $goodss_depth);
	echo "
	<Script>
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	parent.document.search_form.goodss_cid.value ='$goodss_cid';
	parent.document.search_form.goodss_depth.value ='$goodss_depth';
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
		//alert(depth);
		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
		//document.getElementById('act').src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}

	function loadGoodsCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.getAttribute('depth');
		//alert(sel.getAttribute('depth'));
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//alert(depth);
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
	$P->Navigation = "서비스소개 > 도매상품 미리보기";
	$P->title = "도매상품 미리보기";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}
?>