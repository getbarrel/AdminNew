
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

	var freegift_condition = $('input:radio[name=freegift_condition]:checked').val();
	freegift_condition_check(freegift_condition);

	$('input:radio[name=freegift_condition]').on('click',function(){
		var freegift_condition_select = $(this).val();
		freegift_condition_check(freegift_condition_select);
	})

	function freegift_condition_check(type){
		if(type == 'G' ){
			$('#freegift_condition_g').show();
			$('#freegift_condition_c').hide();
			$('#freegift_condition_p').hide();
		}else if(type == 'C'){
			$('#freegift_condition_g').hide();
			$('#freegift_condition_c').show();
			$('#freegift_condition_p').hide();
		}else if(type == 'P'){
			$('#freegift_condition_g').show();
			$('#freegift_condition_c').hide();
			$('#freegift_condition_p').show();
		}else{
			$('#freegift_condition_g').show();
			$('#freegift_condition_c').hide();
			$('#freegift_condition_p').hide();
		}
	}
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



function AddGiftGroup(objName){

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

//freegift_group[".($i+1)."][group_name]


	newRow.find("b[id^=freegift_group_title]").html("사은품 그룹  (GROUP "+(total_rows)+")");
	newRow.find("input[id^=group_name]").attr("name","freegift_group["+(total_rows)+"][group_name]");
	newRow.find("input[id^=group_name]").val("");
	//newRow.find("input[id^=ranking]").val(total_rows+1);

	newRow.find("input[id^=sale_condition_s]").attr("name","freegift_group["+(total_rows)+"][sale_condition_s]");
	newRow.find("input[id^=sale_condition_s]").val('');

	newRow.find("input[id^=sale_condition_e]").attr("name","freegift_group["+(total_rows)+"][sale_condition_e]");
	newRow.find("input[id^=sale_condition_e]").val('');

	newRow.find("input[id2^=event_amount_type_1]").attr("name","freegift_group["+(total_rows)+"][event_amount_type]");
	newRow.find("input[id2^=event_amount_type_2]").attr("name","freegift_group["+(total_rows)+"][event_amount_type]");
	newRow.find("input[id2^=event_amount_type_1]").attr("id","event_amount_type_"+(total_rows)+"_1");
	newRow.find("input[id2^=event_amount_type_2]").attr("id","event_amount_type_"+(total_rows)+"_2");
	newRow.find("label[id2^=label_event_amount_type_1]").attr("for","event_amount_type_"+(total_rows)+"_1");
	newRow.find("label[id2^=label_event_amount_type_2]").attr("for","event_amount_type_"+(total_rows)+"_2");

	newRow.find("input[id2^=is_display_y]").attr("name","freegift_group["+(total_rows)+"][is_display]");
	newRow.find("input[id2^=is_display_n]").attr("name","freegift_group["+(total_rows)+"][is_display]");
	newRow.find("input[id2^=is_display_y]").attr("id","is_display_"+(total_rows)+"_y");
	newRow.find("input[id2^=is_display_n]").attr("id","is_display_"+(total_rows)+"_n");
	newRow.find("label[id2^=label_is_display_y]").attr("for","is_display_"+(total_rows)+"_y");
	newRow.find("label[id2^=label_is_display_n]").attr("for","is_display_"+(total_rows)+"_n");



	newRow.find("input[id^=event_amount]").attr("name","freegift_group["+(total_rows)+"][event_amount]");
	newRow.find("input[id^=event_amount]").val('');

	newRow.find("input[id^=use_yn]").attr("name","freegift_group["+(total_rows)+"][use_yn]");
	newRow.find("input[id^=group_img]").attr("name","freegift_group["+(total_rows)+"][group_img]");
	newRow.find("input[id^=group_link]").attr("name","freegift_group["+(total_rows)+"][group_link]");
	newRow.find("input[id^=group_banner_img]").attr("name","freegift_group["+(total_rows)+"][group_banner_img]");

	newRow.find("input[id^=mgd_ix]").attr("name","freegift_group["+(total_rows)+"][display_type][mgd_ix][]");
	newRow.find("input[id^=type]").attr("name","freegift_group["+(total_rows)+"][display_type][type][]");
	newRow.find("input[id^=set_cnt]").attr("name","freegift_group["+(total_rows)+"][display_type][set_cnt][]");
	
	newRow.attr("group_code",""+(total_rows)+"");
	newRow.find("ul[name^=productList]").attr("id","productList_"+total_rows);
	newRow.find("div[id^=group_product_area_]").attr("id","group_product_area_"+(total_rows)+"");
	newRow.find("div[id^=goods_manual_area_]").attr("id","goods_manual_area_"+(total_rows)+"");
	newRow.find("a[name^=goods_display_type_]").attr("href","#goods_display_type_"+(total_rows)+"");
	newRow.find("a[name^=goods_display_type_]").attr("name","goods_display_type_"+(total_rows)+"");


	if(newRow.find("a[id^=btn_goods_search_add]").length > 0){
		newRow.find("a[id^=btn_goods_search_add]").get(0).onclick="";
		//newRow.find("a[id^=btn_goods_search_add]").attr("onclick","ms_productSearch.show_productSearchBox(event,"+total_rows+",'productList_'"+total_rows+");");
	}

	newRow.find("a[id^=btn_goods_search_add]").click(function(){
		ms_productSearch.show_productSearchBox(event,total_rows,'productList_'+total_rows,'clipart','77');
	});

	newRow.find("ul[name^=productList]").html("");
    newRow.find(".del_button").attr("onclick","del_table('group_info_area',"+(total_rows)+");");
    //newRow.find(".del_button").show();
	newRow.find("script[id^=setproduct]").remove();
	//alert(objName);
	newRow.appendTo($('#group_infos')); 
   
	//alert(newRow.html());  
	return newRow;
}

function del_table(className, num){
	$('.' + className + '[group_code='+num+']').remove();
}

//search.js 로 이동
/*
function SearchGoods(jq_obj, group_code){
	$("div#goods_manual_area_"+group_code+" div.goods_search_text").closest('li').css("font-weight","normal").css('border','1px solid #efefef');
	$("div#goods_manual_area_"+group_code+" div.goods_search_text:contains('"+jq_obj.parent().find("input#search_goods").val()+"')").closest('li').css("font-weight","bold").css('border','1px solid #000000');
}


function SearchGoodsDelete(jq_obj){
	jq_obj.closest('DIV[id^=goods_manual_area]').find('ul[name=productList]').remove();	
}
*/