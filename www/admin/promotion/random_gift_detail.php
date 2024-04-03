<?php
include $_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class";


$sql = "select * from shop_gift_random_certificate where gc_ix = '".$gc_ix."'";
$db->query($sql);
$gifts = $db->fetch();


$sql = "select count(*) cnt ,gift_type,gift_value from shop_gift_random_certificate_detail where gc_ix = '".$gc_ix."' 
group by gift_type, gift_value order by gift_type desc";
$db->query($sql);
$gift_detail = $db->fetchall();
$couponInfo = "";
if(is_array($gift_detail)){
    foreach($gift_detail as $key=>$val){
        if($val['gift_type'] == 'C'){
            $sql = "select publish_name from shop_cupon_publish where publish_ix = '".$val['gift_value']."' ";
            $db->query($sql);
            $db->fetch();
            $publish_name = $db->dt['publish_name'];
            $couponInfo .= "쿠폰 : ".$publish_name.", 발행 수량 : ".number_format($val['cnt']) ."건 <br/>";
        } else if($val['gift_type'] == 'M'){
            $couponInfo .= "적립금 : ".$val['gift_value']." P, 발행 수량 : ".number_format($val['cnt']) ."건 <br/>";
        }
    }
}else{
    $couponInfo .= "발행 정보가 없습니다.";
}
//

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
    <TR>
        <td align=center colspan=2 valign=top>
            <table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
                <tr >
                    <td align='left' colspan=2> ".GetTitleNavigation("쿠폰 상세", "쿠폰 관리 > 쿠폰 상세", false)."</td>
                </tr>
                <tr>
                    <td align=center> <!-- style='padding: 0 10px 0 10px;height:569px;vertical-align:top' -->
                        <table border='0' cellspacing='1' cellpadding='5' width='100%'>
                            <tr>
                                <td bgcolor='#F8F9FA'>
                                    <table border='0' width='100%' cellspacing='1' cellpadding='0'>
                                        <tr>
                                            <td >
                                                <table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
                                                <col width=15%>
                                                <col width=35%>
                                                <col width=15%>
                                                <col width=35%>
                                                    <tr>
                                                        <td class='input_box_title' nowrap> 이름 </td>
                                                        <td class='input_box_item' colspan='3'>".$gifts[gift_certificate_name]."</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='input_box_title' nowrap> 설명 </td>
                                                        <td class='input_box_item' colspan='3'>".$gifts[memo]."</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='input_box_title' nowrap> 사용여부 </td>
                                                        <td class='input_box_item' colspan='3'>".($gifts[is_use] == "1" ? "사용":"미사용")."</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='input_box_title' nowrap> 발행 정보 </td>
                                                        <td class='input_box_item' colspan='3'>".$couponInfo."</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='input_box_title' nowrap> 사용 기간 </td>
                                                        <td class='input_box_item' colspan='3'>".$gifts['gift_start_date']." ~ ".$gifts['gift_end_date']."</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</TABLE>";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션(마케팅)/전시 > 쿠폰관리 > 랜덤 쿠폰 상세";
$P->NaviTitle = "랜덤 쿠폰 상세";
$P->title = "랜덤 쿠폰 상세";
$P->strContents = $Contents;
echo $P->PrintLayOut();