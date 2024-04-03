<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into buyingservice_commercial_area (ca_ix,parent_ca_ix ,ca_code, ca_name,depth, vieworder, disp,regdate) 
			values
			('','$parent_ca_ix','$ca_code','$ca_name','$depth','$vieworder','$disp',NOW())";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상권정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
		
	$sql = "update buyingservice_commercial_area set ca_code='$ca_code',ca_name='$ca_name',vieworder='$vieworder',disp='$disp' where ca_ix='$ca_ix'  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상권정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from buyingservice_commercial_area where ca_ix='$ca_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상권정보가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "json"){
	
	$sql = "select ca_ix , ca_name from buyingservice_commercial_area where ca_code='".$ca_code."'";
	$db->query($sql);


	$datas = $db->fetchall2("object");
	//print_r($events);
	$datas = str_replace("\"true\"","true",json_encode($datas));
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
}

/*
CREATE TABLE IF NOT EXISTS `buyingservice_commercial_area` (
  `ca_ix` int(4) unsigned NOT NULL auto_increment COMMENT '인덱스',
  `parent_ca_ix` int(4) unsigned default NULL COMMENT '상위인덱스값',
  `ca_code` varchar(20) NOT NULL default '' COMMENT '상권코드',
  `ca_name` varchar(20) default NULL COMMENT '상권이름',
  `depth` int(2) unsigned default '1' COMMENT '카테고리 depth',
  `disp` char(1) default '1' COMMENT '사용여부',
  `vieworder` int(8) default '0' COMMENT '노출순서',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (`ca_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='상권정보'  ;
*/
?>
