<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
//auth(8);

if(!$_GET["max"]){
	$max = 20; //페이지당 갯수
}else{
	$max = $_GET["max"];
	session_register("max");
}

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}


$db = new Database;

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


$ptotal = $db->total;


$str_page_bar = page_bar($ptotal, $page, $max, "&cid=$cid&depth=$depth&company_id=$company_id&max=$max");


$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
			<td valign=top style='padding-top:33px;' >";

$Contents .=	"
			</td>
			<td valign=top style='padding:20px;padding-top:0px;' id='product_orderarea'>
			<!--form ><input type=hidden name='mode' value='search'>

			<table width='80%'>
				<tr height=25 align=center>
					<td>상품코드</td>
					<td>제품명</td>
					<td>가격</td>
					<td>검색</td>
				</tr>
				<tr height=25 align=center>
					<td><input type=text name=pid value='$pid' size=8></td>
					<td><input type=text name=pname value='$pname' size=30></td>
					<td nowrap><input type=text name=from_price value='$from_price' size=10> ~ <input type=text name=to_price value='$to_price' size=10></td>
					<td><input type=submit value='search'></td>
					<td><input type=button value='전체보기' onclick=\"document.location.href='/admin/product_order.php'\"></td>
				</tr>
			</table>
			</form-->";
$innerview = "
			<table width='100%' cellpadding=2 cellspacing=0 border=0>
			<col width=10%>
			<col width=60%>
			<col width=15%>
			<col width=15%>
			<tr>
			    <td align='left' colspan=4 > ".GetTitleNavigation("분류별 상품배열", "상점관리 > 분류별 상품배열")."</td>
			</tr>
			<tr>
			    <td align='left' colspan=4 id='array_info'> </td>
			</tr>
			<tr>
			    <td align='left' colspan=4 id='array_info2'> </td>
			</tr>";
			if($cid!="") {
				$inner_category_path = getCategoryPathByAdmin($cid, $depth);
			} else {
				$inner_category_path ="전체";
			}
$innerview .= "
			<tr><td colspan=4 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;text-align:right;'><b> 선택된 카테고리</b> :&nbsp;&nbsp;&nbsp;<b id='select_category_path1'>$inner_category_path</b></div>")."</td></tr>
			<tr>
				<td height=30 colspan=1>
					<table cellpadding=0 cellspacing=0>
						<tr>";
						if($admininfo[mall_type] == "B"){
$innerview .= "<td style='padding-right:3px;'>".CompanyList($company_id, "","onChange=\"document.location.href='".$HTTP_URL."?cid=$cid&depth=$depth&company_id='+this.value+'&max='+document.getElementById('max').value\"")."</td>";
						}
