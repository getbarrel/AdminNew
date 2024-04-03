function copyOptions(obj){

var objs = eval("document.all."+obj);
//var objs = document.getElmentById(obj);
//alert(obj);
if(objs.length > 0 ){
	//alert(objs[0]);
	var obj_table = objs[0].cloneNode(true);
	var target_obj = objs[objs.length-1];	
	var select_idx = objs.length-1; //objs.length;
}else{
	
	var obj_table = objs.cloneNode(true);
	var target_obj = objs;
	var select_idx = 0;//1;
}

//alert(objs.length+":::"+target_obj.outerHTML);
//alert(obj_table.parentElement);
obj_table_text = obj_table.outerHTML;

if(obj_table.id == "options_input"){
	
	obj_table_text = obj_table.outerHTML.replace(/options_item_input_0/g,"options_item_input_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options_item_option_div_0/g,"options_item_option_div_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options_input_status_area_0/g,"options_input_status_area_"+(parseInt(select_idx)+1));
	
	obj_table_text = obj_table_text.replace(/idx=\"0\"/g,"idx="+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options\[0\]/g,"options["+(parseInt(select_idx)+1)+"]");
	obj_table_text = obj_table_text.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all."+obj+".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert(language_data['goods_input_quick.js']['A'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");
	
	//document.write(select_idx+"::"+obj_table_text);
	
}else{
	obj_table_text = obj_table.outerHTML.replace(/options_item_input_0/g,"options_item_input_"+(parseInt(select_idx)));
	obj_table_text = obj_table.outerHTML.replace(/options_item_option_div_0/g,"options_item_option_div_"+(parseInt(select_idx)));
	
	//obj_table_text = obj_table.outerHTML.replace(/options_basic_item_input_0/g,"options_basic_item_input_"+(parseInt(select_idx)));
																								
																								
																								
	obj_table_text = obj_table_text.replace(/detail_idx=\"0\"/g,"detail_idx="+(parseInt(target_obj.detail_idx)+1));
	obj_table_text = obj_table_text.replace(/idx=\"0\"/g,"idx="+(parseInt(select_idx)));
	obj_table_text = obj_table_text.replace(/\[details\]\[0\]/g,"[details]["+(parseInt(target_obj.detail_idx)+1)+"]");
	obj_table_text = obj_table_text.replace(/display_options\[0\]/g,"display_options["+(parseInt(select_idx)+1)+"]");
	obj_table_text = obj_table_text.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all."+obj+".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert(language_data['goods_input_quick.js']['A'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");
	
	obj_table_text = obj_table_text.replace(/add_copy_img_0/g,"add_copy_img_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/addimages\[0\]/g,"addimages["+(parseInt(select_idx)+1)+"]");
	
	//alert(select_idx+"::"+obj_table_text);
}
//alert(obj_table_text);
/*
if(obj_table.childNodes[2].childNodes[0].childNodes[0].childNodes[0].id = 'option_opn_ix'){
	obj_table.childNodes[2].childNodes[0].childNodes[0].childNodes[0].value = '';
}
*/
//alert(obj_table.childNodes[2].childNodes[0].childNodes[0].childNodes[0].value);
target_obj.insertAdjacentHTML("afterEnd",obj_table_text);

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
	
	var rate1 = frm.rate1.value;
	//var rate2 = frm.rate2.value;
	
	if(frm.sellprice.value.length < 1)
	{
		alert(language_data['goods_input_quick.js']['B'][language]);	//'판매가격이 입력되지 않았습니다.'
		return false;
	}
	
	
	if(frm.rate1.value.length < 1)	{
		alert(language_data['goods_input_quick.js']['C'][language]);	//'현금사용시 적립율이 입력되지 않았습니다.'
		return false;
	}else{
		rate1 = rate1/100
	}
	/*
	if(frm.rate2.value.length < 1)	{
		alert(language_data['goods_input_quick.js']['D'][language]);	//'카드사용시 적립율이 입력되지 않았습니다.'
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

return str.replace(re, "");
}

function calcurate_maginrate(frm){
	
	if(frm.sellprice.value.length < 1)	{
		nsellprice = 0;
	}else{
		nsellprice = filterNum(frm.sellprice.value);
	}
	
	if(frm.coprice.value.length < 1)	{
		ncoprice = 0;
	}else{
		ncoprice = filterNum(frm.coprice.value);
	}
	
	if(nsellprice == 0){
		frm.basic_margin.value = "-" ;
	}else{
		frm.basic_margin.value = round((nsellprice-ncoprice)/nsellprice * 100,1) ;
	}
}


function calcurate_margin(frm){	
	
	var card_pay = frm.card_pay.value;
	
	var basic_margin = frm.basic_margin.value/1;
	var sellprice = filterNum(frm.sellprice.value)/1;
//	pricecheckmode = true;
	var reserve = frm.rate1.value/100;

//alert(sellprice);
	
	if(frm.sellprice.value.length < 1)
	{
		alert(language_data['goods_input_quick.js']['B'][language]);	//'판매가격이 입력되지 않았습니다.'
		return false;
	}
	
	
	if(frm.card_pay.value.length < 1)	{
		alert(language_data['goods_input_quick.js']['F'][language]);	//'카드수수료가 입력되지 않았습니다.'
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

function ProductInput(frm,act)
{
	frm.act.value = act;
	
	frm.listprice.value = filterNum(frm.listprice.value);
	frm.sellprice.value = filterNum(frm.sellprice.value);
	frm.coprice.value = filterNum(frm.coprice.value);
	doToggleText(frm);
	
	var categorys = document.all._category;
	if(categorys.length < 3){
		alert(language_data['goods_input_quick.js']['G'][language]);	//'카테고리를 선택해주세요'
		return false;
	}
	
	if (frm.pname.value.length < 1){
		alert(language_data['goods_input_quick.js']['H'][language]);	//'제품명이 입력되지 않았습니다.'
		return false;	
	}
	
	
	var surtax_yorn_bool = false;
	for(i=0; i < frm.surtax_yorn.length;i++){
		if(frm.surtax_yorn[i].checked){
			surtax_yorn_bool = true;
		}
	}
	
	if(!surtax_yorn_bool){
		alert(language_data['goods_input_quick.js']['I'][language]);	//'면세여부를 선택해주세요'
		
		return false;	
	}
	
	/*if (frm.delivery_method.value.length < 1){
		alert(language_data['goods_input_quick.js']['J'][language]);	//'배송방법이 선택되지않았습니다. 배송방법을 선택해주세요.'
		return false;	
	}*/
	
	/*var pack_method_bool = false;
	for(i=0; i < frm.pack_method.length;i++){
		if(frm.pack_method[i].checked){
			pack_method_bool = true;
		}
	}
	
	if(!pack_method_bool){
		alert(language_data['goods_input_quick.js']['K'][language]);	//'포장 방법을 선택해주세요'
		//frm.pack_method[0].focus();
		return false;	
	}*/
	
	/*
	if (frm.sellprice.value != frm.bsellprice.value){
		alert(language_data['goods_input_quick.js']['L'][language]);	//"가격에 대한 정보가 변경되었습니다."
		frm.sellprice.value = FormatNumber3(frm.sellprice.value);
		return false;
	}*/
	
	frm.basicinfo.value = iView.document.body.innerHTML;
	if(frm.coprice.value.length < 1)
	{
		alert(language_data['goods_input_quick.js']['M'][language]);	//"공급가격이 입력되지 않았습니다."
		return false;
	}
	/*
	if (frm.shotinfo.value.length < 1){
		alert(language_data['goods_input_quick.js']['N'][language]);	//'제품소개가 입력되지 않았습니다.'
		return false;	
	}
	*/
	var options_price_stock_option_divs = document.all.options_price_stock_option_div;
	var options_price_stock_option_div_str = "|";
	
	//alert(options_price_stock_option_divs.length);
	for(j=0;j < options_price_stock_option_divs.length;j++){
			if(options_price_stock_option_divs[j].value){
				options_price_stock_option_div_str += options_price_stock_option_divs[j].value+"|";				
			}
	}
	for(j=0;j < options_price_stock_option_divs.length;j++){
			if(options_price_stock_option_divs[options_price_stock_option_divs.length-j-1].value && substr_count(options_price_stock_option_div_str, "|"+options_price_stock_option_divs[options_price_stock_option_divs.length-j-1].value+"|") > 1){
				alert(language_data['goods_input_quick.js']['O'][language]);	//'중복된 가격+재고 옵션구분명이 있습니다. 수정후 다시 시도해주세요'
				options_price_stock_option_divs[options_price_stock_option_divs.length-j-1].focus();
				return false;
			}
	}
	
	var option_names = document.all.option_name;
	var option_names_str = "|";
	//var _option_names = new Array();
	
	var option_details_divs = "";
	var option_details_div_str = "";
	
	for(i=0;i < option_names.length;i++){		
			option_names_str += option_names[i].value+"|";		
			//_option_names[i] = option_names[i].value
	}
	//alert(option_names_str);
	
	
	for(i=0;i < option_names.length;i++){		
		//alert(in_array(option_names[i].value,_option_names));
		//alert(substr_count(option_names_str, "|"+option_names[option_names.length-i-1].value+"|"));
		if(option_names[option_names.length-i-1].value){
			if(option_names[option_names.length-i-1].value && substr_count(option_names_str, "|"+option_names[option_names.length-i-1].value+"|") > 1){
				alert(language_data['goods_input_quick.js']['P'][language]);	//'중복된 옵션명이 있습니다. 수정후 다시 시도해주세요'
				option_names[option_names.length-i-1].focus();
				return false;
			}else{
				option_details_divs = eval("document.all.options_item_option_div_"+i);
				option_details_div_str = "|";
				
				for(j=0;j < option_details_divs.length;j++){		
					//alert(option_details_divs[j].value);
					if(option_details_divs[j].value){
						option_details_div_str += option_details_divs[j].value+"|";	
					}	
						//_option_names[i] = option_names[i].value
				}
				
				for(j=0;j < option_details_divs.length;j++){		
					//alert(option_details_div_str+":::"+substr_count(option_details_div_str, "|"+option_details_divs[option_details_divs.length-j-1].value+"|"));
						if(option_details_divs[option_details_divs.length-j-1].value && substr_count(option_details_div_str, "|"+option_details_divs[option_details_divs.length-j-1].value+"|") > 1){							
							alert(language_data['goods_input_quick.js']['Q'][language]);
							//'중복된 옵션구분명이 있습니다. 수정후 다시 시도해주세요'
							alert(option_details_divs[option_details_divs.length-j-1].outerHTML);
							option_details_divs[option_details_divs.length-j-1].focus();
							return false;
						}
				}
			}
		}
	}
	
	var display_option_titles = document.all.display_option_title;
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
				alert(language_data['goods_input_quick.js']['R'][language]);
				//'중복된 디스플레이 옵션명이 있습니다. 수정후 다시 시도해주세요'
				display_option_titles[display_option_titles.length-i-1].focus();
				return false;			
			}
		}
	}
	if (document.getElementById("InnoAP").GetCount < 1 && ip_AllwaysWithFile == true)
	{
		frm.submit();	
	}else{
		StartUpload();
	}
	//
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
	var copy_image = document.all.copy_img;
	var all_bool = document.all.copy_allimg.checked
	for(i=0;i < copy_image.length;i++){
		if(all_bool){
			copy_image[i].checked = true;	
		}else{
			copy_image[i].checked = false;	
		}
	}
}

function copyAddImageCheckAll(){
	var add_copy_img = document.all.add_copy_img;
	var all_bool = document.all.add_copy_allimg.checked
	for(i=0;i < add_copy_img.length;i++){
		if(all_bool){
			add_copy_img[i].checked = true;	
		}else{
			add_copy_img[i].checked = false;	
		}
	}
}


function ChnageImg(vsize,vid, img_path)
{
	document.getElementById('viewimg').innerHTML = "<img src='"+img_path+"/"+vsize+"_"+vid+".gif' id=chimg>";
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
	document.frames["act"].location.href='./img.add.php?act='+act+'&id='+id+'&pid='+pid; 
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

function showPriceTabContents(vid, tab_id){
	var area = new Array('price_info','detail_price_info');
	var tab = new Array('p_tab_01','p_tab_02');
	
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


function dpCheckOptionData(frm){
	if(frm.dp_title.value.length < 1){
		alert(language_data['goods_input_quick.js']['S'][language]);	//'옵션구분값을 입력해주세요'
		frm.option_div.focus();
		return false;
	}
	
	if(frm.dp_desc.value.length < 1){
		alert(language_data['goods_input_quick.js']['T'][language]);	//'옵션별 비회원가를 입력해주세요'
		frm.option_price.focus();
		return false;
	}
	
	return true;
}


function CheckOptionData(frm){
	var option_kind = frm.option_kind.value
	if(option_kind == 'b' || option_kind == 'p'){
		if(frm.opn_ix.value == ""){
			alert(language_data['goods_input_quick.js']['U'][language]);	//'옵션이름을 선택해주세요'
			frm.option_div.focus();
			return false;
		}
		
		if(frm.option_div.value.length < 1){
			alert(language_data['goods_input_quick.js']['S'][language]);	//'옵션구분값을 입력해주세요'
			frm.option_div.focus();
			return false;
		}
		
		if(frm.option_price.value.length < 1){
			alert(language_data['goods_input_quick.js']['T'][language]);	//'옵션별 비회원가를 입력해주세요'
			frm.option_price.focus();
			return false;
		}
		/*
		if(frm.option_m_price.value.length < 1){
			alert(language_data['goods_input_quick.js']['W'][language]);	//'옵션별 회원가를 입력해주세요'
			frm.option_m_price.focus();
			return false;
		}
		
		if(frm.option_d_price.value.length < 1){
			alert(language_data['goods_input_quick.js']['X'][language]);	//'옵션별 딜러가를 입력해주세요'
			frm.option_d_price.focus();
			return false;
		}
		
		if(frm.option_a_price.value.length < 1){
			alert(language_data['goods_input_quick.js']['A'][language]);	//'옵션별 대리점가를 입력해주세요'
			frm.option_a_price.focus();
			return false;
		}
		*/
	}else if(option_kind == 's'){
		if(frm.opn_ix.value == ""){
			alert(language_data['goods_input_quick.js']['U'][language]);	//'옵션이름을 선택해주세요'
			frm.option_div.focus();
			return false;
		}
		
		if(frm.option_div.value.length < 1){
			alert('옵션구분값을 입력해주세요');	
			frm.option_div.focus();
			return false;
		}
	}else{
		alert(language_data['goods_input_quick.js']['S'][language]);	//'옵션이름을 선택해주세요'
		return false;	
	}
	
	return true;
}

function deleteOption(act,id,pid, opn_ix){
	document.frames["act"].location.href='./option.act.php?act='+act+'&id='+id+'&pid='+pid+'&opn_ix='+opn_ix; 
}

function deleteDisplayOption(act,dp_ix,pid){
	document.frames["act"].location.href='./display_option.act.php?act='+act+'&dp_ix='+dp_ix+'&pid='+pid; 
}

//function UpdateOption(option_id, option_div, option_price,option_m_price,option_d_price,option_a_price, option_stock, option_safestock, option_etc1){	
function UpdateOption(option_id, option_div, option_price, option_stock, option_safestock, option_etc1){	
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
	frm.option_etc1.value = option_etc1;
	
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
        if(isNaN(num)) { alert(language_data['goods_input_quick.js']['Z'][language]);return 0}
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
        if(isNaN(num)) { alert(language_data['goods_input_quick.js']['Z'][language]);return 0}
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
                alert(language_data['goods_input_quick.js']['Z'][language]+"["+keyCode+"]")
                event.returnValue=false
        }
        
}


//콤마표현 없는 정수만입력
function onlyEditableNumber(obj){
 var str = obj.value;
 str = new String(str);
 var Re = /[^0-9]/g;  
 str = str.replace(Re,''); 
 obj.value = str;
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
	                alert(language_data['goods_input_quick.js']['AA'][language]);//"자릿수는 정수로만 구분합니다."
	                return false;
	 }

}


function commaSplit(srcNumber) {
	var txtNumber = '' + srcNumber;
	if (isNaN(txtNumber) || txtNumber == "") {
		//alert(language_data['goods_input_quick.js']['AB'][language]);
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
			alert(language_data['goods_input_quick.js']['AC'][language]);	
			//'구매단가(공급가)를 입력후 복사 버튼을 클릭해주세요'
			return false;
		}
		frm.listprice.value = copy_price;
		frm.sellprice.value = copy_price;	
	}else if(step == 2){
		if(copy_price == ""){
			alert(language_data['goods_input_quick.js']['AD'][language]);	//'정가를 입력후 복사 버튼을 클릭해주세요'
			return false;
		}
		frm.sellprice.value = copy_price;
			
	}
}
*/

function copyPrice(frm, copy_price, step,commi){
	
	if(step == 2){
		if(copy_price == ""){
			alert(language_data['goods_input_quick.js']['AE'][language]);	//'판매가를 입력후 복사 버튼을 클릭해주세요'
			return false;
		}
		var per = 100;
		//alert(commi);
		frm.listprice.value = copy_price;
		//frm.coprice.value = copy_price;//- (copy_price*(commi/per));
		//frm.bcoprice.value = copy_price;// - (copy_price*(commi/per));
		
		//commissionChange(frm);
	}else if(step == 1){
		frm.listprice.value = copy_price;
		//commissionChange(frm);
		//frm.price.value = copy_price;
	}
}

function commi(commi){
	var frm = document.product_input.listprice.value;
	document.product_input.coprice.value = frm - (frm*(commi/100));
	document.product_input.bcoprice.value = frm - (frm*(commi/100));
}

function commissionChange(frm){	
	var listprice = filterNum(frm.listprice.value);
	
	if(eval("frm.one_commission[0].checked")){
		frm.coprice.value = parseInt(listprice) - parseInt(Math.round(listprice*(frm.commission.company_commission/100)));
		frm.commission.value = frm.commission.company_commission;
	}else{
		frm.coprice.value = parseInt(listprice) - parseInt(Math.round(listprice*(frm.commission.goods_commission/100)));		
		frm.commission.value = frm.commission.goods_commission;
	}
	
	//document.listform.coprice.value = frm - (frm*(commi/100));
	//document.listform.bcoprice.value = frm - (frm*(commi/100));
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

function dateSelect(id, obj){
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


function ShowGoodsTypeInfo(vid){
	var area = new Array('GoodsInfo','buyingServiceInfo','AuctionInfo');
	
	for(var i=0; i<area.length; ++i){
		if(vid == "buyingServiceInfo"){			
			document.getElementById("buyingServiceInfTable").style.display = 'block';			
			document.getElementById("buyingServiceClearanceType").style.display = 'block';			
			
		}else{
			document.getElementById("buyingServiceInfTable").style.display = 'none';	
			document.getElementById("buyingServiceClearanceType").style.display = 'none';	
		}
		if(area[i]==vid){
			
			document.getElementById(vid).style.display = 'block';			
			//document.getElementById(tab_id).className = 'on';
		}else{			
			document.getElementById(area[i]).style.display = 'none';
			//document.getElementById(tab[i]).className = '';
		}
	}		
}

function ShowGiftTypeInfo(vid){
	if(vid == "enable"){
		document.getElementById('giftshopinfo').style.display = "block";
	}else{
		document.getElementById('giftshopinfo').style.display = "none";
	}
}