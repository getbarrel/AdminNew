
function LOGIN(){ // CTI 로그온
	var IP = '124.139.171.85'; //교환기 아이피
	var ID = '1001'; //내선번호
	//var IP = document.SN.IPADDR.value; //교환기 아이피
	//var ID = document.SN.ACODE.value; //내선번호
	CALLRABI.WebSiteCall("http://"+IP+":8070/ocx/state.php?STATE=8&ID="+ID); //STATE=8 로그인
	CALLRABI.AGLogON(IP,ID,"01,02,03"); //로그인 (아이피,내선,그룹명)
	//READY();
	//로그인시 후처리로 시작
	PAUSE(0);
	document.SN.STATE_PA[1].selected = true;
}

function LOGOFF(){ // CTI 로그오프
	CALLRABI.AGLogOFF();
	CALLCHECK(9);
	top.location.href="../page/logout.php";
}

function OVER(){ //로그인 중복
	alert("로그인이 중복되었습니다.");
	top.location.href="../../";
}

function RING(VALUE1,VALUE2){ // 링울림 이벤트 발생시 실행 함수

	if(VALUE2 == '1'){
		var VAL = VALUE1.split("/");
		var tel = VAL[0];
		//팝업창 GET변수로 인입 전화번호 넘김
		$('#CallPopup').attr("href","member_cti_pop.php?tel="+tel);
		//facebox로 팝업창 실행
		$('#CallPopup').click();
	}
	if(SPY == "1"){
		STATE(3);
		CALLCHECK(3);
		return false;
	}
	STATE(2);
	CALLCHECK(2);

}

function ANSWER(VALUE1,VALUE2){ // 연결 이벤트 발생시 실행 함수(CID/DNIS/KEY)
	var VAL = VALUE1.split("/");
	
	STATE(3);
	CALLCHECK(3);
}

function EXIT(VALUE1,VALUE2){ // 통화종료 이벤트 발생시 실행 함수 (채널종료)
		alert('통화종료됨');
		//통화종료시 후처리로 상태변경
		STATE(0);
		PAUSE(0);	
}


function READY(){ // [전화대기]
	var RETURN = CALLRABI.QueueReady(""); // 자리비움 해제
	if(RETURN <= 0){ 	// 리턴값이 0 이하값 이면 전송실패
		alert("명령 전송에 오류가 발생하였습니다.");
		return false;
	}
	document.SN.STATE_PA[0].selected = true;
	STATE(1);
	CALLCHECK(1);

}

function PAUSE(VAL){ // [자리비움]
	var RETURN = CALLRABI.QueuePause(""); // 자리비움 적용
	if(RETURN <= 0){ 	// 리턴값이 0 이하값 이면 전송실패
		alert("명령 전송에 오류가 발생하였습니다.");
		return false;
	}
	if(VAL=="0")document.SN.STATE_PA[1].selected = true;
	//document.SN.STATUS.value = "수신거부";
	STATE(VAL);
	CALLCHECK(VAL);
}

function CALLACK(){ // [전화받기]
	$('#callok').hide();
	$('#nomembercrm').show();
	CALLRABI.ACKCall("autoanswer");	// 전화 자동받기 명령
}

function PICKUP(){ // [당겨받기]
	CALLRABI.PickupCall("pickupcall");	// 전화 당겨받기 명령
}

function OUTCALL(){ // [전화걸기] 누름
	var NUM = document.SN.TELNUM.value; //발신번호 읽어옴
	alert(NUM);
	if(NUM!=""){ // 내선번호와 전화번호 확인 후 함수실행 (해당단말기에 [수신거부] 버튼 눌려있으면 동작안함)
		CALLRABI.MakeCall(NUM,"");
	}
}

