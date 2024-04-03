<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("./inventory.lib.php");


if($max == ""){
$max = 200; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if($update_kind == ""){
	$update_kind = 'basic_place';
}

$db = new Database;
$db2 = new Database;

if($admininfo[admin_level] == 9){
	$where = "where g.gid is NOT NULL ";

	if($admininfo[mem_type] == "MD"){
		$where .= " and g.admin in (".getMySellerList($admininfo[charger_ix]).") ";
	}

}else{
	$where = "where g.gid is NOT NULL and g.admin ='".$admininfo[company_id]."' ";
}

if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
	//다중검색 시작 2014-04-10 이학봉
	if($search_text != ""){
		if(strpos($search_text,",") !== false){
			$search_array = explode(",",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			$count_where .= "and ( ";
			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
			$count_where .= ")";
		}else if(strpos($search_text,"\n") !== false){//\n
			$search_array = explode("\n",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			$count_where .= "and ( ";

			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
			$count_where .= ")";
		}else{
			$where .= " and ".$search_type." = '".trim($search_text)."'";
			$count_where .= " and ".$search_type." = '".trim($search_text)."'";
		}
	}

}else{	//검색어 단일검색
	if($search_text != ""){
		if($search_type == "gname_gid"){
			$where .= "and (g.gname LIKE '%".$search_text."%' or g.gid LIKE '%".$search_text."%') ";
		}else{
			$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
		}
	}
}

if($company_id != ""){
	$where .= "and (gbp.company_id = '".$company_id."' or gbp.company_id is null)";
}

if($pi_ix != ""){
	$where .= "and (gbp.pi_ix = '".$pi_ix."' or gbp.pi_ix is null)";
}

if(is_array($cid) && count($cid) > 0){		//품목카테고리 다중검색
	$where .= " and g.cid in (".implode(",",$cid).")";
}

if(is_array($basic_yn) && count($basic_yn) ==1){
	if($basic_yn[0]=="1"){
		$where .= " and gbp.ps_ix is not null";
	}else{
		$where .= " and gbp.ps_ix is null";
	}
}



//if($mode == "batch_search"){
	$sql = "select 
				count(data.gid) as total
			from
			(
				select 
					g.gid
				from 
					inventory_goods g 
					left join inventory_goods_unit gu on (g.gid=gu.gid)
					left join inventory_goods_basic_place gbp on (gu.gid=gbp.gid and gu.gu_ix=gbp.gu_ix)
				$where
			) as data
			";

	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];


	$sql = "select
				data.*,
				ccd.com_name as company_name,
				pi.place_name,
				ps.section_name
			from (
				select 
					g.gid,g.gname,g.gcode,g.barcode,g.item_account,g.standard,gu.unit,gu.gu_ix,gbp.gbp_ix,gbp.company_id,gbp.pi_ix,gbp.ps_ix
				from 
					inventory_goods g 
					left join inventory_goods_unit gu on (g.gid=gu.gid)
					left join inventory_goods_basic_place gbp on (gu.gid=gbp.gid and gu.gu_ix=gbp.gu_ix)
				$where 
					order by g.gid
					LIMIT $start, $max
			) data
			left join common_company_detail ccd on (ccd.company_id = data.company_id)
			left join  inventory_place_info pi on data.pi_ix = pi.pi_ix
			left join  inventory_place_section ps on data.ps_ix = ps.ps_ix
	";

	$db->query($sql);
	$goods_infos = $db->fetchall();
//}
if($mode == "batch_search"){
	if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
	}
	$str_page_bar = page_bar($total, $page, $max, $query_string,"");
}else{
	$str_page_bar = page_bar($total, $page, $max, "&max=$max","");
}


