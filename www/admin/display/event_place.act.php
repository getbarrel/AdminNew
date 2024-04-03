<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;
 

if($act == "update"){
	$gift_start_date = $FromYY."-".$FromMM."-".$FromDD;
	$gift_end_date = $ToYY."-".$ToMM."-".$ToDD;
	
	$sql = "update shop_event_place set				
				place_name='".$_POST["place_name"]."',
				place_zip='".$_POST["place_zip"]."',
				place_addr1='".$_POST["place_addr1"]."',
				place_addr2='".$_POST["place_addr2"]."',
				place_radius='".$_POST["place_radius"]."',
				place_latitude='".$_POST["place_latitude"]."',
				place_longitude='".$_POST["place_longitude"]."'
				where place_ix='".$_POST["place_ix"]."' ";

	$db->query($sql);

	echo "<script>alert('플레이스 정보가 정상적으로 수정되었습니다.');parent.document.location.href='event_place_list.php';</script>";
}

if($act == "insert"){
 
	$sql = "insert into shop_event_place
				(place_ix,place_name,place_zip,place_addr1,place_addr2,place_radius,place_latitude,place_longitude,regdate)
				values
				('','".$_POST["place_name"]."','".$_POST["place_zip"]."','".$_POST["place_addr1"]."','".$_POST["place_addr2"]."','".$_POST["place_radius"]."','".$_POST["place_latitude"]."','".$_POST["place_longitude"]."',NOW())";

	$db->query($sql);
	echo "<script>alert('플레이스 정보가 정상적으로 입력되었습니다.');parent.document.location.href='event_place_list.php';</script>";
}


if($act == "delete"){
	
	$sql = "delete from shop_event_place where place_ix='$place_ix' ";					
 
	$db->query($sql);

	echo "<script>alert(\"플레이스 정보가 정상적으로 삭제 되었습니다. \");parent.document.location.reload();</script>";
}


/*
CREATE TABLE `shop_event_place` (
  `place_ix` int(8) NOT NULL AUTO_INCREMENT,
  `place_name` varchar(255) NOT NULL DEFAULT '',
  `place_zip` varchar(10) NOT NULL DEFAULT '',
  `place_addr1` varchar(100) NOT NULL DEFAULT '',
  `place_addr2` varchar(100) NOT NULL DEFAULT '',
  `place_radius` varchar(100) NOT NULL DEFAULT '',
  `place_latitude` varchar(100) NOT NULL DEFAULT '',
  `place_longitude` varchar(100) NOT NULL DEFAULT '',
  `insert_yn` enum('Y','N') DEFAULT 'Y',
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`place_ix`)
) ENGINE=INNODB DEFAULT CHARSET=utf8

*/
?>