<?
include("../class/layout.class");
include("../include/static_util.php");
include("../include/commerce_menu.php");
include("../class/calender.class");

//include($_SERVER["DOCUMENT_ROOT"]."/forbiz/class/reportpage.class");
//include($_SERVER["DOCUMENT_ROOT"]."/forbiz/report/etcreferer.chart.php");

function ReportTable($vdate,$SelectReport=1){
	$pageview01 = 0;
	$fordb = new Database();
	$fordb2 = new Database();
	$fordb3 = new Database();
	
	
	if($SelectReport == ""){
		$SelectReport = 1;	
	}
	if($vdate == ""){
		$vdate = date("Ymd", time());
		$vyesterday = date("Ymd", time()-84600);
		$voneweekago = date("Ymd", time()-84600*7);
		$vtwoweekago = date("Ymd", time()-84600*14);
		$vfourweekago = date("Ymd", time()-84600*28);
		$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));		
		$vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)));		
	}else{
		if($SelectReport ==3){
			$vdate = substr($vdate,0,6)."01";
		}
		$vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
		$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);		
		$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
		$vtwoweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*14);
		$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
		$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));		
		$vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)));		
	}
	
	if($SelectReport == 1){
		$nLoop = 24;
	}else if($SelectReport ==2){
		$nLoop = 7;
	}else if($SelectReport ==3){
		$nLoop = date("t", mktime(0, 0, 0, substr($vdate,4,2), substr($vdate,6,2), substr($vdate,0,4)));
	}

	if($SelectReport == 1){		
		$sql = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$vdate' and step6 = 1 group by vdate order by vdate";
		$sql2 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$vyesterday' and step6 = 1 group by vdate order by vdate";
		$sql3 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$voneweekago' and step6 = 1 group by vdate order by vdate";
		
		$dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
		$title1 = "해당일";
		$title2 = "1일전";
		$title3 = "일주전";
	}else if($SelectReport == 2){				
		
		$sql = "SELECT vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_COMMERCE_SALESTACK." c where vdate between '$vdate' and '$vweekenddate' and step6 = 1 group by vdate ";
				
		$dateString = "주간 : ". getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
		
	}else if($SelectReport == 3){
		$sql = "SELECT c.vdate as vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_COMMERCE_SALESTACK." c where vdate LIKE '".substr($vdate,0,6)."%' and step6 = 1 group by c.vdate ";
		
		$dateString = getNameOfWeekday(0,$vdate,"monthname");
		
	}
	
	
	
		
	
	
	$mstring = $mstring.TitleBar("구매고객",$dateString);	
