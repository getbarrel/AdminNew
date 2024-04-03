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
    $mstring = "";
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

    if($SelectReport == 1){
        /*
        $sql = "Select d.nh00/v.nh00, d.nh01/v.nh01, d.nh02/v.nh02, d.nh03/v.nh03, d.nh04/v.nh04, d.nh05/v.nh05, d.nh06/v.nh06,
            d.nh07/v.nh07, d.nh08/v.nh08, d.nh09/v.nh09, d.nh10/v.nh10, d.nh11/v.nh11, d.nh12/v.nh12, d.nh13/v.nh13, d.nh14/v.nh14,
            d.nh15/v.nh15, d.nh16/v.nh16, d.nh17/v.nh17, d.nh18/v.nh18, d.nh19/v.nh19, d.nh20/v.nh20, d.nh21/v.nh21, d.nh22/v.nh22,
            d.nh23/v.nh23 from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d
            where v.vdate = '$vdate' and v.vdate = d.vdate";
        */
        $sql = "Select
			case when IFNULL(v.nh00,0) = 0 then 0 else d.nh00/v.nh00 end,
			case when IFNULL(v.nh01,0) = 0 then 0 else d.nh01/v.nh01 end,
			case when IFNULL(v.nh02,0) = 0 then 0 else d.nh02/v.nh02 end,
			case when IFNULL(v.nh03,0) = 0 then 0 else d.nh03/v.nh03 end,
			case when IFNULL(v.nh04,0) = 0 then 0 else d.nh04/v.nh04 end,
			case when IFNULL(v.nh05,0) = 0 then 0 else d.nh05/v.nh05 end,
			case when IFNULL(v.nh06,0) = 0 then 0 else d.nh06/v.nh06 end,
			case when IFNULL(v.nh07,0) = 0 then 0 else d.nh07/v.nh07 end,
			case when IFNULL(v.nh08,0) = 0 then 0 else d.nh08/v.nh08 end,
			case when IFNULL(v.nh09,0) = 0 then 0 else d.nh09/v.nh09 end,
			case when IFNULL(v.nh10,0) = 0 then 0 else d.nh10/v.nh10 end,
			case when IFNULL(v.nh11,0) = 0 then 0 else d.nh11/v.nh11 end,
			case when IFNULL(v.nh12,0) = 0 then 0 else d.nh12/v.nh12 end,
			case when IFNULL(v.nh13,0) = 0 then 0 else d.nh13/v.nh13 end,
			case when IFNULL(v.nh14,0) = 0 then 0 else d.nh14/v.nh14 end,
			case when IFNULL(v.nh15,0) = 0 then 0 else d.nh15/v.nh15 end,
			case when IFNULL(v.nh16,0) = 0 then 0 else d.nh16/v.nh16 end,
			case when IFNULL(v.nh17,0) = 0 then 0 else d.nh17/v.nh17 end,
			case when IFNULL(v.nh18,0) = 0 then 0 else d.nh18/v.nh18 end,
			case when IFNULL(v.nh19,0) = 0 then 0 else d.nh19/v.nh19 end,
			case when IFNULL(v.nh20,0) = 0 then 0 else d.nh20/v.nh20 end,
			case when IFNULL(v.nh21,0) = 0 then 0 else d.nh21/v.nh21 end,
			case when IFNULL(v.nh22,0) = 0 then 0 else d.nh22/v.nh22 end,
			case when IFNULL(v.nh23,0) = 0 then 0 else d.nh23/v.nh23 end
			from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d
			where v.vdate = '$vdate' and v.vdate = d.vdate";

        //echo nl2br($sql);
        $sql1 = "Select
			case when IFNULL(sum(v.nh00),0) = 0 then 0 else sum(d.nh00)/sum(v.nh00) end,
			case when IFNULL(sum(v.nh01),0) = 0 then 0 else sum(d.nh01)/sum(v.nh01) end,
			case when IFNULL(sum(v.nh02),0) = 0 then 0 else sum(d.nh02)/sum(v.nh02) end,
			case when IFNULL(sum(v.nh03),0) = 0 then 0 else sum(d.nh03)/sum(v.nh03) end,
			case when IFNULL(sum(v.nh04),0) = 0 then 0 else sum(d.nh04)/sum(v.nh04) end,
			case when IFNULL(sum(v.nh05),0) = 0 then 0 else sum(d.nh05)/sum(v.nh05) end,
			case when IFNULL(sum(v.nh06),0) = 0 then 0 else sum(d.nh06)/sum(v.nh06) end,
			case when IFNULL(sum(v.nh07),0) = 0 then 0 else sum(d.nh07)/sum(v.nh07) end,
			case when IFNULL(sum(v.nh08),0) = 0 then 0 else sum(d.nh08)/sum(v.nh08) end,
			case when IFNULL(sum(v.nh09),0) = 0 then 0 else sum(d.nh09)/sum(v.nh09) end,
			case when IFNULL(sum(v.nh10),0) = 0 then 0 else sum(d.nh10)/sum(v.nh10) end,
			case when IFNULL(sum(v.nh11),0) = 0 then 0 else sum(d.nh11)/sum(v.nh11) end,
			case when IFNULL(sum(v.nh12),0) = 0 then 0 else sum(d.nh12)/sum(v.nh12) end,
			case when IFNULL(sum(v.nh13),0) = 0 then 0 else sum(d.nh13)/sum(v.nh13) end,
			case when IFNULL(sum(v.nh14),0) = 0 then 0 else sum(d.nh14)/sum(v.nh14) end,
			case when IFNULL(sum(v.nh15),0) = 0 then 0 else sum(d.nh15)/sum(v.nh15) end,
			case when IFNULL(sum(v.nh16),0) = 0 then 0 else sum(d.nh16)/sum(v.nh16) end,
			case when IFNULL(sum(v.nh17),0) = 0 then 0 else sum(d.nh17)/sum(v.nh17) end,
			case when IFNULL(sum(v.nh18),0) = 0 then 0 else sum(d.nh18)/sum(v.nh18) end,
			case when IFNULL(sum(v.nh19),0) = 0 then 0 else sum(d.nh19)/sum(v.nh19) end,
			case when IFNULL(sum(v.nh20),0) = 0 then 0 else sum(d.nh20)/sum(v.nh20) end,
			case when IFNULL(sum(v.nh21),0) = 0 then 0 else sum(d.nh21)/sum(v.nh21) end,
			case when IFNULL(sum(v.nh22),0) = 0 then 0 else sum(d.nh22)/sum(v.nh22) end,
			case when IFNULL(sum(v.nh23),0) = 0 then 0 else sum(d.nh23)/sum(v.nh23) end
			from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d where substr(v.vdate,1,6) = '".substr($vdate,0,6)."' and v.vdate = d.vdate ";

        if($fordb1->dbms_type == "oracle"){
            $sql1 = "Select
				case when IFNULL(sum(v.nh00),0) = 0 then 0 else TO_NUMBER(avg(d.nh00)/avg(v.nh00)) end,
				case when IFNULL(sum(v.nh01),0) = 0 then 0 else TO_NUMBER(avg(d.nh01)/avg(v.nh01)) end,
				case when IFNULL(sum(v.nh02),0) = 0 then 0 else TO_NUMBER(avg(d.nh02)/avg(v.nh02)) end,
				case when IFNULL(sum(v.nh03),0) = 0 then 0 else TO_NUMBER(avg(d.nh03)/avg(v.nh03)) end,
				case when IFNULL(sum(v.nh04),0) = 0 then 0 else TO_NUMBER(avg(d.nh04)/avg(v.nh04)) end,
				case when IFNULL(sum(v.nh05),0) = 0 then 0 else TO_NUMBER(avg(d.nh05)/avg(v.nh05)) end,
				case when IFNULL(sum(v.nh06),0) = 0 then 0 else TO_NUMBER(avg(d.nh06)/avg(v.nh06)) end,
				case when IFNULL(sum(v.nh07),0) = 0 then 0 else TO_NUMBER(avg(d.nh07)/avg(v.nh07)) end,
				case when IFNULL(sum(v.nh08),0) = 0 then 0 else TO_NUMBER(avg(d.nh08)/avg(v.nh08)) end,
				case when IFNULL(sum(v.nh09),0) = 0 then 0 else TO_NUMBER(avg(d.nh09)/avg(v.nh09)) end,
				case when IFNULL(sum(v.nh10),0) = 0 then 0 else TO_NUMBER(avg(d.nh10)/avg(v.nh10)) end,
				case when IFNULL(sum(v.nh11),0) = 0 then 0 else TO_NUMBER(avg(d.nh11)/avg(v.nh11)) end,
				case when IFNULL(sum(v.nh12),0) = 0 then 0 else TO_NUMBER(avg(d.nh12)/avg(v.nh12)) end,
				case when IFNULL(sum(v.nh13),0) = 0 then 0 else TO_NUMBER(avg(d.nh13)/avg(v.nh13)) end,
				case when IFNULL(sum(v.nh14),0) = 0 then 0 else TO_NUMBER(avg(d.nh14)/avg(v.nh14)) end,
				case when IFNULL(sum(v.nh15),0) = 0 then 0 else TO_NUMBER(avg(d.nh15)/avg(v.nh15)) end,
				case when IFNULL(sum(v.nh16),0) = 0 then 0 else TO_NUMBER(avg(d.nh16)/avg(v.nh16)) end,
				case when IFNULL(sum(v.nh17),0) = 0 then 0 else TO_NUMBER(avg(d.nh17)/avg(v.nh17)) end,
				case when IFNULL(sum(v.nh18),0) = 0 then 0 else TO_NUMBER(avg(d.nh18)/avg(v.nh18)) end,
				case when IFNULL(sum(v.nh19),0) = 0 then 0 else TO_NUMBER(avg(d.nh19)/avg(v.nh19)) end,
				case when IFNULL(sum(v.nh20),0) = 0 then 0 else TO_NUMBER(avg(d.nh20)/avg(v.nh20)) end,
				case when IFNULL(sum(v.nh21),0) = 0 then 0 else TO_NUMBER(avg(d.nh21)/avg(v.nh21)) end,
				case when IFNULL(sum(v.nh22),0) = 0 then 0 else TO_NUMBER(avg(d.nh22)/avg(v.nh22)) end,
				case when IFNULL(sum(v.nh23),0) = 0 then 0 else TO_NUMBER(avg(d.nh23)/avg(v.nh23)) end
			from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d where substr(v.vdate,1,6) = '".substr($vdate,0,6)."' and v.vdate = d.vdate ";
        }else{
            $sql1 = "Select FORMAT(avg(d.nh00)/avg(v.nh00),1), FORMAT(avg(d.nh01)/avg(v.nh01),1), FORMAT(avg(d.nh02)/avg(v.nh02),1), FORMAT(avg(d.nh03)/avg(v.nh03),1), FORMAT(avg(d.nh04)/avg(v.nh04),1), FORMAT(avg(d.nh05)/avg(v.nh05),1),
			FORMAT(avg(d.nh06)/avg(v.nh06),1), FORMAT(avg(d.nh07)/avg(v.nh07),1), FORMAT(avg(d.nh08)/avg(v.nh08),1), FORMAT(avg(d.nh09)/avg(v.nh09),1), FORMAT(avg(d.nh10)/avg(v.nh10),1), FORMAT(avg(d.nh11)/avg(v.nh11),1),
			FORMAT(avg(d.nh12)/avg(v.nh12),1), FORMAT(avg(d.nh13)/avg(v.nh13),1), FORMAT(avg(d.nh14)/avg(v.nh14),1), FORMAT(avg(d.nh15)/avg(v.nh15),1), FORMAT(avg(d.nh16)/avg(v.nh16),1), FORMAT(avg(d.nh17)/avg(v.nh17),1),
			FORMAT(avg(d.nh18)/avg(v.nh18),1), FORMAT(avg(d.nh19)/avg(v.nh19),1), FORMAT(avg(d.nh20)/avg(v.nh20),1), FORMAT(avg(d.nh21)/avg(v.nh21),1), FORMAT(avg(d.nh22)/avg(v.nh22),1), FORMAT(avg(d.nh23)/avg(v.nh23),1)
			from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d where substring(v.vdate,1,6) = '".substr($vdate,0,6)."' and v.vdate = d.vdate ";
        }

        /*
                $sql2 = "Select d.nh00/v.nh00, d.nh01/v.nh01, d.nh02/v.nh02, d.nh03/v.nh03, d.nh04/v.nh04, d.nh05/v.nh05, d.nh06/v.nh06,
                    d.nh07/v.nh07, d.nh08/v.nh08, d.nh09/v.nh09, d.nh10/v.nh10, d.nh11/v.nh11, d.nh12/v.nh12, d.nh13/v.nh13, d.nh14/v.nh14,
                    d.nh15/v.nh15, d.nh16/v.nh16, d.nh17/v.nh17, d.nh18/v.nh18, d.nh19/v.nh19, d.nh20/v.nh20, d.nh21/v.nh21, d.nh22/v.nh22,
                    d.nh23/v.nh23 from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d where v.vdate = '$vyesterday' and v.vdate = d.vdate";
        */
        $sql2 = "Select
			case when IFNULL(v.nh00,0) = 0 then 0 else d.nh00/v.nh00 end,
			case when IFNULL(v.nh01,0) = 0 then 0 else d.nh01/v.nh01 end,
			case when IFNULL(v.nh02,0) = 0 then 0 else d.nh02/v.nh02 end,
			case when IFNULL(v.nh03,0) = 0 then 0 else d.nh03/v.nh03 end,
			case when IFNULL(v.nh04,0) = 0 then 0 else d.nh04/v.nh04 end,
			case when IFNULL(v.nh05,0) = 0 then 0 else d.nh05/v.nh05 end,
			case when IFNULL(v.nh06,0) = 0 then 0 else d.nh06/v.nh06 end,
			case when IFNULL(v.nh07,0) = 0 then 0 else d.nh07/v.nh07 end,
			case when IFNULL(v.nh08,0) = 0 then 0 else d.nh08/v.nh08 end,
			case when IFNULL(v.nh09,0) = 0 then 0 else d.nh09/v.nh09 end,
			case when IFNULL(v.nh10,0) = 0 then 0 else d.nh10/v.nh10 end,
			case when IFNULL(v.nh11,0) = 0 then 0 else d.nh11/v.nh11 end,
			case when IFNULL(v.nh12,0) = 0 then 0 else d.nh12/v.nh12 end,
			case when IFNULL(v.nh13,0) = 0 then 0 else d.nh13/v.nh13 end,
			case when IFNULL(v.nh14,0) = 0 then 0 else d.nh14/v.nh14 end,
			case when IFNULL(v.nh15,0) = 0 then 0 else d.nh15/v.nh15 end,
			case when IFNULL(v.nh16,0) = 0 then 0 else d.nh16/v.nh16 end,
			case when IFNULL(v.nh17,0) = 0 then 0 else d.nh17/v.nh17 end,
			case when IFNULL(v.nh18,0) = 0 then 0 else d.nh18/v.nh18 end,
			case when IFNULL(v.nh19,0) = 0 then 0 else d.nh19/v.nh19 end,
			case when IFNULL(v.nh20,0) = 0 then 0 else d.nh20/v.nh20 end,
			case when IFNULL(v.nh21,0) = 0 then 0 else d.nh21/v.nh21 end,
			case when IFNULL(v.nh22,0) = 0 then 0 else d.nh22/v.nh22 end,
			case when IFNULL(v.nh23,0) = 0 then 0 else d.nh23/v.nh23 end
			from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d where v.vdate = '$vyesterday' and v.vdate = d.vdate";
        /*
                $sql3 = "Select d.nh00/v.nh00, d.nh01/v.nh01, d.nh02/v.nh02, d.nh03/v.nh03, d.nh04/v.nh04, d.nh05/v.nh05, d.nh06/v.nh06,
                    d.nh07/v.nh07, d.nh08/v.nh08, d.nh09/v.nh09, d.nh10/v.nh10, d.nh11/v.nh11, d.nh12/v.nh12, d.nh13/v.nh13, d.nh14/v.nh14,
                    d.nh15/v.nh15, d.nh16/v.nh16, d.nh17/v.nh17, d.nh18/v.nh18, d.nh19/v.nh19, d.nh20/v.nh20, d.nh21/v.nh21, d.nh22/v.nh22,
                    d.nh23/v.nh23 from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d where v.vdate = '$voneweekago' and v.vdate = d.vdate";
        */

        $sql3 = "Select
			case when IFNULL(v.nh00,0) = 0 then 0 else d.nh00/v.nh00 end,
			case when IFNULL(v.nh01,0) = 0 then 0 else d.nh01/v.nh01 end,
			case when IFNULL(v.nh02,0) = 0 then 0 else d.nh02/v.nh02 end,
			case when IFNULL(v.nh03,0) = 0 then 0 else d.nh03/v.nh03 end,
			case when IFNULL(v.nh04,0) = 0 then 0 else d.nh04/v.nh04 end,
			case when IFNULL(v.nh05,0) = 0 then 0 else d.nh05/v.nh05 end,
			case when IFNULL(v.nh06,0) = 0 then 0 else d.nh06/v.nh06 end,
			case when IFNULL(v.nh07,0) = 0 then 0 else d.nh07/v.nh07 end,
			case when IFNULL(v.nh08,0) = 0 then 0 else d.nh08/v.nh08 end,
			case when IFNULL(v.nh09,0) = 0 then 0 else d.nh09/v.nh09 end,
			case when IFNULL(v.nh10,0) = 0 then 0 else d.nh10/v.nh10 end,
			case when IFNULL(v.nh11,0) = 0 then 0 else d.nh11/v.nh11 end,
			case when IFNULL(v.nh12,0) = 0 then 0 else d.nh12/v.nh12 end,
			case when IFNULL(v.nh13,0) = 0 then 0 else d.nh13/v.nh13 end,
			case when IFNULL(v.nh14,0) = 0 then 0 else d.nh14/v.nh14 end,
			case when IFNULL(v.nh15,0) = 0 then 0 else d.nh15/v.nh15 end,
			case when IFNULL(v.nh16,0) = 0 then 0 else d.nh16/v.nh16 end,
			case when IFNULL(v.nh17,0) = 0 then 0 else d.nh17/v.nh17 end,
			case when IFNULL(v.nh18,0) = 0 then 0 else d.nh18/v.nh18 end,
			case when IFNULL(v.nh19,0) = 0 then 0 else d.nh19/v.nh19 end,
			case when IFNULL(v.nh20,0) = 0 then 0 else d.nh20/v.nh20 end,
			case when IFNULL(v.nh21,0) = 0 then 0 else d.nh21/v.nh21 end,
			case when IFNULL(v.nh22,0) = 0 then 0 else d.nh22/v.nh22 end,
			case when IFNULL(v.nh23,0) = 0 then 0 else d.nh23/v.nh23 end
			from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d where v.vdate = '$voneweekago' and v.vdate = d.vdate";

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
        $sql .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then v.ncnt else 1 end)";
        for($i=0;$i<7;$i++){
            $sql .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql .= " from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d
				where v.vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."'
				and d.vdate between '$vdate' and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."'";

        $sql2 .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then v.ncnt else 1 end)";
        for($i=0;$i<7;$i++){
            $sql2 .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*$i)."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql2 .= " from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d
				where v.vdate between '$voneweekago' and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."'
				and d.vdate between '$voneweekago' and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."'";

        $sql3 .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then v.ncnt else 1 end)";
        for($i=0;$i<7;$i++){
            $sql3 .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*$i)."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql3 .= " from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d
				where v.vdate between '$vfourweekago' and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."'
				and d.vdate between '$vfourweekago' and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."'";


        // echo $sql2;
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
        $sql .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then v.ncnt else 1 end)";
        for($i = 1; $i < $nLoop;$i++){
            $sql .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql .= "from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d
			     where v.vdate LIKE '".substr($vdate,0,6)."%' and d.vdate LIKE '".substr($vdate,0,6)."%' and v.vdate = d.vdate ";

        $sql2 .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then v.ncnt else 1 end)";

        for($i = 1; $i < $nLoop;$i++){
            $sql2 .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql2 .= "from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d
				where v.vdate LIKE '".substr($vonemonthago,0,6)."%' and d.vdate LIKE '".substr($vonemonthago,0,6)."%' and v.vdate = d.vdate ";

        $sql3 .= "Select sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then v.ncnt else 1 end)";
        for($i = 1; $i < $nLoop;$i++){
            $sql3 .= ",sum(case when d.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then d.nduration else 0 end)/sum(case when v.vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then v.ncnt else 1 end)";
        }
        $sql3 .= "from ".TBL_LOGSTORY_VISITTIME." v, ".TBL_LOGSTORY_DURATIONTIME." d
				where v.vdate LIKE '".substr($vtwomonthago,0,6)."%' and d.vdate LIKE '".substr($vtwomonthago,0,6)."%' and v.vdate = d.vdate ";


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

        $mstring = $mstring.TitleBar("방문횟수 당 평균 체류시간 ","일간 : ". getNameOfWeekday(0,$vdate,"dayname"));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='durationbyvisit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
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
        $mstring .= "<tr height=30 align=center><td class=s_td width=20%>시간</td><td class=m_td width=20%>해당일</td><td class=m_td width=20%>한달평균</td><td class=m_td width=20%>전일</td><td class=e_td width=20%>1주전</td></tr>\n";

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

            $mstring .= "<tr height=30 bgcolor=#ffffff id='Report$i' >
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">$i</td>
		<td class='point' align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($fordb->dt[$i]))."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($fordb1->dt[$i]))."</td>
		<td align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($fordb2->dt[$i]))."</td>
		<td bgcolor=#efefef  align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($fordb3->dt[$i]))."</td></tr>\n";

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

        $mstring .= "<tr height=30 align=right>
								<td width=50 class=s_td width=30>합계</td>
								<td class=m_td width=190 style='padding-right:20px;'>".displayTimeFormat($pageview01)."</td>
								<td class=m_td width=190 style='padding-right:20px;'>".displayTimeFormat($pageview02)."</td>
								<td class=m_td width=190 style='padding-right:20px;'>".displayTimeFormat($pageview03)."</td>
								<td class=e_td width=190 style='padding-right:20px;'>".displayTimeFormat($pageview04)."</td>
								</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",displayTimeFormat(returnZeroValue($maxvalue)),$mstring);
        $mstring = str_replace("{{MINVALUE}}",displayTimeFormat(returnZeroValue($minvalue)),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getTimeString($maxtime),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getTimeString($mintime),$mstring);


    }else if($SelectReport == 2){
        $mstring = $mstring.TitleBar("방문횟수 당 평균 체류시간",getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='durationbyvisit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'>\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=30%>항목</td><td class=m_td width=35%>요일</td><td class=e_td width=35%>평균 체류시간</td></tr>\n";
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
        $mstring .= "<tr height=30 align=center><td class=s_td width=25%>요일</td><td class=m_td width=25%>해당주</td><td class=m_td width=25%>1주전</td><td class=e_td width=25%>4주전</td></tr>\n";

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
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($fordb->dt[$i]))."</td>
		<td bgcolor=#efefef align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($fordb2->dt[$i]))."</td>
		<td  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($fordb3->dt[$i]))."</td></tr>\n";

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

        $mstring .= "<tr height=30 align=right>
									<td class=s_td align=center>합계</td>
									<td class=m_td style='padding-right:20px;'>".displayTimeFormat($pageview01)."</td>
									<td class=m_td style='padding-right:20px;'>".displayTimeFormat($pageview03)."</td>
									<td class=e_td style='padding-right:20px;'>".displayTimeFormat($pageview04)."</td>
									</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",displayTimeFormat(returnZeroValue($maxvalue)),$mstring);
        $mstring = str_replace("{{MINVALUE}}",displayTimeFormat(returnZeroValue($minvalue)),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxtime,$vdate),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($mintime,$vdate),$mstring);
    }else if($SelectReport == 3){
        $mstring = $mstring.TitleBar("방문횟수 당 평균 체류시간",getNameOfWeekday(0,$vdate,"monthname"));
        $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='durationbyvisit.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";


        $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box' >\n";
        $mstring .= "<tr height=30 align=center><td class=s_td width=30%>항목</td><td class=m_td width=35%>날짜</td><td class=e_td width=35%>평균 체류시간</td></tr>\n";
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
        $mstring .= "<tr height=30 align=center><td class=s_td width=25%>날짜</td><td class=m_td width=25%>해당월</td><td class=m_td width=25%>1개월전</td><td class=e_td width=25%>2개월전</td></tr>\n";

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
		<td class='point' align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($fordb->dt[$i]))."</td>
		<td bgcolor=#efefef  align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($fordb2->dt[$i]))."</td>
		<td align=right id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".displayTimeFormat(returnZeroValue($fordb3->dt[$i]))."</td></tr>\n";

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

        $mstring .= "<tr height=30 align=right>
									<td  class=s_td align=center>합계</td>
									<td class=m_td style='padding-right:20px;'>".displayTimeFormat($pageview01)."</td>
									<td class=m_td style='padding-right:20px;'>".displayTimeFormat($pageview03)."</td>
									<td class=e_td style='padding-right:20px;'>".displayTimeFormat($pageview04)."</td>
									</tr>\n";
        $mstring .= "</table>\n";

        $mstring = str_replace("{{MAXVALUE}}",displayTimeFormat(returnZeroValue($maxvalue)),$mstring);
        $mstring = str_replace("{{MINVALUE}}",displayTimeFormat(returnZeroValue($minvalue)),$mstring);
        $mstring = str_replace("{{MAXTIMESTRING}}",getNameOfWeekday($maxday,$vdate,"dayname"),$mstring);
        $mstring = str_replace("{{MINTIMESTRING}}",getNameOfWeekday($minday,$vdate,"dayname"),$mstring);
    }
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 방문횟수 당 평균 체류시간란? 쇼핑몰을 방문한 회원의 재방문 횟수까지 포함하여 횟수당 평균 체류시간을 확인하실 수 있는 리포트입니다.<br>

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


    $mstring .= HelpBox("방문횟수 당 평균 체류시간", $help_text,200);

    return $mstring;
}

if ($mode == "iframe"){

//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";




    $ca = new Calendar();
    $ca->LinkPage = 'durationbyvisit.php';

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

    $p->Navigation = "로그분석 > 방문자 분석 > 방문횟수당 평균 체류시간";
    $p->title = "방문횟수당 평균 체류시간";
    $p->forbizLeftMenu = Stat_munu('durationbyvisit.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}
?>
