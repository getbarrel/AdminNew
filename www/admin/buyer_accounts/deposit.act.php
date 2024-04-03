<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include("../basic/company.lib.php");
$db = new Database;
$db2 = new Database;

//************예치금 *************
if ($act == "deposit_insert"){
	InsertDepositInfo($use_type,$state,$use_state,$oid,$deposit_ix,$deposit,$uid,$etc,$admininfo);
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "deposit_update"){
	
	if($state != '3' || $state != '8'){
		InsertDepositInfo($use_type,$state,$use_state,$oid,$deposit_ix,$deposit,$uid,$etc,$admininfo);
	}else{
		echo("<script>alert('이미 처리된 마일리지는 수정하실수 없습니다.');history.go(-1);</script>");
	}
}

if ($act == "update_state"){

	InsertDepositInfo($use_type,$state,$use_state,$oid,$deposit_ix,$deposit,$uid,$etc,$admininfo);
	echo("<script>top.location.reload();</script>");
}

if($act == "deposit_select_delete"){
	
	if($info_type == 'deposit_wating_list'){
		if(count($rid) > 0){
			for($i=0;$i<count($rid);$i++){
				
				$deposit_ix = $rid[$i];
				
				$sql = "select * from shop_deposit where deposit_ix = '".$deposit_ix."'";
				$db->query($sql);
				$dp_infos = $db->fetch();

				//입금대기 페이지에서 일괄 입금완료 처리 2014-07-23 이학봉
				InsertDepositInfo('P', '3', '7', $dp_infos[oid], $deposit_ix, $dp_infos[deposit], $dp_infos[uid], $dp_infos[etc], $admininfo);
			}	
		}
	}else if($info_type == 'deposit_refund_list'){
	
	
	}
	


	echo("<script>top.location.reload();</script>");
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

if ($act == "deposit_select_delete_dd"){

	if(count($rid) > 0){
		for($i=0;$i<count($rid);$i++){
			$db->query("DELETE FROM shop_deposit  WHERE shop_deposit='".$rid[$i]."'");
			//$db->query("DELETE FROM ".TBL_SHOP_RESERVE_INFO."  WHERE id='".$rid[$i]."'");
		}
	}
	echo("<script>top.location.reload();</script>");
}

?>
