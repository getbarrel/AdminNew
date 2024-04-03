<?
include("../class/layout.class");

$db = new Database;

if($act == "insert"){

	/*
	$sql = "insert into bbs_response_templet set
				rt_ix='',
				templet_name='".$templet_name."',
				templet_text='".$templet_text."',
				view_order='".$view_order."',
				disp='".$disp."',
				regdate=NOW() ";
	*/
	$sql = "insert into bbs_response_templet(rt_ix,templet_div,templet_name,templet_text,view_order,disp,regdate) values('','".$templet_div."','".$templet_name."','".$templet_text."','".$view_order."','".$disp."',NOW())";

	//echo $sql;
	$db->sequences = "BBS_RESPONSE_TEMPLET_SEQ";
	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 템플릿 정보가 정상적으로 등록 되었습니다.');parent.document.location.reload();</script>");
}



if($act == "update"){
	$sql = "update bbs_response_templet set
				templet_div='".$templet_div."',
				templet_name='".$templet_name."',
				templet_text='".$templet_text."',
				view_order='".$view_order."',
				disp='".$disp."'
				where rt_ix = '".$rt_ix."' ";
	//echo $sql;

	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 템플릿 정보가 정상적으로 수정 되었습니다.');parent.document.location.href='./board_response_templet.php';</script>");
}

if($act == "delete"){
	$sql = "delete from bbs_response_templet where rt_ix = '".$rt_ix."' ";
	//echo $sql;

	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 템플릿 정보가 정상적으로 삭제 되었습니다.');parent.document.location.href='./board_response_templet.php';</script>");
}

?>