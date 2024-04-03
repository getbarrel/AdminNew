function OrderViewType(){	
	
		if($('#order_view_type').attr('checked') == true || $('#order_view_type').attr('checked') == "checked"){		
			//alert($('#order_view_type').attr('checked'));
			$.cookie('order_view_type', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('order_view_type', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
		document.location.reload();
}

function searchGoodsFlow(delivery_company, invoice_no){
	//document.write('searchGoodsFlow.php?act=search&delivery_company='+delivery_company+'&invoice_no='+invoice_no);
	if(delivery_company != "" && invoice_no != ""){
		
		var f    = document.createElement('form');
		window.frames['iframe_act'].location.href = '/mypage/searchGoodsFlow.php?act=search&delivery_company='+delivery_company+'&invoice_no='+invoice_no;
	}else{
		alert(language_data['orders.goods_list.js']['A'][language]);// 배송정보가 정확하지 않습니다. 
	}
}

function zipcode(id)
{
	var zip = window.open('../member/zipcode.php?type='+id,'','width=440,height=350,scrollbars=yes,status=no');
}

function return_pop(od_ix)
{
	var zip = window.open('return_reason_pop.php?od_ix='+od_ix,'','width=440,height=350,scrollbars=yes,status=no');
}

function ViewdeliveryCodeInputBox(StatusCode,frm){
	
	
	if(StatusCode == "DI"){
		
		document.getElementById("deliverycode").style.display = "inline";
		document.getElementsByName("quick")[0].style.display = "inline";
	}else{
		document.getElementById("deliverycode").style.display = "none";
		
		document.getElementsByName("quick")[0].style.display = "none";
	}
	
	
}


function init_date(FromDate,ToDate) {
	var frm = document.search_frm;
	
	
	for(i=0; i<frm.vFromYY.length; i++) {
		if(frm.vFromYY.options[i].value == FromDate.substring(0,4))
			frm.vFromYY.options[i].selected=true
	}
	for(i=0; i<frm.vFromMM.length; i++) {
		if(frm.vFromMM.options[i].value == FromDate.substring(5,7))
			frm.vFromMM.options[i].selected=true
	}
	for(i=0; i<frm.vFromDD.length; i++) {
		if(frm.vFromDD.options[i].value == FromDate.substring(8,10))
			frm.vFromDD.options[i].selected=true
	}
	
	
	for(i=0; i<frm.vToYY.length; i++) {
		if(frm.vToYY.options[i].value == ToDate.substring(0,4))
			frm.vToYY.options[i].selected=true
	}
	for(i=0; i<frm.vToMM.length; i++) {
		if(frm.vToMM.options[i].value == ToDate.substring(5,7))
			frm.vToMM.options[i].selected=true
	}
	for(i=0; i<frm.vToDD.length; i++) {
		if(frm.vToDD.options[i].value == ToDate.substring(8,10))
			frm.vToDD.options[i].selected=true
	}
	
}


function onLoad(FromDate, ToDate, frm) {
	if(!frm){
		var frm = document.search_frm;
	}
	
	
	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);
	
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;	
	
	init_date(FromDate,ToDate);
	
}



function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		frm.vFromYY.disabled = false;
		frm.vFromMM.disabled = false;
		frm.vFromDD.disabled = false;
		frm.vToYY.disabled = false;
		frm.vToMM.disabled = false;
		frm.vToDD.disabled = false;
	}else{
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;		
	}
}



function clearAll(frm){
		for(i=0;i < frm.oid.length;i++){
				frm.oid[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.oid.length;i++){
				frm.oid[i].checked = true;
		}
}

function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}


//od_ix 용 생성
function clearAll2(frm){
	for(i=0;i < frm.od_ix.length;i++){
			frm.od_ix[i].checked = false;
	}
}

function checkAll2(frm){
	for(i=0;i < frm.od_ix.length;i++){
			frm.od_ix[i].checked = true;
	}
}

function fixAll2(frm){
	if (!frm.all_fix2.checked){
		clearAll2(frm);
		frm.all_fix2.checked = false;
	}else{
		checkAll2(frm);
		frm.all_fix2.checked = true;
	}
}

