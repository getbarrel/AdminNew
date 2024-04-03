<?php
include ("../jpgraph.php");
include ("../jpgraph_line.php");

$ydata = array(11,3,8,12,5,1,9,13,5,8);

// Create the graph. These two calls are always required
$graph = new Graph(550,250,"auto");	
$graph->SetScale("textlin");
$graph->img->SetMargin(30,130,40,50);  //аб,©Л,╩С,го
$graph->xaxis->SetFont(FF_FONT1,FS_BOLD);
$graph->title->Set("Dashed lineplot");

// Create the linear plot
$lineplot=new LinePlot($ydata);
$lineplot->SetLegend("page view");
$lineplot->SetColor("blue");

// Style can also be specified as SetStyle([1|2|3|4]) or
// SetStyle("solid"|"dotted"|"dashed"|"lobgdashed")
$lineplot->SetStyle("dashed");


// Add the plot to the graph
$graph->Add($lineplot);

// Display the graph
$graph->Stroke();
?>
