function StockSubmit(frm,mode){

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	
	if($('#pi_ix').val() == ""){
		alert('보관장소를 선택해주세요');
		return false;
	}

	//alert($('.input_cnt').length);
	if($('.input_cnt').length > 0){
		var input_cnt = 0;
		var is_input_cnt = 0;
		$('.input_cnt').each(function(){
			if($.trim($(this).val()) != ""){
				input_cnt = input_cnt + $(this).val();
				is_input_cnt++;
			}
		});
		
		var input_price = 0;
		var is_input_price_cnt = 0;
		$('.input_price').each(function(){
			if($.trim($(this).val()) != ""){
				input_price = input_price + $(this).val();
				is_input_price_cnt++;
			}
		});

		if(input_cnt == 0){
			alert('입고 하시고자 하는 항목의 입고수량을 하나 이상 입력해야 합니다.');
			return false;
		}

		if(is_input_cnt > is_input_price_cnt){
			alert('입고 단가를 입력하셔야 합니다.');
			return false;
		}

		if(is_input_cnt < is_input_price_cnt){
			alert('입고 수량을 입력하셔야 합니다.');
			return false;
		}
	}

	/*
	if(frm.input_msg.value == ''){
		alert('내용을 입력해주세요');
		return false;
	}
	*/

	if(mode == "insert"){
		frm.mode.value = mode;
		//frm.target='act';
		frm.submit();		
	}else{
		frm.mode.value = "insert";
		//frm.target='act';
		frm.submit();
	}
}
function StockSubmit2(frm,mode)
{
	/*if(frm.inventory_info.value == ''){
		alert('창고를 선택해주세요');
		return false;
	}
	if(frm.company_info.value == ''){
		alert('입고처를 선택해주세요');
		return false;
	}
	if(frm.input_msg.value == ''){
		alert('내용을 입력해주세요');
		return false;
	}*/
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	
	i

	if(mode == "insert"){
		frm.mode.value = mode;
		frm.target='';
		frm.submit();		
	}else{
		frm.mode.value = mode;
		frm.target='act';
		frm.submit();
	}
}
function inventorySelect2(id,code){
	//alert("1");
	document.input_pop.action = "input_pop.php?id="+id+"&i_ix="+code+"";
	document.input_pop.submit();
}

function sumStock_backup(op_id){

	var input_cnt = document.getElementById("input_cnt_"+op_id).value;
	var stock = document.getElementById("stock_"+op_id).value;
	var safestcok = document.getElementById("safestock_"+op_id).value;
	
	document.getElementById("stock_"+op_id).value = parseInt(stock) + parseInt(input_cnt);

	//alert(parseInt(total_size,10));
	//frm.total_inputstock.value = parseInt(total_size,10);
}


function sumStock(defaultValue){
	var total_size = 0;
	
	frm = document.input_pop;
	//alert(defaultValue);
	//return;
	//alert(frm.input_cnt.length);
	$('.input_cnt').each(function(){
		if($(this).val() != ""){
			total_size = parseInt(total_size) + parseInt($(this).val());
		}
	});
	/*
	for(var i=0;i<frm.input_cnt.length;i++){
		if(frm.input_cnt[i].value != ''){
			total_size = parseInt(total_size) + parseInt(frm.input_cnt[i].value);
		//frm.stock[i].value = parseInt(frm.input_cnt[i].value) + parseInt(defaultValue);
		//alert(total_size);
		}
	}
	*/
	
	
	//alert(parseInt(total_size,10));
	frm.total_inputstock.value = parseInt(total_size,10);
}


function sumStock2(){
	var total_safesize = 0;
	
	frm = document.input_pop;
	//alert(frm.value[0]);
	//return;
	for(var i=0;i<frm.safestock.length;i++){
		total_safesize = parseInt(total_safesize) + parseInt(frm.safestock[i].value);
		//alert(total_size);
	}
	//alert(parseInt(total_size,10));
	frm.total_safestock.value = parseInt(total_safesize,10);
}

function inventoryChange(obj,i_ix){
	if(obj.checked == true){
		document.getElementById('inventory_info').disabled = false;
		document.getElementById('insert_inventory_info').innerHTML = "";
	}else{
		document.getElementById('inventory_info').disabled = true;
		document.getElementById('insert_inventory_info').innerHTML = "<input type='hidden' name='inventory_info' value="+i_ix+">";
	}
}