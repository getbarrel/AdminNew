<?
include("../../class/database.class");
include($_SERVER["DOCUMENT_ROOT"] . "/class/sms.class");

$db = new Database;

if ($act == "update") {

    foreach ($_POST as $key => $val) {
        if ($key != "act" && $key != "mall_ix" && $key != "x" && $key != "y") {
            $sql = "REPLACE INTO shop_payment_config set 
						mall_ix = '" . $_POST[mall_ix] . "',
						pg_code='kcp' , 
						config_name ='" . $key . "',
						config_value ='" . $val . "'  ";
            $db->query($sql);
        }
    }
//
//    // 위 프로세스 적용후 삭제
//    $sql = "update " . TBL_SHOP_SHOPINFO . " set
//			kcp_id='" . trim($kcp_id) . "',
//			kcp_nointerest_price='$kcp_nointerest_price',
//			kcp_nointerest_str='$kcp_nointerest_str',
//			kcp_interest_str='$kcp_interest_str',
//			escrow_use='$escrow_use',
//			escrow_apply='$escrow_apply',
//			escrow_method_bank='$escrow_method_bank',
//			escrow_method_vbank='$escrow_method_vbank',
//			escrow_method_card='$escrow_method_card',
//			kcp_key = '" . trim($kcp_key) . "',kcp_type = '$kcp_type'
//			where mall_ix = '$mall_ix'";

//    $db->query($sql);

//    if ($kcp_type_befor == 'test' && $kcp_type == 'service') {
//
//        $recipient = "강태웅<ktw9@forbiz.co.kr>";
//        $from_name = '몰스토리';
//        $from = 'hong861114@forbiz.co.kr';
//        $subject = '결제모듈 변경 관련 이메일';
//
//        $recipient = iconv('UTF-8', 'EUC-KR', $recipient);
//        $from_name = iconv('UTF-8', 'EUC-KR', $from_name);
//        $subject = iconv('UTF-8', 'EUC-KR', $subject);
//
//        $comment = '상점이름 :' . $_SESSION[shopcfg][shop_name] . ', mall_name :' . $_SESSION[admininfo][mall_ename] . ' 에서 KCP test->service로 변경';
//
//        $headers = "From: $from_name <$from>\n";
//        $headers .= "X-Sender: <$from>\n";
//        $headers .= "X-Mailer: PHP " . phpversion() . "\n";
//        $headers .= "X-Priority: 1\n";
//        $headers .= "Return-Path: <$from>\n";
//        $headers .= "Content-Type: text/plain; ";
//        $headers .= "charset=utf-8\n";
//
//        $comment = stripslashes($comment);
//        $comment = str_replace("\n\r", "\n", $comment);
//
//        mail($recipient, $subject, $comment, $headers);
//
//
//        $s = new SMS();
//
//        $s->send_phone = "0220582214";
//        $s->send_name = "(주)포비즈코리아";
//
//        $s->dest_phone = ("01027435628");
//        $s->dest_name = "강태웅";
//        $s->msg_body = '상점이름 :' . $_SESSION[shopcfg][shop_name] . ', mall_name :' . $_SESSION[admininfo][mall_ename] . ' 에서 KCP test->service로 변경';
//
//        $mall_domain[mall_domain_key] = 'a6a91e49379c3721d274d52b99017fb7';
//
//        $s->sendbyone($mall_domain);
//
//    }

    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
    echo("<script>location.href = 'settlement_config.php';</script>");
}

?>
