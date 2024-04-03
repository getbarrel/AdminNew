<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");

function ReportTable($vdate,$SelectReport=1,$agent_type=""){
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

    if($agent_type){
        $where .= " and agent_type = '$agent_type'";
    }

    //$sql = "Select r.cid, r.cname, b.visit_cnt from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b where b.vdate = '$vdate' and substring(r.cid,0,6) = substring(b.vreferer_id,0,6) and r.depth = $depth group by r.cid, r.cname order by visit_cnt desc";
    if ($SelectReport == 1){

        $sql = "select bd.bd_file, banner_page, b.banner_ix, banner_name, banner_img, banner_width, banner_height, sum(bc.ncnt) as ncnt, agent_type
			from shop_bannerinfo b, logstory_banner_click bc, shop_bannerinfo_detail bd
			where bd.banner_ix=b.banner_ix and b.banner_ix = bc.banner_ix and  bc.vdate = '$vdate'  " . $where;
        $sql .= "group by banner_page, b.banner_ix,banner_name, banner_img, banner_width, banner_height order by ncnt desc ";

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){

        $sql = "select bd.bd_file, banner_page, b.banner_ix, banner_name, banner_img, banner_width, banner_height, sum(bc.ncnt) as ncnt, agent_type
			from shop_bannerinfo b, logstory_banner_click bc, shop_bannerinfo_detail bd
			where bd.banner_ix=b.banner_ix and b.banner_ix = bc.banner_ix and  bc.vdate between '$vdate' and '$vweekenddate'  " . $where;
        $sql .= "group by banner_page, b.banner_ix,banner_name, banner_img, banner_width, banner_height order by ncnt desc ";

        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){

        $sql = "select bd.bd_file, banner_page, b.banner_ix, banner_name, banner_img, banner_width, banner_height, sum(bc.ncnt) as ncnt, agent_type
			from shop_bannerinfo b, logstory_banner_click bc, shop_bannerinfo_detail bd
			where bd.banner_ix=b.banner_ix and b.banner_ix = bc.banner_ix and bc.vdate LIKE '".substr($vdate,0,6)."%' " . $where;
        $sql .= "group by banner_page, b.banner_ix,banner_name, banner_img, banner_width, banner_height order by ncnt desc ";

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }

    $fordb->query($sql);

    $mstring = $mstring.TitleBar("배너 클릭수 분석",$dateString);

    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
    $mstring .= "<tr height=30 align=center><td class=s_td width=10%>순</td>
	<td class=m_td width='10%'>
		<select name='agent_type' onchange='goAction(this);'>
		<option value=''>PC/Mobile</option>
		<option value='W' ".($agent_type=="W"?"selected":"").">PC</option>
		<option value='M' ".($agent_type=="M"?"selected":"").">Mobile</option>
		</select>
	</td>
	<td class=m_td width='15%'>배너그룹</td><td class=m_td width='20%'>배너</td><td class=e_td width=15%>클릭수</td></tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        if($fordb->dt[agent_type] == 'W'){
            $agent_type = "PC";
        }else if($fordb->dt[agent_type] =='M'){
            $agent_type = "Mobile";
        }
        if($fordb->dt[banner_page] == 1){
            $banner_page = "메인";
        }else if($fordb->dt[banner_page] ==2){
            $banner_page = "카테고리메인";
        }else if($fordb->dt[banner_page] ==3){
            $banner_page = "카테고리서브";
        }else if($fordb->dt[banner_page] ==4){
            $banner_page = "신상품";
        }else if($fordb->dt[banner_page] ==5){
            $banner_page = "할인상품";
        }else if($fordb->dt[banner_page] ==6){
            $banner_page = "베스트";
        }else if($fordb->dt[banner_page] ==7){
            $banner_page = "이벤트/기획전";
        }else if($fordb->dt[banner_page] ==8){
            $banner_page = "메인스크롤";
        }else if($fordb->dt[banner_page] ==9){
            $banner_page = "세일스크롤";
        }else if($fordb->dt[banner_page] ==10){
            $banner_page = "신상품스크롤";
        }

        if($before_keyword != '' && $before_keyword != $fordb->dt[keyword]){
            $mstring .= "<tr height=1><td colspan=4 background='../img/dot.gif'></td></tr>";
        }
        $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($agent_type)."&nbsp;</td>
		<td align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($banner_page)."&nbsp;</td>";
        if(substr_count($db->dt[banner_img],'.swf') > 0){
            $mstring .= "<td bgcolor=#efefef onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"><script language='javascript'>generate_flash('".$admin_config[mall_data_root]."/images/banner/".$fordb->dt[banner_ix]."/".$fordb->dt[banner_img]."', '".$fordb->dt[banner_width]."', '".$fordb->dt[banner_height]."');</script></td>";
        }else{

            if(!empty($fordb->dt[banner_img])){

                $image_info = getimagesize ($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/banner/".$fordb->dt[banner_ix]."/".$fordb->dt[banner_img]);

                if($image_info[0] > 300){
                    $mstring .= "<td bgcolor=#efefef style='padding:3 0 3 20' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"><img src='".$admin_config[mall_data_root]."/images/banner/".$fordb->dt[banner_ix]."/".$fordb->dt[banner_img]."' width=110 style='vertical-align:middle'> ".$fordb->dt[banner_name]."</td>";
                }else{
                    $mstring .= "<td bgcolor=#efefef style='padding:3 0 3 20' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"><img src='".$admin_config[mall_data_root]."/images/banner/".$fordb->dt[banner_ix]."/".$fordb->dt[banner_img]."' width=110 style='vertical-align:middle'> ".$fordb->dt[banner_name]."</td>";
                }
            }else{

                $image_info = getimagesize ($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/banner/".$fordb->dt[banner_ix]."/".$fordb->dt[bd_file]);

                if($image_info[0] > 300){
                    $mstring .= "<td bgcolor=#efefef style='padding:3 0 3 20' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"><img src='".$admin_config[mall_data_root]."/images/banner/".$fordb->dt[banner_ix]."/".$fordb->dt[bd_file]."' width=110 style='vertical-align:middle'> ".$fordb->dt[banner_name]."</td>";
                }else{
                    $mstring .= "<td bgcolor=#efefef style='padding:3 0 3 20' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"><img src='".$admin_config[mall_data_root]."/images/banner/".$fordb->dt[banner_ix]."/".$fordb->dt[bd_file]."' width=110 style='vertical-align:middle'> ".$fordb->dt[banner_name]."</td>";
                }
            }
        }
        $mstring .= "
		<!--td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt[banner_name]."</td-->
		<td  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[ncnt]))."&nbsp;</td></tr>\n";



        $pageview01 = $pageview01 + returnZeroValue($fordb->dt[ncnt]);
        $before_keyword = $fordb->dt[keyword];

    }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
    if ($pageview01 == 0){
        $mstring .= "<tr height=300 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
    }

    $mstring .= "<tr height=30 align=right>
	<td class=s_td align=center colspan=4>합계</td>
	<td class=e_td style='padding-right:20px;'>".number_format($pageview01)."</td>
	</tr>\n";
    $mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 배너 클릭수 분석이란? 쇼핑몰을 방문하시는 고객의 유입 배너 클릭수 분석을 확인하실 수 있는 리포트입니다.<br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("배너 클릭수 분석", $help_text);

    return $mstring;
}

if ($mode == "iframe"){
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport,$agent_type)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'searchenginebykeyword.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

}else{

    $script = "<script>
function goAction(obj){
	location.href='?SelectReport=$SelectReport&vdate=$vdate&SubID=$SubID&agent_type='+$(obj).val();
}
</script>";
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 유입사이트 분석 > 배너 클릭수 분석 ";
    $p->title = "배너 클릭수 분석 ";
    $p->forbizLeftMenu = Stat_munu('bannerclick.php', "");//"<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode('searchenginebykeyword.php',date("Ymd", time()),"search_engine")."</div>"
    $p->forbizContents = ReportTable($vdate,$SelectReport,$agent_type);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>".$script;
//$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->PrintReportPage();
}
?>
