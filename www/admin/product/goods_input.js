// 파이어폭스에서 insertAdjacentHTML 를 사용가능하게 하는 구문
if(typeof HTMLElement!="undefined" && !
HTMLElement.prototype.insertAdjacentElement){
    HTMLElement.prototype.insertAdjacentElement = function
(where,parsedNode)
    {
        switch (where){
        case 'beforeBegin':
            this.parentNode.insertBefore(parsedNode,this)
            break;
        case 'afterBegin':
            this.insertBefore(parsedNode,this.firstChild);
            break;
        case 'beforeEnd':
            this.appendChild(parsedNode);
            break;
        case 'afterEnd':
            if (this.nextSibling) 
this.parentNode.insertBefore(parsedNode,this.nextSibling);
            else this.parentNode.appendChild(parsedNode);
            break;
        }
    }

    HTMLElement.prototype.insertAdjacentHTML = function
(where,htmlStr)
    {
        var r = this.ownerDocument.createRange();
        r.setStartBefore(this);
        var parsedHTML = r.createContextualFragment(htmlStr);
        this.insertAdjacentElement(where,parsedHTML)
    }


    HTMLElement.prototype.insertAdjacentText = function
(where,txtStr)
    {
        var parsedText = document.createTextNode(txtStr)
        this.insertAdjacentElement(where,parsedText)
    }
}
// 파이어폭스에서 insertAdjacentHTML 를 사용가능하게 하는 구문


$(document).ready(function() {
	if($('#stock_use_y').is(":checked")){
		$('#stock').click(function(evt){
			//alert(evt.keyCode);
			alert('재고관리 사용시는 재고 수정을 임의로 하실 수 없습니다. 재고관리 입고, 출고 하기를 이용하시기 바랍니다..');
			//if (evt.keyCode==13)
			//	work_tmp();
		});
	
		$('.options_price_stock_option_stock').live('click',function(evt){
			//alert('재고관리 사용시는 재고 수정을 임의로 하실 수 없습니다. 재고관리 입고, 출고 하기를 이용하시기 바랍니다..');
		});
	}

    var product_type = $('input[name=product_type]:checked').val();
	if(product_type == '77'){
		$('#add_info_title').text('가격정보');
	}
	$('input[name^=product_type]').click(function(){
        var product_type = $(this).val();
        if(product_type == '77'){
            $('#add_info_title').text('가격정보');
        }else{
            $('#add_info_title').text('색상명');
		}
	});
});

function stockCheck(vtype){
	var product_type = $('input[name=product_type]:checked').val();

	if(vtype == "Y"){
        $('#requeired_img').css('display','inline-block');
		//재고 관리 사용시 class stock_readonly 를 추가함 2013-07-04 hjy
		$('.stock_readonly').attr('readonly',true);
		$('.stock_readonly').css('backgroundColor','#efefef');

		$('#stock').attr('readonly',true);
		//$('#stock').val('');
		$('#safestock').attr('readonly',true);
		//$('#safestock').val('');
		$('#stock').css('backgroundColor','#efefef');
		if($('#stock_use_y').is(":checked")){
			/*
			$('#stock').click(function(evt){
				alert('재고관리 사용시는 재고 수정을 임의로 하실 수 없습니다. 재고관리 입고, 출고 하기를 이용하시기 바랍니다..');
			});
			*/
			/*
			$('.options_price_stock_option_stock').live('click',function(evt){
				alert('재고관리 사용시는 재고 수정을 임의로 하실 수 없습니다. 재고관리 입고, 출고 하기를 이용하시기 바랍니다..');
			});
			*/
			/*$('.options_price_stock_option_stock').each(function(){
				//alert(1);

				$(this).live('click',function(evt){
					alert('재고관리 사용시는 재고 수정을 임의로 하실 수 없습니다. 재고관리 입고, 출고 하기를 이용하시기 바랍니다..');
				});

			});*/
			
			$('.options_price_stock_option_stock').css('backgroundColor','#efefef');
			$('.options_price_stock_option_stock').attr('readonly',true);
		}

		$('.stockinfo_loade').show();
		
		if(product_type == '0'){
			$('.auto_option_create').show();
			$('.make_option').show();
			$('.make_option_btn').show();

			$("#price_option_zone").css('display','block');
			$("#no_price_option_zone").css('display','none');
			//document.getElementById("no_price_option_zone").style.display = 'none';
			
			$('input[name=stock]').attr('validation',false);		//WMS 사용시 재고는 필수값 아님 2014-09-24 이학봉

			$('#basic_option_zone').hide();
			$('#basic_options_div_tab').hide();
			$('#options_div_tab').show();
			$('#stock_option_zone').show();
		}

		$("input[name='auto_sync_wms']").attr('disabled',false);
	}else{
		//재고 관리 사용시 class stock_readonly 를 추가함 2013-07-04 hjy
		$('.stock_readonly').attr('readonly',false);
		$('.stock_readonly').css('backgroundColor','#ffffff');

		$('#stock').attr('readonly',false);
		$('#safestock').attr('readonly',false);
		$('#stock').css('backgroundColor','#ffffff');
		$('#stock').unbind('click');
		$('.options_price_stock_option_stock').attr('readonly',false);
		$('.options_price_stock_option_stock').css('backgroundColor','#ffffff');
		//$('.options_price_stock_option_stock').unbind('click');
		
		$('.options_price_stock_option_stock').each(function(){
			//alert(1);
			$(this).css('backgroundColor','#ffffff');
			$(this).die('click');
			$(this).unbind('click');
			$(this).live('click',function(evt){
				return false;
			});
		});

		$('.stockinfo_loade').hide();

		if(product_type == '0'){
			if(vtype == 'Q'){
				$('#requeired_img').css('display','inline-block');
			}else if(vtype == 'N'){
				$('#requeired_img').css('display','none');
			}

			if(vtype == "Q"){
				$('.auto_option_create').show();
				$('.make_option').show();
				$('.make_option_btn').show();

				$("#price_option_zone").css('display','block');
				$("#no_price_option_zone").css('display','none');

			}else{
				$("#price_option_zone").css('display','none');
				$("#no_price_option_zone").css('display','block');	
				
			}
			
			if(vtype == "N"){
				$('#basic_option_zone').show();
				$('#basic_options_div_tab').show();
				$('#options_div_tab').hide();
				$('#stock_option_zone').hide();
			}else{
				$('#basic_option_zone').hide();
				$('#basic_options_div_tab').hide();
				$('#options_div_tab').show();
				$('#stock_option_zone').show();
			}
			//$('input[name=stock]').attr('validation',true);	//빠른재고나 사용안함일경우 필수값으로 체크해야함
		}

		$("input[name='auto_sync_wms']").attr('disabled',true);
	}
}


function del_add_img(i) {
	if($('.add_img_input_item').length > 1){
		$('.add_img_input_item')[i].parentNode.removeChild($('.add_img_input_item')[i]);
	}else{
		//alert(language_data['goods_input.php']['G'][language]);
		$('input[id=addimages_ad_ix_'+i+']').val("");
		$('#addimages_td1_'+i).html("");
		$('#addimages_td2_'+i).html("");
		$('#addimages_td3_'+i).html("");
	}
}


function DisplayDeleteOption(obj){

	obj.find("input[id^=dp_ix]").val('');
	obj.find("input[id^=dp_use]").val('');
	obj.find("input[id^=dp_title]").val('');
	obj.find("input[id^=dp_desc]").val('');
	obj.find("input[id^=dp_etc_desc]").val('');
	
}


function DisplayCopyOption(tbName){
//alert(tbName);
//

	var tbody = $('#' + tbName + ' tbody');  
	var total_rows = tbody.find('tr[depth^=1]').length;  
	var rows = tbody.find('tr[depth^=1]').length;  

	if($.browser.msie){
	  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
	}else{
	 // var newRow = tbody.find('tr[depth^=1]:last').clone(true).insertAfter(tbody);  
	  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
	}
	//alert(newRow.html());
	//newRow.find("div[id^=unit_text]").css('display','none');  
	//newRow.find("select[id^=unit]").css('display','inline');  

	//newRow.find("select[id^=unit]").attr("name","display_options["+(total_rows)+"][unit]");
	newRow.find("input[id^=dp_ix]").attr("name","display_options["+(total_rows)+"][dp_ix]");
	newRow.find("input[id^=dp_ix]").val('');
	newRow.find("input[id^=dp_use]").attr("name","display_options["+(total_rows)+"][dp_use]");

	newRow.find("input[id^=dp_title]").attr("name","display_options["+(total_rows)+"][dp_title]");
	newRow.find("input[id^=dp_desc]").attr("name","display_options["+(total_rows)+"][dp_desc]");
	newRow.find("input[id^=dp_etc_desc]").attr("name","display_options["+(total_rows)+"][dp_etc_desc]");



	//alert(newRow.html());  
	//return newRow;
}

function ViralInfoDelete(obj){

	obj.find("input[id^=vi_ix]").val('');
	obj.find("input[id^=vi_use]").val('');
	obj.find("input[id^=viral_name]").val('');
	obj.find("input[id^=viral_desc]").val('');
	obj.find("input[id^=viral_url]").val('');
	
}

function ViralInfoCopy(tbName){
//alert(tbName);
//	

	var tbody = $('#' + tbName + ' tbody');  
	var total_rows = tbody.find('tr[depth^=1]').length;  
	var rows = tbody.find('tr[depth^=1]').length;  

	if($.browser.msie){
	  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
	}else{
	 // var newRow = tbody.find('tr[depth^=1]:last').clone(true).insertAfter(tbody);  
	  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
	}
	//alert(newRow.html());
	//newRow.find("div[id^=unit_text]").css('display','none');  
	//newRow.find("select[id^=unit]").css('display','inline');  

	//newRow.find("select[id^=unit]").attr("name","virals["+(total_rows)+"][unit]");
	newRow.find("input[id^=vi_ix]").attr("name","virals["+(total_rows)+"][vi_ix]");
	newRow.find("input[id^=vi_ix]").val('');
	newRow.find("input[id^=vi_use]").attr("name","virals["+(total_rows)+"][vi_use]");

	newRow.find("input[id^=viral_name]").attr("name","virals["+(total_rows)+"][viral_name]");
	newRow.find("input[id^=viral_desc]").attr("name","virals["+(total_rows)+"][viral_desc]");
	newRow.find("input[id^=viral_url]").attr("name","virals["+(total_rows)+"][viral_url]");

	//alert(newRow.html());  
	//return newRow;
}

function AddOptionsCopyRow(target_id, option_var_name, seq){//가격재고옵션(옵션추가)
	
	var table_target_obj = $('table[id^='+option_var_name+'_table]');
	//var table_target_obj = $('#add_option_zone').find('table[class^='+option_var_name+'_table]');
	//options_input
	if(seq){
		var total_rows = seq;
	}else{
		var total_rows = table_target_obj.length-1;
	}

	var option_obj = $('#'+target_id);//target_obj;//target_obj.parent().parent().parent().parent().parent().parent().parent().parent();
	
	/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
	var option_length = 0;
	option_obj.find('tr[depth=1]:last').each(function(){
		 option_length = $(this).find('#option_length').val();
	});
	option_rows_total = parseInt(option_length) + 1;
	/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */

	///var option_obj = $('table[id^='+target_obj.parent().parent().parent().parent().parent().parent().parent().parent().attr("id")+']');
	//var newRow = option_obj.find('tr[depth=1]:first').clone(true).appendTo("#"+option_obj.attr("id"));  
	var newRow = option_obj.find('tr[depth=1]:first').clone(true).wrapAll("<table/>").appendTo("#"+option_obj.attr("id"));  //
	
	// 다른 옵션 요소는 depth를 가지고 하지만 옵션요소 count 할때는 item 을 가지고 
	//var option_rows_total = option_obj.find('tr[item=1]').length-1;

	/*
	newRow.find("input").each(function(){
			var this_name = $(this).attr("name").replace(/options\[0\]/g,option_var_name+"["+(total_rows)+"]");
			$(this).attr("name",this_name);
	});

	*/
	//alert(total_rows);
	//document.write(newRow.html());
	//alert(newRow.find("input[id^=option_div]").attr("name"));
	newRow.find("input[id$=add_option_div]").val('');
	newRow.find("input[id$=add_option_color]").val('');
	newRow.find("input[id$=add_option_size]").val('');
	newRow.find("input[id$=add_option_coprice]").val('');
	newRow.find("input[id^=add_option_wholesale_listprice]").val('');
	newRow.find("input[id^=add_option_wholesale_price]").val('');
	newRow.find("input[id$=add_option_listprice]").val('');
	newRow.find("input[id$=add_option_sellprice]").val('');
	newRow.find("input[id^=add_option_premiumprice]").val('');
	newRow.find("input[id^=add_option_soldout]").val('');
	newRow.find("input[id^=add_option_set_cnt]").val('1');
	newRow.find("input[id^=add_option_sell_ing_cnt]").val('');
	newRow.find("input[id^=add_option_stock]").val('');
	newRow.find("input[id^=add_option_safestock]").val('');
	newRow.find("input[id^=add_option_code]").val('');
	newRow.find("input[id^=add_option_gid]").val('');
	newRow.find("input[id^=add_option_barcode]").val('');
	newRow.find("input[id^=add_option_etc]").val('');
	newRow.find("select[id^=add_option_surtax_div]").val('');
	newRow.find("input[id^=option_length]").val(option_rows_total);
	
	newRow.find("input[id$=add_option_div]").attr("name",function (){
        return option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"]["+this.id.replace('add_','')+"]";
    });
	newRow.find("input[id$=add_option_color]").attr("name",function (){
		return option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"]["+this.id.replace('add_','')+"]";
	});
	newRow.find("input[id$=add_option_size]").attr("name",function (){
        return option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"]["+this.id.replace('add_','')+"]";
    });
	newRow.find("input[id$=add_option_coprice]").attr("name",function (){
        return option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"]["+this.id.replace('add_option_','')+"]";
    });
	newRow.find("input[id^=add_option_wholesale_listprice]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][wholesale_listprice]");
	newRow.find("input[id^=add_option_wholesale_price]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][wholesale_price]");
	newRow.find("input[id$=add_option_listprice]").attr("name",function (){
        return option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"]["+this.id.replace('add_option_','')+"]";
    });
	newRow.find("input[id$=add_option_sellprice]").attr("name",function (){
        return option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"]["+this.id.replace('add_option_','')+"]";
    });
	newRow.find("input[id^=add_option_premiumprice]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][premiumprice]");
	newRow.find("input[id^=add_option_soldout]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][soldout]");	
	newRow.find("input[id^=add_option_set_cnt]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][set_cnt]");	

	newRow.find("input[id^=add_option_sell_ing_cnt]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][sell_ing_cnt]");
	newRow.find("input[id^=add_option_stock]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][stock]");
	newRow.find("input[id^=add_option_safestock]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][safestock]");
	newRow.find("input[id^=add_option_code]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][code]");
	newRow.find("input[id^=add_option_gid]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][gid]");
	newRow.find("input[id^=add_option_barcode]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][barcode]");
	newRow.find("input[id^=add_option_etc]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][etc]");

	newRow.find("select[id^=add_option_surtax_div]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][option_surtax_div]");

	newRow.find("ul[name^=productList]").attr("id","productSetList_"+option_rows_total+"");
	newRow.find("div[name^=productList]").attr("id","productSetList_"+option_rows_total+"");
	
	if(newRow.find("img[id^=setoption_product_search]").length > 0){
		newRow.find("img[id^=setoption_product_search]").get(0).onclick="";
		newRow.find("img[id^=setoption_product_search]").attr("onclick","");
	}
	//try{
	//
	//}catch(e){};
	newRow.find("img[id^=setoption_product_search]").click(function(){
		//AddOptionsCopyRow(option_var_name+'_table_'+total_rows);
		//alert(total_rows);
		ms_productSearch.show_productSearchBox(event,option_rows_total,'productSetList_'+option_rows_total);
	});

	//alert(newRow.find("input[id^=option_div]").attr("name"));
	
/*	
	newRow.find("input[id^=option_opn_ix]").attr("name",option_var_name+"["+(total_rows)+"][opn_ix]");

	newRow.find("input[id^=options_option_type]").attr("name",option_var_name+"["+(total_rows)+"][option_type]");
	newRow.find("input[id^=options_option_use]").attr("name",option_var_name+"["+(total_rows)+"][option_use]");
	newRow.find("input[id^=option_name]").val("");
	newRow.find("input[id^=option_name]").attr("name",option_var_name+"["+(total_rows)+"][option_name]");
	newRow.find("select[id^=option_kind]").attr("name",option_var_name+"["+(total_rows)+"][option_kind]");
	

	newRow.find("input[id^=options_item_option_div]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][option_div]");
	newRow.find("input[id^=options_item_option_price]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][price]");
	newRow.find("input[id^=options_item_option_code]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][code]");
	newRow.find("input[id^=options_item_option_etc1]").attr("name",option_var_name+"["+(total_rows)+"][details]["+option_rows_total+"][option_etc1]");
	
	*/
	newRow.find("input[id^=options_item_opd_ix]").val("");
	
}

function DelMultTable(target_id,seq){

	$("#del_"+target_id+"_mult_table_tr").live("click",function() {
		if($("#"+target_id+"_product_mult_rate_table tr").size() > 1){
			$(this).parents("#"+target_id+"_product_mult_rate_tr").remove();
		}else{
			//alert("더 이상 삭제할수 없습니다.");
		}
	});
}


function AddMultTable(target_id, option_var_name){
	
	var table_target_obj = $('table[id='+target_id+'] tr');
	var total_rows = table_target_obj.length;

	var option_obj = $('#'+target_id);
	var newRow = option_obj.find('tr[depth=1]:first').clone(true).wrapAll("<table/>").appendTo("#"+option_obj.attr("id"));  //
	var option_rows_total = option_obj.find('tr[item=1]').length;

	//newRow.find("input[id^="+option_var_name+"_is_use]").val('');
	newRow.find("input[id^="+option_var_name+"_mr_id]").val('');
	newRow.find("input[id^="+option_var_name+"_sell_mult_cnt]").val('');
	newRow.find("input[id^="+option_var_name+"_rate_price]").val('');
	newRow.find("input[id^="+option_var_name+"_rate_div]").val('');

	newRow.find("input[id^="+option_var_name+"_is_use]").attr("name","wholesale_rate["+total_rows+"]["+option_var_name+"][is_use]");
	newRow.find("input[id^="+option_var_name+"_mr_id]").attr("name","wholesale_rate["+total_rows+"]["+option_var_name+"][mr_id]");
	newRow.find("input[id^="+option_var_name+"_sell_mult_cnt]").attr("name","wholesale_rate["+total_rows+"]["+option_var_name+"][sell_mult_cnt]");
	newRow.find("input[id^="+option_var_name+"_rate_price]").attr("name","wholesale_rate["+total_rows+"]["+option_var_name+"][rate_price]");
	newRow.find("select[id^="+option_var_name+"_round_cnt]").attr("name","wholesale_rate["+total_rows+"]["+option_var_name+"][round_cnt]");
	newRow.find("select[id^="+option_var_name+"_rate_div]").attr("name","wholesale_rate["+total_rows+"]["+option_var_name+"][rate_div]");
	newRow.find("select[id^="+option_var_name+"_round_type]").attr("name","wholesale_rate["+total_rows+"]["+option_var_name+"][round_type]");

}


