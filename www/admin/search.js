
function SearchInfo(search_type, obj, group_code){
	//alert(search_type);
	
	if(search_type != 'G'){
		if(search_type == 'M'){
			if($(obj).find('input#search_text').val().length  < 2){
				alert('두글자 이상 입력하셔야 합니다.');
				return;
			}
		}else{
			if($(obj).find('input#search_text').val().length  < 2){
				//alert('두글자 이상 입력하셔야 합니다.');
				//return;
			}
		}
	}

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

		//alert(search_type+':::'+act+':::search_text:::'+$(obj).find('input#search_text').val());
		$.ajax({ 
			type: 'GET', 
			data: {'act': act, 'search_type':search_type, 'search_text':$(obj).find('input#search_text').val()},
			url: '../search.act.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				//alert(2)
			},  
			success: function(datas){ 
				//alert(datas);
				//alert('DIV#'+obj.attr('id')+' #search_result_'+group_code+' option');
				$('DIV#'+obj.attr('id')+' #search_result_'+group_code+' option').each(function(){					
					$(this).remove();
				});

				$('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code).unbind("dblclick");
				  $('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code).dblclick(function(){
						MoveSelectBox( $('DIV#'+obj.attr('id')), search_type,'ADD',group_code);
						//ondblclick=\"MoveSelectBox($('DIV#goods_auto_area_".($i+1)." DIV#goods_display_sub_area_B'), 'B','ADD',".($i+1).");\"  
				  });

				$.each(datas, function() {
					 //alert(this['text']);
					 $('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code).append("<option value="+this['value']+" >"+this['text']+"</option>"); //ondblclick=\"MoveSelectBox( $('DIV#"+obj.attr('id')+"'), '"+search_type+"','ADD','"+group_code+"');\" 
					 //append("<option value="'+this['value']+'" ondblclick=\"MoveSelectBox( $('DIV#"+obj.attr('id')+"), '"+search_type+"','ADD','category');\">'+this['text']+'</option>');
					// alert(this.age);
				});
			} 
		}); 
 
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
	
	if(search_type != 'G'){
		if(search_type == 'M'){
			if(obj.val().length  < 2){
				alert('두글자 이상 입력하셔야 합니다.');
				return;
			}
		}else{
			if(obj.val().length  < 2){
				//alert('두글자 이상 입력하셔야 합니다.');
				//return;
			}
		}
	}

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
	jq_obj.closest('DIV[id^=goods_manual_area]').find('ul[name=productList] li').remove();	
}
