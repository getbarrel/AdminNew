function searchGoodsFlow(delivery_company, invoice_no){
	//document.write('searchGoodsFlow.php?act=search&delivery_company='+delivery_company+'&invoice_no='+invoice_no);
	if(delivery_company != "" && invoice_no != ""){
		
		var f    = document.createElement('form');
		document.frames['iframe_act'].location.href = '/mypage/searchGoodsFlow.php?act=search&delivery_company='+delivery_company+'&invoice_no='+invoice_no;
	}else{
		alert(language_data['account.js']['A'][language]);//배송정보가 정확하지 않습니다. 
	}
}

function zipcode(id)
{
	var zip = window.open('zipcode.php?obj='+id,'','width=440,height=200,scrollbars=yes,status=no');
}


function ViewdeliveryCodeInputBox(StatusCode,frm){
	
	
	if(StatusCode == "DI"){
		document.getElementById("deliverycode").style.display = "inline";
		frm.quick.style.display = "inline";
	}else if(StatusCode == "EA"){
		
		document.getElementById("exchangeChoice").style.display = "inline";
		//alert('1');
		//frm.quick.style.display = "inline";
	}else{
		document.getElementById("deliverycode").style.display = "none";
		frm.quick.style.display = "none";
	}
	
	
}

function exchangeProduct(oid,id){
	
	document.exchange_form.action = "exchange_product.act.php?oid="+oid+"&id="+id;
	document.exchange_form.submit();
}

function exchange(oid,id,e_id){
	//alert('1');
	document.exchange_form.action = "exchange_product.php?oid="+oid+"&id="+id+"&e_pid="+e_id;
	
	document.exchange_form.submit();
}

function ReturnOK(oid){
	
	if(confirm(language_data['account.js']['B'][language])){//'정말로 반품 처리 하시겠습니까?'
		document.location.href='orders.act.php?act=stateupdate&oid='+oid;
	}else{
		
	}	
}

function PrintMemberOrder(oid){
	PrintWindow("./orders.print.php?oid="+oid,700,430,'OrderPrint');		
}

function PrintDealingsSheet(oid){
	PrintWindow("./orders.dealings.php?oid="+oid,700,430,'OrderPrint');		
}

function PrintOrderDetail(oid){
	PrintWindow("./orders.view.php?oid="+oid,700,430,'OrderPrint');		
}


function init_date(FromDate,ToDate) {
	var frm = document.search_frm;
	
	/*
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
	*/
	
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


function onLoad(FromDate, ToDate) {
	var frm = document.search_frm;
	
	
	//LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);
	
	
	
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
		for(i=0;i < frm.company_id.length;i++){
				frm.company_id[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.company_id.length;i++){
				frm.company_id[i].checked = true;
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
		alert(language_data['account.js']['C'][language]);//'배송중 상태의 경우는 주문정보수정 페이지에서 택배사및 송장번호를 입력하신후 수정하시기 바랍니다
		return false;
	}
	
	for(i=0;i < frm.oid.length;i++){		
		//alert(frm.oid[i]);
		if(frm.oid[i].checked && frm.oid[i].other_seller_cnt != ""){
			//alert(frm.oid[i].other_seller_cnt);
			if(frm.oid[i].other_seller_cnt == "0"){
				checked_bool = true	;				
			}else{
				//alert(frm.oid[i].value);
				//alert(frm.oid[i].value+":::"+document.getElementById("tr_"+frm.oid[i].value));
				document.getElementById("tr_"+frm.oid[i].value).style.backgroundColor = "pink";
				frm.oid[i].checked = false;
				checked_bool = false	;				
				other_seller_bool = true;
			}
		}
	}
	
	if(other_seller_bool){
		alert('입점업체 상품이 속해 있는경우 수정버튼을 클락하신후 수정페이지에서 상태 변경을 해주시기 바랍니다.');
		return false;
	}
	
	if(!checked_bool){
		alert('상태변경하실 주문을 한개이상 선택하셔야 합니다.');
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

		form.action = 'orders.act.php?act='+act+'&oid='+oid;
		form.submit();
	}

	if (act == "delete")
	{
		if(confirm('정말로 삭제하시겠습니까?'))
		{
			document.frames("act").location.href= 'orders.act.php?act='+act+'&oid='+oid;
		}
	}
}

function order_delete(act, oid){
	if (act == "delete")
	{
		if(confirm('[카드결제]의 경우는 승인취소후 삭제해주세요. 해당 주문을  정말로 삭제하시겠습니까?'))
		{
			document.frames("act").location.href= 'orders.act.php?act='+act+'&oid='+oid;
		}
	}
}

function detailView(value){
	if(value == "list"){
		document.location.href= 'orders.list.php';
	}else{
		document.location.href= 'orders.list_detail.php';
	}
}