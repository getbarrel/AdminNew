<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;
	
	$idx = $_REQUEST[idx];

	$SQL = "UPDATE tax_sales SET send_type = '1' WHERE idx = '$idx'";
	$db->query($SQL);
?>
<script>
alert ("발행에 필요한 승인처리가 완료되었습니다.");
parent.window.close();
</script>