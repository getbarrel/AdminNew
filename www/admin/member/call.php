
<HTML>
<TITLE>:: CALLRABI CTI 2.0 ::</TITLE>

<STYLE>

#BTN{
	width:50px;
	height:35px;
	cursor:pointer;
	font-size:8pt;
}

#TXT{
	border:1px solid #AAAAAA;
	font-size:20pt;
	text-decoration:bold;
}

#TITLE{
	font-size:20pt;
	text-decoration:bold;
}

</STYLE>


<OBJECT id="CALLRABI" codebase="CallrabiK.cab#version=1,0,77,0" width="0" height="0" classid="clsid:2BDC3DA8-DBC2-44AE-AE5E-C576993A2A64" type="application/x-oleobject"></OBJECT>
<SCRIPT language="javascript" for="CALLRABI" event="OnAGLogon(value)">document.SN.LOGON.value=value;</SCRIPT> <!-- 로그인 이벤트 | value = 0: 접속종료 / 1:접속성공 / 2:중복내선로그인 -->
<SCRIPT language="javascript" for="CALLRABI" event="OnRingDetect(val1,val2)">RING(val1,val2);</SCRIPT> <!-- 다이얼 이벤트 | val1 = 전화번호/인입대표번호/IVR입력번호 / val2= 1:수신 2:발신-->
<SCRIPT language="javascript" for="CALLRABI" event="OnBridge(val1,val2)">ANSWER(val1,val2);</SCRIPT> <!-- 호연결(전화연결됨/보류됨) 이벤트 | va1=전화번호 / val2= 1:수신 2:발신 -->
<SCRIPT language="javascript" for="CALLRABI" event="OnAGStateChange(value)">document.SN.STATE.value=value;</SCRIPT> <!-- 상태이벤트(해당이벤트는 참조용)-->
<SCRIPT language="javascript" for="CALLRABI" event="OnPhoneStateChange(value)">document.SN.PHONESTATE.value=value;</SCRIPT> <!-- 전화상태이벤트 0:Idle, 1:Ring, 2:통화중, 3:out중, 6:감청-->
<SCRIPT language="javascript" for="CALLRABI" event="OnHangup(val1,val2)">EXIT(val1,val2);</SCRIPT> <!-- 통화 종료 이벤트 | val1 : 종료된통화 정보(전화번호 정보) / val2 : 종료코드 -->

<SCRIPT>

function LOGIN(){ // CTI 로그온
	var IP = document.SN.IPADDR.value; //교환기 아이피
	var ID = document.SN.ACODE.value; //내선번호
	if(ID==""){
		document.SN.ACODE.focus();
		return false;
	}
	CALLRABI.DebugMode=true; //디버그모드 이벤트로그경로 C:/KLCns/Callrabi/
	CALLRABI.AGLogON(IP,ID,"01,02,03"); //로그인 (아이피,내선,그룹명 (','그룹구분자))

}

function LOGOFF(){ // CTI 로그오프 (로그오프시 자동 수신거부)
	CALLRABI.AGLogOFF();
}

function RING(VALUE1,VALUE2){ // 링울림 이벤트 발생시 실행 함수
	document.SN.RING1.value=VALUE1;
	document.SN.RING2.value=VALUE2;
}

function ANSWER(VALUE1,VALUE2){ // 연결 이벤트 발생시 실행 함수
	document.SN.ANS1.value=VALUE1;
	document.SN.ANS2.value=VALUE2;
}

function EXIT(VALUE1,VALUE2){ // 통화종료 이벤트 발생시 실행 함수 (채널종료)
	document.SN.EXIT1.value=VALUE1;
	document.SN.EXIT2.value=VALUE2;
	if((VALUE2==0)||(VALUE2==16)){
	 	document.SN.RING1.value="";
		document.SN.RING2.value="";
		document.SN.ANS1.value="";
		document.SN.ANS2.value="";
		document.SN.SEEK1.value="";
	}
}

function CLEAR(){
	document.SN.RING1.value="";
	document.SN.RING2.value="";
	document.SN.ANS1.value="";
	document.SN.ANS2.value="";
	document.SN.EXIT1.value="";
	document.SN.EXIT2.value="";
	document.SN.SEEK1.value="";
}

function READY(){ // [전화대기]
	var RETURN = CALLRABI.QueueReady(""); // 자리비움 해제
	if(RETURN <= 0){ 	// 리턴값이 0 이하값 이면 전송실패
		alert("명령 전송에 오류가 발생하였습니다.");
		return false;
	}
}

function PAUSE(){ // [자리비움]
	var RETURN = CALLRABI.QueuePause(""); // 자리비움 적용
	if(RETURN <= 0){ 	// 리턴값이 0 이하값 이면 전송실패
		alert("명령 전송에 오류가 발생하였습니다.");
		return false;
	}	
}

function CALLACK(){ // [전화받기]
	CALLRABI.ACKCall("autoanswer");	// 전화 자동받기 명령
}

function PICKUP(){ // [당겨받기]
	CALLRABI.PickupCall("pickupcall");	// 전화 당겨받기 명령
}

function OUTCALL(){ // [전화걸기] 누름
	var NUM = document.all.TELNUM.value; //발신번호 읽어옴
	if(NUM!=""){ // 내선번호와 전화번호 확인 후 함수실행 (해당단말기에 [수신거부] 버튼 눌려있으면 동작안함)
		CALLRABI.MakeCall(NUM,"");
	}
}

function TRANSFER(){ // [돌려주기] 누름
	var CODE=document.SN.CODENUM.value;
	CALLRABI.TransferCall(CODE,"transfercall",1);
}

