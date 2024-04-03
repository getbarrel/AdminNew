<?
include("../../class/database.class");

$ca_country = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];


$db = new Database;

if($ca_country){
	$sql = "SELECT crfi.* FROM buyingservice_commercial_area crfi
				where ca_country = '$ca_country'  ";
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
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt["ca_name_".$_SESSION["admininfo"]["language"]]."';
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt["ca_ix"]."';
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