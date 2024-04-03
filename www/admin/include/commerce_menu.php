<?
function commerce_munu($strPage,$treeview=""){
global $SubID;
$ca = new Calendar();
$ca->LinkPage = $strPage;
//echo $ca->getMonthView(11, 2004);
$mstring = "
<SCRIPT language=javascript id=clientEventHandlersJS>
<!--
var SMinitiallyOpenSub11464 = '".$SubID."';
//-->
</SCRIPT>
<SCRIPT language=javascript  src='../include/cmenu.js'></SCRIPT>
<SCRIPT language=javascript  src='../include/calender.js'></SCRIPT>
";
$mstring = $mstring."
		<TABLE cellSpacing=0 cellPadding=0 border=0 bgcolor=#6699cc width=190>
		  <TBODY>
		  <TR height=30>
		    <TD style='padding-left:5px' align=center style='padding-left:5px;BORDER-TOP: #ffffff 1px solid;BORDER-left: #ffffff 1px solid;Border-right:1px solid #000000;Border-bottom:1px solid #000000;' class=login1>
						<div id='revolution' style=\"position:relative;top:0pt;width:140px;left:0px;text-align:center;font-family:Arial black;font-size:9pt;color:white;line-height:1;filter:glow(color=black,strength=2)\">
						STATISTIC
						</div>
		    </TD>
		  </TR>
		  <TR><TD width=100% id='calendararea'>".$ca->getMonthView(date("m", time()), date("Y", time()))."</TD></TR>";
		if ($treeview != ""){
$mstring = $mstring."<TR><TD width=100% bgcolor=#ffffff style='overflow:auto;width:290;'>".$treeview."</TD></TR>";
		}

		  if ($SubID == "SM114641Sub") $dispstring = "block"; else $dispstring = "none";
$mstring = $mstring."
		   <TR>
		    <TD>
		      <DIV class=SM_p11464 id=SM114641 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onclick=\"SMpoc11464('SM114641Sub','SM114641')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\">
		      <IMG id=SM114641I src='../image/arrow_blue.gif' border=0>&nbsp;&nbsp;상품분석</DIV>
		      <DIV class=SM_cb11464 id=SM114641Sub style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 0px'>
		      <A href='../commerce/salesbyproduct.php?SubID=SM114641Sub'>
		      <DIV class=SM_c11464 id=SM1146430Sub30 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;상품군별 분석 </DIV></A>
		      <A href='../commerce/productviewbyreferer.php?SubID=SM114641Sub'>
		      <DIV class=SM_c11464 id=SM1146419Sub19 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;상품군별 기여분석</DIV></A>
		      <A href='../commerce/salestep.php?SubID=SM114641Sub'>
		      <DIV class=SM_c11464 id=SM1146430Sub31 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;구매단계분석</DIV></A>
		      <A href='../commerce/escapesalebystep.php?SubID=SM114641Sub'>
		      <DIV class=SM_c11464 id=SM1146430Sub32 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;구매단계별 이탈매출</DIV></A>
		      <A href='../commerce/maxexitbyproduct.php?SubID=SM114641Sub'>
		      <DIV class=SM_c11464 id=SM1146430Sub33 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;최다 이탈상품</DIV></A>
		      <A href='../commerce/maxviewbyproduct.php?SubID=SM114641Sub'>
		      <DIV class=SM_c11464 id=SM1146430Sub34 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;최다 조회상품</DIV></A>
		      <A href='../commerce/maxbuybyproduct.php?SubID=SM114641Sub'>
		      <DIV class=SM_c11464 id=SM1146430Sub34 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;최다 구매상품</DIV></A>
		      </DIV>
		      </TD>
		  </TR>";

		  if ($SubID == "SM11464243Sub") $dispstring = "block"; else $dispstring = "none";
$mstring = $mstring."
		  <TR>
		    <TD>
		      <DIV class=SM_p11464 id=SM11464243 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onclick=\"SMpoc11464('SM11464243Sub','SM11464243')\"
		      onmouseout=\"SMcs11464(this, 'SM_p11464', '')\">

		      <IMG id=SM11464243I src='../image/arrow_blue.gif' border=0>&nbsp;&nbsp;고객리스트</DIV>

		      <DIV class=SM_cb11464 id=SM11464243Sub
		      style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 125px'>
		      <A href='../report/visit.php?SubID=SM11464243Sub'>
		      <DIV class=SM_c11464 id=SM11464286Sub286 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;조회이탈고객(작업중...)</DIV></a>
		      <A href='../commerce/purchasestepescaper.php?SubID=SM11464243Sub'>
		      <DIV class=SM_c11464 id=SM11464264Sub264 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;구매이탈고객</DIV></A>
		      <A href='../commerce/buyerlist.php?SubID=SM11464243Sub'>
		      <DIV class=SM_c11464 id=SM11464297Sub297 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;구매고객</DIV></A>
		      <A href='../report/etcreferer.php?SubID=SM11464243Sub'>
		      <DIV class=SM_c11464 id=SM11464275Sub275 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;신규회원가입고개(작업중...)</DIV></A>
		      <A href='#'>
		      <DIV class=SM_c11464 id=SM11464308Sub308 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;회원검색(작업중...)</DIV></A>
		      <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR>

		   <TR>";
		   if ($SubID == "SM1146487Sub") $dispstring = "block"; else $dispstring = "none";
$mstring = $mstring."
		    <TD>

		      <DIV class=SM_p11464 id=SM1146487
		      onmouseover=\"SMcs11464(this, 'SM_po11464', '')\"
		      onclick=\"SMpoc11464('SM1146487Sub','SM1146487')\"
		      onmouseout=\"SMcs11464(this, 'SM_p11464', '')\"><IMG
		      id=SM1146487I src='../image/arrow_blue.gif' border=0>&nbsp;&nbsp;매출종합분석 </DIV>
		      <DIV class=SM_cb11464 id=SM1146487Sub style='DISPLAY:".$dispstring."; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 150px'>
			      <A href='../commerce/salessummery.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM1146497Sub97 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;매출요약</DIV></A>
			      <A href='../commerce/salesbytime.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM1146497Sub98 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;매출액(시간대)</DIV></A>
			      <A href='../commerce/salesbydate.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;매출액(일자별) </DIV></A>
			      <A href='../commerce/salesratebytime.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;구매율(시간대) </DIV></A>
			      <A href='../commerce/salesratebydate.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;구매율(일자별)</DIV></A>
			      <A href='../commerce/valuesby1person.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;1인당 고객가치 </DIV></A>
			      <A href='../commerce/buyerlist.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;1인당 고객가치 </DIV></A>
		      </DIV></TD>
		      </TR>";
		  if ($SubID == "SM11464176Sub") $dispstring = "block"; else $dispstring = "none";
$mstring = $mstring."
		<TR>
		    <TD>
		      <DIV class=SM_p11464 id=SM11464176
		      onmouseover=\"SMcs11464(this, 'SM_po11464', '')\"
		      onclick=\"SMpoc11464('SM11464176Sub','SM11464176')\"
		      onmouseout=\"SMcs11464(this, 'SM_p11464', '')\"><IMG
		      id=SM11464176I src='../image/arrow_blue.gif' border=0>&nbsp;&nbsp;기여도 분석</DIV>
		      <DIV class=SM_cb11464 id=SM11464176Sub style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 125px'>
		      <A href='../commerce/salesbyreferer.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;요약</DIV></A>
		      <A href='../report/etcreferer.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464197Sub197 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;매출기여종합(작업중...)</DIV></A>
		      <A href='../report/keyword.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;매출기여분석(작업중...)</DIV></A>
		      <A href='../report/keywordbysearchengine.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;회원기여종합(작업중...)</DIV></A>
		      <A href='../report/searchenginebykeyword.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;회원기여분석(작업중...)</DIV></A>
		      <A href='../report/searchenginebykeyword.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;기타관문사이트(작업중...)</DIV></A>
		      <A href='../report/searchenginebykeyword.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;상위매출기여(작업중...)</DIV></A>
		      <A href='../report/searchenginebykeyword.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;상위회원기여(작업중...)</DIV></A>
		      <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR>
		    ";
		  if ($SubID == "SM11464177Sub") $dispstring = "block"; else $dispstring = "none";
$mstring = $mstring."
		<TR>
		    <TD>
		      <DIV class=SM_p11464 id=SM11464177
		      onmouseover=\"SMcs11464(this, 'SM_po11464', '')\"
		      onclick=\"SMpoc11464('SM11464177Sub','SM11464177')\"
		      onmouseout=\"SMcs11464(this, 'SM_p11464', '')\"><IMG
		      id=SM11464177I src='../image/arrow_blue.gif' border=0>&nbsp;&nbsp;관리자모드</DIV>
		      <DIV class=SM_cb11464 id=SM11464177Sub
		      style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; WIDTH: 190px; POSITION: relative; TOP: 0px; HEIGHT: 150px'>
		      <A href='../manage/referer.php?SubID=SM11464177Sub'><DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;레퍼러 관리</DIV></A>
		      <A href='../manage/etcreferer.php?SubID=SM11464177Sub''><DIV class=SM_c11464 id=SM11464197Sub197 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;기타 URL 관리</DIV></A>
		      <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;사이트 이벤트 일정관리</DIV></A>
		      <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;사이트 컨텐츠 분류</DIV></A>
		      <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;키워드 오더 관리</DIV></A>
		      <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;키워드 오더 관리</DIV></A>
		      <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR-->
		    </TBODY></TABLE>";

   return $mstring;
}

?>