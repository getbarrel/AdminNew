
$(document).ready(function () {
	$('.sortable').sortable();
	$('.add_type_choice li').click(function(){
		promotion_type_check_reset();
		var img_tag = $(this).find('img');
		//alert(img_tag.attr('src')+';;;'+img_tag.attr('src').indexOf('_on'));
		if(img_tag.attr('src').indexOf('_on') == -1){
			$(this).find('img').attr('src',img_tag.attr('src').replace('.png','_on.png'));
		}
	});
});

// 전시 구현시 사용할수 있음
function promotion_type_check_reset(){
	//img reset
	$('.add_type_choice li').find('img').each(function( i, element ){
		$(element).attr('src', $(element).attr('src').replace('_on.png', '.png') );
	})
	//checkbox reset
	$('.promotion_types').find('input').attr('checked','');
}

// 전시 구현시 사용할수 있음
function CopyDisplayType(jquery_obj, target_id, group_code){
	//alert(jquery_obj.html());
	var newObj = jquery_obj.clone(true).appendTo($('#'+target_id));

	newObj.find('div[class^=control_view]').css('display','');
	newObj.find('input[type^=hidden]').attr('disabled','');
	newObj.find('input[type^=hidden]').attr('disabled',false);
	newObj.find('select[class^=set_cnt]').attr('disabled','');
	newObj.find('select[class^=set_cnt]').attr('disabled',false);
	newObj.css('margin','0 10px 0 0');
	newObj.get(0).onclick='';
	newObj.attr('onclick','');
	if(newObj.find('img').attr('src').indexOf('_on') == -1){
		newObj.find('img').attr('src',newObj.find('img').attr('src').replace('.png','_on.png'));
	}
	newObj.find('img').dblclick(function(){
		$(this).parent().remove();
		DisplayCntCalcurate(group_code);
	});
	
	newObj.find('select[class^=set_cnt]').change(function(){
		DisplayCntCalcurate(group_code);
	});

	
	DisplayCntCalcurate(group_code);
	
	$('#'+target_id).sortable();
}

// 전시 구현시 사용할수 있음
function DisplayCntCalcurate(group_code){
	var product_cnt = 0;

	$('#display_type_area_'+group_code+' div.control_view').each(function(){
		//alert($(this).find('select[class^=set_cnt]').val()+':::'+$(this).find('select[class^=set_cnt]').attr('dt_goods_num'));
		product_cnt += $(this).find('select[class^=set_cnt]').val() * $(this).find('select[class^=set_cnt]').attr('dt_goods_num');
	});
	

	$('#product_cnt_'+group_code).val(product_cnt);
}

 function ChangeDisplaySubType(obj, group_code, selected_value){
	obj.parent().parent().find('div[class^=goods_display_type_area]').hide();
	$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+selected_value).show();
}
 
 
function ChangeDisplaySubTarget(obj, group_code, selected_value){
	obj.parent().parent().find('div[class^=display_sub_target_area]').hide();
	$('DIV#display_sub_target_area_'+selected_value).show();
}



