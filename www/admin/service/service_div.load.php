<?
include("../../class/database.class");
session_start();
$service_code = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];

//header("Content-Type: application/x-javascript");

$db = new Database;

if($service_code){
	

	$sql = 	"SELECT sd.service_ix
			FROM service_division sd
			where service_code = '$service_code'  ";
	//echo $sql;
	$db->query($sql);
	$db->fetch();
	$service_ix = $db->dt[service_ix];

	$sql =  "SELECT sd.* FROM service_division sd
				where depth = '$depth' and parent_service_ix = '$service_ix'  ";
	//echo $sql;
	$db->query($sql);
}else{
	//$db->query("SELECT service_ix, cname FROM shop_category_info where depth ='".($depth+1)."' and service_ix = '' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	echo "<script type='text/javascript'>
		parent.document.forms['$form'].elements['".$target."'].length = 1;
	</script>";
	exit;
}
//echo "document.forms['$form'].elements['selected_service_ix'].value = '".$service_ix."'; \n";
//echo "document.forms['$form'].elements['selected_depth'].value = '".$depth."'; \n";
if ($db->total){

			//if($target == "service_ix0_1" || $target == "service_ix1_1" || $target == "service_ix2_1" || $target == "service_ix3_1"){

      //}
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = ".($db->total+1).";
			parent.document.forms['$form'].elements['".$target."'].options[0].selected = true;
		</script>\n";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);
				echo "<script type='text/javascript'>
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[service_name]."';
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[service_code]."';
				</script>";
        }
		exit;

}else{
			//if($target == "service_ix0_1" || $target == "service_ix1_1" || $target == "service_ix2_1" || $target == "service_ix3_1"){
      //  echo "document.forms['$form'].elements['service_ix'].value = '".$service_ix."'; \n";
      //}
      	//echo "document.forms['$form'].elements['".$target."'].value = '".$service_ix."'; \n";
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = 1;;
		</script>";
		exit;
        //echo "document.forms['$form'].elements['".$target."'].validation = 'false'; \n";

}





?>