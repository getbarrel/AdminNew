<?
include("../../class/database.class");



$db = new MySQL;

if ($act == "insert")
{

	if($etc == ""){
		$language_name = $language_name;
	}else{
		$language_name = $etc;
	}
	$sql = "insert into global_language 
				(language_ix,mall_ix, language_name,language_code,currency_unit_front,currency_unit_back,disp,regdate) 
				values
				('','$language_name','$mall_ix','$language_code','$currency_unit_front','$currency_unit_back','$disp',NOW())";

	// 오라클일때 사용
	$db->sequences = "SHOP_BANKINFO_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('언어가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.href='language.php';</script>");
}


if ($act == "update"){
		
	$sql = "update global_language set							
				mall_ix='$mall_ix',
				language_name='$language_name',
				language_code='$language_code',
				currency_unit_front='$currency_unit_front',currency_unit_back='$currency_unit_back',disp='$disp' 
				where language_ix='$language_ix' ";

	
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('언어가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'language.php';</script>");
}

if ($act == "delete"){
	
	$sql = "delete from global_language where language_ix='$language_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('언어가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.href='language.php';</script>");
}

?>
