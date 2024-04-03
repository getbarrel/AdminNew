<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");

function ReportTable($vdate,$SelectReport=1){
    $mstring ="";
    $sql = "";
    $sql2 = "";
    $sql3 = "";

    $pageview01 = "";
    $pageview02 = "";
    $pageview03 = "";
    $pageview04 = "";

    $pageview01_web = "";
    $pageview01_mobile = "";
    $pageview02_web = "";
    $pageview02_mobile  = "";
    $pageview03_web = "";
    $pageview03_mobile = "";
    $pageview04_web  = "";
    $pageview04_mobile  = "";
    $minvalue = "";
    $maxvalue ="";

    $fordb = new forbizDatabase();
    $fordb1 = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();
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
        $sql = "Select
		case p.nh00 when 0  then 0 else d.nh00/p.nh00 end,
		case p.nh01 when 0  then 0 else d.nh01/p.nh01 end,
		case p.nh02 when 0  then 0 else d.nh02/p.nh02 end,
		case p.nh03 when 0  then 0 else d.nh03/p.nh03 end,
		case p.nh04 when 0  then 0 else d.nh04/p.nh04 end,
		case p.nh05 when 0  then 0 else d.nh05/p.nh05 end,
		case p.nh06 when 0  then 0 else d.nh06/p.nh06 end,
		case p.nh07 when 0  then 0 else d.nh07/p.nh07 end,
		case p.nh08 when 0  then 0 else d.nh08/p.nh08 end,
		case p.nh09 when 0  then 0 else d.nh09/p.nh09 end,
		case p.nh10 when 0  then 0 else d.nh10/p.nh10 end,
		case p.nh11 when 0  then 0 else d.nh11/p.nh11 end,
		case p.nh12 when 0  then 0 else d.nh12/p.nh12 end,
		case p.nh13 when 0  then 0 else d.nh13/p.nh13 end,
		case p.nh14 when 0  then 0 else d.nh14/p.nh14 end,
		case p.nh15 when 0  then 0 else d.nh15/p.nh15 end,
		case p.nh16 when 0  then 0 else d.nh16/p.nh16 end,
		case p.nh17 when 0  then 0 else d.nh17/p.nh17 end,
		case p.nh18 when 0  then 0 else d.nh18/p.nh18 end,
		case p.nh19 when 0  then 0 else d.nh19/p.nh19 end,
		case p.nh20 when 0  then 0 else d.nh20/p.nh20 end,
		case p.nh21 when 0  then 0 else d.nh21/p.nh21 end,
		case p.nh22 when 0  then 0 else d.nh22/p.nh22 end,
		case p.nh23 when 0  then 0 else d.nh23/p.nh23 end
		from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d
		where p.vdate = '$vdate' and p.vdate = d.vdate  ";




        if($fordb1->dbms_type == "oracle"){
            $sql1 = "Select
				case when IFNULL(avg(p.nh00),0) = 0 then 0 else TO_NUMBER(avg(d.nh00)/avg(p.nh00)) end,
				case when IFNULL(avg(p.nh01),0) = 0 then 0 else TO_NUMBER(avg(d.nh01)/avg(p.nh01)) end,
				case when IFNULL(avg(p.nh02),0) = 0 then 0 else TO_NUMBER(avg(d.nh02)/avg(p.nh02)) end,
				case when IFNULL(avg(p.nh03),0) = 0 then 0 else TO_NUMBER(avg(d.nh03)/avg(p.nh03)) end,
				case when IFNULL(avg(p.nh04),0) = 0 then 0 else TO_NUMBER(avg(d.nh04)/avg(p.nh04)) end,
				case when IFNULL(avg(p.nh05),0) = 0 then 0 else TO_NUMBER(avg(d.nh05)/avg(p.nh05)) end,
				case when IFNULL(avg(p.nh06),0) = 0 then 0 else TO_NUMBER(avg(d.nh06)/avg(p.nh06)) end,
				case when IFNULL(avg(p.nh07),0) = 0 then 0 else TO_NUMBER(avg(d.nh07)/avg(p.nh07)) end,
				case when IFNULL(avg(p.nh08),0) = 0 then 0 else TO_NUMBER(avg(d.nh08)/avg(p.nh08)) end,
				case when IFNULL(avg(p.nh09),0) = 0 then 0 else TO_NUMBER(avg(d.nh09)/avg(p.nh09)) end,
				case when IFNULL(avg(p.nh10),0) = 0 then 0 else TO_NUMBER(avg(d.nh10)/avg(p.nh10)) end,
				case when IFNULL(avg(p.nh11),0) = 0 then 0 else TO_NUMBER(avg(d.nh11)/avg(p.nh11)) end,
				case when IFNULL(avg(p.nh12),0) = 0 then 0 else TO_NUMBER(avg(d.nh12)/avg(p.nh12)) end,
				case when IFNULL(avg(p.nh13),0) = 0 then 0 else TO_NUMBER(avg(d.nh13)/avg(p.nh13)) end,
				case when IFNULL(avg(p.nh14),0) = 0 then 0 else TO_NUMBER(avg(d.nh14)/avg(p.nh14)) end,
				case when IFNULL(avg(p.nh15),0) = 0 then 0 else TO_NUMBER(avg(d.nh15)/avg(p.nh15)) end,
				case when IFNULL(avg(p.nh16),0) = 0 then 0 else TO_NUMBER(avg(d.nh16)/avg(p.nh16)) end,
				case when IFNULL(avg(p.nh17),0) = 0 then 0 else TO_NUMBER(avg(d.nh17)/avg(p.nh17)) end,
				case when IFNULL(avg(p.nh18),0) = 0 then 0 else TO_NUMBER(avg(d.nh18)/avg(p.nh18)) end,
				case when IFNULL(avg(p.nh19),0) = 0 then 0 else TO_NUMBER(avg(d.nh19)/avg(p.nh19)) end,
				case when IFNULL(avg(p.nh20),0) = 0 then 0 else TO_NUMBER(avg(d.nh20)/avg(p.nh20)) end,
				case when IFNULL(avg(p.nh21),0) = 0 then 0 else TO_NUMBER(avg(d.nh21)/avg(p.nh21)) end,
				case when IFNULL(avg(p.nh22),0) = 0 then 0 else TO_NUMBER(avg(d.nh22)/avg(p.nh22)) end,
				case when IFNULL(avg(p.nh23),0) = 0 then 0 else TO_NUMBER(avg(d.nh23)/avg(p.nh23)) end

				from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d
				where substr(p.vdate,1,6) = '".substr($vdate,0,6)."'
				and p.vdate = d.vdate";
        }else{
            $sql1 = "Select FORMAT(avg(d.nh00)/avg(p.nh00),1),
				FORMAT(avg(d.nh01)/avg(p.nh01),1),
				FORMAT(avg(d.nh02)/avg(p.nh02),1),
				FORMAT(avg(d.nh03)/avg(p.nh03),1),
				FORMAT(avg(d.nh04)/avg(p.nh04),1),
				FORMAT(avg(d.nh05)/avg(p.nh05),1),
				FORMAT(avg(d.nh06)/avg(p.nh06),1),
				FORMAT(avg(d.nh07)/avg(p.nh07),1),
				FORMAT(avg(d.nh08)/avg(p.nh08),1),
				FORMAT(avg(d.nh09)/avg(p.nh09),1),
				FORMAT(avg(d.nh10)/avg(p.nh10),1),
				FORMAT(avg(d.nh11)/avg(p.nh11),1),
				FORMAT(avg(d.nh12)/avg(p.nh12),1),
				FORMAT(avg(d.nh13)/avg(p.nh13),1),
				FORMAT(avg(d.nh14)/avg(p.nh14),1),
				FORMAT(avg(d.nh15)/avg(p.nh15),1),
				FORMAT(avg(d.nh16)/avg(p.nh16),1),
				FORMAT(avg(d.nh17)/avg(p.nh17),1),
				FORMAT(avg(d.nh18)/avg(p.nh18),1),
				FORMAT(avg(d.nh19)/avg(p.nh19),1),
				FORMAT(avg(d.nh20)/avg(p.nh20),1),
				FORMAT(avg(d.nh21)/avg(p.nh21),1),
				FORMAT(avg(d.nh22)/avg(p.nh22),1),
				FORMAT(avg(d.nh23)/avg(p.nh23),1)
				from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d
				where substr(p.vdate,1,6) = '".substr($vdate,0,6)."'
				and p.vdate = d.vdate";
        }

        $sql2 = "Select
			case p.nh00 when 0  then 0 else d.nh00/p.nh00 end,
			case p.nh01 when 0  then 0 else d.nh01/p.nh01 end,
			case p.nh02 when 0  then 0 else d.nh02/p.nh02 end,
			case p.nh03 when 0  then 0 else d.nh03/p.nh03 end,
			case p.nh04 when 0  then 0 else d.nh04/p.nh04 end,
			case p.nh05 when 0  then 0 else d.nh05/p.nh05 end,
			case p.nh06 when 0  then 0 else d.nh06/p.nh06 end,
			case p.nh07 when 0  then 0 else d.nh07/p.nh07 end,
			case p.nh08 when 0  then 0 else d.nh08/p.nh08 end,
			case p.nh09 when 0  then 0 else d.nh09/p.nh09 end,
			case p.nh10 when 0  then 0 else d.nh10/p.nh10 end,
			case p.nh11 when 0  then 0 else d.nh11/p.nh11 end,
			case p.nh12 when 0  then 0 else d.nh12/p.nh12 end,
			case p.nh13 when 0  then 0 else d.nh13/p.nh13 end,
			case p.nh14 when 0  then 0 else d.nh14/p.nh14 end,
			case p.nh15 when 0  then 0 else d.nh15/p.nh15 end,
			case p.nh16 when 0  then 0 else d.nh16/p.nh16 end,
			case p.nh17 when 0  then 0 else d.nh17/p.nh17 end,
			case p.nh18 when 0  then 0 else d.nh18/p.nh18 end,
			case p.nh19 when 0  then 0 else d.nh19/p.nh19 end,
			case p.nh20 when 0  then 0 else d.nh20/p.nh20 end,
			case p.nh21 when 0  then 0 else d.nh21/p.nh21 end,
			case p.nh22 when 0  then 0 else d.nh22/p.nh22 end,
			case p.nh23 when 0  then 0 else d.nh23/p.nh23 end
			from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d
			where p.vdate = '$vyesterday' and p.vdate = d.vdate";

        $sql3 = "Select
			case p.nh00 when 0  then 0 else d.nh00/p.nh00 end,
			case p.nh01 when 0  then 0 else d.nh01/p.nh01 end,
			case p.nh02 when 0  then 0 else d.nh02/p.nh02 end,
			case p.nh03 when 0  then 0 else d.nh03/p.nh03 end,
			case p.nh04 when 0  then 0 else d.nh04/p.nh04 end,
			case p.nh05 when 0  then 0 else d.nh05/p.nh05 end,
			case p.nh06 when 0  then 0 else d.nh06/p.nh06 end,
			case p.nh07 when 0  then 0 else d.nh07/p.nh07 end,
			case p.nh08 when 0  then 0 else d.nh08/p.nh08 end,
			case p.nh09 when 0  then 0 else d.nh09/p.nh09 end,
			case p.nh10 when 0  then 0 else d.nh10/p.nh10 end,
			case p.nh11 when 0  then 0 else d.nh11/p.nh11 end,
			case p.nh12 when 0  then 0 else d.nh12/p.nh12 end,
			case p.nh13 when 0  then 0 else d.nh13/p.nh13 end,
			case p.nh14 when 0  then 0 else d.nh14/p.nh14 end,
			case p.nh15 when 0  then 0 else d.nh15/p.nh15 end,
			case p.nh16 when 0  then 0 else d.nh16/p.nh16 end,
			case p.nh17 when 0  then 0 else d.nh17/p.nh17 end,
			case p.nh18 when 0  then 0 else d.nh18/p.nh18 end,
			case p.nh19 when 0  then 0 else d.nh19/p.nh19 end,
			case p.nh20 when 0  then 0 else d.nh20/p.nh20 end,
			case p.nh21 when 0  then 0 else d.nh21/p.nh21 end,
			case p.nh22 when 0  then 0 else d.nh22/p.nh22 end,
			case p.nh23 when 0  then 0 else d.nh23/p.nh23 end
			from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d
			where p.vdate = '$voneweekago' and p.vdate = d.vdate";

        $sql_web = $sql." and p.agent_type = 'W' and d.agent_type = 'W' ";
        $sql_mobile = $sql." and p.agent_type = 'M' and d.agent_type = 'M' ";

        $fordb->query($sql_web);
        $fordb->fetch(0,"row");
        $selected_date_web_data = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_mobile_data = $fordb->dt;

        $sql1_web = $sql1." and p.agent_type = 'W' and d.agent_type = 'W' ";
        $sql1_mobile = $sql1." and p.agent_type = 'M' and d.agent_type = 'M' ";

        $fordb1->query($sql1_web);
        $fordb1->fetch(0,"row");
        $month_avg_web_data = $fordb1->dt;

        $fordb1->query($sql1_mobile);
        $fordb1->fetch(0,"row");
        $month_avg_mobile_data = $fordb1->dt;

        $sql2_web = $sql2." and p.agent_type = 'W' and d.agent_type = 'W' ";
        $sql2_mobile = $sql2." and p.agent_type = 'M' and d.agent_type = 'M' ";

        $fordb2->query($sql2_web);
        $fordb2->fetch(0,"row");
        $one_ago_web_data = $fordb2->dt;

        $fordb2->query($sql2_mobile);
        $fordb2->fetch(0,"row");
        $one_ago_mobile_data = $fordb2->dt;

        $sql3_web = $sql3." and p.agent_type = 'W' and d.agent_type = 'W' ";
        $sql3_mobile = $sql3." and p.agent_type = 'M' and d.agent_type = 'M' ";

        $fordb3->query($sql3_web);
        $fordb3->fetch(0,"row");
        $two_ago_web_data = $fordb3->dt;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $two_ago_mobile_data = $fordb3->dt;

        //echo "total:".$fordb->total;
        //$fordb->fetch(0);
        //$fordb1->fetch(0);
        //$fordb2->fetch(0);
        //$fordb3->fetch(0);
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }
        $sql .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then d.nduration else 0 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then p.ncnt else 1 end)";
        for($i=0;$i<7;$i++){
            $sql .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)."' then d.nduration else 1 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)."' then p.ncnt else 1 end)";
        }
        $sql .= " from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d
				where p.vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."'
				and d.vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."'";





        $sql2 .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then d.nduration else 1 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then p.ncnt else 1 end)";
        for($i=0;$i<7;$i++){
            $sql2 .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*$i)."' then d.nduration else 1 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*$i)."' then p.ncnt else 1 end)";
        }
        $sql2 .= " from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d
				where p.vdate between '$voneweekago' and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."'
				and d.vdate between '$voneweekago' and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."'";


        $sql3 .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then d.nduration else 1 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then p.ncnt else 1 end)";
        for($i=0;$i<7;$i++){
            $sql3 .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*$i)."' then d.nduration else 1 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*$i)."' then p.ncnt else 1 end)";
        }
        $sql3 .= " from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d
				where p.vdate between '$vfourweekago' and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."'
				and d.vdate between '$vfourweekago' and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."'";

        $sql_web = $sql." and p.agent_type = 'W' and d.agent_type = 'W' ";
        $sql_mobile = $sql." and p.agent_type = 'M' and d.agent_type = 'M' ";

        $fordb->query($sql_web);
        $fordb->fetch(0,"row");
        $selected_date_web_data = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_mobile_data = $fordb->dt;

        $sql2_web = $sql2." and p.agent_type = 'W' and d.agent_type = 'W' ";
        $sql2_mobile = $sql2." and p.agent_type = 'M' and d.agent_type = 'M' ";

        $fordb2->query($sql2_web);
        $fordb2->fetch(0,"row");
        $one_ago_web_data = $fordb2->dt;

        $fordb2->query($sql2_mobile);
        $fordb2->fetch(0,"row");
        $one_ago_mobile_data = $fordb2->dt;

        $sql3_web = $sql3." and p.agent_type = 'W' and d.agent_type = 'W' ";
        $sql3_mobile = $sql3." and p.agent_type = 'M' and d.agent_type = 'M' ";

        $fordb3->query($sql3_web);
        $fordb3->fetch(0,"row");
        $two_ago_web_data = $fordb3->dt;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $two_ago_mobile_data = $fordb3->dt;

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            //echo $search_sdate;
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }

    }else if($SelectReport == 3){
        $sql .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then d.nduration else 0 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then p.ncnt else 1 end)";
        for($i = 1; $i < $nLoop;$i++){
            $sql .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then d.nduration else 0 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then p.ncnt else 1 end)";
        }
        $sql .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d  where p.vdate LIKE '".substr($vdate,0,6)."%' and d.vdate LIKE '".substr($vdate,0,6)."%' ";


        $sql2 .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then d.nduration else 0 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then p.ncnt else 1 end)";

        for($i = 1; $i < $nLoop;$i++){
            $sql2 .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then d.nduration else 0 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then p.ncnt else 1 end)";
        }
        $sql2 .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d  where p.vdate LIKE '".substr($vonemonthago,0,6)."%' and d.vdate LIKE '".substr($vonemonthago,0,6)."%'  ";

        $sql3 .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then d.nduration else 0 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then p.ncnt else 1 end)";
        for($i = 1; $i < $nLoop;$i++){
            $sql3 .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then d.nduration else 0 end)/sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then p.ncnt else 1 end)";
        }
        $sql3 .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." p, ".TBL_LOGSTORY_DURATIONTIME." d  where p.vdate LIKE '".substr($vtwomonthago,0,6)."%' and d.vdate LIKE '".substr($vtwomonthago,0,6)."%'  ";


        $sql_web = $sql." and p.agent_type = 'W' and d.agent_type = 'W' ";
        $sql_mobile = $sql." and p.agent_type = 'M' and d.agent_type = 'M' ";

        $fordb->query($sql_web);

        $fordb->fetch(0,"row");
        $selected_date_web_data = $fordb->dt;

        $fordb->query($sql_mobile);
        $fordb->fetch(0,"row");
        $selected_date_mobile_data = $fordb->dt;


        $sql2_web = $sql2." and p.agent_type = 'W' and d.agent_type = 'W' ";
        $sql2_mobile = $sql2." and p.agent_type = 'M' and d.agent_type = 'M' ";

        $fordb2->query($sql2_web);
        $fordb2->fetch(0,"row");
        $one_ago_web_data = $fordb2->dt;

        $fordb2->query($sql2_mobile);
        $fordb2->fetch(0,"row");
        $one_ago_mobile_data = $fordb2->dt;

        $sql3_web = $sql3." and p.agent_type = 'W' and d.agent_type = 'W' ";
        $sql3_mobile = $sql3." and p.agent_type = 'M' and d.agent_type = 'M' ";

        $fordb3->query($sql3_web);
        $fordb3->fetch(0,"row");
        $two_ago_web_data = $fordb3->dt;

        $fordb3->query($sql3_mobile);
        $fordb3->fetch(0,"row");
        $two_ago_mobile_data = $fordb3->dt;
    }

