<?
	include("../class/layout.class");
 	$db = new Database;

	//include_once $DOCUMENT_ROOT."/admin/tax/test_header.php";
	if($max==""){
		$max = 20; //페이지당 갯수
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
		$('#tax_tab1').click(function(){
			$('#tab1_view').slideDown();
		});

		$('#company_write').click(function(){
			window.open('./company_write_step1.php?from=company_list','company','width=550,height=300');
		});

		$('#sch_frm').submit(function(){
			if($('#sch_txt').val() == '')
			{
				alert ('검색어를 입력해주세요.');
				$('#sch_txt').focus();
				return false;
			}

			$('#sch_frm').action = '$PHP_SELF';
			$('#sch_frm').method = 'POST';
		});

		$('#del_btn').click(function(){

			var total_checked = $('input:checkbox[name=\'chk[]\']:checked').length;

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
		$('#pbl_all_btn').click(function(){

			var total_checked = $('input:checkbox[name=\'chk[]\']:checked').length;

			if(total_checked < 1)
			{
				alert ('발행할 게시물을 체크해주세요');
				return;
			}

			$('#frm').attr('action','./proc.publish.php');
			$('#frm').attr('method','POST');
			$('#frm').attr('target','PROC');
			$('#act').val('all');
			$('#frm').submit();

		});

		$('#ex_upload').submit(function(){
			if($('#xls').val() == '')
			{
				alert ('업로드할 파일을 등록해주세요.');
				return false;
			}
		});



	});
	</script>
	";

	$Contents .= "
	<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
			<td align='left' colspan=6> ".GetTitleNavigation("발행예정내역(임시저장)", "세금계산서 관리 > 발행예정내역(임시저장) ")."</td>
		</tr>
	</table>

	<!--form name='ex_upload' id='ex_upload' method='post' action='./proc.ex_upload.php' target='PROC' enctype='multipart/form-data'>
	
	<table width='100%' cellpadding='0' cellspacing='1' border='0' class='list_table_box'>
	   <col width=20%>
		<col width=30%>
		<col width=20%>
		<col width=30%>
	  <tr height='30' >
		<td class='input_box_title'>엑셀파일다운로드</td>
		<td class='list_box_td' style='padding:5px 5px 5px 5px; text-align:left;' colspan='3'><img src='../images/".$admininfo[language]."/btn_sample_excel_save.gif' id='add_btn' style='cursor:pointer' align='absmiddle' onclick='location.href=\"./Tax_Excel.xls\"'></td>
	  </tr>
	  <tr height='30' bgcolor='#FFFFFF'>
		<td class='input_box_title' >엑셀파일등록</td>
		<td class='list_box_td' style='padding:5px 5px 5px 5px; text-align:left;' colspan='3'>
		<input type='file' class='textbox' name='xls' style='height: 22px; width: 200px; border: 1px solid rgb(204, 204, 204);' validation='true' title='엑셀파일등록'>
		</td>
	  </tr>
	</table>
	<div style='width:100%;height:50px;padding:5px 5px 5px 5px' align='center'><input type='image' src='./img/publish.gif'></div>
	</form-->

";
include_once "./inc.search.php";
$Contents .= "

	<form name='frm' id='frm'>
	<input type='hidden' name='act' id='act' value='' />
	<table width='100%' cellpadding='0' cellspacing='0' border='0'  class='list_table_box' style='margin:10px 0 0 0'>
	  <tr height='30' align='center'>
		<td class='m_td'><input type='checkbox' name='' id='' onclick='total_check()'></td>
		<td class='m_td'>문서종류</td>
		<td class='m_td'>구분</td>
		<td class='m_td'>작성일자</td>
		<td class='m_td'>거래처<br>사업자번호</td>
		<td class='m_td'>과세형태</td>
		<td class='m_td'>공급가액</td>
		<td class='m_td'>세액</td>
		<td class='m_td'>합계</td>
		<td class='m_td'>발행형태</td>
		<td class='m_td'>지연<br>발행</td>
		<td class='m_td'>문서<br>형태</td>
		<td class='e_td'>수정</td>
	  </tr>
	";

	$where = "WHERE status = '0'";
		
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
			$where .= "and (r_company_name LIKE '%".trim($search_text)."%'  or r_company_number LIKE '%".trim($r_company_number)."%') ";
		}else{
			$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
		}
	}
	//$WHERE = " WHERE publish_type = '1' ";

	// 리스트 셋
	/*$CPage = (!$CPage || $CPage < 1) ? 1  : $CPage;		// 현재 페이지 1
	$LNum  = (!$LNum || $LNum < 1)   ? 15 : $LNum;		// 리스트 수 15
	$PNum  = (!$PNum || $PNum < 1)   ? 10 : $PNum;		// 페이지 수 10
*/
//echo $where;
	//전체 갯수
	$TQuery = "SELECT * FROM tax_sales ".$where;
	$db->query($TQuery);
	$total = $db->total;

	


