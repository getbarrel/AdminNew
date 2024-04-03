<?
include("../../class/database.class");
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include("../class/layout.class");

$install_path = "../../include/";
include("SOAP/Client.php");
session_start();
//$admininfo[hostserver] = "b2b.mallstory.com";

$db = new Database;


$db->query("SELECT * FROM co_client_hostservers where chs_ix = '".$chs_ix."'  "); 

if($db->total){
	$db->fetch();

	$this_hostserver = $db->dt[server_url];
	$this_server_name = $db->dt[server_name];
}else{
	echo "<script language='javascript'>alert('호스트 서버 선택후 판매사이트 설정이 가능합니다.');</script>";
	exit;
}




if ($act == "update_sellerInfo"){	
	
	$soapclient = new SOAP_Client("http://".$this_hostserver."/admin/cogoods/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	$status = "Y";
	$return_info = $soapclient->call("updateSellerInfo",$params = array("godds_copy"=> $godds_copy, "my_company_id"=> $company_id , "co_company_id"=> $admininfo[company_id]),	$options);
	$return_info = (array)$return_info;
	print_r($return_info);

	if($return_info[bool]){
		echo("<script>alert('".$return_info[message]."');parent.document.location.reload();</script>");	
	}else{
		echo("<script>alert('".$return_info[message]." ');</script>");
	}
}
//updateSellerInfo($godds_copy, $company_id)
/*
create table co_sellershop_apply (
company_id varchar(32) not null,
co_company_id varchar(32) not null,
apply_status enum('AP','AU') default 'AP',
regdate datetime not null,
primary key(company_id))


CREATE TABLE IF NOT EXISTS `co_product_copy_history` (
  cpch_ix int(8) unsigned zerofill NOT NULL auto_increment,
  company_id varchar(32) not null,
  co_company_id varchar(32) not null,
  pid int(10) unsigned zerofill NOT NULL default '0000000000',
  copy_type enum('A','M') default 'M',
  regdate datetime default NULL,
  PRIMARY KEY  (cpch_ix),
  KEY `company_id` (`company_id`),
  KEY `co_company_id` (`co_company_id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='공유상품 복사 히스토리'  ;


*/
?>
