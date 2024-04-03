<?php 
/**
 * 자동 입금확인 조회 페이지
 * @date 2013.09.23
 * @author bgh
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/admin/class/layout.class';
require $_SERVER['DOCUMENT_ROOT'].'/admin/order/bankda/src/bankda.php';

$db = new database;

$sql = "select * from bankda_match where match_bkid='".$Bkid."' and match_result='success'";
$db->query($sql);
$success_cnt = $db->total;

$bankda = new bankda();

$postValue = $_GET;

/* 페이징 기본값 */
if(empty($postValue['max'])){
	$max = 10;
	$postValue['max'] = 10;
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
/* 무통장 주문 내역 */
$postValue["match_result"] = "ready";
$postValue["text"] = $_GET['text'];
$postValue["search_div"] = 'name';

$orderList = $bankda->getOrderList($postValue);
/* 계좌정보 */
$accountList = $bankda->getAccountList();

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
			<input type='hidden' name ='Bkid' value='".$Bkid."' />
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../../images/dot_org.gif' align=absmiddle> <b class=blk>입금예정 주문 검색하기</b></td>
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
											<th class='search_box_title' >통합검색</th>
											<td class='search_box_item' colspan=3>
												<select name='search_div'>
													<option value='all' ".CompareReturnValue('all',$search_div,' selected').">전체</option>
													<option value='name' ".CompareReturnValue('name',$search_div,' selected').">주문/입금자명</option>
													<option value='rname' ".CompareReturnValue('rname',$search_div,' selected').">수취인명</option>
													<option value='price' ".CompareReturnValue('price',$search_div,' selected').">입금예정금액</option>
													<option value='oid' ".CompareReturnValue('oid',$search_div,' selected').">주문번호</option>
												</select>
												<input type='text' class=textbox name='text' size='30' value='$text' style=''>
											</td>
										</tr>
										<!--tr height=30>
											<th class='search_box_title'>은행</th>
											<td class='search_box_item'>
												<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<col width='170px'>
													<col width='*'>
													<tr>
														<td >
														<select name='Bkacctno' style='font-size:12px;'>
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
											<th class='search_box_title' >현재상태</th>
											<td class='search_box_item'>
												<select name='match_result'>
													<option value='all' ".CompareReturnValue('',$match_result,' selected').">선택하세요</option>
													<option value='ready' ".CompareReturnValue('ready',$match_result,' selected').">입금대기</option>
													<option value='success' ".CompareReturnValue('success',$match_result,' selected').">성공</option>
													<option value='manualSuccess' ".CompareReturnValue('manualSuccess',$match_result,' selected').">성공(관리자)</option>
													<option value='duplicate' ".CompareReturnValue('duplicate',$match_result,' selected').">동명이인</option>
													<option value='fail' ".CompareReturnValue('fail',$match_result,' selected').">불일치</option>
												</select>
											</td>
											
										</tr-->
										<tr height=33>
											<th class='search_box_title'>주문일</th>
											<td class='search_box_item'>
												<input type='text' name='sdate' id='start_datepicker' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' >
												~
												<input type='text' name='edate' id='end_datepicker' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' >
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
		</td>
	</tr>
	</form>
</table>";

$transList = $bankda->getTransactionListByOne($Bkid);

$Contents .= "
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class=''>
	<tr height=25>
		<td style='border-bottom:2px solid #efefef'><img src='../../images/dot_org.gif' align=absmiddle> <b class=blk>매칭대상 입금 정보</b></td>
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
								<th class='search_box_title' >입금자명</th>
								<td class='search_box_item' >
									".$transList[0]["Bkjukyo"]."
								</td>
								<th class='search_box_title'>입금은행</th>
								<td class='search_box_item'>
									".$transList[0]["Bkname"]."
								</td>
							</tr>
							<tr height=30>
								<th class='search_box_title'>입금금액</th>
								<td class='search_box_item'>
									".number_format($transList[0]["Bkinput"])." 원
								</td>
								<th class='search_box_title' >입금일자</th>
								<td class='search_box_item'>
									
									".date("Y-m-d", strtotime($transList[0]["Bkxferdatetime"]))."
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
</table>";
//리스트	
										
$Contents .= "
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' style='padding-top:20px;' class=''>
	<tr height=25>
		<td style='border-bottom:2px solid #efefef'><img src='../../images/dot_org.gif' align=absmiddle> <b class=blk>주문내역</b></td>
	</tr>
	<tr>
		<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
			<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
				
				<tr height='25' >
					<td width='11%' align='center' class='m_td' nowrap><b>주문번호</b></td>
					<td width='12%' align='center' class='m_td' nowrap><b>주문자/아이디</b></td>
					<td width='10%'   align='center' class='m_td' nowrap><b>입금자명</b></td>
					<td width='10%' align='center' class='m_td' nowrap><b>결제금액</b></td>
					<td width='10%' align='center' class='m_td' nowrap><b>현재상태</b></td>
					<td width='12%' align='center'  class='m_td' nowrap><b>주문일</b></td>
					<td width='12%' align='center'  class='m_td' nowrap><b>관리</b></td>
					
				</tr>";
			if(!empty($orderList['list'])){
				foreach($orderList['list'] as $ol):
					$memberInfo = $bankda->getMemberInfoByCode($ol['uid']);
					switch($ol['match_result']){
						case 'ready':
							$match_result_text = "입금대기";
							break;
						case 'success':
							$match_result_text = "성공";
							break;
						case 'duplicate':
							if($ol['status'] != 'IR'){
								$match_result_text = "성공(관리자)";
							}else{
								$match_result_text = "동명이인";
							}
							break;
						case 'fail':
							$match_result_text = "불일치";
							break;
						default:
							if($ol['status'] != 'IR' && $ol['status'] != 'CC'){
								$match_result_text = "성공(관리자)";
							}else{
								if($bankda->dateDiff($ol['date'],date('Ymd'),'day') > 7){
									$match_result_text = "기간만료";
								}else{
									$match_result_text = "입금대기";
								}
							}
							break;
					}

					$Contents .="
					<tr height=28 >
						<td class='list_box_td' align='center' nowrap>
								<a href=\"javascript:PoPWindow('/admin/order/orders.edit.php?view=popup&oid=".$ol['oid']."',1000,500,'order_edit');\" style='color:blue;'>
										".$ol['oid']."
								</a>
						</td>
						<td class='list_box_td' align='center' nowrap>".$ol['bname'].(!empty($memberInfo['id']) ? " / ".$memberInfo['id'] : " / 비회원")."</td>
						<td class='list_box_td' align='center' nowrap>".$ol['bank_input_name']."</td>
						<td class='list_box_td' align='center' nowrap>".number_format($ol['payment_price'])." 원</td>
						<td class='list_box_td' align='center' nowrap>".getOrderStatus($ol[status])."</td>
						<td class='list_box_td' align='center' nowrap>".$ol['date']."</td>";
						
						if($ol['match_result'] != 'duplicate'){
							$Contents .="
							<td class='list_box_td' align='center' nowrap><input type='button' value='업데이트' onclick=\"match_update('".$ol['oid']."','".$Bkid."')\"></td>";
						}else{
							if($success_cnt > 0){
								$Contents .="
								<td class='list_box_td' align='center' nowrap><input type='button' value='업데이트' onclick=\"alert('이미 매칭된 입금 내역입니다');\"></td>";
							}else{
								$Contents .="
								<td class='list_box_td' align='center' nowrap><input type='button' value='업데이트' onclick=\"match_update_duplicate('".$ol['oid']."','".$Bkid."')\"></td>";
							}
						}
				
				$Contents .="
					</tr>";
				endforeach;
				$Contents.="
					<tr height=40>
						<td colspan='7' align='center' >&nbsp;".page_bar($orderList['total'], $page, $max, $query_string, $Bkid,"")."&nbsp;</td>
					</tr>";
				
			}else{
				$Contents.="
					<tr height=40>
						<td colspan='7' align='center'>검색결과가 없습니다.</td>
					</tr>";
			}
			$Contents.="		
			</table>
		</td>
	</tr>
</table>
";

$Script .= "
<script language='javascript'>
$(function() {
	//입금만료일
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
		
	//매칭일
	$(\"#mStart_datepicker\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			if($('#mEnd_datepicker').val() != '' && $('#mEnd_datepicker').val() <= dateText){
				$('#mEnd_datepicker').val(dateText);
			}else{
				$('#mEnd_datepicker').datepicker('setDate','+0d');
			}
		}
	});

	$(\"#mEnd_datepicker\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'
	});
		
		
	$('#mDateUse').click(function(){
		if($(this).is(':checked')){
			$('#mStart_datepicker').attr('disabled',false);
			$('#mEnd_datepicker').attr('disabled',false);
		}else{
			$('#mStart_datepicker').attr('disabled',true);
			$('#mEnd_datepicker').attr('disabled',true);
		}
	});
});
function goManualUpdate(){
	
	eventDetail=window.open('','act','');
    eventDetail.location='/admin/order/bankda/bankda.act.php?act=update';
}
function match_update(oid,bkid){
	
	if(confirm(oid + ' 주문건에 대한 입금확인 처리를 진행 하시겠습니까?')){
		window.frames['iframe_act'].location.href= '/admin/order/bankda/bankda.act.php?act=update_one&oid='+oid+'&bkid='+bkid;
 	}
}

function match_update_duplicate(oid,bkid){
	
	if(confirm('동일한 금액, 동일한 입금자명의 입금내역이 존재합니다. 해당 입금내역으로 ' + oid + ' 주문건에 대한 입금확인 처리를 진행하시겠습니까?')){
		window.frames['iframe_act'].location.href= '/admin/order/bankda/bankda.act.php?act=update_one_duplicate&oid='+oid+'&bkid='+bkid;
 	}
}
</script>
";

$P = new ManagePopLayOut();
$P->addScript = "<script language='javascript' src='../../include/DateSelect.js'></script>\n<script language='javascript' src='bankda.js'></script>\n".$Script;
$P->OnloadFunction = "";
$P->Navigation = "자동입금확인 > 수동매칭 ";
$P->NaviTitle = "수동매칭";
$P->strContents = $Contents;
echo $P->PrintLayOut();