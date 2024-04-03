<?


session_start();

if($report_type == "0"){
	//echo Sheet00();
	echo "<form name=contentsfrm style='display:none'><textarea name=contents>".Sheet00()."</textarea></form>";
}else if($report_type == "1"){	
	echo "<form name=contentsfrm style='display:none'><textarea name=contents>".Sheet01()."</textarea></form>";
}else if($report_type == 2){
	echo "<form name=contentsfrm style='display:none'><textarea name=contents>".VacationSheet()."</textarea></form>";
}else if($report_type == 3){
	echo "<form name=contentsfrm style='display:none'><textarea name=contents>".DailySheet()."</textarea></form>";	
}else if($report_type == 4){
	echo "<form name=contentsfrm style='display:none'><textarea name=contents>".WeeklySheet()."</textarea></form>";
}else if($report_type == 5){
	echo "<form name=contentsfrm style='display:none'><textarea name=contents>".MonthlySheet()."</textarea></form>";
}else if($report_type == 6){
	echo "<form name=contentsfrm style='display:none'><textarea name=contents>".CostSheet()."</textarea></form>";
}else if($report_type == 8){
	//echo "<style>TD{font-size:11px;}</style>";
	//echo MeetingSheet();
	echo "<form name=contentsfrm style='display:none'><textarea name=contents>".MeetingSheet()."</textarea></form>";
}else if($report_type == 9){
	echo "<form name=contentsfrm style='display:none'><textarea name=contents>".NewDailySheet()."</textarea></form>";	
}else if($report_type == 99){
	echo "<form name=contentsfrm style='display:none'><textarea name=contents>".NoticeSheet()."</textarea></form>";
}
echo "<script>parent.window.frames['iView'].document.body.innerHTML = document.contentsfrm.contents.value;</script>";	

//}


function MeetingSheet(){ //".getDepartment($user[company_id], $user[code])."
global $admininfo;
return "
<style>
.sheet_contents{padding:20px;line-height:140%}
TD {font-size:12px;font-family:돋움;}
</style>
<table cellpadding=5 cellspacing=1 bgcolor=#000000 border=0 width=760 style='font-size:12px;font-family:돋움;border:0px solid #000000;margin-bottom:30px;'>	
	<col width='150px'>
	<col width='*'>
	<col width='150px'>
	<col width='50px'>
	<col width='70px'>
	<col width='170px'>
	<tr height=30 bgcolor=#ffffff>
		<td colspan=2 align=center bgcolor=#ffffff rowspan=2><u><b style='font-size:24px;font-family:바탕체;'>회의록</b></u></td>
		<td align=center bgcolor=#efefef class='sheet_text' colspan=2>작&nbsp;&nbsp; 성&nbsp;&nbsp; 자</td>
		<td class='sheet_text' align=center bgcolor=#efefef colspan=2>의&nbsp;&nbsp;&nbsp;견</td>
	</tr>
	<tr height=30 bgcolor=#ffffff>
		<td align=center bgcolor=#ffffff colspan=2>".$admininfo[charger]."</td>
		<td colspan=3 align=center>&nbsp;".$admininfo[charger]."</td>
	</tr>
	<tr height=30 width=100% bgcolor=#ffffff >				
		<td align=center > 회 의 명</td>
		<td align=left valign=middle id='sheet_title' colspan=3></td>	
		<td align=center > 진 행 자</td>
		<td align=center valign=middle>&nbsp;".$admininfo[charger]."</td>	
	</tr>
	<tr height=30 width=100% bgcolor=#ffffff >				
		<td align=center > 장 소</td>
		<td align=left valign=middle id='sheet_title' colspan=2></td>		
		<td align=center > 일 시</td>
		<td align=left valign=middle colspan=2>&nbsp;".date("Y-m-d")."</td>	
	</tr>
	<tr height=50 width=100% bgcolor=#ffffff >				
		<td align=center > 참 석 자</td>
		<td align=left valign=middle id='sheet_title' colspan=5>&nbsp;</td>				
	</tr>
	<tr height=30 width=100% bgcolor=silver >				
		<td align=center colspan=6> 회의내용 (토의 및 합의 사항)  </td>		
	</tr>
	<tr height=250 width=100% bgcolor=#ffffff >				
		<td align=left valign=top class='sheet_contents' id='sheet_contents' colspan=6 >
		예)<br>
		<회의주요 내용><br>
		1. 첫번째 안간
		<li> 첫번째 안건관련 논의 사항1</li>
		<li> 첫번째 안건관련 논의 사항2</li>
		<li> 첫번째 안건관련 논의 사항3</li>
		<br><br>
		2. 두번째 안간
		<li> 두번째 안건관련 논의 사항1</li>
		<li> 두번째 안건관련 논의 사항2</li>
		<li> 두번째 안건관련 논의 사항3</li>
		</td>				
	</tr>
