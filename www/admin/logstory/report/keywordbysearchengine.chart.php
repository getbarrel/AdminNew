<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include ("../graph/jpgraph.php");
include ("../graph/jpgraph_pie.php");
include ("../graph/jpgraph_pie3d.php");

//chmod($_SERVER["DOCUMENT_ROOT"]."/manage/logstory/report/", 0777);

function keywordbysearchengineGraph($vdate,$SelectReport=1){
    global $rdepth,$referer_id;
    $fordb = new forbizDatabase();
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

    if ($SelectReport == 1){
        if($rdepth == 1){
            $sql = "select vreferer_id, cname, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt ";
            $sql .= "from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k ";
            $sql .= "where k.kid = b.kid and  b.vdate = '$vdate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  and r.depth = 2 and substring(b.vreferer_id,1,6) = '000001' group by vreferer_id ";
            $sql .= "order by visit_cnt desc, vlevel1, vlevel2,vlevel3 limit 10";
        }else if($rdepth == 2){
            $sql = "select vreferer_id, cname, keyword, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt  
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k 
							where k.kid = b.kid and  b.vdate = '$vdate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  
							and r.depth = 2 and b.vreferer_id LIKE '".substr($referer_id,0,9)."%'  and keyword <> '' ";
            $sql .= "group by vreferer_id, keyword order by visit_cnt desc, vlevel1, vlevel2,vlevel3 limit 10";
        }

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        if($rdepth == 1){
            $sql = "select vreferer_id, cname, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt ";
            $sql .= "from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k ";
            $sql .= "where k.kid = b.kid and  b.vdate between '$vdate' and '$vweekenddate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  and r.depth = 2 and substring(b.vreferer_id,1,6) = '000001' group by vreferer_id ";
            $sql .= "order by visit_cnt desc, vlevel1, vlevel2,vlevel3  limit 10";
        }else if($rdepth == 2){
            //	$sql = "select vreferer_id, cname, keyword, sum(b.visit_cnt) as visit_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k where k.kid = b.kid and  b.vdate = '$vdate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  and r.depth = 2 and b.vreferer_id LIKE '".substr($referer_id,0,9)."%' ";
            //	$sql .= "group by vreferer_id, keyword order by vlevel1, vlevel2,vlevel3, visit_cnt desc";

            $sql = "select vreferer_id, cname, keyword, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k where k.kid = b.kid and  b.vdate between '$vdate' and '$vweekenddate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  and r.depth = 2 and b.vreferer_id LIKE '".substr($referer_id,0,9)."%'  and keyword <> '' ";
            $sql .= "group by vreferer_id, keyword order by visit_cnt desc, vlevel1, vlevel2,vlevel3 limit 10";
        }

        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        if($rdepth == 1){
            $sql = "select vreferer_id, cname, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt ";
            $sql .= "from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k ";
            $sql .= "where k.kid = b.kid and  b.vdate LIKE '".substr($vdate,0,6)."%'  and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  and r.depth = 2 and substring(b.vreferer_id,1,6) = '000001' group by vreferer_id ";
            $sql .= "order by visit_cnt desc , vlevel1, vlevel2,vlevel3 limit 10";
        }else if($rdepth == 2){
            $sql = "select vreferer_id, cname, keyword, sum(b.visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt  from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k where k.kid = b.kid and b.vdate LIKE '".substr($vdate,0,6)."%'  and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)  and r.depth = 2 and b.vreferer_id LIKE '".substr($referer_id,0,9)."%'  and keyword <> '' ";
            $sql .= "group by vreferer_id, keyword order by visit_cnt desc, vlevel1, vlevel2,vlevel3 limit 10";
        }
    }
//	echo $sql;
    $fordb->query($sql);

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $data[$i] = returnZeroValue($fordb->dt[visit_cnt]);

        if($fordb->dt[cname]){
            $cname = $fordb->dt[cname];
        }else{
            $cname = "-";
        }
        if($rdepth == 1){
            $keyword[$i] = iconv('UTF-8','UTF-8',$cname);
            $keyword_encode[$i] = $cname;
        }else if($rdepth == 2){
            $keyword[$i] = iconv('UTF-8','UTF-8',$cname.">".$fordb->dt[keyword]);
            $keyword_encode[$i] = $cname.">".$fordb->dt[keyword]." : ".returnZeroValue($fordb->dt[visit_cnt]);
        }
    }

// Some data
//$data = array(40,21,17,14,23);

// Create the Pie Graph.
    $graph = new PieGraph(888,250);
    $graph->img->SetMargin(0,0,0,0);
//$graph->SetShadow();

// Set A title for the plot
//$graph->title->Set("Client side image map");
    $graph->legend->SetFont(FF_GULIM,FS_NORMAL,9);

// Create
//$p1 = new PiePlot($data);
    $p1 = new PiePlot3D($data);
//$p1->SetLegends(array("Jan","Feb","Mar","Apr","May","Jun","Jul"));

    $p1->SetLegends($keyword);

    $targ=$keyword;
    $alts=$keyword_encode;
    $p1->SetCSIMTargets($targ,$alts);

// Use absolute labels
//$p1->SetLabelType(1);
//$p1->SetLabelFormat("%d kr");

// Move the pie slightly to the left
    $p1->SetCenter(0.3,0.4);

    $graph->Add($p1);
    $graph->Stroke(GenImgName());


    $g .= $graph->GetHTMLImageMap("myimagemap");
    $g .= "<img src=\"".GenImgName()."?".rand()."\" ISMAP USEMAP=\"#myimagemap\" border=0>";

    return $g;
}
?>