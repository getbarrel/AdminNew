var _mode = '';
var _cid = '';
var _depth = '';
var _nset = '1';
var _page = '1';
var	_search_type = '';
var	_search_text = '';
var	_company_id = '';


function getRelationProduct(){
//mode,nset, page,cid,depth

	var _max = $('list_max').value; 
	var args = getRelationProduct.arguments;

	if(args[0] == 'list'){
	_mode = args[0];
	_nset = args[1];
	_page = args[2];
	_cid = args[3];
	_depth = args[4];	
	_parameters_paging = 'mode='+_mode+'_pageing&cid='+_cid+'&depth='+_depth+'&page='+_page+'&max='+_max;
	_parameters_search = 'mode='+_mode+'&cid='+_cid+'&depth='+_depth+'&page='+_page+'&max='+_max;
	
	}else if(args[0] == 'search_list'){
	_mode = args[0];
	_nset = args[1];
	_page = args[2];
	_search_type = args[3];
	_search_text = args[4];	
	_company_id = args[5];	
	_parameters_paging = 'mode='+_mode+'_pageing&search_type='+_search_type+'&search_text='+_search_text+'&company_id='+_company_id+'&page='+_page+'&max='+_max;
	_parameters_search = 'mode='+_mode+'&search_type='+_search_type+'&search_text='+_search_text+'&company_id='+_company_id+'&page='+_page+'&max='+_max; 
	//alert(_parameters_search);
	
	}else{
		if(_mode == 'list'){
			_parameters_paging = 'mode='+_mode+'_pageing&cid='+_cid+'&depth='+_depth+'&page='+_page+'&max='+_max;
			_parameters_search = 'mode='+_mode+'&cid='+_cid+'&depth='+_depth+'&page='+_page+'&max='+_max;
		}else{
			_parameters_paging = 'mode='+_mode+'_pageing&search_type='+_search_type+'&search_text='+_search_text+'&company_id='+_company_id+'&page='+_page+'&max='+_max;
			_parameters_search = 'mode='+_mode+'&search_type='+_search_type+'&search_text='+_search_text+'&company_id='+_company_id+'&page='+_page+'&max='+_max; 
		}
		//alert(_mode+':::'+_nset+':::'+_max);
		
	}


	
	//http://b2bdev.mallstory.com/admin/marketting/relationAjax.category.act.php?mode=search_list_pageing&search_type=p.pname&search_text=가방&company_id=&page=1&max=5
		new Ajax.Request('./relationAjax.category.act.php',
		{
			method: 'POST',
			parameters: _parameters_paging,
			//encoding: 'UTF-8',			
			onComplete: function(transport){
			//contentType: 'application/x-www-form-urlencoded', 
				//document.write(_parameters_paging);
				//alert(transport.responseText);
				total = parseInt(transport.responseText);
				// $('list_max').value;
				//alert($('list_max').value);
				//alert(total);
				try{
					if(total > 0){
						if(_mode == 'list'){
							$('view_paging').innerHTML = page_bar(_mode, total, _nset, _page, _max, _cid, _depth);
							$('list_max').onchange= getRelationProduct;
						}else{
							$('view_paging').innerHTML = search_page_bar(_mode, total, _nset, _page, _max, _search_type, _search_text, _company_id);
							$('list_max').onchange= getRelationProduct;
						}
					}else{
						$('view_paging').innerHTML = '검색결과가 존재하지 않습니다';
					}
							
					//alert($('view_paging').innerHTML)
				}catch(e){
					alert(e.message);
				}

			}
		});

		new Ajax.Request('./relationAjax.category.act.php',
		{
			method: 'POST',
			parameters: _parameters_search,
			onComplete: function(transport){
				
					var xmlDoc = new ActiveXObject('Msxml2.DOMDocument');
					xmlDoc.async = false;
					//alert(transport.responseText);
					xmlDoc.loadXML(transport.responseText);
					
					var err = xmlDoc.parseError;
					
					if (err.errorCode != 0)
						throw new Error('XML 문서 해석 실패 - ' + err.reason);
					
					var xsl = new ActiveXObject('Microsoft.XMLDOM');
					xsl.async = false;
					xsl.load('relationAjax.xsl');
					//alert(3);
					//alert(xmlDoc.transformNode(xsl));
					document.getElementById('reg_product').innerHTML = xmlDoc.transformNode(xsl);
					
					var err = xmlDoc.parseError;
					if (err.errorCode != 0)
						throw new Error('XSL 문서 해석 실패 - ' + err.reason);
					
					var rproducts = xmlDoc.getElementsByTagName('products');
					//alert(rproducts.length);
					
					for(i=0;i<rproducts.length;i++){			
						//alert(rproducts.item(i).selectSingleNode('pid').text);
						new Draggable(rproducts.item(i).selectSingleNode('pid').text, {revert: true});;
					}
					
					//alert(Droppables.add);
					Droppables.add('drop_relation_product', { 
				    accept: 'draggable',
				    hoverclass: 'hover',
				    onDrop: function(element) { 
				    	
				    	//alert(element.id);
				    	var tb0 = document.getElementById('tb_'+element.id);
				    	var tb = document.getElementById('tb_relation_product');
				    	var pgroup_area = document.getElementById('group_product_area_'+select_gorup_code);
				    	
				    	var idx = tb.rows.length;
						
				    	if(tb.innerHTML.indexOf(element.id) < 0){
								
								oTr = tb.insertRow(idx);
								
								oTr.onclick=function() { 
									spoit(this)
								}
								oTr.ondblclick = function() { 				    		
									var _idx = this.rowIndex;
									//alert(_idx);
									tb.deleteRow(_idx);
									document.getElementById('_group_product_code_'+select_gorup_code+'_'+element.id).removeNode(true);
									//tb.deleteRow(_idx);					    
								} ;
								oTr.tr_group_code = select_gorup_code;
								oTr.tr_pid = element.id;
								oTr.style.background="url(../images/dot.gif) repeat-x bottom";
								oTr.style.height = "27px";
					    	
								//oTd = oTr.insertCell(-1);
								//oTd.innerHTML = tb0.rows[0].cells[0].innerHTML;
								oTd = oTr.insertCell(-1);
								oTd.innerHTML = tb0.rows[0].cells[1].innerHTML;
								oTd.style.padding='5px';
								oTd.style.textAlign = 'center';
								oTd = oTr.insertCell(-1);
								oTd.innerHTML = tb0.rows[0].cells[2].innerHTML;
								oTd = oTr.insertCell(-1);
								oTd.innerHTML = "<input type='hidden' name='rpid[]' value='"+element.id+"'>";
								oTd.innerHTML = "<img src='../image/btc_del.gif' style='cursor:hand;'>";
								//alert(tb.outerHTML);
								if(pgroup_area.innerHTML.indexOf(element.id) < 0){
									//pgroup_area.innerHTML = pgroup_area.innerHTML + "<div id='_group_product_code_"+select_gorup_code+"_"+element.id+"' style='display:inline;border:1px solid #efefef;margin:5 5 0 0;padding:2px;' onclick='spoitDIV(this)' ondblclick='this.removeNode(this)'>"+trim(tb0.rows[0].cells[1].innerHTML)+"<input type='hidden' name='rpid["+select_gorup_code+"][]' value='"+element.id+"'></div>";
									div_str = "<div id='_group_product_code_"+select_gorup_code+"_"+element.id+"' pid='"+element.id+"' _select_gorup_code='"+select_gorup_code+"'  style='display:inline;border:1px solid #efefef;margin:0 3 3 3;padding:2px;width:50px;height:50px;text-align:center' onclick='spoitDIV(this)' ondblclick='this.removeNode(this)'>";
									div_str += "<table id='seleted_tb_"+element.id+"' cellpadding=0 cellspacing=0 border=0 style='display:inline;'>";
									div_str += "<tr>";
									div_str += "<td style='display:none;'></td>";
									div_str += "<td>"+trim(tb0.rows[0].cells[1].innerHTML)+"</td>";
									div_str += "<td style='display:none;'>"+trim(tb0.rows[0].cells[2].innerHTML)+"</td>";
									div_str += "<td style='display:none;'><input type='hidden' name='rpid["+select_gorup_code+"][]' value='"+element.id+"'></td>";
									div_str += "</tr>";
									div_str += "</table>";
									div_str += "</div>";
									
									pgroup_area.innerHTML += div_str;
									
								}
								
							}else{
								alert(language_data['relationAjaxForHot.js']['A'][language]);//'이미등록된 상품입니다.'
							}
						
				    }
				  });
  
			}
		});
		
		return;
}

