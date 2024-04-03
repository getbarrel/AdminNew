<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
//auth(8);

if($max == ""){
$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;
if($mode == "search"){
	if($admininfo[admin_level] == 9){
		$where = "where p.id Is NOT NULL  and p.id = r.pid and p.id = po.pid ";
	}else{
		$where = "where p.id Is NOT NULL and p.id = r.pid and p.id = po.pid  and admin ='".$admininfo[company_id]."' ";
	}
	
	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}
	
	if($pname != ""){
		$where = $where."and p.pname LIKE '%".$pname."%' ";
	}
	
	if($from_price != "" && $to_price != ""){
		$where = $where."and p.sellprice between $from_price and $to_price ";
	}
	
	if($stock_status == "soldout"){
		$where = $where."and po.option_stock = 0 ";
	}else if($stock_status == "shortage"){
		$where = $where."and po.option_stock < po.option_safestock ";
	}else if($stock_status == "surplus"){
		$where = $where."and po.option_stock > po.option_safestock ";
	}
/*
	if($stock_status == "soldout"){
		$where = $where."and p.stock = 0 ";
	}else if($stock_status == "shortage"){
		$where = $where."and p.stock < p.safestock ";
	}else if($stock_status == "surplus"){
		$where = $where."and p.stock > p.safestock ";
	}
*/	
	$db->query("SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po $where ");	
	//echo("SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po $where ");	
	//exit;
	
}else{
	if ($cid == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "Where admin ='".$company_id."'";	
			}
			$db->query("SELECT distinct id FROM ".TBL_SHOP_PRODUCT." $addWhere");
		}else{
			$db->query("SELECT distinct id FROM ".TBL_SHOP_PRODUCT." where admin ='".$admininfo[company_id]."'");
		}
	//	echo("SELECT * FROM ".TBL_SHOP_PRODUCT."<br>");
	//	echo $db->total;
	}else{
		switch ($depth){
			case 0:	
				$cut_num = 3;
				break;
			case 1:
				$cut_num = 6;
				break;
			case 2:
				$cut_num = 9;
				break;
			case 3:
				$cut_num = 9;			
				break;		
		}
		if($admininfo[admin_level] == 9){
			$db->query("SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%'");	
		}else{
			$db->query("SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' and admin ='".$admininfo[company_id]."'");	
		}
		
	}
}

$total = $db->total;


$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			<tr><td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;' align=center><img src='../image/title_head.gif' align=absmiddle><b> 상품 재고부족 보고서</b></div>")."</td></tr>
			<tr>
			<td valign=top style='padding-top:33px;'>";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_stock>			
			";
$innerview = "			
			<form name=stockfrm method=post action='product_stock.act.php' target='act'><input type='hidden' name='act' value='update'>			
			
			<table cellpadding=2 cellspacing=0 bgcolor=gray width='100%'>			
			<tr align=center height=25>
				<!--td width=30 class='s_td'><input type=checkbox class=nonborder checked disabled></td>
				<td width=60 class='m_td'>상품코드</td>
				<td width=50 class='m_td'>이미지</td>
				<td width=200 class='m_td'>제품명</td>
				<td width=80 class='m_td'>재고</td>
				<td width=60 class='m_td'>안전재고</td>
				<td width=60 class='m_td'>진열</td>
				<td width=70 class='e_td'>관리</td-->
				
				<!--td width='5%' class=s_td>&nbsp;<input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td-->
				<!--td width='5%' class=s_td>번호</td-->
				<td width='10%' class=s_td>상품코드</td>
				<td width='10%' class=m_td>이미지</td>
				<td width='20%' class=m_td>제품명</td>
				<td width='20%' class=m_td>옵션명</td>
				<td width='15%' class=m_td>재고</td>
				<td width='10%' class=m_td>적정재고</td>
				<td width='10%' class=e_td>부족재고</td>				
				<!--td width='15%' class=e_td>비고</td-->
								
			</tr>
			";
