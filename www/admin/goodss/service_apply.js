var click_bank=false;
function click_method(cobj) {
	//alert(cobj.value);
	if(cobj.value!="bank") {
		$('#bankzone').css('display','none');
		$("input[name=Gubun_cd]").val("");
		$("select[name=incom_bank]").attr("validation","false");
		$("input[name=incom_name]").attr("validation","false");
	} else {
		$('#bankzone').css('display','');
		$("select[name=incom_bank]").attr("validation","true");
		$("input[name=incom_name]").attr("validation","true");
		/*
		receipt_view();
		if(!click_bank) {
			$('select[name=incom_bank]').selectbox();
			click_bank=true;
		}
		*/
	}
}