<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");

		
$Contents .= "<script language='javascript'>viewMenual('$config', 800, 517)</script>";



$LO = new popLayOut;




$LO->addScript = $addScript;
$LO->OnloadFunction = "";
$LO->strContents = $Contents;
$LO->Navigation = "HOME > 메뉴얼 > 동영상메뉴얼";
$LO->PrintLayOut();




?>