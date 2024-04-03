<?php
include ("../jpgraph.php");
include ("../jpgraph_pie.php");

// Some data
$data = array(30,21,17,14,23,12,16,26,21,32,11,15,22,22);

// Create the Pie Graph.
$graph = new PieGraph(300,200,"auto");
$graph->SetColor("steelblue");
$graph->SetShadow();

// Set A title for the plot
$graph->title->Set("Example 1 Pie plot");
$graph->title->SetFont(FF_VERDANA,FS_BOLD,14); 
$graph->title->SetColor("yellow");

// Create pie plot
$p1 = new PiePlot($data);
//$p1->SetSliceColors(array("red","blue","yellow"));
$p1->SetTheme("earth");

$p1->SetFont(FF_ARIAL,FS_NORMAL,10);
$p1->ExplodeSlice(1);
$p1->SetFontColor("white");

$graph->Add($p1);
$graph->Stroke();

?>


