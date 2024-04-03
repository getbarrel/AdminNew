<?php
/**
 * Created by PhpStorm.
 * User: Hyungsoo.Kim
 * Date: 2017-07-11
 * Time: 오전 10:27
 */

require 'goodsflow.class.php';

class Lib_goodsflow extends Call_goodsflow {
    private $db;
    private $site_code;
    private $userInfo;
    private $ticket;
    private $result;
    private $error;
    private $tmp_img_array;
    private $sell_period;
    private $is_test;
    private $pname;
    private $mall_data_root;
    private $api_url;
    private $web_url;


    public function __construct($site_code = '') {
        $this->db = new Database ();
        $this->site_code = $site_code;
        $this->userInfo = $this->getUserInfo ();
        $this->site_id = $this->userInfo['site_id'];
        $this->ticket = $this->userInfo ['api_key'];
        $this->sell_period = 90; //default value
        $this->is_test = true; //테스트 여부
        $this->result = new stdClass();
        $this->result->resultCode = '';
        $this->result->totalItem = 0;
//        $this->result = (object) array('resultCode' => '', 'totalItem' => 0);

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

        if($this->is_test){
            $this->api_url = 'https://test1.goodsflow.com/delivery/api/v2';
            $this->web_url = 'https://test1.goodsflow.com/print';
        }else{
            $this->api_url = 'https://pr1.goodsflow.com/delivery/api/v2';
            $this->web_url = 'https://pr1.goodsflow.com/print';
        }
    }


    public function getPartnerCodeServiceInfo($partnerCode) {
        $response = $this->call($this->ticket, $this->api_url.'/contracts/partner/'.$partnerCode.'/');
        if($response->success == 1){
            $rs=$response->data->items;
        }else{
            $rs=array();
        }
        return $rs;
    }

    public function getPartnerCode($company_id) {
        $sql="select company_ix from common_company_detail where company_id='".$company_id."'";
        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt['company_ix'];
    }

    public function getServiceInsertUrl() {
        return $this->web_url.'/contract/detail.aspx';
    }

    public function getOTP($partnerCode) {
        $response = $this->call($this->ticket, $this->api_url.'/otps/partner/'.$partnerCode.'/',array());
        if($response->success == 1){
            $otp=$response->data;
        }else{
            $otp='';
        }
        return $otp;
    }

    public function getVerifiedResult($requestKey) {
        $requestData['data']['items'][] = array('requestKey'=>$requestKey);
        $response = $this->call($this->ticket, $this->api_url.'/contracts/verify-results/',json_encode($requestData));
        if($response->success == 1){
            $rs=$response->data->items[0];
        }else{
            $rs=NULL;
        }
        return $rs;
    }

    public function getServiceCancel($requestKey) {
        $response = $this->call($this->ticket, $this->api_url.'/contracts/'.$requestKey.'/cancel/',array());
        if($response->success == 1){
            $rs=true;
        }else{
            $rs=false;
        }
        return $rs;
    }

    public function setDBServiceInfo($company_id,$gsi) {
        $sql = "SELECT * FROM shop_goodsflow_info WHERE company_id = '".$company_id."' and requestKey = '".$gsi->requestKey."'";
        $this->db->query($sql);
        if(!$this->db->total){
            $sql = "insert into shop_goodsflow_info (company_id,requestKey,partnerCode,centerCode,deliverCode,centerZipCode,centerAddr1,centerAddr2,centerTel1,centerTel2,use_yn,regdate) values ('".$company_id."','".$gsi->requestKey."','".$gsi->partnerCode."','".$gsi->centerCode."','".$gsi->deliverCode."','".$gsi->centerZipCode."','".$gsi->centerAddr1."','".$gsi->centerAddr2."','".$gsi->centerTel1."','".$gsi->centerTel2."','Y',NOW())";
            $this->db->query($sql);
        }
    }

    public function getDeliverNameToDeliveryCode($deliverCode) {
        $sql = "SELECT * FROM sellertool_etc_linked_relation WHERE site_code = 'goodsflow' and etc_div = 'T' AND target_code = '".$deliverCode."'";
        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt['target_name'];
    }

