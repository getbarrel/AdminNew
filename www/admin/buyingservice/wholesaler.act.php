<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into buyingservice_wholesaler set 
				ws_ix='".$ws_ix."',
				sc_ix='".$sc_ix."',
				ws_name='".$ws_name."',
				ws_tel='".$ws_tel."',
				ws_fax='".$ws_fax."',
				ws_email='".$ws_email."',
				floor='".$floor."',
				line='".$line."',
				no='".$no."',
				homepage='".$homepage."',
				ws_zip='".$ws_zip."',
				ws_addr1='".$ws_addr1."',
				ws_addr2='".$ws_addr2."',
				ws_msg='".$ws_msg."',
				disp='".$disp."',
				regdate=NOW() ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('도매처정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
		
	$sql = "update buyingservice_wholesaler set 
				sc_ix='".$sc_ix."',
				ws_name='".$ws_name."',
				ws_tel='".$ws_tel."',
				ws_fax='".$ws_fax."',
				ws_email='".$ws_email."',
				floor='".$floor."',
				line='".$line."',
				no='".$no."',
				homepage='".$homepage."',
				ws_zip='".$ws_zip."',
				ws_addr1='".$ws_addr1."',
				ws_addr2='".$ws_addr2."',
				ws_msg='".$ws_msg."',
				disp='".$disp."'
				 where ws_ix='$ws_ix'  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('도매처정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from buyingservice_wholesaler where ws_ix='$ws_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('도매처정보가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "json"){
	
	$sql = "select ws_ix , ws_name from buyingservice_wholesaler where ws_code='".$ws_code."'";
	$db->query($sql);


	$datas = $db->fetchall2("object");
	//print_r($events);
	$datas = str_replace("\"true\"","true",json_encode($datas));
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
}

/*
CREATE TABLE IF NOT EXISTS buyingservice_wholesaler (
  `ws_ix` int(6) NOT NULL auto_increment COMMENT '상가정보 관리키',
  `sc_ix` varchar(5) NOT NULL COMMENT '상가코드',
  `ws_name` varchar(100) default NULL COMMENT '도매처명',
  `ws_tel` varchar(13) default NULL COMMENT '전화번호',
  `ws_fax` varchar(13) default NULL COMMENT '팩스번호', 
  `floor` varchar(5) NOT NULL default '' COMMENT '층',
  `line` varchar(5) NOT NULL default '' COMMENT '라인',
  `no` varchar(5) NOT NULL default '' COMMENT '호수',
  `homepage` VARCHAR( 100 ) NOT NULL COMMENT '홈페이지 URL' ,
  `ws_zip` VARCHAR( 20 ) NOT NULL COMMENT '도매처 우편번호' ,
  `ws_addr1` VARCHAR( 100 ) NOT NULL COMMENT '도매처 주소',
  `ws_addr2` VARCHAR( 100 ) NOT NULL COMMENT '도매처주소 상세',
  `ws_msg` mediumtext COMMENT '설명', 
  `disp` enum('Y','N') default 'Y' COMMENT '사용여부',
  `regdate` datetime default NULL COMMENT '등록일',
  PRIMARY KEY  (`ws_ix`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='도매처 정보'  ;
*/
?>
