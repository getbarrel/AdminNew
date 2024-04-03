<?
include_once("../class/layout.class");
//include_once("../sellertool/sellertool.lib.php");

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));

	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");

	$TaxstartDate = $startDate;
	$TaxendDate = $endDate;
}

if($mode!="search"){
	$orderdate=1;
}

$db = new Database;
$odb = new Database;


if($view_type=="list"){
	$title_str  = "미발급리스트";
}else{
	$title_str  = "미발급 세무신고 완료";
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
							if($view_type=="list"){
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
													<TD><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." ><label for='order_from_pos'>POS</label></TD>";
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
										$Contents .= "
														</TR>
													</table>
												</td>
											</tr>
											<tr height=33>
												<th class='search_box_title' colspan='2'>
												입금 확인일자
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."  ></th>
												<td class='search_box_item'  colspan=3>
													".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' colspan='2'>결제방법 : </th>
												<td class='search_box_item' nowrap>
													<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$method,' checked')." ><label for='method_".ORDER_METHOD_CARD."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CARD)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CARD.".gif' align='absmiddle'></label-->&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."' class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle'></label>&nbsp;
													<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle'></label-->&nbsp;
													<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$method,' checked')." ><label for='method_".ORDER_METHOD_CASH."' class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle'></label-->
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$method,' checked')." ><label for='method_".ORDER_METHOD_SAVEPRICE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_SAVEPRICE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_SAVEPRICE.".gif' align='absmiddle'></label>&nbsp;
												</td>
												<th class='search_box_title' >세무처리여부 : </th>
												<td class='search_box_item' nowrap>
													<input type='radio' name='tax_affairs_yn' id='tax_affairs_yn' value='' ".CompareReturnValue("",$tax_affairs_yn,' checked')." ><label for='tax_affairs_yn' >전체</label>&nbsp;
													<input type='radio' name='tax_affairs_yn' id='tax_affairs_y' value='Y' ".CompareReturnValue("Y",$tax_affairs_yn,' checked')." ><label for='tax_affairs_y' >완료</label>&nbsp;
													<input type='radio' name='tax_affairs_yn' id='tax_affairs_n' value='N' ".CompareReturnValue("N",$tax_affairs_yn,' checked')." ><label for='tax_affairs_n' >미완료</label>&nbsp;
												</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' colspan='2'>검색항목 </th>
												<td class='search_box_item' colspan='3'>
													<table cellpadding='3' cellspacing='0' border='0' width='100%'>
														<col width='80px'>
														<tr>
															<td >
															<select name='search_type' style='font-size:12px;'>
																<option value='o.oid' ".CompareReturnValue('o.oid',$search_type,' selected').">주문번호</option>
																<option value='o.bname' ".CompareReturnValue('o.bname',$search_type,' selected').">주문자이름</option>
															</select>
															</td>
															<td width='*'><input type='text' class=textbox name='search_text' size='30' value='$search_text' style=''></td>
														</tr>
													</table>
												</td>
												<!--th class='search_box_title' >결제형태 : </th>
												<td class='search_box_item'>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_W' value='W' ".CompareReturnValue("W",$payment_agent_type,' checked')." ><label for='payment_agent_type_W' class='helpcloud' help_width='90' help_height='15' help_html='PC(웹)결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_w.gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_M' value='M' ".CompareReturnValue("M",$payment_agent_type,' checked')." ><label for='payment_agent_type_M' class='helpcloud' help_width='80' help_height='15' help_html='모바일결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_m.gif' align='absmiddle'></label>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_O' value='O' ".CompareReturnValue("O",$payment_agent_type,' checked')." ><label for='payment_agent_type_O' class='helpcloud' help_width='90' help_height='15' help_html='오프라인주문'><img src='../images/".$admininfo[language]."/s_payment_agent_type_o.gif' align='absmiddle'></label>
												</td-->
											</tr>
											";
							}else{
									$Contents .= "
											<tr height=33>
												<th class='search_box_title' colspan='2'>
													입금 확인일자 <input type='hidden' name='orderdate' value='1'>
												</th>
												<td class='search_box_item'  colspan=3>
													".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>
											<tr height=33>
												<th class='search_box_title' colspan='2'>
												세무신고 처리일자
												</th>
												<td class='search_box_item'  colspan=3>
													".search_date('TaxstartDate','TaxendDate',$TaxstartDate,$TaxendDate)."
												</td>
											</tr>";
							}
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

	//검색 조건 설정 부분
	if($view_type=="list"){
		$where = "WHERE o.oid=od.oid and od.oid=op.oid and od.status <> '' and od.status !='SR' AND op.pay_type='G' AND op.taxsheet_yn = 'N' AND op.receipt_yn = 'N' and op.method not in ('".ORDER_METHOD_RESERVE."','".ORDER_METHOD_CARD."') ";
	}else{
		$where = "WHERE op.tax_affairs_yn = 'Y' AND op.nar_ix=0 ";
		$where2 .= "WHERE date_format(regdate,'%Y%m%d') between '".str_replace("-","",$TaxstartDate)."' and '".str_replace("-","",$TaxendDate)."' ";
	}

	if($orderdate){
		$where .= "and date_format(op.ic_date,'%Y%m%d') between '".str_replace("-","",$startDate)."' and '".str_replace("-","",$endDate)."' ";
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
			$where .= "and op.method in ($method_str) ";
		}
	}else{
		if($method){
			$where .= "and op.method = '$method' ";
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

	if($tax_affairs_yn!=""){
		$where .= "and op.tax_affairs_yn = '$tax_affairs_yn' ";
	}



	/*
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
	*/

	if($view_type=="list"){

		$sql="select * from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, shop_order_payment op $where group by o.oid";
		$db->query($sql);
		$total = $db->total;


		$sql="select o.oid, od.order_from, o.order_date, o.bname ,o.payment_agent_type, o.user_code, o.mem_group
			from
				".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, shop_order_payment op
			$where
			group by o.oid
			ORDER BY order_date DESC
			LIMIT $start, $max";
		$db->query($sql);

	}else{

		$sql="select opay_ix from shop_order_payment op $where ";
		$db->query($sql);

		$oid_str ="";

		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			if($i == 0)						$opay_ix_str=$db->dt[opay_ix];
			else							$opay_ix_str.=",".$db->dt[opay_ix];
		}

		$sql = "select * from (
						select
							'0' as nar_ix,
							'' as regdate,
							'".$startDate."' as sdate,
							'".$endDate."' as edate,
							payment_price,
							save_price,
							refund_price,
							(payment_price + save_price - refund_price) as total_price,
							'' as report_price
						from (
							SELECT
								sum(case when pay_type='F' and method!='".ORDER_METHOD_SAVEPRICE."' then '0' else payment_price end) as payment_price,
								sum(case when pay_type='F' and method='".ORDER_METHOD_SAVEPRICE."' then '0' else payment_price end) as save_price,
								sum(case when pay_type='F' then payment_price else '0' end) as refund_price
							FROM shop_order_payment op
							$where
						) a
						union
						select
							nar_ix,
							date_format(regdate,'%Y-%m-%d') as regdate,
							sdate,
							edate,
							payment_price,
							save_price,
							refund_price,
							total_price,
							report_price
						from shop_notax_affairs_report
						$where2
					) l ";
		$db->query($sql);
		$db->fetch();
		$total = $db->total;

		$sql = "select * from (
						select
							'0' as nar_ix,
							'' as regdate,
							'".$startDate."' as sdate,
							'".$endDate."' as edate,
							payment_price,
							save_price,
							refund_price,
							(payment_price + save_price - refund_price) as total_price,
							'' as report_price
						from (
							SELECT
								sum(case when pay_type='F' and method!='".ORDER_METHOD_SAVEPRICE."' then '0' else payment_price end) as payment_price,
								sum(case when pay_type='F' and method='".ORDER_METHOD_SAVEPRICE."' then '0' else payment_price end) as save_price,
								sum(case when pay_type='F' then payment_price else '0' end) as refund_price
							FROM shop_order_payment op
							$where
						) a
						union
						select
							nar_ix,
							date_format(regdate,'%Y-%m-%d') as regdate,
							sdate,
							edate,
							payment_price,
							save_price,
							refund_price,
							total_price,
							report_price
						from shop_notax_affairs_report
						$where2
					) l
					LIMIT $start, $max";
		//echo $sql;
		$db->query($sql);
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



