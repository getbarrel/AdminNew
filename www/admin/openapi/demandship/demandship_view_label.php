<?php
	header('Content-Type: application/pdf');

	//header("Content-Type: text/html; charset=UTF-8");
	include("../../class/layout.class");
	include_once("demandship.config.php");

	$od_ix = array_filter(array_map('trim',explode(',', $_GET['od_ix'])));
	$od_ix_cnt = count($od_ix);

	$db = new Database;
	$db2 = new Database;

	$sql = "select 
				ds_id, base64_label_pdf
			from
				shop_delivery_overseas
			where
				od_ix in ('". implode('\',\'', $od_ix) ."')";

	$db->query($sql);
	$ds_view = $db->fetchall();

	if(count($ds_view) > 0){
		foreach($ds_view as $od_k => $od_v){
			$content .= base64_decode(trim($od_v['base64_label_pdf']));
		}
	}else{
		echo '<script>window.close();</script>';
	}
	//echo $content;
/*
	$log_txt = $content;  
  
	$log_dir = $_SERVER["DOCUMENT_ROOT"]."/admin/openapi/demandship";   
	$log_file = fopen($log_dir."/log.pdf", "a");  
	fwrite($log_file, $log_txt."\r\n");  
	fclose($log_file);  
	*/

    $file = "log.pdf";
//	echo $file;
//	exit;
    $filename = 'Custom file name for the.pdf'; /* Note: Always use .pdf at the end. */

    header('Content-type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($file));
    header('Accept-Ranges: bytes');
  
    @readfile($file);

	$file2 = "log1.pdf";
//	echo $file;
//	exit;
    $filename = 'Custom file name for the.pdf'; /* Note: Always use .pdf at the end. */

    header('Content-type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($file2));
    header('Accept-Ranges: bytes');

    @readfile($file2);
?>