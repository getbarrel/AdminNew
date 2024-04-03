<?
include("../../class/database.class");


//header("Content-Type: application/x-javascript");

$db = new Database;

if($act == "getPlaceData")
{
	$sql = "SELECT
				pi.pi_ix,
				pi.place_name
			FROM inventory_place_info pi
			WHERE pi.company_id = '".$pi_ix."'
			ORDER BY place_name
	";

	$db->query($sql);
	$datas = array(	
		"datas" => $db->fetchall(), 
	);

	echo (json_encode($datas));
	exit;
}

if($act=="get_place_data_json"){
	$db->query("SELECT * FROM inventory_place_info where pi_ix = '".$_POST[pi_ix]."' ");
	$db->fetch("object");
	echo json_encode($db->dt);
	exit;
}


$company_id = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];
$mode = $_GET['mode'];
//echo $form;

if($company_id){

	//$db->query("SELECT pi_ix, place_name FROM inventory_place_section where pi_ix ='".($pi_ix)."'  and disp = '1' order by place_name ");
	$db->query("SELECT * FROM inventory_place_info where disp = 'Y' and company_id = '".$company_id."' ");

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
						top.document.forms['$form'].elements['".$target."'].options[".($i)."].text = '".$db->dt[place_name]."';
						top.document.forms['$form'].elements['".$target."'].options[".($i)."].value = '".$db->dt[pi_ix]."';
					</script>";
			}

			
			exit;
	}else{
			
			echo "<script type='text/javascript'>top.document.forms['$form'].elements['".$target."'].length = 0;</script> \n";
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
						top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[place_name]."';
						top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[pi_ix]."';
					</script>";
			}

			
			exit;
	}else{
			
			echo "<script type='text/javascript'>top.document.forms['$form'].elements['".$target."'].length = 1;</script> \n";
			exit;
	}
}




?>