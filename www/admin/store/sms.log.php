<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

$db = new Database;
$mdb = new Database;

$sms_design = new SMS;

//검색 1주일단위 디폴트
if ($startDate == ""){
	$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

	$startDate = date("Y-m-d", $before7day);
	$endDate = date("Y-m-d");

}

if($mode!="search"){
	$orderdate=1;
	$send_type = "A";
}

	$mstring .="<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-top:10px;'>
			<tr>
				<td align='left' colspan=6 >".GetTitleNavigation("SMS 발송로그", "마케팅지원 > SMS 발송목록 ")."</td>
			</tr>
			<tr>
				<td align='left' colspan=4 style='padding-bottom:15px;'>
					 <div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='sms.point.php'>SMS 충전하기</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='sms.log.php'>SMS 발송목록</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='sms.log.detail.php'>SMS 발송 상세리스트</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
					</tr>
					</table>
				</div>
				</td>
			</tr>
			<form name='searchmember' method='GET'>
			<input type='hidden' name='mode' value='search' />
			<tr>
				<td>
				<input type=hidden name='license' value='".$admininfo[mall_domain_key]."'><!-- license로 변경 kbk 13/09/23 -->
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
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>일자검색
														<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.searchmember);' ".CompareReturnValue('1',$orderdate,' checked').">
													</th>
													<td class='search_box_item'>
														".search_date('startDate','endDate',$startDate,$endDate)."
													</td>
												 </tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>발송구분</th>
													<td class='search_box_item' colspan='3'>
														<input type=radio name='send_type' value='A' id='send_n'  ".CompareReturnValue("A",$send_type,"checked")."><label for='send_n'>자동발송</label>
														<input type=radio name='send_type' value='M' id='send_y' ".CompareReturnValue("M",$send_type,"checked")."><label for='send_y'>수동발송</label>
														<input type=radio name='send_type' value='R' id='send_r' ".CompareReturnValue("R",$send_type,"checked")."><label for='send_r'>예약발송</label>
													</td>
												</tr>
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
			</tr>
			";
	$mstring .="</table>";

	$mstring .="
			<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
			<tr>
				<td>";
	$nowMonth = date('Ym');
	$table = "msg_result_".$nowMonth;
	if($_GET["license"]){// license로 변경 kbk 13/09/23
		$mstring .=  $sms_design->getSMSProductLogListsUTF8($_GET, $regdate,'',$table);
	}else{
		$mstring .=  $sms_design->getSMSProductLogListsUTF8($admininfo, $regdate,'',$table);
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

</script>";
	$P = new LayOut();
	$P->addScript = $Script;
	
	$display_position = "store";
	
	if($send_date != 1)
		$P->OnloadFunction = "init();";
	else
		$P->OnloadFunction = "init2();";

	if($display_position == "store"){
		$P->strLeftMenu = store_menu();
	}else{
		$P->strLeftMenu = campaign_menu();
	}
	$P->Navigation = "마케팅지원 > 쇼핑몰 환경설정 > SMS 발송로그";
	$P->title = "SMS 발송로그";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();


?>
