<?
include("../../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../basic/company.lib.php");

$db = new Database;

if ($act == "insert"){

	if($info_type == "basic"){

		$company_id  = md5(uniqid(rand()));							//company_id 생성
		$company_code  = trim($_REQUEST[company_code]);				//본사코드
		$open_date  = trim($_REQUEST[open_date]);					//설립일
		$com_ceo  = trim($_REQUEST[com_ceo]);						//대표자명
		$com_div  = trim($_REQUEST[com_div]);						//사업자 유형 R:법인사업자 P:일반사업자 S:간이과세자 E:면세사업자
		$business_type  = trim($_REQUEST[business_type]);			//사업 유형  O : 온라인 F:오프라인 A:온+오프라인
		$com_number  = trim($_REQUEST[com_number_1]."-".$_REQUEST[com_number_2]."-".$_REQUEST[com_number_3]);			//사업자번호
		$corporate_number  = trim($_REQUEST[corporate_number_1]."-".$_REQUEST[corporate_number_2]);			//법인번호
		$com_business_status  = trim($_REQUEST[com_business_status]);			//업태
		$com_business_category  = trim($_REQUEST[com_business_category]);		//업종
		$com_zip  = trim($_REQUEST[com_zip]);							//우편번호
		$com_addr1  = trim($_REQUEST[com_addr1]);						//주소1
		$com_addr2  = trim($_REQUEST[com_addr2]);						//주소2
		$com_phone  = trim($_REQUEST[com_phone_1]."-".$_REQUEST[com_phone_2]."-".$_REQUEST[com_phone_3]);			//대표번호
		$com_mobile  = trim($_REQUEST[com_mobile_1]."-".$_REQUEST[com_mobile_2]."-".$_REQUEST[com_mobile_3]);		//대표핸드폰번호
		$com_fax  = trim($_REQUEST[com_fax_1]."-".$_REQUEST[com_fax_2]."-".$_REQUEST[com_fax_3]);					//대표팩스번호
		$com_homepage  = trim($_REQUEST[com_homepage]);					//홈페이지
		$com_email  = trim($_REQUEST[com_email]);						//이메일
		$online_business_number  = trim($_REQUEST[online_business_number]);			//통신판매업 번호
		$seller_auth  = trim($_REQUEST[seller_auth]);			//본사등록승인 여부 N:승인대기 Y : 승인 X : 승은거부
		$info_type	= trim($_REQUEST[info_type]); // 입력구분값

		$com_type  = trim($_REQUEST[com_type]); // 사업장 유형
		$shipping_yn = trim($_REQUEST[shipping_yn]); // 창고사용유무
		$is_wharehouse = trim($_REQUEST[is_wharehouse]); // 물류창고 사용여부
		$relation_code = trim($_REQUEST[cid2]);	 // 선택한상위 본사 코드
		
		if($com_type == "BO" || $com_type == "BP"){
			$com_name = trim($_REQUEST[com_name_po]);		//사업소 , 영업소 일경우
		}else if($com_type == "BR"){
			$com_name = trim($_REQUEST[com_name]);			//지사일경우 	
		}

		if(is_array($sell_type)){	//거래처 유형
			foreach($sell_type as $key=>$value){
				if($value && $key == '0'){
					$seller_type = $value;
				}else{
					$seller_type .= "|".$value;
				}
			}
		}

		$sql = "insert into ".TBL_COMMON_COMPANY_DETAIL." set
					company_id = '".$company_id."',
					com_type = '".$com_type."',
					open_date = '".$open_date."',
					company_code = '".$company_code."',
					com_name = '".$com_name."',
					com_ceo = '".$com_ceo."',
					com_div = '".$com_div."',
					business_type = '".$business_type."',
					com_number = '".$com_number."',
					corporate_number = '".$corporate_number."',
					com_business_status = '".$com_business_status."',
					com_business_category = '".$com_business_category."',
					com_zip = '".$com_zip."',
					com_addr1 = '".$com_addr1."',
					com_addr2 = '".$com_addr2."',
					com_phone = '".$com_phone."',
					com_mobile = '".$com_mobile."',
					com_fax = '".$com_fax."',
					com_homepage = '".$com_homepage."',
					com_email = '".$com_email."',
					seller_type = '".$seller_type."',
					online_business_number = '".$online_business_number."',
					seller_auth = '".$seller_auth."',
					is_wharehouse = '".$is_wharehouse."',
					person = '$com_person',
					is_erp_link = 'N',
					inputtype = 'A',
					regdate = NOW();";
		$db->query($sql);
		$company_ix = $db->insert_id();
		$db->query("update ".TBL_COMMON_COMPANY_DETAIL." set custseq = '".$company_ix."' where company_ix = '".$company_ix."'");

/*	물류창고 등록은 재고관리 창고 등록에서 이루어진다. 본사, 지사 사업장 등록시 물류창고 사용여부는 사용함, 안함 용도르 쓰여진다 
		if($is_wharehouse == "1"){	//창고 등록 

			$sql = "select IFNULL(max(exit_order),0) as exit_order from inventory_place_info ";
			$db->query($sql);
			if($db->total){
				$db->fetch();
				$exit_order = $db->dt[exit_order];
			}else{
				$exit_order = '1';
			}
			$sql = "insert into inventory_place_info
						(pi_ix,place_type, place_name,place_position,place_tel,place_fax,place_msg, return_position, disp, exit_order,regdate,company_id)
						values
						('','$place_type','$place_name','$place_position','$place_tel','$place_fax','$place_msg','$return_position','$disp','$exit_order',NOW(),'$company_id')";
			
			
			$db->sequences = "INVENTORY_PLACE_INFO_SEQ";
			$db->query($sql);

			$pi_ix = $db->insert_id();

			$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set pi_ix = '".$pi_ix."' where company_id = '".$company_id."'";

			$db->query($sql);

		}	//창고 등록 끝
*/

		// 이미지 저장 시작 
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;
		if(!is_dir($path)){
			exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
		}
		if ($stamp_file_size > 0){
			copy($stamp_file,$path."/"."company_stamp_".$company_id.".gif");
		}
		// 이미지 저장 끝
		
		//트리코드 생성 시작
		$seq	= check_seq($relation_code,$depth);
		$new_code = check_relation($relation_code,$depth);
		//트리코드 생성 끝

		$sql = "insert into ".TBL_COMMON_COMPANY_RELATION." set
					company_id = '".$company_id."',
					relation_code = '".$new_code."',
					seq = '".$seq."',
					reg_date = NOW();";
		$db->query($sql);

		echo("<script>alert('정상적으로 입력 되었습니다.');parent.document.location.href = 'customer.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
		
	}else if($info_type == "seller_info"){
		$sql = "select
					company_code
				from	
					".TBL_COMMON_COMPANY_DETAIL."
				where
					company_id = '".$company_id."'";
		$db->query($sql);
		$db->fetch();
		$company_code = $db->dt[company_code];
		
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;
		if(!is_dir($path)){
			exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
		}

		$data_array = $_FILES;
	
		foreach($data_array as $val => $key){
			if($val != 'sheet_value'){

				if($data_array[$val][size] >0){
					
					$file_type = substr(strrchr($data_array[$val][name], '.'), 1); 
					$del_sql = "delete from	".TBL_COMMON_COMPANY_FILE." where company_id  = '".$company_id."' and sheet_name = '".$val."'";
					$db->query($del_sql);

					$sheet_value = $val."_".$company_id.".".$file_type;		//파일명
					
					$sql = "insert into ".TBL_COMMON_COMPANY_FILE." set
								company_code = '".$company_code."',
								company_id = '".$company_id."',
								sheet_name = '".$val."',
								sheet_value = '".$sheet_value."',
								text = '".$txt."',
								reg_date = NOW();";
					$db->query($sql);
					$size_name = $val."_size";
					$img_name = $_FILES[$val][tmp_name];

					// 이미지 저장 시작 
					if ($$size_name > 0){
						copy($img_name,$path."/".$val."_".$company_id.".".$file_type);
					}
					// 이미지 저장 끝
				}
			}else if($val == "sheet_value"){

				for($i = 0 ; $i<count($key[name]); $i++){
					
					$sheet_name = "sheet_name_"."$i";		//필드명

					$sql = "select * from ".TBL_COMMON_COMPANY_FILE." where company_id = '".$company_id."' and sheet_name = '".$sheet_name."'";
					$db->query($sql);
					$db->fetch();

					if($key[name][$i]){
						$file_type = substr(strrchr($key[name][$i], '.'), 1); 
					}else {
						$file_type =substr(strrchr($db->dt[sheet_value], '.'), 1); 
					}

					$sheet_value = $sheet_name.".".$file_type;		//파일명
					$img_name = $key[tmp_name][$i];			//이미지파일
					$txt = $_REQUEST[sheet_name][$i];		//txt 명
					$size_name = $key[size][$i];	//이미지용량

					if($size_name >0){

						$del_sql = "delete from	".TBL_COMMON_COMPANY_FILE." where company_id  = '".$company_id."' and sheet_name = '".$sheet_name."'";
						$db->query($del_sql);
						
						$sql = "insert into ".TBL_COMMON_COMPANY_FILE." set
							company_code = '".$company_code."',
							company_id = '".$company_id."',
							sheet_name = '".$sheet_name."',
							sheet_value = '".$sheet_value."',
							text = '".$txt."',
							reg_date = NOW();
						";
						$db->query($sql);
						if ($size_name > 0){
						copy($img_name,$path."/".$sheet_value);
						}
					}
				}
			}
		}
	}else if($info_type == "delivery_info"){
		
		$pos_array = $_REQUEST['pos'];
		$solution_yn = $_REQUEST['solution_yn'];
		$solution_code = $_REQUEST['solution_code'];
		$solution_coutcode = $_REQUEST['solution_coutcode'];
		$site_yn = $_REQUEST['site_yn'];
		$site_id = $_REQUEST['site_id'];
		$site_pw = $_REQUEST['site_pw'];
		
		$sql = "select
						company_code
					from	
						".TBL_COMMON_COMPANY_DETAIL."
					where
						company_id = '".$company_id."'
				";
	
			$db->query($sql);
			$db->fetch();
			$company_code = $db->dt[company_code];

		for($i = 0; $i<count($pos_array);$i++){
			
			$pos_use = $pos_array[$i][pos_use];
			$pos_code = $pos_array[$i][pos_code];
			$pos_outcode = $pos_array[$i][pos_outcode];

			$sql = "
				insert into ".TBL_COMMON_POS_DETAIL." set
					company_code = '".$company_code."',
					company_id = '".$company_id."',
					pos_use = '".$pos_use."',
					pos_code = '".$pos_code."',
					pos_outcode = '".$pos_outcode."',
					reg_date = NOW();
			";
		//	echo "$sql";
			$db->query($sql);

		}

		$info_sql = "
				insert into ".TBL_COMMON_POS_INFO." set
					company_code = '".$company_code."',
					company_id = '".$company_id."',
					solution_yn = '".$solution_yn."',
					solution_code = '".$solution_code."',
					solution_coutcode = '".$solution_coutcode."',
					site_yn = '".$site_yn."',
					site_id = '".$site_id."',
					site_pw = '".$site_pw."',
					reg_date = NOW();

		";

		$db->query($info_sql);

	}

	if($admininfo[admin_level] == 9){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'customer.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
	}else if($admininfo[admin_level] == 8){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'customer.add.php?mmode=".$mmode."&company_id=".$admininfo[company_id]."';</script>");
	}
}


if ($act == "update"){	

		$company_code  = trim($_REQUEST[company_code]);	//본사코드
		$open_date  = trim($_REQUEST[open_date]);			//설립일
		
		$com_ceo  = trim($_REQUEST[com_ceo]);			//대표자명
		$com_div  = trim($_REQUEST[com_div]);			//사업자 유형 R:법인사업자 P:일반사업자 S:간이과세자 E:면세사업자
		$business_type  = trim($_REQUEST[business_type]);			//사업 유형  O : 온라인 F:오프라인 A:온+오프라인
		$com_number  = trim($_REQUEST[com_number_1]."-".$_REQUEST[com_number_2]."-".$_REQUEST[com_number_3]);			//사업자번호
		$corporate_number  = trim($_REQUEST[corporate_number_1]."-".$_REQUEST[corporate_number_2]);			//법인번호
		$com_business_status  = trim($_REQUEST[com_business_status]);			//업태
		$com_business_category  = trim($_REQUEST[com_business_category]);			//업종
		$com_zip  = trim($_REQUEST[com_zip]);			//우편번호
		$com_addr1  = trim($_REQUEST[com_addr1]);			//주소1
		$com_addr2  = trim($_REQUEST[com_addr2]);			//주소2
		$com_phone  = trim($_REQUEST[com_phone_1]."-".$_REQUEST[com_phone_2]."-".$_REQUEST[com_phone_3]);			//대표번호
		$com_mobile  = trim($_REQUEST[com_mobile_1]."-".$_REQUEST[com_mobile_2]."-".$_REQUEST[com_mobile_3]);			//대표핸드폰번호
		$com_fax  = trim($_REQUEST[com_fax_1]."-".$_REQUEST[com_fax_2]."-".$_REQUEST[com_fax_3]);			//대표팩스번호
		$com_homepage  = trim($_REQUEST[com_homepage]);			//홈페이지
		$com_email  = trim($_REQUEST[com_email]);			//이메일
		$online_business_number  = trim($_REQUEST[online_business_number]);			//통신판매업 번호
		$seller_auth  = trim($_REQUEST[seller_auth]);			//본사등록승인 여부 N:승인대기 Y : 승인 X : 승은거부
		$info_type	= trim($_REQUEST[info_type]); // 입력구분값
		
		$com_type  = trim($_REQUEST[com_type]); // 입력구분값
		$shipping_yn = trim($_REQUEST[shipping_yn]); // 입력구분값
		$is_wharehouse = trim($_REQUEST[is_wharehouse]); // 물류창고 사용여부

		$relation_code = trim($_REQUEST[cid2]);	 // 선택한상위 본사 코드
		
		//$seller_type_array = array('sales_vendor'=>$sales_vendor,'supply_vendor'=>$supply_vendor,'oversea_sales'=>$oversea_sales,'oversea_supply'=>$oversea_supply,'outsourcing'=>$outsourcing);
		//$seller_type = serialize($seller_type_array);	//거래처 유형

		$company_id = trim($_REQUEST[company_id_1]);	// company_id 로 넘기면 문제가 생겨서 company_id_1 로 이름을 바꿔서 넘김 
		
		if($com_type == "BO" || $com_type == "BP"){
			$com_name  = trim($_REQUEST[com_name_po]);			//사업장,사업소명
		}else if($com_type == "BR"){
			$com_name  = trim($_REQUEST[com_name]);			//사업장,사업소명		
		}

	if(is_array($sell_type)){	//거래처 유형
		foreach($sell_type as $key=>$value){
			if($value && $key == '0'){
				$seller_type = $value;
			}else{
				$seller_type .= "|".$value;
			}
		}
	}

	if(!$_REQUEST[company_code]){
		$sql = "select
					company_code
				from	
					".TBL_COMMON_COMPANY_DETAIL."
				where
					company_id = '".$company_id."'
			";
		$db->query($sql);
		$db->fetch();
		$company_code = $db->dt[company_code];
	}

	if($info_type == "basic"){

		$sql = "UPDATE ".TBL_COMMON_COMPANY_DETAIL." SET
				company_code = '$company_code',
				open_date = '$open_date',
				com_name='$com_name',
				com_div='$com_div',
				business_type = '$business_type',
				com_number = '$com_number',
				com_type='$com_type',
				corporate_number = '$corporate_number',
				com_ceo='$com_ceo',
				com_business_status='$com_business_status',
				com_business_category='$com_business_category',
				com_number='$com_number',
				online_business_number='$online_business_number',
				com_phone = '".$com_phone."',
				com_mobile = '".$com_mobile."',
				com_fax='$com_fax',
				com_email='$com_email',
				com_zip='$com_zip',
				com_addr1='$com_addr1',
				com_addr2='$com_addr2',
				seller_type = '$seller_type',
				seller_auth='$seller_auth',
				person = '$com_person',
				is_erp_link = 'N',
				inputtype = 'U',
				is_wharehouse = '$is_wharehouse',
				com_homepage = '$com_homepage'
			WHERE 
				company_id='$company_id'"; 
		$db->query($sql);

		$relation_code = $cid2;
		$seq	= check_seq($relation_code,$depth);
		$new_code = check_relation($relation_code,$depth);
		$sql_relation = "
				update 
					".TBL_COMMON_COMPANY_RELATION." set
				relation_code = '".$new_code."',
				seq = '".$seq."',
				edit_date = NOW()
				where
					company_id = '".$company_id."'";
		$db->query($sql_relation);
		
		/*
		if($is_wharehouse == "1"){

				$sql = "
						select
							pi_ix
						from
							inventory_place_info
						where
							pi_ix = '".$pi_ix."'
				";

				$db->query($sql);
				$db->fetch();
				$pi_ix_check = $db->dt[pi_ix];
				if($pi_ix_check){
				//	$place_tel = $place_phone1."-".$place_phone2."-".$place_phone3;
				//	$place_fax = $place_fax1."-".$place_fax2."-".$place_fax3;

					$sql = "update inventory_place_info set
								place_type = '$place_type',place_name = '$place_name',place_position = '$place_position', place_tel = '$place_tel', place_fax='$place_fax',	place_msg='$place_msg',	return_position='$return_position',	disp='$disp',company_id = '$company_id'
								where pi_ix = '$pi_ix'";
			
					$db->query($sql);
				}else{

					$sql = "select IFNULL(max(exit_order),0) as exit_order from inventory_place_info ";
					$db->query($sql);
					if($db->total){
						$db->fetch();
						$exit_order = $db->dt[exit_order];
					}else{
						$exit_order = '1';
					}
					$sql = "insert into inventory_place_info
								(pi_ix,place_type, place_name,place_position,place_tel,place_fax,place_msg, return_position, disp, exit_order,regdate,company_id)
								values
								('','$place_type','$place_name','$place_position','$place_tel','$place_fax','$place_msg','$return_position','$disp','$exit_order',NOW(),'$company_id')";
					
					
					$db->sequences = "INVENTORY_PLACE_INFO_SEQ";
					$db->query($sql);

					$pi_ix = $db->insert_id();

					$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set pi_ix = '".$pi_ix."' where company_id = '".$company_id."'";

					$db->query($sql);
				
				}
		}
		if($is_wharehouse == "0"){
			
			$sql = "
						select
							pi_ix
						from
							".TBL_COMMON_COMPANY_DETAIL."
						where
							company_id = '".$company_id."'
			";
			$db->query($sql);
			$db->fetch();

			$pi_ix = $db->dt[pi_ix];

			$sql = "delete from inventory_place_info where pi_ix = '".$pi_ix."'";
			$db->query($sql);

			$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set pi_ix = '0'  where company_id = '".$company_id."'";		//창고가 삭제되면서 company_detail pi_ix 도 0으로 수정됨
			$db->query($sql);
			
		}
		*/


		// 이미지 저장 시작 
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;
		if(!is_dir($path)){
			mkdir($path, 0777);
			//exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
		}
		if ($stamp_file_size > 0){
			copy($stamp_file,$path."/"."company_stamp_".$company_id.".gif");
			//$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif";
		}
		// 이미지 저장 끝

	}else if($info_type == "seller_info"){

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;

		if(!is_dir($path)){
			exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
		}

		$data_array = $_FILES;
	
			foreach($data_array as $val => $key){

				if($val != 'sheet_value'){

					if($data_array[$val][size] >0){
						
						$file_type = substr(strrchr($data_array[$val][name], '.'), 1); 

						$del_sql = "delete from	".TBL_COMMON_COMPANY_FILE." where company_id  = '".$company_id."' and sheet_name = '".$val."'";
						$db->query($del_sql);
						$sheet_value = trim($val."_".$company_id.".".$file_type);		//파일명
						
						$sql = "
							insert into ".TBL_COMMON_COMPANY_FILE." set
								company_code = '".$company_code."',
								company_id = '".$company_id."',
								sheet_name = '".$val."',
								sheet_value = '".$sheet_value."',
								text = '".$txt."',
								reg_date = NOW();
							";

						$db->query($sql);
						$size_name = $val."_size";
						$img_name = $_FILES[$val][tmp_name];

						// 이미지 저장 시작 
						if ($$size_name > 0){
							copy($img_name,$path."/".$val."_".$company_id.".".$file_type);
						}
						// 이미지 저장 끝
					}

				}else if($val == "sheet_value"){

					for($i = 0 ; $i<count($key[name]); $i++){
						
							$sheet_name = "sheet_name_"."$i";		//필드명

							$sql = "select * from ".TBL_COMMON_COMPANY_FILE." where company_id = '".$company_id."' and sheet_name = '".$sheet_name."'";
							$db->query($sql);
							$db->fetch();
						
							if($key[name][$i]){
								$file_type = substr(strrchr($key[name][$i], '.'), 1); 
							}else {
								$file_type =substr(strrchr($db->dt[sheet_value], '.'), 1); 
							}
							
							$sheet_value = trim($sheet_name.".".$file_type);		//파일명
							$img_name = $key[tmp_name][$i];			//이미지파일
							$txt = $_REQUEST[sheet_name][$i];		//txt 명
							$size_name = $key[size][$i];			//이미지용량

							$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;

							if ($size_name > 0){
								@unlink($path."/".$sheet_name.".".$file_type);		//수정시 첨부한 파일이 있으면 삭제한다.
								copy($img_name,$path."/".$sheet_value);
							}else{
								copy($path."/".$_REQUEST[sheet_value][$i][image_name],$path."/".$sheet_value);
								//echo $path."/".$_REQUEST[sheet_value][$i][image_name],$path."/".$sheet_value;
							}

							$del_sql = "delete from	".TBL_COMMON_COMPANY_FILE." where company_id  = '".$company_id."' and sheet_name = '".$sheet_name."'";
							$db->query($del_sql);

							$sql = "
								insert into ".TBL_COMMON_COMPANY_FILE." set
									company_code = '".$company_code."',
									company_id = '".$company_id."',
									sheet_name = '".$sheet_name."',
									sheet_value = '".$sheet_value."',
									text = '".$txt."',
									reg_date = NOW();
								";
								
								$db->query($sql);
					}
				}
			}

			$where = " and sheet_name not in ( ";
			foreach($_REQUEST[sheet_name] as $til =>$val){
				
				if(count($_REQUEST[sheet_name])-1 != $til){
					$where .= "'sheet_name_".$til."',";
				}else{
					$where .= "'sheet_name_".$til."'";
				}	
			}
			$where .=")";
			$del_sql = "delete from	".TBL_COMMON_COMPANY_FILE." where company_id  = '".$company_id."' $where and sheet_name like 'sheet_name_%'";
			$db->query($del_sql);

	}else if($info_type == "delivery_info"){
	
		$pos_array = $_REQUEST['pos'];
		$solution_yn = $_REQUEST['solution_yn'];
		$solution_code = $_REQUEST['solution_code'];
		$solution_coutcode = $_REQUEST['solution_coutcode'];
		$site_yn = $_REQUEST['site_yn'];
		$site_id = $_REQUEST['site_id'];
		$site_pw = $_REQUEST['site_pw'];
	
		for($i = 0; $i<count($pos_array);$i++){
			
			$pos_use = $pos_array[$i][pos_use];
			$pos_code = $pos_array[$i][pos_code];
			$pos_outcode = $pos_array[$i][pos_outcode];
			$pos_detail_ix = $pos_array[$i][pos_detail_ix];

			$sql = "
				update ".TBL_COMMON_POS_DETAIL." set
					company_code = '".$company_code."',
					pos_use = '".$pos_use."',
					pos_code = '".$pos_code."',
					pos_outcode = '".$pos_outcode."',
					edit_date = NOW()
				where
					pos_detail_ix = '".$pos_detail_ix."'
					and company_id = '".$company_id."'
			";
			//echo "$sql";
			$db->query($sql);

		}
			

		$info_sql = "
				update ".TBL_COMMON_POS_INFO." set
					company_code = '".$company_code."',
					solution_yn = '".$solution_yn."',
					solution_code = '".$solution_code."',
					solution_coutcode = '".$solution_coutcode."',
					site_yn = '".$site_yn."',
					site_id = '".$site_id."',
					site_pw = '".$site_pw."',
					reg_date = NOW()
				where
					pos_info_ix = '".$pos_info_ix."'
					and company_id = '".$company_id."'
		";

		$db->query($info_sql);


	}

	if($admininfo[admin_level] == 9){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'customer.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
	}else if($admininfo[admin_level] == 8){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'customer.add.php?mmode=".$mmode."&company_id=".$admininfo[company_id]."';</script>");
	}

}

if ($act == "recommend")
{
	$sql = "UPDATE ".TBL_COMMON_COMPANY_DETAIL." SET
			recommend='$recomm'
			WHERE company_id='$company_id'"; // 이름에 대한 수정을 없앰 kbk
			// 회원과 회사 정보는 1:다 관계 이므로 code 값을 company_id 로 변경

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
	echo("<script>top.document.location.reload();</script>");
}



if($act == "delete"){
	$sql = "delete from ".TBL_COMMON_COMPANY_DETAIL." where company_id ='$company_id'";
	//echo $sql;
	$db->query($sql);

	$sql = "delete from ".TBL_COMMON_COMPANY_RELATION." where company_id ='$company_id'";
	//echo $sql;
	$db->query($sql);	//거래처일 경우 seller_detail 도 삭제해야함

	$sql = "
				select
					pi_ix
				from
					".TBL_COMMON_COMPANY_DETAIL."
				where
					company_id = '".$company_id."'
	";
	$db->query($sql);
	$db->fetch();

	$pi_ix = $db->dt[pi_ix];
	if($pi_ix){
		$sql = "delete from inventory_place_info where pi_ix = '".$pi_ix."'";

		$db->query($sql);	//창고로 등록되어 있다면 창고도 삭제
	}

	$sql = "select code from ".TBL_COMMON_USER." where company_id ='$company_id'";
	$db->query($sql);
	$total = $db->total;
	$users = $db->fetchall();

	for($i=0;$i < count($users);$i++){
		$db->fetch($i);
		$code = $users[$i][code];

		$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL."  where code = '".$code."' ";
		//echo $sql;
		$db->query($sql);

		$sql = "delete from ".TBL_COMMON_USER."  where company_id ='$company_id' and code = '".$code."' ";
		//echo $sql;
		$db->query($sql);

	}

	//이미지 삭제없어서 추가 2012-10-11 홍진영
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif");
	}

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif");
	}

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif");
	}

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif");
	}


	$sql = "select id from ".TBL_SHOP_PRODUCT." where admin = '$company_id'";
	$db->query($sql);
	$total = $db->total;
	for($i=0;$i < $total;$i++){
		$db->fetch($i);
		$id = $db->dt[id];

		$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $id, 'Y');
		$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $id, 'Y');

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/b_$id.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/b_$id.gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/".$uploaddir."m_$id.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/m_$id.gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_$id.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_$id.gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/s_$id.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/s_$id.gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/c_$id.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/c_$id.gif");
		}

		if($id && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id")){
			rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id");
		}

		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='$id'");
		$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='$id'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$id'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='$id'");
		$db->query("DELETE FROM ".TBL_SHOP_RELATION_PRODUCT." WHERE pid = '$id'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT."_auction WHERE pid = '$id'");

	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');</script>");
	echo("<script>location.href = 'company.list.php?mmode=".$mmode."&';</script>");
	echo $delivery_price;

}


