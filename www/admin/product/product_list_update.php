<?

$help_text .= "
<div style='z-index:-1;position:absolute;' id='select_update_parent_save_loading'>
	<div style='width:700px;height:200px;display:block;position:relative;z-index:10;text-align:center;' id='select_update_save_loading'></div>
</div>";

if($update_kind_type == "category" || $update_kind_type == "update_nocategory" || $update_kind_type == "update_state"){	//카테고리 변경

$help_text .= "
			<div id='batch_update_category' ".($update_kind_type == "category"  || $update_kind_type == "update_nocategory"? "style='display:block'":"style='display:none'")." >
			<div style='padding:4px 0px 4px 0px'>
				<img src='../images/dot_org.gif'> <b>상품 카테고리 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택 후 저장 버튼을 클릭해 주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</span>
			</div>

			<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
			<col width=160>
			<col width='*'>
			<tr height=30>
				<td class='input_box_title'>
					<b>변경 형태 </b>
				</td>
				<td class='input_box_item'>";

$help_text .= "
					<input type='radio' name='category_change_type' id='category_change_type_1' value='1' checked><label for='category_change_type_1'> 카테고리 추가</label>";
if($update_kind_type == "category"  || $update_kind_type == "update_state"){ 
$help_text .= "
					<input type='radio' name='category_change_type' id='category_change_type_2' value='2'><label for='category_change_type_2'>카테고리 변경 또는 추가</label>";
$help_text .= "
					<input type='radio' name='category_change_type' id='category_change_type_3' value='3'><label for='category_change_type_3'>기본카테고리 변경(기본카테고리외 삭제)</label>";
}

$help_text .= "
				</td>
			</tr>
			</table><br>

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
			</div><br>

			<input type='hidden' name=selected_cid value='".$cid."'>
			<input type='hidden' name=selected_depth value=''>
			<input type='hidden' id='_category' value=''>
			<input type='hidden' id='_category' value=''>
			<input type='hidden' id='basic' value=''>
			<input type='hidden' id='depth' value=''>
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
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--1차분류--", "cid0", "cid","onChange=\"loadCategory(this,'cid1',2)\" title='1차분류' ", 0, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--2차분류--", "cid1",  "cid","onChange=\"loadCategory(this,'cid2',2)\" title='2차분류'", 1, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--3차분류--", "cid2", "cid", "onChange=\"loadCategory(this,'cid3',2)\" title='3차분류'", 2, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--4차분류--", "cid3", "cid", "onChange=\"loadCategory(this,'cid4',2)\" title='4차분류'", 3, $cid)."</td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--5차분류--", "cid4", "cid", "onChange=\"loadCategory(this,'cid_1',2)\" title='5차분류'", 4, $cid)."</td>
								<td style='padding:5px 4px 5px 6px;'><img src='../images/".$_SESSION["admininfo"]["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\" style='cursor:pointer;'></td>
							</tr>
						</table>
					</td>
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
						</table>
					</td>
				</tr>
			</table><br>
			
			<table border=0 cellpadding=0 cellspacing=0 width='100%' style='padding:5px 10px 5px 10px;border:1px solid silver' >
				<col width=100%>
				<tr>
					<td>
						<table width=100% cellpadding=0 cellspacing=0 id=objCategory >
						<col width=5>
						<col width=50>
						<col width=*>
						<col width=100>
						</table>
					</td>
				</tr>
				<tr>
					<td class='small' height='25' style='padding-left:15px;'>
						<span class='small'> * 첫번째 선택된 카테고리가 기본카테고리로 지정되며 라디오 버튼 클릭으로 기본카테고리를 변경 하실 수 있습니다</span>
					</td>
				</tr>
			</table><br>
			</div>
		</div>";

$help_text .= "
			<div id='batch_update_category_code' ".($update_kind_type == "category_code"? "style='display:block'":"style='display:none'")." >
			<div style='padding:4px 0px 4px 0px'>
				<img src='../images/dot_org.gif'> <b>상품 카테고리 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택 후 저장 버튼을 클릭해 주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</span>
			</div>

			<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
			<col width=160>
			<col width='*'>
			<tr height=30>
				<td class='input_box_title'>
					<b>변경 형태 </b>
				</td>
				<td class='input_box_item'>
					<input type='radio' name='category_change_type_code' id='category_change_type_4' value='4' checked><label for='category_change_type_4'>지정카테고리 추가(기존카테고리 삭제후 추가)</label>
				</td>
			</tr>
			</table><br>
			<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
			<col width=160>
			<col width='*'>
			<tr height=80>
				<td class='input_box_title'> <b>카테고리 지정 </b></td>
				<td class='input_box_item' style='padding:4px;'>
					<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
						<col width=160>
						<col width='*'>
						<tr>
							<td class='input_box_title'> <b>기본카테고리 </b></td>
							<td class='input_box_item' style='padding:4px;'>
								<input type ='text' class='textbox' name='basic_code' id='basic_code' value='' validation=true onkeyup=\"javascript:check_category();\">
								<input type='hidden' name='category_check_yn' id='category_check_yn' value='0'>
								<span id='check_category_code' style='color:red;'> ※ 기본카테고리 확인 </span>
							</td>
						</tr>
						<tr>
							<td class='input_box_title'> <b>추가 카테고리 </b></td>
							<td class='input_box_item' style='padding:4px;'>
								<textarea name='display_category_code' id='display_category_code' class='tline textbox' style='padding: 0px; height: 90px; width: 150px; border: 1px solid rgb(204, 204, 204);'></textarea>
								<span id='check_category_code' style='color:blue;'> ※ 카테고리 구분은 'Enter' 로만 가능합니다.  </span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			</table>
			</div>";

}


if($update_kind_type == "update_sell_priod_date"){		//판매기간/상태/노출 일괄수정
//판매기간
$help_text .= "
	<div id='update_sell_priod_date' ".($update_kind == "priod_date" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>판매/진열 상태 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=40>
		<td class='input_box_title'> 
			<b>적용여부 </b> <br>
			
		</td>
		<td class='input_box_item'>
			<input type='radio' name='btach_is_sell_date' value='0' id='batch_is_sell_date_0' ".($btach_is_sell_date == "0" ? "checked":"")."> <label for='batch_is_sell_date_0'>미적용</label>
			<input type='radio' name='btach_is_sell_date' value='1' id='batch_is_sell_date_1' ".($btach_is_sell_date == "1" || $btach_is_sell_date == "" ? "checked":"")."> <label for='batch_is_sell_date_1'>적용</label>
		</td>
	</tr>
	<tr height=80>
		<td class='input_box_title'> 
			<b>판매기간 </b>
		</td>
		<td class='input_box_item'>
			<table cellpadding='0' cellspacing='2' border='0' bgcolor=#ffffff width=100%>
				<col width='130'>
				<col width='*'>
				<tr>
					<td nowrap>
						".search_date('sell_priod_sdate','sell_priod_edate',$sell_priod_sdate,$sell_priod_edate,'Y','A')."
					</td>
				</tr>
			</table>
			- 판매시작 날짜가 현재일 이전이면 상품이 등록되지 않습니다.<br>
		</td>
	</tr>
	</table>
	</div>";

//판매상태
$help_text .= "
	<div id='batch_update_state' ".($update_kind == "state"? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>판매/진열 상태 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>판매상태 </b></td>
		<td class='input_box_item'>
		".SellState($state,"radio",$id,'c_state')."
		</td>
	</tr>
	</table>
	</div>";

//노출여부
$help_text .= "
	<div id='batch_update_disp' ".($update_kind == "disp"? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>판매/진열 상태 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>노출여부 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='c_disp' id='c_disp_0' value='0'  ".(($disp == "0" || $disp == "") ? "checked":"")."><label for='c_disp_0'> 미노출 </label>
			<input type='radio' name='c_disp' id='c_disp_1' value='1'  ".($disp == "1" ? "checked":"")."><label for='c_disp_1'> 노출 </label>
		</td>
	</tr>
	</table>
	</div>";

}

if($update_kind_type == 'update_brand'){
//판매상태,노출여부 변경
$help_text .= "

<div id='update_brand' ".($update_kind == "update_brand" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'><img src='../images/dot_org.gif'> <b>브랜드</b> <span class=small style='color:gray'>
".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span></div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b> 브랜드 </b></td>
		<td class='input_box_item'>
			<div style='padding-left:5px;float:left'>
			".BrandListSelect($brand, $cid,'batch_brand','batch_brand_name','batch_b_ix')."
			</div>
			<div style='padding-left:5px;float:left'>
			<input type='checkbox' name='brand_check' value='1'> <span class='small blu'> * 체크한 항목만 수정처리 됩니다. </span>
			</div>
		</td>

	</tr>
	</table>";
$help_text .= "
</div>";
}


