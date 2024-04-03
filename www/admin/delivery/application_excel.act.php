<?
include("../../class/database.class");

session_start();

if($admininfo[admin_id] == ""){
	echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='../'</script>";
	exit;	
}

$db = new Database;
$db2 = new Database;




if ($act == "excel_input"){
	require_once 'Excel/reader.php';
	
	if ($excel_file_size > 0){	
		copy($excel_file, "./upfile/".$excel_file_name);
	}
	
	// ExcelFile($filename, $encoding);
	$data = new Spreadsheet_Excel_Reader();
	
	
	// Set output Encoding.
	$data->setOutputEncoding('CP949');
	$data->read("./upfile/".$excel_file_name);
	
	error_reporting(E_ALL ^ E_NOTICE);
	$shift_num = 0;
	
//	echo $data->sheets[0]['numRows'];
	//exit;
	for ($i = 3; $i <= $data->sheets[0]['numRows']; $i++) {		
		//print_r($data->sheets[0]['cells'][$i]);
		//exit;
			//$pcode = $data->sheets[0]['cells'][$i][1+$shift_num];
			$HAWB_NO = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][1+$shift_num]);
			$application_no = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][5+$shift_num]);
			//$brand_name = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][4+$shift_num]);
			
			$sql = "UPDATE delivery_info SET invoice_no = '$HAWB_NO' , status = 'IC' WHERE application_no='$application_no'";
			//echo $sql;
			//exit;
			$db->query($sql);
			
			
			
	}		
		
	
	
	
//	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name)){
//		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
//	}
	
	echo "<script>alert('택배 송장번호 등록이 완료되었습니다.');document.location.href='./application_list.php?status=IC'</script>";

}



?>