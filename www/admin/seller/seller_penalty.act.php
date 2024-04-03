<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include("../basic/company.lib.php");
$db = new Database;
$db2 = new Database;

//************셀러판매신용점수 관리 시작 *************
if ($act == "seller_penalty_insert"){

	InsertPenaltyInfo($state,$use_state,$oid,$od_ix,$penalty,$company_id,$etc,$admininfo);

	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "reserve_delete")
{	
	$sql = "select
				reserve_id
			from
				".TBL_SHOP_RESERVE_INFO." 
			where
				id = '".$id."'";
	$db->query($sql);
	$db->fetch();
	$reserve_id = $db->dt[reserve_id];
	$db->query("DELETE FROM shop_reserve  WHERE reserve_id='$reserve_id'");
	$db->query("DELETE FROM ".TBL_SHOP_RESERVE_INFO."  WHERE id='$id'");

	//echo("<script>top.location.href = 'reserve.pop.php?code=$uid';</script>");
	echo("<script>top.location.reload();</script>");
}

if ($act == "deposit_select_delete"){

	if(count($rid) > 0){
		for($i=0;$i<count($rid);$i++){
			$db->query("DELETE FROM shop_deposit  WHERE shop_deposit='".$rid[$i]."'");
			//$db->query("DELETE FROM ".TBL_SHOP_RESERVE_INFO."  WHERE id='".$rid[$i]."'");
		}
	}
	echo("<script>top.location.reload();</script>");
}

?>