$innerview .= "<td ><select name=max id='max' style=\"font-size:12px;height: 20px; width: 80px;\" align=absmiddle onchange=\"document.location.href='".$HTTP_URL."?cid=$cid&depth=$depth&company_id='+document.getElementById('company_id').value+'&max='+this.value\"><!--view=innerview& behavior: url('../js/selectbox.htc'); -->
							<option value='10' ".CompareReturnValue(10,$max).">10</option>
							<option value='20' ".CompareReturnValue(20,$max).">20</option>
							<option value='30' ".CompareReturnValue(30,$max).">30</option>
							<option value='40' ".CompareReturnValue(40,$max).">40</option>
							<option value='50' ".CompareReturnValue(50,$max).">50</option>
							<option value='100' ".CompareReturnValue(100,$max).">100</option>
							<!--option value='200' ".CompareReturnValue(200,$max).">200</option>
							<option value='300' ".CompareReturnValue(300,$max).">300</option>
							<option value='400' ".CompareReturnValue(400,$max).">400</option>
							<option value='500' ".CompareReturnValue(500,$max).">500</option>
							<option value='600' ".CompareReturnValue(600,$max).">600</option>
							<option value='1000' ".CompareReturnValue(1000,$max).">1000</option-->
							</select></td>
						<td nowrap> &nbsp;씩 보기</td>
						</tr>
					</table>
				</td>
				<td width=150></td>
				<td colspan=2 align=right nowrap>".$str_page_bar."</td>

			</tr>
			</table>
			<table width='100%' cellpadding=2 cellspacing=0 border=0 ><!--onselectstart='return false;' ondragstart='return false;'-->
			<col width=8%>
			<col width=*>
			<col width=10%>
			<col width=10%>
			<col width=10%>
			<col width=7%>
			<col width=10%>
			<form name=vieworderform method=post action='./order.act.php' target='act'>
			<tr height=25 align=left>
				<td colspan=5>
				<a href='javascript:moveTreeGroup(1);'>아래(↓)</a> | <a href='javascript:moveTreeGroup(-1);'>위(↑)</a> |
				<a href='javascript:moveTreeGroup(5);'>아래로5칸</a> | <a href='javascript:moveTreeGroup(-5);'>위로5칸</a> |
				<a href='javascript:moveTreeGroup(10);'>아래로10칸</a> | <a href='javascript:moveTreeGroup(-10);'>위로10칸</a> |
				<a href='javascript:moveTreeGroup(-10000);'>페이지 마지막</a> | <a href='javascript:moveTreeGroup(10000);'>페이지 맨위</a>
				</td>
			</tr>
			<tr height=25 align=center>
				<td  class=s_td>이미지</td>
				<td class=m_td>제품명</td>
				<td  class=m_td>상태</td>
				<td  class=m_td>판매가</td>
				<td  class=m_td>재고수</td>
				<td  class=m_td>주문수</td>
				<td  class=e_td>view order</td>
			</tr>
			</table>

			<table cellpadding=2 cellspacing=0 width=100% onselectstart='return false;' ondragstart='return false;' id='list_table'><!--frame=hsides rules=rows-->
			<col width=8%>
			<col width=*>
			<col width=10%>
			<col width=10%>
			<col width=10%>
			<col width=7%>
			<col width=10%>
			";


	if($orderby == "date"){
		$orderbyString = "order by vieworder2 asc, regdate desc, id desc";
	}else{
		$orderbyString = "order by vieworder2 asc, regdate desc, id desc";
	}
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
	if ($cid == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";
			}
			$sql = "SELECT r.cid, p.id, p.pcode, p.pname, p.sellprice, p.reserve,  p.regdate,p.vieworder,p.order_cnt,p.stock, p.state,  c.com_name,   case when vieworder = 0 then 100000 else vieworder end as vieworder2
							FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
							where c.company_id = p.admin and p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' $addWhere $orderbyString
							LIMIT $start, $max";
			$db->query($sql);

		}else{
			$sql = "SELECT r.cid, p.id, p.pcode, p.pname, p.sellprice, p.reserve,p.regdate,p.vieworder,p.order_cnt,p.stock, p.state,  c.com_name, case when vieworder = 0 then 100000 else vieworder end as vieworder2
							FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
							where c.company_id = p.admin and p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' and admin ='".$admininfo[company_id]."'
							LIMIT $start, $max";
			$db->query($sql);
		}
	}else{

		if($admininfo[admin_level] == 9){
			$sql = "SELECT r.cid, p.id, p.pcode, p.pname, p.sellprice, p.reserve,p.regdate,c.com_name,p.vieworder,p.order_cnt,p.stock,  p.state, case when vieworder = 0 then 100000 else vieworder end as vieworder2
							FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
							where c.company_id = p.admin and p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%'
							order by vieworder2 asc, p.id desc
							LIMIT $start, $max";
			$db->query($sql);
		}else{
			$sql = "SELECT r.cid, p.id, p.pcode, p.pname, p.sellprice, p.reserve, p.regdate,c.com_name,p.vieworder,p.order_cnt, p.stock,p.state, case when vieworder = 0 then 100000 else vieworder end as vieworder2
							FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c
							where c.company_id = p.admin and p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' and admin ='".$admininfo[company_id]."'
							order by vieworder2 asc, p.id desc
							LIMIT $start, $max";
			$db->query($sql);
		}
	}


