<?php

	include("../class/layout.class");
	include '../include/phpexcel/Classes/PHPExcel.php';
	//error_reporting(E_ALL);
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

if($type == "dic"){
	$db = new Database;
	$sub_db = new Database;
	$sql = "select * from admin_dic where language_type = 'korea' and dic_type = 'DESC' ";
	$db->query($sql);
	$dics = $db->fetchall();

	$productListXL = new PHPExcel();

	// 속성 정의
	$productListXL->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("Product List")
								 ->setSubject("Product List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("Product List");



	// 데이터 등록
	$productListXL->getActiveSheet(0)->setCellValue('A' . 1, "dic_ix");
	$productListXL->getActiveSheet(0)->setCellValue('B' . 1, "dic_type");
	$productListXL->getActiveSheet(0)->setCellValue('C' . 1, "dic_code");
	$productListXL->getActiveSheet(0)->setCellValue('D' . 1, "menu_div");
	$productListXL->getActiveSheet(0)->setCellValue('E' . 1, "menu_code");
	$productListXL->getActiveSheet(0)->setCellValue('F' . 1, "language_type");
	$productListXL->getActiveSheet(0)->setCellValue('G' . 1, "text_korea");
	$productListXL->getActiveSheet(0)->setCellValue('H' . 1, "text_english");
	$productListXL->getActiveSheet(0)->setCellValue('I' . 1, "text_indonesian");
	$productListXL->getActiveSheet(0)->setCellValue('J' . 1, "desc_english");
	$productListXL->getActiveSheet(0)->setCellValue('K' . 1, "desc_indonesian");
	$productListXL->getActiveSheet(0)->setCellValue('L' . 1, "eng_dic_ix");
	$productListXL->getActiveSheet(0)->setCellValue('M' . 1, "disp");
	$productListXL->getActiveSheet(0)->setCellValue('N' . 1, "regdate");
	/*
	$productListXL->getActiveSheet(0)->setCellValue('I' . 1, "공급가");
	$productListXL->getActiveSheet(0)->setCellValue('J' . 1, "정가");
	$productListXL->getActiveSheet(0)->setCellValue('K' . 1, "판매가(할인가)");
	$productListXL->getActiveSheet(0)->setCellValue('L' . 1, "수수료");
	$productListXL->getActiveSheet(0)->setCellValue('M' . 1, "배송선택");
	$productListXL->getActiveSheet(0)->setCellValue('N' . 1, "배송비");
	$productListXL->getActiveSheet(0)->setCellValue('O' . 1, "기본이미지");
	$productListXL->getActiveSheet(0)->setCellValue('P' . 1, "상세내용");
	*/
	
	//------ row 1 data ------
	for($i=0;$i<count($dics);$i++){
		$db->fetch($i);
		$text_korea = str_replace("\\","\\\\",$dics[$i][text_korea]);
		$text_korea = str_replace("'","\'",$text_korea);
		
		$sql = "select text_trans from admin_dic where language_type = 'indonesian' and text_korea = '".$text_korea."' ";
		
		$sub_db->query($sql);
		if($sub_db->total){
			$sub_db->fetch();
			$text_indonesian = $sub_db->dt[text_trans];
		}else{
			$text_indonesian = "";
		}

		$productListXL->getActiveSheet()->setCellValue('A' . ($i + 2), $dics[$i][dic_ix]);
		$productListXL->getActiveSheet()->setCellValue('B' . ($i + 2), $dics[$i][dic_type]);
		$productListXL->getActiveSheet()->setCellValue('C' . ($i + 2), $dics[$i][dic_code]);
		$productListXL->getActiveSheet()->setCellValue('D' . ($i + 2), $dics[$i][menu_div]);
		$productListXL->getActiveSheet()->setCellValue('E' . ($i + 2), $dics[$i][menu_code]);
		$productListXL->getActiveSheet()->setCellValue('F' . ($i + 2), $dics[$i][language_type]);
		$productListXL->getActiveSheet()->setCellValue('G' . ($i + 2), $dics[$i][text_korea]);
		$productListXL->getActiveSheet()->setCellValue('H' . ($i + 2), $dics[$i][text_trans]);
		$productListXL->getActiveSheet()->setCellValue('I' . ($i + 2), $text_indonesian);
		$productListXL->getActiveSheet()->setCellValue('J' . ($i + 2), $dics[$i][desc_trans]);
		$productListXL->getActiveSheet()->setCellValue('K' . ($i + 2), $desc_indonesian);
		$productListXL->getActiveSheet()->setCellValue('L' . ($i + 2), $dics[$i][eng_dic_ix]);
		$productListXL->getActiveSheet()->setCellValue('M' . ($i + 2), $dics[$i][disp]);
		$productListXL->getActiveSheet()->setCellValue('N' . ($i + 2), $dics[$i][regdate]);
		/*
		$productListXL->getActiveSheet()->setCellValue('I' . ($i + 2), $db->dt[coprice]);
		$productListXL->getActiveSheet()->setCellValue('J' . ($i + 2), $db->dt[listprice]);
		$productListXL->getActiveSheet()->setCellValue('K' . ($i + 2), $db->dt[sellprice]);
		$productListXL->getActiveSheet()->setCellValue('L' . ($i + 2), $db->dt[one_commission]);
		$productListXL->getActiveSheet()->setCellValue('M' . ($i + 2), $db->dt[delivery_type]);
		$productListXL->getActiveSheet()->setCellValue('N' . ($i + 2), $db->dt[delivery_price]);
		$productListXL->getActiveSheet()->setCellValue('O' . ($i + 2), "http://".$HTTP_HOST.$admin_config[mall_data_root]."/images/product/b_".$db->dt[id].".gif");
		$productListXL->getActiveSheet()->setCellValue('P' . ($i + 2), $db->dt[basicinfo]);
		*/

	}

	// 시트이름등록
	$productListXL->getActiveSheet()->setTitle('상품리스트');

	// 첫번째 시트 선택
	$productListXL->setActiveSheetIndex(0);
	$productListXL->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	/*
	$productListXL->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	*/

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="laguage_pack_indonesian_'.date("Ymd").'.xls"');
	header('Cache-Control: max-age=0');


	$objWriter = PHPExcel_IOFactory::createWriter($productListXL, 'Excel5');
	$objWriter->save('php://output');
}else{


	$db = new Database;
	$sub_db = new Database;
	$sql = "select * from admin_language where language_type = 'english' ";
	$db->query($sql);


	$productListXL = new PHPExcel();

	// 속성 정의
	$productListXL->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("Product List")
								 ->setSubject("Product List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("Product List");



	// 데이터 등록
	$productListXL->getActiveSheet(0)->setCellValue('A' . 1, "language_ix");
	$productListXL->getActiveSheet(0)->setCellValue('B' . 1, "text_div");
	$productListXL->getActiveSheet(0)->setCellValue('C' . 1, "language_type");
	$productListXL->getActiveSheet(0)->setCellValue('D' . 1, "text_name");
	$productListXL->getActiveSheet(0)->setCellValue('E' . 1, "text_korea");
	$productListXL->getActiveSheet(0)->setCellValue('F' . 1, "text_english");
	$productListXL->getActiveSheet(0)->setCellValue('G' . 1, "text_indonesian");
	$productListXL->getActiveSheet(0)->setCellValue('H' . 1, "disp");
	$productListXL->getActiveSheet(0)->setCellValue('I' . 1, "regdate");
	/*
	$productListXL->getActiveSheet(0)->setCellValue('I' . 1, "공급가");
	$productListXL->getActiveSheet(0)->setCellValue('J' . 1, "정가");
	$productListXL->getActiveSheet(0)->setCellValue('K' . 1, "판매가(할인가)");
	$productListXL->getActiveSheet(0)->setCellValue('L' . 1, "수수료");
	$productListXL->getActiveSheet(0)->setCellValue('M' . 1, "배송선택");
	$productListXL->getActiveSheet(0)->setCellValue('N' . 1, "배송비");
	$productListXL->getActiveSheet(0)->setCellValue('O' . 1, "기본이미지");
	$productListXL->getActiveSheet(0)->setCellValue('P' . 1, "상세내용");
	*/
	
	//------ row 1 data ------
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

	$sql = "select text_trans from admin_language where language_type = 'indonesian' and text_korea = '".$db->dt[text_korea]."' ";
	$sub_db->query($sql);
	if($sub_db->total){
		$sub_db->fetch();
		$text_indonesian = $sub_db->dt[text_trans];
	}else{
		$text_indonesian = "";
	}

		$productListXL->getActiveSheet()->setCellValue('A' . ($i + 2), $db->dt[language_ix]);
		$productListXL->getActiveSheet()->setCellValue('B' . ($i + 2), $db->dt[text_div]);
		$productListXL->getActiveSheet()->setCellValue('C' . ($i + 2), $db->dt[language_type]);
		$productListXL->getActiveSheet()->setCellValue('D' . ($i + 2), $db->dt[text_name]);
		$productListXL->getActiveSheet()->setCellValue('E' . ($i + 2), $db->dt[text_korea]);
		$productListXL->getActiveSheet()->setCellValue('F' . ($i + 2), $db->dt[text_trans]);
		$productListXL->getActiveSheet()->setCellValue('G' . ($i + 2), $text_indonesian);
		$productListXL->getActiveSheet()->setCellValue('H' . ($i + 2), $db->dt[disp]);
		$productListXL->getActiveSheet()->setCellValue('I' . ($i + 2), $db->dt[regdate]);
		/*
		$productListXL->getActiveSheet()->setCellValue('I' . ($i + 2), $db->dt[coprice]);
		$productListXL->getActiveSheet()->setCellValue('J' . ($i + 2), $db->dt[listprice]);
		$productListXL->getActiveSheet()->setCellValue('K' . ($i + 2), $db->dt[sellprice]);
		$productListXL->getActiveSheet()->setCellValue('L' . ($i + 2), $db->dt[one_commission]);
		$productListXL->getActiveSheet()->setCellValue('M' . ($i + 2), $db->dt[delivery_type]);
		$productListXL->getActiveSheet()->setCellValue('N' . ($i + 2), $db->dt[delivery_price]);
		$productListXL->getActiveSheet()->setCellValue('O' . ($i + 2), "http://".$HTTP_HOST.$admin_config[mall_data_root]."/images/product/b_".$db->dt[id].".gif");
		$productListXL->getActiveSheet()->setCellValue('P' . ($i + 2), $db->dt[basicinfo]);
		*/

	}

	// 시트이름등록
	$productListXL->getActiveSheet()->setTitle('상품리스트');

	// 첫번째 시트 선택
	$productListXL->setActiveSheetIndex(0);
	$productListXL->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	/*
	$productListXL->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	*/

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="laguage_pack_indonesian_'.date("Ymd").'.xls"');
	header('Cache-Control: max-age=0');


	$objWriter = PHPExcel_IOFactory::createWriter($productListXL, 'Excel5');
	$objWriter->save('php://output');
}
	exit;