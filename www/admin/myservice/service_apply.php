<?
ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear");
include("../class/layout.class");
$install_path = "../../include/";
include("SOAP/Client.php");


$soapclient = new SOAP_Client("http://www.mallstory.com/admin/service/api/");
// server.php 의 namespace 와 일치해야함
$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

$service_detail = (array)$soapclient->call("getServiceDetailInfo",$params = array("service_div"=> $_GET["service_div"],"solution_div"=> $_GET["solution_div"],"mall_domain"=> $_SERVER["HTTP_HOST"],"company_id"=> $admininfo[mall_domain_id], "mall_domain_key"=> $mall_domain_key),	$options);

//print_r($service_detail);
$service_info = (array)$service_detail[service_info];
$options_info = (array)$service_detail[option_info];
//print_r($service_info);

if($kcp_type == "test"){
	if($_SERVER["HTTPS"] == "on"){
		$pg_script = "<script type='text/javascript' src='https://pay.kcp.co.kr/plugin/payplus_test_un.js'></script>";
	}else{
		$pg_script = "<script type='text/javascript' src='http://pay.kcp.co.kr/plugin/payplus_test_un.js'></script>";
	}
}else{
	if($_SERVER["HTTPS"] == "on"){
		$pg_script = "<script type='text/javascript' src='https://pay.kcp.co.kr/plugin/payplus_un.js'></script>";
	}else{
		$pg_script = "<script type='text/javascript' src='http://pay.kcp.co.kr/plugin/payplus_un.js'></script>";
	}
}

if($mall_payment[pay_method] == "Card"){
	$pay_method = "100000000000";
}else if($mall_payment[pay_method] == "iche"){
	$pay_method = "010000000000";
}else if($mall_payment[pay_method] == "virtual"){
	$pay_method = "001000000000";
}else if($mall_payment[pay_method] == "hp"){
	$pay_method = "000010000000";
}


$Script = "
<script language='javascript' src='service_apply.js'></script>
$pg_script
<script type='text/javascript'>
StartSmartUpdate();
</script>";

$Script .= "<script language='JavaScript' >
function ChangeOption(service_unit_value, service_price){
	//alert($('#unit_cnt').val());
	$('#service_price').html(service_price);
	$('#sellprice').val(service_price);
	$('#display_service_price').html(FormatNumber(parseInt($('#service_price').html())*$('#unit_cnt').val())+' 원').css('font-weight','bold');
}

function ServiceSum(obj, unit_text){
	$('#unit_sum').html($('#unit_value').val()*obj.value+' '+unit_text) ;
	$('#service_price').html($('#sellprice').val()*obj.value);
	$('#display_service_price').html(FormatNumber($('#sellprice').val()*obj.value)+' 원');
}

function ServiceEditSum(obj, unit_text){
	$('#unit_sum').html($('#unit_value').val()*obj.value+' '+unit_text) ;
	$('#service_price').html($('#sellprice').val()/$('#unit_cnt').val()*obj.value);
	$('#display_service_price').html(FormatNumber($('#sellprice').val()/$('#unit_cnt').val()*obj.value)+' 원');

	
}

</Script>
<style>

