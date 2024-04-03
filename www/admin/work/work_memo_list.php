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
    <td align='left' colspan=6 > ".GetTitleNavigation(" 메모목록", "업무관리 >  메모목록 ")."
	
	</td>
  </tr>
</table>";

$Contents .= "
<table width=100% cellpadding='0' cellspacing=0 border=0>
	<col width='45%'>
	<col width='45%'>
	
	<tr>
		<td align='left' id='result_area' colspan=2 style='vertical-align:top;padding:0px 10px 20px 10px;'>  ";

$sql = "SELECT wt_ix, work_tmp_title FROM work_tmp  where charger_ix = '".$_SESSION["admininfo"][charger_ix]."' order by regdate desc";
//echo $sql;
$mdb->query($sql);
	$Contents .= "<div class='external-event' wt_ix='' style='display:none;'></div>";
if(!$mdb->total){
		$mdb->fetch($i);
		$Contents .= "<div class='external-event' >메모를 남기실수 있습니다. 더블클릭시 메모가 삭제 됩니다</div>";
}else{
	for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);
		$Contents .= "<div class='external-event element' wt_ix='".$mdb->dt[wt_ix]."' id='memo_list_event_".$mdb->dt[wt_ix]."' style='float:left;word-break:break-all;padding:10px;margin:5px;'  >
							".$mdb->dt[work_tmp_title]."<br><br>
							작성자 : 신훈식  <a href=\"javascript:PopSWindow('work_add.php?mmode=pop&wt_ix=".$mdb->dt[wt_ix]."',800,750,'work_add_info_".$mdb->dt[wt_ix]."')\">내업무담기</a><br>
							".$mdb->dt[regdate]."
							</div>";
	}
}
$Contents .= " 
	    </td>
	</tr>
	
</table><br><br><br>";

$Script = "
<script type='text/javascript' src='../js/jquery.featureList-1.0.0.js'></script>
<script src='../js/jquery.isotope.min.js'></script>
<style type='text/css'>


.element {
  float: left;
  overflow: hidden;
  position: relative;

}
/**** Isotope CSS3 transitions ****/

.isotope,
.isotope .isotope-item {
  -webkit-transition-duration: 0.7s;
     -moz-transition-duration: 0.7s;
      -ms-transition-duration: 0.7s;
       -o-transition-duration: 0.7s;
          transition-duration: 0.7s;
}

.isotope {
  -webkit-transition-property: height, width;
     -moz-transition-property: height, width;
      -ms-transition-property: height, width;
       -o-transition-property: height, width;
          transition-property: height, width;
}

.isotope .isotope-item {
  -webkit-transition-property: -webkit-transform, opacity;
     -moz-transition-property:    -moz-transform, opacity;
      -ms-transition-property:     -ms-transform, opacity;
       -o-transition-property:      -o-transform, opacity;
          transition-property:         transform, opacity;
}

/**** disabling Isotope CSS3 transitions ****/

.isotope.no-transition,
.isotope.no-transition .isotope-item,
.isotope .isotope-item.no-transition {
  -webkit-transition-duration: 0s;
     -moz-transition-duration: 0s;
      -ms-transition-duration: 0s;
       -o-transition-duration: 0s;
          transition-duration: 0s;
}
</style>
";
	
if($mmode == "pop"){
	$P = new ManagePopLayOut();
		
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 메모목록";
	$P->title = "메모목록";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	echo $P->PrintLayOut();
	
}else if($mmode == "inner_list"){
	echo $inner_view;
}else{
	$P = new LayOut();
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 >  메모목록";
	$P->title = "메모목록";
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