<?
include("../class/layout.class");


header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-Disposition: attachment; filename=order_list_account.xls" );
header( "Content-Description: Generated Data" ); 

$db1 = new Database;
$odb = new Database;
	
	
	$where = "WHERE od.status <> '' ";
	
	if($search_type != "" && $search_text != ""){
		if($search_type == "combi_name"){
			$where .= "and (bname LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
		}else{
			$where .= "and $search_type like '%$search_text%'";
		}
	}

	if ($vFromYY != "")	{
		$startDate = $vFromYY.$vFromMM.$vFromDD;
		$endDate = $vToYY.$vToMM.$vToDD;

		$where .= "and date_format(date,'%Y%m%d') between $startDate and $endDate ";
	}

	if($type[0] != ""){ //is_array($type)
		
		for($i=0;$i < count($type);$i++){		
			
			if($type_str == ""){
				$type_str .= "'".$type[$i]."'";
			}else{	
				$type_str .= ",'".$type[$i]."'";
			}
		
		}
		
		if($type_str){
			$where .= "and od.status in ($type_str) ";
		}
	}else{		
		//$status = getOrderStatus($type)
		if($status){
			$where .= "and od.status = '$type'";
		}
	
	}

	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$where .= " and o.oid = od.oid and od.pid = p.id and  p.admin = '".$company_id."'";
		}else{
			$where .= " and o.oid = od.oid and  od.pid = p.id ";
		}
	}else if($admininfo[admin_level] == 8){	
		$where .= " and o.oid = od.oid and  od.pid = p.id and p.admin = '".$admininfo[company_id]."'";
	}
	//$admininfo[company_id]

/*	
	if($admininfo[admin_level] == 9){
		if($admincode == ""){
			
			$db1->query("SELECT oid FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p $where GROUP BY oid ORDER BY date DESC");			
						
		
			$total = $db1->total;	
			echo ("SELECT oid, uid, bname, o.rname, tid, status, method, total_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p $where GROUP BY oid ORDER BY date DESC ");
			$db1->query("SELECT oid, uid, bname, o.rname, tid, status, method, total_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p $where GROUP BY oid ORDER BY date DESC ");
		}else{
			$db1->query("SELECT oid FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p $where GROUP BY oid ORDER BY date DESC");			
						
		
			$total = $db1->total;	
			$db1->query("SELECT oid, uid, bname,o.rname,  tid, status, method, total_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p $where GROUP BY oid ORDER BY date DESC ");
		}
	}else if($admininfo[admin_level] == 8){
		$db1->query("SELECT * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where GROUP BY o.oid ORDER BY date DESC");		
	
		$total = $db1->total;	
		
		$db1->query("SELECT o.oid, uid, o.bname, o.rname, tid, o.status, method, total_price, payment_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where GROUP BY o.oid ORDER BY date DESC ");
	}
*/
//echo ("SELECT * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where  ORDER BY date DESC");		
$sql = "SELECT * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where  ORDER BY date DESC";

$db1->query($sql);		

$total = $db1->total;	
//echo ("SELECT o.oid, uid, o.bname, o.rname, tid, o.status, method, total_price, payment_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where GROUP BY o.oid ORDER BY date DESC ");
			
$sql = "SELECT o.oid, uid, o.bname,o.bmobile,p.surtax_yorn,p.pack_method, o.rname, tid, o.status, method, total_price, payment_price, date, o.method, o.receipt_y, 
				p.pname, addr, zip, msg, rtel, rmobile, od.option_text,od.psprice+od.option_price as psprice ,od.pcnt,od.ptprice,od.status,
				od.company_id, od.company_name, o.mem_group, od.use_baymoney, od.use_coupon, od.use_reserve, od.delivery_price, od.commission, 
				o.use_baymoney_price, o.use_reserve_price, 
				(select id from common_user where code = o.uid ) as id 
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where ORDER BY oid desc , od.pid asc";

						
$db1->query($sql);
	
