<?
include("../class/layout.class");
include("../../include/cash_manage.lib.php");

$db = new Database;

if ($act == "noaccept_withdraw")
{

	foreach($input_price as $key => $val){
		//미지급금입금 <--start-->
		//include("../../include/cash_manage.lib.php"); 에 있음
		$noaccept_data="";
		$noaccept_data[company_id]=$company_id;
		$noaccept_data[oid]=$key;
		$noaccept_data[price]=$val;
		$noaccept_data[msg]="[수동] 관리자 ".$admininfo[charger]." 처리";
		setNoacceptWithdraw($noaccept_data);
		//미지급금입금 <--end-->
	}

	echo("<script>alert('정상적으로 처리되었습니다.');parent.self.close();parent.opener.location.reload();</script>");
}

?>