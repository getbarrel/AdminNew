<?
include("../class/layout.class");
//include("../webedit/webedit.lib.php");

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
	var rootnode = new TreeNode(\"표준상품분류\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('표준상품분류','000000000000000',-1,'','')\";
	rootnode.expanded = true;
";

$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"standard_category_info";

$db->query("SELECT * FROM standard_category_info where depth in(0,1,2,3,4)  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

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


$Contents = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>\n<!--script language='JavaScript' src='referer.js'></script-->

		<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
		    <td align='left' colspan=3> ".GetTitleNavigation("표준상품분류설정", "재고관리 > 표준상품분류설정")."</td>
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
							<table id='tab_05' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('input_addfield','tab_05')\">분류별 부가정보 필드 정의</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td class='btn' style='vertical-align:bottom;padding-bottom:3px;'>";
							if($_SESSION['admin_config']["mall_inventory_category_div"]=="P"){
								$Contents .= "<div class='red'>* 표준상품분류를 상품분류와 동일하게 사용하시기 때문에 수정및추가시 상품카테고리와 같이 반영이 됩니다.</div> ";
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
										<form name='category_order' method='get' action='standard_category.order.php' target='iframe_act'>
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
				<form name='thisCategoryform' method=\"post\" enctype='multipart/form-data' action='standard_category.act.php' target='iframe_act' style='display:inline;'><!--iframe_act-->
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
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류코드 </b></td>
					<td class='input_box_item' nowrap >
						<input type='text' class='textbox' name='category_code' id='category_code' maxlength=40 validation=false title='분류코드'>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'><b>분류 사용 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap  colspan='3' style='padding:5px;'>
						<input type='radio' name='category_use' id='category_use_id' value=1 checked /><label for='category_use_id'> 사용</label>&nbsp;
						<input type='radio' name='category_use' id='category_use_id_0' value=0 /><label for='category_use_id_0'> 미사용</label>
					</td>
				</tr>
				</table><br>
				";


$Contents .= "
			<div id='category_div'>
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;' >
				".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 전시 카테고리 매핑 </b><span class=small><!--하단에 카테고리를 선택하신후 카테고리 등록하기 버튼을 클릭하세요.(다중 카테고리 등록지원)-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')." </span></td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."
				</td>
			</tr>
			</table>

			<div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='category_tab_01' class='on'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"showCategoryTab('select_category','category_tab_01');\">선택등록</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='category_tab_02'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"showCategoryTab('search_category','category_tab_02');\">검색어등록</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td class='btn'>
				</td>
			</tr>
			</table>
			</div><br>";
			
			$Contents .= "
			<input type='hidden' name=selected_cid value='".$cid."'>
			<input type='hidden' name=selected_depth value=''>
			<input type='hidden' id='_category' value=''>
			<input type='hidden' id='_category' value=''>
			<input type='hidden' id='basic' value=''> 

			<table cellpadding=0 cellspacing=0  border='0' width='100%' class='input_table_box' id='select_category' style='display:;'>
				<col width=15%>
				<col width=90%>
				<tr>
					<td class='input_box_title'  nowrap> <b>카테고리 </b> </td>
					<td class='input_box_item'>
						<table width=100% border=0 cellpadding=0 cellspacing=0>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<tr>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--1차분류--", "cid0", "cid","onChange=\"loadCategory($(this),'cid1',2)\" title='1차분류' ", 0, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--2차분류--", "cid1",  "cid","onChange=\"loadCategory($(this),'cid2',2)\" title='2차분류'", 1, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--3차분류--", "cid2", "cid", "onChange=\"loadCategory($(this),'cid3',2)\" title='3차분류'", 2, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--4차분류--", "cid3", "cid", "onChange=\"loadCategory($(this),'cid4',2)\" title='4차분류'", 3, $cid)."</td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--5차분류--", "cid4", "cid", "onChange=\"loadCategory($(this),'cid_1',2)\" title='5차분류'", 4, $cid)."</td>
								<td style='padding:5px 4px 5px 6px;'><img src='../images/".$_SESSION["admininfo"]["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\" style='cursor:pointer;'></td>
							</tr>
						</table>";
				$Contents .= "	</td>
				</tr>
			</table>

			<table cellpadding=0 cellspacing=0  border='0' width='100%' class='input_table_box' id='search_category' style='display:none;'>
				<col width=15%>
				<col width=90%>
				<tr>
					<td class='input_box_title' nowrap> <b>카테고리 </b> </td>
					<td class='input_box_item'>
						<table width=100% border='0' cellpadding=0 cellspacing=0>
							<col width='15%'>
							<col width='10%'>
							<col width='38%'>
							<col width='38%'>
							<tr>
								<td style='padding:5px 0px 5px 2px;'>
								<textarea name='search_category_text' id='search_category_text' style='padding:0px;height:105px;width:99%' class='tline textbox'>".$search_category."</textarea>
								</td>
								<td align='center'>
								<img src='../images/".$_SESSION["admininfo"]["language"]."/search_category.gif' align=absmiddle border=0 onclick=\"search_multcategory()\" style='cursor:pointer;'></td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--1차분류--", "search_category_list", "cid","", 0, $cid)." </td>
								<td style='padding:5px 4px 5px 6px;'><img src='../images/".$_SESSION["admininfo"]["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\" style='cursor:pointer;'></td>
							</tr>
						</table>";

				$Contents .= "	</td>
				</tr>
			</table><br>
			

			<table border=0 cellpadding=0 cellspacing=0 width='100%' style='padding:5px 10px 5px 10px;border:1px solid silver' >
				<col width=100%>
				<tr>
					<td style='padding:10px 10px;'>";
						if($id != ""){
							 
								$Contents .= PrintRelation($id);
						 
						}else{
							$Contents .= "<table width=100% cellpadding=0 cellspacing=0 id=objCategory >
										<col width=5>
										<col width=50>
										<col width=*>
										<col width=30>
						</table>";
						}
				$Contents .= "	</td>
				</tr>
				<tr><td class='small' height='25' style='padding-left:15px;'> <span class='small'> <!--* 첫번째 선택된 카테고리가 기본카테고리로 지정되며 라디오 버튼 클릭으로 기본카테고리를 변경 하실 수 있습니다>--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')." </span></td></tr>
			</table><br>
			</div>";


