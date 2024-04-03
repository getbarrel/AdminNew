<?
include_once("../class/layout.class");
include("../order/orders.lib.php");
include("../inventory/inventory.lib.php");

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));
	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");
}

$db1 = new Database;
$odb = new Database;
$ddb = new Database;
$od_db = new MySQL;
$db = new Database;
$sdb = new Database;

if(!$title_str){
	$title_str  = "매출진행관리";
}

$vdate = date("Ymd", time());
$today = date("Ymd", time());
$firstday = date("Ymd", time()-86400*date("w"));
$lastday = date("Ymd", time()+86400*(6-date("w")));

if($max == ""){
	$max = 15; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

// 검색 조건 설정 부분
 if($view_type == 'sc_order'){
	$where = "WHERE od.status != 'SR' AND od.product_type IN (".implode(',',$sns_product_type).") ";
	$folder_name = "sns";
 }elseif($view_type == 'inventory'){
	 $where = "WHERE od.status != 'SR' AND od.stock_use_yn = 'Y' and od.gu_ix !='0' ";
	$folder_name = "product";
 }else{
	$where = "WHERE od.status != 'SR' ";
	$folder_name = "product";
}

if($pre_type == ORDER_STATUS_DELIVERY_COMPLETE || $pre_type == ORDER_STATUS_BUY_FINALIZED){
	if($mode!="search"){
		$orderdate=1;
	}

	if(!$date_type){
		$date_type="o.order_date";
	}
}

if($orderdate){
	//$where .= "and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
	$where .= "and ".$date_type." between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
}

if($view_type != 'inventory'){
	if($invoice_no_bool == "Y"){
		$where .= " and ifnull(od.invoice_no,'') !='' ";
	}elseif($invoice_no_bool == "N"){
		//$where .= " and ifnull(od.invoice_no,'') ='' ";
	}
}



if(empty($type)){
	$type = $fix_type;
}

if(is_array($type)){
	for($i=0;$i < count($type);$i++){
		if($type[$i]){
			if($type_str == ""){
				$type_str .= "'".$type[$i]."'";
			}else{
				$type_str .= ", '".$type[$i]."' ";
			}
		}
	}

	if($type_str != ""){
		$where .= "and od.status in ($type_str) ";
	}
}else{
	if($type){
		$where .= "and od.status = '$type' ";
	}
}

if(is_array($payment_agent_type)){
	for($i=0;$i < count($payment_agent_type);$i++){
		if($payment_agent_type[$i] != ""){
			if($payment_agent_type_str == ""){
				$payment_agent_type_str .= "'".$payment_agent_type[$i]."'";
			}else{
				$payment_agent_type_str .= ", '".$payment_agent_type[$i]."' ";
			}
		}
	}

	if($payment_agent_type_str != ""){
		$where .= "and o.payment_agent_type in ($payment_agent_type_str) ";
	}
}else{
	if($payment_agent_type){
		$where .= "and o.payment_agent_type = '$payment_agent_type' ";
	}
}

if(is_array($order_from)){
	for($i=0;$i < count($order_from);$i++){
		if($order_from[$i] != ""){
			if($order_from_str == ""){
				$order_from_str .= "'".$order_from[$i]."'";
			}else{
				$order_from_str .= ",'".$order_from[$i]."' ";
			}
		}
	}

	if($order_from_str != ""){
		$where .= "and od.order_from in ($order_from_str) ";
	}
}else{
	if($order_from){
		$where .= "and od.order_from = '$order_from' ";
	}
}

if($product_type != ""){
	$where .= "and od.product_type = '".$product_type."'";
}

if($admin_div != ""){
	if($admin_div=='c'){
		$where .= "and od.company_id = '".$admininfo[company_id]."'";
	}else{
		$where .= "and od.company_id != '".$admininfo[company_id]."'";
	}
}

if($pre_type != ORDER_STATUS_DELIVERY_READY){
	if(is_array($delivery_status)){
		for($i=0;$i < count($delivery_status);$i++){
			if($delivery_status[$i] != ""){
				if($delivery_status_str == ""){
					$delivery_status_str .= "'".$delivery_status[$i]."'";
				}else{
					$delivery_status_str .= ", '".$delivery_status[$i]."' ";
				}
			}
		}

		if($delivery_status_str != ""){
			$where .= "and od.delivery_status in ($delivery_status_str) ";
		}
	}else{
		if($delivery_status){
			$where .= "and od.delivery_status = '$delivery_status' ";
		}
	}
}else{
	if($_COOKIE[view_wdr_order] == 1){
		$where .= "and (od.delivery_status not in ('WDA','WDO','WDP','WDC') or od.delivery_status is null or delivery_status ='')";
	}else{
		$where .= "and (od.delivery_status not in ('WDA','WDO','WDP','WDR','WDC') or od.delivery_status is null or delivery_status ='')";
	}
}

if(is_array($p_admin) && count($p_admin) == 1){
	if($p_admin[0]=="A"){
		$where .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
	}elseif($p_admin[0]=="S"){
		$where .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
	}
}else{
	if($p_admin=="A"){
		$where .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
	}elseif($p_admin=="S"){
		$where .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
	}
}

$left_join="";
if(is_array($method)){
	for($i=0;$i < count($method);$i++){
		if($method[$i] != ""){
			if($method_str == ""){
				$method_str .= "'".$method[$i]."'";
			}else{
				$method_str .= ", '".$method[$i]."' ";
			}
		}
	}
	if($method_str != ""){
		$where .= "and op.method in ($method_str) ";
		$left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.method in ($method_str)) ";
	}
}else{
	if($method){
		$where .= "and op.method = '$method' ";
		$left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.method = '$method') ";
	}
}

if($search_type && $search_text){
	if($search_type == "combi_name"){
		$where .= "and (bname LIKE '%".trim($search_text)."%'  or odd.rname LIKE '%".trim($search_text)."%') ";
	}else{
		$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
	}
	if($search_type == "combi_name" || substr_count($search_type,'odd.') > 0){
		$left_join .= " left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix) ";
	}
}

if($pre_type == ORDER_UNRECEIVED_CLAIM){
	$left_join .= " right join shop_order_unreceived_claim uc on (uc.od_ix=od.od_ix) and uc.claim_status = '".ORDER_UNRECEIVED_CLAIM."' ";
}

if($stock_use_yn != ""){
	$where .= "and od.stock_use_yn = '".$stock_use_yn."'";
}


if($is_check_delivery != ""){
	$where .= "and od.is_check_delivery = '".$is_check_delivery."'";
}

if($view_type == 'inventory'){	//주문건수 1건,2건 검색, 현재 사용안함 2014-08-04
	if($order_cnt == '1'){
		$where .= " and (select count(odd.od_ix) as cnt from shop_order_detail as odd where odd.stock_use_yn = 'Y' and odd.oid = od.oid) = 1";
	}else if($order_cnt == '2'){
		$where .= " and (select count(odd.od_ix) as cnt from shop_order_detail as odd where odd.stock_use_yn = 'Y' and odd.oid = od.oid) > 1";
	}
}

if($view_type == 'inventory'){	//부족재고포함된 주문건
	if($stock_out == 'A'){
		$where .= " and od.pcnt > (select sum(ips2.stock) as total_stock from inventory_product_stockinfo as ips2 where ips2.ps_ix != '2' and ips2.gid = od.gid)";
	}
}

if($mall_ix != ""){
	$where .= "and od.mall_ix = '".$mall_ix."'";
}


$Contents = "
<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("$title_str", "배송관리 > $title_str ")."</td>
	</tr>
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<form name='search_frm' method='get' action=''>
			<input type='hidden' name='mode' value='search' />
			<table width=100%  border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주문정보 검색하기</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05'>
									<TABLE cellSpacing=0 cellPadding=3 style='width:100%;' align=center border=0 class='search_table_box'>
											<col width=15%>
											<col width=35%>
											<col width=15%>
											<col width=35%>";
	if($_SESSION["admin_config"][front_multiview] == "Y"){
	$Contents .= "
	<tr>
		<td class='search_box_title' colspan='1'> 프론트 전시 구분</td>
		<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
	</tr>";
	}
	$Contents .= "";

											if($admininfo[admin_level]==9){
						
											$Contents .= "
											<tr height=30>
												<th class='search_box_title' >판매처 선택 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item' nowrap colspan='3'>
													<table cellpadding=0 cellspacing=0 width='100%' border='0' >
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<TR height=25>";

													if($view_type == 'offline_order'){
														$Contents .= "

															<TD><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." checked><label for='order_from_offline'>통합구매</label></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
														";
													}else{
														$Contents .= "
															<TD><input type='checkbox' name='order_from[]' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자체쇼핑몰</label></TD>
															<!--<TD><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." ><label for='order_from_offline'>통합구매</label></TD>
															<TD><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." ><label for='order_from_pos'>POS</label></TD>-->";
														$db1->query("select * from sellertool_site_info where disp='1' ");
														$sell_order_from=$db1->fetchall();
														for($i=0;$i<count($sell_order_from);$i++){
																
																if($i==5 || ($i > 5 && $i%8==5)) $Contents .= "</TR><TR>";

																$Contents .= "<TD><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></TD>";
														}
													}


										$Contents .= "
														</TR>
													</table>
												</td>
											</tr>";
											}
if($pre_type == ORDER_STATUS_DELIVERY_READY||$pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY || $pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_READY||$pre_type ==ORDER_STATUS_INCOM_COMPLETE){
$Contents .= "
											<tr>
												<th class='search_box_title'>처리상태 </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<TR height=30>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_READY,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_DELAY."' value='".ORDER_STATUS_DELIVERY_DELAY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_DELAY,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_DELAY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_DELAY)."</label></TD>
														<TD></TD>
														<TD></TD>
														<TD></TD>
														<TD></TD>
													</TR>
												</TABLE>
												</td>
											</tr>";
}
											$Contents .= "
											<tr height=27>
												<th class='search_box_title''>
												<select name='date_type'>
													<option value='o.order_date' ".CompareReturnValue('o.order_date',$date_type,' selected').">주문일자</option>
													<option value='od.ic_date' ".CompareReturnValue('od.ic_date',$date_type,' selected').">입금일자</option>";
													if($pre_type==ORDER_STATUS_DELIVERY_READY || $pre_type==ORDER_STATUS_INCOM_COMPLETE || $pre_type==ORDER_STATUS_DELIVERY_ING){
														$Contents .= "
														<option value='od.dr_date' ".CompareReturnValue('od.dr_date',$date_type,' selected').">배송준비중일자</option>";
													}
													if($pre_type==ORDER_STATUS_DELIVERY_ING){
														$Contents .= "
														<option value='od.di_date' ".CompareReturnValue('od.di_date',$date_type,' selected').">배송중일자</option>";
													}
												$Contents .= "
												</select>
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td class='search_box_item' colspan=3>
												".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>";
						if($admininfo[admin_level] == 9){

							if($view_type == 'inventory'){
								$Contents .= "
											<tr height=30>
												<th class='search_box_title'>상품관리구분 </th>
												<td class='search_box_item'>
													<input type='checkbox' name='stock_use_yn' id='stock_use_y' value='Y' ".CompareReturnValue("Y",$stock_use_yn,' checked')." ><label for='stock_use_y'>WMS상품</label>&nbsp;
												</td>
												<th class='search_box_title'>부족재고 </th>
												<td class='search_box_item'>
													<input type='checkbox' name='stock_out' id='stock_out_a' value='A' ".($stock_out == 'A' ?'checked':'')." ><label for='stock_out_a'>부족재고</label>
												</td>
											</tr>";
		
							}else{
								$Contents .= "
											<tr height=30>
												<th class='search_box_title'>상품관리구분 </th>
												<td class='search_box_item' colspan='3'>
													<!--<input type='checkbox' name='p_admin[]' id='p_admin_a' value='A' ".CompareReturnValue("A",$p_admin,' checked')." ><label for='p_admin_a'>본사상품</label>&nbsp;
													<input type='checkbox' name='p_admin[]' id='p_admin_s' value='S' ".CompareReturnValue("S",$p_admin,' checked')." ><label for='p_admin_s'>셀러상품</label>&nbsp;-->
													<input type='checkbox' name='stock_use_yn' id='stock_use_y' value='Y' ".CompareReturnValue("Y",$stock_use_yn,' checked')." ><label for='stock_use_y'>WMS상품</label>&nbsp;
												</td>
											</tr>";
							
							}

						}

								if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
									$Contents .= "
											<tr height=30 style='display:none;'>";

											$Contents .= "
                                                <!--                
												<th class='search_box_title'>업체명 </th>
												<td class='search_box_item'>".CompanyList($company_id,"","")."</td>
												-->
												<th class='search_box_title'>담당MD </th>
												<td class='search_box_item' colspan='3'>".MDSelect($md_code)."</td>
												";

											$Contents .= "
												<!--th class='search_box_title' ".(($admininfo[mall_type] == "F" || $admininfo[admin_level] == 8) ? "colspan=3":"").">상품구분 : </th>
												<td class='search_box_item'>
													<select name='product_type'>
														<option value='' ".CompareReturnValue('',$product_type,' selected').">전체보기</option>
														<option value='0' ".CompareReturnValue('0',$product_type,' selected').">국내</option>
														<option value='2' ".CompareReturnValue('2',$product_type,' selected').">선매입</option>
														<option value='1' ".CompareReturnValue('1',$product_type,' selected').">사이트 주문</option>
													</select>
												</td-->
											</tr>";
									}
								$Contents .= "
										<tr height=30>
											<th class='search_box_title'>조건검색 </th>
											<td class='search_box_item'>
												<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<tr>
														<td width='130px'>
														<select name='search_type' style='font-size:12px;'>
															<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자명+수취인명</option>
															<option value='o.bname' ".CompareReturnValue('o.bname',$search_type,' selected').">주문자명</option>
															<option value='o.buserid' ".CompareReturnValue('o.buserid',$search_type,' selected').">주문자ID</option>
															<option value='od.pname' ".CompareReturnValue('od.pname',$search_type,' selected').">상품명</option>
															<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
															<option value='odd.rname' ".CompareReturnValue('odd.rname',$search_type,' selected').">수취인명</option>
															<option value='o.bmobile' ".CompareReturnValue('o.bmobile',$search_type,' selected').">주문자핸드폰</option>
															<option value='odd.rmobile' ".CompareReturnValue('odd.rmobile',$search_type,' selected').">수취인핸드폰</option>
															<option value='od.invoice_no' ".CompareReturnValue('od.invoice_no',$search_type,' selected').">송장번호</option>
															<option value='od.option_text' ".CompareReturnValue('od.option_text',$search_type,' selected').">옵션명</option>
															<option value='od.gid' ".CompareReturnValue('od.gid',$search_type,' selected').">품목코드</option>
															<option value='od.gu_ix' ".CompareReturnValue('od.gu_ix',$search_type,' selected').">품목시스템코드</option>
														</select>
														</td>
														<td width='*'><input type='text' class=textbox name='search_text' size='30' value='$search_text' style='' ></td>
													</tr>
												</table>
											</td>
											<td class='input_box_title'> 목록갯수</td>
											<td class='input_box_item'>
												<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
												<option value='5' ".CompareReturnValue(5,$max).">5</option>
												<option value='10' ".CompareReturnValue(10,$max).">10</option>
												<option value='15' ".CompareReturnValue(15,$max).">15</option>
												<option value='20' ".CompareReturnValue(20,$max).">20</option>
												<option value='50' ".CompareReturnValue(50,$max).">50</option>
												<option value='100' ".CompareReturnValue(100,$max).">100</option>
												<option value='500' ".CompareReturnValue(500,$max).">500</option>
												<option value='1000' ".CompareReturnValue(1000,$max).">1000</option>
												<option value='1500' ".CompareReturnValue(1500,$max).">1500</option>
												<option value='2000' ".CompareReturnValue(2000,$max).">2000</option>
												</select> <span >한페이지에 보여질 갯수를 선택해주세요</span>
											</td>
										</tr>
									</TABLE>
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
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr>
</table>
</form>

