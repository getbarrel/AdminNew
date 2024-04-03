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
	$sql = "insert into shop_product_style 
				(style_ix,style_name,style_code,disp,regdate) 
				values
				('','$style_name','$style_code','$disp',NOW())";

	// 오라클일때 사용
	$db->sequences = "SHOP_BANKINFO_SEQ";
	$db->query($sql);


	echo("<script style='javascript' src='../js/message.js.php'></script><script>show_alert('스타일가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.href='style.php';</script>");
}


if ($act == "update"){
		
	$sql = "update shop_product_style set							
				style_name='$style_name',style_code='$style_code',disp='$disp' 
				where style_ix='$style_ix' ";

	
	$db->query($sql);

	

	echo("<script style='javascript' src='../js/message.js.php'></script><script>show_alert('스타일가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'style.php';</script>");
}

if ($act == "delete"){
	
	$sql = "delete from shop_product_style where style_ix='$style_ix'";
	$db->query($sql);


	echo("<script style='javascript' src='../js/message.js.php'></script><script>show_alert('스타일가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.href='style.php';</script>");
}

?>
