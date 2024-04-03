<?php
include_once("../class/layout.class");
include_once("../sellertool/sellertool.lib.php");
include("../campaign/mail.config.php");
include("../order/orders.lib.php");

if($excel_searialize_value){
   //  echo urldecode(unserialize($excel_searialize_value));
    $ex_get =  unserialize(urldecode($excel_searialize_value));

    if(isset($ex_get['mode'])) $mode = $ex_get['mode'];
    if(isset($ex_get['startDate'])) $startDate = $ex_get['startDate'];
    if(isset($ex_get['endDate'])) $endDate = $ex_get['endDate'];

    $_GET['order_from'] = $ex_get['order_from'];
}

if($startDate == ""){
    $vdate = date("Y-m-d", time());
    $startDate = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-1,substr($vdate,8,2)+1,substr($vdate,0,4)));
    $endDate = date("Y-m-d");
}


if(!$title_str){
    $title_str  = "매출액(일자별)";
}

if($max == ""){
    $max = 100; //페이지당 갯수
}

if($page == ''){
    $start = 0;
    $page  = 1;
}else{
    $start = ($page - 1) * $max;
}

$whereIn = "";

if(count($_GET['order_from']) > 0){
    if(in_array('self', $_GET['order_from'])){
        $sellerList['self']['site_name'] = '자사몰';
    }
    $whereIn = "'".implode("','", $_GET['order_from'])."'";
    $slave_db->query("select site_name, site_code from sellertool_site_info where site_code in (".$whereIn.")");
    $temp = $slave_db->fetchall();
    if(count($temp) > 0){
        foreach($temp as $p=>$v){
            $sellerList[$v['site_code']]['site_name'] = $v['site_name'];
        }
    }
}

if($whereIn != ""){
    $qryAddWhere = " AND t2.order_from in (".$whereIn.")";
}

$qryOrderInfo ="
SELECT 
	  DATE_FORMAT(t2.di_date,'%Y-%m-%d') as di_date,
	  t2.order_from,
	  max(DATE_FORMAT(t2.di_date,'%w')) AS yoil,  
	  sum(t2.ptprice) AS ptprice
  FROM shop_order t1 
  INNER JOIN shop_order_detail t2
  ON t1.oid = t2.oid
 WHERE DATE_FORMAT(t2.di_date,'%Y-%m-%d') >= '".$startDate."' 
   AND DATE_FORMAT(t2.di_date,'%Y-%m-%d') <= '".$endDate."' 
   AND t2.product_type != '77'
   AND t2.status  NOT IN ('IR', 'SR', 'IB')
   $qryAddWhere
   GROUP BY DATE_FORMAT(t2.di_date,'%Y-%m-%d'), t2.order_from   
 ORDER BY t2.di_date, t2.order_from ASC
" ;


$slave_db->query($qryOrderInfo);
$orderInfo = $slave_db->fetchall();
$orderCnt = count($orderInfo);

if($startDate == ""){
    $startDate = date("Y-m-d");
}
if($endDate == ""){
    $endDate = date("Y-m-d");
}



$orderList = array();
$temp_date = date("Y-m-d", strtotime("-1 day", strtotime($startDate)));
while(true) {
    $temp_date = date("Y-m-d", strtotime("+1 day", strtotime($temp_date)));
    $yoil = date('w', strtotime($temp_date));
    $orderList[$temp_date] = array();

    switch($yoil){
        case 0:
            $orderList[$temp_date]['yoil'] = '일';
            break;
        case 1:
            $orderList[$temp_date]['yoil'] = '월';
            break;
        case 2:
            $orderList[$temp_date]['yoil'] = '화';
            break;
        case 3:
            $orderList[$temp_date]['yoil'] = '수';
            break;
        case 4:
            $orderList[$temp_date]['yoil'] = '목';
            break;
        case 5:
            $orderList[$temp_date]['yoil'] = '금';
            break;
        case 6:
            $orderList[$temp_date]['yoil'] = '토';
            break;
    }

    $orderList[$temp_date]['sites'] = $sellerList;
    $orderList[$temp_date]['date_total'] = 0;

    if($temp_date == $endDate){
        break;
    }
}


if (count($orderInfo) > 0 && count($sellerList) > 0)


    $orderList['total']['total_amount'] = 0;



    foreach($sellerList as $k=>$v){
        $orderList['total']['sites'][$k] = 0;
    }

    foreach($orderInfo as $p=>$v){
        $orderList[$v['di_date']]['sites'][$v['order_from']]['amount'] = intval($v['ptprice']); // 날짜별 해당 업체 총액
        $orderList[$v['di_date']]['date_total'] += intval($v['ptprice']); // 날짜별 총액
        $orderList['total']['sites'][$v['order_from']] = intval($orderList['total']['sites'][$v['order_from']]) + intval($v['ptprice']);   // 업체별 총액
        $orderList['total']['total_amount'] += intval($v['ptprice']);     // 전체 총액
    }
}

