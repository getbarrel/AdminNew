<?php

include_once("../class/layout.class");
include_once("../sellertool/sellertool.lib.php");

include("../campaign/mail.config.php");
include("../order/orders.lib.php");

if($excel_searialize_value){
    //var_dump($excel_searialize_value);
    //echo urldecode(unserialize($excel_searialize_value));
    $ex_get =  unserialize(urldecode($excel_searialize_value));

    if(isset($ex_get['mode'])) $mode = $ex_get['mode'];
    if(isset($ex_get['startDate'])) $startDate = $ex_get['startDate'];
    if(isset($ex_get['endDate'])) $endDate = $ex_get['endDate'];
    if(isset($ex_get['order_from'])) $order_from = $ex_get['order_from'];

    //[mode] => search [startDate] => 2019-01-28 [endDate] => 2019-02-27
//    print_r($ex_get);
//    print_r($order_from);
//    exit;

}
if($startDate == ""){
    $vdate = date("Y-m-d", time());
    $startDate = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-1,substr($vdate,8,2)+1,substr($vdate,0,4)));
    $endDate = date("Y-m-d");
}

if(empty($type)){
    $type = $fix_type;
}

if($view_type == 'pos_order' || $view_type == 'sc_order'){
    $rows_cnt = '3';			// 포스관리일 경우 교환처리상태가 필요없어서 주석처리 됨을 rowspan 값을 2로 설정해줌 2013-07-07 이학봉
}else{
    $rows_cnt = '4';
}

if(!$title_str){
    $title_str  = "상품매출분석";
}

if($max == ""){
    //$max = 15; //페이지당 갯수
    $max = 100000; //페이지당 갯수
}

if($page == ''){
    $start = 0;
    $page  = 1;
}else{
    $start = ($page - 1) * $max;
}

// 검색 조건 설정 부분
if($view_type == 'sc_order'){
    $where = "WHERE od.status !='SR' AND od.product_type IN (".implode(',',$sns_product_type).") ";
    $folder_name = "sns";
}else{
    $where = "WHERE od.status !='SR' ";
    $folder_name = "product";
}

if($mode != "search"){
    $orderdate = 1;
}

if(!$date_type){
    $date_type = "o.order_date";
}

if($orderdate){
    //$where .= "and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
    $where .= "and ".$date_type." between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
}

if($mmode == "personalization" && $mem_ix != ""){
    $where .= " and o.user_code = '".$mem_ix."' ";
}

if(is_array($type)){
    for($i = 0; $i < count($type); $i++){
        if($type[$i] && $type[$i] == "IC"){
            if($o_type_str == ""){
                $o_type_str .= "'".$type[$i]."'";
            }else{
                $o_type_str .= ", '".$type[$i]."' ";
            }
        }else if($type[$i] && $type[$i] != "IC"){
            if($type_str == ""){
                $type_str .= "'".$type[$i]."'";
            }else{
                $type_str .= ", '".$type[$i]."' ";
            }
        }
    }

    if($type_str != "" && $o_type_str != ""){
        $where .= "and (o.status in ($o_type_str) or od.status in ($type_str))";
    }else if($type_str != ""){
        $where .= "and od.status in ($type_str) ";
    }else if($o_type_str != ""){
        $where .= "and o.status in ($o_type_str) ";
    }
}else{
    if($type && $type == "IC"){
        $where .= "and o.status = '$type' ";
    }else if($type && $type != "IC"){
        $where .= "and od.status = '$type' ";
    }
}

if(is_array($refund_type)){
    for($i = 0; $i < count($refund_type); $i++){
        if($refund_type[$i]){
            if($refund_type_str == ""){
                $refund_type_str .= "'".$refund_type[$i]."'";
            }else{
                $refund_type_str .= ", '".$refund_type[$i]."' ";
            }
        }
    }

    if($refund_type_str != ""){
        $where .= "and od.refund_status in ($refund_type_str) ";
    }
}else{
    if($refund_type){
        $where .= "and od.refund_status = '$refund_type' ";
    }
}

$left_join = "";
if(is_array($method)){
    for($i=0;$i < count($method);$i++){
        if($method[$i] != ""){
            if($method_str == ""){
                $method_str .= "'".$method[$i]."'";
            }else{
                $method_str .= ", '".$method[$i]."' ";
            }
        }
    }
    if($method_str != ""){
        $where .= "and op.method in ($method_str) ";
        $left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.method in ($method_str)) ";
    }
}else{
    if($method){
        $where .= "and op.method = '$method' ";
        $left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.method = '$method') ";
    }
}

