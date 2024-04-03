 
$(document).ready(function () {
	//$("select[name^=options]").selectbox();	
	//$("select[class=codi_goods_options]").selectbox();
	//$("select[class=add_goods_options]").selectbox();
	//$("select[class=option_selectbox]").selectbox();
	//$("select[class=goods_options]").selectbox();
	window.name="mom";

});
function minicart_total(){
	var minicart_total = 0;
	var dcprice = 0;
	var option_price = 0;
	var amount = 0;
	$("table#minicart tr.order_detail_rows").each(function(){
		dcprice = parseInt($(this).find('input[minicart_id=dcprice]').val());
		option_price = parseInt($(this).find('input[minicart_id=option_price]').val());
		amount = parseInt($(this).find('input[minicart_id=amount]').val());
		minicart_total += parseInt(dcprice+option_price) * amount ;
		//minicart_total += parseInt($(this).find('input[minicart_id=sellprice]').val()) * parseInt($(this).find('input[minicart_id=amount]').val());
		//alert(minicart_total);
	});
	
	$('#minicart_total').html("<strong>"+FormatNumber2(minicart_total)+"</strong> <span class='size_12 main_color'> 원</span>");
}


function minicart_pcount_cnt(obj, plus_yn){
//	alert(obj.parent().parent().find('#order_amount').val());
	amount_obj = obj.parent().parent().find('input[minicart_id=amount]');
	var pcount = parseInt(amount_obj.val());
	

	if(plus_yn == "p"){
		amount_obj.val(pcount + 1);
	}
	if(plus_yn == "m"){
		if(pcount <= 1) {
			alert("수량은 1개 이상이어야 합니다.");
			return false;
		} else {
			amount_obj.val(pcount - 1);
		}
	}

	var order_amount = amount_obj.val();
	//alert(obj.closest('table[id=minicart_detail]').find('input[minicart_id=dcprice]').val()+":::"+obj.closest('table[id=minicart_detail]').find('input[minicart_id=option_price]').val());
	//var order_sellprice = obj.parent().parent().find('input[minicart_id=sellprice]').val();
	var order_sellprice = parseInt(obj.closest('table[id=minicart_detail]').find('input[minicart_id=dcprice]').val()) + parseInt(obj.closest('table[id=minicart_detail]').find('input[minicart_id=option_price]').val());
	
	
	//alert(obj.parent().parent().parent().find('#order_price').html());
	obj.closest('table[id=minicart_detail]').find('strong[minicart_id=total_price]').html(FormatNumber2(order_amount*order_sellprice));

	minicart_total();
	 
}

function option_pcount_cnt(obj, plus_yn, opnd_ix){
	var pcount = parseInt(obj.val());
	var goods_cnt_per_1box = $("#goods_cnt_per_1box").html(); // 한박스당 상품수
	
	//pcount = parseInt(pcount);
	if($('table#minicart').find('input[id^=box_option_pid_'+opnd_ix+']').is(":checked")){
		if(total_goods_cnt <= pcount) {
			alert("총 구매가능 수량을 초과하였습니다.");
			return false;
		}
	}

	if(plus_yn == "p"){
		obj.val(pcount + 1);
	}
	if(plus_yn == "m"){
		if(pcount <= 1) {
			alert("수량은 1개 이상이어야 합니다.");
			return false;
		} else {
			obj.val(pcount - 1);
		}
	}

	var box_count = $("#box_count").val(); // 구매 박스 수량
	
	var total_goods_cnt = parseInt(goods_cnt_per_1box*box_count);
	$('#total_goods_cnt').html(total_goods_cnt); // 구매할수 있는 상품 총 수량
	$('#box_total_cnt').html(box_count);
	//alert(goods_cnt_per_1box+':::'+box_count+'='+total_goods_cnt);
}

