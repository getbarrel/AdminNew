<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");

function ReportTable($vdate,$SelectReport=1){
    global $referer_id, $depth;
    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
    }

    if($depth == ""){
        $depth = 0;
    }else{
        //	$depth = $depth+1;
    }


    if($vdate == ""){
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $vtwoweekago = date("Ymd", time()-84600*14);
        $vfourweekago = date("Ymd", time()-84600*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)));
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-7,substr($vdate,0,4)));
        $vtwoweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-14,substr($vdate,0,4)));
        $vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-28,substr($vdate,0,4)));
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2),substr($vdate,0,4)));
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2),substr($vdate,0,4)));
    }

    if($SelectReport == 1){
        //echo $depth."<br>";
        if($depth == 1){
            $sql = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt 
					from ".TBL_COMMERCE_SALESTACK." b where vreferer_id LIKE '".substr($referer_id,0,3)."%' and  vdate = '$vdate' and step6 = 1 
					group by vdate order by vdate";

            $sql2 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt 
					from ".TBL_COMMERCE_SALESTACK." b 
					where vreferer_id LIKE '".substr($referer_id,0,3)."%' and  vdate = '$vyesterday' and step6 = 1 
					group by vdate order by vdate";

            $sql3 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." b where vreferer_id LIKE '".substr($referer_id,0,3)."%' and  vdate = '$voneweekago' and step6 = 1 group by vdate order by vdate";
        }else if($depth == 2){
            $sql = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." b where vreferer_id LIKE '".substr($referer_id,0,6)."%' and   vdate = '$vdate' and step6 = 1 group by vdate order by vdate";
            $sql2 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." b where vreferer_id LIKE '".substr($referer_id,0,6)."%' and   vdate = '$vyesterday' and step6 = 1 group by vdate order by vdate";
            $sql3 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." b where vreferer_id LIKE '".substr($referer_id,0,6)."%' and   vdate = '$voneweekago' and step6 = 1 group by vdate order by vdate";
        }else if($depth == 3){
            $sql = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." b where vreferer_id LIKE '".substr($referer_id,0,9)."%' and   vdate = '$vdate' and step6 = 1 group by vdate order by vdate";
            $sql2 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." b where vreferer_id LIKE '".substr($referer_id,0,9)."%' and   vdate = '$vyesterday' and step6 = 1 group by vdate order by vdate";
            $sql3 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." b where vreferer_id LIKE '".substr($referer_id,0,9)."%' and   vdate = '$voneweekago' and step6 = 1 group by vdate order by vdate";
        }else{
            $sql = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." b where  vdate = '$vdate' and step6 = 1 group by vdate order by vdate";
            $sql2 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." b where vdate = '$vyesterday' and step6 = 1 group by vdate order by vdate";
            $sql3 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." b where vdate = '$voneweekago' and step6 = 1 group by vdate order by vdate";
        }

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
        $title1 = "해당일";
        $title2 = "1일전";
        $title3 = "일주전";
    }else if($SelectReport == 2){


        $sql = "Select sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where vreferer_id LIKE '".substr($referer_id,0,6)."%' and vdate between '$vdate' and ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*7)." and step6 = 1  order by vdate";
        $sql2 = "Select sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where  vreferer_id LIKE '".substr($referer_id,0,6)."%' and  vdate between '$voneweekago' and ".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*7)." and step6 = 1 order by vdate";
        $sql3 = "Select sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where  vreferer_id LIKE '".substr($referer_id,0,6)."%' and  vdate between '$vfourweekago' and ".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*7)." and step6 = 1 order by vdate";

        $dateString = "주간 : ". getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        $title1 = "해당주";
        $title2 = "1주전";
        $title3 = "4주전";
    }else if($SelectReport == 3){
        $sql = "Select b.name, b.id as uid,c.pname, a.* from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_MEMBER_DETAIL." b on a.ucode = b.code, ".TBL_SHOP_PRODUCT." c where a.pid = c.id and vdate LIKE '".substr($vdate,0,6)."%' and step6 = 1 order by a.vdate, vtime";

        $sql = "Select sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where vreferer_id LIKE '".substr($referer_id,0,6)."%' and   vdate LIKE '".substr($vdate,0,6)."%' and step6 = 1 order by vdate";
        $sql2 = "Select sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where vreferer_id LIKE '".substr($referer_id,0,6)."%' and   vdate LIKE '".substr($vonemonthago,0,6)."%' and step6 = 1 order by vdate";
        $sql3 = "Select sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where vreferer_id LIKE '".substr($referer_id,0,6)."%' and   vdate LIKE '".substr($vtwomonthago,0,6)."%' and step6 = 1 order by vdate";

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
        $title1 = "해당월";
        $title2 = "1개월전";
        $title3 = "2개월전";
    }