if($update_kind_type == 'update_product_point'){
//판매상태,노출여부 변경
$help_text .= "

<div id='update_brand' ".($update_kind == "update_brand" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'><img src='../images/dot_org.gif'> <b>상품레벨 포인트 일괄변경</b> <span class=small style='color:gray'>
".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span></div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=15%>
	<col width='35%'>
	<col width=15%>
	<col width='35%'>
	<tr height=30>
		<td class='input_box_title'> <b> 적립상태 </b></td>
		<td class='input_box_item'>
			<select name='state' class='p11 ls1' id='state' style='width:65px;'>
				<option value='1' ".CompareReturnValue("1",$db->dt[state],"selected").">적립(+)</option>
				<option value='2' ".CompareReturnValue("2",$db->dt[state],"selected").">차감(-)</option>
			</select>
		</td>
		<td class='input_box_title'> <b> 상품레벨점수구분 </b></td>
		<td class='input_box_item'>
			<select name='use_state' id='use_state' style='display:'>
				<option value='1' ".CompareReturnValue("1",$db->dt[use_state],"selected").">입금완료</option>
				<option value='2' ".CompareReturnValue("2",$db->dt[use_state],"selected").">배송완료</option>
				<option value='3' ".CompareReturnValue("3",$db->dt[use_state],"selected").">구매확정</option>
				<option value='4' ".CompareReturnValue("4",$db->dt[use_state],"selected").">입금후취소</option>
				<option value='5' ".CompareReturnValue("5",$db->dt[use_state],"selected").">교환확정</option>
				<option value='6' ".CompareReturnValue("6",$db->dt[use_state],"selected").">반품확정</option>
				<option value='7' ".CompareReturnValue("7",$db->dt[use_state],"selected").">배송지연</option>
				<option value='8' ".CompareReturnValue("8",$db->dt[use_state],"selected").">추가배송지연</option>
				<option value='9' ".CompareReturnValue("9",$db->dt[use_state],"selected").">기타</option>
			</select>
		</td>
	</tr>
	<tr height=30>
		<td class='input_box_title'> <b> 주문번호 </b></td>
		<td class='input_box_item' >
		<input type='text' class=textbox name='oid' id='oid' value='".$db->dt[oid]."' style='width:80%;'>
		</td>
		<td class='input_box_title'> <b> 적립내용 </b></td>
		<td class='input_box_item' >
		 <input type='text' class=textbox name='etc' id='etc' value='' style='width:80%;'>
		</td>
	</tr>
	<tr height=30>
		<td class='input_box_title'> <b> 상품레벨점수 </b></td>
		<td class='input_box_item' colspan=3>
		 <input type='text' class=textbox name='point' id='point' size=10>
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";
}

if($update_kind_type == 'update_delivery_policy'){	//배송정책

if($company_id == ""){			//검색창에서 넘어온 company_id 로 배송정책 출력하기 위하여 구분 2014-04-17 이학봉
	$company_id = $admininfo[company_id];
}else{
	$company_id = $company_id;
}

$help_text .= "
<div id='update_delivery_policy' ".($update_kind == "delivery_policy" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'>
	<img src='../images/dot_org.gif'> <b>배송정책</b> 
	<span class=small style='color:gray'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
</div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='80%'>";
if($admininfo[admin_level]=='9'){
$help_text .= "
	<tr height=30>
		<td class='input_box_title'> <b> 배송타입 선택 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='batch_delivery_type' id='batch_delivery_type_1' value='1' onclick=\"changeDeliveryArea('1', '".$id."');\" checked><label for='batch_delivery_type_1'>통합배송</label>&nbsp;&nbsp;
			<input type='radio' name='batch_delivery_type' id='batch_delivery_type_2' value='2' onclick=\"changeDeliveryArea('2', '".$id."');\" ><label for='batch_delivery_type_2'>입점업체별 배송</label>&nbsp;&nbsp;
		</td>
	</tr>";
}
$help_text .= "
	<tr>
		<td class='input_box_title' nowrap> 상품별 개별정책 설정</td>
		<td class='input_box_item' nowrap>
		<input type='radio' name='batch_delivery_policy' value='1' id='batch_delivery_policy_1' ".($batch_delivery_policy == '1' || $batch_delivery_policy == "" ? "checked":"")." onclick=\"deliveryTypeView('1')\" ><label for='batch_delivery_policy_1'>사용안함</label>
		<input type='radio' name='batch_delivery_policy' value='2' id='batch_delivery_policy_2' ".($batch_delivery_policy == '2' ? "checked":"")." onclick=\"deliveryTypeView('2')\" ><label for='batch_delivery_policy_2'>사용</label>
		<input type='hidden' name='company_id' value='".$admininfo[company_id]."'>
		</td>
	</tr>
	<tr>
		<td class='input_box_title' nowrap> 
			배송비 정책(소매)
		</td>
		<td class='input_box_item' id='policy_input_retail' ".($delivery_policy == '1' || $id == "" ? "style='display:none;padding:5px;'":"")." nowrap>
			<table cellpadding=0 cellspacing=0 width=99% class='input_table_box' >
			<col width='18%'>
			<col width='*'>
			<tr bgcolor='#ffffff' height=25 align=center>
				<td class='input_box_title' nowrap> 배송비 정책(소매)</td>
				<td class='input_box_item'>
					<input type='radio' class='textbox' name='delivery_div_show' id='delivery_div_1' value='1' style='border:0px;' onclick=\"showDeliveryFromAjax('1', '".$id."');\" checked><label for='delivery_div_1'>택배/방문수령</label>
					<input type='radio' class='textbox' name='delivery_div_show' id='delivery_div_3' value='3' style='border:0px;' onclick=\"showDeliveryFromAjax('3', '".$id."');\"><label for='delivery_div_3'>직배송차량</label> </br>

					<div id='delivery_template_area' style='float:left;padding-top:2px;'>
						".select_delivery_template($admininfo[company_id],'R','1',$id,'checked')."
					</div>
				</td>
			</tr>
			</table>
		</td>
		<td class='input_box_item' id='policy_text_retail' style='padding:0 0 0 10px;".($delivery_policy == '2' ? "display:none;":"")."' nowrap>";
			$sql = "select * from shop_delivery_template where company_id = '".$admininfo[company_id]."' and is_basic_template = '1' and product_sell_type = 'R'";
			$db->query($sql);
			$template_array = $db->fetchall();
			$help_text .= "<span id='basic_template_delivery'>".get_delivery_policy_text($template_array,'0')."</span>";
	$help_text .="
			<input type='checkbox' name='dt_ix[".$template_array[0][product_sell_type]."][".$template_array[0][delivery_div]."][template_check]' id='template_basic_dt_check' value='1' checked>
			<input type='hidden' name='dt_ix[".$template_array[0][product_sell_type]."][".$template_array[0][delivery_div]."][dt_ix]' id='template_basic_dt_r' value='".$template_array[0][dt_ix]."'>
		</td>
	</tr>";
	if($admininfo[admin_level]=='9' && false){
	$help_text .="
	<tr>
		<td class='input_box_title' nowrap> 
			배송비 정책(도매)
		</td>
		<td class='input_box_item' id='policy_input_whole' ".($delivery_policy == '1' || $id == "" ? "style='display:none;padding:5px;'":"")." nowrap>
			<table cellpadding=0 cellspacing=0 width=99% class='input_table_box' >
			<col width='18%'>
			<col width='*'>
			<tr bgcolor='#ffffff' height=25 align=center>
				<td class='input_box_title' nowrap> 택배/퀵서비스/방문수령</td>
				<td class='input_box_item'>
					<div id='' style='float:left;'>
						".select_delivery_template($company_id,'W','1',$id)."
					</div>
				</td>
			</tr>

			<tr bgcolor='#ffffff' height=25 align=center>
				<td class='input_box_title' nowrap> 화물/용달(다마스)</td>
				<td class='input_box_item'>
					<div id='' style='float:left;'>
						".select_delivery_template($company_id,'W','2',$id)."
					</div>
				</td>
			</tr>

			<tr bgcolor='#ffffff' height=25 align=center>
				<td class='input_box_title' nowrap> 직배송차량</td>
				<td class='input_box_item'>
					<div id='' style='float:left;'>
						".select_delivery_template($company_id,'W','3',$id)."
					</div>
				</td>
			</tr>
			</table>

		</td>
		<td class='input_box_item' id='policy_text_whole' style='padding:0 0 0 10px;".($delivery_policy == '2' ? "display:none;":"")."' nowrap>";
			$sql = "select * from shop_delivery_template where company_id = '".$company_id."' and is_basic_template = '1' and product_sell_type = 'W'";
			$db->query($sql);
			$template_array = $db->fetchall();
			$help_text .= get_delivery_policy_text($template_array,'0');
	$help_text .="
			<input type='checkbox' name='dt_ix[".$template_array[0][product_sell_type]."][".$template_array[0][delivery_div]."][template_check]' id='template_basic_dt_check' value='1' checked>
			<input type='hidden' name='dt_ix[".$template_array[0][product_sell_type]."][".$template_array[0][delivery_div]."][dt_ix]' id='template_basic_dt_w' value='".$template_array[0][dt_ix]."'>
		</td>
	</tr>";
	}

	$help_text .="
	</table>";
$help_text .= "
</div>";

}

