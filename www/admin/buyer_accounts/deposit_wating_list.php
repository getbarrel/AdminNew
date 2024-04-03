<?
$state = "1";	//입금대기
$info_type = "deposit_wating_list";
include("../buyer_accounts/deposit_list.php");

// 신규 마일리지 시스템으로 적용하기 위해 새로운 include  처리

//$history_type : 처리상태 (1:입금대기 2:입금취소 3:입금완료 4:사용완료 5:출금요청 6:출금취소 7:출금확정)
//
//$history_type = "1"; //입금대기
//$info_type = "deposit_wating_list";
//include("../buyer_accounts/deposit_list_new.php");

?>