//	echo "sql:".$sql."\n";
//	echo "sql2:".$sql2."\n";
//	echo "sql3:".$sql3."\n";

    if($sql){
        $fordb->query($sql);
    }
    if($sql2){
        $fordb2->query($sql2);
    }
    if($sql3){
        $fordb3->query($sql3);
    }



    $mstring = $mstring.TitleBar("매출기여분석",$dateString);


    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100% >\n";
    $mstring .= "<tr height=2 bgcolor=#ffffff ><td colspan=5 align=left height=30><b>".($referer_id ? getRefererCategoryPath($referer_id,4):"")."</b> 에 대한 매출 기여 분석 입니다.</td></tr>
						</table>";
    $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100% class='list_table_box'>\n";
    $mstring = $mstring."<tr height=30 align=center style='font-weight:bold'><td class=s_td width=25%>날짜</td><td class=m_td width=25%>매출액</td><td class=m_td width=25% nowrap>구매자수</td><td class=e_td width=25% nowrap>구매수량</td></tr>\n";

    if($fordb->total == 0){
        $mstring = $mstring."<tr  class='list_box_td' align=center><td colspan=4 height=200>결과값이 없습니다.</td></tr>\n";
    }else{


        $fordb->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);

        $mstring .= "
			<tr height=30 id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >$title1 </td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".number_format($fordb->dt[sales],0)."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".$fordb->dt[ucnt]."</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".$fordb->dt[cnt]."</td>
			</tr>";
        $i = $i + 1;
        $mstring .= "
			<tr height=30 id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"  nowrap>$title2 </td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView($fordb->dt[sales],$fordb2->dt[sales])."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView($fordb->dt[ucnt],$fordb2->dt[ucnt])."</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView($fordb->dt[cnt],$fordb2->dt[cnt])."</td>
			</tr>";
        $i = $i + 1;
        $mstring .= "
			<tr height=30 class='list_box_td'  align=center>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>$title3 </td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView($fordb->dt[sales],$fordb3->dt[sales])."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView($fordb->dt[ucnt],$fordb3->dt[ucnt])."</td>
			<td onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView($fordb->dt[cnt],$fordb3->dt[cnt])."</td>
			</tr>
			";

        /*
                $mstring .= "<tr height=30>
                <td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt[step6]."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
                </tr>\n";
        */
        //	$exitcnt = $pageview01 + returnZeroValue($fordb->dt[visit_cnt]);


    }

    $mstring = $mstring."</table><br>\n";
//	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=745  >\n";
//	if ($pageview01 == 0){
//		$mstring = $mstring."<tr height=50 class='list_box_td'  align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}
//	$mstring = $mstring."<tr height=2 class='list_box_td'  align=center><td colspan=5 width=190></td></tr>\n";
//	$mstring = $mstring."<tr height=25 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
//	$mstring = $mstring."</table>\n";
    /*
    $help_text = "
    <table>
        <tr>
            <td style='line-height:150%'>
            - 매출기여분석 이란? 유입사이트 그룹별 또는 유입사이트별 매출액 및 구매자수를 1준전과 4주전(한달전) 으로 비교 분석해 볼수 있는 리포트 입니다.<br>
            - 빨간색 그래프는 1주전 대비 도달률, 4주전 대비 도달률을 나타냅니다.<br>
            - 매출기여분석은 구매건수와 매출액으로 나타내진다<br><br><br>
            </td>
        </tr>
    </table>
    ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("매출기여분석", $help_text);

    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->SelectReport = $SelectReport;
    $ca->LinkPage = 'salesanalysisbyreferer.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('salesanalysisbyreferer.php', "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode('salesanalysisbyreferer.php',date("Ymd", time()))."</div>");//.text_button("#", "test","190").colorCirCleBoxStart("#efefef",190)."test<br>test<br>test<br>test<br>test<br>".colorCirCleBoxEnd("#efefef");
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>";
    $p->Navigation = "이커머스분석 > 기여도분석 > 매출기여분석";
    $p->title = "매출기여분석";
    $p->PrintReportPage();
}
?>
