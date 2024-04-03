<?
include_once("../class/layout.class");
//include_once("../lib/barobill.lib.php");

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));

	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");
}

$db = new Database;
$odb = new Database;

if($mode!="search"){
	$orderdate=1;
}

if($view_type=="order_apply"){
	$title_str  = "세금&계산서 신청";
}elseif($view_type=="complete"){
	$title_str  = "세금&계산서 완료";
}


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

									//if($view_type=="order_apply"){
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
													<TD></TD>
														</TR>
													</table>
												</td>
											</tr>";
									//}

									$Contents .= "
										<tr height=33>
											<th class='search_box_title' colspan='2'>";
											/*
											if($view_type=="preiod_ready"){
												$Contents .= "발급신청일";
											}elseif($view_type=="complete"){
												$Contents .= "세금계산서 작성일";
											}else{
												$Contents .= "주문일자";
											}
											*/
											$Contents .= "입금확인일자";
											$Contents .= "
											<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
											<td class='search_box_item'  colspan=3>
												".search_date('startDate','endDate',$startDate,$endDate)."
											</td>
										</tr>";

									//if($view_type!="complete"){
										$Contents .= "
											<tr height=30>
												<th class='search_box_title' colspan='2'>검색항목 </th>
												<td class='search_box_item'>
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
												<th class='search_box_title'>결제방법 : </th>
												<td class='search_box_item' nowrap >
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."' class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle'></label>&nbsp;
													<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle'></label>&nbsp;-->
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$method,' checked')." ><label for='method_".ORDER_METHOD_SAVEPRICE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_SAVEPRICE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_SAVEPRICE.".gif' align='absmiddle'></label>&nbsp;
												</td>
											</tr>";
									//}

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

	if($view_type=="order_apply"){
		$where = "WHERE o.oid=od.oid and od.oid=op.oid and od.status <> '' and od.status !='SR' AND op.pay_type='G' AND op.taxsheet_yn = 'Y' AND op.tax_ix ='0' AND op.bill_ix = '0' AND op.pay_status='IC' ";
	}elseif($view_type=="complete"){
		$where = "WHERE o.oid=od.oid and od.oid=op.oid and od.status <> '' and od.status !='SR' AND op.pay_type='G' AND op.taxsheet_yn = 'Y' AND (op.tax_ix !='0' or op.bill_ix !='0') AND op.pay_status='IC'";
	}else{
		echo "잘못된 접근입니다.";
		exit;
	}

	if($orderdate){
		$where .= "and op.ic_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
		/*
		if($view_type=="preiod_ready"){
			$where .= "and date_format(o.order_date,'%Y%m%d') between '".str_replace("-","",$startDate)."' and '".str_replace("-","",$endDate)."' ";
		}elseif($view_type=="complete"){
			$where .= "and date_format(o.order_date,'%Y%m%d') between '".str_replace("-","",$startDate)."' and '".str_replace("-","",$endDate)."' ";
		}
		*/
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


	$sql="select *
	FROM
		".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, shop_order_payment op
	$where
	group by o.oid
	ORDER BY order_date DESC";
	$db->query($sql);
	$total = $db->total;


	$sql="select o.oid, od.order_from, o.order_date, o.bname ,o.payment_agent_type, o.user_code, o.mem_group, op.tax_com_name, op.tax_com_ceo, op.tax_com_number
		from
			".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, shop_order_payment op
		$where
		group by o.oid
		ORDER BY order_date DESC
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
	<form name=listform method=post action='taxbill.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'><!--target='act'-->
	<input type=hidden id='oid' value=''>
	<input type=hidden name = 'publish_type' id='publish_type' value='1'>
	<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>";

	$Contents .= "
		<tr height='25' >";
		//if($view_type=="order_apply"){
			$Contents .= "
			<td width='30px' align='center' class='s_td' rowspan='2'><input type=checkbox  name='all_fix' onclick='fixAll3(document.listform)'></td>";
		//}
			$Contents .= "
			<td width='7%' align='center'  class='m_td' rowspan='2' ><b>판매처</b></td>
			<td width='*' align='center' class='m_td' rowspan='2'><b>주문&입금일자/주문번호</b></td>
			<td width='7%' align='center'  class='m_td' rowspan='2' ><b>업체명</b></td>
			<td width='7%' align='center' class='m_td' rowspan='2' ><b>대표자명</b></td>
			<td width='8%' align='center' class='m_td' rowspan='2'><b>사업자번호</b></td>
			<td width='6%' align='center' class='m_td' rowspan='2' ><b>과세여부</b></td>
			<td width='21%' align='center' class='m_td' colspan='3'><b>발행금액정보</b></td>
			<td width='6%' align='center' class='m_td' rowspan='2'><b>상태</b></td>
			<td width='12%' align='center' class='m_td' rowspan='2'><b>발급번호</b></td>
			<td width='8%' align='center' class='e_td' rowspan='2'><b>관리</b></td>
		</tr>
		<tr>
			<td width='7%' align='center' class='m_td' ><b>공급가</b></td>
			<td width='7%' align='center' class='m_td' ><b>세액</b></td>
			<td width='7%' align='center' class='m_td' ><b>합계</b></td>
		</tr>";

	if($db->total){

		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);

			$sql = "SELECT
						*
				FROM (
					SELECT
						pay_type,pay_status,method,ic_date,tax_affairs_yn,'Y' as tax_yn, tax_no as bill_no,
						SUM(case when pay_type ='F' then -tax_price else tax_price end) as tax_price,
						'0' as tax_free_price
					FROM shop_order_payment where oid='".$db->dt[oid]."' and tax_price > 0
					having tax_price > 0
					UNION ALL
					SELECT
						pay_type,pay_status,method,ic_date,tax_affairs_yn,'N' as tax_yn, bill_no as bill_no,
						'0' as tax_price,
						SUM(case when pay_type ='F' then -tax_free_price else tax_free_price end) as tax_free_price
					FROM shop_order_payment where oid='".$db->dt[oid]."' and tax_free_price > 0
					having tax_free_price > 0
				) a ";

			$odb->query($sql);

			for ($j = 0; $j < $odb->total; $j++)
			{
				$odb->fetch($j);

				if($odb->dt["tax_yn"]=="Y"){
					$tax_yn="과세";
					$price = $odb->dt["tax_price"];
					$tax = $price - round($price/1.1);
				}else{
					$tax_yn="면세";
					$price = $odb->dt["tax_free_price"];
					$tax=0;
				}

				if($view_type=="order_apply"){
					$status = "발급신청";
				}elseif($view_type=="complete"){
					$status = "발급완료";
				}



				$Contents .= "<tr height=28 >";

					$Contents .= "<td class='list_box_td ' align='center'><input type=checkbox name='oid[]' id='oid' value='".$db->dt[oid]."|".$db->dt[tax_yn]."' ></td>";

					if($j==0){
						$Contents .= "<td class='list_box_td' style='line-height:140%' align=center rowspan='".($odb->total)."'>".getOrderFromName($db->dt[order_from])."</td>";
						$Contents .= "<td class='list_box_td point' style='line-height:140%;' align=center rowspan='".($odb->total)."'>".Black_list_check($db->dt[user_code],$db->dt[bname]."(<span class='small'>".$db->dt[mem_group]."</span>)")."<br/>".substr($db->dt[order_date],0,10)." & ".($odb->dt[ic_date] ? substr($odb->dt[ic_date],0,10) : "입금예정")."<br/><a href=\"../order/orders.read.php?oid=".$db->dt[oid]."\" style='color:#007DB7;font-weight:bold;' class='small' rowspan='".$odb->total."'>".$db->dt[oid]."</a></td>";
						$Contents .= "<td style='line-height:140%' align=center class='list_box_td' rowspan='".($odb->total)."'>".$db->dt[tax_com_name]."</td>";
						$Contents .= "<td class='list_box_td ' align='center' nowrap rowspan='".($odb->total)."'>".$db->dt[tax_com_ceo]."</b></td>";
						$Contents .= "<td class='list_box_td' align='center'  nowrap rowspan='".($odb->total)."'>".$db->dt[tax_com_number]."</td>";
					}

					$Contents .= "<td class='list_box_td' align='center'  nowrap>".$tax_yn."</td>";
					$Contents .= "<td class='list_box_td' align='center'  nowrap>".number_format($price-$tax)."</td>";
					$Contents .= "<td class='list_box_td' align='center'  nowrap>".number_format($tax)."</td>";
					$Contents .= "<td class='list_box_td' align='center' nowrap>".number_format($price)."</td>";
					$Contents .= "<td class='list_box_td' align='center'  nowrap>".$status."</td>
									<td class='list_box_td' align='center' nowrap>".$db->dt[bill_no]."</td>
									<td class='list_box_td' align='center' nowrap>
										<input type='button' value='주문내역' onclick=\"alert('준비중입니다.');/*PopSWindow('./taxbill_order_detail.php?type=tax&tax_ix=".$db->dt[idx]."',1300,660,'taxbill_detail')*/\" /><br/>";
								if($db->dt[status]=='0'){
									//$Contents .= "<input type='button' value='재발행' onclick=\"again_taxbill('".$db->dt[idx]."');\" /> ";
								}else{
									//$Contents .= GetTaxInvoicePopUpURL($_SESSION[admininfo][mall_ename].$db->dt[idx]);
								}
					$Contents .= "</td>";
				$Contents .= "</tr>";

			}
		}

	}else{
		$Contents .= "<tr height=50><td colspan='13' align=center>조회된 결과가 없습니다.</td></tr>";
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
		<input type='radio' name='update_kind' id='update_kind' value='' onclick=\"\" checked><label for='update_kind'>세금계산서 발급</label>
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
				$help_text .= "<input type='radio' name='act' id='order_taxbill' value='order_taxbill' onclick=\"\" checked><label for='order_taxbill' >세금&계산서 신청완료</label>";
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
	function again_taxbill(idx){
		if(confirm('해당 세금계산서를 재발행 하시겠습니까?')){
			window.frames['act'].location.href='./taxbill.act.php?act=again_taxbill&idx='+idx;
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
$P->addScript = "<script language='javascript' src='./taxbill.js'></script>\n".$Script;
$P->Navigation = "구매자정산관리 > $title_str";
$P->title = $title_str;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>