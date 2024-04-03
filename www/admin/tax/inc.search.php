<?
$Contents .= "
<script>
$(document).ready(function(){
	$('#tax_tab1').click(function(){
		$('#tab1_view').slideDown();
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
});

function open_search(kind)
{
	if(kind == 'on')
	{
		$('#s_view2').attr('style','display:');
		$('#s_view1').attr('style','display:none');
		$('#detail_search').val('Y');
	}
	else
	{
		$('#s_view1').attr('style','display:');
		$('#s_view2').attr('style','display:none');
		$('#detail_search').val('');
	}
}

var st = 1;
function check_state()
{
	if(st%2 == 0)	$('input:checkbox[id=\'state[]\']').attr('checked',true);
	else			$('input:checkbox[id=\'state[]\']').removeAttr('checked');
	st++;
}

var mk = 1;
function check_mkind()
{
	if(mk%2 == 0)	$('input:checkbox[id=\'mkind[]\']').attr('checked',true);
	else			$('input:checkbox[id=\'mkind[]\']').removeAttr('checked');
	mk++;
}

var tk = 1;
function check_tkind()
{
	if(tk%2 == 0)	$('input:checkbox[id=\'tkind[]\']').attr('checked',true);
	else			$('input:checkbox[id=\'tkind[]\']').removeAttr('checked');
	tk++;
}

var ps = 1;
function check_pstat()
{
	if(ps%2 == 0)	$('input:checkbox[id=\'pstat[]\']').attr('checked',true);
	else			$('input:checkbox[id=\'pstat[]\']').removeAttr('checked');
	ps++;
}

var cp = 1;
function check_pkind()
{
	if(cp%2 == 0)	$('input:checkbox[id=\'pkind[]\']').attr('checked',true);
	else			$('input:checkbox[id=\'pkind[]\']').removeAttr('checked');
	cp++;
}

var tp = 1;
function check_tper()
{
	if(tp%2 == 0)	$('input:checkbox[id=\'tax_per[]\']').attr('checked',true);
	else			$('input:checkbox[id=\'tax_per[]\']').removeAttr('checked');
	tp++;
}

var ck = 1;
function check_ckind()
{
	if(ck%2 == 0)	$('input:checkbox[id=\'ckind[]\']').attr('checked',true);
	else			$('input:checkbox[id=\'ckind[]\']').removeAttr('checked');
	ck++;
}

function linecheck(obj){
	if(obj.is(':checked')){
		obj.parent().next().find('input[type=checkbox]').prop('checked',true);
	}else{
		obj.parent().next().find('input[type=checkbox]').prop('checked',false)
	}
}
</script>
<form name='search_frm'  method='post'>
<input type='hidden' name='detail_search' id='detail_search' value=''>
<input type='hidden' name='publish_type' id='publish_type' value='$publish_type'>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
				<col width=18%>
				<col width=32%>
				<col width=18%>
				<col width=32%>
				<tr height=33>
					<th class='search_box_title' colspan='1'>
					<select name='date_type'>
						<option value='re_signdate' ".CompareReturnValue('re_signdate',$date_type,' selected').">발행일</option>
						<option value='signdate' ".CompareReturnValue('signdate',$date_type,' selected').">작성일</option>
					</select>
					<input type='checkbox' name='date_search' id='visitdate' value='1' ".CompareReturnValue('1',$date_search,' checked')."></th>
					<td class='search_box_item'  colspan=3>
						".search_date('startDate','endDate',$startDate,$endDate,'','tax')."
					</td>
				</tr>
				<tr height='33'>
					<th class='search_box_title'>문서형태<input type='checkbox' onclick=\"linecheck($(this));\" ></th>
					<td class='search_box_item'>
						<input type='checkbox' name='document_type[]' id='document_type1' value='1' ".CompareReturnValue('1',$document_type,' checked')."><label for='document_type1'>정상발행</label>
						<input type='checkbox' name='document_type[]' id='document_type2' value='2' ".CompareReturnValue('2',$document_type,' checked')."><label for='document_type2'>수정발행</label>
					</td>
					<th class='search_box_title'>문서구분<input type='checkbox' onclick=\"linecheck($(this));\" ></th>
					<td class='search_box_item'>
						<input type='checkbox' name='tax_type[]' id='tax_type1' value='1' ".CompareReturnValue('1',$tax_type,' checked')."><label for='tax_type1'>전자세금계산서</label>
						<input type='checkbox' name='tax_type[]' id='tax_type2' value='2' ".CompareReturnValue('2',$tax_type,' checked')."><label for='tax_type2'>계산서(면세)</label>
					</td>
				</tr>
				
				<tr height='33'>
					<th class='search_box_title'>과세형태<input type='checkbox' onclick=\"linecheck($(this));\" ></th>
					<td class='search_box_item'>
						<input type='checkbox' name='tax_per[]' id='tax_per1' value='1' ".CompareReturnValue('1',$tax_per,' checked')."><label for='tax_per1'>과세</label>
						<input type='checkbox' name='tax_per[]' id='tax_per2' value='2' ".CompareReturnValue('2',$tax_per,' checked')."><label for='tax_per2'>영세</label>
						<input type='checkbox' name='tax_per[]' id='tax_per3' value='3' ".CompareReturnValue('3',$tax_per,' checked')."><label for='tax_per3'>면세(세액없음)</label>
					</td>
					
					<th class='search_box_title'>지연발행<input type='checkbox' onclick=\"linecheck($(this));\" ></th>
					<td class='search_box_item'>
						<input type='checkbox' name='delay[]' id='delay1' value='1' ".CompareReturnValue('1',$delay,' checked')."><label for='delay1'>정상발행</label>
						<input type='checkbox' name='delay[]' id='delay2' value='2' ".CompareReturnValue('2',$delay,' checked')."><label for='delay2'>지연발행</label>
					</td>
				</tr>
				<tr height='33'>
					<th class='search_box_title'>영수/청구<input type='checkbox' onclick=\"linecheck($(this));\" ></th>
					<td class='search_box_item'>
						<input type='checkbox' name='claim_kind[]' id='claim_kind1' value='1' ".CompareReturnValue('1',$claim_kind,' checked')."><label for='claim_kind1'>영수</label>
						<input type='checkbox' name='claim_kind[]' id='claim_kind2' value='2' ".CompareReturnValue('2',$claim_kind,' checked')."><label for='claim_kind2'>청구</label>
					</td>
					
					<th class='search_box_title'>발행상태<input type='checkbox' onclick=\"linecheck($(this));\" ></th>
					<td class='search_box_item' >
						<input type='checkbox' name='status[]' id='status1' value='1' ".CompareReturnValue('1',$status,' checked')."><label for='status1'>발행완료</label>
						<input type='checkbox' name='status[]' id='status2' value='0' ".CompareReturnValue('0',$status,' checked')."><label for='status2'>임시발행</label>
						<input type='checkbox' name='status[]' id='status3' value='3' ".CompareReturnValue('3',$status,' checked')."><label for='status3'>발행취소</label>
					</td>
				</tr>
				
				<tr height='33'>
					<th class='search_box_title'>전송상태<input type='checkbox' onclick=\"linecheck($(this));\" ></th>
					<td class='search_box_item' colspan=''>
						
						<input type='checkbox' name='send_status[]' id='send_status1' value='3' ".CompareReturnValue('3',$send_status,' checked')."><label for='send_status1'>전송중</label>
						<input type='checkbox' name='send_status[]' id='send_status2' value='4' ".CompareReturnValue('4',$send_status,' checked')."><label for='send_status2'>전송성공</label>
						<input type='checkbox' name='send_status[]' id='send_status3' value='5' ".CompareReturnValue('5',$send_status,' checked')."><label for='send_status3'>전송실패</label>
					</td>
					<th class='search_box_title'>발행유형<input type='checkbox' onclick=\"linecheck($(this));\" ></th>
					<td class='search_box_item' colspan=''>
						
						<input type='checkbox' name='publish_type[]' id='publish_type1' value='1' ".CompareReturnValue('1',$publish_type,' checked')."><label for='publish_type1'>정발행</label>
						<input type='checkbox' name='publish_type[]' id='publish_type2' value='2' ".CompareReturnValue('2',$publish_type,' checked')."><label for='publish_type2'>역발행</label>
						<input type='checkbox' name='publish_type[]' id='publish_type3' value='3' ".CompareReturnValue('3',$publish_type,' checked')."><label for='publish_type3'>위수탁</label>
					</td>
				</tr>
				<tr height=33>
					<th class='search_box_title' colspan='1'>
					<select name='search_type'>
						<option value='' ".CompareReturnValue('',$search_type,' selected').">조건검색</option>
						<option value='r_company_number' ".CompareReturnValue('r_company_number',$search_type,' selected').">거래처 + 사업자번호</option>
						<option value='r_personin' ".CompareReturnValue('r_personin',$search_type,' selected').">담당자 이름</option>
						<option value='r_email' ".CompareReturnValue('r_email',$search_type,' selected').">이메일(공급받는자)</option>
						<option value='r_tel' ".CompareReturnValue('r_tel',$search_type,' selected').">담당자 연락처</option>
					</select>
					<td class='search_box_item'  colspan=3>
						<input type='text' name='search_text' style='width:95%;' value='".$search_text."'>
					</td>
				</tr>
				
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr>
</table>
</form>";
if(false){
$Contents .= "
				<table width='100%' cellpadding='0' cellspacing='0' border='0' class='input_table_box'>
				  <tr>
					<td class='input_box_title' width='100'>검색하기</td>
					<td class='input_box_item'>
					  <input type='radio' name='s_type' id='s_type' value='total' $checked1> 전체 &nbsp;&nbsp;
					  <input type='radio' name='s_type' id='s_type' value='r_company_number' $checked2> 사업자번호(주민번호) &nbsp;&nbsp;
					  <input type='radio' name='s_type' id='s_type' value='r_company_name' $checked3> 회사명 &nbsp;&nbsp;
					  <input type='text' class='textbox' name='sch_txt' id='sch_txt' size='30'>
					  <input type='image' src='/admin/image/search01.gif' value='검색' id='frm_btn' align='absbottom'>
					</td>
				  </tr>
				</table>
				<span id='s_view1'>
				<table cellpadding='0' cellspacing='1' border='0'>
					<tr height='20'>
						<td style='cursor:hand' onclick='open_search(\"on\")' style='margin:5px 5px 5px 5px'><img src='./img/d_search.gif'></td>
					</tr>
				</table>
				</span>
				";


				$today_time = date("Y-m-d");
				$week		= date("Y-m-d", time() + 86400 * 7);
				$day15		= date("Y-m-d", time() + 86400 * 15);
				$day30		= date("Y-m-d", time() + 86400 * 30);
				$day60		= date("Y-m-d", time() + 86400 * 60);
				$day90		= date("Y-m-d", time() + 86400 * 90);

				$Contents .= "
				<span id='s_view2' style='display:none'>
				<table width='100%' cellpadding='0' cellspacing='1' border='0' bgcolor='#CCCCCC' style='margin:1px 0 0 0' class='search_table_box'>
				  <tr height='25'>
					<td bgcolor='#F2F2F2' width='100' class='search_box_title'>기간<input type='checkbox' name='' id='' checked></td>
					<td bgcolor='#FFFFFF' valign='middle' class='search_box_item'>
						<!--input type=\"text\" name=\"dateS\" id='dateS' size=\"10\" value=\"\" style='border:1px solid #CCCCCC;width: 100px; height: 18px;background-image:url(/admin/tax/img/dtp_icon.gif);background-repeat:no-repeat;background-position:right; cursor:hand;' onclick=\"Calendar(this);\"> ~ <input type=\"text\" name=\"dateE\" id='dateE' size=\"10\" value=\"\" style=\"border:1px solid #CCCCCC;width: 100px; height: 18px;background-image:url(/admin/tax/img/dtp_icon.gif);background-repeat:no-repeat;background-position:right; cursor:hand;\" onclick=\"Calendar(this);\"-->
						".search_date('dateS','dateE',$dateS,$dateE)."
						<input type='button' value='1기' onclick='period_input(\"".date('Y')."-06-30\",\"".date('Y')."-01-01\",1);'>
						<input type='button' value='2기' onclick='period_input(\"".date('Y')."-12-31\",\"".date('Y')."-07-01\",1);'>
						<input type='button' value='1분기' onclick='period_input(\"".date('Y')."-03-31\",\"".date('Y')."-01-01\",1);'>
						<input type='button' value='2분기' onclick='period_input(\"".date('Y')."-06-30\",\"".date('Y')."-04-01\",1);'>
						<input type='button' value='3분기' onclick='period_input(\"".date('Y')."-09-30\",\"".date('Y')."-07-01\",1);'>
						<input type='button' value='4분기' onclick='period_input(\"".date('Y')."-12-31\",\"".date('Y')."-10-01\",1);'>
						<!--<a href=\"javascript:period_input('$week','$today_time',1);\"><img src='../image/b_btn_s_1week01.gif' align='absmiddle'></a>
						<a href=\"javascript:period_input('$day15','$today_time',1);\"><img src='../image/b_btn_s_15day01.gif' align='absmiddle'></a>
						<a href=\"javascript:period_input('$day30','$today_time',1);\"><img src='../image/b_btn_s_1month01.gif' align='absmiddle'></a>
						<a href=\"javascript:period_input('$day60','$today_time',1);\"><img src='../image/b_btn_s_2month01.gif' align='absmiddle'></a>
						<a href=\"javascript:period_input('$day90','$today_time',1);\"><img src='../image/b_btn_s_3month01.gif' align='absmiddle'></a>-->
					</td>
				  </tr>
				";

				if($PHP_SELF != "/admin/tax/sales_list2.php")
				{
				$Contents .= "
				  <tr height='25'>
					<td bgcolor='#F2F2F2' width='100' class='search_box_title'>상태<input type='checkbox' name='state_T' id='state_T' onclick='check_state()' checked></td>
					<td bgcolor='#FFFFFF' class='search_box_item'>
						<table>
							<tr>
								<td width='100'><input type='checkbox' name='state[]' id='state[]' value='1' checked>발행</td>
								<td width='100'><input type='checkbox' name='state[]' id='state[]' value='2' checked>임시발행</td>
								<td width='100'><input type='checkbox' name='state[]' id='state[]' value='3' checked>취소요청</td>
								<td width='100'><input type='checkbox' name='state[]' id='state[]' value='4' checked>승인요청</td>
								<td width='100'><input type='checkbox' name='state[]' id='state[]' value='5' checked>승인거부</td>
								<td width='100'><input type='checkbox' name='state[]' id='state[]' value='6' checked>승인취소</td>
							</tr>
						</table>
					</td>
				  </tr>
				";
				}

				if($PHP_SELF != "/admin/tax/sales_list.php")
				{
				$Contents .= "
				  <tr height='25'>
					<td bgcolor='#F2F2F2' width='100' class='search_box_title'>구분<input type='checkbox' name='mkind_T' id='mkind_T' onclick='check_mkind()' checked></td>
					<td bgcolor='#FFFFFF' class='search_box_item'>
						<table>
							<tr>
								<td width='100'><input type='checkbox' name='mkind[]' id='mkind[]' value='1' checked>매출</td>
								<td width='100'><input type='checkbox' name='mkind[]' id='mkind[]' value='2' checked>매입</td>
								<td width='100'><input type='checkbox' name='mkind[]' id='mkind[]' value='3' checked>수탁</td>
							</tr>
						</table>
					</td>
				  </tr>
				";
				};
				$Contents .= "
				  <tr height='25'>
					<td bgcolor='#F2F2F2' width='100' class='search_box_title'>문서종류<input type='checkbox' name='tkind_T' id='tkind_T' onclick='check_tkind()' checked></td>
					<td bgcolor='#FFFFFF' class='search_box_item'>
						<table>
							<tr>
								<td width='100'><input type='checkbox' name='tkind[]' id='tkind[]' value='1' checked>세금계산서</td>
								<td width='100'><input type='checkbox' name='tkind[]' id='tkind[]' value='2' checked>계산서</td>
								<!--<td width='100'><input type='checkbox' name='tkind[]' id='tkind[]' checked>거래명세서</td>
								<td width='100'><input type='checkbox' name='tkind[]' id='tkind[]' checked>입금표</td>-->
							</tr>
						</table>
					</td>
				  </tr>
				  <tr height='25'>
					<td bgcolor='#F2F2F2' width='100' class='search_box_title'>문서형태<input type='checkbox' name='pstat_T' id='pstat_T' onclick='check_pstat()' checked></td>
					<td bgcolor='#FFFFFF' class='search_box_item'>
						<table>
							<tr>
								<td width='100'><input type='checkbox' name='pstat[]' id='pstat[]' value='1' checked>일반</td>
								<td width='100'><input type='checkbox' name='pstat[]' id='pstat[]' value='2' checked>수정</td>
							</tr>
						</table>
					</td>
				  </tr>
				  <tr height='25'>
					<td bgcolor='#F2F2F2' width='100' class='search_box_title'>발행형태<input type='checkbox' name='pkind_T' id='pkind_T' onclick='check_pkind()' checked></td>
					<td bgcolor='#FFFFFF' class='search_box_item'>
						<table>
							<tr>
								<td width='100'><input type='checkbox' name='pkind[]' id='pkind[]' value='1' checked>정발행</td>
								<td width='100'><input type='checkbox' name='pkind[]' id='pkind[]' value='2' checked>역발행</td>
								<td width='100'><input type='checkbox' name='pkind[]' id='pkind[]' value='3' checked>위수탁</td>
							</tr>
						</table>
					</td>
				  </tr>
				  <tr height='25'>
					<td bgcolor='#F2F2F2' width='100' class='search_box_title'>과세형태<input type='checkbox' name='tax_per_T' id='tax_per_T' onclick='check_tper()' checked></td>
					<td bgcolor='#FFFFFF' class='search_box_item'>
						<table>
							<tr>
								<td width='100'><input type='checkbox' name='tax_per[]' id='tax_per[]' value='1' checked>과세</td>
								<td width='100'><input type='checkbox' name='tax_per[]' id='tax_per[]' value='2' checked>영세</td>
								<td width='100'><input type='checkbox' name='tax_per[]' id='tax_per[]' value='3' checked>면세</td>
							</tr>
						</table>
					</td>
				  </tr>
				  <tr height='25'>
					<td bgcolor='#F2F2F2' width='100' class='search_box_title'>영수/청구<input type='checkbox' name='ckind_T' id='ckind_T' onclick='check_ckind()' checked></td>
					<td bgcolor='#FFFFFF' class='search_box_item'>
						<table>
							<tr>
								<td width='100'><input type='checkbox' name='ckind[]' id='ckind[]' value='2' checked>영수</td>
								<td width='100'><input type='checkbox' name='ckind[]' id='ckind[]' value='1' checked>청구</td>
							</tr>
						</table>
					</td>
				  </tr>
				  <tr height='25'>
					<td bgcolor='#F2F2F2' width='100' class='search_box_title'>일련번호</td>
					<td bgcolor='#FFFFFF' class='search_box_item'>
						<input type='text' name='numbering' id='numbering' size='20'>
					</td>
				  </tr>
				</table>
				<table cellpadding='0' cellspacing='1' border='0' bgcolor='#CCCCCC' style='margin:1px 0 0 0'>
					<tr height='20' bgcolor='#FFFFFF'>
						<td style='cursor:hand' onclick='open_search(\"off\")' style='margin:5px 5px 5px 5px'> &nbsp;상세검색 <img src='/admin/image/orderby_desc_on.gif'>&nbsp; </td>
					</tr>
				</table>
				</span>

				</form>
				
	";
	}