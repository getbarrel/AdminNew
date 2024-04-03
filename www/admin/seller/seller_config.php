<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
include("../econtract/contract.lib.php");

$shmop = new Shared("basic_seller_setup");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$reserve_data = $shmop->getObjectForKey("basic_seller_setup");
$reserve_data = unserialize(urldecode($reserve_data));

$seller_use_info = $reserve_data[seller_use_info];
$seller_join_type = $reserve_data[seller_join_type]; 
$seller_minishop_use = $reserve_data[seller_minishop_use];
$account_info = $reserve_data[account_info];
$ac_delivery_type = $reserve_data[ac_delivery_type];
$ac_expect_date = $reserve_data[ac_expect_date];
$ac_term_div = $reserve_data[ac_term_div];
$ac_term_date1 = $reserve_data[ac_term_date1];
$ac_term_date2 = $reserve_data[ac_term_date2];

$account_type = $reserve_data[account_type];
$account_method = $reserve_data[account_method];			//=
$wholesale_commission = $reserve_data[wholesale_commission];
$commission = $reserve_data[commission];
$seller_grant_use = $reserve_data[seller_grant_use];
$grant_setup_price = $reserve_data[grant_setup_price];
$ac_grant_price = $reserve_data[ac_grant_price];
$account_div = $reserve_data[account_div];

$electron_contract_commission = $reserve_data[electron_contract_commission];	//전자계약서내 수수료율
$contract_group = $reserve_data[contract_group];
$et_ix = $reserve_data[et_ix];

$db = new Database;
$db2 = new Database;

$mall_ix = $admininfo[mall_ix];	//해당 사이트 mall_ix
$act = 'config_update';

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<col width='25%' />
		<col width='30%' />
		<col width='20%' />
		<col width='30%' />
	<tr>
		<td align='left' colspan=2 > ".GetTitleNavigation("배송/택배정책", "상점관리 > 배송/택배정책 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=".urlencode("몰스토리동영상메뉴얼_배송택배정책(090322)_config.xml")."',800,517,'manual_view')\"  title='배송/택배정책 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a>")."</td>
	</tr>
	<tr>
		<td align='left' colspan=2 style='padding-bottom:20px;'>
		<div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='tab_01' class='on' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='#'>셀러 기본설정</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='width:600px;text-align:right;vertical-align:bottom;padding:0 0 10px 0'>
				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
	</table>";

