

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


function InventoryGoodsInput(frm,act)
{
	frm.act.value = act;

	if(!CheckFormValue(frm)){
		return false;	
	}
	//alert(frm.inventory_goods_input);
/*
	var categorys=document.getElementsByName('category[]');

	if(categorys.length < 1){
		alert(language_data['goods_input.js']['E'][language]);//'카테고리를 선택해주세요'
		return false;
	}
*/

	var inventory_item_names = $("input[inputid=inventory_item_name]");
	var inventory_item_names_str = "|";
	

	for(j=0;j < inventory_item_names.length;j++){
			if(inventory_item_names[j].value){
				inventory_item_names_str += inventory_item_names[j].value+"|";				
			}
	}
	//alert(inventory_item_names_str);
	for(j=0;j < inventory_item_names.length;j++){
			if(inventory_item_names[inventory_item_names.length-j-1].value && substr_count(inventory_item_names_str, "|"+inventory_item_names[inventory_item_names.length-j-1].value+"|") > 1){
				alert('중복되는 단품규격이 있습니다. 수정후 다시 시도해주세요');
				inventory_item_names[inventory_item_names.length-j-1].focus();
				return false;
			}
	}

	if(act=='delete'){
		if(confirm('정말로 삭제 하시겠습니까?')){
			frm.submit();
		}
	}else{
		frm.submit();
	}
}

function filterNum(str) {
re = /^$|,/g;

// "$" and "," 입력 제거

return str.replace(re, "");
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
}


function getBarcode(code_div, this_obj){
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'get_barcode','code_div':code_div},
		url: './inventory_goods_input.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
			
		},  
		success: function(data){ 
			//alert(data);
			if(code_div == "barcode"){
				$(this_obj).parent().find("#barcode").val(data);
			}else{
				$(this_obj).parent().find("#item_barcode").val(data);
			}
		} 
	});
}

function deleteStandardRow(this_obj){

	gid_obj = this_obj.parent().parent().find('#gid');
	if(gid_obj.attr('readonly')=="readonly"||gid_obj.attr('readonly')==true){
		if(confirm('( ※ 실제 데이터가 삭제됩니다. ) 품목번호 ['+ gid_obj.val() +'] 을 정말로 삭제 하시겠습니까 ?')){
			$.ajax({ 
				type: 'POST', 
				data: {'act': 'ajax_delete','gid':gid_obj.val()},
				url: './inventory_goods_input.act.php',  
				dataType: 'html', 
				async: true, 
				beforeSend: function(){ 

				},  
				success: function(resulte){ 
					if(resulte=="Y"){
						if($('#standard_table').find('tr.standard_tr').length > 1){
							this_obj.parent().parent().remove();
						}else{ 
							$('#standard_table').find('tr.standard_tr input').val('');
						}
					}else{
						alert('삭제시 오류가 발생했습니다.');
					}
				} 
			});
		}
	}else{
		if($('#standard_table').find('tr.standard_tr').length > 1){
			this_obj.parent().parent().remove();
		}else{ 
			$('#standard_table').find('tr.standard_tr input').val('');
		}
	}
}

function InventoryDelete(gid){

	if(confirm('( ※ 실제 데이터가 삭제됩니다. ) 품목번호 ['+ gid +'] 을 정말로 삭제 하시겠습니까 ?')){
		$.ajax({ 
			type: 'POST', 
			data: {'act': 'ajax_delete','gid':gid},
			url: './inventory_goods_input.act.php',  
			dataType: 'html', 
			async: true, 
			beforeSend: function(){ 

			},  
			success: function(resulte){ 
				if(resulte=="Y"){
					alert("정상적으로 삭제 되었습니다.");
					location.reload();
				}else if(resulte=="N"){
					alert('상품 또는 옵션에 재고가 맵핑되어있어 삭제하실수 없습니다. 상품및 옵션을 먼저 삭제후 진행해주세요.');
				}else{
					alert('삭제시 오류가 발생했습니다.');
				}
			} 
		});
	}
}

function DeleteUnit(thisObj){
	
	var gid = $('#gid').val();
	var unit = thisObj.closest('tr').find('#unit').val();

	$.ajax({ 
		type: 'POST', 
		data: {'act': 'ajax_delete_check','gid':gid,'unit':unit},
		url: './inventory_goods_input.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
			
		},  
		success: function(resulte){ 
			if(resulte=="Y"){
				if($('#unit_table').find('tr.unit_tr').length > 1){
					thisObj.closest('tr').remove();
					compositionBarcode();
				}else{
					$('#unit_table').find('tr.unit_tr input').val('');
				}
			}else if(resulte=="N"){
				alert('상품 또는 옵션에 재고가 맵핑되어있어 삭제하실수 없습니다. 상품및 옵션을 먼저 삭제후 진행해주세요.');
			}else{
				alert('삭제시 오류가 발생했습니다.');
			}
		}
	});
	
}

function ChangeUnit(obj){
	//alert($("#"+obj.id+" option:selected").text());
	var selected_text = $("#"+obj.id+" option:selected").text();
	$("#unit_text").text(selected_text);
	$("#unit").val($("#"+obj.id+" option:selected").val());//.attr("selected", "selected");
	$("#change_amount").val(1);
	compositionBarcode();
	//$("#unit").
}

