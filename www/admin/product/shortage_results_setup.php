<?
include("../class/layout.class");

$shmop_file = $admininfo[company_id]."_shortage_results_setup";
$shortage_data = getBasicSellerSetup($shmop_file);		//소매적립금 정책 2014-04-11 이학봉

$db = new Database;

$Script = "
<script language='JavaScript' >
function CheckSMS(frm){

	if(frm.sms_contents.value.length < 1){
		alert('SMS 내용을 입력해주세요');
		return false;
	}

	if(frm.mobiles.value.length < 1){
		alert('SMS 보낼 회원이 한명이상이어야 합니다.');
		return false;
	}

	return true;
}

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}
</Script>";

$Contents = "

	<table border='0' width='100%' cellpadding='0' cellspacing='0'>
		<tr>
			<td align='left' colspan=2> ".GetTitleNavigation("상품관리", "실적부족 상품 리스트 노출 설정")."</td>
		</tr>
		<form name='edit_frm' method='post' action='shortage_results_setup.act.php'  onSubmit='return CheckSearch(this)'>
		<input type='hidden' name='act' value='update'>
		<input type='hidden' name='company_id' value='".$admininfo[company_id]."'>
		<tr>
			<td>
				<table class='input_table_box' style='width:100%;' cellpadding=0 cellspacing=0>
				<col width=20%>
				<col width=80%>
				<tr>
					<td class='input_box_title'>사용여부</td>
					<td class='input_box_item'>
						<input type='radio' name='disp' id='disp_1' value='1' ".($shortage_data[disp] == '1' || $shortage_data[disp] == ""?'checked':'')."><label for='disp_1'> 사용</label>
						<input type='radio' name='disp' id='disp_0' value='0' ".($shortage_data[disp] == '0'?'checked':'')."><label for='disp_0'> 미 사용</label>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'>판매상태</td>
					<td class='input_box_item'>
						<input type='checkbox' class='checkbox' name='shortage_state' id='shortage_state_1' value='1' ".($shortage_data[shortage_state] == '1'?'checked':'')."><label for='shortage_state_1'> 판매중</label>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'>설정</td>
					<td class='input_box_item'>
						<table>
						<tr>
							<td>
								<input type='checkbox' name='selling_use' value='1' ".($shortage_data[selling_use] == '1'?'checked':'').">
							</td>
							<td>
								판매중일
							</td>
							<td>
								<select name='shortage_selling_date' style='width:50px;'>";
								for($i=1;$i<=31;$i++){
							$Contents .= "<option value='".$i."' ".($shortage_data[shortage_selling_date] == $i?'selected':'').">".$i."</option>";
								}
							$Contents .= "
								</select>
							</td>
							<td>
								일 이상
							</td>
						</tr>
						<tr>
							<td>
								<input type='checkbox' name='view_cnt_use' value='1' ".($shortage_data[view_cnt_use] == '1'?'checked':'').">
							</td>
							<td>
								클릭수
							</td>
							<td>
								<input type='text' name='shortage_view_cnt' class='textbox numeric' id='shortage_view_cnt' style='width:40px;' value='".$shortage_data[shortage_view_cnt]."'>
							
							</td>
							<td>
								클릭 이하
							</td>
						</tr>
						<tr>
							<td>
								<input type='checkbox' name='order_cnt_use' value='1' ".($shortage_data[order_cnt_use] == '1'?'checked':'').">
							</td>
							<td>
								판매수량
							</td>
							<td>
								<input type='text' name='shortage_order_cnt' class='textbox numeric' id='shortage_order_cnt' style='width:40px;' value='".$shortage_data[shortage_order_cnt]."'>
							</td>
							<td>
								개 이하
							</td>
						</tr>
						<tr>
							<td>
								<input type='checkbox' name='order_rate' value='1' ".($shortage_data[order_rate] == '1'?'checked':'').">
							</td>
							<td>
								클릭대비 판매율
							</td>
							<td>
								<select name='shortage_order_rate' style='width:50px;'>";
								for($i=0;$i<31;$i++){
							$Contents .= "<option value='".$i."' ".($shortage_data[shortage_order_rate] == $i?'selected':'')." >".$i."</option>";
								}
							$Contents .= "
								</select>
							</td>
							<td>
								<span class='small blu'> 0~ 30%만 설정가능 </span>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
		<td align=center style='padding:10px 0 0 0' colspan=2>
			<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle> 
		</td>
	</tr>
	</form>
	</table>";

$help_text = "
	<table  style='width:100%;' cellpadding=0 cellspacing=0>
	<tr>
		<td>
			<span class='small blu'> * 실적부족상품을 이용하시면 현재 판매부진 상품을 손쉽게 확인할 수 있으며, 해당 상품에 대한 프로모션 혹은 잘못된 노출로 인한 상품일 수 있습니다.</span>
		</td>
	</tr>
	<tr>
		<td height='8'></td>
	</tr>
	<tr>
		<td>
			<span class='small blu'> * 설정 기간은 평균 판매일수의 평균 클릭수 대비 판매수량을 설정하면 해당되는 상품을 리스트에 노출합니다.</span>
		</td>
	</tr>
	</table>";

$Contents .= HelpBox("실적부족상품 설정", $help_text,'100');

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "상품관리 > 실적부족 상품 리스트 설정";
$P->NaviTitle = "실적부족 상품 리스트 설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>