if($update_kind_type == 'update_seller'){		//셀러/매입처 일괄수정
$help_text .= "

<div id='update_seller' ".($update_kind == "update_seller" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'><img src='../images/dot_org.gif'> <b>셀러 / 매입처 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span></div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b> 셀러선택 </b></td>
		<td class='input_box_item'>
			<div style='padding-left:5px;float:left'>
			".companyAuthList($company_id , "validation=true title='셀러업체' ","batch_company_id","batch_company_id","batch_com_name")."
			
			</div>
			<div style='padding-left:5px;float:left'>
			<input type='checkbox' name='seller_check' value='1'> <span class='small blu'> * 체크한 항목만 수정처리 됩니다. </span>
			</div>
		</td>
	</tr>
	<tr height=30>
		<td class='input_box_title'> <b> 매입처선택 </b></td>
		<td class='input_box_item'>
		<div style='padding-left:5px;float:left'>
			".TradeCompanyList($trade_admin, "",'batch_trade_admin','batch_trade_admin','batch_trade_name')."
		</div>
		<div style='padding-left:5px;float:left'>
			<input type='checkbox' name='trade_check' value='1'> <span class='small blu'> * 체크한 항목만 수정처리 됩니다. </span>
		</div>
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";
}

if($update_kind_type == 'update_wish'){		//관련상품 일괄변경
$help_text .= "

<div id='update_wish' ".($update_kind == "update_wish" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'>
	<img src='../images/dot_org.gif'> <b>관련상품 변경</b> 
	<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
</div>";


$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b> 노출갯수 </b></td>
		<td class='input_box_item'>
			<input type=text name='relation_product_cnt' class='textbox' value='".$relation_product_cnt."' size=5> 개
		</td>
	</tr>
	</table><br>
	<table border=0 cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
		<tr bgcolor='#ffffff'>
			<!--td bgcolor=#efefef><img src='/admin/image/ico_dot.gif'> <b class=blk>관련상품</b></td-->
			<td class='input_box_item' style='padding:15px;'>
			<div style='padding-bottom:10px;'>
				<input type='radio' class='textbox' name='relation_display_type' id='use_1_m' size=50 value='M' style='border:0px;' ".(($relation_display_type == "M" || $relation_display_type == "") ? "checked":"")." onclick=\"$('#goods_manual_area_1').show();$('#goods_auto_area_1').hide();\"><label for='use_1_m'>수동등록</label>
				<input type='radio' class='textbox' name='relation_display_type' id='use_1_a' size=50 value='A' style='border:0px;' ".($relation_display_type == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_1').hide();$('#goods_auto_area_1').show();\"><label for='use_1_a'>자동등록</label> 
			</div>
			<div id='goods_manual_area_1' style='".(($relation_display_type == "M" || $relation_display_type == "") ? "display:block;":"display:none;")."'>
				<a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,1,'productList_1');\">
				<img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
				<div style='width:100%;padding:5px;' id='group_product_area_1' >".relationProductList($id, "clipart")."</div>
				<div style='width:100%;float:left;'><span class=small>* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.</span>
				</div>
			</div>

			<div style='padding:0px 0px;".($relation_display_type == "A" ? "display:block;":"display:none;")."' id='goods_auto_area_1'>
				<a href=\"javascript:PoPWindow3('../display/category_select.php?mmode=pop&group_code=0',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_select_category.gif' border=0 align=absmiddle></a><br>

				<table border=0 cellpadding=0 cellspacing=0 width='99%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
					<col width=100%>
					<tr>
						<td style='padding-top:5px;'>";

						$help_text .= PrintCategoryRelation(0,$id);

		$help_text .= "	</td>
					</tr>
					<tr>
						<td style='padding:10px;'>
							카테고리 선택하기를 클릭해서 자동 노출하고자 하는 카테고리를 지정 하실 수 있습니다.
						</td>
					</tr>
				</table>

				<div style='padding:5px 0px;'>
				선택한 카테고리 내의 상품을
				<select name='relation_display_order_type'>
					<option value='order_cnt' ".($relation_display_order_type == "order_cnt" ? "selected":"").">구매수순</option>
					<option value='view_cnt' ".($relation_display_order_type == "view_cnt" ? "selected":"").">클릭수순</option>
					<option value='sellprice' ".($relation_display_order_type == "sellprice" ? "selected":"").">최저가순</option>
					<option value='regdate' ".($relation_display_order_type == "regdate" ? "selected":"").">최근등록순</option>
					<option value='wish_cnt' ".($relation_display_order_type == "wish_cnt" ? "selected":"").">찜한순</option>
					<option value='after_score' ".($relation_display_order_type == "after_score" ? "selected":"").">후기순위</option>
				</select>
				으로 
				<select name='relation_display_order_date'>";
				
				for($i=1;$i<=31;$i++){
				$help_text .= "	<option value='".$i."' ".($relation_display_order_date == $i?'selected':'').">".$i."</option>";
				}
				$help_text .= "
				</select>
				 일 기준으로 노출합니다.
				</div>
			</div>";

		$help_text .= "
			</td>
		</tr>
		<tr bgcolor='#F8F9FA'>
			<td colspan=2>
			</td>
		</tr>
	</table><br>
			";

$help_text .= "
</div>";
}

if($update_kind_type == 'update_basic_info'){		//아이콘/SNS/검색키워드(*)

$help_text .= "
<div id='update_basic_info_icon' ".($update_kind == "update_icon" ? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'>
	<img src='../images/dot_org.gif'> <b>아이콘 변경</b> 
	<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b> 변경형태 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='update_icon_type' id='update_icon_type_1' value='1' checked> <label for='update_icon_type_1'>기존 보존 후 아이콘 추가 </label>

			<input type='radio' name='update_icon_type' id='update_icon_type_2' value='2' > <label for='update_icon_type_2'>기존 아이콘 삭제후 아이콘 추가</label>

			<input type='radio' name='update_icon_type' id='update_icon_type_3' value='3' > <label for='update_icon_type_3'>아이콘 전체 미노출</label>
		</td>
	</tr>
	<tr height=30>
		<td class='input_box_title' > 아이콘노출 &nbsp;<a href=\"javascript:PoPWindow('../design/product_icon.php?mmode=pop',960,700,'brand')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_icon.gif' align=absmiddle border=0></a> </td>
		<td class='input_box_item' colspan=3>
			<table width='100%'>
				<tr>
					<td>
						<table border=0>
							<tr>
								";
								$db->query("select idx from shop_icon where disp = '1' and icon_type ='P' order by idx");
								$icon_list = $db->fetchall();
								if(count($icon_list) >0 ){
									for($i=0;$i<count($icon_list);$i++){
										$help_text .=	"<td><input type=\"checkbox\" name='icon_check[]' class=nonborder id=icon_check value=".$icon_list[$i][idx]." ".($icons_checked[$icon_list[$i][idx]] == "1" ? "checked":"")."></td><td><img src='".$_SESSION["admin_config"][mall_data_root]."/images/icon/".$icon_list[$i][idx].".gif' align='absmiddle' style='vertical-align:middle'></td>";
										if($i%8==0 && $i>0) $help_text .=	"</tr></table><table border=0><tr>";
									}
								}
								$help_text .="
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table><br>
	<br>";
$help_text .= "
</div>";


$help_text .= "
<div id='update_basic_info_sns' ".($update_kind == "update_sns"? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'>
	<img src='../images/dot_org.gif'> <b>SNS공유버튼 노출 변경</b> 
	<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b> 변경형태 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='update_sns_type' id='update_sns_type_1' value='1' checked> <label for='update_sns_type_1'>기존 보존 후 SNS 추가 </label>

			<input type='radio' name='update_sns_type' id='update_sns_type_2' value='2' > <label for='update_sns_type_2'>기존 SNS 삭제후 SNS 추가</label>

			<input type='radio' name='update_sns_type' id='update_sns_type_3' value='3' > <label for='update_sns_type_3'>SNS 전체 미사용</label>
		</td>
	</tr>
	<tr height=30>
		<td class='input_box_title'> <b> SNS공유버튼 노출 </b></td>	
		<td class='input_box_item'>
			<input type=\"checkbox\" name='sns_btn_yn' class=nonborder id=sns_btn_yn value='Y' checked> <label for='sns_btn_yn'>사용함</label> (
			<input type=\"checkbox\" name='sns_btn[btn_use1]' class=nonborder id=btn_use1 value=facebook > <label for='btn_use1'>페이스북</label>
			<input type=\"checkbox\" name='sns_btn[btn_use2]' class=nonborder id=btn_use2 value=twitter > <label for='btn_use2'>트위터</label>

			<input type=\"checkbox\" name='sns_btn[btn_use3]' class=nonborder id=btn_use3 value=me2day > <label for='btn_use3'>미투데이</label>
			<input type=\"checkbox\" name='sns_btn[btn_use4]' class=nonborder id=btn_use4 value=yozm > <label for='btn_use4'>요즘</label>

			)
		</td>
	</tr>
	</table><br>
	<br>";
