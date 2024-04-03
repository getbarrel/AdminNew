<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include ("../graph/jpgraph.php");
include ("../graph/jpgraph_pie.php");
include ("../graph/jpgraph_pie3d.php");

function etcrefererGraph($vdate,$SelectReport=1){
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
        $sql = "Select visit_cnt, p.vetcreferer_id, p.vetcreferer_url from ".TBL_LOGSTORY_ETCREFERERINFO." p, ".TBL_LOGSTORY_BYETCREFERER." b where b.vdate = '$vdate' and p.vetcreferer_id = b.vetcreferer_id order by visit_cnt desc LIMIT 0,5";
    }else if($SelectReport == 2){
        /*
        $sql = "Select sum(visit_cnt) as visit_cnt, p.vetcreferer_id, p.vetcreferer_url
                    from ".TBL_LOGSTORY_ETCREFERERINFO." p, ".TBL_LOGSTORY_BYETCREFERER." b
                    where b.vdate between '$vdate' and '$vweekenddate'
                    and p.vetcreferer_id = b.vetcreferer_id
                    group by p.vetcreferer_id
                    order by visit_cnt desc
                    LIMIT 0,5";
        */
        $sql = "select p.vetcreferer_url, b.* from ".TBL_LOGSTORY_ETCREFERERINFO." p, 
					(select sum(visit_cnt) as visit_cnt, vetcreferer_id from ".TBL_LOGSTORY_BYETCREFERER." b 
					where b.vdate between '$vdate' and '$vweekenddate' 
					group by vetcreferer_id 
					order by visit_cnt desc 					
					) b
					where p.vetcreferer_id = b.vetcreferer_id LIMIT 0,10 ";
    }else if($SelectReport == 3){
        /*
        $sql = "Select sum(visit_cnt) as visit_cnt, p.vetcreferer_id, p.vetcreferer_url from ".TBL_LOGSTORY_ETCREFERERINFO." p, ".TBL_LOGSTORY_BYETCREFERER." b where b.vdate LIKE '".substr($vdate,0,6)."%' and p.vetcreferer_id = b.vetcreferer_id group by p.vetcreferer_id order by visit_cnt desc LIMIT 0,5";
        */
        $sql = "select p.vetcreferer_url, b.* from ".TBL_LOGSTORY_ETCREFERERINFO." p, 
					(select sum(visit_cnt) as visit_cnt, vetcreferer_id from ".TBL_LOGSTORY_BYETCREFERER." b 
					where b.vdate LIKE '".substr($vdate,0,6)."%' 
					group by vetcreferer_id 
					order by visit_cnt desc 					
					) b
					where p.vetcreferer_id = b.vetcreferer_id LIMIT 0,5 ";
    }

//	echo $sql;
    $fordb->query($sql);
    if($fordb->total > 0){
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            $data[$i] = returnZeroValue($fordb->dt[visit_cnt]);
            $topurl[$i] = substr($fordb->dt[vetcreferer_url],0,45)."...";
            $topurlLink[$i] = $fordb->dt[vetcreferer_url];
        }

        // Some data
        //$data = array(40,21,17,14,23);

        // Create the Pie Graph.
        $graph = new PieGraph(890,200);
        $graph->img->SetMargin(0,0,0,0);
        //$graph->SetShadow();

        // Set A title for the plot
        //$graph->title->Set("Client side image map");
        $graph->title->SetFont(FF_FONT1,FS_BOLD);

        // Create
        //$p1 = new PiePlot($data);
        $p1 = new PiePlot3D($data);
        //$p1->SetLegends(array("Jan","Feb","Mar","Apr","May","Jun","Jul"));

        $p1->SetLegends($topurl);

        $targ=$topurlLink;
        $alts=$topurl;
        $p1->SetCSIMTargets($targ,$alts);

        // Use absolute labels
        //$p1->SetLabelType(1);
        //$p1->SetLabelFormat("%d kr");

        // Move the pie slightly to the left
        $p1->SetCenter(0.2,0.4);

        $graph->Add($p1);
        $graph->Stroke(GenImgName());


        $g .= $graph->GetHTMLImageMap("myimagemap");
        $g .= "<img src=\"".GenImgName()."\" ISMAP USEMAP=\"#myimagemap\" border=0>";

        return $g;
    }
}