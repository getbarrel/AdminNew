<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");

function ReportTable($vdate,$SelectReport=1){
    $fordb = new forbizDatabase();
    $fordb1 = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();

    $oneweek_ago_web_data = "";
    $exce_down_str = "";
    $mstring = "";

    $visit01_sum_web = "";
    $visit01_sum_mobile = "";
    $visit02_sum_web = "";
    $visit02_sum_mobile = "";
    $visit03_sum_web = "";
    $visit03_sum_mobile = "";
    $visit04_sum_web = "";
    $visit04_mobile_web = "";

    $visit01_web = "";
    $visit01_mobile = "";
    $visit02_web = "";
    $visit02_mobile = "";
    $visit03_web = "";
    $visit03_mobile = "";
    $visit04_web  = "";
    $visit04_mobile = "";
    $minvalue = "";
    $maxvalue = "";
    $visit02 = "";
    $visit03 = "";
    $visit04 = "";
    $one_monthago_web_data = "";

    $sql = "";
    $sql2 = "";
    $sql3 = "";

    if($SelectReport == ""){
        $SelectReport = 1;
    }else if($SelectReport == "4"){
        $SelectReport = 1;
    }
    if($vdate == ""){
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $vtwoweekago = date("Ymd", time()-84600*14);
        $vfourweekago = date("Ymd", time()-84600*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),1,(int)substr($vdate,0,4))-60*60*24);
        $vtwomonthago = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),1,(int)substr($vdate,0,4))-60*60*24*60);
    }else{
        $vyesterday = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
        $vfourweekago = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),1,(int)substr($vdate,0,4))-60*60*24);
        $vtwomonthago = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),1,(int)substr($vdate,0,4))-60*60*24*60);
    }

    if($SelectReport == 1){
        $nLoop = 24;
    }else if($SelectReport ==2){
        $nLoop = 7;
    }else if($SelectReport ==3){
        $nLoop = date("t", mktime(0, 0, 0, (int)substr($vdate,4,2), (int)substr($vdate,6,2), (int)substr($vdate,0,4)));
    }

    if($SelectReport == 1){
        $sql = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 
					from ".TBL_LOGSTORY_VISITTIME." 
					where vdate = '$vdate'";

        $sql1 = "Select avg(nh00), avg(nh01), avg(nh02), avg(nh03), avg(nh04), avg(nh05), avg(nh06), avg(nh07), avg(nh08), avg(nh09), avg(nh10), avg(nh11), avg(nh12), avg(nh13), avg(nh14), avg(nh15), avg(nh16), avg(nh17), avg(nh18), avg(nh19), avg(nh20), avg(nh21), avg(nh22), avg(nh23) 
					from ".TBL_LOGSTORY_VISITTIME." 
					where substr(vdate,1,6) = '".substr($vonemonthago,0,6)."'";
        // 한달평균 --> 전달평균으로 변경
        $sql2 = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 from ".TBL_LOGSTORY_VISITTIME." where vdate = '$vyesterday'";
        $sql3 = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 from ".TBL_LOGSTORY_VISITTIME." where vdate = '$voneweekago'";

        $revisit_sql = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 
					from ".TBL_LOGSTORY_REVISITTIME." 
					where vdate = '$vdate'";


        $sql_web = $sql." and agent_type = 'W'  ";
        $sql_mobile = $sql." and agent_type = 'M' ";

        $fordb->query($sql_web);
        $fordb->fetch(0,"row");
        $selected_date_web_data = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_mobile_data = $fordb->dt;

        $sql1_web = $sql1." and agent_type = 'W'  ";
        $sql1_mobile = $sql1." and agent_type = 'M' ";

        $fordb1->query($sql1_web);
        $fordb1->fetch(0,"row");
        $one_monthago_web_data = $fordb1->dt;

        $fordb1->query($sql1_mobile);
        $fordb1->fetch(0,"row");
        $one_monthago_mobile_data = $fordb1->dt;

        //$fordb2->query($sql2);
        $sql2_web = $sql2." and agent_type = 'W'  ";
        $sql2_mobile = $sql2." and agent_type = 'M' ";

        $fordb2->query($sql2_web);
        $fordb2->fetch(0,"row");
        $yesterday_web_data = $fordb2->dt;

        $fordb2->query($sql2_mobile);
        $fordb2->fetch(0,"row");
        $yesterday_mobile_data = $fordb2->dt;

        $sql3_web = $sql3." and agent_type = 'W'  ";
        $sql3_mobile = $sql3." and agent_type = 'M' ";

        $fordb3->query($sql3_web);
        $fordb3->fetch(0,"row");
        $oneweek_ago_web_data = $oneweek_ago_web_data;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $oneweek_ago_mobile_data = $oneweek_ago_web_data;
        //echo "total:".$fordb->total;
        $fordb->fetch(0);
        $fordb1->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");

    }else if($SelectReport == 2){
        $sql = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_VISITTIME."
		 	where vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."'
			 ";

        $sql2 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_VISITTIME."
		 	where vdate between '$voneweekago' and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."'
		 	 ";
        $sql3 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_VISITTIME."
		 	where vdate between '$vfourweekago' and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."'
			 ";



        $sql_web = $sql." and agent_type = 'W'  ";
        $sql_mobile = $sql." and agent_type = 'M' ";

        $fordb->query($sql_web);
        $fordb->fetch(0,"row");
        $selected_date_web_data = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_mobile_data = $fordb->dt;


        //$fordb2->query($sql2);
        $sql2_web = $sql2." and agent_type = 'W'  ";
        $sql2_mobile = $sql2." and agent_type = 'M' ";

        $fordb2->query($sql2_web);
        $fordb2->fetch(0,"row");
        $yesterday_web_data = $fordb2->dt;

        $fordb2->query($sql2_mobile);
        $fordb2->fetch(0,"row");
        $yesterday_mobile_data = $fordb2->dt;

        $sql3_web = $sql3." and agent_type = 'W'  ";
        $sql3_mobile = $sql3." and agent_type = 'M' ";

        $fordb3->query($sql3_web);
        $fordb3->fetch(0,"row");
        $oneweek_ago_web_data = $oneweek_ago_web_data;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $oneweek_ago_mobile_data = $oneweek_ago_web_data;

        //$fordb3->query($sql3);
        //echo "total:".$fordb->total;
        //$fordb->fetch(0);
        //$fordb1->fetch(0);
        //$fordb2->fetch(0);
        //$fordb3->fetch(0);

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            //echo $search_sdate;
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){
        $sql .= "Select ";
        $sql .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql .= "from ".TBL_LOGSTORY_VISITTIME." where vdate LIKE '".substr($vdate,0,6)."%' ";


        $sql2 .= "Select ";
        $sql2 .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql2 .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql2 .= "from ".TBL_LOGSTORY_VISITTIME." where vdate LIKE '".substr($vonemonthago,0,6)."%' ";

        $sql3 .= "Select ";
        $sql3 .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql3 .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql3 .= "from ".TBL_LOGSTORY_VISITTIME." where vdate LIKE '".substr($vtwomonthago,0,6)."%' ";


        $sql_web = $sql." and agent_type = 'W'  ";
        $sql_mobile = $sql." and agent_type = 'M' ";

        $fordb->query($sql_web);
        $fordb->fetch(0,"row");
        $selected_date_web_data = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_mobile_data = $fordb->dt;

        $sql2_web = $sql2." and agent_type = 'W'  ";
        $sql2_mobile = $sql2." and agent_type = 'M' ";

        $fordb2->query($sql2_web);
        $fordb2->fetch(0,"row");
        $yesterday_web_data = $fordb2->dt;

        $fordb2->query($sql2_mobile);
        $fordb2->fetch(0,"row");
        $yesterday_mobile_data = $fordb2->dt;
        //$fordb2->query($sql2);
        $sql3_web = $sql3." and agent_type = 'W'  ";
        $sql3_mobile = $sql3." and agent_type = 'M' ";

        $fordb3->query($sql3_web);
        $fordb3->fetch(0,"row");
        $oneweek_ago_web_data = $oneweek_ago_web_data;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $oneweek_ago_mobile_data = $oneweek_ago_web_data;

        //echo "total:".$fordb->total;
        $fordb->fetch(0);
        //$fordb1->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }

//	echo $sql1 ."<br>";
//	echo $sql2 ."<br>";
//	echo $sql3 ."<br>";


    if(isset($_GET["mode"]) && (isset($_GET["mode"]) && $_GET["mode"] == "excel")){

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

        if($SelectReport == '3'){

            $sheet->getActiveSheet(0)->mergeCells('A2:E2');
            $sheet->getActiveSheet(0)->setCellValue('A2', "재방문횟수");
            $sheet->getActiveSheet(0)->mergeCells('A3:E3');
            $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

            $start=3;
            $i = $start;

            $sheet->getActiveSheet(0)->mergeCells('A4:A5');
            $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "요일");

            $sheet->getActiveSheet(0)->mergeCells('B4:C4');
            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "해당 월");
            $sheet->getActiveSheet(0)->mergeCells('D4:D5');
            $sheet->getActiveSheet(0)->setCellValue('D' . ($i+1), "1개월 전");
            $sheet->getActiveSheet(0)->mergeCells('E4:E5');
            $sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "2개월 전");

            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+2), "웹");
            $sheet->getActiveSheet(0)->setCellValue('C' . ($i+2), "모바일");

            for($i=0;$i<$nLoop;$i++){

                $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), getNameOfWeekday($i,$vdate,"dayname"));
                $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), returnZeroValue($selected_date_web_data[$i]));
                $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), returnZeroValue($selected_date_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), returnZeroValue($yesterday_web_data[$i]+$yesterday_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), returnZeroValue($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $visit01_web = $visit01_web + $selected_date_web_data[$i];
                $visit01_mobile = $visit01_mobile + $selected_date_mobile_data[$i];
                $visit03 = $visit03 + $yesterday_web_data[$i]+$yesterday_mobile_data[$i];
                $visit04 = $visit04 + $oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i];
            }

            //$i++;
            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), '합계');

            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), $visit01_web);
            $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), $visit01_mobile);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), $visit03);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $visit04);
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);

            $sheet->setActiveSheetIndex(0);
            //$i = $i + 2;


            $sheet->getActiveSheet()->getRowDimension($start+3)->setRowHeight(30);

            //header('Content-Type: application/vnd.ms-excel');
            //header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","순수방문자.xls").'"');
            //header('Cache-Control: max-age=0');

			$filename = "순수방문자";

            // $objWriter->setUseInlineCSS(true);
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $sheet->getActiveSheet()->getStyle('A'.($start+1).':E'.($i+$start+3))->applyFromArray($styleArray);
            $sheet->getActiveSheet()->getStyle('A'.$start.':E'.($i+$start+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getActiveSheet()->getStyle('A'.$start.':E'.($i+$start+3))->getFont()->setSize(10)->setName('돋움');

        }else if($SelectReport == '2'){

            $sheet->getActiveSheet(0)->mergeCells('A2:E2');
            $sheet->getActiveSheet(0)->setCellValue('A2', "방문횟수");
            $sheet->getActiveSheet(0)->mergeCells('A3:E3');
            $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

            $start=3;
            $i = $start;

            $sheet->getActiveSheet(0)->mergeCells('A4:A5');
            $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "요일");

            $sheet->getActiveSheet(0)->mergeCells('B4:C4');
            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "해당 주");
            $sheet->getActiveSheet(0)->mergeCells('D4:D5');
            $sheet->getActiveSheet(0)->setCellValue('D' . ($i+1), "1주 전");
            $sheet->getActiveSheet(0)->mergeCells('E4:E5');
            $sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "4주 전");

            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+2), "웹");
            $sheet->getActiveSheet(0)->setCellValue('C' . ($i+2), "모바일");

            for($i=0;$i<$nLoop;$i++){

                $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), getNameOfWeekday($i,$vdate));
                $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), returnZeroValue($selected_date_web_data[$i]));
                $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), returnZeroValue($selected_date_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), returnZeroValue($yesterday_web_data[$i]+$yesterday_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


                $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), returnZeroValue($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $visit01_web = $visit01_web + $selected_date_web_data[$i];
                $visit01_mobile = $visit01_mobile + $selected_date_mobile_data[$i];
                $visit03 = $visit03 + $yesterday_web_data[$i]+$yesterday_mobile_data[$i];
                $visit04 = $visit04 + $oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i];

            }

            //$i++;
            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), '합계');

            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), $visit01_web);
            $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), $visit01_mobile);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), $visit03);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $visit04);
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


            $sheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);

            $sheet->setActiveSheetIndex(0);
            //$i = $i + 2;


            $sheet->getActiveSheet()->getRowDimension($start+2)->setRowHeight(30);

            //header('Content-Type: application/vnd.ms-excel');
            //header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","순수방문자.xls").'"');
            //header('Cache-Control: max-age=0');

			$filename = "순수방문자";

            // $objWriter->setUseInlineCSS(true);
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $sheet->getActiveSheet()->getStyle('A'.($start+1).':E'.($i+$start+3))->applyFromArray($styleArray);
            $sheet->getActiveSheet()->getStyle('A'.$start.':E'.($i+$start+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getActiveSheet()->getStyle('A'.$start.':E'.($i+$start+3))->getFont()->setSize(10)->setName('돋움');

        }else if($SelectReport == '1'){

            $sheet->getActiveSheet(0)->mergeCells('A2:Q2');
            $sheet->getActiveSheet(0)->setCellValue('A2', "방문횟수");
            $sheet->getActiveSheet(0)->mergeCells('A3:Q3');
            $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

            $start=3;
            $i = $start;

            $sheet->getActiveSheet(0)->mergeCells('A' . ($i+1). ':A' . ($i+3));
            $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "시간");

            $sheet->getActiveSheet(0)->mergeCells('B' . ($i+1). ':E' . ($i+1));
            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "해당일(방문횟수)");

            $sheet->getActiveSheet(0)->mergeCells('B' . ($i+2). ':C' . ($i+2));
            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+2), "웹");

            $sheet->getActiveSheet(0)->mergeCells('D' . ($i+2). ':E' . ($i+2));
            $sheet->getActiveSheet(0)->setCellValue('D' . ($i+2), "모바일");

            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+3), "방문횟수");
            $sheet->getActiveSheet(0)->setCellValue('C' . ($i+3), "점유율(%)");
            $sheet->getActiveSheet(0)->setCellValue('D' . ($i+3), "방문횟수");
            $sheet->getActiveSheet(0)->setCellValue('E' . ($i+3), "점유율(%)");

            $sheet->getActiveSheet(0)->mergeCells('F' . ($i+1). ':I' . ($i+1));
            $sheet->getActiveSheet(0)->setCellValue('F' . ($i+1), "전달평균");

            $sheet->getActiveSheet(0)->mergeCells('F' . ($i+2). ':G' . ($i+2));
            $sheet->getActiveSheet(0)->setCellValue('F' . ($i+2), "웹");

            $sheet->getActiveSheet(0)->mergeCells('H' . ($i+2). ':I' . ($i+2));
            $sheet->getActiveSheet(0)->setCellValue('H' . ($i+2), "모바일");

            $sheet->getActiveSheet(0)->setCellValue('F' . ($i+3), "평균방문횟수");
            $sheet->getActiveSheet(0)->setCellValue('G' . ($i+3), "전달대비 상승률");
            $sheet->getActiveSheet(0)->setCellValue('H' . ($i+3), "평균방문횟수");
            $sheet->getActiveSheet(0)->setCellValue('I' . ($i+3), "전달대비 상승률");

            $sheet->getActiveSheet(0)->mergeCells('J' . ($i+1). ':M' . ($i+1));
            $sheet->getActiveSheet(0)->setCellValue('J' . ($i+1), "전일");

            $sheet->getActiveSheet(0)->mergeCells('J' . ($i+2). ':K' . ($i+2));
            $sheet->getActiveSheet(0)->setCellValue('J' . ($i+2), "웹");

            $sheet->getActiveSheet(0)->mergeCells('L' . ($i+2). ':M' . ($i+2));
            $sheet->getActiveSheet(0)->setCellValue('L' . ($i+2), "모바일");

            $sheet->getActiveSheet(0)->setCellValue('J' . ($i+3), "평균방문횟수");
            $sheet->getActiveSheet(0)->setCellValue('K' . ($i+3), "전일대비 상승률");
            $sheet->getActiveSheet(0)->setCellValue('L' . ($i+3), "평균방문횟수");
            $sheet->getActiveSheet(0)->setCellValue('M' . ($i+3), "전일대비 상승률");

            $sheet->getActiveSheet(0)->mergeCells('N' . ($i+1). ':Q' . ($i+1));
            $sheet->getActiveSheet(0)->setCellValue('N' . ($i+1), "1주전");

            $sheet->getActiveSheet(0)->mergeCells('N' . ($i+2). ':O' . ($i+2));
            $sheet->getActiveSheet(0)->setCellValue('N' . ($i+2), "웹");

            $sheet->getActiveSheet(0)->mergeCells('P' . ($i+2). ':Q' . ($i+2));
            $sheet->getActiveSheet(0)->setCellValue('P' . ($i+2), "모바일");

            $sheet->getActiveSheet(0)->setCellValue('N' . ($i+3), "평균방문횟수");
            $sheet->getActiveSheet(0)->setCellValue('O' . ($i+3), "1주전대비 상승률");
            $sheet->getActiveSheet(0)->setCellValue('P' . ($i+3), "평균방문횟수");
            $sheet->getActiveSheet(0)->setCellValue('Q' . ($i+3), "1주전대비 상승률");


            // 엑셀 포맷 정의시 자동으로 *100 이 처리됨

            for($i=0;$i<$nLoop;$i++){

                $visit01_sum_web = $visit01_sum_web + $selected_date_web_data[$i];
                $visit01_sum_mobile = $visit01_sum_mobile + $selected_date_mobile_data[$i];
                $visit02_sum_web = $visit02_sum_web + $one_monthago_web_data[$i];
                $visit02_sum_mobile = $visit02_sum_mobile + $one_monthago_mobile_data[$i];
                $visit03_sum_web = $visit03_sum_web + $yesterday_web_data[$i];
                $visit03_sum_mobile = $visit03_sum_mobile + $yesterday_mobile_data[$i];


                $visit04_sum_web = $visit04_sum_web + $oneweek_ago_web_data[$i];
                $visit04_mobile_web = $visit04_mobile_web + $oneweek_ago_mobile_data[$i];
            }


            for($i=0;$i<$nLoop;$i++){

                if($one_monthago_web_data[$i] > 0 && $selected_date_web_data[$i]){
                    $rateofrisebyonemonthago_web = (($selected_date_web_data[$i]/$one_monthago_web_data[$i])-1);
                }else{
                    $rateofrisebyonemonthago_web = 0;
                }

                if($one_monthago_mobile_data[$i] > 0 && $selected_date_mobile_data[$i]){
                    $rateofrisebyonemonthago_mobile = (($selected_date_web_data[$i]/$one_monthago_mobile_data[$i])-1);
                }else{
                    $rateofrisebyonemonthago_mobile = 0;
                }

                if($yesterday_web_data[$i] > 0  && $selected_date_web_data[$i]){
                    $rateofrisebyyesterday_web = (($selected_date_web_data[$i]/$yesterday_web_data[$i])-1);
                }else{
                    $rateofrisebyyesterday_web = 0;
                }

                if($yesterday_mobile_data[$i] > 0  && $selected_date_web_data[$i]){
                    $rateofrisebyyesterday_mobile = (($selected_date_web_data[$i]/$yesterday_mobile_data[$i])-1);
                }else{
                    $rateofrisebyyesterday_mobile = 0;
                }

                if($oneweek_ago_web_data[$i] > 0  && $selected_date_web_data[$i]){
                    $rateofrisebyoneweeksago_web = (($selected_date_web_data[$i]/$oneweek_ago_web_data[$i])-1);
                }else{
                    $rateofrisebyoneweeksago_web = 0;
                }

                if($oneweek_ago_mobile_data[$i] > 0  && $selected_date_mobile_data[$i]){
                    $rateofrisebyoneweeksago_mobile = (($selected_date_mobile_data[$i]/$oneweek_ago_mobile_data[$i])-1);
                }else{
                    $rateofrisebyoneweeksago_mobile = 0;
                }

                $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 4), $i.' : 00');
                $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 4), returnZeroValue($selected_date_web_data[$i]));
                $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                if($visit01_sum_web > 0){
                    $selected_date_web_data_rate = $selected_date_web_data[$i]/$visit01_sum_web;
                }else{
                    $selected_date_web_data_rate = "0";
                }

                $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 4), $selected_date_web_data_rate);
                $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 4), returnZeroValue($selected_date_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                if($visit01_sum_mobile > 0){
                    $selected_date_mobile_data_rate = $selected_date_mobile_data[$i]/$visit01_sum_mobile;
                }else{
                    $selected_date_mobile_data_rate = "0";
                }

                $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 4), returnZeroValue($selected_date_mobile_data_rate));
                $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 4), returnZeroValue($one_monthago_web_data[$i]));
                $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 4), $rateofrisebyonemonthago_web);
                $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 4), returnZeroValue($one_monthago_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 4), $rateofrisebyonemonthago_mobile);
                $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 4), returnZeroValue($yesterday_web_data[$i]));
                $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 4), $rateofrisebyyesterday_web);
                $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 4), returnZeroValue($yesterday_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('L' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 4), $rateofrisebyyesterday_mobile);
                $sheet->getActiveSheet()->getStyle('M' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 4), returnZeroValue($oneweek_ago_web_data[$i]));
                $sheet->getActiveSheet()->getStyle('N' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 4), $rateofrisebyoneweeksago_web);
                $sheet->getActiveSheet()->getStyle('O' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                $sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 4), returnZeroValue($oneweek_ago_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('P' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('Q' . ($i + $start + 4), $rateofrisebyoneweeksago_mobile);
                $sheet->getActiveSheet()->getStyle('Q' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

                $visit01_web = $visit01_web + $selected_date_web_data[$i];
                $visit01_mobile = $visit01_mobile + $selected_date_mobile_data[$i];
                $visit02_web = $visit02_web + $one_monthago_web_data[$i];
                $visit02_mobile = $visit02_mobile + $one_monthago_mobile_data[$i];
                $visit03_web = $visit03_web + $yesterday_web_data[$i];
                $visit03_mobile = $visit03_mobile + $yesterday_mobile_data[$i];
                $visit04_web = $visit04_web + $oneweek_ago_web_data[$i];
                $visit04_mobile = $visit04_mobile + $oneweek_ago_mobile_data[$i];

            }


            if($visit02_web > 0 && $visit01_web > 0){
                $rateofrisebyonemonthago_web_sum = (($visit01_web/$visit02_web)-1);
            }else{
                $rateofrisebyonemonthago_web_sum = 0;
            }

            if($visit02_mobile > 0 && $visit01_mobile > 0){
                $rateofrisebyonemonthago_mobile_sum = (($visit01_mobile/$visit02_mobile)-1);
            }else{
                $rateofrisebyonemonthago_mobile_sum = 0;
            }

            if($visit03_web > 0 && $visit01_web > 0){
                $rateofrisebyyesterday_web_sum = (($visit01_web/$visit03_web)-1);
            }else{
                $rateofrisebyyesterday_web_sum = 0;
            }

            if($visit03_mobile > 0 && $visit01_mobile > 0){
                $rateofrisebyyesterday_mobile_sum = (($visit01_mobile/$visit03_mobile)-1);
            }else{
                $rateofrisebyyesterday_mobile_sum = 0;
            }

            if($visit04_web > 0 && $visit01_web > 0){
                $rateofrisebyoneweeksago_web_sum = (($visit01_web/$visit04_web)-1);
            }else{
                $rateofrisebyoneweeksago_web_sum = 0;
            }

            if($visit04_mobile > 0 && $visit01_mobile > 0){
                $rateofrisebyoneweeksago_mobile_sum = (($visit01_mobile/$visit04_mobile)-1);
            }else{
                $rateofrisebyoneweeksago_mobile_sum = 0;
            }

            //$i++;
            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 4), '합계');

            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 4), $visit01_web);
            $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 4), '-');
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 4), $visit01_mobile);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 4), '-');
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 4), $visit02_web);
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 4), $rateofrisebyonemonthago_web_sum);
            $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 4), $visit02_mobile);
            $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 4), $rateofrisebyonemonthago_mobile_sum);
            $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 4), $visit03_web);
            $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 4), $rateofrisebyyesterday_web_sum);
            $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 4), $visit03_mobile);
            $sheet->getActiveSheet()->getStyle('L' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 4), $rateofrisebyyesterday_mobile_sum);
            $sheet->getActiveSheet()->getStyle('M' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 4), $visit04_web);
            $sheet->getActiveSheet()->getStyle('N' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 4), $rateofrisebyoneweeksago_web_sum);
            $sheet->getActiveSheet()->getStyle('O' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 4), $visit04_mobile);
            $sheet->getActiveSheet()->getStyle('P' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('Q' . ($i + $start + 4), $rateofrisebyoneweeksago_mobile_sum);
            $sheet->getActiveSheet()->getStyle('Q' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('O')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('P')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('Q')->setWidth(15);


            $sheet->setActiveSheetIndex(0);
            //$i = $i + 2;


            $sheet->getActiveSheet()->getRowDimension($start+4)->setRowHeight(30);

            //header('Content-Type: application/vnd.ms-excel');
            //header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","재방문횟수.xls").'"');
            //header('Cache-Control: max-age=0');

			$filename = "재방문횟수";

            // $objWriter->setUseInlineCSS(true);
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $sheet->getActiveSheet()->getStyle('A'.($start+1).':Q'.($i+$start+4))->applyFromArray($styleArray);
            $sheet->getActiveSheet()->getStyle('A'.$start.':Q'.($i+$start+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getActiveSheet()->getStyle('A'.$start.':Q'.($i+$start+4))->getFont()->setSize(10)->setName('돋움');

        }

        $sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움');
        $sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        unset($styleArray);
        //$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        //$objWriter->save('php://output');

		$sheet->getActiveSheet()->setTitle('방문횟수');
		$sheet->setActiveSheetIndex(0);

		//	wel_ 엑셀 다운로드 히스토리 저장
		$ig_db = new Database;
		$ig_excel_dn_history_SQL = "
			INSERT INTO
				ig_excel_dn_history
			SET
				code = '".$_SESSION['admininfo']['charger_ix']."',
				ip = '". $_SERVER['REMOTE_ADDR']."',
				dn_type = 'order_salesbydate_paymenttype',
				dn_reason = '".addslashes($_GET['irs'])."',
				dn_text = '".addslashes($_SERVER['HTTP_REFERER'].$QUERY_STRING)."',
				regDt = '".date("Y-m-d H:i:s")."'
		";
		$ig_db->query($ig_excel_dn_history_SQL);
		//	//wel_ 엑셀 다운로드 히스토리 저장

		$download_filename = $filename.'_'.date("YmdHis").'.zip'; 
		$igExcel_file = '../../excelDn/'.$filename.'_'.date("YmdHis").'.xls';
	
		$ig_dnFile_full = '../../excelDn/'.$download_filename;

		$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
		$objWriter->save($igExcel_file);

		if(trim($_GET['ipw']) == "") {
			$ig_pw = "barrel";
		} else {
			$ig_pw = $_GET['ipw'];
		}

		shell_exec('zip -P '.$ig_pw.' -r ../../excelDn/'.$download_filename.' '.$igExcel_file);

		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $download_filename . '"'); // 저장될 파일 이름
		header('Content-Transfer-Encoding: binary');
		header('Content-length: ' . filesize($ig_dnFile_full));
		header('Expires: 0');
		header("Pragma: public");

		ob_clean();
		flush();
		readfile($ig_dnFile_full);

		unlink($igExcel_file);
		unlink($ig_dnFile_full);

        exit;
    }


    //$exce_down_str .= "<a href='?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";

	$exce_down_str = "<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0 style='cursor:pointer' 
	onclick=\"ig_excel_dn_chk('?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."');\">";


    if($SelectReport == 1){
        if(isset($_GET["mode"]) && ($_GET["mode"] == "pop" || $_GET["mode"] == "print")){
            $mstring .= TitleBar("방문횟수 ",$dateString, false, $exce_down_str);
        }else{
            $mstring .= TitleBar("방문횟수 ",$dateString, true, $exce_down_str);
        }
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='visit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center>
						<td class=s_td width=10%>항목</td>
						<td class=m_td width=20%>시간대</td>
						<td class=m_td width=20%>방문횟수</td>
						<td class=e_td width=20%>점유율</td>
					</tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff id='Report10000'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
		<td  onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\" align=center>{{MAXTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right style='padding-right:10px;' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\" >{{MAXVALUE}}</td>
		<td bgcolor=#ffffff  align=right style='padding-right:10px;' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\" >{{MAXVALUE_RATE}} %</td>
		</tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff id='Report10001' >
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
		<td onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\" align=center>{{MINTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right style='padding-right:10px;' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINVALUE}}</td>
		<td bgcolor=#ffffff  align=right style='padding-right:10px;' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINVALUE_RATE}}%</td>
		</tr>\n";
        $mstring .= "</table><br>\n";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n
						<col width='4%'>
						<col width='5%'>
						<col width='5%'>
						<col width='5%'>
						<col width='5%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<!--col width='6%'--> 

						";
        $mstring .= "<tr height=30 align=center>
							<td class=s_td rowspan=3>시간</td>
							<td class=m_td colspan=4>해당일(방문횟수)</td>
							<td class=m_td colspan=4>전달평균</td>
							<td class=m_td colspan=4>전일</td>
							<td class=m_td colspan=4>1주전 </td>
							<!--td class=e_td style='line-height:130%;' rowspan=3>1회방문당<br>마케팅비용</td-->
						</tr>
						<tr height=30 align=center> 
							<td class=m_td colspan=2>웹</td>
							<td class=m_td colspan=2>모바일</td>
							<td class=m_td colspan=2>웹</td>
							<td class=m_td colspan=2>모바일</td>
							<td class=m_td colspan=2>웹</td>
							<td class=m_td colspan=2>모바일</td>
							<td class=m_td colspan=2>웹</td>
							<td class=m_td colspan=2>모바일</td>
						</tr>
						<tr height=30 align=center> 
							<td class=m_td >방문횟수</td>
							<td class=m_td >점유율(%)</td>
							<td class=m_td >방문횟수</td>
							<td class=m_td >점유율(%)</td>
							<td class=m_td >평균방문횟수</td>
							<td class=m_td style='line-height:130%;'>전달대비<br>상승률</td>
							<td class=m_td >평균방문횟수</td>
							<td class=m_td style='line-height:130%;'>전달대비<br>상승률</td>
							<td class=m_td >평균방문횟수</td>
							<td class=m_td style='line-height:130%;'>전일대비<br>상승률</td>
							<td class=m_td >평균방문횟수</td>
							<td class=m_td style='line-height:130%;'>전일대비<br>상승률</td>
							<td class=m_td >평균방문횟수</td>
							<td class=m_td style='line-height:130%;'>1주전대비<br>상승률</td>
							<td class=m_td >평균방문횟수</td>
							<td class=m_td style='line-height:130%;'>1주전대비<br>상승률</td>
							
						</tr>\n";


        $labels = array("해당일(방문횟수)","전달평균","전일","1주전");
        $ykeys = array("a","b","c","d");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => "$i : 00",
                'a' => ($selected_date_web_data[$i]+$selected_date_mobile_data[$i]) ,
                'b' => ($one_monthago_web_data[$i]+$one_monthago_mobile_data[$i]),
                'c' => ($yesterday_web_data[$i]+$yesterday_mobile_data[$i]),
                'd' => ($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i])
            );

            $visit01_sum_web = $visit01_sum_web + $selected_date_web_data[$i];
            $visit01_sum_mobile = $visit01_sum_mobile + $selected_date_mobile_data[$i];
            $visit02_sum_web = $visit02_sum_web + $one_monthago_web_data[$i];
            $visit02_sum_mobile = $visit02_sum_mobile + $one_monthago_mobile_data[$i];
            $visit03_sum_web = $visit03_sum_web + $yesterday_web_data[$i];
            $visit03_sum_mobile = $visit03_sum_mobile + $yesterday_mobile_data[$i];

            $visit04_sum_web = $visit04_sum_web + $oneweek_ago_web_data[$i];
            $visit04_mobile_web = $visit04_mobile_web + $oneweek_ago_mobile_data[$i];
        }
        for($i=0;$i<$nLoop;$i++){

            if($one_monthago_web_data[$i] > 0 && $selected_date_web_data[$i]){
                $rateofrisebyonemonthago_web = (($selected_date_web_data[$i]/$one_monthago_web_data[$i])-1)*100;
            }else{
                $rateofrisebyonemonthago_web = 0;
            }

            if($rateofrisebyonemonthago_web > 0){
                $rateofrisebyonemonthago_web_color =  "color:red;";
                $rateofrisebyonemonthago_web_icon =  "↑";
            }else if($rateofrisebyonemonthago_web == 0){
                $rateofrisebyonemonthago_web_color =  "color:#000000;";
                $rateofrisebyonemonthago_web_icon =  "";
            }else{
                $rateofrisebyonemonthago_web_color = "color:blue;";
                $rateofrisebyonemonthago_web_icon =  "↓";
            }

            if($one_monthago_mobile_data[$i] > 0 && $selected_date_mobile_data[$i]){
                $rateofrisebyonemonthago_mobile = (($selected_date_web_data[$i]/$one_monthago_mobile_data[$i])-1)*100;
            }else{
                $rateofrisebyonemonthago_mobile = 0;
            }

            if($rateofrisebyonemonthago_mobile > 0){
                $rateofrisebyonemonthago_mobile_color =  "color:red;";
                $rateofrisebyonemonthago_mobile_icon =  "↑";
            }else if($rateofrisebyonemonthago_mobile == 0){
                $rateofrisebyonemonthago_mobile_color =  "color:#000000;";
                $rateofrisebyonemonthago_mobile_icon =  "";
            }else{
                $rateofrisebyonemonthago_mobile_color = "color:blue;";
                $rateofrisebyonemonthago_mobile_icon =  "↓";
            }

            if($yesterday_web_data[$i] > 0  && $selected_date_web_data[$i]){
                $rateofrisebyyesterday_web = (($selected_date_web_data[$i]/$yesterday_web_data[$i])-1)*100;
            }else{
                $rateofrisebyyesterday_web = 0;
            }

            if($rateofrisebyyesterday_web > 0){
                $rateofrisebyyesterday_web_color =  "color:red;";
                $rateofrisebyyesterday_web_icon =  "↑";
            }else if($rateofrisebyyesterday_web == 0){
                $rateofrisebyyesterday_web_color =  "color:#000000;";
                $rateofrisebyyesterday_web_icon =  "";
            }else{
                $rateofrisebyyesterday_web_color = "color:blue;";
                $rateofrisebyyesterday_web_icon =  "↓";
            }

            if($yesterday_mobile_data[$i] > 0  && $selected_date_web_data[$i]){
                $rateofrisebyyesterday_mobile = (($selected_date_web_data[$i]/$yesterday_mobile_data[$i])-1)*100;
            }else{
                $rateofrisebyyesterday_mobile = 0;
            }

            if($rateofrisebyyesterday_mobile > 0){
                $rateofrisebyyesterday_mobile_color =  "color:red;";
                $rateofrisebyyesterday_mobile_icon =  "↑";
            }else if($rateofrisebyyesterday_mobile == 0){
                $rateofrisebyyesterday_mobile_color =  "color:#000000;";
                $rateofrisebyyesterday_mobile_icon =  "";
            }else{
                $rateofrisebyyesterday_mobile_color = "color:blue;";
                $rateofrisebyyesterday_mobile_icon =  "↓";
            }

            if($oneweek_ago_web_data[$i] > 0  && $selected_date_web_data[$i]){
                $rateofrisebyoneweeksago_web = (($selected_date_web_data[$i]/$oneweek_ago_web_data[$i])-1)*100;
            }else{
                $rateofrisebyoneweeksago_web = 0;
            }

            if($rateofrisebyoneweeksago_web > 0){
                $rateofrisebyoneweeksago_web_icon =  "↑";
                $rateofrisebyoneweeksago_web_color =  "color:red;";
            }else if($rateofrisebyoneweeksago_web == 0){
                $rateofrisebyoneweeksago_web_color =  "color:#000000;";
                $rateofrisebyoneweeksago_web_icon =  "";
            }else{
                $rateofrisebyoneweeksago_web_color = "color:blue;";
                $rateofrisebyoneweeksago_web_icon =  "↓";
            }


            if($oneweek_ago_mobile_data[$i] > 0  && $selected_date_mobile_data[$i]){
                $rateofrisebyoneweeksago_mobile = (($selected_date_mobile_data[$i]/$oneweek_ago_mobile_data[$i])-1)*100;
            }else{
                $rateofrisebyoneweeksago_mobile = 0;
            }

            if($rateofrisebyoneweeksago_mobile > 0){
                $rateofrisebyoneweeksago_mobile_icon =  "↑";
                $rateofrisebyoneweeksago_mobile_color =  "color:red;";
            }else if($rateofrisebyoneweeksago_mobile == 0){
                $rateofrisebyoneweeksago_mobile_color =  "color:#000000;";
                $rateofrisebyoneweeksago_mobile_icon =  "";
            }else{
                $rateofrisebyoneweeksago_mobile_color = "color:blue;";
                $rateofrisebyoneweeksago_mobile_icon =  "↓";
            }

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">$i : 00</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($selected_date_web_data[$i]))."</td>
		<td bgcolor='#ffffff' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>";
            if($visit01_sum_web > 0){
                $mstring .= number_format(returnZeroValue($selected_date_web_data[$i]/$visit01_sum_web*100),1);
            }else{
                $mstring .= "0";
            }

            $mstring .= " %  </td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($selected_date_mobile_data[$i]))."</td>
		<td bgcolor='#ffffff' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>";
            if($visit01_sum_mobile > 0){
                $mstring .= number_format(returnZeroValue($selected_date_mobile_data[$i]/$visit01_sum_mobile*100),1);
            }else{
                $mstring .= "0";
            }

            $mstring .= " %  </td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($one_monthago_web_data[$i]))."</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;".$rateofrisebyonemonthago_web_color."'>".number_format($rateofrisebyonemonthago_web,1)."% ".$rateofrisebyonemonthago_web_icon."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($one_monthago_mobile_data[$i]))."</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;".$rateofrisebyonemonthago_mobile_color."'>".number_format($rateofrisebyonemonthago_mobile,1)."% ".$rateofrisebyonemonthago_mobile_icon."</td>

		<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($yesterday_web_data[$i]))."</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;".($rateofrisebyyesterday_web_color)."'>".number_format($rateofrisebyyesterday_web,1)."% ".$rateofrisebyyesterday_web_icon."</td>
		<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($yesterday_mobile_data[$i]))."</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;".($rateofrisebyyesterday_mobile_color)."'>".number_format($rateofrisebyyesterday_mobile,1)."% ".$rateofrisebyyesterday_mobile_icon."</td>

		<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($oneweek_ago_web_data[$i]))."</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;".($rateofrisebyoneweeksago_web_color)."'>".number_format($rateofrisebyoneweeksago_web,1)."%  ".$rateofrisebyoneweeksago_web_icon."</td>
		<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($oneweek_ago_mobile_data[$i]))."</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;".($rateofrisebyoneweeksago_mobile_color)."'>".number_format($rateofrisebyoneweeksago_mobile,1)."%  ".$rateofrisebyoneweeksago_mobile_icon."</td>
		<!--td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>0</td-->
		</tr>\n";

            $visit01_web = $visit01_web + $selected_date_web_data[$i];
            $visit01_mobile = $visit01_mobile + $selected_date_mobile_data[$i];
            $visit02_web = $visit02_web + $one_monthago_web_data[$i];
            $visit02_mobile = $visit02_mobile + $one_monthago_mobile_data[$i];
            $visit03_web = $visit03_web + $yesterday_web_data[$i];
            $visit03_mobile = $visit03_mobile + $yesterday_mobile_data[$i];
            $visit04_web = $visit04_web + $oneweek_ago_web_data[$i];
            $visit04_mobile = $visit04_mobile + $oneweek_ago_mobile_data[$i];

            if($minvalue > ($selected_date_web_data[$i] + $selected_date_mobile_data[$i]) || $i == 0 ){
                $minvalue = $selected_date_web_data[$i] + $selected_date_mobile_data[$i];
                if($visit01_sum_web > 0){
                    $minvalue_rate = ($selected_date_web_data[$i] + $selected_date_mobile_data[$i])/$visit01_sum_web*100;
                }else{
                    $minvalue_rate = 0;
                }
                $mintime = $i;
            }
            if($maxvalue < ($selected_date_web_data[$i] + $selected_date_mobile_data[$i]) || $i == 0 ){
                $maxvalue = ($selected_date_web_data[$i] + $selected_date_mobile_data[$i]);
                if($visit01_sum_web > 0){
                    $maxvalue_rate = ($selected_date_web_data[$i] + $selected_date_mobile_data[$i])/$visit01_sum_web*100;
                }else{
                    $minvalue_rate = 0;
                }
                $maxtime = $i;
            }

        }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
        if($visit02_web > 0 && $visit01_web > 0){
            $rateofrisebyonemonthago_web_sum = (($visit01_web/$visit02_web)-1)*100;
        }else{
            $rateofrisebyonemonthago_web_sum = 0;
        }

        if($rateofrisebyonemonthago_web_sum > 0){
            $rateofrisebyonemonthago_web_sum_color =  "color:red;";
            $rateofrisebyonemonthago_web_sum_icon =  "↑";
        }else if($rateofrisebyonemonthago_web_sum == 0){
            $rateofrisebyonemonthago_web_sum_color =  "color:#000000;";
            $rateofrisebyonemonthago_web_sum_icon =  "";
        }else{
            $rateofrisebyonemonthago_web_sum_color = "color:blue;";
            $rateofrisebyonemonthago_web_sum_icon =  "↓";
        }


        if($visit02_mobile > 0 && $visit01_mobile > 0){
            $rateofrisebyonemonthago_mobile_sum = (($visit01_mobile/$visit02_mobile)-1)*100;
        }else{
            $rateofrisebyonemonthago_mobile_sum = 0;
        }

        if($rateofrisebyonemonthago_mobile_sum > 0){
            $rateofrisebyonemonthago_mobile_sum_color =  "color:red;";
            $rateofrisebyonemonthago_mobile_sum_icon =  "↑";
        }else if($rateofrisebyonemonthago_mobile_sum == 0){
            $rateofrisebyonemonthago_mobile_sum_color =  "color:#000000;";
            $rateofrisebyonemonthago_mobile_sum_icon =  "";
        }else{
            $rateofrisebyonemonthago_mobile_sum_color = "color:blue;";
            $rateofrisebyonemonthago_mobile_sum_icon =  "↓";
        }




        if($visit03_web > 0 && $visit01_web > 0){
            $rateofrisebyyesterday_web_sum = (($visit01_web/$visit03_web)-1)*100;
        }else{
            $rateofrisebyyesterday_web_sum = 0;
        }

        if($rateofrisebyyesterday_web_sum > 0){
            $rateofrisebyyesterday_web_sum_color =  "color:red;";
            $rateofrisebyyesterday_web_sum_icon =  "↑";
        }else if($rateofrisebyyesterday_web_sum == 0){
            $rateofrisebyyesterday_web_sum_color =  "color:#000000;";
            $rateofrisebyyesterday_web_sum_icon =  "";
        }else{
            $rateofrisebyyesterday_web_sum_color = "color:blue;";
            $rateofrisebyyesterday_web_sum_icon =  "↓";
        }

        if($visit03_mobile > 0 && $visit01_mobile > 0){
            $rateofrisebyyesterday_mobile_sum = (($visit01_mobile/$visit03_mobile)-1)*100;
        }else{
            $rateofrisebyyesterday_mobile_sum = 0;
        }

        if($rateofrisebyyesterday_mobile_sum > 0){
            $rateofrisebyyesterday_mobile_sum_color =  "color:red;";
            $rateofrisebyyesterday_mobile_sum_icon =  "↑";
        }else if($rateofrisebyyesterday_mobile_sum == 0){
            $rateofrisebyyesterday_mobile_sum_color =  "color:#000000;";
            $rateofrisebyyesterday_mobile_sum_icon =  "";
        }else{
            $rateofrisebyyesterday_mobile_sum_color = "color:blue;";
            $rateofrisebyyesterday_mobile_sum_icon =  "↓";
        }






        if($visit04_web > 0 && $visit01_web > 0){
            $rateofrisebyoneweeksago_web_sum = (($visit01_web/$visit04_web)-1)*100;
        }else{
            $rateofrisebyoneweeksago_web_sum = 0;
        }

        if($rateofrisebyoneweeksago_web_sum > 0){
            $rateofrisebyoneweeksago_web_sum_color =  "color:red;";
            $rateofrisebyoneweeksago_web_sum_icon =  "↑";
        }else if($rateofrisebyoneweeksago_web_sum == 0){
            $rateofrisebyoneweeksago_web_sum_color =  "color:#000000;";
            $rateofrisebyoneweeksago_web_sum_icon =  "";
        }else{
            $rateofrisebyoneweeksago_web_sum_color = "color:blue;";
            $rateofrisebyoneweeksago_web_sum_icon =  "↓";
        }

        if($visit04_mobile > 0 && $visit01_mobile > 0){
            $rateofrisebyoneweeksago_mobile_sum = (($visit01_mobile/$visit04_mobile)-1)*100;
        }else{
            $rateofrisebyoneweeksago_mobile_sum = 0;
        }

        if($rateofrisebyoneweeksago_mobile_sum > 0){
            $rateofrisebyoneweeksago_mobile_sum_color =  "color:red;";
            $rateofrisebyoneweeksago_mobile_sum_icon =  "↑";
        }else if($rateofrisebyoneweeksago_mobile_sum == 0){
            $rateofrisebyoneweeksago_mobile_sum_color =  "color:#000000;";
            $rateofrisebyoneweeksago_mobile_sum_icon =  "";
        }else{
            $rateofrisebyoneweeksago_mobile_sum_color = "color:blue;";
            $rateofrisebyoneweeksago_mobile_sum_icon =  "↓";
        }

        $mstring .= "<tr height=30 align=right>
								<td class=s_td >합계</td>
								<td class=m_td >".number_format(returnZeroValue($visit01_web))."</td>
								<td class=m_td >- </td>
								<td class=m_td >".number_format(returnZeroValue($visit01_mobile))."</td>
								<td class=m_td >- </td>
								<td class=m_td >".number_format(returnZeroValue($visit02_web))."</td>
								<td class=m_td style='".$rateofrisebyyesterday_web_sum_color."'>".number_format(returnZeroValue($rateofrisebyonemonthago_web_sum),1)."% ".$rateofrisebyonemonthago_web_sum_icon."</td>
								<td class=m_td >".number_format(returnZeroValue($visit02_mobile))."</td>
								<td class=m_td style='".$rateofrisebyyesterday_mobile_sum_color."'>".number_format(returnZeroValue($rateofrisebyonemonthago_mobile_sum),1)."% ".$rateofrisebyonemonthago_mobile_sum_icon."</td>

								<td class=m_td >".number_format(returnZeroValue($visit03_web))."</td>
								<td class=m_td style='".$rateofrisebyyesterday_web_sum_color."'>".number_format(returnZeroValue($rateofrisebyyesterday_web_sum),1)."% ".$rateofrisebyyesterday_web_sum_icon."</td>
								<td class=m_td >".number_format(returnZeroValue($visit03_mobile))."</td>
								<td class=m_td style='".$rateofrisebyyesterday_mobile_sum_color."'>".number_format(returnZeroValue($rateofrisebyyesterday_mobile_sum),1)."% ".$rateofrisebyyesterday_mobile_sum_icon."</td>

								<td class=m_td >".number_format(returnZeroValue($visit04_web))."</td>
								<td class=m_td style='".$rateofrisebyoneweeksago_web_sum_color."'>".number_format(returnZeroValue($rateofrisebyoneweeksago_web_sum),1)."% ".$rateofrisebyoneweeksago_web_sum_icon."</td>
								<td class=m_td >".number_format(returnZeroValue($visit04_mobile))."</td>
								<td class=m_td style='".$rateofrisebyoneweeksago_mobile_sum_color."'>".number_format(returnZeroValue($rateofrisebyoneweeksago_mobile_sum),1)."% ".$rateofrisebyoneweeksago_mobile_sum_icon."</td>
								<!--td class=e_td >".number_format(0)."</td-->
								</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",number_format($maxvalue),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format($minvalue),$mstring);

        $mstring = str_replace("{{MAXVALUE_RATE}}",number_format($maxvalue_rate,1),$mstring);
        $mstring = str_replace("{{MINVALUE_RATE}}",number_format($minvalue_rate,1),$mstring);

        $mstring = str_replace("{{MAXTIMESTRING}}",getTimeString($maxtime),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getTimeString($mintime),$mstring);


    }else if($SelectReport == 2){
        //$mstring .= TitleBar("방문횟수 - visit","주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate));
        if(isset($_GET["mode"]) && ($_GET["mode"] == "pop" || $_GET["mode"] == "print")){
            $mstring .= TitleBar("방문횟수 ",$dateString, false, $exce_down_str);
        }else{
            $mstring .= TitleBar("방문횟수 ",$dateString, true, $exce_down_str);
        }

        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='visit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>
				<col width=33%>
				<col width=*>
				<col width=33%>";
        $mstring .= "<tr height=30 align=center><td  class=s_td >항목</td><td class=m_td >시간대</td><td class=e_td >방문횟수</td></tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff>
		<td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
		<td align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">{{MAXTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right id='Report10000' style='padding-right:10px;' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">{{MAXVALUE}}</td>
		</tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff>
		<td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
		<td align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right id='Report10001' style='padding-right:10px;' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINVALUE}}</td>
		</tr>\n";
        $mstring .= "</table><br>\n";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>
				<col width=20%>
				<col width=20%>
				<col width=20%>
				<col width=20%>
				<col width=20%>";
        $mstring .= "<tr height=30 align=center>
						<td class=s_td rowspan=2>요일</td><td class=m_td colspan=2>해당주</td><!--td class=m_td >한달평균</td--><td class=m_td rowspan=2>1주전 </td><td class=e_td rowspan=2>4주전 </td>
					   </tr>
					   <tr height=30>
						<td class=m_td >웹</td>
						<td class=m_td >모바일</td>
					   </tr>\n";

        $labels = array("해당주","1주전","4주전");
        $ykeys = array("a","b","c");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => getNameOfWeekday($i,$vdate),
                'a' => ($selected_date_web_data[$i]+$selected_date_mobile_data[$i]) ,
                'b' => ($yesterday_web_data[$i]+$yesterday_mobile_data[$i]),
                'c' => ($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i])
            );
            $mstring .= "<tr height=30 bgcolor=#ffffff >
		<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate)."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($selected_date_web_data[$i]))."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($selected_date_mobile_data[$i]))."</td>

		<td bgcolor=#efefef  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($yesterday_web_data[$i]+$yesterday_mobile_data[$i]))."</td>
		<td align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i]))."</td></tr>\n";

            $visit01_web = $visit01_web + $selected_date_web_data[$i];
            $visit01_mobile = $visit01_mobile + $selected_date_mobile_data[$i];
            //	$visit02 = $visit02 + $one_monthago_web_data[$i];
            $visit03 = $visit03 + $yesterday_web_data[$i]+$yesterday_mobile_data[$i];
            $visit04 = $visit04 + $oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i];

            if($minvalue > ($selected_date_web_data[$i] + $selected_date_mobile_data[$i]) || $i == 0 ){
                $minvalue = $selected_date_web_data[$i] + $selected_date_mobile_data[$i];
                $mintime = $i;
            }
            if($maxvalue < ($selected_date_web_data[$i] + $selected_date_mobile_data[$i] )|| $i == 0 ){
                $maxvalue = $selected_date_web_data[$i] + $selected_date_mobile_data[$i];
                $maxtime = $i;
            }

        }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
        $mstring .= "<tr height=30 align=right>
								<td width=150 class=s_td>합계</td>
								<td class=m_td style='padding-right:10px;'>".number_format(returnZeroValue($visit01_web))."</td>
								<td class=m_td style='padding-right:10px;'>".number_format(returnZeroValue($visit01_mobile))."</td>
								<td class=m_td style='padding-right:10px;'>".number_format(returnZeroValue($visit03))."</td>
								<td class=e_td style='padding-right:10px;'>".number_format(returnZeroValue($visit04))."</td>
								</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",number_format($maxvalue),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format($minvalue),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxtime,$vdate),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($mintime,$vdate),$mstring);
    }else if($SelectReport == 3){
        //$mstring .= TitleBar("방문횟수 - visit","월간 : ".getNameOfWeekday(0,$vdate,"monthname"));
        if(isset($_GET["mode"]) && ($_GET["mode"] == "pop" || $_GET["mode"] == "print")){
            $mstring .= TitleBar("방문횟수 ",$dateString, false, $exce_down_str);
        }else{
            $mstring .= TitleBar("방문횟수 ",$dateString, true, $exce_down_str);
        }

        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='visit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box' >\n";
        $mstring .= "<tr height=30 align=center><td width=150 class=s_td >항목</td><td class=m_td width=200>시간대</td><td class=e_td width=200>방문횟수</td></tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff>
		<td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
		<td align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">{{MAXTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right id='Report10000' style='padding-right:10px;' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">{{MAXVALUE}}</td>
		</tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff>
		<td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
		<td align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right id='Report10001' style='padding-right:10px;' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINVALUE}}</td>
		</tr>\n";
        $mstring .= "</table><br>\n";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
        $mstring .= "<col width=20%>
				<col width=20%>
				<col width=20%>
				<col width=20%>
				<col width=20%>";
        $mstring .= "<tr height=30 align=center>
						<td class=s_td rowspan=2>요일</td><td class=m_td colspan=2>해당월</td><!--td class=m_td >한달평균</td--><td class=m_td rowspan=2>1개월전 </td><td class=e_td rowspan=2>2개월전 </td>
					   </tr>
					   <tr height=30>
						<td class=m_td >웹</td>
						<td class=m_td >모바일</td>
					   </tr>\n";

        $labels = array("해당월","1개월전","2개월전");
        $ykeys = array("a","b","c");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => getNameOfWeekday($i,$vdate,"dayname"),
                'a' => ($selected_date_web_data[$i]+$selected_date_mobile_data[$i]) ,
                'b' => ($yesterday_web_data[$i]+$yesterday_mobile_data[$i]),
                'c' => ($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff >
		<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate,"dayname")."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($selected_date_web_data[$i]))."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($selected_date_mobile_data[$i]))."</td>


		<td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($yesterday_web_data[$i]+$yesterday_mobile_data[$i]))."</td>
		<td bgcolor=#ffffff  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i]))."</td></tr>\n";

            $visit01_web = $visit01_web + $selected_date_web_data[$i];
            $visit01_mobile = $visit01_mobile + $selected_date_mobile_data[$i];
            $visit02 = $visit02 + $one_monthago_web_data[$i];
            $visit03 = $visit03 + $yesterday_web_data[$i]+$yesterday_mobile_data[$i];
            $visit04 = $visit04 + $oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i];

            if($minvalue > ($selected_date_web_data[$i] + $selected_date_mobile_data[$i]) || $i == 0 ){
                $minvalue = $selected_date_web_data[$i] + $selected_date_mobile_data[$i];
                $minday = $i;
            }
            if($maxvalue < ($selected_date_web_data[$i] + $selected_date_mobile_data[$i]) || $i == 0 ){
                $maxvalue = $selected_date_web_data[$i] + $selected_date_mobile_data[$i];
                $maxday = $i;
            }

        }
        //$mstring .= "</table>\n";
        //$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
        $mstring .= "<tr height=30 align=right><td width=150 class=s_td width=30>합계</td>
								<td class=m_td >".number_format(returnZeroValue($visit01_web))."</td>
								<td class=m_td >".number_format(returnZeroValue($visit01_mobile))."</td>
								<!--td class=m_td >".number_format(returnZeroValue($visit02))."</td-->
								<td class=m_td >".number_format(returnZeroValue($visit03))."</td>
								<td class=e_td >".number_format(returnZeroValue($visit04))."</td>
								</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",number_format($maxvalue),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format($minvalue),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxday,$vdate,"dayname"),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($minday,$vdate,"dayname"),$mstring);
    }
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 방문횟수란? 쇼핑몰을 방문한 횟수를 재방문까지 모두 포함하여 시간대별로 집계한 데이터입니다.<br>
                - 시간대와 일자별로 최대, 최소 방문 횟수를 확인하실 수 있으며, 좌측 달력 이미지를 활용하여 주 단위, 월 단위 방문횟수 역시 확인이 가능합니다.<br>
                </td>
            </tr>
        </table>
        ";*/


    $mstring .= "<link href='../css/morris.css' rel='stylesheet'>
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
	<script script='javascript'>
	  new Morris.Line({
			// ID of the element in which to draw the chart.
			element: 'line-chart',
			// Chart data records -- each entry in this array corresponds to a point on
			// the chart.
			data: ".json_encode($chart_data).",
			xkey: 'y',
			ykeys: ".json_encode($ykeys).",
			labels: ".json_encode($labels).",
			lineColors: ['#D9534F', '#428BCA','#1CAF9A','#5BC0DE'],
			lineWidth: '2px',
			hideHover: true,
			parseTime:false
		});
	</script>";



    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("방문횟수", $help_text);

    return $mstring;
}

