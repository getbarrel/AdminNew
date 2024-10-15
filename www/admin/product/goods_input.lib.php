<?
function getStandardCategoryMultipleSelect($category_text ="기본카테고리 선택", $object_name="cid",$id="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	$tb = "standard_category_info";
	//echo "<script>alert('1')</script>";
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM ".$tb." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%'  and category_use != '0'  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' class='$id' validation=false multiple style='1px solid silver;height:155px;width:100%;'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' class='$id' validation=false multiple  style='border:1px solid silver;height:155px;width:100%;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}



function PrintStandardCategoryRelation($pid){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth,r.basic, r.psr_ix, r.regdate  from shop_product_standard_relation r, standard_category_info c where pid = '$pid' and c.cid = r.cid ORDER BY r.regdate ASC ";
	//echo $sql;
	$db->query($sql);

	$mString = "<table width=100% border=0 cellpadding=0 cellspacing=0 id=objStandardCategory>
						";

	if ($db->total == 0){
		 
		$mString = $mString."<tr bgcolor=#ffffff height=25><td colspan=5 align=left style='padding-left:20px;'><b style='color:#ff2a32'> [GUIDE]</b> 선택된 표준 카테고리 정보가 없습니다. (제휴 판매를 위해서는 표준카테고리 등록이 필요합니다.) </td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentStandardCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='display_standard_category[]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<td class='table_td_white small' width='50'><input type='radio' name='standard_basic' id='standard_basic_".$db->dt[cid]."' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."  validation=true title='기본카테고리' ></td>
				<td class='table_td_white small ' width='*'><label for='standard_basic_".$db->dt[cid]."' >".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</label></td>
				<td class='table_td_white' width='100'><!--a href=\"JavaScript:void(0)\" onClick='standard_category_del(this.parentNode.parentNode)'--><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\" $(this).closest('tr').remove();\" style='cursor:point;'><!--/a--></td>
				</tr>";//onClick='category_del(this.parentNode.parentNode)' 를 onClick='category_del(true,this.parentNode.parentNode)' 로 변경 kbk 13/06/30<img src='../images/".$admininfo["language_"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(true,this.parentNode.parentNode)' style='cursor:pointer;' />
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";

	return $mString;
}



function search_select($pid){
$listdb = new Database;

$listdb->query("SELECT m.id,m.myshopping_desc,s.disp  FROM myshopping m left outer join search_relation s on m.id = s.search_id and s.pid = '$pid'");

	$mstr = "	<select name=search_id[] style='height:130px;width:630px;'multiple>";

	for($i=0;$i < $listdb->total;$i++){
	$listdb->fetch($i);
		if($listdb->dt[disp] == 1){
			$mstr = $mstr."		<option value='".$listdb->dt[id]."' selected>".($i+1).". ".$listdb->dt[myshopping_desc]." ".$listdb->dt[disp]."</option>";
		}else{
			$mstr = $mstr."		<option value='".$listdb->dt[id]."'>".($i+1).". ".$listdb->dt[myshopping_desc]." ".$listdb->dt[disp]."</option>";
		}
	}
	$mstr = $mstr."  </select>";

	return $mstr;

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
	node$cid.action = \"setCategory('insert','$cname','$cid',$depth,'$id')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$cid,$depth)
{
	global $id;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$Parentdepth = $depth - 1;

	if ($depth+1 == 1){
		$cid1 = "000";
	}else if($depth+1 == 2){
		$cid2 = "000";
	}else if($depth+1 == 3){
		$cid3 = "000";
	}else if($depth+1 == 4){
		$cid4 = "000";
	}else if($depth+1 == 3){
		$cid5 = "000";
	}

	$parent_cid = "$cid1$cid2$cid3$cid4$cid5";

	if ($depth ==1){
		$ParentNodeCode = "node$parent_cid";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	return "		var groupnode$cid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$cid.tooltip = '$cname';
		groupnode$cid.id ='nodeid$cid';
		groupnode$cid.action = \"setCategory('insert','$cname','$cid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$cid);\n\n";
}

function Category()
{
	global $id;
	global $db;

$cate = "
<script language=\"JavaScript\" src=\"../include/manager.js\"></script>
<script language=\"JavaScript\" src=\"../include/Tree.js\"></script>
<script>

/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;


/*	Create Root node	*/
	var rootnode = new TreeNode(\"상품카테고리\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('상품카테고리','000000000000000',-1,0,'".$id."')\";
	rootnode.expanded = true;";



$db->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);

	if ($db->dt["depth"] == 0){
		$cate = $cate. PrintNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 1){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 2){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 3){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 4){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}
}

$cate = $cate."	tree.addNode(rootnode);";
$cate = $cate."
</script>
<form>
<div id=TREE_BAR style=\"margin:5;\">
<script>
tree.draw();
tree.nodes[0].select();
</script>
</div>
</form>";

return $cate;
}

function PrintRelation($pid){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth,r.basic, r.rid, r.regdate  from ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c where pid = '$pid' and c.cid = r.cid ORDER BY r.regdate ASC ";
	

	$db->query($sql);

	$mString = "<table width=100% cellpadding=0 cellspacing=0 id=objCategory>
						";

	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=25><td colspan=5 align=left style='padding-left:5px;'><b style='color:#ff2a32'> [GUIDE]</b> 선택된 카테고리 정보가 없습니다. ( 카테고리 선택은 필수 정보입니다.) </td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='display_category[]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<td class='table_td_white small' width='50'><input type='radio' name='basic' id='basic_".$db->dt[cid]."' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."  validation=true title='기본카테고리' ></td>
				<td class='table_td_white small ' width='*'><label for='basic_".$db->dt[cid]."' >".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</label></td>
				<td class='table_td_white' width='100'><!--a href=\"JavaScript:void(0)\" onClick='category_del(this.parentNode.parentNode)'--><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\" category_del(this.parentNode.parentNode)\" style='cursor:point;'><!--/a--></td>
				</tr>";//onClick='category_del(this.parentNode.parentNode)' 를 onClick='category_del(true,this.parentNode.parentNode)' 로 변경 kbk 13/06/30<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(true,this.parentNode.parentNode)' style='cursor:pointer;' />
				//$(this).closest('tr').remove();
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";

	return $mString;
}


function PrintRelation2($pid){
	global $db, $admininfo ;

	$sql = "select c.cid,c.cname,c.depth,r.basic, r.rid, r.regdate  from ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_SNS_CATEGORY_INFO." c where pid = '$pid' and c.cid = r.cid ";


	$db->query($sql);

	$mString = "<table width=100% cellpadding=0 cellspacing=0 id=objCategory>
						";

	if ($db->total == 0){
		//$mString = $mString."<tr bgcolor=#ffffff height=45><td colspan=5 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth],"sns");
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='display_category[]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<td class='table_td_white small' width='50'><input type='radio' name='basic' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."></td>
				<td class='table_td_white small ' width='*'>".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</td>
				<td class='table_td_white' width='100'><!--a href=\"JavaScript:void(0)\" onClick='category_del(this.parentNode.parentNode)'--><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(this.parentNode.parentNode)' style='cursor:pointer;' /><!--/a--></td>
				</tr>";
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";

	return $mString;
}

function PrintMinishopRelation($pid){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth,r.basic, r.rid, r.regdate  from shop_minishop_relation_product r, shop_minishop_category_info c where r.company_id = '".$_SESSION["admininfo"]['company_id']."' AND c.company_id = '".$_SESSION["admininfo"]['company_id']."' AND pid = '$pid' and c.cid = r.cid ORDER BY r.regdate ASC ";
	$db->query($sql);

	$mString = "<table width=100% cellpadding=0 cellspacing=0 id=minishop_objCategory>
						";

	if ($db->total == 0){
		//$mString = $mString."<tr bgcolor=#ffffff height=45><td colspan=5 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='minishop_display_category[]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<td class='table_td_white small' width='50'><input type='radio' name='minishop_basic' id='minishop_basic_".$db->dt[cid]."' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."  validation=true title='기본카테고리' ></td>
				<td class='table_td_white small ' width='*'><label for='minishop_basic_".$db->dt[cid]."' >".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</label></td>
				<td class='table_td_white' width='100'><!--a href=\"JavaScript:void(0)\" onClick='category_del(this.parentNode.parentNode)'--><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\" $(this).closest('tr').remove();\" style='cursor:point;'><!--/a--></td>
				</tr>";//onClick='category_del(this.parentNode.parentNode)' 를 onClick='category_del(true,this.parentNode.parentNode)' 로 변경 kbk 13/06/30<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(true,this.parentNode.parentNode)' style='cursor:pointer;' />
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";

	return $mString;
}

function PrintAddImage($pid){
	global $db, $admin_config;

	$sql = "select id from ".TBL_SHOP_ADDIMAGE." a where pid = '$pid' ";
	$db->query($sql);

	$mString = "<table cellpadding=5 cellspacing=0 width='100%' bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25>
						<td class='s_td'>번호</td>
						<td  class='m_td' colspan=2>클립아트 ID</td>
						<td  class='m_td'>중간이미지</td>
						<td  class='m_td'>큰이미지</td>
						<td  class='e_td'>삭제</td>
					</tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=80><td colspan=6 align=center>입력된 추가이미지가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff><td  align=center  class=small>".($i+1)."</td><td  ><img src='".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif' align=absmiddle style='border:1px solid gray'></td><td  class=small><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif')\">c_".$db->dt[id]."_add.gif</a></td><td  class=small><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[id]."_add.gif')\">m_".$db->dt[id]."_add.gif</a></td><td><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[id]."_add.gif')\">b_".$db->dt[id]."_add.gif</a></td><td align=center  class=small><a href=\"JavaScript:deleteAddimage('delete','".$db->dt[id]."','$pid')\"><img src='../image/btc_del.gif'></a></td></tr>";
			$mString = $mString."<tr height=1><td colspan=6 class='dot-x'></td></tr>";
		}
	}
	$mString = $mString."</table>";

	return $mString;
}

/*
function PrintOption($pid, $opn_ix =''){
	global $db;

	$sql = "select id, option_div,option_price, option_m_price, option_d_price, option_a_price, option_useprice, option_stock, option_safestock,option_code from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' and opn_ix = '$opn_ix' order by id asc";
	$db->query($sql);

	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";
	$mString .=  "<tr height=1><td colspan=9 ></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td rowspan=3 class=small>번호</td><td rowspan=3 class=small>옵션구분</td><td colspan=4 class=small>옵션가격</td><td colspan=2 class=small>옵션재고</td><td rowspan=3 class=small>기타(색상)</td><td rowspan=3 class=small>관리</td></tr>";
	$mString .=  "<tr height=1><td colspan=6 class='dot-x'></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=small>비회원가</td><td class=small>회원가</td><td class=small>딜러가</td><td class=small>대리점가</td><td class=small>재고</td><td class=small>안전재고</td></tr>";
	$mString .=  "<tr height=1><td colspan=9 ></td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=120><td colspan=10 align=center class=small>수정 /  추가 하시고자 하는 옵션이름을 선택해주세요</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff>
			<td  align=center>".($i+1)."</td>
			<td><a href=\"JavaScript:UpdateOption('".$db->dt[id]."','".str_replace("\"","&quot;",$db->dt[option_div])."','".$db->dt[option_price]."','".$db->dt[option_m_price]."','".$db->dt[option_d_price]."','".$db->dt[option_a_price]."','".$db->dt[option_stock]."','".$db->dt[option_safestock]."','".$db->dt[option_code]."')\" ><u>".$db->dt[option_div]."</u></a></td>
			<td>".$db->dt[option_price]."</td>
			<td>".$db->dt[option_m_price]."</td>
			<td>".$db->dt[option_d_price]."</td>
			<td>".$db->dt[option_a_price]."</td>
			<td>".$db->dt[option_stock]."</td>
			<td>".$db->dt[option_safestock]."</td>
			<td>".$db->dt[option_code]."</td>
			<td align=center>
				<a href=\"JavaScript:deleteOption('delete','".$db->dt[id]."','$pid')\"><img  src='../image/btc_del.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=9 class='dot-x'></td></tr>
			";
		}
	}
	$mString = $mString."</table>";

	return $mString;
}
*/

function PrintDisplayOption($pid){
	global $db;

	$sql = "select * from ".TBL_SHOP_PRODUCT_DISPLAYINFO." a where pid = '$pid' ";
	$db->query($sql);

	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class='s_td small' >번호</td><td class='m_td small' >추가정보명</td><td class='m_td small' >추가정보내용</td><td class='e_td small' >관리</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=9 align=center class=small >입력된 상품추가정보 항목이  없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff align=center>
			<td  class=small >".($i+1)."</td>
			<td class=small ><a href=\"JavaScript:UpdateDisplayOption('".$db->dt[dp_ix]."','".$db->dt[dp_title]."','".$db->dt[dp_desc]."')\" ><u>".$db->dt[dp_title]."</u></a></td>
			<td class=small >".$db->dt[dp_desc]."</td>
			<td align=center>
				<a href=\"JavaScript:deleteDisplayOption('delete','".$db->dt[dp_ix]."','$pid')\"><img  src='../image/si_remove.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=4 class='dot-x'></td></tr>
			";
		}
	}
	$mString = $mString."</table>";

	return $mString;
}
/*
function _PrintOption($pid){
	global $db;

	$sql = "select id, option_div,option_price, option_useprice, option_stock from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' ";
	$db->query($sql);

	$mString = "<table cellpadding=2 cellspacing=1 width=100% bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>번호</td><td>옵션이름</td><td>옵션구분</td><td>옵션가격</td><td>옵션재고</td><td>옵션표시</td><td>관리</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=40><td colspan=7 align=center>입력된 옵션이 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff>
			<td  align=center>".($i+1)."</td><td>".$db->dt[option_name]."</td><td>".$db->dt[option_div]."</td><td>".$db->dt[option_price]."</td><td>".$db->dt[option_stock]."</td>
			<td>".PrintSelect($db->dt[option_name],$db->dt[option_div],$db->dt[option_price],$db->dt[option_useprice])."</td>
			<td align=center>
				<a href=\"JavaScript:UpdateOption('".$db->dt[id]."','".$db->dt[option_name]."','".$db->dt[option_div]."','".$db->dt[option_price]."','".$db->dt[option_stock]."')\">○</a>
				<a href=\"JavaScript:deleteOption('delete','".$db->dt[id]."','$pid')\">×</a>
			</td>
			</tr>";
		}
	}
	$mString = $mString."</table>";

	return $mString;
}
*/

