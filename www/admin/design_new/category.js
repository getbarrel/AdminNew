function CategoryMode(cmode)
{
	if(cmode == "edit"){
		document.getElementById('edit_category').style.display ='block';
		document.getElementById('add_subcategory').style.display ='none';
	}else{
		document.getElementById('edit_category').style.display ='none';
		document.getElementById('add_subcategory').style.display ='block';
	}
}

function CategorySave(frm,vMode)
{
	//alert(frm);
	if (frm.this_category.value.length < 1){
		alert('수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요');
		return false;	
	}
	//alert(CheckForm);
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	//alert(iView.document.body.innerHTML);
	
//	frm.category_top_view.value = iView.document.body.innerHTML;
	frm.mode.value = vMode;
	frm.submit();
}

function SubCategorySave(frm,vMode)
{
	//alert(frm);
	if (frm.cid.value.length != 15){
		alert('추가 하시고자 하는 카테고리를 선택해 주세요');
		return false;	
	}
	
	if (frm.sub_category.value.length < 1){
		alert('추가 하시고자 하는 카테고리를 입력해 주세요');
		return false;	
	}
	
	if (frm.path.value.length < 1){
		alert('추가 하시고자 하는 파일경로를 입력해 주세요');
		return false;	
	}
	
	
	
	if (frm.sub_depth.value >= 4){
		alert('카테고리구성은 4단계까지만 가능합니다.');
		return false;	
	}
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	
	frm.mode.value = vMode;
	frm.submit();
}

function setCategory(cname,cid,depth, category_display_type, bbs_name)
{
	
	document.thisCategoryform.this_category.value = cname;
	document.thisCategoryform.cid.value = cid;
	document.thisCategoryform.this_depth.value = depth;
	
	if(category_display_type == "F"){
		document.thisCategoryform.category_display_type[0].checked = true;
		document.thisCategoryform.category_display_type[1].checked = false;
		document.thisCategoryform.category_display_type[2].checked = false;
		
		document.getElementById('bbs_select').style.display = 'none';
		document.getElementById("category_link").innerHTML = "<img src='../image/url_new.gif' style='cursor:hand;' align=absmiddle onclick=\"UrlCopy('/main/page.php?pgid="+cid+"');\"> &nbsp;/main/page.php?pgid="+cid; 
	}else if(category_display_type == "P"){
		document.thisCategoryform.category_display_type[0].checked = false;
		document.thisCategoryform.category_display_type[1].checked = true;
		document.thisCategoryform.category_display_type[2].checked = false;
		
		document.getElementById('bbs_select').style.display = 'none';
		document.getElementById("category_link").innerHTML = "<img src='../image/url_new.gif' style='cursor:hand;' align=absmiddle onclick=\"UrlCopy('/main/page.php?pgid="+cid+"');\"> &nbsp;/main/page.php?pgid="+cid; 
	}else{
		document.thisCategoryform.category_display_type[0].checked = false;
		document.thisCategoryform.category_display_type[1].checked = false;
		document.thisCategoryform.category_display_type[2].checked = true;
		
		document.getElementById('bbs_select').style.display = 'block';
		document.getElementById("category_link").innerHTML = "<img src='../image/url_new.gif' style='cursor:hand;' align=absmiddle onclick=\"UrlCopy('/customer/bbs.php?board="+bbs_name+"');\"> &nbsp;/customer/bbs.php?board="+bbs_name; 
	}
	
	
	document.category_order.this_depth.value = depth;
	document.category_order.cid.value = cid;
	
	//document.subCategoryform.cid.value = cid;
	document.subCategoryform.sub_depth.value = eval(depth+1);
	document.subCategoryform.cid.value = cid;
	
/*	
	if (depth+1 >=4){
		document.getElementById("add_subcategory").style.display = "none";
	}else{
		document.getElementById("add_subcategory").style.display = "block";
	}
*/	
	document.frames["calcufrm"].location.href='calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	document.frames["act"].location.href='category.save.php?mode=infoupdate&cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
}	

function UrlCopy(url) {	 
	 window.clipboardData.setData('Text', url);
	 alert('['+url+']\n선택한 주소가 클립보드에 복사되었습니다.!');
}

function order_up(frm){
	frm.mode.value = "up";
	if (frm.this_depth.value.length < 1){
		alert('상품카테고리를 선택해주세요');
		return false;	
	}
	
	frm.submit();
}

function order_down(frm){
	
	
	frm.mode.value = "down";
	if (frm.this_depth.value.length < 1){
		alert('상품카테고리를 선택해주세요');
		return false;	
	}
	
	frm.submit();
}