</table>


";
	
}


function Sheet00(){ //".getDepartment($user[company_id], $user[code])."
global $user;
return "
<style>
.sheet_contents{padding:20px;line-height:140%}
TD {font-size:12px;font-family:돋움;}
</style>
<table cellpadding=10 cellspacing=0 border=0 width=760 style='border:0px solid #000000'>	
	<tr height=20><td colspan=2 align=center><u><b style='font-size:24px;font-family:바탕체;'>업무 보고</b></u></td></tr>	
	<tr height=100>
		<td valign=top align=left height=100>			
		</td>
		<td valign=top align=right >
			<table cellpadding=0 cellspacing=1 bgcolor=#000000 width=300>				
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>부&nbsp;&nbsp; 서&nbsp;&nbsp; 명</td>
					<td class='sheet_text'>&nbsp;</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>작&nbsp;&nbsp; 성&nbsp;&nbsp; 자</td>
					<td class='sheet_text'>&nbsp;".$user[name]."</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>작&nbsp;&nbsp; 성&nbsp;&nbsp; 일</td>
					<td class='sheet_text'>&nbsp;".date("Y-m-d")."</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height=20 >
		<td colspan=2 align=center valign=middle style='padding-top:0px;padding-bottom:0px;' height=20>
			<table cellpadding=0 cellspacing=0 style='border:1px solid #000000'  width=100% height=100%>
			<tr height=30 width=100% bgcolor=#ffffff >				
				<td width=20% style='border-right:1px solid #000000' align=center class='sheet_title'> 제 목</td><td width=80% align=left valign=middle class='sheet_title' id='sheet_title' style='padding:10px'></td>				
			</tr>
			</table>
		</td>
	</tr>
	<tr height=300>
		<td colspan=2 align=center valign=top>
			<table cellpadding=0 cellspacing=1 bgcolor=#000000  width=100% height=100%>
			<tr height=30 width=100% bgcolor=#efefef >				
				<td width=20% align=center class='sheet_title'> 구 분  </td><td width=80% align=left valign=middle class='sheet_title'>&nbsp;업무 내용및 진행 현황</td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title' > 금 일  </td>
				<td width=80% align=left valign=top class='sheet_contents' id='sheet_contents' style='padding:10px'></td>				
			</tr>
			<!--tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title'> 익 일  </td><td width=80% align=left valign=top class='sheet_contents'></td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title'> 특기사항  </td><td width=80% align=left valign=top class='sheet_contents'></td>				
			</tr-->
			</table>
		</td>
	</tr>	
	<tr height=150>
		<td colspan=2 align=center valign=top>
			
		</td>
	</tr>
</table>


";
	
}