function AddCopyOptions(target, option_var_name, zone_id){	//코디옵션용

	var option_target_obj = $('table[id^='+target+']');
	var option_obj = $('#'+zone_id);
	var total_rows = option_target_obj.length;

	//if(option_var_name == 'set2options' || option_var_name == 'codi_options'){
		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
		var option_length_table = 0;
		option_target_obj.find('tr[options=1]:last').each(function(){
			option_length_table = $(this).find('#option_length_table').val();
		});
		option_table_total = parseInt(option_length_table) + 1;
		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
	//}

	var newRow = option_obj.find('table[id^='+target+']:first').clone(true).appendTo(option_obj);
	
	for($i=newRow.find("tr[depth=1]").length;$i>1;$i--){
		newRow.find("tr[depth=1]").eq($i-1).remove();
	}

	newRow.find("input[id=option_opn_ix]").val('');
	newRow.find("tr[depth=1]").eq(0).parent().find("input[type=text]").each(function() {
		$(this).val("");
	});
	
	newRow.find("input[id^=option_length_table]").val(option_table_total);
	newRow.find("select[id^=option_kind]").attr("onchange","");
	newRow.find("select[id^=option_kind]").change(function(){
		if($(this).val() == 'x'){ 
			newRow.find('#box_option_info').show();
		}else{ 
			newRow.find('#box_option_info').hide();
		}
	});
	
	if(newRow.find("img[id^=btn_option_detail_add]").length > 0){
		newRow.find("img[id^=btn_option_detail_add]").get(0).onclick="";
		newRow.find("img[id^=btn_option_detail_add]").attr("onclick","123");
	}
	newRow.find("img[id^=btn_option_detail_add]").click(function(){
		//alert(option_var_name+'_table_'+total_rows);
		AddOptionsCopyRow(option_var_name+'_table_'+total_rows,option_var_name,total_rows);
	});

	if(newRow.find("div[id^=btn_inventory_search]").length > 0){
		newRow.find("div[id^=btn_inventory_search]").get(0).onclick="";
		newRow.find("div[id^=btn_inventory_search]").attr("onclick","");
	}
	//alert(option_table_total);
	newRow.find("div[id^=btn_inventory_search]").click(function(){
		if(option_var_name == 'set2options' || option_var_name == 'codi_options'){
			option_length_search = newRow.find('#option_length_table').val();	
			ShowModalWindow('../inventory/inventory_search.php?type='+option_var_name+'&seq='+option_length_search,1000,690,'inventory_search');
		}else{
			ShowModalWindow('../inventory/inventory_search.php?type='+option_var_name+'&seq='+total_rows,1000,690,'inventory_search');
		}
	});	//del_coditable
//newRow.find("img[id^=btn_option_detail_add]").attr("onclick","");
	if(newRow.find("div[id^=del_coditable]").length > 0){
		newRow.find("div[id^=del_coditable]").get(0).onclick="";
		newRow.find("div[id^=del_coditable]").attr("onclick","");
	}
	newRow.find("div[id^=del_coditable]").click(function(){
		del_coditable(total_rows);
	});	//del_coditable


	if(option_var_name == "codi_options"){
		newRow.find("img[id^=close_btn]").get(0).onclick="";
		newRow.find("img[id^=close_btn]").click(function(){
			//alert($('#codi_options_table_".$i."').find('tr[depth^=1]').length);
			if($('#codi_options_table_'+total_rows).find('tr[depth^=1]').length > 1){
				$(this).parent().parent().remove();
			}else{ 
				$(this).parent().parent().parent().parent().remove(); 
			}
		});
	}
//
	/*
	option_target_obj.find("img[id^=close_btn]").each(function(){
		$(this).hide();
	});
	*/
	newRow.find("img[id^=close_btn]").show();

	if(option_var_name == "set2options" || option_var_name == "codi_options"){
		newRow.attr("id",option_var_name+'_table_'+(option_table_total));
	}else{
		newRow.attr("id",option_var_name+'_table_'+(total_rows));
	}

	newRow.find("input").each(function(){
			if(option_var_name == "addoptions"){
				var this_name = $(this).attr("name").replace(/addoptions\[0\]/g, option_var_name+"["+(total_rows)+"]");
			}else if(option_var_name == "set2options"){
				var this_name = $(this).attr("name").replace(/set2options\[[0-9]+\]/g, option_var_name+"["+(option_table_total)+"]");
			}else if(option_var_name == "codi_options"){
				var this_name = $(this).attr("name").replace(/codi_options\[[0-9]+\]/g, option_var_name+"["+(option_table_total)+"]");
			}else{
				var this_name = $(this).attr("name").replace(/options\[0\]/g, option_var_name+"["+(total_rows)+"]");
			}
			$(this).attr("name",this_name);
	});

	newRow.find("select").each(function(){
			if(option_var_name == "addoptions"){
				var this_name = $(this).attr("name").replace(/addoptions\[0\]/g, option_var_name+"["+(total_rows)+"]");
			}else if(option_var_name == "set2options"){
				var this_name = $(this).attr("name").replace(/set2options\[[[0-9]+\]/g, option_var_name+"["+(option_table_total)+"]");
			}else if(option_var_name == "codi_options"){
				var this_name = $(this).attr("name").replace(/codi_options\[[[0-9]+\]/g, option_var_name+"["+(option_table_total)+"]");
			}else{
				var this_name = $(this).attr("name").replace(/options\[0\]/g, option_var_name+"["+(total_rows)+"]");
			}
			$(this).attr("name",this_name);
	});

}


function CalOption_stock(){	//옵션선택시 총재고수량을 재고수량에 넣기 2014-09-15 이학봉
	
	var total_stock = 0;

	$('input[id=add_option_stock]').each(function (){
		
		var stock = $(this).val();
		
		if(stock != ''){
			total_stock += parseInt(stock);
		}
	});
	//	alert(total_stock);
	$('input[name=stock]').val(total_stock);
}

function CalcurateMinimumPrice(option_type){
	
	var max_val = 0;
	var min_val = 99999999;

	if(option_type == 'box_options'){
		//alert(option_type);
		var listprice_min_val = 99999999;
		var sellprice_min_val = 99999999;
		var wholesale_listprice_min_val = 99999999;
		var wholesale_price_min_val = 99999999;
		var add_option_coprice = 99999999;

		var box_total = $('DIV#box_option_zone').find('#box_total').val();
		if(box_total == ""){
			box_total = 1;
			$('DIV#box_option_zone').find('#box_total').val(1);
		}
		$('DIV#box_option_zone').find('#add_option_sellprice').each(function(index){
			//alert($('DIV#box_option_zone').find('#add_option_listprice').eq(index).val());
			if(sellprice_min_val > parseInt($(this).val()) && $(this).val() > 0){				
				listprice_min_val = $('DIV#box_option_zone').find('#add_option_listprice').eq(index).val();
				sellprice_min_val = parseInt($(this).val());
				wholesale_listprice_min_val = $('DIV#box_option_zone').find('#add_option_wholesale_listprice').eq(index).val();
				wholesale_price_min_val = $('DIV#box_option_zone').find('#add_option_wholesale_price').eq(index).val();
				add_option_coprice = $('DIV#box_option_zone').find('#add_option_coprice').eq(index).val();
			}
		});
	
		if(sellprice_min_val == 99999999){
			alert('소매 판매가가 정상적으로 입력되지 않았습니다. ');
		}else{
			//alert(wholesale_price_min_val);
			$('input[name^=coprice]').val(add_option_coprice);
			$('input[name^=wholesale_price]').val(wholesale_listprice_min_val);
			$('input[name^=wholesale_sellprice]').val(wholesale_price_min_val);
			$('input[name^=listprice]').val(listprice_min_val);
			$('input[name^=sellprice]').val(sellprice_min_val);
		}
	}else if(option_type == 'set2options'){
		//
		var listprice_min_val = 99999999;
		var sellprice_min_val = 99999999;
		var wholesale_listprice_min_val = 99999999;
		var wholesale_price_min_val = 99999999;
		var add_option_coprice = 99999999;
 
		$('DIV#set2_option_zone').find('tr[setrow=1] #add_option_sellprice').each(function(index){
			
			if(sellprice_min_val > parseInt($(this).val()) && $(this).val() > 0){
				listprice_min_val = $('DIV#set2_option_zone').find('#add_option_listprice').eq(index).val();
				sellprice_min_val = parseInt($(this).val());
				wholesale_listprice_min_val = $('DIV#set2_option_zone').find('#add_option_wholesale_listprice').eq(index).val();
				wholesale_price_min_val = $('DIV#set2_option_zone').find('#add_option_wholesale_price').eq(index).val();
				add_option_coprice = $('DIV#set2_option_zone').find('#add_option_coprice').eq(index).val();
			}
		});

		
		//alert(listprice_min_val);
		$('input[name^=coprice]').val(add_option_coprice);
		$('input[name^=wholesale_price]').val(wholesale_listprice_min_val);
		$('input[name^=wholesale_sellprice]').val(wholesale_price_min_val);
		$('input[name^=listprice]').val(listprice_min_val);
		$('input[name^=sellprice]').val(sellprice_min_val);
	}else if(option_type == 'codi_options'){
		

		var listprice_min_val = 0;
		var sellprice_min_val = 0;
		var wholesale_listprice_min_val = 0;
		var wholesale_price_min_val = 0;
		var add_option_coprice = 0;

		
		$('DIV#codi_option_zone').find('table[option_type=codi]').each(function(index1){
			var _listprice_min_val = 99999999;
			var _sellprice_min_val = 99999999;
			var _wholesale_listprice_min_val = 99999999;
			var _wholesale_price_min_val = 99999999;
			var _add_option_coprice = 99999999;

			$(this).find('#add_option_sellprice').each(function(index2){
				if(_sellprice_min_val > parseInt($(this).val()) && $(this).val() > 0){
					_listprice_min_val = parseInt($('DIV#codi_option_zone').find('table[option_type=codi]').eq(index1).find('#add_option_listprice').eq(index2).val());
					_sellprice_min_val = parseInt($(this).val());
					_wholesale_listprice_min_val = parseInt($('DIV#codi_option_zone').find('table[option_type=codi]').eq(index1).find('#add_option_wholesale_listprice').eq(index2).val());
					_wholesale_price_min_val = parseInt($('DIV#codi_option_zone').find('table[option_type=codi]').eq(index1).find('#add_option_wholesale_price').eq(index2).val());
					_add_option_coprice = parseInt($('DIV#codi_option_zone').find('table[option_type=codi]').eq(index1).find(' #add_option_coprice').eq(index2).val());
				}
			});

			listprice_min_val += _listprice_min_val;
			sellprice_min_val += _sellprice_min_val;
			wholesale_listprice_min_val += _wholesale_listprice_min_val;
			wholesale_price_min_val += _wholesale_price_min_val;
			add_option_coprice += _add_option_coprice;

		});

		
		//alert(listprice_min_val);
		$('input[name^=coprice]').val(add_option_coprice);
		$('input[name^=wholesale_price]').val(wholesale_listprice_min_val);
		$('input[name^=wholesale_sellprice]').val(wholesale_price_min_val);
		$('input[name^=listprice]').val(listprice_min_val);
		$('input[name^=sellprice]').val(sellprice_min_val);
	}

	CalOption_stock();
}

function allOptionCalcuration(){
    OptionCalcuration('add_option_set_cnt', 'set2options_table_0', 'sum');
}

function OptionCalcuration(cal_id, target_obj_id, calcuration_type){
	var option_sum = 0;
	var max_val = 0;
	var min_val = 99999999;
	var premiumprice_sum =0;
	var sellprice_sum = 0;
	var listprice_sum =0;
	var wholesale_sellprice_sum = 0;
	var wholesale_listprice_sum =0;
	var coprice_sum =0;

    var english_sellprice_sum = 0;
    var english_listprice_sum =0;
    var english_coprice_sum =0;
	
	//alert($('#'+target_obj_id).find('tr[depth^=1] input[id^='+cal_id+']').length);
	$('#'+target_obj_id).find('tr[depth^=1] input[id^='+cal_id+']').each(function(){
		if(calcuration_type == 'sum'){
			if(cal_id == "add_option_sellprice" || cal_id == "add_option_wholesale_price"){
				// option_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_set_cnt').val());
                option_sum += parseInt($(this).val());
			}else if(cal_id == "add_option_listprice" || cal_id == "add_option_wholesale_listprice"){
				// option_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_set_cnt').val());
                option_sum += parseInt($(this).val());
			}else if(cal_id == "add_option_set_cnt"){
				// coprice_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_coprice').val());
				// sellprice_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_sellprice').val());
				// listprice_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_listprice').val());

				// premiumprice_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_premiumprice').val());

				// wholesale_sellprice_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_wholesale_price').val());
				// wholesale_listprice_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_wholesale_listprice').val());
                coprice_sum += parseInt($(this).parent().parent().find('#add_option_coprice').val());
                sellprice_sum += parseInt($(this).parent().parent().find('#add_option_sellprice').val());
                listprice_sum += parseInt($(this).parent().parent().find('#add_option_listprice').val());

                premiumprice_sum += parseInt($(this).parent().parent().find('#add_option_premiumprice').val());

                wholesale_sellprice_sum += parseInt($(this).parent().parent().find('#add_option_wholesale_price').val());
                wholesale_listprice_sum += parseInt($(this).parent().parent().find('#add_option_wholesale_listprice').val());

                english_coprice_sum += parseFloat($(this).parent().parent().find('#english_add_option_coprice').val());
                english_sellprice_sum += parseFloat($(this).parent().parent().find('#english_add_option_sellprice').val());
                english_listprice_sum += parseFloat($(this).parent().parent().find('#english_add_option_listprice').val());

			} else if(cal_id == "english_add_option_coprice" || cal_id == "english_add_option_listprice" || cal_id == "english_add_option_sellprice") {
                option_sum += parseFloat($(this).val());
			}
			else{
				option_sum += parseInt($(this).val());
			}
		}else if(calcuration_type == 'max'){
			if(max_val < $(this).val()){
				max_val = parseInt($(this).val());
			}
		}else if(calcuration_type == 'min'){
			if(min_val > $(this).val()){
				min_val = parseInt($(this).val());
			}
		}
	});
	//alert(option_sum);
	//alert($('#'+target_obj_id).find('tr input[sum_id^='+cal_id+']').html());
	if(calcuration_type == 'sum'){
		if(cal_id == "add_option_sellprice" || cal_id == "add_option_wholesale_price"){
			//option_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_set_cnt').val());
			if(!isNaN(option_sum)){
				$('#'+target_obj_id).find('tr input[sum_id^='+cal_id+']').val(option_sum);
			}
		}else if(cal_id == "add_option_listprice" || cal_id == "add_option_wholesale_listprice"){
			//option_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_set_cnt').val());
            if(!isNaN(option_sum)){
				$('#'+target_obj_id).find('tr input[sum_id^='+cal_id+']').val(option_sum);
			}
		}else if(cal_id == "add_option_set_cnt"){
			//sellprice_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_sellprice').val());
			//listprice_sum += parseInt($(this).val() * $(this).parent().parent().find('#add_option_listprice').val());
            if(!isNaN(english_coprice_sum)) {
                $('#' + target_obj_id).find('tr input[sum_id^=english_add_option_coprice]').val(english_coprice_sum);
            }
            if(!isNaN(english_sellprice_sum)) {
                $('#'+target_obj_id).find('tr input[sum_id^=english_add_option_sellprice]').val(english_sellprice_sum);
            }
            if(!isNaN(english_listprice_sum)) {
                $('#'+target_obj_id).find('tr input[sum_id^=english_add_option_listprice]').val(english_listprice_sum);
            }

            if(!isNaN(coprice_sum)) {
                $('#' + target_obj_id).find('tr input[sum_id^=add_option_coprice]').val(coprice_sum);
            }
            if(!isNaN(sellprice_sum)) {
                $('#'+target_obj_id).find('tr input[sum_id^=add_option_sellprice]').val(sellprice_sum);
            }
            if(!isNaN(listprice_sum)) {
                $('#'+target_obj_id).find('tr input[sum_id^=add_option_listprice]').val(listprice_sum);
            }
            if(!isNaN(wholesale_sellprice_sum)) {
                $('#'+target_obj_id).find('tr input[sum_id^=add_option_wholesale_price]').val(wholesale_sellprice_sum);
            }
            if(!isNaN(wholesale_listprice_sum)) {
                $('#'+target_obj_id).find('tr input[sum_id^=add_option_wholesale_listprice]').val(wholesale_listprice_sum);
            }
            if(!isNaN(premiumprice_sum)) {
                $('#'+target_obj_id).find('tr input[sum_id^=add_option_premiumprice]').val(premiumprice_sum);
            }
		}else{
            if(!isNaN(option_sum)){
				$('#'+target_obj_id).find('tr input[sum_id^='+cal_id+']').val(option_sum);
			}
		}
		
	}else if(calcuration_type == 'max'){
        if(!isNaN(max_val)){
			$('#'+target_obj_id).find('tr input[sum_id^='+cal_id+']').val(max_val);
		}
	}else if(calcuration_type == 'min'){
        if(!isNaN(min_val)){
			$('#'+target_obj_id).find('tr input[sum_id^='+cal_id+']').val(min_val);
		}
	}
}


function newCopyOptions(target){

	if($('#basic_option_zone').find('select.option_kind [value="i1"]:selected').length > 0){
		alert('독립옵션은 한 개만 구성할 수 있습니다');
		return false;
	}

	var option_target_obj = $('#basic_option_zone').find('table[id^='+target+']');
	var option_obj = $('#basic_option_zone')
    //var total_rows = option_target_obj.length;
	var total_rows = parseInt(option_obj.find('table[id^='+target+']:last').attr("idx"))+1;
	//var newRow = option_obj.find('table[id^='+target+']:first').clone(true).appendTo(option_obj);

	
	var newRow = option_obj.find('table[id^='+target+']:first').clone(true).wrapAll("<table/>").parent();
//alert(option_obj.html());
	newRow.find("table[id^=options_input]").attr("idx",""+(total_rows)+"");
	newRow.find("input[id^=option_opn_ix]").attr("name","options["+(total_rows)+"][opn_ix]");
	newRow.find("input[id^=option_opn_ix]").val("");
	newRow.find("input[id^=options_option_type ]").attr("name","options["+(total_rows)+"][option_type]");
	newRow.find("input[id^=options_option_use ]").attr("name","options["+(total_rows)+"][option_use]");
	newRow.find("input[id^=option_name ]").val("");
	newRow.find("input[id^=option_name ]").attr("name","options["+(total_rows)+"][option_name]");
	newRow.find("select[id^=option_kind_0]").attr("name","options["+(total_rows)+"][option_kind]");
	newRow.find("select[id^=option_kind_0]").attr("id","option_kind_"+(total_rows)+"");
	newRow.find("table[id^=options_basic_item_input_table_0]").attr("id","options_basic_item_input_table_"+(total_rows)+"");
	//newRow.find("input[id^=options_item_opd_ix_0]").attr("name","options["+(total_rows)+"][opd_ix]"); // 

	
	newRow.find("input[id^=options_item_opd_ix_0]").val("");
	if(true){ 
		// 옵션 그룹 변경시 options_item_option_div_0 뒤 숫자값은 전체 옵션 그룹에 따라서 증가하는거기때문에 그룹변경시 변경되는게 맞음 2012.07.04 
		// 옵션 그룹이 복사 될때는 아래 내용들은 변환될 필요가 없음
		newRow.find("input[id^=options_item_opd_ix_0]").attr("id","options_item_opd_ix_"+(total_rows)+"");

		//newRow.find("input[id^=options_item_option_div_0]").attr("name","options["+(total_rows)+"][details][0][option_div]");
		
		//옵션 그룹이 복제 될때는 해당 정보는 변경되지 않아야 한다. 2012.12.21 신훈식
		//newRow.find("input[id^=options_item_option_div_0]").attr("inputid","options_item_option_div_"+(total_rows)+""); // 아래 내용과 순서가 바뀌면 안됨
		//alert(newRow.html());
		//newRow.find("input[id^=options_item_option_div_0]").val("");
		//newRow.find("input[id^=options_item_option_div_0]").attr("id","options_item_option_div_"+(total_rows)+"");
		//newRow.find("input[class^=options_detail_option_div_0]").attr("class","options_detail_option_div_"+(total_rows)+"");
		

		//newRow.find("input[id^=options_item_option_price_0]").attr("name","options["+(total_rows)+"][details][0][price]");
		newRow.find("input[id^=options_item_option_price_0]").val("");
		newRow.find("input[id^=options_item_option_price_0]").attr("id","options_item_option_price_"+(total_rows)+"");
		
		//newRow.find("input[id^=options_item_option_code_0]").attr("name","options["+(total_rows)+"][details][0][code]");	
		//newRow.find("input[id^=options_item_option_code_0]").attr("id","options_item_option_code_"+(total_rows)+"");
	}

	//newRow.find("table[id^=options_item_input_0]:last")
	//newRow.find("input[id^=options_item_input_0]").attr("id","options_item_input_"+(total_rows)+"");
	newRow.find("table[id^=options_item_input_0]").attr("idx",""+(total_rows)+"");
	newRow.find("select[name^=opnt_ix]").attr("idx",""+(total_rows)+"");
	
	newRow.find("table[id^=options_item_input_0]").attr("id","options_item_input_"+(total_rows)+"");
	newRow.find("table[class^=options_item_input_0]").attr("class","options_item_input_"+(total_rows)+"");
	//alert(newRow.find("img[id^=btn_favorite_option_use]"));
	//newRow.find("img[id^=btn_favorite_option_use]").css('border','1px solid red');
	//newRow.find("img[id^=btn_favorite_option_use]").unbind("click");
	//newRow.find("img[id^=btn_favorite_option_use]").bind("click",function(){
	//	alert(1);
		//SetOptionTmp($('#favorite_option_'+total_rows),total_rows);
	//});



	newRow.find("input[id^=options_item_option_details_ix_0]").attr("id","options_item_option_details_ix_"+(total_rows)+"");
	//newRow.find("input[id^=options_item_option_details_ix_0]").attr("id","options_item_option_details_ix_"+(total_rows)+"");

	
	 
	//option_obj.find('table[id^='+target+']:first').appendTo(newRow.clone().wrapAll("<table/>").parent().html())
	//copyObjectText = newRow.clone().wrapAll("<table/>").parent().html();
	copyObjectText = newRow.html();
	//alert(copyObjectText);
	copyObjectText = copyObjectText.replace(/options\[[0-9]+\]/g,"options["+(total_rows)+"]");
	copyObjectText = copyObjectText.replace(/favorite_option_[0-9]+/g,"favorite_option_"+(total_rows)+"");
	copyObjectText = copyObjectText.replace(/options_basic_item_input_table_[0-9]+/g,"options_basic_item_input_table_"+(total_rows)+"");
	copyObjectText = copyObjectText.replace(/options_item_input_[0-9]+/g,"options_item_input_"+(total_rows)+"");
	
	//copyObjectText = copyObjectText.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;margin:3px;' ondblclick=\"if(document.all."+target+".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert('마지막 한개는 삭제 하실 수 없습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");
	copyObjectText = copyObjectText.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;margin:3px;' ondblclick=\"if($('.options_input').length > 1){$('.options_input[idx="+total_rows+"]').remove();}else{alert('마지막 한개는 삭제 하실 수 없습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");

	

	$(copyObjectText).appendTo(option_obj);
	//$('#option_info_text').val(copyObjectText);//.replace(/options\[0\]/g,"options["+(total_rows)+"]")
	//$('#option_info_text').val(newRow.clone().wrapAll("<table/>").parent().html());//.replace(/options\[0\]/g,"options["+(total_rows)+"]")

	 //alert(newRow.parent().html());
	 //var newRow = option_obj.find('tr[depth^=1]:last').clone(true).appendTo(option_obj);  
/*
	 obj_table_text = obj_table.outerHTML.replace(/options_item_input_0/g,"options_item_input_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options_item_option_div_0/g,"options_item_option_div_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options_item_option_code_0/g,"options_item_option_code_"+(parseInt(select_idx)+1));
   obj_table_text = obj_table_text.replace(/ idx=\"0\"/g," idx="+(parseInt(select_idx)+1));



  
  obj_table_text = obj_table_text.replace(/options\[0\]/g,"options["+(parseInt(select_idx)+1)+"]");

	obj_table_text = obj_table_text.replace(/option_name_area_0/g,"options_input_status_area_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options_basic_item_input_table_0/g,"options_basic_item_input_table_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options_input_status_area_0/g,"options_input_status_area_"+(parseInt(select_idx)+1));
	
	
	
	//obj_table_text = obj_table_text.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all."+obj+".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert('마지막 한개는 삭제 하실 수 없습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");
	var child_txt = "$('."+obj+"[idx="+(parseInt(select_idx)+1)+"]')[0]";
	var parent_txt = "$('."+obj+"[idx="+(parseInt(select_idx)+1)+"]')";
*/

}


function copyOptions(obj){
	
	var select_idx;
	var objs=$("."+obj);

	if(objs.length > 0 ){
		var obj_table = $("."+obj+":first").clone(true);
		var target_obj = $("."+obj+":last");
	}else{	
		var obj_table = objs.clone(true);
		var target_obj = objs;
	}

	if(obj_table.attr("id") == "add_img_input_item"){ //추가 이미지 등록 kbk
		
		select_idx = parseInt($("."+obj+":last").attr("opt_idx"));

		if(language == "korea"){
			var image_copy = "이미지복사";
			var use_copy = "복사함";
			var deepzoom_image_create = "deepzoom 이미지 생성";			
		}else if(language == "english"){
			var image_copy = "Copy Images";
			var use_copy = "Copy";
			var deepzoom_image_create = "Show DeepZoom";
		}else if(language == "indonesian"){
			var image_copy = "Copy Images";
			var use_copy = "Copy";
			var deepzoom_image_create = "Show DeepZoom";
		}

		var obj_table_text="<table width=100% id='add_img_input_item' class='add_img_input_item' opt_idx=\"0\" cellspacing=4 cellpadding=0>";
		obj_table_text+="<col width='7%' /><col width='26%' /><col width='7%' /><col width='26%' /><col width='7%' /><col width='*' />";
		obj_table_text+="<tr align='left'>";
		obj_table_text+="<td></td>";
		obj_table_text+="<td>";
		obj_table_text+="<b >"+image_copy+"</b><input type=checkbox name='addimages[0][add_copy_allimg]' onclick=\"copyAddImageCheckAll('0');\" id='add_copy_allimg_0'  inputid='add_copy_allimg_0' value=1 checked>";
		obj_table_text+="</td>";
		obj_table_text+="<td></td>";
		obj_table_text+="<td>";
		obj_table_text+=""+use_copy+"<input type=checkbox name='addimages[0][add_chk_mimg]' id='add_copy_img_0' value=1 inputid='add_copy_img_0' checked>";
		obj_table_text+="</td>";
		obj_table_text+="<td></td>";
		obj_table_text+="<td>";
		obj_table_text+=""+use_copy+"<input type=checkbox name='addimages[0][add_chk_cimg]' id='add_copy_img_0' value=1 inputid='add_copy_img_0' checked>";
		obj_table_text+="</td>";
		obj_table_text+="</tr>";
		obj_table_text+="<tr align='left'>";
		obj_table_text+="<td></td>";
		obj_table_text+="<td>";
		obj_table_text+="<input type=file class='textbox' name='addimages[0][addbimg]' style='width:90%;vertical-align:middle' value='$option_div'>";
		obj_table_text+="</td>";
		obj_table_text+="<td></td>";
		obj_table_text+="<td>";
		obj_table_text+="<input type=file class='textbox' name='addimages[0][addmimg]'  style='width:90%' value='$option_price'>";
		obj_table_text+="</td>";
		obj_table_text+="<td></td>";
		obj_table_text+="<td>";
		obj_table_text+="<input type=file class='textbox' name='addimages[0][addcimg]'  style='width:90%' value='$option_price'>";
		obj_table_text+="</td>";
		obj_table_text+="</tr>";
		obj_table_text+="<tr align='left'>";
		obj_table_text+="<td></td>";
		obj_table_text+="<td colspan='4'>";
		//obj_table_text+="<b class=small> "+deepzoom_image_create+"</b><input type=checkbox name='addimages[0][add_copy_deepzoomimg]' value=1 >";//딥줌을 사용할 수 없도록 주석처리함 kbk 12/12/26
		obj_table_text+="</td>";
		obj_table_text+="<td align='right'>";
		obj_table_text+="<!-- 옵션 삭제 -->";
		obj_table_text+="</td>";
		obj_table_text+="</tr>";
		obj_table_text+="</table>";
		
		obj_table_text = obj_table_text.replace(/opt_idx=\"0\"/g,"opt_idx="+(parseInt(select_idx)+1));
		obj_table_text = obj_table_text.replace(/addimages\[0\]/g,"addimages["+(parseInt(select_idx)+1)+"]");
		obj_table_text = obj_table_text.replace(/id=\'add_copy_allimg_0\'/g,"id='add_copy_allimg_"+(parseInt(select_idx)+1)+"'");
		obj_table_text = obj_table_text.replace(/inputid=\'add_copy_allimg_0\'/g,"inputid='add_copy_allimg_"+(parseInt(select_idx)+1)+"'");
		obj_table_text = obj_table_text.replace(/id=\'add_copy_img_0\'/g,"id='add_copy_img_"+(parseInt(select_idx)+1)+"'");
		obj_table_text = obj_table_text.replace(/inputid=\'add_copy_img_0\'/g,"inputid='add_copy_img_"+(parseInt(select_idx)+1)+"'");
		obj_table_text = obj_table_text.replace(/onclick=\"copyAddImageCheckAll\(\'0\'\);\"/g,"onclick=\"copyAddImageCheckAll('"+(parseInt(select_idx)+1)+"')\"");
		

		var child_txt="$('."+obj+"[opt_idx="+(parseInt(select_idx)+1)+"]')[0]";
		var parent_txt=child_txt+'.parentNode';
		//alert(obj_table_text);
		obj_table_text = obj_table_text.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('."+obj+"').length > 1){"+parent_txt+".removeChild("+child_txt+");/*this.parentNode.parentNode.parentNode.removeNode(true);*/}else{alert(language_data['goods_input.js']['W'][language]);}\" title='더블클릭시 해당 테이블이 삭제 됩니다.'>");// 호환성 2011-04-08 kbk
		//'마지막 한개는 삭제 하실 수 없습니다.'
		
		obj_table = obj_table_text;

	}else{// 옵션 항목 추가 부분

		select_idx = parseInt(target_obj.attr("idx"));
		detail_idx = parseInt(target_obj.attr('detail_idx'))+1;

		obj_table.find("#options_item_option_soldout_0").attr("id","options_item_option_soldout_"+(detail_idx));
		obj_table.find("#options_item_option_div_0").attr("id","options_item_option_div_"+(detail_idx));
		obj_table.find("#options_item_option_price_0").attr("id","options_item_option_price_"+(detail_idx));
		obj_table.find("#options_item_option_code_0").attr("id","options_item_option_code_"+(detail_idx));
		obj_table.find("#options_item_option_etc1_0").attr("id","options_item_option_etc1_"+(detail_idx));
		
		obj_table.attr("detail_idx",(detail_idx));
		obj_table.attr("idx",(select_idx));
		
		obj_table.find("[name*='[details][0]']").attr("name",function(){
			return $(this).attr("name").replace("[details][0]","[details]["+(detail_idx)+"]");
		});
	}

	$(obj_table).insertAfter(target_obj);
}


function showMessage(_id, message){
	try{
		document.getElementById(_id).innerHTML = message;	
	}catch(e){}
	//document.getElementById(_id).innerHTML = "";
}


function clearInputBox(obj_id){
	//alert(document.getElementById(obj_id).getElementsByTagName('INPUT').length);
	var _objs = document.getElementById(obj_id).getElementsByTagName('INPUT');
	for(i=0;i < _objs.length;i++){
		//alert(_objs[i].outerHTML);
		_objs[i].value='';
	}
}

function setCategory(mode,cname,cid,depth,pid)
{
	//outTip(img3);
	document.frames["act"].location.href='./relation.act.php?mode='+mode+'&cid='+cid+'&pid='+pid; //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
}


function deleteCategory(mode,rid,pid)
{
	document.frames["act"].location.href='./relation.act.php?mode='+mode+'&rid='+rid+'&pid='+pid; 
}
	
function calcurate(frm){	
	
	//var rate1 = frm.rate1.value;
	var rate1 = $('input[name=rate1]').val();
	//var rate2 = frm.rate2.value;
	
	if(frm.sellprice.value.length < 1)
	{
		alert(language_data['goods_input.js']['A'][language]);	
		//'판매가격이 입력되지 않았습니다.'
		return false;
	}
	
	
	if($('input[name=rate1]').length < 1)	{
		//alert(language_data['goods_input.js']['B'][language]);	
		//'현금사용시 적립율이 입력되지 않았습니다.'
		return false;
	}else{
		rate1 = rate1/100
	}
	/*
	if(frm.rate2.value.length < 1)	{
		alert(language_data['goods_input.js']['C'][language]);	
		//'카드사용시 적립율이 입력되지 않았습니다.'
		return false;
	}else{
		rate2 = rate2/100
	}
	*/

	frm.reserve_price.value = Round2(filterNum(frm.sellprice.value) * rate1,1,1);
	return true;
	
}

function filterNum(str) {
re = /^$|,/g;

// "$" and "," 입력 제거
	if(str != ""){
		return str.replace(re, "");
	}
}

function calcurate_maginrate(frm){
	
	//도매
	if(frm.coprice.value.length < 1)	{
		ncoprice = 0;
	}else{
		ncoprice = filterNum(frm.coprice.value);
	}
	
	
	/*다이소 도매 사용안함으로 잠시 주석 처리 2014-08-13 이학봉
	if(frm.wholesale_price.value < 1)	{
		nwholesale_price = 0;
	}else{
		nwholesale_price = filterNum(frm.wholesale_price.value);
	}

	if(frm.wholesale_sellprice.value.length < 1){
		nwholesale_sellprice = 0;
	}else{
		nwholesale_sellprice = filterNum(frm.wholesale_sellprice.value);
	}
	

	if($('input[name=wholesale_price]').lenth < 1){
		nwholesale_price = 0;
	}else{
		nwholesale_price = filterNum($('input[name=wholesale_price]').val());
	}

	if($('input[name=wholesale_sellprice]').lenth < 1){
		nwholesale_sellprice = 0;
	}else{
		nwholesale_sellprice = filterNum($('input[name=wholesale_sellprice]').val());
	}

	if(nwholesale_sellprice == 0){
		//frm.wholesale_basic_margin.value = "-" ;
		$('input[name=wholesale_basic_margin]').val('-');
	}else{
		//frm.wholesale_basic_margin.value = round((nwholesale_sellprice-ncoprice)/ncoprice * 100,2) ;
		$('input[name=wholesale_basic_margin]').val(round((nwholesale_sellprice-ncoprice)/ncoprice * 100,2));
	}

	if(nwholesale_sellprice == 0){
		//frm.wholesale_sale_rate.value = "-" ;
		$('input[name=wholesale_sale_rate]').val('-');
	}else{
		//frm.wholesale_sale_rate.value = round((1-(nwholesale_sellprice/nwholesale_price))* 100,2) ;
		$('input[name=wholesale_sale_rate]').val(round((1-(nwholesale_sellprice/nwholesale_price))* 100,2));
	}*/

	//소매
	if(frm.coprice.value.length < 1)	{
		ncoprice = 0;
	}else{
		ncoprice = filterNum(frm.coprice.value);
	}
	if(frm.listprice.value.length < 1)	{
		nlistprice = 0;
	}else{
		nlistprice = filterNum(frm.listprice.value);
	}
	if(frm.sellprice.value.length < 1)	{
		nsellprice = 0;
	}else{
		nsellprice = filterNum(frm.sellprice.value);
	//	frm.premiumprice.value = frm.sellprice.value * 0.8;
	}
	if(nsellprice == 0){
		frm.basic_margin.value = "-" ;
	}else{
		if(ncoprice == '0'){
			frm.basic_margin.value = '0';
		}else{
			frm.basic_margin.value = round((nsellprice-ncoprice)/ncoprice * 100,2) ;
		}
	}
	if(nsellprice == 0){
		frm.sale_rate.value = "-" ;
	}else{
		if(nlistprice == '0'){
			frm.sale_rate.value = '0';
		}else{
			frm.sale_rate.value = round((1-(nsellprice/nlistprice))* 100,0) ;
		}
	}


}


function calcurate_margin(frm){	
	
	var card_pay = frm.card_pay.value;
	
	var basic_margin = frm.basic_margin.value/1;
	var sellprice = filterNum(frm.sellprice.value)/1;
/*	var wholesale_basic_margin = frm.wholesale.basic_margin.value/1;
	var wholesal_sellprice = filterNum(frm.wholesale_sellprice.value/1;
	*/
//	pricecheckmode = true;
	//var reserve = frm.rate1.value/100;
	var rate1 = $('input[name=rate1]').val();

	var reserve = parseInt(rate1)/100;

	if(frm.sellprice.value.length < 1)
	{
		alert(language_data['goods_input.js']['A'][language]);	
		//'판매가격이 입력되지 않았습니다.'
		return false;
	}
	
	
	if(frm.card_pay.value.length < 1)	{
		alert(language_data['goods_input.js']['D'][language]);	
		//'카드수수료가 입력되지 않았습니다.'
		return false;
	}else{
		card_pay = card_pay/100
	}

	calcurate(frm);
	
	frm.card_price.value = sellprice*card_pay;
//	frm.reserve.value = sellprice*card_pay;
	//frm.nointerest_price.value = sellprice*nointerest_pay;
	if (reserve == 0){
		frm.reserve_price.value = 0;
	}else{
		frm.reserve_price.value = Round2(sellprice*reserve,1,1);
	}
	
/*	if(sellprice >= 200000){
		frm.basic_margin.value  = 2000;
	}else{
		frm.basic_margin.value  = sellprice*0.01;
	}
*/	
	frm.margin.value =  parseInt(frm.card_price.value)  + parseInt(frm.reserve_price.value);// + parseInt(frm.basic_margin.value) //+ parseInt(frm.nointerest_price.value);
//	frm.coprice.value = sellprice - frm.margin.value
	return true;
	
}
var clickable = true;
function ProductInput(frm,act){
	var stock_use_yn = $('input[name=stock_use_yn]:checked').val();
	var stock = $('input[name=stock]').val();

	if(stock_use_yn == 'N'){
		var i1_cnt = $('#basic_option_zone').find('select.option_kind [value="i1"]:selected').length;
		var c1_cnt = $('#basic_option_zone').find('select.option_kind [value="c1"]:selected').length;
		var total_option_cnt = $('#basic_option_zone').find('.options_input').length;

		if(i1_cnt > 0){
			if(total_option_cnt > 1){
				alert('독립옵션은 한 개만 구성할 수 있습니다');
				return false;
			}
		}

		if(c1_cnt > 0){
			if(total_option_cnt < 2){
				alert('조합옵션은 2개이상 구성되어야 합니다');
				return false;
			}
		}

		if(i1_cnt > 0 && c1_cnt > 0){
			alert('독립옵션과 조합옵션은 동시에 등록할 수 없습니다');
			return false;
		}
	}

    if(!clickable){
        alert('상품등록중입니다');
        return;
    }



	//빠른재고 사용시 재고수량을 필수로 넣어야함 시작 2014-09-12 이학봉
	var categorys=document.getElementsByName('display_category[]');
	var product_type = $('input[name=product_type]:checked').val();


	//정가 < 실판매가 막기
	if( parseInt($('input[name=listprice]').val()) < parseInt($('input[name=sellprice]').val())){
		alert("정가 보다 실판매가가 높을 수는 없습니다.");
        $('input[name=listprice]').focus();
		return false;
	}

    var stock_title = $('.stock_title').val();

	//일반 상품 체크
    if(product_type == '0') {
    	if(!$("input[name='stock_options[0][option_use]']").is(":checked")){
            alert("상품옵션 사용 체크 박스를 선택 하셔야 합니다.");
            return false;
		}
		if(!stock_title){
			alert("일반 상품에 옵션명은 필수 입니다.");
			$('.stock_title').focus();
			clickable = true;
			return false;
		}

        var add_option_gid_cnt = 0;
        $(".items > td > #add_option_gid").each(function (k, v) {
            //console.log($(this).val());
			if($(this).val()){
                add_option_gid_cnt++;
			}
        });
        if(stock_use_yn == 'Y' && add_option_gid_cnt <= 0){
            alert("품목코드가 비어있습니다. 재고정보불러오기를 클릭하여, 품목을 추가하셔야 합니다.");
            clickable = true;
            return false;
        }
    }
    //일반 상품 체크 end

	//세트상품 체크
    if(product_type == '99') {
        var set_opt_type = $(".changeable_area.on").find("td").text();
		var set_opt_count = ($('.setOption:checked').length);
		if(set_opt_count < 2){
			alert('세트옵션으로 구성해주세요.');
            clickable = true;
            return false;
		}

        for(i=1;i < $("input[inputid=option_name].textbox.point_color").length;i++){
			if($("input[inputid=option_name].textbox.point_color")[i].value == ''){
				alert("옵션명은 필수 입니다.");
				$("input[inputid=option_name].textbox.point_color")[i].focus();
				clickable = true;
				return false;
			}
		}

        //세트(묶음상품) 옵션 인경우 코디상품옵션
		if(set_opt_type == "세트(묶음상품) 옵션") {
            if(!$("#set2_option_use").is(":checked")){
                alert("상품옵션 체크 박스를 선택 하셔야 합니다.");
            	return false;
			}

			if(!$("#set2_option_use").parent().find("~ td > #add_option_name").val()){
            	alert("옵션명은 필수 입니다.");
                $("#set2_option_use").parent().find("~ td > #add_option_name").focus();
            	return false;
			}

            var sets_options_cnt = 0;
            $(".sets_options > td > #add_option_gid").each(function (k, v) {
            	if($(this).val()){
                    sets_options_cnt++;
				}
            });
            if(sets_options_cnt <= 0){
                alert("품목코드가 비어있습니다. 재고정보불러오기를 클릭하여, 구성상품 품목을 추가하셔야 합니다.");
            	return false;
			}


        }
    }
    //세트상품 체크 end
    //$(".changeable_area.on").find("td").text();


    clickable = false;



	if(product_type != '77'){		//사은품 상품일경우 카테고리선택이 없으므로 무시 2014-04-15 이학봉
		if(categorys.length < 1){
			alert(language_data['goods_input.js']['E'][language]);//'카테고리를 선택해주세요'
			clickable = true;
			$('select[name=cid0]').focus();
			return false;
		}
	}
	//빠른재고 사용시 재고수량을 필수로 넣어야함 끝 2014-09-12 이학봉

	frm.act.value = act;

	if($("#is_sell_date").val() == 1){
		if($("#sell_priod_sdate").val() < ""){
			// ??
		}
	}

	$(".stock_options_table").each(function(){
		if($(this).find("#add_option_div").val().length > 0){
			$(this).find("#add_option_name").attr('validation','true');			
			$(this).find("#add_option_coprice").attr('validation','true');
			$(this).find("#add_option_wholesale_price").attr('validation','true');
			$(this).find("#add_option_sellprice").attr('validation','true');
			$(this).find("#add_option_coprice").attr('validation','true');
		}else{
			$(this).find("#add_option_name").attr('validation','false');
			$(this).find("#box_total").attr('validation','false');
			
			$(this).find("#add_option_coprice").attr('validation','false');
			$(this).find("#add_option_wholesale_price").attr('validation','false');
			$(this).find("#add_option_sellprice").attr('validation','false');
			$(this).find("#add_option_coprice").attr('validation','false');
		}
	});

	$(".box_options_table").each(function(){
		if($(this).find("#add_option_div").val().length > 0){
			$(this).find("#add_option_name").attr('validation','true');
			$(this).find("#box_total").attr('validation','true');
			
			$(this).find("#add_option_coprice").attr('validation','true');
			$(this).find("#add_option_wholesale_price").attr('validation','true');
			$(this).find("#add_option_sellprice").attr('validation','true');
			$(this).find("#add_option_coprice").attr('validation','true');
		}else{
			$(this).find("#add_option_name").attr('validation','false');
			$(this).find("#box_total").attr('validation','false');
			
			$(this).find("#add_option_coprice").attr('validation','false');
			$(this).find("#add_option_wholesale_price").attr('validation','false');
			$(this).find("#add_option_sellprice").attr('validation','false');
			$(this).find("#add_option_coprice").attr('validation','false');
		}
	});

	$(".addoptions_table").each(function(){
		if($(this).find("#add_option_div").val().length > 0){
			$(this).find("#add_option_name").attr('validation','true');
			$(this).find("#add_option_kind").attr('validation','true');
			
			$(this).find("#add_option_coprice").attr('validation','true');
			$(this).find("#add_option_wholesale_price").attr('validation','true');
			$(this).find("#add_option_sellprice").attr('validation','true');
		}else{
			$(this).find("#add_option_name").attr('validation','false');
			$(this).find("#add_option_kind").attr('validation','false');
			$(this).find("#add_option_coprice").attr('validation','false');
			$(this).find("#add_option_wholesale_price").attr('validation','false');
			$(this).find("#add_option_sellprice").attr('validation','false');
		}
	});

	$(".set2options_table tr[depth^=1]").each(function(){
		if($(this).find("#add_option_div").val().length > 0){
			$('#set2option_table').find("#add_option_name").attr('validation','true');
			$('#set2option_table').find("#option_kind").attr('validation','true');
			
			$(this).find("#add_option_coprice").attr('validation','true');
			$(this).find("#add_option_wholesale_price").attr('validation','true');
			$(this).find("#add_option_sellprice").attr('validation','true');
			$(this).find("#add_option_coprice").attr('validation','true');
			$(".set2options_table tr[item=1]:first").each(function(){
				$(this).find("#add_option_div").attr('validation','true');
			});
		}else{
			$('#set2option_table').find("#add_option_name").attr('validation','false');
			$('#set2option_table').find("#option_kind").attr('validation','false');

			$(this).find("#add_option_coprice").attr('validation','false');
			$(this).find("#add_option_wholesale_price").attr('validation','false');
			$(this).find("#add_option_sellprice").attr('validation','false');
			$(this).find("#add_option_coprice").attr('validation','false');
			$(".set2options_table tr[item=1]:first").each(function(){
				$(this).find("#add_option_div").attr('validation','false');
			});
		}
	});

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			clickable = true;
			return false;
		}
	}
	
	//var options_price_stock_option_divs = document.all.options_price_stock_option_div;
	var options_price_stock_option_divs = $("input[inputid=options_price_stock_option_div]");
	var options_price_stock_option_div_str = "|";
	

	for(j=0;j < options_price_stock_option_divs.length;j++){
			if(options_price_stock_option_divs[j].value){
				options_price_stock_option_div_str += options_price_stock_option_divs[j].value+"|";				
			}
	}
	for(j=0;j < options_price_stock_option_divs.length;j++){
			if(options_price_stock_option_divs[options_price_stock_option_divs.length-j-1].value && substr_count(options_price_stock_option_div_str, "|"+options_price_stock_option_divs[options_price_stock_option_divs.length-j-1].value+"|") > 1){
				alert(language_data['goods_input.js']['H'][language]);
				//'중복된 가격+재고 옵션구분명이 있습니다. 수정후 다시 시도해주세요'
				options_price_stock_option_divs[options_price_stock_option_divs.length-j-1].focus();
                clickable = true;
				return false;
			}
	}

	//var option_names = document.all.option_name;
	var option_names = $("input[inputid=option_name]");
	var option_names_str = "|";
	//var _option_names = new Array();
	
	var option_details_divs = "";
	var option_details_div_str = "";
	
	for(i=0; i<option_names.length; i++){
			option_names_str += option_names[i].value+"|";		
			//_option_names[i] = option_names[i].value
	}

	for(i=0;i < option_names.length;i++){
		if(option_names[option_names.length-i-1].value){
			if(option_names[option_names.length-i-1].value && substr_count(option_names_str, "|"+option_names[option_names.length-i-1].value+"|") > 1){
				alert('중복된 옵션명이 있습니다. 수정후 다시 시도해주세요');
				//'중복된 옵션명이 있습니다. 수정후 다시 시도해주세요'
				option_names[option_names.length-i-1].focus();
                clickable = true;
				return false;
			}else{
				option_details_divs = $("input[inputid=options_detail_option_div_"+i+"]");
				option_details_div_str = "|";

				for(j = 1; j < option_details_divs.length; j++){
					if(option_details_divs[j].value){
						option_details_div_str += option_details_divs[j].value+"|";	
					}	
				}

				for(j = 0; j < option_details_divs.length; j++){
					if(option_details_divs[option_details_divs.length-j-1].value && substr_count(option_details_div_str, "|"+option_details_divs[option_details_divs.length-j-1].value+"|") > 1){
						alert(language_data['goods_input.js']['I'][language]+"[2]");
						//'중복된 옵션구분명이 있습니다. 수정후 다시 시도해주세요'
						option_details_divs[option_details_divs.length-j-1].focus();
						clickable = true;
						return false;
					}
				}
			}
		}
	}
	
	//var display_option_titles = document.all.display_option_title;
	var display_option_titles = $("input[inputid=display_option_title]");
	var display_option_titles_str = "|";
	//var _option_names = new Array();
	
	var option_details_divs = "";
	var option_details_div_str = "";
	
	for(i = 0; i < display_option_titles.length; i++){
		display_option_titles_str += display_option_titles[i].value+"|";
	}

	for(i = 0; i < display_option_titles.length; i++){
		if(display_option_titles[display_option_titles.length-i-1].value){
			if(display_option_titles[display_option_titles.length-i-1].value && substr_count(display_option_titles_str, "|"+display_option_titles[display_option_titles.length-i-1].value+"|") > 1){
				alert(language_data['goods_input.js']['J'][language]);
				//'중복된 디스플레이 옵션명이 있습니다. 수정후 다시 시도해주세요'
				display_option_titles[display_option_titles.length-i-1].focus();
                clickable = true;
				return false;
			}
		}
	}

	//frm.listprice.value = filterNum(frm.listprice.value);
	//frm.sellprice.value = filterNum(frm.sellprice.value);
	frm.coprice.value = filterNum(frm.coprice.value);
	//doToggleText(frm);


	//console.log('end'); return false;
	frm.submit(); //일단 막음
}

// {{{ in_array
function in_array(needle, haystack, strict) {
    // Checks if a value exists in an array
    // 
    // +    discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_in_array/
    // +       version: 809.522
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: true

    var found = false, key, strict = !!strict;

    for (key in haystack) {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
            found = true;
            break;
        }
    }

    return found;
}// }}}


// {{{ substr_count
function substr_count( haystack, needle, offset, length ) {
    // Count the number of substring occurrences
    // 
    // +    discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_substr_count/
    // +       version: 810.819
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman
    // *     example 1: substr_count('Kevin van Zonneveld', 'e');
    // *     returns 1: 3
    // *     example 2: substr_count('Kevin van Zonneveld', 'K', 1);
    // *     returns 2: 0
    // *     example 3: substr_count('Kevin van Zonneveld', 'Z', 0, 10);
    // *     returns 3: false

    var pos = 0, cnt = 0;

    haystack += '';
    needle += '';
    if(isNaN(offset)) offset = 0;
    if(isNaN(length)) length = 0;
    offset--;

    while( (offset = haystack.indexOf(needle, offset+1)) != -1 ){
        if(length > 0 && (offset+needle.length) > length){
            return false;
        } else{
            cnt++;
        }
    }

    return cnt;
}// }}}


function copyImageCheckAll(){
	//var copy_image = document.all.copy_img;
	var copy_image = $("input[inputid=copy_img]");
	//var all_bool = document.all.copy_allimg.checked
	var all_bool = $("input[inputid=copy_allimg]").attr("checked");
	for(i=0;i < copy_image.length;i++){
		if(all_bool){
			copy_image[i].checked = true;	
		}else{
			copy_image[i].checked = false;	
		}
	}
}

function copyAddImageCheckAll(num){
	//var add_copy_img = document.all.add_copy_img;
	var add_copy_img = $("input[inputid=add_copy_img_"+num+"]");
	var add_copy_allimg = $("input[inputid=add_copy_allimg_"+num+"]");
	//var all_bool = document.all.add_copy_allimg.checked
	var all_bool = $("input[inputid=add_copy_allimg_"+num+"]").attr("checked");

	for(i=0;i < add_copy_img.length;i++){
		if(all_bool){
			add_copy_img[i].checked = true;	
		}else{
			add_copy_img[i].checked = false;	
		}
	}
}


function ChnageImg(img_path, img_type)
{
	document.getElementById('viewimg'+img_type).innerHTML = "<img src='"+img_path+"' id=chimg>";
	//document.getElementById('viewimg').innerHTML = "<img src='"+img_path+"/"+vsize+"_"+vid+".gif' id=chimg>\n\n<input type=file name='"+vsize+"img' size=30 style='font-size:8pt'>";
}

function AddImageView(img_path)
{
	document.getElementById('add_image_view').innerHTML = "<img src='"+img_path+"' id=addimg valign=middle>";
	//document.getElementById('viewimg').innerHTML = "<img src='"+img_path+"/"+vsize+"_"+vid+".gif' id=chimg>\n\n<input type=file name='"+vsize+"img' size=30 style='font-size:8pt'>";
}

function AddImageAct(frm,act)
{
	frm.act.value = act;
	frm.submit();
}

function AddOption(frm){
	frm.submit();
}

function deleteAddimage(act,id,pid)
{
	window.frames["act"].location.href='./img.add.php?act='+act+'&id='+id+'&pid='+pid; 
	//document.getElementById("act").src='./img.add.php?act='+act+'&id='+id+'&pid='+pid;
}

function showTabContents(vid, tab_id){
	var area = new Array('category_search','keyword_search');
	var tab = new Array('tab_01','tab_02');
	
	for(var i=0; i<area.length; ++i){
		
		if(area[i]==vid){
			document.getElementById(vid).style.display = "block";			
			document.getElementById(tab_id).className = "on";
		}else{			
			document.getElementById(area[i]).style.display = "none";
			document.getElementById(tab[i]).className = "";
		}
	}
}

function showTabImageInfo(vid, tab_id){
	var area = new Array('image_type_01','image_type_02');
	var tab = new Array('image_typetab_01','image_typetab_02');
	
	for(var i=0; i<area.length; ++i){
		
		if(area[i]==vid){
			document.getElementById(vid).style.display = "block";			
			document.getElementById(tab_id).className = "on";
		}else{			
			document.getElementById(area[i]).style.display = "none";
			document.getElementById(tab[i]).className = "";
		}
	}
}

function showTabBasicinfo(vid, tab_id){
	var area = new Array('web_basicinfo_div','mobile_basicinfo_div','english_web_basicinfo_div','english_mobile_basicinfo_div');
	var tab = new Array('b_tab_01','b_tab_02','b_tab_03','b_tab_04');
	
	for(var i=0; i<area.length; ++i){
		
		if(area[i]==vid){
			document.getElementById(vid).style.display = "block";			
			document.getElementById(tab_id).className = "on";
		}else{			
			document.getElementById(area[i]).style.display = "none";
			document.getElementById(tab[i]).className = "";
		}
	}
}

function showPriceTabContents(vid, tab_id){
	var area = new Array('price_info','detail_price_info', 'price_history_info');
	var tab = new Array('p_tab_01','p_tab_02','p_tab_03');
	for(var i=0; i<area.length; ++i){
		
		if(area[i]==vid){
			document.getElementById(vid).style.display = "block";			
			document.getElementById(tab_id).className = "on";
		}else{			
			document.getElementById(area[i]).style.display = "none";
			document.getElementById(tab[i]).className = "";
		}
	}
}

function showCategoryTab(vid, tab_id){
	var area = new Array('select_category','search_category');
	var tab = new Array('category_tab_01','category_tab_02');
	
	for(var i=0; i<area.length; ++i){
		
		if(area[i]==vid){
			document.getElementById(vid).style.display = "";			
			document.getElementById(tab_id).className = "on";
		}else{			
			document.getElementById(area[i]).style.display = "none";
			document.getElementById(tab[i]).className = "";
		}
	}
}



function showStandardCategoryTab(vid, tab_id){
	var area = new Array('select_standard_category','search_standard_category');
	var tab = new Array('standard_category_tab_01','standard_category_tab_02');
	
	for(var i=0; i<area.length; ++i){
		
		if(area[i]==vid){
			document.getElementById(vid).style.display = "";			
			document.getElementById(tab_id).className = "on";
		}else{			
			document.getElementById(area[i]).style.display = "none";
			document.getElementById(tab[i]).className = "";
		}
	}
}


function dpCheckOptionData(frm){
	// if(frm.dp_title.value.length < 1){
	// 	alert(language_data['goods_input.js']['K'][language]);
	// 	//'옵션구분값을 입력해주세요'
	// 	frm.option_div.focus();
	// 	return false;
	// }
	
	if(frm.dp_desc.value.length < 1){
		alert(language_data['goods_input.js']['L'][language]);	
		//'옵션별 비회원가를 입력해주세요'
		frm.option_price.focus();
		return false;
	}
	
	return true;
}


function CheckOptionData(frm){
	var option_kind = frm.option_kind.value
	if(option_kind == 'b' || option_kind == 'p'){
		if(frm.opn_ix.value == ""){
			alert(language_data['goods_input.js']['M'][language]);	
			//'옵션이름을 선택해주세요'
			frm.option_div.focus();
			return false;
		}
		
		if(frm.option_div.value.length < 1){
			alert(language_data['goods_input.js']['K'][language]);	
			//'옵션구분값을 입력해주세요'
			frm.option_div.focus();
			return false;
		}
		
		if(frm.option_price.value.length < 1){
			alert(language_data['goods_input.js']['L'][language]);	//'옵션별 비회원가를 입력해주세요'
			frm.option_price.focus();
			return false;
		}

	}else if(option_kind == 's'){
		if(frm.opn_ix.value == ""){
			alert(language_data['goods_input.js']['M'][language]);	//'옵션이름을 선택해주세요'
			frm.option_div.focus();
			return false;
		}
		
		if(frm.option_div.value.length < 1){
			alert(language_data['goods_input.js']['N'][language]);	//'옵션구분값을 입력해주세요'
			frm.option_div.focus();
			return false;
		}
	}else{
		alert(language_data['goods_input.js']['M'][language]);	//'옵션이름을 선택해주세요'
		return false;	
	}
	
	return true;
}

function deleteOption(act,id,pid, opn_ix){
	window.frames["act"].location.href='./option.act.php?act='+act+'&id='+id+'&pid='+pid+'&opn_ix='+opn_ix; 
	//document.getElementById("act").src='./option.act.php?act='+act+'&id='+id+'&pid='+pid+'&opn_ix='+opn_ix;
}

function deleteDisplayOption(act,dp_ix,pid){
	window.frames["act"].location.href='./display_option.act.php?act='+act+'&dp_ix='+dp_ix+'&pid='+pid; 
	//document.getElementById("act").src='./display_option.act.php?act='+act+'&dp_ix='+dp_ix+'&pid='+pid;
}

//function UpdateOption(option_id, option_div, option_price,option_m_price,option_d_price,option_a_price, option_stock, option_safestock, option_code){	
function UpdateOption(option_id, option_div, option_price, option_stock, option_safestock, option_code){	
	var frm = document.forms["optionform"];
	frm.act.value ='update';
	
	frm.option_id.value = option_id;
	//frm.option_name.value = option_name;
	frm.option_div.value = option_div;
	frm.option_price.value = option_price;
	//frm.option_m_price.value = option_m_price;
	//frm.option_d_price.value = option_d_price;
	//frm.option_a_price.value = option_a_price;
	frm.option_stock.value = option_stock;
	frm.option_safestock.value = option_safestock;
	frm.option_code.value = option_code;
	
	//document.frames["act"].location.href='./option.act.php?act='+act+'&id='+id+'&pid='+pid; 
}

function UpdateDisplayOption(dp_ix, dp_title, dp_desc){	
	var frm = document.forms["dispoptionform"];
	frm.act.value ='update';
	
	frm.dp_ix.value = dp_ix;	
	frm.dp_title.value = dp_title;
	frm.dp_desc.value = dp_desc;
	
}

function FormatNumber2_old(num){
        // 만든이:김인현(jasmint@netsgo.com)
        // ie5.5이상에서 사용할것
        fl=""
        if(isNaN(num)) { alert(language_data['goods_input.js']['Q'][language]);return 0}
		//"문자는 사용할 수 없습니다."
        if(num==0) return num
        
        if(num<0){ 
                num=num*(-1)
                fl="-"
        }else{
                num=num*1 //처음 입력값이 0부터 시작할때 이것을 제거한다.
        }
        num = new String(num)
        temp=new Array()
        co=3
        num_len=num.length
        while (num_len>0){
                num_len=num_len-co
                if(num_len<0){co=num_len+co;num_len=0}
                temp.unshift(num.substr(num_len,co))
        }
        return fl+temp.join(",")
}

function FormatNumber2(num){
        // 만든이:김인현(jasmint@netsgo.com)
        fl=""
        if(isNaN(num)) { alert(language_data['goods_input.js']['Q'][language]);return 0}
		//"문자는 사용할 수 없습니다."
        if(num==0) return num
        
        if(num<0){ 
                num=num*(-1)
                fl="-"
        }else{
                num=num*1 //처음 입력값이 0부터 시작할때 이것을 제거한다.
        }
        num = new String(num)
        temp=""
        co=3
        num_len=num.length
        while (num_len>0){
                num_len=num_len-co
                if(num_len<0){co=num_len+co;num_len=0}
                temp=","+num.substr(num_len,co)+temp
        }
        return fl+temp.substr(1)
}

function FormatNumber3(num){
        num=new String(num)
        num=num.replace(/,/gi,"")
      //  pricecheckmode = false;
        
        return FormatNumber2(num)
}

function num_check() {
        // ie에서만 작동
        var keyCode = event.keyCode
        if ((keyCode < 48 || keyCode > 57) && keyCode != 8){
                alert(language_data['goods_input.js']['Q'][language]+"["+keyCode+"]")
                event.returnValue=false
				//"문자는 사용할 수 없습니다."
        }
        
}



function round(num,ja) {
        ja=Math.pow(10,ja)
        return Math.round(num * ja) / ja;
}

function Round2(Num, Position , Base){
//Num = 반올림할 수
//Position = 반올림할 자릿수(정수로만)
//Base = i 이면 소숫점위의 자릿수에서, f 이면 소숫점아래의 자릿수에서 반올림

	if(Position == 0){ 
	        //1이면 소숫점1 자리에서 반올림
	return Math.round(Num); 
	}else if(Position > 0){
	                var cipher = '1';
	                for(var i=0; i < Position; i++ )
	                                cipher = cipher + '0';
	
	                var no = Number(cipher);
	
	                if(Base=="F"){
	                                //소숫점아래에서 반올림                        
	                                return Math.round(Num * no) / no;
	                }else{
	                                //소숫점위에서 반올림.                        
	                                return Math.round(Num / no) * no;
	                }
	 }else{
	                alert(language_data['goods_input.js']['R'][language]);
					//"자릿수는 정수로만 구분합니다."
	                return false;
	 }

}

function Floor(Num, Position , Base){//내림 함수 kbk 13/07/17
//Num = 내림할 수
//Position = 내림할 자릿수(정수로만)
//Base = i 이면 소숫점위의 자릿수에서, f 이면 소숫점아래의 자릿수에서 내림

	if(Position == 0){ 
	        //1이면 소숫점1 자리에서 반올림
	return Math.floor(Num); 
	}else if(Position > 0){
	                var cipher = '1';
	                for(var i=0; i < Position; i++ )
	                                cipher = cipher + '0';
	
	                var no = Number(cipher);
	
	                if(Base=="F"){
	                                //소숫점아래에서 내림                        
	                                return Math.floor((Num * no) / no);
	                }else{
	                                //소숫점위에서 내림                       
	                                return Math.floor(Num / no) * no;
	                }
	 }else{
	                alert(language_data['goods_input.js']['R'][language]);
					//"자릿수는 정수로만 구분합니다."
	                return false;
	 }

}


function commaSplit(srcNumber) {
	var txtNumber = '' + srcNumber;
	if (isNaN(txtNumber) || txtNumber == "") {
		//alert(language_data['goods_input.js']['S'][language]);
		//"숫자만 입력 하세요"
		return 0;
	}
	else {
		var rxSplit = new RegExp('([0-9])([0-9][0-9][0-9][,.])');
		var arrNumber = txtNumber.split('.');
		arrNumber[0] += '.';
		do {
			arrNumber[0] = arrNumber[0].replace(rxSplit, '$1,$2');
		} while (rxSplit.test(arrNumber[0]));
		
		if (arrNumber.length > 1) {
			return arrNumber.join('');
		}else {
			return arrNumber[0].split('.')[0];
		}
	}
}


function SampleProductInsert(){
	var frm = document.forms['product_input'];
	frm.pname.value = "sample product";
	frm.pcode.value = "pdc00001";
	frm.company.value = "(주) 몰스토리";
	frm.sellprice.value = "10000";
	frm.prd_member_price.value = "9000";
	frm.prd_dealer_price.value = "8000";
	frm.prd_agent_price.value = "7000";
	frm.coprice.value = "5000";
	
}
/*
function copyPrice(frm, copy_price, step){
	
	if(step == 1){
		if(copy_price == ""){
			alert(language_data['goods_input.js']['T'][language]);	
			//'구매단가(공급가)를 입력후 복사 버튼을 클릭해주세요'
			return false;
		}
		frm.listprice.value = copy_price;
		frm.sellprice.value = copy_price;	
	}else if(step == 2){
		if(copy_price == ""){
			alert(language_data['goods_input.js']['U'][language]);	
			//'정가를 입력후 복사 버튼을 클릭해주세요'
			return false;
		}
		frm.sellprice.value = copy_price;
			
	}
}
*/

function copyPrice(frm, copy_price, step,commi){
	
	if(step == 2){
		if(copy_price == ""){
			alert(language_data['goods_input.js']['V'][language]);	
			//'판매가를 입력후 복사 버튼을 클릭해주세요'
			return false;
		}
		var per = 100;
		//alert(commi);
		frm.sellprice.value = copy_price;
		//frm.coprice.value = copy_price;//- (copy_price*(commi/per));
		//frm.bcoprice.value = copy_price;// - (copy_price*(commi/per));
		
		//commissionChange(frm);
	}else if(step == 1){
		frm.sellprice.value = copy_price;
		//commissionChange(frm);
		//frm.price.value = copy_price;
	}
	else if(step == 5) //프리미엄가 계산
	{
		frm.premiumprice.value = copy_price * 0.8;
	}
	else if(step == 6) //프리미엄가 계산
	{
		/*
		$("input[id=add_option_listprice]").each(function(){
			$("input[id=add_option_listprice]").val(frm.listprice.value);
			$("input[id=add_option_sellprice]").val(frm.sellprice.value);
			$("input[id=add_option_premiumprice]").val(frm.premiumprice.value);
		});
*/
		
	}
}

function commi(commi){
	var frm = document.product_input.listprice.value;
	document.product_input.coprice.value = frm - (frm*(commi/100));
	document.product_input.bcoprice.value = frm - (frm*(commi/100));
}

function commissionChange(frm){	
	//var listprice = filterNum(frm.listprice.value);

	//수수료 정보 불러오는 부분 수정 2014-04-07 이학봉 (입점업체를 선택햇을경우 입점업체에 해당되는 정책을 못불러옴)
	var company_id = $('#company_id').val();
	var pid = $('input[name=id]').val();
	var one_commission_use = $('input[name=one_commission]:checked').val();
	
	if(eval("frm.one_commission[0].checked")){
		frm.commission.value = frm.commission.getAttribute("company_commission");// getAttribute 사용 kbk
		frm.wholesale_commission.value = frm.wholesale_commission.getAttribute("whole_company_commission");// getAttribute 사용 kbk
		getSellerSetup(company_id,one_commission_use,pid);	//014-04-07 이학봉 추가
		$('tr #account_info_div').css('display','none');
	}else{
		
		frm.commission.value = frm.commission.getAttribute("goods_commission");// getAttribute 사용 kbk
		frm.wholesale_commission.value = frm.wholesale_commission.getAttribute("whole_goods_commission");// getAttribute 사용 kbk
		getSellerSetup(company_id,one_commission_use,pid);	//014-04-07 이학봉 추가
		$('tr #account_info_div').css('display','');
	}
}

function showLayer(obj_id,group_code){
	if($(obj_id).style.display == "none"){
		//alert($(obj_id).gorup_code);
		select_gorup_code = group_code;
		$(obj_id).style.top = event.y+document.body.scrollTop+10;
		$(obj_id).style.display = 'block';
		selectedGoodsView("selected");
	}else{
		$(obj_id).style.display = 'none';
		preRow = null;
		deleteWhole(false);		
	}
	
}

function dateSelect(id){
	if(id == 1){
		
		document.getElementById('FromYY').disabled = true;
		document.getElementById('FromMM').disabled = true;
		document.getElementById('FromDD').disabled = true;
		document.getElementById('FromHH').disabled = true;
		document.getElementById('FromII').disabled = true;
		document.getElementById('ToYY').disabled = true;
		document.getElementById('ToMM').disabled = true;
		document.getElementById('ToDD').disabled = true;
		document.getElementById('ToHH').disabled = true;
		document.getElementById('ToII').disabled = true;
		document.getElementById('start_price').disabled = true;
	}else{
		document.getElementById('FromYY').disabled = false;
		document.getElementById('FromMM').disabled = false;
		document.getElementById('FromDD').disabled = false;
		document.getElementById('FromHH').disabled = false;
		document.getElementById('FromII').disabled = false;
		document.getElementById('ToYY').disabled = false;
		document.getElementById('ToMM').disabled = false;
		document.getElementById('ToDD').disabled = false;
		document.getElementById('ToHH').disabled = false;
		document.getElementById('ToII').disabled = false;
		document.getElementById('start_price').disabled = false;
	}
}


function showOptionTabContents(vid, tab_id){
	var area = new Array('stock_option_zone','box_option_zone','codi_option_zone');//,'set_option_zone','set2_option_zone'
	var tab = new Array('option_tab_01','option_tab_02','option_tab_05');//,'option_tab_03','option_tab_04'
	
	for(var i=0; i<area.length; ++i){
		
		if(area[i]==vid){
			document.getElementById(vid).style.display = "block";			
			document.getElementById(tab_id).className = "changeable_area on";
			$('#'+vid).find("#add_option_use").attr("checked",true);
			$("input[name=use_option_type]").val(area[i].replace('_zone',''));
		}else{			
			document.getElementById(area[i]).style.display = "none";
			document.getElementById(tab[i]).className = "changeable_area";
			$('#'+area[i]).find("#add_option_use").attr("checked",false);
		}
	}
}

function ShowGoodsTypeInfo(vid,update_yn){
	$('.changeable_area').hide();
	//alert(vid);
	$("#sell_priod_sdate").attr("validation", "false");
	$("#sell_priod_edate").attr("validation", "false");
	$("#free_gift_price").find('input').attr("validation", "false");	

	if(vid == "GoodsInfo" || vid =='sos' || vid == "buyingServiceInfo" || vid == "planGoodsInfo" || vid == "group_goods"){
		//$("#basic_option_zone").css('display','block'); // 논의필요(상품기본옵션)
        $('.mGift').show();
        $('.mSet').hide();
        $('.mNormal').show();

		if($("input[name=stock_use_yn]:checked").val() != 'N'){
			$("#stock_option_zone").css('display','block'); 
			$("#option_tab_01").css('display','block'); 
		}

		$("#add_option_zone_table").css('display','block');	
		$("#product_mult_rate_div").css('display','');	
		displayFreeGift('N');	//사은품 상품일경우 숨김여부 2014-04-08 

		$("#use_option_type").val('non_set_option');			//세트,사은품 상품 제외 상품구분은 가격재고만 사용하기에 기본 옵션으로 지정
		$("#get_set_product_basicinfo").css('display','none');	//세트상품 이미지 불러오기(상품상세정보)
        showTabBasicinfo('web_basicinfo_div','b_tab_01'); //2개나오는 문제 고정
		if(vid == "group_goods"){
			$("#sell_priod_sdate").attr("validation", "true");
			$("#sell_priod_edate").attr("validation", "true");
		}

		$("#option_type_td").attr("colspan","3");
		$("#gift_qty_td1").css('display','none');
		$("#gift_qty_td2").css('display','none');

        $('#no_sell_date').css('display','block');
        $('#is_sell_date_1 > input, #is_sell_date_1 > select').attr('validation', false);
        $('#is_sell_date_0').click();

        //$('#mandatory_select_1').attr("validation", true);
        //$('#mandatory_select_1_global').attr("validation", true);
        //
        // $('#mandatory_info_zone').find('input[type=text][name^=mandatory_info]').attr("validation", true);
        // $('#mandatory_info_zone_global').find('input[type=text][name^=mandatory_info_global]').attr("validation", true);

	}else if(vid == "setGoodsInfo"){
        $('.mGift').show();
        $('.mSet').show();
        $('.mNormal').hide();
		//$("#basic_option_zone").css('display','block'); // 논의필요(상품기본옵션)
		//$("#option_tab_02").css('display','block');
		//$("#option_tab_03").css('display','block');  // 숨겨진 기능
		//$("#option_tab_04").attr('class','changeable_area');
		$("#option_tab_04").css('display','block'); 
		$("#option_tab_05").css('display','block'); 
		
		if($("#use_option_type").val() == "non_set_option"){	//신규상품 등록시 상품구분 세트상품클릭시 기본값 지정 2014-04-15 이학봉
			//$("#use_option_type").val('box_option'); //이거 넣으면 꼬임... 2019 02 15
            showOptionTabContents('codi_option_zone','option_tab_05'); //기본 선택 변경 - 초이스박스 ==> 세트 묶음 상품 옵션
		}

		if($('#use_option_type').val() == "set2_option"){
			$('#set2_option_zone').show();
			$("#option_tab_04").attr('class','changeable_area set_options on');
		}else if($('#use_option_type').val() == "codi_option"){
			$('#codi_option_zone').show();
			$("#option_tab_05").attr('class','changeable_area set_options on');
		}else{
			$("#box_option_zone").css('display','block');	
			$("#option_tab_02").attr('class','changeable_area on');
		}
		//$("#set_option_zone").css('display','block');
		$("#get_set_product_basicinfo").css('display','inline');	//세트상품 이미지 불러오기(상품상세정보)

		displayFreeGift('N');	//사은품 상품일경우 숨김여부 2014-04-08
        showTabBasicinfo('web_basicinfo_div','b_tab_01'); //2개나오는 문제 고정
        //showOptionTabContents('set2_option_zone','option_tab_04'); //기본 선택 변경 - 초이스박스 ==> 세트 묶음 상품 옵션

		$("#option_type_td").attr("colspan","3");
		$("#gift_qty_td1").css('display','none');
		$("#gift_qty_td2").css('display','none');

        $('#no_sell_date').css('display','block');
        $('#is_sell_date_1 > input, #is_sell_date_1 > select').attr('validation', false);
        $('#is_sell_date_0').click();

        //$('#mandatory_select_1').attr("validation", true);
        //$('#mandatory_select_1_global').attr("validation", true);
        //
        // $('#mandatory_info_zone').find('input[type=text][name^=mandatory_info]').attr("validation", true);
        // $('#mandatory_info_zone_global').find('input[type=text][name^=mandatory_info_global]').attr("validation", true);

	}else if(vid == "freeGift"){
		$('.mGift').hide();
		$("#option_tab_04").css('display','block'); 
		$("#option_tab_04").attr('class','changeable_area on');
		$("#set2_option_zone").css('display','block');	
		displayFreeGift('Y');	//사은품 상품일경우 숨김여부 2014-04-08 

		$("#use_option_type").val('set2_option');					//신규상품 등록시 상품구분 세트상품클릭시 기본값 지정 2014-04-15 이학봉
		$("#get_set_product_basicinfo").css('display','none');		//세트상품 이미지 불러오기(상품상세정보)
		$("#price_info_box").find('input').attr("validation", "false");
		$("#free_gift_price").find('input').attr("validation", "true");

		$("#option_type_td").attr("colspan","");
		$("#gift_qty_td1").css('display','');
		$("#gift_qty_td2").css('display','');

		$('#no_sell_date').css('display','none');
		$('#is_sell_date_1').click();
		$('#is_sell_date_1 > input, #is_sell_date_1 > select').attr('validation', true);


        $('#mandatory_select_1').attr("validation", false);
        $('#mandatory_select_1_global').attr("validation", false);
        //
        // $('#mandatory_info_zone').find('input[type=text][name^=mandatory_info]').attr("validation", false);
        // $('#mandatory_info_zone_global').find('input[type=text][name^=mandatory_info_global]').attr("validation", false);


	}else{

	}

	if(vid =='sos'){
		$('#stock_use_q_area').show();
        $('#stock_use_y_area').hide();
        $('#stock_use_q').attr('checked',true);
        $('#stock_use_y').attr('checked',false);
	}else{
        $('#stock_use_q_area').hide();
        $('#stock_use_y_area').show();
        $('#stock_use_q').attr('checked',false);
        $('#stock_use_y').attr('checked',true);
	}

	if(vid == "group_goods"){
		//공동구매 상품일경우 판매기간은 무조건 사용으로 체크 2014-06-17 이학봉
		$('#is_sell_date_1').trigger("click");//attr('checked','checked');
		$('#is_sell_date_0').hide();
		$('label[for=is_sell_date_0]').hide();

		check_is_sell_date('G');
	}else{
		//공동구매 상품외 판매기간 사용하는 상품에는 미적용 노출 2014-07-16 이학봉
		
		$('#is_sell_date_0').show();
		$('label[for=is_sell_date_0]').show();
		//$('#is_sell_date_0').attr('checked','checked');
	}
}

function check_is_sell_date(type){
	
	var value = $('input[name=is_sell_date]:checked').val();
	
	$('*[id^=sell_priod]').attr('disabled',false);
	
}

function ShowGoodsTypeInfo2(vid,update_yn){//update_yn 추가하여 상품 옵션 디스플레이 컨트롤 kbk 13/12/05
	var area = new Array('GoodsInfo','setGoodsInfo','buyingServiceInfo','AuctionInfo','hotcon','CarArea','RealEstateArea','TravelHotelArea','TravelHotelArea','TravelTourismArea','scGoodsInfo','subscription','local_delivery','freeGift','planGoodsInfo');

	for(var i=0; i<area.length; ++i){
		if(vid == "setGoodsInfo" || vid == "subscription"){
			$("#price_option_zone").css('display','none'); // 가격 관리
			$("#targetNumber_area").css('display','none');
			
			if(vid == "subscription"){
				
				$("#basic_option_zone").css('display','none');
				$("#subsciption_input_area").css('display','block');
				$("#stock_option_zone").css('display','none');
				$("#option_tab_01").css('display','none');
				$("#option_tab_01").attr('class','off');
				$("#set2_option_zone").css('display','block');
				$("#option_tab_04").attr('class','on');
				$("#option_tab_05").attr('class','on');
			}else{
				$("#basic_option_zone").css('display','block');	
				$("#subsciption_input_area").css('display','none');
			}

			if(vid == "setGoodsInfo"){ //20130830 Hong
				$("#local_delivery_area").css('display','none');
				$("#no_price_option_zone").css('display','block');			
				$("#get_set_product_basicinfo").css('display','inline');
				if(update_yn!="Y") $("#setGoodsInfoSet").css('display','block');		
				if(update_yn!="Y") $("#set_goods_item_btn").css('display','block');	
				$("#stock_option_zone").css('display','none');	
				if(update_yn!="Y") $("#box_option_zone").css('display','block');	
				if(update_yn!="Y") $("#set2_option_zone").css('display','none');	
				
				$("#option_tab_01").css('display','none');	
				$("#option_tab_02").css('display','block');	
				if(update_yn!="Y") $("#option_tab_02").attr('class','on');	
				//$("#option_tab_03").css('display','block');	
				$("#option_tab_04").attr('class','');	
				$("#option_tab_04").css('display','block');	
				$("#option_tab_05").css('display','block');	
				if($("#use_option_type").val() == "non_set_option"){
					$("#use_option_type").val('box_option');
				}

				$("#option_use_setting").css('display','none');	//세트상품일경우 가격재고 옵션 사용설정을 숨김
			}

			$("#add_option_zone").css('display','none');	

			//세트 상품의 경우 쿠폰 사용 못하도록 고정 kbk 13/07/17
			$("#coupon_use_n").attr("checked",true);
			$("input[name=coupon_use_yn]").attr("disabled",true);

			displayFreeGift('N');	//사은품 상품일경우 숨김여부 2014-04-08 
		//}else if(vid == "subscription"){
		//	$("#price_option_zone").css('display','block');	
		}else if(vid == "freeGift"){
			displayFreeGift('Y');	//사은품 상품일경우 숨김여부 2014-04-08 

			$("#local_delivery_area").css('display','none');		//??? 없는 아이디
			$("#no_price_option_zone").css('display','block');		//??? 없는 아이디
			$("#get_set_product_basicinfo").css('display','none');	//세트상품 이미지 불러오기(상품상세정보)

			if(update_yn!="Y") $("#setGoodsInfoSet").css('display','block');		//??? 없는 아이디	
			if(update_yn!="Y") $("#set_goods_item_btn").css('display','block');		//??? 없는 아이디
			$("#stock_option_zone").css('display','none');							//가격_재고관리 옵션
			if(update_yn!="Y") $("#box_option_zone").css('display','none');			//초이스박스옵션
			if(update_yn!="Y") $("#set2_option_zone").css('display','block');		//세트 묶음상품 옵션
			
			$("#option_tab_01").css('display','none');		//가격 + 재고관리 옵션 탭
			$("#option_tab_02").css('display','none');		//초이스 박스 옵션 탭
			$("#option_tab_04").css('display','block');		//세트 묶음 상품 옵션 탭
			if(update_yn!="Y") $("#option_tab_04").attr('class','on');		//사은품 상품일경우 세트 묶음상품 옵션만 사용가능
			$("#option_tab_05").css('display','none');		//코디 옵션 탭

			if($("#use_option_type").val() == "non_set_option"){
				$("#use_option_type").val('set2_option');		//기본으로 노출 시킬 묶음 세트 상품의 아이디값
			}
		}else{ 

			$("#price_option_zone").css('display','block');
			$("#targetNumber_area").css('display','');
			$("#basic_option_zone").css('display','block');	

			$("#no_price_option_zone").css('display','none');
			$("#get_set_product_basicinfo").css('display','none');
			$("#setGoodsInfoSet").css('display','none');	
			$("#set_goods_item_btn").css('display','none');
			
			$("#option_use_setting").css('display','');	//세트상품이 아닐경우 노출
			var use_option = $('input[name=option_use]:checked').val();	//가격재고 옵션 사용설정에 따라 변경됨 미사용 = n 일경우 가격재고 옵션 숨김

			if(use_option == 'n'){		
				$("#stock_option_zone").css('display','none');	
				$("#option_tab_01").css('display','none');	 
			}else{
				$("#option_tab_01").attr('class','on');	
				$("#stock_option_zone").css('display','block');	
				$("#option_tab_01").css('display','block');	
			}

			$("#box_option_zone").css('display','none');	
			
			$("#set2_option_zone").css('display','none');	

			$("#option_tab_02").css('display','none');	
			//$("#option_tab_03").css('display','none');	
			if(vid == "scGoodsInfo"){		
				$("#option_tab_04").css('display','block');
				$("#option_tab_05").css('display','block');
			}else{
				$("#option_tab_04").css('display','none');
				$("#option_tab_05").css('display','none');
			}

			$("#subsciption_input_area").css('display','none');
			if(vid == "local_delivery" || vid == "GoodsInfo"){
				$("#add_option_zone").css('display','block');
				$("#local_delivery_area").css('display','block');
			}else{
				$("#add_option_zone").css('display','none');
				$("#local_delivery_area").css('display','none');
			}

			$("input[name=coupon_use_yn]").attr("disabled",false);

			displayFreeGift('N');	//사은품 상품일경우 숨김여부 2014-04-08 
		}

		if(vid == "buyingServiceInfo"){			
			$("#buyingServiceInfTable").css('display','');
			$("#buyingServiceClearanceType").css('display','');
			
		}else{
			$("#buyingServiceInfTable").css('display','none');	
			$("#buyingServiceClearanceType").css('display','none');	
		}

		if(vid != "subscription" && vid != "scGoodsInfo") {//스크립트 에러 발생하여 영역 노출이 제대로 되지 않아서 조건 추가함 kbk 13/09/10
			if(area[i]==vid){
				if(vid!="local_delivery"){
					document.getElementById(vid).style.display = 'block';//vid 가 local_delivery 인 경우 해당 영역이 테이블로 구성되어 있어서 block 처리하면 html이 깨짐 kbk 13/09/10
				}else{
					document.getElementById(vid).style.display = '';
				}//document.getElementById(tab_id).className = 'on';
			}else{
				if(document.getElementById(area[i])){
					document.getElementById(area[i]).style.display = 'none';
				}
				//document.getElementById(tab[i]).className = '';
			}
		}
	}
	
	$(".car_info").each(function(){
		if(vid == "CarArea"){
			$(this).attr('validation','true');
		}else{
			$(this).attr('validation','false');
		}
	});

	$(".property_info").each(function(){
		if(vid == "RealEstateArea"){
			$(this).attr('validation','true');
		}else{
			$(this).attr('validation','false');
		}
	});

	$(".travel_hotel_info").each(function(){
		if(vid == "TravelHotelArea"){
			$(this).attr('validation','true');
		}else{
			$(this).attr('validation','false');
		}
	});

	$(".travel_tour_info").each(function(){
		if(vid == "TravelTourismArea"){
			$(this).attr('validation','true');
		}else{
			$(this).attr('validation','false');
		}
	});

}

function ShowGiftTypeInfo(vid){
	if(vid == "enable"){
		document.getElementById('giftshopinfo').style.display = "block";
	}else{
		document.getElementById('giftshopinfo').style.display = "";
	}
}


function deliveryTypeView(type){

	var frm=document.product_input;
	if(type == "2"){

		$('#policy_input_retail').css('display','');
		$('#policy_input_whole').css('display','');
		$('#policy_text_retail').css('display','none');
		$('#policy_text_whole').css('display','none');

		//document.getElementById('policy_input_retail').style.display = "";
		//document.getElementById('policy_input_whole').style.display = "";
		//document.getElementById('policy_text_retail').style.display = "none";
		//document.getElementById('policy_text_whole').style.display = "none";

		$('input[name=dt_ix_retail]').attr('disabled',true);
		$('input[name=dt_ix_whole]').attr('disabled',true);

		$('select[name=dt_ix_retail]').attr('disabled',false);
		$('select[name=dt_ix_whole]').attr('disabled',false);
		
		$('#delivery_template_img_r').css('display','');
		$('#delivery_template_img_w').css('display','');
		//document.getElementById("delivery_product_policy_1").checked=true;
		//no_pay_delivery(1);

		$('select[name^=dt_ix]').attr('disabled',false);
		$('select[name^=dt_ix]').attr('validation',true);
		$('input[id^=template_check]').attr('disabled',false);
		$('input[id^=template_basic_dt]').attr('disabled',true);

	}else{

		$('#policy_input_retail').css('display','none');
		$('#policy_input_whole').css('display','none');
		
		$('#policy_text_retail').css('display','');
		$('#policy_text_whole').css('display','');

		//document.getElementById('policy_input_retail').style.display = "none";
		//document.getElementById('policy_input_whole').style.display = "none";
		//document.getElementById('policy_text_retail').style.display = "";
		//document.getElementById('policy_text_whole').style.display = "";

		$('input[name=dt_ix_retail]').attr('disabled',false);
		$('input[name=dt_ix_whole]').attr('disabled',false);

		$('select[name=dt_ix_retail]').attr('disabled',true);
		$('select[name=dt_ix_whole]').attr('disabled',true);
		//document.getElementById("delivery_product_policy_1").checked=true;
		//no_pay_delivery(1);
		$('#delivery_template_img_r').css('display','none');
		$('#delivery_template_img_w').css('display','none');

		$('select[name^=dt_ix]').attr('disabled',true);
		$('select[name^=dt_ix]').attr('validation',false);
		$('input[id^=template_check]').attr('disabled',true);
		$('input[id^=template_basic_dt]').attr('disabled',false);

		if($('input[name=delivery_type]:checked').val() == 1){
			var company_id = $('#ori_company_id').val();
		}else{
			var company_id = $('#company_id').val();
		}

		$.ajax({ 
			type: 'GET', 
			data: {'act' : 'showDeliveryText','company_id': company_id},
			url: './update_delivery_policy.php',  
			dataType: 'html', 
			success: function(result){ 
				$('#policy_text_retail').html(result);
			} 
		}); 
	}
}

function no_pay_delivery(gubun) { //kbk
	var frm=document.product_input;
	frm.delivery_price.value="";
	if(gubun==1) {
		frm.delivery_price.disabled=true;
		frm.delivery_price.setAttribute("validation","false");
		/*frm.free_delivery_yn[0].disabled=false;
		frm.free_delivery_yn[0].checked=true;
		frm.free_delivery_yn[1].disabled=true;
		frm.free_delivery_count.value="";
		frm.free_delivery_count.disabled=true;*/
		document.getElementById("delivery_package_n").checked=true;
		//document.getElementById("delivery_package_y").style.display="";
		//document.getElementById("package_input").style.display="";  2012-07-20 jk 주석 개별 배송 유료배송 선택에따른 디스플레이 효과부분 제거
		//document.getElementById("package_text").style.display="none";  2012-07-20 jk 주석 개별 배송 유료배송 선택에따른 디스플레이 효과부분 제거
	} else {
		frm.delivery_price.disabled=false;
		frm.delivery_price.setAttribute("validation","true");
		/*frm.free_delivery_yn[1].disabled=false;
		frm.free_delivery_yn[1].checked=true;
		frm.free_delivery_count.disabled=false;*/
		document.getElementById("delivery_package_y").checked=true;
		//document.getElementById("delivery_package_y").style.display="none";
		//document.getElementById("package_input").style.display="none";  2012-07-20 jk 주석 개별 배송 유료배송 선택에따른 디스플레이 효과부분 제거
		//document.getElementById("package_text").style.display="";  2012-07-20 jk 주석 개별 배송 유료배송 선택에따른 디스플레이 효과부분 제거
	}
}

function free_delivery_check(gubun) {//구매수량 컨트롤 kbk 11/11/08
	var cnt=document.getElementById('free_delivery_count');
	if(gubun=='N') {
		document.product_input.free_delivery_count.value='';
		cnt.disabled = true;
		cnt.setAttribute("validation","false");
	} else {
		cnt.disabled = false;
		cnt.setAttribute("validation","true");
	}
}


$(function() {
	$("#start_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		
		if($('#end_datepicker').val() != "" && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+10d');
		}
	}

	});

	//$('#start_timepicker').timepicker();

	
	$("#end_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	$("#make_date_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	$("#expiry_date_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	$("#md_sell_priod_sdate").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		
		if($('#md_sell_priod_edate').val() != "" && $('#md_sell_priod_edate').val() <= dateText){
			$('#md_sell_priod_edate').val(dateText);
		}else{
			$('#md_sell_priod_edate').datepicker('setDate','+10d');
		}
	}

	});

	//$('#start_timepicker').timepicker();

	
	$("#md_sell_priod_edate").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});


	//$('#end_timepicker').timepicker();
});

