<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
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
	setCategory(cname,cid,depth,category_use,category_code)
*/
	var rootnode = new TreeNode(\"품목분류\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('품목분류','000000000000000',-1,'','')\";
	rootnode.expanded = true;
";

$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"inventory_category_info";

$db->query("SELECT * FROM ".$tb." where depth in(0,1,2,3,4)  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);

	if ($db->dt["depth"] == 0){
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


$Contents = "
<script language='JavaScript' src='../include/cTree.js'></script>
<script language='JavaScript' src='../include/manager.js'></script>

		<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
		    <td align='left' colspan=3> ".GetTitleNavigation("품목분류설정", "재고관리 > 품목분류설정")."</td>
		</tr>
		<tr>
			<td valign=top width='100%' align='left' style=''>
				<div class='tab' style='width:100%;height:38px;margin:0px;'>
					<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('edit_category','tab_01')\" style='padding-left:20px;padding-right:20px;'>
									분류 수정
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('add_subcategory','tab_02')\" style='padding-left:20px;padding-right:20px;'>
									분류 추가
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td class='btn' style='vertical-align:bottom;padding-bottom:3px;'>";
							if($_SESSION['admin_config']["mall_inventory_category_div"]=="P"){
								$Contents .= "<div class='red'>* 품목분류를 상품분류와 동일하게 사용하시기 때문에 수정및추가시 상품카테고리와 같이 반영이 됩니다.</div> ";
							}
						$Contents .= "
						</td>
					</tr>
					</table>
				</div>
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
										<form name='category_order' method='get' action='inventory_category.order.php' target='calcufrm'>
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
								<tr><form>
									<td colspan=2 width=200 height=400 valign=top style='overflow:auto;padding:0 10px 10px 10px;'>
									<div style=\"width:200px;height:418px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
									$category
									</div>
									</td>
								</tr></form>
								</table>
							</div>
						</td >
					</tr>
					</table>
				</td>
				<td style='padding-left:13px;'></td>
				<td width='82%' align='right' valign='top'>";

$Contents .= "
				<div id='edit_category' style='display:block;'>
				<form name='thisCategoryform' method=\"post\" enctype='multipart/form-data' action='inventory_category.act.php' target='calcufrm' style='display:inline;'>
				<input type='hidden' name='act' value='update'>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
				<col width='15%'>
				<col width='35%'>
				<col width='15%'>
				<col width='35%'>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류 등록 품목수</b></td>
					<td class='input_box_item' nowrap>
						<span id='product_cnt'>0 개</span>
					</td>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>하위분류포함 품목수</b></td>
					<td class='input_box_item' nowrap>
						<span id='product_total_cnt'>0 개</span>
					</td>
				</tr>
				<tr>
					<td width=170 class='input_box_title' nowrap>  <b>위치 <img src='".$required3_path."'></b></td>
					<td width=* class='input_box_item'  nowrap colspan='3'>
						<div id='selected_category_1' >왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요.</div>
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류명 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						<input type='text' class='textbox' name='this_category' maxlength=40 validation=true title='선택된 분류'>
					</td>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류코드 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap >
						<input type='text' class='textbox' name='category_code' id='category_code' maxlength=40 validation=true title='분류코드'>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'><b>분류 사용 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap  colspan='3' style='padding:5px;'>
						<input type='radio' name='category_use' id='category_use_id' value=1 checked /><label for='category_use_id'> 사용</label>&nbsp;
						<input type='radio' name='category_use' id='category_use_id_0' value=0 /><label for='category_use_id_0'> 미사용</label>
					</td>
				</tr>
				</table>
				<table cellpadding=0 cellspacing=0 width=100% style='padding-top:20px'>
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> ";
if($_SESSION['admin_config']["mall_inventory_category_div"]!="P"){
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D") ){
	$Contents .= "		<!--input type='checkbox' name='sub_cartegory_delete' id='sub_cartegory_delete_id'value='1'> <label for='sub_cartegory_delete_id'>하부분류 모두삭제</label-->
						<img src='../images/".$admininfo["language"]."/bt_category_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"category_del(document.thisCategoryform);\">
						<script>
						function category_del(frm){
							var select = confirm(frm.this_category.value + '을(를) 삭제하시겠습니까?');
							if(select){
								thisCategorySave(frm,'delete');
							}else{
								return false;
							}
						}
						</script>
						";
	}
}
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$Contents .= "
					<img src='../images/".$admininfo["language"]."/bt_category_modify.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"thisCategorySave(document.thisCategoryform,'update');\">";
}
$Contents .= "
					</td>
				</tr>
				</table>
				</form>
				</div>";

$Contents .= "
				<div id='add_subcategory' style='display:none;'>
				<form name='subCategoryform' method=\"post\" enctype='multipart/form-data' action='inventory_category.act.php' target='act' style='display:inline;'>
				<input type='hidden' name='act' value='add'>
				<input type='hidden' name='sub_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_cid' value=''>

				<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr>
					<td width=170 class='input_box_title' nowrap>  <b>위치 <img src='".$required3_path."'></b></td>
					<td width=* class='input_box_item'  nowrap colspan='3'>
						<div id='selected_category_2' >왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요.</div>
					</td>
				</tr>
				<tr >
					<td class='input_box_title' nowrap>  <b>분류명 <img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
						<input type='text' class='textbox' name='sub_category' maxlength=40 validation=true title='선택된 분류'>
					</td>
					<td class='input_box_title' nowrap>  <b>분류코드 <img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
						<input type='text' class='textbox' name='category_code' maxlength=40 validation=true title='분류코드'>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'><b>분류 사용 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap  colspan='3' style='padding:5px;'>
						<input type='radio' name='category_use' id='category_use_id_' value=1 checked><label for='category_use_id_'> 사용</label>&nbsp;
						<input type='radio' name='category_use' id='category_use_id_10' value=0 ><label for='category_use_id_10'> 미사용</label>
					</td>
				</tr>
				</table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$Contents .= "
				<table cellpadding=5 cellspacing=1 width=97% >
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> <img src='../images/".$admininfo["language"]."/bt_category_add.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"SubCategorySave(document.subCategoryform,'add');\"></td>
				</tr>
				</table>";
}
$Contents .= "
				</form>
				</div>";