$help_text .= "
</div>";


$help_text .= "
<div id='update_basic_info_keyword' ".($update_kind == "update_keyword" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'>
	<img src='../images/dot_org.gif'> <b>검색키워드 변경</b> 
	<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b> 변경형태 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='update_keyword_type' id='update_keyword_type_1' value='1' checked> <label for='update_keyword_type_1'>기존 보존 후 검색어 추가 </label>

			<input type='radio' name='update_keyword_type' id='update_keyword_type_2' value='2' > <label for='update_keyword_type_2'>기존 검색어 삭제후 검색어 추가</label>

			<input type='radio' name='update_keyword_type' id='update_keyword_type_3' value='3' > <label for='update_keyword_type_3'>검색어 전체 미노출</label>
		</td>
	</tr>
	<tr height=30>
		<td class='input_box_title'> 검색키워드 </td>
		<td class='input_box_item' style='padding:5px 5px;line-height:150%' colspan=3 style=''>
		<input type=text class='textbox' name='search_keyword' style='width:40%' value='$search_keyword'><br>
		※<span class=small >이곳에 등록된 검색어에 의해서도 해당상품이 검색, 노출 될 수 있도록 해주는 기능입니다.</span> </td>
	</tr>
	</table><br>
	<br>";
$help_text .= "
</div>";

}

if($update_kind_type == 'update_movie'){	//동영상/바이럴URL

$help_text .= "
<div id='update_movie' ".($update_kind == "movie" || $update_kind == ""? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'>
	<img src='../images/dot_org.gif'> <b>동영상/바이럴URL 변경</b> 
	<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b> 동영상 
		<input type='checkbox' name='movie_url_check' value='1'>
		</b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='movie' style='width:40%' value='".$movie."'>
		</td>
	</tr>
	<tr height=30>
		<td class='input_box_title'> 동영상/바이럴URL
		<input type='checkbox' name='virals_check' value='1'>
		</td>
		<td class='input_box_item' style='line-height:100%;padding:5px;'>";

$help_text .="<div id='viral_zone'>";

$help_text .= "<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='virals_input' class='virals_input' opt_idx=0 style='margin-bottom:5px'>
				<col width='7%'>
				<col width='22%'>
				<col width='35%'>
				<col width='36%'>
				<tr height=25 bgcolor='#ffffff' align=center>
					<td bgcolor=\"#efefef\" class=small>사용</td>
					<td bgcolor=\"#efefef\" class=small> 카페&블로그명(20자) *</td>
					<td bgcolor=\"#efefef\" class=small> URL *</td>
					<td bgcolor=\"#efefef\" class=small> 기타설명</td>
				</tr>";

	$sql = "select * from shop_product_viralinfo where pid = '".$id."' order by vi_ix asc ";
	$db->query($sql);
	$virals = $db->fetchall();
	for($i=0;($i < count($virals) || $i < 1);$i++){

$help_text .= "
				<tr align='center' bgcolor='#ffffff' depth=1 item=1>
					<td height='30'><input type='hidden' class='vi_ix' name='virals[".$i."][vi_ix]' id='vi_ix'  value='".$virals[$i][vi_ix]."' /><input type=checkbox name='virals[".$i."][vi_use]' id='vi_use' value='1' ".($virals[$i][vi_use] == "1" ? "checked":"") ."></td>
					<td><input type=text class='textbox' name='virals[".$i."][viral_name]' id='viral_name' inputid='viral_name' style='width:90%;vertical-align:middle' value='".$virals[$i][viral_name]."'></td>
					<td><input type=text class='textbox' name='virals[".$i."][viral_url]' id='viral_url' inputid='viral_url' style='width:90%;vertical-align:middle' value='".$virals[$i][viral_url]."'></td>
					<td><input type=text class='textbox' name='virals[".$i."][viral_desc]'  id='viral_desc'  style='width:85%' value='".$virals[$i][viral_desc]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('#virals_input tbody').find('tr[depth^=1]').length > 1){\$(this).parent().parent().remove();}else{ViralInfoDelete($(this).parent().parent());}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'> <a onclick=\"ViralInfoCopy('virals_input')\" style='cursor:pointer;'><img src='../images/i_add.gif' border=0 style='margin:0px 0 0px 0;' align=absmiddle></a> </td>
				</tr>";
	}

$help_text .= "
				</table>
			</div>";

$help_text .= "
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";

}

if($update_kind_type == 'update_product_md'){
$help_text .= "
<div id='update_product_md' ".($update_kind == "md" || $update_kind == ""? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>검색키워드 변경</b> 
		<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> MD설정 </td>
		<td class='input_box_item' style='line-height:150%'>
		<input type='hidden' name='md_code_1' id='md_code_1' value=''>
		".MDSelect($md_code,'','','1')." 
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";

}

if($update_kind_type == 'update_product_commission'){
$help_text .= "
<div id='update_product_commission' ".($update_kind == "commission" || $update_kind == ""? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>수수료정보 변경</b> 
		<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> 수수료정보 설정 </td>
		<td class='input_box_item' style='line-height:150%;padding:10px;'>
		<input type='hidden' name='md_code_1' id='md_code_1' value=''>
			<div style='padding:3px;' id='fee_setting_zone'>
				<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' ".($_SESSION["admininfo"][admin_level] == 8 ? "style='display:none;'":"").">
					<col width='18%'>
					<col width='32%'>
					<col width='18%'>
					<col width='32%'>
					<tr>
						<td class='input_box_title' > 개별수수료 사용 </td>
						<td class='input_box_item' colspan=3  >
							<input type='radio' name='batch_one_commission' id='batch_one_commission_n' value='N' checked onclick=\"batch_commissionChange(document.listform)\"> <label for='batch_one_commission_n'>사용안함 </label>
							
							<input type='radio' name='batch_one_commission' id='batch_one_commission_y' value='Y'  onclick=\"batch_commissionChange(document.listform)\"> <label for='batch_one_commission_y'>사용</label>

							<input type='hidden' name='the_one_commission' id='the_one_commission_n' value='N' > 
						</td>
					</tr>
					
					<tr id='account_info_div' style='display:none' height=70>
						<td class='input_box_title' > 정산방식 </td>
						<td class='input_box_item' colspan=3  >
							<table width='100%' cellpadding=0 cellspacing=0 style='padding-top:3px;padding-bottom:3px;'>
							<tr>
								<td>
									<input type='radio' name='account_type' id='account_type_1' value='1' ".($account_type == '1' || $account_type == "" ? "checked":"")." > <label for='account_type_1'>판매가 정산방식 (판매가에 수수료 적용)</label>
								</td>
							</tr>
							<tr>
								<td>
									<input type='radio' name='account_type' id='account_type_2' value='2' ".($account_type == '2' ? "checked":"")."> <label for='account_type_2'>매입가 정산방식 (공급가로 정산되며, 하단 수수료에 0 이 아닌 숫자를 입력시 그 숫자의 % 만큼 차감후 정산처리됩니다.)</label>
								</td>
							</tr>
							<tr>
								<td>
									<input type='radio' name='account_type' id='account_type_3' value='3' ".($account_type == '3' ? "checked":"")." > <label for='account_type_3'>미정산 (선매입이고 본사에 재고가 있으며, 상품등록을 셀러가 진행시에 사용되며, 정산에서 제외됩니다.)</label>
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr id='account_info_div' style='display:none'>
						<td class='input_box_title' >소매 수수료  </td>
						<td class='input_box_item' id='DP' style='padding-top:5px;' >
							<input style='width:40px' type='text' class='textbox numeric' size='30' name='commission' id='commission' value='".$commission."' goods_commission='".$goods_commission."' company_commission='".$company_commission."' style='TEXT-ALIGN:right' > % 단위로 입력하시기 바랍니다. <br>
							<span class='small blu'>('개별수수료 사용' 선택시에만 입력 할 수 있습니다.)</span>
						</td>

						<td class='input_box_title' >도매 수수료  </td>
						<td class='input_box_item' id='DP' style='padding-top:5px;' >
							<input style='width:40px' type='text' class='textbox numeric' size='30' name='wholesale_commission' id='wholesale_commission' value='".$wholesale_commission."' whole_goods_commission='".$goods_wholesale_commission."' whole_company_commission='".$company_wholesale_commission."' style='TEXT-ALIGN:right' > % 단위로 입력하시기 바랍니다. <br> <span class='small blu'>('개별수수료 사용' 선택시에만 입력 할 수 있습니다.)</span>
							
						</td>
					</tr>

				</table>
			</div>
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";

}

