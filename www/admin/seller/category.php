<?
include("../class/layout.class");
//include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/menu.tmp.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/menuline.class");

$db = new Database;

$language_list = getTranslationType("","","array");

$category = "
<script  id='dynamic'></script>
<script>
/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;


/*	Create Root node	
	setCategory(cname,cid,depth, category_display_type,category_use,	category_access,category_code,cname_on,is_adult)
*/
	var rootnode = new TreeNode(\"상품분류\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('상품분류','000000000000000',-1,'','','','','상품분류','0','0')\";
	rootnode.expanded = true;
";

$db->query("SELECT * FROM shop_minishop_category_info where depth in(0,1,2,3,4) and company_id = '".$_SESSION["admininfo"]['company_id']."' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

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
<script language='JavaScript' src='category.js'></script>
<script language='JavaScript' src='../include/manager.js'></script>
<script type='text/javascript' src='../colorpicker/farbtastic.js'></script>
<link rel='stylesheet' href='../colorpicker/farbtastic.css' type='text/css' />

		<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
		    <td align='left' colspan=3> ".GetTitleNavigation("상품분류설정", "상품관리 > 상품분류설정")."</td>
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
									<!--input type='radio' name='category_mode' id='category_mode_edit' value='edit' onclick='CategoryMode(this.value)' checked><label for='category_mode_edit' style='font-weight:bold'>선택된 분류 수정</label-->
									분류 수정
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<!--table id='tab_03'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('input_design_subcategory','tab_03')\" style='padding-left:20px;padding-right:20px;'>
									<input type='radio' name='category_mode' id='category_mode_add' value='add' onclick='CategoryMode(this.value)' ><label for='category_mode_add' style='font-weight:bold '> 분류 추가</label>
									분류 디자인
								</td>
								<th class='box_03'></th>
							</tr>
							</table-->
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
							<table id='tab_04' style='display:none;'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('add_person','tab_04')\" style='padding-left:20px;padding-right:20px;'>
									분류MD 설정
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td class='btn'>
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
										<form name='category_order' id ='category_order' method='get' action='categoryorder.php' target=''>
										<input type='hidden' name='this_depth' value=''>
										<input type='hidden' name='cid' value=''>
										<input type='hidden' name='mode' value=''>
										<input type='hidden' name='view' value=''><!--innerview-->
										<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
										<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cur sor:hand' alt='분류 아래로 이동' align=absmiddle>
										<span class=small><!--분류선택후 이동버튼 클릭--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
										</form>
									</td>
								</tr>
								<tr>
									<form>
									<td colspan=2 width=200 height=400 valign=top style='overflow:auto;padding:0 10px 10px 10px;'>
									<div style=\"width:200px;height:418px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
									$category
									</div>
									</td>
									</form>
								</tr>
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
				<form name='thisCategoryform' id='thisCategoryform' method=\"post\" enctype='multipart/form-data' action='category.save.php' target='' style='display:inline;'><!--target=''-->
				<input type='hidden' name='mode' value='modify'>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_mode' value='edit_category'>
				<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
				<col width='18%'>
				<col width='32%'>
				<col width='18%'>
				<col width='32%'>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>위치 <img src='".$required3_path."'></b></td>
					<td width=* class='input_box_item'  nowrap colspan='3'>
						<div id='selected_category_1' >미선택   ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</div>
					</td>
				</tr>

				<tr bgcolor=#ffffff height=130px>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류명 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						";

									$Contents .= "<table>";
									$Contents .= "<col width=70px><col width=*>";
									$Contents .= "<tr height=28><td>분류명</td><td><input type='text' class='textbox' name='this_category' maxlength=40 validation=true title='선택된 분류'></td></tr>";
									if(is_array($language_list)){
									foreach($language_list as $key => $li){
										if ($key != 0) $Contents .= " ";

										$Contents .= "<tr height=28><td>".$li[language_name]." </td><td> <input type=text class='textbox' name=\"global_this_category[".$li[language_code]."]\" id='global_this_category_".$li[language_code]."' maxlength=40 title='글로벌 카테고리(".$li[language_name].")' value=''></td></tr>";
									}
									}


									$Contents .= "</table>


					</td>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류코드 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap >
						<input type='text' class='textbox' name='category_code' id='category_code' maxlength=40 validation=true title='분류코드'>
					</td>
				</tr>
				<tr bgcolor=#ffffff style='display:none;'>
					<td class='input_box_title' > <b>HSCODE NO.</b> </td> 
					 <td class='input_box_item' colspan=3><input type=text class='textbox' name='hscode' value='' style='width:150px;' validation=false title='HSCODE'> </td>
				  </tr>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류 타입 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='radio' name='category_type' id='category_type_c' value='C' checked onclick=\"$('#category_display_link').show();$('#category_link_box').hide();\"><label for='category_type_c'> 카테고리</label>
						<input type='radio' name='category_type' id='category_type_m' value='M' onclick=\"$('#category_display_link').hide();$('#category_link_box').show();\"><label for='category_type_m'> 메뉴</label> * 메뉴는 카테고리로 노출되지 않고 단독 사용할수 있음
					</td>
				</tr>

				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'><b>분류 사용 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap  colspan='3' style='padding:5px;'>
						<input type='radio' name='category_use' id='category_use_id' value=1 checked /><label for='category_use_id'> 사용</label>&nbsp;
						<input type='radio' name='category_use' id='category_use_id_0' value=0 /><label for='category_use_id_0'> 미사용</label>&nbsp;
						<input type='radio' name='category_use' id='category_use_id_2' value=2 /><label for='category_use_id_2'> 숨김카테고리</label><br>
						<div id='cateory_text' style='padding-top:5px;'>
						<span style='font:5pt;'> * 미사용시 활성화가 되지않아 프론트에 노출되지 않음</span>
						</div>
					</td>
				</tr>
				<tr style='display:none;'>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;' ><b>19금 설정 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap  colspan='3' style='padding-left:5px;'>
						<input type='radio' name='is_adult' id='is_adult_0' value='0' checked /><label for='is_adult_0'> 미설정</label>&nbsp;
						<input type='radio' name='is_adult' id='is_adult_1' value='1' ><label for='is_adult_1'> 설정</label>&nbsp;
						<span style='font:5pt;'> * 19금 설정시 생년월일을 통한 만 19세이상만 사용가능함</span>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'><b>카테고리 관리적용 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap  colspan='3' style='padding-left:5px;'>
						<input type='radio' name='is_layout_apply' id='is_layout_apply_n' value='N' checked /><label for='is_layout_apply_n'> 미설정</label>&nbsp;
						<input type='radio' name='is_layout_apply' id='is_layout_apply_y' value='Y' ><label for='is_layout_apply_y'> 설정</label>&nbsp;
						<!--<span style='font:5pt;'> * 19금 설정시 생년월일을 통한 만 19세이상만 사용가능함</span>-->
					</td>
				</tr>
				</table><br>
				
				<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
				<col width='18%'>
				<col width='*'>
				<tr style='display:none;'>
					<td class='input_box_title'> 	<b>분류 링크 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap >
						<div id='category_display_link'  ><span style='color:red;' class=small><!--왼쪽 분류에서 분류를 선택해주세요-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span></div>
						<div id='category_link_box' style='display:none;'><input type='text' class='textbox' size=100 name='category_link' id='category_link' validation=false title='메뉴링크'></div>
					</td>
				</tr>
				<tr  height=450 style='display:none;'>
					<td class='input_box_title'><b>회원그룹 설정</b><br>(카테고리 접근권한 설정)</td>
					<td class='input_box_item' nowrap >
						<table width='60%' border='0' align='left'>
						<tr>
						<td height='20'>
						<span><input type='radio' name='category_access' id='category_access_d' value=D checked /><label for='category_access_d'> 전체 접근 가능</label></span>
							</td>
						</tr>

						<tr height='20'>
						<td>
						<span><input type='radio' name='category_access' id='category_access_m' value=M ><label for='category_access_m'> 회원만 접근 가능</label></span>
							</td>
						</tr>
						<tr  height='20'>
							<td>
							<span><input type='radio' name='category_access' id='category_access_g' value=G ><label for='category_access_g'> 회원 그룹별 관리</label></span>
							</td>
						</tr>
						<tr>
							<td width='300' style='padding-left:20px;'>
								<table width='100%' border='0'>

								<tr>
									<td>
										<table width='100%' border='0' align='center'>
										<tr>
											<td colspan='2'>
												<select name='vip_delete[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;' class='participation' id='participation'  multiple>
												";
												$sql = "select * from shop_groupinfo where 1 order by gp_ix ASC";
												$db->query($sql);

												$group_array = $db->fetchall();
												for($i = 0; $i<count($group_array); $i++){
													$Contents .="<option value='".$group_array[$i][gp_ix]."'>".$group_array[$i][gp_name]."</option>";
												}
												$Contents .="
												</select>
											</td>
										</tr>
										</table>
									</td>
									<td align='center'>
										<div class='float01 email_btns01'>
											<ul>
												<li>
													<a href=\"javascript:CateGoryMoveSelectBox('ADD','4');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
												</li>
												<li>
													<a href=\"javascript:CateGoryMoveSelectBox('REMOVE','4');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
												</li>
											</ul>
										</div>
									</td>
									<td width='300'>
										<table width='100%' border='0' align='center'>
										<tr>
											<td colspan='2'>
												<select name='group_list[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;' id='selected' validation=false title='회원 그룹 대상' multiple>
												
												</select>
											</td>
										</tr>
										</table>
									</td>
									<tr>
								</table>
							</td>
						</tr>
						<tr height='30'>
							<td>
							<span><input type='radio' name='category_access' id='category_access_md' value=U ><label for='category_access_md'> 특정회원 등록 관리</label></span>
							</td>
						</tr>
						<tr>
							<td width='300' style='padding-left:20px;'>
									<table width='100%' border='0'>
									<tr >
										<td style='width:100%;padding:0px;'>
											<table width='100%' border='0'>
											<tr>
											<td align='right' width='*'>
												<input type='text' name='search_text' id='search_text' class='textbox' style='width:195px;' >
												<img src='../v3/images/korea/btn_search.gif' align='absmiddle' id='member_search' style='cursor:pointer;'>
											</td>
											<td align='right'  width='22%'>
												<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('#participation_1 option'),'selected')\" style='cursor:pointer;'/>
											</td>
										</td>
									</tr>
									</table>
								</tr>
								<tr>
									<td>
										<table width='100%' border='0' align='center'>
										<tr>
											<td colspan='2'>
												<select name='vip_delete[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;' class='participation_1' id='participation_1'  multiple>
												
												</select>
											</td>
										</tr>
										</table>
									</td>
									<td align='center'>
										<div class='float01 email_btns01'>
											<ul>
												<li>
													<a href=\"javascript:CateGoryMoveSelectBox('ADD','5');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
												</li>
												<li>
													<a href=\"javascript:CateGoryMoveSelectBox('REMOVE','5');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
												</li>
											</ul>
										</div>
									</td>
									<td width='300'>
										<table width='100%' border='0' align='center'>
										<tr>
											<td colspan='2'>
												<select name='md_list[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;' id='selected_1' validation=false title='MD관리자 대상' multiple>
												
												</select>
											</td>
										</tr>
										</table>
									</td>
									<tr>
								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
				<br>
				<table cellpadding=0 cellspacing=0 width=100% style='padding-top:20px'>
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> ";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D") || true){
$Contents .= "		<input type='checkbox' name='sub_cartegory_delete' id='sub_cartegory_delete_id'value='1' > <label for='sub_cartegory_delete_id'>하부분류 모두삭제</label>
					<img src='../images/".$admininfo["language"]."/bt_category_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"category_del(document.thisCategoryform);\">
					<script>
					function category_del(frm){

						if(parseInt($('#product_total_cnt').text().replace(/[^0-9]/g,'')) > 0){
							alert('카테고리에 등록된 상품이 있습니다. 이동 또는 삭제 후 다시시도해주세요.');
							return false;
						}else{
							var select = confirm(frm.this_category.value + '을(를) 삭제하시겠습니까?');
							if(select){
								CategorySave(frm,'del');
							}else{
								return false;
							}
						}
					}
					</script>
					";
}

