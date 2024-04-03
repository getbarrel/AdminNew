<?php
@set_time_limit(0);
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

$db = new Database();

//$ 환율 가져오기
$sql = "SELECT * FROM global_currency WHERE currency_code = 'USD'";
$db->query($sql);
$usd = $db->fetch();

$rate = $usd['exchange_rate'];

//모든 상품 가져오기
$sql = "SELECT id, listprice, sellprice, coprice FROM shop_product";
$db->query($sql);
$pinfo = $db->fetchall();

foreach($pinfo as $key => $val){
    $id = $val['id'];
    $listprice = $val['listprice'];
    $sellprice = $val['sellprice'];
    $coprice = $val['coprice'];

    $sql = "UPDATE shop_product_global SET listprice = round($listprice/$rate, 2), sellprice = round($sellprice/$rate, 2), coprice = round($coprice/$rate, 2) WHERE id = $id";
    $db->query($sql);

    //상품 옵션조회
    $sql = "SELECT id, option_listprice, option_price, option_coprice FROM shop_product_options_detail WHERE pid = $id";
    $db->query($sql);
    $optionInfo = $db->fetchall();

    if(!empty($optionInfo)){
        foreach($optionInfo as $key2 => $val2) {
            $id = $val2['id'];
            $option_listprice = $val2['option_listprice'];
            $option_price = $val2['option_price'];
            $option_coprice = $val2['option_coprice'];

            $sql = "UPDATE shop_product_options_detail_global SET 
                option_listprice = round($option_listprice/$rate, 2),
                option_price = round($option_price/$rate, 2),
                option_coprice = round($option_coprice/$rate, 2)
                WHERE id = $id";
            $db->query($sql);
        }
    }

}
