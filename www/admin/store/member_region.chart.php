<?php
include_once("../class/layout.class");
  # Include FusionCharts PHP Class
include('../logstory/FusionCharts/Class/FusionCharts_Gen.php');
	
function PieChart(){	
  # Create Column3D chart Object 
  $FC = new FusionCharts("Bar2D","220","450"); 
  # set the relative path of the swf file
  $FC->setSWFPath("../logstory/FusionCharts/Charts/");
/*
  # Set chart attributes
  $strParam="caption=;baseFontSize=12;showNames=1;showValues=1;numberPrefix=;decimalPrecision=0;formatNumberScale=1;pieRadius=100;pieSliceDepth=25;pieYScale=45;pieBorderAlpha=100;pieBorderColor=000000;showPercentageInLabel=1";
  $strParam="caption=;";
  $strParam.="baseFontSize=12;";
  $strParam.="bgAlpha=0;";
  $strParam.="bgColor=f1f1f1;";
 	$strParam.="showNames=1;";
  $strParam.="showValues=1;";
  $strParam.="numberPrefix=1;";
  $strParam.="decimalPrecision=0;";
  $strParam.="formatNumberScale=1;";
  $strParam.="pieRadius=30;"; // 전체 화면에 파이 비율
  $strParam.="pieSliceDepth=25;";
  $strParam.="pieYScale=20;";
  $strParam.="pieBorderAlpha=40;";
  $strParam.="pieFillAlpha=90;";  
  $strParam.="pieBorderColor=000000;";
  $strParam.="pieBorderThickness=1;";
  $strParam.="showPercentageInLabel=1;";
 */ 
   $strParam="caption=;";
  $strParam.="baseFontSize=12;";
  $strParam.="bgAlpha=0;";
  $strParam.="bgColor=f1f1f1;";
 	$strParam.="showNames=1;";
  $strParam.="showValues=1;";
  $strParam.="numberPrefix=;";
  $strParam.="decimalPrecision=0;";
  $strParam.="formatNumberScale=100;";
   // 전체 화면에 파이 비율
  $strParam.="chartRightMargin=0;";
  $strParam.="chartRightMargin=50;";
  $strParam.="numDivLines=0;";
  $strParam.="canvasBorderThickness=1;";  
  $strParam.="canvasBorderColor=efefef;";
  $strParam.="showBarShadow=1;";
  $strParam.="showPercentageInLabel=1;";
  $strParam.="startValue=1000;";
  $strParam.="endValue=1000;";
  $strParam.="isTrendZone=1;";
  $strParam.="showLimits=0;";
  
  
  $FC->setChartParams($strParam);

  # add chart values and category names
  $db = new Database;

if ($admininfo[mall_type] == "O"){
	$sql = "SELECT SUBSTRING(AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."'),1,6) as sido, count(*) as mem_cnt
			FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd 
			where cmd.zip != '' and cmd.zip != '-' and cu.code = cmd.code and cu.mem_type in ('M','C','F','S')
			group by sido
			order by mem_cnt desc
			";
}else{
	$sql = "SELECT SUBSTRING(AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."'),1,6) as sido, count(*) as mem_cnt
			FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd 
			where cmd.zip != '' and cmd.zip != '-' and cu.code = cmd.code and cu.mem_type in ('M','C','F')
			group by sido
			order by mem_cnt desc
			";
}

  
	
	//echo $sql;
	//$db->query($tmp_sql);
	$db->query($sql);
	
	if($db->total > 10){
		$db->query($sql." limit 16 ");
	}

	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);	
		$job_total = $job_total + $db->dt[1];	
	}

	if($job_total == 0){
		return "해당 조건에 결과 값이 모두 0 입니다.";
		exit;	
	}

	$colors[0] = "053c44";
	$colors[1] = "075763";
	$colors[2] = "0c8597"; 
	$colors[3] = "0faec5";
	$colors[4] = "2cd3ec";
	$colors[5] = "6bd8e8";
	$colors[6] = "a0e0ea";
	$colors[7] = "b2e5ed";
	$colors[8] = "dbeef1";	
	$colors[9] = "f7fbfc";
	$colors[10] = "dbeef1";
	
	
		$colors[0] = "953505";
	$colors[1] = "953505";
	$colors[2] = "cc4d0e"; 
	$colors[3] = "e85710";
	$colors[4] = "f76d29";
	$colors[5] = "f88e59";
	$colors[6] = "f7ac87";
	$colors[7] = "f8c2a7";
	$colors[8] = "f9daca";	
	$colors[9] = "f8e6dd";
	$colors[10] = "faf5f3";
	

/*
	$colors[0] = "66cdaa"; 
	$colors[1] = "eec591";
	$colors[2] = "7ac5cd";
	$colors[3] = "66cd00";
	$colors[4] = "458b00";
	$colors[5] = "00cdcd";
	$colors[6] = "008b8b";
	$colors[7] = "daa520";
	$colors[8] = "8b008b";
	$colors[9] = "bcee68";
*/	

/*
	$colors[0] = "AFD8F8"; 
	$colors[1] = "F6BD0F";
	$colors[2] = "8BBA00";
	$colors[3] = "FF8E46";
	$colors[4] = "008E8E";
	$colors[5] = "D64646";
	$colors[6] = "8E468E";
	$colors[7] = "588526";
	$colors[8] = "B3AA00";
	$colors[9] = "9D080D";
	$colors[10] = "A186BE";
	
	$colors[0] = "ff7939"; 
	$colors[1] = "006F00";
	$colors[2] = "0099FF";
	$colors[3] = "CCCC00";
	$colors[4] = "D64646";
	$colors[5] = "ce5c5c";
	$colors[6] = "A186BE";
	$colors[7] = "588526";
	$colors[8] = "736a63";
	$colors[9] = "9D080D";
	$colors[10] = "8c0001";
	*/
/*	
	$colors[0] = "ce5c5c"; 
	$colors[1] = "fe0000";
	$colors[2] = "8c0001";
	$colors[3] = "c0d84e";
	$colors[4] = "1e90ff";
	$colors[5] = "0000fe";
	$colors[6] = "ffa07a";
	$colors[7] = "ffa07a";
	$colors[8] = "ffff01";
	$colors[9] = "ffd701";
	$colors[10] = "f8e77f";
	$colors[11] = "ff8b00";
	$colors[12] = "16aa52";
	$colors[13] = "6baf48";	

	$colors[14] = "458ccc";
	$colors[15] = "01ffff";
	$colors[16] = "9931cc";
	$colors[17] = "ee82ef";
	$colors[18] = "89246a";
	$colors[19] = "7963ab";
	$colors[20] = "bd7f42";
*/	
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
		$FC->addChartData($db->dt[1],"name=".$db->dt[0].";color=".$colors[$i]);
		
	}
 
   $mstring = " <script language='javascript' src='../logstory/FusionCharts/JSClass/FusionCharts.js'></script>"; 
   $mstring .= $FC->renderChart(false, false);
   
   return $mstring;
  
}