<?
include("../../class/database.class");

$pi_ix = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];
$h_type = $_GET['h_type'];
$h_div = $_GET['h_div'];
$mode = $_GET['mode'];
$section_type = $_GET['section_type'];

//echo $form;

//header("Content-Type: application/x-javascript");

$db = new Database;

if($section_type=='S'){
	$where = " and section_type ='S' ";
}elseif($section_type=='D'){
	$where = " and section_type ='D' ";
}

if($pi_ix){

	$db->query("SELECT ps_ix, section_name, section_type FROM inventory_place_section where pi_ix ='".($pi_ix)."'  and disp = '1' $where order by section_name ");
}else{

	echo "<script type='text/javascript'>top.document.forms['$form'].elements['".$target."'].length = 1;</script> \n";
	exit;
}

if($mode=="multiple"){
	if ($db->total){

			echo "<script type='text/javascript'>
				top.document.forms['$form'].elements['".$target."'].length = ".($db->total).";
				//top.document.forms['$form'].elements['".$target."'].setAttribute('validation','true');
				var op_len=top.document.forms['$form'].elements['".$target."'].options.length;
				for(var i=0;i<op_len;i++) {
					top.document.forms['$form'].elements['".$target."'].options[i].selected = false;
				}
				top.document.forms['$form'].elements['".$target."'].options[0].selected = true;
			</script>";

			for($i=0; $i < $db->total; $i++){
					$db->fetch($i);

					
					echo "<script type='text/javascript'>
						top.document.forms['$form'].elements['".$target."'].options[".($i)."].text = '".$db->dt[section_name]."';
						top.document.forms['$form'].elements['".$target."'].options[".($i)."].value = '".$db->dt[ps_ix]."';
						";
					if($h_type == "OW" && $h_div == 1 && $db->dt[section_type] == "D"){
						echo	"top.document.forms['$form'].elements['".$target."'].options[".($i)."].selected = true;";
					}else if($h_type == "OW" && $h_div == 2 && $db->dt[section_type] == "S"){
						echo	"top.document.forms['$form'].elements['".$target."'].options[".($i)."].selected = true;";
					}
						//if($h_type == "IW" && $db->dt[section_type] == "S"){
						//echo	"top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].selected = true;";
						//}
					echo "
					</script>";
			}

			
			exit;
	}else{
			
			echo "<script type='text/javascript'>top.document.forms['$form'].elements['".$target."'].length = 1;</script> \n";
			exit;
	}
}else{
	if ($db->total){

			echo "<script type='text/javascript'>
				top.document.forms['$form'].elements['".$target."'].length = ".($db->total+1).";
				//top.document.forms['$form'].elements['".$target."'].setAttribute('validation','true');
				var op_len=top.document.forms['$form'].elements['".$target."'].options.length;
				for(var i=0;i<op_len;i++) {
					top.document.forms['$form'].elements['".$target."'].options[i].selected = false;
				}
				top.document.forms['$form'].elements['".$target."'].options[0].selected = true;
			</script>";

			for($i=0; $i < $db->total; $i++){
					$db->fetch($i);

					
					echo "<script type='text/javascript'>
						top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[section_name]."';
						top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[ps_ix]."';
						";
					if($h_type == "OW" && $h_div == 1 && $db->dt[section_type] == "D"){
						echo	"top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].selected = true;";
					}else if($h_type == "OW" && $h_div == 2 && $db->dt[section_type] == "S"){
						echo	"top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].selected = true;";
					}
						//if($h_type == "IW" && $db->dt[section_type] == "S"){
						//echo	"top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].selected = true;";
						//}
					echo "
					</script>";
			}

			
			exit;
	}else{
			
			echo "<script type='text/javascript'>top.document.forms['$form'].elements['".$target."'].length = 1;</script> \n";
			exit;
	}
}




?>