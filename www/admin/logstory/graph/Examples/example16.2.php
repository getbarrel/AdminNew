<?php
include ("../jpgraph.php");
include ("../jpgraph_line.php");
include ("../jpgraph_bar.php");

$l1datay = array(11,9,2,4,3,13,17);
$l2datay = array(23,12,5,19,17,10,15);
$datax=array("Jan","Feb","Mar","Apr","May");

// Create the graph. 
$graph = new Graph(400,200,"auto");	
$graph->img->SetMargin(40,130,20,40);
$graph->SetScale("textlin");
$graph->SetShadow();

// Create the linear error plot
$l1plot=new LinePlot($l1datay);
$l1plot->SetColor("red");
$l1plot->SetWeight(2);
$l1plot->SetLegend("Prediction");

// Create the bar plot
$l2plot = new BarPlot($l2datay);
$l2plot->SetFillColor("orange");
$l2plot->SetLegend("Result");

// Add the plots to the graph
$graph->Add($l2plot);
$graph->Add($l1plot);

$graph->title->Set("Example 16.2");
$graph->xaxis->title->Set("X-title");
$graph->yaxis->title->Set("Y-title");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

//$graph->xaxis->SetTickLabels($datax);
//$graph->xaxis->SetTextTicksInterval(2);

// Display the graph
$graph->Stroke();
?>
