<?
include("../class/layout.class");


$db1 = new Database;
$db3 = new Database;



$Contents = "
<script language='javascript' src='"."tglib_orders.php?page=$page&ctgr=$ctgr&qstr=$qstr"."'></script>
<!--span style='width:50px;'></span>".(($admininfo[admin_level] == 9) ? selectadmin($admincode) : "")."<br><br-->
<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("배송비 정산 상세내역", "매출관리 > 배송비 정산 상세내역 ")."</td>
	</tr>
</table>";

	
$Contents .= "
<table border='0' cellspacing='1' cellpadding='15' width='100%'>
                <tr>
                  <td bgcolor='#F8F9FA'>


<img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'>
<b>주문제품정보</b>

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
										<tr height='25' bgcolor='#efefef' align=center>
											<td width='5%' class='s_td'><b>NO</b></td>
											<td align=center width='10%' colspan=2 class='m_td small'><b>주문번호</b></td>
											<td align=center width='10%' class='m_td small'><b>주문일자</b></td>
											<td align=center width='10%' class='m_td small'><b>배송완료일</b></td>
											<td align=center width='5%' class='m_td small'><b>주문총액</b></td>
											<td align=center width='5%' class='m_td small'><b>배송비</b></td>
											<td align=center width='5%' class='e_td small'><b>상품수</b></td>
										";


