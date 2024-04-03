<?
$script_time[start] = time();
include("../class/layout.class");
//include("../class/calender.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database; 

$max = 5;

if($_SESSION["admininfo"]["admin_level"]!=9){
	if($page_type=="")	$page_type=ORDER_STATUS_INCOM_COMPLETE;
}
$Script = "

<script language='javascript' src='shop_main_v3_calender.js'></script>
<style type='text/css'>
	.checkbox_image_tick05{width:30px;}
</style>

<script language='JavaScript'>
function sendMessage(msg){
        window.HybridApp.callAndroid(msg);
}

var list_start = $max;
var lastPostFuncBool = true;
function lastPostFunc() {   
	if(lastPostFuncBool){
		$.post('get_data_html.php?act=order_list&start=' + list_start + '&max=".$max."&page_type=".$page_type."&search_text=".$search_text."&startDate=".$startDate."&endDate=".$endDate."&status=".$status."',    
		function(data){ 
			if(data=='LOGIN'){
				alert('관리자 로그인후 사용하실수 있습니다.');
				location.href='/admin/mobile/admin.php';
				lastPostFuncBool = false;
			}else if(data.substr(0,6)=='{LAST}'){
				data=data.replace('{LAST}','');
				$('#order_list_table>tbody>tr:last').after(data);
				lastPostFuncBool = false;
			}else{
				$('#order_list_table>tbody>tr:last').after(data);
				list_start = list_start + ".$max." ;
			}
		});
	}
}

/*
$(window).scroll(function(){ 
	if  ($(window).scrollTop() >= $(document).height() - $(window).height()){ 
		lastPostFunc(); 
	}
});
*/

function searchGoodsFlow(delivery_company, invoice_no){
	//document.write('searchGoodsFlow.php?act=search&delivery_company='+delivery_company+'&invoice_no='+invoice_no);
	if(delivery_company != '' && invoice_no != ''){
		
		var f = document.createElement('form');
		window.frames['iframe_act'].location.href = '/mypage/searchGoodsFlow.php?act=search&delivery_company='+delivery_company+'&invoice_no='+invoice_no;
	}else{
		alert(language_data['orders.js']['C'][language]);//배송정보가 정확하지 않습니다. 
	}
}

function CheckStatusUpdate(frm){

	var checked_bool = false;

	if($('input[name=act]').val() =='status_update'){
		checked_bool = true;
	}else{
		$('.select_checkbox').each(function(){
			if($(this).is(':checked')){
				checked_bool = true;
			}
		});
	}

	if($('input[name=act]').val()=='delivery_update'){
		if($('select[name=delivery_company]').val()==''){
			alert('배송업체를 선택해주세요');
			$('select[name=delivery_company]').focus();
			return false;
		}

		if($('input[name=deliverycode]').val()==''){
			alert('송장번호를 입력해주세요');
			$('input[name=deliverycode]').focus();
			return false;
		}
	}

	if(!checked_bool){
		alert(language_data['orders.js']['J'][language]);//상태변경하실 주문을 한개이상 선택하셔야 합니다
		return false;
	}else{
		if(confirm('주문의 상태를 변경하시겠습니까?')){
			return true;
		}else{
			return false;
		}
	}
}

function fixAll(id){
	if($('#'+id).is(':checked')){
		$('.select_checkbox').each(function(){
			var tg=$(this);
			$(this).attr('checked',true);
			var in_id=$(this).attr('id');
			var txt='#tick_img_'+in_id;
			if($(txt)) {
				$(txt).attr('src','./images/checkbox_on.png');
			}
		})
	}else{
		$('.select_checkbox').each(function(){
			$(this).attr('checked',false);

			// imageTick
			var in_id=$(this).attr('id');
			var txt='#tick_img_'+in_id;
			if($(txt)) {
				$(txt).attr('src','./images/checkbox.png');
			}
			// imageTick
		})
	}
}

