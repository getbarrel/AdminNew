function FormatNumber2(num){
  
	fl=''
	if(isNaN(num)) { /*alert('문자는 사용할 수 없습니다.');*/return 0}
	if(num==0) return num
	
	if(num<0){ 
			num=num*(-1)
			fl='-'
	}else{
			num=num*1 //처음 입력값이 0부터 시작할때 이것을 제거한다.
	}
	num = new String(num)
	temp=''
	co=3
	num_len=num.length
	while (num_len>0){
			num_len=num_len-co
			if(num_len<0){co=num_len+co;num_len=0}
			temp=','+num.substr(num_len,co)+temp
	}
	return fl+temp.substr(1)
}

function FormatNumber(num){
	num=new String(num)
	num=num.replace(/,/gi,'')
	//pricecheckmode = false;
	
	return FormatNumber2(num)
}

var NOW_SELECT_OD_IX;
	
$(document).ready(function(){

	if($('input[name=send_yn][type=radio]').length > 0){
		send_yn_click($('input[name=send_yn][type=radio]:checked'));
		send_type_click($('input[name=send_type][type=radio]:checked'));
		if($('input[name=send_type][type=radio]:checked').val()=="1"){
			same_addr(false);
		}else{
			same_addr(true);
		}
	}else{
		send_yn_click($('input[name=send_yn]'));
		send_type_click($('input[name=send_type]'));
	}

	$('input[name=send_yn][type=radio]').click(function(){
		send_yn_click($(this));
	});

	$('input[name=send_type][type=radio]').click(function(){
		send_type_click($(this));

		if($(this).val()=="1"){
			same_addr(false);
		}else{
			same_addr(true);
		}

	});
	/*
	$('#same_addr').click(function(){
		same_addr($(this));
	});
	*/
	$('.different_option_click').click(function(){
		NOW_SELECT_OD_IX = $(this).attr('od_ix');
		var result = ShowModalWindow('../goods_option_select.php?pid='+$(this).attr('pid')+'&delivery_package='+$(this).attr('delivery_package'),800,700,'product_search',true);
	});

	$('.different_product_click').click(function(){
		NOW_SELECT_OD_IX = $(this).attr('od_ix');
		var result = ShowModalWindow('../product_search.php?company_id='+$(this).attr('company_id')+'&surtax_yorn='+$(this).attr('surtax_yorn'),800,700,'product_search',true);

		json_result = JSON.parse(result);
		//alert(result);
		select_different_product($(this),json_result);
	});
	
	$('.same_product_click').click(function(){
		NOW_SELECT_OD_IX = $(this).attr('od_ix');
		select_same_product($(this));
	});

	$('#total_apply_delivery_price').keyup(function (){
		change_total_apply_delivery_price($(this));
	});
		
})

function send_yn_click(obj){
	if(obj.val()=="Y"){
		$('.send_yn_n').hide();
		$('.send_yn_y').show();
	}else{
		$('.send_yn_y').hide();
		$('.send_yn_n').show();
	}
}

function send_type_click(obj){
	if(obj.val()=="1"){
		$('.send_type_2').hide();
		$('.send_type_1').show();
	}else{
		$('.send_type_1').hide();
		$('.send_type_2').show();
	}
}

function same_addr(check){
	//alert(check);
	if(check){
		/*
		$('.table_addr').find('input').each(function(){
			$('input[name=return_'+$(this).attr('name')+'][type=text]').val($(this).val());
		})
		*/

		$('.table_return_addr').find('input[type=text]').each(function(){
			$(this).val($(this).attr('user'));
		})
	}else{
		$('.table_return_addr').find('input[type=text]').each(function(){
			$(this).val($(this).attr('com'));
		})
	}
}

function select_different_product(obj,data){

	var tmp = {};
	tmp['select_type']='D';
	tmp['pid']=data['pid'];
	tmp['pname']=data['pname'];
	tmp['opd_id']='';
	tmp['option_text']='';
	tmp['cnt']=obj.attr('pcnt');
	tmp['delivery_package']=data['delivery_package'];

	if(DeliveryPackageCheck(tmp['delivery_package'])){
		ClaimeAddGoods(tmp);
	}
}

function select_same_product(obj){

	var tmp = {};
	tmp['select_type']='S';
	tmp['pid']=obj.attr('pid');
	tmp['pname']=obj.attr('pname');
	tmp['opd_id']='';
	tmp['option_text']=obj.attr('option_text');
	tmp['cnt']=obj.attr('pcnt');
	tmp['delivery_package']=obj.attr('delivery_package');

	if(DeliveryPackageCheck(tmp['delivery_package'])){
		ClaimeAddGoods(tmp);
	}
}

