<?
include("../class/layout.class");
include_once("service.lib.php");

	//print_r($admin_config);
	//echo $_SESSION["admin_config"]["mall_admin_root"];
$Script = "
<style>

input {border:1px solid #c6c6c6}
.receipt_table td {height:25px;}
</style>
<script type='text/javascript'>
 function  jsf__pay_cash( form )
{
	/*jsf__show_progress(true);

	if ( jsf__chk_cash( form ) == false )
	{
		jsf__show_progress(false);
		return;
	}*/

	form.submit();
}

// 진행 바
function  jsf__show_progress( show )
{
	if ( show == true )
	{
		window.show_pay_btn.style.display  = 'none';
		window.show_progress.style.display = 'inline';
	}
	else
	{
		window.show_pay_btn.style.display  = 'inline';
		window.show_progress.style.display = 'none';
	}
}

function  jsf__chk_cash( form )
{
	if ( form.trad_time.value.length != 14 )
	{
		alert(language_data['receipt_apply.php']['A'][language]);//원 거래 시각을 정확히 입력해 주시기 바랍니다.
		form.trad_time.select();
		form.trad_time.focus();
		return false;
	}

	if ( form.corp_type.value == '1' )
	{
		if ( form.corp_tax_no.value.length != 10 )
		{
			alert(language_data['receipt_apply.php']['B'][language]);//발행 사업자번호를 정확히 입력해 주시기 바랍니다.
			form.corp_tax_no.select();
			form.corp_tax_no.focus();
			return false;
		}
	}

	if (  form.tr_code[0].checked )
	{
		if ( form.id_info.value.length != 10 &&
			 form.id_info.value.length != 11 &&
			 form.id_info.value.length != 13 )
		{
			alert(language_data['receipt_apply.php']['C'][language]);//주민번호 또는 휴대폰번호를 정확히 입력해 주시기 바랍니다.
			form.id_info.select();
			form.id_info.focus();
			return false;
		}
	}
	else if (  form.tr_code[1].checked )
	{
		if ( form.id_info.value.length != 10 )
		{
			alert(language_data['receipt_apply.php']['D'][language]);//사업자번호를 정확히 입력해 주시기 바랍니다.
			form.id_info.select();
			form.id_info.focus();
			return false;
		}
	}
	return true;
}
</script>
";

$db = new MySQL;
$db2 = new MySQL;
$db3 = new MySQL;
$db4 = new MySQL;

$db3->query("SELECT pname FROM service_order_detail where oid = '$oid' ");
$db3->fetch();
$pname = $db3->dt[pname];

$db->query("SELECT r.*,AES_DECRYPT(UNHEX(md.name),'".$db->ase_encrypt_key."') as name,AES_DECRYPT(UNHEX(md.mail),'".$db->ase_encrypt_key."') as mail,AES_DECRYPT(UNHEX(md.tel),'".$db->ase_encrypt_key."') as tel FROM service_receipt r , ".TBL_COMMON_USER." m, ".TBL_COMMON_MEMBER_DETAIL." md where order_no = '$oid' and r.id = m.id AND m.code=md.code ");
$db->fetch();

	$db2->query("select payment_price, use_reserve_price,use_cupon_price from service_order where oid = '".$oid."' ");
	$db2->fetch();
	$payment_price = $db2->dt[payment_price];
	$use_reserve_price = $db2->dt[use_reserve_price];
	$use_cupon_price = $db2->dt[use_cupon_price];
	//$use_delivery_price = $db2->dt[delivery_price];
	//$use_member_sale_price = $db2->dt[use_member_sale_price];
	$price = $payment_price;

/*
//과세와 비과세를 계산해서 부과세를 계산함
$sql = "select sum(ptprice), count(*) from service_order_detail where oid = '$oid' and surtax_yorn = 'Y' ";
$db3->query($sql);
$db3->fetch();

$surtax_y = $db3->dt[0];
$surtax_y_cnt = $db3->dt[1];

$sum_use_price=$use_reserve_price+$use_cupon_price;
//비과세 - y
if($surtax_y >= $sum_use_price){ //비과세가 적립금보다 같거나 클때 실행
	$surtax_y = $surtax_y - $sum_use_price;
	$reserve_price = 0;
}else{
	$reserve_price = $sum_use_price - $surtax_y;
	$surtax_y = 0;
}
$sql = "select sum(ptprice), count(*) from service_order_detail where oid = '$oid' and surtax_yorn = 'N' ";
$db3->query($sql);
$db3->fetch();
//과세 - n
$surtax_n_cnt = $db3->dt[1];
if($db3->dt[0] >= $reserve_price){
	$surtax_n = $db3->dt[0] - $reserve_price;
}else{
	$surtax_n = $reserve_price - $db3->dt[0];
}
$surtax_n = $surtax_n - round($surtax_n/1.1);
*/
$surtax_n=0;
if($db->dt[m_useopt] == 0){
	$m_useopt = "소득공제용";
}else{
	$m_useopt = "지출증빙용";
}

$surtax_cnt = $surtax_n_cnt + $surtax_y_cnt - 1;
if($surtax_cnt>0) $pname = $pname." 외 ".$surtax_cnt;
else $pname = $pname;

$sql="SELECT inipay_mid,lgdacom_id,kcp_id FROM ".TBL_SHOP_SHOPINFO." WHERE mall_domain = '".str_replace("www.","",$HTTP_HOST)."'";
$db3->query($sql);
$db3->fetch();
$inipay_mid=$db3->dt["inipay_mid"];
$lgdacom_id=$db3->dt["lgdacom_id"];
$kcp_id=$db3->dt["kcp_id"];

$sql = "select ccd.company_id, csd.shop_name, csd.shop_desc, ccd.com_name , ccd.com_number, ccd.online_business_number, ccd.com_ceo, ccd.com_zip, ccd.com_addr1, 
		ccd.com_addr2, ccd.com_phone, ccd.com_fax, ccd.com_email ,ccd.com_business_status, ccd.com_business_category 
		from common_seller_detail csd, common_company_detail ccd
		where csd.company_id = ccd.company_id and ccd.com_type = 'A'";
$db4->query($sql);
$db4->fetch();
$biz_no=$db4->dt["com_number"];
$shop_name=$db4->dt["shop_name"];
$ceo=$db4->dt["com_ceo"];
$company_address=$db4->dt["com_zip"]." ".$db4->dt["com_addr1"]." ".$db4->dt["com_addr2"];
$phone=$db4->dt["com_phone"];

$Contents = "

		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			";
if($_SESSION["admininfo"]["sattle_module"]=="kcp") {

$Contents .= "
			<form name='cash_form' action='/admin/order/kcp/sample/cash/pp_cli_hub.php' method='post'>
			<input type='hidden' name='req_tx' value='pay'>
			<input type='hidden' name='regi_type' value='service'>
			<input type='hidden' name='corp_type' value='0'>
			<input type='hidden' name='corp_tax_type' value='TG01'>
			<input type='hidden' name='corp_tax_no' value='".$biz_no."'>
			<input type='hidden' name='corp_nm' value='".$shop_name."'>
			<input type='hidden' name='corp_owner_nm' value='".$ceo."'>
			<input type='hidden' name='corp_addr' value='".$company_address."'>
			<input type='hidden' name='corp_telno' value='".$phone."'>
			<input type='hidden' name='trad_time' value='".date("YmdHis")."'>
			<input type='hidden' name='tr_code' value='".$db->dt[m_useopt]."'>";
			$oid_txt="ordr_idxx";
			$good_name_txt="good_name";
			$bname_txt="buyr_name";
			$bmail_txt="buyr_mail";
			$btel_txt="buyr_tel1";
			$bmobil_txt="id_info";
			$ptprice_txt="amt_tot";
			$supply_txt="amt_sup";
			$service_txt="amt_svc";
			$tax_txt="amt_tax";

} else if($_SESSION["admininfo"]["sattle_module"]=="inicis") {

$Contents .= "
			<form name='cash_form' action='./inicis/INIreceipt.php' method='post'>
			<input type=hidden name=mid size=20 maxlength=10 value='".$inipay_mid."'>
			<input type=hidden name=currency value='WON'>
			<input type=hidden name=clickcontrol value=''>
			<input type=hidden name=useopt value='".$db->dt[m_useopt]."'>";
			$oid_txt="ordr_idxx";
			$good_name_txt="goodname";
			$bname_txt="buyername";
			$bmail_txt="buyeremail";
			$btel_txt="buyertel";
			$bmobil_txt="reg_num";
			$ptprice_txt="cr_price";
			$supply_txt="sup_price";
			$service_txt="srvc_price";
			$tax_txt="tax";

} else if($_SESSION["admininfo"]["sattle_module"]=="lgdacom") {

} else if($_SESSION["admininfo"]["sattle_module"]=="nicepay") {
	$sql = "select * from shop_payment_config where mall_ix = '".$admininfo[mall_ix]."' and pg_code = 'nicepay' ";
	$db3->query($sql);
	//$db->fetch();

	for($i=0;$i < $db3->total;$i++){
		$db3->fetch($i);
		$payment_config[$db3->dt[config_name]] = $db3->dt[config_value];
	}
	$Contents .= "
		<form name='tranMgr' method='post' action='./nicepay/receiptResult.php'>
		<input type=hidden name=MERCHANTKEY value='".$payment_config["nicepay_key"]."'>
		<input type=hidden name=MID value='".$payment_config["nicepay_id"]."'>
		<input type=hidden name=ReceiptType value='".$db->dt[m_useopt]."'>
	";
			$oid_txt="ordr_idxx";
			$good_name_txt="GoodsName";
			$bname_txt="BuyerName";
			$bmail_txt="BuyerEmail";//사용안함
			$btel_txt="BuyerTel";//사용안함
			$bmobil_txt="ReceiptTypeNo";
			$ptprice_txt="ReceiptAmt";
			$supply_txt="ReceiptSupplyAmt";
			$service_txt="ReceiptServiceAmt";
			$tax_txt="ReceiptVAT";
}
$Contents .= "
			<tr>
				<td align=center style='padding:10px 10px 0 10px'>
				
					<table border='0' width='100%' cellspacing='0' cellpadding='0' >
						<tr>
							<td >
								<table border='0' width='100%' cellspacing='1' cellpadding='0' bgcolor='#c0c0c0' class='input_table_box'>
									<col width='140'>
									<col width='*'>
									<col width='140'>
									<col width='*'>
									<tr>
										<td class='input_box_title'> 주문번호</td>
										<td class='input_box_item' colspan=3>&nbsp;<input type='text'  class=textbox name='".$oid_txt."' size='30' maxlength='40' value='".$db->dt[order_no]."'></td>
									</tr>
									<tr>
										<td class='input_box_title' > 상품정보</td>
										<td class='input_box_item' colspan=3>&nbsp;<input type='text'  class=textbox name='".$good_name_txt."' maxlength='30' size='50' value='".$pname."'></td>
									</tr>
									<tr>
										<td class='input_box_title' > 주문자이름</td>
										<td class='input_box_item' colspan=3>&nbsp;<input type='text'  class=textbox name='".$bname_txt."' size='20' maxlength='20' value='".$db->dt[name]."'></td>

									</tr>
									<tr>
										<td class='input_box_title' > 주문자 E-Mail</td>
										<td class='input_box_item' colspan=3>&nbsp;<input type='text'  class=textbox name='".$bmail_txt."' size='20' maxlength='50' value='".$db->dt[mail]."'>
										</td>

									</tr>
									<tr>
										<td class='input_box_title' > 주문자 전화번호</td>
										<td class='input_box_item' >&nbsp;<input type='text'  class=textbox name='".$btel_txt."' size='20' maxlength='20' value='".$db->dt[tel]."'>
										</td>
									
										<td class='input_box_title' style='color:#cc0000'> 주민(휴대폰)번호</td>
										<td  class='input_box_item' >&nbsp;<input type='text'  class=textbox name='".$bmobil_txt."' size='16' maxlength='13' value='".$db->dt[m_number]."'></td>
									</tr>
									<tr>
										<td class='input_box_title' > 발행용도</td>
										<td  class='input_box_item' colspan=3>&nbsp;".$m_useopt."</td>
									</tr>
									<tr>
										<td class='input_box_title' style='color:#cc0000'> 거래금액 총합</td>
										<td class='input_box_item' >&nbsp;<input type='text'  class=textbox name='".$ptprice_txt."' size='12' maxlength='9' value='".$price."'></td>

									
										<td class='input_box_title' style='color:#cc0000'> 공급가액</td>
										<td class='input_box_item'>&nbsp;<input type='text'  class=textbox name='".$supply_txt."' size='12' maxlength='9' value='".$price."'></td>
									</tr>
									<tr>
										<td class='input_box_title' > 봉사료</td>
										<td class='input_box_item' >&nbsp;<input type='text'  class=textbox name='".$service_txt."' size='12' maxlength='9' value='0'></td>
									
										<td class='input_box_title' > 부가가치세</td>
										<td class='input_box_item' >&nbsp;<input type='text'  class=textbox name='".$tax_txt."' size='12' maxlength='9' value='".$surtax_n."'></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan='2' align='center' style='padding:10px 0px;'>
								<span id='show_pay_btn'>
									<img src='../images/".$admininfo["language"]."/btn_reg.gif' onclick='jsf__pay_cash( document.cash_form )' style='cursor:pointer;'>
								</span>
								<span id='show_progress' style='display:none'>
									<b>등록 진행중입니다. 잠시만 기다려주십시오</b>
								</span>
							</td>
						</tr>
					</table>
		
		</form>
		</td>
	</tr>
</TABLE>";





$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "서비스 관리 > 증빙서 > 현금영수증신청";
$P->NaviTitle = "현금영수증신청";
$P->title = "현금영수증신청";
$P->strContents = $Contents;
echo $P->PrintLayOut();