function PrintSelect($op_name,$op_div,$op_price,$op_useprice)
{
	$aryOp_div = explode("|",$op_div);
	$aryOp_price = explode("|",$op_price);
	$size = count($aryOp_div);

	$SelectString = "<Select>";

	if ($size == 0){
		$SelectString = $SelectString."<option>옵션이 없습니다.</option>";
	}else{
		if($op_useprice ==1){
			for($i=0; $i < $size; $i++){
				$SelectString = $SelectString."<option value='".$aryOp_div[$i]."'>".$aryOp_div[$i]."</option>";
			}
		}else{
			for($i=0; $i < $size; $i++){
				$SelectString = $SelectString."<option value='".$aryOp_price[$i]."'>".$aryOp_div[$i]."</option>";
			}
		}
	}

	$SelectString = $SelectString."</Select>";

	return $SelectString;
}


function SellState($vstate , $return_type="select",$pid='',$input_name = 'state'){	//vstate = 88 엔터프라이즈 상품등록용 2014-01-13 이학봉
	global $admininfo,$db;

	if($admininfo[admin_level] == 9){

		if($return_type == "select"){
			$mstring = "
			<Select name='".$input_name."' style='height:23px;'>
				<option value=1 ".(($vstate == '1' || $vstate == "") ? "selected":"").">판매중</option>
				<option value=0 ".($vstate == '0' ? "selected":"").">일시품절</option>
				<option value=2 ".(($vstate == '2') ? "selected":"").">판매중지</option>
				<!-- <option value=7 ".(($vstate == 7) ? "selected":"").">수정대기상품</option> -->
				";
			if($admininfo[mall_use_multishop]){
	$mstring .= "<option value=6 ".($vstate == '6' ? "selected":"").">등록신청중</option>";
			}
			$mstring .= "
				<!-- <option value=8 ".(($vstate == 8) ? "selected":"").">승인거부</option>
				<option value=9 ".(($vstate == 9) ? "selected":"").">판매금지</option> -->
				
				<option value=4 ".(($vstate == 4) ? "selected":"").">판매예정</option>				
				<option value=5 ".(($vstate == 5) ? "selected":"").">판매종료</option>
				
				";
			$mstring .= "</Select>";
		}else{
		
			if($vstate == "" ){
				/* 기존엔 상태값들 노출 엔터프라이즈 하면서 수정 2014-01-13 이학봉
				$mstring = "<input type=radio name=state id='state_1' value=1 ".(($vstate == '1' || $vstate == "") ? "checked":"")."><label for='state_1'>판매중</label>
					<input type=radio name=state id='state_0' value=0 ".($vstate == '0' ? "checked":"")."><label for='state_0'>일시품절</label>";
				if($admininfo[mall_use_multishop]){
				$mstring .= "<input type=radio name=state id='state_6'  value=6 ".($vstate == 6 ? "checked":"")."><label for='state_6'>승인대기</label>";
				*/
				$mstring .= "<input type=radio name='".$input_name."' id='state_1' value=1 ".(($vstate == '1' || $vstate == "") ? "checked":"")."><label for='state_1'>판매중</label>&nbsp;
							<input type=radio name='".$input_name."' id='state_0' value=0 ".($vstate == '0' ? "checked":"")."><label for='state_0'>일시품절</label>&nbsp;
							<input type=radio name='".$input_name."' id='state_2' value=2 ".(($vstate == '2') ? "checked":"")."><label for='state_2'>판매중지</label>&nbsp;
							<!-- <input type=radio name='".$input_name."' id='state_7' value=7 ".(($vstate == 7 ) ? "checked":"")."><label for='state_7'>수정대기상품</label>&nbsp; -->";
				if($admininfo[mall_use_multishop]){
				$mstring .= "<!-- <input type=radio name='".$input_name."' id='state_6'  value=6 ".($vstate == 6 ? "checked":"")."><label for='state_6'>승인대기</label>&nbsp; -->";
				}
				$mstring .= "<!-- <input type=radio name='".$input_name."' id='state_8' value=8 ".(($vstate == 8 ) ? "checked":"")."><label for='state_8'>승인거부</label>&nbsp;
							<input type=radio name='".$input_name."' id='state_9' value=9 ".(($vstate == 9 ) ? "checked":"")."><label for='state_9'>판매금지</label> -->
							<input type=radio name='".$input_name."' id='state_4' value=4 ".(($vstate == 4 ) ? "checked":"")."><label for='state_4'>판매예정</label>
							<input type=radio name='".$input_name."' id='state_5' value=5 ".(($vstate == 5 ) ? "checked":"")."><label for='state_5'>판매종료</label>
							
							";
			}else{
				$mstring = "<input type=radio name='".$input_name."' id='state_1' value=1 ".(($vstate == '1' || $vstate == "") ? "checked":"")."><label for='state_1'>판매중</label>&nbsp;
					<input type=radio name='".$input_name."' id='state_0' value=0 ".($vstate == '0' ? "checked":"")."><label for='state_0'>일시품절</label>&nbsp;
					<input type=radio name='".$input_name."' id='state_2' value=2 ".(($vstate == '2') ? "checked":"")."><label for='state_2'>판매중지</label>&nbsp;
					<!-- <input type=radio name='".$input_name."' id='state_7' value=7 ".(($vstate == 7 ) ? "checked":"")."><label for='state_7'>수정대기상품</label> -->";
				if($admininfo[mall_use_multishop]){
				$mstring .= "<!-- <input type=radio name='".$input_name."' id='state_6'  value=6 ".($vstate == 6 ? "checked":"")."><label for='state_6'>승인대기</label>&nbsp; -->";
				}
				$mstring .= "
					<!-- <input type=radio name='".$input_name."' id='state_8' value=8 ".(($vstate == 8 ) ? "checked":"")."><label for='state_8'>승인거부</label>&nbsp;
					<input type=radio name='".$input_name."' id='state_9' value=9 ".(($vstate == 9 ) ? "checked":"")."><label for='state_9'>판매금지</label>&nbsp; -->
					<input type=radio name='".$input_name."' id='state_4' value=4 ".(($vstate == 4 ) ? "checked":"")."><label for='state_4'>판매예정</label>&nbsp;
					<input type=radio name='".$input_name."' id='state_5' value=5 ".(($vstate == 5 ) ? "checked":"")."><label for='state_5'>판매종료</label>&nbsp;
					";
			}
		}
	}else if($admininfo[admin_level] == 8){
		if($return_type == "select"){
			if($vstate == "" || $vstate == "6"){
				if($admininfo[mall_use_multishop]){
					$mstring = "
					<Select name='".$input_name."'>
						<option value=6 selected >등록신청중</option>
					</Select>";
				}else{
					$mstring = "
					<Select name='".$input_name."'>
						<option value=0 ".($vstate == '0' ? "selected":"").">일시품절</option>";
					if ($vstate == 1 ){
					$mstring .= "<option value=1 ".($vstate == 1 ||$vstate == "" ? "selected":"").">판매중</option>";
					}
					$mstring .= "
						<option value=2 ".($vstate == '2' ? "selected":"").">판매중지</option>
					</Select>";
				}
			}else{
				$mstring = "
					<Select name='".$input_name."'>
						<option value=0 ".($vstate == '0' ? "selected":"").">일시품절</option>";
					if ($vstate == 1 ){
					$mstring .= "<option value=1 ".($vstate == 1 ? "selected":"").">판매중</option>";
					}
					if($admininfo[mall_use_multishop]){
						$mstring .= "<option value=6 ".($vstate == 6 ? "selected":"").">등록신청중</option>";
					}
					$mstring .= "
					<option value=1 ".($vstate == 1 ? "selected":"").">판매중</option>
					</Select>";
			}
		}else{
			if($vstate == "" || $vstate == "6"){
				if($admininfo[mall_use_multishop] && $input_name != 'c_state'){
					$mstring = "<input type=radio name='".$input_name."' id='state_6' value=6 checked ><label for='state_6'>승인대기</label>";
				}else{
					$mstring = "<!--<input type=radio name='".$input_name."' id='state_1' value=1 ".(($vstate == 1 || $vstate == "") ? "checked":"")."><label for='state_1'>판매중</label>-->
					<input type=radio name='".$input_name."' id='state_6' value=6 ".($vstate == '6' ? "checked":"")."><label for='state_6'>승인대기</label>	
					<input type=radio name='".$input_name."' id='state_0' value=0 ".($vstate == '0' ? "checked":"")."><label for='state_0'>일시품절</label>				
					<input type=radio name='".$input_name."' id='state_2' value=2 ".(($vstate == 2) ? "checked":"")."><label for='state_2'>판매중지</label>";
				}
			}else{
				$mstring = "<input type=radio name='".$input_name."' id='state_1' value=1 ".(($vstate == 1 || $vstate == "") ? "checked":"")."><label for='state_1'>판매중</label>
					<input type=radio name='".$input_name."' id='state_0' value=0 ".($vstate == '0' ? "checked":"")."><label for='state_0'>일시품절</label>				
					<input type=radio name='".$input_name."' id='state_2' value=2 ".(($vstate == 2) ? "checked":"")."><label for='state_2'>판매중지</label>";
				if($admininfo[mall_use_multishop]){
					$mstring .= "<input type=radio name='".$input_name."' id='state_6'  value=6 ".($vstate == 6 ? "checked":"")."><label for='state_6'>승인대기</label>";
				}
				$mstring .= "
					<input type=radio name='".$input_name."' id='state_8' value=8 ".(($vstate == 8 ) ? "checked":"")."><label for='state_8'>승인거부</label>
					<input type=radio name='".$input_name."' id='state_9' value=9 ".(($vstate == 9 ) ? "checked":"")."><label for='state_9'>판매금지</label>";

				$mstring .= "<script language='JavaScript'>
						$(document).ready(function (){
							var check_insert = '".$pid."';
							var state = '".$vstate."';
							
							$('input[name=state]').attr('disabled','disabled');

							if(check_insert != ''){
								if(state != '6' && state != '8' && state != '9'){
									$('#state_1').attr('disabled',false);
									$('#state_0').attr('disabled',false);
									$('#state_2').attr('disabled',false);
								}else if(state == '8'){
									$('#state_6').attr('disabled',false);
									$('#state_8').attr('disabled',false);
								}
							}else{
								$('#state_6').attr('disabled',false);
							}
						});
						</script>";
			}
		}
	}

	if($return_type != "select"){

		if($pid){
			//판매상태 변경 히스토리 값은 동일상태값 최신등록일 기준으로 데이타 가져옵니다. 2014-02-14 이학봉
			if($vstate !='0'){
			$sql = "select
						p.input_date,
						p.is_auto_change,
						p.auto_change_state,
						psh.*
					from
						shop_product as p 
						inner join shop_product_state_history as psh on (p.id = psh.pid)
					where
						p.id = '".$pid."'
						and psh.state = '".$vstate."'
						order by psh.regdate DESC";
			}else{
			$sql = "select
						p.input_date,
						p.is_auto_change,
						p.auto_change_state
					from
						shop_product as p 
					where
						p.id = '".$pid."'";
			}

			$db->query($sql);
			$db->fetch();
			
			if($db->total > 0){
				$input_date = explode(" ",$db->dt[input_date]);
				$input_stime = explode(":",$input_date[1]);
				$is_auto_change = $db->dt[is_auto_change];
				
				$auto_change_state = explode(" ",$db->dt[auto_change_state]);
				$auto_change_stime = explode(":",$auto_change_state[1]);

				$state_div = $db->dt[state_div];
				$state_msg = $db->dt[state_msg];
			
			}
		}

		$mstring .= "
					<div id='state_div_0' style='display:none;padding:10px;width:100%;'>
					입고예정일 <input type='text' class='textbox' value='".$input_date[0]."' name='input_date' id='input_date_datepicker' style='width:60px;text-align:center;'>
					 일 
					<SELECT name='input_stime'>";
					for($i=0;$i < 24;$i++){
		$mstring .= "<option value='".$i."' ".($input_stime[0] == $i ? "selected":"").">".$i."</option>";
						}
		$mstring .= "
					</SELECT> 시
					<SELECT name='input_smin'>";
					for($i=0;$i < 60;$i++){
		$mstring .= "<option value='".$i."' ".($input_stime[1] == $i ? "selected":"").">".$i."</option>";
						}
		$mstring .= "
					</SELECT> 분
					<input type='checkbox' class='textbox' name='is_auto_change' id='auto_change_check' value='1' ".($is_auto_change == '1'?'checked':'').">
					자동 판매중 상태 전환 

					<input type='text' class='textbox' value='".$auto_change_state[0]."' name='auto_change_state' id='auto_change_state_datepicker' style='width:60px;text-align:center;' disabled>
					 일 
					<SELECT name='auto_change_stime' disabled>";
					for($i=0;$i < 24;$i++){
		$mstring .= "<option value='".$i."' ".($auto_change_stime[0] == $i ? "selected":"").">".$i."</option>";
						}
		$mstring .= "
					</SELECT> 시
					<SELECT name='auto_change_smin' disabled>";
					for($i=0;$i < 60;$i++){
		$mstring .= "<option value='".$i."' ".($auto_change_stime[1] == $i ? "selected":"").">".$i."</option>";
						}
		$mstring .= "
					</SELECT> 분
					</div>

					<div id='state_div_2' style='display:none;padding:10px;width:100%;'>
						<div>
						<select name='state_div' id='select_state_div_2'>
							<option value='1' ".($state_div == '1' ? "selected":"").">상품품절</option>
							<option value='2' ".($state_div == '2' ? "selected":"").">재고부족</option>
							<option value='3' ".($state_div == '3' ? "selected":"").">등록오류</option>
							<option value='10' ".($state_div == '10' ? "selected":"").">기타</option>
						</select>
						</div>
						<div>
						<input type='text' class='textbox' value='".$state_msg."' name='state_msg' id='state_msg_2' style='width:30%'>
						</div>
					</div>

					<div id='state_div_8' style='display:none;padding:10px;width:100%;'>
						<div>
						<select name='state_div' id='select_state_div_8'>
							<option value='4' ".($state_div == '4' ? "selected":"").">판매불가상품</option>
							<option value='5' ".($state_div == '5' ? "selected":"").">정보미흡</option>
							<option value='6' ".($state_div == '6' ? "selected":"").">이미지미흡/다름</option>
							<option value='7' ".($state_div == '7' ? "selected":"").">거래상품아님</option>
							<option value='10' ".($state_div == '10' ? "selected":"").">기타</option>
						</select>
						</div>
						<div>
						<input type='text' class='textbox' value='".$state_msg."' name='state_msg'  id='state_msg_8' style='width:30%'>
						</div>
					</div>

					<div id='state_div_9' style='display:none;padding:10px;width:100%;'>
						<div>
						<select name='state_div' id='select_state_div_9'>
							<option value='8' ".($state_div == '8' ? "selected":"").">저작권위배요청상품</option>
							<option value='9' ".($state_div == '9' ? "selected":"").">이미테이션</option>
							<option value='6' ".($state_div == '6' ? "selected":"").">이미지미흡/다름</option>
							<option value='7' ".($state_div == '7' ? "selected":"").">거래상품아님</option>
							<option value='10' ".($state_div == '10' ? "selected":"").">기타</option>
						</select>
						</div>
						<div>
						<input type='text' class='textbox' value='".$state_msg."' name='state_msg' id='state_msg_9' style='width:30%'>
						</div>
					</div>

					<div id='state_div_7' style='display:none;padding:10px;width:100%;'>
						<div>
						<select name='state_div' id='select_state_div_7'>
							<option value='5' ".($state_div == '5' ? "selected":"").">정보미흡</option>
							<option value='6' ".($state_div == '6' ? "selected":"").">이미지미흡/다름</option>
							<option value='7' ".($state_div == '7' ? "selected":"").">거래상품아님</option>
							<option value='10' ".($state_div == '10' ? "selected":"").">기타</option>
						</select>
						</div>
						<div>
						<input type='text' class='textbox' value='".$state_msg."' name='state_msg' id='state_msg_7' style='width:30%'>
						</div>
					</div>
					<!--script src='/admin/js/jquery-1.8.3.js'></script>
					<script src='/admin/js/jquery-ui.js'></script-->
					<SCRIPT type='text/javascript'>
				
						$(document).ready(function(){
							
							var state = ".($vstate == ''?'1':$vstate).";
							display_state(state);
							$('input[name=".$input_name."]').click(function(){
								var value = $(this).val();
								display_state(value);
							});

							$('#input_date_datepicker').datepicker({
							//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
							dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
							//showMonthAfterYear:true,
							changeMonth: true,
							minDate: new Date(".date('Y').", ".date('m-1').", ".date('d')."),
							dateFormat: 'yy-mm-dd',
							buttonImageOnly: true,
							buttonText: '달력',

							});

							$('#auto_change_state_datepicker').datepicker({
							//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
							dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
							//showMonthAfterYear:true,
							changeMonth: true,
							minDate: new Date(".date('Y').", ".date('m-1').", ".date('d')."),
							dateFormat: 'yy-mm-dd',
							buttonImageOnly: true,
							buttonText: '달력'

							});

							auto_change_date(".($is_auto_change == '1'?'1':'0').");

							$('input[name=is_auto_change]').click(function(){
								if($(this).attr('checked') == 'checked'){
									auto_change_date('1');
								}else{
									auto_change_date('0');
								}
								
							});
							
						});

						function display_state(state){
							var state = state;
							$('div[id^=state_div_]').css('display','none');
							$('#state_div_'+state).css('display','');
							
							if(state !='0'){
								$('input[name=input_date]').attr('disabled',true);
								$('select[name=input_stime]').attr('disabled',true);
								$('select[name=input_smin]').attr('disabled',true);
								$('input[name=is_auto_change]').attr('disabled',true);
								//$('input[name=auto_change_state]').attr('disabled',true);
								//$('select[name=auto_change_stime]').attr('disabled',true);
								//$('select[name=auto_change_smin]').attr('disabled',true);
								
								$('select[name^=state_div]').attr('disabled',true);
								$('input[name^=state_msg]').attr('disabled',true);
								
								$('#select_state_div_'+state).attr('disabled',false);
								$('#state_msg_'+state).attr('disabled',false);
							}else{
								$('select[name^=state_div]').attr('disabled',true);
								$('input[name^=state_msg]').attr('disabled',true);

								$('input[name=input_date]').attr('disabled',false);
								$('select[name=input_stime]').attr('disabled',false);
								$('select[name=input_smin]').attr('disabled',false);
								$('input[name=is_auto_change]').attr('disabled',false);
								//$('input[name=auto_change_state]').attr('disabled',false);
								//$('select[name=auto_change_stime]').attr('disabled',false);
								//$('select[name=auto_change_smin]').attr('disabled',false);
							}
						}

						function auto_change_date(is_auto_change){
							
							var is_auto_change = is_auto_change;
							if(is_auto_change == '1'){
								$('input[name=auto_change_state]').attr('disabled',false);
								$('select[name=auto_change_stime]').attr('disabled',false);
								$('select[name=auto_change_smin]').attr('disabled',false);
								
							}else{
								$('input[name=auto_change_state]').attr('disabled',true);
								$('select[name=auto_change_stime]').attr('disabled',true);
								$('select[name=auto_change_smin]').attr('disabled',true);
								
							}
						}

				
					</SCRIPT>";
	}

	return $mstring;
}