// 초이스 박스옵션 계산함수
function CalcurateBoxOption(obj){
	var opnd_ix = "";
	var selected_box_total = 0; // 선택된 상품수량 
	var goods_cnt_per_1box = $("#goods_cnt_per_1box").html(); // 한박스당 상품수
	var box_count = $("#box_count").val(); // 구매 박스 수량
	var total_goods_cnt = parseInt(goods_cnt_per_1box*box_count);
	$('#total_goods_cnt').html(total_goods_cnt); // 구매할수 있는 상품 총 수량
	$('#box_total_cnt').html(box_count);

	$('table#minicart').find('input[id^=box_option_pid_]').each(function(){
		//alert($(this).is(":checked"));
		if($(this).is(":checked")){
			opnd_ix = $(this).attr('opnd_ix');
			selected_box_total += parseInt($("input[id=pcount_"+opnd_ix).val());
		}
	});
	//alert(total_goods_cnt +"<"+ selected_box_total);
	if(total_goods_cnt < selected_box_total ){
		alert('총 구매가능 수량을 초과하였습니다.');
		var total_gap = selected_box_total - total_goods_cnt;
		//alert(obj.parent().parent().html());
		obj.parent().parent().find('input[id^=pcount]').val(obj.parent().parent().find('input[id^=pcount]').val()-total_gap);
		//obj.attr("checked",false);
	}else{
		//alert($("#selected_box_total").html());
		$("#selected_box_total").html(selected_box_total);
	//	alert((goods_cnt_per_1box));
		$("#remained_box_total").html((goods_cnt_per_1box*box_count) - selected_box_total);
	}
	//alert(selected_box_total);
}

function setPackage(jquery_obj){
	//alert(jquery_obj.is(":checked"));
	if(jquery_obj.is(":checked")){
		jquery_obj.parent().parent().parent().find('input[minicart_id=opnd_ix').val("");
		jquery_obj.parent().parent().parent().find('input[minicart_id=opnd_ix').each(function(){
			if($(this).attr('set_group') == jquery_obj.attr('set_group')){
				//alert($(this).attr('opnd_ix'));
				$(this).val($(this).attr('opnd_ix'));
				$(this).parent().find('input[minicart_id=deleteable]').val(0);
			} 
		});
	}else{
		jquery_obj.parent().parent().parent().find('input[minicart_id=opnd_ix').each(function(){
			if($(this).attr('set_group') == jquery_obj.attr('set_group')){
				//alert($(this).attr('opnd_ix'));
				var cart_ix = $(this).parent().find('input[minicart_id=cart_ix]').val();
				if(!cart_ix){
					$(this).val("");					
				}else{
					$(this).parent().find('input[minicart_id=deleteable]').val(1);					
				}
			} 
		});
	}
}
 
function minicart_delete(obj){
	//minicart_total += parseInt($(this).find('input[minicart_id=sellprice]').val()) * parseInt($(this).find('input[minicart_id=amount]').val());
	//ondblclick="minicart_delete($('table#minicart_detail tr[id=]'));minicart_total();"
	var basic_cnt = $('input[minicart_id=basic][value=1]').length;

	if(obj.find('input[minicart_id=basic]').val() == 1){
		basic_cnt = basic_cnt - 1;
	}
	//alert(basic_cnt);
	if(basic_cnt > 0){
		if($('table#minicart tr.order_detail_rows[delete=0]').length > 1){
			obj.find('input[minicart_id=basic]').val(0);
			obj.find('input[minicart_id=sellprice]').val(0);
			obj.find('input[minicart_id=amount]').val(0);
			obj.find('input[minicart_id=deleteable]').val(1);
			obj.css('display','none');
			obj.attr('delete','1');
		}else{
			$('table#minicart').hide();
			$('table#minicart_sum').hide();
		}
	}else{
		var minicart_cnt = $('table#minicart tr.order_detail_rows[delete=0]');
	
		$('table#minicart tr.order_detail_rows[delete=0]').each(function(i){			
			//if(minicart_cnt > 
			if($(this).find('input[minicart_id=basic]').val() == 1 && basic_cnt == 0){
				$(this).find('input[minicart_id=pid]').val("");
				$(this).find('input[minicart_id=basic]').val(0);
				$(this).find('input[minicart_id=sellprice]').val(0);
				$(this).find('input[minicart_id=amount]').val(0);
				$(this).find('input[minicart_id=deleteable]').val(1);
				$(this).css('display','none');
				$(this).attr('delete','1');
				$('table#minicart').hide();
				$('table#minicart_sum').hide();
			}else{
				$(this).find('input[minicart_id=pid]').val("");
				$(this).find('input[minicart_id=basic]').val(0);
				$(this).find('input[minicart_id=sellprice]').val(0);
				$(this).find('input[minicart_id=amount]').val(0);
				$(this).find('input[minicart_id=deleteable]').val(1);
				$(this).css('display','none');
				$(this).attr('delete','1');
			}
		});
	}
} 