if($mode == "search"){
	if($orderby == "date"){
		$orderbyString = "order by vieworder2 asc, regdate desc, id desc";
	}else{
		$orderbyString = "order by vieworder2 asc, regdate desc, id desc";
	}	
	
	if($admininfo[admin_level] == 9){
		$where = "where c.company_id = p.admin and p.id Is NOT NULL  and p.id = po.pid  ";
	}else{
		$where = "where c.company_id = p.admin and p.id Is NOT NULL and admin ='".$admininfo[company_id]."' and p.id = po.pid ";
	}
	if($pid != ""){
		$where = $where."and  p.id = $pid ";
	}
	
	if($pname != ""){
		$where = $where."and p.pname LIKE '%".$pname."%' ";
	}
	
	if($from_price != "" && $to_price != ""){
		$where = $where."and p.sellprice between $from_price and $to_price ";
	}

	if($stock_status == "soldout"){
		$where = $where."and po.option_stock = 0 ";
	}else if($stock_status == "shortage"){
		$where = $where."and po.option_stock < po.option_safestock ";
	}else if($stock_status == "surplus"){
		$where = $where."and po.option_stock > po.option_safestock ";
	}
	
/*	
	if($stock_status == "soldout"){
		$where = $where."and p.stock = 0 ";
	}else if($stock_status == "shortage"){
		$where = $where."and p.stock < p.safestock ";
	}else if($stock_status == "surplus"){
		$where = $where."and p.stock > p.safestock ";
	}	
*/	
	
	$sql = "SELECT distinct p.id, p.pcode, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name, p.disp, stock, safestock, case when vieworder = 0 then 100000 else vieworder end as vieworder2 FROM ".TBL_SHOP_PRODUCT." p , ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po $where $orderbyString LIMIT $start, $max";	
	$db->query($sql);	
	
	
	
	
}else{		

	if($orderby == "date"){
		$orderbyString = "order by vieworder2 asc, regdate desc, id desc";
	}else{
		$orderbyString = "order by vieworder2 asc, regdate desc, id desc";
	}	
	if ($cid == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";	
			}
			
			$sql = "SELECT distinct p.id, p.pcode, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name, p.disp, stock, safestock, 
					case when vieworder = 0 then 100000 else vieworder end as vieworder2 
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_COMPANY_DETAIL." c 
					where c.company_id = p.admin $addWhere $orderbyString LIMIT $start, $max";
			
			$db->query($sql);
			
		}else{
			$sql = "SELECT distinct p.id, p.pcode, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name, p.disp, stock, safestock, 
					case when vieworder = 0 then 100000 else vieworder end as vieworder2 
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_COMPANY_DETAIL." c 
					where c.company_id = p.admin and admin ='".$admininfo[company_id]."' LIMIT $start, $max";
					
			$db->query($sql);
		}
	}else{
		switch ($depth){
			case 0:	
				$cut_num = 3;
				break;
			case 1:
				$cut_num = 6;
				break;
			case 2:
				$cut_num = 9;
				break;
			case 3:
				$cut_num = 12;
				break;
			case 4:
				$cut_num = 15;
				break;
		}
		if($admininfo[admin_level] == 9){
			$db->query("SELECT distinct p.id, p.pcode, p.pname, p.sellprice, p.regdate,c.com_name,p.disp, stock, safestock, p.vieworder, case when vieworder = 0 then 100000 else vieworder end as vieworder2 FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c where c.company_id = p.admin and p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' order by vieworder2 asc, p.id desc LIMIT $start, $max");			
		}else{
			$db->query("SELECT distinct p.id, p.pcode, p.pname, p.sellprice, p.regdate,c.com_name,p.disp, stock, safestock, p.vieworder, case when vieworder = 0 then 100000 else vieworder end as vieworder2 FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c where c.company_id = p.admin and p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' and admin ='".$admininfo[company_id]."' order by vieworder2 asc, p.id desc LIMIT $start, $max");
		}
	}		
		
}


if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=8 align=center> 해당되는  제품이 없습니다.</td></tr>";
}else{			
	for ($i = 0; $i < $db->total; $i++)
	{
		
		$db->fetch($i);
		
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$db->dt[id].".gif")){
			$img_str = $admin_config[mall_data_root]."/images/product/s_".$db->dt[id].".gif";
		}else{
			$img_str = "../image/no_img.gif";
		}
		
		
	
	$innerview .= "<tr height=1><td colspan=9 background='/img/dot.gif'></td></tr>
				<tr bgcolor='#ffffff'>					
					<td align=center  nowrap><!--a href='/pinfo.php?id=".$db->dt[id]."'-->".$db->dt[pcode]."<!--/a--><input type=hidden name='pid[]' value='".$db->dt[id]."'></td>
					<td bgcolor='#efefef' align=center ><img src='".$img_str."' width=50 height=50></td>
					<td width=250 >".$db->dt[pname]."</td>	
					<td colspan=4 height=1 bgcolor=#ffffff>".PrintStockByOption($db)."</td>				
					<!--td width=150 ></td>
					<td bgcolor='#efefef' align=center><input type=text name='incom".$db->dt[id]."' value='0' onkeyup=\"changeStock('".$db->dt[id]."');\" size=6></td>
					<td bgcolor='#ffffff' align=center><input type=text name='stock".$db->dt[id]."' value='".$db->dt[stock]."' size=6><input type=hidden name='bstock".$db->dt[id]."' value='".$db->dt[stock]."' size=6></td>
					<td bgcolor='#efefef' align=center><input type=text name='safestock".$db->dt[id]."' value='".$db->dt[safestock]."' size=5></td-->
					<!--td bgcolor='#efefef' align=center></td-->									
				</tr>				
				";
	}
}	
	$innerview .= "</table>
				</form>";
	
