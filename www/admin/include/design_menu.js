 
 var SMheightToOpen11464 = 115; 
 var SMspeed11464 = 1; 
 var SMstep11464 = 20; 
 var SMableToCloseSub11464 = false; 
 var SMobjOpen11464 = null; 
 var SMobj11464 = null; 
 var SMtimer11464 = null; 
 var SMopening11464 = false; 
 var SMisWorking11464 = false; 
 var SMtmpNeedOpen11464 = 0; 
 var SMtmpNeedClose11464 = 0; 
 var SMToOpen11464 = -1; 
 
 
function SMpoc11464(SMsubName,SMsubID) 
{ 
	//alert(SMsubID);
if(SMsubID == "SM1146411")
{
	SMheightToOpen11464 = 220; // 레이아웃디자인
}

if(SMsubID == "SM114641")
{
	SMheightToOpen11464 = 375; // 쇼핑몰 디자인
}


if(SMsubID == "SM11464243"){ // 이벤트 디자인
	SMheightToOpen11464 = 175;
}

if(SMsubID == "SM22464243"){ // 기타 디자인관리
	SMheightToOpen11464 = 175;
}

if(SMsubID == "SM1146487"){// 마이페이지
	SMheightToOpen11464 = 300;
}

if(SMsubID == "SM1146488"){// 견적센터
	SMheightToOpen11464 = 125;
}
if(SMsubID == "SM11464176"){ // 회사소개
	SMheightToOpen11464 = 275;
}
if(SMsubID == "SM11464177"){// 기타페이지
	//SMheightToOpen11464 = 150;
	SMheightToOpen11464 = 100;
}


if(SMsubID == "SM11464332"){
	SMheightToOpen11464 = 150;
}

/*
(document.getElementById("SM1146411" + "I")).src = "/manage/image/arrow_blue.gif";
(document.getElementById("SM114641" + "I")).src = "/manage/image/arrow_blue.gif";
(document.getElementById("SM1146487" + "I")).src = "/manage/image/arrow_blue.gif";
(document.getElementById("SM11464176" + "I")).src = "/manage/image/arrow_blue.gif";
(document.getElementById("SM11464177" + "I")).src = "/manage/image/arrow_blue.gif";
(document.getElementById("SM11464243" + "I")).src = "/manage/image/arrow_blue.gif";
*/
//(document.getElementById("SM11464332" + "I")).src = "/manage/image/arrow_blue.gif";




SMToOpen11464 = parseInt(SMheightToOpen11464); 

	if(SMisWorking11464 == false) 
	{ 
		SMobj11464 = document.getElementById(SMsubName); 
		if(SMinitiallyOpenSub11464 != "") 
		{ 
			SMobjOpen11464 = document.getElementById(SMinitiallyOpenSub11464); 
			SMinitiallyOpenSub11464 = ""; 
			
		} 
		if(SMobjOpen11464 != null) 
		{ 
			SMtmpNeedClose11464 = parseInt(SMobjOpen11464.style.height); 
		} 
		SMtimer11464 = window.setInterval(SMda11464, SMspeed11464); 
		//(document.getElementById(SMsubID + "I")).src = "/manage/image/arrow_blue1.gif";
	} 
} 

function SMpoc211464(SMsubName, toOpen) { SMToOpen11464 = parseInt(toOpen); if(SMisWorking11464 == false) { SMobj11464 = document.getElementById(SMsubName); if(SMinitiallyOpenSub11464 != "") { SMobjOpen11464 = document.getElementById(SMinitiallyOpenSub11464); SMinitiallyOpenSub11464 = ""; } if(SMobjOpen11464 != null) { SMtmpNeedClose11464 = parseInt(SMobjOpen11464.style.height); } SMtimer11464 = window.setInterval(SMda11464, SMspeed11464); } } function SMda11464() { if(SMobjOpen11464 == null) { SMoo11464(); } else if(SMobjOpen11464 == SMobj11464) { if(SMableToCloseSub11464 == true) { SMco11464(); } else { window.clearInterval(SMtimer11464); } } else { SMoo211464(); } } function SMoo11464() { SMisWorking11464 = true; SMobj11464.style.display = "block"; if(SMtmpNeedOpen11464 + SMstep11464 <= SMToOpen11464) { SMobj11464.style.height = SMtmpNeedOpen11464 + SMstep11464; SMtmpNeedOpen11464 = SMtmpNeedOpen11464 + SMstep11464; } else { window.clearInterval(SMtimer11464); SMobj11464.style.height = SMToOpen11464; SMobjOpen11464 = SMobj11464; SMtmpNeedOpen11464 = 0; SMisWorking11464 = false; SMToOpen11464 = -1; } } function SMco11464() { SMisWorking11464 = true; if(SMtmpNeedClose11464 - SMstep11464 < SMstep11464) { window.clearInterval(SMtimer11464); SMobjOpen11464.style.display = "none"; SMobjOpen11464.style.height = 1; SMobjOpen11464 = null; SMisWorking11464 = false; SMtmpNeedClose11464 = 0; } else { SMobjOpen11464.style.height = SMtmpNeedClose11464 - SMstep11464; } SMtmpNeedClose11464 = SMtmpNeedClose11464 - SMstep11464; } function SMoo211464() { SMisWorking11464 = true; SMobj11464.style.display = "block"; if(SMtmpNeedOpen11464 + SMstep11464 <= SMToOpen11464) { SMobj11464.style.height = SMtmpNeedOpen11464 + SMstep11464; SMtmpNeedOpen11464 = SMtmpNeedOpen11464 + SMstep11464; } if(SMtmpNeedClose11464 - SMstep11464 >= 1) { SMobjOpen11464.style.height = SMtmpNeedClose11464 - SMstep11464; SMtmpNeedClose11464 = SMtmpNeedClose11464 - SMstep11464; } else { SMobjOpen11464.style.display = "none"; if(SMtmpNeedOpen11464 + SMstep11464 > SMToOpen11464 && SMtmpNeedClose11464 - SMstep11464 < 1) { window.clearInterval(SMtimer11464); SMobj11464.style.height = SMToOpen11464; SMobjOpen11464.style.display = "none"; SMobjOpen11464.style.height = 1; SMobjOpen11464 = null; SMobjOpen11464 = SMobj11464; SMtmpNeedOpen11464 = 0; SMtmpNeedClose11464 = 0; SMisWorking11464 = false; SMToClose11464 = -1; } } } 
function SMcs11464(SMobj, SMstyle, SMimage) 
{ 
	if(SMstyle != ""){ 
		SMobj.className = SMstyle; 
	} 
	if(SMimage != ""){ 
		(document.getElementById(SMobj.id + "I")).src = SMimage;  
	} 
}




//