function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$("#start_datepicker").val(FromDate);
	$("#end_datepicker").val(ToDate);
}

$(function() {
	$("#md_start_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		
		if($('#md_end_datepicker').val() != "" && $('#md_end_datepicker').val() <= dateText){
			$('#md_end_datepicker').val(dateText);
		}else{
			$('#md_end_datepicker').datepicker('setDate','+10d');
		}
	}

	});

	//$('#start_timepicker').timepicker();

	
	$("#md_end_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	$("#md_make_date_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	$("#md_expiry_date_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});

function md_select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$("#md_sell_priod_sdate").val(FromDate);
	$("#md_sell_priod_edate").val(ToDate);
}

function SetOptionTmp(j_select_option){

	$.ajax({ 
		type: 'GET', 
		data: {'act': 'get_options', 'opnt_ix':j_select_option.val()},
		url: './goods_options_input.act.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
				//alert(1);
		},  
		success: function(select_options){ 
			//alert(select_options);
			var select_option_idx = j_select_option.attr('idx');
			var jquery_obj = $('#options_item_input_'+select_option_idx);
			var option_obj = jquery_obj.parent().parent().parent();
			

			option_obj.find("input[id^=option_name]").val(j_select_option.find("option:selected").text());
			option_obj.find("input[id^=options_option_use]").attr("checked","checked");
			//alert(j_select_option.find("option:selected").attr('option_kind'));
			option_obj.find("select[class^=option_kind] > option[value="+j_select_option.find("option:selected").attr('option_kind')+"]").attr("selected","selected");
			
			if(select_options != null){
				//alert(select_options.length);
				var before_option_cnt = 0;
				$.each(select_options, function(i,select_option){ 
					//alert(select_option.option_div);
					//alert($('#options_item_input_'+option_i).parent().find('input[id^=options_item_option_div_'+i+']').parent().html());
					//alert($('#options_item_input_'+option_i).html());
					/*
					if(select_option_idx == 0){
						$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_div_'+i+']').css('border','1px solid red');
					}else if(select_option_idx == 1){
						$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_div_'+i+']').css('border','1px solid blue');
					}else if(select_option_idx == 2){
						$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_div_'+i+']').css('border','1px solid green');
					}
					alert($('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_div_'+i+']').length);
					*/
					//$('#options_item_input_'+select_option_idx).parent().css('border','1px solid red');
					$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_div_'+i+']').val(select_option.option_div);	//select_option.option_div
					$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_price_'+i+']').val(select_option.option_price);	//select_option.option_div
					$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_code_'+i+']').val(select_option.option_code);
					
					if((select_options.length-1) > i){
						//alert($('#options_item_input_'+option_i).parent().find('input[id=options_item_option_div_'+(i+1)+']').val());
						if(!$('#options_item_input_'+select_option_idx).parent().find('input[id=options_item_option_div_'+(i+1)+']').val()){
						copyOptions('options_item_input_'+select_option_idx);				
						}
					}
					before_option_cnt = i;
				}); 
				//alert(before_option_cnt + ":::"+ $('.options_item_input_'+select_option_idx).length);
				var option_detail_obj = $('.options_item_input_'+select_option_idx);
				var j = 0;
				
				$('.options_item_input_'+select_option_idx).each(function(){
					//alert (before_option_cnt +"<"+ j);
					if(before_option_cnt < j){
						$(this).remove();
					}
					j++;
				});
				//for(j = before_option_cnt ; j < option_detail_obj.length; j++){
					//alert(option_detail_obj[j].html());
				//	option_detail_obj[j].remove();
				//}

				//$.each(select_options, function(i,select_option){ 
							
				//}); 
			}

			/*
			$('#work_status_text_'+wl_ix).text(calevents);
			//alert(complete_rate);
			if(complete_rate == 0){				
				complete_rate = 1;
			}
			$('#graph_'+wl_ix).animate({width:complete_rate+"%"},1000);
			$('#charger_'+wl_ix).show();
			
			$('#quick_complate_rate_'+wl_ix).hide();
			$('#quick_complate_rate_'+wl_ix).css('display','none');
			$('#s_loading_'+wl_ix).hide().delay(5000);
			*/
		} 
	}); 

	//parent.opener.copyOptions('options_input');
	

}

