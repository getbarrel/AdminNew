<?
session_start();
$PID  = strval($PID);
	if ($count <= 0 || $count == '')
	{
		$ESTIMATE_INTRA[$PID][pcount] = 1;
		$ESTIMATE_INTRA[$PID][sellprice] = $sellprice;
		$ESTIMATE_INTRA[$PID][totalprice] = $ESTIMATE_INTRA[$PID][sellprice]*$ESTIMATE_INTRA[$PID][pcount];
		
	}
	else
	{
		$ESTIMATE_INTRA[$PID][pcount] = round($count);
		$ESTIMATE_INTRA[$PID][sellprice] = $sellprice;
		$ESTIMATE_INTRA[$PID][totalprice] = $ESTIMATE_INTRA[$PID][sellprice]*$ESTIMATE_INTRA[$PID][pcount];
	}

	session_register("ESTIMATE_INTRA");


//hearder("Location:/cart.php");
echo "<script>parent.document.location.href='./estimate.intra.php?mode=$mode&est_ix=$est_ix';</script>";
?>