$(document).ready(function(){
	
	$('#start_datepicker').datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
				$('#end_datepicker').val(dateText);
			}else{
				$('#end_datepicker').datepicker('setDate','+0d');
			}
		}
	});

	$('#end_datepicker').datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'
	});


	$('input.select_checkbox').imageTick({
		tick_image_path: './images/checkbox_on.png',
		no_tick_image_path: './images/checkbox.png',
		image_tick_class: 'checkbox_image_tick05'
	});

	$('input#all_fix').imageTick({
		tick_image_path: './images/checkbox_on.png',
		no_tick_image_path: './images/checkbox.png',
		image_tick_class: 'checkbox_image_tick05',
		act_value: 'fixAll(\'all_fix\')'
	});

	/*
	사용 안함
	$('.deposit_schedule_head th').click(function(){
		$(this).removeClass('deposit_schedule_head_off').addClass('deposit_schedule_head_on');
		$(this).siblings().removeClass('deposit_schedule_head_on').addClass('deposit_schedule_head_off');
		$(this).next().css('border-left','1px solid #797979');
	});
	*/

});

</Script>";

$Contents01 = "
<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule_head'>
	<tr>";
		if($_SESSION["admininfo"]["admin_level"]==9){
		$Contents01 .= "
		<th style='border-left:0 none;' ".($page_type=="" || $page_type==ORDER_STATUS_INCOM_READY ? "class='deposit_schedule_head_on'" : "class='deposit_schedule_head_off'")." onclick=\"location.href='?page_type=".ORDER_STATUS_INCOM_READY."'\">입금예정</th>";
		}
		$Contents01 .= "
		<th ".($page_type==ORDER_STATUS_INCOM_COMPLETE ? "style='border-left:1px solid #797979;' class='deposit_schedule_head_on'" : "class='deposit_schedule_head_off'")." onclick=\"location.href='?page_type=".ORDER_STATUS_INCOM_COMPLETE."'\">입금확인</th>
		<th ".($page_type==ORDER_STATUS_DELIVERY_READY ? "style='border-left:1px solid #797979;' class='deposit_schedule_head_on'" : "class='deposit_schedule_head_off'")." onclick=\"location.href='?page_type=".ORDER_STATUS_DELIVERY_READY."'\">배송<br />준비중</th>
		<th ".($page_type==ORDER_STATUS_DELIVERY_ING ? "style='border-left:1px solid #797979;' class='deposit_schedule_head_on'" : "class='deposit_schedule_head_off'")." onclick=\"location.href='?page_type=".ORDER_STATUS_DELIVERY_ING."'\">배송중/<br />완료</th>
		<th ".($page_type==ORDER_STATUS_CANCEL_APPLY ? "style='border-left:1px solid #797979;' class='deposit_schedule_head_on'" : "class='deposit_schedule_head_off'")." onclick=\"location.href='?page_type=".ORDER_STATUS_CANCEL_APPLY."'\">클레임</th>
	</tr>