function StateView(){			
	// 상태 체크
	var STATUS = document.getElementById("STATUS").value
	
	if(STATUS == "통 화 중"){
		var tpopup;
		tpopup = window.open('../popup/stateView.php', 'stateView', 'top=0, left=0, toolbar=0, directories=0, status=0, menubar=0, scrollbars=0, resizable=0, width=370, height=370');	
		tpopup.focus();
	}
	else{
		alert("현재 통화중 상태가 아닙니다.");
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
	var CODE=document.SN.TCODE.value;	
	CALLRABI.TransferCall(CODE,"transfercall",1);
}

function SPYCALL(val){ // val:(1)감청 (2)코치 (3)개입
	document.SN.SPY.value = "1";
	var CODE=document.SN.SCODE.value;
	CALLRABI.SPYCall(CODE, val);	
}

function CANCEL(){
	var CODE=document.SN.TCODE.value;
	if(CODE) CALLRABI.TransferCancel(CODE);
	else{
		alert("돌려주기중인 내선번호가 없습니다");
		return false;
	}
}

function HANGUP(){  // [전화끊기] 누름
	CALLRABI.HangupCall(); // 인입벨이 울릴때 Hangup 액션을 주면 호가 전환됨	
	PAUSE(0); //통화종료 후 후처리 변경
	STATS(0);
}

function SEARCH(){ // [번호검출] 누름
	var VAL = CALLRABI.GetCIDData(); //저장되어있는 전화번호정보를 검출 (당겨받았을시 or 돌려받았을시 사용)
	if(VAL>0) document.SN.TELNUM.value = VAL;
	else VAL = document.SN.TELNUM.value;
	
	if(!VAL){
		alert("검출 할 번호가 존재하지 않습니다.");
		return false;
	}
	
	top.MAIN.CIDSEARCH(VAL);
}

function CALLCHECK(VAL){
	var IP = document.SN.IPADDR.value; //교환기 아이피
	var ID = document.SN.ACODE.value; //내선번호
	if(document.SN.STVAL.value != VAL) StartTimer();
	CALLRABI.WebSiteCall("http://"+IP+":8070/ocx/state.php?STATE="+VAL+"&ID="+ID);
	document.SN.STVAL.value = VAL;
}

function SENDCALL(NUM){
	document.SN.TELNUM.value=NUM;
	OUTCALL();
}

function STATE(VAL){
	var OBJ = document.getElementById("phone_status");
	if(VAL == "1"){
		OBJ.value = "1";
	}
	else if(VAL == "0"){
		OBJ.value = "0";
	}
	else if(VAL == "2"){
		OBJ.value = "2";
	}
	else if(VAL == "3"){
		OBJ.value = "3";
	}
	else if(VAL == "4"){
		OBJ.value = "4";
	}
	else if(VAL == "5"){
		OBJ.value = "5";
	}
	else if(VAL == "6"){
		OBJ.value = "6";
	}
	else if(VAL == "7"){
		OBJ.value = "7";
	}
}

function STATE_CHANGE(){
	var VAL = document.SN.STATE_PA.value;
	STATE(VAL);
	if(VAL == "1"){
		$('#TELNUM').val("");
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
 t = setInterval("TimeCheck()",1000); 
} 

function TimeCheck(){ 
 //var hour = Math.floor(i/(60*60)); // 시간 구하기. 시간은 필요없으면 빨간부분은 지우세요.. 
 var min = Math.floor(i/60); // 분 구하기..  Math.floor()함수는 소숫점 버림 함수입니다. 
 var sec = i%60; // 초구하기 
 //frm.timer.value=hour+"시간"+min+"분"+sec+"초"; // 텍스트폼에 출력. 

 min = (min < 10 ? "0" : "") + min;
 sec = (sec < 10 ? "0" : "") + sec;

 //frm.timer.value=min+":"+sec; // 텍스트폼에 출력. 
 $("#TIMER").text(min+":"+sec);
 i++; // 1증가 
} 

function StopTimer() { 
 i = 0; // 0으로 초기화 
 clearInterval(t); // setInterval()함수 정지시키기.. 
}