function CANCEL(){
	var CODE=document.SN.CODENUM.value;
	CALLRABI.TransferCancel(CODE);
}

function HANGUP(){  // [전화끊기] 누름
	CALLRABI.HangupCall(); // 인입벨이 울릴때 Hangup 액션을 주면 호가 전환됨
}

function SEARCH(){ // [번호검출] 누름
	var VAL = CALLRABI.GetCIDData(); //저장되어있는 전화번호정보를 검출 (당겨받았을시 or 돌려받았을시 사용)
	document.SN.SEEK1.value=VAL;
}

</SCRIPT>

<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#F0EFE8">

<FORM name="SN" method="post" action="">
<INPUT type="hidden" name="IPADDR" value="124.139.171.85">

<TABLE>
	<TR>
		<TD>
			<TABLE style="margin-left:10px">
				<TR valign="bottom">
					<TD>
						<SPAN id="TITLE" style="color:#3684B2">:: 내선 로그온 ::</SPAN>
					</TD>
				</TR>
				<TR valign="bottom">
					<TD>
						<SPAN id="TITLE">내선번호</SPAN>
						<INPUT id="TXT" type="text" name="ACODE" style="width:80px;" maxlength="4">
						<INPUT id="BTN" type="button" value="로그온" onclick="LOGIN()">
						<INPUT id="BTN" type="button" value="로그오프" onclick="LOGOFF()">
					</TD>
				</TR>
			</TABLE>
		</TD>
	</TR>
	<TR>
		<TD>

			<TABLE style="margin-left:10px;margin-top:20px">
				<TR valign="bottom">
					<TD>
						<SPAN id="TITLE" style="color:#3684B2">:: 이벤트정보 ::</SPAN>
					</TD>
				</TR>
				<TR>
					<TD>
						<SPAN id="TITLE">접속 이벤트</SPAN>
						<INPUT id="TXT" type="text" name="LOGON" style="width:80px;" maxlength="4">
					</TD>
				</TR>
				<TR>
					<TD>
						<SPAN id="TITLE">상태 이벤트</SPAN>
						<INPUT id="TXT" type="text" name="STATE" style="width:80px;" maxlength="4">
					</TD>
				</TR>
				<TR>
					<TD>
						<SPAN id="TITLE">전화상태 이벤트</SPAN>
						<INPUT id="TXT" type="text" name="PHONESTATE" style="width:80px;" maxlength="4">
					</TD>
				</TR>
				<TR>
					<TD>
						<SPAN id="TITLE">연결 이벤트</SPAN>
						<INPUT id="TXT" type="text" name="RING1" style="width:350px;" readonly>
						<INPUT id="TXT" type="text" name="RING2" style="width:50px;" readonly>
					</TD>
				</TR>
				<TR>
					<TD>
						<SPAN id="TITLE">통화 이벤트</SPAN>
						<INPUT id="TXT" type="text" name="ANS1" style="width:350px;" readonly>
						<INPUT id="TXT" type="text" name="ANS2" style="width:50px;" readonly>
					</TD>
				</TR>
				<TR>
					<TD>
						<SPAN id="TITLE">검출 이벤트</SPAN>
						<INPUT id="TXT" type="text" name="SEEK1" style="width:350px;" readonly>
					</TD>
				</TR>
				<TR>
					<TD>
						<SPAN id="TITLE">종료 이벤트</SPAN>
						<INPUT id="TXT" type="text" name="EXIT1" style="width:350px;" readonly>
						<INPUT id="TXT" type="text" name="EXIT2" style="width:50px;" readonly>
					</TD>
				</TR>
				<TR>
					<TD>
						<INPUT type="button" value="이벤트지우기" onclick="CLEAR()" />
					</TD>
				</TR>
			</TABLE>

		</TD>
	</TR>
	<TR>
		<TD>
			<TABLE style="margin-left:10px;margin-top:10px">
				<TR valign="bottom">
					<TD colspan="4">
						<SPAN id="TITLE" style="color:#3684B2">:: 기능 버튼 ::</SPAN>
					</TD>
				</TR>
				<TR valign="bottom">
					<TD width="140">
						<INPUT id="BTN" type="button" value="수신가능" onclick="READY()">
						<INPUT id="BTN" type="button" value="수신거부" onclick="PAUSE()">
					</TD>
					<TD width="200">
						<INPUT id="BTN" type="button" value="전화받기" onclick="CALLACK()">
						<INPUT id="BTN" type="button" value="당겨받기" onclick="PICKUP()">
						<INPUT id="BTN" type="button" value="번호검출" onclick="SEARCH()">
					</TD>
					<TD width="130">
						내선
						<INPUT id="TXT" type="text" name="CODENUM" style="width:60px;" maxlength="3">
					</TD>
					<TD width="140">
						<INPUT id="BTN" type="button" value="돌려주기" onclick="TRANSFER()">
						<INPUT id="BTN" type="button" value="취소" onclick="CANCEL()">
					</TD>

					<TD width="100">
						<INPUT id="BTN" type="button" value="전화끊기" onclick="HANGUP()">
					</TD>
				</TR>
				<TR valign="bottom" height="70">
					<TD colspan="3">
						<SPAN id="TITLE">전화걸기</SPAN>
						<INPUT id="TXT" type="text" name="TELNUM" style="width:180px;" maxlength="15">
						<INPUT id="BTN" type="button" value="전화걸기" onclick="OUTCALL()">
					</TD>
				</TR>
			</TABLE>
		</TD>
	</TR>
</TABLE>


</FORM>
</BODY>

</HTML>