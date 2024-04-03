<?php
include("../class/reportpage.class");
include("../include/ReportReferTree.php");



function ReportTable($vdate,$SelectReport=1){
    global $depth,$referer_id;
    $pageview01 = 0;
    $sale_sum = 0;
    $sale_cnt_sum = 0;
    if($SelectReport == ""){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    if($depth == ""){
        $depth = 1;
    }else{
        $depth = $depth+1;
    }
    //echo $_SESSION["ss_SelectReport"];

    if($vdate == ""){
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    }

    if($referer_id){
        $referer_id_str = " and vreferer_id LIKE '".substr($referer_id,0,(($depth)*3))."%' ";
    }else{
        $referer_id_str = "";
    }

    if ($SelectReport == 1){

        /*
        if($depth == 1){
            $sql = "select r.cid, r.cname, b.* from (
                      SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
                      FROM commerce_salestack b
                      WHERE b.vdate ='".$vdate."'
                      AND step6 = 1 ".$referer_id_str."
                      group by substring(b.vreferer_id,1,6)
                      ) b , logstory_referer_categoryinfo r
                    where  r.cid = concat(substring(b.vreferer_id,1,6),'000000000') and r.depth = ".$depth."";

        }else if($depth == 2){

            $sql = "select r.cid, r.cname, b.* from (
                      SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
                      FROM commerce_salestack b
                      WHERE b.vdate ='".$vdate."'
                      AND step6 = 1  ".$referer_id_str."
                      group by substring(b.vreferer_id,1,9)
                      ) b , logstory_referer_categoryinfo r
                    where  r.cid = concat(substring(b.vreferer_id,1,9),'000000') and r.depth = ".$depth."";

        }else if($depth == 3){


            $sql = "select r.cid, r.cname, b.* from (
                      SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
                      FROM commerce_salestack b
                      WHERE b.vdate ='".$vdate."'
                      AND step6 = 1  ".$referer_id_str."
                      group by substring(b.vreferer_id,1,12)
                      ) b , logstory_referer_categoryinfo r
                    where  r.cid = concat(substring(b.vreferer_id,1,12),'000') and r.depth = ".$depth."";
        }
        */

        $sql = "select r.cid, r.cname, b.* from (
					  SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
					  FROM commerce_salestack b
					  WHERE  b.vdate ='".$vdate."'
					  AND step6 = 1  ".$referer_id_str."
					  group by substring(b.vreferer_id,1,".(($depth+1)*3).")
					  ) b , logstory_referer_categoryinfo r
					where  r.cid = concat(substring(b.vreferer_id,1,".(($depth+1)*3)."),'".str_repeat("0",15-(($depth+1)*3))."') and r.depth = ".$depth."";


        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        /*
        if($depth == 1){

            $sql = "select r.cid, r.cname, b.* from (
                      SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
                      FROM commerce_salestack b
                      WHERE b.vdate between '".$vdate."' and '".$vweekenddate."'
                      AND step6 = 1  ".$referer_id_str."
                      group by substring(b.vreferer_id,1,6)
                      ) b , logstory_referer_categoryinfo r
                    where  r.cid = concat(substring(b.vreferer_id,1,6),'000000000') and r.depth = ".$depth."";


        }else if($depth == 2){
            $sql = "Select r.cid, r.cname, sum(b.vsale) as vsale,sum(step6) as sale_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b where b.vdate between '$vdate' and '$vweekenddate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9) and r.cid LIKE '".substr($referer_id,0,6)."%' and step6 = 1 group by r.cid, r.cname order by vsale desc";

            $sql = "select r.cid, r.cname, b.* from (
                      SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
                      FROM commerce_salestack b
                      WHERE b.vdate between '".$vdate."' and '".$vweekenddate."'
                      AND step6 = 1  ".$referer_id_str."
                      group by substring(b.vreferer_id,1,9)
                      ) b , logstory_referer_categoryinfo r
                    where  r.cid = concat(substring(b.vreferer_id,1,9),'000000') and r.depth = ".$depth."";
        }else if($depth == 3){
            $sql = "select r.cid, r.cname, b.* from (
                      SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
                      FROM commerce_salestack b
                      WHERE b.vdate between '".$vdate."' and '".$vweekenddate."'
                      AND step6 = 1  ".$referer_id_str."
                      group by substring(b.vreferer_id,1,12)
                      ) b , logstory_referer_categoryinfo r
                    where  r.cid = concat(substring(b.vreferer_id,1,12),'000') and r.depth = ".$depth."";
        }
        */

        $sql = "select r.cid, r.cname, b.* from (
					  SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
					  FROM commerce_salestack b
					  WHERE  b.vdate between '".$vdate."' and '".$vweekenddate."'
					  AND step6 = 1  ".$referer_id_str."
					  group by substring(b.vreferer_id,1,".(($depth+1)*3).")
					  ) b , logstory_referer_categoryinfo r
					where  r.cid = concat(substring(b.vreferer_id,1,".(($depth+1)*3)."),'".str_repeat("0",15-(($depth+1)*3))."') and r.depth = ".$depth."";


        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        /*
        if($depth == 1){
            $sql = "Select r.cid, r.cname, sum(b.vsale) as vsale,sum(step6) as sale_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b where b.vdate LIKE '".substr($vdate,0,6)."%' and substring(r.cid,1,6) = substring(b.vreferer_id,1,6) and r.depth = $depth and step6 = 1 group by r.cid, r.cname  order by vsale desc";
        }else if($depth == 2){
            $sql = "Select r.cid, r.cname, sum(b.vsale) as vsale,sum(step6) as sale_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_COMMERCE_SALESTACK." b where b.vdate LIKE '".substr($vdate,0,6)."%' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9) and r.cid LIKE '".substr($referer_id,0,6)."%' and step6 = 1 group by r.cid, r.cname order by vsale desc";
        }
        */

        $sql = "select r.cid, r.cname, b.* from (
					  SELECT b.vdate, b.vreferer_id, sum(b.vsale)  AS vsale, sum(step6) AS sale_cnt
					  FROM commerce_salestack b
					  WHERE b.vdate LIKE '".substr($vdate,0,6)."%'
					  AND step6 = 1  ".$referer_id_str."
					  group by substring(b.vreferer_id,1,".(($depth+1)*3).")
					  ) b , logstory_referer_categoryinfo r
					where  r.cid = concat(substring(b.vreferer_id,1,".(($depth+1)*3)."),'".str_repeat("0",15-(($depth+1)*3))."') and r.depth = ".$depth."";

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }

    //echo nl2br($sql);

    if($sql != ''){
        $fordb->query($sql);
    }

    $mstring = $mstring.TitleBar("매출기여종합",$dateString);
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
    $mstring .= "<tr height=30  align=center><td class=s_td width=15%>시간</td><td class=m_td width='*'>카테고리명</td><td class=m_td width=15%>구매건수</td><td class=e_td width=15%>매출액</td></tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring .= "<tr height=30   id='Report$i'>
		<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='list_box_td point' style='text-align:left;padding-left:10px;'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" title='".$fordb->dt['cid']."'>
		<!--".$fordb->dt['cname']."--> ".str_replace("전체 > ","",getRefererCategoryPath($fordb->dt['cid'],4))."</td>
		<td class='list_box_td list_bg_gray'  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".$fordb->dt['sale_cnt']."&nbsp;</td>
		<td class='list_box_td point number'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(CheckGraphValue($fordb->dt['vsale']),0)."&nbsp;</td>
		</tr>\n";

        $sale_sum = $sale_sum + CheckGraphValue($fordb->dt['vsale']);
        $sale_cnt_sum = $sale_cnt_sum + CheckGraphValue($fordb->dt['sale_cnt']);

    }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
    if ($sale_sum == 0){
        $mstring .= "<tr height=300 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
    }
    $mstring .= "<tr height=30  align=right>
	<td class=s_td align=center colspan=2>합계</td>
	<td class=m_td style='padding-right:20px;'>".number_format($sale_cnt_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($sale_sum,0)."</td>
	</tr>\n";
    $mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 매출을 발생시킨 회원의 유입사이트별로 발생한 매출을 종합적으로 확인하실 수 있는 리포트로 좌측 카테고리를 활용하여 보다 상세한 유입사이트의 정보를 확인하실 수 있습니다.


    <br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("매출기여종합", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'salesbyreferer.php';

    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";
    echo "<div id='report_view'>".ReportTable($vdate,$SelectReport)."</div>";
    echo "<div id='calendar_view'>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</div>";
    echo "</body>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.getElementById('report_view').innerHTML</Script>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.getElementById('calendar_view').innerHTML;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";
    echo "</html>";

//	echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
//	echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('salesbyreferer.php', "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode('salesbyreferer.php',date("Ymd", time()))."</div>");
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>";
//$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->Navigation = "이커머스분석 > 기여도분석 > 매출기여종합";
    $p->title = "매출기여종합";
    $p->PrintReportPage();
}
?>