if($update_kind_type == 'update_mandatory_type'){
$help_text .= "
<div id='update_mandatory_type' ".($update_kind == "mandatory" || $update_kind == ""? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>상품정보고시 변경</b> 
		<span class=small style='color:gray'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> 상품고시정보 </td>
		<td class='input_box_item' style='line-height:150%;padding:3px;'>
			
			<table cellpadding=0 cellspacing=0 width='100%' border='0'>
				<tr height=30>
					<td>
					<select name='mandatory_type_1' id='mandatory_select_1' onchange='MandatoryChange(1)'>
						<option value='' ".($mi_code == "" ? "selected":"").">상품분류를 선택해주세요</option>";

					$sql = "select * from shop_mandatory_info where is_use = '1' order by mi_code ASC";
					$db->query($sql);
					$mandatory_array = $db->fetchall();
					for($i=0;$i<count($mandatory_array);$i++){
						$help_text .="
						<option value='".$mandatory_array[$i][mi_code]."' ".($mandatory_array[$i][mi_code] == $mi_code ? "selected":"").">".$mandatory_array[$i][mandatory_name]."</option>";
					}
$help_text .="
					</select>
					<select name='mandatory_type_2' id='mandatory_select_2' onchange='MandatoryChange(2)' ".($mi_code == "0"||$mi_code == ""? "style='display:none;'":"")." >
						<option value='1' ".($mandatory_type_2 == "1" ? "selected":"").">국내상품</option>
						<!--<option value='2' ".($mandatory_type_2 == "2" ? "selected":"").">해외상품</option>-->
					</select>
					<a href =\"JavaScript:PoPWindow('./reg_guide.php',703,652,'comparewindow');\" ><img src ='../images/".$_SESSION["admininfo"]["language"]."/product_guide.gif' align=absmiddle></a>
					</td>
				<tr>
				</table>

				<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='mandatory_info_zone' class='mandatory_info_zone' opt_idx=0 style='margin-bottom:10px'>
					<col width='20%'>
					<col width='30%'>
					<col width='20%'>
					<col width='30%'>
					<tr height=25 bgcolor='#ffffff' align=center>
						<td bgcolor=\"#efefef\" class=small>항목</td>
						<td bgcolor=\"#efefef\" class=small> 내용</td>
						<td bgcolor=\"#efefef\" class=small> 항목</td>
						<td bgcolor=\"#efefef\" class=small> 내용</td>
					</tr>
					<tr bgcolor='#ffffff' align=center>
						<td colspan=4 id='mandatory_info_td'>";


$sql = "select * from shop_product_mandatory_info where pid = '".$id."' order by pmi_ix asc ";
$db->query($sql);
$mandatory_info = $db->fetchall();

if($db->total && $id!=""){
	for($i=0;$i < count($mandatory_info);$i = $i + 2){

		$help_text .= "	<table width=100% id='mandatory_info' class='mandatory_info".($i==0?"_basic":"")."' mandatory_info_cnt='".$i."' cellspacing=0 cellpadding=0 >
						<col width='20%'>
						<col width='30%'>
						<col width='20%'>
						<col width='30%'>
						<tr align='center'>
							<td height='30'>
								<input type='hidden' id='mandatory_info_pmi_ix_a' class='' name='mandatory_info[".$i."][pmi_ix]' value='".$mandatory_info[$i][pmi_ix]."' />
								<input type='hidden' id='mandatory_info_pmi_code_a' class='' name='mandatory_info[".$i."][pmi_code]' value='".$mandatory_info[$i][pmi_code]."' />
								<input type=text id='mandatory_info_title_a' class='textbox' name='mandatory_info[".$i."][pmi_title]'  style='width:90%;vertical-align:middle' value='".$mandatory_info[$i][pmi_title]."'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_a' class='textbox' name='mandatory_info[".$i."][pmi_desc]' style='width:85%' value='".$mandatory_info[$i][pmi_desc]."' title ='".$mandatory_info[$i][pmi_title]."' validation='true' >
							</td>
							<td >
								<input type='hidden' id='mandatory_info_pmi_ix_b' class='' name='mandatory_info[".($i+1)."][pmi_ix]' value='".$mandatory_info[($i+1)][pmi_ix]."' />
								<input type='hidden' id='mandatory_info_pmi_code_b' class='' name='mandatory_info[".($i+1)."][pmi_code]' value='".$mandatory_info[($i+1)][pmi_code]."' />
								<input type=text id='mandatory_info_title_b' class='textbox' name='mandatory_info[".($i+1)."][pmi_title]' style='width:90%;vertical-align:middle' value='".$mandatory_info[($i+1)][pmi_title]."'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_b' class='textbox' name='mandatory_info[".($i+1)."][pmi_desc]' style='width:85%' value='".$mandatory_info[($i+1)][pmi_desc]."' title ='".$mandatory_info[($i+1)][pmi_title]."' validation='".($mandatory_info[($i+1)][pmi_title] != "" ? "true" : "false")."' >";
				if($admininfo[admin_level] == '9'){
					$help_text .= "
								<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.mandatory_info').length > 1){document.getElementById('mandatory_info_td').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/}else{clearInputBox('mandatory_info');}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>";
				}
					$help_text .= "
							</td>
						</tr>
						<tr>
							<td height='13' colspan='2'>
								<div class='mandatory_info_comment_".($i)." small' style='padding:0px 20px;text-align:right;'></div>
							</td>
							<td colspan='2'>
								<div class='mandatory_info_comment_".($i+1)." small' style='padding:0px 20px;text-align:right;' ></div>
							</td>
						</tr>
						</table>";
	}
}else{
$help_text .= "
						<table width=100% id='mandatory_info' class='mandatory_info_basic' mandatory_info_cnt='0' cellspacing=0 cellpadding=0 >
						<col width='20%'>
						<col width='30%'>
						<col width='20%'>
						<col width='30%'>
						<tr align='center'>
							<td height='30'>
								<input type='hidden' id='mandatory_info_pmi_ix_a' class='' name='mandatory_info[0][pmi_ix]' value='' />
								<input type='hidden' id='mandatory_info_pmi_code_a' class='' name='mandatory_info[0][pmi_code]' value='' />
								<input type=text id='mandatory_info_title_a' class='textbox' name='mandatory_info[0][pmi_title]' style='width:90%;vertical-align:middle' value='' title='' validation='false'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_a' class='textbox' name='mandatory_info[0][pmi_desc]' style='width:85%' value=''>
							</td>
							<td>
								<input type='hidden' id='mandatory_info_pmi_ix_b' class='' name='mandatory_info[1][pmi_ix]' value='' />
								<input type='hidden' id='mandatory_info_pmi_code_b' class='' name='mandatory_info[1][pmi_code]' value='' />
								<input type=text id='mandatory_info_title_b' class='textbox' name='mandatory_info[1][pmi_title]' style='width:90%;vertical-align:middle' value='' title='' validation='false'>
							</td>
							<td>
								<input type=text id='mandatory_info_desc_b' class='textbox' name='mandatory_info[1][pmi_desc]' style='width:85%' value=''>";
			if($admininfo[admin_level] == '9'){
				$help_text .= "
								<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.mandatory_info').length > 1){document.getElementById('mandatory_info_td').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/}else{clearInputBox('mandatory_info');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>";
			}
				$help_text .= "
							</td>
						</tr>
						<tr>
							<td height='13' colspan='2'>
								<div class='mandatory_info_comment_0 small' style='padding:0px 20px;text-align:right;'></div>
							</td>
							<td colspan='2'>
								<div class='mandatory_info_comment_1 small' style='padding:0px 20px;text-align:right;'></div>
							</td>
						</tr>
						</table>";
}
$help_text .= "
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";

}