function Sheet01(){//".getDepartment($user[company_id], $user[code])."
global $user;
return "
<style>
.sheet_contents{padding:20px;line-height:140%}
TD {font-size:12px;font-family:돋움;}
</style>
<table cellpadding=10 cellspacing=0 border=0 width=760 style='border:0px solid #000000'>	
	<tr height=20><td colspan=2 align=center><u><b style='font-size:24px;font-family:바탕체;'>기 안 서</b></u></td></tr>	
	<tr height=200>
		<td valign=top align=left height=100>
			<table cellpadding=0 cellspacing=0 width=100% style='font-size:12px;'>
				<tr height=29 bgcolor=#ffffff>
					<td width=70 class='bottom_line'>문서&nbsp; 번호</td>
					<td width=30 class='bottom_line'>&nbsp;:&nbsp;</td>
					<td width=170 class='bottom_line' style='padding-right:30px;' align=right>&nbsp;</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line'>보안&nbsp; 등급</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=right>&nbsp;</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line'>기 &nbsp;안&nbsp; 일</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=right>&nbsp;".date("Y-m-d")."</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line'>기&nbsp; 안&nbsp; 자</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=right>&nbsp;".$user[name]."</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line'>기안&nbsp; 부서</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=right>&nbsp;</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line'>직&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      급</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=right>&nbsp;".$user[position]."</td>
				</tr>			
			</table>
		</td>
		<td valign=top align=right >
			<table cellpadding=0 cellspacing=1 bgcolor=#000000>
				<tr height=70 bgcolor=#ffffff>
					<td width=30 align=center vlaign=middle rowspan=2 style='font-size:12px;' class='sheet_text'>결<br><br><br>제</td>
					<td width=80>&nbsp;</td>
					<td width=80>&nbsp;</td>
					<td width=80>&nbsp;</td>
					<td width=80>&nbsp;</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td id=sign01 class='sheet_title'>&nbsp;</td>
					<td id=sign02 class='sheet_title'>&nbsp;</td>
					<td id=sign03 class='sheet_title'>&nbsp;</td>
					<td id=sign04 class='sheet_title'>&nbsp;</td>
				</tr>
				<tr height=75 bgcolor=#ffffff>
					<td align=center vlaign=middle style='font-size:12px;' class='sheet_text'>협<br><br><br>조</td>
					<td width=200 colspan=4>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height=20 >
		<td colspan=2 align=center valign=middle style='padding-top:0px;padding-bottom:0px;' height=20>
			<table cellpadding=0 cellspacing=0 style='border:1px solid #000000'  width=100% height=100%>
			<tr height=20 width=100% bgcolor=#ffffff >				
				<td width=20% style='border-right:1px solid #000000' align=center class='sheet_title'> 제 목 </td><td width=80% align=left valign=middle class='sheet_title' id='sheet_title' style='padding-left:10px;'></td>				
			</tr>
			</table>
		</td>
	</tr>
	<tr height=300>
		<td colspan=2 align=center valign=top>
			<table cellpadding=10 cellspacing=0 style='border:1px solid #000000'  width=100% height=100%>
			<tr height=100% width=100% bgcolor=#ffffff>				
				<td width=80% align=left valign=top class='sheet_contents' id='sheet_contents'>&nbsp;</td>				
			</tr>
			</table>
		</td>
	</tr>
	<tr height=150>
		<td colspan=2 align=center valign=top>
			
		</td>
	</tr>
</table>


";
	
}





function DailySheet(){//".getDepartment($user[company_id], $user[code])."
global $user;
return "
<style>
.sheet_contents{padding:20px;line-height:140%}
TD {font-size:12px;font-family:돋움;}
</style>
<table cellpadding=10 cellspacing=0 border=0 width=760 style='border:0px solid #000000'>	
	<tr height=20><td colspan=2 align=center><u><b style='font-size:24px;font-family:바탕체;'>일일 업무 보고</b></u></td></tr>	
	<tr height=100>
		<td valign=top align=left height=100>			
		</td>
		<td valign=top align=right >
			<table cellpadding=0 cellspacing=1 bgcolor=#000000 width=300>				
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>부&nbsp;&nbsp; 서&nbsp;&nbsp; 명</td>
					<td class='sheet_text'>&nbsp;</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>작&nbsp;&nbsp; 성&nbsp;&nbsp; 자</td>
					<td class='sheet_text'>&nbsp;".$user[name]."</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>작&nbsp;&nbsp; 성&nbsp;&nbsp; 일</td>
					<td class='sheet_text'>&nbsp;".date("Y-m-d")."</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height=20 >
		<td colspan=2 align=center valign=middle style='padding-top:0px;padding-bottom:0px;' height=20>
			<table cellpadding=0 cellspacing=0 style='border:1px solid #000000'  width=100% height=100%>
			<tr height=30 width=100% bgcolor=#ffffff >				
				<td width=20% style='border-right:1px solid #000000' align=center class='sheet_title'> 제 목</td><td width=80% align=left valign=middle class='sheet_title' id='sheet_title' style='padding:3px 10px'></td>				
			</tr>
			</table>
		</td>
	</tr>
	<tr height=300>
		<td colspan=2 align=center valign=top>
			<table cellpadding=0 cellspacing=1 bgcolor=#000000  width=100% height=100%>
			<tr height=30 width=100% bgcolor=#efefef >				
				<td width=20% align=center class='sheet_title'> 구 분  </td><td width=80% align=left valign=middle class='sheet_title'>&nbsp;업무 내용및 진행 현황</td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title' > 금 일  </td>
				<td width=80% align=left valign=top class='sheet_contents' id='sheet_contents' style='padding:10px'></td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title'> 익 일  </td>
				<td width=80% align=left valign=top class='sheet_contents' style='padding:10px'></td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title'> 특기사항  </td>
				<td width=80% align=left valign=top class='sheet_contents' style='padding:10px'></td>				
			</tr>
			</table>
		</td>
	</tr>	
	<tr height=150>
		<td colspan=2 align=center valign=top>
			
		</td>
	</tr>
</table>


";
	
}




