<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");



function ReportTable($vdate,$SelectReport=1){
    global $depth,$referer_id;
    $pageview01 = 0;
    $vsale_sum = 0;
    $sale_cnt_sum = 0;
    $referer_id_str = "";
    if(empty($SelectReport)){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    if(empty($depth)){
        $depth = 1;
    }else{
        $depth = $depth+1;
    }


    if(empty($vdate)){
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $selected_date = date("Ymd", time());
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
        $selected_date = date("Ymd", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)));
    }



    if ($SelectReport == 1){
        if($depth == 1 || $depth == 2){

            /*
            $sql = "Select r.cid, r.cname, sum(b.vsale) as vsale,sum(step6) as sale_cnt
                    from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b
                    where b.vdate = '$vdate' and substring(r.cid,1,6) = substring(b.vreferer_id,1,6) and r.depth = $depth and step6 = 1
                    group by r.cid, r.cname order by vsale desc";
        }else if($depth == 2){
            $sql = "Select r.cid, r.cname, sum(b.vsale) as vsale,sum(step6) as sale_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b
                            where b.vdate = '$vdate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9) and r.cid LIKE '".substr($referer_id,0,6)."%'
                            and step6 = 1 group by r.cid, r.cname order by vsale desc";

                            */

            $sql = "select r.cid, r.cname, b.* from (
					  SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
					  FROM ".TBL_COMMERCE_SALESTACK." b
					  WHERE b.vdate  between '".substr($selected_date,0,6)."01' and '".substr($selected_date,0,6)."31'  
					  AND step6 = 1  ".$referer_id_str."
					  group by substring(b.vreferer_id,1,".(($depth+1)*3).")
					  ) b , ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r
					where  r.cid = concat(substring(b.vreferer_id,1,".(($depth+1)*3)."),'".str_repeat("0",15-(($depth+1)*3))."') and r.depth = ".$depth."";

        }else if($depth == 3){
            $sql = "Select r.cid, r.cname, k.keyword, sum(b.vsale) as vsale,sum(step6) as sale_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b , logstory_keywordinfo k
							where b.vdate = '$vdate' and substring(r.cid,1,12) = substring(b.vreferer_id,1,12) and r.cid LIKE '".substr($referer_id,0,9)."%'
							and b.kid = k.kid
							and step6 = 1 group by r.cid, r.cname, k.kid order by vsale desc";
        }



        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        if($depth == 1 || $depth == 2){
            /*
            $sql = "Select r.cid, r.cname, sum(b.vsale) as vsale,sum(step6) as sale_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b where b.vdate between '$vdate' and '$vweekenddate' and substring(r.cid,1,6) = substring(b.vreferer_id,1,6) and r.depth = $depth and step6 = 1 group by r.cid, r.cname order by vsale desc";
        }else if($depth == 2){
            $sql = "Select r.cid, r.cname, sum(b.vsale) as vsale,sum(step6) as sale_cnt
                            from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b where b.vdate between '$vdate' and '$vweekenddate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9) and r.cid LIKE '".substr($referer_id,0,6)."%' and step6 = 1 group by r.cid, r.cname order by vsale desc";
            */
            $sql = "select r.cid, r.cname, b.* from (
					  SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
					  FROM ".TBL_COMMERCE_SALESTACK." b
					  WHERE b.vdate between '$vdate' and '$vweekenddate' 
					  AND step6 = 1  ".$referer_id_str."
					  group by substring(b.vreferer_id,1,".(($depth+1)*3).")
					  ) b , ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r
					where  r.cid = concat(substring(b.vreferer_id,1,".(($depth+1)*3)."),'".str_repeat("0",15-(($depth+1)*3))."') and r.depth = ".$depth."";

        }else if($depth == 3){
            $sql = "Select r.cid, r.cname, k.keyword , sum(b.vsale) as vsale,sum(step6) as sale_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b , logstory_keywordinfo k
							where b.vdate between '$vdate' and '$vweekenddate' and substring(r.cid,1,12) = substring(b.vreferer_id,1,12)
							and r.cid LIKE '".substr($referer_id,0,9)."%' and step6 = 1
							and b.kid = k.kid
							group by r.cid, r.cname, k.kid  order by vsale desc";

        }
        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        if($depth == 1 || $depth == 2){
            /*
            $sql = "Select r.cid, r.cname, sum(b.vsale) as vsale,sum(step6) as sale_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b where b.vdate LIKE '".substr($vdate,0,6)."%' and substring(r.cid,1,6) = substring(b.vreferer_id,1,6) and r.depth = $depth group by r.cid, r.cname and step6 = 1 order by vsale desc";
        }else if($depth == 2){
            $sql = "Select r.cid, r.cname, sum(b.vsale) as vsale,sum(step6) as sale_cnt
                            from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b
                            where b.vdate LIKE '".substr($vdate,0,6)."%' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)
                            and r.cid LIKE '".substr($referer_id,0,6)."%' and step6 = 1
                            group by r.cid, r.cname order by vsale desc";
            */
            $sql = "select r.cid, r.cname, b.* from (
					  SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
					  FROM ".TBL_COMMERCE_SALESTACK." b
					  WHERE b.vdate LIKE '".substr($vdate,0,6)."%'
					  AND step6 = 1  ".$referer_id_str."
					  group by substring(b.vreferer_id,1,".(($depth+1)*3).")
					  ) b , ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r
					where  r.cid = concat(substring(b.vreferer_id,1,".(($depth+1)*3)."),'".str_repeat("0",15-(($depth+1)*3))."') and r.depth = ".$depth."";

        }else if($depth == 3){
            $sql = "Select r.cid, r.cname, k.keyword , sum(b.vsale) as vsale,sum(step6) as sale_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b , logstory_keywordinfo k
							where b.vdate LIKE '".substr($vdate,0,6)."%' and substring(r.cid,1,12) = substring(b.vreferer_id,1,12)
							and r.cid LIKE '".substr($referer_id,0,9)."%' and step6 = 1
							and b.kid = k.kid
							group by r.cid, r.cname, k.kid order by vsale desc";
        }


        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }

