<?php
require_once('../lib/nusoap.php');
//http://testbed.barobill.co.kr/dti/b_pre.asp

//------------------------------------------------------------------------------------------------
//바로빌 연동서비스 웹서비스 참조(WebService Reference) URL	
$BAROSERVICE_URL = 'http://testws.baroservice.com/TI.asmx?WSDL';	//테스트베드용
//$BAROSERVICE_URL = 'http://ws.baroservice.com/TI.asmx?WSDL';		//실서비스용


$CERTKEY = '41002A15-CBDA-4C0B-96F1-B81B819BB119';			//인증키
$CorpNum = '2141009837';			//연계사업자 사업자번호 ('-' 제외, 10자리)
$ID = 'forbiz';				//연계사업자 아이디
$PWD = 'vhqlwm..^&';				//연계사업자 비밀번호
//------------------------------------------------------------------------------------------------
$BaroService_TI = new nusoap_client($BAROSERVICE_URL, array(
						'trace'		=> 'true',
						'encoding'	=> 'UTF-8' //소스를 ANSI로 사용할 경우 euc-kr로 수정
					));
/*
	$Result = $BaroService_TI->DeleteTaxInvoice(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> 'S20120830161309'
				))->DeleteTaxInvoiceResult;

echo$Result;
*/
function getErrStr($CERTKEY, $ErrCode){
	global $BaroService_TI;

	$ErrStr = $BaroService_TI->GetErrString(array(
										'CERTKEY'		=> $CERTKEY,
										'ErrCode'		=> $ErrCode
										))->GetErrStringResult;
	return $ErrStr;
}

function IsOpened($tax_num){//개봉여부
	global $BaroService_TI,$CERTKEY,$CorpNum;

	$Result = $BaroService_TI->GetTaxInvoiceState(array(
	'CERTKEY'		=> $CERTKEY,
	'CorpNum'		=> $CorpNum,
	'MgtKey'		=> $tax_num
	))->GetTaxInvoiceStateResult;

	if (is_null($Result)){
		return '접속오류';
	}else if ($Result->IsOpened < 0){
		return '오류';
	}else{
		return $Result->IsOpened;
	}
}

function BarobillState($tax_num){
	global $BaroService_TI,$CERTKEY,$CorpNum;

	$Result = $BaroService_TI->GetTaxInvoiceState(array(
	'CERTKEY'		=> $CERTKEY,
	'CorpNum'		=> $CorpNum,
	'MgtKey'		=> $tax_num
	))->GetTaxInvoiceStateResult;

	if(is_null($Result)){
		return '접속오류';
	}else if ($Result->BarobillState < 0){
		return '오류';
	}else{
		$BarobillState=$Result->BarobillState;
		if($BarobillState=='5013' || $BarobillState=='5031' || $BarobillState=='5023' || $BarobillState=='5031' || $BarobillState=='5041'){
			$state = 4;
		}elseif($BarobillState=='4012' || $BarobillState=='4022'){
			$state = 3;
		}else{
			$state = 1;
		}
		return $state;
	}
}

function NTSSendState($tax_num){
	global $BaroService_TI,$CERTKEY,$CorpNum;

	$Result = $BaroService_TI->GetTaxInvoiceState(array(
	'CERTKEY'		=> $CERTKEY,
	'CorpNum'		=> $CorpNum,
	'MgtKey'		=> $tax_num
	))->GetTaxInvoiceStateResult;

	if (is_null($Result)){
		return '접속오류';
	}else if ($Result->NTSSendState < 0){
		return '오류';
	}else{
		return $Result->NTSSendState;
	}
}


function GetTaxinvoiceMailURL($tax_num){
	global $BaroService_TI,$CERTKEY,$CorpNum;
	//echo $tax_num;

	$Result = $BaroService_TI->GetTaxinvoiceMailURL(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $tax_num
				))->GetTaxinvoiceMailURLResult;
	
	if ($Result < 0){
		//return $MgtKey;
		//return "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
		return "<a href=\"javascript:alert('".getErrStr($CERTKEY, $Result)."');\">";
	}else{
		return "<a href=\"$Result\" target=\"_blank\">";
	}
}

?>
