<?
include("../class/layout.class");



$db = new MySQL;

if ($act == "insert")
{
	$sql = "insert into global_transport_fee
				(tf_ix,nation_code,transport_type,start_weight,end_weight,cost,is_use,regdate) 
				values
				('','$nation_code','$transport_type','$start_weight','$end_weight','$cost','$is_use',NOW())
				";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('운송비 정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	$sql = "update global_transport_fee set
				nation_code='".$nation_code."',
				transport_type='".$transport_type."',
				start_weight='".$start_weight."',
				end_weight='".$end_weight."',
				cost='".$cost."',
				is_use='".$is_use."'
				where tf_ix='".$tf_ix."' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('운송비 정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){

	$sql = "delete from global_transport_fee where tf_ix='$tf_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('운송비 정보가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

 
?>
