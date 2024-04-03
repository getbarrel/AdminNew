<?
/**
 * kspay 취소 모듈
 *
 * @author pyw
 * @date 2016.02.16
 */


class kspay{

	private $data;
	private $result;

	public function __construct(){
	    $result = null;
	}

	public function cancelService($data){

		$this->data = $data;

		if($this->data["METHOD"] == 1){ //카드
			$this->cancelServiceCard();
		}
		elseif($this->data["METHOD"] == 4){ //가상계좌
			$this->cancelServiceVbank();
		}
		elseif($this->data["METHOD"] == 5){ //실시간이체
			$this->cancelServiceIche();
		}


		return $this->result;
	}

	private function cancelServiceCard(){

		include_once $_SERVER["DOCUMENT_ROOT"]."/shop/kspay/cancel/KSPayApprovalCancel.inc";	//가장 최신날짜의 KSPayApprovalCancel.inc 파일

		// Default-------------------------------------------------------
			$EncType     = "2";     // 0: 암화안함, 1:openssl, 2: seed
			$Version     = "0210";  // 전문버전
			$VersionType = "00";    // 구분
			$Resend      = "0";     // 전송구분 : 0 : 처음,  2: 재전송

			$RequestDate=           // 요청일자 : yyyymmddhhmmss
				SetZero(strftime("%Y"),4).
				SetZero(strftime("%m"),2).
				SetZero(strftime("%d"),2).
				SetZero(strftime("%H"),2).
				SetZero(strftime("%M"),2).
				SetZero(strftime("%S"),2);
			$KeyInType     = "K";   // KeyInType 여부 : S : Swap, K: KeyInType
			$LineType      = "1";   // lineType 0 : offline, 1:internet, 2:Mobile
			$ApprovalCount = "1";   // 복합승인갯수
			$GoodType      = "0";   // 제품구분 0 : 실물, 1 : 디지털
			$HeadFiller    = "";   // 예비
		//-------------------------------------------------------------------------------

		// Header (입력값 (*) 필수항목)--------------------------------------------------
			$StoreId		=	$this->data["MID"];			// *상점아이디
			$OrderNumber	=	$this->data["OID"]; 		// *주문번호
			$UserName		=	$this->data["BNAME"];   	// *주문자명
			$IdNum		    =	"";       					// 주민번호 or 사업자번호
			$Email			=	$this->data["BMAIL"];       // *email
			$GoodName		=	$this->data["PNAME"];    	// *제품명
			$PhoneNo		=	$this->data["BMOBILE"];     // *휴대폰번호
		// Header end -------------------------------------------------------------------



		// Data Default(수정항목이 아님)-------------------------------------------------
			$ApprovalType   =	$this->data["APPROVALTYPE"];	// 승인구분
			$TransactionNo  =	$this->data["TID"];		// 거래번호

			$Canc_amt       = $this->data["CANC_AMT"];	//' 취소금액
			$Canc_seq       = $this->data["CANC_SEQ"];	//' 취소일련번호
			$Canc_type      = $this->data["CANC_TYPE"];	//' 취소유형 0 :거래번호취소 1: 주문번호취소 3:부분취소
		// Data Default end -------------------------------------------------------------

		// Server로 부터 응답이 없을시 자체응답
			$rApprovalType     = $this->data["RAPPROVALTYPE"];
			$rTransactionNo    = "";              // 거래번호
			$rStatus           = "X";             // 상태 O : 승인, X : 거절
			$rTradeDate        = "";              // 거래일자
			$rTradeTime        = "";              // 거래시간
			$rIssCode          = "00";            // 발급사코드
			$rAquCode          = "00";            // 매입사코드
			$rAuthNo           = "9999";          // 승인번호 or 거절시 오류코드
			$rMessage1         = "취소거절";      // 메시지1
			$rMessage2         = "C잠시후재시도"; // 메시지2
			$rCardNo           = "";              // 카드번호
			$rExpDate          = "";              // 유효기간
			$rInstallment      = "";              // 할부
			$rAmount           = "";              // 금액
			$rMerchantNo       = "";              // 가맹점번호
			$rAuthSendType     = "N";             // 전송구분
			$rApprovalSendType = "N";             // 전송구분(0 : 거절, 1 : 승인, 2: 원카드)
			$rPoint1           = "000000000000";  // Point1
			$rPoint2           = "000000000000";  // Point2
			$rPoint3           = "000000000000";  // Point3
			$rPoint4           = "000000000000";  // Point4
			$rVanTransactionNo = "";
			$rFiller           = "";              // 예비
			$rAuthType         = "";              // ISP : ISP거래, MP1, MP2 : MPI거래, SPACE : 일반거래
			$rMPIPositionType  = "";              // K : KSNET, R : Remote, C : 제3기관, SPACE : 일반거래
			$rMPIReUseType     = "";              // Y : 재사용, N : 재사용아님
			$rEncData          = "";              // MPI, ISP 데이터

		// --------------------------------------------------------------------------------

			KSPayApprovalCancel("localhost", 29991);

			HeadMessage(
				$EncType       ,                  // 0: 암화안함, 1:openssl, 2: seed
				$Version       ,                  // 전문버전
				$VersionType   ,                  // 구분
				$Resend        ,                  // 전송구분 : 0 : 처음,  2: 재전송
				$RequestDate   ,                  // 재사용구분
				$StoreId       ,                  // 상점아이디
				$OrderNumber   ,                  // 주문번호
				$UserName      ,                  // 주문자명
				$IdNum         ,                  // 주민번호 or 사업자번호
				$Email         ,                  // email
				$GoodType      ,                  // 제품구분 0 : 실물, 1 : 디지털
				$GoodName      ,                  // 제품명
				$KeyInType     ,                  // KeyInType 여부 : S : Swap, K: KeyInType
				$LineType      ,                  // lineType 0 : offline, 1:internet, 2:Mobile
				$PhoneNo       ,                  // 휴대폰번호
				$ApprovalCount ,                  // 복합승인갯수
				$HeadFiller    );                 // 예비

		// ------------------------------------------------------------------------------
			if($Canc_type == '3'){
				CancelDataMessage($ApprovalType, $Canc_type, $TransactionNo,	"",	"", SetZero($Canc_amt,9).SetZero($Canc_seq,2),	"", "");
			}
			else{
				CancelDataMessage($ApprovalType, "0", $TransactionNo,	"",	"", "",	"", "");
			}

			if (SendSocket("1")) {
				$rApprovalType		= $GLOBALS[ApprovalType]    ;
				$rTransactionNo		= $GLOBALS[TransactionNo]	;  	// 거래번호
				$rStatus			= $GLOBALS[Status]		  	;	// 상태 O : 승인, X : 거절
				$rTradeDate			= $GLOBALS[TradeDate]		;  	// 거래일자
				$rTradeTime			= $GLOBALS[TradeTime]		;  	// 거래시간
				$rIssCode			= $GLOBALS[IssCode]		  	;	// 발급사코드
				$rAquCode			= $GLOBALS[AquCode]		  	;	// 매입사코드
				$rAuthNo			= $GLOBALS[AuthNo]		  	;	// 승인번호 or 거절시 오류코드
				$rMessage1			= $GLOBALS[Message1]	  	;	// 메시지1
				$rMessage2			= $GLOBALS[Message2]	  	;	// 메시지2
				$rCardNo			= $GLOBALS[CardNo]		  	;	// 카드번호
				$rExpDate			= $GLOBALS[ExpDate]		  	;	// 유효기간
				$rInstallment		= $GLOBALS[Installment]	  	;	// 할부
				$rAmount			= $GLOBALS[Amount]		  	;	// 금액
				$rMerchantNo		= $GLOBALS[MerchantNo]	  	;	// 가맹점번호
				$rAuthSendType		= $GLOBALS[AuthSendType]  	;	// 전송구분= new String(this.read(2))
				$rApprovalSendType	= $GLOBALS[ApprovalSendType];	// 전송구분(0 : 거절, 1 : 승인, 2: 원카드)
				$rPoint1			= $GLOBALS[Point1]		  	;	// Point1
				$rPoint2			= $GLOBALS[Point2]		  	;	// Point2
				$rPoint3			= $GLOBALS[Point3]		  	;	// Point3
				$rPoint4			= $GLOBALS[Point4]		  	;	// Point4
				$rVanTransactionNo  = $GLOBALS[VanTransactionNo];   // Van거래번호
				$rFiller			= $GLOBALS[Filler]		  	;	// 예비
				$rAuthType			= $GLOBALS[AuthType]	  	;	// ISP : ISP거래, MP1, MP2 : MPI거래, SPACE : 일반거래
				$rMPIPositionType	= $GLOBALS[MPIPositionType]	;	// K : KSNET, R : Remote, C : 제3기관, SPACE : 일반거래
				$rMPIReUseType		= $GLOBALS[MPIReUseType]	;	// Y : 재사용, N : 재사용아님
				$rEncData			= $GLOBALS[EncData]		  	;	// MPI, ISP 데이터
			}

		$this->result["ApprovalType"]	= $rApprovalType;		//거래종류
		$this->result["rTransactionNo"] = $rTransactionNo;		//거래번호
		$this->result["resultCode"]		= $rStatus;				//거래성공여부 (O,X)
		$this->result["rTradeDate"]		= $rTradeDate;			//거래 일
		$this->result["rTradeTime"]		= $rTradeTime;			//거래 시간
		$this->result["rRespCode"]		= $rAuthNo;				//응답코드
		$this->result["resultMsg"]		= iconv("EUC-KR","UTF-8",$rMessage1) . " " . iconv("EUC-KR","UTF-8",$rMessage2);			//메세지

		$this->result["method"] = 	$this->data["METHOD"];
		$this->result["mid"] = 	$this->data["MID"];              // 상점 ID
		$this->result["tid"] = $this->data["TID"];               // TID

	}

