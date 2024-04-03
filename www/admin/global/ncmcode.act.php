<?
include("../class/layout.class");



$db = new MySQL;

if ($act == "insert")
{
	$sql = "insert into global_ncmcode(ncm_ix,ncmcode,hscode,ii,ipi,pis,cofins,icms,ncmcode_desc,is_use,regdate) 
				values
				('','$ncmcode','$hscode','$ii','$ipi','$pis','$cofins','$icms','$ncmcode_desc','$is_use',NOW())

				";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('NCMCODE 정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	$sql = "update global_ncmcode set				
				ncmcode='".$ncmcode."',
				hscode='".$hscode."',
				ii='".$ii."',
				ipi='".$ipi."',
				pis='".$pis."',
				cofins='".$cofins."',
				icms='".$icms."',
				ncmcode_desc='".$ncmcode_desc."',
				is_use='".$is_use."'
				where ncm_ix='".$ncm_ix."' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('NCMCODE 정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){

	$sql = "delete from global_ncmcode where ncm_ix='$ncm_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('NCMCODE 정보가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

 
?>
