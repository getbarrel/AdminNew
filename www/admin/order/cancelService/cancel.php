<?
/**
 * 몰스토리 관리자 PG결제 취소 컨트롤러
 *
 * @author bgh
 * @date 2013.07.10
 */
include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.model.php");

class cancel{

    private $model; 		//DB 모델
 	private $pgName; 		//PG명
 	private $pgCon; 		//PG컨트롤러
 	private $receiveData; 	//입력받은 데이터
 	private $requestData; 	//PG사로 보낼 데이터
 	private $resultData; 	//PG사에서 받은 데이터
 	private $error;			//error메시지
 	private $logData;       //Log 데이터

 	public function __construct(){
 		$this->model = new cancelModel();
 	}
 	/**
 	 * 사용중인 PG모듈명 설정
 	 */
	private function setPgName(){
	   $this->pgName = $this->receiveData['settle_module'];
	}

	/**
 	 * 사용중인 PG모듈명 설정
 	 */
	private function setPgCon(){
		unset($this->pgCon);

		switch($this->pgName){
            case "nicepay":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.nicepay.php");
                $this->pgCon = new nicepay();
                break;
            case "nicepay_tx":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.nicepay_tx.php");
                $this->pgCon = new nicepay_tx();
                break;
			case "kcp":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.kcp.php");
				$this->pgCon = new kcp();
				break;
			case "payco":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.payco.php");
				$this->pgCon = new payco();
				break;
            case "billgate":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.billgate.php");   //21060315
                $this->pgCon = new billgate();
                break;
            case "kspay":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.kspay.php");      //21060315
				$this->pgCon = new kspay();
				break;
            case "payline":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.payline.php");    //21060315
                $this->pgCon = new payline();
                break;
            case "inipay_standard":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.inipay_standard.php");    //21060315
                $this->pgCon = new inipay_standard();
                break;
            case "lguplus":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.lguplus.php");
                $this->pgCon = new lguplus();
                break;
            case "naverpayPg":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.naverpayPg.php");
                $this->pgCon = new naverpayPg();
                break;
            case "eximbay":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.eximbay.php");
                $this->pgCon = new eximbay();
                break;
            case "toss":
                include ($_SERVER["DOCUMENT_ROOT"]."/admin/order/cancelService/cancel.toss.php");
                $this->pgCon = new toss();
                break;
			default:
				$this->error["code"] = "5001";
				$this->error["msg"] = "PG모듈을 확인하세요.(".$this->pgName.")";
				$this->errorHandler();
				break;
		}
	}

 	/**
 	 * 결제취소 호출
 	 *
 	 * @param {array} data
 	 * @return {array} result
 	 */
 	public function requestCancel($data){
 		$this->receiveData = $data;

		$this->setPgName();
		$this->setPgCon();

 		$makeResult = $this->makeCancelData();

 		if($makeResult == true){
 			$this->resultData = $this->pgCon->cancelService($this->requestData);
            if(!empty($this->resultData)){
 			    return $this->resultHandler();
            }else{
                $this->error["code"] = "5004";
                $this->error["msg"] = "18";
                $this->errorHandler();
            }
 		}else{
 			$this->error["code"] = "5002";
			$this->error["msg"] = "PG사로 보낼 데이터를 만들지 못했습니다.(function: requestCancel)";
			$this->errorHandler();
 		}

 	}

	/**
 	 * 에스크로 상태변경 호출
 	 *
 	 * @param {array} data
 	 * @return {array} result
 	 */
	public function  requestStatus($data){
		$this->receiveData = $data;

		$this->setPgName();
		$this->setPgCon();

		$makeResult = $this->makeStatuslData();

 		if($makeResult == true){

 			$this->resultData = $this->pgCon->statusService($this->requestData);
            if(!empty($this->resultData)){
 			    return $this->resultHandler();
            }else{
                $this->error["code"] = "5004";
                $this->error["msg"] = "18";
                $this->errorHandler();
            }
 		}else{
 			$this->error["code"] = "5002";
			$this->error["msg"] = "PG사로 보낼 데이터를 만들지 못했습니다.(function: requestCancel)";
			$this->errorHandler();
 		}
	}

