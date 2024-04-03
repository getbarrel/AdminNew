function searchGoodsFlow(delivery_company, invoice_no){
	//document.write('searchGoodsFlow.php?act=search&delivery_company='+delivery_company+'&invoice_no='+invoice_no);
	if(delivery_company != "" && invoice_no != ""){
		
		var f    = document.createElement('form');
		window.frames['iframe_act'].location.href = '/mypage/searchGoodsFlow.php?act=search&delivery_company='+delivery_company+'&invoice_no='+invoice_no;
	}else{
		alert(language_data['orders.js']['C'][language]);//배송정보가 정확하지 않습니다. 
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
	
	
	/*if(StatusCode == "DI"){
		
		document.getElementById("deliverycode").style.display = "inline";
		document.getElementsByName("quick")[0].style.display = "inline";
	}else{
		document.getElementById("deliverycode").style.display = "none";
		
		document.getElementsByName("quick")[0].style.display = "none";
	}*/
	
	
}

function ReturnOK(oid){
	
	if(confirm(language_data['orders.js']['A'][language])){//정말로 반품 처리 하시겠습니까?
		document.location.href='service_orders.act.php?act=stateupdate&oid='+oid;
	}else{
		
	}	
}

function PrintMemberOrder(oid){
	PrintWindow("./service_orders.print.php?oid="+oid,700,430,'OrderPrint');		
}

function PrintDealingsSheet(oid){
	PrintWindow("./service_orders.dealings.php?oid="+oid,700,430,'OrderPrint');		
}

function PrintOrderDetail(oid){
	PrintWindow("./service_orders.view.php?oid="+oid,700,430,'OrderPrint');		
}


function init_date(FromDate,ToDate,FromDate2,ToDate2) {
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

	for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate2.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate2.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate2.substring(8,10))
			frm.FromDD.options[i].selected=true
	}
	
	
	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate2.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate2.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate2.substring(8,10))
			frm.ToDD.options[i].selected=true
	}
	
}

function select_date(FromDate,ToDate,dType) {
	var frm = document.search_frm;
	
	if(dType == 1){
		for(i=0; i<frm.FromYY.length; i++) {
			if(frm.FromYY.options[i].value == FromDate.substring(0,4))
				frm.FromYY.options[i].selected=true
		}
		for(i=0; i<frm.FromMM.length; i++) {
			if(frm.FromMM.options[i].value == FromDate.substring(5,7))
				frm.FromMM.options[i].selected=true
		}
		for(i=0; i<frm.FromDD.length; i++) {
			if(frm.FromDD.options[i].value == FromDate.substring(8,10))
				frm.FromDD.options[i].selected=true
		}
		
		
		for(i=0; i<frm.ToYY.length; i++) {
			if(frm.ToYY.options[i].value == ToDate.substring(0,4))
				frm.ToYY.options[i].selected=true
		}
		for(i=0; i<frm.ToMM.length; i++) {
			if(frm.ToMM.options[i].value == ToDate.substring(5,7))
				frm.ToMM.options[i].selected=true
		}
		for(i=0; i<frm.ToDD.length; i++) {
			if(frm.ToDD.options[i].value == ToDate.substring(8,10))
				frm.ToDD.options[i].selected=true
		}
	}else{
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
	
}


function onLoad(FromDate, ToDate,FromDate2, ToDate2, frm) {
	if(!frm){
		var frm = document.search_frm;
	}
	
	
	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate2);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate2);
	
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;

	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;
	
	init_date(FromDate,ToDate,FromDate2, ToDate2);
	
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

