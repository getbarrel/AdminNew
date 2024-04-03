<?php
include ("../jpgraph.php");
include ("../jpgraph_line.php");

$ydata = array(11,3,8,12,5,1,9,13,5,7);
$ydata2 = array(1,19,15,7,22,14,5,9,21,13);
//$ydata3 = array(1,13,11,17,12,16,5,19,11,13);

// Create the graph. These two calls are always required
$graph = new Graph(300,200,"auto");	
$graph->SetScale("textlin");

// Create the linear plot
$lineplot=new LinePlot($ydata);

$lineplot2=new LinePlot($ydata2);

//$lineplot3=new LinePlot($ydata3);

// Add the plot to the graph
$graph->Add($lineplot);
$graph->Add($lineplot2);
//$graph->Add($lineplot3);

$graph->img->SetMargin(40,20,20,40);
$graph->title->Set("Example 4");
$graph->xaxis->title->Set("X-title");
$graph->yaxis->title->Set("Y-title");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$lineplot->SetColor("blue");
$lineplot->SetWeight(2);

$lineplot2->SetColor("orange");
$lineplot2->SetWeight(2);

$graph->yaxis->SetColor("red");
$graph->yaxis->SetWeight(2);
$graph->SetShadow();

// Display the graph
$graph->Stroke();
?>
