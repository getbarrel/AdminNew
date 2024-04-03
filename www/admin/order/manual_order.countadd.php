<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$cart_key  = strval($cart_key);
	if ($count <= 0 || $count == '')
	{
		$ESTIMATE_INTRA[$cart_key][pcount] = 1;
		$ESTIMATE_INTRA[$cart_key][sellprice] = $sellprice;
		$ESTIMATE_INTRA[$cart_key][totalprice] = $ESTIMATE_INTRA[$cart_key][sellprice]*$ESTIMATE_INTRA[$cart_key][pcount];
		
	}
	else
	{
		$ESTIMATE_INTRA[$cart_key][pcount] = round($count);
		$ESTIMATE_INTRA[$cart_key][sellprice] = $sellprice;
		$ESTIMATE_INTRA[$cart_key][totalprice] = $ESTIMATE_INTRA[$cart_key][sellprice]*$ESTIMATE_INTRA[$cart_key][pcount];
	}

	session_register("ESTIMATE_INTRA");


//hearder("Location:/cart.php");
echo "<script>parent.document.location.reload();</script>";
//echo "<script>parent.document.location.href='./manual_order.cart.php';</script>";
?>