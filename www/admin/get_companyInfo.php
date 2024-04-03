<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

session_start();

$db = new Database;



if ($act == "get"){

	$sql = "select * from common_company_detail
					where com_name='".$com_name."' ";

	//echo $sql;
	$db->query($sql);
	$db->fetch();

	echo $db->dt[company_id];

}


?>