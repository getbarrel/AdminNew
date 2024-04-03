<?php 
/**
 * 거래내역 조회 페이지
 * @date 2013.09.23
 * @author bgh
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/admin/class/layout.class';
require $_SERVER['DOCUMENT_ROOT'].'/admin/order/bankda/src/bankda.php';

$bankda = new bankda();

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

/* 
검색 날짜 기본 값
if(empty($postValue['sdate'])){
	$sdate = date('Ymd',strtotime('-7days'));
	$postValue['sdate'] = $sdate;
}
*/

if(empty($postValue['edate'])){
	$edate = date('Ymd');
	$postValue['edate'] = $edate;
}
$postValue['div'] = 'input';
/* 거래내역 */
$transList = $bankda->getTransactionList($postValue);
/* 계좌정보 */
$accountList = $bankda->getAccountList();
/* 계좌 잔액 */
//$accountBalance = $bankda->getAccountBalance();
/* QUERY STRING */
$query_string = "&".$bankda->buildHttpQuery($postValue,'N');


$Contents .= "
<table width='100%' cellpadding='0' cellspacing='0' border=0>";

/* 검색 조건 테이블 */
$Contents .= "
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0>
			<form name='search_frm' method='get' action=''>
				
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
											<th class='search_box_title'>은행명</th>
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
											<th class='search_box_title'>처리상태</th>
											<td class='search_box_item'>
												<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<col width='170px'>
													<col width='*'>
													<tr>
														<td >
														<select name='match_result' style='font-size:12px;'>
															<option value=''>선택하세요</option>
															<option value='all' ".CompareReturnValue('',$match_result,' selected').">선택하세요</option>
															<option value='ready' ".CompareReturnValue('ready',$match_result,' selected').">입금대기</option>
															<option value='success' ".CompareReturnValue('success',$match_result,' selected').">성공</option>
															<option value='manualSuccess' ".CompareReturnValue('manualSuccess',$match_result,' selected').">성공(관리자)</option>
															<option value='duplicate' ".CompareReturnValue('duplicate',$match_result,' selected').">동명이인</option>
															<option value='fail' ".CompareReturnValue('fail',$match_result,' selected').">불일치</option>

														</select>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr height=30>
											<th class='search_box_title'>입금자명</th>
											<td class='search_box_item'>
												<input type='text' name='bank_name' value=''>
											</td>
											<th class='search_box_title' >입금금액</th>
											<td class='search_box_item'>
												<input type='text' class=textbox name='bank_price' size='30' value='$bank_price' style=''>
											</td>
										</tr>
										<tr height=30>
											<th class='search_box_title'>주문번호</th>
											<td class='search_box_item'>
												<input type='text' name='bank_oid' value='$bank_oid'>
											</td>
											<th class='search_box_title' >메모</th>
											<td class='search_box_item'>
												<input type='text' class=textbox name='bank_memo' size='30' value='$bank_memo' style=''>
											</td>
										</tr>
										<tr height=33>
											<th class='search_box_title'>입금일</th>
											<td class='search_box_item' colspan=3>
												<input type='text' name='input_sdate' class='textbox' value='".$input_sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
												~
												<input type='text' name='input_edate' class='textbox' value='".$input_edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
											</td>
										</tr>
										<tr height=33>
											<th class='search_box_title'>매칭일</th>
											<td class='search_box_item' colspan=3>
												<input type='text' name='matching_sdate' class='textbox' value='".$matching_sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker2'>
												~
												<input type='text' name='matching_edate' class='textbox' value='".$matching_edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker2'>
											</td>
										</tr>
										<tr>
											<th class='search_box_title'>내역수</th>
											<td class='search_box_item' colspan=3>
												<select name='max'>
													<option value='10' ".CompareReturnValue('10',$max,' selected').">10</option>
													<option value='20' ".CompareReturnValue('20',$max,' selected').">20</option>
													<option value='50' ".CompareReturnValue('50',$max,' selected').">50</option>
													<option value='100' ".CompareReturnValue('100',$max,' selected').">100</option>
													<option value='1000' ".CompareReturnValue('1000',$max,' selected').">1000</option>
												</select>
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
			<a href='javascript:goManualUpdate();'><img src='./btn/btn_manualUpdate.gif' style='vertical-align: middle;'></a>
			<!--img src='./btn/btn_matchNow.gif' style='vertical-align: middle;' onclick='immediatelyUpdate();'-->
		</td>
	</tr>
	</form>
</table>";

//리스트											
$Contents .= "
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >
		<td width='5%' align='center' class='m_td'><b>번호</b></td>
		<td width='10%' align='center'  class='m_td' nowrap><b>입금일</b></td>
		<td width='10%' align='center' class='m_td' nowrap><b>은행명</b></td>
		<td width='10%' align='center' class='m_td' nowrap><b>계좌번호</b></td>
		<td width='10%' align='center' class='m_td' nowrap><b>입금금액</b></td>
		<td width='10%' align='center' class='m_td' nowrap><b>입금자명</b></td>
		<td width='10%' align='center' class='m_td' nowrap><b>처리상태</b></td>
		<td width='*'   align='center' class='m_td' nowrap><b>매칭일</b></td>
		
		<td width='10%' align='center' class='m_td' nowrap><b>주문번호</b></td>
		<td width='10%' align='center' class='m_td' nowrap><b>간단메모</b></td>
	</tr>";