function SearchProduct(frm){

	getRelationProduct('search_list',_nset, _page,frm.search_type[frm.search_type.selectedIndex].value, frm.search_text.value, frm.company_id[frm.company_id.selectedIndex].value);
	
}


function selectGoodsList(frm){
       	for(i=0;i < frm.cpid.length;i++){
				if(frm.cpid[i].checked && frm.cpid[i].value != ''){
					//alert(frm.cpid[i].checked+':::'+frm.cpid[i].value);
					SelectedGoodsReg(frm.cpid[i].value,"select",i);
					alert(frm.cpid[i].value);

				}
		}
}

function selectedGoodsView(goods_type){
		//alert(frm.cpid.length);
		//alert(select_gorup_code);
		var selectedGoods = document.getElementById('group_product_area_'+select_gorup_code).getElementsByTagName('DIV');
		//alert(selectedGoods.length);
    for(i=0;i < selectedGoods.length;i++){				
					//alert(frm.cpid[i].checked+':::'+frm.cpid[i].value);
					SelectedGoodsReg(selectedGoods[i].getAttribute("pid"),goods_type,i);
		}
}

function SelectedGoodsReg(spid, goods_type, div_index) { 
	//alert(element.id);
	//alert(element.outerHTML);
	//$('drop_relation_product').innerHTML = $('drop_relation_product').innerHTML+element.innerHTML; 
	//alert(element.getElementsByTagName('table').innerHTML);
	if(goods_type == "select"){
		var tb0 = document.getElementById('tb_'+spid);	
	}else{
		var tb0 = document.getElementById('seleted_tb_'+spid);	
	}
	//alert(spid);
	var tb = document.getElementById('tb_relation_product');
	var pgroup_area = document.getElementById('group_product_area_'+select_gorup_code);
	
	//alert(tb0.outerHTML);
	var idx = tb.rows.length;
	if(tb.innerHTML.indexOf(spid) < 0){
		oTr = tb.insertRow(idx);		
		oTr.onclick=function() { 
			spoit(this)
		}
		oTr.ondblclick = function() { 				    		
			var _idx = this.rowIndex;
			tb.deleteRow(_idx);
			document.getElementById('_group_product_code_'+select_gorup_code+'_'+spid).removeNode(true);
		} ;
		oTr.tr_group_code = select_gorup_code;
		oTr.tr_pid = spid;
		oTr.style.background="url(../images/dot.gif) repeat-x bottom";
		oTr.style.height = "27px";		
		oTd = oTr.insertCell(-1);
		
		oTd.innerHTML = tb0.rows[0].cells[1].innerHTML;
		oTd.style.padding='5px';
		oTd.style.textAlign = 'center';
		oTd = oTr.insertCell(-1);
		oTd.innerHTML = tb0.rows[0].cells[2].innerHTML;
		oTd = oTr.insertCell(-1);
		oTd.innerHTML = "<input type='hidden' name='rpid[]' value='"+spid+"'>";
		
		//alert(tb0.rows[0].cells[1].innerHTML);
		if(pgroup_area.innerHTML.indexOf("_group_product_code_"+spid) < 0 && goods_type == "select"){
			div_str = "<div id='_group_product_code_"+select_gorup_code+"_"+spid+"' pid='"+spid+"' _select_gorup_code='"+select_gorup_code+"' div_index='"+div_index+"' style='display:inline;border:1px solid #efefef;margin:0 3 3 3;padding:2px;width:50px;height:50px;text-align:center' onclick='spoitDIV(this)' ondblclick='this.removeNode(this)'>";
			div_str += "<table id='seleted_tb_"+spid+"' cellpadding=0 cellspacing=0 border=0 style='display:inline;'>";
			div_str += "<tr>";
			div_str += "<td style='display:none;'></td>";
			div_str += "<td>"+trim(tb0.rows[0].cells[1].innerHTML)+"</td>";
			div_str += "<td style='display:none;'>"+trim(tb0.rows[0].cells[2].innerHTML)+"</td>";
			div_str += "<td style='display:none;'><input type='hidden' name='rpid["+select_gorup_code+"][]' value='"+spid+"'></td>";
			div_str += "</tr>";
			div_str += "</table>";
			div_str += "</div>";
			//alert(div_str);
			pgroup_area.innerHTML += div_str;
	
		}
		/*
		else{
		
			div_str = "<div id='_group_product_code_"+select_gorup_code+"_"+spid+"' pid='"+spid+"' div_index='"+div_index+"' style='display:inline;border:1px solid #efefef;margin:5 5 0 0;padding:2px;' ondblclick='this.removeNode(this)'>";
			div_str += "<table id='seleted_tb_"+spid+"' cellpadding=0 cellspacing=0 border=0 style='display:inline;'>";
			div_str += "<tr>";
			div_str += "<td style='display:none;'></td>";
			div_str += "<td>"+trim(tb0.rows[0].cells[1].innerHTML)+"</td>";
			div_str += "<td style='display:none;'>"+trim(tb0.rows[0].cells[2].innerHTML)+"</td>";
			div_str += "<td style='display:none;'><input type='hidden' name='rpid["+select_gorup_code+"][]' value='"+spid+"'></td>";
			div_str += "</tr>";
			div_str += "</table>";
			div_str += "</div>";
			//alert(div_str);
			pgroup_area.innerHTML += div_str;
		}
		*/
		return ;
		//alert(pgroup_area.innerHTML);
		/*
	
		*/
	
	}
}

