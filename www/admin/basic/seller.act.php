<?
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("./company.lib.php");

$db = new Database;
$db2= new Database;

if ($act == "insert"){

	if($info_type == "basic"){
		
		$company_id  = md5(uniqid(rand()));		//company_id 생성
		$company_code  = trim($_REQUEST[company_code]);	//본사코드
		$open_date  = trim($_REQUEST[open_date]);			//설립일
		$com_name  = trim($_REQUEST[com_name]);			//설립일
		$seller_level  = trim($_REQUEST[seller_level]);			//거래처등급
		$com_ceo  = trim($_REQUEST[com_ceo]);			//대표자명
		$com_div  = trim($_REQUEST[com_div]);			//사업자 유형 R:법인사업자 P:일반사업자 S:간이과세자 E:면세사업자
		$business_type  = trim($_REQUEST[business_type]);			//사업 유형  O : 온라인 F:오프라인 A:온+오프라인
		$com_number  = trim($_REQUEST[com_number_1])."-".trim($_REQUEST[com_number_2])."-".trim($_REQUEST[com_number_3]);			//사업자번호
		$corporate_number  = trim($_REQUEST[corporate_number_1])."-".trim($_REQUEST[corporate_number_2]);							//법인번호
		$com_business_status  = trim($_REQUEST[com_business_status]);				//업태
		$com_business_category  = trim($_REQUEST[com_business_category]);			//업종
		$com_zip  = trim($_REQUEST[com_zip]);				//우편번호
		$com_addr1  = trim($_REQUEST[com_addr1]);			//주소1
		$com_addr2  = trim($_REQUEST[com_addr2]);			//주소2
		$com_phone  = trim($_REQUEST[com_phone_1])."-".trim($_REQUEST[com_phone_2])."-".trim($_REQUEST[com_phone_3]);			//대표번호
		$com_mobile  = trim($_REQUEST[com_mobile_1])."-".trim($_REQUEST[com_mobile_2])."-".trim($_REQUEST[com_mobile_3]);		//대표핸드폰번호
		$com_fax  = trim($_REQUEST[com_fax]);				//대표팩스번호
		$com_homepage  = trim($_REQUEST[com_homepage]);		//홈페이지
		$com_email  = trim($_REQUEST[com_email]);			//이메일
		$online_business_number  = trim($_REQUEST[online_business_number]);			//통신판매업 번호
		$seller_auth  = trim($_REQUEST[seller_auth]);			//본사등록승인 여부 N:승인대기 Y : 승인 X : 승은거부
		$info_type	= trim($_REQUEST[info_type]);				// 입력구분값
		
		$com_type  = trim($_REQUEST[com_type]);					// 입력구분값
		$shipping_yn = trim($_REQUEST[shipping_yn]);			// 입력구분값
		$relation_code = trim($_REQUEST[cid2]);					// 선택한상위 본사 코드
		
		//seller_detail_st
		$seller_date = trim($_REQUEST[seller_date]); //거래시작일
		$seller_division = trim($_REQUEST[seller_division]); //거래처구분
		$nationality = trim($_REQUEST[nationality]);	//국내외구분

		//$seller_type_array = array('sales_vendor'=>$sales_vendor,'supply_vendor'=>$supply_vendor,'oversea_sales'=>$oversea_sales,'oversea_supply'=>$oversea_supply,'outsourcing'=>$outsourcing);
		//$seller_type = serialize($seller_type_array);	//거래처 유형

		//$seller_person_position =  trim($_REQUEST[seller_type]);	//거래처 직급
		$seller_message =  trim($_REQUEST[seller_message]);	//거래처 기타사항

		$loan_price =  trim($_REQUEST[loan_price]);	//거래처 여신한도
		$deposit_price =  trim($_REQUEST[deposit_price]);	//거래처 보증금
		$shop_name = trim($_REQUEST[shop_name]);	//거래처 상점명(상호명)
		$md_code = trim($_REQUEST[md_code]);	//담당 MD
		$team = trim($_REQUEST[team]);	//담당팀
		$is_wharehouse = trim($_REQUEST[is_wharehouse]);	//물류창고 사용여부
		$person = trim($_REQUEST[com_person]);	//담당팀

		$virtual_bank = trim($_REQUEST[virtual_bank]);	//지정가상계좌은행
		$virtual_bank_number = trim($_REQUEST[virtual_bank_number]);	//지정가상계좌번호

		if(is_array($sell_type)){	//거래처 유형
			foreach($sell_type as $key=>$value){
				if($value && $key == '0'){
					$seller_type = $value;
				}else{
					$seller_type .= "|".$value;
				}
			}
		}

		//$custseq = get_custseq('14');		//ERP 에 넘겨줄 거래처 시스템 코드값은 14로 시작되는 7자리 숫자여서 몰에서 조합하여 제작한다.

		// 거래처 com_type 은 G 로 통일 한다. 거래처 + 사업자 회원 전부 G
		$sql = "
				insert into ".TBL_COMMON_COMPANY_DETAIL."  set
					company_id = '".$company_id."',
					com_type = 'G',
					open_date = '".$seller_date."',
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
					person = '".$com_person."',
					online_business_number = '".$online_business_number."',
					seller_auth = '".$seller_auth."',
					shipping_yn = '".$shipping_yn."',
					is_wharehouse = '".$is_wharehouse."',";
			
			if($_SESSION["admininfo"]["mallstory_version"] != "service"){
				
			$sql .= "is_erp_link = 'N',
					inputtype = 'A',
					custseq = '".$custseq."',
					";
			}

				$sql .= "
					virtual_bank = '".$virtual_bank."',
					virtual_bank_number = '".$virtual_bank_number."',
					loan_price = '".$loan_price."',
					regdate = NOW();";
		
		$db->query($sql);
		//$insert_id = $db->insert_id();
		
		if($_SESSION["admininfo"]["mallstory_version"] != "service"){
			$company_ix = $db->insert_id();
			$db->query("update ".TBL_COMMON_COMPANY_DETAIL." set custseq = '".$company_ix."' where company_ix = '".$company_ix."'");
		}
		$seller_sql = "insert into ".TBL_COMMON_SELLER_DETAIL." set
							company_id = '".$company_id."',
							shop_name = '".$com_name."',
							seller_level = '".$seller_level."',
							seller_date = '".$seller_date."',
							seller_division = '".$seller_division."',
							nationality = '".$nationality."',
							seller_message = '".$seller_message."',
							
							deposit_price = '".$deposit_price."',
							authorized = '".$authorized."',
							md_code = '".$md_code."',
							team = '".$team."',
							regdate =NOW();";
		$db->query($seller_sql);

		// 이미지 저장 시작 
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;
		if(!is_dir($path)){
			//mkdir($path, 0777);
			exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
		}
		if ($stamp_file_size > 0){
			copy($stamp_file,$path."/"."company_stamp_".$company_id.".gif");
			//$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif";
		}
		// 이미지 저장 끝
		
		//트리코드 생성 시작 
		$seq	= check_seq($relation_code,$depth);
		$new_code = check_relation($relation_code,$depth);
		//트리코드 생성 끝

$sql_relation = "insert into 
					".TBL_COMMON_COMPANY_RELATION." set
				company_id = '".$company_id."',
				relation_code = '".$new_code."',
				seq = '".$seq."',
				reg_date = NOW();";
		$db->query($sql_relation);


		//도매상권관련 추가 2014-02-18 HONG 
		$ws_tel  = trim("$ws_tel_1-$ws_tel_2-$ws_tel_3");
		$ws_charge_phone  = trim("$ws_charge_phone_1-$ws_charge_phone_2-$ws_charge_phone_3");
		$kakao_phone  = trim("$kakao_phone_1-$kakao_phone_2-$kakao_phone_3");

		$sql = "insert into common_company_wholesale (company_id,commercial_disp,ca_country,ca_code,sc_code,floor,line,no,tel,charge_phone,kakao_phone,kakao_id,facebook,twitter,qq,wechat,regdate) values('$company_id','$commercial_disp','$ca_country','$ca_code','$sc_code','$floor','$line','$no','$ws_tel','$ws_charge_phone','$kakao_phone','$kakao_id','$facebook','$twitter','$qq','$wechat',NOW()) ";
		$db->query($sql);

/*
		if($_REQUEST[sales_vendor] == "1"){

				$customer_phone = $customer_phone1_su."-".$customer_phone2_su."-".$customer_phone3_su;
				$customer_fax = $customer_fax1_su."-".$customer_fax2_su."-".$customer_fax3_su;

				$com_phone = $com_phone1_su."-".$com_phone2_su."-".$com_phone3_su;
				$com_fax = $com_fax1_su."-".$com_fax2_su."-".$com_fax3_su;
				

			$sql = "insert into inventory_customer_info
			(customer_type,customer_div,company_id,customer_name,customer_position,storage_fee,customer_phone,customer_fax,customer_msg,regdate) values
			('D','$customer_div_su','$company_id','$customer_name_su','$customer_position_su','$storage_fee_su','$customer_phone','$customer_fax','$customer_msg_su',NOW())";

			$db->query($sql);
		
		}
		
		if($_REQUEST[supply_vendor] == "2"){

				$customer_phone = $customer_phone1."-".$customer_phone2."-".$customer_phone3;
				$customer_fax = $customer_fax1."-".$customer_fax2."-".$customer_fax3;

				$com_phone = $com_phone1."-".$com_phone2."-".$com_phone3;
				$com_fax = $com_fax1."-".$com_fax2."-".$com_fax3;
				$com_zip = $com_zip1."-".$com_zip2;

				$charger_phone = $charger_phone1."-".$charger_phone2."-".$charger_phone3;
				$charger_mobile = $charger_mobile1."-".$charger_mobile2."-".$charger_mobile3;

			$sql = "insert into inventory_customer_info
			(customer_type,customer_div,company_id,customer_name,customer_position,storage_fee,customer_phone,customer_fax,customer_msg,regdate) values
			('E','$customer_div','$company_id','$customer_name','$customer_position','$storage_fee','$customer_phone','$customer_fax','$customer_msg',NOW())";

			$db->query($sql);
		
		}
*/
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'seller.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
		
	}else if($info_type == "seller_info"){
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
					reg_date = NOW();";

		$db->query($info_sql);

	}else{

		$company_id = trim($_REQUEST[company_id]);	//company_id
		$delivery_type = trim($_REQUEST[delivery_type]);	//주소 타입 F 출고지 E 반품 교환지 V 방문수령지
		$mall_ix = trim($_REQUEST[mall_ix]);	// 몰 아이디 
		$addr_name = trim($_REQUEST[addr_name]);	// 출고지명
		$person_name = trim($_REQUEST[person_name]);	// 담당자명
		$addr_phone = trim($_REQUEST[addr_phone_1]."-".$_REQUEST[addr_phone_2]."-".$_REQUEST[addr_phone_3]);	// 전화번호 
		$addr_mobile = trim($_REQUEST[addr_mobile_1]."-".$_REQUEST[addr_mobile_2]."-".$_REQUEST[addr_mobile_3]);	// 전화번호 
		$zip_code = trim($_REQUEST[com_zip]);	// zip 코드

		$com_addr1 = trim($_REQUEST[com_addr1]);	// 주소1
		$com_addr2 = trim($_REQUEST[com_addr2]);	// 주소2

		$basic_addr_use = trim($_REQUEST[basic_addr_use]);	// 기본주소 사용여부
		$code = trim($_REQUEST[code]);	// 코드
		
		if($basic_addr_use == "Y"){
		
			$sql = "select
						addr_ix
					from
						shop_delivery_address
					where
						basic_addr_use = 'Y'
						and mall_ix = '$mall_ix'
						and company_id = '$company_id'
						and delivery_type = '$delivery_type'
			";
			$db2->query($sql);
			$db2->fetch();
			$addr_ix = $db2->dt[addr_ix];

			$up_sql = "
				update shop_delivery_address set
					basic_addr_use = 'N'
				where
					addr_ix = '$addr_ix'
			";

			//echo "$up_sql";exit;
			$db2->query($up_sql);
		}

		$sql = "insert into shop_delivery_address set
					company_id = '$company_id',
					delivery_type = '$delivery_type',
					mall_ix = '$mall_ix',
					addr_name = '$addr_name',
					person_name = '$person_name',
					addr_phone = '$addr_phone',
					addr_mobile = '$addr_mobile',
					zip_code = '$zip_code',
					address_1 = '$com_addr1',
					address_2 = '$com_addr2',
					basic_addr_use = '$basic_addr_use',
					code = '$code',
					regdate = NOW()
		";
		
		$db->query($sql);
	}

	if($admininfo[admin_level] == 9){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'seller.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
	}else if($admininfo[admin_level] == 8){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'seller.add.php?mmode=".$mmode."&company_id=".$admininfo[company_id]."';</script>");
	}
}

