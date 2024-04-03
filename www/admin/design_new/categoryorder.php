<?php
// 	include("../../class/database.class");
	include "../class/LayoutXml/LayoutXml.class";
	include("design.common.php");
	include("../class/layout.class");
	
	$page_path = getDesignTempletPath($pcode, $depth);
	$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml";
	
	$layoutXml = new LayoutXml($layoutXmlPath);
	
	// $db = new Database;
	$depth = $this_depth;
	$this_cid = $cid;
	
	/*	
		$sPos = $depth*3;
		
		if ($depth == 0){
			$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth = $depth and cid LIKE '".substr($cid,0,$sPos)."%'";
		}else{
			$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth = $depth and cid LIKE '".substr($cid,0,$sPos)."%'";
		}
			
		echo($sql);
	*/	
		$select_level = getlevel($this_cid, $depth);
	//	echo"level:".$select_level."<br><br>";
// 		echo "depth:".($depth)."<br><br>";
// 		echo "this_cid:".($this_cid)."<br><br>";
// 		exit;
		if ($mode == "up"){
			$target_cid_length = strlen(getTargetCid($depth,$this_cid,$select_level,-1));
		}else{
			$target_cid_length = strlen(getTargetCid($depth,$this_cid,$select_level,+1));
		}
		
// 		echo " ggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg : ". $target_cid_length;
// 		exit;
		if ($target_cid_length == 15){
			if ($mode == "up"){
	//			echo "strlen:".strlen(getTargetCid($depth,$this_cid,$select_level,-1))."<br><br>";
	//			echo "tarcagetcid:".getTargetCid($depth,$this_cid,$select_level,-1)."<br>";
				Pluslevel($this_cid,$depth,$select_level,getTargetCid($depth,$this_cid,$select_level,-1));
			}else{
	//			echo "strlen:".strlen(getTargetCid($depth,$this_cid,$select_level,+1))."<br><br>";
	//			echo "tarcagetcid:".getTargetCid($depth,$this_cid,$select_level,+1)."<br>";
				Minuslevel($this_cid,$depth,$select_level,getTargetCid($depth,$this_cid,$select_level,+1));
			}
		}else{
			echo "<Script Language='JavaScript'>alert('더이상 진행할 방향이 없습니다.')</Script>";			
		}
	
	echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";	
	
	function getlevel($cid,$depth){
	// 	global $db, $admin_config;
		global $admin_config, $layoutXml;
		
		$levelnum = $depth+1;
// 		if($admin_config[mall_page_type] == "MI"){
// 			$sql = "SELECT vlevel$levelnum  FROM ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth = $depth and cid = '$cid' ";
// 		}else{
// 			$sql = "select vlevel$levelnum from ".TBL_SHOP_LAYOUT_INFO." where depth = $depth and cid = '$cid' ";
// 		}
		$xpathString = sprintf("/layouts/layout[@cid='%s' and @depth='%s']"
				, $cid, $depth);
		
		$layouts = $layoutXml->simpleXml->xpath($xpathString);

		switch ($levelnum) {
			case 1:
				$result = (int) ($layouts[0]->attributes()->vlevel1);
				break;
			case 2:
				$result = (int) $layouts[0]->attributes()->vlevel2;
				break;
			case 3:
				$result = (int) $layouts[0]->attributes()->vlevel3;
				break;
			case 4:
				$result = (int) $layouts[0]->attributes()->vlevel4;
				break;
			case 5:
				$result = (int) $layouts[0]->attributes()->vlevel5;
				break;
			default:
				;
				break;
		}

// 		$db->query($sql);
// 		$db->fetch(0);	
		
// 		return $db->dt[0];
		return $result;
	}
	
	function getTargetCid($depth,$cid,$select_level,$num){
		global $admin_config, $layoutXml;
		
		$sPos = ($depth)*3;
		$levelnum = $depth+1;
		
		if($select_level == 0 && $num < 0){
			return "";	
		}
// 		if($admin_config[mall_page_type] == "MI"){
// 			$sql = "SELECT cid  FROM ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth = $depth and vlevel$levelnum = '".($select_level+$num)."' and cid LIKE '".substr($cid,0,$sPos)."%' ";
// 		}else{
// 			$sql = "select cid from ".TBL_SHOP_LAYOUT_INFO." where depth = $depth and vlevel$levelnum = '".($select_level+$num)."' and cid LIKE '".substr($cid,0,$sPos)."%'";	
// 		}

		$xpathString = sprintf("/layouts/layout[@vlevel%s='%s' and @depth='%s' and substring(@cid, 1, %s)='%s']"
								, (string) $levelnum
								, (string) $select_level+$num
								, (string) $depth
								, (string) $sPos
								, (string) substr($cid, 0, $sPos)
							);
		
		$layouts = $layoutXml->simpleXml->xpath($xpathString);
		
		
	//	echo($sql)."<br>";
// 		$db->query($sql);
	//	echo $db->total;
	
// 		if ($db->total){
		if (count($layouts)){
// 			$db->fetch(0);			
			//echo "오나여?".$db->dt[0];
// 			return $db->dt[0];
			return	$layouts[0]->attributes()->cid;	
		}else{
			if($num < 0){
				return getTargetCid($depth,$cid,$select_level,$num-1);
			}else if($num > 0){
				return getTargetCid($depth,$cid,$select_level,$num+1);
			}
		}
	}
	
	function Pluslevel($cid,$depth,$selectlevel,$target_cid)
	{
// 		global $db, $admin_config;
		global $admin_config, $layoutXmlPath, $layoutXml;
		
		$sPos = ($depth+1)*3;
		$levelnum = $depth+1;
		
// 		if ($depth == 0){
// 			if($admin_config[mall_page_type] == "MI"){
// 				$sql = "UPDATE ".TBL_SHOP_MINISHOP_LAYOUT_INFO." SET vlevel$levelnum = '".($selectlevel)."' where vlevel$levelnum = '".($selectlevel-1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
// 			}else{
// 				$sql = "UPDATE ".TBL_SHOP_LAYOUT_INFO." SET vlevel$levelnum = '".($selectlevel)."' where vlevel$levelnum = '".($selectlevel-1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
// 			}
// 			$db->query($sql);
// 		}else{
// 			if($admin_config[mall_page_type] == "MI"){
// 				$sql = "UPDATE ".TBL_SHOP_MINISHOP_LAYOUT_INFO." SET vlevel$levelnum = '".($selectlevel)."' where vlevel$levelnum = '".($selectlevel-1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
// 			}else{
// 				$sql = "UPDATE ".TBL_SHOP_LAYOUT_INFO." SET vlevel$levelnum = '".($selectlevel)."' where vlevel$levelnum = '".($selectlevel-1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
// 			}
// 			$db->query($sql);
// 		}
	//	echo($sql)."<br>";
		$xpathString = sprintf("/layouts/layout[@vlevel%s='%s' and substring(@cid, 1, %s)='%s']", $levelnum, ($selectlevel - 1), $sPos, substr($target_cid, 0, $sPos));
 		echo $xpathString;
 		echo "\n";
 		echo sprintf("levelnum = %s   selectlevel = %s\n", $levelnum, $selectlevel);

		$layouts = $layoutXml->simpleXml->xpath($xpathString);
		
		print_r($layouts);
		echo "\n";
		echo "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
		echo "\n";
		foreach ($layouts as $layout) {
			switch ($levelnum) {
				case 1:
					$layout->attributes()->vlevel1 = $selectlevel;
					break;
				case 2:
					$layout->attributes()->vlevel2 = $selectlevel;
					break;
				case 3:
					$layout->attributes()->vlevel3 = $selectlevel;
					break;
				case 4:
					$layout->attributes()->vlevel4 = $selectlevel;
					break;
				case 5:
					$layout->attributes()->vlevel5 = $selectlevel;
					break;
				default:
					;
				break;
			}
			$layout->vlevelf = sprintf("%03d%03d%03d%03d%03d"
				                                      ,$layout->attributes()->vlevel1
				                                      ,$layout->attributes()->vlevel2
				                                      ,$layout->attributes()->vlevel3
				                                      ,$layout->attributes()->vlevel4
				                                      ,$layout->attributes()->vlevel5 
									  );
		}
		
		print_r($layouts);
		
		echo "\n";
		echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
		echo "\n";
		
// 		print_r($layoutXml->simpleXml);
		//$layoutXml->SaveXml($layoutXmlPath);
		
// 		$xpathString = sprintf("/layouts/layout[@vlevel%s='%s' and substring(@cid, 1, %s)='%s']", $levelnum, $selectlevel, $sPos, substr($target_cid, 0, $sPos));
// 		$layouts = $layoutXml->simpleXml->xpath($xpathString);
// 		foreach ($layouts as $layout) {
// 			switch ($levelnum) {
// 				case 1:
// 					$layout->attributes()->vlevel1 = $selectlevel - 1;
// 					break;
// 				case 2:
// 					$layout->attributes()->vlevel2 = $selectlevel - 1;
// 					break;
// 				case 3:
// 					$layout->attributes()->vlevel3 = $selectlevel - 1;
// 					break;
// 				case 4:
// 					$layout->attributes()->vlevel4 = $selectlevel - 1;
// 					break;
// 				case 5:
// 					$layout->attributes()->vlevel5 = $selectlevel - 1;
// 					break;
// 				default:
// 					;
// 					break;
// 			}
// 			$layout->vlevelf = sprintf("%03d%03d%03d%03d%03d"
// 					,$layout->attributes()->vlevel1
// 					,$layout->attributes()->vlevel2
// 					,$layout->attributes()->vlevel3
// 					,$layout->attributes()->vlevel4
// 					,$layout->attributes()->vlevel5
// 			);
// 		}
		
		$layoutXml->simpleXml->asXML($layoutXmlPath);
		
// 		$sql = "UPDATE ".TBL_SHOP_LAYOUT_INFO." SET vlevel$levelnum = '".($selectlevel - 1)."' where vlevel$levelnum = '$selectlevel'  and cid LIKE '".substr($cid,0,$sPos)."%'";
// 		$db->query($sql);
	//	echo($sql)."<br>";
	}
	
	function Minuslevel($cid,$depth,$selectlevel,$target_cid)
	{
		global $db, $admin_config;
		
		$sPos = ($depth+1)*3;
		$levelnum = $depth+1;
		
		if ($depth == 0){
			if($admin_config[mall_page_type] == "MI"){
				$sql = "UPDATE ".TBL_SHOP_MINISHOP_LAYOUT_INFO." SET vlevel$levelnum = '$selectlevel' where vlevel$levelnum = '".($selectlevel+1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
			}else{
				$sql = "UPDATE ".TBL_SHOP_LAYOUT_INFO." SET vlevel$levelnum = '$selectlevel' where vlevel$levelnum = '".($selectlevel+1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
			}
			$db->query($sql);
		}else{
			if($admin_config[mall_page_type] == "MI"){
				$sql = "UPDATE ".TBL_SHOP_MINISHOP_LAYOUT_INFO." SET vlevel$levelnum = '$selectlevel' where vlevel$levelnum = '".($selectlevel+1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
			}else{
				$sql = "UPDATE ".TBL_SHOP_LAYOUT_INFO." SET vlevel$levelnum = '$selectlevel' where vlevel$levelnum = '".($selectlevel+1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
			}
			$db->query($sql);
		}
	//	echo($sql)."<br>";
		if($admin_config[mall_page_type] == "MI"){
			$sql = "UPDATE ".TBL_SHOP_MINISHOP_LAYOUT_INFO." SET vlevel$levelnum = '".($selectlevel + 1)."' where vlevel$levelnum = '$selectlevel' and cid LIKE '".substr($cid,0,$sPos)."%'";
		}else{
			$sql = "UPDATE ".TBL_SHOP_LAYOUT_INFO." SET vlevel$levelnum = '".($selectlevel + 1)."' where vlevel$levelnum = '$selectlevel' and cid LIKE '".substr($cid,0,$sPos)."%'";
		}
		$db->query($sql);
	//	echo($sql)."<br>";
	}
?>