if($admininfo[admin_level] == 9){											
//$Contents .= "				<td align=center width='5%' class='m_td small' nowrap><b>수수료<br><span clas='small'>(수수료율사용)</span></b></td>";
}
$Contents .= "				<!--td align=center width='5%' class='e_td small'><b>정산금액</b></td-->
										</tr>";

	if($admininfo[admin_level] == 9){
		if($dac_ix){
			/*
			$sql = "SELECT od.oid,od.pid, od.pname, od.reserve, pcnt, psprice, ptprice, od.option_text, po.option_etc1, od.status , od.coprice,od.commission,od.regdate, od.dc_date, od.one_delivery_price
			FROM ".TBL_SHOP_ORDER_DETAIL." od left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po on od.option1 = po.id left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
			WHERE o.dac_ix = '".$dac_ix."' and od.status = 'AC' and od.delivery_type ='CD'  "; // 
			*/
			$sql = "select o.oid , od.dc_date,  o.date, od.company_id ,  ccd.com_name as company_name,bank_name,bank_number,bank_owner,o.delivery_price  , case when  sum(od.ptprice) < delivery_free_price then od.one_delivery_price else 0 end as one_delivery_price ,   delivery_free_price,  sum(od.ptprice) as sum_ptprice, count(*) as cnt
					from shop_order o, shop_order_detail od , ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd  
					where o.delivery_price != 0 and o.oid = od.oid and od.company_id = ccd.company_id and csd.company_id = ccd.company_id
					and  od.delivery_type = 'CD' and od.status = 'AC'	and o.dac_ix = '".$dac_ix."'	
					group by od.oid
					order by od.company_id asc, date desc";
			
			//echo $sql;
		}else{
			/*
			$sql = "SELECT od.oid,od.pid, od.pname, od.reserve, pcnt, psprice, ptprice, od.option_text, po.option_etc1, od.status , od.coprice,od.commission,od.regdate, od.dc_date, od.one_delivery_price
			FROM ".TBL_SHOP_ORDER_DETAIL." od left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po on od.option1 = po.id left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid 
			WHERE p.admin = '".$company_id."' and od.status = 'AC' and od.delivery_type ='CD' "; // and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate
			*/
			//echo $sql;
			$sql = "select o.oid , od.dc_date,  o.date, od.company_id ,  ccd.com_name as company_name,bank_name,bank_number,bank_owner,o.delivery_price  , case when  sum(od.ptprice) < delivery_free_price then od.one_delivery_price else 0 end as one_delivery_price ,   delivery_free_price,  sum(od.ptprice) as sum_ptprice, count(*) as cnt
		 from shop_order o, shop_order_detail od , ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd  
		where o.delivery_price != 0 and o.dac_ix is null and o.oid = od.oid and od.company_id = ccd.company_id and csd.company_id = ccd.company_id
		and od.delivery_type = 'CD' and od.status = 'AC' and od.company_id = '".$company_id."' and date_format(od.dc_date,'%Y%m%d') <= '".$endDate."' 	
		group by od.oid
		order by od.company_id asc, date desc";
		
		}
	}else if($admininfo[admin_level] == 8){
		if($dac_ix){
			$sql = "select o.oid , od.dc_date,  o.date, od.company_id ,  ccd.com_name as company_name,bank_name,bank_number,bank_owner,o.delivery_price  , case when  sum(od.ptprice) < delivery_free_price then od.one_delivery_price else 0 end as one_delivery_price ,   delivery_free_price,  sum(od.ptprice) as sum_ptprice, count(*) as cnt
						from shop_order o, shop_order_detail od , ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd  
						where o.delivery_price != 0 and o.oid = od.oid and od.company_id = ccd.company_id  and csd.company_id = ccd.company_id
						and od.delivery_type = 'CD' and od.status = 'AC' and od.company_id = '".$admininfo[company_id]."'	and o.dac_ix = '".$dac_ix."'	
						group by od.oid
						order by od.company_id asc, date desc";
		
		}else{
			/*
			$sql = "SELECT od.pid, od.pname, od.reserve, pcnt, psprice, ptprice, od.option_text, po.option_etc1, od.status , od.coprice,od.commission, od.dc_date, od.one_delivery_price
			FROM ".TBL_SHOP_ORDER_DETAIL." od left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  on od.option1 = po.id  left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid 
			WHERE p.admin = '".$admininfo[company_id]."' and od.status = 'AC' and od.delivery_type ='CD' and date_format(od.dc_date,'%Y%m%d') <= $endDate"; // and date_format(os.regdate,'%Y%m%d') between $startDate and $endDate
			*/
			$sql = "select o.oid , od.dc_date,  o.date, od.company_id ,  ccd.com_name as company_name,bank_name,bank_number,bank_owner,o.delivery_price  , case when  sum(od.ptprice) < delivery_free_price then od.one_delivery_price else 0 end as one_delivery_price ,   delivery_free_price,  sum(od.ptprice) as sum_ptprice, count(*) as cnt
		 from shop_order o, shop_order_detail od , ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd  
		where o.delivery_price != 0 and o.dac_ix is null and o.oid = od.oid and od.company_id = ccd.company_id and csd.company_id = ccd.company_id
		and od.delivery_type = 'CD' and od.status = 'AC' and od.company_id = '".$admininfo[company_id]."'		and date_format(od.dc_date,'%Y%m%d') <= '".$endDate."' 	
		group by od.oid
		order by od.company_id asc, date desc";
		}
	}
		
	//echo $sql;
	$db3->query($sql);
	$num = 1;

	$sum = 0;

	for($j = 0; $j < $db3->total; $j++)
	{
		$db3->fetch($j);

		
		$sumptprice = $sumptprice + $db3->dt[sum_ptprice];	
		$sum_cnt = $sum_cnt + $db3->dt[cnt];
		$sum_delivery_price = $sum_delivery_price + $db3->dt[one_delivery_price];
		//$reserve = $db3->dt[reserve];
		//$ptotal = $price * $count;
		//$sum += $ptotal;

$Contents .= "
										<tr height='70' align='center'>
											<td align=center>".($j+1)."</td>
											<td></td>
											<td><div align='center' >
											<a href=\"/admin/order/orders.edit.php?oid=".$db3->dt[oid]."\" target=_blank>".$db3->dt[oid]."</a>											
											</div></td>
											<td align=center style='padding-left:5px;'>".$db3->dt[date]."</td>
											<td align=center style='padding-left:5px;'>".$db3->dt[dc_date]."</td>
											<td align=center>".number_format($db3->dt[sum_ptprice])."</td>									
											<td align=center><b>".number_format($db3->dt[one_delivery_price])." 원</b></td>
													
											<td align=center>".$db3->dt[cnt]."개</td>
											";
if($admininfo[admin_level] == 9){											
//$Contents .= "				<td align=center><div >".number_format($coprice_sum_use_commission)."원</div></td>";			
}								
$Contents .= "				
										</tr>
										 <tr height=1><td colspan=10 background='../image/dot.gif'></td></tr>";

		$num++;	
	}
$Contents = $Contents."					<tr height=20>
											<td colspan=10></td>
										</tr>
										<tr>
											<td colspan=5 align='center' class='small'>합계</td>
											
											<td align='center' class='small' nowrap>".number_format($sumptprice)."원</td>
											<td align='center' class='small'>".number_format($sum_delivery_price)."</td>
											
											<td align='center' class='small'>".$sum_cnt." 개</td>
											
											
											
										
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

		</td>
	</tr>
</table>
";




$Contents = $Contents."  
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";



$P = new LayOut();
$P->strLeftMenu = order_menu();
$P->addScript = "<script language='javascript' src='orders.js'></script>";
$P->Navigation = "HOME > 주문관리 > 정산 상세내역";
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