if ($act == "update"){	

	$company_code  = trim($_REQUEST[company_code]);	//본사코드
	$open_date  = trim($_REQUEST[open_date]);			//설립일
	$com_name  = trim($_REQUEST[com_name]);			//설립일
	$com_ceo  = trim($_REQUEST[com_ceo]);			//대표자명
	$com_div  = trim($_REQUEST[com_div]);			//사업자 유형 R:법인사업자 P:일반사업자 S:간이과세자 E:면세사업자
	$business_type  = trim($_REQUEST[business_type]);			//사업 유형  O : 온라인 F:오프라인 A:온+오프라인
	$com_number  = trim($_REQUEST[com_number_1])."-".trim($_REQUEST[com_number_2])."-".trim($_REQUEST[com_number_3]);			//사업자번호
	$corporate_number  = trim($_REQUEST[corporate_number_1])."-".trim($_REQUEST[corporate_number_2]);			//법인번호
	$com_business_status  = trim($_REQUEST[com_business_status]);			//업태
	$com_business_category  = trim($_REQUEST[com_business_category]);			//업종
	$com_zip  = trim($_REQUEST[com_zip]);			//우편번호
	$com_addr1  = trim($_REQUEST[com_addr1]);			//주소1
	$com_addr2  = trim($_REQUEST[com_addr2]);			//주소2
	$com_phone  = trim($_REQUEST[com_phone_1])."-".trim($_REQUEST[com_phone_2])."-".trim($_REQUEST[com_phone_3]);			//대표번호
	$com_mobile  = trim($_REQUEST[com_mobile_1])."-".trim($_REQUEST[com_mobile_2])."-".trim($_REQUEST[com_mobile_3]);		//대표핸드폰번호
	$com_fax  = trim($_REQUEST[com_fax]);			//대표팩스번호
	$com_homepage  = trim($_REQUEST[com_homepage]);			//홈페이지
	$com_email  = trim($_REQUEST[com_email]);			//이메일
	$online_business_number  = trim($_REQUEST[online_business_number]);			//통신판매업 번호
	$seller_auth  = trim($_REQUEST[seller_auth]);			//본사등록승인 여부 N:승인대기 Y : 승인 X : 승은거부
	$info_type	= trim($_REQUEST[info_type]); // 입력구분값
	$seller_level  = trim($_REQUEST[seller_level]);			//거래처등급

	//$com_type  = trim($_REQUEST[com_type]); // 입력구분값
	$shipping_yn = trim($_REQUEST[shipping_yn]); // 입력구분값

	$relation_code = trim($_REQUEST[cid2]);	 // 선택한상위 본사 코드

	//seller_detail_st
	$seller_date = trim($_REQUEST[seller_date]); //거래시작일
	$seller_division = trim($_REQUEST[seller_division]); //거래처구분
	$nationality = trim($_REQUEST[nationality]);	//국내외구분
	//$seller_person_position =  trim($_REQUEST[seller_type]);	//거래처 직급
	$seller_message =  trim($_REQUEST[seller_message]);	//거래처 기타사항
	$loan_price =  trim($_REQUEST[loan_price]);	//거래처 여신한도
	$deposit_price =  trim($_REQUEST[deposit_price]);	//거래처 보증금
	$shop_name = trim($_REQUEST[shop_name]);	//거래처 상점명(상호명)
	$md_code = trim($_REQUEST[md_code]);	//담당 MD
	$team = trim($_REQUEST[team]);	//담당팀
	$person = trim($_REQUEST[com_person]);	//담당팀

	$virtual_bank = trim($_REQUEST[virtual_bank]);	//지정가상계좌은행
	$virtual_bank_number = trim($_REQUEST[virtual_bank_number]);	//지정가상계좌번호

	if(is_array($sell_type)){	//거래처 유형
		foreach($sell_type as $key=>$value){
			if($value && $key == '0'){
				$seller_type = $value;
			}else{
				$seller_type .= "|".$value;
			}
		}
	}

	$company_id = trim($_REQUEST[company_id_2]);

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

		if($com_type != ""){
			$com_type = $com_type;
		}else{
			$com_type = 'G';
		}

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
					com_phone='$com_phone',
					com_fax='$com_fax',
					com_email='$com_email',
					com_zip='$com_zip',
					com_addr1='$com_addr1', 
					com_addr2='$com_addr2',
					person = '$com_person',
					seller_type = '$seller_type',
					seller_auth='$seller_auth',
					shipping_yn = '$shipping_yn',
					virtual_bank = '$virtual_bank',
					virtual_bank_number = '$virtual_bank_number',
					is_wharehouse = '$is_wharehouse',
					loan_price = '".$loan_price."'";

					if($_SESSION["admininfo"]["mallstory_version"] != "service"){
						$sql .= ",
						is_erp_link = 'N',
						inputtype = 'U'
						";
					}
$sql .= "		WHERE 
					company_id='$company_id'"; 
		$db->query($sql);
		
		//seller_person_position = '".$seller_person_position."',
		$seller_sql = "
			update ".TBL_COMMON_SELLER_DETAIL." set
				shop_name = '".$com_name."',
				seller_date = '".$seller_date."',
				seller_division = '".$seller_division."',
				nationality = '".$nationality."',
				seller_message = '".$seller_message."',
				seller_level  = '".$seller_level."',
				deposit_price = '".$deposit_price."',
				authorized = '".$authorized."',
				md_code = '".$md_code."',
				team = '".$team."',
				edit_date =NOW()
			where
				company_id = '".$company_id."'
		";
		
		//echo "$seller_sql";
		$db->query($seller_sql);

		//거래처별 연결코드 추가 시작 2014-06-13 이학봉
		$seq	= check_seq($relation_code,$depth);
		$new_code = check_relation($relation_code,$depth);

		$sql = "select * from ".TBL_COMMON_COMPANY_RELATION." where relation_code = '".$relation_code."' and company_id = '".$company_id."'";
		$db->query($sql);
		
		if($db->total < 1){//넘어온 relation_code가 현재코드랑 다를때엔 업데트
			$sql = "update ".TBL_COMMON_COMPANY_RELATION." set
						relation_code = '".$new_code."',
						seq = '".$seq."',
						edit_date = NOW()
					where
						company_id = '".$company_id."'";
			$db->query($sql);
		}
		//거래처별 연결코드 추가 끝 2014-06-13 이학봉


		//도매상권관련 추가 2014-02-18 HONG 
		$ws_tel  = trim("$ws_tel_1-$ws_tel_2-$ws_tel_3");
		$ws_charge_phone  = trim("$ws_charge_phone_1-$ws_charge_phone_2-$ws_charge_phone_3");
		$kakao_phone  = trim("$kakao_phone_1-$kakao_phone_2-$kakao_phone_3");

		$sql = "select * from common_company_wholesale where company_id ='$company_id'";
		$db->query($sql);
		if(!$db->total){
			$sql = "insert into common_company_wholesale (company_id,commercial_disp,ca_country,ca_code,sc_code,floor,line,no,tel,charge_phone,kakao_phone,kakao_id,facebook,twitter,qq,wechat,regdate) values('$company_id','$commercial_disp','$ca_country','$ca_code','$sc_code','$floor','$line','$no','$ws_tel','$ws_charge_phone','$kakao_phone','$kakao_id','$facebook','$twitter','$qq','$wechat',NOW()) ";
			$db->query($sql);
		}else{
			$sql = "update common_company_wholesale set
						commercial_disp='".$commercial_disp."',
						ca_country='".$ca_country."',
						ca_code='".$ca_code."',
						sc_code='".$sc_code."',
						floor='".$floor."',
						line='".$line."',
						no='".$no."',
						tel='".$ws_tel."',
						charge_phone='".$ws_charge_phone."',
						kakao_phone='".$kakao_phone."',
						kakao_id='".$kakao_id."',
						facebook='".$facebook."',
						twitter='".$twitter."',
						qq='".$qq."',
						wechat='".$wechat."'
					where company_id='$company_id' ";
			$db->query($sql);
		}

		// 이미지 저장 시작 
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;
		if(!is_dir($path)){
			//mkdir($path, 0777);
			exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
		}
		if ($stamp_file_size > 0){
			copy($stamp_file,$path."/"."company_stamp_".$company_id.".gif");
			//$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif";
		}
		// 이미지 저장 끝

	}else if($info_type == "order_info"){
	
		$customer_name = trim($_REQUEST[customer_name]);	//거래처 담당자명
		$customer_phone = trim($_REQUEST[customer_phone]);	//거래처 전화
		$customer_mobile = trim($_REQUEST[customer_mobile]);	//거래처 핸드폰
		$customer_mail = trim($_REQUEST[customer_mail]);	//거래처 메일
		$customer_position = trim($_REQUEST[customer_position]);	//거래처 직급/직책
		$customer_message = trim($_REQUEST[customer_message]);	//거래처 담당자 메세지

		$tax_person_name = trim($_REQUEST[tax_person_name]);	//세무 담당자명
		$tax_person_phone = trim($_REQUEST[tax_person_phone]);	//세무 담당자 전화
		$tax_person_mobile = trim($_REQUEST[tax_person_mobile]);	//세무 담당자 핸드폰번호
		$tax_person_mail = trim($_REQUEST[tax_person_mail]);	//세무 담당자 메일
		$tax_person_position = trim($_REQUEST[tax_person_position]);	//세무 담당자 직책/직급
		$tax_person_message = trim($_REQUEST[tax_person_message]);	//세무 담당자 기타사항

		$tax_mail = trim($_REQUEST[tax_mail]);	//세무 담당자 기타사항
		$basic_bank  = trim($_REQUEST[basic_bank ]);	//세무 담당자 기타사항
		$holder_name = trim($_REQUEST[holder_name]);	//세무 담당자 기타사항
		$bank_num = trim($_REQUEST[bank_num]);	//세무 담당자 기타사항

		$sql = "
				update ".TBL_COMMON_COMPANY_DETAIL." set
					customer_name = '".$customer_name."',
					customer_phone = '".$customer_phone."',
					customer_mobile = '".$customer_mobile."',
					customer_mail = '".$customer_mail."',
					customer_position = '".$customer_position."',
					customer_message = '".$customer_message."',

					tax_person_name = '".$tax_person_name."',
					tax_person_phone = '".$tax_person_phone."',
					tax_person_mobile = '".$tax_person_mobile."',
					tax_person_mail = '".$tax_person_mail."',
					tax_person_position = '".$tax_person_position."',
					tax_person_message = '".$tax_person_message."',

					tax_mail = '".$tax_mail."',
					basic_bank = '".$basic_bank."',
					holder_name = '".$holder_name."',
					bank_num = '".$bank_num."'
				where
					company_id = '".$company_id."'
		";

		$db->query($sql);


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


	}else{

			$company_id = trim($_REQUEST[company_id]);	//company_id
			$delivery_type = trim($_REQUEST[delivery_type]);	//주소 타입 F 출고지 E 반품 교환지 V 방문수령지
			$mall_ix = trim($_REQUEST[mall_ix]);	// 몰 아이디 
			$addr_name = trim($_REQUEST[addr_name]);	// 출고지명
			$person_name = trim($_REQUEST[person_name]);	// 담당자명
			$addr_phone = trim($_REQUEST[addr_phone_1]."-".$_REQUEST[addr_phone_2]."-".$_REQUEST[addr_phone_3]);	// 전화번호 
			$addr_mobile = trim($_REQUEST[addr_mobile_1]."-".$_REQUEST[addr_mobile_2]."-".$_REQUEST[addr_mobile_3]);	// 전화번호 
			$zip_code = trim($_REQUEST[com_zip]);	// zip 코드

			$com_addr1 = trim($_REQUEST[com_addr1]);	// 주소1
			$com_addr2 = trim($_REQUEST[com_addr2]);	// 주소2

			$basic_addr_use = trim($_REQUEST[basic_addr_use]);	// 기본주소 사용여부
			$code = trim($_REQUEST[code]);	// 코드
			
			if($basic_addr_use == "Y"){
			
				$sql = "
						select
							addr_ix
						from
							shop_delivery_address
						where
							basic_addr_use = 'Y'
							and mall_ix = '$mall_ix'
							and company_id = '$company_id'
							and delivery_type = '$delivery_type'
				";
				$db2->query($sql);
				$db2->fetch();
				$addr_ix = $db2->dt[addr_ix];

				$up_sql = "
					update shop_delivery_address set
						basic_addr_use = 'N'
					where
						addr_ix = '$addr_ix'
				";

				//echo "$up_sql";exit;
				$db2->query($up_sql);
			}

		$sql = "
				update shop_delivery_address set
					delivery_type = '$delivery_type',
					addr_name = '$addr_name',
					person_name = '$person_name',
					addr_phone = '$addr_phone',
					addr_mobile = '$addr_mobile',
					zip_code = '$zip_code',
					address_1 = '$com_addr1',
					address_2 = '$com_addr2',
					basic_addr_use = '$basic_addr_use',
					code = '$code',
					editdate = NOW()
				where
					addr_ix = '".$_REQUEST[addr_ix]."'
		";
		
		$db->query($sql);
	
	}

	if($admininfo[admin_level] == 9){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'seller.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
	}else if($admininfo[admin_level] == 8){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'seller.add.php?mmode=".$mmode."&company_id=".$admininfo[company_id]."';</script>");
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
	
	$sql = "select
			count(*) as cnt
			from
				common_company_detail as ccd 
				left join common_user as cu on (ccd.company_id = cu.company_id)
				left join common_member_detail as cmd on (cu.code = cmd.code) 
				left join shop_product as product on (product.admin = ccd.company_id) 
				left join inventory_goods as goods on (goods.ci_ix = ccd.company_id) 
				left join inventory_place_info as ipi on (ccd.company_id = ipi.company_id)
			where
				cu.company_id = '".$company_id."'
	";
	$db->query($sql);
	$db->fetch();
	$member_cnt = $db->dt[cnt];

	if($member_cnt > 0){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('하위 사업장이나 사원이 존재합니다. ');</script>");
		//echo("<script>parent.document.location.href = 'company.list.php';</script>");
		//echo("<script>top.document.location.reload();</script>");
		exit;
	}

	
	$sql = "delete from ".TBL_COMMON_COMPANY_DETAIL." where company_id ='$company_id'";
	//echo $sql;
	$db->query($sql);

	$sql = "delete from ".TBL_COMMON_SELLER_DETAIL." where company_id ='$company_id'";
	//echo $sql;
	$db->query($sql);

	$sql = "delete from ".TBL_COMMON_COMPANY_RELATION." where company_id ='$company_id'";
	//echo $sql;
	$db->query($sql);	//거래처일 경우 seller_detail 도 삭제해야함

	//$sql = "delete from inventory_place_info where company_id ='$company_id'";
	//echo $sql;
	//$db->query($sql);	//입고 출고  inventory_customer_info 도 삭제해야함

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
	echo("<script>location.href = 'seller.list.php?mmode=".$mmode."&';</script>");
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

if($act == "delivery_delete"){
	admin_log("D",$id,$company_id);
	
	$sql = "delete from shop_delivery_address where  addr_ix = '$addr_ix'";

	$db->query($sql);

	if($admininfo[admin_level] == 9){
		echo("<script>parent.document.location.href = 'seller.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
	}else if($admininfo[admin_level] == 8){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'seller.add.php?mmode=".$mmode."&company_id=".$admininfo[company_id]."';</script>");
	}
}

if($act == "admin_log")
{
	admin_log("R",$charger_id,$company_id);
}

?>
