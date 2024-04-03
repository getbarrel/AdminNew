function deleteProduct(act,id, addQuery){
	if(confirm(language_data['common']['G'][language])){
	//'정말로 삭제하시겠습니까?'
		window.frames['iframe_act'].location.href='./product_list.act.php?act='+act+'&id='+id+addQuery;
		//document.getElementById('iframe_act').src='./product_list.act.php?act='+act+'&id='+id+addQuery;//kbk
	}
}



function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	//var depth = sel.depth;
	var depth = sel.getAttribute("depth");//kbk
	//if(depth == 2){
	//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	//}
	//alert(target);
	//dynamic.src = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
	window.frames["act"].location.href = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	
}

function setCategory(cname,cid,depth,pid){
	//document.location.href='./product_list.php?cid='+cid+'&depth='+depth;
	//document.frames['act'].location.href='./product_list.php?cid='+cid+'&depth='+depth+'&view=innerview';//kbk
	window.frames["act"].location.href = './product_list.php?cid='+cid+'&depth='+depth+'&view=innerview';
}

function clearAll(frm){
		for(i=0;i < frm.cpid.length;i++){
				frm.cpid[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.cpid.length;i++){
				frm.cpid[i].checked = true;
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

function CopyData(frm, pid, pname){
	if(confirm(" ' "+pname+" ' 해당 상품에 대한 정보를 수정하시겠습니까?")){	
		var sfrm = document.forms['saveform'];
		
		sfrm.pid.value = pid;
		/*
		if(eval("frm.disp"+pid+".checked")){
			sfrm.disp.value = 1;
		}else{
			sfrm.disp.value = 0;
		}
		*/
		
		sfrm.pcode.value = eval("frm.pcode"+pid+".value");
		sfrm.reserve.value = eval("frm.reserve"+pid+".value");
		sfrm.reserve_rate.value = eval("frm.reserve_rate"+pid+".value");
		
		try{
			sfrm.state.value = eval("frm.state_"+pid+".value");
		}catch(e){
			sfrm.state.value = '';
		}
		//alert(eval("frm.state_"+pid+".value"));
		
		sfrm.search_keyword.value = eval("frm.search_keyword"+pid+".value");
		sfrm.coprice.value = eval("frm.coprice"+pid+".value");
		sfrm.sellprice.value = eval("frm.sellprice"+pid+".value");
		sfrm.listprice.value = eval("frm.listprice"+pid+".value");
	
		
		
		
		
		sfrm.action = "product_list.act.php";
		sfrm.submit();
	}
}
function CheckDelete(frm){
//	alert(document.getElementById('cpid').length);
//	alert(frm.cpid.length);
	var pid_obj=document.getElementsByName('pid[]');//kbk
	var pid_checked_bool = false;
	//for(i=0;i < frm.cpid.length;i++){//kbk
	for(i=0;i < pid_obj.length;i++){
		//if(frm.cpid[i].checked){//kbk
		if(pid_obj[i].checked){
			pid_checked_bool = true;
			//return true	
		}
	}
	if(!pid_checked_bool){
		alert(language_data['sns_product_list.js']['A'][language]);
		//'삭제하실 제품을 한개이상 선택하셔야 합니다.'
		return false;
	}
	return true;
}

function SelectDelete(frm){
	frm.act.value = "select_delete";
	//CheckDelete(frm);
	var pid_checked_bool = false;
	var pid_obj=document.getElementsByName("pid[]");//kbk
	//for(i=0;i < frm.cpid.length;i++){//kbk
	for(i=0;i < pid_obj.length;i++){
		//if(frm.cpid[i].checked){//kbk
		if(pid_obj[i].checked){
			pid_checked_bool = true;
		}
		//	frm.cpid[i].checked = false;
	}
	if(!pid_checked_bool){
		alert(language_data['sns_product_list.js']['A'][language]);//'삭제하실 제품을 한개이상 선택하셔야 합니다'
		return;
	}else{
		if(confirm(language_data['common']['G'][language])) {
		//"삭제하시겠습니까?"
			frm.submit();
		}
	}
}

function GoodsSelectUpdate(frm){
	var pid_checked_bool = false;
	var pid_obj=document.getElementsByName("pid[]");//kbk
	//for(i=0;i < frm.cpid.length;i++){//kbk
	for(i=0;i < pid_obj.length;i++){
		//if(frm.cpid[i].checked){//kbk
		if(pid_obj[i].checked){
			pid_checked_bool = true;
			//return true	
		}
	}
	if(!pid_checked_bool){
		alert(language_data['sns_product_list.js']['B'][language]);
		//'수정하실 제품을 한개이상 선택하셔야 합니다.'
		return;
	}
	frm.act.value = "update";
	frm.submit();
	
}

function SelectUpdate(frm){
	//alert(frm.search_searialize_value.value.length);
	SelectUpdateLoading();
	
	
	if(frm.update_type.value == 1){
		if(parseInt(frm.search_searialize_value.value.length) <= 58){
			alert(language_data['sns_product_list.js']['C'][language]);//'검색상품 전체에 대한 적용은 검색후 가능합니다.'
			select_update_unloading();
			return false;
		}
		
		if(confirm(language_data['sns_product_list.js']['G'][language])){//'검색상품 전체에 정보변경을 하시겠습니까?'
			return true;
		}else{
			select_update_unloading();
			return false;
		}
	}else if(frm.update_type.value == 2){
		var pid_checked_bool = false;
		var pid_obj=document.getElementsByName("select_pid[]");//kbk
		//for(i=0;i < frm.cpid.length;i++){//kbk
		for(i=0;i < pid_obj.length;i++){
			//if(frm.cpid[i].checked){//kbk
			if(pid_obj[i].checked){
				pid_checked_bool = true;
			}
			//	frm.cpid[i].checked = false;
		}
		if(!pid_checked_bool){
			alert(language_data['sns_product_list.js']['D'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
			select_update_unloading();
			return false;
		}
	}
	
	//return false;
	frm.act.value = "update";
	return true;
	//frm.submit();
	
}

function SelectUpdate2(frm){
	//alert(frm.search_searialize_value.value.length);
	SelectUpdateLoading();
	var pid_checked_bool = false;
	var pid_obj=document.getElementsByName("cpid[]");//kbk
	//for(i=0;i < frm.cpid.length;i++){//kbk
	for(i=0;i < pid_obj.length;i++){
		//if(frm.cpid[i].checked){//kbk
		if(pid_obj[i].checked){
			pid_checked_bool = true;
		}
		//	frm.cpid[i].checked = false;
	}
	if(!pid_checked_bool){
		alert(language_data['sns_product_list.js']['D'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
		select_update_unloading();
		return false;
	}
	
	//return false;
	frm.act.value = "update";
	return true;
	//frm.submit();
	
}


function init_date(FromDate,ToDate) {
	var frm = document.search_form;
	
	
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



function select_date(FromDate,ToDate,dType) {
	var frm = document.search_form;
	
	if(dType == 1){
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
	}else{
		for(i=0; i<frm.vFromYY.length; i++) {
			if(frm.vFromYY.options[i].value == FromDate.substring(0,4))
				frm.vFromYY.options[i].selected=true
		}
		for(i=0; i<frm.vFromMM.length; i++) {
			if(frm.vFromMM.options[i].value == FromDate.substring(5,7))
				frm.vFromMM.options[i].selected=true
		}
		for(i=0; i<frm.vFromDD.length; i++) {
			if(frm.vFromDD.options[i].value == FromDate.substring(8,10))
				frm.vFromDD.options[i].selected=true
		}
		
		
		for(i=0; i<frm.vToYY.length; i++) {
			if(frm.vToYY.options[i].value == ToDate.substring(0,4))
				frm.vToYY.options[i].selected=true
		}
		for(i=0; i<frm.vToMM.length; i++) {
			if(frm.vToMM.options[i].value == ToDate.substring(5,7))
				frm.vToMM.options[i].selected=true
		}
		for(i=0; i<frm.vToDD.length; i++) {
			if(frm.vToDD.options[i].value == ToDate.substring(8,10))
				frm.vToDD.options[i].selected=true
		}
	}
	
}




function onLoad(FromDate, ToDate) {
	var frm = document.search_form;
	
	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	

	init_date(FromDate,ToDate);
	
}

function SelectUpdateLoading(){

		document.getElementById('select_update_parent_save_loading').style.zIndex = '1';
		with (document.getElementById('select_update_save_loading').style){

			width = '100%';
			height = '173px';
			display = 'block';
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

		obj.innerHTML = "<img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 상품정보를 변경중입니다. 잠시만 기다려주세요.";

		document.getElementById('select_update_save_loading').appendChild(obj);

		document.getElementById('select_update_save_loading').style.display = 'block';
}

function select_update_unloading(){

	parent.document.getElementById('select_update_parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('select_update_loadingbar').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').style.display = 'none';
}
