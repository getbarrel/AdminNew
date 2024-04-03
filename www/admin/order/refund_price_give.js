
$(document).ready(function(){
	
	$('#cart_coupon_refund').click(function(){
		cart_coupon_refund($(this));
	});

	$('#cart_coupon_give').click(function(e){
		if( ! $('#cart_coupon_refund').is(':checked') ){
			e.preventDefault();
			//$('#cart_coupon_give').attr('checked',false);
		}
	});

	$('input[name^=tax_product_price]').keyup(function(){
		onlyNumberByRefund(this);
		tax_product_price_key($(this));
	});

	$('input[name^=tax_free_product_price]').keyup(function(){
        onlyNumberByRefund(this);
		tax_free_product_price_key($(this));
	});

	$('input[name^=delivery_price]').keyup(function(){
        onlyNumberByRefund(this);
		delivery_price_key($(this));
	});

	$('input[name^=refund_delivery_price]').keyup(function(){
        onlyNumberByRefund(this);
		refund_delivery_price_key($(this));
	});

	if($('#total_refund_tax_free_product_price').val()==0){
		$('#total_refund_tax_free_product_price').closest('tr').hide();
	}
});

var chkConfirm = true;
function refundCheck(frm){
	
	/* payco 자동 주문취소가 현재 동작하지 않는 관계로 자동처리 시 알럿 추가 JK1523*/
	//alert($('#direct_pg').is(":checked"))
	/*
	if($('#direct_pg').is(":checked") != true){
		alert('payco 승인취소는 가맹점 사이트에서 직접 처리바랍니다.');
		return;
	}
	*/
	
	
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}

	var sum_refund_price = 0;
	var total_refund_price = Number($('#total_refund_price').val());
	
	$('input[id^=refund_price_]').each(function(){
		sum_refund_price+=Number($(this).val());
	})

	if(total_refund_price!=sum_refund_price){
		alert("환불해줘야 하는 금액이 "+(total_refund_price > sum_refund_price ? (total_refund_price-sum_refund_price)+"원 적습니다." : (sum_refund_price - total_refund_price)+"원 많습니다."));
		return false;
	}else{
		if(confirm('해당 금액으로 환불해 주시겠습니까?')){
			if(chkConfirm){
				chkConfirm = false;
				return true;
            }else {
				return false;
			}
		}else{
			return false;
		}
	}
}

//-------과세상품관련-------//

//과세상품 가격 변경시 실행되는 함수
function tax_product_price_key(obj){
	check_tax_product_price(obj);
	check_metod_price(obj);
}

//총과세상품 가격비교후 금액 변경함수!
function check_tax_product_price(obj){
	var sumTaxProductPrice = 0;
	var totalRefundTaxProductPrice = 0;
	var remain_price = 0;

	//과세상품 환불합계와 비교
	sumTaxProductPrice = sum_tax_product_price(true);
	totalRefundTaxProductPrice = total_refund_tax_product_price();

	if(sumTaxProductPrice > totalRefundTaxProductPrice){
		remain_price = totalRefundTaxProductPrice - sum_tax_product_price(false,obj);
		obj.val(remain_price);
	}
}

//과세상품 입력된 금액 합계 및 자신을 제외한 금액 합계 가지고 오는 함수
function sum_tax_product_price(all,obj){
	var return_price=0;
	$('input[name^=tax_product_price]'+(all ? "" : ":not(#"+obj.attr('id')+")")).each(function(){
		return_price+=Number($(this).val());
	})
	return return_price;
}

//과세상품 환불총금액함수
function total_refund_tax_product_price(){
	var return_price=0;
	return_price = Number($('input#total_refund_tax_product_price').val());
	return return_price;
}



//-------비과세상품관련-------//

//비과세상품 가격 변경시 실행되는 함수
function tax_free_product_price_key(obj){
	check_tax_free_product_price(obj);
	check_metod_price(obj);
}

//총비과세상품 가격비교후 금액 변경함수!
function check_tax_free_product_price(obj){
	var sumTaxFreeProductPrice = 0;
	var totalRefundTaxFreeProductPrice = 0;
	var remain_price = 0;

	//과세상품 환불합계와 비교
	sumTaxFreeProductPrice = sum_tax_free_product_price(true);
	totalRefundTaxFreeProductPrice = total_refund_tax_free_product_price();

	if(sumTaxFreeProductPrice > totalRefundTaxFreeProductPrice){
		remain_price = totalRefundTaxFreeProductPrice - sum_tax_free_product_price(false,obj);
		obj.val(remain_price);
	}
}

