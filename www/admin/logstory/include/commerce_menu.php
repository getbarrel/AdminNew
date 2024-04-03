<?php
function commerce_munu($strPage,$treeview="",$search_html="", $report_type_use = false){
    global $SubID, $admininfo, $SelectReport;
    global $search_sdate, $search_edate, $report_group_type;
    global $vdate;

    $ca = new Calendar();
    $ca->LinkPage = $strPage;
//echo $ca->getMonthView(11, 2004);
//echo $vdate;
    if($vdate == ""){
        $vdate = date("Ymd");
    }
    if($search_html == ""){
        $searchview = "
	<Script Language='JavaScript'>


	$(function() {
		$(\"#search_sdate\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			if($('#search_edate').val() != '' && $('#search_edate').val() <= dateText){
				$('#search_edate').val(dateText);
			}
		}

		});

		$(\"#search_edate\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'

		});

	 
	});

	 
	</Script>
	";

        /*

            16 03 17
            페이지를 이동할때마다 오늘 날짜로 검색시작, 종료 날짜가 리셋되도록 변경

            if(!$_GET["search_sdate"] && !$search_sdate){
                $search_sdate = date("Ymd", time()-86400*7);
            }
            if(!$_GET["search_edate"] && !$search_edate){
                $search_edate = date("Ymd");
            }

        */

        if(empty($_GET["search_sdate"])){
            $search_sdate = date("Ymd");
        }
        if(empty($_GET["search_edate"])){
            $search_edate = date("Ymd");
        }

        $searchview .= "	<form name=frmMain target='' onsubmit='return CheckFormValue(this)' >
					<input type='hidden' name='SubID' value='$SubID'>
					<!--input type='hidden' name='mode' value='iframe'--> 
					<input type='hidden' name='SelectReport' value='4'> 
					<table cellpadding=2 cellspacing=0 width=196>
					<tr>
						<TD width=50 style='text-align:center;' nowrap> <b>시작날짜 :</b> </TD>
						<TD colspan=2 style='text-align:right;'   nowrap>
							<input type='text' name='search_sdate' class=textbox id='search_sdate' value='".$search_sdate."'  validation=false title='시작일자' style='width:100px;text-align:center;'>
						</TD>
					</tr>
					<tr>
						<TD style='text-align:center;' nowrap> <b>종료날짜 :</b> </TD>
						<TD colspan=2 style='text-align:right;'  nowrap>
						<input type='text' name='search_edate' class=textbox id='search_edate' value='".$search_edate."' size=17 validation=false title='종료일자' style='width:100px;text-align:center;'>
						</TD>
					</tr>
					<!--tr>
						<TD style='text-align:center;' nowrap> <b>VAT 포함여부 :</b> </TD>
						<TD colspan=2 style='text-align:right;'  nowrap>
						<input type='checkbox' name='search_edate' class=textbox id='search_edate' value='".$search_edate."' size=17 validation=false title='종료일자' style='width:100px;text-align:center;'>
						</TD>
					</tr-->";
        if($report_type_use){
            $searchview .= "	
					<tr>
						<TD colspan=3 style='padding:3px 0px 3px 0px; ' align=right>
						<input type=hidden name='report_group_type' id='report_group_type' value='".($report_group_type == "" ? "D":$report_group_type)."'>
						<div ".($report_group_type == "H" ? "class='report_group_type point_color'":"class='report_group_type'")." id='report_group_type_H' style='float:left;border:1px solid silver;padding:5px;width:51px;text-align:center;margin:0px 2px 2px 0px;cursor:pointer;' onclick=\"SelectReportType('H');\">시간별</div>
						<div  ".(($report_group_type == "" || $report_group_type == "D") ? "class='report_group_type point_color'":"class='report_group_type'")." id='report_group_type_D' style='float:left;border:1px solid silver;padding:5px;width:51px;text-align:center;margin:0px 2px 2px 0px;' onclick=\"SelectReportType('D');\">일자별</div>
						<div ".($report_group_type == "W" ? "class='report_group_type point_color'":"class='report_group_type'")." id='report_group_type_W' style='float:left;border:1px solid silver;padding:5px;width:51px;text-align:center;margin:0px 0px 2px 0px;' onclick=\"SelectReportType('W');\">주별</div>
						<div ".($report_group_type == "M" ? "class='report_group_type point_color'":"class='report_group_type'")." id='report_group_type_M' style='float:left;border:1px solid silver;padding:5px;width:51px;text-align:center;margin-right:2px;' onclick=\"SelectReportType('M');\">월별</div>
						<div ".($report_group_type == "P" ? "class='report_group_type point_color'":"class='report_group_type'")." id='report_group_type_P' style='float:left;border:1px solid silver;padding:5px;width:51px;text-align:center;margin-right:2px;' onclick=\"SelectReportType('P');\">분기별</div>
						<div ".($report_group_type == "Y" ? "class='report_group_type point_color'":"class='report_group_type'")." id='report_group_type_Y' style='float:left;border:1px solid silver;padding:5px;width:51px;text-align:center;' onclick=\"SelectReportType('Y');\">년별</div>
						</TD>
					</tr>";
        }
        $searchview .= "	
					<tr>
						<TD colspan=3 align=right><button style='width:100%;padding:3px;' onclick='document.frmMain.submit();'> search </button></TD>
					</tr>
					</table></form>";
    }else{
        $searchview = $search_html;
    }



    $mstring = "
