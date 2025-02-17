<?
if($mode == "excel"){	//엑셀다운로드
	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$memberXL = new PHPExcel();

		// 속성 정의
	$memberXL->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("Point List")
								 ->setSubject("Point List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("Point List");

	// 데이터 등록
	$memberXL->getActiveSheet(0)->setCellValue('A' . 1, "신청일");
	$memberXL->getActiveSheet(0)->setCellValue('B' . 1, "변경일");
	$memberXL->getActiveSheet(0)->setCellValue('C' . 1, "처리상태");
	$memberXL->getActiveSheet(0)->setCellValue('D' . 1, "입/출금 금액");
	$memberXL->getActiveSheet(0)->setCellValue('E' . 1, "내역");
	$memberXL->getActiveSheet(0)->setCellValue('F' . 1, "회원명");
	$memberXL->getActiveSheet(0)->setCellValue('G' . 1, "아이디");
	
	
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		switch($db->dt[history_type]){
			case '1':
				$use_type = '입금대기';
				$change_date = "";
				break;
			case '2':
				$use_type = '입금취소';
				$change_date = $db->dt[cc_date];
				break;
			case '3':
				$use_type = '입금완료';
				$change_date = $db->dt[ic_date];
				break;
			case '5':
				$use_type = '출금요청';
				break;
			case '6':
				$use_type = '출금취소';
				$change_date = $db->dt[change_date];
				break;
			case '7':
				$use_type = '출금확정';
				$change_date = $db->dt[change_date];
				break;
			case '8':
				$use_type = '송금확정';
				$change_date = $db->dt[change_date];
				break;
		}

		$memberXL->getActiveSheet()->setCellValue('A' . ($i + 2), $db->dt[regdate]);
		$memberXL->getActiveSheet()->setCellValue('B' . ($i + 2), $change_date);
		$memberXL->getActiveSheet()->setCellValue('C' . ($i + 2), $use_type);
		$memberXL->getActiveSheet()->setCellValue('D' . ($i + 2), $db->dt[deposit]);
		$memberXL->getActiveSheet()->setCellValue('E' . ($i + 2), $db->dt[etc]);
		$memberXL->getActiveSheet()->setCellValue('F' . ($i + 2), get_member_name($db->dt[uid]));
		$memberXL->getActiveSheet()->setCellValue('G' . ($i + 2), get_member_id($db->dt[uid]));
	}

	$memberXL->getActiveSheet()->setTitle('예치금 내역');

	// 첫번째 시트 선택
	$memberXL->setActiveSheetIndex(0);
	$memberXL->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="deposit_list.xls"');
	header('Cache-Control: max-age=0');


	$objWriter = PHPExcel_IOFactory::createWriter($memberXL, 'Excel5');
	$objWriter->save('php://output');

	exit;
};
?>