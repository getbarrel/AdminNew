<?
include("../../class/database.class");

$sc_ix = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];


$db = new Database;

if($sc_ix){
	$sql = "SELECT crfi.* FROM buyingservice_shopping_center_floor_info crfi
				where sc_ix = '$sc_ix'  ";
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
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[floor]." 층';
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[floor]."';
				</script>";
        }
		//exit;

}else{
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = 1;;
		</script>";
		exit;
}


if($sc_ix){
	$sql = "SELECT crli.* FROM buyingservice_shopping_center_line_info crli
				where sc_ix = '$sc_ix'  ";
	$db->query($sql);
}else{
	echo "<script type='text/javascript'>
		parent.document.forms['$form'].elements['".$target."'].length = 1;
	</script>";
	exit;
}

if ($db->total){
	echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['line'].length = ".($db->total+1).";
			parent.document.forms['$form'].elements['line'].options[0].selected = true;
		</script>\n";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);
				echo "<script type='text/javascript'>
					parent.document.forms['$form'].elements['line'].options[".($i+1)."].text = '".$db->dt[line]." 라인';
					parent.document.forms['$form'].elements['line'].options[".($i+1)."].value = '".$db->dt[line]."';
				</script>";
        }
		//exit;

}else{
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['line'].length = 1;;
		</script>";
		exit;
}

if($sc_ix){
	$sql = "SELECT sc.no FROM buyingservice_shopping_center sc
				where sc_ix = '$sc_ix'  ";
	$db->query($sql);
}else{
	echo "<script type='text/javascript'>
		parent.document.forms['$form'].elements[no].length = 1;
	</script>";
	exit;
}

if ($db->total){
	$db->fetch();
	echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['no'].length = ".($db->dt[no]+1).";
			parent.document.forms['$form'].elements['no'].options[0].selected = true;
		</script>\n";
		
        for($i=0; $i < $db->dt[no]; $i++){
				echo "<script type='text/javascript'>
					parent.document.forms['$form'].elements['no'].options[".($i+1)."].text = '".($i+1)." 호';
					parent.document.forms['$form'].elements['no'].options[".($i+1)."].value = '".($i+1)."';
				</script>";
        }
		//exit;

}else{
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['no'].length = 1;;
		</script>";
		exit;
}

?>