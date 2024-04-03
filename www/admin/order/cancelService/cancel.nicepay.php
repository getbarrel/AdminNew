<?
/**
 * nicepay 취소 모듈
 * 
 * @author bgh
 * @date 2013.07.10
 */
require_once $_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/nicepayLib/nicepay/web/NicePayWEB.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/nicepayLib/nicepay/core/Constants.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/nicepayLib/nicepay/web/NicePayHttpServletRequestWrapper.php";

class nicepay{
 	
	private $data;
	private $httpRequestWrapper;
	private $nicepayWEB;
	private $result;
	
	public function __construct(){
	    $result = null;
	}
	
	public function cancelService($data){

		$this->data = $data;
        /** 1. Request Wrapper 클래스를 등록한다.  */ 
		$this->httpRequestWrapper = new NicePayHttpServletRequestWrapper($this->data);
		$_REQUEST = $this->httpRequestWrapper->getHttpRequestMap();
		
		/** 2. 소켓 어댑터와 연동하는 Web 인터페이스 객체를 생성한다.*/
		$this->nicepayWEB = new NicePayWEB();
		
		/** 2-1. 로그 디렉토리 설정 */
		$this->nicepayWEB->setParam("NICEPAY_LOG_HOME",$_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/log/nicepayLog/");
		
		/** 2-2. 이벤트로그 모드 설정(0: DISABLE, 1: ENABLE) */
		$this->nicepayWEB->setParam("APP_LOG","1");
		
		/** 2-3. 어플리케이션로그 모드 설정(0: DISABLE, 1: ENABLE) */
		$this->nicepayWEB->setParam("EVENT_LOG","1");
		
		/** 2-4. 암호화플래그 설정(N: 평문, S:암호화) */
		$this->nicepayWEB->setParam("EncFlag","S");
		
		/** 2-5. 서비스모드 설정(결제 서비스 : PY0 , 취소 서비스 : CL0) */
		$this->nicepayWEB->setParam("SERVICE_MODE", "CL0");
		
		/** 3. 결제취소 요청 */
		$this->responseDTO = $this->nicepayWEB->doService($_REQUEST);
		
		/** 4. 취소결과 */
		$this->result["resultCode"] = $this->responseDTO->getParameter("ResultCode"); // 결과코드 (정상 :2001(취소성공), 2002(취소진행중), 그 외 에러)
		$this->result["resultMsg"] = iconv("euc-kr","utf-8",$this->responseDTO->getParameter("ResultMsg"));   // 결과메시지
		$this->result["cancelAmt"] = $this->responseDTO->getParameter("CancelAmt");   // 취소금액
		$this->result["cancelDate"] = $this->responseDTO->getParameter("CancelDate");     // 취소일
		$this->result["cancelTime"] = $this->responseDTO->getParameter("CancelTime");   // 취소시간
		$this->result["cancelNum"] = $this->responseDTO->getParameter("CancelNum");   // 취소번호
		$this->result["payMethod"] = $this->responseDTO->getParameter("PayMethod");   // 취소 결제수단
		$this->result["mid"] = 	$this->responseDTO->getParameter("MID");              // 상점 ID
		$this->result["tid"] = $this->responseDTO->getParameter("TID");               // TID
		
		return $this->result;
	}
 }