function displayProduct($disp){

	global $admininfo;
	if($disp == 1 || $disp == ""){
		$Selectedstr00 = "";
		$Selectedstr01 = " selected";
	}else{
		$Selectedstr00 = " selected";
		$Selectedstr01 = "";
	}
	
	$data = "<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>";

	if($admininfo['admin_level'] == '9'){
		$data .="
		<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
		<!--input type='radio' name='disp'  id='disp_0' value='2' ".ReturnStringAfterCompare($disp, "2", " checked")."><label for='disp_2'>포인트몰</label>
		<input type='radio' name='disp'  id='disp_0' value='3' ".ReturnStringAfterCompare($disp, "3", " checked")."><label for='disp_3'>현금,포인트몰</label>
		<input type='radio' name='disp'  id='disp_0' value='9' ".ReturnStringAfterCompare($disp, "9", " checked")."><label for='disp_9'>공동구매</label-->

		<!--Select name=state>
			<option value=0 $Selectedstr00>일시품절</option>
			<option value=1 $Selectedstr01>판매중</option>
		</Select-->";
	}
	return $data;

}


function relationSetOption($pid, $group_code, $disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid;

	/* <ul id="productList_1" name="productList" class="productList"></ul>을 return 못해서 상품등록시 스트립트 작동안됨
	if(!$pid){
		return "";
	}
	*/

	$max = 105;
	//$group_code = 1;
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$db->query("SELECT  p.id,p.pname, p.sellprice,  p.reserve, rp_ix FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid' and p.disp = 1   ");
	$total = $db->total;

	$db->query("SELECT  p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, rp_ix  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid'  and p.disp = 1 order by rp.vieworder asc limit $start,$max");

	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productSetList_'.$group_code.'" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){

            $addQaDir = "";
            if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                $addQaDir = "/QA";
            }

			$mString = '<ul id="productSetList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script>'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.''.";\n";
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				$mString .= 'ms_productSearch._setProduct("productSetList_'.$group_code.'", "M", "'.$db->dt['id'].'", "'.PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt['id'], "slist").'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'");'."\n";

			}
			$mString .= '</script>'."\n";
		}
	}

	return $mString;

}



