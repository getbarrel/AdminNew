<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include ("../graph/jpgraph.php");
include ("../graph/jpgraph_pie.php");
include ("../graph/jpgraph_pie3d.php");

//chmod($_SERVER["DOCUMENT_ROOT"]."/manage/logstory/report/", 0777);

function keywordGraph($vdate,$SelectReport=1){
    global $cid, $depth;
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
        if($fordb->dbms_type == "oracle"){
            $sql = "select replace(keyword,' ','') as keyword, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
							where k.kid = b.kid and  b.vdate = '$vdate' and substr(r.cid,1,9) = substr(b.vreferer_id,1,9)
							and r.depth = 2 and keyword <> '' and keyword not like '%?%' and substr(keyword,1,1) != '%'
							group by keyword order by visit_cnt desc limit 0,10";
        }else{

            $sql = "select '1' as vieworder, k.kid,replace(keyword,' ','') as keyword,cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
							where k.kid = b.kid and  b.vdate = '$vdate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)
							and r.depth = 2 and keyword <> '' and keyword not like '%?%' and substring(keyword,1,1) != '%'
							group by keyword order by visit_cnt desc limit 10";

            /*
            $sql = "select * from (
                        select '1' as vieworder, k.kid,replace(keyword,' ','') as keyword,cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
                            from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
                            where k.kid = b.kid and  b.vdate = '$vdate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)
                            and r.depth = 2 and keyword <> '' and keyword not like '%?%' and substring(keyword,1,1) != '%'
                            group by keyword order by visit_cnt desc
                            ";
            $sql .= "union
                        select '2' as vieworder, k.kid,'기타' as keyword,cname, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
                            from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
                            where k.kid = b.kid and  b.vdate = '$vdate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)
                            and r.depth = 2 and keyword <> '' and keyword not like '%?%' and substring(keyword,1,1) != '%'
                            group by keyword order by visit_cnt desc limit 10,10000
                            ) data
                            group by vieworder,  keyword order by vieworder asc , visit_cnt desc limit 11";
            */

        }

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        $sql = "select k.kid,vreferer_id, cname, replace(keyword,' ','') as keyword, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
							where k.kid = b.kid and  b.vdate between '$vdate' and '$vweekenddate' and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)
							and r.depth = 2 and keyword <> '' and substring(keyword,1,1) != '%'
							group by keyword order by visit_cnt desc limit 10";

        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){

        $sql = "select k.kid,vreferer_id, cname, replace(keyword,' ','') as keyword, sum(b.visit_cnt) as visit_cnt, sum(b.visitor_cnt) as visitor_cnt
							from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYKEYWORD." b, ".TBL_LOGSTORY_KEYWORDINFO." k
							where k.kid = b.kid and b.vdate LIKE '".substr($vdate,0,6)."%'  and substring(r.cid,1,9) = substring(b.vreferer_id,1,9)
							and r.depth = 2 and keyword <> '' and substring(keyword,1,1) != '%'
							group by k.kid,vreferer_id, cname, keyword order by visit_cnt desc limit 10";

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }
//	echo $sql;
    $fordb->query($sql);

    if($fordb->total > 0){

        for($i=0, $j=0;($i < $fordb->total && $i < 10);$i++, $j++){
            $fordb->fetch($i);
            $data[$j] = returnZeroValue($fordb->dt[visit_cnt]);

            $keyword[$j] = iconv('UTF-8','UTF-8',$fordb->dt[keyword]);
            $keyword_encode[$j] = $fordb->dt[keyword];

        }
        //echo nl2br($sql2);
        //$fordb->query($sql2);
        /*
        for($i=$i;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            $etc_visit_cnt += $fordb->dt[visit_cnt];
        }
        */
        //$j++;
        //	$data[$j] = $etc_visit_cnt;
        //	$keyword[$j] = '기타';
        //	$keyword_encode[$j] = '기타';
        //	print_r($data);
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
    }
    return $g;
}
?>


