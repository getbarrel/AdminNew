<?
//include($_SERVER["DOCUMENT_ROOT"]."/forbiz/include/design.tmp.php");


function returnZeroValue($value){
	if($value == ""){
		return 0;	
	}else{
		return $value;
	}
}

function DisplpayArrowView($thisvalue,$val2){
	
}

function TitleBar($vtitle,$vdatestring=""){

$mstring = $mstring."<table cellpadding=0 cellspacing=0 width=615 border=0>
	<tr><td align=center height=31 valign=middle style='' background='../image/title_bar_head.gif' rowspan=2 width=212 >
		<div align=left id='revolution' style='position:relative;width:190px;top:0px;left:10px;font-size:9pt;color:white;line-height:1;filter:glow(color=black,strength=0)'>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>".$vtitle."</b>
		</div>
	</td>	
	</tr>
	<tr>
	<td width='280' background='../image/title_bar_bg.gif' style='background-position: 0% 100%;background-repeat:repeat-x;padding-left:10px;padding-bottom:6px;' width=212 align=left> $vdatestring </td>
	<td width='113' background='../image/title_bar_bg.gif' style='background-position: 0% 100%;background-repeat:repeat-x;border-right:' width=212 valign=top align=right><!--img src='image/upload.gif' align=absmiddle--> <img src='../image/reload.gif' align=absmiddle></td>
	</tr>";
	$mstring = $mstring."</table>";
	
	return $mstring;	
}

function getNameOfWeekday($WeekNum, $vdate,$type="datename"){
	$WeekName = array("일요일","월요일","화요일","수요일","목요일","금요일","토요일");	 
	

	if($type == "datename"){
		return date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$WeekNum)." (".$WeekName[$WeekNum].")";
	}else if($type == "dayname"){
		return date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$WeekNum);//." (".$WeekName[0].")";
	}else if($type == "monthname"){
		return substr(date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))),0,7)." 월";
	}else{
		return $WeekName[$WeekNum];
	}
}

function getTimeString($time){	
	return ($time-1).":00:00 - $time:00:00" ;	
}

function SetZefoFill($nchar,$fillstr,$nValue){	
	if (strlen($nValue) == $nchar){
		return $nValue;
	}else{
		return str_repeat($fillstr,$nchar-strlen($nValue)).$nValue;
	}
}

function CheckDivision($number, $division_number){
	if($division_number == 0){
		return 0;
	}else{
		return $number/$division_number;
	}
}

function ReturnDateFormat($vdateString){
	return date("Y-m-d",mktime(0,0,0,substr($vdateString,4,2),substr($vdateString,6,2),substr($vdateString,0,4)));
}



function BarChartDraw($sValue){

	If ($sValue >= 100){
		$mstring2 ="
		<table border=0 cellpadding=0  cellspacing=1 bgcolor=#000000 height=17>
		<tr><td width=15 height='100%' bgcolor=red></td></tr>
		</table>";	
	}else{
		$mstring2 ="
		<table border=0 cellpadding=0  cellspacing=1 bgcolor=#000000 height=17>
		<tr><td width=15 bgcolor=#ffffff></td></tr><tr><td height='".number_format($sValue,0)."%' bgcolor=red></td></tr>
		</table>";
	}
	return $mstring2;
}

function BarchartView($value1, $value2){
	
	if($value1 == 0 || $value2 == 0){
		$sValue = 0;
	}else if($value1 == "" || $value2 == ""){
		$sValue = 0;	
	}else{
		$sValue = $value1/$value2*100;	
	} 
	
	if ($sValue == "-"){	
			if (substr($sValue,2,strpos($sValue,".")) >= 10){
					$mstring = "<table border=0 cellpadding=0 cellspacing=0 ><tr><td width=50>".number_format($value2,0)."</td><td align=left width=15>". BarChartDraw($sValue) ."</td><td align=center width=60>". number_format($sValue,1) ." %</td></tr></table>";
			}else{
					$mstring = "<table border=0 cellpadding=0  cellspacing=0><tr><td width=50>".number_format($value2,0)."</td><td align=left width=15>". BarChartDraw($sValue) ."</td><td align=center width=60>". number_format($sValue,1) ." %</td></tr></table>";
			}
		
	}else if (substr($sValue,0,1) != "-" && substr($sValue,0,3) == 0.0){
					$mstring = "<table border=0 cellpadding=0  cellspacing=0><tr><td width=50>".number_format($value2,2)."</td><td align=left width=15><b>-</b></td><td align=center width=60>". number_format($sValue,0) ." %</td></tr></table>";			
	
	}else{
	
			//if (substr($sValue,strpos($sValue,".")) >= 10){
			if($value2 > 1){
					$mstring = "<table border=0 cellpadding=0  cellspacing=0><tr><td width=50>".number_format($value2,0)."</td><td align=left width=15>". BarChartDraw($sValue) ."</td><td align=center width=60>". number_format($sValue,1) ." %</td></tr></table>";
			}else{
					$mstring = "<table border=0 cellpadding=0  cellspacing=0><tr><td width=50>".number_format($value2,2)."</td><td align=left width=15>". BarChartDraw($sValue) ."</td><td align=center width=60>". number_format($sValue,1) ." %</td></tr></table>";
			}
			
	}	
	return $mstring;
}



?>