function relationProductList($pid, $disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid;

	/* <ul id="productList_1" name="productList" class="productList"></ul>을 return 못해서 상품등록시 스트립트 작동안됨
	if(!$pid){
		return "";
	}
	*/

	$max = 105;
	$group_code = 1;
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$db->query("SELECT  p.id FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid'  ");//and p.disp = 1
	$total = $db->total;

	$db->query("SELECT  p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, rp_ix, p.disp, p.state  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid'  order by rp.vieworder asc limit $start,$max");//and p.disp = 1

	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_1" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){

            $addQaDir = "";
            if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                $addQaDir = "/QA";
            }

            $mString = '<ul id="productList_1" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script>'."\n";
			$mString .= 'ms_productSearch.groupCode = 1'.";\n";
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				//function(obj, mode, pID, imgSrc, pName, bName, pPrice, listprice, reserve,coprice,wholesale_price,wholesale_sellprice, disp, state, dcprice, vieworder, viewcnt, regdate)
				$mString .= 'ms_productSearch._setProduct("productList_1", "M", "'.$db->dt['id'].'", "'.PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt['id'], "slist").'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'","", "", "", "", "", "'.$db->dt[disp].'", "'.$db->dt[state].'");'."\n";

			}
			$mString .= '</script>'."\n";
		}
	}


	return $mString;

}