//	echo $sql1 ."<br>";
//	echo $sql2 ."<br>";
//	echo $sql3 ."<br>";


    if($SelectReport == 1){

        $mstring = $mstring.TitleBar("페이지뷰 당 체류시간 ","일간 : ". getNameOfWeekday(0,$vdate,"dayname"));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='durationbypageview.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=33%>항목</td><td class=m_td width=33%>시간대</td><td class=e_td width=34%>평균 체류시간</td></tr>\n";
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
        $mstring .= "<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<tr height=30 align=center>
							<td class=s_td rowspan=2>시간</td>
							<td class=m_td colspan=3>해당일</td>
							<td class=m_td colspan=3>한달평균</td>
							<td class=m_td colspan=2>전일</td>
							<td class=e_td colspan=2>1주전 </td>
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
						</tr>
						\n";

        $labels = array("해당일(페이지뷰)","한달평균","전일","1주전");
        $ykeys = array("a","b","c","d");


        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => date("Y-m-d H:00",strtotime($vdate)+(60*60*$i)),
                'a' => ($selected_date_web_data[$i]+$selected_date_mobile_data[$i]) ,
                'b' => round($month_avg_web_data[$i]+$month_avg_mobile_data[$i]),
                'c' => ($one_ago_web_data[$i]+$one_ago_mobile_data[$i]),
                'd' => ($two_ago_web_data[$i]+$two_ago_mobile_data[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">$i</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($selected_date_web_data[$i]+$selected_date_mobile_data[$i]))."</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($selected_date_web_data[$i]))."</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($selected_date_mobile_data[$i]))."</td>

		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($month_avg_web_data[$i]+$month_avg_mobile_data[$i]))."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($month_avg_web_data[$i]))."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($month_avg_mobile_data[$i]))."</td>

		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($one_ago_web_data[$i]))."</td>
		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($one_ago_mobile_data[$i]))."</td>
		<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($two_ago_web_data[$i]))."</td>
		<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($two_ago_mobile_data[$i]))."</td>
		</tr>\n";

            $pageview01_web = $pageview01_web + returnZeroValue($selected_date_web_data[$i]);
            $pageview01_mobile = $pageview01_mobile + returnZeroValue($selected_date_mobile_data[$i]);
            $pageview02_web = $pageview02_web + returnZeroValue($month_avg_web_data[$i]);
            $pageview02_mobile = $pageview02_mobile + returnZeroValue($month_avg_mobile_data[$i]);

            $pageview03_web = $pageview03_web + returnZeroValue($one_ago_web_data[$i]);
            $pageview03_mobile = $pageview03_mobile + returnZeroValue($one_ago_mobile_data[$i]);

            $pageview04_web = $pageview04_web + returnZeroValue($two_ago_web_data[$i]);
            $pageview04_mobile = $pageview04_mobile + returnZeroValue($two_ago_mobile_data[$i]);

            if($minvalue > $selected_date_web_data[$i] || $i == 0 ){
                $minvalue = $selected_date_web_data[$i];
                $mintime = $i;
            }
            if($maxvalue < $selected_date_web_data[$i] || $i == 0 ){
                $maxvalue = $selected_date_web_data[$i];
                $maxtime = $i;
            }

        }
        $mstring .= "</table>\n";
        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>
	<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>\n";

        $mstring .= "<tr height=30 align=right>
								<td width=50 class=s_td  style='padding-right:20px'>합계</td>
								<td class=m_td style='padding-right:20px'>".displayTimeFormat($pageview01_web+$pageview01_mobile,2)."</td>
								<td class=m_td style='padding-right:20px'>".displayTimeFormat($pageview01_web,2)."</td>
								<td class=m_td style='padding-right:20px'>".displayTimeFormat($pageview01_mobile,2)."</td>

								<td class=m_td style='padding-right:20px'>".displayTimeFormat($pageview02_web+$pageview02_mobile,2)."</td>
								<td class=m_td style='padding-right:20px'>".displayTimeFormat($pageview02_web,2)."</td>
								<td class=m_td style='padding-right:20px'>".displayTimeFormat($pageview02_mobile,2)."</td>

								<td class=m_td style='padding-right:20px'>".displayTimeFormat($pageview03_web,2)."</td>
								<td class=m_td style='padding-right:20px'>".displayTimeFormat($pageview03_mobile,2)."</td>
								<td class=e_td style='padding-right:20px'>".displayTimeFormat($pageview04_web,2)."</td>
								<td class=e_td style='padding-right:20px'>".displayTimeFormat($pageview04_mobile,2)."</td>
								</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",displayTimeFormat(returnZeroValue($maxvalue)),$mstring);
        $mstring = str_replace("{{MINVALUE}}",displayTimeFormat(returnZeroValue($minvalue)),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getTimeString($maxtime),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getTimeString($mintime),$mstring);


    }else if($SelectReport == 2 || $SelectReport == 4){
        $mstring = $mstring.TitleBar("페이지뷰 당 체류시간",$dateString);
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='durationbypageview.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='line-chart' style='height: 300px; position: relative;'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center>
					<td class=s_td width=33%>항목</td>
					<td class=m_td width=33%>시간대</td>
					<td class=e_td width=34%>평균 체류시간</td>
					</tr>
					\n";
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
        $mstring .= "<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<tr height=30 align=center>
			<td class=s_td rowspan=2>요일</td>
			<td class=m_td colspan=3>해당주</td>
			<td class=m_td colspan=2>1주전 </td>
			<td class=e_td colspan=2>4주전 </td>
			</tr>
			<tr height=30 align=center>
				<td class=m_td >계</td>
				<td class=m_td >웹</td>
				<td class=m_td >모바일</td>
				<td class=m_td >웹</td>
				<td class=m_td >모바일</td>
				<td class=m_td >웹</td>
				<td class=m_td >모바일</td>
			</tr>
						\n";

        $labels = array("해당주(페이지뷰)","1주전","4주전");
        $ykeys = array("a","b","c");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => date("Y-m-d",strtotime($vdate)+(60*60*24*$i)),
                'a' => ($selected_date_web_data[$i]+$selected_date_mobile_data[$i]) ,
                'b' => ($one_ago_web_data[$i]+$one_ago_mobile_data[$i]),
                'c' => ($two_ago_web_data[$i]+$two_ago_mobile_data[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff >
		<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate)."</td>
		<td align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($selected_date_web_data[$i]+$selected_date_mobile_data[$i]))."</td>
		<td align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($selected_date_web_data[$i]))."</td>
		<td align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($selected_date_mobile_data[$i]))."</td>

		<td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($one_ago_web_data[$i]))."</td>
		<td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($one_ago_mobile_data[$i]))."</td>
		<td  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($two_ago_web_data[$i]))."</td>
		<td  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($two_ago_mobile_data[$i]))."</td>
		</tr>\n";

            $pageview01 = $pageview01 + returnZeroValue($selected_date_web_data[$i]);
