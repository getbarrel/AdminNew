<?
//include("../../class/database.class");
include("../class/layout.class");
////////////////////
//  2013.05.07 신훈식
//  수정 : 인클루드 패스 오류
//
/////////////////////
//include("../class/database.class");
include_once('../../include/xmlWriter.php');
session_start();

$db = new Database;
$db2 = new Database;

if($mode == "starndard_category_add_field"){
	for($i=1;$i<=10;$i++){
		$field_name = $_POST['etc'.$i];
		$field_ename = $_POST['etc'.$i.'_ename'];
		$field_type = $_POST['etc'.$i.'_type'];
		$field_search = $_POST['etc'.$i.'_search'];
		$field_value = $_POST['etc'.$i.'_value'];
		//echo $field_name."aa<br>";
		echo "<pre>";
		$db->debug = true;
		if($field_name != ""){
			$db->query("select * from sellertool_category_addfield where site_code = '$site_code' and cid = '$cid' and field_code ='etc".$i."' ");
			if($db->total){
				$db->fetch();

				$sql = "update sellertool_category_addfield set  
							field_search = '$field_search',field_name = '$field_name', field_ename = '$field_ename', field_type = '$field_type',field_value = '$field_value'  
							where cid = '$cid' and site_code = '$site_code' and field_code ='etc".$i."'  ";
				$db->query($sql);
			}else{
				$db->query("insert into sellertool_category_addfield (sca_ix, site_code, cid, field_code, field_search, field_ename, field_name, field_type, field_value,regdate) values ('','$site_code','$cid','etc".$i."','$field_search','$field_ename','$field_name','$field_type','$field_value',NOW() ) ");
			}
		}else{
			$db->query("delete from sellertool_category_addfield where site_code = '$site_code' and cid = '$cid' and field_code ='etc".$i."' ");
		}
	}

	echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>parent.document.location.reload();</Script>";
}

 

if($mode == 'get_sellertool_add_fields'){


	$db->query("select * from sellertool_category_addfield where cid = '$cid' and site_code = '$site_code' ");
	if($db->total){
		$add_fields = $db->fetchall();
	}

	$datas = json_encode($add_fields);
	$datas = str_replace("\"true\"","true",$datas);
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
}
 


 /*
 CREATE TABLE IF NOT EXISTS `sellertool_category_addfield` (
  `sca_ix` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '추가분류 인덱스',
  `cid` varchar(15) NOT NULL DEFAULT '' COMMENT '카테고리아이디',
  `site_code` varchar(50) NOT NULL DEFAULT '' COMMENT '제휴사 코드',
  `field_code` varchar(255) NOT NULL DEFAULT '' COMMENT '필드코드',
  `field_search` char(1) DEFAULT '0' COMMENT '검색표시여부',
  `field_ename` varchar(100) NOT NULL DEFAULT '' COMMENT '영문 필드명',
  `field_name` varchar(100) NOT NULL DEFAULT '' COMMENT '필드명',
  `field_type` enum('text','checkbox','radio','select') DEFAULT 'text' COMMENT '필드타입',
  `field_value` varchar(255) DEFAULT NULL COMMENT '필드기본값',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`sca_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='제휴사이트별 카테고리별 확장컬럼정보'
 */