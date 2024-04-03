<?
include("../../class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');



if($admininfo[company_id] == ""){
	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

$db = new Database;

if($act == "list_update"){
	$result = "NO";
	$sql = "SELECT COUNT(*) as cnt FROM ".TBL_SNS_COUPON_INFO." WHERE ci_ix = '".$ci_ix."' and coupon_no LIKE '%".$coupon_text."' ";
	$db->query($sql);
	$db->fetch();
	if($db->dt['cnt'] > 0)	{
		$sql = "UPDATE ".TBL_SNS_COUPON_INFO." SET status = '".SNS_COUPON_STATUS_COMPLETE."' WHERE ci_ix = '".$ci_ix."' ";
		$db->query($sql);
		$result = "OK";
	} else {
		$result = "NO";
	}
	echo $result;
} else if($act == "pop_update"){
	$sql = "SELECT COUNT(*) as cnt FROM ".TBL_SNS_COUPON_INFO." WHERE ci_ix = '".$ci_ix."' and coupon_no = '".$coupon_print.$lastcoupon."' ";
	$db->query($sql);
	$db->fetch();
	if($db->dt['cnt'] > 0)	{
		$sql = "UPDATE ".TBL_SNS_COUPON_INFO." SET status = '".$status."' WHERE ci_ix = '".$ci_ix."' ";
		$db->query($sql);
	} else {
		echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['coupon_list.act.php']['A'][language]);history.back();</script>";
		//'일치하는 쿠폰번호가 없습니다.'
		exit;
	}
		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('정상적으로 수정되었습니다.');opener.location.reload();window.close();</script>";
		exit;
}

?>