$Contents =	"
<script id='dynamic'></script>
			<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
				<td align='left' colspan=4 > ".GetTitleNavigation("기본보관장소관리", "재고관리 > 기본보관장소관리")."</td>
			</tr>
			<input type='hidden' name='mode' value='batch_search'>
			<tr>
				<td colspan=2>
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
						<tr>
							<td class='box_05 align=center' style='padding:0px'>
								<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
									<col width='18%' >
									<col width='32%' >
									<col width='18%' >
									<col width='32%' >
									<tr>
										<td class='input_box_title'>
											<div style='float:left;padding-top:5px;'><b>품목분류</b></div>
											<div style='float:left;padding-left:20px;'>
												<img src='../images/icon/search_icon.gif' value='검색' onclick=\"ShowModalWindow('./search_category.php?group_code=',600,600,'add_brand_category')\" style='cursor:pointer;'>
											</div>
										</td>
										<td class='input_box_item' colspan=3>
											<div id='selected_category_6' style='padding:10px;overflow-y:scroll;max-height:100px;'>
											<table width='98%' cellpadding='0' cellspacing='0' id='objMd'>
											<colgroup>
												<col width='*'>
												<col width='600'>
											</colgroup>
											<tbody>";
												if(count($cid) > 0){
										
													for($k=0;$k<count($cid);$k++){

														$re_cid = $cid[$k];
														$sql = "select * from ".$tb." where cid = '".$re_cid."'";
														
														$db->query($sql);
														$db->fetch();
														$depth = $db->dt[depth];
														
														for($i=0;$i<=$depth;$i++){
															$this_cid = substr(substr($re_cid, 0,($i*3+3)).'000000000000',0,15);
															$sql = "select * from ".$tb." where cid = '".$this_cid."'";

															$db2->query($sql);
															$db2->fetch();
															$cname = $db2->dt[cname];
															$relation_cname[$k] .= $cname." > ";
														}
									
										$Contents .= "	<tr style='height:26px;' id='row_".$re_cid."'>
															<td>
															<input type='hidden' name='cid[]' id='cid_".$re_cid."' value='".$re_cid."'>".$relation_cname[$k]."</td><td><a href='javascript:void(0)' onclick=\"cid_del('".$re_cid."')\"><img src='../images/korea/btc_del.gif' border='0'></a>
															</td>
														</tr>";
													}
												}
									$Contents .= "
												</tbody>
												</table>
											</div>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>사업장/창고  <img src='/admin/icon/required3.gif'></td>
										<td class='input_box_item'>
											".SelectEstablishment($company_id,"company_id","select","true","onChange=\"loadPlace(this,'pi_ix')\" ")."
											".SelectInventoryInfo($company_id, $pi_ix,'pi_ix','select','true',"")."
										</td>
										<td class='input_box_title'>기본창고 지정여부</td>
										<td class='input_box_item'>
											<input type=checkbox name='basic_yn[]' class=nonborder value='1' id='basic_y' validation=false title='사용유무' ".CompareReturnValue("1",$basic_yn," checked")."><label for='basic_y'>지정</label>
											<input type=checkbox name='basic_yn[]' class=nonborder value='0' id='basic_n' validation=false title='사용유무' ".CompareReturnValue("0",$basic_yn," checked")."><label for='basic_n'>미지정</label>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'><b>검색어</b>
											<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
											<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> <label for='mult_search_use'>(다중검색 체크)</label>
										</td>
										<td class='input_box_item' colspan='3'>
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td valign='top'>
														<div style='padding-top:5px;'>
														<select name='search_type'  style=\"font-size:12px;height:22px;min-width:140px;\">
															<option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type).">대표코드</option>
															<option value='g.gid' ".CompareReturnValue("g.gid",$search_type).">품목코드</option>
															<option value='g.gname' ".CompareReturnValue("g.gname",$search_type).">품목명</option>
															<option value='gu.gu_ix' ".CompareReturnValue("gu.gu_ix",$search_type).">시스템코드</option>
															<option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type).">바코드</option>
														</select>
														</div>
													</td>
													<td style='padding:5px;'>
														<div id='search_text_input_div'>
															<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
														</div>
														<div id='search_text_area_div' style='display:none;'>
															<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
														</div>
													</td>
													<td>
														<div>
															<span class='small blu' > * 다중 검색은 다중 품목코드로 검색 지원이 가능합니다. 구분값은 ',' 혹은 'Enter'로 사용 가능합니다. </span>
														</div>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<th class='box_06'></th>
						</tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08'></td>
							<th class='box_09'></th>
						</tr>
					</table>
				</td>
			</tr>
			</table>

			<table cellpadding=0 cellspacing=0  width='100%'>
			<tr>
				<td colspan=2 align=center style='padding-top:20px;'>
					<input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle>
				</td>
			</tr>
			</table>
			</form>";

$Contents .= "
			<table cellpadding=0 cellspacing=0  width='100%' >
			<tr>
				<td height='30'></td>
			</tr>
			</table>";

$Contents .= "
			<table cellpadding=0 cellspacing=0  width='100%'>
			<tr>
				<td style='padding:5px;'>
					전체 : ".$total." 개
				</td>
				<td align='right'  style='padding:5px 0 5px 0;'>
				</td>
			</tr>
			<tr>
			<td valign=top colspan='2' style='padding:0px;padding-top:0px;' id=product_stock>";


