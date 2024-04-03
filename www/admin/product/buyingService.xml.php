<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
header("Pragma: no-cache");
header('Content-type: text/xml;');

if($bs_site || true){
	$db = new Database;
	//$db2 = new Database;
	if($search_type == ""){
		$search_type = "pname";
	}
	
$where = "where bsui_ix is not null ";
if($cid2 != ""){
	$where .= " and bsui.cid LIKE '".substr($cid2,0,($depth+1)*3)."%' ";
}

if($bs_site != ""){
	$where .= " and bsui.bs_site = '".trim($bs_site)."' ";
}
/*
$sql = "select bsui.* , ci.cname , ci.depth
		from shop_buyingservice_url_info bsui
		left join shop_category_info ci on bsui.cid = ci.cid
		$where
		order by regdate desc limit 0, 15 ";
*/
$sql = "select bsui.* , ci.cname , ci.depth 
		from shop_buyingservice_url_info bsui 
		left join shop_category_info ci on bsui.cid = ci.cid  
		$where
		order by regdate desc limit 0, 100 ";


//echo $sql;
$db->query($sql);
	//$db->query("Select $search_type, id from ".TBL_SHOP_PRODUCT." where $search_type LIKE '%".$search_text."%' ");

	//$db->query("Select pname from ".TBL_SHOP_PRODUCT." where pname LIKE '".iconv('EUC-KR','UTF-8',$search_text)."%'");
	//echo ("Select pname from ".TBL_SHOP_PRODUCT." where pname = '".$search_text."%'");

	if($db->total){
		$xmlTmp = "<response>";

		for($i=0;$i < $db->total; $i++){
			$db->fetch($i);
			$xmlTmp .= "	<pid>". $db->dt[bsui_ix]."</pid>";
			//$xmlTmp .= "	<name>". iconv('EUC-KR','UTF-8',$db->dt[0])."</name>";
			$xmlTmp .= "	<name><![CDATA[". $db->dt[orgin_category_info]."]]></name>";
			$xmlTmp .= "	<search_text><![CDATA[". $db->dt[bs_list_url]."]]></search_text>";
			$xmlTmp .= "	<cid>".$db->dt[cid]."</cid>";
			$xmlTmp .= "	<depth>".$db->dt[depth]."</depth>";
			$xmlTmp .= "	<bs_site>".$db->dt[bs_site]."</bs_site>";
			$xmlTmp .= "	<currency_ix>".$db->dt[currency_ix]."</currency_ix>";
			$xmlTmp .= "	<last_working_date>".$db->dt[last_working_date]."</last_working_date>";
		}
		$xmlTmp .= "</response>";
	}else{
	//	$xmlTmp = "<response>";	
	//	$xmlTmp .= "	<name>".iconv('EUC-KR','UTF-8','대밴寃곌낵媛 議댁吏 듬')."</name>";	
	//	$xmlTmp .= "</response>";
	}
	echo $xmlTmp;
}
?>