</table>
<div class='order_history_content' style='margin-bottom:20px;'>
	<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule'>
		<tr>
			<td colspan=''>
				<form name='search_frm' method='get' action=''>
				<input type='hidden' name='mode' value='search' />
				<input type='hidden' name='page_type' value='$page_type' />
					<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule_01'>
					<col width='22%' />
					<col width='*' />
						<tr><td colspan='2' height='8'></td></tr>
						<tr>
							<th>기간</th>
							<td class='term'><input type='text' name='startDate' value='".$startDate."' id='start_datepicker' /> ~ <input type='text' name='endDate' value='".$endDate."' id='end_datepicker'/></td>
						</tr>
						<tr><td colspan='2' height='8'></td></tr>";
						if($page_type==ORDER_STATUS_CANCEL_APPLY){
							$Contents01 .= "
							<tr>
								<th>처리상태</th>
								<td>
									<select name='status' style='width:92%;height:34px;'>
										<option value='' ".CompareReturnValue('',$status," selected").">선택해주세요</option>
										<option value='".ORDER_STATUS_CANCEL_APPLY."' ".CompareReturnValue(ORDER_STATUS_CANCEL_APPLY,$status," selected").">".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</option>
										<option value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$status," selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</option>
										<option value='".ORDER_STATUS_RETURN_APPLY."' ".CompareReturnValue(ORDER_STATUS_RETURN_APPLY,$status," selected").">".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</option>
									</select>
								</td>
							</tr>
							<tr><td colspan='2' height='8'></td></tr>";
						}
						$Contents01 .= "
						<tr>
							<th>검색어</th>
							<td class='search_langth'><input type='text' name='search_text' value='".$search_text."' /></td>
						</tr>
						<tr>
							<td></td>
							<td height='31'>*검색어 : 주문번호/구매자명/수취인</td>
						</tr>
						<tr>
							<td></td>
							<td style='padding-bottom:20px;'><input type='image' src='./images/btns_search.png' width='70' /></td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>";
	
	if($db->dbms_type == "oracle"){
		$where = "WHERE od.status !='SR' and o.oid=od.oid ";
	}else{
		$where = "WHERE od.status <> '' and od.status !='SR' and o.oid=od.oid ";
	}

	if($_SESSION["admininfo"]["admin_level"]!=9){
		$where .= " and od.company_id = '".$_SESSION["admininfo"]["company_id"]."' ";
	}
	
	//입금예정
	if($page_type=="" || $page_type==ORDER_STATUS_INCOM_READY){
		$where .=" and od.status in ('".ORDER_STATUS_INCOM_READY."')";
	}elseif($page_type==ORDER_STATUS_INCOM_COMPLETE){
		$where .=" and od.status in ('".ORDER_STATUS_INCOM_COMPLETE."')";
	}elseif($page_type==ORDER_STATUS_DELIVERY_READY){
		$where .=" and od.status in ('".ORDER_STATUS_DELIVERY_READY."')";
	}elseif($page_type==ORDER_STATUS_DELIVERY_ING){
		$where .=" and od.status in ('".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')";
	}elseif($page_type==ORDER_STATUS_CANCEL_APPLY){
		$where .=" and od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_EXCHANGE_APPLY."','".ORDER_STATUS_RETURN_APPLY."')";
	}

	if($status!=""){
		$where .=" and od.status = '".$status."' ";
	}

	if($startDate!="" && $endDate!=""){
		if($db->dbms_type == "oracle"){
			$where .= " and date_format(o.date_,'%Y%m%d') between $startDate and $endDate ";
		}else{
			$where .= " and date_format(o.order_date,'%Y%m%d') between $startDate and $endDate ";
		}
	}

	if($search_text!=""){
		$where .=" and ( o.oid = '".$search_text."' OR o.bname like '%".$search_text."%' OR o.rname like '%".$search_text."%') ";
	}
	
	/*
	if($db->dbms_type == "oracle"){
		$sql = "SELECT o.oid , sum(od.ptprice-ifnull(od.member_sale_price,0)-ifnull(od.use_coupon,0)) as payment_price
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					group by o.oid
					ORDER BY date_ DESC";
	}else{

		$sql = "SELECT o.oid , sum(od.ptprice-ifnull(od.member_sale_price,0)-ifnull(od.use_coupon,0)) as payment_price
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					group by o.oid
					ORDER BY date DESC";//쿼리 과부하로 인해 o.payment_price 뺌 -> 대표님 작업 kbk 13/05/31
	}
	*/

	if($db->dbms_type == "oracle"){
		$sql = "SELECT o.oid , sum(od.ptprice-ifnull(od.use_coupon,0)) as payment_price
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					group by o.oid
					ORDER BY date_ DESC";
	}else{
//-ifnull(od.use_coupon,0)
		$sql = "SELECT o.oid , sum(od.ptprice) as payment_price
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					group by o.oid
					ORDER BY order_date DESC";//쿼리 과부하로 인해 o.payment_price 뺌 -> 대표님 작업 kbk 13/05/31
	}


	$db->query($sql);

	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$oid_cnt++;
		$total_payment_price += $db->dt[payment_price];
	}
	

	if($page_type=="" || $page_type==ORDER_STATUS_INCOM_READY){//입금 예정일때
		/*
		if($db->dbms_type == "oracle"){
			$sql = "SELECT o.*, sum(od.ptprice-ifnull(od.member_sale_price,0)-ifnull(od.use_coupon,0)) as payment_price
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date_ DESC LIMIT 0, $max";
		}else{

			$sql = "SELECT o.*, sum(od.ptprice-ifnull(od.member_sale_price,0)-ifnull(od.use_coupon,0)) as payment_price
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date DESC LIMIT 0, $max";//쿼리 과부하로 인해 o.payment_price 뺌 -> 대표님 작업 kbk 13/05/31
		}
		*/

		if($db->dbms_type == "oracle"){
			$sql = "SELECT o.*, sum(od.ptprice-ifnull(od.use_coupon,0)) as payment_price
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date_ DESC LIMIT 0, $max";
		}else{
//-ifnull(od.use_coupon,0)
			$sql = "SELECT o.*, sum(od.ptprice) as payment_price
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY order_date DESC LIMIT 0, $max";//쿼리 과부하로 인해 o.payment_price 뺌 -> 대표님 작업 kbk 13/05/31
		}
		$db->query($sql);

	}else{

		if($db->dbms_type == "oracle"){
			$sql = "SELECT o.oid
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date_ DESC LIMIT 0, $max";
		}else{

			$sql = "SELECT o.oid
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date DESC LIMIT 0, $max";//쿼리 과부하로 인해 o.payment_price 뺌 -> 대표님 작업 kbk 13/05/31
		}

		$db->query($sql);

		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$oid_list[] = $db->dt[oid];
		}
		
		if($db->total){
			$where .= " and o.oid in ('".implode("','",$oid_list)."') ";
		}else{
			$where .= " and o.oid in ('') ";
		}
		
		if($db->dbms_type == "oracle"){
			$sql = "SELECT *
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					ORDER BY date_ DESC";
		}else{

			$sql = "SELECT *
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					ORDER BY date DESC";
		}

		$db->query($sql);
	}

	$order_list = $db->fetchall();
	
	//입금예정
	if($page_type=="" || $page_type==ORDER_STATUS_INCOM_READY){

	$Contents01 .= "
	<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
	<input type='hidden' name='act' value='select_status_update'>
	<input type='hidden' name='update_type' value='2'><!--2:선택한주문만-->
	<input type='hidden' name='pre_type' value='".ORDER_STATUS_INCOM_READY."'>
	<input type='hidden' name='status' value='".ORDER_STATUS_INCOM_COMPLETE."'>
		<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule'>
			<tr>
				<td colspan='2'>
					<div class='order_tptal_info'>
						<ul>
							<li><span class='span_BG01'>전체 <b>".number_format($oid_cnt)."</b>건</span></li>
							<li style='float:right;'>(총 주문합계 : <b style='color:#ff3e0c;'>".number_format($total_payment_price)."</b>원)</li>
						</ul>
					</div>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule_02' id='order_list_table'>
					<col width='32%' />
					<col width='*' />
					<col width='20%' />
					<tbody>
						<tr>
							<th>주문번호<br />구매자명(수취인)</th>
							<th>결제방법<br />주문금액(수량)</th>
							<th>처리상태</th>
						</tr>";
						if($db->total){
							for($i=0;$i<count($order_list);$i++){

								$db->fetch($i);

								switch($order_list[$i][method]){
									case ORDER_METHOD_CARD :
											$method = "카드결제";
										break;
									case ORDER_METHOD_BANK :
											$method = "무통장 입금<br/>".$order_list[$i][bank];
										break;
									case ORDER_METHOD_VBANK :
											$method = "가상계좌<br/>".$order_list[$i][bank];
										break;
									case ORDER_METHOD_ASCROW :
											$method = "에스크로";
										break;
									case ORDER_METHOD_NOPAY :
											$method = "무료결제";
										break;
									case ORDER_METHOD_CASH :
											$method = "현금";
										break;
									default:
											$method = "-";
								}

								$Contents01 .= "
								<tr>
									<td>".$order_list[$i][oid]."<br />".$order_list[$i][bname]."(".$order_list[$i][rname].")</td>
									<td style='padding-left:10px;text-align:left;'>".$method."<br /><span style='color:#ff3e0c;'>".number_format($order_list[$i][payment_price])."원(".$order_list[$i][pcnt]."개)</span></td>
									<td><p>".getOrderStatus($order_list[$i][status])."</p><input type='checkbox' name='oid[]' class='select_checkbox' id='oid_".$order_list[$i][oid]."' value='".$order_list[$i][oid]."'/></td>
								</tr>";
							}
						}else{
							$Contents01 .= "<tr><td colspan='3'>조회된 결과가 없습니다.</td></tr>";
						}

						$Contents01 .= "
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style='padding-top:10px;'><input type='checkbox' name='all_fix' id='all_fix' /><label for='all_fix' class='label_total_select'>전체선택</label></td>
			</tr>
			<tr>
				<th class='deposit_confirmation'><input type='image' src='./images/deposit_confirmation.png' /></th>
			</tr>
		</table>
	</form>";

	}elseif($page_type==ORDER_STATUS_INCOM_COMPLETE){
	
	$Contents01 .= "
	<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
	<input type='hidden' name='act' value='select_status_update'>
	<input type='hidden' name='update_type' value='2'><!--2:선택한주문만-->
	<input type='hidden' name='pre_type' value='".ORDER_STATUS_INCOM_COMPLETE."'>
	<input type='hidden' name='status' value='".ORDER_STATUS_DELIVERY_READY."'>
		<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule'>
			<tr>
				<td colspan='2'>
					<div class='order_tptal_info'>
						<ul>
							<li><span class='span_BG01'>전체 <b>".number_format($oid_cnt)."</b>건</span></li>
							<li style='float:right;'>(총 주문합계 : <b style='color:#ff3e0c;'>".number_format($total_payment_price)."</b>원)</li>
						</ul>
					</div>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule_02' id='order_list_table'>
					<col width='32%' />
					<col width='*' />
					<col width='20%' />
					<tbody>
						<tr>
							<th>주문번호<br />구매자명(수취인)</th>
							<th>상품정보<br />주문금액</th>
							<th>처리상태</th>
						</tr>";

						if($db->total){

							for($i=0;$i<count($order_list);$i++){

								$db->fetch($i);
				
								$Contents01 .= "
								<tr>";
									
									if($b_oid != $order_list[$i][oid]){
										$od_cnt = 0;

										foreach($order_list as $order){
											if($order_list[$i][oid] == $order[oid]){
												$od_cnt++;
											}
										}
										
										$Contents01 .= "
										<td rowspan='".$od_cnt."'>".$order_list[$i][oid]."<br />".$order_list[$i][bname]."(".$order_list[$i][rname].")</td>";
									}

									$Contents01 .= "
									<td class='goods_infomation'>
										<dl>
											<dt><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_list[$i][pid], 'm',$order_list[$i])."' /></dt>
											<dd>
												<ul>
													<li>[".$order_list[$i][company_name]."]</li>
													<li>".$order_list[$i][pname]."</li>
													<li><span style='color:#1e9be2;'>옵션 : ".($order_list[$i][option_text]!="" ? $order_list[$i][option_text] : "-")."</span></li>
												</ul>
											</dd>
										</dl>
										<p><span style='color:#ff3e0c;'>".number_format($order_list[$i][ptprice]-$order_list[$i][member_sale_price]-$order_list[$i][use_coupon])."원(".$order_list[$i][pcnt].")개</span></p>
									</td>
									<td><p>".getOrderStatus($order_list[$i][status])."</p><input type='checkbox' name='od_ix[]' class='select_checkbox' id='od_ix_".$order_list[$i][od_ix]."' value='".$order_list[$i][od_ix]."'/></td>
								</tr>";

								$b_oid = $order_list[$i][oid];

							}

						}else{
							$Contents01 .= "<tr><td colspan='3'>조회된 결과가 없습니다.</td></tr>";
						}

					$Contents01 .= "
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td style='padding-top:10px;'><input type='checkbox' name='all_fix' id='all_fix' /><label for='all_fix' class='label_total_select'>전체선택</label></td>
			</tr>
			<tr>
				<th class='delivery_ready'><input type='image' src='./images/delivery_ready.png' /></th>
			</tr>
		</table>
	</form>";

	}elseif($page_type==ORDER_STATUS_DELIVERY_READY){
		$Contents01 .= "
		<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
		<input type='hidden' name='act' value='delivery_update'>
		<input type='hidden' name='list_type' value='order'>
		<input type='hidden' name='pre_type' value='".ORDER_STATUS_DELIVERY_READY."'>
			<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule'>
				<tr>
					<td colspan='2'>
						<div class='order_tptal_info'>
							<ul>
								<li><span class='span_BG01'>전체 <b>".number_format($oid_cnt)."</b>건</span></li>
								<li style='float:right;'>(총 주문합계 : <b style='color:#ff3e0c;'>".number_format($total_payment_price)."</b>원)</li>
							</ul>
						</div>
						<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule_02' id='order_list_table'>
						<col width='32%' />
						<col width='*' />
						<col width='20%' />
						<tbody>
							<tr>
								<th>주문번호<br />구매자명(수취인)</th>
								<th>상품정보<br />주문금액</th>
								<th>처리상태</th>
							</tr>";
							
							if($db->total){

								for($i=0;$i<count($order_list);$i++){

									$db->fetch($i);
					
									$Contents01 .= "
									<tr>";
										
										if($b_oid != $order_list[$i][oid]){
											$od_cnt = 0;

											foreach($order_list as $order){
												if($order_list[$i][oid] == $order[oid]){
													$od_cnt++;
												}
											}
											
											$Contents01 .= "
											<td rowspan='".$od_cnt."'>".$order_list[$i][oid]."<br />".$order_list[$i][bname]."(".$order_list[$i][rname].")</td>";
										}

										$Contents01 .= "
										<td class='goods_infomation'>
											<dl>
												<dt><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_list[$i][pid], 'm',$order_list[$i])."' /></dt>
												<dd>
													<ul>
														<li>[".$order_list[$i][company_name]."]</li>
														<li>".$order_list[$i][pname]."</li>
														<li><span style='color:#1e9be2;'>옵션 : ".($order_list[$i][option_text]!="" ? $order_list[$i][option_text] : "-")."</span></li>
													</ul>
												</dd>
											</dl>
											<p><span style='color:#ff3e0c;'>".number_format($order_list[$i][ptprice]-$order_list[$i][member_sale_price]-$order_list[$i][use_coupon])."원(".$order_list[$i][pcnt].")개</span></p>
										</td>
										<td><p>".getOrderStatus($order_list[$i][status])."</p><input type='checkbox' name='od_ix[]' class='select_checkbox' id='od_ix_".$order_list[$i][od_ix]."' value='".$order_list[$i][od_ix]."'/></td>
									</tr>";

									$b_oid = $order_list[$i][oid];

								}

							}else{
								$Contents01 .= "<tr><td colspan='3'>조회된 결과가 없습니다.</td></tr>";
							}

						$Contents01 .= "
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td style='padding-top:20px;'>
						<table cellpadding='0' cellspacing='0' border='0' width='100%'>
						<col width='35%' />
						<col width='*' />
						<col width='16%' />
							<tr>
								<td>".deliveryCompanyList2("delivery_company","style='height:34px;width:100%;color:#b2b2b2;'",$_SESSION["admininfo"]["company_id"])."
								</td>
								<td style='padding:0 10px;'><input type='text' name='deliverycode' style='height:30px;width:96%;color:#b2b2b2;padding:0 2%;' /></td>
								<!--td align='right'><img src='./images/qr_code.png' alt='QR' style='height:36px;' /></td-->
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<th class='delivery'><input type='image' src='./images/delivery.png' alt='배송중' /></th>
				</tr>
			</table>
		</form>";
	}elseif($page_type==ORDER_STATUS_DELIVERY_ING){

		$Contents01 .= "
		<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule'>
			<tr>
				<td colspan='2'>
					<div class='order_tptal_info'>
						<ul>
							<li><span class='span_BG01'>전체 <b>".number_format($oid_cnt)."</b>건</span></li>
							<li style='float:right;'>(총 주문합계 : <b style='color:#ff3e0c;'>".number_format($total_payment_price)."</b>원)</li>
						</ul>
					</div>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule_02' id='order_list_table'>
					<col width='32%' />
					<col width='*' />
					<col width='35%' />
					<tbody>
						<tr>
							<th>주문번호<br />구매자명(수취인)</th>
							<th>상품정보<br />주문금액</th>
							<th>배송정보</th>
						</tr>";
						
						if($db->total){

							for($i=0;$i<count($order_list);$i++){

								$db->fetch($i);
				
								$Contents01 .= "
								<tr>";
									
									if($b_oid != $order_list[$i][oid]){
										$od_cnt = 0;

										foreach($order_list as $order){
											if($order_list[$i][oid] == $order[oid]){
												$od_cnt++;
											}
										}
										
										$Contents01 .= "
										<td rowspan='".$od_cnt."'>".$order_list[$i][oid]."<br />".$order_list[$i][bname]."(".$order_list[$i][rname].")</td>";
									}

									$Contents01 .= "
									<td style='padding-left:10px;text-align:left;'>[".$order_list[$i][company_name]."]<br />".$order_list[$i][pname]."<br /><span style='color:#1e9be2;'>옵션 : ".($order_list[$i][option_text]!="" ? $order_list[$i][option_text] : "-")."</span><br /><span style='color:#ff3e0c;'>".number_format($order_list[$i][ptprice]-$order_list[$i][member_sale_price]-$order_list[$i][use_coupon])."원(".$order_list[$i][pcnt].")개</span></td>
									<td>
										<table cellpadding='0' cellspacing='0' border='0' width='100%' class='add_td'>
											<tr>
												<td>
													".getOrderStatus($order_list[$i][status])."&nbsp;";
													if($order_list[$i][status]==ORDER_STATUS_DELIVERY_ING){
														$Contents01 .= "
														<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
														<input type='hidden' name='act' value='status_update'>
														<input type='hidden' name='change_status' value='".ORDER_STATUS_DELIVERY_COMPLETE."'>
														<input type='hidden' name='od_ix' value='".$order_list[$i][od_ix]."'>
														<input type='image' src='./images/delivery_success.png' align='absmiddle' width='55' />
														</form>";
													}
												$Contents01 .= "
												</td>
											</tr>
											<tr>
												<td class='delivery_shop'>".deliveryCompanyList($order_list[$i][quick],"text")."</br /><a href=\"javascript:searchGoodsFlow('".$order_list[$i][quick]."', '".str_replace("-","",$order_list[$i][invoice_no])."')\">".$order_list[$i][invoice_no]."</a></td>
											</tr>
										</table>
									</td>
								</tr>";

								$b_oid = $order_list[$i][oid];

							}

						}else{
							$Contents01 .= "<tr><td colspan='3'>조회된 결과가 없습니다.</td></tr>";
						}

					$Contents01 .= "
						</tbody>
					</table>
				</td>
			</tr>
		</table>";


	}elseif($page_type==ORDER_STATUS_CANCEL_APPLY){

		$Contents01 .= "
		<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule'>
			<tr>
				<td colspan='2'>
					<div class='order_tptal_info'>
						<ul>
							<li><span class='span_BG01'>전체 <b>".number_format($oid_cnt)."</b>건</span></li>
							<li style='float:right;'>(총 주문합계 : <b style='color:#ff3e0c;'>".number_format($total_payment_price)."</b>원)</li>
						</ul>
					</div>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' class='deposit_schedule_02' id='order_list_table'>
					<col width='32%' />
					<col width='*' />
					<col width='35%' />
					<tbody>
						<tr>
							<th>주문번호<br />구매자명(수취인)</th>
							<th>상품정보<br />주문금액</th>
							<th>배송정보</th>
						</tr>";

						if($db->total){

							for($i=0;$i<count($order_list);$i++){

								$db->fetch($i);
				
								$Contents01 .= "
								<tr>";
									
									if($b_oid != $order_list[$i][oid]){
										$od_cnt = 0;

										foreach($order_list as $order){
											if($order_list[$i][oid] == $order[oid]){
												$od_cnt++;
											}
										}
										
										$Contents01 .= "
										<td rowspan='".$od_cnt."'>".$order_list[$i][oid]."<br />".$order_list[$i][bname]."(".$order_list[$i][rname].")</td>";
									}

									$Contents01 .= "
									<td style='padding-left:10px;text-align:left;'>[".$order_list[$i][company_name]."]<br />".$order_list[$i][pname]."<br /><span style='color:#1e9be2;'>옵션 : ".($order_list[$i][option_text]!="" ? $order_list[$i][option_text] : "-")."</span><br /><span style='color:#ff3e0c;'>".number_format($order_list[$i][ptprice]-$order_list[$i][member_sale_price]-$order_list[$i][use_coupon])."원(".$order_list[$i][pcnt].")개</span></td>
									<td>
										<table cellpadding='0' cellspacing='0' border='0' width='100%' class='add_td'>
											<tr>
												<td>
													".getOrderStatus($order_list[$i][status])."&nbsp;";
													if($order_list[$i][status]==ORDER_STATUS_CANCEL_APPLY){
														$Contents01 .= "
														<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
														<input type='hidden' name='act' value='status_update'>
														<input type='hidden' name='change_status' value='".ORDER_STATUS_CANCEL_COMPLETE."'>
														<input type='hidden' name='od_ix' value='".$order_list[$i][od_ix]."'>
														<input type='image' src='./images/cancel_success.png' align='absmiddle' width='55' />
														</form>";
													}elseif($order_list[$i][status]==ORDER_STATUS_EXCHANGE_APPLY){
														$Contents01 .= "
														<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
														<input type='hidden' name='act' value='status_update'>
														<input type='hidden' name='change_status' value='".ORDER_STATUS_EXCHANGE_ING."'>
														<input type='hidden' name='od_ix' value='".$order_list[$i][od_ix]."'>
														<input type='image' src='./images/swap_agree.png' align='absmiddle' width='55' />
														</form>";
													}elseif($order_list[$i][status]==ORDER_STATUS_RETURN_APPLY){
														$Contents01 .= "
														<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
														<input type='hidden' name='act' value='status_update'>
														<input type='hidden' name='change_status' value='".ORDER_STATUS_RETURN_ING."'>
														<input type='hidden' name='od_ix' value='".$order_list[$i][od_ix]."'>
														<input type='image' src='./images/return_agree.png' align='absmiddle' width='55' />
														</form>";
													}
												$Contents01 .= "
												</td>
											</tr>
											<tr>
												<td class='delivery_shop'>";
													if($order_list[$i][quick]){
														$Contents01 .= "
														".deliveryCompanyList($order_list[$i][quick],"text")."</br /><a href=\"javascript:searchGoodsFlow('".$order_list[$i][quick]."', '".str_replace("-","",$order_list[$i][invoice_no])."')\">".$order_list[$i][invoice_no]."</a>";
													}
												$Contents01 .= "
												</td>
											</tr>
										</table>
									</td>
								</tr>";

								$b_oid = $order_list[$i][oid];

							}

						}else{
							$Contents01 .= "<tr><td colspan='3'>조회된 결과가 없습니다.</td></tr>";
						}

					$Contents01 .= "
						</tbody>
					</table>
				</td>
			</tr>
		</table>";

	}else{
		exit;
	}

$Contents01 .= "
	<div style='text-align:center;font-weight:bold;font-size:16px;cursor:pointer;padding:10px 0;' onclick=\"lastPostFunc();\">
		더보기
	</div>
</div>";



$Contents = $Contents01;




	$P = new MobileLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	$P->Navigation = "상품리스트";
	$P->TitleBool = false;
	$P->ServiceInfoBool = true;
	echo $P->PrintLayOut();



$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

?>