function MoveSelectBox(obj, search_type, type,group_code){
	if(type == 'ADD'){
		//alert(obj.attr('id'));
			$('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code+' option:selected').each(function(){
				$('DIV#'+obj.attr('id')+' SELECT#selected_result_'+group_code).append("<option value="+$(this).val()+" selected>"+$(this).html()+"</option>"); //ondblclick=\"MoveSelectBox( $('DIV#"+obj.attr('id')+"'), '"+search_type+"','ADD','"+group_code+"');\" 
				var selected_value = $(this).val();
				$('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code+' option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
	}else{
			$('DIV#'+obj.attr('id')+' SELECT#selected_result_'+group_code).dblclick(function(){
				MoveSelectBox( $('DIV#'+obj.attr('id')), search_type,'REMOVE',group_code);
			});
			$('DIV#'+obj.attr('id')+' SELECT#selected_result_'+group_code+' option:selected').each(function(){
				$('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code).append("<option value="+$(this).val()+" selected>"+$(this).html()+"</option>");//ondblclick=\"MoveSelectBox( $('DIV#"+obj.attr('id')+"'), '"+search_type+"','REMOVE','"+group_code+"');\" 
				var selected_value = $(this).val();
				$('DIV#'+obj.attr('id')+' SELECT#selected_result_'+group_code+' option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}else{
						$(this).attr('selected', 'selected');
					}
				});
			});
	}
}


function SearchInfoMulti(search_type, obj, group_code){
	//alert(search_type);
	if(search_type == 'C'){
		var act = 'search_category';
	}else if(search_type == 'G'){
		var act = 'search_group';
	}else if(search_type == 'M'){
		var act = 'search_member';
	}else if(search_type == 'B'){
		var act = 'search_brand';
	}else if(search_type == 'S'){
		var act = 'search_seller';
	}else{
		alert('선택된 정보가 올바르지 않습니다. 확인후 다시시도해주세요');
		return;
	}
		//alert(search_type+':::'+act+':::'+group_code+':::search_text:::'+obj.attr('id'));
		$.ajax({ 
			type: 'GET', 
			data: {'act': act, 'search_type':search_type, 'search_text':obj.val()},
			url: '../search.act.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				//alert(2)
			},  
			success: function(datas){ 
				//alert(datas);
				$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' #search_result_'+group_code+' option').each(function(){
					$(this).remove();
				});
				$.each(datas, function() {
					 //alert(this['brand_name']);
					 $('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#search_result_'+group_code).append("<option value="+this['value']+"  >"+this['text']+"</option>");//ondblclick=\"MoveSelectBoxMulti( $('DIV#goods_auto_area_"+group_code+" DIV#"+obj.attr('id')+"'), '"+search_type+"','ADD',"+group_code+");\"
					 //append('<option value="'+this['value']+'" ondblclick="$(this).remove();">'+this['text']+'</option>');
					// alert(this.age);
				});
			} 
		}); 
 
}

function MoveSelectBoxMulti(obj, search_type, type,group_code){
	if(type == 'ADD'){
			$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#search_result_'+group_code+' option:selected').each(function(){
				 
				$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#selected_result_'+group_code).append("<option value="+$(this).val()+" selected>"+$(this).html()+"</option>"); //ondblclick=\"MoveSelectBoxMulti( $('DIV#goods_auto_area_"+group_code+" DIV#"+obj.attr('id')+"'), '"+search_type+"','ADD','"+group_code+"');\" 
				//append('<option value='+$(this).val()+' ondblclick="$(this).remove();" selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#search_result_'+group_code+' option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
	}else{
			$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#selected_result_'+group_code+' option:selected').each(function(){
				$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#search_result_'+group_code).append("<option value="+$(this).val()+" selected>"+$(this).html()+"</option>");//ondblclick=\"MoveSelectBoxMulti( $('DIV#goods_auto_area_"+group_code+" DIV#"+obj.attr('id')+"'), 'C','REMOVE','"+group_code+"');\" 
				//append('<option value='+$(this).val()+' ondblclick="$(this).remove();" selected>'+$(this).html()+'</option>');
				var selected_value = $(this).val();
				$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#selected_result_'+group_code+' option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
	}
}


function SelectedAll(jquery_obj, selected){
	$(jquery_obj).each(function(){
		$(this).attr('selected', selected);
	});
}




function SearchGoods(jq_obj, group_code){
	$("div#goods_manual_area_"+group_code+" div.goods_search_text").closest('li').css("font-weight","normal").css('border','1px solid #efefef');
	$("div#goods_manual_area_"+group_code+" div.goods_search_text:contains('"+jq_obj.parent().find("input#search_goods").val()+"')").closest('li').css("font-weight","bold").css('border','1px solid #000000');
}


function SearchGoodsDelete(jq_obj){
	jq_obj.closest('DIV[id^=goods_manual_area]').find('ul[name=productList]').remove();	
}


