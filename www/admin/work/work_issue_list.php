<?

include("../class/layout.work.class");
include("work.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../class/calender.big.class");



$mdb = new Database;
WorkConfigSetting($mdb);

$Contents = "
<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation(" 이슈 목록", "업무관리 >  이슈 목록 ")."
	
	</td>
  </tr>
</table>";

$Contents .= "
<table width=100% cellpadding='0' cellspacing=0 border=0>
	<col width='45%'>
	<col width='45%'>
	
	<tr>
		<td align='left' colspan=2 style='vertical-align:top;padding:0px 10px 20px 10px;'> 
	    	".LiveIssue()."
	    </td>
	</tr>
	
</table><br><br><br>";

	
if($mmode == "pop"){
	$P = new ManagePopLayOut();
		
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 이슈 목록";
	$P->title = "이슈 목록";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	echo $P->PrintLayOut();
	
}else if($mmode == "inner_list"){
	echo $inner_view;
}else{
	$P = new LayOut();
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 >  이슈 목록";
	$P->title = "이슈 목록";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	$P->footer_menu = footMenu()."".footAddContents();
	echo $P->PrintLayOut();
}




/*
CREATE TABLE `work_charger_relation` (
  `cr_ix` int(8) unsigned NOT NULL auto_increment ,
  `wl_ix` int(8) DEFAULT NULL,
  `charger_ix` varchar(100) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`cr_ix`)
) TYPE=MyISAM DEFAULT CHARSET=utf8		

CREATE TABLE `work_userinfo` (
  `code` varchar(32) NOT NULL ,
  `google_mail` varchar(100) DEFAULT NULL,
  `google_pass` varchar(100) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`code`)
) TYPE=MyISAM DEFAULT CHARSET=utf8

CREATE TABLE `shop_addressbook` (
  `wl_ix` int(8) unsigned NOT NULL auto_increment,
  `com_div` varchar(20) default '',
  `div` varchar(30) default '',
  `url` varchar(255) default NULL,
  `page` int(8) default '0',
  `com_name` varchar(50) default NULL,
  `charger` varchar(50) default NULL,
  `phone` varchar(50) default NULL,
  `fax` varchar(20) default NULL,
  `mobile` varchar(20) default NULL,
  `email` varchar(50) default NULL,
  `homepage` varchar(50) default NULL,
  `com_address` varchar(50) default NULL,
  `mail_yn` enum('0','1') default '1',
  `marketer` varchar(100) default '',
  `memo` text,
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`wl_ix`),
  KEY `regdate` (`regdate`)
) TYPE=MyISAM DEFAULT CHARSET=utf8


CREATE TABLE `shop_sms_address` (
  `sa_ix` int(8) NOT NULL AUTO_INCREMENT,
  `sg_ix` int(8) DEFAULT NULL,
  `sa_name` varchar(25) NOT NULL DEFAULT '0',
  `sa_mobile` varchar(15) DEFAULT '',
  `sa_sex` enum('M','F')  DEFAULT NULL,
  `sa_etc` varchar(255) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sa_ix`),
  KEY `regdate` (`regdate`),
) ENGINE=MyISAM  DEFAULT CHARSET=utf8

CREATE TABLE `shop_sms_history` (
  `sh_ix` int(8) NOT NULL AUTO_INCREMENT,
  `send_phone` varchar(50) DEFAULT NULL,
  `dest_mobile` varchar(15) DEFAULT '',
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sg_ix`)
}
*/
?>