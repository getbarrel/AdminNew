<?

include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
print_r($_POST);
exit;
if($mode == "insert"){
	$db = new Database;
	if($plevel1 == "" && $page_depth == "0"){
		$plevel1 = "0";
	}

	if($plevel2 == "" && $page_depth == "1"){
		$plevel2 = "0";
	}

	if($plevel3 == "" && $page_depth == "2"){
		$plevel3 = "0";
	}
	$page_cid = $plevel1.$plevel2.$plevel3;
	$db->query("insert into shop_manual (m_ix,page_cid,page_depth,page_name,shot_text,page_text,plevel1,plevel2,plevel3,regdate) values ('','$page_cid','$depth','$page_name','$shot_text','$page_text','$plevel1','$plevel2','$plevel3',NOW())");

	echo "<script>alert('메뉴얼 등록이 완료 되었습니다');document.location.href='manual_category.php'</script>";
}

if($mode == "update"){
	$db = new Database;
	
}
?>