<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");

function ReportTable($vdate,$SelectReport=1){
    global $depth,$referer_id;
    $visit_cnt_sum = 0;
    $mstring = "";
    $visitor_cnt_sum = 0;
    if($SelectReport == ""){
        $SelectReport = 1;
    }else if($SelectReport == "4"){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    if($depth == ""){
        $depth = 1;
    }



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

        $sql = "select k.kid, replace(keyword,' ','') as keyword,cname, sum(b.visit_cnt) as visit_cnt , sum(b.visitor_cnt) as visitor_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k where k.kid = b.kid and  b.vdate = '$vdate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)  and r.depth = 2  and ".($fordb->dbms_type == "oracle" ? "keyword is not null " : "keyword <> '' " )." ";
        $sql .= "group by k.kid, keyword,cname order by k.kid, visit_cnt desc ";

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){

        $sql = "select k.kid,vreferer_id, cname, replace(keyword,' ','') as keyword, sum(b.visit_cnt) as visit_cnt , sum(b.visitor_cnt) as visitor_cnt
						from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
						where k.kid = b.kid and  b.vdate between '$vdate' and '$vweekenddate'
						and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)  and r.depth = 2  and ".($fordb->dbms_type == "oracle" ? "keyword is not null " : "keyword <> '' " )."
						group by k.kid,vreferer_id, cname, keyword
						order by k.kid,visit_cnt desc";

        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){

        $sql = "select k.kid,vreferer_id, cname, replace(keyword,' ','') as keyword, sum(b.visit_cnt) as visit_cnt , sum(b.visitor_cnt) as visitor_cnt
						from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
						where k.kid = b.kid and b.vdate LIKE '".substr($vdate,0,6)."%'
						and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)  and r.depth = 2  and ".($fordb->dbms_type == "oracle" ? "keyword is not null " : "keyword <> '' " )."
						group by k.kid, keyword, vreferer_id, cname order by k.kid,visit_cnt desc";

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }

//	echo $sql;
    $fordb->query($sql);

    $mstring = $mstring.TitleBar("키워드별 검색엔진",$dateString);



    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
    $mstring .= "<tr height=30 align=center><td class=s_td width=5%>순</td>
						<td class=m_td width='*'>키워드</td>
						<td class=m_td width=15%>검색엔진</td>
						<td class=m_td width=15%>방문횟수</td>
						<td class=m_td width=15%>점유율(방문횟수)</td>
						<td class=m_td width=15%>방문자수</td>
						<td class=e_td width=15%>점유율(방문자수)</td>
						</tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $pre_visit_cnt_sum = $pre_visit_cnt_sum + $fordb->dt[visit_cnt];
        $pre_visitor_cnt_sum = $pre_visitor_cnt_sum + $fordb->dt[visitor_cnt];
    }

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        if($before_keyword != '' && $before_keyword != $fordb->dt[keyword]){
            //$mstring .= "<tr height=1><td colspan=4 background='../img/dot.gif'></td></tr>";
        }
        $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($before_keyword == $fordb->dt[keyword] ? "":$fordb->dt[keyword])."&nbsp;</td>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt[cname]."</td>
		<td  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[visit_cnt]))."&nbsp;</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($fordb->dt[visit_cnt]/$pre_visit_cnt_sum*100,1)."%&nbsp;</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[visitor_cnt]),0)."&nbsp;</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($fordb->dt[visitor_cnt]/$pre_visitor_cnt_sum*100,1)."%&nbsp;</td>
		</tr>\n";



        $visit_cnt_sum = $visit_cnt_sum + returnZeroValue($fordb->dt[visit_cnt]);
        $visitor_cnt_sum = $visitor_cnt_sum + returnZeroValue($fordb->dt[visitor_cnt]);
        $before_keyword = $fordb->dt[keyword];

    }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
    if ($visit_cnt_sum == 0){
        $mstring .= "<tr height=100 bgcolor=#ffffff align=center><td colspan=7>결과값이 없습니다.</td></tr>\n";
    }

    $mstring .= "<tr height=30 align=right>
	<td class=s_td align=center colspan=3>합계</td>
	<td class=e_td style='padding-right:20px;'>".number_format($visit_cnt_sum)."</td>
	<td class=e_td style='padding-right:20px;'>100%</td>
	<td class=e_td style='padding-right:20px;'>".number_format($visitor_cnt_sum)."</td>
	<td class=e_td style='padding-right:20px;'>100%</td>
	</tr>\n";
    $mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 키워드별 검색엔진이란? 쇼핑몰을 방문하시는 고객의 유입 키워드별 검색엔진을 확인하실 수 있는 리포트입니다.<br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("키워드별 검색엔진", $help_text);

    return $mstring;
}

if ($mode == "iframe"){
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'searchenginebykeyword.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 유입사이트 분석 > 키워드별 검색엔진 ";
    $p->title = "키워드별 검색엔진 ";
    $p->forbizLeftMenu = Stat_munu('searchenginebykeyword.php', "");//"<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode('searchenginebykeyword.php',date("Ymd", time()),"search_engine")."</div>"
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>";
//$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->PrintReportPage();
}
?>