	private function cancelServiceVBank(){

		include_once $_SERVER["DOCUMENT_ROOT"]."/shop/kspay/cancel/KSPayApprovalCancel.inc";	//가장 최신날짜의 KSPayApprovalCancel.inc 파일

		// Default-------------------------------------------------------
			$EncType     = "2";     // 0: 암화안함, 1:openssl, 2: seed
			$Version     = "0603";  // 전문버전
			$VersionType = "00";    // 구분
			$Resend      = "0";     // 전송구분 : 0 : 처음,  2: 재전송

			$RequestDate=           // 요청일자 : yyyymmddhhmmss
				SetZero(strftime("%Y"),4).
				SetZero(strftime("%m"),2).
				SetZero(strftime("%d"),2).
				SetZero(strftime("%H"),2).
				SetZero(strftime("%M"),2).
				SetZero(strftime("%S"),2);
			$KeyInType     = "K";   // KeyInType 여부 : S : Swap, K: KeyInType
			$LineType      = "1";   // lineType 0 : offline, 1:internet, 2:Mobile
			$ApprovalCount = "1";   // 복합승인갯수
			$GoodType      = "0";   // 제품구분 0 : 실물, 1 : 디지털
			$HeadFiller     = "";    // 예비
		//-------------------------------------------------------------------------------

		// Header (입력값 (*) 필수항목)--------------------------------------------------
			$StoreId		=	$this->data["MID"];			// *상점아이디
			$OrderNumber	=	$this->data["OID"]; 		// *주문번호
			$UserName		=	$this->data["BNAME"];   	// *주문자명
			$IdNum		    =	"";       					// 주민번호 or 사업자번호
			$Email			=	$this->data["BMAIL"];       // *email
			$GoodName		=	$this->data["PNAME"];    	// *제품명
			$PhoneNo		=	$this->data["BMOBILE"];     // *휴대폰번호
		// Header end -------------------------------------------------------------------

		// Data Default(수정항목이 아님)-------------------------------------------------
			$ApprovalType   =	$this->data["APPROVALTYPE"];	// 승인구분
			$TransactionNo  =	$this->data["TID"];		// 거래번호
		// Data Default end -------------------------------------------------------------


		// 승인거절 응답
		// Server로 부터 응답이 없을시 자체응답

			$rVATransactionNo		= "";						// 거래번호
			$rVAStatus				= "X";						// 상태 O : 승인, X : 거절
			$rVATradeDate			= "";						// 거래일자
			$rVATradeTime			= "";						// 거래시간
			$rVABankCode			= "";
			$rVAName				= "";
			$VACloseDate			= "";						//마감일
			$VACloseTime			= "";						//마감시간
			$VARespCode 			= "9999";					//응답코드
			$rVAMessage1			= "취소거절";				// 메시지1
			$rVAMessage2			= "C잠시후재시도";			// 메시지2
			$rVAFiller				= "";						// 예비
		// --------------------------------------------------------------------------------

			KSPayApprovalCancel("localhost", 29991);

			HeadMessage(
				$EncType       ,                  // 0: 암화안함, 1:openssl, 2: seed
				$Version       ,                  // 전문버전
				$VersionType   ,                  // 구분
				$Resend        ,                  // 전송구분 : 0 : 처음,  2: 재전송
				$RequestDate   ,                  // 재사용구분
				$StoreId       ,                  // 상점아이디
				$OrderNumber   ,                  // 주문번호
				$UserName      ,                  // 주문자명
				$IdNum         ,                  // 주민번호 or 사업자번호
				$Email         ,                  // email
				$GoodType      ,                  // 제품구분 0 : 실물, 1 : 디지털
				$GoodName      ,                  // 제품명
				$KeyInType     ,                  // KeyInType 여부 : S : Swap, K: KeyInType
				$LineType      ,                  // lineType 0 : offline, 1:internet, 2:Mobile
				$PhoneNo       ,                  // 휴대폰번호
				$ApprovalCount ,                  // 복합승인갯수
				$HeadFiller    );                 // 예비


		// ------------------------------------------------------------------------------
			CancelDataMessage($ApprovalType, "0", $TransactionNo,	"",	"", "",	"", "");

			if (SendSocket("1")) {
				$rVATransactionNo	= $GLOBALS[VATransactionNo]	;  	// 거래번호
				$rVAStatus			= $GLOBALS[VAStatus]	  	;	// 상태 O : 승인, X : 거절
				$rVATradeDate		= $GLOBALS[VATradeDate]		;  	// 거래일자
				$rVATradeTime		= $GLOBALS[VATradeTime]		;  	// 거래시간
				$rVABankCode		= $GLOBALS[VABankCode]		;	// 발급사코드
				$rVAVirAcctNo 		= $GLOBALS[VAVirAcctNo]		;	// 매입사코드
				$rVAName			= $GLOBALS[VAName]		  	;	// 승인번호 or 거절시 오류코드
				$rVACloseDate		= $GLOBALS[VACloseDate]		;   // 마감일
				$rVACloseTime		= $GLOBALS[VACloseTime]		;   // 마감시간
				$rVARespCode 		= $GLOBALS[VARespCode] 		;	// 응답코드

				$rVAMessage1		= $GLOBALS[VAMessage1]		;	// 메시지1
				$rVAMessage2		= $GLOBALS[VAMessage2]		;	// 메시지2
				$rVAFiller			= $GLOBALS[VAFiller]		  	;	// 예비
			}

		$this->result["rTransactionNo"]	= $rVATransactionNo;	//거래번호
		$this->result["resultCode"]		= $rVAStatus;			//거래성공여부 (O,X)
		$this->result["rTradeDate"]		= $rVATradeDate;		//거래 일
		$this->result["rTradeTime"]		= $rVATradeTime;		//거래 시간
		$this->result["rRespCode"]		= $rVARespCode;			//응답코드
		$this->result["resultMsg"]		= iconv("EUC-KR","UTF-8",$rVAMessage1) . " " . iconv("EUC-KR","UTF-8",$rVAMessage2);			//메세지

		$this->result["method"]			= $this->data["METHOD"];
		$this->result["mid"]			= $this->data["MID"];	// 상점 ID
		$this->result["tid"]			= $this->data["TID"];	// TID

	}

