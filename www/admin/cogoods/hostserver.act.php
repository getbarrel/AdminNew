<?
include("../../class/database.class");
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include("../class/layout.class");

$install_path = "../../include/";
include("SOAP/Client.php");
session_start();
//$admininfo[hostserver] = "b2b.mallstory.com";

$db = new Database;



if ($act == "hostserver_update"){		
	//print_r($_GET);
	//echo $server_name;
	if($basic == 1){
	$sql = "update co_client_hostservers set 
				basic='0'  ";
	//echo $sql;
	$db->query($sql);
	}

	$sql = "update co_client_hostservers set 
				server_name='".$_POST["server_name"]."',server_url='".$server_url."', basic='".$basic."', disp='".$disp."' 
				where chs_ix='".$chs_ix."' ";
	//echo $sql;
	//exit;
	$db->query($sql);
	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정 되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
	exit;
}

if ($act == "hostserver_insert"){		

	$sql = "insert into co_client_hostservers(chs_ix,server_name,server_url,basic,disp,regdate) values('','$server_name','$server_url','$basic','$disp',NOW()) ";
	//echo $sql;
	$db->query($sql);
	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 등록 되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
	exit;
}

if ($act == "hostserver_delete"){		

	$sql = "delete from co_client_hostservers  where chs_ix = '".$chs_ix."' ";
	//echo $sql;
	$db->query($sql);
	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제 되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
	exit;
}

$db->query("SELECT * FROM co_client_hostservers where chs_ix = '".$chs_ix."'  "); 

if($db->total){
	$db->fetch();

	$this_hostserver = $db->dt[server_url];
	$this_server_name = $db->dt[server_name];
}else{
	echo "<script language='javascript'>alert('호스트 서버 선택후 판매사이트 설정이 가능합니다.');</script>";
	exit;
}


if ($act == "update"){	
	
	$sql = "update ".TBL_SHOP_SHOPINFO." set  hostserver = '".$_POST["hostserver"]."' where mall_ix = '$mall_ix' and mall_div = '$mall_div' ";
	//echo $sql;
	$db->query($sql);
	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'hostserver.php';</script>");
}


