function deleteProduct(act,id, addQuery){	
	if(confirm('정말로 삭제하시겠습니까? \n상품이 삭제될 때 주문진행 중인 상품이 있을 경우 \n주문데이터에 문제가 발생 할 수 있습니다.')){//'정말로 삭제하시겠습니까?'
		//document.frames['iframe_act'].location.href='./product_list.act.php?act='+act+'&id='+id+addQuery;
		document.getElementById('iframe_act').src='./product_list.act.php?act='+act+'&id='+id+addQuery;//kbk
	}
}



function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	//var depth = sel.depth;
	//var depth = sel.getAttribute("depth");//kbk
	var depth = $("select[name="+sel.name+"]").attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//if(depth == 2){
	//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	//}
	//alert(target);
	//dynamic.src = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
	
	window.frames["act"].location.href = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	//document.getElementById("act").src = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	
}

function setCategory(cname,cid,depth,pid){
	//document.location.href='./product_list.php?cid='+cid+'&depth='+depth;
	//document.frames['act'].location.href='./product_list.php?cid='+cid+'&depth='+depth+'&view=innerview';//kbk
	document.getElementById('act').src='./product_list.php?cid='+cid+'&depth='+depth+'&view=innerview';
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

function CopyData(frm_name, pid){
	//var language = "korea";
	var frm = document.forms[frm_name];
	//alert(eval("document."+frm_name+".h_pname_"+pid+".value"));
	var pname = $("#h_pname_"+pid).val();//eval("document."+frm_name+".h_pname_"+pid+".value");
	//alert(language);
	//alert(language_data['product_list.js']['C'][language]);
	if(confirm(" ' "+pname+" ' "+ language_data['product_list.js']['C'][language])){	//해당 상품에 대한 정보를 수정하시겠습니까?
	
		var sfrm = document.forms['saveform'];
		
		sfrm.pid.value = pid;
		/*
		if(eval("frm.disp"+pid+".checked")){
			sfrm.disp.value = 1;
		}else{
			sfrm.disp.value = 0;
		}
		*/

		//alert($("#pcode"+pid).val());
		//alert(document.forms[frm_name].elements["pcode"+pid].value);
		
		sfrm.pcode.value = $("#pcode"+pid).val();//eval("frm.pcode"+pid+".value");
		sfrm.reserve.value = $("#reserve"+pid).val();//eval("frm.reserve"+pid+".value");
		sfrm.reserve_rate.value = $("#reserve_rate"+pid).val();//eval("frm.reserve_rate"+pid+".value");
		
		try{
			sfrm.state.value = $("#state_"+pid).val();//eval("frm.state_"+pid+".value");
		}catch(e){
			sfrm.state.value = '';
		}
		//alert(eval("frm.state_"+pid+".value"));
		
		sfrm.search_keyword.value = $("#search_keyword"+pid).val();//eval("frm.search_keyword"+pid+".value");
		sfrm.coprice.value = $("#coprice"+pid).val();//eval("frm.coprice"+pid+".value");
		//2012-11-07 홍진영 오라클에서는 undefined 이면 쿼리 undefined로 값이 들어가 ORA-01722: invalid number  오류나서 수정
		if($("#wholesale_price"+pid).val()){
			sfrm.wholesale_price.value = $("#wholesale_price"+pid).val();
		}
		if($("#wholesale_sellprice"+pid).val()){
			sfrm.wholesale_sellprice.value = $("#wholesale_sellprice"+pid).val();
		}
		sfrm.sellprice.value = $("#sellprice"+pid).val();//eval("frm.sellprice"+pid+".value");
		sfrm.listprice.value = $("#listprice"+pid).val();//eval("frm.listprice"+pid+".value");
	
		
		sfrm.action = "product_list.act.php";
		sfrm.submit();
	}
}

function CheckDelete(frm){
//	alert(document.getElementById('cpid').length);
//	alert(frm.cpid.length);
	var pid_obj=document.getElementsByName('cpid[]');//kbk
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
		alert(language_data['product_list.js']['D'][language]);//'삭제하실 제품을 한개이상 선택하셔야 합니다.'
		return false;
	}
	return true;
}

