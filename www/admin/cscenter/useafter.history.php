<?
include("../class/layout.class");

$Script = "
<script language='JavaScript' src='member.js'></Script>
<style>
.width_class {width:150px;}
input {border:1px solid #c6c6c6;padding:3px;}
.member_table td {text-align:left;}
</style>";

$db = new Database;
$mdb = new Database;
$cdb = new Database;
$rproduct_db = new Database;

$Contents = "통합관리로 추후 재협의";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "고객센타 > 상품후기 작성 이력";
$P->NaviTitle = "상품후기 작성 이력";
$P->title = "상품후기 작성 이력";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
