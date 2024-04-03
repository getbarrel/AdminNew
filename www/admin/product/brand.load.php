<?
include("../../class/database.class");
session_start();
$parent_bd_ix = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];

//header("Content-Type: application/x-javascript");

$db = new Database;

if($parent_bd_ix){
	$sql = 	"SELECT cr.*
			FROM shop_brand_div cr
			where depth = '$depth' and parent_bd_ix = '$parent_bd_ix'  ";
	//echo $sql;
	$db->query($sql);
}else{

	echo "<script type='text/javascript'>
		parent.document.forms['$form'].elements['".$target."'].length = 1;
		if(parent.document.forms['$form'].elements['bd_ix2']) parent.document.forms['$form'].elements['bd_ix2'].value = '".$parent_bd_ix."';//추가 kbk 13/07/01
	</script>";
	exit;
}
//echo "document.forms['$form'].elements['selected_cid'].value = '".$cid."'; \n";
//echo "document.forms['$form'].elements['selected_depth'].value = '".$depth."'; \n";
if ($db->total){

			//if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){

      //}
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = ".($db->total+1).";
			parent.document.forms['$form'].elements['".$target."'].options[0].selected = true;
			if(parent.document.forms['$form'].elements['bd_ix2']) parent.document.forms['$form'].elements['bd_ix2'].value = '".$parent_bd_ix."';//추가 kbk 13/07/01
		</script>\n";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);
				echo "<script type='text/javascript'>
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[div_name]."';
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[bd_ix]."';
				</script>";
        }
		exit;

}else{
			//if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){
      //  echo "document.forms['$form'].elements['cid'].value = '".$cid."'; \n";
      //}
      	//echo "document.forms['$form'].elements['".$target."'].value = '".$cid."'; \n";
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = 1;;
			if(parent.document.forms['$form'].elements['bd_ix2']) parent.document.forms['$form'].elements['bd_ix2'].value = '".$parent_bd_ix."';//추가 kbk 13/07/01
		</script>";
		exit;
        //echo "document.forms['$form'].elements['".$target."'].validation = 'false'; \n";

}





?>