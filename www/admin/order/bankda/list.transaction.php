<?php 
/**
 * 거래내역 조회 페이지
 * @date 2013.09.23
 * @author bgh
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/admin/class/layout.class';
#require $_SERVER['DOCUMENT_ROOT'].'/admin/order/bankda/src/bankda.php';
require $_SERVER['DOCUMENT_ROOT'].'/admin/order/bankda/src/bankda_new.php';
$bankda = new bankda();

$tokenData = $bankda->getToken();

if($tokenData['code'] != 200){
    echo "<script> alert('".$tokenData['msg']."') </script>";
    exit;
}

$postValue = $_GET;

/* 페이징 기본값 */
if(empty($postValue['max'])){
	$max = 10;
	$postValue['max'] = 10;
}
/* 전체,입금,출금 구분 기본값 */
if(empty($div)){
	$div = 'all';
}
/* 검색 날짜 기본 값*/
if(empty($postValue['sdate'])){
	$sdate = date('Ymd',strtotime('-7days'));
	$postValue['sdate'] = $sdate;
}
if(empty($postValue['edate'])){
	$edate = date('Ymd');
	$postValue['edate'] = $edate;
}
/* 거래내역 */
$transList = $bankda->getTransactionList($postValue);

/* 계좌정보 */
$accountList = $bankda->getAccountList();
/* 계좌 잔액 */
$accountBalance = $bankda->getAccountBalance();

/* QUERY STRING */
$query_string = "&".$bankda->buildHttpQuery($postValue,'N');

/* 공통 메뉴 */
require "tabMenu.php"; 

$Contents .= "
<table width='100%' cellpadding='0' cellspacing='0' border=0>";
/* 계좌 잔액 */
if(!empty($accountBalance)){
	$Contents .= "
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../../images/dot_org.gif' align=absmiddle> <b class=blk>계좌 잔액</b></td>
				</tr>
				<tr>
					<td colspan=2 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05'>
									<TABLE cellSpacing=0 cellPadding=3 style='width:100%;' align=center border=0 class='search_table_box'>
										<tr>
											<th class='search_box_title'>은행명</th>";
									if(!empty($accountBalance)){
										foreach($accountBalance as $ab):
											$Contents.=
											"<td class='list_item' align='center'>".$ab["Bkname"]."</br>(".$ab["Bkacctno"].")</td>";
										endforeach;
									}
									$Contents.="
										<tr>
											<th class='search_box_title'>잔액</th>";
									if(!empty($accountBalance)){
										$totalJango = 0;
										foreach($accountBalance as $ab):
											$Contents.=
											"<td class='list_item' align='center'>
													<b><span style='color:red;'>".number_format($ab["Bkjango"])." 원</span></b>
											</td>";
											$totalJango += $ab["Bkjango"];
										endforeach;
									}
									$Contents.="
										</tr>
										</tr>
										<tr height=30>
											<th class='search_box_title'>총 잔액</th>
											<td class='list_item' align='right' colspan=".count($accountBalance).">
												<b><span style='color:red;margin-right:20px;'>".number_format($totalJango)." 원</span></b>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>			
	";
}	
/* 검색 조건 테이블 */
$Contents .= "
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0>
			<form name='search_frm' method='get' action=''>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../../images/dot_org.gif' align=absmiddle> <b class=blk>검색</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05'>
									<TABLE cellSpacing=0 cellPadding=3 style='width:100%;' align=center border=0 class='search_table_box'>
										<col width=15%>
										<col width=35%>
										<col width=15%>
										<col width=35%>
										<tr height=30>
											<th class='search_box_title'>은행</th>
											<td class='search_box_item'>
												<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<col width='170px'>
													<col width='*'>
													<tr>
														<td >
														<select name='Bkacctno' id='BkacctnoSelectBox' style='font-size:12px;'>
															<option value=''>선택하세요</option>";
															if(!empty($accountList)){
																foreach($accountList as $al):
																	$Contents .= "<option value='".$al['Bkacctno']."' ".CompareReturnValue($al['Bkacctno'],$Bkacctno,' selected').">".$al['Bkname']."(".$al['Bkacctno'].")</option>";
																endforeach;
															}
$Contents .= "
														</select>
														</td>
													</tr>
												</table>
											</td>
											<th class='search_box_title'>내역수</th>
											<td class='search_box_item'>
												<select name='max'>
													<option value='10' ".CompareReturnValue('10',$max,' selected').">10</option>
													<option value='20' ".CompareReturnValue('20',$max,' selected').">20</option>
													<option value='50' ".CompareReturnValue('50',$max,' selected').">50</option>
													<option value='100' ".CompareReturnValue('100',$max,' selected').">100</option>
												</select>
											</td>
										</tr>
										<tr height=30>
											<th class='search_box_title'>구분</th>
											<td class='search_box_item'>
												<input type='radio' name='div' id='div_all' value='all' ".CompareReturnValue('all',$div,' checked')." ><label for='div_all'>전체</label>
												<input type='radio' name='div' id='div_input' value='input' ".CompareReturnValue('input',$div,' checked')." ><label for='div_input'>입금</label>
												<input type='radio' name='div' id='div_output' value='output' ".CompareReturnValue('output',$div,' checked')." ><label for='div_output'>출금</label>
											</td>
											<th class='search_box_title' >내역/금액/입금자</th>
											<td class='search_box_item'>
												<input type='text' class=textbox name='text' size='30' value='$text' style=''>
											</td>
										</tr>
										<tr height=33>
											<th class='search_box_title'>기간</th>
											<td class='search_box_item' colspan=3>
												<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
												~
												<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
											</td>
										</tr>
									</TABLE>
								</td>
								<th class='box_06'></th>
							</tr>
							<tr>
								<th class='box_07'></th>
								<td class='box_08'></td>
								<th class='box_09'></th>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'>
			<input type='image' src='../../images/".$admininfo["language"]."/bt_search.gif' border=0>
			<img src='./btn/btn_matchNow.gif' style='vertical-align: middle;' onclick='immediatelyUpdate();'>
		</td>
	</tr>
	</form>
