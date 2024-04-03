function CategorySave(frm,vMode)
{
	//alert(frm);
	if (frm.this_category.value.length < 1){
		alert(language_data['referer.js']['A'][language]);//'수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;	
	}
	frm.mode.value = vMode;
	frm.submit();
}

function SubCategorySave(frm,vMode)
{
	//alert(frm);
	if (frm.cid.value.length != 15){
		alert(language_data['referer.js']['B'][language]);//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;	
	}
	
	if (frm.sub_category.value.length < 1){
		alert(language_data['referer.js']['B'][language]);//'추가 하시고자 하는 상품카테고리를 입력해 주세요'
		return false;	
	}
	
	if (frm.sub_depth.value >= 5){
		alert(language_data['referer.js']['E'][language]);//'카테고리구성은 4단계까지만 가능합니다.'
		return false;	
	}
	
	
	frm.mode.value = vMode;
	frm.submit();
}

function setCategory(cname,cid,depth,referer,keyword, parameter,catimg)
{
	
	document.thisCategoryform.this_category.value = cname;
	document.thisCategoryform.cid.value = cid;
	document.thisCategoryform.this_depth.value = depth;
	document.thisCategoryform.this_referer_url.value = referer;
	document.thisCategoryform.this_keyword.value = keyword;
	document.thisCategoryform.this_parameter.value = parameter;
	
	if(depth == 0 || depth ==1){
		document.thisCategoryform.this_referer_url.disabled = true;
		document.thisCategoryform.this_keyword.disabled = true;
		document.thisCategoryform.this_parameter.disabled = true;
	}else{
		document.thisCategoryform.this_referer_url.disabled = false;
		document.thisCategoryform.this_keyword.disabled = false;
		document.thisCategoryform.this_parameter.disabled = false;
	}
	document.category_order.this_depth.value = depth;
	document.category_order.cid.value = cid;
	
	//document.subCategoryform.cid.value = cid;
	document.subCategoryform.sub_depth.value = eval(depth+1);
	document.subCategoryform.cid.value = cid;
	
	if (depth+1 >=5){
		document.getElementById("add_subcategory").style.display = "none";
	}else{
		document.getElementById("add_subcategory").style.display = "block";
	}

if(catimg){
	document.getElementById("catimg").innerHTML = "<img src='/images/referer/cat_"+cid+".gif' style='cursor:hand;' align=absmiddle width='100' height='100'>"; 
}else{
	document.getElementById("catimg").innerHTML = ""; 
}
	//document.frames["calcufrm"].location.href='referer_calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	document.getElementById("calcufrm").src='referer_calcurate.php?cid='+cid+'&depth='+eval(depth+1); // 호환성 2011-04-05 kbk
}

function order_up(frm){
	frm.mode.value = "up";
	if (frm.this_depth.value.length < 1){
		alert(language_data['referer.js']['F'][language]);//'상품카테고리를 선택해주세요'
		return false;	
	}
	//alert(1);
	frm.submit();
}

function order_down(frm){
	frm.mode.value = "down";
	if (frm.this_depth.value.length < 1){
		alert(language_data['referer.js']['F'][language]);//'상품카테고리를 선택해주세요'
		return false;	
	}
	
	frm.submit();
}