function NewDailySheet(){//".getDepartment($user[company_id], $user[code])."
global $user;
return "
<style>
.sheet_contents{padding:20px;line-height:140%}
TD {font-size:12px;font-family:돋움;}
</style>
<table cellpadding=10 cellspacing=0 border=0 width=100% style='border:0px solid #000000'>	
	<tr height=20><td colspan=2 align=center><u><b style='font-size:24px;font-family:바탕체;'>일일 업무 보고</b></u></td></tr>	
	<tr height=100>
		<td valign=top align=left height=100>			
		</td>
		<td valign=top align=right >
			<table cellpadding=0 cellspacing=1 bgcolor=#000000 width=300>				
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>부&nbsp;&nbsp; 서&nbsp;&nbsp; 명</td>
					<td class='sheet_text'>&nbsp;</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>작&nbsp;&nbsp; 성&nbsp;&nbsp; 자</td>
					<td class='sheet_text'>&nbsp;".$user[name]."</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>작&nbsp;&nbsp; 성&nbsp;&nbsp; 일</td>
					<td class='sheet_text'>&nbsp;".date("Y-m-d")."</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height=20 >
		<td colspan=2 align=center valign=middle style='padding-top:0px;padding-bottom:0px;' height=20>
			<table cellpadding=0 cellspacing=0 style='border:1px solid #000000'  width=100% height=100%>
			<tr height=30 width=100% bgcolor=#ffffff >				
				<td width=15% style='border-right:1px solid #000000' align=center class='sheet_title'> 제 목</td>
				<td width=85% align=left valign=middle class='sheet_title' id='sheet_title' style='padding:3px 10px'></td>				
			</tr>
			</table>
		</td>
	</tr>
	<tr height=300>
		<td colspan=2 align=center valign=top>
			<table cellpadding=0 cellspacing=1 bgcolor=#000000  width=100% height=100%>
			<col width='15%'>
			<col width='20%'>
			<col width='25%'>
			<col width='8%'>
			<col width='10%'>
			<col width='10%'>
			<col width='10%'>
			<tr height=30 width=100% bgcolor=#efefef >				
				<td align=center class='sheet_title'> 구 분  </td>
				<td align=center valign=middle class='sheet_title'>&nbsp;업무명</td>
				<td align=center valign=middle class='sheet_title'>&nbsp;업무내용(상세히)</td>
				<td align=center valign=middle class='sheet_title'>&nbsp;진행율</td>
				<td align=center valign=middle class='sheet_title'>&nbsp;지정마감일</td>
				<td align=center valign=middle class='sheet_title'>&nbsp;완료예정일</td>
				<td align=center valign=middle class='sheet_title'>&nbsp;업무책임자</td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >				
				<td align=center class='sheet_title' rowspan=3> 금일 완료업무  </td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >			
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >			
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >				
				<td align=center class='sheet_title' rowspan=5> 금일 진행업무  </td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >			
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >			
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >			
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >			
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >				
				<td align=center class='sheet_title' rowspan=3> 금일 새로운업무  </td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >			
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=30 width=100% bgcolor=#ffffff >			
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=left valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
				<td align=center valign=top class='sheet_contents' style='padding:10px'></td>
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td align=center class='sheet_title'> 내일계획한 업무  </td>
				<td colspan=6 align=left valign=top class='sheet_contents' style='padding:10px'></td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td align=center class='sheet_title'> 지원요청사항  </td>
				<td colspan=6 align=left valign=top class='sheet_contents' style='padding:10px'></td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td align=center class='sheet_title'> 지시사항(콤멘트) </td>
				<td colspan=6 align=left valign=top class='sheet_contents' style='padding:10px'></td>				
			</tr>
			</table>
		</td>
	</tr>	
	<tr height=150>
		<td colspan=2 align=center valign=top>
			
		</td>
	</tr>
</table>


";
	
}