</table>";

//리스트											
$Contents .= "
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >
		<td width='5%' align='center' class='m_td'><b>번호</b></td>
		<td width='11%' align='center'  class='m_td' nowrap><b>일자</b></td>
		<td width='11%' align='center' class='m_td' nowrap><b>은행명</b></td>
		<td width='11%' align='center' class='m_td' nowrap><b>계좌번호</b></td>
		<td width='11%' align='center' class='m_td' nowrap><b>입금자명</b></td>
		<td width='*'   align='center' class='m_td' nowrap><b>내역</b></td>
		<td width='11%' align='center' class='m_td' nowrap><b>입금액</b></td>
		<td width='11%' align='center' class='m_td' nowrap><b>출금액</b></td>
		<td width='11%' align='center' class='m_td' nowrap><b>잔액</b></td>
	</tr>";
if(!empty($transList['list'])){
	foreach($transList['list'] as $tl):
		$Contents .="
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap>".$tl['Bkid']."</td>
			<td class='list_box_td' align='center' nowrap>".$tl["Bkxferdatetime"]."</td>
			<td class='list_box_td' align='center' nowrap>".$tl['Bkname']."</td>
			<td class='list_box_td' align='center' nowrap>".$tl['Bkacctno']."</td>
			<td class='list_box_td' align='center' nowrap>".$tl['Bkjukyo']."</td>
			<td class='list_box_td' align='center' nowrap>".$tl['Bkcontent']."(".$tl['Bketc'].")</td>
			<td class='list_box_td' align='center' nowrap>";
			if($tl['Bkinput'] > 0){
				$Contents .=number_format($tl['Bkinput'])."원</td>";
			}else{
				$Contents .="</td>";
			}
			$Contents .="
			<td class='list_box_td' align='center' nowrap>";
			if($tl['Bkoutput'] > 0){
				$Contents .=number_format($tl['Bkoutput'])." 원</td>";
			}else{
				$Contents .="</td>";
			}			
			$Contents .="
			<td class='list_box_td' align='center' nowrap>".number_format($tl['Bkjango'])." 원</td>
		</tr>";
	endforeach;
}else{
		$Contents .="
		<tr height=128 >
			<td class='list_box_td' align='center' colspan=9 nowrap>등록된 계좌의 거래내역 정보가 없습니다. </td>
		</tr>";

}
$Contents.="
</table>"; //list table close
/* 거래내역 요약 */
if(!empty($transList['summary'])){
	$input_total = 0;
	$output_total = 0;
	$Contents.="
	<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' style='margin-top:2px;'>
		<tr height='25' >
			<td width='10%' align='center' class='m_td' nowrap><b>은행명</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b>입금액</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b>출금액</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b><span style='color:red'>합계</span></b></td>
		</tr>";
	foreach($transList['summary'] as $tls):
		$Contents.="
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap>".$tls['Bkname']."(".$tls['Bkacctno'].")</td>
			<td class='list_box_td' align='center' nowrap>".number_format($tls['input'])." 원</td>
			<td class='list_box_td' align='center' nowrap>".number_format($tls['output'])." 원</td>
			<td class='list_box_td' align='center' nowrap><span style='color:red'>".number_format($tls['total'])." 원</span></td>
		</tr>
			";
		$input_total += $tls['input'];
		$output_total += $tls['output'];
	endforeach;
	$Contents.="
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap><span style='color:red'>총 거래금액</span></td>
			<td class='list_box_td' align='center' colspan=3 nowrap>
				<span style='color:red'>
					기간내 총 입금 : ".number_format($input_total)." 원
				</span>
				|
				<span style='color:blue'>
					기간내 총 출금 : ".number_format($output_total)." 원
				</span>
				|
				<span style='color:red'>
					총합 : ".number_format($input_total + $output_total)." 원
				</span>
			</td>
		</tr>
		<tr height=40>
			<td colspan='12' align='center' >&nbsp;".page_bar($transList['total'], $page, $max, $query_string,"")."&nbsp;</td>
		</tr>
		";
	$Contents.="
	</table>
	";	
}

$Script .= "
<script language='javascript'>
$(function() {
	$(\"#start_datepicker\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
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

	$(\"#end_datepicker\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'
	});

});


function immediatelyUpdate(){
	var bkacctno = $('#BkacctnoSelectBox').val();
	if(bkacctno != undefined && bkacctno != ''){
		var select = confirm(bkacctno + ' : 즉시조회는 계좌별로 15분에 한번 사용할 수 있습니다. 사용하시겠습니까?');
		if(select){
			eventDetail=window.open('','act','');
	    	eventDetail.location='/admin/order/bankda/bankda.act.php?act=immediatelyUpdate&bkacctno=' + bkacctno;
		}
	}else{
		alert('계좌번호를 선택하세요.');
	}
}
</script>
";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../../include/DateSelect.js'></script>\n<script language='javascript' src='bankda.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->Navigation = "주문관리 > 무통장 자동입금확인";
$P->title = "거래내역/출력";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();