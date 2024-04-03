var _mode = '';
var _cid = '';
var _depth = '';
var _nset = '1';
var _page = '1';
var	_search_type = '';
var	_search_text = '';
var	_company_id = '';

function aasss()
{
	alert(11);
}

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
				var xmlDoc = null;
				if(window.ActiveXObject)	{	// IE
					xmlDoc = new ActiveXObject('Msxml2.DOMDocument');
					xmlDoc.loadXML(transport.responseText);
					xsl = new ActiveXObject("Microsoft.XMLDOM");
				}	else if(document.implementation && document.implementation.createDocument)	{
					var parser = new DOMParser();
					xmlDoc = parser.parseFromString(transport.responseText,"text/xml");
					xsl = document.implementation.createDocument("","",null);
				}	else	{
					alert(language_data['relationAjax.js']['A'][language]);//'XML 객체 생성 실패'
				}
			
				var len = xmlDoc.getElementsByTagName('pid').length;
				var str = '';
				alert(1);
				for(var i = 0; i < len; i++)	{
					try	{
						brand_name = '['+xmlDoc.getElementsByTagName('brand_name')[i].firstChild.nodeValue+']<br />';
					}	catch(e)	{
						brand_name = '';
					}
					str += '<div style="cursor:pointer;" class="draggable" id="'+xmlDoc.getElementsByTagName('pid')[i].firstChild.nodeValue+'">';
					str += '<table bgcolor="#ffffff" width="100%" border="0" id="'+xmlDoc.getElementsByTagName('tb_pid')[i].firstChild.nodeValue+'">';
					str += '<tr>';
					str += '<td width="30"><input type="checkbox" name="pid[]" id="cpid" value="'+xmlDoc.getElementsByTagName('pid')[i].firstChild.nodeValue+'" /></td>';
					str += '<td width="60"><img border="0" id="IMAGE" width="50" height="50" src="'+xmlDoc.getElementsByTagName('img_src')[i].firstChild.nodeValue+'" title="'+xmlDoc.getElementsByTagName('pname')[i].firstChild.nodeValue+'" /></td>';
					str += '<td align="left">'+brand_name+xmlDoc.getElementsByTagName('pname')[i].firstChild.nodeValue+'<br />'+xmlDoc.getElementsByTagName('sellprice')[i].firstChild.nodeValue+'</td>';
					str += '</tr>';
					str += '<tr height="1"><td colspan="5" class="dot-x"></td></tr>';
					str += '</table>';
					str += '</div>';

					//new Draggable(xmlDoc.getElementsByTagName('pid')[i].firstChild.nodeValue, {revert: true});
				}
				
				//xsl.async = false;
				//xsl.load('relationAjax.xsl');

/*
					var xmlDoc = new ActiveXObject('Msxml2.DOMDocument');
					xmlDoc.async = false;

					xmlDoc.loadXML(transport.responseText);
					
					var err = xmlDoc.parseError;
					
					if (err.errorCode != 0)
						throw new Error('XML 문서 해석 실패 - ' + err.reason);

					var xsl = new ActiveXObject('Microsoft.XMLDOM');
					xsl.async = false;
					xsl.load('relationAjax.xsl');
*/					
					//alert(3);
					//alert(xmlDoc.transformNode(xsl));
					//alert(document.getElementById('reg_product').innerHTML);
					//document.getElementById('reg_product').innerHTML = xmlDoc.transformNode(xsl);
					$('reg_product').update(str);
/*					
					var err = xmlDoc.parseError;
					if (err.errorCode != 0)
						throw new Error('XSL 문서 해석 실패 - ' + err.reason);
*/
					for(var i = 0; i < len; i++)	{
						new Draggable(xmlDoc.getElementsByTagName('pid')[i].firstChild.nodeValue, {revert: true});
					}

