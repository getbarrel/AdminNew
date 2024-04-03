<?
/**
 * payline 취소 모듈
 *
 * @author pyw
 * @date 2016.02.22
 */


class payline{

	private $data;
	private $result;

	public function __construct(){
	    $result = null;
	}

	public function cancelService($data){

		/**************************
		 * 1. 라이브러리 인클루드 *
		 **************************/
		$b_admin = true;
		require($_SERVER[DOCUMENT_ROOT]."/shop/payline/lib/payLite.php");
		require($_SERVER[DOCUMENT_ROOT]."/shop/payline/lib/config.php");

		/***************************************
		 * 2. PaylineLite 클래스의 인스턴스 생성 *
		 ***************************************/
		$pay = new payLite;
		$pay->m_payHome = GLB_PAY_HOME;

		$pay->m_ssl = "true";

		$pay->m_ActionType = "CLO";
		$pay->m_CancelAmt = $data[CancelAmt];
		$pay->m_TID = $data[TID];
		$pay->m_CancelMsg = $data[CancelMsg];
		$pay->m_PartialCancelCode = $data[PartialCancelCode];
		$pay->m_CancelPwd = "shtest001";
		$pay->m_debug = "DEBUG";

		// PG에 접속하여 취소 처리를 진행.
		$pay->startAction();

		$this->result["resultCode"] = $pay->m_ResultData["ResultCode"]; // 결과코드 (정상 :2001(취소성공), 2002(취소진행중), 그 외 에러)
		$this->result["resultMsg"] = iconv("EUC-KR","UTF-8",$pay->m_ResultData["ResultMsg"]);   // 결과메시지
		$this->result["cancelAmt"] = $pay->m_ResultData["CancelAmt"];   // 취소금액
		$this->result["cancelDate"] = $pay->m_ResultData["CancelDate"];     // 취소일
		$this->result["cancelTime"] = $pay->m_ResultData["CancelTime"];   // 취소시간
		$this->result["cancelNum"] = $pay->m_ResultData["CancelNum"];   // 취소번호
		$this->result["payMethod"] = $pay->m_ResultData["PayMethod"];   // 취소 결제수단
		$this->result["mid"] = 	$pay->m_ResultData["MID"];              // 상점 ID
		$this->result["tid"] = $pay->m_ResultData["TID"];               // TID

		return $this->result;
	}
 }
