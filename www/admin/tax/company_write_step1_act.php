<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;
	
	$from = $_POST[from];
	$s_type = $_POST[s_type];
	$no1 = $_POST[no1];
	$no2 = $_POST[no2];
	$no3 = $_POST[no3];

	if($s_type == "1")	$company_number = $no1."-".$no2."-".$no3;
	else				$company_number = $jumin1."-".$jumin2;
	
	$db->query("select idx from tax_company_info where company_number = '$company_number'");
	$db->fetch();
	$idx = $db->dt[idx];

	if($idx != "")
	{
		echo "<script>alert ('등록되어있는 거래처입니다.');</script>";
	}
	else
	{
		echo "<script>parent.location.href='./company_write_step2.php?s_type=$s_type&company_number=$company_number&from=$from';</script>";
	}
?>