function selectTmpOption(opnt_ix){

	var option_type = $('#opnt_ix_'+opnt_ix).attr('option_type');

	if( option_type == 's' || option_type == 'c' ){
		var target_option_type = $('.make_option[option_selected=1][option_type=' + option_type + ']:not("#opnt_ix_'+opnt_ix +'")');
		if( target_option_type.length > 0 ){
			alert('같은 타입의 옵션(`'+ target_option_type.html() +'`)을 먼저 선택하였습니다.');
			return;
		}
	}

	if($('#opnt_ix_'+opnt_ix).attr('option_selected') == '1'){
		$('#opnt_ix_'+opnt_ix).css('border','1px solid silver');
		$('#opnt_ix_'+opnt_ix).css('background-color','#efefef');
		$('#opnt_ix_'+opnt_ix).css('font-weight','normal');
		$('#opnt_ix_'+opnt_ix).attr('option_selected','0');
		$('#opnt_ix_'+opnt_ix+'_box').hide();
	}else{
		$('#opnt_ix_'+opnt_ix).css('border','1px solid #000000');
		$('#opnt_ix_'+opnt_ix).css('background-color','silver');
		$('#opnt_ix_'+opnt_ix).css('font-weight','bold');

		
		$('#opnt_ix_'+opnt_ix).attr('option_selected','1');
		$('#opnt_ix_'+opnt_ix+'_box').show();
	}
}

