<?
include($_SERVER[DOCUMENT_ROOT]."/admin/member/mileage.pop.php");
exit;
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
	var frm = document.forms['reserve'];

	frm.reset();
	frm.act.value = 'reserve_insert';
}

function DeleteReserve(id, uid){
	if(confirm('적립금 정보를 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='member.act.php?act=reserve_delete&id='+id+'&uid='+uid;
	}
}

function changeselectBox(state,use_state){
	
	//alert(use_state);
	if(state == '2'){
		$('#reserve_cancel').css('display','');
		$('#reserve_add').css('display','none');

		$('#reserve_add').attr('disabled',true);
		$('#reserve_cancel').attr('disabled',false);
	}else{
		$('#reserve_cancel').css('display','none');
		$('#reserve_add').css('display','');

		$('#reserve_cancel').attr('disabled',true);
		$('#reserve_add').attr('disabled',false);
		
	}

}


function UpdateReserve(id, etc, reserve, state,use_state){
	var frm = document.forms['reserve'];
	

	frm.id.value = id;
	frm.etc.value = etc;
	frm.reserve.value = reserve;

	changeselectBox(state,use_state);

	
	for(i=0;i<frm.state.length;i++){
		if(frm.state[i].value == state){
			frm.state[i].selected = true;
		}
	}
	if(state == '2'){
		for(j=0;j<frm.use_state_cancel.length;j++){
			if(frm.use_state_cancel[j].value == use_state){
				frm.use_state_cancel[j].selected = true;
			}
		}
	}else{
		for(k=0;k<frm.use_state_add.length;k++){
			if(frm.use_state_add[k].value == use_state){
				frm.use_state_add[k].selected = true;
			}
		}
	}

	frm.act.value = 'reserve_update';
}

function CheckReserve(frm){
	if(frm.etc.value.length < 1){
		alert('적립내용을 입력해주세요');
		//frm.etc.focus();
		return false;
	}

	if(frm.reserve.value.length < 1){
		alert('마일리지를 입력해주세요');
		//frm.reserve.focus();
		return false;
	}

	return true;
}

$(document).ready(function(){
	$('#state').change(function(){
		var state_val = $(this).val();
		changeselectBox(state_val);
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
				<form name='reserve' method='post'  action='member.act.php'  onSubmit='return CheckReserve(this)'>
				<input type='hidden' name='act' value='reserve_insert'>
				<input type='hidden' name='reserve_id' value='".$reserve_id."'>
				<input type='hidden' name='uid' value='".$code."'>
				<input type='hidden' name='id' value=''>

						<table border='0' width='100%' cellspacing='1' cellpadding='0' class='search_table_box' style='margin-bottom:10px;'>
							<tr height='20' valign='middle' align=center >
								<td align='center' class='search_box_title' style='text-align:center;padding:0px;'><b>회원</b></td>
								<td class='search_box_title' style='text-align:center;padding:0px;'>적립상태</td>
								<td class='search_box_title' style='text-align:center;padding:0px;'>사용구분</td>
								<td class='search_box_title' style='text-align:center;padding:0px;'>적립내용</td>
								<td class='search_box_title' style='text-align:center;padding:0px;'>마일리지</td>
								<td class='search_box_title' style='text-align:center;padding:0px;'>관리</td>
							</tr>
							<tr height='30' align='center'>
								<td >".$user_name."</td>
								<td>
									<select name='state' class='p11 ls1' id='state'>
										<!--option value='0' ".CompareReturnValue("0",$db->dt[state],"selected").">대기</option-->
										<!--option value='9' ".CompareReturnValue("9",$db->dt[state],"selected").">취소</option-->
										<option value='1' ".CompareReturnValue("1",$db->dt[state],"selected").">완료(+)</option>
										<option value='2' ".CompareReturnValue("2",$db->dt[state],"selected").">사용(-)</option>
									</select>
								</td>
								<td class='list_box_td' >
									<select name='use_state_add' id='reserve_add' style='display:'>
										<!--option value='1' ".CompareReturnValue("1",$db->dt[use_state],"selected").">상품구매</option>
										<option value='2' ".CompareReturnValue("2",$db->dt[use_state],"selected").">주문취소</option>
										<option value='3' ".CompareReturnValue("3",$db->dt[use_state],"selected").">주문반품</option>
										<option value='4' ".CompareReturnValue("4",$db->dt[use_state],"selected").">마케팅</option-->
										<option value='5' ".CompareReturnValue("5",$db->dt[use_state],"selected").">기타</option>
									</select>
									<select name='use_state_cancel' id='reserve_cancel' style='display:none'>
										<!--option value='20' ".CompareReturnValue("20",$db->dt[use_state],"selected").">상품구매사용</option>
										<option value='21' ".CompareReturnValue("21",$db->dt[use_state],"selected").">적립소멸</option>
										<option value='23' ".CompareReturnValue("23",$db->dt[use_state],"selected").">주문취소</option>
										<option value='24' ".CompareReturnValue("24",$db->dt[use_state],"selected").">주문반품</option-->
										<option value='22' ".CompareReturnValue("22",$db->dt[use_state],"selected").">기타</option>
									</select>
								</td>
								<td align='center'  ><input type='text' class=textbox name='etc' value='' size=35></td>
								<td><input type='text' class=textbox name='reserve' size=10></td>
								
								<td>
									<input type='image' src='../images/btn/ok.gif' align=absmiddle> 
									<a href='javascript:ReserveReset();'><img src='../images/btn/cancel.gif' align=absmiddle border=0></a>
								</td>
							</tr>
						</table>

				</form>
				</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
					<table cellspacing=0 cellpadding=0 width=100% class='list_table_box'>
						<tr bgcolor=#efefef align=center height=28>
							<td class='s_td' width=15%>날짜 </td>
							<td class='m_td' width=20%>적립내용 </td>
							<td class='m_td' width=10% >마일리지 </td>
							<td class='m_td' width=15% >사용 마일리지 </td>
							<td class='m_td' width=10% >상태 </td>
							<td class='m_td' width=10% >사용구분</td>
							<td class='e_td' width=15% >주문번호 </td>
							<!--<td class='e_td' width=15% >관리 </td>-->
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

if($reserve_id != ""){
	$where = " and ri.reserve_id = '".$reserve_id."'";
}

if($db->dbms_type == "oracle"){
	$db->query("select ri.reserve_id from ".TBL_SHOP_RESERVE." as ri, shop_reserve as r where ri.uid_ = '$code' and r.reserve_id = ri.reserve_id $where");
}else{
	$db->query("select ri.reserve_id from ".TBL_SHOP_RESERVE." as ri, shop_reserve as r where ri.uid = '$code' and r.reserve_id = ri.reserve_id  $where");
}

$total = $db->total;

if($db->dbms_type == "oracle"){
	$db->query("select r.cancel_reserve, ri.id,r.uid_ as user_id ,r.oid,r.reserve,r.state,r.use_state,ri.etc, DATE_FORMAT(ri.regdate,'%Y.%m.%d %H:%i:%s') as disp_regdate from ".TBL_SHOP_RESERVE_INFO." as ri, shop_reserve as r where ri.uid_ = '$code' $where and r.reserve_id = ri.reserve_id  order by ri.regdate desc, ri.id desc LIMIT $start, $max");
}else{
	$sql = "select
				r.cancel_reserve,
				ri.id,
				r.uid as user_id ,
				r.oid,
				r.reserve,
				r.state,
				r.use_state,
				ri.etc,
				DATE_FORMAT(r.regdate, '%m.%d %H:%i:%s') as disp_regdate
			from 
				".TBL_SHOP_RESERVE_INFO." as ri,
				shop_reserve as r
			where 
				r.uid = '$code' 
				$where
				and r.reserve_id = ri.reserve_id
				order by r.regdate desc, ri.id desc LIMIT $start, $max";
	$db->query($sql);
}

if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		switch($db->dt[state]){
			case '0':
				$mstate = '대기';
				break;
			case '1':
				$mstate = '완료(+)';
				break;
			case '2':
				$mstate = '사용(-)';
				break;
			case '9':
				$mstate = '취소';
				break;
		}

		switch($db->dt[use_state]){
			case '1':
				$use_state = '상품구매';
				break;
			case '2':
				$use_state = '주문취소';
				break;
			case '3':
				$use_state = '주문반품';
				break;
			case '4':
				$use_state = '마케팅';
				break;
			case '5':
				$use_state = '기타';
				break;
			case '20':
				$use_state = '상품구매사용';
				break;
			case '21':
				$use_state = '적립소멸';
				break;
			case '22':
				$use_state = '기타';
				break;
			case '23':
				$use_state = '주문취소';
				break;
			case '24':
				$use_state = '주문반품';
				break;
		}
	
		if($db->dt[state] !='2'){	//적립상태,사용구분 선택후 수정가능 부분
			$add_display = '';
			$cancel_display = 'none';
			$font_color = '#0054FF';
		}else{
			$add_display = 'none';
			$cancel_display = '';
			$font_color = '#FF0000';
		}

		$Contents .= "<tr height=28 align=center>
								<td bgcolor='#fbfbfb'>".$db->dt[disp_regdate]."</td>
								<td style='padding:5 0 5 15' align=left><!--a href=\"javascript:UpdateReserve('".$db->dt[id]."','".$db->dt[etc]."', '".$db->dt[reserve]."','".$db->dt[state]."','".$db->dt[use_state]."')\"-->".$db->dt[etc]."<!--/a--></td>
								<td bgcolor='#fbfbfb'>";
						if($db->dt[state]	== RESERVE_STATUS_ORDER_CANCEL){
							$Contents .= "<!--a href=\"javascript:UpdateReserve('".$db->dt[id]."','".$db->dt[etc]."', '".$db->dt[reserve]."','".$db->dt[state]."','".$db->dt[use_state]."')\"--><s><font color='".$font_color."'-->".number_format($db->dt[reserve])."</font></s><!--/a-->";
						}else{
							$Contents .="<!--a href=\"javascript:UpdateReserve('".$db->dt[id]."','".$db->dt[etc]."', '".$db->dt[reserve]."','".$db->dt[state]."','".$db->dt[use_state]."')\"><font color='".$font_color."'-->". number_format($db->dt[reserve])."</font><!--/a-->";
						}
						$Contents .= "</td>
										<td bgcolor='#fbfbfb'>";
						if($db->dt[state]	== RESERVE_STATUS_ORDER_CANCEL){
							$Contents .= "<s>".number_format($db->dt[cancel_reserve])."</s>";
						}else{
							$Contents .= number_format($db->dt[cancel_reserve]);
						}
						$Contents .= "</td>
								<td>".$mstate."</td>
								<td>".$use_state."</td>
								<td>".$db->dt[oid]."</td>
								<!--<td><a href=\"javascript:DeleteReserve('".$db->dt[id]."', '".$db->dt[user_id]."')\"><img src='../image/btc_del.gif' border=0></a></td>-->
							</tr>";
	}
		//echo $Contents;
}else{
		$Contents .= "<tr height=60><td colspan=7 align=center>적립금 내용이 없습니다.</td></tr>";

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

if($db->dbms_type == "oracle"){
	$db->query("select sum(reserve) as reserve  from shop_reserve_info where uid_ = '$code' and state in ('1','2') ");
}else{
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
}

$db->fetch();
$use_reserve = $db->dt[use_reserve];
$total_reserve = $db->dt[total_reserve];

//208ab634bd0cd3e4f7d87f5b44aa3bdc
$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "회원관리 > 적립금 내역보기";
$P->NaviTitle = "적립금 보유금액 : <span style='color:#ffffff;font-size:20px;'>".number_format($total_reserve - $use_reserve)."</span> 원";
$P->title = "적립금 내역보기";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>

