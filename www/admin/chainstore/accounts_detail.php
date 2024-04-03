<?
include("../class/layout.class");


$db1 = new Database;
$db3 = new Database;



$Contents = "
<script language='javascript' src='"."tglib_orders.php?page=$page&ctgr=$ctgr&qstr=$qstr"."'></script>
<table width='100%'>
<tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("정산 상세내역", "매출관리 > 정산 상세내역 ")."</td>
</tr>

</table>  ";


		if($admininfo[admin_level] == 9 || $admininfo[admin_level] == 8){
			$sql = "SELECT o.oid, o.uid, o.btel,o.bmobile,o.rmobile,o.bname, o.bmail, o.rname, o.rtel, o.rmail, o.zip, o.addr, o.msg, o.return_message,o.return_date,
						UNIX_TIMESTAMP(o.date) AS date, o.method, o.bank, o.tid, o.authcode,
						o.status, o.quick, o.deliverycode, o.total_price, o.use_reserve_price, o.payment_price, o.delivery_price
						FROM ".TBL_SHOP_ORDER." o LEFT JOIN ".TBL_SHOP_ORDER_DETAIL." od ON o.oid=od.oid LEFT JOIN ".TBL_SHOP_PRODUCT." p ON od.pid=p.id
						WHERE o.oid = '".$oid."' AND p.product_type NOT IN ('4','5','6') ";
		}

		//echo $sql;
		$db1->query($sql);
		$db1->fetch();



		$status = getOrderStatus($db1->dt[status]);

		if ($db1->dt[method] == "1")
		{
			if($db1->dt[bank] == ""){
				$method = "카드결제";
			}else{
				$method = $db1->dt[bank];
			}
		}elseif($db1->dt[method] == "0"){
			$method = "계좌입금";
		}elseif($db1->dt[method] == "2"){
			$method = "전화결제";
		}elseif($db1->dt[method] == "3"){
			$method = "후불결제";
		}elseif($db1->dt[method] == "9"){
			$method = "가상계좌[에스크로]";
		}



		$psum = number_format($db1->dt[total_price]);

		$Obj = str_replace("-","",$db1->dt[oid]);

		if ($db1->dt[stats] == "3")
		{
			$delete = "[<a href=\"javascript:alert(language_data['accounts_detail.php']['A'][language]);\">삭제</a>]";//[처리완료] 기록은 삭제할 수 없습니다.
		}
		elseif ($db1->dt[stats] != "9" && $db1->dt[method] == "1")
		{
			$delete = "[<a href=\"javascript:alert(language_data['accounts_detail.php']['B'][language]);\">삭제</a>]";//[카드결제]는 [승인취소]와 [주문취소] 처리를 먼저한 후 삭제해주세요.
		}
		else
		{
			$delete = "[<a href=\"javascript:act('delete','$Obj');\">삭제</a>]";
		}


	if ($db1->dt[method] == "0")
	{
		$authinfo = "결제은행";
		$authdata = $db1->dt[bank];
	}
	else
	{
		$authinfo = "승인번호";
		$authdata = $db1->dt[authcode]."&nbsp;[<a href=\"javascript:PoPWindow2('/shop/inicis/securepay_confirm.php?mid=hongilte00&tid=".$db1->dt[tid]."&merchantreserved=승인확인테스트','400', '80','confirmwindow');\">승인확인</a>]";

		if ($db1->dt[stats] == "3")
		{
			$authcancel = $db1->dt[authcode]. "&nbsp;[<a href=\"javascript:alert(language_data['accounts_detail.php']['C'][language]);\">승인취소</a>]";//[처리완료] 기록은 승인취소할 수 없습니다.
		}
		else
		{
			$authcancel = $db1->dt[authcode]."&nbsp;[<a href=\"javascript:PoPWindow2('card_auth_cancel.php?tid=".$db1->dt[tid]."','400', '80','cancelwindow');\">승인취소</a>]";
		}
	}

