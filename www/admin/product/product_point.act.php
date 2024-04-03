<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include("../basic/company.lib.php");
$db = new Database;
$db2 = new Database;

 
//************셀러판매신용점수 관리 시작 *************
if ($act == "product_point_insert"){

	insertProductPoint($state,$use_state,$oid,$od_ix,$point,$pid,$etc,$admininfo); 
	 
	echo("<script>parent.opener.document.location.reload();parent.document.location.reload();parent.self.close();</script>");
}
 

?>
