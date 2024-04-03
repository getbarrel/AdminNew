<?
include("../class/layout.class");


$Contents ='<iframe name="happytalk" id="happytalk" src="http://mcs2.sweettracker.net/auth/login" width="1400px" height="800px" frameborder="0"></iframe>';


$P = new LayOut();
$P->addScript = $Script;
if($regdate!=1) $P->OnloadFunction = "";
else $P->OnloadFunction = "";
$P->strLeftMenu = cscenter_menu();
$P->Navigation = "고객센타 > 해피톡";
$P->title = "해피톡";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
