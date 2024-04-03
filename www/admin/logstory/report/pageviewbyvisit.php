<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");

function ReportTable($vdate,$SelectReport=1){
    $fordb = new forbizDatabase();
    $fordb1 = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();

    $sql = "";
    $sql2 = "";
    $sql3 = "";
    $mstring ="";
    $pageview01 = "";
    $pageview02 = "";
    $pageview03 = "";
    $pageview04 = "";
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
//echo $SelectReport;
    if($SelectReport == 1){
        $sql = "Select case when IFNULL(v.nh00,0) = 0 then 0 else p.nh00/v.nh00 end,
			case when IFNULL(v.nh01,0) = 0 then 0 else p.nh01/v.nh01 end,
			case when IFNULL(v.nh02,0) = 0 then 0 else p.nh02/v.nh02 end,
			case when IFNULL(v.nh03,0) = 0 then 0 else p.nh03/v.nh03 end,
			case when IFNULL(v.nh04,0) = 0 then 0 else p.nh04/v.nh04 end,
			case when IFNULL(v.nh05,0) = 0 then 0 else p.nh05/v.nh05 end,
			case when IFNULL(v.nh06,0) = 0 then 0 else p.nh06/v.nh06 end,
			case when IFNULL(v.nh07,0) = 0 then 0 else p.nh07/v.nh07 end,
			case when IFNULL(v.nh08,0) = 0 then 0 else p.nh08/v.nh08 end,
			case when IFNULL(v.nh09,0) = 0 then 0 else p.nh09/v.nh09 end,
			case when IFNULL(v.nh10,0) = 0 then 0 else p.nh10/v.nh10 end,
			case when IFNULL(v.nh11,0) = 0 then 0 else p.nh11/v.nh11 end,
			case when IFNULL(v.nh12,0) = 0 then 0 else p.nh12/v.nh12 end,
			case when IFNULL(v.nh13,0) = 0 then 0 else p.nh13/v.nh13 end,
			case when IFNULL(v.nh14,0) = 0 then 0 else p.nh14/v.nh14 end,
			case when IFNULL(v.nh15,0) = 0 then 0 else p.nh15/v.nh15 end,
			case when IFNULL(v.nh16,0) = 0 then 0 else p.nh16/v.nh16 end,
			case when IFNULL(v.nh17,0) = 0 then 0 else p.nh17/v.nh17 end,
			case when IFNULL(v.nh18,0) = 0 then 0 else p.nh18/v.nh18 end,
			case when IFNULL(v.nh19,0) = 0 then 0 else p.nh19/v.nh19 end,
			case when IFNULL(v.nh20,0) = 0 then 0 else p.nh20/v.nh20 end,
			case when IFNULL(v.nh21,0) = 0 then 0 else p.nh21/v.nh21 end,
			case when IFNULL(v.nh22,0) = 0 then 0 else p.nh22/v.nh22 end,
			case when IFNULL(v.nh23,0) = 0 then 0 else p.nh23/v.nh23 end
			from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p
			where v.vdate = '$vdate' and v.vdate = p.vdate";
        if($fordb1->dbms_type == "oracle"){
            $sql1 = "Select
				case when IFNULL(sum(v.nh00),0) = 0 then 0 else TO_NUMBER(avg(p.nh00)/avg(v.nh00)) end,
				case when IFNULL(sum(v.nh01),0) = 0 then 0 else TO_NUMBER(avg(p.nh01)/avg(v.nh01)) end,
				case when IFNULL(sum(v.nh02),0) = 0 then 0 else TO_NUMBER(avg(p.nh02)/avg(v.nh02)) end,
				case when IFNULL(sum(v.nh03),0) = 0 then 0 else TO_NUMBER(avg(p.nh03)/avg(v.nh03)) end,
				case when IFNULL(sum(v.nh04),0) = 0 then 0 else TO_NUMBER(avg(p.nh04)/avg(v.nh04)) end,
				case when IFNULL(sum(v.nh05),0) = 0 then 0 else TO_NUMBER(avg(p.nh05)/avg(v.nh05)) end,
				case when IFNULL(sum(v.nh06),0) = 0 then 0 else TO_NUMBER(avg(p.nh06)/avg(v.nh06)) end,
				case when IFNULL(sum(v.nh07),0) = 0 then 0 else TO_NUMBER(avg(p.nh07)/avg(v.nh07)) end,
				case when IFNULL(sum(v.nh08),0) = 0 then 0 else TO_NUMBER(avg(p.nh08)/avg(v.nh08)) end,
				case when IFNULL(sum(v.nh09),0) = 0 then 0 else TO_NUMBER(avg(p.nh09)/avg(v.nh09)) end,
				case when IFNULL(sum(v.nh10),0) = 0 then 0 else TO_NUMBER(avg(p.nh10)/avg(v.nh10)) end,
				case when IFNULL(sum(v.nh11),0) = 0 then 0 else TO_NUMBER(avg(p.nh11)/avg(v.nh11)) end,
				case when IFNULL(sum(v.nh12),0) = 0 then 0 else TO_NUMBER(avg(p.nh12)/avg(v.nh12)) end,
				case when IFNULL(sum(v.nh13),0) = 0 then 0 else TO_NUMBER(avg(p.nh13)/avg(v.nh13)) end,
				case when IFNULL(sum(v.nh14),0) = 0 then 0 else TO_NUMBER(avg(p.nh14)/avg(v.nh14)) end,
				case when IFNULL(sum(v.nh15),0) = 0 then 0 else TO_NUMBER(avg(p.nh15)/avg(v.nh15)) end,
				case when IFNULL(sum(v.nh16),0) = 0 then 0 else TO_NUMBER(avg(p.nh16)/avg(v.nh16)) end,
				case when IFNULL(sum(v.nh17),0) = 0 then 0 else TO_NUMBER(avg(p.nh17)/avg(v.nh17)) end,
				case when IFNULL(sum(v.nh18),0) = 0 then 0 else TO_NUMBER(avg(p.nh18)/avg(v.nh18)) end,
				case when IFNULL(sum(v.nh19),0) = 0 then 0 else TO_NUMBER(avg(p.nh19)/avg(v.nh19)) end,
				case when IFNULL(sum(v.nh20),0) = 0 then 0 else TO_NUMBER(avg(p.nh20)/avg(v.nh20)) end,
				case when IFNULL(sum(v.nh21),0) = 0 then 0 else TO_NUMBER(avg(p.nh21)/avg(v.nh21)) end,
				case when IFNULL(sum(v.nh22),0) = 0 then 0 else TO_NUMBER(avg(p.nh22)/avg(v.nh22)) end,
				case when IFNULL(sum(v.nh23),0) = 0 then 0 else TO_NUMBER(avg(p.nh23)/avg(v.nh23)) end
			from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p
			where substr(v.vdate,1,6) = '".substr($vdate,0,6)."'  and v.vdate = p.vdate";
        }else{
            $sql1 = "Select FORMAT(avg(p.nh00)/avg(v.nh00),1), FORMAT(avg(p.nh01)/avg(v.nh01),1), FORMAT(avg(p.nh02)/avg(v.nh02),1), FORMAT(avg(p.nh03)/avg(v.nh03),1), FORMAT(avg(p.nh04)/avg(v.nh04),1), FORMAT(avg(p.nh05)/avg(v.nh05),1),
				FORMAT(avg(p.nh06)/avg(v.nh06),1), FORMAT(avg(p.nh07)/avg(v.nh07),1), FORMAT(avg(p.nh08)/avg(v.nh08),1), FORMAT(avg(p.nh09)/avg(v.nh09),1), FORMAT(avg(p.nh10)/avg(v.nh10),1), FORMAT(avg(p.nh11)/avg(v.nh11),1),
				FORMAT(avg(p.nh12)/avg(v.nh12),1), FORMAT(avg(p.nh13)/avg(v.nh13),1), FORMAT(avg(p.nh14)/avg(v.nh14),1), FORMAT(avg(p.nh15)/avg(v.nh15),1), FORMAT(avg(p.nh16)/avg(v.nh16),1), FORMAT(avg(p.nh17)/avg(v.nh17),1),
				FORMAT(avg(p.nh18)/avg(v.nh18),1), FORMAT(avg(p.nh19)/avg(v.nh19),1), FORMAT(avg(p.nh20)/avg(v.nh20),1), FORMAT(avg(p.nh21)/avg(v.nh21),1), FORMAT(avg(p.nh22)/avg(v.nh22),1), FORMAT(avg(p.nh23)/avg(v.nh23),1)
				from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p where substring(v.vdate,1,6) = '".substr($vdate,0,6)."'  and v.vdate = p.vdate";
        }
        /*
                $sql2 = "Select p.nh00/v.nh00, p.nh01/v.nh01, p.nh02/v.nh02, p.nh03/v.nh03, p.nh04/v.nh04, p.nh05/v.nh05, p.nh06/v.nh06,
                    p.nh07/v.nh07, p.nh08/v.nh08, p.nh09/v.nh09, p.nh10/v.nh10, p.nh11/v.nh11, p.nh12/v.nh12, p.nh13/v.nh13, p.nh14/v.nh14,
                    p.nh15/v.nh15, p.nh16/v.nh16, p.nh17/v.nh17, p.nh18/v.nh18, p.nh19/v.nh19, p.nh20/v.nh20, p.nh21/v.nh21, p.nh22/v.nh22,
                    p.nh23/v.nh23 from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p where v.vdate = '$vyesterday' and v.vdate = p.vdate";
        */
        $sql2 = "Select case when IFNULL(v.nh00,0) = 0 then 0 else p.nh00/v.nh00 end,
			case when IFNULL(v.nh01,0) = 0 then 0 else p.nh01/v.nh01 end,
			case when IFNULL(v.nh02,0) = 0 then 0 else p.nh02/v.nh02 end,
			case when IFNULL(v.nh03,0) = 0 then 0 else p.nh03/v.nh03 end,
			case when IFNULL(v.nh04,0) = 0 then 0 else p.nh04/v.nh04 end,
			case when IFNULL(v.nh05,0) = 0 then 0 else p.nh05/v.nh05 end,
			case when IFNULL(v.nh06,0) = 0 then 0 else p.nh06/v.nh06 end,
			case when IFNULL(v.nh07,0) = 0 then 0 else p.nh07/v.nh07 end,
			case when IFNULL(v.nh08,0) = 0 then 0 else p.nh08/v.nh08 end,
			case when IFNULL(v.nh09,0) = 0 then 0 else p.nh09/v.nh09 end,
			case when IFNULL(v.nh10,0) = 0 then 0 else p.nh10/v.nh10 end,
			case when IFNULL(v.nh11,0) = 0 then 0 else p.nh11/v.nh11 end,
			case when IFNULL(v.nh12,0) = 0 then 0 else p.nh12/v.nh12 end,
			case when IFNULL(v.nh13,0) = 0 then 0 else p.nh13/v.nh13 end,
			case when IFNULL(v.nh14,0) = 0 then 0 else p.nh14/v.nh14 end,
			case when IFNULL(v.nh15,0) = 0 then 0 else p.nh15/v.nh15 end,
			case when IFNULL(v.nh16,0) = 0 then 0 else p.nh16/v.nh16 end,
			case when IFNULL(v.nh17,0) = 0 then 0 else p.nh17/v.nh17 end,
			case when IFNULL(v.nh18,0) = 0 then 0 else p.nh18/v.nh18 end,
			case when IFNULL(v.nh19,0) = 0 then 0 else p.nh19/v.nh19 end,
			case when IFNULL(v.nh20,0) = 0 then 0 else p.nh20/v.nh20 end,
			case when IFNULL(v.nh21,0) = 0 then 0 else p.nh21/v.nh21 end,
			case when IFNULL(v.nh22,0) = 0 then 0 else p.nh22/v.nh22 end,
			case when IFNULL(v.nh23,0) = 0 then 0 else p.nh23/v.nh23 end
			from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p
			 where v.vdate = '$vyesterday' and v.vdate = p.vdate ";
        /*
                $sql3 = "Select p.nh00/v.nh00, p.nh01/v.nh01, p.nh02/v.nh02, p.nh03/v.nh03, p.nh04/v.nh04, p.nh05/v.nh05, p.nh06/v.nh06,
                    p.nh07/v.nh07, p.nh08/v.nh08, p.nh09/v.nh09, p.nh10/v.nh10, p.nh11/v.nh11, p.nh12/v.nh12, p.nh13/v.nh13, p.nh14/v.nh14,
                    p.nh15/v.nh15, p.nh16/v.nh16, p.nh17/v.nh17, p.nh18/v.nh18, p.nh19/v.nh19, p.nh20/v.nh20, p.nh21/v.nh21, p.nh22/v.nh22,
                    p.nh23/v.nh23 from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p where v.vdate = '$voneweekago' and v.vdate = p.vdate";
        */
        $sql3 = "Select case when IFNULL(v.nh00,0) = 0 then 0 else p.nh00/v.nh00 end,
			case when IFNULL(v.nh01,0) = 0 then 0 else p.nh01/v.nh01 end,
			case when IFNULL(v.nh02,0) = 0 then 0 else p.nh02/v.nh02 end,
			case when IFNULL(v.nh03,0) = 0 then 0 else p.nh03/v.nh03 end,
			case when IFNULL(v.nh04,0) = 0 then 0 else p.nh04/v.nh04 end,
			case when IFNULL(v.nh05,0) = 0 then 0 else p.nh05/v.nh05 end,
			case when IFNULL(v.nh06,0) = 0 then 0 else p.nh06/v.nh06 end,
			case when IFNULL(v.nh07,0) = 0 then 0 else p.nh07/v.nh07 end,
			case when IFNULL(v.nh08,0) = 0 then 0 else p.nh08/v.nh08 end,
			case when IFNULL(v.nh09,0) = 0 then 0 else p.nh09/v.nh09 end,
			case when IFNULL(v.nh10,0) = 0 then 0 else p.nh10/v.nh10 end,
			case when IFNULL(v.nh11,0) = 0 then 0 else p.nh11/v.nh11 end,
			case when IFNULL(v.nh12,0) = 0 then 0 else p.nh12/v.nh12 end,
			case when IFNULL(v.nh13,0) = 0 then 0 else p.nh13/v.nh13 end,
			case when IFNULL(v.nh14,0) = 0 then 0 else p.nh14/v.nh14 end,
			case when IFNULL(v.nh15,0) = 0 then 0 else p.nh15/v.nh15 end,
			case when IFNULL(v.nh16,0) = 0 then 0 else p.nh16/v.nh16 end,
			case when IFNULL(v.nh17,0) = 0 then 0 else p.nh17/v.nh17 end,
			case when IFNULL(v.nh18,0) = 0 then 0 else p.nh18/v.nh18 end,
			case when IFNULL(v.nh19,0) = 0 then 0 else p.nh19/v.nh19 end,
			case when IFNULL(v.nh20,0) = 0 then 0 else p.nh20/v.nh20 end,
			case when IFNULL(v.nh21,0) = 0 then 0 else p.nh21/v.nh21 end,
			case when IFNULL(v.nh22,0) = 0 then 0 else p.nh22/v.nh22 end,
			case when IFNULL(v.nh23,0) = 0 then 0 else p.nh23/v.nh23 end
			from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p
			 where v.vdate = '$voneweekago' and v.vdate = p.vdate ";

        //echo $sql1;
        $fordb->query($sql);
        $fordb1->query($sql1);
        $fordb2->query($sql2);
        $fordb3->query($sql3);
        //echo "total:".$fordb->total;
        $fordb->fetch(0);
        $fordb1->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);
    }else if($SelectReport == 2){
        $sql .= "Select sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then v.ncnt else 1 end)";
        for($i=0;$i<7;$i++){
            $sql .= ",sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql .= " from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p
				where v.vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."'
				and p.vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."'";

        $sql2 .= "Select sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then v.ncnt else 1 end)";
        for($i=0;$i<7;$i++){
            $sql2 .= ",sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*$i)."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql2 .= " from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p
				where v.vdate between '$voneweekago' and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."'
				and p.vdate between '$voneweekago' and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."'";

        $sql3 .= "Select sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then v.ncnt else 1 end)";
        for($i=0;$i<7;$i++){
            $sql3 .= ",sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*$i)."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql3 .= " from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p
				where v.vdate between '$vfourweekago' and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."'
				and p.vdate between '$vfourweekago' and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."'";

        $fordb->query($sql);
        //$fordb1->query($sql1);
        $fordb2->query($sql2);
        $fordb3->query($sql3);
        //echo "total:".$fordb->total;
        $fordb->fetch(0);
        //$fordb1->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);


    }else if($SelectReport == 3){
        $sql .= "Select sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then v.ncnt else 1 end)";
        for($i = 1; $i < $nLoop;$i++){
            $sql .= ",sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql .= "from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p where v.vdate LIKE '".substr($vdate,0,6)."%' and p.vdate LIKE '".substr($vdate,0,6)."%'  ";


        $sql2 .= "Select sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then v.ncnt else 1 end)";

        for($i = 1; $i < $nLoop;$i++){
            $sql2 .= ",sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql2 .= "from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p where v.vdate LIKE '".substr($vonemonthago,0,6)."%' and p.vdate LIKE '".substr($vonemonthago,0,6)."%'  ";

        $sql3 .= "Select sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then v.ncnt else 1 end)";
        for($i = 1; $i < $nLoop;$i++){
            $sql3 .= ",sum(case when p.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then p.ncnt else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql3 .= "from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_PAGEVIEWTIME." p where v.vdate LIKE '".substr($vtwomonthago,0,6)."%' and p.vdate LIKE '".substr($vtwomonthago,0,6)."%'  ";


        $fordb->query($sql);
        //$fordb1->query($sql1);
        $fordb2->query($sql2);
        $fordb3->query($sql3);
        //echo "total:".$fordb->total;
        $fordb->fetch(0);
        //$fordb1->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);
    }

//	echo $sql1 ."<br>";
//	echo $sql2 ."<br>";
//	echo $sql3 ."<br>";


    if($SelectReport == 1){

        $mstring = $mstring.TitleBar("방문횟수 당 평균 페이지뷰 ","일간 : ". getNameOfWeekday(0,$vdate,"dayname"));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='pageviewbyvisit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=33%>항목</td><td class=m_td width=33%>시간대</td><td class=e_td width=34%>페이지뷰</td></tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff id='Report10000'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
		<td  onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\" align=center>{{MAXTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right style='padding-right:20px;' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\" >{{MAXVALUE}}</td>
		</tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff id='Report10001'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
		<td onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\" align=center>{{MINTIMESTRING}}</td>
		<td bgcolor=#efefef  align=right style='padding-right:20px;' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">{{MINVALUE}}</td>
		</tr>\n";
        $mstring .= "</table><br>\n";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=20%>시간</td><td class=m_td width=20%>해당일</td><td class=m_td width=20%>한달평균</td><td class=m_td width=20%>전일대비</td><td class=e_td width=20%>1주전 대비</td></tr>\n";

        $labels = array("해당일","한달평균","전일","1주전");
        $ykeys = array("a","b","c","d");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => ($i),
                'a' => ($fordb->dt[$i] ? $fordb->dt[$i] : 0) ,
                'b' => ($fordb1->dt[$i] ? $fordb1->dt[$i] : 0),
                'c' => ($fordb2->dt[$i] ? $fordb2->dt[$i] : 0),
                'd' => ($fordb3->dt[$i] ? $fordb3->dt[$i] : 0)
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">$i</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[$i]))."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb1->dt[$i]))."</td>
		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb2->dt[$i]))."</td>
		<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb3->dt[$i]))."</td></tr>\n";

            $pageview01 = $pageview01 + returnZeroValue($fordb->dt[$i]);
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
        $mstring .= "</table>\n";
        /*
            $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
            $mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
            $mstring .= "<tr height=30 align=right>
                                        <td width=50 class=s_td width=30>합계</td>
                                        <td class=m_td style='padding-right:20px;'>".number_format($pageview01,2)."</td>
                                        <td class=m_td style='padding-right:20px;'>".number_format($pageview02,2)."</td>
                                        <td class=m_td style='padding-right:20px;'>".number_format($pageview03,2)."</td>
                                        <td class=e_td style='padding-right:20px;'>".number_format($pageview04,2)."</td>
                                        </tr>\n";
            $mstring .= "</table>\n";
        */
        $mstring = str_replace("{{MAXVALUE}}",number_format($maxvalue,2),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format($minvalue,2),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getTimeString($maxtime),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getTimeString($mintime),$mstring);


    }else if($SelectReport == 2){
        $mstring = $mstring.TitleBar("방문횟수 당 평균 페이지뷰","주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='pageviewbyvisit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>요일</td><td class=e_td width=200>페이지뷰</td></tr>\n";
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
        $mstring .= "<tr height=30 align=center><td class=s_td width=150>요일</td><td class=m_td width=140>해당주</td><td class=m_td width=140>1주전 대비</td><td class=e_td width=140>4주전 대비</td></tr>\n";

        $labels = array("해당주","1주전","4주전");
        $ykeys = array("a","b","c");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => getNameOfWeekday($i,$vdate),
                'a' => ($fordb->dt[$i]) ,
                'b' => ($fordb2->dt[$i]),
                'c' => ($fordb3->dt[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff >
		<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate)."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[$i]))."</td>
		<td bgcolor=#efefef  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb2->dt[$i]))."</td>
		<td align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb3->dt[$i]))."</td></tr>\n";

            $pageview01 = $pageview01 + returnZeroValue($fordb->dt[$i]);
//		$pageview02 = $pageview02 + returnZeroValue($fordb1->dt[$i]);
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
        $mstring .= "</table>\n";
        /*
        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
        $mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
        $mstring .= "<tr height=30 align=right>
                                    <td class=s_td align=center>합계</td>
                                    <td class=m_td style='padding-right:20px;'>".number_format($pageview01,2)."</td>
                                    <td class=m_td style='padding-right:20px;'>".number_format($pageview03,2)."</td>
                                    <td class=e_td style='padding-right:20px;'>".number_format($pageview04,2)."</td>
                                    </tr>\n";
        $mstring .= "</table>\n";
        */
        $mstring = str_replace("{{MAXVALUE}}",number_format(returnZeroValue($maxvalue),2),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format(returnZeroValue($minvalue),2),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxtime,$vdate),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($mintime,$vdate),$mstring);
    }else if($SelectReport == 3){
        $mstring = $mstring.TitleBar("방문횟수 당 평균 페이지뷰","월간 : ".getNameOfWeekday(0,$vdate,"monthname"));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0 >\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='pageviewbyvisit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=33%>항목</td><td class=m_td width=33%>날짜</td><td class=e_td width=33%>페이지뷰</td></tr>\n";
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
        $mstring .= "<tr height=30 align=center><td class=s_td width=150>날짜</td><td class=m_td width=200>해당월</td><td class=m_td width=200>1개월전</td><td class=e_td width=200>2개월전</td></tr>\n";

        $labels = array("해당월","1개월전","2개월전");
        $ykeys = array("a","b","c");

        for($i=0;$i<$nLoop;$i++){

            $chart_data[] = array(
                'y' => getNameOfWeekday($i,$vdate,"dayname"),
                'a' => ($fordb->dt[$i]) ,
                'b' => ($fordb2->dt[$i]),
                'c' => ($fordb3->dt[$i])
            );

            $mstring .= "<tr height=30 bgcolor=#ffffff >
		<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate,"dayname")."</td>
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt[$i]))."</td>
		<td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb2->dt[$i]))."</td>
		<td  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb3->dt[$i]))."</td></tr>\n";

            $pageview01 = $pageview01 + returnZeroValue($fordb->dt[$i]);
