<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{
	$sql = "insert into shop_buyingservice_site (bs_ix,parent_bs_ix , site_name,site_code,site_domain,depth, vieworder, disp,regdate)
			values
			('','$parent_bs_ix','$site_name','$site_code','$site_domain','$depth','$vieworder','$disp',NOW())";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('구매대행 사이트 정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	$sql = "update shop_buyingservice_site set site_name='$site_name',site_code='$site_code',site_domain='$site_domain',vieworder='$vieworder',disp='$disp' where bs_ix='$bs_ix'  ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('구매대행 사이트 정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){

	$sql = "delete from shop_buyingservice_site where bs_ix='$bs_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('구매대행 사이트 정보가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


/*
CREATE TABLE IF NOT EXISTS shop_buyingservice_site (
  `bs_ix` int(4) unsigned NOT NULL auto_increment,
  `parent_bs_ix` int(4) unsigned default NULL,
  `site_name` varchar(20) default NULL,
  `depth` int(2) unsigned default '1',
  `disp` char(1) default '1',
  `vieworder` int(8) default '0',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`bs_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

*/
?>
