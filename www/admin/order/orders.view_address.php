<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include("excel_out_columsinfo.php");

$db = new Database;

if($type == ORDER_STATUS_EXCHANGE_DELIVERY){
	$title = "교환상품 주소정보";
	$navigation = "주문관리 > 교환상품 주소정보";
}else if($type==ORDER_STATUS_RETURN_DELIVERY) {
	$title = "반품상품 주소정보";
	$navigation = "주문관리 > 반품상품 주소정보";
}

$sql="SELECT odd.*,od.pname FROM ".TBL_SHOP_ORDER_DETAIL." od LEFT JOIN shop_order_detail_deliveryinfo odd ON od.od_ix=odd.od_ix WHERE od.oid='".$oid."' AND od.od_ix='".$od_ix."' ";
$db->query($sql);
$db->fetch();
$Contents = "
		<table cellpadding=0 cellspacing=0 border=0 width='100%' align=center style='margin:0px 0 0 0px;background-color:#ffffff;'>	
		<tr height=40>
			<td align='left' colspan=2> ".GetTitleNavigation($title, $navigation, false)."</td>
		</tr>
		<tr>
			<td valign=top style='padding:10px;'>	
			<table border='0' width='100%' cellspacing='1' cellpadding='5' bgcolor='#c0c0c0'>
				<tr height='28' bgcolor=#ffffff>
					<td bgcolor='#efefef' width=120 align='left' ><img src='../image/ico_dot.gif' > 상품명</td>
					<td bgcolor='#ffffff' align='left'>&nbsp;".$db->dt[pname]."</td>
				</tr>
				<tr height='28' bgcolor=#ffffff>
					<td bgcolor='#efefef' align='left' ><img src='../image/ico_dot.gif' > 수신자명</td>
					<td bgcolor='#ffffff' align='left'>&nbsp;".$db->dt[rname]."</td>
				</tr>
				<tr height='28' bgcolor=#ffffff>
					<td bgcolor='#efefef' align='left' ><img src='../image/ico_dot.gif' > 수신자전화</td>
					<td bgcolor='#ffffff' align='left'>&nbsp;".$db->dt[rtel]."</td>
				</tr>
				<tr height='28' bgcolor=#ffffff>
					<td bgcolor='#efefef' align='left' ><img src='../image/ico_dot.gif' > 수신자모바일</td>
					<td bgcolor='#ffffff' align='left'>&nbsp;".$db->dt[rmobile]."</td>
				</tr>
				<tr height='40' bgcolor=#ffffff>
					<td bgcolor='#efefef' align='left' ><img src='../image/ico_dot.gif' > 수신자주소</td>
					<td bgcolor='#ffffff' align='left'>&nbsp;".$db->dt[zip]."<br />&nbsp;".$db->dt[addr1]."&nbsp;".$db->dt[addr2]."</td>
				</tr>
				<tr height='28' bgcolor=#ffffff>
					<td bgcolor='#efefef' align='left' ><img src='../image/ico_dot.gif' > 요청일자</td>
					<td bgcolor='#ffffff' align='left'>&nbsp;".$db->dt[date]."</td>
				</tr>
			</table>
			</td>

		</tr>
		</table>

		";
		
/*		
$Contents .= "			
<script language='JavaScript' src='../js/prototype.js'></script>
<script language='JavaScript' src='../js/scriptaculous.js'></script>
<script type='text/javascript'>
Sortable.create('excel_sortlist',{tag:'div',overlap:'horizontal',constraint:false,
	onUpdate: function(){
		serializeChar				= Sortable.serialize('excel_sortlist');
		
		var params = Form.serialize($(excel_out_frm));
		
	}
});

function CheckValue(frm){
	var params = Form.serialize($(excel_out_frm));
	//alert(params);
	//alert(Sortable.serialize('excel_sortlist')+'&'+Sortable.serialize('excel_sortlist2')+'&'+params);
	new Ajax.Request('excel_out.act.php',
		{
			method: 'POST',
			parameters: Sortable.serialize('excel_sortlist')+'&'+params+'&act=cookie_setting',
			onComplete: function(transport){
				alert('주문내역 저장하기 설정정보가 정상적으로 저장되었습니다.');
				//document.location.reload();
				//alert(transport.responseText);
			}
		});
		
	return false;
}
</script>";
*/



$Script = "
<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
<script type='text/javascript' src='../js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='../js/ui/jquery.ui.core.js'></script>
<style>
  #excel_sortlist { 
      list-style-type:none;
      margin:0;
      padding:0;
     
   }
   #excel_sortlist li {
     margin:1
     padding:0 0 0 10;
     cursor:move;
     width:445px;
     display:inline;
     border:1px solid #c0c0c0;
   }
   
   #excel_sortlist2 div {
     margin:1
     padding:0 0 0 10;
     cursor:move;
     width:445px;
     display:inline;
     border:1px solid #c0c0c0;
   }
  
</style>";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = $navigation;
$P->NaviTitle = $title;
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