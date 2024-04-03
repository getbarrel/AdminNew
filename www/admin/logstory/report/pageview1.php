<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");

function ReportTable($vdate,$SelectReport=1){
    $fordb = new forbizDatabase();
    $fordb1 = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();
    $fordb4 = new forbizDatabase();

    $sql = "";
    $sql2 = "";
    $sql3 = "";


    $pageview01 ="";
    $pageview03 ="";
    $pageview04 ="";

    $pageview01_mobile ="";
    $pageview03_mobile ="";
    $pageview04_mobile ="";

    $minvalue ="";
    $maxvalue ="";
    $duration_sum = "";
    $pageview_sum_mobile = '1';
    $duration_sum_mobile = "";

    if($SelectReport == ""){
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
            $vdate = $vdate;
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
        $sql = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 
					from ".TBL_LOGSTORY_PAGEVIEWTIME." 
					where vdate = '$vdate' and agent_type = 'W' ";

        $sql_mobile = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 
					from ".TBL_LOGSTORY_PAGEVIEWTIME." 
					where vdate = '$vdate' and agent_type = 'M' ";

        $sql1 = "Select avg(nh00), avg(nh01), avg(nh02), avg(nh03), avg(nh04), avg(nh05), avg(nh06), avg(nh07), avg(nh08), avg(nh09), avg(nh10), avg(nh11), avg(nh12), avg(nh13), avg(nh14), avg(nh15), avg(nh16), avg(nh17), avg(nh18), avg(nh19), avg(nh20), avg(nh21), avg(nh22), avg(nh23) 
		from ".TBL_LOGSTORY_PAGEVIEWTIME." 
		where vdate LIKE '".substr($vdate,0,6)."%' and agent_type = 'W'  ";

        $sql1_mobile = "Select avg(nh00), avg(nh01), avg(nh02), avg(nh03), avg(nh04), avg(nh05), avg(nh06), avg(nh07), avg(nh08), avg(nh09), avg(nh10), avg(nh11), avg(nh12), avg(nh13), avg(nh14), avg(nh15), avg(nh16), avg(nh17), avg(nh18), avg(nh19), avg(nh20), avg(nh21), avg(nh22), avg(nh23) 
		from ".TBL_LOGSTORY_PAGEVIEWTIME." 
		where vdate LIKE '".substr($vdate,0,6)."%' and agent_type = 'M'  ";

        $sql2 = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 
						from ".TBL_LOGSTORY_PAGEVIEWTIME." 
						where vdate = '$vyesterday' and agent_type = 'W' ";

        $sql2_mobile = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 
						from ".TBL_LOGSTORY_PAGEVIEWTIME." 
						where vdate = '$vyesterday' and agent_type = 'M' ";

        $sql3 = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 
						from ".TBL_LOGSTORY_PAGEVIEWTIME." 
						where vdate = '$voneweekago' and agent_type = 'W' ";

        $sql3_mobile = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 
						from ".TBL_LOGSTORY_PAGEVIEWTIME." 
						where vdate = '$voneweekago' and agent_type = 'M' ";

        $sql4 = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 
						from ".TBL_LOGSTORY_DURATIONTIME." 
						where vdate = '$vdate' and agent_type = 'W' ";

        $sql4_mobile = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 
						from ".TBL_LOGSTORY_DURATIONTIME." 
						where vdate = '$vdate' and agent_type = 'W' ";


        $fordb->query($sql);
        $fordb->fetch(0,"row");
        $selected_date_web_pageview = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_mobile_pageview = $fordb->dt;

        $fordb1->query($sql1);
        $fordb1->fetch(0);
        $selected_date_web_month_avg = $fordb1->dt;

        $fordb1->query($sql1_mobile);
        $fordb1->fetch(0);
        $selected_date_mobile_month_avg = $fordb1->dt;

        $fordb2->query($sql2);
        $fordb2->fetch(0);
        $selected_date_web_yesterday_pageview = $fordb2->dt;

        $fordb2->query($sql2_mobile);
        $fordb2->fetch(0);
        $selected_date_mobile_yesterday_pageview = $fordb2->dt;

        $fordb3->query($sql3);
        $fordb3->fetch(0);
        $selected_date_web_onweekago_pageview = $fordb3->dt;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0);
        $selected_date_mobile_onweekago_pageview = $fordb3->dt;

        $fordb4->query($sql4);
        $fordb4->fetch(0);
        $selected_date_web_duration = $fordb4->dt;

        $fordb4->query($sql4);
        $fordb4->fetch(0);
        $selected_date_web_duration = $fordb4->dt;

        $fordb4->query($sql4_mobile);
        $fordb4->fetch(0);
        $selected_date_mobile_duration = $fordb4->dt;
        //echo "total:".$fordb->total;

        //print_r($selected_date_web_pageview);

        $fordb2->fetch(0);
        $fordb3->fetch(0);
        $fordb4->fetch(0);

    }else if($SelectReport == 2){
        $sql = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_PAGEVIEWTIME." 
			where vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' and agent_type = 'W' ";

        $sql_mobile = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_PAGEVIEWTIME." 
			where vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' and agent_type = 'M' ";

        //echo $sql;
//		$sql1 = "Select FORMAT(avg(nh00),1), FORMAT(avg(nh01),1), FORMAT(avg(nh02),1), FORMAT(avg(nh03),1), FORMAT(avg(nh04),1), FORMAT(avg(nh05),1), FORMAT(avg(nh06),1), FORMAT(avg(nh07),1), FORMAT(avg(nh08),1), FORMAT(avg(nh09),1), FORMAT(avg(nh10),1), FORMAT(avg(nh11),1), FORMAT(avg(nh12),1), FORMAT(avg(nh13),1), FORMAT(avg(nh14),1), FORMAT(avg(nh15),1), FORMAT(avg(nh16),1), FORMAT(avg(nh17),1), FORMAT(avg(nh18),1), FORMAT(avg(nh19),1), FORMAT(avg(nh20),1), FORMAT(avg(nh21),1), FORMAT(avg(nh22),1), FORMAT(avg(nh23),1) from ".TBL_LOGSTORY_PAGEVIEWTIME." where substring(vdate,1,6) = '".substr($vdate,0,6)."'";
        $sql2 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_PAGEVIEWTIME." 
			where vdate between '$voneweekago' and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."' and agent_type = 'W'  ";

        $sql2_mobile = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_PAGEVIEWTIME." 
			where vdate between '$voneweekago' and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."' and agent_type = 'M'  ";

        $sql3 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_PAGEVIEWTIME." 
			where vdate between '$vfourweekago' and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."' and agent_type = 'W'  ";

        $sql3_mobile = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_PAGEVIEWTIME." 
			where vdate between '$vfourweekago' and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."' and agent_type = 'M'  ";

        $sql4 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then nduration else 0 end)
		 	from ".TBL_LOGSTORY_DURATIONTIME." 
			where vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."'
			and agent_type = 'W' ";

        $sql4_mobile = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then nduration else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then nduration else 0 end)
		 	from ".TBL_LOGSTORY_DURATIONTIME." 
			where vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."'
			and agent_type = 'M' ";



        $fordb->query($sql);
        $fordb->fetch(0,"row");
        $selected_date_web_pageview = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_web_pageview = $fordb->dt;
        //$fordb1->query($sql1);
        $fordb2->query($sql2);
        $fordb2->fetch(0,"row");
        $selected_date_web_onweekago_pageview = $fordb2->dt;

        $fordb2->query($sql2_mobile);
        $fordb2->fetch(0,"row");
        $selected_date_mobile_onweekago_pageview = $fordb2->dt;

        $fordb3->query($sql3);
        $fordb3->fetch(0,"row");
        $selected_date_web_fourweekago_pageview = $fordb3->dt;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $selected_date_mobile_fourweekago_pageview = $fordb3->dt;

        $fordb4->query($sql4);
        $fordb4->fetch(0);
        $selected_date_web_duration = $fordb4->dt;

        $fordb4->query($sql4_mobile);
        $fordb4->fetch(0);
        $selected_date_mobile_duration = $fordb4->dt;
        //echo "total:".$fordb->total;
        //$fordb->fetch(0,"row");
        //$fordb1->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);



    }else if($SelectReport == 3){
        $sql = "Select ";
        $sql .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." where agent_type = 'W' ";

        $sql_mobile = "Select ";
        $sql_mobile .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql_mobile .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql_mobile .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." where agent_type = 'M' ";


//		$sql1 = "Select FORMAT(avg(nh00),1), FORMAT(avg(nh01),1), FORMAT(avg(nh02),1), FORMAT(avg(nh03),1), FORMAT(avg(nh04),1), FORMAT(avg(nh05),1), FORMAT(avg(nh06),1), FORMAT(avg(nh07),1), FORMAT(avg(nh08),1), FORMAT(avg(nh09),1), FORMAT(avg(nh10),1), FORMAT(avg(nh11),1), FORMAT(avg(nh12),1), FORMAT(avg(nh13),1), FORMAT(avg(nh14),1), FORMAT(avg(nh15),1), FORMAT(avg(nh16),1), FORMAT(avg(nh17),1), FORMAT(avg(nh18),1), FORMAT(avg(nh19),1), FORMAT(avg(nh20),1), FORMAT(avg(nh21),1), FORMAT(avg(nh22),1), FORMAT(avg(nh23),1) from ".TBL_LOGSTORY_PAGEVIEWTIME." where substring(vdate,1,6) = '".substr($vdate,0,6)."'";

        $sql2 .= "Select ";
        $sql2 .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql2 .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql2 .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." where agent_type = 'W' ";

        $sql2_mobile = "Select ";
        $sql2_mobile .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql2_mobile .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql2_mobile .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." where agent_type = 'M' ";

        $sql3 = "Select ";
        $sql3 .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql3 .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql3 .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." where agent_type = 'W' ";

        $sql3_mobile = "Select ";
        $sql3_mobile .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then ncnt else 0 end) ";
        for($i = 1; $i < $nLoop;$i++){
            $sql3_mobile .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
        }
        $sql3_mobile .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." where agent_type = 'M' ";


        $fordb->query($sql);
        $fordb->fetch(0,"row");
        $selected_date_web_pageview = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_mobile_pageview = $fordb->dt;
        //$fordb1->query($sql1);
        $fordb2->query($sql2);
        $fordb2->fetch(0,"row");
        $selected_date_web_onemonthago_pageview = $fordb2->dt;

        $fordb2->query($sql2_mobile);
        $fordb2->fetch(0,"row");
        $selected_date_mobile_onemonthago_pageview = $fordb2->dt;

        $fordb3->query($sql3);
        $fordb3->fetch(0,"row");
        $selected_date_web_twomonthago_pageview = $fordb3->dt;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $selected_date_mobile_twomonthago_pageview = $fordb3->dt;

        //echo "total:".$fordb->total;
        //$fordb->fetch(0,"row");
        //$fordb1->fetch(0);
        //$fordb2->fetch(0);
        //$fordb3->fetch(0);
    }