//		$pageview02 = $pageview02 + returnZeroValue($month_avg_web_data[$i]);
            $pageview03 = $pageview03 + returnZeroValue($one_ago_web_data[$i]);
            $pageview04 = $pageview04 + returnZeroValue($two_ago_web_data[$i]);

            if($minvalue > $selected_date_web_data[$i] || $i == 0 ){
                $minvalue = $selected_date_web_data[$i];
                $mintime = $i;
            }
            if($maxvalue < $selected_date_web_data[$i] || $i == 0 ){
                $maxvalue = $selected_date_web_data[$i];
                $maxtime = $i;
            }

        }
        $mstring .= "</table>\n";
        /*
            $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
            $mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
            $mstring .= "<tr height=30 align=right>
            <td class=s_td align=center>합계</td>
            <td class=m_td style='padding-right:20px;'>".displayTimeFormat($pageview01)."</td>
            <td class=m_td style='padding-right:20px;'>".displayTimeFormat($pageview03)."</td>
            <td class=e_td style='padding-right:20px;'>".displayTimeFormat($pageview04)."</td>
            </tr>\n";
            $mstring .= "</table>\n";
        */
        $mstring = str_replace("{{MAXVALUE}}",displayTimeFormat(returnZeroValue($maxvalue)),$mstring);
        $mstring = str_replace("{{MINVALUE}}",displayTimeFormat(returnZeroValue($minvalue)),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxtime,$vdate),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($mintime,$vdate),$mstring);
    }else if($SelectReport == 3){
        $mstring = $mstring.TitleBar("페이지뷰 당 체류시간",getNameOfWeekday(0,$vdate,"monthname"));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='durationbypageview.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."' ></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='line-chart' style='height: 300px; position: relative;'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=33%>항목</td><td class=m_td width=33%>시간대</td><td class=e_td width=34%>평균 체류시간</td></tr>\n";
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
        $mstring .= "<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<tr height=30 align=center>
			<td class=s_td rowspan=2>날짜</td>
			<td class=m_td colspan=3>해당월</td>
			<td class=m_td colspan=2>1개월전 </td>
			<td class=e_td colspan=2>2개월전 </td>
			</tr>
			<tr height=30 align=center>
				<td class=m_td >계</td>
				<td class=m_td >웹</td>
				<td class=m_td >모바일</td>
				<td class=m_td >웹</td>
				<td class=m_td >모바일</td>
				<td class=m_td >웹</td>
				<td class=m_td >모바일</td>
			</tr>\n";

        $labels = array("해당일(페이지뷰)","1개월전","2개월전");
        $ykeys = array("a","b","c");


        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => date("Y-m-d",strtotime(ChangeDate($vdate,"Y-m-d"))+(60*60*24*$i)),
                'a' => ($selected_date_web_data[$i]+$selected_date_mobile_data[$i]) ,
                'b' => ($one_ago_web_data[$i]+$one_ago_mobile_data[$i]),
                'c' => ($two_ago_web_data[$i]+$two_ago_mobile_data[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff >
		<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate,"dayname")."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($selected_date_web_data[$i]+$selected_date_mobile_data[$i]),2)."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($selected_date_web_data[$i]),2)."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($selected_date_mobile_data[$i]),2)."</td>

		<td bgcolor=#efefef  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($one_ago_web_data[$i]),2)."</td>
		<td bgcolor=#efefef  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($one_ago_mobile_data[$i]),2)."</td>

		<td  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($two_ago_web_data[$i]),2)."</td>
		<td  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($two_ago_mobile_data[$i]),2)."</td>
		</tr>\n";

            $pageview01_web = $pageview01_web + returnZeroValue($selected_date_web_data[$i]);
            $pageview01_mobile = $pageview01_mobile + returnZeroValue($selected_date_mobile_data[$i]);
