<?
include("../class/layout.class");

$db = new Database;
/*
$sql = "select
			ccd.com_name,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			cu.id,
			ccd.com_ceo,
			cmd.code
		from
			common_company_detail as ccd
			inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
			left join common_member_detail as cmd on (csd.charge_code = cmd.code)
			left join common_user as cu on (cmd.code = cu.code)
		where
			ccd.company_id = '".$company_id."'";
*/

$sql = "select
			spp.*
		from
			shop_product_point as spp 
			inner join shop_product as sp on (spp.pid = sp.id)
			inner join common_seller_detail as csd on (sp.admin = csd.company_id)
			left join common_member_detail as cmd on (csd.charge_code = cmd.code)
			left join common_user as cu on (cmd.code = cu.code)
			left join shop_order_detail as od on (spp.od_ix = od.od_ix)
		where
			spp.pid = '$pid' ";
$db->query($sql);
$db->fetch();
 
$pname = $db->dt[pname]; 


$Script = "
<script language='JavaScript' >
function ReserveReset(){
	var frm = document.forms['deposit_frm'];

	frm.reset();
	frm.act.value = 'deposit_insert';
}

function DeleteReserve(deposit_ix, uid){
	if(confirm('상품 레벨점수 정보를 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='member.act.php?act=reserve_delete&deposit_ix='+deposit_ix+'&uid='+uid;
	}
}

function UpdateReserve(deposit_ix, etc, deposit, state,use_state,use_type){

	$('input[name=deposit_ix]').val(deposit_ix);
	$('input[name=etc]').val(etc);
	$('input[name=deposit]').val(deposit);

	$('select[name=use_type]').val(use_type);
	//changeUseType(use_type);
	//chnageState(state);
	
	$('input[name=act]').val('deposit_update');

}

function checkSellerPoint(frm){

	if(frm.etc.value.length < 1){
		alert('적립내용을 입력해주세요');
		return false;
	}

	if(frm.deposit.value.length < 1){
		alert('마일리지를 입력해주세요');
		//frm.deposit.focus();
		return false;
	}

	return true;
}

function changeUseType(use_type){

	$('select[name=state]').empty();

	if(use_type == 'P'){
		$('select[name=state]').append('<option value=1>입금대기</option><option value=2>입금취소</option><option value=3>입금완료</option>');
	}else{
		$('select[name=state]').append('<option value=4>사용완료</option><option value=5>출금요청</option><option value=6>출금취소</option><option value=7>출금확정</option><option value=8>출금완료</option>');
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
		$('select[name=use_state]').append('<option value=9>기타</option>');
	}
}

$(document).ready(function(){
	
	var use_type = $('select[name=use_type] option:selected').val();
	//changeUseType(use_type);

	$('select[name=use_type]').change(function (){
		var use_type = $(this).val();
		changeUseType(use_type);
	});
	
	var state = $('select[name=state] option:selected').val();
//	chnageState(state);

	$('select[name=state]').change(function (){
		var state = $(this).val();
		//chnageState(state);
	});

});

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>

			<tr height=30><td class='p11 ls1' style='padding:0 0 0 0px;text-align:left;' > - 상품 레벨점수 내용을 수정/ 추가 하실수 있습니다.</td></tr>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
				<form name='deposit_frm' method='post'  action='product_point.act.php'  onSubmit='return checkSellerPoint(this)' target='act'>
				<input type='hidden' name='act' value='product_point_insert'>
				<input type='hidden' name='company_id' value='".$company_id."'>
				<input type='hidden' name='pid' value='".$pid."'>
				<table border='0' width='100%' cellspacing='1' cellpadding='0'  class='list_table_box' style='margin-bottom:10px;'>
					<!--col width=15%-->
					<col width=11%>
					<col width=13%>
					<col width=12%>
					<col width=*>
					<col width=10%>
					<col width=13%>
					<tr height='30' valign='middle' align=center >
						<!--td class='s_td'><b>셀러명</b></td-->
						<td class='m_td'>적립상태</td>
						<td class='m_td'>주문번호</td>
						<td class='m_td'>상품 레벨점수구분</td>
						<td class='m_td'>적립내용</td>
						<td class='m_td'>상품 레벨점수</td>
						<td class='e_td'>관리</td>
					</tr>
					<tr height='30' align='center'>
						<!--td class='list_box_td'>".$com_name."</td-->
						<td class='list_box_td'>
							<select name='state' class='p11 ls1' id='state' style='width:65px;'>
								<option value='1' ".CompareReturnValue("1",$db->dt[state],"selected").">적립(+)</option>
								<option value='2' ".CompareReturnValue("2",$db->dt[state],"selected").">차감(-)</option>
							</select>
						</td>
						<td class='list_box_td'>
							<input type='text' class=textbox name='oid' id='oid' value='".$db->dt[oid]."' style='width:80%;'>
						</td>
						<td class='list_box_td'>
							<select name='use_state' id='use_state' style='display:'>
								<option value='1' ".CompareReturnValue("1",$db->dt[use_state],"selected").">입금완료</option>
								<option value='2' ".CompareReturnValue("2",$db->dt[use_state],"selected").">배송완료</option>
								<option value='3' ".CompareReturnValue("3",$db->dt[use_state],"selected").">구매확정</option>
								<option value='4' ".CompareReturnValue("4",$db->dt[use_state],"selected").">입금후취소</option>
								<option value='5' ".CompareReturnValue("5",$db->dt[use_state],"selected").">교환확정</option>
								<option value='6' ".CompareReturnValue("6",$db->dt[use_state],"selected").">반품확정</option>
								<option value='7' ".CompareReturnValue("7",$db->dt[use_state],"selected").">배송지연</option>
								<option value='8' ".CompareReturnValue("8",$db->dt[use_state],"selected").">추가배송지연</option>
								<option value='9' ".CompareReturnValue("9",$db->dt[use_state],"selected").">기타</option>
							</select>
						</td>
						<td class='list_box_td' >
							<input type='text' class=textbox name='etc' id='etc' value='' style='width:80%;'>
						</td>
						<td class='list_box_td' >
							<input type='text' class=textbox name='point' id='point' size=10>
						</td>
						<td class='list_box_td' >
							<input type='image' src='../images/btn/ok.gif' align=absmiddle> <a href='javascript:ReserveReset();'><img src='../images/btn/cancel.gif' align=absmiddle border=0></a>
						</td>
					</tr>
				</table>
				</form>
				</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
					<table cellspacing=0 cellpadding=0 width=100% class='list_table_box'>
						<col width=16%>
						<col width=18%>
						<col width=8%>
						<col width=10%>
						<col width=8%>
						<col width=12%>
						<col width=*>
						<tr bgcolor=#efefef align=center height=34>
							<td class='s_td'>처리일자 </td>
							<td class='m_td'>주문번호</td>
							<td class='m_td'>처리상태 </td>
							<td class='m_td'>처리타입 </td>
							<td class='m_td small'><b>판매<br>신용점수</b></td>
							<td class='m_td small'><b>보유판매<br>신용점수</b></td>
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
	$db->query("select * from shop_product_point as spp where spp.pid = '$pid'  ");
}else{
	$db->query("select * from shop_product_point spp where spp.pid = '$pid' ");
}
$total = $db->total;

