

function caculateBuyingServicePrice(obj)
{
	var frm = obj.form;
	
	var orgin_price = filterNum(frm.orgin_price.value);
	if(orgin_price == ""){orgin_price = 0;}
	var exchange_rate = filterNum(frm.exchange_rate.value);
	if(exchange_rate == ""){exchange_rate = 0;}
	var air_wt = filterNum(frm.air_wt.value);
	if(air_wt == ""){air_wt = 0;}
	var air_shipping = filterNum(frm.air_shipping.value);
	if(air_shipping == ""){air_shipping = 0;}
	var duty = filterNum(frm.duty.value);
	if(duty == ""){duty = 0;}
	var clearance_fee = parseFloat(filterNum(frm.clearance_fee.value));
	if(clearance_fee == ""){clearance_fee = 0;}
	var bs_fee_rate = parseFloat(filterNum(frm.bs_fee_rate.value));
	if(bs_fee_rate == ""){bs_fee_rate = 0;}
	var bs_fee = parseFloat(filterNum(frm.bs_fee.value));
	if(bs_fee == ""){bs_fee = 0;}
	var supertax = 0;
	
	if(obj.name == "air_wt"){
		if(air_wt > 1){
			//alert(bs_basic_air_shipping+":::"+(air_wt-1)*bs_add_air_shipping);
			air_shipping = parseFloat(bs_basic_air_shipping) + parseFloat((air_wt-1)*bs_add_air_shipping);
		}else{
			air_shipping = bs_basic_air_shipping;
		}
		frm.air_shipping.value = air_shipping;
		var duty_value = (parseFloat(orgin_price)+parseFloat(air_shipping))*parseFloat(exchange_rate);
		
		if(frm.clearance_type[0].checked){
			duty = 0;
			supertax = 0;
			clearance_fee = 0;
		}else{			
			duty = Round2(duty_value*duty_rate/100,1,1);
			supertax = Round2((duty_value+duty)*bs_supertax_rate/100,1,1);
		}
		
		frm.duty.value = duty+supertax;
		frm.coprice.value = Round2(parseFloat(duty_value) + parseFloat(duty)+ parseFloat(supertax) + clearance_fee,0,1);
		
	}else if(obj.name == "bs_fee_rate"){
		var duty_value = (parseFloat(orgin_price)+parseFloat(air_shipping))*parseFloat(exchange_rate);
		if(frm.clearance_type[0].checked){
			duty = 0;
			supertax = 0;
			clearance_fee = 0;
		}else{			
			duty = Round2(duty_value*duty_rate/100,1,1);
			supertax = Round2((duty_value+duty)*bs_supertax_rate/100,1,1);
		}
		//frm.duty.value = duty+supertax;
		
		frm.bs_fee.value = Round2(parseFloat(bs_fee_rate/100) * (parseFloat(duty_value) + parseFloat(duty)+ parseFloat(supertax) + parseFloat(clearance_fee)),1,1) ;
		frm.coprice.value = Round2(parseFloat(duty_value) + parseFloat(duty) + clearance_fee,0,1);
	}else if(obj.name == "bs_fee"){
		var duty_value = (parseFloat(orgin_price)+parseFloat(air_shipping))*parseFloat(exchange_rate);
		if(frm.clearance_type[0].checked){
			duty = 0;
			supertax = 0;
			clearance_fee = 0;
		}else{
			duty = Round2(duty_value*duty_rate/100,1,1);
			supertax = Round2((duty_value+duty)*bs_supertax_rate/100,1,1);		
		}
		frm.bs_fee_rate.value = Round2(parseFloat(bs_fee)*100/(parseFloat(duty_value) + parseFloat(duty) + parseFloat(supertax) + parseFloat(clearance_fee)),1,"F") ;
		frm.coprice.value = Round2(parseFloat(duty_value) + parseFloat(duty) + clearance_fee,0,1);
	}else if(obj.name == "clearance_type"){
		var duty_value = (parseFloat(orgin_price)+parseFloat(air_shipping))*parseFloat(exchange_rate);
		if(frm.clearance_type[0].checked){
			duty = 0;
			supertax = 0;
			clearance_fee = 0;
		}else{			
			duty = Round2(duty_value*duty_rate/100,1,1);
			supertax = Round2((duty_value+duty)*bs_supertax_rate/100,1,1);
			clearance_fee = bs_clearance_fee;
		}
		
		//alert(frm.clearance_type[0].checked+":::"+(frm.clearance_type[0].checked == "1")+":::"+(duty+supertax));
		frm.duty.value = duty+supertax;
		frm.clearance_fee.value = clearance_fee;
		frm.bs_fee_rate.value = Round2(parseFloat(bs_fee)*100/(parseFloat(duty_value) + parseFloat(duty) + parseFloat(supertax) + parseFloat(clearance_fee)),1,"F") ;
		frm.coprice.value = Round2(parseFloat(duty_value) + parseFloat(duty)+ + parseFloat(supertax) + parseFloat(clearance_fee),0,1);
		
		//alert(obj.value);
	}
	
}
