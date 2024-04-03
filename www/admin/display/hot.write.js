/////////       insertAdjacentHTML 를 Firefox 에서 사용하기 위한 스크립트 시작        /////////////
if(typeof HTMLElement!="undefined" && !HTMLElement.prototype.insertAdjacentElement){
    HTMLElement.prototype.insertAdjacentElement = function (where,parsedNode)
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

    HTMLElement.prototype.insertAdjacentHTML = function (where,htmlStr)
    {
        var r = this.ownerDocument.createRange();
        r.setStartBefore(this);
        var parsedHTML = r.createContextualFragment(htmlStr);
        this.insertAdjacentElement(where,parsedHTML)
    }


    HTMLElement.prototype.insertAdjacentText = function (where,txtStr)
    {
        var parsedText = document.createTextNode(txtStr)
        this.insertAdjacentElement(where,parsedText)
    }
}
/////////       insertAdjacentHTML 를 Firefox 에서 사용하기 위한 스크립트 끝        /////////////

var idx;
var obj_table;
var select_gorup_code = 1;
var group_idx=0;
function my_init(group_total) {
  idx=group_total;
  obj_table=document.all.group_info_area0.cloneNode(true);
}
function add_table() {
  if (idx<9) {  //제한
      idx++;
      
      obj_table.group_code = idx+1;
      //alert(idx);
      //alert(obj_table.outerHTML);
      obj_table_text = obj_table.outerHTML.replace("group_info_area0","group_info_area"+idx);
      //obj_table_text = obj_table_text.replace("<!--삭제버튼-->","<a onclick=\"del_table(this);\"><img src='/admin/images/btn_goods_group_del.gif' border=0 align=absmiddle></a>");
	  obj_table_text = obj_table_text.replace("<!--삭제버튼-->","<a onclick=\"del_table('group_info_area"+idx+"',"+obj_table.group_code+");\"><img src='/admin/images/btn_goods_group_del.gif' border=0 align=absmiddle style='position:relative;top:-2px;'></a>");
      obj_table_text = obj_table_text.replace("GROUP 1","GROUP "+(idx+1));
      //obj_table_text = obj_table_text.replace("group_code=\"1\"","group_code=\""+(idx+1)+"\"");
      
      obj_table_text = obj_table_text.replace("group_name[1]","group_name["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace("group_name_1","group_name_"+(idx+1)+"");
      obj_table_text = obj_table_text.replace("group_img[1]","group_img["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace("showLayer('relation_product_area',1)","showLayer('relation_product_area',"+(idx+1)+")");      
      obj_table_text = obj_table_text.replace("group_product_area_1","group_product_area_"+(idx+1)+"");
      obj_table_text = obj_table_text.replace("group_img_area_1","group_img_area_"+(idx+1)+"");
      
      
      obj_table_text = obj_table_text.replace(/use_yn\[1\]/g,"use_yn["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace(/display_type\[1\]/g,"display_type["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace(/display_type_1/g,"display_type_"+(idx+1));
      obj_table_text = obj_table_text.replace(/use_1_y/g,"use_"+(idx+1)+"_y");
      obj_table_text = obj_table_text.replace(/use_1_n/g,"use_"+(idx+1)+"_n");
      
      

      
      
      //alert(obj_table_text);
      //alert("document.all.group_info_area"+(idx>1?idx-1:"0"))
      eval("document.all.group_info_area"+(idx>1?idx-1:"0")).insertAdjacentHTML("afterEnd",obj_table_text);
      eval("document.all.group_product_area_"+(idx+1)).innerHTML = "";
      //alert(document.getElementById("group_img_area_"+(idx+1)));
      document.getElementById("group_img_area_"+(idx+1)).innerHTML = "";
      document.getElementById("group_name_"+(idx+1)).value = "";
      
  }else{
  	alert(language_data['hot.write.js']['A'][language]);//'상품그룹은 10개까지만 가능합니다.'
  }
	var tbl = document.getElementById('group_info_area'+(idx>2?idx-1:"0"));
	//var input = tbl.rows(0).cells(1).childNodes[0];
	//input.setAttribute('name','a'+idx);
}
function del_table(obj,gCode) {
	/*
	var tbl = obj.parentElement.parentElement;//.parentElement.parentElement.parentElement;
	//alert(idx+":::"+tbl.group_code);
	if((idx+1)==tbl.group_code){
		
			tbl.removeNode(tbl);
			idx--;
		
	}else{
		alert('상품 그룹을 삭제하실려면 마지막 상품그룹 부터 삭제하셔야 합니다.');
	}*/
	var tbl=document.getElementById(obj);
	if((idx+1)==gCode){
		
			document.getElementById("group_area_parent").removeChild(tbl);
			idx--;
		
	}else{
		alert(language_data['hot.write.js']['B'][language]);//'상품 그룹을 삭제하실려면 마지막 상품그룹 부터 삭제하셔야 합니다.'
	}
}

function showLayer(obj_id,group_code,evt){
	/*if($(obj_id).style.display == "none"){		
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
	*/
	var tg=evt.target?evt.target:evt.srcElement;
	var tg_top=getOffsetTop(tg);// /admin/js/dd.js 에 getOffsetTop() 있음 kbk
	if($(obj_id).css("display") == null){
		//alert($(obj_id).gorup_code);
		select_gorup_code = group_code;
		//$(obj_id).style.top = event.y+document.body.scrollTop+10;
		//alert(event.clientY);
		//alert(document.documentElement.scrollTop+10+"px");
		$(obj_id).css("top",parseInt(tg_top)+30+"px");
		$(obj_id).css("display","block");
		selectedGoodsView("selected");
	}else{
		$(obj_id).css("display","none");
		preRow = null;
		deleteWhole(false);		
	}
}

function init_date(FromDate,ToDate) {
	var frm = document.hot_frm;

/*	alert(FromDate);
	alert(FromDate.substring(0,4));
	alert(FromDate.substring(5,7));
	alert(FromDate.substring(8,10));
*/
	for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate.substring(8,10))
			frm.FromDD.options[i].selected=true
	}
	
	
	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate.substring(8,10))
			frm.ToDD.options[i].selected=true
	}
	
	
	
}

function onLoadDate(FromDate, ToDate) {
	var frm = document.hot_frm;
	
	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	
	init_date(FromDate,ToDate);
	
}


function Content_Input(){
	document.hot_frm.content.value = document.hot_frm.event_text.value;		
}



function UpdateOrder(pid,thisorder,changeorder){
	//alert(pid+'::'+changeorder+'::"'+thisorder)
	document.vieworderform.vieworder.value = changeorder;
	document.vieworderform._vieworder.value = thisorder;
	document.vieworderform.pid.value = pid;
	
	document.vieworderform.submit();
}



