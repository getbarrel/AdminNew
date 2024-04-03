<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
require_once("./GetErrString.php"); // 에러메세지 처리파일
if (!isset($_SESSION)) session_start();

		$client = new nusoap_client("https://testws.baroservice.com:8010/ti.asmx?WSDL", true);
		
		$client->xml_encoding = "UTF-8";
		$client->soap_defencoding = "UTF-8";
		$client->decode_utf8 = false;

$db = new Database;
$db2 = new Database;
$db3 = new Database;
$db4 = new Database;

	$CERTKEY = "41002A15-CBDA-4C0B-96F1-B81B819BB119";
	$CorpNum = "2141009837";
	$baroID = "forbiz";
	$baroPWD = "vhqlwm..^&";

if($act == "delete"){
	$status = "발행예정내역 삭제";
	$status_message = "미발급으로 변경";
	$db->query("update shop_order set taxsheet_yn = 'N' where oid = '$oid' ");

	$sql = "insert into shop_taxbill_status (oid, mgtnum, status, status_message,  company_id, quick, number, regdate) 
	values ('".$oid."','".$MgtKey."','".$status."','".$status_message."','".$admininfo[company_id]."','".$quick."','0',now()) ";
	$db->query($sql);
	echo "<script>alert('세금계산서 발급상태가 미발급으로 변경 되었습니다.');parent.document.location.reload()</script>";
}

if($act == "printpop"){
	$status = "인쇄"; //액션명칭
		// 보낼 argument 배열로 함.
		//ProcType 타입은 테이블 참고
		$params = array(
			CERTKEY  		=> $CERTKEY,
			CorpNum			=> $CorpNum,
			MgtKey			=> $MgtKey,
			ID				=> $baroID,
			PWD				=> $baroPWD
			
		);

		// Soap 문장 전송
		if($s == "a") {
			$result = $client->call('GetTaxInvoicePrintURL', $params, '', '', false, true);
			$popupUrl = $result['GetTaxInvoicePrintURLResult'];
		} else if($s == "f") {
			$result = $client->call('GetTaxInvoicePopUpURL', $params, '', '', false, true);
			$popupUrl = $result['GetTaxInvoicePopUpURLResult'];
		}

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
				//echo '<h2>Result</h2><pre>'; 
				//print_r($result); 
				//echo '</pre>';
				
				// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
				if( $popupUrl >-1) {
					echo "<script>window.open('".$popupUrl."','taxbill','top=30,left=100,width=780,height=800');</script>";
					//echo "URL : ". $popupUrl;
				} else {
					$errCode = errCodeCheck($popupUrl);
						echo $status_message = $errCode;
						$sql = "insert into shop_taxbill_status (oid, mgtnum, status, status_message,  company_id, quick, number, regdate) 
						values ('".$oid."','".$MgtKey."','".$status."','".$status_message."','".$admininfo[company_id]."','".$quick."','0',now()) ";
						$db->query($sql);
						echo "<script>alert('".$status_message."');return false;</script>";
						exit;
				}
				
			}
		}
}