function WeeklySheet(){//".getDepartment($user[company_id], $user[code])."
global $user;
return "
<style>
.sheet_contents{padding:20px;line-height:140%}
TD {font-size:12px;font-family:돋움;}
</style>
<table cellpadding=10 cellspacing=0 border=0 width=760 style='border:0px solid #000000'>	
	<tr height=20><td colspan=2 align=center><u><b style='font-size:24px;font-family:바탕체;'>주간 업무 보고</b></u></td></tr>	
	<tr height=100>
		<td valign=top align=left height=100>			
		</td>
		<td valign=top align=right >
			<table cellpadding=0 cellspacing=1 bgcolor=#000000 width=300>				
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>부&nbsp;&nbsp; 서&nbsp;&nbsp; 명</td>
					<td class='sheet_text'>&nbsp;</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>작&nbsp;&nbsp; 성&nbsp;&nbsp; 자</td>
					<td class='sheet_text'>&nbsp;".$user[name]."</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>작&nbsp;&nbsp; 성&nbsp;&nbsp; 일</td>
					<td class='sheet_text'>&nbsp;".date("Y-m-d")."</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height=20 >
		<td colspan=2 align=center valign=middle style='padding-top:0px;padding-bottom:0px;' height=20>
			<table cellpadding=0 cellspacing=0 style='border:1px solid #000000'  width=100% height=100%>
			<tr height=30 width=100% bgcolor=#ffffff >				
				<td width=20% style='border-right:1px solid #000000' align=center class='sheet_title'> 제 목</td><td width=80% align=left valign=middle class='sheet_title' id='sheet_title'></td>				
			</tr>
			</table>
		</td>
	</tr>
	<tr height=300>
		<td colspan=2 align=center valign=top>
			<table cellpadding=0 cellspacing=1 bgcolor=#000000  width=100% height=100%>
			<tr height=30 width=100% bgcolor=#efefef >				
				<td width=20% align=center class='sheet_title'> 구 분  </td><td width=80% align=left valign=middle class='sheet_title' style='padding:10px'>&nbsp;업무 내용및 진행 현황</td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title' > 금 주  </td><td width=80% align=left valign=top class='sheet_title' id='sheet_contents' style='padding:10px'></td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title'> 차 주  </td><td width=80% align=left valign=top class='sheet_title' style='padding:10px'></td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title'> 특기사항  </td><td width=80% align=left valign=top class='sheet_title' style='padding:10px'></td>				
			</tr>
			</table>
		</td>
	</tr>	
	<tr height=150>
		<td colspan=2 align=center valign=top>
			
		</td>
	</tr>
</table>


";
	
}