$innerview = "<div style='overflow-x:hidden;width:100%;'>
			<form name=listform method=post action='inventory_goods_batch.act.php' onsubmit='return SelectUpdate(this)' target='act'>
			<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "batch_search" ? urlencode(serialize($_GET)):"")."'>
			<input type='hidden' name='act' value='update'>
			<input type='hidden' name='search_act_total' value='$total'>
			<table cellpadding=0 cellspacing=0  width='100%' class='list_table_box' style='min-width:1100px;'>
			<col width='4%'>
			<col width='4%'>
			<col width='10%'>
			<col width='*'>
			<col width='10%'>
			<!--col width='7%'-->
			<col width='7%'>
			<col width='12%'>
			<col width='12%'>
			<col width='12%'>
			<tr align=center height=30>
				<td class=s_td rowspan=2><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
				<td class=m_td rowspan=2>순번</td>
				<td class=m_td rowspan=2>대표코드<br>품목코드</td>	
				<td class=m_td rowspan=2>이미지/품목명</td>
				<td class=m_td rowspan=2>규격(옵션)</td>
				<!--td class=m_td rowspan=2>품목계정</td--> 
				<td class=m_td rowspan=2>단위</td>
				<td class=e_td colspan=3 nwrap>기본창고보관장소</td> 
			</tr>
			<tr align=center height=30>
				<td class=m_td >사업장</td>
				<td class=m_td >창고</td>
				<td class=m_td >보관장소</td>
			</tr>";


if(count($goods_infos) == 0){
	if($mode == "batch_search"){
		$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 해당되는 품목이 없습니다.</td></tr>";
	}else{
		$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 원하시는 품목을 검색해주세요.</td></tr>";
	}
}else{

	$before_gid = "";

	for($i = 0; $i < count($goods_infos); $i++){
		$gid_cnt[$goods_infos[$i][gid]]++;
	}

	for ($i=0,$j=0; $i < count($goods_infos); $i++)
	{
		$no = $total - ($page - 1) * $max - $j;

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
			$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c");
		}else{
			$img_str = "../image/no_img.gif";
		}

	$innerview .= "<tr height=35 align=center>";
	
	
	$innerview .= "
					<td bgcolor=#ffffff >
						<input type=checkbox class=nonborder id='gid' name='select_unit[".$goods_infos[$i][gid]."|".$goods_infos[$i][unit]."|".$goods_infos[$i][gu_ix]."]' value='".$goods_infos[$i][gbp_ix]."'>
					</td>";

	if($before_gid != $goods_infos[$i][gid]){

	$innerview .= "
					<td bgcolor=#ffffff rowspan='".$gid_cnt[$goods_infos[$i][gid]]."'>".$no."</td>
					<td bgcolor=#ffffff rowspan='".$gid_cnt[$goods_infos[$i][gid]]."'>".$goods_infos[$i][gcode]."<br>".$goods_infos[$i][gid]."</td>
					<td class='list_box_td point' style='padding:2px 2px;' nowrap rowspan='".$gid_cnt[$goods_infos[$i][gid]]."'>
						<table cellpadding=0 cellspacing=0>
							<tr>
								";
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
		$innerview .= "			<td width='40' align=center style='padding:0px 2px;'><img src='".$img_str."' width=30 height=30 style='border:1px solid #eaeaea' align=absmiddle></td>";
		}
		$innerview .= "
								<td class='list_box_td'style='text-align:left; padding-right:10px;line-height:150%;'>
									<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$goods_infos[$i][gid]."',970,800,'inventory_goods_info')\">
										<b>".$goods_infos[$i][gname]."</b>
									</a>
								</td>
							</tr>
						</table>
					</td>
					<td bgcolor=#ffffff nowrap rowspan='".$gid_cnt[$goods_infos[$i][gid]]."'>".$goods_infos[$i][standard]."</td>
					<!--td bgcolor=#ffffff rowspan='".$gid_cnt[$goods_infos[$i][gid]]."'>".$ITEM_ACCOUNT[$goods_infos[$i][item_account]]."</td-->
					";
		$j++;
	}

	$innerview .= "<td bgcolor=#ffffff>".getUnit($goods_infos[$i][unit], "unit","","text")."</td>";

	if($goods_infos[$i][ps_ix]!=""){
		$innerview .= "
					<td bgcolor=#ffffff >
						".$goods_infos[$i][company_name]."
					</td>
					<td bgcolor=#ffffff >
						".$goods_infos[$i][place_name]."
					</td>
					<td bgcolor=#ffffff nowrap>
						".$goods_infos[$i][section_name]."
					</td>";
	}else{
		$innerview .= "
					<td bgcolor=#ffffff colspan='3'>
						기본보관장소가 지정되지 않았습니다.
					</td>";
	}

	$innerview .= "
				</tr>
				";

	$before_gid = $goods_infos[$i][gid];

	}

}
	$innerview .= "</table>
		
				</div>
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=40><td>".($stock_status == "shortage" ? "<a href=\"javascript:PrintWindow('./print_stock.php?$QUERY_STRING',700,900,'print_stock')\">재고 내역서 출력</a>":"")."</td>
					<td align=right nowrap>".$str_page_bar."</td></tr>
				</table>";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>";

