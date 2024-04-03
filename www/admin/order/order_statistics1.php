<?php
include_once("../class/layout.class");
include_once("../sellertool/sellertool.lib.php");
include("../campaign/mail.config.php");
include("../order/orders.lib.php");

if(isset($_GET['excel_sdate'])) $startDate = $_GET['excel_sdate'];
if(isset($_GET['excel_edate'])) $endDate = $_GET['excel_edate'];


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

$qryOrderInfo ="
SELECT 
      t1.oid,
      t1.user_code,
	  DATE_FORMAT(t2.di_date,'%Y-%m-%d') as di_date,
	  DATE_FORMAT(t2.di_date,'%w') AS yoil,  
	  t2.ptprice
  FROM shop_order t1 
  INNER JOIN shop_order_detail t2
  ON t1.oid = t2.oid
 WHERE DATE_FORMAT(t2.di_date,'%Y-%m-%d') >= '".$startDate."' 
   AND DATE_FORMAT(t2.di_date,'%Y-%m-%d') <= '".$endDate."' 
   AND t2.product_type != '77'
   AND t2.status  NOT IN ('IR', 'SR', 'IB')
   AND t2.order_from = 'self'
 ORDER BY t2.di_date, t2.oid ASC" ;


///////////////////////////////////////////////////   출력부   /////////////////////////////////////////////////////////
if($QUERY_STRING == "nset=$nset&page=$page"){
    $query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
    $query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}


$slave_db->query($qryOrderInfo);
$orderInfo = $slave_db->fetchall();

// 회원 일별 집계
$qryMemberInfo ="
SELECT DATE_FORMAT(DATE,'%Y-%m-%d') AS regdate,
        DATE_FORMAT(DATE,'%w') AS yoil,
        COUNT(*) AS cnt 
  FROM common_user
 WHERE DATE_FORMAT(DATE,'%Y-%m-%d') >= '".$startDate."' 
   AND DATE_FORMAT(DATE,'%Y-%m-%d') <= '".$endDate."' 
 GROUP BY DATE_FORMAT(DATE,'%Y-%m-%d')
 ORDER BY regdate ASC";
$slave_db->query($qryMemberInfo);
$memberInfo = $slave_db->fetchall();

// 조회 직전 일자 총 회원수
$qryMemberTotal ="
SELECT COUNT(*) AS cnt 
  FROM common_user
 WHERE DATE_FORMAT(DATE,'%Y-%m-%d') < '".$startDate."'";
$slave_db->query($qryMemberTotal);
$memberTotal = $slave_db->fetchall();
$memberTotal = $memberTotal['0']['cnt'];

// 탈퇴자 일별 집계
$qryDropInfo ="
SELECT DATE_FORMAT(dropdate,'%Y-%m-%d') AS dropdate,
        DATE_FORMAT(dropdate,'%w') AS yoil, 
        COUNT(*) AS cnt
  FROM common_dropmember
 WHERE DATE_FORMAT(dropdate,'%Y-%m-%d') >= '".$startDate."' 
   AND DATE_FORMAT(dropdate,'%Y-%m-%d') <= '".$endDate."' 
 GROUP BY DATE_FORMAT(dropdate,'%Y-%m-%d')
 ORDER BY dropdate ASC";
$slave_db->query($qryDropInfo);
$dropInfo = $slave_db->fetchall();


