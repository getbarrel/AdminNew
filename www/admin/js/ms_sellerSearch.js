function allCheck(chk, name)
{
	$('input[name="'+name+'"]').attr('checked',((chk)	?	'checked':''));
}

var ms_brandSearch = {
	companyHTML: null,
	groupCode: null,
	
	// 상품 검색 레이어
	show_productSearchBox: function(evt, groupCode, mObj)
	{
		ms_brandSearch.groupCode = groupCode;
		ms_brandSearch.mObj = mObj;
		//alert(language);
		if(language == "korea"){
			var category_search_text = "카테고리검색";
			var keyword_search_text = "키워드검색";
			var product_name = "상품명";
			var product_code = "상품코드";
			var brand_name = "브랜드명";
			var select_category = "검색중입니다.";
			var choice_goods = "선택된 브랜드";
		}else if(language == "english"){
			var category_search_text = "Category";
			var keyword_search_text = "Keyword";
			var product_name = "Product Name";
			var product_code = "Product Code";
			var brand_name = "Brand Name";
			var select_category = "Please select a category ";
			var choice_goods = "Selected Products";
		}else if(language == "indonesian"){
			var category_search_text = "Category";
			var keyword_search_text = "Keyword";
			var product_name = "Product Name";
			var product_code = "Product Code";
			var brand_name = "Brand Name";
			var select_category = "Please select a category ";
			var choice_goods = "Selected Products";
		}else{
			var category_search_text = "카테고리검색";
			var keyword_search_text = "키워드검색";
			var product_name = "상품명";
			var product_code = "상품코드";
			var brand_name = "브랜드명";
			var select_category = "검색중입니다.";
			var choice_goods = "선택된 브랜드";
		}

		if(!$('div#div_productSearchBox').html())	{
			//alert(1);
			//$('<div id="div_productSearchBox" style="position:absolute;width:740px;height:430px;z-index:1;display:none;background-color:#FFFFFF;border:3px solid silver;padding:5px"></div>').appendTo('body');
			$('<div id="div_productSearchBox" style="position:absolute;width:740px;height:430px;z-index:1;display:none;background-color:#FFFFFF;border:3px solid silver;padding:5px"></div>').appendTo('div#container');
			
			// 검색탭
			var str = '<div id="div_productSearch1" style="float:left;width:200px;height:430px;background-color:#ffffff; ">';
			// 키워드 검색 폼
			str += '<div id="div_keywordForm" style="overflow:auto;height:400px;width:200px;border:1px solid #C0C0C0;">';
			str += '<div style="padding:3px 5px;"><select id="search_type"><!--option value="ccd.com_name">회사명</option--><option value="brand_name">'+brand_name+'</option></select> <input type="text" id="search_text" size="15" onkeypress="if(event.keyCode == 13){ms_brandSearch.searchText();return false;}" /></div>';
			str += '<div style="padding:3px 5px;"><img src="../images/'+language+'/btc_search.gif" onclick="ms_brandSearch.searchText();" style="cursor:pointer;" /></div></div>';
			str += '</div>';

			// 검색 상품 리스트
			str += '<div id="div_productSearch2" style="float:left;width:250px;height:430px;margin:0 0 0 10px;border:1px solid #C0C0C0;">';
			str += '<div id="div_productAction" style="height:30px;border-bottom:1px solid #C0C0C0;"><div style="padding:5px;"><input type="checkbox" id="chk_all" style="vertical-align:middle;" value="Y" onclick="allCheck(this.checked, \'pList\');" /><img src="../images/'+language+'/btn_selected_reg.gif" border="0" onclick="ms_brandSearch._selectProduct();" style="cursor:pointer;vertical-align:middle;" />';
			str += ' <select id="listMax" onchange="ms_brandSearch._getBrandList(null, null, 1);" style="vertical-align:middle;"><option value="5">5</option><option value="10">10</option><option value="20" selected>20</option><option value="30">30</option><option value="50">50</option><option value="100">100</option></select></div></div>';
			str += '<div id="div_brandList" style="overflow:auto;border-bottom:1px solid #C0C0C0;height:370px;"><div style="text-align:center;padding:150px 0 0 0;color:gray;">'+select_category+'</div></div>';
			str += '<div id="div_productPaging" style="height:30px;text-align:center;"></div>';
			str += '</div>';

			// 선택 상품 리스트
			str += '<div id="div_productSearch3" style="float:left;width:250px;height:430px;margin:0 0 0 10px;border:1px solid #C0C0C0;">';
			str += '<div id="div_productSelectT" style="height:30px;border-bottom:1px solid #C0C0C0;"><div style="padding:10px;font-size:11px;font-weight:bold;text-align:center;">'+choice_goods+'</div></div>';
			str += '<div id="div_productSelect" style="overflow:auto;border-bottom:1px solid #C0C0C0;height:370px;"></div>';
			str += '<div id="div_productSelectB" style="height:30px;"><div style="padding:5px;"><img src="../images/'+language+'/btn_selected_del.gif" border="0" onclick="ms_brandSearch._delProductSel();" style="cursor:pointer;vertical-align:middle;" /> <img src="../images/'+language+'/btn_whole_del.gif" border="0" onclick="ms_brandSearch._delProductAll();" style="cursor:pointer;vertical-align:middle;" /> <img src="../images/'+language+'/btn_win_close.gif" border="0" onclick="ms_brandSearch._closeDiv();" style="cursor:pointer;vertical-align:middle;" /></div></div>';
			str += '</div>';

			$('div#div_productSearchBox').append(str);
		}
		
		$('div#div_productSelect').html('');
		$('ul#brandList_'+ms_brandSearch.groupCode+' input[name="rbrand['+ms_brandSearch.groupCode+'][]"]').each(function()	{
			band_ix = $(this).val();
			ms_brandSearch._setBrand('div_productSelect', 'A', band_ix, $('#pImage_'+ms_brandSearch.groupCode+'_'+band_ix).attr('src'), $('#brandName_'+ms_brandSearch.groupCode+'_'+band_ix).val(), $('#brandName_'+ms_brandSearch.groupCode+'_'+band_ix).val(), $('#pPrice_'+ms_brandSearch.groupCode+'_'+band_ix).val());
		});
		
		var tg = (evt.target)	?	evt.target:evt.srcElement;
		//$('div#div_productSearchBox').css('left',(parseInt(getOffsetLeft(tg))+100)+'px');
		//$('div#div_productSearchBox').css('top',(parseInt(getOffsetTop(tg))-440)+'px');
		$('div#div_productSearchBox').css('left',(parseInt(getOffsetLeft(tg)))+'px');
		$('div#div_productSearchBox').css('top',(parseInt(getOffsetTop(tg))-50)+'px');
		$('div#div_productSearchBox').slideDown();

		ms_brandSearch._getBrandList('list', 1, 1);
	},

	searchText: function()
	{
		ms_brandSearch._getBrandList('search_list', 1, 1, $('#search_type').val(), $('#search_text').val(), $('select#company_id option:selected').val());
	},

	// 상품 불러오기
	_getBrandList: function()
	{
		//alert(this._mode);
		this._max			= $('select#listMax option:selected').val();
		this._mode			= (arguments[0])	?	arguments[0]:this._mode;
		this._nset			= (arguments[1])	?	arguments[1]:this._nset;
		this. _page			= (arguments[2])	?	arguments[2]:this._page;
		if(this._mode == 'list')	{
			this._search_type	= '';
			this._search_text	= '';
		}	else	{
			this._search_type	= (arguments[3])	?	arguments[3]:this._search_type;
			this._search_text	= (arguments[4])	?	arguments[4]:this._search_text;
		}

		$('div#div_brandList').html('<div style="text-align:center;padding:150px 0 0 0;color:gray;">브랜드를 검색 중입니다.</div>');
		if(!this._mode)	return;
		//alert('/admin/product/relationAjax.category.act.php?mode='+this._mode+'&search_type='+this._search_type+'&search_text='+this._search_text+'&page='+this._page+'&max='+this._max+'&cid='+this._cid+'&depth='+this._depth);
		
		$.ajax({
			url:'/admin/display/relationAjax.brand.act.php',
			type: 'post',
			dataType: 'xml',
			data: ({mode: this._mode,
					search_type: this._search_type,
					search_text: this._search_text,
					page: this._page,
					max: this._max,
			}),
			error: function(xhr){
				$('div#div_brandList').html('<div style="text-align:center;padding:150px 0 0 0;color:gray;">검색된 브랜드가 없습니다.</div>');
				//alert('XML 문서 해석 실패 - '+xhr.status());
			},
			success: function(data)	{
				//document.write(data);
				var total = $(data).find('relationBrands').attr('total');
				if(total > 0)	{
					$('#div_productPaging').html(ms_brandSearch._getPageString(total));
				}
				var items = $(data).find('relationBrands').find('brands');
				var str = '';
				$('#div_brandList').html('');
				items.each(function()	{
					ms_brandSearch._setBrand('div_brandList', 'L', $(this).find('b_ix').text(), $(this).find('img_src').text(), $(this).find('brand_name').text(), $(this).find('brand_name').text(), $(this).find('sellprice').text())
				});
			}
		});
	},

	_tabChange: function(mode)
	{
		if(mode == 'category')	{
			$('#tab_keyword').removeClass('on');
			$('#div_keywordForm').hide();
			$('#tab_category').addClass('on');
			$('#div_categoryForm').show();
		}	else	{
			$('#tab_category').removeClass('on');
			$('#div_categoryForm').hide();
			$('#tab_keyword').addClass('on');
			$('#div_keywordForm').show();
		}
	},

	_selectProduct: function()
	{
		$('input:checkbox[name="pList"]:checked').each(function()	{
			ms_brandSearch._setBrand('div_productSelect','R',$(this).val());
		});
	},

	// 상품 디스플레이
//	
	_setBrand: function(obj, mode, band_ix, imgSrc, brandName, bName, pPrice)
	{
		var str = '';
		var str2 = '';
		//var brandName = (bName)	?	'['+bName+']<br />':'';
		var imgTag = '<img id="pImage_'+ms_brandSearch.groupCode+'_'+band_ix+'" src="'+imgSrc+'" width="130px" height=40 title="'+brandName+'" style="margin:3px;" onerror="this.src=\'/admin/images/noimages_50.gif\';" />';
		if(mode == 'L')	{
			str += '<table width="100%" border="0" >';
			str += '<col width="30" /><col width="60" /><col width="*" />';
			str += '<tr align="center">';
			str += '<td><input type="checkbox" name="pList" value="'+band_ix+'" /></td>';
			str += '<td id="imgObj_'+band_ix+'" onclick=\'ms_brandSearch._setBrand("div_productSelect","R","'+band_ix+'","'+imgSrc+'","'+brandName+'","'+brandName+'","'+pPrice+'");\'>'+imgTag+'<input type="hidden" name="band_ix" value="'+band_ix+'" /><input type="hidden" id="brandName_'+ms_brandSearch.groupCode+'_'+band_ix+'" value="'+brandName+'" /><input type="hidden" id="brandName_'+ms_brandSearch.groupCode+'_'+band_ix+'" value="'+brandName+'" /><input type="hidden" id="pPrice_'+ms_brandSearch.groupCode+'_'+band_ix+'" value="'+pPrice+'" /></td>';
			str += '<td id="textObj_'+band_ix+'" style="text-align:left;">'+brandName+'<div style="margin:3px 0 0 0;width:140px;text-overflow:ellipsis; overflow:hidden;white-space: nowrap;">'+brandName+'</div><div style="margin:3px 0 0 0;">'+pPrice+'</div></td>';
			str += '</tr>';
			str += '<tr><td colspan=3 class="dot-x"></td></tr>';
			str += '</table>';
			$('#'+obj).append(str);
		}	else if(mode == 'A')	{
			str += '<table id="tb_brandList_'+ms_brandSearch.groupCode+'_'+band_ix+'" width="100%" border="0"><!--style="background:url(/admin/image/dot.gif) repeat-x left bottom;"-->';
			str += '<col width="30" /><col width="60" /><col width="*" />';
			str += '<tr align="center">';
			str += '<td><input type="checkbox" name="pList2" value="'+band_ix+'" /></td>';
			str += '<td>'+imgTag+'<input type="hidden" name="band_ix" value="'+band_ix+'" /><input type="hidden" id="brandName_'+ms_brandSearch.groupCode+'_'+band_ix+'" value="'+brandName+'" /><input type="hidden" id="brandName_'+ms_brandSearch.groupCode+'_'+band_ix+'" value="'+brandName+'" /><input type="hidden" id="pPrice_'+ms_brandSearch.groupCode+'_'+band_ix+'" value="'+pPrice+'" /></td>';
			str += '<td style="text-align:left;">'+brandName+'<div style="margin:3px 0 0 0;width:140px;text-overflow:ellipsis; overflow:hidden;white-space: nowrap;">'+brandName+'</div><div style="margin:3px 0 0 0;">'+pPrice+'</div></td>';
			str += '</tr>';
			str += '<tr><td colspan=3 class="dot-x"></td></tr>';
			str += '</table>';
			$('#'+obj).append(str);
		}	else if(mode == 'R')	{
			isReg = false;
			$('input:hidden[name="rbrand['+ms_brandSearch.groupCode+'][]"]').each(function() {
				if(band_ix == $(this).val())	{
					isReg = true;
					return false;
				}
			});
			if(!isReg)	{
				str += '<table id="tb_brandList_'+ms_brandSearch.groupCode+'_'+band_ix+'" width="100%" border="0" ><!--style="background:url(/admin/image/dot.gif) repeat-x left bottom;"-->';
				str += '<col width="30" /><col width="60" /><col width="*" />';
				str += '<tr align="center">';
				str += '<td><input type="checkbox" name="pList2" value="'+band_ix+'" /></td>';
				str += '<td>'+$('#imgObj_'+band_ix).html()+'</td>';
				str += '<td style="text-align:left;">'+$('#textObj_'+band_ix).html()+'</td>';
				str += '</tr>';
				str += '<tr><td colspan=3 class="dot-x"></td></tr>';
				str += '</table>';

				str2 += '<li id="li_brandList_'+ms_brandSearch.groupCode+'_'+band_ix+'" style="float:left;">'+$('#imgObj_'+band_ix).html()+'<input type="hidden" name="rbrand['+ms_brandSearch.groupCode+'][]" value="'+band_ix+'" /></li>';
				$('#'+this.mObj).append(str2);
				$('#'+obj).append(str);

				$('ul[name=brandList]>li').dblclick(function()	{
					ms_brandSearch._delProduct(this);
				});
			}
			
		}	else if(mode == 'M')	{
			str += '<li id="li_brandList_'+ms_brandSearch.groupCode+'_'+band_ix+'" style="float:left;">'+imgTag+'<input type="hidden" name="rbrand['+ms_brandSearch.groupCode+'][]" value="'+band_ix+'" /><input type="hidden" id="brandName_'+ms_brandSearch.groupCode+'_'+band_ix+'" value="'+brandName+'" /><input type="hidden" id="brandName_'+ms_brandSearch.groupCode+'_'+band_ix+'" value="'+brandName+'" /><input type="hidden" id="pPrice_'+ms_brandSearch.groupCode+'_'+band_ix+'" value="'+pPrice+'" /></li>';
			$('#'+obj).append(str);
			$('ul[name=brandList]>li').dblclick(function()	{
				ms_brandSearch._delProduct(this);
			});
		}
		
	},

	// 개별 삭제
	_delProduct: function(obj)
	{
		$(obj).html('');
		$(obj).remove();
	},

	// 선택 삭제
	_delProductSel: function()
	{
		$('input[name="pList2"]:checked').each(function()	{
			$('table#tb_brandList_'+ms_brandSearch.groupCode+'_'+$(this).val()).html('');
			$('table#tb_brandList_'+ms_brandSearch.groupCode+'_'+$(this).val()).remove();
			$('li#li_brandList_'+ms_brandSearch.groupCode+'_'+$(this).val()).html('');
			$('li#li_brandList_'+ms_brandSearch.groupCode+'_'+$(this).val()).remove();
		});
	},

	// 모두 삭제
	_delProductAll: function()
	{
		$('#brandList_'+ms_brandSearch.groupCode).html('');
		$('#div_productSelect').html('');
	},

	_closeDiv: function()
	{
		$('div#div_productSearchBox').slideUp();

	},

	// 페이지 스트링
	_getPageString: function(total_record)
	{
		var page = (arguments[1])	?	arguments[1]:this._page;
		var pageString = '';
		var page_count	= 5;	// 표현할 페이지 스트링 수
		var total_page	= Math.ceil(total_record / this._max);	// 총 페이지 수
		var setPage = Math.floor((page - 1) / page_count) * page_count + 1;
		if((prev = page - page_count) > 0)	pageString += '<img src="/admin/image/pre_pageset.gif" onclick="ms_brandSearch._getBrandList(null, null,'+prev+');" style="cursor:pointer;vertical-align:middle;" /> ';
		var i = 1;
		var tmp = new Array();
		while(i <= page_count && total_page >= setPage)	{
			if(setPage == page)	tmp[i] = '<span style="color:#FF0000;font-weight:bold;">'+setPage+'</span>';
			else	tmp[i] = '<a href="javascript:ms_brandSearch._getBrandList(null, null,'+setPage+');" style="font-weight:bold;color:gray;">'+setPage+'</a>';
			setPage++;
			i++;
		}
		pageString += tmp.join(' ');

		if(page + page_count < total_page)	{
			next = page + page_count;
		}	else	{
			if(setPage < total_page)	{
				next = total_page;
			}	else	{
				next = '';
			}
		}
		if(next)	pageString += ' <img src="/admin/image/next_pageset.gif" onclick="ms_brandSearch._getBrandList(null, null,'+next+');" style="cursor:pointer;vertical-align:middle;" />';

		if(setPage > page_count + 1)	{
			pageString = '<font style="color:gray;"><a href="javascript:ms_brandSearch._getBrandList(null, null, 1);" style="font-weight:bold;color:gray;">1</a>...</font> ' + pageString;
		}

		if(setPage <= total_page)	{
			pageString += ' <font style="color:gray;">...<a href="javascript:ms_brandSearch._getBrandList(null, null,'+(total_page)+');" style="font-weight:bold;color:gray;">'+total_page+'</a></font>';
		}
		return '<div style="padding:10px 0 0 0;">'+pageString+'</div>';

	},

	getProduct: function(type)	{
		//$.data('pid', 

	}

};

$(function()	{
	if($('ul[name=brandList]').length >= 1){
		//alert($('ul[name=brandList]').length +":::"+$('ul[name=brandList]'));
		$('ul[name=brandList]').sortable();
		$('ul[name=brandList]').disableSelection();
		if($('ul[name=brandList]>li').length>0) $('ul[name=brandList]').sortable();
		if($('ul[name=brandList]>li').length>0) $('ul[name=brandList]').disableSelection();
	}

	$('ul[name=brandList]>li').dblclick(function()	{
		ms_brandSearch._delProduct(this);
	});
});