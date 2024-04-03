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
	
	
	if($admininfo[company_id] == ""){
		echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='../'</script>";
		exit;	
	}
	
	$db = new Database;
	//$db2 = new Database;
	
	if ($act == "excel_input" || true){
		
		if ($excel_file_size > 0){	
			copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/dictionnary_pack_indonesian_20111011_2.xls");
		}
		
		syslog(LOG_INFO, 'GET XL START');
		$objPHPExcel = PHPExcel_IOFactory::load("../_language/dic_indonesian_20111017_edit.xls");
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
		while (($objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue() != "") && ($i < 11000)) {
			syslog(LOG_INFO, "WHILE INTERNAL LOOP BEGIN" . "\n");
			//////////////////////////// 데이터를 가져옴 //////////////////////////////////
			//echo "aaa";
			//exit;
			if($objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue() != ""){
				$dic_ix = $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue();
				$dic_type = $objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue();
				$dic_code = $objPHPExcel->getActiveSheet()->getCell('C' . $rownum)->getValue();
				$menu_div = $objPHPExcel->getActiveSheet()->getCell('D' . $rownum)->getValue();
				$menu_code = $objPHPExcel->getActiveSheet()->getCell('E' . $rownum)->getValue();
				$language_type = $objPHPExcel->getActiveSheet()->getCell('F' . $rownum)->getValue();
				$text_korea = $objPHPExcel->getActiveSheet()->getCell('G' . $rownum)->getValue();
				$text_trans = $objPHPExcel->getActiveSheet()->getCell('I' . $rownum)->getValue();
				//$desc_trans = $objPHPExcel->getActiveSheet()->getCell('J' . $rownum)->getValue();
				$desc_trans = $objPHPExcel->getActiveSheet()->getCell('K' . $rownum)->getValue();
				//echo "dic_ix : ".$dic_ix;
				//exit;
				if($dic_type == "DESC"){

					$sql = "select * from admin_dic 
							where dic_type = '$dic_type' and menu_div = '$menu_div' and menu_code = '$menu_code' and language_type = 'indonesian' 
							and dic_code = '$dic_code' ";

					$db->query($sql);
					$db->fetch();

					
					if(!$db->total){
						$desc_trans = str_replace("'","\'",$desc_trans);
						$sql = "insert into admin_dic
								(dic_ix,dic_type,dic_code,menu_div,menu_code,language_type,text_korea,text_trans,desc_trans,disp,regdate) 
								values
								('','$dic_type','$dic_code','$menu_div','$menu_code','indonesian','','','$desc_trans','1',NOW()) ";
						
					//	syslog(LOG_INFO, "INSERT BEGIN" . "\n");
					//	syslog(LOG_INFO, $sql . "\n");
					//	syslog(LOG_INFO, "INSERT END" . "\n");
					//	$db->query($sql);
						echo $sql."<br>";
						//exit;
					}else{
						$dic_ix = $db->dt[dic_ix];
						$desc_trans = str_replace("\\","\\\\",$desc_trans);
						//$desc_trans = str_replace("'","\'",$desc_trans);
						$desc_trans = str_replace("\\","\\\\",$desc_trans);
						$desc_trans = str_replace("\"","\\\"",$desc_trans);
						//$desc_trans = str_replace("'","\'",$desc_trans);

						$sql = "update admin_dic set desc_trans = \"".$desc_trans."\"  where dic_ix = '$dic_ix' and language_type = '$language_type' ";
						echo $sql."<br><br>\n\n\n\n";
						//$db->query($sql);
					}
				}else if($dic_type == "WORD")
				{
					
					//syslog(LOG_INFO, "Condition DIC_TYPE BEGIN" . "\n");
					
					
					$sql = "select text_korea,text_trans from admin_dic 
							where dic_ix = '$dic_ix' and language_type = '$language_type' ";
					$db->query($sql);
					$db->fetch();
					
					
					//syslog(LOG_INFO, "dt.text_trans : " . $db->dt[text_trans] . "   text_trans : " . $text_trans . "\n");
					
					
					if(trim($db->dt[text_trans]) != trim($text_trans) && $db->total && $dic_ix)
					{
						$text_korea = str_replace("\\","\\\\",$text_korea);
						$text_korea = str_replace("'","\'",$text_korea);
						$text_trans = str_replace("\\","\\\\",$text_trans);
						$text_trans = str_replace("'","\'",$text_trans);
						
						
						$sql = "update admin_dic set text_trans = '".$text_trans."'  where dic_ix = '$dic_ix' and language_type = '$language_type' ";
						//echo $sql."<br><br>";
						//$db->query($sql);
					//echo $dic_ix.":::".$db->dt[text_korea].":::".$db->dt[text_trans].":::".$text_trans."<br>\n";
					} else{
						$text_korea = str_replace("\\","\\\\",$text_korea);
						$text_korea = str_replace("'","\'",$text_korea);
						$text_trans = str_replace("\\","\\\\",$text_trans);
						$text_trans = str_replace("'","\'",$text_trans);
						
						$sql = "insert into admin_dic
								(dic_ix,dic_type,dic_code,menu_div,menu_code,language_type,text_korea,text_trans,desc_trans,eng_dic_ix,disp,regdate) 
								values
								('','$dic_type','','$menu_div','$menu_code','indonesian','".$text_korea."','".$text_trans."','','".$dic_ix."','1',NOW()) ";
						
						//echo nl2br($sql)."<br><br>";
						//$db->query($sql);
						//$db->fetch();

					}
					
					//syslog(LOG_INFO, "Condition DIC_TYPE END" . "\n");
				}
				
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
		echo "<script>alert('인도네시아번역 정보 일괄등록이 완료되었습니다.[$rownum]');</script>";
		
	
	}	
syslog(LOG_INFO, 'END');
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
	