/*
echo "<xmp>";
var_dump($orderList);
echo "</xmp>";exit;*/


$Contents = "  
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "매출액(일자별) > $title_str ")."</td>
	</tr>
</table>


<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<form name='search_frm' method='get' action=''>
			<input type='hidden' name='mmode' value='$mmode' />
			<input type='hidden' name='mem_ix' value='$mem_ix' />
			<input type='hidden' name='mode' value='search' />
			<table width=100%  border=0>			
				<tr>
					<td align='left' colspan=2  width='100%' valign=top style='padding-top:5px;'>
					<table height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0  class='search_table_box'>
                    <tr height=30 style='border-color: silver;'>
                        <th style='width:151px; padding-left: 15px; background: #efefef; text-align: center; height: 30px; font-weight: bold; color: #000000;' colspan='2'>거래처 선택 <input type='checkbox' id='ckSellers' onclick='linecheck();' /></th>
                        <td class='search_box_item' nowrap colspan='3'>
                        
                            <table cellpadding=0 cellspacing=0 width='100%' border='0' >
                                <col width='12.5%'>
                                <col width='12.5%'>
                                <col width='12.5%'>
                                <col width='12.5%'>
                                <col width='12.5%'>
                                <col width='12.5%'>
                                <col width='12.5%'>
                                <col width='12.5%'>
                                <tr height='25'>";

                    $Contents .= "<td><input type='checkbox' name='order_from[]' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자사몰</label></td>";

                    $slave_db->query("select * from sellertool_site_info where disp='1' ");
                    $sell_order_from = $slave_db->fetchall();
                    if(count($sell_order_from) > 0){
                        for($i = 0; $i < count($sell_order_from); $i++){
                            if($i == 5 || ($i > 5 && $i%8 == 5)){
                                $Contents .= "</tr><tr>";
                            }
                            $Contents .= "<td><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></td>";
                        }
                        if(count($sell_order_from) < 5){
                            for($j = 0; $j < 5 - count($sell_order_from); $j++){
                                $Contents .= "<td></td>";
                            }
                        }
                    }else{
                        $Contents .= "
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>";
                    }

$Contents .= "
                                    </tr>
                                </table>
                            </td>
                        </tr>
                         <tr>
							<td style='width:151px; padding-left: 15px; background: #efefef; text-align: center; height: 30px; font-weight: bold; color: #000000;' colspan='2'>주문 일자</td>
							<td colspan='3'>
							    <table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
                                <tr style='border-color: silver;'>
                                    <td class='search_box_item'>
                                        ".search_date('startDate','endDate',$startDate,$endDate)."
                                    </td>
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
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;position:relative;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr>	
</table>
</form>


<div style='padding: 8px; text-align: right;'><input type='image' src='../images/".$admininfo["language"]."/btn_excel_save.gif' id='btn_excel_dn' border=0></div>



<!--a name='list_top'></a-->
<form name=listform method=post action='../order/orders.batch.act.php' onsubmit='return BatchSubmit(this)' target='act'>
<input type='hidden' name='mmode' value='$mmode' />
<input type='hidden' name='mem_ix' value='$mem_ix' />
<input type='hidden' name='page' value='$page' />
<input type='hidden' id='oid' value='' />
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."' />



<table width='100%' border='1' cellpadding='3' cellspacing='0' align='center'  item='this_board'>
	<tr>";


//view main content 기준
$view_data = "<table width='100%' border='1' cellpadding='3' cellspacing='0' align='center'  class='data_table' style='display: border-collapse: separate; border-spacing: 1px; background: #c5c5c5; border: 1px;'>
            <thead>
                <tr style='border-color: silver;'>
                    <th style='background: #efefef;'>날짜</th>
                    <th style='background: #efefef;'>요일</th>
                    <th style='background: #efefef;'>일별 합계</th>";
if(count($sellerList) > 0){
    foreach ($sellerList as $p=>$v) {
        $view_data .= "<th style='background: #efefef;'>".$v['site_name']."</th>";
    }
}

$view_data .= "
                </tr>         
            </thead>
            <tbody align='center'>";

// loop
$tr = "";

