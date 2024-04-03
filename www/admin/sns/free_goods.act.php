<?
include("../../class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');



$db = new Database;

$querys[] = 'div_ix = "'.$div_ix.'"';			// 무료쿠폰 분류
$querys[] = 'fp_title = "'.$fp_title.'"';			// 무료쿠폰 제목
$querys[] = 'fp_info = "'.$fp_info.'"';			// 무료쿠폰 간단설명
$querys[] = 'fp_sdate = "'.mktime(0, 0, 0, substr($fp_sdate, 4, 2), substr($fp_sdate, 6, 2), substr($fp_sdate, 0, 4)).'"';			// 유효기간 시작일
$querys[] = 'fp_edate = "'.mktime(23, 59, 0, substr($fp_edate, 4, 2), substr($fp_edate, 6, 2), substr($fp_edate, 0, 4)).'"';			// 유효기간 종료일
$querys[] = 'fp_usesdate = "'.mktime(0, 0, 0, substr($fp_usesdate, 4, 2), substr($fp_usesdate, 6, 2), substr($fp_usesdate, 0, 4)).'"';	// 진행시작일
$querys[] = 'fp_useedate = "'.mktime(23, 59, 0, substr($fp_useedate, 4, 2), substr($fp_useedate, 6, 2), substr($fp_useedate, 0, 4)).'"';	// 진행종료일
$querys[] = 'disp = "'.$disp.'"';			// 무료쿠폰 노출여부
$querys[] = 'fp_zone = "'.$fp_zone.'"';			// 무료쿠폰 지역
$querys[] = 'fp_zipcode = "'.$zipcode1."-".$zipcode2.'"';			// 무료쿠폰 우편번호
$querys[] = 'fp_addr1 = "'.$addr.'"';			// 무료쿠폰 상세주소
$querys[] = 'fp_addr2 = "'.$addr2.'"';			// 무료쿠폰 상세주소2
$querys[] = 'fp_url = "'.$fp_url.'"';			// 무료쿠폰 URL
$querys[] = 'fp_count = "'.$fp_count.'"';			// 무료쿠폰 판매개수
$querys[] = 'fp_contents = "'.$fp_contents.'"';			// 무료쿠폰 발송메일 내용

if ($fp_file_size > 0){
	if($fp_ix) {
		$db->query("SELECT fp_file FROM ".TBL_SNS_FREEPRODUCT." WHERE fp_ix = '".$fp_ix."'");
		$db->fetch();
		$fp_file_tmp = $db->dt[0];
		if($fp_file_tmp) unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config["mall_data_root"]."/images/cupon/".$fp_file_tmp);
	}
	$fileTmp = "free_coupon_".date("ymdhis");
	$exp = end(explode('.', $_FILES['fp_file']['name']));
	copy($fp_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config["mall_data_root"]."/images/cupon/".$fileTmp.".".$exp);
	$querys[] = 'fp_file = "'.$fileTmp.".".$exp.'"';			// 무료쿠폰 발송메일 내용
}

if ($act == "insert")
{
	$querys[] = 'fp_stok = "'.$fp_count.'"';			// 무료쿠폰 남은갯수
	$querys[] = 'regdate = now()';			// 쿠폰 관리번호
	
	$db->query('INSERT INTO '.TBL_SNS_FREEPRODUCT.' SET '.implode(',',$querys));

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('무료쿠폰이 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='free_goods_list.php';</script>");
}


if ($act == "update"){
		
	$db->query('UPDATE '.TBL_SNS_FREEPRODUCT.' SET '.implode(',',$querys).' WHERE fp_ix = "'.$fp_ix.'"');
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('무료쿠폰이 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'free_goods_list.php';</script>");
}

if ($act == "delete"){
	
	$sql = "delete from ".TBL_SNS_FREEPRODUCT." where fp_ix='$fp_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('무료쿠폰이 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='free_goods_list.php';</script>");
}

?>
