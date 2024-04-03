<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
//auth(8);

if($cid != ""){
	$SS_CID = $cid;
	$SS_DEPTH = $depth;
	$SS_NSET = $nset;
	$SS_PAGE = $page;
	session_register("SS_CID");	
	session_register("SS_DEPTH");
	session_register("SS_NSET");	
	session_register("SS_PAGE");	
}else{
	$cid = $SS_CID;
	$depth = $SS_DEPTH;
	$nset = $SS_NSET;
	$page = $SS_PAGE;
}


if($max == ""){
$max = 10; //페이지당 갯수
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




//echo "SS_CID : ".$SS_CID .":::: SS_DEPTH : ".$SS_DEPTH;
//echo "SS_NSET : ".$SS_NSET .":::: SS_DEPTH : ".$SS_PAGE;

$db = new Database;
$db2 = new Database;

if($mode == "search"){
	if($admininfo[admin_level] == 9){
		$where = "where p.id Is NOT NULL  ";
	}else{
		$where = "where p.id Is NOT NULL and admin ='".$admininfo[company_id]."' ";
	}
	
	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}
	
	if($search_text != ""){
		$where = $where."and p.".$search_type." LIKE '%".$search_text."%' ";
	}
	
	/*
	if($from_price != "" && $to_price != ""){
		$where = $where."and p.sellprice between $from_price and $to_price ";
	}
	*/
	$db2->query("SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where ");	
	//echo("SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where ");	
}else{
	if ($cid == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "Where admin ='".$company_id."'";	
			}
			$db2->query("SELECT distinct id FROM ".TBL_SHOP_PRODUCT." $addWhere");
		}else{
			$db2->query("SELECT distinct id FROM ".TBL_SHOP_PRODUCT." where admin ='".$admininfo[company_id]."'");
		}
	//	echo("SELECT * FROM ".TBL_SHOP_PRODUCT."<br>");
	//	echo $db2->total;
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
			$sql = "SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%'";			
			$db2->query($sql);	
			
			//echo $db2->total."<br>";
		}else{
			$db2->query("SELECT distinct p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' and admin ='".$admininfo[company_id]."'");	
		}
		
	}
}

$total = $db2->total;

if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&mode=$mdoe&search_type=$search_type&search_text=$search_text");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&max=$max");
	//echo $total.":::".$page."::::".$max."<br>";
}	

$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr><td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 상품 이미지관리</b> :&nbsp;&nbsp;&nbsp;<b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid, $depth):"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div>")."</td></tr>
			 <tr>
			 	<td colspan=3 align=left style='padding-bottom:10px;'>
			 	<table cellpadding=0 cellspacing=0 border=0>
					<tr><form name='search_form' method='post' action='".$HTTP_URL."' target=act><input type='hidden' name='view' value='innerview'><input type='hidden' name='mode' value='search'><!--search_text-->
					<td align=left valign='bottom' style='padding-right:5px;padding-top:1px;'>						
						<select name='search_type' class='p11 ls1' style=\"behavior: url('../js/selectbox.htc');height:20px;\"><option value='pname'>상품명</option><option value='pcode'>상품코드</option></select>
					</td>
					<td width=600 align=left valign='top'>						
						<input type=text name=search_text  id='search_texts' onkeyup='findNames();' style=\"width:300px;height:20px;border:1px solid #000000 \" value='$search_text'> 
						<input type=image src='../image/find.gif' border=0 align=absmiddle>																				
						<div style='z-index:100;display:none;position:absolute;overflow:auto;height:150px;width:300px;background-color:#FFFAFA' id='popup' onmouseout='focusOutBool=true;' onmouseover='focusOutBool=false;'>											
						        <table id='search_table' bgcolor='#FFFAFA' border='0' cellspacing='0' cellpadding='1'>										            
						            <tbody id='search_table_body'></tbody>									       										            
						        </table>
						</div>										
					</td>
					<td width=50 align=center>&nbsp; </td>					
					</tr></form>
				</table>
			 	</td>
			 </tr>
			
			<tr>
			<td valign=top style='padding-top:33px;'>";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_list>			
			<!--form ><input type=hidden name='mode' value='search'>
			
			<table width='100%'>
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
					<td><input type=button value='전체보기' onclick=\"document.location.href='/admin/product_list.php'\"></td>
				</tr>
			</table>";
		
