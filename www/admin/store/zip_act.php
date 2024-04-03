<?
include("../../class/database.class");


session_start();

$db = new Database;
$db2 = new Database;


if($act == "zipcode" && $excel_file_name != ""){
	require_once 'Excel/reader.php';
	
	
	if ($excel_file_size > 0){	
		copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name,0777);
	}
	
	// ExcelFile($filename, $encoding);
	$data = new Spreadsheet_Excel_Reader();
	
	
	// Set output Encoding.
	$data->setOutputEncoding('utf-8');
	$data->read($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
	
	error_reporting(E_ALL ^ E_NOTICE);
	$shift_num = 0;
	
	$sql = "TRUNCATE TABLE shop_zip ";
	$db2->query($sql);
	
	for ($i = 3; $i <= $data->sheets[0]['numRows']; $i++) {		
			$zip_code = $data->sheets[0]['cells'][$i][1+$shift_num];
			$sido = $data->sheets[0]['cells'][$i][3+$shift_num];
			$sigugun = $data->sheets[0]['cells'][$i][4+$shift_num];
			$dong = $data->sheets[0]['cells'][$i][5+$shift_num];
			$ri = $data->sheets[0]['cells'][$i][6+$shift_num];
			$st_bunji = $data->sheets[0]['cells'][$i][9+$shift_num];		
			$ed_bunji = $data->sheets[0]['cells'][$i][11+$shift_num];		
			$building_name = $data->sheets[0]['cells'][$i][13+$shift_num];
			$dong_start = $data->sheets[0]['cells'][$i][14+$shift_num];
			$dong_end = $data->sheets[0]['cells'][$i][15+$shift_num];
			$change_date = $data->sheets[0]['cells'][$i][16+$shift_num];
			$address = $data->sheets[0]['cells'][$i][17+$shift_num];	
			
			$zip_code1 = substr($zip_code, 0,3);
			$zip_code2 = substr($zip_code, 3,3);
			$sql = "insert into shop_zip (ix,zip_code,sido,sigugun,dong,ri,st_bunji,ed_bunji,building_name,address) values ('".$i."','".$zip_code1."-".$zip_code2."','".$sido."','".$sigugun."','".$dong."','".$ri."','".$st_bunji."','".$ed_bunji."','".addslashes($building_name)."','".addslashes($address)."')";
			//echo $sql;
			//echo "<br>";
			$db->query($sql);
		
	}
	

	echo "<script>alert('완료되었습니다.');document.location.href='./mallinfo.php'</script>";

} else {
	echo "<script>alert('첨부파일을 다시 확인해주세요.');document.location.href='./mallinfo.php'</script>";
}
?>