<?
include("../class/layout.class");
$page_title = "MMS 비용 통계";
$page_navigation = "메일링/SMS > SMS 발송 분석기 > MMS 비용 통계";
$include_menu = "campaign";

$Contents ="통계영역";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = campaign_menu();
$P->Navigation = $page_navigation;
$P->title = $page_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>