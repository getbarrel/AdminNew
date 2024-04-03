<?
include("../../class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into ".TBL_SNS_FREEPRODUCT_DIV." (div_ix, div_name,disp,regdate) values('','$div_name','$disp',NOW())";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('분류가 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='free_goods_category.php?mmode=$mmode';</script>");
}


if ($act == "update"){
		
	$sql = "update ".TBL_SNS_FREEPRODUCT_DIV." set div_name='$div_name',disp='$disp' where div_ix='$div_ix' ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('분류가 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'free_goods_category.php?mmode=$mmode';</script>");
}

if ($act == "delete"){
	
	$sql = "delete from ".TBL_SNS_FREEPRODUCT_DIV." where div_ix='$div_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('분류가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='free_goods_category.php?mmode=$mmode';</script>");
}

?>
