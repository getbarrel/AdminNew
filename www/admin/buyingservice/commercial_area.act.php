<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{

	$sql = "select * from buyingservice_commercial_area where ca_code ='$ca_code' ";
	$db->query($sql);

	if($db->total){
		echo "<script type='text/javascript'>
		<!--
			alert('[".$ca_code."] 는 이미 사용하고 있습니다. 다른코드를 발급해주시기 바랍니다.');
			parent.$.unblockUI();
		//-->
		</script>";
		exit;
	}

	$sql = "insert into buyingservice_commercial_area(ca_ix,ca_code,ca_name_korea,ca_name_english,ca_name_chinese,ca_country,ca_sub_domain,ca_url,ca_charger_ix,ca_start_date,ca_end_date,ca_incentive,ca_give_day,ca_msg,disp,editdate,regdate) values('','$ca_code','$ca_name_korea','$ca_name_english','$ca_name_chinese','$ca_country','$ca_sub_domain','$ca_url','$ca_charger_ix','$ca_start_date','$ca_end_date','$ca_incentive','$ca_give_day','$ca_msg','$disp',NOW(),NOW())";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상권정보가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
		
	$sql = "update buyingservice_commercial_area set
		ca_name_korea='".$ca_name_korea."',
		ca_name_english='".$ca_name_english."',
		ca_name_chinese='".$ca_name_chinese."',
		ca_country='".$ca_country."',
		ca_sub_domain='".$ca_sub_domain."',
		ca_url='".$ca_url."',
		ca_charger_ix='".$ca_charger_ix."',
		ca_start_date='".$ca_start_date."',
		ca_end_date='".$ca_end_date."',
		ca_incentive='".$ca_incentive."',
		ca_give_day='".$ca_give_day."',
		ca_msg='".$ca_msg."',
		disp='".$disp."',
		editdate=NOW()
	where ca_ix='".$ca_ix."'  ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상권정보가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

/*
통합관리 하게 되면 삭제는 안되어야함
if ($act == "delete"){
	
	$sql = "delete from buyingservice_commercial_area where ca_ix='$ca_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상권정보가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}
*/

if ($act == "com_json"){
	
	$sql = "select * from common_company_detail where company_id='".$company_id."'";
	$db->query($sql);

	$datas = $db->fetchall2("object");
	//print_r($events);
	$datas = str_replace("\"true\"","true",json_encode($datas));
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
}

if ($act == "json"){
	
	$sql = "select ca_ix , ca_name_korea , ca_name_english, ca_name_chinese from buyingservice_commercial_area where ca_code='".$ca_code."'";
	$db->query($sql);


	$datas = $db->fetchall2("object");
	//print_r($events);
	$datas = str_replace("\"true\"","true",json_encode($datas));
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
}

/*
CREATE TABLE IF NOT EXISTS `buyingservice_commercial_area` (
  `ca_ix` int(4) unsigned NOT NULL auto_increment COMMENT '인덱스',
  `parent_ca_ix` int(4) unsigned default NULL COMMENT '상위인덱스값',
  `ca_code` varchar(20) NOT NULL default '' COMMENT '상권코드',
  `ca_name` varchar(20) default NULL COMMENT '상권이름',
  `depth` int(2) unsigned default '1' COMMENT '카테고리 depth',
  `disp` char(1) default '1' COMMENT '사용여부',
  `vieworder` int(8) default '0' COMMENT '노출순서',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (`ca_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='상권정보'  ;
*/
?>
