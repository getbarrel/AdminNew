<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include ("../graph/jpgraph.php");
include ("../graph/jpgraph_pie.php");
include ("../graph/jpgraph_pie3d.php");

//chmod($_SERVER["DOCUMENT_ROOT"]."/manage/logstory/report/", 0777);

function TopPageGraph($vdate,$SelectReport=1){
    global $pageinfos, $first_page;
    $g = "";
//print_r($pageinfos);
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

    if($SelectReport == 1){
        $sql = "Select b.ncnt, p.pageid, p.vurl  from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b 
					where b.vdate = '$vdate' and p.pageid = b.pageid 
					order by ncnt desc 
					LIMIT 0,8";
    }else if($SelectReport == 2){
        $sql = "Select sum(ncnt) as ncnt, p.pageid, p.vurl from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b 
					where b.vdate between '$vdate' and '$vweekenddate' and p.pageid = b.pageid 
					group by p.pageid , p.vurl
					order by ncnt desc 
					LIMIT 0,8";
    }else if($SelectReport == 3){
        $sql = "Select sum(ncnt) as ncnt, p.pageid, p.vurl 
					from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b 
					where b.vdate LIKE '".substr($vdate,0,6)."%' 
					and p.pageid = b.pageid 
					group by p.pageid, p.vurl 
					order by ncnt desc 
					LIMIT 0,8";
    }

    //echo $sql;
    $fordb->query($sql);

    if($fordb->total > 0){
        for($i=0;$i < $fordb->total;$i++){
            $fordb->fetch($i);

            $data[$i] = returnZeroValue($fordb->dt['ncnt']);
            //$topurl[$i] = $fordb->dt[vurl];
            $topurl[$i] = ($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? strip_tags($pageinfos[$fordb->dt['pageid']]['page_ko_name']):$pageinfos[$fordb->dt['pageid']]['vurl']);
            $topurl_link[$i] = "?SelectReport=".$SelectReport."&pageid=".$fordb->dt['pageid'];
            if(isset($_GET['pageid']) && ($_GET['pageid']==$fordb->dt['pageid'])){
                $first_page['pageid'] = $fordb->dt['pageid'];
                $first_page['page_ko_name'] = ($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? strip_tags($pageinfos[$fordb->dt['pageid']]['page_ko_name']):$pageinfos[$fordb->dt['pageid']]['vurl']);;
            }else if($i==0){
                $first_page['pageid'] = $fordb->dt['pageid'];
                $first_page['page_ko_name'] = ($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? strip_tags($pageinfos[$fordb->dt['pageid']]['page_ko_name']):$pageinfos[$fordb->dt['pageid']]['vurl']);;
            }
            //$topurl_encode[$i] = $i;
            $topurl_encode[$i] = ($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? iconv('UTF-8','UTF-8',strip_tags($pageinfos[$fordb->dt['pageid']]['page_ko_name'])):$pageinfos[$fordb->dt['pageid']]['vurl']);
        }

        // Some data
        //$data = array(40,21,17,14,23);
        //print_r($data);
        // Create the Pie Graph.
        $graph = new PieGraph(400,200);
        $graph->img->SetMargin(0,0,0,0);
        //$graph->SetShadow();

        // Set A title for the plot
        //$graph->title->Set("Client side image map");
        $graph->legend->SetFont(FF_GULIM,FS_NORMAL,9);
        $graph->SetFrame(false,array(146,207,215),0);
        // Create
        //$p1 = new PiePlot($data);
        $p1 = new PiePlot3D($data);
        //$p1->SetLegends(array("Jan","Feb","Mar","Apr","May","Jun","Jul"));

        $p1->SetLegends($topurl_encode);
        //print_r($topurl_encode);
        $targ=$topurl_link;
        $alts=$topurl;
        $p1->SetCSIMTargets($targ,$alts);

        // Use absolute labels
        //$p1->SetLabelType(1);
        //$p1->SetLabelFormat("%d kr");

        // Move the pie slightly to the left
        $p1->SetCenter(0.3,0.4);

        $graph->Add($p1);
        $graph->Stroke(GenImgName());


        $g .= $graph->GetHTMLImageMap("myimagemap");
        $g .= "<img src=\"".GenImgName()."\" ISMAP USEMAP=\"#myimagemap\" border=0>";
    }else{
        $g = "<div style='text-align:center;'>그래프가 생성되지 않았습니다.</div>";
    }
    return $g;
}

?>


