<?
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include("../class/layout.class");
include("./co_goods.lib.php");
$install_path = "../../include/";
include("SOAP/Client.php");

$db = new Database;

	if($hostserver){
			$soapclient = new SOAP_Client("http://".$hostserver."/admin/cogoods/api/");
			// server.php 의 namespace 와 일치해야함
			$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

			## 한글 인자의 경우 에러가 나므로 인코딩함.



			$_return = $soapclient->call("getSellerInfo",$params = array("company_id"=> $company_id, "sellerinfo"),	$options);
			$_return = (array)$_return;
			$seller_info = (array)$_return[sellerinfo];
			//$total = $sellersinfo["total"];
			//$sellersinfo = $sellersinfo["sellers"];
			//echo $_return["sql"];
			//print_r($_return);
		
	}
$Contents = "
<table class='layer_box' border=0 cellpadding=0 cellspacing=0 style='width:580px;' >
	<col width='11px'>
	<col width='*'>
	<col width='11px'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02' ></td>
		<th class='box_03'></th>
	</tr>
	
	<tr>
		<th class='box_04' style='vertical-align:top'></th>
		<td class='box_05' rowspan=2 valign=top style='padding:0px 15px 5px 5px;line-height:150%;text-align:left;' >
		
		<table cellpadding=0 cellspacing=0 border=0 width='520' align=center style='margin:0px 0 0 20px;background-color:#ffffff;'>	
		<tr>
			<td style='padding:20px 0px;'>
			<table width=100% border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef;background:url(../images/icon/sub_title_dot.gif) no-repeat left center ;padding-left:16px;'><b style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;'> ".$seller_info[shop_name]." 설정하기</b></td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td valign=top width='510'>	
			
				<form name='excel_out_frm' method=\"post\" enctype='multipart/form-data' action='setting.act.php' onsubmit='return CheckValue(this);' target='act' >
				<input type='hidden' name='act' value='update_sellerInfo'>
				<input type=hidden name=company_id value='".$company_id."'>
				<input type=hidden name=chs_ix value='".$chs_ix."'>
				
				<table class='line_color' cellpadding=6  style='background-color:#c0c0c0;border-collapse:collapse; border-spacing:1px;' width='100%' border='0' align='center' bgcolor='#c0c0c0'   >
					<col width='25%'>
					<col width='*'>
					<tr bgcolor=#ffffff>
						<td  style='text-align:left;padding-left:10px' bgcolor=#efefef> <b>상점명</b>  </td>
						<td style='text-align:left;padding-left:10px' >
							".$seller_info[shop_name]."
						</td>
						<td style='text-align:left;padding-left:10px' bgcolor=#efefef> <b>대표자명</b> </td>
						<td style='text-align:left;padding-left:10px' >
							".$seller_info[com_ceo]."
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px' bgcolor=#efefef> <b>도메인</b> </td>
						<td style='text-align:left;padding-left:10px' colspan=3>
							".$seller_info[shop_url]."
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px' bgcolor=#efefef> <b>대표전화</b> </td>
						<td style='text-align:left;padding-left:10px' >
							".$seller_info[com_phone]."
						</td>
						<td style='text-align:left;padding-left:10px' bgcolor=#efefef> <b>대표전화</b> </td>
						<td style='text-align:left;padding-left:10px' >
							".$seller_info[com_fax]."
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px' bgcolor=#efefef> <b>상품공유설정</b></td>
						<td style='text-align:left;padding-left:10px' colspan=3>
							<input type=radio name='goods_copy' id='goods_copy_a' value='A' ".($seller_info[goods_copy] == "A" ? "checked":"")."><label for='goods_copy_a'>자동복사</label>
							<input type=radio name='goods_copy' id='goods_copy_m' value='M' ".($seller_info[goods_copy] == "M" ? "checked":"")."><label for='goods_copy_m'>수동복사</label>
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px' bgcolor=#efefef> <b>상품기본카테고리</b></td>
						<td style='text-align:left;padding-left:10px' colspan=3>
							
						</td>
					</tr>
				</table>			
	
<!--
공유상품 등록시 자동으로 상품 복사 <br>
상품 복사시 기본카테고리 지정<br>
복사해온 상품정보를 공유서버에 저장 필요<br>
상품복사시 공유서버에 저장된 정보를 확인필요
-->
";
				
				
$Contents .="	<tr>
					<td style='padding:10px;' class=small><b>".$seller_info[shop_name]."</b> 공유서버에 필요한 정보를 설정합니다. 	</td>
				</tr>
				<tr>
					<td align=center style='padding:30px 0px '> 
					 <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:hand' >  
					 
					</td>
				</tr>
				
				</table>
				</form>";
		
				
				
