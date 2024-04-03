<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

if ($act == "update")
{
	$sql = "UPDATE reseller_request SET
			content='$content'
			WHERE rq_ix='".$rq_ix."' ";
    

	$db->query($sql);
			
	echo("<script>location.href = 'request_view.php?rq_ix=".$rq_ix."';</script>");

}

if($act == "reseller_insert")
{

	$sql = "UPDATE reseller_request SET	state='2' WHERE rq_ix='".$rq_ix."' ";
	$db->query($sql);

	$sql = "select code from reseller_request where rq_ix='".$rq_ix."' ";
	$db->query($sql);
	$db->fetch();

	$code = $db->dt[code];

	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
	$shmop = new Shared("reseller_rule");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$reseller_rule = $shmop->getObjectForKey("reseller_rule");
	$reseller_rule = unserialize(urldecode($reseller_rule));

	$sql = "insert into reseller_policy (rsl_code,rsl_ok,new_incentive_type,new_incentive,new_incentive_after,incentive_type,incentive_rate,incentive_way,regdate)
	values('".$code."','y','".$reseller_rule[new_incentive_type]."','".$reseller_rule[new_incentive]."','".$reseller_rule[new_incentive_after]."','".$reseller_rule[incentive_type]."','".$reseller_rule[incentive_rate]."','".$reseller_rule[incentive_way]."',NOW())";
	$db->query($sql);

	echo("<script>history.back();</script>");

}

if($act == "reseller_drop")
{
	$db->query("UPDATE reseller_request SET	state='4' WHERE rq_ix='".$rq_ix."' ");


	$db->query("select * from reseller_request WHERE rq_ix='".$rq_ix."' ");
	$db->fetch();

	$rsl_code = $db->dt[code];
	$name = $db->dt[name];
	$email = $db->dt[email];
	$content = $db->dt[content];

	$db->query("select * from common_user WHERE code='".$rsl_code."' ");
	$db->fetch();

	$id = $db->dt[id];

	$db->query("UPDATE reseller_incentive SET ac_ix='0' WHERE rsl_code='".$rsl_code."' and ac_ix is null ");
	
	$db->query("DELETE FROM reseller_accounts WHERE rsl_code='".$rsl_code."' and status='AR' ");

	$db->query("INSERT INTO reseller_dropmember (rsl_code, id, message,dropdate,name,email) VALUES ('".$rsl_code."','".$id."','".$content."',NOW(),'".$name."','".$email."')");

	$db->query("DELETE FROM reseller_policy  WHERE rsl_code='".$rsl_code."'");


	echo("<script>history.back();</script>");

}

?>