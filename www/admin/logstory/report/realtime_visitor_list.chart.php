<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include ("../graph/jpgraph.php");
include ("../graph/jpgraph_pie.php");
include ("../graph/jpgraph_pie3d.php");

//chmod($_SERVER["DOCUMENT_ROOT"]."/manage/logstory/report/", 0777);

function RealTimeVisitorGraph($page_view_infos, $pageinfos){
    global $shmop,$abcd;
    //echo count($page_view_infos);
    //print_r($page_view_infos);
    //print_r(array_count_values($page_view_infos));
    //exit;
    $i=0;
    foreach ($page_view_infos as $key => $values) {

        $data[$i] = returnZeroValue($values);
        //	echo "aaa :".$pageinfos[$key]['page_ko_name'];
        $keyword[$i] = iconv('UTF-8','UTF-8',strip_tags($pageinfos[$key]['page_ko_name']));
        $keyword_encode[$i] = $values;
        $i++;
    }


    /*
    for($i=0;$i < count($page_view_infos);$i++){
        $data[$i] = returnZeroValue($page_view_infos[$i][value]);
echo "aaA".$page_view_infos[$i][value];
        $keyword[$i] = iconv('UTF-8','UTF-8',$page_view_infos[$i]);
        $keyword_encode[$i] = $page_view_infos[$i][value];
    }
    */
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