	/**
	 * 에스크로 주문시 주문상태 PG사로 보낼 데이터 만들기
	 *
	 * @return {boolean} success / fail
	 */
	private function makeStatuslData(){
		$pgInfo = $this->model->getPgInfo($this->pgName);
        if(empty($this->receiveData["tno"])){
            //거래번호 없음
            $this->error["code"] = "5002";
            $this->error["msg"] = "원거래 번호 없음";
            $this->errorHandler();
        }

        $cancel_type = null;

		if(!empty($pgInfo)){
			switch($this->pgName){
				case "kcp":
					$this->requestData["kcp_id"] = $pgInfo["site_cd"];
					$this->requestData["kcp_key"] = $pgInfo["site_key"];
					//$this->requestData["kcp_id"] = "AO312";
					//$this->requestData["kcp_key"] = "3GnZTdJ8qgYfakX0NmvRubl__";
					$this->requestData["kcp_type"] = $pgInfo["service_type"];
                    $this->requestData["req_tx"] = $this->receiveData["req_tx"];
					$this->requestData["tno"] = $this->receiveData["tno"];
					$this->requestData["mod_type"] = $this->receiveData["mod_type"];
					if($this->receiveData["mod_type"] == "STE1"){
						$this->requestData["deli_numb"] = $this->receiveData["deli_numb"];
						$this->requestData["deli_corp"] = $this->receiveData["deli_corp"];
					}else if($this->receiveData["mod_type"] == "STE2" || $this->receiveData["mod_type"] == "STE4"){
						$this->requestData["refund_account"] = $this->receiveData["mod_account"];
						$this->requestData["refund_nm"] = $this->receiveData["mod_depositor"];
						$this->requestData["bank_code"] = $this->receiveData["mod_bankcode"];
					}else if($this->receiveData["mod_type"] == "STE9_V"){
						$this->requestData["refund_account"] = $this->receiveData["mod_account"];
						$this->requestData["refund_nm"] = $this->receiveData["mod_depositor"];
						$this->requestData["bank_code"] = $this->receiveData["mod_bankcode"];
					}else if($this->receiveData["mod_type"] == "STE9_VP"){
						$this->requestData["refund_account"] = $this->receiveData["mod_account"];
						$this->requestData["refund_nm"] = $this->receiveData["mod_depositor"];
						$this->requestData["bank_code"] = $this->receiveData["mod_bankcode"];
						$this->requestData["real_price"] = $this->receiveData["real_price"];
						$this->requestData["cancel_amount"] = $this->receiveData["cancel_amount"];
						$this->requestData["cancel_msg"] = "환불처리";
					}
					break;
				}
			return true;
		}else{
			return false;
		}
	}

