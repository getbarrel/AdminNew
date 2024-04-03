<?
	include("../class/layout.class");
 	$db = new Database;

	//include_once $DOCUMENT_ROOT."/admin/tax/test_header.php";
	
		
	if($max==""){
		$max = 15; //페이지당 갯수
	}

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	
	$search_type = $_REQUEST[search_type];				// 검색 칼럼
	$search_text = $_REQUEST[search_text];				// 검색어
	$publish_type = $_REQUEST[publish_type];	// 1.매출 2.매입 3.위수탁
	
//	echo $date_type;
	
/*
	if($s_type == "total" || $s_type == "")			$checked1 = "checked";
	if($s_type == "r_company_number")				$checked2 = "checked";
	if($s_type == "r_company_name")					$checked3 = "checked";

	$tab01 = $tab02 = $tab03 = $tab04 = "";
*/
	/*if($publish_type == "" || $publish_type == "total") $tab01 = "class='on'";
	if($publish_type == "1")							$tab02 = "class='on'";
	if($publish_type == "2")							$tab03 = "class='on'";
	if($publish_type == "3")							$tab04 = "class='on'";
*/
	
	
	
	$Contents = "
	<script>
	var tc = 0;
	function total_check()
	{
		if(tc%2 == 0)	$('input:checkbox[name=\'chk[]\']').attr('checked',true);
		else			$('input:checkbox[name=\'chk[]\']').removeAttr('checked');

		tc++;
	}

	var total_checked = 0;
	$(document).ready(function(){
		$('#tax_tab1').click(function(){
			$('#tab1_view').slideDown();
		});

		$('#company_write').click(function(){
			window.open('./company_write_step1.php?from=company_list','company','width=550,height=300');
		});

		$('#sch_frm').submit(function(){
			if($('#sch_txt').val() == '' && total_checked == 0)
			{
				alert ('검색어를 입력해주세요.');
				$('#sch_txt').focus();
				return false;
			}

			$('#sch_frm').action = '$PHP_SELF';
			$('#sch_frm').method = 'POST';
		});

		$('#del_btn').click(function(){

			total_checked = $('input:checkbox[name=\'chk[]\']:checked').length;

			if(total_checked < 1)
			{
				alert ('삭제할 게시물을 체크해주세요');
				return;
			}

			$('#frm').attr('action','./sales_del.php');
			$('#frm').attr('method','POST');
			$('#frm').attr('target','PROC');
			$('#frm').submit();
		});
		
		$('#tax_update_btn').click(function(){

			var total_checked = $('input:checkbox[name=\'chk[]\']:checked').length;

			if(total_checked < 1)
			{
				alert ('발행할 게시물을 체크해주세요');
				return;
			}

			$('#frm').attr('action','./popbill_status_update.php');
			$('#frm').attr('method','POST');
			$('#frm').attr('target','PROC');
			$('#act').val('update');
			$('#frm').submit();

		});

		$('#tax_all_update_btn').click(function(){

			

			$('#frm').attr('action','./popbill_status_update.php');
			$('#frm').attr('method','POST');
			$('#frm').attr('target','PROC');
			$('#act').val('update_all');
			$('#frm').submit();

		});


	});
	</script>
	";

	$Contents .= "
	<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("매출/매입 문서조회", "세금계산서 관리 > 매출/매입 문서조회 ")."</td>
		</tr>
	</table>

	<!--div class='tab' style='margin:0 0 5px 0'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>

							<table id='tab_01' $tab01>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='$PHP_SELF?publish_type=total'\">전체</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' $tab02>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='$PHP_SELF?publish_type=1'\" >매출</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' $tab03>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='$PHP_SELF?publish_type=2'\">매입</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04' $tab04>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='$PHP_SELF?publish_type=3'\">위수탁</td>
								<th class='box_03'></th>
							</tr>
							</table>
				<td class='btn'>

				</td>
			</tr>
			</table>
	</div-->
";

include_once "./inc.search.php";