function AddDiscountGroup(objName){

	var obj = $('#' + objName + '');  
   var total_rows = $('.' + objName + '').length+1;  
   //var rows = obj.find('tr[depth^=1]').length;  
  
   if($.browser.msie){
      var newRow = obj.clone(true);
   }else{
	 // var newRow = obj.find('tr[depth^=1]:last').clone(true).insertAfter(obj);  
	  var newRow = obj.clone(true);
   } 
	//newRow.find("div[id^=event_rank]").html(total_rows+1);  

//discount_group[".($i+1)."][group_name]


	newRow.find("b[id^=discount_group_title]").html("기획할인 상품그룹  (GROUP "+(total_rows)+")");
	newRow.find("a[id^=group_del]").show();
	
	newRow.find("input[id^=group_name]").attr("name","discount_group["+(total_rows)+"][group_name]");
	newRow.find("input[id^=group_name]").val("");
	//newRow.find("input[id^=ranking]").val(total_rows+1);

	newRow.find("input[id^=headoffice_rate]").attr("name","discount_group["+(total_rows)+"][headoffice_rate]");
	newRow.find("input[id^=headoffice_rate]").val('');

	newRow.find("input[id^=commission]").attr("name","discount_group["+(total_rows)+"][commission]");
	newRow.find("input[id^=commission]").val('');

	newRow.find("input[id^=seller_rate]").attr("name","discount_group["+(total_rows)+"][seller_rate]");
	newRow.find("input[id^=seller_rate]").val('');

	newRow.find("input[id^=sale_rate]").attr("name","discount_group["+(total_rows)+"][sale_rate]");
	newRow.find("input[id^=sale_rate]").val('');

	newRow.find("input[id^=discount_sale_type]").attr("name","discount_group["+(total_rows)+"][discount_sale_type]");
	newRow.find("input[id^=round_type]").attr("name","discount_group["+(total_rows)+"][round_type]"); 

	newRow.find("input[id2^=is_display_y]").attr("name","discount_group["+(total_rows)+"][is_display]");
	newRow.find("input[id2^=is_display_n]").attr("name","discount_group["+(total_rows)+"][is_display]");
	newRow.find("input[id2^=is_display_y]").attr("id","is_display_"+(total_rows)+"_y");
	newRow.find("input[id2^=is_display_n]").attr("id","is_display_"+(total_rows)+"_n");
	newRow.find("label[id2^=label_is_display_y]").attr("for","is_display_"+(total_rows)+"_y");
	newRow.find("label[id2^=label_is_display_n]").attr("for","is_display_"+(total_rows)+"_n");

    newRow.find("input[name$='[coupon_use_yn]']").attr("name","discount_group["+(total_rows)+"][coupon_use_yn]");

	newRow.find("input[id^=event_amount]").attr("name","discount_group["+(total_rows)+"][event_amount]");
	newRow.find("input[id^=event_amount]").val('');

	newRow.find("input[id^=use_yn]").attr("name","discount_group["+(total_rows)+"][use_yn]");
	newRow.find("input[id^=group_img]").attr("name","discount_group["+(total_rows)+"][group_img]");
	newRow.find("input[id^=group_link]").attr("name","discount_group["+(total_rows)+"][group_link]");
	newRow.find("input[id^=group_banner_img]").attr("name","discount_group["+(total_rows)+"][group_banner_img]");

	newRow.find("input[id^=mgd_ix]").attr("name","discount_group["+(total_rows)+"][display_type][mgd_ix][]");
	newRow.find("input[id^=type]").attr("name","discount_group["+(total_rows)+"][display_type][type][]");
	newRow.find("input[id^=set_cnt]").attr("name","discount_group["+(total_rows)+"][display_type][set_cnt][]");
	
	newRow.attr("group_code",""+(total_rows)+"");
	newRow.find("ul[name^=productList]").attr("id","productList_"+total_rows);
	newRow.find("div[id^=group_product_area_]").attr("id","group_product_area_"+(total_rows)+"");
	newRow.find("div[id^=goods_manual_area_]").attr("id","goods_manual_area_"+(total_rows)+"");
	newRow.find("div[id^=goods_auto_area_]").attr("id","goods_auto_area_"+(total_rows)+"");
	newRow.find("a[name^=goods_display_type_]").attr("href","#goods_display_type_"+(total_rows)+"");
	newRow.find("a[name^=goods_display_type_]").attr("name","goods_display_type_"+(total_rows)+"");

    newRow.find("input[name$='[goods_display_type]']").attr("name","discount_group["+(total_rows)+"][goods_display_type]");

	newRow.find("input[id^=discount_sale_type_1_1]").attr("id","discount_sale_type_"+(total_rows)+"_1");
	newRow.find("input[id^=discount_sale_type_1_2]").attr("id","discount_sale_type_"+(total_rows)+"_2");
	newRow.find("label[for^=discount_sale_type_1_1]").attr("for","discount_sale_type_"+(total_rows)+"_1");
	newRow.find("label[for^=discount_sale_type_1_2]").attr("for","discount_sale_type_"+(total_rows)+"_2");
	newRow.find("select[id^=round_position]").attr("name","discount_group["+(total_rows)+"][round_position]");
	newRow.find("select[id^=round_type]").attr("name","discount_group["+(total_rows)+"][round_type]");
	


	if(newRow.find("a[id^=btn_goods_search_add]").length > 0){
		newRow.find("a[id^=btn_goods_search_add]").get(0).onclick=""; 
		newRow.find("a[id^=btn_goods_search_add]").click(function(){
			ms_productSearch.show_productSearchBox(event,total_rows,'productList_'+total_rows,'clipart');
		});
	}

	




	

	

	/*카테고리 검색 부분*/
	// newRow.find("DIV#goods_display_sub_area_C input[id^=search_text]").keyup(function(){
	// 	//alert($(this).html());
	// 	SearchInfo('C',$('DIV#goods_auto_area_'+total_rows+' DIV#goods_display_sub_area_C'), total_rows);
	// });

	newRow.find("DIV#goods_display_sub_area_C a[id^=btn_selectbox_add]").attr("href","javascript:MoveSelectBox($('DIV#goods_auto_area_"+(total_rows)+" DIV#goods_display_sub_area_C'), 'C','ADD',"+total_rows+");");
	newRow.find("DIV#goods_display_sub_area_C a[id^=btn_selectbox_remove]").attr("href","javascript:MoveSelectBox($('DIV#goods_auto_area_"+(total_rows)+" DIV#goods_display_sub_area_C'), 'C','REMOVE',"+total_rows+");");

	

	if(newRow.find("DIV#goods_display_sub_area_C img[id^=btn_search_info]").length > 0){
		newRow.find("DIV#goods_display_sub_area_C img[id^=btn_search_info]").get(0).onclick="";
		newRow.find("DIV#goods_display_sub_area_C img[id^=btn_search_info]").click(function(){
			SearchInfo('C',$('DIV#goods_auto_area_'+total_rows+' DIV#goods_display_sub_area_C'), total_rows);
		});
	}

	if(newRow.find("DIV#goods_display_sub_area_C img[id^=btn_select_all]").length > 0){
		newRow.find("DIV#goods_display_sub_area_C img[id^=btn_select_all]").get(0).onclick="";
		newRow.find("DIV#goods_display_sub_area_C img[id^=btn_select_all]").click(function(){
			//SearchInfo('C',$('DIV#goods_auto_area_'+total_rows+' DIV#goods_display_sub_area_C'), total_rows);
			SelectedAll($('DIV#goods_display_sub_area_C #search_result_'+total_rows+' option'),'selected');
		});
	}


	newRow.find("DIV#goods_display_sub_area_C select[uid^=search_result]").attr("name","discount_group["+(total_rows)+"][category][]");
	newRow.find("DIV#goods_display_sub_area_C select[uid^=search_result]").attr("id","search_result_"+(total_rows));
	newRow.find("DIV#goods_display_sub_area_C select[uid^=search_result]").get(0).ondblclick="";
	newRow.find("DIV#goods_display_sub_area_C select[uid^=search_result]").dblclick(function(){
		//alert($(this).html());
		MoveSelectBox($('DIV#goods_auto_area_'+(total_rows)+' DIV#goods_display_sub_area_C'), 'C','ADD',total_rows); //ChangeDisplaySubType($(this), total_rows , $(this).val());
	});

	newRow.find("DIV#goods_display_sub_area_C select[uid^=selected_result]").attr("name","discount_group["+(total_rows)+"][category][]");
	newRow.find("DIV#goods_display_sub_area_C select[uid^=selected_result]").attr("id","selected_result_"+(total_rows));
	newRow.find("DIV#goods_display_sub_area_C select[uid^=selected_result]").get(0).ondblclick="";
	newRow.find("DIV#goods_display_sub_area_C select[uid^=selected_result]").dblclick(function(){
		//alert($(this).html());
		MoveSelectBox($('DIV#goods_auto_area_'+(total_rows)+' DIV#goods_display_sub_area_C'), 'C','REMOVE',total_rows); //ChangeDisplaySubType($(this), total_rows , $(this).val());
	});

	newRow.find("ul[name^=productList]").html("");
	newRow.find("script[id^=setproduct]").remove();
	//alert(objName);
	newRow.appendTo($('#group_infos')); 
   
	//alert(newRow.html());  
	return newRow;
}

function changeCommission(jobj){
	if($.isNumeric(headoffice_rate)){
		return false;
	}
	if($.isNumeric(seller_rate)){
		return false;
	}
	var headoffice_rate = $(jobj).closest('td').find('input[id=headoffice_rate]').val();
	if(headoffice_rate == ""){
		headoffice_rate = 0;
	}else{
		headoffice_rate = parseInt(headoffice_rate);
	}
	var seller_rate = $(jobj).closest('td').find('input[id=seller_rate]').val();
	if(seller_rate == ""){
		seller_rate = 0;
	}else{
		seller_rate = parseInt(seller_rate);
	}

	$(jobj).closest('td').find('input[id=sale_rate]').val(headoffice_rate+seller_rate);
	

}