<?php
/**
* =====================================================================================
* Class for base module for Popbill API SDK. It include base functionality for
* RESTful web service request and parse json result. It uses Linkhub module
* to accomplish authentication APIs.
*
* This module uses curl and openssl for HTTPS Request. So related modules must
* be installed and enabled.
*
* http://www.linkhub.co.kr
* Author : Kim Seongjun (pallet027@gmail.com)
* Written : 2014-04-15
*
* Thanks for your interest.
* We welcome any suggestions, feedbacks, blames or anything.
* ======================================================================================
*/
require_once 'Popbill/popbill.php';

class TaxinvoiceService extends PopbillBase {
	
	public function __construct($LinkID,$SecretKey) {
    	parent::__construct($LinkID,$SecretKey);
    	$this->AddScope('110');
    }
    
    //팝빌 세금계산서 연결 url
    public function GetURL($CorpNum,$UserID,$TOGO) {
    	$response = $this->executeCURL('/Taxinvoice/?TG='.$TOGO,$CorpNum,$UserID);
    	return $response->url;
    }
    
    //관리번호 사용여부 확인
    public function CheckMgtKeyInUse($CorpNum,$MgtKeyType,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	try
    	{
    		$response = $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey,$CorpNum);
    		return is_null($response->itemKey) == false;
    	}catch(PopbillException $pe) {
    		if($pe->getCode() == -11000005) {return false;}
    		throw $pe;
    	}
    }
    
    //임시저장
    public function Register($CorpNum, $Taxinvoice, $UserID = null, $writeSpecification = false) {
    	if($writeSpecification) {
    		$Taxinvoice->writeSpecification = $writeSpecification;
    	}
    	$postdata = json_encode($Taxinvoice);
    	return $this->executeCURL('/Taxinvoice',$CorpNum,$UserID,true,null,$postdata);
    }    
    
    //삭제
    public function Delete($CorpNum,$MgtKeyType,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'DELETE','');
    }
    
    //수정
    public function Update($CorpNum,$MgtKeyType,$MgtKey,$Taxinvoice, $UserID = null, $writeSpecification = false) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	if($writeSpecification) {
    		$Taxinvoice->writeSpecification = $writeSpecification;
    	}
    	
    	$postdata = json_encode($Taxinvoice);
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true, 'PATCH', $postdata);
    }
    
    //발행예정
    public function Send($CorpNum,$MgtKeyType,$MgtKey,$Memo = '',$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	$Request = new MemoRequest();
    	$Request->memo = $Memo;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'SEND',$postdata);
    }
    
    //발행예정취소
    public function CancelSend($CorpNum,$MgtKeyType,$MgtKey,$Memo = '',$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	$Request = new MemoRequest();
    	$Request->memo = $Memo;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'CANCELSEND',$postdata);
    }
    
    //발행예정 승인
    public function Accept($CorpNum,$MgtKeyType,$MgtKey,$Memo = '',$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	$Request = new MemoRequest();
    	$Request->memo = $Memo;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'ACCEPT',$postdata);
    }
    
    //발행예정 거부
    public function Deny($CorpNum,$MgtKeyType,$MgtKey,$Memo = '',$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	$Request = new MemoRequest();
    	$Request->memo = $Memo;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'DENY',$postdata);
    }
    
    //발행
    public function Issue($CorpNum,$MgtKeyType,$MgtKey,$Memo = '',$EmailSubject = null , $ForceIssue = false, $UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	$Request = new IssueRequest();
    	$Request->memo = $Memo;
    	$Request->emailSubject = $EmailSubject;
    	$Request->forceIssue = $ForceIssue;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'ISSUE',$postdata);
    }
    
    //발행취소
    public function CancelIssue($CorpNum,$MgtKeyType,$MgtKey,$Memo = '',$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	$Request = new MemoRequest();
    	$Request->memo = $Memo;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'CANCELISSUE',$postdata);
    }
    
    //역)발행요청
    public function Request($CorpNum,$MgtKeyType,$MgtKey,$Memo = '',$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	$Request = new MemoRequest();
    	$Request->memo = $Memo;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'REQUEST',$postdata);
    }
    
    //역)발행요청 거부
    public function Refuse($CorpNum,$MgtKeyType,$MgtKey,$Memo = '',$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	$Request = new MemoRequest();
    	$Request->memo = $Memo;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'REFUSE',$postdata);
    }
    
    //역)발행요청 취소
    public function CancelRequest($CorpNum,$MgtKeyType,$MgtKey,$Memo = '',$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	$Request = new MemoRequest();
    	$Request->memo = $Memo;
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'CANCELREQUEST',$postdata);
    }
    
    //국세청 즉시전송 요청
    public function SendToNTS($CorpNum,$MgtKeyType,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'NTS','');
    }
    
    //알림메일 재전송
    public function SendEmail($CorpNum,$MgtKeyType,$MgtKey,$Receiver,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	$Request = array('receiver' => $Receiver);
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'EMAIL',$postdata);
    }
    
    //알림문자 재전송
    public function SendSMS($CorpNum,$MgtKeyType,$MgtKey,$Sender,$Receiver,$Contents,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	$Request = array('receiver' => $Receiver,'sender'=>$Sender,'contents' => $Contents);
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'SMS',$postdata);
    }
    
    //알림팩스 재전송
    public function SendFAX($CorpNum,$MgtKeyType,$MgtKey,$Sender,$Receiver,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.',-99999999);
    	}
    	
    	$Request = array('receiver' => $Receiver,'sender'=>$Sender);
    	$postdata = json_encode($Request);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum, $UserID, true,'FAX',$postdata);
    }
    
    //세금계산서 요약정보 및 상태정보 확인
    public function GetInfo($CorpNum,$MgtKeyType,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey, $CorpNum);
    }
    
    //세금계산서 상세정보 확인 
    public function GetDetailInfo($CorpNum,$MgtKeyType,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey.'?Detail', $CorpNum);
    }
    
    //세금계산서 요약정보 다량확인 최대 1000건
    public function GetInfos($CorpNum,$MgtKeyType,$MgtKeyList = array()) {
    	if(is_null($MgtKeyList) || empty($MgtKeyList)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	$postdata = json_encode($MgtKeyList);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType, $CorpNum, null, true,null,$postdata);
    }
    
    //세금계산서 문서이력 확인 
    public function GetLogs($CorpNum,$MgtKeyType,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey.'/Logs', $CorpNum);
    }
    
    //파일첨부
    public function AttachFile($CorpNum,$MgtKeyType,$MgtKey,$FilePath , $UserID = null) {
    
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    
    	$postdata = array('Filedata' => '@'.$FilePath);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey.'/Files', $CorpNum, $UserID, true,null,$postdata,true);
    
    }
    
    //첨부파일 목록 확인 
    public function GetFiles($CorpNum,$MgtKeyType,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey.'/Files', $CorpNum);
    }
    
    //첨부파일 삭제 
    public function DeleteFile($CorpNum,$MgtKeyType,$MgtKey,$FileID,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	if(is_null($FileID) || empty($FileID)) {
    		throw new PopbillException('파일아이디가 입력되지 않았습니다.');
    	}
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey.'/Files/'.$FileID, $CorpNum,$UserID,true,'DELETE','');
    }
    
    //팝업URL
    public function GetPopUpURL($CorpNum,$MgtKeyType,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey.'?TG=POPUP', $CorpNum,$UserID)->url;
    }
    
    //인쇄URL
    public function GetPrintURL($CorpNum,$MgtKeyType,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey.'?TG=PRINT', $CorpNum,$UserID)->url;
    }
    
    //공급받는자 메일URL
    public function GetMailURL($CorpNum,$MgtKeyType,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'/'.$MgtKey.'?TG=MAIL', $CorpNum,$UserID)->url;
    }
    
    //세금계산서 다량인쇄 URL
    public function GetMassPrintURL($CorpNum,$MgtKeyType,$MgtKeyList = array(),$UserID = null) {
    	if(is_null($MgtKeyList) || empty($MgtKeyList)) {
    		throw new PopbillException('관리번호가 입력되지 않았습니다.');
    	}
    	
    	$postdata = json_encode($MgtKeyList);
    	
    	return $this->executeCURL('/Taxinvoice/'.$MgtKeyType.'?Print', $CorpNum, $UserID, true,null,$postdata)->url;
    }
    
    //회원인증서 만료일 확인
    public function GetCertificateExpireDate($CorpNum) {
    	return $this->executeCURL('/Taxinvoice?cfg=CERT', $CorpNum)->certificateExpiration;
    }
    
    //발행단가 확인
    public function GetUnitCost($CorpNum) {
    	return $this->executeCURL('/Taxinvoice?cfg=UNITCOST', $CorpNum)->unitCost;
    }
    
    //대용량 연계사업자 유통메일목록 확인
    public function GetEmailPublicKeys($CorpNum) {
    	return $this->executeCURL('/Taxinvoice/EmailPublicKeys', $CorpNum);
    }
    
}

