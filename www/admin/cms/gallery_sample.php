<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

$db->query("SELECT display_type FROM deepzoom_gallery_info WHERE dgi_ix='$dgi_ix'");
$db->fetch();
$display_type = $db->dt[display_type];


if($mode == "copy"){
	$mstring =  "<script type=\"text/javascript\" src=\"http://".$_SERVER["HTTP_HOST"]."/admin/deepzoom/gallery.js\" ></script>
		<script type=\"text/javascript\" >
			GelleryView('".$display_type."','".$dgi_ix."');
		</script>";
	echo $mstring;
}else{
	//echo $display_type;
	if($display_type == "0" && false){
		$db->query("SELECT di.* FROM deepzoom_image di, deepzoom_gallery_relation dgi WHERE dgi_ix='$dgi_ix' and di.di_ix = dgi.di_ix ");
		$datas = $db->fetchall2();
		//print_r($events);
		$datas = str_replace("\"true\"","true",json_encode($datas));
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	}else{
?>
	<html>
    <head>
    <title>Test Page</title>
    </head>
    <body>
		<script type="text/javascript" src="http://<?=$_SERVER["HTTP_HOST"]?>/admin/deepzoom/gallery.js" ></script>
		<!--script type="text/javascript" src="js/jquery.bxslider2.min.js" ></script>
		<script type="text/javascript" src="http://appengine-pipeline.googlecode.com/svn-history/r2/trunk/ui/jquery.json.min.js" ></script-->	
		<script type="text/javascript" >
			GelleryView('<?=$display_type?>','<?=$dgi_ix?>');
		</script>
	 </html>
<?
	}
}	
?>