if($search_type && $search_text){
    if($search_type == "combi_name"){
        $where .= "and (bname LIKE '%".trim($search_text)."%'  or odd.rname LIKE '%".trim($search_text)."%') ";
    }else if($search_type == "combi_cooid"){
        $where .= "and (od.co_oid = '".trim($search_text)."'  or od.co_od_ix = '".trim($search_text)."') ";
    }else if($search_type == "combi_email"){
        $where .= "and (bmail LIKE '%".trim($search_text)."%'  or odd.rmail LIKE '%".trim($search_text)."%') ";
    }else if($search_type == "combi_tel"){
        $where .= "and (REPLACE(btel,'-','') LIKE '%".trim($search_text)."%'  or REPLACE(odd.rtel,'-','') LIKE '%".trim($search_text)."%' or btel LIKE '%".trim($search_text)."%' or odd.rtel LIKE '%".trim($search_text)."%') ";
    }else if($search_type == "combi_mobile"){
        $where .= "and ( REPLACE(bmobile,'-','') LIKE '%".trim($search_text)."%'  or REPLACE(odd.rmobile,'-','') LIKE '%".trim($search_text)."%' or bmobile LIKE '%".trim($search_text)."%'  or odd.rmobile LIKE '%".trim($search_text)."%' ) ";
    }else{
        if($search_type == "o.bmobile"||$search_type == "odd.rmobile"){
            $where .= "and ( REPLACE(".$search_type.",'-','') LIKE '%".trim($search_text)."%' or ".$search_type." LIKE '%".trim($search_text)."%' ) ";
        }else{
            $where .= "and $search_type LIKE '%".trim($search_text)."%' ";
        }
    }
    if($search_type == "combi_name" || $search_type == "combi_email" || $search_type == "combi_tel" || $search_type == "combi_mobile" || substr_count($search_type,'odd.') > 0){
        $left_join .= " left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix) ";
    }
}

/**
 * 제휴사 ... 자동으로 들어감
 */
$where_op = "";
if(is_array($order_from)){
    for($i = 0; $i < count($order_from); $i++){
        if($order_from[$i] != ""){
            if($order_from_str == ""){
                $order_from_str .= "'".$order_from[$i]."'";
            }else{
                $order_from_str .= ",'".$order_from[$i]."' ";
            }
        }
    }

    if($order_from_str != ""){
        $where_op .= "and od.order_from in ($order_from_str) ";
    }
}else{
    if($order_from){
        $where_op .= "and od.order_from = '$order_from' ";
    }
}

if(is_array($payment_agent_type)){
    for($i = 0; $i < count($payment_agent_type); $i++){
        if($payment_agent_type[$i] != ""){
            if($payment_agent_type_str == ""){
                $payment_agent_type_str .= "'".$payment_agent_type[$i]."'";
            }else{
                $payment_agent_type_str .= ", '".$payment_agent_type[$i]."' ";
            }
        }
    }

    if($payment_agent_type_str != ""){
        $where .= "and o.payment_agent_type in ($payment_agent_type_str) ";
    }
}else{
    if($payment_agent_type){
        $where .= "and o.payment_agent_type = '$payment_agent_type' ";
    }
}

if(is_array($delivery_status)){
    for($i = 0; $i < count($delivery_status); $i++){
        if($delivery_status[$i] != ""){
            if($delivery_status_str == ""){
                $delivery_status_str .= "'".$delivery_status[$i]."'";
            }else{
                $delivery_status_str .= ", '".$delivery_status[$i]."' ";
            }
        }
    }

    if($delivery_status_str != ""){
        $where .= "and od.delivery_status in ($delivery_status_str) ";
    }
}else{
    if($delivery_status){
        $where .= "and od.delivery_status = '$delivery_status' ";
    }
}

if($md_code != ""){
    $where .= "and od.md_code = '".$md_code."'";
}

if(is_array($p_admin) && count($p_admin) == 1){
    if($p_admin[0] == "A"){
        $where .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
    }else if($p_admin[0] == "S"){
        $where .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
    }
}else{
    if($p_admin == "A"){
        $where .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
    }else if($p_admin == "S"){
        $where .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
    }
}

