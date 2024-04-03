<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-12-16
 * Time: 오전 10:43
 */
include $_SERVER["DOCUMENT_ROOT"]."/class/layout.class";

$db = new database;

//검증 후 사용 (2019-12-16 일 기준 검증 되지 않음 JK)

/**
 * 상품 옵션별 재고 및 판매진행재고 정보에 따른 상품 재고 판매진행 재고 업데이트 일괄 처리 기능 건
 */

//재고 조정 처리
$sql = "select id from shop_product where state = '1' and disp = '1' ";
$db->query($sql);
$products = $db->fetchall();

if(is_array($products)){
    foreach($products as $key=>$val){
        $sql = "SELECT o.pid, sum(option_stock) as option_stock 
								FROM ".TBL_SHOP_PRODUCT_OPTIONS." o , ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od
								WHERE od.pid='".$val['id']."' and o.opn_ix = od.opn_ix
								and option_kind in ('a','b','x','x2','s2') and od.option_soldout != '1' group by o.pid ";
        $db->query($sql);
        if($db->total) {
            $db->fetch();
            $goods_stock = $db->dt[option_stock];
        }else{
            //세트 상품일때 품목별 재고 상품 업데이트 방법 처리
            $sql = "select sum(stock) as option_stock 
                                    from inventory_product_stockinfo where gid in (
                                    SELECT od.option_gid 
                                    FROM " . TBL_SHOP_PRODUCT_OPTIONS . " o , " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od
                                    WHERE od.pid='" . $val['id'] . "' and o.opn_ix = od.opn_ix
                                    and option_soldout != '1'
                                    and option_kind in ('c')
                                    group by od.option_gid
								)
								";
            $db->query($sql);
            $db->fetch();
            $goods_stock = $db->dt[option_stock];
        }

        $db->query("update ".TBL_SHOP_PRODUCT." set stock = '".$goods_stock."'  where id ='".$val['id']."'");
        $db->query("update shop_product_global set stock = '".$goods_stock."'  where id ='".$val['id']."'");
    }
}

//판매진행 재고 조정 처리
$sql = "select option_gid from shop_product_options_detail where pid in (select id from shop_product where state = '1' and disp = '1' ) group by option_gid ";
$db->query($sql);
$options = $db->fetchall();
if(is_array($options)){
    foreach($options as $key=>$val){
        sellingCntUpdate($val['option_gid']);
    }
}