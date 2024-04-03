<?php

include $_SERVER["DOCUMENT_ROOT"]."/class/mysql.class";
include "../include/phpexcel/Classes/PHPExcel.php";
include "inventory.lib.php";

$db = new MySQL;

if($act=="excelUpload")
{
	if ($excel_file_size > 0){
		copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
	}

	date_default_timezone_set('Asia/Seoul');
	$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
	
	$basic_col = "A";
	$rownum = 2;

	while (($objPHPExcel->getActiveSheet()->getCell($basic_col . $rownum)->getValue() != "") && ($rownum < 30000))
	{
		$gu_ix = $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue();
		$pi_ix = $objPHPExcel->getActiveSheet()->getCell('B' . $rownum)->getValue();
		$pcnt = $objPHPExcel->getActiveSheet()->getCell('C' . $rownum)->getValue();
		$bcnt = $objPHPExcel->getActiveSheet()->getCell('C' . $rownum)->getValue();
		
		$dataArr[] = $gu_ix."|".$pi_ix."|".$pcnt."|".$bcnt;

		$y++;
		$z++;
		$rownum++;
	}
}

if(!empty($dataArr))
{
	for($i=0; $i<count($dataArr); $i++)
	{
		$datas = explode("|", $dataArr[$i]);
		$gu_ix = $datas[0];
		$pi_ix = $datas[1];
		$pcnt = $datas[2];
		$bcnt = $datas[3];

		$sql = "SELECT
					pi.company_id,
					ps.pi_ix,
					(SELECT ps_ix FROM inventory_place_section ps2 WHERE ps2.pi_ix = ps.pi_ix AND ps2.section_type = 'P' LIMIT 1) AS pix,
					(SELECT ps_ix FROM inventory_place_section ps2 WHERE ps2.pi_ix = ps.pi_ix AND ps2.section_type = 'B' LIMIT 1) AS bix
				FROM inventory_place_section ps
				INNER JOIN inventory_place_info pi
				ON ps.pi_ix = pi.pi_ix
				WHERE
					ps.pi_ix = '".$pi_ix."'
				LIMIT 1
		";
		$db->query($sql);
		$db->fetch();
		$pi_ix = $db->dt["pi_ix"];
		$ps_ix = $db->dt["ps_ix"];
		$pix = $db->dt["pix"];
		$bix = $db->dt["bix"];
		$company_id = $db->dt["company_id"];

		if($moveType == "B")
		{
			$cnt = $pcnt;
			$Fix = $pix;
			$From = 2;
			$msg = "반품창고 현황 - 기본창고로 이동";
			$Htype = "IW";
		}
		else if($moveType == "GB")
		{
			$cnt = $pcnt;
			$Fix = $pix;
			$Tix = $bix;
			$From = 2;
			$To = 1;
			$msg = "반품창고 현황 - 양호->불량";
			$Htype = "ETC";
		}
		else if($moveType == "BG")
		{
			$cnt = $bcnt;
			$Fix = $bix;
			$Tix = $pix;
			$From = 2;
			$To = 1;
			$msg = "반품창고 현황 - 불량->양호";
			$Htype = "ETC";
		}
		else if($moveType == "BD")
		{
			$cnt = $bcnt;
			$Fix = $bix;
			$From = 2;
			$msg = "반품창고 현황 - 불량->반품";
			$Htype = "04";
		}
		else if($moveType == "BR")
		{
			$cnt = $bcnt;
			$Fix = $bix;
			$From = 2;
			$msg = "반품창고 현황 - 불량->폐기(손망실)";
			$Htype = "09";
		}

		//[S] gu_ix, 수량, company_id, pi_ix, ps_ix, 구분(1:입고|2:출고), msg
		itemStockInventory($gu_ix, $cnt, $company_id, $pi_ix, $Fix, $From, $Htype, $msg);
		if($moveType == "GB" || $moveType == "BG")
		{
			// 양->불, 불->양일때는 입고처리까지
			itemStockInventory($gu_ix, $cnt, $company_id, $pi_ix, $Tix, $To, $Htype, $msg);
		}
		//[E] gu_ix, 수량, company_id, pi_ix, ps_ix, 구분(1:입고|2:출고), msg
	}
	echo "
		<script type='text/javascript'>
			alert('정상적으로 처리되었습니다.');
			top.location.reload();
		</script>
	";
}

function itemStockInventory($gu_ix, $cnt, $company_id, $pi_ix, $ps_ix, $h_div, $h_type, $msg)
{
	$db = new MySQL;

	$sql = "SELECT
				g.gid,
				gu.unit,
				gu.sellprice as pt_dcprice,
				g.standard,
				'".$cnt."' as amount ,
				'".$company_id."' as company_id,
				'".$pi_ix."' as pi_ix,
				'".$ps_ix."' as ps_ix
			FROM
				inventory_goods g ,
				inventory_goods_unit gu
			WHERE
				g.gid = gu.gid
				AND gu.gu_ix = '".$gu_ix."'
	";
	$db->query($sql);
	$delivery_iteminfo = $db->fetchall();

	$item_info[pi_ix] = $pi_ix;
	$item_info[ps_ix] = $ps_ix;
	$item_info[company_id] = $company_id;
	$item_info[h_div] = $h_div;
	$item_info[vdate] = date("Ymd");
	$item_info[ioid] = "1".substr(date("YmdHis"),1)."-".rand(10000, 99999);
	$item_info[msg] = $msg;
	$item_info[h_type] = $h_type;
	$item_info[charger_name] = $_SESSION[admininfo]["charger"];
	$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
	$item_info[detail] = $delivery_iteminfo;

	UpdateGoodsItemStockInfo($item_info, $db);
}

?>