if($orderCnt > 0 && count($_GET['order_from']) > 0) {

    foreach ($orderList as $k => $v) {

        if($k == 'total'){
            continue;
        }

        $tr .= "<tr style='border-color: silver;'>
                <td style='background: #fff;' align='center'>" . $k . "</td>
                <td style='background: #fff;' align='center'>" . $v['yoil'] . "</td>
                <td style='background: #fff;' align='right'>" . number_format($v['date_total']) . "</td>";

            foreach($v['sites'] as $k2 =>$v2){
                $tr .= "<td style='background: #fff;text-align:right;'>" . number_format($v2['amount']) . "</td>";
            }

        $tr .= "</tr>";
    }


    $tr .= "<tr style='border-color: silver;'>
                <th style='background: #efefef;' colspan='2'>비율</th>
                <td style='background: #efefef;text-align: right'></td>";

        foreach ($orderList['total']['sites'] as $k => $v) {

            $trRatio = ($v / $orderList['total']['total_amount']) * 100;

            $tr .= "<td style='background: #efefef; text-align: right'>" . round($trRatio,2) . "</td>";
        }
    $tr .= "</tr>";



    $tr .= "<tr style='border-color: silver;'>
                <th style='background: #efefef;' colspan='2'>누적 합계</th>
                <td style='background: #efefef;text-align: right'>".number_format($orderList['total']['total_amount'])."</td>";

    foreach ($orderList['total']['sites'] as $k => $v) {
        $tr .= "
                        <td style='background: #efefef; text-align: right'>" . number_format($v) . "</td>
                    ";
    }
    $tr .= "</tr>";











}else{
    $tr .= "<tr style='border-color: silver;'><td colspan='".(3 + count($sellerList))."' style='background: #fff;'>검색 조건에 따른 주문 내역이 없습니다. </td></tr>";
}
// end loop






$view_data .= $tr;
$view_data .= " </tbody>        
        </table>"; //end table data




if($_GET['excel'] == "act") {
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . iconv("UTF-8", "CP949", "자사몰 일단위 매출") . '_' . date("Ymd") . '.xls"');
    header('Cache-Control: max-age=0');

    echo $view_data; exit;
}

$Contents .= $view_data;

if($admininfo[admin_level] != 9){
    $Contents .= "<span style='color:red'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
}

$Contents .= "
		</td>
	</tr>
  </table>
</table>";



$js = "
<script type='text/javascript'>
<!--
$(window).on('load', function(){
    $('#btn_excel_dn').click(function (){
        var sdate = $('input[name=startDate]').val();
        var edate = $('input[name=endDate]').val();
        var action = $('#form_excels').attr('action');
        var url = $('input[name=search_searialize_value]').val();
           
        $('input[name=excel_searialize_value]').val(url);
        $('input[name=excel_sdate]').val(sdate);
        $('input[name=excel_edate]').val(edate);
        $('input[name=excel]').val('act');
        $('#form_excels').submit();
    });
});

function linecheck(){
    var ck = $('#ckSellers').prop('checked');
    if(ck){
        $('[name^=order_from]').prop('checked',true);
    }else{
       $('[name^=order_from]').prop('checked',false);
    }
}
-->
</script>

<form id='form_excels' action='./order_statistics2.php' method='get'>
    <input type='hidden' name='excel_searialize_value' value=''>
    <input type='hidden' name='excel_sdate' value=''>
    <input type='hidden' name='excel_edate' value=''>
    <input type='hidden' name='excel' value=''>
</form>
<style>
.data_table th {padding: 10px 10px;}
.data_table td {padding: 10px 10px;}
.of_chk{margin-left: 10%;}
.of_default{margin-left: 10px;}
</style>";

$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value='' />
</form>";


if($mmode == "personalization"){
    $P = new ManagePopLayOut();
    $P->addScript = $js;
    $P->strLeftMenu = order_menu();
    $P->Navigation = "주문관리 > 주문리스트";
    $P->title = "주문리스트";
    $P->NaviTitle = "주문리스트";
    $P->strContents = $Contents;
    $P->layout_display = false;
    $P->view_type = "personalization";

    echo $P->PrintLayOut();

}else{

    $P = new LayOut();
    if($view_type == "sellertool"){
        $P->strLeftMenu = sellertool_menu();
    }else if($view_type == "offline_order"){
        $P->strLeftMenu = offline_order_menu();
    }else if($view_type == "pos_order"){
        $P->strLeftMenu = pos_order_menu();
    }else if($view_type == "sc_order"){
        $P->strLeftMenu = sns_menu();
    }else if($view_type == "inventory"){
        $P->strLeftMenu = inventory_menu();
    }else{
        $P->strLeftMenu = order_menu();
    }
    $P->addScript = $js;
    $P->Navigation = "주문관리 > 매출종합 분석 > 거래처별 매출(일별) ";
    $P->title = "거래처별 매출(일별)";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}


function make_time_arr($non_table = array())
{
    $arr = array();
    for($i = 0; $i<=23; $i++ ){
        $arr[$i] = "";
    }
    return  $arr;
}

?>