/*					
					var rproducts = xmlDoc.getElementsByTagName('products');
				
					for(i=0;i<rproducts.length;i++){			
						alert(rproducts.item(i).selectSingleNode('pid').text);
						new Draggable(rproducts.item(i).selectSingleNode('pid').text, {revert: true});
					}
*/
					//alert(Droppables.add);
					Droppables.add('drop_relation_product', { 
				    accept: 'draggable',
				    hoverclass: 'hover',
				    onDrop: function(element) { 
				    	
				    	
				    	var tb0 = document.getElementById('tb_'+element.id);
				    	var tb = document.getElementById('tb_relation_product');
				    	var idx = tb.rows.length;
						//alert(tb.innerHTML.indexOf(element.id));
				    	if(tb.innerHTML.indexOf(element.id) < 0){
								oTr = tb.insertRow(idx);
								
								oTr.onclick=function() { 
									spoit(this)
								}
								oTr.ondblclick = function() { 				    		
									var _idx = this.rowIndex;
									//alert(_idx);
									tb.deleteRow(_idx);					    	
									//tb.deleteRow(_idx);					    
								} ;
								//alert(oTr.style);
								//oTr.style="background: url(../images/dot.gif) repeat-x left bottom;";
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
							//	oTd = oTr.insertCell(-1);
							//	oTd.innerHTML = "닫기";
							/*	
								oTr = tb.insertRow(idx+1);
								oTd = oTr.insertCell(-1);
								oTd.colSpan = 3;
								oTd.background = "../image/dot.gif";
							*/
							}else{
								alert(language_data['relationAjax.js']['B'][language]);//'이미등록된 상품입니다.'
							}
						
				    }
				  });
  
			}
		});
}

function SearchProduct(frm){

	getRelationProduct('search_list',_nset, _page,frm.search_type[frm.search_type.selectedIndex].value, frm.search_text.value, frm.company_id[frm.company_id.selectedIndex].value);
	
}


function selectGoodsList(frm){
		//alert(frm.cpid.length);
       	for(i=0;i < frm.cpid.length;i++){
				if(frm.cpid[i].checked && frm.cpid[i].value != ''){
					//alert(frm.cpid[i].checked+':::'+frm.cpid[i].value);
					SelectedGoodsReg(frm.cpid[i].value);

				}
		}
}

function SelectedGoodsReg(spid) { 
	//alert(element.id);
	//alert(element.outerHTML);
	//$('drop_relation_product').innerHTML = $('drop_relation_product').innerHTML+element.innerHTML; 
	//alert(element.getElementsByTagName('table').innerHTML);
	var tb0 = document.getElementById('tb_'+spid);
	var tb = document.getElementById('tb_relation_product');
	var idx = tb.rows.length;
	//alert(idx);
	if(tb.innerHTML.indexOf(spid) < 0){
		oTr = tb.insertRow(idx);
		
		oTr.onclick=function() { 
			spoit(this)
		}
		oTr.ondblclick = function() { 				    		
			var _idx = this.rowIndex;
			//alert(_idx);
			tb.deleteRow(_idx);					    	
			//tb.deleteRow(_idx);					    
		} ;
		//alert(oTr.style);
		//oTr.style="background: url(../images/dot.gif) repeat-x left bottom;";
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
		oTd.innerHTML = "<input type='hidden' name='rpid[]' value='"+spid+"'>";
	//	oTd = oTr.insertCell(-1);
	//	oTd.innerHTML = "닫기";
	
	/*	
		oTr = tb.insertRow(idx+1);
		oTd = oTr.insertCell(-1);
		oTd.colSpan = 3;
		oTd.background = "../image/dot.gif";
	*/
	//}else{
		//alert('이미등록된 상품입니다.');
	}	
}

function deleteWhole(){
	var tb = document.getElementById('tb_relation_product');
	var idx = tb.rows.length;
	//alert(tb.rows.length);
	for(i=1;i <= idx;i++){
			//alert(idx-i);
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
	var tab = new Array('tab_cSearch01','tab_cSearch02');
	
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

function spoit(obj)
{
	iciRow = obj;
	iciHighlight();
}
function iciHighlight(){
	
	if (preRow) preRow.style.backgroundColor = "";
	iciRow.style.backgroundColor = "#efefef"; // FFF4E6
	preRow = iciRow;
}

function iciHighlight_backup(){	
	if (preRow) preRow.style.borderTop = "";	
	iciRow.style.borderTop  = "1px solid #efefef"; // FFF4E6
	preRow = iciRow;
}

function moveTree(idx)
{
	var objTop = iciRow.parentNode.parentNode;
	var nextPos = iciRow.rowIndex+idx;
	if (nextPos==objTop.rows.length) nextPos = 0;
	objTop.moveRow(iciRow.rowIndex,nextPos);
}

function keydnTree()
{
	if (iciRow==null) return;
	switch (event.keyCode){
		case 38: moveTree(-1); break;
		case 40: moveTree(1); break;
	}
	return false;
}

document.onkeydown = keydnTree;