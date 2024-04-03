<?php
/**
 * 디멘드쉽 API 라이브러리
 * 
 * @version 0.4
 * @author bgh
 * @date 2013.12.04
 */
require 'demandship.config.php';
require 'demandship.class.php';
require $_SERVER['DOCUMENT_ROOT'].'/admin/openapi/standard.object.php';
//include_once $_SERVER ['DOCUMENT_ROOT'] . '/admin/class/layout.class';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/class/database.class';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/admin/lib/imageResize.lib.php';

ini_set('memory_limit',-1);

class Lib_demandship extends Call_demandship {
	private $app_id;
	private $app_secret;
	private $pathner_access_token;
	 
	private $db;
	private $db2;
	public $site_code;
	private $userInfo;
	private $api_key;
	private $vcode;
	private $co;
	private $vcodeUrl;
	private $vcodeCoUrl;
	private $currencyRate;
	private $result;
	private $error;
	public $debug;
	private $buying_service_currencyinfo;
	

	public function __construct($dummy_key = '') {
		$this->db = new Database ();
		$this->db2 = new Database ();
		$this->site_code = $dummy_key;
		$this->userInfo = $this->getUserInfo ();
		$this->api_key = $this->userInfo ['api_key'];
		$this->seller_key = $this->userInfo['company_id'];
		$this->debug = false;
		if($_POST["is_debuging"] || $this->debug){
			$this->is_message_display = true;
		}else{
			$this->is_message_display = false;
		}

		/*
		1. 파트너 계약을 통해서 오프라인에서 발급받아서 사용
		2. seller/add API 를 사용할때만 pathner_access_token 은 사용
		  - seller/add 등록후 발급받은 정보를 저장 관리하여야 한다.
		{
		"code":0,"message":"Seller created",
			"data":{
			"seller_id":"d259bb87-634f-45b7-85e6-bf0cde3d0696",
			"access_token":"7dc8a33736b539136447bce26a9888288eb6a71cbcecd6b816b82c2c7d35b330",
			"refresh_token":"69d494dfcc3496fa5ce709116598562232ff280bb43df328f0501ca2c801c8a4"
			}
		}
			
		3. seller access_token , refresh_token 을 셀러정보에 저장하여 관리하여야 한다.
		4. seller access_token 이 만료 됐을경우 /api/v1/oauth/access_token seller refresh_token 을 사용하여 access_token 을 갱신하여 한다.
		5. /api/v1/oauth/access_token 에 필요한 정보는 

		*/

		/*
		운영정보
		App ID: 0e90f0b5fcce961ba694d6a307af9fadf2e72b5bc1d1c74be1b30fea69effa98
App Secret: 85a6a8113be60f4d4a2ebd76dbaa1b516c725f68a5f433b04f12cafa6f9343c8
Access Token: 49d8ab40790c054a642b5313ce7117d7dc89184e078bf09203e36dcce1356389

define('APP_ID',"6ddafc6902d9249ae585ba4dff365278e3fbf6f55c792a527610e47ed6f876bc"); // 운영
define('APP_SECRET',"85a6a8113be60f4d4a2ebd76dbaa1b516c725f68a5f433b04f12cafa6f9343c8"); // 운영
define('PATHNER_ACCESS_TOKEN',"49d8ab40790c054a642b5313ce7117d7dc89184e078bf09203e36dcce1356389"); // 운영
		*/
		//print_r($this->userInfo);

		$sql = "select 
				demandship_service_key, company_id
				from
					common_seller_delivery
				where
					company_id = '". $_SESSION['admininfo']['company_id'] ."' limit 1";
		$this->db->query($sql);
		$sellkey = $this->db->fetch();


		$this->app_id = APP_ID;			//"6ddafc6902d9249ae585ba4dff365278e3fbf6f55c792a527610e47ed6f876bc";
		$this->app_secret = APP_SECRET;	//"af930c8a4ca84a721a87b06c007a596f2f9367f50b5458ca4a926767111129fc";

		if(!empty($sellkey['demandship_service_key'])){
			$this->pathner_access_token = $sellkey['demandship_service_key'];
			$this->access_token = $sellkey['demandship_service_key'];
		}

		//$this->pathner_access_token = PATHNER_ACCESS_TOKEN;
		//$this->access_token = PATHNER_ACCESS_TOKEN;

		list ( $this->vcode , $this->co ) = explode( '|' , $this->userInfo ['api_key'] );
		$this->vcodeUrl = '?vcode=' . $this->vcode;
		$this->vcodeCoUrl = '?vcode=' . $this->vcode . '&co=' . $this->co;
		//echo "buyingservice_price_yn:".$this->userInfo['buyingservice_price_yn'];
		//exit;
		if( $this->userInfo['buyingservice_price_yn'] == 'Y' && ! empty( $this->userInfo['currency_ix'] ) ){
			$sql="select * from sellertool_buyingservice_info where company_id = '".$this->seller_key."' and disp='1' order by regdate desc limit 1 ";
			//echo $sql;
			$this->db->query ($sql) ;
			if(!$this->db->total){
				$sql="select * from shop_buyingservice_info where exchange_type='".$this->userInfo['currency_ix']."' and disp='1' order by regdate desc ";
				$this->db->query ($sql) ;
			}
			//echo $sql;
			//exit;
			
			$this->db->fetch();
			$this->buying_service_currencyinfo = $this->db->dt;
			$this->currencyRate = number_format(1/$this->db->dt['exchange_rate'],5);
		}

		if( empty($this->currencyRate) ){
			$this->currencyRate = 1;
		}
	}
	public function updateCurrencyInfo() {
		if( $this->userInfo['buyingservice_price_yn'] == 'Y' && ! empty( $this->userInfo['currency_ix'] ) ){
			$sql="select * from sellertool_buyingservice_info where company_id = '".$this->seller_key."' and disp='1' order by regdate desc limit 1 ";
			//echo $sql;
			$this->db->query ($sql) ;
			if(!$this->db->total){
				$sql="select * from shop_buyingservice_info where exchange_type='".$this->userInfo['currency_ix']."' and disp='1' order by regdate desc ";
				$this->db->query ($sql) ;
			}
			//echo $sql;
			//exit;
			
			$this->db->fetch();
			$this->buying_service_currencyinfo = $this->db->dt;
			$this->currencyRate = number_format(1/$this->db->dt['exchange_rate'],5);
		}
	}

	public function getCouriers ($data) {
		if($this->checkUsaleToken($this->seller_key)){
			$action = "/api/v1/couriers";

			$this->method = GET;
			$result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );

			$result = json_decode($result, true);
			if($this->is_message_display){
				print_r($result);
			}
			$couriers_infos = $result[data];
			for($i=0 ; $i < count($couriers_infos) ;$i++){
				$sql = "select * from shop_code where code_gubun = '02' and code_ix = '".$couriers_infos[$i][slug]."' ";
				if($this->is_message_display){
					echo nl2br($sql).";<br>";
				}
				$this->db->query ($sql);
				if($this->db->total){
					$sql = "update shop_code set code_name = '".$couriers_infos[$i][name]."(".$couriers_infos[$i][other_name].")' , code_etc2 = '".$couriers_infos[$i][phone]."' where code_gubun= '02' and code_ix = '".$couriers_infos[$i][slug]."'  ";
				}else{
					$sql = "insert into shop_code
					(code_gubun,code_ix,code_name,code_etc1,code_etc2,code_etc3,code_etc4,disp,view_order) values('02','".$couriers_infos[$i][slug]."','".$couriers_infos[$i][name]."(".$couriers_infos[$i][other_name].")','','".$couriers_infos[$i][phone]."','$code_etc3','$code_etc4','1','".($i+1)."')";
				}
				if($this->is_message_display){
				echo nl2br($sql).";<br><br>";
				}
				$this->db->query ($sql);
			}
		}
	}
	
	public function getVariants($data){
		if($this->checkUsaleToken($this->seller_key)){
			 
			//$api_data["endDate"]=$endDate;
			$Params[start] = $data["start"];
			$Params[limit] = $data["limit"];

			$action = "/api/v2/variants/multi-get";
			$post_datas = array( 
				"start"=>$Params[start],
				"limit"=>$Params[limit]
			);
			if($this->is_message_display){
				echo "<pre>";print_r($post_datas);
			}
			$requestBody = json_encode($post_datas);
			$this->method = GET;
			$result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );

			$result = json_decode($result, true);
			if($this->is_message_display){
				print_r($result);
			}
			return $result;
		}
	}

	public function getOrder($data) {

		if($this->checkUsaleToken($this->seller_key)){
			if($data["startDate"] && !is_null($data["startDate"])){
				$Params[since] = ChangeDate($data["startDate"],"Y-m-d H:i:s");
			}else{
				$Params[since] = $vfourweekago = date("Y-m-d H:i:s", time()-84600*90);
			}
			//$api_data["endDate"]=$endDate;
			$Params[start] = $data["start"];
			$Params[limit] = $data["limit"];

			 
			$action = "/api/v2/orders/multi-get";
			$post_datas = array(
				"since"=>$Params[since],
				"start"=>$Params[start],
				"limit"=>$Params[limit]
			);
			if($this->is_message_display){
				echo "<pre>";print_r($post_datas);
			}
			$requestBody = json_encode($post_datas);
			$this->method = GET;
			$result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );

			$result = json_decode($result, true);
			if($this->is_message_display){
				print_r($result);
			}
			//return $result;
			$order_infos = $result[data];
			//return ;
			for($i=0 ; $i < count($order_infos) ;$i++){
				
				/*
				[id] => 8d42e743-0697-4fbf-8d68-7e4c5a89b98a
                    [transaction_id] => f3ff38fe-2a96-55de-a4e9-69ecb92c5e4b
                    [product_id] => 73ecdf3c-e5c0-4240-97d6-bed25c6331b5
                    [variant_id] => ce70e815-f7f3-46e2-86c2-96b3cff3e7da
                    [variant_name] => Blue 3-6m
                    [buyer_id] => 8cbaf0f7-55da-445c-93e4-3e308d2b2e94
                    [quantity] => 1
                    [sku] => DD007_3-6m_Blue
                    [state] => PAID
                    [shipping_provider] => 
                    [tracking_number] => 
                    [shipped_date] => 
                    [ship_note] => 
                    [order_total] => 9.0
                    [days_to_fulfill] => 
                    [hours_to_fulfill] => 
                    [price] => 7.0
                    [cost] => 7.0
                    [shipping] => 2.0
                    [shipping_cost] => 2.0
                    [product_name] => Hot sale !Carton print baby jumpsuit rompers
                    [product_image_url] => http://static1.ensogo.co.th/assets/deals/4d12f895-08b3-4152-a35b-b972d0eac32c/deal_box.jpg?ts=1453170674
                    [order_time] => 2016-01-19T02:31:12.000Z
                    [refunded_by] => 
                    [refunded_time] => 
                    [refund_reason] => 
                    [last_updated] => 2016-03-14T03:19:04.000Z
                    [country] => th
                    [shipping_detail] => Array
                        (
                            [city] => 
                            [country] => 
                            [name] => Ka Man Lo
                            [phone_number] => 64059090
                            [state] => 
                            [street_address1] => 12 F, Block 3, Highland Park, Kwai Chung
                            [street_address2] => 
                            [zipcode] => 
                        )

				*/
				$bname = $order_infos[$i][shipping_detail][name];
				$rname = $order_infos[$i][shipping_detail][name];
				$rtel = $order_infos[$i][shipping_detail][phone_number];
				$raddr = $order_infos[$i][shipping_detail][street_address1]." ".$order_infos[$i][shipping_detail][street_address2];
				$rzip = $order_infos[$i][shipping_detail][zipcode];

				$static_date = date("Y-m-d",strtotime($order_infos[$i][order_time]));
				$order_date = date("Y-m-d H:i:s",strtotime($order_infos[$i][order_time]));
				
				//PAID, SHIPPED, DELIVERED, REFUNDED, UNDELIVERED
				if($order_infos[$i][state] == "PAID"){
					$status = "IC";
				}else if($order_infos[$i][state] == "SHIPPED"){
					$status = "DI";
				}else if($order_infos[$i][state] == "DELIVERED"){
					$status = "DC";
				}else if($order_infos[$i][state] == "REFUNDED"){
					$status = "FC";
				}else{
					$status = "IR";
				}

				$co_oid = $order_infos[$i][id];
				$co_buyer_id = $order_infos[$i][buyer_id];
				$co_transaction_id = $order_infos[$i][transaction_id];
				$buying_origin_country = $order_infos[$i][country];

				
				$deliverycode = $order_infos[$i][tracking_number];
				$payment_price = $order_infos[$i][order_total];

				$order[delivery_total_price] = $order_infos[$i][shipping_cost];
				$order[delivery_method] = "";

				// 상품정보
				$co_pid = $order_infos[$i][product_id];
				$co_variant_id = $order_infos[$i][variant_id];

				$select_option_text = $order_infos[$i][variant_name];
				$pcode = $order_infos[$i][sku];
				$sku_info = explode("-",$order_infos[$i][sku]);
				if(count($sku_info) > 1){
					$pid = $sku_info[0];
					$select_option_id = $sku_info[1];
				}else if(strlen($order_infos[$i][sku]) == 10) {//0000057244
					$pid = $order_infos[$i][sku];
				}else{
					$sql = "select pid from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where id = '".$order_infos[$i][sku]."' ";
				//	echo $sql;
					$this->db->query ( $sql );
					$this->db->fetch();
					$pid = $this->db->dt["pid"];
					//$pid = $order_infos[$i][sku];//$sku_info[0];
				}
				$pname = $order_infos[$i][product_name];
				$count = $order_infos[$i][quantity];
				$product_type = 1;
				$psprice = $order_infos[$i][price];
				$ptprice = $order_infos[$i][order_total];
				$company_id = $this->seller_key;
				$delivery_type = "D";
				$order_from = "ensogo";
				if($bco_transaction_id != $co_transaction_id){
					unset($_payment_price[$co_transaction_id]);
				}

				if($bcompany_id == ""){
					$bcompany_id = $company_id;
				}
				if($bco_transaction_id == ""){
					$bco_transaction_id = $co_transaction_id;
				} 

				$sql = "select com_name from common_company_detail ccd where company_id = '".$company_id."' ";
			//	echo $sql;
				$this->db->query ( $sql );
				$this->db->fetch();
				$company_name = $this->db->dt["com_name"];


				//$sql = "select * from ".TBL_SHOP_ORDER." where co_oid = '".$co_oid."' ";
				$sql = "select * from ".TBL_SHOP_ORDER." where co_transaction_id = '".$co_transaction_id."'  ";// 
				echo $sql."<br>";
				$this->db->query ( $sql );
				if($this->db->total == 0){
					$oid = date("YmdHis")."-".rand(1000, 9999);
					$_payment_price[$co_transaction_id] += $payment_price;
					$_delivery_total_price[$company_id] += $order[delivery_total_price];

					$sql = "insert into ".TBL_SHOP_ORDER."
							(oid,uid, buserid, bname,mem_group,gp_ix, btel,bmobile,bmail,bzip,baddr,rname,rtel,rmobile,rmail,zip,addr,msg,date,static_date,method,vb_info,bank,bank_input_date,settle_module, tid,authcode,status,delivery_price, delivery_method, delivery_type, instead_buy_price, use_cupon_code,use_cupon_price,use_reserve_price,use_member_sale_price,total_price,payment_price,real_income_price,real_delivery_price,real_instead_buy_price,taxsheet_yn,receipt_y,es_sendno,co_oid, co_buyer_id, co_transaction_id, buying_origin_country, user_ip,user_agent, payment_agent_type)
							values
							('$oid','','','$bname','','','$btel','$bmobile','$bmail','$bzip','$baddr','$rname','$rtel','$rmobile','$rmail','$rzip','$raddr','$msg','".$order_date."','".$static_date."','$nPaymethod','$vb_info','$strCard','','".$layout_config[sattle_module]."','$pg_tid','$authcode','$status','".$order[delivery_total_price]."','".$order[delivery_method]."','".$delivery_type."','".$order[instead_buy_price]."','','".$order[use_cupon_price]."','".$order[reserve_price]."','".$order[member_sale_price]."','$sum','".$payment_price."','".$payment_price."','".$order[delivery_total_price]."','".$order[instead_buy_price]."','".$order[taxsheet_yn]."','$receipt_y','".$ES_SENDNO."','".$co_oid."','".$co_buyer_id."','".$co_transaction_id."','".$buying_origin_country."','".$user_ip."','".$user_agent."','".($layout_config[mall_page_type] == "M" ? "M":"W")."')";
					echo nl2br($sql)."<br><br><br>";
					//exit;
					$this->db->query ($sql);

				}else{
					$_payment_price[$co_transaction_id] += $payment_price;
					$_delivery_total_price[$co_transaction_id."-".$company_id] += $order[delivery_total_price];
					$this->db->fetch();
					$oid = $this->db->dt[oid];

					$sql = "update ".TBL_SHOP_ORDER." set payment_price = '".$_payment_price[$co_transaction_id]."' where co_transaction_id = '".$co_transaction_id."' ";
					echo "<b>".$sql."</b><br>";
					///$sql = "update ".TBL_SHOP_ORDER." set payment_price = '".$_payment_price[$co_transaction_id]."',  co_transaction_id = '".$co_transaction_id."'  where co_oid = '".$co_oid."' ";
					$this->db->query ($sql);
				}

					
				$sql = "select * from ".TBL_SHOP_ORDER_DETAIL." where co_pid = '".$co_pid."' and co_oid = '".$co_oid."' ";
				//echo $sql."<br>";
				$this->db->query ( $sql );
				//echo "<b>".$this->db->total."===".$oid."</b><br>";
				if($this->db->total == 0 && $oid){
					$sql = "insert into ".TBL_SHOP_ORDER_DETAIL."
									(od_ix,mall_ix, oid,rfid, kwid, order_from, cid,pid,pcode, product_type, pname,paper_pname,option1,option_text,option_price, pcnt,coprice,psprice,ptprice,status, reserve,use_coupon,use_coupon_code, company_id,company_name,reg_charger_ix,com_phone, represent_name, represent_mobile,one_commission,commission,surtax_yorn,stock_use_yn,co_oid,  co_pid, sc_ix, floor, line, no, regdate)
									values
									('','','$oid','','','".$order_from."','$cid','$pid','$pcode','$product_type','$pname','".$paper_pname."','$select_option_id','$select_option_text','$option_price','$count','$coprice','$psprice','$ptprice','$status','$reserve','','','$company_id','$company_name','$reg_charger_ix','$com_phone','$represent_name','$represent_mobile','$one_commission','$commission','$surtax_yorn','$stock_use_yn','".$co_oid."', '$co_pid','$sc_ix','$floor','$line','$no',NOW())";
					echo nl2br($sql)."<br><br><br>";
					$this->db->query ( $sql );
				}else{
					$sql = "select status from  ".TBL_SHOP_ORDER_DETAIL."  where co_pid = '".$co_pid."' and co_oid = '".$co_oid."' ";
					//$sql = "update ".TBL_SHOP_ORDER."  set status = '".$status."' where co_oid = '".$co_oid."' ";
					$this->db->query ( $sql );
					$this->db->fetch();
					$this_status = $this->db->dt[status];

					//$sql = "update ".TBL_SHOP_ORDER_DETAIL."  set  pid = '".$pid."', company_name = '".$company_name."', co_oid = '".$co_oid."' , co_pid = '".$co_pid."' where oid = '".$oid."' ";
					//echo nl2br($sql)."<br><br><br>";
					//$this->db->query ( $sql );
					//if($status != $this_status){
						echo $this->db->total."::".$status."::".$this_status."<br>";
						//$sql = "update ".TBL_SHOP_ORDER_DETAIL."  set status = '".$status."', co_pid = '".$co_pid."' , co_oid = '".$co_oid."' where co_pid = '".substr($co_pid,0,30)."' and co_oid = '".substr($co_oid,0,30)."'  ";
						$sql = "update ".TBL_SHOP_ORDER_DETAIL."  set status = '".$status."', co_pid = '".$co_pid."' , co_oid = '".$co_oid."' where co_pid = '".$co_pid."' and co_oid = '".$co_oid."'  ";
						echo $sql."<br><br>";
						$this->db->query ( $sql );
					//}
				}
				
				//".$bcompany_id ."!=". $company_id ."&&
				$bco_transaction_id = $co_transaction_id;
				//echo "<b>". $bco_transaction_id ." != ". $co_transaction_id." ============= ".count($order_infos)." == ".$i." </b><br>";
				if($bco_transaction_id != $co_transaction_id || (count($order_infos) - 1) == $i){
						$sql = "select * from shop_order_delivery sod where oid = '".$oid."' and company_id = '".$bcompany_id."' ";
						$this->db->query ( $sql );

						if($this->db->total == 0){
							$sql = "insert into shop_order_delivery 
										(ode_ix , oid,company_id,company_total,delivery_price,order_delivery_policy,delivery_pay_type,regdate) 
										values 
										('','".$oid."','".$bcompany_id."', (select IFNULL(count(*),1)  from shop_order o,  shop_order_detail sod where o.oid = sod.oid and o.oid = '".$oid."' and sod.company_id = '".$bcompany_id."' and co_transaction_id = '".$bco_transaction_id."' group by company_id) ,'".$_delivery_total_price[$bco_transaction_id."-".$bcompany_id]."','T','',NOW())";
							//echo nl2br($sql)."<br><br><br>";
							$this->db->query ( $sql );
						}
						unset($oid);
						unset($_delivery_total_price[$co_transaction_id."-".$bcompany_id]);
				}

				
				$bcompany_id = $company_id;

				if($this->is_message_display){
					//echo nl2br($sql)."<br><br>";
				}
			}
		}
	}