input {border:1px solid #c6c6c6;padding:3px;}
.member_table td {text-align:left;}
</style>";

$payment_oid = date("YmdHi")."-".rand(1000, 9999);
$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "마이서비스 > 회원정보 수정", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 15px;text-align:left;' ><img src='../image/title_head.gif' align=absmiddle>  <b class=blk>".$service_info[sv_name]." > ".$service_info[sp_name]."</b> ".($_GET["si_ix"] == "" ? "신규 서비스 신청합니다.":"서비스 연장합니다")." </td></tr>
			<tr>
				<td align=center style='padding: 0 10px 0 10px;vertical-align:top'>

				      <form name='EDIT_".$db->dt[code]."' action='service_apply.act.php' method='post' onsubmit='return CheckFormValue(this)' >
					  <input type='hidden' name='act' value='service_apply'>
					  <input type='hidden' name='si_ix' value='".$_GET[si_ix]."'>
					  <input type='hidden' name='oid' value='".$payment_oid."'>
					  <input type='hidden' name='service_div' value='".$_GET[service_div]."'>
					  <input type='hidden' name='solution_div' value='".$_GET[solution_div]."'>
					  <input type='hidden' name='sp_name' value='".$service_info[sp_name]."'>
					  <input type='hidden' name='company_id' value='".$db->dt[company_id]."'>
					  <input type='hidden' name='mall_domain_key' value='".$db->dt[mall_domain_key]."'>

					  
					  <table border='0' cellspacing='1' cellpadding='5' width='100%'>
						<tr>
						  <td bgcolor='#F8F9FA'>

				<table border='0' width='100%' cellspacing='1' cellpadding='0'>
					<tr>
						<td >
							<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
								<col width=20%>
								<col width=*>
								<tr>
									<td class='input_box_title' nowrap> 서비스명</td>
									<td class='input_box_item' style='padding:0px 10px;'><b class=blk>".$service_info[sv_name]." > ".$service_info[sp_name]."</b></td>
							    </tr>";
			if($service_info[si_status] != ""){
			$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 현재 서비스 기간</td>
									<td class='input_box_item' style='padding:0px 10px;font-weight:bold;'>".$service_info[sm_sdate]." ~ ".$service_info[sm_edate]."</td>
							    </tr>";
			}
			$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 쇼핑몰 계정</td>
									<td class='input_box_item' style='padding:0px 10px;'>".$_SERVER["HTTP_HOST"]."</td>
							    </tr>
								<tr ".($service_info["unit_text"] == "" ? "style='display:none;'":"")." >
									<td class='input_box_title' nowrap> 서비스 내용</td>
									<td class='input_box_item' style='padding:0px 10px;'>";
				$amount = array("1","2","3","4","5","6","7","8","9","10","15","20","30","40","50","60","70","80","90","100");
				if($_GET["si_ix"] == ""){
						$Contents .= "".$service_info["unit_value"]." ".$service_info["unit_text"]." <div style='margin:0px 10px;display:inline;'>*</div> 
									<input type='hidden' name='unit_value' value='".$service_info["unit_value"]."'  id='unit_value' style='border:0px;' >
									<input type='hidden' name='mall_ix' value='".$admininfo[mall_ix]."'  id='mall_ix' style='border:0px;' >
									<select name='unit_cnt' id='unit_cnt' validation=false title='수량' style='width:50px;' onchange=\"ServiceSum(this,'".$service_info["unit_text"]."');\">
										<!--option>수량</option-->";
						
						for($i=0;$i <= count($amount);$i++){
							if($service_info["si_status"] == "SI"){
								$Contents .= "<option value='".$amount[$i]."' ".($amount[$i] == $service_info["service_unit_value"] ? "selected":"").">".$amount[$i]." </option>";
							}else{
								$Contents .= "<option value='".$amount[$i]."' ".($amount[$i] == "1" ? "selected":"").">".$amount[$i]." </option>";
							}
						}
						$Contents .= "
									</select><div style='margin:0px 10px;display:inline;'>=</div> 
									<div style='margin-left:10px;display:inline;' id='unit_sum'>";
						if($service_info["si_status"] == "SI"){
							$Contents .=  ($service_info["unit_value"]*$service_info["service_unit_value"])." ".$service_info["unit_text"];
						}else{
							$Contents .=  ($service_info["unit_value"])." ".$service_info["unit_text"];
						}

						$Contents .= "</div>";
				}else{
						$Contents .=  "<div id='service_text' style='display:inline;margin-right:5px;font-weight:bold;color:#ea4200;'>";
						if($service_info["si_status"] == "SI"){
							$Contents .=  number_format($service_info["service_unit_value"])." ".$service_info["unit_text"];
						}else{
							$Contents .=  ($service_info["unit_value"])." ".$service_info["unit_text"];
						}
						$Contents .=  "</div>";
						$Contents .=  "<div id='service_edit' style='display:none;margin-right:5px;'><div style='margin:0px 5px;display:inline;'>==></div>
									<input type='hidden' name='change_unit_value' value='".$service_info["unit_value"]."'  id='unit_value' style='border:0px;' >
									<input type='hidden' name='mall_ix' value='".$admininfo[mall_ix]."'  id='mall_ix' style='border:0px;' >
									<input type='hidden' name='unit_cnt' value='".$service_info["service_unit_value"]/$service_info["unit_value"]."'  id='unit_cnt' style='border:0px;' >
									
									<!--div style='margin:0px 5px;display:inline;'>".$service_info["unit_value"]."</div-->
									<select name='change_unit_cnt' validation=false title='수량' style='width:100px;' onchange=\"ServiceEditSum(this,'".$service_info["unit_text"]."');\">";
						for($i=0;$i < count($amount) ;$i++){
							if($service_info["service_unit_value"]/$service_info["unit_value"] <= $amount[$i]){
								if($service_info["si_status"] == "SI"){
									$Contents .= "<option value='".$amount[$i]."' ".($amount[$i] == $service_info["service_unit_value"] ? "selected":"").">".number_format($amount[$i]*$service_info["unit_value"])." ".$service_info["unit_text"]." </option>";
								}else{
									$Contents .= "<option value='".$amount[$i]."' ".($amount[$i] == "1" ? "selected":"").">".$amount[$i]." </option>";
								}
							}
						}
						$Contents .= "</select>";
						$Contents .=  "</div >";
						$Contents .=  "<input type='checkbox' style='border:0px;' name='apply_type' id='apply_type_c' onclick=\"if(\$(this).attr('checked')){\$('#service_edit').css('display','inline');}else{\$('#service_edit').css('display','none');}\" value='C'><label for='apply_type_c'>서비스변경</label>";
				}
						$Contents .= "
									</td>
							    </tr>
								<tr>
									<td class='input_box_title' nowrap> 서비스 항목</td>
									<td class='input_box_item' >";

									for($i=0; $i < count($options_info);$i++){
										$option_info = (array)$options_info[$i];

										//if($i == 0){
										//	$basic_price = $option_info[option_price];
										//}
										$Contents .= "<input type='radio' name='options' id='options_".($i+1)."' style='border:0px;' value='".$option_info[opnd_ix]."' ".($i == 0 ? "checked":"")." onclick=\"ChangeOption('".number_format(($i+1))."','".$option_info[option_price]."')\" validation=true title='서비스 항목'><label for='options_".($i+1)."' ><b>".$option_info[option_div]."</b></label> ";
										//number_format($option_info[option_price])
									}

									$Contents .= "
										
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 서비스 가격</td>
									<td class='input_box_item' style='padding:0px 10px;' >";
							if($service_info["si_status"] == "SI"){
							$Contents .= "
										<input type='hidden' name='sellprice' value='".($service_info["sellprice"]*$service_info["service_unit_value"]/$service_info["unit_value"])."'  id='sellprice' style='border:0px;' >
										<!--s>".number_format($service_info[listprice])."</s-->
										<b id='service_price' style='display:none;'>".($service_info[sellprice]*$service_info["service_unit_value"]/$service_info["unit_value"])."</b> 
										<b id='display_service_price'>".number_format($service_info[sellprice]*$service_info["service_unit_value"]/$service_info["unit_value"])." 원</b>   (VAT 포함)";
							}else{
							$Contents .= "
										<input type='hidden' name='sellprice' value='".$service_info["sellprice"]."'  id='sellprice' style='border:0px;' >
										<!--s>".number_format($service_info[listprice])."</s-->
										<b id='service_price' style='display:none;'>".$service_info[sellprice]."</b> 
										<b id='display_service_price'>".number_format($service_info[sellprice])." 원</b>   (VAT 포함)";
							}
							$Contents .="
									</td>
								</tr>
								<!--tr>
									<td class='input_box_title' nowrap> 서비스 기간</td>
									<td class='input_box_item'>
										<input type='radio' name='priod' id='priod_1' style='border:0px;' value='1' ".($db->dt[priod] == "1" ? "checked":"")." validation=true title='서비스기간'><label for='priod_1'>1개월(30일)</label> 
										<input type='radio' name='priod' id='priod_3' style='border:0px;' value='6' ".($db->dt[priod] == "3" ? "checked":"")." validation=true title='서비스기간'><label for='priod_3'>3개월(90일)</label>
										<input type='radio' name='priod' id='priod_6' style='border:0px;' value='9' ".($db->dt[priod] == "6" ? "checked":"")." validation=true title='서비스기간'><label for='priod_6'>6개월(120일)</label>
										<input type='radio' name='priod' id='priod_12' style='border:0px;' value='12' ".($db->dt[priod] == "12" ? "checked":"")." validation=true title='서비스기간'><label for='priod_12'>12개월(360일)</label>
									</td>
								</tr-->
								<tr>
									<td class='input_box_title' nowrap> 결제 방법</td>
									<td class='input_box_item'>
										<input type='radio' name='gopaymethod' value='bank' ".$gopaymethod." id='gopaymethod_1' style='border:0px;' onclick=\"click_method(this)\"  validation=true title='결제방법'><label for='gopaymethod_1' >무통장입금</label>
										<input type='radio' name='gopaymethod' value='Card' ".$gopaymethod." id='gopaymethod_2' style='border:0px;' onclick=\"click_method(this)\" validation=true title='결제방법'><label for='gopaymethod_2' >신용카드</label>
										<input type='radio' name='gopaymethod' value='iche' ".$gopaymethod." id='gopaymethod_3' style='border:0px;' onclick=\"click_method(this)\" validation=true title='결제방법'><label for='gopaymethod_3' >실시간계좌이체</label>
										<input type='radio' name='gopaymethod' value='virtual' ".$gopaymethod." id='gopaymethod_4' style='border:0px;' onclick=\"click_method(this)\" validation=true title='결제방법'><label for='gopaymethod_4' >가상계좌</label>
										
									</td>
								</tr>
							</table>
						</td>
					</tr>

				</table>
				<table width='100%' border='0'>
					<tr>
						<td align='left' style='line-height:120%;'>
							※ <span class='small'>  서비스 신청을 원하시는 기간과 결제 방법을 선택하시고 결제 하시면 서비스 신청이 완료됩니다.</span><br>
							※ <span class='small'>  서비스 연장시 기본 조건이 변경시 변경하신 서비스로 환산해서 연산 적용됩니다..</span>
						</td>
						
					</tr>
				</table>
				<table width='100%' border='0'>
					<tr>
						<td align='center' style='padding:20px 0px;'>
						<table>
							<tr>
								<td>";
								if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")){								
								$Contents .="<input type=image src='../images/".$admininfo["language"]."/bts_ok.gif' border=0 style='cursor:pointer;border:0px;'>";
								}else{
								$Contents .="<img src='../images/".$admininfo["language"]."/bts_ok.gif' onClick=\"alert('죄송합니다.\\n결제는 익스플로어에서만 가능합니다.');\" border=0 style='cursor:pointer;border:0px;'>";
								}
								$Contents .="
								</td>
								<td><img src='../images/".$admininfo["language"]."/btn_close.gif' border=0 onClick='self.close();' style='cursor:pointer;'></td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<!-- 수정마침 -->

			  </td>
			</tr>
			</tr>
		  </table>
		</td>
	  </tr>
	</table>
	</form>


		</td>
	</tr>