function selectTmpOptionDetail(opndt_ix){
	
	if($('#opndt_ix_'+opndt_ix).attr('option_detail_selected') == '1'){				
		$('#opndt_ix_'+opndt_ix).css('background-color','#ffffff');
		$('#opndt_ix_'+opndt_ix).css('font-weight','normal');
		$('#opndt_ix_'+opndt_ix).attr('option_detail_selected','0');
	}else{
		$('#opndt_ix_'+opndt_ix).css('background-color','#fff7da');
		$('#opndt_ix_'+opndt_ix).css('font-weight','bold');		
		$('#opndt_ix_'+opndt_ix).attr('option_detail_selected','1');
	}
}

function selectTmpOptionDetailAll(opndt_ix){
	
	var opndt_ix;

	$('.make_option_detail_'+opndt_ix).each(function(){
		opndt_ix = $(this).attr('opndt_ix');
		$('#opndt_ix_'+opndt_ix).css('background-color','#fff7da');
		$('#opndt_ix_'+opndt_ix).css('font-weight','bold');		
		$('#opndt_ix_'+opndt_ix).attr('option_detail_selected','1');
	});
}

function tmpOptionDetailAdd (opnt_ix){

	html =		'<div style="float:left;martin:3px;padding:5px;" >';
	html +=			'<input type="text" option_detail_selected="1" class="textbox addTmpOptionDetail make_option_detail_'+opnt_ix+'" value="" style="float:left;width:90px;margin-right:3px;"/>';
	html +=		'</div>';

	$(html).insertAfter('#opnt_first_area_'+opnt_ix);
}

