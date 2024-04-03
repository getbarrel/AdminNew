
function loadUser(sel,target,wl_ix) {
	//alert(sel);
	var trigger = "";
	//alert(sel.length);
	for(i=0;i < sel.length;i++){
		//alert(sel[i].selected);
		if(sel[i].selected){
			if(trigger == ""){
				trigger = sel[i].value;
			}else{
				trigger += ","+sel[i].value;
			}
		}
	}
	//var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	var depth = sel.depth;
	//alert(trigger);
	//document.write('workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	
	//document.write ('<script language=\"javascript\" src=\"workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2\"></script>');
	//dynamic.src = 'workuser.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	//document.write('user.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2&wl_ix='+wl_ix);
	if(wl_ix){
		window.frames["act"].location.href = 'user.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2&wl_ix='+wl_ix;
	}else{
		window.frames["act"].location.href = 'user.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2&wl_ix=';
	}
	//alert(dynamic.src);
}


function copyOptions(obj){
//var objs = eval("document.all."+obj);
//var objs=$("."+obj);
//alert(objs.length);
//var objs = document.getElmentById(obj);
//alert(obj);
/*
if(objs.length > 0 ){
	//alert(objs[0]);
	var obj_table = objs[0].cloneNode(true);
	var target_obj = objs[objs.length-1];	
	var select_idx = objs.length-1; //objs.length;
}else{
	
	var obj_table = objs[0].cloneNode(true);
	var target_obj = objs;
	var select_idx = 0;//1;
}
if(obj_table.id=="display_authorization_line_input_item") {//디스플레이 옵션 수정 kbk 12/06/19
	var dis_first_ix=$(".display_authorization_line_dp_ix:eq(0)").val();
}
*/
//alert(objs.length+":::"+target_obj.outerHTML);
//alert(obj_table.parentElement);
//alert(typeof(objs[0].outerHTML));
//obj_table_text = obj_table.outerHTML;
var newRow = $("#"+obj+":last").clone(true).appendTo("#authorization_line_input_table");  

var aldt_ix = $(".authorization_line_item_input").length-1;
//alert(aldt_ix);
	//alert($(obj_table).find('input[id^=authorization_line_aldt_ix]').val());
	newRow.find('input[id^=authorization_line_aldt_ix]').val("");
	newRow.find('input[id^=authorization_line_aldt_ix]').attr("name","authorization_line[0][details]["+aldt_ix+"][aldt_ix]");
	newRow.find('select[id^=authorization_line_department]').attr("name","authorization_line[0][details]["+aldt_ix+"][department]");
	newRow.find('select[id^=authorization_line_department]').unbind("change");
	newRow.find('select[id^=authorization_line_department]').change(function(){
		//alert(1);
		loadUser(this,"authorization_line[0][details]["+aldt_ix+"][charger_ix]");
	});
	newRow.find('select[id^=authorization_line_user]').attr("name","authorization_line[0][details]["+aldt_ix+"][charger_ix]");
	newRow.find('input[id^=authorization_line_position]').attr("name","authorization_line[0][details]["+aldt_ix+"][position]");
	newRow.find('input[id^=authorization_line_charger_name]').attr("name","authorization_line[0][details]["+aldt_ix+"][charger_name]");
	newRow.find('input[id^=authorization_line_disp_name]').attr("name","authorization_line[0][details]["+aldt_ix+"][disp_name]");
	newRow.find('input[id^=authorization_line_order_approve]').attr("name","authorization_line[0][details]["+aldt_ix+"][order_approve]");

	//name= id='authorization_line_aldt_ix' 
	//newRow.replace(/\[details\]\[0\]/g,"[details]["+(parseInt(target_obj.getAttribute('aldt_ix'))+1)+"]");
	//alert($(obj_table).find('input[id^=authorization_line_aldt_ix]').val());
	
if(false){	
	obj_table_text = obj_table_text.replace(/authorization_line_item_option_details_ix_0/g,"authorization_line_item_option_details_ix_"+(parseInt(target_obj.getAttribute('aldt_ix'))+1));	
	obj_table_text = obj_table_text.replace(/aldt_ix=\"0\"/g,"aldt_ix="+(parseInt(target_obj.getAttribute('aldt_ix'))+1));
	obj_table_text = obj_table_text.replace(/ idx=\"0\"/g," idx="+(parseInt(select_idx)));
	obj_table_text = obj_table_text.replace(/\[details\]\[0\]/g,"[details]["+(parseInt(target_obj.getAttribute('aldt_ix'))+1)+"]");
	obj_table_text = obj_table_text.replace(/display_authorization_line\[0\]/g,"display_authorization_line["+(parseInt(select_idx)+1)+"]");

	//obj_table_text = obj_table_text.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all."+obj+".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert('마지막 한개는 삭제 하실 수 없습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");

	obj_table_text = obj_table_text.replace(/opt_idx=\"0\"/g,"opt_idx="+(parseInt(select_idx)+1));
	var child_txt="$('."+obj+"[idx="+(parseInt(select_idx)+1)+"]')[0]";
	var parent_txt=child_txt+'.parentNode';
	obj_table_text = obj_table_text.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('."+obj+"').length > 1){"+parent_txt+".removeChild("+child_txt+");/*this.parentNode.parentNode.parentNode.removeNode(true);*/}else{alert(language_data['goods_input.js']['W'][language]);}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>");
	//'마지막 한개는 삭제 하실 수 없습니다.'
	
	obj_table_text = obj_table_text.replace(/add_copy_img_0/g,"add_copy_img_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/addimages\[0\]/g,"addimages["+(parseInt(select_idx)+1)+"]");
	//alert($(obj_table).find('input[id^=authorization_line_aldt_ix]').val());
	$('.authorization_line_user').change(function(){
	//	alert($(this).find('option:selected').attr('ps_name'));
	//	$(this).parent().parent().parent().find('input[id^=authorization_line_position]').val($(this).find('option:selected').attr('ps_name'));
	});
}	
	//alert(select_idx+"::"+obj_table_text);

//alert(obj_table_text);
/*
if(obj_table.childNodes[2].childNodes[0].childNodes[0].childNodes[0].id = 'option_opn_ix'){
	obj_table.childNodes[2].childNodes[0].childNodes[0].childNodes[0].value = '';
}
*/
//alert(obj_table.childNodes[2].childNodes[0].childNodes[0].childNodes[0].value);
//target_obj.insertAdjacentHTML("afterEnd",obj_table_text);

}

function showMessage(_id, message){
	try{
		document.getElementById(_id).innerHTML = message;	
	}catch(e){}
	//document.getElementById(_id).innerHTML = "";
}

function OptionInput(frm,act)
{
	frm.act.value = act;


	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	
	
	/*
	//var authline_names = document.all.authline_name;
	var authline_names = $("input[inputid=authline_name]");
	var authline_names_str = "|";
	//var _authline_names = new Array();
	
	var option_details_divs = "";
	var option_details_div_str = "";
	
	for(i=0;i < authline_names.length;i++){		
			authline_names_str += authline_names[i].value+"|";		
			//_authline_names[i] = authline_names[i].value
	}
	//alert(authline_names_str);
	
	
	for(i=0;i < authline_names.length;i++){
		
		//alert(in_array(authline_names[i].value,_authline_names));
		//alert(substr_count(authline_names_str, "|"+authline_names[authline_names.length-i-1].value+"|"));
		if(authline_names[authline_names.length-i-1].value){
			
			if(authline_names[authline_names.length-i-1].value && substr_count(authline_names_str, "|"+authline_names[authline_names.length-i-1].value+"|") > 1){
				alert(language_data['goods_input.js']['I'][language]);
				//'중복된 옵션명이 있습니다. 수정후 다시 시도해주세요'
				authline_names[authline_names.length-i-1].focus();
				return false;
			}else{
				
				//option_details_divs = eval("document.all.authorization_line_item_department_"+i);
				option_details_divs = $("input[inputid=authorization_line_item_department_"+i+"]");
				option_details_div_str = "|";
				
				for(j=0;j < option_details_divs.length;j++){		
					//alert(option_details_divs[j].value);
					if(option_details_divs[j].value){
						option_details_div_str += option_details_divs[j].value+"|";	
					}	
						//_authline_names[i] = authline_names[i].value
				}
				
				for(j=0;j < option_details_divs.length;j++){		
					//alert(option_details_div_str+":::"+substr_count(option_details_div_str, "|"+option_details_divs[option_details_divs.length-j-1].value+"|"));
						if(option_details_divs[option_details_divs.length-j-1].value && substr_count(option_details_div_str, "|"+option_details_divs[option_details_divs.length-j-1].value+"|") > 1){
							//alert(1);
							alert(language_data['goods_input.js']['I'][language]);
							//'중복된 옵션구분명이 있습니다. 수정후 다시 시도해주세요'
							//alert(option_details_divs[option_details_divs.length-j-1].outerHTML+"<--");
							option_details_divs[option_details_divs.length-j-1].focus();
							return false;
						}
				}
			}
		}
	}
	*/
	
	frm.submit();	
}