    public function getUserInfo() {
        $sql = "SELECT * 
				FROM sellertool_site_info 
				WHERE site_code = '" . $this->site_code . "'";
        $this->db->query ( $sql );
        if ($this->db->total) {
            return $this->db->fetch ();
        } else {
            $this->error ['code'] = '1001';
            $this->error ['msg'] = '제휴사 정보가 올바르지 않습니다.';
            $this->printError ();
        }
    }

    function sendOrdReqDelivery($data){

        /*
        if($data[order_from] == 'self' || $data[order_from] == 'cafe24_box' || $data[order_from] == 'cafe24_soho'){	//과금되는 계정
            $api_key = $this->ticket;
            $mem_code = $this->site_id;
        }else{	//과금되지 않는 계정
            if($_SERVER['HTTP_HOST'] == 'daisodev.daisomall.co.kr'){	//개발키
                $api_key = 'c208cd61-d855-413d-b047-056c3067a7be';
                $mem_code = 'daisomallTtest';
            }else{	//운영키
                $api_key = '83e49587-b3a7-4b44-8077-cc2e8f3b0e9b';
                $mem_code = 'daisomallT';
            }
        }
        */
        $api_key = $this->ticket;
        $mem_code = $this->site_id;

        //송장번호 공백제거 추가
        $data['invoice_no'] = trim($data['invoice_no']);

        //택배사의 송장번호가 유효한지 체크
//        if(!$this->checkInvoiceNo($data)) {
//            $this->result->resultCode = 'fail';
//            $this->result->message = '택배사의 유효하지 않은 송장번호입니다.';
//            return $this->result;
//        }

        $oid = $data[oid];
        $od_ix = $data[od_ix];
        $co_oid = $data[co_oid];
        $co_od_ix = $data[co_od_ix];
        $co_delivery_no = $data[co_delivery_no];
        $option_etc = $data[option_etc];
        $ic_date = $data[ic_date];
        $due_date = $data[due_date];
        $quick = $data[quick];
        $invoice_no = $data[invoice_no];

        $requestArray = $this->makeOrderConfirm($oid, $od_ix, $quick, $invoice_no);

        $requestData[data][items][] = $requestArray;

        //echo "<br/>";

        $requestData = json_encode($requestData);

        $action = "https://ws1.goodsflow.com/gws/api/Member/v3/SendTraceRequest/".$mem_code."";

        //전송 실패가 발송하는 경우가 있어 기본적으로 프로세스를 3회 동작 시키고 성공시 for문 종료 처리
        for($i=0; $i < 3; $i++){
            $response = $this->call($api_key, $action, $requestData);

            if($response){
                break;
            }
        }

        //print_r($response);
        //exit;

        if($response->success == 1){
            $this->result->resultCode = 'success';
            $this->result->message = $response->error->message;
            $status_data[transUniqueCode] = $oid."_".$invoice_no;
            $status_data[detail_message] = '';
            $status_data[status] = 'WDR';

            $status_message = '굿스플로 연동 성공';
        }else{
            $this->result->resultCode = 'fail';
            $this->result->transUniqueCode = $response->error->details[0]->transUniqueCode;
            $this->result->message = $response->error->details[0]->message;
            $status_data[transUniqueCode] = $response->error->details[0]->transUniqueCode;
            $status_data[detail_message] = $response->error->details[0]->message;

            $status_message = '굿스플로 연동 실패['.$status_data[detail_message].']';
        }

        $status_data[api] = 'SendTraceRequest';
        $status_data[oid] = $oid;
        $status_data[od_ix] = $od_ix;
        $status_data[itemUniqueCode] = $od_ix."_".str_replace(" ", "", str_replace("-", "", $invoice_no));
        $status_data[error_status] = $response->error->status;
        $status_data[error_message] = $response->error->message;
        $status_data[error_detailMessage] = $response->error->detailMessage;
        $status_data[json_data] = json_encode($response);
        $status_data[resultCode] = $this->result->resultCode;

        set_order_status($oid, 'GDR', $status_message, '시스템', $data[company_id], $od_ix, $data[pid], $reason_code="", $quick, $invoice_no, $c_type="");

        $this->set_order_goodsflow_response($status_data);
        $this->set_order_goodsflow_status($status_data);

        return $this->result;
    }