function ChangeEndDate(frm){
	if(frm.enddate.checked){
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
	}else{
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
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


function listAction(frm){
		
	PoPWindow('../sms.pop.php',450,300,'sendsms');
	frm.action = '../sms.pop.php';
	frm.target = 'sendsms';
	frm.submit();
}


function CheckStatusUpdate(frm){
	var checked_bool = false;
	var other_seller_bool = false;
	if(frm.status.value == 'DI'){
		alert(language_data['orders.js']['D'][language]);//배송중 상태의 경우는 주문정보수정 페이지에서 택배사및 송장번호를 입력하신후 수정하시기 바랍니다
		return false;
	}
	if(frm.status.value == "IR"){
		alert(language_data['orders.js']['E'][language]);
		return false;
	}
	for(i=0;i < frm.oid.length;i++){	
		if(frm.oid[i].checked){
			var od_status = eval("document.all.od_status_"+frm.oid[i].value.replace("-",""));
			checked_bool = true	;			
			for(j=1;j < od_status.length;j++){
				if(frm.status.value == od_status[j].value){
					alert(language_data['orders.js']['F'][language]);//현재상태와 변경을 원하시는 상태가 같은 주문이 한개이상 있으면 상태 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.
					return false;
				}else if(frm.status.value == "IC"){
					if(od_status[j].value != 'IR'){	
						alert(language_data['orders.js']['G'][language]);//입금예정 상태가 아닌 주문이 한개이상 포함되어 있으면  입금 확인으로 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.
						return false;
					}
				}else if(frm.status.value == "DR"){
					if(od_status[j].value != 'IC'){	
						alert(language_data['orders.js']['H'][language]);//입금확인 상태가 아닌 주문이 한개이상 포함되어 있으면  배송준비중 으로 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.
						return false;
					}					
				}
			}
			
		}
	}
	
	if(!checked_bool){
		alert(language_data['orders.js']['J'][language]);//상태변경하실 주문을 한개이상 선택하셔야 합니다
		return false;
	}else{
		return true;
	}
}



function act(act, oid)
{
	
	if (act == "update")
	{
		var form = eval("document.EDIT_"+oid);

		form.action = 'service_orders.act.php?act='+act+'&oid='+oid;
		form.submit();
	}

	if (act == "delete")
	{
		if(confirm(language_data['orders.js']['I'][language])){//카테고리를 선택해주세요
			window.frames["iframe_act"].location.href= 'service_orders.act.php?act='+act+'&oid='+oid;
		}
	}
}

function order_delete(act, oid){
	if (act == "delete")
	{
		if(confirm(language_data['orders.js']['B'][language])){//[카드결제]의 경우는 승인취소후 삭제해주세요. 해당 주문을  정말로 삭제하시겠습니까?
			window.frames["iframe_act"].location.href= 'service_orders.act.php?act='+act+'&oid='+oid;
		}
	}
}

function orderStatusUpdate(frm){
	
	if(frm.status.value.length < 1){
		alert(language_data['orders.js']['N'][language]);//'상태정보를 선택해주세요'
		return false;
	}

	var equal_cnt = 0;
	var not_equal_cnt = 0;
	$('.od_ix').each(function(){
		//alert($(this).is(":checked"));
		if($(this).is(":checked")){
			if($(this).attr("od_status") == frm.status.value){
				equal_cnt++;
			}else{
				not_equal_cnt++;
			}
		}
	});

	if(equal_cnt > 0){
		alert('변경하시고자 하는 상태와 현재상태가 같은 주문이 있습니다. 확인후 다시 시도해주세요');
		return false;
	}



	if(frm.status.value == "DC"){
		var di_cnt = 0;
		var not_di_cnt = 0;
		$('.od_ix').each(function(){
			//alert($(this).is(":checked"));
			if($(this).is(":checked")){
				if($(this).attr("od_status") == "DI"){
					di_cnt++;
				}else{
					not_di_cnt++;
				}
			}
		});

		if(di_cnt == 0 && not_di_cnt == 0){
			alert('변경하시고자 하는 주문정보가 하나이상 선택되어야 합니다.');
			return false;
		}

		if(not_di_cnt > 0){
			alert('배송중인 상품만 배송완료 처리 가능합니다.');
			return false;
		}
	}

	if(frm.status.value == "DI"){
		if(frm.quick.value == ''){
			alert(language_data['orders.js']['K'][language]);//배송 업체를 선택해주세요
			frm.quick.focus();
			return false;
		}
		if(frm.deliverycode.value.length < 1){
			alert(language_data['orders.js']['L'][language]);//송장번호를 입력해주세요
			frm.deliverycode.focus();
			return false;
		}
	}
	
	if(confirm(language_data['orders.js']['M'][language].replace('_STATUS_',frm.status.options[frm.status.selectedIndex].text))){ 
		return true;
	}else{
		return false;
	}
}

function loadService(sel,target) {
	
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	
	var depth = sel.getAttribute('depth');
//	document.write('service_div.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
	window.frames['iframe_act'].location.href = 'service_div.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	

}