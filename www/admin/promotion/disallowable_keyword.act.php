<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert"){

	$sql = "insert into shop_disallowable_config
				(dc_ix,disallowable_search_keyword,disallowable_search_keyword_use,disallowable_pname_keyword,disallowable_pname_keyword_use,regdate) 
				values
				('$dc_ix','$disallowable_search_keyword','$disallowable_search_keyword_use','$disallowable_pname_keyword','$disallowable_pname_keyword_use',NOW())";
	$db->sequences = "DISALLOWABLE_CONFIG_SEQ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색창 키워드 관리  정보가  정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	$sql = "update shop_disallowable_config set
				disallowable_search_keyword='".$disallowable_search_keyword."',
				disallowable_search_keyword_use='".$disallowable_search_keyword_use."',
				disallowable_pname_keyword='".$disallowable_pname_keyword."',
				disallowable_pname_keyword_use='".$disallowable_pname_keyword_use."'
				where dc_ix='".$dc_ix."' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색창 키워드 관리 정보가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

?>