$Contents .= "
	<form name='delivery_form' action='seller.act.php' method='post' onsubmit='return SubmitX(this)' target='act'>
	<input name='act' type='hidden' value='".$act."'>
	<input name='company_id' type='hidden' value = '".$admininfo[company_id]."'>
	<input name='mall_ix' type='hidden' value='".$mall_ix."'>";

	$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>기본정책_</b>셀러가 첫 등록시 기본값으로 설정하는 곳입니다.
			</td>
		</tr>
	</table>";

	$Contents .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' class='input_table_box'>
	<col width='18%' />
	<col width='*' />
		<tr bgcolor=#ffffff>
			<td class='input_box_title'> <b>사이트 설정 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=radio name='seller_use_info' value='1' id='seller_use_info_1' checked><label for='seller_use_info_1'> 종합몰(역발행)</label>&nbsp;
				<!--<input type=radio name='seller_use_info' value='2' id='seller_use_info_2' ".CompareReturnValue("2",$seller_use_info,"checked")."><label for='seller_use_info_2'> 오픈마켓(서비스수수료)</label>-->
			</td>
		</tr>
		<tr bgcolor=#ffffff>
			<td class='input_box_title'> <b>셀러 사용여부 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=radio name='seller_join_type' value='M' id='seller_join_type_a' ".CompareReturnValue("M",$seller_join_type,"checked")."><label for='seller_join_type_a'> 사용(수동승인)</label>&nbsp;
				<input type=radio name='seller_join_type' value='A' id='seller_join_type_m' ".CompareReturnValue("A",$seller_join_type,"checked")."><label for='seller_join_type_m'> 사용(자동승인)</label>&nbsp;
			</td>
		</tr>
		<tr bgcolor=#ffffff>
			<td class='input_box_title'> <b>셀러 미니샵 사용여부 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=radio name='seller_minishop_use' value='1' id='seller_minishop_use_1' ".CompareReturnValue("1",$seller_minishop_use,"checked")." checked><label for='seller_minishop_use_1'> 사용(수동승인)</label>&nbsp;
				<input type=radio name='seller_minishop_use' value='2' id='seller_minishop_use_2' ".CompareReturnValue("2",$seller_minishop_use,"checked")."><label for='seller_minishop_use_2'> 사용(자동승인)</label>&nbsp;
			</td>
		</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' style='padding-top:20px'>
	</table>";

	$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>셀러 정산 설정</b>
			</td>
		</tr>
	</table>";

	$Contents .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
	<col width='18%' />
	<col width='*' />
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>정산 방식 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding-top:5px;padding-bottom:5px;'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td height=25>
						<input type='radio' name='account_type' id='account_type_1' value='1' ".CompareReturnValue('1',$account_type,"checked")."> <label for='account_type_1'>판매가 정산방식  ( 판매가에 수수료 적용 후 정산처리됩니다. )</label>
					</td>
				</tr>
				<tr height=25>
					<td>
						<input type='radio' name='account_type' id='account_type_2' value='2' ".CompareReturnValue('2',$account_type,"checked")."> <label for='account_type_2'>매입가 정산방식 ( 공급가로 정산되며, 하단 수수료에 0 이 아닌 숫자를 입력시 그 숫자의 % 만큼 차감 후 정산 처리됩니다.)</label>
					</td>
				</tr>
				<!--
				<tr>
					<td>
						<input type='radio' name='account_type' id='account_type_3' value='3' ".CompareReturnValue('3',$account_type,"checked")."> <label for='account_type_3'>미정산 ( 선 매입으로 본사에 재고가 있으며, 상품등록을 셀러가 진행시에 사용되며, 정산에서 제외됩니다.)</label>
					</td>
				</tr>-->
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>정산 예정 내역 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item' style='padding:10px;'>
		    <input type='hidden' name='account_info' value='1' title='기본설정(기간별) 기간설정' />
		    
		    상품별 주문 처리상태가 
		     <select name='ac_delivery_type' id='ac_delivery_type_1' style='width:80px;'>
                <option value='0'> 선택 </opion>
                <option value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
                <option value='".ORDER_STATUS_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_READY,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
                <option value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
                <option value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
                <option value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</option>
            </select> 
			으로 변경된 일로부터 
			<select name='ac_expect_date' id='ac_expect_date_1' style='width:45px;'>";
            for($i=0; $i<=31; $i++){
                $Contents .= "<option value='".$i."' ".CompareReturnValue($i,$ac_expect_date,"selected").">".$i." </option>";
            }
            $Contents .= "
            </select>일 후
            
			<!--
			<table width='100%' cellpadding=0 cellspacing=0 border='0' >
			<col width='15%' />
			<col width='*' />
			<tr bgcolor=#ffffff>
				<td class='input_box_item'>
					<input type='radio' name='account_info' value='2' id='account_info_2' ".CompareReturnValue('2',$account_info,"checked")."> <label for='account_info_2'> 상품별(건별)정산</label>
				</td>
			</tr>
			<tr bgcolor=#ffffff>
				<td class='input_box_item' style='padding-left:20px;'>
					배송 처리상태 <select name='ac_delivery_type' id='ac_delivery_type_2' style='width:80px;'>
					<option value='0'> 선택 </opion>
					<option value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
					<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
					<option value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</option>
					</select> 
					상태 변경후 <select name='ac_expect_date' id='ac_expect_date_2' style='width:45px;'>
					<option value='0'> 선택 </opion>
					";
					for($i=1; $i<=31; $i++){
						$Contents .= "<option value='".$i."' ".CompareReturnValue($i,$ac_expect_date,"selected").">".$i." </option>";
					}
					
			$Contents .= "
					</select> 일 후 정산신청 처리됩니다.
				</td>
			</tr>
			</table>
			-->
			
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>정산확정내역 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item' style='padding:10px;'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' >
			<tr>
				<td>
				<select name='ac_term_div' id='ac_term_div'  style='width:80px;'>
				<option value='0'> 선택 </opion>
				<option value='1' ".CompareReturnValue('1',$ac_term_div,"selected").">월 1 회</option>
				<option value='2' ".CompareReturnValue('2',$ac_term_div,"selected").">월 2 회</option>
				<option value='3' ".CompareReturnValue('3',$ac_term_div,"selected").">매주 1 회</option>
				</select> 
				
				<select name='ac_term_date1' id='ac_term_date1' style='width:45px;'>
				<option value='0'> 선택 </opion>
				";
				for($i=1; $i<=31; $i++){
					$Contents .= "<option value='".$i."' ".CompareReturnValue($i,$ac_term_date1,"selected").">".$i." </option>";
				}
				
		$Contents .= "
				</select> 일 
				<select name='ac_term_date2' id='ac_term_date2' style='width:45px;'>
				<option value='0'> 선택 </opion>
				";
				for($i=1; $i<=31; $i++){
					$Contents .= "<option value='".$i."' ".CompareReturnValue($i,$ac_term_date2,"selected").">".$i." </option>";
				}
				
		$Contents .= "
				</select>
				<select name='ac_term_date1' id='ac_term_date1_week' style='width:70px;display:none;'>
					<option value='0' ".CompareReturnValue('0',$ac_term_date1,"selected")."> 일요일 </opion>
					<option value='1' ".CompareReturnValue('1',$ac_term_date1,"selected")."> 월요일 </opion>
					<option value='2' ".CompareReturnValue('2',$ac_term_date1,"selected")."> 화요일 </opion>
					<option value='3' ".CompareReturnValue('3',$ac_term_date1,"selected")."> 수요일 </opion>
					<option value='4' ".CompareReturnValue('4',$ac_term_date1,"selected")."> 목요일 </opion>
					<option value='5' ".CompareReturnValue('5',$ac_term_date1,"selected")."> 금요일 </opion>
					<option value='6' ".CompareReturnValue('6',$ac_term_date1,"selected")."> 토요일 </opion>
				</select>
				 정산 확정
				</td>
			</tr>
			<tr>
				<td style='padding-top:6px'>
					<span class='small blu'> * 매월 설정된 일자의 전일까지 누적된 정산예정내역의 주문 건이 정산확정 내역으로 넘어갑니다.</span><br/>
					<span class='small blu'> * 정산확정내역에서 운영사와 입점사 상호 협의 하여 정산금액을 최종 조정한 후, 운영사가 송금대기 처리를 합니다.</span>
				</td>
			</tr>
			</table>
		</td>
	</tr>

	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>정산 지급방식  <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
		<input type='radio' id='account_method_cash' name='account_method' checked value='".ORDER_METHOD_CASH."' ><label for='account_method_cash'> 현금</label>
		<!--
		<input type='radio' id='account_method_service' name='account_method' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$account_method,"checked")."><label for='account_method_service'> 예치금</label>
		-->
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>정산 유형  <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<div id='account_div' style='float:left;width:230px;position:relative;top:2px;'>
				<input type='radio' id='account_div_c' name='account_div' value='c' ".CompareReturnValue('c',$account_div,"checked")."><label for='account_div_c'> 카테고리별 설정</label>
				<input type='radio' id='account_div_s' name='account_div' value='s' ".CompareReturnValue('s',$account_div,"checked")."><label for='account_div_s'> 셀러별 설정</label>
			</div>
			<div id='account_div_table' style='float:left;'>
				소매 수수료 : 
				<input type='text' id='commission' name='commission' style='width:30px; text-align:center;' value='".$commission."' maxlength='2'><label for='commission'> %</label>&nbsp;&nbsp;&nbsp;
				도매 수수료 : 
				<input type='text' id='wholesale_commission' name='wholesale_commission' style='width:30px; text-align:center;' value='".$wholesale_commission."'  maxlength='2'><label for='wholesale_commission'> %</label>
			</div>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>전자계약 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
		전자계약 선택 
		".getContractGroup($contract_group, "onchange=\"loadContract($(this), 'et_ix')\"")."
		".getContract($contract_group, $et_ix,"   ")."
		&nbsp;&nbsp;&nbsp;
		계약서내 수수료율 &nbsp;&nbsp;
		<input type='text' class='textbox numeric' name='electron_contract_commission' style='width:40px;' value='".$electron_contract_commission."'> %
		</td>
	</tr>
	</table>";

