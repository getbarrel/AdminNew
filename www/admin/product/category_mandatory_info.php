<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");


$db = new Database;

$category = "
<script  id='dynamic'></script>
<script>
/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;

/*	Create Root node	
	setCategoryMandatory(cname,cid,depth, category_display_type,category_use,	category_access,category_code,cname_on,is_adult)
*/
	var rootnode = new TreeNode(\"상품분류\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategoryMandatory('상품분류','000000000000000',-1,'','','','','상품분류','0')\";
	rootnode.expanded = true;
";

$sql = "SELECT 
			c.*
		FROM 
			".TBL_SHOP_CATEGORY_INFO." as c
		where
			c.depth in(0,1,2,3,4)
			order by c.vlevel1, c.vlevel2, c.vlevel3, c.vlevel4, c.vlevel5";
$db->query($sql);

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{
	$db->fetch($i);

	if($db->dt["depth"] == 0){
		$category = $category.PrintNode($db);
	}else if($db->dt["depth"] == 1){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 2){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 3){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 4){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 5){
		$category = $category.PrintGroupNode($db);
	}
}

$category = $category."
	tree.addNode(rootnode);
	tree.draw();
	tree.nodes[0].select();
</script>";

//".sTree()."
$Contents = "
<Script Language='JavaScript' src='../js/XMLHttp.js'></Script>
<Script Language='JavaScript' src='../design/design.js'></Script>
<script language='JavaScript' src='/admin/include/sTree.js'></script>

<script language='JavaScript' src='../include/mTree.js'></script>
<script language='JavaScript' src='category.js'></script>
<script language='JavaScript' src='../include/manager.js'></script>

		<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
		    <td align='left' colspan=3> ".GetTitleNavigation("상품분류설정", "상품관리 > 상품분류설정")."</td>
		</tr>
		<tr>
			<td valign=top width='100%' align='left' style=''>
			<table cellpadding=0 cellspacing=0 width=100% >
			<tr>
				<td width='15%' align='left'  valign='top'>
					<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
					<tr>
						<td valign=top width=236>
							<div id=TREE_BAR >
								<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 width=100% style='border:3px solid #d8d8d8'>
								<tr>
									<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle nowrap>
										<form name='category_order' method='get' action='categoryorder.php' target='calcufrm'>
										<input type='hidden' name='this_depth' value=''>
										<input type='hidden' name='cid' value=''>
										<input type='hidden' name='mode' value=''>
										<input type='hidden' name='view' value=''><!--innerview-->
										<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
										<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cur sor:hand' alt='분류 아래로 이동' align=absmiddle>
										</td>
										<td width=190 valign=middle>
										<span class=small><!--분류선택후 이동버튼 클릭--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
										</form>
									</td>
								</tr>

					<form name='addCategoryDiscount' method=\"post\" enctype='multipart/form-data' action='category_mandatory.save.php' target='act' style='display:inline;'><!--target='calcufrm'-->
								<tr>
									<td colspan=2 width=200 height=400 valign=top style='overflow:auto;padding:0 10px 10px 10px;'>
									<div style=\"width:200px;height:418px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
									".$category."
									</div>
									</td>
								</tr>
								</table>
							</div>
						</td >
					</tr>
					</table>
					<table cellpadding=0 cellspacing=0 width=100% align='center'border=0>
					<tr>
						<td height='10'></td>
					</tr>
					<!--
					<tr>
						<td style='padding:5px;' align='center'>
							<a href='javascript:Initialize_discount()'> *** 할인율 초기화 ***</a>
						</td>
					</tr>-->
					</table>
				</td>
				<td style='padding-left:13px;'></td>
				<td width='82%' align='right' valign='top'>";

$Contents .= "
				<input type='hidden' name='mode' value='category_mandatory'>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_cid' value=''>
				<input type='hidden' name='this_category'>
				<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
				<col width='18%'>
				<col width='32%'>
				<col width='18%'>
				<col width='32%'>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>위치 <img src='".$required3_path."'></b></td>
					<td width=* class='input_box_item'  nowrap colspan='3'>
						<div id='selected_category_1' >미선택 --> <!--왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</div>
					</td>
				</tr>

				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'><b>상품고시정보 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap  colspan='3'>
						".ProductMandtory($company_id , $mi_ix)."
					</td>
				</tr>
				</table><br>
				

				<table cellpadding=0 cellspacing=0 width=100% style='padding-top:10px'>
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> ";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$Contents .= "
					<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"thisCategorySave(document.addCategoryDiscount,'update');\">";
}
$Contents .= "
					</td>
				</tr>
				</table>
				</form>";

$Contents .= "
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
		";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= "
		<table cellpadding=0 cellspacing=0 border=0 width=100%>
		<tr height=10>
			<td colspan=3>
			".HelpBox("카테고리별 상품고시정보 관리 ", $help_text)."
			</td>
		</tr>
		</table>
		<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0></iframe>";

$P = new LayOut;
$addScript = "

<script src='../include/rightmenu.js'></script>\n
<SCRIPT type='text/javascript'>

function department_del(dp_ix){
	$('#department_row_'+dp_ix).remove();
}

function person_del(code){
	$('#row_'+code).remove();
}

</SCRIPT>
".$Script;
$P->addScript = $addScript; /**/
$P->OnloadFunction = ""; 
$P->title = "";
$P->strLeftMenu = product_menu();
$P->strContents = $Contents;
$P->Navigation = "상품관리 > 상품분류관리 > 카테고리별 상품고시정보";
$P->title = "카테고리별 상품고시정보";
$P->PrintLayOut();



