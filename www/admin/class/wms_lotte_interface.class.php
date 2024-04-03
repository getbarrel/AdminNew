<?php
/**
 * Created by PhpStorm.
 * User: 이석현
 * Date: 2019-02-18
 * Time: 오후 5:24
 */
include_once($_SERVER["DOCUMENT_ROOT"] . "/class/database.class");
include_once($_SERVER["DOCUMENT_ROOT"] . "/class/mysql_wms_lotte.class.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/admin/inventory/inventory.lib.php");

class WmsLotteInterface
{
    protected $db;
    protected $wmsDB;

    public function __construct()
    {
        $this->db = new Database;
        $this->wmsDB = new WmsMySQL;
    }

    //솔루션 주문상태 배송중비중 -> WMS 출고 예정 데이터 입력
    public function deliveryReady($od_ix)
    {
        if ($od_ix) {
            $sql = "SELECT od.oid, od.od_ix as od_od_ix, rname, rtel, rmobile, zip, addr1, addr2, gid, pcnt, msg
                    FROM shop_order o inner join shop_order_detail as od on o.oid = od.oid inner join shop_order_detail_deliveryinfo odd on odd.odd_ix=od.odd_ix
                    WHERE od.od_ix='$od_ix'";
            $this->db->query($sql);
            $this->db->fetch();

            //$this->db->total
            $v = $this->db->dt;

            $check_sql = "SELECT COUNT(*) AS CNT FROM VGET_OUTBOUND_DWT WHERE ORDER_NO_CUST='".$v['oid']."' AND LINE_NO_CUST='".$v['od_od_ix']."'";
            $this->wmsDB->query($check_sql);
            $this->wmsDB->fetch();
            $check_reulst = $this->wmsDB->dt;

            if($check_reulst['CNT'] == 0) {
                $insert_sql = "INSERT INTO VGET_OUTBOUND_DWT(
                    ORDER_NO_CUST
                    ,LINE_NO_CUST
                    ,ACPER_CD
                    ,ACPER_NM
                    ,ACPER_TEL
                    ,ACPER_HTEL
                    ,ACPER_ZIP_NO
                    ,ACPER_ADDRESS_M
                    ,ACPER_ADDRESS_D
                    ,ORDER_DATE
                    ,ITEM_CD_CUST
                    ,ORDER_QTY
                    ,DLV_GRP
                    ,PAY_CON_CD
                    ,STATUS
                    ,CREATE_DATE
                    ,CREATE_TIME
                    ,CREATE_ON
                    ,REMARK
                    )
                    VALUES (
                    '" . $v['oid'] . "'
                    ,'" . $v['od_od_ix'] . "'
                    ,'W29382'
                    ,'" . $v['rname'] . "'
                    ,'" . $v['rtel'] . "'
                    ,'" . $v['rmobile'] . "'
                    ,'" . $v['zip'] . "'
                    ,'" . $v['addr1'] . "'
                    ,'" . $v['addr2'] . "'
                    ,REPLACE(DATE(NOW()),'-','')
                    ,'" . $v['gid'] . "'
                    ,'" . $v['pcnt'] . "'
                    ,'20'
                    ,'03'
                    ,'1'
                    ,REPLACE(DATE(NOW()),'-','')
                    ,REPLACE(TIME(NOW()),':','')
                    ,'admin'
                    ,'" . $v['msg'] . "'
                    )";
                $this->wmsDB->query($insert_sql);
                $this->wmsDB->fetch();
            }
        }
    }

    //크론 - WMS 출고 실적 조회 -> 솔루션 주문상태 배송중 -> WMS 출고 실적 업데이트 (재조회 안되도록)
    public function cronDeliverying()
    {
        $sql = "SELECT MGR_NO, ORDER_NO_CUST, LINE_NO_CUST, INV_NO FROM VPUT_OUTBOUND_DWT WHERE STATUS='1'";
        $this->wmsDB->query($sql);
        $data = $this->wmsDB->fetchall("object");

        if (count($data) > 0) {
            foreach ($data as $v) {
                $update_str = " update_date = NOW() ";
                $update_str .= " , quick= '12' ";
                $update_str .= " , invoice_no= '$v[INV_NO]' ";
                $update_str .= " , status= 'DI' ";
                $update_str .= " , di_date = NOW() ";

                //주문상태 배송중 처리
                $update_sql = "UPDATE shop_order_detail SET $update_str WHERE od_ix='" . $v['LINE_NO_CUST'] . "'";
                $this->db->query($update_sql);

                //log 저장
                set_order_status($v['ORDER_NO_CUST'], 'DI', 'WMS 롯데택배 배송중 처리 크론 업데이트', '시스템', $_SESSION["admininfo"]["company_id"], $v['LINE_NO_CUST'], "", "", '12', $v['INV_NO']);

                //재조회 안되도록 처리
                $update_wms = "UPDATE VPUT_OUTBOUND_DWT SET STATUS='3' WHERE MGR_NO='" . $v['MGR_NO'] . "'";
                $this->wmsDB->query($update_wms);
                $this->wmsDB->fetch();

                //재고 업데이트
                $sql = "select * from " . TBL_SHOP_ORDER_DETAIL . " where od_ix in (" . $v['LINE_NO_CUST'] . ") ";
                $this->db->query($sql);
                $this->db->fetch();
                $order_detail = $this->db->dt;

                if ($order_detail['stock_use_yn'] == "Y") {
                    $this->UpdateGoodsItemStockInfo(2, $order_detail, '상품판매 - 출고 (연동)');
                }

                UpdateProductCnt_complete($order_detail);
            }
        }
    }

    //솔루션 주문상태 반품&교환승인 -> WMS 반품예정 데이터 입력
    public function deliveryReturn($od_ix)
    {
        if ($od_ix) {
            $sql = "SELECT od.oid, od.od_ix as od_od_ix, rname, rtel, rmobile, zip, addr1, addr2, gid, pcnt, order_type, msg
                    FROM shop_order o inner join shop_order_detail as od on o.oid = od.oid inner join shop_order_detail_deliveryinfo odd on odd.odd_ix=od.odd_ix
                    WHERE od.od_ix='$od_ix'";
            $this->db->query($sql);
            $this->db->fetch();

            $v = $this->db->dt;

            //고객이 지정택배요청시 수거상태로 db에 저장되고 wms에 회수지시로 넣어줘야함.
            if ($v['order_type'] == '4') {
                $ORDER_TYPE_RET_VALUE = 'Y';
            } else {
                $ORDER_TYPE_RET_VALUE = 'N';
            }

            $check_sql = "SELECT COUNT(*) AS CNT FROM VGET_RETURN_DWT WHERE ORDER_NO_CUST='".$v['oid']."' AND LINE_NO_CUST='".$v['od_od_ix']."'";
            $this->wmsDB->query($check_sql);
            $this->wmsDB->fetch();
            $check_reulst = $this->wmsDB->dt;

            if($check_reulst['CNT'] == 0) {
                $insert_sql = "INSERT INTO VGET_RETURN_DWT(
                                ORDER_NO_CUST
                                ,LINE_NO_CUST
                                ,SNPER_CD
                                ,SNPER_NM
                                ,SNPER_TEL
                                ,SNPER_HTEL
                                ,SNPER_ZIP_NO
                                ,SNPER_ADDRESS_M
                                ,SNPER_ADDRESS_D
                                ,ORDER_TYPE_RET
                                ,ORDER_DATE
                                ,ITEM_CD_CUST
                                ,ORDER_QTY
                                ,DLV_GRP
                                ,PAY_CON_CD
                                ,STATUS
                                ,CREATE_DATE
                                ,CREATE_TIME
                                ,CREATE_ON
                                ,REMARK
                                )
                                VALUES (
                                '" . $v['oid'] . "'
                                ,'" . $v['od_od_ix'] . "'
                                ,'A93893'
                                ,'" . $v['rname'] . "'
                                ,'" . $v['rtel'] . "'
                                ,'" . $v['rmobile'] . "'
                                ,'" . $v['zip'] . "'
                                ,'" . $v['addr1'] . "'
                                ,'" . $v['addr2'] . "'
                                ,'" . $ORDER_TYPE_RET_VALUE . "'
                                ,REPLACE(DATE(NOW()),'-','')
                                ,'" . $v['gid'] . "'
                                ,'" . $v['pcnt'] . "'                                
                                ,'20'
                                ,'03'
                                ,'1'
                                ,REPLACE(DATE(NOW()),'-','')
                                ,REPLACE(TIME(NOW()),':','')
                                ,'admin'
                                ,'" . $v['msg'] . "'
                                )";
                $this->wmsDB->query($insert_sql);
                $this->wmsDB->fetch();
            }
        }
    }

    //크론 - WMS 현 재고 조회 -> 솔루션 재고 업데이트 처리
    public function cronNowStock()
    {
        $wms_sql = "SELECT ITEM_CD_CUST, STOCK_QTY FROM VPRODUCT_CNT";
        $this->wmsDB->query($wms_sql);
        $data = $this->wmsDB->fetchall("object");

        if (count($data) > 0) {
            foreach ($data as $v) {
                $order_detail['gid'] = $v['ITEM_CD_CUST'];

                $sql = "SELECT count(*) AS cnt FROM inventory_goods_unit where gid='" . $order_detail['gid'] . "'";
                $this->db->query($sql);
                $this->db->fetch();
                if ($this->db->dt['cnt'] > 0) {
                    $sql = "SELECT stock FROM inventory_product_stockinfo where gid='" . $order_detail['gid'] . "' and ps_ix=1";
                    $this->db->query($sql);
                    $this->db->fetch();
                    $stock = $this->db->dt['stock'];
                    if(empty($stock)){
                        $stock = 0;
                    }

                    if ($v['STOCK_QTY'] > $stock) {
                        $h_div = 1; // 1:입고, 2: 출고
                        $order_detail['pcnt'] = $v['STOCK_QTY'] - $stock;
                    } else if ($v['STOCK_QTY'] < $stock) {
                        $h_div = 2; // 1:입고, 2: 출고
                        $order_detail['pcnt'] = $stock - $v['STOCK_QTY'];
                    } else {
                        continue;
                    }

                    $this->UpdateGoodsItemStockInfo($h_div, $order_detail, '실재고 반영');
                }
            }
        }
    }


    protected function UpdateGoodsItemStockInfo($h_div, $order_detail, $msg)
    {
        //입고 보관장소
        $sql = "select pi.pi_ix, pi.company_id, pi.place_name, ps.ps_ix, ps.section_name
                                          from	inventory_place_section ps
                                          left join inventory_place_info pi on pi.pi_ix = ps.pi_ix
                                          where ps.ps_ix = '1'";
        $this->db->query($sql);
        $this->db->fetch();
        $order_item_info = $this->db->dt;

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
        $this->db->query($sql);
        $delivery_iteminfo = $this->db->fetchall('object');

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
    }
}