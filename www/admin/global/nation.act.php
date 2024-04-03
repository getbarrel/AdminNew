<?
include("../../class/database.class");



$db = new MySQL;

if ($act == "insert")
{

	if($etc == ""){
		$nation_name = $nation_name;
	}else{
		$nation_name = $etc;
	}


	$sql = "insert into global_nation 
				(nation_ix,nation_name,nation_code,language_ix, currency_ix, disp,regdate) 
				values
				('','$nation_name','$nation_code','$language_ix','$currency_ix','$disp',NOW())";

	// 오라클일때 사용
	$db->sequences = "SHOP_BANKINFO_SEQ";
	$db->query($sql);
	//echo nl2br($sql);

	echo("<script nation='javascript' src='../js/message.js.php'></script><script>show_alert('국가가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.href='nation.php';</script>");
}


if ($act == "update"){
		
	$sql = "update global_nation set							
				nation_name='$nation_name',
				nation_code='$nation_code',
				language_ix='$language_ix',
				currency_ix='$currency_ix',
				disp='$disp' 
				where nation_ix='$nation_ix' ";

	
	$db->query($sql);

	echo("<script nation='javascript' src='../js/message.js.php'></script><script>show_alert('국가가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'nation.php';</script>");
}

if ($act == "delete"){
	
	$sql = "delete from global_nation where nation_ix='$nation_ix'";
	$db->query($sql);


	echo("<script nation='javascript' src='../js/message.js.php'></script><script>show_alert('국가가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.href='nation.php';</script>");
}

?>
