<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
header("Pragma: no-cache");
header('Content-type: text/xml;');

$db = new Database;
 
if($search_string == ""){
	//$sql = "SELECT * FROM ".TBL_SHOP_ZIP." WHERE send_cost_tekbae not in (0,3000) and send_cost_quick not in (0,3000) and send_cost_truck not in (0,3000) ORDER BY sido, sigugun,dong";
	$sql = "SELECT * FROM ".TBL_SHOP_ZIP." WHERE send_cost_tekbae <> 0 ORDER BY sido, sigugun,dong";
}else{
	$search_string = iconv("EUC-KR","UTF-8",$search_string);
	$sql = "SELECT * FROM ".TBL_SHOP_ZIP." WHERE sido LIKE '%$search_string%' OR sigugun LIKE '%$search_string%' OR dong LIKE '%$search_string%'  OR ri LIKE '%$search_string%'  ORDER BY sido, sigugun,dong";
}
//echo $sql;
//exit;

$db->query($sql);

if($db->total){
		$xmlTmp = "<zips>";		
	for($i=0;$i < $db->total; $i++){
		$db->fetch($i);		
		$code[0] = substr($db->dt[zip_code],0,3);
		$code[1] = substr($db->dt[zip_code],-3);
			
		$xmlTmp .= "	<zip>";
		$xmlTmp .= "		<zip_ix>".$db->dt[ix]."</zip_ix>";
		$xmlTmp .= "		<zip_code>".$db->dt[zip_code]."</zip_code>";
		$xmlTmp .= "		<zip_code_view>".$code[0]."".$code[1]."</zip_code_view>";
		$xmlTmp .= "		<address>".$db->dt[address]."</address>";		
		$xmlTmp .= "		<send_cost_tekbae>".iconv('EUC-KR','UTF-8',$db->dt[send_cost_tekbae])."</send_cost_tekbae>";
		$xmlTmp .= "		<send_cost_quick>".iconv('EUC-KR','UTF-8',$db->dt[send_cost_quick])."</send_cost_quick>";
		$xmlTmp .= "		<send_cost_truck>".iconv('EUC-KR','UTF-8',$db->dt[send_cost_truck])."</send_cost_truck>";
		
	//	$xmlTmp .= "		<send_cost_tekbae_name>".iconv('EUC-KR','UTF-8',"send_cost_tekbae_".$code[0]."-".$code[1])."</send_cost_tekbae_name>";
	//	$xmlTmp .= "		<send_cost_quick_name>".iconv('EUC-KR','UTF-8',"send_cost_tekbae_".$code[0]."-".$code[1])."</send_cost_quick_name>";
	//	$xmlTmp .= "		<send_cost_truck_name>".iconv('EUC-KR','UTF-8',"send_cost_tekbae_".$code[0]."-".$code[1])."</send_cost_truck_name>";
		$xmlTmp .= "	</zip>";
	}
	$xmlTmp .= "</zips>";
}else{
	$xmlTmp = "	<zips>";
	$xmlTmp .= "		<zip>";
	$xmlTmp .= "			<zip_ix>-</zip_ix>";
	$xmlTmp .= "			<zip_code>-</zip_code>";
	$xmlTmp .= "			<zip_code_view>-</zip_code_view>";
	$xmlTmp .= "			<address>-</address>";		
	$xmlTmp .= "			<send_cost_tekbae>-</send_cost_tekbae>";
	$xmlTmp .= "			<send_cost_quick>-</send_cost_quick>";
	$xmlTmp .= "			<send_cost_truck>-</send_cost_truck>";
	$xmlTmp .= "		</zip>";
	$xmlTmp .= "	</zips>";
}
echo $xmlTmp;

?>