if($act == "sendtonts"){
	$status = "국세청전송"; //액션명칭
//exit($act);
	$status_message = "국세청 전송이 실패하였습니다.";
	try {
		if($MgtKey){
			// 보낼 argument 배열로 함.
			//ProcType 타입은 테이블 참고
			$params = array(
				CERTKEY		=> $CERTKEY,
				CorpNum		=> $CorpNum,
				MgtKey		=> $MgtKey
				
			);

			// Soap 문장 전송
			if($s == "f"){ //강제전송
				$result = $client->call('ForceSendToNTS', $params, '', '', false, true);

				if ($client->fault) {
					// Soap 문장 오류시 예외처리
					//echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
					//print_r($result); echo '</pre>';
				} else {
					$err = $client->getError();
					if ($err) {
						// Soap 결과 오류시 예외처리
						$status_message = '<h2>Error</h2><pre>' . $err . '</pre>';
					} else {
						//echo '<h2>Result</h2><pre>';
						//print_r($result);
						//echo '</pre>';
						// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
						$ForceSendToNTSResult = $result['ForceSendToNTSResult'];
						if( $ForceSendToNTSResult == 1) {
							$status_message = "국세청 즉시 신고(강제) 요청 완료";
							$db->query("update shop_taxbill set tax_sendnts = 'Y',tax_senddate = NOW() where tax_no = '$MgtKey' ");
						} else {
							$errCode = errCodeCheck($ForceSendToNTSResult);
								$status_message = $errCode;
						}
					}
				}
			} else if($s == "a") { //승인된 상태만전송
				$result = $client->call('SendToNTS', $params, '', '', false, true);

				if ($client->fault) {
					// Soap 문장 오류시 예외처리
					//echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
					//print_r($result); echo '</pre>';
				} else {
					$err = $client->getError();
					if ($err) {
						// Soap 결과 오류시 예외처리
						$status_message = '<h2>Error</h2><pre>' . $err . '</pre>';
					} else {
						//echo '<h2>Result</h2><pre>';
						//print_r($result);
						//echo '</pre>';
						// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
						$SendToNTSResult = $result['SendToNTSResult'];
						if( $SendToNTSResult == 1) {
							$status_message = "국세청 즉시 신고 요청 완료";
							$db->query("update shop_taxbill set tax_sendnts = 'Y',tax_senddate = NOW() where tax_no = '$MgtKey' ");
						} else {
							$errCode = errCodeCheck($SendToNTSResult);
								$status_message = $errCode;
						}
					}
				}
			}
		} else {
			echo "<script>alert('정상적이지 않습니다.');document.location = 'taxissue_list.php'</script>";
			exit;
		}

	} catch (Exception $e) {
		echo "<script>alert('국세청 전송이 실패하였습니다.');document.location = 'taxissue_list.php'</script>";
		exit;
	}
	$sql = "insert into shop_taxbill_status (oid, mgtnum, status, status_message,  company_id, quick, number, regdate) 
	values ('".$oid."','".$MgtKey."','".$status."','".$status_message."','".$admininfo[company_id]."','".$quick."','0',now()) ";
	$db->query($sql);
	echo "<script>alert('".$status_message."');document.location = 'taxissue_list.php'</script>";
	exit;
}

if($act == "select_sendtonts"){
	$status = "국세청 일괄전송"; //액션명칭
//exit($act);
	$status_message = "국세청 전송이 실패하였습니다.";
	try {
		if(count($tax_no) < 1){
			echo "<script>alert('체크박스를 체크하시고 진행해 주시기 바랍니다.')</script>";
			exit;
		}
		

			for($i=0;$i<count($tax_no);$i++){
				$MgtKey = $tax_no[$i];
				// 보낼 argument 배열로 함.
				//ProcType 타입은 테이블 참고
				$params = array(
					CERTKEY		=> $CERTKEY,
					CorpNum		=> $CorpNum,
					MgtKey		=> $MgtKey
					
				);

				// Soap 문장 전송
				$result = $client->call('ForceSendToNTS', $params, '', '', false, true);

				if ($client->fault) {
					// Soap 문장 오류시 예외처리
					//echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
					//print_r($result); echo '</pre>';
				} else {
					$err = $client->getError();
					if ($err) {
						// Soap 결과 오류시 예외처리
						$status_message = '<h2>Error</h2><pre>' . $err . '</pre>';
					} else {
						//echo '<h2>Result</h2><pre>';
						//print_r($result);
						//echo '</pre>';
						// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
						$ForceSendToNTSResult = $result['ForceSendToNTSResult'];
						if( $ForceSendToNTSResult == 1) {
							$status_message = "국세청 즉시 신고(강제) 요청 완료";
							$db->query("update shop_taxbill set tax_sendnts = '4',tax_senddate = NOW() where tax_no = '$MgtKey' ");
						} else {
							$errCode = errCodeCheck($ForceSendToNTSResult);
								$status_message = $errCode;
						}
					}
				}
				$sql = "insert into shop_taxbill_status (oid, mgtnum, status, status_message,  company_id, quick, number, regdate) 
				values ('".$oid."','".$MgtKey."','".$status."','".$status_message."','".$admininfo[company_id]."','".$quick."','0',now()) ";
				$db->query($sql);
			}

	} catch (Exception $e) {
		echo "<script>alert('국세청 전송이 실패하였습니다.');document.location = 'taxissue_list.php'</script>";
		exit;
	}
	echo "<script>alert('".$status_message."');document.location = 'taxissue_list.php'</script>";
	exit;
}

