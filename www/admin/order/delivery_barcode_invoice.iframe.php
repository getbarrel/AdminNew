<?php
include("../class/layout.class");
include("$DOCUMENT_ROOT/include/email.send.php");
include("../inventory/inventory.lib.php");
include("../../include/cash_manage.lib.php");

$db = new Database;

//입점업체일 경우 조건 추가 2013-04-04 bgh
if($admininfo[admin_level] == 8){
	$and_company_id = " and company_id = '".$admininfo["company_id"]."' ";
}else{
	$and_company_id = "";
}

echo "
<LINK REL='stylesheet' HREF='/admin/v3/include/admin.css?2023438184' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<LINK href='/admin/css/facebox2.css' type='text/css' rel='stylesheet'>
<link rel='stylesheet' type='text/css' href='/admin/v3/css/class.css' />
<link rel='stylesheet' type='text/css' href='/admin/v3/css/common.css' />
<script language='javascript'>
var language = 'korea';
</script>
<link rel='stylesheet' type='text/css' href='/admin/v3/css/jquery-ui.css' />
<script src='/admin/js/jquery-1.8.3.js'></script>
<script src='/admin/js/jquery-ui.js'></script>
<script language='javascript' src='/admin/js/jquery.combobox.js'></script>
<script language='javascript' src='/admin/js/jquery.blockUI.js'></script>
<script language='javascript' src='/admin/js/jquery.cookie.js'></script>
<script language='JavaScript' src='/admin/js/admin.js'></Script>
<script language='JavaScript' src='/admin/js/zoom.js'></Script>
<script language='JavaScript' src='/admin/js/auto.validation.js'></Script>
<script language='JavaScript' src='/admin/js/dd.js'></Script>
<script language='JavaScript' src='/admin/_language/language.js'></Script>
<script language='JavaScript' src='/admin/_language/language.php'></Script>
<style type='text/css'>
	.barcode-title-box {padding:7px 13px; background:#ffd236;}
	.barcode-title-box:after {content:''; display:block; clear:both;}
	.barcode-title-box .barcode-title {float:left; padding-top:8px;}
	.barcode-title-box .barcode {float:right; border:1px solid #c21f12; background:#fff; width:300px; padding:4px 8px}
	.barcode-title-box .barcode .barcode-bg {padding-left:35px; display:block; background:url(../images/korea/barcode.png) 0 2px no-repeat;}
	.barcode-title-box .barcode .barcode-bg input {width:260px;border:0;color:#743710;}
	#member_info_view {padding:15px 13px; background:#fff; border-top:2px solid #f3ad03;}
	.packing-table01 {table-layout:fixed; width:100%;}
	.packing-table01 tr th {height:34px; border-bottom:1px dashed #aaa; text-align:left; }
	.packing-table01 tr th span.th-dot {padding-left:21px; font-weight:bold; font-size:13px; background:url(../images/korea/th-dot.gif) 6px center no-repeat;}
	.packing-table01 tr td {border-bottom:1px dashed #aaa;}

	.packing-box02 {}
	.packing-box02 h4 {height:34px; line-height:34px; padding-left:21px; font-weight:bold; font-size:13px; background:url(../images/korea/th-dot.gif) 6px center no-repeat;}
	.packing-table02 {border-top:1px solid #c5c5c5; border-left:1px solid #c5c5c5; width:100%;}
	.packing-table02 tr th {height:32px; color:#000; background:#f2f2f2; border-right:1px solid #c5c5c5; border-bottom:1px solid #c5c5c5; text-align:center;}
	.packing-table02 tr td {border-right:1px solid #c5c5c5; border-bottom:1px solid #c5c5c5; text-align:center; padding:10px 0;}
</style>
<script type='text/javascript'>
<!--
	$(window.document).click(function(){
		$('input#invoice_no' , parent.document).focus();
	})
	

	$('#set_butten_tr', parent.document).hide();
	$('#set_butten_no_tr', parent.document).show();

	$('input#sub_auto_set' , parent.document).val('');
	$('input#sub_invoice_no' , parent.document).val('');
	parent.invoice_no_clean();

//-->
</script>
<style type='text/css'>
	.no_action {
		width:100%;margin-top:100px;text-align:center;vartical-align:middle;font-size:20px;color:#c6c6c6;
	}
</style>

";


if($act=="" || ($act!="" && $invoice_no =="" && $sub_auto_set=="" && $sub_invoice_no=="")){

$Contents = "
<script type='text/javascript'>
	parent.sys_msg_text(\"&nbsp;\");
</script>
<div class='no_action'>
	<b>송장번호를 입력해주세요.</b>
</div>";

}else{

	if($invoice_no!="" && $sub_invoice_no!=""){
		$sub_auto_set="";
		$sub_invoice_no="";
	}

	if($sub_auto_set=="Y"){
		$auto_set = $sub_auto_set;
		$invoice_no = $sub_invoice_no;
	}
	
	$sql="select od.oid,od.od_ix 
		from 
			shop_order_detail od
		where
			od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_EXCHANGE_APPLY."','".ORDER_STATUS_EXCHANGE_DENY."','".ORDER_STATUS_EXCHANGE_ING."','".ORDER_STATUS_EXCHANGE_DELIVERY."','".ORDER_STATUS_EXCHANGE_ACCEPT."','".ORDER_STATUS_EXCHANGE_DEFER."','".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."','".ORDER_STATUS_EXCHANGE_COMPLETE."','".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_DENY."','".ORDER_STATUS_RETURN_ING."','".ORDER_STATUS_RETURN_DELIVERY."','".ORDER_STATUS_RETURN_ACCEPT."','".ORDER_STATUS_RETURN_DEFER."','".ORDER_STATUS_RETURN_IMPOSSIBLE."','".ORDER_STATUS_RETURN_COMPLETE."')
		and 
			od.delivery_status in ('".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."')
		and
			od.stock_use_yn ='Y'
		and
			(ifnull(od.invoice_no,'') like '".$invoice_no."' OR ifnull(od.invoice_no,'') like '%,".$invoice_no.",%' OR ifnull(od.invoice_no,'') like '".$invoice_no.",%' OR ifnull(od.invoice_no,'') like '%,".$invoice_no."')  
		$and_company_id ";

	$db->query($sql);
	//echo $sql;
	$claim_order_list = $db->fetchall("object");

	$sql="select o.oid,GROUP_CONCAT(od.od_ix) as od_ix_str from 
			shop_order o 
		left join 
			shop_order_detail od
		on 
			(o.oid=od.oid) 
		where 
			(ifnull(od.invoice_no,'') like '".$invoice_no."' OR ifnull(od.invoice_no,'') like '%,".$invoice_no.",%' OR ifnull(od.invoice_no,'') like '".$invoice_no.",%' OR ifnull(od.invoice_no,'') like '%,".$invoice_no."')  
		and
			od.status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_DELAY."')
		and 
			od.delivery_status in ('".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."','".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."')
		and
			od.stock_use_yn ='Y'
		$and_company_id
		group by 
			o.oid";
	$db->query($sql);
	
	if(!$db->total && count($claim_order_list)==0){
		$Contents = "
		<script type='text/javascript'>
		<!--
			parent.sys_msg_text(\"<img src='../images/icon/alarm_warning.gif' align='absmiddle'> <span class='blue'>".$invoice_no."</span> 로 조회하셨습니다.\");
		//-->
		</script>
		<div class='no_action'>
			<b>검색된 주문이 없습니다.</b>
		</div>";
	}else{

		$order_list = $db->fetchall("object");
		
		$od_ix = array();
		$list_oid = array();
		$all_od_ix_str = "";
		if(count($order_list)){
			foreach($order_list as $ol){
				$list_oid[$ol["oid"]]=str_replace(",","','",$ol["od_ix_str"]);
				$all_od_ix_str .= ",".str_replace(",","','",$ol["od_ix_str"]);
			}

			$all_od_ix_str = substr($all_od_ix_str,1);

			$od_ix= explode("','",$all_od_ix_str);
		}else{
			$order_list = array();
		}
		
		if(count($claim_order_list)>0){

			$claim_oid = array();
			$claim_od_ix = array();
			foreach($claim_order_list as $ol){
				$claim_od_ix[]=$ol["od_ix"];
				if(!in_array($ol["oid"],$claim_oid)){
					$claim_oid[]=$ol["oid"];
				}
			}

			foreach($claim_oid as $coid){
				if(!array_key_exists($coid,$list_oid)){
					$tmp_array["oid"] = $coid;
					array_push($order_list,$tmp_array);
				}
			}
		}

		$Contents = "
		<script type='text/javascript'>
		<!-- ";

		if($auto_set=="Y"){

			if(count($od_ix)>0){
				order_update_function($od_ix);
			}
			//msg_data + 
			$Contents .= "
			parent.sys_msg_text(\"<img src='../images/icon/alarm_safe.gif' align='absmiddle'> <span class='blue'>".$invoice_no."</span> 의 주문건들이 정상적으로 처리되었습니다.\");
			";
		}else{

			$Contents .= "
			$('input#sub_auto_set' , parent.document).val('Y');
			$('input#sub_invoice_no' , parent.document).val('".$invoice_no."');
			$('#set_butten_tr', parent.document).show();
			$('#set_butten_no_tr', parent.document).hide();

			parent.sys_msg_text(\"<img src='../images/icon/alarm_safe.gif' align='absmiddle'> <span class='blue'>".$invoice_no."</span> 로 조회로 조회하신 내역 주문 <span class='red'>".count($order_list)."</span>건이 검색되었습니다.\");
			";
		}

		
		if(count($claim_od_ix) > 0){
			$Contents .= " $(document).ready(function(){alert(' *** 경고 *** \\n\\n 출고요청 후 처리상태가 변경된 리스트가 존재합니다. \\n\\n 확인 후 처리하세요. \\n\\n (주문서 하단에서 확인 가능합니다) ');}); "; 
		}

		$Contents .= "
		//-->
		</script>";
	
		foreach($order_list as $order){

			$sql="select o.*,odd.* from 
				shop_order o 
			left join 
				shop_order_detail od
			on 
				(o.oid=od.oid)
			left join 
				 shop_order_detail_deliveryinfo odd
			on 
				(odd.odd_ix=od.odd_ix)
			where
				od.oid='".$order["oid"]."'
			and
				od.stock_use_yn ='Y'
			$and_company_id";

			$db->query($sql);
			$db->fetch();

			$Contents .= "
			<div class='barcode-title-box'>
				<div class='barcode-title'>
					<img src='../images/korea/order-title.png' alt='주문내역' />
				</div>
			</div>

			<div id='member_info_view' ".($i!=0 ? "margin-top:5px;":"").">
				<div class='packing-box01'>
					<table border='0' cellspacing='0' cellpadding='0' class='packing-table01'>
						<col width='163px'>
						<col width='259px'>
						<col width='163px'>
						<col width='*'>
						<tbody>
							<tr>
								<th><span class='th-dot'>주문번호</span></th>
								<td>".$db->dt["oid"]."</td>
								<th><span class='th-dot'>주문일자</span></th>
								<td>".substr($db->dt["order_date"],0,10)."</td>
							</tr>
							<tr>
								<th><span class='th-dot'>주문자 명</span></th>
								<td>".$db->dt["bname"]."</td>
								<th><span class='th-dot'>주문자 전화번호</span></th>
								<td>".$db->dt["btel"]."</td>
							<tr>
								<th><span class='th-dot'>수취인 명</span></th>
								<td>".$db->dt["rname"]."</td>
								<th><span class='th-dot'>수취인 핸드폰</span></th>
								<td>".$db->dt["rmobile"]."</td>
							</tr>
							<tr>
								<th><span class='th-dot'>배송지 주소</span></th>
								<td colspan='3'>[".$db->dt["addr1"]."] ".$db->dt["addr2"]."</td>
							</tr>
							<tr>
								<th><span class='th-dot'>배송문구</span></th>
								<td colspan='3'>".$db->dt["msg"]."</td>
							</tr>
						</tbody>
					</table>
				</div>";

				if(count($od_ix) > 0){
					$Contents .= "
					<div class='packing-box02'>
						<h4>품목리스트</h4>
						<table border='0' cellspacing='0' cellpadding='0' class='packing-table02'>
							<thead>
								<tr>
									<th>품목/상품 코드</th>
									<th>상품명</th>
									<th>옵션</th>
									<th>출고/처리상태</th>
									<th>배송타입</th>
									<th>수량</th>
									<th>바코드</th>
								</tr>
							</thead>
							<tbody>
							";
								$sql="select od.*,gu.barcode from 
									shop_order_detail od left join inventory_goods_unit gu on (od.gid=gu.gid and od.gu_ix=gu.gu_ix)
								where
									od.oid='".$order["oid"]."'
								and 
									od.od_ix in ('".implode("','",$od_ix)."')
								and
									od.stock_use_yn ='Y'
								$and_company_id";

								$db->query($sql);

								for($i=0;$i<$db->total;$i++){
									$db->fetch($i);

									$Contents .= "
									<tr>
										<td>".$db->dt["gid"]."</td>
										<td>".$db->dt["pname"]."</td>
										<td>".strip_tags($db->dt["option_text"])."</td>
										<td>".getOrderStatus($db->dt["delivery_status"])."<br/>".getOrderStatus($db->dt["status"])."</td>
										<td>".DeliveryMethod("",$db->dt["delivery_method"],"","text")."</td>
										<td>".$db->dt["pcnt"]."</td>
										<td><input type='hidden' class='barcode' od_ix='".$db->dt["od_ix"]."' pname='".str_replace("'","\'",$db->dt["pname"])."' cnt_check='N' value='".$db->dt["barcode"]."' />".$db->dt["barcode"]."</td>
									</tr>
									";
								}

								$Contents .= "
							</tbody>
						</table>
					</div>";
				}
				
				if(count($claim_od_ix) > 0){
					$Contents .= "
					<div class='packing-box02'>
						<h4>출고취소리스트</h4>
						<table border='0' cellspacing='0' cellpadding='0' class='packing-table02'>
							<thead>
								<tr>
									<th>품목/상품 코드</th>
									<th>상품명</th>
									<th>옵션</th>
									<th>출고/처리상태</th>
									<th>배송타입</th>
									<th>수량</th>
									<th>바코드</th>
								</tr>
							</thead>
							<tbody>
							";
								$sql="select od.*,gu.barcode from 
									shop_order_detail od left join inventory_goods_unit gu on (od.gid=gu.gid and od.gu_ix=gu.gu_ix)
								where
									od.oid='".$order["oid"]."'
								and 
									od.od_ix in ('".implode("','",$claim_od_ix)."') ";

								$db->query($sql);

								for($i=0;$i<$db->total;$i++){
									$db->fetch($i);

									$Contents .= "
									
									<tr>
										<td>".$db->dt["gid"]."</td>
										<td>".$db->dt["pname"]."</td>
										<td>".strip_tags($db->dt["option_text"])."</td>
										<td>".getOrderStatus($db->dt["delivery_status"])."<br/>".getOrderStatus($db->dt["status"])."</td>
										<td>".DeliveryMethod("",$db->dt["delivery_method"],"","text")."</td>
										<td>".$db->dt["pcnt"]."</td>
										<td>".$db->dt["barcode"]."</td>
									</tr>
									";
								}

								$Contents .= "
							</tbody>
						</table>
					</div>";
				}
				
			$Contents .= "
			</div>";

		}
	}
}

echo $Contents;
exit;

function order_update_function($od_ix){
	global $and_company_id,$sub_auto_set,$invoice_no;

	$db = new Database;

	if(is_array($od_ix)){
		$od_ix_str="";
		for($j=0;$j < count($od_ix);$j++){
			if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
			else						$od_ix_str .= ",'".$od_ix[$j]."'";
		}
	}

	$sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
	$db->query($sql);
	$order_details = $db->fetchall();

	for($i=0;$i < count($order_details);$i++){

		$update_str="";

		$update_str .= " , is_check_delivery = 'Y' ";

		$sql="update ".TBL_SHOP_ORDER_DETAIL." set
			update_date = NOW()
			$update_str
			where od_ix='".$order_details[$i]["od_ix"]."' 
			$and_company_id";

		$db->query($sql);
		
		set_order_status($order_details[$i][oid],ORDER_STATUS_WAREHOUSE_DELIVERY_READY,"출고검수확정[".($sub_auto_set=="Y" ? "수동" : "자동")."]",$_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")",$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"","",$invoice_no);
	}
}



?>