function zipcode(type) {
	var zip = window.open('../member/zipcode.php?type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function del_estd_ix(estd_ix){
	
	if(!estd_ix){
		alert('삭제할 정보가 없습니다.');
		return false;
	}
	if(confirm('해당 상품정보를 삭제하겟습니가?')){
		$.ajax({ 
			type: 'GET', 
			data: {
					'act': 'delete_estd_ix',
					'estd_ix' : estd_ix
					},
			url: './estimate.act.php',
			dataType: 'html',
			async: true, 
			beforeSend: function(){ 
				//alert(2)
			},
			success: function(datas){ 
				window.location.reload();
			}
		});
	
	}else{
		return false;
	}

}

function takeOpionData(data){
	
	var est_ix = $('input[name=est_ix]').val();
	var mem_ix = $('input[name=ucode]').val();

	$.ajax({ 
		type: 'GET', 
		data: {
				'act': 'insert_estimate_detail',
				'estimate_detail':data,
				'est_ix' : est_ix,
				'mem_ix' : mem_ix
				},
		url: './estimate.act.php',
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
			//alert(2)
		},
		success: function(datas){ 
			if(datas == 'Y'){
				window.location.reload();
			}else{
				alert('일시품절중인 상품입니다.');
			}
		}
	});


	/*
	for (i=0; i < data['options'].length ;i++)
	{
		tmp['opd_id']=data['options'][i]['opnd_ix_array'];
		tmp['option_text']=data['options'][i]['option_text'];
		tmp['cnt']=data['options'][i]['pcount'];
		tmp['delivery_package']=data['options'][i]['delivery_package'];
		
		if(DeliveryPackageCheck(tmp['delivery_package'])){
			ClaimeAddGoods(tmp);
		}
	}
	*/
}



function ClaimeAddGoods(data){
	
	if($("#ea_product_table_"+NOW_SELECT_OD_IX+" tr").length == 1){
		$("#ea_product_no_select_"+NOW_SELECT_OD_IX+"").html("<td align='center' height='35'><input type='hidden' id='delivery_package' value='"+data['delivery_package']+"' /><b>상품명</b></td><td align='center' height='35'><b>옵션</b></td><td align='center' height='35'><b>수량</b></td><td align='center' height='35'><b>기타</b></td>");
	}

	//ec_product_select_type (D:다른상품, S:같은상품)
	var html = " <tr style='border-top:1px solid #c5c5c5;' height='50'> ";
	html += "		<td style='line-height:130%;padding:0px 10px;' width='40%'>";
	html += "			<input type='hidden' name='ec_product_select_type["+ NOW_SELECT_OD_IX +"][]' value='"+ data['select_type'] +"' />";
	html += "			<input type='hidden' name='ec_product_pid["+ NOW_SELECT_OD_IX +"][]' value='"+ data['pid'] +"' />";
	html += "			<input type='hidden' name='ec_product_opd_id["+ NOW_SELECT_OD_IX +"][]' value='"+ data['opd_id'] +"' />";
	//html += "			<input type='hidden' name='ec_product_cnt["+ NOW_SELECT_OD_IX +"][]' value='"+ data['cnt'] +"' />";
	html += "			<b>"+ data['pname'] +"</b>";
	html += "		</td>";
	html += "		<td style='line-height:130%;padding:0px 10px;' width='30%'>"+ data['option_text'] +"</td>";
	if(data['select_type']=='S'){
		html += "	<td align='center' width='20%'>교환요청수량과동일</td>";
	}else{
		html += "	<td align='center' width='20%'><input type='text' size='3' class='textbox numeric' name='ec_product_cnt["+ NOW_SELECT_OD_IX +"][]' value='"+ data['cnt'] +"' value='"+ data['cnt'] +"' /></td>";
	}
	html += "		<td align='center' width='10%'><img src='../images/i_close.gif' align='absmiddle' style='cursor:pointer;margin:5px;' title='더블클릭시 해당 라인이 삭제 됩니다.' ondblclick=\"ClaimeDeleteGoods($(this));\"></td>";
	html += "	</tr>";

	$(html).appendTo("#ea_product_table_"+NOW_SELECT_OD_IX+"").find('.numeric').numeric();
	
}

function ClaimeDeleteGoods(obj){
	obj.closest('tr').remove();
	if($("#ea_product_table_"+NOW_SELECT_OD_IX+" tr").length == 1){
		$("#ea_product_no_select_"+NOW_SELECT_OD_IX+"").html("<td align='center' valign='middle' colspan='4' height='50'><select validation='true' title='교환할 상품' style='display:none;'><option></option></select>교환할 상품을 선택해 주세요.</td>");
	}
}

function DeliveryPackageCheck(delivery_package){
	var obj = $('#delivery_package');

	if(obj.length > 0){
		if(obj.val()=="Y"){
			alert("개별배송은 상품은 하나만 담을수 있습니다.");
			return false;
		}else{
			if(obj.val() == delivery_package){
				return true;
			}else{
				alert("묶음 상품과 개별배송 상품을 같이 담을수 없습니다.");
				return false;
			}
		}
	}else{
		return true;
	}

	
}


function change_total_apply_delivery_price(obj){

	var total_apply_product_price = parseInt(obj.attr('total_apply_product_price'));
	if(obj.val()==''){
		var total_apply_delivery_price = 0;
	}else{
		var total_apply_delivery_price = parseInt(obj.val());
	}
	var total_apply_tax_price = parseInt(obj.attr('total_apply_tax_price'));
	var delivery_dc_price = parseInt(obj.attr('delivery_dc_price'));
	var total_apply_price =  total_apply_product_price+total_apply_delivery_price;
	//alert(total_apply_tax_price);
	var gep = total_apply_delivery_price-delivery_dc_price;

	$('#total_apply_price').val(total_apply_price);
	//$('#total_apply_price_text').text(FormatNumber(Math.abs(total_apply_price)));
	$('#total_apply_price_text').text(FormatNumber((total_apply_price)));
	
	$('#total_apply_tax_price').val(total_apply_tax_price+gep);

	if(total_apply_price < 0){
		$('#payment_type').val('add');
		$('.payment_type_add').show();
		$('.payment_type_refund').hide();
	}else{
		$('#payment_type').val('refund');
		$('.payment_type_add').hide();
		$('.payment_type_refund').show();
	}
}