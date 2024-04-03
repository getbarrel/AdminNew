<?
include("../class/layout.class");

$db = new Database;


$Script = "
<script language='javascript'>


</script>";

$Contents = "
<script language='javascript' src='"."tglib_orders.php?page=$page&ctgr=$ctgr&qstr=$qstr"."'></script>
<table width='100%'>
<tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("세금계산서 상세내역", "구매자정산관리 > 세금계산서 상세내역 ")."</td>
</tr>

</table>  ";

$Contents = $Contents."
<table border='0' cellspacing='1' cellpadding='15' width='100%'>
<tr>
  <td bgcolor='#F8F9FA'>
	<table border='0' cellspacing='0' cellpadding='0' width='100%'>
		<tr>
			<td height='20'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'> <b>주문제품정보</b></td>
		</tr>
	</table>
<div style='overflow:auto;height:500px;width:100%'>
<table width='100%' border='0' cellpadding='0' cellspacing='1'>
	<tr>
		<td bgcolor='silver'>
			<table border='0' width='100%' cellspacing='1' cellpadding='2'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0'>
										<tr height='25r' bgcolor='#efefef' align=center>
											<td width='30' class='m_td small' rowspan='2'><b>번호</b></td>
											<td width='*' colspan=2 class='m_td small' rowspan='2'><b>상품명</b></td>
											<td width='8%' class='m_td small' rowspan='2'><b>옵션</b></td>
											<td width='7%' class='m_td small' rowspan='2'><b>판매가(할인가)</b></td>
											<td width='4%' class='m_td small' rowspan='2'><b>수량</b></td>
											<td width='6%' class='m_td small' rowspan='2'><b>상품가격</b></td>
											<td width='6%' class='m_td small' rowspan='2'><b>할인액</b></td>
											<td width='18%' class='m_td small' colspan='3'><b>실결제금액</b></td>
											<td width='5%' class='m_td small' rowspan='2'><b>적립금</b></td>
											<td width='6%' class='m_td small' rowspan='2'><b>처리상태</b></td>
											<td width='6%' class='m_td small' rowspan='2'><b>출고처리</b></td>
											<td width='6%' class='m_td small' rowspan='2'><b>환불상태</b></td>
										</tr>
										<tr height='30'>
											<td width='6%' class='m_td small'><b>공급가</b></td>
											<td width='6%' class='m_td small'><b>세액</b></td>
											<td width='6%' class='m_td small'><b>판매가</b></td>
										</tr>";
	if($type=='tax'){
		$sql="select * from shop_order o, shop_order_detail od where o.oid=od.oid and o.taxsheet_yn ='Y' and od.surtax_yorn ='N' and o.tax_ix='".$tax_ix."' ";
		$db->query($sql);
	}elseif($type=='bill'){
		$sql="select * from shop_order o, shop_order_detail od where o.oid=od.oid and o.taxsheet_yn ='Y' and od.surtax_yorn ='Y' and o.bill_ix='".$tax_ix."' ";
		$db->query($sql);
	}

	$num = 1;
	$sum = 0;
	$arr_sns_ptype=array(4,5,6);

	for($j = 0; $j < $db->total; $j++){
		$db->fetch($j);

		$pname = $db->dt[pname];
		$sub_pname = $db->dt[sub_pname];
		$pcode = $db->dt[pcode];
		$product_type = $db->dt[product_type];
		$count = $db->dt[pcnt];
		$option_div = $db->dt[option_text];
		$option_etc1 = $db->dt[option_etc];
		$msgbyproduct = $db->dt[msgbyproduct];
		$option_price = $db->dt[option_price];
		$price = $db->dt[psprice]+$db->dt[option_price];
		$coprice = $db->dt[coprice];
		$sumptprice = $sumptprice + $db->dt[ptprice];
		$od_admin_message=$db->dt["admin_message"];
		$sale_price=$db->dt[use_coupon]+$db->dt[member_sale_price];

		$reserve = $db->dt[reserve] * $count;
		$ptotal = $price * $count;
		$sum += $ptotal;
		
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "s"))){
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "s");
		}else{
			$img_str = "../image/no_img.gif";
		}