//과세상품 입력된 금액 합계 및 자신을 제외한 금액 합계 가지고 오는 함수
function sum_tax_free_product_price(all,obj){
	var return_price = 0;
	$('input[name^=tax_free_product_price]'+(all ? "" : ":not(#"+obj.attr('id')+")")).each(function(){
		return_price+=Number($(this).val());
	});
	return return_price;
}

//과세상품 환불총금액함수
function total_refund_tax_free_product_price(){
	var return_price = 0;
	return_price = Number($('input#total_refund_tax_free_product_price').val());
	return return_price;
}



//-------배송비관련-------//

//배송비 변경시 실행되는 함수
function delivery_price_key(obj){
	check_delivery_price(obj);
	check_metod_price(obj);
}

//사용자 임의 배송비 변경시 실행되는 함수
function refund_delivery_price_key(obj){
	var sumRefundDeliveryPrice = 0;
	var totalRefundPrice = 0;
	$('input[name^=refund_delivery_price]').each(function(){
		sumRefundDeliveryPrice+=Number($(this).val());
	});

	$('#total_refund_delivery_price').val(sumRefundDeliveryPrice);

	$('#total_refund_tax_product_price,#total_refund_tax_free_product_price,#total_refund_delivery_price').each(function(){
		totalRefundPrice+=Number($(this).val());
	});
	
	$('#total_refund_price').val(totalRefundPrice);
}

//총비과세상품 가격비교후 금액 변경함수!
function check_delivery_price(obj){
	var sumDeliveryPrice = 0;
	var totalRefundDeliveryPrice = 0;
	var remain_price = 0;

	//과세상품 환불합계와 비교
	sumDeliveryPrice = sum_delivery_price(true);
	totalRefundDeliveryPrice = total_refund_delivery_price();

	//if(totalRefundDeliveryPrice > 0){
		if(sumDeliveryPrice > totalRefundDeliveryPrice){
			remain_price = totalRefundDeliveryPrice - sum_delivery_price(false,obj);
			obj.val(remain_price);
		}
	/*
	}else{
		obj.val(0);
	}
	*/
}

//과세상품 입력된 금액 합계 및 자신을 제외한 금액 합계 가지고 오는 함수
function sum_delivery_price(all,obj){
	var return_price = 0;
	$('input[name^=delivery_price]'+(all ? "" : ":not(#"+obj.attr('id')+")")).each(function(){
		return_price+=Number($(this).val());
	});
	return return_price;
}

//과세상품 환불총금액함수
function total_refund_delivery_price(){
	var return_price = 0;
	return_price = Number($('input#total_refund_delivery_price').val());
	return return_price;
}



//-------결제타입관련-------//

//결제타입에 따른 가격비교후 금액 변경함수!
function check_metod_price(obj){
	var sumMethodPrice = 0;
	var maxRefundMethodPrice = 0;
	var remain_price = 0;

	//결제타입에 따른 가격비교!
	sumMethodPrice = sum_method_price(true,obj);
	maxRefundMethodPrice = max_refund_method_price(obj);

	if(sumMethodPrice > maxRefundMethodPrice){
		remain_price = maxRefundMethodPrice - sum_method_price(false,obj);
		obj.val(remain_price);
	}
	
	//결제타입에 따른 총결제금액변경
	sumMethodPrice = sum_method_price(true,obj);
	$('input#refund_price_'+obj.attr('method')).val(sumMethodPrice);
}

//각 결제타입에 따라 입력된 금액 및 자신을 제외한 금액 합계 가지고 오는 함수
function sum_method_price(all,obj){
	var return_price = 0;
	$('input[method='+obj.attr('method')+']'+(all ? "" : ":not(#"+obj.attr('id')+")")).each(function(){
		return_price+=Number($(this).val());
	});
	return return_price;
}

//각 결제타입에 따라 MAX 금액 가지고 오는 함수
function max_refund_method_price(obj){
	var return_price = 0;
	return_price = Number($('input#refund_price_'+obj.attr('method')).attr("maxMethodPrice"));
	return return_price;
}


