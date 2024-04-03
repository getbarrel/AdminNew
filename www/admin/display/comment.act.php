<?
/*
메인프로모션 분류 추가로 인해 이미지명이 중복되어 저장되므로 분류 코드를 이미지명 앞에 붙여서 중복 저장을 막음(기존에 쓰던 방식은 그대로 유지) kbk 13/05/09
*/
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");

$db = new Database;

if ($act == "insert"){

	$unix_timestamp_sdate = mktime($comment_start_h,$comment_start_i,$comment_start_s,substr($comment_start,5,2),substr($comment_start,8,2),substr($comment_start,0,4));
	$unix_timestamp_edate = mktime($comment_end_h,$comment_end_i,$comment_end_s,substr($comment_end,5,2),substr($comment_end,8,2),substr($comment_end,0,4));

	$sql = "INSERT INTO shop_comment
			(
			 title, title_en, comment_use, comment_state, information,
			 comment_notify, comment_limit, comment_start, comment_end, comment_secret_use, 
			 comment_answer_use, comment_img_use, comment_view_permission, comment_sug_use, 
			 worker_ix, regdate, upddate 
			)
			VALUES
    		(
    		 '".$title."', '".$title_en."', '".$comment_use."', '".$comment_state."', '".$information."', 
    		 '".$comment_notify."', '".$comment_limit."', '".$unix_timestamp_sdate."', '".$unix_timestamp_edate."', '".$comment_secret_use."',
    		 '".$comment_answer_use."', '".$comment_img_use."', '".$comment_view_permission."', '".$comment_sug_use."',
			 '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW()
			)
	";

	$db->query($sql);

	echo "<Script Language='JavaScript'>alert('등록되었습니다.');parent.document.location.href='comment.list.php';</Script>";
}

if ($act == "update"){

	$unix_timestamp_sdate = mktime($comment_start_h,$comment_start_i,$comment_start_s,substr($comment_start,5,2),substr($comment_start,8,2),substr($comment_start,0,4));
	$unix_timestamp_edate = mktime($comment_end_h,$comment_end_i,$comment_end_s,substr($comment_end,5,2),substr($comment_end,8,2),substr($comment_end,0,4));

	$sql = "UPDATE shop_comment SET
			title = '".$title."', title_en = '".$title_en."', comment_use = '".$comment_use."', comment_state = '".$comment_state."', information = '".$information."', 
			comment_notify = '".$comment_notify."', comment_limit = '".$comment_limit."', comment_start = '".$unix_timestamp_sdate."', comment_end = '".$unix_timestamp_edate."', comment_secret_use = '".$comment_secret_use."',
            comment_answer_use = '".$comment_answer_use."', comment_img_use = '".$comment_img_use."', comment_view_permission = '".$comment_view_permission."', comment_sug_use = '".$comment_sug_use."',
            upddate = NOW()
		WHERE
			cmt_ix = '$cmt_ix'
 	";

	$db->query($sql);

	echo "<Script Language='JavaScript'>alert('수정 되었습니다.');parent.document.location.href='comment.write.php?cmt_ix=".$cmt_ix."&act=".$act."';</Script>";
}

if ($act == "delete"){
	$sql = "DELETE FROM shop_comment WHERE cmt_ix = '$cmt_ix' ";

	$db->query($sql);

	echo "<Script Language='JavaScript'>alert('삭제 되었습니다.');parent.document.location.href='comment.list.php';</Script>";
}
?> 