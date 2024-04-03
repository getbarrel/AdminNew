<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

$db->query("SELECT display_type FROM deepzoom_gallery_info WHERE dgi_ix='$dgi_ix'");
$db->fetch();
$display_type = $db->dt[display_type];


$sql = "SELECT concat('http://".$_SERVER["HTTP_HOST"]."/data/basic/deepzoom/',di.di_ix,'/s_',di.deepzoom_file) as src 
		FROM deepzoom_image di, deepzoom_gallery_relation dgi 
		WHERE dgi_ix='$dgi_ix' and di.di_ix = dgi.di_ix and dgi.dgi_ix='$dgi_ix' ";

$db->query($sql);
$datas = $db->fetchall2("object");
//print_r($datas);
//exit;
//$datas = str_replace("\"true\"","true",json_encode($datas));
//$datas = str_replace("\"false\"","false",$datas);
echo json_encode($datas);


?>