function AddUnit(tbName){

   var tbody = $('#' + tbName + ' tbody');

   var total_rows = parseInt(tbody.find('tr.unit_tr:last').attr('depth'))+1;  

   var newRow = tbody.find('tr.unit_tr:first').clone(true).attr('depth',total_rows).appendTo(tbody);
	
	newRow.find("div[id^=unit_text]").css('display','none');  
	newRow.find("select[id^=unit]").css('display','inline');  
	newRow.find("select[id^=unit]").attr("name","goods_unit["+(total_rows)+"][unit]");
	newRow.find("input[id^=b_unit]").attr("name","goods_unit["+(total_rows)+"][b_unit]");
	newRow.find("input[id^=b_unit]").val('');

	newRow.find("input[id^=change_amount]").attr("name","goods_unit["+(total_rows)+"][change_amount]").val('');
	newRow.find("input[id^=buying_price]").attr("name","goods_unit["+(total_rows)+"][buying_price]").val('');
	newRow.find("input[id^=wholesale_price]").attr("name","goods_unit["+(total_rows)+"][wholesale_price]").val('');
	newRow.find("input[id^=sellprice]").attr("name","goods_unit["+(total_rows)+"][sellprice]").val('');
	newRow.find("input[id^=weight]").attr("name","goods_unit["+(total_rows)+"][weight]").val('');
	newRow.find("input[id^=width_length]").attr("name","goods_unit["+(total_rows)+"][width_length]").val('');
	newRow.find("input[id^=depth_length]").attr("name","goods_unit["+(total_rows)+"][depth_length]").val('');
	newRow.find("input[id^=height_length]").attr("name","goods_unit["+(total_rows)+"][height_length]").val('');

	//return newRow;
}

function AddStandard(tbName){

   var tbody = $('#' + tbName + ' tbody');

   var total_rows = parseInt(tbody.find('tr.standard_tr:last').attr('depth'))+1;  

   var newRow = tbody.find('tr.standard_tr:first').clone(true).attr('depth',total_rows).appendTo(tbody);
	
	newRow.find("input[id^=standard]").attr("name","standard["+(total_rows)+"][standard]").val('');
	newRow.find("input[id^=gid]").attr("name","standard["+(total_rows)+"][gid]").val('').attr("readonly",false);
	newRow.find("input[id^=barcode]").attr("name","standard["+(total_rows)+"][barcode]").val('');
	newRow.find("input[id^=etc]").attr("name","standard["+(total_rows)+"][etc]").val('');
	compositionBarcode();
}

function compositionBarcode(){
	

	var barcode_input_data = new Array();
	var barcode_input_html="";
	var unit;
	var depth;
	var unit_text;
	var barcode_value;
	
	$('#standard_table tr.standard_tr').each(function(){
		depth = $(this).attr('depth');
		barcode_input_data[depth] = new Array();

		$(this).find('[id^=barcode]').each(function(i){
			barcode_input_data[depth][i] = $(this).val();
		});
	});

	$('#standard_table tr.standard_tr').each(function(){

		depth = $(this).attr('depth');
		barcode_td = $(this).find('td.barcode_td');

		$('#unit_table tbody').find('select[id^=unit]').each(function(i){
			
			unit = $(this).val();
			unit_text = $(this).find(':selected').text().replace('단위',"<span class='red'>미지정</span>");
			
			if(barcode_input_data[depth][i])
				barcode_value = barcode_input_data[depth][i];
			else
				barcode_value = "";

			barcode_input_html += ""+unit_text+" : <input type=text class='textbox point_color number' name='standard["+depth+"][barcode]["+unit+"]' unit='"+unit+"' id='barcode' style='width:70%;margin-bottom:3px;' value='"+barcode_value+"'><br/>";

		});
		
		barcode_td.html(barcode_input_html);
		barcode_input_html="";
	});
	
}

function valueCheck(this_, obj_name){
	//alert(this_.id);
	//alert($(this_).val());
	
		$.post("inventory_goods_input.act.php", {
		  value: $("#"+this_.id).val(),
		  act: "value_check_jquery",
		  value_name:this_.id
		}, function(data){
			  //alert(this_.id);
			
			if(data == "300") {
				$("#"+this_.id+"_check_text").css("color","#00B050").html('사용 가능한 '+obj_name+' 입니다.');
				$("#"+this_.id+"_flag").val('1');
				$("#"+this_.id).attr("dup_check",'true');
				//alert($("#"+this_.id).attr("dup_check"));
			} else if(data == "130") {
				$("#"+this_.id+"_check_text").css("color","#FF5A00").html(''+obj_name+'는 6자이상 입력해 주세요.');//16자 이하로 
				$("#"+this_.id+"_flag").val('0');
				$("#"+this_.id).attr("dup_check",'false');
				return false;
			} else if(data == "120") {
				$("#"+this_.id+"_check_text").css("color","#FF5A00").html('이미 사용중인 '+obj_name+' 입니다.');
				$("#"+this_.id+"_flag").val('0');
				$("#"+this_.id).attr("dup_check",'false');
				return false;
			} else if(data == "110") {
				$("#"+this_.id+"_check_text").css("color","#FF5A00").html('첫글자는 영문으로, 다음은 영문(소문자)과 숫자의 조합만 가능합니다.');
				$("#"+this_.id+"_flag").val('0');
				$("#"+this_.id).attr("dup_check",'false');
				return false;
			} else {
				$("#"+this_.id+"_check_text").css("color","#FF5A00").html('이미 사용중인 '+obj_name+' 입니다.');
				//$("#"+this_.id+"_check_text").html(data);
				$("#"+this_.id+"_flag").val('0');
				$("#"+this_.id).attr("dup_check",'false');
				return false;
			}
			
		});
}

$(document).ready(function() {
	//$('#basic_unit').combobox();
	//$('.combobox').combobox();

});


