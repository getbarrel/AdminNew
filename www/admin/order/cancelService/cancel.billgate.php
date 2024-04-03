<?
/**
 * billgate 취소 모듈
 *
 * @author pyw
 * @date 2016.03.15
 */



include_once $_SERVER["DOCUMENT_ROOT"]."/shop/billgate/class/Message.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/shop/billgate/class/MessageTag.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/shop/billgate/class/ServiceCode.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/shop/billgate/class/Command.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/shop/billgate/class/ServiceBroker.php";


class billgate{

	private $result;

	public function __construct(){
	    $result = null;
	}

	public function cancelService($data){

		include_once $_SERVER["DOCUMENT_ROOT"]."/shop/billgate/config.php";

		//결제타입이 카드 또는 이체가 아닌데 들어오면 에러
		if( !($data["method"] == "1" || $data["method"] == "5")){
			$this->result["res_cd"] = "2002";  // 결과 코드
			$this->result["res_msg"] = "잘못된 결제타입이 들어왔습니다.[In cancel.billgate] ";// 결과 메시지
			return $this->result;
		}

		//취소 요청 파라메터
		$serviceId		= $data["SERVICE_ID"];		//테스트 아이디 일반결제 : 실시간:M1100147 카드:glx_api
		$orderId 		= $data["ORDER_ID"];		//취소 요청번호
		$orderDate 		= $data["ORDER_DATE"]; 		//취소 요청일시
		$transactionId 	= $data["TRANSACTION_ID"];	//취소건의 거래번호
		$amount 		= $data["DEAL_AMOUNT"];		//부분취소건의

		//---------------------------------------
		// API 인스턴스 생성
		//---------------------------------------		
		$reqMsg = new Message(); //요청 메시지
		$resMsg = new Message(); //응답 메시지
		$tag = new MessageTag(); //태그
		$svcCode = new ServiceCode(); //서비스 코드
		$cmd = new Command(); //Command
		$broker = new ServiceBroker($COMMAND, $CONFIG_FILE); //통신 모듈

		//---------------------------------------
		//Header 설정
		//---------------------------------------
		$reqMsg->setVersion("0100"); //버전 (0100)
		$reqMsg->setMerchantId($serviceId); //가맹점 아이디

		if($data["method"] == "1"){
			$reqMsg->setServiceCode($svcCode->CREDIT_CARD); //서비스코드 카드
		}elseif($data["method"] == "5"){
			$reqMsg->setServiceCode($svcCode->ACCOUNT_TRANSFER); //서비스코드 이체
		}

		if($data["method"] == "1" && $data["REQUIRE_TYPE"] != "all"){
			//카드결제 부분취소
			$reqMsg->setCommand($cmd->CANCEL_ADMIN_REQUEST);
		}elseif($data["method"] == "1"){
			//카드결제 취소 커맨드
			$reqMsg->setCommand($cmd->CANCEL_SMS_REQUEST);
		}else{
			//그 외 취소 커맨드
			$reqMsg->setCommand($cmd->CANCEL_REQUEST); //승인 취소 요청 Command
		}

		$reqMsg->setOrderId($orderId); //주문번호
		$reqMsg->setOrderDate($orderDate); //주문일시(YYYYMMDDHHMMSS)

		//---------------------------------------
		//Body 설정
		//---------------------------------------

		//승인 거래번호
		if($transactionId != NULL){
			$reqMsg->put($tag->TRANSACTION_ID, $transactionId);
		}

		//카드결제시 취소금액이 들어가는 정보.
		if($data["method"] == "1"){
			if($amount != NULL){
				$reqMsg->put($tag->DEAL_AMOUNT, $amount);
			}
		}

		//카드결제시 , 부분취소시 들어가는 정보.
		if($data["method"] == "1" && $data["REQUIRE_TYPE"] != "all"){
			if($REQUIRE_TYPE != NULL){
				$reqMsg->put($tag->REQUIRE_TYPE, $REQUIRE_TYPE);
			}
		}

		//---------------------------------------
		// 요청 전송
		//---------------------------------------
		$broker->setReqMsg($reqMsg); //요청 메시지 설정

		if($data["method"] == "1"){
			$broker->invoke($svcCode->CREDIT_CARD); //응답 요청
		}elseif($data["method"] == "5"){
			$broker->invoke($svcCode->ACCOUNT_TRANSFER); //응답 요청
		}

		$resMsg = $broker->getResMsg(); //응답 메시지 확인

		//---------------------------------------
		//요청 결과
		//---------------------------------------
		$msg = $resMsg->get($tag->RESPONSE_MESSAGE); //응답 메시지

		$RESPONSE_CODE = $resMsg->get($tag->RESPONSE_CODE);
		$RESPONSE_MESSAGE = $resMsg->get($tag->RESPONSE_MESSAGE);
		$DETAIL_RESPONSE_CODE = $resMsg->get($tag->DETAIL_RESPONSE_CODE);
		$DETAIL_RESPONSE_MESSAGE = $resMsg->get($tag->DETAIL_RESPONSE_MESSAGE);

		$this->result["res_cd"] = $RESPONSE_CODE.$DETAIL_RESPONSE_CODE;  // 결과 코드
		$this->result["res_msg"] = iconv("EUC-KR","UTF-8",$RESPONSE_MESSAGE)."_".iconv("EUC-KR","UTF-8",$DETAIL_RESPONSE_MESSAGE); // 결과 메시지

		return $this->result;
	}
 }