    public function getOrderDelivery(){

        $action = "https://ws1.goodsflow.com/gws/api/Member/v3/ReceiveTraceResult/".$this->site_id."";

        $response = $this->call($this->ticket, $action, array());

        $response->data->totalItems;
//        echo '<pre>';
//        print_r($response);
//        echo '</pre>';

        if($response->success == 1){
            $this->result->resultCode = 'success';
            $this->result->totalItems = $response->data->totalItems;
            $this->result->items = $response->data->items;
            $this->result->message = $response->error->message;
        }else{
            $this->result->resultCode = 'fail';
            if($response->error->details[0]->message){
                $this->result->message = $response->error->details[0]->message;
            }else{
                $this->result->message = $response->error->message;
            }
        }

        return $this->result;
    }

    public function getConfirmStatus($data){
        $db = new Database();

        $trans_unique_code = $data['transUniqueCode'];
        $status = $data['status'];

        $sql = "SELECT 
					*
				FROM 
					shop_order_goodsflow_status
				WHERE 
					trans_unique_code = '".$trans_unique_code."'
					AND status = '".$status."'";
        $db->query($sql);

        if($db->total){
            $this->result = true;
        }else{
            $this->result = false;
        }

        return $this->result;

    }

    private function makeOrderConfirm($oid, $od_ix, $quick, $invoice_no){
        $db = new Database ();
        $db2 = new Database ();

        $sql = "SELECT
					so.oid, sod.od_ix, sod.pid, sod.pcode, sod.pname, sod.option_id, sod.pcnt, so.bname, so.bzip, 
					so.baddr, so.bmobile, so.btel, sod.dr_date, sod.invoice_no, sod.coprice, sod.listprice, sod.psprice, sod.dcprice, sod.ptprice, sod.pt_dcprice,
					sod.regdate, sod.ic_date, sod.update_date, sod.dt_ix, so.delivery_price, sod.order_from,
					ccd.company_ix, ccd.company_id, ccd.com_name, sodd.rname, sodd.zip, sodd.addr1, sodd.addr2, sodd.rmobile, sodd.rtel,
					(select delivery_pay_type from shop_order_delivery sod2 where sod2.oid = sod.oid and sod2.company_id = sod.company_id limit 1) as delivery_pay_type
				FROM
					shop_order so 
					INNER JOIN shop_order_detail sod ON so.oid = sod.oid
					LEFT JOIN shop_order_detail_deliveryinfo sodd ON sod.oid = sodd.oid
					LEFT JOIN shop_product sp ON sod.pid = sp.id
					LEFT JOIN common_company_detail ccd ON sp.admin = ccd.company_id
				WHERE
					sod.oid = '".$oid."'
					AND sod.od_ix = '".$od_ix."'";
        $db->query($sql);
        $order_data = $db->fetch();

        $update_date = preg_replace("/\s+/", "", str_replace(":", "", str_replace("-", "", $order_data['update_date'])));
        $regdate = preg_replace("/\s+/", "", str_replace(":", "", str_replace("-", "", $order_data['regdate'])));

        if($order_data['order_from'] == 'offline'){
            $ic_date = $regdate;
        }else{
            $ic_date = preg_replace("/\s+/", "", str_replace(":", "", str_replace("-", "", $order_data['ic_date'])));
        }

        if($order_data['delivery_pay_type'] == '2'){
            $payTypeCode = 'BH'; // 착불
        }else{
            $payTypeCode = 'SH'; // 선불
        }
        /* 2017-06-21 조건 변경
        if($order_data['delivery_price'] > 0){
            $payTypeCode = 'BH';
        }else{
            $payTypeCode = 'SH';
        }
        */

        $sql = "SELECT * FROM shop_order_delivery WHERE oid = '".$order_data['oid']."' AND pid = '".$order_data['pid']."'";
        $db2->query($sql);
        $db2->fetch();

        if($db2->dt['delivery_policy'] == '9'){
            $dlvRetType = 'R';
        }else{
            $dlvRetType = 'D';
        }

        $sql = "SELECT * FROM shop_order_goodsflow_status WHERE trans_unique_code = '".$order_data['oid']."_".$invoice_no."' AND status = 'DR'";
        $db2->query($sql);

        if($db2->total > 0){
            $inputType = 'U';
        }else{
            $inputType = 'I';
        }

        $sql = "SELECT * FROM sellertool_etc_linked_relation WHERE site_code = 'goodsflow' and etc_div = 'T' AND origin_code = '".$quick."'";
        $db2->query($sql);
        $db2->fetch();
        $target_code = $db2->dt['target_code'];


        //$data['inputType'] = $inputType;																			//	Y 자료구분 신규(I), 수정(U)
        if(empty($data['transUniqueCode'])) {
            $data['transUniqueCode'] = $order_data['oid']."_".str_replace("-", "", $invoice_no);                //	Y 배송번호* 배송에 대한 고유번호 (굿스플로 전송단위)
        }
        $data['sectionCode'] = $this->site_id;																	//	관리구분코드 기본값은 회원사코드
        $data['sellerCode'] = $order_data['company_id'];														//	Y 판매자ID 판매자ID
        $data['sellerName'] = $order_data['com_name'];
        $data['fromName'] = str_replace("'", "", $order_data['bname']);									//	Y 보내는분 명
        $data['toName'] = $order_data['rname'];																//	Y 받는분 명
        $data['toMobile'] = $order_data['rmobile'];																//	Y 받는분 전화번호1 (휴대폰번호) 444 우측정렬  예)010-1234-5678
        $data['toTelephone'] = $order_data['rtel'];																//	받는분 전화번호2 (일반전화) 444 우측정렬 예)02-123-4567
        $data['logisticsCode'] = $target_code;																	//	Y 배송사 코드 3.4 배송사리스트 참조
        $data['invoiceNo'] = str_replace("-", "", $invoice_no);												//	Y 운송장번호 숫자만(‘-’제외), 예)1234567890
        $data['dlvRetType'] = $dlvRetType;																		//	Y 배송구분 정상(D), 반품(R)
        $data['invoicePrintDate'] = $update_date;																//	Y 운송장 등록일시 YYYYMMDDHHMMSS, 예)20090101083000
        //$data['ordName'] = str_replace("'", "", $order_data['bname']);										//	주문자명 SMS 이용 시 필수
        $data['packingNo'] = $order_data['dt_ix'];																//	배송묶음번호* 묶음배송에 대한 일련번 호 있을 시 사용  dt_ix
        $data['payTypeCode'] = $payTypeCode;																	//	기본 운임지불방법 * 판매자부담(SH), 구매자 부담(BH)

        //$sub_data['itemUniqueCode'] = $order_data['od_ix']."_".str_replace("-", "", $invoice_no);	//	Y 고객사용번호* 상품에 대한 고유번호 (결과 생성 및 보고 단 위) invoice_no
        $sub_data['orderNo'] = $order_data['oid'];																//	Y 주문번호   oid
        $sub_data['orderLine'] = $order_data['od_ix'];														//	주문행 번호 od_ix
        $sub_data['itemCode'] = $order_data['pid'];															//	주문행 상품코드
        $sub_data['itemName'] = $order_data['pname'];														//	Y 주문행 상품명
        $sub_data['itemOption'] = $order_data['option_id'];													//	주문행 상품옵션
        $sub_data['itemQty'] = $order_data['pcnt'];															//	Y 주문행 상품수량
        $sub_data['itemPrice'] = $order_data['dcprice'];														//	주문행 상품단가
        $sub_data['orderDate'] = $regdate;																			//	Y 주문일시 YYYYMMDDHHMMSS, 예)20090101083000
        $sub_data['paymentDate'] = $ic_date;																	//	Y 입금일시 YYYYMMDDHHMMSS, 예)20090101083000
        $sub_data['defCode1'] = "";																					//	업체관리코드1* 사용자정의코드1
        $sub_data['defCode2'] = "";																					//	업체관리코드2* 사용자정의코드2

        $data['requestDetails'][] = $sub_data;

        return $data;
    }