if($act == "user_insert"){

	//$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE company_id = '$company_id' and id = '$id' ");
	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id = '$id' ");//입점업체별로 동일 아이디가 생성될 수 있으므로 company_id를 조건절에서 뺌(by 김수현대리) kbk 12/02/08

	if ($db->total)
	{
		echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$id] ' + language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 사용자 입니다.
		//echo("<script>history.back();</script>");
		exit;
	}

	$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id = '$id' ");

	if ($db->total)
	{
		echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$id] '+ language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 아이디 입니다.
		//echo("<script>history.back();</script>");
		exit;
	}

	$id    = trim($id);
	$pw  = trim($pw);
	$name  = trim($name);
	$nick_name  = trim($nick_name);
	//$mail  = trim($mail1."@".$mail2);
	$addr1 = trim($addr1);
	$addr2 = trim($addr2);
	$comp  = trim($comp);
	$class = trim($class);
	$birthday=$birthday1."-".$birthday2."-".$birthday3;
	$zip   = "$zipcode1-$zipcode2";
	$tel = trim($tel);
	$pcs = trim($pcs);
	//$tel   = "$tel1-$tel2-$tel3";
	//$pcs   = "$pcs1-$pcs2-$pcs3";
	$code  = md5(uniqid(rand()));



	if(!isset($department)){
		$department = "0";
	}

	if(!isset($position)){
		$position = "0";
	}

	$gp_ix = "1";
	if($db->dbms_type == "oracle"){
		$sql = "INSERT INTO ".TBL_COMMON_USER."
						(code, id, pw, mem_type, language, company_id, date_, visit, last, ip, auth,authorized)
						VALUES
						('$code','$id','".hash("sha256", $pw)."','S','".$language_type."','".$company_id."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."', '".$auth."','".$authorized."')";
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_USER."
						(code, id, pw, mem_type, language, company_id, date, visit, last, ip, auth,authorized)
						VALUES
						('$code','$id','".hash("sha256", $pw)."','S','".$language_type."','".$company_id."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."', '".$auth."','".$authorized."')";
	}

	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code, name, mail, tel, pcs, date_, recom_id, gp_ix)
					VALUES
					('$code',AES_ENCRYPT('$name','".$db->ase_encrypt_key."'),AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'),AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'),NOW(),'".$admininfo[charger_id]."','$gp_ix')";
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, name, mail, tel, pcs, date, recom_id, gp_ix)
						VALUES
						('$code',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),NOW(),'".$admininfo[charger_id]."','$gp_ix')";
	}

	$db->query($sql);

	admin_log("C",$id,$company_id);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('셀러가 정상적으로 등록되었습니다.');parent.document.location.reload();</script>");

}