$Contents .="<table cellpadding=0 cellspacing=0 width=100% >
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right style='padding-top:20px'> ";

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
				<form name='subCategoryform' method=\"post\" enctype='multipart/form-data' action='standard_category.act.php' target='act' style='display:inline;'>
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
					<td class='input_box_title' nowrap>  <b>분류코드 </b></td>
					<td class='input_box_item'>
						<input type='text' class='textbox' name='category_code' maxlength=40 validation=false title='분류코드'>
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
				<div class='doong'  id='input_addfield' style='display:none;'>
					<form name='add_field' method=\"post\" enctype='multipart/form-data' action='category.save.php' onsubmit='return SaveAddField(this);' target='calcufrm'>
					<input type='hidden' name='mode' value='add_field'>
					<input type='hidden' name='cid' value=''>
					<input type='hidden' name='sub_mode' value='add_field'>
					<table cellpadding=5 cellspacing=0 border=0 width='97%' >
						<tr height=40>
							<td colspan=2 width=120 nowrap><b>선택된 분류</b> </td>
							<td colspan=5 width='270' id='category_name' style='font-weight:bold;' class='small'>
							미선택 --> 왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요
							</td>
						</tr>
					</table>
					<table cellpadding=5 cellspacing=0 border=0 width='97%' class='input_table_box' id='add_field_table'>
						<col width=100>
						<col width=50>
						<col width=160>
						<col width=160>
						<col width=100>
						<col width=*>
						<tr bgcolor=#efefef style='text-align:center;'>
							<td class='input_box_title' nowrap>구분</td>
							<td class='input_box_title' nowrap>주요<br>정보</td>
							<td class='input_box_title' nowrap>영문명 </td>
							<td class='input_box_title' >필드이름 </td>
							<td class='input_box_title' nowrap>필드타입</td>
							<td class='input_box_title' nowrap>
								필드기본값 예) 1024*768|1280*800|1600*1200
							</td>
						</tr>
						<tr>
							<td class='input_box_title' >기타정보 1</td>
							<td class='input_box_item' ><input type='checkbox' name='etc1_search' value='1'>  </td>
							<td class='input_box_item' ><input type='text' name='etc1_ename' id='etc1_ename' value=''>  </td>
							<td class='input_box_item' ><input type='text' name='etc1'  id='etc1' value=''>  </td>
							<td class='input_box_item' nowrap>
								<select name='etc1_type' id='etc1_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item'  nowrap>
								<input type='text' name='etc1_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap>기타정보 2</td>
							<td class='input_box_item' ><input type='checkbox' name='etc2_search' value='1'>  </td>
							<td class='input_box_item' ><input type='text' name='etc2_ename' value=''></td>
							<td class='input_box_item' ><input type='text' name='etc2' value=''></td>
							<td class='input_box_item' nowrap>
								<select name='etc2_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item' nowrap>
								<input type='text' name='etc2_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap>기타정보 3</td>
							<td class='input_box_item' ><input type='checkbox' name='etc3_search' value='1'>  </td>
							<td class='input_box_item'><input type='text' name='etc3_ename' value=''>  </td>
							<td class='input_box_item'><input type='text' name='etc3' value=''></td>
							<td class='input_box_item' nowrap>
								<select name='etc3_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item' nowrap>
								<input type='text' name='etc3_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap>기타정보 4</td>
							<td class='input_box_item' ><input type='checkbox' name='etc4_search' value='1'>  </td>
							<td class='input_box_item'><input type='text' name='etc4_ename' value=''>  </td>
							<td class='input_box_item'><input type='text' name='etc4' value=''></td>
							<td class='input_box_item' nowrap>
								<select name='etc4_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item' nowrap>
								<input type='text' name='etc4_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap>기타정보 5</td>
							<td class='input_box_item' ><input type='checkbox' name='etc5_search' value='1'>  </td>
							<td class='input_box_item'><input type='text' name='etc5_ename' value=''>  </td>
							<td class='input_box_item'><input type='text' name='etc5' value=''></td>
							<td class='input_box_item' nowrap>
								<select name='etc5_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item' nowrap>
								<input type='text' name='etc5_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap>기타정보 6</td>
							<td class='input_box_item' ><input type='checkbox' name='etc6_search' value='1'>  </td>
							<td class='input_box_item'><input type='text' name='etc6_ename' value=''>  </td>
							<td class='input_box_item'><input type='text' name='etc6' value=''></td>
							<td class='input_box_item' nowrap>
								<select name='etc6_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item' nowrap>
								<input type='text' name='etc6_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap>기타정보 7</td>
							<td class='input_box_item' ><input type='checkbox' name='etc7_search' value='1'>  </td>
							<td class='input_box_item'><input type='text' name='etc7_ename' value=''>  </td>
							<td class='input_box_item'><input type='text' name='etc7' value=''></td>
							<td class='input_box_item'nowrap>
								<select name='etc7_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item' nowrap>
								<input type='text' name='etc7_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap>기타정보 8</td>
							<td class='input_box_item' ><input type='checkbox' name='etc8_search' value='1'>  </td>
							<td class='input_box_item'><input type='text' name='etc8_ename' value=''>  </td>
							<td class='input_box_item'><input type='text' name='etc8' value=''></td>
							<td class='input_box_item' nowrap>
								<select name='etc8_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item' nowrap>
								<input type='text' name='etc8_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap>기타정보 9</td>
							<td class='input_box_item' ><input type='checkbox' name='etc9_search' value='1'>  </td>
							<td class='input_box_item'><input type='text' name='etc9_ename' value=''>  </td>
							<td class='input_box_item'><input type='text' name='etc9' value=''></td>
							<td class='input_box_item' nowrap>
								<select name='etc9_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item' nowrap>
								<input type='text' name='etc9_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td  class='input_box_title' nowrap>기타정보 10</td>
							<td class='input_box_item' ><input type='checkbox' name='etc10_search' value='1'>  </td>
							<td class='input_box_item'><input type='text' name='etc10_ename' value=''>  </td>
							<td class='input_box_item'><input type='text' name='etc10' value=''></td>
							<td class='input_box_item' nowrap>
								<select name='etc10_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item' nowrap>
								<input type='text' name='etc10_value' size=50 value=''>
							</td>
						</tr>
					</table>
					<table cellpadding=5 cellspacing=0 border=0 width='97%' >
						<tr>
							<td colspan=2 align=center> <input type=image src='../image/b_save.gif' border=0 align=absmiddle ></td>
						</tr>
					</table>
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
						<form name='category_order' method='get' action='standard_category.order.php' target='iframe_act'>
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
<script src='../include/rightmenu.js'></script>\n
<SCRIPT type='text/javascript'>
<!--
	function categoryadd()
	{
		var ret;
		var str = new Array();
		var dupe_bool = false;
		var obj = $('form[name=thisCategoryform]').find('select[class^=cid]');
		var admin_level = '".$_SESSION["admininfo"]["admin_level"]."';

		if(admin_level == 8){
			if($('input[type=radio][name=basic]').length > 0){
				alert('카테고리 입력은 한개만 가능합니다. ');
				return false;
			}
		}

		var category_info = new Array();
		var text = '';
 

		obj.each(function(index){
			
			if($(this).find('option:selected').val()){
				if($(this).find('option:selected').length == 1){
					str[str.length] =  $(this).find('option:selected').text();
					ret = $(this).find('option:selected').val();
					text = '{ \"cid\": \"'+$(this).val()+'\", \"category_str\": \"'+str.join(\" > \") + '>'+ $(this).text()+'\"}' ;
					
				}
				if(true){
					if($(this).find('option:selected').length > 1){
						var selected_obj = $(this).find('option:selected');
						selected_obj.each(function(_index){
							// alert($(this).val());
							//alert(selected_obj.length+';;;'+_index);
							if(_index == 0){//(selected_obj.length-1)
								//alert('첫번째 ');
								text = '{ \"cid\": \"'+$(this).val()+'\", \"category_str\": \"'+str.join(\" > \") + ' > '+ $(this).text()+'\"},' ;
							}else if(_index == (selected_obj.length-1)){
								text += '{ \"cid\": \"'+$(this).val()+'\", \"category_str\": \"'+str.join(\" > \") + ' > '+ $(this).text()+'\"}' ;
							}else{
								//alert('와야해~~');
								text += '{ \"cid\": \"'+$(this).val()+'\", \"category_str\": \"'+str.join(\" > \") + ' > '+ $(this).text()+'\"},' ;
							}
							//alert(text);
						});
						text = '{ \"category_info\" : ['+ text +']}';
						//alert(text);
						
					
					}else{

					}
				}
			}
		});
//console.log(text);
//alert(text);
var selected_obj = JSON.parse(text);
//console.log(selected_obj);
//alert(selected_obj.category_info.length);
		if(selected_obj.category_info.length > 1){
			$.each(selected_obj.category_info, function(i,selected_category_info){  
				if (!selected_category_info.cid){
					alert(language_data['goods_input.php']['A'][language]);//'카테고리를 선택해주세요'
					return;
				}

				var cate = $('input[name^=display_category]');//document.getElementsByName('display_category[]'); // 호환성 kbk

				cate.each(function(){
					if(selected_category_info.cid == $(this).val()){
						dupe_bool = true;
						alert(language_data['goods_input.php']['B'][language]);
						//'이미등록된 카테고리 입니다.'
						return;
					}
				});
				if(dupe_bool){
					return ;
				}

				var obj = $('#objCategory');
				
				if(obj.find('tr').length == 0){
					var input_str = \"<input type=radio name=basic id='basic_\"+ selected_category_info.cid + \"' value='\"+ selected_category_info.cid + \"' validation=true title='기본카테고리' checked>\";	
				}else{
					var input_str = \"<input type=radio name=basic id='basic_\"+ selected_category_info.cid + \"' value='\"+ selected_category_info.cid + \"' validation=true title='기본카테고리' >\";
				}

				obj.append(\"<tr id='num_tr' height=30 class=''><td><input type=text name=display_category[] id='_category' value='\" + selected_category_info.cid + \"' style='display:none'></td><td id='currPosition'>\"+input_str+\"</td><td><label for='basic_\"+selected_category_info.cid+\"'>\"+selected_category_info.category_str+\"</label></td><td><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\\\" $(this).closest('tr').remove();\\\" style='cursor:point;'></td></tr>\");
			});
		}else{
				if (!ret){
					alert(language_data['goods_input.php']['A'][language]);//'카테고리를 선택해주세요'
					return;
				}

				var cate = $('input[name^=display_category]');//document.getElementsByName('display_category[]'); // 호환성 kbk

				cate.each(function(){
					if(ret == $(this).val()){
						dupe_bool = true;
						alert(language_data['goods_input.php']['B'][language]);
						//'이미등록된 카테고리 입니다.'
						return;
					}
				});
				if(dupe_bool){
					return ;
				}

				var obj = $('#objCategory');
				
				if(obj.find('tr').length == 0){
					var input_str = \"<input type=radio name=basic id='basic_\"+ ret + \"' value='\"+ ret + \"' validation=true title='기본카테고리' checked>\";	
				}else{
					var input_str = \"<input type=radio name=basic id='basic_\"+ ret + \"' value='\"+ ret + \"' validation=true title='기본카테고리' >\";
				}

				obj.append(\"<tr id='num_tr' height=30 class=''><td><input type=text name=display_category[] id='_category' value='\" + ret + \"' style='display:none'></td><td id='currPosition'>\"+input_str+\"</td><td><label for='basic_\"+ret+\"'>\"+str.join(\" > \")+\"</label></td><td><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\\\" $(this).closest('tr').remove();\\\" style='cursor:point;'></td></tr>\");
		}
	}

	function category_del(el)
	{

		idx = el.rowIndex;
		var obj = document.getElementById('objCategory');
		obj.deleteRow(idx);
		var cObj=\$('input[name=basic]');
		var cObj_num=0;
		if(cObj.length == null){
			//cObj[0].checked = true; // 0이 나오지 null이 나오지 않음 kbk
		}else{
			for(var i=0;i<cObj.length;i++){
				if(cObj[i].checked){
					cObj_num++;
				}
			}
			if(cObj_num==0) {
				cObj[0].checked = true;
			}
		}
		//cate.splice(idx,1);
	}
	function loadCategory(obj,target) {
		
		var trigger = obj.find('option:selected').val();
		var form = obj.closest('form').attr('name');
		var depth = obj.attr('depth');//sel.getAttribute('depth');
		
		$.ajax({ 
				type: 'GET', 
				data: {'return_type': 'json', 'form':form, 'trigger':trigger, 'depth':depth, 'target':target},
				url: '../product/category.load.php',  
				dataType: 'json', 
				async: true, 
				beforeSend: function(){ 
					
				},  
				success: function(datas){
					$('select[class=cid]').each(function(){
						if(parseInt($(this).attr('depth')) > parseInt(depth)){
							$(this).find('option').not(':first').remove();
						}
					});
					 
					if(datas != null){
						$.each(datas, function(i, data){ 
								$('select[name='+target+']').append(\"<option value='\"+data.cid+\"'>\"+data.cname+\"</option>\");
						});  
					}
				} 
			});  
	}

	function showCategoryTab(vid, tab_id){
		var area = new Array('select_category','search_category');
		var tab = new Array('category_tab_01','category_tab_02');
		
		for(var i=0; i<area.length; ++i){
			
			if(area[i]==vid){
				document.getElementById(vid).style.display = '';			
				document.getElementById(tab_id).className = 'on';
			}else{			
				document.getElementById(area[i]).style.display = 'none';
				document.getElementById(tab[i]).className = '';
			}
		}
	}

		
	function search_multcategory (){
		
		var search_text = $('#search_category_text').val();
	//console.log('./goods_input.act.php?search_text='+search_text+'&act=search_multcategory');
		if(search_text){
			$.ajax({
			    url : '../product/goods_input.act.php',
			    type : 'POST',
			    data : {search_text:search_text,
						act:'search_multcategory'
						},
			    dataType: 'json',
			    error: function(data,error){// 실패시 실행함수 
			        alert(error);},
			    success: function(args){

					if(args){
						$('select[name=search_category_list]').empty();
						$.each(args, function(index, entry){
							//alert(index);
							$('select[name=search_category_list]').append('<option value='+index+'>'+entry+'</option>');
						});
					}else{
						alert('검색한 분류가 없습니다.');
					}
	        	}
	    	});
		}else{
			alert('검색어를 입력해 주세요.');
		}

	}

	function thisCategorySave(frm,vMode)
	{
		//alert(frm);
		
		if (frm.this_category.value.length < 1){
			alert('수정/삭제 하시고자 하는 표준상품분류를 선택해 주세요');
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
			alert('추가 하시고자 하는 표준상품분류를 선택해 주세요');
			return false;	
		}

		if (frm.cid.value.length != 15){
			alert('추가 하시고자 하는 표준상품분류를 선택해 주세요');
			return false;	
		}

		
		
		if (frm.sub_category.value.length < 1){
			alert('추가 하시고자 하는 표준상품분류를 입력해 주세요');
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
		var area = new Array('edit_category','add_subcategory','input_addfield');
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
		//alert(document.thisCategoryform.cid.value); 
		document.thisCategoryform.selected_cid.value = cid;
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
		
		
		document.getElementById('calcufrm').src='standard_calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...

		$.ajax({
		    url : './standard_category.act.php',
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
				
				$.ajax({
				    url : './standard_category.act.php',
				    type : 'POST',
				    data : {cid:cid,
							depth:depth,
							act:'get_category_relation_infos'
							},
				    dataType: 'json',
				    error: function(data,error){// 실패시 실행함수 
				        alert(error);},
				    success: function(datas){
						$('table[id=objCategory]').each(function(){ 
								$(this).find('tr').remove(); 
						});
						var obj = $('#objCategory');
		
						if(datas != null){
							$.each(datas, function(i, data){ 
								if(obj.find('tr').length == 0){
									var input_str = \"<input type=radio name=basic id='basic_\"+ data.cid + \"' value='\"+ data.cid + \"' validation=true title='기본카테고리' checked>\";	
								}else{
									var input_str = \"<input type=radio name=basic id='basic_\"+ data.cid + \"' value='\"+ data.cid + \"' validation=true title='기본카테고리' >\";
								}

								obj.append(\"<tr id='num_tr' height=30 class=''><td><input type=text name=display_category[] id='_category' value='\" + data.cid + \"' style='display:none'></td><td id='currPosition'>\"+input_str+\"</td><td><label for='basic_\"+data.cid+\"'>\"+data.category_path+\"</label></td><td><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\\\" $(this).closest('tr').remove();\\\" style='cursor:point;'></td></tr>\");
								
							});  
						}
						
			        }
			    });
				
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
		$P->strLeftMenu = Stat_munu("standard_category.php");
		$P->strContents = $Contents;
		$P->Navigation = "상품관리 > 표준분류설정";
		$P->NaviTitle = "표준분류설정";
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		
		$P->addScript = $addScript; /**/
		$P->OnloadFunction = ""; //showSubMenuLayer('storeleft'); MenuHidden(false);
		$P->title = "";//text_button('#', " ♣ 분류 구성");
		$P->strLeftMenu = product_menu();


		$P->strContents = $Contents;
		$P->Navigation = "상품관리 > 상품분류관리 > 표준분류설정";
		$P->title = "표준분류설정";
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
	$mstring =  "		
	var groupnode$mcid = new TreeNode('$cname ',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
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



function PrintRelation($relation_cid){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth,r.basic, r.rid, r.regdate  from logstory_category_relation r, standard_category_info c where relation_cid = '".$relation_cid."' and c.cid = r.cid ORDER BY r.regdate ASC ";
	

	$db->query($sql);

	$mString = "<table width=100% cellpadding=0 cellspacing=0 id=objCategory>
						";

	if ($db->total == 0){
		//$mString = $mString."<tr bgcolor=#ffffff height=45><td colspan=5 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='display_category[]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<td class='table_td_white small' width='50'><input type='radio' name='basic' id='basic_".$db->dt[cid]."' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."  validation=true title='기본카테고리' ></td>
				<td class='table_td_white small ' width='*'><label for='basic_".$db->dt[cid]."' >".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</label></td>
				<td class='table_td_white' width='100'><!--a href=\"JavaScript:void(0)\" onClick='category_del(this.parentNode.parentNode)'--><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\" $(this).closest('tr').remove();\" style='cursor:point;'><!--/a--></td>
				</tr>";//onClick='category_del(this.parentNode.parentNode)' 를 onClick='category_del(true,this.parentNode.parentNode)' 로 변경 kbk 13/06/30<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(true,this.parentNode.parentNode)' style='cursor:pointer;' />
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";

	return $mString;
}

?>