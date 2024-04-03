<?
include("../class/layout.class");



$db = new MySQL;

if ($act == "insert")
{
	$sql = "insert into global_hscode
				(hs_ix,hscode,basic_tax_rate,hscode_desc,is_use,regdate) 
				values
				('','$hscode','$basic_tax_rate','$hscode_desc','$is_use',NOW())
				";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('HSCODE 정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	$sql = "update global_hscode set 
				hscode='".$hscode."',
				basic_tax_rate='".$basic_tax_rate."',
				hscode_desc='".$hscode_desc."',
				is_use='".$is_use."'
				where hs_ix='".$hs_ix."' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('HSCODE 정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){

	$sql = "delete from global_hscode where hs_ix='$hs_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('HSCODE 정보가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

 
?>
