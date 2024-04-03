<?
include("../class/layout.class");

$db = new Database;

if($act == 'update'){

	$sql = "update shop_cooperation set
				md_code = '".$md_code."',
				md_name = '".$md_name."',
				status = '".$status."',
				reply = '".$reply."'
			where
				ix = '".$ix."'";

	$db->query($sql);
	echo "<script>alert('수정되었습니다.');self.close();</script>";
}
?>