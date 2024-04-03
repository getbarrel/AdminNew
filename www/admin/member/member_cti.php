<?php
	/* CTI 2014-06-12 JBG  
	*  오류나 수정사항 많을수 있습니다.
	*  수정시 주석부탁 드립니다 ~_~
	*/ 

	//include($_SERVER['DOCUMENT_ROOT'].'/class/layout.class');
	include($_SERVER['DOCUMENT_ROOT'].'/admin/class/layout.class');
	$db = new Database;
	

$mstring .= "<meta http-equiv='X-UA-Compatible' content='IE=Edge'/>

<OBJECT id='CALLRABI' codebase='CallrabiK.cab#version=1,0,77,0' width='0' height='0' classid='clsid:2BDC3DA8-DBC2-44AE-AE5E-C576993A2A64' type='application/x-oleobject'></OBJECT>
<SCRIPT language='javascript' for='CALLRABI' event='OnAGLogon(value)'>
	if(value == 1){
		$('.member_cti_header').show();
		var crm_src = $('#member_crm').attr('src');
		$('#member_crm').attr('src',crm_src+'&recall=o');
	}
</SCRIPT> <!-- 로그인 이벤트 | value = 0: 접속종료 / 1:접속성공 / 2:중복내선로그인 -->
<SCRIPT language='javascript' for='CALLRABI' event='OnRingDetect(val1,val2)'>RING(val1,val2);</SCRIPT> <!-- 다이얼 이벤트 | val1 = 전화번호/인입대표번호/IVR입력번호 / val2= 1:수신 2:발신-->
<SCRIPT language='javascript' for='CALLRABI' event='OnBridge(val1,val2)'>ANSWER(val1,val2);</SCRIPT> <!-- 호연결(전화연결됨/보류됨) 이벤트 | va1=전화번호 / val2= 1:수신 2:발신 -->
<SCRIPT language='javascript' for='CALLRABI' event='OnAGStateChange(value)'>document.SN.STATE.value=value;</SCRIPT> <!-- 상태이벤트(해당이벤트는 참조용)-->
<SCRIPT language='javascript' for='CALLRABI' event='OnPhoneStateChange(value)'>document.SN.PHONESTATE.value=value;</SCRIPT> <!-- 전화상태이벤트 0:Idle, 1:Ring, 2:통화중, 3:out중, 6:감청-->
<SCRIPT language='javascript' for='CALLRABI' event='OnHangup(val1,val2)'>EXIT(val1,val2);</SCRIPT> <!-- 통화 종료 이벤트 | val1 : 종료된통화 정보(전화번호 정보) / val2 : 종료코드 -->
<script type='text/javascript' src='../js/jquery-1.8.3.js'></Script>
<script type='text/javascript' src='../js/facebox.js'></Script>
<script type='text/javascript' src='../js/admin.js'></Script>
<LINK href='../css/facebox.css' type='text/css' rel='stylesheet'>
<script type='text/javascript'>
$(function(){
	setProductModal();	
	//CALLRABI API 로그인호출함수
	LOGIN();
	
	setInterval(function(){

		$.ajax({
				url: '/admin/member/cti_request.php',
				type: 'post',
				dataType: 'html',
				data: ({mode: 'auto'}),
				success: function(result){
					request_num = result.split('|');
					request_num = request_num[0];
					$('.reuqest_num').html(request_num);
				}
		});

	},10000)
	
});

