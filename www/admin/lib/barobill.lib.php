<?php

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/lib/nusoap.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;


//http://testbed.barobill.co.kr/dti/b_pre.asp

//------------------------------------------------------------------------------------------------
//바로빌 연동서비스 웹서비스 참조(WebService Reference) URL
$BAROSERVICE_URL = 'http://testws.baroservice.com/TI.asmx?WSDL';	//테스트베드용
//$BAROSERVICE_URL = 'http://ws.baroservice.com/TI.asmx?WSDL';		//실서비스용

/*
$CERTKEY = '41002A15-CBDA-4C0B-96F1-B81B819BB119';			//인증키
$CorpNum = '2141009837';			//연계사업자 사업자번호 ('-' 제외, 10자리)
$ID = 'forbiz';				//연계사업자 아이디
$PWD = 'vhqlwm..^&';				//연계사업자 비밀번호
*/

$sql = "
		select
			case when config_name = 'barobill_key' then config_value end as barobill_key,
			case when config_name = 'barobill_id' then config_value end as barobill_id,
			case when config_name = 'barobill_pw' then config_value end as barobill_pw
		from
			shop_payment_config
		where
			mall_ix = '".$admininfo[mall_ix]."'
			and config_name like 'barobill%'
";
$db->query($sql);
$payment_array = $db->fetchall();

if(!empty($payment_array)) {
	foreach($payment_array as $key => $value){
		foreach($value as $ky =>$val){
			if($ky == "barobill_key" && $val ){
				$barobill_key = $val;
			}elseif($ky == "barobill_id" && $val){
				$barobill_id = $val;
				//echo $ky."::::".$val."<br/>";
			}elseif($ky == "barobill_pw" && $val){
					$barobill_pw = $val;
			}
		}
	}
}

$CERTKEY = $barobill_key;
$ID = $barobill_id;		//연계사업자 아이디
$PWD = $barobill_pw;				//연계사업자 비밀번호

$db->query("SELECT com_number FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE com_type = 'A' ");
$db->fetch();
$CorpNum = str_replace('-','',$db->dt[com_number]);			//연계사업자 사업자번호 ('-' 제외, 10자리)
//$CorpNum = '2141009837';
//echo $CorpNum;
//------------------------------------------------------------------------------------------------

$BaroService_TI = new nusoap_client($BAROSERVICE_URL, true);
$BaroService_TI->xml_encoding = "UTF-8";
$BaroService_TI->soap_defencoding = "UTF-8";
$BaroService_TI->decode_utf8 = false;

if ($err) {
	// 접속 오류에 대한 대응
	echo 'SOAP통신오류<pre>'.$err.'</pre>';
	echo '<h4>Debug</h4><pre>'.htmlspecialchars($BaroService_TI->getDebug(),ENT_QUOTES).'</pre>';
	exit();
}

function checkCallErr($Result){
	global $BaroService_TI;

	if ($BaroService_TI->fault) {
		echo 'SOAP통신오류<br><br>1. 파라메터를 확인하세요.';
		return false;
	} else {
		$err = $BaroService_TI->getError();
		if ($err) {
			echo 'SOAP통신오류<br><br><pre>'.$err.'</pre>';
			return false;
		}
	}
	return true;
}

function getErrStr($CERTKEY, $ErrCode){
	global $BaroService_TI;

	$ErrStr = $BaroService_TI->call('GetErrString', array(
										'CERTKEY'		=> $CERTKEY,
										'ErrCode'		=> $ErrCode
										), '', '', false, true);
	return $ErrStr['GetErrStringResult'];
}


function BarobillState($MgtKey){
	global $BaroService_TI,$CERTKEY,$CorpNum;

	$Result = $BaroService_TI->call('GetTaxInvoiceState', array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey
				), '', '', false, true);

	if (checkCallErr($Result)) {

		$Result = $Result['GetTaxInvoiceStateResult'];

		if (is_null($Result) || $Result == ''){
			//return '문서 상태를 불러오지 못했습니다.';
			echo "<script>alert('문서 상태를 불러오지 못했습니다.');</script>";
			return false;
		}else if ($Result['BarobillState'] < 0){
			//return '오류코드 : '.$Result['BarobillState'].'<br><br>'.getErrStr($CERTKEY,$Result['BarobillState']);
			echo "<script>alert('".getErrStr($CERTKEY, $Result['BarobillState'])."');</script>";
			return false;
		}else{
			return $Result;
			/*
			return '자체문서관리번호 : '.$Result['MgtKey'].'<br>
				바로빌문서관리번호 : '.$Result['InvoiceKey'].'<br>
				바로빌상태코드 : '.$Result['BarobillState'].'<br>
				개봉여부 : '.$Result['OpenYN'].'<br>
				메모1 : '.$Result['Remark1'].'<br>
				메모2 : '.$Result['Remark2'].'<br>
				국세청전송상태 : '.$Result['NTSSendState'].'<br>
				국세청승인번호 : '.$Result['NTSSendKey'].'<br>
				국세청전송결과 : '.$Result['NTSSendResult'].'<br>
				국세청전송일시 : '.$Result['NTSSendDT'].'<br>
				전송결과수신일시 : '.$Result['NTSResultDT'];
			*/
		}
	}
}

