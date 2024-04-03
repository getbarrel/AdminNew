<?php 

function ConvertLayout2()
//($dbname, $dbip, $dbuser, $dbpass, $skin_type, $templet_name, $table_name, $outputXmlPath){
{
	global $admininfo, $admin_config;
	///////////////////////////// 설정 ////////////////////////////
	$db = new Database(); 
	
	$dbname	= $db->db_name;
	$dbip	= $db->db_host;
	$dbuser = $db->db_user;
	$dbpass = $db->db_pass;
	
	$skin_type = $admin_config["mall_page_type"];	
		
	//$templet_name = 'stylestory';
	//$templet_name = 'mobile_blue';
	
	
	$templet_name =$admin_config["selected_templete"];
	
	if($admin_config[mall_page_type] == "MI"){
		$table_name = TBL_SHOP_MINISHOP_LAYOUT_INFO;
	}else{
		$table_name = 'shop_layout_info';
	}
	if($admin_config[mall_page_type] == "M"){
		$outputXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/mobile_templet/" . $admin_config["selected_templete"] . "/layout2.xml";
	}else if($admin_config[mall_page_type] == "MI"){
		$outputXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/minishop_templet/" . $admin_config["selected_templete"] . "/layout2.xml";
	}else{
		$outputXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml";
	}
	
///////////////////////////////////////////////////////////////

	//$conn = mysql_connect($dbip, $dbuser, $dbpass);
	//mysql_select_db($dbname);

	$SQL ="SELECT a.cid
	     , IFNULL(mall_ix, '" . $admininfo[mall_ix] . "') as mall_ix
			 , IFNULL(templet_name, '" . $templet_name . "') as templet_name
		   , IFNULL(skin_type, '" . $skin_type . "') as skin_type
	     , IFNULL(pcode, a.cid) as pcode
	     , depth, vlevel1, vlevel2, vlevel3, vlevel4, vlevel5,
	cname,path,basic_link, catimg, leftcatimg, subimg, category_top_view, category_display_type, bbs_name,
	category_use, is_layout_apply, page_type, page_path, page_link, page_title, page_help, page_addscript,
	page_body, page_desc, page_navi, layout, header1, header2, leftmenu, contents, contents_add, rightmenu,
	footer1, footer2, caching, caching_time, a.regdate
	   FROM " . $table_name . " a
	   LEFT OUTER
	   JOIN (SELECT * FROM shop_design
	   WHERE ((templet_name = '" . $templet_name . "')
	     AND  (skin_type    = '" . $skin_type ."')) ) b
	     ON a.cid = b.pcode
	 ORDER BY vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";
	//mysql_query("SET NAMES utf8");
	//echo nl2br($SQL);
	//exit;
 	//$result = mysql_query($SQL);
	$db->query($SQL);
	$results = $db->fetchall();

 	$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><layouts></layouts>");
 	if($admin_config[mall_page_type] == "MI"){
		$photoskins = $xml->addChild("photoskins");
		
		$photoskin = $photoskins->addChild("photoskin");
		$photoskin->addAttribute("photoskin_type", "3");
		$photoskin->addAttribute("photoskin_name", "snow");
		$photoskin->addChild("photoskin_path", "/data/basic/photoskin/dutch.jpg");

		$photoskin = $photoskins->addChild("photoskin");
		$photoskin->addAttribute("photoskin_type", "3");
		$photoskin->addAttribute("photoskin_name", "rain");
		$photoskin->addChild("photoskin_path", "/data/basic/photoskin/spring.jpg");

		$photoskin = $photoskins->addChild("photoskin");
		$photoskin->addAttribute("photoskin_type", "3");
		$photoskin->addAttribute("photoskin_name", "cloudy");
		$photoskin->addChild("photoskin_path", "/data/basic/photoskin/autumn.jpg");
		
		$photoskin = $photoskins->addChild("photoskin");
		$photoskin->addAttribute("photoskin_type", "3");
		$photoskin->addAttribute("photoskin_name", "clean");
		$photoskin->addChild("photoskin_path", "/data/basic/photoskin/evening.jpg");
		
		$photoskin = $photoskins->addChild("photoskin");
		$photoskin->addAttribute("photoskin_type", "2");
		$photoskin->addAttribute("photoskin_name", "night");
		$photoskin->addChild("photoskin_path", "/data/basic/photoskin/spring.jpg");
		
		$photoskin = $photoskins->addChild("photoskin");
		$photoskin->addAttribute("photoskin_type", "2");
		$photoskin->addAttribute("photoskin_name", "evening");
		$photoskin->addChild("photoskin_path", "/data/basic/photoskin/dutch.jpg");
		
		$photoskin = $photoskins->addChild("photoskin");
		$photoskin->addAttribute("photoskin_type", "2");
		$photoskin->addAttribute("photoskin_name", "afternoon");
		$photoskin->addChild("photoskin_path", "/data/basic/photoskin/Babycat_Cartoon_001.jpg");
		
		$photoskin = $photoskins->addChild("photoskin");
		$photoskin->addAttribute("photoskin_type", "2");
		$photoskin->addAttribute("photoskin_name", "morning");
		$photoskin->addChild("photoskin_path", "/data/basic/photoskin/night.jpg");
		
		$photoskin = $photoskins->addChild("photoskin");
		$photoskin->addAttribute("photoskin_type", "1");
		$photoskin->addAttribute("photoskin_name", "basic_skin");
		$photoskin->addChild("photoskin_path", "/data/basic/photoskin/evening.jpg");
	}
 	//while ($row = mysql_fetch_assoc($result)){
	for($i=0;$i < count($results);$i++){
		$row = $results[$i];
 		
 		$layout = $xml->addChild(layout);
 		
 		$layout->addAttribute("mall_ix", $row["mall_ix"]);
 		$layout->addAttribute("templet_name", $row["templet_name"]);
 		$layout->addAttribute("skin_type", $row["skin_type"]);
 		$layout->addAttribute("pcode", $row["pcode"]);
 		$layout->addAttribute("cid", $row["cid"]);
 		$layout->addAttribute("basic_link", $row["basic_link"]);
 		$layout->addAttribute("depth", $row["depth"]);
 		$layout->addAttribute("vlevel1", $row["vlevel1"]);
 		$layout->addAttribute("vlevel2", $row["vlevel2"]);
 		$layout->addAttribute("vlevel3", $row["vlevel3"]);
 		$layout->addAttribute("vlevel4", $row["vlevel4"]);
 		$layout->addAttribute("vlevel5", $row["vlevel5"]);
 		$layout->addChild("vlevelf", sprintf("%03d%03d%03d%03d%03d"
							,$row["vlevel1"]
							,$row["vlevel2"]
							,$row["vlevel3"]
							,$row["vlevel4"]
							,$row["vlevel5"]
				     			)
				     );
 		
 		$layout->addChild("cname", htmlspecialchars($row["cname"]));
 		//$layout->addChild("cname", htmlspecialchars("한글"));
 		$layout->addChild("path", $row["path"]);
 		$layout->addChild("layout", $row["layout"]);
 		$layout->addChild("page_navi", $row["page_navi"]);

 		$layout->addChild("header1", $row["header1"]);
 		$layout->addChild("header2", $row["header2"]);
 		$layout->addChild("leftmenu", $row["leftmenu"]);
 		$layout->addChild("contents", $row["contents"]);
 		$layout->addChild("contents_add", $row["contents_add"]);
 		$layout->addChild("rightmenu", $row["rightmenu"]);
 		$layout->addChild("footer1", $row["footer1"]);
 		$layout->addChild("footer2", $row["footer2"]);
 		
 		
 		$layout->addChild("catimg", $row["catimg"]);
 		$layout->addChild("leftcatimg", $row["leftcatimg"]);
 		$layout->addChild("subimg", $row["subimg"]);
 		$layout->addChild("category_top_view", $row["category_top_view"]);
 		$layout->addChild("category_display_type", $row["category_display_type"]);
 		$layout->addChild("bbs_name", $row["bbs_name"]);
 		$layout->addChild("category_use", $row["category_use"]);
 		$layout->addChild("is_layout_apply", $row["is_layout_apply"]);
 		
 		$layout->addChild("page_type", $row["page_type"]);
 		$layout->addChild("page_path", $row["page_path"]);
 		$layout->addChild("page_link", $row["page_link"]);
 		
 		$title = $layout->addChild("page_title", htmlspecialchars($row["page_title"]));
 		
 		$layout->addChild("page_help", $row["page_help"]);
 		$layout->addChild("page_addscript", $row["page_addscript"]);
 		$layout->addChild("page_body", $row["page_body"]);
 		
 		$layout->addChild("caching", $row["caching"]);
 		$layout->addChild("caching_time", $row["caching_time"]);
 		$layout->addChild("regedit", $row["regedit"]);
 		
 	}
 	//echo $outputXmlPath;
	$xml->asXML($outputXmlPath);
}
