<?php
/**
 * CJmall 상품 / 주문 API 라이브러리
 * 
 * @version 0.4
 * @author bgh
 * @date 2013.12.04
 */
require 'cjmall.config.php';
require 'cjmall.class.php';
require $_SERVER['DOCUMENT_ROOT'].'/admin/openapi/standard.object.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/class/database.class';
//include_once $_SERVER ['DOCUMENT_ROOT'] . '/admin/class/layout.class';
class Lib_cjmall extends Call_cjmall {
	private $db;
	private $site_code;
	private $api_key;
	private $api_ticket;
	private $userInfo;
	private $result;
	private $error_type;
	private $error;
	private $mall_data_root;
	private $registState;
	
	private $marginRate;
	private $leadtime;

	private $zContactSeqNo;
	private $zSupShipSeqNo;
	private $zReturnSeqNo;
	private $zAsSupShipSeqNo;
	private $zAsReturnSeqNo;
	private $delivCostCd;
	
	public function __construct($dummy_key = '') {
		$this->db = new Database ();
		$this->result = new resultData();
		$this->site_code = $dummy_key;
		$this->userInfo = $this->getUserInfo ();
		$this->api_key = $this->userInfo ['api_key'];
		$this->api_ticket = $this->userInfo ['api_ticket'];


		if(! empty($_SESSION ['layout_config'] ['mall_data_root'])){
			$this->mall_data_root = $_SESSION ['layout_config'] ['mall_data_root'];
		}elseif(! empty($_SESSION ['admininfo'] ['mall_data_root'])){
			$this->mall_data_root = $_SESSION ['admininfo'] ['mall_data_root'];
		}elseif(! empty($layout_config['mall_data_root'])){
			$this->mall_data_root = $layout_config["mall_data_root"];
		}elseif(! empty($admininfo["mall_data_root"])){
			$this->mall_data_root = $admininfo["mall_data_root"];
		}else{
			$sql = "select mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
			$this->db->query($sql);
			$this->db->fetch();			

			$this->mall_data_root = $this->db->dt[mall_data_root];
		}

		/**
		** 중요 **
		marginRate 마진율
		leadtime 배송일
		**/

		/*
			오픈전 체크 사항
			ii. 배송비 코드 MD쪽에서 어떻게 할지 결정한후 피드백 받아야함
			iii. 출하지 및 배송 코드 관련 CJ 쪽에 전달 받아서 수정 처리 해야함
		*/

		/*
		15|02|10002|10005|10005|10005|10005|00265691
		스틸레디마또
		*/

		list($marginRate, $leadtime, $zContactSeqNo, $zSupShipSeqNo, $zReturnSeqNo, $zAsSupShipSeqNo, $zAsReturnSeqNo, $delivCostCd) = explode("|",$this->userInfo ['site_id']);

		$this->marginRate = $marginRate;
		$this->leadtime = $leadtime;

		$this->zContactSeqNo=$zContactSeqNo;//기본정보 - 협력사담당자
		$this->zSupShipSeqNo=$zSupShipSeqNo;//기본정보 - 출하지
		$this->zReturnSeqNo=$zReturnSeqNo;//기본정보 - 회수지
		$this->zAsSupShipSeqNo=$zAsSupShipSeqNo;//기본정보 - AS출하지
		$this->zAsReturnSeqNo=$zAsReturnSeqNo;//기본정보 - AS회수지
		$this->delivCostCd=$delivCostCd;
		
	}

	public function set_error_type($error_type) {
		$this->error_type = $error_type;
	}

	/**
	 * 사용자 정보 가져오기
	 * 티켓 발급을 위한 아이디 패스워드
	 *
	 */
	public function getUserInfo() {
		$sql = "SELECT * 
				FROM sellertool_site_info 
				WHERE site_code = '" . $this->site_code . "'";
		//echo $sql;
		$this->db->query ( $sql );
		if ($this->db->total) {
			return $this->db->fetch ();
		} else {
			$this->error ['code'] = '1001';
			$this->error ['msg'] = '제휴사 정보가 올바르지 않습니다.(cjmall)';
			if($this->error_type=="return"){
				return "[".$this->error ['code']."]".$this->error ['msg'];
			}else{
				$this->printError ();
			}
		}
	}
	
	//GS 상품 조회
	private function cjmallGoodsSearch($sdate,$edate) {
		$requestXmlBody='<?xml version="1.0" encoding="UTF-8"?>
		<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_03_07" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_03_07.xsd">
			<tns:vendorId>'.$this->api_key.'</tns:vendorId>
			<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			<tns:contents>
				<tns:sinstDtFrom>'.$sdate.'</tns:sinstDtFrom>
				<tns:sinstDtTo>'.$edate.'</tns:sinstDtTo>
				<tns:schnCd>30</tns:schnCd>
				<tns:vpn>'.$pid.'</tns:vpn>
			</tns:contents>
		</tns:ifRequest>';
		return $this->call ( CJMALL_URL, $requestXmlBody );
	}
	
	//GS 상품 조회하여 연동키 수정 처리
	public function cjmallCheckRegistGoods($sdate,$edate) {

		$sResult = $this->cjmallGoodsSearch($sdate,$edate);
		//print_r($sResult);
		if(count($sResult->unit) > 0){
			
			$checkItemCd = array();

			foreach( $sResult->unit as $unit ){
				$itemCd = (string)$unit->itemCd;//판매코드
				$unitCd = (string)$unit->unitCd;//단품코드
				$vpn = (string)$unit->vpn;//업체상품코드 (옵션 id)

				$sql = "SELECT pid FROM shop_product_options_detail WHERE id = '".$vpn."' ";
				$this->db->query($sql);
				if($this->db->total > 0){
					$this->db->fetch ();
					$pid = $this->db->dt['pid'];
					
					//상품 키 업데이트 체크
					if( ! $checkItemCd[ $itemCd ] ){
						$sql = "SELECT meta_id FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid' and shop_value = '".$pid."' and sellertool_key = 'itemCd' and sellertool_value='standByResponse' ";
						$this->db->query($sql);
						if($this->db->total > 0){
							$this->db->fetch ();
							$meta_id = $this->db->dt['meta_id'];
							
							$sql = "UPDATE sellertool_reponse SET sellertool_value='".$itemCd."' WHERE meta_id = '".$meta_id."' ";
							$this->db->query($sql);
							//echo $sql."<br/>";
						}

						$checkItemCd[ $itemCd ] = true;
					}
					
					//옵션키 체크
					$sql = "SELECT meta_id FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid|id' and shop_value = '".$pid."|".$vpn."' and sellertool_key = 'unitCd' ";
					$this->db->query($sql);
					if( !($this->db->total > 0) ){
						$sql = "insert into sellertool_reponse 
						(site_code,shop_key, shop_value, sellertool_key, sellertool_value, is_basic, regdate ) values('".$this->site_code."','pid|id','".$pid."|".$vpn."','unitCd','".$unitCd."','0', NOW())";
						$this->db->query($sql);
						//echo $sql."<br/>";
					}
				}
			}
		}

		exit;
	}

