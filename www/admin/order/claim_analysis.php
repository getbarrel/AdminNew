<?
include("../class/layout.class");
include("../lib/claim.lib.php");

//$db = new Database;

if($view_status==""){
	$view_status=ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE;
}

if($view_status==ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE){
	$b_status="IR";
	$f_status="CA";
}elseif($view_status==ORDER_STATUS_CANCEL_COMPLETE){
	$b_status="IC";
	$f_status="CA";
}elseif($view_status==ORDER_STATUS_EXCHANGE_APPLY){
	$b_status="DC";
	$f_status="EA";
}elseif($view_status==ORDER_STATUS_RETURN_APPLY){
	$b_status="DC";
	$f_status="RA";
}

$Contents01 = "
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
	</tr>";

$Contents01 .= "
	  <!--tr height=30><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주문상태별 요약</b></td></tr-->
	  <tr>
	  	<td style='padding:5px 0px 0px 0px'>
	  		".claimByDateReportTable($vdate)."
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
$P->Navigation = "매출관리 > 주문/클래임관리 > 클래임분석";
$P->title = "클래임분석";
echo $P->PrintLayOut();


?>