function getTaxSalesStatus($BarobillState){
	/*
		바로빌 정발행
		1000	임시저장
		2010	발행예정_승인대기
		2011	발행예정_승인완료
		4012	발행예정_거부
		5013	발행예정_공급자취소[승인전 취소]
		5031	발행예정_공급자취소[승인후 취소]
		5031	발행완료 후 공급자에 의한 발행취소
		3014	발행완료(즉시발행/즉시전송)
		3011	발행완료(발행예정후 발행)

		바로빌 역발행
		1000	임시저장
		2020	역발행요청_발행대기
		4022	역발행요청_공급받는자 거부
		5023	역발행오청_공급받는자 취소[발행전 취소]
		5031	발행완료후 공급자에 의한 발행취소
		3021	발행완료
	*/
	if($BarobillState=="3014"||$BarobillState=="3011"||$BarobillState=="3021"){
		$status='1';//발행
	}else{
		$status='0';//임시발행
	}

	return $status;
}

function updateTaxSalesStatus($t_list){
	
	$db = new Database;
	foreach($t_list as $tl){
		$tmp_idx[]=$_SESSION[admininfo][mall_ename].$tl[idx];
	}

	$Result=BarobillStates($tmp_idx);

	if($Result){
		if (!is_null($Result['MgtKey'])){
			$status = getTaxSalesStatus($Result['BarobillState']);
			$sql="update tax_sales set status='".$status."', send_status = '".$Result['NTSSendState']."' , national_tax_no = '".$Result['NTSSendKey']."' where idx='".str_replace($_SESSION[admininfo][mall_ename],'',$Result['MgtKey'])."' ";
			$db->query($sql);
		}else{
			foreach ($Result as $es){
				$status = getTaxSalesStatus($es['BarobillState']);
				$sql="update tax_sales set status='".$status."', send_status = '".$es['NTSSendState']."' , national_tax_no = '".$es['NTSSendKey']."' where idx='".str_replace($_SESSION[admininfo][mall_ename],'',$es['MgtKey'])."' ";
				$db->query($sql);
			}
		}
	}
}

function getTaxSalesSendStatus($SendStatus){
	if($SendStatus=='1'){
		$send_status="전송전";
	}elseif($SendStatus=='2'){
		$send_status="전송대기";
	}elseif($SendStatus=='3'){
		$send_status="전송중";
	}elseif($SendStatus=='4'){
		$send_status="전송완료";
	}elseif($SendStatus=='5'){
		$send_status="전송실패";
	}else{
		$send_status="-";
	}
	return $send_status;
}

function BarobillStates($MgtKeyStr){
	global $BaroService_TI,$CERTKEY,$CorpNum;
	
	//echo $MgtKeyStr;
	$MgtKeyList = array(	//자체문서관리번호 배열
					string => $MgtKeyStr
				);
	//print_r($MgtKeyList);
	$Result = $BaroService_TI->call('GetTaxInvoiceStates', array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKeyList'	=> $MgtKeyList
				), '', '', false, true);

	if (checkCallErr($Result)) {

		$Result = $Result['GetTaxInvoiceStatesResult']['TaxInvoiceState'];

		if (is_null($Result) || $Result == ''){
			//echo '문서 상태를 불러오지 못했습니다.';
			return false;
		}else{
			return $Result;
		}
		/*else if (!is_null($Result['MgtKey'])){
			echo $Result['MgtKey'].', '.
				$Result['InvoiceKey'].', '.
				$Result['BarobillState'].', '.
				$Result['OpenYN'].', '.
				$Result['Remark1'].', '.
				$Result['Remark2'].', '.
				$Result['NTSSendState'].', '.
				$Result['NTSSendKey'].', '.
				$Result['NTSSendResult'].', '.
				$Result['NTSSendDT'].', '.
				$Result['NTSResultDT'];

		}else{
			foreach ($Result as $es){
				echo $es['MgtKey'].', '.
					$es['InvoiceKey'].', '.
					$es['BarobillState'].', '.
					$es['OpenYN'].', '.
					$es['Remark1'].', '.
					$es['Remark2'].', '.
					$es['NTSSendState'].', '.
					$es['NTSSendKey'].', '.
					$es['NTSSendResult'].', '.
					$es['NTSSendDT'].', '.
					$es['NTSResultDT'].'';
			}
		}
		*/
	}
}