/*
	// 페이지 클래스
	include_once $DOCUMENT_ROOT."/admin/class/class.PageDivide.php";
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
			$publish_kind = "<span style='color:red;'>역발행</span>";
		}
		if($db->dt[publish_type] == 3)
		{
			$publish_type = "<span style='color:blue;'>위수탁</span>";
			$send_url = "sales_write2.php";
		}

		$tax_type = $db->dt[tax_type];
		if($tax_type == 1) $tax_view = "세금";
		if($tax_type == 2) $tax_view = "계산";

		$tax_per = $db->dt[tax_per];	// 과세형태
		if($tax_per == 1) $tax_show = "<span style='color:red;'>과세</span>";
		if($tax_per == 2) $tax_show = "영세";
		if($tax_per == 3) $tax_show = "면세";

		$claim_kind = $db->dt[claim_kind];
		if($claim_kind == 1) $claim_show  = "영수";
		if($claim_kind == 2) $claim_show = "청구";
		
		
		
		//$y = substr( $db->dt[signdate], 0, 4 );
		$m = substr( $db->dt[signdate], 5, 2 );
		//$d = substr( $db->dt[signdate], 8, 2 );

		$delay_basic_month =  date("m", mktime(0,0,0, $m+1));
		
		$basic_date = date('Y-m-d');
		
		$delay_base = date('Y-'.$delay_basic_month.'-10');


		
				
		if($basic_date <= $delay_base){
			$delay_text = "일반";
			
		}else{
			$delay_text = "<span style='color:red;'>지연</span>";
		}
		
		if($db->dt[m_kind] !=''){
			$m_kind = "<span style='color:red;'>수정</span>";
		}else{
			$m_kind = "일반";
		}
		//echo $delay_base;

		$Contents .= "
		  <tr height='30' align='center' bgcolor='#FFFFFF'>
			<td class='list_box_td list_bg_gray'><input type='checkbox' name='chk[]' id='chk[]' value='".$db->dt[idx]."'></td>
			<td class='list_box_td'>".$tax_view."</td>
			<td class='list_box_td list_bg_gray'>".$publish_type."</td>
			<td class='list_box_td'>".$db->dt[signdate]."</td>";
			if($db->dt[publish_type] == '1'){
			$Contents .= "
			<td class='list_box_td list_bg_gray'>".$db->dt[r_company_name]."<br>".$db->dt[r_company_number]."</td>";
			}else{
			$Contents .= "
			<td class='list_box_td list_bg_gray'>".$db->dt[s_company_name]."<br>".$db->dt[r_company_number]."</td>";
			}
			$Contents .= "
			<td class='list_box_td'>".$tax_show."</td>
			<td class='list_box_td list_bg_gray'>".number_format($db->dt[supply_price])."</td>
			<td class='list_box_td list_bg_gray'>".number_format($db->dt[tax_price])."</td>
			<td class='list_box_td list_bg_gray'>".number_format($db->dt[total_price])."</td>
			<td class='list_box_td list_bg_gray'>".$publish_kind."</td>
			<td class='list_box_td list_bg_gray'>".$delay_text."</td>
			<td class='list_box_td list_bg_gray'>".$m_kind."</td>
			<td class='list_box_td'><a href='./".$send_url."?idx=".$db->dt[idx]."&tax_type=$tax_type'><img src='../images/".$admininfo[language]."/bts_modify.gif'  style='cursor:pointer' align='absmiddle'></a></td>
		  </tr>
		";

	}
	}else{
$Contents .= "<tr height='80' align='center' bgcolor='#FFFFFF'>
				<td colspan='13'> 내역이 존재 하지 않습니다.</td>
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
			<td align='left' width='25%'><img src='../images/".$admininfo[language]."/btn_select_public.gif'  style='cursor:pointer' align='absmiddle' id='pbl_btn'> <img src='../images/".$admininfo[language]."/btn_publish_IRS_all.gif'  style='cursor:pointer' align='absmiddle' id='pbl_all_btn'> <img src='../images/".$admininfo[language]."/btn_select_del.gif'  style='cursor:pointer' align='absmiddle' id='del_btn'> </td>
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
	$P->Navigation = "세금계산서관리 > 대량/임시 발행 리스트";
	$P->title = "대량/임시 발행 리스트";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
?>