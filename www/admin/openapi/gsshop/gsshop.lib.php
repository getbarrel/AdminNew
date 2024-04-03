<?php
/**
 * CJmall 상품 / 주문 API 라이브러리
 * 
 * @version 0.4
 * @author bgh
 * @date 2013.12.04
 */
require 'gsshop.config.php';
require 'gsshop.class.php';
require $_SERVER['DOCUMENT_ROOT'].'/admin/openapi/standard.object.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/class/database.class';
//include_once $_SERVER ['DOCUMENT_ROOT'] . '/admin/class/layout.class';
class Lib_gsshop extends Call_gsshop {
	private $db;
	private $site_code;
	private $api_key;
	private $api_ticket;
	private $userInfo;
	private $result;
	private $error_type;
	private $error;
	private $mall_data_root;

	private $item;

	private $regId;
	private $supCd;
	private $chrDlvcCd;
	private $prdRelspAddrCd;
	private $prdPrcSupGivRtamt;
	
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

		list($chrDlvcCd, $prdRelspAddrCd, $prdPrcSupGivRtamt) = explode("|",$this->api_ticket);

		$this->regId = $this->userInfo ['site_id']; //등록자
		$this->supCd = $this->api_key; //협력사코드
		$this->chrDlvcCd = $chrDlvcCd; //배송비 코드
		$this->prdRelspAddrCd = $prdRelspAddrCd; //상품출고지주소코드
		$this->prdPrcSupGivRtamt = $prdPrcSupGivRtamt; //협력사지급율

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
			$this->error ['msg'] = '제휴사 정보가 올바르지 않습니다.(gsshop)';
			if($this->error_type=="return"){
				return "[".$this->error ['code']."]".$this->error ['msg'];
			}else{
				$this->printError ();
			}
		}
	}

	/**
	 * 상품등록
	 * api 등록 절차
	 *
	 * @param string $pid        	
	 * @param string $add_info_id        	
	 */
	public function registGoods($pid = '', $add_info_id = '') {
		
		if($_SESSION['admininfo']['charger_id']=='forbiz'){
			echo time()."|||||".$pid."|수정시작<br/>";
		}

		$sql = "select * from shop_product where id = '".$pid."' and admin in (select company_id from sellertool_not_company where site_code = '".$this->site_code."' and state = '1')  ";
		$this->db->query($sql);
		//연동 제한 셀러의 경우 프로세스 진입 시 처리 안되도록
		if( !empty( $this->db->total ) ){
			return;
		}

		$sql = "SELECT * FROM sellertool_reponse where site_code = '".$this->site_code."' and shop_key='pid' and shop_value = '".$pid."' and sellertool_key = 'prdCd'";
		$this->db->query ( $sql );
		$this->db->fetch ();
		$prdCd = $this->db->dt['sellertool_value'];

		if( ! empty($prdCd) ){
			//modGbn N: 노출상품명 수정, P : 가격, S : 판매상태, I : 이미지, D : 기술서, N : 노출상품명, SA : 속성추가 및 주문가능수량수정, SS : 속성판매종료, B : 도서정보(도서몰전용)

			
			$modGbn = 'N';
			$this->makeAddItemArray ( $pid, $add_info_id, $modGbn , $prdCd );
			
			//xml 에러는 처음 한번만 체크하면됨
			if( ! empty($this->error['code'])){
				$this->result->productNo = $prdCd;
				$this->result->resultCode = "500";
				$this->result->message = $this->error ['msg'];
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
				$this->result->resultCode = 'fail';
				return $this->result;
			}

			$result = $this->call ( GSSHOP_URL, $this->item );
			list($rcode, $message, $_pid_, $supCd, $co_pid, $co_option_id_text ) = explode("|",$result);

			if($rcode!="S"){
				$this->result->productNo = $prdCd;
				$this->result->resultCode = "500";
				$this->result->message = $message;
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
				$this->result->resultCode = 'fail';
			}
			
			
			if($_SESSION['admininfo']['charger_id']=='forbiz'){
				echo time()."|||||".$pid."|가격시작<br/>";
			}

			$modGbn = 'P';
			$this->makeAddItemArray ( $pid, $add_info_id, $modGbn , $prdCd );

			$result = $this->call ( GSSHOP_URL, $this->item );
			
			list($rcode, $message, $_pid_, $supCd, $co_pid, $co_option_id_text ) = explode("|",$result);

			if($rcode!="S"){
				$this->result->productNo = $prdCd;
				$this->result->resultCode = "500";
				$this->result->message = $message;
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
				$this->result->resultCode = 'fail';
			}
			
			if($_SESSION['admininfo']['charger_id']=='forbiz'){
				echo time()."|||||".$pid."|가격수정<br/>";
			}

			$modGbn = 'SA';
			$this->makeAddItemArray ( $pid, $add_info_id, $modGbn , $prdCd );
			$result = $this->call ( GSSHOP_URL, $this->item );
			/*
			if($_SESSION['admininfo']['charger_id']=='forbiz'){
				print_r($result);
				exit;
			}
			*/
			
			if($_SESSION['admininfo']['charger_id']=='forbiz'){
				echo time()."|||||".$pid."|옵션응답<br/>";
			}

			$this->db->close();
			$this->db->dbcon($this->db->db_name);

			list($rcode, $message, $_pid_, $supCd, $co_pid, $co_option_id_text ) = explode("|",$result);
			if( ! empty($co_option_id_text) ){

				$co_option_ids = explode(",",$co_option_id_text);

				foreach($co_option_ids as $co_option_id){

					list($attrPrdListAttrPrdCd , $option_id) = explode("^",$co_option_id);
					$sql = "SELECT * FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid|id' and shop_value = '".$pid."|".$option_id."' and sellertool_key = 'attrPrdListAttrPrdCd' and sellertool_value ='".$attrPrdListAttrPrdCd."' ";
					$this->db->query($sql);
					
					if( empty( $this->db->total ) ){
						$sql = "insert into sellertool_reponse 
							(site_code,shop_key, shop_value, sellertool_key, sellertool_value, is_basic, regdate ) values('".$this->site_code."','pid|id','".$pid."|".$option_id."','attrPrdListAttrPrdCd','".$attrPrdListAttrPrdCd."','0', NOW())";
						$this->db->query($sql);
					}
				}
			}
			
			if($rcode!="S"){
				$this->result->productNo = $prdCd;
				$this->result->resultCode = "500";
				$this->result->message = $message;
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
				$this->result->resultCode = 'fail';
			}

			if($_SESSION['admininfo']['charger_id']=='forbiz'){
				echo time()."|||||".$pid."|옵션수정<br/>";
			}
			
			$modGbn = 'I';
			$this->makeAddItemArray ( $pid, $add_info_id, $modGbn , $prdCd );
			$result = $this->call ( GSSHOP_URL, $this->item );
			
			list($rcode, $message, $_pid_, $supCd, $co_pid, $co_option_id_text ) = explode("|",$result);

			if($rcode!="S"){
				$this->result->productNo = $prdCd;
				$this->result->resultCode = "500";
				$this->result->message = $message;
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
				$this->result->resultCode = 'fail';
			}
			
			
			$modGbn = 'D';
			$this->makeAddItemArray ( $pid, $add_info_id, $modGbn , $prdCd );
			$result = $this->call ( GSSHOP_URL, $this->item );
			
			list($rcode, $message, $_pid_, $supCd, $co_pid, $co_option_id_text ) = explode("|",$result);

			if($rcode!="S"){
				$this->result->productNo = $prdCd;
				$this->result->resultCode = "500";
				$this->result->message = $message;
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
				$this->result->resultCode = 'fail';
			}
			

			$modGbn = 'S';
			$this->makeAddItemArray ( $pid, $add_info_id, $modGbn , $prdCd );
		
		}else{
			$this->makeAddItemArray ( $pid, $add_info_id );
		}

		if(empty($this->error['code'])){
			
			//S|상품가(이) 저장성공하였습니다.|0000104859|1014508|19069652|19069652001^0000104859
			$result = $this->call ( GSSHOP_URL, $this->item );
			
			if($_SESSION['admininfo']['charger_id']=='forbiz'){
				echo time()."|||||".$pid."|판매수정<br/>";
			}

			//print_r($result);
			// E|null : EC노출 상품명 길이(30/60byte)초과, 수정 바랍니다.|0000103768|1014508|
			// S|SA : 성공하였습니다.|0000103768|1014508|19069648|19069648001^863846,19069648002^863847,19069648003^863848,19069648004^863849,19069648005^863850

//통신 연결 불완전한 이슈로 연동 결과 남김
//$fp = fopen($_SERVER["DOCUMENT_ROOT"]. $this->mall_data_root . '_logs/sellertool/gsshop/log/API_LOG_' . date('Ym') . '.log', 'a');
//$fh = $result.'
//';
//fwrite($fp, $fh);
//fclose($fp);
			
			//2016-03-08 Hong 임시 상품등록이 늦어 자꾸 2006 MySQL server has gone away 에러가 나서 임시 처리
			$this->db->close();
			$this->db->dbcon($this->db->db_name);

			list($rcode, $message, $_pid_, $supCd, $co_pid, $co_option_id_text ) = explode("|",$result);

			if( ! empty($rcode) ){
				if( ! empty($co_pid) ){
					$sql = "SELECT * FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid' and shop_value = '".$pid."' and sellertool_key = 'prdCd' and sellertool_value ='".$co_pid."' ";
					$this->db->query($sql);
					
					if( empty( $this->db->total ) ){
						$sql = "insert into sellertool_reponse 
							(site_code,shop_key, shop_value, sellertool_key, sellertool_value, is_basic, regdate ) values('".$this->site_code."','pid','".$pid."','prdCd','".$co_pid."','0', NOW())";
						$this->db->query($sql);
					}
				}

				if( ! empty($co_option_id_text) ){

					$co_option_ids = explode(",",$co_option_id_text);

					foreach($co_option_ids as $co_option_id){
						list($attrPrdListAttrPrdCd , $option_id) = explode("^",$co_option_id);
						$sql = "SELECT * FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid|id' and shop_value = '".$pid."|".$option_id."' and sellertool_key = 'attrPrdListAttrPrdCd' and sellertool_value ='".$attrPrdListAttrPrdCd."' ";
						$this->db->query($sql);
						
						if( empty( $this->db->total ) ){
							$sql = "insert into sellertool_reponse 
								(site_code,shop_key, shop_value, sellertool_key, sellertool_value, is_basic, regdate ) values('".$this->site_code."','pid|id','".$pid."|".$option_id."','attrPrdListAttrPrdCd','".$attrPrdListAttrPrdCd."','0', NOW())";
							$this->db->query($sql);
						}
					}
				}

				if($_SESSION['admininfo']['charger_id']=='forbiz'){
					echo time()."|||||".$pid."|수정완료<br/>";
				}

				//성공처리
				if($rcode=="S"){
					$this->result->message = '상품등록\수정 완료';
					$this->result->resultCode = '200';
					$this->result->productNo = $co_pid;
					$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);

					//프론트 처리용 결과코드
					$resultCode = 'success';
					$this->result->resultCode = $resultCode;
				}else{
					$this->result->message = $message;
					$this->result->resultCode = "500";
					$this->result->productNo = $co_pid;
					//임시로 차후 주석처리!
					$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
					$resultCode = 'fail';
					$this->result->resultCode = $resultCode;
				}

				return $this->result;
			}else{
				$this->result->productNo = $prdCd;
				$this->result->resultCode = "500";
				$this->result->message = "연동에 실패 하였습니다[응답없음]";
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$this->result);
				$resultCode = 'fail';
				$this->result->resultCode = $resultCode;
				return $this->result;
			}

		}else{
			$this->result->productNo = $prdCd;
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
	 
	private function makeAddItemArray($pid = '', $add_info_id = '' , $modGbn = '', $prdCd = ''){
		global $HEAD_OFFICE_CODE;
		
		$this->error ['code'] = "";
		$this->error ['msg'] = "";
		
		//2016-03-08 Hong 임시 상품등록이 늦어 자꾸 2006 MySQL server has gone away 에러가 나서 임시 처리
		$this->db->close();
		$this->db->dbcon($this->db->db_name);

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
		
		$target_brand = $this->getTargetEtc ( $pinfo['brand'] , 'B' ); // 브랜드
		if (empty ( $target_brand )) {
			$this->error ['code'] = '2001';
			$this->error ['msg'] = '브랜드 연동이 필요합니다.';
			return;
		}else{
			 
			if($target_brand[delivery_policy_code]){
				$this->chrDlvcCd = $target_brand[delivery_policy_code]; //배송비 코드
			}else{
				$this->error ['code'] = '2001';
				$this->error ['msg'] = '브랜드별 배송정책 코드가 필요합니다.';
				return;
			}
			if($target_brand[return_policy_code]){
				$this->prdRelspAddrCd = $target_brand[return_policy_code]; //상품출고지주소코드
			}else{
				$this->error ['code'] = '2001';
				$this->error ['msg'] = '브랜드별 상품출고지 코드가 필요합니다.';
				return;
			}
			 
			if($target_brand[supplyer_commission]){
				$this->prdPrcSupGivRtamt = $target_brand[	supplyer_commission]; //협력사지급율
			}else{
			
				$this->error ['code'] = '2001';
				$this->error ['msg'] = '브랜드별 협력사 지급율 정보가 필요합니다.['.$target_brand[supplyer_commission].']';
				return;
			}
		}

		$target_itemtype = $this->getTargetItemType ( $pid ); // 연계된 품목 정보
		if (empty ( $target_itemtype )) {
			$this->error ['code'] = '2001';
			$this->error ['msg'] = '품목(상품)분류 연동이 필요합니다.';
			return;
		}

		if(!($pinfo ['sellprice'] > 0)){
			$this->error ['code'] = '2002';
			$this->error ['msg'] = '판매가가 없습니다.';
			return;
		}

		
		$operMdId = $this->getOperMdId($target_brand['target_code']);//운영mdid
		if (empty ( $operMdId )) {
			$this->error ['code'] = '2001';
			$this->error ['msg'] = 'MDID가 없습니다. 브랜드코드 = '.$target_brand['target_code'];
			return;
		}

		$options = $this->makeOptionArray($pinfo['id'], $pinfo);

		$Official = $this->makeOfficialNoticeArray($pinfo['id']);

		if( $options['option_check']=='NOT' ){
			$this->error ['code'] = '2003';
			$this->error ['msg'] = '가격이 같은 옵션이 없습니다.';
			return;
		}

		$image_server_domain = IMAGE_SERVER_DOMAIN;
		if( empty($image_server_domain) ){
			$image_server_domain = "http://". $_SERVER['SERVER_NAME'];
		}
	
		if( ! empty($modGbn) ){
			$regGbn = 'U';
		}else{
			$regGbn = 'I';
		}
		
		$sql = "select dt.* from 
		shop_product_delivery pd ,
		shop_delivery_template dt
		where pd.pid='".$pid."' and pd.dt_ix=dt.dt_ix";
		$this->db->query($sql);
		$this->db->fetch();
		$dt_ix_template = $this->db->fetch();
		
		$dlvsCoCd = $this->getDeleveryCompanyCode($dt_ix_template['tekbae_ix']);
		
		if( $dt_ix_template['delivery_policy']=='1' ){//무료배송
			$chrDlvYn = 'N';
		}else{
			$chrDlvYn = 'Y';
		}
		
		$rtpOnewyRndtrpCd = $dt_ix_template['return_shipping_cnt'];
		
		$bundlDlvCd = 'A01';
		if( ($dt_ix_template['delivery_policy']=='3' && $dt_ix_template['delivery_package']=='Y') || $dt_ix_template['delivery_policy']=='6' ){// 결제금액당
			$bundlDlvCd = 'A02';
		}

		if($dt_ix_template['delivery_policy'] == '6' || $dt_ix_template['delivery_policy'] == '4'){
			$this->error ['code'] = '2002';
			$this->error ['msg'] = '수량 단위별 배송비 정책은 사용할 수 없습니다.';
			return;
		}
		
		//해당 값은 가격에 대한 유효시작일시를 나타내는 값입니다. 해당 값에 대한 입력값은 현재시점을 기준으로 입력해주셔야 합니다.
		//$saleStrDtm = date('YmdHis',strtotime( $pinfo['regdate'] ));
		$saleStrDtm = date('YmdHis');
		$saleEndDtm = '29991231235959';
		
		/* 2016-07-13 판매기간이 29991231235959 이게 아니면 품절 처리됨... ㅜ
		if( $pinfo['is_sell_date']=='1' ){
			$saleStrDtm = date('Ymd000000',strtotime( $pinfo['sell_priod_sdate'] ));
			$saleEndDtm = date('Ymd235959',strtotime( $pinfo['sell_priod_edate'] ));
		}
		*/
		
		if($pinfo['state'] != '1' || $pinfo['disp'] != '1'){
			$saleEndDtm = date('YmdHis',strtotime('+1 minutes'));
		}
		
		if( $pinfo['is_sell_date']=='1' && $regGbn == 'U' ){// 수정일때
			$saleStrDtm = date('YmdHis');
		}

		if($_SESSION['admininfo']['charger_id']=='forbiz'){
			//print_r($saleStrDtm."<br/>");
			//print_r($saleEndDtm."<br/>");
			//exit;
		}
		
		/*
		include_once($_SERVER['DOCUMENT_ROOT'].'/class/jglory/shoplinker/SLPObjectBuilder.php');// - make_apply_name
		$SLP = new SLPObjectBuilder();
		$pname = $SLP->make_apply_name(MALL_CODE_AUCTION,$pinfo[product_sale_type],$pinfo[brand_name],$pinfo[pcode],$pinfo[pname]);
		*/

		$pname = $pinfo[pname];
		$prdNm = $pinfo[pname];
		$prdNmChgExposPrdNm = $pname;

		//$prdNm = mb_strimwidth($pinfo[pname], 0, 26, "...", "UTF8");
		//$prdNmChgExposPrdNm = mb_strimwidth($pname, 0, 36, "...", "UTF8");

		//$pname = urlencode($pname);
		//$prdNm = urlencode($prdNm);
		//$prdNmChgExposPrdNm = urlencode($prdNmChgExposPrdNm);

		//taxTypCd 01 : 면세, 02 : 과세, 03 : 영세
		if( $pinfo['surtax_yorn'] == 'Y' ){
			$taxTypCd = "01";
		}else{
			$taxTypCd = "02";
		}
		
		//prdTypCd P : 일반 (속성구분이 없는 경우) S : 속성 (색상/사이즈/형태/사이즈가 있는 경우)
		if( count($options['options']) > 0){
			$prdTypCd = 'S';
		}else{
			$prdTypCd = 'P';
		}
		
		if( $pinfo['is_adult'] == '1' ){
			$adultCertYn = 'Y';
		}else{
			$adultCertYn = 'N';
		}

		$prdPrcSupGivRtamt = round ($pinfo['sellprice'] * ( 100 - $this->prdPrcSupGivRtamt) / 100);

		$basicinfo = str_replace("http://".$_SERVER['SERVER_NAME']."/data","/data",$pinfo["basicinfo"]); //상세 이미지 url에 도메인 추가
		$basicinfo = str_replace("http://www.".$_SERVER['SERVER_NAME']."/data","/data",$basicinfo); //상세 이미지 url에 도메인 추가
		$basicinfo = str_replace("/data","http://".$_SERVER['SERVER_NAME']."/data",$basicinfo); //상세 이미지 url에 도메인 추가
		//$data_text_convert = "<img src='http://images.enter6.co.kr/data/entersix_data/images/banner/39/0908_976.jpg'> ";
		$data_text_convert .= str_replace("<IMG","<img",$basicinfo);
		$data_text_convert = str_replace('"',"&quot;",$data_text_convert);
		$data_text_convert = str_replace("'","&quot;",$data_text_convert);
		preg_match_all("|<img .*src=&quot;(.*)&quot;.*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);
		for($i=0;$i < count($out[1]);$i++){

			if(substr_count($out[1][$i],"http://")==0 && substr_count($out[1][$i],"https://")==0){
				$data_text_convert = str_replace($out[1][$i],$image_server_domain.$out[1][$i],$data_text_convert);
			}
		}
		//$data_text_convert .= "<img src='http://images.enter6.co.kr/data/entersix_data/templet/entersix/images/entersix_delivery_img2.jpg' alt='' border='0'>";
		$basicinfo = str_replace("&quot;",'"',$data_text_convert);
		
		if( $pinfo['stock_use_yn'] == 'Q' || $pinfo['stock_use_yn'] == 'Y' ){
			$attrPrdListOrdPsblQty = $pinfo['stock'];
		}else{
			$attrPrdListOrdPsblQty = "999";
		}

		if($regGbn == 'I'){
			
			$img_array = $this->getImageArray( $pinfo['id'] );

			//주석 * 는 필수
			$this->item = array();
			$this->makeItemCommon($regGbn, $modGbn, $pinfo);
			$this->makeItemPname($saleStrDtm, $saleEndDtm, $prdNmChgExposPrdNm, $pinfo);
			$this->makeItemPrice($saleStrDtm, $saleEndDtm, $prdPrcSupGivRtamt, $pinfo);
			$this->makeItemImg($image_server_domain, $img_array);
			$this->makeItemBasicinfo($basicinfo);
			$this->makeItemDisp($saleStrDtm, $saleEndDtm);
			$this->makeItemOption($prdTypCd, $saleStrDtm, $saleEndDtm, $attrPrdListOrdPsblQty, $options, $pinfo, $regGbn);

			$this->item[]['prdSpecListCnt'] = ''; //사양리스트건수
			$this->item[]['prdDescdGnrlListCnt'] = '0'; //* 일반기술서리스트건수
			$this->item[]['prdDescdHtmlItmListCnt'] = '0'; //* 이미지항목기술서리스트건수
			$this->item[]['pmoListCnt'] = '0'; //프로모션리스트건수
			$this->item[]['pmoGftListCnt'] = '0'; //프로모션사은품리스트건수
			$this->item[]['prdSectListCnt'] = count($target_cate); //매장정보리스트건수
			$this->item[]['prdGovPublsItmListCnt'] = count($Official);
			$this->item[]['brandCd'] = $target_brand['target_code'];//* 브랜드코드
			$this->item[]['dlvPickMthodCd'] = '3200';//* 배송수거방법코드
			$this->item[]['dlvsCoCd'] = $dlvsCoCd;//* 택배사코드
			$this->item[]['cardUseLimitYn'] = 'N';//카드사용제한여부
			$this->item[]['baseAccmLimitYn'] = 'Y';//* 기본적립금제한여부
			$this->item[]['selAccmApplyYn'] = 'Y';//* 선택적립금적용여부
			$this->item[]['selAccRt'] = '';//* 선택적립율
			$this->item[]['immAccmDcLimitYn'] = 'Y';//* 즉시적립금할인제한여부
			$this->item[]['immAccmDcRt'] = '';//* 즉시적립율
			$this->item[]['mnfcCoNm'] = $pinfo['company'];//* 제조사명
			$this->item[]['operMdId'] = $operMdId;//* 운영mdid
			$this->item[]['prdClsCd'] = $target_itemtype['target_cid'];//* 상품분류코드
			$this->item[]['orgpNm'] = $pinfo['origin'];//* 원산지명
			$this->item[]['prdNm'] = $prdNm;//* 상품명(송장)
			$this->item[]['autoOrdPrdNm'] = '';//자동주문상품명
			$this->item[]['prdEngNm'] = '';//상품영문명
			$this->item[]['regChanlGrpCd'] = 'GE';//* 등록채널그룹코드
			$this->item[]['ordPrdTypCd'] = '02';//* 주문상품유형코드
			$this->item[]['taxTypCd'] = $taxTypCd;//* 세금유형코드
			$this->item[]['dlvDtGuideCd'] = 'N';//* 배송일자안내코드
			$this->item[]['oboxCd'] = '';//* 합포장코드
			$this->item[]['chrDlvYn'] = $chrDlvYn;//* 유료배송여부
			 
		 
			$this->item[]['chrDlvcCd'] = $this->chrDlvcCd;//* 유료배송비코드
	 
			$this->item[]['exchRtpChrYn'] = 'Y';//* 교환반품유료여부
			$this->item[]['rtpDlvcCd'] = $this->chrDlvcCd;//* 반품배송비코드
			$this->item[]['rtpOnewyRndtrpCd'] = $rtpOnewyRndtrpCd;//* 반품편도왕복코드
			$this->item[]['exchOnewyRndtrpCd'] = '2';//* 교환편도왕복코드
			$this->item[]['chrDlvAddYn'] = 'N';//* 유료배송추가여부
			$this->item[]['prdGbnCd'] = '00';//*상품구분코드 00 : 일반상품, 02 : 사은품-업체제공
			$this->item[]['bundlDlvCd'] = $bundlDlvCd;//* 묶음배송코드
			$this->item[]['modelNo'] = '';//모델번호
			$this->item[]['cpnApplyTypCd'] = '00';//* 쿠폰적용유형코드  00 : 모든쿠폰허용 03 : 상품쿠폰만 적용 09 : 쿠폰제한
			$this->item[]['openAftRtpNoadmtYn'] = 'N';//* 개봉후반품불가여부
			$this->item[]['istTypCd'] = '';//입고유형코드
			$this->item[]['prdRelspAddrCd'] = $this->prdRelspAddrCd;//* 상품출고지주소코드
			$this->item[]['prdRetpAddrCd'] = $this->prdRelspAddrCd;//* 상품반송지주소코드
			$this->item[]['separOrdNoadmtYn'] = 'N';//* 단독주문불가여부
			$this->item[]['gftTypCd'] = '00';//* 사은품유형코드
			$this->item[]['prchTypCd'] = '03';//* 매입유형코드
			$this->item[]['zrwonSaleYn'] = 'N';//* 0원판매여부
			$this->item[]['subSupCd'] = $this->supCd;//* 하위협력사코드 
			$this->item[]['ordMnfcYn'] = 'N';//* 주문제작여부
			$this->item[]['ordMnfcTypCd'] = '';//* 주문제작유형코드
			$this->item[]['ordMnfcCntnt'] = '';//* 주문제작내용
			$this->item[]['ordMnfcTermDdcnt'] = '';//* 주문제작기간일수
			$this->item[]['attrTypExposCd'] = 'L';//* 속성유형노출코드
			$this->item[]['adultCertYn'] = $adultCertYn;//* 성인인증여부
			$this->item[]['barcdNo'] = '';//바코드번호
			$this->item[]['apntDlvDlvsCoCd'] = '';//* 지정배송택배사코드
			$this->item[]['apntPickDlvsCoCd'] = '';//* 지정수거택배사코드
			$this->item[]['gnuinYn'] = 'N';//* 정품여부
			$this->item[]['frmlesPrdTypCd'] = 'N';//* 무형상품유형코드
			$this->item[]['rsrvSalePrdYn'] = 'N';//예약판매여부
			$this->item[]['attrTypNm1'] = $options['option_name'][0];//속성유형명1
			$this->item[]['attrTypNm2'] = $options['option_name'][1];
			$this->item[]['attrTypNm3'] = $options['option_name'][3];
			$this->item[]['attrTypNm4'] = $options['option_name'][4];
			$this->item[]['prdBaseCmposCntnt'] = $pname;//* 상품기본구성내용
			$this->item[]['orgprdPkgCnt'] = '1';//* 본품포장갯수
			$this->item[]['prdAddCmposCntnt'] = '';//상품추가구성내용
			$this->item[]['addCmposPkgCnt'] = '';//추가구성포장개수
			$this->item[]['addCmposOrgpNm'] = '';//추가구성원산지명
			$this->item[]['addCmposMnfcCoNm'] = '';//추가구성제조사명
			$this->item[]['prdGftCmposCntnt'] = '';//상품사은품구성내용
			$this->item[]['gftPkgCnt'] = '';//사은품포장개수
			$this->item[]['gftCmposOrgpNm'] = '';//사은품구성원산지명
			$this->item[]['gftCmposMnfcCoNm'] = '';//사은품구성제조사명
			foreach($target_cate as $tc){
				$this->item[]['prdSectListSectid'] = $tc['target_cid'];//매장정보아이디
			}
			$this->item[]['pmoListValidStrDtm'] = '';//유효시작일시
			$this->item[]['pmoListValidEndDtm'] = '';//유효종료일시
			$this->item[]['pmoListChanlCd'] = '';//채널코드
			$this->item[]['pmoListApplyPriorRank'] = '';//적용우선순위
			$this->item[]['pmoListOfferTypCd'] = '';//오퍼유형코드
			$this->item[]['pmoListRtAmtCd'] = '';//율액코드
			$this->item[]['pmoListGshsApplyRtAmt'] = '';//당사적용율액
			$this->item[]['pmoListSupApplyRtAmt'] = '';//협력사적용율액
			$this->item[]['pmoListNoIntMmCnt'] = '';//무이자개월수
			$this->item[]['pmoListGftSelQty'] = '';//사은품선택개수
			$this->item[]['pmoGftListSupGftPrdCd'] = '';//협력사사은품상품코드
			$this->item[]['pmoGftListGftSupCd'] = '';//사은품협력사코드
			$this->item[]['pmoGftListAddCmposGbnCd'] = '';//필수여부
			$this->item[]['pmoGftListMandYn'] = '';//사용여부
			$this->item[]['pmoGftListIndiviDlvPmsnYn'] = '';//개별배송허용여부
			$this->item[]['pmoGftListWthdrwYn'] = '';//회수여부
			$this->item[]['safeCertGbnCd'] = '0';//* 안전인증구분정보 0 : 해당사항없음, 1 : 전기안전인증, 2 : 공산품안전인증, 3 : 공산품자율안전확인번호, 4 : 전기용품자율안전확인
			$this->item[]['safeCertOrgCd'] = '0';//* 인증기관
			$this->item[]['safeCertModelNm'] = '';//인증모델명
			$this->item[]['safeCertNo'] = '';//인증번호
			$this->item[]['safeCertDt'] = '';//인증일
			$this->item[]['safeCertFileNm'] = '';//안전인증파일명

			foreach($Official as $govPublsItmCd => $govPublsItmCntnt){
				$this->item[]['govPublsItmCd'] = $govPublsItmCd;//* 정부고시항목값
				$this->item[]['govPublsItmCntnt'] = $govPublsItmCntnt;//* 정부고시항목내용
			}

		}elseif($modGbn=='P'){//가격수정
			//http://test1.gsshop.com/alia/aliaCommonPrd.gs?regGbn=U&modGbn=P&regId=BAN&supPrdCd=20160119_02&supCd=1020909&prdPrcValidStrDtm=20160222200000&prdPrcValidEndDtm=29991231235959&prdPrcSalePrc=15000&prdPrcPrchPrc=&prdPrcSupGivRtamtCd=01&prdPrcSupGivRtamt=10000
			$this->item = array();
			$this->makeItemCommon($regGbn, $modGbn, $pinfo);
			$this->makeItemPrice($saleStrDtm, $saleEndDtm, $prdPrcSupGivRtamt, $pinfo);
		}elseif($modGbn=='I'){//이미지
			//http://test1.gsshop.com/alia/aliaCommonPrd.gs?regGbn=U&modGbn=I&regId=BAN&prdCntntListCnt=1&supPrdCd=20160119_02&supCd=1020909&prdCntntListCntntUrlNm=http%3A%2F%2Fimage.gsshop.com%2Fimage%2F17%2F03%2F13%2F17031369_L1.jpg

			$img_array = $this->getImageArray( $pinfo['id'] );

			$this->item = array();
			$this->makeItemCommon($regGbn, $modGbn, $pinfo);
			$this->makeItemImg($image_server_domain, $img_array);
		}elseif($modGbn=='D'){//기술서
			//http://test1.gsshop.com/alia/aliaCommonPrd.gs?regGbn=U&modGbn=D&regId=BAN&supPrdCd=20160119_02&supCd=1020909&prdDescdHtmlDescdExplnCntnt=%3CP%3ETEST2%3C%2Fp%3E%0A%3Cimg%20src%3D%22http%3A%2F%2Fimage.gsshop.com%2Fimage%2F17%2F03%2F13%2F17031369_L1.jpg%22%2F%3E
			$this->item = array();
			$this->makeItemCommon($regGbn, $modGbn, $pinfo);
			$this->makeItemBasicinfo($basicinfo);
		}elseif($modGbn=='S'){//판매상태
			/*
			"판매종료
			http://test1.gsshop.com/alia/aliaCommonPrd.gs?regGbn=U&modGbn=S&regId=BAN&saleEndDtm=20160222100000&supPrdCd=20160119_02&supCd=1020909&attrSaleEndStModYn=Y

			판매중
			http://test1.gsshop.com/alia/aliaCommonPrd.gs?regGbn=U&modGbn=S&regId=BAN&saleEndDtm=29991231235959&supPrdCd=20160119_02&supCd=1020909&attrSaleEndStModYn=Y"
			*/
			$this->item = array();
			$this->makeItemCommon($regGbn, $modGbn, $pinfo);
			$this->makeItemDisp($saleStrDtm, $saleEndDtm);
		}elseif($modGbn=='SA'){//속성(주문가능수량)
			/*
			"일반상품
			http://test1.gsshop.com/alia/aliaCommonPrd.gs?regGbn=U&modGbn=SA&regId=BAN&attrPrdListCnt=1&supPrdCd=20160119_02&supCd=1020909&prdTypCd=P&attrPrdListSupAttrPrdCd=241395731&attrPrdListAttrPrdCd=&attrPrdListAttrValCd1=00000&attrPrdListAttrValCd2=00000&attrPrdListAttrValCd3=00000&attrPrdListAttrValCd4=00000&attrPrdListSaleStrDtm=20160222200000&attrPrdListSaleEndDtm=29991231235959&attrPrdListModelNo=&attrPrdListAttrVal1=공통&attrPrdListAttrVal2=공통&attrPrdListAttrVal3=공통&attrPrdListAttrVal4=공통&attrPrdListArsAttrVal1=&attrPrdListArsAttrVal2=&attrPrdListArsAttrVal3=&attrPrdListArsAttrVal4=&attrPrdListAttrPkgCnt=&attrPrdListAttrCmposCntnt=&attrPrdListOrgpNm=&attrPrdListMnfcCoNm=&attrPrdListSafeStockQty=10&attrPrdListTempoutYn=N&attrPrdListTempoutDtm=&attrPrdListChanlGrpCd=AZ&attrPrdListOrdPsblQty=500

			속성상품
			http://test1.gsshop.com/alia/aliaCommonPrd.gs?regGbn=U&modGbn=SA&regId=BAN&attrPrdListCnt=1&supPrdCd=20160120_01&supCd=1020909&prdTypCd=S&attrPrdListSupAttrPrdCd=20160120_01&attrPrdListAttrPrdCd=&attrPrdListAttrValCd1=00000&attrPrdListAttrValCd2=00000&attrPrdListAttrValCd3=00000&attrPrdListAttrValCd4=00000&attrPrdListSaleStrDtm=20160222200000&attrPrdListSaleEndDtm=29991231235959&attrPrdListModelNo=&attrPrdListAttrVal1=빨강&attrPrdListAttrVal2=1&attrPrdListAttrVal3=None&attrPrdListAttrVal4=None&attrPrdListArsAttrVal1=&attrPrdListArsAttrVal2=&attrPrdListArsAttrVal3=&attrPrdListArsAttrVal4=&attrPrdListAttrPkgCnt=&attrPrdListAttrCmposCntnt=&attrPrdListOrgpNm=국내&attrPrdListMnfcCoNm=(주)바보사랑&attrPrdListSafeStockQty=10&attrPrdListTempoutYn=N&attrPrdListTempoutDtm=&attrPrdListChanlGrpCd=AZ&attrPrdListOrdPsblQty=500"
			*/

			$this->item = array();
			$this->makeItemCommon($regGbn, $modGbn, $pinfo);
			$this->makeItemOption($prdTypCd, $saleStrDtm, $saleEndDtm, $attrPrdListOrdPsblQty, $options, $pinfo, $regGbn);
		}elseif($modGbn=='N'){//노출상품명수정
			//http://test1.gsshop.com/alia/aliaCommonPrd.gs?regGbn=U&modGbn=N&regId=BAN&supPrdCd=20160119_02&supCd=1020909&prdNmChgValidStrDtm=20160222195000&prdNmChgValidEndDtm=29991231235959&prdNmChgExposPrdNm=테스트상품2&prdNmChgExposPmoNm=&prdNmChgExposprSntncNm=

			$this->item = array();
			$this->makeItemCommon($regGbn, $modGbn, $pinfo);
			$this->makeItemPname($saleStrDtm, $saleEndDtm, $prdNmChgExposPrdNm, $pinfo);
		}
	}

	private function makeItemCommon($regGbn, $modGbn, $pinfo) {

		$this->item[]['regGbn'] = $regGbn; //* 등록구분 I : 신규, U : 수정
		$this->item[]['modGbn'] = $modGbn; //* 수정구분 P : 가격, S : 판매상태, I : 이미지, D : 기술서, N : 노출상품명, SA : 속성추가 및 주문가능수량수정, SS : 속성판매종료, B : 도서정보(도서몰전용)
		$this->item[]['regId'] = $this->regId; //* 등록자
		$this->item[]['supPrdCd'] = $pinfo['id'];//* 협력사상품코드
		$this->item[]['supCd'] = $this->supCd;//* 협력사코드
	}

	private function makeItemPname($saleStrDtm, $saleEndDtm, $prdNmChgExposPrdNm, $pinfo) {
		$this->item[]['prdNmChgValidStrDtm'] = $saleStrDtm;//* 유효시작일시
		$this->item[]['prdNmChgValidEndDtm'] = $saleEndDtm;//* 유효종료일시
		$this->item[]['prdNmChgExposPrdNm'] = $prdNmChgExposPrdNm;//* 노출상품명
		$this->item[]['prdNmChgExposPmoNm'] = '';//노출프로모션명
		$this->item[]['prdNmChgExposprSntncNm'] = $pinfo['shotinfo'];//노출홍보문구명
	}


	private function makeItemPrice($saleStrDtm, $saleEndDtm, $prdPrcSupGivRtamt, $pinfo) {

		$this->item[]['prdPrcValidStrDtm'] = $saleStrDtm;//* 유효시작일시
		$this->item[]['prdPrcValidEndDtm'] = '29991231235959';//$saleEndDtm;//* 유효종료일시
		$this->item[]['prdPrcSalePrc'] = $pinfo['sellprice'];//* 판매가격
		$this->item[]['prdPrcPrchPrc'] = '';//매입가격
		$this->item[]['prdPrcSupGivRtamtCd'] = '01';//* 협력사지급율/액코드
		$this->item[]['prdPrcSupGivRtamt'] = $prdPrcSupGivRtamt;//* 협력사지급율/액
	}

	private function makeItemImg($image_server_domain, $img_array) {
		$this->item[]['prdCntntListCnt'] = count($img_array['add']) + 1;//* 이미지리스트건수
		$this->item[]['prdCntntListCntntUrlNm'] =  $image_server_domain . $img_array['fiximage']; //* 이미지url
		if( count($img_array['add']) > 0 ){
			foreach( $img_array['add'] as $addImg){
				$this->item[]['prdCntntListCntntUrlNm'] =  $image_server_domain . $addImg;
			}
		}
	}

	private function makeItemBasicinfo($basicinfo) {
		$this->item[]['prdDescdHtmlDescdExplnCntnt'] = $basicinfo;//* 기술서설명내용
	}

	private function makeItemDisp($saleStrDtm, $saleEndDtm) {
		$this->item[]['saleStrDtm'] = $saleStrDtm;//* 판매시작일시
		$this->item[]['saleEndDtm'] = $saleEndDtm;//* 판매종료일시
		$this->item[]['attrSaleEndStModYn'] = 'Y';//속성판매종료상태수정설정
	}

	private function makeItemOption($prdTypCd, $saleStrDtm, $saleEndDtm, $attrPrdListOrdPsblQty, $options, $pinfo, $regGbn) {
		$this->item[]['prdTypCd'] = $prdTypCd;//* 상품유형코드
		if($prdTypCd == 'S'){

			$sql = "SELECT sellertool_value, shop_value FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid|id' and shop_value like '".$pinfo['id']."%' and sellertool_key = 'attrPrdListAttrPrdCd'";
			$this->db->query ( $sql );
			$sellertool_values = $this->db->fetchall("object");
			if(count($sellertool_values) > 0){
				$attrPrdListSupAttrPrdCds = array();
				foreach($sellertool_values as $v){
					$optionsId = explode("|",$v['shop_value']);
					$attrPrdListSupAttrPrdCds[ $optionsId[1] ] = $v['sellertool_value'];
				}
			}

			foreach($options['options'] as $option){
				$this->item[]['attrPrdListSupAttrPrdCd'] = $option['id'];//* 협력사속성상품코드
				
				if($regGbn=='U'){
					$attrPrdListAttrPrdCd = $attrPrdListSupAttrPrdCds[ $option['id'] ];
					unset( $attrPrdListSupAttrPrdCds[ $option['id'] ] );
				}else{
					$attrPrdListAttrPrdCd = '';
				}

				$this->item[]['attrPrdListAttrPrdCd'] = $attrPrdListAttrPrdCd;//GS속성상품코드
				$this->item[]['attrPrdListAttrValCd1'] = '00000';//속성값코드1
				$this->item[]['attrPrdListAttrValCd2'] = '00000';
				$this->item[]['attrPrdListAttrValCd3'] = '00000';
				$this->item[]['attrPrdListAttrValCd4'] = '00000';
				$this->item[]['attrPrdListSaleStrDtm'] = $saleStrDtm;//* 판매시작일시
				$this->item[]['attrPrdListSaleEndDtm'] = $saleEndDtm;//* 판매종료일시
				$this->item[]['attrPrdListModelNo'] = '';//모델번호
				$this->item[]['attrPrdListAttrVal1'] = $option['option_div'];//속성값1
				$this->item[]['attrPrdListAttrVal2'] = '공통';
				$this->item[]['attrPrdListAttrVal3'] = '공통';
				$this->item[]['attrPrdListAttrVal4'] = '공통';
				$this->item[]['attrPrdListArsAttrVal1'] = '';//* 자동주문속성값1
				$this->item[]['attrPrdListArsAttrVal2'] = '';
				$this->item[]['attrPrdListArsAttrVal3'] = '';
				$this->item[]['attrPrdListArsAttrVal4'] = '';
				$this->item[]['attrPrdListAttrPkgCnt'] = '';//* 속성포장개수
				$this->item[]['attrPrdListAttrCmposCntnt'] = '';//* 속성구성정보
				$this->item[]['attrPrdListOrgpNm'] = $pinfo['origin'];//* 원산지명
				$this->item[]['attrPrdListMnfcCoNm'] = $pinfo['company'];//* 제조사명
				$this->item[]['attrPrdListSafeStockQty'] = '5';//* 안전재고수량
				$this->item[]['attrPrdListTempoutYn'] = ( $option['option_stock'] > 0 ? 'N' : 'Y' );//* 일시품절여부
				$this->item[]['attrPrdListTempoutDtm'] = '';//* 일시품절일시
				$this->item[]['attrPrdListChanlGrpCd'] = 'AZ';//* 채널그룹코드
				$this->item[]['attrPrdListOrdPsblQty'] = $option['option_stock'];//* 주문가능수량
			}

			if( count($attrPrdListSupAttrPrdCds) > 0 ){
				foreach($attrPrdListSupAttrPrdCds as $option_id => $attrPrdListAttrPrdCd){
					$this->item[]['attrPrdListSupAttrPrdCd'] = $option_id;//* 협력사속성상품코드
					$this->item[]['attrPrdListAttrPrdCd'] = $attrPrdListAttrPrdCd;//GS속성상품코드
					$this->item[]['attrPrdListAttrValCd1'] = '00000';//속성값코드1
					$this->item[]['attrPrdListAttrValCd2'] = '00000';
					$this->item[]['attrPrdListAttrValCd3'] = '00000';
					$this->item[]['attrPrdListAttrValCd4'] = '00000';
					$this->item[]['attrPrdListSaleStrDtm'] = $saleStrDtm;//* 판매시작일시
					$this->item[]['attrPrdListSaleEndDtm'] = $saleEndDtm;//* 판매종료일시
					$this->item[]['attrPrdListModelNo'] = '';//모델번호
					$this->item[]['attrPrdListAttrVal1'] = '판매종료 옵션 '.$option_id;//속성값1
					$this->item[]['attrPrdListAttrVal2'] = '공통';
					$this->item[]['attrPrdListAttrVal3'] = '공통';
					$this->item[]['attrPrdListAttrVal4'] = '공통';
					$this->item[]['attrPrdListArsAttrVal1'] = '';//* 자동주문속성값1
					$this->item[]['attrPrdListArsAttrVal2'] = '';
					$this->item[]['attrPrdListArsAttrVal3'] = '';
					$this->item[]['attrPrdListArsAttrVal4'] = '';
					$this->item[]['attrPrdListAttrPkgCnt'] = '';//* 속성포장개수
					$this->item[]['attrPrdListAttrCmposCntnt'] = '';//* 속성구성정보
					$this->item[]['attrPrdListOrgpNm'] = $pinfo['origin'];//* 원산지명
					$this->item[]['attrPrdListMnfcCoNm'] = $pinfo['company'];//* 제조사명
					$this->item[]['attrPrdListSafeStockQty'] = '0';//* 안전재고수량
					$this->item[]['attrPrdListTempoutYn'] = 'Y';//* 일시품절여부
					$this->item[]['attrPrdListTempoutDtm'] = '';//* 일시품절일시
					$this->item[]['attrPrdListChanlGrpCd'] = 'AZ';//* 채널그룹코드
					$this->item[]['attrPrdListOrdPsblQty'] = 0;//* 주문가능수량
				}
			}

			$this->item[]['attrPrdListCnt'] = count($options['options']) + count($attrPrdListSupAttrPrdCds);
			
		}else{
			$this->item[]['attrPrdListCnt'] = 1;
			$this->item[]['attrPrdListSupAttrPrdCd'] = $pinfo['id'];//* 협력사속성상품코드

			if($regGbn=='U'){

				$sql = "SELECT sellertool_value FROM sellertool_reponse WHERE site_code = '".$this->site_code."' and shop_key='pid|id' and shop_value = '".$pinfo['id']."|".$pinfo['id']."' and sellertool_key = 'attrPrdListAttrPrdCd' ";

				$this->db->query ( $sql );
				$this->db->fetch ();
				$attrPrdListAttrPrdCd = $this->db->dt['sellertool_value'];

			}else{
				$attrPrdListAttrPrdCd = '';
			}

			$this->item[]['attrPrdListAttrPrdCd'] = $attrPrdListAttrPrdCd;//GS속성상품코드
			$this->item[]['attrPrdListAttrValCd1'] = '00000';//속성값코드1
			$this->item[]['attrPrdListAttrValCd2'] = '00000';
			$this->item[]['attrPrdListAttrValCd3'] = '00000';
			$this->item[]['attrPrdListAttrValCd4'] = '00000';
			$this->item[]['attrPrdListSaleStrDtm'] = $saleStrDtm;//* 판매시작일시
			$this->item[]['attrPrdListSaleEndDtm'] = $saleEndDtm;//* 판매종료일시
			$this->item[]['attrPrdListModelNo'] = '';//모델번호
			$this->item[]['attrPrdListAttrVal1'] = '공통';//속성값1
			$this->item[]['attrPrdListAttrVal2'] = '공통';
			$this->item[]['attrPrdListAttrVal3'] = '공통';
			$this->item[]['attrPrdListAttrVal4'] = '공통';
			$this->item[]['attrPrdListArsAttrVal1'] = '';//* 자동주문속성값1
			$this->item[]['attrPrdListArsAttrVal2'] = '';
			$this->item[]['attrPrdListArsAttrVal3'] = '';
			$this->item[]['attrPrdListArsAttrVal4'] = '';
			$this->item[]['attrPrdListAttrPkgCnt'] = '';//* 속성포장개수
			$this->item[]['attrPrdListAttrCmposCntnt'] = '';//* 속성구성정보
			$this->item[]['attrPrdListOrgpNm'] = '';//* 원산지명
			$this->item[]['attrPrdListMnfcCoNm'] = '';//* 제조사명
			$this->item[]['attrPrdListSafeStockQty'] = '5';//* 안전재고수량
			$this->item[]['attrPrdListTempoutYn'] = ( $attrPrdListOrdPsblQty > 0 ? 'N' : 'Y' );//* 일시품절여부
			$this->item[]['attrPrdListTempoutDtm'] = '';//* 일시품절일시
			$this->item[]['attrPrdListChanlGrpCd'] = 'AZ';//* 채널그룹코드
			$this->item[]['attrPrdListOrdPsblQty'] = $attrPrdListOrdPsblQty;//* 주문가능수량
		}
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
					$sql = "SELECT id, option_div, option_stock , option_price, od.option_soldout FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' order by id";
					$this->db->query ( $sql );
					$stock_tmp_options[] = $this->db->fetchall('object');
				}elseif($_options_[$i]['option_kind']=="i1" || $_options_[$i]['option_kind']=="i2"){
					$sql = "SELECT id, option_div, option_stock, option_price, od.option_soldout FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' order by id";
					$this->db->query ( $sql );
					$i_tmp_options[] = $this->db->fetchall('object');
				}elseif($_options_[$i]['option_kind']=="c2"){
					$sql = "
					select 1 as id, '' as option_div , '0' as option_stock, '0' as option_price, '0' as option_soldout
					UNION ALL
					SELECT id, option_div, option_stock, option_price, od.option_soldout FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' order by id";
					$this->db->query ( $sql );
					$tmp_options[] = $this->db->fetchall('object');
				}else{
					$sql = "SELECT id, '".$_options_[$i]['option_kind']."' as option_kind, option_div, option_stock, option_price, od.option_soldout FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' order by id";
					$this->db->query ( $sql );
					$tmp_options[] = $this->db->fetchall('object');
				}

			}

			if(count($tmp_options)>2){
				for($i=0;$i<count($tmp_options[0]);$i++){
					for($j=0;$j<count($tmp_options[1]);$j++){
						for($k=0;$k<count($tmp_options[2]);$k++){
							$options[$cnt]['id'] = $tmp_options[0][$i]['id'] . '-' . $tmp_options[1][$j]['id'] . '-' . $tmp_options[2][$k]['id'];
							$options[$cnt]['option_stock'] = "100";
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
						$options[$cnt]['option_stock'] = "100";
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
					$options[$cnt]['option_stock'] = "100";
					$options[$cnt]['option_price'] = $pinfo ['sellprice'] + $tmp_options[0][$i]['option_price'] + $gep_price;
					$options[$cnt]['option_div'] = $tmp_options[0][$i]['option_div'];
					$cnt++;
				}
			}

			if(count($i_tmp_options)>0){
				for($i=0;$i<count($i_tmp_options[0]);$i++){
					$options[$cnt]['id'] = $tmp_options[0][$i]['id'];
					$options[$cnt]['option_stock'] = "100";
					$options[$cnt]['option_price'] = $pinfo ['sellprice'] + $i_tmp_options[0][$i]['option_price'] + $gep_price;
					$options[$cnt]['option_div'] = $i_tmp_options[0][$i]['option_div'];
					$cnt++;
				}
			}

			if(count($stock_tmp_options)>0){
				$optoin_stock_use_yn=true;
				for($i=0;$i<count($stock_tmp_options[0]);$i++){
					$options[$cnt]['id'] = $stock_tmp_options[0][$i]['id'];
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
		
		$cnt=0;
		$return = array();
		$return['option_check'] = 'N';
		
		for($i=0;$i<count($_options_);$i++){
			$return['option_name'][$cnt] = $_options_[$i]['option_name'];
			$cnt++;
		}
		
		if(count($options)>0){
			$cnt=0;
			$return['option_check'] = 'Y';

			for($i=0;$i<count($options);$i++){
				if($pinfo ['sellprice'] == $options[$i]['option_price']){
					$return['options'][$cnt] = $options[$i];
					$cnt++;
				}
			}
		}

		if( $return['option_check'] == 'Y' ){
			if( ! (count($return['options']) > 0) ){
				$return['option_check'] = 'NOT';
			}
		}

		return $return;
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
				$return[ $OfficialNotices[$dt_code] ] = $data['pmi_desc'];

				if( empty($return[ $OfficialNotices[$dt_code] ] ) ){
					$return[ $OfficialNotices[$dt_code] ] = "상세상세정보 참고";
				}
			}else{
				$return[ $OfficialNotices[""] ] = "상세상세 참고";
			}
		}
		
		unset($return[""]);

		return $return;
	}

	//계약시 변경되는 사항들!!
	private function getOperMdId($code) {

		//$infos["GS브랜드코드"]="MDID";
		$infos["20329"]="80053"; //아디다스(ADIDAS) 
		$infos["20321"]="80053"; //뉴발란스(NEW BALANCE) 
		$infos["23883"]="80053"; //리복(REEBOK) 
		$infos["20335"]="80053"; //스케쳐스(SKECHERS) 
		$infos["8430"]="80053"; //프로스펙스(PROSPECS) 
		$infos["131944"]="80053"; //엠리밋(M LIMITED) 
		$infos["23878"]="80053"; //반스(VANS) 
		$infos["7783"]="80053"; //아레나(ARENA) 
		$infos["81504"]="83039"; //노스페이스 화이트라벨 
		$infos["7730"]="83039"; //밀레(MILLET) 
		$infos["113237"]="83039"; //코오롱스포츠(KOLON SPORT) 
		$infos["26572"]="83039"; //웨스트우드(WESTWOOD) 
		$infos["68674"]="83039"; //와일드로즈(WILDROSES) 
		$infos["3120"]="83039"; //케이투(K2) 
		$infos["65208"]="83039"; //콜핑 
		$infos["119994"]="83039"; //오프로드 
		$infos["139133"]="80107"; //리바이스 바디웨어(LEVIS BODYWEAR) 
		$infos["64735"]="80107"; //게스 언더웨어(GUESS UNDERWEAR) 
		$infos["72744"]="80107"; //에블린(EBLIN) 
		$infos["63586"]="80107"; //코데즈컴바인 이너웨어(CODES COMBINE innerwear) 
		$infos["80270"]="80107"; //캘빈클라인 언더웨어(CALVIN KLEIN underwear) 
		$infos["61108"]="83016"; //잇미샤(IT MICHAA) 
		$infos["25591"]="82108"; //캘빈클라인진(CALVIN KLEIN JEANS) 
		$infos["23825"]="82108"; //버커루진(BUCKAROO JEANS) 
		$infos["72890"]="82108"; //힐피거데님(Hilfiger Denim) 
		$infos["19118"]="82108"; //앤듀(ANDEW) 
		$infos["7500"]="82108"; //어스앤뎀(US N THEM) 
		$infos["20394"]="82108"; //카이아크만(KAI-AAKMANM) 
		$infos["22217"]="82108"; //테이트(TATE) 
		$infos["6976"]="82108"; //흄(HUM) 
		$infos["61580"]="82108"; //펠틱스(FELTICS) 
		$infos["7031"]="82108"; //지오다노(GIORDANO) 
		$infos["7036"]="82108"; //클라이드(CLIDE) 
		$infos["7038"]="82108"; //폴햄(POLHAM) 
		$infos["7037"]="82108"; //티비제이(TBJ) 
		$infos["111333"]="82108"; //후아유(WHO.A.U) 
		$infos["62932"]="82108"; //FRJ (FRJ JEANS) 
		$infos["22887"]="82108"; //NBA(NBA) 
		$infos["59084"]="82108"; //팬콧(PANCOAT) 
		$infos["26650"]="82108"; //엠엘비(MLB) 
		$infos["19342"]="82108"; //크리스크리스티(CHRIS-CHRISTY) 
		$infos["153093"]="82108"; //애드호크(AD HOC) 
		$infos["72818"]="82108"; //햇츠온(HATS ON) 
		$infos["147318"]="83016"; //킬리안(KTLIAN) 
		$infos["71950"]="83016"; //게스슈즈(Guess Shoes) 
		$infos["131263"]="83016"; //디자인스킨(DESIGN SKIN) 
		$infos["21338"]="83016"; //팀버랜드
		$infos["20334"]="80053"; //컨버스
		$infos["23211"]="83039"; //네파
		$infos["129570"]="83039"; //블랙야크키즈
		$infos["5460"]="83016"; //빈치스벤치
		$infos["119742"]="83087"; //본지플로어
		$infos["25120"]="83087"; //지오지아
		$infos["116680"]="83087"; //더셔츠스튜디오
		$infos["76772"]="83016"; //더블스타
		$infos["129225"]="83016"; //캘빈클라인 ACC
		$infos["129589"]="89107"; //유아동 뉴발란스키즈
		$infos["157760"]="83108"; //쥬딩앤폴
		$infos["160125"]="82231"; //디마또
		$infos["161515"]="82231"; //스틸레디마또

		return $infos[$code];
	}

	private function getOfficialNoticeCode($code) {

		$info["1"]["1"]="1001";//의류제품 소재
		$info["1"]["2"]="1002";//의류색상
		$info["1"]["3"]="1003";//의류치수
		$info["1"]["4"]="1004";//의류제조자,수입품의 경우 수입자를 함께 표기
		$info["1"]["5"]="1005";//의류제조국
		$info["1"]["6"]="1006";//의류세탁방법 및 취급시 주의사항
		$info["1"]["7"]="1007";//의류제조년월
		$info["1"]["8"]="1008";//의류품질보증기준
		$info["1"]["9"]="1009";//의류A/S 책임자와 전화번호
		$info["2"]["1"]="1101";//구두 / 신발제품 소재
		$info["2"]["2"]="1102";//구두 / 신발색상
		$info["2"]["3"]="1103";//구두 / 신발치수
		$info["2"][""]="1104";//구두 / 신발
		$info["2"]["4"]="1105";//구두 / 신발제조자, 수입품의 경우 수입자를 함께 표기
		$info["2"]["5"]="1106";//구두 / 신발제조국
		$info["2"]["7"]="1107";//구두 / 신발품질보증기준
		$info["2"]["8"]="1108";//구두 / 신발A/S 책임자와 전화번호
		$info["2"]["6"]="1109";//구두 / 신발취급시 주의사항
		$info["3"]["1"]="1201";//가방종류
		$info["3"]["2"]="1202";//가방소재
		$info["3"]["3"]="1203";//가방색상
		$info["3"]["4"]="1204";//가방크기
		$info["3"]["5"]="1205";//가방제조자,수입품의 경우 수입자를 함께 표기 
		$info["3"]["6"]="1206";//가방제조국
		$info["3"]["7"]="1207";//가방취급시 주의사항
		$info["3"]["8"]="1208";//가방품질보증기준
		$info["3"]["9"]="1209";//가방A/S 책임자와 전화번호
		$info["4"]["1"]="1301";//패션잡화 (모자 / 벨트 / 액세서리)종류
		$info["4"]["2"]="1302";//패션잡화 (모자 / 벨트 / 액세서리)소재
		$info["4"]["3"]="1303";//패션잡화 (모자 / 벨트 / 액세서리)치수
		$info["4"]["4"]="1304";//패션잡화 (모자 / 벨트 / 액세서리)제조자,수입품의 경우 수입자를 함께 표기
		$info["4"]["5"]="1305";//패션잡화 (모자 / 벨트 / 액세서리)제조국
		$info["4"]["6"]="1306";//패션잡화 (모자 / 벨트 / 액세서리)취급시 주의사항
		$info["4"]["7"]="1307";//패션잡화 (모자 / 벨트 / 액세서리)품질보증기준
		$info["4"]["8"]="1308";//패션잡화 (모자 / 벨트 / 액세서리)A/S 책임자와 전화번호
		$info["5"]["1"]="1401";//침구류 / 커튼제품 소재
		$info["5"]["2"]="1402";//침구류 / 커튼색상
		$info["5"]["3"]="1403";//침구류 / 커튼치수
		$info["5"]["4"]="1404";//침구류 / 커튼제품구성
		$info["5"]["5"]="1405";//침구류 / 커튼제조자,수입품의 경우 수입자를 함께 표기
		$info["5"]["6"]="1406";//침구류 / 커튼제조국
		$info["5"]["7"]="1407";//침구류 / 커튼세탁방법 및 취급시 주의사항
		$info["5"]["8"]="1408";//침구류 / 커튼품질보증 기준
		$info["5"]["9"]="1409";//침구류 / 커튼A/S 책임자와 전화번호
		$info["5"][""]="1410";//침구류 / 커튼
		$info["6"]["1"]="1501";//가구(침대 / 소파 / 싱크대 / DIY제품)품명
		$info["6"]["2"]="1502";//가구(침대 / 소파 / 싱크대 / DIY제품)KC 인증 필 유무
		$info["6"]["3"]="1503";//가구(침대 / 소파 / 싱크대 / DIY제품)색상
		$info["6"]["4"]="1504";//가구(침대 / 소파 / 싱크대 / DIY제품)구성품
		$info["6"]["5"]="1505";//가구(침대 / 소파 / 싱크대 / DIY제품)주요 소재
		$info["6"]["6"]="1506";//가구(침대 / 소파 / 싱크대 / DIY제품)제조자,수입품의 경우 수입자를 함께 표기 
		$info["6"]["7"]="1507";//가구(침대 / 소파 / 싱크대 / DIY제품)제조국
		$info["6"]["8"]="1508";//가구(침대 / 소파 / 싱크대 / DIY제품)크기
		$info["6"]["9"]="1509";//가구(침대 / 소파 / 싱크대 / DIY제품)배송·설치비용
		$info["6"]["10"]="1510";//가구(침대 / 소파 / 싱크대 / DIY제품)품질보증기준
		$info["6"]["11"]="1511";//가구(침대 / 소파 / 싱크대 / DIY제품)A/S 책임자와 전화번호
		$info["7"]["1"]="1601";//영상가전(TV류)품명 및 모델명
		$info["7"]["2"]="1602";//영상가전(TV류)전기용품 안전인증 필 유무
		$info["7"]["3"]="1603";//영상가전(TV류)정격전압, 소비전력, 에너지소비효율등급
		$info["7"]["4"]="1604";//영상가전(TV류)동일모델의 출시년월
		$info["7"]["5"]="1605";//영상가전(TV류)제조자,수입품의 경우 수입자를 함께 표기
		$info["7"]["6"]="1606";//영상가전(TV류)제조국
		$info["7"]["8"]="1607";//영상가전(TV류)화면사양
		$info["7"]["9"]="1608";//영상가전(TV류)품질보증기준
		$info["7"]["10"]="1609";//영상가전(TV류)A/S 책임자와 전화번호
		$info["7"]["7"]="1610";//영상가전(TV류)크기
		$info["8"]["1"]="1701";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)품명 및 모델명
		$info["8"]["2"]="1702";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)전기용품 안전인증 필 유무
		$info["8"]["3"]="1703";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)정격전압, 소비전력, 에너지소비효율등급
		$info["8"]["4"]="1704";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)동일모델의 출시년월
		$info["8"]["5"]="1705";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)제조자,수입품의 경우 수입자를 함께 표기
		$info["8"]["6"]="1706";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)제조국
		$info["8"]["7"]="1707";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)크기
		$info["8"]["8"]="1708";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)품질보증기준
		$info["8"]["9"]="1709";//가정용 전기제품(냉장고 / 세탁기 / 식기세척기 / 전자레인지)A/S 책임자와 전화번호
		$info["9"]["1"]="1801";//계절가전(에어컨 / 온풍기)품명 및 모델명
		$info["9"]["2"]="1802";//계절가전(에어컨 / 온풍기)전기용품 안전인증 필 유무
		$info["9"]["3"]="1803";//계절가전(에어컨 / 온풍기)정격전압, 소비전력, 에너지소비효율등급
		$info["9"]["4"]="1804";//계절가전(에어컨 / 온풍기)동일모델의 출시년월
		$info["9"]["5"]="1805";//계절가전(에어컨 / 온풍기)제조자,수입품의 경우 수입자를 함께 표기
		$info["9"]["6"]="1806";//계절가전(에어컨 / 온풍기)제조국
		$info["9"]["7"]="1807";//계절가전(에어컨 / 온풍기)크기
		$info["9"]["8"]="1808";//계절가전(에어컨 / 온풍기)냉난방면적
		$info["9"]["9"]="1809";//계절가전(에어컨 / 온풍기)추가설치비용
		$info["9"]["10"]="1810";//계절가전(에어컨 / 온풍기)품질보증기준
		$info["9"]["11"]="1811";//계절가전(에어컨 / 온풍기)A/S 책임자와 전화번호
		$info["10"]["1"]="1901";//사무용기기(컴퓨터 / 노트북 / 프린터)품명 및 모델명
		$info["10"]["2"]="1902";//사무용기기(컴퓨터 / 노트북 / 프린터)KCC 인증 필 유무
		$info["10"]["3"]="1903";//사무용기기(컴퓨터 / 노트북 / 프린터)정격전압, 소비전력, 에너지소비효율등급
		$info["10"]["4"]="1904";//사무용기기(컴퓨터 / 노트북 / 프린터)동일모델의 출시년월
		$info["10"]["5"]="1905";//사무용기기(컴퓨터 / 노트북 / 프린터)제조자
		$info["10"]["6"]="1906";//사무용기기(컴퓨터 / 노트북 / 프린터)제조국
		$info["10"]["7"]="1907";//사무용기기(컴퓨터 / 노트북 / 프린터)크기,무게
		$info["10"]["8"]="1908";//사무용기기(컴퓨터 / 노트북 / 프린터)주요 사양
		$info["10"]["9"]="1909";//사무용기기(컴퓨터 / 노트북 / 프린터)품질보증기준
		$info["10"]["10"]="1910";//사무용기기(컴퓨터 / 노트북 / 프린터)A/S 책임자와 전화번호
		$info["11"]["1"]="2001";//광학기기(디지털카메라 / 캠코더)품명 및 모델명
		$info["11"]["2"]="2002";//광학기기(디지털카메라 / 캠코더)KCC 인증 필 유무
		$info["11"]["3"]="2003";//광학기기(디지털카메라 / 캠코더)동일모델의 출시년월
		$info["11"]["4"]="2004";//광학기기(디지털카메라 / 캠코더)제조자
		$info["11"]["5"]="2005";//광학기기(디지털카메라 / 캠코더)제조국
		$info["11"]["6"]="2006";//광학기기(디지털카메라 / 캠코더)크기, 무게
		$info["11"]["7"]="2007";//광학기기(디지털카메라 / 캠코더)주요 사양
		$info["11"]["8"]="2008";//광학기기(디지털카메라 / 캠코더)품질보증기준
		$info["11"]["9"]="2009";//광학기기(디지털카메라 / 캠코더)A/S 책임자와 전화번호
		$info["12"]["1"]="2101";//소형전자(MP3 / 전자사전 등)품명 및 모델명
		$info["12"]["2"]="2102";//소형전자(MP3 / 전자사전 등)KC 인증 필 유무
		$info["12"]["3"]="2103";//소형전자(MP3 / 전자사전 등)정격전압, 소비전력
		$info["12"]["4"]="2104";//소형전자(MP3 / 전자사전 등)동일모델의 출시년월
		$info["12"]["5"]="2105";//소형전자(MP3 / 전자사전 등)제조자
		$info["12"]["6"]="2106";//소형전자(MP3 / 전자사전 등)제조국
		$info["12"]["7"]="2107";//소형전자(MP3 / 전자사전 등)크기, 무게
		$info["12"]["8"]="2108";//소형전자(MP3 / 전자사전 등)주요 사양
		$info["12"]["9"]="2109";//소형전자(MP3 / 전자사전 등)품질보증기준
		$info["12"]["10"]="2110";//소형전자(MP3 / 전자사전 등)A/S 책임자와 전화번호
		$info["13"]["1"]="2201";//휴대폰품명 및 모델명
		$info["13"]["2"]="2202";//휴대폰KCC 인증 필 유무
		$info["13"]["3"]="2203";//휴대폰동일모델의 출시년월
		$info["13"]["4"]="2204";//휴대폰제조자
		$info["13"]["5"]="2205";//휴대폰제조국
		$info["13"]["6"]="2206";//휴대폰크기, 무게
		$info["13"][""]="2207";//휴대폰
		$info["13"]["7"]="2208";//휴대폰이동통신사
		$info["13"]["8"]="2209";//휴대폰가입절차
		$info["13"]["9"]="2210";//휴대폰소비자의 추가적인 부담사항
		$info["13"]["10"]="2211";//휴대폰주요사양
		$info["13"]["11"]="2212";//휴대폰품질보증기준
		$info["13"]["12"]="2213";//휴대폰A/S 책임자와 전화번호
		$info["14"]["1"]="2301";//내비게이션품명 및 모델명
		$info["14"]["2"]="2302";//내비게이션KCC 인증 필 유무
		$info["14"]["3"]="2303";//내비게이션정격전압, 소비전력
		$info["14"]["4"]="2304";//내비게이션동일모델의 출시년월
		$info["14"]["5"]="2305";//내비게이션제조자
		$info["14"]["6"]="2306";//내비게이션제조국
		$info["14"]["7"]="2307";//내비게이션크기, 무게
		$info["14"]["8"]="2308";//내비게이션주요 사양
		$info["14"]["9"]="2309";//내비게이션맵 업데이트 비용 및 무상기간
		$info["14"]["10"]="2310";//내비게이션품질보증기준
		$info["14"]["11"]="2311";//내비게이션A/S 책임자와 전화번호
		$info["15"]["1"]="2401";//자동차용품(자동자부품/기타 자동차용품)품명 및 모델명
		$info["15"]["2"]="2402";//자동차용품(자동자부품/기타 자동차용품)동일모델의 출시년월
		$info["15"]["3"]="2403";//자동차용품(자동자부품/기타 자동차용품)자동차관리법에 따른 자동차부품 자기인증 유무
		$info["15"]["4"]="2404";//자동차용품(자동자부품/기타 자동차용품)제조자, 수입품의 경우 수입자를 함께 표기
		$info["15"]["5"]="2405";//자동차용품(자동자부품/기타 자동차용품)제조국
		$info["15"]["6"]="2406";//자동차용품(자동자부품/기타 자동차용품)크기
		$info["15"]["7"]="2407";//자동차용품(자동자부품/기타 자동차용품)적용차종
		$info["15"]["8"]="2408";//자동차용품(자동자부품/기타 자동차용품)품질보증기준
		$info["15"]["9"]="2409";//자동차용품(자동자부품/기타 자동차용품)A/S 책임자와 전화번호
		$info["15"][""]="2410";//자동차용품(자동자부품/기타 자동차용품)
		$info["16"]["1"]="2501";//의료기기품명 및 모델명
		$info["16"]["2"]="2502";//의료기기의료기기법상 허가·신고 번호 및 광고사전심의필 유무
		$info["16"]["3"]="2503";//의료기기전기용품안전관리법상 KC 인증 필 유무
		$info["16"]["4"]="2504";//의료기기정격전압, 소비전력
		$info["16"]["5"]="2505";//의료기기동일모델의 출시년월
		$info["16"]["6"]="2506";//의료기기제조자, 수입품의 경우 수입자를 함께 표기
		$info["16"]["7"]="2507";//의료기기제조국
		$info["16"]["8"]="2508";//의료기기제품의 사용 목적 및 사용방법
		$info["16"]["9"]="2509";//의료기기취급시 주의사항
		$info["16"]["10"]="2510";//의료기기품질보증기준
		$info["16"]["11"]="2511";//의료기기A/S 책임자와 전화번호
		$info["17"]["1"]="2601";//주방용품품명 및 모델명
		$info["17"]["2"]="2602";//주방용품재질
		$info["17"]["3"]="2603";//주방용품구성품
		$info["17"]["4"]="2604";//주방용품크기
		$info["17"]["5"]="2605";//주방용품동일모델의 출시년월
		$info["17"]["6"]="2606";//주방용품제조자, 수입품의 경우 수입자를 함께 표기
		$info["17"]["7"]="2607";//주방용품제조국
		$info["17"]["8"]="2608";//주방용품식품위생법에 따른 수입 기구·용기의 경우 “식품위생법에 따른 수입신고를 필함”의 문구
		$info["17"]["9"]="2609";//주방용품품질보증기준
		$info["17"]["10"]="2610";//주방용품A/S 책임자와 전화번호
		$info["18"]["1"]="2701";//화장품용량 또는 중량
		$info["18"]["2"]="2702";//화장품제품 주요 사양
		$info["18"]["3"]="2703";//화장품사용기한 또는 개봉 후 사용기간
		$info["18"]["4"]="2704";//화장품사용방법
		$info["18"]["5"]="2705";//화장품제조자 및 제조판매업자
		$info["18"]["6"]="2706";//화장품제조국
		$info["18"]["7"]="2707";//화장품주요성분
		$info["18"]["8"]="2708";//화장품기능성 화장품의 경우 화장품법에 따른 식품의약품안전청 심사 필 유무
		$info["18"]["9"]="2709";//화장품사용할 때 주의사항
		$info["18"]["10"]="2710";//화장품품질보증기준
		$info["18"]["11"]="2711";//화장품소비자상담관련 전화번호
		$info["19"]["1"]="2801";//귀금속 / 보석 / 시계류소재 / 순도 / 밴드재질
		$info["19"]["2"]="2802";//귀금속 / 보석 / 시계류중량
		$info["19"]["3"]="2803";//귀금속 / 보석 / 시계류제조자, 수입품의 경우 수입자를 함께 표기
		$info["19"]["4"]="2804";//귀금속 / 보석 / 시계류제조국
		$info["19"]["5"]="2805";//귀금속 / 보석 / 시계류치수
		$info["19"]["6"]="2806";//귀금속 / 보석 / 시계류착용 시 주의사항
		$info["19"][""]="2807";//귀금속 / 보석 / 시계류
		$info["19"]["8"]="2808";//귀금속 / 보석 / 시계류귀금속, 보석류
		$info["19"]["9"]="2809";//귀금속 / 보석 / 시계류시계
		$info["19"]["9"]="2810";//귀금속 / 보석 / 시계류보증서 제공여부
		$info["19"]["10"]="2811";//귀금속 / 보석 / 시계류품질보증기준
		$info["19"]["11"]="2812";//귀금속 / 보석 / 시계류A/S 책임자와 전화번호
		$info["20"]["1"]="2901";//식품(농수산물)포장단위별 용량(중량), 수량, 크기
		$info["20"]["2"]="2902";//식품(농수산물)생산자, 수입품의 경우 수입자를 함께 표기
		$info["20"]["3"]="2903";//식품(농수산물)농수산물의 원산지 표시에 관한 법률에 따른 원산지
		$info["20"]["4"]="2904";//식품(농수산물)제조연월일(포장일 또는 생산연도), 유통기한 또는 품질유지기한
		$info["20"][""]="2905";//식품(농수산물)
		$info["20"]["5"]="2906";//식품(농수산물)농산물
		$info["20"]["6"]="2907";//식품(농수산물)축산물
		$info["20"]["7"]="2908";//식품(농수산물)수산물
		$info["20"]["8"]="2909";//식품(농수산물)수입식품에 해당하는 경우 “식품위생법에 따른 수입신고를 필함”의 문구
		$info["20"]["9"]="2910";//식품(농수산물)상품구성
		$info["20"]["10"]="2911";//식품(농수산물)보관방법 또는 취급방법
		$info["20"]["11"]="2912";//식품(농수산물)소비자상담 관련 전화번호
		$info["21"][""]="3001";//가공식품
		$info["21"]["1"]="3002";//가공식품식품의 유형
		$info["21"]["2"]="3003";//가공식품생산자 및 소재지, 수입품의 경우 수입자를 함께 표기
		$info["21"]["3"]="3004";//가공식품제조연월일, 유통기한 또는 품질유지기한
		$info["21"]["4"]="3005";//가공식품포장단위별 용량(중량), 수량
		$info["21"]["5"]="3006";//가공식품원재료명 및 함량
		$info["21"]["6"]="3007";//가공식품영양성분
		$info["21"]["7"]="3008";//가공식품유전자재조합식품에 해당하는 경우의 표시
		$info["21"]["8"]="3009";//가공식품영유아식 또는 체중조절식품 등에 해당하는 경우 표시광고 사전심의필
		$info["21"]["9"]="3010";//가공식품수입식품에 해당하는 경우 “식품위생법에 따른 수입신고를 필함”의 문구
		$info["21"]["10"]="3011";//가공식품소비자상담 관련 전화번호
		$info["22"][""]="3101";//건강기능식품
		$info["22"]["1"]="3102";//건강기능식품식품의 유형
		$info["22"]["2"]="3103";//건강기능식품생산자 및 소재지, 수입품의 경우 수입자를 함께 표기
		$info["22"]["3"]="3104";//건강기능식품제조연월일, 유통기한 또는 품질유지기한
		$info["22"]["4"]="3105";//건강기능식품포장단위별 용량(중량), 수량
		$info["22"]["5"]="3106";//건강기능식품원재료명 및 함량
		$info["22"]["6"]="3107";//건강기능식품영양정보
		$info["22"]["7"]="3108";//건강기능식품기능정보
		$info["22"]["8"]="3109";//건강기능식품섭취량, 섭취방법 및 섭취 시 주의사항
		$info["22"]["9"]="3110";//건강기능식품질병의 예방 및 치료를 위한 의약품이 아니라는 내용의 표현
		$info["22"]["10"]="3111";//건강기능식품유전자재조합식품에 해당하는 경우의 표시
		$info["22"]["11"]="3112";//건강기능식품표시광고 사전심의필
		$info["22"]["12"]="3113";//건강기능식품수입식품에 해당하는 경우 “건강기능식품에 관한 법률에 따른 수입신고를 필함”의 문구
		$info["22"]["13"]="3114";//건강기능식품소비자상담 관련 전화번호
		$info["23"]["1"]="3201";//영유아용품품명 및 모델명
		$info["23"]["2"]="3202";//영유아용품KC 인증 필
		$info["23"]["3"]="3203";//영유아용품크기, 중량
		$info["23"]["4"]="3204";//영유아용품색상
		$info["23"]["5"]="3205";//영유아용품재질
		$info["23"]["6"]="3206";//영유아용품사용연령
		$info["23"]["7"]="3207";//영유아용품동일모델의 출시년월
		$info["23"]["8"]="3208";//영유아용품제조자, 수입품의 경우 수입자를 함께 표기
		$info["23"]["9"]="3209";//영유아용품제조국
		$info["23"]["10"]="3210";//영유아용품취급방법 및 취급시 주의사항, 안전표시
		$info["23"]["11"]="3211";//영유아용품품질보증기준
		$info["23"]["12"]="3212";//영유아용품A/S 책임자와 전화번호
		$info["24"]["1"]="3301";//악기품명 및 모델명
		$info["24"]["2"]="3302";//악기크기
		$info["24"]["3"]="3303";//악기색상
		$info["24"]["4"]="3304";//악기재질
		$info["24"]["5"]="3305";//악기제품 구성
		$info["24"]["6"]="3306";//악기동일모델의 출시년월
		$info["24"]["7"]="3307";//악기제조자, 수입품의 경우 수입자를 함께 표기
		$info["24"]["8"]="3308";//악기제조국
		$info["24"]["9"]="3309";//악기상품별 세부 사양
		$info["24"]["10"]="3310";//악기품질보증기준
		$info["24"]["11"]="3311";//악기A/S 책임자와 전화번호
		$info["25"]["1"]="3401";//스포츠용품품명 및 모델명
		$info["25"]["2"]="3402";//스포츠용품크기, 중량
		$info["25"]["3"]="3403";//스포츠용품색상
		$info["25"]["4"]="3404";//스포츠용품재질
		$info["25"]["5"]="3405";//스포츠용품제품 구성
		$info["25"]["6"]="3406";//스포츠용품동일모델의 출시년월
		$info["25"]["7"]="3407";//스포츠용품제조자, 수입품의 경우 수입자를 함께 표기
		$info["25"]["8"]="3408";//스포츠용품제조국
		$info["25"]["9"]="3409";//스포츠용품상품별 세부 사양
		$info["25"]["10"]="3410";//스포츠용품품질보증기준
		$info["25"]["11"]="3411";//스포츠용품A/S 책임자와 전화번호
		$info["26"]["1"]="3501";//서적도서명
		$info["26"]["2"]="3502";//서적저자, 출판사
		$info["26"]["3"]="3503";//서적크기
		$info["26"]["4"]="3504";//서적쪽수
		$info["26"]["5"]="3505";//서적제품 구성
		$info["26"]["6"]="3506";//서적출간일
		$info["26"]["7"]="3507";//서적목차 또는 책소개
		$info["27"]["1"]="3601";//호텔 / 펜션 예약국가 또는 지역명
		$info["27"]["2"]="3602";//호텔 / 펜션 예약숙소형태
		$info["27"]["3"]="3603";//호텔 / 펜션 예약등급, 객실타입
		$info["27"]["4"]="3604";//호텔 / 펜션 예약사용가능 인원, 인원 추가 시 비용
		$info["27"]["5"]="3605";//호텔 / 펜션 예약부대시설, 제공 서비스
		$info["27"]["6"]="3606";//호텔 / 펜션 예약취소 규정
		$info["27"]["7"]="3607";//호텔 / 펜션 예약예약담당 연락처
		$info["28"]["1"]="3701";//여행패키지여행사
		$info["28"]["2"]="3702";//여행패키지이용항공편
		$info["28"]["3"]="3703";//여행패키지여행기간 및 일정
		$info["28"]["4"]="3704";//여행패키지총 예정 인원, 출발 가능 인원
		$info["28"]["5"]="3705";//여행패키지숙박정보
		$info["28"]["6"]="3706";//여행패키지포함 내역
		$info["28"]["7"]="3707";//여행패키지추가 경비 항목과 금액
		$info["28"]["8"]="3708";//여행패키지취소 규정
		$info["28"]["9"]="3709";//여행패키지해외여행의 경우 외교통상부가 지정하는 여행경보단계
		$info["28"]["10"]="3710";//여행패키지예약담당 연락처
		$info["29"]["1"]="3801";//항공권요금조건, 왕복·편도 여부
		$info["29"]["2"]="3802";//항공권유효기간
		$info["29"]["3"]="3803";//항공권제한사항
		$info["29"]["4"]="3804";//항공권티켓수령방법
		$info["29"]["5"]="3805";//항공권좌석종류
		$info["29"]["6"]="3806";//항공권추가 경비 항목과 금액
		$info["29"]["7"]="3807";//항공권취소 규정
		$info["29"]["8"]="3808";//항공권예약담당 연락처
		$info["30"]["1"]="3901";//자동차 대여 서비스 (렌터카)차종
		$info["30"]["2"]="3902";//자동차 대여 서비스 (렌터카)소유권 이전 조건
		$info["30"]["3"]="3903";//자동차 대여 서비스 (렌터카)추가 선택 시 비용
		$info["30"]["4"]="3904";//자동차 대여 서비스 (렌터카)차량 반환 시 연료대금 정산 방법
		$info["30"]["5"]="3905";//자동차 대여 서비스 (렌터카)차량의 고장·훼손 시 소비자 책임
		$info["30"]["6"]="3906";//자동차 대여 서비스 (렌터카)예약 취소 또는 중도 해약 시 환불 기준
		$info["30"]["7"]="3907";//자동차 대여 서비스 (렌터카)소비자상담 관련 전화번호
		$info["31"]["1"]="4001";//물품대여 서비스 (정수기, 비데, 공기청정기 등)품명 및 모델명
		$info["31"]["2"]="4002";//물품대여 서비스 (정수기, 비데, 공기청정기 등)소유권 이전 조건
		$info["31"]["3"]="4003";//물품대여 서비스 (정수기, 비데, 공기청정기 등)유지보수 조건
		$info["31"]["4"]="4004";//물품대여 서비스 (정수기, 비데, 공기청정기 등)상품의 고장·분실·훼손 시 소비자 책임
		$info["31"]["5"]="4005";//물품대여 서비스 (정수기, 비데, 공기청정기 등)중도 해약 시 환불 기준
		$info["31"]["6"]="4006";//물품대여 서비스 (정수기, 비데, 공기청정기 등)제품 사양
		$info["31"]["8"]="4007";//물품대여 서비스 (정수기, 비데, 공기청정기 등)소비자상담 관련 전화번호
		$info["33"]["1"]="4101";//디지털 콘텐츠 (음원, 게임, 인터넷강의 등)제작자 또는 공급자
		$info["33"]["2"]="4102";//디지털 콘텐츠 (음원, 게임, 인터넷강의 등)이용조건, 이용기간
		$info["33"]["3"]="4103";//디지털 콘텐츠 (음원, 게임, 인터넷강의 등)상품 제공 방식
		$info["33"]["4"]="4104";//디지털 콘텐츠 (음원, 게임, 인터넷강의 등)최소 시스템 사양, 필수 소프트웨어
		$info["33"]["5"]="4105";//디지털 콘텐츠 (음원, 게임, 인터넷강의 등)청약철회 또는 계약의 해제·해지에 따른 효과
		$info["33"]["6"]="4106";//디지털 콘텐츠 (음원, 게임, 인터넷강의 등)소비자상담 관련 전화번호
		$info["34"]["1"]="4201";//상품권 / 쿠폰발행자
		$info["34"]["2"]="4202";//상품권 / 쿠폰유효기간, 이용조건
		$info["34"]["3"]="4203";//상품권 / 쿠폰이용 가능 매장
		$info["34"]["4"]="4204";//상품권 / 쿠폰잔액 환급 조건
		$info["34"]["5"]="4205";//상품권 / 쿠폰소비자상담 관련 전화번호
		$info["35"]["1"]="4301";//기타품명 및 모델명
		$info["35"]["2"]="4302";//기타법에 의한 인증·허가 등을 받았음을 확인할 수 있는 경우 그에 대한 사항
		$info["35"]["3"]="4303";//기타제조국 또는 원산지
		$info["35"]["4"]="4304";//기타제조자, 수입품의 경우 수입자를 함께 표기
		$info["35"]["5"]="4305";//기타A/S 책임자와 전화번호 또는 소비자상담 관련 전화번호
		$info["36"]["1"]="4401";//모바일쿠폰발행자
		$info["36"]["2"]="4402";//모바일쿠폰유효기간
		$info["36"]["3"]="4403";//모바일쿠폰이용조건
		$info["36"]["4"]="4404";//모바일쿠폰이용 가능 매장
		$info["36"]["5"]="4405";//모바일쿠폰환불조건 및 방법
		$info["36"]["6"]="4406";//모바일쿠폰소비자상담 관련 전화번호
		$info["37"]["1"]="4601";//기타용역서비스 제공 사업자
		$info["37"]["2"]="4602";//기타용역법에 의한 인증,허가 등을 받았음을 확인할수 있는 경우 그에 대한 사항
		$info["37"]["3"]="4603";//기타용역이용조건
		$info["37"]["4"]="4604";//기타용역취소,중도해약,해지 조건 및 환불기준
		$info["37"]["5"]="4605";//기타용역취소,환불방법
		$info["37"]["6"]="4606";//기타용역소비자상담 관련 전화번호

		return $info[$code];
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
			
			$z = 0;
			$cid_mapping_check = FALSE;
			foreach ( $cid_list as $cl ) :
				
				$sql = "SELECT
	                		target_cid,
	                		target_name
	                	FROM sellertool_category_linked_relation
	                	WHERE
	                		origin_cid = '" . $cl ['cid'] . "'
	                	AND site_code = '" . $this->site_code . "'
						ORDER BY
							target_cid DESC";

				$this->db->query ( $sql );
				if ($this->db->total) {
					$cid_mapping_check = TRUE;
					$relations = $this->db->fetchall ("object");
					
					foreach ( $relations as $r ) :

					$result [ $z ]['target_cid'] = $r ["target_cid"];
					$result [ $z ]['target_name'] = $r ["target_name"];
					$z++;

					endforeach;
				}
			endforeach
			;
			if ($cid_mapping_check) {
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
					delivery_policy_code,
					return_policy_code,
					supplyer_commission
				FROM sellertool_etc_linked_relation
				WHERE
					".$search_column." = '" . $origin_code . "'
				AND site_code = '" . $this->site_code . "'
				AND etc_div = '". $div ."' ";

		$this->db->query ( $sql );
		if ($this->db->total) {
			$this->db->fetch ();
			$result['target_code'] = $this->db->dt["target_code"];
			$result['target_name'] = $this->db->dt["target_name"];
			$result['delivery_policy_code'] = $this->db->dt["delivery_policy_code"];
			$result['return_policy_code'] = $this->db->dt["return_policy_code"];
			$result['supplyer_commission'] = $this->db->dt["supplyer_commission"];
			return $result;
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
	
	public function getDeleveryCompanyCode($quick) {

		$map_array  = array (
			"AE" => "에어보이익스프레스",
			"CG" => "CJ KOREA (Global)",
			"CI" => "25",//천일택배
			"CJ" => "18",//CJGLS
			"CV" => "42",//편의점택배
			"DG" => "DHL Global Mail",
			"DH" => "6",//대한통운
			"DL" => "DHL",
			"DS" => "22",//대신택배
			"EE" => "APEX(ECMS Express)",
			"EP" => "1",//우체국택배
			"ER" => "우체국등기",
			"ES" => "EMS",
			"FA" => "15",//동부익스프레스,KG로지스
			"FX" => "Fedex",
			"GE" => "GSI Express",
			"GN" => "GSMNtoN(인로스)",
			"H1" => "26",//합동택배
			"HD" => "12",//현대택배
			"HG" => "Hyundai (Global)",
			"HJ" => "13",
			"IN" => "43",//GTX로지스
			"IP" => "i-Parcel",
			"IY" => "23",//일양택배
			"JG" => "Hanjin (Global)",
			"KD" => "21",//경동택배
			"KG" => "5",//로젠택배
			"KL" => "10",//KGB택배
			"KY" => "건영택배",
			"MB" => "GS수퍼",
			"PT" => "범한판토스",
			"TE" => "TNT Express",
			"UP" => "UPS",
			"US" => "USPS"
		);
		
		
		$quick = (int)$quick;
		$return = array_search($quick,$map_array);

		if(empty($return))		$return = 0;

		return $return;
	}


	/**
	 * 택배사 코드 목록
	 *
	 * @return multitype:string
	 */
	/*
	public function getDeleveryCompanyCodeList() {
		
		return array (
				"AE" => "에어보이익스프레스",
			"CG" => "CJ KOREA (Global)",
			"CI" => "천일택배",
			"CJ" => "CJGLS",
			"CV" => "편의점택배",
			"DG" => "DHL Global Mail",
			"DH" => "대한통운",
			"DL" => "DHL",
			"DS" => "대신택배",
			"EE" => "APEX(ECMS Express)",
			"EP" => "우체국택배",
			"ER" => "우체국등기",
			"ES" => "EMS",
			"FA" => "KG로지스",
			"FX" => "Fedex",
			"GE" => "GSI Express",
			"GN" => "GSMNtoN(인로스)",
			"H1" => "합동택배",
			"HD" => "현대택배",
			"HG" => "Hyundai (Global)",
			"HJ" => "한진택배",
			"IN" => "GTX 로지스",
			"IP" => "i-Parcel",
			"IY" => "일양택배",
			"JG" => "Hanjin (Global)",
			"KD" => "경동택배",
			"KG" => "로젠택배",
			"KL" => "KGB택배",
			"KY" => "건영택배",
			"MB" => "GS수퍼",
			"PT" => "범한판토스",
			"TE" => "TNT Express",
			"UP" => "UPS",
			"US" => "USPS"
		);
		
	}
	*/
	
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
     * 발주확인할 내역요청(결재완료 목록조회)
     * 
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
	
	 function getOdrRequest($startTime, $endTime){
		
		/***
			중오 : 주문 요청을 하면 /openapi/gsshop_api/put_orders.php (GS쪽에 경로를 알려줘서 세팅이 되어야함) 데이터를 건건씩 POST로 보내준다. 
		**/
		 //tnsType 주문구분(주문/반품 : S, 취소 : C)
		
		/*
		$getStr = "?supCd=" . $this->supCd . "&sdDt=" . substr($endTime,0,8) . "&tnsType=S";
		$getStr = $getStr = "?supCd=" . $this->supCd . "&sdDt=" . substr($endTime,0,4).'-'.substr($endTime,4,2).'-'.substr($endTime,6,2) . "&tnsType=S";
		$getStr = "?supCd=" . $this->supCd . "&sdDt=" . $endTime . "&tnsType=S";
		echo GSSHOP_ORDER_URL . $getStr;
		$Result = $this->call ( GSSHOP_ORDER_URL . $getStr );
		print_r($Result);
		exit;
		*/

		$request = array();
		$request[]['supCd'] = $this->supCd;
		$request[]['sdDt'] = substr($startTime,0,4).'-'.substr($startTime,4,2).'-'.substr($startTime,6,2);
		$request[]['tnsType'] = 'S';
		print_r($request);
		$this->call ( GSSHOP_ORDER_URL, $request );
	}

	/**
     * 발주확인할 내역(결재완료 목록조회)
     * 
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
	
	//*****실제로 _POST 로 데이터를 받아 처리****
	 function getOdrComplete($startTime, $endTime){
		/*
		Array
		(
			[DateTime] => 20160415
			[DocumentID] => ORDINF
			[ErrorMessage] => 
			[ErrorOccur] => 
			[MessageID] => ORDINF-20160415
			[Price] => 
			[ProcessType] => O
			[PtnModelCode] => 000000019069655003
			[PtnOrderSequence] => 001
			[Quantity] => 1
			[Receiver] => ENTERSIX
			[Sender] => GS SHOP
			[UniqueID] => ORDINF-20160415
			[attrTypNm1] => 095
			[attrTypNm2] => 공통
			[attrTypNm3] => 공통
			[attrTypNm4] => 공통
			[custNo] => 0044340823
			[custPrsnCelTel] => 0504-2305-5403
			[custPrsnHomTel] => 0504-2305-5403
			[custPrsnNm] => 양희익
			[delivAddr1] => 경기 부천시 원미구 역곡동 
			[delivAddr2] => 대주아파트 
			[delivMsg] => 
			[delivZip] => 14653
			[dlvSchdDt] => 
			[dtlPrdCd] => 000000019069655003
			[dtlSupPrdCd] => 867602
			[gftPrdCd] => 
			[ordDt] => 2016-04-15
			[ordItemNo] => 000020
			[ordNo] => 0745179201
			[ordQty] => 1
			[ordTypeCd] => O
			[ordTypeCdG] => TA
			[orgOrdItemNo] => 
			[orgOrdNo] => 
			[orgOrgOrdItemNo] => 000020
			[orgOrgOrdNo] => 0745179201
			[prdCd] => 000000019069655003
			[prdNm] => [Women
			[retCvsFlag] => 
			[retExchYn] => 
			[rlOrdPrsnCelTel] => 0504-2305-5403
			[rlOrdPrsnHomTel] => 0504-2305-5403
			[rlOrdPrsnNm] => 양희익
			[rsrvDlvYn] => N
			[salePrc] => 69000
			[shipDirDt] => 2016-04-15
			[shipDirTm] => 16
			[stdUprc] => 69000
			[supGivRtamt] => 62100
			[supPrdCd] => 0000104061
			[supPrdNm] => 
			[totItemCnt] => 2
		)

		Sender	전송자	GS SHOP
		Receiver	수신자	BANDINLUNIS
		MessageID	메시지ID	ORDINF-201201041540032
		DateTime	전송일시	201201041540032
		ProcessType	전송구분	(주문:O, 반품:R, 교환:X, 취소:C)
		DocumentID	문서ID	ORDINF
		UniqueID	고유ID	ORDINF-201201041540032
		ErrorOccur	에러코드	
		ErrorMessage	에러메세지	
		ordNo	주문번호	0524782354
		ordItemNo	주문아이템번호	000010
		ordTypeCd	주문유형	(주문:O, 반품:R, 교환:X, 취소:C)
		ordTypeCdG	주문상세유형	"(주문 - TA, ZOR3, ZOR4, ZOR5)
		(반품 - ZKA, ZKA3, ZRE1, ZRE2)"
		shipDirDt	출하지시일	2012-01-04
		shipDirTm	출하지시시간	15
		prdCd	상품코드	000000006117197001
		prdNm	상품명	NEW 301구로 끝내는 중
		dtlPrdCd	상품상세코드	000000006117197001
		attrTypNm1	주문옵션1	레드(색상)
		attrTypNm2	주문옵션2	77(사이즈)
		attrTypNm3	주문옵션3	7cm(STYLE)
		attrTypNm4	주문옵션4	고급머플러증정(사은품)
		ordQty	주문수량	1
		dlvSchdDt	출고준수일	2012-01-09
		rlOrdPrsnNm	주문자	김명주
		rlOrdPrsnHomTel	주문자연락처	02-111-1111
		rlOrdPrsnCelTel	주문자핸드폰	010-222-2222
		custPrsnNm	수취인	김명주
		custPrsnHomTel	수취인연락처	02-111-1111
		custPrsnCelTel	수취인핸드폰	010-222-2222
		delivZip	배송지우편번호	137-809
		delivAddr1	배송지주소1	서울 서초구 반포1동
		delivAddr2	배송지주소2	720-8번지 XX빌라 111호
		delivMsg	배송메세지	빠른 배송 부탁드립니다
		orgOrdNo	직전주문번호	(교환 또는 반품일경우)
		orgOrdItemNo	직전주문아이템번호	(교환 또는 반품일경우)
		ordDt	주문일자	2012-01-04
		supPrdCd	협력사상품코드	MD1001794_7034418
		stdUprc	노출가	11840
		salePrc	판매가	11840
		supGivRtamt	업체지급액	11129
		retCvsFlag	편의점택배여부(반품)	C
		totItemCnt	주문번호내총아이템건수	5
		dtlSupPrdCd	협력사속성상품코드	2634076
		custNo	고객번호	20219613
		orgOrgOrdNo	최초원주문번호	(교환 또는 반품일경우)
		orgOrgOrdItemNo	최초원주문아이템번호	(교환 또는 반품일경우)
		retExchYn	반품교환유료배송여부	Y
		gftPrdCd	사은품본품주문아이템번호	000010
		rsrvDlvYn	예약배송여부	Y
		*/
		
		$requestData = $_POST;
		
		if($requestData['ordTypeCd']=='O'){
			$return[0]["co_oid"]			=		mb_convert_encoding($requestData['ordNo'],'UTF-8','EUC-KR');//주문번호
			$return[0]["co_od_ix"]			=		mb_convert_encoding($requestData['ordItemNo'],'UTF-8','EUC-KR');//주문 순번

			$return[0]["addr1"]				=		str_replace("'","`", mb_convert_encoding($requestData['delivAddr1'],'UTF-8','EUC-KR'));//수취인 주소
			$return[0]["addr2"]				=		str_replace("'","`", mb_convert_encoding($requestData['delivAddr2'],'UTF-8','EUC-KR'));//수취인 주소
			$return[0]["zip"]				=		mb_convert_encoding($requestData['delivZip'],'UTF-8','EUC-KR');//수취인 우편번호
			$return[0]["rname"]				=		mb_convert_encoding($requestData['custPrsnNm'],'UTF-8','EUC-KR');//수취인
			$return[0]["rtel"]				=		mb_convert_encoding($requestData['custPrsnHomTel'],'UTF-8','EUC-KR');//수취인 전화번호
			$return[0]["rmobile"]			=		mb_convert_encoding($requestData['custPrsnCelTel'],'UTF-8','EUC-KR');//수취인 핸드폰번호
			$return[0]["msg"]				=		mb_convert_encoding($requestData['delivMsg'],'UTF-8','EUC-KR');//배송 메모

			$return[0]["bname"]				=		mb_convert_encoding($requestData['rlOrdPrsnNm'],'UTF-8','EUC-KR');//주문자명
			$return[0]["btel"]				=		mb_convert_encoding($requestData['rlOrdPrsnHomTel'],'UTF-8','EUC-KR');//주문자 전화번호
			$return[0]["bmobile"]			=		mb_convert_encoding($requestData['rlOrdPrsnCelTel'],'UTF-8','EUC-KR');//주문자 핸드폰번호
			$return[0]["bmail"]				=		'';//주문자 이메일

			$return[0]["delivery_dcprice"]	=		0;//총배송비 금액
			
			$return[0]["regdate"]			=		date('Y-m-d H:i:s');//주문번호생성일
			//$return[0]["ic_date"]			=		mb_convert_encoding($requestData['shipDirDt'],'UTF-8','EUC-KR').' '.mb_convert_encoding($requestData['shipDirTm'],'UTF-8','EUC-KR').':00:00';//주문결제완료일

			$return[0]["psprice"]			=		mb_convert_encoding($requestData['stdUprc'],'UTF-8','EUC-KR');//상품 판매가(단품)
			$return[0]["pt_dcprice"]			=	mb_convert_encoding($requestData['salePrc'],'UTF-8','EUC-KR') * mb_convert_encoding($requestData['ordQty'],'UTF-8','EUC-KR');//총 상품 판매가
			$return[0]["pcnt"]				=		mb_convert_encoding($requestData['ordQty'],'UTF-8','EUC-KR');//수량

			//$return[0]["pname"]				=		mb_convert_encoding($requestData['prdNm'],'UTF-8','EUC-KR');//상품명			
			$return[0]["pid"]				=		mb_convert_encoding($requestData['supPrdCd'],'UTF-8','EUC-KR');//상품코드

			//착불 (1.선불 2. 착불)
			$return[0]["delivery_pay_method"]=		'1';//배송비착불여부
			$return[0]["option_id"]				=		mb_convert_encoding($requestData['dtlSupPrdCd'],'UTF-8','EUC-KR');//옵션코드
			$return[0]["option_text"]			=		mb_convert_encoding($requestData['attrTypNm1'],'UTF-8','EUC-KR');//옵션명
		}

		//print_r($return);

		return $return;
	}

	/**
     * 발주확인 완료결과
     * 
     */
	
	 function getOdrResponse($od_ix){

		$sql = "SELECT co_oid, co_od_ix FROM shop_order_detail where od_ix='".$od_ix."' ";
		$this->db->query($sql);
		if( $this->db->total ){
			 $this->db->fetch();

			$ordNo =  $this->db->dt['co_oid'];
			$ordItemNo =  $this->db->dt['co_od_ix'];
			$ConfirmedDeliveryDate =  date('Y-m-d');
			$sendFg = 'S';
		}else{
			$ordNo = '';
			$ordItemNo = '';
			$ConfirmedDeliveryDate = '';
			$sendFg = 'E';
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
		<PurchaseOrder_V01_00>
		'.$this->responseXmlHeader('ORDINF',$od_ix,'S').'
		<MessageBody>
				<PurchaseOrders>
						<ordItemNo>'.$ordItemNo.'</ordItemNo>
						<ordNo>'.$ordNo.'</ordNo>
						<OrderGenerationDate>'.$ConfirmedDeliveryDate.'</OrderGenerationDate>
						<ProductLineItem>
								<ConfirmedDeliveryDate>'.$ConfirmedDeliveryDate.'</ConfirmedDeliveryDate>
								<sendFg>'.$sendFg.'</sendFg>
						</ProductLineItem>
				</PurchaseOrders>
		</MessageBody>
		</PurchaseOrder_V01_00>';
		header ( "content-type: text/xml" );
		ob_clean();
		echo $xml;
		//로그 확인하려면 exit 주석처리해야함
		exit;
	}
	
	function responseXmlHeader($type,$index,$ProcessType){
		$msg_id = $type.'-'.$index.'-'.date('YmdHis');
		$xml = '<MessageHeader>
				<Sender>ENTERSIX</Sender>
				<Receiver>GS SHOP</Receiver>
				<MessageID>'.$msg_id.'</MessageID>
				<DateTime>'.date('YmdHis').'</DateTime>
				<ProcessType>'.$ProcessType.'</ProcessType>
				<DocumentID>'.$type.'</DocumentID>
				<UniqueID>'.$msg_id.'</UniqueID>
				<ErrorOccur></ErrorOccur>
				<ErrorMessage></ErrorMessage>
		</MessageHeader>';
		return $xml;
	}
	
	//주문 취소 요청
	function getCancelApplyOdrRequest($startTime, $endTime){
		
		/***
			중오 : 주문 요청을 하면 /openapi/gsshop_api/put_orders.php (GS쪽에 경로를 알려줘서 세팅이 되어야함) 데이터를 건건씩 POST로 보내준다. 
		**/
		 //tnsType 주문구분(주문/반품 : S, 취소 : C)
		
		/*
		$getStr = "?supCd=" . $this->supCd . "&sdDt=" . substr($endTime,0,8) . "&tnsType=S";
		$getStr = $getStr = "?supCd=" . $this->supCd . "&sdDt=" . substr($endTime,0,4).'-'.substr($endTime,4,2).'-'.substr($endTime,6,2) . "&tnsType=S";
		$getStr = "?supCd=" . $this->supCd . "&sdDt=" . $endTime . "&tnsType=S";
		echo GSSHOP_ORDER_URL . $getStr;
		$Result = $this->call ( GSSHOP_ORDER_URL . $getStr );
		print_r($Result);
		exit;
		*/

		$request = array();
		$request[]['supCd'] = $this->supCd;
		$request[]['sdDt'] = substr($startTime,0,4).'-'.substr($startTime,4,2).'-'.substr($startTime,6,2);
		$request[]['tnsType'] = 'C';
		$this->call ( GSSHOP_ORDER_URL, $request );
	}

	/**
     * 주문 취소 내역
     * 
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
	//*****실제로 _POST 로 데이터를 받아 처리****

	 function getCancelApplyOdrComplete($startTime, $endTime){
		
		/*
		Array
		(
			[DateTime] => 20160415
			[DocumentID] => ORDINF
			[ErrorMessage] => 
			[ErrorOccur] => 
			[MessageID] => ORDINF-20160415
			[Price] => 
			[ProcessType] => O
			[PtnModelCode] => 000000019069655003
			[PtnOrderSequence] => 001
			[Quantity] => 1
			[Receiver] => ENTERSIX
			[Sender] => GS SHOP
			[UniqueID] => ORDINF-20160415
			[attrTypNm1] => 095
			[attrTypNm2] => 공통
			[attrTypNm3] => 공통
			[attrTypNm4] => 공통
			[custNo] => 0044340823
			[custPrsnCelTel] => 0504-2305-5403
			[custPrsnHomTel] => 0504-2305-5403
			[custPrsnNm] => 양희익
			[delivAddr1] => 경기 부천시 원미구 역곡동 
			[delivAddr2] => 대주아파트 
			[delivMsg] => 
			[delivZip] => 14653
			[dlvSchdDt] => 
			[dtlPrdCd] => 000000019069655003
			[dtlSupPrdCd] => 867602
			[gftPrdCd] => 
			[ordDt] => 2016-04-15
			[ordItemNo] => 000020
			[ordNo] => 0745179201
			[ordQty] => 1
			[ordTypeCd] => O
			[ordTypeCdG] => TA
			[orgOrdItemNo] => 
			[orgOrdNo] => 
			[orgOrgOrdItemNo] => 000020
			[orgOrgOrdNo] => 0745179201
			[prdCd] => 000000019069655003
			[prdNm] => [Women
			[retCvsFlag] => 
			[retExchYn] => 
			[rlOrdPrsnCelTel] => 0504-2305-5403
			[rlOrdPrsnHomTel] => 0504-2305-5403
			[rlOrdPrsnNm] => 양희익
			[rsrvDlvYn] => N
			[salePrc] => 69000
			[shipDirDt] => 2016-04-15
			[shipDirTm] => 16
			[stdUprc] => 69000
			[supGivRtamt] => 62100
			[supPrdCd] => 0000104061
			[supPrdNm] => 
			[totItemCnt] => 2
		)

		Sender	전송자	GS SHOP
		Receiver	수신자	BANDINLUNIS
		MessageID	메시지ID	ORDINF-201201041540032
		DateTime	전송일시	201201041540032
		ProcessType	전송구분	(주문:O, 반품:R, 교환:X, 취소:C)
		DocumentID	문서ID	ORDINF
		UniqueID	고유ID	ORDINF-201201041540032
		ErrorOccur	에러코드	
		ErrorMessage	에러메세지	
		ordNo	주문번호	0524782354
		ordItemNo	주문아이템번호	000010
		ordTypeCd	주문유형	(주문:O, 반품:R, 교환:X, 취소:C)
		ordTypeCdG	주문상세유형	"(주문 - TA, ZOR3, ZOR4, ZOR5)
		(반품 - ZKA, ZKA3, ZRE1, ZRE2)"
		shipDirDt	출하지시일	2012-01-04
		shipDirTm	출하지시시간	15
		prdCd	상품코드	000000006117197001
		prdNm	상품명	NEW 301구로 끝내는 중
		dtlPrdCd	상품상세코드	000000006117197001
		attrTypNm1	주문옵션1	레드(색상)
		attrTypNm2	주문옵션2	77(사이즈)
		attrTypNm3	주문옵션3	7cm(STYLE)
		attrTypNm4	주문옵션4	고급머플러증정(사은품)
		ordQty	주문수량	1
		dlvSchdDt	출고준수일	2012-01-09
		rlOrdPrsnNm	주문자	김명주
		rlOrdPrsnHomTel	주문자연락처	02-111-1111
		rlOrdPrsnCelTel	주문자핸드폰	010-222-2222
		custPrsnNm	수취인	김명주
		custPrsnHomTel	수취인연락처	02-111-1111
		custPrsnCelTel	수취인핸드폰	010-222-2222
		delivZip	배송지우편번호	137-809
		delivAddr1	배송지주소1	서울 서초구 반포1동
		delivAddr2	배송지주소2	720-8번지 XX빌라 111호
		delivMsg	배송메세지	빠른 배송 부탁드립니다
		orgOrdNo	직전주문번호	(교환 또는 반품일경우)
		orgOrdItemNo	직전주문아이템번호	(교환 또는 반품일경우)
		ordDt	주문일자	2012-01-04
		supPrdCd	협력사상품코드	MD1001794_7034418
		stdUprc	노출가	11840
		salePrc	판매가	11840
		supGivRtamt	업체지급액	11129
		retCvsFlag	편의점택배여부(반품)	C
		totItemCnt	주문번호내총아이템건수	5
		dtlSupPrdCd	협력사속성상품코드	2634076
		custNo	고객번호	20219613
		orgOrgOrdNo	최초원주문번호	(교환 또는 반품일경우)
		orgOrgOrdItemNo	최초원주문아이템번호	(교환 또는 반품일경우)
		retExchYn	반품교환유료배송여부	Y
		gftPrdCd	사은품본품주문아이템번호	000010
		rsrvDlvYn	예약배송여부	Y
		*/
		
		$requestData = $_POST;

		if($requestData['ordTypeCd']=='C'){

			$return[0]["co_oid"]			=		mb_convert_encoding($requestData['ordNo'],'UTF-8','EUC-KR');//주문번호
			$return[0]["co_od_ix"]			=		mb_convert_encoding($requestData['ordItemNo'],'UTF-8','EUC-KR');//주문 순번
			$return[0]["pcnt"]				=		mb_convert_encoding($requestData['ordQty'],'UTF-8','EUC-KR');//수량
			$return[0]["co_claim_group"]	=		$return[0]["co_od_ix"];//취소 등록 고유 번호
			$return[0]["msg"]				=		'GS API 취소완료';//취소 등록 사유
			$return[0]["regdate"]			=		date('Y-m-d H:i:s');//취소일자
			$return[0]["reason_code"]		=		"SYS";
		}

		return $return;
	}
	
	
	/**
     * 취소 요청 완료결과
     * 
     */
	
	 function getCancelApplyOdrResponse($od_ix){

		$sql = "SELECT co_oid, co_od_ix, status FROM shop_order_detail where od_ix='".$od_ix."' ";
		$this->db->query($sql);
		$this->db->fetch();

		if( $this->db->dt['status'] == 'CC' ){
			$ordNo =  $this->db->dt['co_oid'];
			$ordItemNo =  $this->db->dt['co_od_ix'];
			$ConfirmedDeliveryDate =  date('Y-m-d');
			$sendFg = 'S';
		}else{
			$ordNo = '';
			$ordItemNo = '';
			$ConfirmedDeliveryDate = '';
			$sendFg = 'E';
		}

		$xml = '<?xml version="1.0" encoding="utf-8"?>
		<PurchaseOrder_V01_00>
		'.$this->responseXmlHeader('ORDINF',$od_ix,'C').'
		<MessageBody>
				<PurchaseOrders>
						<ordItemNo>'.$ordItemNo.'</ordItemNo>
						<ordNo>'.$ordNo.'</ordNo>
						<OrderGenerationDate>'.$ConfirmedDeliveryDate.'</OrderGenerationDate>
						<ProductLineItem>
								<ConfirmedDeliveryDate>'.$ConfirmedDeliveryDate.'</ConfirmedDeliveryDate>
								<sendFg>'.$sendFg.'</sendFg>
						</ProductLineItem>
				</PurchaseOrders>
		</MessageBody>
		</PurchaseOrder_V01_00>';
		
		header ( "content-type: text/xml" );
		ob_clean();
		echo $xml;
		//로그 확인하려면 exit 주석처리해야함
		exit;
	}


	/**
     * 출고 요청
     * 
     */
	
	 function sendOrdReqDelivery($data){
		
		$co_oid = $data[co_oid];
		$co_od_ix = $data[co_od_ix];

		$requestXmlBody = $this->makeOrderDeliveryXml ($data);

		$Result = $this->call ( GSSHOP_DELIVERY_URL, $requestXmlBody, false );

		if(! empty ($Result)){
			//성공처리
			$return = new resultData();
			if($Result=="S"||$Result=="Y"){
				$return->resultCode = '200';
				$return->message = "출고 연동완료 co_oid = ".$data['co_oid'].", co_od_ix = ".$data['co_od_ix'];
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$return,'delivery',$data['od_ix']);
				$resultCode = 'success';
				$return->resultCode = $resultCode;
			}else{
				$return->resultCode = '500';
				$return->message = "에러[".$Result."] co_oid = ".$data['co_oid'].", co_od_ix = ".$data['co_od_ix'];
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$return,'delivery',$data['od_ix']);
				$resultCode = 'fail';
				$return->resultCode = $resultCode;
			}
			return $return;
		}else{
			$return = new resultData();
			$return->resultCode = '500';
			$return->message = "응답없음 co_oid = ".$data['co_oid'].", co_od_ix = ".$data['co_od_ix'];
			$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$return,'delivery',$data['od_ix']);
			$resultCode = 'response_fail';
			$return->resultCode = $resultCode;
			return $return;
		}
	}

	/**
     * 출고 요청 XML 생성
     * 
     */
	function makeOrderDeliveryXml($data){
		global $HEAD_OFFICE_CODE;
		
		//"(출고완료:C, 배송완료:D, 반품완료:R)(주문취소불가:X, 주문품절:P)(출고예정일등록:N, 환불보류:H)"
		$xml = '<?xml version="1.0" encoding="utf-8"?>
		<DeliveryStatus_V01_00>
		'.$this->responseXmlHeader('DLVINF',$data['od_ix'],'C').'
		<MessageBody>
			<OrderStatus>
					<ordNo>'.$data['co_oid'].'</ordNo>
					<ordItemNo>'.$data['co_od_ix'].'</ordItemNo>
					<deliveryCd>' . $this->getDeleveryCompanyCode( $data['quick'] ) . '</deliveryCd>
					<deliveryNo>'.$data['invoice_no'].'</deliveryNo>
					<cmpulDlv>C</cmpulDlv>
			</OrderStatus>
		</MessageBody>
		</DeliveryStatus_V01_00>';

		return $xml;
	}

}