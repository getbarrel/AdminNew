function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_display','batch_update_category','batch_update_bs_goods_stock'); //,'batch_update_sms','batch_update_coupon'

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
			$.cookie('goodss_goods_list', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}

function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	//var depth = sel.getAttribute("depth");//kbk
	var depth = $("select[name="+sel.name+"]").attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//alert(depth);
	//alert($("select[name="+sel.name+"]").attr('depth'));
	//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	//document.write('../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}

function clearAll(frm){
		for(i=0;i < frm.goodss_pid.length;i++){
				frm.goodss_pid[i].checked = false;
		}
}
function checkAll(frm){
	for(i=0;i < frm.goodss_pid.length;i++){
				frm.goodss_pid[i].checked = true;
		}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function SelectDeleteWholeSaleGooods(frm){
	
		frm.act.value = "select_delete";
		frm.action = "../product/product_list.act.php";

		//CheckDelete(frm);
		var pid_checked_bool = false;
		var pid_obj=document.getElementsByName("goodss_pid[]");//kbk
		//for(i=0;i < frm.select_pid.length;i++){//kbk
		for(i=0;i < pid_obj.length;i++){
			//if(frm.select_pid[i].checked){//kbk
			if(pid_obj[i].checked){
				pid_checked_bool = true;
			}
			//	frm.select_pid[i].checked = false;
		}
		if(!pid_checked_bool){
			alert(language_data['product_list.js']['D'][language]);//'삭제하실 제품을 한개이상 선택하셔야 합니다'
			return;
		}else{
			if(confirm(language_data['product_list.js']['E'][language])){//'선택하신 상품을 정말로 삭제하시겠습니까? 삭제하시면 상품과 관련된 모든 데이타가 삭제되게 됩니다.'
				frm.submit();
			}
		}
}

function GoodssSelectUpdate(frm){
	//alert(frm.search_searialize_value.value.length);
	if($('input:radio[name^=update_kind]:checked').val() == "category"){
		if(!(frm.c_cid.value.length > 0)){
			alert('변경 또는 추가하시고자 하는 카테고리를 선택해주세요');
			return false;
		}
	}else if($('input:radio[name^=update_kind]:checked').val() == "bs_goods_stock"){

	}
	//	alert($('input:radio[name^=update_kind]:checked').val());
	//return false;
	SelectUpdateLoading();
	
	
	if(frm.update_type.value == 1){
		if(parseInt(frm.search_searialize_value.value.length) <= 58){
			alert(language_data['product_list.js']['K'][language]);	//'검색상품 전체에 대한 적용은 검색후 가능합니다.'
			select_update_unloading();
			return false;
		}
		
		if(confirm(language_data['product_list.js']['G'][language])){//'검색상품 전체에 정보변경을 하시겠습니까?'
			return true;
		}else{
			select_update_unloading();
			return false;
		}
	}else if(frm.update_type.value == 2){
		var pid_checked_bool = false;
		var pid_obj=document.getElementsByName("goodss_pid[]");//kbk
		//for(i=0;i < frm.cpid.length;i++){//kbk
		for(i=0;i < pid_obj.length;i++){
			//if(frm.cpid[i].checked){//kbk
			if(pid_obj[i].checked){
				pid_checked_bool = true;
			}
			//	frm.cpid[i].checked = false;
		}
		if(!pid_checked_bool){
			alert(language_data['product_list.js']['H'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
			select_update_unloading();
			return false;
		}
	}

	
	
	//return false;
	frm.act.value = "stock_update";
	return true;
	//frm.submit();
	
}



function SelectUpdateLoading(){
	document.getElementById('select_update_parent_save_loading').style.zIndex = '1';
	with (document.getElementById('select_update_save_loading').style){

		width = '100%';
		height = '173px';
		backgroundColor = '#ffffff';
		filter = 'Alpha(Opacity=70)';
		opacity = '0.8';
	}

	var obj = document.createElement('div');
	with (obj.style){
		position = 'relative';
		zIndex = 100;
	}
	obj.id = 'select_update_loadingbar';

	obj.innerHTML = "<img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 상품을 가져오는 중입니다..";

	document.getElementById('select_update_save_loading').appendChild(obj);

	document.getElementById('select_update_save_loading').style.display = 'block';
}


function select_update_unloading(){

	parent.document.getElementById('select_update_parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('select_update_loadingbar').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').style.display = 'none';
}
	