/*
function copyPollItem(obj){

var objs = eval("document.all."+obj);
//var objs = document.getElmentById(obj);
//alert(obj);
if(objs.length > 0 ){
	//alert(objs[0]);
	var obj_table = objs[0].cloneNode(true);
	var target_obj = objs[objs.length-1];	
}else{
	
	var obj_table = objs.cloneNode(true);
	var target_obj = objs;
}

//alert(objs.length+":::"+target_obj.outerHTML);
//alert(obj_table.parentElement);
obj_table_text = obj_table.outerHTML;

	
obj_table_text = obj_table_text.replace("DISPLAY: none","DISPLAY: block");
	

//alert(obj_table_text);
target_obj.insertAdjacentHTML("afterEnd",obj_table_text);

}
*/


function CopyPollItemRow(target){

	var option_target_obj = $('#zone_').find('table[id^='+target+']');
	var option_obj = $('#zone_')
    var total_rows = option_target_obj.length;
	var newRow = option_obj.find('table[id^='+target+']:last').clone(true).appendTo(option_target_obj.parent());

	newRow.find("input[id^=pf_ix]").attr("name","pf_ix[]").val("");
	newRow.find("input[id^=fielddesc]").attr("name","fielddesc[]").val("");
	newRow.find("input[id^=result]").attr("name","result[]").val("");
	newRow.find("span[id^=number]").text((total_rows+1)+'.');

}


function DeleteRow(jquery_obj){
	jquery_obj.parent().parent().remove();
}

