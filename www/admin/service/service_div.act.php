<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into service_division (service_ix,parent_service_ix , service_name,service_code,depth, vieworder, disp,regdate) 
			values
			('','$parent_service_ix','$service_name','$service_code','$depth','$vieworder','$disp',NOW())";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('서비스 정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
		
	$sql = "update service_division set service_name='$service_name',service_code='$service_code',vieworder='$vieworder',disp='$disp' where service_ix='$service_ix'  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('서비스 정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from service_division where service_ix='$service_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('서비스 정보가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


/*
CREATE TABLE IF NOT EXISTS `service_division` (
  `service_ix` int(4) unsigned NOT NULL auto_increment COMMENT '인덱스',
  `parent_service_ix` int(4) unsigned default NULL COMMENT '상위카테고리인덱스값',
  `service_name` varchar(20) default NULL COMMENT '서비스명',
  `service_code` varchar(20) default NULL COMMENT '서비스코드',
  `depth` int(2) unsigned default '1' COMMENT '카테고리depth',
  `disp` char(1) default '1' COMMENT '사용여부',
  `vieworder` int(8) default '0' COMMENT '노출순서',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (`service_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='서비스 코드정보'  ;


*/
?>
