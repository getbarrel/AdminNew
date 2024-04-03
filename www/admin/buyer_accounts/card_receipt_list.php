<?
include_once("../class/layout.class");
//include_once("../sellertool/sellertool.lib.php");

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));

	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");

}

if($mode!="search"){
	$orderdate=1;
}

$db = new Database;
$odb = new Database;

$title_str  = "카드영수증발급 완료";


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
												주문일자
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td class='search_box_item'  colspan=3>
													".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' colspan='2'>검색항목 </th>
												<td class='search_box_item' colspan=3>
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
											</tr>
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
	
// 검색 조건 설정 부분
$where = "WHERE o.oid=od.oid and od.oid=op.oid and od.status <> '' and od.status !='SR' AND op.method = '".ORDER_METHOD_CARD."' ";

if($orderdate){
	$where .= "and o.order_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
}


if($search_type && $search_text){
	if($search_type == "combi_name"){
		$where .= "and (bname LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
	}else{
		$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
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


$sql="select o.oid, od.order_from, o.order_date, o.bname ,o.payment_agent_type, o.user_code, o.mem_group
FROM
	".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, shop_order_payment op
$where 
group by o.oid";
$db->query($sql);
$total = $db->total;


$sql = "select o.oid, od.order_from, o.order_date, o.bname ,o.payment_agent_type, o.user_code, o.mem_group
FROM 
	".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, shop_order_payment op
$where 
group by o.oid
ORDER BY o.order_date DESC
LIMIT $start, $max";
$db->query($sql);


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
	<form name=listform method=post action='notax.act.php' onsubmit='' target='act'><!--target='act'-->
	<input type='hidden' name='act' value='tax_affairs'>
	<input type=hidden id='oid' value='' >
	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='25' >
			<td width='30px' align='center' class='s_td' >순번</td>
			<td width='7%' align='center'  class='m_td' nowrap><b>판매처</b></td>
			<td width='*' align='center' class='m_td'><b>주문일자/주문번호</b></td>
			<td width='8%' align='center' class='m_td' nowrap><b>결제방법</b></td>
			<td width='6%' align='center' class='m_td' nowrap><b>결제상태</b></td>
			<td width='8%' align='center'  class='m_td' nowrap><b>과세</b></td>
			<td width='8%' align='center' class='m_td' nowrap><b>면세</b></td>
			<td width='8%' align='center' class='m_td' nowrap><b>합계</b></td>
			<td width='8%' align='center' class='m_td' nowrap><b>실 발급금액</b></td>
			<td width='6%' align='center' class='m_td' nowrap><b>증빙서류</b></td>
			<td width='6%' align='center' class='m_td' nowrap><b>발급여부</b></td>
			<td width='7%' align='center' class='e_td' nowrap><b>관리</b></td>
		</tr>";

	if($db->total){
		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);

			$no = $total - ($page - 1) * $max - $i;
			
			$real_payment_price=0;

			$tax_affairs_yn="완료";
			$m_useopt="카드영수증";
			$receipt_yn="발급완료";
			
			$sql = "SELECT 
				pay_type,pay_status,method,ic_date,tax_affairs_yn,tid,settle_module,
				(case when pay_type ='F' then -tax_price else tax_price end) as tax_price,
				(case when pay_type ='F' then -tax_free_price else tax_free_price end) as tax_free_price,
				(case when pay_type ='F' then -payment_price else payment_price end) as payment_price
			FROM shop_order_payment where oid='".$db->dt[oid]."' and method ='".ORDER_METHOD_CARD."' ";
			$odb->query($sql);
			
			for ($j = 0; $j < $odb->total; $j++)
			{
				$odb->fetch($j);


				$m_payment_price=$odb->dt[payment_price];
				$m_rcr_price=$odb->dt[payment_price];
				
				
				if($odb->dt[pay_status]==ORDER_STATUS_INCOM_COMPLETE){
					$real_payment_price += $m_payment_price;
					$color="";
				}else{
					$color="red";
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

				if($odb->dt[settle_module]=='nicepay'){
					$receipt_resulte = "<a href=\"javascript:printReceipt('".$odb->dt[tid]."')\">증빙문서보기</a>";
				}elseif($odb->dt[settle_module]=='inicis'||$odb->dt[settle_module]=='m_inicis'){
					$receipt_resulte = "<a href=\"javascript:printReceipt('준비중입니다.')\">증빙문서보기</a>";
				}elseif($odb->dt[settle_module]=='mobilians'){
					$receipt_resulte = "<a href=\"javascript:printReceipt('준비중입니다.')\">증빙문서보기</a>";
				}else{
					$receipt_resulte = "";
				}

				$Contents .= "
				<tr height=28 >";
					if($j==0){
						$Contents .= "
						<td class='list_box_td ' align='center' rowspan='".$odb->total."'>".$no."</td>
						<td  class='list_box_td ' style='line-height:140%' align=center rowspan='".$odb->total."'>".getOrderFromName($db->dt[order_from])."</td>
						<td class='list_box_td point' style='line-height:140%;' align=center rowspan='".$odb->total."'>
							".Black_list_check($db->dt[user_code],$db->dt[bname]."(<span class='small'>".$db->dt[mem_group]."</span>)")."
							<br/>".$db->dt[order_date]."
							<br/><a href=\"../order/orders.read.php?oid=".$db->dt[oid]."\" style='color:#007DB7;font-weight:bold;' class='small' rowspan='".$odb->total."'>".$db->dt[oid]."</a>
						</td>";
					}
					
					$Contents .= "
					<td class='list_box_td' align='center' nowrap>".getMethodStatus($odb->dt[method],"img")." (".$pay_type.")</td>
					<td class='list_box_td' align='center'  nowrap>".getOrderStatus($odb->dt[pay_status])."</td>
					<td class='list_box_td ".$color."' align='center'  nowrap>
						".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($odb->dt[tax_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td style='line-height:140%' align=center class='list_box_td'>
						".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($odb->dt[tax_free_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>";

					if($j==0){
						$Contents .= "
						<td class='list_box_td point' align='center'  nowrap rowspan='".$odb->total."'>
							{".$db->dt[oid]."_total}
						</td>
						<td class='list_box_td' align='center'  nowrap rowspan='".$odb->total."'>
							".($db->dt[receipt_yn]=="Y"?"-":$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($m_rcr_price)." ".$currency_display[$admin_config["currency_unit"]]["back"])."
						</td>
						<td class='list_box_td' align='center'  rowspan='".$odb->total."'>".$m_useopt."</td>
						<td class='list_box_td' align='center'  rowspan='".$odb->total."'>".$receipt_yn."</td>
						<td class='list_box_td' align='center' rowspan='".$odb->total."'>";

							$Contents.=$receipt_resulte;

						$Contents .= "
						</td>";
					}


				$Contents .= "
				</tr>";
			}

			$Contents = str_replace("{".$db->dt[oid]."_total}",$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($real_payment_price)." ".$currency_display[$admin_config["currency_unit"]]["back"],$Contents);
		}

	}else{
	$Contents .= "<tr height=50><td colspan='14' align=center>조회된 결과가 없습니다.</td></tr>
				";
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
/*
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
*/
$Contents .= "
</form>
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>
";

$Script="
<script type='text/javascript'>
<!--

function printReceipt(tid) {
	var status = 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=420,height=735';
	var url = 'https://pg.nicepay.co.kr/issue/IssueLoader.jsp?TID='+tid+'&type=0';
	window.open(url,'popupIssue',status);
}

//-->
</script>
";


$P = new LayOut();
$P->strLeftMenu = buyer_accounts_menu();
$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n".$Script;
$P->Navigation = "구매자정산관리 > $title_str";
$P->title = $title_str;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>