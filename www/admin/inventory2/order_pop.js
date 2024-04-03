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

	if($('.order_cnt').length > 0){
		var order_cnt = 0;
		var is_order_cnt = 0;
		$('.order_cnt').each(function(){
			if($.trim($(this).val()) != ""){
				order_cnt = order_cnt + $(this).val();
				is_order_cnt++;
			}
		});
		
		var order_price = 0;
		var is_order_price_cnt = 0;
		$('.order_price').each(function(){
			if($.trim($(this).val()) != ""){
				order_price = order_price + $(this).val();
				is_order_price_cnt++;
			}
		});

		if(order_cnt == 0){
			alert('발주 하시고자 하는 항목의 발주수량을 하나 이상 입력해야 합니다.');
			return false;
		}

		if(is_order_cnt > is_order_price_cnt){
			alert('발주 단가를 입력하셔야 합니다.'+order_price+' :::: '+is_order_price_cnt);
			return false;
		}

		if(is_order_cnt < is_order_price_cnt){
			alert('발주 수량을 입력하셔야 합니다.');
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
		frm.target='';
		frm.submit();		
	}else{
		frm.mode.value = "insert";
		frm.target='act';
		frm.submit();
	}
}


function sumStock(defaultValue){
	var total_size = 0;
	
	frm = document.order_pop;
	//alert(defaultValue);
	//return;
	//alert(frm.input_size.length);
	
	for(var i=0;i<frm.order_cnt.length;i++){
		if(frm.order_cnt[i].value != ''){
			total_size = parseInt(total_size) + parseInt(frm.order_cnt[i].value);
		//frm.stock[i].value = parseInt(frm.input_size[i].value) + parseInt(defaultValue);
		//alert(total_size);
		}
	}
	
	
	//alert(parseInt(total_size,10));
	frm.total_inputstock.value = parseInt(total_size,10);
}