if($view_type=="list"){
	$Contents .= "
	<form name=listform method=post action='notax.act.php' onsubmit='return select_tax_affairs_apply(this)' target='act'><!--target='act'-->
	<input type='hidden' name='act' value='tax_affairs'>
	<input type=hidden id='oid' value='' >
	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='25' >
			<td width='15px' align='center' class='s_td' ><input type=checkbox  name='all_fix' onclick='fixAll2(document.listform)'></td>
			<td width='10%' align='center'  class='m_td' nowrap><b>판매처</b></td>
			<td width='*' align='center' class='m_td'><b>주문일자/주문번호</b></td>
			<td width='12%' align='center'  class='m_td' nowrap><b>주문자명</b></td>
			<td width='8%' align='center' class='m_td' nowrap><b>결제방법</b></td>
			<td width='7%' align='center' class='m_td' nowrap><b>결제상태</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b>실 결제금액</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b>합계</b></td>
			<td width='6%' align='center' class='m_td' nowrap><b>증빙서류</b></td>
			<td width='7%' align='center' class='m_td' nowrap><b>세무처리</b></td>
			<td width='10%' align='center' class='e_td' nowrap><b>관리</b></td>
		</tr>";

	if($db->total){
		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);

			$sql = "SELECT
				pay_type,pay_status,method,ic_date,tax_affairs_yn,
				(case when pay_type ='F' then -payment_price else payment_price end) as payment_price
			FROM shop_order_payment where oid='".$db->dt[oid]."' and method !='".ORDER_METHOD_RESERVE."' ";
			$odb->query($sql);

			$real_payment_price = 0;

			for ($j = 0; $j < $odb->total; $j++)
			{
				$odb->fetch($j);

				if($odb->dt[pay_status]==ORDER_STATUS_INCOM_COMPLETE){
					$real_payment_price += $odb->dt[payment_price];
					$color="";
				}else{
					$color="red";
				}

				if($odb->dt[tax_affairs_yn]=="Y"){
					$tax_affairs_yn="완료";
				}else{
					$tax_affairs_yn="미완료";
				}

				if($odb->dt[pay_type]=="G"){
					$pay_type="정상";
				}elseif($odb->dt[pay_type]=="F"){
					$pay_type="환불";
				}elseif($odb->dt[pay_type]=="A"){
					$pay_type="추가";
				}else{
					$pay_type="-";
				}

				$Contents .= "
					<tr height=28 >";

					if($j==0){
						$Contents .= "
						<td class='list_box_td' align='center' rowspan='".$odb->total."'><input type=checkbox name='oid[]' id='oid' value='".$db->dt[oid]."' ></td>
						<td  class='list_box_td' style='line-height:140%' align=center rowspan='".$odb->total."'>".getOrderFromName($db->dt[order_from])."</td>
						<td class='list_box_td point' style='line-height:140%;' align=center rowspan='".$odb->total."'>".$db->dt[order_date]."<br><a href=\"../order/orders.read.php?oid=".$db->dt[oid]."\" style='color:#007DB7;font-weight:bold;' class='small' rowspan='".$odb->total."'>".$db->dt[oid]."</a></td>
						<td style='line-height:140%' align=center class='list_box_td' rowspan='".$odb->total."'>
							".Black_list_check($db->dt[user_code],$db->dt[bname]."(<span class='small'>".$db->dt[mem_group]."</span>)")."
						</td>";
					}

					$Contents .= "
						<td class='list_box_td' align='center' nowrap>".getMethodStatus($odb->dt[method],"img")." (".$pay_type.")</td>
						<td class='list_box_td' align='center' nowrap>".getOrderStatus($odb->dt[pay_status])."</td>
						<td class='list_box_td point ".$color."' align='center' nowrap>
							".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($odb->dt[payment_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
						</td>";

					if($j==0){
						$Contents .= "
						<td class='list_box_td' align='center' rowspan='".$odb->total."' nowrap>{".$db->dt[oid]."_total}</td>
						<td class='list_box_td' align='center' rowspan='".$odb->total."'>미발급</td>
						<td class='list_box_td' align='center' rowspan='".$odb->total."'>".$tax_affairs_yn."</td>
						<td class='list_box_td' align='center'rowspan='".$odb->total."'>";
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
							if($odb->dt[tax_affairs_yn]!="Y"){
								//$Contents .= "<img src='../images/".$admininfo[language]."/btn_confirm.gif' align='absmiddle' style='cursor:pointer' onclick=\"tax_affairs_apply ('".$db->dt[oid]."')\" /> ";
								$Contents .= "
								<a href=\"javascript:PopSWindow('./receipt_apply.pop.php?oid=".$db->dt[oid]."&m_useopt=0',300,100,'receipt_apply');\">소득공제신청</a>
								<br/><a href=\"javascript:PopSWindow('./receipt_apply.pop.php?oid=".$db->dt[oid]."&m_useopt=1',300,100,'receipt_apply');\">지출증빙신청</a>
								<br/><a href=\"javascript:PopSWindow('./taxbill_apply.pop.php?oid=".$db->dt[oid]."',300,100,'taxbill_apply');\">세금&계산서신청</a>";
							}
						}
						$Contents .= "
						</td>";
					}

					$Contents .= "
					</tr>";
			}

			$Contents = str_replace("{".$db->dt[oid]."_total}",$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($real_payment_price)." ".$currency_display[$admin_config["currency_unit"]]["back"],$Contents);
		}

	}else{
		$Contents .= "<tr height=50><td colspan='12' align=center>조회된 결과가 없습니다.</td></tr>";
	}

	$Contents .= "
	  </table>";
}else{
	$Contents .= "
	<form name=listform method=post action='notax.act.php' target='act'><!--target='act'-->
	<input type='hidden' name='act' value='report_price'>
	<input type='hidden' name='nar_ix' value=''>
	<input type='hidden' name='opay_ix_str' value='".$opay_ix_str."'>
	<input type='hidden' name='sdate' value=''>
	<input type='hidden' name='edate' value=''>
	<input type='hidden' name='payment_price' value=''>
	<input type='hidden' name='save_price' value=''>
	<input type='hidden' name='refund_price' value=''>
	<input type='hidden' name='total_price' value=''>

	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='25' >
			<td width='9%' align='center'  class='m_td' nowrap><b>세무신고 처리일</b></td>
			<td width='14%' align='center' class='m_td'><b>신고날짜</b></td>
			<td width='12%' align='center'  class='m_td' nowrap><b>실결제금액</b></td>
			<td width='9%' align='center' class='m_td' nowrap><b>예치금사용금액</b></td>
			<td width='9%' align='center' class='m_td' nowrap><b>환불금액</b></td>
			<td width='8%' align='center' class='m_td' nowrap><b>합계</b></td>
			<td width='8%' align='center' class='m_td' nowrap><b>실 세무신고액</b></td>
			<td width='8%' align='center' class='m_td' nowrap><b>증빙서류</b></td>
		</tr>";

	if($db->total){
		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);

			$Contents .= "
			<tr height=28 >
				<td  class='list_box_td ' style='line-height:140%' align=center>".$db->dt[regdate]."</td>
				<td class='list_box_td point' style='line-height:140%;".($i!=0 ?"background:#e4e4ee;":"")."' align=center>".$db->dt[sdate]." ~ ".$db->dt[edate]."</td>
				<td style='line-height:140%' align=center class='list_box_td'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[payment_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
				<td class='list_box_td' align='center'  nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[save_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td class='list_box_td point' align='center'  nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format(-$db->dt[refund_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";
				$Contents .= "<td class='list_box_td' align='center'  nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[total_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";
				$Contents .= "<td class='list_box_td' align='center'  nowrap>
				<span class='helpcloud' help_height='30' help_html='더블클릭 하시면 실 세무신고액이 수정됩니다.'><input type='text' name='report_price[".$db->dt[nar_ix]."]' value='".$db->dt[report_price]."' class='number' style='width:60px;margin:2px;' ondblclick=\"if(confirm('실 세무신고액을 수정하시겠습니까?')){report_price('".$db->dt[nar_ix]."','".$db->dt[sdate]."','".$db->dt[edate]."','".$db->dt[payment_price]."','".$db->dt[save_price]."','".$db->dt[refund_price]."','".$db->dt[total_price]."');}\" /></span> 원 </td>";
				$Contents .= "<td class='list_box_td' align='center'  nowrap>미발급</td>
			</tr>";
		}

	}else{
		$Contents .= "<tr height=50><td colspan='8' align=center>조회된 결과가 없습니다.</td></tr>";
	}
	$Contents .= "
	  </table>";
}

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

if($view_type=="list"){
	$help_title = "
		<nobr>
			<select name='update_type'>
				<!--option value='1'>검색한주문 전체에게</option-->
				<option value='2'>선택한주문 전체에게</option>
			</select>
			<input type='radio' name='update_kind' id='update_kind' value='' onclick=\"\" checked><label for='update_kind'>세무처리 완료처리</label>
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
				<td class='input_box_item'>
					<input type='radio' name='tax_affairs_yn' id='tax_affairs_y' value='Y' onclick=\"\" checked><label for='tax_affairs_y' >세무신고완료</label>
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

	function report_price(nar_ix,sdate,edate,payment_price,save_price,refund_price,total_price){

		$('[name=nar_ix]').val(nar_ix);
		$('[name=sdate]').val(sdate);
		$('[name=edate]').val(edate);
		$('[name=payment_price]').val(payment_price);
		$('[name=save_price]').val(save_price);
		$('[name=refund_price]').val(refund_price);
		$('[name=total_price]').val(total_price);
		$('form[name=listform]').submit();
	}

	function tax_affairs_apply (oid){
		if(confirm('세무처리를 완료 하시겠습니까 ? ')){
			window.frames['iframe_act'].location.href= 'notax.act.php?act=tax_affairs&tax_affairs_yn=Y&oid='+oid;
		}
	}

	function select_tax_affairs_apply (frm){
		var checked_bool = false;

		for(i=0;i < frm.oid.length;i++){
			if(frm.oid[i].checked){
				checked_bool = true;
			}
		}


		if(!checked_bool){
			alert(language_data['orders.js']['J'][language]);//상태변경하실 주문을 한개이상 선택하셔야 합니다
			return false;
		}else{
			if(confirm('선택하신 주문을 세무처리를 완료 하시겠습니까 ? ')){
				frm.submit();
			}
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
$P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n".$Script;
$P->Navigation = "구매자정산관리 > $title_str";
$P->title = $title_str;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>