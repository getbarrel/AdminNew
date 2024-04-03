<?
include("../../class/database.class");

$db = new Database;

if ($act == "insert")
{

	$sql = "
		insert into common_dropmember_setup set
			dp_code = '".$dp_code."',
			dp_name = '".$dp_name."',
			dp_msg = '".$dp_msg."',
			disp = '".$disp."',
			regdate = NOW();
	";

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('탈퇴사유설정이 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='dropmember_setup.php';</script>");

}


if ($act == "update"){

    if($disp == NULL || $disp == ""){
        $disp = 1;
    }

		$sql = "update common_dropmember_setup set
						dp_name = '".$dp_name."',
						dp_code = '".$dp_code."',
						dp_msg = '".$dp_msg."',
						disp = '".$disp."',
						editdate=NOW()
						where drop_ix='".$drop_ix."'";

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('그룹이 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'dropmember_setup.php';</script>");
}

if ($act == "delete"){


		$sql = "delete from common_dropmember_setup where drop_ix='$drop_ix'";
		$db->query($sql);

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('그룹이 정상적으로 삭제되었습니다.');</script>");
		echo("<script>top.document.location.href='dropmember_setup.php';</script>");
		exit;

}




?>
