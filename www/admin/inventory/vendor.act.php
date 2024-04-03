<?
//print_r($_POST);
//exit;
include("../../class/database.class");
$db = new Database;
//echo $customer_div;
if($act == "insert"){
	$customer_phone = $customer_phone1."-".$customer_phone2."-".$customer_phone3;
	$customer_fax = $customer_fax1."-".$customer_fax2."-".$customer_fax3;

	$com_phone = $com_phone1."-".$com_phone2."-".$com_phone3;
	$com_fax = $com_fax1."-".$com_fax2."-".$com_fax3;
	$com_zip = $com_zip1."-".$com_zip2;

	$charger_phone = $charger_phone1."-".$charger_phone2."-".$charger_phone3;
	$charger_mobile = $charger_mobile1."-".$charger_mobile2."-".$charger_mobile3;


	$sql = "insert into inventory_customer_info
	(ci_ix,customer_type,customer_div,company_id,customer_name,customer_position,storage_fee,customer_phone,customer_fax,customer_msg,regdate) values
	('','$customer_type','$customer_div','$company_id','$customer_name','$customer_position','$storage_fee','$customer_phone','$customer_fax','$customer_msg',NOW())";

	//echo nl2br($sql);
	//exit;
	$db->sequences = "INVENTORY_CUSTOMER_INFO_SEQ";
	$db->query($sql);

	if($customer_div == "9"){

		if($db->dbms_type == "oracle"){
			$ci_ix = $db->last_insert_id;
		}else{
			$db->query("SELECT ci_ix FROM inventory_customer_info WHERE ci_ix=LAST_INSERT_ID() ");
			$db->fetch();
			$ci_ix = $db->dt[ci_ix];
		}

		$sql = "insert into inventory_company_detail
		(ci_ix,com_name,com_div,com_ceo,com_business_status,com_business_category,com_number,online_business_number,com_phone,com_fax,homepage,com_email,com_zip,com_addr1,com_addr2,bank_owner,bank_name,bank_number,charger,charger_email,charger_phone,charger_mobile,charger_department,charger_position,regdate) values
		('$ci_ix','$com_name','$com_div','$com_ceo','$com_business_status','$com_business_category','$com_number','$online_business_number','$com_phone','$com_fax','$homepage','$com_email','$com_zip','$com_addr1','$com_addr2','$bank_owner','$bank_name','$bank_number','$charger','$charger_email','$charger_phone','$charger_mobile','$charger_department','$charger_position',NOW())";

		//echo nl2br($sql);
		//exit;
		$db->sequences = "INVENTORY_COMPANY_DT_SEQ";
		$db->query($sql);

	}

	if($customer_type == "D"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('판매처가 정상적으로 등록 되었습니다.');document.location.href='sales_vendor_list.php'</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('입고처가 정상적으로 등록 되었습니다.');document.location.href='supply_vendor_list.php'</script>";
	}

}
if($act == "delete"){
	if($customer_div == "E"){
		$db->query("delete from inventory_customer_info where ci_ix = '".$ci_ix."'");

		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('입고처가 정상적으로 삭제 되었습니다.');document.location.href='supply_vendor_list.php'</script>";
	}else{
		$db->query("delete from inventory_customer_info where ci_ix = '".$ci_ix."'");

		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('판매처가 정상적으로 삭제 되었습니다.');document.location.href='sales_vendor_list.php'</script>";
	}
}

