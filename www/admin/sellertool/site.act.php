<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into sellertool_site_info set 
				si_ix='".$si_ix."',
				site_div='".$site_div."',
				site_name='".$site_name."',
				site_code='".$site_code."',
				site_domain='".$site_domain."',
				site_id='".$site_id."',
				site_pw='".$site_pw."',
                api_key='".$api_key."',
				disp='".$disp."',
				vieworder='".$vieworder."',
				api_yn = '".$api_yn."',
				api_ticket = '".$api_ticket."',
				use_mapping_div='|".implode('|',$use_mapping_div)."|',
				regdate= NOW() ";

	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('제휴사 연동결과 정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
	
	$sql = "update sellertool_site_info set 				
				site_div='".$site_div."',
				site_name='".$site_name."',
				site_code='".$site_code."',
				site_domain='".$site_domain."',
				site_id='".$site_id."',
				site_pw='".$site_pw."',
                api_key='".$api_key."',
				disp='".$disp."',
				vieworder='".$vieworder."',
				api_yn = '".$api_yn."',
				api_ticket = '".$api_ticket."',
				use_mapping_div='|".implode('|',$use_mapping_div)."|'
				where si_ix='".$si_ix."'
  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('제휴사 연동결과  정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from sellertool_site_info where si_ix='$si_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('제휴사 연동결과  정보가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "detail_insert")
{

	
	$sql = "insert into sellertool_site_detail_info set 
				si_ix='".$si_ix."',
				api_name='".$api_name."',
				api_method='".$api_method."',
				api_key_1='".$api_key_1."',
				api_key_2='".$api_key_2."',
				disp='".$disp."',
				regdate= NOW() ";

	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('제휴사 API key 정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "detail_update"){
		
	$sql = "update sellertool_site_detail_info set 	
				si_ix='".$si_ix."',
				api_name='".$api_name."',
				api_method='".$api_method."',
				api_key_1='".$api_key_1."',
				api_key_2='".$api_key_2."',
				disp='".$disp."'
				where sid_ix='".$sid_ix."'
  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('제휴사 API key  정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "detail_delete"){
	
	$sql = "delete from sellertool_site_detail_info where sid_ix='$sid_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('제휴사 API key 정보가 정상적으로 삭제되었습니다.');</script>");
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

*/
?>
