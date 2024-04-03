<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include_once ("../graph/jpgraph.php");
include_once ("../graph/jpgraph_bar.php");
include_once ("../graph/jpgraph_line.php");
include_once("../include/util.php");
include_once("../class/database.class");


$fordb = new forbizDatabase();
$fordb1 = new forbizDatabase();
$fordb2 = new forbizDatabase();
$fordb3 = new forbizDatabase();

if($vdate == ""){
    $vyesterday = date("Ymd", time()-1);
    $towdayago = date("Ymd", time()-2);
    $vdate = date("Ymd", time());
}else{
    $week_num = date("w",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)));

    $firstday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-$week_num,substr($vdate,0,4)));
    //echo $vdate;
    if($SelectReport ==3){
        //$vdate = $vdate."01";
        $vdate = $firstday;
    }else if($SelectReport ==1 || $SelectReport ==2){
        $vdate = $firstday;
    }
    //echo $firstday;
    $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
    $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    $vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
    $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*30);
    $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*60);
}
if($SelectReport == 1){
    $nLoop = 7;
}else if($SelectReport ==2){
    $nLoop = 7;
}else if($SelectReport ==3){
    //$nLoop = date("t", mktime(0, 0, 0, substr($vdate,4,2), substr($vdate,6,2), substr($vdate,0,4)));
    $nLoop = 7;
}

if($SelectReport == 1 || $SelectReport == 2 || $SelectReport == 3){
    $sql = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)+1,substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)+2,substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)+3,substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)+4,substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)+5,substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)+6,substr($vdate,0,4)))."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_BYPAGE." where pageid = '$pageid' ";
//		$sql1 = "Select FORMAT(avg(nh00),1), FORMAT(avg(nh01),1), FORMAT(avg(nh02),1), FORMAT(avg(nh03),1), FORMAT(avg(nh04),1), FORMAT(avg(nh05),1), FORMAT(avg(nh06),1), FORMAT(avg(nh07),1), FORMAT(avg(nh08),1), FORMAT(avg(nh09),1), FORMAT(avg(nh10),1), FORMAT(avg(nh11),1), FORMAT(avg(nh12),1), FORMAT(avg(nh13),1), FORMAT(avg(nh14),1), FORMAT(avg(nh15),1), FORMAT(avg(nh16),1), FORMAT(avg(nh17),1), FORMAT(avg(nh18),1), FORMAT(avg(nh19),1), FORMAT(avg(nh20),1), FORMAT(avg(nh21),1), FORMAT(avg(nh22),1), FORMAT(avg(nh23),1) from ".TBL_LOGSTORY_PAGEVIEWTIME." where substring(vdate,1,6) = '".substr($vdate,0,6)."'";
    $sql1 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2)+1,substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2)+2,substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2)+3,substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2)+4,substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2)+5,substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2)+6,substr($voneweekago,0,4)))."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_BYPAGE." where pageid = '$pageid' ";
    $sql2 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_BYPAGE." where pageid = '$pageid' ";
    $sql3 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_BYPAGE." where pageid = '$pageid' ";

    //echo $sql;
    $fordb->query($sql);
    $fordb1->query($sql1);
    $fordb2->query($sql2);
    $fordb3->query($sql3);
    //echo "total:".$fordb->total;
    $fordb->fetch(0);
    $fordb1->fetch(0);
    $fordb2->fetch(0);
    $fordb3->fetch(0);
    $regend01 = "해당일";
    $regend02 = "평균";
    $regend03 = "1일전";
    $regend04 = "1주전";
    $xaixs_title = "요일";

    $labels = array("해당일","평균","1일전","1주전");
    $ykeys = array("a","b","c","d");

}else if($SelectReport == 2){
    $sql = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_PAGEVIEWTIME." ";
//		$sql1 = "Select FORMAT(avg(nh00),1), FORMAT(avg(nh01),1), FORMAT(avg(nh02),1), FORMAT(avg(nh03),1), FORMAT(avg(nh04),1), FORMAT(avg(nh05),1), FORMAT(avg(nh06),1), FORMAT(avg(nh07),1), FORMAT(avg(nh08),1), FORMAT(avg(nh09),1), FORMAT(avg(nh10),1), FORMAT(avg(nh11),1), FORMAT(avg(nh12),1), FORMAT(avg(nh13),1), FORMAT(avg(nh14),1), FORMAT(avg(nh15),1), FORMAT(avg(nh16),1), FORMAT(avg(nh17),1), FORMAT(avg(nh18),1), FORMAT(avg(nh19),1), FORMAT(avg(nh20),1), FORMAT(avg(nh21),1), FORMAT(avg(nh22),1), FORMAT(avg(nh23),1) from ".TBL_LOGSTORY_PAGEVIEWTIME." where substring(vdate,1,6) = '".substr($vdate,0,6)."'";
    $sql2 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_PAGEVIEWTIME." ";
    $sql3 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*2)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*3)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*4)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*5)."' then ncnt else 0 end),
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4))+60*60*24*6)."' then ncnt else 0 end)
		 	from ".TBL_LOGSTORY_PAGEVIEWTIME." ";



    $fordb->query($sql);
    //$fordb1->query($sql1);
    $fordb2->query($sql2);
    $fordb3->query($sql3);
    //echo "total:".$fordb->total;
    $fordb->fetch(0);
    //$fordb1->fetch(0);
    $fordb2->fetch(0);
    $fordb3->fetch(0);
    $regend01 = "해당일";
    //$regend02 = "평균";
    $regend03 = "1일전";
    $regend04 = "1주전";
    $xaixs_title = "요일";

    $labels = array("해당일","1일전","1주전");
    $ykeys = array("a","b","c");

}else if($SelectReport == 3){
    $sql .= "Select ";
    $sql .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4)))."' then ncnt else 0 end) ";
    for($i = 1; $i < $nLoop;$i++){
        $sql .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
    }
    $sql .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." ";