$Contents = $Contents."
<table border='0' cellspacing='1' cellpadding='15' width='100%'>
                <tr>
                  <td bgcolor='#F8F9FA'>
					<table border='0' cellspacing='0' cellpadding='0' width='100%'>
						<tr>
							<td height='20'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'> <b>주문제품정보</b></td>
						</tr>
					</table>
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
											<td width='5%' class='s_td'><b>번호</b></td>
											<!--td width='10%' class='m_td'><b>상품코드</b></td-->
											<td width='*' colspan=2 class='m_td'><b>주문번호/제품명</b></td>
											<td width='15%' class='m_td'><b>옵션</b></td>
											<td width='5%' class='m_td'><b>비고</b></td>
											<td width='5%' class='m_td'><b>수량</b></td>
											<td width='8%'  class='m_td'><b>단가</b></td>
											<td width='7%' class='m_td'><b>판매액</b></td>
											<td width='7%' class='m_td'><b>수수료율</b></td>
											<td width='7%' class='m_td small' nowrap><b>수수료</b></td>
											<td width='8%' class='m_td small' nowrap><b>정산금액</b></td>
											<td width='8%' class='e_td small' nowrap><b>배송비정산</b></td>

										</tr>";

	if($admininfo[admin_level] == 9){
		if($ac_ix){
			$sql = "SELECT od.oid, od.pid, od.pname, od.reserve, pcnt, psprice, ptprice, od.option_text, po.option_etc1, od.status ,
							od.commission, od.ptprice*(100-od.commission)/100 as coprice, od.delivery_price
							FROM ".TBL_SHOP_ORDER_DETAIL." od
							left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po on od.option1 = po.id
							left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
							WHERE od.ac_ix = '".$ac_ix."' AND p.product_type NOT IN ('4','5','6') ";
		//echo $sql;
		}else{
		$sql = "SELECT od.oid, od.pid, od.pname, od.reserve, pcnt, psprice, ptprice, od.option_text, po.option_etc1, od.status ,od.commission,
						od.ptprice*(100-od.commission)/100 as coprice , od.delivery_price
						FROM ".TBL_SHOP_ORDER_DETAIL." od left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po on od.option1 = po.id left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
						WHERE p.admin = '".$company_id."' and od.status ='".ORDER_STATUS_DELIVERY_COMPLETE."' AND p.product_type NOT IN ('4','5','6') ";
		}
	}else if($admininfo[admin_level] == 8){
		$sql = "SELECT od.oid, od.pid, od.pname, od.reserve, pcnt, psprice, ptprice, od.option_text, po.option_etc1, od.status  ,od.commission,
						od.ptprice*(100-od.commission)/100 as coprice , od.delivery_price
						FROM ".TBL_SHOP_ORDER_DETAIL." od left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  on od.option1 = po.id  left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
						WHERE od.ac_ix = '".$ac_ix."' and p.admin = '".$admininfo[company_id]."' AND p.product_type NOT IN ('4','5','6') ";
	}

	$db3->query($sql);


	$num = 1;

	$sum = 0;

	for($j = 0; $j < $db3->total; $j++){
		$db3->fetch($j);

		$pname = $db3->dt[pname];
		$pcode = $db3->dt[pcode];
		$count = $db3->dt[pcnt];
		//$option_div = $db3->dt[option_text];
		//$option_etc1 = $db3->dt[option_etc1];
		//$price = $db3->dt[psprice];
		//$coprice = $db3->dt[coprice];
		$coprice_sum += $db3->dt[coprice];
		$sumptprice = $sumptprice + $db3->dt[ptprice];
		$ptprice_sum += $db3->dt[ptprice];
		$commission_sum += $db3->dt[ptprice]-$db3->dt[coprice];
		$account_sum += $db3->dt[coprice];
		$count_sum += $db3->dt[pcnt];
		$delivery_price_sum += $db3->dt[delivery_price];

		$reserve = $db3->dt[reserve];
		$ptotal = $price * $count;
		$sum += $ptotal;

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db3->dt[pid], "s"))){
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db3->dt[pid], "s");
		}else{
			$img_str = "../image/no_img.gif";
		}

