<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;



if ($act == "update")
{
	$sql = "UPDATE reseller_policy SET
			rsl_ok='$rsl_ok',
			new_incentive_type='$new_incentive_type',
			new_incentive='$new_incentive',
			new_incentive_after='$new_incentive_after',
			incentive_type='$incentive_type',
			incentive_rate='$incentive_rate',
			incentive_way='$incentive_way'
			WHERE rsl_code='$code' ";
    
	//echo $sql;
	//exit;

	$db->query($sql);
			
	echo("<script>location.href = 'personal_rule_info.php?code=".$code."';</script>");

}

?>