</TABLE>";


	$PaymentAddScript	="
	<form name='order_info' method='post' action='./complete_app.php' id='pay_form'>
	<input type=hidden name='pg_com' value='kcp'>
	<input type=hidden name='incom_bank' value=''>
	<input type=hidden name='incom_name' value=''>
	<input type=hidden name='receipt_type' value=''>
	<input type=hidden name='Confirm_no' value=''>
	<input type=hidden name='Gubun_cd' value=''>

	<input type=hidden name='com_name' value=''>
	<input type=hidden name='com_ceo' value=''>
	<input type=hidden name='com_number' value=''>
	<input type=hidden name='com_business_status' value=''>
	<input type=hidden name='com_business_category' value=''>
	<input type=hidden name='com_charge_name' value=''>
	<input type=hidden name='com_charge_tel' value=''>
	<input type=hidden name='com_charge_mail' value=''>
	<input type=hidden name='com_zip' value=''>
	<input type=hidden name='com_addr1' value=''>
	<input type=hidden name='com_addr2' value=''>

	<input type='hidden' name='req_tx'          value='pay' />
	<input type=hidden name='gopaymethod' value='".$mall_payment[pay_method]."'>
	<input type=hidden name='pay_method' value='".$pay_method."'>
	<input type=hidden name='ordr_idxx' value='".$mall_payment[oid]."'>
	<input type=hidden name='good_name' value='".$mall_payment[pname]."'>
	<input type=hidden name='good_mny' value='".$mall_payment[sellprice]."'>
	<input type=hidden name='buyr_name' value='".$mall_payment[name]."'>
	<input type=hidden name='buyr_mail' value='".$mall_payment[mail]."'>
	<input type=hidden name='buyr_tel1' value='".$mall_payment[tel]."'>
	<input type=hidden name='buyr_tel2' value='".$mall_payment[pcs]."'>
	<input type='hidden' name='site_cd'         value='".$kcp_id."' />
	<input type='hidden' name='site_key'        value='".$kcp_key."' />
	<input type='hidden' name='site_name'       value='mallstory' />
	<input type='hidden' name='quotaopt'        value='12'/>
	<input type='hidden' name='currency'        value='WON'/>
	<input type='hidden' name='module_type'     value='01'/>
	<input type='hidden' name='epnt_issu'       value='' />
	<input type='hidden' name='soc_no'          value='' />
	<input type='hidden' name='escw_used'       value='N'/>
	<input type='hidden' name='res_cd'          value=''/>
	<input type='hidden' name='res_msg'         value=''/>
	<input type='hidden' name='tno'             value=''/>
	<input type='hidden' name='trace_no'        value=''/>
	<input type='hidden' name='enc_info'        value=''/>
	<input type='hidden' name='enc_data'        value=''/>
	<input type='hidden' name='ret_pay_method'  value=''/>
	<input type='hidden' name='tran_cd'         value=''/>
	<input type='hidden' name='bank_name'       value=''/>
	<input type='hidden' name='bank_issu'       value=''/>
	<input type='hidden' name='use_pay_method'  value=''/>
	<input type='hidden' name='cash_tsdtime'    value=''/>
	<input type='hidden' name='cash_yn'         value=''/>
	<input type='hidden' name='cash_authno'     value=''/>
	<input type='hidden' name='cash_tr_code'    value=''/>
	<input type='hidden' name='cash_id_info'    value=''/>
	<input type='hidden' name='wish_vbank_list' value='05:03:04:07:11:23:26:32:34:81:71'/>
	<input type='hidden' name='disp_tax_yn'     value='Y'/>
	<input type='hidden' name='site_logo'       value='http://testpay.kcp.co.kr/plugin/img/KcpLogo.jpg' />

	<!-- 에스크로 결제처리 모드 : 에스크로: Y, 일반: N, KCP 설정 조건: O  -->
	<input type='hidden' name='pay_mod'         value='N'/>
	<!-- 배송 소요일 : 예상 배송 소요일을 입력 -->
	<input type='hidden'  name='deli_term' value='03'/>
	<!-- 장바구니 상품 개수 : 장바구니에 담겨있는 상품의 개수를 입력(good_info의 seq값 참조) -->
	<input type='hidden'  name='bask_cntx' value='".count($carts3)."'/>
	<!-- 장바구니 상품 상세 정보 (자바 스크립트 샘플 create_goodInfo()가 온로드 이벤트시 설정되는 부분입니다.) -->
	<input type='hidden' name='good_info'       value=''/>
	<input type='hidden' name='rcvr_name' value='".$mall_payment[name]."'/>
	<input type='hidden' name='rcvr_tel1' value='".$mall_payment[tel]."'/>
	<input type='hidden' name='rcvr_tel2' value='".$mall_payment[pcs]."'/>
	<input type='hidden' name='rcvr_mail' value='".$mall_payment[mail]."'/>
	<input type='hidden' name='rcvr_zipx' value='".$mall_payment[zip]."'/>
	<input type='hidden' name='rcvr_add1' value='".$mall_payment[addr1]."'/>
	<input type='hidden' name='rcvr_add2' value='".$mall_payment[addr2]."'/>
	</form>";


$Contents .= $PaymentAddScript;

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "마이서비스 > 서비스 신청/연장";
$P->NaviTitle = "서비스 신청/연장";
$P->title = "서비스 신청/연장";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