if ($act == "sellershop_insert"){		
	
	$soapclient = new SOAP_Client("http://".$this_hostserver."/admin/cogoods/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	
	//echo $this_hostserver;
	//exit;
	//
	/*
	$sql = "SELECT ccd.company_id,com_name,com_div,com_type,com_ceo,com_business_status,com_business_category,com_number,online_business_number,com_phone,com_fax,com_email,com_zip,com_addr1,com_addr2,
	bank_owner,bank_name,bank_number,shop_name,shop_desc , '".$_SERVER["HTTP_HOST"]."' as url
	FROM ".TBL_COMMON_COMPANY_DETAIL." ccd, ".TBL_COMMON_SELLER_DETAIL." csd 
	WHERE ccd.company_id = csd.company_id and ccd.company_id = '".$admininfo[company_id]."'"; //business_day,
	$db->query($sql);
	*/
	$sql = "select * from ".TBL_COMMON_USER." cu where cu.company_id = '".$admininfo[company_id]."' and code = '".$admininfo[charger_ix]."' ";
	//echo $sql;
	$db->query($sql);
	$db->fetch(0,"object");
	$sellerInfo[user] = (array)$db->dt;

	//print_r($admininfo);
	//print_r($sellerInfo);
	//exit;

	$sql = "select cmd.code,birthday,birthday_div,
				AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, 
				AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,
				AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
				AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,
				AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,
				AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
				info,sms,nick_name,job,cmd.date,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6 
				from ".TBL_COMMON_MEMBER_DETAIL." cmd 
				where code = '".$admininfo[charger_ix]."' ";
	$db->query($sql);
	$db->fetch(0,"object");
	$sellerInfo[member_detail] = (array)$db->dt;

	$sql = "select * from ".TBL_COMMON_COMPANY_DETAIL." ccd where ccd.company_id = '".$admininfo[company_id]."' ";
	$db->query($sql);
	$db->fetch(0,"object");
	$sellerInfo[company_detail] = (array)$db->dt;

	$sql = "select csd.* ,'".$_SERVER["HTTP_HOST"]."' as shop_url from ".TBL_COMMON_SELLER_DETAIL." csd where csd.company_id = '".$admininfo[company_id]."' ";
	$db->query($sql);
	$db->fetch(0,"object");
	$sellerInfo[seller_detail] = (array)$db->dt;


	$sql = "select * from ".TBL_COMMON_SELLER_DELIVERY." csd where csd.company_id = '".$admininfo[company_id]."' ";
	$db->query($sql);
	$db->fetch(0,"object");
	$sellerInfo[seller_delivery] = (array)$db->dt;
	
	//print_r($sellerInfo);
	//exit;
	$ret = $soapclient->call("SellerShopAdd",$params = array("sellerInfo"=> $sellerInfo),	$options);
	print_r($ret);
	exit;
	if($ret){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('판매사이트가 정상적으로 등록 되었습니다.');parent.document.location.reload();</script>");
	}else{
		echo("<script>alert('이미 등록된 판매사이트 입니다.');</script>");
	}

}


if ($act == "seller_reg_auth"){	
	
	$soapclient = new SOAP_Client("http://".$this_hostserver."/admin/cogoods/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	$status = "Y";
	$return_info = $soapclient->call("SellerRegAuth",$params = array("status"=> $status, "co_company_id"=> $co_company_id),	$options);
	$return_info = (array)$return_info;
	print_r($return_info);

	if($return_info[total_cnt] == $return_info[true_cnt]){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert(' ".$return_info[message]." 회원가입 승인이  정상적으로 처리 되었습니다.');parent.document.location.reload();</script>");
	}else if($return_info[total_cnt] == $return_info[false_cnt]){
		echo("<script>alert('이미 회원 승인된 사이트 입니다.');</script>");
	}else{
		echo("<script>alert('".$return_info[total_cnt]."개 업체중 ".$return_info[true_cnt]." 개 업체가 정상적으로 승인되었습니다. ".$return_info[false_cnt]." 개 업체는 이미 승인된 업체입니다.');</script>");
	}
}

if ($act == "sellershop_apply"){	
	
	$soapclient = new SOAP_Client("http://".$this_hostserver."/admin/cogoods/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	
	$return_info = $soapclient->call("SellerShopApply",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $co_company_id),	$options);
	$return_info = (array)$return_info;
	if($return_info[total_cnt] == $return_info[true_cnt]){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('입점업체 신청이  정상적으로 처리 되었습니다.');parent.document.location.reload();</script>");
	}else if($return_info[total_cnt] == $return_info[false_cnt]){
		echo("<script>alert('이미 신청된 입점업체 입니다.');</script>");
	}else{
		echo("<script>alert('".$return_info[total_cnt]."개 업체중 ".$return_info[true_cnt]." 개 업체가 정상적으로 등록되었습니다. ".$return_info[false_cnt]." 개 업체는 이미 등록중인 업체입니다.');</script>");
	}
}

if ($act == "sellershop_approval"){	
	
	$soapclient = new SOAP_Client("http://".$this_hostserver."/admin/cogoods/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	//echo ($co_company_id);
	//print_r($co_company_id);
	$soapclient->call("setHostServer",$params = array("hostserver_url"=> $this_hostserver),	$options);
	$ret = $soapclient->call("SellerShopApproval",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $co_company_id),	$options);
	$ret = (array)$ret;
	//print_r($ret);
	//echo "ret:".$ret;
	//print_r($ret);
	//exit;
	if($ret[bool]){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('".$ret[message]."');parent.document.location.reload();</script>");
	}else{
		echo("<script>alert('".$ret[message]."');</script>");
	}
}


if ($act == "sellershop_delete"){	
	
	$soapclient = new SOAP_Client("http://".$this_hostserver."/admin/cogoods/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	
	$ret = $soapclient->call("SellerShopDelete",$params = array("company_id"=> $admininfo[company_id]),	$options);
	$ret = (array)$ret;
	//print_r($ret);
	if($ret[bool]){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('".$ret[message]."');parent.document.location.reload();</script>");	
	}else{
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('".$ret[message]."');</script>");	
	}
}

if ($act == "sellershop_cancel"){	
	$soapclient = new SOAP_Client("http://".$this_hostserver."/admin/cogoods/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	//echo ($co_company_id);
	//print_r($co_company_id);
	$ret = $soapclient->call("SellerShopCancel",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $co_company_id),	$options);
	$ret = (array)$ret;
	//print_r($ret);
	//echo "ret:".$ret;
	//exit;
	//print_r($ret);
	if($ret["bool"]){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('".$ret["message"]."');parent.document.location.reload();</script>");
	}else{
		echo("<script>alert('".$ret["message"]."');</script>");
	}
		
}

if ($act == "sellershop_sellercancel"){	
	$soapclient = new SOAP_Client("http://".$this_hostserver."/admin/cogoods/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	//echo ($co_company_id);
	//print_r($co_company_id);
	$ret = $soapclient->call("SellerShopSellerCancel",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $co_company_id),	$options);
	$ret = (array)$ret;
	print_r($ret);
	//echo "ret:".$ret;
	//exit;
	//print_r($ret);
	if($ret["bool"]){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('".$ret["message"]."');parent.document.location.reload();</script>");
	}else{
		echo("<script>alert('".$ret["message"]."');</script>");
	}
		
}
/*
create table co_sellershop_apply (
company_id varchar(32) not null,
co_company_id varchar(32) not null,
apply_status enum('AP','AU') default 'AP',
regdate datetime not null,
primary key(company_id))




*/
?>