//	echo $sql1 ."<br>";
//	echo $sql2 ."<br>";
//	echo $sql3 ."<br>";

    $mstring = "";
    if($SelectReport == 1){

        $mstring .= $mstring.TitleBar("페이지뷰 - PV","일간 : ". getNameOfWeekday(0,$vdate,"dayname"));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";

        if(false){
            $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='basicflot' style='width: 100%; height: 300px; padding: 0px; position: relative;' /></td></tr>\n";
            $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='basicflot2' style='width: 100%; height: 300px; padding: 0px; position: relative;' /></td></tr>\n";
            $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='trackingchart' style='width: 100%; height: 300px; padding: 0px; position: relative;' /></td></tr>\n";
            $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='realtimechart' style='width: 100%; height: 300px; padding: 0px; position: relative;' /></td></tr>\n";
            $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='barchart' style='width: 100%; height: 300px; padding: 0px; position: relative;' /></td></tr>\n";

            $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='piechart' style='width: 100%; height: 300px; padding: 0px; position: relative;'></div></td></tr>\n";
        }
        $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><img src='pageview.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."' ></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=33%>항목</td><td class=m_td width=33%>시간대</td><td class=e_td width=34%>페이지뷰</td></tr>\n";
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
        $mstring .= "	<col width='5%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>

						<col width='10%'>
						<col width='10%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>

						<tr height=30 align=center>
							<td rowspan=2 class=s_td>시간</td>
							<td class=m_td colspan=3 >해당일(페이지뷰)</td>
							<td class=m_td colspan=3 >점유율</td>
							<td class=m_td colspan=2 >체류시간</td>
							<td class=m_td colspan=2 >한달평균</td>
							<td class=m_td colspan=2 >전일</td>
							<td class=e_td colspan=2 >1주전</td>
						</tr>
						<tr height=30 align=center>
							<td class=m_td >계</td>
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td>
							<td class=m_td >계</td>
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td>
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
        $pageview_sum = 0;
        if(is_array($selected_date_web_pageview)){
            $pageview_sum = array_sum($selected_date_web_pageview);
        }
        $mobile_pageview_sum = 0;
        if(is_array($selected_date_mobile_pageview)){
            $mobile_pageview_sum = array_sum($selected_date_mobile_pageview);
        }

        $labels = array("해당일(페이지뷰)","한달평균","전일","1주전");
        $ykeys = array("a","b","c","d");
        //print_r($labels);
        //exit;

        $pageview01 = 0;
        $pageview02 = 0;
        $pageview03 = 0;
        $pageview04 = 0;
        $duration_sum = 0;

        $pageview01_mobile = 0;
        $pageview02_mobile = 0;
        $pageview03_mobile = 0;
        $pageview04_mobile = 0;
        $duration_sum_mobile = 0;

        $minvalue = 0;
        $maxvalue = 0;

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => date("Y-m-d H:00",strtotime($vdate)+(60*60*$i)),
                'a' => ($selected_date_web_pageview[$i]+$selected_date_mobile_pageview[$i]) ,
                'b' => round($selected_date_web_month_avg[$i]+$selected_date_mobile_month_avg[$i]),
                'c' => ($selected_date_web_yesterday_pageview[$i]+$selected_date_mobile_yesterday_pageview[$i]),
                'd'=> ($selected_date_web_onweekago_pageview[$i]+$selected_date_mobile_onweekago_pageview[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">$i</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format($selected_date_web_pageview[$i] + $selected_date_mobile_pageview[$i])."</td>

		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:10px;'>".number_format(returnZeroValue($selected_date_web_pageview[$i]))."</td>	
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_mobile_pageview[$i]))."</td>

		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>";
            if(($pageview_sum+$mobile_pageview_sum) > 0){
                $mstring .= number_format(($selected_date_web_pageview[$i]+$selected_date_mobile_pageview[$i])/($pageview_sum+$mobile_pageview_sum)*100,1);
            }else{
                $mstring .= "0";
            }
            $mstring .= " %</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>";
            if($pageview_sum > 0){
                $mstring .= number_format($selected_date_web_pageview[$i]/$pageview_sum*100,1);
            }else{
                $mstring .= "0";
            }
            $mstring .= " %</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".($mobile_pageview_sum >  0 ?  number_format($selected_date_mobile_pageview[$i]/$mobile_pageview_sum*100,1):0)." %</td>

		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat($selected_date_web_duration[$i],0)."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat($selected_date_mobile_duration[$i],0)."</td>
		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($selected_date_web_month_avg[$i],0)."</td>
		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($selected_date_mobile_month_avg[$i],0)."</td>
		

		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_yesterday_pageview[$i]))."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_mobile_yesterday_pageview[$i]))."</td>

		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_onweekago_pageview[$i]))."</td>
		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_mobile_onweekago_pageview[$i]))."</td>
		</tr>\n";

            $pageview01 += $selected_date_web_pageview[$i];
            $pageview02 += $selected_date_web_month_avg[$i];
            $pageview03 += $selected_date_web_yesterday_pageview[$i];
            $pageview04 += $selected_date_web_onweekago_pageview[$i];
            $duration_sum += $selected_date_web_duration[$i];

            $pageview01_mobile += $selected_date_mobile_pageview[$i];
            $pageview02_mobile += $selected_date_mobile_month_avg[$i];
            $pageview03_mobile += $selected_date_mobile_yesterday_pageview[$i];
            $pageview04_mobile += $selected_date_mobile_onweekago_pageview[$i];
            $duration_sum_mobile += $selected_date_mobile_duration[$i];

            if($minvalue > $selected_date_web_pageview[$i] || $i == 0 ){
                $minvalue = $selected_date_web_pageview[$i];
                $mintime = $i;
            }
            if($maxvalue < $selected_date_web_pageview[$i] || $i == 0 ){
                $maxvalue = $selected_date_web_pageview[$i];
                $maxtime = $i;
            }

        }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
        $mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=15 width=190></td></tr>\n";
        $mstring .= "<tr height=30 align=right>
	<td align=center class=s_td width=30>합계</td>
	<td class='m_td point' style='padding-right:20px;'>".number_format(returnZeroValue($pageview01+$pageview01_mobile))."</td>
	<td class='m_td point' style='padding-right:20px;'>".number_format(returnZeroValue($pageview01))."</td>
	<td class='m_td point' style='padding-right:20px;'>".number_format(returnZeroValue($pageview01_mobile))."</td>
	<td class='m_td' style='padding-right:20px;'>100%</td>
	<td class='m_td' style='padding-right:20px;'>100%</td>
	<td class='m_td' style='padding-right:20px;'>100%</td>
	<td class=m_td style='padding-right:20px;'>".displayTimeFormat($duration_sum)."</td>
	<td class=m_td style='padding-right:20px;'>".displayTimeFormat($duration_sum_mobile)."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview02),0)."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview02_mobile),0)."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview03))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview03_mobile))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview04))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview04_mobile))."</td>
	</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",number_format(returnZeroValue($maxvalue)),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format(returnZeroValue($minvalue)),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getTimeString($maxtime),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getTimeString($mintime),$mstring);


    }else if($SelectReport == 2){

        $mstring = $mstring.TitleBar("페이지뷰 - PV",getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate,'dayname'));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='pageview.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center>
						<td class=s_td width=33%>항목</td>
						<td class=m_td width=33%>요일</td>
						<td class=e_td width=34%>페이지뷰</td>
						
						</tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff id='Report10000'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
		<td align=center onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">{{MAXTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right style='padding-right:20px;' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">{{MAXVALUE}}</td>
		</tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff id='Report10001'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
		<td align=center onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right style='padding-right:20px;' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINVALUE}}</td>
		</tr>\n";
        $mstring .= "</table><br>\n";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
        $mstring .= "<col width='10%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>

						<col width='10%'>
						<col width='10%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<tr height=30 align=center>
							<td class=s_td rowspan=2>요일</td>
							<td class=m_td colspan=3>해당주(페이지뷰)</td>
							<td class=m_td colspan=3>점유율</td>
							<td class=m_td colspan=2>체류시간</td>

							<td class=m_td colspan=2>1주전</td>
							<td class=e_td colspan=2>4주전</td>
						</tr>
						<tr height=30 align=center>
							<td class=m_td >계</td>
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td>
							<td class=m_td >계</td>
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td>
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td>
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td>
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td>
						</tr>\n";
        $pageview_sum = array_sum($selected_date_web_pageview);

        if(is_array($selected_date_mobile_pageview)){
            $pageview_sum_mobile = array_sum($selected_date_mobile_pageview);
        }


        $labels = array("해당주(페이지뷰)","1주전","4주전");
        $ykeys = array("a","b","c");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => date("Y-m-d",strtotime($vdate)+(60*60*24*$i)),
                'a' => ($selected_date_web_pageview[$i]+$selected_date_mobile_pageview[$i]) ,
                'b' => ($selected_date_web_onweekago_pageview[$i]+$selected_date_mobile_onweekago_pageview[$i]),
                'c' => ($selected_date_web_fourweekago_pageview[$i]+$selected_date_mobile_fourweekago_pageview[$i])
            );
            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate,'dayname')."</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_pageview[$i]+$selected_date_mobile_pageview[$i]))."</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_pageview[$i]))."</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_mobile_pageview[$i]))."</td>

		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(($selected_date_web_pageview[$i]+$selected_date_mobile_pageview[$i])/($pageview_sum+$pageview_sum_mobile)*100,1)." %</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($selected_date_web_pageview[$i]/$pageview_sum*100,1)." %</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($selected_date_mobile_pageview[$i]/$pageview_sum_mobile*100,1)." %</td>

		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat($selected_date_web_duration[$i],0)."</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat($selected_date_mobile_duration[$i],0)."</td>

		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_onweekago_pageview[$i]))."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_mobile_onweekago_pageview[$i]))."</td>

		<td  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_fourweekago_pageview[$i]))."</td>
		<td  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_mobile_fourweekago_pageview[$i]))."</td>
		</tr>\n";

            $pageview01 += $selected_date_web_pageview[$i];
            //	$pageview02 += $selected_date_web_month_avg[$i];

            $pageview03 += $selected_date_web_yesterday_pageview[$i];
            $pageview04 += $selected_date_web_onweekago_pageview[$i];
            $duration_sum += $selected_date_web_duration[$i];

            $pageview01_mobile += $selected_date_mobile_pageview[$i];
            //	$pageview02_mobile += $selected_date_mobile_month_avg[$i];

            $pageview03_mobile += $selected_date_mobile_yesterday_pageview[$i];
            $pageview04_mobile += $selected_date_mobile_onweekago_pageview[$i];
            $duration_sum_mobile += $selected_date_mobile_duration[$i];

            if($minvalue > $selected_date_web_pageview[$i] || $i == 0 ){
                $minvalue = $selected_date_web_pageview[$i];
                $mintime = $i;
            }
            if($maxvalue < $selected_date_web_pageview[$i] || $i == 0 ){
                $maxvalue = $selected_date_web_pageview[$i];
                $maxtime = $i;
            }

        }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
        $mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=13 width=190></td></tr>\n";
        $mstring .= "<tr height=30 align=right>
	<td align=center class=s_td >합계</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview01+$pageview01_mobile))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview01))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview01_mobile))."</td>
	<td class='m_td' style='padding-right:20px;'>100%</td>
	<td class='m_td' style='padding-right:20px;'>100%</td>
	<td class='m_td' style='padding-right:20px;'>100%</td>
	<td class=m_td style='padding-right:20px;'>".displayTimeFormat($duration_sum)."</td>
	<td class=m_td style='padding-right:20px;'>".displayTimeFormat($duration_sum_mobile)."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview03))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview03_mobile))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview04))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview04_mobile))."</td>
	</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",number_format(returnZeroValue($maxvalue)),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format(returnZeroValue($minvalue)),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxtime,$vdate),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($mintime,$vdate),$mstring);
    }else if($SelectReport == 3){
        $mstring = $mstring.TitleBar("페이지뷰 - PV",getNameOfWeekday(0,$vdate,"monthname"));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0 >\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='pageview.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=30%>항목</td><td class=m_td width=35%>날짜</td><td class=e_td width=35%>페이지뷰</td></tr>\n";


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
        //$mstring .= "<tr height=30 align=center><td class=s_td width=150>일자</td><td class=m_td width=140>해당일</td><!--td class=m_td width=140>한달평균</td--><td class=m_td width=140>1개월전</td><td class=e_td width=140>2개월전</td></tr>\n";
        $mstring .= "<col width='15%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>

						<col width='10%'>
						<col width='10%'>
						<col width='6%'>
						<col width='6%'>
						<tr height=30 align=center>
							<td class=s_td rowspan=2>일자</td>
							<td class=m_td colspan=3>해당일(페이지뷰)</td>
							<td class=m_td colspan=2>1개월전</td>
							<td class=e_td colspan=2>2개월전</td>
						</tr>
						<tr height=30 align=center>
							<td class=m_td >계</td>
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td>
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td>
							<td class=m_td >웹</td>
							<td class=m_td >모바일</td> 
						</tr>";

        $labels = array("해당일(페이지뷰)","1개월전","2개월전");
        $ykeys = array("a","b","c");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => date("Y-m-d",strtotime(ChangeDate($vdate,"Y-m-d"))+(60*60*24*$i)),
                'a' => ($selected_date_web_pageview[$i]+$selected_date_mobile_pageview[$i]) ,
                'b' => ($selected_date_web_onemonthago_pageview[$i]+$selected_date_mobile_onemonthago_pageview[$i]),
                'c' => ($selected_date_web_towmonthago_pageview[$i]+$selected_date_mobile_towmonthago_pageview[$i])
            );
            $mstring .= "<tr height=30 bgcolor=#ffffff >
		<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate,"monthDay")."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_pageview[$i]+$selected_date_mobile_pageview[$i]))."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_pageview[$i]))."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_mobile_pageview[$i]))."</td>

		 
		<td bgcolor=#efefef  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_onemonthago_pageview[$i]))."</td>
		<td bgcolor=#efefef  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_mobile_onemonthago_pageview[$i]))."</td>

		<td align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_web_towmonthago_pageview[$i]))."</td>
		<td align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($selected_date_mobile_towmonthago_pageview[$i]))."</td>
		</tr>\n";

            $pageview01 += $selected_date_web_pageview[$i];
            $pageview03 += $selected_date_web_onemonthago_pageview[$i];
            $pageview04 += $selected_date_web_towmonthago_pageview[$i];

            $pageview01_mobile += $selected_date_mobile_pageview[$i];
            $pageview03_mobile += $selected_date_mobile_onemonthago_pageview[$i];
            $pageview04_mobile += $selected_date_mobile_towmonthago_pageview[$i];

            if($minvalue > $selected_date_web_pageview[$i] || $i == 0 ){
                $minvalue = $selected_date_web_pageview[$i];
                $minday = $i;
            }
            if($maxvalue < $selected_date_web_pageview[$i] || $i == 0 ){
                $maxvalue = $selected_date_web_pageview[$i];
                $maxday = $i;
            }

        }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
        $mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=8 width=190></td></tr>\n";
        $mstring .= "<tr height=30 align=right>
	<td align=center class=s_td >합계</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview01+$pageview01_mobile))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview01))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview01_mobile))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview03))."</td>
	<td class=m_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview03_mobile))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview04))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(returnZeroValue($pageview04_mobile))."</td>
	</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",number_format(returnZeroValue($maxvalue)),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format(returnZeroValue($minvalue)),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxday,$vdate,"dayname"),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($minday,$vdate,"dayname"),$mstring);
    }

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
        hideHover: true
    });
</script>";

    /*
        $help_text = "
        <table cellpadding=1 cellspacing=0 class='small' width=100%>
            <tr>
                <td style='line-height:150%'>
                - 페리이지뷰란? 해당 일에 쇼핑몰을 방문한 고객들에 의해 발생한 총 페이지 호출 횟수를 집계하여 확인하실 수 있는 리포트입니다.<br>
                - 최다 요청 시간과 최소 요청 시간을 확인하실 수 있으며, 좌측 달력 이미지를 이용하여 주 단위, 월 단위의 페이지 뷰도 확인하실 수 있습니다.<br>
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );

    $mstring .= HelpBox("페이지뷰", $help_text);

    return $mstring;
}


if ($mode == "iframe"){

//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";




    $ca = new Calendar();
    $ca->LinkPage = 'pageview1.php';

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
//	echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;;parent.ChangeCalenderView($SelectReport);</Script>\n";
//	echo "<Script>parent.initTable('MaxViewProductTable');</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 페이지 분석 > 페이지뷰";
    $p->title = "페이지뷰";
    $p->forbizLeftMenu = Stat_munu('pageview1.php');
    $p->forbizContents = ReportTable($vdate."01",$SelectReport);
    $p->PrintReportPage();
}
?>
