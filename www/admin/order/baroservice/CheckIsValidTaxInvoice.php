<?php
	iconv_set_encoding("internal_encoding", "utf-8");
	require_once('lib/nusoap.php');
	require_once("./GetErrString.php");

  $NowTime = date("YmdHis", time()); 	
	try {		
		$client = new nusoap_client("https://testws.baroservice.com:8010/ti.asmx?WSDL", true);
		
		$client->xml_encoding = "UTF-8";
		$client->soap_defencoding = "UTF-8";
		$client->decode_utf8 = false;

		$err = $client->getError();
		if ($err) {
			// 접속 오류에 대한 대응
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			exit();
		}

		// 보낼 argument 배열로 함.
		// array 배열 조합의 특성상 하단에서 부터 먼저 조합을 시작 한다.


		//공급자 정보
		$params_InvoicerParty = array(
			ContactID		=> "ingcream",  
			CorpNum			=> "4168138772",  
			MgtNum			=> "INTEROP" .  $NowTime,  
			CorpName		=> "(주)케이넷",  
			TaxRegID		=> "1111",  
			CEOName			=> "이천호",  
			Addr			=> "광주광역시 북구 대어쩌구..",  
			BizClass		=> "소프트웨어",  
			BizType			=> "서비스",  
			ContactName		=> "이형국",  
			TEL				=> "062-710-8285",  
			HP				=> "",  
			Email			=> "knetdev@gmail.com"
		);

		//공급받는자 정보
		$params_InvoiceeParty = array(
			ContactID		=> "",
			CorpNum			=> "1231212312",
			MgtNum			=> "",
			CorpName		=> "테스터", 
			TaxRegID		=> "",
			CEOName			=> "김테스",  
			Addr			=> "서울",  
			BizClass		=> "소프트웨어",  
			BizType			=> "서비스",  
			ContactName		=> "김씨",  
			TEL				=> "062-000-000",
			HP				=> "",
			Email			=> "pallet027@nate.com"
		);

		//위탁자 정보
		$params_BrokerParty = array(
			ContactID		=> "",
			CorpNum			=> "",
			MgtNum			=> "",
			CorpName		=> "", 
			TaxRegID		=> "",
			CEOName			=> "",  
			Addr			=> "",  
			BizClass		=> "",  
			BizType			=> "",  
			ContactName		=> "",  
			TEL				=> "",
			HP				=> "",
			Email			=> ""
		);

		//계산서 상세항목
		$params_TaxInvoiceTradeLineItem_1 = array(
			PurchaseExpiry	=> "20090723", 
			Name			=> "품목1", 
			Information		=> "비고", 
			ChargeableUnit	=> "", 
			UnitPrice		=> "", 
			Amount			=> "2500", 
			Tax				=> "250", 
			Descriptio		=> ""
		);

		$params_TaxInvoiceTradeLineItem_2 = array(
			PurchaseExpiry	=> "20090723", 
			Name			=> "품목2", 
			Information		=> "비고", 
			ChargeableUnit	=> "", 
			UnitPrice		=> "", 
			Amount			=> "2500", 
			Tax				=> "250", 
			Descriptio		=> ""
		);


		$params_TaxInvoiceTradeLineItem = array(
			0	=> $params_TaxInvoiceTradeLineItem_1,
			1	=> $params_TaxInvoiceTradeLineItem_2
		);


		$params_TaxInvoiceTradeLineItems = array(
			TaxInvoiceTradeLineItem	=> $params_TaxInvoiceTradeLineItem
		);

		$params_Invoice = array(
			InvoiceKey  		=> "KNETERP" .  $NowTime,
			InvoicerParty		=> $params_InvoicerParty,
			InvoiceeParty		=> $params_InvoiceeParty,
			BrokerParty			=> $params_BrokerParty,
			IssueDirection		=> "1",
			TaxInvoiceType		=> "1",
			TaxType				=> "1",
			TaxCalcType			=> "1",
			PurposeType			=> "1",
			ModifyCode			=> "",
			Kwon				=> "1",
			Ho					=> "1",
			SerialNum			=> "12",
			Cash				=> "",
			ChkBill				=> "",
			Note				=> "",
			Credit				=> "",
			WriteDate			=> "",
			AmountTotal			=> "5000",
			TaxTotal			=> "500",
			TotalAmount			=> "5500",
			Remark1				=> "비고1",
			Remark2				=> "비고2",
			Remark3				=> "비고3", 
			TaxInvoiceTradeLineItems => $params_TaxInvoiceTradeLineItems
		);

		$params = array(
			CERTKEY		=> "41002A15-CBDA-4C0B-96F1-B81B819BB119",
			CorpNum		=> "2141009837",
			Invoice		=> $params_Invoice
		);

				//echo '<h2>SoaP</h2><pre>'; 
				//print_r($params); 
				//echo '</pre>';

		// Soap 문장 전송		
		$result = $client->call('CheckIsValidTaxInvoice', $params, '', '', false, true);
		
		if ($client->fault) {
			// Soap 문장 오류시 예외처리
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; 
			print_r($result); echo '</pre>';
		} else {
			$err = $client->getError();
			if ($err) {
				// Soap 결과 오류시 예외처리
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				// 정상적으로 응답이 오는 경우에 대한 처리

				// 결과 문장에 대한 출력
				echo '<h2>Result</h2><pre>'; 
				print_r($result); 
				echo '</pre>';
				
				// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
				$CheckIsValidTaxInvoiceResult = $result['CheckIsValidTaxInvoiceResult'];				
				switch ($CheckIsValidTaxInvoiceResult) {
					case 1:
						echo "유효";
						break;
					default:
						$errCode = errCodeCheck($CheckIsValidTaxInvoiceResult);
					echo $errCode;
						break;
				}
			}
		}
	} catch (Exception $e) {
		echo '접속오류: ',  $e->getMessage(), "\n";
	}


?>