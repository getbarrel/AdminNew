<?php
include ("../logstory/graph/jpgraph.php");
include ("../logstory/graph/jpgraph_pie.php");
include ("../logstory/graph/jpgraph_pie3d.php");




function company_3DPie(){
	// Some data
$db = new Database;

/*
	$sql = "SELECT b.sido, count(*)
			FROM ".TBL_COMMON_MEMBER_DETAIL." a, ".TBL_SHOP_ZIP." b
			where replace(a.zip,'-','') = b.zip_code and a.zip != '' and a.zip != '-'
			group by b.sido
			LIMIT 10";
*/			
/*
	$tmp_sql = "create temporary table ".TBL_SHOP_ZIP."_tmp ENGINE = MEMORY select sido, SUBSTRING(zip_code,1,3) as zip_code, count(*) from ".TBL_SHOP_ZIP." group by zip_code";
	$sql = "SELECT b.sido, count(*) as mem_cnt
			FROM ".TBL_COMMON_MEMBER_DETAIL." a, ".TBL_SHOP_ZIP."_tmp b
			where SUBSTRING(a.zip,1,3) = b.zip_code and a.zip != '' and a.zip != '-'
			group by b.sido
			order by mem_cnt desc
			LIMIT 10";
*/	
	$sql = "SELECT SUBSTRING(AES_DECRYPT(UNHEX(a.addr1),'".$db->ase_encrypt_key."'),1,3) as sido, count(*) as mem_cnt
			FROM ".TBL_COMMON_MEMBER_DETAIL." a
			where a.zip != '' and a.zip != '-'
			group by sido
			order by mem_cnt desc
			LIMIT 10";
	
	//echo $sql;
	//$db->query($tmp_sql);
	$db->query($sql);
	
	if($db->total > 10){
		$db->query($sql." limit 10 ");
	}

	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);	
		$job_total = $job_total + $db->dt[1];	
	}

	if($job_total == 0){
		return "해당 조건에 결과 값이 모두 0 입니다.";
		exit;	
	}


	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
		
		if($job_total == 0){
			$job_rate = 0;	
		}else{
			$job_rate = number_format($db->dt[1]/$job_total*100,1);
		}
		
		//$legends[$i] = $db->dt[standard_industry_branch_1];
		//$legends[$i] = iconv('EUC-KR','UTF-8',$db->dt[0]."(".$db->dt[1].")");
		$legends[$i] = $db->dt[0]."(".$db->dt[1].")";
		//$legends[$i] = $i;
		$data[$i] = $job_rate;
	}
	
	//$data = array(40,21,17,27,23);
	
	// Create the Pie Graph. 
	if($db->total < 5){
		$graph = new PieGraph(220,250, "auto");	
	}else if($db->total < 7){
		$graph = new PieGraph(220,340, "auto");	
	}else if($db->total < 11){
		$graph = new PieGraph(220,370, "auto");	
	}else{
		$graph = new PieGraph(220,390, "auto");	
	}
	
	$graph->img->SetMargin(0,0,0,0);
	//$graph->SetScale("textlin");
	$graph->SetFrame(false,array(146,207,215),0);
	$graph->legend->Pos(0.4,0.8,"center","bottom");
	$graph->legend->SetFont(FF_GULIM,FS_NORMAL,9);
	
	//$graph->SetShadow();
	
	// Set A title for the plot
	//$graph->title->Set("3D Pie Client side image map");
	//$graph->title->SetFont(FF_FONT1,FS_BOLD);
	
	// Create
	$p1 = new PiePlot3D($data);	
	$p1->SetLegends($legends);
	$targ=array("?v=1","?v=2","?v=3",
				"?v=4","?v=5","?v=6");
	$alts=array("val=%v","val=%v","val=%v","val=%v","val=%v","val=%v");
	$p1->SetCSIMTargets($targ,$alts);
	
	// Use absolute labels
	$p1->SetLabelType(0);
	//$p1->SetLabelFormat("%d kr");
	
	// Move the pie slightly to the left
	$p1->SetCenter(0.5,0.2);
	
	$graph->Add($p1);
	// Display the graph
	$graph->Stroke(GenImgName());
	
	
	$mstring =  $graph->GetHTMLImageMap("myimagemap");
	$mstring .=  "<img src=\"".GenImgName()."\" ISMAP USEMAP=\"#myimagemap\" border=0>";
	
	return $mstring;
}

/*
include ("../graph/jpgraph.php");
include ("../graph/jpgraph_bar.php");

$datay=array(12,8,19,3,10,5);
$datax=array("Jan JanJanJanJanJan","Feb JanJanJanJan","Mar JanJan","Apr JanJanJanJanJan","May JanJanJanJan");


// Create the graph. These two calls are always required
$graph = new Graph(500,300,"auto");	
$graph->img->SetMargin(140,30,20,30);
//$graph->SetAngle(90);
$graph->SetScale("textlin");
$graph->SetFrame(false,array(0,0,0),0);
//$graph->SetShadow();

// Create a bar pot
$bplot = new BarPlot($datay);
//$bplot->SetFillColor("orange");
$targ=array("bar_clsmex2.php","bar_clsmex2.php","bar_clsmex2.php","bar_clsmex2.php","bar_clsmex2.php","bar_clsmex2.php");
$alts=array("val=%d","val=%d","val=%d","val=%d","val=%d","val=%d");
$bplot->SetCSIMTargets($targ,$alts);

$graph->Add($bplot);


//$graph->title->Set(iconv('EUC-KR','UTF-8','예제 18'));
//$graph->title->Set('Example 18');
//$graph->xaxis->title->Set("X-title");
//$graph->yaxis->title->Set("Y-title");

//$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->title->SetFont(FF_GULIM,FS_NORMAL,9);

$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->SetTickLabels($datax);
//$graph->xaxis->SetLabelAngle(90);

// Display the graph
//$graph->Stroke();


// Display the graph
$graph->Stroke(GenImgName());


echo $graph->GetHTMLImageMap("myimagemap");
echo "<img src=\"".GenImgName()."\" ISMAP USEMAP=\"#myimagemap\" border=0>";
*/
?>
