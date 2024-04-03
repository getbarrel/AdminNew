<?

include("../../class/database.class");
include $_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php";
$db = new Database;

if($mode == 'excelUpd'){
	include '../include/phpexcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$allData = array();

	include("../include/lib/pclzip.lib.php");

    PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

    date_default_timezone_set('Asia/Seoul');

    copy($product_file, $_SERVER["DOCUMENT_ROOT"] . "" . $admin_config[mall_data_root] . "/images/upfile/" . iconv("UTF-8", "EUC-KR", $_FILES['product_file']['name']));

	$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"] . "" . $admin_config[mall_data_root] . "/images/upfile/" . iconv("UTF-8", "EUC-KR", $_FILES['product_file']['name']));

	$sheet = $objPHPExcel->getSheet(0);                             // 0번 시트
	$maxRow = $sheet->getHighestRow();                              // 마지막 줄

	for ($rownum = 2; $rownum <= $maxRow; $rownum++) {
		$sql = "UPDATE " . TBL_SHOP_PRODUCT_RELATION . " SET " . $objPHPExcel->getActiveSheet()->getCell('O' . $rownum)->getValue() . "='" . $objPHPExcel->getActiveSheet()->getCell('M' . $rownum)->getValue() . "' WHERE rid ='" . $objPHPExcel->getActiveSheet()->getCell('N' . $rownum)->getValue() . "' ";
		$db->query($sql);
    }
}else{
	ksort($sort);

	$sortDepth = 'sortdepth' . $depth;
	$sort_cid = SetLikeCategory($cid);

	for($i=0;$i < count($sno);$i++){

		$sql = "select pid from ".TBL_SHOP_PRODUCT_RELATION." WHERE rid ='" . $sno[$i] . "'";
		$db->query($sql);
		$db->fetch();
		$pid = $db->dt['pid'];

		$sql = "select count(*) cnt from ".TBL_SHOP_PRODUCT_RELATION." where cid like '".$sort_cid."%' and pid = '".$pid."'";
		$db->query($sql);
		$db->fetch();
		$cnt = $db->dt['cnt'];

		if($cnt > 1){
			$sql = "UPDATE " . TBL_SHOP_PRODUCT_RELATION . " SET " . $sortDepth . "='" . $sort[$i] . "' WHERE pid ='" . $pid . "' and cid like '".$sort_cid."%' ";
			$db->query($sql);
		}else{
			$sql = "UPDATE " . TBL_SHOP_PRODUCT_RELATION . " SET " . $sortDepth . "='" . $sort[$i] . "' WHERE rid ='" . $sno[$i] . "' ";
			$db->query($sql);
		}


	}
}
echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('처리 완료');</script>");
echo("<script>parent.setCategory('$cname','$cid',$depth,'$id','".$company_id."','".$max."');</script>");
//echo("<script>parent.document.location.reload();</script>");
exit;

/*$db = new Database;

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

	echo("<script>location.href = './product_order.php?view=innerview&cid=$cid&depth=$depth&max=$max&page=$page&nset=$nset';</script>");
*/

?>
