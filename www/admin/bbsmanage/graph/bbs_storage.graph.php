<?php
//@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include ($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/graph/jpgraph.php");
include ($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/graph/jpgraph_pie.php");
include ($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/graph/jpgraph_pie3d.php");

//chmod($_SERVER["DOCUMENT_ROOT"]."/manage/logstory/report/", 0777);

function BbsStorageGraph(){
global $pageinfos, $first_page;

$bbs_info = getMyService("BASIC_ADD","BBS");
//getMyDBinfo("bbs");

$bbs_db_storage = getMyDBinfo("bbs");
$data[0] = returnZeroValue($bbs_db_storage);
$data[1] = returnZeroValue($bbs_info["service_unit_value"]*1024);

$topurl_encode[0] = "게시판 DB 용량 : ".$bbs_db_storage;
$topurl_encode[1] = "게시판 파일 용량 : ".($bbs_info["service_unit_value"])." MB";
/*
for($i=0;$i<$fordb->total;$i++){	
	$fordb->fetch($i);
	$data[$i] = returnZeroValue($fordb->dt[ncnt]);
	//$topurl[$i] = $fordb->dt[vurl];
	$topurl[$i] = ($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? strip_tags($pageinfos[$fordb->dt['pageid']]['page_ko_name']):$pageinfos[$fordb->dt['pageid']][vurl]);
	$topurl_link[$i] = "?SelectReport=".$SelectReport."&pageid=".$fordb->dt['pageid'];
	if($_GET[pageid]==$fordb->dt['pageid']){
		$first_page[pageid] = $fordb->dt['pageid'];
		$first_page[page_ko_name] = ($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? strip_tags($pageinfos[$fordb->dt['pageid']]['page_ko_name']):$pageinfos[$fordb->dt['pageid']][vurl]);;
	}else if($i==0){
		$first_page[pageid] = $fordb->dt['pageid'];
		$first_page[page_ko_name] = ($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? strip_tags($pageinfos[$fordb->dt['pageid']]['page_ko_name']):$pageinfos[$fordb->dt['pageid']][vurl]);;
	}
	//$topurl_encode[$i] = $i;
	$topurl_encode[$i] = ($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? iconv('UTF-8','UTF-8',strip_tags($pageinfos[$fordb->dt['pageid']]['page_ko_name'])):$pageinfos[$fordb->dt['pageid']][vurl]);
}
*/	
// Some data
//$data = array(40,21,17,14,23);

// Create the Pie Graph. 
$graph = new PieGraph(400,100);
$graph->img->SetMargin(0,0,0,0);

//$graph->SetShadow("#fff7da");
//print_r($graph);
//
// Set A title for the plot
//$graph->title->Set("Client side image map");
$graph->legend->SetFont(FF_GULIM,FS_NORMAL,9);
$graph->SetFrame(false,array(146,207,215),0);
// Create
//$p1 = new PiePlot($data);

$p1 = new PiePlot3D($data);
//print_r($p1);
//$p1->SetFillColor("#fff7da");
//$p1->SetLegends(array("Jan","Feb","Mar","Apr","May","Jun","Jul"));

$p1->SetLegends($topurl_encode);
//print_r($topurl_encode);
$targ=$topurl_link;
$alts=$topurl_encode;
$p1->SetCSIMTargets($targ,$alts);

// Use absolute labels
//$p1->SetLabelType(1);
//$p1->SetLabelFormat("%d kr");

// Move the pie slightly to the left
$p1->SetCenter(0.3,0.4);

$graph->Add($p1);
$graph->Stroke("./graph/".GenImgName());


$g .= $graph->GetHTMLImageMap("myimagemap");
$g .= "<img src=\"./graph/".GenImgName()."\" ISMAP USEMAP=\"#myimagemap\" border=0>";

return $g;
}

?>


