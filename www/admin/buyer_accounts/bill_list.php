<?
include_once("../class/layout.class");
include_once("../lib/barobill.lib.php");

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}

$db = new Database;
$odb = new Database;
$ddb = new Database;

if($mode!="search"){
	$orderdate=1;
}

//if($view_type=="order_apply"||$view_type=="period_apply"||$view_type=="preiod_ready"){
if($view_type=="order_apply"){
	$title_str  = "계산서 신청(주문별)";
}elseif($view_type=="period_apply"){
	$title_str  = "계산서 신청확인(기간별)";
}elseif($view_type=="preiod_ready"){
	$title_str  = "계산서 발급신청(기간별)";
}elseif($view_type=="complete"){
	$title_str  = "계산서 완료";
}


$vdate = date("Ymd", time());
$today = date("Ymd", time());
$firstday = date("Ymd", time()-84600*date("w"));
$lastday = date("Ymd", time()+84600*(6-date("w")));


$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$Contents = "

<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "구매자정산관리 > $title_str ")."</td>
	</tr>
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
		<form name='search_frm' method='get' action=''>
		<input type='hidden' name='view_type' value='".$view_type."' />
		<input type='hidden' name='mode' value='search' />
			<table width=100%  border=0>
				<tr height=25>
					<td colspan=2  align='left'  style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>주문정보 검색하기</b></td>
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
									<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0 >
									<TR>
										<TD bgColor=#ffffff style='padding:0 0 3px 0;'>
										<table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
										<col width=5%>
										<col width=10%>
										<col width=35%>
										<col width=15%>
										<col width=35%>";

									if($view_type=="order_apply" ||$view_type=="period_apply"){
										$Contents .= "
											<tr height=30>
												<th class='search_box_title' colspan='2'>판매처 선택 </th>
												<td class='search_box_item' nowrap colspan='3'>
													<table cellpadding=0 cellspacing=0 width='100%' border='0' >
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<TR height=25>
													<TD><input type='checkbox' name='order_from[]' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자체쇼핑몰</label></TD>
													<TD><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." ><label for='order_from_offline'>오프라인 영업</label></TD>
													<TD><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." ><label for='order_from_pos'>POS</label></TD>
													<TD></TD>
													<TD></TD>
													<TD></TD>";
													/*
													$db->query("select * from sellertool_site_info where disp='1' ");
													$sell_order_from=$db->fetchall();
													if(count($sell_order_from) > 0){

														for($i=0;$i<count($sell_order_from);$i++){
																$Contents .= "<TD><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></TD>";
														}
													}else{
													$Contents .= "
													<TD></TD>
													<TD></TD>
													<TD></TD>
													";
													}
													*/
										$Contents .= "
														</TR>
													</table>
												</td>
											</tr>";
									}

									if($view_type!="complete"){
									$Contents .= "
											<tr height=30>
												<th class='search_box_title' colspan='2'>검색항목 </th>
												<td class='search_box_item' colspan=3>
													<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<col width='200px'>
													<tr>
														<td >
														<select name='search_type' style='font-size:12px;'>
															<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자이름+입금자명+수취인명</option>
															<option value='bname' ".CompareReturnValue('bname',$search_type,' selected').">주문자이름</option>
															<option value='pname' ".CompareReturnValue('pname',$search_type,' selected').">상품이름</option>
															<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
															<option value='rname' ".CompareReturnValue('rname',$search_type,' selected').">수취인이름</option>
															<option value='bmobile' ".CompareReturnValue('bmobile',$search_type,' selected').">주문자핸드폰</option>
															<option value='rmobile' ".CompareReturnValue('rmobile',$search_type,' selected').">수취인핸드폰</option>
															<!--option value='deliverycode' ".CompareReturnValue('deliverycode',$search_type,' selected').">송장번호</option-->
														</select>
														</td>
														<td width='*'><input type='text' class=textbox name='search_text' size='30' value='$search_text' style=''></td>
														</tr>
														</table>
													</td>
											</tr>";
									}
										$Contents .= "
											<tr height=33>
												<th class='search_box_title' colspan='2'>";
												if($view_type=="preiod_ready"){
													$Contents .= "발급신청일";
												}elseif($view_type=="complete"){
													$Contents .= "계산서 작성일";
												}else{
													$Contents .= "주문일자";
												}
												$Contents .= "
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td class='search_box_item'  colspan=3>
													<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff>
														<tr>
															<TD  nowrap><input type='text' name='startDate' class='textbox point_color' value='".$startDate."' style='height:20px;width:100px;text-align:center;' id='start_datepicker'></TD>
															<TD style='padding:0 5px;' align=center> ~ </TD>
															<TD nowrap><input type='text' name='endDate' class='textbox point_color' value='".$endDate."' style='height:20px;width:100px;text-align:center;' id='end_datepicker'></TD>
															<td>";

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

							$Contents .= "
																<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
																<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
																<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
																<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
																<a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
																<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
																<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>

															</td>
														</tr>
													</table>
												</td>
											</tr>";
										if($view_type=="order_apply" ||$view_type=="period_apply"){
											$Contents .= "
											<tr height=30>
												<th class='search_box_title' colspan='2'>결제방법 : </th>
												<td class='search_box_item' nowrap>
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>&nbsp;
													<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$method,' checked')." ><label for='method_".ORDER_METHOD_CARD."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CARD)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CARD.".gif' align='absmiddle'></label>&nbsp;-->
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."' class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle'></label>&nbsp;
													<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle'></label>&nbsp;-->
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$method,' checked')." ><label for='method_".ORDER_METHOD_CASH."' class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle'></label>
												</td>
												<th class='search_box_title' >결제형태 : </th>
												<td class='search_box_item'>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_W' value='W' ".CompareReturnValue("W",$payment_agent_type,' checked')." ><label for='payment_agent_type_W' class='helpcloud' help_width='90' help_height='15' help_html='PC(웹)결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_w.gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_M' value='M' ".CompareReturnValue("M",$payment_agent_type,' checked')." ><label for='payment_agent_type_M' class='helpcloud' help_width='80' help_height='15' help_html='모바일결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_m.gif' align='absmiddle'></label>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_O' value='O' ".CompareReturnValue("O",$payment_agent_type,' checked')." ><label for='payment_agent_type_O' class='helpcloud' help_width='90' help_height='15' help_html='오프라인주문'><img src='../images/".$admininfo[language]."/s_payment_agent_type_o.gif' align='absmiddle'></label>
												</td>
											</tr>";
										}
									/*
									}elseif($view_type=="member"){

									}elseif($view_type=="complete"){

									}*/

							$Contents .= "
										</table>
										</TD>
									</TR>
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