function relationProductList2($pid, $disp_type=""){

    global $start,$page, $orderby, $admin_config, $erpid;

    /* <ul id="productList_1" name="productList" class="productList"></ul>을 return 못해서 상품등록시 스트립트 작동안됨
    if(!$pid){
        return "";
    }
    */

    $max = 105;
    $group_code = 1;
    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $db = new Database;

    $db->query("SELECT  p.id FROM ".TBL_SHOP_PRODUCT." p, shop_relation_product2 rp where p.id = rp.rp_pid and rp.pid = '$pid'  ");//and p.disp = 1
    $total = $db->total;

    $db->query("SELECT  p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, rp_ix, p.disp, p.state  FROM ".TBL_SHOP_PRODUCT." p, shop_relation_product2 rp where p.id = rp.rp_pid and rp.pid = '$pid'  order by rp.vieworder asc limit $start,$max");//and p.disp = 1

    if ($db->total == 0){
        if($disp_type == "clipart"){
            $mString = '<ul id="productList_2" name="productList" class="productList"></ul>';
        }
    }else{
        $i=0;
        if($disp_type == "clipart"){

            $addQaDir = "";
            if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                $addQaDir = "/QA";
            }

            $mString = '<ul id="productList_2" name="productList" class="productList"></ul>'."\n";
            $mString .= '<script>'."\n";
            $mString .= 'ms_productSearch.groupCode = 2'.";\n";
            for($i=0;$i<$db->total;$i++){
                $db->fetch($i);
                //function(obj, mode, pID, imgSrc, pName, bName, pPrice, listprice, reserve,coprice,wholesale_price,wholesale_sellprice, disp, state, dcprice, vieworder, viewcnt, regdate)
                $mString .= 'ms_productSearch._setProduct("productList_2", "M", "'.$db->dt['id'].'", "'.PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt['id'], "slist").'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'","", "", "", "", "", "'.$db->dt[disp].'", "'.$db->dt[state].'");'."\n";

            }
            $mString .= '</script>'."\n";
        }
    }


    return $mString;

}

function addProductList($pid, $disp_type=""){

    global $start,$page, $orderby, $admin_config, $erpid;

    /* <ul id="productList_1" name="productList" class="productList"></ul>을 return 못해서 상품등록시 스트립트 작동안됨
    if(!$pid){
        return "";
    }
    */

    $max = 105;
    $group_code = 1;
    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $db = new Database;

    $db->query("SELECT  p.id FROM ".TBL_SHOP_PRODUCT." p, shop_relation_add_product rp where p.id = rp.rp_pid and rp.pid = '$pid'  ");//and p.disp = 1
    $total = $db->total;

    $db->query("SELECT  p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, rp_ix, p.disp, p.state  FROM ".TBL_SHOP_PRODUCT." p, shop_relation_add_product rp where p.id = rp.rp_pid and rp.pid = '$pid'  order by rp.vieworder asc limit $start,$max");//and p.disp = 1

    if ($db->total == 0){
        if($disp_type == "clipart"){
            $mString = '<ul id="productList_3" name="productList" class="productList"></ul>';
        }
    }else{
        $i=0;
        if($disp_type == "clipart"){

            $addQaDir = "";
            if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                $addQaDir = "/QA";
            }

            $mString = '<ul id="productList_3" name="productList" class="productList"></ul>'."\n";
            $mString .= '<script>'."\n";
            $mString .= 'ms_productSearch.groupCode = 3'.";\n";
            for($i=0;$i<$db->total;$i++){
                $db->fetch($i);
                //function(obj, mode, pID, imgSrc, pName, bName, pPrice, listprice, reserve,coprice,wholesale_price,wholesale_sellprice, disp, state, dcprice, vieworder, viewcnt, regdate)
                $mString .= 'ms_productSearch._setProduct("productList_3", "M", "'.$db->dt['id'].'", "'.PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt['id'], "slist").'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'","", "", "", "", "", "'.$db->dt[disp].'", "'.$db->dt[state].'");'."\n";

            }
            $mString .= '</script>'."\n";
        }
    }


    return $mString;

}

function addProductList1($pid, $disp_type=""){

    global $start,$page, $orderby, $admin_config, $erpid;

    /* <ul id="productList_1" name="productList" class="productList"></ul>을 return 못해서 상품등록시 스트립트 작동안됨
    if(!$pid){
        return "";
    }
    */

    $max = 105;
    $group_code = 1;
    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $db = new Database;

    $db->query("SELECT  p.id FROM ".TBL_SHOP_PRODUCT." p, shop_relation_product3 rp where p.id = rp.rp_pid and rp.pid = '$pid'  ");//and p.disp = 1
    $total = $db->total;

    $db->query("SELECT  p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, rp_ix, p.disp, p.state  FROM ".TBL_SHOP_PRODUCT." p, shop_relation_product3 rp where p.id = rp.rp_pid and rp.pid = '$pid'  order by rp.vieworder asc limit $start,$max");//and p.disp = 1

    if ($db->total == 0){
        if($disp_type == "clipart"){
            $mString = '<ul id="productList_4" name="productList" class="productList"></ul>';
        }
    }else{
        $i=0;
        if($disp_type == "clipart"){

            $addQaDir = "";
            if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                $addQaDir = "/QA";
            }

            $mString = '<ul id="productList_4" name="productList" class="productList"></ul>'."\n";
            $mString .= '<script>'."\n";
            $mString .= 'ms_productSearch.groupCode = 4'.";\n";
            for($i=0;$i<$db->total;$i++){
                $db->fetch($i);
                //function(obj, mode, pID, imgSrc, pName, bName, pPrice, listprice, reserve,coprice,wholesale_price,wholesale_sellprice, disp, state, dcprice, vieworder, viewcnt, regdate)
                $mString .= 'ms_productSearch._setProduct("productList_4", "M", "'.$db->dt['id'].'", "'.PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt['id'], "slist").'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'","", "", "", "", "", "'.$db->dt[disp].'", "'.$db->dt[state].'");'."\n";

            }
            $mString .= '</script>'."\n";
        }
    }


    return $mString;

}

