<?php

require_once dirname(__FILE__).'/WebParamGather.php';

class PayCommonWebParamGather implements WebParamGather{
	
	public function PayCommonWebParamGather(){
		
	}
	
	/**
	 * 
	 * @param $request
	 */
	public function gather($request){
		$webParam = new WebMessageDTO();
		
		
		
		$webParam->setParameter(TID,$request["TID"]);
		$webParam->setParameter(GOODS_CNT,$request["GoodsCnt"]);
		$webParam->setParameter(GOODS_NAME,$request["GoodsName"]);
		$webParam->setParameter(GOODS_AMT,$request["Amt"]);
		$webParam->setParameter(MOID,$request["Moid"]);
		//$webParam->setParameter(CURRENCY,$request["Currency"]);
		$webParam->setParameter(MID,$request["MID"]);
		$webParam->setParameter(MERCHANT_KEY,$request["EncodeKey"]);
		$webParam->setParameter(MALL_IP,$request["MallIP"]);
		$webParam->setParameter(USER_IP,$request["UserIP"]);
		$webParam->setParameter(MALL_RESERVED,$request["MallReserved"]);
		$webParam->setParameter(RETRY_URL,$request["RetryURL"]);
		$webParam->setParameter(MALL_USER_ID,$request["MallUserID"]);
		$webParam->setParameter(BUYER_NAME,$request["BuyerName"]);
		$webParam->setParameter(BUYER_AUTH_NO,$request["BuyerAuthNum"]);
		$webParam->setParameter(BUYER_TEL,$request["BuyerTel"]);
		$webParam->setParameter(BUYER_EMAIL,$request["BuyerEmail"]);
		$webParam->setParameter(PARENT_EMAIL,$request["ParentEmail"]);
		$webParam->setParameter(BUYER_ADDRESS,$request["BuyerAddr"]);
		$webParam->setParameter(BUYER_POST_NO,$request["BuyerPostNo"]);
		$webParam->setParameter(SUB_ID,$request["SUB_ID"]);
		
		return $webParam;
	}
}
?>