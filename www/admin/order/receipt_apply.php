<?
/*
lgdacom 추가 13/06/05
현금영수증 발급 금액을 shop_order_detail 의 주문상태(status)가 결제완료인 것들만 가져와서 shop_order 에서 주문차감금액을 빼고 배송비를 더해서 실주문금액으로 넘기게 수정 kbk 13/06/05
*/
include("../class/layout.class");


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

$db = new Database;
$db2 = new Database;
$db3 = new Database;

$sql = "SELECT r.*,
		AES_DECRYPT(UNHEX(md.name),'".$db->ase_encrypt_key."') as name,
		AES_DECRYPT(UNHEX(md.mail),'".$db->ase_encrypt_key."') as mail,
		AES_DECRYPT(UNHEX(md.tel),'".$db->ase_encrypt_key."') as tel 
	FROM receipt r LEFT JOIN ".TBL_COMMON_USER." m ON r.id = m.id LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." md ON  m.code=md.code 
where order_no = '$oid' ";
$db->query($sql);//비회원 신청때문에 쿼리 수정함 kbk 13/08/03
$db->fetch();
if($db->dt[m_useopt] == 0){
	$m_useopt = "소득공제용";
}else{
	$m_useopt = "지출증빙용";
}

$sql = "SELECT 
			SUM(case when pay_type ='F' then -tax_price else tax_price end) as tax_price,
			SUM(case when pay_type ='F' then -tax_free_price else tax_free_price end) as tax_free_price,
			ic_date
		FROM shop_order_payment where oid='".$oid."' and method in (select method from shop_order_payment where oid='".$oid."' and pay_status='IC' and pay_type = 'G' and method not in ('".ORDER_METHOD_RESERVE."') and receipt_yn='Y' group by method) ";
$db2->query($sql);
$db2->fetch();
$ic_date = $db2->dt["ic_date"];
if($db2->dt["tax_price"] > 0){
	$tax_price = $db2->dt["tax_price"];
}

if($db2->dt["tax_free_price"] > 0){
	$tax_free_price = $db2->dt["tax_free_price"];
}

$price=$tax_price+$tax_free_price;
$tax=$tax_price-round($tax_price/1.1);

$db2->query("SELECT pname FROM ".TBL_SHOP_ORDER_DETAIL." where oid = '$oid' ");
$db2->fetch();
$pname = $db2->dt[pname];

if($db2->total > 1) $pname = $pname." 외 ".($db2->total - 1);
else $pname = $pname;

$db2->query("select bname,order_date from ".TBL_SHOP_ORDER." o where o.oid = '".$oid."' ");//선불 배송비 서브 쿼리 사용 kbk 13/06/05
$db2->fetch();
$bname = $db2->dt[bname];
$order_date = $db2->dt[order_date];

$sql="SELECT inipay_mid,lgdacom_id,lgdacom_key,lgdacom_type,kcp_id FROM ".TBL_SHOP_SHOPINFO." WHERE mall_ix = '".$_SESSION["admininfo"]["mall_ix"]."' ";
$db3->query($sql);
$db3->fetch();
$inipay_mid=$db3->dt["inipay_mid"];
$lgdacom_id=$db3->dt["lgdacom_id"];
$lgdacom_key=$db3->dt["lgdacom_key"];
$lgdacom_type=$db3->dt["lgdacom_type"];
$kcp_id=$db3->dt["kcp_id"];

$Contents = "
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>";