<form name=listform method=post action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'><!--target='act'-->
<input type='hidden' name='act' value='select_status_update'>
<input type='hidden' name='pre_type' value='$pre_type'>
<input type='hidden' id='oid' value=''>
<input type='hidden' id='od_ix' value=''>
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
 <tr>";


if($admininfo[admin_level] == 9){
	if($company_id != ""){
		$where .= " and o.oid = od.oid and  od.company_id = '".$company_id."'";
	}else{
		$where .= " and o.oid = od.oid ";
	}

	if($admininfo[mem_type] == "MD"){
		$where .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
	}
}else if($admininfo[admin_level] == 8){
	$where .= " and o.oid = od.oid and od.company_id = '".$admininfo[company_id]."'";
}else{
	$where .= " and o.oid = od.oid ";
}

if($view_type == 'offline_order'){		//영업관리 용도 2013-07-05 이학봉
	$where .= " and od.order_from in ('offline') ";
}



$sql = "SELECT count(distinct od.od_ix) as total
			FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
			$left_join
			$where ";

$db1->query($sql);
$db1->fetch();
$order_goods_total = $db1->dt[total];

if($pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){

	$sub_group_by =" GROUP BY od.gu_ix, od.oid ";

	$sql = "SELECT  od.gu_ix, od.oid, g.gid, gu.unit, g.gname, gu.barcode, g.standard
		FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
		$left_join
		left join inventory_goods_unit gu on gu.gu_ix = od.gu_ix
		left join inventory_goods g on gu.gid = g.gid
		$where
		$sub_group_by ";

	$db1->query($sql);

	$total = $db1->total;

	$sql = "SELECT od.gu_ix, o.oid, od.order_from, g.gid, gu.unit, g.gname, gu.barcode, g.standard, count(*) as group_cnt, sum(od.pcnt) as sum_pcnt
		FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
		$left_join
		left join inventory_goods_unit gu on gu.gu_ix = od.gu_ix
		left join inventory_goods g on gu.gid = g.gid
		$where
		$sub_group_by
		LIMIT $start, $max ";
	$db1->query($sql);

}else{

	$sql = "SELECT distinct o.oid
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$left_join
					$where ";

	$db1->query($sql);

	$total = $db1->total;


	$sql = "SELECT distinct o.oid
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				$left_join
				$where
				ORDER BY o.order_date DESC LIMIT $start, $max";
	$db1->query($sql);

}

 $Contents .= "<td colspan=3 align=left>
						<b>전체 주문수(상품수) : <span class='blue'>".$total."</span> (".$order_goods_total.") 건</b>
						<!--input type='checkbox' name='view_wdr_order' id='view_wdr_order' onclick=\"ToggleOrder('WDR')\" ".($_COOKIE[view_wdr_order] == 1 ? "checked":"")." >
						<label for='view_wdr_order'> 출고대기포함</label--><!--임시 주석처리-->
					</td>
				<td colspan=5 align=right>";

if($pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM || $pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY || $pre_type == ORDER_STATUS_INCOM_COMPLETE){

	//배송방법을 바꾸게 되면 이슈(정산및 배송비)가 있어서 일단 주석처리!
	$Contents .="<select id='order_select_type' ><option value='s'>선택한 주문만</option><option value='l'>전체 주문을</option></select> <!-- ".DeliveryMethod("","","id='order_select_delivery_method'","select")." --> ".deliveryCompanyList2("","id='order_select_delivery_company'",$_SESSION[admininfo][company_id])." <img src='../images/btn/btn_order_relation01.gif' alt='일괄 변경' onclick=\"order_select_delivery()\" align='absmiddle' style='cursor:pointer;' /> ";

	$Contents .="<script type='text/javascript'>
	<!--
	function order_select_delivery (){
		//var m_val = $('#order_select_delivery_method').val();
		var c_val = $('#order_select_delivery_company').val();
		if( $('#order_select_type').val()=='s'){
			$('input[name^=od_ix]:checked').each(function(){
				//$('[name^=delivery_method][name*='+$(this).val()+']').val(m_val);
				$('[name^=quick][name*='+$(this).val()+']').val(c_val);
			})
		}else{
			//$('[name^=delivery_method]').val(m_val);
			$('[name^=quick]').val(c_val);
		}
	}

	//-->
	</script>";
}

