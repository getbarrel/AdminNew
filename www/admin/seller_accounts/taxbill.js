/*

function tax_compute2(index,date,total_price){
	//alert(total_price);
	$("input[name='coprice["+index+"]["+date+"]']").val(Math.ceil(total_price/1.1));
	$("input[name='tax["+index+"]["+date+"]']").val(total_price-Math.ceil(total_price/1.1));
}
*/

function CheckStatusUpdate(frm){
	var checked_bool = false;

	if(frm.act.value == 'inversely_taxbill'){
		for(i=0;i < frm.ar_ix.length;i++){
			if(frm.ar_ix[i].checked){
				checked_bool = true;
			}
		}
	}
	/*
	else if (frm.act.value == 'preiod_taxbill'){
		for(i=0;i < frm.uid.length;i++){
			if(frm.uid[i].checked){
				checked_bool = true;
			}
		}
	}
	*/

	if(!checked_bool){
		alert('한개 이상 선택하셔야합니다.');
		return false;
	}else{
		return true;
	}
}

function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
	}
}

function clearAll(frm){
		for(i=0;i < frm.ar_ix.length;i++){
				frm.ar_ix[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.ar_ix.length;i++){
				frm.ar_ix[i].checked = true;
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