/*
function MakeStockOption(){
	var make_option = new Array();
	var mk_i = 0;
	$('.make_option').each(function(){
		//alert($(this).attr('option_selected') + "::"+$(this).html());

		if($(this).attr('option_selected') == '1'){
			make_option[mk_i] = new Array();
			$('.make_option_detail_'+$(this).attr('opnt_ix')).each(function(){
				//alert(mk_i+":::"+$(this).html());
				if($(this).attr('option_detail_selected') == '1'){
					make_option[mk_i].push($(this).html());
					//alert($(this).attr('option_detail_selected') + "::"+$(this).html());
				}
			});
			//alert(mk_i+"<==");
			mk_i++;
		}
		
	});
	//alert(make_option);
	var result=allPossibleCases(make_option);
	//alert(r);

	var item_id = 0;
	//alert(result);
	for(j=0; j < result.length ; j++){
		if((result.length-1) > j){
			AddOptionsCopyRow('stock_options_table','stock_options_table');
			//copyOptions('options_basic_item_input_0');				
		}
	}
	
	$("table#stock_options_table").find(".add_option_div").each(function(){
		//alert(result[item_id]);
		$(this).val(result[item_id]);
		item_id++;
	});
}
*/

//2016-09-05 Hong 변경
function MakeStockOption(){
	var make_option = new Array();
	var tmp_option = "";
	var mk_i = 0;
	var option_type_info = new Array();
	var lazadaBool = false;
	$('.make_option').each(function(){
		//alert($(this).attr('option_selected') + "::"+$(this).html());
		if($(this).attr('option_selected') == '1'){
			if($('.make_option_detail_'+$(this).attr('opnt_ix')+'[option_detail_selected=1]').length > 0){
				var option_type = $(this).attr('option_type');
				option_type_info[mk_i] = option_type;
				make_option[mk_i] = new Array();
				$('.make_option_detail_'+$(this).attr('opnt_ix')).each(function(){
					//alert(mk_i+":::"+$(this).html());
					if($(this).attr('option_detail_selected') == '1'){
						if($(this).is('input')){
							tmp_option = $(this).val();
						}else{
							tmp_option = $(this).html();
						}
						
						if(tmp_option.length > 0){
							make_option[mk_i].push(tmp_option);
							
							//라자다관련 추가 작업
							if( $(this).attr('opnt_ix') == 'lazada' ){
								lazadaBool = true;
							}
						}
					}
				});
				mk_i++;
			}
		}
		
	});

	var result=allPossibleCases(make_option);
	
	if($('.options_price_stock_option_div').length < result.length){
		
		var optoin_detail_cnt = $('.options_price_stock_option_div').length;

		for(j=0; j < (result.length - optoin_detail_cnt); j++){
			AddOptionsCopyRow('stock_options_table','stock_options');
		}
	}

	if(result.length){

		//console.log( option_type_info );
		var item_id = 0;
		var options_price_stock_type = $('.options_price_stock_type:checked').val();
		var option_div_tmp = Array();
		$('.options_price_stock_option_div').each(function(){
			
			var tmp = Array();
			var color_index=null, size_index=null;
			if( option_type_info.indexOf('c') >= 0 ){
				color_index = option_type_info.indexOf('c');
			}
			if( option_type_info.indexOf('s') >= 0 ){
				size_index = option_type_info.indexOf('s');
			}

			tmp = result[item_id].split('+');

			/*
			console.log('tmp:'+tmp);
			console.log('options_price_stock_type:'+options_price_stock_type);
			console.log('color_index:'+color_index);
			console.log('size_index:'+size_index);
			*/

			if( options_price_stock_type == 'o' ){
				if( color_index !=null && size_index!=null){
					$(this).closest('tr').find('.options_price_stock_option_size').val(tmp[size_index]);
					$(this).closest('tr').find('.options_price_stock_option_color').val(tmp[color_index]);
				}else if ( color_index !=null ) {
					$(this).closest('tr').find('.options_price_stock_option_color').val(tmp[color_index]);
					for(key=0; key < tmp.length; key++){
						if( key != color_index){
							$(this).closest('tr').find('.options_price_stock_option_size').val(tmp[key]);
							break;
						}
					}
				}else if ( size_index !=null ) {
					$(this).closest('tr').find('.options_price_stock_option_size').val(tmp[size_index]);
					for(key=0; key < tmp.length; key++){
						if( key != size_index){
							$(this).closest('tr').find('.options_price_stock_option_color').val(tmp[key]);
							break;
						}
					}
				}else{
					$(this).closest('tr').find('.options_price_stock_option_color').val(tmp[0]);
					$(this).closest('tr').find('.options_price_stock_option_size').val(tmp[1]);
				}
			} else if ( options_price_stock_type == 's' ) {
				if(size_index!=null){
					$(this).closest('tr').find('.options_price_stock_option_size').val(tmp[size_index]);
				}else{
					$(this).closest('tr').find('.options_price_stock_option_size').val(tmp[0]);
				}
			} else if ( options_price_stock_type == 'c' ) {
				if(color_index!=null){
					$(this).closest('tr').find('.options_price_stock_option_size').val(tmp[color_index]);
				}else{
					$(this).closest('tr').find('.options_price_stock_option_size').val(tmp[0]);
				}
			} else {
				$(this).val(result[item_id]);
			}

			if( lazadaBool ){
				$(this).closest('tr').find('#add_option_etc').val(tmp[size_index]);
			}

			item_id++;
		});
	}
}


