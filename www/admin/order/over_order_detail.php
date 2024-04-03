<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2020-04-16
 * Time: 오후 6:04
 */
include("../class/layout.class");

$gid = $_GET['gid'];

$sql = "select 
          oid,pid,regdate,status,pcnt
        from 
          shop_order_detail 
        where 
          gid = '".$gid."' 
        and gid != '' 
        and status IN ('IC', 'DR') 
        AND erp_link_date IS NULL 
        order by regdate asc
        ";
$db->query($sql);
$order_data = $db->fetchall();

$list_item = "";
$mmode = "personalization";
if(!empty($order_data)){
    foreach($order_data as $key=>$val){
        $list_item .= "
        <tr height=35 bgcolor=#ffffff align=center>
            <td class='list_box_td '>".$val['oid']."</td>
            <td class='list_box_td '> ".$val['regdate']."</td>
            <td class='list_box_td '> ".getOrderStatus($val['status'])."</td>
            <td class='list_box_td '> ".$val['pcnt']."</td>
            <td class='list_box_td '> 
                <a href=\"javascript:PopSWindow('../order/orders.edit.php?oid=".$val[oid]."&pid=".$val[pid]."&mmode=".$mmode."&mem_ix=".$mem_ix."&soldout_gid=".$gid."',960,800);\" >
                <input type='button' value='상세보기' > 
                </a>
            </td>
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
        <td class='s_td' >주문번호</td>
        <td class='m_td' >주문일자</td>
        <td class='m_td' >주문상태</td>   
        <td class='m_td' >주문수량</td>        
        <td class='m_td' >상세보기</td>
    </tr>        
    ".$list_item."
</table>

";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "과주문 상세내역";
$P->NaviTitle = "과주문 상세내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();