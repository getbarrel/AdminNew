<?php
	include("../../class/layout.class");
	include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
	include($_SERVER["DOCUMENT_ROOT"]."/admin/basic/company.lib.php");

	$db = new Database;
	$db2 = new Database;
	$db3 = new Database;

	if($act == "seller_insert"){
		if($info_type == "basic"){

            $com_type = 'S';
            $com_name = trim($com_name);
            if(is_array($sell_type)){
                $seller_type = implode('|',$sell_type);
            }else{
                $seller_type = "";
            }
            $com_number = $com_number_1."-".$com_number_2."-".$com_number_3;
            $corporate_number = $corporate_number_1."-".$corporate_number_2;
            $com_phone = $com_phone_1."-".$com_phone_2."-".$com_phone_3;
            $com_mobile = $com_mobile_1."-".$com_mobile_2."-".$com_mobile_3;

            $company_id  = md5(uniqid(rand()));
            $activation_code = md5(uniqid(rand()));
            $shop_company_id = $_SESSION[shopcfg][company_id];

            $sql="select company_id from ".TBL_COMMON_COMPANY_DETAIL." where com_number='".$com_number."'";
            $db->query($sql);
            if($db->total){
                echo("<script>alert('이미 등록된 사업자입니다.');</script>");
                exit;
            }

            $sql = "INSERT INTO ".TBL_COMMON_COMPANY_DETAIL."
                        (company_id,com_type,com_div, com_name,seller_type, com_ceo,com_email, com_business_status, com_business_category, online_business_number, com_number, com_phone,com_mobile, com_phone_div,com_sms,com_fax, com_zip, com_addr1, com_addr2, com_homepage,seller_auth,is_erp_link,inputtype,regdate,mail_info,corporate_number,is_wharehouse,loan_price)
                    VALUES
                        ('$company_id','$com_type','$com_div','$com_name','$seller_type', '$com_ceo','$com_email', '$com_business_status', '$com_business_category', '$online_business_number', '$com_number','$com_phone','$com_mobile','$com_phone_div','$com_sms','$com_fax', '$com_zip', '$com_addr1', '$com_addr2', '$com_homepage','$seller_auth','N','A',NOW(),'$mail_info','$corporate_number','$is_wharehouse','$loan_price')";
            $db->query($sql);

            $company_ix = $db->insert_id();
            //erp 매출 거래처 코드 1410001 번부터 시작된다 2013-07-22 선유도 이학봉
            $sql = "update ".TBL_COMMON_COMPANY_DETAIL." set custseq = '".$company_ix."' where company_ix = '".$company_ix."'";
            $db->query($sql);
            //erp 매출 거래처 코드 1410001 번부터 시작된다 2013-07-22 선유도 이학봉

            if($seller_auth == 'Y'){
                $seller_auth_sql = " , authorized_date = NOW()";
            }

            $sql = "INSERT INTO common_seller_detail SET
                        company_id = '$company_id',
                        shop_name = '$shop_name',
                        shop_name_linear = '" . linear_hangul($shop_name) . "',
                        md_code = '$md_code',
                        team = '$team',
                        authorized = '$seller_auth',
                        bank_owner = '$bank_owner',
                        bank_name = '$bank_name',
                        bank_number = '$bank_number',
                        seller_level = '$seller_level',
                        sg_ix = '$sg_ix',
                        seller_date = '$seller_date',
                        deposit_price = '$deposit_price',
                        seller_message = '".$seller_message."',
                        seller_msg = '".$seller_msg."',
                        topseller_display = ". ($topsellerDisplay == '' ? '0' : $topsellerDisplay ) .",
                        display_priority  = ". ($displayPriority == '' ? 'null' : $displayPriority) .",
                        regdate = now()
                        ".$seller_auth_sql;
            $db->query($sql);

            //트리코드 생성 시작	2013-06-26 이학봉 사업자 회원도 company_detail 에 입력되기에 relation_code 가 생성되어서 해당 쇼핑몰 관리업체 밑으로 들어가야함
            if($shop_company_id){	// 해당 쇼핑몰 세션 키값이 잇을경우에 생성	2013-10-07 이학봉 수정
                $sql = "select relation_code from	".TBL_COMMON_COMPANY_RELATION." where company_id = '".$shop_company_id."'";
                $db->query($sql);
                $db->fetch();
                $relation_code = $db->dt[relation_code];
                $seq = check_seq($relation_code,$depth);
                $new_code = check_relation($relation_code,$depth);

                $sql_relation = "
                        insert into 
                            ".TBL_COMMON_COMPANY_RELATION." set
                        company_id = '".$company_id."',
                        relation_code = '".$new_code."',
                        seq = '".$seq."',
                        reg_date = NOW()";
                $db->query($sql_relation);
                //트리코드 생성 끝
            }

            $mall_ix = $_SESSION["admininfo"][mall_ix];	//해당 사이트 mall_ix

            //셀러 기본정책 추가 시작 2014-04-07 이학봉
            $seller_config = getBasicSellerSetup('basic_seller_setup');	//셀러별 기본 수수료 설정 가져오기 (2014-04-07 이학봉) 위에서 설정했음2014-06-11
            $account_info = $seller_config[account_info];
            $ac_delivery_type = $seller_config[ac_delivery_type];
            $ac_expect_date = $seller_config[ac_expect_date];
            $ac_term_div = $seller_config[ac_term_div];
            $ac_term_date1 = $seller_config[ac_term_date1];
            $ac_term_date2 = $seller_config[ac_term_date2];

            $account_type = $seller_config[account_type];
            $account_method = $seller_config[account_method];		//정산 지급 방신 10: 현금 12: 예치금
            $wholesale_commission = $seller_config[wholesale_commission];
            $commission = $seller_config[commission];

            $seller_grant_use = $seller_config[seller_grant_use];
            $grant_setup_price = $seller_config[grant_setup_price];
            $ac_grant_price = $seller_config[ac_grant_price];
            $account_div = $seller_config[account_div];				//정산방식 유형 s:셀러 c:카테고리
            //셀러 기본정책 추가 끝 2014-04-07 이학봉

            $sql = "insert into common_seller_delivery set
                        company_id = '".$company_id."',
                        account_info = '".$account_info."',
                        ac_delivery_type = '".$ac_delivery_type."',
                        ac_expect_date = '".$ac_expect_date."',
                        ac_term_div = '".$ac_term_div."',
                        ac_term_date1 = '".$ac_term_date1."',
                        ac_term_date2 = '".$ac_term_date2."',
                        account_type = '".$account_type."',
                        wholesale_commission = '".$wholesale_commission."',
                        commission = '".$commission."',
                        seller_grant_use = '".$seller_grant_use."',
                        grant_setup_price = '".$grant_setup_price."',
                        ac_grant_price = '".$ac_grant_price."',
                        account_div = '".$account_div."'";
            $db->query($sql);

		}else if($info_type == 'order_info'){
			
			$customer_name = trim($_REQUEST[customer_name]);	//거래처 담당자명
			$customer_phone = trim($_REQUEST[customer_phone]);	//거래처 전화
			$customer_mobile = trim($_REQUEST[customer_mobile]);	//거래처 핸드폰
			$customer_mail = trim($_REQUEST[customer_mail]);	//거래처 메일
			$customer_position = trim($_REQUEST[customer_position]);	//거래처 직급/직책
			$customer_message = trim($_REQUEST[seller_message]);	//거래처 담당자 메세지

			$tax_person_name = trim($_REQUEST[tax_person_name]);	//세무 담당자명
			$tax_person_phone = trim($_REQUEST[tax_person_phone]);	//세무 담당자 전화
			$tax_person_mobile = trim($_REQUEST[tax_person_mobile]);	//세무 담당자 핸드폰번호
			$tax_person_mail = trim($_REQUEST[tax_person_mail]);	//세무 담당자 메일
			$tax_person_position = trim($_REQUEST[tax_person_position]);	//세무 담당자 직책/직급
			$tax_person_message = trim($_REQUEST[tax_seller_message]);	//세무 담당자 기타사항

			$tax_mail = trim($_REQUEST[tax_mail]);	//세무 담당자 기타사항
			$basic_bank  = trim($_REQUEST[basic_bank ]);	//세무 담당자 기타사항
			$holder_name = trim($_REQUEST[holder_name]);	//세무 담당자 기타사항
			$bank_num = trim($_REQUEST[bank_num]);	//세무 담당자 기타사항

			$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set
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
						tax_person_message = '".$tax_person_message."'

					where
						company_id = '".$company_id."'";
			$db->query($sql);

		}else if($info_type == 'seller_info'){

			$sql = "update common_seller_detail set
						shop_name='$shop_name',
						shop_name_linear = '" . linear_hangul($shop_name) . "',
						shop_desc='$shop_desc',
						homepage='$homepage',
						minishop_templet='$minishop_templet',
						minishop_use = '$minishop_use',
						bank_owner='$bank_owner',
						bank_name='$bank_name',
						bank_number='$bank_number',
						md_code='$md_code',
						team='$team',
						topseller_display = " . ($topsellerDisplay == '' ? '0' : $topsellerDisplay ) . ",
						display_priority  = " . ($displayPriority == '' ? 'null' : $displayPriority) . ",
						edit_date = NOW()
					where
						company_id='$company_id' ";

			$db->query($sql);

			$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/";
			if(!is_dir($path)){
				mkdir($path, 0777);
			}
			if ($shop_logo_img_size > 0){
				copy($shop_logo_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif");
				$shop_logo_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif";
			}

			if ($shop_img_size > 0){
				copy($shop_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif");
				$shop_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif";
			}

			if ($shop_img_thum_size > 0){
				copy($shop_img_thum, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif");
				$shop_img_thum_str = $admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif";
			}

			if ($shop_bg_size > 0){
				copy($shop_bg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_bg_".$company_id.".gif");
				$shop_bg_str = $admin_config[mall_data_root]."/images/shopimg/shop_bg_".$company_id.".gif";
			}
			
			$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images"."/minishop_banner/".$_POST['company_id']."/";
			if(!is_dir($path)){
				mkdir($path, 0777, true);
			}

			/* 2016 11 30 이후로 아래 삭제.
			   더 아래에 있는 코드가 진짜임.

			if(is_array($_FILES['file']['size'])){
				foreach($_FILES['file']['size'] as $_key=>$_val)	{
					if($_val > 0)	{
						$target = $path."/".$_FILES['file']['name'][$_key];
						copy($_FILES['file']['tmp_name'][$_key], $target );
						chmod($target, 0777);

						

						$sql = sprintf("insert into shop_minishop_banner 
						                      (company_id, title, link, file, banner_priority, regdate, tmp_update) 
											   values('%s', '%s', '%s', '%s', %d, NOW(),  '1')"
											   , $_POST['company_id']
											   , $_POST['title'][$_key]
											   , $_POST['link'][$_key]
											   , $_FILES['file']['name'][$_key]
											   , $_POST['banner_priority'][$_key]);
						$db->query($sql);
					}
				}
			}
			*/
            if(is_array($_FILES['file']['size'])){
                foreach($_FILES['file']['size'] as $_key=>$_val){
                    if($ix[$_key] != "" && $nondelete[$ix[$_key]] == 1){
                        $sql = "update shop_minishop_banner set tmp_update='1' where ix='".$ix[$_key]."' ";
                        $db->query($sql);
                    }
                    if($_val > 0)	{
                        $target = $path."/".$_FILES['file']['name'][$_key];
                        copy($_FILES['file']['tmp_name'][$_key], $target);
                        chmod($target, 0777);

                        if($ix[$_key] == ""){
                            $sql = sprintf("insert into shop_minishop_banner 
                                                  (company_id, title, link, file, banner_priority, regdate, tmp_update) 
                                                   values('%s', '%s', '%s', '%s', %d, NOW(),  '1')"
                                                   , $_POST['company_id']
                                                   , $_POST['title'][$_key]
                                                   , $_POST['link'][$_key]
                                                   , $_FILES['file']['name'][$_key]
                                                   , $_POST['bannerPriority'][$_key]);
                            $db->query($sql);
                            $lastIx = mysql_insert_id();
                            array_push($ix, $lastIx);
                        } else {
                            $sql = "update shop_minishop_banner set file='".$_FILES['file']['name'][$_key]."',link='".$link[$_key]."',title='".$title[$_key]."',banner_priority=".$bannerPriority[$_key].", tmp_update='1' where
                            ix='".$ix[$_key]."' ";
                            $db->query($sql);
                        }
                    }
                }
            }

            if(count($ix) > 0){
                $ix = array_filter($ix, create_function('$value', 'return $value !== "";'));
                $ixs = implode(", ", $ix);
                $sql = sprintf("DELETE FROM shop_minishop_banner WHERE company_id = '%s' AND ix not in (%s) ", $_POST['company_id'], $ixs);
                $db->query($sql);
            }
		}else if($info_type == 'tax_info'){

			$tax_mail = trim($_REQUEST[tax_mail]);	//세금계산서이메일
			$basic_bank = trim($_REQUEST[basic_bank]);	//거래처 은행
			$bank_num = trim($_REQUEST[bank_num]);	//계좌번호
			$holder_name = trim($_REQUEST[holder_name]);	//예금주
			
			$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set
						tax_mail = '".$tax_mail."',
						basic_bank = '".$basic_bank."',
						holder_name = '".$holder_name."',
						bank_num = '".$bank_num."'
					where
						company_id = '".$company_id."'";
			$db->query($sql);

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
							and delivery_type = '$delivery_type'";
				$db2->query($sql);
				$db2->fetch();
				$addr_ix = $db2->dt[addr_ix];

				$up_sql = "update shop_delivery_address set
								basic_addr_use = 'N'
							where
								addr_ix = '$addr_ix'";
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
						is_delivery_use = '".$is_delivery_use."',
						regdate = NOW()";
			$db->query($sql);
		
		}

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
		echo("<script>parent.document.location.href = '/admin/seller/company.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");

	}else if($act == "seller_updateinfo"){
		if($info_type == "basic"){
			$com_phone = $com_phone_1."-".$com_phone_2."-".$com_phone_3;
			$com_mobile = $com_mobile_1."-".$com_mobile_2."-".$com_mobile_3;
			
			if($_SESSION["admininfo"]["admin_level"] == '9' || $_SESSION["admininfo"]["admin_level"] == '8'){
				//미니샵 -> 셀러가 셀러 및 사업자정보 수정할수 있도록 $_SESSION["admininfo"]["admin_level"] == '8' 권한도 추가 2016.08.16

				if(is_array($sell_type)){	//거래처 유형
					foreach($sell_type as $key=>$value){
						if($value && $key == '0'){
							$seller_type_text = $value;
						}else{
							$seller_type_text .= "|".$value;
						}
					}
				}

				$com_number  = trim($_REQUEST[com_number_1])."-".trim($_REQUEST[com_number_2])."-".trim($_REQUEST[com_number_3]);			//사업자번호
				$corporate_number  = trim($_REQUEST[corporate_number_1])."-".trim($_REQUEST[corporate_number_2]);			//법인번호
				$com_phone  = trim($_REQUEST[com_phone_1])."-".trim($_REQUEST[com_phone_2])."-".trim($_REQUEST[com_phone_3]);			//대표번호
				$com_mobile  = trim($_REQUEST[com_mobile_1])."-".trim($_REQUEST[com_mobile_2])."-".trim($_REQUEST[com_mobile_3]);		//대표핸드폰번호

                if($_SESSION["admininfo"]["admin_level"] == '9'){
                    $where_company = ",
								company_code = '".$company_code."',
								seller_type = '".$seller_type_text."',
								is_wharehouse = '".$is_wharehouse."',
								com_name = '".$com_name."',
								com_ceo = '".$com_ceo."',
								com_number = '".$com_number."',
								corporate_number = '".$corporate_number."',
								com_business_status = '".$com_business_status."',
								com_business_category = '".$com_business_category."',
								com_div = '".$com_div."',
								com_phone = '".$com_phone."',
								com_mobile = '".$com_mobile."',
								com_email = '".$com_email."',
								com_homepage = '".$com_homepage."',
								com_zip = '".$com_zip."',
								com_addr1 = '".$com_addr1."',
								com_addr2 = '".$com_addr2."',
								seller_auth = '".$seller_auth."',
								loan_price = '".$loan_price."',
								halfclub_code = '".$halfclub_code."'";

                    $where_seller = " ,
								sg_ix = '".$sg_ix."',
								seller_cid = '".$seller_cid."',
								seller_brand = '".$b_ix."',
								seller_date = '".$seller_date."',
								seller_division = '".$seller_division."',
								nationality = '".$nationality."',
								deposit_price = '".$deposit_price."',
								seller_level = '".$seller_level."'
								";
                }else{
                    $where_company = ",
								company_code = '".$company_code."',
								seller_type = '".$seller_type_text."',
								is_wharehouse = '".$is_wharehouse."',
								com_name = '".$com_name."',
								com_ceo = '".$com_ceo."',
								com_number = '".$com_number."',
								corporate_number = '".$corporate_number."',
								com_business_status = '".$com_business_status."',
								com_business_category = '".$com_business_category."',
								com_div = '".$com_div."',
								com_phone = '".$com_phone."',
								com_mobile = '".$com_mobile."',
								com_email = '".$com_email."',
								com_homepage = '".$com_homepage."',
								com_zip = '".$com_zip."',
								com_addr1 = '".$com_addr1."',
								com_addr2 = '".$com_addr2."'";

                    $where_seller = " ,
								seller_date = '".$seller_date."',
								seller_division = '".$seller_division."',
								nationality = '".$nationality."'
								";
                }

				if($seller_auth == 'Y'){
					$where_seller .= " , authorized_date = NOW()"; 
				}
			}

			//셀러가 수정할수 있는 부분
			$sql = "update common_company_detail set
						com_phone = '".$com_phone."',
						com_mobile = '".$com_mobile."',
						com_email = '".$com_email."',
						com_homepage = '".$com_homepage."'
						$where_company
					where
						company_id = '".$company_id."'";
			$db->query($sql);

			//셀러가 수정할수 있는 부분
			$sql = "update common_seller_detail set
						seller_message = '".$seller_message."',
						seller_msg = '".$seller_msg."'
						$where_seller
					where
						company_id = '".$company_id."'";
			$db->query($sql);

			/*API 상품연동 대상업체 정보 입력*/
			if(is_array($api_get_product)){
				for($i=0; $i < count($api_get_product); $i++){
					$sql = "select * from sellertool_not_company where company_id = '".$company_id."' and site_code = '".$api_get_product[$i]."' ";
					$db->query($sql);
					if($db->total){
						$sql = "update sellertool_not_company set state = '1' where company_id = '".$company_id."' and site_code = '".$api_get_product[$i]."'";
						$db->query($sql);
					}else{
						$sql = "insert into sellertool_not_company (company_id,site_code,state) values ('".$company_id."','".$api_get_product[$i]."','1')";
						$db->query($sql);
					}
				}
				$sql = "select * from sellertool_not_company where company_id = '".$company_id."' and site_code not in ('".implode("','",$api_get_product)."') ";	
				$db->query($sql);
				if($db->total){
					for($i=0; $i < $db->total; $i++){
						$db->fetch($i);
						$sql = "update sellertool_not_company set state = '0' where company_id = '".$company_id."' and site_code = '".$db->dt[site_code]."'";
						$db2->query($sql);
					}
				}
			}else{
				$sql = "update sellertool_not_company set state = '0' where company_id = '".$company_id."' ";
				$db2->query($sql);
				
			}
			/*끝*/
			

			//2016-04-27 Hong API 연동 제외 했다가 다시 연동 시작할때 일괄 상품 데이터 처리
			foreach($api_get_product_befor as $site_code => $checked){
				//제외에서 사용을 푼경우
				if( $checked && ( empty($api_get_product) || ! in_array($site_code,$api_get_product) ) ){
					
					$sql="select p.id, sp.state, sp.sgp_ix
							from shop_product p left join sellertool_get_product sp on (p.id = sp.pid and site_code='".$site_code."') 
							where p.admin='".$company_id."'";
					$db->query($sql);
					if($db->total > 0){
						$lists = $db->fetchall("object");
						foreach($lists as $data){
							if( empty($data['sgp_ix']) ){
								$sql = "insert into sellertool_get_product (pid,site_code,state) values ('".$data['id']."','".$site_code."','1')";
							}elseif( $data['state'] !='1' ){
								$sql = "update sellertool_get_product set state = '1' where sgp_ix = '".$data['sgp_ix']."'";
							}else{
								continue;
							}
							$db->query($sql);
						}
					}
				}
			}
		}

		if($info_type == "order_info"){

			$customer_name = trim($_REQUEST[customer_name]);	//거래처 담당자명
			$customer_phone = trim($_REQUEST[customer_phone]);	//거래처 전화
			$customer_mobile = trim($_REQUEST[customer_mobile]);	//거래처 핸드폰
			$customer_mail = trim($_REQUEST[customer_mail]);	//거래처 메일
			$customer_position = trim($_REQUEST[customer_position]);	//거래처 직급/직책
			$customer_message = trim($_REQUEST[seller_message]);	//거래처 담당자 메세지

			$tax_person_name = trim($_REQUEST[tax_person_name]);	//세무 담당자명
			$tax_person_phone = trim($_REQUEST[tax_person_phone]);	//세무 담당자 전화
			$tax_person_mobile = trim($_REQUEST[tax_person_mobile]);	//세무 담당자 핸드폰번호
			$tax_person_mail = trim($_REQUEST[tax_person_mail]);	//세무 담당자 메일
			$tax_person_position = trim($_REQUEST[tax_person_position]);	//세무 담당자 직책/직급
			$tax_person_message = trim($_REQUEST[tax_seller_message]);	//세무 담당자 기타사항

			$tax_mail = trim($_REQUEST[tax_mail]);	//세무 담당자 기타사항
			$basic_bank  = trim($_REQUEST[basic_bank ]);	//세무 담당자 기타사항
			$holder_name = trim($_REQUEST[holder_name]);	//세무 담당자 기타사항
			$bank_num = trim($_REQUEST[bank_num]);	//세무 담당자 기타사항

			$cs_name = trim($_REQUEST[cs_name]);	//거래처 담당자명
			$cs_phone = trim($_REQUEST[cs_phone]);	//거래처 전화
			$cs_mobile = trim($_REQUEST[cs_mobile]);	//거래처 핸드폰
			$cs_mail = trim($_REQUEST[cs_mail]);	//거래처 메일
			$cs_position = trim($_REQUEST[cs_position]);	//거래처 직급/직책
			$cs_message = trim($_REQUEST[cs_message]);	//거래처 담당자 메세지

			$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set
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
						tax_person_message = '".$tax_person_message."'
					where
						company_id = '".$company_id."'";
			$db->query($sql);

			//common_company_detail 에 담당자 정보,세무자 정보등을 seller_detail 로 옮길 예정 2014-07-02 이학봉
			$sql = "update common_seller_detail set
						cs_name = '".$cs_name."',
						cs_phone = '".$cs_phone."',
						cs_mobile = '".$cs_mobile."',
						cs_mail = '".$cs_mail."',
						cs_position = '".$cs_position."',
						cs_message = '".$cs_message."',
						edit_date = NOW()
					where
						company_id='$company_id' ";
			$db->query($sql);

		}

		if($info_type == 'seller_info'){

			//[S] snsgroup정보 json, url encode하여 DB에 저장
			$sns_json = urlencode(json_encode($sns_group));
			//[E] snsgroup정보 json, url encode하여 DB에 저장

			$sql = "update common_seller_detail set
						shop_name='$shop_name',
						shop_name_linear = '" . linear_hangul($shop_name) . "',
						shop_desc='$shop_desc',
						homepage='$homepage',
						minishop_templet='$minishop_templet',
						minishop_use = '$minishop_use',
						bank_owner='$bank_owner',
						bank_name='$bank_name',
						bank_number='$bank_number',
						sns_group = '".$sns_json."',
						cs_name = '".$cs_name."',
						cs_phone = '".$cs_phone."',
						cs_mobile = '".$cs_mobile."',
						cs_mail = '".$cs_mail."',
						cs_position = '".$cs_position."',
						cs_message = '".$cs_message."',

						md_code='$md_code',
						team='$team',
						edit_date = NOW()
					where
						company_id='$company_id' ";
			$db->query($sql);

			$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/";
			if(!is_dir($path)){
				mkdir($path, 0777);
			}
			if ($shop_logo_img_size > 0){
				copy($shop_logo_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif");
				$shop_logo_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif";
			}
	 
			if ($shop_img_size > 0){
				copy($shop_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif");
				$shop_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif";
			}

			if ($shop_img_thum_size > 0){
				copy($shop_img_thum, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif");
				$shop_img_thum_str = $admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif";
			}
			if ($shop_bg_size > 0){
				copy($shop_bg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_bg_".$company_id.".gif");
				$shop_bg_str = $admin_config[mall_data_root]."/images/shopimg/shop_bg_".$company_id.".gif";
			}

		}

		if($info_type == "factory_info" || $info_type == "exchange_info" || $info_type == "visit_info"){

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
							and delivery_type = '$delivery_type'";

				$db2->query($sql);
				$db2->fetch();
				$addr_ix = $db2->dt[addr_ix];

				
		$up_sql = "update shop_delivery_address set
						basic_addr_use = 'N'
					where
						addr_ix = '$addr_ix'
				";

				$db2->query($up_sql);
			}
			
			if($_REQUEST[addr_ix]){
				$sql = "update shop_delivery_address set
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
							is_delivery_use = '".$is_delivery_use."',
							editdate = NOW()
						where
							addr_ix = '".$_REQUEST[addr_ix]."'";
				$db->query($sql);

			}else{
				$sql = "insert into shop_delivery_address set
							company_id = '$company_id',
							mall_ix = '".$admininfo[mall_ix]."',
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
							is_delivery_use = '".$is_delivery_use."',
							regdate = NOW();";
				$db->query($sql);
			
			}
		}

		if($info_type == "seller_setup"){	//셀러 수수료 설정

			$account_info = $_REQUEST[account_info];
			$ac_delivery_type = $_REQUEST[ac_delivery_type];
			$ac_expect_date = $_REQUEST[ac_expect_date];
			$ac_term_div = $_REQUEST[ac_term_div];
			$ac_term_date1 = $_REQUEST[ac_term_date1];
			$ac_term_date2 = $_REQUEST[ac_term_date2];
			$account_type = $_REQUEST[account_type];
			$account_method = $_REQUEST[account_method];
			$wholesale_commission = $_REQUEST[wholesale_commission];
			$commission = $_REQUEST[commission];
			$seller_grant_use = $_REQUEST[seller_grant_use];
			$grant_setup_price = $_REQUEST[grant_setup_price];
			$ac_grant_price = $_REQUEST[ac_grant_price];
			$account_div = $_REQUEST[account_div];
			$econtract_commission = $_REQUEST[electron_contract_commission];
			$et_ix = $_REQUEST[et_ix];

			$sql = "update common_seller_delivery set et_ix = '".$et_ix."', econtract_commission = '".$econtract_commission."' where company_id = '".$company_id."'";
			$db->query($sql);

			$sql = "select
						count(company_id) as cnt
					from
						common_seller_delivery
					where
						company_id = '".$company_id."'";

			$db->query($sql);
			$db->fetch();

			$cnt = $db->dt[cnt];

			if($cnt > 0){	//update
				$sql = "update common_seller_delivery set
							account_info = '".$account_info."',
							ac_delivery_type = '".$ac_delivery_type."',
							ac_expect_date = '".$ac_expect_date."',
							ac_term_div = '".$ac_term_div."',
							ac_term_date1 = '".$ac_term_date1."',
							ac_term_date2 = '".$ac_term_date2."',
							substitude_rate = '".$substitude_rate."',

							account_type = '".$account_type."',
							account_method = '".$account_method."',
							wholesale_commission = '".$wholesale_commission."',
							commission = '".$commission."',

							seller_grant_use = '".$seller_grant_use."',
							grant_setup_price = '".$grant_setup_price."',
							ac_grant_price = '".$ac_grant_price."',
							account_div = '".$account_div."'
						where
							company_id = '".$company_id."'
							";
				$db->query($sql);
			
			}else{
				$sql = "insert into common_seller_delivery set
							company_id = '".$company_id."',
							account_info = '".$account_info."',
							ac_delivery_type = '".$ac_delivery_type."',
							ac_expect_date = '".$ac_expect_date."',
							ac_term_div = '".$ac_term_div."',
							ac_term_date1 = '".$ac_term_date1."',
							ac_term_date2 = '".$ac_term_date2."',

							account_type = '".$account_type."',
							account_method = '".$account_method."',
							wholesale_commission = '".$wholesale_commission."',
							commission = '".$commission."',

							seller_grant_use = '".$seller_grant_use."',
							grant_setup_price = '".$grant_setup_price."',
							ac_grant_price = '".$ac_grant_price."',
							account_div = '".$account_div."'";
				$db->query($sql);
			}
		}

		if($info_type == 'tax_info'){
			$tax_mail = trim($_REQUEST[tax_mail]);	//세금계산서이메일
			$basic_bank = trim($_REQUEST[basic_bank]);	//거래처 은행
			$bank_num = trim($_REQUEST[bank_num]);	//계좌번호
			$holder_name = trim($_REQUEST[holder_name]);	//예금주
			
			$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set
						tax_mail = '".$tax_mail."',
						basic_bank = '".$basic_bank."',
						holder_name = '".$holder_name."',
						bank_num = '".$bank_num."'
					where
						company_id = '".$company_id."'";
			$db->query($sql);

		}

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images"."/minishop_banner/".$_POST['company_id']."/";
		if(!is_dir($path)){
			mkdir($path, 0777, true);
		}

		if(is_array($_FILES['file']['size'])){
			foreach($_FILES['file']['size'] as $_key=>$_val){
				if($ix[$_key] != "" && $nondelete[$ix[$_key]] == 1){
					$sql = "update shop_minishop_banner set tmp_update='1' where ix='".$ix[$_key]."' ";
					$db->query($sql);
				}
				if($_val > 0)	{
					$target = $path."/".$_FILES['file']['name'][$_key];
					copy($_FILES['file']['tmp_name'][$_key], $target);
					chmod($target, 0777);

					if($ix[$_key] == ""){
						$sql = sprintf("insert into shop_minishop_banner 
						                      (company_id, title, link, file, banner_priority, regdate, tmp_update) 
											   values('%s', '%s', '%s', '%s', %d, NOW(),  '1')"
											   , $_POST['company_id']
											   , $_POST['title'][$_key]
											   , $_POST['link'][$_key]
											   , $_FILES['file']['name'][$_key]
											   , $_POST['bannerPriority'][$_key]);
						$db->query($sql);
						$lastIx = mysql_insert_id();
						array_push($ix, $lastIx);
					} else {
						$sql = "update shop_minishop_banner set file='".$_FILES['file']['name'][$_key]."',link='".$link[$_key]."',title='".$title[$_key]."',banner_priority=".$bannerPriority[$_key].", tmp_update='1' where
						ix='".$ix[$_key]."' ";
						$db->query($sql);
					}
				}
			}
		}

		if(count($ix) > 0){
			$ix = array_filter($ix, create_function('$value', 'return $value !== "";'));
			$ixs = implode(", ", $ix);
			$sql = sprintf("DELETE FROM shop_minishop_banner WHERE company_id = '%s' AND ix not in (%s) ", $_POST['company_id'], $ixs);
			$db->query($sql);
		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
		if($page_type == "seller_company"){
			echo("<script>parent.document.location.href = '/admin/seller/seller_company.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
		}else{
			echo("<script>parent.document.location.href = '/admin/seller/company.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
		}
	}else if ($act == "recommend"){
		$sql = "UPDATE ".TBL_COMMON_COMPANY_DETAIL." SET
				recommend='$recomm'
				WHERE company_id='$company_id'"; // 이름에 대한 수정을 없앰 kbk
				// 회원과 회사 정보는 1:다 관계 이므로 code 값을 company_id 로 변경

		$db->query($sql);

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
		echo("<script>top.document.location.reload();</script>");

	}else if($act == "delete"){

		//셀러 삭제시 셀러와 회원을 분리시켜주기로함 2013-09-12 이학봉 단 셀러는 거래처로 되고 회원은 일반 회원으로 전환한다. 
		$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set
					com_type ='G',
					inputtype = 'U',
					regdate = NOW()
				where
					company_id = '".$company_id."'";
		$db->query($sql);

		$sql = "update ".TBL_COMMON_SELLER_DETAIL." set
					charge_code = ''
				where
					company_id = '".$company_id."'";
		$db->query($sql);

		$sql = "select code from ".TBL_COMMON_USER." where company_id ='$company_id'";
		$db->query($sql);
		$total = $db->total;
		$users = $db->fetchall();

		for($i=0;$i < count($users);$i++){
			$db->fetch($i);
			$code = $users[$i][code];
			
			$sql = "update ".TBL_COMMON_USER." set
						mem_type = 'M',
						mem_div = 'D',
						company_id = ''
					where
						code = '".$code."'";
			$db->query($sql);
		}

		//이미지 삭제없어서 추가 2012-10-11 홍진영
		/*
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
		*/
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');</script>");
		echo("<script>location.href = 'company_list.php?mmode=".$mmode."&';</script>");

	}else if($act == "user_insert"){

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
        $pass = hash("sha256", $pw);
		$name  = trim($name);
		$nick_name  = trim($nick_name);
		$addr1 = trim($addr1);
		$addr2 = trim($addr2);
        $birthday = $birthday_yyyy.'-'.$birthday_mm.'-'.$birthday_dd;
        $pcs = $pcs1."-".$pcs2."-".$pcs3;
        $zip = $zip1;
        $tel = $tel1."-".$tel2."-".$tel3;

		if(!isset($department)){
			$department = "0";
		}

		if(!isset($position)){
			$position = "0";
		}

        $is_id_auth = "Y";
        $request_info = 'S';
        $request_yn = 'Y';
        $agent_type = "W";
        $mem_div = "S";

        $code  = md5(uniqid(rand()));

        $sql = "INSERT INTO ".TBL_COMMON_USER."
						(code, id, pw, mem_type, mem_div, date, regdate_desc, visit, last, ip, company_id, authorized, auth, activation_code, is_id_auth,is_pos_link,join_status,request_info,request_yn,request_date, user_agent, agent_type,language)						VALUES
						('$code','$id','".$pass."','$mem_type','$mem_div',NOW(),'".(time()*-1)."','0',NOW(),'$REMOTE_ADDR','$company_id','$authorized','$authClass', '$activation_code', '$is_id_auth','N','I','$request_info','$request_yn',NOW(),'".$_SERVER["HTTP_USER_AGENT"]."','".$agent_type."','".$language_type."')";
        $db->query($sql);

        $sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
							(code, mem_card ,level_ix,birthday, birthday_div, name, mail, zip, addr1, addr2, doro_zip, doro_addr1, doro_addr2, tel,tel_div,pcs_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6,voucher_div,voucher_num_div,voucher_phone,phone_voucher_name,voucher_card,card_voucher_name,expense_num,nationality)
							VALUES
							('$code',HEX(AES_ENCRYPT('$mem_card','".$db->ase_encrypt_key."')),'1','$birthday','$birthday_div',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$doro_addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$doro_addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div','$pcs_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6','$voucher_div','$voucher_num_div','$voucher_phone','$phone_voucher_name','$voucher_card','$card_voucher_name','$expense_num','$nationality')";
        $db->query($sql);

        if($charge_check == "Y"){		//셀러 대표자 회원으로 지정
            $sql = "update ".TBL_COMMON_SELLER_DETAIL." set charge_code = '$code' where company_id = '".$company_id."'";
            $db->query($sql);
        }

		admin_log("C",$id,$company_id);

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('셀러가 정상적으로 등록되었습니다.');parent.document.location.reload();</script>");

	}else if($act == "user_update"){

		admin_log("U",$b_id,$company_id);

        $name  = trim($name);
        $nick_name  = trim($nick_name);
        $addr1 = trim($addr1);
        $addr2 = trim($addr2);
        $birthday = $birthday_yyyy.'-'.$birthday_mm.'-'.$birthday_dd;
        $pcs = $pcs1."-".$pcs2."-".$pcs3;
        $zip = $zip1;
        $tel = $tel1."-".$tel2."-".$tel3;

        if(!isset($department)){
            $department = "0";
        }

        if(!isset($position)){
            $position = "0";
        }

        $pw = trim($pw);
        if($change_pass){
            if($pw){
                $update_pass_str = ", pw= '".hash("sha256", $pw)."'";
            }
        }

		$sql = "UPDATE ".TBL_COMMON_USER." SET
				language = '$language_type',authorized = '$authorized', auth = '$authClass' $update_pass_str
				WHERE code='$code'";
		$db->query($sql);

        $sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET
                mail= HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),
                tel=HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),
                pcs=HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),
                name = HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),
                gp_ix = '$gp_ix',
                level_ix = '$level_ix',
                level_msg = '$level_msg',
                sex_div = '$sex_div',
                birthday = '$birthday',
                birthday_div = '$birthday_div',
                sms = '$sms',
                info = '$info',
                zip = HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),
                addr1 = HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),
                addr2 = HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),
                department = '$department',
                position = '$position'
                WHERE code='$code'";
		$db->query($sql);

        if($charge_check == "Y"){		//셀러 대표자 회원으로 지정
            $sql = "update ".TBL_COMMON_SELLER_DETAIL." set charge_code = '$code' where company_id = '".$company_id."'";
            $db->query($sql);
        }

		//변경정보와 로그인 아이디가 같으면 랭귀지 변경정보를 세션에 반영한다.
		if($admininfo[charger_ix] == $code){
			$admininfo["language"] = $language_type;
		}

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('관리자가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
	}else if($act == "user_delete"){

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
	}else if($act == "admin_log"){
		admin_log("R",$charger_id,$company_id);
	}else if($act == "seller_all_update"){
		foreach($code as $key => $value){

			$sql="UPDATE ".TBL_COMMON_USER." SET auth='".$use_disp."' WHERE code='".$value."' ";	//셀러회원 권한 부여 이학봉
			$db2->query($sql);

		}

		if($mode=="pop") {
			echo "<script>alert('정상적으로 변경 되었습니다.');top.opener.document.location.reload();top.window.close();</script>";
			exit;
		} else {
			echo "<script>alert('정상적으로 변경 되었습니다..');top.document.location.reload();location.href='about:blank';</script>";
			exit;
		}
	}else if($act =="seller_update"){

		$sql = "update ".TBL_COMMON_USER." set auth = '4' where code='".$code."' ";
		$db2->query($sql);

		if($mode=="pop"){
			echo "<script>top.opener.document.location.reload();top.window.close();</script>";
			exit;
		}else{
			echo "<script>top.document.location.reload();location.href='about:blank';</script>";
			exit;
		}
	}else if($act == "template_update"){

		if(is_array($code_ix)) {
			$delivery_company=implode(",",$code_ix);		//셀러별 택배설정 코드값
		}

		$sql = "update common_seller_delivery set
					delivery_policy = '".$delivery_policy."',
					delivery_product_policy = '".$delivery_product_policy."',
					delivery_company='".$delivery_company."',
					delivery_deadline_yn='".$delivery_deadline_yn."',
					delivery_deadline_hour='".$delivery_deadline_hour."',
					delivery_deadline_minute='".$delivery_deadline_minute."',
					goodsflow_return_yn='".$goodsflow_return_yn."',
					goodsflow_policy_type='".$goodsflow_policy_type."'
				where
					company_id = '".$company_id."'
					";
		$db->query($sql);

		echo "<script>alert('정상적으로 변경 되었습니다.');top.document.location.reload();location.href='about:blank';</script>";
		exit;

	}else if($act == "overseas_update"){

		$sql = "update common_seller_delivery set
					unit = '".$unit_info[0]."'
					, demandship_service_key = '".trim($service_key)."'
				where
					company_id = '".$company_id."'
					";
		$db->query($sql);

		if(is_array($code_ix)) {
			$delivery_company=implode(",",$code_ix);		//셀러별 택배설정 코드값
		}

		$sql = "update common_seller_delivery set
					delivery_policy = '".$delivery_policy."',
					delivery_company='".$delivery_company."'
				where
					company_id = '".$company_id."'
					";
		$db->query($sql);

		echo "<script>alert('정상적으로 변경 되었습니다.');top.document.location.reload();location.href='about:blank';</script>";
		exit;
	}else if($act == 'addr_delete'){		//출고지,교환/반품지, 방문수령지 데이타 삭제 2014-08-12 이학봉
		
		if($addr_ix){
			$sql = "delete from shop_delivery_address where addr_ix = '".$addr_ix."'";
			$db->query($sql);

			echo("<script>top.document.location.href = '../seller/company.add.php?info_type=".$info_type."&company_id=".$company_id."';</script>");
			exit;
		}

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
		$_service_info = (array)$myservice_info[$service_type];
		$myservice_info = (array)$_service_info[$solution_type];

		if($myservice_info[si_status] == "SI" && $myservice_info[sm_edate] >= date("Y-m-d")){
			if($service_use_value != ""){
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
	}

	// 한글 linear 하는것
	// 2016.11.16 이철성
	function utf8_strlen($str) { return mb_strlen($str, 'UTF-8'); }
	function utf8_charAt($str, $num) { return mb_substr($str, $num, 1, 'UTF-8'); }
	function utf8_ord($ch) {
	  $len = strlen($ch);
	  if($len <= 0) return false;
	  $h = ord($ch{0});
	  if ($h <= 0x7F) return $h;
	  if ($h < 0xC2) return false;
	  if ($h <= 0xDF && $len>1) return ($h & 0x1F) <<  6 | (ord($ch{1}) & 0x3F);
	  if ($h <= 0xEF && $len>2) return ($h & 0x0F) << 12 | (ord($ch{1}) & 0x3F) << 6 | (ord($ch{2}) & 0x3F);          
	  if ($h <= 0xF4 && $len>3) return ($h & 0x0F) << 18 | (ord($ch{1}) & 0x3F) << 12 | (ord($ch{2}) & 0x3F) << 6 | (ord($ch{3}) & 0x3F);
	  return false;
	}

	function linear_hangul($str) {
	  $cho = array("ㄱ","ㄲ","ㄴ","ㄷ","ㄸ","ㄹ","ㅁ","ㅂ","ㅃ","ㅅ","ㅆ","ㅇ","ㅈ","ㅉ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");
	  $jung = array("ㅏ","ㅐ","ㅑ","ㅒ","ㅓ","ㅔ","ㅕ","ㅖ","ㅗ","ㅘ","ㅙ","ㅚ","ㅛ","ㅜ","ㅝ","ㅞ","ㅟ","ㅠ","ㅡ","ㅢ","ㅣ");
	  $jong = array("","ㄱ","ㄲ","ㄳ","ㄴ","ㄵ","ㄶ","ㄷ","ㄹ","ㄺ","ㄻ","ㄼ","ㄽ","ㄾ","ㄿ","ㅀ","ㅁ","ㅂ","ㅄ","ㅅ","ㅆ","ㅇ","ㅈ","ㅊ","ㅋ"," ㅌ","ㅍ","ㅎ");
	  $result = "";
	  for ($i=0; $i<utf8_strlen($str); $i++) {
		$code = utf8_ord(utf8_charAt($str, $i)) - 44032;
		if ($code > -1 && $code < 11172) {        
		  $cho_idx = $code / 588;      
		  $jung_idx = $code % 588 / 28;  
		  $jong_idx = $code % 28;
		  $result .= $cho[$cho_idx].$jung[$jung_idx].$jong[$jong_idx];
		} else {
		   $result .= utf8_charAt($str, $i);
		}
	  }
	  return $result;
	}