function MakeOption(){
	console.log('this');
	var make_option = new Array();
	var mk_i = 0;
	$('.make_option').each(function(){
		//alert($(this).attr('option_selected') + "::"+$(this).html());

		if($(this).attr('option_selected') == '1'){
			make_option[mk_i] = new Array();
			$('.make_option_detail_'+$(this).attr('opnt_ix')).each(function(){
				//alert(mk_i+":::"+$(this).html());
				if($(this).attr('option_detail_selected') == '1'){
					make_option[mk_i].push($(this).html());
					//alert($(this).attr('option_detail_selected') + "::"+$(this).html());
				}
			});
			//alert(mk_i+"<==");
			mk_i++;
		}
		
	});
	//alert(make_option);
	var result=allPossibleCases(make_option);
	//alert(r);

	var item_id = 0;
	//alert(result);
	for(j=0; j < result.length ; j++){
		if((result.length-1) > j){
			copyOptions('options_basic_item_input_0');				
		}
	}
	
	$('.options_price_stock_option_div').each(function(){
		//alert(item_id);
		$(this).val(result[item_id]);
		item_id++;
	});
}


//var allArrays = [['a', 'b'], ['c', 'z'], ['d', 'e', 'f']];

function allPossibleCases(arr) {
	if (arr.length === 0) {
		return [];
	}else if (arr.length ===1){
		return arr[0];
	}else {
		var result = [];
		var allCasesOfRest = allPossibleCases(arr.slice(1));  // recur with the rest of array
		var item_id = 0;
		//alert($('#options_basic_item_input_0').html())
		for (var c in allCasesOfRest) {
		  for (var i = 0; i < arr[0].length; i++) {
			result.push(arr[0][i] + "+"+ allCasesOfRest[c]);
			//alert(arr[0][i] + " + "+allCasesOfRest[c]);
			//$('#options_basic_item_input_0').find('input[id^=options_price_stock_option_div]').val(arr[0][i] + " + "+allCasesOfRest[c]);	//select_option.option_div
			//$('#options_basic_item_input_0').parent().find('input[id^=options_price_stock_option_price_'+item_id+']').val('');	//select_option.option_div
			//$('#options_basic_item_input_0').parent().find('input[id^=options_price_stock_option_code_'+item_id+']').val('');
			//alert(arr[0].length);
			//if((arr[0].length-1) > i){
				//if(!$('#options_basic_item_input_0').find('input[id=options_price_stock_option_div]').val()){
				//	copyOptions('options_basic_item_input_0');				
				//}
			//}
			before_option_cnt = i;
			item_id++;
		  }		 
		}
		
		
		return result;
	}
}
//var r=allPossibleCases(allArrays);

function SetProdcutBasicInfo() {	//상품상세정보 입력???
	
	var id = new Array();
	
	if($("input[name='rpid[2][]']").length < 2){
		alert('세트상품등록을 2개 이상 선택하셔야 합니다.');
		return false;
	}
	
	number = 0;
	$("input[name='rpid[2][]']").each(function(){
		id[number] = $(this).val();
		number ++;
	});

	$.ajax({ 
		type: 'GET', 
		data: {'act': 'get_basicinfo', 'id':id},
		url: './goods_input.act.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
				//alert(1);
		},  
		success: function(select_options){ 
			//alert($("textarea[name=basicinfo]").text());
			$("textarea[name=basicinfo]").text(select_options);
			//Content_Input();
			//Init(document.product_input);
		}
		
	}); 

}

function SetGoodsDisplay(){
	//alert($('#set_goods_area'));

	var _obj = $("#set_goods_area").find("ul[class^=productList] li");
	//alert(_obj.length);
	if(_obj.length > 0){
		
		$('#set_goods_cnt').attr("validation","true");
		$('#set_goods_item_area').html("");
		$('#set_goods_area').find("ul[class^=productList] li").each(function(){
			//alert($(this).html());

			var pid = $(this).find("input[name^=listPid]").val();
			var pname = $(this).find("input[class^=pName]").val();
			//alert(pname);
			
			$("<table cellpadding=0 cellspacing=0 id='select_option_table_"+pid+"'><tr><td><div class='selected_set_goods'><a href='goods_input.php?id="+pid+"' target=_blank>"+pname+"</a></div></td><td class='option_tiem_area' pname='"+pname+"' pid='"+pid+"' id='option_area_"+pid+"'></td></tr></table>").clone(true).appendTo("#set_goods_item_area");  
			
			$.ajax({ 
				type: 'GET', 
				data: {'act': 'get_options', 'pid':pid},
				url: './goods_input.act.php',  
				dataType: 'json', 
				async: false, 
				beforeSend: function(){ 
						//alert(1);
				},  
				success: function(datas){ 
					if(datas){
						if(datas.length){
							$.each(datas, function(i,data){ 
								//alert(data.option_div);
								$("<div class='selected_set_goods_detail' option_detail_selected='0' id='opnd_ix_"+data.id+"' option_div='"+data.option_div+"' option_stock='"+data.option_stock+"' option_code='"+data.option_code+"'  onclick=\"selectOptionDetail($(this),'"+data.id+"');\">"+data.option_div+" - (<b>"+data.option_stock+"</b>)</div>").appendTo("td[id^=option_area_"+pid+"]");  
							});
							$("#set_goods_item_btn").css("display","block");
						}
					}
				} 
			}); 

	/*
			if($(this).attr('option_selected') == '1'){
				make_option[mk_i] = new Array();
				$('.make_option_detail_'+$(this).attr('opnt_ix')).each(function(){
					//alert(mk_i+":::"+$(this).html());
					if($(this).attr('option_detail_selected') == '1'){
						make_option[mk_i].push($(this).html());
						//alert($(this).attr('option_detail_selected') + "::"+$(this).html());
					}
				});
				//alert(mk_i+"<==");
				mk_i++;
			}
	*/		
		});
	}else{
		alert('세트 구성상품이 선택되지 않았습니다. 세트 상품 선택후 세트상품 옵션구성을 해주세요');
	}
}

function SetMakeOption(){
	var select_option_idx = 0;
	var create_option_cnt = 0;

	var option_length = $('.option_tiem_area').length;
	

	$('.option_tiem_area').each(function(){
		var stock_sum = 0;
		$(this).find("div.selected_set_goods_detail").each(function(){
			if($(this).attr('option_detail_selected') == 1){
				stock_sum += parseInt($(this).attr('option_stock'));
			}
		});
		if(stock_sum > 0){
			create_option_cnt++;
		}
	});

	//alert(create_option_cnt+">"+$('.options_input').length);

	$('.option_tiem_area').each(function(){
		//alert((option_length - 2 > select_option_idx)+"="+option_length+" - 2 > "+select_option_idx);
		if(create_option_cnt > $('.options_input').length){
			//if(option_length-1 > $('.options_input').length){
				//alert($('.option_tiem_area').length+"1");
				//alert((option_length-1)+":::"+$('.options_input').length);
				newCopyOptions('options_input');
				select_option_idx++;
			//}
		}
	});
	create_option_cnt = 0;
	var select_option_idx = 0;
	$('.option_tiem_area').each(function(){
		//alert($(this).find("div.selected_set_goods_detail").length);
		if($(this).find("div.selected_set_goods_detail").length > 0){
			var pid = $(this).attr("pid");
			var pname = $(this).attr("pname");
			//alert(pid);
			var selected_set_goods_detail_length = $('#option_area_'+pid).find("div[class^=selected_set_goods_detail]").length;
			//alert($('#option_area_'+pid).find("input[id^=option_name]").length);
			$('#options_item_input_'+select_option_idx).parent().parent().parent().find("input[id^=option_name]").val(pname);
			$('#options_item_input_'+select_option_idx).parent().parent().parent().find("select[class^=option_kind] option:eq(3)").attr("selected","selected")
			var i = 0;		
			$('#option_area_'+pid).find("div[class^=selected_set_goods_detail]").each(function(){
				//alert($(this).attr("option_detail_selected"));
				if($(this).attr("option_detail_selected") == 1){
					if(i != 0 && $(this).attr("option_detail_selected") == 1){
						//alert(1);
						//alert($('#options_item_input_'+option_i).parent().find('input[id=options_item_option_div_'+(i+1)+']').val());
						if(!$('#options_item_input_'+select_option_idx).parent().find('input[id=options_item_option_div_'+(i)+']').val()){
							copyOptions('options_item_input_'+select_option_idx);				
						}
					}

					var option_div = $(this).attr("option_div");
					var option_price = "0";
					var option_code = $(this).attr("option_code");
					//var pid = $(this).attr("pid");
					//alert(select_option_idx+"::"+i+":::"+option_div);
					$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_div_'+i+']').val(option_div);	//select_option.option_div
					$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_price_'+i+']').val(option_price);	//select_option.option_div
					$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_code_'+i+']').val(option_code);
					$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_etc1_'+i+']').val(pid);
					i++;
					
					before_option_cnt = i;
				}
			});
			select_option_idx++;
		}
	//	
	});
}


function selectOptionDetail(jquery_obj, opnd_ix){
	if(jquery_obj.attr('option_stock') > 0){
		if($('#opnd_ix_'+opnd_ix).attr('option_detail_selected') == '1'){				
			$('#opnd_ix_'+opnd_ix).css('background-color','#ffffff');
			$('#opnd_ix_'+opnd_ix).css('background-color','#efefef');
			$('#opnd_ix_'+opnd_ix).css('font-weight','normal');
			$('#opnd_ix_'+opnd_ix).attr('option_detail_selected','0');
		}else{
			$('#opnd_ix_'+opnd_ix).css('background-color','#fff7da');
			$('#opnd_ix_'+opnd_ix).css('font-weight','bold');		
			$('#opnd_ix_'+opnd_ix).attr('option_detail_selected','1');
		}
	}else{
		alert('세트 상품 구성을 위한 옵션 수량이 부족합니다.');
	}
}


function SetGoodsOptionDisplay(j_select_option){

	$.ajax({ 
		type: 'GET', 
		data: {'act': 'get_options', 'opnt_ix':j_select_option.val()},
		url: './goods_options_input.act.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
				//alert(1);
		},  
		success: function(select_options){ 
			//alert(select_options);
			var select_option_idx = j_select_option.attr('idx');
			var jquery_obj = $('#options_item_input_'+select_option_idx);
			var option_obj = jquery_obj.parent().parent().parent();
			

			option_obj.find("input[id^=option_name]").val(j_select_option.find("option:selected").text());
			option_obj.find("input[id^=options_option_use]").attr("checked","checked");
			//alert(j_select_option.find("option:selected").attr('option_kind'));
			option_obj.find("select[class^=option_kind] > option[value="+j_select_option.find("option:selected").attr('option_kind')+"]").attr("selected","selected");
			
			if(select_options != null){
				//alert(select_options.length);
				var before_option_cnt = 0;
				$.each(select_options, function(i,select_option){ 
					
					$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_div_'+i+']').val(select_option.option_div);	//select_option.option_div
					$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_price_'+i+']').val(select_option.option_price);	//select_option.option_div
					$('#options_item_input_'+select_option_idx).parent().find('input[id^=options_item_option_code_'+i+']').val(select_option.option_code);
					
					if((select_options.length-1) > i){
						//alert($('#options_item_input_'+option_i).parent().find('input[id=options_item_option_div_'+(i+1)+']').val());
						if(!$('#options_item_input_'+select_option_idx).parent().find('input[id=options_item_option_div_'+(i+1)+']').val()){
						copyOptions('options_item_input_'+select_option_idx);
						}
					}
					before_option_cnt = i;
				}); 
				//alert(before_option_cnt + ":::"+ $('.options_item_input_'+select_option_idx).length);
				var option_detail_obj = $('.options_item_input_'+select_option_idx);
				var j = 0;
				
				$('.options_item_input_'+select_option_idx).each(function(){
					//alert (before_option_cnt +"<"+ j);
					if(before_option_cnt < j){
						$(this).remove();
					}
					j++;
				});
			
			}

		}
	});

}

function del_coditable(index){
	
	var len = $("table[id^=codi_options_table_]").length;
	if(len > 1){
		$('#codi_options_table_'+index).remove();
	}else{
		alert('더 이상 삭제할수 없습니다.');
	}
	
}


