<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{
	
	
	$sql = "insert into shop_brand_div (bd_ix,parent_bd_ix , div_name,depth, vieworder, disp,regdate) 
			values
			('','$parent_bd_ix','$div_name','$depth','$vieworder','$disp',NOW())";
	$db->query($sql);
	$db->query("SELECT bd_ix FROM shop_brand_div WHERE bd_ix=LAST_INSERT_ID()");
	$db->fetch();
	$bd_ix = $db->dt[0];

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/$bd_ix/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}

	if ($_FILES["brand_div_img"]["size"] >0)
	{
		copy($_FILES["brand_div_img"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/".$bd_ix."/brand_div_".$bd_ix.".gif");
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('브랜드 분류가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
		
	$sql = "update shop_brand_div set div_name='$div_name',vieworder='$vieworder',disp='$disp' where bd_ix='$bd_ix'  ";
	
	$db->query($sql);

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/$bd_ix/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}

	if ($_FILES["brand_div_img"]["size"] >0)
	{
		copy($_FILES["brand_div_img"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/".$bd_ix."/brand_div_".$bd_ix.".gif");
	}

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('브랜드 분류가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($mode == "image_delete")
{
	if ($imagetype == "brand_div"){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/".$bd_ix."/brand_div_".$bd_ix.".gif");
	}

	echo "
		<script language='javascript' src='../js/message.js.php'></script><Script Language='Javascript'>
		show_alert('이미지가 정상적으로 삭제되었습니다.');
		parent.document.location.reload();
		</Script>";
}

if ($act == "delete"){
	
	$sql = "delete from shop_brand_div where bd_ix='$bd_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('브랜드 분류가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


/*
CREATE TABLE IF NOT EXISTS shop_brand_div (
  `bd_ix` int(4) unsigned NOT NULL auto_increment,
  `parent_bd_ix` int(4) unsigned default NULL,
  `div_name` varchar(20) default NULL,
  `depth` int(2) unsigned default '1',
  `disp` char(1) default '1',
  `vieworder` int(8) default '0',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`bd_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

*/
?>
