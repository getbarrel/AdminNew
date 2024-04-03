 
 
<html>
 
    <head><META http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="alternate" type="text/xml" href="/ti.asmx?disco" />
 
    <style type="text/css">
    
		BODY { color: #000000; background-color: white; font-family: Verdana; margin-left: 0px; margin-top: 0px; }
		#content { margin-left: 30px; font-size: .70em; padding-bottom: 2em; }
		A:link { color: #336699; font-weight: bold; text-decoration: underline; }
		A:visited { color: #6699cc; font-weight: bold; text-decoration: underline; }
		A:active { color: #336699; font-weight: bold; text-decoration: underline; }
		A:hover { color: cc3300; font-weight: bold; text-decoration: underline; }
		P { color: #000000; margin-top: 0px; margin-bottom: 12px; font-family: Verdana; }
		pre { background-color: #e5e5cc; padding: 5px; font-family: Courier New; font-size: x-small; margin-top: -5px; border: 1px #f0f0e0 solid; }
		td { color: #000000; font-family: Verdana; font-size: .7em; }
		h2 { font-size: 1.5em; font-weight: bold; margin-top: 25px; margin-bottom: 10px; border-top: 1px solid #003366; margin-left: -15px; color: #003366; }
		h3 { font-size: 1.1em; color: #000000; margin-left: -15px; margin-top: 10px; margin-bottom: 10px; }
		ul { margin-top: 10px; margin-left: 20px; }
		ol { margin-top: 10px; margin-left: 20px; }
		li { margin-top: 10px; color: #000000; }
		font.value { color: darkblue; font: bold; }
		font.key { color: darkgreen; font: bold; }
		font.error { color: darkred; font: bold; }
		.heading1 { color: #ffffff; font-family: Tahoma; font-size: 26px; font-weight: normal; background-color: #003366; margin-top: 0px; margin-bottom: 0px; margin-left: -30px; padding-top: 10px; padding-bottom: 3px; padding-left: 15px; width: 105%; }
		.button { background-color: #dcdcdc; font-family: Verdana; font-size: 1em; border-top: #cccccc 1px solid; border-bottom: #666666 1px solid; border-left: #cccccc 1px solid; border-right: #666666 1px solid; }
		.frmheader { color: #000000; background: #dcdcdc; font-family: Verdana; font-size: .7em; font-weight: normal; border-bottom: 1px solid #dcdcdc; padding-top: 2px; padding-bottom: 2px; }
		.frmtext { font-family: Verdana; font-size: .7em; margin-top: 8px; margin-bottom: 0px; margin-left: 32px; }
		.frmInput { font-family: Verdana; font-size: 1em; }
		.intro { margin-left: -15px; }
           
    </style>
 
    <title>
	BaroService_TI 웹 서비스
</title></head>
 
  <body>
 
    <div id="content">
 
      <p class="heading1">BaroService_TI PHP 샘플</p><br>
 
      <span>
          <p class="intro">바로빌 세금계산서 연동서비스</p>
      </span>
 
      <span>
 
          <p class="intro">다음 작업이 지원됩니다. 형식 정의를 보려면 <a href="https://testws.baroservice.com:8010/ti.asmx?WSDL">서비스 설명</a>을 참조하십시오. </p>
          
          
              <ul>
            
              <li>
                <a href="AddUserToCorp.php">AddUserToCorp</a>
                
                <span>
                  <br>회원사에 사용자 추가
                </span>
              </li>
              <p>
            
              <li>
                <a href="AttachFileByFTP.php">AttachFileByFTP</a>
                
                <span>
                  <br>FTP를 사용하여 계산서에 파일첨부,표시명추가[FTP 전송된 화일을 첨부합니다]
                </span>
              </li>
              <p>
            
              <li>
                <a href="CancelNTSSendReserve.php">CancelNTSSendReserve</a>
                
                <span>
                  <br>국세청 예약전송 취소, 예약한 전송건중 대기중인건만 취소 가능
                </span>
              </li>
              <p>
            
              <li>
                <a href="ChangeCorpManager.php">ChangeCorpManager</a>
                
                <span>
                  <br>회원사 관리자 변경
                </span>
              </li>
              <p>
            
              <li>
                <a href="ChangeNTSSendOption.php">ChangeNTSSendOption</a>
                
                <span>
                  <br>국세청 대량전송설정 변경
                </span>
              </li>
              <p>
            
              <li>
                <a href="CheckCERTIsValid.php">CheckCERTIsValid</a>
                
                <span>
                  <br>TEST
                </span>
              </li>
              <p>
            
              <li>
                <a href="CheckChargeable.php">CheckChargeable</a>
                
                <span>
                  <br>과금가능 여부 확인[과금관련]
                </span>
              </li>
              <p>
            
              <li>
                <a href="CheckCorpIsMember.php">CheckCorpIsMember</a>
                
                <span>
                  <br>바로빌회원사 여부 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="CheckIsValidTaxInvoice.php">CheckIsValidTaxInvoice</a>
                
                <span>
                  <br>세금계산서 등록전 유효성체크
                </span>
              </li>
              <p>
            
              <li>
                <a href="CheckMgtNumIsExists.php">CheckMgtNumIsExists</a>
                
                <span>
                  <br>발행자관리번호로 세금계산서 유무 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="DeleteAttachFile.php">DeleteAttachFile</a>
                
                <span>
                  <br>첨부된 파일 모두삭제[첨부된 모든파일을 삭제합니다.]
                </span>
              </li>
              <p>
            
              <li>
                <a href="DeleteAttachFileWithFileIndex.php">DeleteAttachFileWithFileIndex</a>
                
                <span>
                  <br>첨부된 파일 한개삭제[GetAttachedFileList로 확인된 FileIndex로 특정화일만 삭제합니다.]
                </span>
              </li>
              <p>
            
              <li>
                <a href="DeleteTaxInvoice.php">DeleteTaxInvoice</a>
                
                <span>
                  <br>세금계산서 삭제 [자체관리번호][임시저장상태와, 승인/발행 거부, 취소완료상태에서만 가능]
                </span>
              </li>
              <p>
            
              <li>
                <a href="DeleteTaxInvoiceIK.php">DeleteTaxInvoiceIK</a>
                
                <span>
                  <br>세금계산서 삭제[바로빌관리번호][임시저장상태와, 승인/발행 거부, 취소완료상태에서만 가능]
                </span>
              </li>
              <p>
            
              <li>
                <a href="ForceSendToNTS.php">ForceSendToNTS</a>
                
                <span>
                  <br>국세청강제 전송 요청, 승인요청, 취소요청등이 강제처리됨
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetAttachedFileList.php">GetAttachedFileList</a>
                
                <span>
                  <br>첨부된 파일리스트를 확인합니다.
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetBalanceCostAmount.php">GetBalanceCostAmount</a>
                
                <span>
                  <br>충전잔액 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetBaroBillURL.php">GetBaroBillURL</a>
                
                <span>
                  <br>바로빌 링크 연결 URL확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetCashChargeURL.php">GetCashChargeURL</a>
                
                <span>
                  <br>정액충전 URL 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetCertificateExpireDate.php">GetCertificateExpireDate</a>
                
                <span>
                  <br>등록한 공인인증서 만료일 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetCertificateRegistURL">GetCertificateRegistURL</a>
                
                <span>
                  <br>공인인증서 등록 URL 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetChargeUnitCost.php">GetChargeUnitCost</a>
                
                <span>
                  <br>발행단가 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetCorpMemberContacts.php">GetCorpMemberContacts</a>
                
                <span>
                  <br>바로빌회원사의 담당자 목록확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetEmailPublicKeys.php">GetEmailPublicKeys</a>
                
                <span>
                  <br>ASP업체 Email 목록확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetErrString.php">GetErrString</a>
                
                <span>
                  <br>오류코드의 상세설명문자열을 반환.
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetJicInRegistURL.php">GetJicInRegistURL</a>
                
                <span>
                  <br>직인 등록 URL을 획득합니다.
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetLinkedDocs.php">GetLinkedDocs</a>
                
                <span>
                  <br>연결된 문서 목록을 확인합니다.
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetLoginURL.php">GetLoginURL</a>
                
                <span>
                  <br>SSO지원을 위한 로그인 URL 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetNTSSendOption.php">GetNTSSendOption</a>
                
                <span>
                  <br>국세청 대량전송설정 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoice.php">GetTaxInvoice</a>
                
                <span>
                  <br>테스트중....
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoiceIK.php">GetTaxInvoiceIK</a>
                
                <span>
                  <br>테스트중....
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoiceLog.php">GetTaxInvoiceLog</a>
                
                <span>
                  <br>세금계산서에 대한 문서이력을 조회.
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoiceLogIK.php">GetTaxInvoiceLogIK</a>
                
                <span>
                  <br>세금계산서에 대한 문서이력을 조회.
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoicePopUpURL.php">GetTaxInvoicePopUpURL</a>
                
                <span>
                  <br>세금계산서 팝업으로 보기위한 URL제공
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoicePopUpURLIK.php">GetTaxInvoicePopUpURLIK</a>
                
                <span>
                  <br>세금계산서 팝업으로 보기위한 URL제공
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoicePopUpURLNK.php">GetTaxInvoicePopUpURLNK</a>
                
                <span>
                  <br>테스트중....
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoicePrintURL.php">GetTaxInvoicePrintURL</a>
                
                <span>
                  <br>인쇄용 팝업 URL 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoicePrintURLIK.php">GetTaxInvoicePrintURLIK</a>
                
                <span>
                  <br>인쇄용 팝업 URL 확인
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoiceState.php">GetTaxInvoiceState</a>
                
                <span>
                  <br>세금계산서 상세상태 확인 [자체관리번호]
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoiceStates.php">GetTaxInvoiceStates</a>
                
                <span>
                  <br>세금계산서 상태 확인 [자체관리번호][대량, 최대100건까지만 처리]
                </span>
              </li>
              <p>
            
              <li>
                <a href="GetTaxInvoiceStatesIK.php">GetTaxInvoiceStatesIK</a>
                
                <span>
                  <br>세금계산서 상태 확인 [바로빌관리번호][대량, 최대100건까지만 처리]
                </span>
              </li>
              <p>
            
              <li>
                <a href="IssueTaxInvoice.php">IssueTaxInvoice</a>
                
                <span>
                  <br>세금계산서 발행
                </span>
              </li>
              <p>
            
              <li>
                <a href="MakeDocLinkage.php">MakeDocLinkage</a>
                
                <span>
                  <br>문서간 연결을 맺습니다.
                </span>
              </li>
              <p>
            
              <li>
                <a href="Ping.php">Ping</a>
                
                <span>
                  <br>Ping~ Pong
                </span>
              </li>
              <p>
            
              <li>
                <a href="ProcTaxInvoice.php">ProcTaxInvoice</a>
                
                <span>
                  <br>세금계산서 처리[ProcType에 따라, 취소/거절등을 처리합니다.
                </span>
              </li>
              <p>
            
              <li>
                <a href="ReSendEmail.php">ReSendEmail</a>
                
                <span>
                  <br>세금계산서 상태에 따른 관련 Email 재전송[취소,거부는 전송불가능]
                </span>
              </li>
              <p>
            
              <li>
                <a href="ReSendSMS.php">ReSendSMS</a>
                
                <span>
                  <br>사업자에게 SMS문자를 발송합니다.[충전금액에서 차감]
                </span>
              </li>
              <p>
            
              <li>
                <a href="RegistBrokerTaxInvoice.php">RegistBrokerTaxInvoice</a>
                
                <span>
                  <br>위수탁세금계산서 등록
                </span>
              </li>
              <p>
            
              <li>
                <a href="RegistCorp.php">RegistCorp</a>
                
                <span>
                  <br>회원사가입 API
                </span>
              </li>
              <p>
            
              <li>
                <a href="RegistTaxInvoice.php">RegistTaxInvoice</a>
                
                <span>
                  <br>일반세금계산서 등록
                </span>
              </li>
              <p>
            
              <li>
                <a href="RemoveDocLinkage.php">RemoveDocLinkage</a>
                
                <span>
                  <br>문서간 연결을 해제합니다.
                </span>
              </li>
              <p>
            
              <li>
                <a href="ReserveSendToNTS.php">ReserveSendToNTS</a>
                
                <span>
                  <br>국세청 예약전송 요청[예약가능 기간은 현재시점 부터 익월 5일 18:00까지만 가능]
                </span>
              </li>
              <p>
            
              <li>
                <a href="SendToNTS.php">SendToNTS</a>
                
                <span>
                  <br>국세청으로 즉시전송을 요청
                </span>
              </li>
              <p>
            
              <li>
                <a href="UpdateBrokerTaxInvoice.php">UpdateBrokerTaxInvoice</a>
                
                <span>
                  <br>위수탁세금계산서 수정 [임시저장상태만 가능]
                </span>
              </li>
              <p>
            
              <li>
                <a href="UpdateCorpInfo.php">UpdateCorpInfo</a>
                
                <span>
                  <br>회원사 정보 수정
                </span>
              </li>
              <p>
            
              <li>
                <a href="UpdateTaxInvoice.php">UpdateTaxInvoice</a>
                
                <span>
                  <br>일반세금계산서 수정 [임시저장상태만 가능]
                </span>
              </li>
              <p>
            
              <li>
                <a href="UpdateUserInfo.php">UpdateUserInfo</a>
                
                <span>
                  <br>회원사용자 정보 수정
                </span>
              </li>
              <p>
            
              <li>
                <a href="UpdateUserPWD.php">UpdateUserPWD</a>
                
                <span>
                  <br>회원사용자 암호 변경
                </span>
              </li>
              <p>
            
              </ul>
            
      </span>
 
      
      
 
    <span>
        
    </span>
    
      
 
      
 
    
  </body>
</html>

