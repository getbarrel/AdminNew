<?
include("../../class/database.class");



$db = new Database;

if ($act == "update")
{

		
	$sql = 	"update ".TBL_SHOP_SHOPINFO." set 
			allthegate_id='$allthegate_id',allthegate_nointerest_use='$allthegate_nointerest_use',allthegate_nointerest_price='$allthegate_nointerest_price',allthegate_nointerest_str='$allthegate_nointerest_str',allthegate_interest_str='$allthegate_interest_str',escrow_use='$escrow_use',escrow_apply='$escrow_apply',escrow_method_bank='$escrow_method_bank',escrow_method_vbank='$escrow_method_vbank',escrow_method_card='$escrow_method_card'
			where mall_ix = '$mall_ix'";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'allthegate.php';</script>");
}

?>