if($db1->total){
	//echo "주문자명\t수취인명\t수취인주소\t우편번호\t전화번호\t이동통신\t상품명\t배송메시지\n";
	//$mstring = "주문번호\t업체아이디\t상품명\t과세/면세\t주문일\t주문자명\t회원유형\t특별회원그룹\t아이디\t연락처1\t연락처2\t받는자\t우편번호\t수취인주소\t연락처1\t연락처2\t결제수단\t판매가\t수량\t적립금\t쿠폰\t배송료\t포장비\t베이머니사용액\t적립금사용액\t실결제\t상태\t증빙서\n";
	$mstring = "<b>매출내역서(정산용)</b><br><br>		
							<table border=1 cellspacing=1 cellpadding=2 bgcolor=silver style='font-size:11px;font-family:돋움체'>";	
			$mstring .= "<tr align=center bgcolor=#ffffff >
									<td rowspan=2>NO</td>
									<td rowspan=2>업체명</td>
									<td rowspan=2>결제일</td>
									<td rowspan=2>주문번호</td>
									<td rowspan=2>제품명</td>
									<td rowspan=2>옵션</td>
									<td rowspan=2>비고</td>
									<td rowspan=2>수량</td>
									<td colspan=4>상품매출</td>
									<td colspan=3>배송비정산금액</td>
									<td colspan=3>합계</td>
									<td colspan=3>매출원가</td>
									<td rowspan=2>수수료율(%)</td>
									<td rowspan=2>수수료</td>
									<td rowspan=2>쿠폰용금액</td>
									<td rowspan=2>베이머니사용금액</td>									
									<td rowspan=2>적립금사용금액</td>									
									<td rowspan=2>실결제금액</td>
									<td rowspan=2>면세여부</td>
									<td rowspan=2>정산상태</td>
									<td rowspan=2>증빙</td>
									<td rowspan=2>결제방법</td>
									<td rowspan=2>회원명</td>
									<td rowspan=2>회원등급</td>
									</tr>";	
			$mstring .= "<tr bgcolor=#ffffff>
									<td>단가</td>
									<td>공급가</td>
									<td>부가세</td>
									<td bgcolor=#efefef>합계</td>
									<td>공급가</td>
									<td>부가세</td>
									<td bgcolor=#efefef>합계</td>
									<td>공급가</td>
									<td>부가세</td>
									<td bgcolor=#efefef>합계</td>
									<td>공급가</td>
									<td>부가세</td>
									<td bgcolor=#efefef>합계</td>
									
									</tr>";	
	for ($i = 0, $j=0; $i < $db1->total; $i++, $j++)
	{
		$db1->fetch($i);
		
		$status = getOrderStatus($db1->dt[status], $db1->dt[method]);

		if ($db1->dt[method] == ORDER_METHOD_CARD)
			{
				if($db1->dt[bank] == ""){
					$method = "카드결제";
				}else{
					$method = $db1->dt[bank];
				}
				$receipt_str = "카드결제";
			}elseif($db1->dt[method] == ORDER_METHOD_BANK){
				$method = "계좌입금";
				if($db1->dt[receipt_y] == "Y"){
					$receipt_str = "현금영수증(발행)";
				}else{
					$receipt_str = "현금영수증(미발행)";
				}
			}elseif($db1->dt[method] == ORDER_METHOD_PHONE){
				$method = "전화결제";			
			}elseif($db1->dt[method] == ORDER_METHOD_AFTER){
				$method = "후불결제";			
			}elseif($db1->dt[method] == ORDER_METHOD_VBANK){
				$method = "가상계좌";
				if($db1->dt[receipt_y] == "Y"){
					$receipt_str = "현금영수증(발행)";
				}else{
					$receipt_str = "현금영수증(미발행)";
				}
			}elseif($db1->dt[method] == ORDER_METHOD_ICHE){
				$method = "실시간계좌이체";
				if($db1->dt[receipt_y] == "Y"){
					$receipt_str = "현금영수증(발행)";
				}else{
					$receipt_str = "현금영수증(미발행)";
				}
			}elseif($db1->dt[method] == ORDER_METHOD_ASCROW){
				$method = "가상계좌[에스크로]";					
				if($db1->dt[receipt_y] == "Y"){
					$receipt_str = "현금영수증(발행)";
				}else{
					$receipt_str = "현금영수증(미발행)";
				}	
			}
		
		
		if($db1->dt[pack_method] == "B"){
			$pack_method = "냉장포장(1,500)";
		}else if($db1->dt[pack_method] == "C"){
			$pack_method = "냉동포장(1,500)";
		}else{
			$pack_method = "일반포장";
		}

		if($boid != $db1->dt[oid]){
			$use_baymoney_price_cal = 0;
			$use_reserve_price_cal = 0;
			$this_reserve_price = 0;
			$this_use_baymoney_price = 0;
		}
		
		$account_price = ($db1->dt[ptprice]*(100-$db1->dt[commission])/100);
		
		$mstring .= "<tr bgcolor=#ffffff><td>".($j+1)."</td>";
				$mstring .= "<td>".$db1->dt[company_name]."</td>";
				$mstring .= "<td>".$db1->dt[date]."</td>";
				$mstring .= "<td>".$db1->dt[oid]."</td>";
				$mstring .= "<td>".strip_tags($db1->dt[pname])."</td>";
				$mstring .= "<td>".strip_tags($db1->dt[option_text])."</td>";
				$mstring .= "<td>".$db1->dt[option_etc1]."</td>";
				$mstring .= "<td>".$db1->dt[pcnt]."</td>";
				$mstring .= "<td>".$db1->dt[psprice]."</td>";
				//상품매출
				$mstring .= "<td>".($db1->dt[surtax_yorn] == "Y" ? $db1->dt[psprice]:round($db1->dt[psprice]/1.1))."</td>";
				$mstring .= "<td>".($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[psprice]-round($db1->dt[psprice]/1.1))."</td>";
				$mstring .= "<td bgcolor=#efefef>".($db1->dt[ptprice])."</td>";
				//배송비 정산금액
				$mstring .= "<td>".($db1->dt[surtax_yorn] == "Y" ? "0":round($db1->dt[delivery_price]/1.1))."</td>";
				$mstring .= "<td>".($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1))."</td>";
				$mstring .= "<td bgcolor=#efefef>".($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[delivery_price])."</td>";
				// 합계				
				$mstring .= "<td>".(($db1->dt[surtax_yorn] == "Y" ? $db1->dt[ptprice]:round($db1->dt[ptprice]/1.1))+($db1->dt[surtax_yorn] == "Y" ? 0:round($db1->dt[delivery_price]/1.1)))."</td>";
				$mstring .= "<td>".(($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[ptprice]-round($db1->dt[ptprice]/1.1))+($db1->dt[surtax_yorn] == "Y" ? 0:($db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1))))."</td>";
				$mstring .= "<td bgcolor=#efefef>".($db1->dt[surtax_yorn] == "Y" ? $db1->dt[ptprice]:intval($db1->dt[ptprice]+$db1->dt[delivery_price]))."</td>";
				// 매출원가
				$mstring .= "<td>".(($db1->dt[surtax_yorn] == "Y" ? $account_price:round($account_price/1.1))+(($db1->dt[surtax_yorn] == "Y" ? "0":round($db1->dt[delivery_price]/1.1))))."</td>";
				$mstring .= "<td>".(($db1->dt[surtax_yorn] == "Y" ? "0":$account_price-round($account_price/1.1))+($db1->dt[surtax_yorn] == "Y" ? "0":($db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1))))."</td>";
				
				$sale_base_sum = ($db1->dt[surtax_yorn] == "Y" ? $db1->dt[ptprice]:intval($db1->dt[ptprice]+$db1->dt[delivery_price]));
				$mstring .= "<td bgcolor=#efefef>".($db1->dt[surtax_yorn] == "Y" ? intval($account_price):intval($account_price+($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[delivery_price])))."</td>";
				
				$mstring .= "<td>".($db1->dt[commission])."</td>";
				$mstring .= "<td>".($db1->dt[ptprice]-($db1->dt[ptprice]*(100-$db1->dt[commission])/100))."</td>";
				$mstring .= "<td>".($db1->dt[use_coupon])."</td>";		
				$sale_base_sum = $sale_base_sum - $db1->dt[use_coupon];
				
				if(($db1->dt[use_baymoney_price] - $use_baymoney_price_cal) >= $sale_base_sum ){ //베이머니 사용금액 남은 금액이 합계금액 보다 클때는 
					$this_use_baymoney_price = $sale_base_sum;
				}else{
					if(($db1->dt[use_baymoney_price] - $use_baymoney_price_cal) <= 0){
						$this_use_baymoney_price = 0;
					}else{
						$this_use_baymoney_price = $db1->dt[use_baymoney_price] - $use_baymoney_price_cal;
					}
					
					if(($db1->dt[use_reserve_price] - $use_reserve_price_cal) >= ($sale_base_sum -$this_use_baymoney_price)){
						$this_reserve_price = ($sale_base_sum-$this_use_baymoney_price);
					}else{
						if(($db1->dt[use_reserve_price] - $use_reserve_price_cal) <= 0){
							$this_reserve_price = 0;
						}else{
							$this_reserve_price = $db1->dt[use_reserve_price] - $use_reserve_price_cal;
						}
					}
					
					if(($db1->dt[use_reserve_price] - $use_reserve_price_cal) >=  ($sale_base_sum-$this_use_baymoney_price)){
						$use_reserve_price_cal +=  ($sale_base_sum-$this_use_baymoney_price);
					}else{
						$use_reserve_price_cal += $db1->dt[use_reserve_price];
					}
					
				}
				
				//$mstring .= "<td>".$db1->dt[use_baymoney_price].":::".($this_use_baymoney_price).":::$use_baymoney_price_cal</td>"; 
				$mstring .= "<td>".($this_use_baymoney_price)."</td>"; 

				
				if(($db1->dt[use_baymoney_price] - $use_baymoney_price_cal) >= $sale_base_sum){
					$use_baymoney_price_cal += $sale_base_sum;
				}else{
					$use_baymoney_price_cal += $db1->dt[use_baymoney_price];
				}
				
				$mstring .= "<td>".($this_reserve_price)."</td>";
						
				$mstring .= "<td>".($sale_base_sum-$this_use_baymoney_price-$this_reserve_price)."</td>";
				$mstring .= "<td>".($db1->dt[surtax_yorn] == "Y" ? "면세":"과세")."</td>";			
				$mstring .= "<td>".($status)."</td>";
				$mstring .= "<td>".$receipt_str."</td>";
				$mstring .= "<td>".$method."</td>";
				$mstring .= "<td>".$db1->dt[bname]."</td>";
				$mstring .= "<td>".$db1->dt[mem_group]."</td>";
				$mstring .= "</tr>";
				
				
				
				if($db1->dt[surtax_yorn] == "Y" && $db1->dt[delivery_price] > 0){
					$j++;
					$mstring .= "<tr bgcolor=#ffffff><td>".($j+1)."</td>";
					$mstring .= "<td>".$db1->dt[company_name]."</td>";
					$mstring .= "<td>".$db1->dt[date]."</td>";
					$mstring .= "<td>".$db1->dt[oid]."</td>";
					$mstring .= "<td>배송료</td>";
					$mstring .= "<td></td>";
					$mstring .= "<td></td>";
					$mstring .= "<td></td>";
					$mstring .= "<td></td>";
					
					$mstring .= "<td></td>";
					$mstring .= "<td></td>";
					$mstring .= "<td></td>";
					
					$mstring .= "<td>".(round($db1->dt[delivery_price]/1.1))."</td>";
					$mstring .= "<td>".($db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1))."</td>";
					$mstring .= "<td bgcolor=#efefef>".$db1->dt[delivery_price]."</td>";
									
					$mstring .= "<td>".(round($db1->dt[delivery_price]/1.1))."</td>";
					$mstring .= "<td>".(($db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1)))."</td>";
					$mstring .= "<td bgcolor=#efefef>".intval($db1->dt[delivery_price])."</td>";
					
					$mstring .= "<td></td>";
					$mstring .= "<td></td>";
					$mstring .= "<td></td>";
					
					$mstring .= "<td>100%</td>";
					$mstring .= "<td></td>";
					$mstring .= "<td>0</td>";
					$mstring .= "<td>0</td>";
					$mstring .= "<td>0</td>";
					$mstring .= "<td>".intval($db1->dt[delivery_price])."</td>";		
					$mstring .= "<td></td>";			
					$mstring .= "<td>".($status)."</td>";
					$mstring .= "<td>".$receipt_str."</td>";
					$mstring .= "<td>".$method."</td>";
					$mstring .= "<td>".$db1->dt[bname]."</td>";
					$mstring .= "<td>".$db1->dt[mem_group]."</td>";
					$mstring .= "</tr>";
				}
		$boid = $db1->dt[oid];
		//echo $db1->dt[bname]."\t".$db1->dt[rname]."\t".$db1->dt[addr]."\t".$db1->dt[zip]."\t".$db1->dt[rtel]."\t".$db1->dt[rmobile]."\t".$db1->dt[pname]."\t".$db1->dt[msg]."\n";
	//	$mstring .=  $db1->dt[oid]."\t".$db1->dt[company_id]."\t".$db1->dt[pname]."\t".$surtax_yorn."\t".$db1->dt[date]."\t".$db1->dt[bname]."\t".$gp_name."\t".$gp_name2."\t".$db1->dt[id]."\t".$db1->dt[btel]."\t".$db1->dt[bmobile]."\t".$db1->dt[rname]."\t".$db1->dt[zip]."\t".$db1->dt[addr]."\t".$db1->dt[rtel]."\t".$db1->dt[rmobile]."\t".$method."\t".$db1->dt[psprice]."\t".$db1->dt[pcnt]."\t".$db1->dt[reserve]."\t".$db1->dt[use_cupon_price]."\t".$db1->dt[delivery]."\t".$pack_method."\t".$db1->dt[use_baymoney_price]."\t".$db1->dt[use_reserve_price]."\t".$db1->dt[ptprice]."\t".strip_tags($status)."\t".$receipt_y."\n";
		
	
	}
}
$mstring .= "</table>";
echo $mstring;
//echo iconv("utf-8","CP949",$mstring);

?>
