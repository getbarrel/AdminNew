<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");


$db = new Database;




if ($act == "memsales_update")
{

	$db->query("select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name ,scd.dp_name,scd.dp_ix  from common_member_detail cmd left join shop_company_department scd on (cmd.department = scd.dp_ix) where code='".$code."'  ");
	$db->fetch();

	$name=$db->dt[name];
	$dp_name=$db->dt[dp_name];
	$dp_ix=$db->dt[dp_ix];


	foreach($sale as $key => $val){
		
		$db->query("select * from shop_member_sales_plan where plan_date ='".$key."' and code='$code' ");
		$db->fetch();
		$msp_ix = $db->dt[msp_ix];

		if($db->total){
			$db->query("update shop_member_sales_plan set name='$name',dp_ix='$dp_ix',dp_name='$dp_name',plan_price='$val' where msp_ix='$msp_ix' ");
		}else{
			$sql = "insert into shop_member_sales_plan(msp_ix,code,name,dp_ix,dp_name,plan_price,plan_date,regdate) values('','$code','$name','$dp_ix','$dp_name','$val','$key',NOW()) ";
			$db->query($sql);
		}
	}

	echo("<script>alert('정상적으로 저장되었습니다.');parent.location.reload();</script>");
}

?>