if($act == "user_update"){

	admin_log("U",$b_id,$company_id);


	$tel = trim($tel);
	$pcs = trim($pcs);

	if($change_pass){
		$update_pass_str = ", pw= '".hash("sha256", $pw)."'";
	}

	if(trim($charger_id) != trim($bcharger_id)){
		$db->query("select * from ".TBL_COMMON_USER."  where company_id='".trim($company_id)."' and id='".trim($id)."' ");

		if($db->total){
			//echo "<script language='javascript'>alert('$charger_id 아이디는 이미 사용중입니다.');</script>";
			echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$charger_id] ' + language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 사용자 입니다.
			exit;
		}

		$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id = '$id' ");

		if ($db->total)
		{
			//echo("<script>alert('[$id] 이미 등록된 아이디 입니다. ');</script>");
			echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$id] ' + language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 아이디 입니다.
			//echo("<script>history.back();</script>");
			exit;
		}
	}

	if(!isset($department)){
		$department = "0";
	}

	if(!isset($position)){
		$position = "0";
	}

	$sql = "UPDATE ".TBL_COMMON_USER." SET
			id='$id' , language = '$language_type',authorized = '$authorized', auth = '$auth' $update_pass_str
			WHERE code='$code'";

	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET
				mail= AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'), tel=AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),pcs=AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'), name = AES_ENCRYPT('$name','".$db->ase_encrypt_key."') , department = '$department' , position = '$position'
				WHERE code='$code'";
	}else{
		$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET
				mail= HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')), tel=HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),pcs=HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')), name = HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')) , department = '$department' , position = '$position'
				WHERE code='$code'";
	}

	//echo $sql;
	//exit	;
	$db->query($sql);

	//변경정보와 로그인 아이디가 같으면 랭귀지 변경정보를 세션에 반영한다.
	if($admininfo[charger_ix] == $code){
		$admininfo["language"] = $language_type;
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('관리자가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

if($act == "user_delete"){

	admin_log("D",$id,$company_id);

	$db->query("SELECT code, company_id FROM ".TBL_COMMON_USER." WHERE company_id = '$company_id' and code = '$code' ");
	$db->fetch();
	$code = $db->dt[code];

	$sql = "delete from ".TBL_COMMON_USER." where company_id ='$company_id' and code = '$code'";

	$db->query($sql);

	$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where  code = '$code'";

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('입점업체 사용자 정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
	//echo("<script>location.href = 'company_list.php';</script>");
}

if($act == "admin_log")
{
	admin_log("R",$charger_id,$company_id);
}

function admin_log($crud_div,$id,$company_id)
{
	global $admininfo;

	$mdb = new Database;


	if($mdb->dbms_type == "oracle"){
		$sql = "select ccd.com_name, AES_DECRYPT(cmd.name,'".$mdb->ase_encrypt_key."') as name
			from common_user cu, common_member_detail cmd ,  common_company_detail ccd
			where cu.code = cmd.code and cu.company_id = ccd.company_id
			and cu.company_id = '$company_id'
			and cu.id = '$id'";
			//echo $sql;
	}else{
		$sql = "select ccd.com_name, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name
			from common_user cu, common_member_detail cmd ,  common_company_detail ccd
			where cu.code = cmd.code and cu.company_id = ccd.company_id
			and cu.company_id = '$company_id'
			and cu.id = '$id'";
	}

	$mdb->query($sql);
	$mdb->fetch();


	$sql = "insert into admin_log(log_ix,accept_com_name,accept_m_name,admin_id,admin_name,crud_div,ip,regdate) values('','".$mdb->dt[com_name]."','".$mdb->dt[name]."','".$admininfo['charger_id']."','".$admininfo['charger']."','$crud_div','".$_SERVER["REMOTE_ADDR"]."',NOW())";
	$mdb->sequences = "ADMIN_LOG_SEQ";
	$mdb->query($sql);

}


function checkMyService($service_type,$solution_type, $service_use_value = ""){
	$service_mall_type = array("H","F","R","B");
	if(!in_array($_SESSION["admininfo"]["mall_type"],$service_mall_type)){
		//return true;
	}
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
	$shmop = new Shared("myservice_info");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$myservice_info = $shmop->getObjectForKey("myservice_info");
	$myservice_info = unserialize(urldecode($myservice_info));
	//echo $solution_type;
	//$__service_info = eval("\$myservice_info->".$service_type);
	$_service_info = (array)$myservice_info[$service_type];
	$myservice_info = (array)$_service_info[$solution_type];
	//print_r($myservice_info);
	//return $myservice_info->$service_type->$solution_type->si_status;
	//echo $myservice_info[si_status];
	//echo $myservice_info[service_unit_value];
	if($myservice_info[si_status] == "SI" && $myservice_info[sm_edate] >= date("Y-m-d")){
		if($service_use_value != ""){
			//echo $myservice_info[service_unit_value];
			if($myservice_info[service_unit_value] > $service_use_value){
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}else{
		return false;
	}
	//return $myservice_info;
}

?>
