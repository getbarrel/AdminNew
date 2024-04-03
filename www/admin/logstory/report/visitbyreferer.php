<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");

if(empty($_GET['depth'])){
    $depth = 0;
}

if(empty($referer_id)){
    $referer_id = "000000000000000";
}


function ReportTable($vdate,$SelectReport=1){
    global $depth,$referer_id;
    $pageview01 = 0;
    $mstring = "";
    $visitbyrefererinfo = array();

    $pre_visit_cnt_sum = "";
    $pre_visitor_cnt_sum = "";
    $visit_cnt_sum = 0;
    $visitor_cnt_sum = 0;
    if($SelectReport == ""){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    //echo "depth:".$depth."<br>";

    if($depth == "" ){//|| $depth == 2
        $depth = 1;
    }else{
        $depth = $depth+1;
    }

    //echo "depth:".$depth."<br>";


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


//echo $depth;
    //$sql = "Select r.cid, r.depth, r.cname, b.visit_cnt from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b where b.vdate = '$vdate' and substring(r.cid,0,6) = substring(b.vreferer_id,0,6) and r.depth = $depth group by r.cid, r.cname order by visit_cnt desc";
    if ($SelectReport == 1){
        if($depth == 1){
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
							where b.vdate = '$vdate' and r.cid = b.vreferer_id
							group by substr(r.cid,1,6) order by visit_cnt desc";
            //and r.depth = $depth

        }else if($depth == 2){
            /*
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
                            from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
                            where b.vdate = '$vdate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)  and r.cid LIKE '".substr($referer_id,0,6)."%'
                            group by r.cid, r.depth, r.cname order by visit_cnt desc";
            */
            //and r.depth = $depth
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
							where b.vdate = '$vdate' and r.cid = b.vreferer_id and r.cid LIKE '".substr($referer_id,0,6)."%'
							group by substr(r.cid,1,9) order by visit_cnt desc";
            //and r.depth = $depth
        }else if($depth == 3){
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
							where b.vdate = '$vdate' and r.cid = b.vreferer_id  and r.cid LIKE '".substr($referer_id,0,9)."%'
							group by substr(r.cid,1,15) order by visit_cnt desc";
            //and r.depth = $depth
        }
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        if($depth == 1){
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
							where b.vdate between '$vdate' and '$vweekenddate' and r.cid = b.vreferer_id
							group by substr(r.cid,1,6)  order by visit_cnt desc";
            // and r.depth = $depth
        }else if($depth == 2){
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
							where b.vdate between '$vdate' and '$vweekenddate' and r.cid = b.vreferer_id 
							and r.cid LIKE '".substr($referer_id,0,6)."%'
							group by substr(r.cid,1,9) order by visit_cnt desc";
        }else if($depth == 3){
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
							where b.vdate between '$vdate' and '$vweekenddate' and r.cid = b.vreferer_id and r.cid LIKE '".substr($referer_id,0,9)."%'
							group by substr(r.cid,1,12) order by visit_cnt desc";
        }
        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        if($depth == 1){
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
							where b.vdate LIKE '".substr($vdate,0,6)."%' and r.cid = b.vreferer_id 
							group by substr(r.cid,1,6) order by visit_cnt desc";
            //and r.depth = $depth
        }else if($depth == 2){
            /*
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
                            from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
                            where b.vdate LIKE '".substr($vdate,0,6)."%' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9) and r.cid LIKE '".substr($referer_id,0,6)."%'
                            group by r.cid, r.depth, r.cname order by visit_cnt desc";
            */
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
							where b.vdate LIKE '".substr($vdate,0,6)."%' and r.cid = b.vreferer_id and r.cid LIKE '".substr($referer_id,0,6)."%'
							group by substr(r.cid,1,9)  order by visit_cnt desc";

        }else if($depth == 3){
            /*
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
                            from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
                            where b.vdate LIKE '".substr($vdate,0,6)."%' and substr(r.cid,1,12) = substr(b.vreferer_id,1,12) and r.cid LIKE '".substr($referer_id,0,9)."%'
                            group by r.cid, r.depth, r.cname order by visit_cnt desc";
            */
            $sql = "Select r.cid, r.depth, r.cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b
							where b.vdate LIKE '".substr($vdate,0,6)."%' and r.cid = b.vreferer_id and r.cid LIKE '".substr($referer_id,0,9)."%'
							group by substr(r.cid,1,12) order by visit_cnt desc";
        }
        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }



    if($sql != ''){
        //echo nl2br($sql);
        $fordb->query($sql);
    }

    $mstring = $mstring.TitleBar("유입사이트별 방문횟수",$dateString);



    $mstring .= "<table cellpadding=3 cellspacing=0 style='width:100%;table-layout:fixed;' ID='MaxViewProductTable' class='list_table_box'>
		<col width=5%>
		<col width='*'>
		<col width=15%>
		<col width=15%>
		<col width=15%>
		<col width=15%>
		\n";
    $mstring .= "<tr height=30 align=center>
						<td class=s_td >순</td>
						<td class=m_td >카테고리명</td>
						<td class=m_td >방문횟수</td>
						<td class=m_td >점유율(방문횟수)</td>
						<td class=e_td >방문자수</td>
						<td class=m_td >점유율(방문자수)</td>
						</tr>\n";
    if($fordb->total){
        $visitbyrefererinfo = $fordb->fetchall();
    }
    //print_r($visitbyrefererinfo);
    if(count($visitbyrefererinfo) > 0) {
        for ($i = 0; $i < count($visitbyrefererinfo); $i++) {
            $pre_visit_cnt_sum += $visitbyrefererinfo[$i]['visit_cnt'];
            $pre_visitor_cnt_sum += $visitbyrefererinfo[$i]['visitor_cnt'];
        }
    }

    if(count($visitbyrefererinfo) > 0) {

        for ($i = 0; $i < count($visitbyrefererinfo); $i++) {
            //$fordb->fetch($i);

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i' >
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('" . $i . "',true)\" onmouseout=\"mouseOnTD('$i',false)\" >" . ($i + 1) . "</td>
		<td class='point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" onclick=\"getRefererDetail('$vdate','$vweekenddate', '" . $visitbyrefererinfo[$i]['cid'] . "','" . $visitbyrefererinfo[$i]['depth'] . "');\" style='padding:10px;'><img src='/admin/images/i_add.gif' align=absmiddle> " . getRefererCategoryPath($visitbyrefererinfo[$i]['cid'], ($depth >= 3 ? 4 : $depth)) . "</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>" . number_format(returnZeroValue($visitbyrefererinfo[$i]['visit_cnt']), 0) . "&nbsp;</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>" . number_format($visitbyrefererinfo[$i]['visit_cnt'] / $pre_visit_cnt_sum * 100, 1) . "%&nbsp;</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>" . number_format(returnZeroValue($visitbyrefererinfo[$i]['visitor_cnt']), 0) . "&nbsp;</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>" . number_format($visitbyrefererinfo[$i]['visitor_cnt'] / $pre_visitor_cnt_sum * 100, 1) . "%&nbsp;</td>
		</tr>
		<tr id='url_" . $visitbyrefererinfo[$i]['cid'] . "' style='display:none;'>
			<td align=left colspan=6 style='padding-left:60px;'></td>
		</tr>\n";

            $visit_cnt_sum = $visit_cnt_sum + CheckGraphValue($visitbyrefererinfo[$i]['visit_cnt']);
            $visitor_cnt_sum = $visitor_cnt_sum + CheckGraphValue($visitbyrefererinfo[$i]['visitor_cnt']);

            if (false) {
                $visit_referer_urls = getRefererURL($vdate, $vweekenddate, $visitbyrefererinfo[$i]['cid'], $visitbyrefererinfo[$i]['depth'], $fordb);

                for ($j = 0; $j < count($visit_referer_urls); $j++) {
                    if ($visit_referer_urls[$j]['vurl'] != "") {
                        $mstring .= "<tr class='url_" . $visitbyrefererinfo[$i]['cid'] . "' style='display:none;'>
					<td align=right>" . ($j + 1) . "</td>
					<td colspan=5 style='padding:8px;0px;word-break:break-all;'>
					<a href='http://" . $visit_referer_urls[$j]['vurl'] . "?" . $visit_referer_urls[$j]['keyword'] . "' target=_blank>
					" . $visit_referer_urls[$j]['vurl'] . "" . (trim($visit_referer_urls[$j]['keyword']) != "" ? "?" . $visit_referer_urls[$j]['keyword'] : "") . "
					</a>
					</td>
				</tr>\n";
                    }
                }
            }


        }
    }

    if ($visit_cnt_sum == 0){
        $mstring .= "<tr height=50 bgcolor=#ffffff align=center><td colspan=6>결과값이 없습니다.</td></tr>\n";
    }
    ;
    $mstring .= "<tr height=30 align=right>
	<td class=s_td align=center colspan=2>합계</td>

	<td class=m_td style='padding-right:20px;'>".number_format($visit_cnt_sum,0)."</td>
	<td class=m_td style='padding-right:20px;'>100%</td>
	<td class=e_td style='padding-right:20px;'>".number_format($visitor_cnt_sum,0)."</td>
	<td class=m_td style='padding-right:20px;'>100%</td>
	</tr>\n";
    $mstring .= "</table>\n";

    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:140%'>
                - 쇼핑몰을 방문하는 고객의 유입경로에대한 분석입니다. <br>
                - 이는 검색엔진및 온라인 프로모션에 대한 평가의 잣대기 되기도 하며 결과를 바탕으로 다시 프로모션을 진행하시면 보다 효율적으로
                대응하실수 있습니다<br>
                - 좌측에 카테고리를 클릭하시면 해당분류에 대한 상세리포트를 확인하실수 있습니다
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("유입사이트별 방문횟수", $help_text);

    return $mstring;
}

if ($mode == "iframe"){
//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'visitbyreferer.php';

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

    $Script = "
<script language='JavaScript' >
function getRefererDetail(vdate, vweekenddate, referer_id, referer_depth){
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'getRefererDetail','vdate': vdate,'vweekenddate': vweekenddate,'referer_id': referer_id,'referer_depth': referer_depth},
		url: 'visitbyreferer.act.php',  
		dataType: 'json', 
		async: true, 
		beforeSend: function(){ 
			//alert(referer_id);
		},  
		success: function(datas){ 
			//alert(datas);
			var mstring = '<table>';
			$.each(datas, function(i,data){ 
				mstring += '<tr><td>'+data.vurl+'</td></tr>';
			}); 
			mstring +='</table>';
			//alert(mstring);
			$('#url_'+referer_id).find('td').html(mstring);
			$('#url_'+referer_id).show()

		},
		error:function(request,status,error){
			alert('code:'+request.status+'\\n'+'message:'+request.responseText+'\\n'+'error:'+error);
		}
	});
}
</script>
";


    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 유입사이트 분석 > 유입사이트별 방문횟수";
    $p->title = "유입사이트별 방문횟수";
    $p->forbizLeftMenu = Stat_munu('visitbyreferer.php', "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode('visitbyreferer.php',date("Ymd", time()), "referer",3)."</div>");
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>".$Script;
//$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->PrintReportPage();
}

function getRefererURL( $vdate, $vweekenddate, $vreferer_id , $depth,$db){
    global $SelectReport;
    if ($SelectReport == 1){
        $date_where = "and vdate= '$vdate' ";
    }else if($SelectReport == 2){
        $date_where = "and vdate between '".$vdate."' and '".$vweekenddate."' ";
    }else if($depth == 3){
        $date_where = "and vdate LIKE '".substr($vdate,0,6)."%' ";
    }
    $sql = "SELECT * FROM logstory_refererurl where vreferer_id LIKE '".substr($vreferer_id,0,($depth+1)*3)."%'  ".$date_where." order by vurl asc limit 100 ";
    //echo $sql."<br>";
    $db->query($sql);

    return $db->fetchall();
}

?>
