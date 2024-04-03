<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");

function ReportTable($vdate,$SelectReport=1){
    global $search_sdate, $search_edate;

    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();
    $fordb4 = new forbizDatabase();
    $fordb5 = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
    }
    if($vdate == ""){
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $vtwoweekago = date("Ymd", time()-84600*14);
        $vfourweekago = date("Ymd", time()-84600*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2),substr($vdate,0,4)));
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2),substr($vdate,0,4)));
    }else{
        if($SelectReport ==3){
            $vdate = substr($vdate,0,6)."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
        $vtwoweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*14);
        $vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2),substr($vdate,0,4)));
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2),substr($vdate,0,4)));
    }

    if($SelectReport == 1){
        $nLoop = 24;
    }else if($SelectReport ==2){
        $nLoop = 7;
    }else if($SelectReport ==3){
        $nLoop = date("t", mktime(0, 0, 0, substr($vdate,4,2), substr($vdate,6,2), substr($vdate,0,4)));
    }else if($SelectReport ==4){
        $timestamp_search_sdate = mktime(0, 0, 0, substr($search_sdate,4,2), substr($search_sdate,6,2), substr($search_sdate,0,4));
        $timestamp_search_edate = mktime(0, 0, 0, substr($search_edate,4,2), substr($search_edate,6,2), substr($search_edate,0,4));
        $nLoop = ($timestamp_search_edate-$timestamp_search_sdate)/86400;

    }

    if($SelectReport == 1){
        $sql = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$vdate' and step6 = 1 group by vdate order by vdate";
        $sql2 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$vyesterday' and step6 = 1 group by vdate order by vdate";
        $sql3 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$voneweekago' and step6 = 1 group by vdate order by vdate";


        $sql4 = "Select
				sum(case when vdate = '$vdate' then ncnt else 0 end) as today,
				sum(case when vdate = '$vyesterday' then ncnt else 0 end) as yesterday,
				sum(case when vdate = '$voneweekago' then ncnt else 0 end) as oneweeksago
				from ".TBL_LOGSTORY_VISITTIME."";

        $sql5 = "Select
				sum(case when vdate = '$vdate' then ncnt else 0 end) as today,
				sum(case when vdate = '$vyesterday' then ncnt else 0 end) as yesterday,
				sum(case when vdate = '$voneweekago' then ncnt else 0 end) as oneweeksago
				from ".TBL_LOGSTORY_VISITOR."";

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
        $title1 = "해당일";
        $title2 = "1일전";
        $title3 = "일주전";
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }
        $sql = "SELECT vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_COMMERCE_SALESTACK." c where vdate between '$vdate' and '$vweekenddate' and step6 = 1 group by vdate ";

        $sql2 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end) ";
        for($i=2;$i < $nLoop;$i++){
            $sql2 .= ", sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        /*
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then ncnt else 0 end)
        */

        $sql2 .= "	from ".TBL_LOGSTORY_VISITTIME." ";

        $sql3 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end) ";
        for($i=2;$i < $nLoop;$i++){
            $sql3 .= ", sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        /*
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then ncnt else 0 end)
        */
        $sql3 .= " 	from ".TBL_LOGSTORY_VISITOR." ";

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }

    }else if($SelectReport == 3){
        $sql = "SELECT c.vdate as vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_COMMERCE_SALESTACK." c where vdate LIKE '".substr($vdate,0,6)."%' and step6 = 1 group by c.vdate ";

        $sql2 = "Select vdate, ncnt from ".TBL_LOGSTORY_VISITTIME." where vdate LIKE '".substr($vdate,0,6)."%'";

        $sql3 = "Select vdate, ncnt from ".TBL_LOGSTORY_VISITOR." where vdate LIKE '".substr($vdate,0,6)."%'";
        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

    }





    $mstring = $mstring.TitleBar("1인당 고객가치",$dateString);

    if($SelectReport == 1){
        $fordb->query($sql);
        $fordb2->query($sql2);
        $fordb3->query($sql3);
        $fordb4->query($sql4);
        $fordb5->query($sql5);

        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  border=0 class='list_table_box' >\n";
        $mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=20%>시간</td><td class=m_td width=30% nowrap>구매고객1인당 매출액(원)</td><td class=m_td width=20%>1방문당 매출액(원)</td><td class=e_td width=30% nowrap>방문고객 1인당 매출액(원)</td></tr>\n";
        //$mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=150>날짜</td><td class=m_td width=150 nowrap>구매고객 1인당 매출액</td><td class=m_td width=165>1방문횟수당 매출액</td><td class=e_td width=150 nowrap>방문고객 1인당 매출액</td></tr>\n";

//	if($fordb->total == 0){
//		$mstring .= "<tr height=50 class='list_box_td'  align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}else{


        $fordb->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);
        $fordb4->fetch(0);
        $fordb5->fetch(0);



        $salerate = @number_format((returnZeroValue($fordb->dt['ucnt'])/returnZeroValue($fordb4->dt['today'])*100),2);
        $salerate2 = @number_format((returnZeroValue($fordb2->dt['ucnt'])/returnZeroValue($fordb4->dt['yesterday'])*100),2);
        $salerate3 = number_format((returnZeroValue($fordb3->dt['ucnt'])/returnZeroValue($fordb4->dt['oneweeksago'])*100),2);
        $i=0;
        $mstring .= "
			<tr height=30 id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap align='center'>$title1 </td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".($fordb->dt['ucnt'] == 0 ? "0":number_format($fordb->dt['sales']/$fordb->dt['ucnt'],0))."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=center>".($fordb4->dt['today'] == 0 ? "0":number_format($fordb->dt['sales']/$fordb4->dt['today'],0))."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".number_format($fordb->dt['sales']/$fordb5->dt['today'],0)."</td>
			</tr>";
        $i = $i + 1;
        $mstring .= "
			<tr height=30 id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap align='center'>$title2 </td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".BarchartView(returnZeroValue(($fordb->dt['ucnt'] == 0 ? "0":$fordb->dt['sales']/$fordb->dt['ucnt'])),returnZeroValue(($fordb2->dt['ucnt'] == 0 ? "0":$fordb2->dt['sales']/$fordb2->dt['ucnt'])))."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=right>".@BarchartView($fordb->dt['sales']/$fordb4->dt['today'],$fordb2->dt['sales']/$fordb4->dt['yesterday'])."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".@BarchartView(returnZeroValue($fordb->dt['sales']/$fordb5->dt['today']),returnZeroValue($fordb2->dt['sales']/$fordb5->dt['yesterday']))."</td>
			</tr>";
        $i = $i + 1;
        $mstring .= "
			<tr height=30 id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap align='center'>$title3 </td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".BarchartView(returnZeroValue(($fordb->dt['ucnt'] == 0 ? "0":$fordb3->dt['sales']/$fordb->dt['ucnt'])),returnZeroValue(($fordb3->dt['ucnt'] == 0 ? "0":$fordb3->dt['sales']/$fordb3->dt['ucnt'])))."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=right>".@BarchartView($fordb->dt['sales']/$fordb4->dt['today'],$fordb3->dt['sales']/$fordb4->dt['oneweeksago'])."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".@BarchartView(returnZeroValue($fordb->dt['sales']/$fordb4->dt['today']),returnZeroValue($fordb3->dt['sales']/$fordb4->dt['oneweeksago']))."</td>
			</tr>
			";

        /*
                $mstring .= "<tr height=30>
                <td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt['step6']."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
                </tr>\n";
        */
        //	$exitcnt = $pageview01 + returnZeroValue($fordb->dt['visit_cnt']);


        //}

        $mstring .= "</table>\n<br>";

    }else if ($SelectReport == 2 || $SelectReport == 4){
        $fordb->query($sql);
        $fordb2->query($sql2);
        $fordb3->query($sql3);
        //echo $sql;

        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%   class='list_table_box'>\n";
        $mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=20%>시간</td><td class=m_td width=30% nowrap>구매고객1인당 매출액(원)</td><td class=m_td width=20%>1방문당 매출액(원)</td><td class=e_td width=30% nowrap>방문고객 1인당 매출액(원)</td></tr>\n";

        if($fordb->total == 0){
            $mstring .= "<tr class='list_box_td'  align=center><td colspan=4 height=100 >결과값이 없습니다.</td></tr>\n";
        }else{
            $j = 0;
            $fordb->fetch($j);
            $fordb2->fetch(0);
            $fordb3->fetch(0);
            for($i=0;$i <= $nLoop;$i++){

                if($fordb->dt['vdate'] == date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)){
                    $mstring .= "
				<tr height=30 id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate,"datename")."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".number_format(returnZeroValue($fordb->dt['sales']/$fordb->dt['ucnt']),0)."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' >".($fordb2->dt[$i] > 0 ? number_format(returnZeroValue($fordb->dt['sales']/$fordb2->dt[$i]),0):"0")."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".($fordb3->dt[$i] > 0 ? number_format((returnZeroValue($fordb->dt['sales']/$fordb3->dt[$i])),2) :"0")."</td>
				</tr>";

                    if($fordb->dt['ucnt'] > 0){
                        $values1 = $values1 + returnZeroValue($fordb->dt['sales']/$fordb->dt['ucnt']);
                    }
                    if($fordb2->dt[$i] > 0){
                        $values2 = $values2 + returnZeroValue($fordb->dt['sales']/$fordb2->dt[$i]);
                    }
                    if($fordb3->dt[$i] > 0){
                        $values3 = $values3 + returnZeroValue($fordb->dt['sales']/$fordb3->dt[$i]);
                    }
                    $j = $j + 1;
                    $fordb->fetch($j);
                }else{
                    $mstring .= "
				<tr height=30  id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate,"datename")."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>-</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>-</td>
				</tr>";

                    //$values1 = $values1 + returnZeroValue($fordb->dt['sales']/$fordb->dt['ucnt']);
                }

            }

        }

        $mstring .= "<tr height=30 align=center >
	<td class=s_td >합계</td>
	<td class=m_td style='padding-right:20px'>".number_format(returnZeroValue($values1),0)."</td>
	<td class=m_td style='padding-right:20px'>".number_format($values2,0)."</td>
	<td class=e_td style='padding-right:20px'>".number_format(returnZeroValue($values3),0)."</td>
	</tr>\n";
        $mstring .= "</table><br>\n";
    }else if ($SelectReport == 3){

        $fordb->query($sql);
        $fordb2->query($sql2);
        $fordb3->query($sql3);

        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box' >\n";
        $mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=19%>시간</td><td class=m_td width=27% nowrap>구매고객1인당 매출액(원)</td><td class=m_td width=27%>1방문당 매출액(원)</td><td class=e_td width=27% nowrap>방문고객 1인당 매출액(원)</td></tr>\n";

        if($fordb->total == 0){
            $mstring .= "<tr class='list_box_td'  align=center><td colspan=4 height=100 >결과값이 없습니다.</td></tr>\n";
        }else{
            $j = 0;
            $fordb->fetch($j);

            for($i=0;$i < $nLoop;$i++){
                $fordb->fetch($i);
                $fordb2->fetch($i);
                $fordb3->fetch($i);

                if($fordb->dt['vdate'] == date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)){
                    $mstring .= "
				<tr height=30 id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate,"dayname")."</td>

				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>".number_format(returnZeroValue($fordb->dt['sales']/$fordb->dt['ucnt']),0)."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>".number_format(returnZeroValue($fordb->dt['sales'])/returnZeroValue($fordb2->dt['ncnt']),0)."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>".number_format(returnZeroValue($fordb->dt['sales']/$fordb3->dt['ncnt']),0)."</td>

				</tr>";

                    $j = $j + 1;

                    $values1sum = $values1sum + returnZeroValue($fordb->dt['sales']/$fordb->dt['ucnt']);
                    $values2sum = $values2sum + returnZeroValue($fordb->dt['sales']/$fordb2->dt['ncnt']);
                    $values3sum = $values2sum + returnZeroValue($fordb->dt['sales']/$fordb3->dt['ncnt']);
                }else{
                    if($beforedate == $fordb2->dt['vdate']){
                        $values1 = 0;
                        $values2 = 0;
                        $values3 = 0;
                    }else{
                        $values1 = @returnZeroValue($fordb->dt['sales']/$fordb2->dt['ucnt']);
                        $values2 = @returnZeroValue($fordb->dt['sales']/$fordb2->dt['ncnt']);
                        $values3 = @returnZeroValue($fordb->dt['sales']/$fordb2->dt['ncnt']);
                    }
                    $mstring .= "
				<tr height=30 id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate,"dayname")."</td>

				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>".number_format($values1,0)."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>".number_format($values2,0)."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>".number_format($values3,0)."</td>

				</tr>";

                    $values2sum = $values2sum + returnZeroValue($values2);
                }

                $beforedate = $fordb2->dt['vdate'];
            }

        }

        $mstring .= "<tr height=30  >
					<td class=s_td width=19%>합계</td>
					<td class=m_td style='padding-right:20px' width=27%  align=right  nowrap>".number_format(returnZeroValue($values1sum),0)."</td>
					<td class=m_td style='padding-right:20px' width=27% align=right> ".number_format($values2sum,0)."</td>
					<td class=e_td style='padding-right:20px' width=27% align=right nowrap>".number_format(returnZeroValue($values3sum),0)."</td></tr>\n";
        $mstring .= "</table><br>\n";
    }
    /*
    $help_text = "
    <table cellpadding=1 cellspacing=0 class='small' >
        <tr>
            <td style='line-height:150%'>
            - 쇼핑몰의 총 매출액 대비 방문한 횟수와 방문자수를 비교하여 구매고객 1인당 매출액, 1방문당 매출액, 방문고객 1인당 매출액을 보여주는 리포트입니다.<br>
            - 구매고객 1인당 매출액 : 총 매출액 대비 구매고객을 비교하여 보여주며, 구매고객 1인당 매출액을 확인하실 수 있습니다.<br>
            &nbsp;&nbsp;(※ 구매고객 1인당 매출액 = 총 매출액 ÷ 총 구매 고객)<br>
            - 1방문 당 매출액 : 총 매출액 대비 총 방문 횟수를 비교하여 보여주며, 1회 방문 당 매출 평균을 확인하실 수 있습니다.<br>
            &nbsp;&nbsp;(※ 1방문 당 매출액 = 총 매출액 ÷ 총 방문 횟수)<br>
            - 방문고객 1인당 매출액 : 총 매출액 대비 순수 방문자수를 비교하여 보여주며, 방문자 1인당 매출 평균을 확인하실 수 있습니다.<br>
            &nbsp;&nbsp;(※ 방문고객 1인당 매출액 = 총 매출액 ÷ 순수방문자)<br>
            - CPC 광고 집행시 참고하실 수 있습니다.<br>
            </td>
        </tr>

        <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ></td></tr>


    </table>
    ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );



    $mstring .= HelpBox("1인당 고객가치", $help_text);

    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->SelectReport = $SelectReport;
    $ca->LinkPage = 'valuesby1person.php';


    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";

    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('valuesby1person.php');//.text_button("#", "test","190").colorCirCleBoxStart("#efefef",190)."test<br>test<br>test<br>test<br>test<br>".colorCirCleBoxEnd("#efefef");
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 매출종합분석 > 1인당 고객가치";
    $p->title = "1인당 고객가치";
    $p->PrintReportPage();
}
?>