$orderList = array();
if(count($orderInfo) > 0){

    foreach($orderInfo as $p=>$v){

        if(isset($orderList[$v['di_date']])){

            if($orderList[$v['di_date']]['oid'] != $v['oid']){
                $orderList[$v['di_date']]['order_cnt'] += 1;
            }

            $orderList[$v['di_date']]['order_amount'] += $v['ptprice'];
            $orderList[$v['di_date']]['yoil'] = $v['yoil'];
            
            // 회원 
            if($v['user_code'] != ''){
                if($orderList[$v['di_date']]['oid'] != $v['oid']){
                    $orderList[$v['di_date']]['member_order_cnt'] += 1;
                }
                $orderList[$v['di_date']]['member_order_amount'] += $v['ptprice'];
            // 비회원
            }else{
                if($orderList[$v['di_date']]['oid'] != $v['oid']){
                    $orderList[$v['di_date']]['nonmember_order_cnt'] += 1;
                }
                $orderList[$v['di_date']]['nonmember_order_amount'] += $v['ptprice'];
            }

        }else{

            $orderList[$v['di_date']]['order_cnt'] = 1;
            $orderList[$v['di_date']]['order_amount'] = $v['ptprice'];
            $orderList[$v['di_date']]['yoil'] = $v['yoil'];

            if($v['user_code'] != ''){
                $orderList[$v['di_date']]['member_order_cnt'] = 1;
                $orderList[$v['di_date']]['member_order_amount'] = $v['ptprice'];
                $orderList[$v['di_date']]['nonmember_order_cnt'] = 0;
                $orderList[$v['di_date']]['nonmember_order_amount'] = 0;
            }else{
                $orderList[$v['di_date']]['member_order_cnt'] = 0;
                $orderList[$v['di_date']]['member_order_amount'] = 0;
                $orderList[$v['di_date']]['nonmember_order_cnt'] = 1;
                $orderList[$v['di_date']]['nonmember_order_amount'] = $v['ptprice'];
            }

        }
        $orderList[$v['di_date']]['oid'] = $v['oid'];
    }
}

// var_dump($orderList);exit; ///////////////////////////
if(count($memberInfo) > 0){
    foreach($memberInfo as $p=>$v){
        $orderList[$v['regdate']]['new_member_cnt'] = intval($v['cnt']);
        $orderList[$v['regdate']]['yoil'] = intval($v['yoil']);
    }
}

if(count($dropInfo) > 0){
    foreach($dropInfo as $p=>$v){
        $orderList[$v['dropdate']]['drop_member_cnt'] = intval($v['cnt']);
        $orderList[$v['dropdate']]['yoil'] = intval($v['yoil']);
    }
}


if(count($orderList)){
    $i=0;
    $tmpTotal = 0;
    foreach($orderList as $p=>$v){

        if($i==0){
            $tmpTotal += intval($memberTotal) + intval($v['new_member_cnt']) - intval($v['drop_member_cnt']);
        }else{
            $tmpTotal += intval($v['new_member_cnt']) - intval($v['drop_member_cnt']);
        }

        $orderList[$p]['member_sum'] = $tmpTotal;

        if(!isset($orderList[$p]['order_cnt'])){
            $orderList[$p]['order_cnt'] = 0;
        }
        if(!isset($orderList[$p]['order_amount'])){
            $orderList[$p]['order_amount'] = 0;
        }
        if(!isset($orderList[$p]['member_order_cnt'])){
            $orderList[$p]['member_order_cnt'] = 0;
        }
        if(!isset($orderList[$p]['non_member_order_cnt'])){
            $orderList[$p]['non_member_order_cnt'] = 0;
        }

        switch($v['yoil']){
            case 0:
                $orderList[$p]['yoil'] = '일';
                break;
            case 1:
                $orderList[$p]['yoil'] = '월';
                break;
            case 2:
                $orderList[$p]['yoil'] = '화';
                break;
            case 3:
                $orderList[$p]['yoil'] = '수';
                break;
            case 4:
                $orderList[$p]['yoil'] = '목';
                break;
            case 5:
                $orderList[$p]['yoil'] = '금';
                break;
            case 6:
                $orderList[$p]['yoil'] = '토';
                break;
        }
        $i++;
    }

    ksort($orderList);
}



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
					<table height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0 >
						<tr>
							<td bgColor=#ffffff style='padding:0 0 3px 0;height:120px;'>
								<table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
                                <tr style='border-color: silver;'>
                                    <th style='width:151px; padding-left: 15px; background: #efefef; text-align: center; height: 30px; font-weight: bold; color: #000000;'>주문 일자</th>
                                    <td class='search_box_item'  colspan=3>
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
                    <th rowspan='2' style='background: #efefef;'>일자</th>
                    <th rowspan='2' style='background: #efefef;'>요일</th>
                    <th rowspan='2' style='background: #efefef;'>자사몰 매출액</th>
                    <th rowspan='2' style='background: #efefef;'>단가</th>
                    <th colspan='5' style='background: #efefef;'>결제건수</th>
                    <th rowspan='2' style='background: #efefef;'>U.V<br>(방문자수)</th>
                    <th rowspan='2' style='background: #efefef;'>P.V<br>(페이지뷰)</th>
                    <th rowspan='2' style='background: #efefef;'>일평균PV</th>
                    <th rowspan='2' style='background: #efefef;'>구매전환율</th>
                    <th rowspan='2' style='background: #efefef;'>신규회원</th>
                    <th rowspan='2' style='background: #efefef;'>회원탈퇴</th>
                    <th rowspan='2' style='background: #efefef;'>순증감</th>
                    <th rowspan='2' style='background: #efefef;'>누계회원수</th>
                </tr>
                <tr style='border-color: silver;'>
                    <th style='background: #efefef;'>합계</th>
                    <th colspan='2' style='background: #efefef;'>회원</th>
                    <th colspan='2' style='background: #efefef;'>비회원</th>
                </tr>            
            </thead>
            <tbody align='center'>";

