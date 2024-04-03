
function CopyBuyingServiceInfoRow(target){
	
	var option_target_obj = $('#buying_service_zone').find('tr[id^='+target+']');
	var option_obj = $('#buying_service_zone')
    var total_rows = option_target_obj.length;
	//var newRow = option_obj.find('table[id^='+target+']:first').clone(true).appendTo(option_obj);
	var newRow = option_obj.find('tr[id^='+target+']:last').clone(true).appendTo(option_target_obj.parent());
	//alert(option_target_obj);
	newRow.find("input[id^=baid_ix]").val("");
	newRow.find("select[id^=ws_ix]").attr("name","buying_infos["+(total_rows)+"][ws_ix]");
	newRow.find("select[id^=division]").attr("name","buying_infos["+(total_rows)+"][division]");
	newRow.find("input[id^=paper_name]").attr("name","buying_infos["+(total_rows)+"][paper_name]");
	newRow.find("input[id^=goodss_name]").attr("name","buying_infos["+(total_rows)+"][goodss_name]");
	newRow.find("input[id^=color]").attr("name","buying_infos["+(total_rows)+"][color]");
	newRow.find("input[id^=size]").attr("name","buying_infos["+(total_rows)+"][size]");
	newRow.find("input[id^=amount]").attr("name","buying_infos["+(total_rows)+"][amount]");
	newRow.find("input[id^=buying_complete_cnt]").attr("name","buying_infos["+(total_rows)+"][buying_complete_cnt]");
	newRow.find("input[id^=soldout_cancel_cnt]").attr("name","buying_infos["+(total_rows)+"][soldout_cancel_cnt]");
	newRow.find("input[id^=incom_ready_cnt]").attr("name","buying_infos["+(total_rows)+"][incom_ready_cnt]");
	newRow.find("input[id^=buying_price]").attr("name","buying_infos["+(total_rows)+"][buying_price]");
	newRow.find("input[id^=total_price]").attr("name","buying_infos["+(total_rows)+"][total_price]");

	newRow.find("input[id^=pre_payment_price]").attr("name","buying_infos["+(total_rows)+"][pre_payment_price]");	
	newRow.find("img[class^=btn_deletes]").css("display","inline");
	
	var option_target_obj2 = $('#buying_service_zone').find('tr[class^='+target+'2]');
	var option_obj2 = $('#buying_service_zone')
    var total_rows2 = option_target_obj2.length;

	var newRow2 = option_obj2.find('tr[class^='+target+'2]:last').clone(true).appendTo(option_target_obj.parent());
	newRow2.find("input[id^=comment]").attr("name","buying_infos["+(total_rows2+1)+"][comment]");
	/*
	if($.browser.msie){
      var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
   }else{
	 // var newRow = tbody.find('tr[depth^=1]:last').clone(true).insertAfter(tbody);  
	  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
   }
   */
	//alert(option_target_obj.html());
if(false){
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
}	
	 
	//option_obj.find('table[id^='+target+']:first').appendTo(newRow.clone().wrapAll("<table/>").parent().html())
//	copyObjectText = newRow.clone().wrapAll("<tr/>").parent().html();
	//alert(copyObjectText);
	/*
	copyObjectText = copyObjectText.replace(/options\[0\]/g,"options["+(total_rows)+"]");
	copyObjectText = copyObjectText.replace(/favorite_option_0/g,"favorite_option_"+(total_rows)+"");
	copyObjectText = copyObjectText.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;margin:3px;' ondblclick=\"if(document.all."+target+".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert('마지막 한개는 삭제 하실 수 없습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");
	*/
	//$(copyObjectText).appendTo(option_obj);
	//$('#option_info_text').val(copyObjectText);//.replace(/options\[0\]/g,"options["+(total_rows)+"]")
	//$('#option_info_text').val(newRow.clone().wrapAll("<table/>").parent().html());//.replace(/options\[0\]/g,"options["+(total_rows)+"]")

}


function DeleteRow(jquery_obj){
	jquery_obj.parent().parent().remove();
}