$Contents .= "
	<form name='frm' id='frm'>
	<input type='hidden' name='act' id='act' value='' />
	<table width='100%' cellpadding='0' cellspacing='0' border='0' class='list_table_box' style='margin:10px 0 0 0'>
	  <tr height='40' align='center' >
		<td class='s_td'><input type='checkbox' name='' id='' onclick='total_check()'></td>
		<td class='m_td'>구분</td>
		<td class='m_td'>발행처</td>
		<td class='m_td'>작성일자<br>발행일자</td>
		<td class='m_td'>거래처<br>사업자번호</td>
		<!--td class='m_td'>대표자</td-->
		<!--td class='m_td'>사업자번호(주민번호)</td-->
		<td class='m_td'>과세형태</td>
		<td class='m_td'>공급가액</td>
		<td class='m_td'>세액</td>
		<td class='m_td'>합계</td>
		<!--td class='m_td'>상태/발행일자</td-->
		<td class='m_td'>발행형태</td>
		<td class='m_td'>지연발행</td>
		<td class='m_td'>문서형태</td>
		<td class='m_td'>메일발송</td>
		<td class='m_td'>상태<br>update_date</td>
		<td class='m_td'>개봉</td>
		<td class='e_td'>보기</td>
	  </tr>
	";

	//if($publish_type == "total" || $publish_type == "")	{
		$where = " where status = '1' ";
	//}else{
	//	$where = " where status = '1' and publish_type = '$publish_type'";
	//}
	
	if($date_search != ""){
		$startDate = $_POST[startDate];
		$endDate = $_POST[endDate];
		if($date_type == 're_signdate'){
			if($startDate != "" && $endDate != "")	$where .= " AND re_signdate BETWEEN '".$startDate." 00:00:00' AND '".$endDate." 23:59:59'";
		}else if($date_type == 'signdate'){
			if($startDate != "" && $endDate != "")	$where .= " AND signdate BETWEEN '$startDate' AND '$endDate'";
		}
	}

	if(is_array($tax_type)){
		for($i=0;$i < count($tax_type);$i++){
			if($tax_type[$i] != ""){
				if($tax_type_str == ""){
					$tax_type_str .= "'".$tax_type[$i]."'";
				}else{
					$tax_type_str .= ", '".$tax_type[$i]."' ";
				}
			}
		}

		if($tax_type_str != ""){
			$where .= "and tax_type in ($tax_type_str) ";
		}
	}else{
		if($tax_type){
			$where .= "and tax_type = '$tax_type' ";
		}
	}

	if(is_array($tax_per)){
		for($i=0;$i < count($tax_per);$i++){
			if($tax_per[$i] != ""){
				if($tax_per_str == ""){
					$tax_per_str .= "'".$tax_per[$i]."'";
				}else{
					$tax_per_str .= ", '".$tax_per[$i]."' ";
				}
			}
		}

		if($tax_per_str != ""){
			$where .= "and tax_per in ($tax_per_str) ";
		}
	}else{
		if($tax_per){
			$where .= "and tax_per = '$tax_per' ";
		}
	}

	if(is_array($claim_kind)){
		for($i=0;$i < count($claim_kind);$i++){
			if($claim_kind[$i] != ""){
				if($claim_kind_str == ""){
					$claim_kind_str .= "'".$claim_kind[$i]."'";
				}else{
					$claim_kind_str .= ", '".$claim_kind[$i]."' ";
				}
			}
		}

		if($claim_kind_str != ""){
			$where .= "and claim_kind in ($claim_kind_str) ";
		}
	}else{
		if($claim_kind){
			$where .= "and claim_kind = '$claim_kind' ";
		}
	}

	if(is_array($status)){
		for($i=0;$i < count($status);$i++){
			if($status[$i] != ""){
				if($status_str == ""){
					$status_str .= "'".$status[$i]."'";
				}else{
					$status_str .= ", '".$status[$i]."' ";
				}
			}
		}

		if($status_str != ""){
			$where .= "and status in ($status_str) ";
		}
	}else{
		if($status){
			$where .= "and status = '$status' ";
		}
	}

	if(is_array($publish_type)){
		for($i=0;$i < count($publish_type);$i++){
			if($publish_type[$i] != ""){
				if($publish_type_str == ""){
					$publish_type_str .= "'".$publish_type[$i]."'";
				}else{
					$publish_type_str .= ", '".$publish_type[$i]."' ";
				}
			}
		}

		if($publish_type_str != ""){
			$where .= "and publish_type in ($publish_type_str) ";
		}
	}else{
		if($publish_type){
			$where .= "and publish_type = '$publish_type' ";
		}
	}
	if(is_array($send_status)){
		for($i=0;$i < count($send_status);$i++){
			if($send_status[$i] != ""){
				if($send_status_str == ""){
					$send_status_str .= "'".$send_status[$i]."'";
				}else{
					$send_status_str .= ", '".$send_status[$i]."' ";
				}
			}
		}

		if($send_status_str != ""){
			$where .= "and send_status in ($send_status_str) ";
		}
	}else{
		if($send_status){
			$where .= "and send_status = '$send_status' ";
		}
	}

	if(is_array($document_type)){
		for($i=0;$i < count($document_type);$i++){
			if($document_type[$i] != ""){
				if($document_type_str == ""){
					$document_type_str .= "'".$document_type[$i]."'";
				}else{
					$document_type_str .= ", '".$document_type[$i]."' ";
				}
			}
		}

		if($document_type_str != ""){
			$where .= "and document_type in ($document_type_str) ";
		}
	}else{
		if($document_type){
			$where .= "and document_type = '$document_type' ";
		}
	}

	if($search_type && $search_text){
		if($search_type == "r_company_number"){
			$where .= "and (r_company_name LIKE '%".trim($search_text)."%'  or r_company_number LIKE '%".trim($search_text)."%') ";
		}else{
			$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
		}
	}
