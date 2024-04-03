<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;
	
	$idx = $_GET[idx];
	$chk = $_POST[chk];
	$del_num = sizeof($chk);
	
	if($idx != "")
	{
		$SQL = "DELETE FROM tax_company_info WHERE idx = '$idx'";
		$db->query($SQL);
	}
	else
	{
		for($i=0; $i < $del_num; $i++)
		{
			//echo $chk[$i]."<br>";
			
			$idx = $chk[$i];
			$SQL = "DELETE FROM tax_company_info WHERE idx = '$idx'";
			$db->query($SQL);
		}
	}
?>
<script>
alert ("삭제처리되었습니다.");
parent.location.reload();
</script>