function SelectDelete(frm){
	
		frm.act.value = "select_delete";
		frm.action = "../product/product_list.act.php";
		//CheckDelete(frm);
		var pid_checked_bool = false;
    	var pid_state_checked_bool = true;
		var pid_obj=document.getElementsByName("cpid[]");//kbk
		//for(i=0;i < frm.cpid.length;i++){//kbk
		for(i=0;i < pid_obj.length;i++){
			//if(frm.cpid[i].checked){//kbk
			if(pid_obj[i].checked){
				pid_checked_bool = true;
			}
			if($(pid_obj[i]).attr('state')=='1' || $(pid_obj[i]).attr('state')=='0'  ){
                pid_obj[i].checked = false;
                pid_state_checked_bool = false;
			}
			//	frm.cpid[i].checked = false;
		}
		if(!pid_checked_bool){
			alert('삭제하실 제품을 한개이상 선택하셔야 합니다');//'삭제하실 제품을 한개이상 선택하셔야 합니다'
			return;
		}else if(!pid_state_checked_bool){
            if(confirm('선택된 상품에 판매중,일시품절중인 상품이 포함되어 있습니다. \n해당 상품을 제외 하고 삭제 하시겠습니까?.')) {
                frm.submit();
            }
		}else{
			if(confirm('선택하신 상품을 정말로 삭제하시겠습니까? \n삭제하시면 상품과 관련된 모든 데이타가 삭제되게 됩니다.\n상품이 삭제될 때 주문진행 중인 상품이 있을 경우 \n주문데이터에 문제가 발생 할 수 있습니다.')){//'선택하신 상품을 정말로 삭제하시겠습니까? 삭제하시면 상품과 관련된 모든 데이타가 삭제되게 됩니다.'
				frm.submit();
			}
		}
}

