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
  obj_table = $('#group_info_area0').clone(true);//document.getElementById('group_info_area0').cloneNode();
}

function add_table(type) {
  idx = $("div[id^=group_info_area]").length;

  idx = parseInt(idx);

  if (idx<20) {  //제한
	  //alert(obj_table.find("div[id^=group_info_area]").length);
      obj_table.attr('group_code',(idx+1));
	  //alert("group_info_area"+(idx));
	  obj_table.attr('id',"group_info_area"+(idx));
	  
      //alert(obj_table.group_code);
      //document.write(obj_table.wrapAll("<div></div>").parent().html());
	  obj_table.find("script[id^=productListScript]").remove();
	  obj_table.find("script[id^=productListScript]").html("");
	  //obj_table_text = obj_table[0].outerHTML ;
      obj_table.find('script').remove();
      obj_table_text = obj_table.wrapAll("<div></div>").parent().html();
	  
	  //alert(idx);
	  obj_table_text.replace("group_info_area0","group_info_area"+(idx+1));
	  //document.write(obj_table_text);
	  //
	  //$('#aaaa').val(obj_table_text);
      obj_table_text = obj_table_text.replace("<!--삭제버튼-->","<a onclick=\"del_table('group_info_area"+idx+"',"+obj_table.group_code+");\"><img src='/admin/images/"+language+"/btn_goods_group_del.gif' border=0 align=absmiddle style='position:relative;top:-2px;'></a>");
      obj_table_text = obj_table_text.replace("GROUP 1","GROUP "+(idx+1));
      //obj_table_text = obj_table_text.replace("group_code=\"1\"","group_code=\""+(idx+1)+"\"");
      
      obj_table_text = obj_table_text.replace("group_name[1]","group_name["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace("group_name_1","group_name_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace("event_name[1]","event_name["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace("event_name_1","event_name_"+(idx+1)+"");
      obj_table_text = obj_table_text.replace("group_img[1]","group_img["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace("group_img_del[1]","group_img_del["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace("group_over_img[1]","group_over_img["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace("group_over_img_del[1]","group_over_img_del["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace("group_link[1]","group_link["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace("group_banner_img[1]","group_banner_img["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace("group_banner_img_del[1]","group_banner_img_del["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace("search_result_1","search_result_"+(idx+1));
	  obj_table_text = obj_table_text.replace("search_result[1]","search_result["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace("selected_result_1","selected_result_"+(idx+1));
	  obj_table_text = obj_table_text.replace("selected_result[1]","selected_result["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace("SearchInfo('B',$('DIV#goods_auto_area_1 DIV#goods_display_sub_area_B input#search_text'), 1);","SearchInfo('B',$('DIV#goods_auto_area_"+(idx+1)+" DIV#goods_display_sub_area_B input#search_text'), "+(idx+1)+");");  
	  obj_table_text = obj_table_text.replace("SearchInfo('S',$('DIV#goods_auto_area_1 DIV#goods_display_sub_area_S input#search_text'), 1);","SearchInfo('S',$('DIV#goods_auto_area_"+(idx+1)+" DIV#goods_display_sub_area_S input#search_text'), "+(idx+1)+");");
	  
	  obj_table_text = obj_table_text.replace("SelectedAll($('DIV#goods_display_sub_area_B #search_result_1 option'),'selected')","SelectedAll($('DIV#goods_display_sub_area_B #search_result_"+(idx+1)+" option'),'selected')");
	  obj_table_text = obj_table_text.replace("SelectedAll($('DIV#goods_display_sub_area_S #search_result_1 option'),'selected')","SelectedAll($('DIV#goods_display_sub_area_S #search_result_"+(idx+1)+" option'),'selected')");

	  obj_table_text = obj_table_text.replace("MoveSelectBox('B','ADD',1);","MoveSelectBox('B','ADD',"+(idx+1)+");");
	  obj_table_text = obj_table_text.replace("MoveSelectBox('S','ADD',1);","MoveSelectBox('S','ADD',"+(idx+1)+");");
	  obj_table_text = obj_table_text.replace("MoveSelectBox('B','REMOVE',1);","MoveSelectBox('B','REMOVE',"+(idx+1)+");");
	  obj_table_text = obj_table_text.replace("MoveSelectBox('S','REMOVE',1);","MoveSelectBox('S','REMOVE',"+(idx+1)+");");


	  obj_table_text = obj_table_text.replace("product_cnt[1]","product_cnt["+(idx+1)+"]");

	  //obj_table_text = obj_table_text.replace(/\"productList_1\"/g,"\"productList_"+(idx+1)+"\"");
	  obj_table_text = obj_table_text.replace(/productList_1/g,"productList_"+(idx+1)+"");

      //obj_table_text = obj_table_text.replace("ms_productSearch.show_productSearchBox(event,1,'productList_1')","ms_productSearch.show_productSearchBox(event,"+(idx+1)+",'productList_"+(idx+1)+"')");
	  obj_table_text = obj_table_text.replace("ms_productSearch.show_productSearchBox(event,1,","ms_productSearch.show_productSearchBox(event,"+(idx+1)+",");
      obj_table_text = obj_table_text.replace("group_product_area_1","group_product_area_"+(idx+1)+"");
      obj_table_text = obj_table_text.replace("group_img_area_1","group_img_area_"+(idx+1)+"");
      obj_table_text = obj_table_text.replace("group_over_img_area_1","group_over_img_area_"+(idx+1)+"");	
      obj_table_text = obj_table_text.replace("group_banner_img_area_1","group_banner_img_area_"+(idx+1)+"");

	  obj_table_text = obj_table_text.replace("product_cnt_1","product_cnt_"+(idx+1)+"");
      

	  obj_table_text = obj_table_text.replace("objCategory_1","objCategory_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace("categoryadd('1')","categoryadd('"+(idx+1)+"')");
	  obj_table_text = obj_table_text.replace(/category_del\(1/g,"category_del\("+(idx+1));

	  obj_table_text = obj_table_text.replace(/goods_manual_area_1/g,"goods_manual_area_"+(idx+1));
	  obj_table_text = obj_table_text.replace(/goods_auto_area_1/g,"goods_auto_area_"+(idx+1));
      obj_table_text = obj_table_text.replace(/display_auto_type\[1\]/g,"display_auto_type["+(idx+1)+"]");

	  obj_table_text = obj_table_text.replace("display_info_type[1]","display_info_type["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace(/brands_manual_area_1/g,"brands_manual_area_"+(idx+1));
	  obj_table_text = obj_table_text.replace(/brandList_1/g,"brandList_"+(idx+1)+"");

      obj_table_text = obj_table_text.replace(/use_yn\[1\]/g,"use_yn["+(idx+1)+"]");
      obj_table_text = obj_table_text.replace(/display_type\[1\]/g,"display_type["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace(/basic_display_yn\[1\]/g,"basic_display_yn["+(idx+1)+"]");

	  
      obj_table_text = obj_table_text.replace(/display_type_1/g,"display_type_"+(idx+1));
      obj_table_text = obj_table_text.replace(/use_1_y/g,"use_"+(idx+1)+"_y");
      obj_table_text = obj_table_text.replace(/use_1_n/g,"use_"+(idx+1)+"_n");

	  obj_table_text = obj_table_text.replace(/basic_display_yn_1_y/g,"basic_display_yn_"+(idx+1)+"_y");
	  obj_table_text = obj_table_text.replace(/basic_display_yn_1_n/g,"basic_display_yn_"+(idx+1)+"_n");

	  obj_table_text = obj_table_text.replace(/group_img_del_1/g,"group_img_del_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace(/group_over_img_del_1/g,"group_over_img_del_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace(/group_banner_img_del_1/g,"group_banner_img_del_"+(idx+1)+"");
	
	  
	  obj_table_text = obj_table_text.replace(/add_type_choice_1/g,"add_type_choice_"+(idx+1)+"");

	  obj_table_text = obj_table_text.replace(/display_type_area_1\', 1/g,"display_type_area_"+(idx+1)+"', "+(idx+1)+"");
	  //obj_table_text = obj_table_text.replace(/display_type_area_1/g,"display_type_area_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace(/display_type_area_1\"/g,"display_type_area_"+(idx+1)+"\"");

	  obj_table_text = obj_table_text.replace(/use_1_m/g,"use_"+(idx+1)+"_m");
	  obj_table_text = obj_table_text.replace(/use_1_a/g,"use_"+(idx+1)+"_a");
		

	  obj_table_text = obj_table_text.replace(/goods_display_sub_type\[1\]/g,"goods_display_sub_type["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace(/goods_display_sub_type_1/g,"goods_display_sub_type_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace(/search_result_1/g,"search_result_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace(/display_auto_priod\[1\]/g,"display_auto_priod["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace(/display_auto_priod_1/g,"display_auto_priod_"+(idx+1)+"");
	  
	  obj_table_text = obj_table_text.replace(/md_mem_ix\[1\]/g,"md_mem_ix["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace(/md_code_1/g,"md_code_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace(/md_mem_name\[1\]/g,"md_mem_name["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace(/md_name_1/g,"md_name_"+(idx+1)+"");
	  obj_table_text = obj_table_text.replace(/group_code=1/g,"group_code="+(idx+1)+"");
	 
	  obj_table_text = obj_table_text.replace(/group_sales_target\[1\]/g,"group_sales_target["+(idx+1)+"]");
	  obj_table_text = obj_table_text.replace(/group_sales_target_1/g,"group_sales_target_"+(idx+1)+"");
	 
	  obj_table_text = obj_table_text.replace(/SearchGoods\(\$\(this\), \'1\'\)/g,"SearchGoods($(this), '"+(idx+1)+"')");
	  obj_table_text = obj_table_text.replace("ChangeDisplaySubType($(this), 1 , this.value)","ChangeDisplaySubType($(this), "+(idx+1)+" , this.value)");

	  obj_table_text = obj_table_text.replace(/group_order\[1\]/g,"group_order["+(idx+1)+"]");
      //alert(obj_table_text);
      //alert("document.all.group_info_area"+(idx>1?idx-1:"0"))
	  //alert("group_info_area"+(idx > 1 ? (idx-1):"0"));
	
		if(type=="cate_main"){
			document.getElementById("group_info_area"+(idx>1?idx-1:"0")).insertAdjacentHTML("afterEnd",obj_table_text);
			document.getElementById("group_img_area_"+(idx+1)).innerHTML = "";
			document.getElementById("group_name_"+(idx+1)).value = "";

			$('ul#productList_'+(idx+1)).html('');
			  
			$('ul[name=productList]').sortable();
			$('ul[name=productList]').disableSelection();
		}else{
			var newObj = $(obj_table_text);
		  
		  newObj.find('.input-order').val((idx+1));

		  $(newObj).find('.add_type_choice li').click(function(){
				promotion_type_check_reset();
				var img_tag = $(this).find('img');
				//alert(img_tag.attr('src')+';;;'+img_tag.attr('src').indexOf('_on'));
				if(img_tag.attr('src').indexOf('_on') == -1){
					$(this).find('img').attr('src',img_tag.attr('src').replace('.png','_on.png'));
				}
			});

		$(newObj).find('.promotion_type_box').click(function(){
			promotion_type_check_reset();
			var img_tag = $(this).find('img');
			img_tag.attr('src',img_tag.attr('src').replace('.png','_on.png'));
			
			$(this).find('input').attr('checked','checked');
		});

		 // document.getElementById("group_info_area"+(idx > 1 ? idx-1:"0")).insertAdjacentHTML("afterEnd",obj_table_text);
		  //document.getElementById("group_product_area_"+(idx+1)).innerHTML = "";
		  //document.getElementById("productList_"+(idx+1)).innerHTML = "";
		  //alert(document.getElementById("group_img_area_"+(idx+1)));
		  newObj.find("#group_img_area_"+(idx+1)).html("");
		 // alert("group_over_img_area_"+(idx+1));
		  newObj.find("#group_over_img_area_"+(idx+1)).html("");
		  newObj.find("#group_banner_img_area_"+(idx+1)).html("");
		  newObj.find("#display_type_area_"+(idx+1)).html("");

		  newObj.find("#group_name_"+(idx+1)).val("");
		  newObj.find("input[id^=product_cnt_]").val("");
		  newObj.find("input[name^=epg_ix]").val("");

		  newObj.find('ul#productList_'+(idx+1)).html('');
		  //document.write(newObj.html());
		  newObj.find("script[id^=productListScript]").remove();
		  newObj.find("script[id^=productListScript]").html("");

		  //$('script#productListScript_'+(idx+1)).remove();
		  //$('script#productListScript_'+(idx+1)).html("");
		  //alert($('script#productListScript_'+(idx+1)));
		  
		  
		  $('ul[name=productList]').sortable();
		  $('ul[name=productList]').disableSelection();
		 //alert((idx > 1 ? (idx-1):"0"));
		//  newObj.appendTo($('#group_info_area'+(idx > 1 ? (idx-1):"0")));
			newObj.appendTo($('#group_area_parent'));
		  //alert(2);
		   sortGroup();

		   newObj.find("#group_name_"+(idx+1)).focus();
		}
	  

	   idx++;
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
	//document.event_frm.content.value = document.event_frm.event_text.value;		
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




function AddGift(tbName){

	var tbody = $('#' + tbName + ' tbody');  
   var total_rows = tbody.find('tr[depth^=1]').length;  
   var rows = tbody.find('tr[depth^=1]').length;  
  
   if($.browser.msie){
      var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
   }else{
	 // var newRow = tbody.find('tr[depth^=1]:last').clone(true).insertAfter(tbody);  
	  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
   }

	newRow.find("div[id^=event_rank]").html(total_rows+1);  


	newRow.find("input[id^=ranking]").attr("name","event_gift["+(total_rows)+"][ranking]");
	newRow.find("input[id^=ranking]").val(total_rows+1);

	newRow.find("input[id^=gift_name]").attr("name","event_gift["+(total_rows)+"][gift_name]");
	newRow.find("input[id^=gift_name]").val('');

	newRow.find("select[id^=gift_code]").attr("name","event_gift["+(total_rows)+"][gift_code]");

	newRow.find("input[id^=gift_code]").attr("name","event_gift["+(total_rows)+"][gift_code]");
	newRow.find("input[id^=gift_code]").val('');
	newRow.find("input[id^=gift_amount]").attr("name","event_gift["+(total_rows)+"][gift_amount]");
	newRow.find("input[id^=use_point]").attr("name","event_gift["+(total_rows)+"][use_point]");
	
   
	//alert(newRow.html());  
	return newRow;
}

function sortGroup(init) {
	var groupAreaWrapper = $('#group_area_parent'),
		childrenSelector = '.group_info_area_wrapper';

	if (init) {
	/**	var sortFunction = function($data, customOptions) {
			var options = {
				reversed: false,
				by: function(a) { return a.text(); }
			};
			$.extend(options, customOptions);
			arr = $data.get();
			arr.sort(function(a, b) {
				var valA = options.by($(a));
				var valB = options.by($(b));
				if (options.reversed) {
					return (valA < valB) ? 1 : (valA > valB) ? -1 : 0;
				} else {
					return (valA < valB) ? -1 : (valA > valB) ? 1 : 0;
				}
			});
			return $(arr);
		};

		var filteredData = groupAreaWrapper.find(childrenSelector).clone();
		sortedData = sortFunction(filteredData, {
			'by' : function(v) {
				return $(v).find('.input-order').val();
			}
		});
		groupAreaWrapper.quicksand(sortedData, {
			'duration': 10
		});*/

	/**	var groupChildren = groupAreaWrapper.find(childrenSelector);
		groupChildren.sort(function(a, b) {
			var aVal = $(a).find('.input-order').val();
			var bVal = $(b).find('.input-order').val();
			if(aVal > bVal) { return 1; }
			if(aVal < bVal) { return -1; }
			return 0;
		});
		groupChildren.detach().appendTo(groupAreaWrapper);*/
	}
    if(groupAreaWrapper.data("sortable")) {
        groupAreaWrapper.sortable('destroy');
        groupAreaWrapper.sortable({
            'cursor': 'move',
            'items': childrenSelector,
            'handle': '.drag-link',
            'stop': function (event, ui) {
                groupAreaWrapper.find(childrenSelector).each(function (i, elt) {
                    $(this).find('.input-order').val((i + 1));
                });
            }
        });
    }

	groupAreaWrapper.find(childrenSelector).each(function(i, elt) {
		var tableBox = $(elt).find('.input_table_box').data('isOpened', true);

		$(elt).find('.slide-up-down-link').unbind('click').bind('click', function(e) {
			e.preventDefault();
			console.log("A");
			isOpened = tableBox.data('isOpened');
			isOpened ? tableBox.find('tr:not(:first)').hide() : tableBox.find('tr:not(:first)').show();
			isOpened ? $(this).addClass('closed') : $(this).removeClass('closed');
			tableBox.data('isOpened', !isOpened);

			return false;
		});
	});
}

function createProducts() {
	$(document).ready(function() {
		$('.items-to-create').each(function() {
			var groupCode = $(this).find('.group-code').html();
			ms_productSearch.groupCode = groupCode;
			$(this).find('.item').each(function() {
				var item = $(this);
				ms_productSearch._setProduct("productList_" + groupCode, "M", item.find('.id').html(), 
					item.find('.img-path').html(), item.find('.pname').html(), 
					item.find('.brand_name').html(), item.find('.sellprice').html(),
					item.find('.listprice').html(),  item.find('.reserve').html(), 
					item.find('.coprice').html(), item.find('.wholesale_price').html(), 
					item.find('.wholesale_sellprice').html(),
					item.find('.disp').html(),
					item.find('.state').html(),
					item.find('.dcprice').html(),
					item.find('.vieworder').html(),
					item.find('.view_cnt').html(),
					item.find('.regdate').html());
			});
		});
	});
}

function slideUpDownAll() {
	var allLinkDOM = $('.slide-up-down-all .slide-up-down-link'),
		isOpened = !allLinkDOM.hasClass('closed'),
		groupAreaWrapper = $('#group_area_parent'),
		childrenSelector = '.group_info_area_wrapper';

	
	groupAreaWrapper.find('.input_table_box').data('isOpened', isOpened);
	groupAreaWrapper.find('.slide-up-down-link').trigger('click');
	isOpened ? allLinkDOM.addClass('closed') : allLinkDOM.removeClass('closed');
}

function timeCheck(chk){
	var chkTime = $(chk).attr("time");
	if(chkTime == "true"){
		$(chk).parent().parent().find("input[type=text]").attr("disabled", false);
	}else{
		$(chk).parent().parent().find("input[type=text]").attr("disabled", true);
		$(chk).parent().parent().find("input[type=text]").val(0);
	}
}