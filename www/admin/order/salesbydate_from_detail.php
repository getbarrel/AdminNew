<?
include("../class/layout.class");
//include("./pie.graph.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../lib/report.lib.php");

$db = new Database;
$mdb = new Database;
$sms_design = new SMS;

if($groupbytype == ""){
	$groupbytype="day";
}
if(!is_array($age)){
	$age[] = "";
}

if($_GET[mall_ix] !="" ){
	if($_GET["mall_ix"] == "dcb33fdbf7c6f40e334a43ce42194637"){
		$where .="and od.buyer_type = '1' ";
	}else if($_GET["mall_ix"] == "dcb33fdbf7c6f40e334a43ce42194638"){
		$where .="and od.buyer_type = '2' ";
	}
}
if($_GET[seller_type] !="" ){
	$where .="and seller_type = '".$_GET[seller_type]."' ";
}

if($status_disp == "IC"){
	$date_str = "date_format(od.ic_date,'%Y%m%d')";
	$date_colorm = "od.ic_date";
}else if($status_disp == "DI"){
	$date_str = "date_format(od.di_date,'%Y%m%d')";
	$date_colorm = "od.di_date";
}else if($status_disp == "OC" || $status_disp == ""){
	$date_str = "date_format(od.regdate,'%Y%m%d')";
	$date_colorm = "od.regdate";
}


if($_GET["sdate"] && $_GET["edate"]){
	$startDate = $_GET["sdate"];
	$endDate = $_GET["edate"];

	$PickPeriod = ( strtotime($endDate) - strtotime($startDate) ) / 86400;
	
	if($PickPeriod > 15){
		echo "<script>alert('조회 기간은 15일를 초과할 수 없습니다');location.href='./salesbydate_from_detail.php?groupbytype=goods2'</script>";
		//echo "<script>location.reload();</script>";
		exit;
	}


	$where .= "and ".$date_colorm." between '$startDate 00:00:00' and '$endDate 23:59:59' ";
}else{
	$sdate = date("Y-m-d");
	$edate = date("Y-m-d");

	$where .= "and ".$date_colorm." between '$sdate 00:00:00' and '$edate 23:59:59' ";
}


if($cid2 != ""){
	$where .= " and od.cid LIKE '".substr($cid2,0,($depth+1)*3)."%'";
}

if(is_array($age)){
	for($i=0;$i < count($age);$i++){
		if($age[$i] != ""){
			if($age_str == ""){
				if($age[$i] == 10){
					$age_str .= " o.age between 0 and ".($age[$i]+9)." ";
				}else if($age[$i] == 60){
					$age_str .= " o.age >= ".$age[$i]."  ";
				}else{
					$age_str .= " o.age between ".$age[$i]." and ".($age[$i]+9)." ";
				}
			}else{
				if($age[$i] == 10){
					$age_str .= " or o.age between 0 and ".($age[$i]+9)." ";
				}else if($age[$i] == 60){
					$age_str .= " or o.age >= ".$age[$i]."  ";
				}else{
					$age_str .= " or o.age between ".$age[$i]." and ".($age[$i]+10)." ";
				}
			}
		}
	}

	if($age_str != ""){
		$where .= "and ($age_str) ";
	}
}else{
	if($age){
		$where .= "and o.age between ".$age[$i]." and ".($age[$i]+10)." ";
	}
}

if($_GET[member_div] =="member" ){
	$where .="and o.uid != '' ";
}else if($_GET[member_div] =="nonmember" ){
	$where .="and o.uid = '' ";
}

if($_GET[sex] !="" ){
	//$where .="and sex = '".$_GET[sex]."' ";
	//컬럼 추가후 
}

//promotion_cupon_code
if($_GET["brand_code"]){
	$brand_code_array = str_replace(" ","",$_GET["brand_code"]);
	$brand_code_array = explode(",",$brand_code_array);
	if(is_array($brand_code_array)){
		$where .= " AND od.brand_code IN ('".implode("','",$brand_code_array)."')";
	}else{
		$where .= " AND od.brand_code = '".$_GET["brand_code"]."' ";
	}
}

if($_GET["promotion_cupon_code"]){
	$use_coupon_code_array = explode(",",$_GET["promotion_cupon_code"]);
	if(is_array($use_coupon_code_array)){
		$where .= " AND od.use_coupon_code IN ('".implode("','",$use_coupon_code_array)."')";
	}else{
		$where .= " AND od.use_coupon_code = '".$_GET["promotion_cupon_code"]."' ";
	}
}

if($_GET["product_code"]){
	$product_code_array = explode(",",$_GET["product_code"]);
	if(is_array($product_code_array)){
		$where .= " AND od.pid IN ('".implode("','",$product_code_array)."')";
	}else{
		$where .= " AND od.pid = '".$_GET["product_code"]."' ";
	}
}

if($_GET["company_v_code"]){
	$company_v_code_array = explode(",",$_GET["company_v_code"]);
	if(is_array($company_v_code_array)){
		$where .= " AND od.company_id IN ('".implode("','",$company_v_code_array)."')";
	}else{
		$where .= " AND od.company_id = '".$_GET["company_v_code"]."' ";
	}
}

if($_GET["trade_company_code"]){
	$trade_company_code_array = explode(",",$_GET["trade_company_code"]);
	if(is_array($trade_company_code_array)){
		$where .= " AND od.trade_company IN ('".implode("','",$trade_company_code_array)."')";
	}else{
		$where .= " AND od.trade_company = '".$_GET["trade_company_code"]."' ";
	}
}
// promotion_cupon_code


if($_GET["vat_type"]){
	$vat_type = $_GET["vat_type"];
}else{
	$vat_type = "Y";
}

if($_GET["status_disp"]){
	$status_disp = $_GET["status_disp"];
}else{
	$status_disp = "OC";
}


