<?
	include("../class/layout.class");
 //	$db = new Database;

	include $DOCUMENT_ROOT."/admin/basic/seller.lump.php";

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = tax_menu();
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->Navigation = "세금계산서관리 > 거래처관리";
	$P->title = "거래처관리";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
?>