if($db->total == 0){
	$innerview = $innerview."<tr bgcolor=#ffffff height=50><td colspan=8 align=center> 등록된 제품이 없습니다.</td></tr>";
}else{
	$total = $db->total;
	for ($i = 0; $i < $db->total; $i++)
	{

		$db->fetch($i);

		if($db->dt[state] == 1){
			$state_str = "판매중";
		}else if($db->dt[state] == 6){
			$state_str = "등록신청중";
		}else if($db->dt[state] == 7){
			$state_str = "수정신청중";
		}else if($db->dt[state] == 0){
			$state_str = "일시품절중";
		}

		//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$db->dt[id].".gif")){
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s"))) {
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s");
		}else{
			$img_str = "../image/no_img.gif";
		}
	// tr의 모양을 바꿀 경우 스크립트도 같이 수정해야 함 product_order3.js moveTreeGroup() kbk
	$innerview .= "<tr height=40 class='dot_xx' align=center style='cursor:pointer;' onclick=\"spoit(this)\" id='".$db->dt[id]."'>
					<td><img src='".$img_str."' width=30 height=30 align=absmiddle style='border:1px solid #efefef'></td>
					<td style='padding-left:0px;line-height:130%' align=left>
					<b style='color:gray' class=small>".getCategoryPathByAdmin($db->dt[cid], 4)."</b>
					".($db->dt["new"] == 1 ? "<img src='/icon/icon_new.gif' border=0 align=absmiddle>":"")."
					".($db->dt["hot"] == 1 ? "<img src='/icon/icon_hot.gif' border=0 align=absmiddle>":"")."
					".($db->dt["event"] == 1 ? "<img src='/icon/icon_event.gif' border=0 align=absmiddle>":"")."<br>
					<b>[".$db->dt[id]."] ".$db->dt[pname]."</b><br>
					</td>
					<td>
					".$state_str."
					</td>
					<td>".number_format($db->dt[sellprice],0)." 원</td>
					<td style='padding-right:5px;'>".number_format($db->dt[stock],0)." </td>
					<td>".number_format($db->dt[order_cnt],0)." </td>
					<td style='line-height:130%' class=small nowrap>".$db->dt[vieworder]."<!--".$db->dt[regdate]."<br>".$db->dt[company_name]."-->
					<input type=hidden name=sno[] value='".$db->dt[id]."'>
					<input type=hidden name=sort[] value='".$db->dt[vieworder]."'>
					</td>
				</tr>
				<!--tr height=1><td colspan=8 class='dot-x'></td></tr-->";

	}
}
	$innerview .= "

				</table>
				<table width='100%'>
					<tr bgcolor=#ffffff><td height=50 align=center><input type=image src='../image/b_save.gif' border=0 align=absmiddle></td></tr>
					<tr><td align=right nowrap>".$str_page_bar."</td></tr>
				</table></form>
				<!--form name=vieworderform method=post action='./order.act.php' target='act'>
				<input type='hidden' name='vieworder'>
				<input type='hidden' name='_vieworder'>
				<input type='hidden' name='pid'>
				<input type='hidden' name='cid' value='$cid'>
				<input type='hidden' name='depth' value='$depth'>
				<input type='hidden' name='max' value='$max'>
				<input type='hidden' name='page' value='$page'>
				<input type='hidden' name='nset' value='$nset'>
				</form-->
				";

$Contents = $Contents.$innerview ."


			</td>
			</tr>
		</table>";

$help_text = "
	<table>
		<tr>
			<td style='line-height:150%' class=small>
			<img src='../image/icon_list.gif' align=absmiddle>먼저 상품배열을 변경하시고자 하는 카테고리를 선택해주세요<br>
			<img src='../image/icon_list.gif' align=absmiddle>상품배열을 변경하시고자 하는 제품을 클릭한후 <b>↑ ↓ 방향키</b>를 눌러서 이동하시후 <b>저장</b>버튼을 누르시면 저장됩니다<br>
			</td>
		</tr>
	</table>
	";
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle' border='0' ></td><td class='small' >먼저 상품배열을 변경하시고자 하는 카테고리를 선택해주세요</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle' border='0' ></td><td class='small' style='line-height:120%'>상품배열을 변경하시고자 하는 제품을 선택 합니다. ctrl 키를 누른후 선택하시면 복수개의 상품을 선택하여 한꺼번에 변경이 가능합니다. 연속된 복수개의 상품의 순서 변경을 원하실 경우 시작 상품 선택후 shift 키와 같이 선택하시면 복수개의 상품을 선택 하실수 있습니다.</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle' border='0' ></td><td class='small' style='line-height:120%' >선택된 상품들의 순서를 변경하기 위해서는  <b>↑ ↓ 방향키</b>를 눌르거나 아래(↓) | 위(↑) | 아래로5칸 | 위로5칸 | 아래로10칸 | 위로10칸 | 페이지 마지막 | 페이지 맨위  버튼을 클릭해서 순서를 변경 합니다. </td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle' border='0' ></td><td class='small' >변경된 상품의 노출 순서를 저장하기 위해서는 아래 <b>저장</b>버튼을 누르시면 저장됩니다</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle' border='0' ></td><td class='small' >한페이지에 더 많은 상품을 보기 위해서는 상단에 셀렉트 박스의 숫자를 증가 시키면 많은 상품을 보실수 있습니다</td></tr>
</table>
";

$Contents .= HelpBox("분류별 상품배열", $help_text, 120);

$category_str ="<div class=box id=img3  style='width:146px;height:250px;overflow:auto;padding:4px'>".Category()."</div>";


if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_orderarea').innerHTML = document.body.innerHTML;
	//parent.document.vieworderform.max.value='$max';
	parent.document.getElementById('select_category_path1').innerHTML='".$inner_category_path."';
	</Script>";
}else{
	$P = new LayOut();
	$P->strLeftMenu = display_menu("/admin",$category_str);
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script><script Language='JavaScript' src='product_order3.js'></script>";
	$P->Navigation = "HOME > 상품관리 > 분류별 상품배열";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->BodyFunctionAdd = "ondragstart='return false' onselectstart='return false'";
	$P->PrintLayOut();
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

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

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

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

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

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.expanded = $expandstring;
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}

?>