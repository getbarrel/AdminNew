<?
include("../../class/database.class");
include("../basic/company.lib.php");
session_start();

$db = new Database;

if ($act == "idcheck")
{
	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id='$id'");

	if ($db->total)
	{
		echo("<script>alert('이미 등록된 [아이디]입니다.');</script>");
		//echo("<script>top.form.id_flag.value = '0';</script>");
		echo("<script>top.edit_form.admin_id.dup_check = false;</script>");
	}
	else
	{
		echo("<script>alert('사용 가능한 [아이디]입니다.');</script>");
		//echo("<script>top.form.id_flag.value = '1';</script>");
		echo("<script>top.edit_form.admin_id.dup_check = true;</script>");
	}
}


if ($act == "update"){

	if($com_phone1 !=''){
		$com_phone  = trim("$com_phone1-$com_phone2-$com_phone3");
	}else{
		$com_phone  = trim("$com_phone2-$com_phone3");
	}
	$com_mobile  = trim("$com_mobile1-$com_mobile2-$com_mobile3");
	$com_fax  = trim("$com_fax1-$com_fax2-$com_fax3");
	$homepage  = trim($homepage);
	$shipping_company  = trim($shipping_company);
	
	if($cs_phone1 !=''){
		$cs_phone  = trim("$cs_phone1-$cs_phone2-$cs_phone3");
	}else{
		$cs_phone  = trim("$cs_phone2-$cs_phone3");
	}

	if($info_type == "basic" || $info_type == ""){

		$return_zip  = trim($return_zip1."-".$return_zip2);

		////////////////////////////////////
		$company_code  = trim($_REQUEST[company_code]);	//본사코드
		$com_name  = trim($_REQUEST[com_name]);			//설립일
		$com_ceo  = trim($_REQUEST[com_ceo]);			//대표자명
		$com_div  = trim($_REQUEST[com_div]);			//사업자 유형 R:법인사업자 P:일반사업자 S:간이과세자 E:면세사업자
		$com_number  = trim($_REQUEST[com_number_1])."-".trim($_REQUEST[com_number_2])."-".trim($_REQUEST[com_number_3]);			//사업자번호
		$corporate_number  = trim($_REQUEST[corporate_number_1])."-".trim($_REQUEST[corporate_number_2]);			//법인번호
		$com_business_status  = trim($_REQUEST[com_business_status]);			//업태
		$com_business_category  = trim($_REQUEST[com_business_category]);			//업종
		$com_zip  = trim($_REQUEST[com_zip]);			//우편번호
		$com_addr1  = trim($_REQUEST[com_addr1]);			//주소1
		$com_addr2  = trim($_REQUEST[com_addr2]);			//주소2
		$com_fax  = trim($_REQUEST[com_fax_1])."-".trim($_REQUEST[com_fax_2])."-".trim($_REQUEST[com_fax_3]);			//대표번호
//		$com_phone  = trim($_REQUEST[com_phone_1])."-".trim($_REQUEST[com_phone_2])."-".trim($_REQUEST[com_phone_3]);			//대표번호
		$com_mobile  = trim($_REQUEST[com_mobile_1])."-".trim($_REQUEST[com_mobile_2])."-".trim($_REQUEST[com_mobile_3]);		//대표핸드폰번호
		$com_homepage  = trim($_REQUEST[com_homepage]);			//홈페이지
		$com_email  = trim($_REQUEST[com_email]);			//이메일
		//$online_business_number  = trim($_REQUEST[online_business_number]);			//통신판매업 번호
		$seller_auth  = trim($_REQUEST[seller_auth]);			//본사등록승인 여부 N:승인대기 Y : 승인 X : 승은거부
		$is_wharehouse = trim($_REQUEST[is_wharehouse]);	//물류창고 사용여부
		//seller_detail_st
		$seller_date = trim($_REQUEST[seller_date]); //거래시작일
		$seller_division = trim($_REQUEST[seller_division]); //거래처구분
		$nationality = trim($_REQUEST[nationality]); //국내외 구분

		if(is_array($seller_type)){	//거래처 유형
			foreach($seller_type as $key=>$value){
				if($value && $key == '0'){
					$seller_type = $value;
				}else{
					$seller_type .= "|".$value;
				}
			}
		}
		
		$seller_message =  trim($_REQUEST[seller_message]);	//거래처 기타사항
		$shop_name = trim($_REQUEST[shop_name]);	//거래처 상점명(상호명)
		$officer_name  = trim($officer_name);
		$officer_email  = trim($officer_email);
//		$cs_phone = trim($_REQUEST[cs_phone1])."-".trim($_REQUEST[cs_phone2])."-".trim($_REQUEST[cs_phone3]);			//cs 문의전화
//		$shipping_phone = trim($_REQUEST[shipping_phone_1])."-".trim($_REQUEST[shipping_phone_2])."-".trim($_REQUEST[shipping_phone_3]);			//배송문의전화
		if($shipping_phone_1 !=''){
			$shipping_phone  = trim("$shipping_phone_1-$shipping_phone_2-$shipping_phone_3");
		}else{
			$shipping_phone  = trim("$shipping_phone_2-$shipping_phone_3");
		}
		$sql = "UPDATE ".TBL_COMMON_COMPANY_DETAIL." SET
					company_code = '$company_code',
					open_date = '$seller_date',
					com_name='$com_name', 
					com_div='$com_div', 
					com_number = '$com_number',
					corporate_number = '$corporate_number',
					com_ceo='$com_ceo', 
					com_business_status='$com_business_status', 
					com_business_category='$com_business_category',
					com_number='$com_number',
					online_business_number='$online_business_number',
					com_phone='$com_phone',
					com_mobile='$com_mobile',
					com_email='$com_email',
					com_fax = '$com_fax',
					com_zip='$com_zip',
					com_addr1='$com_addr1', 
					com_addr2='$com_addr2',
					com_homepage = '$com_homepage',
					seller_type = '$seller_type',
					seller_auth='$seller_auth',
					is_wharehouse = '$is_wharehouse',
					opening_time = '$opening_time',
					cs_phone = '$cs_phone',
					shipping_phone = '$shipping_phone',
					officer_name = '$officer_name',
					officer_email = '$officer_email'
				WHERE
					company_id='$company_id'"; 
		$db->query($sql);

		$sql = "update ".TBL_COMMON_SELLER_DETAIL." set
					seller_date = '".$seller_date."',
					seller_division = '".$seller_division."',
					nationality = '".$nationality."',
					seller_message = '".$seller_message."'
				WHERE 
					company_id='$company_id'";
		$db->query($sql);
		
		/*
		//거래처별 연결코드 추가 시작 2014-06-13 이학봉
		$seq	= check_seq($relation_code,$depth);
		//$new_code = check_relation($relation_code,$depth);
		$new_code = get_relation_code('5');
		$db->query("select * from ".TBL_COMMON_COMPANY_RELATION." where relation_code = '".$new_code."'");

		if($db->total > 0){
	$sql_relation = "update ".TBL_COMMON_COMPANY_RELATION." set
						relation_code = '".$new_code."',
						seq = '".$seq."',
						edit_date = NOW()
					where
						company_id = '".$company_id."'";
			$db->query($sql_relation);
		}else{
			
	$sql_relation = "insert into ".TBL_COMMON_COMPANY_RELATION." 
					(company_id,relation_code,seq,depth,reg_date)
					values
					('".$company_id."','".$new_code."','".$seq."','".$depth."',NOW())";
			$db->query($sql_relation);
		}
		//거래처별 연결코드 추가 끝 2014-06-13 이학봉
		*/
		
		//도매상권관련 추가 2014-02-18 HONG 
		$ws_tel  = trim("$ws_tel_1-$ws_tel_2-$ws_tel_3");
		$ws_charge_phone  = trim("$ws_charge_phone_1-$ws_charge_phone_2-$ws_charge_phone_3");
		$kakao_phone  = trim("$kakao_phone_1-$kakao_phone_2-$kakao_phone_3");

		$sql = "select * from common_company_wholesale where company_id ='$company_id'";
		$db->query($sql);
		if(!$db->total){
			$sql = "insert into common_company_wholesale 
					(company_id,commercial_disp,ca_country,ca_code,sc_code,floor,line,no,tel,charge_phone,kakao_phone,kakao_id,facebook,twitter,qq,wechat,regdate)
					values
					('$company_id','$commercial_disp','$ca_country','$ca_code','$sc_code','$floor','$line','$no','$ws_tel','$ws_charge_phone','$kakao_phone','$kakao_id','$facebook','$twitter','$qq','$wechat',NOW()) ";
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
					where 
						company_id='$company_id' ";
			$db->query($sql);
		}
		

		
		/*
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}
		if ($company_stamp_size > 0){
			copy($company_stamp, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/company_stamp_".$admininfo[company_id].".gif");
		}
		*/
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
        $path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_config";

        if(!is_dir($path)){
            mkdir($path, 0777);
            chmod($path,0777);
        }

        $companyInfo = require($path.'/company.php');
        $changeParameterList = array('company_id','com_name','com_number','online_business_number','com_ceo','com_addr1','com_addr2','com_phone','com_fax','com_email','com_business_category','com_business_status','opening_time','cs_phone','officer_name','officer_email','global_cs_phone','global_cs_email','global_opening_time');
        foreach($changeParameterList as $key => $parameter){
            $companyInfo[$parameter] = $$parameter;
        }
        //DB 추가 필요 해당 코드는 임시로 작업 됨 실제로는 DB 에 등록 시켜서 처리 되어야 함 20191129 JK [S]
		//DB 에 추가하지는 않고 바로 파일로 생성하도록 처리 하였으며 관리자 리스트에서는 해당 파일을 읽어 정보를 취득하도록 처리 JK20191203
//        $companyInfo['global_cs_phone'] = "+82-1899-8751";
//		$companyInfo['global_cs_email'] = "en_help@getbarrel.com";
//        $companyInfo['global_opening_time'] = "Contact us
//en_help@getbarrel.com";
        //DB 추가 필요 해당 코드는 임시로 작업 됨 실제로는 DB 에 등록 시켜서 처리 되어야 함 20191129 JK [E]

        $first = true;
        $companyInfoModify = array();
        $companyInfoModify[] = '<?php';
        //$companyInfoModify[] = 'return [';
        $companyInfoModify[] = 'return array(';
        foreach($companyInfo as $key => $value){
            $str = '';
            if($first) {
                $first = false;
            }else{
                $str .= ',';
			}
            $str .= "'".$key."' => '".$value."'";

            $companyInfoModify[] = $str;
        }
        //$companyInfoModify[] = '];';
        $companyInfoModify[] = ');';

        file_put_contents($path.'/company.php', implode("\n",$companyInfoModify));

	}else if($info_type == "person_info"){

		$customer_name = trim($_REQUEST[customer_name]);	//거래처 담당자명
		$customer_phone = trim($_REQUEST[customer_phone_1]."-".$_REQUEST[customer_phone_2]."-".$_REQUEST[customer_phone_3]);	//거래처 전화
		$customer_mobile = trim($_REQUEST[customer_mobile_1]."-".$_REQUEST[customer_mobile_2]."-".$_REQUEST[customer_mobile_3]);	//거래처 핸드폰
		$customer_mail = trim($_REQUEST[customer_mail]);	//거래처 메일
		$customer_position = trim($_REQUEST[customer_position]);	//거래처 직급/직책
		$customer_message = trim($_REQUEST[customer_message]);	//거래처 담당자 메세지
		$tax_person_name = trim($_REQUEST[tax_person_name]);	//세무 담당자명
		$tax_person_phone = trim($_REQUEST[tax_person_phone_1]."-".$_REQUEST[tax_person_phone_2]."-".$_REQUEST[tax_person_phone_3]);	//세무 담당자 전화
		$tax_person_mobile = trim($_REQUEST[tax_person_mobile_1]."-".$_REQUEST[tax_person_mobile_2]."-".$_REQUEST[tax_person_mobile_3]);	//세무 담당자 핸드폰번호
		$tax_person_mail = trim($_REQUEST[tax_person_mail]);	//세무 담당자 메일
		$tax_person_position = trim($_REQUEST[tax_person_position]);	//세무 담당자 직책/직급
		$tax_person_message = trim($_REQUEST[tax_person_message]);	//세무 담당자 기타사항
		$bank_owner = trim($_REQUEST[bank_owner]);	//세무 담당자 기타사항
		$bank_name  = trim($_REQUEST[bank_name ]);	//세무 담당자 기타사항
		$bank_number = trim($_REQUEST[bank_number]);	//세무 담당자 기타사항

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
	
	}else if($info_type == "shop_info"){
		/**
		 * 상점 정보 수정인데 common_seller_delivery 테이블을 조회하는 것이 이상하다
		 * sehyun 20170807
		 */
		//$sql = "select * from common_seller_delivery where company_id ='$company_id'";
        $sql = "select * from common_seller_detail where company_id ='$company_id'";
		$db->query($sql);
		if(!$db->total){
			$sql = "insert into common_seller_detail set
					company_id='$company_id',
					shop_name='$shop_name',
					shop_desc='$shop_desc'
				
					 ";
					 /*
					homepage='$homepage',
					bank_owner='$bank_owner',
					bank_name='$bank_name',
					bank_number='$bank_number'	*/

            $db->query($sql);
		}else{

			$sql = "update common_seller_detail set 
						shop_name='$shop_name',
						shop_desc='$shop_desc'
					where company_id='$company_id' ";
						/*homepage='$homepage',
						bank_owner='$bank_owner',
						bank_name='$bank_name',
						bank_number='$bank_number'*/
			//echo $sql;
			$db->query($sql);
		}

        // 이미지 저장 끝
        $path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_config";

        if(!is_dir($path)){
            mkdir($path, 0777);
            chmod($path,0777);
        }

        $companyInfo = require($path.'/company.php');
        $changeParameterList = array('shop_name');
		$companyInfo['shop_name'] = $shop_name;

        $first = true;
        $companyInfoModify = array();
        $companyInfoModify[] = '<?php';
        //$companyInfoModify[] = 'return [';
        $companyInfoModify[] = 'return array(';
        foreach($companyInfo as $key => $value){
            $str = '';
            if($first) {
                $first = false;
            }else{
                $str .= ',';
            }
            $str .= "'".$key."' => '".$value."'";

            $companyInfoModify[] = $str;
        }
        //$companyInfoModify[] = '];';
        $companyInfoModify[] = ');';

        file_put_contents($path.'/company.php', implode("\n",$companyInfoModify));

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}
		if ($shop_logo_img_size > 0){
			copy($shop_logo_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$admininfo[company_id].".gif");
			$shop_logo_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_logo_".$admininfo[company_id].".gif";

		}

		if ($shop_img_size > 0){
			copy($shop_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$admininfo[company_id].".gif");
			$shop_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_".$admininfo[company_id].".gif";

		}
		if ($shop_lo_size > 0){
			copy($shop_lo, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_lo_".$admininfo[company_id].".gif");
			$shop_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_lo_".$admininfo[company_id].".gif";

		}
		
		for($i=0;$i < count($group_name);$i++){
			$db->query("Select mpg_ix from shop_minishop_product_group where group_code = '".($i+1)."' AND company_id='".$company_id."' ");

			if($db->total){
				$db->fetch();
				$sql = "update shop_minishop_product_group set
							group_name='".$group_name[$i+1]."',
							display_type='".$display_type[$i+1]."',
							insert_yn='Y', use_yn='".$use_yn[$i+1]."',
							group_link='".$group_link[$i+1]."',
							product_cnt='".$product_cnt[$i+1]."',
							goods_display_type='".$goods_display_type[$i+1]."',							
							display_auto_type='".$display_auto_type[$i+1]."'
						where
							mpg_ix='".$db->dt[mpg_ix]."'
							and group_code = '".($i+1)."' 
							AND company_id='".$company_id."' ";
				$db->query($sql);
			}else{

				$sql = "insert into shop_minishop_product_group 
						(mpg_ix,group_name,group_code,group_link,display_type,product_cnt, goods_display_type, display_auto_type, insert_yn, use_yn, company_id, regdate)
						values
						('','".$group_name[$i+1]."','".($i+1)."','".$group_link[$i+1]."','".$display_type[$i+1]."','".$product_cnt[$i+1]."','".$goods_display_type[$i+1]."','".$display_auto_type[$i+1]."','Y','".$use_yn[$i+1]."','".$company_id."',NOW())";
				$db->query($sql);
			}
			if($page_type=="M") $img_pre_text="";
			else $img_pre_text=$page_type."_";
			if ($group_img_del[$i+1] == "Y")
			{
				unlink("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/minishop/".$img_pre_text."minishop_group_".($i+1).".gif");
			}
			
			if ($_FILES["group_img"]["size"][$i+1] > 0)
			{
				copy($_FILES["group_img"][tmp_name][$i+1], "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/minishop/".$img_pre_text."minishop_group_".($i+1).".gif");
			}

			if ($group_banner_img_del[$i+1] == "Y")
			{
				unlink("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/minishop/".$img_pre_text."minishop_group_banner_".($i+1).".gif");
			}

			if ($_FILES["group_banner_img"]["size"][$i+1] > 0)
			{
				copy($_FILES["group_banner_img"][tmp_name][$i+1], "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/minishop/".$img_pre_text."minishop_group_banner_".($i+1).".gif");
			}

			for($j=0;$j < count($rpid[$i+1]);$j++){
				$db->query("Select mpr_ix from shop_minishop_product_relation where group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' AND company_id='".$company_id."' ");

				if(!$db->total){
					$sql = "insert into shop_minishop_product_relation (mpr_ix,pid,group_code, vieworder, insert_yn, company_id, regdate) values ('','".$rpid[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', '".$company_id."', NOW())";
					$db->query($sql);
				}else{
					$sql = "update shop_minishop_product_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' AND company_id='".$company_id."' ";
					$db->query($sql);
				}
			}

			$db->query("delete from shop_minishop_product_relation where group_code = '".($i+1)."' and insert_yn = 'N' AND company_id='".$company_id."' ");

			$db->query("update shop_minishop_category_relation set insert_yn = 'N'  where group_code = '".($i+1)."' AND company_id='".$company_id."' ");

			for($j=0;$j < count($category[$i+1]);$j++){
				$db->query("Select mcr_ix from shop_minishop_category_relation where group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' AND company_id='".$company_id."' ");

				if(!$db->total){
					$sql = "insert into shop_minishop_category_relation (mcr_ix,cid,group_code, vieworder, insert_yn, company_id, regdate) values ('','".$category[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', '".$company_id."', NOW())";
					$db->query($sql);
				}else{
					$sql = "update shop_minishop_category_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' AND company_id='".$company_id."' ";
					$db->query($sql);
				}
			}

			$db->query("delete from shop_minishop_category_relation where group_code = '".($i+1)."' and insert_yn = 'N' AND company_id='".$company_id."' ");

		}

		$db->query("delete from shop_minishop_product_relation where insert_yn = 'N' AND company_id='".$company_id."' ");
		$db->query("delete from shop_minishop_product_group where insert_yn = 'N' AND company_id='".$company_id."' ");

	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if($act == "user_insert"){

	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE company_id = '".$admininfo[company_id]."' and id = '$id' ");

	if ($db->total)
	{
		echo("<script>alert('[$id] 는 이미 등록된 사용자 입니다.');</script>");
		//echo("<script>history.back();</script>");
		exit;
	}

	$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id = '$id' ");

	if ($db->total)
	{
		echo("<script>alert('[$id] 이미 등록된 아이디 입니다. ');</script>");
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
	$tel   = "$tel1-$tel2-$tel3";
	$pcs   = "$pcs1-$pcs2-$pcs3";
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
					(code, id, pw, mem_type, language, company_id, authorized, auth, date_, visit, last, ip)
					VALUES
					('$code','$id','".hash("sha256", $pw)."','A','".$language_type."','".$admininfo[company_id]."','".$authorized."','".$auth."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."')";
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_USER."
					(code, id, pw, mem_type, language, company_id, authorized, auth, date, visit, last, ip)
					VALUES
					('$code','$id','".hash("sha256", $pw)."','A','".$language_type."','".$admininfo[company_id]."','".$authorized."','".$auth."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."')";
	}
	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code, name, mail, tel, pcs, date_, recom_id,  gp_ix ,department, position)
					VALUES
					('$code',AES_ENCRYPT('$name','".$db->ase_encrypt_key."'),AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'),AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'),NOW(),'".$admininfo[charger_id]."','$gp_ix','$department','$position')";
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code, name, mail, tel, pcs, date, recom_id,  gp_ix,department, position)
					VALUES
					('$code',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),NOW(),'".$admininfo[charger_id]."','$gp_ix','$department','$position')";
	}
	$db->query($sql);

	admin_log("C",$id,$company_id);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('관리자가 정상적으로 등록되었습니다.');parent.document.location.reload();</script>");

}

