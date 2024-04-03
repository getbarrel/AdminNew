<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");



$db = new Database;
$sdb = new Database;

if ($act == "insert"){

	if(!$group_ix){
		$group_ix = $parent_group_ix;
	}
	
	//회원가입 유무확인 쿼리(이름,전화번호 동일시 가입=O 아니면 =X) *JBG 2014/03/13
	$sql = "SELECT * FROM common_member_detail WHERE name = HEX(AES_ENCRYPT('".$user_name."','".$db->ase_encrypt_key."')) AND pcs =HEX(AES_ENCRYPT('".$mobile."','".$db->ase_encrypt_key."'))";
	$sdb->query($sql);
	
	if($sdb->total){
		$userjoin	=	"1";	
	}else{
		$userjoin	=	"0";
	}

	//echo ($sql);
	//exit;

	$sql = "insert into shop_addressbook
					(ab_ix,company_id,mbjoin, group_ix,user_name,mobile,email,mail_yn,sms_yn,url,page,com_name,phone,fax,homepage,com_address,marketer,memo,regdate)
					values
					('$ab_ix','".$admininfo[company_id]."','$userjoin','$group_ix','$user_name','$mobile','$email','$mail_yn','$sms_yn','$url','$page','$com_name','$phone','$fax','$homepage','$com_address','$marketer','$memo',NOW())";
	$db->sequences = "SHOP_ADDRESSBOOK_SEQ";
	$db->query($sql);
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');parent.document.location.reload();</script>");
}

if ($act == "update"){
	if(!$group_ix){
		$group_ix = $parent_group_ix;
	}
	$sql = "update shop_addressbook set
					group_ix='$group_ix',user_name='$user_name',mobile='$mobile',email='$email',mail_yn='$mail_yn',sms_yn='$sms_yn',url='$url',page='$page',
					com_name='$com_name',phone='$phone',fax='$fax',homepage='$homepage',com_address='$com_address',marketer='$marketer',memo='$memo'
					where ab_ix='$ab_ix' and company_id = '".$admininfo[company_id]."' ";


	$db->query($sql);
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정 되었습니다. ');parent.document.location.reload();</script>");
}


if ($act == "delete"){
	$db->query("delete from shop_addressbook where  ab_ix ='$ab_ix'  and company_id = '".$admininfo[company_id]."'  ");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>");
}


if ($act == "sample_excel_down"){
	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-Disposition: attachment; filename=".iconv("utf-8","CP949","메일링/SMS주소록_일괄등록")."_sample.xls" );
	header( "Content-Description: PHP5 Generated Data" );
	//header("Content-charset=euc-kr");

	//$mstring = "이름,핸드폰,이메일\n";
	//$mstring .= "홍길동,010-1234-1234,sample@mallstory.com\n";
	//$mstring .= "갑돌이,010-5678-5678,sample2@mallstory.com\n";

	$mstring="이름\t핸드폰\t이메일\n";
	$mstring.="홍길동\t010-1234-1234\tsample@mallstory.com\n";
	$mstring.="갑돌이\t010-5678-5678\tsample2@mallstory.com\n"; // kbk

	echo iconv("utf-8","CP949",$mstring);
}
if ($act == "excel_insert"){
	require_once '../product/Excel/reader.php';

	/*
	if ($addressbook_excel_size > 0){
		copy($addressbook_excel, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".iconv("utf-8","CP949",$addressbook_excel_name));
	}
	*/
	if ($addressbook_excel_size > 0){
		copy($addressbook_excel, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$addressbook_excel_name);
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$addressbook_excel_name,0777);
	}
	/*
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".iconv("utf-8","CP949",$addressbook_excel_name))){
		echo "있음";
	}else{
		echo "없음";
	}
	*/
	//exit;
	// ExcelFile($filename, $encoding);
	$data = new Spreadsheet_Excel_Reader();


	// Set output Encoding.
	$data->setOutputEncoding('CP949');
	//$data->setUTFEncoder('mb');

	//$data->setOutputEncoding('UTF-8');


	$data->read($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$addressbook_excel_name);
	/*
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".iconv("utf-8","CP949",$addressbook_excel_name))){
		echo "파일있음";
	}*/
	//$data->read($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".iconv("utf-8","CP949",$addressbook_excel_name));

	error_reporting(E_ALL ^ E_NOTICE);
	$shift_num = 0;

	//echo $data->sheets[0]['numRows'];
	//exit;
	for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
		//print_r($data->sheets[0]['cells'][$i]);
		//exit;
			$user_name = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][1+$shift_num]);
			$mobile = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][2+$shift_num]);
			$email = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][3+$shift_num]);

			if(!$group_ix){
				$group_ix = $parent_group_ix;
			}
			$sql = "insert into shop_addressbook
							(ab_ix,company_id, group_ix,user_name,mobile,email,mail_yn,sms_yn,url,page,com_name,phone,fax,homepage,com_address,marketer,memo,regdate)
							values
							('','".$admininfo[company_id]."','$group_ix','$user_name','$mobile','$email','$mail_yn','$sms_yn','$url','$page','$com_name','$phone','$fax','$homepage','$com_address','$marketer','$memo',NOW())";
			$db->sequences = "SHOP_ADDRESSBOOK_SEQ";
			$db->query($sql);

	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메일링/SMS 주소록이 정삭적으로 입력 되었습니다.');parent.document.location='addressbook_list.php';</script>");
}
?>