if($SelectReport == 1){	
	$fordb->query($sql);
	$fordb2->query($sql2);
	$fordb3->query($sql3);
	
	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=615   STYLE='TABLE-LAYOUT:fixed'>\n";	
	$mstring = $mstring."<tr height=25 align=center style='font-weight:bold'><td class=s_td width=150>날짜</td><td class=m_td width=165>매출액</td><td class=m_td width=150 nowrap>구매자수</td><td class=e_td width=150 nowrap>구매수량</td></tr>\n";
	
//	if($fordb->total == 0){
//		$mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";	
//	}else{
	
		
	$fordb->fetch(0);
	$fordb2->fetch(0);
	$fordb3->fetch(0);
			
			$mstring .= "
			<tr height=30 bgcolor=#ffffff >
			<td bgcolor=#efefef id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:25px;padding-right:5px' nowrap>$title1 </td>
			<td bgcolor=#ffffff id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=center>".number_format(returnZeroValue($fordb->dt[sales]),0)."</td>
			<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".returnZeroValue($fordb->dt[ucnt])."</td>
			<td bgcolor=#ffffff align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".returnZeroValue($fordb->dt[cnt])."</td>			
			</tr>";
			$i = $i + 1;
			$mstring .= "
			<tr height=30 bgcolor=#ffffff >
			<td bgcolor=#efefef id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:25px;padding-right:5px' nowrap>$title2 </td>
			<td bgcolor=#ffffff id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=right>".BarchartView($fordb->dt[sales],$fordb2->dt[sales])."</td>
			<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".BarchartView(returnZeroValue($fordb->dt[ucnt]),returnZeroValue($fordb2->dt[ucnt]))."</td>
			<td bgcolor=#ffffff align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".BarchartView(returnZeroValue($fordb->dt[cnt]),returnZeroValue($fordb2->dt[cnt]))."</td>			
			</tr>";
			$i = $i + 1;
			$mstring .= "
			<tr height=30 bgcolor=#ffffff >
			<td bgcolor=#efefef id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:25px;padding-right:5px' nowrap>$title3 </td>
			<td bgcolor=#ffffff id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=right>".BarchartView(returnZeroValue($fordb->dt[sales]),returnZeroValue($fordb3->dt[sales]))."</td>
			<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".BarchartView(returnZeroValue($fordb->dt[ucnt]),returnZeroValue($fordb3->dt[ucnt]))."</td>
			<td bgcolor=#ffffff align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".BarchartView(returnZeroValue($fordb->dt[cnt]),returnZeroValue($fordb3->dt[cnt]))."</td>		
			</tr>
			";
			
	/*		
			$mstring .= "<tr height=30>
			<td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt[step6]."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
			</tr>\n";
	*/		
		//	$exitcnt = $pageview01 + returnZeroValue($fordb->dt[visit_cnt]);
			
			
		//}
	
	$mstring = $mstring."</table>\n<br>";	
	
}else if ($SelectReport == 2){
	$fordb->query($sql);
//	echo $sql;
	
	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=615   STYLE='TABLE-LAYOUT:fixed'>\n";	
	$mstring = $mstring."<tr height=25 align=center style='font-weight:bold'><td class=s_td width=150>시간</td><td class=m_td width=165>매출액</td><td class=m_td width=150 nowrap>구매자수</td><td class=e_td width=150 nowrap>구매수량</td></tr>\n";
	
	if($fordb->total == 0){
		$mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";	
	}else{
		$j = 0;
		$fordb->fetch($j);
		for($i=0;$i < $nLoop;$i++){
			
			if($fordb->dt[vdate] == date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)){			
				$mstring .= "
				<tr height=30 bgcolor=#ffffff >
				<td bgcolor=#efefef id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:25px;padding-right:5px' nowrap>".getNameOfWeekday($i,$vdate)."</td>
				<td bgcolor=#ffffff id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=right>".number_format(returnZeroValue($fordb->dt[sales]),0)."</td>
				<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".returnZeroValue($fordb->dt[ucnt])."</td>
				<td bgcolor=#ffffff align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".returnZeroValue($fordb->dt[cnt])."</td>			
				</tr>";
				
				$j = $j + 1;
				$fordb->fetch($j); 
				$sumprice = $sumprice + $fordb->dt[sales];
				$sumucnt = $sumucnt + $fordb->dt[ucnt];
				$sumcnt = $sumcnt + $fordb->dt[cnt];
			}else{
				$mstring .= "
				<tr height=30 bgcolor=#ffffff >
				<td bgcolor=#efefef id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:25px;padding-right:5px' nowrap>".getNameOfWeekday($i,$vdate)."</td>
				<td bgcolor=#ffffff id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=right>0</td>
				<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>0</td>
				<td bgcolor=#ffffff align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>0</td>			
				</tr>";
			}
			
		}	
			
	}
	$mstring = $mstring."<tr height=25 align=center style='font-weight:bold'><td class=s_td width=150>합계</td><td class=m_td align=right width=165>".number_format($sumprice,0)."</td><td class=m_td width=150 nowrap>".returnZeroValue($sumucnt)."</td><td class=e_td width=150 nowrap>".returnZeroValue($sumcnt)."</td></tr>\n";
	$mstring = $mstring."</table><br>\n";	
}else if ($SelectReport == 3){
	$fordb->query($sql);
	
	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=615   STYLE='TABLE-LAYOUT:fixed'>\n";	
	$mstring = $mstring."<tr height=25 align=center style='font-weight:bold'><td class=s_td width=150>시간</td><td class=m_td width=165>매출액</td><td class=m_td width=150 nowrap>구매자수</td><td class=e_td width=150 nowrap>구매수량</td></tr>\n";
	
	if($fordb->total == 0){
		$mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";	
	}else{
		$j = 0;
		$fordb->fetch($j);
		for($i=0;$i < $nLoop;$i++){
			
			if($fordb->dt[vdate] == date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)){			
				$mstring .= "
				<tr height=30 bgcolor=#ffffff >
				<td bgcolor=#efefef id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:25px;padding-right:5px' nowrap>".getNameOfWeekday($i,$vdate,"dayname")."</td>
				<td bgcolor=#ffffff id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=right>".number_format(returnZeroValue($fordb->dt[sales]),0)."</td>
				<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".returnZeroValue($fordb->dt[ucnt])."</td>
				<td bgcolor=#ffffff align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".returnZeroValue($fordb->dt[cnt])."</td>			
				</tr>";
				
				$j = $j + 1;
				$fordb->fetch($j); 
				$sumprice = $sumprice + $fordb->dt[sales];
				$sumucnt = $sumucnt + $fordb->dt[ucnt];
				$sumcnt = $sumcnt + $fordb->dt[cnt];
			}else{
				$mstring .= "
				<tr height=30 bgcolor=#ffffff >
				<td bgcolor=#efefef id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:25px;padding-right:5px' nowrap>".getNameOfWeekday($i,$vdate,"dayname")."</td>
				<td bgcolor=#ffffff id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=right>0</td>
				<td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>0</td>
				<td bgcolor=#ffffff align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>0</td>			
				</tr>";
			}
			
		}	
			
	}
	$mstring = $mstring."<tr height=25 align=center style='font-weight:bold'><td class=s_td width=150>합계</td><td class=m_td align=right width=165>".number_format($sumprice,0)."</td><td class=m_td width=150 nowrap>".returnZeroValue($sumucnt)."</td><td class=e_td width=150 nowrap>".returnZeroValue($sumcnt)."</td></tr>\n";
	$mstring = $mstring."</table><br>\n";	
}
	
