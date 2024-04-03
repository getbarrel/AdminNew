<?
include("../../class/database.class");
include("sellertool.lib.php");
include("../openapi/openapi.lib.php");


$site_code = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];
$obj = $_GET['obj'];
//echo $form;
//header("Content-Type: application/x-javascript");

$db = new Database;

if($site_code){
	 $sql = "SELECT * from sellertool_site_add_info where disp = 'Y' and site_code = '".$site_code."'";
	// echo $sql;
	$db->query($sql);
	$goods_addinfos = $db->fetchall("object");
}else{
	echo "<script type='text/javascript'>
		top.document.forms['$form'].elements['".$target."'].length = 1;
		top.document.forms['$form'].elements['cid2'].value = '';
	</script>";
	exit;
}



if (count($goods_addinfos) > 0){


        //echo "document.forms['$form'].elements['cid'].value = '".$cid."'; \n";
        echo "<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
		<script type='text/javascript'>
			top.document.forms['$form'].elements['".$target."'].length = ".(count($goods_addinfos)+1).";
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');
			var op_len=top.document.forms['$form'].elements['".$target."'].options.length;
			for(var i=0;i<op_len;i++) {
				top.document.forms['$form'].elements['".$target."'].options[i].selected = false;
			}
			top.document.forms['$form'].elements['".$target."'].options[0].selected = true;
		</script>";
        
  
        //for($i=0; $i < $db->total; $i++){
		for($i=0; $i < count($goods_addinfos); $i++){
			$category_info = (array)$goods_addinfos[$i];

                echo "<script type='text/javascript'>
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$category_info[add_info_name]."';					
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$category_info[ssai_ix]."';
					
				</script>";
        }
		exit;


}else{
        echo "<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
		<script type='text/javascript'>
			//alert('$depth');
			//sellertool_category
			top.document.forms['$form'].elements['".$target."'].length = 1;
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');

			
		</script>";
		exit;
}





?>