function cart_coupon_refund(obj){
	var cart_cupon_price = Number($('#tax_product_price_14').attr('data'));
	var cart_cupon_free_price = Number($('#tax_free_product_price_14').attr('data'));

	if(obj.is(':checked')){
		$('#tax_product_price_14').val(cart_cupon_price);
		$('#tax_free_product_price_14').val(cart_cupon_free_price);
		//$('#refund_price_14').val( Number(cart_cupon_price) + Number(cart_cupon_free_price) );

		$("input[id^=tax_product_price_]:not('#tax_product_price_14')").each(function(){
			var thisPrice = Number($(this).val());
			if(thisPrice - cart_cupon_price < 0){
				cart_cupon_price = cart_cupon_price - thisPrice;
				$(this).val(0);
			}else{
				$(this).val( thisPrice - cart_cupon_price );
				cart_cupon_price = 0;
			}
		});

		$("input[id^=tax_free_product_price_]:not('tax_free_product_price_14')").each(function(){
			var thisPrice = Number($(this).val());
			if(thisPrice - cart_cupon_free_price < 0){
				cart_cupon_free_price = cart_cupon_free_price - thisPrice;
				$(this).val(0);
			}else{
				$(this).val( thisPrice - cart_cupon_free_price );
				cart_cupon_free_price = 0;
			}
		});

	}else{
		$('input[method=14]').val(0);
		//$('#refund_price_14').val(0);
		$('#cart_coupon_give').attr('checked',false);
		
		
		var maxTaxPrice = Number($('#total_refund_tax_product_price').val());
		var maxFreePrice = Number($('#total_refund_tax_free_product_price').val());
		var maxDeliveryPrice = Number($('#total_refund_delivery_price').val());

		$("input[id^=refund_price_]:not('#refund_price_14')").each(function(){
			var method = $(this).attr('id').replace("refund_price_","");
			$('input[method='+method+']').each(function(){
				var maxmethodprice = Number($('#refund_price_'+$(this).attr('method')).attr('maxmethodprice'));

				if( $(this).attr('id') == 'tax_product_price_' + method ){
					if( maxmethodprice >= maxTaxPrice ){
						$(this).val( maxTaxPrice );
						maxmethodprice -= maxTaxPrice;
						maxTaxPrice = 0;
					}else{
						$(this).val( maxmethodprice );
						maxTaxPrice -= maxmethodprice;
						maxmethodprice = 0;
					}
				}
				
				if( $(this).attr('id') == 'tax_free_product_price_' + method ){
					if( maxmethodprice >= maxFreePrice ){
						$(this).val( maxFreePrice );
						maxmethodprice -= maxFreePrice;
						maxFreePrice = 0;
					}else{
						$(this).val( maxmethodprice );
						maxFreePrice -= maxmethodprice;
						maxmethodprice = 0;
					}
				}

				if( $(this).attr('id') == 'delivery_price_' + method ){
					if( maxmethodprice >= maxDeliveryPrice ){
						$(this).val( maxDeliveryPrice );
						maxmethodprice -= maxDeliveryPrice;
						maxDeliveryPrice = 0;
					}else{
						$(this).val( maxmethodprice );
						maxDeliveryPrice -= maxmethodprice;
						maxmethodprice = 0;
					}
				}
			})
		})

		/*
		var maxprice = Number($('#total_refund_tax_product_price').val());
		$("input[id^=tax_product_price_]:not('#tax_product_price_14')").each(function(){
			var maxmethodprice = Number($('#refund_price_'+$(this).attr('method')).attr('maxmethodprice'));
			if(maxmethodprice >= maxprice){
				$(this).val( maxprice );
				maxprice = 0;
			}else{
				$(this).val( maxmethodprice );
				maxprice -= maxmethodprice;
			}
		});
		
		var maxprice = Number($('#total_refund_tax_free_product_price').val());
		$("input[id^=tax_free_product_price_]:not('#tax_free_product_price_14')").each(function(){
			var maxmethodprice = Number($('#tax_free_product_price_'+$(this).attr('method')).attr('maxmethodprice'));
			if(maxmethodprice >= maxprice){
				$(this).val( maxprice );
				maxprice = 0;
			}else{
				$(this).val( maxmethodprice );
				maxprice -= maxmethodprice;
			}
		});
		
		var maxprice = Number($('#total_refund_delivery_price').val());
		$("input[id^=delivery_price_]:not('#delivery_price_14')").each(function(){
			var maxmethodprice = Number($('#delivery_price_'+$(this).attr('method')).attr('maxmethodprice'));
			if(maxmethodprice >= maxprice){
				$(this).val( maxprice );
				maxprice = 0;
			}else{
				$(this).val( maxmethodprice );
				maxprice -= maxmethodprice;
			}
		});
		*/
	}

	$('input[id^=refund_price_]').each(function(){
		var method = $(this).attr('id').replace("refund_price_","");
		var total_price = 0;
		$('input[method='+method+']').each(function(){
			total_price += Number($(this).val());
		})
		$(this).val(total_price);
	})
}

//고객사 cs환경 버그로 인해 admin.js에 있는걸 한번더 추가
function onlyNumberByRefund(obj){
    var str = obj.value;
    str = new String(str);
    var Re = /[^0-9\-.]/g;
    str = str.replace(Re,'');
    obj.value = str;
}