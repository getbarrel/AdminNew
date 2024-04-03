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
	var frm = document.forms['reserve'];

	frm.reset();
	frm.act.value = 'reserve_insert';
	$('#inputBtn').attr('disabled',false);
	
}

var checkReserveBool = true;
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
	
	$('#inputBtn').attr('disabled','disabled');
	
	var inputNum = $('input[name=reserve]').val();
	var inputMsg = $('input[name=etc]').val();
	var inputType = $('select[name=state] :selected').val();
    var msg = '적립내역을 확인해 주세요 \\n';
	switch(inputType){
        case '1':
            msg += '적립상태 : 완료(+)\\n';
            msg += '적립내용 : '+inputMsg+'\\n';
            msg += '마일리지 : '+inputNum+'\\n';
            msg += '금액을 적립처리 하시겠습니까?';
            break;
        case '2':
            msg += '적립상태 : 사용(-)\\n';
            msg += '적립내용 : '+inputMsg+'\\n';
            msg += '마일리지 : '+inputNum+'\\n';
            msg += '금액을 사용처리 하시겠습니까?';           
            break;
    }

	if(confirm(msg)){
	    if(checkReserveBool == true){
	        checkReserveBool = false;
	        return true;
	    }else{
	        alert('등록중입니다.');
	        location.reload();
	        return false;
	    }        
    }else{
	    $('#inputBtn').attr('disabled',false);
	    return false;
    }
}

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>

			<tr height=30><td class='p11 ls1' style='padding:0 0 0 20px;text-align:left;' > - 마일리지 내용을 추가 하실수 있습니다. (기존 적립,사용 된 마일리지는 수정이 불가능 합니다.)</td></tr>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
				<form name='reserve' method='post'  action='member.act.php'  onSubmit='return CheckReserve(this)' target='act'>
				<input type='hidden' name='act' value='reserve_insert'>
				<input type='hidden' name='reserve_id' value='".$reserve_id."'>
				<input type='hidden' name='uid' value='".$code."'>
				<input type='hidden' name='id' value=''>

						<table border='0' width='100%' cellspacing='1' cellpadding='0' class='search_table_box' style='margin-bottom:10px;'>
							<tr height='20' valign='middle' align=center >
								<td align='center' class='search_box_title' style='text-align:center;padding:0px;'><b>회원</b></td>
								<td class='search_box_title' style='text-align:center;padding:0px;'>적립상태</td>
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
								<td align='center'  ><input type='text' class=textbox name='etc' value='' size=35></td>
								<td><input type='text' class=textbox name='reserve' size=10></td>
								
								<td>
									<input type='image' src='../images/btn/ok.gif' align=absmiddle id='inputBtn' > 
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
							<td class='m_td' width=10% >상태 </td>
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
	$sql = "select 
				ri.ml_ix 
			from 
				shop_mileage_log as ri
			where 
				ri.uid_ = '$code' 
			$where";
	$db->query($sql);
}else{
	$sql = "select 
				ri.ml_ix 
			from 
				shop_mileage_log as ri
			where 
				ri.uid = '$code' 
			$where";
	$db->query($sql);
}

$total = $db->total;

if($db->dbms_type == "oracle"){
	$sql = "select 
				ri.ml_mileage as mileage, ri.ml_ix,ri.uid_ as user_id ,ri.oid,ri.ml_state as state,ri.message, DATE_FORMAT(ri.regdate,'%Y.%m.%d %H:%i:%s') as disp_regdate 
			from 
				shop_mileage_log as ri
			where 
				ri.uid_ = '$code' 
			$where 
				order by ri.ml_ix desc LIMIT $start, $max";

	$db->query($sql);
}else{
	$sql = "select
				ri.ml_mileage as mileage,
				ri.ml_ix,
				ri.uid as user_id ,
				ri.oid,
				ri.ml_state as state,
				ri.message,
				DATE_FORMAT(ri.regdate, '%m.%d %H:%i:%s') as disp_regdate
			from 
				shop_mileage_log as ri
			where 
				ri.uid = '$code' 
			$where
				order by ri.ml_ix desc LIMIT $start, $max";
	$db->query($sql);
}

if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		switch($db->dt[state]){
			case '1':
				$mstate = '적립완료(+)';
				break;
			case '2':
				$mstate = '사용(-)';
				break;
			case '9':
				$mstate = '취소';
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
								<td style='padding:5px 0 5px 15px;' align=left> ".$db->dt[message]." </td>
								<td bgcolor='#fbfbfb'>";
						
							$Contents .="<font color='".$font_color."'>". number_format($db->dt[mileage])."</font> ";
						
						$Contents .= "</td>
								<td>".$mstate."</td>
								<td>".$db->dt[oid]."</td>
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
    $sql = "select 
				mileage
			from 
				common_user
			where 
				code = '$code' ";
    $db->query($sql);
    $db->fetch();
    $user_mileage = $db->dt[mileage];
}

//208ab634bd0cd3e4f7d87f5b44aa3bdc
if($mmode == "personalization") {
    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->Navigation = "회원관리 > 마일리지 내역보기";
    $P->NaviTitle = "마일리지 보유금액 : <span style='color:black;font-size:20px;'>" . number_format($user_mileage) . "</span> 원";
    $P->title = "마일리지 내역보기";
    $P->strContents = $Contents;
    $P->layout_display = false;
    $P->view_type = "personalization";
    echo $P->PrintLayOut();
}else {
    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->Navigation = "회원관리 > 마일리지 내역보기";
    $P->NaviTitle = "마일리지 보유금액 : <span style='color:#ffffff;font-size:20px;'>" . number_format($user_mileage) . "</span> 원";
    $P->title = "마일리지 내역보기";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}

?>