//		$sql1 = "Select FORMAT(avg(nh00),1), FORMAT(avg(nh01),1), FORMAT(avg(nh02),1), FORMAT(avg(nh03),1), FORMAT(avg(nh04),1), FORMAT(avg(nh05),1), FORMAT(avg(nh06),1), FORMAT(avg(nh07),1), FORMAT(avg(nh08),1), FORMAT(avg(nh09),1), FORMAT(avg(nh10),1), FORMAT(avg(nh11),1), FORMAT(avg(nh12),1), FORMAT(avg(nh13),1), FORMAT(avg(nh14),1), FORMAT(avg(nh15),1), FORMAT(avg(nh16),1), FORMAT(avg(nh17),1), FORMAT(avg(nh18),1), FORMAT(avg(nh19),1), FORMAT(avg(nh20),1), FORMAT(avg(nh21),1), FORMAT(avg(nh22),1), FORMAT(avg(nh23),1) from ".TBL_LOGSTORY_PAGEVIEWTIME." where substring(vdate,1,6) = '".substr($vdate,0,6)."'";

    $sql2 .= "Select ";
    $sql2 .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4)))."' then ncnt else 0 end) ";
    for($i = 1; $i < $nLoop;$i++){
        $sql2 .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vonemonthago,4,2),1,substr($vonemonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
    }
    $sql2 .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." ";

    $sql3 .= "Select ";
    $sql3 .= "sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4)))."' then ncnt else 0 end) ";
    for($i = 1; $i < $nLoop;$i++){
        $sql3 .= ",sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vtwomonthago,4,2),1,substr($vtwomonthago,0,4))+60*60*24*$i)."' then ncnt else 0 end) ";
    }
    $sql3 .= "from ".TBL_LOGSTORY_PAGEVIEWTIME." ";


    $fordb->query($sql);
    //$fordb1->query($sql1);
    $fordb2->query($sql2);
    $fordb3->query($sql3);
    //echo "total:".$fordb->total;
    $fordb->fetch(0);
    //$fordb1->fetch(0);
    $fordb2->fetch(0);
    $fordb3->fetch(0);
    $regend01 = "해당월";
    //	$regend02 = "평균";
    $regend03 = "1개월전";
    $regend04 = "2개월전";
    $xaixs_title = "일자";

    $labels = array("해당월","1개월전","2개월전");
    $ykeys = array("a","b","c");
}

$_title_data = array('일요일','월요일','화요일','수요일','목요일','금요일',"토요일");

if($SelectReport == 1 || $SelectReport == 2 || $SelectReport == 3){
    for($i=0;$i<$nLoop;$i++){
        $datay[$i] = CheckGraphValue($fordb->dt[$i]);
        $datay1[$i] = CheckGraphValue($fordb1->dt[$i]);
        $datay2[$i] = CheckGraphValue($fordb2->dt[$i]);
        $datay3[$i] = CheckGraphValue($fordb3->dt[$i]);

        $chart_data[] = array(
            'y' => $_title_data[$i],
            'a' => ($fordb->dt[$i]) ,
            'b' => ($fordb1->dt[$i]),
            'c' => ($fordb2->dt[$i]),
            'd' => ($fordb3->dt[$i])
        );

    }
}else{
    for($i=0;$i<$nLoop;$i++){
        $datay[$i] = CheckGraphValue($fordb->dt[$i]);
        $datay2[$i] = CheckGraphValue($fordb2->dt[$i]);
        $datay3[$i] = CheckGraphValue($fordb3->dt[$i]);

        $chart_data[] = array(
            'y' => $_title_data[$i],
            'a' => ($fordb->dt[$i]) ,
            'b' => ($fordb2->dt[$i]),
            'c' => ($fordb3->dt[$i])
        );

    }
}