$Contents .=	"
			</form-->";
$innerview = "			
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td>
				<table cellpadding=0 cellspacing=0>
					<tr>
						<td>
						<table cellpadding=0 cellspacing=0>
							<tr>
							<td>
								".CompanyList($company_id)."
							</td>
							<td style='padding-left:10px;'>
								<select name=max style=\"behavior: url('../js/selectbox.htc'); height: 20px; width: 50px;\" align=absmiddle onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\">
								<option value='10' ".CompareReturnValue(10,$max).">10</option>
								<option value='20' ".CompareReturnValue(20,$max).">20</option>
								<option value='50' ".CompareReturnValue(50,$max).">50</option>
								<option value='100' ".CompareReturnValue(100,$max).">100</option>
								</select> 
							</td>
							<td style='padding-left:3px;'> 씩 보기</td>
							</tr>
						</table>
						</td>						
					</tr>
				</table>
				</td>
				<td align=right>".$str_page_bar."</td></tr></table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100%>			
			<tr bgcolor='#cccccc' align=center><form name=listform method=post action='product_list.act.php' onsubmit='return CheckDelete(this)' target='act'>
			<input type='hidden' name='act' value='select_delete'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='depth' value='$depth'>
				<td width='5%' class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
				<!--td width='10%' class=m_td>상품코드</td-->
				<td width='10%' class=m_td>이미지</td>
				<td width='50%' class=m_td>제품명</td>						
				<td width='20%' class=m_td>이미지</td>
				<td width='10%' class=e_td>관리</td>
			</tr>";
			
if($mode == "search"){
	/*
	if($admininfo[admin_level] == 9){
		$where = "where p.id Is NOT NULL  ";
	}else{
		$where = "where p.id Is NOT NULL and admin ='".$admininfo[company_id]."' ";
	}
	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}
	*/
	if($search_text != ""){
		$where = "and p.".$search_type." LIKE '%".$search_text."%' ";
	}
	
	/*
	if($from_price != "" && $to_price != ""){
		$where = $where."and p.sellprice between $from_price and $to_price ";
	}
	*/
	
	if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";	
			}
			$sql = "SELECT distinct p.id, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,
			p.company, p.pcode, p.coprice,  p.new, p.hot, p.event, p.disp, p.editdate, p.reserve_rate, 
			case when vieworder = 0 then 100000 else vieworder end as vieworder2 
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c 
			where c.company_id = p.admin and p.id = r.pid  $addWhere $where $orderbyString LIMIT $start, $max";
			//echo $sql;
			$db->query($sql);
		}else{
			$sql = "SELECT distinct p.id, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,
			p.company, p.pcode, p.coprice,  p.new, p.hot, p.event, p.disp, p.editdate, p.reserve_rate, 
			case when vieworder = 0 then 100000 else vieworder end as vieworder2 
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c 
			where c.company_id = p.admin and p.id = r.pid  and admin ='".$admininfo[company_id]."' $where LIMIT $start, $max";
			
			$db->query($sql);
		}
	//$db->query("SELECT p.id, p.pname, p.sellprice, p.regdate,p.vieworder FROM ".TBL_SHOP_PRODUCT." p $where LIMIT $start, $max");	
	
	
	
}else{		

	if($orderby == "date"){
		$orderbyString = "order by regdate desc, vieworder2 asc, id desc";
	}else{
		$orderbyString = "order by regdate desc, vieworder2 asc, id desc";
	}	
	if ($cid == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";	
			}
			$sql = "SELECT distinct p.id, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name,   p.search_keyword,
			p.company, p.pcode, p.coprice, p.new, p.hot, p.event, p.disp, p.editdate, p.reserve_rate, 
			case when vieworder = 0 then 100000 else vieworder end as vieworder2 
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c 
			where c.company_id = p.admin and p.id = r.pid  $addWhere $orderbyString LIMIT $start, $max";
			$db->query($sql);
		}else{
			$sql = "SELECT distinct p.id, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name,  p.search_keyword,
			p.company, p.pcode, p.coprice,  p.new, p.hot, p.event, p.disp, p.editdate, p.reserve_rate,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2 
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c 
			where c.company_id = p.admin and p.id = r.pid  and admin ='".$admininfo[company_id]."' LIMIT $start, $max";
			
			
			
			$db->query($sql);
		}
		
		//echo $sql;
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
			$sql = "SELECT distinct p.id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,r.cid, p.search_keyword,
				p.company, p.pcode, p.coprice,  p.new, p.hot, p.event, p.disp, p.editdate,  p.reserve_rate, 
				case when vieworder = 0 then 100000 else vieworder end as vieworder2 
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c 
				where c.company_id = p.admin and p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' order by vieworder2 asc, p.id desc LIMIT $start, $max";
			$db->query($sql);	
		}else{
			$sql = "
				SELECT distinct p.id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder, r.cid, p.search_keyword,
				p.company, p.pcode, p.coprice,  p.new, p.hot, p.event, p.disp, p.editdate,  p.reserve_rate, 
				case when vieworder = 0 then 100000 else vieworder end as vieworder2 
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c 
				where c.company_id = p.admin and p.id = r.pid and r.cid LIKE '".substr($cid,0,$cut_num)."%' and admin ='".$admininfo[company_id]."' order by vieworder2 asc, p.id desc LIMIT $start, $max";
			$db->query($sql);
			
		}
		
		
}		
		
	}
