<?
include("../../class/database.class");

$cid = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];

//header("Content-Type: application/x-javascript");	

$db = new Database;

if($cid){
	$sql = 	"SELECT abg.*
			FROM work_group abg 
			where group_depth = '$depth' and parent_group_ix = '$cid' and disp = 1 ";
	//echo $sql;
	$db->query($sql);
}else{
	//$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid = '' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	echo "<script language='javascript' >\n";
	echo "parent.document.forms['$form'].elements['".$target."'].length = 1; \n";
	echo "</script>";
	exit;
}
//echo "parent.document.forms['$form'].elements['selected_cid'].value = '".$cid."'; \n";
//echo "parent.document.forms['$form'].elements['selected_depth'].value = '".$depth."'; \n";
if ($db->total){

			//if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){
        
      //}
	    echo "<script language='javascript' >";
        echo "parent.document.forms['$form'].elements['".$target."'].length = ".($db->total+1)."; \n";
        //echo "parent.document.forms['$form'].elements['".$target."'].validation = 'true'; \n";
        echo "parent.document.forms['$form'].elements['".$target."'].options[0].selected = true; \n";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);

                echo "parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[group_name]."'; \n";
                echo "parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[group_ix]."'; \n";
        }
		echo "</script>";


}else{
			//if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){
      //  echo "parent.document.forms['$form'].elements['cid'].value = '".$cid."'; \n";
      //}
		echo "<script language='javascript' >";
      	//echo "parent.document.forms['$form'].elements['".$target."'].value = '".$cid."'; \n";
        echo "parent.document.forms['$form'].elements['".$target."'].length = 1; \n";
        //echo "parent.document.forms['$form'].elements['".$target."'].validation = 'false'; \n";
		echo "</script>";

}





?>