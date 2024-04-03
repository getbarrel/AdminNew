<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
$page_title = "S/LMS 일별통계";
$page_navigation = "메일링/SMS > SMS 발송 분석기 > S/LMS 일별통계";
$include_menu = "campaign";


$db = new MySQL;
$mdb = new MySQL;
$sms_design = new SMS;

	//검색 1주일단위 디폴트
		if ($startDate == ""){
			$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

			$startDate = date("Y-m-d", $before7day);
			$endDate = date("Y-m-d");

		}

		if ($vstartDate == ""){
			$before14day = mktime(0, 0, 0, date("m")  , date("d")-14, date("Y"));
			$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));
			$vstartDate = date("Y-m-d", $before14day);
			$vendDate = date("Y-m-d",$before7day);
		}

		if($mode != 'search'){
			$send = 0;
		}
	
	$mstring .="<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-top:10px;'>
			<tr>
				<td align='left' colspan=6 >".GetTitleNavigation("메일링/SMS", "SMS 발송 분석기 > S/LMS 일별통계 ")."</td>
			</tr>
			<tr>
				<td>
				<form name='searchmember' method='GET'>
				<input type=hidden name='license' value='".$admininfo[mall_domain_key]."'><!-- license로 변경 kbk 13/09/23 -->
				<input type='hidden' name='mode' value='search' />
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  border=0 cellpadding='0' cellspacing='0'>
							<tr>
								<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>

										<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
										<TR>
											<TD bgColor=#ffffff style='padding:0 0 0 0;'>
											<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
												 <tr height=27>
													<td class='search_box_title'>조건설정</td>
													<td class='search_box_item' colspan='3'>
														<input type=radio name='send' value='0' id='schday'  ".CompareReturnValue("0",$send,"checked")."><label for='schday'>시간대별</label>
														<input type=radio name='send' value='1' id='schdays' ".CompareReturnValue("1",$send,"checked")."><label for='schdays'>일별</label>
														<input type=radio name='send' value='2' id='schmonth' ".CompareReturnValue("2",$send,"checked")."><label for='schmonth'>월별별</label>
														<input type=radio name='send' value='3' id='schperioad' ".CompareReturnValue("3",$send,"checked")."><label for='schperioad'>기간별</label>
														<input type=radio name='send' value='4' id='schtotal' ".CompareReturnValue("4",$send,"checked")."><label for='schtotal'>종합비교분석</label>
													</td>
												</tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>";
														
														if($send == "4"){
															$mstring .="기준일자";
														}else{
															$mstring .="발송일자";
														}

													$mstring .="</th>
													<td class='search_box_item'>
														".search_date('startDate','endDate',$startDate,$endDate)."
													</td>
												 </tr>";
												if($send == "4"){
													$mstring .="<tr height='27'>
														<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>비교일자
														</th>
														<td class='search_box_item'>
															".search_date('vstartDate','vendDate',$vstartDate,$vendDate)."
														</td>
													 </tr>";
												}
												$mstring .= "
											</table>
											</TD>
										</TR>
										</TABLE>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr >
					<td colspan=3 align=center style='padding:10px 0 20px 0'>
						<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
					</td>
				</tr>
				</table>
				</form>
				</td>
			</tr>";
	$mstring .="</table>";
	
	$mstring .="
			<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
			<tr>
				<td>";
	if($send == "0"){

		if($_GET["license"]){
			$mstring .=  $sms_design->getSMSProductLogTimeUTF8($_GET, $regdate);
		}else{
			$mstring .=  $sms_design->getSMSProductLogTimeUTF8($admininfo, $regdate);
		}
	}else if($send == "1"){
		
		if($_GET["license"]){
			$mstring .=  $sms_design->getSMSProductLogDayUTF8($_GET, $regdate);
		}else{
			$mstring .=  $sms_design->getSMSProductLogDayUTF8($admininfo, $regdate);
		}

	}else if($send == "2"){
		
		if($_GET["license"]){
			$mstring .=  $sms_design->getSMSProductLogMonthUTF8($_GET, $regdate);
		}else{
			$mstring .=  $sms_design->getSMSProductLogMonthUTF8($admininfo, $regdate);
		}

	}else if($send == "3"){
		
		if($_GET["license"]){
			$mstring .=  $sms_design->getSMSProductLogPeriodUTF8($_GET, $regdate);
		}else{
			$mstring .=  $sms_design->getSMSProductLogPeriodUTF8($admininfo, $regdate);
		}

	}else if($send == "4"){
		
		if($_GET["license"]){
			$mstring .=  $sms_design->getSMSProductLogTotalUTF8($_GET, $regdate);
		}else{
			$mstring .=  $sms_design->getSMSProductLogTotalUTF8($admininfo, $regdate);
		}

	}
	
	$mstring .="
				</td>
			</tr>
			</table>
			";
	
	$Contents = $mstring;
	
	$Script = "<script language='javascript' src='../include/DateSelect.js'></script>
<script language='javascript' >
function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
		$('#endDate').attr('disabled',false);
		$('#startDate').attr('disabled',false);
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
		$('#endDate').attr('disabled',true);
		$('#startDate').attr('disabled',true);
	}
}
function ChangevOrderDate(frm){
	if(frm.vorderdate.checked){
		$('#vstartDate').addClass('point_color');
		$('#vendDate').addClass('point_color');
		$('#vendDate').attr('disabled',false);
		$('#vstartDate').attr('disabled',false);
	}else{
		$('#vstartDate').removeClass('point_color');
		$('#vendDate').removeClass('point_color');
		$('#vendDate').attr('disabled',true);
		$('#vstartDate').attr('disabled',true);
	}
}
</script>";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = campaign_menu();
$P->Navigation = $page_navigation;
$P->title = $page_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>