function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($mdb){
	
	$cdb = new Database;

	$cname = $mdb->dt[cname];
	$cname_on = $mdb->dt[cname_on];
	$is_adult = $mdb->dt[is_adult];
	$cid = $mdb->dt[cid];
	$depth = $mdb->dt[depth];
	$category_display_type = $mdb->dt[category_display_type];
	$category_code = $mdb->dt[category_code];
	$category_use = $mdb->dt[category_use];
	$is_layout_apply = $mdb->dt[is_layout_apply];
	$mandatory_type = $mdb->dt[mandatory_type];

	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);
	
	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);
	if($mdb->dt[wholesale_dc_rate] || $mdb->dt[dc_rate]){
		$cname = $cname."&nbsp;(".$mdb->dt[wholesale_dc_rate]."%/".$mdb->dt[dc_rate]."%)";
	}
	$cname_on = str_replace("\"","&quot;",$cname_on);
	$cname_on = str_replace("'","&#39;",$cname_on);

//	if ($cid == $mcid){
//		$expandstring = "true";
//	}else{
//		$expandstring = "false";
//	}

	$cdb->query("select * from shop_category_auth where cid = '".$cid."' and category_access not in ('MD','DE')");
	$cdb->fetch();
	if($cdb->dt[category_access]){
		$category_access = $cdb->dt[category_access];
	}else{
		for($i=$depth;$i>=0;$i--){

			$org_cid = substr($cid,0,3+(3*$i));
			$org_cid =$org_cid."000000000000";
			$for_cid = substr($org_cid,0,15);

			$sql = "select * from shop_category_auth where cid = '".$for_cid."' and category_access not in ('MD','DE') ";
			$cdb->query($sql);
			$cdb->fetch();
			$category_access = $cdb->dt[category_access];

			if($cdb->dt[category_access]){
				$category_access = $cdb->dt[category_access];
				break;
			}else{
				$category_access = 'D';
			}

		}
	}

	
	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.cid = '$cid';
	node$cid.cdepth = '$depth';
	node$cid.is_layout_apply = '$is_layout_apply';
	node$cid.action = \"setCategoryMandatory('$cname','$cid',$depth, '$category_display_type','$category_use','$category_access','$category_code','$cname_on','$is_adult','$is_layout_apply','$mandatory_type')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($mdb)
{

	global $cid;
	
	$cdb = new Database;
	$cname = $mdb->dt[cname];
	$cname_on = $mdb->dt[cname_on];
	$mcid = $mdb->dt[cid];
	$depth = $mdb->dt[depth];
	$category_display_type = $mdb->dt[category_display_type];
	$category_code = $mdb->dt[category_code];
	$is_adult = $mdb->dt[is_adult];
	$category_use = $mdb->dt[category_use];
	$is_layout_apply = $mdb->dt[is_layout_apply];
	$mandatory_type = $mdb->dt[mandatory_type];

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
	$cname = str_replace("'","&#39;",$cname);
	if($mdb->dt[wholesale_dc_rate] || $mdb->dt[dc_rate]){
		$cname = $cname."&nbsp;(".$mdb->dt[wholesale_dc_rate]."%/".$mdb->dt[dc_rate]."%)";
	}
	$cname_on = str_replace("\"","&quot;",$cname_on);
	$cname_on = str_replace("'","&#39;",$cname_on);

///echo "".substr($mcid,0,($_GET["depth"]+1)*3)."==".substr($_GET["cid"],0,($_GET["depth"]+1)*3)."<br><br>";
	$mstring =  "		var groupnode$mcid = new TreeNode('$cname ',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	if ($mcid == $cid || (substr($mcid,0,($depth)*3) == substr($_GET["cid"],0,($depth)*3) && $_GET["depth"] > $depth )  || (substr($mcid,0,($depth+1)*3) == substr($_GET["cid"],0,($depth+1)*3)) ){//
		$mstring .=  "	groupnode$mcid.expanded = true;\n";
	//	$mstring .=  "	groupnode$cid.select = true;\n";
	}

	$cdb->query("select * from shop_category_auth where cid = '".$mcid."' and category_access not in ('MD','DE')");
	$cdb->fetch();
	if($cdb->dt[category_access]){
		$category_access = $cdb->dt[category_access];
	}else{
		for($i=$depth;$i>=0;$i--){

			$org_cid = substr($mcid,0,3+(3*$i));
			$org_cid =$org_cid."000000000000";
			$for_cid = substr($org_cid,0,15);

			$sql = "select * from shop_category_auth where cid = '".$for_cid."'  and category_access not in ('MD','DE')";

			$cdb->query($sql);
			$cdb->fetch();
			$category_access = $cdb->dt[category_access];

			if($cdb->dt[category_access]){
				$category_access = $cdb->dt[category_access];
				break;
			}else{
				$category_access = 'D';
			}
		}
	}

	$mstring .=  "	groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.cid = '$mcid';
		groupnode$mcid.cdepth = '$depth';
		groupnode$mcid.is_layout_apply = '$is_layout_apply';
		groupnode$mcid.action = \"setCategoryMandatory('$cname','$mcid',$depth, '$category_display_type','$category_use','$category_access','$category_code','$cname_on','$is_adult','$is_layout_apply','$mandatory_type')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";

	return $mstring;
}



?>