<?php
ini_set('memory_limit','-1');
/**
 * 카테고리 등록
 * 
 * @date 2013.11.07
 * @author hjy
 */
include ($_SERVER ["DOCUMENT_ROOT"] . "/class/layout.class");
//include($_SERVER ["DOCUMENT_ROOT"] . "/admin/sellertool/sellertool.lib.php");
$site_code = "cjmall";

/////////////////////////////////////////////// 제조사 ///////////////////////////////////////////////

$Infos[] = array("code"=>"362433","code_name"=>"디시지(DCG)");



$etc_div = "C";
$sql = "DELETE FROM sellertool_received_etc WHERE site_code = '".$site_code."' and etc_div='".$etc_div."'";
$db->query($sql);

foreach($Infos as $Info):
	
	$code = trim($Info['code']);
	$code_name = trim(str_replace("'","&#39;",$Info['code_name']));

	$sql = "INSERT INTO 
				sellertool_received_etc
			SET
				etc_div = '".$etc_div."',
				site_code = '".$site_code."',
				code_name = '".$code_name."',
				code = '".$code."',
				insert_date = NOW()
			";
	$db->query($sql);

endforeach;
exit;
