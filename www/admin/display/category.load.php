<?
include("../../class/database.class");

$bbs_div = $_GET['trigger'];
$div_depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];

header("Content-Type: application/x-javascript");	

$db = new Database;
if($bbs_div){
	$db->query("SELECT div_ix, div_name FROM bbs_manage_div where div_depth ='".($div_depth+1)."' and parent_div_ix = '$bbs_div'  ");
}else{	
	echo "document.forms['$form'].elements['".$target."'].length = 1; \n";
	exit;
}

if ($db->total){
	
	
	echo "document.forms['$form'].elements['".$target."'].length = ".($db->total+1)."; \n";
	echo "document.forms['$form'].elements['".$target."'].options[0].selected = true; \n";
	
	for($i=0; $i < $db->total; $i++){
		$db->fetch($i);
		
		
		echo "document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[div_name]."'; \n";
		echo "document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[div_ix]."'; \n";
	}
	
	
	
}else{	
	echo "document.forms['$form'].elements['".$target."'].length = 1; \n";
	echo "document.forms['$form'].elements['".$target."'].validation = 'false'; \n";
	
}



function getCategoryList($category_text ="기본카테고리 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	$mdb->query("SELECT * FROM open_category_info where depth ='$depth' and cid LIKE '".substr($cid,0,$depth-1)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	
	$mstring = "<Select name='$object_name' onchange=\"oChangeCategory(this);\" style='width:160px;'>";
	
	if ($mdb->total){
			$mstring = $mstring."<option value=''>$category_text</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			if($cid == $mdb->dt[cid]){
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>";
			}
		}
	}else{
	$mstring = $mstring."<option value=''> $category_text</option>";
	}
	
	$mstring = $mstring."</Select>";
	
	return $mstring;
}


?>