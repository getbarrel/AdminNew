<?
	# datafile_del.php
	
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;

	$idx = $_GET[idx];
	
	if($idx == "") die;

	$SQL = "DELETE FROM tax_datafile WHERE idx = '$idx'";
	$db->query($SQL);
?>
<script>
alert ("첨부파일이 삭제되었습니다.");
</script>