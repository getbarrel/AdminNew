<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{

	if($etc == ""){
		$bank_name = $bank_name;
	}else{
		$bank_name = $etc;
	}
	$sql = "insert into ".TBL_SHOP_BANKINFO." 
				(bank_ix,bank_name,bank_number,bank_owner,disp,regdate) 
				values
				('','$bank_name','$bank_number','$bank_owner','$disp',NOW())";

	// 오라클일때 사용
	$db->sequences = "SHOP_BANKINFO_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('무통장 계좌가 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='bank.php';</script>");
}


if ($act == "update"){
		
	$sql = "update ".TBL_SHOP_BANKINFO." set							
				bank_name='$bank_name',bank_number='$bank_number',bank_owner='$bank_owner',disp='$disp' 
				where bank_ix='$bank_ix' ";

	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('무통장 계좌가 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'bank.php';</script>");
}

if ($act == "delete"){
	
	$sql = "delete from ".TBL_SHOP_BANKINFO." where bank_ix='$bank_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('무통장 계좌가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='bank.php';</script>");
}

?>
