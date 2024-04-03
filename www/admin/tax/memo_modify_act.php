<?
	//memo_modity_act.php
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;

	$idx = $_POST[idx];
	$memo = addslashes($_POST[memo]);

	$SQL = "
	UPDATE 
		tax_sales 
	SET 
		memo = '$memo' 
	WHERE
		idx = '$idx'
	";
	$db->query($SQL);
?>
<script>alert ("메모내용이 수정되었습니다.");</script>