if($update_kind_type == 'update_price'){

$help_text .= "
<div id='update_price' ".($update_kind == "price" || $update_kind == ""? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>가격 변경</b> 
		<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> 가격정보 설정 </td>
		<td class='input_box_item' style='line-height:150%;padding:3px;'>";

$help_text .= "		
				<table cellpadding=1 cellspacing=1 bgcolor=silver width=100% >
				<col width=15%>
				<col width=15%>
				<col width=15%>
				<col width=15%>
				<col width=15%>";

$help_text .= "				<tr bgcolor='#efefef' height=35 align=center >
					<td>공급가</td>
					<td colspan=2>도매가</td>
					<td colspan=2>소매가</td>
				</tr>
				<tr bgcolor='#efefef' height=35 align=center>
					<td  >".($_SESSION["admininfo"][admin_level] == 9 ? "<b>구매단가(원가) <img src='".$required3_path."'></b>":"<b>공급가격 <img src='".$required3_path."'></b>")." </td>";
$help_text .= "					<td ><b>도매 판매가 ".($_SESSION["admininfo"][mall_type] == "BW" ? "<img src='".$required3_path."'>":"")."</b></td>";
$help_text .= "					<td ><b>도매 할인가 ".($_SESSION["admininfo"][mall_type] == "BW" ? "<img src='".$required3_path."'>":"")."</b></td>";
$help_text .= "					<td ><b>소매 권장정가 ".($_SESSION["admininfo"][mall_type] != "BW" ? "<img src='".$required3_path."'>":"")."</b> ";

$help_text .= "					".($_SESSION["admininfo"][admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.listform, document.listform.listprice.value, 1);calcurate_maginrate(document.listform)\" style='cursor:pointer;color:red'> 복사→</span>":"")."</td>
							<td >
							<b>소매 권장판매가(할인가) ".($_SESSION["admininfo"][mall_type] != "BW" ? "<img src='".$required3_path."'>":"")."</b>
					</td>
				</tr>
				<tr bgcolor='#fbfbfb' height=35 align=center>

					<td class='point_color'><input type=hidden name=bcoprice value='$coprice' >
					".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
					<input type=text class='textbox  numeric' name=coprice size=13 style='text-align:right;padding-right:3px;' value='$coprice'  onkeyup='calcurate_maginrate(document.listform)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  $message  $readonlyString ".(($_SESSION["admininfo"][mall_type] == "O" || $_SESSION["admininfo"][mall_type] == "E") ? "":"validation=true")." title='구매단가(공급가)'>
						".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
					</td>
					<td class='point_color'><input type=hidden name=bwholesale_price value='$wholesale_price' >
						".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
						<input type=text class='textbox numeric' name=wholesale_price size=13 style='text-align:right;padding-right:3px;' value='$wholesale_price'  onkeyup='calcurate_maginrate(document.listform)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  $message  $readonlyString ".($_SESSION["admininfo"][mall_type] == "BW" ? "validation=true":"")." title='도매가'>
							".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
					</td>
					<td class='point_color'><input type=hidden name=bwholesale_sellprice value='$wholesale_sellprice' >
						".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
						<input type=text class='textbox numeric' name=wholesale_sellprice size=13 style='text-align:right;padding-right:3px;' value='$wholesale_sellprice'  onkeyup='calcurate_maginrate(document.listform)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  $message  $readonlyString ".($_SESSION["admininfo"][mall_type] == "BW" ? "validation=true":"")." title='도매가 판매가(할인가)'>
							".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
					</td>
					<td class='point_color'><input type=hidden name=blistprice value='$listprice'>
						".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]." <input type=text class='textbox numeric' name=listprice value='$listprice' size=13  style='TEXT-ALIGN:right;padding-right:3px;".$colorString."'  maxlength=16  $message  $readonlyString ".($_SESSION["admininfo"][mall_type] != "BW" ? "validation=true":"validation=false")." title='정가'> ".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
					</td>
					<td class='point_color'>
						<input type=hidden name=bsellprice value='$sellprice'>
						".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["front"]."
						<input type=text class='textbox numeric' name=sellprice size=13 value='$sellprice'  maxlength=16 onkeyup='calcurate_maginrate(document.listform)' style='TEXT-ALIGN:right;padding-right:3px;".$colorString." ' $message  $readonlyString ".($_SESSION["admininfo"][mall_type] != "BW" ? "validation=true":"validation=false")." title='판매가(할인가)'>
						".$currency_display[$_SESSION["admin_config"]["currency_unit"]]["back"]."
					</td>
					<input type='hidden' class='textbox'  name='wholesale_basic_margin'>
					<input type='hidden' class='textbox'  name='wholesale_sale_rate'>
					<input type='hidden' class='textbox'  name='basic_margin'>
					<input type='hidden' class='textbox'  name='sale_rate'>
				</tr>
			</table>";
$help_text .= "
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";

$help_text .= "
<div id='update_mileage' ".($update_kind == "mileage"? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>마일리지설정(개별설정)</b> 
		<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> 개별 적립금 사용유무 </td>
		<td class='input_box_item' style='line-height:150%;padding:2px;'>
			<table width='99%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
			<tr bgcolor='#efefef' height=35>
				<td bgcolor='#fbfbfb' style='padding-left:20px;'>
				<table>
					<tr>
						<td>
							도매 : 
							<input type='radio' name='wholesale_reserve_yn' id='wholesale_reserve_n' value='N' checked>
							<label for='wholesale_reserve_n'>미적용</label>
							<input type='radio' name='wholesale_reserve_yn' id='wholesale_reserve_y'  value='Y'>
							<label for='wholesale_reserve_y'>적용</label> 
						</td>
						<td bgcolor='#fbfbfb'>
							<input type=text class='textbox integer' name=wholesale_rate1 size=13 style='text-align:right;padding-right:3px;' value='$wholesale_rate1'> &nbsp;&nbsp;
							<select name=wholesale_rate_type style='font-size:12px;width:50px; height:22px;  vertical-align:middle;'>
								<option value='1' > % </option>
							</select>
						</td>
					</tr>
				</table>
				</td>
				<td bgcolor='#fbfbfb' style='padding-left:20px;'>
					<table>
					<tr>
						<td>
							소매 : 
							<input type='radio' name='reserve_yn' id='reserve_n' value='N' checked>
							<label for='reserve_n'>미적용</label>
							<input type='radio' name='reserve_yn' id='reserve_y'  value='Y'>
							<label for='reserve_y'>적용</label> 
						</td>
						<td bgcolor='#fbfbfb'>
							<input type=text class='textbox integer' name=rate1 size=13 style='text-align:right;padding-right:3px;' value='$rate1'> &nbsp;&nbsp;
							<select name=rate_type style='font-size:12px;width:50px; height:22px;  vertical-align:middle;'>
								<option value='1' > % </option>
							</select>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";

}


if($update_kind_type == 'update_state'){
if( $admininfo[admin_level] == '9'){
$help_text .= "
<div id='update_state' ".($update_kind == "state" || $update_kind == ""? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>판매상태 변경</b> 
		<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>";
$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> 판매상태 </td>
		<td class='input_box_item' style='line-height:150%;padding:2px;'>
			".SellState($state,"radio",$id,'c_state')."
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";
}

//상품일괄삭제
$help_text .= "
<div id='batch_update_product' ".($update_kind == "product_delete"? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'><img src='../images/dot_org.gif'> <b>판매/진열 상태 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span></div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>상품삭제 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='product_del' id='product_del_0' value='0' ".($state2 == "0" ? "checked":"")."><label for='product_del_0'>상품삭제</label>
			<input type='radio' name='product_del' id='product_del_1' value='1' ".(($state2 == "" || $state2 == "1") ? "checked":"")."><label for='product_del_1'>삭제안함</label>
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";


}


if($update_kind_type == 'update_is_mobile_use'){
if( $admininfo[admin_level] == '9'){
$help_text .= "
<div id='update_is_mobile_use' ".($update_kind == "is_mobile_use" || $update_kind == ""? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>모바일사용유무 변경</b> 
		<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>";
$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> 모바일상품여부 </td>
		<td class='input_box_item' style='line-height:150%;padding:2px;'>
			<input type='radio' name='batch_is_mobile_use' value='A' id='batch_is_mobile_use_a' ".($batch_is_mobile_use == "A" || $batch_is_mobile_use == "" ? "checked":"")." > 
			<label for='batch_is_mobile_use_a'> 전체</label>
			<input type='radio' name='batch_is_mobile_use' value='M' id='batch_is_mobile_use_m' ".($batch_is_mobile_use == "M" ? "checked":"")." > 
			<label for='batch_is_mobile_use_m'> 모바일 </label>
			<input type='radio' name='batch_is_mobile_use' value='W' id='batch_is_mobile_use_w' ".($batch_is_mobile_use == "W" ? "checked":"")." > 
			<label for='batch_is_mobile_use_w'> 웹 </label>
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";
}

//상품일괄삭제
$help_text .= "
<div id='batch_update_product' ".($update_kind == "product_delete"? "style='display:block'":"style='display:none'")." >
<div style='padding:14px 0 4px 0'><img src='../images/dot_org.gif'> <b>판매/진열 상태 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span></div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>상품삭제 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='product_del' id='product_del_0' value='0' ".($state2 == "0" ? "checked":"")."><label for='product_del_0'>상품삭제</label>
			<input type='radio' name='product_del' id='product_del_1' value='1' ".(($state2 == "" || $state2 == "1") ? "checked":"")."><label for='product_del_1'>삭제안함</label>
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";


}


