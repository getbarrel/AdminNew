<?
include("../../class/database.class");

$cid = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];
//echo $form;
//header("Content-Type: application/x-javascript");

$db = new Database;
//echo ("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 <br><br>");
if($cid){
	//echo ("SELECT cid, cname FROM open_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
}else{
	//$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid = '' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	//echo "alert(1);\n";
	echo "<script type='text/javascript'>
		top.document.forms['$form'].elements['".$target."'].length = 1;
		top.document.forms['$form'].elements['c_cid'].value = '';
	</script>";
	exit;
}

if ($db->total){


        //echo "document.forms['$form'].elements['cid'].value = '".$cid."'; \n";
        echo "<script type='text/javascript'>
			top.document.forms['$form'].elements['".$target."'].length = ".($db->total+1).";
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');
			var op_len=top.document.forms['$form'].elements['".$target."'].options.length;
			for(var i=0;i<op_len;i++) {
				top.document.forms['$form'].elements['".$target."'].options[i].selected = false;
			}
			top.document.forms['$form'].elements['".$target."'].options[0].selected = true;
			top.document.forms['$form'].elements['c_cid'].value = '".$cid."';
			top.document.forms['$form'].elements['c_depth'].value = '".$depth."';
		</script>";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);


                echo "<script type='text/javascript'>
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[cname]."';
					top.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[cid]."';
				</script>";
        }
		exit;


}else{
        echo "<script type='text/javascript'>
			top.document.forms['$form'].elements['c_cid'].value = '".$cid."';
			top.document.forms['$form'].elements['c_depth'].value = '".$depth."';
			top.document.forms['$form'].elements['".$target."'].length = 1;
			top.document.forms['$form'].elements['".$target."'].setAttribute('validation','false');
		</script>";
		exit;

}





?>