<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
 <tr>";

	if($view_type=="order_apply"){//주문별
		$where = "WHERE o.oid = od.oid and od.status <> '' and od.status !='SR' AND o.taxsheet_yn = 'Y' and o.tax_period_yn='N' and (o.tax_affairs_yn = 'N' or bill_ix is null) and od.surtax_yorn = 'Y' ";
		$group_by_str=" group by o.oid ";
	}elseif($view_type=="period_apply"){//기간별 신청
		$where = "WHERE o.oid = od.oid and od.status <> '' and od.status !='SR' AND o.taxsheet_yn = 'Y' and o.tax_period_yn='Y' and (o.tax_affairs_yn = 'N' or bill_ix is null) and (o.tax_period_apply_date is null OR o.tax_period_apply_date ='0000-00-00' ) and od.surtax_yorn = 'Y' ";
		$group_by_str=" group by o.oid ";
	}elseif($view_type=="preiod_ready"){//발급대기
		$where = "WHERE o.oid = od.oid and od.status <> '' and od.status !='SR' AND o.taxsheet_yn = 'Y' and o.tax_period_yn = 'Y' and (o.tax_affairs_yn = 'N' or bill_ix is null) and o.tax_period_apply_date is not null and od.surtax_yorn = 'Y'";
		$group_by_str=" group by o.uid,o.tax_period_apply_date ";
	}elseif($view_type=="complete"){
		$where = "WHERE tax_div	='1' and tax_type = '2' ";
	}else{
		echo "잘못된 접근입니다.";
		exit;
	}

	if ($oid != "")		$where .= "and od.oid = '$oid' ";
	if ($bname != "")	$where .= "and bname = '$bname' ";
	if ($rname != "")	$where .= "and rname = '$rname' ";
	if ($rmobile != "")    $where .= "and rmobile = '$rmobile' ";
	if ($bmobile != "")    $where .= "and bmobile = '$bmobile' ";
	

	if($orderdate){
		if($view_type=="preiod_ready"){
			$where .= "and date_format(o.tax_period_apply_date,'%Y%m%d') between $startDate and $endDate ";
		}elseif($view_type=="complete"){
			$where .= "and date_format(signdate,'%Y%m%d') between $startDate and $endDate ";
		}else{
			$where .= "and date_format(o.date,'%Y%m%d') between $startDate and $endDate ";
		}
	}

	if($search_type && $search_text){
		if($search_type == "combi_name"){
			$where .= "and (bname LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
		}else{
			$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
		}
	}

	if(is_array($method)){
		for($i=0;$i < count($method);$i++){
			if($method[$i] != ""){
				if($method_str == ""){
					$method_str .= "'".$method[$i]."'";
				}else{
					$method_str .= ",'".$method[$i]."' ";
				}
			}
		}

		if($method_str != ""){
			$where .= "and o.method in ($method_str) ";
		}
	}else{
		if($method){
			$where .= "and o.method = '$method' ";
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


	if($view_type=="order_apply"||$view_type=="period_apply"){
		$sql = "select * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od $where $group_by_str";
		//$db->query($sql);
		$db->fetch();
		$total = $db->total;

		$sql = "select 
					o.*,od.order_from,
					sum(od.pcnt) as pcnt,
					sum(case when od.refund_status !='FC' or od.refund_status is null then od.ptprice-ifnull(od.use_coupon,'0')-ifnull(od.member_sale_price,'0') else '0' end) as g_product_price,
					sum(case when od.refund_status ='FC' then od.ptprice-ifnull(od.use_coupon,'0')-ifnull(od.member_sale_price,'0') else '0' end) as f_product_price
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od $where $group_by_str
				ORDER BY o.date DESC LIMIT $start, $max";
		//$db->query($sql);
	}elseif($view_type=="preiod_ready"){
		$sql = "select * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od $where $group_by_str";
		//$db->query($sql);
		$db->fetch();

		//echo $total;
		$sql = "select o.*,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,ccd.com_name, ccd.com_number from (
						select 
							o.*,od.order_from,
							sum(od.pcnt) as pcnt,
							sum(case when od.refund_status !='FC' or od.refund_status is null then od.ptprice-ifnull(od.use_coupon,'0')-ifnull(od.member_sale_price,'0') else '0' end) as g_product_price,
							sum(case when od.refund_status ='FC' then od.ptprice-ifnull(od.use_coupon,'0')-ifnull(od.member_sale_price,'0') else '0' end) as f_product_price
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od $where $group_by_str
						ORDER BY o.date DESC LIMIT $start, $max 
					) o left join common_user cu on (o.uid=cu.code) left join common_member_detail cmd using(code) left join common_company_detail ccd  on (cu.company_id=ccd.company_id)";
		//$db->query($sql);

	}else{

		//계산서 바로빌 상태 업데이트
		$sql = "select idx from (select idx,send_status FROM tax_sales $where LIMIT $start, $max) t where t.send_status not in ('4','5') ";
		//$db->query($sql);
		
		if($db->total){
			$t_list=$db->fetchall();
			updateTaxSalesStatus($t_list);
		}

		$sql = "select * FROM tax_sales $where ";
		//$db->query($sql);
		$db->fetch();
		$total = $db->total;
		
		$sql = "select t.*,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,ccd.com_name, ccd.com_number from (select * FROM tax_sales $where ORDER BY signdate DESC LIMIT $start, $max) t left join common_user cu using(code) left join common_member_detail cmd using(code) left join common_company_detail ccd  on (cu.company_id=ccd.company_id)";
		//$db->query($sql);
	}

$Contents .= "<td colspan=3 align=left><b class=blk>전체 건수 : $total 건</b></td>
	<td colspan=10 align=right>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	//$Contents .= "<a href='orders_excel2003.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></span>";
}else{
	//$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}

$Contents .= "
	</td>
  </tr>
  </table>";

	$Contents .= "
	<form name=listform method=post action='bill.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'><!--target='act'-->
	<input type=hidden id='oid' value='' >
	<input type=hidden id='uid' value='' >
	<input type=hidden id='idx' value='' >
	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>";
	
		if($view_type!="complete"){
			if($view_type=="preiod_ready"){
				$Contents .= "
				<tr height='25' >
					<td width='2%' align='center' class='s_td' rowspan='2'><input type=checkbox  name='all_fix' onclick='fixAll2(document.listform)'></td>";
			}else{
				$Contents .= "
				<tr height='25' >
					<td width='2%' align='center' class='s_td' rowspan='2'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>";
			}

			if($view_type=="preiod_ready"){
				$Contents .= "
				<td width='5%' align='center'  class='m_td' rowspan='2' ><b>발급신청일</b></td>
				<td width='8%' align='center' class='m_td' rowspan='2'><b>이름</b></td>
				<td width='8%' align='center'  class='m_td' rowspan='2' ><b>업체명</b></td>
				<td width='7%' align='center' class='m_td' rowspan='2' ><b>사업자번호</b></td>";
			}else{
				$Contents .= "
				<td width='5%' align='center'  class='m_td' rowspan='2' ><b>판매처</b></td>
				<td width='10%' align='center' class='m_td' rowspan='2'><b>주문일자<br/>/주문번호</b></td>
				<td width='8%' align='center'  class='m_td' rowspan='2' ><b>주문자명<br/>/수취인</b></td>
				<td width='5%' align='center' class='m_td' rowspan='2' ><b>결제방법<br/>/결제형태</b></td>";
			}

				$Contents .= "
				<td width='5%' align='center' class='m_td' rowspan='2' ><b>상품<br/>총수량</b></td>
				<td width='5%' align='center' class='m_td' rowspan='2' ><b>상품금액(+)</b></td>
				<td width='5%' align='center' class='m_td' rowspan='2' ><b>상품환불<br/>금액(-)</b></td>
				<td width='5%' align='center' class='m_td'  rowspan='2'><b>배송비(+)</b></td>
				<td width='5%' align='center' class='m_td' rowspan='2' ><b>배송비 <br/>추가/환불금액(+-)</b></td>
				<td width='15%' align='center' class='m_td' colspan='3'><b>현금결제금액(VAT 포함)</b></td>
				<td width='15%' align='center' class='m_td' colspan='3'><b>계산서용</b></td>";
				if($view_type!='period_apply'){
					$Contents .= "<td width='15%' align='center' class='e_td' colspan='3'><b>실 계산서 발행용</b></td>";
				}
			$Contents .= "
			</tr>
			<tr>
				<td width='5%' align='center' class='m_td' ><b>현금</b></td>
				<td width='5%' align='center' class='m_td' ><b>예치금</b></td>
				<td width='5%' align='center' class='m_td' ><b>합계</b></td>
				<td width='5%' align='center' class='m_td' ><b>공급가</b></td>
				<td width='5%' align='center' class='m_td' ><b>세액</b></td>
				<td width='5%' align='center' class='m_td' ><b>합계</b></td>";
			if($view_type!='period_apply'){
				$Contents .= "
				<td width='5%' align='center' class='m_td' ><b>공급가</b></td>
				<td width='5%' align='center' class='m_td' ><b>세액</b></td>
				<td width='5%' align='center' class='m_td' ><b>합계</b></td>";
			}
			$Contents .= "
			</tr>";
		}else{
			$Contents .= "
				<tr height='25' >
					<td width='2%' align='center' class='s_td' rowspan='2'><input type=checkbox  name='all_fix' onclick='fixAll3(document.listform)'></td>
					<td width='5%' align='center'  class='m_td' rowspan='2' ><b>발급신청일</b></td>
					<td width='5%' align='center' class='m_td' rowspan='2'><b>이름</b></td>
					<td width='8%' align='center'  class='m_td' rowspan='2' ><b>업체명</b></td>
					<td width='5%' align='center' class='m_td' rowspan='2' ><b>사업자번호</b></td>
					<td width='15%' align='center' class='m_td' colspan='3'><b>계산서용</b></td>
					<td width='5%' align='center' class='m_td' rowspan='2' ><b>계산서 작성일</b></td>
					<td width='5%' align='center' class='m_td'  rowspan='2'><b>승인번호</b></td>
					<td width='15%' align='center' class='e_td' colspan='3'><b>실 계산서 발행용</b></td>
					<td width='5%' align='center' class='m_td' rowspan='2'><b>상태</b></td>
					<td width='5%' align='center' class='e_td' rowspan='2'><b>관리</b></td>
				</tr>
				<tr>
					<td width='5%' align='center' class='m_td' ><b>공급가</b></td>
					<td width='5%' align='center' class='m_td' ><b>세액</b></td>
					<td width='5%' align='center' class='m_td' ><b>합계</b></td>
					<td width='5%' align='center' class='m_td' ><b>공급가</b></td>
					<td width='5%' align='center' class='m_td' ><b>세액</b></td>
					<td width='5%' align='center' class='m_td' ><b>합계</b></td>
				</tr>";

		}
	if($db->total){
		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);
			
			//$no = $total - ($page - 1) * $max - $i;
			if($view_type!="complete"){
				if ($db->dt[method] == ORDER_METHOD_CARD)
				{
					if($db->dt[bank] == ""){
						$method = "<label class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CARD)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CARD.".gif' align='absmiddle'></label>";
					}else{
						$method = $db->dt[bank];
					}
					$receipt_y = "카드결제";
				}elseif($db->dt[method] == ORDER_METHOD_BANK){
					$method = "<label class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>";
					if($db->dt[receipt_y] == "Y"){
						$receipt_y = "발행";
					}else if($db->dt[receipt_y] == "N"){
						$receipt_y = "미발행";
					}
				}elseif($db->dt[method] == ORDER_METHOD_PHONE){
					$method = "<label class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle'></label>";
				}elseif($db->dt[method] == ORDER_METHOD_AFTER){
					$method = "<label class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_AFTER)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_AFTER.".gif' align='absmiddle'></label>";
				}elseif($db->dt[method] == ORDER_METHOD_VBANK){
					$method = "<label class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>";
					if($db->dt[receipt_y] == "Y"){
						$receipt_y = "발행";
					}else if($db->dt[receipt_y] == "N"){
						$receipt_y = "미발행";
					}
				}elseif($db->dt[method] == ORDER_METHOD_ICHE){
					$method = "<label class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle'></label>";
					if($db->dt[receipt_y] == "Y"){
						$receipt_y = "발행";
					}else if($db->dt[receipt_y] == "N"){
						$receipt_y = "미발행";
					}
				}elseif($db->dt[method] == ORDER_METHOD_MOBILE){
					$method = "<label class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_MOBILE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_MOBILE.".gif' align='absmiddle'></label>";
				}elseif($db->dt[method] == ORDER_METHOD_ASCROW){
					$method = "가상계좌[에스크로]";
					if($db->dt[receipt_y] == "Y"){
						$receipt_y = "발행";
					}else if($db->dt[receipt_y] == "N"){
						$receipt_y = "미발행";
					}
				}elseif($db->dt[method] == ORDER_METHOD_NOPAY){
					$method = "무료결제";
					if($db->dt[receipt_y] == "Y"){
						$receipt_y = "발행";
					}else if($db->dt[receipt_y] == "N"){
						$receipt_y = "미발행";
					}
				}elseif($db->dt[method] == ORDER_METHOD_CASH){
					$method = "<label class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle'></label>";
					if($db->dt[receipt_y] == "Y"){
						$receipt_y = "발행";
					}else if($db->dt[receipt_y] == "N"){
						$receipt_y = "미발행";
					}
				}else{
					$receipt_y = "제휴사";
				}
				
				/*
				$odb->query("select 
							sum(case when payment_status='G' then expect_delivery_price  else '0' end) as g_delivery_price,
							sum(case when payment_status='G' then saveprice  else '0' end) as g_saveprice,
							sum(case when payment_status='F' then -delivery_price  else '0' end) as f_delivery_price,
							sum(case when payment_status='F' then -saveprice  else '0' end) as f_saveprice,
							sum(case when payment_status='A' then delivery_price  else '0' end) as a_delivery_price
						 from shop_order_price where ".($view_type=="preiod_ready" ? "oid in (select oid from shop_order where tax_period_apply_date = '".$db->dt[tax_period_apply_date]."' and uid='".$db->dt[uid]."' ) " : "oid='".$db->dt[oid]."'")."");
				$odb->fetch();
				*/

				$order_total_cash_price=$db->dt[g_product_price]+$db->dt[f_product_price]+$odb->dt[g_delivery_price]+$odb->dt[f_delivery_price]+$odb->dt[a_delivery_price];
				$order_total_save_price=$odb->dt[g_saveprice]+$odb->dt[f_saveprice];

				$order_total_price=$order_total_cash_price+$order_total_save_price;

				$Contents .= "<tr height=28 >";
					if($view_type=="preiod_ready"){
						$Contents .= "<td class='list_box_td ' align='center'>
								<input type=checkbox name='uid[]' id='uid' value='".$db->dt[uid]."' >
								<input type=hidden name='tax_period_apply_date[]' value='".$db->dt[tax_period_apply_date]."' >
							</td>";
					}else{
						$Contents .= "<td class='list_box_td ' align='center'><input type=checkbox name='oid[]' id='oid' value='".$db->dt[oid]."' ></td>";
					}
					
					if($view_type=="preiod_ready"){
						$Contents .= "<td  class='list_box_td ' style='line-height:140%' align=center>".$db->dt[tax_period_apply_date]."</td>";
						$Contents .= "<td class='list_box_td point' style='line-height:140%;' align=center>".$db->dt[name]."</td>";
						$Contents .= "<td style='line-height:140%' align=center class='list_box_td'>".$db->dt[com_name]."</td>";
						$Contents .= "<td class='list_box_td ' align='center' nowrap>".$db->dt[com_number]."</b></td>";
					}else{
						$Contents .= "<td  class='list_box_td ' style='line-height:140%' align=center>".getOrderFromName($db->dt[order_from])."</td>";
						$Contents .= "<td class='list_box_td point' style='line-height:140%;' align=center>".$db->dt[date]."<br><a href=\"../order/orders.read.php?oid=".$db->dt[oid]."&pid=".$db->dt[pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$db->dt[oid]."</a></td>";
						$Contents .= "<td style='line-height:140%' align=center class='list_box_td'>".($db->dt[user_id] != "" ? "<a href=\"javascript:PopSWindow('../member/member_view.php?code=".$db->dt[user_id]."',950,500,'member_info')\" >".Black_list_check($db->dt[user_id],$db->dt[bname]."(<span class='small'>".$db->dt[mem_group]."</span>)")."</a>":$db->dt[bname]."")." / ".$db->dt[rname]."</span></td>";
						$Contents .= "<td class='list_box_td ' align='center' nowrap>".$method." / ".getPaymentAgentType($db->dt[payment_agent_type],'img')."</b></td>";
					}

					$Contents .= "
								<td class='list_box_td' align='center'  nowrap>".number_format($db->dt[pcnt])."</td>
								<td class='list_box_td' align='center'  nowrap>".number_format($db->dt[g_product_price])."</td>";
			$Contents .= "<td class='list_box_td' align='center'  nowrap>".number_format($db->dt[f_product_price])."</td>";
			$Contents .= "<td class='list_box_td' align='center'  nowrap>".number_format($odb->dt[g_delivery_price])."</td>
								<td class='list_box_td' align='center'  nowrap>".number_format($odb->dt[f_delivery_price]+$odb->dt[a_delivery_price])."</td>
								<td class='list_box_td' align='center'  nowrap>".number_format($order_total_cash_price)."</td>
								<td class='list_box_td' align='center'  nowrap>".number_format($order_total_save_price)."</td>
								<td class='list_box_td' align='center' nowrap>".number_format($order_total_price)."</td>";
					if($view_type=="preiod_ready"){
						$Contents .= "
								<td class='list_box_td' align='center'  nowrap>
									".number_format($order_total_price)." <input type='hidden' name='expect_coprice[".$db->dt[uid]."][".$db->dt[tax_period_apply_date]."]' value='".($order_total_price)."' />
								</td>
								<td class='list_box_td' align='center'  nowrap>
									0 <input type='hidden' name='expect_tax[".$db->dt[uid]."][".$db->dt[tax_period_apply_date]."]' value='0' />
								</td>
								<td class='list_box_td' align='center' nowrap>
									".number_format($order_total_price)." <input type='hidden' name='expect_total[".$db->dt[uid]."][".$db->dt[tax_period_apply_date]."]' value='".($order_total_price)."' />
								</td>
								<td class='list_box_td' align='center'  ><input type='text' class='number' name='coprice[".$db->dt[uid]."][".$db->dt[tax_period_apply_date]."]' value='".($order_total_price)."' style='width:70%;background-color:#efefef;' readonly /></td>
								<td class='list_box_td' align='center'  ><input type='text' class='number' name='tax[".$db->dt[uid]."][".$db->dt[tax_period_apply_date]."]' value='0' style='width:70%;background-color:#efefef;' readonly/></td>
								<td class='list_box_td' align='center' ><input type='text' class='number' name='total[".$db->dt[uid]."][".$db->dt[tax_period_apply_date]."]' value='".($order_total_price)."' onkeyup=\"tax_compute2('".$db->dt[uid]."','".$db->dt[tax_period_apply_date]."',this.value)\" style='width:70%' /></td>";
					}else{
						$Contents .= "
								<td class='list_box_td' align='center'  nowrap>
									".number_format($order_total_price)." <input type='hidden' name='expect_coprice[".$db->dt[oid]."]' value='".($order_total_price)."' />
								</td>
								<td class='list_box_td' align='center'  nowrap>
									0 <input type='hidden' name='expect_tax[".$db->dt[oid]."]' value='0' />
								</td>
								<td class='list_box_td' align='center' nowrap>
									".number_format($order_total_price)." <input type='hidden' name='expect_total[".$db->dt[oid]."]' value='".($order_total_price)."' />
								</td>";
							if($view_type!='period_apply'){
								$Contents .= "
								<td class='list_box_td' align='center'  ><input type='text' class='number' name='coprice[".$db->dt[oid]."]' value='".($order_total_price)."' style='width:70%;background-color:#efefef;' readonly /></td>
								<td class='list_box_td' align='center'  ><input type='text' class='number' name='tax[".$db->dt[oid]."]' value='0' style='width:70%;background-color:#efefef;' readonly/></td>
								<td class='list_box_td' align='center' ><input type='text' class='number' name='total[".$db->dt[oid]."]' value='".($order_total_price)."' onkeyup=\"tax_compute('".$db->dt[oid]."',this.value)\" style='width:70%' /></td>";
							}
					}
						$Contents .= "
						</tr>";
			}else{
				$Contents .= "<tr height=28 >";
				$Contents .= "<td class='list_box_td ' align='center'><input type=checkbox name='idx[]' id='idx' value='".$db->dt[idx]."' ></td>";
				$Contents .= "<td  class='list_box_td ' style='line-height:140%' align=center>".$db->dt[apply_date]."</td>";
				$Contents .= "<td class='list_box_td point' style='line-height:140%;' align=center><a href=\"javascript:PopSWindow('../member/member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".$db->dt[name]."</a></td>";
				$Contents .= "<td style='line-height:140%' align=center class='list_box_td'>".$db->dt[com_name]."</td>";
				$Contents .= "<td class='list_box_td ' align='center' nowrap>".$db->dt[com_number]."</b></td>";
				$Contents .= "<td class='list_box_td' align='center'  nowrap>".number_format($db->dt[expect_supply_price])."</td>
										<td class='list_box_td' align='center'  nowrap>".number_format($db->dt[expect_tax_price])."</td>";
				$Contents .= "<td class='list_box_td' align='center'  nowrap>".number_format($db->dt[expect_total_price])."</td>";
				$Contents .= "<td class='list_box_td' align='center'  nowrap>".$db->dt[signdate]."</td>
										<td class='list_box_td' align='center' nowrap>".$db->dt[national_tax_no]."</td>";
				$Contents .= "<td class='list_box_td' align='center'  nowrap>".number_format($db->dt[supply_price])."</td>
										<td class='list_box_td' align='center'  nowrap>".number_format($db->dt[tax_price])."</td>
										<td class='list_box_td' align='center'  nowrap>".number_format($db->dt[total_price])."</td>
										<td class='list_box_td' align='center'  nowrap>".getTaxSalesSendStatus($db->dt[send_status])."</td>
										<td class='list_box_td' align='center'  nowrap>
											<input type='button' value='주문내역' onclick=\"PopSWindow('./taxbill_order_detail.php?type=bill&bill_ix=".$db->dt[idx]."',1300,660,'taxbill_detail')\" /><br/>";
										if($db->dt[status]=='0'){
											$Contents .= "<input type='button' value='재발행' onclick=\"again_taxbill('".$db->dt[idx]."');\" /> ";
										}else{
											$Contents .= GetTaxInvoicePopUpURL($_SESSION[admininfo][mall_ename].$db->dt[idx],"bill");
										}
				$Contents .= "</td>
									</tr>";
			}
		}

	}else{
		if($view_type!='period_apply'){
			$Contents .= "<tr height=50><td colspan='19' align=center>조회된 결과가 없습니다.</td></tr>";
		}else{
			$Contents .= "<tr height=50><td colspan='16' align=center>조회된 결과가 없습니다.</td></tr>";
		}
	}
	$Contents .= "
	  </table>";

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
	</tabel>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
  <tr height=40>
    <td colspan='13' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string."#list_top","")."&nbsp;</td>
  </tr>
