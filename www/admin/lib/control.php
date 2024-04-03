<?
include("arcodian.class");


function AcodianControlMenu(){
?>
<STYLE type=text/css>
.SM_po11464 {
	font-size:11px;BORDER-TOP: #ffffff 1px solid;BORDER-left: #ffffff 1px solid; Border-right:1px solid #000000;Border-bottom:1px solid #000000; PADDING-LEFT: 8px; padding-top:8px;  WIDTH: 190px; CURSOR: hand; COLOR: #2343a1; POSITION: relative; TOP: 0px; BACKGROUND-COLOR: #6699cc; TEXT-DECORATION: none; HEIGHT:26px
}
.SM_p11464 {
	font-size:11px;BORDER-TOP: #ffffff 1px solid;BORDER-left: #ffffff 1px solid;Border-right:1px solid #000000;Border-bottom:1px solid #000000; PADDING-LEFT: 8px; padding-top:8px;  WIDTH: 190px; COLOR: #ffffff; POSITION: relative; TOP: 0px; BACKGROUND-COLOR: #6699cc; TEXT-DECORATION: none; HEIGHT:26px
}
.SM_co11464 {
	BORDER-TOP: #ffffff 1px solid;BORDER-left: #ffffff 1px solid;Border-right:1px solid #000000;Border-bottom:1px solid #000000; FONT: normal 11px verdana; WIDTH: 190px; CURSOR: hand; COLOR: #2343a1; BACKGROUND-COLOR: #FCBC5E; TEXT-DECORATION: none;HEIGHT:25px; padding-top:7px;
}
.SM_c11464 {
	BORDER-TOP: #ffffff 1px solid;BORDER-left: #ffffff 1px solid;Border-right:1px solid #000000;Border-bottom:1px solid #000000; FONT: normal 11px verdana; WIDTH: 190px; COLOR: #2343a1; BACKGROUND-COLOR: #C3CCDA; TEXT-DECORATION: none; HEIGHT:25px; padding-top:7px;
}
.SM_cb11464 {
	BACKGROUND-COLOR: white
}
.SM_ps11464 {
	BORDER-TOP: #ffffff 1px solid; PADDING-LEFT: 8px; FONT: bold 11px verdana; WIDTH: 190px;HEIGHT:30px; CURSOR: hand; COLOR: #2343a1; POSITION: relative; TOP: 0px; BACKGROUND-COLOR: #c4e2f2; TEXT-DECORATION: none; padding-top:7px;
}
.SM_cs11464 {
	BORDER-TOP: #ffffff 1px solid; FONT: 11px verdana; WIDTH: 190px; COLOR: #2343a1; BACKGROUND-COLOR: #f0f8fc; TEXT-DECORATION: none; padding-top:7px;
}
.SMEmptyDiv11464 {
	BORDER-TOP: medium none; OVERFLOW: hidden; WIDTH: 100%
}
</STYLE>

<SCRIPT language=javascript id=clientEventHandlersJS>
<!--
 var SMinitiallyOpenSub11464 = "<?=$SubID?>"; 
 var SMheightToOpen11464 = 125; 
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
if(SMsubID == "SM114641")
{
	SMheightToOpen11464 = 150;
}
if(SMsubID == "SM1146487"){
	SMheightToOpen11464 = 75;
}
if(SMsubID == "SM11464176"){
	SMheightToOpen11464 = 0;
}
if(SMsubID == "SM11464243"){
	//SMheightToOpen11464 = 150;
	SMheightToOpen11464 = 125;
}
if(SMsubID == "SM11464332"){
	SMheightToOpen11464 = 125;
}
(document.getElementById("SM114641" + "I")).src = "./basic/image/arrow_blue.gif";
(document.getElementById("SM1146487" + "I")).src = "./basic/image/arrow_blue.gif";
//(document.getElementById("SM11464176" + "I")).src = "./basic/image/arrow_blue.gif";
(document.getElementById("SM11464243" + "I")).src = "./basic/image/arrow_blue.gif";
(document.getElementById("SM11464332" + "I")).src = "./basic/image/arrow_blue.gif";




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
		(document.getElementById(SMsubID + "I")).src = "./basic/image/arrow_blue1.gif";
	} 
} 