// loop
$tr = "";
$table_list = array();
if($orderList){
//    $times = make_time_arr(); //시간만큼 셋팅
//    $table_list = merge_time_to_order($times, $order_list); //주문리스트를 시간만큼 루트 돌아서 하나의 배열을 리턴
    $table_list = $order_list;
}


$newMemberSum = 0;
$dropMemberSum = 0;
$newDropSum = 0;


$orderRate = 0;
$totalMemberOrderRate = 0;
$totalNonmemberOrderRate = 0;
$totalMember = 0;


if($orderList) {

    foreach ($orderList as $k => $v) {

        if($v['order_cnt'] == 0){
            $amountPerOrder = 0;
            $memberOrderRate = 0;
            $nonmemberOrderRate = 0;
        }else{
            $amountPerOrder = round($v['order_amount'] / $v['order_cnt'], 1);
            $memberOrderRate = round($v['member_order_cnt'] / $v['order_cnt'], 2);
            $nonmemberOrderRate = round($v['nonmember_order_cnt'] / $v['member_order_cnt'], 2);
        }

        $orderRate += $tmpOrderUnitAmount;
        $totalMemberOrderRate += $memberOrderRate;
        $totalNonmemberOrderRate += $nonmemberOrderRate;

        $totalOrderAmount += $v['order_amount'];
        $totalOrderCnt += $v['order_cnt'];
        $totalMemberOrderCnt += $v['member_order_cnt'];
        $totalNonmemberOrderCnt += $v['nonmember_order_cnt'];

        $totalNewMember += $v['new_member_cnt'];
        $totalDropMember += $v['drop_member_cnt'];
        $totalNewDrop += $v['new_member_cnt'] - $v['drop_member_cnt'];
        $totalMember = $v['member_sum'];


        $tr .= "<tr style='border-color: silver;'>
                <td style='background: #fff;' align='center'>" . $k . "</td>
                <td style='background: #fff;' align='center'>" . $v['yoil']  . "</td>
                <td style='background: #fff;text-align:right;'>" . number_format($v['order_amount']) . "</td>
                <td style='background: #fff;text-align:right;'>" . number_format($amountPerOrder) . "</td>
                
                <td style='background: #fff;'>" . number_format($v['order_cnt']) . "</td>
                
                <td style='background: #fff;'>" . number_format($v['member_order_cnt']) . "</td>
                <td style='background: #fff;'>" . $memberOrderRate . "</td>
                
                <td style='background: #fff;'>" . number_format($v['nonmember_order_cnt']) . "</td>
                <td style='background: #fff;'>" . $nonmemberOrderRate . "</td>
                
                <td style='background: #fff;'></td>
                <td style='background: #fff;'></td>
                <td style='background: #fff;'></td>
                <td style='background: #fff;'></td>
                <td style='background: #fff;'>" . number_format($v['new_member_cnt']) . "</td>
                <td style='background: #fff;'>" . number_format($v['drop_member_cnt']) . "</td>
                <td style='background: #fff;'>" . number_format($v['new_member_cnt'] - $v['drop_member_cnt']) . "</td>
                <td style='background: #fff;text-align:right;'>" . number_format($v['member_sum']) . "</td>
             </tr>";
    }

    if($totalOrderCnt == 0){
        $totalAmountPerOrder = 0;
        $totalMemberOrderRate = 0;
        $totalNonmemberOrderRate = 0;
    }else{
        $totalAmountPerOrder = round($totalOrderAmount / $totalOrderCnt);
        $totalMemberOrderRate += round($totalMemberOrderCnt / $totalOrderCnt);
        $totalNonmemberOrderRate += round($totalNonmemberOrderCnt / $totalOrderCnt);
    }



    $tr .= "<tr style='border-color: silver;'>
                <th style='background: #efefef;'>누적 합계</th>
                <td style='background: #efefef;'></td>
                <td style='background: #efefef;text-align:right;'>".number_format($totalOrderAmount)."</td>
                <td style='background: #efefef;text-align:right;'>".number_format($totalAmountPerOrder)."</td>
                <td style='background: #efefef;'>".number_format($totalOrderCnt)."</td>
                <td style='background: #efefef;'>".number_format($totalMemberOrderCnt)."</td>
                <td style='background: #efefef;'>".number_format($totalMemberOrderRate)."</td>
                <td style='background: #efefef;'>".number_format($totalNonmemberOrderCnt)."</td>
                <td style='background: #efefef;'>".number_format($totalNonmemberOrderRate)."</td>
                <td style='background: #efefef;'></td>
                <td style='background: #efefef;'></td>
                <td style='background: #efefef;'></td>
                <td style='background: #efefef;'></td>
                <td style='background: #efefef;'>".number_format($totalNewMember)."</td>
                <td style='background: #efefef;'>".number_format($totalDropMember)."</td>
                <td style='background: #efefef;'>".number_format($totalNewDrop)."</td>
                <td style='background: #efefef;text-align:right;'>".number_format($totalMember)."</td>
             </tr>";


}else{
    $tr .= "<tr style='border-color: silver;'><td colspan='4' style='background: #fff;'>검색 후 통계 확인이 가능합니다.</td></tr>";
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
        
        $('input[name=excel_sdate]').val(sdate);
        $('input[name=excel_edate]').val(edate);
        $('input[name=excel]').val('act');
        $('#form_excels').submit();
    });
});
-->
</script>

<form id='form_excels' action='./order_statistics1.php' method='get'>
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
    $P->Navigation = "주문관리 > 매출종합 분석 > 자사몰 일단위 매출";
    $P->title = " 자사몰 일단위 매출";
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

function merge_time_to_order($times = array(), $order_list = array()){
    $table_list = array();
    $key_table = array();
    foreach ($order_list[0] as $key => $val) {
        $key_table[$key] = 0;
    }
    foreach($times as $k => $v) {
        foreach ($order_list as $key => $val) {
            if($k == $val['times']) {
                $table_list[$k]['oid'] = $val['oid'];
                $table_list[$k]['o_count'] = $val['o_count'];
                $table_list[$k]['total_price'] = $val['total_price'];
                $table_list[$k]['od_pount'] = $val['od_pount'];
                $table_list[$k]['ic_date'] = $val['ic_date'];
                $table_list[$k]['times'] = $val['times'];
                $table_list[$k]['pcount'] = $val['pcount'];
            }else if(!$table_list[$k]['oid']){
                $table_list[$k] = $key_table; //무조건 0값으로 넣음
                $table_list[$k]['times'] = $k;
            }
        }
    }
    return $table_list;
}
?>