    public function sendTraceResultResponse($requestArray){

        $action = "https://ws1.goodsflow.com/gws/api/Member/v3/SendTraceResultResponse/".$this->site_id."";

        $response = $this->call($this->ticket, $action, $requestArray);

        echo "<br/>====================================<br/>";
        print_r($response);
        echo "<br/>====================================<br/>";
        //exit;
        $status_data[api] = 'SendTraceResultResponse';
        $status_data[status] = 'DC';
        $status_data[error_status] = $response->error->status;
        $status_data[error_message] = $response->error->message;
        $status_data[error_detailMessage] = $response->error->detailMessage;
        $status_data[json_data] = json_encode($response);

        if($response->success == 1){
            $this->result->resultCode = 'success';
            $this->result->totalItems = $response->data->totalItems;
            $this->result->items = $response->data->items;
            $this->result->message = $response->error->message;
        }else{
            $this->result->resultCode = 'fail';
            $this->result->message = $response->error->details[0]->message;
        }
        /*
        echo "<br/>====================================<br/>";
        print_r($status_data);
        echo "<br/>====================================<br/>";
        */
        $this->set_order_goodsflow_response($status_data);

        return $this->result;
    }

    public function useReturnServiceInfo($od_ix){
        //isUse: true,false
        //message: 메세지
        //data: 실제 반품 코드 사용하는 shop_goodsflow_info 정보

        $return = array();
        $return['isUse']=false;
        $return['message']="";
        $return['data']="";

        $sql = "SELECT 
          od.company_id,
          sd.goodsflow_return_yn,
          sd.goodsflow_policy_type,
          odd.send_yn,
          odd.send_type
        FROM
            shop_order_detail od
            LEFT JOIN common_seller_delivery sd ON (sd.company_id=od.company_id)
            LEFT JOIN shop_order_detail_deliveryinfo odd ON (odd.odd_ix=od.odd_ix)
        WHERE od.od_ix = '".$od_ix."' ";
        $this->db->query($sql);
        $sellInfo = $this->db->fetch();

        //아직 반품상품을 보내지 않았습니다. , 지정택배방문요청(셀러업체와 계약된 택배업체 방문수령 수거)
        if(!($sellInfo['send_yn'] == 'N' && $sellInfo['send_type'] == '2')){
            $return['message']="지정택배방문요청이 아님";
            return $return;
        }

        if($sellInfo['goodsflow_return_yn'] != 'Y'){
            $return['message']="판매자 굿스플로 반품 서비스 사용유무 - 사용안함";
            return $return;
        }

        $use_company_id='';
        if($sellInfo['goodsflow_policy_type'] == 2){
            $use_company_id = $sellInfo['company_id'];
        }else{
            $sql="SELECT 
              cd.company_id,
              sd.goodsflow_return_yn
          FROM 
            common_company_detail cd 
            LEFT JOIN common_seller_delivery sd ON (sd.company_id=cd.company_id)
          WHERE cd.com_type='A'";
            $this->db->query($sql);
            $adminInfo = $this->db->fetch();
            if($adminInfo['goodsflow_return_yn'] != 'Y'){
                $return['message']="본사 굿스플로 반품 서비스 사용유무 - 사용안함";
                return $return;
            }
            $use_company_id = $adminInfo['company_id'];
        }

        $sql = "SELECT sgi.*, ccd.com_name
        FROM 
          shop_goodsflow_info sgi 
          LEFT JOIN common_company_detail ccd ON (sgi.company_id=ccd.company_id)
        WHERE sgi.company_id = '".$use_company_id."' ";
        $this->db->query($sql);

        $return['isUse']=true;
        $return['data']=$this->db->fetch();
        return $return;
    }