$Contents .= "
										<tr height='70' align='center'>
											<td >".$num."</td>
											<td ><div style='border:1px solid silver;padding:5px;width:60px;'><img src=\"".$img_str."\" width=50 height=50></div></td>
											<td ><div align='left' style='padding:5 0 5 10'><b class=blue>".$db3->dt[oid]."</b><br><!--a href=\"javascript:PoPWindow('/shop/goods_view.php?id=".$db3->dt[pid]."','1000','700','preview')\"--><a href='/shop/goods_view.php?id=".$db3->dt[pid]."' target='_blank'>".$db3->dt[pname]."</a></div></td>
											<td align=left style='padding-left:5px;'>".$db3->dt[option_text]."</td>
											<td align=center>".$db3->dt[option_etc1]."</td>
											<td >".$db3->dt[pcnt]." 개</td>
											<td >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db3->dt[psprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db3->dt[ptprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td align=center>".number_format($db3->dt[commission])." %</td>
											<td >".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db3->dt[ptprice]-$db3->dt[coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db3->dt[coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db3->dt[delivery_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
										</tr>
										 <tr height=1><td colspan=12 class='dot-x'></td></tr>";

		$num++;
	}
$Contents = $Contents."
										<tr height='30' align='center'>
											<td colspan=4>합계</td>

											<td align=center></td>
											<td >".$count_sum." 개</td>
											<td ></td>
											<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ptprice_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td align=center></td>
											<td >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($commission_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($account_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($delivery_price_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
										</tr>
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

<br>
<table width='100%' border='0' cellpadding='0' cellspacing='1'>
	<tr>
		<td>
		<img src='../image/title_head.gif' align=absmiddle>
		<font color='#000000'><!--정산 총액은 <b>".number_format($coprice_sum)."</b> 입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A',$coprice_sum,"coprice_sum")."</font>
		</td>";

unset($sumptprice);

$Contents .= "

	</tr>
</table>
		</td>
	</tr>
</table>
";


	if (strlen($db1->dt[msg]))
	{

$Contents = $Contents."
<br><br>
<img src='/image/aas.gif' width='11' height='11' valign='absmiddle'>
<b>전달사항</b>

<table width='100%' border='0' cellpadding='0' cellspacing='1'>
	<tr>
		<td bgcolor='#6783A8'>
			<table border='0' width='100%' cellspacing='1' cellpadding='2'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%'>
							<tr>
								<td>
".nl2br($db1->dt[msg])."
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>";

	}



$Contents = $Contents."
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";



$P = new LayOut();
$P->strLeftMenu = chainstore_menu();
$P->addScript = "<script language='javascript' src='orders.js'></script>";
$P->Navigation = "정산관리 > 정산 상세내역";
$P->title = "정산 상세내역";
$P->strContents = $Contents;


echo $P->PrintLayOut();


function SelectQuickLink($QuickCode, $deliverycode){
	$divname = array ("#",
	"http://www.ilogen.com/customer/reserve_03-1_ok.asp?f_slipno=",
	"http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo=",
	"http://samsunghth.com/homepage/searchTraceGoods/SearchTraceDtdShtno.jhtml?dtdShtno=",
	"#",
	"#",
	"http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=",
	"http://www.kgbls.co.kr/tracing.asp?number=",
	"http://www.yellowcap.co.kr/branch/chase/listbody.html?a_gb=branch&a_cd=5&a_item=0&f_slipno=",
	"#");


	return "<a href='".$divname[$QuickCode]."$deliverycode' target=_blank>$deliverycode</a>";

}

?>