$Contents .= "
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
		";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle ><img src='/admin/image/icon_list.gif' b></td><td class='small' style='line-height:120%'>분류 디자인을 텍스트 또는 이미지로 하실수 있습니다. 이미지 등록후 예시와 같이 치환코드를 변경해주시면 됩니다 <span class=small>예) text 사용시 : {categorys.cname} , image 사용시 : {categorys.leftcatimg}</span></td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' style='line-height:120%' >상단에 <label for='category_mode_add1' style='font-weight:bold'> 분류 추가</label> 를 선택하신후 좌측 품목 분류에서 추가하기 원하는 분류를  선택하신후  <b>분류 추가</b> 입력란에 분류를 입력하신후 <img src='../image/bt_category_add.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>분류 수정</b> : 상단에 <label for='category_mode_add1' style='font-weight:bold'> 선택된 분류 수정 </label> 를 선택하신후 좌측 품목 분류에서 추가하기 원하는 분류를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>분류 삭제</b> : 상단에 <label for='category_mode_add1' style='font-weight:bold'> 선택된 분류 수정 </label> 를 선택하신후 좌측 품목 분류에서 추가하기 원하는 분류를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' > 하부분류를 포함해서 삭제하고 싶으신경우에는 <input type='checkbox' name='sub_cartegory_delete1' id='sub_cartegory_delete_id1'value='1' > <label for='sub_cartegory_delete_id1' style='font-weight:bold'>하부분류 모두삭제</label> 를 선택한후 <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= "
		<table cellpadding=0 cellspacing=0 border=0 width=100%>
		<tr height=10>
			<td colspan=3>
			".HelpBox("분류 관리 ", $help_text)."
			</td>
		</tr>
		</table>
		<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0></iframe>";

