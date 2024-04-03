<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

$db->query("SELECT deepzoom_file FROM deepzoom_image WHERE di_ix='$di_ix'");
$db->fetch();
$deepzoom_file = $db->dt[deepzoom_file];



$doc = new DOMDocument();
$local_xml_path = $_SERVER["DOCUMENT_ROOT"]."/".$admin_config[mall_data_root]."/deepzoom/".$di_ix."/".$deepzoom_file.".xml";
$xml_path = "http://".$_SERVER["HTTP_HOST"]."/".$admin_config[mall_data_root]."/deepzoom/".$di_ix."/".$deepzoom_file.".xml";

if (!file_exists($local_xml_path)){
	echo $xml_path."<br>딥줌이 존재하지 않습니다.";
	exit;
}


$doc->load($xml_path);
$outXML = $doc->saveXML(); 
//echo nl2br($outXML);
$xml = simplexml_load_string($outXML);
$attr = $xml->Size[0]->attributes();
$Width = $attr->Width;
$Height = $attr->Height;

/*
$xml = new SimpleXMLElement($outXML);

$result = $xml->xpath("/Image/*");
echo $result[0];
exit;


$xpath = new DOMXpath($doc);
$params = $xpath->query("//Image/*");
foreach ($params as $param) {		
	echo $param->getElementsByTagName("Size");
}
//echo $params[0];
exit;
$group_ix = $param->getElementsByTagName("Size")->item(0)->nodeValue;
*/
if($mode == "copy"){
	$mstring =  "<script type=\"text/javascript\" src=\"http://".$_SERVER["HTTP_HOST"]."/admin/deepzoom/js/vesview.js\" ></script>\n";
	$mstring .=  "<script type=\"text/javascript\" >VES('VEScontainer', '".$xml_path."','".$Width."','".$Height."');</script>";
	echo $mstring;
}else{
?>
 <html>
    <head>
    <title>Test Page</title>
    </head>
    <body>
	<!--div id="VEScontainer" style="width:100%;height:100%"></div-->
    <script type="text/javascript" src="js/vesview.js" ></script>
	<script type="text/javascript" >VES('VEScontainer', '<?=$xml_path?>','<?=$Width?>','<?=$Height?>');</script>
    </body>
 </html>
<?
}	
?>