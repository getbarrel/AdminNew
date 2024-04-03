<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

$db = new Database;

$sql="select * from common_seller_delivery where company_id ='".$admininfo[company_id]."' ";
$db->query($sql);
$db->fetch();

$Contents01 .= "
	 <table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<col width='20%' />
		<col width='30%' />
		<col width='20%' />
		<col width='30%' />
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 미니샵 상품관리</b></div>")."</td>
	  </tr>
	 </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'>
		<col width='15%' />
		<col width='30%' />
		<col width='25%' />
		<col width='30%' />
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>노출여부 <img src='".$required3_path."'></b></td>
	    <td class='input_box_item' colspan=3>
			<input type='radio' id='seller_minishop_promotion_use_1' name='seller_minishop_promotion_use' value='1' ".($db->dt[seller_minishop_promotion_use] == "1" ? "checked":"")."> <label for='seller_minishop_promotion_use_1'>노출</label> &nbsp;&nbsp;
			<input type='radio' id='seller_minishop_promotion_use_0' name='seller_minishop_promotion_use' value='0' ".($db->dt[seller_minishop_promotion_use] == "0" ? "checked":"")."> <label for='seller_minishop_promotion_use_0'>미노출</label> 
		</td>
	  </tr>
	  </table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
		<col width='20%' />
		<col width='30%' />
		<col width='20%' />
		<col width='30%' />
		  <tr height=40>
			<td colspan=4 style='padding:10px 0px'>
			</td>
		</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
			<col width='20%' />
			<col width='30%' />
			<col width='20%' />
			<col width='30%' />
		  <tr>
			 <td  colspan='4' style=''>";

	$gdb = new Database;
	$gdb->query("SELECT * FROM shop_minishop_product_group WHERE company_id='".$admininfo[company_id]."' order by group_code asc ");

	for($i=0;$i < 4;$i++){
	$gdb->fetch($i);

	if($i==0)				$group_name="BEST 아이템";
	elseif($i==1)			$group_name="신규 아이템";
	elseif($i==2)			$group_name="전체상품";
	elseif($i==3)			$group_name="TOP Seller 아이템";
	else					$group_name="-";

	$Contents01 .= "
						  <div id='group_info_area".$i."' group_code='".($i+1)."'>
						  <div style='padding:10px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>".$group_name."<input type='hidden' name='group_name[".($i+1)."]' id='group_name_".($i+1)."' value='".$group_name."'></b> <!--a onclick=\"add_table()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle></a--> ".($i == 0 ? "":"<!--a onclick=\"del_table('group_info_area".$i."',".($i+1).");\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle></a-->")."</div>
						  <table width='100%' border='0' cellpadding='5' cellspacing='1' bgcolor='#E9E9E9' class='search_table_box'>
						  <col width='15%'>
						  <col width='*'>
							<!--tr>
							  <td class='search_box_title'><b>메인 상품그룹명</b></td>
							  <td class='search_box_item'>
							  <input type='text' class='textbox' name='group_name[".($i+1)."]' id='group_name_".($i+1)."' size=50 value=\"".$gdb->dt[group_name]."\"> 상품그룹 이미지 등록을 하지 않은경우 노출됩니다.
							  </td>
							</tr-->
							<tr>
							  <!--td class='search_box_title'><b>전시여부</b></td>
							  <td class='search_box_item'>
							  <input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_y' size=50 value='Y' style='border:0px;' ".($gdb->dt[use_yn] == "Y" || $gdb->dt[use_yn] == ""? "checked":"")."><label for='use_".($i+1)."_y'> 전시</label>
							  <input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_n' size=50 value='N' style='border:0px;' ".($gdb->dt[use_yn] == "N" ? "checked":"")."><label for='use_".($i+1)."_n'> 전시 하지 않음</label>
							  </td-->
							  <td class='search_box_title'><b>상품노출갯수</b></td>
							  <td class='search_box_item'>
								<input type='hidden' name='use_yn[".($i+1)."]'  value='Y'>
							  <input type='text' class='textbox' name='product_cnt[".($i+1)."]' id='product_cnt_".($i+1)."' size=10 value='".$gdb->dt[product_cnt]."'>
							  </td>
							</tr>
							<!--tr>
							  <td class='search_box_title'><b>상품그룹 이미지</b></td>
							  <td class='search_box_item' style='padding:10px'>
							  <input type='file' class='textbox' name='group_img[".($i+1)."]' id='group_img' size=50 value=''> <input type='checkbox' name='group_img_del[".($i+1)."]' id='group_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_img_del_".($i+1)."'>그룹이미지 삭제</label><br>
							  <div style='padding:5px 5px 5px 0px;height:90px;width:90%;overflow:auto' id='group_img_area_".($i+1)."'>";
	if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/main/".$img_pre_text."main_group_".($i+1).".gif")){
		$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/main/".$img_pre_text."main_group_".($i+1).".gif'>";
	}

	$Contents01 .= "</div><br>
							  <span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
							  </td>
							</tr>
							<tr>
							  <td class='search_box_title'><b>상품그룹이미지링크</b></td>
							  <td class='search_box_item'>
							  <input type='text' class='textbox' name='group_link[".($i+1)."]' id='group_link_".($i+1)."' size=50 value='".$gdb->dt[group_link]."'>
							  </td>
							</tr>
							<tr>
							  <td class='search_box_title'><b>상품그룹 배너 이미지</b></td>
							  <td class='search_box_item' style='padding:10px'>
							  <input type='file' class='textbox' name='group_banner_img[".($i+1)."]' id='group_banner_img' size=50 value=''> <input type='checkbox' name='group_banner_img_del[".($i+1)."]' id='group_banner_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_banner_img_del_".($i+1)."'>그룹 배너이미지 삭제</label><br>
							  <div style='padding:5px 5px 5px 0px;height:90px;width:90%;overflow:auto' id='group_banner_img_area_".($i+1)."'>";
	if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/main/".$img_pre_text."main_group_banner_".($i+1).".gif")){
		$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/main/".$img_pre_text."main_group_banner_".($i+1).".gif'>";
	}

	$Contents01 .= "						</div><br>
							  <span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
							  </td>
							</tr>
							<tr>
							  <td class='search_box_title'><b>전시타입</b></td>
							  <td class='search_box_item' style='padding:10px 5px;'>
							  <div style='float:left;text-align:center;width:130px;'>
								<img src='../images/".$admininfo["language"]."/g_5.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_0').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_0' value='0' style='border:0px;' ".($gdb->dt[display_type] == "0" ? "checked":"")."><label for='display_type_".($i+1)."_0'>기본형(5EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:130px;'>
								<img src='../images/".$admininfo["language"]."/g_4.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_1').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_1' value='1' style='border:0px;' ".($gdb->dt[display_type] == "1" ? "checked":"")."><label for='display_type_".($i+1)."_1'>기본형(4EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:130px;'>
								<img src='../images/".$admininfo["language"]."/g_3.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_2').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_2' value='2' style='border:0px;' ".($gdb->dt[display_type] == "2" ? "checked":"")."><label for='display_type_".($i+1)."_2'>기본형2(3EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:140px;'>
								<img src='../images/".$admininfo["language"]."/slide_4.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_3').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_3' value='3' style='border:0px;' ".($gdb->dt[display_type] == "3" ? "checked":"")."><label for='display_type_".($i+1)."_3' class='small'>슬라이드형(4EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:135px;display:none;'>
								<img src='../images/".$admininfo["language"]."/g_16.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_4').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_4' value='4' style='border:0px;' ".($gdb->dt[display_type] == "4" ? "checked":"")."><label for='display_type_".($i+1)."_4'>기본형4(1/*EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:135px;'>
								<img src='../images/".$admininfo["language"]."/g_17.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_5').checked = true;\"><br>
							  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_5' value='5' style='border:0px;' ".($gdb->dt[display_type] == "5" ? "checked":"")."><label for='display_type_".($i+1)."_5'>기본형(4EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:135px;'>
							  <img src='../images/".$admininfo["language"]."/g_24.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_6').checked = true;\"><br>
							  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_6' value='6' style='border:0px;' ".($gdb->dt[display_type] == "6" ? "checked":"")."><label for='display_type_".($i+1)."_6'>기본형(2/4EA 배열)</label>
							  </div>
							  </td>
							</tr-->
							<tr>
							  <td class='search_box_title'><b>전시상품</b></td>
							  <td class='search_box_item' style='padding:10px 10px;'>
							   <div style='padding-bottom:10px;'>
								  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_m'>수동등록</label>
								  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_a' size=50 value='A' style='border:0px;' ".($gdb->dt[goods_display_type] == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_a'>자동등록</label><br>
							  </div>
							  <div id='goods_manual_area_".($i+1)."' style='".($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == ""  ? "display:block;":"display:none;")."'>
								  <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,".($i+1).",'productList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
								  <div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >".relationEventGroupProductList(($i+1), "clipart",$page_type)."</div>
								  <div style='width:100%;float:left;'>
								  </div>
							  </div>
							  <div style='padding:0px 0px;".($gdb->dt[goods_display_type] == "A" ? "display:block;":"display:none;")."' id='goods_auto_area_".($i+1)."'>
								<a href=\"javascript:PoPWindow3('/admin/display/category_select.php?type=minishop&mmode=pop&group_code=".($i+1)."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_select_category.gif' border=0 align=absmiddle></a><br>
								<table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
									<col width=100%>
									<tr>
										<td style='padding-top:5px;'>";

												$Contents01 .= PrintCategoryRelation(($i+1),$page_type);

						$Contents01 .= "	</td>
									</tr>
									<tr><td style='padding-bottom:5px;'>카테고리 선택하기를 클릭해서 자동 노출하고자 하는 카테고리를 지정 하실 수 있습니다.</td></tr>
								</table>
								<div style='padding:5px 0px;'>
								선택한 카테고리 내의 상품을
								<select name='display_auto_type[".($i+1)."]'>
									<option value='order_cnt' ".($gdb->dt[display_auto_type] == "order_cnt" ? "selected":"").">구매수순</option>
									<option value='view_cnt' ".($gdb->dt[display_auto_type] == "view_cnt" ? "selected":"").">클릭수순</option>
									<option value='sellprice' ".($gdb->dt[display_auto_type] == "sellprice" ? "selected":"").">최저가순</option>
									<option value='regdate' ".($gdb->dt[display_auto_type] == "regdate" ? "selected":"").">최근등록순</option>
									<option value='wish_cnt' ".($gdb->dt[display_auto_type] == "wish_cnt" ? "selected":"").">찜한순</option>
									<option value='after_score' ".($gdb->dt[display_auto_type] == "after_score" ? "selected":"").">후기순위</option>
								</select>
								으로 노출 합니다.
								</div>
								</div>
							  </td>
							</tr>
						  </table><br><br>
						  </div>";
		}

	$Contents01 .= "</td>
		  </tr>
		</table>
