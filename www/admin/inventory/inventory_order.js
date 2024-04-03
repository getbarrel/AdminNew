function deleteProduct(act,id){
	if(confirm('정말로 삭제하시겠습니까?')){
		document.frames['act'].location.href='./product_input.act.php?act='+act+'&id='+id;
	}
}

function setCategory(cname,cid,depth,pid){
	document.frames['act'].location.href='./product_order.php?cid='+cid+'&depth='+depth+'&view=innerview';
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
		//iciRow.style.backgroundColor = "#f9ded1"; // FFF4E6  f9f2ee
		$(iciRow).find("td").css('background-color','#f9ded1');
		//alert(preRows.length);
		for(i=1;i < preRows.length;i++){
			//alert(preRows.length+"::"+preRows[i]);
			//alert(preRows[i].id+"=="+ iciRow.id + ":::"+ (preRows[i] == iciRow));
				if(preRows[i] == iciRow){
					//alert(i+"==="+preRows[i].id +"=="+ iciRow.id + "::"+  iciRow.style.backgroundColor);
					deleteRow = i;
					//iciRow.style.backgroundColor = "";
					$(preRows[i]).find("td").each(function(){
						$(this).css('background-color','');	
					});
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
			//objTop.rows[j].style.backgroundColor = "";
			$(objTop.rows[j]).find("td").each(function(){
				$(this).css('background-color','');	
			});
		}
		if (preRow && !shift_press) preRow.style.backgroundColor = "";
		$(iciRow).find("td").css('background-color','#f9ded1');
		preRow = iciRow;
		//iciRow.style.backgroundColor = "#f9ded1";

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
				//objTop.rows[j].style.backgroundColor = "#f9ded1";
				$(objTop.rows[j]).find("td").each(function(){
					$(this).css('background-color','#f9ded1');	
				});
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
	//alert(iciRow.rowIndex+":::"+nextPos);
	
}

function moveTreeGroup__(idx)
{
	//alert(idx);
	$('#product_order_table tbody').moveRow(2, 3);
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
	//var last_row_idx = preRows.length;
	//
	//alert(last_row_idx);
	for(i=0;i <preRows.length;i++){
		//alert(preRows[i].id+":::"+preRows[i].rowIndex);
		var objTop = preRows[i].parentNode.parentNode;
		
		var nextPos = preRows[i].rowIndex+idx;
		
		if (nextPos==objTop.rows.length) nextPos = 0;
		//
		//alert(objTop.id);
		if(nextPos > -1){
			//alert(preRows[i].outerHTML);
			//$("#from_idx").val(preRows[i].rowIndex);
			//$("#to_idx").val(nextPos);
			//alert(preRows[i].rowIndex+":::"+nextPos);
			if(objTop.rows.length < nextPos){
				//objTop.moveRow(preRows[i].rowIndex,0);
				//$("#mouse_action").val(1);
				$('#'+objTop.id+' tbody').moveRow(preRows[i].rowIndex, 0);
			}else{
				//objTop.moveRow(preRows[i].rowIndex,nextPos);
				//$("#mouse_action").val(2);
				$('#'+objTop.id+' tbody').moveRow(preRows[i].rowIndex, nextPos);
			}
		}else{
			//alert(nextPos);
			//objTop.moveRow(preRows[i].rowIndex,-1);
			var last_row_idx = $('#'+objTop.id+' tbody').find("tr").length-1;
			//$("#from_idx").val(preRows[i].rowIndex);
			//$("#to_idx").val(last_row_idx);
			//$("#mouse_action").val(3);
			$('#'+objTop.id+' tbody').moveRow(preRows[i].rowIndex, last_row_idx);
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

jQuery.fn.moveRow = function(from, to, useBefore) {
    var trs = this.find(">tr");
	//alert(--from+":::"+--to);
	//trs.eq(--from)['insert' + (useBefore && 'Before' || 'After')](trs.eq(--to));
	if(from < to){
		trs.eq(from)['insert' + (useBefore && 'Before' || 'After')](trs.eq(to));
	}else{
		trs.eq(from)['insert' + (useBefore && 'After' || 'Before')](trs.eq(to));
	}
    return this;
};
