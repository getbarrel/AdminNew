<?

include("../../class/database.class");

$db = new Database;

	if ($vieworder != $_vieworder)
	{
		if ($vieworder != 0)
		{
			if ($vieworder < $_vieworder)
			{
				$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET vieworder=vieworder+1 WHERE vieworder < '$_vieworder' AND vieworder >= '$vieworder' AND vieworder <> '0'");
			}
			else
			{
				if ($_vieworder != 0)
				{
					$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET vieworder=vieworder-1 WHERE vieworder > '$_vieworder' AND vieworder <= '$vieworder' AND vieworder <> '0'");
				}
				else
				{
					$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET vieworder=vieworder+1 WHERE vieworder >= '$vieworder'");
				}
			}
		}
		else
		{
			$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET vieworder=vieworder-1 WHERE vieworder > '$_vieworder'");
		}
	}

	$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET vieworder='$vieworder' WHERE id='$pid'");

	echo("<script>location.href = './product_order_hit.php?view=innerview&cid=$cid&depth=$depth&max=$max&page=$page&nset=$nset';</script>");


?>