//	echo $sql;
    if(!empty($sql)){
        $fordb->query($sql);
    }

    $mstring = $mstring.TitleBar("기여도 요약",$dateString);
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
    //$mstring .= "<tr height=2 bgcolor=#ffffff ><td colspan=5 align=left><b>".($cid ? getCategoryPath($cid,4):"")."</b> </td></tr>\n";
    $mstring .= "<tr height=30  align=center>
	<td class=s_td width=15%>시간</td>";
    $mstring .= "<td class=m_td width='*'>레퍼러분류명</td>";
    if($depth == 3){
        $mstring .= "<td class=m_td width=20%>키워드명</td>";
    }
    $mstring .= "
	<td class=m_td width=10%>구매건수</td>
	<td class=e_td width=15%>매출액</td>
	</tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring .= "<tr height=30  id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
				<td class='list_box_td point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='text-align:left;padding-left:10px;'> ".str_replace("전체 > ","",getRefererCategoryPath($fordb->dt['cid'],4))."</td>";

        if($depth == 3){
            $mstring .= "<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px;'>".$fordb->dt['keyword']."</td>
					<td style='text-align:center;padding:0px;' class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['sale_cnt']."</td>
					<td style='text-align:center;padding:0px;' class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(CheckGraphValue($fordb->dt['vsale']),0)."</td>";
        }else{
            $mstring .= "<td style='text-align:center;padding:0px;' class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['sale_cnt']."</td>
					<td style='text-align:center;padding:0px;' class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(CheckGraphValue($fordb->dt['vsale']),0)."</td>";
        }
        $mstring .= "

		</tr>\n";

        $vsale_sum = $vsale_sum + CheckGraphValue($fordb->dt['vsale']);
        $sale_cnt_sum = $sale_cnt_sum + CheckGraphValue($fordb->dt['sale_cnt']);

    }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
    if ($vsale_sum == 0){
        $mstring .= "<tr height=300 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
    }

    $mstring .= "<tr height=30  align=right>
	<td class=s_td align=center colspan=".($depth == 3 ? 3:2).">합계</td>
	<td class=m_td>".number_format($sale_cnt_sum,0)."</td>
	<td class=e_td>".number_format($vsale_sum,0)."</td>
	</tr>\n";
    $mstring .= "</table><br>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 기여도란? 유입사이트가 매출에 기여한 부분을 정략적으로 나타내주는 값이다.<br>
                - 기여도는 구매건수와 매출액으로 나타내진다<br>
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("기여도 요약", $help_text)."<div style='height:200px;'></div>";
    return $mstring;
}

if ($mode == "iframe"){
    //echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    //echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'summationbyreferer.php';

    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";
    echo "<div id='report_view'>".ReportTable($vdate,$SelectReport)."</div>";
    echo "<div id='calendar_view'>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</div>";
    echo "</body>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.getElementById('report_view').innerHTML</Script>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.getElementById('calendar_view').innerHTML;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";
    echo "</html>";

    //echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    //echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('summationbyreferer.php', "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode('salesbyreferer.php',($vdate ? $vdate:date("Ymd", time())))."</div>");
//$p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>";
//$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 기여도분석 > 기여도 요약";
    $p->title = "기여도 요약";
    $p->PrintReportPage();
}
?>