if($chart_mode!="morris"){
    //$datay=array(12,8,19,3,10,5,8,5,3,6,8,9,0,7,5,4,3,6,78,89,7,55,42,11);

    // Create the graph. These two calls are always required
    $graph = new Graph(450,200,"auto");
    $graph->img->SetMargin(60,80,30,40);
    $graph->SetScale("textlin");
    $graph->yaxis->SetTitleMargin(55);
    $graph->yaxis->scale->SetGrace(5);
    //$graph->SetColor("white");bmkb
    $graph->SetFrame(true,array(255,255,255),0);

    $graph->SetMarginColor("#efefef");
    $graph->legend->SetFont(FF_GULIM,FS_NORMAL,9);
    //$graph->SetFrame(true,"white");
    //$graph->SetShadow();
    $graph->legend->Pos(0.02,0.32,"right","center");
    // Create a bar pot
    $bplot = new BarPlot($datay);
    $bplot->SetWidth(0.7);
    $bplot->SetLegend("this day");
    $bplot->SetLegend(iconv('UTF-8','UTF-8',$regend01));

    //$bplot->SetFillColor("orange").$bplot->SetWidth(0.8);

    if($SelectReport == 1 && false){
        // Create the linear error plot
        $avgline=new LinePlot($datay1);
        //$l1plot->mark->SetType(MARK_FILLEDCIRCLE);
        //$avgline->mark->SetType(MARK_SQUARE);
        //$avgline->mark->SetFillColor("orange");
        $avgline->mark->SetWidth(3);
        $avgline->SetColor("red");
        $avgline->SetWeight(1);
        $avgline->SetLegend("average");
        $avgline->SetLegend(iconv('UTF-8','UTF-8',$regend02));
    }



    // Create the linear error plot
    $l1plot1=new LinePlot($datay2);
    //$l1plot->mark->SetType(MARK_FILLEDCIRCLE);
    $l1plot1->mark->SetType(MARK_SQUARE);
    $l1plot1->mark->SetFillColor("red");
    $l1plot1->mark->SetWidth(3);
    $l1plot1->SetColor("blue");
    $l1plot1->SetWeight(1);
    //$l1plot1->SetLegend("one day ago");
    $l1plot1->SetLegend(iconv('UTF-8','UTF-8',$regend03));

    // Create the linear error plot
    $l1plot2=new LinePlot($datay3);
    //$l1plot->mark->SetType(MARK_FILLEDCIRCLE);
    $l1plot2->mark->SetType(MARK_UTRIANGLE);
    $l1plot2->mark->SetFillColor("blue");
    $l1plot2->mark->SetWidth(3);
    $l1plot2->SetColor("black");
    $l1plot2->SetWeight(1);
    $l1plot2->SetLegend(iconv('UTF-8','UTF-8',$regend04));
    //$l1plot2->SetLegend->SetFont(FF_GULIM,FS_NORMAL,9);
    //$l1plot2->SetLegend("a week ago");


    $graph->Add($bplot);
    if($SelectReport == 1 && false){
        //print_r($datay1);
        $graph->Add($avgline);
    }
    $graph->Add($l1plot1);
    $graph->Add($l1plot2);

    $graph->title->Set(iconv('UTF-8','UTF-8',($pagename ? $pagename." 요일별 추이":'페이지뷰')));
    $graph->xaxis->title->Set(iconv('UTF-8','UTF-8',$xaixs_title));//"TIME"
    $graph->yaxis->title->Set(iconv('UTF-8','UTF-8','뷰지이페'));//"PAGE VIEW"


    $graph->title->SetFont(FF_GULIM,FS_NORMAL,9);
    $graph->yaxis->title->SetFont(FF_GULIM,FS_NORMAL,9);
    $graph->xaxis->title->SetFont(FF_GULIM,FS_NORMAL,9);
    $graph->xaxis->SetFont(FF_GULIM,FS_NORMAL,9);
    $datax=array(iconv('UTF-8','UTF-8','일요일'),iconv('UTF-8','UTF-8','월요일'),iconv('UTF-8','UTF-8','화요일'),iconv('UTF-8','UTF-8','수요일'),iconv('UTF-8','UTF-8','목요일'),iconv('UTF-8','UTF-8','금요일'),iconv('UTF-8','UTF-8',"토요일"));
    if($SelectReport == 1 || $SelectReport == 2){

        $graph->xaxis->SetTickLabels($datax);
    }else{
        $graph->xaxis->SetTickLabels($datax);
    }
    // Display the graph
    $graph->Stroke();

    //echo $graph->GetHTMLImageMap("myimagemap");
    //echo "<img src=\"".GenImgName()."\" ISMAP USEMAP=\"#myimagemap\" border=0>";

}
?>