if($act == "input"){

	$sql = "select * from ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id = '".$admininfo[company_id]."' ";
	$db2->query($sql);
	$db2->fetch();
	$com_number = explode("-", $db2->dt[com_number]);
	$com_number = trim($com_number[0]).trim($com_number[1]).trim($com_number[2]);
	$status_message = "";
	$status_message2 = "";
	$NowTime = date("YmdHis", time()); 
	$MgtNum = "FORBIZ" .  $NowTime;
	
	//공급자 정보
	$params_InvoicerParty = array(
		ContactID		=> "FORBIZ",  
		CorpNum			=> $com_number,  
		MgtNum			=> $MgtNum,  
		CorpName		=> $db2->dt[com_name],  
		TaxRegID		=> "",  
		CEOName			=> $db2->dt[com_ceo],  
		Addr			=> $db2->dt[com_addr1]." ".$db2->dt[com_addr2],
		BizClass		=>	$db2->dt[com_business_category],  
		BizType			=> $db2->dt[com_business_status],  
		ContactName		=> $db2->dt[com_ceo],  
		TEL				=> $db2->dt[com_phone],  
		HP				=> $db2->dt[com_phone],  
		Email			=> $db2->dt[com_email]
	);


	for($j=0;$j < count($td_cost);$j++){
		$taxt_regdate = date("Y").$conmonth[$j].$cinday[$j];
		if($td_cost[$j]){
			//계산서 상세항목
			$params_TaxInvoiceTradeLineItem[$j] = array(
				PurchaseExpiry			=> $taxt_regdate, 
				Name					=> $pname[$j], 
				Information				=> $tax_note[$j], 
				ChargeableUnit			=> $pcnt[$j], 
				UnitPrice				=> $td_cost[$j], 
				Amount					=> $ptprice[$j], 
				Tax						=> $td_tax[$j], 
				Descriptio				=> $td_note[$j]
			);
			
			if($j > 0) $gubun = ";";
			else $gubun = "";
			
			$sql_detail .= $gubun." insert into shop_taxbill_detail (tax_orderno, td_date, td_name, td_amount, td_cost, td_price, td_tax, td_note, td_tax_states, td_issue_states, td_order_states)
			values ('', '".$taxt_regdate."', '".addslashes($pname[$j])."', '".$pcnt[$j]."', '".$td_cost[$j]."', '".$ptprice[$j]."', '".$td_tax[$j]."', '".$td_note[$j]."', 'Y', 'Y', 'Y' )";
		}
	}
		//exit($sql_detail);
	
	$com_number = explode("-", $com_number);
	$com_number = $com_number[0].$com_number[1].$com_number[2];

		$sql_taxbill = "insert into shop_taxbill (	tax_orderno, tax_no, company_id, tax_comnumber, tax_comname, tax_name, tax_comaddr, tax_business_status, tax_business_category, taxt_regdate, tax_price, tax_prce_tax, tax_note, taxt_price_sum, tax_cash, tax_cheque, tax_promissnote, tax_credit, tax_charge_name, tax_charge_email, tax_kind, tax_deposit_states, tax_deposit_date, tax_issue_states, tax_sendnts)
		values ('', '".$MgtNum."', '', '".$com_number."', '".$com_name."', '".$com_ceo."', '".$com_addr."', '".$com_business_status."', '".$com_business_category."', '".$taxt_regdate."', '".$tax_price."', 
		'".$tax_prce_tax."', '".$tax_note."', '".$totalprice."', '".$Cash."', '".$ChkBill."', '".$Note."', '".$Credit."', '".$tax_charge_name."', '".$tax_charge_email."', 'Y', 'Y', '".date("Y-m-d")."', 
		'0' , '1')";
		
//					exit($sql_taxbill);


	//공급받는자 정보
	$params_InvoiceeParty = array(
		ContactID		=> "",
		CorpNum			=> $com_number,
		MgtNum			=> "",
		CorpName		=> $com_name, 
		TaxRegID		=> "",
		CEOName			=> $com_ceo,  
		Addr			=> $com_addr,  
		BizClass		=> $com_business_status,  //업종
		BizType			=> $com_business_category,  //업태
		ContactName		=> $tax_charge_name,   
		TEL				=> $TEL,
		HP				=> $HP,
		Email			=> $tax_charge_email
	);
//echo '<pre>';
//print_r($params_InvoiceeParty); echo '</pre>';

	$params_TaxInvoiceTradeLineItems = array(
		TaxInvoiceTradeLineItem	=> $params_TaxInvoiceTradeLineItem
	);

	$params_Invoice = array(
		InvoiceKey  			=> "",
		InvoicerParty			=> $params_InvoicerParty,
		InvoiceeParty			=> $params_InvoiceeParty,
		InvoiceeASPEmail		=> "nts@barobill.co.kr",
		BrokerParty				=> null,
		IssueDirection			=> "1", //1 정발핼 2 역발행
		TaxInvoiceType			=> "1", //1 세금계산서 2 계산서 3 위수탁세금계산서 4 위수탁계산서
		TaxType					=> "1", //1 과세 2 영세 3 면세
		TaxCalcType				=> "1", //1 절상 2 절사 3 반올림
		PurposeType				=> $PurposeType, //1 영수 2 청구
		ModifyCode				=> "", //1 기제사항의 착오/정정 2 공급가액의 변동 3 환입 4 계약의 혜지 5 내국신용장 사후개설 (수정 세금계산서 작성시에 수정코드 사유코드 반드시 기재)
		Kwon					=> $Kwon, //별지서식 11호 상의 [권]항목
		Ho						=> $Ho, //별지서식 11호 상의 [호]항목
		SerialNum				=> $SerialNum, //별지서식 11호 상의 [일련번호]항목
		Cash					=> "",
		ChkBill					=> "",
		Note					=> "",
		Credit					=> "",
		WriteDate				=> "",
		AmountTotal				=> $tax_price,
		TaxTotal				=> $tax_prce_tax,
		TotalAmount				=> $totalprice,
		Remark1					=> $tax_note,
		Remark2					=> $tax_note,
		Remark3					=> $tax_note, 
		TaxInvoiceTradeLineItems => $params_TaxInvoiceTradeLineItems
	);

		//echo '<pre>';
		//print_r($params_Invoice); echo '</pre>';
		//exit;
	$params = array(
		CERTKEY		=> $CERTKEY,
		CorpNum		=> $CorpNum,
		Invoice		=> $params_Invoice
	);

	//echo '<h2>SoaP</h2><pre>'; 
	//print_r($params); 
	//echo '</pre>';
	//exit;
	
	// Soap 문장 전송
	$result = $client->call('RegistTaxInvoice', $params, '', '', false, true);
	$issueok = false;
	if ($client->fault) {
		// Soap 문장 오류시 예외처리
		echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; 
		print_r($result); echo '</pre>';
	} else {
		$err = $client->getError();
		if ($err) {
			// Soap 결과 오류시 예외처리
			$status_message = '<h2>Error</h2><pre>' . $err . '</pre>';
		} else {
			// 정상적으로 응답이 오는 경우에 대한 처리

			// 결과 문장에 대한 출력
			//echo '<h2>Result</h2><pre>'; 
			//print_r($result); 
			//echo '</pre>';
			
			// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
			if ( $result['RegistTaxInvoiceResult'] < 0 ) {
				$errCode = errCodeCheck($result['RegistTaxInvoiceResult']);
					$status_message = $errCode;
			} else {
				//임시저장성공시에만 쿼리 업데이트 및 인서트를 실행한다.
				if($sql_detail){
					$taxbill_detail_sql = explode(";",$sql_detail);
					for($j=0;$j<count($taxbill_detail_sql);$j++){
						$db->query($taxbill_detail_sql[$j]);
					}
				}
				$db->query($sql_taxbill);
				$issueok = true;
			}

		}
	}

	if($issueok == true){
		/****************  임시 저장된 문서를 발송한다 start **************************/
		$params2 = array(
			CERTKEY		=> $CERTKEY,
			CorpNum		=> $CorpNum,
			MgtKey		=> $MgtNum,
			SendSMS		=> "true"
		);

		// Soap 문장 전송
		$result = $client->call('IssueTaxInvoice', $params2, '', '', false, true);
		if ($client->fault) {
			// Soap 문장 오류시 예외처리
			//echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
			//print_r($result); echo '</pre>';
		} else {
			$err = $client->getError();
			if ($err) {
				// Soap 결과 오류시 예외처리
				$status_message = $err;
			} else {
				// 정상적으로 응답이 오는 경우에 대한 처리

				// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
				$IssueTaxInvoiceResult = $result['IssueTaxInvoiceResult'];
				if( $IssueTaxInvoiceResult > 0) {
					switch ($IssueTaxInvoiceResult ) {
						case 1:
							$status_message = "발행성공";
							// 발행성공일경우 발행상태를 변경한다.
							$updateno++;
							$sql = "update shop_taxbill set tax_issue_states = '2000' where tax_no = '".$MgtNum."' ";
							$db->query($sql);
							break;
						case 2:
							$status_message = "발행성공(SMS 전송 충전액 부족으로 실패)";
							break;
						case 3:
							$status_message = "발행성공(Email전송에 실패하였습니다. 메일을 재발송하십시오.)";
							break;
					}
				} else {
					$errCode = errCodeCheck($IssueTaxInvoiceResult);
						$status_message = $errCode;
				}
			}
		}
		/****************  임시 저장된 문서를 발송한다 end **************************/
	}

	$sql = "insert into shop_taxbill_status (oid, mgtnum, status, status_message,  company_id, quick, number, regdate) 
	values ('".$oid[$i]."','".$MgtNum."','".$status."','".$status_message."','".$admininfo[company_id]."','".$quick."','".count($taxbill_detail_sql)."',now()) ";
	$db->query($sql);
	echo "<script>alert('".$status_message."');parent.document.location.reload()</script>";
	exit;
}