class Taxinvoice
{
	
	public $writeSpecification;
	public $writeDate;
	public $chargeDirection;
	public $issueType;
	public $issueTiming;
	public $taxType;
	public $ivoicerCorpNum;
	public $invoicerMgtKey;
	public $invoicerTaxRegID;
	public $invoicerCorpName;
	public $invoicerCEOName;
	public $invoicerAddr;
	public $invoicerBizClass;
	public $invoicerBizType;
	public $invoicerContactName;
	public $invoicerDeptName;
	public $invoicerTEL;
	public $invoicerHP;
	public $invoicerEmail;
	public $invoicerSMSSendYN;
	
	public $invoiceeCorpNum;
	public $invoiceeType;
	public $invoiceeMgtKey;
	public $invoiceeTaxRegID;
	public $invoiceeCorpName;
	public $invoiceeCEOName;
	public $invoiceeAddr;
	public $invoiceeBizClass;
	public $invoiceeBizType;
	public $invoiceeContactName1;
	public $invoiceeDeptName1;
	public $invoiceeTEL1;
	public $invoiceeHP1;
	public $invoiceeEmail2;
	public $invoiceeContactName2;
	public $invoiceeDeptName2;
	public $invoiceeTEL2;
	public $invoiceeHP2;
	public $invoiceeEmail1;
	public $invoiceeSMSSendYN;
	