if($view == "innerview"){
	$P = new popLayOut;
	$addScript = "
	<script language='JavaScript' src='../webedit/webedit.js'></script>
	<script src='../include/rightmenu.js'></script>\n".$Script;
	$P->addScript = $addScript; /**/
	$P->OnloadFunction = "";
	$P->title = "";//text_button('#', " ♣ 분류 구성");
	$P->strLeftMenu = "";
	$Contents ="
		<div id='category_area'>
			<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
				<tr>
					<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle>
						<form name='category_order' method='get' action='inventory_category.order.php' target='calcufrm'>
						<input type='hidden' name='this_depth' value=''>
						<input type='hidden' name='cid' value=''>
						<input type='hidden' name='mode' value=''>
						<input type='hidden' name='view' value='innerview'>
						<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
						<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cursor:hand' alt='분류 아래로 이동' align=absmiddle>
						<span class=small>분류선택후 이동버튼 클릭</span>
						</form>
					</td>
				</tr>
				<tr>
					<td width=200 height=400 valign=top style='overflow:auto;padding:0 10 10 10;'>
					<form>
						<div style=\"width:200px;height:420px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
						$category
						</div>
					</form>
					</td>
				</tr>
				</table>
		</div>

		<script>alert(document.getElementById('category_area').innerHTML);parent.document.getElementById('TREE_BAR').innerHTML = document.getElementById('category_area').innerHTML;</script>";

	$P->strContents = $Contents;
	$P->Navigation = "";
	$P->PrintLayOut();
	exit;
}else{


$addScript = "
<script language='JavaScript' src='../webedit/webedit.js'></script>
<script src='../include/rightmenu.js'></script>\n
<SCRIPT type='text/javascript'>
<!--

	function thisCategorySave(frm,vMode)
	{
		//alert(frm);
		
		if (frm.this_category.value.length < 1){
			alert('수정/삭제 하시고자 하는 품목분류를 선택해 주세요');
			return false;	
		}
		if(CheckFormValue(frm)){	
			frm.act.value = vMode;
			frm.submit();
		}
		
		
	}

	function SubCategorySave(frm,vMode)
	{

		if (frm.sub_cid.value.length != 15){
			alert('추가 하시고자 하는 품목분류를 선택해 주세요');
			return false;	
		}

		if (frm.cid.value.length != 15){
			alert('추가 하시고자 하는 품목분류를 선택해 주세요');
			return false;	
		}

		
		
		if (frm.sub_category.value.length < 1){
			alert('추가 하시고자 하는 품목분류를 입력해 주세요');
			return false;	
		}
		
		if (frm.sub_depth.value >= 5){
			return false;	
		}

		if(CheckFormValue(frm)){	
			frm.act.value = vMode;
			frm.submit();
		}
	}

	function showTabContents(vid, tab_id){
		var area = new Array('edit_category','add_subcategory');
		var tab = new Array('tab_01','tab_02');

		for(var i=0; i<area.length; ++i){

			if(area[i]==vid){
				
				document.getElementById(vid).style.display = 'block';
				//document.getElementById(tab_id).className = 'on';
				if(window.addEventListener) { // 호환성 kbk
					document.getElementById(tab_id).setAttribute('class','on');
				} else {
					document.getElementById(tab_id).className = 'on';
				}
			}else{			
		
				document.getElementById(area[i]).style.display = 'none';
				//document.getElementById(tab[i]).className = '';
				if(window.addEventListener) { // 호환성 kbk
					document.getElementById(tab[i]).setAttribute('class','');
				} else {
					document.getElementById(tab[i]).className = '';
				}
			}
		}
		
	}

	function setCategory(cname,cid,depth,category_use,category_code)
	{

		cname = cname.replace('&quot;','\'');

		document.thisCategoryform.this_category.value = cname;
		document.thisCategoryform.category_code.value = category_code;	//분류코드
		document.thisCategoryform.cid.value = cid;
		document.thisCategoryform.this_depth.value = depth;

		if(category_use == '1'){	//사용
			$('#category_use_id').attr('checked',true);
		}else if(category_use == '0'){	//미사용
			$('#category_use_id_0').attr('checked',true);
		}else{
			$('#category_use_id_0').attr('checked',true);
		}

		document.category_order.this_depth.value = depth;
		document.category_order.cid.value = cid;
		
		document.subCategoryform.cid.value = cid;
		document.subCategoryform.sub_depth.value = eval(depth+1);
		
		
		document.getElementById('calcufrm').src='calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...

		$.ajax({
		    url : './inventory_category.act.php',
		    type : 'POST',
		    data : {cid:cid,
					depth:depth,
					act:'product_cnt'
					},
		    dataType: 'json',
		    error: function(data,error){// 실패시 실행함수 
		        alert(error);},
		    success: function(args){
				if(args != null){
					$('#product_cnt').html(args.product_cnt+' 개');
					$('#product_total_cnt').html(args.product_total_cnt+' 개');
				}
	        }
	    });
	}

//-->



</SCRIPT>
".$Script;

	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->addScript = $addScript;
		//$P->OnloadFunction = "initTrees();";
		$P->strLeftMenu = inventory_menu();
		$P->strContents = $Contents;
		$P->Navigation = "재고관리 > 품목분류설정";
		$P->NaviTitle = "품목분류설정";
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut;
		$P->addScript = $addScript; /**/
		//$P->OnloadFunction = "Init(document.design_subcategory);MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')"; //showSubMenuLayer('storeleft'); MenuHidden(false);
		$P->title = "";//text_button('#', " ♣ 분류 구성"); 
		$P->strLeftMenu = inventory_menu();
		$P->strContents = $Contents;
		$P->Navigation = "재고관리 > 품목분류설정";
		$P->title = "품목분류설정";
		$P->PrintLayOut();
	}

}

function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($mdb){
	
	//$cdb = new Database;

	$cname = $mdb->dt[cname];
	$cname_on = $mdb->dt[cname_on];
	$is_adult = $mdb->dt[is_adult];
	$cid = $mdb->dt[cid];
	$depth = $mdb->dt[depth];
	$category_display_type = $mdb->dt[category_display_type];
	
	$category_code = $mdb->dt[category_code];
	$category_use = $mdb->dt[category_use];
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);
	
	$cname_on = str_replace("\"","&quot;",$cname_on);
	$cname_on = str_replace("'","&#39;",$cname_on);

	
	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_use','$category_code')\";
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
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth, '$category_use','$category_code')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";

	return $mstring;
}


?>