$Contents = $Contents.$innerview ."			
			</td>
			</tr>
		</table>
		<iframe name='act' src='' width=0 height=0></iframe>
			";


$category_str ="<div class=box id=img3  style='width:190px;height:190px;overflow:auto;'>".Category()."</div>";


//if($view == "innerview"){
if(false){
	echo "<html><body>$innerview</body></html>";	
	echo "<Script>parent.document.getElementById('product_stock').innerHTML = document.body.innerHTML;</Script>";
}else{
	echo "<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<META content='MSHTML 6.00.2800.1498' name=GENERATOR></HEAD>
	<title></title>
</head>
<LINK REL='stylesheet' HREF='../include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='../logstory/include/logstory.css' TYPE='text/css'>";
	echo $Contents;
	
echo "</body>
</html>";
	/*
	$P = new LayOut();
	$P->strLeftMenu = product_menu("/manage",$category_str);
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='product_stock.js'></script>";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
	*/
}



function PrintStockByOption($db){
	
	$mdb = new Database;
	
	$sql = "select id, option_div,option_price, option_m_price, option_d_price, option_a_price, option_useprice, option_stock, option_safestock,option_etc1 from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '".$db->dt[id]."' order by id asc";
	$mdb->query($sql);
	
	$mString = "<table cellpadding=4 cellspacing=0 width=100% height=100% style='table-layout : fixed' bgcolor=silver border=0>";
	
	
	
	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>비회원가</td><td>회원가</td><td>딜러가</td><td>대리점가</td><td >재고</td><td >안전재고</td></tr>";
	$mString .=  "<input type=hidden id='_option_stock".$db->dt[id]."' value=0>";
	if ($mdb->total == 0){		
		$mString .= "<td width='40%' bgcolor='#efefef'  align=center></td>
			<td width='20%' bgcolor='#ffffff' align=center><input type=text name='incom".$db->dt[id]."' value='0' onkeyup=\"changeStock('".$db->dt[id]."');\" size=6></td>
			<td width='20%' bgcolor='#efefef' align=center><input type=text name='stock".$db->dt[id]."' value='".$db->dt[stock]."' size=6><input type=hidden name='bstock".$db->dt[id]."' value='".$db->dt[stock]."' size=6></td>
			<td width='20%' bgcolor='#ffffff' align=center><input type=text name='safestock".$db->dt[id]."' value='".$db->dt[safestock]."' size=5></td>";
	}else{
		$i=0;
		for($i=0;$i<$mdb->total;$i++){
			$mdb->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff>			
			<td width='40%' bgcolor='#efefef' align=center>".$mdb->dt[option_div]."</td>
			<!--td width='20%' align=center bgcolor='#ffffff' ><input type=text value='0' id='_option_incom".$db->dt[id]."' name='option_incom".$db->dt[id]."_".$mdb->dt[id]."' onkeyup=\"changeStockByOption('".$db->dt[id]."', '".$mdb->dt[id]."');calcurateStockByOption('".$db->dt[id]."');\"  size=6></td-->
			<td width='20%' align=center bgcolor='#ffffff' >".$mdb->dt[option_stock]."</td>
			<td width='20%' align=center bgcolor='#efefef' >".$mdb->dt[option_safestock]."</td>
			<td width='20%' align=center bgcolor='#ffffff' >".($mdb->dt[option_safestock]-$mdb->dt[option_stock])."</td>
			<!--td align=center>				
				<a href=JavaScript:deleteOption('delete','".$mdb->dt[id]."','$pid')><img  src='../image/si_remove.gif' border=0></a>
			</td-->
			</tr>			
			";
		}
		
		$mString .= "<td width='40%' bgcolor='#efefef' align=center>총계</td>			
			<td width='20%' bgcolor='#ffffff' align=center>".$db->dt[stock]."</td>
			<td width='20%' bgcolor='#efefef' align=center>".$db->dt[safestock]."</td>
			<td width='20%' bgcolor='#ffffff' align=center>".($db->dt[safestock]-$db->dt[stock])."</td>";
	}
	
	$mString = $mString."</table>";
	
	return $mString;
}



