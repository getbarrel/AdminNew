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
		$send = "day";
	}

	
	$mstring .="<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-top:10px;'>
			<tr>
				<td align='left' colspan=6 >".GetTitleNavigation("메일링/SMS", "SMS 발송 분석기 > S/LMS 일별통계 ")."</td>
			</tr>
			<tr>
				<td>
				<form name='searchmember' method='GET'>
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
														<!--<input type=radio name='send' value='0' id='schday'  ".CompareReturnValue("0",$send,"checked")."><label for='schday'>시간대별</label>-->
														<input type=radio name='send' value='day' id='schdays' ".CompareReturnValue("day",$send,"checked")."><label for='schdays'>일별</label>
														<input type=radio name='send' value='month' id='schmonth' ".CompareReturnValue("month",$send,"checked")."><label for='schmonth'>월별</label>
														<!--<input type=radio name='send' value='3' id='schperioad' ".CompareReturnValue("3",$send,"checked")."><label for='schperioad'>기간별</label>
														<input type=radio name='send' value='4' id='schtotal' ".CompareReturnValue("4",$send,"checked")."><label for='schtotal'>종합비교분석</label>-->
													</td>
												</tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>";
														
														if($send == "4"){
															$mstring .="기준일자";
														}else{
															$mstring .="<select name='select_date_type'>
																			<option value='send'".CompareReturnValue("send",$select_date_type,"selected").">발송일자</option>
																			<option value='sign'".CompareReturnValue("sign",$select_date_type,"selected").">계약일자</option>
																		</select>";
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
				<td>".getContractList()."
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
$P->Navigation = "전자계약 관리 > 전자계약 통계";
$P->title = "전자계약 통계";
$P->strLeftMenu = econtract_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();


function getContractList(){
	global $db, $mdb, $page, $search_type;
	global $auth_delete_msg, $auth_excel_msg, $admininfo;
		
	
	if($_REQUEST['mode'] == 'search'){

		if($_REQUEST['select_date_type'] == 'send'){
			$date = 'regdate';
		}else{
			$date = 'signature_date';
		}
		
		if($_REQUEST['send'] == 'day'){

			$startDate = $_REQUEST['startDate'];
			$endDate = $_REQUEST['endDate'];

			$date_type = "date_format($date, '%Y-%m-%d') as daily";
			$date_group = "daily";
			
		}else if($_REQUEST['send'] == 'month'){

			$startDate = $_REQUEST['startDate'];
			$endDate = $_REQUEST['endDate'];

			$startDate	= substr($startDate , 0 , 7)."-01";
			$endDate	= substr($endDate , 0 , 7)."-31";

			$date_type = "date_format($date, '%Y-%m') as month";
			$date_group = "month";
		}

	}else{
		$date = 'regdate';
		$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

		$startDate = date("Y-m-d", $before7day);
		$endDate = date("Y-m-d");

		$date_type = "date_format($date, '%Y-%m-%d') as daily";
		$date_group = "daily";

		

	}
	
	$where = "AND $date between '$startDate 00:00:00' and '$endDate 23:59:59' ";		

	$sql = "SELECT 
				count(*) as total ,
				count(IF(com_signature != '' , 1 , 0)) as com_signature ,
				count(IF(contractor_signature != '' , 1 , 0)) as contractor_signature ,
				count(IF(status = 'CC' , 1 , 0)) as status_cc ,
				count(IF(status = 'CA' , 1 , 0)) as status_ca ,
				count(IF(status = 'CRS' , 1 , 0)) as status_crs ,
				count(IF(status = 'CRT' , 1 , 0)) as status_crt ,
				count(IF(status = 'CRM' , 1 , 0)) as status_crm 
			FROM 
				econtract_info 
			WHERE 1 $where
			";
	//echo nl2br($sql);
	$mdb->query($sql);
	$mdb->fetch();

	$total = $mdb->dt['total'];
	$com_signature = $mdb->dt['com_signature'];
	$contractor_signature = $mdb->dt['contractor_signature'];
	$status_cc = $mdb->dt['status_cc'];
	$status_ca = $mdb->dt['status_ca'];
	$status_crs = $mdb->dt['status_crs'];
	$status_crt = $mdb->dt['status_crt'];
	$status_crm = $mdb->dt['status_crm'];

	$max = 20;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$mString = "<table cellpadding=0 cellspacing=0 border=0 width=100%  class='list_table_box'>";
	$mString .= "
	<col width='15%'>
	<col width='10%'>
	<col width='5%'>
	<col width='5%'>
	<col width='5%'>
	<col width='10%'>
	<col width='10%'>
	<col width='10%'>
	<col width='10%'>
	<col width='10%'>
	<tr align=center bgcolor=#efefef height='30'>
		<td class=s_td rowspan='2' >구분</td>
		<td class=m_td rowspan='2' >계약서 작성</td>
		<td class=m_td colspan='3'>서명단계</td>
		<td class=m_td rowspan='2'>서명요청<br />계약서</td>
		<td class=m_td rowspan='2'>서명취소<br />계약서</td>
		<td class=m_td rowspan='2'>서명반려<br />계약서</td>
		<td class=m_td rowspan='2'>서명삭제<br />계약서</td>
		<td class=m_td rowspan='2'>서명완료<br />계약서</td>
	</tr>
	<tr align=center bgcolor=#efefef height='30'>
		<td class=m_td >갑 서명</td>
		<td class=m_td >을 서명</td>
		<td class=m_td >서명완료</td>
	</tr>
	<tr align=center bgcolor=#efefef height='30'>
		<td class=s_td >총 합계</td>
		<td class=m_td >".$total."</td>
		<td class=m_td >".$com_signature."</td>
		<td class=m_td >".$contractor_signature."</td>
		<td class=m_td >".$status_cc."</td>
		<td class=m_td >".$status_ca."</td>
		<td class=m_td >".$status_crs."</td>
		<td class=m_td >".$status_crt."</td>
		<td class=m_td >".$status_crm."</td>
		<td class=m_td >".$status_cc."</td>
	</tr>
	";

	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=10 align=center>데이터가 없습니다.</td></tr>";
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right style='padding:10px 0px;'><a href='search_text.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a></td></tr>";

	}else{

		$sql = "SELECT 
				$date_type ,
				count(*) as total ,
				count(IF(com_signature != '' , 1 , 0)) as com_signature ,
				count(IF(contractor_signature != '' , 1 , 0)) as contractor_signature ,
				count(IF(status = 'CC' , 1 , 0)) as status_cc ,
				count(IF(status = 'CA' , 1 , 0)) as status_ca ,
				count(IF(status = 'CRS' , 1 , 0)) as status_crs ,
				count(IF(status = 'CRT' , 1 , 0)) as status_crt ,
				count(IF(status = 'CRM' , 1 , 0)) as status_crm 
			FROM 
				econtract_info 
			WHERE 1 $where
			GROUP BY $date_group
			ORDER BY regdate DESC";
//echo nl2br($sql);
		$db->query($sql);
		$db->fetch();
		$total = $db->total;

		$sql = "SELECT 
				$date_type ,
				count(*) as total ,
				count(IF(com_signature != '' , 1 , NULL)) as com_signature ,
				count(IF(contractor_signature != '' , 1 , NULL)) as contractor_signature ,
				count(IF(status = 'CC' , 1 , NULL)) as status_cc ,
				count(IF(status = 'CA' , 1 , NULL)) as status_ca ,
				count(IF(status = 'CRS' , 1 , NULL)) as status_crs ,
				count(IF(status = 'CRT' , 1 , NULL)) as status_crt ,
				count(IF(status = 'CRM' , 1 , NULL)) as status_crm 
			FROM 
				econtract_info 
			WHERE 1 $where
			GROUP BY $date_group
			ORDER BY regdate DESC
			LIMIT $start , $max
			";
		
		$db->query($sql);
		$data = $db->fetchall();
		//$total = count($data);

		if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
			$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
		}else{
			$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
		}

		$str_page_bar = page_bar($total, $page, $max, $query_string,"");

		foreach($data as $val){
			
			//$no = $total - ($page - 1) * $max - $i;

			$mString = $mString."
			<tr height=30 bgcolor=#ffffff align=center>
			<td class='list_box_td' >".$val[$date_group]."</td>
			<td class='list_box_td' >".$val['total']."</td>
			<td class='list_box_td' >".$val['com_signature']."</td>
			<td class='list_box_td'>".$val['contractor_signature']."</td>
			<td class='list_box_td point'>".$val['status_cc']."</td>
			<td class='list_box_td' >".$val['status_ca']."</td>
			<td class='list_box_td' >".$val['status_crs']."</td>
			<td class='list_box_td' >".$val['status_crt']."</td>
			<td class='list_box_td' >".$val['status_crm']."</td>
			<td class='list_box_td point'>".$val['status_cc']."</td>
			</tr>
			";
		}

		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff style='height:50px;'>
					<td colspan=3 align=left>".$str_page_bar."</td>
					<td colspan=2 align=right>";
		$mString .= "
					</td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}

?>