";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table>
";
}



$Contents = "<form name='edit_form' action='recommend_promotion_product.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='iframe_act'>
<input name='act' type='hidden' value='update'>
<input name='company_id' type='hidden' value='".$admininfo[company_id]."'>";
$Contents = $Contents."<table width='100%' border=0>";
//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</table></form><br><br>";

$Script = "<!--script language='javascript' src='company.add.js'></script-->
<script type='text/javascript' src='/admin/js/ms_productSearch.js'></script>
<script language='javascript'>

function category_del(group_code, el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory_'+group_code);
	obj.deleteRow(idx);
	var cObj=\$('input[name=basic]');
	if(cObj.length == null){
		//cObj[0].checked = true; // 0이 나오지 null이 나오지 않음 kbk
	}else{
		for(var i=0;i<cObj.length;i++){
			if(cObj[i].checked){
				return true;
				break;
			}else{
				cObj[0].checked = true;
			}
		}
	}
	//cate.splice(idx,1);
}

function ChangeAddress(obj){
	if(obj.checked){
		document.getElementById('input_address_area').style.display = '';
	}else{
		document.getElementById('input_address_area').style.display = 'none';
	}
}


</script>
";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = seller_menu();
$P->strContents = $Contents;
$P->Navigation = "셀러관리 > 프로모션관리 > 추천상품관리(미니샵)";
$P->title = "추천상품관리(미니샵)";
echo $P->PrintLayOut();



