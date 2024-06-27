function ContentMode(cmode)
{
	if(cmode == "edit"){
		document.getElementById('edit_content').style.display ='block';
		document.getElementById('add_content').style.display ='none';
	}else{
		document.getElementById('edit_content').style.display ='none';
		document.getElementById('add_content').style.display ='block';
	}
}

function ContentSave(frm,vMode)
{
	//alert(frm);

	if (frm.cname.value.length < 1){
		alert(language_data['content_class.js']['A'][language]);
		//'수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}

	if(CheckFormValue(frm)){
		frm.mode.value = vMode;
		frm.submit();
	}
}

function thisContentSave(frm,vMode)
{
	//alert(frm);

	if (frm.cname.value.length < 1){
		alert('수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요');
		//'수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}
	
	if (frm.cname.value.length < 1){
		//alert(language_data['content_class.js']['B'][language]);
		alert("국문 분류명을 입력하시기 바랍니다.");
		//'추가 하시고자 하는 상품카테고리를 입력해 주세요'
		return false;
	}

	if(CheckFormValue(frm)){
		frm.mode.value = vMode;
		frm.submit();
	}


}

function SubContentSave(frm,vMode)
{
	if (frm.sub_cid.value == '003000000000000'){
		alert("배럴컨텐츠에 1 Depth 항목은 추가할 수 없습니다.");
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}

	/*if (frm.sub_cid.value == '001005000000000'){
		alert("배럴 인사이드에 2 Depth 항목은 추가할 수 없습니다.");
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}*/
	
	/*if (frm.sub_cid.value == '001001005000000'){
		alert("배럴 인사이드 > 기획전에 3 Depth 항목은 추가할 수 없습니다.");
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}*/

	/*if (frm.sub_cid.value == '001003001000000'){
		alert("배럴 인사이드 > 팀배럴에 3 Depth 항목은 추가할 수 없습니다.");
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}*/

	if (frm.sub_cid.value.length != 15){
		//alert(language_data['category.js']['B'][language]);
		alert("추가 하시고자 하는 분류를 선택하시기 바랍니다.");
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}

	if (frm.cid.value.length != 15){
		//alert(language_data['content_class.js']['B'][language]);
		alert("추가 하시고자 하는 분류를 선택하시기 바랍니다.");
		//'추가 하시고자 하는 상품카테고리를 선택해 주세요'
		return false;
	}

	if (frm.cname.value.length < 1){
		//alert(language_data['content_class.js']['B'][language]);
		alert("국문 분류명을 입력하시기 바랍니다.");
		//'추가 하시고자 하는 상품카테고리를 입력해 주세요'
		return false;
	}

	if (frm.sub_depth.value >= 5){
		//alert(language_data['content_class.js']['C'][language]);
		alert('분류구성은 4 Depth 까지만 가능합니다.');
		//'카테고리구성은 4단계까지만 가능합니다.'
		return false;
	}

	if(CheckFormValue(frm)){
		frm.mode.value = vMode;
		frm.submit();
	}
}

function setContent(cname, cid, depth, global_cname, b_preface, i_preface, u_preface, c_preface, content_link, content_link_yn, content_use, content_view, content_type, content_list_use)
{
	cname			= cname.replace("&quot;","\"");
	cname			= cname.replace("&#39;","'");

	global_cname	= global_cname.replace("&quot;","\"");
	global_cname	= global_cname.replace("&#39;","'");

	document.thisContentform.cname.value		= cname;
	document.thisContentform.global_cname.value = global_cname;

	document.thisContentform.cid.value			= cid;
	document.thisContentform.this_depth.value	= depth;
	document.thisContentform.c_preface.value	= c_preface;
	document.thisContentform.content_link.value	= content_link;

	if(b_preface == "Y"){
		$('#b_preface').attr('checked',true);
	}else if(b_preface == "N"){
		$('#b_preface').attr('checked',false);
	}

	if(i_preface == "Y"){
		$('#i_preface').attr('checked',true);
	}else if(i_preface == "N"){
		$('#i_preface').attr('checked',false);
	}

	if(u_preface == "Y"){
		$('#u_preface').attr('checked',true);
	}else if(u_preface == "N"){
		$('#u_preface').attr('checked',false);
	}

	if(content_link_yn == "Y"){
		$('#content_link_yn').attr('checked',true);
	}else if(content_link_yn == "N"){
		$('#content_link_yn').attr('checked',false);
	}

	if(content_use == "1"){	
		$('#content_use_1').attr('checked',true);
	}else if(content_use == "0"){
		$('#content_use_0').attr('checked',true);
	}

	if(content_list_use == "1"){
		$('#content_list_use_1').attr('checked',true);
	}else if(content_list_use == "0"){
		$('#content_list_use_0').attr('checked',true);
	}

	if(content_view == "1"){	
		$('#content_view_1').attr('checked',true);
	}else if(content_view == "0"){
		$('#content_view_0').attr('checked',true);
	}

	if(content_type == "1"){	
		$('#content_type_1').attr('checked',true);
	}else if(content_type == "2"){
		$('#content_type_2').attr('checked',true);
	}else if(content_type == "3"){
		$('#content_type_3').attr('checked',true);
	}else if(content_type == "4"){
		$('#content_type_4').attr('checked',true);
	}

	document.subContentform.cid.value = cid;
	document.subContentform.sub_depth.value = eval(depth+1);

	document.content_order.this_depth.value = depth;
	document.content_order.cid.value = cid;

	document.getElementById("calcufrm").src='calcurate.php?mode=&cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
}

function setContentList(cname, cid, depth, content_type){
	document.sForm.cid.value	= cid;
	document.sForm.depth.value	= depth;
	document.sForm.content_type.value	= content_type;

	if(document.sForm.cid.value == "001003000000000"){
		document.getElementById('titleName').innerHTML  = '선수명';
	}else{
		document.getElementById('titleName').innerHTML  = '제목';
	}

	

	/*
	document.getElementById("list_"+cid.substr(0,6)).style.display = '';
	if(document.sForm.before_cid.value != ""){
		document.getElementById("list_"+document.sForm.before_cid.value).style.display = 'none';
	}

	document.sForm.before_cid.value	= cid.substr(0,6);
	*/

	document.getElementById("calcufrm").src='calcurate.php?mode=list&cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
}

function order_up(frm){
	frm.mode.value = "up";
	if (frm.this_depth.value.length < 1){
		//alert(language_data['content_class.js']['E'][language]);
		alert("분류를 선택해주세요");
		//'상품카테고리를 선택해주세요'
		return false;
	}

	frm.submit();
}

function order_down(frm){
	//alert(frm.view);


	frm.mode.value = "down";
	if (frm.this_depth.value.length < 1){
		//alert(language_data['content_class.js']['E'][language]);
		alert("분류를 선택해주세요");
		//'상품카테고리를 선택해주세요'
		return false;
	}

	frm.submit();
}

function showTabContents(vid, tab_id){
	var area = new Array('edit_content','add_content');
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