if ($mode == "iframe"){

//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";




    $ca = new Calendar();
    $ca->LinkPage = 'visit.php';

//	echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
//	echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";
    echo "<div id='report_view'>".ReportTable($vdate,$SelectReport)."</div>";
    echo "<div id='calendar_view'>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</div>";
    echo "</body>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.getElementById('report_view').innerHTML</Script>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.getElementById('calendar_view').innerHTML;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";
    echo "</html>";
}else if($mode == "pop" || $mode == "report" || $mode == "print"){
    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->Navigation = "로그분석 > 방문자 분석 > 방문횟수";

    $P->title = "방문횟수";
    //$P->strContents = ReportTable($vdate,$SelectReport);

    $P->NaviTitle = "방문횟수";
    $P->strContents = ReportTable($vdate,$SelectReport);

    $P->OnloadFunction = "";
    //	$P->layout_display = false;
    echo $P->PrintLayOut();

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 방문자 분석 > 방문횟수";
    $p->title = "방문횟수";
    $p->forbizLeftMenu = Stat_munu('visit.php');
    $p->forbizContents = ReportTable($vdate."01",$SelectReport);
    $p->PrintReportPage();
}
?>
<script type="text/javascript">
	//	wel_ 새벽시간(23시~07시)이나 휴무일 등 업무시간 외 다운로드시 검수 member_excel2003
	function ig_excel_dn_chk(s_val_Data) {
		//console.log(s_val_Data);
		var ig_now = new Date();   //현재시간
		var ig_hour = ig_now.getHours();   //현재 시간 중 시간.

			//	새벽시간(23시~07시), 휴무일(일, 토)
		//if(Number(ig_hour) >= "23" || Number(ig_hour) <= "7" || Number(ig_now.getDay()) == "0" || Number(ig_now.getDay()) == "6") {
			var ig_inputString = prompt('사유를 간략하게 입력하세요.\r\n(20자 이내(띄어쓰기포함), 특수문자 제외)');

			if(ig_inputString != null && ig_inputString.trim() != "") {
				//	엑셀다운로드 진행

					var str_length = ig_inputString.length;		// 전체길이

					if(str_length > "20") {
						alert("사유가 20자 이상 입니다.");
						return false;
					} else {
						var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

							if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

								var str_PW_length = ig_inputString_PW.length;		// 전체길이

								if(str_PW_length > "15") {
									alert("비밀번호를 15자 이하로 해주세요.");
									return false;
								} else {
									location.href = s_val_Data+"irs="+ig_inputString+"&ipw="+ig_inputString_PW;
								}

							} else {
								alert("비밀번호를 입력해 주세요.");
								return false;
							}
					}


			} else {
				alert("사유를 입력하세요");
				return false;
			}
		/*} else {
			//	일반 업무때 다운로드
			var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

				if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

					var str_PW_length = ig_inputString_PW.length;		// 전체길이

					if(str_PW_length > "15") {
						alert("비밀번호를 15자 이하로 해주세요.");
						return false;
					} else {
						location.href = s_val_Data+"&ipw="+ig_inputString_PW;
					}

				} else {
					alert("비밀번호를 입력해 주세요.");
					return false;
				}
		}*/



	}
</script>