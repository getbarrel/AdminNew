<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;
	
	$from = $_POST[from];

	$idx = $_POST[idx];
	$s_type = $_POST[s_type];
	$company_number = $_POST[company_number];
	$c_name = $_POST[c_name];
	$c_ceo = $_POST[c_ceo];
	$zip1 = $_POST[zip1];
	$zip2 = $_POST[zip2];
	$addr1 = $_POST[addr1];
	$addr2 = $_POST[addr2];
	$c_status = $_POST[c_status];
	$c_items = $_POST[c_items];
	$c_personin = $_POST[c_personin];
	$email = $_POST[email_id]."@".$_POST[email_com];
	if($_POST[c_tel1] != "")	$c_tel = $_POST[c_tel1]."-".$_POST[c_tel2]."-".$_POST[c_tel3];
	if($_POST[c_mobile1] != "")	$c_mobile = $_POST[c_mobile1]."-".$_POST[c_mobile2]."-".$_POST[c_mobile3];
	if($_POST[c_fax1] != "")	$c_fax = $_POST[c_fax1]."-".$_POST[c_fax2]."-".$_POST[c_fax3];
	$c_position = $_POST[c_position];
	$c_memo = $_POST[c_memo];

	
	if($idx == "")
	{
		$SQL_C = "SELECT count(*) as cnt FROM tax_company_info WHERE company_number = '$company_number'";
		$db->query($SQL_C);
		$db->fetch();
		if($db->dt[cnt] > 0)
		{
			echo "<script>alert ('이미 등록된 사업자번호 또는 주민등록번호입니다.');</script>";
			die;
		}

		$SQL = "
		insert into 
			tax_company_info 
		set
			c_type = '$s_type',
			company_number = '$company_number',
			company_name = '$c_name',
			ceo = '$c_ceo',
			zip1 = '$zip1',
			zip2 = '$zip2',
			addr1 = '$addr1',
			addr2 = '$addr2',
			company_status = '$c_status',
			company_items = '$c_items',
			personin = '$c_personin',
			email = '$email',
			tel = '$c_tel',
			mobile = '$c_mobile',
			fax = '$c_fax',
			c_position = '$c_position',
			memo = '$c_memo',
			signdate = now()
		";
		echo $SQL;
		$db->query($SQL);
		
		if($from == "company_list")	echo "<script>alert ('신규 거래처가 등록되었습니다.'); parent.opener.location.reload(); parent.window.close();</script>";
		else						echo "<script>alert ('신규 거래처가 등록되었습니다.'); parent.window.close();</script>";
	}
	else
	{
		$SQL = "
		update 
			tax_company_info 
		set
			company_name = '$c_name',
			ceo = '$c_ceo',
			zip1 = '$zip1',
			zip2 = '$zip2',
			addr1 = '$addr1',
			addr2 = '$addr2',
			company_status = '$c_status',
			company_items = '$c_items',
			personin = '$c_personin',
			email = '$email',
			tel = '$c_tel',
			mobile = '$c_mobile',
			fax = '$c_fax',
			c_position = '$c_position',
			memo = '$c_memo'
		where
			idx = '$idx'
		";
		echo $SQL;
		$db->query($SQL);

		echo "<script>alert ('정보가 수정되었습니다.'); parent.opener.location.reload();</script>";
	}
?>