	/**
	 * PG사로 보낼 데이터 만들기
	 *
	 * @return {boolean} success / fail
	 */
	private function makeCancelData(){
		$pgInfo = $this->model->getPgInfo($this->pgName);
		//$orderInfo = $this->model->getOrderInfo($this->receiveData["oid"],$this->receiveData["method"]);
        if(empty($this->receiveData["tid"])){
            //거래번호 없음
            $this->error["code"] = "5002";
            $this->error["msg"] = "원거래 번호 없음";
            $this->errorHandler();
        }

        $cancel_type = null;


		if(!empty($pgInfo)){
			switch($this->pgName){
				case "payco":

                    if($this->receiveData["real_price"] == $this->receiveData["cancel_amount"]){
                        $cancel_type = "ALL"; //전체취소

            		}else if($this->receiveData["real_price"] > $this->receiveData["cancel_amount"]){
                        if($this->receiveData["remain_price"] >= $this->receiveData["cancel_amount"]){
                            $cancel_type = "PART"; //부분취소

                        }else{
                            $this->error["code"] = "5006";
        					$this->error["msg"] = "취소 요청 금액 오류. 남은 결제금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
        					$this->errorHandler($this->error);
        					break;
                        }
            		}else{
      		            $this->error["code"] = "5005";
    					$this->error["msg"] = "취소 요청 금액 오류. 결재금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
    					$this->errorHandler($this->error);
    					break;
            		}

                    $this->requestData["pgInfo"] = $pgInfo;

					$this->requestData["cancelType"] = $cancel_type;
                    list($orderNo, $tid) = explode("|", $this->receiveData["tid"]);
                    $this->requestData["orderNo"] = $orderNo;
					$this->requestData["orderCertifyKey"] = $tid;
					$this->requestData["sellerOrderProductReferenceKey"] = $this->receiveData["oid"];
					$this->requestData["cancelTotalAmt"] = $this->receiveData["cancel_amount"];
					$this->requestData["cancelAmt"] = $this->receiveData["cancel_amount"];
					$this->requestData["requestMemo"] = $this->receiveData["cancel_msg"];
					$this->requestData["totalCancelTaxfreeAmt"] = $this->receiveData["cancel_tax_free_amount"];
					$this->requestData["totalCancelTaxableAmt"] = round($this->receiveData["cancel_tax_amount"]/1.1);  ;
					$this->requestData["totalCancelVatAmt"] = $this->receiveData["cancel_tax_amount"]-$this->requestData["totalCancelTaxableAmt"];
					$this->requestData["totalCancelPossibleAmt"] = $this->receiveData["remain_price"];
					$this->requestData["cancelDetailContent"] = str_replace(array("\"","'","\"","\n","\r"), array("¨","＇","＇"," "," "),$this->receiveData["reason"]);
					break;

				case "kcp":

					if($this->receiveData["real_price"] == $this->receiveData["cancel_amount"]){
						$this->requestData["cancel_type"] = "all";
					}else{
						if($this->receiveData["remain_price"] >= $this->receiveData["cancel_amount"]){
							$this->requestData["cancel_type"] = "part";
						}else{
							$this->error["code"] = "5006";
        					$this->error["msg"] = "취소 요청 금액 오류. 남은 결제금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
        					$this->errorHandler($this->error);
        					break;
						}
					}

					$this->requestData["kcp_id"] = $pgInfo["site_cd"];
					$this->requestData["kcp_key"] = $pgInfo["site_key"];
					$this->requestData["kcp_type"] = $pgInfo["service_type"];
                    $this->requestData["oid"] = $this->receiveData["oid"];
					$this->requestData["tid"] = $this->receiveData["tid"];
					$this->requestData["reason"] = $this->receiveData["reason"];
					$this->requestData["cancel_amount"] = $this->receiveData["cancel_amount"];
					$this->requestData["remain_price"] = $this->receiveData["remain_price"];

                    $this->requestData["method"] = $this->receiveData["method"];
                    $this->requestData["bank_code"] = $this->receiveData["bank_code"];
                    $this->requestData["bank_number"] = $this->receiveData["bank_number"];
                    $this->requestData["bank_owner"] = $this->receiveData["bank_owner"];

					/* 복함과세 거래건 관련
					$this->requestData["tax_flag"] = "TG03";
					$this->requestData["cancel_tax_amount"] = $this->receiveData["cancel_tax_amount"];
					$this->requestData["cancel_tax_free_amount"] = $this->receiveData["cancel_tax_free_amount"];
					$this->requestData["cancel_tax_free_amount"] = $this->receiveData["cancel_tax_free_amount"];
					*/

					break;

                case "nicepay":

                    //전체취소 및 부분취소 나이스페이 다시 수정해야함 2015-09-24 HONG
                    if($this->receiveData["real_price"] == $this->receiveData["cancel_amount"]){
                        $cancel_type = 0; //전체취소

                    }else if($this->receiveData["real_price"] > $this->receiveData["cancel_amount"]){
                        if($this->receiveData["remain_price"] >= $this->receiveData["cancel_amount"]){
                            $cancel_type = 1; //부분취소

                        }else{
                            $this->error["code"] = "5006";
                            $this->error["msg"] = "취소 요청 금액 오류. 남은 결제금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
                            $this->errorHandler($this->error);
                            break;
                        }
                    }else{
                        $this->error["code"] = "5005";
                        $this->error["msg"] = "취소 요청 금액 오류. 결재금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
                        $this->errorHandler($this->error);
                        break;
                    }
                    //$this->requestData["MID"] = $pgInfo[$this->pgName."_id"];
                    $this->requestData["MID"] = $pgInfo["mid"];
                    $this->requestData["CancelPwd"] = $pgInfo["cancel_pwd"];
                    $this->requestData["CancelAmt"] = $this->receiveData["cancel_amount"];
                    $this->requestData["CancelMsg"] = $this->receiveData["cancel_msg"];
                    $this->requestData["PartialCancelCode"] = $cancel_type;
                    $this->requestData["TID"] = $this->receiveData["tid"];

                    break;

                case "nicepay_tx":

                    //전체취소 및 부분취소 나이스페이 다시 수정해야함 2015-09-24 HONG
                    if($this->receiveData["real_price"] == $this->receiveData["cancel_amount"]){
                        $cancel_type = 0; //전체취소

                    }else if($this->receiveData["real_price"] > $this->receiveData["cancel_amount"]){
                        if($this->receiveData["remain_price"] >= $this->receiveData["cancel_amount"]){
                            $cancel_type = 1; //부분취소

                        }else{
                            $this->error["code"] = "5006";
                            $this->error["msg"] = "취소 요청 금액 오류. 남은 결제금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
                            $this->errorHandler($this->error);
                            break;
                        }
                    }else{
                        $this->error["code"] = "5005";
                        $this->error["msg"] = "취소 요청 금액 오류. 결재금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
                        $this->errorHandler($this->error);
                        break;
                    }
                    $this->requestData["MID"] = $pgInfo['mid'];
                    $this->requestData["CancelPwd"] = $pgInfo["cancel_pwd"];
                    $this->requestData["CancelAmt"] = $this->receiveData["cancel_amount"];
                    $this->requestData["CancelMsg"] = $this->receiveData["cancel_msg"];
                    $this->requestData["PartialCancelCode"] = $cancel_type;
                    $this->requestData["TID"] = $this->receiveData["tid"];

                    break;

                case "inipay_standard":

                    if($this->receiveData["real_price"] == $this->receiveData["cancel_amount"]){
                        $cancel_type = 0; //전체취소

                    }else if($this->receiveData["real_price"] > $this->receiveData["cancel_amount"]){
                        if($this->receiveData["remain_price"] >= $this->receiveData["cancel_amount"]){
                            $cancel_type = 1; //부분취소

                        }else{
                            $this->error["code"] = "5006";
                            $this->error["msg"] = "취소 요청 금액 오류. 남은 결제금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
                            $this->errorHandler($this->error);
                            break;
                        }
                    }else{
                        $this->error["code"] = "5005";
                        $this->error["msg"] = "취소 요청 금액 오류. 결재금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
                        $this->errorHandler($this->error);
                        break;
                    }
                    $this->requestData["MID"] = $pgInfo['mid'];
                    $this->requestData["CancelPwd"] = $pgInfo["cancel_pwd"];
                    $this->requestData["CancelAmt"] = $this->receiveData["cancel_amount"];
                    $this->requestData["CancelMsg"] = $this->receiveData["cancel_msg"];
                    $this->requestData["PartialCancelCode"] = $cancel_type;
                    $this->requestData["TID"] = $this->receiveData["tid"];

                    break;

                case "billgate":

                    if($this->receiveData["real_price"] == $this->receiveData["cancel_amount"]){
                        $this->requestData["cancel_type"] = "all";
                    }else{
                        if($this->receiveData["remain_price"] >= $this->receiveData["cancel_amount"]){
                            //부분취소
                            $this->requestData["cancel_type"] = "0000";
                        }elseif( ($this->receiveData["real_price"] - $this->receiveData["remain_price"]) == $this->receiveData["cancel_amount"]){
                            //전체금액 - 남은금액 == 취소금액 이면 나머지 전체취소
                            $this->requestData["cancel_type"] = "1000";
                        }else{
                            $this->error["code"] = "5006";
                            $this->error["msg"] = "취소 요청 금액 오류. 남은 결제금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
                            $this->errorHandler($this->error);
                            break;
                        }
                    }

                    //실시간 계좌이체일경우 전체 취소만 가능하다.
                    if($this->receiveData["method"] == 5 && $this->requestData["cancel_type"] == "0000"){
                        $this->error["code"] = "5007";
                        $this->error["msg"] = "실시간 계좌이체는 부분취소를 하실 수 없습니다.(전체취소만 가능합니다.)(function: makeCancelData)";
                        $this->errorHandler($this->error);
                        break;
                    }

                    $this->requestData["SERVICE_ID"] = $pgInfo["billgate_id"];              //서비스아이디
                    $this->requestData["REQUIRE_TYPE"] = $this->requestData["cancel_type"]; //취소타입. 전체취소일경우 빈값
                    $this->requestData["TRANSACTION_ID"] = $this->receiveData["tid"];       //TID
                    $this->requestData["ORDER_ID"] = $this->receiveData["oid"];             //OID
                    $this->requestData["ORDER_DATE"] = date("YmdHis");                 //취소시간
                    $this->requestData["DEAL_AMOUNT"] = $this->receiveData["cancel_amount"];//취소금액

                    $this->requestData["method"] = $this->receiveData["method"];//취소금액

                    //아래는 호환성을 위해서 남겨둔다.
                    $this->requestData["reason"] = $this->receiveData["reason"];
                    $this->requestData["cancel_amount"] = $this->receiveData["cancel_amount"];
                    $this->requestData["remain_price"] = $this->receiveData["remain_price"];
                    break;

                case "kspay":
					//전체취소 및 부분취소
                    if($this->receiveData["real_price"] == $this->receiveData["cancel_amount"]){
                        $canc_type = 0; //전체취소

            		}else if($this->receiveData["real_price"] > $this->receiveData["cancel_amount"]){
                        if($this->receiveData["remain_price"] >= $this->receiveData["cancel_amount"]){
                            $canc_type = 3; //부분취소

                        }else{
                            $this->error["code"] = "5006";
        					$this->error["msg"] = "취소 요청 금액 오류. 남은 결제금액 보다 작거나 같아야 합니다.(function: makeCancelData)".$this->receiveData["real_price"].":". $this->receiveData["cancel_amount"].":".$this->receiveData["remain_price"];
        					$this->errorHandler($this->error);
        					break;
                        }
            		}else{
      		            $this->error["code"] = "5005";
    					$this->error["msg"] = "취소 요청 금액 오류. 결재금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
    					$this->errorHandler($this->error);
    					break;
            		}

					$this->requestData["MID"] = $pgInfo[$this->pgName."_id"];
					$this->requestData["CancelAmt"] = $this->receiveData["cancel_amount"];
					$this->requestData["CancelMsg"] = $this->receiveData["cancel_msg"];
					$this->requestData["TID"] = $this->receiveData["tid"];


					$this->requestData["OID"] = $this->receiveData["oid"];
					$this->requestData["METHOD"] = $this->receiveData["method"];
					$this->requestData["BNAME"] = $this->receiveData["name"];
					$this->requestData["BMAIL"] = $this->receiveData["mail"];
					$this->requestData["BMOBILE"] = $this->receiveData["mobile"];
					$this->requestData["PNAME"] = $this->receiveData["pname"];

					//카드결제 부분취소 정보
					$this->requestData["CANC_TYPE"] = $canc_type;							// 0:전체취소 3:부분취소
					$this->requestData["CANC_AMT"] = $this->receiveData["cancel_amount"];	//취소금액
					$this->requestData["CANC_SEQ"] = $this->receiveData["refund_cnt"];	    // 취소 일련번호. 부분취소일경우 하나씩 증가해줘야한다. total_cancel

					//모델에서 부분취소는 1이다.
					if($canc_type == "3"){
						$this->requestData["PartialCancelCode"] = "1";
					}

                    print_r($this->requestData);

					//ApprovalType
					//1010 : 카드
					//2010 : 실시간계좌이체
					//6010 : 가상계좌
					//rApprovalType
					//1011 : 카드(실패)
					//2011 : 실시간계좌이체(실패)
					//6011 : 가상계좌(실패)
					if($this->receiveData["method"] == 1){	//카드
						$this->requestData["APPROVALTYPE"] = "1010";
						$this->requestData["RAPPROVALTYPE"] = "1011";
					}
					elseif($this->receiveData["method"] == 4){	//가상계좌
						$this->requestData["APPROVALTYPE"] = "6010";
						$this->requestData["RAPPROVALTYPE"] = "6011";
					}
					elseif($this->receiveData["method"] == 5){	//이체
						$this->requestData["APPROVALTYPE"] = "2010";
						$this->requestData["RAPPROVALTYPE"] = "2011";
					}

					break;

				case "payline":

                    if($this->receiveData["real_price"] == $this->receiveData["cancel_amount"]){
                        $cancel_type = 0; //전체취소

            		}else if($this->receiveData["real_price"] > $this->receiveData["cancel_amount"]){
                        if($this->receiveData["remain_price"] >= $this->receiveData["cancel_amount"]){
                            $cancel_type = 1; //부분취소

                        }else{
                            $this->error["code"] = "5006";
        					$this->error["msg"] = "취소 요청 금액 오류. 남은 결제금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
        					$this->errorHandler($this->error);
        					break;
                        }
            		}else{
      		            $this->error["code"] = "5005";
    					$this->error["msg"] = "취소 요청 금액 오류. 결재금액 보다 작거나 같아야 합니다.(function: makeCancelData)";
    					$this->errorHandler($this->error);
    					break;
            		}

					$this->requestData["MID"] = $pgInfo[$this->pgName."_id"];
					$this->requestData["CancelPwd"] = "alrammcom";
					$this->requestData["CancelMsg"] = $this->receiveData["cancel_msg"];
					$this->requestData["PartialCancelCode"] = $cancel_type;
					$this->requestData["TID"] = $this->receiveData["tid"];

					//테스트일때는 1004원만 결제 가능
					//취소 또한 1004원만 가능 (그 외 금액 오류로인해)
					if($pgInfo[$this->pgName."_type"] == "test"){
						$this->requestData["CancelAmt"] = "1004";
					}
					else{
						$this->requestData["CancelAmt"] = $this->receiveData["cancel_amount"];
					}

					break;

                case "lguplus":
                    $this->requestData["lguplus_tid"] = $this->receiveData["tid"];
                    $this->requestData["cancel_amount"] = $this->receiveData["cancel_amount"];
                    $this->requestData["cancel_tax_amount"] = $this->receiveData["cancel_tax_amount"];
                    $this->requestData["cancel_tax_free_amount"] = $this->receiveData["cancel_tax_free_amount"];
                    $this->requestData["remain_price"] = $this->receiveData["remain_price"];
                    $this->requestData["bank_code"] = $this->receiveData["bank_code"];
                    $this->requestData["bank_number"] = $this->receiveData["bank_number"];
                    $this->requestData["bank_owner"] = $this->receiveData["bank_owner"];
                    $this->requestData["mobile"] = $this->receiveData["mobile"];

                    if(!empty($this->receiveData["reason"])){
                        $this->requestData["reason"] = $this->receiveData["reason"];
                    }else{
                        $this->requestData["reason"] = "관리자 취소";
                    }
                    break;
                case "naverpayPg":
                case "eximbay":
                    $this->pgCon->setConfig($pgInfo);
                    $this->requestData = $this->receiveData;
                    break;
                case "toss":
                    $this->requestData = $this->receiveData;
                    $this->requestData["toss_api_key"] = $pgInfo["toss_api_key"];
                    break;
                default:
                    $this->error["code"] = "5001";
                    $this->error["msg"] = "PG코드를 확인하세요.(function: makeCancelData)";
                    $this->errorHandler();
                    break;
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 결과값 처리
	 */
 	private function resultHandler(){
		unset($this->logData);
 	    $this->logData["receiveData"] = $this->receiveData;
        switch($this->pgName){
			case "payco":
					if(trim($this->resultData["code"]) == "0"){
						$this->logData["cancelResult"]["code"] = "3000";
						$this->logData["cancelResult"]["msg"] = $this->resultData["message"];
						$this->model->insertLog($this->logData);
						$returnData["result"]="success";
						$returnData["request_data"]=$this->requestData;
						return $returnData;
					}else{
						 $this->logData["cancelResult"]["code"] = "3300";
						$this->logData["cancelResult"]["msg"] = "[".$this->resultData["code"]."]".$this->resultData["message"];
						$this->model->insertLog($this->logData);
						$returnData["result"]="fail";
						$returnData["msg"]=$this->resultData["message"];
						return $returnData;
					}
				break;
			case "kcp":
					if(trim($this->resultData["res_cd"]) == "0000"){
						$this->logData["cancelResult"]["code"] = "3000";
						$this->logData["cancelResult"]["msg"] = $this->resultData["res_msg"];
						$this->model->insertLog($this->logData);
						$returnData["result"]="success";
						$returnData["request_data"]=$this->requestData;
						return $returnData;
					}else{
						 $this->logData["cancelResult"]["code"] = "3300";
						$this->logData["cancelResult"]["msg"] = "[".$this->resultData["res_cd"]."]".$this->resultData["res_msg"];
						$this->model->insertLog($this->logData);
						$returnData["result"]="fail";
						$returnData["msg"]=$this->resultData["res_msg"];
						return $returnData;
					}
				break;
 			case "nicepay":
            case "nicepay_tx":
                //2014-04-09 2211 추가(계좌이체환불성공)
                if(trim($this->resultData["resultCode"]) == "2001" || trim($this->resultData["resultCode"]) == "2002" || trim($this->resultData["resultCode"]) == "2211"){
                    $this->logData["cancelResult"]["code"] = "3000";
                    $this->logData["cancelResult"]["msg"] = $this->resultData["resultMsg"];
                    $this->model->insertLog($this->logData);

                    //2014-05-19 Hong return을 배열로 던지게끔 처리(취소요청한 데이타도 던져주어서 필요한곳 사용하게끔!)
                    //return "success";
                    $returnData["result"]="success";
                    $returnData["request_data"]=$this->requestData;
                    return $returnData;
                }else{
                    $this->logData["cancelResult"]["code"] = "3300";
                    $this->logData["cancelResult"]["msg"] = "[".$this->resultData["resultCode"]."]".$this->resultData["resultMsg"];
                    $this->model->insertLog($this->logData);
                    //echo "<script>alert('[".$this->resultData["resultCode"]."] ".$this->resultData["resultMsg"]."');</script>";
                    //return "fail";
                    $returnData["result"]="fail";
                    $returnData["msg"]=$this->resultData["resultMsg"];
                    return $returnData;
                }
                break;
            case "inipay_standard":
                if(trim($this->resultData["resultCode"]) == "00"){
                    $this->logData["cancelResult"]["code"] = "3000";
                    $this->logData["cancelResult"]["msg"] = $this->resultData["resultMsg"];
                    $this->model->insertLog($this->logData);

                    //2014-05-19 Hong return을 배열로 던지게끔 처리(취소요청한 데이타도 던져주어서 필요한곳 사용하게끔!)
                    //return "success";
                    $returnData["result"]="success";
                    $returnData["request_data"]=$this->requestData;
                    return $returnData;
                }else{
                    $this->logData["cancelResult"]["code"] = "3300";
                    $this->logData["cancelResult"]["msg"] = "[".$this->resultData["resultCode"]."]".$this->resultData["resultMsg"];
                    $this->model->insertLog($this->logData);
                    //echo "<script>alert('[".$this->resultData["resultCode"]."] ".$this->resultData["resultMsg"]."');</script>";
                    //return "fail";
                    $returnData["result"]="fail";
                    $returnData["msg"]=$this->resultData["resultMsg"];
                    return $returnData;
                }
                break;
            case "billgate":
                if(trim($this->resultData["res_cd"]) == "000000"){
                    $this->logData["cancelResult"]["code"] = "3000";
                    $this->logData["cancelResult"]["msg"] = $this->resultData["res_msg"];
                    $this->model->insertLog($this->logData);
                    $returnData["result"]="success";
                    $returnData["request_data"]=$this->requestData;
                    return $returnData;
                }else{
                     $this->logData["cancelResult"]["code"] = "3300";
                    $this->logData["cancelResult"]["msg"] = "[".$this->resultData["res_cd"]."]".$this->resultData["res_msg"];
                    $this->model->insertLog($this->logData);
                    $returnData["result"]="fail";
                    $returnData["msg"]=$this->resultData["res_msg"];
                    return $returnData;
                }
                break;

            case "kspay":
				//여기 조건을 좀 더 추가해줘야 한다.
				if(trim($this->resultData["resultCode"]) == "O" && ( trim($this->resultData["rRespCode"]) == "0000" || strlen(trim($this->resultData["rRespCode"])) > 4) ){
                    $this->logData["cancelResult"]["code"] = "3000";
                    $this->logData["cancelResult"]["msg"] = $this->resultData["resultMsg"];
                    $this->model->insertLog($this->logData);

					$returnData["result"]="success";
					$returnData["request_data"]=$this->requestData;
                    return $returnData;
		 		}else{
		 		    $this->logData["cancelResult"]["code"] = "3300";
                    $this->logData["cancelResult"]["msg"] = "[".$this->resultData["method"]."]"."[".$this->resultData["resultCode"]."]"."[".$this->resultData["rRespCode"]."]".$this->resultData["resultMsg"];
		 		    $this->model->insertLog($this->logData);

					$returnData["result"]="fail";
					$returnData["msg"]=$this->resultData["resultMsg"];
					return $returnData;
		 		}
				break;

			case "payline":
		 		if(trim($this->resultData["resultCode"]) == "2001" || trim($this->resultData["resultCode"]) == "2002" || trim($this->resultData["resultCode"]) == "2211"){
                    $this->logData["cancelResult"]["code"] = "3000";
                    $this->logData["cancelResult"]["msg"] = $this->resultData["resultMsg"];
                    $this->model->insertLog($this->logData);

					$returnData["result"]="success";
					$returnData["request_data"]=$this->requestData;
                    return $returnData;
		 		}else{
		 		    $this->logData["cancelResult"]["code"] = "3300";
                    $this->logData["cancelResult"]["msg"] = "[".$this->resultData["resultCode"]."]".$this->resultData["resultMsg"];
		 		    $this->model->insertLog($this->logData);

					$returnData["result"]="fail";
					$returnData["msg"]=$this->resultData["resultMsg"];
					return $returnData;
		 		}
	 			break;

            case "lguplus":
                if(trim($this->resultData["resultCode"]) == "0000" || trim($this->resultData["resultCode"]) == "RF00" ){
                    $this->logData["cancelResult"]["code"] = "3000";
                    $this->logData["cancelResult"]["msg"] = $this->resultData["resultMsg"];
                    $this->model->insertLog($this->logData);

                    $returnData["result"]="success";
                    $returnData["request_data"]=$this->requestData;
                    return $returnData;
                }else{
                    $this->logData["cancelResult"]["code"] = "3300";
                    $this->logData["cancelResult"]["msg"] = "[".$this->resultData["resultCode"]."]".$this->resultData["resultMsg"];
                    $this->model->insertLog($this->logData);

                    $returnData["result"]="fail";
                    $returnData["msg"]=$this->resultData["resultMsg"];
                    return $returnData;
                }
                break;
            case "naverpayPg":
            case "eximbay":
            case "toss":
                if($this->resultData->result){
                    $returnData["result"]="success";
                    $returnData["request_data"]=$this->requestData;
                    $this->logData["cancelResult"]["msg"] = "성공";
                    $this->model->insertLog($this->logData);
                    return $returnData;
                }else{
                    $returnData["result"]="fail";
                    $returnData["msg"]=$this->resultData->message;
                    $this->logData["cancelResult"]["msg"] = $this->resultData->message;
                    $this->model->insertLog($this->logData);
                    return $returnData;
                }
                break;
	 		default:
	 			$this->error["code"] = "5001";
				$this->error["msg"] = "PG코드를 확인하세요.(function: resultHandler)";
				$this->errorHandler();
				break;
 		}

 	}

 	/**
 	 * 에러 메시지 처리
 	 */
 	private function errorHandler(){
 		if(!empty($this->error)){
 		     echo "<script>alert('Error[".$this->error["code"]."] ".$this->error["msg"]."');</script>";
             exit;
 		}
   }

 }
