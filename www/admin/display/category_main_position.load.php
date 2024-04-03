<?
include("../../class/database.class");

$div_ix = $_GET['trigger'];
$target = $_GET['target'];
$form = $_GET['form'];

//header("Content-Type: application/x-javascript");	

$db = new Database;
if($div_ix){
	$db->query("SELECT * FROM shop_category_main_position where div_ix = '$div_ix'  ");
}else{	
	echo "<script type='text/javascript'>
			top.document.forms['$form'].elements['".$target."'].length = 1; \n
		</script>";
	exit;
}

if ($db->total){
	
	
	echo "<script type='text/javascript'>
	top.document.forms['$form'].elements['".$target."'].length = ".($db->total+1)."; \n
	top.document.forms['$form'].elements['".$target."'].options[0].selected = true; \n
	</script>";
	
	for($i=0; $i < $db->total; $i++){
		$db->fetch($i);
		
		
		echo "<script type='text/javascript'>
		top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[cmp_name]."'; \n
		top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[cmp_ix]."'; \n
		</script>";
	}
	
	
	
}else{	
	echo "<script type='text/javascript'>
	top.document.forms['$form'].elements['".$target."'].length = 1; \n
	top.document.forms['$form'].elements['".$target."'].validation = 'false'; \n
	</script>";
	
}


?>