	private function cancelServiceIche(){

		include_once $_SERVER["DOCUMENT_ROOT"]."/shop/kspay/cancel/KSPayApprovalCancel.inc";	//가장 최신날짜의 KSPayApprovalCancel.inc 파일

		//Header부 Data --------------------------------------------------
			$EncType			= "2"; 									// 0: 암화안함, 1:ssl, 2: seed
			$Version			= "0603"; 								// 전문버전
			$VersionType		= "00";	 								// 구분
			$Resend				= "0"; 									// 전송구분 : 0 : 처음,  2: 재전송
			// 요청일자
			$RequestDate		= SetZero(strftime("%Y"),4).
								  SetZero(strftime("%m"),2).
								  SetZero(strftime("%d"),2).
								  SetZero(strftime("%H"),2).
								  SetZero(strftime("%M"),2).
								  SetZero(strftime("%S"),2);
			$KeyInType			= "K";  								// KeyInType 여부 : S : Swap, K: KeyInType
			$LineType			= "1";  								// lineType 0 : offline, 1:internet, 2:Mobile
			$ApprovalCount		= "1"; 									// 복합승인갯수
			$GoodType			= "0";  								// 제품구분 0 : 실물, 1 : 디지털
			$HeadFiller			= ""; 									// 예비

		// Header (입력값 (*) 필수항목)--------------------------------------------------
			$StoreId		=	$this->data["MID"];			// *상점아이디
			$OrderNumber	=	$this->data["OID"]; 		// *주문번호
			$UserName		=	$this->data["BNAME"];   	// *주문자명
			$IdNum		    =	"";       					// 주민번호 or 사업자번호
			$Email			=	$this->data["BMAIL"];       // *email
			$GoodName		=	$this->data["PNAME"];    	// *제품명
			$PhoneNo		=	$this->data["BMOBILE"];     // *휴대폰번호
		// Header end -------------------------------------------------------------------

		// Data Default(수정항목이 아님)-------------------------------------------------
			$ApprovalType   =	$this->data["APPROVALTYPE"];	// 승인구분
			$TransactionNo  =	$this->data["TID"];		// 거래번호
		// Data Default end -------------------------------------------------------------


		// Server로 부터 응답이 없을시 자체응답
			$rApprovalType    		= $this->data["RAPPROVALTYPE"];							// 승인구분
			$rACTransactionNo    	= $TransactionNo;					// 거래번호
			$rACStatus           	= "X";								// 오류구분 :승인 X:거절
			$rACTradeDate        	= "";								// 거래 개시 일자(YYYYMMDD)
			$rACTradeTime        	= "";								// 거래 개시 시간(HHMMSS)
			$rACAcctSele         	= "";								// 계좌이체 구분 -	1:Dacom, 2:Pop Banking,	3:실시간계좌이체 4: 승인형계좌이체
			$rACFeeSele          	= "";								// 선/후불제구분 -	1:선불,	2:후불
			$rACInjaName         	= "";								// 인자명(통장인쇄메세지-상점명)
			$rACPareBankCode     	= "";								// 입금모계좌코드
			$rACPareAcctNo       	= "";								// 입금모계좌번호
			$rACCustBankCode     	= "";								// 출금모계좌코드
			$rACCustAcctNo       	= "";								// 출금모계좌번호
			$rACAmount	       		= "";								// 금액	(결제대상금액)
			$rACBankTransactionNo	= "";								// 은행거래번호
			$rACIpgumNm          	= "";								// 입금자명
			$rACBankFee          	= "0";								// 계좌이체 수수료
			$rACBankAmount       	= "";								// 총결제금액(결제대상금액+ 수수료
			$rACBankRespCode     	= "9999";							// 오류코드
			$rACMessage1         	= "취소거절";						// 오류 message 1
			$rACMessage2         	= "C잠시후재시도";					// 오류 message 2
			$rACFiller           	= "";								// 예비
		// --------------------------------------------------------------------------------

			KSPayApprovalCancel("localhost", 29991);

			HeadMessage(
				$EncType       ,                  // 0: 암화안함, 1:openssl, 2: seed
				$Version       ,                  // 전문버전
				$VersionType   ,                  // 구분
				$Resend        ,                  // 전송구분 : 0 : 처음,  2: 재전송
				$RequestDate   ,                  // 재사용구분
				$StoreId       ,                  // 상점아이디
				$OrderNumber   ,                  // 주문번호
				$UserName      ,                  // 주문자명
				$IdNum         ,                  // 주민번호 or 사업자번호
				$Email         ,                  // email
				$GoodType      ,                  // 제품구분 0 : 실물, 1 : 디지털
				$GoodName      ,                  // 제품명
				$KeyInType     ,                  // KeyInType 여부 : S : Swap, K: KeyInType
				$LineType      ,                  // lineType 0 : offline, 1:internet, 2:Mobile
				$PhoneNo       ,                  // 휴대폰번호
				$ApprovalCount ,                  // 복합승인갯수
				$HeadFiller    );                 // 예비

			CancelDataMessage($ApprovalType, "0", $TransactionNo,	"",	"", "",	"", "");

			if (SendSocket("1")) {
					$rApprovalType		=	$GLOBALS[ApprovalType]		;
					$rTransactionNo		=	$GLOBALS[ACTransactionNo]	;   // 거래번호
					$rStatus			=	$GLOBALS[ACStatus]			;   // 오류구분 :승인 X:거절
					$rTradeDate			=	$GLOBALS[ACTradeDate]		;   // 거래 개시 일자(YYYYMMDD)
					$rTradeTime			=	$GLOBALS[ACTradeTime]		;   // 거래 개시 시간(HHMMSS)
					$rAcctSele		   	=	$GLOBALS[ACAcctSele]		;   // 계좌이체 구분-1:Dacom,2:Pop Banking,3:Scrapping 계좌이체, 4:승인형계좌이체, 5:금결원계좌이체
					$rFeeSele			=	$GLOBALS[ACFeeSele]			;   // 선/후불제구분 -	1:선불,	2:후불
					$rInjaName		   	=	$GLOBALS[ACInjaName]		;   // 인자명(통장인쇄메세지-상점명)
					$rPareBankCode	   	=	$GLOBALS[ACPareBankCode]	;   // 입금모계좌코드
					$rPareAcctNo		=	$GLOBALS[ACPareAcctNo]		;   // 입금모계좌번호
					$rCustBankCode	    =	$GLOBALS[ACCustBankCode]	;   // 출금모계좌코드
					$rCustAcctNo		=	$GLOBALS[ACCustAcctNo]		;   // 출금모계좌번호
					$rAmount			=	$GLOBALS[ACAmount]			;   // 금액	(결제대상금액)
					$rBankTransactionNo =	$GLOBALS[ACBankTransactionNo];   // 은행거래번호
					$rIpgumNm			=	$GLOBALS[ACIpgumNm]			;   // 입금자명
					$rBankFee			=	$GLOBALS[ACBankFee]			;   // 계좌이체 수수료
					$rBankAmount		=	$GLOBALS[ACBankAmount]		;   // 총결제금액(결제대상금액+ 수수료)
					$rBankRespCode	    =	$GLOBALS[ACBankRespCode]	;   // 오류코드
					$rMessage1		    =	$GLOBALS[ACMessage1]		;   // 오류 message 1
					$rMessage2		    =	$GLOBALS[ACMessage2]		;   // 오류 message 2
					$rFiller			=	$GLOBALS[ACFiller]			;   // 예비
			}

		$this->result["ApprovalType"]	= $rApprovalType;		//거래종류
		$this->result["rTransactionNo"]	= $rTransactionNo;		//거래번호
		$this->result["resultCode"]		= $rStatus;				//거래성공여부 (O,X)
		$this->result["rTradeDate"]		= $rTradeDate;			//거래 일
		$this->result["rTradeTime"]		= $rTradeTime;			//거래 시간
		$this->result["rRespCode"]		= $rBankRespCode;		//응답코드
		$this->result["resultMsg"]		= iconv("EUC-KR","UTF-8",$rVAMessage1) . " " . iconv("EUC-KR","UTF-8",$rVAMessage2);			//메세지

		$this->result["method"]			= $this->data["METHOD"];
		$this->result["mid"]			= $this->data["MID"];	// 상점 ID
		$this->result["tid"]			= $this->data["TID"];	// TID

	}
 }
