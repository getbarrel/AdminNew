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
	var _max = $('select#list_max option:selected').val(); 
	var args = getRelationProduct.arguments;

	var _mode = args[0];
	var _nset = args[1];
	var _page = args[2];

	var param_mode_paging	= _mode+'_pageing';
	var param_mode_search	= _mode;

	var param_search_type	= '';
	var param_search_text	= '';
	var param_company_id	= '';
	var param_page			= _page;
	var param_max			= _max;
	var param_cid			= '';
	var param_depth			= '';

	if(args[0] == 'list'){
		param_cid			= args[3];
		param_depth			= args[4];
	}	else if(args[0] == 'search_list')	{
		param_search_type	= args[3];
		param_search_text	= args[4];
		param_company_id	= args[5];
	}	else	{
		if(_mode == 'list'){
			param_cid			= args[3];
			param_depth			= args[4];
		}else{
			param_search_type	= args[3];
			param_search_text	= args[4];
			param_company_id	= args[5];
		}
		//alert(_mode+':::'+_nset+':::'+_max);
	}

	//alert('?mode='+param_mode_paging+'&search_type='+param_search_type+'&search_text='+param_search_text+'&company_id='+param_company_id+'&page='+param_page+'&max='+param_max+'&cid='+param_cid+'&depth='+param_depth);
	//http://b2bdev.mallstory.com/admin/marketting/relationAjax.category.act.php?mode=search_list_pageing&search_type=p.pname&search_text=가방&company_id=&page=1&max=5

		//new Ajax.Request('../marketting/relationAjax.category.act.php',
		$.ajax({
			url:'../marketting/relationAjax.category.act.php',
			type: 'POST',
			dataType: 'text',
			data: ({mode: param_mode_paging,
					search_type: param_search_type,
					search_text: param_search_text,
					company_id: param_company_id,
					page: param_page,
					max: param_max,
					cid: param_cid,
					depth: param_depth
			}),
			
			success: function(data){
				
				total = parseInt(data);
				try{
					if(total > 0){
						if(_mode == 'list'){
							$('#view_paging').html(page_bar(_mode, total, _nset, param_page, param_max, param_cid, param_depth));
							$('select#list_max').change(function()	{
								getRelationProduct();
							});
						}else{
							$('#view_paging').html(search_page_bar(_mode, total, _nset, param_page, param_max, param_search_type, param_search_text, param_company_id));
							$('select#list_max').change(function()	{
								getRelationProduct();
							});
						}
					}else{
						$('#view_paging').html('검색결과가 존재하지 않습니다');
					}
				}catch(e){
					alert(e.message);
				}

			}
		});

		//new Ajax.Request('../marketting/relationAjax.category.act.php',
		$.ajax({
			url:'../marketting/relationAjax.category.act.php',
			method: 'POST',
			dataType: 'xml',
			data: ({mode: param_mode_search,
					search_type: param_search_type,
					search_text: param_search_text,
					company_id: param_company_id,
					page: param_page,
					max: param_max,
					cid: param_cid,
					depth: param_depth
			}),
			error: function(xhr){
				alert(language_data['relationAjaxForEvent.js']['A'][language]+xhr.status());//'XML 문서 해석 실패 - '
			},
			success: function(data)	{
				
				var items = $(data).find('relationProducts').find('products');
				var str = '';
				items.each(function()	{
					brand_name = ($(this).find('brand_name').text())	?	'['+$(this).find('brand_name').text()+']<br />':'';
					str += '<div style="cursor:pointer;" class="draggable" id="'+$(this).find('pid').text()+'">';
					str += '<table bgcolor="#ffffff" width="100%" border="0" id="'+$(this).find('tb_pid').text()+'">';	
					str += '<tr>';
					str += '<td width="30"><input type="checkbox" name="pid[]" id="cpid" value="'+$(this).find('pid').text()+'" /></td>';
					str += '<td width="60" id="cObj_'+$(this).find('pid').text()+'"><img src="'+$(this).find('img_src').text()+'" alt="'+$(this).find('pname').text()+'" onerror="this.src=\'/admin/images/noimages_50.gif\';"><input type="hidden" name="rpid['+select_gorup_code+'][]" value="'+$(this).find('pid').text()+'"><input type="hidden" name="brand_name['+select_gorup_code+'][]" value="'+$(this).find('brand_name').text()+'"><input type="hidden" name="pname['+select_gorup_code+'][]" value="'+$(this).find('pname').text()+'"><input type="hidden" name="sellprice['+select_gorup_code+'][]" value="'+$(this).find('sellprice').text()+'"></td>';
					str += '<td align="left">'+brand_name+$(this).find('pname').text()+'<br />'+$(this).find('sellprice').text()+'원</td>';
					str += '</tr>';
					str += '<tr height="1"><td colspan="5" class="dot-x"></td></tr>';
					str += '</table>';
					str += '</div>';
				});
				$('#reg_product').html(str);
			}
		});
		
		return;
}

function SearchProduct(frm)
{
	getRelationProduct('search_list',_nset, _page,frm.search_type[frm.search_type.selectedIndex].value, frm.search_text.value, frm.company_id[frm.company_id.selectedIndex].value);
}


function selectGoodsList(frm)
{
	$('input:checkbox[name="pid[]"]:checked').each(function(_key, _val)	{
		SelectedGoodsReg($(this).val(),"select", _key, $('#cObj_'+$(this).val()).html(), $('#cObj_'+$(this).val()+'>'+'input[name="pname['+select_gorup_code+'][]"]').val());
	});
}

function selectedGoodsView(goods_type)
{
	var selectedGoods = $('div#group_product_area_'+select_gorup_code+' ul li');
	
	selectedGoods.each(function(_key, _val)	{
		cObj = $(this).html();
		pName = $(this).find('input[name="pname['+select_gorup_code+'][]"]').val();
		SelectedGoodsReg($(this).find('input[name="pid['+select_gorup_code+'][]"]').val(), goods_type, _key, cObj, pName);
	});
}

function SelectedGoodsReg(spid, goods_type, div_index, cObjHTML, pName)
{ 
	if(goods_type == 'select')	{
		var str	= '<li id="li_productList'+spid+'">';
		str	+= cObjHTML;
		str += '</li>';
		$('ul#productList_'+select_gorup_code).append(str);
		$('ul#productList_'+select_gorup_code+' li').dblclick(function()	{
			$(this).remove();
		});
	}

	var str	= '<li id="li_productList'+spid+'">';
	str	+= '<div id="div_html" style="float:left;margin:5px 3px;width:50px;height:50px;text-align:center;border:1px solid #efefef;">'+cObjHTML+'</div>';
	str	+= '<div style="float:left;margin:5px 3px;width:190px;height:50px;text-align:left;">'+pName+'</div>';
	str	+= '</li>';

	$('ul#_productList').append(str);
}

function trim(value) {
	return value.replace(/^\s+|\s+$/g,"");
}

function deleteWhole(clipart_delete){
	if(clipart_delete)	{
		$('ul#productList_'+select_gorup_code+' li').remove();
		alert($('input[name="rpid[1][]"]').size());
		
	}
	$('ul#_productList li').remove();
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
	iciRow = $(obj);
	iciRow2 = $("#_group_product_code_"+select_gorup_code+"_"+obj.tr_pid+"");
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

function moveDIV(idx)
{
	alert(iciRow2.previousSibling.id);
	if(idx < 0)	{

	}
}
/*
function moveDIV(idx)	{
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
*/


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