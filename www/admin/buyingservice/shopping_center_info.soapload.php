<?
include("../../class/database.class");

$sc_code = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];


$db = new Database;

if($sc_code){
	$sql = "SELECT * FROM buyingservice_shopping_center 
				where sc_code = '$sc_code'  ";
	$db->query($sql);
	$db->fetch();
	$sc_ix=$db->dt["sc_ix"];
}else{
	echo "<script type='text/javascript'>
		parent.document.forms['$form'].elements['".$target."'].length = 1;
		parent.document.forms['$form'].elements['line'].length = 1;
		parent.document.forms['$form'].elements['no'].length = 1;
	</script>";
	exit;
}


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
			parent.document.forms['$form'].elements['".$target."'].length = 1;
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
			parent.document.forms['$form'].elements['line'].length = 1;
		</script>";
		exit;
}

if($sc_ix){
	$sql = "SELECT start_no,end_no,(end_no-start_no) as no FROM buyingservice_shopping_center sc
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
			parent.document.forms['$form'].elements['no'].length = ".($db->dt[no]+2).";
			parent.document.forms['$form'].elements['no'].options[0].selected = true;
		</script>\n";
		
        for($i=$db->dt[start_no]; $i <= $db->dt[end_no]; $i++){
				echo "<script type='text/javascript'>
					parent.document.forms['$form'].elements['no'].options[".($i)."].text = '".($i)." 호';
					parent.document.forms['$form'].elements['no'].options[".($i)."].value = '".($i)."';
				</script>";
        }
		//exit;

}else{
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['no'].length = 1;
		</script>";
		exit;
}

?>