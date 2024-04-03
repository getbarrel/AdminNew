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

function searchZip(search_string, search_mode){
	var xmlHttp = new XMLHttp();
		
	if(search_mode){
		if(search_string == ""){
			alert('검색어 입력후 검색해 주세요');	
			return false;
		}
		
		if(search_string.length < 2){
			alert('2자 이상 검색어 입력후 검색해 주세요');	
			return false;
		}
	}
	
	if(document.getElementById("searchZipArea")){  		
		document.getElementById("searchZipArea").innerHTML = "<img src=\"/admin/images/indicator.gif\">";
	}
		
	//alert(search_string);
	var sURL = './zip.xml.php?search_string='+search_string;

	ret = xmlHttp.request('get', sURL, false, null);
	
	var xmlDoc = new ActiveXObject('Msxml2.DOMDocument');
	xmlDoc.async = false;
	xmlDoc.loadXML(xmlHttp.responseText);
	
	var err = xmlDoc.parseError;

	if (err.errorCode != 0)
		throw new Error('XML 문서 해석 실패 - ' + err.reason);	
	
	var xsl = new ActiveXObject('Microsoft.XMLDOM');
	xsl.async = false;
	xsl.load('./zip_list.xsl');
	
	
	//document.getElementById('member_count').innerHTML = xmlDoc.getElementsByTagName('total')[0].firstChild.data
	//document.write(xmlDoc.transformNode(xsl));
	document.getElementById('searchZipArea').innerHTML = xmlDoc.transformNode(xsl);
	
	var err = xmlDoc.parseError;
	if (err.errorCode != 0)
		throw new Error('XSL 문서 해석 실패 - ' + err.reason);
}


function changeTekbaeCost(send_tekbae_cost){
	var obj = document.all.tekbae_cost;
	
	for(i=0;i < obj.length;i++){
		obj[i].value = send_tekbae_cost;
	}
}


function changeQuickCost(send_quick_cost){
	var obj = document.all.quick_cost;
	
	for(i=0;i < obj.length;i++){
		obj[i].value = send_quick_cost;
	}
}

function changeTruckCost(send_truck_cost){
	var obj = document.all.truck_cost;
	
	for(i=0;i < obj.length;i++){
		obj[i].value = send_truck_cost;
	}
}



function clearAll(frm){
		for(i=0;i < frm.edit_cost.length;i++){
				frm.edit_cost[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.edit_cost.length;i++){
				frm.edit_cost[i].checked = true;
		}
}

function fixAll(frm){
	if (!frm.all_edit.checked){
		clearAll(frm);
		frm.all_edit.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_edit.checked = true;
	}
}

function showRegion(type){
	if(type == "1"){
		document.getElementById('region_zip').style.display = "none";
		document.getElementById('region_name').style.display = "block";
	}else{
		document.getElementById('region_name').style.display = "none";
		document.getElementById('region_zip').style.display = "block";
	}
}

function insertInputBox(obj){
	//var objs = eval("document.all."+obj);
	var objs=$("table."+obj).find("tr");
	//alert(objs.find("tr").html());
	//var objs = document.getElmentById(obj);
	if(objs.length > 0 ){
		//alert(objs[0]);
		var obj_table = objs[0].cloneNode(true);
		var target_obj = objs[objs.length-1];	
	}else{
		
		var obj_table = objs[0].cloneNode(true);
		var target_obj = objs[0];
	}
	var newRow = objs.clone(true).appendTo($("#region_name_table_add"));  
	//newRow.find("input[id^=architecture_code]").attr("name","architecture["+(total_rows)+"][architecture_code]");
	//alert(objs.length+":::"+target_obj.outerHTML);
	//alert(obj_table.parentElement);
	/*
	var obj_table_text = obj_table.outerHTML;
	obj_table_text = obj_table_text.replace(/style=\"display:none;\"/g," "); // 크롬 kbk
	obj_table_text = obj_table_text.replace(/style=\"display:\snone;\"/g," "); // 파폭 kbk
	obj_table_text = obj_table_text.replace(/style=\"DISPLAY:\snone\"/g," "); // 익스 kbk
	//obj_table_text = obj_table_text.replace("disabled","");
	obj_table_text = obj_table_text.replace(/disabled=\"disabled\"/g," "); // 크롬,파폭 kbk
	obj_table_text = obj_table_text.replace(/disabled/g," "); // 익스 kbk
	*/
	//obj_table_text = obj_table_text.replace("validation=false","validation=true");
	//alert(obj_table_text);
	//target_obj.insertAdjacentHTML("afterEnd",obj_table_text);
	//obj_table_text.insertAfter($("#region_name_table_add"))
}

function del_table(txt,evt) {
	var pObj=document.getElementById(txt);
	var tg=evt.target?evt.target:evt.srcElement;
	var tg_p=tg.parentNode;
	while(tg_p.parentNode) {
		tg_p=tg_p.parentNode;
		if(tg_p.tagName=="TABLE") {
			break;
		}
	}
	//alert(tg_p.tagName);
	pObj.removeChild(tg_p);
}