function MonthlySheet(){//".getDepartment($user[company_id], $user[code])."
global $user;
return "
<style>
.sheet_contents{padding:20px;line-height:140%}
TD {font-size:12px;font-family:돋움;}
</style>
<table cellpadding=10 cellspacing=0 border=0 width=760 style='border:0px solid #000000'>	
	<tr height=20><td colspan=2 align=center><u><b style='font-size:24px;font-family:바탕체;'>월간 업무 보고</b></u></td></tr>	
	<tr height=100>
		<td valign=top align=left height=80>			
		</td>
		<td valign=top align=right >
			<table cellpadding=0 cellspacing=1 bgcolor=#000000 width=300>				
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>부&nbsp;&nbsp; 서&nbsp;&nbsp; 명</td>
					<td class='sheet_text'>&nbsp;</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>작&nbsp;&nbsp; 성&nbsp;&nbsp; 자</td>
					<td class='sheet_text'>&nbsp;".$user[name]."</td>
				</tr>
				<tr height=30 bgcolor=#ffffff>
					<td width=150 align=center bgcolor=#efefef class='sheet_text'>작&nbsp;&nbsp; 성&nbsp;&nbsp; 일</td>
					<td class='sheet_text'>&nbsp;".date("Y-m-d")."</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height=20 >
		<td colspan=2 align=center valign=middle style='padding-top:0px;padding-bottom:0px;' height=20>
			<table cellpadding=0 cellspacing=0 style='border:1px solid #000000'  width=100% height=100%>
			<tr height=30 width=100% bgcolor=#ffffff >				
				<td width=20% style='border-right:1px solid #000000' align=center class='sheet_title'> 제 목</td><td width=80% align=left valign=middle class='sheet_title' id='sheet_title'></td>				
			</tr>
			</table>
		</td>
	</tr>
	<tr height=300>
		<td colspan=2 align=center valign=top>
			<table cellpadding=0 cellspacing=1 bgcolor=#000000  width=100% height=100%>
			<tr height=30 width=100% bgcolor=#efefef >				
				<td width=20% align=center class='sheet_title'> 구 분  </td><td width=80% align=left valign=middle class='sheet_title'>&nbsp;업무 내용및 진행 현황</td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title' > 금 월  </td><td width=80% align=left valign=top class='sheet_title' id='sheet_contents' style='padding:10px'></td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title'> 익 월 </td><td width=80% align=left valign=top class='sheet_title' style='padding:10px'></td>				
			</tr>
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title'> 특기사항  </td><td width=80% align=left valign=top class='sheet_title' style='padding:10px'></td>				
			</tr>
			</table>
		</td>
	</tr>	
	<tr height=150>
		<td colspan=2 align=center valign=top>
			
		</td>
	</tr>
</table>


";
	
}


