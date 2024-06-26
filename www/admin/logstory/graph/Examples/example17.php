<?php
include ("../jpgraph.php");
include ("../jpgraph_line.php");

$new_datay = 			array(11,7,5,8,3,5,5,4,8,6,5,5,3,2,5,1,2,0);
$inprogress_datay = 	array( 4,5,4,5,6,5,7,4,7,4,4,3,2,4,1,2,2,1);
$fixed_datay = 		array(4,5,7,10,13,15,15,22,26,26,30,34,40,43,47,55,60,62);

// Create the graph. These two calls are always required
$graph = new Graph(300,200,"auto");	
$graph->img->SetMargin(40,30,20,40);
$graph->SetScale("textlin");
$graph->SetShadow();

// Create the linear plots for each category
$dplot[] = new LinePLot($new_datay);
$dplot[] = new LinePLot($inprogress_datay);
$dplot[] = new LinePLot($fixed_datay);

$dplot[0]->SetFillColor("red");
$dplot[1]->SetFillColor("blue");
$dplot[2]->SetFillColor("green");

// Create the accumulated graph
$accplot = new AccLinePlot($dplot);

// Add the plot to the graph
$graph->Add($accplot);

$graph->xaxis->SetTextTicksInterval(2);
$graph->title->Set("Example 17");
$graph->xaxis->title->Set("X-title");
$graph->yaxis->title->Set("Y-title");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

// Display the graph
$graph->Stroke();
?>