if($act == "user_update"){

	admin_log("U",$id,$company_id);
	$tel = $tel1."-".$tel2."-".$tel3;
	$pcs = $pcs1."-".$pcs2."-".$pcs3;

	if($change_pass){
		$update_pass_str = ", pw= '".hash("sha256", $pw)."'";
	}

	if(trim($id) != trim($b_id)){
		$db->query("select * from ".TBL_COMMON_USER."  where company_id='".trim($admininfo[company_id])."' and id='".trim($id)."' ");

		if($db->total){
			echo "<script language='javascript'>alert('$charger_id 아이디는 이미 사용중입니다.');</script>";
			exit;
		}

		$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id = '$id' ");

		if ($db->total){
			echo("<script>alert('[$id] 이미 등록된 아이디 입니다. ');</script>");
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
			id='$id' , language = '".$language_type."',authorized = '$authorized', auth = '$auth' $update_pass_str
			WHERE code='$code'"; // 이름에 대한 수정을 없앰 kbk
	$db->query($sql);

	$sql = "select * from  ".TBL_COMMON_MEMBER_DETAIL." where code='$code' ";

	$db->query($sql);
	if($db->total){
		if($department){
			$department_str = ", department = '$department' ";
		}

		if($position){
			$position_str = ", position = '$position' ";
		}

		if($db->dbms_type == "oracle"){
			$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET
				name = AES_ENCRYPT('$name','".$db->ase_encrypt_key."'), mail=AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'), tel=AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),pcs=AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."') $department_str $position_str
				WHERE code='$code'"; // 이름에 대한 수정을 없앰 kbk , shs 2011.07.17
		}else{
			$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET
				name = HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')), mail=HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')), tel=HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),pcs=HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')) $department_str $position_str
				WHERE code='$code'"; // 이름에 대한 수정을 없앰 kbk , shs 2011.07.17 ,
		}

		//echo $sql;
		//exit	;
		$db->query($sql);
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, name, mail, tel, pcs, date, recom_id, department, position,  gp_ix)
						VALUES
						('$code',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),NOW(),'".$admininfo[charger_id]."','$department','$position','$gp_ix')";

		$db->query($sql);
	}

	//변경정보와 로그인 아이디가 같으면 랭귀지 변경정보를 세션에 반영한다.
	if($admininfo[charger_ix] == $code){
		$admininfo["language"] = $language_type;
	}
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('관리자가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

if($act == "user_delete"){

	$db->query("SELECT code, id, company_id FROM ".TBL_COMMON_USER." WHERE company_id = '".$admininfo[company_id]."' and code = '$code' ");
	$db->fetch();
	$code = $db->dt[code];
	$id = $db->dt[id];

	admin_log("D",$id,$admininfo[company_id]);

	$sql = "delete from ".TBL_COMMON_USER." where company_id ='".$admininfo[company_id]."' and code = '$code'";
	$db->query($sql);

	$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where code = '$code'";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('입점업체 사용자 정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
	//echo("<script>location.href = 'company_list.php';</script>");
}


if($act == "admin_log")
{
	admin_log("R",$charger_id,$admininfo[company_id]);
}

if($act == "stamp_del"){
	
	$file_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."/company_stamp_".$company_id.".gif";

	if(file_exists($file_path)){
		unlink($file_path);
		echo "Y";
	}else{
		echo "N";
	}
	exit;
}

if($act == "shop_logo_del"){
	
	$file_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/".$img_name."".$company_id.".gif";

	if(file_exists($file_path)){
		unlink($file_path);
		echo "Y";
	}else{
		echo "N";
	}
	exit;
}

function admin_log($crud_div,$id,$company_id)
{
	global $admininfo;

	$mdb = new Database;

	if($mdb->dbms_type == "oracle"){
		$sql = "select ccd.com_name, AES_DECRYPT(cmd.name) as name
			from common_user cu, common_member_detail cmd ,  common_company_detail ccd
			where cu.code = cmd.code and cu.company_id = ccd.company_id
			and cu.company_id = '$company_id'
			and cu.id = '$id'";
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


function rmdirr($target,$verbose=false){

	$exceptions=array('.','..');
	if(!$sourcedir=@opendir($target))
	{
	if($verbose)
		echo '<strong>Couldn&#146;t open '.$target."</strong><br />\n";
	return false;
	}
	while(false!==($sibling=readdir($sourcedir))){
		if(!in_array($sibling,$exceptions)){
			$object=str_replace('//','/',$target.'/'.$sibling);
			if($verbose)
				echo 'Processing: <strong>'.$object."</strong><br />\n";
			if(is_dir($object))
				rmdirr($object);
			if(is_file($object)){
				$result=@unlink($object);
				if($verbose&&$result)
					echo "File has been removed<br />\n";
				if($verbose&&(!$result))
					echo "<strong>Couldn&#146;t remove file</strong>";
			}
		}
	}
	closedir($sourcedir);
	if($result=@rmdir($target))
	{
		if($verbose)
			echo "Target directory has been removed<br />\n";
		return true;
	}

	if($verbose){
		echo "<strong>Couldn&#146;t remove target directory</strong>";
		return false;
	}
}
?>