function SMpoc211464(SMsubName, toOpen) { SMToOpen11464 = parseInt(toOpen); if(SMisWorking11464 == false) { SMobj11464 = document.getElementById(SMsubName); if(SMinitiallyOpenSub11464 != "") { SMobjOpen11464 = document.getElementById(SMinitiallyOpenSub11464); SMinitiallyOpenSub11464 = ""; } if(SMobjOpen11464 != null) { SMtmpNeedClose11464 = parseInt(SMobjOpen11464.style.height); } SMtimer11464 = window.setInterval(SMda11464, SMspeed11464); } } function SMda11464() { if(SMobjOpen11464 == null) { SMoo11464(); } else if(SMobjOpen11464 == SMobj11464) { if(SMableToCloseSub11464 == true) { SMco11464(); } else { window.clearInterval(SMtimer11464); } } else { SMoo211464(); } } function SMoo11464() { SMisWorking11464 = true; SMobj11464.style.display = "block"; if(SMtmpNeedOpen11464 + SMstep11464 <= SMToOpen11464) { SMobj11464.style.height = SMtmpNeedOpen11464 + SMstep11464; SMtmpNeedOpen11464 = SMtmpNeedOpen11464 + SMstep11464; } else { window.clearInterval(SMtimer11464); SMobj11464.style.height = SMToOpen11464; SMobjOpen11464 = SMobj11464; SMtmpNeedOpen11464 = 0; SMisWorking11464 = false; SMToOpen11464 = -1; } } function SMco11464() { SMisWorking11464 = true; if(SMtmpNeedClose11464 - SMstep11464 < SMstep11464) { window.clearInterval(SMtimer11464); SMobjOpen11464.style.display = "none"; SMobjOpen11464.style.height = 1; SMobjOpen11464 = null; SMisWorking11464 = false; SMtmpNeedClose11464 = 0; } else { SMobjOpen11464.style.height = SMtmpNeedClose11464 - SMstep11464; } SMtmpNeedClose11464 = SMtmpNeedClose11464 - SMstep11464; } function SMoo211464() { SMisWorking11464 = true; SMobj11464.style.display = "block"; if(SMtmpNeedOpen11464 + SMstep11464 <= SMToOpen11464) { SMobj11464.style.height = SMtmpNeedOpen11464 + SMstep11464; SMtmpNeedOpen11464 = SMtmpNeedOpen11464 + SMstep11464; } if(SMtmpNeedClose11464 - SMstep11464 >= 1) { SMobjOpen11464.style.height = SMtmpNeedClose11464 - SMstep11464; SMtmpNeedClose11464 = SMtmpNeedClose11464 - SMstep11464; } else { SMobjOpen11464.style.display = "none"; if(SMtmpNeedOpen11464 + SMstep11464 > SMToOpen11464 && SMtmpNeedClose11464 - SMstep11464 < 1) { window.clearInterval(SMtimer11464); SMobj11464.style.height = SMToOpen11464; SMobjOpen11464.style.display = "none"; SMobjOpen11464.style.height = 1; SMobjOpen11464 = null; SMobjOpen11464 = SMobj11464; SMtmpNeedOpen11464 = 0; SMtmpNeedClose11464 = 0; SMisWorking11464 = false; SMToClose11464 = -1; } } } 
function SMcs11464(SMobj, SMstyle, SMimage) 
{ 
if(SMstyle != "") 
{ 
SMobj.className = SMstyle; 
} 
if(SMimage != "") 
{ 
(document.getElementById(SMobj.id + "I")).src = SMimage;  
} 
}
//-->
</SCRIPT>

				          
					<TABLE cellSpacing=0 cellPadding=0 border=0 bgcolor=#6699cc width=190>
					  <TBODY>
					  <TR height=30>
					    <TD style="padding-left:5px;BORDER-TOP: #ffffff 1px solid;BORDER-left: #ffffff 1px solid;Border-right:1px solid #000000;Border-bottom:1px solid #000000;" align=center class=login1>															
									<div id='revolution' style='position:relative;top:0pt;width:140px;left:0px;text-align:center;font-family:Arial black;font-size:9pt;color:white;line-height:1;filter:glow(color=black,strength=2)'>
									ADMINISTRATOR
									</div>							
					    </TD>
					  </TR>			  
					 
					   <TR>
					    <TD>
					      <DIV class=SM_p11464 id=SM11464243 
					      onmouseover='SMcs11464(this, "SM_po11464", "")' 
					      onclick='SMpoc11464("SM11464243Sub","SM11464243")' 
					      onmouseout='SMcs11464(this, "SM_p11464", "")'>
					      
					      <IMG id=SM11464243I src="./basic/image/arrow_blue.gif" border=0>&nbsp;&nbsp; FLIGHT<!--basic data manage--></DIV>
					      
					      <DIV class=SM_cb11464 id=SM11464243Sub 
					      style="DISPLAY: <?=($SubID == "SM11464243Sub") ? "block":"none"; ?>; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 125px">
					      <A href="/admin/flightlist.asp?SubID=SM11464243Sub">
					      
					      <DIV class=SM_c11464 id=SM11464286Sub286 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;FLIGHT INFORMATION<!--flight schdule data--></DIV></a>					      
					      <A href="/admin/flightinfo.asp?SubID=SM11464243Sub">
					      <DIV class=SM_c11464 id=SM11464264Sub264 onmouseover='SMcs11464(this, "SM_co11464", "")' 
					      onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;FLIGHT CREATION<!--flight information--></DIV></A>
					      <A href="/admin/Allowed_lateral_weight.asp?SubID=SM11464243Sub">
					      <DIV class=SM_c11464 id=SM11464275Sub275 onmouseover='SMcs11464(this, "SM_co11464", "")' 
					      onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;allowed lateral weight </DIV></A>
					     
					      <A href="/admin/MTOW_MLWT_PER_AZFW.asp?SubID=SM11464243Sub">
					      <DIV class=SM_c11464 id=SM11464297Sub297 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;MTOW MLWT per AZFW</DIV></A>
					      <A href="/admin/ULD_POSITION_LIMITS-KGS.asp?SubID=SM11464243Sub">
					      <DIV class=SM_c11464 id=SM11464308Sub308 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;uld position limits kgs </DIV></A>
					      <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR>
					  <TR>
					    <TD>
					      <DIV class=SM_p11464 id=SM114641 onmouseover='SMcs11464(this, "SM_po11464", "")' onclick='SMpoc11464("SM114641Sub","SM114641")' onmouseout='SMcs11464(this, "SM_p11464", "")'>
					      <IMG id=SM114641I src="./basic/image/arrow_blue.gif" border=0>&nbsp;&nbsp;INDEX<!--Index Manage--></DIV>
					      <DIV class=SM_cb11464 id=SM114641Sub style="DISPLAY: <?=($SubID == "SM114641Sub") ? "block":"none"; ?>; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 0px">            
					      <A href="/admin/cargoindex.asp?SubID=SM114641Sub">
					      <DIV class=SM_c11464 id=SM1146430Sub30 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;cargo index </DIV></A>
					      <A href="/admin/fuelindex.asp?SubID=SM114641Sub">
					      <DIV class=SM_c11464 id=SM1146419Sub19 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;fuel index </DIV></A>
					      
					      <A href="/admin/fuelindexmanual.asp?SubID=SM114641Sub">
					      <DIV class=SM_c11464 id=SM1146430Sub32 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;fuel index (manual)</DIV></A>
					      <A href="/admin/crewindex.asp?SubID=SM114641Sub">
					      <DIV class=SM_c11464 id=SM1146430Sub31 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;crew index</DIV></A>
					      <A href="/admin/MAX_PLT_LOAD_PER_SIDE_IDX.asp?SubID=SM114641Sub">
					      <DIV class=SM_c11464 id=SM1146430Sub33 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;maximun pallet load per side </DIV></A>
					      <A href="/UI/Admin/Admin_Tagdesc.asp?vsite_id=<?=$vsite_id?>&SubID=SM114641Sub">
					      <DIV class=SM_c11464 id=SM1146430Sub34 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;-</DIV></A>
					      </DIV>
					      </TD>
					  </TR>
					  <TR>
					    <TD>
					      <DIV class=SM_p11464 id=SM11464176 
					      onmouseover='SMcs11464(this, "SM_po11464", "")' 
					      onclick='SMpoc11464("SM11464176Sub","SM11464176")' 
					      onmouseout='SMcs11464(this, "SM_p11464", "")'><IMG 
					      id=SM11464176I src="./basic/image/arrow_blue.gif" border=0>&nbsp;&nbsp;추가할수 있는 부분</DIV>
					      <DIV class=SM_cb11464 id=SM11464176Sub 
					      style="DISPLAY: <?=($SubID == "SM11464176Sub") ? "block":"none"; ?>; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 75px">
					      <A href="/UI/CP/CP_user_regdit.asp?vsite_id=<?=$vsite_id?>&SubID=SM11464176Sub">
					      <DIV class=SM_c11464 id=SM11464186Sub186 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;CP 계정부여</DIV></A>
					      <A href="/UI/CP/Portal_User_Regdit.asp?vsite_id=<?=$vsite_id?>&SubID=SM11464176Sub">
					      <DIV class=SM_c11464 id=SM11464197Sub197 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;사용자 계정부여</DIV></A>
					      <A href="#">
					      <DIV class=SM_c11464 id=SM11464208Sub208 
					      onmouseover='SMcs11464(this, "SM_co11464", "")' 
					      onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;</DIV></A>
					      <DIV class=SMEmptyDiv11464></DIV></DIV>
					    </TD>
					   </TR>
					   <TR>
					    <TD>
					    
					      <DIV class=SM_p11464 id=SM1146487 
					      onmouseover='SMcs11464(this, "SM_po11464", "")' 
					      onclick='SMpoc11464("SM1146487Sub","SM1146487")' 
					      onmouseout='SMcs11464(this, "SM_p11464", "")'><IMG 
					      id=SM1146487I src="./basic/image/arrow_blue.gif" border=0>&nbsp;&nbsp;USER MANAGE </DIV>
					      <DIV class=SM_cb11464 id=SM1146487Sub 
					      style="DISPLAY: <?=($SubID == "SM1146487Sub") ? "block":"none"; ?>; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 0px">
					      <A href="/admin/team.asp?mode=list&SubID=SM1146487Sub">
					      <DIV class=SM_c11464 id=SM1146497Sub97 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;TEAM</DIV></A>
					      <A href="/admin/group.asp?SubID=SM1146487Sub">
					      <DIV class=SM_c11464 id=SM1146497Sub98 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;GROUP</DIV></A>
					      <A href="/admin/loadmaster.asp?SubID=SM1146487Sub">
					      <DIV class=SM_c11464 id=SM11464108Sub108 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;USER</DIV></A></DIV></TD></TR>
					  
					 <!--TR>
					    <TD>
					      <DIV class=SM_p11464 id=SM1146487 
					      onmouseover='SMcs11464(this, "SM_po11464", "")' 
					      onclick='SMpoc11464("SM1146487Sub","SM1146487")' 
					      onmouseout='SMcs11464(this, "SM_p11464", "")'><IMG 
					      id=SM1146487I src="./basic/image/arrow_blue.gif" border=0>&nbsp;&nbsp;카테고리 관리 </DIV>
					      <DIV class=SM_cb11464 id=SM1146487Sub 
					      style="DISPLAY: <?=($SubID == "SM1146487Sub") ? "block":"none"; ?>; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 0px">
					      <A href="/CESERVER/CategoryEdit.asp?vsite_id=<?=$vsite_id?>&SubID=SM1146487Sub">
					      <DIV class=SM_c11464 id=SM1146497Sub97 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;카테고리 편집기 (사이트 분석)</DIV></A>
					      <A href="/shop1/announce_insert.asp">
					      <DIV class=SM_c11464 id=SM1146497Sub98 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;Referrer 관리 (매출. 잠재고객분석)</DIV></A>
					      <A href="T_MSG.asp?SubID=SM1146487Sub">
					      <DIV class=SM_c11464 id=SM11464108Sub108 onmouseover='SMcs11464(this, "SM_co11464", "")' onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;상품 자동삭제(매출분석, 잠재고객분석)</DIV></A--></DIV></TD></TR>
					  
					  <TR>
					    <TD>
					      <DIV class=SM_p11464 id=SM11464332 
					      onmouseover='SMcs11464(this, "SM_po11464", "")' 
					      onclick='SMpoc11464("SM11464332Sub","SM11464332")' 
					      onmouseout='SMcs11464(this, "SM_p11464", "")'><IMG 
					      id=SM11464332I src="./basic/image/arrow_blue.gif" border=0>&nbsp;&nbsp;message manager</DIV>
					      <DIV class=SM_cb11464 id=SM11464332Sub 
					      style="DISPLAY: <?=($SubID == "SM11464245Sub") ? "block":"none"; ?>; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 150px">
					      <!--A href="/Target/step_00.asp?&SubID=SM11464245Sub">
					      <DIV class=SM_c11464 id=SM11464253Sub253 onmouseover='SMcs11464(this, "SM_co11464", "")' 
					      onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;타겟설정</DIV></A-->
					      <A href="/admin/news.asp?SubID=SM11464245Sub">
					      <DIV class=SM_c11464 id=SM11464264Sub264 onmouseover='SMcs11464(this, "SM_co11464", "")' 
					      onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;news </DIV></A>
					      <!--A href="/UI/Admin/email_sending.asp?vsite_id=<?=$vsite_id?>&SubID=SM11464245Sub">
					      <DIV class=SM_c11464 id=SM11464275Sub275 onmouseover='SMcs11464(this, "SM_co11464", "")' 
					      onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;e-mail 보내기</DIV></A-->
					      <A href="/UI/Campaign/Email/input.asp?vsite_id=<?=$vsite_id?>&SubID=SM11464245Sub">
					      <DIV class=SM_c11464 id=SM11464275Sub275 onmouseover='SMcs11464(this, "SM_co11464", "")' 
					      onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;-</DIV></A>
					     
					      <A href="/UI/Campaign/Manage_CampaignHistory.asp?vsite_id=<?=$vsite_id?>&SubID=SM11464245Sub">
					      <DIV class=SM_c11464 id=SM11464286Sub286 onmouseover='SMcs11464(this, "SM_co11464", "")' 
					      onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;-</DIV></A>
					      
					      <A href="/UI/Campaign/Manage_Target.asp?vsite_id=<?=$vsite_id?>&SubID=SM11464245Sub">
					      <DIV class=SM_c11464 id=SM11464297Sub297 onmouseover='SMcs11464(this, "SM_co11464", "")' 
					      onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;-</DIV></A>
					      
					      <!--A href="/UI/Admin/Admin_Referer.asp?vsite_id=<?=$vsite_id?>&SubID=SM11464245Sub">
					      <DIV class=SM_c11464 id=SM11464308Sub308 onmouseover='SMcs11464(this, "SM_co11464", "")' 
					      onmouseout='SMcs11464(this, "SM_c11464", "")'>&nbsp;&nbsp;&nbsp;사용자 정의 등록</DIV></A-->
					      
					      </TD></TR>
					
					</TBODY>
				</TABLE>
          
          
<?
}
?>