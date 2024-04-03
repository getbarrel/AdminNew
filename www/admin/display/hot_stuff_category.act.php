<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into shop_recommend_div (div_ix, div_name,disp,regdate) values('','$div_name','$disp',NOW())";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메인추천상품 분류가 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='hot_stuff_category.php?mmode=$mmode';</script>");
}


if ($act == "update"){
		
	$sql = "update shop_recommend_div set div_name='$div_name',disp='$disp' where div_ix='$div_ix' ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메인추천상품 분류가 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'hot_stuff_category.php?mmode=$mmode';</script>");
}

if ($act == "delete"){
	
	$sql = "delete from shop_recommend_div where div_ix='$div_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메인추천상품 분류가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='hot_stuff_category.php?mmode=$mmode';</script>");
}

?>
