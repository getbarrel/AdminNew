<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");

function ReportTable($vdate,$SelectReport=1){
    $fordb = new forbizDatabase();
    $forpcdb = new forbizDatabase();
    $formdb = new forbizDatabase();
    $foradb = new forbizDatabase();
    $fordb1 = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();

    $sql = "";
    $sqlpc = "";
    $sqlm = "";
    $sqla = "";
    $sql2 = "";
    $sql3 = "";
    $exce_down_str = "";
    $mstring = "";
    $pageview01 = "";
    $pageview02 = "";
    $pageview03 = "";
    $pageview04 = "";
    $pageviewpc = "";
    $pageviewm = "";
    $pageviewa = "";
    $minvalue = "";
    $maxvalue = "";


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
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
        $vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))-60*60*24);
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))-60*60*24*60);
    }

    if($SelectReport == 1){
        $nLoop = 24;
    }else if($SelectReport ==2){
        $nLoop = 7;
    }else if($SelectReport ==3){
        $nLoop = date("t", mktime(0, 0, 0, substr($vdate,4,2), substr($vdate,6,2), substr($vdate,0,4)));
    }

    if($SelectReport == 1){
        $sql = "Select sum(nh00) as nh00, sum(nh01) as nh01, sum(nh02) as nh02, sum(nh03) as nh03, sum(nh04) as nh04, sum(nh05) as nh05, 
					sum(nh06) as nh06, sum(nh07) as nh07, sum(nh08) as nh08, sum(nh09) as nh09, sum(nh10) as nh10, sum(nh11) as nh11, 
					sum(nh12) as nh12, sum(nh13) as nh13, sum(nh14) as nh14, sum(nh15) as nh15, sum(nh16) as nh16, sum(nh17) as nh17, 
					sum(nh18) as nh18, sum(nh19) as nh19, sum(nh20) as nh20, sum(nh21) as nh21, sum(nh22)  as nh22, sum(nh23)  as nh23 from ".TBL_LOGSTORY_VISITOR." where vdate = '$vdate'";
        $sqlpc = "Select sum(nh00) as nh00, sum(nh01) as nh01, sum(nh02) as nh02, sum(nh03) as nh03, sum(nh04) as nh04, sum(nh05) as nh05, 
					sum(nh06) as nh06, sum(nh07) as nh07, sum(nh08) as nh08, sum(nh09) as nh09, sum(nh10) as nh10, sum(nh11) as nh11, 
					sum(nh12) as nh12, sum(nh13) as nh13, sum(nh14) as nh14, sum(nh15) as nh15, sum(nh16) as nh16, sum(nh17) as nh17, 
					sum(nh18) as nh18, sum(nh19) as nh19, sum(nh20) as nh20, sum(nh21) as nh21, sum(nh22)  as nh22, sum(nh23)  as nh23 from ".TBL_LOGSTORY_VISITOR." where agent_type = 'W' and vdate = '$vdate'";
        $sqlm = "Select sum(nh00) as nh00, sum(nh01) as nh01, sum(nh02) as nh02, sum(nh03) as nh03, sum(nh04) as nh04, sum(nh05) as nh05, 
					sum(nh06) as nh06, sum(nh07) as nh07, sum(nh08) as nh08, sum(nh09) as nh09, sum(nh10) as nh10, sum(nh11) as nh11, 
					sum(nh12) as nh12, sum(nh13) as nh13, sum(nh14) as nh14, sum(nh15) as nh15, sum(nh16) as nh16, sum(nh17) as nh17, 
					sum(nh18) as nh18, sum(nh19) as nh19, sum(nh20) as nh20, sum(nh21) as nh21, sum(nh22)  as nh22, sum(nh23)  as nh23 from ".TBL_LOGSTORY_VISITOR." where agent_type in ('M','') and vdate = '$vdate'";
        $sqla = "Select sum(nh00) as nh00, sum(nh01) as nh01, sum(nh02) as nh02, sum(nh03) as nh03, sum(nh04) as nh04, sum(nh05) as nh05, 
					sum(nh06) as nh06, sum(nh07) as nh07, sum(nh08) as nh08, sum(nh09) as nh09, sum(nh10) as nh10, sum(nh11) as nh11, 
					sum(nh12) as nh12, sum(nh13) as nh13, sum(nh14) as nh14, sum(nh15) as nh15, sum(nh16) as nh16, sum(nh17) as nh17, 
					sum(nh18) as nh18, sum(nh19) as nh19, sum(nh20) as nh20, sum(nh21) as nh21, sum(nh22)  as nh22, sum(nh23)  as nh23 from ".TBL_LOGSTORY_VISITOR." where agent_type = 'A' and vdate = '$vdate'";
        $sql1 = "Select avg(nh00), avg(nh01), avg(nh02), avg(nh03), avg(nh04), avg(nh05), avg(nh06), avg(nh07), avg(nh08), avg(nh09), avg(nh10), avg(nh11), avg(nh12), avg(nh13), avg(nh14), avg(nh15), avg(nh16), avg(nh17), avg(nh18), avg(nh19), avg(nh20), avg(nh21), avg(nh22), avg(nh23) from ".TBL_LOGSTORY_VISITOR." where substr(vdate,1,6) = '".substr($vdate,0,6)."'";
        $sql2 = "Select sum(nh00) as nh00, sum(nh01) as nh01, sum(nh02) as nh02, sum(nh03) as nh03, sum(nh04) as nh04, sum(nh05) as nh05, 
					sum(nh06) as nh06, sum(nh07) as nh07, sum(nh08) as nh08, sum(nh09) as nh09, sum(nh10) as nh10, sum(nh11) as nh11, 
					sum(nh12) as nh12, sum(nh13) as nh13, sum(nh14) as nh14, sum(nh15) as nh15, sum(nh16) as nh16, sum(nh17) as nh17, 
					sum(nh18) as nh18, sum(nh19) as nh19, sum(nh20) as nh20, sum(nh21) as nh21, sum(nh22)  as nh22, sum(nh23)  as nh23 from ".TBL_LOGSTORY_VISITOR." where vdate = '$vyesterday'";
        $sql3 = "Select sum(nh00) as nh00, sum(nh01) as nh01, sum(nh02) as nh02, sum(nh03) as nh03, sum(nh04) as nh04, sum(nh05) as nh05, 
					sum(nh06) as nh06, sum(nh07) as nh07, sum(nh08) as nh08, sum(nh09) as nh09, sum(nh10) as nh10, sum(nh11) as nh11, 
					sum(nh12) as nh12, sum(nh13) as nh13, sum(nh14) as nh14, sum(nh15) as nh15, sum(nh16) as nh16, sum(nh17) as nh17, 
					sum(nh18) as nh18, sum(nh19) as nh19, sum(nh20) as nh20, sum(nh21) as nh21, sum(nh22)  as nh22, sum(nh23)  as nh23 from ".TBL_LOGSTORY_VISITOR." where vdate = '$voneweekago'";


        $fordb->query($sql);
        $forpcdb->query($sqlpc);
        $formdb->query($sqlm);
        $foradb->query($sqla);
        $fordb1->query($sql1);
        $fordb2->query($sql2);
        $fordb3->query($sql3);
        //echo "total:".$fordb->total;
        $fordb->fetch(0);
        $forpcdb->fetch(0);
        $formdb->fetch(0);
        $foradb->fetch(0);
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
		 	from ".TBL_LOGSTORY_VISITOR."
		 	where vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' ";

        $sqlpc = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_VISITOR."
		 	where agent_type = 'W' and vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' ";

        $sqlm = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_VISITOR."
		 	where agent_type in ('M','') and vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' ";

        $sqla = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_VISITOR."
		 	where agent_type = 'A' and vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' ";

        $sql2 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_VISITOR."
		 	where vdate between '$voneweekago' and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."' ";
        $sql3 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_VISITOR."
		 	where vdate between '$vfourweekago' and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."' ";



        $fordb->query($sql);
        $forpcdb->query($sqlpc);
        $formdb->query($sqlm);
        $foradb->query($sqla);
        //$fordb1->query($sql1);
        $fordb2->query($sql2);
        $fordb3->query($sql3);
        //echo "total:".$fordb->total;
        $fordb->fetch(0);
        $forpcdb->fetch(0);
        $formdb->fetch(0);
        $foradb->fetch(0);
        //$fordb1->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);

        $dateString = getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);

    }else if($SelectReport == 3){
        $sql .= "Select ";
        $sql .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql .= "from ".TBL_LOGSTORY_VISITOR." where vdate LIKE '".substr($vdate,0,6)."%'  ";

        $sqlpc .= "Select ";
        $sqlpc .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sqlpc .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sqlpc .= "from ".TBL_LOGSTORY_VISITOR." where agent_type = 'W' and vdate LIKE '".substr($vdate,0,6)."%'  ";

        $sqlm .= "Select ";
        $sqlm .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sqlm .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sqlm .= "from ".TBL_LOGSTORY_VISITOR." where agent_type in ('M','') and vdate LIKE '".substr($vdate,0,6)."%'  ";

        $sqla .= "Select ";
        $sqla .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sqla .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sqla .= "from ".TBL_LOGSTORY_VISITOR." where agent_type = 'A' and vdate LIKE '".substr($vdate,0,6)."%'  ";

        $sql2 .= "Select ";
        $sql2 .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql2 .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql2 .= "from ".TBL_LOGSTORY_VISITOR." where vdate LIKE '".substr($vonemonthago,0,6)."%'  ";

        $sql3 .= "Select ";
        $sql3 .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql3 .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql3 .= "from ".TBL_LOGSTORY_VISITOR." where vdate LIKE '".substr($vtwomonthago,0,6)."%'  ";

        //echo $sqla;
        $fordb->query($sql);
        $forpcdb->query($sqlpc);
        $formdb->query($sqlm);
        $foradb->query($sqla);
        //$fordb1->query($sql1);
        $fordb2->query($sql2);
        $fordb3->query($sql3);
        //echo "total:".$fordb->total;
        $fordb->fetch(0);
        $forpcdb->fetch(0);
        $formdb->fetch(0);
        $foradb->fetch(0);
        //$fordb1->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);

        $dateString = getNameOfWeekday(0,$vdate,"monthname");

    }


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
            $sheet->getActiveSheet(0)->setCellValue('A2', "방문횟수");
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
                $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), returnZeroValue($fordb->dt[$i]));
                $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), returnZeroValue($fordb2->dt[$i]));
                $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), returnZeroValue($fordb3->dt[$i]));
                $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $pageview01 = $pageview01 + returnZeroValue($fordb->dt[$i]);
                $pageview03 = $pageview03 + returnZeroValue($fordb2->dt[$i]);
                $pageview04 = $pageview04 + returnZeroValue($fordb3->dt[$i]);

            }

            //$i++;
            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), '합계');

            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), $pageview01);
            $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), $pageview03);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), $pageview04);
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
            $sheet->getActiveSheet(0)->setCellValue('A2', "방문횟수");
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
                $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), returnZeroValue($fordb->dt[$i]));
                $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), returnZeroValue($fordb2->dt[$i]));
                $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), returnZeroValue($fordb3->dt[$i]));
                $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $pageview01 = $pageview01 + returnZeroValue($fordb->dt[$i]);
                $pageview03 = $pageview03 + returnZeroValue($fordb2->dt[$i]);
                $pageview04 = $pageview04 + returnZeroValue($fordb3->dt[$i]);

            }

            //$i++;
            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), '합계');

            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), $pageview01);
            $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), $pageview03);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), $pageview04);
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

            $sheet->getActiveSheet(0)->mergeCells('A2:E2');
            $sheet->getActiveSheet(0)->setCellValue('A2', "방문횟수");
            $sheet->getActiveSheet(0)->mergeCells('A3:E3');
            $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

            $start=3;
            $i = $start;

            $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "시간");
            $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "해당 일");
            $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "한 달 평균");
            $sheet->getActiveSheet(0)->setCellValue('D' . ($i+1), "전일");
            $sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "1주 전");

            for($i=0;$i<$nLoop;$i++){

                $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), $i);
                $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), returnZeroValue($fordb->dt[$i]));
                $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), returnZeroValue($fordb1->dt[$i]));
                $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), returnZeroValue($fordb2->dt[$i]));
                $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 2), returnZeroValue($fordb3->dt[$i]));
                $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

                $pageview01 = $pageview01 + returnZeroValue($fordb->dt[$i]);
                $pageview02 = $pageview02 + returnZeroValue($fordb1->dt[$i]);
                $pageview03 = $pageview03 + returnZeroValue($fordb2->dt[$i]);
                $pageview04 = $pageview04 + returnZeroValue($fordb3->dt[$i]);

            }

            //$i++;
            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), '합계');

            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), $pageview01);
            $sheet->getActiveSheet()->getStyle('B' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), $pageview02);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), $pageview03);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 2), $pageview04);
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
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
            $sheet->getActiveSheet()->getStyle('A'.($start+1).':E'.($i+$start+2))->applyFromArray($styleArray);
            $sheet->getActiveSheet()->getStyle('A'.$start.':E'.($i+$start+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getActiveSheet()->getStyle('A'.$start.':E'.($i+$start+2))->getFont()->setSize(10)->setName('돋움');

        }

        $sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움');
        $sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($i+$start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        unset($styleArray);
        //$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        //$objWriter->save('php://output');

		$sheet->getActiveSheet()->setTitle('순수방문자');
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


//	echo nl2br($sql) ."<br>";
//	echo $sql1 ."<br>";
//	echo $sql2 ."<br>";
//	echo $sql3 ."<br>";


    //$exce_down_str .= "<a href='?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."'><img //src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";

	$exce_down_str = "<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0 style='cursor:pointer' 
	onclick=\"ig_excel_dn_chk('?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."');\">";

    if($SelectReport == 1){

        $mstring = $mstring.TitleBar("순수방문자",$dateString, true, $exce_down_str);
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='visitor.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=33%>항목</td><td class=m_td width=33%>시간대</td><td class=e_td width=34%>순수방문자수</td></tr>\n";
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
					<td class=s_td width=10%>시간</td>
					<td class=m_td width=10%>해당일(전체)</td>
					<td class=m_td width=10%>해당일(PC)</td>
					<td class=m_td width=10%>해당일(M)</td>
					<td class=m_td width=10%>해당일(A)</td>
					<td class=m_td width=10%>한달평균</td>
					<td class=m_td width=10%>전일</td>
					<td class=e_td width=10%>1주전 </td>
				</tr>\n";

        $labels = array("해당일","한달평균","전일","1주전","PC","M","A");
        $ykeys = array("a","b","c","d","p","m","i");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => ($i),
                'a' => ($fordb->dt[$i]),
                'b' => ($fordb1->dt[$i]),
                'c' => ($fordb2->dt[$i]),
                'd' => ($fordb3->dt[$i]),
                'p' => ($forpcdb->dt[$i]),
                'm' => ($formdb->dt[$i]),
                'i' => ($foradb->dt[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
						<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">$i</td>
						<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[$i]))."</td>
						<td align='right' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($forpcdb->dt[$i]))."</td>
						<td bgcolor='#efefef' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($formdb->dt[$i]))."</td>
						<td align='right' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($foradb->dt[$i]))."</td>
						<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb1->dt[$i]))."</td>
						<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb2->dt[$i]))."</td>
						<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb3->dt[$i]))."</td>
					</tr>\n";

            $pageview01 = $pageview01 + returnZeroValue($fordb->dt[$i]);
            $pageviewpc = $pageviewpc + returnZeroValue($forpcdb->dt[$i]);
            $pageviewm = $pageviewm + returnZeroValue($formdb->dt[$i]);
            $pageviewa = $pageviewa + returnZeroValue($foradb->dt[$i]);
            $pageview02 = $pageview02 + returnZeroValue($fordb1->dt[$i]);
            $pageview03 = $pageview03 + returnZeroValue($fordb2->dt[$i]);
            $pageview04 = $pageview04 + returnZeroValue($fordb3->dt[$i]);

            if($minvalue > $fordb->dt[$i] || $i == 0 ){
                $minvalue = $fordb->dt[$i];
                $mintime = $i;
            }
            if($maxvalue < $fordb->dt[$i] || $i == 0 ){
                $maxvalue = $fordb->dt[$i];
                $maxtime = $i;
            }

        }

        $mstring .= "<tr height=30 align=right>
	<td class=s_td align=center>합계</td>
	<td class=m_td width=190>".number_format($pageview01)."</td>
	<td class=m_td width=190>".number_format($pageviewpc)."</td>
	<td class=m_td width=190>".number_format($pageviewm)."</td>
	<td class=m_td width=190>".number_format($pageviewa)."</td>
	<td class=m_td width=190>".number_format($pageview02)."</td>
	<td class=m_td width=190>".number_format($pageview03)."</td>
	<td class=e_td width=190>".number_format($pageview04)."</td>
	</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",number_format($maxvalue),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format($minvalue),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getTimeString($maxtime),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getTimeString($mintime),$mstring);


    }else if($SelectReport == 2){
        $mstring = $mstring.TitleBar("순수방문자 ",$dateString, true, $exce_down_str);
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='visitor.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=30%>항목</td><td class=m_td width=35%>요일</td><td class=e_td width=35%>순수방문자수</td></tr>\n";
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
        $mstring .= "<tr height=30 align=center>
					<td class=s_td width=10%>요일</td>
					<td class=m_td width=10%>해당주</td>
					<td class=m_td width=10%>해당주(PC)</td>
					<td class=m_td width=10%>해당주(M)</td>
					<td class=m_td width=10%>해당주(A)</td>
					<!--td class=m_td width=25%>한달평균</td-->
					<td class=m_td width=10%>1주전 </td>
					<td class=e_td width=10%>4주전 </td>
				</tr>\n";

        $labels = array("해당주","1주전","4주전","PC","M","A");
        $ykeys = array("a","b","c","p","m","i");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => getNameOfWeekday($i,$vdate),
                'a' => ($fordb->dt[$i]),
                'b' => ($fordb2->dt[$i]),
                'c' => ($fordb3->dt[$i]),
                'p' => ($forpcdb->dt[$i]),
                'm' => ($formdb->dt[$i]),
                'i' => ($foradb->dt[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff >
						<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate)."</td>
						<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[$i]))."</td>
						<td align=right align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($forpcdb->dt[$i]))."</td>
						<td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($formdb->dt[$i]))."</td>
						<td align=right align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($foradb->dt[$i]))."</td>
						<!--td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb1->dt[$i]))."</td-->
						<td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb2->dt[$i]))."</td>
						<td  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb3->dt[$i]))."</td>
					</tr>\n";

            $pageview01 = $pageview01 + returnZeroValue($fordb->dt[$i]);
            $pageviewpc = $pageviewpc + returnZeroValue($forpcdb->dt[$i]);
            $pageviewm = $pageviewm + returnZeroValue($formdb->dt[$i]);
            $pageviewa = $pageviewa + returnZeroValue($foradb->dt[$i]);
            //	$pageview02 = $pageview02 + returnZeroValue($fordb1->dt[$i]);
            $pageview03 = $pageview03 + returnZeroValue($fordb2->dt[$i]);
            $pageview04 = $pageview04 + returnZeroValue($fordb3->dt[$i]);

            if($minvalue > $fordb->dt[$i] || $i == 0 ){
                $minvalue = $fordb->dt[$i];
                $mintime = $i;
            }
            if($maxvalue < $fordb->dt[$i] || $i == 0 ){
                $maxvalue = $fordb->dt[$i];
                $maxtime = $i;
            }

        }

        $mstring .= "<tr height=30 align=right>
	<td class=s_td align=center>합계</td>
	<td class=m_td style='padding-right:20px;'>".number_format($pageview01)."</td>
	<td class=m_td style='padding-right:20px;'>".number_format($pageviewpc)."</td>
	<td class=m_td style='padding-right:20px;'>".number_format($pageviewm)."</td>
	<td class=m_td style='padding-right:20px;'>".number_format($pageviewa)."</td>

	<td class=m_td style='padding-right:20px;'>".number_format($pageview03)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($pageview04)."</td>
	</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",number_format($maxvalue),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format($minvalue),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxtime,$vdate),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($mintime,$vdate),$mstring);
    }else if($SelectReport == 3){
        $mstring = $mstring.TitleBar("순수방문자 ",$dateString, true, $exce_down_str);
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='visitor.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box' >\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=33%>항목</td><td class=m_td width=33%>날짜</td><td class=e_td width=33%>순수방문자수</td></tr>\n";
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
        $mstring .= "<tr height=30 align=center>
					<td class=s_td width=10%>요일</td>
					<td class=m_td width=10%>해당월</td>
					<td class=m_td width=10%>해당월(PC)</td>
					<td class=m_td width=10%>해당월(M)</td>
					<td class=m_td width=10%>해당월(A)</td>
					<!--td class=m_td width=10%>한달평균</td-->
					<td class=m_td width=10%>1개월전</td>
					<td class=e_td width=10%>2개월전</td>
				</tr>\n";

        $labels = array("해당월","1개월전","2개월전","PC","M","A");
        $ykeys = array("a","b","c","p","m","i");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => getNameOfWeekday($i,$vdate,"dayname"),
                'a' => ($fordb->dt[$i]),
                'b' => ($fordb2->dt[$i]),
                'c' => ($fordb3->dt[$i]),
                'p' => ($forpcdb->dt[$i]),
                'm' => ($formdb->dt[$i]),
                'i' => ($foradb->dt[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff >
						<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate,"dayname")."</td>
						<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[$i]))."</td>
						<td align=right align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($forpcdb->dt[$i]))."</td>
						<td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($formdb->dt[$i]))."</td>
						<td align=right align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($foradb->dt[$i]))."</td>
						<!--td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb1->dt[$i]))."</td-->
						<td bgcolor=#efefef  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb2->dt[$i]))."</td>
						<td align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb3->dt[$i]))."</td>
					</tr>\n";

            $pageview01 = $pageview01 + returnZeroValue($fordb->dt[$i]);
            $pageviewpc = $pageviewpc + returnZeroValue($forpcdb->dt[$i]);
            $pageviewm = $pageviewm + returnZeroValue($formdb->dt[$i]);
            $pageviewa = $pageviewa + returnZeroValue($foradb->dt[$i]);
            //	$pageview02 = $pageview02 + returnZeroValue($fordb1->dt[$i]);
            $pageview03 = $pageview03 + returnZeroValue($fordb2->dt[$i]);
            $pageview04 = $pageview04 + returnZeroValue($fordb3->dt[$i]);

            if($minvalue > $fordb->dt[$i] || $i == 0 ){
                $minvalue = $fordb->dt[$i];
                $minday = $i;
            }
            if($maxvalue < $fordb->dt[$i] || $i == 0 ){
                $maxvalue = $fordb->dt[$i];
                $maxday = $i;
            }

        }

        $mstring .= "<tr height=30 align=right>
	<td  class=s_td align=center>합계</td>
	<td class=m_td style='padding-right:20px;'>".number_format($pageview01)."</td>
	<td class=m_td style='padding-right:20px;'>".number_format($pageviewpc)."</td>
	<td class=m_td style='padding-right:20px;'>".number_format($pageviewm)."</td>
	<td class=m_td style='padding-right:20px;'>".number_format($pageviewa)."</td>

	<td class=m_td style='padding-right:20px;'>".number_format($pageview03)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($pageview04)."</td>
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
                - 순수방문자란 ? 하루동안 쇼핑몰을 방문한 사람으로 방문횟수와는 다른 개념입니다. 순수방문자는 같은사람이 3번을 방문했을경우 순수방문자는 1명으로 간주되어 분석됩니다<br>
                - 순수방문자 사이트를 평가하는 중요한 팩터중의 한가지로 해당기간동안 사이트를 방문한 절대적인 사람을 말합니다.<br>
                - 순수방문자를 시간대별로 보여주고 있으며, 주간리포트는 요일별, 월간리포트는 일자별로 제공됩니다<br>
                - 리포트는 해당일에 대한 수치 뿐만아니라  한달평균, 전일, 1주전을 비교하여 제공하고 있으며 이는 현재일의 수치를 다른 값들과 비교하여 리포트를 이해하시는데 도움이 됩니다
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


    $mstring .= HelpBox("순수방문자", $help_text);
    return $mstring;
}

if ($mode == "iframe"){

    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";




    $ca = new Calendar();
    $ca->LinkPage = 'visitor.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 순수방문자 분석 > 순수방문자";
    $p->title = "순수방문자";
    $p->forbizLeftMenu = Stat_munu('visitor.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
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