if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=8 align=center> 등록된 제품이 없습니다.</td></tr>";
}else{	
	
	
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
		
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$db->dt[id].".gif")){
			$img_str = $admin_config[mall_data_root]."/images/product/s_".$db->dt[id].".gif";
			$img_str_m = $admin_config[mall_data_root]."/images/product/ms_".$db->dt[id].".gif";
		}else{
			$img_str = "../image/no_img.gif";
			$img_str_m = "../image/no_img.gif";
		}
	
	$innerview .= "<tr bgcolor='#ffffff'>
					<td bgcolor='#efefef' align=center><input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$db->dt[id]."'><input type=hidden class=nonborder id='pid' name=pid[] value='".$db->dt[id]."'></td>
					<td bgcolor='#ffffff' align=center >
					<img src='".$img_str."' width=50 height=50>
					</td>
					<td>
						<table cellpadding=3 cellspacing=0 width='100%'>
						<tr>
							<td>
							<a href='product_input.php?id=".$db->dt[id]."' style='color:orange;'><b >".$db->dt[pname]."</b></a> 
							</td>
						</tr>												
						<tr>
							<td nowrap> 
							<input type='radio' onclick=\"changeImageInputMode('".$db->dt[id]."', this.value);\" name='select_img_".$db->dt[id]."' value='1' id='select_img_".$db->dt[id]."_1' checked><label for='select_img_".$db->dt[id]."_1'>전체 이미지 등록</label>
							<input type='radio' onclick=\"changeImageInputMode('".$db->dt[id]."', this.value);\" name='select_img_".$db->dt[id]."' value='2' id='select_img_".$db->dt[id]."_2' ><label for='select_img_".$db->dt[id]."_2'>개별이미지 등록</label>
							<table cellpadding=3 cellspacing=0 id='img_all_table_".$db->dt[id]."' style='display:block;'>
								<tr><td><b>전체이미지 등록</b></td><td><input type=file name='allimg' size=30 style='font-size:8pt'> </td></tr>
							</table>
							<table cellpadding=3 cellspacing=0 id='img_one_table_".$db->dt[id]."' style='display:none;'>
								<tr><td><b>개별이미지(500×500)</b></td><td><input type=file name='bimg' size=30 style='font-size:8pt'></td><td><img src='../image/ico_img_view.gif'></td></tr>
								<tr><td><b>개별이미지(300×300)</b></td><td><input type=file name='mimg' size=30 style='font-size:8pt'></td><td><img src='../image/ico_img_view.gif'></td></tr>
								<tr><td><b>개별이미지(137×137)</b></td><td><input type=file name='msimg' size=30 style='font-size:8pt'></td><td><img src='../image/ico_img_view.gif'></td></tr>
								<tr><td><b>개별이미지(90×90)</b></td><td><input type=file name='simg' size=30 style='font-size:8pt'></td><td><img src='../image/ico_img_view.gif'></td></tr>
								<tr><td><b>개별이미지(50×50)</b></td><td><input type=file name='cimg' size=30 style='font-size:8pt'></td><td><img src='../image/ico_img_view.gif'></td></tr>
							</table>
							</td>
						</tr>
						
						</table>
					</td>
					
					<td bgcolor='#efefef' align=center nowrap>
						<img src='".$img_str_m."'>
					</td>
					<td bgcolor='#ffffff' style='padding-left:10px;' nowrap>
					<table >						
						<tr><td><a href=\"javascript:CopyData(document.forms['listform'], '".$db->dt[id]."','".$db->dt[pname]."');\"><img src='../image/bt_modify.gif' border=0 align=absmiddle title=\" ' ".$db->dt[pname]." '  에 대한 정보를 수정합니다.\"></a></td></tr>						
						<tr><td><img src='../image/bt_del.gif' border=0 align=absmiddle style='cursor:hand' border=0 onclick=\"deleteProduct('delete','".$db->dt[id]."','&cid=$cid&depth=$depth')\"></td></tr>
					</table>
					</td>
				</tr>
				<tr height=1><td colspan=7 background='/img/dot.gif'></td></tr>";
	
	}
}	
	$innerview .= "</table>
				<table width='100%'><tr height=30><td><a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../image/bt_all_del.gif' border=0 align=absmiddle ></a> <a href=\"JavaScript:SelectUpdate(document.forms['listform']);\"><img src='../image/bt_all_modify.gif' border=0 align=absmiddle style='cursor:hand;'></a></td><td align=right>".$str_page_bar."</td></tr></table>
				</form>
				";
	
