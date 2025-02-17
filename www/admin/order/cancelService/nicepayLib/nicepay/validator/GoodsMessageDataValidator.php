<?php

/**
 * 
 * @author kblee
 *
 */
class GoodsMessageDataValidator{
	
	/**
	 * 
	 */
	public function GoodsMessageDataValidator(){
		
	}
	
	/**
	 * 
	 * @param $mdto
	 */
	public function validate($mdto){
		
		// 상품개수
		if($mdto->getParameter(GOODS_CNT) == null || $mdto->getParameter(GOODS_CNT) == ""){
			if(LogMode::isAppLogable()){
				$logJournal = NicePayLogJournal::getInstance();
				$logJournal->errorAppLog("상품개수 미설정 오류입니다.");
			}
			throw new ServiceException("V104","상품개수 미설정 오류입니다.");
		}
		
		// 상품명
		if($mdto->getParameter(GOODS_NAME) == null || $mdto->getParameter(GOODS_NAME) == ""){
			if(LogMode::isAppLogable()){
				$logJournal = NicePayLogJournal::getInstance();
				$logJournal->errorAppLog("상품명 미설정 오류입니다.");
			}
			throw new ServiceException("V401","상품명 미설정 오류입니다.");
		}
		
		// 금액
		if($mdto->getParameter(GOODS_AMT) == null || $mdto->getParameter(GOODS_AMT) == ""){
			if(LogMode::isAppLogable()){
				$logJournal = NicePayLogJournal::getInstance();
				$logJournal->errorAppLog("상품금액 미설정 오류입니다.");
			}
			throw new ServiceException("V402","상품금액 미설정 오류입니다.");
		}
		
		// 통화구분 
		if($mdto->getParameter(CURRENCY) == null || $mdto->getParameter(CURRENCY) == ""){
			if(LogMode::isAppLogable()){
				$logJournal = NicePayLogJournal::getInstance();
				$logJournal->errorAppLog("통화구분 미설정 오류입니다.");
			}
			throw new ServiceException("V203","통화구분 미설정 오류입니다.");
		}
	}
}

?>
