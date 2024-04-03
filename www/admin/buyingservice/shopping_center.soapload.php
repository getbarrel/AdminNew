<?
include("../../class/database.class");

$ca_code = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];


$db = new Database;

if($ca_code){
	$sql = "SELECT sc.* FROM buyingservice_shopping_center sc left join buyingservice_commercial_area ca on (sc.ca_ix=ca.ca_ix)
				where ca.ca_code = '$ca_code'  ";
	echo $sql;
	$db->query($sql);
}else{
	echo "<script type='text/javascript'>
		parent.document.forms['$form'].elements['".$target."'].length = 1;
	</script>";
	exit;
}

if ($db->total){
	echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = ".($db->total+1).";
			parent.document.forms['$form'].elements['".$target."'].options[0].selected = true;
		</script>\n";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);
				echo "<script type='text/javascript'>
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt["sc_name_".$_SESSION["admininfo"]["language"]]."';
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt["sc_code"]."';
				</script>";
        }
		//exit;

}else{
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = 1;
		</script>";
		exit;
}

?>