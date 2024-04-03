<?
/**
 * 몰스토리 관리자 PG결제 취소 모델
 *
 * @author bgh
 * @date 2013.07.10
 *
 */
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

class cancelModel{

	private $db = null;
	private $result = null;

	public function __construct(){
		$this->db = new Database();
	}

	/**
	 * PG사 정보 가져오기
	 *
	 * @param {string} PG명
	 * @return {array} pginfo
	 */
	public function getPgInfo($pgName){
		if($pgName=='payco'){

            $sql = "select
			*
		from
			shop_mall_config
		where
			mall_ix = '".$_SESSION['admininfo'][mall_ix]."'
			AND config_name in ('payco_seller_key','payco_cp_id','payco_product_id','payco_service_type')";
            $this->db->query($sql);
            $payment_array = $this->db->fetchall("object");

            for($i=0; $i < count($payment_array);$i++){
                $result[$payment_array[$i][config_name]] = $payment_array[$i][config_value];
            }

            return $result;


		}else if($pgName=='eximbay'){

            $sql = "select
			*
		from
			shop_mall_config
		where
			mall_ix = '".$_SESSION['admininfo'][mall_ix]."'
			AND config_name in ('eximbay_service_type','eximbay_mid','eximbay_secret_key')";
            $this->db->query($sql);
            $payment_array = $this->db->fetchall("object");

            for($i=0; $i < count($payment_array);$i++){
                $result[$payment_array[$i][config_name]] = $payment_array[$i][config_value];
            }

            return $result;

        }else if($pgName=='naverpayPg'){

            $sql = "select
			*
		from
			shop_mall_config
		where
			mall_ix = '".$_SESSION['admininfo'][mall_ix]."'
			AND config_name in ('naverpay_pg_partner_id','naverpay_pg_client_id','naverpay_pg_client_secret','naverpay_pg_service_type')";
            $this->db->query($sql);
            $payment_array = $this->db->fetchall("object");

            for($i=0; $i < count($payment_array);$i++){
                $result[$payment_array[$i][config_name]] = $payment_array[$i][config_value];
            }

            return $result;

        }else if($pgName=='toss'){

            $sql = "select
			*
		from
			shop_mall_config
		where
			mall_ix = '".$_SESSION['admininfo'][mall_ix]."'
			AND config_name in ('toss_api_key')";
            $this->db->query($sql);
            $payment_array = $this->db->fetchall("object");

            for($i=0; $i < count($payment_array);$i++){
                $result[$payment_array[$i][config_name]] = $payment_array[$i][config_value];
            }

            return $result;

        }else{

			$sql = "SELECT * FROM shop_payment_config WHERE pg_code = '".$pgName."'  AND  mall_ix = '".$_SESSION['admininfo']['mall_ix']."'"  ;
			$this->db->query($sql);

			if($this->db->total){
				for($i=0;$i < $this->db->total;$i++){
					$this->db->fetch($i);
					$result[$this->db->dt["config_name"]] = $this->db->dt["config_value"];
				}
				return $result;
			}else{
				return null;
			}
		}
	}

	/**
	 * 주문정보 가져오기
	 *
	 * @param {string} 주문번호,{string} 결제방법
	 * @return {array} 주문정보
	 * @modify
	 *		2014-05-19 Hong
	 *			-복수결제가 가능하게끔 솔루션변경되어 그에따른 처리!
	 *			-param 에 결제방법 추가
	 *		2015-09-24 Hong
	 *			- 사용안함
	 */

	public function getOrderInfo($oid,$method){
		$orderInfo = null;

		//$sql = "SELECT * FROM shop_order WHERE oid = '".$oid."'";
		//결제정보는 shop_order_payment에 정상결제인 있음
		$sql = "SELECT * FROM shop_order_payment WHERE oid = '".$oid."' and method='".$method."' and pay_type='G' LIMIT 0,1";
		$this->db->query($sql);
		if($this->db->total){
			$orderInfo = $this->db->fetch();

			//TODO: shop_order_price테이블 데이터 픽스 되는대로 맞춰서 수정할 것.
			$sql = "SELECT
						sum(case when payment_status = 'G' then product_price + delivery_price - reserve - point - saveprice else 0 end) as payment_price,
                        sum(case when payment_status = 'F' then product_price + delivery_price - reserve - point - saveprice else 0 end) as canceled_price
					FROM
						shop_order_price
					WHERE
						oid = '".$oid."' ";
			$this->db->query($sql);
			if($this->db->total){
				$this->db->fetch();
				$orderInfo["real_price"] = $this->db->dt["payment_price"];
                $orderInfo["canceled_price"] = $this->db->dt["canceled_price"];
                $orderInfo["remain_price"] = $orderInfo["real_price"] - $orderInfo["canceled_price"];
			}
		}
		return $orderInfo;

	}

	/**
	 * 사용중인 PG명 가져오기
	 */
 	public function getPgName(){
 		$sql = "SELECT config_value FROM shop_mall_config WHERE config_name = 'sattle_module'";
 		$this->db->query($sql);
 		if($this->db->total){
 			$this->db->fetch();
 			return $this->db->dt["config_value"];
 		}else{
 			return null;
 		}
 	}

    /**
     * 주문 로그에 남기기
     *
     * @param {array} data[receiveData] = 컨트롤러가 받은 데이터,
     *                data[cancelResult] = 취소처리결과 code/msg
     *
     */
    public function insertLog($data){
        if(empty($data["cancelResult"]["msg"])){
            $data["cancelResult"]["msg"] = "처리 실패";
        }
        $msg = $data["receiveData"]["cancel_amount"]." PG사 취소요청 -> ".$data["cancelResult"]["msg"];
		set_order_status($data["receiveData"]["oid"],ORDER_STATUS_REFUND_APPLY,$msg,"시스템","");
    }
}
