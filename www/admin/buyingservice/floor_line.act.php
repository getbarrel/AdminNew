<?
include("../class/layout.class");



$db = new Database;

if($type=='floor'){
	$type='F';
	$coment = "층";
}else{
	$type='L';
	$coment = "라인";
}

if ($act == "insert")
{

	$sql = "insert into buyingservice_floorline_area (fl_ix,code, name,disp,type,language_type,regdate,vieworder) 
			values
			('','$code','$name','$disp','$type','$language_type',NOW(),'$vieworder')";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('".$coment."이 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
		
	$sql = "update buyingservice_floorline_area set code='$code',name='$name',disp='$disp' ,language_type='$language_type' ,vieworder='$vieworder' where fl_ix='$fl_ix'  ";
	
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('".$coment."이 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from buyingservice_floorline_area where fl_ix='$fl_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('".$coment."이 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "json"){
	
	$sql = "select fl_ix , name from buyingservice_floorline_area where code='".$code."'";
	$db->query($sql);


	$datas = $db->fetchall2("object");
	//print_r($events);
	$datas = str_replace("\"true\"","true",json_encode($datas));
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
}

/*

CREATE TABLE `buyingservice_floorline_area` (
  `fl_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '코드',
  `name` varchar(20) DEFAULT NULL COMMENT '이름',
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `type` char(1) DEFAULT NULL COMMENT 'F:층 L:라인',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`fl_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='층,라인 정보';

*/
?>