function relationSetProductList($pid, $disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid;

	/* <ul id="productList_1" name="productList" class="productList"></ul>을 return 못해서 상품등록시 스트립트 작동안됨
	if(!$pid){
		return "";
	}
	*/

	$max = 105;
	$group_code = 1;
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$db->query("SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, psr_ix FROM ".TBL_SHOP_PRODUCT." p, shop_product_set_relation rp where p.id = rp.set_pid and rp.pid = '$pid' and p.disp = 1   ");
	$total = $db->total;

	$db->query("SELECT distinct p.id, p.pcode, p.pname, p.sellprice,  p.reserve, psr_ix , rp.vieworder  FROM ".TBL_SHOP_PRODUCT." p, shop_product_set_relation rp where p.id = rp.set_pid and rp.pid = '$pid'  and p.disp = 1 order by rp.vieworder asc limit $start,$max");

	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_2" name="productList" class="productList"></ul>';
			$mString .= ' ';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){

            $addQaDir = "";
            if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                $addQaDir = "/QA";
            }

            $mString = '<ul id="productList_2" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script>'."\n";
			$mString .= 'ms_productSearch.groupCode = 2'.";\n";
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				$mString .= 'ms_productSearch._setProduct("productList_2", "M", "'.$db->dt['id'].'", "'.PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt['id'], "slist").'", "'.str_replace("\"","&quot;",trim($db->dt['pname'])).'", "'.str_replace("\"","&quot;",trim($db->dt['brand_name'])).'", "'.$db->dt['sellprice'].'");'."\n";

			}
			$mString .= '</script>'."\n";
		}
	}


	return $mString;

}


function printBuyingCompany($bc_ix, $cid, $return_type ="")
{
//global $db;

	$mdb = new Database;

	if($cid){
		$mdb->query("SELECT * FROM shop_buying_company where disp=1 and cid = '$cid'");
	}else{
		$mdb->query("SELECT * FROM shop_buying_company where disp=1 ");
	}

	$bl = "<Select name='buying_company' style='height:23px;'>";
	if ($mdb->total == 0)	{
		$bl = $bl."<Option>등록된 사입업체가 없습니다.</Option>";
	}else{
		if($return_type == ""){
			$bl = $bl."<Option value=''>사입업체 선택</Option>";
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($bc_ix == $mdb->dt[bc_ix]){
					$strSelected = "Selected";
				}else{
					$strSelected = "";
				}

				$bl = $bl."<Option value='".$mdb->dt[bc_ix]."' $strSelected>".$mdb->dt[bc_name]."</Option>";

			}
		}else{
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($brand == $mdb->dt[bc_ix]){
					return $mdb->dt[bc_name];
				}
			}
		}
	}

	$bl = $bl."</Select>";

	return $bl;
}




function PrintOptionName($pid, $select_opn_ix)
{
	$mdb = new Database;
	$mdb->query("SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS." where pid ='$pid'");

	$SelectString = "<Select name='opn_ix' onchange=\"ChangeOptionName('$pid', this);\">";

	if ($mdb->total){
			$SelectString = $SelectString."<option value=''>옵션이름 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			$SelectString = $SelectString."<option value='".$mdb->dt[opn_ix]."' option_kind='".$mdb->dt[option_kind]."'>".$mdb->dt[option_name]."</option>";
		}
	}else{
	$SelectString = $SelectString."<option value=''> 옵션이 없습니다.</option>";
	}

	$SelectString = $SelectString."</Select>";

	return $SelectString;
}


function getOptionTmp($option_i)
{
	global $admininfo,$goods_options_tmp_type;

	$whereadd ="and ".$goods_options_tmp_type." = '".$admininfo[$goods_options_tmp_type]."' ";

	$mdb = new Database;
	$mdb->query("SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." where disp ='1' $whereadd");

	$SelectString = "<Select name='opnt_ix' id='favorite_option_".$option_i."' idx=\"".$option_i."\" style='margin-left:4px;'>";//onchange=\"SetOptionTmp($(this),'".$option_i."');\"

	if ($mdb->total){
			$SelectString = $SelectString."<option value=''>자주쓰는  옵션선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			$SelectString = $SelectString."<option value='".$mdb->dt[opnt_ix]."' option_kind='".$mdb->dt[option_kind]."' option_use='".$mdb->dt[option_use]."'>".$mdb->dt[option_name]."</option>";
		}
	}else{
	$SelectString = $SelectString."<option value=''> 자주쓰는  옵션이  없습니다.</option>";
	}

	$SelectString = $SelectString."</Select>";

	return $SelectString;
}

