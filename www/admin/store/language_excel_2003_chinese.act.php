<?php
	include("../../class/database.class");
	include("../lib/imageResize.lib.php");
	include '../include/phpexcel/Classes/PHPExcel.php';
	
	
	define_syslog_variables();
openlog("phplog", LOG_PID , LOG_LOCAL0);

	
	
	
	
	
	
	
	
	//ini_set('memory_list','1000M');
	//error_reporting(E_ALL);
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	
	date_default_timezone_set('Asia/Seoul');

	syslog(LOG_INFO, 'BEGIN');
	
	session_start();
	
	
	$removestring = array("\n", "\t");
	
	//language_excel_2003_indonesian.act.php
	if($admininfo[company_id] == ""){
		echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='../'</script>";
		exit;	
	}
	
	$db = new Database;
	//$db2 = new Database;
	
	if ($act == "excel_input" || true){
		
		if ($excel_file_size > 0){	
			//copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/laguage_pack_indonesian_20111011.xls");
		}
		
		syslog(LOG_INFO, 'GET XL START');
		//$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/laguage_pack_indonesian_20111011.xls");
		$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."/admin/_language/laguage_pack_chinese_20111228.xls");
		syslog(LOG_INFO, 'GET XL END');
		$shift_num = 0;
		
		
		
		// 데이터는 2줄부터 시작
		
		$rownum = 2;

		
		$columnVar = $objPHPExcel->setActiveSheetIndex(0)->getCell('A' . ($rownum))->getValue();
		
		SYSLOG(LOG_INFO, "columnVar : " . $columnVar . "\n");
		
		//echo ($objPHPExcel->getActiveSheet()->getCell('G' . $rownum)->getValue());
		//print_r ($objPHPExcel->getActiveSheet()->getCell('G' . $rownum)->getValue());
		//exit;
		
		//SYSLOG(LOG_INFO, "(($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != '') && ($i < 11000))\n");
		
		//SYSLOG(LOG_INFO, "(($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != '') && ($i < 11000))\n");
		while (($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != "") && ($i < 11000)) {
			syslog(LOG_INFO, "WHILE INTERNAL LOOP BEGIN" . "\n");
			//////////////////////////// 데이터를 가져옴 //////////////////////////////////
			//echo "aaa";
			//exit;
			if($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != ""){
				$language_ix = $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue();
				$text_div = $objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue();
				$language_type = $objPHPExcel->getActiveSheet()->getCell('C' . $rownum)->getValue();
				$text_name = $objPHPExcel->getActiveSheet()->getCell('D' . $rownum)->getValue();
				$text_korea = $objPHPExcel->getActiveSheet()->getCell('E' . $rownum)->getValue();
				$text_trans = $objPHPExcel->getActiveSheet()->getCell('G' . $rownum)->getValue(); // 일본어 필드
				$disp = 1;//$objPHPExcel->getActiveSheet()->getCell('I' . $rownum)->getValue();
				//$regdate = $objPHPExcel->getActiveSheet()->getCell('I' . $rownum)->getValue();
				//echo "language_ix : ".$language_ix;
				//exit;
				$text_name = $text_korea;
					
					//syslog(LOG_INFO, "Condition DIC_TYPE BEGIN" . "\n");
					
					
					$sql = "select language_ix, text_korea,text_trans from admin_language 
							where text_korea = '$text_korea' and language_type = '$language_type' and text_div = '$text_div' ";
					$db->query($sql);
					$db->fetch();
					
					
					//syslog(LOG_INFO, "dt.text_trans : " . $db->dt[text_trans] . "   text_trans : " . $text_trans . "\n");
					
					
					if($db->total && $language_ix)
					{
						$text_korea = str_replace("\\","\\\\",$text_korea);
						$text_korea = str_replace("'","\'",$text_korea);
						$text_trans = str_replace("\\","\\\\",$text_trans);
						$text_trans = str_replace("'","\'",$text_trans);
						
						
						$sql = "update admin_language set text_trans = '".$text_trans."'  where language_ix = '$language_ix' and language_type = '$language_type' ";
						//echo $sql."<br><br>";
						//$db->query($sql);
					//echo $language_ix.":::".$db->dt[text_korea].":::".$db->dt[text_trans].":::".$text_trans."<br>\n";
					} else{
						$text_korea = str_replace("\\","\\\\",$text_korea);
						$text_korea = str_replace("'","\'",$text_korea);
						$text_trans = str_replace("\\","\\\\",$text_trans);
						$text_trans = str_replace("'","\'",$text_trans);
						
						if(trim($text_korea) != "" && trim($text_trans) != ""){
							$sql = "insert into admin_language
									(language_ix,text_div,language_type,text_name,text_korea,text_trans,disp,regdate)				
									values
									('','$text_div','$language_type','$text_name','$text_korea','$text_trans','$disp',NOW())";
							
							echo nl2br($sql)." ==> $language_ix <br><br>";
							//$db->query($sql);
						}
						//$db->fetch();

					}
					
					//syslog(LOG_INFO, "Condition DIC_TYPE END" . "\n");
				
				
				if($rownum > 40){
					//exit;
				}
			}
			//exit;
			/////////////////////////////////////////////////////////////////////////////////
			$rownum++;
			
			//syslog(LOG_INFO, "WHILE INTERNAL LOOP END" . "\n");
		}
		
		//exit;
		echo "<script>alert('중국어번역 정보 일괄등록이 완료되었습니다.[$rownum]');</script>";
		
	
	}	
//syslog(LOG_INFO, 'END');
closelog();
	
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
	

