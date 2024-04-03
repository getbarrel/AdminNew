<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");
include("../report/keyword.chart.php");

function ReportTable($vdate,$SelectReport=1){
    global $depth,$referer_id;
    //echo $vdate;
    $pageview01 = 0;
    $visit_cnt_sum = 0;
    $visitor_cnt_sum = 0;
    $chart_data = array();
    $mstring = "";
    if($SelectReport == ""){
        $SelectReport = 1;
    }else if($SelectReport == "4"){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    if($depth == ""){
        $depth = 1;
    }

    //print_r($_SESSION);

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



    //$sql = "Select r.cid, r.cname, b.visit_cnt from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b where b.vdate = '$vdate' and substring(r.cid,0,6) = substring(b.vreferer_id,0,6) and r.depth = $depth group by r.cid, r.cname order by visit_cnt desc";
    if ($SelectReport == 1){
        if($fordb->dbms_type == "oracle"){
            $sql = "select k.kid, replace(keyword,' ','') as keyword, sum(b.visit_cnt) as visit_cnt , sum(b.visitor_cnt) as visitor_cnt
			from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
			where k.kid = b.kid and  b.vdate = '$vdate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)
			and r.depth = 2 and keyword is not null and keyword not like '%?%' and substr(keyword,1,1) != '%'";
            $sql .= "group by k.kid,keyword order by visit_cnt desc ";
        }else{
            $sql = "select k.kid,replace(keyword,' ','') as keyword,cname, sum(b.visit_cnt) as visit_cnt , sum(b.visitor_cnt) as visitor_cnt
			from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
			where k.kid = b.kid and  b.vdate = '$vdate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)
			and r.depth = 2 and keyword <> '' and keyword not like '%?%' and substring(keyword,1,1) != '%'";
            $sql .= "group by keyword order by visit_cnt desc ";
        }
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        if($fordb->dbms_type == "oracle"){
            $sql = "select k.kid,vreferer_id, cname, replace(keyword,' ','') as keyword, sum(b.visit_cnt) as visit_cnt , sum(b.visitor_cnt) as visitor_cnt
			from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
			where k.kid = b.kid and  b.vdate between '$vdate' and '$vweekenddate'
			and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  and substring(keyword,1,1) != '%'
			and r.depth = 2 and keyword is not null
			group by  k.kid,vreferer_id, cname , keyword order by visit_cnt desc";
        }else{
            $sql = "select k.kid,vreferer_id, cname, replace(keyword,' ','') as keyword, sum(b.visit_cnt) as visit_cnt , sum(b.visitor_cnt) as visitor_cnt
			from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
			where k.kid = b.kid and  b.vdate between '$vdate' and '$vweekenddate'
			and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  and substring(keyword,1,1) != '%'
			and r.depth = 2 and keyword <> ''
			group by  k.kid,vreferer_id, cname , keyword order by visit_cnt desc";
        }
        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        if($fordb->dbms_type == "oracle"){
            $sql = "select k.kid,vreferer_id, cname, replace(keyword,' ','') as keyword, sum(b.visit_cnt) as visit_cnt , sum(b.visitor_cnt) as visitor_cnt
			from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
			where k.kid = b.kid and b.vdate LIKE '".substr($vdate,0,6)."%'
			and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  and substring(keyword,1,1) != '%' and substring(keyword,1,1) != '?'
			and r.depth = 2 and keyword is not null
			group by  k.kid,vreferer_id, cname , keyword
			order by visit_cnt desc";
        }else{
            $sql = "select k.kid,vreferer_id, cname, replace(keyword,' ','') as keyword, sum(b.visit_cnt) as visit_cnt , sum(b.visitor_cnt) as visitor_cnt
			from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
			where k.kid = b.kid and b.vdate LIKE '".substr($vdate,0,6)."%'
			and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  and substring(keyword,1,1) != '%' and substring(keyword,1,1) != '?'
			and r.depth = 2 and keyword <> ''
			group by  k.kid,vreferer_id, cname , keyword
			order by visit_cnt desc";
        }
        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }

//	echo $sql;
    $fordb->query($sql);
//exit;
    $mstring .= TitleBar("키워드별 방문횟수",$dateString);

    $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
    if($fordb->total){
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;' align=left>".keywordGraph($vdate,$SelectReport)."</td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding:30px 0px;text-align:center;'><div id='piechart' style='width: 70%; height: 300px; padding: 0px; position: relative;'></div></td></tr>\n";
    }
    $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
    $mstring .= "</table>";

    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
    $mstring .= "<tr height=25 align=center><td class=s_td width=10%>순</td>
								<td class=m_td width='*'>키워드</td>
								<td class=m_td width=15%>방문횟수</td>
								<td class=m_td width=15%>점유율(방문횟수)</td>
								<td class=m_td width=15%>방문자수</td>
								<td class=e_td width=15%>점유율(방문자수)</td>
								</tr>\n";

    $color[] = "#D9534F";
    $color[] = "#1CAF9A";
    $color[] = "#F0AD4E";
    $color[] = "#428BCA";
    $color[] = "#5BC0DE";


    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $pre_visit_cnt_sum = $pre_visit_cnt_sum + $fordb->dt[visit_cnt];
        $pre_visitor_cnt_sum = $pre_visitor_cnt_sum + $fordb->dt[visitor_cnt];

        if($i < 10){
            $chart_data[] = array(
                label => $fordb->dt[keyword],
                data => array(0=>array("1",$fordb->dt[visit_cnt])) ,
                color => $color[$i]
            );
        }

    }

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring .= "<tr height=25 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:10px;'> <img src='/admin/images/i_add.gif' align=absmiddle>  ".$fordb->dt[keyword]."&nbsp;</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($fordb->dt[visit_cnt],0)."&nbsp;</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($fordb->dt[visit_cnt]/$pre_visit_cnt_sum*100,1)."%&nbsp;</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[visitor_cnt]),0)."&nbsp;</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($fordb->dt[visitor_cnt]/$pre_visitor_cnt_sum*100,1)."%&nbsp;</td>
		</tr>\n";

        $visit_cnt_sum = $visit_cnt_sum + returnZeroValue($fordb->dt[visit_cnt]);
        $visitor_cnt_sum = $visitor_cnt_sum + returnZeroValue($fordb->dt[visitor_cnt]);


    }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
    if ($visit_cnt_sum == 0){
        $mstring .= "<tr height=50 bgcolor=#ffffff align=center><td colspan=6>결과값이 없습니다.</td></tr>\n";
    }

    $mstring .= "<tr height=25 align=right>
	<td class=s_td align=center colspan=2>합계</td>

	<td class=m_td style='padding-right:20px;'>".number_format($visit_cnt_sum,0)."</td>
	<td class=m_td style='padding-right:20px;'>-</td>
	<td class=m_td style='padding-right:20px;'>".number_format($visitor_cnt_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>-</td>
	</tr>\n";
    $mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 키워드별 방문횟수란? 검색엔진이나 포털 등에서 검색을 통하여 쇼핑몰을 방문하였을 경우 해당 검색어에 대한 방문 횟수를 집계하여 확인 하실 수 있는 리포트입니다.<br>

                </td>
            </tr>
        </table>
        ";*/

    $mstring .= "<link href='../css/morris.css' rel='stylesheet'>
