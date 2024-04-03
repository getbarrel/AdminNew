
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
	if(frm.regdate.checked){
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