$Contents1 .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class=''>
	<tr>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b>  셀러 판매장려금</b><span class=small> &nbsp;&nbsp;&nbsp;<font color=#5B5B5B>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</font> </span></div>")."</td>
	</tr>
	</table>";

$Contents1 .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
	<col width='20%' />
	<col width='*' />
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>추가 판매장려금 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' style='padding:10px;'>
				<table cellpadding=0 cellspacing=0 width='100%'>
					<tr>
						<td class='input_box_item'>
							<input type='radio' id='seller_grant_use_1' name='seller_grant_use' value='1' ".CompareReturnValue("1",$seller_grant_use,"checked")." checked><label for='seller_grant_use_1'> 사용</label>
							<input type='radio' id='seller_grant_use_0' name='seller_grant_use' value='0' ".CompareReturnValue("0",$seller_grant_use,"checked")."><label for='seller_grant_use_0'> 미사용</label>
						</td>
					</tr>
					<tr>
						<td class='input_box_item'>
							매출액 
							<input type='text' id='grant_setup_price' name='grant_setup_price' style='width:80px' value='".$grant_setup_price."' dir='rtl'> 원 이상일 경우 정산시 
							<input type='text' id='ac_grant_price' name='ac_grant_price' style='width:80px' value='".$ac_grant_price."' dir='rtl'> 원 추가 수수료 정산에서 합니다. (VAT 포함)<br>

							* 매출액 목표가 달성시 매 달성 금액 회수만큼 추가 정산 합니다.<br>
							* 정산 기준으로 매월 31일 까지의 매출을 통계하여 측정됩니다.
							</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>";