if($admininfo[admin_level] != 9){
	$Contents .= "<span style='color:red'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
}

	$Contents .= ($is_demandship == "Y" ? "<input type='button' value='디멘드쉽 라벨생성' id=\"submitMakeLabel\" />" : "") . "
	<script>
		$(document).ready(function(){
			$(\"#submitMakeLabel\").on(\"click\", function(){
				var checked_bool = false;
				var chk_od_ix = '';
				var frm = document.listform;

				for(i=0;i < frm.od_ix.length;i++){
					if(frm.od_ix[i].checked){
						checked_bool = true;
						chk_od_ix += frm.od_ix[i].value + ',';
					}
				}

				if(!checked_bool){
					alert('상품을 선택해주세요.');
				}else{
					//alert(chk_od_ix);
					$.ajax({
						type: 'GET',
						data: {'act': 'make_label', 'od_ix': chk_od_ix},
						url: '/admin/openapi/demandship/demandship_make_label.act.php',
						dataType: 'json', 
						async: true, 
						beforeSend: function(){
							$('#loading_img').html(\"<img src='/admin/images/loading_large.gif' border='0'  align='absmiddle'>\");
						},
						success: function(data){
							window.open('/admin/openapi/demandship/labels/' + data.fileName, '');
//							console.log(data);
//							if(data.length > 0){
//								window.open();
//								alert(data);
//							}
						},
						complete: function(){
							$('#loading_img').hide();
						},
						error:function(x, o, e){
							alert(x.status + \" : \"+ o +\" : \"+e);
						}
					});
				}
			});

			$(\"#submitViewLabel\").on(\"click\", function(){
				var checked_bool = false;
				var chk_od_ix = '';
				var frm = document.listform;

				for(i=0;i < frm.od_ix.length;i++){
					if(frm.od_ix[i].checked){
						checked_bool = true;
						chk_od_ix += frm.od_ix[i].value + ',';
					}
				}

				if(!checked_bool){
					alert('상품을 선택해주세요.');
				}else{
					window.open(\"/admin/openapi/demandship/demandship_view_label.php?od_ix=\" + chk_od_ix, \"demandship_view_label\", \"width=800, height=800, top=0, left=0\");
				}
			});
		});

		function makeLabel(od_ix_val){
			var checked_bool = false;
			var chk_od_ix = od_ix_val;

			if(!od_ix_val){
				alert('상품을 선택해주세요.');
			}else{
				//alert(chk_od_ix);
				$.ajax({
					type: 'GET',
					data: {'act': 'make_label', 'od_ix': chk_od_ix},
					url: '/admin/openapi/demandship/demandship_make_label.act.php',
					dataType: 'html', 
					async: true, 
					beforeSend: function(){
						$('#loading_img').html(\"<img src='/admin/images/loading_large.gif' border='0'  align='absmiddle'>\");
					},
					success: function(data){
						if(data.length > 0){
							alert(data);
						}
					},
					complete: function(){
						$('#loading_img').hide();
					},
					error:function(x, o, e){
						alert(x.status + \" : \"+ o +\" : \"+e);
					}
				});
			}
		}

	</script>
	";

//if($pre_type == ORDER_STATUS_DELIVERY_READY){

	/*
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$Contents .= "<a href='excel_out.php?excel_type=delivery&pre_type=$pre_type' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a> ";
	}else{
		$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a> ";
	}
	*/
	

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		//$Contents .= "<a href='orders_excel2003.php?excel_type=delivery&pre_type=$pre_type&invoice_no_bool=$invoice_no_bool&".$type_param."&".$delivery_type_param."&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle'></a>";
		
		$Contents .= orderExcelTemplateSelect("O");

		$Contents .= "<span class='helpcloud' help_height='30' help_html='주문정보를 엑셀로 다운로드 하실 수 있습니다..'>
		<img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle' style='cursor:pointer' onclick=\"if(jQuery('#oet_ix').val().length > 0){location.href='../order/orders_excel2003.php?oet_ix='+jQuery('#oet_ix').val()+'&excel_type=delivery&view_type=$view_type&pre_type=$pre_type&invoice_no_bool=$invoice_no_bool&stock_use_yn=$stock_use_yn&is_check_delivery=$is_check_delivery&".$type_param."&".$delivery_type_param."&".$QUERY_STRING."'}else{alert('엑셀양식을선택해주세요.');}\" ></span>";

	}else{
		$Contents .= "
		<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle'></a>";
	}
//}


//invoice_no_bool 조건을 다시줄 필요 있음
if(($pre_type == ORDER_STATUS_DELIVERY_ING && $invoice_no_bool=="Y")||$pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY && $is_check_delivery!="Y"){//송장입력완료페이지 //ShowModalWindow
	$Contents .= " <a href='../order/delivery_barcode_invoice.pop.php' rel='facebox' ><img src='../images/btn/btn_order_relation02.gif' alt='배송확정검수' align='absmiddle' style='cursor:pointer;' /></a>";
}elseif($pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING){
	$Contents .= " <a href='../order/picking_barcode_invoice.pop.php' rel='facebox' ><img src='../images/btn/btn_order_relation03.gif' alt='포장검수' align='absmiddle' style='cursor:pointer;' /></a>";
}

$Contents .= "
  </td>
  </tr>
  </table>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='30' >";
		$Contents .= "
		<td class='s_td' width='30px' rowspan='2'><input type=checkbox  name='all_fix2' onclick='fixAll2(document.listform)'></td>
		<td width='12%' align='center' class='m_td'><font color='#000000' ><b>주문일자/주문번호</b></font></td>";

if($pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){
		$Contents .= "
		<td width='20%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>품목명/규격(옵션)</b></font></td>
		<td width='7%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>품목코드/단위</b></font></td>
		<td width='4%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>관리</b></font></td>
		<td width='6%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>주문상세번호</b></font></td>
		<td width='5%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>주문수량</b></font></td>
		<td width='*' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>출고창고<br/>(현재고) 업체명 > 창고명 > 보관장소</b></font></td>";
}else{
		$Contents .= "
		<td width='*' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>주문상세번호/상품명/옵션</b></font></td>
		<td width='7%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>결제금액(수량)</b></font></td>
		<td width='7%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>배송비</b></font></td>";

}

if($pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM || $pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
	$Contents .= "
	<td width='10%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000'><b>품목코드/품목명<br/>/규격(옵션)</b></font></td>
	<td width='10%' align='center' class='m_td' nowrap><font color='#000000'><b>사업장/창고</b></font></td>";
}

if($pre_type != ORDER_STATUS_WAREHOUSE_DELIVERY_READY && $pre_type != ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE && $pre_type != ORDER_STATUS_BUY_FINALIZED && substr($pre_type,0,3)!="WMS"){
		$Contents .= "<td width='6%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>재고<br/>/진행<br/>/부족</b></font></td>";
}

if($pre_type == ORDER_STATUS_DELIVERY_READY){
	$Contents .= "
		<td width='8%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' class=small><b>처리상태</b></font></td>
		<td width='8%' align='center' class='e_td' rowspan='2' nowrap><font color='#000000' class=small><b>발송예정일</b></font></td>";
}elseif($pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM || $pre_type =="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY||$pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE||$pre_type ==ORDER_STATUS_INCOM_COMPLETE||$pre_type == ORDER_STATUS_DELIVERY_ING){
	$Contents .= "
		<td width='8%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' class=small><b >처리상태</b></font></td>";
	if($admininfo[admin_level]==9){
		$Contents .= "
		<td width='8%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' class=small><b >출고처리상태</b></font></td>";
	}
	$Contents .= "<td width='10%' align='center' class='m_td' nowrap><font color='#000000' ><b>배송타입/택배사</b></font></td>";
}elseif($pre_type == ORDER_STATUS_DELIVERY_COMPLETE || $pre_type == ORDER_STATUS_BUY_FINALIZED){
	$Contents .= "
		<td width='6%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' class=small><b >처리상태</b></font></td>";
	if($admininfo[admin_level]==9){
		$Contents .= "
		<td width='6%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' class=small><b >출고처리상태</b></font></td>";
	}

	$Contents .= "
		<td width='10%' align='center' class='m_td' nowrap><font color='#000000' ><b>배송타입/택배사</b></font></td>
		<td width='6%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' ><b>정산처리상태</b></font></td>";

	if($pre_type == ORDER_STATUS_BUY_FINALIZED){
		$Contents .= "
		<td width='6%' align='center' class='m_td' rowspan='2' ><font color='#000000' ><b>리뷰작성</b></font></td>";
	}
}else if($pre_type == ORDER_UNRECEIVED_CLAIM){
	$Contents .= "
	<td width='6%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' class=small><b >처리상태</b></font></td>
	<td width='16%' align='center' class='e_td' rowspan='2' nowrap><font color='#000000' class=small><b >미수령신고메시지</b></font></td>";
}else{
	$Contents .= "
		<td width='6%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000' class=small><b >처리상태</b></font></td>";
	if($admininfo[admin_level]==9){
		$Contents .= "
		<td width='6%' align='center' class='e_td' rowspan='2' nowrap><font color='#000000' class=small><b >출고처리상태</b></font></td>";
	}
}

$Contents .= "
	</tr>";

if($pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE||$pre_type ==ORDER_STATUS_INCOM_COMPLETE||$pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE||$pre_type == ORDER_STATUS_BUY_FINALIZED){
	$Contents .= "
	<tr height='30' >
		<td align='center' class='m_td' ><font color='#000000' ><b>주문자명/수취인</b></font></td>
		<td align='center' class='m_td' ><font color='#000000' ><b>송장번호/위치추적</b></font></td>
	</tr>";

}elseif($pre_type =="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM||$pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
	$Contents .= "
	<tr height='30' >
		<td align='center' class='m_td'><font color='#000000' ><b>주문자명/수취인</b></font></td>
		<td align='center' class='m_td'><font color='#000000' ><b>보관장소/현재고</b></font></td>
		<td align='center' class='m_td'><font color='#000000' ><b>송장번호/위치추적</b></font></td>
	</tr>";
}else{
	$Contents .= "
	<tr height='30' >
		<td align='center' class='m_td' ><font color='#000000' ><b>주문자명/수취인</b></font></td>
	</tr>";
}


if($db1->total){

	$addWhere = " AND od.status !='SR' ";

	if(is_array($type)){
		if($type_str != ""){
			$addWhere .= "and od.status in ($type_str) ";
		}
	}else{
		if($type){
			$addWhere .= "and od.status = '$type' ";
		}
	}
	
	if($view_type != 'inventory'){
		if($invoice_no_bool == "Y"){
			$addWhere .= " and ifnull(od.invoice_no,'') !='' ";
		}elseif($invoice_no_bool == "N"){
			//$addWhere .= " and ifnull(od.invoice_no,'') ='' ";
		}
	}

	if($pre_type != ORDER_STATUS_DELIVERY_READY){
		if(is_array($delivery_status)){
			if($delivery_status_str != ""){
				$addWhere .= "and od.delivery_status in ($delivery_status_str) ";
			}
		}else{
			if($delivery_status){
				$addWhere .= "and od.delivery_status = '$delivery_status' ";
			}
		}
	}else{
		if($_COOKIE[view_wdr_order] == 1){
			$addWhere .= "and (od.delivery_status not in ('WDA','WDC') or od.delivery_status is null) ";
		}else{
			$addWhere .= "and (od.delivery_status not in ('WDA','WDR','WDC') or od.delivery_status is null) ";
		}
	}

	if($admininfo[admin_level] == 9){
		if($admininfo[mem_type] == "MD"){
			$addWhere .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}
	}else if($admininfo[admin_level] == 8){
		$addWhere .= " and od.company_id ='".$admininfo[company_id]."'  ";
	}

	if(is_array($p_admin) && count($p_admin) == 1){
		if($p_admin[0]=="A"){
			$addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
		}elseif($p_admin[0]=="S"){
			$addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
		}
	}else{
		if($p_admin=="A"){
			$addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
		}elseif($p_admin=="S"){
			$addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
		}
	}

	if($stock_use_yn != ""){
		$addWhere .= "and od.stock_use_yn = '".$stock_use_yn."'";
	}

	for ($i = 0; $i < $db1->total; $i++)
	{
		$db1->fetch($i);

		if($pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){
			$sub_where=" and od.gu_ix = '".$db1->dt[gu_ix]."' ";
		}elseif(substr($pre_type,0,4)=="WMS_"){
			$sub_left_join ="
			left join inventory_goods_unit gu on (gu.gu_ix=od.gu_ix)
			left join inventory_goods g on (g.gid=gu.gid)
			left join common_company_detail ccd on (ccd.company_id = od.delivery_company_id)
			left join inventory_place_info pi  on (pi.pi_ix = od.delivery_pi_ix)
			left join inventory_place_section ps on (ps.ps_ix = ".($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY ? "od.delivery_basic_ps_ix" : "od.delivery_ps_ix").")
			left join inventory_product_stockinfo ips on (ips.gid=g.gid and ips.unit=gu.unit and ips.company_id=od.delivery_company_id and ips.pi_ix=od.delivery_pi_ix and ips.ps_ix=ps.ps_ix)
			";

			$sub_select ="
			ips.stock, ccd.com_name as inventory_com_name, pi.place_name, ps.section_name, gu.gid, gu.unit, g.gname, g.standard, ";
		}else{
			$sub_left_join ="
			left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option_id=pod.id)
			left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid)
			left join inventory_goods_unit gu on (gu.gu_ix=od.gu_ix) ";

			$sub_select ="
			(case when od.stock_use_yn ='Y' then
				(select sum(stock) from inventory_product_stockinfo ps where ps.gid=gu.gid and ps.unit=gu.unit)
			else
				(case when (od.stock_use_yn ='Y' and (od.option_kind = 'x2' or od.option_kind = 'b' or od.option_kind = 'x' or od.option_kind = 's2' or od.option_kind = 'c')) or od.order_from != 'self' then pod.option_stock else p.stock end)
			end) as stock,
			
			(case when od.stock_use_yn ='Y' then
				gu.sell_ing_cnt
			else
				(case when (od.stock_use_yn ='Y' and (od.option_kind = 'x2' or od.option_kind = 'b' or od.option_kind = 'x' or od.option_kind = 's2' or od.option_kind = 'c')) or od.order_from != 'self' then pod.option_sell_ing_cnt else p.sell_ing_cnt end)
			end) as sell_ing_cnt, ";
		}



		$sql = "SELECT o.oid, o.delivery_box_no, o.payment_price, o.payment_agent_type, o.user_code as user_id, o.buserid, o.bmobile, o.bname,
		 o.mem_group, o.status as ostatus, o.order_date as regdate, o.total_price,

		od.od_ix, od.product_type, od.pid, od.pname, od.set_group, od.set_name, od.sub_pname, od.option_text, od.coprice,od.listprice,od.psprice, od.pcnt, od.ptprice, od.pt_dcprice,
		od.commission, od.delivery_status, od.stock_use_yn, od.order_from, od.pcode, od.admin_message, od.status, od.delivery_method, od.quick, od.invoice_no, od.option_kind,
		od.company_name, od.company_id, od.reserve, od.co_pid, date_format(od.ic_date,'%Y-%m-%d') as incom_date, od.gid, od.due_date, od.ic_date, od.dr_date,od.co_oid,od.co_od_ix,
		od.real_lack_stock,

		od.delivery_type,od.delivery_policy,od.delivery_package,od.delivery_method,od.delivery_pay_method,od.ori_company_id,od.delivery_addr_use,od.factory_info_addr_ix,od.dps_status,od.ode_ix,

		(select IFNULL(delivery_dcprice,'0') as delivery_dcprice
			from
				shop_order_delivery
			where
				ode_ix=od.ode_ix
		) as delivery_totalprice,

		(select regdate from shop_order_status where oid=od.oid and (case when od.status='IC' then status='IC' else (od_ix=od.od_ix and status=od.status) end) order by regdate desc limit 1) as status_regdate,
		(select regdate from shop_order_status where oid=od.oid and od_ix=od.od_ix and status=od.delivery_status order by regdate desc limit 1) as delivery_status_regdate,

		".$sub_select."
		(case when od.account_type='3' or od.refund_status='FC' then '0' else (case when od.account_type in ('1','') then (od.ptprice -(od.ptprice*(od.commission)/100)) else ((od.coprice*od.pcnt)-(od.coprice*od.pcnt*(od.commission)/100)) end) end) as expect_ac_price

		FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
		".$sub_left_join."
		where o.oid = od.oid
		and o.oid = '".$db1->dt[oid]."'
		".$sub_where."
		".$addWhere."
		ORDER BY o.oid desc, od.ode_ix ASC, od.pid ASC, od.set_group asc";

		$ddb->query($sql);

		$od_count = $ddb->total;

		$b_oid = '';
		$bcompany_id = '';

		for($j=0;$j < $ddb->total;$j++){
			$ddb->fetch($j);

			$delivery_pay_type = getDeliveryPayType($ddb->dt[delivery_pay_method]);

			$one_status = getOrderStatus($ddb->dt[status]).($ddb->dt[admin_message]!="" ? "<br><b class='grn'>".$ddb->dt[admin_message]."</b>":"")."<br>".str_replace(' ','<br/>',$ddb->dt[status_regdate]);
			if($pre_type == ORDER_STATUS_DELIVERY_READY||$pre_type == ORDER_STATUS_INCOM_COMPLETE){
				$dr_ic_day=round((strtotime($ddb->dt[dr_date])-strtotime($ddb->dt[ic_date]))/60/60/24,1);
				$one_status .= "<br/><span class='red'>(".($dr_ic_day > 0? $dr_ic_day : "0")."일)</span>";
			}

			if($ddb->dt[is_erp_link] == "Y"){
				$is_erp_link = '반영';
			}else{
				$is_erp_link = '미반영';
			}

			if($ddb->dt[is_erp_link_return] == "Y"){
				$is_erp_link_return = '반영';
			}else{
				$is_erp_link_return = '미반영';
			}

			if($ddb->dt[erp_link_date] != "0000-00-00 00:00:00"){
				$erp_link_date = $ddb->dt[erp_link_date];
			}

			$Contents .= "<tr height=28 >";

				$Contents .= "<td class='list_box_td' nowrap align='center'><input type=checkbox name='od_ix[]' oid='".$ddb->dt[oid]."' set_group='".$ddb->dt[set_group]."' id='od_ix' value='".$ddb->dt[od_ix]."' ></td>";

			if($ddb->dt[oid] != $b_oid){

				$u_etc_info=get_order_user_info($ddb->dt[user_id]);

				$b_mem_info = "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='주문자 <br/>ID/성명 : ".$ddb->dt[buserid]."/".$ddb->dt[bname]."<br/>핸드폰 : ".$ddb->dt[bmobile]." <br/>회원그룹 : ".$ddb->dt[mem_group]." <br/>최근로그인 : ".$u_etc_info["user_last"]." <br/>최근주문(30일) : ".$u_etc_info["user_order_cnt"]."건' />".Black_list_check($ddb->dt[user_id],$ddb->dt[bname].( $ddb->dt[buserid] ? "(<span class='small'>".$ddb->dt[buserid]."</span>)" : "(<span class='small'>비회원</span>)"))."</b> <br/> ".($_SESSION["admininfo"]["admin_level"] > 8 && $ddb->dt[user_id] ? "<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$ddb->dt[user_id]."&mmode=pop',1280,800,'member_view')\"  style='cursor:pointer;'>" : "");

				$recipient_info=getOrderRecipientInfo($ddb->dt);
				$recipient_=$recipient_info["recipient"];
				$recipient_str=$recipient_info["recipient_str"];
				$recipient_width=$recipient_info["recipient_width"];
				$recipient_height=$recipient_info["recipient_height"];

				$r_mem_info= "<b style='cursor:pointer' class='helpcloud' help_width='".$recipient_width."' help_height='".$recipient_height."' help_html='".$recipient_str."' />".$recipient_."</b>";

				$Contents .= "<td class='list_box_td' style='line-height:140%' align=center rowspan='".$od_count."' nowrap>
								".($admininfo[admin_level]==9 ? "<b class='red'>".getOrderFromName($ddb->dt[order_from])."</b><br/>" : "")."
								".$ddb->dt[regdate]."<br>
								<font color='blue' class=small><b>
									<span style='color:#007DB7;font-weight:bold;' class='small'>".$ddb->dt[oid]."</span></b>".($ddb->dt[delivery_box_no] ? "<b style='color:red;'>-".$ddb->dt[delivery_box_no]."</b>":"")."
								</font><br>
								<span class='helpcloud' help_width='55' help_height='15' help_html='주문서'>
									<img src='../images/icon/paper.gif' style='cursor:pointer' align='absmiddle' onclick=\"PopSWindow('../order/orders.read.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."&mmode=pop',960,600,'order_read')\"/>
									<a href=\"javascript:PopSWindow('../order/orders.edit.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."&mmode=personalization&mem_ix=".$mem_ix."',960,600,'order_edit');\" ><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a>
								</span>
							
								".($ddb->dt[is_erp_link] == 'Y' ? "<br/>ERP매출 : ".$is_erp_link : "")."
								".($ddb->dt[is_erp_link_return] == 'Y' ? "<br/>ERP반품 : ".$is_erp_link_return : "")."
								".($ddb->dt[is_erp_link] == 'Y' ? "<br/>".$erp_link_date."<br>" : "")."
								<br/>
								".$b_mem_info." / ".$r_mem_info."
							  </td>";
			}

				if($pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){

					if($b_oid != $db1->dt[oid] || $bgu_ix != $db1->dt[gu_ix]){
						/*
						$sql = "SELECT ips.*, ccd.com_name as inventory_com_name, pi.place_name, ps.section_name FROM
							inventory_goods_unit gu
							left join inventory_product_stockinfo ips on (ips.gid=gu.gid and ips.unit=gu.unit)
							left join common_company_detail ccd on (ccd.company_id = ips.company_id)
							left join inventory_place_info pi  on (pi.pi_ix = ips.pi_ix)
							left join inventory_place_section ps on (ps.ps_ix = ips.ps_ix)
						where gu.gid='".$db1->dt[gid]."' and gu.unit='".$db1->dt[unit]."'
						order by ips.stock desc ";
						*/
						$sql = "SELECT ips.*, ccd.com_name as inventory_com_name, pi.place_name, ps.section_name FROM
							inventory_product_stockinfo ips
							left join common_company_detail ccd on (ccd.company_id = ips.company_id)
							left join inventory_place_info pi  on (pi.pi_ix = ips.pi_ix)
							left join inventory_place_section ps on (ps.ps_ix = ips.ps_ix)
						where ips.gid='".$db1->dt[gid]."' and ips.unit='".$db1->dt[unit]."'
						order by pi.exit_order asc, ips.stock desc ";
						$od_db->query($sql);
						$stock_info = $od_db->fetchall("object");
						$frist_stock = $stock_info[0]["stock"];

						$Contents .= "
						<td class='list_box_td' style='text-align:left;padding-left:10px' rowspan='".$db1->dt[group_cnt]."'>".$db1->dt[gname]." ".($db1->dt[standard]!="" ? "<br/> ▶".$db1->dt[standard] : "")."</td>
						<td align='center' class='list_box_td' rowspan='".$db1->dt[group_cnt]."'>".$db1->dt[gid]."<br/>(".getUnit($db1->dt[unit], "basic_unit","","text").")</td>";
					}

					$Contents .= "
					<td align='center' class='list_box_td'>
						".($ddb->dt[pcnt] > 1 && $db1->dt[sum_pcnt] > $frist_stock ? "<img src='../images/".$admininfo["language"]."/btn_order_split.gif' align=absmiddle onclick=\"PoPWindow3('../order/order_detail_separation.php?mmode=pop&od_ix=".$ddb->dt[od_ix]."',1000,600,'order_detail_separation')\" style='cursor:pointer;' />" : "")."
					</td>
					<td align='center' class='list_box_td'><span style='color:#007DB7;font-weight:bold;' class='small'>".$ddb->dt[od_ix]."</span></td>
					<td align='center' class='list_box_td'>
						<span style='color:#007DB7;font-weight:bold;' class='small'>".$ddb->dt[pcnt]."</span>";
		if($ddb->dt[pcnt] > GetGoodsStock($ddb->dt[gid])){	//출고요청
			$Contents .="<br><br>&nbsp;  &nbsp; <img src='../images/icon/alarm_danger.gif'>";
		}
	
$Contents .= "
					</td>
					<td align='center' class='list_box_td'>
						<select name='sub_delivery_info[".$ddb->dt[od_ix]."]'>";
							if(is_array($stock_info)){
								foreach($stock_info as $si){
									$Contents .= "
									<option value='".$si["company_id"]."|".$si["pi_ix"]."|".$si["ps_ix"]."'>(".number_format($si["stock"]).") ".$si["inventory_com_name"]." > ".$si["place_name"]." > ".$si["section_name"]."</option>";
								}
							}else{
								//$Contents .= "
								//<option value=''>재고가 없습니다.</option>";
								$sql = "SELECT ccd.company_id, ccd.com_name as inventory_com_name, pi.pi_ix as pi_ix, pi.place_name, ps.ps_ix, ps.section_name FROM
									common_company_detail ccd
									left join inventory_place_info pi  on (ccd.company_id = pi.company_id)
									left join inventory_place_section ps on (pi.pi_ix = ps.pi_ix and ps.section_type='S')
								where ccd.company_id='".$_SESSION["admininfo"]["company_id"]."' ";
								$od_db->query($sql);
								$stock_info = $od_db->fetchall("object");
								
								foreach($stock_info as $si){
									$Contents .= "
									<option value='".$si["company_id"]."|".$si["pi_ix"]."|".$si["ps_ix"]."'>(".number_format($si["stock"]).") ".$si["inventory_com_name"]." > ".$si["place_name"]." > ".$si["section_name"]."</option>";
								}

							}
						$Contents .= "
						</select>
					</td>";
				}else{
						$Contents .= "
						<td class='list_box_td' style='padding-left:10px'>
							<TABLE>
								<TR>
									<TD align='center'>
									<a  href='/shop/goods_view.php?id=".$ddb->dt[pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], "m", $ddb->dt)."'  width=50 style='margin:5px;'></a><br/>";

						if($ddb->dt[product_type]=='21'||$ddb->dt[product_type]=='31'){
						$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($ddb->dt[product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$ddb->dt[product_type].".gif' align='absmiddle' ></label> ";
						}
						if($ddb->dt[company_id]==$HEAD_OFFICE_CODE){
							$Contents .= "<label class='helpcloud' help_width='70' help_height='15' help_html='본사상품'><img src='../images/".$admininfo[language]."/s_admin_product.gif' align='absmiddle' ></label> ";
						}
						if($ddb->dt[stock_use_yn]=='Y'){
						$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
						}

						$Contents .= "
									</TD>
									<td width='5'></td>
									<TD style='line-height:140%;text-align:left;'>";

						$Contents .= "<span style='color:#007DB7;font-weight:bold;' class='small'>".$ddb->dt[od_ix]." ".($ddb->dt[co_oid] ? "(".$ddb->dt[co_oid].")" : "")."</span><br/>";

						if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
							if($bcompany_id != $ddb->dt[company_id]){
								$seller_info_str= GET_SELLER_INFO($ddb->dt[company_id]);
							}

							$Contents .= "<b style='cursor:pointer' class='helpcloud' help_width='230' help_html='".$seller_info_str."'>".($ddb->dt[company_name] ? $ddb->dt[company_name]:"-")."</b> <img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow('../seller/seller_company.php?company_id=".$ddb->dt[company_id]."&mmode=pop',960,600,'brand');\"  style='cursor:pointer;'><br/>";
						}
						
						$Contents .= "<a  href='../".$folder_name."/goods_input.php?id=".$ddb->dt[pid]."' target=_blank />";

						if($ddb->dt[product_type]=='99'||$ddb->dt[product_type]=='21'||$ddb->dt[product_type]=='31'){
							$Contents .= "<b class='".($ddb->dt[product_type]=='99' ? "red" : "blue")."' >".$ddb->dt[pname]."</b><br/><strong>".$ddb->dt[set_name]."<br /></strong>".$ddb->dt[sub_pname];
						}else{
							$Contents .= $ddb->dt[pname];
						}
						
						$Contents .= "</a>";

						if($ddb->dt[gid]){
							$Contents .= " &nbsp; / &nbsp;&nbsp;<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$ddb->dt[gid]."',1100,900,'inventory_goods_info')\"><b class='grn'>".$ddb->dt[gid]."</b></a>";
						}

						if(strip_tags($ddb->dt[option_text])){
							$Contents .= "<br/> ▶ ".strip_tags($ddb->dt[option_text]);
						}

						$Contents .="
									</TD>
								</TR>
							</TABLE>
						</td>
						<td class='list_box_td' align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ddb->dt[pt_dcprice])."".$currency_display[$admin_config["currency_unit"]]["back"]." (".number_format($ddb->dt[pcnt])."개)</td>";

						//배송비 분리 시작 2014-05-21 이학봉
                        if($b_ode_ix != $ddb->dt[ode_ix]){

                            $sql = "SELECT
                                        COUNT(DISTINCT(od.od_ix)) AS com_cnt
                                    FROM
                                        ".TBL_SHOP_ORDER." o,
                                        ".TBL_SHOP_ORDER_DETAIL." od
                                    where
                                        o.oid = od.oid
                                        and o.oid = '".$ddb->dt[oid]."' 
                                        and od.ode_ix='".$ddb->dt[ode_ix]."'
                                        $addWhere
                                        ";

                            $od_db->query($sql);//$od_db는 상단에서 선언
                            $od_db->fetch();
                            $com_cnt=$od_db->dt["com_cnt"];

                            $Contents .="<td class='' align=center style='line-height:140%;' rowspan='".$com_cnt."'>
                                    ".number_format($ddb->dt[delivery_totalprice])."원
                                    </td>";
                        }
						//배송비 분리 끝 2014-05-21 이학봉

						if(substr($pre_type,0,4) == "WMS_"){
							$Contents .="
							<td class='list_box_td' style='text-align:left;padding-left:10px'>[".$ddb->dt[gid]."] ".getUnit($ddb->dt[unit], "basic_unit","","text")."<br/>".$ddb->dt[gname]." ".($ddb->dt[standard]!="" ? "<br/> ▶".$ddb->dt[standard] : "")."</td>
							<td align='left' style='padding-left:5px;'>
								".$ddb->dt[inventory_com_name]."<br/>
								&nbsp; > ".$ddb->dt[place_name]."<br/>
								&nbsp; > ".$ddb->dt[section_name]."<br/>
								&nbsp; > ".number_format($ddb->dt[stock]);
				
				if($ddb->dt[stock] <= 0){
	$Contents .="<br><br>&nbsp; &nbsp; &nbsp; &nbsp; <img src='../images/icon/alarm_danger.gif'>";
				
				}
$Contents .="
							</td>";
						}

						if($pre_type != ORDER_STATUS_WAREHOUSE_DELIVERY_READY && $pre_type != ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE && $pre_type != ORDER_STATUS_BUY_FINALIZED && substr($pre_type,0,3)!="WMS"){

							$Contents .="<td class='list_box_td ' align=center>
									".number_format($ddb->dt[stock])."<br/>/-".number_format($ddb->dt[sell_ing_cnt])."<br/>/".
										($ddb->dt[stock]-$ddb->dt[sell_ing_cnt] < 0 ? "<b class='red'>".number_format($ddb->dt[stock]-$ddb->dt[sell_ing_cnt])."</b>" : "-0");
			
							if($ddb->dt[stock_use_yn]=='Y'){
								$Contents .="<br/>";
								
								if($ddb->dt[real_lack_stock] < 0){
									$Contents .="<b class='red'>".$ddb->dt[real_lack_stock]."</b> <img src='../images/icon/alarm_danger.gif' align='absmiddle'>";
								}else{
									$Contents .="<b class='grn'>".$ddb->dt[real_lack_stock]."</b>";
								}
							}

							/*
							if($ddb->dt[stock] - $ddb->dt[sell_ing_cnt] <= '0'){

								$Contents .="<br><br> &nbsp; &nbsp;<img src='../images/icon/alarm_danger.gif'>";
							}else{
								//echo $ddb->dt[stock]." == ".$ddb->dt[sell_ing_cnt]."<br>";
							}
							*/

							$Contents .="
							</td>";
						}
				}

				$Contents .="<td class='list_box_td point' align='center'>".$one_status."</td>";

				if($admininfo[admin_level]==9 && $pre_type != ORDER_STATUS_DELIVERY_READY){
					if($pre_type==ORDER_UNRECEIVED_CLAIM){
						$Contents .="<td class='list_box_td blue_point' align='center' nowrap><b>".unreceived_claim($ddb->dt[od_ix],'message')."</b><Br>".unreceived_claim($ddb->dt[od_ix],'claim_date')."</td>";
					}else{
						$Contents .="<td class='list_box_td ' align='center' nowrap>".getOrderStatus($ddb->dt[delivery_status])."<br>".str_replace(" ","<br/>",$ddb->dt[delivery_status_regdate])."<br><font color='red'><b>".$ddb->dt[dps_status]."</b></font></td>";
					}
				}

				if($pre_type == ORDER_STATUS_DELIVERY_READY){

					if($ddb->dt[due_date]=="0000-00-00" || $ddb->dt[due_date]==""){
						$Contents .= "<td align='center' class='list_box_td'>당일</td>";
					}else{
						$Contents .= "<td align='center' class='list_box_td'>".$ddb->dt[due_date]."</td>";
					}

				}elseif($pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE||$pre_type == ORDER_STATUS_DELIVERY_ING){

                    if(!$ddb->dt[delivery_method]) $ddb->dt[delivery_method] = 1;
					$Contents .= "
					<td align='center' class='list_box_td'>
						".DeliveryMethod("",$ddb->dt[delivery_method],"","text")." / ".deliveryCompanyList($ddb->dt[quick],"text")."
						<br/>".$ddb->dt[invoice_no]." ";
						
						if(substr_count($ddb->dt[invoice_no],",") > 0){

							$explode_invoice_no = explode(",",$ddb->dt[invoice_no]);
							$Contents .= "
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"$('#invoice_no_table_".$ddb->dt[od_ix]."').toggle()\"  style='cursor:pointer;'>
							<table border=1 id='invoice_no_table_".$ddb->dt[od_ix]."' class='invoice_no_table' style='position:absolute;width:100px;display:none;' cellpadding=1  cellspacing=0>
								<col width=3><col width=*><col width=13><col width=*><col width=3>
								<tr>
									<th class='box_01'></th>
									<td class='box_02' colspan=3></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05 ' colspan=3 style='line-height:130%;padding:5px;'>";
										foreach($explode_invoice_no as $invoice_no){
											$Contents .= "
											<div style='cursor:pointer;' onclick=\"searchGoodsFlow('".$ddb->dt[quick]."', '".str_replace("-","",$invoice_no)."');\">".$invoice_no."</div>";
										}
									$Contents .= "
									</td>
									<th class='box_06'></th>
								</tr>
								<tr>
									<th class='box_07'></th>
									<td class='box_02' colspan=3></td>
									<th class='box_09'></th>
								</tr>
							</table>
							";

						}else{
							$Contents .= "
							<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"searchGoodsFlow('".$ddb->dt[quick]."', '".str_replace("-","",$ddb->dt[invoice_no])."')\"  style='cursor:pointer;'>";
						}

					$Contents .= "
					</td>";

				}elseif($pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM || $pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type == ORDER_STATUS_INCOM_COMPLETE || $pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){

							$Contents .= "<td  class='list_box_td' align='center'  style='padding:3px;' >
							<!--
							".DeliveryMethod("delivery_method[".$ddb->dt[od_ix]."]",$ddb->dt[delivery_method],"onchange=\"SetDeliveryInfoCopy('delivery_method',$(this),'".$ddb->dt[product_type]."','".$ddb->dt[oid]."','".$ddb->dt[set_group]."');\" style='width:106px;' ","select")."
							<br>
							-->

							".DeliveryMethod("",$ddb->dt[delivery_method],"","text")."
							<br/>
							".deliveryCompanyList2("quick[".$ddb->dt[od_ix]."]"," style='margin-top:3px;width:106px;' onchange=\"SetDeliveryInfoCopy('quick',$(this),'".$ddb->dt[product_type]."','".$ddb->dt[oid]."','".$ddb->dt[set_group]."')\"",$_SESSION[admininfo][company_id],$ddb->dt[quick])."
							<br>
							<input type='text' name='deliverycode[".$ddb->dt[od_ix]."]' class='textbox'  size=15 value='".$ddb->dt[invoice_no]."' validation=true title='송장번호' style='margin-top:3px;' onkeyup=\"SetDeliveryInfoCopy('deliverycode',$(this),'".$ddb->dt[product_type]."','".$ddb->dt[oid]."','".$ddb->dt[set_group]."')\" >
							<br>
							".(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ? "<img src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='margin-top:3px;cursor:pointer;' onclick=\"SelectDeliveryIng('".$ddb->dt[product_type]."','".$ddb->dt[oid]."','".$ddb->dt[set_group]."','".$ddb->dt[od_ix]."');\">" : "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='margin-top:3px;cursor:pointer;'></a>" )."
							</td>";

				}elseif($pre_type == ORDER_STATUS_DELIVERY_COMPLETE || $pre_type == ORDER_STATUS_BUY_FINALIZED){
                    if(!$ddb->dt[delivery_method]) $ddb->dt[delivery_method] = 1;
                    if(!$ddb->dt[quick]) $ddb->dt[delivery_method] = null;
					$Contents .= "
						<td align='center' class='list_box_td'>
							".DeliveryMethod("",$ddb->dt[delivery_method],"","text")." / ".deliveryCompanyList($ddb->dt[quick],"text")."
							<br/>".$ddb->dt[invoice_no]." ";
							
							if(substr_count($ddb->dt[invoice_no],",") > 0){

								$explode_invoice_no = explode(",",$ddb->dt[invoice_no]);
								$Contents .= "
								<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"$('#invoice_no_table_".$ddb->dt[od_ix]."').toggle()\"  style='cursor:pointer;'>
								<table border=1 id='invoice_no_table_".$ddb->dt[od_ix]."' class='invoice_no_table' style='position:absolute;width:100px;display:none;' cellpadding=1  cellspacing=0>
									<col width=3><col width=*><col width=13><col width=*><col width=3>
									<tr>
										<th class='box_01'></th>
										<td class='box_02' colspan=3></td>
										<th class='box_03'></th>
									</tr>
									<tr>
										<th class='box_04'></th>
										<td class='box_05 ' colspan=3 style='line-height:130%;padding:5px;'>";
											foreach($explode_invoice_no as $invoice_no){
												$Contents .= "
												<div style='cursor:pointer;' onclick=\"searchGoodsFlow('".$ddb->dt[quick]."', '".str_replace("-","",$invoice_no)."');\">".$invoice_no."</div>";
											}
										$Contents .= "
										</td>
										<th class='box_06'></th>
									</tr>
									<tr>
										<th class='box_07'></th>
										<td class='box_02' colspan=3></td>
										<th class='box_09'></th>
									</tr>
									<tr>
										<th ></th>
										<td ></td>
										<td class='box_10'></td>
										<td ></td>
										<th ></th>
									</tr>
								</table>
								";

							}else{
								$Contents .= "
								<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"searchGoodsFlow('".$ddb->dt[quick]."', '".str_replace("-","",$ddb->dt[invoice_no])."')\"  style='cursor:pointer;'>";
							}

						$Contents .= "
						</td>
						<td align='center' class='list_box_td'>".($ddb->dt[accounts_status] ? getOrderStatus($ddb->dt[accounts_status]) . "<br/>" . $ddb->dt[ac_status_regdate] : "-")."</td>";
					if($pre_type == ORDER_STATUS_BUY_FINALIZED){
						$Contents .= "
						<td align='center' class='list_box_td'><span style='cursor:pointer;' class='helpcloud' help_width='130' help_height='100' help_html='총평점 : **** 95%<br/>상품평 : **** 90%<br/>상품평 : **** 91%<br/>설명평 : **** 92%<br/>배송평 : *** 80%<br/>포장평 : *** 75%'>준비중</span></td>";
					}
				}

			$Contents .= "</tr>";


			$b_oid = $ddb->dt[oid];
			$bcompany_id = $ddb->dt[company_id];
			$bproduct_id = $ddb->dt[pid];
			$bgu_ix = $db1->dt[gu_ix];
			$bset_group = $ddb->dt[set_group];
			$b_product_type = $ddb->dt[product_type];
			$b_factory_info_addr_ix  = $ddb->dt[factory_info_addr_ix];
			$b_delivery_type = $ddb->dt[delivery_type];
            $b_ode_ix = $ddb->dt[ode_ix];
		}
	}
}else{

	if($pre_type==ORDER_STATUS_INCOM_COMPLETE||$pre_type==ORDER_STATUS_DELIVERY_ING)
		$result_colpan="9";
	elseif($pre_type==ORDER_STATUS_WAREHOUSE_DELIVERY_READY)
		$result_colpan="7";
	elseif($pre_type==ORDER_STATUS_DELIVERY_COMPLETE||$pre_type==ORDER_STATUS_BUY_FINALIZED||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY)
		$result_colpan="10";
	elseif($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY)
		$result_colpan="11";
	else
		$result_colpan="8";

	$Contents .= "<tr height=50><td colspan='".$result_colpan."' align=center>조회된 결과가 없습니다.</td></tr>";
}