if($stock_use_yn != ""){
    $where .= "and od.stock_use_yn = '".$stock_use_yn."'";
}

if($mall_ix != ""){
    $where .= "and od.mall_ix = '".$mall_ix."'";
}

if($gp_ix != ""){
    $where .= "and o.gp_ix = '".$gp_ix."'";
}



//echo $sql;
//var_dump($order_from_str);
//startDate
$sql ="
SELECT 
	o.oid
	, count(od.oid) AS o_count
	, sum(o.total_price) AS total_price
	, sum(od.pcnt) AS od_pount
	, od.ic_date
	, od.order_from
   
FROM shop_order o
INNER JOIN  shop_order_detail od ON o.oid = od.oid


WHERE 
	od.ic_date >= DATE_FORMAT('".$startDate."','%Y-%m-%d') 
	AND  od.ic_date <= DATE_FORMAT('".$endDate." 23:59:59','%Y-%m-%d %H:%i:%s')
	AND  od.status = 'IC' 
	".$where_op."
GROUP BY od.order_from
ORDER BY od.ic_date ASC  
" ;





///////////////////////////////////////////////////   출력부   /////////////////////////////////////////////////////////


if($QUERY_STRING == "nset=$nset&page=$page"){
    $query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
    $query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

//$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;


/*
$Contents = "
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "상품매출분석 > $title_str ")."</td>
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
				";

// 제휴사


$Contents .= "
					<td align='left' colspan=2  width='100%' valign=top style='padding-top:5px;'>
					<table height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0 >
					";
if($admininfo["admin_level"] == 9){
    $Contents .= "
                                  <tr>
							<td bgColor=#ffffff style=''>
                                <table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
                                                <tr style='border-color: silver;'>
                                                <th style='padding-left: 15px; background: #efefef; text-align: left; height: 30px; font-weight: bold; color: #000000;'>제휴사 선택</th>";

    if($view_type == 'offline_order'){		//영업관리용도
        $Contents .= "                              <td colspan='8'><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." checked><label for='order_from_offline'>통합구매</label></td>";
    }else if($view_type == 'pos_order'){		//포스관리용도
        $Contents .= "                              <td colspan='8'><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." checked><label for='order_from_pos'>POS</label></td>";
    }else{
        $Contents .= "
                                                    <td><input type='checkbox' name='order_from[]' class='of_default' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자체쇼핑몰</label>";
        $slave_db->query("select * from sellertool_site_info where disp='1' ");
        $sell_order_from = $slave_db->fetchall();
        if(count($sell_order_from) > 0){
            for($i = 0; $i < count($sell_order_from); $i++){
//                if($i == 5 || ($i > 5 && $i%8 == 5)){
//                    $Contents .= "              </tr>
//                                                <tr>";
//                }
                $Contents .= "<input type='checkbox' name='order_from[]' class='of_chk' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label>";
            }
//            if(count($sell_order_from) < 5){
//                for($j = 0; $j < 5 - count($sell_order_from); $j++){
//                    $Contents .= "<td></td>";
//                }
//            }
            $Contents .= "</td>";
        }else{
            $Contents .= "
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>";
        }
    }

    $Contents .= "</tr>";
}
*/

$Contents = "
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "주문관리 > $title_str ")."</td>
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
				<!--tr height=25>
					<td colspan=2  align='left'  style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>주문정보 검색하기</b></td>
				</tr-->
				<tr>
					<td align='left' colspan=2  width='100%' valign=top style='padding-top:5px;'>
					<table height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0 >
						<tr>
							<td bgColor=#ffffff style='padding:0 0 3px 0;height:120px;'>
								<table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
									<col width=5%>
									<col width=10%>
									<col width=35%>
									<col width=15%>
									<col width=35%>";
if($_SESSION["admin_config"][front_multiview] == "Y"){
    $Contents .= "
                                    <tr>
                                        <td class='search_box_title' colspan='2'> 프론트 전시 구분</td>
                                        <td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
                                    </tr>";
}

$Contents .= "";

