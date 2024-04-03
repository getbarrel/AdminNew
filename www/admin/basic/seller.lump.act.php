<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("./company.lib.php");

//print_r($_POST);
//exit;
$db = new Database;
$db2 = new Database;

define_syslog_variables();
openlog("phplog", LOG_PID , LOG_LOCAL0);

//syslog(LOG_INFO, '업로드시작.\r\n');

if($act == "excel_input"){

	require_once '../product/Excel/reader.php';

	if ($excel_file_size > 0){
		copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
	}

	// ExcelFile($filename, $encoding);
	$data = new Spreadsheet_Excel_Reader();


	// Set output Encoding.
	$data->setOutputEncoding('CP949');
	$data->read($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);

	error_reporting(E_ALL ^ E_NOTICE);
	$shift_num = 0;

	//print_r( $data);
	//exit;

	$passNo = 0;
//syslog(LOG_INFO, $i.' : 실제업로드시작.\r\n');


	for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
		//print_r($data->sheets[0]['cells'][$i]);
		//exit;
		$company_code = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][1+$shift_num]));	 //거래처 코드
		$seller_date = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][2+$shift_num]));	//거래시작일
		$seller_level = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][3+$shift_num]));	//거래처등급
		$seller_division = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][4+$shift_num]));	//거래처구분
		$nationality = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][5+$shift_num]));	//국내외구분
		$seller_type = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][6+$shift_num]));	//거래처유형
		$is_wharehouse = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][7+$shift_num]));	//물류창고사용여부
		$com_name = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][8+$shift_num]));	//상호명
		$com_ceo = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][9+$shift_num]));	//대표자명
		$com_number = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][10+$shift_num]));	//사업자등록번호
		$corporate_number = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][11+$shift_num]));	//법인번호
		$com_business_status = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][12+$shift_num]));	//업태
		$com_business_category = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][13+$shift_num]));	//업종
		$com_div = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][14+$shift_num]));	//사업자유형
		$com_phone = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][15+$shift_num]));	//전화번호
		$com_mobile = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][16+$shift_num]));	//핸드폰번호
		$com_email = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][17+$shift_num]));	//이메일
		$com_homepage = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][18+$shift_num]));	//홈페이지
		$com_zip = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][19+$shift_num]));	//우편번호
		$com_addr1 = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][20+$shift_num]));	//상세주소1
		$com_addr2 = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][21+$shift_num]));	//상세주소2
		$relation_code = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][22+$shift_num]));	//본사 담당사업장 트리코드
		$loan_price = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][23+$shift_num]));	//여신한도
		$deposit_price = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][24+$shift_num]));	//보증금
		$person_id = trim(iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][25+$shift_num]));	//담당자 ID

		if($person_id){
			$sql = "select code from common_user where id = '".$person_id."'";
			$db->query($sql);
			$db->fetch();
			if($db->dt[code]){
				$person = $db->dt[code];
			}else{
				$person = "";
			}
		}

		//echo "<pre>";
		//print_r($data->sheets[0]['cells']);

		$company_id  = md5(uniqid(rand()));

//////////////////
		$sql = "select
					com_number
				from
					".TBL_COMMON_COMPANY_DETAIL."
				where
					com_number = '".$com_number."'
		";

		$db->query($sql);
		$db->fetch();

		if($db->dt[com_number] or $com_number=""){
			break;
		}

		$sql = "insert into 
					".TBL_COMMON_COMPANY_DETAIL." set
				company_id = '".$company_id."',
				com_type = 'C',
				open_date = '".$seller_date."',
				company_code = '".$company_code."',
				com_name = '".$com_name."',
				com_ceo = '".$com_ceo."',
				com_div = '".$com_div."',
				business_type = 'A',
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
				seller_auth = 'Y',
				is_wharehouse = '".$is_wharehouse."',
				person = '".$person."',
				loan_price = '".$loan_price."',
				is_erp_link = 'N',
				inputtype = 'A',
				regdate = NOW();
		";
		
		//echo "$sql";
		$db->query($sql);

		$company_ix = $db->insert_id();
		$db->query("update ".TBL_COMMON_COMPANY_DETAIL." set custseq = '".$company_ix."' where company_ix = '".$company_ix."'");
		//$insert_id = $db->insert_id();
		
		$seller_sql = "insert into ".TBL_COMMON_SELLER_DETAIL." set
				company_id = '".$company_id."',
				shop_name = '".$com_name."',
				seller_level = '".$seller_level."',
				seller_date = '".$seller_date."',
				seller_division = '".$seller_division."',
				nationality = '".$nationality."',
				seller_message = '".$seller_message."',
				deposit_price = '".$deposit_price."',
				authorized = 'Y',
				md_code = '".$md_code."',
				team = '".$team."',
				regdate =NOW();
		";

		$db->query($seller_sql);

        $relation_code = "C0001";
		//트리코드 생성 시작 
		$seq	= check_seq($relation_code,$depth);
		$new_code = check_relation($relation_code,$depth);

		//트리코드 생성 끝

		$sql_relation = "insert into 
					".TBL_COMMON_COMPANY_RELATION." set
				company_id = '".$company_id."',
				relation_code = '".$new_code."',
				seq = '".$seq."',
				reg_date = NOW();
		";

		$db->query($sql_relation);

////////////////////

	}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('일광등록이 완료되었습니다. ');top.location.reload();</script>";

}
//syslog(LOG_INFO, '업로드종료.\r\n');


function admin_log($crud_div,$id,$company_id)
{
	global $admininfo;

	$mdb = new Database;

	$mdb->query("select ccd.com_name, cmd.name from common_user cu, common_member_detail cmd ,  common_company_detail ccd  where cu.code = cmd.code and cu.company_id = ccd.company_id and cu.company_id = '$company_id' and cu.id = '$id'");

	$mdb->fetch();

	$sql = "insert into admin_log(accept_com_name,accept_m_name,admin_id,admin_name,crud_div,ip,regdate) values('".$mdb->dt[com_name]."','".$mdb->dt[name]."','".$admininfo['charger_id']."','".$admininfo['charger']."','$crud_div','".$_SERVER["REMOTE_ADDR"]."',NOW())";

	$mdb->query($sql);


}


closelog();

?>
