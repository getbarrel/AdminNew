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


/*	Create Root node	*/
	var rootnode = new TreeNode(\"상품분류\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('상품분류','000000000000000',-1,'')\";
	rootnode.expanded = true;
";


$db->query("SELECT * FROM co_product_category_info where depth in(0,1,2,3)  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");


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

		<table cellpadding=0 cellspacing=0 border=0 width='100%'>

		<tr>
		    <td align='left' colspan=3> ".GetTitleNavigation("상품분류설정", "상품관리 > 상품분류설정")."</td>
		</tr>
		<tr>
			<td valign=top width=236>
			<div id=TREE_BAR >
				<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 width=100% style='border:3px solid #d8d8d8'>
				<tr><form name='category_order' method='get' action='categoryorder.php' target='calcufrm'>
					<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle nowrap>
					<input type='hidden' name='this_depth' value=''>
					<input type='hidden' name='cid' value=''>
					<input type='hidden' name='mode' value=''>
					<input type='hidden' name='view' value=''><!--innerview-->
					<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
					<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cursor:hand' alt='분류 아래로 이동' align=absmiddle>
					</td>
					<td width=190 valign=middle>
					<span class=small><!--분류선택후 이동버튼 클릭--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
					</td>
				</tr></form>
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
			<td width='*' style='padding:5px;'>

			</td>
			<td valign=top width='100%' align='left' style=''>
				<div class='tab' style='width:100%;height:38px;margin:0px;'>
					<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('edit_category','tab_01')\" style='padding-left:20px;padding-right:20px;'>
									<!--input type='radio' name='category_mode' id='category_mode_edit' value='edit' onclick='CategoryMode(this.value)' checked><label for='category_mode_edit' style='font-weight:bold'>선택된 분류 수정</label-->
									선택된 분류 수정
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('add_subcategory','tab_02')\" style='padding-left:20px;padding-right:20px;'>
									<!--input type='radio' name='category_mode' id='category_mode_add' value='add' onclick='CategoryMode(this.value)' ><label for='category_mode_add' style='font-weight:bold'> 분류 추가</label-->
									분류 추가
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<!--table id='tab_03' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('input_addfield','tab_03')\">분류별 부가정보 필드 정의</td>
								<th class='box_03'></th>
							</tr>
							</table-->
						</td>
						<td class='btn'>

						</td>
					</tr>
					</table>
				</div>



				<div id='edit_category' style='display:block;'>
				<form name='thisCategoryform' method=\"post\" enctype='multipart/form-data' action='category.save.php' target='calcufrm' style='display:inline;'>
				<input type='hidden' name='mode' value=''>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='category_top_view' value=''>
				<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'><b>선택된 분류 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap >
					<input type='text' class='textbox' name='this_category' maxlength=40 validation=true title='선택된 분류'>
					<input type='checkbox' name='category_use' id='category_use_id' value=1 ><label for='category_use_id'> 분류사용</label>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 	<b>선택된 분류 링크 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' id='category_link' nowrap>
						<span style='color:red;' class=small><!--왼쪽 분류에서 분류를 선택해주세요-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류 타입 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						<input type='radio' name='category_display_type' id='category_display_type_text' value='T' ><label for='category_display_type_text'> text 사용</label>
						<input type='radio' name='category_display_type' id='category_display_type_image' value='I' ><label for='category_display_type_image'> 이미지 사용</label>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td class='input_box_title'>분류이미지 추가</td>
					<td class='input_box_item' style='padding:5px 0px 1px 10px;'>
					<input type='file' class=textbox name='category_img' style='padding-bottom:4px;'> 분류명(메뉴)으로 사용할 이미지 <input type='checkbox' name='ch_category_img' id='ch_category_img' value='Y' /> <label for='ch_category_img'>삭제</label>
					<div id='category_img_area' style='padding:5px 0px 0px 10px;'></div>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap> 	좌측분류이미지 추가</td>
					<td class='input_box_item' style='padding:5px 0px 1px 10px;'>
					<input type='file' class=textbox name='leftcategory_img' style='padding-bottom:4px;'> 분류명(메뉴)으로 사용할 이미지 <input type='checkbox' name='ch_leftcategory_img' id='ch_leftcategory_img' value='Y' /> <label for='ch_leftcategory_img'>삭제</label>
					<div id='leftcategory_img_area' style='padding:5px 0px 0px 10px;'></div>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 	서브이미지 추가(이미지)</td>
					<td class='input_box_item' style='padding:5px 0px 1px 10px;'>
					<input type='file' class=textbox name='sub_img' style='padding-bottom:4px;'> 해당 분류의 메인페이지 상단에 노출되는 이미지 <input type='checkbox' name='ch_sub_img' id='ch_sub_img' value='Y' /> <label for='ch_sub_img'>삭제</label>
					<div id='sub_img_area' style='width:400px;padding:5px 0px 0px 10px;'></div>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap> 	서브이미지 추가(HTML)</td>
					<td class='input_box_item' style='padding-left: 0px;'>".WebEdit()."</td>
				</tr>
				</table>
				<table cellpadding=5 cellspacing=1 width=100% >
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right valign=top style='padding:0px;padding-right:20px;'>
					<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
          			      <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
					</td>
				</tr>

				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> ";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$Contents .= "		<input type='checkbox' name='sub_cartegory_delete' id='sub_cartegory_delete_id'value='1' > <label for='sub_cartegory_delete_id'>하부분류 모두삭제</label>
					<img src='../images/".$admininfo["language"]."/bt_category_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"CategorySave(document.thisCategoryform,'del');\">";
}
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$Contents .= "
					<img src='../images/".$admininfo["language"]."/bt_category_modify.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"CategorySave(document.thisCategoryform,'modify');\">";
}
$Contents .= "
					</td>
				</tr>
				</table>
				</form>


				</div>

				<div id='add_subcategory' style='display:none;'>
				<form name='subCategoryform' method=\"post\" enctype='multipart/form-data' action='category.save.php' target='calcufrm' style='display:inline;'>
				<input type='hidden' name='mode' value=''>
				<input type='hidden' name='sub_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_cid' value=''>

				<table cellpadding=0 cellspacing=0 border=0 width=97% class='input_table_box'>
				<tr>
					<td width=170 class='input_box_title' nowrap>  <b>선택된 분류 <img src='".$required3_path."'></b></td>
					<td width=* class='input_box_item' id='selected_category'  nowrap>미선택 --> <!--왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</td>
				</tr>
				<tr >
					<td class='input_box_title' nowrap>  <b>하부분류 추가 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type='text' name='sub_category' class='textbox'  maxlength=40 validation=true title='하부분류'>
							</td>
							<td><input type='checkbox' name='category_use' value=1></td>
							<td><span class=small> 분류사용</span></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류 타입 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						<input type='radio' name='category_display_type' id='category_display_type_text' value='T' ><label for='category_display_type_text'> text 사용</label>
						<input type='radio' name='category_display_type' id='category_display_type_image' value='I' ><label for='category_display_type_image'> 이미지 사용</label>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td class='input_box_title'>  분류이미지 추가</td>
					<td class='input_box_item'><input type='file' class='textbox' name='category_img'></td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap>  좌측 분류이미지 추가</td>
					<td class='input_box_item'><input type='file' class='textbox' name='leftcategory_img'></td>
				</tr>
				<tr>
					<td class='input_box_title'>  서브이미지 추가</td>
					<td class='input_box_item'><input type='file'class='textbox'  name='sub_img'></td>
				</tr>
				</table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$Contents .= "
				<table cellpadding=5 cellspacing=1 width=97% >
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> <img src='../images/".$admininfo["language"]."/bt_category_add.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"SubCategorySave(document.subCategoryform,'insert');\"></td>
				</tr>
				</table>";
}
$Contents .= "
				</form>
				</div>
				<div class='doong'  id='input_addfield' style='display:none;'>
					<form name='add_field' method=\"post\" enctype='multipart/form-data' action='category.save.php' onsubmit='return SaveAddField(this);' target='calcufrm'>
					<input type='hidden' name='mode' value='add_field'>
					<input type='hidden' name='cid' value=''>
					<table cellpadding=5 cellspacing=0 border=0 width='97%'>
						<tr>
							<td colspan=2 width=120 nowrap><b>선택된 분류</b> </td>
							<td colspan=5 width='270' id='category_name' style='font-weight:bold;' class='small'>
							미선택 --> 왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요
							</td>
						</tr>
						<tr bgcolor=#efefef>
							<td width=100 nowrap>구분</td>
							<td width='60' nowrap>검색표시 <td>
							<td width='100' >필드이름 <td>
							<td width=100 nowrap>필드타입</td>
							<td width='80%'  nowrap>
								필드기본값 예) 1024*768|1280*800|1600*1200
							</td>
						</tr>
						<tr>
							<td >기타정보 1</td>
							<td ><input type='checkbox' name='etc1_search' value='1'>  <td>
							<td ><input type='text' name='etc1' value=''>  <td>
							<td nowrap>
								<select name='etc1_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td width='80%'  nowrap>
								<input type='text' name='etc1_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td nowrap>기타정보 2</td>
							<td ><input type='checkbox' name='etc2_search' value='1'>  <td>
							<td ><input type='text' name='etc2' value=''><td>
							<td nowrap>
								<select name='etc2_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td nowrap>
								<input type='text' name='etc2_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td nowrap>기타정보 3</td>
							<td ><input type='checkbox' name='etc3_search' value='1'>  <td>
							<td ><input type='text' name='etc3' value=''><td>
							<td nowrap>
								<select name='etc3_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td nowrap>
								<input type='text' name='etc3_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td nowrap>기타정보 4</td>
							<td ><input type='checkbox' name='etc4_search' value='1'>  <td>
							<td ><input type='text' name='etc4' value=''><td>
							<td nowrap>
								<select name='etc4_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td nowrap>
								<input type='text' name='etc4_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td nowrap>기타정보 5</td>
							<td ><input type='checkbox' name='etc5_search' value='1'>  <td>
							<td ><input type='text' name='etc5' value=''><td>
							<td nowrap>
								<select name='etc5_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td nowrap>
								<input type='text' name='etc5_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td nowrap>기타정보 6</td>
							<td ><input type='checkbox' name='etc6_search' value='1'>  <td>
							<td ><input type='text' name='etc6' value=''><td>
							<td nowrap>
								<select name='etc6_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td nowrap>
								<input type='text' name='etc6_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td nowrap>기타정보 7</td>
							<td ><input type='checkbox' name='etc7_search' value='1'>  <td>
							<td ><input type='text' name='etc7' value=''><td>
							<td nowrap>
								<select name='etc7_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td nowrap>
								<input type='text' name='etc7_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td nowrap>기타정보 8</td>
							<td ><input type='checkbox' name='etc8_search' value='1'>  <td>
							<td ><input type='text' name='etc8' value=''><td>
							<td nowrap>
								<select name='etc8_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td nowrap>
								<input type='text' name='etc8_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td nowrap>기타정보 9</td>
							<td ><input type='checkbox' name='etc9_search' value='1'>  <td>
							<td ><input type='text' name='etc9' value=''><td>
							<td nowrap>
								<select name='etc9_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td nowrap>
								<input type='text' name='etc9_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td nowrap>기타정보 10</td>
							<td ><input type='checkbox' name='etc10_search' value='1'>  <td>
							<td ><input type='text' name='etc10' value=''><td>
							<td nowrap>
								<select name='etc10_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td nowrap>
								<input type='text' name='etc10_value' size=50 value=''>
							</td>
						</tr>
						<tr>
							<td colspan=2 align=right> <input type=image src='../image/b_save.gif' border=0 align=absmiddle ></td>
						</tr>
					</table>
				</form>
				</div>
			</td>
		</tr>
		";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle ><img src='/admin/image/icon_list.gif' b></td><td class='small' style='line-height:120%'>분류 디자인을 텍스트 또는 이미지로 하실수 있습니다. 이미지 등록후 예시와 같이 치환코드를 변경해주시면 됩니다 <span class=small>예) text 사용시 : {categorys.cname} , image 사용시 : {categorys.leftcatimg}</span></td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' style='line-height:120%' >상단에 <label for='category_mode_add1' style='font-weight:bold'> 분류 추가</label> 를 선택하신후 좌측 상품 분류에서 추가하기 원하는 분류를  선택하신후  <b>분류 추가</b> 입력란에 분류를 입력하신후 <img src='../image/bt_category_add.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>분류 수정</b> : 상단에 <label for='category_mode_add1' style='font-weight:bold'> 선택된 분류 수정 </label> 를 선택하신후 좌측 상품 분류에서 추가하기 원하는 분류를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>분류 삭제</b> : 상단에 <label for='category_mode_add1' style='font-weight:bold'> 선택된 분류 수정 </label> 를 선택하신후 좌측 상품 분류에서 추가하기 원하는 분류를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' > 하부분류를 포함해서 삭제하고 싶으신경우에는 <input type='checkbox' name='sub_cartegory_delete1' id='sub_cartegory_delete_id1'value='1' > <label for='sub_cartegory_delete_id1' style='font-weight:bold'>하부분류 모두삭제</label> 를 선택한후 <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= "
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
	<script language='JavaScript' src='../include/manager.js'></script>
	<script language='JavaScript' src='../include/cTree.js'></script>
	<script language='JavaScript' src='category.js'></script>
	<script src='../include/rightmenu.js'></script>\n".$Script;
	$P->addScript = $addScript; /**/
	$P->OnloadFunction = "";
	$P->title = "";//text_button('#', " ♣ 분류 구성");
	$P->strLeftMenu = "";
	$Contents ="
		<div id='category_area'>
			<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
				<tr><form name='category_order' method='get' action='categoryorder.php' target='calcufrm'>
					<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle>
					<input type='hidden' name='this_depth' value=''>
					<input type='hidden' name='cid' value=''>
					<input type='hidden' name='mode' value=''>
					<input type='hidden' name='view' value='innerview'>
					<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
					<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cursor:hand' alt='분류 아래로 이동' align=absmiddle>
					<span class=small>분류선택후 이동버튼 클릭</span>
					</td>
				</tr></form>
				<tr><form>
					<td width=200 height=400 valign=top style='overflow:auto;padding:0 10 10 10;'>
					<div style=\"width:200px;height:420px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
					$category
					</div>
					</td>
				</tr></form>
				</table>
		</div>

		<script>alert(document.getElementById('category_area').innerHTML);parent.document.getElementById('TREE_BAR').innerHTML = document.getElementById('category_area').innerHTML;</script>";

	$P->strContents = $Contents;
	$P->Navigation = "";
	$P->PrintLayOut();
	exit;
}else{

$P = new LayOut;
$addScript = "
<script language='JavaScript' src='../webedit/webedit.js'></script>
<script language='JavaScript' src='../include/manager.js'></script>
<script language='JavaScript' src='../include/cTree.js'></script>
<script language='JavaScript' src='category.js'></script>
<script src='../include/rightmenu.js'></script>\n".$Script;
$P->addScript = $addScript; /**/
$P->OnloadFunction = "Init(document.thisCategoryform);MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')"; //showSubMenuLayer('storeleft'); MenuHidden(false);
$P->title = "";//text_button('#', " ♣ 분류 구성");
$P->strLeftMenu = cogoods_menu();


$P->strContents = $Contents;
$P->Navigation = "공유상품관리 > 공유서버 상품분류설정";
$P->title = "상품분류설정";
$P->PrintLayOut();
}
/*
echo "
<SCRIPT type=text/javascript>
<!--//
// 메뉴의 좌측에 들어갈 타이틀을 입력하세요
eyesys_title='LCS'
// 타이틀에 그라데이션 효과를 보여줄 배경색을 설정 하세요
eyesys_titlecol1='white' // 아래쪽 색상
eyesys_titlecol2='white' // 위쪽 색상
// 타이틀의 글자 색상
eyesys_titletext='gray'
// 메뉴와 각 항목의 배경색
eyesys_bg='#ffffff'
// 마우스를 대었을때의 배경색
eyesys_bgov='#006699'
// 메뉴의 색상
eyesys_cl='#000000'
// 마우스 오버시 메뉴 색상
eyesys_clov='white'
// 메뉴의 가로크기
eyesys_width=123
eyesys_init()

// 아래 방법으로 메뉴를 설정 합니다
// eyesys_item(제목,아이콘,URL)
// 아이콘을 사용하지 않으려면 null 을 입력 합니다
//Eyesys_item('Copy (ctrl+C)',null,'JavaScript:parent.CopySpread();parent.sysmen.hide();');
//Eyesys_item('Paste (ctrl+v)',null,'JavaScript:parent.PasteSpread();parent.sysmen.hide();');
//Eyesys_item('Erase (delete)',null,'javascript:parent.EraseSpread();parent.sysmen.hide();');
//Eyesys_item('Delete line',null,'');
//Eyesys_item('All clear',null,'');


eyesys_close()

//document.write ('<pre>'+strContextMenu+'</pre>');

//-->
</SCRIPT>";



$mstring ="
	<html>
	<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
	<body>
	<div id='category_top_view_area'>
	".$db->dt[category_top_view]."
	</div>
	</body>
	</html>
	<script>
	parent.document.forms['thisCategoryform'].category_use.checked = $category_use;
	parent.iView.document.body.innerHTML = document.getElementById('category_top_view_area').innerHTML;
	</script>";
*/
//echo $db->total;
function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($mdb){

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
	$cname = str_replace("'","&#39;",$cname);

//	if ($cid == $mcid){
//		$expandstring = "true";
//	}else{
//		$expandstring = "false";
//	}

	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type')\";
	rootnode.addNode(node$cid);\n\n";
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
	$cname = str_replace("'","&#39;",$cname);

	$mstring =  "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	if ($mcid == $cid || $mcid == substr($cid,0,6)."000000000"){
		$mstring .=  "	groupnode$mcid.expanded = true;\n";
	//	$mstring .=  "	groupnode$cid.select = true;\n";
	}


	$mstring .=  "	groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth, '$category_display_type')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";

	return $mstring;
}


