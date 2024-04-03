function deleteProduct(act,id){
	if(confirm(language_data['common']['g'][language])){//'정말로 삭제하시겠습니까?'
		//document.frames['act'].location.href='./product_input.act.php?act='+act+'&id='+id;
		document.getElementById('act').src='./product_input.act.php?act='+act+'&id='+id;
	}
}

function setCategory(cname,cid,depth,pid){
	//document.frames['act'].location.href='?cid='+cid+'&depth='+depth+'&view=innerview';
	document.getElementById('act').src='?cid='+cid+'&depth='+depth+'&view=innerview';
}

function UpdateOrder(pid,thisorder,changeorder){
	//alert(pid+'::'+changeorder+'::"'+thisorder)
	document.vieworderform.vieworder.value = changeorder;
	document.vieworderform._vieworder.value = thisorder;
	document.vieworderform.pid.value = pid;
	
	document.vieworderform.submit();
}



var iciRow, preRow;
var shift_press = false;
var ctrl_press = false;
var shift_start_rowIndex = "";
var shift_end_rowIndex = "";
var preRows = new Array();
var preRowIds = new Array();
var preRowIdxs = new Array();
preRows.push("");
preRowIds.push("");

function spoit(obj)
{
	iciRow = obj;
	iciHighlight();
}

function iciHighlight()
{
	var deleteRow;		
	if(!shift_press){
		shift_start_rowIndex = iciRow.rowIndex;
	}
	
	if(ctrl_press){
		iciRow.style.backgroundColor = "#f9ded1"; // FFF4E6  f9f2ee
		//alert(preRows.length);
		for(i=1;i < preRows.length;i++){
			//alert(preRows.length+"::"+preRows[i]);
			//alert(preRows[i].id+"=="+ iciRow.id + ":::"+ (preRows[i] == iciRow));
				if(preRows[i] == iciRow){
					//alert(i+"==="+preRows[i].id +"=="+ iciRow.id + "::"+  iciRow.style.backgroundColor);
					deleteRow = i;
					iciRow.style.backgroundColor = "";
					//alert(i+"==="+preRows[i] +"=="+ iciRow + "::"+  iciRow.style.backgroundColor);		
				}
			
		}
		//alert(deleteRow);
		if(deleteRow){
			//alert(deleteRow);
			preRows.splice(deleteRow,1);
			preRowIds.splice(deleteRow,1);
			preRowIdxs.splice(deleteRow,1);
		}else{
			if (!inArray(iciRow, preRows)){
				preRows.push(iciRow);
				preRowIds.push(iciRow.id);
				preRowIdxs.push(iciRow.rowIndex);
			}else{
				preRows.splice(0,1);
				preRowIds.splice(0,1);
				preRowIdxs.splice(0,1);
				iciRow.style.backgroundColor = "";
			}
		}
	}else{
		var objTop = iciRow.parentNode.parentNode;		
		for(j=0;j < objTop.rows.length;j++){
			objTop.rows[j].style.backgroundColor = "";
		}
		if (preRow && !shift_press) preRow.style.backgroundColor = "";
		preRow = iciRow;
		iciRow.style.backgroundColor = "#f9ded1";
		preRows = [iciRow];
		preRowIds = [iciRow.id];
		preRowIdxs =[iciRow.rowIndex];
	}
	
	
	
	//iciRow.rowIndex
	if(shift_press){
		
			if(shift_end_rowIndex == ""){ 
				shift_end_rowIndex = iciRow.rowIndex;
			}else{
				shift_end_rowIndex = "";
			}
	
		//alert(shift_start_rowIndex != "");
		if(shift_start_rowIndex >= 0 && shift_end_rowIndex != ""){
			if(shift_start_rowIndex > shift_end_rowIndex){
				var _shift_start_rowIndex = shift_start_rowIndex;
				shift_start_rowIndex = shift_end_rowIndex;
				shift_end_rowIndex = _shift_start_rowIndex;
			}
			//alert(shift_start_rowIndex+":::"+shift_end_rowIndex);
			var objTop = iciRow.parentNode.parentNode;		
			for(j=shift_start_rowIndex;(j < objTop.rows.length && j <= shift_end_rowIndex);j++){
				//alert(j);
				objTop.rows[j].style.backgroundColor = "#f9ded1";
				//alert(inArray(objTop.rows[j].id, preRowIds)+"::"+objTop.rows[j].id+":::"+preRowIds.join(","));
				if (!inArray(objTop.rows[j], preRows)){
					preRows.push(objTop.rows[j]);
					preRowIds.push(objTop.rows[j].id);
					preRowIdxs.push(objTop.rows[j].rowIndex);
				}
				//if(shift_end_rowIndex
		//		alert(objTop.rows[j].id);
			}
		}
		
	//	alert(iciRow.rowIndex);
	//	objTop.rows.length
		
	}else{
		//shift_start_rowIndex = "";
		shift_end_rowIndex = "";
	}
	
	
	//document.getElementById("array_info").innerHTML = preRowIds.join(",");
	//document.getElementById("array_info2").innerHTML = shift_press+":::"+shift_start_rowIndex+":::"+shift_end_rowIndex;//preRowIds.join(",");
	//alert(shift_press);
	//alert(preRows)
}