$Contents .= "
			<tr height='70' align='center'>
				<td >".$num."</td>
				<td style='padding:3px 0px;'><a href='../product/goods_input.php?id=".$db->dt[pid]."' class=''  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], 'm', $db->dt)."'  title='".$LargeImageSize."'><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "m" , $db->dt)."'  onerror=\"this.src='".$admin_config[mall_data_root]."/images/noimg_52.gif'\" width=50 height=50 style='margin:5px;'></a></td>
				<td align='left' style='padding:5px 0 5px 0;line-height:130%'>";
				if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
					$Contents .= "<a href=\"javascript:PoPWindow('../seller/company.add.php?company_id=".$db->dt[company_id]."&mmode=pop',960,600,'brand')\"><b>".($db->dt[company_name] ? $db->dt[company_name]:"-")."</b></a><br>";
				}
				if(in_array($product_type,$arr_sns_ptype)){
$Contents .= "								<a href=\"/sns/shop/goods_view.php?id=".$db->dt[pid]."\" target=_blank>".$pname."</a>";
				} else {
					if($product_type=='99'){
						$Contents .= "<a href=\"/shop/goods_view.php?id=".$db->dt[pid]."\" target=_blank><b class='red' >".$pname."</b><br/>".$sub_pname."</a>";
					}else{
						$Contents .= "<a href=\"/shop/goods_view.php?id=".$db->dt[pid]."\" target=_blank>".$pname."</a>";
					}
				}

$Contents .= "
				</td>
				<td align=left style='padding:7px 5px;'>".($db->dt[stock_use_yn]=='Y' ? "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>" :"")." ".strip_tags($option_div)."".($option_price != '' ? " + ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($option_price)."".$currency_display[$admin_config["currency_unit"]]["back"]."":"")."</td>
				<td >".number_format($price)." 원</td>
				<td >".$count." 개</td>
				<td align=center>".number_format($db->dt[ptprice])."원</td>
				<td align=center>".number_format($sale_price)."원</td>
				<!--td align=center>".number_format($db->dt[use_coupon])." ".($db->dt[use_coupon] > 0 ? "<br><a href=\"javascript:PopSWindow('../display/cupon_publish.php?mmode=pop&regist_ix=".$db->dt[use_coupon_code]."',900,700,'cupon_detail_pop');\" class=blue>쿠폰확인</a>":"")."</td-->
				<td align=center> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format(round($db->dt[ptprice]-$sale_price)/1.1)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td align=center> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[ptprice]-$sale_price-round($db->dt[ptprice]-$sale_price)/1.1)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td align=center> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[ptprice]-$sale_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td >".number_format($reserve)." P</td>
				<td >".getOrderStatus($db->dt[status])."</td>
				<td >".getOrderStatus($db->dt[delivery_status])."</td>
				<td >".getOrderStatus($db->dt[refund_status])."</td>
			</tr>
			 <tr height=1><td colspan=15 class='dot-x'></td></tr>";

		$num++;
	}
$Contents = $Contents."
										<!--tr height='30' align='center'>
											<td colspan=7>합계</td>

											<td align=center></td>
											<td >".$count_sum." 개</td>
											<td ></td>
											<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ptprice_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td align=center></td>
											<td >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($commission_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($delivery_price_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($account_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
										</tr-->
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
		</td>
	</tr>
</table>
";


$Contents = $Contents."
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";



$P = new ManagePopLayOut();
$P->OnloadFunction = "";
$P->addScript = $Script;
$P->Navigation = "구매자정산관리 > 세금계산서 상세내역";
$P->title = "세금계산서 상세내역";
$P->NaviTitle = "세금계산서 상세내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>