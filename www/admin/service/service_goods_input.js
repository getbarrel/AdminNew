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

function loadService(sel,target) {
	
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	
	var depth = sel.getAttribute('depth');
//	document.write('service_div.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
	window.frames['act'].location.href = 'service_div.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	

}


function newCopyOptions(){
	var option_target_obj = $('#$basic_option_zone').find('table[id^=options_input]:last');
	var option_obj = $('#basic_option_zone')

	 var newRow = option_obj.find('table[id^=options_input]:first');
	 var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(option_obj);  
}

function copyOptions(obj){
//var objs = eval("document.all."+obj);
var objs=$("."+obj);
//alert(objs.length);
//var objs = document.getElmentById(obj);
//alert(obj);
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
//alert(objs.length+":::"+target_obj.outerHTML);
//alert(obj_table.parentElement);
//alert(typeof(objs[0].outerHTML));
obj_table_text = obj_table.outerHTML;
//alert(obj_table.id);
if(obj_table.id == "options_input"){
	obj_table_text = obj_table.outerHTML.replace(/options_item_input_0/g,"options_item_input_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options_item_option_div_0/g,"options_item_option_div_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options_input_status_area_0/g,"options_input_status_area_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options_item_option_code_0/g,"options_item_option_code_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/option_name_area_0/g,"options_input_status_area_"+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options_basic_item_input_table_0/g,"options_basic_item_input_table_"+(parseInt(select_idx)+1));
	
	
	obj_table_text = obj_table_text.replace(/ idx=\"0\"/g," idx="+(parseInt(select_idx)+1));
	obj_table_text = obj_table_text.replace(/options\[0\]/g,"options["+(parseInt(select_idx)+1)+"]");
	//obj_table_text = obj_table_text.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all."+obj+".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert('마지막 한개는 삭제 하실 수 없습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");
	var child_txt = "$('."+obj+"[idx="+(parseInt(select_idx)+1)+"]')[0]";
	var parent_txt = "$('."+obj+"[idx="+(parseInt(select_idx)+1)+"]')";
	//alert(obj_table_text);
	obj_table_text = obj_table_text.replace("<!-- 옵션 삭제 -->"," <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('."+obj+"').length > 1){"+parent_txt+".remove();/*this.parentNode.parentNode.parentNode.removeNode(true);*/}else{alert(language_data['goods_input.js']['W'][language]);}\" title='더블클릭시 해당 테이블이 삭제 됩니다.'>");// 호환성 2011-04-08 kbk
	//'마지막 한개는 삭제 하실 수 없습니다.'
	//alert(obj_table_text);
	//document.write(select_idx+"::"+obj_table_text);
	
}else if(obj_table.id == "add_img_input_item") { //추가 이미지 등록 kbk

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
	obj_table_text+="<b class=small> "+deepzoom_image_create+"</b><input type=checkbox name='addimages[0][add_copy_deepzoomimg]' value=1 >";
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
}else{// 옵션 항목 추가 부분
	//2010 . 01. 11 일 신훈식 주석처리 이부분은 옵션 세트별로 추가 되는게 맞음
	//obj_table_text = obj_table.outerHTML.replace(/options_item_input_0/g,"options_item_input_"+(parseInt(select_idx)));
	
	//obj_table_text = obj_table.outerHTML.replace(/options_item_option_div_0/g,"options_item_option_div_"+(parseInt(select_idx)));
	//obj_table_text = obj_table.outerHTML.replace(/options_item_option_code_0/g,"options_item_option_code_"+(parseInt(select_idx)));
	
	//obj_table_text = obj_table.outerHTML.replace(/options_basic_item_input_0/g,"options_basic_item_input_"+(parseInt(select_idx)));
																								
																								
														
	obj_table_text = obj_table_text.replace(/detail_idx=\"0\"/g,"detail_idx="+(parseInt(target_obj.getAttribute('detail_idx'))+1));
	obj_table_text = obj_table_text.replace(/ idx=\"0\"/g," idx="+(parseInt(select_idx)));
	obj_table_text = obj_table_text.replace(/\[details\]\[0\]/g,"[details]["+(parseInt(target_obj.getAttribute('detail_idx'))+1)+"]");
	obj_table_text = obj_table_text.replace(/display_options\[0\]/g,"display_options["+(parseInt(select_idx)+1)+"]");
	//obj_table_text = obj_table_text.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:hand;' ondblclick=\"if(document.all."+obj+".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert('마지막 한개는 삭제 하실 수 없습니다.');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>");

	obj_table_text = obj_table_text.replace(/opt_idx=\"0\"/g,"opt_idx="+(parseInt(select_idx)+1));
	var child_txt="$('."+obj+"[idx="+(parseInt(select_idx)+1)+"]')[0]";
	var parent_txt=child_txt+'.parentNode';
	obj_table_text = obj_table_text.replace("<!-- 옵션 삭제 -->","<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('."+obj+"').length > 1){"+parent_txt+".removeChild("+child_txt+");/*this.parentNode.parentNode.parentNode.removeNode(true);*/}else{alert(language_data['goods_input.js']['W'][language]);}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>");
	//'마지막 한개는 삭제 하실 수 없습니다.'
	
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
	if(obj_table.id == "options_input"){
		var opn_ix_hidden=document.getElementsByName("options["+(parseInt(select_idx)+1)+"][opn_ix]")[0];
		//alert(opn_ix_hidden.value);
		opn_ix_hidden.value="";
	}
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
		alert(language_data['goods_input.js']['A'][language]);	
		//'판매가격이 입력되지 않았습니다.'
		return false;
	}
	
	
	if(frm.rate1.value.length < 1)	{
		alert(language_data['goods_input.js']['B'][language]);	
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

function ProductInput(frm,act)
{
	frm.act.value = act;
	
	frm.listprice.value = filterNum(frm.listprice.value);
	frm.sellprice.value = filterNum(frm.sellprice.value);
	frm.coprice.value = filterNum(frm.coprice.value);
	doToggleText(frm);
	
	//var categorys = document.getElementById("_category"); //kbk
	var categorys=document.getElementsByName('category[]');
	//if(categorys.length < 3){
	if(categorys.length < 1){
		alert(language_data['goods_input.js']['E'][language]);//'카테고리를 선택해주세요'
		return false;
	}

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	
	
	//frm.basicinfo.value = iView.document.body.innerHTML;
	frm.basicinfo.value = document.getElementById("iView").contentWindow.document.body.innerHTML;
	/*if(frm.coprice.value.length < 1)
	{
		alert(language_data['goods_input.js']['F'][language]);
		//"공급가격이 입력되지 않았습니다."
		frm.coprice.focus();
		return false;
	}*/
	/*
	if (frm.shotinfo.value.length < 1){
		alert(language_data['goods_input.js']['G'][language]);
		//'제품소개가 입력되지 않았습니다.'
		return false;	
	}
	*/
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
				return false;
			}
	}

	
	//var option_names = document.all.option_name;
	var option_names = $("input[inputid=option_name]");
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
/*
function copyAddImageCheckAll(){
	//var add_copy_img = document.all.add_copy_img;
	var add_copy_img = $("input[inputid=add_copy_img]");
	var add_copy_allimg = $("input[inputid=add_copy_allimg]");
	//var all_bool = document.all.add_copy_allimg.checked
	var all_bool = $("input[inputid=add_copy_allimg]").attr("checked");

	for(i=0;i < add_copy_img.length;i++){
		if(all_bool){
			add_copy_img[i].checked = true;	
		}else{
			add_copy_img[i].checked = false;	
		}
	}
	for(i=0;i < add_copy_allimg.length;i++){
		if(all_bool){
			add_copy_allimg[i].checked = true;	
		}else{
			add_copy_allimg[i].checked = false;	
		}
	}
}
*/
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
	/*for(i=0;i < add_copy_allimg.length;i++){
		if(all_bool){
			add_copy_allimg[i].checked = true;	
		}else{
			add_copy_allimg[i].checked = false;	
		}
	}*/
}


function ChnageImg(img_path)
{
	document.getElementById('viewimg').innerHTML = "<img src='"+img_path+"' id=chimg>";
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
		alert(language_data['goods_input.js']['K'][language]);	
		//'옵션구분값을 입력해주세요'
		frm.option_div.focus();
		return false;
	}
	
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
		/*
		if(frm.option_m_price.value.length < 1){
			alert(language_data['goods_input.js']['X'][language]);	//'옵션별 회원가를 입력해주세요'
			frm.option_m_price.focus();
			return false;
		}
		
		if(frm.option_d_price.value.length < 1){
			alert(language_data['goods_input.js']['O'][language]);	//'옵션별 딜러가를 입력해주세요'
			frm.option_d_price.focus();
			return false;
		}
		
		if(frm.option_a_price.value.length < 1){
			alert(language_data['goods_input.js']['P'][language]);	//'옵션별 대리점가를 입력해주세요'
			frm.option_a_price.focus();
			return false;
		}
		*/
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
}

function commi(commi){
	var frm = document.product_input.listprice.value;
	document.product_input.coprice.value = frm - (frm*(commi/100));
	document.product_input.bcoprice.value = frm - (frm*(commi/100));
}

function commissionChange(frm){	
	var listprice = filterNum(frm.listprice.value);
	if(eval("frm.one_commission[0].checked")){
		/*frm.coprice.value = parseInt(listprice) - parseInt(Math.round(listprice*(frm.commission.getAttribute("company_commission")/100)));
		frm.commission.value = frm.commission.getAttribute("company_commission");*/
		frm.commission.value=0;
		frm.commission.disabled=true;
		frm.commission.setAttribute("validation","false");
	}else{
		/*frm.coprice.value = parseInt(listprice) - parseInt(Math.round(listprice*(frm.commission.getAttribute("goods_commission")/100)));	
		frm.commission.value = frm.commission.getAttribute("goods_commission");*/
		frm.commission.disabled=false;
		frm.commission.setAttribute("validation","true");
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
	var area = new Array('GoodsInfo','buyingServiceInfo','AuctionInfo','hotcon','CarArea','RealEstateArea','TravelHotelArea','TravelHotelArea','TravelTourismArea');
	
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
			if(document.getElementById(area[i])){
				document.getElementById(area[i]).style.display = 'none';
			}
			//document.getElementById(tab[i]).className = '';
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

function stockCheck(vtype){
	if(vtype == "1"){
		$('#stock').attr('readonly',false);
		$('#safestock').attr('readonly',false);
	}else{
		$('#stock').attr('readonly',true);
		$('#safestock').attr('readonly',true);
		//document.getElementById('stock').readonly = true;
		//document.getElementById('safestock').readonly = true;
	}
}
function deliveryTypeView(type){
	var frm=document.product_input;
	if(type == "2"){

		document.getElementById('policy_input').style.display = "";

		document.getElementById('policy_text').style.display = "none";
		document.getElementById("delivery_product_policy_1").checked=true;
		no_pay_delivery(1);
	}else{

		document.getElementById('policy_input').style.display = "none";

		document.getElementById('policy_text').style.display = "";
		document.getElementById("delivery_product_policy_1").checked=true;
		no_pay_delivery(1);
	}
}

function no_pay_delivery(gubun) { //kbk
	var frm=document.product_input;
	frm.delivery_price.value="";
	if(gubun==1) {
		frm.delivery_price.disabled=true;
		/*frm.free_delivery_yn[0].disabled=false;
		frm.free_delivery_yn[0].checked=true;
		frm.free_delivery_yn[1].disabled=true;
		frm.free_delivery_count.value="";
		frm.free_delivery_count.disabled=true;*/
		document.getElementById("delivery_package_y").checked=false;
		//document.getElementById("delivery_package_y").style.display="";
		document.getElementById("package_input").style.display="";
		document.getElementById("package_text").style.display="none";
	} else {
		frm.delivery_price.disabled=false;
		/*frm.free_delivery_yn[1].disabled=false;
		frm.free_delivery_yn[1].checked=true;
		frm.free_delivery_count.disabled=false;*/
		document.getElementById("delivery_package_y").checked=true;
		//document.getElementById("delivery_package_y").style.display="none";
		document.getElementById("package_input").style.display="none";
		document.getElementById("package_text").style.display="";
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

	//$('#end_timepicker').timepicker();
});

function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$("#start_datepicker").val(FromDate);
	$("#end_datepicker").val(ToDate);
}