<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into sellertool_site_seller_info
				(ssi_ix,si_ix,site_id,site_pw,api_key,api_ticket,disp,api_type,regdate) 
				values
				('','$si_ix','$site_id','$site_pw','$api_key','$api_ticket','$disp','$api_type',NOW())  ";

	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('셀러별 제휴사 연동결과 정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
	
	$sql = "update sellertool_site_seller_info set 				
				si_ix='$si_ix',
				site_id='$site_id',
				site_pw='$site_pw',
				api_key='$api_key',
				api_ticket='$api_ticket',
				disp='$disp',
				api_type='$api_type'
				where ssi_ix='".$ssi_ix."'
  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('셀러별 제휴사 연동결과  정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from sellertool_site_seller_info where ssi_ix='$ssi_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('셀러별 제휴사 연동결과  정보가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

 
/*
CREATE TABLE IF NOT EXISTS shop_buyingservice_site (
  `si_ix` int(4) unsigned NOT NULL auto_increment,
  `parent_si_ix` int(4) unsigned default NULL,
  `site_name` varchar(20) default NULL,
  `depth` int(2) unsigned default '1',
  `disp` char(1) default '1',
  `vieworder` int(8) default '0',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`si_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;




CREATE TABLE IF NOT EXISTS `sellertool_site_seller_info` (
  `ssi_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `si_ix` int(4) unsigned NOT NULL COMMENT ' 제휴사이트 인덱스',
  `site_id` varchar(50) DEFAULT NULL COMMENT '사이트 아이디',
  `site_pw` varchar(255) DEFAULT NULL COMMENT '사이트 비밀번호',
  `api_key` varchar(255) DEFAULT NULL COMMENT 'seller api key',
  `api_ticket` varchar(255) NOT NULL COMMENT '티켓키값',
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `api_type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '사이트 기본여부 1 : 자체 API , 2: 대표 API ',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`ssi_ix`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='셀러별 제휴사(오픈마켓) 정보'


*/
?>
