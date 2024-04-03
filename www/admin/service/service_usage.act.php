<?
include("../class/layout.class");
include("$DOCUMENT_ROOT/include/email.send.php");

$db = new MySQL;
$db2 = new MySQL;
if ($act == "serviceinfo_update"){
	$vFromYY = trim($vFromYY);
	$vFromMM = trim($vFromMM);
	$vFromDD  = trim($vFromDD);
	$s_hour = trim($s_hour);

	$start_date=$vFromYY."-".$vFromMM."-".$vFromDD." ".$s_hour;

	$vToYY = trim($vToYY);
	$vToMM = trim($vToMM);
	$vToDD  = trim($vToDD);
	$e_hour = trim($e_hour);

	$end_date=$vToYY."-".$vToMM."-".$vToDD." ".$e_hour;

	if($admininfo[admin_level] == 9){
		$sql = "UPDATE service_info SET start_date='".$start_date."', end_date='".$end_date."' WHERE si_ix='".$_POST["si_ix"]."' ";
		$db->query($sql);
	}else{
		$sql = "UPDATE service_info SET start_date='".$start_date."', end_date='".$end_date."' WHERE si_ix='".$_POST["si_ix"]."' ";
		$db->query($sql);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('서비스이용정보가 정상적으로 수정되었습니다.');parent.location.reload();location.href='about:blank';</script>");
} else if($act=="select_status_update") {
	if($admininfo[admin_level] == 9 && $status!=""){
		for($j=0;$j < count($si_ix);$j++){
			$sql="UPDATE service_info SET si_status='".$status."' WHERE si_ix='".$si_ix[$j]."' ";
			$db->query($sql);
		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('서비스이용정보가 정상적으로 일괄 수정되었습니다.');parent.location.reload();location.href='about:blank';</script>");
	}
} else if($act=="delete") {
	$sql="DELETE FROM service_info WHERE si_ix='".$si_ix."' ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('서비스이용정보가 정상적으로 삭제되었습니다.');parent.location.reload();location.href='about:blank';</script>");
}

?>