if($_SESSION["admininfo"]["sattle_module"]=="kcp") {

$Contents .= "
			<form name='cash_form' action='./kcp/sample/cash/pp_cli_hub.php' method='post'>
			<input type='hidden' name='req_tx' value='pay'>
			<input type='hidden' name='corp_type' value='0'>
			<input type='hidden' name='corp_tax_type' value='TG01'>
			<input type='hidden' name='corp_tax_no' value='".$_SESSION["shopcfg"]["biz_no"]."'>
			<input type='hidden' name='corp_nm' value='".$_SESSION["shopcfg"]["shop_name"]."'>
			<input type='hidden' name='corp_owner_nm' value='".$_SESSION["shopcfg"]["ceo"]."'>
			<input type='hidden' name='corp_addr' value='".$_SESSION["shopcfg"]["company_address"]."'>
			<input type='hidden' name='corp_telno' value='".$_SESSION["shopcfg"]["phone"]."'>
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

} else if($_SESSION["admininfo"]["sattle_module"]=="lgdacom") {//lgdacom 추가 kbk 13/06/05
	switch($method) {
		case "0" : $pay_method="SC0100";//무통장입금
		break;
		case "4" : $pay_method="SC0040";//가상계좌
		break;
		case "5" : $pay_method="SC0030";//계좌이체
		break;
	}
	$sql="SELECT * FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE com_type='A' ";
	$db3->query($sql);
	$db3->fetch();
$Contents .= "
			<form name='cash_form' method='post' id='LGD_PAYINFO' action='/admin/order/lgdacom/receiptResult.php'>
			<input type='hidden' name='CST_MID' value='".$lgdacom_id."' />
			<input type='hidden' name='CST_PLATFORM' value='".$lgdacom_type."' /><!-- 서비스구분 -->
			<input type='hidden' name='LGD_PAYTYPE' value='".$pay_method."' />
			<input type='hidden' name='LGD_TID' value='".$tid."' />
			<input type='hidden' name='LGD_METHOD' value='AUTH' /><!-- 발급/취소 AUTH: 발급, CANCEL: 취소 -->
			<input type='hidden' name='LGD_CUSTOM_MERTNAME' value='".trim($db3->dt["com_name"])."' /><!-- 상점명 -->
			<input type='hidden' name='LGD_CUSTOM_BUSINESSNUM' value='".str_replace("-","",trim($db3->dt["com_number"]))."' /><!-- 사업자등록번호 -->
			<input type='hidden' name='LGD_CUSTOM_MERTPHONE' value='".str_replace("-","",trim($db3->dt["com_phone"]))."' /><!-- 상점전화번호 -->
			<input type='hidden' name='LGD_CASHRECEIPTUSE' value='".($db->dt[m_useopt]+1)."' /><!-- 현금영수증발급용도 -->";
			$oid_txt="LGD_OID";
			$bmobil_txt="LGD_CASHCARDNUM";
			$ptprice_txt="LGD_AMOUNT";
			$good_name_txt="LGD_PRODUCTINFO";
			if($method==0) $pname=$pname;
			else $pname="";//무통장입금이 아닌 경우 상품명을 받지 않는다

} else if($_SESSION["admininfo"]["sattle_module"]=="nicepay") {
	$sql = "select * from shop_payment_config where mall_ix = '".$admininfo[mall_ix]."' and pg_code = 'nicepay' ";
	$db3->query($sql);
	//$db->fetch();

	for($i=0;$i < $db3->total;$i++){
		$db3->fetch($i);
		$payment_config[$db3->dt[config_name]] = $db3->dt[config_value];
	}
	if($db->dt[m_useopt]=="0") $ch_m_useopt="1";
	else $ch_m_useopt="2";
	$Contents .= "
		<form name='cash_form' method='post' action='./nicepay/receiptResult.php'>
		<input type=hidden name=MERCHANTKEY value='".$payment_config["nicepay_key"]."'>
		<input type=hidden name=MID value='".$payment_config["nicepay_id"]."'>
		<input type=hidden name=ReceiptType value='".$ch_m_useopt."'>
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
} else if($_SESSION["admininfo"]["sattle_module"]=="billgate"){
	$sql = "select * from shop_payment_config
				where mall_ix = '".$admininfo[mall_ix]."' and pg_code = 'billgate' ";
	$db3->query($sql);

	for($i=0;$i < $db3->total;$i++){
		$db3->fetch($i);
		$payment_config[$db3->dt[config_name]] = $db3->dt[config_value];
	}

	
	//결제 요청 파라메터
	if( $payment_config["billgate_type"] == "test" ){
		$serviceId	= "glx_api" ; // 테스트 아이디 일반결제 : glx_api, 자동결제 : glx_at
	}
	else{
		$serviceId	= $payment_config["billgate_id"];	//"glx_api" ; // 테스트 아이디 일반결제 : glx_api, 자동결제 : glx_at
	}

	if($db->dt[m_useopt] == 0){
		$IDENTIFIER_TYPE = "20";
	}else{
		$IDENTIFIER_TYPE = "30";
	}
	$item_name = str_replace(',','',$pname);
	$item_name = substr($item_name,0,20);

$Contents .= "
			<form name='cash_form' action='../../shop/billgate/billreceipt.php' method='post'>
			<input type=hidden name=SERVICE_ID size=20 value='".$serviceId."'>
			<input type=hidden name=ORDER_DATE value='".date('YmdHis',strtotime($order_date))."'>
			<input type=hidden name=DEAL_DATE value='".date('YmdHis',strtotime($ic_date))."'>
			<input type=hidden name=USING_TYPE value='".$db->dt[m_useopt]."'>
			<input type=hidden name=DEAL_TYPE value='0'>
			<input type=hidden name=IDENTIFIER_TYPE value='".$IDENTIFIER_TYPE."'>
			<input type=hidden name=ITEM_NAME value='".$item_name."'>";
			$oid_txt="ORDER_ID";
			$bmobil_txt="IDENTIFIER";
			$supply_txt="DEAL_AMOUNT";
			$service_txt="SERVICE_CHARGE";
			$tax_txt="VAT";
			$ptprice_txt="PAYMENT_PRICE";
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
										<td class='input_box_item' colspan=3>&nbsp;<input type='text'  class=textbox name='".$bname_txt."' size='20' maxlength='20' value='".($db->dt[rname]!=""?$db->dt[rname]:$bname)."'></td><!-- 주문에 있는 주문자 이름이 아닌 현금영수증 신청할 때 입력한 신청자명으로 바꿈 (기존에는 회원명으로 신청함) kbk 13/07/13 -->

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
										<td class='input_box_item'>&nbsp;<input type='text'  class=textbox name='".$supply_txt."' size='12' maxlength='9' value='".round($price - $tax)."'></td>
									</tr>
									<tr>
										<td class='input_box_title' > 봉사료</td>
										<td class='input_box_item' >&nbsp;<input type='text'  class=textbox name='".$service_txt."' size='12' maxlength='9' value='0'></td>
									
										<td class='input_box_title' > 부가가치세</td>
										<td class='input_box_item' >&nbsp;<input type='text'  class=textbox name='".$tax_txt."' size='12' maxlength='9' value='".$tax."'></td>
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
$P->Navigation = "주문관리 > 증빙서 > 현금영수증신청";
$P->NaviTitle = "현금영수증신청";
$P->title = "현금영수증신청";
$P->strContents = $Contents;
echo $P->PrintLayOut();