function SelectGoodsOption(frm){	 

	if($('table#minicart tr.order_detail_rows[delete=0]').length == 0){
		for(i=0;i < frm.elements.length;i++){
			if(!CheckForm(frm.elements[i])){
				return false;
			}
		}
	}

	if($('table#minicart tr.order_detail_rows').length > 0){
		var pid = frm.id.value;
		var pname = frm.pname.value;
		var set_group = "";
		
		var basic_option_cnt = $('table#minicart tr.order_detail_rows').find("input[minicart_id=option_kind][value=b]").length;	
		var add_option_cnt = $('table#minicart tr.order_detail_rows').find("input[minicart_id=option_kind][value=a]").length;	
		if($("select.goods_options[option_kind!=a]").length > 0 ){
			if(add_option_cnt > 0 && basic_option_cnt == 0){
				alert("기본상품을 한개이상 구매하셔야 합니다.");
				return false;
			}
		}

		if($('#option_kind').val() == "x" || $('#option_kind').val() == "c" || $('#option_kind').val() == "x2" || $('#option_kind').val() == "s2" || add_option_cnt > 0){
			if($("input[name=set_group]").length > 0 && $("input[name=set_group]").val() != ""){
				//set_group = $("input[name=set_group]").val();
			}else{
				//set_group = getCartGoodsGroupNo(pid);
			}
		}

		if($('#option_kind').val() == "x"){
			var box_count = $('#box_count').val();
			var goods_cnt_per_1box = parseInt($('#goods_cnt_per_1box').html());
			var total_pcount = 0;
			$('table#minicart tr.order_detail_rows').each(function(){
				if($(this).find('input[minicart_id=opnd_ix]').is(":checked")){
					total_pcount += parseInt($(this).find('input[minicart_id=amount]').val());
				}
			});

			if((goods_cnt_per_1box*box_count) > total_pcount){
				alert(((goods_cnt_per_1box*box_count) - total_pcount )+" 개의 수량을 더 구매하셔야 합니다. ");
				return false;
			}
		}
		
		var data = {};
		data['pid']=pid;
		data['pname']=pname;
		data['options'] = new Array();

		$('table#minicart tr.order_detail_rows').each(function(j){
			
			var error_cnt = 0;
			var success_cnt = 0;
			var opnd_ix = "";
			if($('#option_kind').val() == "c"){
				var cart_ix = $(this).find('select[minicart_id=opnd_ix] option:selected').attr('cart_ix');
			}else{
				var cart_ix = $(this).find('input[minicart_id=cart_ix]').val();
			}

			if($(this).find('input[minicart_id=opnd_ix]').attr('type') == "checkbox"){	
				if($(this).find('input[minicart_id=opnd_ix]').attr('checked') == "checked"){	
					opnd_ix = $(this).find('input[minicart_id=opnd_ix]').val();
				}
			}else if($(this).find('select[minicart_id=opnd_ix]').length > 0){	//코디상품일때 
				opnd_ix = $(this).find('select[minicart_id=opnd_ix]').val();
			}else{

				opnd_ix = $(this).find('input[minicart_id=opnd_ix]').val();
			}

			if(opnd_ix != undefined && opnd_ix != ''){

				var set_count = 0;

				if($('#option_kind').val() == "x2" || $('#option_kind').val() == "s2" ){
					var pcount = parseInt($(this).find('input[minicart_id=set_cnt]').val()) * parseInt($(this).parent().parent().parent().find('input[minicart_id=amount]').val());
	
					set_count = parseInt($(this).parent().parent().parent().find('input[minicart_id=amount]').val());
				}else if($('#option_kind').val() == "c"){
					var pcount = parseInt($("table#minicart").find('input[minicart_id=pcount]').val());
					set_count = pcount;
				}else if($('#option_kind').val() == "x"){
					var pcount = $(this).find('input[minicart_id=amount]').val();
					set_count = parseInt($(this).closest('table[class=choice_box]').find('input[id^=box_count]').val());
				}else{
					var pcount = $(this).find('input[minicart_id=amount]').val();
				}
				data['options'][j] = {"opnd_ix_array":opnd_ix,"pcount":pcount,"option_text":$(this).find("span[minicart_id=pname_text]").text(),"delivery_package":$('#delivery_package').val()};
			}
		});

		if(window.dialogArguments){
			var opener = window.dialogArguments;
		}else{
			var opener = window.opener;
		}

		opener.takeOpionData(data);
		//alert(data);

	}else{
		alert('옵션을 선택해주세요.');
		return false;
	}

	self.close();
}