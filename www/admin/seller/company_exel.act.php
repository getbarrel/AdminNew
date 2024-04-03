<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");


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
		$name = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][1+$shift_num]);
		$id = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][2+$shift_num]);
		$pass = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][3+$shift_num]);
		$mail = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][4+$shift_num]);
		$tel = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][5+$shift_num]);
		$pcs = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][6+$shift_num]);
		$zipcode = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][7+$shift_num]);
		$addr1 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][8+$shift_num]);
		$addr2 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][9+$shift_num]);
		$shop_name = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][10+$shift_num]);
		$com_ceo = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][11+$shift_num]);
		$com_name = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][12+$shift_num]);
		$com_number = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][13+$shift_num]);
		$com_email = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][14+$shift_num]);
		$com_zip = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][15+$shift_num]);
		$com_addr1 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][16+$shift_num]);
		$com_addr2 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][17+$shift_num]);
		$com_phone = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][18+$shift_num]);
		$md_id = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][19+$shift_num]);
		$bank_name = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][20+$shift_num]);
		$bank_owner = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][21+$shift_num]);
		$bank_number = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][22+$shift_num]);

		//print_r($data->sheets[0]['cells']);
		//exit;

		$gp_ix = "44";

		$id    = trim($id);
		$pass  = trim($pass);
		$pass = hash("sha256", $pass);

		$mem_name  = trim($name);
		$nick_name  = trim($nick_name);
		//$mail  = trim($mail1."@".$mail2);
		$addr1 = trim($addr1);
		$addr2 = trim($addr2);
		$code  = md5(uniqid(rand()));

		$company_id  = md5(uniqid(rand()));

		$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id = '$id'");

		if ($db->total) {
			$db->fetch();
			//syslog(LOG_INFO, $i.' : '.$id.' 패스함.\r\n');
			/**
			syslog(LOG_INFO, $i.' : '.$id.' 삭제.\r\n');
			$sql = "delete from ".TBL_COMMON_COMPANY_DETAIL." where company_id ='".$db->dt[company_id]."'";
			//echo $sql;
			$db2->query($sql);

			$sql = "select code from ".TBL_COMMON_USER." where company_id ='".$db->dt[company_id]."'";
			$db2->query($sql);
			$total = $db2->total;
			$db2->fetch($i);

			$code = $db2->dt[code];

			$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL."  where code = '".$code."' ";
			//echo $sql;
			$db2->query($sql);

			$sql = "delete from ".TBL_COMMON_USER."  where id = '".$id."' ";
			//echo $sql;
			$db2->query($sql);
				**/

			$passNo++;
		} else {

			if($md_id) {
				$sql ="SELECT cu.code, cmd.team FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd WHERE cu.code = cmd.code AND cu.id = '$md_id' ";
				$db->query($sql);
				$db->fetch();

				$md_code = $db->dt[code];
				$team = $db->dt[team];
			}

			//$sql = "INSERT INTO ".TBL_COMMON_USER."	(code, id, pw, mem_type, date, visit, last, ip, company_id, language, auth, authorized,join_auth)VALUES				('$code','$id','".$pass."','S',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id', 'indonesian', '4', 'Y','Y')";

			//join_auth 없어서 삭제 홍진영

			if($db->dbms_type == "oracle"){
				$sql = "INSERT INTO ".TBL_COMMON_USER."	(code, id, pw, mem_type, date_, visit, last, ip, company_id, language, auth, authorized) VALUES ('$code','$id','".$pass."','S',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id', 'indonesian', '4', 'Y')";
			}else{
				$sql = "INSERT INTO ".TBL_COMMON_USER."	(code, id, pw, mem_type, date, visit, last, ip, company_id, language, auth, authorized) VALUES ('$code','$id','".$pass."','S',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id', 'indonesian', '4', 'Y')";
			}
			$db->query($sql);


			if($db->dbms_type == "oracle"){
				$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
					VALUES
					('$code',AES_ENCRYPT('".$db->ase_encrypt_key."'),'$birthday','$birthday_div',AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."'),AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'),AES_ENCRYPT('$zipcode','".$db->ase_encrypt_key."'),AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."'),AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."'),AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),'$tel_div',AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
			}else{
				$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
					VALUES
					('$code',HEX(AES_ENCRYPT('".$db->ase_encrypt_key."')),'$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zipcode','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
			}
			$db->query($sql);

			/*
			$sql = "INSERT INTO ".TBL_COMMON_COMPANY_DETAIL." set
				company_id = '$company_id'
				, com_name = '$com_name'
				, com_div = 'R'
				, com_type = 'S'
				, com_ceo = '$com_ceo'
				, com_business_status = '$com_business_status'
				, com_business_category = '$com_business_category'
				, com_number = '$com_number'
				, online_business_number = '$online_business_number'
				, com_phone = '$com_phone'
				, com_email = '$com_email'
				, com_fax = '$com_fax'
				, com_zip = '$com_zip'
				, com_addr1 = '$com_addr1'
				, com_addr2 = '$com_addr2'
				, seller_auth = 'Y'
				, regdate = now()
			";*/

			$sql = "insert into ".TBL_COMMON_COMPANY_DETAIL." (company_id,com_name,com_div,com_type,com_ceo,com_business_status,com_business_category,com_number,online_business_number,com_phone,com_fax,com_email,com_zip,com_addr1,com_addr2,seller_auth,regdate) values('$company_id','$com_name','R','S','$com_ceo','$com_business_status','$com_business_category','$com_number','$online_business_number','$com_phone','$com_fax','$com_email','$com_zip','$com_addr1','$com_addr2','Y',NOW())";

			$db->query($sql);

			$join_type="seller";

			$sql = "select order_excel_info1, order_excel_info2, order_excel_checked   from common_company_detail ccd, common_seller_detail csd
						where ccd.company_id = csd.company_id and com_type='A' ";
			$db->query($sql);
			$db->fetch();
			$order_excel_info1 = $db->dt[order_excel_info1];
			$order_excel_info2 = $db->dt[order_excel_info2];
			$order_excel_checked = $db->dt[order_excel_checked];

			/*
			$sql = "INSERT INTO common_seller_detail SET
						company_id = '$company_id',
						shop_name = '$shop_name',
						md_code = '$md_code',
						team = '$team',
						bank_owner = '$bank_owner',
						bank_name = '$bank_name',
						bank_number = '$bank_number',
						regdate = now()
						$bank_file_str
						$ktp_file_str
						";
			*/
			$sql = "insert into common_seller_detail (company_id,shop_name,md_code,team,bank_owner,bank_name,bank_number,order_excel_info1,order_excel_info2,order_excel_checked,regdate) values('$company_id','$shop_name','$md_code','$team','$bank_owner','$bank_name','$bank_number','$order_excel_info1','$order_excel_info2','$order_excel_checked',NOW())";
			$db->query($sql);

			/*
			$sql = "INSERT INTO common_seller_delivery SET
						company_id = '$company_id',
						commission = '0',
						delivery_policy = '1',
						delivery_basic_policy = '2',
						delivery_price = '2500',
						delivery_freeprice = '30000',
						delivery_free_policy = '1',
						delivery_product_policy = '3',
						regdate = now()
						";
			*/

			$sql = "insert into common_seller_delivery (company_id,commission,delivery_policy,delivery_basic_policy,delivery_price,delivery_freeprice,delivery_free_policy,delivery_product_policy,regdate) values('$company_id','0','1','2','2500','30000','1','3',NOW())";

			$db->query($sql);

		}
	}




	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name)){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
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
