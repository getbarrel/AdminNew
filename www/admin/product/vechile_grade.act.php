<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into shop_car_grade (gr_ix,parent_gr_ix , vechile_div, md_ix, grade_name,depth, vieworder, disp,regdate) 
			values
			('','$parent_gr_ix','".$vechile_div."','".$md_ix."','$grade_name','$depth','$vieworder','$disp',NOW())";
			
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('자동차 등급이 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
		
	$sql = "update shop_car_grade set 
			vechile_div='$vechile_div',grade_name='$grade_name',vieworder='$vieworder',disp='$disp' 
			where gr_ix='$gr_ix'  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('자동차 등급이 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from shop_car_grade where gr_ix='$gr_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('자동차 등급이 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


/*
CREATE TABLE IF NOT EXISTS shop_car_grade (
  `gr_ix` int(4) unsigned NOT NULL auto_increment,
  `parent_gr_ix` int(4) unsigned default NULL,
  `gr_ix` int(4) unsigned default NULL,
  `grade_name` varchar(20) default NULL,
  `depth` int(2) unsigned default '1',
  `disp` char(1) default '1',
  `vieworder` int(8) default '0',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`gr_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='자동차 등급정보';

*/
?>