$Contents .= "
					<img src='../images/".$admininfo["language"]."/bt_category_modify.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"thisCategorySave(document.thisCategoryform,'modify');\">";

$Contents .= "
					</td>
				</tr>
				</table>
				</form>
				</div>";

$Contents .= "
				<div id='add_subcategory' style='display:none;'>
				<form name='subCategoryform' id='subCategoryform' method=\"post\" enctype='multipart/form-data' action='category.save.php' target='' style='display:inline;'>
				<input type='hidden' name='mode' value='insert'>
				<input type='hidden' name='sub_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_cid' value=''>

				<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box' >
				<col width='20%'>
				<col width='*'>
				<tr>
					<td width=170 class='input_box_title' nowrap>  <b>위치 <img src='".$required3_path."'></b></td>
					<td width=* class='input_box_item'  nowrap colspan='3'>
						<div id='selected_category_3' >미선택 --> <!--왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</div>
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
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>분류 타입 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='radio' name='category_type' id='sub_category_type_c' value='C' onclick=\"$('#sub_category_link_box').hide();\" checked><label for='sub_category_type_c'> 카테고리</label>
						<input type='radio' name='category_type' id='sub_category_type_m' value='M' onclick=\"$('#sub_category_link_box').show();\"><label for='sub_category_type_m'> 메뉴</label>
						&nbsp;<span style='font:5pt; padding=5px;'> * 메뉴는 카테고리로 노출되지 않고 단독 사용할수 있음</span>
					</td>
				</tr>
				<tr id='sub_category_link_box' style='display:none;'>
					<td class='input_box_title'> 	<b>선택된 분류 링크 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap colspan='3'>
						<input type='text' class='textbox' size=100 name='category_link' id='category_link' validation=false title='메뉴링크'>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'><b>분류 사용 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap  colspan='3' style='padding:5px;'>
						<input type='radio' name='category_use' id='category_use_id_' value=1 checked><label for='category_use_id_'> 사용</label>&nbsp;
						<input type='radio' name='category_use' id='category_use_id_10' value=0 ><label for='category_use_id_10'> 미사용</label>&nbsp;
						<input type='radio' name='category_use' id='category_use_id_12' value=2 ><label for='category_use_id_12'> 숨김카테고리</label><br>
						<div id='cateory_text' style='padding-top:5px;'>
						<span style='font:5pt;'> * 미사용시 활성화가 되지않아 프론트에 노출되지 않음</span>
						</div>
					</td>
				</tr>
				</table>";

