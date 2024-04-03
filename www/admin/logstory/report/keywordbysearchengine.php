<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");
include("../report/keywordbysearchengine.chart.php");

if(empty($rdepth)){
    $rdepth = 0;
}

if(empty($referer_id)){
    $referer_id = "000000000000000";
}

function ReportTable($vdate,$SelectReport=1){
    global $rdepth,$referer_id;
    $pageview01 = 0;
    $chart_data = array();
    if($SelectReport == ""){
        $SelectReport = 1;
    }else if($SelectReport == "4"){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    if($rdepth == ""){
        $rdepth = 1;
    }

    //echo $cid;


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



    //$sql = "Select r.cid, r.cname, b.visit_cnt from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b where b.vdate = '$vdate' and substr(r.cid,0,6) = substr(b.vreferer_id,0,6) and r.depth = $rdepth group by r.cid, r.cname order by visit_cnt desc";
    if ($SelectReport == 1){
        /*
            if($rdepth == 1){
                $sql = "select vreferer_id, cname, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt ";
                $sql .= "from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k ";
                $sql .= "where k.kid = b.kid and  b.vdate = '$vdate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)  and r.depth = 2 and substr(b.vreferer_id,1,6) = '000001' group by vreferer_id, cname ";
                $sql .= "order by visit_cnt desc, vlevel1, vlevel2,vlevel3 ";
            }else if($rdepth == 2){
                $sql = "select vreferer_id, cname, keyword, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt
                                from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
                                where k.kid = b.kid and  b.vdate = '$vdate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)
                                and r.depth = 2 and b.vreferer_id LIKE '".substr($referer_id,0,9)."%'   ";
                $sql .= "group by vreferer_id, keyword order by  visit_cnt desc";
            }
        */
        if($rdepth == 1){
            $sql = "select vreferer_id, cname, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt ";
            $sql .= "from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k ";
            $sql .= "where k.kid = b.kid and  b.vdate = '$vdate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)  and r.depth = 2 and substr(b.vreferer_id,1,6) = '000001' group by vreferer_id ,cname ";
            $sql .= "order by visit_cnt desc "; //vlevel1, vlevel2,vlevel3
        }else if($rdepth == 2){
            $sql = "select vreferer_id, cname, keyword, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt
								from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
								where k.kid = b.kid and  b.vdate = '$vdate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)
								and r.depth = 2 and b.vreferer_id LIKE '".substr($referer_id,0,9)."%'   ";
            $sql .= "group by vreferer_id, cname, keyword order by  visit_cnt desc";
        }
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        if($rdepth == 1){
            $sql = "select vreferer_id, cname, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt ";
            $sql .= "from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k ";
            $sql .= "where k.kid = b.kid and  b.vdate between '$vdate' and '$vweekenddate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)  and r.depth = 2 and substr(b.vreferer_id,1,6) = '000001' group by vreferer_id, cname ";
            $sql .= "order by  visit_cnt desc";
        }else if($rdepth == 2){
            //	$sql = "select vreferer_id, cname, keyword, sum(b.visit_cnt) as visit_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k where k.kid = b.kid and  b.vdate = '$vdate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)  and r.depth = 2 and b.vreferer_id LIKE '".substr($referer_id,0,9)."%' ";
            //	$sql .= "group by vreferer_id, keyword order by vlevel1, vlevel2,vlevel3, visit_cnt desc";

            $sql = "select vreferer_id, cname, keyword, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt
						from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
						where k.kid = b.kid and  b.vdate between '$vdate' and '$vweekenddate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)
						and r.depth = 2 and b.vreferer_id LIKE '".substr($referer_id,0,9)."%'
						and ".($fordb->dbms_type == "oracle" ? "keyword is not null " : "keyword <> '' " )."
						group by vreferer_id, cname, keyword order by visit_cnt desc";
        }
        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        if($rdepth == 1){
            $sql = "select vreferer_id, cname, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt ";
            $sql .= "from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k ";
            $sql .= "where k.kid = b.kid and  b.vdate LIKE '".substr($vdate,0,6)."%'  and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)  and r.depth = 2 and substr(b.vreferer_id,1,6) = '000001'
						group by vreferer_id, cname ";
            $sql .= "order by visit_cnt desc";
        }else if($rdepth == 2){
            $sql = "select vreferer_id, cname, keyword, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt
						from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
						where k.kid = b.kid and b.vdate LIKE '".substr($vdate,0,6)."%'
						and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)
						and r.depth = 2 and b.vreferer_id LIKE '".substr($referer_id,0,9)."%'  and ".($fordb->dbms_type == "oracle" ? "keyword is not null " : "keyword <> '' " )."
						group by vreferer_id, cname, keyword
						order by visit_cnt desc";
        }
        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }
    //echo $rdepth;

    $fordb->query($sql);

    if(isset($_GET["mode"]) && ($_GET["mode"] == "excel")){

        include '../../include/phpexcel/Classes/PHPExcel.php';
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

        //date_default_timezone_set('Asia/Seoul');

        $sheet = new PHPExcel();

        // 속성 정의
        $sheet->getProperties()->setCreator("포비즈 코리아")
            ->setLastModifiedBy("Mallstory.com")
            ->setTitle("accounts plan price List")
            ->setSubject("accounts plan price List")
            ->setDescription("generated by forbiz korea")
            ->setKeywords("mallstory")
            ->setCategory("accounts plan price List");
        $col = 'A';
        $i=0;

        $start=3;
        $i = $start;

        $sheet->getActiveSheet(0)->mergeCells('A2:G2');
        $sheet->getActiveSheet(0)->setCellValue('A2', "검색엔진별 키워드");
        $sheet->getActiveSheet(0)->mergeCells('A3:G3');
        $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);


        $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "순");
        $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "검색엔진");
        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "키워드");
        $sheet->getActiveSheet(0)->setCellValue('D' . ($i+1), "방문횟수");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "방문회수 점유율");
        $sheet->getActiveSheet(0)->setCellValue('F' . ($i+1), "방문자수");
        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+1), "방문자수 점유율");

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $visit_sum = $visit_sum + CheckGraphValue($fordb->dt[visit_cnt]);
            $visitor_sum = $visitor_sum + CheckGraphValue($fordb->dt[visitor_cnt]);
        }
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            if($visit_sum > 0){
                $view_rate = $fordb->dt[visit_cnt]/$visit_sum; // 엑셀 포맷 정의시 자동으로 *100 이 처리됨
            }else{
                $view_rate = 0;
            }

            if($visitor_sum > 0){
                $visitor_rate = $fordb->dt[visitor_cnt]/$visitor_sum; // 엑셀 포맷 정의시 자동으로 *100 이 처리됨
            }else{
                $visitor_rate = 0;
            }

            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), ($i + 1));
            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), $fordb->dt[cname]);


            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), $fordb->dt[keyword]);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), $fordb->dt[visit_cnt]);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 2), $view_rate);
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 2), $fordb->dt[visitor_cnt]);
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 2), $visitor_rate);
            $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            //$nview_cnt_sum = $nview_cnt_sum + returnZeroValue($fordb->dt[nview_cnt]);
            //$order_change_rate_sum += $fordb->dt[order_detail_cnt]/$fordb->dt[nview_cnt]*100;


        }

        $sheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        //$sheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $sheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $sheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $sheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $sheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $sheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","검색엔진별 키워드.xls").'"');
        header('Cache-Control: max-age=0');

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $sheet->getActiveSheet()->getStyle('A'.($start+1).':G'.($i+$start+1))->applyFromArray($styleArray);
        $sheet->getActiveSheet()->getStyle('A'.$start.':G'.($i+$start+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $sheet->getActiveSheet()->getStyle('A'.($start).':G'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($start).':G'.($start+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true); // 타이틀 영역 가운데 정렬
        $sheet->getActiveSheet()->getStyle('B'.($start+2).':B'.($i+$start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT); // 카테고리 영역 왼쪽 정렬
        //$sheet->getActiveSheet()->getStyle('A'.$start.':J'.($i+$start+3))->getAlignment()->setIndent(1);
        //$sheet->getActiveSheet()->getStyle('A10')->getAlignment()->setIndent(10); 왼쪽 들여쓰기
        $sheet->getActiveSheet()->getStyle('A'.$start.':G'.($i+$start+3))->getFont()->setSize(10)->setName('돋움');  // 리포트 영역 폰트 설정

        $sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움'); // 타이틀 폰트 설정
        $sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // 타이틀 정렬
        $sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); // 리포트 타입 정렬
        $sheet->getActiveSheet()->getStyle('A'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // 합계텍스트 정렬
        //

        unset($styleArray);
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        $objWriter->save('php://output');

        exit;
    }

    $exce_down_str = "<a href='?mode=excel&".str_replace(array("&mode=iframe","mode=pop&"), "",$_SERVER["QUERY_STRING"])."'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";

    if(isset($_GET["mode"]) && ($_GET["mode"] == "pop")){
        $mstring = TitleBar("검색엔진별 키워드",$dateString, false, $exce_down_str);
    }else{
        $mstring = TitleBar("검색엔진별 키워드",$dateString, true, $exce_down_str);
    }

    $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
    if($fordb->total){
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;' align=left>".keywordbysearchengineGraph($vdate,$SelectReport)."</td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding:30px 0px;text-align:center;'><div id='piechart' style='width: 70%; height: 300px; padding: 0px; position: relative;'></div></td></tr>\n";

        $color[] = "#D9534F";
        $color[] = "#1CAF9A";
        $color[] = "#F0AD4E";
        $color[] = "#428BCA";
        $color[] = "#5BC0DE";

    }

    $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
    $mstring .= "</table>";

    if($rdepth == 1){
        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td width=50 class=s_td width=30>순</td><td class=m_td width=390>검색엔진</td><td class=m_td width=190>방문횟수</td><td class=m_td width=190>방문회수 점유율</td><td class=m_td width=190>방문자수</td><td class=e_td width=190>방문자수 점유율</td></tr>\n";
        $visit_sum = 0;
        $visitor_sum = 0;
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $visit_sum = $visit_sum + CheckGraphValue($fordb->dt[visit_cnt]);
            $visitor_sum = $visitor_sum + CheckGraphValue($fordb->dt[visitor_cnt]);

            if($i < 10){
                $chart_data[] = array(
                    label => $fordb->dt[cname],
                    data => array(0=>array("1",$fordb->dt[visit_cnt])) ,
                    color => $color[$i]
                );
            }

        }

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='point' align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt[cname]."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[visit_cnt]),0)." </td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(($fordb->dt[visit_cnt]/$visit_sum*100),1)."%</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[visitor_cnt]),0)."</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(($fordb->dt[visitor_cnt]/$visitor_sum*100),1)."%</td>
		</tr>\n";


//		$visit_sum = $visit_sum + CheckGraphValue($fordb->dt[visit_cnt]);
//		$visitor_sum = $visitor_sum + CheckGraphValue($fordb->dt[visitor_cnt]);


        }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
        if ($visit_sum == 0){
            $mstring .= "<tr height=100 bgcolor=#ffffff align=center><td colspan=6>결과값이 없습니다.</td></tr>\n";
        }

        $mstring .= "<tr height=30 align=right>
	<td class=s_td align=center colspan=2>합계</td>

	<td class=m_td style='padding-right:20px;'>".number_format($visit_sum,0)."</td>
	<td class=m_td style='padding-right:20px;'>100%</td>
	<td class=m_td style='padding-right:20px;' >".number_format($visitor_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>100%</td>
	</tr>\n";
        $mstring .= "</table>\n";
    }else if($rdepth == 2 || $rdepth == 3){ // 2013.01.07 일 신훈식 임시추가
        //exit;
        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center>
						<td width=50 class=s_td width=30>순</td>
						<td class=m_td width=100>검색엔진</td>
						<td class=m_td width=130>키워드</td>
						<td class=m_td width=100>방문횟수</td>
						<td class=m_td width=100>방문횟수 점유율</td>
						<td class=m_td width=100>방문자수</td>
						<td class=e_td width=100>방문자수 점유율</td>
						</tr>\n";
        $visit_sum = 0;
        $visitor_sum = 0;
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $visit_sum = $visit_sum + CheckGraphValue($fordb->dt[visit_cnt]);
            $visitor_sum = $visitor_sum + CheckGraphValue($fordb->dt[visitor_cnt]);

            if($i < 10){
                $chart_data[] = array(
                    label => $fordb->dt[cname],
                    data => array(0=>array("1",$fordb->dt[visit_cnt])) ,
                    color => $color[$i]
                );
            }

        }
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='point' align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt[cname]."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".$fordb->dt[keyword]."</td>
		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($fordb->dt[visit_cnt],0)."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(($fordb->dt[visit_cnt]/$visit_sum*100),1)."%</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[visitor_cnt]),0)."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(($fordb->dt[visitor_cnt]/$visitor_sum*100),1)."%</td>
		</tr>\n";

            $visit_sum = $visit_sum + CheckGraphValue($fordb->dt[visit_cnt]);
            $visitor_sum = $visitor_sum + CheckGraphValue($fordb->dt[visitor_cnt]);


        }
        //$mstring .= "</table>\n";
        //$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        if ($visit_sum == 0){
            $mstring .= "<tr height=100 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
        }

        $mstring .= "<tr height=30 align=center>
						<td  class=s_td   colspan=2>합계</td>
						<td class=m_td  >&nbsp;</td>
						<td class=m_td   align=right>".number_format($visit_sum)."</td>
						<td class=m_td  >-</td>
						<td class=m_td   align=right>".$visitor_sum."</td>
						<td class=e_td >-</td>
						</tr>\n";
        $mstring .= "</table>\n";
    }
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:140%'>
                ① 쇼핑몰을 방문하는 고객의 검색어별 키워드에 대한 분석입니다. <br>
                ② 각종 온라인 프로모션에 대한 상세한 리포트로써 귀사의 프로모션에 대한 효율을 높여드릴것입니다<br>
                ③ 또한 <b>오버츄어</b> 광고에 대한  결과를 확인할수 있습니다
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


    $mstring .= HelpBox("검색엔진별 키워드 ", $help_text);

    return $mstring;
}
if ($mode == "iframe"){
    //echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    //echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    //echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'keywordbysearchengine.php';

    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";
    echo "<div id='report_view'>".ReportTable($vdate,$SelectReport)."</div>";
    echo "<div id='calendar_view'>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</div>";
    echo "</body>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.getElementById('report_view').innerHTML;</Script>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.getElementById('calendar_view').innerHTML;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";
    echo "</html>";

    //echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    //echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";
}else if($mode == "pop" || $mode == "report" || $mode == "print"){
    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->Navigation = "로그분석 > 유입사이트 분석 > 검색엔진별 키워드";
    $P->NaviTitle = "검색엔진별 키워드";
    $P->title = "검색엔진별 키워드";
    $P->strContents = ReportTable($vdate,$SelectReport);
    $P->OnloadFunction = "";
    //	$P->layout_display = false;
    echo $P->PrintLayOut();

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 유입사이트 분석 > 검색엔진별 키워드 ";
    $p->title = "검색엔진별 키워드 ";
    $p->forbizLeftMenu = Stat_munu('keywordbysearchengine.php', "<div id=TREE_BAR style=\"width:100px;margin:10px 5px;orverflow:auto;\">".GetTreeNode('keywordbysearchengine.php',date("Ymd", time()),"search_engine")."</div>");
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>";
    //$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->PrintReportPage();
}
?>
