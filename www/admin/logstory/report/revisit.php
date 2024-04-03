<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");

function ReportTable($vdate,$SelectReport=1){
    $fordb = new forbizDatabase();
    $fordb1 = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();

    $exce_down_str = "";
    $mstring = "";
    $sql = "";
    $sql2 = "";
    $sql3 = "";
    $one_monthago_web_data = "";

    $pageview01_web = "";
    $pageview02_web = "";
    $pageview03_web = "";
    $pageview04_web = "";

    $pageview01_mobile = "";
    $pageview02_mobile  = "";
    $pageview03_mobile = "";
    $pageview04_mobile = "";


    $minvalue  = "";
    $maxvalue  = "";

    $revisit01 = "";
    $revisit02  = "";
    $revisit03 = "";
    $revisit04 = "";

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
    }else{
        $vyesterday = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),(int)substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),(int)substr($vdate,0,4))-60*60*24*7);
        $vfourweekago = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),(int)substr($vdate,0,4))-60*60*24*28);
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
        $sql = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 from ".TBL_LOGSTORY_REVISITTIME." where vdate = '$vdate'";
        if($fordb1->dbms_type == "oracle"){

            $sql1 = "Select TO_NUMBER(avg(nh00)), TO_NUMBER(avg(nh01)), TO_NUMBER(avg(nh02)), TO_NUMBER(avg(nh03)), TO_NUMBER(avg(nh04)), TO_NUMBER(avg(nh05)), TO_NUMBER(avg(nh06)), TO_NUMBER(avg(nh07)), TO_NUMBER(avg(nh08)), TO_NUMBER(avg(nh09)), TO_NUMBER(avg(nh10)), TO_NUMBER(avg(nh11)), TO_NUMBER(avg(nh12)), TO_NUMBER(avg(nh13)), TO_NUMBER(avg(nh14)), TO_NUMBER(avg(nh15)), TO_NUMBER(avg(nh16)), TO_NUMBER(avg(nh17)), TO_NUMBER(avg(nh18)), TO_NUMBER(avg(nh19)), TO_NUMBER(avg(nh20)), TO_NUMBER(avg(nh21)), TO_NUMBER(avg(nh22)), TO_NUMBER(avg(nh23)) from ".TBL_LOGSTORY_REVISITTIME." where substr(vdate,1,6) = '".substr($vdate,0,6)."'";

        }else{
            $sql1 = "Select FORMAT(avg(nh00),1), FORMAT(avg(nh01),1), FORMAT(avg(nh02),1), FORMAT(avg(nh03),1), FORMAT(avg(nh04),1), FORMAT(avg(nh05),1), FORMAT(avg(nh06),1), FORMAT(avg(nh07),1), FORMAT(avg(nh08),1), FORMAT(avg(nh09),1), FORMAT(avg(nh10),1), FORMAT(avg(nh11),1), FORMAT(avg(nh12),1), FORMAT(avg(nh13),1), FORMAT(avg(nh14),1), FORMAT(avg(nh15),1), FORMAT(avg(nh16),1), FORMAT(avg(nh17),1), FORMAT(avg(nh18),1), FORMAT(avg(nh19),1), FORMAT(avg(nh20),1), FORMAT(avg(nh21),1), FORMAT(avg(nh22),1), FORMAT(avg(nh23),1) from ".TBL_LOGSTORY_REVISITTIME." where substring(vdate,1,6) = '".substr($vdate,0,6)."'";
        }
        $sql2 = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 from ".TBL_LOGSTORY_REVISITTIME." where vdate = '$vyesterday'";
        $sql3 = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 from ".TBL_LOGSTORY_REVISITTIME." where vdate = '$voneweekago'";

        $sql_web = $sql." and agent_type = 'W'  ";
        $sql_mobile = $sql." and agent_type = 'M' ";

        $fordb->query($sql_web);
        $fordb->fetch(0,"row");
        $selected_date_web_data = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_mobile_data = $fordb->dt;

        //$fordb->query($sql);
        $sql1_web = $sql1." and agent_type = 'W'  ";
        $sql1_mobile = $sql1." and agent_type = 'M' ";

        $fordb1->query($sql1_web);
        $fordb1->fetch(0,"row");
        $one_monthago_web_data = $fordb1->dt;

        $fordb1->query($sql1_mobile);
        $fordb1->fetch(0,"row");
        $one_monthago_mobile_data = $fordb1->dt;


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
        $oneweek_ago_web_data = $fordb3->dt;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $oneweek_ago_mobile_data = $fordb3->dt;
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
		 	from ".TBL_LOGSTORY_REVISITTIME."
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
		 	from ".TBL_LOGSTORY_REVISITTIME."
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
		 	from ".TBL_LOGSTORY_REVISITTIME."
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
        //$fordb1->query($sql1);
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
        $oneweek_ago_web_data = $fordb3->dt;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $oneweek_ago_mobile_data = $fordb3->dt;
        //echo "total:".$fordb->total;
        //$fordb->fetch(0);
        //$fordb1->fetch(0);
        //$fordb2->fetch(0);
        //$fordb3->fetch(0);

        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);

    }else if($SelectReport == 3){
        $sql .= "Select ";
        $sql .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql .= "from ".TBL_LOGSTORY_REVISITTIME." where vdate LIKE '".substr($vdate,0,6)."%' ";


        $sql2 .= "Select ";
        $sql2 .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql2 .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql2 .= "from ".TBL_LOGSTORY_REVISITTIME." where vdate LIKE '".substr($vonemonthago,0,6)."%' ";

        $sql3 .= "Select ";
        $sql3 .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql3 .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql3 .= "from ".TBL_LOGSTORY_REVISITTIME." where vdate LIKE '".substr($vtwomonthago,0,6)."%' ";


        $sql_web = $sql." and agent_type = 'W'  ";
        $sql_mobile = $sql." and agent_type = 'M' ";

        $fordb->query($sql_web);
        $fordb->fetch(0,"row");
        $selected_date_web_data = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_mobile_data = $fordb->dt;
        //$fordb1->query($sql1);
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
        $oneweek_ago_web_data = $fordb3->dt;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $oneweek_ago_mobile_data = $fordb3->dt;
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

            $sheet->getActiveSheet(0)->mergeCells('A2:D2');
            $sheet->getActiveSheet(0)->setCellValue('A2', "재방문횟수");
            $sheet->getActiveSheet(0)->mergeCells('A3:D3');
            $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

            $start=3;
            $i = $start;

            $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "요일");
            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "해당 월");
            $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "1개월 전");
            $sheet->getActiveSheet(0)->setCellValue('D' . ($i+1), "2개월 전");

            for($i=0;$i<$nLoop;$i++){

                $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), getNameOfWeekday($i,$vdate,"dayname"));
                $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), returnZeroValue($selected_date_web_data[$i]+$selected_date_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), returnZeroValue($yesterday_web_data[$i]+$yesterday_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), returnZeroValue($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $revisit01 = $revisit01 + $selected_date_web_data[$i]+$selected_date_mobile_data[$i];
                $revisit03 = $revisit03 + $yesterday_web_data[$i]+$yesterday_mobile_data[$i];
                $revisit04 = $revisit04 + $oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i];

            }

            //$i++;
            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), '합계');

            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), $revisit01);
            $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), $revisit03);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), $revisit04);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);

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
            $sheet->getActiveSheet()->getStyle('A'.($start+1).':D'.($i+$start+2))->applyFromArray($styleArray);
            $sheet->getActiveSheet()->getStyle('A'.$start.':D'.($i+$start+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getActiveSheet()->getStyle('A'.$start.':D'.($i+$start+2))->getFont()->setSize(10)->setName('돋움');

        }else if($SelectReport == '2'){

            $sheet->getActiveSheet(0)->mergeCells('A2:D2');
            $sheet->getActiveSheet(0)->setCellValue('A2', "재방문횟수");
            $sheet->getActiveSheet(0)->mergeCells('A3:D3');
            $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

            $start=3;
            $i = $start;

            $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "요일");
            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "해당 주");
            $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "1주 전");
            $sheet->getActiveSheet(0)->setCellValue('D' . ($i+1), "4주 전");

            for($i=0;$i<$nLoop;$i++){

                $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), getNameOfWeekday($i,$vdate));
                $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), returnZeroValue($selected_date_web_data[$i]+$selected_date_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), returnZeroValue($yesterday_web_data[$i]+$yesterday_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), returnZeroValue($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $pageview01_web = $pageview01_web + $selected_date_web_data[$i]+$selected_date_mobile_data[$i];
                $pageview03_web = $pageview03_web + $yesterday_web_data[$i]+$yesterday_mobile_data[$i];
                $pageview04_web = $pageview04_web + $oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i];

            }

            //$i++;
            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), '합계');

            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), $pageview01_web);
            $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), $pageview03_web);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), $pageview04_web);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);

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
            $sheet->getActiveSheet()->getStyle('A'.($start+1).':D'.($i+$start+2))->applyFromArray($styleArray);
            $sheet->getActiveSheet()->getStyle('A'.$start.':D'.($i+$start+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getActiveSheet()->getStyle('A'.$start.':D'.($i+$start+2))->getFont()->setSize(10)->setName('돋움');

        }else if($SelectReport == '1'){

            $sheet->getActiveSheet(0)->mergeCells('A2:I2');
            $sheet->getActiveSheet(0)->setCellValue('A2', "재방문횟수");
            $sheet->getActiveSheet(0)->mergeCells('A3:I3');
            $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

            $start=3;
            $i = $start;

            $sheet->getActiveSheet(0)->mergeCells('A' . ($i+1). ':A' . ($i+2));
            $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "시간");

            $sheet->getActiveSheet(0)->mergeCells('B' . ($i+1). ':C' . ($i+1));
            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "해당일");
            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+2), "웹");
            $sheet->getActiveSheet(0)->setCellValue('C' . ($i+2), "모바일");

            $sheet->getActiveSheet(0)->mergeCells('D' . ($i+1). ':E' . ($i+1));
            $sheet->getActiveSheet(0)->setCellValue('D' . ($i+1), "한 달 평균");
            $sheet->getActiveSheet(0)->setCellValue('D' . ($i+2), "웹");
            $sheet->getActiveSheet(0)->setCellValue('E' . ($i+2), "모바일");

            $sheet->getActiveSheet(0)->mergeCells('F' . ($i+1). ':G' . ($i+1));
            $sheet->getActiveSheet(0)->setCellValue('F' . ($i+1), "전 일");
            $sheet->getActiveSheet(0)->setCellValue('F' . ($i+2), "웹");
            $sheet->getActiveSheet(0)->setCellValue('G' . ($i+2), "모바일");

            $sheet->getActiveSheet(0)->mergeCells('H' . ($i+1). ':I' . ($i+1));
            $sheet->getActiveSheet(0)->setCellValue('H' . ($i+1), "1주 전");
            $sheet->getActiveSheet(0)->setCellValue('H' . ($i+2), "웹");
            $sheet->getActiveSheet(0)->setCellValue('I' . ($i+2), "모바일");


            for($i=0;$i<$nLoop;$i++){

                $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), $i);
                $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), returnZeroValue($selected_date_web_data[$i]));
                $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), returnZeroValue($selected_date_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), returnZeroValue($one_monthago_web_data[$i]));
                $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), returnZeroValue($one_monthago_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), returnZeroValue($yesterday_web_data[$i]));
                $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), returnZeroValue($yesterday_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), returnZeroValue($oneweek_ago_web_data[$i]));
                $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), returnZeroValue($oneweek_ago_mobile_data[$i]));
                $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $pageview01_web = $pageview01_web + $selected_date_web_data[$i];
                $pageview02_web = $pageview02_web + $one_monthago_web_data[$i];
                $pageview03_web = $pageview03_web + $yesterday_web_data[$i];
                $pageview04_web = $pageview04_web + $oneweek_ago_web_data[$i];

                $pageview01_mobile = $pageview01_mobile + $selected_date_mobile_data[$i];
                $pageview02_mobile = $pageview02_mobile + $one_monthago_mobile_data[$i];
                $pageview03_mobile = $pageview03_mobile + $yesterday_mobile_data[$i];
                $pageview04_mobile = $pageview04_mobile + $oneweek_ago_mobile_data[$i];

            }


            //$i++;
            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), '합계');

            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), $pageview01_web);
            $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), $pageview01_mobile);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), $pageview02_web);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $pageview02_mobile);
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), $pageview03_web);
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), $pageview03_mobile);
            $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), $pageview04_web);
            $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), $pageview04_mobile);
            $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $sheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);

            $sheet->setActiveSheetIndex(0);
            //$i = $i + 2;


            $sheet->getActiveSheet()->getRowDimension($start+2)->setRowHeight(30);

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
            $sheet->getActiveSheet()->getStyle('A'.($start+1).':I'.($i+$start+3))->applyFromArray($styleArray);
            $sheet->getActiveSheet()->getStyle('A'.$start.':I'.($i+$start+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getActiveSheet()->getStyle('A'.$start.':I'.($i+$start+3))->getFont()->setSize(10)->setName('돋움');

        }

        $sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움');
        $sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        unset($styleArray);
        //$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        //$objWriter->save('php://output');

		$sheet->getActiveSheet()->setTitle('재방문횟수');
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

        $mstring .= TitleBar("재방문횟수 ",$dateString, true, $exce_down_str);
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='revisit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";

        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=33%>".$SelectReport."항목</td><td class=m_td width=33%>시간대</td><td class=e_td width=34%>방문횟수</td></tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff id='Report10000'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
		<td onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\" align=center>{{MAXTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right style='padding-right:20px;' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\" >{{MAXVALUE}}</td>
		</tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff id='Report10001'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
		<td onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\" align=center>{{MINTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right style='padding-right:20px;' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINVALUE}}</td>
		</tr>\n";
        $mstring .= "</table><br>\n";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center>
						<td class=s_td width=10% rowspan=2>시간</td>
						<td class=m_td width=20% colspan=2>해당일</td>
						<td class=m_td width=20% colspan=2>한달평균</td>
						<td class=m_td width=20% colspan=2>전일</td>
						<td class=e_td width=20% colspan=2>1주전 </td>
						</tr>
						<tr height=30 align=center> 
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td> 
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td> 
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td> 
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td> 
						</tr>
						\n";

        $labels = array("해당일","한달평균","전일","1주전");
        $ykeys = array("a","b","c","d");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => ($i),
                'a' => ($selected_date_web_data[$i]+$selected_date_mobile_data[$i]) ,
                'b' => ($one_monthago_web_data[$i]+$one_monthago_mobile_data[$i]),
                'c' => ($yesterday_web_data[$i]+$yesterday_mobile_data[$i]),
                'd' => ($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">$i</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_data[$i]))."</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_mobile_data[$i]))."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($one_monthago_web_data[$i]))."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($one_monthago_mobile_data[$i]))."</td>
		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($yesterday_web_data[$i]))."</td>
		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($yesterday_mobile_data[$i]))."</td>
		<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($oneweek_ago_web_data[$i]))."</td>
		<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($oneweek_ago_mobile_data[$i]))."</td></tr>\n";

            $pageview01_web = $pageview01_web + $selected_date_web_data[$i];
            $pageview02_web = $pageview02_web + $one_monthago_web_data[$i];
            $pageview03_web = $pageview03_web + $yesterday_web_data[$i];
            $pageview04_web = $pageview04_web + $oneweek_ago_web_data[$i];

            $pageview01_mobile = $pageview01_mobile + $selected_date_mobile_data[$i];
            $pageview02_mobile = $pageview02_mobile + $one_monthago_mobile_data[$i];
            $pageview03_mobile = $pageview03_mobile + $yesterday_mobile_data[$i];
            $pageview04_mobile = $pageview04_mobile + $oneweek_ago_mobile_data[$i];


            if($minvalue > $selected_date_web_data[$i] || $i == 0 ){
                $minvalue = $selected_date_web_data[$i];
                $mintime = $i;
            }
            if($maxvalue < $selected_date_web_data[$i] || $i == 0 ){
                $maxvalue = $selected_date_web_data[$i];
                $maxtime = $i;
            }

        }

        $mstring .= "<tr height=30 align=center>
	<td width=50 class=s_td width=30>합계</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview01_web))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview01_mobile))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview02_web))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview02_mobile))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview03_web))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview03_mobile))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview04_web))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview04_mobile))."</td>
	</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",number_format($maxvalue),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format($minvalue),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getTimeString($maxtime),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getTimeString($mintime),$mstring);


    }else if($SelectReport == 2){
        $mstring .= TitleBar("재방문횟수",$dateString, true, $exce_down_str);
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='revisit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box' >\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=30%>항목</td><td class=m_td width=35%>요일</td><td class=e_td width=35%>방문횟수</td></tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff>
		<td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
		<td align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">{{MAXTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right id='Report10000' style='padding-right:20px;' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">{{MAXVALUE}}</td>
		</tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff>
		<td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
		<td align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right id='Report10001' style='padding-right:20px;' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINVALUE}}</td>
		</tr>\n";
        $mstring .= "</table><br>\n";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=25%>요일</td><td class=m_td width=25%>해당주</td><!--td class=m_td width=25%>한달평균</td--><td class=m_td width=25%>1주전 </td><td class=e_td width=25%>4주전 </td></tr>\n";

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
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_data[$i]+$selected_date_mobile_data[$i]))."</td>

		<td bgcolor=#efefef  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($yesterday_web_data[$i]+$yesterday_mobile_data[$i]))."</td>
		<td align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i]))."</td></tr>\n";

            $pageview01_web = $pageview01_web + $selected_date_web_data[$i]+$selected_date_mobile_data[$i];
            //	$pageview02_web = $pageview02_web + $one_monthago_web_data[$i];
            $pageview03_web = $pageview03_web + $yesterday_web_data[$i]+$yesterday_mobile_data[$i];
            $pageview04_web = $pageview04_web + $oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i];

            if($minvalue > $selected_date_web_data[$i] || $i == 0 ){
                $minvalue = $selected_date_web_data[$i];
                $mintime = $i;
            }
            if($maxvalue < $selected_date_web_data[$i] || $i == 0 ){
                $maxvalue = $selected_date_web_data[$i];
                $maxtime = $i;
            }

        }

        $mstring .= "<tr height=30 align=right>
	<td align=center class=s_td>합계</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview01_web))."</td>
	<!--td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview02_web))."</td-->
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview03_web))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview04_web))."</td>
	</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",number_format($maxvalue),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format($minvalue),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxtime,$vdate),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($mintime,$vdate),$mstring);
    }else if($SelectReport == 3){
        $mstring .= TitleBar("재방문횟수",$dateString, true, $exce_down_str);
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='revisit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box' >\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=30%>항목</td><td class=m_td width=35%>날짜</td><td class=e_td width=35%>방문횟수</td></tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff>
		<td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
		<td align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">{{MAXTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right id='Report10000' style='padding-right:20px;' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">{{MAXVALUE}}</td>
		</tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff>
		<td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
		<td align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right id='Report10001' style='padding-right:20px;' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINVALUE}}</td>
		</tr>\n";
        $mstring .= "</table><br>\n";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=25%>요일</td><td class=m_td width=25%>해당월</td><!--td class=m_td width=25%>한달평균</td--><td class=m_td width=25%>1개월전</td><td class=e_td width=25%>2개월전</td></tr>\n";

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
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_data[$i]+$selected_date_mobile_data[$i]))."</td>

		<td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($yesterday_web_data[$i]+$yesterday_mobile_data[$i]))."</td>
		<td bgcolor=#ffffff  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i]))."</td></tr>\n";


            $revisit01 = $revisit01 + $selected_date_web_data[$i]+$selected_date_mobile_data[$i];
            $revisit02 = $revisit02 + $one_monthago_web_data[$i];
            $revisit03 = $revisit03 + $yesterday_web_data[$i]+$yesterday_mobile_data[$i];
            $revisit04 = $revisit04 + $oneweek_ago_web_data[$i]+$oneweek_ago_mobile_data[$i];

            if($minvalue > $selected_date_web_data[$i] || $i == 0 ){
                $minvalue = $selected_date_web_data[$i];
                $minday = $i;
            }
            if($maxvalue < $selected_date_web_data[$i] || $i == 0 ){
                $maxvalue = $selected_date_web_data[$i];
                $maxday = $i;
            }

        }

        $mstring .= "<tr height=30 align=right>
	<td class=s_td align=center>합계</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($revisit01))."</td>
	<!--td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($revisit02))."</td-->
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($revisit03))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(returnZeroValue($revisit04))."</td>
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
                - 재방문횟수란? 쇼핑몰의 최초 방문 후 재방문을 한 방문자를 집계하여 확인 할 수 있는 리스트입니다.<br>
                - 이전에 쇼핑몰을 한번이라도 방문한 고객이 재방문 하였을 경우 데이터가 기록됩니다.<br>
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


    $mstring .= HelpBox("재방문횟수", $help_text);
    return $mstring;
}

if ($mode == "iframe"){

//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";




    $ca = new Calendar();
    $ca->LinkPage = 'revisit.php';

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


}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 방문자 분석 > 재방문횟수";
    $p->title = "재방문횟수";
    $p->forbizLeftMenu = Stat_munu('revisit.php');
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