    public function returnRegist($od_ix, $info){
        //isUse: true,false
        //message: 메세지
        //data: 실제 반품 코드 사용하는 shop_goodsflow_info 정보

        $return = array();
        $return['success']=false;
        $return['message']="";

        if(!$info){
            $return['message']="굿스플로 반품 서비스정보 없음";
            return;
        }

        $requestData = array();
        $orderData = array();
        $orderItemsData = array();

        $sql="SELECT
          CONCAT(od.od_ix, '-', od.claim_group) AS transUniqueCd
          ,bdi.rname AS sndName
          ,REPLACE(bdi.zip,'-','') AS sndZipCode
          ,bdi.addr1 AS sndAddr1
          ,bdi.addr2 AS sndAddr2
          ,REPLACE(bdi.rmobile,'-','') AS sndTel1
          ,REPLACE(bdi.rtel,'-','') AS sndTel2
          ,o.bname AS ordName
          ,REPLACE(o.bmobile,'-','') AS ordTel1
          ,REPLACE(o.btel,'-','') AS ordTel2
          ,od.invoice_no AS sheetNo
          ,bdi.msg AS msgToTrans
          ,od.od_ix AS uniqueCd
          ,o.oid AS ordNo
          ,od.pcode AS itemCode
          ,od.pname AS itemName
          ,od.option_text AS itemOption
          ,od.pcnt AS itemQty
          ,od.dcprice AS itemPrice
          ,DATE_FORMAT(od.regdate,'%Y%m%d%H%i%s') AS ordDate
          ,DATE_FORMAT(od.ic_date,'%Y%m%d%H%i%s') AS paymentDate
          ,(SELECT 
                  sd.shop_name
              FROM 
                common_company_detail cd 
                LEFT JOIN common_seller_detail sd ON (sd.company_id=cd.company_id)
              WHERE cd.com_type='A'
            ) AS defCode1
        FROM shop_order_detail od
             LEFT JOIN shop_order_detail_deliveryinfo bdi ON (bdi.odd_ix=od.odd_ix)
             LEFT JOIN shop_order o ON (o.oid=od.oid)
        WHERE od.od_ix = '".$od_ix."'";
        $this->db->query($sql);
        $dbData = $this->db->fetch();

        $orderData['transUniqueCd'] = $dbData['transUniqueCd']; //string 30 Y 배송고유번호 주문전송 단위
        $orderData['centerCode'] = $info['centerCode']; //string 20 Y 발송지코드 서비스이용 승인된 발송지코드
        $orderData['deliverCode'] = $info['deliverCode']; //string 20  택배사코드 [[택배사코드]] 전담반품인 경우 [택배사코드: ""]
        $orderData['sndName'] = $dbData['sndName']; //string 50 Y 보내는분명
        $orderData['sndZipCode'] = $dbData['sndZipCode']; //string 6 Y 보내는분 우편번호
        $orderData['sndAddr1'] = $dbData['sndAddr1']; //string 100 Y 보내는분 기본주소
        $orderData['sndAddr2'] = $dbData['sndAddr2']; //string 100   보내는분 상세주소
        $orderData['sndTel1'] = $dbData['sndTel1']; //string 12 Y 보내는분 전화1
        $orderData['sndTel2'] = $dbData['sndTel2']; //string 12   보내는분 전화2
        $orderData['rcvName'] = $info['com_name']; //string 50 Y 받는분명
        $orderData['rcvZipCode'] = $info['centerZipCode']; //string 6 Y 받는분 우편번호
        $orderData['rcvAddr1'] = $info['centerAddr1']; //string 100 Y 받는분 기본주소
        $orderData['rcvAddr2'] = $info['centerAddr2']; //string 100   받는분 상세주소
        $orderData['rcvTel1'] = $info['centerTel1']; //string 12 Y 받는분 전화1
        $orderData['rcvTel2'] = $info['centerTel2']; //string 12   받는분 전화2
        $orderData['mallId'] = 'com-ix-'.$info['partnerCode']; //string 20 Y 판매자ID 협력사에서 사용 판매자 ID
        $orderData['ordName'] = $dbData['ordName']; //string 50   주문자명
        $orderData['ordTel1'] = $dbData['ordTel1']; //string 12   주문자전화1
        $orderData['ordTel2'] = $dbData['ordTel2']; //string 12   주문자전화2
        $orderData['status'] = 'N'; //string 1 Y 처리상태 [[처리상태코드]] "N": 일반, "O": 원송장
        $orderData['sheetNo'] = $dbData['sheetNo']; //string 20 Y 송장번호 *반품건의 원송장번호
        $orderData['paymentTypeCode'] = 'SH'; //string 2 Y 운임지불방법 [[지불방법코드]] "SH": 선불, "BH": 착불
        $orderData['preShippingPriceYN'] = 'Y'; //string 1   선결제여부 배송비를 판매자가 결제한 여부 "Y": 선결제, "N": 결제안함
        $orderData['boxSize'] = ''; //string 2   기본 박스규격 [[박스규격코드]] 없으면 승인계약의 최소박스규격
        $orderData['msgToTrans'] = $dbData['msgToTrans']; //string 100   배송메시지

        $orderItemsData['uniqueCd'] = $dbData['uniqueCd']; //string 50 Y 고객사용번호 결과보고 단위
        $orderItemsData['ordNo'] = $dbData['ordNo']; //string 50 Y 주문번호
        $orderItemsData['ordLineNo'] = 1; //integer  5   주문행번호
        $orderItemsData['itemCode'] = $dbData['itemCode']; //string 30   상품코드
        $orderItemsData['itemName'] = $dbData['itemName']; //string 250 Y 상품명
        $orderItemsData['itemOption'] = strip_tags($dbData['itemOption']); //string 500   상품옵션
        $orderItemsData['itemQty'] = $dbData['itemQty']; //integer  10 Y 상품수량
        $orderItemsData['itemPrice'] = $dbData['itemPrice']; //integer  10   상품단가
        $orderItemsData['ordDate'] = $dbData['ordDate']; //string 14 Y 주문일시 YYYYMMDDHH24mmss 36
        $orderItemsData['paymentDate'] = $dbData['paymentDate']; //string 14    입금일시 YYYYMMDDHH24mmss
        $orderItemsData['defCode1'] = $dbData['defCode1']; //string 50   업체관리코드 1 판매쇼핑몰명
        $orderItemsData['defCode2'] = $dbData['defCode2']; //string 50   업체관리코드 2

        $orderData['orderItems'][] = $orderItemsData;

        $requestData['data']['items'][] = $orderData;

        $response = $this->call($this->ticket, $this->api_url.'/returns/partner/'.$info['partnerCode'].'/',json_encode($requestData));
        $return['success']=$response->success;
        if($response->success==true) {
            $setResponseData['api']="returnRegist";
            $setResponseData['status']=ORDER_STATUS_RETURN_ING;
            $setResponseData['oid']=$dbData['ordNo'];
            $setResponseData['od_ix']=$dbData['uniqueCd'];
            $setResponseData['transUniqueCode']=$dbData['transUniqueCd'];
            $setResponseData['itemUniqueCode']=$dbData['uniqueCd'];
            $setResponseData['json_data']=json_encode($requestData);

            $this->set_order_goodsflow_response($setResponseData);
        }else{
            if(is_array($response->error->detail->items[0]->detailErrors) && count($response->error->detail->items[0]->detailErrors)>0){
                foreach($response->error->detail->items[0]->detailErrors AS $index=>$detailError){
                    if($index!=0){
                        $return['message'].= ' , ';
                    }
                    $return['message'].= $detailError->message;
                }
            }else if(is_array($response->error->detail->items) && count($response->error->detail->items)>0){
                foreach($response->error->detail->items AS $index=>$detailError){
                    if($index!=0){
                        $return['message'].= ' , ';
                    }
                    $return['message'].= $detailError->message;
                }
            }else{
                $return['message']='등록 실패';
            }
        }
        return $return;
    }