	public $trusteeCorpNum;
	public $trusteeMgtKey;
	public $trusteeTaxRegID;
	public $trusteeCorpName;
	public $trusteeCEOName;
	public $trusteeAddr;
	public $trusteeBizClass;
	public $trusteeBizType;
	public $trusteeContactName;
	public $trusteeDeptName;
	public $trusteeTEL;
	public $trusteeHP;
	public $trusteeEmail;
	public $trusteeSMSSendYN;
	
	public $taxTotal;
	public $supplyCostTotal;
	public $totalAmount;
	public $modifyCode;
	public $purposeType;
	public $serialNum;
	public $cash;
	public $chkBill;
	public $credit;
	public $note;
	public $remark1;
	public $remark2;
	public $remark3;
	public $kwon;
	public $ho;
	public $businessLicenseYN;
	public $bankBookYN;
	public $faxsendYN;
	public $faxreceiveNum;
	public $originalTaxinvoiceKey;
	public $detailList;
	public $addContactList;
	
}
class TaxinvoiceDetail {
	public $serialNum;
	public $purchaseDT;
	public $itemName;
	public $spec;
	public $qty;
	public $unitCost;
	public $supplyCost;
	public $tax;
	public $remark;
}
class TaxinvoiceAddContact {
	public $serialNum;
	public $email;
	public $contactName;
}
class ENumMgtKeyType {
	const SELL = 'SELL';
	const BUY = 'BUY';
	const TRUSTEE = 'TRUSTEE';
}
class MemoRequest {
	public $memo;
}
class IssueRequest {
	public $memo;
	public $emailSubject;
	public $forceIssue;
}
?>