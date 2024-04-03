function act(act, code)
{

	if (act == "update")
	{
		var form = eval("document.EDIT_"+code);
		

		form.action = 'orders.act.php?act='+act+'&code='+code+'<?="&page=$page&ctgr=$ctgr&qstr=".rawurlencode($qstr)?>';
		form.submit();
	}

	if (act == "delete")
	{
		//'정말로 삭제하시겠습니까?'
		if(confirm(language_data['common']['G'][language]))
		{
			document.frames("act").location.href= 'orders.act.php?act='+act+'&code='+code+'<?="&page=$page&ctgr=$ctgr&qstr=".rawurlencode($qstr)?>';
		}
	}
}


function ViewdeliveryCodeInputBox(StatusCode,code){
	var form = eval("document.EDIT_"+code);
	if(StatusCode == 2){
		form.deliverycode.style.display = "block";
		form.quick.style.display = "block";
	}else{
		form.deliverycode.style.display = "none";
		form.quick.style.display = "none";
	}
	
	
}

function ReturnOK(oid){
	
	if(confirm(language_data['mail.target.js']['A'][language]))){
	//'정말로 반품 처리 하시겠습니까?'
		document.location.href='orders.act.php?act=stateupdate&oid='+oid;
	}else{
		
	}	
}


function init(FromDate,ToDate) {
	var frm = document.target_form;

/*	alert(FromDate);
	alert(FromDate.substring(0,4));
	alert(FromDate.substring(5,7));
	alert(FromDate.substring(8,10));
*/
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
	
	
	
	for(i=0; i<frm.ToYY2.length; i++) {
		if(frm.ToYY2.options[i].value == ToDate.substring(0,4))
			frm.ToYY2.options[i].selected=true
	}
	for(i=0; i<frm.ToMM2.length; i++) {
		if(frm.ToMM2.options[i].value == ToDate.substring(5,7))
			frm.ToMM2.options[i].selected=true
	}
	for(i=0; i<frm.ToDD2.length; i++) {
		if(frm.ToDD2.options[i].value == ToDate.substring(8,10))
			frm.ToDD2.options[i].selected=true
	}
	
	
	for(i=0; i<frm.FromYY2.length; i++) {
		if(frm.FromYY2.options[i].value == FromDate.substring(0,4))
			frm.FromYY2.options[i].selected=true
	}
	for(i=0; i<frm.FromMM2.length; i++) {
		if(frm.FromMM2.options[i].value == FromDate.substring(5,7))
			frm.FromMM2.options[i].selected=true
	}
	for(i=0; i<frm.FromDD2.length; i++) {
		if(frm.FromDD2.options[i].value == FromDate.substring(8,10))
			frm.FromDD2.options[i].selected=true
	}
	
	
	
	
	
}

function onLoad(FromDate, ToDate) {
	var frm = document.target_form;
	
	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	
	LoadValues(frm.FromYY2, frm.FromMM2, frm.FromDD2, FromDate);
	LoadValues(frm.ToYY2, frm.ToMM2, frm.ToDD2, ToDate);
	
	init(FromDate,ToDate);
	
}

function goSearch(frm,status){
	
	frm.status.value = status;
	frm.submit();
}