$Contents .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'>
	<tr bgcolor=#ffffff >
			<td colspan=2 align=center style='padding:20px;'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .= "<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
			}
	$Contents .= "
		</td></tr>
	</table>
</form>";


$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>셀러기본 설정 수정 히스토리</b>
			</td>
		</tr>
	</table>

	<table width=100%  opt_idx=0 cellspacing=0 cellpadding=0 class='input_table_box'>
	<col width='18%'>
	<col width='*'>
	<tr height=100>
		<td class='input_box_title'><b>셀러기본설정 수정 정보</b></td>
		<td class='input_box_item'>
			<div style='width:98%;height:85px;padding:6px;margin:5px;line-height:140%;overflow:auto' >".nl2br(seller_edit_history_Text($admininfo['company_id']))."</div>
		</td>
	</tr>
	</table>";


$Script = "
<script language='JavaScript' >

function loadContract(obj,target) {
	
	var contract_group = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name'); 

	$.ajax({ 
		type: 'GET', 
		data: {'act':'getContractList','return_type': 'json',  'contract_group':contract_group},
		url: '../econtract/contract.act.php',  
		dataType: 'json', 
		async: true, 
		beforeSend: function(){  
		},  
		error: function(request,status,error){ 
			alert('code:'+request.status+':: message:'+request.responseText+':: error:'+error);
		},  
		success: function(datas){
			$('select#'+target).find('option').not(':first').remove();
			if(datas != null){
				$.each(datas, function(i, data){ 
						$('select[name='+target+']').append(\"<option value='\"+data.et_ix+\"'>\"+data.contract_title+\"</option>\");
				});  
			}
		} 
	});  
}


function SubmitX(frm){
	if(!CheckFormValue(frm)){
		return false;
	}
	frm.content.value = iView.document.body.innerHTML;
	return true;
}

$(document).ready(function (){
	
	$('input[name=account_type]').click(function (){
		var value = $(this).val();
		if(value == '2'){
			$('#wholesale_commission').val('0');
			$('#commission').val('0');
			$('#wholesale_commission').attr('readonly',true);
			$('#commission').attr('readonly',true);

		}else{
			$('#wholesale_commission').attr('readonly',false);
			$('#commission').attr('readonly',false);
		}
	});

	if($('input[name=account_type]:checked').val() == '2'){
		$('#wholesale_commission').val('0');
		$('#commission').val('0');
		$('#wholesale_commission').attr('readonly',true);
		$('#commission').attr('readonly',true);
	}else{
		$('#wholesale_commission').attr('readonly',false);
		$('#commission').attr('readonly',false);
	}
	
	if($('input[name=account_info]:checked').val() == '1' || true){

		$('#ac_delivery_type_2').attr('disabled',true);
		$('#ac_expect_date_2').attr('disabled',true);
		
		$('#ac_delivery_type_1').attr('disabled',false);
		$('#ac_expect_date_1').attr('disabled',false);

		//$('#ac_delivery_type_2').val('0');
		//$('#ac_expect_date_2').val('0');

	}else{

		$('#ac_delivery_type_1').attr('disabled',true);
		$('#ac_expect_date_1').attr('disabled',true);

		$('#ac_delivery_type_2').attr('disabled',false);
		$('#ac_expect_date_2').attr('disabled',false);

		//$('#ac_delivery_type_1').val('0');
		//$('#ac_expect_date_1').val('0');
	}

	$('input[name=account_info]').click(function (){
	
		var value = $(this).val();

		if(value == '1'){

			$('#ac_delivery_type_2').attr('disabled',true);
			$('#ac_expect_date_2').attr('disabled',true);
			
			$('#ac_delivery_type_1').attr('disabled',false);
			$('#ac_expect_date_1').attr('disabled',false);

			//$('#ac_term_div').attr('disabled',false);
			//$('#ac_term_date1_week').attr('disabled',false);
			//$('#ac_term_date1').attr('disabled',false);
			//$('#ac_term_date2').attr('disabled',false);

		}else if (value == '2'){

			$('#ac_delivery_type_1').attr('disabled',true);
			$('#ac_expect_date_1').attr('disabled',true);
			$('#ac_delivery_type_2').attr('disabled',false);
			$('#ac_expect_date_2').attr('disabled',false);

			//$('#ac_term_div').attr('disabled',true);
			//$('#ac_term_date1_week').attr('disabled',true);
			//$('#ac_term_date1').attr('disabled',true);
			//$('#ac_term_date2').attr('disabled',true);
		
		}
	
	});

	$('#ac_term_div').change(function(){
		
		var value = $(this).val();

		if(value == '1'){

			$('#ac_term_date2').css('display','none');
			$('#ac_term_date2').attr('disabled',true);
			
			$('#ac_term_date1_week').css('display','none');
			$('#ac_term_date1_week').attr('disabled',true);
			
			$('#ac_term_date1').css('display','');
			$('#ac_term_date1').attr('disabled',false);

		}else if(value == '2'){
			
			$('#ac_term_date1').css('display','');
			$('#ac_term_date1').attr('disabled',false);

			$('#ac_term_date2').css('display','');
			$('#ac_term_date2').attr('disabled',false);
			
			$('#ac_term_date1_week').css('display','none');
			$('#ac_term_date1_week').attr('disabled',true);
		
		}else if(value == '3'){

			$('#ac_term_date1_week').css('display','');
			$('#ac_term_date1_week').attr('disabled',false);

			$('#ac_term_date1').css('display','none');
			$('#ac_term_date1').attr('disabled',true);

			$('#ac_term_date2').css('display','none');
			$('#ac_term_date2').attr('disabled',true);
		}
	});

	change_term_div();

	
	$('input[name=account_div]').click(function (){
		var value = $(this).val();
		if(value == 'c'){
			$('#account_div_table').css('display','none');
		}else{
			$('#account_div_table').css('display','');
		}
	});

	var accrount_div_value = $('input[name=account_div][checked]').val(); 
	if(accrount_div_value == 'c'){
		$('#account_div_table').css('display','none');
	}else{
		$('#account_div_table').css('display','');
	}
});

function change_term_div(){
	
	value = $('#ac_term_div').val();

	if(value == '1'){

		$('#ac_term_date2').css('display','none');
		$('#ac_term_date2').attr('disabled',true);
		
		$('#ac_term_date1_week').css('display','none');
		$('#ac_term_date1_week').attr('disabled',true);
		
		$('#ac_term_date1').css('display','');
		$('#ac_term_date1').attr('disabled',false);

	}else if(value == '2'){
		
		$('#ac_term_date1').css('display','');
		$('#ac_term_date1').attr('disabled',false);

		$('#ac_term_date2').css('display','');
		$('#ac_term_date2').attr('disabled',false);
		
		$('#ac_term_date1_week').css('display','none');
		$('#ac_term_date1_week').attr('disabled',true);
	
	}else if(value == '3'){

		$('#ac_term_date1_week').css('display','');
		$('#ac_term_date1_week').attr('disabled',false);

		$('#ac_term_date1').css('display','none');
		$('#ac_term_date1').attr('disabled',true);

		$('#ac_term_date2').css('display','none');
		$('#ac_term_date2').attr('disabled',true);
	}

}
</script>
";


$P = new LayOut();
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->strLeftMenu = seller_menu();
$P->Navigation = "셀러관리 > 셀러설정 > 셀러 기본설정";
$P->title = "셀러 기본설정";
$P->TitleBool = false;
$P->strContents = $Contents;
echo $P->PrintLayOut();

// 상품 수정 페이지 히스토리 노출 함수 2014-04-09 이학봉
function seller_edit_history_Text($company_id){

	if(!$company_id){
		return false;
	}
	$db = new Database;

	$sql = "select 
			*
			from
				common_seller_config_edit_history
			where
				company_id = '".$company_id."'
				order by sceh_ix ASC";
	$db->query($sql);
	$data_array = $db->fetchall();

/*
	$compare_value[0] = array("input_name"=>"seller_use_info", "column_name"=>"seller_use_info", "name_text"=>"사이트설정");
	$compare_value[1] = array("input_name"=>"seller_join_type", "column_name"=>"seller_join_type", "name_text"=>"셀러 사용여부");
	$compare_value[2] = array("input_name"=>"seller_minishop_use", "column_name"=>"seller_minishop_use", "name_text"=>"셀러 미니샵 사용여부");

	$compare_value[3] = array("input_name"=>"account_type", "column_name"=>"account_type", "name_text"=>"정산방식");
	$compare_value[4] = array("input_name"=>"account_info", "column_name"=>"account_info", "name_text"=>"정산 상품 기간설정");

	$compare_value[5] = array("input_name"=>"ac_delivery_type", "column_name"=>"ac_delivery_type", "name_text"=>"배송처리상태(정산상품기간설정)");
	$compare_value[6] = array("input_name"=>"ac_expect_date", "column_name"=>"ac_expect_date", "name_text"=>"정산처리예정일자");

	$compare_value[7] = array("input_name"=>"ac_term_div", "column_name"=>"ac_term_div", "name_text"=>"정산확정일(월횟수)");
	$compare_value[8] = array("input_name"=>"ac_term_date1", "column_name"=>"ac_term_date1", "name_text"=>"정산처리일(1)");
	$compare_value[9] = array("input_name"=>"ac_term_date2", "column_name"=>"ac_term_date2", "name_text"=>"정산처리일(2)");

	$compare_value[10] = array("input_name"=>"account_method", "column_name"=>"account_method", "name_text"=>"정산 지급방식");

	$compare_value[11] = array("input_name"=>"account_div", "column_name"=>"account_div", "name_text"=>"정산유형");	

	$compare_value[12] = array("input_name"=>"wholesale_commission", "column_name"=>"wholesale_commission", "name_text"=>"도매수수료");
	$compare_value[13] = array("input_name"=>"commission", "column_name"=>"commission", "name_text"=>"소매수수료");

	$compare_value[14] = array("input_name"=>"electron_contract_category", "column_name"=>"electron_contract_category", "name_text"=>"전자계약서분류");

	$compare_value[15] = array("input_name"=>"electron_contract", "column_name"=>"electron_contract", "name_text"=>"계약서종류");
	$compare_value[16] = array("input_name"=>"electron_contract_commission", "column_name"=>"electron_contract_commission", "name_text"=>"계약서내 수수료율");
*/
	for($i=0;$i<count($data_array);$i++){

		if($data_array[$i][column_name] == 'seller_use_info'){
			switch($data_array[$i][b_data]){
				case '1':
					$b_data = "종합몰(역발행)";
				break;
				case '2':
					$b_data = "오픈마켓(서비스수수료)";
				break;
			}

			switch($data_array[$i][after_data]){
				case '1':
					$after_data = "종합몰(역발행)";
				break;
				case '2':
					$after_data = "오픈마켓(서비스수수료)";
				break;
			}

			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_data." -> <b>".$after_data."</b> ( ".$data_array[$i][chager_name]." )\n";
		}else if($data_array[$i][column_name] == 'seller_join_type'){
			switch($data_array[$i][b_data]){
				case 'A':
					$b_data = "자동승인";
				break;
				case 'M':
					$b_data = "수동승인";
				break;
			}

			switch($data_array[$i][after_data]){
				case 'A':
					$after_data = "자동승인";
				break;
				case 'B':
					$after_data = "수동승인";
				break;
			}

			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_data." -> <b>".$after_data."</b> ( ".$data_array[$i][chager_name]." )\n";
		}else if($data_array[$i][column_name] == 'seller_minishop_use'){
			switch($data_array[$i][b_data]){
				case '1':
					$b_data = "수동승인";
				break;
				case '2':
					$b_data = "자동승인";
				break;
			}

			switch($data_array[$i][after_data]){
				case '1':
					$after_data = "수동승인";
				break;
				case '2':
					$after_data = "자동승인";
				break;
			}

			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_data." -> <b>".$after_data."</b> ( ".$data_array[$i][chager_name]." )\n";
		}else if($data_array[$i][column_name] == 'account_type'){
			switch($data_array[$i][b_data]){
				case '1':
					$b_data = "판매가 정산방식";
				break;
				case '2':
					$b_data = "매입가 정산방식";
				break;
				case '3':
					$b_data = "미정산";
				break;
			}

			switch($data_array[$i][after_data]){
				case '1':
					$after_data = "판매가 정산방식";
				break;
				case '2':
					$after_data = "매입가 정산방식";
				break;
				case '2':
					$after_data = "미정산";
				break;
			}

			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_data." -> <b>".$after_data."</b> ( ".$data_array[$i][chager_name]." )\n";
		}else if($data_array[$i][column_name] == 'ac_delivery_type'){
			switch($data_array[$i][b_data]){
				case 'DI':
					$b_data = "배송중";
				break;
				case 'DC':
					$b_data = "배송완료";
				break;
				case 'BF':
					$b_data = "거래완료";
				break;
			}

			switch($data_array[$i][after_data]){
				case 'DI':
					$after_data = "배송중";
				break;
				case 'DC':
					$after_data = "배송완료";
				break;
				case 'BF':
					$after_data = "거래완료";
				break;
			}

			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_data." -> <b>".$after_data."</b> ( ".$data_array[$i][chager_name]." )\n";
		}else if($data_array[$i][column_name] == 'ac_term_div'){
			switch($data_array[$i][b_data]){
				case '1':
					$b_data = "월 1회";
				break;
				case '2':
					$b_data = "월 2회";
				break;
				case '3':
					$b_data = "매주 1회";
				break;
			}

			switch($data_array[$i][after_data]){
				case '1':
					$after_data = "월 1회";
				break;
				case '2':
					$after_data = "월 2회";
				break;
				case '3':
					$after_data = "매주 1회";
				break;
			}

			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_data." -> <b>".$after_data."</b> ( ".$data_array[$i][chager_name]." )\n";
		}else if($data_array[$i][column_name] == 'account_div'){
			switch($data_array[$i][b_data]){
				case 'c':
					$b_data = "카테고리별 설정";
				break;
				case 's':
					$b_data = "셀러별 설정";
				break;
			}
			switch($data_array[$i][after_data]){
				case 'c':
					$after_data = "카테고리별 설정";
				break;
				case 's':
					$after_data = "셀러별 설정";
				break;
			}

			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$b_data." -> <b>".$after_data."</b> ( ".$data_array[$i][chager_name]." )\n";
		}else{
			$history_text .= $data_array[$i][regdate]." ".$data_array[$i][column_text]." 변경 : ".$data_array[$i][b_data]." -> ".$data_array[$i][after_data]." ( ".$data_array[$i][chager_name]." )\n";
		}
	}

	return $history_text;
}
// 상품 수정 페이지 히스토리 노출 함수 2014-04-09 이학봉


?>