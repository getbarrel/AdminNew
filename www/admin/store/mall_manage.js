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
			//objTop.rows[j].style.backgroundColor = "";
			//if(j == 0){
			$(objTop.rows[j]).find("td").each(function(){
				$(this).css('background-color','');	
			});
			//}
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
	/*if(idx > 0){
		preRows.sort(descComparator);
		
	}else{
		preRows.sort(ascComparator);
	}*/
	//preRowIds.sort(ascComparator);
	//alert(preRowIds.join(","));
	for(i=0;i <preRows.length;i++){
		//alert(preRows[i].id+":::"+preRows[i].rowIndex);
		var objTop = preRows[i].parentNode.parentNode;
		var nextPos = preRows[i].rowIndex+idx;
		if (nextPos==objTop.rows.length) nextPos = 0;
		//alert(preRows[i].rowIndex+":::"+nextPos);
		if(nextPos >= -1){
			//alert(nextPos);
			if(objTop.rows.length < nextPos){
				//objTop.moveRow(preRows[i].rowIndex,0);

				// 호환성을 위한 수정 2011-04-12 kbk
				var ori_obj=preRows[i].cloneNode(true);
				document.getElementById('list_table').deleteRow(preRows[i].rowIndex);
				var td_obj=document.getElementById('list_table').insertRow(0);
				var child_len=ori_obj.childNodes.length;
				var tr_id=ori_obj.getAttribute('idx');
				td_obj.setAttribute("height","30");
				td_obj.setAttribute("idx",tr_id);
				if(window.addEventListener) td_obj.setAttribute("class","dot_xx");
				else td_obj.className="dot_xx";
				//td_obj.setAttribute("align","center");
				td_obj.setAttribute("onClick","spoit(this)");
				for(var j=0;j<child_len;j++) {
					if(ori_obj.childNodes[j].tagName=='TD') {
						var td_cell=ori_obj.childNodes[j].cloneNode(true);
						td_obj.appendChild(td_cell);
						//td_cell.innerHTML=preRows[i].childNodes[j].innerHTML;
					}
				}
				td_obj.style.cursor="pointer";
				$(td_obj).append('<input type="text" name="idx[]" value="'+tr_id+'" />');
				$(td_obj).append('<input type="text" name="sno[]" value="'+tr_id+'" />');
				$(td_obj).append('<input type="text" name="sort[]" value="" />');
				/*var input_obj=document.createElement("input");
				input_obj.setAttribute("type","hidden");
				input_obj.setAttribute("name","idx[]");
				input_obj.setAttribute("value",tr_id);
				td_obj.appendChild(input_obj);
				var input_obj2=document.createElement("input");
				input_obj2.setAttribute("type","hidden");
				input_obj2.setAttribute("name","sno[]");
				input_obj2.setAttribute("value",tr_id);
				td_obj.appendChild(input_obj2);
				var input_obj3=document.createElement("input");
				input_obj3.setAttribute("type","hidden");
				input_obj3.setAttribute("name","sort[]");
				input_obj3.setAttribute("value","");
				td_obj.appendChild(input_obj3);*/
				spoit(td_obj);

				order_table();//sort를 순차적으로 대입함
				// 호환성을 위한 수정 2011-04-12 kbk
			}else{
				//objTop.moveRow(preRows[i].rowIndex,nextPos);

				// 호환성을 위한 수정 2011-04-12 kbk
				var ori_obj=preRows[i].cloneNode(true);
				document.getElementById('list_table').deleteRow(preRows[i].rowIndex);
				var td_obj=document.getElementById('list_table').insertRow(nextPos);
				var child_len=ori_obj.childNodes.length;
				var tr_id=ori_obj.getAttribute('idx');
				td_obj.setAttribute("height","30");
				td_obj.setAttribute("idx",tr_id);
				if(window.addEventListener) td_obj.setAttribute("class","dot_xx");
				else td_obj.className="dot_xx";
				//td_obj.setAttribute("align","center");
				td_obj.setAttribute("onClick","spoit(this)");
				for(var j=0;j<child_len;j++) {
					if(ori_obj.childNodes[j].tagName=='TD') {
						var td_cell=ori_obj.childNodes[j].cloneNode(true);
						td_obj.appendChild(td_cell);
						//td_cell.innerHTML=preRows[i].childNodes[j].innerHTML;

					}
				}
				td_obj.style.cursor="pointer";
				//$(td_obj).append('<input type="hidden" name="idx[]" value="'+tr_id+'" />');
				//$(td_obj).append('<input type="text" name="sno[]" value="'+tr_id+'" />');
				//$(td_obj).append('<input type="text" name="sort[]" value="" />');

				/*var input_obj=document.createElement("input");
				input_obj.setAttribute("type","hidden");
				input_obj.setAttribute("name","idx[]");
				input_obj.setAttribute("value",tr_id);
				td_obj.appendChild(input_obj);
				var input_obj2=document.createElement("input");
				input_obj2.setAttribute("type","hidden");
				input_obj2.setAttribute("name","sno[]");
				input_obj2.setAttribute("value",tr_id);
				td_obj.appendChild(input_obj2);
				var input_obj3=document.createElement("input");
				input_obj3.setAttribute("type","hidden");
				input_obj3.setAttribute("name","sort[]");
				input_obj3.setAttribute("value","");
				td_obj.appendChild(input_obj3);*/
				spoit(td_obj);
				
				order_table();//sort를 순차적으로 대입함
				// 호환성을 위한 수정 2011-04-12 kbk

			}
		}else{
			//alert(nextPos);
			//objTop.moveRow(preRows[i].rowIndex,-1);

			// 호환성을 위한 수정 2011-04-12 kbk
			var ori_obj=preRows[i].cloneNode(true);
			document.getElementById('list_table').deleteRow(preRows[i].rowIndex);
			var td_obj=document.getElementById('list_table').insertRow(-1);
			var child_len=ori_obj.childNodes.length;
			var tr_id=ori_obj.getAttribute('idx');
			td_obj.setAttribute("height","30");
			td_obj.setAttribute("idx",tr_id);
			if(window.addEventListener) td_obj.setAttribute("class","dot_xx");
			else td_obj.className="dot_xx";
			//td_obj.setAttribute("align","center");
			td_obj.setAttribute("onClick","spoit(this)");
			for(var j=0;j<child_len;j++) {
				if(ori_obj.childNodes[j].tagName=='TD') {
					var td_cell=ori_obj.childNodes[j].cloneNode(true);
					td_obj.appendChild(td_cell);
					//td_cell.innerHTML=preRows[i].childNodes[j].innerHTML;
				}
			}
			td_obj.style.cursor="pointer";
			$(td_obj).append('<input type="text" name="idx[]" value="'+tr_id+'" />');
			$(td_obj).append('<input type="text" name="sno[]" value="'+tr_id+'" />');
			$(td_obj).append('<input type="text" name="sort[]" value="" />');
			/*
			var input_obj=document.createElement("input");
			input_obj.setAttribute("type","hidden");
			input_obj.setAttribute("name","idx[]");
			input_obj.setAttribute("value",tr_id);
			td_obj.appendChild(input_obj);
			var input_obj2=document.createElement("input");
			input_obj2.setAttribute("type","hidden");
			input_obj2.setAttribute("name","sno[]");
			input_obj2.setAttribute("value",tr_id);
			td_obj.appendChild(input_obj2);
			var input_obj3=document.createElement("input");
			input_obj3.setAttribute("type","hidden");
			input_obj3.setAttribute("name","sort[]");
			input_obj3.setAttribute("value","");
			td_obj.appendChild(input_obj3);
			*/
			spoit(td_obj);

			order_table();//sort를 순차적으로 대입함
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

function keydnTree()
{
	//alert(event.keyCode);
	if (iciRow==null) return;
	switch (event.keyCode){
		case 38: moveTreeGroup(-1); break;
		case 40: moveTreeGroup(1); break;
		case 16: shift_press = true; break;
		case 17: ctrl_press = true; break;
	}
	
	return false;
}

function keyupTree(){	
	if (iciRow==null) return;
	switch (event.keyCode){		
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


//document.onkeydown = keydnTree;
//document.onkeyup = keyupTree;

function order_table() {
	//var pObj=document.getElementById("list_table");
	//var sObj=pObj.getElementsByName("sort[]");
	var sort = 1;

	$("input[name='sort[]']").each(function()	{
		$(this).val(sort);
		sort++;
	});

}


/*			>>>>>>>>>>>>>>>>> 개정법안 추가 kbk 13/02/16 >>>>>>>>>>>>>>>>>>>[Start] */

function ch_auth_form(cobj) {
	if(cobj.value=="N") {
		//$("#use_identity").css("display","none");
		$("#use_ipin").css("display","none");
		$("#use_nice").css("display","none");
		$("#use_com_number").css("display","none");
		$("#use_mobie").css("display","none");
		//$("#mall_use_identificationUse").attr("checked",false);
		$("#mall_use_ipin").attr("checked",false);
		$("#mall_use_niceid").attr("checked",false);
		$("#use_com_number").attr("checked",false);
		$("#use_mobie").attr("checked",false);
	} else {
		//$("#use_identity").css("display","");
		$("#use_ipin").css("display","");
		$("#use_nice").css("display","");
		$("#use_com_number").css("display","");
		$("#use_mobie").css("display","");
	}
}









/*			<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<[End] */