function SelectDeleteBuyingServiceGooods(frm){
	
		frm.act.value = "select_delete";
		//frm.action = "product_list.act.php";

		//CheckDelete(frm);
		var pid_checked_bool = false;
		var pid_obj=document.getElementsByName("select_pid[]");//kbk
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

function GoodsSelectUpdate(frm){
	var pid_checked_bool = false;
	var pid_obj=document.getElementsByName("cpid[]");//kbk
	//for(i=0;i < frm.cpid.length;i++){//kbk
	for(i=0;i < pid_obj.length;i++){
		//if(frm.cpid[i].checked){//kbk
		if(pid_obj[i].checked){
			pid_checked_bool = true;
			//return true	
		}
	}
	if(!pid_checked_bool){
		alert(language_data['product_list.js']['F'][language]);//'수정하실 제품을 한개이상 선택하셔야 합니다.'
		return;
	}
	frm.act.value = "update";
	frm.submit();
	
}

function SelectUpdate(frm){
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
			alert(language_data['product_list.js']['H'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
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
		alert(language_data['product_list.js']['H'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
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
			height = '179px';
			backgroundColor = '#ffffff';
			filter = 'Alpha(Opacity=70)';
			//border = "1px solid red";
			opacity = '0.8';
			//left = "-20px";
			//top = "-14px";
		}

		var obj = document.createElement('div');
		with (obj.style){
			position = 'relative';
			zIndex = 100;
		}
		obj.id = 'select_update_loadingbar';

		obj.innerHTML = "<table width=100% height=100%><tr><td valign=middle align=center><img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 상품정보를 변경중입니다. 잠시만 기다려주세요.</td></tr></table>";

		document.getElementById('select_update_save_loading').appendChild(obj);

		document.getElementById('select_update_save_loading').style.display = 'block';
}

function select_update_unloading(){

	parent.document.getElementById('select_update_parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('select_update_loadingbar').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').style.display = 'none';
}

function ShowSearchDiv(info){

	if(info == 'text'){
		$('#search_text_div').css('display','');
		$('#search_excel_div').css('display','none');
		$('table#tab_01').attr('class','on');
		$('table#tab_02').attr('class','');
	}else{
		$('#search_text_div').css('display','none');
		$('#search_excel_div').css('display','');
		$('table#tab_02').attr('class','on');
		$('table#tab_01').attr('class','');
	}
}

function cid_del(code){
	$('#row_'+code).remove();
}


$(document).ready(function (){

//다중검색어 시작 2014-04-10 이학봉

	$('input[name=mult_search_use]').click(function (){
		var value = $(this).attr('checked');

		if(value == 'checked'){
			$('#search_text_input_div').css('display','none');
			$('#search_text_area_div').css('display','');
			
			$('#search_text_input_div').find("input").css('disabled',true);
			$('#search_text_area_div').find("input").attr('disabled',false);

			$('#search_text_area').attr('disabled',false);
			$('#search_texts').attr('disabled',true);
		}else{
			$('#search_text_input_div').css('display','');
			$('#search_text_area_div').css('display','none');

			$('#search_text_area').attr('disabled',true);
			$('#search_texts').attr('disabled',false);

			$('#search_text_input_div').find("input").css('disabled',false);
			$('#search_text_area_div').find("input").attr('disabled',true);

		}
	});

	var mult_search_use = $('input[name=mult_search_use]:checked').val();
		
	if(mult_search_use == '1'){
		$('#search_text_input_div').css('display','none');
		$('#search_text_area_div').css('display','');

		$('#search_text_area').attr('disabled',false);
		$('#search_texts').attr('disabled',true);
	}else{
		$('#search_text_input_div').css('display','');
		$('#search_text_area_div').css('display','none');

		$('#search_text_area').attr('disabled',true);
		$('#search_texts').attr('disabled',false);
	}

//다중검색어 끝 2014-04-10 이학봉

});


$(function () { 

	//$("#search_excel_file").wrapAll("<form id='myupload' action='product_list.act.php' method='post' enctype='multipart/form-data></form>"); 
	$("#search_excel_file").change(function(){
		$("#search_form").ajaxSubmit({
			type:'POST',
			url:'product_list.act.php',
			dataType:  'html', 
			success: function(data) { 
				$('#search_excel_file').val('');
			},
			error:function(xhr){ 
				//alert('error');
			}
		}); 
	}); 

});


function excel_down_submit(frm){

	var favorites_excel = $('select[name=favorites_excel] :selected').val();

	if(favorites_excel){
		frm.favorites_excel_idx.value=favorites_excel;
        frm.act.value = "list_down";
        frm.action = "../product/product_update_exceldown2.php";
	}else {
        frm.act.value = "excel_down";
        frm.action = "../product/product_update_exceldown.php";
    }
	//CheckDelete(frm);
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
		alert('다운받을 상품을 한개이상 선택하셔야 합니다.');//'삭제하실 제품을 한개이상 선택하셔야 합니다'
		return;
	}else{
		if(confirm('선택하신 상품을 다운받으시겠습니까?')){//'선택하신 상품을 정말로 삭제하시겠습니까? 삭제하시면 상품과 관련된 모든 데이타가 삭제되게 됩니다.'
			frm.submit();
		}
	}
}


function batch_commissionChange(frm){	
	//var listprice = filterNum(frm.listprice.value);

	//수수료 정보 불러오는 부분 수정 2014-04-07 이학봉 (입점업체를 선택햇을경우 입점업체에 해당되는 정책을 못불러옴)
	var company_id = $('#company_id').val();
	var pid = $('input[name=id]').val();
	var one_commission_use = $('input[name=batch_one_commission]:checked').val();
	
	if(eval("frm.batch_one_commission[0].checked")){
		frm.commission.value = frm.commission.getAttribute("company_commission");// getAttribute 사용 kbk
		frm.wholesale_commission.value = frm.wholesale_commission.getAttribute("whole_company_commission");// getAttribute 사용 kbk
		getSellerSetup(company_id,one_commission_use,pid);	//014-04-07 이학봉 추가
		$('tr #account_info_div').css('display','none');
	}else{
		
		frm.commission.value = frm.commission.getAttribute("goods_commission");// getAttribute 사용 kbk
		frm.wholesale_commission.value = frm.wholesale_commission.getAttribute("whole_goods_commission");// getAttribute 사용 kbk
		getSellerSetup(company_id,one_commission_use,pid);	//014-04-07 이학봉 추가
		$('tr #account_info_div').css('display','');
	}
}



