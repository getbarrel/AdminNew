<?
include("../class/layout.class");
$page_title = "SMS 서비스 소개";
$page_navigation = "메일링/SMS > SMS 충전관리 > SMS 서비스소개";
$include_menu = "campaign";

$Contents ="이미지영역";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = campaign_menu();
$P->Navigation = $page_navigation;
$P->title = $page_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>