function relationEventGroupProductList($group_code, $disp_type="",$page_type="M"){
	global $start,$page, $orderby, $admin_config, $erpid, $admininfo;

	$max = 105;

	$company_id = $admininfo[company_id];

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new MySQL;

	$sql = "SELECT COUNT(*) FROM ".TBL_SHOP_PRODUCT." p, shop_minishop_product_relation erp where p.id = erp.pid and group_code = '$group_code' and p.disp = 1 AND erp.company_id='".$company_id."' ";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[0];

	$sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, mpr_ix, erp.vieworder, erp.group_code, p.brand_name, p.disp, p.state
					FROM ".TBL_SHOP_PRODUCT." p, shop_minishop_product_relation erp
					where p.id = erp.pid and group_code = '$group_code' AND erp.company_id='".$company_id."' order by erp.vieworder asc limit $start,$max";
	$db->query($sql);

	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script>'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $db->dt['id'], 'c');
				$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$db->dt['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'", "", "", "", "", "", "'.$db->dt[disp].'", "'.$db->dt[state].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}


function PrintCategoryRelation($group_code,$page_type="M"){
	global $db ,$admininfo, $admininfo ;

	$company_id = $admininfo[company_id];

	$sql = "select c.cid,c.cname,c.depth, r.mcr_ix, r.regdate  from shop_minishop_category_relation r, shop_minishop_category_info c where group_code = '".$group_code."' and c.cid = r.cid AND r.company_id='".$company_id."' ";
	//echo $sql."<br><br>";
	$db->query($sql);

	if ($db->total == 0){
		$mString .= "<table cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."' style='width:100%;' >
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
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='category[".$group_code."][]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
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


?>