function trim(value) {
 return value.replace(/^\s+|\s+$/g,"");
}

function deleteWhole($clipart_delete){
	var tb = document.getElementById('tb_relation_product');
	var idx = tb.rows.length;
	//alert(tb.rows.length);
	for(i=1;i <= idx;i++){
			//alert(idx-i);
			if($clipart_delete){
				document.getElementById('_group_product_code_'+select_gorup_code+'_'+tb.rows[idx-i].tr_pid).removeNode(true);
			}
			tb.deleteRow(idx-i);
//			tb.deleteRow(idx-i+1);
			
	}
}



function page_bar(mode, total, nset, page, max,cid, depth){
	//var nset = 1;
	var total_page;
	var prev_mark = "";
	var next_mark = "";
	var last_page_string ="";
	var page_size = 5;
	
	if (total % max > 0){
		total_page = Math.floor(total / max) + 1;
	}else{
		total_page = Math.floor(total / max);
	}
	
	//if (nset == ""){nset = 1;}
	
	var next = ((nset)*page_size+1);
	var prev = ((nset-2)*page_size+1);
	
	
	
	//echo total_page.":::".next."::::".prev."<br>";
	if (total){
		var prev_mark = (prev > 0) ? "<a href=\"javascript:getRelationProduct('"+mode+"',"+(nset-1)+","+((nset-2)*page_size+1)+",'"+cid+"',"+depth+");\" ><img src='/admin/image/pre_pageset.gif' border=0 align=absmiddle></a> " : "<img src='/admin/image/pre_pageset.gif' border=0 align=absmiddle> ";
		var next_mark = (next <= total_page) ? "<a href=\"javascript:getRelationProduct('"+mode+"',"+(nset+1)+","+(nset*page_size+1)+",'"+cid+"',"+depth+");\" ><img src='/admin/image/next_pageset.gif' border=0 align=absmiddle></a>" :  " <img src='/admin/image/next_pageset.gif' border=0 align=absmiddle>";
	}

	var page_string = prev_mark;
//	alert((nset-1)*page_size+1);
//	for (i = page - page_size; i <= page + page_size; i++)
	//alert((nset-1)*page_size+1);
	for (i = (nset-1)*page_size+1 ; i <= ((nset-1)*page_size + page_size); i++)
	{
		if (i > 0){			
			if (i <= total_page)
			{
				//alert(i +"!="+ page +"="+(i != page));
				if (i != page){
					if(i != ((nset-1)*page_size+1)){
						page_string = page_string+("<!--font color='silver'>|</font--> <a href=\"javascript:getRelationProduct('"+mode+"',"+nset+","+i+",'"+cid+"',"+depth+");\" style='font-weight:bold;color:gray' >"+i+"</a> ");
					}else{
						page_string = page_string+(" <a href=\"javascript:getRelationProduct('"+mode+"',"+nset+","+i+",'"+cid+"',"+depth+");\" style='font-weight:bold;color:gray' >"+i+"</a> ");
					}
					
				}else{
					if(i != ((nset-1)*page_size+1)){
						page_string = page_string+("<!--font color='silver'>|</font--> <font color=#FF0000 style='font-weight:bold'>"+i+"</font> ");
					}else{
						page_string = page_string+("<font color=#FF0000 style='font-weight:bold'>"+i+"</font> ");
					}
				}
				
				
			}
		}
	}
	if(nset < (Math.floor(total_page/page_size)+1)){
		last_page_string = "<b style='color:gray'>...</b> <a href=\"javascript:getRelationProduct('"+mode+"',"+(Math.floor(total_page/page_size)+1)+","+total_page+",'"+cid+"',"+depth+");\" style='font-weight:bold;color:gray' >"+total_page+"</a> ";
	}
	page_string = page_string+last_page_string+next_mark;
	//alert(page_string);
	return page_string;
}

