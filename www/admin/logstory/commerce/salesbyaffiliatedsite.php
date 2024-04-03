<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");
include("../include/commerce.lib.php");


function ReportTable($vdate,$SelectReport=1){

    global $code, $cid, $depth, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status;
    global $search_sdate, $search_edate, $report_type, $price_type;
    $nview_cnt = 0;
    //$code = $referer_id; // 기여사이트 코드와 충돌 되서 변경함



    $order_sale_sum = 0;
    $real_sale_coprice_sum = 0;
    $order_sale_cnt = 0;
    $sale_all_cnt = 0;
    $sale_all_sum = 0;
    $real_sale_cnt_sum = 0;
    $cancel_sale_sum = 0;
    $sale_coprice_sum = 0;
    $cancel_sale_cnt = 0;
    $margin_sum = 0;
    $return_sale_cnt = 0;
    $return_sale_sum = 0;

    if($SelectReport == ""){
        $SelectReport = 1;
    }elseif($SelectReport == "2" || $SelectReport == "4"){
        $SelectReport = 3;
    }

    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();

    if($depth == ""){
        $depth = 0;
    }else{
        $depth = $depth+1;
    }
    if($SelectReport == "4"){
        $vdate = $search_sdate;
        $vweekenddate = $search_edate;
    }

    $params = explode('&', $_SERVER['QUERY_STRING']);

    foreach ($params as $param) {
        if(!empty($param)) {
            $name_value = explode('=', $param);
            $name = $name_value[0];
            $value = $name_value[1];

            if($name == 'order_from%5B%5D'){
                $order_from[] = $value;
            }elseif($name == 'edate' || $name == 'sdate'){
                $name = $value;
            }
        }
    }

    if($price_type == 1 || empty($price_type)){
        $price_type_q=' od.pt_dcprice ';
    }elseif($price_type == 2){
        $price_type_q=' CAST( od.psprice*od.pcnt AS SIGNED ) ';
    }

    if($price_type == 1 || empty($price_type)){
        $date_type=' od.regdate ';
    }elseif($price_type == 2){
        $date_type=' od.ic_date ';
    }

    if(empty($_GET['sdate']) || empty($_GET['edate'])){
        $edate = date("Y-m-d", time());
        $sdate = date("Y-m-d", time());
        $compare_edate = date("Y-m-d", strtotime('-1 week'));
        $compare_sdate = date('Y-m-d', strtotime('-1 week'));

        $vdate = date("Y-m-d", time());
        $vdate2 = date("Y-m-d", time());

        $vyesterday = date("Y-m-d", time()-84600);
        $v15ago = date("Y-m-d", time()-84600*15);
        $vonemonthago = date("Y-m-d", time()-84600*30);
        $v2monthago = date("Y-m-d", time()-84600*60);
        $v3monthago = date("Y-m-d", time()-84600*90);
        $voneweekago = date("Y-m-d", time()-84600*7);
        $search_sdate = date("Y-m-d", time()-84600*7);
        $search_edate = date("Y-m-d", time());
    }else{
        $sdate = $_GET['sdate'];
        $edate = $_GET['edate'];
        $compare_sdate = $_GET['compare_sdate'];
        $compare_edate = $_GET['compare_edate'];

        $vdate = date("Y-m-d", time());
        $vdate2 = date("Y-m-d", time());

        $vweekenddate = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24*7);
        $v15ago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24*15);
        $vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24*30);
        $v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24*60);
        $v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24*90);
    }

    if(isset($_GET["orderby"]) && $_GET["orderby"] != "" && isset($_GET["ordertype"]) && $_GET["ordertype"] != ""){
        $orderbyString = " order by ".$_GET["orderby"]." ".$_GET["ordertype"].", real_sale_sum desc ";
    }else{
        $orderbyString = " order by  code asc , vdate desc "; //order_sale_cnt desc ,
    }

    $where = " where 1=1 ";

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

    $coprice_str = "case when (od.commission > 0 and od.coprice = 0) then od.psprice-(od.psprice*od.commission/100) else od.coprice end ";


    $basic_sql = "select od.compare_sales,od.vdate,od.order_from,od.regdate, site_name as name,
						sum(order_sale_cnt) as order_sale_cnt,sum(order_sale_sum) as order_sale_sum,sum(order_coprice_sum) as order_coprice_sum,
						sum(sale_all_cnt) as sale_all_cnt,sum(sale_all_sum) as sale_all_sum,sum(coprice_all_sum) as coprice_all_sum,
						sum(cancel_sale_cnt) as cancel_sale_cnt,sum(cancel_sale_sum) as cancel_sale_sum,sum(cancel_coprice_sum) as cancel_coprice_sum,
						sum(return_sale_cnt) as return_sale_cnt,sum(return_sale_sum) as return_sale_sum,sum(return_coprice_sum) as return_coprice_sum,
						 IFNULL(ssi.site_code,'0') as code from
						( Select case when ".$date_type." between '".$sdate." 00:00:00' and '".$edate." 23:59:59' 
						then '01'
						when ".$date_type." between '".$compare_sdate." 00:00:00' and '".$compare_edate." 23:59:59'
						then '02' 
						else '03' end as compare_sales,
						date_format( ".$date_type." , '%Y-%m-%d') as vdate, od.order_from,
						date_format( ".$date_type." , '%Y-%m-%d') as regdate,
						sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."') then od.pcnt else 0 end) as order_sale_cnt, 
						sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."') then ".$price_type_q." else 0 end) as order_sale_sum, 
						sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."') then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 

						sum(case when od.status not in ('".implode("','",$all_sale_status)."') then od.pcnt else 0 end) as sale_all_cnt, 
						sum(case when od.status not in ('".implode("','",$all_sale_status)."') then ".$price_type_q." else 0 end) as sale_all_sum, 
						sum(case when od.status not in ('".implode("','",$all_sale_status)."') then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

						0 as cancel_sale_cnt, 
						0 as cancel_sale_sum, 
						0 as cancel_coprice_sum, 

						0 as return_sale_cnt, 
						0 as return_sale_sum, 
						0 as return_coprice_sum

						from 
						shop_order_detail od

						".$where." and od.status NOT IN ('".implode("','",$non_sale_status)."') {date_where1}
						group by od.order_from, compare_sales 


						UNION

						Select case 
						when fc_date between '".$sdate." 00:00:00' and '".$edate." 23:59:59' 
						then '01'
						when fc_date between '".$compare_sdate." 00:00:00' and '".$compare_edate." 23:59:59'
						then '02' 
						else '03' end as compare_sales,
						date_format( ".$date_type." , '%Y-%m-%d') as vdate, od.order_from,
						date_format( ".$date_type." , '%Y-%m-%d') as regdate,
						0 as order_sale_cnt, 
						0 as order_sale_sum, 
						0 as order_coprice_sum, 

						0 as sale_all_cnt, 
						0 as sale_all_sum, 
						0 as coprice_all_sum,

						sum(case when od.status='CC' then od.pcnt else 0 end) as cancel_sale_cnt, 
						sum(case when od.status='CC' then ".$price_type_q." else 0 end) as cancel_sale_sum, 
						sum(case when od.status='CC' then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

						sum(case when od.status='RC' then od.pcnt else 0 end) as return_sale_cnt, 
						sum(case when od.status='RC' then ".$price_type_q." else 0 end) as return_sale_sum, 
						sum(case when od.status='RC' then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum 

						from 
						shop_order_detail od

						".$where." and od.status NOT IN ('".implode("','",$non_sale_status)."') {date_where2}
						group by od.order_from, compare_sales 

						) od
						left join sellertool_site_info ssi on od.order_from = ssi.site_code
						group by od.order_from, od.compare_sales 
						order by od.order_from, od.compare_sales
				";



    $date_where1 = " and ( ".$date_type." between '".$sdate." 00:00:00' and '".$edate." 23:59:59' or ".$date_type." between '".$compare_sdate." 00:00:00' and '".$compare_edate." 23:59:59' ) ";
    $date_where2 = " and ( fc_date between '".$sdate." 00:00:00' and '".$edate." 23:59:59' or fc_date between '".$compare_sdate." 00:00:00' and '".$compare_edate." 23:59:59' ) ";

    $sql = str_replace("{date_where1}", $date_where1, $basic_sql);
    $sql = str_replace("{date_where2}", $date_where2, $sql);

    $dateString = "매출 기간 : ".$sdate." - ".$edate."<br>";
    $dateString .= "대비 기간 : ".$compare_sdate." - ".$compare_edate;

    if($sql){
        $fordb->query($sql);
    }

    $dateString .= " (".($code ? getCategoryPath($code,4):"전체").")";

    $mstring = "";
    $mstring .="
	<script type='text/javascript'>
	<!--
		function setSelectDate(FromDate,ToDate,dType) {
			var frm = document.searchmember;
			if(dType == 2){
				$(\"#memreg_sdate\").val(FromDate);
				$(\"#memreg_edate\").val(ToDate);
			}else{
				$(\"#start_datepicker\").val(FromDate);
				$(\"#end_datepicker\").val(ToDate);
			}
		}

		function setSelectDate2(FromDate,ToDate,dType) {
			var frm = document.searchmember;
			if(dType == 2){
				$(\"#memreg_sdate\").val(FromDate);
				$(\"#memreg_edate\").val(ToDate);
			}else{
				$(\"#start_datepicker2\").val(FromDate);
				$(\"#end_datepicker2\").val(ToDate);
			}
		}

	//-->
	</script>
	<table cellpadding=0 cellspacing=0 width=100%>
	<tr height=150>
		<td   >
			<form name='search_form' method='get' action='' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='groupbytype' value='$groupbytype'>
			<input type='hidden' name='SubID' value='$SubID' />
			<input type='hidden' name='eprice' value='1000000' />
			<input type='hidden' name='price_type' value='".$_GET['price_type']."' />
			<table class='box_shadow' style='width:1100px;margin-left: 10%;' cellpadding='0' cellspacing='0' border='0' ><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:1px'>
						<table cellpadding=0 cellspacing=1 border=0 width=70% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>";
    $mstring .= "
							<tr height=27>
							  <td class='search_box_title'><b>매출액 기간</b></td>
							  <td class='search_box_item' colspan=3 >
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=20>
									<col width=70>
									<col width=*>
									<tr>
										<td>
										".Log_search_date('sdate','edate',$sdate,$edate,'N')."
										</td>
									</tr>
								</table>
							  </td>
							</tr>
							<tr height=27>
							  <td class='search_box_title'><b>대비 매출액 기간</b></td>
							  <td class='search_box_item' colspan=3 >
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=20>
									<col width=70>
									<col width=*>
									<tr>
										<td>
										".Log_search_date('compare_sdate','compare_edate',$compare_sdate,$compare_edate,'N')."
										</td>
									</tr>
								</table>
							  </td>
							</tr>
							";
    $mstring .="<tr>
								<td class='search_box_title'><b>제휴처</b></td>
								<td class='search_box_item' colspan=3 >
									<table cellpadding=0 cellspacing=0 width='100%' border='0' >
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<TR height=25>
								";

    $fordb2->query("select * from sellertool_site_info where disp='1' order by vieworder");
    $sell_order_from=$fordb2->fetchall();
    if(count($sell_order_from) > 0){

        for($i=0;$i<count($sell_order_from);$i++){

            if($i==5 || ($i > 5 && $i%8==5)) $mstring .= "</TR><TR>";

            $mstring .= "<TD><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i]['site_code']."' value='".$sell_order_from[$i]['site_code']."' ".CompareReturnValue($sell_order_from[$i]['site_code'],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i]['site_code']."'>".$sell_order_from[$i]['site_name']."</label></TD>";

        }
    }

    $mstring .="
									</TR>
								</table>
								</td>
							</tr>";
    $mstring .=	"
						</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
				<tr >
					<td colspan=2 align=center style='padding:10px 0px'><input type=image src='../../images/".$_SESSION["admininfo"]["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	</table>";

    $mstring .= "<table width='100%' border=0>";
    if(isset($_GET["mode"]) && ($_GET["mode"] == "pop" || $_GET["mode"] == "print")){
        $mstring .= "<tr><td >".TitleBar("제휴처별  매출 분석 : ".($code ? "-".getCategoryPath($code,4):""),$dateString,false, $exce_down_str)."</td></tr>";
    }else{
        $mstring .= "<tr><td >".TitleBar("제휴처별  매출 분석 : ".($code ? "-".getCategoryPath($code,4):""),$dateString,false, $exce_down_str)."</td></tr>";
    }
    $mstring .= "
						<tr height=50>
							<td >
								<div class='tab'>
											<table class='s_org_tab'>
											<tr>
												<td class='tab'> 
													<table id='tab_01'  ".(($report_type == '3') ? "class=on":"").">
													<tr>
														<th class='box_01'></th>
														<td class='box_02' onclick=\"document.location.href='?SubID=".$_GET["SubID"]."&report_type=3'\">매출요약 분석</td>
														<th class='box_03'></th>
													</tr>
													</table> 
													<table id='tab_02'  ".($report_type == '1' && $price_type == '1' ? "class=on":"").">
													<tr>
														<th class='box_01'></th>
														<td class='box_02' onclick=\"document.location.href='?SubID=".$_GET["SubID"]."&report_type=1&price_type=1'\">매출상세 분석(주문일자)</td>
														<th class='box_03'></th>
													</tr>
													</table> 
													<table id='tab_03' ".(($report_type == '1' && $price_type == '2') || $report_type == '' || $price_type == '' ? "class=on":"")." >
													<tr>
														<th class='box_01'></th>
														<td class='box_02' onclick=\"document.location.href='?SubID=".$_GET["SubID"]."&report_type=1&price_type=2'\">매출상세 분석(입금확인)</td>
														<th class='box_03'></th>
													</tr>
													</table> 
												</td>
												<td class='btn' style='padding:5px 0px 0px 10px;'>
													
												</td>
											</tr>
											</table>
										</div>
							</td>
						  </tr>
					</table>";
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>
						<col width='15%'>
						<col width='7%'>
						<col width='5%'>
						<col width='7%'>
						<col width='5%'>
						<col width='7%'>
						<col width='5%'>
						<col width='7%'>
						<col width='5%'>
						<col width='7%'>
						<col width='5%'>
						<col width='7%'>
						<!--col width='7%'>
						<col width='5%'>
						<col width='7%'-->";
    $mstring .= "
		<tr height=30>
			<!--td class=s_td rowspan=3>순</td-->
			<td class=m_td rowspan=3 colspan=2>제휴처명</td>
			<td class=m_td colspan=2>주문매출</td>
			<td class=m_td colspan=8>매출</td>
			<!--td class=m_td rowspan=3>실매출액<br>원가</td>
			<td class=m_td colspan=2 rowspan=2>수익</td-->
		</tr>
		<tr height=30>
			<td class='m_td small' colspan=2 style='line-height:140%;'><b>전체주문</b><br>".($price_type == 1 ? "(입금예정포함)": "")."</td>
			<td class=m_td colspan=2>전체매출액(전체)</td>
			<td class=m_td colspan=2>취소매출액(-)</td>
			<td class=m_td colspan=2>반품매출액(-)</td>
			<td class=m_td colspan=2>실매출액(+)</td>
		</tr>
		<tr height=30 align=center>			
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>".OrderByLink("수량(개)", "real_sale_cnt", $ordertype)."</td>
			<td class=m_td >".OrderByLink("주문액(원)", "real_sale_sum", $ordertype)."</td>
			<!--td class=m_td >마진(원)</td>
			<td class=m_td >마진율(%)</td-->
			</tr>\n";
    /*

    sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
                    sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum,
                    sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
                    sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum,
                    sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
                    sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum,

                    */

    $sales_data_arr = $fordb->fetchall("object");

    unset($newarray);
    $newarray = array();

    for($kk=0; $kk<count($sales_data_arr); $kk++){

        if($sales_data_arr[$kk]['compare_sales'] == '01'){
            if($sales_data_arr[($kk+1)]['compare_sales'] != '02'){

                $newarray[] = $sales_data_arr[$kk];

                $newarray[] = array(
                    'compare_sales' => '02',
                    'code' => $sales_data_arr[$kk]['code'],
                    'name' => $sales_data_arr[$kk]['name'],
                    'regdate' => 0,
                    'order_sale_cnt01' => 0,
                    'order_sale_sum01' => 0,
                    'order_coprice_sum01' => 0.0000,
                    'order_sale_cnt02' => 0,
                    'order_sale_sum02' => 0,
                    'order_coprice_sum02' => 0.0000,
                    'sale_all_cnt01' => 0,
                    'sale_all_sum01' => 0,
                    'coprice_all_sum01' => 0.0000,
                    'sale_all_cnt02' => 0,
                    'sale_all_sum02' => 0,
                    'coprice_all_sum02' => 0.0000,
                    'cancel_sale_cnt01' => 0,
                    'cancel_sale_sum01' => 0,
                    'cancel_coprice_sum01' => 0.0000,
                    'cancel_sale_cnt02' => 0,
                    'cancel_sale_sum02' => 0,
                    'cancel_coprice_sum02' => 0.0000,
                    'return_sale_cnt01' => 0,
                    'return_sale_sum01' => 0,
                    'return_coprice_sum01' => 0.0000,
                    'return_sale_cnt02' => 0,
                    'return_sale_sum02' => 0,
                    'return_coprice_sum02' => 0.0000,
                    'amount_cnt' => 0
                );
            }else{
                $newarray[] = $sales_data_arr[$kk];
            }
        }elseif($sales_data_arr[$kk]['compare_sales'] == '02'){
            if($sales_data_arr[($kk-1)]['compare_sales'] != '01'){
                $newarray[] = array(
                    'compare_sales' => '01',
                    'code' => $sales_data_arr[$kk]['code'],
                    'name' => $sales_data_arr[$kk]['name'],
                    'regdate' => 0,
                    'order_sale_cnt01' => 0,
                    'order_sale_sum01' => 0,
                    'order_coprice_sum01' => 0.0000,
                    'order_sale_cnt02' => 0,
                    'order_sale_sum02' => 0,
                    'order_coprice_sum02' => 0.0000,
                    'sale_all_cnt01' => 0,
                    'sale_all_sum01' => 0,
                    'coprice_all_sum01' => 0.0000,
                    'sale_all_cnt02' => 0,
                    'sale_all_sum02' => 0,
                    'coprice_all_sum02' => 0.0000,
                    'cancel_sale_cnt01' => 0,
                    'cancel_sale_sum01' => 0,
                    'cancel_coprice_sum01' => 0.0000,
                    'cancel_sale_cnt02' => 0,
                    'cancel_sale_sum02' => 0,
                    'cancel_coprice_sum02' => 0.0000,
                    'return_sale_cnt01' => 0,
                    'return_sale_sum01' => 0,
                    'return_coprice_sum01' => 0.0000,
                    'return_sale_cnt02' => 0,
                    'return_sale_sum02' => 0,
                    'return_coprice_sum02' => 0.0000,
                    'amount_cnt' => 0
                );

                $newarray[] = $sales_data_arr[$kk];

            }else{
                $newarray[] = $sales_data_arr[$kk];
            }
        }
    }

    $arr_code = get_field_data($newarray, 'code');

    if(is_array($arr_code)){
        $check_empty = array_filter($arr_code);
    }

    if(! empty($check_empty)){
        $code_rowspan = array_count_values($arr_code);
    }

    $noDupl_code = "";
    $code_rowspan = array();
    $middle_order_sale_cnt = array();
    $middle_order_sale_sum = array();
    $middle_sale_all_cnt = array();
    $middle_sale_all_sum = array();
    $middle_cancel_sale_cnt = array();
    $middle_cancel_sale_sum = array();
    $middle_return_sale_cnt = array();
    $middle_return_sale_sum = array();
    $middle_real_sale_cnt_sum = array();
    $middle_real_sale_coprice_sum = array();
    $middle_sale_coprice_sum = array();
    $middle_margin_sum = array();
    for($i=0, $j=0;$i<count($newarray);$i++, $j++){

        if($noDupl_code != $newarray[$i]["code"]){

            $noDupl_code = $newarray[$i]["code"];
            $j = 0;
            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>";
            $mstring .= "
			<td class='list_box_td point' style='text-align:center;'   ".($code_rowspan[$newarray[$i]['code']] > 1 ? "rowspan=3":"").">";
            if($newarray[$i]["name"] == ""){
                $mstring .= "자사몰";
            }else{
                $mstring .= $newarray[$i]['name'];//." ".$newarray[$i]['code'];
            }
            $mstring .= "
			</td>";
        }

        if(( $newarray[$i]["compare_sales"] != '' && $code_rowspan[$newarray[$i]["code"]] > 1) || ($newarray[$i]["compare_sales"] == '' && $code_rowspan[$newarray[$i]["code"]] <= 2)
            || ($newarray[$i]["compare_sales"] != '' && $code_rowspan[$newarray[$i]["code"]] == 1)){
            $mstring .= "		
		<td class='list_box_td number' style='text-align:center;height:30px;' >";
            if($newarray[$i]["compare_sales"] == '01'){
                $mstring .= "매출액";
            }else if($newarray[$i]["compare_sales"] == '02'){
                $mstring .= "대비 매출";
            }else{
                $mstring .= "매출없음";
            }
            $mstring .= "</td>

		<td class='list_box_td number' >".number_format($newarray[$i]['sale_all_cnt'],0)."</td><!-- order_sale_cnt -->
		<td class='list_box_td number' >".number_format($newarray[$i]['sale_all_sum'],0)."</td><!-- order_sale_sum -->

		<td class='list_box_td number' >".number_format($newarray[$i]['sale_all_cnt'],0)."</td>
		<td class='list_box_td number' >".number_format($newarray[$i]['sale_all_sum'],0)."</td>

		<td class='list_box_td number' >".number_format($newarray[$i]['cancel_sale_cnt'],0)."</td>
		<td class='list_box_td number' >".number_format($newarray[$i]['cancel_sale_sum'],0)."</td>

		<td class='list_box_td number' >".number_format($newarray[$i]['return_sale_cnt'],0)."</td>
		<td class='list_box_td number' >".number_format($newarray[$i]['return_sale_sum'],0)."</td>";

            $real_sale_cnt = $newarray[$i]['sale_all_cnt']-$newarray[$i]['cancel_sale_cnt']-$newarray[$i]['return_sale_cnt'];

            $mstring .= "<td class='list_box_td number' >".number_format($real_sale_cnt,0)."</td>";

            $real_sale_coprice = $newarray[$i]['sale_all_sum']-$newarray[$i]['cancel_sale_sum']-$newarray[$i]['return_sale_sum'];
            $mstring .= "<td class='list_box_td number point' >".number_format($real_sale_coprice,0)."</td>";

            $sale_coprice = $newarray[$i]['coprice_all_sum']-$newarray[$i]['cancel_coprice_sum']-$newarray[$i]['return_coprice_sum'];
            //echo $newarray[$i]['coprice_all_sum']."-".$newarray[$i]['cancel_coprice_sum']."-".$newarray[$i]['return_coprice_sum']."<br>";
            $mstring .= "<!--td class='list_box_td number' >".number_format($sale_coprice,0)."</td>";

            $margin = $real_sale_coprice - $sale_coprice;
            $mstring .= "<td class='list_box_td number' >".number_format($margin,0)."</td>";
            if($real_sale_coprice > 0){
                $margin_rate = $margin/$real_sale_coprice*100;
            }else{
                $margin_rate = 0;
            }
            $mstring .= "<td class='list_box_td number' >".number_format($margin_rate,1)."</td-->";
            $mstring .= "
		</tr>\n";
        }

        if($j ==0){
            $order_sale_cnt = $order_sale_cnt + returnZeroValue($newarray[$i]['order_sale_cnt']);
            $order_sale_sum = $order_sale_sum + returnZeroValue($newarray[$i]['order_sale_sum']);

            $sale_all_cnt = $sale_all_cnt + returnZeroValue($newarray[$i]['sale_all_cnt']);
            $sale_all_sum = $sale_all_sum + returnZeroValue($newarray[$i]['sale_all_sum']);

            $cancel_sale_cnt = $cancel_sale_cnt + returnZeroValue($newarray[$i]['cancel_sale_cnt']);
            $cancel_sale_sum = $cancel_sale_sum + returnZeroValue($newarray[$i]['cancel_sale_sum']);

            $return_sale_cnt = $return_sale_cnt + returnZeroValue($newarray[$i]['return_sale_cnt']);
            $return_sale_sum = $return_sale_sum + returnZeroValue($newarray[$i]['return_sale_sum']);

            $real_sale_cnt_sum = $real_sale_cnt_sum + returnZeroValue($real_sale_cnt);
            $real_sale_coprice_sum = $real_sale_coprice_sum + returnZeroValue($real_sale_coprice);
            $sale_coprice_sum = $sale_coprice_sum + returnZeroValue($sale_coprice);
            $margin_sum = $margin_sum + returnZeroValue($margin);
        }



        $middle_order_sale_cnt[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($newarray[$i]['order_sale_cnt']);
        $middle_order_sale_sum[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($newarray[$i]['order_sale_sum']);

        $middle_sale_all_cnt[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($newarray[$i]['sale_all_cnt']);
        $middle_sale_all_sum[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($newarray[$i]['sale_all_sum']);

        $middle_cancel_sale_cnt[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($newarray[$i]['cancel_sale_cnt']);
        $middle_cancel_sale_sum[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($newarray[$i]['cancel_sale_sum']);

        $middle_return_sale_cnt[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($newarray[$i]['return_sale_cnt']);
        $middle_return_sale_sum[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($newarray[$i]['return_sale_sum']);

        $middle_real_sale_cnt_sum[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($real_sale_cnt);
        $middle_real_sale_coprice_sum[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($real_sale_coprice);
        $middle_sale_coprice_sum[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($sale_coprice);
        $middle_margin_sum[$newarray[$i]["code"]][$newarray[$i]["compare_sales"]] += returnZeroValue($margin);

        if($j == 1){

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i' ".($middle_order_sale_sum[$newarray[$i]["code"]]['01'] - $middle_order_sale_sum[$newarray[$i]["code"]]['02'] > 0 ? "style='color:blue' ":"style='color:red' ").">
								<td style='text-align:center;'>차액매출</td>
								<td class='list_box_td number'>".number_format($middle_order_sale_cnt[$newarray[$i]["code"]]['01'] - $middle_order_sale_cnt[$newarray[$i]["code"]]['02'])."</td>
								<td class='list_box_td number'>".number_format($middle_order_sale_sum[$newarray[$i]["code"]]['01'] - $middle_order_sale_sum[$newarray[$i]["code"]]['02'])."</td>
								<td class='list_box_td number'>".number_format($middle_sale_all_cnt[$newarray[$i]["code"]]['01'] - $middle_sale_all_cnt[$newarray[$i]["code"]]['02'])."</td>
								<td class='list_box_td number'>".number_format($middle_sale_all_sum[$newarray[$i]["code"]]['01'] - $middle_sale_all_sum[$newarray[$i]["code"]]['02'])."</td>
								<td class='list_box_td number'>".number_format($middle_cancel_sale_cnt[$newarray[$i]["code"]]['01'] - $middle_cancel_sale_cnt[$newarray[$i]["code"]]['02'])."</td>
								<td class='list_box_td number'>".number_format($middle_cancel_sale_sum[$newarray[$i]["code"]]['01'] - $middle_cancel_sale_sum[$newarray[$i]["code"]]['02'])."</td>

								<td class='list_box_td number'>".number_format($middle_return_sale_cnt[$newarray[$i]["code"]]['01'] - $middle_return_sale_cnt[$newarray[$i]["code"]]['02'])."</td>
								<td class='list_box_td number'>".number_format($middle_return_sale_sum[$newarray[$i]["code"]]['01'] - $middle_return_sale_sum[$newarray[$i]["code"]]['02'])."</td>

								<td class='list_box_td number'>".number_format($middle_real_sale_cnt_sum[$newarray[$i]["code"]]['01'] - $middle_real_sale_cnt_sum[$newarray[$i]["code"]]['02'])."</td>
								<td class='list_box_td number point'>".number_format($middle_real_sale_coprice_sum[$newarray[$i]["code"]]['01'] - $middle_real_sale_coprice_sum[$newarray[$i]["code"]]['02'])."</td>
								<!--td class='list_box_td number'>".number_format($middle_sale_coprice_sum[$newarray[$i]["code"]]['01'] - $middle_sale_coprice_sum[$newarray[$i]["code"]]['02'])."</td>
								<td class='list_box_td number'>".number_format($middle_sale_coprice_sum[$newarray[$i]["code"]]['01'] - $middle_sale_coprice_sum[$newarray[$i]["code"]]['02'])."</td>
								<td class='list_box_td number'>".number_format($middle_order_sale_cnt[$newarray[$i]["code"]]['01'] - $middle_order_sale_cnt[$newarray[$i]["code"]]['02'])."</td-->
								</tr>";

            $j = -1;

            unset($middle_order_sale_cnt);
            unset($middle_order_sale_sum);

            unset($middle_sale_all_cnt);
            unset($middle_sale_all_sum);

            unset($middle_cancel_sale_cnt);
            unset($middle_cancel_sale_sum);

            unset($middle_return_sale_cnt);
            unset($middle_return_sale_sum);

            unset($middle_real_sale_cnt_sum);
            unset($middle_real_sale_coprice_sum);
            unset($middle_sale_coprice_sum);
            unset($middle_margin_sum);

        }


    }

    if ($order_sale_sum == 0){
        $mstring .= "<tr  align=center height=200><td colspan=15 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
    }
    //$mstring .= "</table>\n";
    /*
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box' style='margin-top:5px;'>
                        <col width='3%'>
                        <col width='*'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>";
    */
    //$mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=2></td></tr>\n";
    if($real_sale_coprice_sum > 0){
        $margin_sum_rate = $margin_sum/$real_sale_coprice_sum*100;
    }else{
        $margin_sum_rate = 0;
    }

    $mstring .= "<tr height=25 align=right>
	<td class=s_td align=center colspan=2>합계(매출액)</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_sale_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_sale_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($return_sale_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($return_sale_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_coprice_sum,0)."</td>
	<!--td class='e_td number' style='padding-right:10px;'>".number_format($sale_coprice_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($margin_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($margin_sum_rate,1)."</td-->
	</tr>\n";
    $mstring .= "</table>\n";
    $mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함 (단위:원)  </td></tr></table>";

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


    $mstring .= HelpBox("제휴처별  매출 분석", $help_text);
    return $mstring;
}


$script = "<script language='javascript'>
function reloadView(){
	
		if($('#view_detail').attr('checked') == true || $('#view_detail').attr('checked') == 'checked'){		
			$.cookie('view_detail', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('view_detail', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
		
		document.location.reload();
	
	}

function reloadSaleView(){
	
	if($('#view_self_sale').attr('checked') == true || $('#view_self_sale').attr('checked') == 'checked'){		
		$.cookie('view_self_sale', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{		
		$.cookie('view_self_sale', '0', {expires:1,domain:document.domain, path:'/', secure:0});
	}
	
	document.location.reload();

}

</script>
";

if($mode == "excel"){
    if($report_type == 2){
        ReportTable2($vdate,$SelectReport);
    }else{
        ReportTable($vdate,$SelectReport);
    }
}else if ($mode == "iframe"){
//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'salesbyaffiliatedsite.php';

    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";
    if($report_type == 2){
        echo "<div id='report_view'>".ReportTable2($vdate,$SelectReport)."</div>";
    }else if($report_type == 3){
        echo "<div id='report_view'>".ReportTable3($vdate,$SelectReport)."</div>";
    }else{
        echo "<div id='report_view'>".ReportTable($vdate,$SelectReport)."</div>";
    }
    echo "<div id='calendar_view'>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</div>";
    echo "</body>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.getElementById('report_view').innerHTML;parent.unloading()</Script>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.getElementById('calendar_view').innerHTML;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";
    //echo "<Script>alert(1);</Script>";
    echo "</html>";

//	echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
//	echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";
}else if($mode == "pop" || $mode == "report" || $mode == "print"){
    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->Navigation = "이커머스분석 > 상품별 종합분석 > 제휴처별  매출 분석";

    $P->title = "제휴처별  매출 분석";
    //$P->strContents = ReportTable($vdate,$SelectReport);
    if($report_type == 2){
        $P->NaviTitle = "제휴처별  매출 분석 - 구매전환 분석";
        $P->strContents = ReportTable2($vdate,$SelectReport);
    }else if($report_type == 3){
        $P->NaviTitle = "제휴처별  매출 분석 - 매출요약 분석";
        $P->strContents = ReportTable3($vdate,$SelectReport);
    }else{
        $P->NaviTitle = "제휴처별  매출 분석 - 매출상세분석";
        $P->strContents = ReportTable($vdate,$SelectReport);
    }
    $P->OnloadFunction = "";
    //	$P->layout_display = false;
    echo $P->PrintLayOut();
}else{


    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('salesbyaffiliatedsite.php', "", " ");
    if($report_type == 2){
        $p->forbizContents = ReportTable2($vdate,$SelectReport);
    }else if($report_type == 3){
        $p->forbizContents = ReportTable3($vdate,$SelectReport);
    }else{
        $p->forbizContents = ReportTable($vdate,$SelectReport);
    }
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>\n$script ";
    //$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->Navigation = "이커머스분석 > 상품별 종합분석 > 제휴처별  매출 분석";
    $p->title = "제휴처별  매출 분석";
    $p->ContentsWidth = "98%";
    $p->PrintReportPage();

}


function ReportTable3($vdate,$SelectReport=1){

    global $depth,$cid, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status;
    global $search_sdate, $search_edate, $report_type, $price_type;
    $nview_cnt = 0;



    $order_sale_sum = 0;
    $real_sale_coprice_sum = 0;
    $order_sale_cnt = 0;
    $sale_all_cnt = 0;
    $sale_all_sum = 0;
    $real_sale_cnt_sum = 0;
    $real_sale_cnt_new = 0;
    $real_sale_sum_new = 0;
    $real_sale_cnt_reorder = 0;
    $real_sale_sum_reorder = 0;
    $real_sale_cnt_web = 0;
    $real_sale_sum_web = 0;
    $real_sale_cnt_mobile = 0;
    $real_sale_sum_mobile = 0;
    $order_cnt_web = 0;
    $order_cnt_mobile = 0;
    $sale_coprice_sum = 0;
    $margin_sum = 0;
    $sale_coprice = 0;
    $margin = 0;

    //$cid = $referer_id;
    if($SelectReport == ""){
        $SelectReport = 1;
    }

    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();

    if($depth == ""){
        $depth = 0;
    }else{
        $depth = $depth+1;
    }
    if($SelectReport == "4"){
        $vdate = $search_sdate;
        $vweekenddate = $search_edate;
    }

    $params = explode('&', $_SERVER['QUERY_STRING']);

    foreach ($params as $param) {
        $name_value = explode('=', $param);
        $name = $name_value[0];
        $value = $name_value[1];

        if($name == 'order_from%5B%5D'){
            $order_from[] = $value;
        }elseif($name == 'edate' || $name == 'sdate'){
            $name = $value;
        }
    }

    if(empty($_GET['sdate']) || empty($_GET['edate'])){
        $edate = date("Y-m-d", time());
        $sdate = date('Y-m-d', strtotime('-1 month'));
        $vdate = date("Y-m-d", time());
        $vyesterday = date("Y-m-d", time()-84600);
        $v15ago = date("Y-m-d", time()-84600*15);
        $vonemonthago = date("Y-m-d", time()-84600*30);
        $v2monthago = date("Y-m-d", time()-84600*60);
        $v3monthago = date("Y-m-d", time()-84600*90);
        $voneweekago = date("Y-m-d", time()-84600*7);
        $search_sdate = date("Y-m-d", time()-84600*7);
        $search_edate = date("Y-m-d", time());
    }else{
        $sdate = $_GET['sdate'];
        $edate = $_GET['edate'];
        $vdate = $edate;
        $vweekenddate = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24*7);
        $v15ago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24*15);
        $vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24*30);
        $v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24*60);
        $v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2),substr($vdate,8,2),substr($vdate,0,4))-60*60*24*90);
    }

    if(isset($_GET["orderby"]) && $_GET["orderby"] != "" && isset($_GET["ordertype"]) && $_GET["ordertype"] != ""){
        $orderbyString = " order by ".$_GET["orderby"]." ".$_GET["ordertype"].", ptprice desc ";
    }else{
        $orderbyString = " order by ptprice desc  ";
    }

    $coprice_str = "case when (od.commission > 0 and od.coprice = 0) then od.psprice-(od.psprice*od.commission/100) else od.coprice end ";

    $where = " ";

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

    if($SelectReport == "4"){
        $vdate = $search_sdate;
        $vweekenddate = $search_edate;
    }

    $dateString = "구매 기간 : ".$sdate." - ".$edate."<br>";


    $sql = "Select IFNULL(ssi.site_code,'0') as code,  site_name  as name, 
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then 1 else 0 end) as order_cnt, 
				sum(case when (o.payment_agent_type = 'W' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_web, 
				sum(case when (o.payment_agent_type = 'M' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_mobile, 
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
				
				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

				sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
				sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
				sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

				(
					sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) - 
					sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) - 
					sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
				) as real_sale_cnt , 

				(
					sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) - 
					sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) - 
					sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end)
				) as real_sale_sum , 

				(
					sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
					sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
					sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
				) as real_sale_cnt_new , 

				(
					sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
					sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
					sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
				) as real_sale_sum_new , 

				(
					sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
					sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
					sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
				) as real_sale_cnt_reorder , 

				(
					sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
					sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
					sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
				) as real_sale_sum_reorder , 

				(
					sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
					sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
					sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
				) as real_sale_cnt_web , 

				(
					sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
					sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
					sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
				) as real_sale_sum_web , 

				(
					sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
					sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
					sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
				) as real_sale_cnt_mobile , 

				(
					sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
					sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
					sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
				) as real_sale_sum_mobile , 

				sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum
				from  shop_order_detail od join shop_order o on od.oid = o.oid 
				left join sellertool_site_info ssi on od.order_from = ssi.site_code
				where od.regdate  between '".$sdate." 00:00:00' and '".$edate." 23:59:59' 
				and od.status NOT IN ('".implode("','",$non_sale_status)."') 
				
				and substr(od.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."'".$where;
    $sql .= "group by ssi.site_code ";
    $sql .= $orderbyString;


    if($sql){
        $fordb->query($sql);
    }


    $mstring = "";
    $mstring .="
	<script type='text/javascript'>
	<!--
		function setSelectDate(FromDate,ToDate,dType) {
			var frm = document.searchmember;
			if(dType == 2){
				$(\"#memreg_sdate\").val(FromDate);
				$(\"#memreg_edate\").val(ToDate);
			}else{
				$(\"#start_datepicker\").val(FromDate);
				$(\"#end_datepicker\").val(ToDate);
			}
		}

	//-->
	</script>
	<table cellpadding=0 cellspacing=0 width=100%>
	<tr height=150>
		<td   >
			<form name='search_form' method='get' action='' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='groupbytype' value='$groupbytype'>
			<input type='hidden' name='SubID' value='$SubID' />
			<input type='hidden' name='eprice' value='1000000' />
			<input type='hidden' name='price_type' value='".$_GET['price_type']."' />
			<table class='box_shadow' style='width:1100px;margin-left: 10%;' cellpadding='0' cellspacing='0' border='0' ><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:1px'>
						<table cellpadding=0 cellspacing=1 border=0 width=70% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>";
    $mstring .= "
							<tr height=27>
							  <td class='search_box_title'><b>구매일자</b></td>
							  <td class='search_box_item' colspan=3 >
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=20>
									<col width=70>
									<col width=*>
									<tr>
										<td>
										".Log_search_date('sdate','edate',$sdate,$edate,'N')."
										</td>
									</tr>
								</table>
							  </td>
							</tr>
							";
    $mstring .="<tr>
								<td class='search_box_title'><b>제휴처</b></td>
								<td class='search_box_item' colspan=3 >
									<table cellpadding=0 cellspacing=0 width='100%' border='0' >
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<col width='12.5%'>
										<TR height=25>
								";

    $fordb2->query("select * from sellertool_site_info where disp='1' order by vieworder");
    $sell_order_from=$fordb2->fetchall();
    if(count($sell_order_from) > 0){

        for($i=0;$i<count($sell_order_from);$i++){

            if($i==5 || ($i > 5 && $i%8==5)) $mstring .= "</TR><TR>";

            $mstring .= "<TD><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i]['site_code']."' value='".$sell_order_from[$i]['site_code']."' ".CompareReturnValue($sell_order_from[$i]['site_code'],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i]['site_code']."'>".$sell_order_from[$i]['site_name']."</label></TD>";

        }
    }

    $mstring .="
									</TR>
								</table>
								</td>
							</tr>";
    $mstring .=	"
						</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
				<tr >
					<td colspan=2 align=center style='padding:10px 0px'><input type=image src='../../images/".$_SESSION["admininfo"]["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	</table>";


    $mstring .= "<table width='100%' border=0>";
    if(isset($_GET["mode"]) && ($_GET["mode"] == "pop" || $_GET["mode"] == "print")){
        $mstring .= "<tr><td >".TitleBar("제휴처별  매출 분석 : ",$dateString,false, $exce_down_str)."</td></tr>";
    }else{
        $mstring .= "<tr><td >".TitleBar("제휴처별  매출 분석 : ",$dateString,false, $exce_down_str)."</td></tr>";
    }
    $mstring .= " 
						<tr height=50>
							<td >
								<div class='tab'>
									<table class='s_org_tab'>
									<tr>
										<td class='tab'> 
											<table id='tab_01'  ".(($report_type == '3') ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?SubID=".$_GET["SubID"]."&report_type=3'\">매출요약 분석</td>
												<th class='box_03'></th>
											</tr>
											</table> 
											<table id='tab_02'  ".($report_type == '1' && $price_type == '1' ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?SubID=".$_GET["SubID"]."&report_type=1&price_type=1'\">매출상세 분석(주문일자)</td>
												<th class='box_03'></th>
											</tr>
											</table> 
											<table id='tab_03' ".($report_type == '1' && $price_type == '2' ? "class=on":"")." >
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?SubID=".$_GET["SubID"]."&report_type=1&price_type=2'\">매출상세 분석(입금확인)</td>
												<th class='box_03'></th>
											</tr>
											</table> 
										</td>
									</tr>
									</table>
								</div>
							</td>
						  </tr>
					</table>";
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>
						<col width='3%'>
						<col width='*'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>";
    $mstring .= "
		<tr height=30>
			<td class=s_td rowspan=3>순</td>
			<td class=m_td rowspan=3>".OrderByLink("제휴처명", "code", $ordertype)."</td>
			<td class=m_td colspan=2>주문매출</td>
			<td class=m_td colspan=2>매출</td>
			<td class=m_td colspan=4>신규회원가입대비<br>매출</td>
			<td class=m_td colspan=4>에이전트별<br>매출</td>
			<td class=m_td rowspan=2 colspan=2>건별매출</td>
		</tr>
		<tr height=30>
			<td class='m_td small' colspan=2 style='line-height:140%;'><b>전체주문</b><br>(입금예정포함)</td>
			<!--td class=m_td colspan=2>전체매출액(전체)</td-->
			
			<td class=m_td colspan=2>실매출액(+)</td>
			<td class=m_td colspan=2>신규매출</td>
			<td class=m_td colspan=2>재구매매출</td>
			<td class=m_td colspan=2>웹매출</td>
			<td class=m_td colspan=2>모바일매출</td>
		</tr>
		<tr height=30 align=center>			
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<!--td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td-->
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td >웹</td>
			<td class=m_td >모바일</td>
			</tr>\n";
    /*

    sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
                    sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum,
                    sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
                    sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum,
                    sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
                    sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum,

                    */

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['name'];
        $mstring .= "</td>
		

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['order_sale_cnt'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['order_sale_sum'],0)."&nbsp;</td>

		<!--td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sale_all_cnt'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sale_all_sum'],0)."&nbsp;</td-->

		";

        $real_sale_cnt = $fordb->dt['sale_all_cnt']-$fordb->dt['cancel_sale_cnt']-$fordb->dt['return_sale_cnt'];
        $real_sale_coprice = $fordb->dt['sale_all_sum']-$fordb->dt['cancel_sale_sum']-$fordb->dt['return_sale_sum'];

        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_cnt,0)."&nbsp;</td>";


        $mstring .= "<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_coprice,0)."&nbsp;</td>";
        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_cnt_new'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_sum_new'],0)."&nbsp;</td>

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_cnt_reorder'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_sum_reorder'],0)."&nbsp;</td>";

        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_cnt_web'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_sum_web'],0)."&nbsp;</td>

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_cnt_mobile'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_sum_mobile'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt['order_cnt_web'] > 0 ? $fordb->dt['real_sale_sum_web']/$fordb->dt['order_cnt_web']:0),0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt['order_cnt_mobile'] > 0 ? $fordb->dt['real_sale_sum_mobile']/$fordb->dt['order_cnt_mobile']:0),0)."&nbsp;</td>
		";

        /*
        $sale_coprice = $fordb->dt['coprice_all_sum']-$fordb->dt['cancel_coprice_sum']-$fordb->dt['return_coprice_sum'];
        //echo $fordb->dt['coprice_all_sum']."-".$fordb->dt['cancel_coprice_sum']."-".$fordb->dt['return_coprice_sum']."<br>";
        $mstring .= "<td class='list_box_td number blue_point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($sale_coprice,0)."&nbsp;</td>";

        $margin = $real_sale_coprice - $sale_coprice;
        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin,0)."&nbsp;</td>";
        if($real_sale_coprice > 0){
            $margin_rate = $margin/$real_sale_coprice*100;
        }else{
            $margin_rate = 0;
        }
        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin_rate,1)."&nbsp;</td>";
        */



        $mstring .= "</tr>\n";

        $order_sale_cnt = $order_sale_cnt + returnZeroValue($fordb->dt['order_sale_cnt']);
        $order_cnt_web = $order_cnt_web + returnZeroValue($fordb->dt['order_cnt_web']);
        $order_cnt_mobile = $order_cnt_mobile + returnZeroValue($fordb->dt['order_cnt_mobile']);

        $order_sale_sum = $order_sale_sum + returnZeroValue($fordb->dt['order_sale_sum']);

        $sale_all_cnt = $sale_all_cnt + returnZeroValue($fordb->dt['sale_all_cnt']);
        $sale_all_sum = $sale_all_sum + returnZeroValue($fordb->dt['sale_all_sum']);

        $real_sale_cnt_new = $real_sale_cnt_new + returnZeroValue($fordb->dt['real_sale_cnt_new']);
        $real_sale_sum_new = $real_sale_sum_new + returnZeroValue($fordb->dt['real_sale_sum_new']);

        $real_sale_cnt_reorder = $real_sale_cnt_reorder + returnZeroValue($fordb->dt['real_sale_cnt_reorder']);
        $real_sale_sum_reorder = $real_sale_sum_reorder + returnZeroValue($fordb->dt['real_sale_sum_reorder']);


        $real_sale_cnt_web = $real_sale_cnt_web + returnZeroValue($fordb->dt['real_sale_cnt_web']);
        $real_sale_cnt_mobile = $real_sale_cnt_mobile + returnZeroValue($fordb->dt['real_sale_cnt_mobile']);

        $real_sale_sum_web = $real_sale_sum_web + returnZeroValue($fordb->dt['real_sale_sum_web']);
        $real_sale_sum_mobile = $real_sale_sum_mobile + returnZeroValue($fordb->dt['real_sale_sum_mobile']);





        $real_sale_cnt_sum = $real_sale_cnt_sum + returnZeroValue($real_sale_cnt);
        $real_sale_coprice_sum = $real_sale_coprice_sum + returnZeroValue($real_sale_coprice);
        $sale_coprice_sum = $sale_coprice_sum + returnZeroValue($sale_coprice);
        $margin_sum = $margin_sum + returnZeroValue($margin);


    }

    if ($order_sale_sum == 0){
        $mstring .= "<tr  align=center height=200><td colspan=16 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
    }
    //$mstring .= "</table>\n";
    /*
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box' style='margin-top:5px;'>
                        <col width='3%'>
                        <col width='*'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>";
    */
    //$mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=2></td></tr>\n";
    if($real_sale_coprice_sum > 0){
        $margin_sum_rate = $margin_sum/$real_sale_coprice_sum*100;
    }else{
        $margin_sum_rate = 0;
    }

    $mstring .= "<tr height=25 align=right>
	<td class=s_td align=center colspan=2>합계</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_sum,0)."</td>
	<!--td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_sum,0)."</td-->
	 
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_coprice_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_new,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_new,0)."</td>	
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_reorder,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_reorder,0)."</td>

	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_web,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_web,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_mobile,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_mobile,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format(($order_cnt_web > 0 ? $real_sale_sum_web/$order_cnt_web:0),0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format(($order_cnt_mobile > 0 ? $real_sale_sum_mobile/$order_cnt_mobile:0),0)."</td>
	</tr>\n";
    $mstring .= "</table>\n";
    $mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함 (단위:원)  </td></tr></table>";

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


    $mstring .= HelpBox("제휴처별  매출 분석", $help_text);
    return $mstring;
}


function get_field_data($array, $field, $idField = null) {
    $_out = array();

    if (is_array($array)) {
        if ($idField == null) {
            foreach ($array as $value) {
                foreach ($value as $key => $val) {
                    if($field == $key){
                        $_out[] = $value[$field];
                    }
                }
            }
        }
        else {
            foreach ($array as $value) {
                $_out[$value[$idField]] = $value[$field];
            }
        }
        return $_out;
    }
    else {
        return false;
    }
}