$Contents .= "		
		<iframe name='calcufrm' src='' width=0 height=0></iframe>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>
		";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->layout_display = false;
$P->Navigation = "주문관리 > 교환 신청사유";
$P->NaviTitle = "교환 신청사유";
$P->strContents = $Contents;
echo $P->PrintLayOut();

/*
$P = new popLayOut;
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->strContents = $Contents;
$P->Navigation = "HOME > 주문관리 > 주문내역저장하기";
$P->PrintLayOut();
*/


$mstring ="
	<html>
	<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
	<body >
	<div id='category_top_view_area' >
	".$db->dt[category_top_view]."
	</div>
	</body>
	</html>
	<script>		
	parent.document.forms['thisCategoryform'].category_use.checked = $category_use;
	parent.iView.document.body.innerHTML = document.getElementById('category_top_view_area').innerHTML;
	</script>";

//echo $db->total;
function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);

	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";
	
	return $vPrintRootNode;
}

function PrintNode($mdb){
	global $selected_cid;
	$cname = $mdb->dt[cname];
	$cid = $mdb->dt[cid];
	$depth = $mdb->dt[depth];
	$category_display_type = $mdb->dt[category_display_type];
	
	

	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname = str_replace("\"","&quot;",$cname);
	
//	if ($cid == $mcid){
//		$expandstring = "true";	
//	}else{
//		$expandstring = "false";	
//	}
	
//	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	
	if($mdb->dt[category_display_type] == "P"){
		$mstring =  "		var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
	}else if($mdb->dt[category_display_type] == "F"){
		$mstring =  "		var node$cid = new TreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	}else{
		$mstring =  "		var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');\n";
	}
	
	
	if(substr($selected_cid,0,($depth+1)*3) == substr($cid,0,($depth+1)*3)){
		$mstring .=  "
		node$cid.expanded = true;
		node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type')\";
		rootnode.addNode(node$cid);\n\n";
	}else{
		$mstring .=  "
		node$cid.expanded = false;
		node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type')\";
		rootnode.addNode(node$cid);\n\n";
	}
	
	return $mstring;
}

function PrintGroupNode($mdb)
{
	$cname = $mdb->dt[cname];
	$mcid = $mdb->dt[cid];
	$depth = $mdb->dt[depth];
	$category_display_type = $mdb->dt[category_display_type];
	
	global $cid;
	$cid1 = substr($mcid,0,3);
	$cid2 = substr($mcid,3,3);
	$cid3 = substr($mcid,6,3);
	$cid4 = substr($mcid,9,3);
	$cid5 = substr($mcid,12,3);

	$Parentdepth = $depth - 1;
	
	if ($depth+1 == 1){
		$cid1 = "000";
	}else if($depth+1 == 2){
		$cid2 = "000";
	}else if($depth+1 == 3){
		$cid3 = "000";
	}else if($depth+1 == 4){
		$cid4 = "000";
	}else if($depth+1 == 5){
		$cid5 = "000";
	}
	
	$parent_cid = "$cid1$cid2$cid3$cid4$cid5";
	
	if ($depth ==1){
		$ParentNodeCode = "node$parent_cid";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==4){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==5){
		$ParentNodeCode = "groupnode$parent_cid";
	}
	
	
	
	$cname = str_replace("\"","&quot;",$cname);
	if($mdb->dt[category_display_type] == "P"){
		$mstring =  "		var groupnode$mcid = new TreeNode('$cname', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
	}else if($mdb->dt[category_display_type] == "B"){
		$mstring =  "		var groupnode$mcid = new TreeNode('$cname', '../resources/Common_TreeNode_BBS.gif', '../resources/Common_TreeNode_BBS.gif');\n";		
	}else{
		$mstring =  "		var groupnode$mcid = new TreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	}
	
	if ($mcid == $cid){
		$mstring .=  "	groupnode$cid.expanded = true;\n";	
	//	$mstring .=  "	groupnode$cid.select = true;\n";
	}
	
	
	$mstring .=  "	groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth, '$category_display_type')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
		
	return $mstring;
}


?>