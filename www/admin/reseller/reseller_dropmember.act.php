<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

if ($act == "dropmember_delete")
{
	$db->query("DELETE FROM reseller_request  WHERE code='$rsl_code'");

	$db->query("DELETE FROM reseller_dropmember WHERE rsl_code='$rsl_code'");
	echo("<script>top.location.href = 'reseller_dropmember.php';</script>");
}

?>