$Contents .= "
	</table>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
	  <tr height=40>
		<td colspan='10' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
	  </tr>
	</table>";

//if($pre_type==ORDER_STATUS_DELIVERY_READY||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){
if($pre_type==ORDER_STATUS_DELIVERY_READY||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){

	$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>";
		if($_SESSION["admininfo"]["admin_level"] == 9 && $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){
			$help_title .= "
			<input type='radio' name='update_kind' id='update_kind_level0' value='level0' onclick=\"ChangeUpdateForm('help_text_level0');\" ><label for='update_kind_level0'>출고처리상태변경</label>";
		}
		$help_title .= "
		<input type='radio' name='update_kind' id='update_kind_level1' value='level1' onclick=\"ChangeUpdateForm('help_text_level1');\" ".($pre_type == ORDER_STATUS_DELIVERY_READY ? "checked" : "")." ><label for='update_kind_level1'>배송처리상태변경</label>
		<input type='radio' name='update_kind' id='update_kind_level3' value='level3' onclick=\"ChangeUpdateForm('help_text_level3');\" ><label for='update_kind_level3'>주문출력하기</label>";

	$help_title .= "
	</nobr>";

	$help_text = "
	<script type='text/javascript'>
	<!--
		function HelpTextChangeStatus(level,status){
			$('#ht_'+level+'_reason').hide();
			$('.ht_'+level+'_reason_'+status).show();
			if(status=='DD'){
				$('.ht_'+level+'_reason_'+status).find('.delay').attr('disabled',false);
				$('.ht_'+level+'_reason_'+status).find('.delay').show();
				$('.ht_'+level+'_reason_'+status).find('.common').hide();
				$('.ht_'+level+'_reason_'+status).find('.common').attr('disabled',true);
			}else{
				$('.ht_'+level+'_reason_'+status).find('.common').attr('disabled',false);
				$('.ht_'+level+'_reason_'+status).find('.common').show();
				$('.ht_'+level+'_reason_'+status).find('.delay').hide();
				$('.ht_'+level+'_reason_'+status).find('.delay').attr('disabled',true);
			}
			$('#ht_'+level+'_msg').hide();
			$('.ht_'+level+'_msg_'+status).show();
			$('#ht_'+level+'_delivery').hide();
			$('.ht_'+level+'_delivery_'+status).show();
			$('#ht_'+level+'_od_message').hide();
			$('.ht_'+level+'_od_message_'+status).show();
			$('input[name=od_message]:hidden').val('');

		}

		$(document).ready(function(){

			$('#due_datepicker').datepicker({
				//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
				dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
				//showMonthAfterYear:true,
				dateFormat: 'yy-mm-dd',
				buttonImageOnly: true,
				buttonText: '달력'
			});
		";

		if($pre_type==ORDER_STATUS_DELIVERY_READY){
				$help_text .= "ChangeUpdateForm('help_text_level1');";
		}elseif($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){
			$help_text .= "HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."');";
		}
		$help_text .= "
		});

	//-->
	</script>
	<div style='padding:14px 0px 5px 0px;'><img src='../images/dot_org.gif'> <b>결제/배송처리 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 결제/배송처리상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>";
	if($pre_type==ORDER_STATUS_DELIVERY_READY){
		$help_text .= "
		<div id='help_text_level4' style='display:none'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
			<col width=170>
			<col width=*>
			<tr id='ht_level4_status'>
				<td class='input_box_title'> <b>택배업체 선택</b></td>
				<td class='input_box_item'> ";

			$sdb->query("select * from shop_code where code_gubun='06'");
			$shop_code = $sdb->fetchall();

			if($pre_type==ORDER_STATUS_DELIVERY_READY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){
				if($sdb->total > 0){
					foreach($shop_code as $key => $val){
						$help_text .= "<input type='radio' name='level4_status' value='".$val[code_gubun]."_".$val[code_ix]."' checked>".$val[code_name];
					}
				}
			}
			$help_text .= "
				</td>
			</tr>
		</table>
		</div>";
	}

	$help_text .= "
	<div id='help_text_level0'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level0_status'>
			<td class='input_box_title'> <b>출고처리상태</b></td>
			<td class='input_box_item'> ";
		if($pre_type==ORDER_STATUS_DELIVERY_READY){
			$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."')\" checked><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."'  >".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY)."</label>";
		}elseif($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){
			$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM."')\" checked><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM)."</label>
			<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."')\"><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING)."</label>
			<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_WDACC' value='WDACC' ><label for='level0_update_delivery_status_WDACC'  >출고요청 취소</label> ";
		}
		$help_text .= "
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level1' style='display:none'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level1_status'>
			<td class='input_box_title'> <b>처리상태</b></td>
			<td class='input_box_item'>
				<!-- <input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_DELIVERY_DELAY."' value='".ORDER_STATUS_DELIVERY_DELAY."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_DELIVERY_DELAY."')\" checked><label for='level1_update_status_".ORDER_STATUS_DELIVERY_DELAY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_DELAY)."</label> -->";
				if($pre_type==ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){
					$help_text .= "<input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_DELIVERY_READY."')\" ><label for='level1_update_status_".ORDER_STATUS_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label> ";
				}
				if($_SESSION["admininfo"]["admin_level"] == 9){
				$help_text .= "
				<!-- <input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_CANCEL_COMPLETE."')\"><label for='level1_update_status_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label> -->
				<input type='radio' name='level1_status' checked id='level1_update_status_".ORDER_STATUS_CANCEL_APPLY."' value='".ORDER_STATUS_CANCEL_APPLY."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_CANCEL_APPLY."')\"><label for='level1_update_status_".ORDER_STATUS_CANCEL_APPLY."'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label>";
				}
				$help_text .= "
				<!-- <input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_INCOM_COMPLETE."')\"><label for='level1_update_status_".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label> -->
			</td>
		</tr>
		
		<tr id='ht_level1_reason' class='ht_level1_reason_".ORDER_STATUS_DELIVERY_DELAY." ht_level1_reason_".ORDER_STATUS_CANCEL_COMPLETE." ht_level1_reason_".ORDER_STATUS_CANCEL_APPLY."'>
			<td class='input_box_title'> <b>사유</b></td>
			<td class='input_box_item'>
				<select name='level1_reason_code' class='delay' style='font-size:12px;'>";
					$help_text .= "<option value='' >사유</option>";
					foreach($order_select_status_div['A']['DD']['DD'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
				<select name='level1_reason_code' class='common' style='font-size:12px;display:none;' disabled>";
					$help_text .= "<option value='' >사유</option>";
					foreach($order_select_status_div['A']['IR']['CA'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
				<span class='delay'>
				 예상 발송일 :
				<img src='../images/".$admininfo["language"]."/calendar_icon.gif' align='absmiddle'> <input type='text' name='level1_due_date' class='textbox point_color' value='' style='height:20px;width:70px;text-align:center;' id='due_datepicker'>
				</span>
			</td>
		</tr>
		<tr id='ht_level1_msg' class='ht_level1_msg_".ORDER_STATUS_DELIVERY_DELAY." ht_level1_msg_".ORDER_STATUS_CANCEL_COMPLETE." ht_level1_msg_".ORDER_STATUS_CANCEL_APPLY."' >
			<td class='input_box_title'> <b>기타(고객에게 노출)</b></td>
			<td class='input_box_item'>
				 <input type=text name='level1_msg'  class='textbox' value='' style='width:350px;' >
			</td>
		</tr>
		<tr id='ht_level1_od_message' class='ht_level1_od_message_".ORDER_STATUS_DELIVERY_DELAY."' >
			<td class='input_box_title'> <b>관리자용 코멘트</b></td>
			<td class='input_box_item'>
				 <input type=text name='od_message' class='textbox' value='' style='width:350px;' >
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level2' style='display:none'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level2_status'>
			<td class='input_box_title'> <b>구매주문요청</b></td>
			<td class='input_box_item'>
				준비중입니다.
			</td>
		</tr>
		<tr id='ht_level2_status'>
			<td class='input_box_title'> <b>구매요청수량</b></td>
			<td class='input_box_item'>
				준비중입니다.
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level3' style='display:none;height:190px;'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level3_status' height=90>
			<td class='input_box_title'> <b>주문출력하기</b></td>
			<td class='input_box_item' style='line-heigth:150%;'>
				<input type='radio' name='level3_status' id='level3_update_status_provider_print' value='provider_print' checked><label for='level3_update_status_provider_print'>공급자용</label> <input type='radio' name='level3_status' id='level3_update_status_buyer_print' value='buyer_print' ><label for='level3_update_status_buyer_print'>구매자용</label> <input type='radio' name='level3_status' id='level3_update_status_combo_print' value='combo_print' ><label for='level3_update_status_combo_print'>공급자+구매자용</label> <input type='radio' name='level3_status' id='level3_update_status_noprice_print' value='noprice_print' ><label for='level3_update_status_noprice_print'>가격 노출 X</label>
				<div style='padding:5px 0px 0px 5px;'>
				* <span class='small'>인쇄시 머릿글이나 바닦글 또는 여백을 설정하시고 싶으시면 옵션에 인쇄 -> 페이지 설정에서 설정해주시면됩니다. </span>
				<br/> * <span class='small'>구분선이라 라인이 안나올시 배경색 및 이미지 인쇄 체크박스 체크 해주시면 됩니다.</span>
				<!--span class='small'>주문이 인쇄가 안되거나 ActiveX 컨트롤이 설치가 안될때 수동으로 받아서 설치해 주시기 바랍니다. <input type='button' value='다운로드' onclick=\"location.href='./scriptx/ScriptX.msi'\" /> </span-->
				</div>
			</td>
		</tr>
	</table>
	</div>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>";

	$Contents .= HelpBox($help_title, $help_text,650);

}elseif($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM){
 
	$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>";
	    /*
		$help_title .= "
		<input type='radio' name='update_kind' id='update_kind_level4' value='level4' onclick=\"ChangeUpdateForm('help_text_level4');\" checked><label for='update_kind_level4'>주문정보 택배업체 전송</label>";
	    */
		$help_title .= "
		<input type='radio' name='update_kind' id='update_kind_level0' value='level0' onclick=\"ChangeUpdateForm('help_text_level0');\" checked><label for='update_kind_level0'>출고처리상태변경</label>
		<input type='radio' name='update_kind' id='update_kind_level3' value='level3' onclick=\"ChangeUpdateForm('help_text_level3');\" ><label for='update_kind_level3'>송장번호 추가/변경</label>
		<input type='radio' name='update_kind' id='update_kind_level1' value='level1' onclick=\"ChangeUpdateForm('help_text_level1');\" ><label for='update_kind_level1'>배송처리상태변경</label>
		<input type='radio' name='update_kind' id='update_kind_level2' value='level2' onclick=\"ChangeUpdateForm('help_text_level2');\" ><label for='update_kind_level2'>주문출력하기</label>";

	$help_title .= "
	</nobr>";

	$help_text = "
	<script type='text/javascript'>
	<!--
		function HelpTextChangeStatus(level,status){
			$('#ht_'+level+'_reason').hide();
			$('.ht_'+level+'_reason_'+status).show();
			if(status=='DD'){
				$('.ht_'+level+'_reason_'+status).find('.delay').attr('disabled',false);
				$('.ht_'+level+'_reason_'+status).find('.delay').show();
				$('.ht_'+level+'_reason_'+status).find('.common').hide();
				$('.ht_'+level+'_reason_'+status).find('.common').attr('disabled',true);
			}else{
				$('.ht_'+level+'_reason_'+status).find('.common').attr('disabled',false);
				$('.ht_'+level+'_reason_'+status).find('.common').show();
				$('.ht_'+level+'_reason_'+status).find('.delay').hide();
				$('.ht_'+level+'_reason_'+status).find('.delay').attr('disabled',true);
			}
			$('#ht_'+level+'_msg').hide();
			$('.ht_'+level+'_msg_'+status).show();
			$('#ht_'+level+'_delivery').hide();
			$('.ht_'+level+'_delivery_'+status).show();
		}

		$(document).ready(function(){

			$('#due_datepicker').datepicker({
				//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
				dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
				//showMonthAfterYear:true,
				dateFormat: 'yy-mm-dd',
				buttonImageOnly: true,
				buttonText: '달력'
			});

			HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."');
		});

	//-->
	</script>
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>결제/배송처리 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 결제/배송처리상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
	<div id='help_text_level4' style='display:none'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level4_status'>
			<td class='input_box_title'> <b>택배업체 선택</b></td>
			<td class='input_box_item'> ";

		$sdb->query("select * from shop_code where code_gubun='06'");
		$shop_code = $sdb->fetchall();

		if($pre_type==ORDER_STATUS_DELIVERY_READY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY || $pre_type == "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM){
			if($sdb->total > 0){
				foreach($shop_code as $key => $val){
					$help_text .= "<input type='radio' name='level4_status' value='".$val[code_gubun]."_".$val[code_ix]."' checked>".$val[code_name];
				}
			}
		}
		$help_text .= "
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level0'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level0_status'>
			<td class='input_box_title'> <b>출고처리상태</b></td>
			<td class='input_box_item'>
				<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_CUSTOMIZING' value='CUSTOMIZING' checked><label for='level0_update_delivery_status_CUSTOMIZING'>DPS 전송</label>

				<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."')\"  ><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING)."</label>



				<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."')\"><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY)."</label>
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level1' style='display:none'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level1_status'>
			<td class='input_box_title'> <b>처리상태</b></td>
			<td class='input_box_item'>
				<input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_DELIVERY_DELAY."' value='".ORDER_STATUS_DELIVERY_DELAY."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_DELIVERY_DELAY."')\" checked><label for='level1_update_status_".ORDER_STATUS_DELIVERY_DELAY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_DELAY)."</label>";
				if($_SESSION["admininfo"]["admin_level"] == 9){
				$help_text .= "
				<input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_CANCEL_COMPLETE."')\"><label for='level1_update_status_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label>
				<input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_CANCEL_APPLY."' value='".ORDER_STATUS_CANCEL_APPLY."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_CANCEL_APPLY."')\"><label for='level1_update_status_".ORDER_STATUS_CANCEL_APPLY."'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label>";
				}
				$help_text .= "
				<input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_INCOM_COMPLETE."')\"><label for='level1_update_status_".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label>
			</td>
		</tr>
		<tr id='ht_level1_reason' class='ht_level1_reason_".ORDER_STATUS_DELIVERY_DELAY." ht_level1_reason_".ORDER_STATUS_CANCEL_COMPLETE." ht_level1_reason_".ORDER_STATUS_CANCEL_APPLY."'>
			<td class='input_box_title'> <b>사유</b></td>
			<td class='input_box_item'>
				<select name='level1_reason_code' class='delay' style='font-size:12px;'>";
					$help_text .= "<option value='' >사유</option>";
					foreach($order_select_status_div['A']['DD']['DD'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
				<select name='level1_reason_code' class='common' style='font-size:12px;display:none;' disabled>";
					$help_text .= "<option value='' >사유</option>";
					foreach($order_select_status_div['A']['IR']['CA'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
				<span class='delay'>
				 예상 발송일 :
				<img src='../images/".$admininfo["language"]."/calendar_icon.gif' align='absmiddle'> <input type='text' name='level1_due_date' class='textbox point_color' value='' style='height:20px;width:70px;text-align:center;' id='due_datepicker'>
				</span>
			</td>
		</tr>
		<tr id='ht_level1_msg' class='ht_level1_msg_".ORDER_STATUS_DELIVERY_DELAY." ht_level1_msg_".ORDER_STATUS_CANCEL_COMPLETE." ht_level1_msg_".ORDER_STATUS_CANCEL_APPLY."' >
			<td class='input_box_title'> <b>기타</b></td>
			<td class='input_box_item'>
				 <input type=text name='level1_msg'  class='textbox' value='' style='width:350px;' >
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level2' style='display:none'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level2_status' >
			<td class='input_box_title'> <b>주문출력하기</b></td>
			<td class='input_box_item'>";

			if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM){
				$help_text .= "<input type='radio' name='level2_status' id='level2_update_status_picking_print' value='picking_print' checked><label for='level2_update_status_picking_print'>picking서 출력하기</label> ";
			}

			$help_text .= "
				<input type='radio' name='level2_status' id='level2_update_status_provider_print' value='provider_print' ".($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM ? "" : "checked")."><label for='level2_update_status_provider_print'>공급자용</label> <input type='radio' name='level2_status' id='level2_update_status_buyer_print' value='buyer_print' ><label for='level2_update_status_buyer_print'>구매자용</label> <input type='radio' name='level2_status' id='level2_update_status_combo_print' value='combo_print' ><label for='level2_update_status_combo_print'>공급자+구매자용</label> <input type='radio' name='level2_status' id='level2_update_status_noprice_print' value='noprice_print' ><label for='level2_update_status_noprice_print'>가격 노출 X</label>
				<br/>&nbsp;&nbsp; * <span class='small'>인쇄시 머릿글이나 바닦글 또는 여백을 설정하시고 싶으시면 옵션에 인쇄 -> 페이지 설정에서 설정해주시면됩니다. </span>
				<br/>&nbsp;&nbsp; * <span class='small'>구분선이라 라인이 안나올시 배경색 및 이미지 인쇄 체크박스 체크 해주시면 됩니다.</span>
				<!--span class='small'>주문이 인쇄가 안되거나 ActiveX 컨트롤이 설치가 안될때 수동으로 받아서 설치해 주시기 바랍니다. <input type='button' value='다운로드' onclick=\"location.href='./scriptx/ScriptX.msi'\" /> </span-->
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level3' style='display:none'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level3_delivery_status'>
			<td class='input_box_title'> <b>배송/송장처리상태</b></td>
			<td class='input_box_item'>
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_invoce_update' value='invoce_update' onclick=\"HelpTextChangeStatus('level3','invoce_update')\" checked><label for='level3_update_delivery_status_invoce_update'>개별 송장번호 변경</label>
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_select_invoce_update' value='select_invoce_update' onclick=\"HelpTextChangeStatus('level3','select_invoce_update')\"><label for='level3_update_delivery_status_select_invoce_update'>일괄 송장번호 변경</label>
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_invoce_add' value='invoce_add' onclick=\"HelpTextChangeStatus('level3','invoce_add')\"><label for='level3_update_delivery_status_invoce_add'>송장번호 추가</label>
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_invoce_delete' value='invoce_delete' onclick=\"HelpTextChangeStatus('level3','invoce_delete')\"><label for='level3_update_delivery_status_invoce_delete'>송장번호 삭제</label>
			</td>
		</tr>
		<tr id='ht_level3_reason' class='ht_level3_reason_select_invoce_update' style='display:none'>
			<td class='input_box_title'><b>택배사</b></td>
			<td class='input_box_item'>
				".deliveryCompanyList2("help_quick","",$_SESSION["admininfo"]["company_id"],"")."
			</td>
		</tr>
		<tr id='ht_level3_msg' class='ht_level3_msg_select_invoce_update ht_level3_msg_invoce_add' style='display:none'>
			<td class='input_box_title'> <b>송장번호</b> <img src='../images/".$admininfo["language"]."/btn_add.gif' border=0 style='margin:2px 0 3px 5px; vertical-align:middle;cursor:pointer' onclick=\"help_deliverycode_copy('help_deliverycode_area');\"></td>
			<td class='input_box_item'>
				<span class='help_deliverycode_area'>
					<input type='text' name='help_deliverycode[]' class='textbox'  size=15 value='' >
					<img src='../images/btn_x.gif' style='cursor:pointer' ondblclick=\"help_deliverycode_delete($(this));\" align='absmiddle'> &nbsp;
				</span>
			</td>
		</tr>
	</table>
	</div>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>";

	$Contents .= HelpBox($help_title, $help_text,720);

}elseif($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){//포장대기,출고대기

	$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind_level0' value='level0' onclick=\"ChangeUpdateForm('help_text_level0');\" checked><label for='update_kind_level0'>출고처리상태변경</label>
		<input type='radio' name='update_kind' id='update_kind_level3' value='level3' onclick=\"ChangeUpdateForm('help_text_level3');\" ><label for='update_kind_level3'>송장번호 추가/변경</label>
		<input type='radio' name='update_kind' id='update_kind_level1' value='level1' onclick=\"ChangeUpdateForm('help_text_level1');\" ><label for='update_kind_level1'>배송처리상태변경</label>
		<input type='radio' name='update_kind' id='update_kind_level2' value='level2' onclick=\"ChangeUpdateForm('help_text_level2');\" ><label for='update_kind_level2'>주문출력하기</label>

	</nobr>";

	$help_text = "
	<script type='text/javascript'>
	<!--
		function HelpTextChangeStatus(level,status){
			$('#ht_'+level+'_reason').hide();
			$('.ht_'+level+'_reason_'+status).show();
			if(status=='DD'){
				$('.ht_'+level+'_reason_'+status).find('.delay').attr('disabled',false);
				$('.ht_'+level+'_reason_'+status).find('.delay').show();
				$('.ht_'+level+'_reason_'+status).find('.common').hide();
				$('.ht_'+level+'_reason_'+status).find('.common').attr('disabled',true);
			}else{
				$('.ht_'+level+'_reason_'+status).find('.common').attr('disabled',false);
				$('.ht_'+level+'_reason_'+status).find('.common').show();
				$('.ht_'+level+'_reason_'+status).find('.delay').hide();
				$('.ht_'+level+'_reason_'+status).find('.delay').attr('disabled',true);
			}
			$('#ht_'+level+'_msg').hide();
			$('.ht_'+level+'_msg_'+status).show();
			$('#ht_'+level+'_delivery').hide();
			$('.ht_'+level+'_delivery_'+status).show();

			if(level=='level0' && status=='WDC'){
				$('#ht_'+level+'_status').show();
				$('#level0_update_status_DI').attr('checked',true);
			}else if(level=='level0' && status!='WDC'){
				$('#ht_'+level+'_status').hide();
				$('#level0_update_status_DI').attr('checked',false);
			}
		}

		$(document).ready(function(){";
				if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING){
					$help_text .= "HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'); ";
				}elseif($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
					$help_text .= "HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."'); ";
				}
				$help_text .= "
				$('#due_datepicker').datepicker({
				//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
				dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
				//showMonthAfterYear:true,
				dateFormat: 'yy-mm-dd',
				buttonImageOnly: true,
				buttonText: '달력'
			});
		});


        

	//-->
	</script>
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>결제/배송처리 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 결제/배송처리상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
	<div id='help_text_level0'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level0_delivery_status'>
			<td class='input_box_title'> <b>출고처리상태</b></td>
			<td class='input_box_item'> ";
			if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING){
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."')\" checked ><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_READY)."</label> ";
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."')\" ><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY)."</label> ";
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."')\" ><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."'  >".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE)."</label> ";
			}elseif($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."')\" checked ><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."'  >".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE)."</label> ";
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."')\" ><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING)."</label> ";
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."')\" ><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY)."</label> ";
			}
		$help_text .= "
			</td>
		</tr>
		<tr id='ht_level0_status' class='ht_level0_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."'>
			<td class='input_box_title'> <b>배송처리상태</b></td>
			<td class='input_box_item'> ";
				$help_text .= "<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' ><label for='level0_update_status_".ORDER_STATUS_DELIVERY_ING."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label>";

		$help_text .= "
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level1' style='display:none'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level1_status'>
			<td class='input_box_title'> <b>처리상태</b></td>
			<td class='input_box_item'>
				<input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_DELIVERY_DELAY."' value='".ORDER_STATUS_DELIVERY_DELAY."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_DELIVERY_DELAY."')\" checked><label for='level1_update_status_".ORDER_STATUS_DELIVERY_DELAY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_DELAY)."</label>
				<input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_DELIVERY_READY."')\" ><label for='level1_update_status_".ORDER_STATUS_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label>";
				if($_SESSION["admininfo"]["admin_level"] == 9){
				$help_text .= "
				<input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_CANCEL_COMPLETE."')\"><label for='level1_update_status_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label>
				<input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_CANCEL_APPLY."' value='".ORDER_STATUS_CANCEL_APPLY."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_CANCEL_APPLY."')\"><label for='level1_update_status_".ORDER_STATUS_CANCEL_APPLY."'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label>";
				}
				$help_text .= "
				<input type='radio' name='level1_status' id='level1_update_status_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' onclick=\"HelpTextChangeStatus('level1','".ORDER_STATUS_INCOM_COMPLETE."')\"><label for='level1_update_status_".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label>
			</td>
		</tr>
		<tr id='ht_level1_reason' class='ht_level1_reason_".ORDER_STATUS_DELIVERY_DELAY." ht_level1_reason_".ORDER_STATUS_CANCEL_COMPLETE." ht_level1_reason_".ORDER_STATUS_CANCEL_APPLY."'>
			<td class='input_box_title'> <b>사유</b></td>
			<td class='input_box_item'>
				<select name='level1_reason_code' class='delay' style='font-size:12px;'>";
					$help_text .= "<option value='' >사유</option>";
					foreach($order_select_status_div['A']['DD']['DD'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
				<select name='level1_reason_code' class='common' style='font-size:12px;display:none;' disabled>";
					$help_text .= "<option value='' >사유</option>";
					foreach($order_select_status_div['A']['IR']['CA'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
				<span class='delay'>
				 예상 발송일 :
				<img src='../images/".$admininfo["language"]."/calendar_icon.gif' align='absmiddle'> <input type='text' name='level1_due_date' class='textbox point_color' value='' style='height:20px;width:70px;text-align:center;' id='due_datepicker'>
				</span>
			</td>
		</tr>
		<tr id='ht_level1_msg' class='ht_level1_msg_".ORDER_STATUS_DELIVERY_DELAY." ht_level1_msg_".ORDER_STATUS_CANCEL_COMPLETE." ht_level1_msg_".ORDER_STATUS_CANCEL_APPLY."' >
			<td class='input_box_title'> <b>기타</b></td>
			<td class='input_box_item'>
				 <input type=text name='level1_msg'  class='textbox' value='' style='width:350px;' >
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level2' style='display:none'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level2_status' >
			<td class='input_box_title'> <b>주문출력하기</b></td>
			<td class='input_box_item'>";
		
				if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM){
					$help_text .= "<input type='radio' name='level2_status' id='level2_update_status_picking_print' value='picking_print' checked><label for='level2_update_status_picking_print'>picking서 출력하기</label> ";
				}
				$help_text .= "
				<input type='radio' name='level2_status' id='level2_update_status_provider_print' value='provider_print' ".($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING ? "" : "checked")."><label for='level2_update_status_provider_print'>공급자용</label> <input type='radio' name='level2_status' id='level2_update_status_buyer_print' value='buyer_print' ><label for='level2_update_status_buyer_print'>구매자용</label> <input type='radio' name='level2_status' id='level2_update_status_combo_print' value='combo_print' ><label for='level2_update_status_combo_print'>공급자+구매자용</label> <input type='radio' name='level2_status' id='level2_update_status_noprice_print' value='noprice_print' ><label for='level2_update_status_noprice_print'>가격 노출 X</label>
				<br/>&nbsp;&nbsp; * <span class='small'>인쇄시 머릿글이나 바닦글 또는 여백을 설정하시고 싶으시면 옵션에 인쇄 -> 페이지 설정에서 설정해주시면됩니다. </span>
				<br/>&nbsp;&nbsp; * <span class='small'>구분선이라 라인이 안나올시 배경색 및 이미지 인쇄 체크박스 체크 해주시면 됩니다.</span>
				<!--span class='small'>주문이 인쇄가 안되거나 ActiveX 컨트롤이 설치가 안될때 수동으로 받아서 설치해 주시기 바랍니다. <input type='button' value='다운로드' onclick=\"location.href='./scriptx/ScriptX.msi'\" /> </span-->
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level3' style='display:none'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level3_delivery_status'>
			<td class='input_box_title'> <b>배송/송장처리상태</b></td>
			<td class='input_box_item'>
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_invoce_update' value='invoce_update' onclick=\"HelpTextChangeStatus('level3','invoce_update')\" checked><label for='level3_update_delivery_status_invoce_update'>개별 송장번호 변경</label>
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_select_invoce_update' value='select_invoce_update' onclick=\"HelpTextChangeStatus('level3','select_invoce_update')\"><label for='level3_update_delivery_status_select_invoce_update'>일괄 송장번호 변경</label>
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_invoce_add' value='invoce_add' onclick=\"HelpTextChangeStatus('level3','invoce_add')\"><label for='level3_update_delivery_status_invoce_add'>송장번호 추가</label>
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_invoce_delete' value='invoce_delete' onclick=\"HelpTextChangeStatus('level3','invoce_delete')\"><label for='level3_update_delivery_status_invoce_delete'>송장번호 삭제</label>
			</td>
		</tr>
		<tr id='ht_level3_reason' class='ht_level3_reason_select_invoce_update' style='display:none'>
			<td class='input_box_title'><b>택배사</b></td>
			<td class='input_box_item'>
				".deliveryCompanyList2("help_quick","",$_SESSION["admininfo"]["company_id"],"")."
			</td>
		</tr>
		<tr id='ht_level3_msg' class='ht_level3_msg_select_invoce_update ht_level3_msg_invoce_add' style='display:none'>
			<td class='input_box_title'> <b>송장번호</b> <img src='../images/".$admininfo["language"]."/btn_add.gif' border=0 style='margin:2px 0 3px 5px; vertical-align:middle;cursor:pointer' onclick=\"help_deliverycode_copy('help_deliverycode_area');\"></td>
			<td class='input_box_item'>
				<span class='help_deliverycode_area'>
					<input type='text' name='help_deliverycode[]' class='textbox'  size=15 value='' >
					<img src='../images/btn_x.gif' style='cursor:pointer' ondblclick=\"help_deliverycode_delete($(this));\" align='absmiddle'> &nbsp;
				</span>
			</td>
		</tr>
	</table>
	</div>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>";

	$Contents .= HelpBox($help_title, $help_text,720);

}elseif($pre_type==ORDER_STATUS_INCOM_COMPLETE||$pre_type==ORDER_STATUS_DELIVERY_ING||$pre_type == ORDER_STATUS_DELIVERY_COMPLETE){//||$pre_type ==ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE
	$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>";

		if($pre_type==ORDER_STATUS_DELIVERY_COMPLETE && $_SESSION["admininfo"]["admin_level"]!=9){
			$help_title .= "
			<input type='radio' name='update_kind' id='update_kind_level3' value='level3' onclick=\"ChangeUpdateForm('help_text_level3');\" checked><label for='update_kind_level3'>송장번호 추가/변경</label>";
		}else{
			$help_title .= "
			<input type='radio' name='update_kind' id='update_kind_level0' value='level0' onclick=\"ChangeUpdateForm('help_text_level0');\" checked><label for='update_kind_level0'>배송처리상태변경</label>
			<input type='radio' name='update_kind' id='update_kind_level3' value='level3' onclick=\"ChangeUpdateForm('help_text_level3');\" ><label for='update_kind_level3'>송장번호 추가/변경</label>";
		}

	$help_title .= "
	</nobr>";

	$help_text = "
	<script type='text/javascript'>
	<!--
		function HelpTextChangeStatus(level,status){
			$('#ht_'+level+'_reason').hide();
			$('.ht_'+level+'_reason_'+status).show();
			$('#ht_'+level+'_msg').hide();
			$('.ht_'+level+'_msg_'+status).show();
			$('#ht_'+level+'_delivery').hide();
			$('.ht_'+level+'_delivery_'+status).show();
			";

			if($pre_type==ORDER_STATUS_DELIVERY_ING){

			$help_text .= "
			if(status=='DR'){
				$('#ht_level0_delivery').find('input').each(function(){
					$(this).attr('checked',false);
				})
				$('#ht_level0_delivery').find('input:first').each(function(){
					$(this).attr('checked',true);
				})
				$('#ht_level0_msg').find('input').each(function(){
					$(this).attr('checked',false);
				})
				$('#ht_level0_msg').find('input:first').each(function(){
					$(this).attr('checked',true);
				})
			}else{
				$('#ht_level0_delivery').find('input').each(function(){
					$(this).attr('checked',false);
				})

				$('#ht_level0_msg').find('input').each(function(){
					$(this).attr('checked',false);
				})
			}";

			}

		$help_text .= "
		}

		$(document).ready(function(){";
		if($pre_type==ORDER_STATUS_INCOM_COMPLETE){
			$help_text .= "HelpTextChangeStatus('level0','".ORDER_STATUS_DELIVERY_ING."');";
		}elseif($pre_type==ORDER_STATUS_DELIVERY_ING){//||$pre_type ==ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE
			if($invoice_no_bool=="Y"){
				$help_text .= "HelpTextChangeStatus('level0','".ORDER_STATUS_DELIVERY_ING."');";
			}else{
				$help_text .= "HelpTextChangeStatus('level0','".ORDER_STATUS_DELIVERY_COMPLETE."');";
			}
		}elseif($pre_type==ORDER_STATUS_DELIVERY_COMPLETE){
			if($_SESSION["admininfo"]["admin_level"]==9){
				$help_text .= "HelpTextChangeStatus('level0','".ORDER_STATUS_BUY_FINALIZED."');";
			}else{
				$help_text .= "ChangeUpdateForm('help_text_level3');";
			}
		}
		$help_text .= "
		});

	//-->
	</script>
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>결제/배송처리 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 결제/배송처리상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
	<div id='help_text_level0'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>";
		/*
		if($pre_type ==ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE){
			$help_text .= "
			<tr id='ht_level0_delivery_status'>
				<td class='input_box_title'> <b>출고처리상태</b></td>
				<td class='input_box_item'> ";
	$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."')\" checked><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."'  >".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE)."</label> ";
			$help_text .= "
				</td>
			</tr>";
		}
		*/
		$help_text .= "
		<tr id='ht_level0_status'>
			<td class='input_box_title'> <b>배송처리상태</b></td>
			<td class='input_box_item'> ";

if($pre_type==ORDER_STATUS_INCOM_COMPLETE){
	$help_text .= "<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_DELIVERY_ING."')\" checked><label for='level0_update_status_".ORDER_STATUS_DELIVERY_ING."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label>";
}elseif($pre_type==ORDER_STATUS_DELIVERY_ING){//||$pre_type ==ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE
	if($invoice_no_bool=="Y"){
		$help_text .= "<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_DELIVERY_ING."')\" checked><label for='level0_update_status_".ORDER_STATUS_DELIVERY_ING."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label>";
	}else{
		
		if($_SESSION["admininfo"]["admin_level"]==9){
			$help_text .= "<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_DELIVERY_COMPLETE."')\" checked><label for='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label> ";
			$help_text .= "<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_DELIVERY_READY."')\" ><label for='level0_update_status_".ORDER_STATUS_DELIVERY_READY."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label>";
		}else{
			$help_text .= "<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_DELIVERY_READY."')\" checked><label for='level0_update_status_".ORDER_STATUS_DELIVERY_READY."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label>";
		}
	}
}elseif($pre_type==ORDER_STATUS_DELIVERY_COMPLETE){

	$help_text .= "<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_BUY_FINALIZED."' value='".ORDER_STATUS_BUY_FINALIZED."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_BUY_FINALIZED."')\" checked><label for='level0_update_status_".ORDER_STATUS_BUY_FINALIZED."'  >".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</label>";
	$help_text .= "<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_DELIVERY_ING."')\" ><label for='level0_update_status_".ORDER_STATUS_DELIVERY_ING."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label>";

}
		$help_text .= "
			</td>
		</tr>";
	if($pre_type==ORDER_STATUS_DELIVERY_ING){
		if($_SESSION["admininfo"]["admin_level"]==9){
			$help_text .= "
			<tr id='ht_level0_delivery' class='ht_level0_delivery_".ORDER_STATUS_DELIVERY_READY."' style='display:none;'>
				<td class='input_box_title'> <b>출고처리상태</b></td>
				<td class='input_box_item'> ";
					$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' checked ><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_READY)." (출고리스트 삭제 및 재고가 다시 입고 됩니다.)</label> ";
			$help_text .= "
				</td>
			</tr>";
		}
		$help_text .= "
		<tr id='ht_level0_msg' class='ht_level0_msg_".ORDER_STATUS_DELIVERY_READY."' style='display:none;'>
			<td class='input_box_title'> <b>송장번호상태</b></td>
			<td class='input_box_item'>
				<input type='radio' name='delete_invoice_yn' id='delete_invoice_n' value='N' checked ><label for='delete_invoice_n'>송장번호 유지</label>
				<input type='radio' name='delete_invoice_yn' id='delete_invoice_y' value='Y' ><label for='delete_invoice_y'>송장번호 삭제</label>
			</td>
		</tr>";
	}
	$help_text .= "
	</table>
	</div>
	<div id='help_text_level1' style='display:none'></div>
	<div id='help_text_level2' style='display:none'></div>
	<div id='help_text_level3' style='display:none'>";

		$help_text .= "
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level3_delivery_status'>
			<td class='input_box_title'> <b>배송/송장처리상태</b></td>
			<td class='input_box_item'>";

			if($pre_type!=ORDER_STATUS_DELIVERY_ING){
				$help_text .= "
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_invoce_update' value='invoce_update' onclick=\"HelpTextChangeStatus('level3','invoce_update')\" checked><label for='level3_update_delivery_status_invoce_update'>개별 송장번호 변경</label>";
			}
				$help_text .= "
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_select_invoce_update' value='select_invoce_update' onclick=\"HelpTextChangeStatus('level3','select_invoce_update')\" ".($pre_type==ORDER_STATUS_DELIVERY_ING ? "checked" : "")."><label for='level3_update_delivery_status_select_invoce_update'>일괄 송장번호 변경</label>
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_invoce_add' value='invoce_add' onclick=\"HelpTextChangeStatus('level3','invoce_add')\"><label for='level3_update_delivery_status_invoce_add'>송장번호 추가</label>";
			if($pre_type!=ORDER_STATUS_DELIVERY_ING){
				$help_text .= "
				<input type='radio' name='level3_delivery_status' id='level3_update_delivery_status_invoce_delete' value='invoce_delete' onclick=\"HelpTextChangeStatus('level3','invoce_delete')\"><label for='level3_update_delivery_status_invoce_delete'>송장번호 삭제</label>";
			}
			$help_text .= "
			</td>
		</tr>
		<tr id='ht_level3_reason' class='ht_level3_reason_select_invoce_update' ".($pre_type==ORDER_STATUS_DELIVERY_ING ? "" : "style='display:none'").">
			<td class='input_box_title'><b>택배사</b></td>
			<td class='input_box_item'>
				".deliveryCompanyList2("help_quick","",$_SESSION["admininfo"]["company_id"],"")."
			</td>
		</tr>
		<tr id='ht_level3_msg' class='ht_level3_msg_select_invoce_update ht_level3_msg_invoce_add' ".($pre_type==ORDER_STATUS_DELIVERY_ING ? "" : "style='display:none'").">
			<td class='input_box_title'> <b>송장번호</b> <img src='../images/".$admininfo["language"]."/btn_add.gif' border=0 style='margin:2px 0 3px 5px; vertical-align:middle;cursor:pointer' onclick=\"help_deliverycode_copy('help_deliverycode_area');\"></td>
			<td class='input_box_item'>
				<span class='help_deliverycode_area'>
					<input type='text' name='help_deliverycode[]' class='textbox'  size=15 value='' >
					<img src='../images/btn_x.gif' style='cursor:pointer' ondblclick=\"help_deliverycode_delete($(this));\" align='absmiddle'> &nbsp;
				</span>
			</td>
		</tr>
		</table>";

	$help_text .= "
	</div>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>";

	$Contents .= HelpBox($help_title, $help_text,500);
}else if($pre_type==ORDER_UNRECEIVED_CLAIM){
	$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>";

		$help_title .= "
		<input type='radio' name='update_kind' id='update_kind_level0' value='level0_uc' onclick=\"ChangeUpdateForm('help_text_level0');\" checked><label for='update_kind_level0'>신고철회</label>
		<input type='radio' name='update_kind' id='update_kind_level3' value='level3_uc' onclick=\"ChangeUpdateForm('help_text_level3');\" ><label for='update_kind_level3'>신고철회/송장재입력</label>";

	$help_title .= "
	</nobr>";

	$help_text = "
	
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>미수령신고 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
	<div id='help_text_level0'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>";
		
		$help_text .= "
		<tr id='level0_status'>
			<td class='input_box_title'> <b>미수령신고상태</b></td>
			<td class='input_box_item'> 
				<input type='radio' name='level0_uc_status' id='level0_uc_status' value='".ORDER_UNRECEIVED_CLAIM_COMPLETE."'  checked><label for='level0_uc_status' >신고요청 철회</label>
			</td>
		</tr>
		<tr id='level0_uc_message' class='level0_uc_message_".ORDER_UNRECEIVED_CLAIM."' >
			<td class='input_box_title'> <b>철회 메시지</b></td>
			<td class='input_box_item'>
				 <input type=text name='level0_uc_message' class='textbox' value='' style='width:350px;' >
			</td>
		</tr>
	</table>
	</div>
	<div id='help_text_level3' style='display:none'>";

		$help_text .= "
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='level3_status'>
			<td class='input_box_title'> <b>미수령신고상태</b></td>
			<td class='input_box_item'> 
				<input type='radio' name='level3_uc_status' id='level3_uc_status' value='".ORDER_UNRECEIVED_CLAIM_COMPLETE."'  checked><label for='level3_uc_status' >신고요청 철회</label>
			</td>
		</tr>
		<tr id='level3_uc_message' class='level3_uc_message_".ORDER_UNRECEIVED_CLAIM."' >
			<td class='input_box_title'> <b>철회 메시지</b></td>
			<td class='input_box_item'>
				 <input type=text name='level3_uc_message' class='textbox' value='' style='width:350px;' >
			</td>
		</tr>
		<tr id='level3_reason' class='level3_reason_select_invoce_update' >
			<td class='input_box_title'><b>택배사</b></td>
			<td class='input_box_item'>
				".deliveryCompanyList2("help_quick","",$_SESSION["admininfo"]["company_id"],"")."
			</td>
		</tr>
		<tr id='ht_level3_msg' class='level3_msg_select_invoce_update level3_msg_invoce_add' >
			<td class='input_box_title'> <b>송장번호</b> <img src='../images/".$admininfo["language"]."/btn_add.gif' border=0 style='margin:2px 0 3px 5px; vertical-align:middle;cursor:pointer' onclick=\"help_deliverycode_copy('help_deliverycode_area');\"></td>
			<td class='input_box_item'>
				<span class='help_deliverycode_area'>
					<input type='text' name='help_deliverycode[]' class='textbox'  size=15 value='' >
					<img src='../images/btn_x.gif' style='cursor:pointer' ondblclick=\"help_deliverycode_delete($(this));\" align='absmiddle'> &nbsp;
				</span>
			</td>
		</tr>
		</table>";

	$help_text .= "
	</div>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>";

	$Contents .= HelpBox($help_title, $help_text,500);
}else{
	$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>";


		$help_title .= "
		<input type='radio' name='update_kind' id='update_kind_level1' value='level1' onclick=\"ChangeUpdateForm('help_text_level1');\" checked><label for='update_kind_level1'>주문출력하기</label>";


	$help_title .= "
	</nobr>";

	$help_text = "
	<script type='text/javascript'>
	<!--
		function HelpTextChangeStatus(level,status){
			$('#ht_'+level+'_reason').hide();
			$('.ht_'+level+'_reason_'+status).show();
			if(status=='DD'){
				$('.ht_'+level+'_reason_'+status).find('.delay').attr('disabled',false);
				$('.ht_'+level+'_reason_'+status).find('.delay').show();
				$('.ht_'+level+'_reason_'+status).find('.common').hide();
				$('.ht_'+level+'_reason_'+status).find('.common').attr('disabled',true);
			}else{
				$('.ht_'+level+'_reason_'+status).find('.common').attr('disabled',false);
				$('.ht_'+level+'_reason_'+status).find('.common').show();
				$('.ht_'+level+'_reason_'+status).find('.delay').hide();
				$('.ht_'+level+'_reason_'+status).find('.delay').attr('disabled',true);
			}
			$('#ht_'+level+'_msg').hide();
			$('.ht_'+level+'_msg_'+status).show();
			$('#ht_'+level+'_delivery').hide();
			$('.ht_'+level+'_delivery_'+status).show();
		}

		$(document).ready(function(){

		});

	//-->
	</script>
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>결제/배송처리 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 결제/배송처리상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
	<div id='help_text_level1'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level1_status' >
			<td class='input_box_title'> <b>주문출력하기</b></td>
			<td class='input_box_item'>
				<input type='radio' name='level1_status' id='level1_update_status_provider_print' value='provider_print' checked><label for='level1_update_status_provider_print'>공급자용</label> <input type='radio' name='level1_status' id='level1_update_status_buyer_print' value='buyer_print' ><label for='level1_update_status_buyer_print'>구매자용</label> <input type='radio' name='level1_status' id='level1_update_status_combo_print' value='combo_print' ><label for='level1_update_status_combo_print'>공급자+구매자용</label> <input type='radio' name='level1_status' id='level1_update_status_noprice_print' value='noprice_print' ><label for='level1_update_status_noprice_print'>가격 노출 X</label>
				<br/>&nbsp;&nbsp; * <span class='small'>인쇄시 머릿글이나 바닦글 또는 여백을 설정하시고 싶으시면 옵션에 인쇄 -> 페이지 설정에서 설정해주시면됩니다. </span>
				<br/>&nbsp;&nbsp; * <span class='small'>구분선이라 라인이 안나올시 배경색 및 이미지 인쇄 체크박스 체크 해주시면 됩니다.</span>
				<!--span class='small'>주문이 인쇄가 안되거나 ActiveX 컨트롤이 설치가 안될때 수동으로 받아서 설치해 주시기 바랍니다. <input type='button' value='다운로드' onclick=\"location.href='./scriptx/ScriptX.msi'\" /> </span-->
			</td>
		</tr>
	</table>
	</div>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>";

	$Contents .= HelpBox($help_title, $help_text,250);
}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
</form>
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$Script = "
<script language='javascript'>
function ToggleOrder(type){
	if(type == 'WDR'){
		if($('#view_wdr_order').attr('checked') == true || $('#view_wdr_order').attr('checked') == 'checked'){

			$.cookie('view_wdr_order', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			$.cookie('view_wdr_order', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
	}else if(type == 'project'){
		if($('#view_project_job').attr('checked') == true || $('#view_project_job').attr('checked') == 'checked'){
			$.cookie('view_project_job', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			$.cookie('view_project_job', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
	}
	document.location.reload();
}

$(window).on('load', function(){ 
        
            HelpTextChangeStatus('level1','CA'); 
        });
</script>
";

$P = new LayOut();
if($view_type == "offline_order"){
	$P->strLeftMenu = offline_order_menu();
}else if($view_type == 'sc_order'){
	$P->strLeftMenu = sns_menu();
}else if($view_type == "inventory"){
	$P->strLeftMenu = inventory_menu();
}else{
	$P->strLeftMenu = order_menu();
}

$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";//MenuHidden(false);<script language='javascript' src='../include/DateSelect.js'></script>\n
$P->addScript = "<script language='javascript' src='../order/orders.goods_list.js'></script>\n".$Script;
$P->Navigation = "배송관리 > $title_str";
$P->title = $title_str;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>