if($act == "update"){

	$customer_phone = $customer_phone1."-".$customer_phone2."-".$customer_phone3;
	$customer_fax = $customer_fax1."-".$customer_fax2."-".$customer_fax3;

	$com_phone = $com_phone1."-".$com_phone2."-".$com_phone3;
	$com_fax = $com_fax1."-".$com_fax2."-".$com_fax3;
	$com_zip = $com_zip1."-".$com_zip2;

	$charger_phone = $charger_phone1."-".$charger_phone2."-".$charger_phone3;
	$charger_mobile = $charger_mobile1."-".$charger_mobile2."-".$charger_mobile3;

	$sql = "update inventory_customer_info set
				customer_type='".$customer_type."',
				customer_div='".$customer_div."',
				company_id='".$company_id."',
				customer_name='".$customer_name."',
				customer_position='".$customer_position."',
				storage_fee='".$storage_fee."',
				customer_phone='".$customer_phone."',
				customer_fax='".$customer_fax."',
				customer_msg='".$customer_msg."'
				where ci_ix = '$ci_ix'  ";

	$db->query($sql);

	if($customer_div == "9"){

		$sql = "update inventory_company_detail set
		com_name='".$com_name."',
		com_div='".$com_div."',
		com_ceo='".$com_ceo."',
		com_business_status='".$com_business_status."',
		com_business_category='".$com_business_category."',
		com_number='".$com_number."',
		online_business_number='".$online_business_number."',
		com_phone='".$com_phone."',
		com_fax='".$com_fax."',
		homepage='".$homepage."',
		com_email='".$com_email."',
		com_zip='".$com_zip."',
		com_addr1='".$com_addr1."',
		com_addr2='".$com_addr2."',
		bank_owner='".$bank_owner."',
		bank_name='".$bank_name."',
		bank_number='".$bank_number."',
		charger='".$charger."',
		charger_email='".$charger_email."',
		charger_phone='".$charger_phone."',
		charger_mobile='".$charger_mobile."',
		charger_department='".$charger_department."',
		charger_position='".$charger_position."'
		where ci_ix = '$ci_ix'   ";

		//echo nl2br($sql);
		//exit;

		$db->query($sql);
	}

	if($customer_type == "D"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('판매처가 정상적으로 수정 되었습니다.');document.location.href='sales_vendor_list.php'</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('입고처가 정상적으로 수정 되었습니다.');document.location.href='supply_vendor_list.php'</script>";
	}
}

/*

CREATE TABLE IF NOT EXISTS `inventory_customer_info` (
  `ci_ix` int(6) NOT NULL auto_increment,
  `customer_type` enum('D','E') NOT NULL default '1',
  `customer_div` enum('1','9') NOT NULL default '1',
  `customer_name` varchar(255) default NULL,
  `customer_phone` varchar(13) default NULL,
  `customer_fax` varchar(13) default NULL,
  `customer_msg` mediumtext,
  `regdate` datetime default NULL,
  PRIMARY KEY  (`ci_ix`),
  KEY `customer_div` (`customer_div`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8



CREATE TABLE IF NOT EXISTS `inventory_company_detail` (
  `company_id` varchar(32) NOT NULL default '',
  `ci_ix` int(6) NOT NULL ,
  `com_name` varchar(50) default NULL,
  `com_div` enum('P','R') NOT NULL default 'P' COMMENT 'P:개인 , R :  법인',
  `com_type` enum('G','S','A') NOT NULL default 'G' COMMENT 'G : 일반기업, S : 셀러 , A : 쇼핑몰 운영업체',
  `com_ceo` varchar(20) default NULL,
  `com_business_status` varchar(255) default NULL,
  `com_business_category` varchar(255) default NULL,
  `com_number` varchar(20) default NULL,
  `online_business_number` varchar(30) NOT NULL,
  `com_phone` varchar(15) default NULL,
  `com_fax` varchar(15) default NULL,
  `com_email` varchar(100) NOT NULL,
  `com_zip` varchar(7) default NULL,
  `com_addr1` varchar(255) default NULL,
  `com_addr2` varchar(255) default NULL,
  `bank_owner` varchar(20) default NULL,
  `bank_name` varchar(20) default NULL,
  `bank_number` varchar(30) default NULL,
  `charger` varchar(20) default NULL,
  `charger_email` varchar(40) default NULL,
  `charger_phone` varchar(20) default NULL,
  `charger_mobile` varchar(20) default NULL,
  `charger_department` varchar(50) default NULL,
  `charger_position` varchar(50) default NULL,
  `regdate` datetime NOT NULL,
  PRIMARY KEY  (`company_id`,`ci_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


*/
?>