/*
	제목 : 전시관리 > 분류관리 : 이현우(2013-05-09)
	내용 : /admin/product/category.js 를 복사해와서 수정함

*/
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
	if (frm.sub_div_ix.value.length != 15){
		alert(language_data['category.js']['B'][language]);
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;	
	}

	if (frm.div_ix.value.length != 15){
		alert(language_data['category.js']['B'][language]);
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;	
	}

	
	
	if (frm.sub_category.value.length < 1){
		alert(language_data['category.js']['B'][language]);
		//'추가 하시고자 하는 상품카테고리를 입력해 주세요'
		return false;	
	}
	
	if (frm.sub_depth.value >= 5){
		alert(language_data['category.js']['C'][language]);
		//'카테고리구성은 4단계까지만 가능합니다.'
		return false;	
	}
	
	if(CheckFormValue(frm)){	
		frm.mode.value = vMode;
		frm.submit();
	}
}

function setCategory(cname,div_ix,depth, div_disp, div_menu_disp)
{
	//alert(cname+','+div_ix+','+depth);
	cname = cname.replace("&quot;","\"");
	cname = cname.replace("&#39;","'");
	document.thisCategoryform.this_category.value = cname;
	document.thisCategoryform.div_name.value = cname;
	document.thisCategoryform.div_ix.value = div_ix;
	document.thisCategoryform.this_depth.value = depth;			
	document.category_order.this_depth.value = depth;
	document.category_order.div_ix.value = div_ix;


	if (div_disp==1){
		document.thisCategoryform.disp[0].checked = true;
	}else{
		document.thisCategoryform.disp[1].checked = true;
	}
	if (div_menu_disp==1){
		document.thisCategoryform.menu_disp[0].checked = true;
	}else{
		document.thisCategoryform.menu_disp[1].checked = true;
	}
	
	document.subCategoryform.sub_depth.value = eval(depth+1);
	document.subCategoryform.div_ix.value = div_ix;		
	
	document.getElementById("calcufrm").src='calcurate.php?div_ix='+div_ix+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	//document.getElementById("act").src='display_div.save.php?mode=infoupdate&div_ix='+div_ix+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
	if (depth==1){
		document.thisCategoryform.menu_disp[0].disabled = true;
		document.thisCategoryform.menu_disp[1].disabled = true;		
		document.subCategoryform.menu_disp[0].disabled = true;
		document.subCategoryform.menu_disp[1].disabled = true;
	}else if (depth==0){
		document.subCategoryform.menu_disp[0].disabled = true;
		document.subCategoryform.menu_disp[1].disabled = true;	
		document.subCategoryform.parent_div_ix.value = div_ix;		
		document.thisCategoryform.menu_disp[0].disabled = false;
		document.thisCategoryform.menu_disp[1].disabled = false;		
	}
	
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

// 전시분류 동적 select box
function loadDivix(sel,target, div_ix) {
	//var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.name;	
	//alert(sel + "," + target + "," + div_ix);
	window.frames['iframe_act_thispage'].location.href = 'display_div.load.php?form=' + form + '&target=' + target +'&div_ix='+div_ix;
}

// 전시 배너분류 동적 select box
function loadBannerDivix(sel,target, div_ix) {
	//var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.name;	
	//alert(sel + "," + target + "," + div_ix);
	window.frames['iframe_act_thispage'].location.href = 'display_banner_div.load.php?form=' + form + '&target=' + target +'&div_ix='+div_ix;
}