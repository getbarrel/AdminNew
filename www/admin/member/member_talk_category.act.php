<?
include("../../class/database.class");

$db = new Database;

if ($act == "insert")
{

	$sql = "
		insert into shop_member_talk_category set
			tc_code = '".$tc_code."',
			tc_name = '".$tc_name."',
			tc_msg = '".$tc_msg."',
			disp = '".$disp."',
			regdate = NOW();
	";

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('문의분류가 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='member_talk_category.php?info_type=category';</script>");

}


if ($act == "update"){

    if($disp == NULL || $disp == ""){
        $disp = 1;
    }

		$sql = "update shop_member_talk_category set
						tc_name = '".$tc_name."',
						tc_code = '".$tc_code."',
						tc_msg = '".$tc_msg."',
						disp = '".$disp."',
						editdate=NOW()
						where tc_ix='".$tc_ix."'";

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('문의분류가 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'member_talk_category.php?info_type=category';</script>");
}

if ($act == "delete"){


		$sql = "delete from shop_member_talk_category where tc_ix='$tc_ix'";
		$db->query($sql);

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('문의분류가 정상적으로 삭제되었습니다.');</script>");
		echo("<script>document.location.href='member_talk_category.php?info_type=category';</script>");
		exit;

}

?>
