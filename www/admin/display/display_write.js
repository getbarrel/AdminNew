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
  obj_table=document.getElementById('group_info_area0').cloneNode(true);
}
function add_table() {
//	alert(idx);
  if (idx<9) {  //제한
      idx++;
      
      obj_table.group_code = idx+1;
      //alert(obj_table.group_code);
      //alert(obj_table.outerHTML);
      obj_table_text = obj_table.outerHTML.replace("group_info_area0","group_info_area"+idx);
	  $('#aaaa').val(obj_table_text);
      obj_table_text = obj_table_text.replace("<!--삭제버튼-->","<a onclick=\"del_table('group_info_area"+idx+"',"+obj_table.group_code+");\"><img src='/admin/images/"+language+"/btn_goods_group_del.gif' border=0 align=absmiddle style='position:relative;top:-2px;'></a>");
      obj_table_text = obj_table_text.replace("GROUP 1","GROUP "+(idx+1));
      //obj_table_text = obj_table_text.replace("group_code=\"1\"","group_code=\""+(idx+1)+"\"");
      
      obj_table_text = obj_table_text.replace("group_name[1]","group_name["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace("group_name_1","group_name_"+(idx+1)+"");
      obj_table_text = obj_table_text.replace("group_img[1]","group_img["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace("product_cnt[1]","product_cnt["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace("ms_productSearch.show_productSearchBox(event,1,'productList_1')","ms_productSearch.show_productSearchBox(event,"+(idx+1)+",'productList_"+(idx+1)+"')");      
      obj_table_text = obj_table_text.replace("group_product_area_1","group_product_area_"+(idx+1)+"");
      obj_table_text = obj_table_text.replace("group_img_area_1","group_img_area_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace("product_cnt_1","product_cnt_"+(idx+1)+"");
      obj_table_text = obj_table_text.replace("productList_1","productList_"+(idx+1)+"");

	  obj_table_text = obj_table_text.replace("group_code=1","group_code="+(idx+1)+"");
	  obj_table_text = obj_table_text.replace("objCategory_1","objCategory_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace("categoryadd('1')","categoryadd('"+(idx+1)+"')");


	  obj_table_text = obj_table_text.replace(/goods_manual_area_1/g,"goods_manual_area_"+(idx+1));
	  obj_table_text = obj_table_text.replace(/goods_auto_area_1/g,"goods_auto_area_"+(idx+1));
      obj_table_text = obj_table_text.replace(/display_auto_type\[1\]/g,"display_auto_type["+(idx+1)+"]");

      obj_table_text = obj_table_text.replace(/use_yn\[1\]/g,"use_yn["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace(/display_type\[1\]/g,"display_type["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace(/display_type_1/g,"display_type_"+(idx+1));
      obj_table_text = obj_table_text.replace(/use_1_y/g,"use_"+(idx+1)+"_y");
      obj_table_text = obj_table_text.replace(/use_1_n/g,"use_"+(idx+1)+"_n");
      
     
      //alert(obj_table_text);
      //alert("document.all.group_info_area"+(idx>1?idx-1:"0"))
      document.getElementById("group_info_area"+(idx>1?idx-1:"0")).insertAdjacentHTML("afterEnd",obj_table_text);
      //document.getElementById("group_product_area_"+(idx+1)).innerHTML = "";
	  //document.getElementById("productList_"+(idx+1)).innerHTML = "";
      //alert(document.getElementById("group_img_area_"+(idx+1)));
      document.getElementById("group_img_area_"+(idx+1)).innerHTML = "";
      document.getElementById("group_name_"+(idx+1)).value = "";

	  $('ul#productList_'+(idx+1)).html('');
      
      $('ul[name=productList]').sortable();
      $('ul[name=productList]').disableSelection();
  }else{
  	alert(language_data['event.write.js']['A'][language]);//'상품그룹은 10개까지만 가능합니다.'
  }
	var tbl = document.getElementById('group_info_area'+(idx>2?idx-1:"0"));
	//var input = tbl.rows(0).cells(1).childNodes[0];
	//input.setAttribute('name','a'+idx);
}
function del_table(obj,gCode) {
	//alert(obj);
	//var tg=obj.target?obj.target:obj.srcElement;
	//var tbl = tg.parentElement.parentElement;//.parentElement.parentElement.parentElement;
	//var tbl = tg.parentNode.parentNode.parentNode.parentNode.parentNode;//.parentElement.parentElement.parentElement;
	//var tbl_code=tbl.getAttribute("group_code");
	//alert(tbl.getAttribute("id"));
	//alert(idx+":::"+obj_table.group_code);
	//alert(gCode);
	
	//var tbl=document.getElementById(obj);
	if((idx+1)==gCode || true){
		$('#'+obj).remove();
		//document.getElementById("group_area_parent").removeChild(tbl);
		idx--;
	}else{
		alert(language_data['event.write.js']['B'][language]);//'상품 그룹을 삭제하실려면 마지막 상품그룹 부터 삭제하셔야 합니다.'
	}
}

var gpCode = null;

function showLayer(obj_id,group_code,evt){
	var tg=evt.target?evt.target:evt.srcElement;
	var tg_top=getOffsetTop(tg);// /admin/js/dd.js 에 getOffsetTop() 있음 kbk

	if(gpCode != group_code)	{
		deleteWhole(false);	
		gpCode = group_code;
		
		select_gorup_code = group_code;
		$('#'+obj_id).css('top', parseInt(tg_top)+30+"px");
		$('#'+obj_id).show();
		selectedGoodsView("selected");
	}	else	{
		if($('#'+obj_id).css('display') == 'none')	{
			select_gorup_code = group_code;
			$('#'+obj_id).css('top', parseInt(tg_top)+30+"px");
			$('#'+obj_id).show();
			selectedGoodsView("selected");
		}else{
			$('#'+obj_id).hide();
			preRow = null;
			deleteWhole(false);		
		}
	}
	
}

function init_date(FromDate,ToDate) {
	var frm = document.event_frm;

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
	var frm = document.event_frm;
	
	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	
	init_date(FromDate,ToDate);
	
}


function Content_Input(){
	document.event_frm.content.value = document.event_frm.event_text.value;		
}



function UpdateOrder(pid,thisorder,changeorder){
	//alert(pid+'::'+changeorder+'::"'+thisorder)
	document.vieworderform.vieworder.value = changeorder;
	document.vieworderform._vieworder.value = thisorder;
	document.vieworderform.pid.value = pid;
	
	document.vieworderform.submit();
}

function CategoryInput(frm,mode)
{
	if(frm.title.value == "") {
		alert(language_data['event.write.js']['C'][language]);//'분류명을 입력해주세요'
		return false;
	}
	//frm.companyimg.style.display="block";
	frm.submit();
}

function cateEdit(frm,er_ix) {
	frm.er_ix.value= er_ix;
	frm.act.value= 'cate_update';
	frm.title.value= $("#title_"+er_ix).text();
	if($("#title_"+er_ix).attr("rel") == "Y") {
		$("#use_yn").attr("checked", true);
	} else {
		$("#use_yn").attr("checked", false);
	}
}
