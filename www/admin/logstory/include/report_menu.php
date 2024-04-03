<?php
function Stat_munu($strPage,$treeview=""){
    global $SubID, $admininfo ;
    $ca = new Calendar();
    $ca->LinkPage = $strPage;
//echo $ca->getMonthView(11, 2004);

    $vdate = $_GET['vdate'];
    if($vdate !=''){
        $vyear = substr($vdate, 0, 4);
        $vmonth = substr($vdate, 4, 2);

    }else{
        $vyear = date("Y", time());
        $vmonth = date("m", time());
    }
    $mstring = "
<SCRIPT language=javascript id=clientEventHandlersJS>
<!--
var SMinitiallyOpenSub11464 = '".$SubID."';
//-->
</SCRIPT>
<SCRIPT language=javascript  src='../include/menu.js'></SCRIPT>
<SCRIPT language=javascript  src='../include/calender.js'></SCRIPT>
";
    $mstring .= "
	<table cellpadding=0  cellspacing=0 width=196 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='../../v3/images/".$_SESSION['admininfo']['language']."/left_title_log.gif'></td></tr>
	</table>
	<table cellpadding=0 cellspacing=0 width=196 border=0 class='table_border'>
		  <TR><TD width=100% id='calendararea'>".$ca->getMonthView($vmonth,$vyear)."</TD></TR>
		  <TR>
		    <TD height=30 align=center style='padding-left:5px;BORDER-TOP: #ffffff 1px solid;background-color:#edeeed;' class=login1>
		    		<table cellSpacing=0 cellPadding=0 border=0 width=100% bgcolor='#edeeed'>
		    			<tr>
		    				<!--td>
						<div id='revolution' style=\"position:relative;top:0pt;width:140px;left:20px;text-align:center;font-family:Arial black;font-size:9pt;color:white;line-height:1;filter:glow(color=black,strength=2)\">
						SITE ANALISYS
						</div>
						</td-->
						<td align='left'>
						<img src='../img/icon_q.gif' border=0 style='cursor:pointer;' onMouseMove=\"mouseMove();\" onMouseOver=\"overdp('menu_view'); return true;\" onMouseOut=outdp();>
						 <div id='menu_view' style='position:absolute; width:200px;height:10px; z-index:100; visibility: hidden'>
		                              <table width='200' border='0' cellspacing='1' cellpadding='0' bgcolor='#666666'>
		                                <tr>
		                                  <td>
		                                    <table width='100%' border='0' cellspacing='5' cellpadding='0' bgcolor='#EEEEEE'>
		                                      <tr>
		                                        <td bgcolor='#FAFAFA'>
		                                          <table width='100%' border='0' cellspacing='10' cellpadding='0'>
		                                            <tr>
		                                              <td align='center' height='25' bgcolor='#EEEEEE' class='yellow16'><b>칼렌다 메뉴 설명</b></td>
		                                            </tr>
		                                            <tr>
		                                              <td class='td16'>
		                                              <li> ' ".date("F")." ".date("Y")."' 을 누르면 월간 리포트를 보실수 있습니다.
		                                              <li> 'DAILY' 를 선택하신후 각 날짜를 누르시면 일간 리포트를 보실수 있습니다.
		                                              <li> 'WEEKLY' 를 선택하신후 주 단위로 날짜를 선택하시면 주간 리포트를 보실수 있습니다.
		                                              </td>
		                                            </tr>
		                                            <tr>
		                                              <td height='1' bgcolor='#EEEEEE'></td>
		                                            </tr>
		                                            <!--tr>
		                                              <td class='td16'>자세히 보시려면 Click !</td>
		                                            </tr-->
		                                          </table>
		                                        </td>
		                                      </tr>
		                                    </table>
		                                  </td>
		                                </tr>
		                              </table>
		                              </div>
						</td>
					</tr>
				</table>
		    </TD>
		  </TR>
		  ";
    if ($treeview != ""){
        $mstring = $mstring."<TR><TD width=100% bgcolor=#ffffff style='overflow:auto;width:290px;padding:10px;'>".$treeview."</TD></TR>";
    }
    $mstring = $mstring."</table>";


    $mstring .= "<table cellpadding=0  cellspacing=0 width=196 border=0>
							<tr height=23 bgcolor='#FFFFFF'>
								<td style='border-collapse:separate; border:1px solid #c0c0c0;padding:5px 5px 15px 5px;' align=left >
									<div style='float:left;border:1px solid gray;background-color:#efefef;width:44%;text-align:center;padding:4px;'><a href='#' id='btnExpandAll'>전체열기</a></div>
									<div style='float:left;border:1px solid gray;background-color:#efefef;width:44%;text-align:center;padding:4px;margin-left:2px;'><a href='#' id='btnCollapseAll'>전체닫기</a></div>
									<div id='leftmenu_tree'  style='overflow:auto;width:180px;height:950px;border: 0px solid silver;text-align:left;'></div>
								</td>
							</tr>";
    $mstring.= "</table>	
<link href='../../lib/dynatree/skin/ui.dynatree.css' rel='stylesheet' type='text/css' id='skinSheet'>
<script src='../../lib/dynatree/jquery.dynatree.js' type='text/javascript'></script>
<script language='javascript'>
MenuTreeView('leftmenu_tree', 'logstory/report');
</script>
		";
    /*
              if ($SubID == "SM114641Sub") $dispstring = "block"; else $dispstring = "none";
    $mstring = $mstring."
               <TR>
                <TD class='leftmenu_td_border' style='text-align:left;'>
                  <DIV class=SM_p11464 id=SM114641 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\"  onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='width:100%;padding-top:0px;vertical-align:top'><table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM114641Sub','SM114641')\" height='25' valign='middle'><IMG id=SM11464243I src='../../images/icon/dot_orange_triangle.gif' border=0 align='abstop'>&nbsp;페이지 분석</td><td style='padding-left:5px;' style='vertical-align:top'><!--img src='/admin/image/btn_spop_manage.gif'--></td></tr></table></DIV>
                  <DIV class=SM_cb11464 id=SM114641Sub style='DISPLAY: ".$dispstring."; OVERFLOW: hidden;  POSITION: relative; HEIGHT: 172px;background-color: #edeeed;'>

                  <DIV class=SM_c11464 id=SM1146430Sub30 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><A href='../report/pageview1.php?SubID=SM114641Sub'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 페이지뷰</A></DIV>
                  <A href='../report/pageviewbypage.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146419Sub19 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 페이지별 페이지뷰</DIV></A>
                   <A href='../report/durationbypageview.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146419Sub19 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 페이지뷰 당 체류시간</DIV></A>
                  <A href='../report/toppage.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub31 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 자주찾는 페이지</DIV></A>
                  <A href='../report/lowpageview.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub32 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 자주찾지않는 페이지</DIV></A>
                  <A href='../report/nonpageview.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub33 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 비요청 페이지</DIV></A>
                  <A href='../report/newpage.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub34 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 신규등록 페이지</DIV></A>
                  </DIV>
                  </TD>
              </TR>";

              if ($SubID == "SM11464243Sub") $dispstring = "block"; else $dispstring = "none";
    $mstring = $mstring."
              <TR>
                <TD class='leftmenu_td_border' style='text-align:left;'>
                  <DIV class=SM_p11464 id=SM11464243 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onclick=\"SMpoc11464('SM11464243Sub','SM11464243')\"
                  onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'>
                  <table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM11464243Sub','SM11464243')\" height='25' valign='middle'><IMG id=SM11464243I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;방문자(회수) 분석</td><td style='padding-left:5px;' style='vertical-align:top'><!--img src='/admin/image/btn_spop_manage.gif'--></td></tr></table>
                  </DIV>

                  <DIV class=SM_cb11464 id=SM11464243Sub
                  style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; POSITION: relative; HEIGHT: 100px;background-color: #edeeed;'>
                  <A href='../report/visit.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464286Sub286 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 방문횟수</DIV></a>
                  <A href='../report/pageviewbyvisit.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464264Sub264 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 방문횟수당 페이지뷰</DIV></A>
                  <A href='../report/durationbyvisit.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464297Sub297 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 방문횟수당 체류시간</DIV></A>
                  <A href='../report/revisit.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464275Sub275 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 재방문 횟수</DIV></A>
                  <!--A href='#'>
                  <DIV class=SM_c11464 id=SM11464308Sub308 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">-</DIV></A-->
                  <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR>

               <TR>";
               if ($SubID == "SM1146487Sub") $dispstring = "block";
               else $dispstring = "none";
    $mstring = $mstring."
                <TD class='leftmenu_td_border' style='text-align:left;'>

                  <DIV class=SM_p11464 id=SM1146487 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\"  onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'>
                  <table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM1146487Sub','SM1146487')\" height='25' valign='middle'><IMG id=SM1146487I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;순수 방문자 분석</td><td style='padding-left:5px;' style='vertical-align:top'><!--img src='/admin/image/btn_spop_manage.gif'--></td></tr></table>
                  </DIV>
                  <DIV class=SM_cb11464 id=SM1146487Sub
                  style='DISPLAY:".$dispstring."; OVERFLOW: hidden; POSITION: relative;height:125px;background-color: #edeeed;'>
                  <A href='../report/visitor.php?SubID=SM1146487Sub'>
                  <DIV class=SM_c11464 id=SM1146497Sub97 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">
                    <img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 순수방문자</DIV></A>
                  <A href='../report/durationbyvisitor.php?SubID=SM1146487Sub'>
                  <DIV class=SM_c11464 id=SM1146497Sub98 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">
                    <img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 순수방문자당 체류시간 </DIV></A>
                  <A href='../report/pageviewbyvisitor.php?SubID=SM1146487Sub'>
                  <DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">
                    <img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 순수방문자당 페이지뷰  </DIV></A>
                  <A href='../report/visitor_list.php?SubID=SM1146487Sub'>
                  <DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">
                     <img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 방문자 IP 리스트 </DIV></A>
                  <A href='../report/realtime_visitor_list.php?SubID=SM1146487Sub'>
                  <DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">
                    <img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 실시간 방문자 리스트  </DIV></A>
                  </DIV>
                      </TD>
                  </TR>";
              if ($SubID == "SM11464176Sub") $dispstring = "block"; else $dispstring = "none";
    $mstring = $mstring."
            <TR>
                <TD class='leftmenu_td_border' style='text-align:left;'>
                  <DIV class=SM_p11464 id=SM11464176 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'>
                  <table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM11464176Sub','SM11464176')\" height='25' valign='middle'><IMG id=SM11464176I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;유입사이트 분석</td><td style='padding-left:5px;' style='vertical-align:top'><!--img src='/admin/image/btn_spop_manage.gif'--></td></tr></table>
                  </DIV>
                  <DIV class=SM_cb11464 id=SM11464176Sub
                  style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; POSITION: relative; HEIGHT: 125px;background-color: #edeeed;'>
                  <A href='../report/visitbyreferer.php?SubID=SM11464176Sub'>
                  <DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 유입사이트별 방문횟수</DIV></A>
                  <A href='../report/etcreferer.php?SubID=SM11464176Sub'>
                  <DIV class=SM_c11464 id=SM11464197Sub197 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 기타방문 URL</DIV></A>
                  <A href='../report/keyword.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 키워드별 방문횟수</DIV></A>
                  <A href='../report/keywordbysearchengine.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 검색엔진별 키워드</DIV></A>
                  <A href='../report/searchenginebykeyword.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 키워드별 검색엔진</DIV></A>
                  <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR>
                ";
            if ($SubID == "SM11464275Sub") $dispstring = "block"; else $dispstring = "none";
    $mstring = $mstring."
            <TR>
                <TD class='leftmenu_td_border' style='text-align:left;'>
                  <DIV class=SM_p11464 id=SM11464275 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'>
                  <table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM11464275Sub','SM11464275')\" height='25' valign='middle'><IMG id=SM11464275I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;배너 분석</td><td style='padding-left:5px;' style='vertical-align:top'><!--img src='/admin/image/btn_spop_manage.gif'--></td></tr></table>
                  </DIV>
                  <DIV class=SM_cb11464 id=SM11464275Sub
                  style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; POSITION: relative; HEIGHT: 50px;background-color: #edeeed;'>
                  <A href='../report/banner_main.php?SubID=SM11464275Sub'>
                  <DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 메인페이지 배너분석</DIV></A>
                  <A href='../report/bannerclick.php?SubID=SM11464275Sub'>
                  <DIV class=SM_c11464 id=SM11464197Sub197 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 배너 클릭수 분석</DIV></A>
                  <!--A href='../report/keyword.php?SubID=SM11464275Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 키워드별 방문횟수</DIV></A>
                  <A href='../report/keywordbysearchengine.php?SubID=SM11464275Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 검색엔진별 키워드</DIV></A>
                  <A href='../report/searchenginebykeyword.php?SubID=SM11464275Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 키워드별 검색엔진</DIV></A-->
                  <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR>
                ";



              if ($SubID == "SM11464177Sub") $dispstring = "block"; else $dispstring = "none";
    $mstring = $mstring."
            <TR>
                <TD class='leftmenu_td_border' style='text-align:left;'>
                  <DIV class=SM_p11464 id=SM11464177 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'>
                  <table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM11464177Sub','SM11464177')\" height='25' valign='middle'><IMG id=SM11464177I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;관리자모드</td><td style='padding-left:5px;' style='vertical-align:top'><!--img src='/admin/image/btn_spop_manage.gif'--></td></tr></table>
                  </DIV>
                  <DIV class=SM_cb11464 id=SM11464177Sub
                  style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; POSITION: relative; HEIGHT: 75px;background-color: #edeeed;'>
                  <A href='../manage/referer.php?SubID=SM11464177Sub'><DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 레퍼러 관리</DIV></A>
                  <A href='../manage/page.php?SubID=SM11464177Sub'><DIV class=SM_c11464 id=SM11464197Sub197 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 페이지 정보 관리</DIV></A>
                  <A href='../manage/etcreferer.php?SubID=SM11464177Sub'><DIV class=SM_c11464 id=SM11464197Sub197 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 기타 URL 관리</DIV></A>
                  <!--A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 사이트 이벤트 일정관리</DIV></A>
                  <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 사이트 컨텐츠 분류</DIV></A>
                  <A href='../manage/keyword.php?SubID=SM11464177Sub''><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 키워드 오더 관리</DIV></A>
                  <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\">&nbsp;&nbsp;&nbsp;키워드 오더 관리</DIV></A-->
                  <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR-->
                </TBODY></TABLE>";
    */

    return $mstring;
}

?>