function SelectViewOrder($select_pid,$thisorder, $befororder, $nextorder){
global $user;


			
	//for($i=0;$i<$mdb->dt[maxorder];$i++){
		if(($befororder == '' && $nextorder == '') || ($befororder == '' && $nextorder == $thisorder) ||($befororder == $thisorder && $nextorder == '')){
			//$befororder|$thisorder|$nextorder
			$thisString = "
					<table cellpadding=0 cellspacing=1 border=0>
						<tr><td><img src='../image/t.gif' border=0 style='filter:alpha(opacity=50)' ></td></tr>
						<tr><td><img src='../image/b.gif' border=0 style='filter:alpha(opacity=50)' ></td></tr>
					</table>";
		}else if($befororder == '' || $befororder == $thisorder){
			$thisString = "
					<table cellpadding=0 cellspacing=1 border=0>
						<tr><td><img src='../image/t.gif' border=0 style='filter:alpha(opacity=50)' ></td></tr>
						<tr><td><img src='../image/b.gif' border=0 style='cursor:hand' onclick=\"UpdateOrder('$select_pid','$thisorder','$nextorder')\"></td></tr>
					</table>";
		}else if($nextorder == '' || $nextorder == $thisorder){
			$thisString = "
					<table cellpadding=0 cellspacing=1 border=0>
						<tr><td><img src='../image/t.gif' border=0 style='cursor:hand' onclick=\"UpdateOrder('$select_pid','$thisorder','$befororder')\"></td></tr>
						<tr><td><img src='../image/b.gif' border=0 style='filter:alpha(opacity=50)' ></td></tr>
					</table>";
		}else{
			$thisString = "
					<table cellpadding=0 cellspacing=1 border=0>
						<tr><td><img src='../image/t.gif' border=0 style='cursor:hand' onclick=\"UpdateOrder('$select_pid','$thisorder','$befororder')\"></td></tr>
						<tr><td><img src='../image/b.gif' border=0 style='cursor:hand' onclick=\"UpdateOrder('$select_pid','$thisorder','$nextorder')\"></td></tr>
					</table>";
		}
		
	//}	
	
	return $thisString;

}


function Category()
{
$mdb = new Database;
	
	global $id;
	
$m_string = "	
<script language='JavaScript' src='../include/manager.js'></script>
<script language='JavaScript' src='../include/Tree.js'></script>
<script>

/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = 'black';
	tree.bgColor = 'white';
	tree.borderWidth = 0;


/*	Create Root node	*/
	var rootnode = new TreeNode('상품카테고리', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');
	rootnode.action = \"setCategory('product category','000000000000000',-1,'".$id."')\";
	rootnode.expanded = true;";

$mdb->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $mdb->total;
for ($i = 0; $i < $mdb->total; $i++)
{

	$mdb->fetch($i);
	
	if ($mdb->dt["depth"] == 0){
		$m_string = $m_string.PrintNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
	}else if($mdb->dt["depth"] == 1){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
	}else if($mdb->dt["depth"] == 2){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
	}else if($mdb->dt["depth"] == 3){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
	}else if($mdb->dt["depth"] == 4){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
	}
}

	$m_string = $m_string."tree.addNode(rootnode);";

$m_string = $m_string."	
</script>
<form>
<div id=TREE_BAR style='margin:5;'>
<script>		
tree.draw();
tree.nodes[0].select();
</script>
</div>
</form>";

return $m_string;
}




function PrintRootNode($cname){
	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";
	
	return $vPrintRootNode;
}

function PrintNode($cname,$cid,$depth)
{
	global $id;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('$cname','$cid',$depth,'$id')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$mcid,$depth)
{
	global $id,$cid;
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

	if ($cid == $mcid){
		$expandstring = "true";	
	}else{
		$expandstring = "false";	
	}

	return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.expanded = $expandstring;
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}


?>
