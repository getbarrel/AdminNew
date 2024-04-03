function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	var depth = sel.depth;
	//if(depth == 2){
	//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	//}
	//alert(target);
	dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	
}
function zipcode(type) {
	var zip = window.open('../member/zipcode.php?type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}
function idsearch() {
	var zip = window.open('./estimate.idsearch.php','','width=640,height=300,scrollbars=yes,status=no');
}
	function isEQ()	{
		
		var form = document.form;

		if (form.same[0].checked)
		{
			form.name_b.value = form.name_a.value;
			//form.mail_b.value = form.mail_a.value;
			form.zipcode1_b.value = form.zipcode1.value;
			form.zipcode2_b.value = form.zipcode2.value;
			form.addr1_b.value = form.addr1.value;
			form.addr2_b.value = form.addr2.value;
			form.tel1_b.value = form.tel1_a.value;
			form.tel2_b.value = form.tel2_a.value;
			form.tel3_b.value = form.tel3_a.value;
			form.pcs1_b.value = form.pcs1_a.value;
			form.pcs2_b.value = form.pcs2_a.value;
			form.pcs3_b.value = form.pcs3_a.value;
			
		}
		else
		{
			form.name_b.value = '';
			//form.mail_b.value = '';
			form.zipcode1_b.value = '';
			form.zipcode2_b.value = '';
			form.addr1_b.value = '';
			form.addr2_b.value = '';
			form.tel1_b.value = '';
			form.tel2_b.value = '';
			form.tel3_b.value = '';
			form.pcs1_b.value = '';
			form.pcs2_b.value = '';
			form.pcs3_b.value = '';
		}
	}
function isEQ_sc()	{
		
	var form1 = document.form;
	var form2 = document.sc_info;

	if (form.same_sc[0].checked)
	{
		form.sc_damdang.value = form.name_a.value;
		form.sc_mail.value = form.mail_a.value;
		form.sc_tel1.value = form.tel1_a.value;
		form.sc_tel2.value = form.tel2_a.value;
		form.sc_tel3.value = form.tel3_a.value;
		form.sc_pcs1.value = form.pcs1_a.value;
		form.sc_pcs2.value = form.pcs2_a.value;
		form.sc_pcs3.value = form.pcs3_a.value;
		
	}
	else
	{
		form.sc_damdang.value = form2.sc_damdang.value;
		form.sc_mail.value = form2.sc_mail.value;
		form.sc_tel1.value = form2.sc_damdang_tel1.value;
		form.sc_tel2.value = form2.sc_damdang_tel2.value;
		form.sc_tel3.value = form2.sc_damdang_tel3.value;
		form.sc_pcs1.value = form2.sc_damdang_pcs1.value;
		form.sc_pcs2.value = form2.sc_damdang_pcs2.value;
		form.sc_pcs3.value = form2.sc_damdang_pcs3.value;
	}
}

function input_text(){
	if($("#msg2").attr("rel") == "first"){
		$("#msg2").val("");
		$("#msg2").attr("rel","");
	}
}

function receiptChoice(clickType){

	if(clickType == "1"){
		document.getElementById('receipt_result1').style.display = "none";
		document.getElementById('receipt_result2').style.display = "";
		document.getElementById('receipt_result3').style.display = "";
		document.getElementById('receipt_result1_1').style.display = "";
		document.getElementById('com_num_info').style.display = "";
			document.getElementById('receipt_result_non').style.display = "none";
	}

	if(clickType == "2"){
		//alert(document.form.payment_div[0].checked);
			document.getElementById('receipt_result1').style.display = "none";
			document.getElementById('receipt_result2').style.display = "none";
			document.getElementById('receipt_result3').style.display = "none";
			document.getElementById('receipt_result1_1').style.display = "";
			document.getElementById('com_num_info').style.display = "";
			document.getElementById('receipt_result_non').style.display = "none";
		
		$(".valid").each(function(){
			$(this).attr('validation','false');
		})
	}

	if(clickType == "3"){

		document.getElementById('receipt_result1').style.display = "none";
		document.getElementById('receipt_result2').style.display = "none";
		document.getElementById('receipt_result3').style.display = "none";
		document.getElementById('receipt_result1_1').style.display = "none";
		document.getElementById('com_num_info').style.display = "none";
		document.getElementById('receipt_result_non').style.display = "block";
		$(".valid").each(function(){
			$(this).attr('validation','false');
		})
	}
}