if($act == "select_update"){
	$status = "계산서발송"; //액션명칭

	try {		

		if(count($oid) < 1){
			echo "<script>alert('체크박스를 체크하시고 진행해 주시기 바랍니다.')</script>";
			exit;
		}
		$updateno = '0';
		$incnt = count($oid);
		

		for($i=0;$i<count($oid);$i++){
			$sql = "select * from ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id = '".$admininfo[company_id]."' ";
			//exit($sql);
			$db2->query($sql);
			$db2->fetch();
			$com_number = explode("-", $db2->dt[com_number]);
			$com_number = trim($com_number[0]).trim($com_number[1]).trim($com_number[2]);
			$status_message = "";
			$status_message2 = "";
			$NowTime = date("YmdHis", time()); 
			$MgtNum = "FORBIZ" .  $NowTime;
			
			//공급자 정보
			$params_InvoicerParty = array(
				ContactID		=> "FORBIZ",  
				CorpNum			=> $com_number,  
				MgtNum			=> $MgtNum,  
				CorpName		=> $db2->dt[com_name],  
				TaxRegID		=> "",  
				CEOName			=> $db2->dt[com_ceo],  
				Addr			=> $db2->dt[com_addr1]." ".$db2->dt[com_addr2],
				BizClass		=>	$db2->dt[com_business_category],  
				BizType			=> $db2->dt[com_business_status],  
				ContactName		=> $db2->dt[com_ceo],  
				TEL				=> $db2->dt[com_phone],  
				HP				=> $db2->dt[com_phone],  
				Email			=> $db2->dt[com_email]
			);

			//echo '<pre>';
			//print_r($params_InvoicerParty); echo '</pre>';
			//exit;
			
			$taxt_regdate = $taxt_regdate_y[$oid[$i]]."-".$taxt_regdate_m[$oid[$i]]."-".$taxt_regdate_d[$oid[$i]];

			$sql = "select sum(case when od.status in ('IR','IC','DR','DI','EA','EI','ED','FA','RA','RI','RD','CA') then 1 else 0 end) as ing_cnt,  sum(case when (od.surtax_yorn = 'N' or od.surtax_yorn = '') then 1 else 0 end) as surtax_yorn from shop_order_detail od where oid = '".$oid[$i]."' ";
			
			$db2->query($sql);
			$db2->fetch();
			
			if($db2->dt[ing_cnt] == 0 && $db2->dt[surtax_yorn] > 0){

				$sql = "
				select ccd.*, o.total_price
				from shop_order o, common_user cu, common_company_detail ccd 
				where taxsheet_yn = 'Y'
				and o.uid = cu.code and cu.company_id = ccd.company_id  and o.oid = '".$oid[$i]."'
				";
				
				//exit($sql);
				$db4->query($sql);
				$db4->fetch();
				

				$db3->query("SELECT * FROM `shop_order_detail` WHERE `oid` = '".$oid[$i]."' and (surtax_yorn = 'N' or surtax_yorn = '') order by surtax_yorn ");
				
				$totalprice = 0;
				for($j=0;$j < $db3->total;$j++){
					$db3->fetch($j);
					$td_tax = round($db3->dt[ptprice]*0.1);
					$td_cost = $db3->dt[ptprice]-$td_tax;
					$amount = $td_cost * $db3->dt[pcnt];
					
					$taxt_Nonhiregdate = explode("-", $taxt_regdate);
					$taxt_Nonhiregdate = $taxt_Nonhiregdate[0].$taxt_Nonhiregdate[1].$taxt_Nonhiregdate[2];
					//계산서 상세항목
					$params_TaxInvoiceTradeLineItem[$j] = array(
						PurchaseExpiry			=> $taxt_Nonhiregdate, 
						Name					=> $db3->dt[pname], 
						Information				=> "", 
						ChargeableUnit			=> $db3->dt[pcnt], 
						UnitPrice				=> $td_cost, 
						Amount					=> $amount, 
						Tax						=> $td_tax, 
						Descriptio				=> $td_note
					);
					
					if($j > 0) $gubun = ";";
					else $gubun = "";
					
					$pname = $db3->dt[pname]; //테스트때문에 변수에담아놓았음 ..^^;;;;
					//$pname = str_replace("\"","&quot;",$db3->dt[pname]);
					//$pname = str_replace("'","&#39;",$pname);

					$sql_detail .= $gubun." insert into shop_taxbill_detail (tax_orderno, td_date, td_name, td_amount, td_cost, td_price, td_tax, td_note, td_tax_states, td_issue_states, td_order_states)
					values ('".$oid[$i]."', '".$taxt_regdate."', '".addslashes($pname)."', '".$db3->dt[pcnt]."', '".$td_cost."', '".$db3->dt[ptprice]."', '".$td_tax."', '".$td_note."', '".$db3->dt[surtax_yorn]."', 'Y', '".$db3->dt[status]."' )";
					$totalprice = $totalprice + $db3->dt[ptprice];
				}
					//exit($sql_detail);
				
				$com_number = explode("-", $db4->dt[com_number]);
				$com_number = $com_number[0].$com_number[1].$com_number[2];
				$tax_deposit_date = "";
				if($tax_deposit_states[$oid[$i]] == "Y") $tax_deposit_date = $bank_date;

				$com_addr = $db4->dt[com_addr1]." ".$db4->dt[com_addr2];
				
				$tax_prce_tax = round($totalprice*0.1);
				$tax_price = $totalprice-$tax_prce_tax;
					$sql_taxbill = "insert into shop_taxbill (	tax_orderno, tax_no, company_id, tax_comnumber, tax_comname, tax_name, tax_comaddr, tax_business_status, tax_business_category, taxt_regdate, tax_price, tax_prce_tax, tax_note, taxt_price_sum, tax_cash, tax_cheque, tax_promissnote, tax_credit, tax_charge_name, tax_charge_email, tax_kind, tax_deposit_states, tax_deposit_date, tax_issue_states, tax_sendnts)
					values ('".$oid[$i]."', '".$MgtNum."', '".$db4->dt[code]."', '".$db4->dt[com_number]."', '".$db4->dt[com_name]."', '".$db4->dt[com_ceo]."', '".$com_addr."', '".$db4->dt[com_business_status]."', '".$db4->dt[com_business_category]."', '".$taxt_regdate."', '".$tax_price."', 
					'".$tax_prce_tax."', '".$tax_note."', '".$totalprice."', '".$totalprice."', '".$tax_cheque."', '".$tax_promissnote."', '".$tax_credit."', '".$tax_charge_name[$oid[$i]]."', '".$tax_charge_email[$oid[$i]]."', 'Y', '".$tax_deposit_states[$oid[$i]]."', '".$tax_deposit_date."', 
					'0' , '1')";
					
//					exit($sql_taxbill);


				//공급받는자 정보
				$params_InvoiceeParty = array(
					ContactID		=> "",
					CorpNum			=> $com_number,
					MgtNum			=> "",
					CorpName		=> $db4->dt[com_name], 
					TaxRegID		=> "",
					CEOName			=> $db4->dt[com_ceo],  
					Addr			=> $com_addr,  
					BizClass		=> $db4->dt[com_business_status],  
					BizType			=> $db4->dt[com_business_category],  
					ContactName		=> $tax_charge_name[$oid[$i]],  
					TEL				=> "",
					HP				=> "",
					Email			=> $tax_charge_email[$oid[$i]]
				);
			//echo '<pre>';
			//print_r($params_InvoiceeParty); echo '</pre>';

				$params_TaxInvoiceTradeLineItems = array(
					TaxInvoiceTradeLineItem	=> $params_TaxInvoiceTradeLineItem
				);

				$params_Invoice = array(
					InvoiceKey  			=> "",
					InvoicerParty			=> $params_InvoicerParty,
					InvoiceeParty			=> $params_InvoiceeParty,
					InvoiceeASPEmail		=> "nts@barobill.co.kr",
					BrokerParty				=> null,
					IssueDirection			=> "1", //1 정발핼 2 역발행
					TaxInvoiceType			=> "1", //1 세금계산서 2 계산서 3 위수탁세금계산서 4 위수탁계산서
					TaxType					=> "1", //1 과세 2 영세 3 면세
					TaxCalcType				=> "1", //1 절상 2 절사 3 반올림
					PurposeType				=> "1", //1 영수 2 청구
					ModifyCode				=> "", //1 기제사항의 착오/정정 2 공급가액의 변동 3 환입 4 계약의 혜지 5 내국신용장 사후개설 (수정 세금계산서 작성시에 수정코드 사유코드 반드시 기재)
					Kwon					=> "1", //별지서식 11호 상의 [권]항목
					Ho						=> "1", //별지서식 11호 상의 [호]항목
					SerialNum				=> "12", //별지서식 11호 상의 [일련번호]항목
					Cash					=> "",
					ChkBill					=> "",
					Note					=> "",
					Credit					=> "",
					WriteDate				=> "",
					AmountTotal				=> $tax_price,
					TaxTotal				=> $tax_prce_tax,
					TotalAmount				=> $totalprice,
					Remark1					=> $tax_note,
					Remark2					=> $tax_note,
					Remark3					=> $tax_note, 
					TaxInvoiceTradeLineItems => $params_TaxInvoiceTradeLineItems
				);

					//echo '<pre>';
					//print_r($params_Invoice); echo '</pre>';
					//exit;
				$params = array(
					CERTKEY		=> $CERTKEY,
					CorpNum		=> $CorpNum,
					Invoice		=> $params_Invoice
				);

				//echo '<h2>SoaP</h2><pre>'; 
				//print_r($params); 
				//echo '</pre>';
				//exit;
				
				// Soap 문장 전송
				$result = $client->call('RegistTaxInvoice', $params, '', '', false, true);
				$issueok = false;
				if ($client->fault) {
					// Soap 문장 오류시 예외처리
					echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; 
					print_r($result); echo '</pre>';
				} else {
					$err = $client->getError();
					if ($err) {
						// Soap 결과 오류시 예외처리
						$status_message = '<h2>Error</h2><pre>' . $err . '</pre>';
					} else {
						// 정상적으로 응답이 오는 경우에 대한 처리

						// 결과 문장에 대한 출력
						//echo '<h2>Result</h2><pre>'; 
						//print_r($result); 
						//echo '</pre>';
						
						// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
						if ( $result['RegistTaxInvoiceResult'] < 0 ) {
							$errCode = errCodeCheck($result['RegistTaxInvoiceResult']);
								$status_message = $errCode;
						} else {
							//임시저장성공시에만 쿼리 업데이트 및 인서트를 실행한다.
							if($sql_detail){
								$sql = "update shop_order set taxsheet_yn = 'C' where oid = '".$oid[$i]."' ";
								$db->query($sql);
								$sql = "update shop_order_detail set tax_states = 'Y' where oid = '".$oid[$i]."' and surtax_yorn = 'N' ";
								$db->query($sql);
				
								$taxbill_detail_sql = explode(";",$sql_detail);
								for($j=0;$j<count($taxbill_detail_sql);$j++){
									$db->query($taxbill_detail_sql[$j]);
								}
							}
							$db->query($sql_taxbill);
							$issueok = true;
						}

					}
				}
		
				if($issueok == true){
					/****************  임시 저장된 문서를 발송한다 start **************************/
					$params2 = array(
						CERTKEY		=> $CERTKEY,
						CorpNum		=> $CorpNum,
						MgtKey		=> $MgtNum,
						SendSMS		=> "true"
					);

					// Soap 문장 전송
					$result = $client->call('IssueTaxInvoice', $params2, '', '', false, true);
					if ($client->fault) {
						// Soap 문장 오류시 예외처리
						//echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
						//print_r($result); echo '</pre>';
					} else {
						$err = $client->getError();
						if ($err) {
							// Soap 결과 오류시 예외처리
							$status_message = $err;
						} else {
							// 정상적으로 응답이 오는 경우에 대한 처리

							// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
							$IssueTaxInvoiceResult = $result['IssueTaxInvoiceResult'];
							if( $IssueTaxInvoiceResult > 0) {
								switch ($IssueTaxInvoiceResult ) {
									case 1:
										$status_message = "발행성공";
										// 발행성공일경우 발행상태를 변경한다.
										$updateno++;
										$sql = "update shop_taxbill set tax_issue_states = '2000' where tax_no = '".$MgtNum."' ";
										$db->query($sql);
										break;
									case 2:
										$status_message = "발행성공(SMS 전송 충전액 부족으로 실패)";
										break;
									case 3:
										$status_message = "발행성공(Email전송에 실패하였습니다. 메일을 재발송하십시오.)";
										break;
								}
							} else {
								$errCode = errCodeCheck($IssueTaxInvoiceResult);
									$status_message = $errCode;
							}
						}
					}
					/****************  임시 저장된 문서를 발송한다 end **************************/
				}
			} else { //발송할 order_detail이 있을경우만 실행
				$status_message = "발송할 상품상세정보가 없어 발송안됨";
			}
			$sql = "insert into shop_taxbill_status (oid, mgtnum, status, status_message,  company_id, quick, number, regdate) 
			values ('".$oid[$i]."','".$MgtNum."','".$status."','".$status_message."','".$admininfo[company_id]."','".$quick."','".count($taxbill_detail_sql)."',now()) ";
			$db->query($sql);
		} //선택한 데이터만큼 for문으로 반복 루프
		$falseno = $incnt - $updateno;

		echo "<script>alert('선택하신회원들중 \\n세금계산서 ".$incnt."건중 \\n".$updateno."건 발송 \\n".$falseno."건 실패 \\n발급완료로 변경되었습니다.');parent.document.location.reload()</script>";
		exit;
	} catch (Exception $e) {
		echo "<script>alert('실패하였습니다.');</script>";
	}
}
?>