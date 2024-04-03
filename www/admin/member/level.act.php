<?
include("../../class/database.class");

$db = new Database;

if ($act == "update"){

	$data_array = $_REQUEST[data];

	foreach($data_array as $name =>$value){

			$sql = "update shop_level set
						st_point='".$value[st_point]."',
						ed_point='".$value[ed_point]."',
						disp='".$value[disp]."',
						editdate=NOW()
						where level_ix='".$name."'";

			$db->query($sql);

	}
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('레벨정보가 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'level.php';</script>");
}

if ($act == "all_update"){

	$lv_disp = $_REQUEST[lv_disp];
	$order_point = $_REQUEST[order_point];
	$member_cancel = $_REQUEST[member_cancel];
	$member_exchange = $_REQUEST[member_exchange];
	$member_return = $_REQUEST[member_return];
	$order_decide = $_REQUEST[order_decide];


			$sql = "update shop_level set
						lv_disp='".$lv_disp."',
						order_point='".$order_point."',
						member_cancel='".$member_cancel."',
						member_exchange='".$member_exchange."',
						member_return='".$member_return."',
						order_decide='".$order_decide."'
						";
	
			$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('레벨정보가 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'level.php';</script>");
}

?>