function setProductModal(){
	$('a[rel*=facebox]').facebox({
		loadingImage : '../images/loading.gif',
		closeImage   : '../images/close_btn01.gif'
	});
}
function LOGIN(){ // CTI 로그온
	var IP = document.SN.IPADDR.value; //교환기 아이피
	//var ID = '1001'; //내선번호
	var ID = document.SN.ACODE.value; //내선번호
	if(ID.length > 0){
		
		CALLRABI.WebSiteCall(\"http://\" + IP + \":8070/ocx/state.php?STATE=8&ID=\"+ ID + \"&requset=\"); //STATE=8 로그인
		
		CALLRABI.AGLogON(IP,ID,'00,01,02'); //로그인 (아이피,내선,그룹명)
		
		READY();
		
		//로그인시 후처리로 시작
		PAUSE(0);
		document.SN.STATE_PA[1].selected = true;
	}
}

function LOGOFF(){ // CTI 로그오프
	CALLRABI.AGLogOFF();
	CALLCHECK(9);
	top.location.href='../page/logout.php';
}

function OVER(){ //로그인 중복
	alert('로그인이 중복되었습니다.');
	top.location.href='../../';
}

function RING(VALUE1,VALUE2){ // 링울림 이벤트 발생시 실행 함수
	if(VALUE2 == '1'){
		var VAL = VALUE1.split('/');
		var tel = VAL[0];
		var tel_type = VAL[1];
		//팝업창 GET변수로 인입 전화번호 넘김
		$('#CallPopup').attr(\"href\",\"member_cti_pop.php?tel=\" + tel + \"&tel_type=\" + tel_type + \"&request=\");
		//facebox로 팝업창 실행
		$('#CallPopup')[0].click();
	}
	/*
	if(SPY == '1'){
		STATE(3);
		CALLCHECK(3);
		return false;
	}*/
	STATE(2);
	CALLCHECK(2);

}

function ANSWER(VALUE1,VALUE2){ // 연결 이벤트 발생시 실행 함수(CID/DNIS/KEY)
	var VAL = VALUE1.split('/');
	STATE(3);
	CALLCHECK(3);
}

function EXIT(VALUE1,VALUE2){ // 통화종료 이벤트 발생시 실행 함수 (채널종료)
		$('#facebox .popup .close')[0].click();
		//통화종료시 후처리로 상태변경
		STATE(0);
		PAUSE(0);	
}


function READY(){ // [전화대기]
	var RETURN = CALLRABI.QueueReady(''); // 자리비움 해제
	if(RETURN <= 0){ 	// 리턴값이 0 이하값 이면 전송실패
		alert('명령 전송에 오류가 발생하였습니다.');
		return false;
	}
	document.SN.STATE_PA[0].selected = true;
	STATE(1);
	CALLCHECK(1);

}

function PAUSE(VAL){ // [자리비움]
	var RETURN = CALLRABI.QueuePause(''); // 자리비움 적용
	if(RETURN <= 0){ 	// 리턴값이 0 이하값 이면 전송실패
		alert('명령 전송에 오류가 발생하였습니다.');
		return false;
	}
	if(VAL=='0')document.SN.STATE_PA[1].selected = true;
	//document.SN.STATUS.value = '수신거부';
	STATE(VAL);
	CALLCHECK(VAL);
}

function CALLACK(){ // [전화받기]
	$('#callok').hide();
	$('#nomembercrm').show();
	CALLRABI.ACKCall('autoanswer');	// 전화 자동받기 명령
}

function PICKUP(){ // [당겨받기]
	CALLRABI.PickupCall('pickupcall');	// 전화 당겨받기 명령
}

function OUTCALL(){ // [전화걸기] 누름
	var NUM = document.SN.TELNUM.value; //발신번호 읽어옴
	if(NUM!=''){ // 내선번호와 전화번호 확인 후 함수실행 (해당단말기에 [수신거부] 버튼 눌려있으면 동작안함)
		CALLRABI.MakeCall(NUM,'');
	}
}

function StateView(){		
	// 상태 체크
	var STATUS = document.getElementById('STATUS').value;
	if(STATUS == '통 화 중'){
		var tpopup;
		tpopup = window.open('../popup/stateView.php', 'stateView', 'top=0, left=0, toolbar=0, directories=0, status=0, menubar=0, scrollbars=0, resizable=0, width=370, height=370');	
		tpopup.focus();
	}else{
		alert('현재 통화중 상태가 아닙니다.');
		return false;
	}
}	

function spyView(){
	var spopup;
	spopup = window.open('../popup/spyView.php', 'spyView', 'top=0, left=0, toolbar=0, directories=0, status=0, menubar=0, scrollbars=0, resizable=0, width=370, height=370');	
	spopup.focus();
	//spopup.LOADDING();
}	

function TRANSFER(){ // [돌려주기] 누름
	var CODE=document.SN.TELNUM.value;	
	CALLRABI.TransferCall(CODE,'transfercall',1);
}

function SPYCALL(val){ // val:(1)감청 (2)코치 (3)개입
	document.SN.SPY.value = '1';
	var CODE=document.SN.SCODE.value;
	CALLRABI.SPYCall(CODE, val);	
}

function CANCEL(){
	var CODE=document.SN.TELNUM.value;
	if(CODE) CALLRABI.TransferCancel(CODE);
	else{
		alert('돌려주기중인 내선번호가 없습니다');
		return false;
	}
}

function HANGUP(){  // [전화끊기] 누름
	CALLRABI.HangupCall(); // 인입벨이 울릴때 Hangup 액션을 주면 호가 전환됨	
	document.SN.STATE_PA[2].disabled = true;
	PAUSE(0); //통화종료 후 후처리 변경
	STATS(0);
}

function SEARCH(){ // [번호검출] 누름
	var VAL = CALLRABI.GetCIDData(); //저장되어있는 전화번호정보를 검출 (당겨받았을시 or 돌려받았을시 사용)
	if(VAL>0) document.SN.TELNUM.value = VAL;
	else VAL = document.SN.TELNUM.value;
	
	if(!VAL){
		alert('검출 할 번호가 존재하지 않습니다.');
		return false;
	}
	
	top.MAIN.CIDSEARCH(VAL);
}

function CALLCHECK(VAL){
	var IP = document.SN.IPADDR.value; //교환기 아이피
	var ID = document.SN.ACODE.value; //내선번호
	if(document.SN.STVAL != 'undefined'){
		if(document.SN.STVAL.value != VAL) {
			StartTimer();
		}
	}
	CALLRABI.WebSiteCall(\"http://\" + IP + \":8070/ocx/state.php?STATE=\" + VAL + \"&ID=\" + ID + \"&request=\");
	document.SN.STVAL.value = VAL;
}

function SENDCALL(NUM){
	document.SN.TELNUM.value=NUM;
	OUTCALL();
}

function STATE(VAL){
	$('#phone_status').val(VAL);
}

function STATE_CHANGE(){
	var VAL = document.SN.STATE_PA.value;
	STATE(VAL);
	if(VAL == '1'){
		$('input[name=\"TELNUM\"]').val('');
		READY();	
	}else{ 
		PAUSE(VAL);
	}
}


//////////////////////시간체크//////////////////////////

var t; 
var i = 0; // 전역변수 i에 0 대입..  

function StartTimer(){ 
 StopTimer();
 t = setInterval('TimeCheck()',1000); 
} 

function TimeCheck(){ 
 //var hour = Math.floor(i/(60*60)); // 시간 구하기. 시간은 필요없으면 빨간부분은 지우세요.. 
 var min = Math.floor(i/60); // 분 구하기..  Math.floor()함수는 소숫점 버림 함수입니다. 
 var sec = i%60; // 초구하기 
 //frm.timer.value=hour+'시간'+min+'분'+sec+'초'; // 텍스트폼에 출력. 

 min = (min < 10 ? '0' : '') + min;
 sec = (sec < 10 ? '0' : '') + sec;

 //frm.timer.value=min+':'+sec; // 텍스트폼에 출력. 
 $('#TIMER').text(min+':'+sec);
 i++; // 1증가 
} 

function StopTimer() { 
 i = 0; // 0으로 초기화 
 clearInterval(t); // setInterval()함수 정지시키기.. 
}
	
</script>
<style type='text/css'>
	body {margin:0px; padding:0px; overflow:hidden; height:100%; width:1300px; position:absolute; top:0px; left:0px;}
	body,p,h1,h2,h3,h4,h5,h6,ul,ol,li,dl,dt,dd,table,th,td,form,fieldset,legend,input,textarea,button{margin:0;padding:0;font-size:12px;font-family:Dotum,Arial;color:#666;}
	h1,h2,h3,h4,h5,h6	{font-size:12px;}
	img,fieldset{border:0px;}
	ul,li,ol{list-style:none;}
	a{text-decoration:none;} a:link {color:#181818;} a:hover {text-decoration:underline;color:#585858;} a:visited {color:#181818;}
	em,address{font-style:normal}
	.nobr{text-overflow:ellipsis; overflow:hidden;white-space:nowrap;}
	table	{ border-collapse:collapse;table-layout:fixed;}
	td,th	{padding:0;margin:0;}
	input,label	{vertical-align:middle;border:0;}
	label {cursor:pointer;}

	.member_cti_wrap {width:1300px; min-height:800px; height:800px; background:url('../images/member_cti_wrap _background.png') 0 0 repeat-y;}
	.member_cti_wrap2 {width:1300px; height:800px; background:url('../images/member_cti_wrap _background.png') 0 0 repeat-y; position:absolute; top:0px;}
	
	.member_cti_header {width:100%; margin-bottom:4px; margin-left:10px;height:40px; }
	.member_cti_header ul { display:inline-block;}
	.member_cti_header ul:after {content:''; display:block; clear:both;}
	.member_cti_header ul li {float:left; }
	.member_cti_height {line-height:0px; font-size:0px; cursor:pointer;}
	.member_cti_backgroundnone {background-image:none !important;}
	.member_cti_background {background:url('../images/cti_headertop_background.png') 0 0 no-repeat; width:994px; height:40px;}
	.member_cti_header2 {width:100%; text-align:center; height:42px;}
	.member_cti_header2 ul {display:inline-block;}
	.member_cti_header2 ul:after {content:''; display:block; clear:both;}
	.member_cti_header2 ul li {float:left;  height:42px;}
	.member_cti_right {background:url('../images/cti_headertop2_background.png') right 0 no-repeat !important; min-width:10px;}
	.member_cti_text {line-height:42px; color:#fff; background:#ff4c3e url('../images/cti_headertop2_gap.png') right 14px no-repeat;}
	.member_cti_text a {color:#fff !important; text-decoration:none;}
	.member_cti_text em {font-weight:bold; color:#f6ff6e;}
	.member_cti_text span.member_grave {background:url('../images/.png') 0 0 no-repeat; padding-left:23px;}
	.member_cti_text span.member_coupon {background:url('../images/memer_icon_red.png') right 13px no-repeat; padding-right:21px; display:inline-block; height:40px; cursor:pointer; position:relative;}
	.member_cti_textwidth1 {padding:0px 13px 0 20px;}
	.member_cti_textwidth2 {padding:0px 13px;}
	.member_cti_ol {padding-left:99px;}
	.member_cti_ol li {padding-left:5px;color:#fff; font-weight:bold; background:url('../images/cti_headertop2_gap2.png') right 15px no-repeat; padding-right:11px;}
	.member_cti_ol ol {float:right;}
	.member_cti_ol li a {color:#fff;}
	.point_cti_ol {color:#f6ff6e !important;}
	.member_cti_ol .member_cti_backgroundnone img {margin-top:10px;}
	.member_cti_contant {margin:0px 13px 0 11px; background:#fff;}
	.member_cti_contant:after {content:''; display:block; clear:both;}
	.cti_contant_type1 {width:810px !important; min-height:20px; background:#fff; position:relative; float:left;}
	.cti_contant_topback {background:url('../images/content_drow_sp.png') 0 0 repeat-x; opacity:0.4;filter:alpha(opacity=40);position:absolute; top:0px; width:100%; min-height:2px;}
	.cti_contant_menu {float:left; width:168px; height:694px; background:#fafafa;border-right:1px solid #cccccc;}
	.member_cti_contant_bottom {width:1260px; min-height:12px; margin-left:11px; background:url('../images/member_cti_contant_bottom.png') 0 0 no-repeat;}
	.cti_contant_menu ul {margin-top:12px;}
	.cti_contant_menu ul li {height:33px; border-bottom:1px dashed #cccccc; line-height:33px; margin-left:4px;}
	.cti_contant_menu ul li span {margin-left:22px;}
	.cti_contant_menu ul li a {text-decoration:none;}
	.cti_contant_menu ul li a strong {color:#fb5240;}
	
	.cti_buttom_wrap {position:absolute; left:27px; bottom:0px;}
	.cti_buttom_wrap ul:after {content:''; display:block; clear:both;}
	.cti_buttom_wrap ul li {float:left; margin-right:10px; line-height:0px; font-size:0px;}
	.cti_fixed_wrap {height:38px; border-bottom:1px solid #cccccc; padding:11px 0 0 9px;}
	.cti_fixed_title_ul1 {margin:0px 11px 0 9px;}
	.cti_fixed_title_ul1:after {content:''; display:block;clear:both;}
	.cti_fixed_title_ul1 li {float:left;}
	.cti_fixed_title_li {float:right !important; font-weight:bold; color:#363636;}
	.cti_fixed_title_li label {margin-right:3px;}
	.cti_fixed_title_ul2:after {content:''; display:block; clear:both;}
	.cti_fixed_title_li2 {padding:11px 0 7px;}

	.cti_fixed_title_ul2 {margin:0px 11px 10px 9px;}
	.cti_fixed_title_ul2 li {float:left;}
	.content_coll_wrap {margin:0px 11px 10px 9px; border:1px solid #ccc; background:#fff; width:400px;}
	.content_coll_wrap dt{position:relative; padding:10px; border-bottom:1px solid #e5e5e5;}
	.content_coll_wrap dt:after {content:''; display:block;clear:both;}
	.search_coll_wrap {margin-right:5px;border:1px solid #cccccc; background:#fff; float:left; width:173px; height:26px;}
	.search_coll_wrap img {position:relative;top:4px;cursor:pointer;}
	.input_backgorund {background:none !important;}
	.search_scro_wrap {width:148px; height:26px;border:1px solid #ccc; background:#fff; float:left;}
	.search_coll_close {position:absolute; right:11px; top:16px; cursor:pointer; display:none;}
	.coll_division:after {content:''; display:block;clear:both;}
	.coll_division_div1 {width:98px; background:#fff; border:1px solid #cccccc; height:26px;  margin:10px 0; margin-right:5px;margin-left:10px; float:left; display:inline;} 
	.coll_division_div2 {width:98px; background:#fff; border:1px solid #cccccc; height:26px; margin:10px 0;  margin-right:5px; float:left; display:inline;} 
	.coll_division_div3 {width:168px; height:26px; border:1px solid #ccc;  float:left; margin:10px 0; display:inline;}
	.coll_division {border-bottom:1px solid #e5e5e5;}
	.coll_content_creation { padding:10px; border-bottom:1px solid #e5e5e5;}
	.coll_content_creation div {padding-top:10px;}
	.coll_content_creation div:after {content:''; display:block; clear:both;}
	.coll_content_creation div ul {float:left;}
	.coll_content_creation div ul li {float:left; line-height:0;}
	.coll_content_creation div ul li label {font-weight:bold; margin-left:4px; color:#363636;}
	.coll_content_creation_li1 {width:139px;}
	.coll_content_creation_li2 {width:119px;}
	.coll_content_date {border-bottom:1px solid #e5e5e5;}
	.coll_content_date:after {content:''; display:block; clear:both;}
	.coll_content_div_date1 {text-align:center; line-height:26px;border:1px solid #ccc; margin:10px 5px 10px 10px; width:88px; height:26px; background:url('../images/date_background_top.png') 0 0 no-repeat; font-weight:bold; color:#363636; float:left;  display:inline; color:#363636;}
	.coll_content_div_date2 {width:63px; border:1px solid #ccc; height:26px; background:#fff; margin:10px 0; float:left; display:inline; font-weight:bold; margin-right:5px;}
	.coll_content_div_date2 select {font-weight:bold; color:#363636;}
	.coll_content_div_date3 {width:143px; height:26; position:relative; border:1px solid #ccc; float:left; background:#fff; margin:10px 0; display:inline;}
	.coll_content_div_date3 img {position:absolute; top:-1px; right:-1px;}
	.coll_content_save {text-align:center;}
	.coll_content_save img {padding:10px 0;}
	.search_scro_wrap select {font-weight:bold; color:#363636;}
	.coll_division_div1 select {font-weight:bold; color:#363636;}
	.coll_division_div2 select {font-weight:bold; color:#363636;}

	.right_tap_menu {position:absolute; left:-39px; top:0px;}
	.right_tap_menu li {line-height:0px; font-size:0px; cursor:pointer; position:relative;}
	.right_tap_menu li.s_point_z {z-index:99;}
	.right_tap_menu_li {margin-top:-1px;}
	.cti_icon_wrap_toll {float:left; margin-left:60px; margin-top:5px;}
	.cti_icon_wrap1 {border:1px solid red; float:left; width:128px; height:26px; border:1px solid #303545;background:#303545; border-right:1px solid #181b23; border-bottom:1px solid #181b23;}
	.cti_icon_wrap1 select { color:#fff; background:#303545; }
	.cti_icon_wrap2 {width:208px; height:26px; border-top:1px solid #0c0c0c; border-bottom:1px solid #858585; border-right:1px solid #353535; background:#fff; float:left; margin-right:14px; display:inline;}
	.cti_icon_wrap3 {float:left; margin-right:52px; display:inline;}
	.cti_icon_wrap3 ul {float:left; margin-top:-2px; display:inline;}
	.cti_icon_wrap3 ul li {float:left;  margin-right:9px; line-height:0px; font-size:0px; cursor:pointer; display:inline;}
	.cti_icon_wrap4 {float:left; margin-right:34px;}
	.cti_icon_wrap4 dt {line-height:0px; font-size:0px;  float:left;}
	.cti_icon_wrap4 dd {float:left;}
	.cti_icon_wrap4 dd {font-size:14px; font-weight:bold; color:#ffd053; text-decoration:underline; margin:0px 9px 0 5px; margin-top:7px;}
	.cti_icon_wrap4 img {margin-top:7px;}
	.cti_icon_wrap5 {float:left; cursor:pointer;}

	.mouse_show_coupon {width:115px; min-height:20px; position:absolute; top:28px; left:64px; z-index:9999; display:none;}
	.mouse_show_top {height:10px !important; width:100%; line-height:10px; background:url('../images/mouse_po_top.png') 0 0 no-repeat;}
	.mouse_show_bottom {height:3px !important; width:100%; line-height:3px; background:url('../images/mouse_po_bottom.png') 0 0 no-repeat; }
	.mouse_show_middle {min-height:87px; width:113px; border-left:1px solid #303440; border-right:1px solid #303440; background:#fff;}
	.mouse_show_middle ol {margin-top:5px;}
	.mouse_show_middle ol li {float:none !important; line-height:16px; height:16px; text-align:left; margin-bottom:5px; margin-left:12px !important;}
	.mouse_show_middle ol li span {height:16px; display:block;}
	.mouse_show_middle ol li span a {color:#363636 !important;}
	.mouse_show_middle ol li em {color:#ff4c3e; font-weight:bold; text-decoration:underline;}
	.mouse_show_middle ol li em a {color:#ff4c3e !important; font-weight:bold; text-decoration:underline;}
	.mouse_show_middle_01 {background:url('../images/mouse_po_icon_01.png') 0 0 no-repeat; padding-left:21px;}
	.mouse_show_middle_02 {background:url('../images/mouse_po_icon_02.png') 0 0 no-repeat; padding-left:21px;}
	.mouse_show_middle_03 {background:url('../images/mouse_po_icon_03.png') 0 0 no-repeat; padding-left:21px;}
	/*2014-05-15 mbh*/
	.select_call_num{margin-right: 5px; border: 1px solid #cccccc; background: #fff; float: left; width: 148px; height: 26px; }
	#call_num{font-weight:bold; color:#353535; }
	.search_call_num{width: 243px; height: 26px; border: 1px solid #ccc; background: #fff; float: left; position:relative; top:0; left:0;}
	.search_call_num img{ position:absolute; top:4px; right:5px; cursor:pointer;}

	.cti_icon_wrap3 ul li {position:relative; top:0; left:0;}
	.cti_icon_wrap3 ul li div.top_hover{position:absolute; top:30px; left:-10px; display:none; }
	.cti_icon_wrap3 ul li:hover > div.top_hover{display:block; }


</style>";

	$sql = "SELECT 
				cti_num 
			FROM 
				common_member_detail
			WHERE code = '$admininfo[charger_ix]'
			";
	$db->query($sql);
	$db->fetch();
	$cti_num = $db->dt['cti_num'];
$mstring .= "
<FORM name='SN' method='post' action=''>
<INPUT type='hidden' name='IPADDR' value='183.111.154.13' />
<INPUT type='hidden' name='ACODE' value='$cti_num' />
<INPUT type='hidden' name='STATE' />
<INPUT type='hidden' name='STVAL' />
<INPUT type='hidden' name='PHONESTATE' />
<div class='member_cti_wrap2' >
	<a href='/admin/member/member_cti_pop.php' style='display:none;' id='CallPopup' rel='facebox'></a>	
	<iframe name='act' id='act' style='display:none;'></iframe>
	<iframe src='member_crm.php?mem_ix=".$code."&mode=".$mode."&code=".$code."&con_view=".$con_view."' name='member_crm' class='autoheight' id='member_crm' marginwidth='0' marginheight='0' width='1280px' height='800px' topmargin='0' scrolling='yes' class='the_iframe' frameborder='0'  style='' allowtransparency='true'></iframe>
</div>
</form>

<script type='text/javascript'>
<!--
	$(document).ready(function(e) {
		 $('.the_iframe').load(function() {
			  $(this).height($(this).contents().find('body')[0].scrollHeight+30+'px');
		 });
	});
//-->
</script>";

$Contents = $mstring;

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->OnloadFunction = '';
$P->strLeftMenu = member_menu();
//$P->Navigation = '회원관리 > CRM';
//$P->title = '전체회원';
//$P->NaviTitle =  'C/S 상담내역';
$P->strContents =  $Contents;
$P->layout_display = false;
//$P->view_type = 'personalization';
echo $P->PrintLayOut();