//	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=615  >\n";
//	if ($pageview01 == 0){
//		$mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";	
//	}
//	$mstring = $mstring."<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
//	$mstring = $mstring."<tr height=25 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
//	$mstring = $mstring."</table>\n";
	
	
	return $mstring;
}
if ($mode == "iframe"){
	
	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
	echo "<Script>parent.document.getElementById('contentsarea').innerHTML = document.tablefrm.reportvalue.value;</Script>\n";
	
	$ca = new Calendar();
	$ca->SelectReport = $SelectReport;
	$ca->LinkPage = 'salesbydate.php';
	
	
	echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
	
	echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;".($SelectReport != 3 ? "parent.ChangeCalenderView($SelectReport);":"")."</Script>";
	
	
}else{
	/*
$p = new forbizReportPage();
$p->TopNaviSelect = "step2";
$p->forbizLeftMenu = commerce_munu('salesbydate.php');//.text_button("#", "test","190").colorCirCleBoxStart("#efefef",190)."test<br>test<br>test<br>test<br>test<br>".colorCirCleBoxEnd("#efefef");
$p->forbizContents = ReportTable($vdate,$SelectReport).colorCirCleBox("#efefef",615,"<br><br><br><br><br>");
$p->PrintReportPage();
*/
$Script = "";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = commerce_munu('salesbydate.php');;
$P->strContents = ReportTable($vdate,$SelectReport).colorCirCleBox("#efefef",615,"<br><br><br><br><br>");
echo $P->PrintLayOut();


}
?>
