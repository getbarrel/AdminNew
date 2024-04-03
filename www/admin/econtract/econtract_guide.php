<?
include("../class/layout.class");
include("./contract.lib.php");


$Contents = "
	<img src='/admin/images/korea/econtract_guide.jpg' alt='전자계약 안내공지' />
";

 $Script = "
 
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = econtract_menu();
$P->strContents = $Contents;
$P->Navigation = "전자계약 > 전자계약 안내공지";
$P->title = "전자계약 안내공지";
echo $P->PrintLayOut();


?>