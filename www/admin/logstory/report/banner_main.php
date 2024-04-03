<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");

function ReportTable($vdate,$SelectReport=1){
    global $depth,$referer_id, $admin_config;
    $pageview01 = 0;
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
        if($fordb->dbms_type == "oracle"){
            $sql = "select banner_page, b.banner_ix, sum(bc.ncnt) as ncnt
			from shop_bannerinfo b, logstory_banner_click bc
			where b.banner_ix = bc.banner_ix and  bc.vdate = '$vdate'  ";
            $sql .= "group by banner_page, b.banner_ix order by ncnt desc ";
        }else{
            $sql = "select banner_page, b.banner_ix, banner_name, banner_img, banner_width, banner_height, sum(bc.ncnt) as ncnt
			from shop_bannerinfo b, logstory_banner_click bc
			where b.banner_ix = bc.banner_ix and  bc.vdate = '$vdate'  ";
            $sql .= "group by banner_page, banner_ix order by ncnt desc ";
        }
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        $sql = "select banner_page, b.banner_ix, banner_name, banner_img, banner_width, banner_height, sum(bc.ncnt) as ncnt
			from shop_bannerinfo b, logstory_banner_click bc
			where b.banner_ix = bc.banner_ix and  bc.vdate between '$vdate' and '$vweekenddate'  ";
        $sql .= "group by banner_page, banner_ix order by ncnt desc ";

        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){

        $sql = "select banner_page, b.banner_ix, banner_name, banner_img, banner_width, banner_height, sum(bc.ncnt) as ncnt
			from shop_bannerinfo b, logstory_banner_click bc
			where b.banner_ix = bc.banner_ix and bc.vdate LIKE '".substr($vdate,0,6)."%' ";
        $sql .= "group by banner_page, banner_ix order by ncnt desc ";

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }

//	echo $sql;
    $fordb->query($sql);

    $mstring = $mstring.TitleBar("메인페이지 클릭분석",$dateString);



    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";


    $mstring .= "<tr height=300 bgcolor=#ffffff align=center><td colspan=5><iframe name='act' id='act' src='/?SelectReport=$SelectReport&vdate=".$vdate."&viewtype=analysis' width=1100 height=1600 frameborder=0></iframe></td></tr>\n";

    $mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";

    $mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 메인페이지 클릭분석이란? 쇼핑몰을 방문하시는 고객이 메인페이지 클릭분석을 확인하실 수 있는 리포트입니다.<br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("메인페이지 클릭분석", $help_text);

    return $mstring;
}

if ($mode == "iframe"){
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'banner_main.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 배너 분석 > 메인페이지 클릭분석 ";
    $p->title = "메인페이지 클릭분석 ";
    $p->forbizLeftMenu = Stat_munu('banner_main.php', "");//"<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode('searchenginebykeyword.php',date("Ymd", time()),"search_engine")."</div>"
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>";
//$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->PrintReportPage();
}
?>
