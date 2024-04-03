<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into shop_realestate_region (rg_ix,parent_rg_ix , region_name,depth, vieworder, disp,regdate) 
			values
			('','$parent_rg_ix','$region_name','$depth','$vieworder','$disp',NOW())";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('지역이 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
		
	$sql = "update shop_realestate_region set region_name='$region_name',vieworder='$vieworder',disp='$disp' where rg_ix='$rg_ix'  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('지역이 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from shop_realestate_region where rg_ix='$rg_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('지역이 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


/*
CREATE TABLE IF NOT EXISTS shop_realestate_region (
  `rg_ix` int(4) unsigned NOT NULL auto_increment,
  `parent_rg_ix` int(4) unsigned default NULL,
  `region_name` varchar(20) default NULL,
  `depth` int(2) unsigned default '1',
  `disp` char(1) default '1',
  `vieworder` int(8) default '0',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`rg_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

*/
?>
