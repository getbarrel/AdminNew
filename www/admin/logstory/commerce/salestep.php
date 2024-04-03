<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");

function ReportTable($vdate,$SelectReport=1){

    $pageview01 = 0;
    $mstring = "";
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
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

    if($SelectReport == 1){
        /*
        $sql = "Select IFNULL(sum(case when is_order = 0 then step1 else 0 end),0) as step1,
                IFNULL(sum(case when is_order = 0 then step2 else 0 end),0) as step2,
                IFNULL(sum(case when is_order = 0 then step3 else 0 end),0) as step3,
                IFNULL(sum(case when is_order = 0 then step4 else 0 end),0) as step4,
                IFNULL(sum(case when is_order = 0 then step5 else 0 end),0) as step5,
                IFNULL(sum(step6),0) as step6
        */
        if($_GET["report_type"] == 2){
            $sql = "Select IFNULL(sum(step1),0) as step1, 
				IFNULL(sum(step2),0) as step2, 
				IFNULL(sum(step3),0) as step3, 
				IFNULL(sum(step4),0) as step4, 
				IFNULL(sum(step5),0) as step5, 
				IFNULL(sum(step6),0) as step6,
				sum(case when agent_type = 'W' then IFNULL(step1,0) else 0 end ) as web_step1, 
				sum(case when agent_type = 'W' then IFNULL(step2,0) else 0 end ) as web_step2, 
				sum(case when agent_type = 'W' then IFNULL(step3,0) else 0 end ) as web_step3, 
				sum(case when agent_type = 'W' then IFNULL(step4,0) else 0 end ) as web_step4, 
				sum(case when agent_type = 'W' then IFNULL(step5,0) else 0 end ) as web_step5, 
				sum(case when agent_type = 'W' then IFNULL(step6,0) else 0 end ) as web_step6,
				sum(case when agent_type = 'M' then IFNULL(step1,0) else 0 end ) as mobile_step1, 
				sum(case when agent_type = 'M' then IFNULL(step2,0) else 0 end ) as mobile_step2, 
				sum(case when agent_type = 'M' then IFNULL(step3,0) else 0 end ) as mobile_step3, 
				sum(case when agent_type = 'M' then IFNULL(step4,0) else 0 end ) as mobile_step4, 
				sum(case when agent_type = 'M' then IFNULL(step5,0) else 0 end ) as mobile_step5, 
				sum(case when agent_type = 'M' then IFNULL(step6,0) else 0 end ) as mobile_step6,
				sum(case when is_order = 1 and step6 != 1 then step1 else 0 end) as step1_revision,
				sum(case when is_order = 1 and step6 != 1 then step2 else 0 end) as step2_revision,
				sum(case when is_order = 1 and step6 != 1 then step3 else 0 end) as step3_revision,
				sum(case when is_order = 1 and step6 != 1 then step4 else 0 end) as step4_revision,
				sum(case when is_order = 1 and step6 != 1 then step5 else 0 end) as step5_revision
			from (
				select vdate, agent_type,
				max(step1) as step1 ,
				max(step2) as step2 ,
				max(step3) as step3 ,
				max(step4) as step4 ,
				max(step5) as step5 ,
				max(step6) as step6 ,
				max(is_order) as is_order
				from ".TBL_COMMERCE_SALESTACK." 
				where vdate = '$vdate' 
				group by vdate, ucode
				) cs
				where vdate = '$vdate' group by vdate ";
            $sql2 = "Select 
						sum(case when agent_type = 'W' then IFNULL(nview_cnt,0) else 0 end ) as web_cnt, 
						sum(case when agent_type = 'M' then IFNULL(nview_cnt,0) else 0 end ) as mobile_cnt, 
						IFNULL(sum(nview_cnt),0) as cnt  
						from ".TBL_COMMERCE_VIEWINGVIEW." 
						where vdate = '$vdate' group by vdate ";

        }else{
            $sql = "Select IFNULL(sum(step1),0) as step1, 
						IFNULL(sum(step2),0) as step2, 
						IFNULL(sum(step3),0) as step3, 
						IFNULL(sum(step4),0) as step4, 
						IFNULL(sum(step5),0) as step5, 
						IFNULL(sum(step6),0) as step6,
						sum(case when agent_type = 'W' then IFNULL(step1,0) else 0 end ) as web_step1, 
						sum(case when agent_type = 'W' then IFNULL(step2,0) else 0 end ) as web_step2, 
						sum(case when agent_type = 'W' then IFNULL(step3,0) else 0 end ) as web_step3, 
						sum(case when agent_type = 'W' then IFNULL(step4,0) else 0 end ) as web_step4, 
						sum(case when agent_type = 'W' then IFNULL(step5,0) else 0 end ) as web_step5, 
						sum(case when agent_type = 'W' then IFNULL(step6,0) else 0 end ) as web_step6,
						sum(case when agent_type = 'M' then IFNULL(step1,0) else 0 end ) as mobile_step1, 
						sum(case when agent_type = 'M' then IFNULL(step2,0) else 0 end ) as mobile_step2, 
						sum(case when agent_type = 'M' then IFNULL(step3,0) else 0 end ) as mobile_step3, 
						sum(case when agent_type = 'M' then IFNULL(step4,0) else 0 end ) as mobile_step4, 
						sum(case when agent_type = 'M' then IFNULL(step5,0) else 0 end ) as mobile_step5, 
						sum(case when agent_type = 'M' then IFNULL(step6,0) else 0 end ) as mobile_step6,
						sum(case when is_order = 1 and step6 != 1 then step1 else 0 end) as step1_revision,
						sum(case when is_order = 1 and step6 != 1 then step2 else 0 end) as step2_revision,
						sum(case when is_order = 1 and step6 != 1 then step3 else 0 end) as step3_revision,
						sum(case when is_order = 1 and step6 != 1 then step4 else 0 end) as step4_revision,
						sum(case when is_order = 1 and step6 != 1 then step5 else 0 end) as step5_revision
			from ".TBL_COMMERCE_SALESTACK." where vdate = '$vdate' group by vdate";
            $sql2 = "Select 
						sum(case when agent_type = 'W' then IFNULL(nview_cnt,0) else 0 end ) as web_cnt, 
						sum(case when agent_type = 'M' then IFNULL(nview_cnt,0) else 0 end ) as mobile_cnt, 
						IFNULL(sum(nview_cnt),0) as cnt  
						from ".TBL_COMMERCE_VIEWINGVIEW." where vdate = '$vdate' group by vdate";
        }

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        if($_GET["report_type"] == 2){
            $sql = "Select IFNULL(sum(step1),0) as step1, 
				IFNULL(sum(step2),0) as step2, 
				IFNULL(sum(step3),0) as step3, 
				IFNULL(sum(step4),0) as step4, 
				IFNULL(sum(step5),0) as step5, 
				IFNULL(sum(step6),0) as step6,
				sum(case when agent_type = 'W' then IFNULL(step1,0) else 0 end ) as web_step1, 
				sum(case when agent_type = 'W' then IFNULL(step2,0) else 0 end ) as web_step2, 
				sum(case when agent_type = 'W' then IFNULL(step3,0) else 0 end ) as web_step3, 
				sum(case when agent_type = 'W' then IFNULL(step4,0) else 0 end ) as web_step4, 
				sum(case when agent_type = 'W' then IFNULL(step5,0) else 0 end ) as web_step5, 
				sum(case when agent_type = 'W' then IFNULL(step6,0) else 0 end ) as web_step6,
				sum(case when agent_type = 'M' then IFNULL(step1,0) else 0 end ) as mobile_step1, 
				sum(case when agent_type = 'M' then IFNULL(step2,0) else 0 end ) as mobile_step2, 
				sum(case when agent_type = 'M' then IFNULL(step3,0) else 0 end ) as mobile_step3, 
				sum(case when agent_type = 'M' then IFNULL(step4,0) else 0 end ) as mobile_step4, 
				sum(case when agent_type = 'M' then IFNULL(step5,0) else 0 end ) as mobile_step5, 
				sum(case when agent_type = 'M' then IFNULL(step6,0) else 0 end ) as mobile_step6,
				sum(case when is_order = 1 and step6 != 1 then step1 else 0 end) as step1_revision,
				sum(case when is_order = 1 and step6 != 1 then step2 else 0 end) as step2_revision,
				sum(case when is_order = 1 and step6 != 1 then step3 else 0 end) as step3_revision,
				sum(case when is_order = 1 and step6 != 1 then step4 else 0 end) as step4_revision,
				sum(case when is_order = 1 and step6 != 1 then step5 else 0 end) as step5_revision
			from (
				select vdate, agent_type,
				max(step1) as step1 ,
				max(step2) as step2 ,
				max(step3) as step3 ,
				max(step4) as step4 ,
				max(step5) as step5 ,
				max(step6) as step6 ,
				max(is_order) as is_order
				from ".TBL_COMMERCE_SALESTACK." 
				where vdate between '$vdate' and '$vweekenddate'
				group by vdate, ucode
				) cs
				where vdate between '$vdate' and '$vweekenddate' group by vdate ";
            $sql2 = "Select 
						sum(case when agent_type = 'W' then IFNULL(nview_cnt,0) else 0 end ) as web_cnt, 
						sum(case when agent_type = 'M' then IFNULL(nview_cnt,0) else 0 end ) as mobile_cnt, 
						IFNULL(sum(nview_cnt),0) as cnt  
						from ".TBL_COMMERCE_VIEWINGVIEW." where vdate between '$vdate' and '$vweekenddate' group by vdate ";

        }else{
            $sql = "Select IFNULL(sum(step1),0) as step1, 
						IFNULL(sum(step2),0) as step2, 
						IFNULL(sum(step3),0) as step3, 
						IFNULL(sum(step4),0) as step4, 
						IFNULL(sum(step5),0) as step5, 
						IFNULL(sum(step6),0) as step6,
						sum(case when agent_type = 'W' then IFNULL(step1,0) else 0 end ) as web_step1, 
						sum(case when agent_type = 'W' then IFNULL(step2,0) else 0 end ) as web_step2, 
						sum(case when agent_type = 'W' then IFNULL(step3,0) else 0 end ) as web_step3, 
						sum(case when agent_type = 'W' then IFNULL(step4,0) else 0 end ) as web_step4, 
						sum(case when agent_type = 'W' then IFNULL(step5,0) else 0 end ) as web_step5, 
						sum(case when agent_type = 'W' then IFNULL(step6,0) else 0 end ) as web_step6,
						sum(case when agent_type = 'M' then IFNULL(step1,0) else 0 end ) as mobile_step1, 
						sum(case when agent_type = 'M' then IFNULL(step2,0) else 0 end ) as mobile_step2, 
						sum(case when agent_type = 'M' then IFNULL(step3,0) else 0 end ) as mobile_step3, 
						sum(case when agent_type = 'M' then IFNULL(step4,0) else 0 end ) as mobile_step4, 
						sum(case when agent_type = 'M' then IFNULL(step5,0) else 0 end ) as mobile_step5, 
						sum(case when agent_type = 'M' then IFNULL(step6,0) else 0 end ) as mobile_step6,
						sum(case when is_order = 1 and step6 != 1 then step1 else 0 end) as step1_revision,
						sum(case when is_order = 1 and step6 != 1 then step2 else 0 end) as step2_revision,
						sum(case when is_order = 1 and step6 != 1 then step3 else 0 end) as step3_revision,
						sum(case when is_order = 1 and step6 != 1 then step4 else 0 end) as step4_revision,
						sum(case when is_order = 1 and step6 != 1 then step5 else 0 end) as step5_revision
			from ".TBL_COMMERCE_SALESTACK."
			where vdate between '$vdate' and '$vweekenddate' ";

            $sql2 = "Select sum(case when agent_type = 'W' then IFNULL(nview_cnt,0) else 0 end ) as web_cnt, 
						sum(case when agent_type = 'M' then IFNULL(nview_cnt,0) else 0 end ) as mobile_cnt, 
						IFNULL(sum(nview_cnt),0) as cnt  
						from ".TBL_COMMERCE_VIEWINGVIEW." where vdate between '$vdate' and '$vweekenddate' ";
        }

        $dateString = "주간 : ". getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        if($_GET["report_type"] == 2){
            $sql = "Select IFNULL(sum(step1),0) as step1, 
				IFNULL(sum(step2),0) as step2, 
				IFNULL(sum(step3),0) as step3, 
				IFNULL(sum(step4),0) as step4, 
				IFNULL(sum(step5),0) as step5, 
				IFNULL(sum(step6),0) as step6,
				sum(case when agent_type = 'W' then IFNULL(step1,0) else 0 end ) as web_step1, 
				sum(case when agent_type = 'W' then IFNULL(step2,0) else 0 end ) as web_step2, 
				sum(case when agent_type = 'W' then IFNULL(step3,0) else 0 end ) as web_step3, 
				sum(case when agent_type = 'W' then IFNULL(step4,0) else 0 end ) as web_step4, 
				sum(case when agent_type = 'W' then IFNULL(step5,0) else 0 end ) as web_step5, 
				sum(case when agent_type = 'W' then IFNULL(step6,0) else 0 end ) as web_step6,
				sum(case when agent_type = 'M' then IFNULL(step1,0) else 0 end ) as mobile_step1, 
				sum(case when agent_type = 'M' then IFNULL(step2,0) else 0 end ) as mobile_step2, 
				sum(case when agent_type = 'M' then IFNULL(step3,0) else 0 end ) as mobile_step3, 
				sum(case when agent_type = 'M' then IFNULL(step4,0) else 0 end ) as mobile_step4, 
				sum(case when agent_type = 'M' then IFNULL(step5,0) else 0 end ) as mobile_step5, 
				sum(case when agent_type = 'M' then IFNULL(step6,0) else 0 end ) as mobile_step6,
				sum(case when is_order = 1 and step6 != 1 then step1 else 0 end) as step1_revision,
				sum(case when is_order = 1 and step6 != 1 then step2 else 0 end) as step2_revision,
				sum(case when is_order = 1 and step6 != 1 then step3 else 0 end) as step3_revision,
				sum(case when is_order = 1 and step6 != 1 then step4 else 0 end) as step4_revision,
				sum(case when is_order = 1 and step6 != 1 then step5 else 0 end) as step5_revision
			from (
				select vdate, agent_type,
				max(step1) as step1 ,
				max(step2) as step2 ,
				max(step3) as step3 ,
				max(step4) as step4 ,
				max(step5) as step5 ,
				max(step6) as step6 ,
				max(is_order) as is_order
				from ".TBL_COMMERCE_SALESTACK." 
				where vdate LIKE '".substr($vdate,0,6)."%' 
				group by vdate, ucode
				) cs
				where vdate LIKE '".substr($vdate,0,6)."%' group by vdate ";
            $sql2 = "Select sum(case when agent_type = 'W' then IFNULL(nview_cnt,0) else 0 end ) as web_cnt, 
						sum(case when agent_type = 'M' then IFNULL(nview_cnt,0) else 0 end ) as mobile_cnt, 
						IFNULL(sum(nview_cnt),0) as cnt  
						from ".TBL_COMMERCE_VIEWINGVIEW." where vdate LIKE '".substr($vdate,0,6)."%' group by vdate ";

        }else{
            $sql = "Select IFNULL(sum(step1),0) as step1, 
						IFNULL(sum(step2),0) as step2, 
						IFNULL(sum(step3),0) as step3, 
						IFNULL(sum(step4),0) as step4, 
						IFNULL(sum(step5),0) as step5, 
						IFNULL(sum(step6),0) as step6,
						sum(case when agent_type = 'W' then IFNULL(step1,0) else 0 end ) as web_step1, 
						sum(case when agent_type = 'W' then IFNULL(step2,0) else 0 end ) as web_step2, 
						sum(case when agent_type = 'W' then IFNULL(step3,0) else 0 end ) as web_step3, 
						sum(case when agent_type = 'W' then IFNULL(step4,0) else 0 end ) as web_step4, 
						sum(case when agent_type = 'W' then IFNULL(step5,0) else 0 end ) as web_step5, 
						sum(case when agent_type = 'W' then IFNULL(step6,0) else 0 end ) as web_step6,
						sum(case when agent_type = 'M' then IFNULL(step1,0) else 0 end ) as mobile_step1, 
						sum(case when agent_type = 'M' then IFNULL(step2,0) else 0 end ) as mobile_step2, 
						sum(case when agent_type = 'M' then IFNULL(step3,0) else 0 end ) as mobile_step3, 
						sum(case when agent_type = 'M' then IFNULL(step4,0) else 0 end ) as mobile_step4, 
						sum(case when agent_type = 'M' then IFNULL(step5,0) else 0 end ) as mobile_step5, 
						sum(case when agent_type = 'M' then IFNULL(step6,0) else 0 end ) as mobile_step6,
						sum(case when is_order = 1 and step6 != 1 then step1 else 0 end) as step1_revision,
						sum(case when is_order = 1 and step6 != 1 then step2 else 0 end) as step2_revision,
						sum(case when is_order = 1 and step6 != 1 then step3 else 0 end) as step3_revision,
						sum(case when is_order = 1 and step6 != 1 then step4 else 0 end) as step4_revision,
						sum(case when is_order = 1 and step6 != 1 then step5 else 0 end) as step5_revision
			from ".TBL_COMMERCE_SALESTACK." where vdate LIKE '".substr($vdate,0,6)."%' ";

            $sql2 = "Select sum(case when agent_type = 'W' then IFNULL(nview_cnt,0) else 0 end ) as web_cnt, 
						sum(case when agent_type = 'M' then IFNULL(nview_cnt,0) else 0 end ) as mobile_cnt, 
						IFNULL(sum(nview_cnt),0) as cnt  
						from ".TBL_COMMERCE_VIEWINGVIEW." where vdate LIKE '".substr($vdate,0,6)."%'";
            $dateString = getNameOfWeekday(0,$vdate,"monthname");
        }
    }

    //echo $sql;
    if($sql){
        $fordb->query($sql);
    }
    if($sql2){
        $fordb2->query($sql2);
    }


    $mstring = $mstring.TitleBar("구매단계분석",$dateString);
//	$mstring .= "<table cellpadding=0 cellspacing=0 width=745 border=0 >\n";
//	$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'>".etcrefererGraph($vdate,$SelectReport)."</td></tr>\n";
//	$mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
//	$mstring .= "</table>";
    /*
        $mstring .= "<table cellpadding=3 cellspacing=0 width=745  >\n";
        $mstring .= "<tr height=30  align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>페이지 명</td><td class=e_td width=50>페이지뷰</td></tr>\n";
        $mstring .= "<tr height=30  bgcolor=#ffffff>
            <td class='list_box_td list_bg_gray'  align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
            <td align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td class='list_box_td list_bg_gray'   align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">543</td>
            </tr>\n";
        $mstring .= "<tr height=30  bgcolor=#ffffff>
            <td class='list_box_td list_bg_gray'  align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
            <td align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td class='list_box_td list_bg_gray'   align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">33</td>
            </tr>\n";
        $mstring .= "</table><br>\nword-break:keep-all";
    */
    $mstring = "<table width='100%' border=0>";

    $mstring .= " 
						<tr height=45>
							<td >
								<div class='tab'>
									<table class='s_org_tab'>
									<tr>
										<td class='tab'> 
											<table id='tab_01'  ".(($_GET["report_type"] == '1' || $_GET["report_type"] == '') ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=1'\">상품기준 분석</td>
												<th class='box_03'></th>
											</tr>
											</table> 
											<table id='tab_01'   ".(($_GET["report_type"] == '2') ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=2'\">사용자기준 분석</td>
												<th class='box_03'></th>
											</tr>
											</table> 
										</td>
										<td class='btn' style='padding:5px 0px 0px 10px;'>
											 
										</td>
									</tr>
									</table>
								</div>
							</td>
						  </tr>
					</table>";
    $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box' >\n";
    $mstring = $mstring."<col width='20%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>";
    $mstring = $mstring."<tr height=30  align=center style='font-weight:bold'>
			<td class=s_td rowspan=2>구매단계</td>
			<td class=m_td colspan=3  nowrap>PC(웹)</td>
			<td class=m_td colspan=3  nowrap>모바일</td> 
			<td class=m_td colspan=3  nowrap>전체</td> 
			</tr>\n";
    $mstring .= "<tr height=30  align=center style='font-weight:bold'> 
	<td class=m_td nowrap>진입횟수(상품수)</td>
	<td class=m_td nowrap>이탈횟수</td>
	<td class=e_td nowrap>이탈율(%)</td>

	<td class=m_td nowrap>진입횟수(상품수)</td>
	<td class=m_td nowrap>이탈횟수</td>
	<td class=e_td nowrap>이탈율(%)</td>

	<td class=m_td nowrap>진입횟수(상품수)</td>
	<td class=m_td nowrap>이탈횟수</td>
	<td class=e_td nowrap>이탈율(%)</td>
	</tr>\n";
//	for($i=0;$i<$fordb->total;$i++){
    $fordb->fetch(0);
    $fordb2->fetch(0);
    $i = 0;


    $mstring .= "<tr height=30 id='Report$i'>
		<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >상품조회</td>
		
		<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".($fordb2->dt['web_cnt'] ? number_format($fordb2->dt['web_cnt']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format($fordb2->dt['web_cnt']-$fordb->dt['web_step1'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px''>".number_format(CheckDivision(($fordb2->dt['web_cnt']-$fordb->dt['web_step1']),$fordb2->dt['web_cnt'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".($fordb2->dt['mobile_cnt'] ? number_format($fordb2->dt['mobile_cnt']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format($fordb2->dt['mobile_cnt']-$fordb->dt['mobile_step1'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px''>".number_format(CheckDivision(($fordb2->dt['mobile_cnt']-$fordb->dt['mobile_step1']),$fordb2->dt['mobile_cnt'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".($fordb2->dt['cnt'] ? number_format($fordb2->dt['cnt']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format($fordb2->dt['cnt']-$fordb->dt['step1'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px''>".number_format(CheckDivision(($fordb2->dt['cnt']-$fordb->dt['step1']),$fordb2->dt['cnt'])*100,1)."</td>
		</tr>";



    $i = $i + 1;

    $mstring .= "<tr height=30 id='Report$i'>
		<td class='list_box_td list_bg_gray'   onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=left>쇼핑카트</td>
		
		<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".($fordb->dt['web_step1'] ? number_format($fordb->dt['web_step1']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format($fordb->dt['web_step1']-$fordb->dt['web_step2']-$fordb->dt['web_step1_revision'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(CheckDivision(($fordb->dt['web_step1']-$fordb->dt['web_step2']-$fordb->dt['web_step1_revision']),$fordb->dt['web_step1'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".($fordb->dt['mobile_step1'] ? number_format($fordb->dt['mobile_step1']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format($fordb->dt['mobile_step1']-$fordb->dt['mobile_step2']-$fordb->dt['mobile_step1_revision'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(CheckDivision(($fordb->dt['mobile_step1']-$fordb->dt['mobile_step2']-$fordb->dt['mobile_step1_revision']),$fordb->dt['mobile_step1'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".($fordb->dt['step1'] ? number_format($fordb->dt['step1']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format($fordb->dt['step1']-$fordb->dt['step2']-$fordb->dt['step1_revision'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(CheckDivision(($fordb->dt['step1']-$fordb->dt['step2']-$fordb->dt['step1_revision']),$fordb->dt['step1'])*100,1)."</td>
		</tr>";

    $i = $i + 1;

    $mstring .= "<tr height=30 id='Report$i'>
		<td class='list_box_td list_bg_gray'   onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=left>결제정보입력</td>
		
		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".($fordb->dt['web_step2'] ? number_format($fordb->dt['web_step2']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format($fordb->dt['web_step2']-$fordb->dt['web_step3']-$fordb->dt['web_step2_revision'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(CheckDivision(($fordb->dt['web_step2']-$fordb->dt['web_step3']-$fordb->dt['web_step2_revision']),$fordb->dt['web_step2'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".($fordb->dt['mobile_step2'] ? number_format($fordb->dt['mobile_step2']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format($fordb->dt['mobile_step2']-$fordb->dt['mobile_step3']-$fordb->dt['mobile_step2_revision'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(CheckDivision(($fordb->dt['mobile_step2']-$fordb->dt['mobile_step3']-$fordb->dt['mobile_step2_revision']),$fordb->dt['mobile_step2'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".($fordb->dt['step2'] ? number_format($fordb->dt['step2']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format($fordb->dt['step2']-$fordb->dt['step3']-$fordb->dt['step2_revision'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(CheckDivision(($fordb->dt['step2']-$fordb->dt['step3']-$fordb->dt['step2_revision']),$fordb->dt['step2'])*100,1)."</td>
		</tr>";
    $i = $i + 1;
    $mstring .= "<tr height=30  id='Report$i'>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"  align=left>결제정보확인</td>
		
		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px' >".($fordb->dt['web_step3'] ? number_format($fordb->dt['web_step3']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(($fordb->dt['web_step3']-$fordb->dt['web_step6']-$fordb->dt['web_step3_revision']))."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(CheckDivision(($fordb->dt['web_step3']-$fordb->dt['web_step6']-$fordb->dt['web_step3_revision']),$fordb->dt['web_step3'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px' >".($fordb->dt['mobile_step3'] ? number_format($fordb->dt['mobile_step3']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(($fordb->dt['mobile_step3']-$fordb->dt['mobile_step6']-$fordb->dt['mobile_step3_revision']))."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(CheckDivision(($fordb->dt['mobile_step3']-$fordb->dt['mobile_step6']-$fordb->dt['mobile_step3_revision']),$fordb->dt['mobile_step3'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px' >".($fordb->dt['step3'] ? number_format($fordb->dt['step3']):"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(($fordb->dt['step3']-$fordb->dt['step6']-$fordb->dt['step3_revision']))."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0px'>".number_format(CheckDivision(($fordb->dt['step3']-$fordb->dt['step6']-$fordb->dt['step3_revision']),$fordb->dt['step3'])*100,1)."</td>
		</tr>\n";
    $i = $i + 1;
    /*	$mstring .= "<tr height=30 class='list_box_td'  >
        <td class='list_box_td list_bg_gray'  id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px'>결제완료</td>
        <td class='list_box_td'  align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['step6']."&nbsp;</td>
        <td class='list_box_td list_bg_gray'  align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" width=390  wrap>-</td>
        </tr>\n";
    */

    $mstring .= "<tr height=30  align=right style='font-weight:bold'>
		<td class='list_box_td list_bg_gray'>결제완료</td>
		<td class='list_box_td point'  style='padding:0px'> ".($fordb->dt['web_step6'] ? number_format($fordb->dt['web_step6']):"0")."</td>
		<td class='list_box_td list_bg_gray' style='padding:0px'>-</td>
		<td class='list_box_td'  style='padding:0px'>-</td>

		<td class='list_box_td point'  style='padding:0px'> ".($fordb->dt['mobile_step6'] ? number_format($fordb->dt['mobile_step6']):"0")."</td>
		<td class='list_box_td list_bg_gray' style='padding:0px'>-</td>
		<td class='list_box_td'  style='padding:0px'>-</td>

		<td class='list_box_td point'  style='padding:0px'> ".($fordb->dt['step6'] ? number_format($fordb->dt['step6']):"0")."</td>
		<td class='list_box_td list_bg_gray' style='padding:0px'>-</td>
		<td class='list_box_td'  style='padding:0px'>-</td>
		</tr>\n";

    //	$exitcnt = $pageview01 + returnZeroValue($fordb->dt['visit_cnt']);


//	}
    $mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=745  >\n";
//	if ($pageview01 == 0){
//		$mstring .= "<tr height=50 class='list_box_td'  align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}
//	$mstring .= "<tr height=2 class='list_box_td'  align=center><td colspan=5 width=190></td></tr>\n";
//	$mstring .= "<tr height=30  align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
//	$mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 방문 고객이 쇼핑몰 상품 구매절차 중 이탈한 위치 및 횟수를 확인하실 수 있습니다.<br>
                (※ 분석 리포트 데이터 중 마이너스(-)로 표기 된 데이터는 이탈 없이 최종 구매 단계 또는 회원가입 단계를 완료한 데이터입니다.)
                <br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("구매단계분석", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'salestep.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('salestep.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 구매/회원단계 분석> 구매단계분석";
    $p->title = "구매단계분석";
    $p->PrintReportPage();
}
?>