$Contents .= "
				<table cellpadding=5 cellspacing=1 width=97% >
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> <img src='../images/".$admininfo["language"]."/bt_category_add.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"SubCategorySave(document.subCategoryform,'insert');\"></td>
				</tr>
				</table>";

$Contents .= "
				</form>
				</div>";


$Contents .= "
				<div class='doong'  id='input_addfield' style='display:none;'>
					<form name='add_field' method=\"post\" enctype='multipart/form-data' action='category.save.php' onsubmit='return SaveAddField(this);' target=''>
					<input type='hidden' name='mode' value='add_field'>
					<input type='hidden' name='cid' value=''>
					<input type='hidden' name='sub_mode' value='add_field'>
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
							<td ><input type='checkbox' name='etc2_search' value='1'><td>
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
				</div>";
$Contents .= "
				<div id='add_person' style='display:none;'>
				<form name='AddPersonForm' id='AddPersonForm' method=\"post\" enctype='multipart/form-data' action='category.save.php' target='' style='display:inline;'><!--target=''-->
				<input type='hidden' name='mode' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='this_category' value=''>
				<input type='hidden' name='sub_mode' value='add_person'>
				<input type='hidden' name='md_use' value='1'>
				<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr>
					<td width=170 class='input_box_title' nowrap>  <b>선택된 분류 <img src='".$required3_path."'></b></td>
					<td width=* class='input_box_item'  nowrap>
						<div id='selected_category_4' >미선택 --> <!--왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</div>
					</td>
				</tr>
				<tr id='sub_category_link_box' style='display:none;'>
					<td class='input_box_title'> 	<b>선택된 분류 링크 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						<input type='text' class='textbox' size=100 name='category_link' id='category_link' validation=false title='메뉴링크'>
					</td>
				</tr>
				<!--
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>담당자 사용 <img src='".$required3_path."'></b></td>
					<td class='input_box_item' nowrap>
						<input type='radio' name='md_use' id='md_use_1' value='1' checked><label for='md_use_1'> 사용</label>
						<input type='radio' name='md_use' id='md_use_0' value='0'><label for='md_use_0'> 미사용</label>
					</td>
				</tr>-->
				</table><br>
				<!--
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<col width='15%'>
				<col width='5%'>
				<col width='*'>
				<tr>
					<td  height='25' style='padding:5px 0px;' align='left' bgcolor=#ffffff>
						<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>부서지정</b>
					</td>
					<td  height='25' style='padding:5px 0px;' align='left' bgcolor=#ffffff>
						<input type='button' name='add_department' value='추가' onclick=\"ShowModalWindow('./search_department.php?group_code=',600,600,'add_department')\" style='cursor:pointer;'>
					</td>
					<td  align='left'>
						<span class='small'> * 매출액 관련해서 부서별 통계에 적용됩니다.</span>
				</tr>
				</table>
				
				<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr>
					<td width=170 class='input_box_title' nowrap><b>부서지정</b></td>
					<td width=* class='input_box_item'  nowrap>
						<div id='selected_category_5' style='padding:10px;'>
						<table width='100%' cellpadding='0' cellspacing='0' id='objDepartment'>
						<colgroup>
							<col width='5'>
							<col width='*'>
							<col width='100'>
						</colgroup>
						<tbody>
							<tr id='num_tr' class=''>
							</tr>
						</tbody>
						</table>
						</div>
					</td>
				</tr>
				</table><br>-->

				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<col width='15%'>
				<col width='5%'>
				<col width='*'>
				<tr>
					<td  height='25' style='padding:5px 0px;' align='left' bgcolor=#ffffff>
						<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>담당자 지정</b>
					</td>
					<td  height='25' style='padding:5px 0px;' align='left' bgcolor=#ffffff>
						<img src='../images/icon/pop_plus_btn.gif' style='cursor:pointer;' alt='추가' title='추가' onclick=\"PoPWindow('./search_md.php?group_code=',600,600,'add_md')\"/>
				
					</td>
					<td align='left'>
						<span class='small'> * 매출액 관련해서 부서별 통계에 적용됩니다.</span>
				</tr>
				</table>
				<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr>
					<td width=170 class='input_box_title' nowrap><b>담당자 지정</b></td>
					<td width=* class='input_box_item'  nowrap>
						<div id='selected_category_6' style='padding:10px;'>
						<table width='100%' cellpadding='0' cellspacing='0' id='objMd'>
						<colgroup>
							<col width='5'>
							<col width='*'>
							<col width='100'>
						</colgroup>
						<tbody>
							<tr id='num_tr' class=''>
							</tr>
						</tbody>
						</table>
						</div>
					</td>
				</tr>
				</table>
	";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$Contents .= "
				<table cellpadding=5 cellspacing=1 width=97% >
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> <img src='../images/".$admininfo["language"]."/bt_category_modify.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"thisCategorySave(document.AddPersonForm,'modify');\"></td>
				</tr>
				</table>";
}

