<?
include("../../class/database.class");

$cid = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];
//echo $form;
header("Content-Type: application/x-javascript");

$db = new Database;
//echo ("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 <br><br>");
if($cid){
	//echo ("SELECT cid, cname FROM open_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
}else{
	//$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid = '' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	echo "document.forms['$form'].elements['".$target."'].length = 1; \n";
	echo "document.forms['$form'].elements['cid2'].value = ''; \n";
	exit;
}

if ($db->total){


        //echo "document.forms['$form'].elements['cid'].value = '".$cid."'; \n";
        echo "document.forms['$form'].elements['".$target."'].length = ".($db->total+1)."; \n";
        echo "document.forms['$form'].elements['".$target."'].validation = 'false'; \n";
        echo "document.forms['$form'].elements['".$target."'].options[0].selected = true; \n";
		echo "document.forms['$form'].elements['cid2'].value = '".$cid."'; \n";
		echo "document.forms['$form'].elements['depth'].value = '".$depth."'; \n";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);


                echo "document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[cname]."'; \n";
                echo "document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[cid]."'; \n";
        }



}else{
        echo "document.forms['$form'].elements['cid2'].value = '".$cid."'; \n";
				echo "document.forms['$form'].elements['depth'].value = '".$depth."'; \n";
        echo "document.forms['$form'].elements['".$target."'].length = 1; \n";
        echo "document.forms['$form'].elements['".$target."'].validation = 'false'; \n";

}





?>