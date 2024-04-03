<?
$static_div = "date";
$static_div_name = "일자별";

$regdate = 1;
$search_date_type = 1;
if($sdate == "" || $edate == ""){
	
	$_GET["sdate"] = date("Y-m-d", time()-84600*30);
	$_GET["edate"] = date("Y-m-d");
}
include "static_base.php";
