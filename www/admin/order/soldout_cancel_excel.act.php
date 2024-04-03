<?php
	include("../class/layout.class");
	include("../lib/imageResize.lib.php");
	include '../include/phpexcel/Classes/PHPExcel.php';

	//error_reporting(E_ALL);
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');





	$removestring = array("\n", "\t");


	if($admininfo[company_id] == ""){
		echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='../'</script>";
		exit;
	}

	$db = new Database;
	//$db2 = new Database;

	if ($act == "excel_input"){
		//echo $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/upfile/".$excel_file_name;
		if ($excel_file_size > 0){
			copy($excel_file, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
		}

		$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);

		$shift_num = 0;
		$rownum = 2;


		$columnVar = $objPHPExcel->setActiveSheetIndex(0)->getCell('A' . ($rownum))->getValue();



		while (($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != "") && ($i < 11000)) {
			//////////////////////////// 데이터를 가져옴 //////////////////////////////////
			$oid = $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue();
			$pid = $objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue();

			if($oid != "" && $pid != "" ){

				$sql="select oid, od_ix, pid, status from ".TBL_SHOP_ORDER_DETAIL." where oid='".$oid."' and pid ='".$pid."' and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."') "; //and
				$db->query($sql);

				$order_details = $db->fetchall();
				for($i=0;$i < count($order_details);$i++){

					$pid = $order_details[$i][pid];
					$status = $order_details[$i][status];

					$sql="update ".TBL_SHOP_ORDER_DETAIL." set
						status = '".ORDER_STATUS_SOLDOUT_CANCEL."'
						where od_ix='".$order_details[$i][od_ix]."' ";

					$db->query($sql);
					//echo nl2br($sql)."<br><br>";
					//exit;
					$sql = "insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, pid, status, status_message, company_id,quick,invoice_no, regdate )
							values
							('','".$order_details[$i][oid]."','".$order_details[$i][pid]."','".ORDER_STATUS_SOLDOUT_CANCEL."','일괄품절취소 처리 완료','".$admininfo[company_id]."','','',NOW())";
					//echo nl2br($sql)."<br>";
					$db->sequences = "SHOP_ORDER_STATUS_SEQ";
					$db->query($sql);

				}

				if($rownum > 500){
					exit;
				}
			}
			/////////////////////////////////////////////////////////////////////////////////
			$rownum++;
		}

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('일괄 일괄품절취소 처리가 정상적으로 처리되었습니다');parent.document.location.reload();</script>");
		//echo "<script>alert('카테고리 일괄등록이 완료되었습니다.');</script>";
	}