function moveTree(idx)
{
	var objTop = iciRow.parentNode.parentNode;
	var nextPos = iciRow.rowIndex+idx;
	if (nextPos==objTop.rows.length) nextPos = 0;
	objTop.moveRow(iciRow.rowIndex,nextPos);
}

function moveTreeGroup(idx)
{
	if(idx > 0){
		preRows.sort(descComparator);
	}else{
		preRows.sort(ascComparator);
	}
//	preRowIds.sort(ascComparator);
//	alert(preRowIds.join(","));

	for(i=0;i <preRows.length;i++){
		//alert(preRows[i].id+":::"+preRows[i].rowIndex);
		if(!preRows[i]) {
			alert(language_data['product_order3.js']['A'][language]);//"이동할 상품을 선택해주세요."
			return;
		}
		var objTop = preRows[i].parentNode.parentNode;
		var nextPos = preRows[i].rowIndex+idx;
		if (nextPos==objTop.rows.length) nextPos = 0;
		//alert(preRows[i].rowIndex+":::"+nextPos);
		if(nextPos >= -1){
			//alert(nextPos);
			
			if(objTop.rows.length < nextPos){
				//objTop.moveRow(preRows[i].rowIndex,0);// 원본
				
				// 호환성을 위한 수정 2011-04-12 kbk
				var ori_obj=preRows[i].cloneNode(true);
				document.getElementById('list_table').deleteRow(preRows[i].rowIndex);
				var td_obj=document.getElementById('list_table').insertRow(0);
				var child_len=ori_obj.childNodes.length;
				var tr_id=ori_obj.getAttribute('id');
				td_obj.setAttribute("height","40");
				td_obj.setAttribute("id",tr_id);
				if(window.addEventListener) td_obj.setAttribute("class","dot_xx");
				else td_obj.className="dot_xx";
				td_obj.setAttribute("align","center");
				td_obj.setAttribute("onClick","spoit(this)");
				for(var j=0;j<child_len;j++) {
					if(ori_obj.childNodes[j].tagName=='TD') {
						var td_cell=ori_obj.childNodes[j].cloneNode(true);
						td_obj.appendChild(td_cell);
						//td_cell.innerHTML=preRows[i].childNodes[j].innerHTML;
					}
				}
				td_obj.style.cursor="pointer";
				spoit(td_obj);
				// 호환성을 위한 수정 2011-04-12 kbk
			}else{
				//objTop.moveRow(preRows[i].rowIndex,nextPos); // 원본

				// 호환성을 위한 수정 2011-04-12 kbk
				var ori_obj=preRows[i].cloneNode(true);
				document.getElementById('list_table').deleteRow(preRows[i].rowIndex);
				var td_obj=document.getElementById('list_table').insertRow(nextPos);
				var child_len=ori_obj.childNodes.length;
				var tr_id=ori_obj.getAttribute('id');
				td_obj.setAttribute("height","40");
				td_obj.setAttribute("id",tr_id);
				if(window.addEventListener) td_obj.setAttribute("class","dot_xx");
				else td_obj.className="dot_xx";
				td_obj.setAttribute("align","center");
				td_obj.setAttribute("onClick","spoit(this)");
				for(var j=0;j<child_len;j++) {
					if(ori_obj.childNodes[j].tagName=='TD') {
						var td_cell=ori_obj.childNodes[j].cloneNode(true);
						td_obj.appendChild(td_cell);
						//td_cell.innerHTML=preRows[i].childNodes[j].innerHTML;
					}
				}
				td_obj.style.cursor="pointer";
				spoit(td_obj);
				// 호환성을 위한 수정 2011-04-12 kbk

				//var textNode=preRows[i];
				//alert(preRows[i].childNodes.length);
				//document.getElementById('list_table').appendChild=textNode;
			}
		}else{
		//	alert(nextPos);
			//objTop.moveRow(preRows[i].rowIndex,-1);//원본

			// 호환성을 위한 수정 2011-04-12 kbk
			var ori_obj=preRows[i].cloneNode(true);
			document.getElementById('list_table').deleteRow(preRows[i].rowIndex);
			var td_obj=document.getElementById('list_table').insertRow(-1);
			var child_len=ori_obj.childNodes.length;
			var tr_id=ori_obj.getAttribute('id');
			td_obj.setAttribute("height","40");
			td_obj.setAttribute("id",tr_id);
			if(window.addEventListener) td_obj.setAttribute("class","dot_xx");
			else td_obj.className="dot_xx";
			td_obj.setAttribute("align","center");
			td_obj.setAttribute("onClick","spoit(this)");
			for(var j=0;j<child_len;j++) {
				if(ori_obj.childNodes[j].tagName=='TD') {
					var td_cell=ori_obj.childNodes[j].cloneNode(true);
					td_obj.appendChild(td_cell);
					//td_cell.innerHTML=preRows[i].childNodes[j].innerHTML;
				}
			}
			td_obj.style.cursor="pointer";
			spoit(td_obj);
			// 호환성을 위한 수정 2011-04-12 kbk
		}
	}
	/*
	var objTop = iciRow.parentNode.parentNode;
	var nextPos = iciRow.rowIndex+idx;
	if (nextPos==objTop.rows.length) nextPos = 0;
	objTop.moveRow(iciRow.rowIndex,nextPos);
	*/
}

function keydnTree(result)
{
	
	if (iciRow==null) return;
	switch (result){
		case 38: moveTreeGroup(-1); break;
		case 40: moveTreeGroup(1); break;
		case 16: shift_press = true; break;
		case 17: ctrl_press = true; break;
	}
	return false;
}

function keyupTree(result){	
	if (iciRow==null) return;
	switch (result){		
		case 16: shift_press = false; break;
		case 17: ctrl_press = false; break;
	}
	return false;
}

// 역행 정렬
  function descComparator(a, b) {
      return b.rowIndex - a.rowIndex;
  }

  // 순행 정렬
  function ascComparator(a, b) {
      return a.rowIndex - b.rowIndex;
  }

document.onkeydown  = function(e){ 
    var result = ""; 
    
    if(typeof(e) != "undefined") 
        result = e.which; 
    else 
        result = event.keyCode; 

    return keydnTree(result) 
}
document.onkeyup  = function(e){ 
    var result = ""; 
    
    if(typeof(e) != "undefined") 
        result = e.which; 
    else 
        result = event.keyCode; 

    return keyupTree(result) 
}
//document.onkeydown = keydnTree;
//document.onkeyup = keyupTree;