/*
	if($search_text != "")
	{
		
			$startDate = $_POST[startDate];
			$endDate = $_POST[endDate];
			if($startDate != "" && $endDate != "")	$add_w = " AND signdate BETWEEN '$startDate' AND '$endDate'";

			# 상태
			for($i=0; $i < sizeof($state); $i++)
			{
				if($state_v != "" && $i < sizeof($state))	$state_v .= ",";
				if($state[$i] != "") $state_v .= "'".$state[$i]."'";
			}
			if($state_v != "") $add_w .= " AND status in (".$state_v.")";

			# 구분
			for($i=0; $i < sizeof($mkind); $i++)
			{
				if($mkind_v != "" && $i < sizeof($mkind))	$mkind_v .= ",";
				if($mkind[$i] != "") $mkind_v .= "'".$mkind[$i]."'";
			}
			if($mkind_v != "") $add_w .= " AND publish_type in (".$mkind_v.")";

			# 문서종류
			for($i=0; $i < sizeof($tkind); $i++)
			{
				if($tkind_v != "" && $i < sizeof($tkind))	$tkind_v .= ",";
				if($tkind[$i] != "") $tkind_v .= "'".$tkind[$i]."'";
			}
			if($tkind_v != "") $add_w .= " AND tax_type in (".$tkind_v.")";

			/*# 문서형태
			for($i=0; $i < sizeof($pstat); $i++)
			{
				if($pstat_v != "" && $i < sizeof($pstat))	$pstat_v .= ",";
				if($pstat[$i] != "") $pstat_v .= "'".$pstat[$i]."'";
			}
			if($pstat_v != "") $add_w .= " AND p_kind in (".$pstat_v.")";*/

			# 발행형태
			/*for($i=0; $i < sizeof($pkind); $i++)
			{
				if($pkind_v != "" && $i < sizeof($pkind))	$pkind_v .= ",";
				if($pkind[$i] != "") $pkind_v .= "'".$pkind[$i]."'";
			}
			if($pkind_v != "") $add_w .= " AND tax_type in (".$pkind_v.")";*/

			# 과세형태
	/*		for($i=0; $i < sizeof($pstat); $i++)
			{
				if($pstat_v != "" && $i < sizeof($pstat))	$pstat_v .= ",";
				if($pstat[$i] != "") $pstat_v .= "'".$pstat[$i]."'";
			}
			if($pstat_v != "") $add_w .= " AND tax_per in (".$pstat_v.")";

			# 영수/청구
			for($i=0; $i < sizeof($pstat); $i++)
			{
				if($ckind_v != "" && $i < sizeof($ckind))	$ckind_v .= ",";
				if($ckind[$i] != "") $ckind_v .= "'".$ckind[$i]."'";
			}
			if($ckind_v != "") $add_w .= " AND claim_kind in (".$ckind_v.")";
		
		if($publish_type == "total" || $publish_type == "")
		{
			if($s_type != "total")	$WHERE = " WHERE $s_type like '%$sch_txt%'".$add_w;
			else					$WHERE = " WHERE s_company_number like '%$sch_txt%' OR s_company_name like '%$sch_txt%' OR s_name like '%$sch_txt%'.$add_w";
		}
		else
		{
			if($s_type != "total")	$WHERE = " WHERE $s_type like '%$sch_txt%' AND publish_type = '$publish_type'".$add_w;
			else					$WHERE = " WHERE s_company_number like '%$sch_txt%' OR s_company_name like '%$sch_txt%' OR s_name like '%$sch_txt%' AND publish_type = '$publish_type'".$add_w;
		}
	}
	else
	{
		if($publish_type == "total" || $publish_type == "")
		{
			//$WHERE = " WHERE publish_type = '$publish_type'";
		}
		else
		{
			$WHERE = " WHERE publish_type = '$publish_type'";
		}
	}*/
	//echo $WHERE;

	//$WHERE = " WHERE publish_type = '1' ";


	//전체 갯수
	$TQuery = "SELECT * FROM tax_sales ".$where;
	//echo $TQuery;
	$db->query($TQuery);
	$total = $db->total;


	$SQL = "SELECT * FROM tax_sales ".$where." ORDER BY idx DESC LIMIT $start, $max";
	$db->query($SQL);
	if ($total > 0){
		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);

			if($db->dt[publish_type] == 1)
			{
				$publish_type = "매출";
				$send_url = "sales_view.php";
				$publish_kind = "정발행";
			}
			if($db->dt[publish_type] == 2)
			{
				$publish_type = "매입";
				$send_url = "purchase_write.php";
				$publish_kind = "<span style='color:red;'>역발행</span>";
			}
			if($db->dt[publish_type] == 3)
			{
				$publish_type = "위수탁";
				$publish_kind = "<span style='color:blue;'>위수탁</span>";
				$send_url = "sales_write2.php";
			}
			$send_url = "sales_view.php";

			$tax_type = $db->dt[tax_type];
			if($tax_type == 1) $tax_view = "세금";
			if($tax_type == 2) $tax_view = "계산서";

			$tax_per = $db->dt[tax_per];	// 과세형태
			if($tax_per == 1) $tax_show = "과세";
			if($tax_per == 2) $tax_show = "영세";
			if($tax_per == 3) $tax_show = "면세(세액없음)";

			$claim_kind = $db->dt[claim_kind];
			if($claim_kind == 1) $claim_show  = "영수";
			if($claim_kind == 2) $claim_show = "청구";

			$status = $db->dt[status];
			$document_type = $db->dt[document_type];

			if($status == "1") $status_view = "승인완료";
			if($status == "2") $status_view = "임시발행";
			if($status == "3") $status_view = "발행취소";
			if($status == "4") $status_view = "승인요청";
			if($status == "5") $status_view = "승인거부";
			if($status == "6") $status_view = "승인취소";
			
			if($document_type == "1") $document_type = "일반";
			if($document_type == "2") $document_type = "<span style='color:red;'>수정</span>";
			
			$Contents .= "
			  <tr height='40' align='center' bgcolor='#FFFFFF'>
				<td class='list_box_td list_bg_gray'><input type='checkbox' name='chk[]' id='chk[]' value='".$db->dt[idx]."'></td>
				<td class='list_box_td'>".$tax_view."</td>
				<!--td class='list_box_td list_bg_gray'>".$publish_type."</td-->
				<td class='list_box_td list_bg_gray'>발행처</td>
				<td class='list_box_td'>".$db->dt[signdate]."<br>".substr($db->dt[re_signdate],0,10)."</td>";
				if($db->dt[publish_type] == '1'){
				$Contents .= "
				<td class='list_box_td list_bg_gray' style='padding:5px;line-height:150%;'>".$db->dt[r_company_name]."<br>".$db->dt[r_company_number]."</td>";
				}else{
				$Contents .= "
				<td class='list_box_td list_bg_gray' style='padding:5px;line-height:150%;'>".$db->dt[s_company_name]."<br>".$db->dt[r_company_number]."</td>";
				}
				$Contents .= "
				<!--td class='list_box_td'>".$db->dt[r_name]."</td-->
				<!--td class='list_box_td list_bg_gray'>".$db->dt[r_company_number]."</td-->
				<td class='list_box_td'>".$tax_show."</td>
				<td class='list_box_td list_bg_gray'>".number_format($db->dt[supply_price])."</td>
				<td class='list_box_td'>".number_format($db->dt[tax_price])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($db->dt[total_price])."</td>
				<!--td class='list_box_td'>".$status_view." / ".substr($db->dt[signdate],0,10)."</td-->
				<td class='list_box_td '>".$publish_kind."</td>
				<td class='list_box_td list_bg_gray'>-</td>
				<td class='list_box_td '>".$document_type."</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[mail_re_send]."</td>
				<!--td class='list_box_td '>".$status_view."</td-->
				";if(false){
				$Contents .= "
				<!--td class='list_box_td '>".getTaxStatusText($db->dt[s_company_number],$db->dt[publish_type],$db->dt[idx])."</td>
				<td class='list_box_td list_bg_gray'>".getTaxStatusText($db->dt[s_company_number],$db->dt[publish_type],$db->dt[idx],'mail')."</td-->";
				}$Contents .= "
				<td class='list_box_td '>".$db->dt[tax_status_text]."<br>".$db->dt[tex_status_update_date]."</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[tax_mail_open]."</td>
				<td class='list_box_td'><a href='./".$send_url."?idx=".$db->dt[idx]."'><img src='../images/".$admininfo[language]."/btn_quick_view.gif'  style='cursor:pointer' align='absmiddle'></a></td>
			  </tr>
			";

		}
	}else{
$Contents .= "<tr height='80' align='center' bgcolor='#FFFFFF'>
				<td colspan='16'> 내역이 존재 하지 않습니다.</td>
			</tr>";
	}
	if($QUERY_STRING == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	}

	$Contents .= "
	</table>
	</form>

	<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
		<tr height=40>
			<td align='left' width='25%'><!--img src='../images/".$admininfo[language]."/btn_select_del.gif'  style='cursor:pointer' align='absmiddle' id='del_btn'-->
			<input type='button' id='tax_update_btn' value='선택업데이트' >
			<input type='button' id='tax_all_update_btn' value='전체업데이트'  >
			</td>
			<td colspan='12' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string."#list_top","")."&nbsp;</td>
		</tr>
	</table>
	<iframe name='PROC' id='PROC' width='0' height='0'></iframe>
	";
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<tr>
		<td valign=middle><img src='/admin/image/icon_list.gif' ></td>
		<td class='small' ><b>발행완료</b> : 공급자가 전자(세금)계산서를 발행하여 국세청 전송을 기다리는 상태입니다.</td>
	</tr>
	<tr>
		<td valign=middle><img src='/admin/image/icon_list.gif' ></td>
		<td class='small' ><b>지연발행</b> : 발행 마감기한을 지나 발행한 전자(세금)계산서로 공급자와 공급받는자 모두 가산세가 부과됩니다.</td>
	</tr>
	<tr>
		<td valign=middle><img src='/admin/image/icon_list.gif' ></td>
		<td class='small' style='line-height:120%'><b>발행취소</b> : 공급자가 발행한 전자(세금)계산서를 국세청 전송 전에 '취소' 한 상태로, 국세청에 전송되지 않습니다.</td>
	</tr>
	<tr>
		<td valign=middle><img src='/admin/image/icon_list.gif' ></td>
		<td class='small' style='line-height:120%' ><b>전송중</b> : 전자(세금)계산서를 국세청으로 전송하였으나, 국세청에서 전송결과가 반환되지 않은 상태입니다.</td>
	</tr>
	<tr>
		<td valign=middle><img src='/admin/image/icon_list.gif' ></td>
		<td class='small' style='line-height:120%' ><b>전송성공</b> : 국세청 전송이 정상적으로 완료된 상태입니다.</td>
	</tr>
	<tr>
		<td valign=middle><img src='/admin/image/icon_list.gif' ></td>
		<td class='small' style='line-height:120%' ><b>전송실페</b> : 국세청 전송이 특정한 사유로 실패한 상태로, 실패사유를 확인하여 다시 발행해야 합니다.</td>
	</tr>
</table>
";
$Contents .= HelpBox("매출조회", $help_text);

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = tax_menu();
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->Navigation = "세금계산서관리 > 매출/매입 문서조회";
	$P->title = "매출/매입 문서조회";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
?>