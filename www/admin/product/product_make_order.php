<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
//auth(8);
//echo phpinfo();
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
		$where = "where p.id Is NOT NULL  and p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' ";
	}else{
		$where = "where p.id Is NOT NULL and admin ='".$admininfo[company_id]."' and p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' ";
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
		$where = $where."and p.stock = 0 ";
	}else if($stock_status == "shortage"){
		$where = $where."and p.stock < p.safestock ";
	}else if($stock_status == "surplus"){
		$where = $where."and p.stock > p.safestock ";
	}
	
	$db->query("SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where ");	
	//$db->query("SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where ");	
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

if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page, $max, "&max=$max&mode=search&stock_status=$stock_status");
}else{
	$str_page_bar = page_bar($total, $page, $max, "&max=$max");
}	
	

$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			<tr><td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 상품 생산지지서 발행</b> :&nbsp;&nbsp;&nbsp;<b id='select_category_path1'>전체</b></div>")."</td></tr>
			<tr>
			<td valign=top style='padding-top:33px;'>";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_stock>			
			";
$innerview = "			
			<table width='100%'><form name=makeorderfrm method=post action='product_stock.act.php' target='act'><input type='hidden' name='act' value='update'>
			<tr>
				<td width='10%'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;' align=center>재고상태</div>")."</td>
				<td colspan=6 align=left style='padding:10px;' width='90%'>
				<input type='radio' name='stock_status' value='whole' id='owhole' onclick=\"document.frames['act'].location.href='product_make_order.php?view=innerview&cid=$cid&depth=$depth&mode=search&stock_status=whole&max=$max'\" ".CompareReturnValue("whole","$stock_status"," checked")."><label for='owhole'>전체</label>
				<input type='radio' name='stock_status' value='soldout' id='osoldout' onclick=\"document.frames['act'].location.href='product_make_order.php?view=innerview&cid=$cid&depth=$depth&mode=search&stock_status=soldout&max=$max'\" ".CompareReturnValue("soldout","$stock_status"," checked")."><label for='osoldout'>품절</label>
				<input type='radio' name='stock_status' value='shortage' id='oshortage' onclick=\"document.frames['act'].location.href='product_make_order.php?view=innerview&cid=$cid&depth=$depth&mode=search&stock_status=shortage&max=$max'\" ".CompareReturnValue("shortage","$stock_status"," checked")."><label for='oshortage'>부족</label>
				<input type='radio' name='stock_status' value='surplus' id='osurplus' onclick=\"document.frames['act'].location.href='product_make_order.php?view=innerview&cid=$cid&depth=$depth&mode=search&stock_status=surplus&max=$max'\" ".CompareReturnValue("surplus","$stock_status"," checked")."><label for='osurplus'>여유</label>
				</td>
			</tr>
			<tr height=30>				
				<td colspan=2 nowrap>
					<table>
						<tr>
						<td>".CompanyList($company_id)."</td>
						<td style='padding-left:0px;' nowrap><select name=max style=\"behavior: url('../js/selectbox.htc'); height: 20px; width: 50px;\" onchange=\"document.frames['act'].location.href='".$HTTP_URL."?view=innerview&max='+this.value\">
							<option value='5' ".CompareReturnValue(5,$max).">5</option>
							<option value='10' ".CompareReturnValue(10,$max).">10</option>
							<option value='20' ".CompareReturnValue(20,$max).">20</option>
							<option value='50' ".CompareReturnValue(50,$max).">50</option>
							<option value='100' ".CompareReturnValue(100,$max).">100</option>
							</select></td>
						<td nowrap> 씩 보기</td>
						</tr>
					</table>
				</td>				
				
				<td align=right nowrap>".$str_page_bar."</td>
				
			</tr>
			</table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray width='100%'>			
			<tr align=center height=25>
				<!--td width='5%' class=s_td>&nbsp;<input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td-->
				<td width='10%' class=s_td>상품코드</td>
				<td width='10%' class=m_td>이미지</td>
				<td width='30%' class=m_td>제품명</td>
				<td width='15%' class=m_td>규격</td>		
				<td width='15%' class=e_td>관리</td>
								
			</tr>
			";
