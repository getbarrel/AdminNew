<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{


	$sql = "insert into shop_main_position(mp_ix,agent_type, div_ix,mp_name,disp,vieworder,regdate) values ('','$agent_type','$div_ix','$mp_name','$disp','$vieworder',NOW()) ";
	//echo $sql;
	//exit;
	$db->sequences = "shop_main_position_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메인전시 그룹 위치가 정상적으로 등록되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../mShop/main_goods_category_position.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href='main_goods_category_position.php?mmode=$mmode';</script>");
	}
}


if ($act == "update"){

	$sql = "update shop_main_position set div_ix='$div_ix',mp_name='$mp_name',disp='$disp',vieworder='$vieworder' where mp_ix='$mp_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메인전시 그룹 위치가 정상적으로 수정되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../mShop/main_goods_category_position.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href = 'main_goods_category_position.php?mmode=$mmode';</script>");
	}
}

if ($act == "delete"){

	$sql = "delete from shop_main_position where mp_ix='$mp_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메인전시 그룹 위치가 정상적으로 삭제되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../mShop/main_goods_category_position.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href='main_goods_category_position.php?mmode=$mmode';</script>");
	}
}

?>