$Contents .= "
				</form>
				</div>";

$Contents .= "
				<div class='doong'  id='input_design_subcategory' style='display:none;'>
				<form name='design_subcategory' method=\"post\" enctype='multipart/form-data' action='category.save.php' target='' onsubmit='return SaveAddField(this);' style='display:inline;'>
				<input type='hidden' name='mode' value='modify'>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_mode' value='design_subcategory'>

				<table cellpadding=5 cellspacing=0 border=0 width='100%'  class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr bgcolor=#ffffff>
					<td class='input_box_title' nowrap oncontextmenu='init2();return false;'> 	<b>위치 <img src='".$required3_path."'></b></td>
					<td width=* class='input_box_item'  nowrap>
						<div id='selected_category_2' >미선택 --> <!--왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</div>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'>기본카테고리 디자인 설정</td>
					<td class='input_box_item' style='padding:5px 5px 5px 5px;'>

						<table cellpadding=5 cellspacing=0 border=0 style='padding-bottom:5px;'>
						<tr>
							<td>
								<input type='radio' name='category_display_type' id='sub_category_display_type_text' checked value='T' /><label for='sub_category_display_type_text'> 텍스트 사용</label>
							</td>
						</tr>
						</table>
						<table cellpadding=5 cellspacing=0 border=0 width='98%'  class='input_table_box' align='right' >
						<col width='20%'>
						<col width='17%'>
						<col width='63%'>
						<tr>
							<td class='input_box_title' >마우스 오버 전 </td>
							<td class='input_box_item' style='padding:0px 10px;'>
								<input type='text' class='textbox'  name='this_category' id='design_this_category' value='' maxlength=40 validation=true title='선택된 분류'>
							</td>
							<td class='input_box_item' >
								<div style='float: left;'>
									<input type='text' id='this_category_color' style='width:50px;' class='textbox' name='this_category_color' value='#000000'  onclick=\"setcolorpicker('colorpicker10','this_category_color')\"/>
								</div>
								<div id='colorpicker10' style='display:none;float: left;'></div>
								<div style='float: left;'>
								".DisplayStyleSetup('cname_style','cname_style')."
								</div>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' >마우스 오버 후 </td>
							<td class='input_box_item' style='padding:0px 10px;'>
								<input type='text' class='textbox' name='this_category_on' id='design_this_category_on' maxlength=40 validation=false title='선택된 분류 마우스오버'>
							</td>
							<td class='input_box_item' >
								<div style='float: left;'>
									<input type='text' id='this_category_on_color' style='width:50px;margin:0px 2px;' class='textbox' name='this_category_on_color' value='#000000'  onclick=\"setcolorpicker('colorpicker20','this_category_on_color')\"/>
								</div>
								<div id='colorpicker20' style='display:none;float: left;'></div>
								<div style='float: left;'>
									".DisplayStyleSetup('cname_on_style','cname_on_style')."
								</div>
							</td>
						</tr>
						</table>

						<table cellpadding=5 cellspacing=0 border=0 style='padding-bottom:5px;'>
						<tr>
							<td>
								<input type='radio' name='category_display_type' id='sub_category_display_type_image' value='I'><label for='sub_category_display_type_image'> 이미지사용</label>
							</td>
						</tr>
						</table>

						<table cellpadding=5 cellspacing=0 border=0 width='98%'  class='input_table_box' align='right' >
						<col width='20%'>
						<col width='80%'>
						<tr>
							<td class='input_box_title'>분류이미지<br>(상단)</td>
							<td class='input_box_item' style='padding:5px 5px 5px 5px;'>
								<table border=0 class='input_table_box' width=100% >
									<col width='20%'>
									<col width='80%'>
									<tr>
										<td class='input_box_title' > 마우스 오버 전 </td>
										<td class='input_box_item' style='padding-top:8px;'>
										<input type='file' class=textbox name='category_img' style='padding-bottom:4px;'> 분류명(상단메뉴)으로 사용할 이미지 <input type='checkbox' name='ch_category_img' id='ch_category_img' value='Y' /> <label for='ch_category_img'>삭제</label>
										<div id='category_img_area' style='padding:5px 0px 5px 10px;'></div>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' > 마우스 오버 후 </td>
										<td class='input_box_item' style='padding-top:8px;'>
										<input type='file' class=textbox name='category_img_on' style='padding-bottom:4px;'> 분류명(상단메뉴)으로 사용할 이미지 <input type='checkbox' name='ch_category_img_on' id='ch_category_img_on' value='Y' /> <label for='ch_category_img_on'>삭제</label>
										<div id='category_img_on_area' style='padding:5px 0px 5px 10px;'></div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap>분류이미지<br>(좌측)</td>
							<td class='input_box_item' style='padding:5px 5px 5px 5px;'>
								<table border=0 class='input_table_box' width=100%>
									<col width='20%'>
									<col width='80%'>
									<tr>
										<td class='input_box_title' > 마우스 오버 전 </td>
										<td class='input_box_item' style='padding-top:8px;'>
										<input type='file' class=textbox name='leftcategory_img' style='padding-bottom:4px;'> 분류명(좌측메뉴)으로 사용할 이미지 <input type='checkbox' name='ch_leftcategory_img' id='ch_leftcategory_img' value='Y' /> <label for='ch_leftcategory_img'>삭제</label>
										<div id='leftcategory_img_area' style='padding:5px 0px 5px 10px;'></div>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' > 마우스 오버 후 </td>
										<td class='input_box_item' style='padding-top:8px;'>
										<input type='file' class=textbox name='leftcategory_img_on' style='padding-bottom:4px;'> 분류명(좌측메뉴)으로 사용할 이미지 <input type='checkbox' name='ch_leftcategory_img_on' id='ch_leftcategory_img_on' value='Y' /> <label for='ch_leftcategory_img_on'>삭제</label>
										<div id='leftcategory_img_on_area' style='padding:5px 0px 5px 10px;'></div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap>분류이미지<br>(우측)</td>
							<td class='input_box_item' style='padding:5px 5px 5px 5px;'>
								<table border=0 class='input_table_box' width=100%>
									<col width='20%'>
									<col width='80%'>
									<tr>
										<td class='input_box_title' > 마우스 오버 전 </td>
										<td class='input_box_item' style='padding-top:8px;'>
										<input type='file' class=textbox name='rightcategory_img' style='padding-bottom:4px;'> 분류명(우측메뉴)으로 사용할 이미지 <input type='checkbox' name='ch_rightcategory_img' id='ch_rightcategory_img' value='Y' /> <label for='ch_rightcategory_img'>삭제</label>
										<div id='rightcategory_img_area' style='padding:5px 0px 5px 10px;'></div>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' > 마우스 오버 후 </td>
										<td class='input_box_item' style='padding-top:8px;'>
										<input type='file' class=textbox name='rightcategory_img_on' style='padding-bottom:4px;'> 분류명(우측메뉴)으로 사용할 이미지 <input type='checkbox' name='ch_rightcategory_img_on' id='ch_rightcategory_img_on' value='Y' /> <label for='ch_rightcategory_img_on'>삭제</label>
										<div id='rightcategory_img_on_area' style='padding:5px 0px 5px 10px;'></div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 	아이콘 사용</td>
					<td class='input_box_item' style='padding:5px;'>
						<input type='file' class=textbox name='sub_img_icon' style='padding-bottom:4px;'> 해당 분류의 메인페이지 상단에 노출되는 이미지 <input type='checkbox' name='ch_sub_img_icon' id='ch_sub_img_icon' value='Y' /> <label for='ch_sub_img_icon'>삭제</label>
						<div id='sub_img_icon_area' style='padding:5px 0px 5px 0px;'></div>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 	서브이미지 추가(이미지)</td>
					<td class='input_box_item' style='padding:5px;'>
						<table border=0 class='input_table_box' width=100%>
							<col width='20%'>
							<col width='80%'> 
							<tr>
								<td class='input_box_title' > 마우스 오버 전 </td>
								<td class='input_box_item' style='padding-top:8px;'>
								<input type='file' class=textbox name='sub_img' style='padding-bottom:4px;'> 해당 분류의 메인페이지 상단에 노출되는 이미지 <input type='checkbox' name='ch_sub_img' id='ch_sub_img' value='Y' /> <label for='ch_sub_img'>삭제</label>
								<div id='sub_img_area' style='width:400px;padding:5px 0px 5px 0px;'></div>
								</td>
							</tr>
							<tr>
								<td class='input_box_title' > 마우스 오버 후 </td>
								<td class='input_box_item' style='padding-top:8px;'>
									<input type='file' class=textbox name='sub_img_on' style='padding-bottom:4px;'> 해당 분류의 메인페이지 상단에 노출되는 이미지 <input type='checkbox' name='ch_sub_img_on' id='ch_sub_img_on' value='Y' /> <label for='ch_sub_img_on'>삭제</label>
									<div id='sub_img_on_area' style='width:400px;padding:5px 0px 5px 0px;'></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap> 	서브이미지 추가(HTML)</td>
					<td class='input_box_item' style='padding-left: 0px;'>
						<textarea name='category_top_view'  id='category_top_view'></textarea>
					</td>
				</tr>
				</table>
				<br>
				<table border='0' cellpadding=0 cellspacing=0 width='100%' class='search_table_box' style='margin-bottom:10px;'>
				<col width='20%'>
				<col width='*'>
				
				<tr>
					<td class='input_box_title'> <b>리스트 정렬</b></td>
					<td class='input_box_item' style='padding:5px 10px;' colspan=3 >
					<input type='radio' class='textbox' name='order_type' id='order_type_regdate' value='regdate' checked style='border:0px;' ".($gdb->dt[order_type] == "regdate" ? "checked":"")."><label for='order_type_regdate'>최근 등록순</label>
					<input type='radio' class='textbox' name='order_type' id='order_type_order_cnt' value='order_cnt' style='border:0px;' ".($gdb->dt[order_type] == "order_cnt" ? "checked":"")."><label for='order_type_order_cnt'>구매수순</label>
					<input type='radio' class='textbox' name='order_type' id='order_type_view_cnt' value='view_cnt' style='border:0px;' ".($gdb->dt[order_type] == "view_cnt" ? "checked":"")."><label for='order_type_view_cnt'>클릭수순</label>
					<input type='radio' class='textbox' name='order_type' id='order_type_sellprice' value='sellprice' style='border:0px;' ".($gdb->dt[order_type] == "sellprice" ? "checked":"")."><label for='order_type_sellprice'>최저가순</label>
					<input type='radio' class='textbox' name='order_type' id='order_type_favorite_cnt' value='favorite_cnt' style='border:0px;' ".($gdb->dt[order_type] == "favorite_cnt" ? "checked":"")."><label for='order_type_favorite_cnt'>관심상품순</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<select name='order_type_date' id='order_type_date'>
					<option value='0'>미설정</option>
					<option value='7'>7일</option>
					<option value='15'>15일</option>
					<option value='30'>30일</option>
					<option value='60'>60일</option>
					<option value='90'>90일</option>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> <b>스타일</b></td>
					<td class='input_box_item' style='padding:5px 10px;' colspan=3>
					<div style='float:left;text-align:center;width:130px;padding-top:10px;'>
						<img src='../images/".$admininfo["language"]."/g_5.gif' align=center onclick=\"document.getElementById('display_type_0').checked = true; change_display_type('0');\"  style='cursor:pointer;'><br>
						<input type='radio'  name='display_type' id='display_type_0' value='0' checked style='border:0px;' ".($gdb->dt[display_type] == "0" ? "checked":"")."><label for='display_type_0'>기본형(5EA 배열)</label>
					</div>
					<div style='float:left;text-align:center;width:130px;padding-top:10px;'>
						<img src='../images/".$admininfo["language"]."/g_4.gif' align=center onclick=\"document.getElementById('display_type_1').checked = true; change_display_type('1')\"><br>
						<input type='radio'  name='display_type' id='display_type_1' value='1' style='border:0px;' ".($gdb->dt[display_type] == "1" ? "checked":"")."><label for='display_type_1'>기본형(4EA 배열)</label>
					</div>
					<div style='float:left;text-align:center;width:130px;padding-top:10px;'>
						<img src='../images/".$admininfo["language"]."/g_3.gif' align=center onclick=\"document.getElementById('display_type_2').checked = true; change_display_type('2')\"><br>
						<input type='radio'  name='display_type' id='display_type_2' value='2' style='border:0px;' ".($gdb->dt[display_type] == "2" ? "checked":"")."><label for='display_type_2'>기본형2(3EA 배열)</label>
					</div>
					<div style='float:left;text-align:center;width:135px;padding-top:10px;'>
						<img src='../images/".$admininfo["language"]."/g_16.gif' align=center onclick=\"document.getElementById('display_type_4').checked = true; change_display_type('4')\"><br>
						<input type='radio'  name='display_type' id='display_type_4' value='4' style='border:0px;' ".($gdb->dt[display_type] == "4" ? "checked":"")."><label for='display_type_4'>기본형4(1/*EA 배열)</label>
					</div>
					<div style='float:left;text-align:center;width:135px;padding-top:10px;'>
						<img src='../images/".$admininfo["language"]."/g_17.gif' align=center onclick=\"document.getElementById('display_type_5').checked = true; change_display_type('5')\"><br>
					<input type='radio'  name='display_type' id='display_type_5' value='5' style='border:0px;' ".($gdb->dt[display_type] == "5" ? "checked":"")."><label for='display_type_5'>기본형(4EA 배열)</label>
					</div>
					<div style='float:left;text-align:center;width:135px;padding-top:10px;'>
					<img src='../images/".$admininfo["language"]."/g_24.gif' align=center onclick=\"document.getElementById('display_type_6').checked = true; change_display_type('6')\"><br>
					<input type='radio'  name='display_type' id='display_type_6' value='6' style='border:0px;' ".($gdb->dt[display_type] == "6" ? "checked":"")."><label for='display_type_6'>기본형(2/4EA 배열)</label>
					</div>
					</td>
				</tr>
				<tr height=27>
					<td class='search_box_title' nowrap> <b>상품노출 개수</b></td>
					<td class='search_box_item' colspan=3>
					가로 ( 자동:<div class='small' id='display_text' style='display:inline'>갤러리형 5열 </div>) * 세로 
					<input type='hidden' name='good_cnt_x' id='good_cnt_x' value=''>
					<input class='textbox number' type='text' name='good_cnt_y' id='good_cnt_y' size=5 value='".$db->dt[goods_max]."' maxlength='50' onkeyup=\"get_goods_max()\"> 행 = 총 상품
					<div class='small' id='goods_max_cnt' style='display:inline;'> 0 </div> 개
					<input class='textbox number' type='hidden' name='goods_max' id='goods_max' size=5 value='".$db->dt[goods_max]."' maxlength='50' >
					</td>
				</tr>
				<tr height=27>
					<td class='search_box_title' nowrap> <b>상품노출 이미지</b></td>
					<td class='search_box_item' colspan=3 style='padding:5px;'>
						<table cellpadding=0 cellspacing=0 border=0 width=97%>
						<col width='2%'>
						<col width='16%'>
						<col width='*'>
						<tr>
							<td align='center'><input type='checkbox' class='checkbox' name='product_border_use' id='product_border_use' value='1'>
							<td>상품 이미지 테두리 설정 : </td>
							<td style='vertical-align:middle'>
							<div style='float: left;vertical-align:middle;'>
							<input type='text' id='product_border_color' style='width:50px;vertical-align:middle;' class='textbox' name='product_border_color' value='#000000' onclick=\"setcolorpicker('colorpicker1','product_border_color')\"/>
							</div>
							<div id='colorpicker1' style='display:none;float: left;vertical-align:middle;'></div>
							<div style='float: left; padding-left:3px;'>
							<input type='text' class='textbox' style='width:50px;' name='product_style_line' id='product_style_line' value=''> PX
							</div>
							</td>
						</tr>
						<tr>
							<td align='center'><input type='checkbox' class='checkbox' name='pname_style_use' id='pname_style_use' value='1'>
							<td>상품명 : </td>
							<td>
							<div style='float: left;'>
							<input type='text' id='pname_color' style='width:50px;' class='textbox' name='pname_color' value='#000000'  onclick=\"setcolorpicker('colorpicker2','pname_color')\"/>
							</div>
							<div id='colorpicker2' style='display:none;float: left;'></div>
							<div style='float: left;'>
							".DisplayStyleSetup('pname_style','pname_style')."
							</div>
							</td>
						</tr>
						<tr>
							<td align='center'><input type='checkbox' class='checkbox' name='product_info_use' id='product_info_use' value='1'>
							<td>상품간략소개 : </td>
							<td>
							<div style='float: left;'>
							<input type='text' id='product_info_color' style='width:50px;' class='textbox' name='product_info_color' value='#000000'  onclick=\"setcolorpicker('colorpicker3','product_info_color')\"/>
							</div>
							<div id='colorpicker3' style='display:none;float: left;'></div>
							<div style='float: left;'>
							".DisplayStyleSetup('product_info_style','product_info_style')."
							</div>
							</td>
						</tr>
						<tr>
							<td align='center'><input type='checkbox' class='checkbox' name='product_listprice_use' id='product_listprice_use' value='1'>
							<td>상품 판매가 : </td>
							<td>
							<div style='float: left;'>
							<input type='text' id='product_listprice_color' style='width:50px;' class='textbox' name='product_listprice_color' value='#000000'  onclick=\"setcolorpicker('colorpicker4','product_listprice_color')\"/>
							</div>
							<div id='colorpicker4' style='display:none;float: left;'></div>
							<div style='float: left;'>
							".DisplayStyleSetup('product_listprice_style','product_listprice_style')."
							</div>
							</td>
						</tr>
						<tr>
							<td align='center'><input type='checkbox' class='checkbox' name='product_sellprice_use' id='product_sellprice_use' value='1'>
							<td>상품 할인간 : </td>
							<td>
							<div style='float: left;'>
							<input type='text' id='product_sellprice_color' style='width:50px;' class='textbox' name='product_sellprice_color' value='#000000'  onclick=\"setcolorpicker('colorpicker5','product_sellprice_color')\"/>
							</div>
							<div id='colorpicker5' style='display:none;float: left;'></div>
							<div style='float: left;'>
							".DisplayStyleSetup('product_sellprice_style','product_sellprice_style')."
							</div>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$Contents .= "
				<table cellpadding=5 cellspacing=1 width=97% >
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> <img src='../images/".$admininfo["language"]."/bt_category_modify.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"CategorySave(document.design_subcategory,'modify');\"></td>
				</tr>
				</table>";
}
$Contents .= "
				</from>
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
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' style='line-height:120%' >상단에 <label for='category_mode_add1' style='font-weight:bold'> 분류 추가</label> 를 선택하신후 좌측 상품 분류에서 추가하기 원하는 분류를  선택하신후  <b>분류 추가</b> 입력란에 분류를 입력하신후 <img src='../image/bt_category_add.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>분류 수정</b> : 상단에 <label for='category_mode_add1' style='font-weight:bold'> 선택된 분류 수정 </label> 를 선택하신후 좌측 상품 분류에서 추가하기 원하는 분류를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>분류 삭제</b> : 상단에 <label for='category_mode_add1' style='font-weight:bold'> 선택된 분류 수정 </label> 를 선택하신후 좌측 상품 분류에서 추가하기 원하는 분류를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
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
	<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
	<script src='../include/rightmenu.js'></script>\n".$Script;
	$P->addScript = $addScript; /**/
	$P->OnloadFunction = "";
	$P->title = "";//text_button('#', " ♣ 분류 구성");
	$P->strLeftMenu = seller_menu();
	$P->strContents = $Contents;
	$P->Navigation = "";
	$P->PrintLayOut();
	exit;
}else{

$P = new LayOut;
$addScript = "
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
<script src='../include/rightmenu.js'></script>\n
<SCRIPT type='text/javascript'>
<!--
	$(document).ready(function(){
		$('#member_search').click(function(){
			var search_text = $('#search_text').val();

			if(search_text){
				$.ajax({
				    url : './category.save.php',
				    type : 'POST',
				    data : {search_text:search_text,
							mode:'member_search'
							},
				    dataType: 'json',
				    error: function(data,error){// 실패시 실행함수 
				        alert(error);},
				    success: function(args){
						//$('#participation_1').empty();
						$.each(args, function(index, entry){
							$('#participation_1').append(\"<option value=\"+index+\">\"+entry+\"</option>\");
						});
		        	}
		    	});
			}else{
				alert('검색어를 입력하세요.');
			}

		});
		
					
		CKEDITOR.replace('category_top_view',{
			startupFocus : false,height:500
		});


		$('#colorpicker1').farbtastic('#color1');		//색상표선택
		$('#colorpicker2').farbtastic('#color2');		//색상표선택
		$('#colorpicker3').farbtastic('#color3');		//색상표선택
		$('#colorpicker4').farbtastic('#color4');		//색상표선택
		$('#colorpicker5').farbtastic('#color5');		//색상표선택

	
	});
//-->

function setcolorpicker(div_id,input_id){
	
	$('#'+div_id).farbtastic('#'+input_id);		//색상표선택
	$('#'+div_id).css('display','');

}

function department_del(dp_ix){
	$('#department_row_'+dp_ix).remove();
}

function person_del(code){
	$('#row_'+code).remove();
}

</SCRIPT>
".$Script;
$P->addScript = $addScript; /**/
$P->OnloadFunction = ""; //showSubMenuLayer('storeleft'); MenuHidden(false);
$P->title = "";//text_button('#', " ♣ 분류 구성"); 
$P->strLeftMenu = seller_menu();


$P->strContents = $Contents;
$P->Navigation = "셀러관리 > 셀러상품관리 > 상품분류설정";
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
	
	$cdb = new Database;

	$cname = $mdb->dt[cname];
	$cname_on = $mdb->dt[cname_on];
	$is_adult = $mdb->dt[is_adult];
	$is_layout_apply = $mdb->dt[is_layout_apply];
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
	node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type','$category_use','$category_access','$category_code','$cname_on','$is_adult','$is_layout_apply')\";
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
	$is_layout_apply = $mdb->dt[is_layout_apply];
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
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth, '$category_display_type','$category_use','$category_access','$category_code','$cname_on','$is_adult','$is_layout_apply')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";

	return $mstring;
}


?>