function search_page_bar(mode, total, nset, page, max,search_type, search_text, company_id){
	//var nset = 1;
	var total_page;
	var prev_mark = "";
	var next_mark = "";
	var last_page_string ="";
	var page_size = 5;
	
	if (total % max > 0){
		total_page = Math.floor(total / max) + 1;
	}else{
		total_page = Math.floor(total / max);
	}
	
	//if (nset == ""){nset = 1;}
	
	var next = ((nset)*page_size+1);
	var prev = ((nset-2)*page_size+1);
	
	
	
	//echo total_page.":::".next."::::".prev."<br>";
	if (total){
		var prev_mark = (prev > 0) ? "<a href=\"javascript:getRelationProduct('"+mode+"',"+(nset-1)+","+((nset-2)*page_size+1)+",'"+search_type+"','"+search_text+"','"+company_id+"');\" ><img src='/admin/image/pre_pageset.gif' border=0 align=absmiddle></a> " : "<img src='/admin/image/pre_pageset.gif' border=0 align=absmiddle> ";
		var next_mark = (next <= total_page) ? "<a href=\"javascript:getRelationProduct('"+mode+"',"+(nset+1)+","+(nset*page_size+1)+",'"+search_type+"','"+search_text+"','"+company_id+"');\" ><img src='/admin/image/next_pageset.gif' border=0 align=absmiddle></a>" :  " <img src='/admin/image/next_pageset.gif' border=0 align=absmiddle>";
	}

	var page_string = prev_mark;
//	alert((nset-1)*page_size+1);
//	for (i = page - page_size; i <= page + page_size; i++)
	//alert((nset-1)*page_size+1);
	for (i = (nset-1)*page_size+1 ; i <= ((nset-1)*page_size + page_size); i++)
	{
		if (i > 0){			
			if (i <= total_page)
			{
				//alert(i +"!="+ page +"="+(i != page));
				if (i != page){
					if(i != ((nset-1)*page_size+1)){
						page_string = page_string+("<!--font color='silver'>|</font--> <a href=\"javascript:getRelationProduct('"+mode+"',"+nset+","+i+",'"+search_type+"','"+search_text+"','"+company_id+"');\" style='font-weight:bold;color:gray' >"+i+"</a> ");
					}else{
						page_string = page_string+(" <a href=\"javascript:getRelationProduct('"+mode+"',"+nset+","+i+",'"+search_type+"','"+search_text+"','"+company_id+"');\" style='font-weight:bold;color:gray' >"+i+"</a> ");
					}
					
				}else{
					if(i != ((nset-1)*page_size+1)){
						page_string = page_string+("<!--font color='silver'>|</font--> <font color=#FF0000 style='font-weight:bold'>"+i+"</font> ");
					}else{
						page_string = page_string+("<font color=#FF0000 style='font-weight:bold'>"+i+"</font> ");
					}
				}
				
				
			}
		}
	}
	if(nset < (Math.floor(total_page/page_size)+1)){
		last_page_string = "<b style='color:gray'>...</b> <a href=\"javascript:getRelationProduct('"+mode+"',"+(Math.floor(total_page/page_size)+1)+","+total_page+",'"+search_type+"','"+search_text+"','"+company_id+"');\" style='font-weight:bold;color:gray' >"+total_page+"</a> ";
	}
	page_string = page_string+last_page_string+next_mark;
	//alert(page_string);
	return page_string;
}