function getOptionTmpTitle(){
	global $admininfo, $layout_config, $goods_options_tmp_type;

//	if($_SESSION["layout_config"]["mall_use_inventory"] != "Y"){
		$whereadd ="and ".$goods_options_tmp_type." = '".$admininfo[$goods_options_tmp_type]."' ";

		$mdb = new Database;
		$mdb->query("SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS_TMP." where disp ='1' $whereadd ");
		$option_tmp_infos = $mdb->fetchall();
		//echo count($option_tmp_infos);
		//if (count($option_tmp_infos) > 0){
			//exit;
			$SelectString .= "<div style='width:100%;' id='favorites_options_area'>";
			for($i=0; $i < count($option_tmp_infos); $i++){
				//$mdb->fetch($i);
				//print_r($option_tmp_infos[$i]);
				$SelectString .= "<div id='opnt_ix_".$option_tmp_infos[$i][opnt_ix]."' opnt_ix='".$option_tmp_infos[$i][opnt_ix]."' option_type='".$option_tmp_infos[$i][option_type]."' class='make_option' option_selected='0' style='float:left;width:90px;border:1px solid silver;background-color:#efefef;padding:5px;margin:3px 3px 3px 0px;cursor:pointer;text-align:center;' onclick=\"selectTmpOption('".$option_tmp_infos[$i][opnt_ix]."')\"> ".$option_tmp_infos[$i][option_name]."</div>";
			}
			$SelectString .= "<div style='float:left;' class='make_option_btn' ><a href='./goods_options_tmp.php' target=_blank><img src='../images/".$admininfo["language"]."/btn_favorite_option_manage.gif' id='btn_favorite_option_use'   style='cursor:pointer;margin:4px 0px 0px 4px;' ></a></div>  ";
			$SelectString .= "</div>  ";
			//$SelectString .= "<div style='width:100%;border:1px solid blue;'>";
			for($i=0; $i < count($option_tmp_infos) ; $i++){
				//$mdb->fetch($i);
				$SelectString .= "<div id='opnt_ix_".$option_tmp_infos[$i][opnt_ix]."_box' style='clear:both;width:100%;border:0px solid blue;display:none;'>";
				$SelectString .= "<div id='opnt_first_area_".$option_tmp_infos[$i][opnt_ix]."' title='".$option_tmp_infos[$i][opnt_ix]."' style='float:left;width:90px;border:1px solid silver;background-color:#efefef;padding:5px;margin:3px 3px 3px 0px;'>".$option_tmp_infos[$i][option_name]."  <span style='width:30px;'>&nbsp;</span>
				
				<a href=\"javascript:selectTmpOptionDetailAll('".$option_tmp_infos[$i][opnt_ix]."');\" ><b style='font-size:12px;'>ALL</b></a>&nbsp; <a href=\"javascript:tmpOptionDetailAdd('".$option_tmp_infos[$i][opnt_ix]."');\"><b style='font-size:12px;'>+</b></a>
				
				</div>";

				$sql = "select * from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." where opnt_ix = '".$option_tmp_infos[$i][opnt_ix]."' order by opndt_ix asc ";
			//echo $sql;
				$mdb->query($sql);
				$option_detail_tmp_infos = $mdb->fetchall();

				for($j=0; $j < count($option_detail_tmp_infos) ; $j++){
					$SelectString .= "<div id='opndt_ix_".$option_detail_tmp_infos[$j][opndt_ix]."' opnt_ix='".$option_tmp_infos[$i][opnt_ix]."'  opndt_ix='".$option_detail_tmp_infos[$j][opndt_ix]."' class='make_option_detail_".$option_tmp_infos[$i][opnt_ix]."' option_detail_selected='0' style='float:left;width:90px;border:1px solid silver;background-color:#ffffff;padding:5px;margin:3px;cursor:pointer;' onclick=\"selectTmpOptionDetail('".$option_detail_tmp_infos[$j][opndt_ix]."')\">".$option_detail_tmp_infos[$j][option_div]."</div>";
				}
				//$SelectString .= "<br><br><br> ";
				$SelectString .= "</div>";

			}
			$SelectString .= "<div class='auto_option_create' style='clear:both;width:90px;border:1px solid silver;background-color:#efefef;padding:5px;margin:10px 3px 3px 0px;cursor:pointer;;text-align:center;' onclick='MakeStockOption()'>옵션자동생성</div>"; //MakeOption

		//}
//	}
/*
	$SelectString .= "<div class='stockinfo_loade' style='".(($_SESSION["layout_config"]["mall_use_inventory"] == "Y" && $_SESSION["admininfo"]["admin_level"] == 9) ? "display:block;":"display:none;")."float:left;margin:3px 3px 3px 0px;cursor:pointer;text-align:center;' onclick=\"PoPWindow('../inventory/inventory_search.php',950,480,'inventory_search')\"><img src='../images/korea/stock.gif' alt='재고정보불러오기' title='재고정보불러오기' /></div>";
*/
	return $SelectString;
}

function PrintCategoryRelation($group_code,$pid){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth, r.rc_ix, r.regdate  from shop_relation_category r, ".TBL_SHOP_CATEGORY_INFO." c where  c.cid = r.cid and pid='".$pid."'";

	//echo $sql."<br><br>";
	$db->query($sql);

	if ($db->total == 0){
		$mString .= "<table cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."' >
						<col width=5>
						<col width=*>
						<col width=100>
					</table>";
	}else{
		$i=0;
		$mString = "<table width=100% border=0 cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."'>";
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='category[".$group_code."][]' id='_category' value='".$db->dt[cid]."' style='display:none'><input type='text' name='depth[".$group_code."][]' id='_depth' value='".$db->dt[depth]."' style='display:none'></td>
				<!--td class='table_td_white small' width='50'><input type='radio' name='basic[".$group_code."]' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."></td-->
				<td class='table_td_white small ' width='*'>".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</td>
				<td class='table_td_white' width='100' align=right><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(".$group_code.",this.parentNode.parentNode)' style='cursor:pointer;' /></td>
				</tr>";
		}
		$mString .= "</table>";
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";

	return $mString;
}

function select_delivery_template($admin,$product_sell_type='R',$delivery_div='1',$pid='', $checked=''){

global $db ,$admininfo;
if($pid != ""){
	$sql = "select * from shop_product_delivery where pid = '".$pid."' and is_wholesale = '".$product_sell_type."' and delivery_div = '".$delivery_div."' and company_id = '".$admin."'";
	$db->query($sql);
	$db->fetch();
	$dt_ix_retail = $db->dt[dt_ix];
}

$Contents .="
			<input type='checkbox' name='dt_ix[".$product_sell_type."][".$delivery_div."][template_check]' id='template_check_".$product_sell_type."_".$delivery_div."' value='1' style='display:none;' $checked>
			<select name='dt_ix[".$product_sell_type."][".$delivery_div."][dt_ix]' id='dt_ix_".$product_sell_type."_".$delivery_div."' validation='true' title='배송정책'>";
			
			$sql = "select
					* 
					from 
						shop_delivery_template
					where
						company_id = '".$admin."' 
						and product_sell_type = '".$product_sell_type."'
						and delivery_div = '".$delivery_div."'
						order by dt_ix ASC";

			$db->query($sql);
			$template_array = $db->fetchall();
			
			if(count($template_array) > 0){
				$Contents .="<option value=''>배송정책을 선택해주세요</option>";
				for($jj=0;$jj<count($template_array);$jj++){
					$template_text = get_delivery_policy_text($template_array,$jj);

		$Contents .="<option value='".$template_array[$jj][dt_ix]."' ".($template_array[$jj][dt_ix] == $dt_ix_retail ?'selected':'').">".$template_text."</option>";

				}
			}else{
				$Contents .="<option value=''>해당 배송정책이 없습니다.</option>";
			}

$Contents .="</select>";


$Contents .="
<Script language='javascript'>

	$(document).ready(function (){
		
		var check = $('#template_check_".$product_sell_type."_".$delivery_div."').attr('checked');

		if(check == 'checked'){
			//$('#dt_ix_".$product_sell_type."_".$delivery_div."').attr('validation','true');
		}else{
			//$('#dt_ix_".$product_sell_type."_".$delivery_div."').attr('validation','false');
		}

		$('#template_check_".$product_sell_type."_".$delivery_div."').click(function (){
			
			var check = $(this).attr('checked');
			//alert(check);
			if(check == 'checked'){
				//$('#dt_ix_".$product_sell_type."_".$delivery_div."').attr('validation','true');
			}else{
				//$('#dt_ix_".$product_sell_type."_".$delivery_div."').attr('validation','false');
			}
		});

	});


</Script>
";

return $Contents;

}



function displayCategoryAddInfomation($cid="", $depth="1",  $datas=""){
	global $db;
	
	if($cid){
		//echo "<pre>";
		//$db->debug = true;
		$sql = "select * from shop_category_addfield where cid LIKE '".substr($cid,0,(5-$depth)*3)."%' ";
		//echo $sql;
		$db->query($sql); //cid LIKE '".substr($cid,0,($depth)*3)."%
		if($db->total){
			$add_fields = $db->fetchall();
		

			$mstring = "<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=40>
						<td style='background-color:#efefef;'>
							 <table cellpadding=0 width=100%><tr><td style='padding:10px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 카테고리별 추가정보 설정 </b><span class=small> </span></td><td align=right style='padding-right:20px;' class=small></td></tr></table> 
						</td>
					</tr>
					</table><br>";

			
			$mstring .= "<table cellpadding=0 cellspacing=0  border=0 width='100%' class='input_table_box'>
						<col width=15%>
						<col width=90%>"; 
			if(is_array($add_fields)){
				for($i=0;$i < count($add_fields);$i++){
					$field_values = explode("|",$add_fields[$i][f_value]);
					if($add_fields[$i][f_type] == "checkbox"){
						$_mstring = "";
						foreach($field_values as $key => $val){
						//	print_r($datas[$add_fields[$i][f_ename]]);
							$checked_bool = false;
							if(is_array($datas)){
								if(is_array($datas[$add_fields[$i][f_ename]])){
									if(is_array($datas[$add_fields[$i][f_ename]][value])){
										if(in_array($val, $datas[$add_fields[$i][f_ename]][value])){
											$checked_bool = true;
										}else{
											$checked_bool = false;
										}
									}
								}
							}
							$_mstring .= "<div style='padding-right:10px;float:left;'>
												<input type=hidden name='category_add_infomations[".$add_fields[$i][f_ename]."][title]'  value='".$add_fields[$i][f_name]."' >
												<input type=checkbox id='".$add_fields[$i][f_ename]."_".$key."' name='category_add_infomations[".$add_fields[$i][f_ename]."][value][]'  value='".$val."' ".($checked_bool ? "checked":"")."><label for='".$add_fields[$i][f_ename]."_".$key."'>".$val."</label>
												</div>";
							//
						}
					}else if($add_fields[$i][f_type] == "text"){
						//print_r($add_fields);
						if(is_array($datas[$add_fields[$i][f_ename]])){
							$_mstring .= "<div style='padding-right:10px;float:left;width:90%;'>
							<input type=hidden name='category_add_infomations[".$add_fields[$i][f_ename]."][title]'  value='".$add_fields[$i][f_name]."' >
							<input type=text class='textbox' id='".$add_fields[$i][f_ename]."_".$key."' name='category_add_infomations[".$add_fields[$i][f_ename]."][value][]'  value='".(is_array($datas) ? $datas[$add_fields[$i][f_ename]][value][0]:"")."' style='width:100%'>
							</div>";

						}else{
							$_mstring .= "<div style='padding-right:10px;float:left;width:90%;'>
							<input type=hidden name='category_add_infomations[".$add_fields[$i][f_ename]."][title]'  value='".$add_fields[$i][f_name]."' >
							<input type=text class='textbox' id='".$add_fields[$i][f_ename]."_".$key."' name='category_add_infomations[".$add_fields[$i][f_ename]."][value][]'  value='' style='width:100%'>
							</div>";
						}
					}
				$mstring .= "
							<tr>
								<td class='input_box_title' > ".$add_fields[$i][f_name]."</td>
								<td class='input_box_item' colspan=3>".$_mstring."</td>
							</tr>";
				}
			}
			$mstring .= " </table><br><br>";
			return $mstring;
		}else{
			if($depth > 3){
				return "";
			}
			return displayCategoryAddInfomation($cid, ($depth+1), $datas);
		}
	}
	
}




function displayStandardCategoryAddInfomation($sellertool="", $cid="", $depth=0){
	global $db;
	
	if($cid){
		//echo "<pre>";
		//$db->debug = true;
		//if(is_array($sellertools)){
		//		foreach($sellertools as $key => $sellertool){
					$sql = "select cr.*  from sellertool_site_info cr  where site_code = '".$sellertool."'   ";
					$db->query($sql);
					$db->fetch();
					$site_name = $db->dt[site_name];

					$sql = "select * from sellertool_category_addfield where cid LIKE '".substr($cid,0,(5-$depth)*3)."%' and site_code = '".$sellertool."'  ";
					
					$db->query($sql); //cid LIKE '".substr($cid,0,($depth)*3)."%
					if($db->total){
						//echo $sql."<br>";
						$add_fields = $db->fetchall();
					

						$mstring = "<table width='100%' cellpadding=0 cellspacing=0>
								<tr height=40>
									<td style='background-color:#efefef;'>
										 <table cellpadding=0 width=100%><tr><td style='padding:10px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> ".$site_name." 카테고리별 추가정보 설정 </b><span class=small> </span></td><td align=right style='padding-right:20px;' class=small></td></tr></table> 
									</td>
								</tr>
								</table><br>";

						
						$mstring .= "<table cellpadding=0 cellspacing=0  border=0 width='100%' class='input_table_box'>
									<col width=15%>
									<col width=90%>"; 
						for($i=0;$i < count($add_fields);$i++){
							$field_values = explode("|",$add_fields[$i][field_value]);
							if($add_fields[$i][field_type] == "checkbox"){
								$_mstring = "";
								foreach($field_values as $key => $val){
									$_mstring .= "<div style='padding-right:10px;float:left;'><input type=checkbox id='".$add_fields[$i][field_ename]."_".$key."' name='category_add_infomations[".$add_fields[$i][field_ename]."][]'  value='".$val."' ><label for='".$add_fields[$i][field_ename]."_".$key."'>".$val."</label></div>";
								}
							}else if($add_fields[$i][field_type] == "select"){
								$_mstring = "<select name='category_add_infomations[".$add_fields[$i][field_ename]."][]'>";
								$_mstring .= "<option  value='' >".$add_fields[$i][field_name]." 선택</option>";
								foreach($field_values as $key => $val){
									$_mstring .= "<option  value='".$val."' >".$val."</option>";
								}
								$_mstring .= "</option>";
							}
						$mstring .= "
									<tr>
										<td class='input_box_title' > ".$add_fields[$i][field_name]."</td>
										<td class='input_box_item' colspan=3>".$_mstring."</td>
									</tr>";
						}
						$mstring .= " </table><br><br>";
						return $mstring;
					}else{
						if($depth > 3){
							return "";
						}
						//echo $cid."<br>";
						return displayStandardCategoryAddInfomation($sellertool, $cid, ($depth+1));
					}
				//}
		//}
	}
	
}

function giftProductList($pid, $disp_type=""){

    global $start,$page, $orderby, $admin_config, $erpid;

    /* <ul id="productList_1" name="productList" class="productList"></ul>을 return 못해서 상품등록시 스트립트 작동안됨
    if(!$pid){
        return "";
    }
    */

    $max = 105;
    $group_code = 1;
    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $db = new Database;

    $db->query("SELECT  p.product_type, p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, gift_ix, p.disp, p.state ,p.one_commission, p.product_type, i.gid  FROM ".TBL_SHOP_PRODUCT." p left join inventory_goods_unit i on p.pcode=i.gu_ix, shop_product_gift gp where p.id = gp.gift_pid and gp.pid = '$pid' order by gp.vieworder asc limit $start,$max");

    if ($db->total == 0){
        if($disp_type == "clipart"){
            $mString = '<ul id="gift_1" name="giftList" class="productList"></ul>';
        }
    }else{
        $i=0;
        if($disp_type == "clipart"){

            $addQaDir = "";
            if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                $addQaDir = "/QA";
            }

            $mString = '<ul id="gift_1" name="giftList" class="productList"></ul>'."\n";
            $mString .= '<script>'."\n";
            $mString .= 'ms_productSearch.groupCode = 1'.";\n";
            for($i=0;$i<$db->total;$i++){
                $db->fetch($i);
                //function(obj, mode, pID, imgSrc, pName, bName, pPrice, listprice, reserve,coprice,wholesale_price,wholesale_sellprice, disp, state, dcprice, vieworder, viewcnt, regdate)
                $mString .= 'ms_productSearch._setProduct("gift_1", "M", "'.$db->dt['id'].'", "'.PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt['id'], "slist").'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'","", "", "", "", "", "'.$db->dt[disp].'", "'.$db->dt[state].'","","","","","'.$db->dt[one_commission].'","'.$db->dt[product_type].'","'.$db->dt[gid].'");'."\n";
            }
            $mString .= '</script>'."\n";
        }
    }


    return $mString;

}

