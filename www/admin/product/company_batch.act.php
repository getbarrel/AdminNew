<?php
	include("../../class/database.class");
	include("../lib/imageResize.lib.php");
	include '../include/phpexcel/Classes/PHPExcel.php';
	include('../../include/lib.function.php');
	include('../../include/global_util.php');
	session_start();

	//error_reporting(E_ALL);
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');
	session_start();
	$removestring = array("\n", "\t");


	if($admininfo[company_id] == ""){
		echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='../'</script>";
		//'관리자 로그인후 사용하실수 있습니다.'
		exit;
	}

	$db = new Database;
	$db2 = new Database;
	$image_db = new Database;

	if ($act == "excel_input"){

		if ($excel_file_size > 0){
			copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
		}

		$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
		$shift_num = 0;

		//데이터는 2줄부터 시작, 1 줄은 제목+코드명
		$rownum = 2;
		$columnVar = $objPHPExcel->setActiveSheetIndex(0)->getCell('B' . ($rownum))->getValue();
		//$sourceDirectory = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/BrandBatchUploadImages/";

		while (($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != "") && ($i < 11000)) {
			//////////////////////////// 데이터를 가져옴 //////////////////////////////////

			$company_name = $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue();				//제조사명
			$cp_code = $objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue();				//제조사코드
			$disp = $objPHPExcel->getActiveSheet()->getCell('C' . $rownum)->getValue();						//사용유무
			$status = $objPHPExcel->getActiveSheet()->getCell('D' . $rownum)->getValue();				//신청상태
			$cid_array = $objPHPExcel->getActiveSheet()->getCell('E' . $rownum)->getValue();				//상품카테고리 배열
			$cd_ix = $objPHPExcel->getActiveSheet()->getCell('F' . $rownum)->getValue();					//제조사분류
			$cp_shotinfo = $objPHPExcel->getActiveSheet()->getCell('G' . $rownum)->getValue();				//간략설명
			$zipcode = $objPHPExcel->getActiveSheet()->getCell('H' . $rownum)->getValue();				//우편코드
			$addr1 = $objPHPExcel->getActiveSheet()->getCell('I' . $rownum)->getValue();					//주소1
			$addr2 = $objPHPExcel->getActiveSheet()->getCell('J' . $rownum)->getValue();					//주소1

			//상품명이 있는경우 db입력 진행.
			if(!empty($company_name) && !empty($cp_code) && !empty($cd_ix) && !empty($disp) && !empty($status)){
				$company_name = trim($company_name);
				$cp_code = trim($cp_code);
				$disp = trim($disp);
				$status = trim($status);
				$cid_unit = explode("|",$cid_array);
				$cd_ix = trim($cd_ix);
				$zipcode = trim($zipcode);
				$addr1 = trim($addr1);
				$addr2 = trim($addr2);
				
				$sql = "select cd_ix from shop_company_div where cd_ix='".$cd_ix."'";
				$db->query($sql);
				$db->fetch();

				if($db->total){	//행당 브랜드 분류가 존재시 
		
					$sql = "insert into shop_company set
								cd_ix = '".$cd_ix."',
								company_name = '".$company_name."',
								cp_code = '".$cp_code."',
								disp = '".$disp."',
								status = '".$status."',
								zipcode = '".$zipcode."',
								addr1 = '".$addr1."',
								addr2 = '".$addr2."',
								cp_shotinfo  = '".$cp_shotinfo."',
								regdate = NOW()
								";
					$db->query($sql);
					$c_ix = $db->insert_id();

					/*
						상품카테고리 입력 시작 
					*/
					for($i=0;$i<count($cid_unit);$i++){
						$cid = $cid_unit[$i];
						if($i == "0"){
							$basic = '1';	//첫번째 분류를 기본분류로 지정한다.
						}else{
							$basic = '0';
						}

						$sql = "insert into shop_company_relation set
									cid = '".$cid."',
									c_ix = '".$c_ix."',
									basic = '".$basic."',
									disp = '1',
									insert_yn = 'Y',
									regdate = NOW()";
						$db->query($sql);
					}
				}
			}
			$rownum++;
		}

		// 다 쓴 파일 삭제
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name)){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
		}
		//set_time_limit(300);
		//ini_set('memory_limit', '128M');

		echo "<script language='javascript' src='../js/message.js.php'></script><script>alert('제조사등록 완료되었습니다.');document.location.href='./company_list.php'</script>";

	}


	if ($act == "excel_update"){

		if ($excel_file_size > 0){
			copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
		}

		$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);

		$shift_num = 0;

		// 데이터는 2줄부터 시작, 1 줄은 제목+코드명

		$rownum = 2;

		$columnVar = $objPHPExcel->setActiveSheetIndex(0)->getCell('A' . ($rownum))->getValue();

		while (($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != "") && ($i < 11000)) {
			//////////////////////////// 데이터를 가져옴 //////////////////////////////////

			$gid = $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue();				//품목코드
			$cid = $objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue();				//품목분류
			$gname = $objPHPExcel->getActiveSheet()->getCell('C' . $rownum)->getValue();				//품목명
			$is_use = $objPHPExcel->getActiveSheet()->getCell('D' . $rownum)->getValue();				//사용여부
			$gcode = $objPHPExcel->getActiveSheet()->getCell('E' . $rownum)->getValue();						//대표코드
			$order_basic_unit = $objPHPExcel->getActiveSheet()->getCell('F' . $rownum)->getValue();//매입기본단위
			$item_account = $objPHPExcel->getActiveSheet()->getCell('G' . $rownum)->getValue();	//품목계정
			$standard = $objPHPExcel->getActiveSheet()->getCell('H' . $rownum)->getValue();		//규격	
			$company_name = $objPHPExcel->getActiveSheet()->getCell('I' . $rownum)->getValue();		//기본보관사업장
			$place_name = $objPHPExcel->getActiveSheet()->getCell('J' . $rownum)->getValue();		//기본보관창고
			$section_name = $objPHPExcel->getActiveSheet()->getCell('K' . $rownum)->getValue();		//기본보관장소
			$ci_ix = $objPHPExcel->getActiveSheet()->getCell('L' . $rownum)->getValue();		//주매입처
			$origin_code = $objPHPExcel->getActiveSheet()->getCell('M' . $rownum)->getValue();		//원산지
			$maker = $objPHPExcel->getActiveSheet()->getCell('N' . $rownum)->getValue();		//제조사
			$brand_name = $objPHPExcel->getActiveSheet()->getCell('O' . $rownum)->getValue();			//브랜드
			$model = $objPHPExcel->getActiveSheet()->getCell('P' . $rownum)->getValue();		//모델명
			$available_priod = $objPHPExcel->getActiveSheet()->getCell('Q' . $rownum)->getValue();		//유효기간
			$surtax_div = $objPHPExcel->getActiveSheet()->getCell('R' . $rownum)->getValue();		//부가세적용
			$bs_goods_url = $objPHPExcel->getActiveSheet()->getCell('S' . $rownum)->getValue();		//품목구매 URL
			$search_keyword = $objPHPExcel->getActiveSheet()->getCell('T' . $rownum)->getValue();		//검색키워드
			$etc = $objPHPExcel->getActiveSheet()->getCell('U' . $rownum)->getValue();		//기타
			$leadtime = $objPHPExcel->getActiveSheet()->getCell('V' . $rownum)->getValue();		//리드타임
			$available_amountperday = $objPHPExcel->getActiveSheet()->getCell('W' . $rownum)->getValue();		//일별생산량/구매가능량
			$valuation = $objPHPExcel->getActiveSheet()->getCell('X' . $rownum)->getValue();		//재고평가
			$lotno = $objPHPExcel->getActiveSheet()->getCell('Y' . $rownum)->getValue();		//생산라인번호
            $img_name = $objPHPExcel->getActiveSheet()->getCell('Z' . $rownum)->getValue();		//이미지 파일명 (나중에 추가가능하면 사용함 )

            //단품정보 입력받는 루프
			
			$unit = $objPHPExcel->getActiveSheet()->getCell('AA' . $rownum)->getValue();		//이미지 파일명 (나중에 추가가능하면 사용함 )
			$change_amount = $objPHPExcel->getActiveSheet()->getCell('AB' . $rownum)->getValue();		//이미지 파일명 (나중에 추가가능하면 사용함 )
			$buying_price = $objPHPExcel->getActiveSheet()->getCell('AC' . $rownum)->getValue();		//이미지 파일명 (나중에 추가가능하면 사용함 )
			$offline_wholesale_price = $objPHPExcel->getActiveSheet()->getCell('AD' . $rownum)->getValue();		//이미지 파일명 (나중에 추가가능하면 사용함 )
			$wholesale_price = $objPHPExcel->getActiveSheet()->getCell('AE' . $rownum)->getValue();		//이미지 파일명 (나중에 추가가능하면 사용함 )
			$sellprice = $objPHPExcel->getActiveSheet()->getCell('AF' . $rownum)->getValue();		//이미지 파일명 (나중에 추가가능하면 사용함 )
			$safestock = $objPHPExcel->getActiveSheet()->getCell('AG' . $rownum)->getValue();		//이미지 파일명 (나중에 추가가능하면 사용함 )
			$sell_ing_cnt = $objPHPExcel->getActiveSheet()->getCell('AH' . $rownum)->getValue();		//이미지 파일명 (나중에 추가가능하면 사용함 )
			$barcode = $objPHPExcel->getActiveSheet()->getCell('AI' . $rownum)->getValue();		//이미지 파일명 (나중에 추가가능하면 사용함 )
			$wholesale_sellprice = $objPHPExcel->getActiveSheet()->getCell('AJ' . $rownum)->getValue();		//도매할인가
			$discount_price = $objPHPExcel->getActiveSheet()->getCell('AK' . $rownum)->getValue();		//소매할인가
		
           /* $_col = "AA";
            $unit_array = "";
            $index = 0;
            while($objPHPExcel->getActiveSheet()->getCell($_col.$rownum)->getValue() != ""){
                $unit_array[$index] = $objPHPExcel->getActiveSheet()->getCell($_col . $rownum)->getValue();
                $index++;
                $_col++;
            }
            */

			
            //상품명이 있는경우 db입력 진행.     
            if(!empty($gname) && !empty($gid)){
				$gid = trim($gid);
				$cid = trim(str_repeat('0',15-strlen($cid)).$cid);
				$gname = trim(str_replace("'","&#39;",$gname));
				$is_use = trim($is_use);
				$gcode = trim($gcode);
				$order_basic_unit = trim($order_basic_unit);
				$item_account = trim($item_account);
				$standard = trim($standard);
				//$company_name = trim($company_name);
				//$place_name = trim($place_name); 
				//$section_name = trim($section_name); 텍스트에 공백이 들어가있어서 제거 되면 안될 듯 jk
				$ci_ix = trim($ci_ix);
				//$origin_code = trim(str_replace("'","&#39;",$origin_code));
				$maker = trim(str_replace("'","&#39;",$maker));
				$brand_name = trim($brand_name);
				$model = trim($model);
				$available_priod = trim($available_priod);
				$surtax_div = trim($surtax_div);
				$bs_goods_url = trim($bs_goods_url);
				$search_keyword = trim(str_replace("'","&#39;",$search_keyword));
				$etc = trim($etc);
				$leadtime = trim($leadtime);
				$available_amountperday = trim($available_amountperday);
				$valuation = trim($valuation);
				$lotno = trim($lotno);
				$img_name = trim($img_name);

				$sql = "select gid from inventory_goods where gid='".$gid."'";


				$db->query($sql);
				$db->fetch();

				if($db->total){

					/*
					$tmp_array=explode("|",$unit_array[$i]);
					
					if($i == 0)		$basic_unit = trim($tmp_array[0]); //기본단위
					$unit = trim($tmp_array[0]);
					$change_amount = trim($tmp_array[1]);
					$buying_price =  trim(str_replace(",","",$tmp_array[2]));
					$offline_wholesale_price = trim(str_replace(",","",$tmp_array[3]));
					$wholesale_price = trim(str_replace(",","",$tmp_array[4]));
					$sellprice = trim(str_replace(",","",$tmp_array[5]));
					$safestock = trim($tmp_array[6]);
					$sell_ing_cnt = trim($tmp_array[7]);
					$barcode = trim($tmp_array[8]);
					*/
					
					$unit_array = "";
					$change_amount_array = "";
					$buying_price_array = "";
					$offline_wholesale_price_array  = "";
					$wholesale_price_array  = "";
					$sellprice_array  = "";
					$safestock_array  = "";
					$sell_ing_cnt_array  = "";
					$barcode_array  = "";
					$wholesale_sellprice_array = "";	//도매할인가
					$discount_price_array = "";				//소매할인가

					$unit_array = explode("|",$unit); //단위 정보
					$change_amount_array = explode("|",$change_amount); // 단위당 수량
					$buying_price_array = explode("|",$buying_price); // 기본매입가
					$offline_wholesale_price_array  = explode("|",$offline_wholesale_price); // 오프라인도매가
					$wholesale_price_array  = explode("|",$wholesale_price); // 기본도매가
					$sellprice_array  = explode("|",$sellprice); // 기본소매가
					$safestock_array  = explode("|",$safestock); // 안전재고
					$sell_ing_cnt_array  = explode("|",$sell_ing_cnt); // 가용재고
					$barcode_array  = explode("|",$barcode); // 바코드
					$wholesale_sellprice_array  = explode("|",$wholesale_sellprice); // 도매할인가
					$discount_price_array  = explode("|",$discount_price); // 소매할인가


					$basic_unit = $unit_array[0]; //기본단위
					/*단위 정보의 수량을 기준으로 업데이트 처리jk130716*/
					for($u=0;$u<count($unit_array);$u++){
						switch($unit_array[$u]){
							case 'EA' :  $tmp_unit = 1; //  $unit = 1; ->   $tmp_unit = 1; 으로 변경 20130716 hjy
							break;
							case 'Kg' :  $tmp_unit = 2;
							break;
							case 'm2' :  $tmp_unit = 3;
							break;
							case 'Roll' :  $tmp_unit = 4;
							break;
							case 'BOX' :  $tmp_unit = 5;
							break;
							case 'Pack' :  $tmp_unit = 6;
							break;
							case '생산단위' :  $tmp_unit = 7;
							break;
							case '식' :  $tmp_unit = 8;
							break;
						}

						$sql = "select gid from inventory_goods_unit where gid='".$gid."' and unit ='".$tmp_unit."' ";
						$db->query($sql);
						$db->fetch();	
						
						if($db->total){
							$sql = "update inventory_goods_unit set
									change_amount = '".$change_amount_array[$u]."',
									numerator = '".$change_amount_array[$u]."',
									buying_price = '".$buying_price_array[$u]."',
									offline_wholesale_price = '".$offline_wholesale_price_array[$u]."',
									wholesale_price = '".$wholesale_price_array[$u]."',
									sellprice = '".$sellprice_array[$u]."',
									safestock = '".$safestock_array[$u]."',
									sell_ing_cnt = '".$sell_ing_cnt_array[$u]."',
									barcode = '".$barcode_array[$u]."',
									wholesale_sellprice = '".$wholesale_sellprice_array[$u]."',
									discount_price = '".$discount_price_array[$u]."',
									add_status = 'U',
									is_pos_link = 'N',
									editdate=NOW()
									where gid = '".$gid."' and unit = '".$tmp_unit."'";

							$db->query($sql);
						}
					}
					/*$sql = "select gid from inventory_goods_unit where gid='".$gid."' and unit ='".$unit."' ";
					$db->query($sql);
					$db->fetch();

					if($db->total){
						$sql = "update inventory_goods_unit set
								change_amount = '".$change_amount."',
								numerator = '".$change_amount."',
								buying_price = '".$buying_price."',
								offline_wholesale_price = '".$offline_wholesale_price."',
								wholesale_price = '".$wholesale_price."',
								sellprice = '".$sellprice."',
								safestock = '".$safestock."',
								sell_ing_cnt = '".$sell_ing_cnt."',
								barcode = '".$barcode."',
								add_status = 'U',
								is_pos_link = 'N',
								editdate=NOW()
								where gid = '".$gid."' and unit = '".$unit."'";
						$db->query($sql);
					}*/
			
					if ($is_use == "사용"){
						$is_use = "Y";
					}else if($is_use == "사용안함"){
						$is_use = "N";
					}else{
						$is_use = "1";
					}
					
					switch($basic_unit){
						case 'EA' :  $basic_unit = 1;
						break;
						case 'Kg' :  $basic_unit = 2;
						break;
						case 'm2' :  $basic_unit = 3;
						break;
						case 'Roll' :  $basic_unit = 4;
						break;
						case 'BOX' :  $basic_unit = 5;
						break;
						case 'Pack' :  $basic_unit = 6;
						break;
						case '생산단위' :  $basic_unit = 7;
						break;
						case '식' :  $basic_unit = 8;
						break;
					}
					
					switch($item_account){
						case '원재료' :  $item_account = 1;
						break;
						case '부재료' :  $item_account = 2;
						break;
						case '반제품' :  $item_account = 3;
						break;
						case '완제품(상품)' :  $item_account = 4;
						break;
						case '용역' :  $item_account = 5;
						break;
						case '저장품' :  $item_account = 6;
						break;
						case '가상품목' :  $item_account = 9;
						break;
						
					}
					switch($surtax_div){
						case '부과세포함' :  $surtax_div = 1;
						break;
						case '부과세별도' :  $surtax_div = 2;
						break;
						case '영세율적용' :  $surtax_div = 3;
						break;
						case '면세율적용' :  $surtax_div = 4;
						break;
						case '부가세없음' :  $surtax_div = 5;
						break;
						
					}
					if(!empty($place_name)){
						$sql = "select pi_ix FROM inventory_place_info where place_name = '".$place_name."' and company_id = '".$company_name."'";
						$db->query($sql);
						$db->fetch();
						$pi_ix = $db->dt[pi_ix];
					}
					
					if(!empty($section_name)){
						$sql = "select ps_ix FROM inventory_place_section where section_name = '".$section_name."' and pi_ix = '".$pi_ix."' ";
						$db->query($sql);
						$db->fetch();
						$ps_ix = $db->dt[ps_ix];
					}
					if(!empty($brand_name)){
						$sql = "SELECT *
									FROM 
										shop_brand 
									WHERE
										brand_code  ='".$brand_name."' ";
						//echo $sql;
						$db->query($sql);
						$db->fetch();
						$b_ix = $db->dt[b_ix];
					}
					if($valuation == "이동식평균법"){
						$valuation = 1;
					}else{
						$valuation = 1;
					}
					if($origin_code){// 엑셀에 등록된 원산지코드를 받아와 실제 코드의 인덱스 정보를 DB에 저장하기 위해 작업 jk130719 어라운지에는 이렇게 들어가야한다함
						$sql = "select * from common_origin where origin_code = '".$origin_code."'";
						$db->query($sql);
						$db->fetch();
						$orgin = $db->dt[origin_name];
					}
					
					$sql = "update inventory_goods set
								gname = '".$gname."',
								gcode = '".$gcode."',
								barcode = '".$barcode."',
								basic_unit = '".$basic_unit."',
								order_basic_unit = '".$basic_unit."',
								sell_basic_unit = '".$basic_unit."',
								cid = '".$cid."',
								model = '".$model."',
								available_priod = '".$available_priod."',
								admin = '".$admininfo[company_id]."',
								item_account = '".$item_account."',
								standard = '".$standard."',
								orgin = '".$orgin."',
								maker = '".$maker."',
								b_ix='".$b_ix."',
								ci_ix = '".$ci_ix."',
								pi_ix = '".$pi_ix."',
								ps_ix = '".$ps_ix."',
								surtax_div = '".$surtax_div."',
								is_use = '".$is_use."',
								valuation = '".$valuation."',
								etc = '".$etc."',
								bs_goods_url = '".$bs_goods_url."',
								search_keyword = '".$search_keyword."',
								leadtime = '".$leadtime."',
								available_amountperday = '".$available_amountperday."',
								lotno = '".$lotno."',
								editdate = NOW()
								where gid = '".$gid."'
					";

					$db->sequences = "INVENTORY_GOODS_SEQ";
					$db->query($sql);
					

					/////////////////////////////////	현재 일괄등록에 이미지를 추가하지 않아서 잠시 주석 2013-06-24 이학봉
					if ($img_name != "") {
						Batch_Images_Processing($img_name, $gid);
					}
				}
			}
			$rownum++;
		}

		// 다 쓴 파일 삭제
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name)){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
		}
		//set_time_limit(300);
		//ini_set('memory_limit', '128M');
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('재고상품등록이 완료되었습니다.');document.location.href='./inventory_goods_input_excel.php'</script>";

	}

	function Batch_Images_Processing($xlImageFileName, $b_ix,$type = 'logo')
	{
		global $admin_config;
		
		if($type == "logo"){	//로고디자인
			$image_div = "";
		}else if($type == "top"){	//상단이미지
			$image_div = "b_";
		}
		//echo $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/".$image_div."brand_".$b_ix.".gif";
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/";

		if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/")){//폴더가 생성되지 않아서 수정 2012-05-25 홍진영
			mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/");
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/",0777);
		}

		copy($xlImageFileName, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/".$image_div."brand_".$b_ix.".gif");

	}

	function ExcelImageCopy($image_path, $pid){
		global $admin_config, $DOCUMENT_ROOT,$image_db;

		$basedir = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/inventory/";
		$targetDirectory = $basedir . UploadDirText($basedir, $pid, 'Y') . '/';

		if(!file_exists($image_path)) {
			return ;
		}
		$image_info = getimagesize ($image_path);
		$image_type = substr($image_info['mime'],-3);

		$image_db->query("select width,height from shop_image_resizeinfo order by idx");//kbk 11/12/15
		$image_info2 = $image_db->fetchall();
		$basic_img_src = $targetDirectory . "/basic_".$pid.".gif";
		copy($image_path, $basic_img_src);

		switch ($image_type) {
			case "gif":
			case "GIF":
				MirrorGif($basic_img_src, $targetDirectory . "/b_".$pid.".gif", MIRROR_NONE);
				resize_gif($targetDirectory . "/b_".$pid.".gif",$image_info2[0][0],$image_info2[0][1]);

				MirrorGif($basic_img_src, $targetDirectory . "/c_".$pid.".gif", MIRROR_NONE);
				resize_gif($targetDirectory . "/c_".$pid.".gif",$image_info2[4][0],$image_info2[4][1]);
				break;

				/*
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$pid.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$pid.".gif",300,300);

				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$pid.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$pid.".gif",137,137);

				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif",90,90);

				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$pid.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$pid.".gif",50,50);
				break;
				*/
			default:
				//copy($image_path, $basic_img_src);

				Mirror($basic_img_src, $targetDirectory . "/b_".$pid.".gif", MIRROR_NONE);
				resize_jpg($targetDirectory . "/b_".$pid.".gif",$image_info2[0][0],$image_info2[0][1]);

				Mirror($basic_img_src, $targetDirectory . "/c_".$pid.".gif", MIRROR_NONE);
				resize_jpg($targetDirectory . "/c_".$pid.".gif",$image_info2[4][0],$image_info2[4][1]);
				break;
		}
		if (is_file($image_path)) {
			@unlink($image_path);
		}
		//syslog(LOG_INFO, 'END\r\n');
	}


	// removes a directory and everything within it
	function rmdirr($target,$verbose=false)
	{
		$exceptions=array('.','..');
		if (!$sourcedir=@opendir($target))
		{
		   if ($verbose)
		       echo '<strong>Couldn&#146;t open '.$target."</strong><br />\n";
		   return false;
		}
		while(false!==($sibling=readdir($sourcedir)))
		{
		   if(!in_array($sibling,$exceptions))
		   {
		       $object=str_replace('//','/',$target.'/'.$sibling);
		       if($verbose)
		           echo 'Processing: <strong>'.$object."</strong><br />\n";
		       if(is_dir($object))
		           rmdirr($object);
		       if(is_file($object))
		       {
		           $result=@unlink($object);
		           if ($verbose&&$result)
		               echo "File has been removed<br />\n";
		           if ($verbose&&(!$result))
		               echo "<strong>Couldn&#146;t remove file</strong>";
		       }
		   }
		}
		closedir($sourcedir);
		if($result=@rmdir($target))
		{
		   if ($verbose)
		       echo "Target directory has been removed<br />\n";
		   return true;
		}
		if ($verbose)
		   echo "<strong>Couldn&#146;t remove target directory</strong>";

		return false;
	}


	function ClearText($str)
	{
		return str_replace(">","",$str);
	}


	function returnFileName($filestr){
		$strfile = split("/",$filestr);

		return str_replace("%20","",$strfile[count($strfile)-1]);
		//return count($strfile);

	}

	function returnImagePath($str){
		$IMG = split(" ",$str);

		for($i=0;$i<count($IMG);$i++){
			//echo substr_count($IMG[$i],"src");
				if(substr_count($IMG[$i],"src=") > 0){
					$mstring = str_replace("src=","",$IMG[$i]);
					return str_replace("\"","",$mstring);
				}
		}
	}

	function imageExists($image,$dir) {

	    $i=1; $probeer=$image;

	    while(file_exists($dir.$probeer)) {
	        $punt=strrpos($image,".");
	        if(substr($image,($punt-3),1)!==("[") && substr($image,($punt-1),1)!==("]")) {
	            $probeer=substr($image,0,$punt)."[".$i."]".
	            substr($image,($punt),strlen($image)-$punt);
	        } else {
	            $probeer=substr($image,0,($punt-3))."[".$i."]".
	            substr($image,($punt),strlen($image)-$punt);
	        }
	        $i++;
	    }
	    return $probeer;
	}
?>