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
		alert(language_data['category.js']['A'][language]);
		//'수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;	
	}
	
	//alert(iView.document.body.innerHTML);
	
	//frm.category_top_view.value = iView.document.body.innerHTML;
	frm.category_top_view.value = document.getElementById("iView").contentWindow.document.body.innerHTML; // 호환성 2011-04-07 kbk
	if(CheckFormValue(frm)){	
		frm.mode.value = vMode;
		frm.submit();
	}
	
	
}

function SubCategorySave(frm,vMode)
{
	//alert(frm);
	if (frm.cid.value.length != 15){
		alert(language_data['category.js']['B'][language]);
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;	
	}
	
	if (frm.sub_category.value.length < 1){
		alert(language_data['category.js']['B'][language]);
		//'추가 하시고자 하는 상품카테고리를 입력해 주세요'
		return false;	
	}
	
	if (frm.sub_depth.value >= 4){
		alert(language_data['category.js']['C'][language]);
		//'카테고리구성은 4단계까지만 가능합니다.'
		return false;	
	}
	
	if(CheckFormValue(frm)){	
		frm.mode.value = vMode;
		frm.submit();
	}
}

function setCategory(cname,cid,depth, category_display_type)
{
	
	cname = cname.replace("&quot;","\"");
	cname = cname.replace("&#39;","'");
	document.thisCategoryform.this_category.value = cname;
	document.thisCategoryform.cid.value = cid;
	document.thisCategoryform.this_depth.value = depth;
	
	if(category_display_type == "T"){
		document.thisCategoryform.category_display_type[0].checked = true;
		document.thisCategoryform.category_display_type[1].checked = false;
	}else{
		document.thisCategoryform.category_display_type[0].checked = false;
		document.thisCategoryform.category_display_type[1].checked = true;
	}
	document.thisCategoryform.ch_category_img.checked = false;
	document.thisCategoryform.ch_leftcategory_img.checked = false;
	document.thisCategoryform.ch_sub_img.checked = false;
	
	document.getElementById("category_link").innerHTML = "<img src='/admin/image/url_new.gif' style='cursor:pointer;' align=absmiddle onclick=\"UrlCopy('/shop/goods_list.php?cid="+cid+"&depth="+depth+"');\"> &nbsp;/shop/goods_list.php?cid="+cid+"&depth="+depth+""; 
	document.category_order.this_depth.value = depth;
	document.category_order.cid.value = cid;
	
	//document.subCategoryform.cid.value = cid;
	document.subCategoryform.sub_depth.value = eval(depth+1);
	document.subCategoryform.cid.value = cid;
	
	document.add_field.cid.value = cid;
	document.getElementById("category_name").innerHTML = cname;
	
	
/*	
	if (depth+1 >=4){
		document.getElementById("add_subcategory").style.display = "none";
	}else{
		document.getElementById("add_subcategory").style.display = "block";
	}
*/
	//dynamic.src = 'addfield.load.php?mode=view&cid=' + cid +'&form=add_field';	
	//document.frames["calcufrm"].location.href='calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	//document.frames["act"].location.href='category.save.php?mode=infoupdate&cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	document.getElementById("calcufrm").src='service_calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	document.getElementById("act").src='service_category.save.php?mode=infoupdate&cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	
}	
function UrlCopy(url) {	 
	 window.clipboardData.setData('Text', url);
	 alert('['+url+']\n'+language_data['category.js']['D'][language]);
	 //선택한 주소가 클립보드에 복사되었습니다.!
}

function order_up(frm){
	frm.mode.value = "up";
	if (frm.this_depth.value.length < 1){
		alert(language_data['category.js']['E'][language]);
		//'상품카테고리를 선택해주세요'
		return false;	
	}
	
	frm.submit();
}

function order_down(frm){
	//alert(frm.view);
	
	
	frm.mode.value = "down";
	if (frm.this_depth.value.length < 1){
		alert(language_data['category.js']['E'][language]);
		//'상품카테고리를 선택해주세요'
		return false;	
	}
	
	frm.submit();
}


function showTabContents(vid, tab_id){
	var area = new Array('edit_category','add_subcategory'); /*,'input_addfield'*/
	var tab = new Array('tab_01','tab_02'); /*,'tab_03'*/
	
	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';
			//document.getElementById(tab_id).className = 'on';
			if(window.addEventListener) { // 호환성 kbk
				document.getElementById(tab_id).setAttribute("class","on");
			} else {
				document.getElementById(tab_id).className = 'on';
			}
		}else{			
			document.getElementById(area[i]).style.display = 'none';
			//document.getElementById(tab[i]).className = '';
			if(window.addEventListener) { // 호환성 kbk
				document.getElementById(tab[i]).setAttribute("class","");
			} else {
				document.getElementById(tab[i]).className = '';
			}
		}
	}
	
}