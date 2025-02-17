<?php
require_once dirname(__FILE__).'/WebParamGather.php';

/**
 * 
 * @author kblee
 *
 */
class VBankWebParamGather implements WebParamGather{
	
	/**
	 * Default Constructor
	 */
	public function VBankWebParamGather(){
		
	}
	
	/**
	 * 
	 * @param $request
	 */
	public function gather($request) {
		$webParam = new WebMessageDTO();
		
		$bankCode = $request["BankCode"];
		$webParam->setParameter(VBANK_CODE,$bankCode);
		
		// accountName이 비었을 경우 구매자명으로 설정
		$vbankAccountName = $request["VbankAccountName"];
		if($vbankAccountName == null || $vbankAccountName == ""){
			$vbankAccountName = $request["BuyerName"];
		}
		
		$receitTypeNo = $request["ReceiptTypeNo"];
		$webParam->setParameter(BUYER_AUTH_NO,$receitTypeNo);
		
		$webParam->setParameter(ACCT_NAME,$vbankAccountName);
		
		$cashReceitType = $request["CashReceiptType"];
		$webParam->setParameter(RECEIPT_TYPE,$cashReceitType);
		
		$receiptTypeNo =  $request["ReceiptTypeNo"];
		$webParam->setParameter(RECEIPT_TYPE_NO,$receiptTypeNo);
		
		$vbankExpDate = $request["VbankExpDate"];
		
		if (strlen($vbankExpDate) == 12 ){
			$vbankExpTime = substr($vbankExpDate,8,4)."59";
			$webParam->setParameter(VBANK_EXPIRE_DATE,substr($vbankExpDate,0,8));
			$webParam->setParameter(VBANK_EXPIRE_TIME,$vbankExpTime);
		}else{
			$webParam->setParameter(VBANK_EXPIRE_DATE,$vbankExpDate);
		}
		
		$transType = $request["TransType"] == null ? "0" : $request["TransType"];
		$webParam->setParameter(TRANS_TYPE,$transType);
		
		$trKey = $request["TrKey"] == null ? "0" : $request["TrKey"];
		$webParam->setParameter(TR_KEY,$trKey);
		
		return $webParam;
	}
	
}
?>