<!--script src='../js/jquery-1.10.2.min.js'></script-->
<script src='../js/jquery-migrate-1.2.1.min.js'></script>
<script src='../js/bootstrap.min.js'></script>

<script src='../js/modernizr.min.js'></script> 
<script src='../js/jquery.sparkline.min.js'></script>
<script src='../js/toggles.min.js'></script>

<script src='../js/retina.min.js'></script> 
<script src='../js/jquery.cookies.js'></script>

<script src='../js/flot/flot.min.js'></script>
<script src='../js/flot/flot.resize.min.js'></script>
<script src='../js/flot/flot.symbol.min.js'></script>
<script src='../js/flot/flot.crosshair.min.js'></script>
<script src='../js/flot/flot.categories.min.js'></script>
<script src='../js/flot/flot.pie.min.js'></script>
<script src='../js/morris.min.js'></script>
<script src='../js/raphael-2.1.0.min.js'></script>

<script src='../js/custom.js'></script>
<!--script src='../js/charts.js'></script-->
<script script='javascript'>
var piedata = ".json_encode($chart_data).";
    
    jQuery.plot('#piechart', piedata, {
        series: {
            pie: {
                show: true,
                radius: 1,
                label: {
                    show: true,
                    radius: 2/3,
                    formatter: labelFormatter,
                    threshold: 0.1
                }
            }
        },
        grid: {
            hoverable: true,
            clickable: true
        }
    });

	function labelFormatter(label, series) {
		return \"<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>\" + label + \"<br/>\" + Math.round(series.percent) + \"%</div>\";
	}

</script>";

    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("키워드별 방문횟수", $help_text);

    return $mstring;
}

if ($mode == "iframe"){
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'keyword.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 유입사이트 분석 > 키워드별 방문횟수";
    $p->title = "키워드별 방문횟수";
    $p->forbizLeftMenu = Stat_munu('keyword.php', "");//"<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode('keyword.php',date("Ymd", time()),"search_engine")."</div>"
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>";
//$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->PrintReportPage();
}
?>
