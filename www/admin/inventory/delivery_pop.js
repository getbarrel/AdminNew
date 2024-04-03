function StockSubmit(frm){
	
	var check_delivery_cnt = true;

	if($('#pi_ix').val() == "" || $('#pi_ix').val() == "0"){
		alert('출고 하려는 창고를 선택해주세요');
		return false;
	}
	/*
	if($('.tab_pi_ix').length == 0){
		alert('입력된 재고 정보나 출고창고가 없어 더이상 출고를 진행 할 수 없습니다. 확인 후 다시 진행해주세요.');
		self.close();
		return false;
	}*/
	

	if($('.delivery_cnt').length > 0){
		var delivery_cnt = 0;
		var is_delivery_cnt = 0;
		$('.delivery_cnt').each(function(){
			if($(this).val() != ""){
				delivery_cnt = delivery_cnt + $(this).val();
				is_delivery_cnt++;
			}
		});
		
		var delivery_price = 0;
		var is_delivery_price_cnt = 0;
		$('.delivery_price').each(function(){
			if($.trim($(this).val()) != ""){
				delivery_price = delivery_price + $(this).val();
				is_delivery_price_cnt++;
			}
		});



		if(delivery_cnt == 0){
			alert('출고하시고자 하는 항목의 출고수량을 하나 이상 입력해야 합니다.');
			return false;
		}


		if(is_delivery_cnt > is_delivery_price_cnt){
			alert('출고 단가를 입력하셔야 합니다.' );
			return false;
		}

		if(is_delivery_cnt < is_delivery_price_cnt){
			alert('출고 수량을 입력하셔야 합니다.');
			return false;
		}


		$('.gi_ix').each(function(){
			if($('#delivery_cnt_'+$(this).val()) != ""){
				if(parseInt($('#delivery_cnt_'+$(this).val()).val()) > parseInt($('#stock_'+$(this).val()).val())){				
					check_delivery_cnt = false;
				}
			}
		});
	

		if(!check_delivery_cnt){
			alert('현재 재고보다 출고 수량이 더 많습니다. 출고 수량을 확인해주세요');
			return false;
		}

	}else{
		if(parseInt($('#delivery_cnt').val()) > parseInt($('#stock').val())){				
				alert('현재 재고보다 출고 수량이 더 많습니다. 출고 수량을 확인해주세요');
			return false;
		}
	}

	if($('#delivery_type').val() == '1'){
		$('#move_pi_ix').attr('validation',true);		
		$('#output_saler').attr('validation',false);
	}else{
		$('#move_pi_ix').attr('validation',false);
		$('#output_saler').attr('validation',true);
		
	};

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	/*
	if($('#pi_ix').val() == ""){
		alert('보관장소를 선택해주세요');
		return false;
	}
	*/
	/*
	if(frm.input_msg.value == ''){
		alert('내용을 입력해주세요');
		return false;
	}
	*/

	return true;
}


function SelectDeliveryType(outtype){
	if(outtype == '1'){
		$('.move_warehouse_area').show();
		$('#move_pi_ix').attr('validation',true);		
		$('#d_ci_ix').attr('validation',false);		
		
		$('.default').hide();
	}else{
		$('.move_warehouse_area').hide();
		$('#move_pi_ix').attr('validation',false);
		$('#d_ci_ix').attr('validation',true);		
		$('.default').show();
	};
}
	

function sumStock(defaultValue){
	var total_size = 0;
	
	frm = document.output_pop;


	$('.delivery_cnt').each(function(){
		if($(this).val() != ""){
			total_size = total_size + parseInt($(this).val());
		}
	});
	
	
	//alert(parseInt(total_size,10));
	frm.total_delivery_stock.value = parseInt(total_size,10);
}
