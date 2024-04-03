<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{


	$sql = "insert into shop_banner_div (div_ix,agent_type, div_name,disp,regdate) values('','$agent_type','$div_name','$disp',NOW())";
	$db->sequences = "SHOP_BANNER_DIV_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('베너 분류가 정상적으로 등록되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../mShop/banner_category.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href='banner_category.php?mmode=$mmode';</script>");
	}
}


if ($act == "update"){

	$sql = "update shop_banner_div set div_name='$div_name',disp='$disp' where div_ix='$div_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('베너 분류가 정상적으로 수정되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../mShop/banner_category.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.location.href = 'banner_category.php?mmode=$mmode';</script>");
	}
}

if ($act == "delete"){

	$sql = "delete from shop_banner_div where div_ix='$div_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('베너 분류가 정상적으로 삭제되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../mShop/banner_category.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href='banner_category.php?mmode=$mmode';</script>");
	}
}

?>