    public function checkReturnRegist($od_ix){
        //isUse: true,false
        //message: 메세지
        //data: 배송 고유번호 transUniqueCd

        $return = array();
        $return['isUse']=false;
        $return['message']="";
        $return['data']="";

        $sql = "SELECT 
          gr.trans_unique_code as transUniqueCd
        FROM
            shop_order_detail od
            ,shop_order_goodsflow_response gr 
        WHERE
            gr.api='returnRegist' 
            AND gr.status='RI' 
            AND gr.oid=od.oid 
            AND gr.od_ix=od.od_ix 
            AND gr.trans_unique_code=CONCAT(od.od_ix, '-', od.claim_group)
            AND gr.item_unique_code=od.od_ix
            AND od.od_ix = '".$od_ix."' ";
        $this->db->query($sql);
        if($this->db->total > 0){
            $info = $this->db->fetch();
            $return['isUse']=true;
            $return['data']=$info['transUniqueCd'];
        }else{
            $return['message']="반품 신청 안된 주문건";
        }
        return $return;
    }

    public function returnCancel($transUniqueCd){
        //success: true,false
        //message: 메세지

        $return = array();
        $return['success']=false;
        $return['message']="";

        $response = $this->call($this->ticket, $this->api_url.'/returns/'.$transUniqueCd.'/cancel/',array());
        if($response->success == 1){
            $return['success']=true;
        }else{
            $return['message']=$response->error->message;
        }
        return $return;
    }