/*
CREATE TABLE IF NOT EXISTS `co_product_category_info` (
  `cid` varchar(15) NOT NULL COMMENT '카테고리 코드',
  `depth` smallint(1) unsigned default NULL COMMENT '카테고리 깊이',
  `vlevel1` int(3) default NULL COMMENT '깊이0 정렬값',
  `vlevel2` int(3) default NULL COMMENT '깊이1 정렬값',
  `vlevel3` int(3) default NULL COMMENT '깊이2 정렬값',
  `vlevel4` int(3) default NULL COMMENT '깊이3 정렬값',
  `vlevel5` int(3) default NULL COMMENT '깊이4 정렬값',
  `cname` varchar(40) default NULL COMMENT '카테고리명',
  `catimg` varchar(100) default NULL COMMENT '카테고리 이미지',
  `leftcatimg` varchar(100) default NULL COMMENT '좌측 카테고리 이미지',
  `subimg` varchar(100) default NULL COMMENT '서브 이미지',
  `category_top_view` mediumtext COMMENT '상단 카테고리 표시여부',
  `category_display_type` char(1) default 'T' COMMENT '카테고리 표시 유형 (T:텍스트, I:이미지)',
  `category_use` char(1) default '1' COMMENT '카테고리 사용 여부',
  `regdate` date default NULL COMMENT '등록일',
  PRIMARY KEY  (`cid`),
  KEY `IDX_MCI_DEPTH` (`depth`),
  KEY `category_use` (`category_use`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='공유상품 분류정보';

*/

?>