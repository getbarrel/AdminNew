<?

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/include/commerce.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/include/util.php");

function salesByDateReportTable($vdate,$groupbytype="day",$SelectReport=1){
	global $depth,$referer_id, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status;
	global $search_sdate, $search_edate;

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
		$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
	}

//				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else '0' end) as order_amount_sum2,

	$sql = "select data.vdate, sum(data.order_sum) as order_sum,sum(data.order_amount_sum) as order_amount_sum, sum(order_sale_sum) as order_sale_sum, sum(order_coprice_sum) as order_coprice_sum, 
				sum(sale_cnt) as sale_cnt, 
				sum(sale_all_cnt) as sale_all_cnt, 
				sum(sale_all_sum) as sale_all_sum, sum(coprice_all_sum) as coprice_all_sum, 
				sum(cancel_cnt) as cancel_cnt, sum(cancel_sale_cnt) as cancel_sale_cnt, sum(cancel_sale_sum) as cancel_sale_sum, sum(cancel_coprice_sum) as cancel_coprice_sum, 
				sum(return_cnt) as return_cnt, sum(return_sale_cnt) as return_sale_cnt, sum(return_sale_sum) as return_sale_sum, sum(return_coprice_sum) as return_coprice_sum, 
				sum(whole_delivery_cnt) as whole_delivery_cnt, sum(whole_delivery_sum) as whole_delivery_sum, sum(return_delivery_cnt) as return_delivery_cnt, sum(return_delivery_sum) as return_delivery_sum
				from (
					Select ";
					
					if($groupbytype=="dashboard_week"){
						$sql .= "date_format(od.regdate,'%U')";
					}else if($groupbytype=="dashboard_month"){
						$sql .= "date_format(od.regdate,'%Y%m')" ;
					}else{
						$sql .= "date_format(od.regdate,'%Y%m%d')" ;
					}

					$sql .= " as vdate ,
					'0' as order_sum,
					sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else '0' end) as order_amount_sum,
					sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (od.pt_dcprice)  else '0' end) as order_sale_sum,
					sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as order_coprice_sum,
					0 as sale_cnt,
					sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else '0' end) as sale_all_cnt,
					sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as sale_all_sum,
					sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as coprice_all_sum,

                 '0' as cancel_cnt,
					sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else '0' end) as cancel_sale_cnt,
					sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as cancel_sale_sum,
					sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as cancel_coprice_sum,

                 '0' as return_cnt,
					sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else '0' end) as return_sale_cnt,
					sum(case when od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as return_sale_sum,
					sum(case when od.status IN ('".implode("','",$return_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as return_coprice_sum, 
					'0' as whole_delivery_cnt,
					'0' as whole_delivery_sum,
					'0' as return_delivery_cnt,
					'0' as return_delivery_sum
					from  shop_order_detail od";
					if($groupbytype=="today"){
						$sql .= " where od.regdate between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
					}else if($groupbytype=="dashboard_today"){
						$sql .= " where od.regdate between '".datestrReturn($vyesterday)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
					}else if($groupbytype=="dashboard_week"){
						$sql .= " where od.regdate between '".datestrReturn($voneweekago)." 00:00:00' and '".datestrReturn($vweekenddate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
					}else if($groupbytype=="dashboard_month"){
						$sql .= " where od.regdate between '".date("Y-m-01",strtotime("-1 month"))." 00:00:00' and '".datestrReturn($vdate, 'month_l')." 23:59:59'
						AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
					}else{
						$sql .= " where od.regdate between '".datestrReturn($vdate, 'month_f')." 00:00:00' and '".datestrReturn($vdate, 'month_l')." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
					}

                    $sql .= " and od.status not in ('SR','IB') ";

					if($groupbytype=="dashboard_week"){
						$sql .= " group by date_format(od.regdate,'%U')";
					}else if($groupbytype=="dashboard_month"){
						$sql .= " group by date_format(od.regdate,'%Y%m')";
					}else{
						$sql .= " group by date_format(od.regdate,'%Y%m%d')";
					}
					$sql .= "
					union 
					select ";
					
					if($groupbytype=="dashboard_week"){
						$sql .= "date_format(o.order_date,'%U')";
					}else if($groupbytype=="dashboard_month"){
						$sql .= "date_format(o.order_date,'%Y%m')" ;
					}else{
						$sql .= "date_format(o.order_date,'%Y%m%d')" ;
					}

					$sql .= " as vdate , 
					'0' as order_sum,
					'0' as order_amount_sum,
					'0' as order_sale_sum,
					'0' as order_coprice_sum,
					0 as sale_cnt,
					'0' as sale_all_cnt,
					'0' as sale_all_sum,
					'0' as coprice_all_sum,

                 '0' as cancel_cnt,
					'0' as cancel_sale_cnt,
					'0' as cancel_sale_sum,
					'0' as cancel_coprice_sum,

                 '0' as return_cnt,
					'0' as return_sale_cnt,
					'0' as return_sale_sum,
					'0' as return_coprice_sum, 
					sum(case when oph.price_div = 'D' and payment_status = 'G'  then 1 else '0' end) as whole_delivery_cnt,
					sum(case when oph.price_div = 'D' and payment_status = 'G'  then oph.expect_price else '0' end) as whole_delivery_sum,
					sum(case when oph.price_div = 'D' and payment_status = 'F'  then 1 else '0' end) as return_delivery_cnt,
					sum(case when oph.price_div = 'D' and payment_status = 'F'  then oph.expect_price else '0' end) as return_delivery_sum
					from 
					shop_order o, shop_order_price_history oph";
					if($groupbytype=="today"){
						$sql .= " where o.oid = oph.oid and o.order_date between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
					}else if($groupbytype=="dashboard_today"){
						$sql .= " where o.oid = oph.oid and o.order_date between '".datestrReturn($vyesterday)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
					}else if($groupbytype=="dashboard_week"){
						$sql .= " where o.oid = oph.oid and o.order_date between '".datestrReturn($voneweekago)." 00:00:00' and '".datestrReturn($vweekenddate)." 23:59:59' ";
					}else if($groupbytype=="dashboard_month"){
						$sql .= " where o.oid = oph.oid and o.order_date between '".date("Y-m-01",strtotime("-1 month"))." 00:00:00' and '".datestrReturn($vdate, 'month_l')." 23:59:59' ";
					}else{
						$sql .= " where o.oid = oph.oid and static_date LIKE '".substr($vdate,0,6)."%' ";
					}

                    $sql .= " and o.status not in ('SR','IB') ";

				if($groupbytype=="day" || $groupbytype=="dashboard_today"){
					$sql .= "group by static_date ";
				}else if($groupbytype=="dashboard_week"){
					$sql .= " group by date_format(o.order_date,'%U')";
				}else if($groupbytype=="dashboard_month"){
					$sql .= " group by date_format(o.order_date,'%Y%m')";
				}
					$sql .= "
					union 
					select 
					";
					
					if($groupbytype=="dashboard_week"){
						$sql .= "date_format(o.order_date,'%U')";
					}else if($groupbytype=="dashboard_month"){
						$sql .= "date_format(o.order_date,'%Y%m')";
					}else{
						$sql .= "static_date";
					}

					$sql .= " as vdate ,  
					count(*) as order_sum,
					'0' as order_amount_sum,
					'0' as order_sale_sum,
					'0' as order_coprice_sum,
					sum(case when o.status not in ('".implode("','",$all_sale_status)."')  then 1 else 0 end) as sale_cnt,
					'0' as sale_all_cnt,
					'0' as sale_all_sum,
					'0' as coprice_all_sum,

                  sum(case when (select count(*) from shop_order_detail od where od.oid=o.oid and od.status IN ('".implode("','",$cancel_status)."') limit 1) > 0  then 1 else 0 end) as cancel_cnt,
					'0' as cancel_sale_cnt,
					'0' as cancel_sale_sum,
					'0' as cancel_coprice_sum,

                 sum(case when (select count(*) from shop_order_detail od where od.oid=o.oid and od.status IN ('".implode("','",$return_status)."') limit 1) > 0  then 1 else 0 end) as return_cnt,
					'0' as return_sale_cnt,
					'0' as return_sale_sum,
					'0' as return_coprice_sum, 
					0 as whole_delivery_cnt,
					0 as whole_delivery_sum,
					0 as return_delivery_cnt,
					0 as return_delivery_sum
					from 
					shop_order o ";
					if($groupbytype=="today"){
						$sql .= " where o.order_date between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
					}else if($groupbytype=="dashboard_today"){
						$sql .= " where o.order_date between '".datestrReturn($vyesterday)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
					}else if($groupbytype=="dashboard_week"){
						$sql .= " where o.order_date between '".datestrReturn($voneweekago)." 00:00:00' and '".datestrReturn($vweekenddate)." 23:59:59' ";
					}else if($groupbytype=="dashboard_month"){
						$sql .= " where o.order_date between '".date("Y-m-01",strtotime("-1 month"))." 00:00:00' and '".datestrReturn($vdate, 'month_l')." 23:59:59' ";
					}else{
						$sql .= " where static_date LIKE '".substr($vdate,0,6)."%' ";
					}

                    $sql .= " and o.status not in ('SR','IB') ";

				if($groupbytype=="day" || $groupbytype=="dashboard_today"){
					$sql .= "group by static_date ";
				}else if($groupbytype=="dashboard_week"){
					$sql .= " group by date_format(o.order_date,'%U')";
				}else if($groupbytype=="dashboard_month"){
					$sql .= " group by date_format(o.order_date,'%Y%m')";
				}
					$sql .= "
				) data
				group by vdate ";

					//echo $sql; echo '<br><br>';
	
				//							left join ".TBL_COMMERCE_VIEWINGVIEW." b on od.pid = b.pid
				//AND od.status NOT IN ('".implode("','",$non_sale_status)."')
				//and substr(c.cid,1,".(($depth+1)*3).") = substr(b.cid,1,3)


		$dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

	if($sql){
		$fordb->query($sql);
	}

	if($groupbytype=="day"){
		$mstring = "<table width='100%' border=0>
						<tr><td>".TitleBar("상품군별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString)."</td><td align=right>(단위:원) </td></tr>
					</table>";
	}

	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>";
	if($groupbytype=="day"){
		$mstring .= "
						<col width='3%'>
						<col width='*'>";
	}else if($groupbytype=="dashboard_today" || $groupbytype=="dashboard_week" ||  $groupbytype=="dashboard_month"){
		$mstring .= "
						<col width='*'>";
	}
		$mstring .= "
						<col width='4%'>
						<col width='4%'>
						<col width='7%'>

						<col width='4%'>
						<col width='4%'>
						<col width='7%'>
						<col width='4%'>
						<col width='4%'>
						<col width='7%'>
						<col width='4%'>
						<col width='4%'>
						<col width='7%'>
						<col width='4%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='6%'>";
				if(!($groupbytype=="dashboard_today" || $groupbytype=="dashboard_week" ||  $groupbytype=="dashboard_month")){
			$mstring .= "
						<col width='4%'>
						<col width='7%'>
						<col width='4%'>
						<col width='7%'>
						<col width='4%'>
						<col width='7%'>
						<col width='7%'>";
				}
	$mstring .= "
		<tr height=30>";
		if($groupbytype=="day"){
			$mstring .= "
			<td class=s_td rowspan=3>순</td>
			<td class=m_td rowspan=3>날짜</td>";
		}else if($groupbytype=="dashboard_today" || $groupbytype=="dashboard_week" ||  $groupbytype=="dashboard_month"){
			$mstring .= "
			<td class=m_td rowspan=3>날짜</td>";
		}
			$mstring .= "
			<td class=m_td colspan=3>주문매출</td>
			<td class=m_td colspan=11>매출</td>
			<td class=m_td rowspan=3>실매출액<br>원가</td>
			<td class=m_td colspan=2 rowspan=2>수익</td>";
			if(!($groupbytype=="dashboard_today" || $groupbytype=="dashboard_week" ||  $groupbytype=="dashboard_month")){
			$mstring .= "
			<td class=m_td colspan=6>배송비</td>
			<td class=m_td rowspan=3 >매출액<br>(상품+배송비)</td>";
			}
$mstring .= "
		</tr>
		<tr height=30>
			<td class='m_td small' colspan=3 style='line-height:140%;'><b>전체주문</b><br>(입금예정포함)</td>
			<td class=m_td colspan=3>전체매출액(전체)</td>
			<td class=m_td colspan=3>취소매출액(-)</td>
			<td class=m_td colspan=3>반품매출액(-)</td>
			<td class=m_td colspan=2>실매출액(+)</td>";
			if(!($groupbytype=="dashboard_today" || $groupbytype=="dashboard_week" ||  $groupbytype=="dashboard_month")){
			$mstring .= "
			<td class=m_td colspan=2>전체매출액</td>
			<td class=m_td colspan=2>환불매출액</td>
			<td class=m_td colspan=2>실매출액</td>";
			}
$mstring .= "
		</tr>
		<tr height=30 align=center>
			<td class=m_td nowrap>주문(건)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>주문(건)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>주문(건)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>주문(건)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td >마진(원)</td>
			<td class=m_td >마진율(%)</td>";
				if(!($groupbytype=="dashboard_today" || $groupbytype=="dashboard_week" ||  $groupbytype=="dashboard_month")){
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

			sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_amount_sum,
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as order_sale_sum,
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as cancel_sale_sum,
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
							sum(case when od.status IN ('".implode("','",$return_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as return_sale_sum,

							*/

	for($i=0;$i<$fordb->total;$i++){
		$fordb->fetch($i);

		$real_sale_cnt = $fordb->dt[sale_all_cnt]-$fordb->dt[cancel_sale_cnt]-$fordb->dt[return_sale_cnt];
		$real_sale_sum = $fordb->dt[sale_all_sum]-$fordb->dt[cancel_sale_sum]-$fordb->dt[return_sale_sum];//실매출액원가
        ///var_dump($fordb->dt[sale_all_sum], $fordb->dt[cancel_sale_sum],$fordb->dt[return_sale_sum] ); exit;

		 $sale_coprice = $fordb->dt[coprice_all_sum]-$fordb->dt[cancel_coprice_sum]-$fordb->dt[return_coprice_sum];

        //var_dump( $fordb->dt[coprice_all_sum], $fordb->dt[cancel_coprice_sum], $fordb->dt[return_coprice_sum]); exit;
		$margin = $real_sale_sum - $sale_coprice;
		if($real_sale_sum > 0){
			$margin_rate = $margin/$real_sale_sum*100;
		}else{
			$margin_rate = 0;
		}

		if($groupbytype=="day" || $groupbytype=="dashboard_today" || $groupbytype=="dashboard_week" ||  $groupbytype=="dashboard_month"){
			
			$week_num = date("w",mktime(0,0,0,substr($fordb->dt[vdate],4,2),substr($fordb->dt[vdate],6,2),substr($fordb->dt[vdate],0,4)));

			$mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>";
				if($groupbytype=="day"){ 
				$mstring .= "<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>";
				
				}
			$mstring .= "
			<td class='list_box_td point' style='text-align:center;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>";
			if($groupbytype=="dashboard_week"){
				if($fordb->dt[vdate] == date("W")){
					$mstring .= "금주";
				}else{
					$mstring .= "전주";
				}
				//$mstring .= $fordb->dt[vdate]."==".date("W");
			}else if($groupbytype=="dashboard_month"){
				if($fordb->dt[vdate] == date("Ym")){
					$mstring .= "금월";
				}else{
					$mstring .= "전월";
				}
			}else if($groupbytype=="dashboard_today"){
				if($fordb->dt[vdate] == date("Ymd")){
					$mstring .= "금일";
				}else{
					$mstring .= "전일";
				}
			}else{
				$mstring .= "".str_replace(" ","<br>",getNameOfWeekday($week_num, $fordb->dt[vdate],"priodname"));
			}

			$mstring .= " </td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[order_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[order_amount_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[order_sale_sum],0)."</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[sale_cnt],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[sale_all_cnt],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[sale_all_sum],0)."</td>

            <td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[cancel_cnt],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[cancel_sale_cnt],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[cancel_sale_sum],0)."</td>

            <td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[return_cnt],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[return_sale_cnt],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[return_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($real_sale_cnt,0)."</td>
			<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($real_sale_sum,0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($sale_coprice,0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($margin,0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($margin_rate,0)." %</td>";
				if(!($groupbytype=="dashboard_today" || $groupbytype=="dashboard_week" ||  $groupbytype=="dashboard_month")){
			$mstring .= "
			
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[whole_delivery_cnt],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[whole_delivery_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[return_delivery_cnt],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[return_delivery_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".number_format($fordb->dt[whole_delivery_cnt]-$fordb->dt[return_delivery_cnt],0)."</td>
			<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum],0)."</td>";

			$real_sale_sum_with_deliveryprice = $real_sale_sum +  $fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum];
			$mstring .= "
			<td class='list_box_td number blue_point str' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_sum_with_deliveryprice,0)."</td>";
				}
			$mstring .= "
			</tr>\n";
		}

		$order_sum = $order_sum + returnZeroValue($fordb->dt[order_sum]);
		$order_amount_sum = $order_amount_sum + returnZeroValue($fordb->dt[order_amount_sum]);
		$order_sale_sum = $order_sale_sum + returnZeroValue($fordb->dt[order_sale_sum]);

		$sale_cnt = $sale_cnt + returnZeroValue($fordb->dt[sale_cnt]);
		$sale_all_cnt = $sale_all_cnt + returnZeroValue($fordb->dt[sale_all_cnt]);
		$sale_all_sum = $sale_all_sum + returnZeroValue($fordb->dt[sale_all_sum]);

        $cancel_cnt = $cancel_cnt + returnZeroValue($fordb->dt[cancel_cnt]);
		$cancel_sale_cnt = $cancel_sale_cnt + returnZeroValue($fordb->dt[cancel_sale_cnt]);
		$cancel_sale_sum = $cancel_sale_sum + returnZeroValue($fordb->dt[cancel_sale_sum]);

        $return_cnt = $return_cnt + returnZeroValue($fordb->dt[return_cnt]);
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
		if($groupbytype=="day"){
			$mstring .= "<tr  align=center height=30><td colspan=26 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}else{
			$mstring .= "<tr  align=center height=30><td colspan=24 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}
		
	}

	if($real_sale_sum_sum > 0){
		$margin_sum_rate = $margin_sum/$real_sale_sum_sum*100;
	}else{
		$margin_sum_rate = 0;
	}

	if($sale_all_sum != 0){
		if(!($groupbytype == "dashboard_today" || $groupbytype == "dashboard_week" || $groupbytype == "dashboard_month")){
			if($groupbytype=="day"){
				$mstring .= "<tr height=25 align=right>
				<td class=s_td align=center colspan=2>합계</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($order_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($order_amount_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_sum,0)."</td>

				<td class='e_td number' style='padding-right:10px;'>".number_format($sale_cnt,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_cnt,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_cnt,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_sale_cnt,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_sale_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($return_cnt,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($return_sale_cnt,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($return_sale_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($sale_coprice_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($margin_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($margin_sum_rate,0)." %</td>

				<td class='e_td number' style='padding-right:10px;'>".number_format($whole_delivery_cnt,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($whole_delivery_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($return_delivery_cnt,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($return_delivery_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($real_delivery_cnt,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($real_delivery_sum,0)."</td>
				<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_with_deliveryprice_sum,0)."</td>
				</tr>\n";
			}else{
				$mstring .= "<tr height=25 align=right>
				<td class='number' style='padding-right:10px;'>".number_format($order_sum,0)."</td>
				<td class='number' style='padding-right:10px;'>".number_format($order_amount_sum,0)."</td>
				<td class='number' style='padding-right:10px;'>".number_format($order_sale_sum,0)."</td>

				<td class='number' style='padding-right:10px;'>".number_format($sale_cnt,0)."</td>
				<td class='number' style='padding-right:10px;'>".number_format($sale_all_cnt,0)."</td>
				<td class='number' style='padding-right:10px;'>".number_format($sale_all_sum,0)."</td>
				<td class='number' style='padding-right:10px;'>".number_format($cancel_cnt,0)."</td>
				<td class='number' style='padding-right:10px;'>".number_format($cancel_sale_cnt,0)."</td>
				<td class='number' style='padding-right:10px;'>".number_format($cancel_sale_sum,0)."</td>
				<td class='number' style='padding-right:10px;'>".number_format($return_cnt,0)."</td>
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
		}
	}
	$mstring .= "</table>\n";

	if($groupbytype=="day"){
		$mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함</td></tr></table>";

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



function salesByDateFromReportTable($vdate,$groupbytype="day",$SelectReport=1){
	global $depth,$referer_id, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status, $not_real_sale_status;
	global $search_sdate, $search_edate;

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


	$sql = "Select date_format(od.regdate,'%Y%m%d') as vdate ,
				sum(case when order_from = 'self' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as self_sale_all_sum,
				sum(case when order_from = 'self' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as self_cancel_sale_sum,
				sum(case when order_from = 'self' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as self_return_sale_sum,

				sum(case when order_from = 'offline' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as offline_sale_all_sum,
				sum(case when order_from = 'offline' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as offline_cancel_sale_sum,
				sum(case when order_from = 'offline' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as offline_return_sale_sum,

				sum(case when order_from = 'pos' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as pos_sale_all_sum,
				sum(case when order_from = 'pos' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as pos_cancel_sale_sum,
				sum(case when order_from = 'pos' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as pos_return_sale_sum,

				sum(case when od.status not in ('".implode("','",$not_real_sale_status)."')  then (od.pt_dcprice)  else '0' end) as sale_sum
				from  shop_order_detail od";
				if($groupbytype=="today"){
					$sql .= " where od.regdate between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
				}else{
					$sql .= " where od.regdate between '".datestrReturn($vdate,"month_f")." 00:00:00' and '".datestrReturn($vdate,"month_l")." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
				}
				
				if($groupbytype=="day"){
					$sql .="group by date_format(od.regdate,'%Y%m%d')";
				}
				//				not_real_sale_status			left join ".TBL_COMMERCE_VIEWINGVIEW." b on od.pid = b.pid
				//AND od.status NOT IN ('".implode("','",$non_sale_status)."')
				//and substr(c.cid,1,".(($depth+1)*3).") = substr(b.cid,1,3)


		$dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

	//echo nl2br($sql);
	//exit;
	if($sql){
		$fordb->query($sql);
	}
	if($groupbytype=="day"){
		$mstring = "<table width='100%' border=0>
							<tr><td>".TitleBar("상품군별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString)."</td><td align=right>(단위:원) </td></tr>
						</table>";
	}
	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>";

	if($groupbytype=="day"){
		$mstring .= "
						<col width='3%'>";
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
		if($groupbytype=="day"){
			$mstring .= "
			<td class=s_td rowspan=2>날짜</td>";
		}
			$mstring .= "
			<td class=m_td rowspan=2>실매출액</td>
			<td class=m_td colspan=5>자체쇼핑몰</td>
			<td class=m_td colspan=5>오프라인 영업</td>
			<td class=m_td colspan=5>POS</td>

		</tr>
		<tr height=30 align=center>
			<td class=m_td nowrap>전체매출액</td>
			<td class=m_td >취소매출액</td>
			<td class=m_td nowrap>반품매출액</td>
			<td class=m_td >실매출액</td>
			<td class=m_td nowrap>점유율</td>
			<td class=m_td nowrap>전체매출액</td>
			<td class=m_td >취소매출액</td>
			<td class=m_td nowrap>반품매출액</td>
			<td class=m_td >실매출액</td>
			<td class=m_td nowrap>점유율</td>
			<td class=m_td nowrap>전체매출액</td>
			<td class=m_td >취소매출액</td>
			<td class=m_td nowrap>반품매출액</td>
			<td class=m_td >실매출액</td>
			<td class=m_td nowrap>점유율</td>
			</tr>\n";
			/*

			sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_amount_sum,
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as order_sale_sum,
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as cancel_sale_sum,
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
							sum(case when od.status IN ('".implode("','",$return_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as return_sale_sum,
$sql = "Select date_format(od.regdate,'%Y%m%d') as vdate ,
				sum(case when order_from = 'self' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as self_sale_all_sum,
				sum(case when order_from = 'self' and od.status IN ('".implode("','",$cancel_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as self_cancel_sale_sum,
				sum(case when order_from = 'self' and od.status IN ('".implode("','",$return_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as self_return_sale_sum,
				sum(case when order_from = 'self' then (od.ptprice-od.use_coupon)  else 0 end) as self_sale_sum,
				sum(case when order_from = 'offline' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as offline_sale_all_sum,
				sum(case when order_from = 'offline' and od.status IN ('".implode("','",$cancel_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as offline_cancel_sale_sum,
				sum(case when order_from = 'offline' and od.status IN ('".implode("','",$return_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as offline_return_sale_sum,
				sum(case when order_from = 'offline' then (od.ptprice-od.use_coupon)  else 0 end) as offline_sale_sum,
				sum(case when order_from = 'pos' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as pos_sale_all_sum,
				sum(case when order_from = 'pos' and od.status IN ('".implode("','",$cancel_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as pos_cancel_sale_sum,
				sum(case when order_from = 'pos' and od.status IN ('".implode("','",$return_status)."')  then (od.ptprice-od.use_coupon)  else 0 end) as pos_return_sale_sum,
				sum(case when order_from = 'pos' then (od.ptprice-od.use_coupon)  else 0 end) as pos_sale_sum,
				sum((od.ptprice-od.use_coupon) ) as sale_sum
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

		if($groupbytype=="day"){
			$week_num = date("w",mktime(0,0,0,substr($fordb->dt[vdate],4,2),substr($fordb->dt[vdate],6,2),substr($fordb->dt[vdate],0,4)));

			$mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".getNameOfWeekday($week_num, $fordb->dt[vdate],"priodname")." </td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[self_sale_all_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[self_cancel_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[self_return_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\"
			onmouseout=\"mouseOnTD('$i',false)\">".number_format($_self_sale_sum,0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $_self_sale_sum/$fordb->dt[sale_sum]*100 : 0),0)."%</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[offline_sale_all_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[offline_cancel_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[offline_return_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($_offline_sale_sum,0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $_offline_sale_sum/$fordb->dt[sale_sum]*100 : 0),0)."%</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[pos_sale_all_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[pos_cancel_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[pos_return_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($_pos_sale_sum,0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $_pos_sale_sum/$fordb->dt[sale_sum]*100 : 0),0)."%</td>
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
		if($groupbytype=="day"){
			$mstring .= "<tr  align=center height=30><td colspan=17 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}else{
			$mstring .= "<tr  align=center height=30><td colspan=16 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}
	}

	if($sale_sum != 0){
		if($groupbytype=="day"){
			$mstring .= "<tr height=25 align=right>
			<td class=s_td align=center>합계</td>
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
	}
	$mstring .= "</table>\n";

	if($groupbytype=="day"){
		$mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함</td></tr></table>";

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


		$mstring .= HelpBox("일별매출액(판매처)", $help_text);
	}
	return $mstring;
}


/**
 * 일별 매출액 (사이트)
 * @param $vdate
 * @param string $groupbytype
 * @param int $SelectReport
 * @return string
 */
function salesByDateMemypeReportTable($vdate,$groupbytype="day",$SelectReport=1){
	global $depth,$referer_id, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status, $not_real_sale_status;
	global $search_sdate, $search_edate;

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


	$sql = "Select date_format(od.regdate,'%Y%m%d') as vdate ,
				sum(case when buyer_type = '1' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as b2c_sale_all_sum,
				sum(case when buyer_type = '1' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as b2c_cancel_sale_sum,
				sum(case when buyer_type = '1' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as b2c_return_sale_sum,
				sum(case when buyer_type = '2' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as b2b_sale_all_sum,
				sum(case when buyer_type = '2' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as b2b_cancel_sale_sum,
				sum(case when buyer_type = '2' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as b2b_return_sale_sum,
				sum(case when od.status not in ('".implode("','",$not_real_sale_status)."') then (od.pt_dcprice)  else '0' end) as sale_sum
				from  shop_order_detail od
				where od.regdate between '".datestrReturn($vdate,"month_f")." 00:00:00' and '".datestrReturn($vdate,"month_l")." 23:59:59'
				AND od.status NOT IN ('".implode("','",$non_sale_status)."')
				and order_from = 'self'  ";
				if($groupbytype=="day"){
					$sql .= "group by date_format(od.regdate,'%Y%m%d')";
				}
				//							left join ".TBL_COMMERCE_VIEWINGVIEW." b on od.pid = b.pid
				//AND od.status NOT IN ('".implode("','",$non_sale_status)."')
				//and substr(c.cid,1,".(($depth+1)*3).") = substr(b.cid,1,3)

        //echo $sql; echo '<br><br>';
		$dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

	//echo nl2br($sql);
	if($sql){
		$fordb->query($sql);
	}

	if($groupbytype=="day"){
	$mstring = "<table width='100%' border=0>
						<tr><td>".TitleBar("상품군별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString)."</td><td align=right>(단위:원) </td></tr>
					</table>";
	}

	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>";
	if($groupbytype=="day"){
		$mstring .= "
						<col width='*'>";
	}
	$mstring .= "
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>";
	$mstring .= "
		<tr height=30>";
		if($groupbytype=="day"){
			$mstring .= "
			<td class=s_td rowspan=2>날짜</td>";
		}
		$mstring .= "
			<td class=m_td rowspan=2>실매출액</td>
			<td class=m_td colspan=5>B2C</td>
			<td class=m_td colspan=5>B2B</td>
		</tr>
		<tr height=30 align=center>
			<td class=m_td nowrap>전체매출액</td>
			<td class=m_td >취소매출액</td>
			<td class=m_td nowrap>반품매출액</td>
			<td class=m_td >실매출액</td>
			<td class=m_td nowrap>점유율</td>
			<td class=m_td nowrap>전체매출액</td>
			<td class=m_td >취소매출액</td>
			<td class=m_td nowrap>반품매출액</td>
			<td class=m_td >실매출액</td>
			<td class=m_td nowrap>점유율</td>
			</tr>\n";


	for($i=0;$i<$fordb->total;$i++){
		$fordb->fetch($i);

		if($groupbytype=="day"){
			$week_num = date("w",mktime(0,0,0,substr($fordb->dt[vdate],4,2),substr($fordb->dt[vdate],6,2),substr($fordb->dt[vdate],0,4)));

			$mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td point' style='text-align:center;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".getNameOfWeekday($week_num, $fordb->dt[vdate],"priodname")." </td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[b2c_sale_all_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[b2c_cancel_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[b2c_return_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[b2c_sale_all_sum]-$fordb->dt[b2c_cancel_sale_sum]-$fordb->dt[b2c_return_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[b2c_sale_all_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[b2b_sale_all_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[b2b_cancel_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[b2b_return_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[b2b_sale_all_sum]-$fordb->dt[b2b_cancel_sale_sum]-$fordb->dt[b2b_return_sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[b2b_sale_all_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>";

			$mstring .= "
			</tr>\n";
		}
		$sale_sum = $sale_sum + returnZeroValue($fordb->dt[sale_sum]);

		$b2c_sale_all_sum = $b2c_sale_all_sum + returnZeroValue($fordb->dt[b2c_sale_all_sum]);
		$b2c_cancel_sale_sum = $b2c_cancel_sale_sum + returnZeroValue($fordb->dt[b2c_cancel_sale_sum]);
		$b2c_return_sale_sum = $b2c_return_sale_sum + returnZeroValue($fordb->dt[b2c_return_sale_sum]);
		$b2c_sale_sum = $b2c_sale_sum + returnZeroValue($fordb->dt[b2c_sale_all_sum]-$fordb->dt[b2c_cancel_sale_sum]-$fordb->dt[b2c_return_sale_sum]);

		$b2b_sale_all_sum = $b2b_sale_all_sum + returnZeroValue($fordb->dt[b2b_sale_all_sum]);
		$b2b_cancel_sale_sum = $b2b_cancel_sale_sum + returnZeroValue($fordb->dt[b2b_cancel_sale_sum]);
		$b2b_return_sale_sum = $b2b_return_sale_sum + returnZeroValue($fordb->dt[b2b_return_sale_sum]);
		$b2b_sale_sum = $b2b_sale_sum + returnZeroValue($fordb->dt[b2b_sale_all_sum]-$fordb->dt[b2b_cancel_sale_sum]-$fordb->dt[b2b_return_sale_sum]);

	}

	if ($sale_sum == 0){
		if($groupbytype=="day"){
			$mstring .= "<tr  align=center height=200><td colspan=12 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}else{
			$mstring .= "<tr  align=center height=200><td colspan=11 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}
	}

	if($sale_sum != 0){
		if($groupbytype=="day"){
			$mstring .= "<tr height=25 align=right>
			<td class=s_td align=center>합계</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($sale_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($b2c_sale_all_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($b2c_cancel_sale_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($b2c_return_sale_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($b2c_sale_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $b2c_sale_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($b2b_sale_all_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($b2b_cancel_sale_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($b2b_return_sale_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($b2b_sale_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $b2b_sale_sum/$sale_sum*100 : 0),0)."%</td>
			</tr>\n";
		}else{
			$mstring .= "<tr height=25 align=right>
			<td class='number point' style='padding-right:10px;'>".number_format($sale_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format($b2c_sale_all_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format($b2c_cancel_sale_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format($b2c_return_sale_sum,0)."</td>
			<td class='number point' style='padding-right:10px;'>".number_format($b2c_sale_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $b2c_sale_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='number' style='padding-right:10px;'>".number_format($b2b_sale_all_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format($b2b_cancel_sale_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format($b2b_return_sale_sum,0)."</td>
			<td class='number point' style='padding-right:10px;'>".number_format($b2b_sale_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $b2b_sale_sum/$sale_sum*100 : 0),0)."%</td>
			</tr>\n";
		}
	}
	$mstring .= "</table>\n";

	if($groupbytype=="day"){
		$mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함</td></tr></table>";

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


		$mstring .= HelpBox("일별매출액(사이트)", $help_text);
	}
	return $mstring;
}


function salesByDatePaymenttypeReportTable($vdate,$groupbytype="day",$SelectReport=1){
	global $depth,$referer_id, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status, $not_real_sale_status;
	global $search_sdate, $search_edate;

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

    $group_name = "''";
    if(!$search_sdate && !$search_edate) {
        if ($vdate == "") {
            $vdate = date("Ymd", time());
            $vyesterday = date("Ymd", time() - 84600);
            $voneweekago = date("Ymd", time() - 84600 * 7);
        } else {

            if ($SelectReport == 3) {
                $vdate = $vdate . "01";
            }
            $vweekenddate = date("Ymd", mktime(0, 0, 0, substr($vdate, 4, 2), substr($vdate, 6, 2), substr($vdate, 0, 4)) + 60 * 60 * 24 * 6);
            $vyesterday = date("Ymd", mktime(0, 0, 0, substr($vdate, 4, 2), substr($vdate, 6, 2), substr($vdate, 0, 4)) - 60 * 60 * 24);
            $voneweekago = date("Ymd", mktime(0, 0, 0, substr($vdate, 4, 2), substr($vdate, 6, 2), substr($vdate, 0, 4)) - 60 * 60 * 24 * 7);
        }
        $regdateWhere = "regdate between '".datestrReturn($vdate,"month_f")." 00:00:00' and '".datestrReturn($vdate,"month_l")." 23:59:59'";
    }else{
        if($SelectReport == 4){
            $group_name = "DATE_FORMAT(regdate,'%H')";
        }
        $regdateWhere = "regdate between '".$search_sdate." 00:00:00' and '".$search_edate." 23:59:59'";
    }





		$sql = "Select ".$group_name." as group_name, date_format(regdate,'%Y%m%d') as vdate ,
			-- sum(case when method = '".ORDER_METHOD_BANK."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as bank_sum,
			sum(case when method = '".ORDER_METHOD_CARD."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as card_sum,
			sum(case when method = '".ORDER_METHOD_RESERVE."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as reserve_sum,
			sum(case when method = '".ORDER_METHOD_VBANK."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as vbank_sum,
			sum(case when method = '".ORDER_METHOD_ICHE."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as iche_sum,
			sum(case when method = '".ORDER_METHOD_PAYCO."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as payco_sum,
			sum(case when method = '".ORDER_METHOD_KAKAOPAY."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as kakao_sum,
			sum(case when method = '".ORDER_METHOD_NPAY."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as npay_sum,
			sum(case when method = '".ORDER_METHOD_EXIMBAY."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as eximbay_sum,
			sum(case when method = '".ORDER_METHOD_NOPAY."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as nopay_sum,
			sum(case when method = '".ORDER_METHOD_BANK."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as bank_sum,
			sum(case when method = '".ORDER_METHOD_TOSS."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as toss_sum,
			-- sum(case when method = '".ORDER_METHOD_CASH."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as cash_sum,
			
			-- sum(case when method = '".ORDER_METHOD_SAVEPRICE."' then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as save_sum,
			-- sum((case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end)) as sale_sum
			sum(case when method in (
			'".ORDER_METHOD_CARD."',
			'".ORDER_METHOD_RESERVE."',
			'".ORDER_METHOD_VBANK."',
			'".ORDER_METHOD_ICHE."',
			'".ORDER_METHOD_PAYCO."',
			'".ORDER_METHOD_KAKAOPAY."',
			'".ORDER_METHOD_NPAY."',
			'".ORDER_METHOD_NOPAY."',
			'".ORDER_METHOD_BANK."',
			'".ORDER_METHOD_TOSS."'
			) then (case when pay_type='F' then -IFNULL(payment_price,0) else IFNULL(payment_price,0) end) else '0' end) as sale_sum
			from  shop_order_payment
			where
				$regdateWhere
			and 
				pay_status in ('IC','LO') ";
            if($SelectReport == 4){
                $sql .= "group by ". $group_name ;
            }else{
                if($groupbytype=="day"){
                    $sql .= "group by date_format(regdate,'%Y%m%d')";
                }
            }

        if($SelectReport == '2'){
            $dateString = "기간 : ".$search_sdate." ~ ".$search_edate;
        }else{
            $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
        }

      //  echo $sql;
	//echo nl2br($sql);
	if($sql){
		$fordb->query($sql);
	}

    if(isset($_GET["mode"]) && $_GET["mode"] == "excel"){
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

        $sheet->getActiveSheet(0)->mergeCells('A2:Y2');
        $sheet->getActiveSheet(0)->setCellValue('A2', "결제타입별 매출액");
        $sheet->getActiveSheet(0)->mergeCells('A3:Y3');
        $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

        $sheet->getActiveSheet(0)->mergeCells('A'.($i+1).':A'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('B'.($i+1).':B'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('C'.($i+1).':C'.($i+2));

        $sheet->getActiveSheet(0)->mergeCells('D'.($i+1).':E'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('F'.($i+1).':G'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('H'.($i+1).':I'.($i+1));

        $sheet->getActiveSheet(0)->mergeCells('J'.($i+1).':K'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('L'.($i+1).':M'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('N'.($i+1).':O'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('P'.($i+1).':Q'.($i+1));

        $sheet->getActiveSheet(0)->mergeCells('R'.($i+1).':S'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('T'.($i+1).':U'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('V'.($i+1).':W'.($i+1));

		$sheet->getActiveSheet(0)->mergeCells('X'.($i+1).':Y'.($i+1));

        $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "순");
        $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), '날짜');
        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "실매출액(엑심베이제외)");
        $sheet->getActiveSheet(0)->setCellValue('D' . ($i+1), "신용카드");
        $sheet->getActiveSheet(0)->setCellValue('F' . ($i+1), "가상계좌");
        $sheet->getActiveSheet(0)->setCellValue('H' . ($i+1), "실시간계좌이체");

        $sheet->getActiveSheet(0)->setCellValue('J' . ($i+1), "PAYCO");
        $sheet->getActiveSheet(0)->setCellValue('L' . ($i+1), "카카오페이");
        $sheet->getActiveSheet(0)->setCellValue('N' . ($i+1), "네이버페이");
        $sheet->getActiveSheet(0)->setCellValue('P' . ($i+1), "엑심베이");
        $sheet->getActiveSheet(0)->setCellValue('R' . ($i+1), "적립금");
        $sheet->getActiveSheet(0)->setCellValue('T' . ($i+1), "무료결제");
        $sheet->getActiveSheet(0)->setCellValue('V' . ($i+1), "무통장결제");

		$sheet->getActiveSheet(0)->setCellValue('X' . ($i+1), "토스결제");


        $sheet->getActiveSheet(0)->setCellValue('D' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+2), "점유율");

        $sheet->getActiveSheet(0)->setCellValue('F' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+2), "점유율");

        $sheet->getActiveSheet(0)->setCellValue('H' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('I' . ($i+2), "점유율");

        $sheet->getActiveSheet(0)->setCellValue('J' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('K' . ($i+2), "점유율");

        $sheet->getActiveSheet(0)->setCellValue('L' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('M' . ($i+2), "점유율");

        $sheet->getActiveSheet(0)->setCellValue('N' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('O' . ($i+2), "점유율");

        $sheet->getActiveSheet(0)->setCellValue('P' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('Q' . ($i+2), "점유율");

        $sheet->getActiveSheet(0)->setCellValue('R' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('S' . ($i+2), "점유율");

        $sheet->getActiveSheet(0)->setCellValue('T' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('U' . ($i+2), "점유율");

        $sheet->getActiveSheet(0)->setCellValue('V' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('W' . ($i+2), "점유율");

		$sheet->getActiveSheet(0)->setCellValue('X' . ($i+2), "실매출액");
        $sheet->getActiveSheet(0)->setCellValue('Y' . ($i+2), "점유율");


        $sheet->setActiveSheetIndex(0);
        //$i = $i + 2;

        $order_cnt_sum = 0;
        $sale_order_cnt_sum = 0;
        $cancel_order_cnt_sum = 0;
        $return_order_cnt_sum = 0;
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $week_num = date("w",mktime(0,0,0,substr($fordb->dt[vdate],4,2),substr($fordb->dt[vdate],6,2),substr($fordb->dt[vdate],0,4)));

            if($SelectReport == '4'){
                $group_value = $fordb->dt['group_name']." 시";
            }else{
                $group_value = getNameOfWeekday($week_num, $fordb->dt[vdate],"priodname");
            }

            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), ($i + 1));
            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), $group_value);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3),number_format($fordb->dt[sale_sum],0));
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3),number_format($fordb->dt[card_sum],0)) ;
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), number_format(($fordb->dt[sale_sum] ? $fordb->dt[card_sum]/$fordb->dt[sale_sum]*100:0),0)."%");
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), number_format($fordb->dt[vbank_sum],0));
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), number_format(($fordb->dt[sale_sum] ? $fordb->dt[vbank_sum]/$fordb->dt[sale_sum]*100:0),0)."%");
            $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), number_format($fordb->dt[iche_sum],0));
            $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), number_format(($fordb->dt[sale_sum] ? $fordb->dt[iche_sum]/$fordb->dt[sale_sum]*100:0),0)."%");
            $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 3), number_format($fordb->dt[payco_sum],0));
            $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 3), number_format(($fordb->dt[sale_sum] ? $fordb->dt[payco_sum]/$fordb->dt[sale_sum]*100:0),0)."%");
            $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 3), number_format($fordb->dt[kakao_sum],0));
            $sheet->getActiveSheet()->getStyle('L' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 3), number_format(($fordb->dt[sale_sum] ? $fordb->dt[kakao_sum]/$fordb->dt[sale_sum]*100:0),0)."%");
            $sheet->getActiveSheet()->getStyle('M' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 3), number_format($fordb->dt[npay_sum],0));
            $sheet->getActiveSheet()->getStyle('N' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 3), number_format(($fordb->dt[sale_sum] ? $fordb->dt[npay_sum]/$fordb->dt[sale_sum]*100:0),0)."%");
            $sheet->getActiveSheet()->getStyle('O' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 3), number_format($fordb->dt[eximbay_sum],2));
            //$sheet->getActiveSheet()->getStyle('P' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('Q' . ($i + $start + 3), 0);
            $sheet->getActiveSheet()->getStyle('Q' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('R' . ($i + $start + 3), number_format($fordb->dt[reserve_sum],'-'));
            $sheet->getActiveSheet()->getStyle('R' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('S' . ($i + $start + 3), number_format(($fordb->dt[sale_sum] ? $fordb->dt[reserve_sum]/$fordb->dt[sale_sum]*100:0),0)."%");
            $sheet->getActiveSheet()->getStyle('S' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('T' . ($i + $start + 3), number_format($fordb->dt[nopay_sum],0));
            $sheet->getActiveSheet()->getStyle('T' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('U' . ($i + $start + 3), number_format(($fordb->dt[sale_sum] ? $fordb->dt[nopay_sum]/$fordb->dt[sale_sum]*100:0),0)."%");
            $sheet->getActiveSheet()->getStyle('U' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('V' . ($i + $start + 3), number_format($fordb->dt[bank_sum],0));
            $sheet->getActiveSheet()->getStyle('V' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('W' . ($i + $start + 3), number_format(($fordb->dt[sale_sum] ? $fordb->dt[bank_sum]/$fordb->dt[sale_sum]*100:0),0)."%");
            $sheet->getActiveSheet()->getStyle('W' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$sheet->getActiveSheet()->setCellValue('X' . ($i + $start + 3), number_format($fordb->dt[toss_sum],0));
            $sheet->getActiveSheet()->getStyle('X' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('Y' . ($i + $start + 3), number_format(($fordb->dt[sale_sum] ? $fordb->dt[toss_sum]/$fordb->dt[sale_sum]*100:0),0)."%");
            $sheet->getActiveSheet()->getStyle('Y' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


            $sale_sum = $sale_sum + returnZeroValue($fordb->dt[sale_sum]);

            $card_sum = $card_sum + returnZeroValue($fordb->dt[card_sum]);
            $vbank_sum = $vbank_sum + returnZeroValue($fordb->dt[vbank_sum]);
            $iche_sum = $iche_sum + returnZeroValue($fordb->dt[iche_sum]);
            $payco_sum = $payco_sum + returnZeroValue($fordb->dt[payco_sum]);
            $kakao_sum = $kakao_sum + returnZeroValue($fordb->dt[kakao_sum]);
            $npay_sum = $npay_sum + returnZeroValue($fordb->dt[npay_sum]);
            $eximbay_sum = $eximbay_sum + returnZeroValue($fordb->dt[eximbay_sum]);
            $reserve_sum = $reserve_sum + returnZeroValue($fordb->dt[reserve_sum]);
            $nopay_sum = $nopay_sum + returnZeroValue($fordb->dt[nopay_sum]);
            $bank_sum = $bank_sum + returnZeroValue($fordb->dt[bank_sum]);
			$toss_sum = $toss_sum + returnZeroValue($fordb->dt[toss_sum]);
            $save_sum = $save_sum + returnZeroValue($fordb->dt[save_sum]);


        }
        if($groupbytype=="day"){
            //$i++;
            $sheet->getActiveSheet(0)->mergeCells('A'.($i + $start+3).':B'.($i+ $start+3));
            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), '합계');
            //$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), getIventoryCategoryPathByAdmin($goods_infos[$i]['cid'], 4));
            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), number_format($sale_sum,0));
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), number_format($card_sum,0));
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), number_format(($sale_sum ? $card_sum/$sale_sum*100 : 0),0)."%");
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), number_format($vbank_sum,0));
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), number_format(($sale_sum ? $vbank_sum/$sale_sum*100 : 0),0)."%");
            $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), number_format($iche_sum,0));
            $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), number_format(($sale_sum ? $iche_sum/$sale_sum*100 : 0),0)."%");
            $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 3), number_format($payco_sum,0));
            $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 3), number_format(($sale_sum ? $payco_sum/$sale_sum*100 : 0),0)."%");
            $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 3), number_format($kakao_sum,0));
            $sheet->getActiveSheet()->getStyle('L' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 3), number_format(($sale_sum ? $kakao_sum/$sale_sum*100 : 0),0)."%");
            $sheet->getActiveSheet()->getStyle('M' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 3), number_format($npay_sum,0));
            $sheet->getActiveSheet()->getStyle('N' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


            $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 3), number_format(($sale_sum ? $npay_sum/$sale_sum*100 : 0),0)."%");
            $sheet->getActiveSheet()->getStyle('O' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 3), number_format($eximbay_sum,2));
            //$sheet->getActiveSheet()->getStyle('P' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('Q' . ($i + $start + 3), '-');
//            $sheet->getActiveSheet()->getStyle('Q' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


            $sheet->getActiveSheet()->setCellValue('R' . ($i + $start + 3), number_format($reserve_sum,0));
            $sheet->getActiveSheet()->getStyle('R' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('S' . ($i + $start + 3), number_format(($sale_sum ? $reserve_sum/$sale_sum*100 : 0),0)."%");
            $sheet->getActiveSheet()->getStyle('S' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('T' . ($i + $start + 3), number_format($nopay_sum,0));
            $sheet->getActiveSheet()->getStyle('T' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('U' . ($i + $start + 3), number_format(($sale_sum ? $nopay_sum/$sale_sum*100 : 0),0)."%");
            $sheet->getActiveSheet()->getStyle('U' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('V' . ($i + $start + 3), number_format($bank_sum,0));
            $sheet->getActiveSheet()->getStyle('V' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('W' . ($i + $start + 3), number_format(($sale_sum ? $bank_sum/$sale_sum*100 : 0),0)."%");
            $sheet->getActiveSheet()->getStyle('W' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

			$sheet->getActiveSheet()->setCellValue('X' . ($i + $start + 3), number_format($toss_sum,0));
            $sheet->getActiveSheet()->getStyle('X' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('Y' . ($i + $start + 3), number_format(($sale_sum ? $toss_sum/$sale_sum*100 : 0),0)."%");
            $sheet->getActiveSheet()->getStyle('Y' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

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
        $sheet->getActiveSheet()->getColumnDimension('P')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('R')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('S')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('T')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('U')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('V')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('W')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('X')->setWidth(10);
		$sheet->getActiveSheet()->getColumnDimension('Y')->setWidth(10);

        $sheet->getActiveSheet()->getRowDimension($start+2)->setRowHeight(30);

        // $objWriter->setUseInlineCSS(true);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $sheet->getActiveSheet()->getStyle('A'.($start+1).':Y'.($i+$start+3))->applyFromArray($styleArray);
        $sheet->getActiveSheet()->getStyle('A'.$start.':Y'.($i+$start+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $sheet->getActiveSheet()->getStyle('A'.$start.':Y'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($start).':Y'.($start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true);
        $sheet->getActiveSheet()->getStyle('B'.($start+3).':B'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        //$sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getAlignment()->setIndent(1);
        //$sheet->getActiveSheet()->getStyle('A10')->getAlignment()->setIndent(10); 왼쪽 들여쓰기
        $sheet->getActiveSheet()->getStyle('A'.$start.':Y'.($i+$start+3))->getFont()->setSize(10)->setName('돋움');

        $sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움');
        $sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        unset($styleArray);


		$sheet->getActiveSheet()->setTitle('일별매출액');
		$sheet->setActiveSheetIndex(0);

		//	wel_ 엑셀 다운로드 히스토리 저장
		$ig_db = new Database;
		$ig_excel_dn_history_SQL = "
			INSERT INTO
				ig_excel_dn_history
			SET
				code = '".$_SESSION['admininfo']['charger_ix']."',
				ip = '". $_SERVER['REMOTE_ADDR']."',
				dn_type = 'order_salesbydate_paymenttype',
				dn_reason = '".addslashes($_GET['irs'])."',
				dn_text = '".addslashes($_SERVER['HTTP_REFERER'].$QUERY_STRING)."',
				regDt = '".date("Y-m-d H:i:s")."'
		";
		$ig_db->query($ig_excel_dn_history_SQL);
		//	//wel_ 엑셀 다운로드 히스토리 저장

		$download_filename = '매출요약_매출상세분석_'.date("YmdHis").'.zip'; 
		$igExcel_file = '../excelDn/매출요약_매출상세분석_'.date("YmdHis").'.xls';
	

		$ig_dnFile_full = '../excelDn/'.$download_filename;


		$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
		$objWriter->save($igExcel_file);


		$ig_dnFile_full = '../excelDn/'.$download_filename;

		if(trim($_GET['ipw']) == "") {
			$ig_pw = "barrel";
		} else {
			$ig_pw = $_GET['ipw'];
		}


		shell_exec('zip -P '.$ig_pw.' -r ../excelDn/'.$download_filename.' '.$igExcel_file);

		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $download_filename . '"'); // 저장될 파일 이름
		header('Content-Transfer-Encoding: binary');
		header('Content-length: ' . filesize($ig_dnFile_full));
		header('Expires: 0');
		header("Pragma: public");

		ob_clean();
		flush();
		readfile($ig_dnFile_full);


		unlink($igExcel_file);
		unlink($ig_dnFile_full);

        exit;
    }
    //$exce_down_str = "<a href='?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";
	$exce_down_str = "<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0 style='cursor:pointer' 
	onclick=\"ig_excel_dn_chk('?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."');\">";


	if($groupbytype=="day"){
		$mstring = "<table width='100%' border=0>
							<tr><td>".TitleBar("상품군별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString)."</td><td align=right>(단위:원) </td></tr>
						</table>";
	}

	$mstring .="
    <table width='100%' border=0>
        <TR> 
            <TD> 
                ".search_date('startDate','endDate',$search_sdate,$search_edate,'','','readonly')."
                <label><input type='checkbox' name='SelectReport' id='SelectReport' value='4' ".CompareReturnValue("4",$SelectReport,"checked").">시간별 조회</label>
                <input type='button' value='검색' style='vertical-align: middle;' id='searchReport' />
            </TD>
        </TR>
    </table>
	<html>
  <head>
    <script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
    <script type=\"text/javascript\">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
      /*
        var data = google.visualization.arrayToDataTable([
          ['결제수단', '해당월', '전월', '작년'],
          ['신용카드', 1000, 400, 200],
          ['가상계좌', 1170, 460, 250],
          ['실시간계좌이체', 660, 1120, 300],
          ['PAYCO', 1030, 540, 350],
          ['카카오페이', 1030, 540, 350],
          ['네이버페이', 1030, 540, 350],
          ['엑심베이', 1030, 540, 350],
          ['적립금', 1030, 540, 350],
          ['무료결제', 1030, 540, 350],
          ['무통장', 1030, 540, 350]
        ]);*/
        var card_sum = 0;
        var vbank_sum = 0;
        var iche_sum = 0;
        var payco_sum = 0;
        var kakao_sum = 0;
        var npay_sum = 0;
        var eximbay_sum = 0;
        var reserve_sum = 0;
        var nopay_sum = 0;
        var bank_sum = 0;
		var toss_sum = 0;
        
        card_sum = parseInt($('#card_sum').attr('sum'));
        vbank_sum = $('#vbank_sum').attr('sum');
        iche_sum = $('#iche_sum').attr('sum');
        payco_sum = $('#payco_sum').attr('sum');
        kakao_sum = $('#kakao_sum').attr('sum');
        npay_sum = $('#npay_sum').attr('sum');
        eximbay_sum = $('#eximbay_sum').attr('sum');
        reserve_sum = $('#reserve_sum').attr('sum');
        nopay_sum = $('#nopay_sum').attr('sum');
        bank_sum = $('#bank_sum').attr('sum');
		toss_sum = $('#toss_sum').attr('sum');
        
        var data = google.visualization.arrayToDataTable([
          ['결제수단', '매출액'],
          ['신용카드', card_sum],
          ['가상계좌', vbank_sum],
          ['실시간계좌이체', iche_sum],
          ['PAYCO', payco_sum],
          ['카카오페이', kakao_sum],
          ['네이버페이', npay_sum],
          ['엑심베이', eximbay_sum],
          ['적립금', reserve_sum],
          ['무료결제', nopay_sum],
          ['무통장', bank_sum],
		  ['토스', toss_sum]
        ]);
        

        var options = {
          chart: {
            title: '결제타입 별 매출 금액',
            subtitle: '".$dateString."',
          },
          bars: 'vertical',
          vAxis: {
            format: 'decimal',
            minValue:0,
            gridlines: { count: 4 },
            viewWindow: {
                min: 0
            }
           },
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
      
      $(document).ready(function(){
        $('#searchReport').on('click',function(){
            var sDate = $('input[name=startDate]').val();
            var eDate = $('input[name=endDate]').val();
            var SelectReport = 2;
            if($('#SelectReport').is(':checked') == true){
                SelectReport = $('#SelectReport').val();
            }
            if(!sDate && !eDate){
                alert('날짜를 지정해 주세요');
                return false;
            }
           
            document.location.href='?SelectReport='+SelectReport+'&vdate=".$vdate."&search_sdate='+sDate+'&search_edate='+eDate;
        });
      });
    </script>
  </head>
  <body>
    <div id=\"columnchart_material\" style=\"width: 100%; height: 500px;\"></div>
  </body>
</html>
	";
	$mstring .="
	<table width=100%>
	    <tr> 
	        <td align='right'>".$exce_down_str."</td>
	    </tr>
	</table>
	";

	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>";
	if($groupbytype=="day"){
		$mstring .= "
						<col width='*'>";
	}
	$mstring .= "
						<col width='6%'>
						<col width='7%'>
						<col width='4%'>
						<col width='5%'>
						<col width='4%'>
						<col width='5%'>
						<col width='4%'>
						<col width='5%'>
						<col width='4%'>
						<col width='5%'>
						<col width='4%'>
						<col width='5%'>
						<col width='4%'>
						<col width='5%'>
						<col width='4%'>
						<col width='5%'>
						<col width='4%'>
						<col width='5%'>
						<col width='4%'>
						<col width='5%'>
						<col width='4%'>
						<col width='5%'>
						<col width='4%'>";
	$mstring .= "
		<tr height=30>";
		if($groupbytype=="day"){
			$mstring .= "
			<td class=s_td rowspan=2>날짜</td>";
		}
		$mstring .= "
			<td class=m_td rowspan=2>실매출액<br>(엑심베이제외)</td>
			<td class=m_td colspan=2>신용카드</td>			
			<td class=m_td colspan=2>가상계좌</td>
			<td class=m_td colspan=2>실시간계좌이체</td>
			<td class=m_td colspan=2>PAYCO</td>
			<td class=m_td colspan=2>카카오페이</td>
			<td class=m_td colspan=2>네이버페이</td>
			<td class=m_td colspan=2>엑심베이</td>
			<td class=m_td colspan=2>적립금</td>
			<td class=m_td colspan=2>무료결제</td>
			<td class=m_td colspan=2>무통장결제</td>
			<td class=m_td colspan=2>토스결제</td>
		</tr>
		<tr height=30 align=center>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			<td class=m_td nowrap>실매출액</td>
			<td class=m_td >점유율</td>
			</tr>\n";


	for($i=0;$i<$fordb->total;$i++){
		$fordb->fetch($i);

		if($groupbytype=="day"){
			$week_num = date("w",mktime(0,0,0,substr($fordb->dt[vdate],4,2),substr($fordb->dt[vdate],6,2),substr($fordb->dt[vdate],0,4)));

			if($SelectReport == '4'){
                $group_value = $fordb->dt['group_name']." 시";
            }else{
                $group_value = getNameOfWeekday($week_num, $fordb->dt[vdate],"priodname");
            }


			$mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td point' style='text-align:center;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$group_value."</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[sale_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[card_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[card_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[vbank_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[vbank_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[iche_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[iche_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[payco_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[payco_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[kakao_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[kakao_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>
			
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[npay_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[npay_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>
			
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[eximbay_sum],2)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">-</td>

            <td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[reserve_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[reserve_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>


			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i', true)\" onmouseout=\"mouseOnTD('$i', false)\">".number_format($fordb->dt[nopay_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i', true)\" onmouseout=\"mouseOnTD('$i', false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[nopay_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>
			
            <td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[bank_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[bank_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>
			
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[toss_sum],0)."</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt[sale_sum] ? $fordb->dt[toss_sum]/$fordb->dt[sale_sum]*100:0),0)."%</td>";

			$mstring .= "

			</tr>\n";
		}

		$sale_sum = $sale_sum + returnZeroValue($fordb->dt[sale_sum]);

		$card_sum = $card_sum + returnZeroValue($fordb->dt[card_sum]);
		$vbank_sum = $vbank_sum + returnZeroValue($fordb->dt[vbank_sum]);
		$iche_sum = $iche_sum + returnZeroValue($fordb->dt[iche_sum]);
        $payco_sum = $payco_sum + returnZeroValue($fordb->dt[payco_sum]);
        $kakao_sum = $kakao_sum + returnZeroValue($fordb->dt[kakao_sum]);
        $npay_sum = $npay_sum + returnZeroValue($fordb->dt[npay_sum]);
		$eximbay_sum = $eximbay_sum + returnZeroValue($fordb->dt[eximbay_sum]);
        $reserve_sum = $reserve_sum + returnZeroValue($fordb->dt[reserve_sum]);
        $nopay_sum = $nopay_sum + returnZeroValue($fordb->dt[nopay_sum]);
        $bank_sum = $bank_sum + returnZeroValue($fordb->dt[bank_sum]);
		$toss_sum = $toss_sum + returnZeroValue($fordb->dt[toss_sum]);
		$save_sum = $save_sum + returnZeroValue($fordb->dt[save_sum]);

	}

	if ($sale_sum == 0){
		if($groupbytype=="day"){
			$mstring .= "<tr  align=center height=200><td colspan=22 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}else{
			$mstring .= "<tr  align=center height=200><td colspan=15 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}
	}
	
	if($sale_sum != 0){
		if($groupbytype=="day"){
			$mstring .= "<tr height=25 align=right>
			<td class=s_td align=center>합계</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($sale_sum,0)."</td>
			<td class='e_td number' id='card_sum' sum='".$card_sum."' style='padding-right:10px;'>".number_format($card_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $card_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='e_td number' id='vbank_sum' sum='".$vbank_sum."' style='padding-right:10px;'>".number_format($vbank_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $vbank_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='e_td number' id='iche_sum' sum='".$iche_sum."' style='padding-right:10px;'>".number_format($iche_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $iche_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='e_td number' id='payco_sum' sum='".$payco_sum."' style='padding-right:10px;'>".number_format($payco_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $payco_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='e_td number' id='kakao_sum' sum='".$kakao_sum."' style='padding-right:10px;'>".number_format($kakao_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $kakao_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='e_td number' id='npay_sum' sum='".$npay_sum."' style='padding-right:10px;'>".number_format($npay_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $npay_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='e_td number' id='eximbay_sum' sum='".$eximbay_sum."' style='padding-right:10px;'>".number_format($eximbay_sum,2)."</td>
			<td class='e_td number' style='padding-right:10px;'>-</td>
			<td class='e_td number' id='reserve_sum' sum='".$reserve_sum."' style='padding-right:10px;'>".number_format($reserve_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $reserve_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='e_td number' id='nopay_sum' sum='".$nopay_sum."' style='padding-right:10px;'>".number_format($nopay_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $nopay_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='e_td number' id='bank_sum' sum='".$bank_sum."' style='padding-right:10px;'>".number_format($bank_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $bank_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='e_td number' id='toss_sum' sum='".$toss_sum."' style='padding-right:10px;'>".number_format($toss_sum,0)."</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format(($sale_sum ? $toss_sum/$sale_sum*100 : 0),0)."%</td>
			</tr>\n";
		}else{
			$mstring .= "<tr height=25 align=right>
			<td class='number point' style='padding-right:10px;'>".number_format($sale_sum,0)."</td>
			<td class='number' id='card_sum' sum='".$card_sum."' style='padding-right:10px;'>".number_format($card_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $card_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='number' id='vbank_sum' sum='".$vbank_sum."' style='padding-right:10px;'>".number_format($vbank_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $vbank_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='number' id='iche_sum' sum='".$iche_sum."' style='padding-right:10px;'>".number_format($iche_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $iche_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='number' id='payco_sum' sum='".$payco_sum."' style='padding-right:10px;'>".number_format($payco_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $payco_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='number' id='kakao_sum' sum='".$kakao_sum."' style='padding-right:10px;'>".number_format($kakao_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $kakao_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='number' id='npay_sum' sum='".$npay_sum."' style='padding-right:10px;'>".number_format($npay_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $npay_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='number' id='eximbay_sum' sum='".$eximbay_sum."' style='padding-right:10px;'>".number_format($eximbay_sum,2)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $eximbay_sum/$sale_sum*100 : 0),2)."%</td>
			<td class='number' id='reserve_sum' sum='".$reserve_sum."' style='padding-right:10px;'>".number_format($reserve_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $reserve_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='number' id='nopay_sum' sum='".$nopay_sum."' style='padding-right:10px;'>".number_format($nopay_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $nopay_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='number' id='bank_sum' sum='".$bank_sum."' style='padding-right:10px;'>".number_format($bank_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $bank_sum/$sale_sum*100 : 0),0)."%</td>
			<td class='number' id='toss_sum' sum='".$toss_sum."' style='padding-right:10px;'>".number_format($toss_sum,0)."</td>
			<td class='number' style='padding-right:10px;'>".number_format(($sale_sum ? $toss_sum/$sale_sum*100 : 0),0)."%</td>
			</tr>\n";
		}
	}
	$mstring .= "</table>\n";

	if($groupbytype=="day"){
		$mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함</td></tr></table>";

		/*
		$help_text = "
		<table>
			<tr>
				<td style='line-height:150%'>
				- 카테고리별 상품조회 회수를 바탕으로 귀사 결제타입의 인기카테고리와 비인기 카테고리를 정확히 파악하여 그에 맞는 운영및 마케팅 정책을 수립 수행할수 있습니다<br>
				- 좌측 카테고리를 클릭하면 하부 카테고리에 대한 상세 정보가 표시 됩니다<br><br>
				</td>
			</tr>
		</table>
		";*/
		$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B' );


		$mstring .= HelpBox("일별매출액(결제타입)", $help_text);
	}
	return $mstring;
}


function datestrReturn($date,$type="day"){
	if($type=="month"){
		$return = substr($date,0,4)."-".substr($date,4,2);
	}elseif($type=="month_f"){
		$return = substr($date,0,4)."-".substr($date,4,2)."-01";
	}elseif($type=="month_l"){
		$return = substr($date,0,4)."-".substr($date,4,2)."-".date("t",strtotime($date.'01'));
	}else{
		$return = substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2);
	}
	return $return;
}
?>
<script type="text/javascript">
	//	wel_ 새벽시간(23시~07시)이나 휴무일 등 업무시간 외 다운로드시 검수 member_excel2003
	function ig_excel_dn_chk(s_val_Data) {
		//console.log(s_val_Data);
		var ig_now = new Date();   //현재시간
		var ig_hour = ig_now.getHours();   //현재 시간 중 시간.

			//	새벽시간(23시~07시), 휴무일(일, 토)
		//if(Number(ig_hour) >= "23" || Number(ig_hour) <= "7" || Number(ig_now.getDay()) == "0" || Number(ig_now.getDay()) == "6") {
			var ig_inputString = prompt('사유를 간략하게 입력하세요.\r\n(20자 이내(띄어쓰기포함), 특수문자 제외)');

			if(ig_inputString != null && ig_inputString.trim() != "") {
				//	엑셀다운로드 진행

					var str_length = ig_inputString.length;		// 전체길이

					if(str_length > "20") {
						alert("사유가 20자 이상 입니다.");
						return false;
					} else {
						var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

							if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

								var str_PW_length = ig_inputString_PW.length;		// 전체길이

								if(str_PW_length > "15") {
									alert("비밀번호를 15자 이하로 해주세요.");
									return false;
								} else {
									location.href = s_val_Data+"irs="+ig_inputString+"&ipw="+ig_inputString_PW;
								}

							} else {
								alert("비밀번호를 입력해 주세요.");
								return false;
							}
					}


			} else {
				alert("사유를 입력하세요");
				return false;
			}
		/*} else {
			//	일반 업무때 다운로드
			var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

				if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

					var str_PW_length = ig_inputString_PW.length;		// 전체길이

					if(str_PW_length > "15") {
						alert("비밀번호를 15자 이하로 해주세요.");
						return false;
					} else {
						location.href = s_val_Data+"&ipw="+ig_inputString_PW;
					}

				} else {
					alert("비밀번호를 입력해 주세요.");
					return false;
				}
		}*/



	}
</script>