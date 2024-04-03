<?
include("../class/layout.class");


$db = new Database;

if($db->dbms_type == "oracle"){
	$db->query("select AES_DECRYPT(IFNULL(cmd.name,'-'),'".$db->ase_encrypt_key."') as name from ".TBL_COMMON_MEMBER_DETAIL." cmd where code = '$code' ");
}else{
	$db->query("select AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$db->ase_encrypt_key."') as name from ".TBL_COMMON_MEMBER_DETAIL." cmd where code = '$code' ");
}

if($db->total){
	$db->fetch();
	$user_name = $db->dt[name];
}else{
	$user_name = "미지정";
}

$Script = "
<script language='JavaScript' >
function ReserveReset(){
	var frm = document.forms['deposit_frm'];

	frm.reset();
	frm.act.value = 'deposit_insert';
}

function DeleteReserve(deposit_ix, uid){
	if(confirm('예치금 정보를 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='member.act.php?act=reserve_delete&deposit_ix='+deposit_ix+'&uid='+uid;
	}
}

function UpdateReserve(deposit_ix, etc, deposit, state,use_state,use_type){

	$('input[name=deposit_ix]').val(deposit_ix);
	$('input[name=etc]').val(etc);
	$('input[name=deposit]').val(deposit);

	$('select[name=use_type]').val(use_type);
	changeUseType(use_type);
	chnageState(state);
	
	$('input[name=act]').val('deposit_update');

}

function CheckReserve(frm){

	if(frm.etc.value.length < 1){
		alert('입출금 상세내역을 입력해주세요');
		return false;
	}

	if(frm.deposit.value == '0' || frm.deposit.value.length < '1'){
		alert('예치금금액을 입력해주세요');
		return false;
	}

	return true;
}

function changeUseType(use_type){

	$('select[name=state]').empty();

	if(use_type == 'P'){
		$('select[name=state]').append('<option value=1>입금대기</option><option value=3>입금완료</option>');
	}else{
		$('select[name=state]').append('<option value=10>사용대기</option><option value=4>사용완료</option><option value=5>출금요청</option><option value=7>출금확정</option>');
	}
}

function chnageState(state){
	
	$('select[name=use_state]').empty();

	if(state == '2'){
		$('select[name=use_state]').append('<option value=1>지연취소</option><option value=9>기타</option>');
	}else if(state == '3'){
		$('select[name=use_state]').append('<option value=2>고객입금</option><option value=3>주문취소</option><option value=4>주문교환</option><option value=5>주문반품입금</option><option value=6>마케팅</option><option value=9>기타</option>');
	}else if(state == '4'){
		$('select[name=use_state]').append('<option value=7>상품구매</option><option value=9>기타</option>');
	}else if(state == '5'){
		$('select[name=use_state]').append('<option value=2>고객요청</option><option value=9>기타</option>');
	}else{
		$('select[name=use_state]').append('<option value=2>고객요청</option><option value=9>기타</option>');
	}
}

$(document).ready(function(){
	
	var use_type = $('select[name=use_type] option:selected').val();
	changeUseType(use_type);

	$('select[name=use_type]').change(function (){
		var use_type = $(this).val();
		changeUseType(use_type);
	});
	
	var state = $('select[name=state] option:selected').val();
	chnageState(state);

	$('select[name=state]').change(function (){
		var state = $(this).val();
		chnageState(state);
	});

});

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>

			<tr height=30><td class='p11 ls1' style='padding:0 0 0 20px;text-align:left;' > - 적립금 내용을 수정/ 추가 하실수 있습니다.</td></tr>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
				<form name='deposit_frm' method='post'  action='deposit.act.php'  onSubmit='return CheckReserve(this)' target='act'>
				<input type='hidden' name='act' value='deposit_insert'>
				<input type='hidden' name='uid' value='".$code."'>
				<input type='hidden' name='deposit_ix' value=''>

						<table border='0' width='100%' cellspacing='1' cellpadding='0'  class='list_table_box' style='margin-bottom:10px;'>
							<col width=10%>
							<col width=11%>
							<col width=13%>
							<col width=15%>
							<col width=*>
							<col width=13%>
							<col width=13%>
							<tr height='20' valign='middle' align=center >
								<td class='s_td'><b>회원</b></td>
								<td class='m_td'>입출금 구분</td>
								<td class='m_td'>처리상태</td>
								<td class='m_td'>타입</td>
								<td class='m_td'>입출금 상세내역</td>
								<td class='m_td'>예치금</td>
								<td class='e_td'>관리</td>
							</tr>
							<tr height='30' align='center'>
								<td class='list_box_td'>".$user_name."</td>
								<td class='list_box_td'>
									<select name='use_type' id='state' style='width:'>
										<option value='P' ".CompareReturnValue("P",$db->dt[use_type],"selected").">입금</option>
										<option value='W' ".CompareReturnValue("W",$db->dt[use_type],"selected").">출금</option>
									</select>
								</td>
								<td class='list_box_td'>
									<select name='state' class='p11 ls1' id='state'>
									</select>
								</td>
								<td class='list_box_td' >
									<select name='use_state' id='use_state' style='display:'>
									</select>
								</td>
								<td class='list_box_td' >
									<input type='text' class=textbox name='etc' value='' style='width:80%;'>
								</td>
								<td class='list_box_td' >
									<input type='text' class='textbox numeric' name='deposit' style='width:60px;'>
								</td>
								<td class='list_box_td' >
									<input type='image' src='../images/btn/ok.gif' align=absmiddle style='cursor:pointer;'>
									<a href='javascript:ReserveReset();'><img src='../images/btn/cancel.gif' align=absmiddle border=0 style='cursor:pointer;'></a>
								</td>
							</tr>
						</table>

				</form>
				</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
					<table cellspacing=0 cellpadding=0 width=100% class='list_table_box'>
						<col width=15%>
						<col width=12%>
						<col width=10%>
						<col width=10%>
						<col width=13%>
						<col width=10%>
						<col width=*>
						<tr bgcolor=#efefef align=center height=28>
							<td class='s_td'>처리일자 </td>
							<td class='m_td'>입/출금 구분 </td>
							<td class='m_td'>처리상태 </td>
							<td class='m_td'>처리타입 </td>
							<td class='m_td'>입출금 금액 </td>
							<td class='m_td'>보유금액</td>
							<td class='e_td'>입출금 상세내역 </td>
						</tr>";

$max = 10; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

if($db->dbms_type == "oracle"){
	$db->query("select * from shop_deposit where uid = '".$code."' ");
}else{
	$db->query("select * from shop_deposit where uid = '".$code."'");
} 
$total = $db->total;

$sql = "select
			*
		from 
			shop_deposit
		where
			uid = '".$code."' 
			order by edit_date DESC LIMIT $start, $max";
$db->query($sql);

$data_array = $db->fetchall();
if(count($data_array) > 0){
	for($i=0; $i< count($data_array); $i++){
		//echo $data_array[$i][waiting_date];
		switch($data_array[$i][use_type]){
			case 'P':
				$use_type = '입금';
				break;
			case 'W':
				$use_type = '출금';
				break;
		}

		switch($data_array[$i][state]){
			case '1':
				$mstate = '입금대기';
				$regdate = $data_array[$i][waiting_date];
				break;
			case '2':
				$mstate = '입금취소';
				$regdate = $data_array[$i][cancel_date];
				break;
			case '3':
				$mstate = '입금완료';
				$regdate = $data_array[$i][complete_date];
				break;
			case '4':
				$mstate = '사용완료';
				$regdate = $data_array[$i][use_date];
				break;
			case '5':
				$mstate = '출금요청';
				$regdate = $data_array[$i][w_request_date];
				break;
			case '6':
				$mstate = '출금취소';
				$regdate = $data_array[$i][w_cancel_date];
				break;
			case '7':
				$mstate = '출금확정';
				$regdate = $data_array[$i][w_fixed_date];
				break;
			case '8':
				$mstate = '송금완료';
				$regdate = $data_array[$i][w_complate_date];
				break;
			case '10':
				$mstate = '사용대기';
				$regdate = $data_array[$i][w_use_date];
				break;
			case '11':
				$mstate = '사용대기취소';
				$regdate = $data_array[$i][c_use_date];
				break;
		}

		switch($data_array[$i][use_state]){
			case '1':
				$use_state = '지연취소';
				break;
			case '2':
				$use_state = '고객입금';
				break;
			case '3':
				$use_state = '주문취소';
				break;
			case '4':
				$use_state = '주문교환';
				break;
			case '5':
				$use_state = '주문반품입금';
				break;
			case '6':
				$use_state = '마케팅';
				break;
			case '7':
				$use_state = '상품구매';
				break;
			case '8':
				$use_state = '고객요청';
				break;
			case '9':
				$use_state = '기타';
				break;
		}
	
		if($data_array[$i][state] =='4' || $data_array[$i][state] =='7' || $data_array[$i][state] =='8'){
			$font_color = '#FF0000';
		}else if($data_array[$i][state] =='3'){
			$font_color = '#0054FF';
		}else{
			$font_color = '#000000';
		}

	$Contents .= "	<tr height=28 align=center>
						<td bgcolor='#fbfbfb'>".$regdate."</td>
						<td bgcolor='#fbfbfb'>".$use_type."</td>
						<td bgcolor='#fbfbfb'>".$mstate."</td>
						<td bgcolor='#fbfbfb'>".$use_state."</td>
						<td bgcolor='#fbfbfb'>";
						if($data_array[$i][state]	== '2' || $data_array[$i][state]	== '11' || $data_array[$i][state]	== '6'){
							$Contents .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."','".$data_array[$i][etc]."', '".$data_array[$i][deposit]."','".$data_array[$i][state]."','".$data_array[$i][use_state]."','".$data_array[$i][use_type]."')\"><s><font color='".$font_color."'>".number_format($data_array[$i][deposit])."</font></s></a>
							";
						}else{
							$Contents .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."','".$data_array[$i][etc]."', '".$data_array[$i][deposit]."','".$data_array[$i][state]."','".$data_array[$i][use_state]."','".$data_array[$i][use_type]."')\"><font color='".$font_color."'>".number_format($data_array[$i][deposit])."</font></a>
							";
							//$Contents .="<font color='".$font_color."'>". number_format($data_array[$i][deposit])."</font>";
						}
	$Contents .= "		</td>
						<td bgcolor='#fbfbfb'>".number_format($data_array[$i][use_deposit])."</td>
						<td style='padding:5 0 5 15' align=left>
							<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."','".$data_array[$i][etc]."', '".$data_array[$i][deposit]."','".$data_array[$i][state]."','".$data_array[$i][use_state]."','".$data_array[$i][use_type]."')\">".$data_array[$i][etc]."</a>
						</td>
						<!--<td><a href=\"javascript:DeleteReserve('".$data_array[$i][id]."', '".$data_array[$i][user_id]."')\"><img src='../image/btc_del.gif' border=0></a></td>-->
					</tr>";
	}

}else{
		$Contents .= "<tr height=60><td colspan=7 align=center>예치금 내용이 없습니다.</td></tr>";

}
$Contents .= "
			<tr height=40><td colspan=7 align=center>".page_bar($total, $page, $max,"&code=$code","")."</td></tr>
			</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</TABLE>";

$sql = " select 
		sum(if(r.state = '2',r.reserve,0)) as use_reserve,
		sum(if(r.state = '1',r.reserve,0)) as total_reserve
		from
			shop_reserve as r 
			inner join shop_reserve_info as ri on (r.reserve_id = ri.reserve_id )
		where
			1
			and r.auto_cancel = 'N'
			and r.state in ('2','1')
			and r.uid = '$code'
";
$db->query($sql);

$db->fetch();
$use_reserve = $db->dt[use_reserve];
$total_reserve = $db->dt[total_reserve];

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "회원관리 > 예치금 내역보기";
$P->NaviTitle = "예치금 보유금액 : <span style='color:#ffffff;font-size:20px;'>".number_format($total_reserve - $use_reserve)."</span> 원";
$P->title = "예치금 내역보기";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>

