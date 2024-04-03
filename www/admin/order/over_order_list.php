<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2020-04-16
 * Time: 오후 4:56
 */
include_once("../class/layout.class");

$sql = "select gid,sum(pcnt) as sell_ing_cnt from shop_order_detail where gid != '' and status IN ('IC', 'DR') AND erp_link_date IS NULL group by gid ";
$db->query($sql);
$gid_datas = $db->fetchall();
$over_order_data = array();
$i = 0;
if(is_array($gid_datas)){
    foreach($gid_datas as $key=>$val){
        $sql = "select ifnull(sum(stock),0) as stock from inventory_product_stockinfo where gid = '" . $val['gid'] . "'";
        $db->query($sql);
        $db->fetch();
        $stock = $db->dt['stock'];

        $sql = "select sum(pcnt) as sell_ing_cnt from shop_order_detail  where gid = '" . $val['gid'] . "' and status in ('IR','IC','DR','DD')";
        $db->query($sql);
        $db->fetch();
        $sell_ing_cnt = $db->dt['sell_ing_cnt'];

        if(($stock - $sell_ing_cnt) < 0){
            $over_order_data[$i]['gid'] = $val['gid'];
            $over_order_data[$i]['stock'] = $stock;
            $over_order_data[$i]['sell_ing_cnt'] = $sell_ing_cnt;
            $over_order_data[$i]['over_stock'] = $stock - $sell_ing_cnt;

            $i++;
        }
    }
}
$list_item = "";
if(!empty($over_order_data)){
    foreach($over_order_data as $key=>$val){
        $list_item .= "
        <tr height=35 bgcolor=#ffffff align=center>
            <td class='list_box_td '>".$val['gid']."</td>
            <td class='list_box_td '> ".$val['stock']."</td>
            <td class='list_box_td '> ".$val['sell_ing_cnt']."</td>
            <td class='list_box_td '> ".$val['over_stock']."</td>
            <td class='list_box_td '> <input type='button' value='상세보기' onclick=\"overOrderPop('".$val['gid']."')\" > </td>
        </tr>
        ";
    }
}else{
    $list_item .= "
    <tr height=35 bgcolor=#ffffff align=center>
        <td class='list_box_td ' colspan='5'> 과주문 내역이 없습니다. </td>
    </tr>
    ";
}

$Contents = "
<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>
    <col width = '20%' />
    <col width = '20%' />
    <col width = '20%' />
    <col width = '20%' />
    <col width = '20%' />
    <tr align=center bgcolor=#efefef height=25>
        <td class='s_td' >품목코드</td>
        <td class='m_td' >현재재고</td>
        <td class='m_td' >판매진행재고</td>
        <td class='m_td' >과주문재고수량</td>
        <td class='m_td' >상세보기</td>
    </tr>        
    ".$list_item."
</table>

";

$Script = "
<script>
    function overOrderPop(gid){
        PoPWindow('../order/over_order_detail.php?gid='+gid,800,600,'over_order_detail');
    }
</script>
";


$P = new LayOut();

$P->strLeftMenu = order_menu();
$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
$P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n".$Script;
$P->Navigation = "주문관리 > 과주문 리스트";
$P->title = "과주문 리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();