function GetTaxInvoicePopUpURL($MgtKey,$view_type="tax"){
	global $BaroService_TI,$CERTKEY,$CorpNum,$ID,$PWD;
	
	$Result = $BaroService_TI->call('GetTaxInvoicePopUpURL', array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey,
				'ID'			=> $ID,
				'PWD'			=> $PWD
				), '', '', false, true);
	
	if (checkCallErr($Result)) {

		$Result = $Result['GetTaxInvoicePopUpURLResult'];

		if ($Result < 0){
			//echo "<script>alert('".getErrStr($CERTKEY, $Result)."');</script>";
			return getErrStr($CERTKEY, $Result);
		}else{
			if($view_type=="tax"){
				return "<a href=\"$Result\" target=\"_blank\">세금계산서보기</a>";
			}else{
				return "<a href=\"$Result\" target=\"_blank\">계산서보기</a>";
			}
		}
	}
}

function GetTaxinvoiceMailURL($tax_num){
	global $BaroService_TI,$CERTKEY,$CorpNum,$ID,$PWD;
	//echo $tax_num;
	
	$Result = $BaroService_TI->call('GetTaxinvoiceMailURL', array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey
				), '', '', false, true);
	
	if (checkCallErr($Result)) {

		$Result = $Result['GetTaxinvoiceMailURLResult'];

		if ($Result < 0){
			//echo "<script>alert('".getErrStr($CERTKEY, $Result)."');</script>";
			return getErrStr($CERTKEY, $Result);
		}else{
			return "<a href=\"$Result\" target=\"_blank\">$Result</a>";
		}
	}
}