$Contents = $Contents.$innerview ."	
			</td>
			</tr>
		</table>
		<form name=saveform method=post action='./product_list.act.php' target='act'>
				<input type='hidden' name='act' value='update_one'>
				<input type='hidden' name='pid'>						
				<input type='hidden' name='pcode'>
				<input type='hidden' name='disp'>
				<input type='hidden' name='reserve_rate'>
				
				<input type='hidden' name='search_keyword'>
				<input type='hidden' name='coprice'>
				<input type='hidden' name='sellprice' >
				
			</form>
		<iframe name='act' src='' width=0 height=0></iframe>
			";

$category_str ="<div class=box id=img3  style='width:190px;height:190px;overflow:auto;'>".Category()."</div>";

if($view == "innerview"){
	
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";	
	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path:"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	</Script>";
}else{
	$P = new LayOut();
	$P->strLeftMenu = product_menu("/manage",$category_str);
	$P->addScript = "<script Language='JavaScript' src='product_image.js'></script>";
	$P->strContents = $Contents;	
	$P->PrintLayOut();
}

function SelectViewOrder($vPid,$thisorder)
{
	
$mdb = new Database;
$mdb->query("SELECT count(vieworder) as maxorder FROM ".TBL_SHOP_PRODUCT." ");	
$mdb->fetch(0);

			$mstring = "<Select name='vieworder' onchange=\"UpdateOrder('$vPid',$thisorder,this.value)\">\n";
	for($i=0;$i<$mdb->dt[maxorder];$i++){
		if($thisorder == $i){
			$mstring = $mstring."<option value='$i' selected>$i</option>\n";
		}else{
			$mstring = $mstring."<option value='$i'>$i</option>\n";
		}
	}	
		$mstring = $mstring."</Select>\n";
		
	return $mstring;

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
