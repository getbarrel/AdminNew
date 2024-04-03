<?
include("bbs.config.php");
$P = new msLayOut();

$msb = new MsBoard($bbs_table_name);
$msb->MsBoardConfigration($P);


//$P->addScript = PrintEvent(2,"pop");
$P->Contents = $msb->PrintMsBoardRead($article_no, $bbs_ix, $page)."<br>".$msb->PrintMsBoardResponse($bbs_ix, $article_no, $page);
$P->strLeftMenu = store_menu();
$P->Navigation = "HOME > 상점관리 > 입점업체공지사항";
echo $P->LoadLayOut();

?>