<?
include("../../class/database.class");
session_start();
$md_ix = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];

//header("Content-Type: application/x-javascript");

$db = new Database;

if($md_ix){
	$sql = 	"SELECT * FROM shop_car_grade where disp=1 and md_ix = '".$md_ix."' order by vieworder asc ";
	//echo $sql;
	$db->query($sql);
}else{
	//$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid = '' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	echo "<script type='text/javascript'>
		parent.document.forms['$form'].elements['".$target."'].length = 1;
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
		</script>\n";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);
				echo "<script type='text/javascript'>
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[grade_name]."';
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[gr_ix]."';
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
		</script>";
		exit;
        //echo "document.forms['$form'].elements['".$target."'].validation = 'false'; \n";

}





?>