function loadShoppingCenterInfo(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;

	//var depth = sel.getAttribute('depth');
	//document.write('shopping_center_info.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
	window.frames['act'].location.href = '../buyingservice/shopping_center_info.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}


function change_company_sale_rate(type){
	var head_company_sale_rate = $('#'+type+'_head_company_sale_rate').val();
	var seller_company_sale_rate = $('#'+type+'_seller_company_sale_rate').val();
	var whole_total_company_sale_rate = parseInt(head_company_sale_rate) + parseInt(seller_company_sale_rate);

	$('#'+type+'_total_company_sale_rate').val(whole_total_company_sale_rate);
}

function search_multcategory (){
	
	var search_text = $('#search_category_text').val();
//console.log('./goods_input.act.php?search_text='+search_text+'&act=search_multcategory');
	if(search_text){
		$.ajax({
			url : '../product/goods_input.act.php',
			type : 'POST',
			data : {search_text:search_text,
					act:'search_multcategory'
					},
			dataType: 'json',
			error: function(data,error){// 실패시 실행함수 
				alert(error);},
			success: function(args){

				if(args){
					$('select[name=search_category_list]').empty();
					$.each(args, function(index, entry){
						//alert(index);
						$('select[name=search_category_list]').append("<option value="+index+">"+entry+"</option>");
					});
				}else{
					alert('검색한 분류가 없습니다.');
				}
			}
		});
	}else{
		alert('검색어를 입력해 주세요.');
	}

}


function search_standard_category (){
	
	var search_text = $('#search_standard_category_text').val();
//console.log('./goods_input.act.php?search_text='+search_text+'&act=search_standard_category');
	if(search_text){
		$.ajax({
			url : '../product/goods_input.act.php',
			type : 'POST',
			data : {search_text:search_text,
					act:'search_standard_category'
					},
			dataType: 'json',
			error: function(data,error){// 실패시 실행함수 
				alert(error);},
			success: function(args){

				if(args){
					$('select[name=search_standard_category_list]').empty();
					$.each(args, function(index, entry){
						//alert(index);
						$('select[name=search_standard_category_list]').append("<option value="+index+">"+entry+"</option>");
					});
				}else{
					alert('검색한 분류가 없습니다.');
				}
			}
		});
	}else{
		alert('검색어를 입력해 주세요.');
	}

}



function popup_delivery_template(pid,info_type){

	var company_id = $('input[name=company_id]').val();
	PoPWindow3("../product/product_delivery_template.php?mmode=pop&pid="+pid+"&page_type=goods_input&company_id="+company_id+"&info_type="+info_type,"960","900");
}


$(document).ready(function (){
	
	$('input[id^=whole_is_use]').click(function (){
		//alert($(this).attr('checked'));

		if($(this).attr('checked') == 'checked'){
			//$(this).val('1');
		}else{
			//$(this).val('2');
		}
	
	});
	
	if($('input[name=one_commission]:checked').val() == 'N'){
		$('input[id=commission]').attr('readonly',true);
		$('input[id=wholesale_commission]').attr('readonly',true);
	}else{
		$('input[id=commission]').attr('readonly',false);
		$('input[id=wholesale_commission]').attr('readonly',false);
	}

	var value = $('input[name=md_one_commission]:checked').val();
	if(value == 'N'){
		$('#md_discount_name').attr('disabled',true);
		$('input[name=md_sell_date_use]').attr('disabled',true);
		$('input[name=md_sell_priod_sdate]').attr('disabled',true);
		$('input[name=md_sell_priod_edate]').attr('disabled',true);

		$('#whole_head_company_sale_rate').attr('disabled',true);
		$('#whole_seller_company_sale_rate').attr('disabled',true);
		$('#whole_total_company_sale_rate').attr('disabled',true);

		$('#retail_head_company_sale_rate').attr('disabled',true);
		$('#retail_seller_company_sale_rate').attr('disabled',true);
		$('#retail_total_company_sale_rate').attr('disabled',true);

	}else{
		$('#md_discount_name').attr('disabled',false);
		$('input[name=md_sell_date_use]').attr('disabled',false);
		$('input[name=md_sell_priod_sdate]').attr('disabled',false);
		$('input[name=md_sell_priod_edate]').attr('disabled',false);

		$('#whole_head_company_sale_rate').attr('disabled',false);
		$('#whole_seller_company_sale_rate').attr('disabled',false);
		$('#whole_total_company_sale_rate').attr('disabled',false);

		$('#retail_head_company_sale_rate').attr('disabled',false);
		$('#retail_seller_company_sale_rate').attr('disabled',false);
		$('#retail_total_company_sale_rate').attr('disabled',false);
	
	}

	change_company_sale_rate('whole');
	change_company_sale_rate('retail');

	$('input[name=md_one_commission]').click(function (){
		var value = $(this).val();

		if(value == 'N'){
			$('#md_discount_name').attr('disabled',true);
			$('input[name=md_sell_date_use]').attr('disabled',true);
			$('input[name=md_sell_priod_sdate]').attr('disabled',true);
			$('input[name=md_sell_priod_edate]').attr('disabled',true);

			$('#whole_head_company_sale_rate').attr('disabled',true);
			$('#whole_seller_company_sale_rate').attr('disabled',true);
			$('#whole_total_company_sale_rate').attr('disabled',true);

			$('#retail_head_company_sale_rate').attr('disabled',true);
			$('#retail_seller_company_sale_rate').attr('disabled',true);
			$('#retail_total_company_sale_rate').attr('disabled',true);

		}else{
			$('#md_discount_name').attr('disabled',false);
			$('input[name=md_sell_date_use]').attr('disabled',false);
			$('input[name=md_sell_priod_sdate]').attr('disabled',false);
			$('input[name=md_sell_priod_edate]').attr('disabled',false);

			$('#whole_head_company_sale_rate').attr('disabled',false);
			$('#whole_seller_company_sale_rate').attr('disabled',false);
			$('#whole_total_company_sale_rate').attr('disabled',false);

			$('#retail_head_company_sale_rate').attr('disabled',false);
			$('#retail_seller_company_sale_rate').attr('disabled',false);
			$('#retail_total_company_sale_rate').attr('disabled',false);
		
		}
	
	});

	//배송타입 선택시 배송정책값 구분 delivery_type 1:통합배송 2:입점업체별 배송()
	
	$('input[name=delivery_type]').click(function (){
		
		var delivery_type = $(this).val();

		if(delivery_type == '1'){	//통합배송
			var company_id = $('input[name=ori_company_id]').val();
		}else{
			var company_id = $('input[name=company_id]').val();
		}
	//	alert(company_id);
		
		PutDeliveryTemplate(company_id);
	});

});

function getSellerSetup(company_id,one_commission_use,pid){
	
	$.ajax({

		type: 'POST', 
		data:{act: 'get_seller_setup',
				company_id : company_id,
				one_commission_use : one_commission_use,
				pid : pid
		},
		url: './goods_input.act.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
				//alert(1);
		},
		success: function(data_array){ 
			$.each(data_array, function(index, entry){
				$('input[name=commission]').val(entry.commission);
				$('input[name=wholesale_commission]').val(entry.wholesale_commission);
				//alert(index);
			});

			if(one_commission_use == 'N'){
				$('input[id=commission]').attr('readonly',true);
				$('input[id=wholesale_commission]').attr('readonly',true);
			}else{
				$('input[id=commission]').attr('readonly',false);
				$('input[id=wholesale_commission]').attr('readonly',false);
			}
		}
		
	});

}

function displayFreeGift(display_use){
	
	if(display_use == 'Y'){
		$('tr [id^=product_77_]').css('display','none');	//기본정책중 tr 만 
		$('#mandatory_info_zone_table').css('display','none');	//상품고시정보
        $('#mandatory_info_zone_table_global').css('display','none');	//상품고시정보(글로벌)

		$('#delivery_setting_table').css('display','none');	//배송정책

		$('#fee_setting_zone_table').css('display','none');	//수수료설정
		$('#md_rate_zone_table').css('display','none');		//MD할인설정
		//$('#add_option_zone_table').css('display','none');	//
		$('#display_option_zone').css('display','none');
		$('#viral_zone').css('display','none');
		$('#product_relation_zone').css('display','none');
		$('#s_org_tab_table').css('display','none');
		$(".set_options").css('display','none');
		//$("#option_use_setting").css('display','none');	//세트상품일경우 가격재고 옵션 사용설정을 숨김
		$('#category_div').css('display','none');		//분류
		$('#standard_category_div').css('display','none');		//분류
		$('#free_gift_price').css('display','');
		$("#options_div_tab").css("display", "none");
		$('#filter_area').css("display", "none");
		$(".product_detail_zone").css("display", "none");
		$('.pcode_stockinfo_loade').css("display", "");
		$('#product_gift_zone').hide();
        showMandatoryArea('N');
        showMandatoryAreaGlobal('N');
	}else{

		
		$('tr [id^=product_77_]').css('display','');
		$('#mandatory_info_zone_table').css('display','');
        $('#mandatory_info_zone_table_global').css('display','');
		$('#delivery_setting_table').css('display','');

		$('#fee_setting_zone_table').css('display','');
		$('#md_rate_zone_table').css('display','');
		//$('#add_option_zone_table').css('display','');
		$('#display_option_zone').css('display','');
		$('#viral_zone').css('display','');
		$('#product_relation_zone').css('display','');
		$('#s_org_tab_table').css('display','');
		//$("#option_use_setting").css('display','');	//세트상품일경우 가격재고 옵션 사용설정을 숨김
		$('#category_div').css('display','');
		$('#standard_category_div').css('display','');		//분류
		$('#free_gift_price').css('display','none');
        $('#filter_area').css("display", "");
        $('#product_gift_zone').show();
		if($("input[name=stock_use_yn]:checked").val() != 'N'){
			$("#options_div_tab").css("display", "");
		}
		$(".product_detail_zone").css("display", "");
        $('.pcode_stockinfo_loade').css("display", "none");
	}

}



function sell_priod_datepicker_check (obj){
	if($(obj).prop('checked')){
		$('#start_datepicker').css('background-color','#fff');
		$('#end_datepicker').css('background-color','#fff');
		$('#start_datepicker').attr('disabled',false);
		$('#end_datepicker').attr('disabled',false);
	}else{
		$('#start_datepicker').css('background-color','#efefef');
		$('#end_datepicker').css('background-color','#efefef');
		$('#start_datepicker').attr('disabled',true);
		$('#end_datepicker').attr('disabled',true);
		$('#start_datepicker').val('');
		$('#end_datepicker').val('');
	}
}


function input_stock(tbName){	//가격재고옵션 금액 동일적용함수 2014-06-14 이학봉
	
	var tmp_stock = $('#input_stock').val();

	$('#' + tbName + ' tbody').find('tr[depth^=1]').each(function (){
		$(this).find('#add_option_stock').val(tmp_stock);
	});

}

function price_check(target,tbName){	//가격재고옵션 금액 동일적용함수 2014-06-14 이학봉
	
	if(!target){
		return false;
	}
	
	var priceList = Array();
	
	if(target == 'wholesale_listprice'){
		priceList.push({"price":"wholesale_price","target":"add_option_wholesale_listprice"});
		priceList.push({"price":"wholesale_sellprice","target":"add_option_wholesale_price"});
	}else if(target == 'retail_listprice'){
		priceList.push({"price":"listprice","target":"add_option_listprice"});
		priceList.push({"price":"sellprice","target":"add_option_sellprice"});
		priceList.push({"price":"premiumprice","target":"add_option_premiumprice"});
	}else if(target == 'coprice'){
		priceList.push({"price":"coprice","target":"add_option_coprice"});
	}

	$.each(priceList,function (index,item){

		var tmp_price = $('input[name='+ item.price +']').val();

		$('#' + tbName + ' tbody').find('tr[depth^=1]').each(function (){
			$(this).find('#'+item.target).val(tmp_price);
		});
	});

	/*
	if(!target){
		return false;
	}
	//var tbName = 'stock_options_table';

	if(target == 'wholesale_listprice'){
		listprice_id = 'add_option_wholesale_listprice';
		sellprice_id = 'add_option_wholesale_price';
	}else if(target == 'retail_listprice'){
		listprice_id = 'add_option_listprice';
		sellprice_id = 'add_option_sellprice';
	}else if(target == 'coprice'){
		listprice_id = 'add_option_coprice';
	}

	var tbody = $('#' + tbName + ' tbody').find('tr[depth^=1]:first');  
	var total_rows = tbody.find('tr[depth^=1]').length;  
	
	var listprice = tbody.find('#'+listprice_id).val();
	var sellprice = tbody.find('#'+sellprice_id).val();

	//alert(listprice+'__'+sellprice);

	$('#' + tbName + ' tbody').find('tr[depth^=1]').each(function (){

		$(this).find('#'+listprice_id).val(listprice);
		if(target != 'coprice'){
			$(this).find('#'+sellprice_id).val(sellprice);
		}

	});
	*/
}


function PutDeliveryTemplate(company_id){
	
	$.ajax({
	url : '../seller_search.act.php',
	type : 'POST',
	data : {company_id:company_id,
			act:'select_delivery_template',
			mode:'select_company'
			},
	dataType: 'json',
	error: function(data,error){// 실패시 실행함수 
		alert(error);},
	success: function(args){

			if(args != null){
				
				//기본배송정책 뿌려주는 부분 시작 
				$.each(args.input, function(is_wholesale, entry){
					$.each(entry,function (delivery_div,detail){
						$.each(detail,function (dt_ix,delivery_name){
							$('#basic_template_delivery').html(delivery_name);
							$('#template_basic_dt_check').attr('checked',true);
							$('#template_basic_dt_r').val(dt_ix);
						});
					});
				}); 

				//개별배송정책 뿌려주는 부분 시작 
				$('select[id^=dt_ix]').empty();
				$.each(args.select, function(is_wholesale, entry){
					$.each(entry,function (delivery_div,detail){
						$('#dt_ix_'+is_wholesale+'_'+delivery_div).append('<option value=>배송정책 선택</option>');
						$.each(detail,function (dt_ix,delivery_name){
							$('#dt_ix_'+is_wholesale+'_'+delivery_div).append('<option value='+dt_ix+' selected>'+delivery_name+'</option>');
						});
					});
				}); 

			}else{
				alert('해당 셀러업체 배송정책이 존재하지 않습니다. 상품관리 > 셀러관리 > 배송정책에서 기본 배송정책을 설정해주세요.');
				$('select[id^=dt_ix]').empty();
				$('select[name^=dt_ix]').each(function (){
					$(this).append('<option value=>해당 배송정책이 없습니다.</option>');
				});
				$('#basic_template_delivery').html('해당 배송정책이 없습니다.');

			}
		}
	});

}

function sumit_product_option(){	//옵션미리보기 스크립트 2014-09-11 이학봉

	var queryString = $('#product_input').formSerialize(); 
	$.post(
		'./goods_option_select.act.php?act=insert',
		queryString,
		function (data){

			PoPWindow('/admin/product/goods_option_select.php?pid='+data+'&delivery_package=N',450,700,'goods_option_temp');

		}, 'html'); 
	
	//return false;
	
}

function showMandatoryArea(type){
	if(type == 'Y'){
		$('.MandatoryArea').show();
		$('#mandatory_info_zone').show();
		$('#mandatory_info_zone').find('input[type=text][name^=mandatory_info]').attr("validation", "true");
		$('select[name=mandatory_type_1]').attr("validation", "true");
	}else{
		$('.MandatoryArea').hide();
		$('#mandatory_info_zone').hide();
		$('#mandatory_info_zone').find('input[type=text][name^=mandatory_info]').attr("validation", "false");
		$('select[name=mandatory_type_1]').attr("validation", "false");
	}
}

function showMandatoryAreaGlobal(type){
    if(type == 'Y'){
        $('.MandatoryAreaGlobal').show();
        $('#mandatory_info_zone_global').show();
        $('#mandatory_info_zone_global').find('input[type=text][name^=mandatory_info_global]').attr("validation", "true");
        $('select[name=mandatory_type_1_global]').attr("validation", "true");
    }else{
        $('.MandatoryAreaGlobal').hide();
        $('#mandatory_info_zone_global').hide();
        $('#mandatory_info_zone_global').find('input[type=text][name^=mandatory_info_global]').attr("validation", "false");
        $('select[name=mandatory_type_1_global]').attr("validation", "false");
    }
}

function showDeliveryFromAjax_gi(type, id){
	if(type == ''){
		var type = $('input[name=delivery_div_show]:checked').val();
	}

	if($('input[name=delivery_type]:checked').val() == 1){
		var company_id = $('#ori_company_id').val();
	}else{
		var company_id = $('#company_id').val();
	}

	$.ajax({ 
		type: 'GET', 
		data: {'act' : 'showDeliveryFromAjax','company_id': company_id,'type': type,'id': id},
		url: './update_delivery_policy.php',  
		dataType: 'html', 
		success: function(result){ 
			$('#delivery_template_area').html(result);
		} 
	}); 
}

function changeDeliveryArea_gi(type, id){
	if(type == 1){
		var company_id = $('#ori_company_id').val();
	}else{
		var company_id = $('#company_id').val();

		if(company_id == ''){
			alert('셀러업체를 선택해주시기 바랍니다');
			$('input[name=delivery_type][value=1]').prop('checked', true);
			$('input[name=delivery_type][value='+type+']').prop('checked', false);
			return false;
		}
	}

	$.ajax({ 
		type: 'GET', 
		data: {'act' : 'showDeliveryText','company_id': company_id},
		url: './update_delivery_policy.php',  
		dataType: 'html', 
		success: function(result){ 
			$('#policy_text_retail').html(result);
		} 
	}); 

	showDeliveryFromAjax('', id);

	return;
}

function change_price(obj,mode){

	var $this = $(obj);
	var type = $('.product_type:checked').val();

	var target = $this.closest('.codi_options_table'); //기본은 세트 타겟

	if(type == 0) {
		//일반일 때 타겟변경
		target = $this.closest('.stock_options_table');
	}

	if(mode == 'option') {
		var coprice = $('input[name=coprice]').val();
		var listprice = $('input[name=listprice]').val();
		var sellprice = $('input[name=sellprice]').val();
		var english_coprice = $('input[name=english_coprice]').val();
		var english_listprice = $('input[name=english_listprice]').val();
		var english_sellprice = $('input[name=english_sellprice]').val();

        target.find('input[id=add_option_coprice]').val(coprice);
        target.find('input[id=add_option_listprice]').val(listprice);
        target.find('input[id=add_option_sellprice]').val(sellprice);
		target.find('input[id=english_add_option_coprice]').val(english_coprice);
		target.find('input[id=english_add_option_listprice]').val(english_listprice);
		target.find('input[id=english_add_option_sellprice]').val(english_sellprice);
	}else if(mode == 'zero') {
		target.find('input[id=add_option_coprice]').val(0);
		target.find('input[id=add_option_listprice]').val(0);
		target.find('input[id=add_option_sellprice]').val(0);
		target.find('input[id=english_add_option_coprice]').val(0);
		target.find('input[id=english_add_option_listprice]').val(0);
		target.find('input[id=english_add_option_sellprice]').val(0);
	}else if(mode == 'currency'){
		var exchange = $this.attr('exchange');

        target.find('input[id=add_option_coprice]').each(function(){
        	var coprice = $(this).val();
        	var english_coprice = 0.00;
        	if(coprice){
                english_coprice = coprice/exchange;
			}

            $(this).siblings('input[id=english_add_option_coprice]').val(english_coprice.toFixed(2));

		});

        target.find('input[id=add_option_listprice]').each(function(){
            var coprice = $(this).val();
            var english_coprice = 0.00;
            if(coprice){
                english_coprice = coprice/exchange;
            }

            $(this).siblings('input[id=english_add_option_listprice]').val(english_coprice.toFixed(2));

        });

        target.find('input[id=add_option_sellprice]').each(function(){
            var coprice = $(this).val();
            var english_coprice = 0.00;
            if(coprice){
                english_coprice = coprice/exchange;
            }

            $(this).siblings('input[id=english_add_option_sellprice]').val(english_coprice.toFixed(2));

        });
	}
}

/**옵션별 일괄 품절 체크 기능**/
function allSoldOut(frm){

    if($(frm).is(':checked') == true){
        $('.option_soldout').attr('checked',true);
    }else{
        $('.option_soldout').attr('checked',false);
    }

}
