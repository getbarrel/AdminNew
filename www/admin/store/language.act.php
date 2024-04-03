<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{

	if($etc == ""){
		$bank_name = $bank_name;
	}else{
		$bank_name = $etc;
	}


	if($db->dbms_type == "oracle"){
		$sql = "select * from admin_language where text_div = '$text_div' and text_korea like '$text_korea' and language_type = '$language_type'";
	}else{
		$sql = "select * from admin_language where text_div = '$text_div' and text_korea = '$text_korea' and language_type = '$language_type'";
	}

	$db->query($sql);
	if(!$db->total){
		$sql = "insert into admin_language(language_ix,text_div,language_type,text_korea,text_trans,disp,regdate) values('','$text_div','$language_type','$text_korea','$text_trans','$disp',NOW())";
		$db->sequences = "ADMIN_LANGUAGE_SEQ";
		$db->query($sql);
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('랭귀지 목록이 정상적으로 등록되었습니다.');</script>");
		echo("<script>document.location.href='language.php?text_div=$text_div&language_type=$language_type';</script>");
	}else{

		echo("<script>alert('이미 등록된 랭귀지 목록입니다.');</script>");
		echo("<script>document.location.href='language.php?text_div=$text_div&language_type=$language_type';</script>");
	}
}


if ($act == "update"){

	$sql = "update admin_language set text_div='$text_div',language_type='$language_type',text_korea='$text_korea',text_trans='$text_trans',disp='$disp' where language_ix='$language_ix'  ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('랭귀지 목록이 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'language.php?text_div=$text_div&language_type=$language_type';</script>");
}

if ($act == "delete"){

	$sql = "delete from admin_language where language_ix='$language_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('랭귀지 목록이 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='language.php?text_div=$text_div&language_type=$language_type';</script>");
}

?>
