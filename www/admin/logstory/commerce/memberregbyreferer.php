<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");



function ReportTable($vdate,$SelectReport=1){
    global $depth,$referer_id;
    $pageview01 = 0;
    $memreg_cnt = 0;
    if($SelectReport == ""){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    if($depth == ""){
        $depth = 1;
    }else{
        $depth = $depth+1;
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

    if($referer_id){
        $referer_id_str = " and vreferer_id LIKE '".substr($referer_id,0,(($depth)*3))."%' ";
    }else{
        $referer_id_str = "";
    }


    if ($SelectReport == 1){
        if($depth == 1){
            $sql = "Select r.cid, r.cname,sum(step6) as memreg_cnt
					from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_MEMBERREG_STACK." b
					where b.vdate = '$vdate' and substring(r.cid,1,6) = substring(b.vreferer_id,1,6) and r.depth = $depth and step6 = 1
					group by r.cid, r.cname order by memreg_cnt desc";
        }else if($depth == 2){
            $sql = "Select r.cid, r.cname,sum(step6) as memreg_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_MEMBERREG_STACK." b
			where b.vdate = '$vdate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9) and r.cid LIKE '".substr($referer_id,0,6)."%' and step6 = 1 group by r.cid, r.cname order by memreg_cnt desc";
        }

        $sql = "select r.cid, r.cname, b.* from (
					  SELECT b.vdate, b.vreferer_id,  sum(step6) AS memreg_cnt
					  FROM  ".TBL_LOGSTORY_MEMBERREG_STACK." b
					  WHERE b.vdate = '".$vdate."' 
					  AND step6 = 1  ".$referer_id_str."
					  group by substring(b.vreferer_id,1,".(($depth+1)*3).")
					  ) b , ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r
					where  r.cid = concat(substring(b.vreferer_id,1,".(($depth+1)*3)."),'".str_repeat("0",15-(($depth+1)*3))."') and r.depth = ".$depth."";

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        if($depth == 1){
            $sql = "Select r.cid, r.cname, sum(step6) as memreg_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_MEMBERREG_STACK." b where b.vdate between '$vdate' and '$vweekenddate' and substring(r.cid,1,6) = substring(b.vreferer_id,1,6) and r.depth = $depth and step6 = 1 group by r.cid, r.cname order by memreg_cnt desc";
        }else if($depth == 2){
            $sql = "Select r.cid, r.cname, sum(step6) as memreg_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_MEMBERREG_STACK." b where b.vdate between '$vdate' and '$vweekenddate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9) and r.cid LIKE '".substr($referer_id,0,6)."%' and step6 = 1 group by r.cid, r.cname order by memreg_cnt desc";
        }

        $sql = "select r.cid, r.cname, b.* from (
					  SELECT b.vdate, b.vreferer_id,  sum(step6) AS memreg_cnt
					  FROM  ".TBL_LOGSTORY_MEMBERREG_STACK." b
					  WHERE b.vdate between '".$vdate."' and '".$vweekenddate."'
					  AND step6 = 1  ".$referer_id_str."
					  group by substring(b.vreferer_id,1,".(($depth+1)*3).")
					  ) b , ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r
					where  r.cid = concat(substring(b.vreferer_id,1,".(($depth+1)*3)."),'".str_repeat("0",15-(($depth+1)*3))."') and r.depth = ".$depth."";

        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        if($depth == 1){
            $sql = "Select r.cid, r.cname, sum(step6) as memreg_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_MEMBERREG_STACK." b where b.vdate LIKE '".substr($vdate,0,6)."%' and substring(r.cid,1,6) = substring(b.vreferer_id,1,6) and r.depth = $depth and step6 = 1 group by r.cid, r.cname  order by memreg_cnt desc";
        }else if($depth == 2){
            $sql = "Select r.cid, r.cname, sum(step6) as memreg_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_MEMBERREG_STACK." b where b.vdate LIKE '".substr($vdate,0,6)."%' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9) and r.cid LIKE '".substr($referer_id,0,6)."%' and step6 = 1 group by r.cid, r.cname order by memreg_cnt desc";
        }

        $sql = "select r.cid, r.cname, b.* from (
					  SELECT b.vdate, b.vreferer_id,  sum(step6) AS memreg_cnt
					  FROM  ".TBL_LOGSTORY_MEMBERREG_STACK." b
					  WHERE b.vdate LIKE '".substr($vdate,0,6)."%'
					  AND step6 = 1  ".$referer_id_str."
					  group by substring(b.vreferer_id,1,".(($depth+1)*3).")
					  ) b , ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r
					where  r.cid = concat(substring(b.vreferer_id,1,".(($depth+1)*3)."),'".str_repeat("0",15-(($depth+1)*3))."') and r.depth = ".$depth."";



        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }

//	echo $sql;

    if($sql != ''){
        $fordb->query($sql);
    }

    $mstring = $mstring.TitleBar("회원기여종합",$dateString);
    $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
    $mstring = $mstring."<tr height=30  align=center><td class=s_td width=15%>시간</td><td class=m_td width='*'>기여사이트</td><td <td class=e_td width=25%>회원가입수</td></tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring = $mstring."<tr height=30  id='Report$i'>
		<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='list_box_td point' style='text-align:left;pading-left:10px;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\"> ".str_replace("전체 > ","",getRefererCategoryPath($fordb->dt[cid],4))."</td>
		<td class='list_box_td list_bg_gray'align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt[memreg_cnt]."&nbsp;</td>
		<!--td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">&nbsp;</td-->
		</tr>\n";

        $memreg_cnt = $memreg_cnt + returnZeroValue($fordb->dt[memreg_cnt]);


    }
    //$mstring = $mstring."</table>\n";
    //$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  >\n";
    if ($memreg_cnt == 0){
        $mstring = $mstring."<tr class='list_box_td' align=center><td colspan=3 height=200 >결과값이 없습니다.</td></tr>\n";
    }


    $mstring = $mstring."<tr height=30  align=center><td class=s_td width=30 colspan=2>합계</td><td class=e_td>".number_format($pageview01,0)."</td></tr>\n";
    $mstring = $mstring."</table>\n";


    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("회원기여 종합", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'memberregbyreferer.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('memberregbyreferer.php', "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode('memberregbyreferer.php',date("Ymd", time()))."</div>");
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>";
//$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->Navigation = "이커머스분석 > 기여도분석 > 회원기여종합";
    $p->title = "회원기여종합";
    $p->PrintReportPage();
}
?>