	/**
	 * 상품등록
	 * api 등록 절차
	 *
	 * @param string $pid        	
	 * @param string $add_info_id        	
	 */
	public function registGoods($pid = '', $add_info_id = '') {
		
		$sql = "select * from shop_product where id = '".$pid."' and admin in (select company_id from sellertool_not_company where site_code = '".$this->site_code."' and state = '1')  ";
		$this->db->query($sql);
		//연동 제한 셀러의 경우 프로세스 진입 시 처리 안되도록
		if( !empty( $this->db->total ) ){
			return;
		}
	
		//423 JEEP
		$sql = "select * from shop_product where id = '".$pid."' and brand in ('423') ";
		$this->db->query($sql);
		//연동 제한 브랜드 경우 프로세스 진입 시 처리 안되도록
		if( !empty( $this->db->total ) ){
			return;
		}

		$this->registState = '';//등록 상태값

		//*********** 승인후에 pid값을 던져 주기 떄문에 조회하여 업데이트 처리 해야함 START
		$sql = "SELECT meta_id, sellertool_value, regdate FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid' and shop_value = '".$pid."' and sellertool_key = 'itemCd' ";
		$this->db->query($sql);

		if( $this->db->total > 0 ){
			$this->db->fetch ();
			if($this->db->dt['sellertool_value'] == 'standByResponse'){
				$this->registState = 'standByResponse';
				$itemCd = '';
				$meta_id = $this->db->dt['meta_id'];
				$regdate = $this->db->dt['regdate'];
			}else{
				$this->registState = 'insertComplete';
				$itemCd = $this->db->dt['sellertool_value'];
				$meta_id = '';
				$regdate = '';
			}
		}else{
			$this->registState = 'tryInsert';
			$itemCd = '';
			$meta_id = '';
			$regdate = '';
		}

		//*********** 승인후에 pid값을 던져 주기 떄문에 조회하여 업데이트 처리 해야함 END
		
		if( $this->registState == 'standByResponse' ){
			//승인이 나야 수정할수가 있음 수정프로세스는 /cron/sellertool/cjmall/cjmall_check_regist_goods.php 스케줄링 돌면서 처리됨
			return;
		}else if( $this->registState == 'tryInsert' ){
			$requestXmlBody = $this->makeAddItemXml ( $pid, $add_info_id );
		}else if( $this->registState == 'insertComplete' ){
			$returnXml = $this->makeAddItemXml ( $pid, $add_info_id, $itemCd );
			
			//판매가격 수정
			$requestXmlBody = $returnXml['price'];
			if( ! empty($requestXmlBody) ){
				$result = $this->call ( CJMALL_URL, $requestXmlBody );
				if( count($result->itemPrices) > 0 ){
					foreach($result->itemPrices as $itemPrices){
						if($itemPrices->ifResult->successYn!="true"){
							$this->result->resultCode = "500";
							$this->result->message = $itemPrices->ifResult->errorMsg."[itemCD_ZIP=".$itemPrices->itemPrice->itemCD_ZIP."]";
							$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
						}
					}
				}
			}

			//판매가격 수정
			$requestXmlBody = $returnXml['stock'];
			if( ! empty($requestXmlBody) ){
				$result = $this->call ( CJMALL_URL, $requestXmlBody );
				if( count($result->ltSupplyPlans) > 0 ){
					foreach($result->ltSupplyPlans as $itemPrices){
						if($itemPrices->ifResult->successYn!="true"){
							$this->result->resultCode = "500";
							$this->result->message = $itemPrices->ifResult->errorMsg."[itemCD_ZIP=".$itemPrices->itemPrice->itemCD_ZIP."]";
							$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
						}
					}
				}
			}

			//판매판매 수정
			$requestXmlBody = $returnXml['state'];
			$result = $this->call ( CJMALL_URL, $requestXmlBody );
				
			if( count($result->itemStates) > 0 ){
				foreach($result->itemStates as $itemStates){
					if($itemStates->ifResult->successYn!="true"){
						$this->result->resultCode = "500";
						$this->result->message = $itemStates->ifResult->errorMsg."[itemCd_zip=".$itemStates->itemState->itemCd_zip."]";
						$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
					}
				}
			}

			//상품정보 수정
			$requestXmlBody = $returnXml['info'];
		}

		if(empty($this->error['code'])){

			$result = $this->call ( CJMALL_URL, $requestXmlBody );
	
			$_result_=$result->results->ifResult;

			if(! empty ($_result_)){

				//성공처리
				if($_result_->successYn=="true"){
					if($this->registState == 'tryInsert'){
						$sql = "insert into sellertool_reponse 
								(site_code,shop_key, shop_value, sellertool_key, sellertool_value, is_basic, regdate ) values('".$this->site_code."','pid','".$pid."','itemCd','standByResponse','0', NOW())";
						$this->db->query($sql);

						$this->result->message = '상품등록 완료 (MD 승인전)';
					}else{
						$this->result->message = '상품수정 완료';
					}

					$this->result->resultCode = '200';
					$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);

					//프론트 처리용 결과코드
					$resultCode = 'success';
					$this->result->resultCode = $resultCode;
				}else{
					if($this->registState != 'insertComplete'){
						if( substr_count($_result_->errorMsg,'협력사상품코드(Vpn)]가 이미 존재합니다') > 0 ){
							$sql = "insert into sellertool_reponse 
									(site_code,shop_key, shop_value, sellertool_key, sellertool_value, is_basic, regdate ) values('".$this->site_code."','pid','".$pid."','itemCd','standByResponse','0', NOW())";
							$this->db->query($sql);
						}
					}
					$this->result->message = $_result_->errorMsg;
					$this->result->resultCode = "500";
					//임시로 차후 주석처리!
					$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
					$resultCode = 'fail';
					$this->result->resultCode = $resultCode;
				}

				return $this->result;
			}else{
				$this->result->resultCode = "500";
				$this->result->message = "연동에 실패 하였습니다[응답없음]";
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
				$resultCode = 'fail';
				$this->result->resultCode = $resultCode;
				return $this->result;
			}

		}else{
			$this->result->resultCode = "500";
			$this->result->message = $this->error ['msg'];
			$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
			$this->result->resultCode = 'fail';
			return $this->result;
		}
	}
	
	/**
	 * xml생성
	 *
	 * @param string $pid        	
	 */
	 
	private function makeAddItemXml($pid = '', $add_info_id = '', $itemCd = '') {
		global $HEAD_OFFICE_CODE;

		$this->error ['code'] = "";
		$this->error ['msg'] = "";
		
		$sql = "SELECT * FROM " . TBL_SHOP_PRODUCT . " WHERE id = '" . $pid . "'";
		$this->db->query ( $sql );

		if ($this->db->total) {
			$pinfo = $this->db->fetch (); // 상품정보
			$target_cate = $this->getTargetCategory ( $pid ); // 연계된 카테고리 정보

			if (empty ( $target_cate )) {
				$this->error ['code'] = '2001';
				$this->error ['msg'] = '카테고리 연동이 필요합니다.';
				return;
			}
		}else{
			$this->error ['code'] = '9999';
			$this->error ['msg'] = '상품을 다시 확인 바랍니다.';
			return;
		}

		//packInd A-진행, I-일시중단
		if( $pinfo['state']=='1' && $pinfo['disp'] == '1' ){
			$packInd = 'A';
		}else{
			$packInd = 'I';
		}

		if ( $packInd == 'I' && $this->registState == 'tryInsert' ) {
			$this->error ['code'] = '2001';
			$this->error ['msg'] = '판매중(노출)상태가 아닙니다.';
			return;
		}
		
		$target_itemtype = $this->getTargetItemType ( $pid ); // 연계된 품목 정보
		if (empty ( $target_itemtype )) {
			$this->error ['code'] = '2001';
			$this->error ['msg'] = '품목(상품)분류 연동이 필요합니다.';
			return;
		}

		$target_brand = $this->getTargetEtc ( $pinfo['brand'] , 'B' ); // 브랜드
		if (empty ( $target_brand )) {
			$this->error ['code'] = '2001';
			$this->error ['msg'] = '브랜드 연동이 필요합니다.';
			return;
		}else{

			if($target_brand[delivery_policy_code]){
				$this->delivCostCd = $target_brand[delivery_policy_code]; //배송비 코드
			}else{
				$this->error ['code'] = '2001';
				$this->error ['msg'] = '브랜드별 배송정책 코드가 필요합니다.';
				return;
			}
			if($target_brand[return_policy_code]){
				$this->zSupShipSeqNo = $target_brand[return_policy_code]; //상품출고지주소코드
				$this->zReturnSeqNo = $target_brand[return_policy_code];
				$this->zAsSupShipSeqNo = $target_brand[return_policy_code];
				$this->zAsReturnSeqNo = $target_brand[return_policy_code];
			}else{
				$this->error ['code'] = '2001';
				$this->error ['msg'] = '브랜드별 상품출고지 코드가 필요합니다.';
				return;
			}
			if($target_brand[supplyer_commission]){
				$this->marginRate = $target_brand[supplyer_commission]; //협력사지급율
			}else{
				$this->error ['code'] = '2001';
				$this->error ['msg'] = '브랜드별 협력사 지급율 정보가 필요합니다.';
				return;
			}
		}

		$target_origin = $this->getTargetEtc ( $pinfo['origin'] , 'N' ); // 제조국
		if (empty ( $target_origin )) {
			$this->error ['code'] = '2001';
			$this->error ['msg'] = '제조국(원산지) 연동이 필요합니다.';
			return;
		}

		$target_company = $this->getTargetEtc ( $pinfo['company'] , 'C' ); // 제조사
		if (empty ( $target_company )) {
			$this->error ['code'] = '2001';
			$this->error ['msg'] = '제조사 연동이 필요합니다.';
			return;
		}


		$options = $this->makeOptionArray($pinfo['id'], $pinfo);
		if ( ! ( count( $options ) > 0 ) ) {
			$this->error ['code'] = '2002';
			$this->error ['msg'] = '재고관리 옵션은 필수입니다.';
			return;
		}

		$image_server_domain = IMAGE_SERVER_DOMAIN;
		if( empty($image_server_domain) ){
			$image_server_domain = "http://". $_SERVER['SERVER_NAME'];
		}else{
			$image_server_domain = substr($image_server_domain,0,-1);
		}


//		include_once($_SERVER['DOCUMENT_ROOT'].'/class/jglory/shoplinker/SLPObjectBuilder.php');// - make_apply_name
//		 $SLP = new SLPObjectBuilder();
		//($mall_code, $sale_type, $brand_name, $product_code, $product_name)
//		$pname = $SLP->make_apply_name(MALL_CODE_INTERPARK,$pinfo[product_sale_type],$pinfo[brand_name],$pinfo[pcode],$pinfo[pname]);
		$pname = $pinfo[pname];
		$zLocalBolDesc = mb_strimwidth($pinfo[pname], 0, 36, "...", "UTF8");
		$zlocalCcDesc = mb_strimwidth($pinfo[pname], 0, 16, "...", "UTF8");
		

		$this->db->query("SELECT cid FROM ".TBL_SELLERTOOL_PRODUCT_RELATION." WHERE pid = '".$pinfo["id"]."' and cid like '006%' ");
		if($this->db->total){
			//$childYn = "Y";
			$certItemRequireYn = 'Y';
		}else{
			//$childYn = "N";
			$certItemRequireYn = 'N';
		}

		/*
		vatCode
		S : 과세
		E : 면세
		N : 비과세
		Z : 영세
		*/
		if( $pinfo[surtax_yorn] == 'Y' ){
			$vatCode = "E";
		}else{
			$vatCode = "S";
		}

		/*
		zDeliveryType
		10 : 센터배송
		20 : 협력사배송
		30 : 직택배
		35 : 직택배Ⅱ
		40 : 직송
		99 : 배송없음
		*/
		$zDeliveryType = 20;
		
		/*
		zShippingMethod
		10 : 택배배송
		20 : 설치상품
		30 : 배달서비스
		40 : 우편/등기배송
		*/
		$zShippingMethod = "10";

		$sql = "select dt.* from 
		shop_product_delivery pd ,
		shop_delivery_template dt
		where pd.pid='".$pid."' and pd.dt_ix=dt.dt_ix";
		$this->db->query($sql);
		$this->db->fetch();
		$dt_ix_template = $this->db->fetch();
		
		$courier = $this->getDeleveryCompanyCode($dt_ix_template['tekbae_ix']);

		if($dt_ix_template['delivery_policy'] == '6' || $dt_ix_template['delivery_policy'] == '4'){
			$this->error ['code'] = '2002';
			$this->error ['msg'] = '수량 단위별 배송비 정책은 사용할 수 없습니다.';
			return;
		}

		if( $dt_ix_template['delivery_policy']=='1' ){//무료배송
			$deliveryHomeCost = 0;
		}elseif( $dt_ix_template['delivery_policy']=='2' ){//고정
			$deliveryHomeCost = $dt_ix_template['delivery_price'];
		}elseif( $dt_ix_template['delivery_policy']=='3' ){// 결제금액당
			$sql = "select * from shop_delivery_terms where dt_ix='".$dt_ix_template['dt_ix']."' and delivery_basic_terms <= '".$pinfo['sellprice']."' order by delivery_basic_terms asc limit 1";
			$this->db->query($sql);
			if($this->total > 0){
				$dt_ix_terms = $this->db->fetch();
			}else{
				$sql = "select * from shop_delivery_terms where dt_ix='".$dt_ix_template['dt_ix']."' and delivery_basic_terms >= '".$pinfo['sellprice']."' order by delivery_basic_terms asc limit 1";
				$this->db->query($sql);
				$dt_ix_terms = $this->db->fetch();
			}

			$deliveryHomeCost = $dt_ix_terms['delivery_price'];
		}

		if( $deliveryHomeCost=='' ){
			$deliveryHomeCost = 0;
		}
		
		/*
		//delivCostType
		01 : 일반배송
		02 : 배송없음
		03 : 바로사용
		04 : 착불
		*/
		if( $dt_ix_template['delivery_basic_policy']=='2' ){//착불
			$delivCostType = '04';
		}else{
			$delivCostType = '01';
		}


		/*
		zreturnNotReqInd
		10	배송구분적용
		20	직회수
		*/
		$zreturnNotReqInd = 10;
	
		/*
		zCostomMadeInd
		Enum(Y=주문제작,N=주문제작안함)
		*/
		$zCostomMadeInd = 'N';
		
		/*
		stockMgntLevel
		Enum(1=판매코드,2=단품코드)
		*/
		$stockMgntLevel = '2';
		
		/*
		leadtime
		00	당일배송
		01	당일출고
		02	익일출고
		03	2일후출고
		04	4일
		05	5일
		06	6일
		07	7일
		08	8일
		09	9일
		10	10일
		11	11일
		12	12일
		13	13일
		14	14일
		15	15일
		*/
		$leadtime=$this->leadtime;

		/*
		lowpriceInd
		Enum(Y=유료배송,N=무료배송)
		*/
		if($deliveryHomeCost > 0){
			$lowpriceInd = 'Y';
		}else{
			$lowpriceInd = 'N';
		}

		/*
		delayShipRewardIind
		Enum(Y=지연보상,N=지연보상안함)
		*/
		$delayShipRewardIind='N';


		/*
		reserveDayInd
		N-주문즉시 출하지시
		Y-최초공급가능일 출하지시_Default
		*/
		$reserveDayInd='Y';
		
		/*
		zContactSeqNo,zSupShipSeqNo,zReturnSeqNo,zAsSupShipSeqNo,zAsReturnSeqNo
		주소지 정보 파트너시스템에서 주소지코드 조회하여 입력하고 없는 경우 파트너에서 직접 등록
		*/
		$zContactSeqNo=$this->zContactSeqNo;
		$zSupShipSeqNo=$this->zSupShipSeqNo;
		$zReturnSeqNo=$this->zReturnSeqNo;
		$zAsSupShipSeqNo=$this->zAsSupShipSeqNo;
		$zAsReturnSeqNo=$this->zAsReturnSeqNo;
		
		$startSaleDt = date('Y-m-d');
		$endSaleDt = '9999-12-01';

		if( $pinfo['is_sell_date']=='1' ){
			//$startSaleDt = date('Y-m-d',strtotime( $pinfo['sell_priod_sdate'] ));
			$endSaleDt = date('Y-m-d',strtotime( $pinfo['sell_priod_edate'] ));
		}

		$keyword = str_replace(",",";",$pinfo['search_keyword']);
		$keywords = explode(';',$keyword);
		$keywords = array_slice($keywords,0,5);
		$keyword = implode(";",$keywords);

		$basicinfo = str_replace("http://".$_SERVER['SERVER_NAME']."/data","/data",$pinfo["basicinfo"]); //상세 이미지 url에 도메인 추가
		$basicinfo = str_replace("http://www.".$_SERVER['SERVER_NAME']."/data","/data",$basicinfo); //상세 이미지 url에 도메인 추가
		$basicinfo = str_replace("/data","http://".$_SERVER['SERVER_NAME']."/data",$basicinfo); //상세 이미지 url에 도메인 추가
		//$data_text_convert = "<img src='http://www.enter6.co.kr/data/entersix_data/images/banner/39/0908_976.jpg'> ";
		$data_text_convert .= str_replace("<IMG","<img",$basicinfo);
		$data_text_convert = str_replace('"',"&quot;",$data_text_convert);
		$data_text_convert = str_replace("'","&quot;",$data_text_convert);
		preg_match_all("|<img .*src=&quot;(.*)&quot;.*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);
		for($i=0;$i < count($out[1]);$i++){

			if(substr_count($out[1][$i],"http://")==0 && substr_count($out[1][$i],"https://")==0){
				$data_text_convert = str_replace($out[1][$i],$image_server_domain.$out[1][$i],$data_text_convert);
			}
		}
 		//$data_text_convert .= "<img src='http://www.enter6.co.kr/data/entersix_data/templet/entersix/images/entersix_delivery_img.jpg' alt='' border='0'> ";
		$basicinfo = str_replace("&quot;",'"',$data_text_convert);

		//특수문자 예외처리
		$basicinfo = str_replace("",'-',$basicinfo);
		
		$img_array = $this->getImageArray( $pinfo['id'] );
		
		$Official = $this->makeOfficialNoticeArray($pinfo['id']);
		
		if( $this->registState == 'tryInsert' ){//등록 Xml

			$xml = '<?xml version="1.0" encoding="UTF-8"?>';
			$xml .= '
			<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_03_01" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_03_01.xsd">';
				$xml .= '
				<tns:vendorId>'.$this->api_key.'</tns:vendorId>';//협력업체코드
				$xml .= '
				<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>';//인증키
				$xml .= '
				<tns:good>';
					$xml .= '
					<tns:chnCls>30</tns:chnCls>';//상품분류체계 - 가등록채널구분
					$xml .= '
					<tns:tGrpCd>'.$target_itemtype['target_cid'].'</tns:tGrpCd>';//상품분류체계 - 상품분류
					$xml .= '
					<tns:uniqBrandCd>'.$target_brand['target_code'].'</tns:uniqBrandCd>';//상품분류체계 - 브랜드
					$xml .= '
					<tns:giftInd>Y</tns:giftInd>';//상품분류체계 - 상품구분
					$xml .= '
					<tns:uniqMkrNatCd>'.$target_origin['target_code'].'</tns:uniqMkrNatCd>';//상품분류체계 - 제조국
					$xml .= '
					<tns:uniqMkrCompCd>'.$target_company['target_code'].'</tns:uniqMkrCompCd>';//상품분류체계 - 제조사
					$xml .= '
					<tns:itemDesc><![CDATA['.$pname.']]></tns:itemDesc>';//기본정보 - 상품명
					$xml .= '
					<tns:zLocalBolDesc><![CDATA['.$zLocalBolDesc.']]></tns:zLocalBolDesc>';//기본정보 - 운송장명
					$xml .= '
					<tns:zlocalCcDesc><![CDATA['.$zlocalCcDesc.']]></tns:zlocalCcDesc>';//기본정보 - SMS상품명
					$xml .= '
					<tns:vatCode>'.$vatCode.'</tns:vatCode>';//기본정보 - 과세형태
					$xml .= '
					<tns:zDeliveryType>'.$zDeliveryType.'</tns:zDeliveryType>';//기본정보 - 배송구분
					$xml .= '
					<tns:zShippingMethod>'.$zShippingMethod.'</tns:zShippingMethod>';//기본정보 - 배송유형
					$xml .= '
					<tns:courier>'.$courier.'</tns:courier>';//기본정보 - 택배사
					$xml .= '
					<tns:deliveryHomeCost>'.$deliveryHomeCost.'</tns:deliveryHomeCost>';//기본정보 - 배송비
					$xml .= '
					<tns:zreturnNotReqInd>'.$zreturnNotReqInd.'</tns:zreturnNotReqInd>';//기본정보 - 회수구분
					//zJointPackingQty	기본정보 - 합포장단위
					$xml .= '
					<tns:zCostomMadeInd>'.$zCostomMadeInd.'</tns:zCostomMadeInd>';//기본정보 - 주문제작여부
					$xml .= '
					<tns:stockMgntLevel>'.$stockMgntLevel.'</tns:stockMgntLevel>';//기본정보 - 재고관리레벨
					//$xml .= '
					//<tns:leadtime>'.$leadtime.'</tns:leadtime>';//기본정보 - 리드타임
					//leadtimeChgRsn	기본정보 - 적용사유
					$xml .= '
					<tns:lowpriceInd>'.$lowpriceInd.'</tns:lowpriceInd>';//기본정보 - 유료배송여부
					$xml .= '
					<tns:delayShipRewardIind>'.$delayShipRewardIind.'</tns:delayShipRewardIind>';//기본정보 - 지연보상여부
					//packingMethod	기본정보 - 입고형태
					//zOrderMaxQty	기본정보 - 1회최대주문수량
					//zDayOrderMaxQty	기본정보 - 1일최대주문수량
					$xml .= '
					<tns:reserveDayInd>'.$reserveDayInd.'</tns:reserveDayInd>';//기본정보 - 예약배송방식
					$xml .= '
					<tns:zContactSeqNo>'.$zContactSeqNo.'</tns:zContactSeqNo>';//기본정보 - 협력사담당자
					$xml .= '
					<tns:zSupShipSeqNo>'.$zSupShipSeqNo.'</tns:zSupShipSeqNo>';//기본정보 - 출하지
					$xml .= '
					<tns:zReturnSeqNo>'.$zReturnSeqNo.'</tns:zReturnSeqNo>';//기본정보 - 회수지
					$xml .= '
					<tns:zAsSupShipSeqNo>'.$zAsSupShipSeqNo.'</tns:zAsSupShipSeqNo>';//기본정보 - AS출하지
					$xml .= '
					<tns:zAsReturnSeqNo>'.$zAsReturnSeqNo.'</tns:zAsReturnSeqNo>';//기본정보 - AS회수지
					//cpnItemYn	기본정보 - 이용권상품 여부
					/*
					$xml .= '
					<tns:childYn>'.$childYn.'</tns:childYn>';//기본정보 - 어린이제품 여부
					if($childYn=='Y'){
						//400017 : 어린이제품 안전특별법 안전인증
						//400018 : 어린이제품 안전특별법 안전확인
						//000000 : 공급자적합성확인대상"
						$xml .= '
						<tns:certChildCd>400017</tns:certChildCd>';//certChildCd	기본정보 - 안정인증항목구분
					}
					*/
					$xml .= '
					<tns:certItemRequireYn>'.$certItemRequireYn.'</tns:certItemRequireYn>'; //certItemRequireYn 기본정보 - 인증항목필수여부
					$xml .= '
					<tns:delivCostCd>'.$this->delivCostCd.'</tns:delivCostCd>';//기본정보 - 배송비 코드
					$xml .= '
					<tns:delivCostType>'.$delivCostType.'</tns:delivCostType>';//기본정보 - 배송비속성 코드

					/*
					0 : 빠른배송 불가(Default)
					1 : 빠른배송 가능
					*/
					$xml .= '
					<tns:fastDelivYn>0</tns:fastDelivYn>';//기본정보 - 빠른배송여부


					//unitType	단품 Type
					if( count($options) > 0 ){
						foreach($options as $option){
						$xml .= '
						<tns:unit>';//단품정보
							$xml .= '
							<tns:unitNm><![CDATA['.$option['option_div'].']]></tns:unitNm>';//단품정보 - 단품상세
							$xml .= '
							<tns:unitRetail>'.$option['option_price'].'</tns:unitRetail>';//단품정보 - 판매가
							/*
							unitCost
							"* 마진율 확인요함
							1. 과세상품 : 매입원가(VAT제외) = Round(판매가/1.1 - 0.1 * (판매가/1.1)), 0)
							2. 면세상품 : 매입원가(VAT제외) = Round(판매가 - 0.1 * 판매가, 0)"
							*/
							//$option_price = $option['option_price'] * $this->marginRate;
							//$option_price = ($option['option_price'] * ( 100 - $this->marginRate) / 100);
							
							if( $vatCode == 'E' ){
								$unitCost = round( $option['option_price'] - ($this->marginRate/100) * $option['option_price'], 0);
							}else{
								$unitCost = round( $option['option_price']/1.1 - ($this->marginRate/100) * ($option['option_price']/1.1), 0);
							}

							$xml .= '
							<tns:unitCost>'.$unitCost.'</tns:unitCost>';//단품정보 - 매입원가
							$xml .= '
							<tns:availableQty>'.$option['option_stock'].'</tns:availableQty>';//단품정보 - 공급가능수량
							$xml .= '
							<tns:leadTime>'.$leadtime.'</tns:leadTime>';//단품정보 - 리드타임
							$xml .= '
							<tns:unitApplyRsn>20</tns:unitApplyRsn>';//단품정보 - 적용사유 상품포장 으로 고정입력
							$xml .= '
							<tns:startSaleDt>'.$startSaleDt.'</tns:startSaleDt>';//단품정보 - 판매시작일자
							$xml .= '
							<tns:endSaleDt>'.$endSaleDt.'</tns:endSaleDt>';//단품정보 - 판매종료일자
							$xml .= '
							<tns:vpn>'.$option['id'].'</tns:vpn>';//단품정보 - 협력사상품코드
						$xml .= '
						</tns:unit>';
						}
					}
					$xml .= '
					<tns:mallitem>';//CJmall상품정보
						$xml .= '
						<tns:mallItemDesc><![CDATA['.$pname.']]></tns:mallItemDesc>';//CJmall상품정보 - CJmall상품명
						$xml .= '
						<tns:keyword><![CDATA['.$keyword.']]></tns:keyword>';//CJmall상품정보 - 검색키워드
						$xml .= '
						<tns:mallCtg>';//CJmall카테고리 정보
							$xml .= '
							<tns:mainInd>Y</tns:mainInd>';//메인카테고리여부
							$xml .= '
							<tns:ctgName>'.$target_cate['target_cid'].'</tns:ctgName>';//CJmall카테고리(세)
						$xml .= '
						</tns:mallCtg>';
					$xml .= '
					</tns:mallitem>';

					if( $certItemRequireYn == 'Y' ){
					$xml .= '
					<tns:cert>
						<tns:certCode>400017</tns:certCode>
						<tns:certSeq>1</tns:certSeq>
						<tns:certCateCd>004</tns:certCateCd>
					</tns:cert>';
					}

					$xml .= '
					<tns:goodsReport>';//상품기술서
						$xml .= '
						<tns:pedfId>91059</tns:pedfId>';//상품기술서 - 기술서상세항목
						$xml .= '
						<tns:html>';//상품기술서 - 기술서상세내역
							$xml .= '
							<![CDATA['.$basicinfo.']]>';
						$xml .= '
						</tns:html>';
					$xml .= '
					</tns:goodsReport>';

					foreach($Official as $pedfId => $pedfValue){
						$xml .= '
						<tns:goodsReport>';
						$xml .= '
							<tns:pedfId>'.$pedfId.'</tns:pedfId>';
						$xml .= '
							<tns:html>';
						$xml .= '
								<![CDATA['.$pedfValue.']]>';
						$xml .= '
							</tns:html>';
						$xml .= '
						</tns:goodsReport>';
					}

					$xml .= '
					<tns:image>';//이미지정보

						$xml .= '
						<tns:imageMain><![CDATA[' . $image_server_domain . $img_array['fiximage'] . ']]></tns:imageMain>';//이미지정보(URL) - 기본이미지
						for($i=2; $i<5 ;$i++){
							if( ! empty($data['add']['Picture'.$i]) ){
								$xml .='
								<tns:imageSub'.$i.'><![CDATA[' . $image_server_domain . $data['add']['Picture'.$i] . ']]></tns:imageSub'.$i.'>';//이미지정보(URL) - 부이미지1
							}
						}
					$xml .= '
					</tns:image>';
				$xml .= '
				</tns:good>';
			$xml .= '
			</tns:ifRequest>';
			
			$returnXml = str_replace("'","&#39;",$xml);

		}else{
			
			//수정 관련 xml을 array로
			$returnXml = array();
			
			//판매상품 정보 수정
			$xml = '<?xml version="1.0" encoding="UTF-8"?>
			<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_03_02" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_03_02.xsd">
			 <tns:vendorId>'.$this->api_key.'</tns:vendorId>
			 <tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			 <tns:good>
			  <tns:sItem>'.$itemCd.'</tns:sItem>
			  <tns:loc>30</tns:loc>
			  <tns:zLocalBolDesc><![CDATA['.$zLocalBolDesc.']]></tns:zLocalBolDesc>
			  <tns:zlocalCcDesc><![CDATA['.$zlocalCcDesc.']]></tns:zlocalCcDesc>
			  <tns:zContactSeqNo>'.$zContactSeqNo.'</tns:zContactSeqNo>
			  <tns:zSupShipSeqNo>'.$zSupShipSeqNo.'</tns:zSupShipSeqNo>
			  <tns:zReturnSeqNo>'.$zReturnSeqNo.'</tns:zReturnSeqNo>
			  <tns:zAsSupShipSeqNo>'.$zAsSupShipSeqNo.'</tns:zAsSupShipSeqNo>
			  <tns:zAsReturnSeqNo>'.$zAsReturnSeqNo.'</tns:zAsReturnSeqNo>';

			  /*
			  <tns:childYn>'.$childYn.'</tns:childYn>';
				if($childYn=='Y'){
					//400017 : 어린이제품 안전특별법 안전인증
					//400018 : 어린이제품 안전특별법 안전확인
					//000000 : 공급자적합성확인대상"
					$xml .= '
					<tns:certChildCd>400017</tns:certChildCd>';//certChildCd	기본정보 - 안정인증항목구분
				}
			  */
			$xml .= '
			  <tns:certItemRequireYn>'.$certItemRequireYn.'</tns:certItemRequireYn>'; //certItemRequireYn 기본정보 - 인증항목필수여부

			$xml .= '
			  <tns:delivCostCd>'.$this->delivCostCd.'</tns:delivCostCd>
			  <tns:delivCostType>'.$delivCostType.'</tns:delivCostType>
			  <tns:fastDelivYn>0</tns:fastDelivYn>';
			
	

			//수정 옵션 걸러내야함
			$sql = "SELECT shop_value, sellertool_value FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid|id' and shop_value like '".$pid."|%' and sellertool_key = 'unitCd' ";
			$this->db->query($sql);
			$presentOPtion = $this->db->fetchall("object");
			
			if(count($presentOPtion) > 0){
				foreach($presentOPtion as $po){
					$presentOptions[ $po['sellertool_value'] ] = str_replace($pid.'|','',$po['shop_value']);
				}
			}else{
				$presentOptions = array();
			}

			//추가 옵션
			$addOptions = array();
			//수정 옵션
			$updateOptions = array();
			//삭제된 옵션
			$deleteOptions = array();

			if( count($options) > 0 ){
				foreach($options as $option){
					//수정옵션
					if(in_array($option['id'],$presentOptions)){
						$updateOptions[ array_search($option['id'],$presentOptions) ] = $option['id'];
						unset($presentOptions[ array_search($option['id'],$presentOptions) ]);
					}else{
						$addOptions[] = $option['id'];
					}
				}
			}
			//위에서 체크 하고 남은 옵션 키들은 내려야 하는 옵션
			$deleteOptions = $presentOptions;

			//unitType	단품 Type 추가만!!!
			if( count($options) > 0 ){
				foreach($options as $option){
					if(in_array($option['id'],$addOptions)){
					$xml .= '
					<tns:unit>';//단품정보
						$xml .= '
						<tns:unitNm><![CDATA['.$option['option_div'].']]></tns:unitNm>';//단품정보 - 단품상세
						$xml .= '
						<tns:unitRetail>'.$option['option_price'].'</tns:unitRetail>';//단품정보 - 판매가
						/*
						unitCost
						"* 마진율 확인요함
						1. 과세상품 : 매입원가(VAT제외) = Round(판매가/1.1 - 0.1 * (판매가/1.1)), 0)
						2. 면세상품 : 매입원가(VAT제외) = Round(판매가 - 0.1 * 판매가, 0)"
						*/
						//$option_price = $option['option_price'] * $this->marginRate;
						//$option_price = ($option['option_price'] * ( 100 - $this->marginRate) / 100);
						
						if( $vatCode == 'E' ){
							$unitCost = round( $option['option_price'] - ($this->marginRate/100) * $option['option_price'], 0);
						}else{
							$unitCost = round( $option['option_price']/1.1 - ($this->marginRate/100) * ($option['option_price']/1.1), 0);
						}

						$xml .= '
						<tns:unitCost>'.$unitCost.'</tns:unitCost>';//단품정보 - 매입원가
						$xml .= '
						<tns:availableQty>'.$option['option_stock'].'</tns:availableQty>';//단품정보 - 공급가능수량
						$xml .= '
						<tns:leadTime>'.$leadtime.'</tns:leadTime>';//단품정보 - 리드타임
						$xml .= '
						<tns:unitApplyRsn>20</tns:unitApplyRsn>';//단품정보 - 적용사유 상품포장 으로 고정입력
						$xml .= '
						<tns:startSaleDt>'.$startSaleDt.'</tns:startSaleDt>';//단품정보 - 판매시작일자
						$xml .= '
						<tns:endSaleDt>'.$endSaleDt.'</tns:endSaleDt>';//단품정보 - 판매종료일자
						$xml .= '
						<tns:vpn>'.$option['id'].'</tns:vpn>';//단품정보 - 협력사상품코드
					$xml .= '
					</tns:unit>';
					}
				}
			}

			$xml .= '
			  <tns:mallitem>
			   <tns:mallItemDesc><![CDATA['.$pname.']]></tns:mallItemDesc>
			  </tns:mallitem>';
			   if( $certItemRequireYn == 'Y' ){
				$xml .= '
				<tns:cert>
					<tns:certCode>400017</tns:certCode>
					<tns:certSeq>1</tns:certSeq>
					<tns:certCateCd>004</tns:certCateCd>
				</tns:cert>';
				}

			$xml .= '
			  <tns:goodsReport>
			   <tns:pedfId>91059</tns:pedfId>
			   <tns:html><![CDATA['.$basicinfo.']]></tns:html>
			  </tns:goodsReport>';

			  foreach($Official as $pedfId => $pedfValue){
				$xml .= '
				<tns:goodsReport>';
				$xml .= '
					<tns:pedfId>'.$pedfId.'</tns:pedfId>';
				$xml .= '
					<tns:html>';
				$xml .= '
						<![CDATA['.$pedfValue.']]>';
				$xml .= '
					</tns:html>';
				$xml .= '
				</tns:goodsReport>';
			}
			

			$xml .= '
			<tns:image>';//이미지정보

				$xml .= '
				<tns:imageMain><![CDATA[' . $image_server_domain . $img_array['fiximage'] . ']]></tns:imageMain>';//이미지정보(URL) - 기본이미지
				for($i=2; $i<5 ;$i++){
					if( ! empty($data['add']['Picture'.$i]) ){
						$xml .='
						<tns:imageSub'.$i.'><![CDATA[' . $image_server_domain . $data['add']['Picture'.$i] . ']]></tns:imageSub'.$i.'>';//이미지정보(URL) - 부이미지1
					}
				}
			$xml .= '
			</tns:image>';
		

			$xml .= '
			 </tns:good>
			</tns:ifRequest>';

			$returnXml['info'] = str_replace("'","&#39;",$xml);

			//판매가격 수정
			//typeCd 가 '01'이면 itemCdZip 코드가  판매코드, '02'이면 단품코드
			if( count($updateOptions) > 0 ){
				$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_03_04" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_03_04.xsd">
				 <tns:vendorId>'.$this->api_key.'</tns:vendorId>
				 <tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>';
				 if( count($options) > 0 ){
					foreach($options as $option){
						if(in_array($option['id'],$updateOptions)){

							/*
							unitCost
							"* 마진율 확인요함
							1. 과세상품 : 매입원가(VAT제외) = Round(판매가/1.1 - 0.1 * (판매가/1.1)), 0)
							2. 면세상품 : 매입원가(VAT제외) = Round(판매가 - 0.1 * 판매가, 0)"
							*/
							//$option_price = $option['option_price'] * $this->marginRate;
							//$option_price = ($option['option_price'] * ( 100 - $this->marginRate) / 100);
							
							if( $vatCode == 'E' ){
								$unitCost = round( $option['option_price'] - ($this->marginRate/100) * $option['option_price'], 0);
							}else{
								$unitCost = round( $option['option_price']/1.1 - ($this->marginRate/100) * ($option['option_price']/1.1), 0);
							}
							
							$itemCD_ZIP = array_search($option['id'],$updateOptions);

							 $xml .= '
							 <tns:itemPrices>
							  <tns:typeCD>02</tns:typeCD>
							  <tns:itemCD_ZIP>'.$itemCD_ZIP.'</tns:itemCD_ZIP>
							  <tns:chnCls>30</tns:chnCls>
							  <tns:effectiveDate>'.$startSaleDt.'</tns:effectiveDate>
							  <tns:newUnitRetail>'.$option['option_price'].'</tns:newUnitRetail>
							  <tns:newUnitCost>'.$unitCost.'</tns:newUnitCost>
							 </tns:itemPrices>';
						}
					}
				 }
				 $xml .= '
				</tns:ifRequest>';

				$returnXml['price'] = str_replace("'","&#39;",$xml);
			}

			//판매상품 상태 수정
			//typeCd가 '01'이면 판매코드, '02'이면 단품코드
			$xml = '<?xml version="1.0" encoding="UTF-8"?>
			<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_03_03" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_03_03.xsd">
			 <tns:vendorId>'.$this->api_key.'</tns:vendorId>
			 <tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>';

			  if( count($deleteOptions) > 0 ){
					foreach($deleteOptions as $itemCd_zip => $deleteOptionId){
						 $xml .= '
						 <tns:itemStates>
						  <tns:typeCd>02</tns:typeCd>
						  <tns:itemCd_zip>'.$itemCd_zip.'</tns:itemCd_zip>
						  <tns:chnCls>30</tns:chnCls>
						  <tns:packInd>I</tns:packInd>
						 </tns:itemStates>';
					}
			  }

			  if( count($updateOptions) > 0 ){
				   if( count($options) > 0 ){
						foreach($options as $option){
							if(in_array($option['id'],$updateOptions) && $option['option_stock'] == 0){

								$itemCd_zip = array_search($option['id'],$updateOptions);

								$xml .= '
								 <tns:itemStates>
								  <tns:typeCd>02</tns:typeCd>
								  <tns:itemCd_zip>'.$itemCd_zip.'</tns:itemCd_zip>
								  <tns:chnCls>30</tns:chnCls>
								  <tns:packInd>I</tns:packInd>
								 </tns:itemStates>';
							}
						}
				   }
			  }

			 $xml .= '
			 <tns:itemStates>
			  <tns:typeCd>01</tns:typeCd>
			  <tns:itemCd_zip>'.$itemCd.'</tns:itemCd_zip>
			  <tns:chnCls>30</tns:chnCls>
			  <tns:packInd>'.$packInd.'</tns:packInd>
			 </tns:itemStates>
			</tns:ifRequest>';

			$returnXml['state'] = str_replace("'","&#39;",$xml);


			$stock_update_cnt = 0;
			if( count($updateOptions) > 0 ){
				$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_03_05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_03_05.xsd">
				 <tns:vendorId>'.$this->api_key.'</tns:vendorId>
				 <tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>';
				 if( count($options) > 0 ){
					foreach($options as $option){
						if(in_array($option['id'],$updateOptions) && $option['option_stock'] > 0){
							
							$unitCd = array_search($option['id'],$updateOptions);
							
							$stock_update_cnt++;
							 $xml .= '
							 <tns:ltSupplyPlans>
							  <tns:unitCd>'.$unitCd.'</tns:unitCd>
							  <tns:chnCls>30</tns:chnCls>
							  <tns:strDt>'.$startSaleDt.'</tns:strDt>
							  <tns:endDt>9999-12-30</tns:endDt>
							  <tns:availSupQty>'.$option['option_stock'].'</tns:availSupQty>
							 </tns:ltSupplyPlans>';
						}
					}
				 }
				 $xml .= '
				</tns:ifRequest>';
				$returnXml['stock'] = str_replace("'","&#39;",$xml);
			}

			if($stock_update_cnt == 0){
				$returnXml['stock']="";
				unset($returnXml['stock']);
			}
		}
		
		/*
		if($_SESSION['admininfo']['charger_id']=='forbiz'){
			print_r($returnXml);
			//exit;
		}
		*/

		return $returnXml;
	}

	private function makeOfficialNoticeArray($pid) {

		$sql="select pmi_code, pmi_desc from shop_product_mandatory_info where pid='".$pid."' ";
		$this->db->query ( $sql );
		$datas = $this->db->fetchall("object");

		$return = array();
		foreach($datas as $data){
			list($code, $dt_code) = explode("|",$data['pmi_code']);
			$code = (int)$code;
			$dt_code = (int)$dt_code;

			if( empty($OfficialNotices) ){
				$OfficialNotices = $this->getOfficialNoticeCode($code);
			}
			
			if( ! empty($OfficialNotices[$dt_code]) ){
				$return[ $OfficialNotices[$dt_code] ] = str_replace('㈜','(주)',$data['pmi_desc']);

				if( empty($return[ $OfficialNotices[$dt_code] ] ) ){
					$return[ $OfficialNotices[$dt_code] ] = "상세상세정보참고";
				}
			}
		}

		if(!empty($OfficialNotices['no'])){
			$return[ $OfficialNotices['no'] ] = "상세상세정보참고";
		}

		return $return;
	}

	private function getOfficialNoticeCode($code) {

		$info["1"]["1"]="25001";//의류제품 소재
		$info["1"]["2"]="25002";//의류색상
		$info["1"]["3"]="25003";//의류치수
		$info["1"]["4"]="25004";//의류제조자,수입품의 경우 수입자를 함께 표기
		$info["1"]["5"]="25005";//의류제조국
		$info["1"]["6"]="25006";//의류세탁방법 및 취급시 주의사항
		$info["1"]["7"]="25007";//의류제조년월
		$info["1"]["8"]="25008";//의류품질보증기준
		$info["1"]["9"]="25009";//의류A/S 책임자와 전화번호
		$info["2"]["1"]="25010";//구두 / 신발제품 소재
		$info["2"]["2"]="25002";//구두 / 신발색상
		$info["2"]["3"]="25011";//구두 / 신발치수
		$info["2"]["no"]="25012";//
		$info["2"]["4"]="25004";//구두 / 신발제조자, 수입품의 경우 수입자를 함께 표기
		$info["2"]["5"]="25005";//구두 / 신발제조국
		$info["2"]["6"]="25013";//구두 / 신발취급시 주의사항
		$info["2"]["7"]="25008";//구두 / 신발품질보증기준
		$info["2"]["8"]="25009";//구두 / 신발A/S 책임자와 전화번호
		$info["3"]["1"]="25014";//가방종류
		$info["3"]["2"]="25015";//가방소재
		$info["3"]["3"]="25002";//가방색상
		$info["3"]["4"]="25016";//가방크기
		$info["3"]["5"]="25004";//가방제조자,수입품의 경우 수입자를 함께 표기 
		$info["3"]["6"]="25005";//가방제조국
		$info["3"]["7"]="25013";//가방취급시 주의사항
		$info["3"]["8"]="25008";//가방품질보증기준
		$info["3"]["9"]="25009";//가방A/S 책임자와 전화번호
		$info["4"]["1"]="25014";//패션잡화 (모자 / 벨트 / 액세서리)종류
		$info["4"]["2"]="25015";//패션잡화 (모자 / 벨트 / 액세서리)소재
		$info["4"]["3"]="25003";//패션잡화 (모자 / 벨트 / 액세서리)치수
		$info["4"]["4"]="25004";//패션잡화 (모자 / 벨트 / 액세서리)제조자,수입품의 경우 수입자를 함께 표기
		$info["4"]["5"]="25005";//패션잡화 (모자 / 벨트 / 액세서리)제조국
		$info["4"]["6"]="25013";//패션잡화 (모자 / 벨트 / 액세서리)취급시 주의사항
		$info["4"]["7"]="25008";//패션잡화 (모자 / 벨트 / 액세서리)품질보증기준
		$info["4"]["8"]="25009";//패션잡화 (모자 / 벨트 / 액세서리)A/S 책임자와 전화번호
		$info["5"]["1"]="25163";//침구류 / 커튼제품 소재
		$info["5"]["2"]="25002";//침구류 / 커튼색상
		$info["5"]["3"]="25003";//침구류 / 커튼치수
		$info["5"]["4"]="25018";//침구류 / 커튼제품구성
		$info["5"]["5"]="25004";//침구류 / 커튼제조자,수입품의 경우 수입자를 함께 표기
		$info["5"]["6"]="25005";//침구류 / 커튼제조국
		$info["5"]["7"]="25006";//침구류 / 커튼세탁방법 및 취급시 주의사항
		$info["5"]["8"]="25008";//침구류 / 커튼품질보증 기준
		$info["5"]["9"]="25009";//침구류 / 커튼A/S 책임자와 전화번호
		$info["6"]["1"]="25019";//가구(침대 / 소파 / 싱크대 / DIY제품)품명
		$info["6"]["2"]="25020";//가구(침대 / 소파 / 싱크대 / DIY제품)KC 인증 필 유무
		$info["6"]["3"]="25002";//가구(침대 / 소파 / 싱크대 / DIY제품)색상
		$info["6"]["4"]="25021";//가구(침대 / 소파 / 싱크대 / DIY제품)구성품
		$info["6"]["5"]="25022";//가구(침대 / 소파 / 싱크대 / DIY제품)주요 소재
		$info["6"]["6"]="25164";//가구(침대 / 소파 / 싱크대 / DIY제품)제조자,수입품의 경우 수입자를 함께 표기 
		$info["6"]["7"]="25165";//가구(침대 / 소파 / 싱크대 / DIY제품)제조국
		$info["6"]["8"]="25016";//가구(침대 / 소파 / 싱크대 / DIY제품)크기
		$info["6"]["9"]="25023";//가구(침대 / 소파 / 싱크대 / DIY제품)배송·설치비용
		$info["6"]["10"]="25008";//가구(침대 / 소파 / 싱크대 / DIY제품)품질보증기준
		$info["6"]["11"]="25009";//가구(침대 / 소파 / 싱크대 / DIY제품)A/S 책임자와 전화번호
		$info["7"]["1"]="25024";//영상가전(TV류)품명 및 모델명
		$info["7"]["2"]="25025";//영상가전(TV류)전기용품 안전인증 필 유무
		$info["7"]["3"]="25026";//영상가전(TV류)정격전압, 소비전력, 에너지소비효율등급
		$info["7"]["4"]="25027";//영상가전(TV류)동일모델의 출시년월
		$info["7"]["5"]="25004";//영상가전(TV류)제조자,수입품의 경우 수입자를 함께 표기
		$info["7"]["6"]="25005";//영상가전(TV류)제조국
		$info["7"]["7"]="25028";//영상가전(TV류)크기
		$info["7"]["8"]="25029";//영상가전(TV류)화면사양
		$info["7"]["9"]="25008";//영상가전(TV류)품질보증기준
		$info["7"]["10"]="25009";//영상가전(TV류)A/S 책임자와 전화번호
		$info["8"]["1"]="25024";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)품명 및 모델명
		$info["8"]["2"]="25025";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)전기용품 안전인증 필 유무
		$info["8"]["3"]="25026";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)정격전압, 소비전력, 에너지소비효율등급
		$info["8"]["4"]="25027";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)동일모델의 출시년월
		$info["8"]["5"]="25004";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)제조자,수입품의 경우 수입자를 함께 표기
		$info["8"]["6"]="25005";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)제조국
		$info["8"]["7"]="25030";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)크기
		$info["8"]["8"]="25008";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)품질보증기준
		$info["8"]["9"]="25009";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)A/S 책임자와 전화번호
		$info["9"]["1"]="25024";//계절가전(에어컨 / 온풍기)품명 및 모델명
		$info["9"]["2"]="25025";//계절가전(에어컨 / 온풍기)전기용품 안전인증 필 유무
		$info["9"]["3"]="25026";//계절가전(에어컨 / 온풍기)정격전압, 소비전력, 에너지소비효율등급
		$info["9"]["4"]="25027";//계절가전(에어컨 / 온풍기)동일모델의 출시년월
		$info["9"]["5"]="25004";//계절가전(에어컨 / 온풍기)제조자,수입품의 경우 수입자를 함께 표기
		$info["9"]["6"]="25005";//계절가전(에어컨 / 온풍기)제조국
		$info["9"]["7"]="25031";//계절가전(에어컨 / 온풍기)크기
		$info["9"]["8"]="25032";//계절가전(에어컨 / 온풍기)냉난방면적
		$info["9"]["9"]="25033";//계절가전(에어컨 / 온풍기)추가설치비용
		$info["9"]["10"]="25008";//계절가전(에어컨 / 온풍기)품질보증기준
		$info["9"]["11"]="25009";//계절가전(에어컨 / 온풍기)A/S 책임자와 전화번호
		$info["10"]["1"]="25024";//사무용기기(컴퓨터 / 노트북 / 프린터)품명 및 모델명
		$info["10"]["2"]="25039";//사무용기기(컴퓨터 / 노트북 / 프린터)KCC 인증 필 유무
		$info["10"]["3"]="25026";//사무용기기(컴퓨터 / 노트북 / 프린터)정격전압, 소비전력, 에너지소비효율등급
		$info["10"]["4"]="25027";//사무용기기(컴퓨터 / 노트북 / 프린터)동일모델의 출시년월
		$info["10"]["5"]="25004";//사무용기기(컴퓨터 / 노트북 / 프린터)제조자
		$info["10"]["6"]="25005";//사무용기기(컴퓨터 / 노트북 / 프린터)제조국
		$info["10"]["7"]="25035";//사무용기기(컴퓨터 / 노트북 / 프린터)크기,무게
		$info["10"]["8"]="25169";//사무용기기(컴퓨터 / 노트북 / 프린터)주요 사양
		$info["10"]["9"]="25008";//사무용기기(컴퓨터 / 노트북 / 프린터)품질보증기준
		$info["10"]["10"]="25009";//사무용기기(컴퓨터 / 노트북 / 프린터)A/S 책임자와 전화번호
		$info["11"]["1"]="25024";//광학기기(디지털카메라 / 캠코더)품명 및 모델명
		$info["11"]["2"]="25034";//광학기기(디지털카메라 / 캠코더)KCC 인증 필 유무
		$info["11"]["3"]="25027";//광학기기(디지털카메라 / 캠코더)동일모델의 출시년월
		$info["11"]["4"]="25004";//광학기기(디지털카메라 / 캠코더)제조자
		$info["11"]["5"]="25005";//광학기기(디지털카메라 / 캠코더)제조국
		$info["11"]["6"]="25037";//광학기기(디지털카메라 / 캠코더)크기, 무게
		$info["11"]["7"]="25038";//광학기기(디지털카메라 / 캠코더)주요 사양
		$info["11"]["8"]="25008";//광학기기(디지털카메라 / 캠코더)품질보증기준
		$info["11"]["9"]="25009";//광학기기(디지털카메라 / 캠코더)A/S 책임자와 전화번호
		$info["12"]["1"]="25024";//소형전자(MP3 / 전자사전 등)품명 및 모델명
		$info["12"]["2"]="25039";//소형전자(MP3 / 전자사전 등)KC 인증 필 유무
		$info["12"]["3"]="25040";//소형전자(MP3 / 전자사전 등)정격전압, 소비전력
		$info["12"]["4"]="25027";//소형전자(MP3 / 전자사전 등)동일모델의 출시년월
		$info["12"]["5"]="25004";//소형전자(MP3 / 전자사전 등)제조자
		$info["12"]["6"]="25005";//소형전자(MP3 / 전자사전 등)제조국
		$info["12"]["7"]="25037";//소형전자(MP3 / 전자사전 등)크기, 무게
		$info["12"]["8"]="25038";//소형전자(MP3 / 전자사전 등)주요 사양
		$info["12"]["9"]="25008";//소형전자(MP3 / 전자사전 등)품질보증기준
		$info["12"]["10"]="25009";//소형전자(MP3 / 전자사전 등)A/S 책임자와 전화번호
		$info["13"]["1"]="25024";//휴대폰품명 및 모델명
		$info["13"]["2"]="25034";//휴대폰KCC 인증 필 유무
		$info["13"]["3"]="25027";//휴대폰동일모델의 출시년월
		$info["13"]["4"]="25004";//휴대폰제조자
		$info["13"]["5"]="25005";//휴대폰제조국
		$info["13"]["6"]="25037";//휴대폰크기, 무게
		$info["13"]["7"]="25041";//휴대폰이동통신사
		$info["13"]["8"]="25042";//휴대폰가입절차
		$info["13"]["9"]="25043";//휴대폰소비자의 추가적인 부담사항
		$info["13"]["10"]="25038";//휴대폰주요사양
		$info["13"]["11"]="25008";//휴대폰품질보증기준
		$info["13"]["12"]="25009";//휴대폰A/S 책임자와 전화번호
		$info["14"]["1"]="25024";//내비게이션품명 및 모델명
		$info["14"]["2"]="25034";//내비게이션KCC 인증 필 유무
		$info["14"]["3"]="25040";//내비게이션정격전압, 소비전력
		$info["14"]["4"]="25027";//내비게이션동일모델의 출시년월
		$info["14"]["5"]="25004";//내비게이션제조자
		$info["14"]["6"]="25005";//내비게이션제조국
		$info["14"]["7"]="25037";//내비게이션크기, 무게
		$info["14"]["8"]="25038";//내비게이션주요 사양
		$info["14"]["9"]="25044";//내비게이션맵 업데이트 비용 및 무상기간
		$info["14"]["10"]="25008";//내비게이션품질보증기준
		$info["14"]["11"]="25009";//내비게이션A/S 책임자와 전화번호
		$info["15"]["1"]="25024";//자동차용품(자동자부품/기타 자동차용품)품명 및 모델명
		$info["15"]["2"]="25027";//자동차용품(자동자부품/기타 자동차용품)동일모델의 출시년월
		$info["15"]["3"]="25045";//자동차용품(자동자부품/기타 자동차용품)자동차부품 자기인증 유무
		$info["15"]["4"]="25004";//자동차용품(자동자부품/기타 자동차용품)제조자, 수입품의 경우 수입자를 함께 표기
		$info["15"]["5"]="25005";//자동차용품(자동자부품/기타 자동차용품)제조국
		$info["15"]["6"]="25016";//자동차용품(자동자부품/기타 자동차용품)크기
		$info["15"]["7"]="25046";//자동차용품(자동자부품/기타 자동차용품)적용차종
		$info["15"]["8"]="25166";//자동차용품(자동자부품/기타 자동차용품)품질보증기준
		$info["15"]["9"]="25008";//자동차용품(자동자부품/기타 자동차용품)A/S 책임자와 전화번호
		$info["16"]["1"]="25009";//의료기기품명 및 모델명
		$info["16"]["2"]="25024";//의료기기의료기기법상 허가·신고 번호 및 광고사전심의필 유무
		$info["16"]["3"]="25047";//의료기기광고사전심의필 유무
		$info["16"]["5"]="25049";//의료기기정격전압, 소비전력
		$info["16"]["6"]="25027";//의료기기동일모델의 출시년월
		$info["16"]["7"]="25004";//의료기기제조자, 수입품의 경우 수입자를 함께 표기
		$info["16"]["8"]="25005";//의료기기제조국
		$info["16"]["9"]="25050";//의료기기제품의 사용 목적 및 사용방법
		$info["16"]["10"]="25013";//의료기기취급시 주의사항
		$info["16"]["11"]="25008";//의료기기품질보증기준
		$info["16"]["12"]="25009";//의료기기A/S 책임자와 전화번호
		$info["17"]["1"]="25024";//주방용품품명 및 모델명
		$info["17"]["2"]="25051";//주방용품재질
		$info["17"]["3"]="25021";//주방용품구성품
		$info["17"]["4"]="25016";//주방용품크기
		$info["17"]["5"]="25027";//주방용품동일모델의 출시년월
		$info["17"]["6"]="25004";//주방용품제조자, 수입품의 경우 수입자를 함께 표기
		$info["17"]["7"]="25005";//주방용품제조국
		$info["17"]["8"]="25052";//주방용품수입 기구/용기
		$info["17"]["9"]="25008";//주방용품품질보증기준
		$info["17"]["10"]="25009";//주방용품A/S 책임자와 전화번호
		$info["18"]["1"]="25053";//화장품용량 또는 중량
		$info["18"]["2"]="25054";//화장품제품 주요 사양
		$info["18"]["3"]="25055";//화장품사용기한 또는 개봉 후 사용기간
		$info["18"]["4"]="25056";//화장품사용방법
		$info["18"]["5"]="25057";//화장품제조자 및 제조판매업자
		$info["18"]["6"]="25005";//화장품제조국
		$info["18"]["7"]="25058";//화장품주요성분
		$info["18"]["8"]="25059";//화장품기능성 화장품의 경우 화장품법에 따른 식품의약품안전청 심사 필 유무
		$info["18"]["9"]="25060";//화장품사용할 때 주의사항
		$info["18"]["10"]="25008";//화장품품질보증기준
		$info["18"]["11"]="25061";//화장품소비자상담관련 전화번호
		$info["19"]["1"]="25062";//귀금속 / 보석 / 시계류소재 / 순도 / 밴드재질
		$info["19"]["2"]="25063";//귀금속 / 보석 / 시계류중량
		$info["19"]["3"]="25004";//귀금속 / 보석 / 시계류제조자, 수입품의 경우 수입자를 함께 표기
		$info["19"]["4"]="25168";//귀금속 / 보석 / 시계류제조국
		$info["19"]["5"]="25003";//귀금속 / 보석 / 시계류치수
		$info["19"]["6"]="25064";//귀금속 / 보석 / 시계류착용 시 주의사항
		$info["19"]["no"]="25065";//
		$info["19"]["7"]="25066";//귀금속 / 보석 / 시계류주요사양
		$info["19"]["8"]="25067";//귀금속 / 보석 / 시계류보증서 제공여부
		$info["19"]["9"]="25008";//귀금속 / 보석 / 시계류품질보증기준
		$info["19"]["10"]="25009";//귀금속 / 보석 / 시계류A/S 책임자와 전화번호
		$info["20"]["1"]="25068";//식품(농수산물)포장단위별 용량(중량), 수량, 크기
		$info["20"]["2"]="25069";//식품(농수산물)생산자, 수입품의 경우 수입자를 함께 표기
		$info["20"]["3"]="25070";//식품(농수산물)농수산물의 원산지 표시에 관한 법률에 따른 원산지
		$info["20"]["4"]="25071";//식품(농수산물)제조연월일(포장일 또는 생산연도), 유통기한 또는 품질유지기한
		$info["20"]["5"]="25072";//식품(농수산물)농산물
		$info["20"]["6"]="25073";//식품(농수산물)축산물
		$info["20"]["7"]="25074";//식품(농수산물)수산물
		$info["20"]["8"]="25075";//식품(농수산물)수입식품에 해당하는 경우 “식품위생법에 따른 수입신고를 필함”의 문구
		$info["20"]["9"]="25076";//식품(농수산물)상품구성
		$info["20"]["10"]="25077";//식품(농수산물)보관방법 또는 취급방법
		$info["20"]["11"]="25078";//식품(농수산물)소비자상담 관련 전화번호
		$info["21"]["1"]="25079";//가공식품식품의 유형
		$info["21"]["2"]="25080";//가공식품생산자/소재지/수입자
		$info["21"]["3"]="25081";//가공식품제조연월일
		$info["21"]["5"]="25082";//가공식품포장단위별 용량(중량), 수량
		$info["21"]["6"]="25083";//가공식품원재료명 및 함량
		$info["21"]["7"]="25084";//가공식품영양성분
		$info["21"]["8"]="25085";//가공식품유전자재조합식품에 해당하는 경우의 표시
		$info["21"]["9"]="25086";//가공식품영유아식 또는 체중조절식품 등에 해당하는 경우 표시광고 사전심의필
		$info["21"]["10"]="25087";//가공식품수입식품에 해당하는 경우 “식품위생법에 따른 수입신고를 필함”의 문구
		$info["21"]["11"]="25078";//가공식품소비자상담 관련 전화번호
		$info["22"]["1"]="25088";//건강기능식품식품의 유형
		$info["22"]["2"]="25080";//건강기능식품생산자/소재지/수입자
		$info["22"]["3"]="25090";//건강기능식품제조연월일
		$info["22"]["4"]="25091";//건강기능식품유통기한 또는 품질유지기한
		$info["22"]["5"]="25092";//건강기능식품포장단위별 용량(중량), 수량
		$info["22"]["6"]="25093";//건강기능식품원재료명 및 함량
		$info["22"]["7"]="25094";//건강기능식품영양정보
		$info["22"]["8"]="25095";//건강기능식품기능정보
		$info["22"]["9"]="25096";//건강기능식품섭취량, 섭취방법 및 섭취 시 주의사항
		$info["22"]["10"]="25097";//건강기능식품유전자재조합식품 유무
		$info["22"]["11"]="25098";//건강기능식품표시광고 사전심의 유무
		$info["22"]["12"]="25099";//건강기능식품수입식품 여부
		$info["22"]["13"]="25078";//건강기능식품소비자상담 관련 전화번호
		$info["23"]["1"]="25024";//영유아용품품명 및 모델명
		$info["23"]["2"]="25100";//영유아용품KC 인증 필
		$info["23"]["3"]="25101";//영유아용품크기, 중량
		$info["23"]["4"]="25002";//영유아용품색상
		$info["23"]["5"]="25102";//영유아용품재질
		$info["23"]["6"]="25103";//영유아용품사용연령
		$info["23"]["7"]="25027";//영유아용품동일모델의 출시년월
		$info["23"]["8"]="25004";//영유아용품제조자, 수입품의 경우 수입자를 함께 표기
		$info["23"]["9"]="25005";//영유아용품제조국
		$info["23"]["10"]="25104";//영유아용품취급방법 및 취급시 주의사항, 안전표시
		$info["23"]["11"]="25008";//영유아용품품질보증기준
		$info["23"]["12"]="25009";//영유아용품A/S 책임자와 전화번호
		$info["24"]["1"]="25024";//악기품명 및 모델명
		$info["24"]["2"]="25016";//악기크기
		$info["24"]["3"]="25002";//악기색상
		$info["24"]["4"]="25051";//악기재질
		$info["24"]["5"]="25105";//악기제품 구성
		$info["24"]["6"]="25027";//악기동일모델의 출시년월
		$info["24"]["7"]="25004";//악기제조자, 수입품의 경우 수입자를 함께 표기
		$info["24"]["8"]="25005";//악기제조국
		$info["24"]["9"]="25106";//악기상품별 세부 사양
		$info["24"]["10"]="25008";//악기품질보증기준
		$info["24"]["11"]="25009";//악기A/S 책임자와 전화번호
		$info["25"]["1"]="25024";//스포츠용품품명 및 모델명
		$info["25"]["2"]="25101";//스포츠용품크기, 중량
		$info["25"]["3"]="25002";//스포츠용품색상
		$info["25"]["4"]="25051";//스포츠용품재질
		$info["25"]["5"]="25105";//스포츠용품제품 구성
		$info["25"]["6"]="25027";//스포츠용품동일모델의 출시년월
		$info["25"]["7"]="25004";//스포츠용품제조자, 수입품의 경우 수입자를 함께 표기
		$info["25"]["8"]="25005";//스포츠용품제조국
		$info["25"]["9"]="25106";//스포츠용품상품별 세부 사양
		$info["25"]["10"]="25008";//스포츠용품품질보증기준
		$info["25"]["11"]="25009";//스포츠용품A/S 책임자와 전화번호
		$info["26"]["1"]="25107";//서적도서명
		$info["26"]["2"]="25108";//서적저자, 출판사
		$info["26"]["3"]="25109";//서적크기
		$info["26"]["4"]="25110";//서적쪽수
		$info["26"]["5"]="25111";//서적제품 구성
		$info["26"]["6"]="25112";//서적출간일
		$info["26"]["7"]="25113";//서적목차 또는 책소개
		$info["28"]["1"]="25121";//여행상품여행사
		$info["28"]["2"]="25122";//여행상품이용항공편
		$info["28"]["3"]="25123";//여행상품여행기간 및 일정
		$info["28"]["4"]="25124";//여행상품총 예정 인원, 출발 가능 인원
		$info["28"]["5"]="25125";//여행상품숙박정보
		$info["28"]["6"]="25126";//여행상품여행상품 가격
		$info["28"]["7"]="25127";//여행상품선택경비 유무 및 세부 내용
		$info["28"]["8"]="25119";//여행상품취소 규정
		$info["28"]["9"]="25128";//여행상품해외여행의 경우 외교부가 지정하는 여행경보단계
		$info["28"]["10"]="25120";//여행상품예약담당 연락처
		$info["31"]["1"]="25024";//물품대여 서비스 (정수기, 비데, 공기청정기 등)품명 및 모델명
		$info["31"]["2"]="25136";//물품대여 서비스 (정수기, 비데, 공기청정기 등)소유권 이전 조건
		$info["31"]["3"]="25141";//물품대여 서비스 (정수기, 비데, 공기청정기 등)유지보수 조건
		$info["31"]["4"]="25142";//물품대여 서비스 (정수기, 비데, 공기청정기 등)상품의 고장·분실·훼손 시 소비자 책임
		$info["31"]["5"]="25143";//물품대여 서비스 (정수기, 비데, 공기청정기 등)중도 해약 시 환불 기준
		$info["31"]["6"]="25144";//물품대여 서비스 (정수기, 비데, 공기청정기 등)제품 사양
		$info["31"]["7"]="25078";//물품대여 서비스 (정수기, 비데, 공기청정기 등)소비자상담 관련 전화번호

		return $info[$code];
	}

	private function makeOptionArray($pid, $pinfo) {
		$subtract_stock = 0;

		/*
		//최저가 찾기 (가격떄문에!)
		$sql = "SELECT ifnull(min(od.option_price),'X') as option_price FROM " . TBL_SHOP_PRODUCT_OPTIONS . " o left join shop_product_options_detail od on (o.opn_ix =od.opn_ix) WHERE o.pid='" . $pid . "' and o.option_kind in ('b') and o.option_use='1'";
		$this->db->query ( $sql );
		$this->db->fetch();
		if($this->db->dt[option_price]!='X'){
			$tmp_option_price = $this->db->dt[option_price];
			
			if($pinfo ['sellprice'] > $tmp_option_price){
				$gep_price = $pinfo ['sellprice'] - $tmp_option_price;
				$pinfo ['sellprice'] = $tmp_option_price;
			}
		}
		
		if(!($pinfo ['sellprice'] > 0)){
			$this->error ['code'] = '2002';
			$this->error ['msg'] = '판매가가 없습니다. pid = ' . $pid;
			if($this->error_type=="return"){
				return "[".$this->error ['code']."]".$this->error ['msg'];
			}else{
				$this->printError ();
			}
		}

		if($pinfo ['one_commission']=="Y" && $pinfo ['commission'] < "8"){
			$this->error ['code'] = '2002';
			$this->error ['msg'] = '개별수수료가 8% 미만입니다. commission = '.$pinfo ['commission'].'  pid = ' . $pid;
			if($this->error_type=="return"){
				return "[".$this->error ['code']."]".$this->error ['msg'];
			}else{
				$this->printError ();
			}
		}
		*/

		//옵션 관련 처리하기!!!
		//추가구성상품은 X
		//$sql = "SELECT * FROM " . TBL_SHOP_PRODUCT_OPTIONS . " o WHERE o.pid='" . $pid . "' and option_kind in ('b','c1','c2','i1','i2') and o.option_use='1' order by opn_ix ";
		$sql = "SELECT * FROM " . TBL_SHOP_PRODUCT_OPTIONS . " o WHERE o.pid='" . $pid . "' and option_kind in ('b') and o.option_use='1' order by opn_ix ";
		$this->db->query ( $sql );
		
		if($this->db->total){
			
			$options = array();
				
			$cnt=0;
			$tmp_options=array();
			$i_tmp_options=array();
			$stock_tmp_options=array();
			$optoin_stock_use_yn=false;
			
			$_options_ = $this->db->fetchall("object");

			for($i=0;$i<count($_options_);$i++){

				if($_options_[$i]['option_kind']=="b"){
					$sql = "SELECT id, option_div, option_stock , option_sell_ing_cnt, option_price, od.option_soldout FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' order by id";
					$this->db->query ( $sql );
					$stock_tmp_options[] = $this->db->fetchall('object');
				}elseif($_options_[$i]['option_kind']=="i1" || $_options_[$i]['option_kind']=="i2"){
					$sql = "SELECT id, option_div, option_stock, option_sell_ing_cnt, option_price, od.option_soldout FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' order by id";
					$this->db->query ( $sql );
					$i_tmp_options[] = $this->db->fetchall('object');
				}elseif($_options_[$i]['option_kind']=="c2"){
					$sql = "
					select 1 as id, '' as option_div , '0' as option_stock, '0' as option_sell_ing_cnt, '0' as option_price, '0' as option_soldout
					UNION ALL
					SELECT id, option_div, option_stock, option_sell_ing_cnt, option_price, od.option_soldout FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' order by id";
					$this->db->query ( $sql );
					$tmp_options[] = $this->db->fetchall('object');
				}else{
					$sql = "SELECT id, '".$_options_[$i]['option_kind']."' as option_kind, option_div, option_stock, option_sell_ing_cnt, option_price, od.option_soldout FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' order by id";
					$this->db->query ( $sql );
					$tmp_options[] = $this->db->fetchall('object');
				}

			}

			if(count($tmp_options)>2){
				for($i=0;$i<count($tmp_options[0]);$i++){
					for($j=0;$j<count($tmp_options[1]);$j++){
						for($k=0;$k<count($tmp_options[2]);$k++){
							$options[$cnt]['id'] = $tmp_options[0][$i]['id'] . '-' . $tmp_options[1][$j]['id'] . '-' . $tmp_options[2][$k]['id'];
							$options[$cnt]['option_stock'] = "200";
							$options[$cnt]['option_price'] = $pinfo ['sellprice'] + ($tmp_options[0][$i]['option_price']+$tmp_options[1][$j]['option_price']+$tmp_options[2][$k]['option_price']) + $gep_price;

							$tmp_option_div=array();
							if($tmp_options[0][$i]['option_div']!=""){
								$tmp_option_div[]=$tmp_options[0][$i]['option_div'];
							}
							if($tmp_options[1][$j]['option_div']!=""){
								$tmp_option_div[]=$tmp_options[1][$j]['option_div'];
							}
							if($tmp_options[2][$k]['option_div']!=""){
								$tmp_option_div[]=$tmp_options[2][$k]['option_div'];
							}

							if(count($tmp_option_div)>0){
								$options[$cnt]['option_div'] = implode("|",$tmp_option_div);
							}else{
								$options[$cnt]['option_div'] = "옵션선택안함";
							}

							$cnt++;
						}
					}
				}
			}elseif(count($tmp_options)==2){
				for($i=0;$i<count($tmp_options[0]);$i++){
					for($j=0;$j<count($tmp_options[1]);$j++){
						$options[$cnt]['id'] = $tmp_options[0][$i]['id'] . '-' . $tmp_options[1][$j]['id'];
						$options[$cnt]['option_stock'] = "200";
						$options[$cnt]['option_price'] = $pinfo ['sellprice'] + ($tmp_options[0][$i]['option_price']+$tmp_options[1][$j]['option_price']) + $gep_price;

						$tmp_option_div=array();
						if($tmp_options[0][$i]['option_div']!=""){
							$tmp_option_div[]=$tmp_options[0][$i]['option_div'];
						}
						if($tmp_options[1][$j]['option_div']!=""){
							$tmp_option_div[]=$tmp_options[1][$j]['option_div'];
						}

						if(count($tmp_option_div)>0){
							$options[$cnt]['option_div'] = implode("|",$tmp_option_div);
						}else{
							$options[$cnt]['option_div'] = "옵션선택안함";
						}

						$cnt++;
					}
				}
			}else{
				for($i=0;$i<count($tmp_options[0]);$i++){
					$options[$cnt]['id'] = $tmp_options[0][$i]['id'];
					$options[$cnt]['option_stock'] = "200";
					$options[$cnt]['option_price'] = $pinfo ['sellprice'] + $tmp_options[0][$i]['option_price'] + $gep_price;
					$options[$cnt]['option_div'] = $tmp_options[0][$i]['option_div'];
					$cnt++;
				}
			}

			if(count($i_tmp_options)>0){
				for($i=0;$i<count($i_tmp_options[0]);$i++){
					$options[$cnt]['id'] = $tmp_options[0][$i]['id'];
					$options[$cnt]['option_stock'] = "200";
					$options[$cnt]['option_price'] = $pinfo ['sellprice'] + $i_tmp_options[0][$i]['option_price'] + $gep_price;
					$options[$cnt]['option_div'] = $i_tmp_options[0][$i]['option_div'];
					$cnt++;
				}
			}

			if(count($stock_tmp_options)>0){
				$optoin_stock_use_yn=true;
				for($i=0;$i<count($stock_tmp_options[0]);$i++){
					$options[$cnt]['id'] = $stock_tmp_options[0][$i]['id'];
					$subtract_stock = ($stock_tmp_options[0][$i]['option_sell_ing_cnt'] > 0 ? $stock_tmp_options[0][$i]['option_sell_ing_cnt'] : 0 );
					// 2016-11-14 재고 고정으로 변경 요청으로 인함 S
					/*if( ($stock_tmp_options[0][$i]['option_stock'] - $subtract_stock) > 0 && $stock_tmp_options[0][$i]['option_soldout'] == '0' ){
						$options[$cnt]['option_stock'] = ($stock_tmp_options[0][$i]['option_stock'] - $subtract_stock > 200 ? 200 : $stock_tmp_options[0][$i]['option_stock'] - $subtract_stock );
					}else{
						$options[$cnt]['option_stock'] = 0;
					}*/
					// 2016-11-14 재고 고정으로 변경 요청으로 인함 E
					if( $stock_tmp_options[0][$i]['option_soldout'] == '0' ){
						$options[$cnt]['option_stock'] = 100;
					}else{
						$options[$cnt]['option_stock'] = 0;
					}
					//가격+재고옵션 가격은 최저가를 가지고 sellprice 를 변경시켜야 한다
					$options[$cnt]['option_price'] = $stock_tmp_options[0][$i]['option_price'];
					$options[$cnt]['option_div'] = $stock_tmp_options[0][$i]['option_div'];
					$cnt++;
				}
			}
		}

		return $options;
	}

	private function getImageArray( $pid ){
		$data = array();
		
		$base_dir = $this->mall_data_root . '/images/product'; // 베이스이미지폴더	
		$uploadDir = $base_dir . $this->uploadDirText ( $pid );
		$real_path = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
        //기본이미지 변환
		$this->smart_resize_image ( $real_path . "/basic_" . $pid . ".gif", $width = 100, $height = 100, $proportional = false, $output = $real_path . "/list_" . $pid . ".jpg", $delete_original = false, $use_linux_commands = false );
		$this->smart_resize_image ( $real_path . "/basic_" . $pid . ".gif", $width = 760, $height = 760, $proportional = false, $output = $real_path . "/fix_" . $pid . ".jpg", $delete_original = false, $use_linux_commands = false );
		$this->smart_resize_image ( $real_path . "/basic_" . $pid . ".gif", $width = 280, $height = 280, $proportional = false, $output = $real_path . "/pic1_" . $pid . ".jpg", $delete_original = false, $use_linux_commands = false );
		
		$default_web_url = "http://". $_SERVER['SERVER_NAME'] . $uploadDir;
        $data['listing'] = $uploadDir . "/list_" . $pid . ".jpg"; //130x130
        $data['fiximage'] = $uploadDir . "/fix_" . $pid . ".jpg"; // 고정이미지? large
        $data['picture1'] = $uploadDir . "/pic1_" . $pid . ".jpg"; // 기본사진(400x400)

        //추가이미지 변환
        $sql = "SELECT * FROM shop_addimage WHERE pid = '".$pid."'";
        $this->db->query($sql);
        if($this->db->total){
        	$result = $this->db->fetchall();
        	$count = 2;
        	$base_dir = $this->mall_data_root . '/images/addimg'; // 베이스이미지폴더	
			$uploadDir = $base_dir . $this->uploadDirText ( $pid );
			$real_path = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
        	foreach($result as $rt):
        		$this->smart_resize_image ( $real_path . "/basic_" . $rt['id'] . "_add.gif", $width = 760, $height = 760, $proportional = false, $output = $real_path . "/pic".$count."_". $rt['id'] . ".jpg", $delete_original = false, $use_linux_commands = false );
				$data['add']['Picture'.$count] = $uploadDir . "/pic".$count."_" . $rt['id'] . ".jpg";
        		$count++;
        	endforeach;
        }
        $this->tmp_img_array = $data;
        return $data;
	}

	public function smart_resize_image($file, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false) {
		if ($height <= 0 && $width <= 0) {
			return false;
		}
		if(file_exists($file)){
			$info = getimagesize ( $file );
		}else{
			return false;
		}
		$image = '';
		
		$final_width = 0;
		$final_height = 0;
		list ( $width_old, $height_old ) = $info;
		
		if ($proportional) {
			if ($width == 0)
				$factor = $height / $height_old;
			elseif ($height == 0)
				$factor = $width / $width_old;
			else
				$factor = min ( $width / $width_old, $height / $height_old );
			$final_width = round ( $width_old * $factor );
			$final_height = round ( $height_old * $factor );
		} else {
			$final_width = ($width <= 0) ? $width_old : $width;
			$final_height = ($height <= 0) ? $height_old : $height;
		}
		
		switch ($info [2]) {
			case IMAGETYPE_GIF :
				$image = imagecreatefromgif ( $file );
				break;
			case IMAGETYPE_JPEG :
				$image = imagecreatefromjpeg ( $file );
				break;
			case IMAGETYPE_PNG :
				$image = imagecreatefrompng ( $file );
				break;
			default :
				return false;
		}
		
		$image_resized = imagecreatetruecolor ( $final_width, $final_height );
		
		if (($info [2] == IMAGETYPE_GIF) || ($info [2] == IMAGETYPE_PNG)) {
			$trnprt_indx = imagecolortransparent ( $image );
			// If we have a specific transparent color
			if ($trnprt_indx >= 0) {
				// Get the original image's transparent color's RGB values
				$trnprt_color = imagecolorsforindex ( $image, $trnprt_indx );
				// Allocate the same color in the new image resource
				$trnprt_indx = imagecolorallocate ( $image_resized, $trnprt_color ['red'], $trnprt_color ['green'], $trnprt_color ['blue'] );
				// Completely fill the background of the new image with allocated color.
				imagefill ( $image_resized, 0, 0, $trnprt_indx );
				// Set the background color for new image to transparent
				imagecolortransparent ( $image_resized, $trnprt_indx );
			}			// Always make a transparent background color for PNGs that don't have one allocated already
			elseif ($info [2] == IMAGETYPE_PNG) {
				// Turn off transparency blending (temporarily)
				imagealphablending ( $image_resized, false );
				// Create a new transparent color for image
				$color = imagecolorallocatealpha ( $image_resized, 0, 0, 0, 127 );
				
				// Completely fill the background of the new image with allocated color.
				imagefill ( $image_resized, 0, 0, $color );
				
				// Restore transparency blending
				imagesavealpha ( $image_resized, true );
			}
		}
		
		imagecopyresampled ( $image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old );
		
		if ($delete_original) {
			if ($use_linux_commands)
				exec ( 'rm ' . $file );
			else
				@unlink ( $file );
		}
		
		switch (strtolower ( $output )) {
			case 'browser' :
				$mime = image_type_to_mime_type ( $info [2] );
				header ( "Content-type: $mime" );
				$output = NULL;
				break;
			case 'file' :
				$output = $file;
				break;
			case 'return' :
				return $image_resized;
				break;
			default :
				break;
		}
		
		//무조건 jpg로 변환
		imagejpeg ( $image_resized, $output );
		
		
		return true;
	}

	public function uploadDirText( $pid ) {
		$fstdir = "/" . substr ( $pid, 0, 2 );
		$sedir = "/" . substr ( $pid, 2, 2 );
		$thdir = "/" . substr ( $pid, 4, 2 );
		$fordir = "/" . substr ( $pid, 6, 2 );
		$fifdir = "/" . substr ( $pid, 8, 2 );
		return $fstdir . $sedir . $thdir . $fordir . $fifdir;
	}
	

	/**
	 * 연계된 카테고리 구하기
	 *
	 * @param string $pid        	
	 * @return array NULL
	 */

	private function getTargetCategory($pid = '') {
		//2016-02-18 TBL_SHOP_PRODUCT_RELATION - > TBL_SELLERTOOL_PRODUCT_RELATION 로 바꿈 /admin/sellertool/sellertool.lib.php 에 선언
		$sql = "SELECT cid 
            		FROM " . TBL_SELLERTOOL_PRODUCT_RELATION . " 
            		WHERE pid = '$pid' 
				ORDER BY CAST(basic as CHAR) DESC";
		$this->db->query ( $sql );
		if ($this->db->total) {
			$cid_list = $this->db->fetchall ();
			
			$cid_mapping_check = FALSE;
			foreach ( $cid_list as $cl ) :
				
				$sql = "SELECT
	                		target_cid,
	                		target_name
	                	FROM sellertool_category_linked_relation
	                	WHERE
	                		origin_cid = '" . $cl ['cid'] . "'
	                	AND site_code = '" . $this->site_code . "'";
				
				/*
				$sql = "SELECT
	                		target_cid,
	                		target_name
	                	FROM sellertool_category_linked_relation
	                	WHERE
	                		origin_cid = '" . $cl ['cid'] . "'
	                	AND api_key = '" . $this->api_key . "'";
				*/
				$this->db->query ( $sql );
				if ($this->db->total) {
					$cid_mapping_check = TRUE;
					$this->db->fetch ();
					break;
				}
			endforeach
			;
			if ($cid_mapping_check) {
				$result ['target_cid'] = $this->db->dt ["target_cid"];
				$result ['target_name'] = $this->db->dt ["target_name"];
				return $result;
			}
		}
		return NULL;
	}

	private function getTargetItemType($pid = '') {
		//2016-02-18 TBL_SHOP_PRODUCT_RELATION - > TBL_SELLERTOOL_PRODUCT_RELATION 로 바꿈 /admin/sellertool/sellertool.lib.php 에 선언
		$sql = "SELECT cid 
            		FROM " . TBL_SELLERTOOL_PRODUCT_RELATION . " 
            		WHERE pid = '$pid' 
				ORDER BY CAST(basic as CHAR) DESC";
		$this->db->query ( $sql );
		if ($this->db->total) {
			$cid_list = $this->db->fetchall ();
			
			$cid_mapping_check = FALSE;
			foreach ( $cid_list as $cl ) :
				
				$sql = "SELECT
	                		target_cid,
	                		target_name
	                	FROM sellertool_itemtype_linked_relation
	                	WHERE
	                		origin_cid = '" . $cl ['cid'] . "'
	                	AND site_code = '" . $this->site_code . "'";

				$this->db->query ( $sql );
				if ($this->db->total) {
					$cid_mapping_check = TRUE;
					$this->db->fetch ();
					break;
				}
			endforeach
			;
			if ($cid_mapping_check) {
				$result ['target_cid'] = $this->db->dt ["target_cid"];
				$result ['target_name'] = $this->db->dt ["target_name"];
				return $result;
			}
		}
		return NULL;
	}

	private function getTargetEtc($origin_code = '',$div='B') {
		
		if($div == 'B'){
			$search_column = "origin_code";
		}else{
			$search_column = "origin_name";
		}

		$sql = "SELECT
					target_code,
					target_name,
					delivery_policy_code ,
					return_policy_code,
					supplyer_commission
				FROM sellertool_etc_linked_relation
				WHERE
					".$search_column." = '" . trim(str_replace(array("\t","\n","\r"),"",$origin_code)) . "'
				AND site_code = '" . $this->site_code . "'
				AND etc_div = '". $div ."' ";

		$this->db->query ( $sql );
		if ($this->db->total) {
			$this->db->fetch ();
			$result ['target_code'] = $this->db->dt ["target_code"];
			$result ['target_name'] = $this->db->dt ["target_name"];
			$result ['delivery_policy_code'] = $this->db->dt ["delivery_policy_code"];
			$result ['return_policy_code'] = $this->db->dt ["return_policy_code"];
			$result ['supplyer_commission'] = $this->db->dt ["supplyer_commission"];
			return $result;
		}
		
		return NULL;
	}
	
	public function getDeleveryCompanyCode($quick) {

		$map_array  = array (
				"11" => "12",//현대택배
				"12" => "6",//대한통운
				"13" => "신세기",
				"15" => "13",//한진택배
				"16" => "1",//우체국택배
				"17" => "오렌지택배",
				"18" => "스피드코리아",
				"19" => "타사택배",
				"21" => "이클라인",
				"22" => "18",//CJ
				"29" => "삼성HTH택배",
				"32" => "퀵서비스",
				"57" => "트라넷택배",
				"58" => "호남택배",
				"59" => "지하철택배",
				"61" => "후다닥",
				"64" => "e-택배",
				"65" => "건영택배",
				"66" => "삼영택배",
				"67" => "아주택배",
				"68" => "KT택배",
				"69" => "Yellow cap",
				"70" => "5",//로젠택배
				"71" => "23",//일양택배
				"72" => "동서택배",
				"73" => "양양택배",
				"74" => "우리택배",
				"75" => "오세기택배",
				"76" => "고구려택배",
				"77" => "호남택배",
				"78" => "21",//경동택배
				"79" => "훼미리택배",
				"80" => "자체배송",
				"81" => "22",//대신택배
				"82" => "25",//천일택배
				"83" => "KGB특급택배",
				"84" => "한서택배",
				"85" => "일개미트랜스",
				"91" => "용차",
				"93" => "KGB로지스",
				"99" => "기타중소택배사",
				"ZZ" => "택배사없음"
		);
		
		$quick = (int)$quick;
		$return = array_search($quick,$map_array);

		if(empty($return))		$return = 99;
		
		//대한통운 12 -> 22
		if($return=='12')		$return = 22;

		return $return;
	}

	/**
	 * 택배사 코드 목록
	 *
	 * @return multitype:string
	 */
	
	public function getDeleveryCompanyCodeList() {
		
		return array (
				"11" => "현대택배",
				"12" => "대한통운",
				"13" => "신세기",
				"15" => "한진택배",
				"16" => "우체국",
				"17" => "오렌지택배",
				"18" => "스피드코리아",
				"19" => "타사택배",
				"21" => "이클라인",
				"22" => "CJGLS",
				"29" => "삼성HTH택배",
				"32" => "퀵서비스",
				"57" => "트라넷택배",
				"58" => "호남택배",
				"59" => "지하철택배",
				"61" => "후다닥",
				"64" => "e-택배",
				"65" => "건영택배",
				"66" => "삼영택배",
				"67" => "아주택배",
				"68" => "KT택배",
				"69" => "Yellow cap",
				"70" => "로젠택배",
				"71" => "일양택배",
				"72" => "동서택배",
				"73" => "양양택배",
				"74" => "우리택배",
				"75" => "오세기택배",
				"76" => "고구려택배",
				"77" => "호남택배",
				"78" => "경동택배",
				"79" => "훼미리택배",
				"80" => "자체배송",
				"81" => "대신택배",
				"82" => "천일택배",
				"83" => "KGB특급택배",
				"84" => "한서택배",
				"85" => "일개미트랜스",
				"91" => "용차",
				"93" => "KGB로지스",
				"99" => "기타중소택배사",
				"ZZ" => "택배사없음"
		);
		
	}
	
	
	/**
	 * 입력한 카테고리의 하위 카테고리(+1 depth) 가져오는 함수
	 *
	 * @param
	 *        	{string}dispNo = 카테고리넘버
	 * @return object 카테고리정보
	 */
	public function getSubCategory($dispNo = "") {
		$sql = "SELECT
           			depth,disp_name,disp_no,parent_no
           		FROM sellertool_received_category
           		WHERE
           			site_code = '".$this->site_code."' ";
		if (! empty ( $dispNo )) {
			$sql .= "AND
					parent_no = '" . $dispNo . "' ";
		} else {
			$sql .= "AND
					depth = 1 ";
		}
		$sql .= "ORDER BY disp_no ASC";
		$this->db->query ( $sql );
		if ($this->db->total) {
			$result = $this->db->fetchall ( "object", MYSQL_ASSOC );
			$key = 0;
			foreach ( $result as $rt ) :
				$return [$key] = new categoryData ();
				$return [$key]->depth = $rt ['depth'];
				$return [$key]->disp_name = $rt ['disp_name'];
				$return [$key]->disp_no = $rt ['disp_no'];
				$return [$key]->parent_no = $rt ['parent_no'];
				
				$key ++;
			endforeach
			;
		} else {
			$return = NULL;
		}
		return $return;
	}

	public function getSubItemType($dispNo = "") {
		$sql = "SELECT
           			depth,disp_name,disp_no,parent_no
           		FROM sellertool_received_itemtype
           		WHERE
           			site_code = '".$this->site_code."' ";
		if (! empty ( $dispNo )) {
			$sql .= "AND
					parent_no = '" . $dispNo . "' ";
		} else {
			$sql .= "AND
					depth = 1 ";
		}
		$sql .= "ORDER BY disp_no ASC";
		$this->db->query ( $sql );
		if ($this->db->total) {
			$result = $this->db->fetchall ( "object", MYSQL_ASSOC );
			$key = 0;
			foreach ( $result as $rt ) :
				$return [$key] = new categoryData ();
				$return [$key]->depth = $rt ['depth'];
				$return [$key]->disp_name = $rt ['disp_name'];
				$return [$key]->disp_no = $rt ['disp_no'];
				$return [$key]->parent_no = $rt ['parent_no'];
				
				$key ++;
			endforeach
			;
		} else {
			$return = NULL;
		}
		return $return;
	}
	
	

	 /**
     * 등록결과 로그에 넣기
     * 
     * @param {string} pid 상품코드
     * @param {string} add_info_id 등록옵션 시퀀스 
     * @param {string} target_cid 등록된 카테고리아이디
     * @param {string} target_name 등록된 카테고리명
     * @param {string} result 등록후 리턴받은 메시지
     */

	public function submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, $type='regist', $od_ix=''){
        
        $_message = str_replace("'","",$result->message);
        $_productNo = $result->productNo;
        $_resultCode = $result->resultCode;
		
		if($type == 'regist'){
			$sql = "select srl_ix from sellertool_regist_relation where site_code='".$this->site_code."' and pid='$pid' order by srl_ix desc limit 0,1";
			$this->db->query($sql);

			if($this->db->total){
				$this->db->fetch();
				$sql = "update sellertool_regist_relation set 
					result_pno='".$_productNo."',
					result_msg='".$_message."',
					result_code='".$_resultCode."',
					update_date=NOW()
				where srl_ix='".$this->db->dt[srl_ix]."'";
				$this->db->query($sql);
			}else{
				$sql = "insert into sellertool_regist_relation (site_code, pid, add_info_id, target_cid, target_name, result_pno, result_msg, result_code, regist_date)values('".$this->site_code."','$pid','$add_info_id','$target_cid','$target_name','$_productNo','$_message','$_resultCode',NOW())"; 
				$this->db->query($sql);
			}
		}

		$sql = "INSERT INTO 
				sellertool_log (site_code, type, pid, target_pid, add_info_id, target_cid, target_name, result_pno, result_msg, result_code, regist_date)
				values('".$this->site_code."','$type','$pid','$od_ix','$add_info_id','$target_cid','$target_name','$_productNo','$_message','$_resultCode',NOW())";
		$this->db->query($sql);

    }

	private function printError() {
		echo "<script>alert('" . $this->error ['code'] . " : " . $this->error ['msg'] . "');parent.select_update_unloading();</script>";
		exit;
	}

	/******************************************* 주문 API START ******************************************************************/
	/**
     * 발주확인할 내역(결재완료 목록조회)
     * 
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
	
	 function getOdrComplete($startTime, $endTime){
		set_time_limit(9999999);
		$requestXmlBody = $this->makeOrderListXml ( $startTime, $endTime );
		//print_r($requestXmlBody);
		$result = $this->call ( CJMALL_URL, $requestXmlBody );
		print_r($requestXmlBody);
		print_r($result);
		/*
		ordDtlClsCd
		J007 10 주문
		J007 20 취소
		J007 30 반품
		J007 31 반품취소
		J007 40 교환배송
		J007 41 교환배취
		J007 45 교환회수
		J007 46 교환회취
		J007 50 A/S
		J007 55 추가회수
		J007 56 구성품맞교환
		J007 57 추가발송
		04_01 인터페이스 문서가 혹시 없으신가요? 
		*/

		if(count($result->instruction) > 0){
			$key = 0;
			foreach($result->instruction as $rt):

				foreach($rt->instructionDetail as $rtd):

					if( (string)$rtd->ordDtlClsCd == '10'){
						$return[$key]["co_oid"]				=		(string)$rt->ordNo;//주문번호

						$return[$key]["bname"]				=		(string)$rt->custNm;//주문자명
						$return[$key]["btel"]				=		(string)$rt->custTelNo;//주문자 전화번호
						$return[$key]["delivery_dcprice"]	=		(int)$rt->custDeliveryCost;//총배송비 금액


						$co_od_ix = (string)$rtd->ordGSeq."|".(string)$rtd->ordDSeq."|".(string)$rtd->ordWSeq;
						$return[$key]["co_od_ix"]			=		$co_od_ix;//주문 순번

						$return[$key]["rname"]				=		(string)$rtd->receverNm;//수취인
						
						if( strlen((string)$rtd->zipno) == 6 ){
							$zip = substr((string)$rtd->zipno,0,3)."-".substr((string)$rtd->zipno,3,3);
						}else{
							$zip = (string)$rtd->zipno;
						}
						$return[$key]["zip"]				=		$zip;//수취인 우편번호
						$return[$key]["addr1"]				=		str_replace("'","`", (string)$rtd->addr_1);//수취인 주소
						$return[$key]["addr2"]				=		str_replace("'","`", (string)$rtd->addr_2);//수취인 주소
						$return[$key]["rtel"]				=		(string)$rtd->telno;//수취인 전화번호
						$return[$key]["rmobile"]			=		(string)$rtd->cellno;//수취인 핸드폰번호


						$unitCd = (string)$rtd->unitCd;
						$itemCd = (string)$rtd->itemCd;
						
						$sql = "SELECT shop_value FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid|id' and sellertool_key = 'unitCd' and sellertool_value ='". $unitCd ."' ";
						$this->db->query($sql);
						$this->db->fetch();
						list($pid,$op_id) = explode("|",$this->db->dt['shop_value']);

						if( empty($pid) ){

							$op_id = (string)$rtd->contItemCd; //협력사상품코드(단품코드)

							$sql = "SELECT shop_value FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid' and sellertool_key = 'itemCd' and sellertool_value ='". $itemCd ."' ";
							$this->db->query($sql);
							if($db->total > 0){
								$this->db->fetch();
								$pid = $this->db->dt['shop_value'];
							}else{
								$sql = "select pid from shop_product_options_detail where id='".$op_id."'";
								$this->db->query($sql);
								$this->db->fetch();
								$pid = $this->db->dt['pid'];
							}
						}

						$return[$key]["pid"]				=		$pid;//상품코드


						//착불 (1.선불 2. 착불)
						$return[$key]["delivery_pay_method"]=		'1';//배송비착불여부

						$return[$key]["option_id"]				=		$op_id;//옵션코드
						$return[$key]["pname"]					=		(string)$rtd->itemName;//상품명
						$return[$key]["option_text"]			=		(string)$rtd->unitNm;//옵션명
						$return[$key]["pcnt"]					=		(int)$rtd->outwQty;//수량
						$return[$key]["psprice"]				=		(string)$rtd->realslAmt;//상품 판매가(단품)
						$return[$key]["pt_dcprice"]				=		(string)$rtd->outwAmt;//총 상품 판매가
						$return[$key]["regdate"]				=		(string)$rtd->ordDtm;//주문번호생성일
						$return[$key]["ic_date"]				=		(string)$rtd->ordDtm;//주문결제완료일
						$return[$key]["msg"]					=		(string)$rtd->msgSpec;//배송 메모

						$return[$key]["bmobile"]			=		'';//주문자 핸드폰번호
						$return[$key]["bmail"]				=		'';//주문자 이메일

						$key++;
					}
				endforeach;
			endforeach;
		}

		return $return;
	}

	private function makeOrderListXml( $startTime, $endTime ) {
		
		//instructionCls 1=출고, 2=취소
		$xml='<?xml version="1.0" encoding="UTF-8"?>
		<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_04_01" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_04_01.xsd">
			<tns:vendorId>'.$this->api_key.'</tns:vendorId>
			<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			<tns:contents>
				<tns:instructionCls>1</tns:instructionCls>
				<tns:wbCrtDt>' . substr($startTime,0,4) . '-' . substr($startTime,4,2) . '-' . substr($startTime,6,2) . '</tns:wbCrtDt>
			</tns:contents>
		</tns:ifRequest>';
		
		return $xml;
	}

	/**
     * 출고 요청 (배송시작)
     */

	 function sendOrdReqDelivery($data){

		$requestXmlBody = $this->makeOrderDeliveryXml ( $data );
		//print_r($requestXmlBody);
		$result = $this->call ( CJMALL_URL, $requestXmlBody );

		//print_R($result->beNotYets);

		$Status = (string)$result->beNotYets->ifResult->successYn;
		$Message = (string)$result->beNotYets->ifResult->errorMsg;

		if( $Status == 'true'){
			//성공처리
			$return = new resultData();
			$return->resultCode = '200';
			$return->message = "연동완료 co_oid = ".$data['co_oid'].", co_od_ix = ".$data['co_od_ix'];
			$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'delivery',$data['od_ix']);
			$return->resultCode = 'success';
			return $return;
		}else{
			$return = new resultData();
			$return->resultCode = '500';
			$return->message = $Message." co_oid = ".$data['co_oid'].", co_od_ix = ".$data['co_od_ix'];
			$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'delivery',$data['od_ix']);
			$return->resultCode = 'fail';
			return $return;
		}
	}
	
	function makeOrderDeliveryXml($data){

		list($ordGSeq,$ordDSeq,$ordWSeq) = explode("|",$data['co_od_ix']);
		
		$xml='<?xml version="1.0" encoding="UTF-8"?>
		<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_04_04" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_04_04.xsd">
			<tns:vendorId>'.$this->api_key.'</tns:vendorId>
			<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			<tns:takeout>
				<tns:ordNo>' . $data['co_oid'] . '</tns:ordNo>
				<tns:ordGSeq>' . $ordGSeq . '</tns:ordGSeq>
				<tns:ordDSeq>' . $ordDSeq . '</tns:ordDSeq>
				<tns:ordWSeq>' . $ordWSeq . '</tns:ordWSeq>
				<tns:delicompCd>' . $this->getDeleveryCompanyCode( $data['quick'] ) . '</tns:delicompCd>
				<tns:wbNo>' . $data['invoice_no'] . '</tns:wbNo>
				<tns:vendorOrdId>' . $data['od_ix'] . '</tns:vendorOrdId>
			</tns:takeout>
		</tns:ifRequest>';

		return $xml;
	}

	/**
     * 주문 취소 완료 내역
     * 
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
	
	 function getCancelApplyOdrComplete($startTime, $endTime){

		$requestXmlBody = $this->makeCancelApplyOrderListXml ( $startTime, $endTime );
		//print_r($requestXmlBody);
		$result = $this->call ( CJMALL_URL, $requestXmlBody );

		/*
		ordDtlClsCd
		J007 10 주문
		J007 20 취소
		J007 30 반품
		J007 31 반품취소
		J007 40 교환배송
		J007 41 교환배취
		J007 45 교환회수
		J007 46 교환회취
		J007 50 A/S
		J007 55 추가회수
		J007 56 구성품맞교환
		J007 57 추가발송
		04_01 인터페이스 문서가 혹시 없으신가요? 
		*/

		if(count($result->instruction) > 0){
			$key = 0;
			foreach($result->instruction as $rt):

				foreach($rt->instructionDetail as $rtd):

					if( (string)$rtd->ordDtlClsCd == '20'){
						$return[$key]["co_oid"]				=		(string)$rt->ordNo;//주문번호

						$co_od_ix = (string)$rtd->ordGSeq."|".(string)$rtd->ordDSeq."|".(string)$rtd->ordWSeq;
						$return[$key]["co_od_ix"]			=		$co_od_ix;//주문 순번
						$return[$key]["pcnt"]				=		(int)$rtd->outwQty;//수량
						$return[$key]["co_claim_group"]		=		$return[$key]["co_oid"]."|".$return[$key]["co_od_ix"];//취소 등록 고유 번호
						$return[$key]["msg"]				=		(string)$rtd->cnclRsn . " " . (string)$rtd->cnclRsnSpec;//취소 등록 사유
						$return[$key]["regdate"]			=		date('Y-m-d H:i:s');//취소일자
						$return[$key]["reason_code"]		=		"SYS";

						$key++;
					}
				endforeach;
			endforeach;
		}

		return $return;
	}

	/**
     * 주문 취소 완료 내역 XML 생성
     * 
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
	function makeCancelApplyOrderListXml($startTime, $endTime ){
	
		//instructionCls 1=출고, 2=취소
		$xml='<?xml version="1.0" encoding="UTF-8"?>
		<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_04_01" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_04_01.xsd">
			<tns:vendorId>'.$this->api_key.'</tns:vendorId>
			<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			<tns:contents>
				<tns:instructionCls>2</tns:instructionCls>
				<tns:wbCrtDt>' . substr($startTime,0,4) . '-' . substr($startTime,4,2) . '-' . substr($startTime,6,2) . '</tns:wbCrtDt>
			</tns:contents>
		</tns:ifRequest>';
		
		return $xml;
	}


	/**
     * 상품 QNA 리스트 요청
     * 
     */
	
	 function getProductQnaList($startTime, $endTime){
		
		$requestXmlBody = $this->makeProductQnaListXml ( $startTime, $endTime );
		print_r($requestXmlBody);
		$result = $this->call ( CJMALL_URL, $requestXmlBody );
		
		print_R($result);
		exit;
		if(count($result->qad) > 0){
			$key = 0;
			foreach($result->qad as $rt):
				
				if( ! ((int)$rt->reply_cnt > 0) && (string)$rt->use_yn !=='0' ){
					$bbs_div = "P";

					$itemCd = (string)$rt->item_cd;
					$sql = "SELECT shop_value FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid' and sellertool_key = 'itemCd' and sellertool_value ='". $itemCd ."' ";
					$this->db->query($sql);
					$this->db->fetch();
					$pid = $this->db->dt['shop_value'];

					$sql = "select admin from shop_product where id='".$pid."'";
					$this->db->query($sql);
					$this->db->fetch();
					$company_id =$this->db->dt[admin];

					$return[$key]["bbs_div"]			=		$bbs_div;//구분
					$return[$key]["co_bbs_ix"]			=		(string)$rt->seq;//문의게시글번호
					$return[$key]["co_pid"]				=		$itemCd;//11st상품번호
					$return[$key]["bbs_subject"]		=		(string)$rt->item_nm;//제목
					$return[$key]["bbs_contents"]		=		(string)$rt->contents;//질문내용
					$return[$key]["bbs_name"]			=		(string)$rt->cust_nm . "(". (string)$rt->member_id .")";//고객명
					$return[$key]["pname"]				=		(string)$rt->item_nm;//상품명
					$return[$key]["pid"]				=		$pid;//상품번호
					$return[$key]["company_id"]			=		$company_id;//업체아이디
					$return[$key]["regdate"]			=		date('Y-m-d H:i:s');//등록일자
					$return[$key]["msg_type"]			=		'B';//메시지 구분 B : 일반  E : 긴급
					
					$key++;
				}

			endforeach;
		}
		
		print_R($return);
		exit;

        return $return;
	}
	
	function makeProductQnaListXml( $startTime, $endTime ){
		
		/*
		chn_cd
			30001001 CJmall
			30001016 CJmall(공동구매)
			30002008 B2E(공동구매)
			30005001 CJmall(올리브영)
			30006001 CJmall(CJ오마트)
			30008001 CJmall(슈대즐)
			30009001 CJmall(1st Look)
			30010001 CJmall(스타일로산다)
			30012001 CJmall(베이비오샵)
			50001005 Mobile(공동구매)
			50003001 Mobile(올리브영)
			50004001 Mobile(CJ오마트)
			50006001 Mobile(슈대즐)
			50007001 Mobile(1st Look)
			50008001 Mobile(스타일로산다)
			50010001 Mobile(베이비오샵)
		*/

		$xml = '<?xml version="1.0" encoding="UTF-8"?>
		<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_05_04" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_05_04.xsd">
			<tns:vendorId>'.$this->api_key.'</tns:vendorId>
			<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			<tns:contents>
				<tns:page>1</tns:page>
				<tns:rowsperpage>300</tns:rowsperpage>
				<tns:chn_cd>30001001</tns:chn_cd>
				<tns:item_cd></tns:item_cd>
				<tns:date1>' . substr($startTime,0,4) . '-' . substr($startTime,4,2) . '-' . substr($startTime,6,2) . '</tns:date1>
				<tns:date2>' . substr($endTime,0,4) . '-' . substr($endTime,4,2) . '-' . substr($endTime,6,2) . '</tns:date2>
				<tns:group_purch_gb></tns:group_purch_gb>
			</tns:contents>
		</tns:ifRequest>';

		return $xml;
	}

	/**
     * 상품 QNA 답변 요청
     * 
     */
	
	 function sendAnswerProductQna($data){

		$requestXmlBody='<?xml version="1.0" encoding="UTF-8"?>
		<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_05_06" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_05_06.xsd">
			<tns:vendorId>'.$this->api_key.'</tns:vendorId>
			<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			<tns:qans>
				<tns:item_cd>' . $data['co_pid'] . '</tns:item_cd>
				<tns:seq>' . $data["co_bbs_ix"] . '</tns:seq>
				<tns:recontents><![CDATA[' . $data["bbs_response"] . ']]></tns:recontents>
			</tns:qans>
		</tns:ifRequest>';

		$result = $this->call ( CJMALL_URL, $requestXmlBody );
		
		print_r($result);
		exit;
		$Status = (string)$result->qans->successYn;
		$Message = (string)$result->qans->successYn;
		
		if(! empty ($Status)){
			if( $Status == 'true'){
				//성공처리
				$return = new resultData();
				$return->resultCode = '200';
				$return->message = "연동완료";
				$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'bbs',$data["bbs_ix"]);
				$return->resultCode = 'success';
				return $return;
			}else{
				$return = new resultData();
				$return->resultCode = '500';
				$return->message = "연동실패 ".$Message;
				$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'bbs',$data["bbs_ix"]);
				$return->resultCode = 'fail';
				return $return;
			}
		}else{
			$return = new resultData();
			$return->resultCode = '500';
			$return->message = "응답없음 co_bbs_ix = ".$data['co_bbs_ix'];
			$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'bbs',$data["bbs_ix"]);
			$resultCode = 'response_fail';
			$return->resultCode = $resultCode;
			return $return;
		}
	}

	/**
     * SR 리스트 요청
     * 
     */
	
	 function getEmergencyMsgList($startTime, $endTime){
		
		$requestXmlBody = $this->makeEmergencyMsgListXml ( $startTime, $endTime );
		//print_r($requestXmlBody);
		$result = $this->call ( CJMALL_URL, $requestXmlBody );

		//print_R($result);
		//exit;
		if(count($result->sr) > 0){
			$key = 0;
			foreach($result->sr as $rt):

				foreach($rt->srProc as $rtd):
				
				if( (string)$rtd->progCd =='??' ){

					$bbs_div = "E";

					$itemCd = (string)$rt->itemCd;
					$sql = "SELECT shop_value FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid' and sellertool_key = 'itemCd' and sellertool_value ='". $itemCd ."' ";
					$this->db->query($sql);
					$this->db->fetch();
					$pid = $this->db->dt['shop_value'];

					$sql = "select admin from shop_product where id='".$pid."'";
					$this->db->query($sql);
					$this->db->fetch();
					$company_id =$this->db->dt[admin];
					

					$sql = "SELECT oid FROM shop_order_detail WHERE order_from = '".$this->site_code."' and co_oid='".(string)$rt->ordNo."' and co_od_ix='".(string)$rt->ordGSeq."|".(string)$rt->ordDSeq."|".(string)$rt->ordWSeq."' ";
					$this->db->query($sql);
					$this->db->fetch();
					$oid = $this->db->dt['oid'];

					$return[$key]["bbs_div"]			=		$bbs_div;//구분
					$return[$key]["pname"]				=		(string)$rt->itemNm . " " . (string)$rt->unitNm ;//상품명
					$return[$key]["co_bbs_ix"]			=		(string)$rt->vocId."|".(string)$rt->srSeq."|".(string)$rtd->srProcSeq;//문의게시글번호
					$return[$key]["co_od_ix"]			=		(string)$rt->ordNo."|".(string)$rt->ordGSeq."|".(string)$rt->ordDSeq."|".(string)$rt->ordWSeq;//주문번호
					$return[$key]["co_pid"]				=		$itemCd;//cj상품번호
					$return[$key]["bbs_subject"]		=		"[".(string)$rt->emergYn."] 주문번호 : " . $oid . " ". (string)$rt->qrNm . "(".(string)$rt->qrTelno.") 문의" ;//제목
					$return[$key]["bbs_contents"]		=		(string)$rtd->procSpec;//질문내용
					$return[$key]["bbs_name"]			=		(string)$rt->custNm . "-" . (string)$rt->recvNm ;//고객명
					$return[$key]["pid"]				=		$pid;//상품번호
					$return[$key]["company_id"]			=		$company_id;//업체아이디
					$return[$key]["regdate"]			=		date('Y-m-d H:i:s');//등록일자
					$return[$key]["msg_type"]			=		'E';//메시지 구분 B : 일반  E : 긴급
					
					$key++;
				}

				endforeach;
			endforeach;
		}
		
		print_R($return);
		exit;

        return $return;
	}
	
	function makeEmergencyMsgListXml( $startTime, $endTime ){
		
		//sschKwordCls 1=CJ의뢰일자/2=처리예정일

		$xml = '<?xml version="1.0" encoding="UTF-8"?>
		<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_05_01" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_05_01.xsd">
			<tns:vendorId>'.$this->api_key.'</tns:vendorId>
			<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			<tns:contents>
				<tns:reqDtm>' . substr($startTime,0,4) . '-' . substr($startTime,4,2) . '-' . substr($startTime,6,2) . '</tns:reqDtm>
			</tns:contents>
		</tns:ifRequest>';

		return $xml;
	}


	/**
     * SR답변
     * 
     */
	
	 function sendAnswerEmergencyMsg($data){

		list($vocId, $srSeq, $srProcSeq) = explode("|",$data["co_bbs_ix"]);
		
		//callYn 0:미통화/1:통화/2:부재중
		//venProcCd 20:처리중/40:완료
		//repairYn 0:선택안함/1:수선회수선택

		$sql = "select AES_DECRYPT(UNHEX(name),'".$this->db->ase_encrypt_key."') as name from common_member_detail where code='".$_SESSION['admininfo']['charger_ix']."'";
		$this->db->query($sql);
		$this->db->fetch();
		$procNm =$this->db->dt[name];

		$requestXmlBody='<?xml version="1.0" encoding="UTF-8"?>
		<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_05_02" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_05_02.xsd">
			<tns:vendorId>'.$this->api_key.'</tns:vendorId>
			<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			<tns:srProc>
				<tns:vocId>' . $vocId . '</tns:vocId>
				<tns:srSeq>' . $srSeq . '</tns:srSeq>
				<tns:srProcSeq>' . $srProcSeq . '</tns:srProcSeq>
				<tns:callYn>0</tns:callYn>
				<tns:procNm>' . $procNm . '(' . $_SESSION['admininfo']['charger_id'] . ')</tns:procNm>
				<tns:procSpec><![CDATA[' . $data["bbs_response"] . ']]></tns:procSpec>
				<tns:venProcCd>' . ($data['bbs_re_bool'] == "Y" ? "20" : "40") . '</tns:venProcCd>
				<tns:procPlanDt>' . date('Y-m-d') . '</tns:procPlanDt>
				<tns:repairYn>0</tns:repairYn>
			</tns:srProc>
		</tns:ifRequest>';

		$result = $this->call ( CJMALL_URL, $requestXmlBody );
		
		print_r($result);
		exit;
		$Status = (string)$result->srProc->successYn;
		$Message = (string)$result->srProc->successYn;
		
		if(! empty ($Status)){
			if( $Status == 'true'){
				//성공처리
				$return = new resultData();
				$return->resultCode = '200';
				$return->message = "연동완료";
				$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'bbs',$data["bbs_ix"]);
				$return->resultCode = 'success';
				return $return;
			}else{
				$return = new resultData();
				$return->resultCode = '500';
				$return->message = "연동실패 ".$Message;
				$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'bbs',$data["bbs_ix"]);
				$return->resultCode = 'fail';
				return $return;
			}
		}else{
			$return = new resultData();
			$return->resultCode = '500';
			$return->message = "응답없음 co_bbs_ix = ".$data['co_bbs_ix'];
			$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'bbs',$data["bbs_ix"]);
			$resultCode = 'response_fail';
			$return->resultCode = $resultCode;
			return $return;
		}
	}

	 /**
     * 판매취소/거부
     * 미배송정보 등록
     */

	 function setDenySell($data){

		//배송 지연 및 취소요청 관련 API인듯 40 재고부족취소, 50 고객취소요청 으로 요청시 취소가능??한지 확인 필요후 진행 
		$requestXmlBody = $this->makeDenySellXml ( $data );
		//print_r($requestXmlBody);

		$result = $this->call ( CJMALL_URL, $requestXmlBody );

		//print_R($result);

		$Status = (string)$result->beNotYets->ifResult->successYn;
		$Message = (string)$result->beNotYets->ifResult->errorMsg;

		if(! empty ($Status)){
			if( $Status == 'true'){
				//성공처리
				$return = new resultData();
				$return->resultCode = '200';
				$return->message = "판매취소 연동완료";
				$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'delivery',$data["od_ix"]);
				$return->resultCode = 'success';
				return $return;
			}else{
				$return = new resultData();
				$return->resultCode = '500';
				$return->message = "판매취소 연동실패 ".$Message;
				$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'delivery',$data["od_ix"]);
				$return->resultCode = 'fail';
				return $return;
			}
		}else{
			$return = new resultData();
			$return->resultCode = '500';
			$return->message = "판매취소 응답없음 co_oid = ".$data['co_oid'].", co_od_ix = ".$data['co_od_ix'];
			$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'delivery',$data["od_ix"]);
			$resultCode = 'response_fail';
			$return->resultCode = $resultCode;
			return $return;
		}
	 }

	 /**
     * 판매취소/거부 XML 생성
     * 
     */
	function makeDenySellXml( $data ){

		list($ordGSeq,$ordDSeq,$ordWSeq) = explode("|",$data['co_od_ix']);
		//noDelyGb Enum(10=재고부족지연,40=재고부족취소요청,50=고객취소요청, 92=고객지정일,93=고객부재,94=협력사지정)

		$sql="SELECT 
			reason_code, status_message
		FROM 
			shop_order_status os 
		WHERE
			os.oid='".$data[oid]."'
		and
			os.od_ix = '".$data[od_ix]."'
		and 
			os.status in ('CA','CC')
		ORDER BY os.regdate DESC LIMIT 1 ";
		$this->db->query($sql);
		$this->db->fetch();
		$reason_code = $this->db->dt['reason_code'];
		$status_message = $this->db->dt['status_message'];

		if( $reason_code=='PSL' || $reason_code=='PSO'){ //PSL:재고부족, PSO:상품품절
			$noDelyGb = '40';
		}else{
			$noDelyGb = '50';
		}

		$xml = '<?xml version="1.0" encoding="UTF-8"?>
		<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_04_03" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_04_03.xsd">
			<tns:vendorId>'.$this->api_key.'</tns:vendorId>
			<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			<tns:beNotYets>
				<tns:ordNo>' . $data['co_oid'] . '</tns:ordNo>
				<tns:ordGSeq>' . $ordGSeq . '</tns:ordGSeq>
				<tns:ordDSeq>' . $ordDSeq . '</tns:ordDSeq>
				<tns:ordWSeq>' . $ordWSeq . '</tns:ordWSeq>
				<tns:noDelyGb>' . $noDelyGb . '</tns:noDelyGb>
				<tns:noDelyDesc><![CDATA['.$status_message.']]></tns:noDelyDesc>
				<tns:hopeDelyDate>' . date('Y-m-d') . ' </tns:hopeDelyDate>
			</tns:beNotYets>
		</tns:ifRequest>';

		return $xml;
	}

	 /**
     * 배송완료
     * 고객인수 등록
     */

	 function cjmallDeliveryComplete($data){
		
		list($ordGSeq,$ordDSeq,$ordWSeq) = explode("|",$data['co_od_ix']);

		$requestXmlBody='<?xml version="1.0" encoding="UTF-8"?>
		<tns:ifRequest xmlns:tns="http://www.example.org/ifpa" tns:ifId="IF_04_05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.example.org/ifpa ../IF_04_05.xsd">
			<tns:vendorId>'.$this->api_key.'</tns:vendorId>
			<tns:vendorCertKey>'.$this->api_ticket.'</tns:vendorCertKey>
			<tns:receiveComplete>
				<tns:ordNo>' . $data['co_oid'] . '</tns:ordNo>
				<tns:ordGSeq>' . $ordGSeq . '</tns:ordGSeq>
				<tns:ordDSeq>' . $ordDSeq . '</tns:ordDSeq>
				<tns:ordWSeq>' . $ordWSeq . '</tns:ordWSeq>
				<tns:recvNm>' . $data['rname'] . '</tns:recvNm>
			</tns:receiveComplete>
		</tns:ifRequest>';

		$result = $this->call ( CJMALL_URL, $requestXmlBody );

		$Status = (string)$result->beNotYets->ifResult->successYn;
		$Message = (string)$result->beNotYets->ifResult->errorMsg;

		if(! empty ($Status)){
			if( $Status == 'true'){
				//성공처리
				$return = new resultData();
				$return->resultCode = '200';
				$return->message = "배송완료 연동완료";
				$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'delivery',$data["od_ix"]);
				$return->resultCode = 'success';
				return $return;
			}else{
				$return = new resultData();
				$return->resultCode = '500';
				$return->message = "배송완료 연동실패 ".$Message;
				$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'delivery',$data["od_ix"]);
				$return->resultCode = 'fail';
				return $return;
			}
		}else{
			$return = new resultData();
			$return->resultCode = '500';
			$return->message = "배송완료 응답없음 co_oid = ".$data['co_oid'].", co_od_ix = ".$data['co_od_ix'];
			$this->submitRegistLog($pid,$add_info_id,$target_cate_array['target_cid'],$target_cate_array['target_name'],$return,'delivery',$data["od_ix"]);
			$resultCode = 'response_fail';
			$return->resultCode = $resultCode;
			return $return;
		}
	 }
	
}
