<?php
include_once("../class/layout.class");
include_once("../sellertool/sellertool.lib.php");
include("../campaign/mail.config.php");
include("../order/orders.lib.php");

if($excel_start_month != ""){
    $start_date = $excel_start_month;
}

if($start_date == ""){
    $start_date = date("Y-m-d");
    $startDate = substr($start_date,0,7);
}else{
    $startDate = substr($start_date,0,7);
}

if(!$title_str){
    $title_str  = "자사몰 판매수량 ".$startDate."(월)";
}

// 자사몰 월별 상품 판매 수량
$qryOrderInfo ="
SELECT t2.pid, 
        t2.pname, 
        SUM(t2.pcnt) AS pcnt, 
        SUM(t2.ptprice) AS ptAmount
FROM shop_order t1
INNER JOIN shop_order_detail t2 ON t1.oid = t2.oid
INNER JOIN shop_product t3 ON t2.pid = t3.id
WHERE DATE_FORMAT(t2.di_date,'%Y-%m') = '$startDate' 
  AND t2.product_type != '77' 
  AND t2.status NOT IN ('IR', 'SR', 'IB') 
  AND t2.order_from = 'self'
GROUP BY t2.pid
ORDER BY t2.pname ASC";
$slave_db->query($qryOrderInfo);

$orderInfo = $slave_db->fetchall();
$orderCnt = count($orderInfo);

$orderList = array();

if (count($orderInfo) > 0){
    $orderList = $orderInfo;
}



$Contents = "  
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "상품별 판매 수량  > $title_str ")."</td>
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
					<td align='left'  width='100%' valign=top style='padding-top:5px;'>
					<table style='width:100%;' align=center border=0  class='search_table_box'>
                        <tr>
							<td style='width:251px; padding-left: 15px; background: #efefef; text-align: center; height: 30px; font-weight: bold; color: #000000;'>주문 월 선택</td>
							<td>
							    <table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
                                <tr style='border-color: silver;'>
                                    <td class='search_box_item'>
                                        <table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff style='float:left;'>
                                            <tr>
                                                <td>
                                                    <img src='../images/".$admininfo["language"]."/calendar_icon.gif'>
                                                </td>
                                                <td nowrap>
                                                    <input type='text' name='start_date' class='textbox point_color' value='".$start_date."' style='height:20px;width:70px;text-align:center;' id='start_date' ".$property."> 
                                                    ".$start_time_select."
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


<table width='100%' border='1' cellpadding='3' cellspacing='0' align='center'  item='this_board'>
	<tr>";

//view main content 기준
$view_data = "<table width='100%' border='1' cellpadding='3' cellspacing='0' align='center'  class='data_table' style='display: border-collapse: separate; border-spacing: 1px; background: #c5c5c5; border: 1px;'>
            <thead>
                <tr style='border-color: silver;'>
                    <th style='background: #efefef;'>상품명</th>
                    <th style='background: #efefef;'>판매수량</th>
                    <th style='background: #efefef;'>판매금액</th>
                </tr>         
            </thead>
            <tbody align='center'>";

$tr = "";

if($orderCnt > 0) {

    $totalPcnt = 0;
    $totalPamount = 0;
    foreach($orderList as $p=>$v) {

        $totalPcnt += $v['pcnt'];
        $totalPamount += $v['ptAmount'];

        $tr .= "<tr style='border-color: silver;'>
                <th style='background: #f4f4f4;text-align: left;width: 50%'>".$v['pname']."</th>
                <td style='background: #fff;text-align: right'>" . number_format($v['pcnt']) . "</td>
                <td style='background: #fff;text-align: right'>" . number_format($v['ptAmount']) . "</td>";
        $tr .= "</tr>";
    }

    $tr .= "<tr style='border-color: silver;'>
                <th style='background: #efefef;text-align: center;width: 50%'>합 계</th>
                <td style='background: #efefef;text-align: right'>" . number_format($totalPcnt) . "</td>
                <td style='background: #efefef;text-align: right'>" . number_format($totalPamount) . "</td>";
    $tr .= "</tr>";

}else{
    $tr .= "<tr style='border-color: silver;'><td colspan='3' style='background: #fff;'>검색 조건에 따른 주문 내역이 없습니다. </td></tr>";
}
// end loop






$view_data .= $tr;
$view_data .= " </tbody>        
        </table>"; //end table data




if($_GET['excel'] == "act") {
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . iconv("UTF-8", "CP949", "자사몰 월매출") . '_' . date("Ymd") . '.xls"');
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
        
        var sdate = $('input[name=start_date]').val();
        var action = $('#form_excels').attr('action');
           sdate = sdate.substring(0,7);

        $('input[name=excel_start_month]').val(sdate);
        $('input[name=excel]').val('act');
        $('#form_excels').submit();
    });
});
-->
</script>




<form id='form_excels' action='./order_statistics3.php' method='get'>
    <input type='hidden' name='excel_start_month' value=''>
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


$Contents .= "
<script type='text/javascript'>
<!--
function start_date(FromDate,ToDate,dType) {
    if($('#start_date').attr('disabled') == 'disabled'){
        alert('비활성화 상태에서는 날짜 선택이 불가합니다.');
    }else{
        var frm = document.search_frm;
        $('#start_date').val(FromDate);
    }
}

$(document).ready(function (){
    
    $('#start_date').datepicker({
        monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
        dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
        showMonthAfterYear:true,
        dateFormat: 'yy-mm-dd',
        buttonImageOnly: true,
        buttonText: '달력'
    });
    /*
    $('#start_date').on( 'change', function() {
      var temp = $('#start_date').val();
      $('#start_date').val(temp.substring(0,7));
    });
    */
});
//-->
</script>";



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
    $P->Navigation = "주문관리 > 매출종합 분석 > 자사몰 판매수량 ".$startDate."월";
    $P->title = "자사몰 판매수량 ".$startDate."월";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}


?>