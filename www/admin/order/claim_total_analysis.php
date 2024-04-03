<?
include("../class/layout.class");
include("../lib/claim.lib.php");

//$db = new Database;

if($act!="search"){
	$startDate=date("Ym01");
	$endDate=date("Ymd");
}

if(empty($view_status)){
	$view_status = 'IB';
}

$Script="
	<script type='text/javascript'>
	<!--
		$(document).ready(function (){
			$('#start_datepicker').datepicker({
			//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			//showMonthAfterYear:true,
			dateFormat: 'yymmdd',
			buttonImageOnly: true,
			buttonText: '달력',
			onSelect: function(dateText, inst){
				//alert(dateText);
				if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
					$('#end_datepicker').val(dateText);
				}else{
					$('#end_datepicker').datepicker('setDate','+0d');
				}
			}

			});

			$('#end_datepicker').datepicker({
			//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			//showMonthAfterYear:true,
			dateFormat: 'yymmdd',
			buttonImageOnly: true,
			buttonText: '달력'

			});
		});

		function setSelectDate(FromDate,ToDate,dType) {
			var frm = document.search_frm;

			$('#start_datepicker').val(FromDate);
			$('#end_datepicker').val(ToDate);
		}

	//-->
	</script>
";
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
		    <td align='left' colspan=6 > ".GetTitleNavigation("클래임토탈분석", "매출관리 > 주문/클래임관리 > 클래임토탈분석")."</td>
	  </tr>
	</table>

	<form name='search_frm' method='get' action=''>
	<input type=hidden name='act' value='search'>
	<input type=hidden name='view_status' value='".$_GET['view_status']."'>
	<table width='100%' class='search_table_box'>
		<col width=15%>
		<col width=35%>
		<col width=15%>
		<col width=35%>
		<tr height=33>
			<th class='search_box_title'>기간</th>
			<td class='search_box_item' colspan=3>
				<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff>
					<tr>
						<TD  nowrap><input type='text' name='startDate' class='textbox point_color' value='".$startDate."' style='height:20px;width:100px;text-align:center;' id='start_datepicker'></TD>
						<TD style='padding:0 5px;' align=center> ~ </TD>
						<TD nowrap><input type='text' name='endDate' class='textbox point_color' value='".$endDate."' style='height:20px;width:100px;text-align:center;' id='end_datepicker'></TD>
						<td>";

$vdate = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()-84600);
$voneweekago = date("Ymd", time()-84600*7);
$vtwoweekago = date("Ymd", time()-84600*14);
$vfourweekago = date("Ymd", time()-84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

$Contents01 .= "
			<a href=\"javascript:setSelectDate('$today','$today');\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
			<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday');\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
			<a href=\"javascript:setSelectDate('$voneweekago','$today');\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
			<a href=\"javascript:setSelectDate('$v15ago','$today');\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
			<a href=\"javascript:setSelectDate('$vonemonthago','$today');\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
			<a href=\"javascript:setSelectDate('$v2monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
			<a href=\"javascript:setSelectDate('$v3monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>

						</td>
					</tr>
				</table>
			</td>
		</tr>

	</table>
	<table width='100%' >
		<tr bgcolor=#ffffff >
			<td colspan=4 align=center style='padding:10px 0px'><input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:hand;border:0px;' ></td>
		</tr>
	</table>
	</form>

	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  	  <tr>
		    <td align='left' colspan=6 > ".GetTitleNavigation("클래임분석", "매출관리 > 주문/클래임관리 > 클래임분석")."</td>
	  </tr>
	  <tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'>
			<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".($view_status==ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE ? "class='on'" : "").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?view_status=".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'\">입금전취소사유</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_02' ".($view_status==ORDER_STATUS_CANCEL_COMPLETE ? "class='on'" : "").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?view_status=".ORDER_STATUS_CANCEL_COMPLETE."'\">입금후취소사유</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_03' ".($view_status==ORDER_STATUS_EXCHANGE_APPLY ? "class='on'" : "").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?view_status=".ORDER_STATUS_EXCHANGE_APPLY."'\">교환요청사유</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_04' ".($view_status==ORDER_STATUS_RETURN_APPLY ? "class='on'" : "").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?view_status=".ORDER_STATUS_RETURN_APPLY."'\">반품요청사유</td>
									<th class='box_03'></th>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>";

if($view_status == 'IB'){
	$b_status="IR";
	$f_status="CA";
	$Contents01 .= "
		  <tr height=30><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>클래임 분류 : 입금 전 취소 사유</b></td></tr>
		  <tr>
			<td style='padding:5px 0px 0px 0px'>
				".claimByDateReportTable($vdate,'period')."
			</td>
		  </tr>";
}

if($view_status == 'CC'){
	$b_status="IC";
	$f_status="CA";
	$Contents01 .= "
		<tr height=15><td></td></tr>
		  <tr height=30><td style='border-bottom:2px solid #efefef;'><img src='../images/dot_org.gif' align=absmiddle> <b>클래임 분류 : 입금 후 취소 사유</b></td></tr>
		  <tr>
			<td style='padding:5px 0px 0px 0px'>
				".claimByDateReportTable($vdate,'period')."
			</td>
		  </tr>";
}

if($view_status == 'EA'){
	$b_status="DC";
	$f_status="EA";
	$Contents01 .= "
		<tr height=15><td></td></tr>
		  <tr height=30><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>클래임 분류 : 교환요청 사유</b></td></tr>
		  <tr>
			<td style='padding:5px 0px 0px 0px'>
				".claimByDateReportTable($vdate,'period')."
			</td>
		  </tr>";
}

if($view_status == 'RA'){
	$b_status="DC";
	$f_status="RA";
	$Contents01 .= "
		<tr height=15><td></td></tr>
		  <tr height=30><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>클래임 분류 : 반품요청 사유</b></td></tr>
		  <tr>
			<td style='padding:5px 0px 0px 0px'>
				".claimByDateReportTable($vdate,'period')."
			</td>
		  </tr>";
}
$Contents01 .= "
	  <tr height=50><td colspan=5 class=small><!--* 해당 통계는 주문상세내용을 기준으로 산정되며 매출액은 주문취소금액 과 입금예정 내역은 제외됩니다.-->
	   ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</td></tr>


	  <tr height=50><td colspan=5></td></tr>
	</table>";



$Contents = $Contents01;



$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
$P->Navigation = "매출관리 > 주문/클래임관리 > 클래임토탈분석";
$P->title = "클래임토탈분석";
echo $P->PrintLayOut();


?>