if($_GET["groupbytype"]){
	$groupbytype = $_GET["groupbytype"];
}else{
	$groupbytype = "day";
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
		    <td align='left' colspan=6 > ".GetTitleNavigation("매출요약", "매출관리 > 매출요약 > 주문상태별 요약 ")."</td>
	  </tr>
	  <tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'> 
			<div class='tab'>
				<table class='s_org_tab'>
					<col width='750px'>
					<col width='*'>
					<tr>
						<td class='tab'>
							
							<table id='tab_01' ".(($groupbytype == "day" ) ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='?groupbytype=day&mmode=$mmode'>&nbsp;&nbsp;일자별(팀별)&nbsp;&nbsp; </a></td>
								<th class='box_03'></th>
							</tr>
							</table>
					
							<table id='tab_02' ".(($groupbytype == "category" ) ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='?groupbytype=category&mmode=$mmode'>&nbsp;&nbsp;카테고리별(팀별)&nbsp;&nbsp; </a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".(($groupbytype == "goods" ) ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='?groupbytype=goods&mmode=$mmode'>&nbsp;&nbsp;상품별(팀별)&nbsp;&nbsp; </a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".(($groupbytype == "from" ) ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='?groupbytype=from&mmode=$mmode'>&nbsp;&nbsp;판매처별&nbsp;&nbsp; </a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".(($groupbytype == "trade" ) ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='?groupbytype=trade&mmode=$mmode'>&nbsp;&nbsp;매입처별&nbsp;&nbsp; </a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".(($groupbytype == "goods2" ) ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='?groupbytype=goods2&mmode=$mmode'>&nbsp;&nbsp;상품별 매출&nbsp;&nbsp; </a></td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'></td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	 
	
	<tr height=150>
		<td   >
			 <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='groupbytype' value='$groupbytype'>
			<input type='hidden' name='sprice' value='0' />
			<input type='hidden' name='eprice' value='1000000' />
			<input type='hidden' name='mode' value='search'>
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
							<col width='35%'>";
					if($_SESSION["admin_config"][front_multiview] == "Y"){
					$Contents01 .= "
					<tr>
						<td class='search_box_title' > 쇼핑몰타입</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
					}
					$Contents01 .= "
							<tr height=27>
								<td class='search_box_title'><b>구매일자</b></td>
								<td class='search_box_item' colspan=3 >
								".search_date('sdate','edate',$sdate,$edate,'','L')."
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>전시 카테고리선택</b></td>
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
							<tr  height=27>
							  <td class='search_box_title' width='13%' bgcolor='#efefef' align=center >연령대</td>
							  <td class='search_box_item' width='*' align=left style='padding-left:5px;' colspan=3>
							  
								  <input type='checkbox' name='age[]' id='age_' value='' ".CompareReturnValue("",$age,"checked")."><label for='age_'>전체</label>
								  <input type='checkbox' name='age[]' id='age_10' value='10'  ".CompareReturnValue("10",$age,"checked")."><label for='age_10'  style='width:200px;'>~10대</label>
								  <input type='checkbox' name='age[]' id='age_20' value='20'  ".CompareReturnValue("20",$age,"checked")."><label for='age_20' >20대</label>
								  <input type='checkbox' name='age[]' id='age_30' value='30'  ".CompareReturnValue("30",$age,"checked")."><label for='age_30' >30대</label>
								  <input type='checkbox' name='age[]' id='age_40' value='40'  ".CompareReturnValue("40",$age,"checked")."><label for='age_40' >40대</label>
								  <input type='checkbox' name='age[]' id='age_50' value='50'  ".CompareReturnValue("50",$age,"checked")."><label for='age_50'  >50대</label>
								  <input type='checkbox' name='age[]' id='age_60' value='60'  ".CompareReturnValue("60",$age,"checked")."><label for='age_60' >ETC</label>
								</select>
							  </td>
							 </tr>
							<tr  height=27>
								<td class='search_box_title'><b>회원/비회원 여부</b></td>
								<td class='search_box_item'>
									<select name='member_div' style='font-size:12px;'>
										<option value='' ".ReturnStringAfterCompare($member_div, "", " selected").">전체</option>
										<option value='member' ".ReturnStringAfterCompare($member_div, "member", " selected").">회원</option>
										<option value='nonmember' ".ReturnStringAfterCompare($member_div, "nonmember", " selected").">비회원</option>
									</select>
								</td>
								<td class='search_box_title' bgcolor='#efefef' align=center>성별검색 </td>
								  <td class='search_box_item' align=left style='padding-left:5px;'>
								  <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
								  <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
								  <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
								</td>
							</tr>
							";
if(false){
$Contents01 .= "
							<tr  height=27>
							  <td class='search_box_title' width='13%' bgcolor='#efefef' align=center >지역선택</td>
							  <td class='search_box_item' width='*' align=left style='padding-left:5px;'>
							  <select name='region' >
								  <option value=''>-- 선택 --</option>
								  <option value='서울'  ".CompareReturnValue("서울",$region,"selected").">서울</option>
								  <option value='충북'  ".CompareReturnValue("충북",$region,"selected").">충북</option>
								  <option value='충남'  ".CompareReturnValue("충남",$region,"selected").">충남</option>
								  <option value='전북'  ".CompareReturnValue("전북",$region,"selected").">전북</option>
								  <option value='제주'  ".CompareReturnValue("제주",$region,"selected").">제주</option>
								  <option value='전남'  ".CompareReturnValue("전남",$region,"selected").">전남</option>
								  <option value='경북'  ".CompareReturnValue("경북",$region,"selected").">경북</option>
								  <option value='경남'  ".CompareReturnValue("경남",$region,"selected").">경남</option>
								  <option value='경기'  ".CompareReturnValue("경기",$region,"selected").">경기</option>
								  <option value='부산'  ".CompareReturnValue("부산",$region,"selected").">부산</option>
								  <option value='대구'  ".CompareReturnValue("대구",$region,"selected").">대구</option>
								  <option value='인천'  ".CompareReturnValue("인천",$region,"selected").">인천</option>
								  <option value='광주'  ".CompareReturnValue("광주",$region,"selected").">광주</option>
								  <option value='대전'  ".CompareReturnValue("대전",$region,"selected").">대전</option>
								  <option value='울산'  ".CompareReturnValue("울산",$region,"selected").">울산</option>
								  <option value='강원'  ".CompareReturnValue("강원",$region,"selected").">강원</option>
								</select>
							  </td>
							<td class='search_box_title' bgcolor='#efefef' align=center>성별검색 </td>
							  <td class='search_box_item' align=left style='padding-left:5px;'>
							  <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
							  <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
							  <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
							</td>
							</tr>
							";
}
if(false){
$Contents01 .= "
							<tr>
								<td class='search_box_title'><b>제휴사 카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<select name='company_cid1' id='com_cid1' onchange='loadComCategory(this);'><option value=''>대분류</option>".getCompanyCateSelectBoxOption(1,$company_cid1)."</select>
									";
									if(!empty($company_cid)){
										$Contents01 .="
													<select name='company_cid' id='com_cid2'>".getCompanyCateSelectBoxOption(2,$company_cid)."</select>";
									}else{
										$Contents01 .="
													<select name='company_cid' id='com_cid2'><option value=''>중분류</option></select>";
									}
									$Contents01 .=	"
								</td>
							</tr>";
}
$Contents01 .= "
							<tr>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../images/".$admininfo["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=brand',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#brand_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='brand_code' id='brand_code' value='".$brand_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>상품코드</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../images/".$admininfo["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=product',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#product_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='product_code' id='product_code' value='".$product_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
			if($admininfo[admin_level] == 9){
				$Contents01 .="<tr>
								<td class='search_box_title'><b>셀러업체</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../images/".$admininfo["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=company_v',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#company_v_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='company_v_code' id='company_v_code' value='".$company_v_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>매입업체</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../images/".$admininfo["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=trade_company',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#trade_company_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='trade_company_code' id='trade_company_code' value='".$trade_company_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
			}
if(false){
				$Contents01 .="<tr>
								<td class='search_box_title'><b>프로모션(카드)</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../images/".$admininfo["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=promotion_card',600,380,'code_search')\"  style='cursor:pointer;'></td>
											<td><input type=text class='textbox' name='promotion_card_code' id='promotion_card_code' value='".$promotion_card_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
}

$Contents01 .="
							<tr>
								<td class='search_box_title'><b>프로모션(쿠폰)</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../images/".$admininfo["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=promotion_cupon',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#promotion_cupon_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='promotion_cupon_code' id='promotion_cupon_code' value='".$promotion_cupon_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
if($admininfo[admin_id] == "forbiz"){
			if($admininfo[admin_level] == 9 && false){
				$Contents01 .="<tr>
								<td class='search_box_title'><b>제휴사</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../images/".$admininfo["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=company_j',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#company_j_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='company_j_code' id='company_j_code' value='".$company_j_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
			}
}
				$Contents01 .="
							<!--tr>
								<td class='search_box_title'><b>주문상태</b></td>
								<td class='search_box_item' colspan=3>
								<input type='checkbox' name='status[]'  id='status_0' value=\"DC','BF\" ".CompareReturnValue("DC','BF", $status, " checked")."><label for='status_0'>주문</label>
								<input type='checkbox' name='status[]'  id='status_1' value='CC' ".CompareReturnValue("CC", $status, " checked")."><label for='status_1'>취소</label>
								<input type='checkbox' name='status[]'  id='status_2' value='SO' ".CompareReturnValue("SO", $status, " checked")."><label for='status_2'>품절</label>
								<input type='checkbox' name='status[]'  id='status_3' value='FC' ".CompareReturnValue("FC", $status, " checked")."><label for='status_3'>환불</label>
								</td>
							</tr-->
							";
 
							$Contents01 .=	"
							<tr>
								<td class='search_box_title'><b>VAT</b></td>
                                <td class='search_box_item'  >
                                    <input type='radio' name='vat_type'  id='vat_y' value='Y' ".ReturnStringAfterCompare($vat_type, "Y", " checked")."><label for='vat_y'>포함</label>
                                    <input type='radio' name='vat_type' id='vat_n' value='N' ".ReturnStringAfterCompare($vat_type,"N"," checked")."><label for='vat_n'>제외</label>
                                </td>

								<!--td class='search_box_title'>
										<select name='search_type'  style=\"font-size:12px;height:20px;\">
											<option value='pname'".ReturnStringAfterCompare($search_type, "pname", " selected").">상품명</option>
											<option value='pid'".ReturnStringAfterCompare($search_type, "pid", " selected").">상품코드</option>
										</select>
								</td>
								<td class='search_box_item' style='padding-right:5px;margin-top:3px;'>
									<table cellpadding=0 cellspacing=0 >
										<tr >
											<td><INPUT id=search_texts  class='textbox1' value='".$search_text."' onclick='findNames();'  clickbool='false' style='height:16px;FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
											<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' >
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
								</td--> 
								<td class='search_box_title'><b>집계기준</b></td>
                                <td class='search_box_item'  >
                                    <input type='radio' name='status_disp'  id='oc' value='OC' ".ReturnStringAfterCompare($status_disp, "OC", " checked")."><label for='oc'>주문일 기준</label>
									<input type='radio' name='status_disp'  id='ic' value='IC' ".ReturnStringAfterCompare($status_disp, "IC", " checked")."><label for='ic'>결제완료 기준</label>
                                    <input type='radio' name='status_disp' id='di' value='DI' ".ReturnStringAfterCompare($status_disp,"DI"," checked")."><label for='di'>출고완료 기준</label>
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
				<tr >
					<td colspan=2 align=center style='padding:10px 0px'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	
	";
if(false){
$Contents01 .= "
	  <tr height=40>
		<td >
			<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>";
if(!$selected_month){
	$selected_month = date("Ym",time());
}
for($i=-4;$i < 6;$i++){
	$display_month = date("Ym",mktime(0,0,0,substr($selected_month,4,2)+$i,substr($selected_month,6,2),substr($selected_month,0,4)));
	$display_month2 = date("Y.m",mktime(0,0,0,substr($selected_month,4,2)+$i,substr($selected_month,6,2),substr($selected_month,0,4)));
	$Contents01 .= "
								<table id='tab_01'  ".($display_month == $selected_month ? "class=on":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?selected_month=".$display_month."'\">".$display_month2."</td>
									<th class='box_03'></th>
								</tr>
								</table>
								";
}

$Contents01 .= "
							</td>
							<td class='btn' style='padding:10px 0px 0px 10px;'>

							</td>
						</tr>
						</table>
					</div>
		</td>
	  </tr>";
}
$Contents01 .= "
	  <tr height=30 align='right' ".($_SESSION["admininfo"]["charger_id"]=="forbiz" ? "" : "style='display:none'")."><td style='border-bottom:2px solid #efefef'><a href='?mode=excel&".str_replace("&mode=search", "",$_SERVER["QUERY_STRING"])."'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a></td></tr>
	  <tr>
	  	<td style='padding:5px 0px 0px 0px'>";
		if($groupbytype == "from" || $groupbytype == "trade" || $groupbytype == "goods2"){
	  		$Contents01 .= salesByDateDetailReportTable($vdate, $groupbytype);
		}else{
			$Contents01 .= salesByDateFromDetailReportTable($vdate, $groupbytype);
		}
		$Contents01 .="
	  	</td>
	  </tr>

	  <tr height=50><td colspan=5 class=small><!--* 해당 통계는 주문상세내용을 기준으로 산정되며 매출액은 주문취소금액 과 입금예정 내역은 제외됩니다.-->
	   ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</td></tr>


	  <tr height=50><td colspan=5></td></tr>
	</table>";



$Contents = $Contents01;

$Script .= "
<script Language='JavaScript' type='text/javascript'>
$(function() {
	$(\"#start_datepicker\").datepicker({
	monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	showMonthAfterYear:true,
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
	monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});


function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	//var depth = sel.getAttribute('depth');
	var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//alert(depth);
	//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	//alert(1);
	// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}



function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

</script>";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
if($groupbytype == "from"){
	$P->Navigation = "매출관리 > 일별매출액(판매처)";
	$P->title = "팀별 매출액 ";
}else if($groupbytype == "category"){
	$P->Navigation = "매출관리 > 팀별 매출액(카테고리) ";
	$P->title = "팀별 매출액(카테고리)";
}else if($groupbytype == "day"){
	$P->Navigation = "매출관리 > 팀별 매출액(일자별) ";
	$P->title = "팀별 매출액(일자별)";
}else if($groupbytype == "trade"){
	$P->Navigation = "매출관리 > 매입처별 매출액";
	$P->title = "매입처별 매출액";
}else if($groupbytype == "goods"){
	$P->Navigation = "매출관리 > 상품별(팀별) 매출액";
	$P->title = "상품별(팀별) 매출액";
}else if($groupbytype == "goods2"){
	$P->Navigation = "매출관리 > 상품별 매출액";
	$P->title = "상품별 매출액";
}

echo $P->PrintLayOut();


function salesByDateDetailReportTable($vdate,$groupbytype="day",$SelectReport=1){
	global $depth,$referer_id, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status;
	global $search_sdate, $search_edate;
	global $where;

	$nview_cnt = 0;
	$cid = $referer_id;
	if($SelectReport == ""){
		$SelectReport = 1;
	}
	$fordb = new Database();
	if($depth == ""){
		$depth = 0;
	}else{
		$depth = $depth+1;
	}



	if($vdate == ""){
		$vdate = date("Ymd", time());
		$vyesterday = date("Ymd", time()-84600);
		$voneweekago = date("Ymd", time()-84600*7);
	}else{
		if($SelectReport ==3){
			$vdate = $vdate."01";
		}
		$vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
		$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
		$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
	}

	if($groupbytype=="day"){
		$group_colum1 = "date_format(od.regdate,'%Y%m%d') as group_colum ";
		$group_colum2 = "date_format(od.regdate,'%Y%m%d') as group_colum ";
	}else if($groupbytype=="from"){
		$group_colum1 = "od.order_from  as group_colum";
		$group_colum2 = " 'self' as group_colum";
		//$group_colum = "date_format(od.regdate,'%Y%m%d') as group_colum ";
	}else if($groupbytype=="trade"){
		$group_colum1 = "od.trade_company_name as group_colum_name, od.trade_company  as group_colum";
		$group_colum2 = " '' as group_colum_name, '' as group_colum";
	}else if($groupbytype=="goods2"){
		$group_colum1 = "od.pname as group_colum_name, od.pid  as group_colum";
		$group_colum2 = " '' as group_colum_name, '' as group_colum";
	}

	if($_GET["vat_type"] == "Y"){
		$price_str = "(od.pt_dcprice)";
		$coprice_str = "od.coprice";
	}else{
		$price_str = "case when surtax_yorn = 'N' then (od.pt_dcprice)*100/110 else (od.pt_dcprice) end  ";
		$coprice_str = "case when surtax_yorn = 'N' then (od.coprice)*100/110 else (od.coprice) end  ";
	}

	if($_GET["status_disp"] == "IC"){
		$date_str = "date_format(od.ic_date,'%Y%m%d')";
	}else if($_GET["status_disp"] == "DI"){
		$date_str = "date_format(od.di_date,'%Y%m%d')";
	}else if($_GET["status_disp"] == "OC" || $_GET["status_disp"] == ""){
		$date_str = "date_format(od.regdate,'%Y%m%d')";
	}


	$sql = "select data.group_colum, ".(($groupbytype=="goods2" || $groupbytype == "trade") ? "group_colum_name, ":"")." sum(data.order_sale_cnt) as order_sale_cnt, sum(order_sale_sum) as order_sale_sum, sum(order_coprice_sum) as order_coprice_sum, 
				sum(sale_all_cnt) as sale_all_cnt, sum(sale_all_sum) as sale_all_sum, sum(coprice_all_sum) as coprice_all_sum, 
				sum(cancel_sale_cnt) as cancel_sale_cnt, sum(cancel_sale_sum) as cancel_sale_sum, sum(cancel_coprice_sum) as cancel_coprice_sum, 
				sum(return_sale_cnt) as return_sale_cnt, sum(return_sale_sum) as return_sale_sum, sum(return_coprice_sum) as return_coprice_sum, 
				sum(whole_delivery_cnt) as whole_delivery_cnt, sum(whole_delivery_sum) as whole_delivery_sum, sum(return_delivery_cnt) as return_delivery_cnt, sum(return_delivery_sum) as return_delivery_sum
				from (
				Select ".$group_colum1." ,
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$price_str."  else 0 end) as order_sale_sum,
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum,

				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt,
				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$price_str."  else 0 end) as sale_all_sum,
				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum,

				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$price_str."  else 0 end) as cancel_sale_sum,
				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum,

				sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
				sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$price_str."  else 0 end) as return_sale_sum,
				sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 
				0 as whole_delivery_cnt,
				0 as whole_delivery_sum,
				0 as return_delivery_cnt,
				0 as return_delivery_sum
				from shop_order o, shop_order_detail od
				where o.oid = od.oid and od.status NOT IN ('".implode("','",$non_sale_status)."')
				$where
				
				";
				//and ".$date_str." LIKE '".substr($vdate,0,6)."%'  AND

				if($groupbytype=="day"){
					$sql .= " group by date_format(o.date,'%Y%m%d') ";
				}else if($groupbytype=="from"){
					$sql .= " group by group_colum ";
					//$sql .= "group by date_format(o.date,'%Y%m%d') ";
				}else if($groupbytype=="goods2"){
					$sql .= " group by group_colum ";
				}else if($groupbytype=="trade"){
					$sql .= " group by group_colum ";
				}

				//상품별 매출  검색이 안대서 $where 추가했음 2013-10-04 이학봉

				if($groupbytype!="goods2"){
					$sql .= "
					union 
					select ".$group_colum2." , 
					0 as order_sale_cnt,
					0 as order_sale_sum,
					0 as order_coprice_sum,

					0 as sale_all_cnt,
					0 as sale_all_sum,
					0 as coprice_all_sum,

					0 as cancel_sale_cnt,
					0 as cancel_sale_sum,
					0 as cancel_coprice_sum,

					0 as return_sale_cnt,
					0 as return_sale_sum,
					0 as return_coprice_sum, 
					sum(case when oph.price_div = 'D' and payment_status = 'G'  then 1 else 0 end) as whole_delivery_cnt,
					sum(case when oph.price_div = 'D' and payment_status = 'G'  then oph.expect_price else 0 end) as whole_delivery_sum,
					sum(case when oph.price_div = 'D' and payment_status = 'F'  then 1 else 0 end) as return_delivery_cnt,
					sum(case when oph.price_div = 'D' and payment_status = 'F'  then oph.expect_price else 0 end) as return_delivery_sum
					from 
					shop_order o, shop_order_price_history oph
					where o.oid = oph.oid 
					and o.oid in (select o.oid from shop_order o, shop_order_detail od where o.oid = od.oid $where)
					and oph.expect_price > 0
					";//oid = '201309061906-2905'
				
				//and date_format(o.date,'%Y%m%d') LIKE '".substr($vdate,0,6)."%' 

				if($groupbytype=="day"){
					$sql .= " group by date_format(o.date,'%Y%m%d') ";
				}else if($groupbytype=="from"){
					$sql .= " group by group_colum ";
					//$sql .= "group by date_format(o.date,'%Y%m%d') ";
				}else if($groupbytype=="goods2"){
					$sql .= " group by group_colum ";
				}else if($groupbytype=="trade"){
					$sql .= " group by group_colum ";
				}
			}
				$sql .= ") data 
				group by group_colum ";

				if($groupbytype=="goods2"){
					$sql .= " order by order_sale_cnt desc ";
				}

				//							left join ".TBL_COMMERCE_VIEWINGVIEW." b on od.pid = b.pid
				//AND od.status NOT IN ('".implode("','",$non_sale_status)."')
				//and substr(c.cid,1,".(($depth+1)*3).") = substr(b.cid,1,3)


		$dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

	//echo nl2br($sql);
	if($sql){
		if($_GET["sdate"] != "" && $_GET["edate"] != ""){
			$fordb->query($sql);
		}
	}

	if($_GET["mode"] == "excel"){
		include '../include/phpexcel/Classes/PHPExcel.php';
		PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

		//date_default_timezone_set('Asia/Seoul');

		$sheet = new PHPExcel();

		// 속성 정의
		$sheet->getProperties()->setCreator("포비즈 코리아")
									 ->setLastModifiedBy("Mallstory.com")
									 ->setTitle("accounts plan price List")
									 ->setSubject("accounts plan price List")
									 ->setDescription("generated by forbiz korea")
									 ->setKeywords("mallstory")
									 ->setCategory("accounts plan price List");
		$col = 'A';

		
		$start=3;
		$i = $start;
		
		if($groupbytype=="from"){
			$excel_title="팀별 상세분석(팀별)";
		}else if($groupbytype=="goods2"){
			$excel_title="상품별 매출분석";
		}
		
		if($groupbytype == "from"){
			$sheet->getActiveSheet(0)->mergeCells('A2:V2');
			$sheet->getActiveSheet(0)->setCellValue('A2', $excel_title);
			$sheet->getActiveSheet(0)->mergeCells('A3:V3');
			$sheet->getActiveSheet(0)->setCellValue('A3', $dateString);
		}else if($groupbytype=="goods2"){
			$sheet->getActiveSheet(0)->mergeCells('A2:O2');
			$sheet->getActiveSheet(0)->setCellValue('A2', $excel_title);
			$sheet->getActiveSheet(0)->mergeCells('A3:O3');
			$sheet->getActiveSheet(0)->setCellValue('A3', $dateString);
		}
		$sheet->getActiveSheet(0)->mergeCells('A'.($i+1).':A'.($i+3));
		$sheet->getActiveSheet(0)->mergeCells('B'.($i+1).':B'.($i+3));
		$sheet->getActiveSheet(0)->mergeCells('C'.($i+1).':D'.($i+1));
		$sheet->getActiveSheet(0)->mergeCells('E'.($i+1).':L'.($i+1));
		$sheet->getActiveSheet(0)->mergeCells('M'.($i+1).':M'.($i+3));
		$sheet->getActiveSheet(0)->mergeCells('N'.($i+1).':O'.($i+2));
		
		if($groupbytype == "from"){
			$sheet->getActiveSheet(0)->mergeCells('P'.($i+1).':U'.($i+1));
			$sheet->getActiveSheet(0)->mergeCells('V'.($i+1).':V'.($i+3));
		}

		$sheet->getActiveSheet(0)->mergeCells('C'.($i+2).':D'.($i+2));
		$sheet->getActiveSheet(0)->mergeCells('E'.($i+2).':F'.($i+2));
		$sheet->getActiveSheet(0)->mergeCells('G'.($i+2).':H'.($i+2));
		$sheet->getActiveSheet(0)->mergeCells('I'.($i+2).':J'.($i+2));
		$sheet->getActiveSheet(0)->mergeCells('K'.($i+2).':L'.($i+2));
		
		if($groupbytype == "from"){
			$sheet->getActiveSheet(0)->mergeCells('P'.($i+2).':Q'.($i+2));
			$sheet->getActiveSheet(0)->mergeCells('R'.($i+2).':S'.($i+2));
			$sheet->getActiveSheet(0)->mergeCells('T'.($i+2).':U'.($i+2));
		}



		$sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "순");

		if($groupbytype=="from"){
			$sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "판매처(팀별)");
		}else if($groupbytype=="goods2"){
			$sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "<상품코드> 상품별");
		}

		$sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "주문매출"); 
		$sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "매출");
		$sheet->getActiveSheet(0)->setCellValue('M' . ($i+1), "실매출액원가");
		$sheet->getActiveSheet(0)->setCellValue('N' . ($i+1), "수익");
		
		if($groupbytype == "from"){
			$sheet->getActiveSheet(0)->setCellValue('P' . ($i+1), "배송비");
			$sheet->getActiveSheet(0)->setCellValue('V' . ($i+1), "매출액\r(상품+배송비)");
		}

		$sheet->getActiveSheet(0)->setCellValue('C' . ($i+2), "전체주문\r(입금예정포함)");
		$sheet->getActiveSheet(0)->setCellValue('E' . ($i+2), "전체매출액(전체)");
		$sheet->getActiveSheet(0)->setCellValue('G' . ($i+2), "취소매출액(-)");
		$sheet->getActiveSheet(0)->setCellValue('I' . ($i+2), "반품매출액(-)");
		$sheet->getActiveSheet(0)->setCellValue('K' . ($i+2), "실매출액(+)");
		
		if($groupbytype == "from"){
			$sheet->getActiveSheet(0)->setCellValue('P' . ($i+2), "전체매출액");
			$sheet->getActiveSheet(0)->setCellValue('R' . ($i+2), "환불매출액");
			$sheet->getActiveSheet(0)->setCellValue('T' . ($i+2), "실매출액");
		}


		$sheet->getActiveSheet(0)->setCellValue('C' . ($i+3), "수량(개)");
		$sheet->getActiveSheet(0)->setCellValue('D' . ($i+3), "주문액(원)");
		$sheet->getActiveSheet(0)->setCellValue('E' . ($i+3), "수량(개)");
		$sheet->getActiveSheet(0)->setCellValue('F' . ($i+3), "주문액(원)");
		$sheet->getActiveSheet(0)->setCellValue('G' . ($i+3), "수량(개)");
		$sheet->getActiveSheet(0)->setCellValue('H' . ($i+3), "주문액(원)");
		$sheet->getActiveSheet(0)->setCellValue('I' . ($i+3), "수량(개)");
		$sheet->getActiveSheet(0)->setCellValue('J' . ($i+3), "주문액(원)");
		$sheet->getActiveSheet(0)->setCellValue('K' . ($i+3), "수량(개)");
		$sheet->getActiveSheet(0)->setCellValue('L' . ($i+3), "주문액(원)");
		$sheet->getActiveSheet(0)->setCellValue('N' . ($i+3), "마진(원)");
		$sheet->getActiveSheet(0)->setCellValue('O' . ($i+3), "마진율(%)");
		
		if($groupbytype == "from"){
			$sheet->getActiveSheet(0)->setCellValue('P' . ($i+3), "수량(개)");
			$sheet->getActiveSheet(0)->setCellValue('Q' . ($i+3), "주문액(원)");
			$sheet->getActiveSheet(0)->setCellValue('E' . ($i+3), "수량(개)");
			$sheet->getActiveSheet(0)->setCellValue('S' . ($i+3), "주문액(원)");
			$sheet->getActiveSheet(0)->setCellValue('T' . ($i+3), "수량(개)");
			$sheet->getActiveSheet(0)->setCellValue('U' . ($i+3), "주문액(원)");
		}

		$sheet->setActiveSheetIndex(0);
		//$i = $i + 2;

		for($i=0;$i<$fordb->total;$i++){
			$fordb->fetch($i);

			$sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 4), ($i + 1));
			
			if($groupbytype =="from"){
				if($fordb->dt[group_colum] == "offline"){
					$group_name = "오프라인영업";
				}else if($fordb->dt[group_colum] == "self"){
					$group_name = "자체쇼핑몰";
				}else if($fordb->dt[group_colum] == "pos"){
					$group_name = "POS";
				}else{
					$group_name = "기타";
				}
				//$group_name = $fordb->dt[group_colum];
			}else if($groupbytype =="goods2"){
				$group_name = $fordb->dt[group_colum];
				$group_colum_name = $fordb->dt[group_colum_name];
			}

			if($groupbytype=="from"){
				$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 4), $group_name);
			}else if($groupbytype=="goods2"){
				$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 4), "<".$group_colum_name.">".$group_name);
			}

			$sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 4), $fordb->dt[order_sale_cnt]);
			$sheet->getActiveSheet()->getStyle('C' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 4), $fordb->dt[order_sale_sum]);
			$sheet->getActiveSheet()->getStyle('D' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 4), $fordb->dt[sale_all_cnt]);
			$sheet->getActiveSheet()->getStyle('E' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 4), $fordb->dt[sale_all_sum]);
			$sheet->getActiveSheet()->getStyle('F' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 4), $fordb->dt[cancel_sale_cnt]);
			$sheet->getActiveSheet()->getStyle('G' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 4), $fordb->dt[cancel_sale_sum]);
			$sheet->getActiveSheet()->getStyle('H' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 4), $fordb->dt[return_sale_cnt]);
			$sheet->getActiveSheet()->getStyle('I' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 4), $fordb->dt[return_sale_sum]);
			$sheet->getActiveSheet()->getStyle('J' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			
			$real_sale_cnt = $fordb->dt[sale_all_cnt]-$fordb->dt[cancel_sale_cnt]-$fordb->dt[return_sale_cnt];
			$sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 4), $real_sale_cnt);	
			$sheet->getActiveSheet()->getStyle('K' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$real_sale_coprice = $fordb->dt[sale_all_sum]-$fordb->dt[cancel_sale_sum]-$fordb->dt[return_sale_sum];
			$sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 4), $real_sale_coprice);
			$sheet->getActiveSheet()->getStyle('L' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sale_coprice = $fordb->dt[coprice_all_sum]-$fordb->dt[cancel_coprice_sum]-$fordb->dt[return_coprice_sum];
			$sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 4), $sale_coprice);
			$sheet->getActiveSheet()->getStyle('M' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$margin = $real_sale_coprice - $sale_coprice;
			$sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 4), $margin);
			$sheet->getActiveSheet()->getStyle('N' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
			

		//	$mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin,0)."&nbsp;</td>";
			if($real_sale_coprice > 0){
				$margin_rate = round($margin/$real_sale_coprice*100);
			}else{
				$margin_rate = 0;
			}
			$sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 4), $margin_rate);
			$sheet->getActiveSheet()->getStyle('O' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


			if($groupbytype == "from"){

				$sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 4), $fordb->dt[whole_delivery_cnt]);
				$sheet->getActiveSheet()->getStyle('P' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

				$sheet->getActiveSheet()->setCellValue('Q' . ($i + $start + 4), $fordb->dt[whole_delivery_sum]);
				$sheet->getActiveSheet()->getStyle('Q' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

				$sheet->getActiveSheet()->setCellValue('R' . ($i + $start + 4), $fordb->dt[return_delivery_cnt]);
				$sheet->getActiveSheet()->getStyle('R' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

				$sheet->getActiveSheet()->setCellValue('S' . ($i + $start + 4), $fordb->dt[return_delivery_sum]);
				$sheet->getActiveSheet()->getStyle('S' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

				$sheet->getActiveSheet()->setCellValue('T' . ($i + $start + 4), $fordb->dt[whole_delivery_cnt]-$fordb->dt[return_delivery_cnt]);
				$sheet->getActiveSheet()->getStyle('T' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

				$sheet->getActiveSheet()->setCellValue('U' . ($i + $start + 4), $fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum]);
				$sheet->getActiveSheet()->getStyle('U' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				
				$real_sale_sum_with_deliveryprice = $real_sale_coprice +  $fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum];

				$sheet->getActiveSheet()->setCellValue('V' . ($i + $start + 4), $real_sale_sum_with_deliveryprice);
				$sheet->getActiveSheet()->getStyle('V' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			}

			$order_sale_cnt = $order_sale_cnt + returnZeroValue($fordb->dt[order_sale_cnt]);
			$order_sale_sum = $order_sale_sum + returnZeroValue($fordb->dt[order_sale_sum]);

			$sale_all_cnt = $sale_all_cnt + returnZeroValue($fordb->dt[sale_all_cnt]);
			$sale_all_sum = $sale_all_sum + returnZeroValue($fordb->dt[sale_all_sum]);

			$cancel_sale_cnt = $cancel_sale_cnt + returnZeroValue($fordb->dt[cancel_sale_cnt]);
			$cancel_sale_sum = $cancel_sale_sum + returnZeroValue($fordb->dt[cancel_sale_sum]);

			$return_sale_cnt = $return_sale_cnt + returnZeroValue($fordb->dt[return_sale_cnt]);
			$return_sale_sum = $return_sale_sum + returnZeroValue($fordb->dt[return_sale_sum]);

			$real_sale_cnt_sum = $real_sale_cnt_sum + returnZeroValue($real_sale_cnt);
			$real_sale_coprice_sum = $real_sale_coprice_sum + returnZeroValue($real_sale_coprice);
			$sale_coprice_sum = $sale_coprice_sum + returnZeroValue($sale_coprice);
			$margin_sum = $margin_sum + returnZeroValue($margin);
			
			$whole_delivery_cnt += returnZeroValue($fordb->dt[whole_delivery_cnt]);
			$whole_delivery_sum += returnZeroValue($fordb->dt[whole_delivery_sum]);
			$return_delivery_cnt += returnZeroValue($fordb->dt[return_delivery_cnt]);
			$return_delivery_sum += returnZeroValue($fordb->dt[return_delivery_sum]);

			$real_delivery_cnt += returnZeroValue($fordb->dt[whole_delivery_cnt]-$fordb->dt[return_delivery_cnt]);
			$real_delivery_sum += returnZeroValue($fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum]);

			$real_sale_sum_with_deliveryprice_sum += $real_sale_sum_with_deliveryprice;

		}

		if($real_sale_coprice_sum > 0){
			$margin_sum_rate = round($margin_sum/$real_sale_coprice_sum*100);
		}else{
			$margin_sum_rate = 0;
		}

		//$i++;
		$sheet->getActiveSheet(0)->mergeCells('A'.($i + $start+4).':C'.($i+ $start+4));
		$sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 4), '합계');
		//$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 4), getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4));
		$sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 4), $order_sale_cnt);
		$sheet->getActiveSheet()->getStyle('C' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 4), $order_sale_sum);
		$sheet->getActiveSheet()->getStyle('D' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 4), $sale_all_cnt);
		$sheet->getActiveSheet()->getStyle('E' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 4), $sale_all_sum);
		$sheet->getActiveSheet()->getStyle('F' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 4), $cancel_sale_cnt);
		$sheet->getActiveSheet()->getStyle('G' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 4), $cancel_sale_sum);
		$sheet->getActiveSheet()->getStyle('H' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 4), $return_sale_cnt);
		$sheet->getActiveSheet()->getStyle('I' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 4), $return_sale_sum);
		$sheet->getActiveSheet()->getStyle('J' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
		

		$sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 4), $real_sale_cnt_sum);	
		$sheet->getActiveSheet()->getStyle('K' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 4), $real_sale_coprice_sum);
		$sheet->getActiveSheet()->getStyle('L' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 4), $sale_coprice_sum);
		$sheet->getActiveSheet()->getStyle('M' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 4), $margin_sum);
		$sheet->getActiveSheet()->getStyle('N' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 4), $margin_sum_rate);
		$sheet->getActiveSheet()->getStyle('O' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		if($groupbytype == "from"){

			$sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 4), $whole_delivery_cnt);
			$sheet->getActiveSheet()->getStyle('P' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('Q' . ($i + $start + 4), $whole_delivery_sum);
			$sheet->getActiveSheet()->getStyle('Q' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('R' . ($i + $start + 4), $return_delivery_cnt);
			$sheet->getActiveSheet()->getStyle('R' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('S' . ($i + $start + 4), $return_delivery_sum);
			$sheet->getActiveSheet()->getStyle('S' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('T' . ($i + $start + 4), $real_delivery_cnt);
			$sheet->getActiveSheet()->getStyle('T' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('U' . ($i + $start + 4), $real_delivery_sum);
			$sheet->getActiveSheet()->getStyle('U' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('V' . ($i + $start + 4), $real_sale_sum_with_deliveryprice_sum);
			$sheet->getActiveSheet()->getStyle('V' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		}

		$sheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$sheet->getActiveSheet()->getColumnDimension('B')->setWidth(45);
		//$sheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$sheet->getActiveSheet()->getColumnDimension('C')->setWidth(9);
		$sheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('E')->setWidth(9);
		$sheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('G')->setWidth(9);
		$sheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('I')->setWidth(9);
		$sheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('K')->setWidth(9);
		$sheet->getActiveSheet()->getColumnDimension('L')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		$sheet->getActiveSheet()->getColumnDimension('N')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('O')->setWidth(10);
		
		if($groupbytype == "from"){
			$sheet->getActiveSheet()->getColumnDimension('P')->setWidth(9);
			$sheet->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
			$sheet->getActiveSheet()->getColumnDimension('E')->setWidth(9);
			$sheet->getActiveSheet()->getColumnDimension('S')->setWidth(10);
			$sheet->getActiveSheet()->getColumnDimension('T')->setWidth(9);
			$sheet->getActiveSheet()->getColumnDimension('U')->setWidth(10);
			$sheet->getActiveSheet()->getColumnDimension('V')->setWidth(10);
		}

		$sheet->getActiveSheet()->getRowDimension($start+2)->setRowHeight(30);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949",$excel_title.".xls").'"');
		header('Cache-Control: max-age=0');

		

		// $objWriter->setUseInlineCSS(true);
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  )
		  );
		
		
		if($groupbytype == "from"){
			$sheet->getActiveSheet()->getStyle('A'.($start+1).':V'.($i+$start+4))->applyFromArray($styleArray);
			$sheet->getActiveSheet()->getStyle('A'.$start.':V'.($i+$start+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$sheet->getActiveSheet()->getStyle('A'.$start.':V'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getActiveSheet()->getStyle('A'.($start).':V'.($start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true);
			$sheet->getActiveSheet()->getStyle('B'.($start+4).':B'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			//$sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getAlignment()->setIndent(1);
			//$sheet->getActiveSheet()->getStyle('A10')->getAlignment()->setIndent(10); 왼쪽 들여쓰기
			$sheet->getActiveSheet()->getStyle('A'.$start.':V'.($i+$start+4))->getFont()->setSize(10)->setName('돋움');

			$sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움');
			$sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getActiveSheet()->getStyle('A'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}else{
			$sheet->getActiveSheet()->getStyle('A'.($start+1).':O'.($i+$start+4))->applyFromArray($styleArray);
			$sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getActiveSheet()->getStyle('A'.($start).':O'.($start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true);
			$sheet->getActiveSheet()->getStyle('B'.($start+4).':B'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			//$sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getAlignment()->setIndent(1);
			//$sheet->getActiveSheet()->getStyle('A10')->getAlignment()->setIndent(10); 왼쪽 들여쓰기
			$sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getFont()->setSize(10)->setName('돋움');

			$sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움');
			$sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getActiveSheet()->getStyle('A'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}

		

		  unset($styleArray);
		$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
		$objWriter->save('php://output');

		exit; 
	}

/*
	if($groupbytype !=""){
		$mstring = "<table width='100%' border=0>
						<tr><td>".TitleBar("상품군별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString)."</td><td align=right>(단위:원) </td></tr>
					</table>";
	}
*/
	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>";
	if($groupbytype !=""){
		 if($groupbytype == "goods2"){
		$mstring .= "<col width='2%'>
						<col width='3%'>
						<col width='*'>";
		 }else{
		$mstring .= "<col width='3%'>
						<col width='*'>";
		 }
	}
		$mstring .= "
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='6%'>";
	$mstring .= "
		<tr height=30>";
		if($groupbytype !=""){
			if($groupbytype =="day"){
			$mstring .= "
			<td class=s_td rowspan=3>순</td>
			<td class=m_td rowspan=3>날짜</td>";
			}else if($groupbytype =="from"){
			$mstring .= "
			<td class=s_td rowspan=3>순</td>
			<td class=m_td rowspan=3>판매처(팀별)</td>";
			}else if($groupbytype =="trade"){
				$mstring .= "
			<td class=s_td rowspan=3>순</td>
			<td class=m_td rowspan=3>매입처</td>";
			}else if($groupbytype =="goods2"){
				$mstring .= "
			<td class=s_td rowspan=3>순</td>
			<td class=m_td rowspan=3>상품코드</td>
			<td class=m_td rowspan=3>상품명</td>";
			}
		}
			$mstring .= "
			<td class=m_td colspan=2>주문매출</td>
			<td class=m_td colspan=8>매출</td>
			<td class=m_td rowspan=3>실매출액<br>원가</td>
			<td class=m_td colspan=2 rowspan=2>수익</td>";
			if($groupbytype != "trade"  && $groupbytype != "goods2"){
			$mstring .= "<td class=m_td colspan=6>배송비</td>
			<td class=m_td rowspan=3 >매출액<br>(상품+배송비)</td>";
			}
			$mstring .= "
		</tr>
		<tr height=30>
			<td class='m_td small' colspan=2 style='line-height:140%;'><b>전체주문</b><br>(입금예정포함)</td>
			<td class=m_td colspan=2>전체매출액(전체)</td>
			<td class=m_td colspan=2>취소매출액(-)</td>
			<td class=m_td colspan=2>반품매출액(-)</td>
			<td class=m_td colspan=2>실매출액(+)</td>";
			if($groupbytype != "trade"  && $groupbytype != "goods2"){
			$mstring .= "
			<td class=m_td colspan=2>전체매출액</td>
			<td class=m_td colspan=2>환불매출액</td>
			<td class=m_td colspan=2>실매출액</td>";
			}
			$mstring .= "
		</tr>
		<tr height=30 align=center>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td >마진(원)</td>
			<td class=m_td >마진율(%)</td>";
			if($groupbytype != "trade"  && $groupbytype != "goods2"){
			$mstring .= "
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>";
			}
			$mstring .= "
			</tr>\n";
			/*

			sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$price_str."  else 0 end) as order_sale_sum,
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$price_str."  else 0 end) as cancel_sale_sum,
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$price_str."  else 0 end) as return_sale_sum,

							*/

	for($i=0;$i<$fordb->total;$i++){
		$fordb->fetch($i);

		$real_sale_cnt = $fordb->dt[sale_all_cnt]-$fordb->dt[cancel_sale_cnt]-$fordb->dt[return_sale_cnt];
		$real_sale_sum = $fordb->dt[sale_all_sum]-$fordb->dt[cancel_sale_sum]-$fordb->dt[return_sale_sum];
		$sale_coprice = $fordb->dt[coprice_all_sum]-$fordb->dt[cancel_coprice_sum]-$fordb->dt[return_coprice_sum];
		$margin = $real_sale_sum - $sale_coprice;
		if($real_sale_sum > 0){
			$margin_rate = round($margin/$real_sale_sum*100);
		}else{
			$margin_rate = 0;
		}

		if($groupbytype !=""){
			$week_num = date("w",mktime(0,0,0,substr($fordb->dt[vdate],4,2),substr($fordb->dt[vdate],6,2),substr($fordb->dt[vdate],0,4)));

			if($groupbytype =="day"){
				$group_name = getNameOfWeekday($week_num, $fordb->dt[group_colum],"priodname");
			}else if($groupbytype =="from"){
				if($fordb->dt[group_colum] == "offline"){
					$group_name = "오프라인영업";
				}else if($fordb->dt[group_colum] == "self"){
					$group_name = "자체쇼핑몰";
				}else if($fordb->dt[group_colum] == "pos"){
					$group_name = "POS";
				}else{
					$group_name = "기타";
				}
				//$group_name = $fordb->dt[group_colum];
			}else if($groupbytype =="trade"){
				if($_SESSION["adminininfo"]["charger_id"] == "forbiz"){
					$group_name = $fordb->dt[group_colum_name]." - ".$fordb->dt[group_colum]."";
				}else{
					$group_name = $fordb->dt[group_colum];
					$group_name = $fordb->dt[group_colum_name];
				}
			}else if($groupbytype =="goods2"){
				$group_name = $fordb->dt[group_colum];
				$group_colum_name = $fordb->dt[group_colum_name];
			}

			$mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>";
			 if($groupbytype =="goods2"){
				$mstring .= "<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$group_name." </td>";
				$mstring .= "<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$group_colum_name." </td>";
			 }else{
				$mstring .= "<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$group_name." </td>";
			 }
			$mstring .= "
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[order_sale_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[order_sale_sum],0)."&nbsp;</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[sale_all_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[sale_all_sum],0)."&nbsp;</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[cancel_sale_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[cancel_sale_sum],0)."&nbsp;</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[return_sale_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[return_sale_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_cnt,0)."&nbsp;</td>
			<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_sum,0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($sale_coprice,0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin,0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin_rate,0)." %&nbsp;</td>";
			if($groupbytype != "trade" && $groupbytype != "goods2"){
			$mstring .= "
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[whole_delivery_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[whole_delivery_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[return_delivery_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[return_delivery_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[whole_delivery_cnt]-$fordb->dt[return_delivery_cnt],0)."&nbsp;</td>
			<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum],0)."&nbsp;</td>";

			$real_sale_sum_with_deliveryprice = $real_sale_sum +  $fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum];
			$mstring .= "
			<td class='list_box_td number blue_point str' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_sum_with_deliveryprice,0)."</td>";
			}
			$mstring .= "
			</tr>\n";
		}

		$order_sale_cnt = $order_sale_cnt + returnZeroValue($fordb->dt[order_sale_cnt]);
		$order_sale_sum = $order_sale_sum + returnZeroValue($fordb->dt[order_sale_sum]);

		$sale_all_cnt = $sale_all_cnt + returnZeroValue($fordb->dt[sale_all_cnt]);
		$sale_all_sum = $sale_all_sum + returnZeroValue($fordb->dt[sale_all_sum]);

		$cancel_sale_cnt = $cancel_sale_cnt + returnZeroValue($fordb->dt[cancel_sale_cnt]);
		$cancel_sale_sum = $cancel_sale_sum + returnZeroValue($fordb->dt[cancel_sale_sum]);

		$return_sale_cnt = $return_sale_cnt + returnZeroValue($fordb->dt[return_sale_cnt]);
		$return_sale_sum = $return_sale_sum + returnZeroValue($fordb->dt[return_sale_sum]);

		$real_sale_cnt_sum = $real_sale_cnt_sum + returnZeroValue($real_sale_cnt);
		$real_sale_sum_sum = $real_sale_sum_sum + returnZeroValue($real_sale_sum);
		$sale_coprice_sum = $sale_coprice_sum + returnZeroValue($sale_coprice);
		$margin_sum = $margin_sum + returnZeroValue($margin);

		$whole_delivery_cnt += returnZeroValue($fordb->dt[whole_delivery_cnt]);
		$whole_delivery_sum += returnZeroValue($fordb->dt[whole_delivery_sum]);
		$return_delivery_cnt += returnZeroValue($fordb->dt[return_delivery_cnt]);
		$return_delivery_sum += returnZeroValue($fordb->dt[return_delivery_sum]);

		$real_delivery_cnt += returnZeroValue($fordb->dt[whole_delivery_cnt]-$fordb->dt[return_delivery_cnt]);
		$real_delivery_sum += returnZeroValue($fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum]);

		$real_sale_sum_with_deliveryprice_sum += $real_sale_sum_with_deliveryprice;

	}

	if ($sale_all_sum == 0){
		if($groupbytype == "from"){
			$mstring .= "<tr  align=center height=200><td colspan=22 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}else if($groupbytype == "trade"){
			$mstring .= "<tr  align=center height=200><td colspan=15 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}else if($groupbytype == "goods2"){
			$mstring .= "<tr  align=center height=200><td colspan=16 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		
		}else{
			$mstring .= "<tr  align=center height=200><td colspan=13 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}
		
	}

	if($real_sale_sum_sum > 0){
		$margin_sum_rate = $margin_sum/$real_sale_sum_sum*100;
	}else{
		$margin_sum_rate = 0;
	}

	if($groupbytype !=""){
		$mstring .= "<tr height=25 align=right>";
		 if($groupbytype == "goods2"){
			$mstring .= "<td class=s_td align=center colspan=3>합계</td>";
		 }else{
			$mstring .= "<td class=s_td align=center colspan=2>합계</td>";
		 }
		$mstring .= "
		<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_sale_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($return_sale_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($return_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($sale_coprice_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($margin_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($margin_sum_rate,0)." %</td>";
if($groupbytype != "trade"  && $groupbytype != "goods2"){
$mstring .= "
		<td class='e_td number' style='padding-right:10px;'>".number_format($whole_delivery_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($whole_delivery_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($return_delivery_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($return_delivery_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_delivery_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_delivery_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_with_deliveryprice_sum,0)."</td>";
}
$mstring .= "
		</tr>\n";
	}else{
		$mstring .= "<tr height=25 align=right>
		<td class='number' style='padding-right:10px;'>".number_format($order_sale_cnt,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($order_sale_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($sale_all_cnt,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($sale_all_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($cancel_sale_cnt,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($cancel_sale_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($return_sale_cnt,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($return_sale_sum,0)."</td>
		<td class='number point' style='padding-right:10px;'>".number_format($real_sale_cnt_sum,0)."</td>
		<td class='number point' style='padding-right:10px;'>".number_format($real_sale_sum_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($sale_coprice_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($margin_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($margin_sum_rate,0)." %</td>

		<td class='e_td number' style='padding-right:10px;'>".number_format($whole_delivery_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($whole_delivery_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($return_delivery_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($return_delivery_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_delivery_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_delivery_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_sum+$real_delivery_sum,0)."</td>
		</tr>\n";
	}
	$mstring .= "</table>\n";

	if($groupbytype !=""){
		
		$mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>".(($_GET["vat_type"] == "Y" || $_GET["vat_type"] == "") ? "VAT 포함":"VAT 미포함")."</td></tr></table>";

		/*
		$help_text = "
		<table>
			<tr>
				<td style='line-height:150%'>
				- 카테고리별 상품조회 회수를 바탕으로 귀사 사이트의 인기카테고리와 비인기 카테고리를 정확히 파악하여 그에 맞는 운영및 마케팅 정책을 수립 수행할수 있습니다<br>
				- 좌측 카테고리를 클릭하면 하부 카테고리에 대한 상세 정보가 표시 됩니다<br><br>
				</td>
			</tr>
		</table>
		";*/

		$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B' );


		$mstring .= HelpBox("일별매출(종합)", $help_text);
	}
	return $mstring;
}


function salesByDateFromDetailReportTable($vdate,$groupbytype="day",$SelectReport=1){
	global $depth,$referer_id, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status, $not_real_sale_status;
	global $search_sdate, $search_edate;
	global $where;

	$nview_cnt = 0;
	$cid = $referer_id;
	if($SelectReport == ""){
		$SelectReport = 1;
	}
	$fordb = new Database();
	if($depth == ""){
		$depth = 0;
	}else{
		$depth = $depth+1;
	}



	if($vdate == ""){
		$vdate = date("Ymd", time());
		$vyesterday = date("Ymd", time()-84600);
		$voneweekago = date("Ymd", time()-84600*7);
	}else{
		if($SelectReport ==3){
			$vdate = $vdate."01";
		}
		$vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
		$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
		$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
	}

	if($_GET["status_disp"] == "IC"){
		$date_str = "date_format(od.ic_date,'%Y%m%d')";
	}else if($_GET["status_disp"] == "DI"){
		$date_str = "date_format(od.di_date,'%Y%m%d')";
	}else if($_GET["status_disp"] == "OC" || $_GET["status_disp"] == ""){
		$date_str = "date_format(od.regdate,'%Y%m%d')";
	}

	if($groupbytype=="day"){
		$sql = "Select ".$date_str." as vdate , ";
	}else if($groupbytype=="category"){
		$sql = "Select IFNULL(cid,'9') as cid , ";
	}else if($groupbytype=="goods"){
		$sql = "Select pid , pname, ";
	}

	if($_GET["vat_type"] == "Y"){
		$price_str = "(od.pt_dcprice)";
	}else{
		$price_str = "case when surtax_yorn = 'N' then (od.pt_dcprice)*100/110 else (od.pt_dcprice) end  ";
	}

	
	
	$sql .= "
				sum(case when order_from = 'self' and od.status not in ('".implode("','",$all_sale_status)."')  then ".$price_str."  else 0 end) as self_sale_all_sum,
				sum(case when order_from = 'self' and od.status IN ('".implode("','",$cancel_status)."')  then ".$price_str."  else 0 end) as self_cancel_sale_sum,
				sum(case when order_from = 'self' and od.status IN ('".implode("','",$return_status)."')  then ".$price_str."  else 0 end) as self_return_sale_sum,

				sum(case when order_from = 'offline' and od.status not in ('".implode("','",$all_sale_status)."')  then ".$price_str."  else 0 end) as offline_sale_all_sum,
				sum(case when order_from = 'offline' and od.status IN ('".implode("','",$cancel_status)."')  then ".$price_str."  else 0 end) as offline_cancel_sale_sum,
				sum(case when order_from = 'offline' and od.status IN ('".implode("','",$return_status)."')  then ".$price_str."  else 0 end) as offline_return_sale_sum,

				sum(case when order_from = 'pos' and od.status not in ('".implode("','",$all_sale_status)."')  then ".$price_str."  else 0 end) as pos_sale_all_sum,
				sum(case when order_from = 'pos' and od.status IN ('".implode("','",$cancel_status)."')  then ".$price_str."  else 0 end) as pos_cancel_sale_sum,
				sum(case when order_from = 'pos' and od.status IN ('".implode("','",$return_status)."')  then ".$price_str."  else 0 end) as pos_return_sale_sum,

				sum(case when od.status not in ('".implode("','",$not_real_sale_status)."')  then ".$price_str."  else 0 end) as sale_sum
				from  shop_order o, shop_order_detail od
				where o.oid = od.oid 				
				and od.status NOT IN ('".implode("','",$non_sale_status)."') 
				$where 
				";
				//and ".$date_str." LIKE '".substr($vdate,0,6)."%'  

				if($groupbytype=="day"){
					$sql .="group by ".$date_str." ";
				}else if($groupbytype=="category"){
					$sql .="group by cid ";
				}else if($groupbytype=="goods"){
					$sql .="group by pid ";
				}
				//				not_real_sale_status			left join ".TBL_COMMERCE_VIEWINGVIEW." b on od.pid = b.pid
				//AND od.status NOT IN ('".implode("','",$non_sale_status)."')
				//and substr(c.cid,1,".(($depth+1)*3).") = substr(b.cid,1,3)


		$dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

	//echo nl2br($sql);
	if($sql){
		if($_GET["sdate"] != "" && $_GET["edate"] != ""){
			$fordb->query($sql);
		}
	}

	if($_GET["mode"] == "excel"){
		include '../include/phpexcel/Classes/PHPExcel.php';
		PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

		//date_default_timezone_set('Asia/Seoul');

		$sheet = new PHPExcel();

		// 속성 정의
		$sheet->getProperties()->setCreator("포비즈 코리아")
									 ->setLastModifiedBy("Mallstory.com")
									 ->setTitle("salesbydate from detail List")
									 ->setSubject("salesbydate from detail List")
									 ->setDescription("generated by forbiz korea")
									 ->setKeywords("mallstory")
									 ->setCategory("salesbydate from detail List");
		$col = 'A';

		$start=3;
		$i = $start;
		
		if($groupbytype=="day"){
			$excel_title="팀별 상세분석(일자)";
		}else if($groupbytype=="category"){
			$excel_title="팀별 상세분석(카테고리)";
		}else if($groupbytype=="goods"){
			$excel_title="팀별 상세분석(상품별)";
		}

		$sheet->getActiveSheet(0)->mergeCells('A2:Q2');
		$sheet->getActiveSheet(0)->setCellValue('A2', $excel_title);
		$sheet->getActiveSheet(0)->mergeCells('A3:Q3');
		$sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

		$sheet->getActiveSheet(0)->mergeCells('A'.($i+1).':A'.($i+2));
		$sheet->getActiveSheet(0)->mergeCells('B'.($i+1).':B'.($i+2));

		$sheet->getActiveSheet(0)->mergeCells('C'.($i+1).':G'.($i+1));
		$sheet->getActiveSheet(0)->mergeCells('H'.($i+1).':L'.($i+1));
		$sheet->getActiveSheet(0)->mergeCells('M'.($i+1).':Q'.($i+1));
		
		if($groupbytype=="day"){
			$sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "날짜");
		}else if($groupbytype=="category"){
			$sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "카테고리");
		}else if($groupbytype=="goods"){
			$sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "<상품코드> 상품별");
		}
		
		$sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "실매출액");
		$sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "자체쇼핑몰"); 
		$sheet->getActiveSheet(0)->setCellValue('H' . ($i+1), "오프라인영업");
		$sheet->getActiveSheet(0)->setCellValue('M' . ($i+1), "POS");

		$sheet->getActiveSheet(0)->setCellValue('C' . ($i+2), "전체매출액");
		$sheet->getActiveSheet(0)->setCellValue('D' . ($i+2), "취소매출액");
		$sheet->getActiveSheet(0)->setCellValue('E' . ($i+2), "반품매출액");
		$sheet->getActiveSheet(0)->setCellValue('F' . ($i+2), "실매출액");
		$sheet->getActiveSheet(0)->setCellValue('G' . ($i+2), "점유율");

		$sheet->getActiveSheet(0)->setCellValue('H' . ($i+2), "전체매출액");
		$sheet->getActiveSheet(0)->setCellValue('I' . ($i+2), "취소매출액");
		$sheet->getActiveSheet(0)->setCellValue('J' . ($i+2), "반품매출액");
		$sheet->getActiveSheet(0)->setCellValue('K' . ($i+2), "실매출액");
		$sheet->getActiveSheet(0)->setCellValue('L' . ($i+2), "점유율");

		$sheet->getActiveSheet(0)->setCellValue('M' . ($i+2), "전체매출액");
		$sheet->getActiveSheet(0)->setCellValue('N' . ($i+2), "취소매출액");
		$sheet->getActiveSheet(0)->setCellValue('O' . ($i+2), "반품매출액");
		$sheet->getActiveSheet(0)->setCellValue('P' . ($i+2), "실매출액");
		$sheet->getActiveSheet(0)->setCellValue('Q' . ($i+2), "점유율");

		$sheet->setActiveSheetIndex(0);
		//$i = $i + 2;

		for($i=0;$i<$fordb->total;$i++){
			$fordb->fetch($i);
			
			$week_num = date("w",mktime(0,0,0,substr($fordb->dt[vdate],4,2),substr($fordb->dt[vdate],6,2),substr($fordb->dt[vdate],0,4)));

			if($groupbytype == "day"){
				$title_val = getNameOfWeekday($week_num, $fordb->dt[vdate],"priodname");
			}else if($groupbytype == "category"){
				$title_val = ($fordb->dt[cid] == "" ? "기타" : strip_tags(getCategoryPath($fordb->dt[cid],4)));
                $title_val = str_replace("&gt;",">",$title_val);
			}else if($groupbytype == "goods"){
				$title_val = "<".$fordb->dt[pid].">".$fordb->dt[pname];
			}

			$_self_sale_sum = $fordb->dt[self_sale_all_sum]-$fordb->dt[self_cancel_sale_sum]-$fordb->dt[self_return_sale_sum];
			$_offline_sale_sum = $fordb->dt[offline_sale_all_sum]-$fordb->dt[offline_cancel_sale_sum]-$fordb->dt[offline_return_sale_sum];
			$_pos_sale_sum = $fordb->dt[pos_sale_all_sum]-$fordb->dt[pos_cancel_sale_sum]-$fordb->dt[pos_return_sale_sum];


			$sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), $title_val);

			$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), $fordb->dt[sale_sum]);
			$sheet->getActiveSheet()->getStyle('B' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), $fordb->dt[self_sale_all_sum]);
			$sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), $fordb->dt[self_cancel_sale_sum]);
			$sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $fordb->dt[self_return_sale_sum]);
			$sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), $_self_sale_sum);
			$sheet->getActiveSheet()->getStyle('F' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), ($fordb->dt[sale_sum] > 0 ? $_self_sale_sum/$fordb->dt[sale_sum] : 0));
			$sheet->getActiveSheet()->getStyle('G' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


			$sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), $fordb->dt[offline_sale_all_sum]);
			$sheet->getActiveSheet()->getStyle('H' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), $fordb->dt[offline_cancel_sale_sum]);
			$sheet->getActiveSheet()->getStyle('I' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 3), $fordb->dt[offline_return_sale_sum]);
			$sheet->getActiveSheet()->getStyle('J' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 3), $_offline_sale_sum);
			$sheet->getActiveSheet()->getStyle('K' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 3), ($fordb->dt[sale_sum] > 0 ? $_offline_sale_sum/$fordb->dt[sale_sum] : 0));
			$sheet->getActiveSheet()->getStyle('L' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


			$sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 3), $fordb->dt[pos_sale_all_sum]);
			$sheet->getActiveSheet()->getStyle('M' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 3), $fordb->dt[pos_cancel_sale_sum]);
			$sheet->getActiveSheet()->getStyle('N' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 3), $fordb->dt[pos_return_sale_sum]);
			$sheet->getActiveSheet()->getStyle('O' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 3), $_pos_sale_sum);
			$sheet->getActiveSheet()->getStyle('P' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('Q' . ($i + $start + 3), ($fordb->dt[sale_sum] > 0 ? $_pos_sale_sum/$fordb->dt[sale_sum] : 0));
			$sheet->getActiveSheet()->getStyle('Q' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


			$sale_sum = $sale_sum + returnZeroValue($fordb->dt[sale_sum]);

			$self_sale_all_sum = $self_sale_all_sum + returnZeroValue($fordb->dt[self_sale_all_sum]);
			$self_cancel_sale_sum = $self_cancel_sale_sum + returnZeroValue($fordb->dt[self_cancel_sale_sum]);
			$self_return_sale_sum = $self_return_sale_sum + returnZeroValue($fordb->dt[self_return_sale_sum]);
			$self_sale_sum += $_self_sale_sum;// + ($fordb->dt[self_sale_all_sum] - $fordb->dt[self_cancel_sale_sum] - $fordb->dt[self_return_sale_sum]);

			$offline_sale_all_sum = $offline_sale_all_sum + returnZeroValue($fordb->dt[offline_sale_all_sum]);
			$offline_cancel_sale_sum = $offline_cancel_sale_sum + returnZeroValue($fordb->dt[offline_cancel_sale_sum]);
			$offline_return_sale_sum = $offline_return_sale_sum + returnZeroValue($fordb->dt[offline_return_sale_sum]);
			$offline_sale_sum += $_offline_sale_sum;// + returnZeroValue($fordb->dt[offline_sale_all_sum] - $fordb->dt[offline_cancel_sale_sum] - $fordb->dt[offline_return_sale_sum]);

			$pos_sale_all_sum = $pos_sale_all_sum + returnZeroValue($fordb->dt[pos_sale_all_sum]);
			$pos_cancel_sale_sum = $pos_cancel_sale_sum + returnZeroValue($fordb->dt[pos_cancel_sale_sum]);
			$pos_return_sale_sum = $pos_return_sale_sum + returnZeroValue($fordb->dt[pos_return_sale_sum]);
			$pos_sale_sum += $_pos_sale_sum;

		}


		$sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), "합계");

		$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), $sale_sum);
		$sheet->getActiveSheet()->getStyle('B' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), $self_sale_all_sum);
		$sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), $self_cancel_sale_sum);
		$sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $self_return_sale_sum);
		$sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), $self_sale_sum);
		$sheet->getActiveSheet()->getStyle('F' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), ($sale_sum ? $self_sale_sum/$sale_sum : 0));
		$sheet->getActiveSheet()->getStyle('G' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


		$sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), $offline_sale_all_sum);
		$sheet->getActiveSheet()->getStyle('H' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), $offline_cancel_sale_sum);
		$sheet->getActiveSheet()->getStyle('I' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 3), $offline_return_sale_sum);
		$sheet->getActiveSheet()->getStyle('J' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 3), $offline_sale_sum);
		$sheet->getActiveSheet()->getStyle('K' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 3), ($sale_sum ? $offline_sale_sum/$sale_sum : 0));
		$sheet->getActiveSheet()->getStyle('L' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

		$sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 3), $pos_sale_all_sum);
		$sheet->getActiveSheet()->getStyle('M' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 3), $pos_cancel_sale_sum);
		$sheet->getActiveSheet()->getStyle('N' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 3), $pos_return_sale_sum);
		$sheet->getActiveSheet()->getStyle('O' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 3), $pos_sale_sum);
		$sheet->getActiveSheet()->getStyle('P' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

		$sheet->getActiveSheet()->setCellValue('Q' . ($i + $start + 3), ($sale_sum ? $pos_sale_sum/$sale_sum : 0));
		$sheet->getActiveSheet()->getStyle('Q' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


		$sheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$sheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('G')->setWidth(9);
		$sheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('K')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('L')->setWidth(9);
		$sheet->getActiveSheet()->getColumnDimension('M')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('N')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('O')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('P')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('Q')->setWidth(9);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949",$excel_title.".xls").'"');
		header('Cache-Control: max-age=0');

		

		// $objWriter->setUseInlineCSS(true);
		  $styleArray = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  )
		  );
		$sheet->getActiveSheet()->getStyle('A'.($start+1).':Q'.($i+$start+3))->applyFromArray($styleArray);
		$sheet->getActiveSheet()->getStyle('A'.$start.':Q'.($i+$start+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$sheet->getActiveSheet()->getStyle('A'.$start.':Q'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$sheet->getActiveSheet()->getStyle('A'.($start).':Q'.($start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true);
		$sheet->getActiveSheet()->getStyle('A'.($start+3).':A'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		//$sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getAlignment()->setIndent(1);
		//$sheet->getActiveSheet()->getStyle('A10')->getAlignment()->setIndent(10); 왼쪽 들여쓰기
		$sheet->getActiveSheet()->getStyle('A'.$start.':Q'.($i+$start+3))->getFont()->setSize(10)->setName('돋움');

		$sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움');
		$sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$sheet->getActiveSheet()->getStyle('A'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		unset($styleArray);

		$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
		$objWriter->save('php://output');

		exit; 
	}

	/*
	if($groupbytype != ""){
		$mstring = "<table width='100%' border=0>
							<tr><td>".TitleBar("상품군별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString)."</td><td align=right>(단위:원) </td></tr>
						</table>";
	}
	*/
	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>";

	if($groupbytype !=""){
		if($groupbytype =="goods"){
			$mstring .= "<col width='3%'><col width='3%'>";
		}else{
			$mstring .= "<col width='3%'>";
		}
	}
		$mstring .= "
						<col width='*'>
						<col width='6%'>
						<col width='7%'>
						<col width='6%'>
						<col width='7%'>
						<col width='6%'>
						<col width='7%'>
						<col width='6%'>
						<col width='7%'>
						<col width='6%'>
						<col width='7%'>
						<col width='7%'>
						<col width='6%'>
						<col width='7%'>
						<col width='6%'>
						<col width='7%'>";
	$mstring .= "
		<tr height=30>";
		if($groupbytype != ""){
			if($groupbytype =="day"){
				$mstring .= "<td class=s_td rowspan=2>날짜</td>";
			}else if($groupbytype =="category"){
				$mstring .= "<td class=s_td rowspan=2>카테고리</td>";
			}else if($groupbytype =="goods"){
				$mstring .= "<td class=s_td rowspan=2>상품코드</td>";
				$mstring .= "<td class=s_td rowspan=2>상품명</td>";
			}
			
		}
			$mstring .= "
			<td class=m_td rowspan=2>실매출액</td>
			<td class=m_td colspan=5>자체쇼핑몰</td>
			<td class=m_td colspan=5>오프라인 영업</td>
			<td class=m_td colspan=5>POS</td>

		</tr>
		<tr height=30 align=center>
			<td class=m_td nowrap>전체매출액</td>
			<td class=m_td nowrap>취소매출액</td>
			<td class=m_td nowrap>반품매출액</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td nowrap>점유율</td>
			<td class=m_td nowrap>전체매출액</td>
			<td class=m_td nowrap>취소매출액</td>
			<td class=m_td nowrap>반품매출액</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td nowrap>점유율</td>
			<td class=m_td nowrap>전체매출액</td>
			<td class=m_td nowrap>취소매출액</td>
			<td class=m_td nowrap>반품매출액</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td nowrap>점유율</td>
			</tr>\n";

			/*
			sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (od.pt_dcprice)  else 0 end) as order_sale_sum,
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else 0 end) as cancel_sale_sum,
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
							sum(case when od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else 0 end) as return_sale_sum,
$sql = "Select date_format(od.regdate,'%Y%m%d') as vdate ,
				sum(case when order_from = 'self' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else 0 end) as self_sale_all_sum,
				sum(case when order_from = 'self' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else 0 end) as self_cancel_sale_sum,
				sum(case when order_from = 'self' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else 0 end) as self_return_sale_sum,
				sum(case when order_from = 'self' then (od.pt_dcprice)  else 0 end) as self_sale_sum,
				sum(case when order_from = 'offline' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else 0 end) as offline_sale_all_sum,
				sum(case when order_from = 'offline' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else 0 end) as offline_cancel_sale_sum,
				sum(case when order_from = 'offline' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else 0 end) as offline_return_sale_sum,
				sum(case when order_from = 'offline' then (od.pt_dcprice)  else 0 end) as offline_sale_sum,
				sum(case when order_from = 'pos' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else 0 end) as pos_sale_all_sum,
				sum(case when order_from = 'pos' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else 0 end) as pos_cancel_sale_sum,
				sum(case when order_from = 'pos' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else 0 end) as pos_return_sale_sum,
				sum(case when order_from = 'pos' then (od.pt_dcprice)  else 0 end) as pos_sale_sum,
				sum((od.pt_dcprice) ) as sale_sum
				from  shop_order_detail od
				where date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,6)."%'  AND od.status NOT IN ('".implode("','",$non_sale_status)."')
				group by date_format(od.regdate,'%Y%m%d')
				";
				*/

	for($i=0;$i<$fordb->total;$i++){
		$fordb->fetch($i);

		$_self_sale_sum = $fordb->dt[self_sale_all_sum]-$fordb->dt[self_cancel_sale_sum]-$fordb->dt[self_return_sale_sum];
		$_offline_sale_sum = $fordb->dt[offline_sale_all_sum]-$fordb->dt[offline_cancel_sale_sum]-$fordb->dt[offline_return_sale_sum];
		$_pos_sale_sum = $fordb->dt[pos_sale_all_sum]-$fordb->dt[pos_cancel_sale_sum]-$fordb->dt[pos_return_sale_sum];

		if($groupbytype != ""){
			$week_num = date("w",mktime(0,0,0,substr($fordb->dt[vdate],4,2),substr($fordb->dt[vdate],6,2),substr($fordb->dt[vdate],0,4)));
			
			if($groupbytype == "day"){
				$group_val = getNameOfWeekday($week_num, $fordb->dt[vdate],"priodname");
			}else if($groupbytype == "category"){
				//$group_val = getCategoryPath($fordb->dt[cid],4);
				$group_val = ($fordb->dt[cid] == "" ? "기타" : strip_tags(getCategoryPath($fordb->dt[cid],4)));
			}else if($groupbytype == "goods"){
				$group_val = $fordb->dt[pname];
			}
			$mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>";
			if($groupbytype == "goods"){
			$mstring .= "<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$fordb->dt[pid]." </td>";
			$mstring .= "<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$group_val." </td>";
			}else{
			$mstring .= "<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$group_val." </td>";
			}
			$mstring .= "
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[sale_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[self_sale_all_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[self_cancel_sale_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[self_return_sale_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\"
			onmouseout=\"mouseOnTD('$i',false)\">".number_format($_self_sale_sum,0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] > 0 ? $_self_sale_sum/$fordb->dt[sale_sum]*100 : 0),0)."%</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[offline_sale_all_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[offline_cancel_sale_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[offline_return_sale_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($_offline_sale_sum,0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] > 0 ? $_offline_sale_sum/$fordb->dt[sale_sum]*100 : 0),0)."%</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[pos_sale_all_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[pos_cancel_sale_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[pos_return_sale_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($_pos_sale_sum,0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] > 0 ? $_pos_sale_sum/$fordb->dt[sale_sum]*100 : 0),0)."%</td>
			</tr>\n";
		}

		$sale_sum = $sale_sum + returnZeroValue($fordb->dt[sale_sum]);

		$self_sale_all_sum = $self_sale_all_sum + returnZeroValue($fordb->dt[self_sale_all_sum]);
		$self_cancel_sale_sum = $self_cancel_sale_sum + returnZeroValue($fordb->dt[self_cancel_sale_sum]);
		$self_return_sale_sum = $self_return_sale_sum + returnZeroValue($fordb->dt[self_return_sale_sum]);
		$self_sale_sum += $_self_sale_sum;// + ($fordb->dt[self_sale_all_sum] - $fordb->dt[self_cancel_sale_sum] - $fordb->dt[self_return_sale_sum]);

		$offline_sale_all_sum = $offline_sale_all_sum + returnZeroValue($fordb->dt[offline_sale_all_sum]);
		$offline_cancel_sale_sum = $offline_cancel_sale_sum + returnZeroValue($fordb->dt[offline_cancel_sale_sum]);
		$offline_return_sale_sum = $offline_return_sale_sum + returnZeroValue($fordb->dt[offline_return_sale_sum]);
		$offline_sale_sum += $_offline_sale_sum;// + returnZeroValue($fordb->dt[offline_sale_all_sum] - $fordb->dt[offline_cancel_sale_sum] - $fordb->dt[offline_return_sale_sum]);

		$pos_sale_all_sum = $pos_sale_all_sum + returnZeroValue($fordb->dt[pos_sale_all_sum]);
		$pos_cancel_sale_sum = $pos_cancel_sale_sum + returnZeroValue($fordb->dt[pos_cancel_sale_sum]);
		$pos_return_sale_sum = $pos_return_sale_sum + returnZeroValue($fordb->dt[pos_return_sale_sum]);
		$pos_sale_sum += $_pos_sale_sum;


	}

	if ($sale_sum == 0){
		if($groupbytype == "goods"){
			$mstring .= "<tr  align=center height=200><td colspan=18 class='list_box_td'  >결과값이 없습니다..</td></tr>\n";
		}else if($groupbytype == "category" || $groupbytype == "day"){
			$mstring .= "<tr  align=center height=200><td colspan=17 class='list_box_td'  >결과값이 없습니다..</td></tr>\n";
		}else{
			$mstring .= "<tr  align=center height=200><td colspan=16 class='list_box_td'  >결과값이 없습니다...</td></tr>\n";
		}
	}


	if($groupbytype !=""){
		$mstring .= "<tr height=25 align=right>";
		if($groupbytype == "goods"){
		$mstring .= "<td class=s_td align=center colspan=2>합계</td>";
		}else{
		$mstring .= "<td class=s_td align=center>합계</td>";
		}
		$mstring .= "
		<td class='e_td number' style='padding-right:10px;'>".number_format($sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($self_sale_all_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($self_cancel_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($self_return_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($self_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $self_sale_sum/$sale_sum*100 : 0),0)."%</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($offline_sale_all_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($offline_cancel_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($offline_return_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($offline_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $offline_sale_sum/$sale_sum*100 : 0),0)."%</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($pos_sale_all_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($pos_cancel_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($pos_return_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($pos_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $pos_sale_sum/$sale_sum*100 : 0),0)."%</td>
		</tr>\n";
	}else{
		$mstring .= "<tr height=25 align=right>
		<td class='number point' style='padding-right:10px;'>".number_format($sale_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($self_sale_all_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($self_cancel_sale_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($self_return_sale_sum,0)."</td>
		<td class='number point' style='padding-right:10px;'>".number_format($self_sale_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $self_sale_sum/$sale_sum*100 : 0),0)."%</td>
		<td class='number' style='padding-right:10px;'>".number_format($offline_sale_all_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($offline_cancel_sale_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($offline_return_sale_sum,0)."</td>
		<td class='number point' style='padding-right:10px;'>".number_format($offline_sale_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $offline_sale_sum/$sale_sum*100 : 0),0)."%</td>
		<td class='number' style='padding-right:10px;'>".number_format($pos_sale_all_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($pos_cancel_sale_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format($pos_return_sale_sum,0)."</td>
		<td class='number point' style='padding-right:10px;'>".number_format($pos_sale_sum,0)."</td>
		<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $pos_sale_sum/$sale_sum*100 : 0),0)."%</td>
		</tr>\n";
	}
	$mstring .= "</table>\n";

	if($groupbytype=="day"){
		$mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함</td></tr></table>";

		
		$help_text = "
		<table>
			<tr>
				<td style='line-height:150%'>
				- 매입처별 분석 및 현재 주문데이타가 없는 리포트는 데이타 마이그레이션이 필요합니다.<br>
				- 매출처별 분석 및 현재 주문데이타가 없는 리포트는 데이타 마이그레이션이 필요합니다.<br>
				</td>
			</tr>
		</table>
		";
		//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B' );


		$mstring .= HelpBox("일별매출액(판매처)", $help_text);
	}
	return $mstring;
}



function getCompanyCateSelectBoxOption($depth = 1,$cid){
	$db = new Database;
	return  ;
	$output = "";
	if(!empty($cid)){
		$prefix_cid = substr($cid,0,3);
	}
	$sql = "SELECT
					CATE_ID, CATE_NAME
				FROM
					COMMON_SELLER_CATE
				WHERE
					DEPTH = '".$depth."'
				AND
					DISP = 'Y'";
	if($depth == 2){
		$sql .= " AND CATE_ID LIKE '".$prefix_cid."%'";
	}
	$sql .= " ORDER BY CATE_ID ASC";
	$db->query($sql);
	if($db->total){
		$result = $db->fetchall();

		$output = "";
		foreach($result as $rt):
			if($depth == 1 && (substr($rt["cate_id"],0,3) == $prefix_cid)){
				$output .= "<option value = '".$rt["cate_id"]."' selected>".$rt["cate_name"]."</option>";
			}else if($depth == 2 && $rt["cate_id"] == $cid){
				$output .= "<option value = '".$rt["cate_id"]."' selected>".$rt["cate_name"]."</option>";
			}else{
				$output .= "<option value = '".$rt["cate_id"]."'>".$rt["cate_name"]."</option>";
			}
		endforeach;
	}else{
		$output = null;
	}
	return $output;
}