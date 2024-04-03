<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into shop_car_model (md_ix,parent_md_ix ,vechile_div, mf_ix, vt_ix,  model_name,depth, vieworder, disp,regdate) 
			values
			('','$parent_md_ix','".$vechile_div."','".$mf_ix."','".$vt_ix."','$model_name','$depth','$vieworder','$disp',NOW())";
			
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('모델이 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
		
	$sql = "update shop_car_model set 
			vechile_div='$vechile_div',model_name='$model_name',vieworder='$vieworder',disp='$disp' 
			where md_ix='$md_ix'  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('모델이 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from shop_car_model where md_ix='$md_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('모델이 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "json"){
	
	$sql = "select md_ix , model_name from shop_car_model where vechile_div='".$vechile_div."'";
	$db->query($sql);


	$datas = $db->fetchall2("object");
	//print_r($events);
	$datas = str_replace("\"true\"","true",json_encode($datas));
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
}

/*
CREATE TABLE IF NOT EXISTS shop_car_model (
  `md_ix` int(4) unsigned NOT NULL auto_increment,
  `parent_md_ix` int(4) unsigned default NULL,
  `model_name` varchar(20) default NULL,
  `depth` int(2) unsigned default '1',
  `disp` char(1) default '1',
  `vieworder` int(8) default '0',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`md_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

*/
?>
