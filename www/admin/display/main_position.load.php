<?
include("../../class/database.class");

$div_ix = $_GET['trigger'];
$div_depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];

//header("Content-Type: application/x-javascript");	

$db = new Database;
if($div_ix){
	$db->query("SELECT mp_ix, mp_name FROM shop_main_position where  div_ix = '$div_ix'  ");
}else{	
	echo "<script type='text/javascript'>";
	echo "parent.document.forms['$form'].elements['".$target."'].length = 1; \n";
	echo "</script>";
	exit;
}

if ($db->total){
	
	echo "<script type='text/javascript'>";
	echo "parent.document.forms['$form'].elements['".$target."'].length = ".($db->total+1)."; \n";
	echo "parent.document.forms['$form'].elements['".$target."'].options[0].selected = true; \n";
	
	for($i=0; $i < $db->total; $i++){
		$db->fetch($i);
		
		
		echo "parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[mp_name]."'; \n";
		echo "parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[mp_ix]."'; \n";
	}
	
	echo "</script>";
	
}else{	
	echo "<script type='text/javascript'>";
	echo "parent.document.forms['$form'].elements['".$target."'].length = 1; \n";
	echo "parent.document.forms['$form'].elements['".$target."'].validation = 'false'; \n";
	echo "</script>";
}



?>