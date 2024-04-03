<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;
$db2 = new Database;
if(!$cid){
	$db->query("select page_cid,page_name,page_depth,shot_text,page_text,plevel1,plevel2,plevel3,(select count(page_cid) from shop_manual where page_depth =1 and plevel1 = a.plevel1) as ptotal from shop_manual a where page_depth = 0");
	
	if($db->total){
		$db2->query("select max(plevel1) from shop_manual where page_depth = 0");
		$db2->fetch();
		$plevel1 = $db2->dt[0] + 1;
		$json_names = array();
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			if($ptotal == 0){
				$ifLazy = false;
			}else{
				$ifLazy = true;
			}
			$json_names[] = array("key"=>$db->dt[page_cid],"title"=>$db->dt[page_name],"isFolder"=>true,"isLazy"=>$ifLazy,"depth"=>$db->dt[page_depth],"plevel1"=>$plevel1,"plevel2"=>$plevel2,"plevel3"=>$plevel3);
		}
		echo json_encode($json_names);
	}else{
		
	}
	
}else{
	$depth = $depth + 1;
	$db->query("select page_cid,page_name,page_depth,(select count(page_cid) from shop_manual where page_depth =2 and plevel1 = a.plevel1) as ptotal from shop_manual_category a where page_depth = '".$depth."' and plevel1 = '".$plevel1."' ");
	
	if($db->total){
		if($depth == "1"){
			$db2->query("select max(plevel2) from shop_manual where page_depth = '$depth' ");
			$db2->fetch();
			$plevel2 = $db2->dt[0] + 1;
		}else{
			$db2->query("select max(plevel3) from shop_manual where page_depth = '$depth' ");
			$db2->fetch();
			$plevel3 = $db2->dt[0] + 1;
		}
		$json_names = array();
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);

			if($ptotal == 0){
				$ifLazy = false;
			}else{
				$ifLazy = true;
			}
			$json_names[] = array("key"=>$db->dt[page_cid],"title"=>$db->dt[page_name],"isFolder"=>true,"isLazy"=>$ifLazy,"depth"=>$db->dt[page_depth],"plevel1"=>$plevel1,"plevel2"=>$plevel2,"plevel3"=>$plevel3);
		}
		echo json_encode($json_names);
	}else{

	}
}
?>