function showTabContents(vid, tab_id){
	var area = new Array('category_search','keyword_search');
	var tab = new Array('tab_01','tab_02');
	
	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';			
			document.getElementById(tab_id).className = 'on';
		}else{			
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}
}
function clearAll(frm){
		for(i=0;i < frm.cpid.length;i++){
				frm.cpid[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.cpid.length;i++){
				frm.cpid[i].checked = true;
		}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}



var iciRow, preRow;
var iciRow2, preRow2;
function spoitDIV(obj){
	//alert(obj._select_gorup_code);
	iciRow = null;
	iciRow2 = obj;//$("_group_product_code_"+obj._select_gorup_code+"_"+obj.pid+"");
	iciHighlightDIV();
}

function iciHighlightDIV(){
	//alert(iciRow2+":::"+ iciRow2);
	if (preRow2){ 		
		preRow2.style.border = "1px solid #efefef";		
		iciRow2.style.border = "2px solid silver";
	}else{		
//		iciRow2.style.backgroundColor = "gray"; // FFF4E6
		iciRow2.style.border = "2px solid silver";
	}	
	preRow2 = iciRow2;
}

function spoit(obj)
{
	
	iciRow = obj;
	iciRow2 = $("_group_product_code_"+select_gorup_code+"_"+obj.tr_pid+"");
	iciHighlight();
}



function iciHighlight(){
	
	if (preRow){ 
		preRow.style.backgroundColor = "";
		//preRow2.style.backgroundColor = "";
		iciRow.style.backgroundColor = "#efefef"; // FFF4E6
		
	}else{
		iciRow.style.backgroundColor = "#efefef"; // FFF4E6
		//iciRow2.style.backgroundColor = "#efefef"; // FFF4E6
	}
	
	if (preRow2){ 
		preRow2.style.border = "1px solid #efefef";		
		//iciRow2.style.backgroundColor = "#efefef"; // FFF4E6
	}
	preRow = iciRow;
	//preRow2 = iciRow2;
}


function moveTree(idx)
{
	var objTop = iciRow.parentNode.parentNode;
	
	var nextPos = iciRow.rowIndex+idx;
	if (nextPos==objTop.rows.length) nextPos = 0;
	objTop.moveRow(iciRow.rowIndex,nextPos);
	//alert(1);
	//selectedGoodsView("selectd_sort");
	//alert(2);
}

function moveDIV(idx){
	var _iciRow2;
	//alert(idx);
	if(idx < 0){	
		//alert(iciRow2.previousSibling.id);
		if(iciRow2.previousSibling){
			iciRow2.insertAdjacentHTML("afterEnd",iciRow2.previousSibling.outerHTML);
			iciRow2.previousSibling.removeNode(iciRow2.parentNode);
		}else{
			iciRow2.parentNode.lastChild.insertAdjacentHTML("afterEnd",iciRow2.outerHTML);
			_iciRow2 = iciRow2.parentNode.lastChild;
			iciRow2.removeNode(iciRow2.parentNode);
			iciRow2 = _iciRow2;
		}		
	}else{
		if(iciRow2.nextSibling){
			iciRow2.insertAdjacentHTML("beforeBegin",iciRow2.nextSibling.outerHTML);
			iciRow2.nextSibling.removeNode(iciRow2.parentNode);
		}else{
			iciRow2.parentNode.firstChild.insertAdjacentHTML("beforeBegin",iciRow2.outerHTML);
			_iciRow2 = iciRow2.parentNode.firstChild;
			iciRow2.removeNode(iciRow2.parentNode);
			iciRow2 = _iciRow2;
		}
	}
	//$('debug_area').value =document.getElementById('group_product_area_'+select_gorup_code).innerHTML;
	preRow2 = iciRow2;
	
}


function keydnTree()
{
	//alert(iciRow+":::"+ iciRow2);
	
	if (iciRow==null && iciRow2==null) return;
	if (iciRow!=null && iciRow2==null){
		switch (event.keyCode){
			case 38: moveTree(-1); break;
			case 40: moveTree(1);break;
		}
	}else if (iciRow==null && iciRow2!=null){		
		switch (event.keyCode){
			case 37: case 38: moveDIV(-1); break;
			case 39: case 40: moveDIV(1);break;
		}
	}else{
		switch (event.keyCode){
			case 38: moveTree(-1);moveDIV(-1); break;
			case 40: moveTree(1);moveDIV(1);break;
		}
	}
	//return false;
}

document.onkeydown = keydnTree;