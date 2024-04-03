<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");


$shmop = new Shared("deposit_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$deposit_data = $shmop->getObjectForKey("deposit_rule");
$deposit_data = unserialize(urldecode($deposit_data));

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>예치금 관리 정책</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>예치금 사용 여부 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='deposit_use_yn' id='deposit_use_y' value='Y' ".($deposit_data[deposit_use_yn] == "Y" ? "checked":"")."> <label for='deposit_use_y'>사용 </label>
		<input type='radio' name='deposit_use_yn' id='deposit_use_n' value='N' ".($deposit_data[deposit_use_yn] =="N" || $deposit_data[deposit_use_yn] == "" ? "checked":"")."> <label for='deposit_use_n'>사용안함</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50'>
		<td class='input_box_title'> <b>예치금 충전 시 결제 타입 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='checkbox' name='deposit_payment_module[]' id='module_card' value='card' ".CompareReturnValue("card",$deposit_data[deposit_payment_module],"checked")."> <label for='module_card'>신용카드 </label>
		<input type='checkbox' name='deposit_payment_module[]' id='module_virtual' value='virtual' ".CompareReturnValue("virtual",$deposit_data[deposit_payment_module],"checked")."> <label for='module_virtual'>가상계좌</label>
		<input type='checkbox' name='deposit_payment_module[]' id='module_iche' value='iche' ".CompareReturnValue("iche",$deposit_data[deposit_payment_module],"checked")."> <label for='module_iche'>실시간계좌이체</label>
		<input type='checkbox' name='deposit_payment_module[]' id='module_bank' value='bank' ".CompareReturnValue("bank",$deposit_data[deposit_payment_module],"checked")."> <label for='module_bank'>무통장입금</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='display:none;' class='bank_input_day'>
		<td class='input_box_title'> <b>무통장 입금기일<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		 <input type='text' name='bank_input_day' id='bank_input_day' style='width:30px;' value='".$deposit_data[bank_input_day]."' > 일 
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50'>
		<td class='input_box_title'> <b>결제모듈 선택 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='sattle_module' id='sattle_module_inipay' value='inipay' ".($deposit_data[sattle_module] == "inipay" ? "checked":"")."> <label for='sattle_module_inipay'>이니시스</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50'>
		<td class='input_box_title'> <b>결제모듈 정보입력 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<a href='javascript:void(0)' onclick=\"settlement_config()\" />정보입력페이지 이동 </a>
		</td>
	</tr>
	<!--tr bgcolor='#ffffff' height='50'>
		<td class='input_box_title'> <b>증빙문서 발급 시점 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='receipt_issued_type' id='receipt_issued_type_y' value='now' ".($deposit_data[receipt_issued_type] == "now" ? "checked":"")."> <label for='receipt_issued_type_y'>충전 시 발행 </label>
		<input type='radio' name='receipt_issued_type' id='receipt_issued_type_n' value='after' ".($deposit_data[receipt_issued_type] =="after" || $deposit_data[receipt_issued_type] == "" ? "checked":"")."> <label for='receipt_issued_type_n'>사용시 발행</label>
		
		</td>
	</tr-->
	<!--tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'>
			<b>예치금 환불 설정 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			<input type='radio' name='deposit_refund_yn' id='deposit_refund_y' value='Y' ".($deposit_data[deposit_refund_yn] == "Y" || $deposit_data[deposit_refund_yn] == ""  ? "checked":"")."> <label for='deposit_refund_y'>가능 </label>
			<input type='radio' name='deposit_refund_yn' id='deposit_refund_n' value='N' ".($deposit_data[deposit_refund_yn] =="N" ? "checked":"")."> <label for='deposit_refund_n'>불가</label>
		</td>
	</tr-->

	</table>";

////////////////////////////////////////////////////////////////////////////
if(false){
$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='20%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>예치금 사용정책</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'> <b>예치금 사용제한 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px;'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed; padding:5px;'>
			<col width='100%' />
			<tr height=30>
				<td style='padding-left:5px;'> 
					- 일반 상품 구매 합계액이  <input type=text class='textbox number' name='total_order_price' value='".$deposit_data[total_order_price]."' style='width:60px;' validation='true' title='예치금 In-Use제한 설정'> 원 이상 상품 구매시사용 가능(무제한이일 경우 0원입력)
				</td>
			</tr>
			<tr height=30>
				<td style='padding-left:5px;'>
					- 서비스 상품 구매 합계액이  <input type=text class='textbox number' name='service_total_order_price' value='".$deposit_data[service_total_order_price]."' style='width:60px;' validation='true' title='예치금 In-Use제한 설정'> 원 이상 상품 구매시사용 가능(무제한이일 경우 0원 입력)
				</td>
			</tr>
			<tr height=30>
				<td style='padding-left:5px;'> 
					<span class=blue>* 신규 상품 등록시 기본으로 적용되며, 상품별로 개별 설정시 본 설정은 적용되지 않습니다.".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
				</td>
			</tr>
			<tr height=30>
				<td style='padding-left:5px;'>
					- 보유 예치금이   <input type=text class='textbox number' name='min_deposit_price' value='".$deposit_data[min_deposit_price]."' style='width:60px;' validation='true' title='예치금 In-Use제한 설정'> 원 이상일때 상품 구매시 사용 가능(제한이 없을경우 0입력)
				</td>
			</tr>
			<tr height=30>
				<td > 
					<input type=radio name='deposit_one_use_type'  id='once_deposit_one_use_type_1' value='1' ".($deposit_data[deposit_one_use_type] == "1" ? "checked":"")." > 1회 사용한도 최대  <input type=text class='textbox number' name='use_deposit_max' value='".$deposit_data[use_deposit_max]."' style='width:60px;' id='deposit_one_use_type' ".($deposit_data[deposit_one_use_type] == "1" ? "validation='true'":"validation='false'")." title='예치금 1회 In-Use 한도'> 원  까지만 사용 가능 * 0원일 경우 전액예치금 사용 가능 합니다.
				</td>
			</tr>
			<tr height=30>
				<td>
					<input type=radio name='deposit_one_use_type' id='once_deposit_one_use_type_2' value='2' ".($deposit_data[deposit_one_use_type] == "2" ? "checked":"")." > 1회 사용한도 상품 구매 합계액의 <input type=text class='textbox number' name='max_goods_sum_rate' value='".$deposit_data[max_goods_sum_rate]."' style='width:60px;' id='max_goods_sum_rate' ".($deposit_data[deposit_one_use_type] == "2" ? "validation='true'":"validation='false'")." title='Mileage 1회 In-Use 한도'>%  까지만 사용 가능 * 100%시 전액 예치금 사용 가능합니다.
				</td>
			</tr>
			</table>
		</td>
	</tr>

	
</table>";

////////////////////////////////////////////////////////////////////////////

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='20%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>예치금 소멸기간 설정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'>
			<b>예치금 자동소멸 설정 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			<input type='radio' name='deposit_extinction_yn' id='deposit_extinction_y' value='Y' ".($deposit_data[deposit_extinction_yn] == "Y"   ? "checked":"")."> <label for='deposit_extinction_y'>사용 </label>
			<input type='radio' name='deposit_extinction_yn' id='deposit_extinction_n' value='N' ".($deposit_data[deposit_extinction_yn] =="N"  || $deposit_data[deposit_extinction_yn] == "" ? "checked":"")."> <label for='deposit_extinction_n'>사용안함</label>
		</td>
	</tr>
	
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'>
			<b>휴면 회원 자동 소멸 기간 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item' style='padding:10px;'>
			최근 주문일로부터 &nbsp;&nbsp;
			<select name='order_year' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=10; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$deposit_data[order_year],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 년  &nbsp;&nbsp;
			<select name='order_month' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=12; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$deposit_data[order_month],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 개월 지난 회원으로 예치금이 &nbsp;&nbsp;
			<input type='text' class='textbox number' name='order_member_deposit' value='".$deposit_data[order_member_deposit]."' style='width:70px;'>&nbsp;&nbsp; 미만인 회원은 휴면회원으로 간주하여 회사는 해당 회원의 충전된 예치금을 회수 합니다.
		</td>
	</tr>
	
</table>";
}
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px;'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='deposit_rule.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>
<input type='hidden' name='act' value='deposit'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";


  $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
$help_text .= "증빙문서를 예치금 충전 시 발행 할 경우 예치금 환불 기능을 사용 할 수 없습니다. (이미 세무 처리가 완료된 금액으로 세무관련 이슈가 있습니다.)";
$Contents .=  HelpBox("예치금 설정", $help_text, 100);

$Script = "<script language='javascript'>
$(document).ready(function(){
	$('#deposit_refund_y').click(function(){
		if($(':radio[name=receipt_issued_type]:checked').val() == 'now'){
			alert('예치금 환불 설정의 경우 증빙문서 발급 시점이 예치금 사용 시 에만 가능 합니다.');
			$('#deposit_refund_n').attr('checked',true);
		}
			
	});

	$('#receipt_issued_type_y').click(function(){
		if($(':radio[name=deposit_refund_yn]:checked').val() == 'Y'){
			alert('증빙문서 발급 시점이 충전 시 발급 일경우 예치금 환불 기능을 사용 할 수 없습니다.');
			$('#deposit_refund_n').attr('checked',true);
		}
	});
	if($('#module_bank').is(':checked')){
		$('.bank_input_day').show();
	}else{
		$('.bank_input_day').hide();
	}
	$('input[name^=deposit_payment_module]').click(function(){
		console.log($(this).is(':checked'))
		if(this.value == 'bank'){
			if($(this).is(':checked')){
				$('.bank_input_day').show();
			}else{
				$('.bank_input_day').hide();
			}
		}
	});
});
function settlement_config(){
	var sattle_module = $(':input:radio[name=sattle_module]:checked').val();
	if( sattle_module == undefined){
		alert('결제모듈을 선택해 주세요');
	}else{
		location.href='/admin/store/settlement_config.php?sattle_module='+sattle_module;
	}
	
}


</script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = buyer_accounts_menu();
$P->Navigation = "구매자정산관리 > 예치금 설정";
$P->title = "예치금 설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>