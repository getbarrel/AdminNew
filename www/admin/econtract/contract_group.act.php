<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{


	$sql = "insert into econtract_group (group_ix, group_name,disp,regdate) values('','$group_name','$disp',NOW())";
	$db->sequences = "ECONTRACT_GROUP_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메인페이지 전시 분류가 정상적으로 등록되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../econtract/contract_group.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href='contract_group.php?mmode=$mmode';</script>");
	}
}


if ($act == "update"){

	$sql = "update econtract_group set group_name='".$group_name."',disp='".$disp."' where group_ix='$group_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메인페이지 전시 분류가 정상적으로 수정되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../econtract/contract_group.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href = 'contract_group.php?mmode=$mmode';</script>");
	}
}

if ($act == "delete"){

	$sql = "delete from econtract_group where group_ix='$group_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메인페이지 전시 분류가 정상적으로 삭제되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>parent.document.location.href='../econtract/contract_group.php?mmode=$mmode';</script>");
	}else{
		echo("<script>parent.document.location.href='contract_group.php?mmode=$mmode';</script>");
	}
}


/*
CREATE TABLE IF NOT EXISTS `econtract_group` (
  `group_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `group_name` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '구분명',
  `disp` char(1) CHARACTER SET utf8 DEFAULT '1' COMMENT '노출여부',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`group_ix`),
  KEY `disp` (`disp`,`group_ix`)
) ENGINE=MyISAM  COMMENT='계약서 분류정보'  


*/
?>