//판매처
if($admininfo["admin_level"] == 9){
    $Contents .= "
                                    <tr height=30>
                                        <th class='search_box_title' colspan='2'>판매처 선택 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
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
                                                <tr height=25>";

    if($view_type == 'offline_order'){		//영업관리용도
        $Contents .= "                              <td colspan='8'><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." checked><label for='order_from_offline'>통합구매</label></td>";
    }else if($view_type == 'pos_order'){		//포스관리용도
        $Contents .= "                              <td colspan='8'><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." checked><label for='order_from_pos'>POS</label></td>";
    }else{
        $Contents .= "
                                                    <td><input type='checkbox' name='order_from[]' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자체쇼핑몰</label></td>";
        $slave_db->query("select * from sellertool_site_info where disp='1' ");
        $sell_order_from = $slave_db->fetchall();
        if(count($sell_order_from) > 0){
            for($i = 0; $i < count($sell_order_from); $i++){
                if($i == 5 || ($i > 5 && $i%8 == 5)){
                    $Contents .= "              </tr>
                                                <tr>";
                }
                $Contents .= "                  <td><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></td>";
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
    }

    $Contents .= "
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>";
} //end 판매처

$Contents .= "
                                        <tr style='border-color: silver;'>
                                            <th style='background: #efefef; padding-left: 15px; background: #efefef; text-align: left; height: 30px; font-weight: bold; color: #000000;' colspan='2'>기간</th>
                                            
                                            <td class='search_box_item' colspan='5'>
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
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center'  item='this_board'>
	<tr>";




if($mode == "search"){
    $slave_db->query($sql);
    $order_list=$slave_db->fetchall("object");
}

//view main content 기준
$view_data = "
        <table width='100%' border='1' cellpadding='3' cellspacing='0' align='center'  class='data_table' style='display: border-collapse: separate; border-spacing: 1px; background: #c5c5c5; border: 1px;'>
            <thead>
                <tr style='border-color: silver;'><th rowspan='2' style='background: #efefef;'>제휴사</th><th colspan='3' style='background: #efefef;'>매출(입금예정 제외)</th></tr>
                <tr style='border-color: silver;'><th style='background: #efefef;'>주문(건)</th><th style='background: #efefef;'>실 매출액(원)</th><th style='background: #efefef;'>수량(개)</th></tr>            
            </thead>
            <tbody align='center'>
";

// loop
$tr = "";
$table_list = array();
if($order_list){
    //여기는 동일함
    $table_list = $order_list;
 }

$o_count = 0; //주문수량
$total_price = 0; //총액
$od_pount = 0; //상품수량

if($table_list) {
    foreach ($table_list as $key => $val) {

        $tr .= "<tr style='border-color: silver;'>
                <td style='background: #fff;'>" . sellertool_site_find_code($val['order_from'], $db) . "</td>
                <td style='background: #fff;'>" . number_format($val['o_count']) . "</td>
                <td style='background: #fff;'>" . number_format($val['total_price']) . "</td>
                <td style='background: #fff;'>" . number_format($val['od_pount']) . "</td>                
             </tr>";
        $o_count = $o_count + $val['o_count'];
        $total_price = $total_price + $val['total_price'];
        $od_pount = $od_pount + $val['od_pount'];
    }
    /* 종합 생략 함
    $tr .= "<tr>
                <th>합계</th>
                <td>".$o_count."</td>
                <td>".$total_price."</td>
                <td>".$od_pount."</td>                
             </tr>";
    */
}else{
    $tr .= "<tr style='border-color: silver;'><td colspan='4' style='background: #fff;'>검색 후 통계 확인 가능합니다.</td></tr>";
}
// end loop




$view_data .= $tr;
$view_data .= " </tbody>        
        </table>"; //end table data
if($_GET['excel'] == "act") {
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . iconv("UTF-8", "CP949", "상품매출분석") . '_' . date("Ymd") . '.xls"');
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
  ";


$Contents .= "
	</table>";



$Script="
<script type='text/javascript'>
<!--

	function BatchSubmit(frm){

		if(frm.update_type.value == 1 && '".$_GET[mode]."' != 'search'){
			alert('적용대상중 [검색주문전체]는 검색후 사용 가능합니다. 확인후 다시 시도해주세요');
			return false;
		}

		if($('#update_kind_sms').attr('checked')){
			if(frm.sms_text.value.length < 1){
				alert('SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요');
				frm.sms_text.focus();
				return false;
			}
		}else if($('#update_kind_sendemail').attr('checked')){

			if(frm.email_subject.value.length < 1){
				alert(language_data['member_batch.php']['F'][language]);//'이메일 제목을 입력해주세요'
				frm.email_subject.focus();
				return false;
			}

			/*
			if(frm.mail_content.value.length < 1 || frm.mail_content.value == '<P>&nbsp;</P>'){
				alert(language_data['member_batch.php']['G'][language]);//'이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요'
				//frm.mail_content.focus();
				return false;
			}
			*/
		}

		if(frm.update_type.value == 1){
			if($('#update_kind_sms').attr('checked')){
				if(!confirm('검색주문 전체에게 SMS 발송을 하시겠습니까?')){return false;}
			}else if($('#update_kind_sendemail').attr('checked')){
				if(!confirm('검색주문 전체에게 이메일 발송을 하시겠습니까?')){return false;}//'검색회원 전체에게 이메일발송을 하시겠습니까?'
			}
			//alert(frm.update_kind.value);
		}else if(frm.update_type.value == 2){
			var oid_checked_bool = false;
			for(i=0;i < frm.oid.length;i++){
				if(frm.oid[i].checked){
					oid_checked_bool = true;
				}
			}
			if(!oid_checked_bool){
				alert('선택하신 주문이 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요.');
				return false;
			}
		}
	}

	function view_order_num(sel,num) {
		var sms_cnt=document.getElementById('remainder_sms_cnt');
		var email_cnt=document.getElementById('remainder_email_cnt');
		var frm=document.listform;
		if(sel.value==1) {
			sms_cnt.innerHTML=num;
			email_cnt.innerHTML=num;
		} else {
			var oid_checked_num = 0;
			for(i=1;i < frm.oid.length;i++){
				if(frm.oid[i].checked){
					oid_checked_num++;
				}
			}
			sms_cnt.innerHTML=oid_checked_num;
			email_cnt.innerHTML=oid_checked_num;
		}
	}

	function ChangeUpdateForm(selected_id){
		var area = new Array('batch_update_sms','batch_update_sendemail');

		for(var i=0; i<area.length; ++i){
			if(area[i]==selected_id){
				document.getElementById(selected_id).style.display = 'block';
			}else{
				document.getElementById(area[i]).style.display = 'none';
			}
		}
	}

	function LoadEmail(email_type){
		if(email_type == 'new'){
			//$('#email_subject_text').css('display','inline');
			$('#email_select_area').css('display','none');
		}else if(email_type == 'box'){
			//$('#email_subject_text').css('display','none');
			$('#email_select_area').css('display','inline');
		}
	}	
//-->
</script>";

$js = "
<script type='text/javascript'>
<!--

$(document).ready(function() {
		
});

$(window).on('load', function(){
    $('#btn_excel_dn').click(function (){
		    var uri = $('input[name=search_searialize_value]').val();
		    var action = $('#form_excels').attr('action');
		    
		    $('input[name=excel_searialize_value]').val(uri);
		    $('input[name=excel]').val('act');
		    
		    //uri = action + '?' + uri + '&excel=act';		    
		    //$('#form_excels').attr('action',uri);		  
		    //alert($('#form_excels').attr('action'));
		    
		    $('#form_excels').submit();
    });
});
-->
</script>
<form id='form_excels' action='./order_total_list_openmaket.php' method='get'>
<input type='hidden' name='excel_searialize_value' value=''>
<input type='hidden' name='excel' value=''>
</form>
<style>
.data_table th {padding: 10px 10px;}
.data_table td {padding: 10px 10px;}
.of_chk{margin-left: 10%;}
.of_default{margin-left: 10px;}
</style>
";

$Contents .= "

<form name='lyrstat'>
	<input type='hidden' name='opend' value='' />
</form>";
if($mmode == "personalization"){
    $P = new ManagePopLayOut();
    //$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
    //$P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n".$Script; //필요없음
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
    //$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
    //$P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n".$Script; //필요없음
    $P->addScript = $js;
    $P->Navigation = "주문관리 >  상품매출분석";
    $P->title = "상품매출분석";
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

function sellertool_site_find_code($find_code, &$db){
    $site_name = "";
    if(!$find_code || $find_code == 'self') {
        $site_name = "듀이트리";
    }else{
        $sql = "
        SELECT site_name
        FROM sellertool_site_info 
        WHERE site_code = '" . $find_code . "'";
        $db->query($sql);
        $data = $db->fetchAll("object");
        if($data&& isset($data[0])){
            $site_name = $data[0]['site_name'];
        }else{
            $site_name = '미등록 제휴사';
        }
    }
    return $site_name;
}
?>