<SCRIPT language=javascript id=clientEventHandlersJS>
<!--
var SMinitiallyOpenSub11464 = '".$SubID."';

function SelectReportType(select_val){
	$('.report_group_type').attr('class','report_group_type');
	$('#report_group_type').val(select_val);
	$('#report_group_type_'+select_val).attr('class','report_group_type point_color');
	$('.report_group_type').each(function(){
		
	});
}
//-->
</SCRIPT>
<SCRIPT language=javascript  src='../include/cmenu.js'></SCRIPT>
<SCRIPT language=javascript  src='../include/calender.js'></SCRIPT>
";

    $mstring .= "
	<table cellpadding=0  cellspacing=0 width=196 border=0>";
    $mstring .= "
			<tr><td align=center style='padding-bottom:5px;'><img src='../../v3/images/".$admininfo['language']."/left_title_ecommerce.gif'></td></tr>";
    if(!$search_html){
        $mstring .= "
			<tr>
			<td align='left' style='height:35px;'>								
				<div class='tab' style='position:relative;width:100%;height:29px;margin:0px;'>
				<table width='100%' class='s_org_tab'>				
				<tr>							
					<td class='tab' >
						<table id='tab_1'  style='float:left;".(($search_html) ? "display:none;":"")."' ".(($search_html || $SelectReport != 4) ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"$('#calendararea').show();$('#priodarea').hide();$('#tab_1').attr('class','on');$('#tab_2').attr('class','');ChangeReport(1);\">일자검색</td>
							<th class='box_03'></th>							
						</tr>
						</table>
						<table id='tab_2' style='float:left;' ".($SelectReport == 4 ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' style='color:#000000;' onclick=\"$('#calendararea').hide();$('#priodarea').show();$('#tab_1').attr('class','');$('#tab_2').attr('class','on');\">기간별검색</td>
							<th class='box_03'></th>				
						</tr>
						</table>
					</td>							
					<td align='right' id='test_text'>
					</td>
				</tr>
				</table>										
				</div>					
			</td>
		</tr>";
    }

    $mstring .= "</table>";

    $mstring .= "
	<table cellpadding=0 cellspacing=0 width=196 border=0 class='table_border'>
		";

    if ($search_html != ""){
        $mstring = $mstring."<TR><TD width=100% class=SM_p11464 style='overflow:auto;padding:4px;background-color:#ffffff;' id='priodarea'>".$searchview."</TD></TR>";
    }else{
        $mstring = $mstring."<TR><TD width=100% class=SM_p11464 style='overflow:auto;padding:4px;background-color:#ffffff;".($SelectReport == 4 ? "":"display:none")."' id='priodarea'>".$searchview."</TD></TR>";
        $mstring = $mstring."<TR>
											<TD width=100% id='calendararea' style='".($SelectReport != 4 ? "":"display:none")."'>
											".$ca->getMonthView(date("m", strtotime($vdate)), date("Y", strtotime($vdate)))."											
											</TD>
										</TR>";
    }


    if ($treeview != ""){
        $mstring = $mstring."<TR><TD  class='leftmenu_td_border' bgcolor=#ffffff style='overflow:auto;width:194px;'><div style='overflow:auto;width:184px;height:200px;padding:10px 5px;'>".$treeview."</div></TD></TR>";
    }



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
MenuTreeView('leftmenu_tree', 'logstory/commerce');
</script>
		";

    /*
              if ($SubID == "SM114641Sub") $dispstring = "block"; else $dispstring = "none";
    $mstring = $mstring."
               <TR>
                <TD class='leftmenu_td_border' style='text-align:left;'>
                  <DIV class=SM_p11464 id=SM114641 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\"  onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'><table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM114641Sub','SM114641')\" height='25' valign='middle'><IMG id=SM114641I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;상품별 종합분석</td><td style='padding-left:5px;' style='vertical-align:top'></td></tr></table>
                  </DIV>
                  <DIV class=SM_cb11464 id=SM114641Sub style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; POSITION: relative; HEIGHT: 250px;background-color: #edeeed;'>
                  <A href='../commerce/salesbycategory.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub30 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"salesbycategory.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle >  상품군별 분석 </DIV></A>
                  <A href='../commerce/productviewbyreferer.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146419Sub19 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"productviewbyreferer.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 유입사이트별 분석</DIV></A>
                  <A href='../commerce/salesbykeyword.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub31 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"salesbykeyword.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 키워드별 분석</DIV></A>
                  <A href='../commerce/salesbyprice.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub31 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"salesbyprice.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 가격대별 분석</DIV></A>
                  <A href='../commerce/salesbyproduct.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub30 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"salesbyproduct.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 상품판매종합 분석 </DIV></A>
                  <A href='../commerce/cartanalysis.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub30 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"cartanalysis.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 실시간장바구니 분석 </DIV></A>
                  <A href='../commerce/cartescapeanalysis.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub30 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"cartescapeanalysis.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 장바구니 이탈상품 </DIV></A>
                  <A href='../commerce/wishlist_analysis.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub30 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"wishlist_analysis.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 실시간찜상품 분석 </DIV></A>
                  <A href='../commerce/claimanalysis.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub30 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"claimanalysis.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 클레임상품 분석 </DIV></A>
                  <!--A href='../commerce/escapesalebystep.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub32 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"escapesalebystep.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 구매단계별 이탈매출</DIV></A-->
                  <A href='../commerce/maxexitbyproduct.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub33 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"maxexitbyproduct.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 최다 이탈상품</DIV></A>
                  <A href='../commerce/maxviewbyproduct.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub34 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"maxviewbyproduct.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 최다 조회상품</DIV></A>
                  <A href='../commerce/maxbuybyproduct.php?SubID=SM114641Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub34 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"maxbuybyproduct.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 최다 구매상품</DIV></A>
                  </DIV>
                  </TD>
              </TR>";

              if ($SubID == "SM11464243Sub") $dispstring = "block"; else $dispstring = "none";
    $mstring = $mstring."
              <TR>
                <TD class='leftmenu_td_border' style='text-align:left;'>
                  <DIV class=SM_p11464 id=SM11464243 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'><table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM11464243Sub','SM11464243')\" height='25' valign='middle'><IMG id=SM11464243I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;고객종합분석(CRM)</td><td style='padding-left:5px;' style='vertical-align:top'></td></tr></table></DIV>

                  <DIV class=SM_cb11464 id=SM11464243Sub
                  style='DISPLAY: ".$dispstring."; OVERFLOW: hidden; POSITION: relative; HEIGHT: 225px;background-color: #edeeed;'>
                  <A href='../commerce/loginanalysis.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464286Sub286 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"loginanalysis.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 최다로그인고객</DIV></a>
                  <A href='../commerce/durationanalysis.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464286Sub286 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"durationanalysis.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 최대체류고객</DIV></a>
                  <A href='../commerce/searchmemberlist.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464286Sub286 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"searchmemberlist.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 조회이탈고객</DIV></a>
                  <A href='../commerce/purchasestepescaper.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464264Sub264 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"purchasestepescaper.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 구매이탈고객</DIV></A>
                  <A href='../commerce/buyerlist.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464297Sub297 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"buyerlist.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 구매고객</DIV></A>
                  <A href='../commerce/memberreglist.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464275Sub275 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"memberreglist.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 신규회원가입고객</DIV></A>
                  <A href='../commerce/searchmember.php?SubID=SM11464243Sub'>
                  <DIV class=SM_c11464 id=SM11464308Sub308 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\" style='".(substr_count($_SERVER["PHP_SELF"],"searchmember.php") > 0 ? "font-weight:bold;":"")."'><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 회원검색</DIV></A>
                  <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR>

               <TR>";
               if ($SubID == "SM1146487Sub") $dispstring = "block"; else $dispstring = "none";
    $mstring = $mstring."
                <TD class='leftmenu_td_border' style='text-align:left;'>

                  <DIV class=SM_p11464 id=SM1146487
                  onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'>
                  <table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM1146487Sub','SM1146487')\" height='25' valign='middle'><IMG
                  id=SM1146487I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;매출종합분석</td><td style='padding-left:5px;' style='vertical-align:top'></td></tr></table></DIV>
                  <DIV class=SM_cb11464 id=SM1146487Sub style='DISPLAY:".$dispstring."; OVERFLOW: hidden;  POSITION: relative; HEIGHT: 150px;background-color: #edeeed;'>
                      <A href='../commerce/salessummery.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM1146497Sub97 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 매출요약</DIV></A>
                      <A href='../commerce/salesbytime.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM1146497Sub98 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 매출액(시간대)</DIV></A>
                      <A href='../commerce/salesbydate.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 매출액(일자별) </DIV></A>
                      <A href='../commerce/salesratebytime.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 구매율(시간대) </DIV></A>
                      <A href='../commerce/salesratebydate.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 구매율(일자별)</DIV></A>
                      <A href='../commerce/valuesby1person.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 1인당 고객가치 </DIV></A>
                      <A href='../commerce/buyerlist.php?SubID=SM1146487Sub'><DIV class=SM_c11464 id=SM11464108Sub108 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 1인당 고객가치 </DIV></A>
                  </DIV></TD>
                  </TR>";
              if ($SubID == "SM11464176Sub") $dispstring = "block"; else $dispstring = "none";
    $mstring = $mstring."
            <TR>
                <TD class='leftmenu_td_border' style='text-align:left;'>
                  <DIV class=SM_p11464 id=SM11464176 onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'>
                  <table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM11464176Sub','SM11464176')\" height='25' valign='middle'><IMG
                  id=SM11464176I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;기여도 분석</td><td style='padding-left:5px;' style='vertical-align:top'></td></tr></table></DIV>
                  <DIV class=SM_cb11464 id=SM11464176Sub style='DISPLAY: ".$dispstring."; OVERFLOW: hidden;  POSITION: relative;HEIGHT: 125px;background-color: #edeeed;'>
                  <A href='../commerce/summationbyreferer.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 기여도 요약</DIV></A>
                  <A href='../commerce/salesbyreferer.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464197Sub197 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 매출기여종합</DIV></A>
                  <A href='../commerce/salesanalysisbyreferer.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 매출기여분석</DIV></A>
                  <A href='../commerce/memberregbyreferer.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 회원기여종합</DIV></A>
                  <A href='../commerce/memberanalysisbyreferer.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 회원기여분석</DIV></A>
                  <A href='../report/searchenginebykeyword.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 기타관문사이트(작업중...)</DIV></A>
                  <A href='../report/searchenginebykeyword.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 상위매출기여(작업중...)</DIV></A>
                  <A href='../report/searchenginebykeyword.php?SubID=SM11464176Sub'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 상위회원기여(작업중...)</DIV></A>
                  <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR>
                ";
              if ($SubID == "SM11464177Sub") $dispstring = "block"; else $dispstring = "none";
    $mstring = $mstring."
            <TR>
                <TD class='leftmenu_td_border' style='text-align:left;'>
                  <DIV class=SM_p11464 id=SM11464177
                  onmouseover=\"SMcs11464(this, 'SM_po11464', '')\" onmouseout=\"SMcs11464(this, 'SM_p11464', '')\" style='padding-top:0px;vertical-align:top'>
                  <table cellpadding=0 cellspacing=0 border=0><tr><td onclick=\"SMpoc11464('SM11464177Sub','SM11464177')\" height='25' valign='middle'><IMG
                  id=SM11464177I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;구매/회원단계분석</td><td style='padding-left:5px;' style='vertical-align:top'></td></tr></table></DIV>
                  <DIV class=SM_cb11464 id=SM11464177Sub style='DISPLAY: ".$dispstring."; OVERFLOW: hidden;  POSITION: relative;HEIGHT: 75px;background-color: #edeeed;'>
                  <A href='../commerce/salestep.php?SubID=SM11464177Sub'>
                   <DIV class=SM_c11464 id=SM1146430Sub31 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 구매단계분석</DIV></A>
                  <A href='../commerce/escapesalebystep.php?SubID=SM11464177Sub'>
                  <DIV class=SM_c11464 id=SM1146430Sub32 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 구매단계별 이탈매출</DIV></A>
                  <A href='../commerce/maxexitbyproduct.php?SubID=SM11464177Sub'>
                  <A href='../commerce/memberregstep.php?SubID=SM11464177Sub'><DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 회원가입단계분석</DIV></A>
                  <!--A href='../manage/etcreferer.php?SubID=SM11464177Sub''><DIV class=SM_c11464 id=SM11464197Sub197 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 기타 URL 관리</DIV></A>
                  <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 사이트 이벤트 일정관리</DIV></A>
                  <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 사이트 컨텐츠 분류</DIV></A>
                  <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 키워드 오더 관리</DIV></A>
                  <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 키워드 오더 관리</DIV></A-->
                  <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR-->
                </TBODY></TABLE>";*/


    /*
    $mstring = $mstring."
            <TR>
                <TD>
                  <DIV class=SM_p11464 id=SM11464177
                  onmouseover=\"SMcs11464(this, 'SM_po11464', '')\"
                  onclick=\"SMpoc11464('SM11464177Sub','SM11464177')\"
                  onmouseout=\"SMcs11464(this, 'SM_p11464', '')\"><IMG
                  id=SM11464177I src='../../images/icon/dot_orange_triangle.gif' border=0>&nbsp;관리자모드</DIV>
                  <DIV class=SM_cb11464 id=SM11464177Sub
                  style='DISPLAY: ".$dispstring."; OVERFLOW: hidden;  POSITION: relative; TOP: 1px; HEIGHT: 150px'>
                  <A href='../manage/referer.php?SubID=SM11464177Sub'><DIV class=SM_c11464 id=SM11464186Sub186 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 레퍼러 관리</DIV></A>
                  <A href='../manage/etcreferer.php?SubID=SM11464177Sub''><DIV class=SM_c11464 id=SM11464197Sub197 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 기타 URL 관리</DIV></A>
                  <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 사이트 이벤트 일정관리</DIV></A>
                  <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 사이트 컨텐츠 분류</DIV></A>
                  <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 키워드 오더 관리</DIV></A>
                  <A href='#'><DIV class=SM_c11464 id=SM11464208Sub208 onmouseover=\"SMcs11464(this, 'SM_co11464', '')\" onmouseout=\"SMcs11464(this, 'SM_c11464', '')\"><img src='../../images/icon/left_dot.gif' border=0 align=absmiddle> 키워드 오더 관리</DIV></A>
                  <DIV class=SMEmptyDiv11464></DIV></DIV></TD></TR-->
                </TBODY></TABLE>";
    */
    return $mstring;
}

?>