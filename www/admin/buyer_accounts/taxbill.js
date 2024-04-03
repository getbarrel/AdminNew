
function CheckStatusUpdate(frm){
	var checked_bool = false;

	if(frm.act.value == 'order_taxbill' || frm.act.value == 'preiod_ready'){
		for(i=0;i < frm.oid.length;i++){
			if(frm.oid[i].checked){
				checked_bool = true;
			}
		}
	}else if (frm.act.value == 'preiod_taxbill'){
		for(i=0;i < frm.uid.length;i++){
			if(frm.uid[i].checked){
				checked_bool = true;
			}
		}
	}

	if(!checked_bool){
		alert('한개 이상 선택하셔야합니다.');
		return false;
	}else{
		return true;
	}
}

function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#start_datepicker').addClass('point_color');
		$('#end_datepicker').addClass('point_color');
	}else{
		$('#start_datepicker').removeClass('point_color');
		$('#end_datepicker').removeClass('point_color');
	}
}

function tax_compute(index,total_price){
	//alert(total_price);
	$("input[name='coprice["+index+"]']").val(Math.ceil(total_price/1.1));
	$("input[name='tax["+index+"]']").val(total_price-Math.ceil(total_price/1.1));
}

function tax_compute2(index,date,total_price){
	//alert(total_price);
	$("input[name='coprice["+index+"]["+date+"]']").val(Math.ceil(total_price/1.1));
	$("input[name='tax["+index+"]["+date+"]']").val(total_price-Math.ceil(total_price/1.1));
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


function clearAll2(frm){
		for(i=0;i < frm.uid.length;i++){
				frm.uid[i].checked = false;
		}
}

function checkAll2(frm){
       	for(i=0;i < frm.uid.length;i++){
				frm.uid[i].checked = true;
		}
}

function fixAll2(frm){
	if (!frm.all_fix.checked){
		clearAll2(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll2(frm);
		frm.all_fix.checked = true;
	}
}


function clearAll3(frm){
		for(i=0;i < frm.oid.length;i++){
				frm.oid[i].checked = false;
		}
}

function checkAll3(frm){
       	for(i=0;i < frm.oid.length;i++){
				frm.oid[i].checked = true;
		}
}

function fixAll3(frm){
	if (!frm.all_fix.checked){
		clearAll3(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll3(frm);
		frm.all_fix.checked = true;
	}
}