if(!empty($transList['list'])){
	foreach($transList['list'] as $tl):
		$db = new MySQL;
		$sql = "select 
						bm.*,o.status, (select count(*) from bankda_match_self s where s.oid=bm.match_oid) as self_cnt 
						from bankda_match as bm 
					left join 
						shop_order o on bm.match_oid = o.oid
					left join
						shop_order_detail od on bm.match_oid = od.oid
					where match_bkid = '".$tl['Bkid']."'";
		$db->query($sql);
		$db->fetch();
		$match_result_text = "";
		switch($db->dt['match_result']){
			case 'ready':
				$match_result_text = "입금대기";
				break;
			case 'success':
				$match_result_text = "성공".($db->dt['self_cnt'] > 0 ? "(수동)" : "(자동)");
				break;
			case 'duplicate':
				if($db->dt['status'] != 'IR'){
					$match_result_text = "성공(관리자)";
				}else{
					$match_result_text = "동명이인";
				}
				break;
			case 'fail':
				$match_result_text = "불일치";
				break;
			default:
				
				break;
		}
		switch($db->dt['match_type']){
			case 'save':
				$match_type_text = "(M포인트)";
				break;
			case 'order':
				//$match_type_text = "(주문)";
				//170821 성공(주문)이 아니라 성공(수동), 성공(자동)으로 노출시켜달라는 요청 있었음. 모바일앤유는 M포인트를 쓰지 않으므로 성공 옆에 수동, 자동 여부 넣고 해당 부분은 주석처리함 pde
				break;
			default:
				$match_type_text = "";
				break;
		}
		$Contents .="
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap>".$tl['Bkid']."</td>
			<td class='list_box_td' align='center' nowrap>".$tl["Bkdate"]."</td>
			<td class='list_box_td' align='center' nowrap>".$tl['Bkname']."</td>
			<td class='list_box_td' align='center' nowrap>".$tl['Bkacctno']."</td>
			<td class='list_box_td' align='center' nowrap>";
			if($tl['Bkinput'] > 0){
				$Contents .=number_format($tl['Bkinput'])."원</td>";
			}else{
				$Contents .="</td>";
			}
			$Contents .="
			<td class='list_box_td' align='center' nowrap>".$tl['Bkjukyo']."</td>
			<td class='list_box_td' align='center' nowrap>".$match_result_text." ".$match_type_text."</td>
			<td class='list_box_td' align='center' nowrap>".$tl['matching_date']."</td>
			<td class='list_box_td' align='center' nowrap>";
			if($tl['oid'] == ''){
				$Contents .="<input type='button' value='수동매칭' onClick=\"PopSWindow('match_pop.php?mmode=pop&Bkid=".$tl['Bkid']."&text=".$tl['Bkjukyo']."&mmode=pop',900,710,'match_pop')\"> </td>";
			}else{
				$Contents .="".$tl['oid']."</td>";
			}
			$Contents .="
			</td>
			
			
			<td class='list_box_td memocloud' style='cursor:pointer;' align='center' help_html_".$tl['Bkid']."='".$tl['memo']."' nowrap>".($tl['memo'] != "" ? "<b><font color='red'>메모</font></b>" : "메모")."
				<input type='hidden' name='idx_ix' id='idx_ix' value='".$tl['Bkid']."' >
			</td>
		</tr>";
	endforeach;
}else{
		$Contents .="
		<tr height=128 >
			<td class='list_box_td' align='center' colspan=10 nowrap>등록된 계좌의 거래내역 정보가 없습니다. </td>
		</tr>";

}
$Contents.="
</table>"; //list table close

	$Contents.="
	<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' style='margin-top:20px;'>
		<tr height='25' >
			<td width='20%' align='center' class='m_td' nowrap><b>처리상태</b></td>
			<td width='*' align='center' class='m_td' nowrap><b>내용</b></td>
		</tr>
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap>매칭성공</td>
			<td class='list_box_td' align='center' nowrap>시스템에 의하여 입금확인처리가 완료된 주문건을 나타냅니다</td>
		</tr>
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap>매칭성공(관리자ID)</td>
			<td class='list_box_td' align='center' nowrap>관리자가 이미 입금확인 단계로 변경한 주문건 입니다.</td>
		</tr>
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap>매칭실패</td>
			<td class='list_box_td' align='center' nowrap>입금정보가 맞지않아 매칭실패된 주문건입니다.</td>
		</tr>
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap>매칭실패 (동명이인)</td>
			<td class='list_box_td' align='center' nowrap>입금정보가 동일한 주문이 2건 이상이 있는 주문건입니다.</td>
		</tr>
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap>수동 매칭</td>
			<td class='list_box_td' align='center' nowrap>매칭실패 상태에서 관리자가 주문건을 입금확인으로 변경 한 상태를 나타냅니다.</td>
		</tr>
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap>기타</td>
			<td class='list_box_td' align='center' nowrap>매칭실패 상태에서 입금자를 찾지 못하거나, 주문입금건이 아닌 경우 매칭범위에서 제외 시키려면 “기타”로 변경합니다</td>
		</tr>
		";
	$Contents.="
	</table>
	";	


$Script .= "
<script language='javascript'>
$(function() {
	$(\"#start_datepicker\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
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
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'
	});
$(\"#start_datepicker2\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			if($('#end_datepicker2').val() != '' && $('#end_datepicker2').val() <= dateText){
				$('#end_datepicker2').val(dateText);
			}else{
				$('#end_datepicker2').datepicker('setDate','+0d');
			}
		}
	});

	$(\"#end_datepicker2\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
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
function goManualUpdate(){
	
	eventDetail=window.open('','act','');
    eventDetail.location='/admin/order/bankda/bankda.act.php?act=update';
}
</script>
";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../../include/DateSelect.js'></script>\n<script language='javascript' src='bankda.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->Navigation = "주문관리 > 실시간 자동입금확인";
$P->title = "실시간 자동입금확인";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();