//가용재고 일괄수정
if($update_kind_type == 'update_available_stock'){
$help_text .= "
<div id='update_available_stock' ".($update_kind == "available_stock" || $update_kind == ""? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>가용재고 수량 변경</b> 
		<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> 가용재고 설정 </td>
		<td class='input_box_item' style='line-height:150%'>
			<input type=text class='textbox integer' name=available_stock  style='width:70px;' value=''> 개
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";

}


//가용재고 일괄수정
if($update_kind_type == 'update_sellertool_goods'){
$help_text .= "
<div id='update_sellertool_goods' ".($update_kind == "sellertool_goods" || $update_kind == ""? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>제휴사 판매설정 변경</b> 
		<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr>
						<td class='input_box_title'> <b>제휴사상품연동여부</b>    </td>
						<td class='input_box_item' colspan=3>
							";

						$sql = "select * from sellertool_get_product where pid = '".$id."' and state = '1' ";
						$db->query($sql);
						$partner_prd_reg = $db->fetchall();
						//print_r($partner_prd_reg);
						if(is_array($partner_prd_reg)){
						for($i=0; $i < count($partner_prd_reg); $i++){
							$help_text .= "
								<input type='hidden' name='partner_prd_reg_before[]' value='".$partner_prd_reg[$i]['site_code']."' />
							";
							$partner_prd_data[] =  $partner_prd_reg[$i]['site_code'];
						}
						}else{
							$partner_prd_data = array();
						}
						//$sql = "select * from sellertool_site_info where api_yn = 'Y'";
						$sql = "select * from sellertool_site_info where api_yn = 'Y' and site_code not in (select site_code from sellertool_not_company where state= '1' and company_id = '".$admin."')";
						$db2->query($sql);

						if($db2->total){
							for($i=0; $i < $db2->total; $i++){
								$db2->fetch($i);
								//$sql = "select state from sellertool_get_product where company_id = '".$company_id."' and site_code = '".$db2->dt[site_code]."'";
								
								//$mdb->query($sql);
								if(in_array($db2->dt[site_code],$partner_prd_data)){
									$help_text .= "
									<input type=checkbox name='partner_prd_reg[]' id='partner_prd_reg_".$i."' value='".$db2->dt[site_code]."' checked ><label for='partner_prd_reg_".$i."'>".$db2->dt[site_name]."</label>";	
								}else{
									$help_text .= "
									<input type=checkbox name='partner_prd_reg[]' id='partner_prd_reg_".$i."' value='".$db2->dt[site_code]."' ".($act =='insert' ? "checked" : "" )." ><label for='partner_prd_reg_".$i."'>".$db2->dt[site_name]."</label>";
								}		
						
							}
						}
				$help_text .= "
							
						</td>
					</tr>
	</table>";
$help_text .= "
</div>";

}


if($update_kind_type == 'update_product_mrogoup'){
$help_text .= "
<div id='update_product_mrogoup' ".($update_kind == "update_product_mrogoup" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:14px 0 4px 0'>
		<img src='../images/dot_org.gif'> <b>고정단가 그룹 변경</b> 
		<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
	</div>";

$help_text .="
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> 고정단가 회원그룹 </td>
		<td class='input_box_item' style='line-height:150%'>
		<input type='hidden' name='md_code_1' id='md_code_1' value=''>
		".MDSelect($md_code,'','','1')." 
		</td>
	</tr>
	</table>";
$help_text .= "
</div>";

}