function VacationSheet(){//".getDepartment($user[company_id], $user[code])."
global $user;
return "
<style>
.sheet_contents{padding:20px;line-height:140%}
TD {font-size:12px;font-family:돋움;}
</style>
<table cellpadding=10 cellspacing=0 border=0 width=760 style='border:0px solid #000000'>	
	<tr height=20><td colspan=2 align=center><u><b style='font-size:24px;font-family:바탕체;'>휴 가 계</b></u></td></tr>	
	<tr height=130>
		<td valign=top align=left width=300>
			<table cellpadding=0 cellspacing=0 width=100% style='font-size:12px;'>				
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line'>신 &nbsp;청&nbsp; 일</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=right>&nbsp;".date("Y-m-d")."</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line'>신&nbsp; 청&nbsp; 자</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=right>&nbsp;".$user[name]."</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line'>부&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      서</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=right>&nbsp;</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line'>직&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      급</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=right>&nbsp;".$user[position]."</td>
				</tr>			
			</table>
		</td>
		<td valign=top align=right >
			<table cellpadding=0 cellspacing=1 bgcolor=#000000>
				<tr height=20 bgcolor=#ffffff>
					<td width=30 align=center vlaign=middle rowspan=3 style='font-size:12px;' class='sheet_text'>결<br><br><br>제</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>		
				<tr height=70 bgcolor=#ffffff>					
					<td width=60>&nbsp;</td>
					<td width=60>&nbsp;</td>
					<td width=60>&nbsp;</td>
					<td width=60>&nbsp;</td>
				</tr>
				<tr height=25 bgcolor=#ffffff>
					<td id=sign01 class='sheet_title'>&nbsp;</td>
					<td id=sign02 class='sheet_title'>&nbsp;</td>
					<td id=sign03 class='sheet_title'>&nbsp;</td>
					<td id=sign04 class='sheet_title'>&nbsp;</td>
				</tr>				
			</table>
		</td>
	</tr>
	<tr height=20 >
		<td colspan=2 align=center valign=middle style='padding-top:0px;padding-bottom:0px;' height=20>
			<table cellpadding=5 cellspacing=1 bgcolor=#000000  width=100% height=100%>
			<tr height=20 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title'> 휴가기간 </td>
				<td width=80% align=center valign=middle colspan=7 class='sheet_text'>&nbsp;&nbsp;&nbsp;년&nbsp;&nbsp;&nbsp;  월&nbsp;&nbsp;&nbsp;  일 &nbsp;&nbsp;&nbsp;~ &nbsp;&nbsp;&nbsp;년 &nbsp;&nbsp;&nbsp;월 &nbsp;&nbsp;&nbsp;일 [ &nbsp;박 &nbsp;일]</td>				
			</tr>			
			<tr height=20 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_text' rowspan=2> 휴가종류 </td>
				<td align=center valign=middle class='sheet_text'>정기</td>
				<td align=center valign=middle class='sheet_text'>연차</td>
				<td align=center valign=middle class='sheet_text'>월차</td>
				<td align=center valign=middle class='sheet_text'>공가</td>
				<td align=center valign=middle class='sheet_text'>경조</td>
				<td align=center valign=middle class='sheet_text'>출산</td>
				<td align=center valign=middle class='sheet_text'>결근/기타</td>
			</tr>
			<tr height=20 width=100% bgcolor=#ffffff >				
				<td align=left valign=middle class='sheet_text'></td>
				<td align=left valign=middle class='sheet_text'></td>
				<td align=left valign=middle class='sheet_text'></td>
				<td align=left valign=middle class='sheet_text'></td>
				<td align=left valign=middle class='sheet_text'></td>
				<td align=left valign=middle class='sheet_text'></td>
				<td align=left valign=middle class='sheet_text'></td>
			</tr>
			<tr height=20 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_text'> 휴가사유 </td>
				<td width=80% align=left valign=middle colspan=7  class='sheet_text'>&nbsp;</td>				
				<td width=80% align=left valign=middle  class='sheet_text' id='sheet_title' style='display:none'>&nbsp;[".date("Y-m-d")."] ".$user[name]." 휴가계</td>
			</tr>
			</table>
		</td>
	</tr>	
	<tr height=300>
		<td colspan=2 align=center valign=top >			
			<table cellpadding=10 cellspacing=0   width=100% height=100%>
			<tr height=30>
				<td align=left valign=bottom class='sheet_text'>
					기타사항
				</td>
			</tr>
			<tr height=100% width=100% bgcolor=#ffffff>				
				<td width=80% style='border:1px solid #000000' align=left valign=top class='sheet_contents' id='sheet_contents'>&nbsp;</td>				
			</tr>
			</table>
		</td>
	</tr>
	<tr height=150>
		<td colspan=2 align=center valign=top>
			
		</td>
	</tr>
</table>


";
	
}


