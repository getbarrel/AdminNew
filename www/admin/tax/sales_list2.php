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
	
	$search_type = $_REQUEST[search_type];
	$search_text = $_REQUEST[search_text];

	/*if($s_type == "total" || $s_type == "")			$checked1 = "checked";
	if($s_type == "r_company_number")				$checked2 = "checked";
	if($s_type == "r_company_name")					$checked3 = "checked";
*/
	$Contents = "
	<script src='tax.js'></script>
	<script src='/admin/js/calendar.js'></script>
	<script>
	var tc = 0;
	function total_check()
	{
		if(tc%2 == 0)	$('input:checkbox[name=\'chk[]\']').attr('checked',true);
		else			$('input:checkbox[name=\'chk[]\']').removeAttr('checked');

		tc++;
	}

	$(document).ready(function(){

		$('#company_write').click(function(){
			window.open('./company_write_step1.php?from=company_list','company','width=550,height=300');
		});

		$('#del_btn').click(function(){

			var total_checked = $('input:checkbox[name=\'chk[]\']:checked').length;

			if(total_checked < 1
			{
				alert ('삭제할 게시물을 체크해주세요');
				return;
			}

			$('#frm').attr('action','./sales_del.php');
			$('#frm').attr('method','POST');
			$('#frm').attr('target','PROC');
			$('#frm').submit();
		});

		$('#pbl_btn').click(function(){

			var total_checked = $('input:checkbox[name=\'chk[]\']:checked').length;

			if(total_checked < 1)
			{
				alert ('발행할 게시물을 체크해주세요');
				return;
			}

			$('#frm').attr('action','./proc.publish.php');
			$('#frm').attr('method','POST');
			$('#frm').attr('target','PROC');
			$('#frm').submit();

		});

	});
	</script>
	";

	$Contents .= "
	<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("발행예정내역(임시저장)", "세금계산서 관리 > 발행예정내역(임시저장) ")."</td>
		</tr>
	</table>
	";

	include_once "./inc.search.php";

	$Contents .= "
	<form name='frm' id='frm'>
	<table width='100%' cellpadding='0' cellspacing='1' border='0' class='list_table_box' style='margin:10px 0 0 0'>
	  <tr height='30' align='center' >
		<td class='s_td'><input type='checkbox' name='' id='' onclick='total_check()'></td>
		<td class='m_td'>문서종류</td>
		<td class='m_td'>구분</td>
		<td class='m_td'>작성일자</td>
		<td class='m_td''>공급받는자</td>
		<td class='m_td'>대표자</td>
		<td class='m_td'>사업자번호(주민번호)</td>
		<td class='m_td'>과세형태</td>
		<td class='m_td'>공급가액</td>
		<td class='m_td'>영수청구</td>
		<td class='m_td'>발행형태</td>
		<td class='e_td'>수정</td>
	  </tr>
	";
	
	$where = " where status ='0' ";
	
	
	if($date_search != ""){
		$startDate = $_POST[startDate];
		$endDate = $_POST[endDate];
		if($date_type == 're_signdate'){
			if($startDate != "" && $endDate != "")	$where .= " AND re_signdate BETWEEN '$startDate' AND '$endDate'";
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

	if($search_type && $search_text){
		if($search_type == "r_company_number"){
			$where .= "and (r_company_name LIKE '%".trim($search_text)."%'  or r_company_number LIKE '%".trim($r_company_number)."%') ";
		}else{
			$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
		}
	}
	/*
	if($sch_txt != "")
	{
		if($detail_search == "Y")
		{
			$dateS = $_POST[dateS];
			$dateE = $_POST[dateE];
			if($dateS != "" && $dateE != "")	$add_w = " AND signdate BETWEEN '$dateS' AND '$dateE'";

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

		/*	# 과세형태
			for($i=0; $i < sizeof($pstat); $i++)
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
		}


		if($s_type != "total")	$WHERE = " WHERE $s_type like '%$sch_txt%' AND status = '2'".$add_w;
		else					$WHERE = " WHERE (s_company_number like '%$sch_txt%' OR s_company_name like '%$sch_txt%' OR s_name like '%$sch_txt%') AND status = '2'".$add_w;
	}
	else
	{
		$WHERE = "WHERE status = '2'";
	}*/
	//echo $WHERE;

	//$WHERE = " WHERE publish_type = '1' ";

	// 리스트 셋
	/*$CPage = (!$CPage || $CPage < 1) ? 1  : $CPage;		// 현재 페이지 1
	$LNum  = (!$LNum || $LNum < 1)   ? 15 : $LNum;		// 리스트 수 15
	$PNum  = (!$PNum || $PNum < 1)   ? 10 : $PNum;		// 페이지 수 10
*/
	//전체 갯수
	$TQuery = "SELECT * FROM tax_sales ".$where;
	$db->query($TQuery);
	$total = $db->total;

	

	// 페이지 클래스
	/*include_once $DOCUMENT_ROOT."/admin/class/class.PageDivide.php";
	$pageDivide = new PageDivide($TPage,$PNum,$CPage," | ");
	$PAGES = $pageDivide->Page_Divide("","&m=$_GET[m]&kind=$_GET[kind]");

	$LinkPagePrev	= $PAGES[PagePrev];
	$LinkPageList	= $PAGES[PageList];
	$LinkPageNext	= $PAGES[PageNext];
	$LinkListPrev	= $PAGES[ListPrev];
	$LinkListNext	= $PAGES[ListNext];
*/
	$SQL = "SELECT * FROM tax_sales ".$where." ORDER BY idx DESC LIMIT $start, $max";
	$db->query($SQL);
	if($total > 0){
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		if($db->dt[publish_type] == 1)
		{
			$publish_type = "매출";
			$send_url = "sales_write.php";
			$publish_kind = "정발행";
		}
		if($db->dt[publish_type] == 2)
		{
			$publish_type = "매입";
			$send_url = "purchase_write.php";
			$publish_kind = "역발행";
		}
		if($db->dt[publish_type] == 3)
		{
			$publish_type = "위수탁";
			$publish_kind = "위수탁";
			$send_url = "sales_write2.php";
		}

		$tax_type = $db->dt[tax_type];
		if($tax_type == 1) $tax_view = "세금";
		if($tax_type == 2) $tax_view = "계산";

		$tax_per = $db->dt[tax_per];	// 과세형태
		if($tax_per == 1) $tax_show = "과세";
		if($tax_per == 2) $tax_show = "영세";

		$claim_kind = $db->dt[claim_kind];
		if($claim_kind == 1) $claim_show  = "영수";
		if($claim_kind == 2) $claim_show = "청구";



		$Contents .= "
		  <tr height='30' align='center' bgcolor='#FFFFFF'>
			<td class='list_box_td list_bg_gray'><input type='checkbox' name='chk[]' id='chk[]' value='".$db->dt[idx]."'></td>
			<td class='list_box_td'>".$tax_view."</td>
			<td class='list_box_td list_bg_gray'>".$publish_type."</td>
			<td class='list_box_td'>".$db->dt[signdate]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[r_company_name]."</td>
			<td class='list_box_td'>".$db->dt[r_name]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[r_company_number]."</td>
			<td class='list_box_td'>".$tax_show."</td>
			<td class='list_box_td list_bg_gray'>".number_format($db->dt[supply_price])."</td>
			<td class='list_box_td'>".$claim_show."</td>
			<td class='list_box_td list_bg_gray'>".$publish_kind."</td>
			<td class='list_box_td'><a href='./".$send_url."?idx=".$db->dt[idx]."'><img src='../images/".$admininfo[language]."/bts_modify.gif'  style='cursor:pointer' align='absmiddle'></a></td>
		  </tr>
		";

	}
	}else{
$Contents .= "<tr height='80' align='center' bgcolor='#FFFFFF'>
				<td colspan='12'> 내역이 존재 하지 않습니다.</td>
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
			<td align='left' width='25%'><img src='../images/".$admininfo[language]."/btn_select_public.gif'  style='cursor:pointer' align='absmiddle'  id='pbl_btn'>  <img src='../images/".$admininfo[language]."/btn_select_del.gif'  style='cursor:pointer' align='absmiddle'  id='del_btn'></td>
			<td colspan='12' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string."#list_top","")."&nbsp;</td>
		</tr>
	</table>
	<iframe name='PROC' id='PROC' width='0' height='0'></iframe>
	";

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = tax_menu();
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->Navigation = "세금계산서관리 > 임시저장 문서함";
	$P->title = "임시저장 문서함";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
?>