function listAction(frm){
		
	PoPWindow('../sms.pop.php',450,300,'sendsms');
	frm.action = '../sms.pop.php';
	frm.target = 'sendsms';
	frm.submit();
}


function CheckStatusUpdate(frm){
	var checked_bool = false;
	var other_seller_bool = false;
	var select_oid ="";
	var oid_str ="";
	var select_cnt =0;
	var pre_type = frm.pre_type.value;
	var level ="";

	$('[name=update_kind]').each(function(){
		if($(this).prop('checked')==true){
			level = $(this).val();
		}
	});

	var status_str = level+"_status";
	var status ="";
	var delivery_status_str = level+"_delivery_status";
	var delivery_status ="";
	var reason_code_str = level+"_reason_code";


	$('[name='+status_str+']').each(function(){
		if($(this).prop('checked')==true){
			status = $(this).val();
		}
	});

	$('[name='+delivery_status_str+']').each(function(){
		if($(this).prop('checked')==true){
			delivery_status = $(this).val();
		}
	});

	if(status.length < 1 && delivery_status.length < 1){
		alert('처리상태값을 선택해주세요');
		return false;
	}
	
	if(frm.update_type.value==2){// 선택한 주문일때

		for(i=0;i < frm.od_ix.length;i++){
			if(frm.od_ix[i].checked){
				checked_bool = true;
				if(status=='provider_print' || status=='buyer_print' || status=='combo_print'){
					if($(frm.od_ix[i]).attr('oid')){
						if(select_oid != $(frm.od_ix[i]).attr('oid')){
							select_oid = $(frm.od_ix[i]).attr('oid');
							if(select_cnt==0)		oid_str += $(frm.od_ix[i]).attr('oid');
							else							oid_str += ','+$(frm.od_ix[i]).attr('oid');
							select_cnt++;
						}
					}
				}
				/*
				else if(pre_type=='WDR'||pre_type=='IC'){//출고대기,빠른송장입력
					if(status=='DI'){
						if(frm.od_ix[i].value){
							if($("[name='delivery_method["+frm.od_ix[i].value+"]']").val().length < 1){
								$("[name='delivery_method["+frm.od_ix[i].value+"]']").focus();
								alert('배송타입을 선택해야 합니다.');
								return false;
							}
							if($("[name='delivery_method["+frm.od_ix[i].value+"]']").val()=='tekbae' && $("[name='quick["+frm.od_ix[i].value+"]']").val().length < 1){
								$("[name='quick["+frm.od_ix[i].value+"]']").focus();
								alert('배송업체을 선택해야 합니다.');
								return false;
							}
							if($("[name='delivery_method["+frm.od_ix[i].value+"]']").val()=='tekbae' && $("[name='deliverycode["+frm.od_ix[i].value+"]']").val().length < 1){
								$("[name='deliverycode["+frm.od_ix[i].value+"]']").focus();
								alert('송장번호를 입력해야 합니다.');
								return false;
							}
						}
					}
				}*/
			}
		}
		

		if(!checked_bool){
			alert(language_data['orders.goods_list.js']['G'][language]);//상태변경하실 주문을 한개이상 선택하셔야 합니다.
			return false;
		}else{
			if(status=='DI'){//배송중은 delivery_update 쪽에서 모두 처리
				frm.act.value='delivery_update';
			}else if(delivery_status=='WDA' || delivery_status=='WDR'|| delivery_status=='WDACC'){
				frm.act.value='select_delivery_status_update';
			}

			if(status=='provider_print'||status=='buyer_print'||status=='combo_print'){//공급자용,구매자용
				PopSWindow('../order/orders.read.php?mmode=print&print_type='+status+'&oid='+oid_str,950,500,'orders_print');
			}else{
				if(confirm('선택하신 상태로 처리 하시겠습니까?')){
					return true;
				}else{
					return false;
				}
			}
		}
		return false;
	}
}

function ChangeUpdateForm(selected_id){
	var area = new Array('help_text_level0','help_text_level1','help_text_level2');

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}