$sql = "select
			*
		from 
			shop_product_point
		where
			pid = '$pid' 
			order by regdate DESC, pp_ix DESC LIMIT $start, $max";
$db->query($sql);

$data_array = $db->fetchall();
if(count($data_array) > 0){
	for($i=0; $i< count($data_array); $i++){

		switch($data_array[$i][state]){
			case '1':
				$mstate = '적립(+)';
				$regdate = $data_array[$i][complete_date];
				break;
			case '2':
				$mstate = '차감(-)';
				$regdate = $data_array[$i][use_date];
				break;
		}

		switch($data_array[$i][use_state]){
			case '1':
				$use_state = '입금완료';
				break;
			case '2':
				$use_state = '배송완료';
				break;
			case '3':
				$use_state = '구매확정';
				break;
			case '4':
				$use_state = '입금후취소';
				break;
			case '5':
				$use_state = '교환확정';
				break;
			case '6':
				$use_state = '반품확정';
				break;
			case '7':
				$use_state = '발송지연';
				break;
			case '8':
				$use_state = '추가발송지연';
				break;
			case '9':
				$use_state = '기타';
				break;
		}
	
		if($data_array[$i][state] !='2'){	//적립상태,사용구분 선택후 수정가능 부분
			$add_display = '';
			$cancel_display = 'none';
			$font_color = '#0054FF';
		}else{
			$add_display = 'none';
			$cancel_display = '';
			$font_color = '#FF0000';
		}

	$Contents .= "	<tr height=28 align=center>
						<td bgcolor='#fbfbfb'>".$regdate."</td>
						<td bgcolor='#fbfbfb'>".$data_array[$i][oid]."</td>
						<td bgcolor='#fbfbfb' class='point'>".$mstate."</td>
						<td bgcolor='#fbfbfb' >".$use_state."</td>
						<td bgcolor='#fbfbfb'>";
						$Contents .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][pp_ix]."','".$data_array[$i][etc]."', '".$data_array[$i][point]."','".$data_array[$i][state]."','".$data_array[$i][use_state]."','".$data_array[$i][oid]."')\"><font color='".$font_color."'>".number_format($data_array[$i][point])."</font></a>
							";
						$Contents .= "</td>
										<td bgcolor='#fbfbfb' class='point'>";
						if($data_array[$i][state]	== RESERVE_STATUS_ORDER_CANCEL){
							$Contents .= "<s>".number_format($data_array[$i][total_point])."</s>";
						}else{
							$Contents .= number_format($data_array[$i][total_point]);
						}
	$Contents .= "		</td>
						<td style='padding:5px 0 5px 10px' align=left>".$data_array[$i][etc]."</a>
						</td>
						<!--<td><a href=\"javascript:DeleteReserve('".$data_array[$i][id]."', '".$data_array[$i][user_id]."')\"><img src='../image/btc_del.gif' border=0></a></td>-->
					</tr>";
	}

}else{
		$Contents .= "<tr height=60><td colspan=7 align=center>상품 레벨점수 내용이 없습니다.</td></tr>";

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
 
$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "셀러관리 > 상품 레벨점수 내역보기";
$P->NaviTitle = $pname." 상품 레벨점수 : <span style='color:#ffffff;font-size:20px;'></span> ";
$P->title = "상품 레벨점수 내역보기";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>