//		$pageview02 = $pageview02 + returnZeroValue($fordb1->dt[$i]);
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
        $mstring .= "</table>\n";
        /*
            $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
            $mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
            $mstring .= "<tr height=30 align=right>
                                        <td class=s_td align=center>합계</td>
                                        <td class=m_td style='padding-right:20px;'>".number_format($pageview01,2)."</td>
                                        <td class=m_td style='padding-right:20px;'>".number_format($pageview03,2)."</td>
                                        <td class=e_td style='padding-right:20px;'>".number_format($pageview04,2)."</td>
                                        </tr>\n";
            $mstring .= "</table>\n";
        */
        $mstring = str_replace("{{MAXVALUE}}",number_format(returnZeroValue($maxvalue),2),$mstring);
        $mstring = str_replace("{{MINVALUE}}",number_format(returnZeroValue($minvalue),2),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxday,$vdate,"dayname"),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($minday,$vdate,"dayname"),$mstring);
    }
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 방문횟수 당 평균 페이지뷰란? 페이지의 호출 횟수를 총 방문 횟수와 비교하여 방문 횟수 대비 페이지 뷰를 확인하실 수 있는 리포트입니다.<br>

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


    $mstring .= HelpBox("방문횟수 당 평균 페이지뷰", $help_text,200);

    return $mstring;
}



if ($mode == "iframe"){

//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";




    $ca = new Calendar();
    $ca->LinkPage = 'pageviewbyvisit.php';

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

    $p->Navigation = "로그분석 > 방문자 분석 > 방문횟수당 평균 페이지뷰";
    $p->title = "방문횟수당 평균 페이지뷰";
    $p->forbizLeftMenu = Stat_munu('pageviewbyvisit.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}
?>
