<?
include("../../class/database.class");



$db = new MySQL;

if ($act == "insert")
{

	if($etc == ""){
		$style_name = $style_name;
	}else{
		$style_name = $etc;
	}
	$sql = "insert into shop_design_gnb 
				(gnb_ix,gnb_name, gnb_link, gnb_code,disp,regdate) 
				values
				('','$gnb_name', '$gnb_link', '$gnb_code', '$disp',NOW())";

	// 오라클일때 사용
	$db->sequences = "SHOP_DESIGN_GNB_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상단 메뉴(GNB)가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.href='gnb.php';</script>");
}


if ($act == "update"){
		
	$sql = "update shop_design_gnb set							
				gnb_name='$gnb_name',
				gnb_link='$gnb_link',
				gnb_code='$gnb_code',
				disp='$disp' 
				where gnb_ix='$gnb_ix' ";

	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상단 메뉴(GNB)가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'gnb.php';</script>");
}

if ($act == "delete"){
	
	$sql = "delete from shop_design_gnb where gnb_ix='$gnb_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상단 메뉴(GNB)가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.href='gnb.php';</script>");
}

?>
