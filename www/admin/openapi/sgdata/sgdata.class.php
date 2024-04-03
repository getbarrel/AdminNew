<?php
/**
 * Created by PhpStorm.
 * User: FB_sc
 * Date: 2019-06-20
 * Time: 오후 1:42
 */

include_once($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/admin/inventory/inventory.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/admin/sellertool/sellertool.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"] ."/admin/logstory/class/sharedmemory.class");

class SgERP {

    private $db;
    private $slave_db;
    private $file;
    private $filename;
    private $type;
    private $function_name;
    private $readfile;
    private $opath;
    private $ipath;
    private $epath;
    private $bpath;
    private $spath;
    private $process_data;
    private $process_total;
    private $send_pull;
    private $is_error;
    private $cmd;
    private $coupon_rule;
    private $param;
    private $processYn;

    public function __construct($type) {
        $this->db = new Database();
        $this->slave_db = new Database();
        $this->slave_db->slave_db_setting();
        $this->type = $type;
        $this->is_error = false;
        $this->send_pull = array();

        //sharedmemory.class
        $shmop = new Shared("b2c_coupon_rule");
        $shmop->filepath = $_SERVER["DOCUMENT_ROOT"]."/data/barrel_data"."/_shared/";
        $shmop->SetFilePath();
        $coupon_data = $shmop->getObjectForKey("b2c_coupon_rule");
        $this->coupon_rule = unserialize(urldecode($coupon_data));

        //path 설정
        $this->opath = $_SERVER["DOCUMENT_ROOT"].'/data/barrel_data/erp/OUT/';
        $this->ipath = $_SERVER["DOCUMENT_ROOT"].'/data/barrel_data/erp/IN/';
        $this->epath = $_SERVER["DOCUMENT_ROOT"].'/data/barrel_data/erp/ERROR/';
        $this->bpath = $_SERVER["DOCUMENT_ROOT"].'/data/barrel_data/erp/BACKUP/'.date('Ymd').'/';
        if(!is_dir($this->bpath)){
            mkdir($this->bpath, 0777);
        }
        $this->spath = $_SERVER["DOCUMENT_ROOT"].'/admin/openapi/sgdata/sample/';
    }

    /**
     * desc: in 폴더에 처리된 주문정보 XML 파일저장
     * params: data
     **/
    private function writeXML() {
        $sample = '';
        $cdata_arr = array('SCustName', 'Saddr1', 'Saddr2', 'CustName', 'Description', 'GiftMsg', 'Cus_Nm');
        try {
            switch($this->type) {
                case 'D':
                    $root = 'Order';
                    //$xml = new SimpleXMLExtended('<Order/>');
                    $this->filename = $this->type.'0';
                    break;
                case 'R':
                    $root = 'Refund';
                    //$xml = new SimpleXMLExtended('<Cancel/>');
                    $this->filename = $this->type.'0';
                    break;
                case 'M':
                    $root = 'Member';
                    //$xml = new SimpleXMLExtended('<Member/>');
                    $this->filename = $this->type.'0';
                    break;
                case 'A':
                    $root = 'Cancel';
                    //$xml = new SimpleXMLExtended('<Cancel/>');
                    $this->filename = $this->type.'0';
                default :
                    break;
            }

            if(!empty($root)) {
                $datas = $this->getData();
                $total = count($datas);

                if($total > 0) {
                    //저장될 파일명
                    $this->filename .= date('YmdHi') . '.xml';
                    $xml = "<?xml version=\"1.0\"?>";
                    $xml .= "<$root>";
                    foreach($datas as $data) {
                        $xml .= str_replace('<?xml version="1.0"?>', '', $this->array_to_xml('OrderInfo', $data));
                    }
                    $xml .= "</$root>";
                    $this->saveXML($xml);
                }

            }else {
                throw new Exception('Invalid Type');
            }
        }catch(Exception $e){
            $this->function_name = 'writeXML';
            $error['msg'] = json_encode((array)$e);
            $this->error($error);
        }
    }

    /**
     * desc: Array => XML 전환
     * params: XML root / Array 데이터
     **/
    public function array_to_xml( $root, $arr ) {
        if(!function_exists('a2x')) {
            function a2x( $arr, &$xml, $pk=null ) {
                foreach( $arr as $k => $v ) {
                    if( !is_array($v) ) {
                        $xml->addChild($k,htmlspecialchars($v));
                    }else if( is_numeric(key($v)) ){
                        a2x($v, $xml, $k);
                    }else {
                        a2x($v, $xml->addChild( is_null($pk)? $k: $pk));
                    }
                }
            }
        }

        $xml = new SimpleXMLElement("<$root/>");
        a2x($arr, $xml);
        return $this->formatXmlString($xml->asXML());
    }

    /**
     * desc: Pretty XML
     * params: xml 데이터
     **/
    public function formatXmlString($xml) {

        // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
        $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

        // now indent the tags
        $token      = strtok($xml, "\n");
        $result     = ''; // holds formatted version as it is built
        $pad        = 0; // initial indent
        $matches    = array(); // returns from preg_matches()

        // scan each line and adjust indent based on opening/closing tags
        while ($token !== false) :

            // test for the various tag states

            // 1. open and closing tags on same line - no change
            if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
                $indent=0;
            // 2. closing tag - outdent now
            elseif (preg_match('/^<\/\w/', $token, $matches)) :
                $pad--;
                $indent = 0;
            // 3. opening tag - don't pad this one, only subsequent tags
            elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
                $indent=1;
            // 4. no indentation needed
            else :
                $indent = 0;
            endif;

            // pad the line with the required number of leading spaces
            $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
            $result .= $line . "\n"; // add to the cumulative result, with linefeed
            $token   = strtok("\n"); // get the next token
            $pad    += $indent; // update the pad size for subsequent lines
        endwhile;

        return $result;
    }

    /**
     * desc: order 정보를 가져와 setOrderData를 이용해 샘플에 맞는 데이터 생성
     **/
    private function getData() {
        $return_data = array();
        if($this->type == 'A'){
            $this->process_total = count($this->param);
            $this->process_data = $this->param;

            for($i=0; $i<$this->process_total; $i++) {
                $return_data[$i] = $this->setCancelData($this->process_data[$i]);
            }

        }else if($this->type == 'M') {
            $sql = "SELECT
                        AES_DECRYPT(UNHEX(name),'".$this->db->ase_encrypt_key."') as name,
                        cu.code,
                        birthday,
                        sex_div,
                        AES_DECRYPT(UNHEX(pcs),'".$this->db->ase_encrypt_key."') as pcs,
                        AES_DECRYPT(UNHEX(zip),'".$this->db->ase_encrypt_key."') as zip,
                        AES_DECRYPT(UNHEX(addr1),'".$this->db->ase_encrypt_key."') as addr1,
                        AES_DECRYPT(UNHEX(addr2),'".$this->db->ase_encrypt_key."') as addr2,
                        id
                    FROM
                        common_user cu
                    LEFT JOIN common_member_detail cmd ON (cu. CODE = cmd. CODE)
                    WHERE
                        erp_link_date IS NULL;";
            $this->db->query($sql);
            $this->process_total = $this->db->total;
            $this->process_data = $this->db->fetchall();

            for($i=0; $i<$this->process_total; $i++) {
                $return_data[$i] = $this->setMemberData($this->process_data[$i]);
            }

        }else {
            if($this->type == 'D') {
                $sql = "SELECT
                    *, od.od_ix,
                    sum(dc_price) AS dc_price,
                    op.reserve
                FROM
                    shop_order o
                LEFT JOIN shop_order_detail od ON (o.oid = od.oid)
                LEFT JOIN shop_order_detail_deliveryinfo odd ON (o.oid = odd.oid)
                LEFT JOIN shop_order_detail_discount dd ON (od.od_ix = dd.od_ix)
                LEFT JOIN shop_order_price op ON (
                    o.oid = op.oid
                    AND payment_status = 'G'
                )
                WHERE
                    od. STATUS IN ('IC', 'DR')
                AND erp_link_date IS NULL
                AND od.oid IN (SELECT oid FROM (SELECT DISTINCT od.oid FROM shop_order_detail od WHERE od. STATUS IN ('IC', 'DR') AND od.erp_link_date IS NULL LIMIT 1000) a )
                GROUP BY
                    od.od_ix
                ORDER BY
                    od.od_ix";
					// (select scp.cupon_no from shop_cupon_publish scp where scp.cupon_ix = (select sc.cupon_ix from shop_cupon sc where sc.cupon_kind = dd.dc_msg)) as cupon_no,
				    // dd.dc_msg
            }else if($this->type == 'R') {
                $sql = "SELECT
                        *, od.status, od.od_ix, odd.send_type, od.invoice_no as org_invoice
                    FROM
                        shop_order o
                    LEFT JOIN shop_order_detail od ON (o.oid = od.oid)
                    LEFT JOIN shop_order_claim_delivery ocd  ON (o.oid = ocd.oid AND od.claim_group = ocd.claim_group)
                    LEFT JOIN shop_order_detail_deliveryinfo odd ON (od.odd_ix = odd.odd_ix)
                    WHERE
                        od.status in('CC', 'EI', 'ED', 'RI', 'RD')
                    AND 
                        od.erp_link_date is not null
                    AND 
                        od.erp_refund_link_date is null
                    GROUP BY
                        od.od_ix
                    ";
            }
            $this->slave_db->query($sql);
            $this->process_total = $this->slave_db->total;
            $this->process_data = $this->slave_db->fetchall();
            $overOrderCnt = 0;

            for($i=0; $i<$this->process_total; $i++) {

                if($this->type == 'D') {
                    //우편번호, 전화번호에 한글들어갈시 예외
                    if(preg_match("/[\xA1-\xFE]/", $this->process_data[$i]['rmobile']) || preg_match("/[\xA1-\xFE]/", $this->process_data[$i]['zip'])) {
                        continue;
                    }


                }

                if($this->type == 'D') {
                    //현재 판매 진행 재고 체크
                    $sql = "select sum(pcnt) as sell_ing_cnt from shop_order_detail  where gid = '" . $this->process_data[$i]['gid'] . "' and status in ('IR','IC','DR','DD')";
                    $this->slave_db->query($sql);
                    $this->slave_db->fetch();
                    $sell_ing_cnt = $this->slave_db->dt['sell_ing_cnt'];

                    //해당 품목은 재고 체크
                    $sql = "select ifnull(sum(stock),0) as stock from inventory_product_stockinfo where gid = '" . $this->process_data[$i]['gid'] . "'";
                    $this->slave_db->query($sql);
                    $this->slave_db->fetch();
                    $stock = $this->slave_db->dt['stock'];

                    if(($stock - $sell_ing_cnt) < 0){
                        //과주문 대상으로 제외할 주문
                        //과주문 데이터는 연동 하지 않고 모두 패스 처리
                        //해당 품목에 대한 주문건을 관리자가 확인 후 취소 처리 할 경우 정상적으로 재 수집
                        unset($this->process_data[$i]);
                        $overOrderCnt ++;
                        continue;
                    }
                }

                $return_data[$i] = $this->setOrderData($this->process_data[$i]);

            }
            if($overOrderCnt > 0){
                $this->process_total = $this->process_total - $overOrderCnt;
            }
        }

        return $return_data;
    }

    /**
     * desc: 취소요청 정보 세팅
     * params: 취소요청 정보
     **/
    private function setCancelData($cancel) {
        $cancel_data = array();
        $cancel_data['Command'] = $this->type.'0';
        $cancel_data['OwnerID'] = 'SGDATA';
        $cancel_data['InterfaceDate'] = date('Ymd');
        $cancel_data['InterfaceType'] = 'N';
        $cancel_data['ErpOrderID'] = $cancel['oid'];
        $cancel_data['OrderSeq'] = $cancel['od_ix'];
        $cancel_data['ItemID'] = $cancel['gid'];
        $cancel_data['RetYn'] = 1;

        return $cancel_data;
    }

    /**
     * desc: 유저정보 데이터 양식 세팅
     * params: 유저정보 데이터
     **/
    private function setMemberData($member) {
        $member_data = array();
        $member_data['Command'] = $this->type.'0';
        $member_data['OwnerID'] = 'SGDATA';
        $member_data['InterfaceDate'] = date('Ymd');
        $member_data['InterfaceType'] = 'N';

        $member_data['Cus_Nm'] = $member['name'];
        $member_data['Di_No'] = $member['code'];
        $member_data['Birth_Day'] = $member['birthday'];
        $member_data['Sex'] = $member['sex_div']='W'? 'F':$member['sex_div'];
        $member_data['Hp_No'] = $member['pcs'];
        $member_data['Zip_Cd'] = $member['zip'];
        $member_data['Addr'] = $member['addr1'];
        $member_data['Addr1'] = $member['addr2'];
        $member_data['Web_Id'] = $member['id'];

        return $member_data;
    }

    /**
     * desc: order 정보를 가져와 샘플에 맞는 데이터 생성
     **/
    private function setOrderData($order) {
        $order_data = array();

        $order_data['Command'] = $this->type.'0';
        $order_data['OwnerID'] = 'SGDATA';
        $order_data['InterfaceDate'] = date('Ymd');
        $order_data['InterfaceType'] = 'N';

        if($this->type == 'R') {
            if($order['status'] == 'CC') {
                $order_data['ErpOrderID'] = 'C_'.$order['oid'];
                $order_data['ReasonID'] = '1';
            }else if($order['status'] == 'RI' || $order['status'] == 'RD') {
                $order_data['ErpOrderID'] = 'R_'.$order['oid'];
                $order_data['ReasonID'] = '4';
            }else if($order['status'] == 'EI' || $order['status'] == 'ED'){
                $order_data['ErpOrderID'] = 'E_'.$order['oid'];
                $order_data['ReasonID'] = '3';
            }
        }else {
            $order_data['ErpOrderID'] = $order['oid'];
        }

        if($this->type == 'R' && $order['claim_group'] > 0) {
            //OrgOrderSeq 구하기

            $sql = "SELECT od_ix FROM shop_order_detail WHERE oid = '".$order['oid']."' AND gid = '".$order['gid']."' AND invoice_no = '".$order['org_invoice']."' AND claim_group = 0 ";
            $this->slave_db->query($sql);
            $this->slave_db->fetch();

            $orderSeq = $this->slave_db->dt['od_ix'];

            if(empty($orderSeq)) {
                $orderSeq = $order['od_ix'];
            }

        }else {
            $orderSeq = $order['od_ix'];
        }

        $order_data['OrderSeq'] = $orderSeq;
        $order_data['OrgOrderSeq'] = $order['claim_delivery_od_ix'];
        $order_data['OrderType'] = '1';
        $order_data['ItemID'] = $order['gid'];
        $order_data['ContentType'] = '1';
        $order_data['OrderQty'] = $order['pcnt'];
        $order_data['SCustName'] = $order['rname'];
        $order_data['Szip'] = $order['zip'];
        $order_data['Saddr1'] = $order['addr1']; //C
        $order_data['Saddr2'] = $order['addr2']; //C
        $order_data['SPhone'] = $order['rtel'];
        $order_data['SCellPhone'] = $order['rmobile'];
        $order_data['CustName'] = $order['bname']; //C
        $order_data['BPhone'] = $order['btel'];
        $order_data['BCellPhone'] = $order['bmobile'];
        $order_data['Description'] = $order['msg']; //C
        $order_data['GiftYn'] = $order['product_type'] == '77' ? 'Y' : 'N';

        if($this->type == 'D') {
            $order_data['GiftMsg'] = ''; //C
            $order_data['PayType'] = '';
            $order_data['SalePrice'] = $order['dcprice'];
            $order_data['DeliYN'] = $order['delivery_price'] > 0 ? 'Y' : 'N';
            $order_data['SumPrice'] = $order['payment_price'] - $order['delivery_price'];
            //$order_data['Coupon'] = $order['dc_price'] ? $order['dc_price'] : 0;

            $order_data['PayConCd'] = '1';

            //해외배송구분
            $sql = "SELECT  CASE mall_templete_type WHEN '국문' THEN 'N' ELSE 'Y' END as overseasYn FROM shop_shopinfo WHERE mall_ix = '".$order['mall_ix']."' AND mall_div = 'B'";
            $this->slave_db->query($sql);
            $this->slave_db->fetch();
            $order_data['OverseasYn'] = $this->slave_db->dt['overseasYn'];

            if($order['claim_group'] > 0 && $order['claim_delivery_od_ix'] > 0) {
                //교환에대한 배송일경우
                //SumPirce를 교환 상품에 대해서만 합계를 내야함

                $sql = "SELECT sum(pt_dcprice) as sumprice FROM `shop_order_detail` where oid = '".$order['oid']."' and claim_group = '".$order['claim_group']."'and claim_delivery_od_ix > 0";
                $this->slave_db->query($sql);
                $this->slave_db->fetch();
                $order_data['SumPrice'] = $this->slave_db->dt['sumprice'];

                //SG Data 요청으로 쿠폰과 적립금은 0원처리
                $order_data['CouponList'] = "";
                $order_data['ItemCouponNo'] = "";
                $order_data['ItemCouponAmt'] = 0;
                $order_data['Mileage'] = 0;
            }else {
                $order_data['Mileage'] = $order['reserve'];
                //쿠폰사용내역조회
                $sql = "SELECT * FROM shop_order_detail_discount WHERE od_ix = '".$order['od_ix']."' and dc_type = 'CP'";
                $this->slave_db->query($sql);
                $ctotal = $this->slave_db->total;


                if($ctotal > 0) {
                    //상품 쿠폰정보 구하기
                    $sql = "SELECT
                                *
                            FROM
                                shop_cupon_regist cr
                            LEFT JOIN shop_cupon_publish cp ON (
                                cr.publish_ix = cp.publish_ix
                            )
                            LEFT JOIN shop_cupon c ON(
                                cp.cupon_ix = c.cupon_ix
                            )
                            LEFT JOIN shop_order_detail_discount odd ON (
                              odd.dc_ix = cr.regist_ix
                            )
                            WHERE
                                odd.od_ix = '".$order['od_ix']."'";

                    $this->slave_db->query($sql);
                    $couponInfos = $this->slave_db->fetchall();
                    $couponTotal = $this->slave_db->total;

                    $z = 1;
                    for($i=0; $i<$couponTotal; $i++) {
                        $couponInfo = $couponInfos[$i];
                        if($couponInfo['cupon_div'] == 'G') {
                            $order_data['ItemCouponNo'] = $couponInfo['cupon_no'];
                            $order_data['ItemCouponAmt'] = $couponInfo['dc_price'];
                        }

                        if($couponInfo['cupon_div'] == 'C'){
                            //장바구니 쿠폰 개별 사용 금액 정보 전달
                            $sql = "SELECT dc_price FROM shop_order_detail_discount WHERE od_ix= '".$order['od_ix']."' AND dc_ix = '".$couponInfo['dc_ix']."'";
                            $this->slave_db->query($sql);
                            $cartCouponData = $this->slave_db->fetch();
                            $order_data['OrdCouponNo'] = $couponInfo['cupon_no'];
                            $order_data['OrdCouponAmt'] = $cartCouponData['dc_price'];

                            $sql = "SELECT sum(dc_price) as sum_price FROM shop_order_detail_discount WHERE oid= '".$order['oid']."' AND dc_ix = '".$couponInfo['dc_ix']."'";
                            $this->slave_db->query($sql);
                            $sum_price = $this->slave_db->fetch();


                            $order_data['CouponList']['CouponInfo'][$i]['CouponCd'] = $z;
                            $order_data['CouponList']['CouponInfo'][$i]['CouponNm'] = $couponInfo['publish_name'];
                            $order_data['CouponList']['CouponInfo'][$i]['CouponNo'] = $couponInfo['cupon_no'];
                            $order_data['CouponList']['CouponInfo'][$i]['CouponAmt'] = $sum_price['sum_price'];
                            $z++;
                        }

                    }

                }else {
                    $order_data['CouponList'] = "";
                    $order_data['ItemCouponNo'] = "";
                    $order_data['ItemCouponAmt'] = 0;
                }
            }

        }else if($this->type == 'R') {
            $order_data['OrgOrderID'] = $order['oid']; // 반품생성된 주문번호
            $order_data['ReasonName'] = '';
            $order_data['PrepayYn'] = '';
            $order_data['PickYn'] = $order['send_type'] == '2' ? 'Y' : 'N';
        }

        return $order_data;

    }

    /**
     * desc: xml 파일 저장
     * params: xml 데이터
     **/
    private function saveXML($xml) {
        $dom = new DOMDocument('1.0');
        $dom->loadXML($xml);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->encoding = 'euc-kr';
        $dom->save($this->ipath.$this->filename);
        //백업용도
        copy($this->ipath.$this->filename, $this->bpath.$this->filename);
        $this->complete();
    }

    /**
     * desc: order 정보 완료처리
     **/
    private function complete() {
        $this->process_data = array_values($this->process_data);
        for($i=0; $i<$this->process_total; $i++) {
            $data = $this->process_data[$i];

            if($this->type == 'D') {
                $sql = "update shop_order_detail set erp_link_date = now() where od_ix = '".$data['od_ix']."'";
                $this->db->query($sql);
                set_order_status($data['oid'],'','ERP - 주문발주 처리','','',$data['od_ix'],$data['pid'], 'ERP');
            }else if($this->type == 'R') {
                $sql = "update shop_order_detail set erp_refund_link_date = now() where od_ix = '".$data['od_ix']."'";
                $this->db->query($sql);
                if($data['status'] == 'CC') {

                }else if($data['status'] == 'RI'){
                    $sql="update shop_order_detail set status = 'RD' , update_date = NOW() where od_ix='".$data['od_ix']."'";
                    $this->db->query($sql);

                    set_order_status($data['oid'],'RD','ERP - 반품상품배송중 처리','','',$data['od_ix'],$data['pid'], 'ERP');
                }else if($data['status'] == 'EI'){
                    $sql="update shop_order_detail set status = 'ED' , update_date = NOW() where od_ix='".$data['od_ix']."'";
                    $this->db->query($sql);

                    set_order_status($data['oid'],'ED','ERP - 교환상품배송중 처리','','',$data['od_ix'],$data['pid'], 'ERP');
                }
            }else if($this->type == 'M') {
                $sql = "update common_user set erp_link_date = NOW() where code = '".$data['code']."'";
                $this->db->query($sql);
            }

        }

        unset($this->process_data);
        unset($this->process_total);
    }

    /**
     * desc: writeXML 함수전용 sample 가져오기
     * params: sample (filename)
     **/
    private function readSample($sample) {
        try{
            if(is_dir($this->spath)) {
                $rfile = $this->spath.$sample;
                if(is_file($rfile)) {
                    $xml = simplexml_load_file($rfile);
                    return $xml;
                }
            }
        }catch(Exception $e){
            $error['msg'] = json_encode((array)$e);
            $this->error($error);
        }
    }

    /**
     * desc: db에 로그남기기
     * params: error data
     **/
    private function error($error) {

        $this->is_error = true;

        $sql = "INSERT 
                  INTO sgdata_log (type, cmd, data, msg, regdate) 
                  VALUES (
                    '".$this->type."',
                    '".$this->cmd."',
                    '".$error['data']."',
                    '".$error['msg']."',
                    NOW()
        ); ";

        $this->db->query($sql);

    }

    /**
     * desc: out 폴더 읽어와 처리
     * params:
     **/
    private function readXML() {
        if(is_dir($this->opath)) {
            $dh = opendir($this->opath);

            //정렬을 위해 먼저 파일 수집
            while(($this->file = readdir($dh)) !== false) {
                if(!is_file($this->opath.$this->file)) continue;
                $file[] = $this->file;
            }
            if(count($file) > 1) {
                //상품이 재고보다 먼저 해야하므로 파일 알파뱃순으로  정렬
                sort($file);
            }
            print_r($file);
            $z = 0;
            while(!empty($file[$z])) {
                $this->file =  $file[$z];

                $this->readfile = $this->opath.$this->file;

                //오픈초반 백업용도
                if(file_exists($this->readfile)) {
                    copy($this->readfile, $this->bpath . $this->file);
                }

                if(is_file($this->readfile)) {

                    $xml = simplexml_load_file($this->readfile);
                    $storeCode = '';

                    foreach($xml as $key => $value) {
                        $this->cmd = $value->Command;

                        switch ($this->cmd) {
                            case 'I1' : //송장발행 정보
                                $this->invoice($value);
                                break;
                            case 'O1' : //통합배송 출고 완료정보
                                $this->delivery($value);
                                break;
                            case 'R1' : //반품완료정보
                                $this->refund($value);
                                break;
                            case 'S1' : //재고정보
                                $this->stock($value);
                                break;
                            case 'G1' : //상품정보
                                $this->goods($value);
                                break;
                            case 'M1' : //매장정보
                                $storeCode .= $value->ShopCd.',';
                                $this->store($value);
                                break;
                            default :

                                $error['data'] = json_encode($value);
                                $error['msg']  = '일치하지 않는 커맨드';

                                $this->error($error);
                                break;
                        }

                    }

                    //매장정보 업데이트시
                    if(!empty($storeCode)) {
                        $storeCode = substr($storeCode, 0, -1);
                        $sql = "UPDATE barrel_store_info set view_yn = 'N' where store_code not in ($storeCode)";
                        $this->db->query($sql);
                    }

                    if($this->is_error) {
                        if(file_exists($this->readfile)) {
                            copy($this->readfile, $this->epath . $this->file);
                        }
                    }

                    //처리된 파일은 제거
                    if(file_exists($this->readfile)){
                        unlink($this->readfile);
                    }

                }

                $z++;
            }
        }
    }

    /**
     * desc: 매장등록
     * params: store data
     **/
    private function store($data) {
        try {
            $store_code = $data->ShopCd;
            $store_name = $data->ShopNm;
            $city_code = $data->RegionCd;
            $area_code = $data->LocCd;
            $store_address1 = $data->Addr1;
            $store_address2 = $data->Addr2;
            $open_time = $data->WdTm;
            $store_tel = $data->TelNo;
            $bus = $data->TraInfo;
            $subway = $data->TraInfo1;

            if(empty($store_code)) {
                throw new Exception("ERP store - empty store code");
            }

            //스토어 존재 체크
            $sql = "SELECT COUNT(*) as cnt FROM barrel_store_info WHERE store_code = '$store_code'";
            $this->slave_db->query($sql);
            $this->slave_db->fetch();
            $store_cnt = $this->slave_db->dt['cnt'];

            if($store_cnt > 0) {
                //update
                $sql = 'UPDATE barrel_store_info 
                        SET store_name = "'.$store_name.'", city_code = "'.$city_code.'", area_code = "'.$area_code.'", store_address1 = "'.$store_address1.'", store_address2 = "'.$store_address2.'", store_tel = "'.$store_tel.'", bus = "'.$bus.'", subway = "'.$subway.'", open_time = "'.$open_time.'", view_yn = "Y" 
                        WHERE store_code = "'.$store_code.'"; ';
            }else {
                //insert
                $sql = 'INSERT INTO barrel_store_info(store_code, store_name, open_time, store_address1, store_address2, city_code, area_code, store_tel, bus, subway, view_yn, regdate) 
                        VALUES("'.$store_code.'", "'.$store_name.'","'.$open_time.'", "'.$store_address1.'", "'.$store_address2.'", "'.$city_code.'", "'.$area_code.'", "'.$store_tel.'", "'.$bus.'", "'.$subway.'", "Y", now());';
            }
            $result = $this->db->query($sql);
            if(!$result) {
                throw new Exception('ERP - store - fail execute SQL');
            }

        }catch(Exception $e){
            $error['data'] = json_encode($data);
            $error['msg']  = json_encode((array)$e);

            $this->error($error);
        }
    }


    /**
     * desc: 품목등록
     *       ItemID =  스타일(ID) + 칼라 + 사이즈
     * params: Goods data
     **/
    private function goods($data) {
        try{
            //상품정보
            $itype = $data->InterfaceType; // N(신규) or U(수정)
            $id = $data->ItemID; // 상품코드
            $name = $data->ItemName; // 상품이름
            $style = $data->ItemSty; // 스타일
            $size = $data->ItemSize; // 사이즈
            $color = $data->ItemCol; // 칼라
            $season = $data->ItemSesn; //시즌
            $brand = $data->ItemBrd; //브랜드
            $weight = (float)$data->ItemWeight / 1000; //무게 g로 넘어와 kg으로 변경

            $sellprice = $data->TagPrice; //소비자가

            $admin = '';

            $sql = "SELECT count(*) as cnt FROM inventory_goods WHERE gid = '$id'";
            $this->slave_db->query($sql);
            $this->slave_db->fetch();
            $cnt = $this->slave_db->dt['cnt'];

            if($cnt == 0) {
                $sql = "insert into inventory_goods (
                          gid, gname, gcode, barcode, basic_unit, order_basic_unit, item_account, admin_type, admin, brand, style, color, size, weight,season,is_use, status, editdate, regdate
                        ) 
                        values (
                          '$id', '".addslashes($name)."', '$id', '$id','1', '1', '1', 'A', '$admin', '$brand', '$style','$color', '$size', '$weight', '$season', 'Y', '1', now(), now()
                        );";
                $this->db->query($sql);

                $sql = "insert into inventory_goods_unit (
                          gid, unit, change_amount, weight,sellprice, editdate, regdate
                        )
                        values (
                          '$id', '1', '1', '$weight', '$sellprice', now(), now()
                        );";
                $this->db->query($sql);
            }else {
                $sql = "update inventory_goods set gname = '".addslashes($name)."', brand = '$brand', style = '$style', color = '$color', size = '$size', weight = '$weight', season = '$season' where gid = '$id';";
                $this->db->query($sql);

                $sql = "update inventory_goods_unit set sellprice = '$sellprice', weight = '$weight' where gid = '$id'";
                $this->db->query($sql);
            }

        }catch(Exception $e) {

            $error['data'] = json_encode($data);
            $error['msg']  = json_encode((array)$e);

            $this->error($error);
        }


    }

    /**
     * desc: 재고 정보 DB 데이터화
     * params: stock data
     **/
    private function stock($data) {
        $gid = $data->ItemID; // 품목코드
        $erp_stock = $data->StockQty; // 수량
        $update_type = $data->UpdtType; // 업데이트 타입
        $use_flag = $data->UseFlag; // 품절표기

        try{

            if(empty($gid)) {
                throw new Exception("ERP stock - gid값 누락");
            }


            if(empty($erp_stock) && empty($use_flag)) {
                throw new Exception("ERP stock - 재고값 누락");
            }

            if(empty($update_type)) {
                $update_type = 'B';
            }

            $sql = "INSERT INTO tmp_sgdata_stock(gid, stock, process_yn, update_type, use_flag, regdate, editdate) VALUES('$gid', '$erp_stock', 'N', '$update_type', '$use_flag' ,now(), now())";
            $result = $this->db->query($sql);

            if(!$result) {
                throw new Exception("ERP stock - 재고업데이트 실패(DB)");
            }
        }catch(Exception $e){
            $error['data'] = json_encode($data);
            $error['msg']  = json_encode((array)$e);

            $this->error($error);
        }

    }
    /**
     * desc: 재고 프로세스
     * params:
     **/
    public function updateStock() {

        try {
            $sql = "SELECT * FROM tmp_sgdata_stock WHERE process_yn = 'N'";

            $this->slave_db->query($sql);
            $this->slave_db->fetchall();
            $total = $this->slave_db->total;
            if($total > 0) {
                $part = 1000;
                $loop = (int)($total / $part) + 1;

                for ($i = 0; $i < $loop; $i++) {
                    $sql ="SELECT * FROM tmp_sgdata_stock WHERE process_yn = 'N' LIMIT $part";
                    $this->slave_db->query($sql);
                    $process_data = $this->slave_db->fetchall();
                    $process_total = $this->slave_db->total;

                    for ($j = 0; $j < $process_total; $j++) {
                        $data = $process_data[$j];
                        $idx = $data['idx'];
                        $gid = $data['gid'];
                        $erp_stock = $data['stock'];
                        $use_flag = $data['use_flag']; // 품절처리 여부 Y : 품절, N : 품절해제 , 빈값 : 재고처리진행
                        $update_type = $data['update_type']; // 갱신구분 A : 일괄적용, B : 부분적용
                        $order_detail['pcnt'] = 0;
                        $order_detail['gid'] = $gid;
                        $order_detail['ps_ix'] = 1;
                        $order_detail['pi_ix'] = 1;

                        if(!empty($use_flag)){
                            //품절처리진행
                            $option_soldout = 0;

                            if($use_flag == 'Y') {
                                $option_soldout = 1;
                                //사은품 일 경우
                                $sql = "update shop_product set state = '0' where id in (
                                      select temp.id from ( 
                                        select p.id from shop_product p left join shop_product_addinfo pa on(p.id = pa.pid) where gid_text = '$gid' and product_type = '77'
                                        ) as temp
                                    ) 
                                    and product_type = '77' 
                                    and state = '1'";
                                $this->db->query($sql);

                                $sql = "update shop_product_global set state = '0' where id in (
                                      select temp.id from ( 
                                        select p.id from shop_product p left join shop_product_addinfo pa on(p.id = pa.pid) where gid_text = '$gid' and product_type = '77'
                                        ) as temp
                                    ) 
                                    and product_type = '77' 
                                    and state = '1'";
                                $this->db->query($sql);

                            }else {
                                //사은품 일 경우
                                $sql = "update shop_product set state = '1' where id in (
                                      select temp.id from ( 
                                        select p.id from shop_product p left join shop_product_addinfo pa on(p.id = pa.pid) where gid_text = '$gid' and product_type = '77'
                                        ) as temp
                                    ) 
                                    and product_type = '77' 
                                    and state = '0'";
                                $this->db->query($sql);

                                $sql = "update shop_product_global set state = '1' where id in (
                                      select temp.id from ( 
                                        select p.id from shop_product p left join shop_product_addinfo pa on(p.id = pa.pid) where gid_text = '$gid' and product_type = '77'
                                        ) as temp
                                    ) 
                                    and product_type = '77' 
                                    and state = '0'";
                                $this->db->query($sql);
                            }

                            //상품 옵션의 등록된 품목코드 품절처리
                            $sql = "update shop_product_options_detail set option_soldout = $option_soldout where option_gid = '$gid'";
                            $this->db->query($sql);

                            $sql = "update shop_product_options_detail_global set option_soldout = $option_soldout where option_gid = '$gid'";
                            $this->db->query($sql);


                        }else {
                            //재고처리진행
                            $sql = "SELECT IFNULL(sum(stock),0) as stock FROM inventory_product_stockinfo where gid='" . $order_detail['gid'] . "' and ps_ix=1 group by gid, ps_ix";
                            $this->slave_db->query($sql);
                            $this->slave_db->fetch();
                            $stock = $this->slave_db->dt['stock'];
                            if (empty($stock)) {
                                $stock = 0;
                            }
                            if($update_type == 'A') {
                                //erp 물량 대로 업데이트
                                $this->updateSellingCnt($gid);
                                $sql = "select sell_ing_cnt from inventory_goods_unit where gid = '".$gid."'";
                                $this->slave_db->query($sql);
                                $this->slave_db->fetch();
                                $selling_cnt = $this->slave_db->dt['sell_ing_cnt'];
                                if($selling_cnt < 0 ){
                                    $selling_cnt = 0;
                                }

                                $update_stock = $erp_stock + $selling_cnt;


                                if($update_stock == $stock) {
                                    $input = '';
                                }else if($update_stock > $stock){
                                    $h_div = 1;// 1:입고, 2: 출고
                                    $input = 'input';
                                    if($stock < 0 ){
                                        $order_detail['pcnt'] = $update_stock + ($stock * -1);
                                    }else {
                                        $order_detail['pcnt'] = $update_stock - $stock;
                                    }
                                }else {
                                    if($stock < 0 ){
                                        $h_div = 1;
                                        $input = 'input';
                                        $order_detail['pcnt'] = $update_stock + ($stock * -1);
                                    }else {
                                        $h_div = 2;
                                        $input = 'output';
                                        $order_detail['pcnt'] = $stock - $update_stock;
                                    }
                                }
                                if($input != '') {
                                    $this->updateGoodsItemStockInfo($h_div, $order_detail, 'ERP 실재고 반영 - ['.$gid.'] Update Type : A, '.$input.', '.$order_detail['pcnt'], $update_type);
                                }

                            }else if($update_type == 'B') {
                                //현재고에 더하기
                                if ($erp_stock > 0) {
                                    $h_div = 1; // 1:입고, 2: 출고
                                    $order_detail['pcnt'] = $erp_stock;
                                    $this->updateGoodsItemStockInfo($h_div, $order_detail, 'ERP 실재고 반영 - ['.$gid.'] Update Type : B, input, '.$order_detail['pcnt'], $update_type);
                                } else if ($erp_stock < 0) {
                                    $h_div = 2; // 1:입고, 2: 출고
                                    $order_detail['pcnt'] = $erp_stock * -1;
                                    $this->updateGoodsItemStockInfo($h_div, $order_detail, 'ERP 실재고 반영 - ['.$gid.'] Update Type : B, output, '.$order_detail['pcnt'], $update_type);
                                }

                            }
                        }
                        $sql = "UPDATE tmp_sgdata_stock set process_yn = 'Y' WHERE idx = '$idx'";
                        $this->db->query($sql);
                    }
                    unset($process_data);
                    unset($data);
                    unset($order_detail);
                }
            }

        }catch(Exception $e){
            $error['data'] = json_encode($data);
            $error['msg']  = json_encode((array)$e);

            $this->error($error);
        }
    }

    /**
     * desc: 재고 프로세스
     * params: 입,출고 / 주문정보, 메세지, 재고업데이트 타입(A : 덮어씌우기, B : 현재기준 +,-)
     **/
    protected function updateGoodsItemStockInfo($h_div, $order_detail, $msg, $type) {
        //입고 보관장소
        $sql = "select pi.pi_ix, pi.company_id, pi.place_name, ps.ps_ix, ps.section_name
                                          from	inventory_place_section ps
                                          left join inventory_place_info pi on pi.pi_ix = ps.pi_ix
                                          where ps.ps_ix = '1'";

        $this->slave_db->query($sql);
        $this->slave_db->fetch();
        $order_item_info = $this->slave_db->dt;

        $sql = "select g.gid, gu.unit, g.standard,
                  '" . $order_detail['pcnt'] . "' as amount ,
                  '" . $order_detail['psprice'] . "' as price ,
                  '" . $order_detail['pt_dcprice'] . "' as pt_dcprice ,
                  '" . $order_item_info['company_id'] . "' as company_id,
                  '" . $order_item_info['pi_ix'] . "' as pi_ix,
                  '" . $order_item_info['ps_ix'] . "' as ps_ix
                  from inventory_goods g , inventory_goods_unit gu
                  where g.gid = gu.gid and g.gid = '" . $order_detail['gid'] . "'";
        // 출고가격을 어떻게 처리 할지?
        // 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ...
        $this->slave_db->query($sql);
        $delivery_iteminfo = $this->slave_db->fetchall('object');

        $item_info['pi_ix'] = $order_item_info['pi_ix'];
        $item_info['ps_ix'] = $order_item_info['ps_ix'];
        $item_info['company_id'] = $order_item_info['company_id'];
        $item_info['h_div'] = $h_div; // 2: 출고 1:입고
        $item_info['vdate'] = date("Ymd");
        $item_info['oid'] = $order_detail['oid'];
        $item_info['msg'] = $msg;
        $item_info['h_type'] = '01';//01; 상품매출
        $item_info['charger_name'] = $_SESSION['admininfo']['charger'];
        $item_info['charger_ix'] = $_SESSION['admininfo']['charger_ix'];
        $item_info['detail'] = $delivery_iteminfo;

        UpdateGoodsItemStockInfo($item_info, $this->db);

        if($type != 'A') {
            $this->updateSellingCnt($order_detail['gid']);
        }

    }

    /**
     * desc: 판매진행재고 갱신
     * params: 품목코드
     **/
    protected function updateSellingCnt($gid) {
        //판매진행재고 업데이트

        $this->slave_db->query("select gu_ix from inventory_goods_unit where gid = '".$gid."'");
        $this->slave_db->fetch();
        $gu_ix = $this->slave_db->dt['gu_ix'];

        $this->slave_db->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where gu_ix = '".$gu_ix."' and status in ('IR','IC','DR','DD')");
        $this->slave_db->fetch();
        $sell_ing_cnt = $this->slave_db->dt['sell_ing_cnt'];

        $sql = "select od.id as opnd_ix ,pid from ".TBL_SHOP_PRODUCT." p inner join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) where p.stock_use_yn='Y' and option_gid = '".$gid."' ";
        $this->slave_db->query($sql);
        if($this->slave_db->total){
            $option_dt_info = $this->slave_db->fetchall();
            $current_pid = '';
            $before_pid = '';

            for($j=0;$j<count($option_dt_info);$j++){
                $current_pid = $option_dt_info[$j]['pid'];
                $this->db->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = '".$sell_ing_cnt."' where id = '".$option_dt_info[$j]['opnd_ix']."' ");
                $this->db->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_GLOBAL." set option_sell_ing_cnt = '".$sell_ing_cnt."' where id = '".$option_dt_info[$j]['opnd_ix']."' ");

                if($before_pid != $current_pid) {
                    //$this->slave_db->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where pid = '".$option_dt_info[$j]['pid']."' and status in ('IR','IC','DR','DD')");
                    $this->slave_db->query("select sum(option_sell_ing_cnt) as sell_ing_cnt from shop_product_options_detail where pid = '".$current_pid."' and option_soldout != '1'   ");
                    $this->slave_db->fetch();
                    $p_sell_ing_cnt = $this->slave_db->dt['sell_ing_cnt'];

                    $this->db->query("UPDATE ".TBL_SHOP_PRODUCT."  Set sell_ing_cnt = '".$p_sell_ing_cnt."'  WHERE id = '".$option_dt_info[$j]['pid']."'");
                    $this->db->query("UPDATE ".TBL_SHOP_PRODUCT_GLOBAL."  Set sell_ing_cnt = '".$p_sell_ing_cnt."'  WHERE id = '".$option_dt_info[$j]['pid']."'");
                }
                $before_pid = $option_dt_info[$j]['pid'];
            }
        }

        $this->db->query("update inventory_goods_unit set sell_ing_cnt = '".$sell_ing_cnt."' where gid = '".$gid."' ");

    }

    /**
     * desc: 주문 송장번호 DB 데이터화
     * params: order data
     **/
    private function invoice($data) {
        $oid = $data->ErpOrderID; // Unique 주문아이디(index)
        $invoice_no = $data->InvoiceNo; // 송장번호
        $itemNorm = $data->ItemNorm; // 미출여부
        $od_ix = $data->OrderSeq;
        try{

            if(empty($oid)) {
                throw new Exception("ERP invoice - None oid");
            }

            if(empty($invoice_no)) {
                throw new Exception("ERP invoice - None invoice");
            }

            if(empty($od_ix)) {
                throw new Exception("ERP invoice - None od_ix");
            }

            $sql = "INSERT INTO tmp_sgdata_invoice(oid, od_ix, invoice_no, itemNorm, process_yn, regdate, editdate) VALUES('$oid', '$od_ix', '$invoice_no', '$itemNorm','N', now(), now())";
            $result = $this->db->query($sql);

            if(!$result) {
                throw new Exception("ERP invoice - 송장처리 추가 실패(DB)");
            }
        }catch(Exception $e){
            $error['data'] = json_encode($data);
            $error['msg']  = json_encode((array)$e);

            $this->error($error);
        }
    }

    /**
     * desc: 주문 송장번호 업데이트 루프
     * params:
     **/
    public function invoiceProcess() {

        $sql = "SELECT * FROM tmp_sgdata_invoice WHERE process_yn = 'N' order by regdate LIMIT 5000;";
        $this->slave_db->query($sql);
        $datas = $this->slave_db->fetchall();
        $total = $this->slave_db->total;
        $this->cmd = 'I1';
        if($total > 0) {

            for($i=0; $i<$total; $i++) {
                $data = $datas[$i];

                $this->updateInvoice($data);

                $sql = "update tmp_sgdata_invoice set process_yn = 'Y', editdate = NOW() where idx = '".$data['idx']."'";
                $this->db->query($sql);
            }

        }
    }

    /**
     * desc: 주문 송장번호 업데이트 프로세스
     * params: 송장정보
     **/
    private function updateInvoice($data) {
        try{

            $oid = $data['oid'];
            $od_ix = $data['od_ix'];
            $invoice = $data['invoice_no'];
            $itemNorm = $data['itemNorm'];

            $sql = "SELECT *  FROM shop_order_detail WHERE oid = '$oid' and od_ix = '$od_ix';";
            $this->db->query($sql);
            $oinfo = $this->db->fetch();
            $quick = 18;

            if($oinfo['mall_ix'] == '20bd04dac38084b2bafdd6d78cd596b2') {
                $quick = 1;
            }

            if($itemNorm == 'N') {

                if(trim($invoice) == '' || empty($invoice)) {
                    throw new Exception("ERP invoice - None invoice_no");
                }

                //배송중 처리
                if(strpos($oinfo['invoice_no'], ',') !== false) {
                    //,가 이미 존재할때
                    $iArr = explode(',',$oinfo['invoice_no']);

                    if(!in_array($invoice, $iArr)) {
                        //존재하지 않을 시 붙이기
                        $invoice = $oinfo['invoice_no'].','.$invoice;
                    }
                }else {
                    if($oinfo['invoice_no'] != $invoice && !empty($oinfo['invoice_no'])) {
                        $invoice = $oinfo['invoice_no'].','.$invoice;
                    }
                }
                $sql = "update shop_order_detail set invoice_no = '$invoice', quick = $quick where oid = '$oid' and od_ix = '$od_ix';";
                $this->db->query($sql); //추후 테스트

                if($oinfo['status'] == 'IC') {
                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_READY."' , update_date = NOW(), dr_date= NOW()  where  od_ix='".$oinfo['od_ix']."'";
                    $result = $this->db->query($sql);

                    if($result){
                        set_order_status($oinfo['oid'],'DR','ERP invoice - 배송준비중 처리완료', '','',$oinfo['od_ix'], $oinfo['pid'], 'ERP');
                    }else {
                        throw new Exception("ERP invoice - 배송준비중 처리 실패(DB)");
                    }
                }else if($oinfo['status'] == 'CA'){
                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_READY."' , update_date = NOW(), dr_date= NOW()  where  od_ix='".$oinfo['od_ix']."'";
                    $result = $this->db->query($sql);

                    if($result){
                        set_order_status($oinfo['oid'],'DR','ERP invoice - 취소철회 처리완료', '','',$oinfo['od_ix'], $oinfo['pid'], 'ERP');
                    }else {
                        throw new Exception("ERP invoice - 취소철회 실패(DB)");
                    }
                }else {
                    if($oinfo['status'] != 'DR' && $oinfo['status'] != 'CC' ) {
                        throw new Exception("ERP invoice - 배송준비중 처리 실패(STATUS, DATA)");
                    }
                }
            }else {
                if($itemNorm == 'Y') {
                    $msg = "취소";
                    $reason_code = "ERPC";
                }else if($itemNorm == 'H') {
                    $msg = "보류";
                    $reason_code = "ERPH";
                }
                if($oinfo['status'] != 'CC' && $oinfo['status'] != 'CA') {
                    //입금확인 or 배송중(교환예정상품)
                    if($oinfo['status'] == 'IC' || $oinfo['status'] == 'DR'){
                        $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_CANCEL_APPLY."'  , need_claim_yn = 'Y',ca_date = NOW()  , update_date = NOW()  where od_ix='".$oinfo['od_ix']."'";
                        $result = $this->db->query($sql);

                        if($result) {
                            set_order_status($oinfo['oid'], 'CA', 'ERP invoice - '.$msg.'요청 처리완료', '', '',$oinfo['od_ix'],$oinfo['pid'], $reason_code);
                        }else {
                            throw new Exception("ERP invoice - ".$msg."완료 처리 실패(DB)");
                        }
                    }else {
                        throw new Exception("ERP invoice - ".$msg."완료 처리 실패(DATA)");
                    }
                }
            }
        }catch(Exception  $e) {
            $error['data'] = json_encode($data);
            $error['msg'] = json_encode((array)$e);

            $this->error($error);
        }
    }

    /**
     * desc: 주문 반품회수 완료처리 (반품상품배송중 => 반품회수완료) / 일괄처리량이 많지 않아 즉시처리
     * params: 주문 데이터
     **/
    private function refund($data) {
        $oid = $data->ErpOrderID;
        $return_product_state = $data->ContentType; // 1 : 양품, 2 : 불량품
        $od_ix = $data->OrderSeq;

        try {

            $sql = "SELECT * FROM shop_order_detail WHERE oid = '$oid' AND od_ix = '$od_ix' AND status in ('RI', 'RD', 'EI', 'ED')";
            $this->slave_db->query($sql);
            $this->process_data = $this->slave_db->fetchall();
            $this->process_total = $this->slave_db->total;

            if($this->process_total > 0) {

                for ($i = 0; $i < $this->process_total; $i++) {
                    $order_details = $this->process_data[$i];

                    $change_status = 'RT'; //반품회수완료

                    if ($order_details['status'] == 'EI' || $order_details['status'] == 'ED') {
                        $change_status = 'ET'; // 교환회수완료
                    }

                    $sql = "update " . TBL_SHOP_ORDER_DETAIL . " set status = '" . $change_status . "' , return_product_state = '" . $return_product_state . "' ,  update_date = NOW() where od_ix='" . $order_details['od_ix'] . "'";
                    $result = $this->db->query($sql);

                    if ($result) {
                        set_order_status($order_details['oid'], ORDER_STATUS_RETURN_ACCEPT, 'ERP - ' . $order_details['od_ix'] . ' 반품완료처리 (반품배송중 -> 반품배송완료)', '', '', $order_details['od_ix'], $order_details['pid'], 'ERP');

                        //재고처리
                        $sql = "select pi.pi_ix, pi.company_id, pi.place_name, ps.ps_ix, ps.section_name
                                    from inventory_goods g
                                    left join inventory_goods_unit gu on g.gid =gu.gid
                                    left join inventory_place_info pi on pi.pi_ix = '1'
                                    left join inventory_place_section ps on ps.pi_ix = pi.pi_ix and ps.ps_ix = '1'
                                    where gu.gu_ix = '" . $order_details['gu_ix'] . "' ";

                        $this->slave_db->query($sql);

                        if ($this->slave_db->total > 0) {

                            $this->slave_db->fetch();
                            $order_item_info = $this->slave_db->dt;

                            $sql = "select g.gid, gu.unit, g.standard,
                                '" . (!empty($detailCnt) ? $detailCnt : $order_details['pcnt']) . "' as amount ,
                                '" . $order_details['psprice'] . "' as price ,
                                '" . $order_details['pt_dcprice'] . "' as pt_dcprice ,
                                '" . $order_item_info['company_id'] . "' as company_id,
                                '" . $order_item_info['pi_ix'] . "' as pi_ix,
                                '" . $order_item_info['ps_ix'] . "' as ps_ix
                                from inventory_goods g , inventory_goods_unit gu
                                where g.gid = gu.gid and gu.gu_ix = '" . $order_details['gu_ix'] . "'";

                            $this->slave_db->query($sql);
                            $delivery_iteminfo = $this->slave_db->fetchall();

                            $item_info['pi_ix'] = $order_item_info['pi_ix'];
                            $item_info['ps_ix'] = $order_item_info['ps_ix'];
                            $item_info['company_id'] = $order_item_info['company_id'];
                            $item_info['h_div'] = "1"; // 1:입고 2: 출고
                            $item_info['vdate'] = date("Ymd");
                            $item_info['ioid'] = "1" . substr(date("YmdHis"), 1) . "-" . rand(10000, 99999);
                            $item_info['oid'] = $order_details['oid'];
                            $item_info['msg'] = "ERP 반품회수완료 - 입고";
                            $htype = '05';
                            if ($order_details['status'] == 'RD' || $order_details['status'] == 'RI') {
                                $htype = '04';
                            }
                            $item_info['h_type'] = $htype;//01; 상품매출 04:반품, 05:교환
                            $item_info['charger_name'] = $_SESSION[admininfo]["charger"];
                            $item_info['charger_ix'] = $_SESSION[admininfo]["charger_ix"];
                            $item_info['detail'] = $delivery_iteminfo;

                            UpdateGoodsItemStockInfo($item_info, $this->db);
                        }
                    } else {
                        throw new Exception('ERP refund - 반품완료처리 실패(DB)');
                    }
                }
            }

            unset($this->process_total);
            unset($this->process_data);

        }catch(Exception $e){
            $error['data'] = json_encode($data);
            $error['msg'] = json_encode((array)$e);

            $this->error($error);
        }
    }

    /**
     * desc: 출고 완료 ( 배송준비중 => 배송중 ) DB 데이터화
     * params: 주문 데이터
     **/
    private function delivery($data) {

        $oid = $data->ErpOrderID;
        $invoice = $data->InvoiceNo;
        $od_ix = $data->OrderSeq;
        $pcnt = $data->OrderQty;
        $gid = $data->ItemID;
        $itemNorm = $data->ItemNorm;

        try{

            if(empty($oid)) {
                throw new Exception("ERP delivery - oid 값 누락");
            }

            if(empty($invoice)) {
                throw new Exception("ERP delivery - invoice 값 누락");
            }

            if(empty($od_ix)) {
                throw new Exception("ERP delivery - od_ix 값 누락");
            }

            $sql = "INSERT INTO tmp_sgdata_order(oid, od_ix, gid, pcnt, invoice_no, itemNorm, process_yn, regdate, editdate) VALUES('$oid', '$od_ix', '$gid', '$pcnt', '$invoice', '$itemNorm', 'N', now(), now())";
            $result = $this->db->query($sql);

            if(!$result) {
                throw new Exception("ERP delivery - 주문처리 추가 실패(DB)");
            }
        }catch(Exception $e){
            $error['data'] = json_encode($data);
            $error['msg']  = json_encode((array)$e);

            $this->error($error);
        }
    }

    /**
     * desc: 배송중 프로세스 루프
     * params:
     **/
    public function deliveryProcess() {

        $sql = "SELECT * FROM tmp_sgdata_order WHERE process_yn = 'N' order by regdate LIMIT 1000;";
        $this->slave_db->query($sql);
        $datas = $this->slave_db->fetchall();
        $total = $this->slave_db->total;
        $this->cmd = 'O1';
        if($total > 0) {

            for($i=0; $i<$total; $i++) {
                $this->processYn = false;
                $data = $datas[$i];

                $this->updateOrder($data);
                if($this->processYn){
                    $sql = "update tmp_sgdata_order set process_yn = 'Y', editdate = NOW() where idx = '".$data['idx']."'";
                    $this->db->query($sql);
                }
            }

            //send_pull에 od_ix 존재시 push 보내기
            if(!empty($this->send_pull)) {
                $this->sendPush($this->send_pull);
                unset($this->send_pull);
            }
        }

    }

    /**
     * desc: 배송중 프로세스
     * params: 주문 데이터
     **/
    private function updateOrder($data) {
        try {
            $oid = $data['oid'];
            $od_ix = $data['od_ix'];
            $invoice = $data['invoice_no'];
            $itemNorm = $data['itemNorm'];

            if($itemNorm == 'N') {

                $sql = "SELECT * FROM shop_order_detail WHERE  oid = '$oid' AND od_ix = '$od_ix' AND status in ('DR', 'DD');";
                $this->slave_db->query($sql);
                $this->process_data = $this->slave_db->fetchall();
                $this->process_total = $this->slave_db->total;

                if ($this->process_total > 0) {
                    //굿스플로 연동 고려
                    for ($i = 0; $i < $this->process_total; $i++) {
                        $order_details = $this->process_data[$i];
                        $quick = 18;
                        $result = true;

                        if($order_details['mall_ix'] == '20bd04dac38084b2bafdd6d78cd596b2') {
                            //해외몰은 goodsflow 연결 안하고 우체국으로 연동
                            $quick = 1;
                        }else {
                            $sql = "SELECT * FROM sellertool_site_info WHERE site_code = 'goodsflow'";
                            $this->slave_db->query($sql);
                            $goods_flow = $this->slave_db->fetch();

                            if ($goods_flow['api_yn'] == 'Y') {
                                if ($order_details['status'] == ORDER_STATUS_DELIVERY_READY && $quick != '40') {
                                    if (function_exists('sellerToolUpdateOrderStatus')) {
                                        $goodsflow_result = sellerToolUpdateOrderStatus(ORDER_STATUS_DELIVERY_ING, $order_details['od_ix'], '', true, $quick, $invoice);

                                        if ($goodsflow_result != 'success') {
                                            $result = false;
                                        }
                                    }
                                }
                            }
                        }

                        if ($result) {
                            $sql = "UPDATE shop_order_detail SET status = '" . ORDER_STATUS_DELIVERY_ING . "', update_date = NOW(), di_date = NOW() WHERE od_ix = '" . $order_details['od_ix'] . "'";
                            $this->db->query($sql);

                            set_order_status($order_details['oid'], ORDER_STATUS_DELIVERY_ING, 'ERP - ' . $order_details['od_ix'] . ' 출고완료처리 (배송준비중 -> 배송중)', '', '', $order_details['od_ix'], $order_details['pid'], "ERP", $quick, $invoice);

                            //재고차감
                            $sql = "select status, pid, gu_ix, gid, stock_use_yn from " . TBL_SHOP_ORDER_DETAIL . " where od_ix='" . $order_details['od_ix'] . "'  ";
                            $this->slave_db->query($sql);
                            $this->slave_db->fetch();
                            $gu_ix = $this->slave_db->dt['gu_ix'];
                            $gid = $this->slave_db->dt['gid'];


                            $sql = "select pi.pi_ix, pi.company_id, pi.place_name, ps.ps_ix, ps.section_name
                                        from	inventory_place_section ps
                                        left join inventory_place_info pi on pi.pi_ix = ps.pi_ix
                                        where ps.ps_ix = '1'";

                            $this->slave_db->query($sql);
                            $this->slave_db->fetch();
                            $order_item_info = $this->slave_db->dt;

                            $sql = "select g.gid, gu.unit, g.standard,
                                '" . $order_details['pcnt'] . "' as amount ,
                                '" . $order_details['psprice'] . "' as price ,
                                '" . $order_details['pt_dcprice'] . "' as pt_dcprice ,
                                '" . $order_item_info['pi_ix'] . "' as pi_ix,
                                '" . $order_item_info['ps_ix'] . "' as ps_ix
                                from inventory_goods g , inventory_goods_unit gu
                                where g.gid = gu.gid and gu.gu_ix = '" . $gu_ix . "'";

                            // 출고가격을 어떻게 처리 할지?
                            // 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ...
                            $this->slave_db->query($sql);
                            $delivery_iteminfo = $this->slave_db->fetchall();

                            $item_info['pi_ix'] = $order_item_info['pi_ix'];
                            $item_info['ps_ix'] = $order_item_info['ps_ix'];
                            $item_info['company_id'] = $order_item_info['company_id'];
                            $item_info['h_div'] = "2"; // 2: 출고
                            $item_info['vdate'] = date("Ymd");
                            //$item_info[ci_ix] = $_POST["ci_ix"];
                            $item_info['oid'] = $order_details['oid'];
                            $item_info['msg'] = "상품판매 - 출고";//$_POST["etc"];
                            $item_info['h_type'] = '01';//01; 상품매출
                            $item_info['detail'] = $delivery_iteminfo;

                            UpdateGoodsItemStockInfo($item_info, $this->db);

                            $this->updateSellingCnt($gid);

                            //push 보낼 주문수집
                            if (!in_array($order_details['od_ix'], $this->send_pull)) {
                                array_push($this->send_pull, $order_details['od_ix']);
                            }
                            $this->processYn = true;
                        }
                    }
                }
                unset($this->process_total);
                unset($this->process_data);
            }else {
                //미출Y는 아무 처리없이 통과
                $this->processYn = true;
            }

        }catch(Exception $e){
            $error['data'] = json_encode($data);
            $error['msg'] = json_encode((array)$e);

            $this->error($error);
        }
    }

    /**
     * desc: 푸쉬, SMS, Mail 발송
     * params: 주문 데이터
     **/
    private function sendPush($orders) {
        try {
            $sql = "select DISTINCT oid, odd_ix from " . TBL_SHOP_ORDER_DETAIL . " where od_ix in (" . implode(',', $orders) . ") and status='" . ORDER_STATUS_DELIVERY_ING . "'";
            $this->slave_db->query($sql);
            $order_infos = $this->slave_db->fetchall("object");

            for ($i = 0; $i < count($order_infos); $i++) {
                $order = '';
                $mail_info = array();
                $qry = "select o.user_code,
                            od.invoice_no, 
                            od.pname, 
                            od.quick, 
                            odd.oid, 
                            odd.rname, 
                            odd.rtel, 
                            odd.rmobile, 
                            concat(odd.addr1,' ',odd.addr2) as addr, 
                            odd.msg, 
                            odd.zip, 
                            date_format(o.order_date,'%Y-%m-%d') as order_date, 
                            o.bname, 
                            o.bmail, 
                            o.bmobile, 
                            o.payment_price,
                            od.claim_delivery_od_ix,
                            od.claim_group 
                      from shop_order_detail_deliveryinfo odd 
                      left join shop_order o on (odd.oid=o.oid) 
                      left join shop_order_detail od on (o.oid=od.oid) 
                      WHERE odd.oid='" . $order_infos[$i][oid] . "' 
                        and odd.odd_ix='" . $order_infos[$i][odd_ix] . "' 
                        and od.od_ix in (" . implode(',', $orders) . ") 
                        and od.status='" . ORDER_STATUS_DELIVERY_ING . "' ";
                $this->slave_db->query($qry);
                $order = $this->slave_db->fetch();

                $this->slave_db->query("select *, pid as id from " . TBL_SHOP_ORDER_DETAIL . " WHERE oid='" . $order_infos[$i][oid] . "' and status= '" . ORDER_STATUS_DELIVERY_ING . "' AND od_ix IN (" . implode(',', $orders) . ") and product_type not in ('77') ");
                $order_details = $this->slave_db->fetchall("object");


                if ($order_details[0][order_from] == 'self') {

                    $mail_info['mem_name'] = $order['bname'];
                    $mail_info['mem_mail'] = $order['bmail'];
                    $mail_info['mem_id'] = $order['bname'];
                    $mail_info['mem_mobile'] = $order['bmobile'];
                    $mail_info['addr1'] = $order['addr'];
                    $mail_info['rtel'] = $order['rtel'];
                    $mail_info['msg'] = $order['msg'];
                    $mail_info['zip'] = str_replace("-", "", $order['zip']);
                    $mail_info['payment_price'] = $order['payment_price'];
                    $mail_info['invoice'] = $order['invoice_no'];

                    $http_type = (!empty($_SERVER['HTTPS'])) ? 'https://' : 'http://';
                    $mail_info['domain'] = $http_type . $_SERVER['HTTP_HOST'];

                    //$mail_info[pname] = substr($order[pname],0,20);
                    $mail_info['pname'] = mb_substr($order['pname'], 0, 15, "utf-8") . (count($order_details) > 1 ? " 외 " . (count($order_details) - 1) . "건" : "");
                    $mail_info['quick'] = deliveryCompany($order['quick']);
                    $mail_info['msg_code'] = '0301'; // MSG 발송코드 0301 : 상품발송
                    $mail_info['oid'] = $order_details[0]['oid'];
                    $mail_info['type'] = 'erp';

                    $sucess_od_ix = array();

                    for($j=0; $j<count($order_details); $j++) {
                        array_push($sucess_od_ix, $order_details[$j]['od_ix']);
                    }

                    if($order[claim_group] > 0 && $order[claim_delivery_od_ix] != 0) {
                        //교환건체크
                        $mail_info['sendType'] = 'E';
                        $mail_info['processIds'] = implode(',',$sucess_od_ix);
                    }

                    if($mail_info['sendType'] == 'E') {
                        //교환발송건
                        sendMessageByStep('admin_ms_email_exchange_send_sucess', $mail_info);
                    }else {
                        //첫주문발송건
                        sendMessageByStep('admin_ms_email_good_send_sucess', $mail_info);
                    }
                }
            }
        }catch(Exception $e){
            $error['data'] = json_encode($orders);
            $error['msg'] = json_encode((array)$e);

            $this->error($error);
        }
    }


    /**
     * desc: IN / OUT 폴더처리 분기
     * params: 주문 데이터
     **/
    public function execute($param = '') {
        if(!empty($param)) {
            $this->param = $param;
        }

        if(!empty($this->type)) {
            $this->writeXML();
        }else {
            $this->type = 'R';
            $this->readXML();
        }

    }
}

/**
 * desc: XML child의 값에 CDATA 값처리 가능하게 하는 클래스 (SimpleXMLElement 상속)
 */
class SimpleXMLExtended extends SimpleXMLElement
{
    public function addCData($cdata_text)
    {
        $node = dom_import_simplexml($this);
        $no   = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }

    public function addChildWithCDATA($name, $value = NULL)
    {
        $new_child = $this->addChild($name);
        if ($new_child !== NULL) {
            $node = dom_import_simplexml($new_child);
            $no   = $node->ownerDocument;
            $node->appendChild($no->createCDATASection($value));
        }
        return $new_child;
    }
}