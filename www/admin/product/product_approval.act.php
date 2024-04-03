<?
include("../../class/database.class");
//print_r($_POST);
//exit;
session_start();

if($admininfo[company_id] == ""){
	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

$db = new Database;
$db2 = new Database;


if ($act == "update"){
//echo count($pid);
	/*
	if($approval_type == 1){
		$approval_str = "state = 1 ";
	}else if($approval_type == 2){
		$approval_str = "state = 1, disp = 1 ";
	}else if($approval_type == 3){
		$approval_str = "disp = 0 ";
	}else if($approval_type == 4){
		$approval_str = "state = 0 ";
	}
	*/

	for($i=0;$i<count($cpid);$i++){//disp='".$_POST["disp".$pid[$i]]."',

		if($admininfo[admin_level] == 9){
			$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET state = ".$state." , disp = ".$disp.",  editdate = NOW() Where id = '".$cpid[$i]."' "; // ,state = ".$state." , disp = '".$disp."'

			//echo $sql."<br><br>";
			//exit;
			$db->query ($sql);
		}

	}

	//$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET  stock='$stock',safestock = '$safestock', display='$display' Where id = $id ");

	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
	//header("Location:./product_list.php?view=innerview");
}

?>