function order_delete(act, oid){
	if (act == "delete")
	{
		if(confirm(language_data['orders.goods_list.js']['H'][language])){//[카드결제]의 경우는 승인취소후 삭제해주세요. 해당 주문을  정말로 삭제하시겠습니까?
			window.frames["act"].location.href= 'orders.act.php?act='+act+'&oid='+oid;
		}
	}
}

function ChangeStatus(act, oid, od_ix, this_status, change_status){
		//alert(this_status+":::"+change_status);
		if(change_status == "IC"){
			if(confirm(language_data['orders.goods_list.js']['I'][language])){//'해당 주문을 입금확인 처리 하시겠습니까?'
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "DR"){
			if(confirm(language_data['orders.goods_list.js']['J'][language])){//'해당 주문상품을 배송준비중처리 하시겠습니까?'
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "DC"){
			if(confirm(language_data['orders.goods_list.js']['K'][language])){//해당 주문상품을 배송완료 처리 하시겠습니까?
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "CC"){
			if(confirm(language_data['orders.goods_list.js']['L'][language])){//해당 주문상품을 취소승인을 하시겠습니까?
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "EI"){
			if(confirm(language_data['orders.goods_list.js']['M'][language])){//해당 주문상품을 교환승인을 하시겠습니까?
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "EC"){
			if(confirm('해당 주문상품을 교환배송완료처리 하시겠습니까?')){//language_data['orders.goods_list.js']['N'][language]
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "RI"){
			if(confirm(language_data['orders.goods_list.js']['O'][language])){//해당 주문상품을 반품승인처리 하시겠습니까?
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "RC"){
			if(confirm(language_data['orders.goods_list.js']['P'][language])){//해당 주문상품을 반품회수완료처리 하시겠습니까?
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "FA"){
			if(confirm(language_data['orders.goods_list.js']['Q'][language])){//해당 주문상품을 환불신청 처리 하시겠습니까?
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "FC"){
			if(confirm(language_data['orders.goods_list.js']['R'][language])){//해당 주문상품을 환불완료 처리 하시겠습니까?
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "OR"){
			if(confirm('해당 주문상품을 해외프로세싱중 처리 하시겠습니까?')){
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "TR"){
			if(confirm('해당 주문상품을 항공배송준비중 처리 하시겠습니까?')){
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "SO"){
			if(confirm('해당 주문상품을 품절취소 처리 하시겠습니까?')){
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "EF"){//여기서부터 체크
			if(confirm('해당 주문상품을 교환보류 처리 하시겠습니까?')){
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "ET"){
			if(confirm('해당 주문상품을 교환회수완료 처리 하시겠습니까?')){
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "RF"){
			if(confirm('해당 주문상품을 반품보류 처리 하시겠습니까?')){
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "DI"){
			if(confirm('해당 주문상품을 배송중 처리 하시겠습니까?')){
				window.frames["act"].location.href= 'orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}
}


function orderStatusUpdate(frm){
	//alert($(".od_ix_"+frm.oid.value).serializeArray());
	//var f    = document.createElement('form');
	var od_ix_str = '';
	$(".od_ix_"+frm.oid.value+":checked").each(function(){
		if(od_ix_str == ""){
			od_ix_str = $(this).val();
		}else{
			od_ix_str += ","+ $(this).val();
		}
		
	});
	frm.od_ix_str.value = od_ix_str;


	if(frm.od_ix_str.value == ''){
		alert(language_data['orders.goods_list.js']['W'][language]);//'배송완료 처리할 상품을 선택해주세요'
		return false;
	}

	if(frm.delivery_method.value == ''){
		alert('배송 방법을 선택해주세요.');
		frm.delivery_method.focus();
		return false;
	}

	if(frm.delivery_company.value == ''){
		alert(language_data['orders.goods_list.js']['T'][language]);//배송 업체를 선택해주세요
		frm.delivery_company.focus();
		return false;
	}

	if(frm.deliverycode.value.length < 1){
		alert(language_data['orders.goods_list.js']['U'][language]);//송장번호를 입력해주세요
		frm.deliverycode.focus();
		return false;
	}

	
	
	if(confirm(language_data['orders.goods_list.js']['V'][language])){//'선택된 주문상품을 배송처리 하시겠습니까?'
		return true;
	}else{
		return false;
	}
}