function CostSheet(){//".getDepartment($user[company_id], $user[code])."
global $user;
return "
<style>
.sheet_contents{padding:20px;line-height:140%}
TD {font-size:12px;font-family:돋움;}
</style>
<table cellpadding=10 cellspacing=0 border=0 width=760 style='border:0px solid #000000'>	
	<tr height=20><td colspan=2 align=center><u><b style='font-size:24px;font-family:바탕체;'>경비 신청서</b></u></td></tr>	
	<tr height=130>
		<td valign=top align=left width=300>
			<table cellpadding=0 cellspacing=0 width=100% style='font-size:12px;' border=0>				
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line' align=center>신 &nbsp;청&nbsp; 일</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=left>&nbsp;".date("Y-m-d")."</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line' align=center>신&nbsp; 청&nbsp; 자</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=left>&nbsp;".$user[name]."</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line' align=center>부&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      서</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=left>&nbsp;</td>
				</tr>
				<tr height=29 bgcolor=#ffffff>
					<td class='bottom_line' align=center>직&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      급</td>
					<td class='bottom_line'>&nbsp;:&nbsp;</td>
					<td class='bottom_line' style='padding-right:30px;' align=left>&nbsp;".$user[position]."</td>
				</tr>			
			</table>
		</td>
		<td valign=top align=right >
			<table cellpadding=0 cellspacing=1 bgcolor=#000000>
				<tr height=20 bgcolor=#ffffff>
					<td width=30 align=center vlaign=middle rowspan=3 style='font-size:12px;' class='sheet_text'>결<br><br><br>제</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>		
				<tr height=70 bgcolor=#ffffff>					
					<td width=60>&nbsp;</td>
					<td width=60>&nbsp;</td>
					<td width=60>&nbsp;</td>
					<td width=60>&nbsp;</td>
				</tr>
				<tr height=25 bgcolor=#ffffff>
					<td id=sign01 class='sheet_title'>&nbsp;</td>
					<td id=sign02 class='sheet_title'>&nbsp;</td>
					<td id=sign03 class='sheet_title'>&nbsp;</td>
					<td id=sign04 class='sheet_title'>&nbsp;</td>
				</tr>				
			</table>
		</td>
	</tr>
	<tr height=20 >
		<td colspan=2 align=center valign=middle style='padding-top:0px;padding-bottom:0px;' height=20>
			<table cellpadding=5 cellspacing=1 bgcolor=#000000  width=100% height=100%>				
			<tr height=20 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_text' width=80 bgcolor=#efefef> 경비항목 </td>
				<td align=center valign=middle class='sheet_text' width=80 ></td>
				<td align=center valign=middle  bgcolor=#efefef class='sheet_text' width=80 >경비지출일</td>
				<td align=center valign=middle class='sheet_text' width=80 ></td>
				<td align=center valign=middle  bgcolor=#efefef class='sheet_text' width=80 >청구금액</td>
				<td align=center valign=middle class='sheet_text' width=80 ></td>
			</tr>
			<tr height=20 width=100% bgcolor=#ffffff >				
				<td width=20% align=center  bgcolor=#efefef class='sheet_text'> 경비지출사유 </td>
				<td width=80% align=left valign=middle colspan=5  class='sheet_text' id='sheet_title'>&nbsp;</td>				
			</tr>
			</table>
		</td>
	</tr>	
	<tr height=300>
		<td colspan=2 align=center valign=top >			
			<table cellpadding=10 cellspacing=0   width=100% height=100%>
			<tr height=30>
				<td align=left valign=bottom class='sheet_text'>
					기타사항
				</td>
			</tr>
			<tr height=100% width=100% bgcolor=#ffffff>				
				<td width=80% style='border:1px solid #000000' align=left valign=top class='sheet_contents' id='sheet_contents'>&nbsp;</td>				
			</tr>
			</table>
		</td>
	</tr>
	<tr height=150>
		<td colspan=2 align=center valign=top>
			
		</td>
	</tr>
</table>


";
	
}




function NoticeSheet(){
global $user;
return "
<style>
.sheet_contents{padding:20px;line-height:140%}
TD {font-size:12px;font-family:돋움;}
</style>
<table cellpadding=10 cellspacing=0 border=0 width=760 style='border:0px solid #000000'>	
	<tr height=20><td colspan=2 align=center><u><b style='font-size:24px;font-family:바탕체;'>공지사항</b></u></td></tr>	
	<tr height=20 >
		<td colspan=2 align=center valign=middle style='padding-top:0px;padding-bottom:0px;' height=20>
			<table cellpadding=0 cellspacing=0 style='border:1px solid #000000'  width=100% height=100%>
			<tr height=30 width=100% bgcolor=#ffffff >				
				<td width=20% style='border-right:1px solid #000000' align=center class='sheet_title'> 제 목</td><td width=80% align=left valign=middle class='sheet_title' id='sheet_title'>&nbsp;</td>				
			</tr>
			</table>
		</td>
	</tr>
	<tr height=300>
		<td colspan=2 align=center valign=top>
			<table cellpadding=0 cellspacing=1 bgcolor=#000000  width=100% height=100%>			
			<tr height=150 width=100% bgcolor=#ffffff >				
				<td width=20% align=center class='sheet_title' > 내용  </td><td width=80% align=left valign=top class='sheet_contents' id='sheet_contents'></td>				
			</tr>			
			</table>
		</td>
	</tr>	
	<tr height=150>
		<td colspan=2 align=center valign=top>
			
		</td>
	</tr>
</table>


";
	
}

?>