
function copyOptions(id_name){
	
	obj_tr= $('#'+id_name).find('tr.option_details_tr:last');
	var newRow = obj_tr.clone(true).appendTo('#'+id_name);

	var o_num=obj_tr.attr('o_num');
	var od_num=parseInt(obj_tr.attr('od_num'))+1;
	newRow.attr('od_num',od_num);

	newRow.find(".textbox").val("");
	newRow.find("#options_item_option_img").hide();
	newRow.find("#delete_option_imgfile").hide();
	
	newRow.find("#options_item_option_id").attr("name","options["+(o_num)+"][details]["+(od_num)+"][opd_ix]");
	newRow.find("#options_item_option_use").attr("name","options["+(o_num)+"][details]["+(od_num)+"][option_use]").attr('checked',true);
	newRow.find("#options_item_opt_dt_code").attr("name","options["+(o_num)+"][details]["+(od_num)+"][opt_dt_code]");
	newRow.find("#options_item_option_div").attr("name","options["+(o_num)+"][details]["+(od_num)+"][option_div]");
	newRow.find("#options_item_option_div_engish").attr("name","options["+(o_num)+"][details]["+(od_num)+"][option_div_engish]");
	newRow.find("#options_item_option_div_china").attr("name","options["+(o_num)+"][details]["+(od_num)+"][option_div_china]");
	newRow.find("options_item_option_imgfile").attr("name","options["+(o_num)+"][details]["+(od_num)+"][option_imgfile]");

}

function deleteImg(obj,opnt_ix,opndt_ix){
	if(confirm('이미지를 삭제하시겠습니까?')){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'img_delete', 'opnt_ix':opnt_ix, 'opndt_ix':opndt_ix},
			url: './goods_options_input.act.php',  
			dataType: 'html', 
			async: false, 
			beforeSend: function(){ 

			},  
			success: function(resulte){
				if(resulte=='Y'){
					obj.parent().find('img').hide();
				}else{
					alert('이미지가 삭제되지 않았습니다.');
				}
			}
		});
	}
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


	var option_div_check = true;
	$("table[id^=options_input_]").each(function(){
		var table_obj = $(this);
		table_obj.find('[name*=option_div]').each(function(){
			if(table_obj.find('[name*=option_div][value='+ $(this).val()+']').length > 1){
				alert(language_data['goods_input.js']['I'][language]);
				$(this).focus();
                option_div_check = false;
				return false;
			}
		})
	});

	if(option_div_check){
        frm.submit();
	}

	/*
	//var option_names = document.all.option_name;
	var option_names = $("input[inputid=option_name]");
	var option_names_str = "|";
	//var _option_names = new Array();
	
	var option_details_divs = "";
	var option_details_div_str = "";
	//alert(option_names.length);
	for(i=0;i < option_names.length;i++){		
			option_names_str += option_names[i].value+"|";		
			//_option_names[i] = option_names[i].value
	}
	//alert(option_names_str);
	
	
	for(i=0;i < option_names.length;i++){
		
		//alert(in_array(option_names[i].value,_option_names));
		//alert(substr_count(option_names_str, "|"+option_names[option_names.length-i-1].value+"|"));
		
		
		if(option_names[option_names.length-i-1].value){
			//alert(option_names[option_names.length-i-1]);
			if(option_names[option_names.length-i-1].value && substr_count(option_names_str, "|"+option_names[option_names.length-i-1].value+"|") > 1){
				alert(language_data['goods_input.js']['I'][language]);
				//'중복된 옵션명이 있습니다. 수정후 다시 시도해주세요'
				option_names[option_names.length-i-1].focus();
				return false;
			}else{
		
				//option_details_divs = eval("document.all.options_item_option_div_"+i);
				option_details_divs = $("input[inputid=options_item_option_div_"+i+"]");
				option_details_div_str = "|";
				
				for(j=0;j < option_details_divs.length;j++){		
					//alert(option_details_divs[j].value);
					if(option_details_divs[j].value){
						option_details_div_str += option_details_divs[j].value+"|";	
					}	
						//_option_names[i] = option_names[i].value
				}
				
				for(j=0;j < option_details_divs.length;j++){		
					//alert(option_details_divs[option_details_divs.length-j-1].value);
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
	
	//var display_option_titles = document.all.display_option_title;
	var display_option_titles = $("input[inputid=display_option_title]");
	var display_option_titles_str = "|";
	//var _option_names = new Array();
	
	var option_details_divs = "";
	var option_details_div_str = "";
	
	for(i=0;i < display_option_titles.length;i++){		
			display_option_titles_str += display_option_titles[i].value+"|";		
			//_option_names[i] = option_names[i].value
	}
	//alert(display_option_titles_str);
	for(i=0;i < display_option_titles.length;i++){		
		//alert(in_array(display_option_titles[i].value,_display_option_titles));
		//alert(substr_count(display_option_titles_str, "|"+display_option_titles[display_option_titles.length-i-1].value+"|"));
		if(display_option_titles[display_option_titles.length-i-1].value){
			if(display_option_titles[display_option_titles.length-i-1].value && substr_count(display_option_titles_str, "|"+display_option_titles[display_option_titles.length-i-1].value+"|") > 1){
				alert(language_data['goods_input.js']['J'][language]);
				//'중복된 디스플레이 옵션명이 있습니다. 수정후 다시 시도해주세요'
				display_option_titles[display_option_titles.length-i-1].focus();
				return false;			
			}
		}
	}
	frm.submit();
	*/
}



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
