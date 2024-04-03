<?php
	include("../../class/database.class");
	include("../lib/imageResize.lib.php");
	include '../include/phpexcel/Classes/PHPExcel.php';

	//error_reporting(E_ALL);
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	
	date_default_timezone_set('Asia/Seoul');

	
	session_start();
	
	
	$removestring = array("\n", "\t");
	//exit;

	if($admininfo[company_id] == ""){
		echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='../'</script>";
		//'관리자 로그인후 사용하실수 있습니다.'
		exit;	
	}
	
	$db = new Database;
	//$db2 = new Database;
	
	if ($act == "excel_input" || true){

		if ($excel_file_size > 0){	
			//copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/Aland_Category.xls");
			copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/dev.dcgworld.com_excel.xls");
		}
		
		//$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/Aland_Category.xls");
		$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/dev.dcgworld.com_excel.xls");
		
		$shift_num = 0;
		
		////////////////////////////////////////////////////////////////////////////////////////////////
		/*
		// 컬럼 찾기

		// 컬럼이름들
		$columns = array(
							pcode => "A",
							admin => "B",
							pname => "C",
							brand_name => "D",
							brand => "E",
							company => "F",
							make_country => "G",
							shotinfo => "H",
							search_keyword => "I",
							category_str => "J",
							basicinfo => "K",
							delivery_company => "L",
							delivery_price1 => "M",
							delivery_price2 => "N",
							delivery_product_policy => "O",
							option_name1 => "P",
							option_item1 => "Q",
							option_name2 => "R",
							option_item2 => "S",
							option_name3 => "T",
							option_item3 => "U",
							coprice => "V",
							listprice => "W",
							sellprice => "X",
							stock => "Y",
							safestock => "Z",
							surtax => "AA",
							reserve => "AB",
							reserve_rate => "AC"
						);
		
		$columns_flip = array_flip($columns); // 키와 값을 바꾼것
		
		
		//$i = 1;
		
		$XLColumns = array();
		
		
		foreach ($columns_flip as $item) // item 은 변수명 pcode, admin, pname 등
		{
			
			//$columnXCoordinate = "A";
			foreach ($columns as $columnXCoordinate) // item2 는 컬럼 A, B, C, D, E, F, G 등
			{
				$columnVar = $objPHPExcel->setActiveSheetIndex(0)->getCell($columnXCoordinate . "2");
				
				if (trim($item) == trim($columnVar->getValue())) {
					//error_log("columnXCoordinate=>item : " . $columnXCoordinate . " => " . $item);
					$XLColumns = array_merge($XLColumns, array($columnXCoordinate => $item));
					//error_log(print_r($XLColumns, true));
				}
				//$columnXCoordinate++;
			}
			
		}
		error_log("last! " . print_r($XLColumns, true));
		*/
		/*
	$objPHPExcel->getActiveSheet()->setCellValue('A10', print_r($XLColumns));	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Di			sposition: attachment;filename="test.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	*/
	

		/*
		$XLColumnsXCoordinates = array_flip($XLColumns);
		
		foreach ($XLColumnsXCoordinates as $XLColumnsXCoordinate)
		{
			
		}
		*/
		
		// 데이터는 2줄부터 시작
		
		$rownum = 1;
		

		$sql = "delete from shop_category_info ";
    	$db->query($sql);

		
		$columnVar = $objPHPExcel->setActiveSheetIndex(0)->getCell('A' . ($rownum))->getValue();
		
		
		$cid = "000000000000000";
		$depth = 0;
		$category_use = 1;
		$level1 = 0;
		$level2 = 0;
		$level3 = 0;
		$level4 = 0;
		$level5 = 0;

		while (($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != "") && ($i < 10)) {
			//////////////////////////// 데이터를 가져옴 //////////////////////////////////
			if($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != ""){

				$category_text[0] = $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue();
				$category_text[1] = $objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue();
				$category_text[2] = $objPHPExcel->getActiveSheet()->getCell('C' . $rownum)->getValue();
				$category_text[3] = $objPHPExcel->getActiveSheet()->getCell('D' . $rownum)->getValue();
                
                $category_text[0] = str_replace(array("\t","\r","\n"),"",trim($category_text[0]));
				$category_text[1] = str_replace(array("\t","\r","\n"),"",trim($category_text[1]));
				$category_text[2] = str_replace(array("\t","\r","\n"),"",trim($category_text[2]));
				$category_text[3] = str_replace(array("\t","\r","\n"),"",trim($category_text[3]));

				$category_text[0] = str_replace("'","&#39;",$category_text[0]);
				$category_text[1] = str_replace("'","&#39;",$category_text[1]);
				$category_text[2] = str_replace("'","&#39;",$category_text[2]);
				$category_text[3] = str_replace("'","&#39;",$category_text[3]);
                
                //$category_text[4] = $objPHPExcel->getActiveSheet()->getCell('E' . $rownum)->getValue();                                
				//echo $category_text[0].">".$category_text[1].">".$category_text[2].">".$category_text[3]."<br/>";
				//exit;
				//print_r($category_text);
                for($i=0;$i < count($category_text);$i++){
				    if(trim($category_text[$i]) != ""){
						

						if($i == 0){
							$sql = "select * from shop_category_info where cname = '".trim($category_text[$i])."' and depth = '".$i."' ";
						}else{
    						$sql = "select * from shop_category_info where cname = '".trim($category_text[$i])."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' and depth = '".$i."'";
						}
    					//echo $sql."<br>";
    					$db->query($sql);
                        
    					if(!$db->total){
    						$depth = $i;
    						$sub_cid = getNextCid($cid,$depth);
    						$sub_depth = $i;
    						$sub_category = $category_text[$i];
    						$cid = $sub_cid;
    
    						if ($sub_depth+1 == 1){
    							$level1 = getMaxlevel($cid,$sub_depth);						
    						}else if($sub_depth+1 ==2){
    							$level2 = getMaxlevel($cid,$sub_depth);
    						}else if($sub_depth+1 ==3){
    							$level3 = getMaxlevel($cid,$sub_depth);
    						}else if($sub_depth+1 ==4){
    							$level4 = getMaxlevel($cid,$sub_depth);
    						}else if($sub_depth+1 ==5){
    							$level5 = getMaxlevel($cid,$sub_depth);
    						}
    
    						if ($category_img_size > 0){
    							copy($category_img, "../../image/category/".$category_img_name);
    						}
    
    						if ($leftcategory_img_size > 0){
    							copy($leftcategory_img, "../../image/category/".$leftcategory_img_name);
    						}
    
    						$sql = "insert into shop_category_info (cid, depth,vlevel1, vlevel2, vlevel3,vlevel4,vlevel5,cname,category_code,catimg,leftcatimg, category_display_type, category_use,regdate) values ('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5', '$sub_category', '$sub_category','$category_img_name','$leftcategory_img_name','T','$category_use',NOW());";
    						echo $i." - ".$sql."<br>";
    
    						$db->query($sql);
    
    					}else{
    						$db->fetch();
    						$cid = $db->dt["cid"];
    						$depth = $db->dt["depth"];
    						$level1 = $db->dt["vlevel1"];
    						$level2 = $db->dt["vlevel2"];
    						$level3 = $db->dt["vlevel3"];
    						$level4 = $db->dt["vlevel4"];
    						$level5 = $db->dt["vlevel5"];
    					}
                    }
				}
				$depth = 0;
				$cid = "000000000000000";
				$level1 = 0;
				$level2 = 0;
				$level3 = 0;
				$level4 = 0;
				$level5 = 0;
				//exit;
			
				if($rownum > 10000){//410){
					exit;
				}
			}
			/////////////////////////////////////////////////////////////////////////////////
			$rownum++;
		}
		
		
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('카테고리 일괄등록이 완료되었습니다.');</script>");
		
	
	}	
		
function getMaxlevel($cid,$depth)
{
	global $db;

	$strdepth = $depth + 1;

	$sPos = $depth*3;
	$sql = "select IFNULL(max(vlevel$strdepth),0)+1 as maxlevel from shop_category_info where cid LIKE '".substr($cid,0,$sPos)."%'";
    echo $sql."<br>";
	$db->query($sql);
	/*
	if($strdepth != 1){
		$db->fetch(0);
		return $db->dt["maxlevel"];
	}else{
		
		return "0";
	}
	*/
	
	$db->fetch(0);
	return $db->dt["maxlevel"];
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
	$sql = "select max(substring(cid,$sPos,3))+1 as maxid from shop_category_info where cid LIKE '".substr($cid,0,$sPos)."%'";
	echo $sql."<br><br>";
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


