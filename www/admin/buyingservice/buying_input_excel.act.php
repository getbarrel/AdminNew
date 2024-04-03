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
		
		$sql = "insert into buyingservice_apply_info set 
					buying_mem_name='".$buying_mem_name."',
					mem_ix='".$mem_ix."',
					apply_date='".$apply_date."',
					buying_status='".$buying_status."',
					regdate= NOW()
					";
		//$db->debug = true;			
		$db->query($sql);
		$db->query("SELECT bai_ix FROM buyingservice_apply_info WHERE bai_ix=LAST_INSERT_ID()");
		$db->fetch();
		$bai_ix = $db->dt[bai_ix];	

		while (($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != "") && ($i < 11000)) {
			//////////////////////////// 데이터를 가져옴 //////////////////////////////////
			$oid = $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue();
			$paper_name = $objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue();
			$color = $objPHPExcel->getActiveSheet()->getCell('C' . $rownum)->getValue();
			$size = $objPHPExcel->getActiveSheet()->getCell('D' . $rownum)->getValue();
			$amount = $objPHPExcel->getActiveSheet()->getCell('E' . $rownum)->getValue();
			$buying_price = $objPHPExcel->getActiveSheet()->getCell('F' . $rownum)->getValue();
			$pre_payment_price = $objPHPExcel->getActiveSheet()->getCell('G' . $rownum)->getValue();
			$exchange_yn_text = $objPHPExcel->getActiveSheet()->getCell('H' . $rownum)->getValue();

			if($oid != "" && $delivery_company != "" && $invoice_no != "" && $company_id != ""){
				if($exchange_yn_text == "반품교환"){
					$exchange_yn == "Y" ;
				}else{
					$exchange_yn == "N" ;
				}

				$sql = "insert into buyingservice_apply_info_detail set 
							bai_ix='".$bai_ix."',
							ws_ix='".$buying_infos[$i][ws_ix]."',
							paper_name='".$paper_name."',
							color='".$color."',
							size='".$size."',
							amount='".$amount."',
							buying_complete_cnt='0',
							soldout_cancel_cnt='0',
							incom_ready_cnt='0',
							buying_price='".$buying_price."',
							pre_payment_price='".$pre_payment_price."',
							exchange_yn='".($exchange_yn)."',
							regdate=NOW()
							";
				//$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,dp_title,dp_desc,dp_use, regdate) values('','".$buying_infos[$i]["dp_title"]."','".$buying_infos[$i]["dp_desc"]."','".$dp_use."',NOW()) ";

				$db->query($sql);
				
			
				if($rownum > 500){
					exit;
				}
			}
			/////////////////////////////////////////////////////////////////////////////////
			$rownum++;
		}
		
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('일괄 송장입력 처리가 정상적으로 처리되었습니다');parent.document.location.reload();</script>");
		//echo "<script>alert('카테고리 일괄등록이 완료되었습니다.');</script>";
		
	
	}	
		
function getMaxlevel($cid,$depth)
{
	global $db;

	$strdepth = $depth + 1;

	$sPos = $depth*3;
	$sql = "select IFNULL(max(vlevel$strdepth),0)+1 as maxlevel from shop_category_info where cid LIKE '".substr($cid,0,$sPos)."%'";
//echo $sql."<br>";
	$db->query($sql);
	if($strdepth != 1){
		$db->fetch(0);
		return $db->dt["maxlevel"];
	}else{
		
		return "0";
	}

}	
	

function getNextCid($cid,$depth)
{
	global $db;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$sPosA = $depth*3;
	$sPos = $depth*3 + 1;
	$sql = "select max(substring(cid,$sPos,3))+1 as maxid from shop_category_info where cid LIKE '".substr($cid,0,$sPos-1)."%'";
	
	$db->query($sql);
	$db->fetch(0);

/*	
	echo $sql."<br>";
	echo $db->dt["maxid"]."<br>";
*/	
	if ($depth + 1 == 1){
		$cid1 = setFourChar($db->dt[0]);	
	}else if ($depth + 1 == 2){
		$cid2 = setFourChar($db->dt[0]);
	}else if ($depth + 1 == 3){
		$cid3 = setFourChar($db->dt[0]);
	}else if ($depth + 1 == 4){
		$cid4 = setFourChar($db->dt[0]);
	}else if ($depth + 1 == 5){
		$cid5 = setFourChar($db->dt[0]);
	}
	
	
	
	return "$cid1$cid2$cid3$cid4$cid5";

}


function setFourChar($cid_part)
{
	$chrlen = strlen($cid_part);
	
	$strCid = "$cid_part";
	for($i=0; $i < 3 - $chrlen ; $i++){
		$strCid = "0".$strCid;
	}
	
	return $strCid;
}


