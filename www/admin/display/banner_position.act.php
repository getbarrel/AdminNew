<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{


	$sql = "insert into shop_banner_position(bp_ix,div_ix,bp_name,disp,vieworder,regdate) values ('','$div_ix','$bp_name','$disp','$vieworder',NOW()) ";
	//echo $sql;
	//exit;
	$db->sequences = "SHOP_BANNER_POSITION_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너 위치가 정상적으로 등록되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../mShop/banner_position.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href='banner_position.php?mmode=$mmode';</script>");
	}
}


if ($act == "update"){

	$sql = "update shop_banner_position set div_ix='$div_ix',bp_name='$bp_name',disp='$disp',vieworder='$vieworder' where bp_ix='$bp_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너 위치가 정상적으로 수정되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../mShop/banner_position.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href = 'banner_position.php?mmode=$mmode';</script>");
	}
}

if ($act == "delete"){

	$sql = "delete from shop_banner_position where bp_ix='$bp_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너 위치가 정상적으로 삭제되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../mShop/banner_position.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href='banner_position.php?mmode=$mmode';</script>");
	}
}

?>
