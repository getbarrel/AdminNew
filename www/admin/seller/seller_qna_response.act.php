<?
include("../class/layout.class");

$db = new Database;

if($act == "insert"){

	$sql = "insert into common_support_response(type,templet_name,templet_text,view_order,disp,regdate) values('".$type."','".$templet_name."','".$templet_text."','".$view_order."','".$disp."',NOW())";
	$db->sequences = "BBS_RESPONSE_TEMPLET_SEQ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 템플릿 정보가 정상적으로 등록 되었습니다.');parent.document.location.reload();</script>");
}



if($act == "update"){
	$sql = "update common_support_response set
				type = '".$type."',
				templet_name='".$templet_name."',
				templet_text='".$templet_text."',
				view_order='".$view_order."',
				disp='".$disp."',
				editdate = NOW()
				where csr_ix = '".$csr_ix."' ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 템플릿 정보가 정상적으로 수정 되었습니다.');parent.document.location.href='./seller_qna_response.php';</script>");
}


if($act == "delete"){
	$sql = "delete from common_support_response where csr_ix = '".$csr_ix."' ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 템플릿 정보가 정상적으로 삭제 되었습니다.');parent.document.location.href='./seller_qna_response.php';</script>");
}

?>