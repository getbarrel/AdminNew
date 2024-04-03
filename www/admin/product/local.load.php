<?
header('Content-Type: text/html; charset=utf-8'); 

include("../../class/database.class");
session_start();
$sido = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];

//header("Content-Type: application/x-javascript");

$db = new Database;

if($sido){
	if($class_name == "sido"){
		$col_name = 'sigugun';
	}else if($class_name == "sigugun"){
		$col_name = 'dong';
	}
	$sql = 	"SELECT distinct ".$col_name." as local_name
			FROM shop_zip cr
			where  ".$class_name." = '$sido'  ";
	//echo $sql;
	$db->query($sql);
}else{

	echo "<script type='text/javascript'> 
		parent.document.forms['$form'].elements['".$target."'].length = 1;
	</script>";
	exit;
} 

if ($db->total){
 
		echo "<!--script src='../js/jquery-1.8.3.js'></script-->
		<script type='text/javascript'>
			//alert('".($db->total+1)."');
			//$('form[name^=$form]').find('select
			parent.document.forms['$form'].elements['".$target."'].length = ".($db->total+1).";
			//parent.document.getElementById('".$target."').length  = ".($db->total+1).";
			parent.document.forms['$form'].elements['".$target."'].options[0].selected = true;
			//parent.document.forms['$form'].getElementById('".$target."').options[0].selected = true;
		</script>\n";
//exit;
        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);
				echo "<script type='text/javascript'>
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[local_name]."';
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[local_name]."';
				</script>";
        } 

}else{ 
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = 1;;
		</script>";
		exit; 
}





?>