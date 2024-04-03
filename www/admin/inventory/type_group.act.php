<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("inventory.lib.php");
//include('../../include/xmlWriter.php');
$db = new Database;

session_start();

/*
if ($disp != 1){
	$disp = 0;shop_brand
}
*/
if ($mode == 'insert')
{
	$type_div = implode($type_div,"|");
	$sql = "INSERT INTO inventory_type (dt_ix, type, type_div, type_name, type_code, disp,is_basic,regdate) values('', '$type','$type_div', '$type_name', '$type_code','$disp','0',now()) ";
	$db->sequences = "INVENTORY_TYPE_SEQ";
	$db->query($sql);

	if($mmode == "pop"){
	echo "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='pragma' content='no-cache'>
		<body>
		<div id='invencode_select_area_".$type."'>
		".selectDeliveryType('',$select_name,$type)."
		</div>
		</body>
		</html>
		<Script Language='Javascript'>
		parent.opener.document.getElementById('brand_select_area').innerHTML = document.getElementById('brand_select_area').innerHTML;
		parent.document.location.reload();
		</Script>";
	}else{
		echo "
		<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
		//header("Location:brand.php?mmode=$mmode");
	}
}

if ($mode == "update")
{
	$type_div = implode($type_div,"|");
	$sql = "UPDATE inventory_type SET
			type_div = '$type_div',
			type_name = '$type_name',
			type_code = '$type_code',
			disp = '$disp'
			WHERE dt_ix='$dt_ix' " ;
	$db->query($sql);

	if($mmode == "pop"){
	echo "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='pragma' content='no-cache'>
		<body>
		<div id='invencode_select_area_".$type."'>
		".selectDeliveryType('',$select_name,$type)."
		</div>
		</body>
		</html>
		<Script Language='Javascript'>
		parent.opener.document.getElementById('brand_select_area').innerHTML = document.getElementById('brand_select_area').innerHTML;
		parent.document.location.reload();
		</Script>";
	}else{
		echo "
		<script language='javascript' src='../js/message.js.php'></script><Script Language='Javascript'>
		show_alert('정상적으로 수정되었습니다.');
		parent.document.location.reload();
		</Script>";
		//header("Location:brand.php?mmode=$mmode");
	}
}

if ($mode == "delete")
{


	$db->query("DELETE FROM inventory_type WHERE dt_ix='$dt_ix'");

	if($mmode == "pop"){
	echo "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='pragma' content='no-cache'>
		<body>
		<div id='invencode_select_area_".$type."'>
		".selectDeliveryType('',$select_name,$type)."
		</div>
		</body>
		</html>
		<Script Language='Javascript'>
		parent.opener.document.getElementById('brand_select_area').innerHTML = document.getElementById('brand_select_area').innerHTML;
		parent.document.location.reload();
		</Script>";
	}else{
		echo "
		<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
	}
}

?>