if($mode == "search"){
	if($admininfo[admin_level] == 9){
		$where = "where c.company_id = p.admin and p.id Is NOT NULL  ";
	}else{
		$where = "where c.company_id = p.admin and p.id Is NOT NULL and admin ='".$admininfo[company_id]."' ";
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
		$where = $where."and p.stock = 0 ";
	}else if($stock_status == "shortage"){
		$where = $where."and p.stock < p.safestock ";
	}else if($stock_status == "surplus"){
		$where = $where."and p.stock > p.safestock ";
	}	
	
	
	$sql = "SELECT p.id, p.pcode, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name, p.disp, stock, safestock, case when vieworder = 0 then 100000 else vieworder end as vieworder2 FROM ".TBL_SHOP_PRODUCT." p , ".TBL_COMMON_COMPANY_DETAIL." c $where LIMIT $start, $max";	
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
			$db->query("SELECT distinct p.id, p.pcode, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name, p.disp, stock, safestock, case when vieworder = 0 then 100000 else vieworder end as vieworder2 FROM ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_COMPANY_DETAIL." c where c.company_id = p.admin $addWhere $orderbyString LIMIT $start, $max");
			
		}else{
			$db->query("SELECT distinct p.id, p.pcode, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name, p.disp, stock, safestock, case when vieworder = 0 then 100000 else vieworder end as vieworder2 FROM ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_COMPANY_DETAIL." c where c.company_id = p.admin and admin ='".$admininfo[company_id]."' LIMIT $start, $max");
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
	
	$innerview .= "	<tr bgcolor='#ffffff'>
					<!--td bgcolor='#efefef' align=center><input type=checkbox class=nonborder name='".$db->dt[id]."'></td-->
					<td align=center nowrap><!--a href='/pinfo.php?id=".$db->dt[id]."'-->".$db->dt[pcode]."<!--/a--><input type=hidden name='pid[]' value='".$db->dt[id]."'></td>
					<td bgcolor='#efefef' align=center ><img src='".$img_str."' width=50 height=50></td>
					<td width=250>".$db->dt[pname]."</td>
					<td bgcolor='#efefef' align=center><!--input type=text name='stock".$db->dt[id]."' value='".$db->dt[stock]."' size=6-->".MakeOptionByMakeOrder($db->dt[id])."</td>					
					<td bgcolor='#ffffff' align=center nowrap>
					<table >
						<!--tr><td><a href='product_input.php?id=".$db->dt[id]."'><img src='../image/bt_modify.gif' border=0 align=absmiddle ></a></td></tr-->
						<tr><td><a href=\"JavaScript:PrintMakeOrder('".$db->dt[id]."',document.forms['makeorderfrm'].option_standard_".$db->dt[id].")\">생산지시서 발행</a></td></tr>
						<!--tr><td><img src='../image/bt_del.gif' border=0 align=absmiddle style='cursor:hand' border=0 onclick=\"deleteProduct('delete','".$db->dt[id]."')\"></td></tr-->
					</table>
					</td>					
				</tr>
				<tr height=1><td colspan=8 background='/img/dot.gif'></td></tr>";
	
	}
}	
	$innerview .= "	</table>
				<table width='100%'>
					<tr height=30><td>".($stock_status == "shortage" ? "<a href=\"javascript:PrintWindow('./print_stock.php?$QUERY_STRING',700,900,'print_stock')\">재고 내역서 출력</a>":"")."</td><td align=right nowrap>".$str_page_bar."</td></tr>
					<tr><td align=left></td><td  align=right style='padding:10px;'><input type='image' src='../image/b_edit.gif' border=0></td></tr>
				</table></form>";
	
$Contents = $Contents.$innerview ."			
			</td>
			</tr>
		</table>
		<iframe name='act' src='' width=0 height=0></iframe>
			";

$help_text = "- 부족한 상품의 옵션을 선택하신후 생산지시서 발행 버튼을 눌러주세요.<br>	";

$Contents .= HelpBox("상품 생산지시서 발행", $help_text);

$category_str ="<div class=box id=img3  style='width:190px;height:190px;overflow:auto;'>".Category()."</div>";


if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";	
	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_stock').innerHTML = document.body.innerHTML;	
	parent.document.getElementById('select_category_path1').innerHTML='".$inner_category_path."';
	</Script>";
	
}else{
	$P = new LayOut();
	$P->strLeftMenu = product_menu("/manage",$category_str);
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='product_make_order.js'></script>";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}


function MakeOptionByMakeOrder($pid){
	global $user;
	$mdb = new Database;
	
	$sql = "select id, option_div,option_price, option_m_price,option_d_price,option_a_price, option_stock from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a, ".TBL_SHOP_PRODUCT_OPTIONS." b where a.opn_ix = b.opn_ix and a.pid = '$pid' and b.option_kind = 'b' ";
	
	$mdb->query($sql);
	
	
	if ($mdb->total == 0){
		return "<input type=hidden name='option_standard_".$pid."' value=1>";
	}else{
		$mString = "<Select name='option_standard_".$pid."' >\n";	//onchange=\"ChangeOption('".$user[mem_level]."',this, this.selectedIndex);\">";	
		$mString .= "<option value='0' stock='0' price='0'>규격을 선택해주세요</option>\n";
	
		$i=0;
		for($i=0;$i < $mdb->total; $i++){			
			$mdb->fetch($i);			
			$mString .= "<option value='".$mdb->dt[id]."' stock='".$mdb->dt[option_stock]."' price='".$mdb->dt[option_price]."' m_price='".$mdb->dt[option_m_price]."' d_price='".$mdb->dt[option_d_price]."' a_price='".$mdb->dt[option_a_price]."'>".$mdb->dt[option_div]."</option>\n";
						
		}
		$mString .= "</select>";
	}
	
	
	return $mString;
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