//DELETE FROM `shop_order_detail` WHERE `oid` LIKE '20160507194003-6448' and date_format(regdate,'%Y%m%d') = '20160526'
	public function getCategoryInfo($company_id) {
	 
		if($this->checkUsaleToken($company_id)){
			//echo "checkUsaleToken";
			$this->method = GET;
			$action = "/api/v2/categories";
			  
			$result = $this->call (DEMANDSHIP_URL, $action, "" );
			//print_R($result);
			return $result;
		}
	}
	
	/**
	 * 사용자 정보 가져오기
	 * 티켓 발급을 위한 아이디 패스워드
	 *
	 * 티켓이 이렇게 해서는 발급을 해도 사용이 안됨.. 사용자에게 팝업으로 노출시키고 수동처리해야하는것으로 판단됨.
	 */
	public function getUserInfo() {
		$sql = "SELECT * 
				FROM sellertool_site_info 
				WHERE site_code = '" . $this->site_code . "'";
		//echo $sql;
		//exit;
		$this->db->query ( $sql );
		if ($this->db->total) {
			return $this->db->fetch ();
		} else {
			$this->error ['code'] = '1001';
			$this->error ['msg'] = '제휴사 정보가 올바르지 않습니다.(오가게)';
			$this->printError ();
		}
	}

	public function getMetaInfo($add_info_id, $meta_key) {
		$sql = "select * from sellertool_add_info_meta where add_info_id = '".$add_info_id."'  ";
		$this->db->query ( $sql );
		if($this->db->total) {

			$sql = "SELECT meta_id, add_info_id, meta_key, meta_value
					FROM sellertool_add_info_meta 
					WHERE add_info_id = '" . $add_info_id. "' and meta_key = '".$meta_key."' ";
			//echo $sql;
			$this->db->query ( $sql );
			if ($this->db->total) {
				$this->db->fetch();
				return $this->db->dt["meta_value"];
			} else {
				$this->error ['code'] = '1001';
				$this->error ['msg'] = '메타정보가 존재하지 않습니다.';
				//$this->printError ();
				return;
			}
		}else{
			$this->error ['code'] = '2010';
			$this->error ['msg'] = '제휴사이트에 셀러가 등록되지 않았습니다.. seller_key = ' . $add_info_id;
			$this->printError ();
			 
		}
	}

	public function SellerAdd($data) {
		
		$action = "/api/v2/sellers/add";
		 
		$post_datas = array("contact_name"=>$data[shop_name],"email"=>$data[com_email],"phone"=>$data[com_phone],"store_name"=>$data[shop_name]);
		$post_data = json_encode($post_datas);
		$this->method = POST;

		$result = $this->call ( DEMANDSHIP_URL, $action, $post_data );
		//$result = '{"code":0,"message":"Seller created","data":{"seller_id":"48707381-c271-4c42-ac32-20004699bf8c","access_token":"1d6053e8d26202450bbe3651077793482980eaaa66d4cbd1f7849859b0e21d64","refresh_token":"5e5cad7424e90d69c7c5bdee888f82fca274f644fc6f8453ef2d304f320f9e35"}}';
		$result = (array)json_decode($result);
		if($this->is_message_display){
			print_r($result);
		}

 
		if($result[code] == 0){ 
			$sql = "select * from sellertool_add_info_meta where add_info_id = '".$this->seller_key."'  ";
			//echo $sql;
			$this->db->debug = false;
			//$this->db->query ( $sql );
			
				$insertable_metakey = array("created_at", "expires_in", "access_token", "refresh_token", "token_type", "store_name", "contact_name", "user_id", "seller_id");

				$response_result = (array)$result["data"];
				//print_r($response_result);
				foreach($response_result as $meta_key => $meta_value){
					if(in_array($meta_key, $insertable_metakey)){
						$sql = "select * from sellertool_add_info_meta where add_info_id = '".$this->seller_key."' and meta_key= '".$meta_key."' ";
						$this->db->query ( $sql );
						if ($this->db->total) {
							$sql = "update sellertool_add_info_meta set
										meta_value= '".$meta_value."' 
										where add_info_id = '".$this->seller_key."' and meta_key= '".$meta_key."'  ";
							//echo nl2br($sql);
							$this->db->query ( $sql );
						}else{
							$sql = "insert into sellertool_add_info_meta 
										(meta_id,add_info_id,meta_key,meta_value) 
										values
										('','".$this->seller_key."','".$meta_key."','".$meta_value."') ";
							//echo nl2br($sql);
							$this->db->query ( $sql );
						 
						}
					}
				}
				 
		}
		return $result;
	}

	public function checkUsaleToken($seller_key) {
		if($this->is_message_display){
			echo "seller_key:".$seller_key;		
			echo ($this->getMetaInfo($seller_key, "created_at")  + $this->getMetaInfo($seller_key, "expires_in"))."::".time()."<br>";
		}
		if($this->getMetaInfo($seller_key, "created_at")  + $this->getMetaInfo($seller_key, "expires_in") < time()){
			if($this->is_message_display){
				echo "기한만료 토큰<br>";
			}
			$this->method = POST;
			$action = "/api/v2/oauth/access_token";
			$post_datas = array(
				"grant_type"=>"refresh_token",
				"client_id"=>"".$this->app_id."",
				"client_secret"=>"".$this->app_secret."",
				"refresh_token"=>"".$this->getMetaInfo($seller_key, "refresh_token")."",
				"redirect_uri"=>"urn:ietf:wg:oauth:2.0:oob"
			);
			if($this->is_message_display){
				echo "seller_key:".$seller_key."<br>";
				echo "<pre>";print_r($post_datas);
			}
			$requestBody = json_encode($post_datas);
			$this->access_token = $this->getMetaInfo($seller_key, "access_token");//$this->pathner_access_token;
			$result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );
			$result = json_decode($result);
			if($this->is_message_display){
				echo "토큰 갱신결과".$action."<br>";
				print_r($result);
			}
			//exit;
			
			if(count($result) > 0){ 
				$sql = "select * from sellertool_add_info_meta where add_info_id = '".$seller_key."'  ";
				$this->db->query ( $sql );
				if($this->db->total) {
					$insertable_metakey = array("created_at", "expires_in", "access_token", "refresh_token", "token_type", "store_name", "contact_name", "user_id");
					foreach($result as $meta_key => $meta_value){
						if(in_array($meta_key, $insertable_metakey)){
							$sql = "select * from sellertool_add_info_meta where add_info_id = '".$seller_key."' and meta_key= '".$meta_key."' ";
							$this->db->query ( $sql );
							if ($this->db->total) {
								$sql = "update sellertool_add_info_meta set
											meta_value= '".$meta_value."' 
											where add_info_id = '".$seller_key."' and meta_key= '".$meta_key."'  ";
								//echo nl2br($sql);
								$this->db->query ( $sql );
							}else{
								$sql = "insert into sellertool_add_info_meta 
											(meta_id,add_info_id,meta_key,meta_value) 
											values
											('','".$seller_key."','".$meta_key."','".$meta_value."') ";
								//echo nl2br($sql);
								$this->db->query ( $sql );
							 
							}
							if($meta_key == "access_token"){
								$this->access_token = $meta_value;
							}
						}
					}
					return true;
				}else{
					$this->error ['code'] = '2010';
					$this->error ['msg'] = '제휴사이트에 셀러가 등록되지 않았습니다.. seller_key = ' . $seller_key;
					$this->printError ();
				}
			}
		}else{
			$this->access_token = $this->getMetaInfo($seller_key, "access_token");
			if($this->is_message_display){
			echo "사용가능 토큰<br>";
			}
			return true;
		}

		
	}
	

	/**
	 * 배송정보 업데이트
	 * api 등록 절차 AddItem -> ReviseItemStock -> ReviseItemSelling
	 *
	 * @param string $co_oid        	
	 * @param string $add_info_id        	
	 */
	public function updateShippingInfo($co_oid ,$shipping_provider, $tracking_number, $add_info_id = '') {

		if($this->checkUsaleToken($this->seller_key)){
			//$action = '/api/v2/orders/'.$co_oid.'/modify-tracking';
			$action = '/api/v2/orders/'.$co_oid.'/fulfill-one';
			
			$this->method = POST;
			$request[id] = $co_oid;
			$request[shipping_provider] = $shipping_provider;
			$request[tracking_number] = $tracking_number;

			$requestBody = json_encode($request);
			$Result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );
			$Result = json_decode($Result, true);

			if($this->is_message_display){
				echo "<br>전송결과 시작======<br>";
				print_r($Result);
				echo "<br>전송결과 종료======<br>";
			}
	
			if($Result['code']=="0"){
				$result->message = '배송정보 업데이트 완료';
				$result->resultCode = 'success'; 
				//$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			}else{
				$result->message = '배송정보 업데이트 실패';
				$result->resultCode = 'fail'; 

				$action = '/api/v2/orders/'.$co_oid.'/modify-tracking';
			
				$this->method = POST;
				$request[id] = $co_oid;
				$request[shipping_provider] = $shipping_provider;
				$request[tracking_number] = $tracking_number;

				$requestBody = json_encode($request);
				$Result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );
				$Result = json_decode($Result, true);
				if($this->is_message_display){
					echo "<br>전송결과 시작======<br>";
					print_r($Result);
					echo "<br>전송결과 종료======<br>";
				}

				//$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			}

			return $Result;
		}else{
			echo "셀러키가 유효하지 않습니다.";

		}
	}
	/**
	 * 상품비활성화
	 * api 등록 절차 AddItem -> ReviseItemStock -> ReviseItemSelling
	 *
	 * @param string $pid        	
	 * @param string $add_info_id        	
	 */
	public function disableGoods($pid = '', $add_info_id = '') {
		$co_pid = $this->getGoodbuySellyPid ( $pid );
		if($this->checkUsaleToken($this->seller_key)){
			$action = '/api/v2/products/'.$co_pid.'/disable';
			$this->method = POST;
			$request[id] = $co_pid;
			$requestBody = json_encode($request);
			$Result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );
			$Result = json_decode($Result, true);
			if($this->is_message_display){
				echo "<br>전송결과 시작======<br>";
				print_r($Result);
				echo "<br>전송결과 종료======<br>";
			}
	
			if($Result['code']=="0"){
				$result->message = '상품 비활성화 완료';
				$result->resultCode = 'success'; 
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			}else{
				$result->message = '상품 비활성화 실패';
				$result->resultCode = 'fail'; 
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			}
		}
	}

	/**
	 * 상품활성화
	 * api 등록 절차 AddItem -> ReviseItemStock -> ReviseItemSelling
	 *
	 * @param string $pid        	
	 * @param string $add_info_id        	
	 */
	public function enableGoods($pid = '', $add_info_id = '') {
		$co_pid = $this->getGoodbuySellyPid ( $pid );
		if($this->checkUsaleToken($this->seller_key)){
			$action = '/api/v2/products/'.$co_pid.'/enable';
			$this->method = POST;
			$request[id] = $co_pid;
			$requestBody = json_encode($request);
			$Result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );
			$Result = json_decode($Result, true);
			if($this->is_message_display){
				echo "<br>전송결과 시작======<br>";
				print_r($Result);
				echo "<br>전송결과 종료======<br>";
			}
			if($Result['code']=="0"){
				$result->message = '상품활성화 완료';
				$result->resultCode = 'success'; 
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			}else{
				$result->message = '상품활성화 실패';
				$result->resultCode = 'fail'; 
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			}
		}

	}


	/**
	 * 옵션 비활성화
	 * api 등록 절차 AddItem -> ReviseItemStock -> ReviseItemSelling
	 *
	 * @param string $pid        	
	 * @param string $add_info_id        	
	 */
	public function disableVariants($variant_id = '', $add_info_id = '') {
		//$co_pid = $this->getGoodbuySellyPid ( $pid );
		if($this->checkUsaleToken($this->seller_key)){
			$action = '/api/v2/variants/'.$variant_id.'/disable';
			$this->method = POST;
			$request[id] = $variant_id;
			$requestBody = json_encode($request);
			$Result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );
			return $Result;
			exit;
			$Result = json_decode($Result, true);
			if($this->is_message_display){
				echo "<br>전송결과 시작======<br>";
				print_r($Result);
				echo "<br>전송결과 종료======<br>";
			}
	
			if($Result['code']=="0"){
				$result->message = '옵션 비활성화 완료';
				$result->resultCode = 'success'; 
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			}else{
				$result->message = '옵션 비활성화 실패';
				$result->resultCode = 'fail'; 
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			}
		}
	}

	/**
	 * 옵션활성화
	 * api 등록 절차 AddItem -> ReviseItemStock -> ReviseItemSelling
	 *
	 * @param string $pid        	
	 * @param string $add_info_id        	
	 */
	public function enableVariants($variant_id = '', $add_info_id = '') {
		//$co_pid = $this->getGoodbuySellyPid ( $pid );
		if($this->checkUsaleToken($this->seller_key)){
			$action = '/api/v2/variants/'.$variant_id.'/enable';
			$this->method = POST;
			$request[id] = $variant_id;
			$requestBody = json_encode($request);
			$Result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );
			return $Result;
			exit;
			$Result = json_decode($Result, true);
			if($this->is_message_display){
				echo "<br>전송결과 시작======<br>";
				print_r($Result);
				echo "<br>전송결과 종료======<br>";
			}
			if($Result['code']=="0"){
				$result->message = '옵션 활성화 완료';
				$result->resultCode = 'success'; 
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			}else{
				$result->message = '옵션 활성화 실패';
				$result->resultCode = 'fail'; 
				$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			}
		}
	}

	public function sendoutShipments($dsid){
		$action = "/api/v1/shipments/sendout";
		$result = $this->call(DEMANDSHIP_URL, $action, $dsid);
		print_r($result);
		//exit;
	}

	/**
	 * error 출력
	 * #exit 처리할 에러만
	 */
	private function printError(){
		/*
		$return = new resultData();
		$result->message = $this->error ['code'] . " : " . $this->error ['msg'] ;
		$result->resultCode = '500';
		$result->productNo = $Result['info']['goods_id']."|".$Result['info']['twitter_id'];
		*/
		//$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result);
		//echo "<script>alert('" . $this->error ['code'] . " : " . $this->error ['msg'] . "');" . $this->error ['script'] . "</script>";
		echo "<script>alert('" . $this->error ['code'] . " : " . $this->error ['msg'] . "');" . "</script>";
		exit();
	}

	/**
	 * 배송정보등록
	 */
	public function registShipments($odIxArray = ''){

		if(is_array($odIxArray) && count($odIxArray) > 0){
			$odIxArr = array();
			$odIxArrayOri = $odIxArray;
			$cnt = 0;

			foreach($odIxArray as $od_ix):

				$sql = "select distinct od_ix from shop_delivery_overseas where od_ix = '".$od_ix."'";
				$this->db->query($sql);
				$od_ix_arr_row = $this->db->fetchall();

				$t_count++;

				if(count($od_ix_arr_row) > 0){
					foreach($od_ix_arr_row as $od_ix_arr){
						if($od_ix_arr['od_ix'] == $od_ix){
							array_push($odIxArr, $od_ix_arr['od_ix']);
							$f_count++;
						}
						$cnt++;
					}
				}
				//$s_count++;
				//$f_count++;
			endforeach;
		}

		/*
		print_r($odIxArray);
		print_r($odIxArr);
		exit;
		
		if($f_count > 0){
			$odIxArray = array_diff($odIxArray, $odIxArr);
		
			if(count($odIxArrayOri) == count($odIxArr)){
				$this->error['code'] = '500';
				$this->error['msg'] = '선택하신 상품은 디멘드쉽에 등록되어 있습니다.';
				$this->printError();
			}
		}
		*/


































		$action = '/api/v1/shipments';
		$requestBody = $this->makeAddItem($odIxArray);
        /*
        print_r($requestBody);
        exit;
        */

		if($this->is_message_display){
			echo "<br>requestBody start======<br>";
			$requestBody_print = $requestBody;
			echo "<pre>";print_r($requestBody_print);
			echo "<br>requestBody end======<br>";
		}

		$Result = $this->call(DEMANDSHIP_URL, $action, $requestBody);
        /*
		echo '<pre>';
		print_r($Result);
		echo '</pre>';
		exit;
        */
		if($this->is_message_display){
			echo "<br>전송결과 시작======<br>";
			print_r($Result);
			echo "<br>전송결과 종료======<br>";
		}

		//{"status":"success","shipments":[{"ds_id":"DSUS000030073","order_number":"201701121742-0000001","shipping_cost":11.1,"status":"Draft","consignee":{"name":"\uc774\ubbfc\ud574","address1":"\uc11c\uc6b8\ud2b9\ubcc4\uc2dc \uc11c\ucd08\uad6c \uc591\uc7ac\ub3d9 15-7 \uac74\uc601\ube4c\ub529","address2":"2\uce35 \ud3ec\ube44\uc988\ucf54\ub9ac\uc544","zip_code":"06744 ","country":"Malaysia"},"errors":["Zipcode invalid"]}]}
		// stdClass Object( [message] => 배송정보 등록 완료 - [DSUS000031898] / [resultCode] => success / [productNo] => / [sellprice] => )

		if(!empty($Result)){
			$Result = (array)json_decode($Result, true);
			//성공처리
			print_r($Result);
			#echo $Result['status'];
			#exit;

			$return = new resultData();

			for($i = 0; $i <= count($Result['shipments'])-1; $i++){
				unset($od_ix);
				if($Result['status'] == "success" && $Result['shipments'][$i]['status'] == 'New'){
					$result_detail = (array)$Result["shipments"];

					if(!empty($Result['shipments'][$i]['ds_id'])){
						$sql = "update shop_order_detail set quick='601', invoice_no='".$Result['shipments'][$i]['ds_id']."' where oid = '".$Result['shipments'][$i]['order_number']."'";
						$this->db->query($sql);

						// 쉬핑 코스트 우선 고정. 고쳐야함.
						$shipping_cost = $Result['shipments'][$i]['shipping_cost'];
						$country = $Result['shipments'][$i]['consignee']['country'];
						$status = $Result['shipments'][$i]['status'];
						$regdate = date('Y-m-d H:i:s');

						$sql = "select od_ix, order_from from shop_order_detail where oid = '".$Result['shipments'][$i]['order_number']."'";
						$this->db->query($sql);
						if($this->db->total){
							$od_ix_fetch = $this->db->fetchall();
							foreach($od_ix_fetch as $val){
								$od_ix = $val['od_ix'];
								//$od_ix .= $val['od_ix'];
								//$od_ix .= ',';

								$sql = "insert into shop_delivery_overseas (ds_id, oid, od_ix, shipping_cost, country, status, regdate, order_from) values('".$Result['shipments'][$i]['ds_id']."', '".$Result['shipments'][$i]['order_number']."', '".$od_ix."', ".$shipping_cost.", '".$country."', '".$status."', '".$regdate."', '".$val['order_from']."');";
								$this->db->query($sql);
							}
							//echo count($od_ix_fetch['od_ix']);
							//$od_ix = implode(",", $od_ix_fetch['od_ix']);
						}
					}

					$result->message = '배송정보 등록 완료 - ['.$Result['shipments'][$i]['ds_id'].']';
					$result->resultCode = '200';
					if($this->is_message_display){
						echo "type1:".$result_detail["ds_id"]."<br>";
					}

					$result->productNo = $result_detail["product_id"];//$Result['info']['goods_id']."|".$Result['info']['twitter_id'];
					$result->sellprice = $save_sellprice;
					//$result->product_id = $Result["product_id"];

					//$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
					/*
					if($co_pid == false){ // 최초 등록시에만 처리 
						$this->submitOptionRegistLog($pid,str_replace($pid."-","",$sku), $result_detail["product_id"], $result_detail["variant_id"], $result, $Result);
					}

					if($result_detail["product_id"]){
						$this->registVariants($pid, $result_detail["product_id"],"insert");
					}else{
						$this->registVariants($pid, $co_pid,"update");
					}
					*/

					//프론트 처리용 결과코드
					$resultCode = 'Success';
					//$result->resultCode = $resultCode;

					$this->submitDeliveryRegistLog($Result['shipments'][$i]['order_number'], $Result['shipments'][$i]['ds_id'], $resultCode, '');

				}else if($Result['status'] == "success" && $Result['shipments'][$i]['status'] == 'Draft'){

					if(!empty($Result['shipments'][$i]['errors'])){
						$resultCode = 'Draft';

						//$result->message = $Result['message']." pid = $pid ".$this->error ['msg'];
						//$result->resultCode = $resultCode;
						$this->submitDeliveryRegistLog($Result['shipments'][$i]['order_number'], $Result['shipments'][$i]['ds_id'], $resultCode, $Result['shipments'][$i]['errors']);

						/*
						$this->error ['code'] = '501';
						$this->error ['msg'] = '판매가가 없습니다. pid = ' . $pid;
						$this->printError();
						*/
					}

					$result->resultCode = '500';

				}else{
					$resultCode = 'Fail';
					$this->submitDeliveryRegistLog($Result['shipments'][$i]['order_number'], $Result['shipments'][$i]['ds_id'], $resultCode, $Result['shipments'][$i]['errors']);
					$result->resultCode = '500';
				}
			}
			return $result;
		}else{
			/*
			$Result = (array)json_decode($Result);
			$return = new resultData();
			$resultCode = 'fail';
			$return->message = "연동에 실패 하였습니다 - [".$Result['message']."] od_ix = $pid ".$this->error ['msg'];
			//$return->resultCode = "9999";
			$result->resultCode = $resultCode;
			$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
			*/
			$result->resultCode = '500';
			return $result;
		}
	}
	
	private function registVariants($pid , $product_id, $mode){
		$options = $this->makeOption( $pid , $pinfo );
		if(empty($pinfo)){
			$sql = "SELECT p.* , IFNULL(pc.color_name,'') as color_name FROM " . TBL_SHOP_PRODUCT . " p left join shop_product_color pc on p.color = pc.color_ix WHERE p.id = '" . $pid . "'";
			$this->db->query ( $sql );

			if ($this->db->total) {
				$pinfo = $this->db->fetch (); // 상품정보

			}
		}
		//옵션 관련 처리하기!!!
		$sql = "SELECT * FROM " . TBL_SHOP_PRODUCT_OPTIONS . " o WHERE o.pid='" . $pid . "' and option_kind in ('b','s','p') and o.option_use='1' order by opn_ix ";
		echo "<b>".nl2br($sql)."</b>";
		$this->db->query ( $sql );
		$options = $this->db->fetchall("object");

		$sql = "SELECT id as opnd_ix, global_odinfo, option_div, option_price, option_stock, option_etc1
				FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$options[0]["opn_ix"]."' and od.option_soldout in ('0','') order by id";
		echo "<b>".nl2br($sql)."</b>";
		$this->db->query ( $sql );
		$options_details = $this->db->fetchall('object');

		
		if(count($options_details) > 0){
			if($mode == "update"){
				for($i=0;$i<count($options_details);$i++){
					$this->VariantProcess($pid, $options, $options_details[$i], $pinfo, $product_id);
				}

			}else{
				for($i=1;$i<count($options_details);$i++){
					$this->VariantProcess($pid, $options, $options_details[$i], $pinfo, $product_id);
				}
			}
		}else{
			$this->VariantProcess($pid, $options, $options_details[$i], $pinfo, $product_id);
		}
	}
	
	private function VariantProcess($pid, $options, $options_detail, $pinfo, $product_id){

		if($options_detail[opnd_ix]){
			$sql = "select orr_ix , target_variant_id from sellertool_option_regist_relation where site_code='".$this->site_code."' and pid='$pid' and opnd_ix = '".$options_detail[opnd_ix]."' and target_variant_id != '' order by regist_date desc limit 0,1";
		}else{
			$sql = "select orr_ix , target_variant_id from sellertool_option_regist_relation where site_code='".$this->site_code."' and pid='".$pid."' and target_variant_id != ''   order by regist_date desc limit 0,1";
		}

		if($this->is_message_display){
			echo nl2br($sql)."<br><br>";
		}
				$this->db->query($sql);
				$this->db->fetch();
				if($this->db->total && $this->db->dt[target_variant_id]){
					
					$variants['id'] = $this->db->dt[target_variant_id];
					if($options_detail[opnd_ix]){
						$variants['sku'] = $options_detail[opnd_ix];
					}else{
						$variants['sku'] = $pinfo[id];
					}
					if($options_detail['option_div']){
						$variants['name'] = getGlobalTargetName($options_detail['option_div'],$options_detail['global_odinfo'],'option_div',$this->userInfo['language_code']);//
													//$options_detail['option_div'];//getGlobalTargetName($pinfo['pname'],$pinfo['global_pinfo'],'pname',$this->userInfo['language_code']);
					}else{
						//$variants['name'] = getGlobalTargetName($pinfo['pname'],$pinfo['global_pinfo'],'pname',$this->userInfo['language_code']);
						if($pinfo['brand_name']){
							$variants['name'] = "[".$pinfo['brand_name']."]".getGlobalTargetName($pinfo['pname'],$pinfo['global_pinfo'],'pname',$this->userInfo['language_code']);
						}else{
							$variants['name'] = getGlobalTargetName($pinfo['pname'],$pinfo['global_pinfo'],'pname',$this->userInfo['language_code']);
						}
					}
					$variants['color'] = $pinfo['color_name']; 

					$variants['size'] = $pinfo['size'];
					//echo "stock_use_yn:".$pinfo["stock_use_yn"];
					if($pinfo["stock_use_yn"] == "N"){
						$variants['inventory'] = 99999; /// 재고
					}else{
						if($options_detail[opnd_ix]){
							$variants['inventory'] = $options_detail[option_stock];
						}else{
							$variants['inventory'] = $pinfo[stock];
						}
					}

					//2016-04-20 Hong 임시 처리
					//$variants['inventory'] = 99999; /// 재고

					if($options_detail['option_price'] > 0){
						if($options[option_kind] == "b"){
							$variants['price'] = $this->BuyingServicePriceCalcurate($options_detail['option_price'], $this->buying_service_currencyinfo); // 옵션정보가 있을때 가격은 원상품의 가격과 합산되서 넘어옴 
							$variants['msrp'] = $this->BuyingServicePriceCalcurate($options_detail['option_price'], $this->buying_service_currencyinfo);//$options_detail['option_price'] * $this->currencyRate; 
						}else{
							$variants['price'] = $this->BuyingServicePriceCalcurate(($pinfo['sellprice']+$options_detail['option_price']) , $this->buying_service_currencyinfo);//($pinfo['sellprice']+$options_detail['option_price']) * $this->currencyRate ; // 옵션정보가 있을때 가격은 원상품의 가격과 합산되서 넘어옴 
							$variants['msrp'] = $this->BuyingServicePriceCalcurate(($pinfo['listprice']+$options_detail['option_price']) , $this->buying_service_currencyinfo);//($pinfo['listprice']+$options_detail['option_price']) * $this->currencyRate ;
						}
					}else{
						$variants['price'] = $this->BuyingServicePriceCalcurate($pinfo['sellprice'] , $this->buying_service_currencyinfo);//$pinfo['sellprice'] * $this->currencyRate;
						$variants['msrp'] = $this->BuyingServicePriceCalcurate($pinfo['listprice'] , $this->buying_service_currencyinfo); //$pinfo['listprice'] * $this->currencyRate;
					}

					//print_r($this->buying_service_currencyinfo);
					//print_r($this->buying_service_currencyinfo["nation_currency_info"]);
					$nation_currency_info = json_decode($this->buying_service_currencyinfo[nation_currency_info], true);
					$shippings = "";
					$countries = "";
					$shipping_time = "";
					$prices = "";
					$msrps = "";
					//print_r($nation_currency_info);
					
					if($this->buying_service_currencyinfo["shipping_caculation_type"] == 2 && false){
						$variants['shipping'] = "0";
					}else{
						$variants['shipping'] = $this->buying_service_currencyinfo["bs_basic_air_shipping"];
					}

					foreach($nation_currency_info["basic_air_shipping"] as $key => $val){
						if($nation_currency_info["is_selling"][$key]){
								if($val == 0){
									if($pinfo['p_weight'] <= 0.1 && $this->buying_service_currencyinfo["bs_100_air_shipping"] > 0){
										$shipping = $this->buying_service_currencyinfo["bs_100_air_shipping"];
									}else if($pinfo['p_weight'] <= 0.25 && $this->buying_service_currencyinfo["bs_250_air_shipping"] > 0){
										$shipping = $this->buying_service_currencyinfo["bs_250_air_shipping"];
									}else if($pinfo['p_weight'] > 0.25){
										$shipping = $this->buying_service_currencyinfo["bs_basic_air_shipping"];
									}else{
										$shipping = $this->buying_service_currencyinfo["bs_basic_air_shipping"];
									}
								}else{
									if($pinfo['p_weight'] <= 0.1){
										$shipping = $nation_currency_info["bs_100_air_shipping"][$key];
									}else if($pinfo['p_weight'] <= 0.25){
										$shipping = $nation_currency_info["bs_250_air_shipping"][$key];
									}else if($pinfo['p_weight'] > 0.25){
										$shipping = $val;
									}else{
										$shipping = $val;
									}									
								}
								if($pinfo['p_weight'] > 0.5){
									//echo "add_air_shipping[".$key."]:".$nation_currency_info["add_air_shipping"][$key]."<br>\n";
									if($nation_currency_info["add_air_shipping"][$key] > 0){
										$shipping += $nation_currency_info["add_air_shipping"][$key] * ceil(($pinfo['p_weight']-0.5)/0.5);	
									}else{
										$shipping += $this->buying_service_currencyinfo["bs_add_air_shipping"] * ceil(($pinfo['p_weight']-0.5)/0.5);	
									}
								}
								if($this->buying_service_currencyinfo["shipping_caculation_type"] == 2 && false){
									if($prices == ""){
										$prices = $variants['price']+$shipping;
									}else{
										$prices .= "|".($variants['price']+$shipping);
									}

									if($msrps == ""){
										$msrps = $variants['msrp']+$shipping;
									}else{
										$msrps .= "|".($variants['msrp']+$shipping);
									}

								}else{
									if($prices == ""){
										$prices = $variants['price'];
									}else{
										$prices .= "|".$variants['price'];
									}

									if($msrps == ""){
										$msrps = $variants['msrp'];
									}else{
										$msrps .= "|".$variants['msrp'];
									}
								}
								
								if($this->buying_service_currencyinfo["shipping_caculation_type"] == 2 && false){
									$shipping = "0";
								}

								if($shippings == ""){
									$shippings = $shipping;
								}else{
									$shippings .= "|".$shipping;
								}
								if($countries == ""){
									$countries = $key;
								}else{
									$countries .= "|".$key;
								}
								if($nation_currency_info["shipping_time"][$key] != ""){
									if($shipping_time == ""){
										$shipping_time = $nation_currency_info["shipping_time"][$key];
									}else{
										$shipping_time .= "|".$nation_currency_info["shipping_time"][$key];
									}
								}else{
									if($shipping_time == ""){
										$shipping_time = $this->buying_service_currencyinfo["shipping_time"];
									}else{
										$shipping_time .= "|".$this->buying_service_currencyinfo["shipping_time"];
									}
								}
						}
					}
					//echo $countries."<br>";
					//echo $shippings."<br>";

					$variants['prices'] = $prices;
					$variants['countries'] = $countries;
					$variants['shippings'] = $shippings;
					$variants['shipping_time'] = $this->buying_service_currencyinfo["shipping_time"];//$shipping_time;
					
					$variants['msrps'] = $msrps;

					$action = "/api/v2/variants/".$this->db->dt[target_variant_id]."/update";

				}else{
					$variants['product_id'] = $product_id;
					if($options_detail[opnd_ix]){
						$variants['sku'] = $options_detail[opnd_ix];
					}else{
						$variants['sku'] = $pinfo[id];
					}
					if($options_detail['option_div']){
						$variants['name'] =getGlobalTargetName($options_detail['option_div'],$options_detail['global_odinfo'],'option_div',$this->userInfo['language_code']);				
													//$options_detail['option_div'];//getGlobalTargetName($pinfo['pname'],$pinfo['global_pinfo'],'pname',$this->userInfo['language_code']);
					}else{
						 
						if($pinfo['brand_name']){
							$variants['name'] = "[".$pinfo['brand_name']."]".getGlobalTargetName($pinfo['pname'],$pinfo['global_pinfo'],'pname',$this->userInfo['language_code']);
						}else{
							$variants['name'] = getGlobalTargetName($pinfo['pname'],$pinfo['global_pinfo'],'pname',$this->userInfo['language_code']);
						}
					}
					$variants['color'] = $pinfo['color_name']; 

					$variants['size'] = $pinfo['size'];
					if($pinfo["stock_use_yn"] == "N"){
						$variants['inventory'] = 99999; /// 재고
					}else{
						if($options_detail[opnd_ix]){
							$variants['inventory'] = $options_detail[option_stock];
						}else{
							$variants['inventory'] = $pinfo[stock];
						}
					}

					//2016-04-20 Hong 임시 처리
					//$variants['inventory'] = 99999; /// 재고

					if($options_detail['option_price'] > 0){
						if($options[option_kind] == "b"){
							$variants['price'] = $this->BuyingServicePriceCalcurate($options_detail['option_price'], $this->buying_service_currencyinfo); // 옵션정보가 있을때 가격은 원상품의 가격과 합산되서 넘어옴 
							$variants['msrp'] = $this->BuyingServicePriceCalcurate($options_detail['option_price'], $this->buying_service_currencyinfo);//$options_detail['option_price'] * $this->currencyRate; 
						}else{
							$variants['price'] = $this->BuyingServicePriceCalcurate(($pinfo['sellprice']+$options_detail['option_price']) , $this->buying_service_currencyinfo);//($pinfo['sellprice']+$options_detail['option_price']) * $this->currencyRate ; // 옵션정보가 있을때 가격은 원상품의 가격과 합산되서 넘어옴 
							$variants['msrp'] = $this->BuyingServicePriceCalcurate(($pinfo['listprice']+$options_detail['option_price']) , $this->buying_service_currencyinfo);//($pinfo['listprice']+$options_detail['option_price']) * $this->currencyRate ;
						}
					}else{
						$variants['price'] = $this->BuyingServicePriceCalcurate($pinfo['sellprice'] , $this->buying_service_currencyinfo);//$pinfo['sellprice'] * $this->currencyRate;
						$variants['msrp'] = $this->BuyingServicePriceCalcurate($pinfo['listprice'] , $this->buying_service_currencyinfo); //$pinfo['listprice'] * $this->currencyRate;
					}

					$nation_currency_info = json_decode($this->buying_service_currencyinfo[nation_currency_info], true);
					$shippings = "";
					$countries = "";
					$shipping_time = "";
					$prices = "";
					$msrps = "";
					//print_r($nation_currency_info);
					if($this->buying_service_currencyinfo["shipping_caculation_type"] == 2 && false){
						$variants['shipping'] = "0";
					}else{
						$variants['shipping'] = $this->buying_service_currencyinfo["bs_basic_air_shipping"];
					}
					foreach($nation_currency_info["basic_air_shipping"] as $key => $val){
						if($nation_currency_info["is_selling"][$key]){
								/*
								if($val == 0){
									$shipping = $this->buying_service_currencyinfo["bs_basic_air_shipping"];
								}else{
									$shipping = $val;
								}
								*/
								if($val == 0){
									if($pinfo['p_weight'] <= 0.1 && $this->buying_service_currencyinfo["bs_100_air_shipping"] > 0){
										$shipping = $this->buying_service_currencyinfo["bs_100_air_shipping"];
									}else if($pinfo['p_weight'] <= 0.25 && $this->buying_service_currencyinfo["bs_250_air_shipping"] > 0){
										$shipping = $this->buying_service_currencyinfo["bs_250_air_shipping"];
									}else if($pinfo['p_weight'] > 0.25){
										$shipping = $this->buying_service_currencyinfo["bs_basic_air_shipping"];
									}else{
										$shipping = $this->buying_service_currencyinfo["bs_basic_air_shipping"];
									}
								}else{
									if($pinfo['p_weight'] <= 0.1){
										$shipping = $nation_currency_info["bs_100_air_shipping"][$key];
									}else if($pinfo['p_weight'] <= 0.25){
										$shipping = $nation_currency_info["bs_250_air_shipping"][$key];
									}else if($pinfo['p_weight'] > 0.25){
										$shipping = $val;
									}else{
										$shipping = $val;
									}									
								}

								if($pinfo['p_weight'] > 0.5){
									//echo "add_air_shipping[".$key."]:".$nation_currency_info["add_air_shipping"][$key]."<br>\n";
									if($nation_currency_info["add_air_shipping"][$key] > 0){
										$shipping += $nation_currency_info["add_air_shipping"][$key] * ceil(($pinfo['p_weight']-0.5)/0.5);	
									}else{
										$shipping += $this->buying_service_currencyinfo["bs_add_air_shipping"] * ceil(($pinfo['p_weight']-0.5)/0.5);	
									}
								}
								if($this->buying_service_currencyinfo["shipping_caculation_type"] == 2 && false){
									if($prices == ""){
										$prices = $variants['price']+$shipping;
									}else{
										$prices .= "|".($variants['price']+$shipping);
									}

									if($msrps == ""){
										$msrps = $variants['msrp']+$shipping;
									}else{
										$msrps .= "|".($variants['msrp']+$shipping);
									}

								}else{
									if($prices == ""){
										$prices = $variants['price'];
									}else{
										$prices .= "|".$variants['price'];
									}

									if($msrps == ""){
										$msrps = $variants['msrp'];
									}else{
										$msrps .= "|".$variants['msrp'];
									}
								}

								if($this->buying_service_currencyinfo["shipping_caculation_type"] == 2 && false){
									$shipping = "0";
								}

								if($shippings == ""){
									$shippings = $shipping;
								}else{
									$shippings .= "|".$shipping;
								}
								if($countries == ""){
									$countries = $key;
								}else{
									$countries .= "|".$key;
								}
								if($nation_currency_info["shipping_time"][$key] != ""){
									if($shipping_time == ""){
										$shipping_time = $nation_currency_info["shipping_time"][$key];
									}else{
										$shipping_time .= "|".$nation_currency_info["shipping_time"][$key];
									}
								}else{
									if($shipping_time == ""){
										$shipping_time = $this->buying_service_currencyinfo["shipping_time"];
									}else{
										$shipping_time .= "|".$this->buying_service_currencyinfo["shipping_time"];
									}
								}
						}
					}
					//echo $countries."<br>";
					//echo $shippings."<br>";

					$variants['prices'] = $prices;
					$variants['countries'] = $countries;
					$variants['shippings'] = $shippings;
					$variants['shipping_time'] = $this->buying_service_currencyinfo["shipping_time"];//$shipping_time;
					$variants['msrps'] = $msrps;

					$action = '/api/v2/variants/add';
				}
				$this->method = POST;
				$requestBody = json_encode($variants);
				if($this->is_message_display){
					echo "<pre><br>".$action."<br>";print_r($requestBody);
				}
				$Result = $this->call ( DEMANDSHIP_URL, $action, $requestBody );
				if($this->is_message_display){
					echo "<br>variants 전송결과 시작======<br>";
					print_r($Result);
				}
				$Result = (array)json_decode($Result, true);
				//$this->submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, print_r($Result, true));
				if($Result[code] == 0){
					$return = new resultData();
					$resultCode = 'success';
					$return->message = "등록성공 pid = ".$pid." : opnd_ix = ".$options_detail[opnd_ix]." ".$Result[message];
					//$return->resultCode = "9999";

					$result->sellprice = $variants['price'];
					$result->resultCode = $resultCode;
				}else{
					$return = new resultData();
					$resultCode = 'fail';
					$return->message = "등록실패 pid = ".$pid." : opnd_ix = ".$options_detail[opnd_ix]." ".$Result[message];
					//$return->resultCode = "9999";
					$result->resultCode = $resultCode;
				}
				$this->submitOptionRegistLog($pid,$options_detail[opnd_ix], $product_id, $Result["data"]["variant_id"], $result, $Result);
				if($this->is_message_display){
					echo "<br>variants 전송결과 종료======<br>";
				}
	}

	/**
	 * xml생성
	 *
	 * @param string $od_ix
	 */
	private function makeAddItem($odIxArray){

		// 수취인 배송정보
		$sql = sprintf("SELECT odd.*, sp.*, od.od_ix, od.co_oid, od.co_od_ix, od.order_from, od.pcnt,odd.msg, o.customs_clearance_number
						  FROM shop_order_detail od LEFT JOIN shop_order_detail_deliveryinfo odd ON (od.odd_ix = odd.odd_ix)
													LEFT JOIN shop_order o ON (o.oid = odd.oid)
												    LEFT JOIN shop_product sp ON (sp.id = od.pid)
					     WHERE od.od_ix in (%s)", implode(",", $odIxArray)) . " group by oid";
		$this->db->query($sql);

		//$i = 0;
		if ($this->db->total){
			$odInfos = $this->db->fetchall();
			//foreach($odInfos as $odInfo){
			for($i=0 ; $i < count($odInfos);$i++){
				$odInfo = $odInfos[$i];
				$addr1_arr = explode(",", $odInfo['addr1']);

				$sql = "SELECT minor_name, etc4
						  FROM minor_code 
						 WHERE major_cd = 'regional_code'
						 AND minor_name LIKE '%". trim($addr1_arr[1]) ."%' LIMIT 1";
				$this->db2->query($sql);
				if($this->db2->total){
					$regional_code = $this->db2->fetchall();

					if($odInfo['order_from'] == '11st_my'){

						// 11번가 말레이시아 addr1 형식 => 18300 Gua Musang, Kelantan
						$country_code = 'MY';
						$state_code = $regional_code[0]['etc4'];
						$state_name = $regional_code[0]['minor_name'];

						$city_arr = explode(" ", trim($addr1_arr[0]));

						$sql = "SELECT minor_name
								  FROM minor_code 
								 WHERE major_cd = 'city_code'
								 AND minor_name LIKE '%". trim($city_arr[1]) ."%' LIMIT 1";
						$this->db2->query($sql);
						if($this->db2->total){
							$city_code = $this->db2->fetchall();
							$city = $city_code[0]['minor_name'];
						}
						$zipcode = $city_arr[0];
						$addr1 = $odInfo['addr2'];
						$store_name = "11st Malaysia";
						$service_code = 'MY11';
						$tax_duty = 'DDP';
						$warehouse_code = 'WHICN';
						$currency_code = 'MYR';
					}
				}

				if($odInfo['order_from'] != '11st_my'){
					$country_code = 'KR';
					$zipcode = str_replace('-', '', $odInfo['zip']);
					$state_code = '';
					$state_name = '';
					$city = '';
					$addr1 = $odInfo['addr1'];
					$addr2 = $odInfo['addr2'];

					switch($odInfo['order_from']){
					case 'storefarm':
						$store_name = 'Naver Storefarm';
						break;
					case '11st':
						$store_name = '11st Korea';
						break;
					default:
						$store_name = 'Other';
						break;
					}
                    /*
					$pcc = $odInfo['customs_clearance_number'];
					$service_code = 'KR01';
					$tax_duty = 'DDU';
					$warehouse_code = 'WHLAX';
					$currency_code = 'KRW';
                    */
                    $pcc = $odInfo['customs_clearance_number'];
                    $country_code = 'JP';
                    $service_code = 'JP11';
                    $tax_duty = 'DDP';
                    $city = '藤沢市';
                    $state_code = ' Kanagawa';
                    $state_name = '神奈川県';
                    $warehouse_code = 'WHICN';
                    $currency_code = 'JPY';
				}

				//$weight														= $weight + $odInfo['product_weight'] * $odInfo['pcnt'];
				$orderInfos['shipments'][$i]['order_date']						= substr($odInfo['regdate'], 0, 10); // 주문일
				$orderInfos['shipments'][$i]['order_number']					= $odInfo['oid']; // 쇼핑몰 주문번호
				$orderInfos['shipments'][$i]['reference']						= $odInfo['msg']; // 운송자에 프린트되는 배송 요청사항 등
				$orderInfos['shipments'][$i]['tax_duty']						= $tax_duty; // $odInfo['']; // DDP: 발송인 세금 납부 (Demandship 에 카드정보가 저장된 이용자만 사용 가능), DDU: 수취인 세금 납부
				$orderInfos['shipments'][$i]['store_name']						= $store_name;

				////////////////////////////////// 합배송

				$orderInfos['shipments'][$i]['weight']							= $orderInfos['shipments'][$i]['weight'] + $odInfo['product_weight'] * $odInfo['pcnt'];
				$orderInfos['shipments'][$i]['height']							= $orderInfos['shipments'][$i]['height'] + $odInfo['product_height'] * $odInfo['pcnt'];

				$orderInfos['shipments'][$i]['width']							= $orderInfos['shipments'][$i]['width']  + $odInfo['product_width'] * $odInfo['pcnt'];
				$orderInfos['shipments'][$i]['length']							= $orderInfos['shipments'][$i]['length'] + $odInfo['product_depth'] * $odInfo['pcnt'];

				$orderInfos['shipments'][$i]['weight_unit']						= ($odInfo['unit'] == 'k' ? 'kg' : 'lb'); // $odInfo['']; // 무게단위 (kg, lb 중 선택 가능) ★
				$orderInfos['shipments'][$i]['dimension_unit']					= ($odInfo['unit'] == 'k' ? 'cm' : 'in'); // $odInfo['']; // 길이단위 (cm, in 중 선택 가능) ★
				$orderInfos['shipments'][$i]['currency_code']					= $currency_code; // $odInfo['']; // 통화 ISO 3자리 코드 사용 (KRW, USD, MYR, JPY, AUD, EUR) ★
				$orderInfos['shipments'][$i]['category']						= 'other'; // $odInfo['']; // 상품 카테고리
				$orderInfos['shipments'][$i]['warehouse_code']					= $warehouse_code; // $odInfo['']; // warehouse_code

				$orderInfos['shipments'][$i]['consignee']['name']				= $odInfo['r_first_name'] . $odInfo['r_last_name']; // 수취인명 ★
				$orderInfos['shipments'][$i]['consignee']['name_local']			= $odInfo['r_first_name'] . $odInfo['r_last_name'];
				$orderInfos['shipments'][$i]['consignee']['phone']				= str_replace('-', '', $odInfo['rmobile']); // 수취인 전화번호 ★
				$orderInfos['shipments'][$i]['consignee']['email']				= $odInfo['rmail']; // 'chory@forbiz.co.kr'; // $odInfo['rmail']; // 수취인 이메일
				$orderInfos['shipments'][$i]['consignee']['city']				= $city; // 'Kuala Lumpur'; // $odInfo['addr1']; // 시/군/구
				$orderInfos['shipments'][$i]['consignee']['state_code']			= $state_code; // 'KUL'; // $odInfo['']; // States of Malaysia (ex. JHR, KDH, KTN ...)
				$orderInfos['shipments'][$i]['consignee']['state_name']			= $state_name; // $odInfo['']; // 시/도
				$orderInfos['shipments'][$i]['consignee']['zip_code']			= $zipcode; // 우편번호 (한국은 5자리 새 우편번호만 이용 가능) ★
				$orderInfos['shipments'][$i]['consignee']['country_code']		= $country_code; // $odInfo['']; // 도착국가코드 (KR, MY) ★
				$orderInfos['shipments'][$i]['consignee']['address1']			= $addr1; // 수취인주소1 ★
				$orderInfos['shipments'][$i]['consignee']['address2']			= $addr2; // 수취인주소2

				if(strlen($pcc) > 0)
					$orderInfos['shipments'][$i]['consignee']['pcc']			= $pcc; // 개인통관고유부호

				$orderInfos['shipments'][$i]['consignee']['reference']			= $pcc; // 운송자에 프린트되는 배송 요청사항 등

				$orderInfos['shipments'][$i]['delivery']['service_code']		= $service_code; // KR01, MY01 ★


				$sql = "SELECT od.od_ix as od_ix, od.co_oid, od.co_od_ix, od.pid, od.order_from,od.pname, od.pid , od.psprice as sellprice , od.pcnt,
						p.product_weight, p.hscode, p.origin, p.global_pinfo, p.id
						FROM shop_order_detail od 
						left join shop_product p on od.pid = p.id 
						WHERE od.oid = '". $odInfo['oid'] ."' ";
				//echo $sql;
				//exit;
				$this->db->query($sql);
				$delivery_items = $this->db->fetchall();

				for($j=0 ; $j < count($delivery_items);$j++){
					$delivery_item = $delivery_items[$j];

					switch($delivery_item['origin']){
                        case "대한민국":
                            $origin = "KR";
                            break;
                    }

					if(in_array($delivery_item['od_ix'], $odIxArray)){
						//$sellprice = $delivery_item['sellprice'] * 4.41;
						$sellprice = $delivery_item['sellprice'];
						$pinfo = json_decode($delivery_item['global_pinfo'], true);

						$orderInfos['shipments'][$i]['items'][$j]['description']			= urldecode($pinfo["pname"]["english"]); // 상품명 ★
						$orderInfos['shipments'][$i]['items'][$j]['item_code']				= $delivery_item['pid']; // 상품코드
						$orderInfos['shipments'][$i]['items'][$j]['quantity']				= $delivery_item['pcnt']; // 2; // $odInfo['pcnt']; // 수량 ★
						$orderInfos['shipments'][$i]['items'][$j]['price']					= $sellprice; //$odInfo['dollar_price']; // 단가 (after dollar_price) ★
						$orderInfos['shipments'][$i]['items'][$j]['category']				= 'other'; // $odInfo['']; // 상품 카테고리
						$orderInfos['shipments'][$i]['items'][$j]['weight']					= $delivery_item['product_weight']; // 상품별 무게 ★
						//$orderInfos['shipments'][$i]['items'][$j]['origin_country_code']	= $delivery_item['origin'];	// 원산지
						$orderInfos['shipments'][$i]['items'][$j]['origin_country_code']	= 'KR';	// 원산지
						$orderInfos['shipments'][$i]['items'][$j]['hscode']					= $delivery_item['hscode']; // 'CODE123456'; // $odInfo['hscode']; // HSCODE
                        $orderInfos['shipments'][$i]['items'][$j]['reference']		        = $delivery_item['id']; // channel_reference_number
						// $orderInfos['shipments'][$i]['items'][$j]['sku']					= 'ABCDEFGHIJK'; // $odInfo[''];	// SKU
						// $orderInfos['shipments'][$i]['items'][$j]['composition']			= 'Blu-ray disk and soundtrack'; // $odInfo[''];	// 소재
						// $orderInfos['shipments'][$i]['items'][$j]['reference']				= 'Bestseller'; // $odInfo['']; // 운송자에 프린트되는 배송 요청사항 등
					}
				}
				//$i++;
			}
			//print_r($orderInfos);
			//exit;
			$jsonData = json_encode($orderInfos);
		}
		return $jsonData;
	}
	

	private function makeUpdateStatus( $pid , $goods_id , $twitter_id ) {
		$param = array();
		
		$param['goods_id'] = $goods_id;
		$param['twitter_id'] = $twitter_id;

		$sql = "SELECT * FROM " . TBL_SHOP_PRODUCT . " WHERE id = '" . $pid . "'";
		$this->db->query ( $sql );
		$pinfo = $this->db->fetch (); // 상품정보
		
		//$param['up_status'] 上下架状态（1-上架，2-下架）。
		if ($this->db->total) {
			if($pinfo ['disp']!='0'){
				if($pinfo ['state']=='1')			$param['up_status'] = "1";
				elseif($pinfo ['state']=='0')		$param['up_status'] = "2";
				else								$param['up_status'] = "2";
			}else{
				$param['up_status'] = "2";
			}
		}else{
			$param['up_status'] = "2";
		}

		$return = '';
		foreach ($param as $k => $v) {
			$return .= "$k=" . urlencode($v) . "&";
		}

		return $return;
	}

	private function makeUpdatePrice( $pid , $goods_id , $twitter_id ) {
		$param = array();
		
		$param['goods_code'] = $goods_id;
		$param['twitter_id'] = $twitter_id;
		
		$sql = "SELECT * FROM " . TBL_SHOP_PRODUCT . " WHERE id = '" . $pid . "'";
		$this->db->query ( $sql );
		$pinfo = $this->db->fetch (); // 상품정보
		
		
		$options = $this->makeOption( $pid , $pinfo );

		if( $options['max_price'] > $pinfo['sellprice'] ){
			$pinfo['sellprice'] = $options['max_price'];
		}
		
		$pinfo['sellprice'] = $pinfo['sellprice'] * $this->currencyRate;
		
		$param['sku_price'] = $pinfo['sellprice'];

		if(!($pinfo ['sellprice'] > 0)){
			$this->error ['code'] = '2002';
			$this->error ['msg'] = '판매가가 없습니다. pid = ' . $pid;
			$this->printError ();
		}

		$return = '';
		foreach ($param as $k => $v) {
			$return .= "$k=" . urlencode($v) . "&";
		}

		return $return;
	}

	/**
     * 아이소다 워터마크 없는 이미지로 교체하기
     * 
     * @return {html} 이미지 교체된 상세내용
     */
    function changeClearImage($product_info){
        global $layout_config;
        $uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$layout_config["mall_data_root"]."/images/product", $product_info["id"], 'Y');
        $down_dir = MD5("FORBIZ".$product_info["id"]);
        $filepath = $layout_config["mall_data_root"]."/images/product".$uploaddir."/download/".$down_dir."/".$product_info["download_desc"];
        $real_filepath = $_SERVER["DOCUMENT_ROOT"].$layout_config["mall_data_root"]."/images/product".$uploaddir."/download/".$down_dir."/".$product_info["download_desc"];
        if(file_exists($real_filepath)){
            $return_str = "<P align=center><IMG id=wyditor_img border=0 src='http://".$_SESSION[admin_config][mall_domain].$filepath."'><BR></P>";
            return $return_str;
        }else{
            return NULL;
        }
        
    }

	/**
	 * 상품고시 xml
	 *
	 * @param unknown $insertId
	 * @param unknown $pid
	 */
	 
	private function returnGoodsDisplayInfo($pid) {
		$sql = "SELECT
            		dp_title , dp_desc
            	FROM 
					shop_product_displayinfo
        		WHERE
            		pid = '" . $pid . "'
            	AND dp_use ='1' ";
		$this->db->query ( $sql );
		if ($this->db->total) {
			return $this->db->fetchall ();
		} else {
			return array();
		}
	}

	/**
	 * 옵션 등록
	 *
	 * @param unknown $insertId        	
	 * @param unknown $pid        	
	 */
	 /*
	private function registOption($insertId, $pid) {
		$action = 'http://www.ensogo.co.kr/APIv1/ShoppingService/ReviseItemStock';
		$requestXmlBody = $this->makeOptionXml ( $insertId, $pid );
		$addOptionResult = $this->call ( SHOPPING, $action, $requestXmlBody );
		
		if( ! empty ($addItemResult->Body->Fault)){
			//fail 처리
			$return = new resultData();
			$resultCode = 'fail';
			$return->message = $addItemResult->Body->Fault->faultstring;
			$return->resultCode = $addItemResult->Body->Fault->faultcode;
			
			return $return;
		}else{
			$itemId = $addOptionResult->Body->ReviseItemStockResponse->ReviseItemStockResult->ItemStock->attributes()->ItemID;
			
			if (! empty ( $itemId )) {
				// ReviseItemStock(옵션등록) success 
			
				return (string)$itemId;
			} else {
				// fail 
				$this->error ['code'] = '2005';
				$this->error ['msg'] = '옵션 등록 실패';
				$this->printError ();
			}
		}
	}
	*/
	private function imgUploade( $url , $resize = false , $pid='' , $image_upload_type = "main"){

		if( $resize ){

			$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/product", $pid, 'Y');
			if($image_upload_type == "main"){
				$image_info = getimagesize ($url);
				$tmp_image_type = explode('/',$image_info['mime']);
				$image_type = $tmp_image_type[1];
				
				if ( ! file_exists($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/product".$uploaddir."/640_900_".$pid.".gif")){
					
					if($image_type == "gif" || $image_type == "GIF"){
						MirrorGif($url, $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/product".$uploaddir."/640_900_".$pid.".gif", MIRROR_NONE);
						resize_gif($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/product".$uploaddir."/640_900_".$pid.".gif",640,900,'C');
					}else if($image_type == "png" || $image_type == "PNG"){
						MirrorPNG($url, $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/product".$uploaddir."/640_900_".$pid.".gif", MIRROR_NONE);
						resize_png($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/product".$uploaddir."/640_900_".$pid.".gif",640,900,'C');
					}else{
						Mirror($url, $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/product".$uploaddir."/640_900_".$pid.".gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/product".$uploaddir."/640_900_".$pid.".gif",640,900,'C');
					}
				}

				$url = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/product".$uploaddir."/640_900_".$pid.".gif";
			}else{
				$image_info = getimagesize ($url);
				$tmp_image_type = explode('/',$image_info['mime']);
				$image_type = $tmp_image_type[1];
				if($image_info[0] > $image_info[1]){
					$image_resize_type = "W";
				}else{
					$image_resize_type = "H";
				}
				if($image_info[0] < 640 || $image_info[1] < 900 ){
					$resize_url = $_SERVER["DOCUMENT_ROOT"].str_replace("http://".$_SERVER["HTTP_HOST"], "", $url);
					if($image_type == "gif" || $image_type == "GIF"){ 
						resize_gif($resize_url,640,900, $image_resize_type);
					}else if($image_type == "png" || $image_type == "PNG"){ 
						resize_png($resize_url,640,900, $image_resize_type);
					}else{ 
						resize_jpg($resize_url,640,900, $image_resize_type);
					}
				}
			}

			$image_info = getimagesize ($url);
			$tmp_image_type = explode('/',$image_info['mime']);
			$image_type = $tmp_image_type[1];

		}else{
			$image_info = @getimagesize ($url);
			$tmp_image_type = explode('/',$image_info['mime']);
			$image_type = $tmp_image_type[1];
		}
		
		if($image_info[0] >= 640 && $image_info[1] >= 900 ){
			//echo $url."<br/>";
			$pic = file_get_contents( $url );
			$picByte = base64_encode($pic);
			$param = array();

			$param['image'] = $picByte;
			$param['ext'] = $image_type;
			
			//echo print_r($param)."<br/>";

			$o = '';
			foreach ($param as $k => $v) {
				$o .= "$k=" . urlencode($v) . "&";       //默认UTF-8编码格式
			}

			$result = $this->call ( DEMANDSHIP_URL, '/image/upload' . $this->vcodeCoUrl , $o );
			return $result['info']['pic_uri'];
		}
	}
	/**
     * 아이소다 옵션구성하기!
     * 
     * @return array
     */
	 
	private function makeOption( $pid , $pinfo="" ){
		if(empty($pinfo)){
			#$sql = "SELECT p.* , IFNULL(pc.color_name,'') as color_name FROM " . TBL_SHOP_PRODUCT . " p left join shop_product_color pc on p.color = pc.color_ix WHERE p.id = '" . $pid . "'";
			$sql = "SELECT p.* FROM " . TBL_SHOP_PRODUCT . " p WHERE p.id = '" . $pid . "'";
			$this->db->query ( $sql );

			if ($this->db->total) {
				$pinfo = $this->db->fetch (); // 상품정보

			}
		}
		//옵션 관련 처리하기!!!
		$sql = "SELECT * FROM " . TBL_SHOP_PRODUCT_OPTIONS . " o WHERE o.pid='" . $pid . "' and option_kind in ('b','s','p') and o.option_use='1' order by opn_ix ";
		$this->db->query ( $sql );
		
		if($this->db->total){
			$cnt=0;
			$options = array();
			$tmp_options=array();
			$_options_ = $this->db->fetchall("object");
	
			for($i=0;$i<count($_options_);$i++){
				
				//$options[$cnt]['option_name'] = ( ! empty($_options_[$i]['option_name']) ? $_options_[$i]['option_name']: "颜色" );
				$options[$cnt]['option_name'] = "颜色";

				$sql = "SELECT id, global_odinfo, option_div, option_price, option_stock, option_etc1
				FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' and od.option_soldout in ('0','') order by id";
				$this->db->query ( $sql );
				$tmp_options = $this->db->fetchall('object');

				for($j=0;$j<count($tmp_options);$j++){

					$options[$cnt]['option_id'][] = $tmp_options[$j]['id'];

					$tmp_options[$j]['option_div'] = getGlobalTargetName($tmp_options[$j]['option_div'],$tmp_options[$j]['global_odinfo'],'option_div',$this->userInfo['language_code']);

					$options[$cnt]['option_div'][] = $tmp_options[$j]['option_div'];

					if( $_options_[$i]["option_kind"] == "b" ){
						$options[$cnt]['option_price'][] = $tmp_options[$j]['option_price'];
					}else{
						$options[$cnt]['option_price'][] = $pinfo['sellprice'] + $tmp_options[$j]['option_price'];
					}
					
					list($option_size,$option_stock) = explode( "|", $tmp_options[$j]['option_etc1']);
					$options[$cnt]['option_size'] = $option_size;
					$options[$cnt]['option_stock'][] = $tmp_options[$j]['option_stock'];
				}

				$cnt++;
			}
		}else{
			$options[0]['option_name'] = "";
			$options[0]['option_id'][0] = "";

			if( $pinfo['stock_use_yn'] == 'Y' ){
				$option_stock = (int)($pinfo['stock']);
			}else{
				$option_stock = "9999";
			}
			$options[0]['option_price'][0] = $pinfo['sellprice'];
			$options[0]['option_div'][0] = "";
			$options[$cnt]['option_size'] = '';
			$options[$cnt]['option_stock'][] = (int)$option_stock;
		}
		return $options;
		exit;
		$total_stock = 0;
		$option = array();

		$option['max_option_price'] = 0;


		if(is_array($options)){
			foreach( $options as $ops ):
				if(is_array($ops['option_price'])){
					foreach( $ops['option_price'] as $op ):
						if( $ops['max_option_price'] < $op){
							$ops['max_option_price'] = $op;
						}
					endforeach;
				}

				$option['sku_properties'][] = $ops['option_name'] . ":" . implode( '^' , $ops['option_div'] );
				$option['sku_properties'][] = "尺码" . ":" .str_replace( ',', '^' , $ops['option_size'] ); 
				if(is_array($ops['option_id'])){
					foreach( $ops['option_id'] as $option_id ):
						//$option['sku_style_no'][] = $option_id . str_repeat( '^' . $option_id , count(explode(',', $ops['option_size'])) - 1);
						$option_id_str="";
						for($i=0; $i < count(explode(',', $ops['option_size']));$i++){
							$option_id_str .= '^' . $option_id . "_" . $i;
						}
						$option['sku_style_no'][] = substr($option_id_str,1);
					endforeach;
				}
				if(is_array($ops['option_stock'])){
					foreach( $ops['option_stock'] as $stock ):
						if(substr_count($stock, ",")){
							$option['sku_stocks'][] = str_replace( ',', '^' , $stock );
						}else{
							$option['sku_stocks'][] = "99999";
						}
					endforeach;
				}

			endforeach;
		}

		$return['max_price'] = $option['max_option_price'];
		$return['sku_properties'] = implode( "|" , $option['sku_properties'] );
		$return['sku_stocks'] = implode( "|" , $option['sku_stocks'] );
		$return['sku_style_no'] = implode( "|" , $option['sku_style_no'] );

		return $return;
	}

	/*
	private function makeOption( $pid , $pinfo ){

		//옵션 관련 처리하기!!!
		//추가구성상품은 X
		$sql = "SELECT * FROM " . TBL_SHOP_PRODUCT_OPTIONS . " o WHERE o.pid='" . $pid . "' and option_kind in ('b','s','p') and o.option_use='1' order by opn_ix ";
		$this->db->query ( $sql );
		
		if($this->db->total){
			$cnt=0;
			$options = array();
			$tmp_options=array();
			$_options_ = $this->db->fetchall("object");
	
			for($i=0;$i<count($_options_);$i++){
				
				$options[$cnt]['option_name'] = ( ! empty($_options_[$i]['option_name']) ? $_options_[$i]['option_name']: "옵션" );

				$sql = "SELECT id, option_div, option_price, option_stock 
				FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' and od.option_soldout in ('0','') order by id";
				$this->db->query ( $sql );
				$tmp_options = $this->db->fetchall('object');

				for($j=0;$j<count($tmp_options);$j++){

					$options[$cnt]['option_id'][] = $tmp_options[$j]['id'];
					$options[$cnt]['option_div'][] = $tmp_options[$j]['option_div'];

					if( $_options_[$i]["option_kind"] == "b" ){
						$options[$cnt]['option_stock'][] = (int)$tmp_options[$j]['option_stock'];
						$options[$cnt]['option_price'][] = $tmp_options[$j]['option_price'];
					}else{
						$options[$cnt]['option_stock'][] = "9999";
						$options[$cnt]['option_price'][] = $pinfo['sellprice'] + $tmp_options[$j]['option_price'];
						
					}
				}

				$cnt++;
			}
		}else{
			$options[0]['option_name'] = "옵션";
			$options[0]['option_id'][0] = "1";

			if( $pinfo['stock_use_yn'] == 'Y' ){
				$options[0]['option_stock'][0] = (int)($pinfo['stock']);
			}else{
				$options[0]['option_stock'][0] = "9999";
			}
			$options[0]['option_price'][0] = $pinfo['sellprice'];
			$options[0]['option_div'][0] = "FREE";
		}
		
		$total_stock = 0;
		$option = array();

		$option['max_option_price'] = 0;
		foreach( $options as $ops ):
			foreach( $ops['option_price'] as $op ):
				if( $ops['max_option_price'] < $op){
					$ops['max_option_price'] = $op;
				}
			endforeach;
			$option['sku_properties'][] = $ops['option_name'] . ":" . implode( '^' , $ops['option_div'] );
			$option['sku_stocks'][] = implode( '^' , $ops['option_stock'] );
			$option['sku_style_no'][] = implode( '^' , $ops['option_id'] );
			//$option['sku_stocks'] = implode( '^' , $ops['option_stock'] );
			//$option['sku_style_no'] = implode( '^' , $ops['option_id'] );
		endforeach;

		$return['max_price'] = $option['max_option_price'];
		$return['sku_properties'] = implode( "|" , $option['sku_properties'] );
		$return['sku_stocks'] = implode( "|" , $option['sku_stocks'] );
		$return['sku_style_no'] = implode( "|" , $option['sku_style_no'] );
		//$return['sku_stocks'] = $option['sku_stocks'];
		//$return['sku_style_no'] = $option['sku_style_no'];
		return $return;
	}
	*/

	/*
	function makeOption( $pid , $pinfo ){
		
		$gep_price = 0;

		//옵션 관련 처리하기!!!
		//추가구성상품은 X
		$sql = "SELECT * FROM " . TBL_SHOP_PRODUCT_OPTIONS . " o WHERE o.pid='" . $pid . "' and option_kind in ('b','s','p') and o.option_use='1' order by opn_ix ";
		$this->db->query ( $sql );
		
		if($this->db->total){
			
			$options = array();
				
			$cnt=0;
			$tmp_options=array();
			$stock_tmp_options=array();
			$optoin_stock_use_yn=false;
			
			$_options_ = $this->db->fetchall("object");

			for($i=0;$i<count($_options_);$i++){

				if($_options_[$i]['option_kind']=="b"){
					$sql = "SELECT id, option_div, option_stock , option_price FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' and od.option_soldout in ('0','') order by id";
					$this->db->query ( $sql );
					$stock_tmp_options[] = $this->db->fetchall('object');
				}else{
					$sql = "SELECT id, '".$_options_[$i]['option_kind']."' as option_kind, option_div, option_stock, option_price FROM ". TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od  WHERE od.opn_ix='".$_options_[$i]["opn_ix"]."' and od.option_soldout in ('0','') order by id";
					$this->db->query ( $sql );
					$tmp_options[] = $this->db->fetchall('object');
				}

			}

			if(count($tmp_options)>2){
				for($i=0;$i<count($tmp_options[0]);$i++){
					for($j=0;$j<count($tmp_options[1]);$j++){
						for($k=0;$k<count($tmp_options[2]);$k++){
							$options[$cnt]['id'] = $tmp_options[0][$i]['id'];
							$options[$cnt]['option_stock'] = "9999";
							$options[$cnt]['option_price'] = ($tmp_options[0][$i]['option_price']+$tmp_options[1][$j]['option_price']+$tmp_options[2][$k]['option_price']) + $gep_price;

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
								$options[$cnt]['option_div'] = implode("_",$tmp_option_div);
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
						$options[$cnt]['id'] = $tmp_options[0][$i]['id'];
						$options[$cnt]['option_stock'] = "9999";
						$options[$cnt]['option_price'] = ($tmp_options[0][$i]['option_price']+$tmp_options[1][$j]['option_price']) + $gep_price;

						$tmp_option_div=array();
						if($tmp_options[0][$i]['option_div']!=""){
							$tmp_option_div[]=$tmp_options[0][$i]['option_div'];
						}
						if($tmp_options[1][$j]['option_div']!=""){
							$tmp_option_div[]=$tmp_options[1][$j]['option_div'];
						}

						if(count($tmp_option_div)>0){
							$options[$cnt]['option_div'] = implode("_",$tmp_option_div);
						}else{
							$options[$cnt]['option_div'] = "옵션선택안함";
						}

						$cnt++;
					}
				}
			}else{
				for($i=0;$i<count($tmp_options[0]);$i++){
					$options[$cnt]['id'] =$tmp_options[0][$i]['id'];
					$options[$cnt]['option_stock'] = "9999";
					$options[$cnt]['option_price'] = $tmp_options[0][$i]['option_price'] + $gep_price;
					$options[$cnt]['option_div'] = $tmp_options[0][$i]['option_div'];
					$cnt++;
				}
			}

			if(count($stock_tmp_options)>0){
				$optoin_stock_use_yn=true;
				for($i=0;$i<count($stock_tmp_options[0]);$i++){
					$options[$cnt]['id'] = $stock_tmp_options[0][$i]['id'];
					if( ($stock_tmp_options[0][$i]['option_stock'] - $subtract_stock) > 0){
						$options[$cnt]['option_stock'] = ($stock_tmp_options[0][$i]['option_stock'] - $subtract_stock);
					}else{
						$options[$cnt]['option_stock'] = 0;
					}
					//가격+재고옵션 가격은 최저가를 가지고 sellprice 를 변경시켜야 한다
					$options[$cnt]['option_price'] = $stock_tmp_options[0][$i]['option_price']- $pinfo ['sellprice'];
					$options[$cnt]['option_div'] = $stock_tmp_options[0][$i]['option_div'];
					$cnt++;
				}
			}
		}else{
			$options[0]['id'] = "1";
			if( $pinfo['stock_use_yn']=='Y' ){
				$options[0]['option_stock'] = $pinfo['stock'];
			}else{
				$options[0]['option_stock'] = "9999";
			}
			$options[0]['option_price'] = "0";
			$options[0]['option_div'] = "FREE";
		}
		
		$total_stock = 0;
		$option = array();
		
		$option['max_price'] = 0;
		foreach( $options as $o ):
			
			$max_price = ($o['option_price'] +  $pinfo ['sellprice']);
			if( $option['max_price'] < $max_price ){
				$option['max_price'] = $max_price;
			}

			$option['sku_properties'][] = $o['option_div'];
			$option['sku_stocks'][] = $o['option_stock'];
			$option['sku_style_no'][] = $o['id'];

		endforeach;

		$return['max_price'] = $option['max_price'];
		$return['sku_properties'] = 'OPTION:'.implode( "|" , $option['sku_properties'] );
		$return['sku_stocks'] = implode( "^" , $option['sku_stocks'] );
		$return['sku_style_no'] = implode( "^" , $option['sku_style_no'] );

		return $return;
	}
	*/

	
	/**
	 * 옵션 상세정보 가져오기
	 *
	 * @param string $pid
	 * @param string $opn_ix
	 * @return object
	 */
	 /*
	private function get_option_detail($pid, $opn_ix) {
		$result = NULL;
		$sql = "SELECT
        		 *
        		FROM " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . "
        		WHERE
        			pid = '" . $pid . "'
        		AND opn_ix = '" . $opn_ix . "' ";
		
		$this->db->query ( $sql );
		$result = $this->db->fetchAll ();
		
		return $result;
	}
	*/
	/**
	 * 상품정보제공고시 등록
	 */
	 /*
	private function registOfficialNotice( $insertId, $pid ){
		$action = 'http://www.ensogo.co.kr/APIv1/ShoppingService/AddOfficialNotice';
		$requestXmlBody = $this->makeOfficialXml ( $insertId, $pid );
		$AddOfficialNoticeResult = $this->call ( SHOPPING, $action, $requestXmlBody );
		
		if( ! empty ($addItemResult->Body->Fault)){
			//fail 처리
			$return = new resultData();
			$resultCode = 'fail';
			$return->message = $addItemResult->Body->Fault->faultstring;
			$return->resultCode = $addItemResult->Body->Fault->faultcode;
			
			return $return;
		}else{
			
			$itemId = $AddOfficialNoticeResult->Body->AddOfficialNoticeResponse->AddOfficialNoticeResult->attributes ()->ItemID;
			
			if (! empty ( $itemId )) {
				// AddOfficialNotice success 
				return (string)$itemId;
			} else {
				// fail 
				$this->error ['code'] = '2005';
				$this->error ['msg'] = '상품정보제공고시 등록 실패';
				$this->printError ();
			}	
		}
	}
	*/
	/**
	 * 상품정보제공고시 XML 생성
	 */
	 /*
	private function makeOfficialXml( $insertId, $pid ){
		$xml = '<?xml version="1.0" encoding="utf-8"?>
					<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
					  <soap:Header>
					    <EncryptedTicket xmlns="http://www.ensogo.co.kr/Security">
					      <Value>' . $this->ticket . '</Value>
					    </EncryptedTicket>
					  </soap:Header>
					  <soap:Body>
					    <AddOfficialNotice xmlns="http://www.ensogo.co.kr/APIv1/ShoppingService">
					      <req Version="1">
					        <MemberTicket xmlns="http://schema.ensogo.co.kr/Arche.Sell3.Service.xsd">
					          <Ticket xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd"></Ticket>
					        </MemberTicket>
					        <ItemOfficialNotice xmlns="http://schema.ensogo.co.kr/Arche.Sell3.Service.xsd">
					          <ItemID xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd">' . $insertId . '</ItemID>
					          <NotiItemGroupNo xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd">1</NotiItemGroupNo>
					          <ItemOfficialNotiValue NotiItemCode="1-1" NotiItemValue="11" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="1-2" NotiItemValue="22" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="1-3" NotiItemValue="33" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="1-4" NotiItemValue="44" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="1-5" NotiItemValue="55" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="1-6" NotiItemValue="66" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="1-7" NotiItemValue="77" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="1-8" NotiItemValue="88" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="1-9" NotiItemValue="99" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="1-10" NotiItemValue="1010" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="999-1" NotiItemValue="거래정보1" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="999-2" NotiItemValue="거래정보2" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="999-3" NotiItemValue="거래정보3" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
							  <ItemOfficialNotiValue NotiItemCode="999-4" NotiItemValue="거래정보4" ExtraMarkIs="false" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd" />
					        </ItemOfficialNotice>
					      </req>
					    </AddOfficialNotice>
					  </soap:Body>
					</soap:Envelope>';
		return $xml;
	}
	*/
	/**
	 * 판매정보 등록
	 *
	 * @param unknown $insertId        	
	 * @param unknown $pid        	
	 */
	 /*
	private function registSelling($insertId, $pid) {
		$action = 'http://www.ensogo.co.kr/APIv1/ShoppingService/ReviseItemSelling';
		$requestXmlBody = $this->makeSellingXml ( $insertId, $pid );
		$registSellingResult = $this->call ( SHOPPING, $action, $requestXmlBody );
		
		if( ! empty ($addItemResult->Body->Fault)){
			//fail 처리
			$return = new resultData();
			$resultCode = 'fail';
			$return->message = $addItemResult->Body->Fault->faultstring;
			$return->resultCode = $addItemResult->Body->Fault->faultcode;
			
			return $return;

		}else{
			//success처리
			return 'success';
		}
		
	}
	*/
	/*
	public function makeSellingXml($insertId = '',$pid = ''){
		$xml = '<?xml version="1.0" encoding="utf-8"?>
					<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
								xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
						<soap:Header>
							<EncryptedTicket xmlns="http://www.ensogo.co.kr/Security">
								<Value>'.$this->ticket.'</Value>
							</EncryptedTicket>
						</soap:Header>
						<soap:Body>
							<ReviseItemSelling xmlns="http://www.ensogo.co.kr/APIv1/ShoppingService">
								<req Version="1">
									<ItemSelling ItemID="'.$insertId.'" xmlns="http://schema.ensogo.co.kr/Arche.Sell3.Service.xsd">
										<Period Status="OnSale" xmlns="http://schema.ensogo.co.kr/Arche.Service.xsd">
										<Period ApplyPeriod="15" ApplyDate="'.date('Y-m-d').'"/>
										</Period>
									</ItemSelling>
								</req>
							</ReviseItemSelling>
						</soap:Body>
					</soap:Envelope>';
		return $xml;
	}
	*/

	/**
	 * 연계된 카테고리 구하기
	 *
	 * @param string $pid        	
	 * @return array NULL
	 */

	private function getTargetCategory($pid = '') {
		$sql = "SELECT cid 
            		FROM " . TBL_SHOP_PRODUCT_RELATION . " 
            		WHERE pid = '$pid'";
		$this->db->query ( $sql );
		if ($this->db->total) {
			$cid_list = $this->db->fetchall ();
			
			$cid_mapping_check = FALSE;
			foreach ( $cid_list as $cl ) :
				/*
				$sql = "SELECT
	                		target_cid,
	                		target_name
	                	FROM sellertool_category_linked_relation
	                	WHERE
	                		origin_cid = '" . $cl ['cid'] . "'
	                	AND site_code = '" . $this->site_code . "'";
				*/
				
				$sql = "SELECT
	                		target_cid,
	                		target_name
	                	FROM sellertool_category_linked_relation
	                	WHERE
	                		origin_cid = '" . $cl ['cid'] . "'
	                	AND api_key = '" . $this->api_key . "'";
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
	
	/**
	 * 카테고리 가이드 가지고오기
	 *
	 * @param string $target_cid        	
	 * @return array NULL
	 */

	private function getCategoryGuide($target_cid) {

		$sql = "SELECT
					lr.clr_ix, lr.origin_cate_guide, lr.target_cate_guide, rc.parent_no
				FROM 
					sellertool_category_linked_relation lr 
					left join sellertool_received_category rc on (lr.target_cid=rc.disp_no)
				WHERE
					target_cid = '" . $target_cid . "'
				AND api_key = '" . $this->api_key . "'";

		$this->db->query ( $sql );
		$this->db->fetch ();
		if (!empty($this->db->dt['origin_cate_guide'])) {
			return array('clr_ix'=>$this->db->dt['clr_ix'],'origin_cate_guide'=>$this->db->dt['origin_cate_guide'],'target_cate_guide'=>$this->db->dt['target_cate_guide']);
		}else{
			if(!empty($this->db->dt['parent_no'])){
				return $this->getCategoryGuide($this->db->dt['parent_no']);
			}else{
				return false;
			}
		}
	}

	/**
	 * 택배사 코드 목록
	 *
	 * @return multitype:string
	 */
	 /*
	public function getDeleveryCompanyCodeList() {
		return array (
				'ajutb' => '아주택배',
				'bellexpress' => '(주)벨익스프레스',
				'chonil' => '천일택배',
				'cjgls' => 'CJ GLS택배',
				'daesin' => '대신택배',
				'dongbu' => '동부익스프레스택배',
				'epost' => '우체국택배',
				'etc' => '기타',
				'gmgls' => '굳모닝',
				'gtx' => 'GTX택배',
				'hanaro' => '하나로로지스',
				'hanjin' => '한진택배',
				'hapdong' => '합동택배',
				'hth' => '삼성HTH택배',
				'hyundai' => '현대택배',
				'ilyang' => '일양로지스',
				'innogis' => '이노지스택배',
				'kgb' => '로젠택배',
				'kgbls' => 'KGB택배',
				'kgbsl' => '이젠택배',
				'korex' => '대한통운택배',
				'ktlogistics' => 'KT로지스',
				'kyungdong' => '경동택배',
				'nedex' => '네덱스' 
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
           			site_code = 'demandship' ";
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
	 public function submitRegistLog($pid,$add_info_id,$target_cid,$target_name,$result, $response_text=""){
        $db = new MySQL;
        
        $_message = $result->message;
        $_productNo = $result->productNo;
		$_target_sellprice = $result->sellprice;
        if($result->resultCode == "fail"){
			$_resultCode = "500";
		}else{
			$_resultCode = $result->resultCode;
		}
		
		/*
        //모든 log 넣는곳
        $sql = "insert into sellertool_log (site_code, type, pid, add_info_id, target_cid, target_name, result_pno, result_msg, result_code, regist_date)values('goodbuyselly','regist','$pid','$add_info_id','$target_cid','$target_name','$_productNo','$_message','$_resultCode',NOW())";
        
        $db->query($sql);
        
        // 제휴사연동 릴레이션테이블에 추가
        //if($_resultCode == '200'){
            $sql = "insert into sellertool_regist_relation (site_code, pid, add_info_id, target_cid, target_name, result_pno, result_msg, result_code, regist_date)values('goodbuyselly','$pid','$add_info_id','$target_cid','$target_name','$_productNo','$_message','$_resultCode',NOW())"; 
            $db->query($sql);
        //}
		*/

		$sql = "select srl_ix from sellertool_regist_relation where site_code='".$this->site_code."' and pid='$pid' order by regist_date desc limit 0,1";
		$this->db->query($sql);
		
		
		if($this->db->total){
			$this->db->fetch();
			if($_resultCode == 500){
				$sql = "update sellertool_regist_relation set 
					result_msg='".$_message."',
					result_code='".$_resultCode."',					
					update_response_text='".$response_text."',
					update_date=NOW()
				where srl_ix='".$this->db->dt[srl_ix]."'";

			}else{
				$sql = "update sellertool_regist_relation set 
					result_msg='".$_message."',
					result_code='".$_resultCode."', ";
					if($_productNo){
					$sql .="result_pno = '".$_productNo."', ";
					}
					$sql .="
					target_price='".$_target_sellprice."',
					update_response_text='".$response_text."',
					update_date=NOW()
				where srl_ix='".$this->db->dt[srl_ix]."'";
			}
			if($this->is_message_display){
				echo "<br><br>";
				echo nl2br($sql);
			}
			$this->db->query($sql);
		}else{
			$sql = "insert into sellertool_regist_relation 
						(site_code, pid, add_info_id, target_cid, target_name, result_pno, result_msg, result_code, target_price, response_text, regist_date)
						values
						('".$this->site_code."','$pid','$add_info_id','$target_cid','$target_name','$_productNo','$_message','$_resultCode','$_target_sellprice','$response_text',NOW())"; 
			if($this->is_message_display){
				echo "<br><br>";
				echo nl2br($sql);
			}
			$this->db->query($sql);
		}
		if($this->is_message_display){
			echo "<br><br>";
		}
    }


	 /**
	 * 등록결과 로그에 넣기
	 */
	 public function submitDeliveryRegistLog($oid, $dsid, $result_code, $res_error_array){
		$db = new MySQL;

		if(count($res_error_array) > 1)
			$res_error = implode("|", $res_error_array);
		else
			$res_error = $res_error_array;

		$regdate = date("Y-m-d H:i:s");

		// 모든 log 넣는곳
		$sql = "insert into sellertool_delivery_regist_log (site_code, type, oid, dsid, result_code, result_msg, seller_id, reg_date) values('demandship','regist','$oid','$dsid','$result_code','$res_error', '".$_SESSION['admininfo']['admin_id']."', '$regdate')";
		$db->query($sql);
	}


	public function submitOptionRegistLog($pid,$opnd_ix, $target_pid,$target_variant_id, $result, $response_text=""){
		//$this->submitOptionRegistLog($pid,$options_details[$i][opnd_ix], $product_id, $Result["data"]["variant_id"], $result, print_r($Result, true))
        $db = new MySQL;
        
        $_message = $result->message;
        $_productNo = $result->productNo;
		$_target_sellprice = $result->sellprice;
		$response = $response_text;
		$response_text = print_r($response_text, true);

        if($result->resultCode == "fail"){
			$_resultCode = "500";
		}else{
			$_resultCode = $result->resultCode;
		}
		 
 
		$sql = "select orr_ix, target_variant_id, opnd_ix from sellertool_option_regist_relation where site_code='".$this->site_code."' and pid='$pid' and opnd_ix = '".$opnd_ix."' order by regist_date desc limit 0,1";
		echo "<br>".$sql."<br>";
		$this->db->query($sql);
		echo "<br>=====response=====";
		echo "<br>response[code] : ".$response["code"];
		//print_r($this->db->dt);
		echo "=====response=====";
		
		if($this->db->total){
			$this->db->fetch();
			if($response[code] == "4002"){
					if($this->db->dt[opnd_ix] == 0 && $this->db->dt[target_variant_id] != ""){
						
						//$sql = "delete from sellertool_option_regist_relation where orr_ix='".$this->db->dt[orr_ix]."' ";
						//echo $sql."<br>";
						//$this->db->query($sql);

						$sql = "update sellertool_option_regist_relation set 
									opnd_ix='".$this->db->dt[opnd_ix]."',
									update_date=NOW()
								where pid='".$pid."' and opnd_ix = '0' ";
					
					}else{
						//$sql = "delete from sellertool_option_regist_relation where orr_ix = '".$this->db->dt[orr_ix]."' ";
						//echo $sql."<br>";
						//$this->db->query($sql);

						$sql = "update sellertool_option_regist_relation set 
									opnd_ix='".$this->db->dt[opnd_ix]."',
									update_date=NOW()
								where pid='".$pid."' and opnd_ix = '0' and target_variant_id  != '' ";
					}
			}else{
					if($_resultCode == 500){
						$sql = "update sellertool_option_regist_relation set 
							result_msg='".$_message."',
							result_code='".$_resultCode."',
							update_response_text='".$response_text."',
							update_date=NOW()
						where orr_ix='".$this->db->dt[orr_ix]."'";
					 
					}else{
						$sql = "update sellertool_option_regist_relation set 
							result_msg='".$_message."',
							result_code='".$_resultCode."',
							update_response_text='".$response_text."',
							target_price='".$_target_sellprice."',
							update_date=NOW()
						where orr_ix='".$this->db->dt[orr_ix]."'";
					}
			}
			if($this->is_message_display){
				echo "result : ".$_resultCode."<br><br>";
				echo "<br><br>";
				echo nl2br($sql);
			}
			$this->db->query($sql);
		}else{
			if($response[code] == "4002"){
				if($opnd_ix){
					$sql = "update sellertool_option_regist_relation set 
									opnd_ix='".$opnd_ix."',
									update_date=NOW()
								where pid='".$pid."' and opnd_ix = '0' and target_variant_id  != '' ";
					echo nl2br($sql);
					$this->db->query($sql);
				}
			}
			$sql = "insert into sellertool_option_regist_relation 
						(site_code, pid, opnd_ix, target_variant_id, result_msg, result_code, target_price,  response_text, regist_date)
						values
						('".$this->site_code."','$pid','$opnd_ix','$target_variant_id','$_message','$_resultCode','$_target_sellprice','$response_text',NOW())"; 
			if($this->is_message_display){
				echo "<br><br>";
				echo nl2br($sql);
			}
			$this->db->query($sql);
		}
		if($this->is_message_display){
			echo "<br><br>";
		}
    }
	
	public function getGoodbuySellyPid($pid){
		$db = new MySQL;
		
		$sql = "select result_pno from sellertool_regist_relation where site_code='".$this->site_code."' and pid='$pid' order by result_pno desc limit 0,1";
		$this->db->query($sql);
		$this->db->fetch();
		$result_pno = $this->db->dt['result_pno'];
		if(empty($result_pno)){
			return false;
		}else{
			return $result_pno;
		}
	}

	private function BuyingServicePriceCalcurate($price,$buying_service_currencyinfo){

	if($buying_service_currencyinfo[bs_air_wt] <= 1){ // 예상무게가 기본 1파운드 미만일경우
		$air_shipping = $buying_service_currencyinfo[bs_basic_air_shipping]; // 기본 1파운드 항공운송료
	}else{// 예상무계가 1파운드를 초과할경우
		$air_shipping = $buying_service_currencyinfo[bs_basic_air_shipping] + ($buying_service_currencyinfo[bs_add_air_shipping] * ($buying_service_currencyinfo[bs_air_wt] - 1)); 
	}
	
	$price = str_replace(",","",$price);
	$bs_duty_basis = ($price+$air_shipping)*1/$buying_service_currencyinfo[exchange_rate]; // 관세 대상 기준금액
	$bs_duty = round($bs_duty_basis*$buying_service_currencyinfo[bs_duty_rate]/100,-1); // 관세
	$bs_supertax = round(($bs_duty_basis+$bs_duty)*$buying_service_currencyinfo[bs_supertax_rate]/100,-1); // 부가세
	
	$bs_fee_rate = $buying_service_currencyinfo[bs_fee_rate];
	
	$buyingservice_coprice = round(($price+$air_shipping)*1/$buying_service_currencyinfo[exchange_rate]+$bs_duty+$bs_supertax+$buying_service_currencyinfo[clearance_fee],0);
	// 공급원가 = (orgin 원가 + 항공운송료)* 환율 + 관세 + 부가세 + 통관수수료 
	$bs_fee = round($buyingservice_coprice*$bs_fee_rate/100,1);
   
    $bs_fee_array[air_shipping] = $air_shipping;
    $bs_fee_array[bs_duty] = $bs_duty;
    $bs_fee_array[bs_supertax] = $bs_supertax;
    $bs_fee_array[buyingservice_coprice] = $buyingservice_coprice + $bs_fee; // 상품가격에 수수료율 추가
    $bs_fee_array[bs_fee] = $bs_fee;
    //echo "buyingservice_coprice:".$buyingservice_coprice."::".$bs_fee_rate."::".$bs_fee.":::".$bs_fee_array[buyingservice_coprice]."<br>";
    return $bs_fee_array[buyingservice_coprice];
}
}

/*
CREATE TABLE IF NOT EXISTS `sellertool_option_regist_relation` (
  `orr_ix` int(10) NOT NULL AUTO_INCREMENT COMMENT 'seq',
  `site_code` varchar(20) DEFAULT NULL COMMENT '사이트 코드',
  `pid` varchar(20) DEFAULT NULL COMMENT '쇼핑몰 상품코드',
  `target_pid` varchar(50) DEFAULT NULL COMMENT '제휴사에 등록한 수정코드',
  `result_pno` varchar(100) DEFAULT NULL COMMENT '결과-등록된상품코드',
  `result_msg` varchar(255) DEFAULT NULL COMMENT '결과-메시지',
  `result_code` varchar(10) DEFAULT NULL COMMENT '결과-코드, 200성공,500에러',
  `target_price` varchar(30) DEFAULT NULL COMMENT '제휴사 판매가격', 
  `response_text` mediumtext NOT NULL COMMENT '통신결과전문',
  `update_response_text` mediumtext NOT NULL COMMENT '수정결과',
  `regist_date` datetime COMMENT '등록일',
  `update_date` datetime COMMENT '수정일',
  PRIMARY KEY (`orr_ix`),
  KEY `pid` (`pid`),
  KEY `result_code` (`result_code`),
  KEY `site_code` (`site_code`),
  KEY `result_pno` (`result_pno`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8
*/