//		$pageview02 = $pageview02 + returnZeroValue($month_avg_web_data[$i]);
            $pageview03_web = $pageview03_web + returnZeroValue($one_ago_web_data[$i]);
            $pageview03_mobile = $pageview03_mobile + returnZeroValue($one_ago_mobile_data[$i]);

            $pageview04_web = $pageview04_web + returnZeroValue($two_ago_web_data[$i]);
            $pageview04_mobile = $pageview04_mobile + returnZeroValue($two_ago_mobile_data[$i]);

            if($minvalue > $selected_date_web_data[$i] || $i == 0 ){
                $minvalue = $selected_date_web_data[$i];
                $minday = $i;
            }
            if($maxvalue < $selected_date_web_data[$i] || $i == 0 ){
                $maxvalue = $selected_date_web_data[$i];
                $maxday = $i;
            }

        }
        $mstring .= "</table>\n";
        /*
            $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
            $mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
            $mstring .= "<tr height=30 align=right>
            <td class=s_td align=center>합계</td>
            <td class=m_td style='padding-right:20px;'>".displayTimeFormat($pageview01,2)."</td>
            <td class=m_td style='padding-right:20px;'>".displayTimeFormat($pageview03,2)."</td>
            <td class=e_td style='padding-right:20px;'>".displayTimeFormat($pageview04,2)."</td>
            </tr>\n";
            $mstring .= "</table>\n";
        */
        $mstring = str_replace("{{MAXVALUE}}",displayTimeFormat(returnZeroValue($maxvalue)),$mstring);
        $mstring = str_replace("{{MINVALUE}}",displayTimeFormat(returnZeroValue($minvalue)),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxday,$vdate,"dayname"),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($minday,$vdate,"dayname"),$mstring);
    }


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
			hideHover: true
		});
	</script>";

    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 페이지뷰당 체류시간이란 ? 방문자가 1페이지를 보는데 소요되는 평균시간을 말합니다<br>
                - 페이지뷰당 체류시간을 시간대별로 보여주고 있으며, 주간리포트는 요일별, 월간리포트는 일자별로 제공됩니다<br>
                - 리포트는 해당일에 대한 수치 뿐만아니라  한달평균, 전일, 1주전을 비교하여 제공하고 있으며 이는 현재일의 수치를 다른 값들과 비교하여 리포트를 이해하시는데 도움이 됩니다
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );

    $mstring .= HelpBox("페이지뷰당 체류시간", $help_text);
    return $mstring;
}
if ($mode == "iframe"){

//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";

    $ca = new Calendar();
    $ca->LinkPage = 'durationbypageview.php';

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
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 페이지 분석 > 페이지뷰당 체류시간";
    $p->title = "페이지뷰당 체류시간";
    $p->forbizLeftMenu = Stat_munu('durationbypageview.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}
?>