function getFilterItem($filter_type,$pid){
    $db = new database;
    $orderBy = "order by filter_sort";

    /*if($filter_type == 'SHOES') {
        $orderBy = " order by filter_code";
    }*/

    $sql = "select * from shop_product_filter where filter_type = '$filter_type' $orderBy";
    $db->query($sql);
    $filter = $db->fetchall();

    $result = "";
    if(is_array($filter) && count($filter) > 0){
        foreach($filter as $key=>$val){
            $sql = "select * from shop_product_filter_relation where pid = '$pid' and filter_idx = '".$val['idx']."' and filter_type = '".$filter_type."'  ";

            $db->query($sql);
            if($db->total){
                $checkedBool = "checked";
            }else{
                $checkedBool = "";
            }

            $result .= "<label><input type='checkbox' name=\"product_filter[".$filter_type."][]\" value='".$val['idx']."' $checkedBool>".$val['filter_name']." </label>";
        }
    }

    return $result;
}

function input_filter_info($product_filter,$pid,$type){
    $db = new database;
    if($type == 'delete'){
        $sql = "delete from shop_product_filter_relation where pid = '".$pid."' ";
        $db->query($sql);

        return;
    }else{
        $sql = "update shop_product_filter_relation set insert_yn = 'N' where pid = '".$pid."' ";
        $db->query($sql);

        if(is_array($product_filter) && count($product_filter) > 0){
            foreach($product_filter as $key=>$val){

                if(is_array($val) && count($val) > 0){
                    foreach($val as $v){
                        $sql = "select 
                          * 
                        from 
                          shop_product_filter_relation 
                        where 
                          pid = '".$pid."' 
                        and 
                            filter_idx = '".$v."' 
                        and 
                            filter_type = '".$key."'
                        ";
                        $db->query($sql);
                        if($db->total){
                            $db->fetch();
                            $idx = $db->dt['idx'];
                            $sql = "update shop_product_filter_relation set insert_yn = 'Y' where idx = '".$idx."'";
                            $db->query($sql);
                        }else{
                            $sql = "insert shop_product_filter_relation set
                                pid = '".$pid."',
                                filter_idx = '".$v."',
                                filter_type = '".$key."',
                                insert_yn = 'Y',
                                regdate = NOW()";
                            $db->query($sql);
                        }
                    }
                }

            }
        }

        $sql = "delete from shop_product_filter_relation where insert_yn = 'N' and pid = '".$pid."' ";
        $db->query($sql);
    }

    return;
}

function getLaundryList($category_text ="본사", $object_name="cid", $onchange_handler="", $depth=0, $cid="",$type = "member")
{

	$db = new Database;

	if($depth == 0){
		$where = " and laundry_use = 1 and laundry_use_en = 1 "; 
	} else {
		$subCid = substr($cid,0,3);
		$where = " and cid like '$subCid%'and laundry_use = 1 and laundry_use_en = 1 "; 
	}

    $db->query("select cid, title from shop_laundry_info where depth = $depth $where order by cid asc");

	if($depth == '1'){
		$validation = "true";
	}else{
		$validation = "false";
	}

	if ($db->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler validation='".$validation."' style='width:140px;font-size:12px;' validation='false'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $db->total; $i++){
			$db->fetch($i);
			if($cid == $db->dt[cid]){

				$mstring = $mstring."<option value='".$db->dt[cid]."' selected>".$db->dt[title]."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$db->dt[cid]."' >".$db->dt[title]."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler validation=false  style='width:140px;font-size:12px;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";
	
	return $mstring;
}
?>