if(checkMenuAuth(md5("/admin/product/goods_input.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table>";
}else{
	$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>";
}

//선택메뉴
$select = "
	<select name='update_type' >
		<option value='2' selected>선택한 상품 전체에</option>c
		<option value='1' >검색한 상품 전체에</option>
	</select>";

if($update_kind_type == 'update_sell_priod_date'){

$select .= "
	<input type='radio' name='update_kind' id='update_sell_priod_date_priod_date' value='priod_date' ".($update_kind == 'priod_date' || $update_kind== ''?'checked':'')." onclick=\"ChangeUpdateForm('update_sell_priod_date');\"><label for='update_sell_priod_date_priod_date'>판매기간 설정</label>";

$select .= "
	<input type='radio' name='update_kind' id='update_state_type' value='state' ".CompareReturnValue("state",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_state');\"><label for='update_state_type'>판매상태 설정</label>";

$select .= "
	<input type='radio' name='update_kind' id='update_disp' value='disp' ".CompareReturnValue("disp",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_disp');\"><label for='update_disp'>노출여부 설정</label>";


}

if($update_kind_type == 'category' || $update_kind_type == 'update_nocategory'){
$select .= "
	<input type='radio' name='update_kind' id='update_kind_category' value='category' ".($update_kind == 'category' || $update_kind== ''?'checked':'')." onclick=\"ChangeUpdateForm('batch_update_category');\"><label for='update_kind_category'>카테고리 변경</label>";
	if($admininfo[admin_level] == '9'){
	$select .= "
		<input type='radio' name='update_kind' id='update_kind_category_code' value='category_code' ".($update_kind == 'category_code'?'checked':'')." onclick=\"ChangeUpdateForm('batch_update_category_code');\"><label for='update_kind_category_code'>카테고리 추가(코드형)</label>";
	}
}

if($update_kind_type == 'update_brand'){
$select .= "
	<input type='radio' name='update_kind' id='update_brand' value='update_brand' checked onclick=\"ChangeUpdateForm('batch_update_reserve');\"><label for='update_brand'>브랜드</label>";
}

if($update_kind_type == 'update_delivery_policy'){
$select .= "
	<input type='radio' name='update_kind' id='delivery_policy' value='delivery_policy' checked onclick=\"ChangeUpdateForm('update_delivery_policy');\"><label for='delivery_policy'> 배송정책 설정 </label>";

}

if($update_kind_type == 'update_seller'){
$select .= "
	<input type='radio' name='update_kind' id='radio_update_seller' value='update_seller' checked onclick=\"ChangeUpdateForm('update_seller');\"><label for='radio_update_seller'> 셀러/매입처 설정 </label>";
}

if($update_kind_type == 'update_wish'){
$select .= "
	<input type='radio' name='update_kind' id='radio_update_wish' value='update_wish' checked onclick=\"ChangeUpdateForm('update_wish');\"><label for='radio_update_wish'> 관련상품 설정 </label>";
}

if($update_kind_type == 'update_basic_info'){

$select .= "
	<input type='radio' name='update_kind' id='update_keyword' value='update_keyword' checked onclick=\"ChangeUpdateForm('update_basic_info_keyword');\"><label for='update_keyword'>검색키워드</label>";

}

if($update_kind_type == 'update_movie'){
$select .= "
	<input type='radio' name='update_kind' id='radio_update_movie' value='movie' checked onclick=\"ChangeUpdateForm('update_movie');\"><label for='radio_update_movie'> 동영상/바이럴URL </label>";
}

if($update_kind_type == 'update_product_md'){
$select .= "
	<input type='radio' name='update_kind' id='radio_update_product_md' value='md' checked onclick=\"ChangeUpdateForm('update_product_md');\"><label for='radio_update_product_md'> MD설정</label>";
}

if($update_kind_type == 'update_product_commission'){
$select .= "
	<input type='radio' name='update_kind' id='radio_update_product_commission' value='commission' checked onclick=\"ChangeUpdateForm('update_product_commission');\"><label for='radio_update_product_commission'> 수수료정보</label>";
}

if($update_kind_type == 'update_mandatory_type'){
$select .= "
	<input type='radio' name='update_kind' id='radio_update_mandatory_type' value='mandatory' checked onclick=\"ChangeUpdateForm('update_mandatory_type');\"><label for='radio_update_mandatory_type'> 상품정보고시</label>";
}

if($update_kind_type == 'update_price'){
$select .= "
	<input type='radio' name='update_kind' id='update_type_price' value='price' checked onclick=\"ChangeUpdateForm('update_price');\"><label for='update_type_price'> 가격변경</label>";

$select .= "
	<input type='radio' name='update_kind' id='update_type_mileage' value='mileage' onclick=\"ChangeUpdateForm('update_mileage');\"><label for='update_type_mileage'> 마일리지설정(개별설정)</label>";
}


if($update_kind_type == 'update_state'){

	if($admininfo[admin_level] == '9'){
		$select .= "
			<input type='radio' name='update_kind' id='update_type_state' value='state' checked onclick=\"ChangeUpdateForm('update_state');\"><label for='update_type_state'> 판매상태</label>";

		$select .= "
			<input type='radio' name='update_kind' id='update_kind_category' value='category' onclick=\"ChangeUpdateForm('batch_update_category');\"><label for='update_kind_category'> 카테고리변경</label>";
	}

$select .= "
	<input type='radio' name='update_kind' id='update_kind_product' value='product_delete' ".CompareReturnValue("product_delete",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_product');\"><label for='update_kind_product'>상품일괄삭제</label>
	";

}

if($update_kind_type == 'update_is_mobile_use'){
$select .= "
	<input type='radio' name='update_kind' id='is_mobile_use' value='is_mobile_use' checked onclick=\"ChangeUpdateForm('update_is_mobile_use');\"><label for='is_mobile_use'> 관련상품 설정 </label>";
}


if($update_kind_type == 'update_product_point'){
$select .= "
	<input type='radio' name='update_kind' id='radio_update_product_point' value='update_product_point' checked onclick=\"ChangeUpdateForm('update_product_point');\"><label for='radio_update_product_point'> 상품레벨 포인트 </label>";
}

//가용재고
if($update_kind_type == 'update_available_stock'){
$select .= "
	<input type='radio' name='update_kind' id='radio_available_stock' value='available_stock' checked onclick=\"ChangeUpdateForm('available_stock');\"><label for='radio_available_stock'> 가용재고 설정 </label>";
}

if($update_kind_type == 'update_sellertool_goods'){
$select .= "
	<input type='radio' name='update_kind' id='radio_sellertool_goods' value='sellertool_goods' checked onclick=\"ChangeUpdateForm('sellertool_goods');\"><label for='radio_sellertool_goods'>제휴판매설정</label>";
}

if($update_kind_type == 'update_product_mrogoup'){
$select .= "
	<input type='radio' name='update_kind' id='radio_update_product_mrogoup' value='md' checked onclick=\"ChangeUpdateForm('update_product_mrogoup');\"><label for='radio_update_product_mrogoup'> 고정단가 회원그룹</label>";
}
$Script .= "
<script Language='JavaScript' src='../product/goods_input.js?v=".rand()."'></script>
<script Language='JavaScript' src='../product/goods_mandatory_info.js?v=".rand()."'></script>
<script language='javascript'>

function unloading(){
	parent.document.getElementById('parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('loadingbar').innerHTML ='';
	parent.document.getElementById('save_loading').innerHTML ='';
	parent.document.getElementById('save_loading').style.display = 'none';
}

function ChangeUpdateForm(selected_id){
	var area = new Array('update_sell_priod_date','batch_update_state','batch_update_disp','batch_update_category','batch_update_reserve','batch_update_pos','batch_update_whole','batch_update_product','update_brand','update_basic_info_icon','update_basic_info_sns','update_basic_info_keyword','update_movie','update_product_md','update_price','update_mileage','update_state','batch_update_category_code','update_delivery_type','update_delivery_policy','update_product_mrogoup'); 

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
				//alert('2___'+selected_id);
			//alert(selected_id);
			document.getElementById(selected_id).style.display = 'block';
			//$.cookie('goodsinfo_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			//alert('1___'+selected_id);
			$('#'+area[i]).css('display','none');
			//document.getElementById(area[i]).style.display = 'none';
		}
	}
}

function categoryadd()
{
	var ret;
	var str = new Array();
	var obj = document.listform.cid;
	for (i=0;i<obj.length;i++){
		if (obj[i].value){
			str[str.length] = obj[i][obj[i].selectedIndex].text;
			ret = obj[i].value;
		}
	}
	if (!ret){
		alert(language_data['goods_input.php']['A'][language]);//'카테고리를 선택해주세요'
		return;
	}
	//var cate = document.all._category;
	var cate=document.getElementsByName('display_category[]'); // 호환성 kbk
	//alert(cate.length);

	//if(is_array([cate])){
		//alert(cate.length);
		for(i=0;i < cate.length;i++){
			//alert(ret +'=='+ cate[i].value);
			//alert(cate[i].value);
			if(ret == cate[i].value){
				alert(language_data['goods_input.php']['B'][language]);
				//'이미등록된 카테고리 입니다.'
				return;
			}
		}
	//}

	//cate.unshift(ret);
	var obj = document.getElementById('objCategory');
	//oTr = obj.insertRow();
	oTr = obj.insertRow(-1); // 크롬과 파폭에서는 td의 생성이 반대로 됨 -1 인자를 넣어주면 순서대로 형성됨 2011-04-07 kbk
	oTr.id = 'num_tr';
	oTr.height = '30px';
	//oTr.className = 'dot_xx';
	if(window.addEventListener) oTr.setAttribute('class','');
	else oTr.className = '';
	oTd = oTr.insertCell(-1);
	//oTd.className = '';
	if(window.addEventListener) oTd.setAttribute('class','');
	else oTd.className = '';
	oTd.innerHTML = \"<input type=text name=display_category[] id='_category' value='\" + ret + \"' style='display:none'>\";
	oTd = oTr.insertCell(-1);
	//oTd.className = '';
	if(window.addEventListener) oTd.setAttribute('class','');
	else oTd.className = '';
	if(oTr.rowIndex == 0){
		oTd.innerHTML = \"<input type=radio name=basic id='basic_\"+ ret + \"' value='\"+ ret + \"' validation=true title='기본카테고리' checked>\";
	}else{
		oTd.innerHTML = \"<input type=radio name=basic id='basic_\"+ ret + \"' value='\"+ ret + \"' validation=true title='기본카테고리' >\";
	}
	oTd = oTr.insertCell(-1);
	//oTd.id = \"currPosition\";
	if(window.addEventListener) oTd.setAttribute('id','currPosition');
	else oTd.id = 'currPosition';
	//oTd.className = '';
	if(window.addEventListener) oTd.setAttribute('class','');
	else oTd.className = '';
	oTd.innerHTML = \"<label for='basic_\"+ret+\"'>\"+str.join(\" > \")+\"</label>\";
	oTd = oTr.insertCell(-1);
	//oTd.className = '';
	if(window.addEventListener) oTd.setAttribute('class','');
	else oTd.className = '';
	oTd.innerHTML = \" <a href='javascript:void(0)' onClick='category_del(true,this.parentNode.parentNode)'><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0></a>\";
	//onClick='category_del(this.parentNode.parentNode)' 를 onClick='category_del(true,this.parentNode.parentNode)' 로 변경 kbk 13/07/01

}

function category_del(ot,el) {//ot 추가 kbk 13/07/01
	idx = el.rowIndex;

	if(ot) var obj = document.getElementById('objCategory');
	else var obj = document.getElementById('objCategory_'+ot);
	obj.deleteRow(idx);
	if(ot) {
		var cObj=\$('input[name=basic]');
		var cObj_num=0;
		if(cObj.length == null){
			//cObj[0].checked = true; // 0이 나오지 null이 나오지 않음 kbk
		}else{
			for(var i=0;i<cObj.length;i++){
				//if(cObj[i].is(':checked')){ 20131028 Hong .is() 함수 오류로 인한 수정
				if(cObj[i].checked == true){
					cObj_num++;
				}
			}
			if(cObj_num==0) {
				if(cObj[0]) cObj[0].checked = true;//if(cObj[0]) 추가 kbk 13/07/01
			}
		}
		//cate.splice(idx,1);
	}
}

function check_category(){

	var category_code = $('#basic_code').val();

	$.ajax({	
		url : '../product/goods_batch.act.php',
		type : 'POST',
		data : {category_code:category_code,
				mode:'check_category_code'
		},
		dataType: 'HTML',
		error: function(data,error){// 실패시 실행함수 
		alert(error);
		},

		success: function(args){
			if(args == 'Y'){
				$('#check_category_code').css('color','blue');
				$('#check_category_code').html('※ 사용 가능한 카테고리 입니다.');
				$('#category_check_yn').val('1');
			}else{
				$('#check_category_code').html('※ 존재하지 않는 카테고리 입니다.');
			}
		}

	});

}

function showDeliveryFromAjax(type, id){
	if(type == ''){
		var type = $('input[name=delivery_div_show]:checked').val();
	}

	if($('input[name=batch_delivery_type]:checked').val() == 1){
		var company_id = $('#ori_company_id').val();
	}else{
		var company_id = $('#company_id').val();
	}

	$.ajax({ 
		type: 'GET', 
		data: {'act' : 'showDeliveryFromAjax','company_id': company_id,'type': type,'id': id},
		url: './update_delivery_policy.php',  
		dataType: 'html', 
		success: function(result){ 
			$('#delivery_template_area').html(result);
		} 
	}); 
}

function changeDeliveryArea(type, id){
	if(type == 1){
		var company_id = $('#ori_company_id').val();
	}else{
		var company_id = $('#company_id').val();

		if(company_id == ''){
			alert('셀러업체를 선택 후 검색해주시기 바랍니다');
			$('input[name=batch_delivery_type][value=1]').prop('checked', true);
			$('input[name=batch_delivery_type][value='+type+']').prop('checked', false);
			return false;
		}
	}

	$.ajax({ 
		type: 'GET', 
		data: {'act' : 'showDeliveryText','company_id': company_id},
		url: './update_delivery_policy.php',  
		dataType: 'html', 
		success: function(result){ 
			$('#policy_text_retail').html(result);
		} 
	}); 

	showDeliveryFromAjax('', id);

	return;
}

</script>
";


?>