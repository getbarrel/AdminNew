<?
include("../class/layout.class");

$db = new Database;

/*
echo $year."<br>";
echo $quarter."<br>";
echo $tax_file_name."<br>";
echo $income_bill_name."<br>";
echo $contents."<br>";
*/

if($act == 'insert'){
	
	$quarter = $year."/".$quarter;

	$tax_file_name = str_replace(";","",$tax_file_name);
	$tax_file_name = str_replace(" ","",$tax_file_name);
	
	$income_bill_name = str_replace(";","",$income_bill_name);
	$income_bill_name = str_replace(" ","",$income_bill_name);

	$sql = "INSERT INTO tax_receipt_input (re_ix,user_code,company_id, company_name, quarter, tax_file, income_bill, contents, regdate) VALUES ('','$code','$company_id','$company_name','$quarter','$tax_file_name','$income_bill_name','$contents',NOW()) ";
	
	$db->query($sql);
	
	
	$sql = "select re_ix from tax_receipt_input where re_ix = LAST_INSERT_ID()";
	$db->query($sql);
	$db->fetch();
	$re_ix = $db->dt[re_ix];

	//echo $_FILES['cooper_file']['tmp_name'];
	if($tax_file_size > 0 || $income_bill_size > 0) {
		//$path = $_SERVER["DOCUMENT_ROOT"]."/data/basic/images/cooperation/";
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config["mall_data_root"]."/images/tax_receipt_input/";

		if(is_writable($path)) {
			mkdir($path.$re_ix,0777,true);
			chmod($path.$re_ix,0777);
		}
		move_uploaded_file($tax_file, $path.$re_ix."/".$tax_file_name);
		move_uploaded_file($income_bill, $path.$re_ix."/".$income_bill_name);
		//copy($cooper_file, $path.$re_ix."/".$cooper_file_name);
	}
		echo "<script>alert('기장자료가 정상적으로 등록되었습니다.');top.window.close();</script>";
	
	exit;
}
if($act == 'update'){
	$sql="update tax_receipt set receipt_div = '$receipt_div' , status = '$status' where re_ix= '$re_ix'";
	$db->query($sql);
	
	//echo "<script>alert('증빙자료가 정상적으로 수정되었습니다.');top.window.close();</script>";opener.location.reload()
	echo "<script>alert('증빙자료가 정상적으로 수정되었습니다.');opener.location.reload();top.window.close();</script>";
	exit;
}

?>