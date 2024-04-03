<?
include("../class/layout.class");
//include("./pie.graph.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../lib/report.lib.php");

$db = new Database;
$mdb = new Database;
$sms_design = new SMS;


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
		    <td align='left' colspan=6 > ".GetTitleNavigation("매출요약", "매출관리 > 매출요약 > 주문상태별 요약 ")."</td>
	  </tr>";
if(false){
$Contents01 .= "
	  <tr height=40>
		<td >
			<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>";
if(!$selected_month){
	$selected_month = date("Ym",time());
}
for($i=-4;$i < 6;$i++){
	$display_month = date("Ym",mktime(0,0,0,substr($selected_month,4,2)+$i,substr($selected_month,6,2),substr($selected_month,0,4)));
	$display_month2 = date("Y.m",mktime(0,0,0,substr($selected_month,4,2)+$i,substr($selected_month,6,2),substr($selected_month,0,4)));
	$Contents01 .= "
								<table id='tab_01'  ".($display_month == $selected_month ? "class=on":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?selected_month=".$display_month."'\">".$display_month2."</td>
									<th class='box_03'></th>
								</tr>
								</table>
								";
}

$Contents01 .= "
							</td>
							<td class='btn' style='padding:10px 0px 0px 10px;'>

							</td>
						</tr>
						</table>
					</div>
		</td>
	  </tr>";
}


$Contents01 .= "
	  <!--tr height=30><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주문상태별 요약</b></td></tr-->
	  <tr>
	  	<td style='padding:5px 0px 0px 0px'>
	  		".salesByDateReportTable($vdate)."
	  	</td>
	  </tr>

	  <tr height=50><td colspan=5 class=small><!--* 해당 통계는 주문상세내용을 기준으로 산정되며 매출액은 주문취소금액 과 입금예정 내역은 제외됩니다.-->
	   ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</td></tr>


	  <tr height=50><td colspan=5></td></tr>
	</table>";



$Contents = $Contents01;






$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
$P->Navigation = "매출관리 > 일별매출액(종합)";
$P->title = "일별매출액(종합)";
echo $P->PrintLayOut();


?>