</table>";

if($view_type!="complete"){
$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind' value='' onclick=\"\" checked><label for='update_kind'>계산서 발급</label>
	</nobr>";

	$help_text = "
	<script type='text/javascript'>
	<!--

	//-->
	</script>

	<div id='' style='margin-top:15px;'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level0_status'>
			<td class='input_box_title'> <b>처리상태</b></td>
			<td class='input_box_item'>";
			if($view_type=="order_apply"){
				$help_text .= "<input type='radio' name='act' id='order_bill' value='order_bill' onclick=\"\" checked><label for='order_bill' >발급완료 : 주문별(개별) 세금게산서 발급</label>";
			}elseif($view_type=="period_apply"){
				$help_text .= "<input type='radio' name='act' id='preiod_ready' value='preiod_ready' onclick=\"\" checked><label for='preiod_ready' >기간별 발급 신청</label>";
			}elseif($view_type=="preiod_ready"){
				$help_text .= "<input type='radio' name='act' id='preiod_bill' value='preiod_bill' onclick=\"\" checked><label for='preiod_bill' >발급완료 : 기간별 계산서 발급</label>";
			}
		$help_text .= "
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

	$Contents .= HelpBox($help_title, $help_text,300);
}

$Contents .= "
</form>
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>
";

$Script="
<script type='text/javascript'>
<!--
	function again_bill(idx){
		if(confirm('해당 계산서를 재발행 하시겠습니까?')){
			window.frames['act'].location.href='./bill.act.php?act=again_bill&idx='+idx;
		}
	}
//-->
</script>
";


$P = new LayOut();
$P->strLeftMenu = buyer_accounts_menu();
if($view_type=="list"){
	$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";//MenuHidden(false);
}
$P->addScript = "<script language='javascript' src='./bill.js'></script>\n".$Script;
$P->Navigation = "구매자정산관리 > $title_str";
$P->title = $title_str;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>