//일괄수정 폼 시작
$select = "
	<select name='update_type' >
		<option value='2' selected>선택한 상품 전체에</option>
		<option value='1' >검색한 상품 전체에</option>
	</select>

	<input type='radio' name='update_kind' id='basic_place' value='basic_place' ".CompareReturnValue("basic_place",$update_kind,"checked")." onclick=\"ChangeUpdateForm('basic_place');\"><label for='update_once'> 기본보관장소 설정</label>
";

$help_text .= "
	<div class='update_kind_div' id='basic_place' ".($update_kind == "basic_place"? "style='display:block'":"style='display:none'")." >
		<div style='padding:4px 0 4px 0'>
			<img src='../images/dot_org.gif'> <b>기본보관장소 설정</b> <span class=small style='color:gray'> 변경하고자 하는 기본품목보관장소 정보를 설정한 후 저장 버튼을 눌러 주세요.</span>
		</div>
		<div style='padding:0 0 4px 0'>
			<img src='../images/dot_org.gif'> <span class=small style='color:gray'>사업장 창고별로 보관장소가 변경됩니다.</span>
		</div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='18%'>
	<col width='*'>
	<tr>
		<td class='input_box_title'> <b>기본보관장소 설정</b></td>
		<td class='input_box_item' style='padding-left:10px;'>
			".SelectEstablishment($_SESSION["admin"]["company_id"],"company_id","select","true","onChange=\"loadPlace(this,'pi_ix')\" ")."
			".SelectInventoryInfo($_SESSION["admin"]["company_id"], '','pi_ix','select','true', "onChange=\"loadPlaceSection(this,'ps_ix')\" ")."
			".SelectSectionInfo('','','ps_ix',"select","true" )."
		</td>
	</tr>
	</table>
	</div>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table>";
}else{
	$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>";
}

$Contents .= "".HelpBox($select, $help_text,'300')."</form>";
//일괄수정 폼 끝

$Script = "
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
<script Language='JavaScript' type='text/javascript'>

function SelectUpdate(frm){

if(frm.update_type.value == 1){
	if(parseInt(frm.search_searialize_value.value.length) <= 58){
		alert(language_data['product_list.js']['K'][language]);	//'검색상품 전체에 대한 적용은 검색후 가능합니다.'
		//select_update_unloading();
		return false;
	}
	
	if(confirm(language_data['product_list.js']['G'][language])){//'검색상품 전체에 정보변경을 하시겠습니까?'
		return true;
	}else{
		//select_update_unloading();
		return false;
	}
}else if(frm.update_type.value == 2){

	var pid_checked_bool = false;

	$('input[name^=select_unit]').each(function (){
		var checked = $(this).is(':checked');
		if(checked == true){
			pid_checked_bool = true;
		}
	})

	if(!pid_checked_bool){
		alert(language_data['product_list.js']['H'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
		//select_update_unloading();
		return false;
		
	}
}

if(!CheckFormValue(frm)){
	return false;
}

frm.act.value = 'update';
return true;
//frm.submit();

}

function ChangeUpdateForm(selected_id){

	$('.update_kind_div').hide();
	$('.update_kind_div#'+selected_id).show();
}

function clearAll(frm){
	for(i=0;i < frm.gid.length;i++){
		frm.gid[i].checked = false;
	}
}

function checkAll(frm){
	for(i=0;i < frm.gid.length;i++){
		frm.gid[i].checked = true;
	}
}

function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function cid_del(code){
	$('#row_'+code).remove();
}

function mult_search_use_check(){
	var mult_search_use = $('input[name=mult_search_use]:checked').val();
		
	if(mult_search_use == '1'){
		$('#search_text_input_div').css('display','none');
		$('#search_text_area_div').css('display','');

		$('#search_text_area').attr('disabled',false);
		$('#search_texts').attr('disabled',true);
		$('#search_type option[value=gname_gid]').remove(); 
	}else{
		$('#search_text_input_div').css('display','');
		$('#search_text_area_div').css('display','none');

		$('#search_text_area').attr('disabled',true);
		$('#search_texts').attr('disabled',false);
		if($('#search_type option[value=gname_gid]').length==0){
			$('#search_type').prepend('<option value=gname_gid>품목명+품목코드</option>'); 
		}
	}
}

$(document).ready(function (){

	//다중검색어 시작 2014-04-10 이학봉
	//다중검색어 수정 2014-05-29 홍진영
	mult_search_use_check();
	$('input[name=mult_search_use]').click(function (){
		mult_search_use_check();
	});
	//다중검색어 끝 2014-04-10 이학봉

});

</script>";

$P = new LayOut();
$P->strLeftMenu = inventory_menu();
$P->addScript = $Script;
$P->Navigation = "품목가격관리 > 기본보관장소관리";
$P->title = "기본보관장소관리";
$P->strContents = $Contents;
$P->PrintLayOut();

?>