function barobill_input($idx){

	global $BaroService_TI,$CERTKEY,$ID,$PWD,$CorpNum,$db;

	$sql = "select * from tax_sales WHERE idx = '".$idx."'";
	$db->query($sql);
	$db->fetch();
	$tax_info = $db->dt;

	if($tax_info[publish_type] !=3){
		$IssueDirection = $tax_info[publish_type];					//1-정발행, 2-역발행(위수탁 세금계산서는 정발행만 허용)
	}else{
		$IssueDirection = 1;
	}


	if($tax_info[publish_type] !=3){//비과세 일때 계산서로~
		$TaxInvoiceType = $tax_info[tax_type];					//1-세금계산서, 2-계산서, 4-위수탁세금계산서, 5-위수탁계산서
	}else{
		if($tax_info[tax_type]==1){
			$TaxInvoiceType = 4;
		}else{
			$TaxInvoiceType = 5;
		}
	}

	//-------------------------------------------
	//과세형태
	//-------------------------------------------
	//TaxInvoiceType 이 1,4 일 때 : 1-과세, 2-영세
	//TaxInvoiceType 이 2,5 일 때 : 3-면세
	//-------------------------------------------

	//tax_per 1:과세 2:면세
	if($tax_info[tax_per]==2){
		$TaxType = '3';
	}else{
		$TaxType = '1';
	}
	$TaxCalcType = 1;						//세율계산방법 : 1-절상, 2-절사, 3-반올림

	//$tax_info[claim_kind] 1:청구 2:영수
	if($tax_info[claim_kind]==2){
		$PurposeType = 1;						//1-영수, 2-청구
	}else{
		$PurposeType = 2;
	}

	//-------------------------------------------
	//수정사유코드
	//-------------------------------------------
	//수정세금계산서를 작성하는 경우에 사용
	//1-기재사항의 착오 정정, 2-공급가액의 변동, 3-재화의 환입, 4-계약의 해제, 5-내국신용장 사후개설, 6-착오에 의한 이중발행
	//-------------------------------------------
	//$ModifyCode = 1;

	$Kwon = '';								//별지서식 11호 상의 [권] 항목
	$Ho = '';								//별지서식 11호 상의 [호] 항목
	$SerialNum = '';				//별지서식 11호 상의 [일련번호] 항목

	//-------------------------------------------
	//공급가액 총액
	//-------------------------------------------
	$AmountTotal = $tax_info[supply_price];

	//-------------------------------------------
	//세액합계
	//-------------------------------------------
	//$TaxType 이 2 또는 3 으로 셋팅된 경우 0으로 입력
	//-------------------------------------------
	$TaxTotal = $tax_info[tax_price];

	//-------------------------------------------
	//합계금액
	//-------------------------------------------
	//공급가액 총액 + 세액합계 와 일치해야 합니다.
	//-------------------------------------------
	$TotalAmount = $tax_info[total_price];

	$Cash = '';								//현금
	$ChkBill = '';							//수표
	$Note = '';								//어음
	$Credit = '';							//외상미수금

	//$Remark1 = '비고1-1';
	//$Remark2 = '비고1-2';
	//$Remark3 = '비고1-3';

	$signdate   = explode("-", $tax_info[signdate]);
	$WriteDate = $signdate[0].$signdate[1].$signdate[2];						//작성일자 (YYYYMMDD), 공백입력 시 Today로 작성됨.

	//$NowTime = date("YmdHis", time());
	$MgtNum = $_SESSION[admininfo][mall_ename].$idx;
	
	/*
	if($IssueDirection=='1'){//IssueDirection				//1-정발행, 2-역발행(위수탁 세금계산서는 정발행만 허용)
		$InvoicerParty_MgtNum=$MgtNum;
		$InvoiceeParty_MgtNum='';
		$InvoicerParty_ContactID=$ID;
		$InvoiceeParty_ContactID='';
	}else{
		$InvoicerParty_MgtNum='';
		$InvoiceeParty_MgtNum=$MgtNum;
		$InvoicerParty_ContactID='';
		$InvoiceeParty_ContactID=$ID;
	}*/

	$s_company_number = explode("-", $tax_info[s_company_number]);
	$s_company_number = trim($s_company_number[0]).trim($s_company_number[1]).trim($s_company_number[2]);
	//-------------------------------------------
	//공급자 정보
	//------------------------------------------
	$InvoicerParty = array(
		'MgtNum' 		=> $MgtNum,						//정발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> $s_company_number,						//필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
		'TaxRegID' 		=> '',
		'CorpName' 		=> $tax_info[s_company_name],		//필수입력
		'CEOName' 		=> $tax_info[s_name],				//필수입력
		'Addr' 			=> $tax_info[s_address],
		'BizType' 		=> '',
		'BizClass' 		=> '',
		'ContactID' 	=> $ID,					//필수입력 - 담당자 바로빌 아이디 위수탁자일때 공급자는 바로빌 회원사여야함
		'ContactName' 	=> $tax_info[s_personin],				//필수입력
		'TEL' 			=> $tax_info[s_tel],
		'HP' 			=> '',
		'Email' 		=> $tax_info[s_email]		//필수입력
	);

	$r_company_number = explode("-", $tax_info[r_company_number]);
	$r_company_number = trim($r_company_number[0]).trim($r_company_number[1]).trim($r_company_number[2]);
	//-------------------------------------------
	//공급받는자 정보
	//------------------------------------------
	$InvoiceeParty = array(
		'MgtNum' 		=> $MgtNum,				//역발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> $r_company_number,			//필수입력
		'TaxRegID' 		=> '',
		'CorpName' 		=> $tax_info[r_company_name],	//필수입력
		'CEOName' 		=> $tax_info[r_name],	//필수입력
		'Addr' 			=> $tax_info[r_address],
		'BizType' 		=> '',
		'BizClass' 		=> '',
		'ContactID' 	=> $ID,						//역발행시 필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> $tax_info[r_personin],				//필수입력
		'TEL' 			=> $tax_info[r_tel],
		'HP' 			=> '',
		'Email' 		=> $tax_info[r_email]		//역발행시 필수입력
	);

/*
	if($_POST['ac_ix'] ==''){
		$sql = "select * from ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id = '".$admininfo[company_id]."' ";
		$db1->query($sql);
		$db1->fetch();
		$sc_number = explode("-", $db1->dt[com_number]);
		$sc_number = trim($sc_number[0]).trim($sc_number[1]).trim($sc_number[2]);
		//-------------------------------------------
		//수탁자 정보
		//------------------------------------------

		$BrokerParty = array(
			'MgtNum' 		=> $MgtNum,						//위수탁발행시 필수입력 - 자체문서관리번호
			'CorpNum' 		=> $CorpNum,						//위수탁발행시 필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
			'TaxRegID' 		=> '',
			'CorpName' 		=> $db1->dt[com_name],						//위수탁발행시 필수입력
			'CEOName' 		=> $db1->dt[com_ceo],						//위수탁발행시 필수입력
			'Addr' 			=> $db1->dt[com_zip]." ".$db1->dt[com_addr1]."".$db1->dt[com_addr2],
			'BizType' 		=> $db1->dt[com_business_category],
			'BizClass' 		=> $db1->dt[com_business_status],
			'ContactID' 	=> $ID,						//위수탁발행시 필수입력 - 담당자 바로빌 아이디
			'ContactName' 	=> $db1->dt[com_ceo],						//위수탁발행시 필수입력
			'TEL' 			=> $db1->dt[com_phone],
			'HP' 			=> '',
			'Email' 		=> $db1->dt[com_email]						//위수탁발행시 필수입력
		);

	}
*/

	$sql = "select * from tax_sales_detail WHERE p_idx = '".$idx."'";
	$db->query($sql);
	$tax_sales_dt = $db->fetchall();
	//-------------------------------------------
	//품목
	//-------------------------------------------
	for($i=0;$i < count($tax_sales_dt); $i++){
		$TaxInvoiceTradeLineItems['TaxInvoiceTradeLineItem'][$i]=array("PurchaseExpiry"=>'', "Name"=>$tax_sales_dt[$i][product], "Information"=>'', "ChargeableUnit"=>'', "UnitPrice"=>'', "Amount"=>$tax_sales_dt[$i][p_price], "Tax"=>$tax_sales_dt[$i][tax], "Description"=>'');
	}


	//-------------------------------------------
	//전자세금계산서
	//-------------------------------------------
	$TaxInvoice = array(
		'InvoiceKey'				=> '',
		'InvoiceeASPEmail'			=> '',
		'IssueDirection'			=> $IssueDirection,
		'TaxInvoiceType'			=> $TaxInvoiceType,
		'TaxType'					=> $TaxType,
		'TaxCalcType'				=> $TaxCalcType,
		'PurposeType'				=> $PurposeType,
		'ModifyCode'				=> $ModifyCode,
		'Kwon'						=> $Kwon,
		'Ho'						=> $Ho,
		'SerialNum'					=> $SerialNum,
		'Cash'						=> $Cash,
		'ChkBill'					=> $ChkBill,
		'Note'						=> $Note,
		'Credit'					=> $Credit,
		'WriteDate'					=> $WriteDate,
		'AmountTotal'				=> $AmountTotal,
		'TaxTotal'					=> $TaxTotal,
		'TotalAmount'				=> $TotalAmount,
		'Remark1'					=> $Remark1,
		'Remark2'					=> $Remark2,
		'Remark3'					=> $Remark3,
		'InvoicerParty'				=> $InvoicerParty,
		'InvoiceeParty'				=> $InvoiceeParty,
		'BrokerParty'				=> $BrokerParty,
		'TaxInvoiceTradeLineItems'	=> $TaxInvoiceTradeLineItems
	);

	//$IssueDirection				//1-정발행, 2-역발행(위수탁 세금계산서는 정발행만 허용)
	//TaxInvoiceType				//1-세금계산서, 2-계산서, 4-위수탁세금계산서, 5-위수탁계산서
	if($IssueDirection ==1 && ($TaxInvoiceType ==1 || $TaxInvoiceType ==2) ){
		//정발행
		$Result = $BaroService_TI->call('RegistTaxInvoice', array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['InvoicerParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				), '', '', false, true);

		$tax_type =1;
	}elseif($IssueDirection == 2 ){
		//역발행
		$Result = $BaroService_TI->call('RegistTaxInvoiceReverse', array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['InvoiceeParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				), '', '', false, true);
		$tax_type =2;
	}elseif($IssueDirection ==1 && ($TaxInvoiceType ==4 || $TaxInvoiceType ==5)){
		//위수탁
		$Result = $BaroService_TI->call('RegistBrokerTaxInvoice', array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['BrokerParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				), '', '', false, true);
		$tax_type =3;
	}

	//echo $Result."<br/>";
	//print_r($TaxInvoice['InvoicerParty']['CorpNum']);
	if (checkCallErr($Result)) {
		if($tax_type ==1){
			$Result = $Result['RegistTaxInvoiceResult'];	//정발행
		}elseif($tax_type ==2){
			$Result = $Result['RegistTaxInvoiceReverseResult'];	//역발행
		}elseif($tax_type ==3){
			$Result = $Result['RegistBrokerTaxInvoiceResult'];	//위수탁
		}

		if ($Result < 0){
			echo "<script>alert('".getErrStr($CERTKEY, $Result)."');</script>";
			return false;
			//echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
		}else{
			$db->query("update tax_sales set status='2', re_signdate = NOW() where idx='".$idx."' ");
			return true;
		}
	}
}

function barobill_update($idx,$oid=""){

	global $BaroService_TI,$CERTKEY,$ID,$PWD,$CorpNum,$db;

	$sql = "select * from tax_sales WHERE idx = '".$idx."'";
	$db->query($sql);
	$db->fetch();
	$tax_info = $db->dt;

	if($tax_info[publish_type] !=3){
		$IssueDirection = $tax_info[publish_type];					//1-정발행, 2-역발행(위수탁 세금계산서는 정발행만 허용)
	}else{
		$IssueDirection = 1;
	}


	if($tax_info[publish_type] !=3){//비과세 일때 계산서로~
		$TaxInvoiceType = $tax_info[tax_type];					//1-세금계산서, 2-계산서, 4-위수탁세금계산서, 5-위수탁계산서
	}else{
		if($tax_info[tax_type]==1){
			$TaxInvoiceType = 4;
		}else{
			$TaxInvoiceType = 5;
		}
	}

	//-------------------------------------------
	//과세형태
	//-------------------------------------------
	//TaxInvoiceType 이 1,4 일 때 : 1-과세, 2-영세
	//TaxInvoiceType 이 2,5 일 때 : 3-면세
	//-------------------------------------------

	//tax_per 1:과세 2:면세
	if($tax_info[tax_per]==2){
		$TaxType = '3';
	}else{
		$TaxType = '1';
	}
	$TaxCalcType = 1;						//세율계산방법 : 1-절상, 2-절사, 3-반올림

	//$TaxType[claim_kind] 1:청구 2:영수
	if($TaxType[claim_kind]==2){
		$PurposeType = 1;						//1-영수, 2-청구
	}else{
		$PurposeType = 2;
	}

	//-------------------------------------------
	//수정사유코드
	//-------------------------------------------
	//수정세금계산서를 작성하는 경우에 사용
	//1-기재사항의 착오 정정, 2-공급가액의 변동, 3-재화의 환입, 4-계약의 해제, 5-내국신용장 사후개설, 6-착오에 의한 이중발행
	//-------------------------------------------
	$ModifyCode = 3;

	$Kwon = '';								//별지서식 11호 상의 [권] 항목
	$Ho = '';								//별지서식 11호 상의 [호] 항목
	$SerialNum = '';				//별지서식 11호 상의 [일련번호] 항목

	//-------------------------------------------
	//공급가액 총액
	//-------------------------------------------
	$AmountTotal = $tax_info[supply_price];

	//-------------------------------------------
	//세액합계
	//-------------------------------------------
	//$TaxType 이 2 또는 3 으로 셋팅된 경우 0으로 입력
	//-------------------------------------------
	$TaxTotal = $tax_info[tax_price];

	//-------------------------------------------
	//합계금액
	//-------------------------------------------
	//공급가액 총액 + 세액합계 와 일치해야 합니다.
	//-------------------------------------------
	$TotalAmount = $tax_info[total_price];

	$Cash = '';								//현금
	$ChkBill = '';							//수표
	$Note = '';								//어음
	$Credit = '';							//외상미수금

	//$Remark1 = '비고1-1';
	//$Remark2 = '비고1-2';
	//$Remark3 = '비고1-3';

	$signdate   = explode("-", $tax_info[signdate]);
	$WriteDate = $signdate[0].$signdate[1].$signdate[2];						//작성일자 (YYYYMMDD), 공백입력 시 Today로 작성됨.

	//$NowTime = date("YmdHis", time());
	$MgtNum = $_SESSION[admininfo][mall_ename].$idx;
	
	/*
	if($IssueDirection=='1'){//IssueDirection				//1-정발행, 2-역발행(위수탁 세금계산서는 정발행만 허용)
		$InvoicerParty_MgtNum=$MgtNum;
		$InvoiceeParty_MgtNum='';
		$InvoicerParty_ContactID=$ID;
		$InvoiceeParty_ContactID='';
	}else{
		$InvoicerParty_MgtNum='';
		$InvoiceeParty_MgtNum=$MgtNum;
		$InvoicerParty_ContactID='';
		$InvoiceeParty_ContactID=$ID;
	}*/

	$s_company_number = explode("-", $tax_info[s_company_number]);
	$s_company_number = trim($s_company_number[0]).trim($s_company_number[1]).trim($s_company_number[2]);
	//-------------------------------------------
	//공급자 정보
	//------------------------------------------
	$InvoicerParty = array(
		'MgtNum' 		=> $MgtNum,						//정발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> $s_company_number,						//필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
		'TaxRegID' 		=> '',
		'CorpName' 		=> $tax_info[s_company_name],		//필수입력
		'CEOName' 		=> $tax_info[s_name],				//필수입력
		'Addr' 			=> $tax_info[s_address],
		'BizType' 		=> '',
		'BizClass' 		=> '',
		'ContactID' 	=> $ID,					//필수입력 - 담당자 바로빌 아이디 위수탁자일때 공급자는 바로빌 회원사여야함
		'ContactName' 	=> $tax_info[s_personin],				//필수입력
		'TEL' 			=> $tax_info[s_tel],
		'HP' 			=> '',
		'Email' 		=> $tax_info[s_email]		//필수입력
	);



	$r_company_number = explode("-", $tax_info[r_company_number]);
	$r_company_number = trim($r_company_number[0]).trim($r_company_number[1]).trim($r_company_number[2]);
	//-------------------------------------------
	//공급받는자 정보
	//------------------------------------------
	$InvoiceeParty = array(
		'MgtNum' 		=> $MgtNum,				//역발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> $r_company_number,			//필수입력
		'TaxRegID' 		=> '',
		'CorpName' 		=> $tax_info[r_company_name],	//필수입력
		'CEOName' 		=> $tax_info[r_name],	//필수입력
		'Addr' 			=> $tax_info[r_address],
		'BizType' 		=> '',
		'BizClass' 		=> '',
		'ContactID' 	=> $ID,						//역발행시 필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> $tax_info[r_personin],				//필수입력
		'TEL' 			=> $tax_info[r_tel],
		'HP' 			=> '',
		'Email' 		=> $tax_info[r_email]		//역발행시 필수입력
	);

/*
	if($_POST['ac_ix'] ==''){
		$sql = "select * from ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id = '".$admininfo[company_id]."' ";
		$db1->query($sql);
		$db1->fetch();
		$sc_number = explode("-", $db1->dt[com_number]);
		$sc_number = trim($sc_number[0]).trim($sc_number[1]).trim($sc_number[2]);
		//-------------------------------------------
		//수탁자 정보
		//------------------------------------------

		$BrokerParty = array(
			'MgtNum' 		=> $MgtNum,						//위수탁발행시 필수입력 - 자체문서관리번호
			'CorpNum' 		=> $CorpNum,						//위수탁발행시 필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
			'TaxRegID' 		=> '',
			'CorpName' 		=> $db1->dt[com_name],						//위수탁발행시 필수입력
			'CEOName' 		=> $db1->dt[com_ceo],						//위수탁발행시 필수입력
			'Addr' 			=> $db1->dt[com_zip]." ".$db1->dt[com_addr1]."".$db1->dt[com_addr2],
			'BizType' 		=> $db1->dt[com_business_category],
			'BizClass' 		=> $db1->dt[com_business_status],
			'ContactID' 	=> $ID,						//위수탁발행시 필수입력 - 담당자 바로빌 아이디
			'ContactName' 	=> $db1->dt[com_ceo],						//위수탁발행시 필수입력
			'TEL' 			=> $db1->dt[com_phone],
			'HP' 			=> '',
			'Email' 		=> $db1->dt[com_email]						//위수탁발행시 필수입력
		);

	}
*/

	$sql = "select * from tax_sales_detail WHERE p_idx = '".$idx."'";
	$db->query($sql);
	$tax_sales_dt = $db->fetchall();
	//-------------------------------------------
	//품목
	//-------------------------------------------
	for($i=0;$i < count($tax_sales_dt); $i++){
		$TaxInvoiceTradeLineItems['TaxInvoiceTradeLineItem'][$i]=array("PurchaseExpiry"=>'', "Name"=>$tax_sales_dt[$i][product], "Information"=>'', "ChargeableUnit"=>'', "UnitPrice"=>'', "Amount"=>$tax_sales_dt[$i][p_price], "Tax"=>$tax_sales_dt[$i][tax], "Description"=>'');
	}


	//-------------------------------------------
	//전자세금계산서
	//-------------------------------------------
	$TaxInvoice = array(
		'InvoiceKey'				=> '',
		'InvoiceeASPEmail'			=> '',
		'IssueDirection'			=> $IssueDirection,
		'TaxInvoiceType'			=> $TaxInvoiceType,
		'TaxType'					=> $TaxType,
		'TaxCalcType'				=> $TaxCalcType,
		'PurposeType'				=> $PurposeType,
		'ModifyCode'				=> $ModifyCode,
		'Kwon'						=> $Kwon,
		'Ho'						=> $Ho,
		'SerialNum'					=> $SerialNum,
		'Cash'						=> $Cash,
		'ChkBill'					=> $ChkBill,
		'Note'						=> $Note,
		'Credit'					=> $Credit,
		'WriteDate'					=> $WriteDate,
		'AmountTotal'				=> $AmountTotal,
		'TaxTotal'					=> $TaxTotal,
		'TotalAmount'				=> $TotalAmount,
		'Remark1'					=> $Remark1,
		'Remark2'					=> $Remark2,
		'Remark3'					=> $Remark3,
		'InvoicerParty'				=> $InvoicerParty,
		'InvoiceeParty'				=> $InvoiceeParty,
		'BrokerParty'				=> $BrokerParty,
		'TaxInvoiceTradeLineItems'	=> $TaxInvoiceTradeLineItems
	);

	//$IssueDirection				//1-정발행, 2-역발행(위수탁 세금계산서는 정발행만 허용)
	//TaxInvoiceType				//1-세금계산서, 2-계산서, 4-위수탁세금계산서, 5-위수탁계산서
	if($IssueDirection ==1 && ($TaxInvoiceType ==1 || $TaxInvoiceType ==2) ){
		//정발행
		$Result = $BaroService_TI->call('UpdateTaxInvoice', array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['InvoicerParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				), '', '', false, true);

		$tax_type =1;
	}elseif($IssueDirection == 2 ){
		//역발행
		$Result = $BaroService_TI->call('UpdateTaxInvoice', array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['InvoiceeParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				), '', '', false, true);
		$tax_type =2;
	}elseif($IssueDirection ==1 && ($TaxInvoiceType ==4 || $TaxInvoiceType ==5)){
		//위수탁
		$Result = $BaroService_TI->call('UpdateBrokerTaxInvoice', array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['BrokerParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				), '', '', false, true);
		$tax_type =3;
	}

	//echo $Result."<br/>";
	//print_r($TaxInvoice['InvoicerParty']['CorpNum']);
	if (checkCallErr($Result)) {
		if($tax_type ==1){
			$Result = $Result['UpdateTaxInvoiceResult'];	//정발행
		}elseif($tax_type ==2){
			$Result = $Result['UpdateTaxInvoiceResult'];	//역발행
		}elseif($tax_type ==3){
			$Result = $Result['UpdateBrokerTaxInvoiceResult'];	//위수탁
		}

		if ($Result < 0){
			echo "<script>alert('".getErrStr($CERTKEY, $Result)."');</script>";
			//echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
			return false;
		}else{
			$db->query("update tax_sales set status='2', re_signdate = NOW() ,oid='".$oid."' where idx='".$idx."' ");
			return true;
		}
	}
}

function insert_tax_sales($info,$buyer,$seller,$expect,$real,$product){

	$tdb = new Database;
	
	//publish_type 1,매출 2,매입 3,위수탁
	//tax_div 구분 1:구매자,2:판매자
	//tax_type 1.세금계산서 2.계산서
	//tax_per 1:과세 2:면세

	
	if($seller[com_number]==""){
		echo "<script>alert('공급자받는자의 사업자번호가 없습니다.');</script>";
		return false;
	}

	if($seller[com_name]==""){
		echo "<script>alert('공급자받는자의 업체명이 없습니다.');</script>";
		return false;
	}

	$SQL = "
		INSERT INTO
			tax_sales
		SET
			publish_type		='".$info[publish_type]."',
			tax_div	='".$info[tax_div]."',
			tax_type			='".$info[tax_type]."',
			numbering_k	='',
			numbering_h	='',
			numbering	='',
			s_company_number	='".$buyer[com_number]."',
			s_company_j	='',
			s_company_name		='".$buyer[com_name]."',
			s_name				='".$buyer[com_ceo]."',
			s_address			='".$buyer[com_addr1]." ".$buyer[com_addr2]."',
			s_state				='".$buyer[com_business_status]."',
			s_items				='".$buyer[com_business_category]."',
			s_personin			='".$buyer[tax_person_name]."',
			s_tel	='',
			s_email				='".$buyer[tax_person_mail]."',
			r_company_number	='".$seller[com_number]."',
			r_company_j			='',
			r_company_name		='".$seller[com_name]."',
			r_name				='".$seller[com_ceo]."',
			r_address			='".$seller[com_addr1]." ".$seller[com_addr2]."',
			r_state				='".$seller[com_business_status]."',
			r_items				='".$seller[com_business_category]."',
			r_personin			='".$seller[tax_person_name]."',
			r_tel				='',
			r_email				='".$seller[tax_person_mail]."',
			company_j			='',
			tax_per				='".$info[tax_per]."',
			marking				='',
			expect_supply_price		='".$expect[supply_price]."',
			expect_tax_price			='".$expect[tax_price]."',
			expect_total_price			='".$expect[total_price]."',
			supply_price		='".$real[supply_price]."',
			tax_price			='".$real[tax_price]."',
			total_price			='".$real[total_price]."',
			cash				='',
			cheque				='',
			pro_note			='',
			outstanding			='',
			claim_kind			='2',";
			//claim_kind 1:청구 2:영수
			if($seller[apply_date]){
				$SQL .= "apply_date		='".$seller[apply_date]."',";
			}
			$SQL .= "
			signdate			=now(),
			re_signdate			='',
			send_type			='1',
			sms_chk				='',
			sms_number			='',
			fax_chk				='',
			fax_number			='',
			memo				='',
			status				='0',
			send_status		='1',
			code					='".$seller[code]."',
			company_id		='".$seller[company_id]."',
			ac_ix					='".$seller[ac_ix]."'";
			if($info[oid]){
				$SQL .= ",oid		='".$info[oid]."'";
			}
		
	$tdb->query($SQL);

	$SQL_C = "SELECT idx FROM tax_sales ORDER BY idx DESC LIMIT 1";
	$tdb->query($SQL_C);
	$tdb->fetch();
	$p_idx = $tdb->dt[idx];

	$SQL_2 = "
	INSERT INTO
		tax_sales_detail
	SET
		p_idx = '".$p_idx."',
		t_mon = '".$product[mon]."',
		t_day = '".$product[day]."',
		product = '".$product[product]."',
		p_size = '',
		cnt = '',
		price = '',
		p_price = '".$real[supply_price]."',
		tax = '".$real[tax_price]."',
		comment = '',
		signdate = now()
	";
	$tdb->query($SQL_2);

	return $p_idx;
}

?>