    public function set_order_goodsflow_response($data){
        global $master_db;

        if($data['transUniqueCode']){
            $sql = "INSERT INTO shop_order_goodsflow_response VALUES ('', '".$data['api']."', '".$data['status']."', '".$data['oid']."', '".$data['od_ix']."', '".$data['transUniqueCode']."', '".$data['itemUniqueCode']."', '".$data['seq']."', '".$data['sectionCode']."', '".$data['logisticsCode']."', '".$data['invoiceNo']."', '".$data['itemQty']."', '".$data['dlvStatType']."', '".$data['procDateTime']."', '".$data['taker']."', '".$data['exceptionCode']."', '".$data['exceptionName']."', '".$data['errorCode']."', '".$data['errorName']."', '".$data['createDateTime']."', '".$data['json_data']."', NOW())";

            $master_db->query($sql);
        }
    }

    public function set_order_goodsflow_status($data){
        global $master_db;

        $sql = "SELECT * FROM shop_order_goodsflow_status WHERE trans_unique_code = '".$data['transUniqueCode']."'";
        $master_db->query($sql);

        $sql ='';
        if($master_db->total > 0){
            if($data['status'] == 'DR'){
                if($data['resultCode'] == 'success'){
                    $sql = "UPDATE shop_order_goodsflow_status
							SET
								status = 'DR',
								update_date = NOW()
							WHERE
								trans_unique_code = '".$data['transUniqueCode']."'";
                }else{
                    $sql = "UPDATE shop_order_goodsflow_status
							SET
								update_date = NOW()
							WHERE
								trans_unique_code = '".$data['transUniqueCode']."'";
                }
            }else{
                if($data['dlvStatType'] == '30'){
                    $sql = "UPDATE shop_order_goodsflow_status
							SET
								status = 'DI',
								update_date = NOW()
							WHERE
								trans_unique_code = '".$data['transUniqueCode']."'";
                }else if($data['dlvStatType'] == '70'){
                    $sql = "UPDATE shop_order_goodsflow_status
							SET
								status = 'DC',
								update_date = NOW()
							WHERE
								trans_unique_code = '".$data['transUniqueCode']."'";
                }else{
                    $sql = "UPDATE shop_order_goodsflow_status
							SET
								update_date = NOW()
							WHERE
								trans_unique_code = '".$data['transUniqueCode']."'";
                }
            }
        }else{
            if($data['status'] == 'DR'){
                if($data['resultCode'] == 'success'){
                    $sql = "INSERT INTO shop_order_goodsflow_status VALUES ('', '".$data['oid']."', '".$data['od_ix']."', '".$data['transUniqueCode']."', '".$data['itemUniqueCode']."', 'DR', '', NOW())";
                }
            }else if($data['status'] == 'WDR'){
                if($data['resultCode'] == 'success'){
                    $sql = "INSERT INTO shop_order_goodsflow_status VALUES ('', '".$data['oid']."', '".$data['od_ix']."', '".$data['transUniqueCode']."', '".$data['itemUniqueCode']."', 'WDR', '', NOW())";
                }
            }else{
                if($data['dlvStatType'] == '30'){
                    $sql = "INSERT INTO shop_order_goodsflow_status VALUES ('', '".$data['oid']."', '".$data['od_ix']."', '".$data['transUniqueCode']."', '".$data['itemUniqueCode']."', 'DI', '', NOW())";
                }else if($data['dlvStatType'] == '70'){
                    $sql = "INSERT INTO shop_order_goodsflow_status VALUES ('', '".$data['oid']."', '".$data['od_ix']."', '".$data['transUniqueCode']."', '".$data['itemUniqueCode']."', 'DC', '', NOW())";
                }
            }
        }

        if($sql){
            $master_db->query($sql);
        }
    }

    /////////////굿스플로 버전 3.0 추가//////////////

    //택배사 송장번호가 유효한지 체크
    public function checkInvoiceNo($data){
        $db = new database();

        $sql = "SELECT * FROM sellertool_etc_linked_relation WHERE site_code = 'goodsflow' and etc_div = 'T' AND origin_code = '".$data['quick']."'";
        $db->query($sql);
        $db->fetch();
        $target_code = $db->dt['target_code'];

        $requestArray['logisticsCode'] = $target_code;
        $requestArray['invoiceNo'] = $data['invoice_no'];

        $requestData['data']['items'][] = $requestArray;
        $requestData = json_encode($requestData);

        $action = "https://ws1.goodsflow.com/gws/api/Member/v3/CheckInvoiceNo/".$this->site_id."";

        $response = $this->call($this->ticket, $action, $requestData);

        return $response->data->items['0']->isOk;
    }

}

?>