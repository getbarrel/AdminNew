<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("./inventory.lib.php");
//auth(8);
//print_r($admininfo);
if($max == ""){
$max = 20; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;

if($admininfo[admin_level] == 9){
	$where = "where g.gid Is NOT NULL ";

	if($admininfo[mem_type] == "MD"){
		$where .= " and g.admin in (".getMySellerList($admininfo[charger_ix]).") ";
	}

}else{
	$where = "where g.gid Is NOT NULL and g.admin ='".$admininfo[company_id]."' ";
}

if($search_text != ""){
	if($search_type == "gname_gid"){
		$where .= "and (g.gname LIKE '%".$search_text."%' or g.gid LIKE '%".$search_text."%') ";
	}else{
		$where .= "and g.".$search_type." LIKE '%".$search_text."%' ";
	}
}


if($company_id != ""){
	$where .= "and pi.company_id = '".$company_id."' ";
}

if($pi_ix != ""){
	$where .= "and pi.pi_ix = '".$pi_ix."' ";
}

if($ps_ix != ""){
	$where .= "and ps.ps_ix = '".$ps_ix."' ";
}


if($item_account != ""){
	$where .= "and g.item_account = '".$item_account."' ";
}

/*
if($stock_status == "soldout"){
	$stock_where = "and (stock = 0 or option_stock_yn = 'N') ";
}else if($stock_status == "shortage"){
	$stock_where = "and (stock < safestock or option_stock_yn = 'R') ";
}else if($stock_status == "surplus"){
	$stock_where = "and (stock > safestock or option_stock_yn = 'Y')";
}
*/

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

if ($cid2){
	$where .= " and g.cid LIKE '".substr($cid2,0,$cut_num)."%' ";
}

$sql = "select count(*) as total
		from inventory_goods g 
		left join inventory_goods_unit gu on (g.gid=gu.gid)
		left join  inventory_place_section ps on g.ps_ix = ps.ps_ix
 		$where 
		 $stock_where 
		 ";

//left join inventory_goods_unit gu on g.gid =gi.gid  
//echo $sql;
$db->query($sql);
$db->fetch();
$total = $db->dt[total];
//	echo $db->total;
	//exit;

$orderbyString = "order by g.regdate desc";


$sql = "select data.* ,
	(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name 
	from (
		select g.cid,g.gid, g.gname, g.gcode, g.admin, g.item_account, g.ci_ix, g.pi_ix, g.is_use, g.standard,  date_format(g.regdate,'%Y-%m-%d') as regdate, pi.company_id,  pi.place_name, ps.section_name, gu.unit ,gu.gu_ix, gu.offline_wholesale_price, gu.wholesale_price, gu.wholesale_sellprice, gu.sellprice, gu.discount_price
		from inventory_goods g left join inventory_place_info pi on (g.pi_ix = pi.pi_ix)  
		left join inventory_goods_unit gu on (g.gid=gu.gid)
		left join  inventory_place_section ps on g.ps_ix = ps.ps_ix
		$where
		$stock_where 
		$orderbyString 
		LIMIT $start, $max
	) data
	 ";
$db->query($sql);

$goods_infos = $db->fetchall();

//print_r($_SERVER);
if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
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
<script  id='dynamic'></script>
	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
		<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
			    <td align='left' colspan=4 > ".GetTitleNavigation("재고현황", "재고관리 > 재고현황")."</td>
			</tr>
			<tr height=150>
				<td colspan=2>
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:0px'>
								<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
									<col width='150' >
									<col width='*' >
									<col width='150' >
									<col width='*' >
									<tr>
										<td class='input_box_title'>  <b>선택된 카테고리</b>  </td>
										<td class='input_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getIventoryCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
									</tr>
									<tr>
										<td class='input_box_title'><b>카테고리선택</b></td>
										<td class='input_box_item' colspan=3>
											<table border=0 cellpadding=0 cellspacing=0>
												<tr>
													<td style='padding-right:5px;'>".getInventoryCategoryList("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
													<td style='padding-right:5px;'>".getInventoryCategoryList("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
													<td style='padding-right:5px;'>".getInventoryCategoryList("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
													<td>".getInventoryCategoryList("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>주거래처</td>
										<td class='input_box_item' >
											".SelectSupplyCompany($ci_ix,'ci_ix','select','false')."
										</td>
										<td class='input_box_title'>주보관창고</td>
										<td class='input_box_item'>
											".SelectEstablishment($company_id,"company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
											".SelectInventoryInfo($company_id, $pi_ix,'pi_ix','select','false', "onChange=\"loadPlaceSection(this,'ps_ix')\" ")."
											".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","false" )." 
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>품목계정</td>
										<td class='input_box_item' >
											".getItemAccount($item_account)."
										</td>
										<td class='input_box_title'>사용여부</td>
										<td class='input_box_item'>
											<input type=radio name=disp class=nonborder value='' id='disp_' validation=true title='사용유무' ".($disp == "" ? "checked":"")."><label for='disp_'>전체</label>
											<input type=radio name=disp class=nonborder value=1 id='disp_1' validation=true title='사용유무' ".($disp == "1" ? "checked":"")."><label for='disp_1'>사용</label>
											<input type=radio name=disp class=nonborder value=0 id='disp_0' validation=true title='사용유무' ".($disp == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>  <b>검색어</b>  </td>
										<td class='input_box_item' valign='top' style='padding-right:5px;padding-top:7px;' >
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td><select name='search_type'  style=\"font-size:12px;height:22px;\">
																<option value='gname_gid' ".CompareReturnValue("gname_gid",$search_type).">품목명+품목코드</option>
																<option value='gid' ".CompareReturnValue("gid",$search_type).">품목코드</option>
																<!--option value='gcode'>대표코드</option-->
																<option value='gname' ".CompareReturnValue("gname",$search_type).">품목명</option>
																</select>
																</td>
													<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox' value='".$search_text."' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
													<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
														<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
															<tr height=20>
																<td width=100%  style='padding:0 0 0 5'>
																	<table width=100% cellpadding=0 cellspacing=0 border=0>
																		<tr>
																			<td class='p11 ls1'>검색어 자동완성</td>
																			<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:hand;padding:0 10 0 0' align=right>닫기</td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr height=100% >
																<td valign=top bgColor=#efefef style='padding:0 6 5 6' colspan=2>
																	<table width=100% height=100% bgcolor=#ffffff>
																		<tr>
																			<td valign=top >
																			<div style='POSITION: absolute; overflow-y:auto;HEIGHT: 120px;' id='search_data_area'>
																				<TABLE id=search_table style='table-layout:fixed;'  width=100% cellSpacing=0 cellPadding=1 bgColor=#ffffff border=0>
																				<TBODY id=search_table_body></TBODY>
																				</TABLE>
																			<div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
														</DIV>
													</td>
													<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
												</tr>
											</table>
										</td>
										<td class='input_box_title'><b>목록갯수</b></td>
										<td class='input_box_item'>
											<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
												<option value='5' ".CompareReturnValue(5,$max).">5</option>
												<option value='10' ".CompareReturnValue(10,$max).">10</option>
												<option value='20' ".CompareReturnValue(20,$max).">20</option>
												<option value='50' ".CompareReturnValue(50,$max).">50</option>
												<option value='100' ".CompareReturnValue(100,$max).">100</option>
											</select> <span class='small'>한페이지에 보여질 갯수를 선택해주세요</span>
										</td>
									</tr>
									<!--tr>
										<td class='input_box_title'><b>재고상태</b></td>
										<td class='input_box_item' colspan='3'>
										<input type='radio' name='stock_status' value='whole' id='owhole' ".CompareReturnValue("whole","$stock_status"," checked")."><label for='owhole'>전체</label>
										<input type='radio' name='stock_status' value='soldout' id='osoldout' ".CompareReturnValue("soldout","$stock_status"," checked")."><label for='osoldout'>품절</label>
										<input type='radio' name='stock_status' value='shortage' id='oshortage' ".CompareReturnValue("shortage","$stock_status"," checked")."><label for='oshortage'>부족</label>
										<input type='radio' name='stock_status' value='surplus' id='osurplus' ".CompareReturnValue("surplus","$stock_status"," checked")."><label for='osurplus'>여유</label>
										</td>
									</tr-->
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
			<tr >
				<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
			</tr>
			<tr>
		</table>
	</form>";
$Contents .=	"
	<form name='listform' method='post' action='./inventory_goods_batch.act.php' onsubmit='return SelectUpdate(this);' target='act'>
	<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
	<input type='hidden' id='gu_ix'>
	<table width='100%' cellpadding=0 cellspacing=0>
		<tr height=40><td>전체 : ".$total."</td>
		<td align=right nowrap>".$str_page_bar."</td></tr>
	</table>
	<table cellpadding=0 cellspacing=0 width='100%'>
		<tr>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_stock>
			";

	$innerview = "<div style='overflow-x:hidden;width:100%;'>
			<table cellpadding=0 cellspacing=0  width='100%' class='list_table_box' style='min-width:1100px;'>
			<col width='25px'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='*%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='10%'>
			<col width='15%'>
			<tr align=center height=30>
				<td class=s_td rowspan=2><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
				<td class=m_td rowspan=2>등록일</td>
				<td class=m_td rowspan=2>대표코드</td>
				<td class=s_td rowspan=2>품목코드</td>
				<td class=m_td rowspan=2>이미지/품목명</td>
				<td class=m_td rowspan=2>품목 규격</td>
				<td class=m_td rowspan=2>품목계정</td>
				<td class=m_td rowspan=2>단위</td>
				<td class=m_td colspan=3 nwrap>가격정보</td>
				<td class=e_td rowspan=2>사용여부</td>
			</tr>
			<tr align=center height=30>
				<td class=m_td >오프라인<br/>도매가</td>
				<td class=m_td >도매가<br/>/할인가</td>
				<td class=m_td >소매가<br/>/할인가</td>
			</tr>
			";

if(count($goods_infos) == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 해당되는  품목이 없습니다.</td></tr>";
}else{

	$before_pid = "";
	//echo $total;
	//print_r($goods_infos);
	for ($i = 0; $i < count($goods_infos); $i++)
	{
		$no = $total - ($page - 1) * $max - $i;

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
			$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c");
		}else{
			$img_str = "../image/no_img.gif";
		}
		
		/*
		$sql = "SELECT com_name FROM ".TBL_COMMON_COMPANY_DETAIL." cmd WHERE cmd.company_id = '".$goods_infos[$i][admin]."' ";
		$db->query($sql);
		$db->fetch();
		$com_name = $db->dt[com_name];
		*/

	$innerview .= "<tr height=35 align=center>
					<td bgcolor=#ffffff><input type='checkbox' name='gu_ix[]' id='gu_ix' value='".$goods_infos[$i][gu_ix]."'></td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][regdate]."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][gcode]."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][gid]."</td>
					<td class='list_box_td point' style='padding:2px 2px;' nowrap>
						<table cellpadding=0 cellspacing=0>
							<tr>
								";
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
		$innerview .= "<td width='40' align=center style='padding:0px 2px;'><img src='".$img_str."' width=30 height=30 style='border:1px solid #eaeaea' align=absmiddle></td>";
		}
		$innerview .= "
								<td  class='list_box_td'style='text-align:left; padding-right:10px;line-height:150%;'>
								<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$goods_infos[$i][gid]."',970,800,'inventory_goods_info')\"><b>".$goods_infos[$i][gname]."</b></a>
								</td>
							</tr>
						</table>
					</td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][standard]."</td>
					<td bgcolor=#ffffff>".$ITEM_ACCOUNT[$goods_infos[$i][item_account]]."</td>
					<td bgcolor=#ffffff>".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
					<td bgcolor=#ffffff style='padding:0 3px;' nowrap><input type='text' class='number' style='width:60px;' name='offline_wholesale_price[".$goods_infos[$i][gu_ix]."]' value='".$goods_infos[$i][offline_wholesale_price]."' onkeyup='this.value=filterNum(this.value);' /></td>

					<td bgcolor=#ffffff style='padding:0 3px;' nowrap><input type='text' class='number' style='width:60px;' name='wholesale_price[".$goods_infos[$i][gu_ix]."]' value='".$goods_infos[$i][wholesale_price]."' onkeyup='this.value=filterNum(this.value);' /><br/><input type='text' class='number' style='width:60px;' name='wholesale_sellprice[".$goods_infos[$i][gu_ix]."]' value='".$goods_infos[$i][wholesale_sellprice]."' onkeyup='this.value=filterNum(this.value);' /></td>

					<td bgcolor=#ffffff style='padding:0 3px;' nowrap><input type='text' class='number' style='width:60px;' name='sellprice[".$goods_infos[$i][gu_ix]."]' value='".$goods_infos[$i][sellprice]."' onkeyup='this.value=filterNum(this.value);' /><br/><input type='text' class='number' style='width:60px;' name='discount_price[".$goods_infos[$i][gu_ix]."]' value='".$goods_infos[$i][discount_price]."' onkeyup='this.value=filterNum(this.value);' /></td>

					<td bgcolor=#ffffff>".($goods_infos[$i][is_use] == "1" || $goods_infos[$i][is_use] == "Y" ? "사용":"사용안함")."</td>
				</tr>
				";
	}

}
	$innerview .= "</table>
				</div>";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=40><td></td>
			<td align=right nowrap>".$str_page_bar."</td></tr>
		</table>";

$help_text = "
<div style='z-index:-1;position:absolute;' id='select_update_parent_save_loading'>
<div style='width:700px;height:200px;display:block;position:relative;z-index:10;text-align:center;' id='select_update_save_loading'></div>
</div>
<div id='batch_update_price'>
<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b>품목 가격 변경</b> <span class=small style='color:gray'><br/> - 변경하시고자 하는 가격정보를 설정한 후 저장 버튼을 클릭해주세요<br/> - 입력필드에 미입력시 기존 가격은 변경되지 않습니다.</span></div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>가격수정 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='update_kind' id='kind_price_each' value='price_each' onclick=\"$('.kind_price_tr').hide();\" checked><label for='kind_price_each'>개별가격수정</label>
			<input type='radio' name='update_kind' id='kind_price_batch' value='price_batch' onclick=\"$('.kind_price_tr').hide();$('.price_batch_tr').show();\"><label for='kind_price_batch'>일괄가격수정</label>
			<input type='radio' name='update_kind' id='kind_price_coprice' value='price_coprice' onclick=\"$('.kind_price_tr').hide();$('.price_coprice_tr').show();\"><label for='kind_price_coprice'>공급가대비가격수정</label>
		</td>
	</tr>
	<tr height=30 style='display:none;' class='kind_price_tr price_batch_tr'>
		<td class='input_box_title'> <b>일괄 가격수정</b></td>
		<td class='input_box_item'>
			▶ 오프라인 도매가 : <input type='text' class='number' style='width:60px;' name='batch_offline_wholesale_price' onkeyup='this.value=filterNum(this.value);' /> &nbsp;
			▶ 도매가 : <input type='text' class='number' style='width:60px;' name='batch_wholesale_price' onkeyup='this.value=filterNum(this.value);' /> &nbsp;
			▶ 도매가 할인가 : <input type='text' class='number' style='width:60px;' name='batch_wholesale_sellprice' onkeyup='this.value=filterNum(this.value);' /> &nbsp;
			▶ 소매가 : <input type='text' class='number' style='width:60px;' name='batch_sellprice' onkeyup='this.value=filterNum(this.value);' /> &nbsp;
			▶ 소매가 할인가 : <input type='text' class='number' style='width:60px;' name='batch_discount_price' onkeyup='this.value=filterNum(this.value);' /> 
		</td>
	</tr>
	<tr height=30 style='display:none;' class='kind_price_tr price_coprice_tr'>
		<td class='input_box_title'> <b>도매/소매가 수정</b></td>
		<td class='input_box_item' style='padding:3px 3px;'>
			▶ 도매가 = 공급가 * <input type='text' class='number' style='width:60px;' name='wholesale_price_value' onkeyup='this.value=filterNum(this.value);' /> % &nbsp;
			▶ 도매할인가 = 도매가에 <input type='text' class='number' style='width:60px;' name='wholesale_sellprice_value' onkeyup='this.value=filterNum(this.value);' />  ".sell_select_box("wholesale_sellprice_type")." 할인 &nbsp;<br/>
			▶ 소매가 = 공급가 * <input type='text' class='number' style='width:60px;' name='sellprice_value' onkeyup='this.value=filterNum(this.value);' /> % &nbsp;
			▶ 소매할인가 = 소매가에 <input type='text' class='number' style='width:60px;' name='discount_price_value' onkeyup='this.value=filterNum(this.value);' />  ".sell_select_box("discount_price_type")." 할인 &nbsp;
		</td>
	</tr>
	</table>";
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
$help_text .= "
</div>";

$select = "
<select name='update_type' >
	<option value='2'>선택한 품목 전체에</option>
	<option value='1' selected>검색한 품목 전체에</option>
</select>
<input type='radio' id='update_kind_display' onclick=\"ChangeUpdateForm('batch_update_price');\" checked><label for='update_kind_display'>가격정보 수정</label>
";


$Contents .= HelpBox($select, $help_text, 250)."</form>";


if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_stock').innerHTML = document.body.innerHTML;
	parent.document.getElementById('select_category_path1').innerHTML='".$inner_category3_path."';
	</Script>";
}else{
	$Script = "<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
	<script Language='JavaScript' type='text/javascript'>
	
	function SelectUpdate(frm){

		if($('input:radio[name^=update_kind]:checked').val() == 'price_each'){
			if(frm.update_type.value == 1){
				alert('개별가격수정은 선택한 품목만 가능합니다.');
				return false;
			}
		}

		SelectUpdateLoading();
		
		
		if(frm.update_type.value == 1){
			if(parseInt(frm.search_searialize_value.value.length) <= 58){
				alert('검색한 품목 전체에 대한 적용은 검색후 가능합니다.');
				select_update_unloading();
				return false;
			}
			
			if(confirm('검색 품목 전체에 정보변경을 하시겠습니까?')){
				return true;
			}else{
				select_update_unloading();
				return false;
			}
		}else if(frm.update_type.value == 2){
			var gu_ix_checked_bool = false;
			var gu_ix_obj=document.getElementsByName('gu_ix[]');
			for(i=0;i < gu_ix_obj.length;i++){
				if(gu_ix_obj[i].checked){
					gu_ix_checked_bool = true;
				}
			}

			if(!gu_ix_checked_bool){
				alert('선택된 품목이 없습니다. 변경하시고자 하는 품목을 선택하신 후 저장 버튼을 클릭해주세요');
				select_update_unloading();
				return false;
			}
		}
		return true;
	}

	function SelectUpdateLoading(){

		document.getElementById('select_update_parent_save_loading').style.zIndex = '1';
		with (document.getElementById('select_update_save_loading').style){

			width = '100%';
			height = '179px';
			backgroundColor = '#ffffff';
			filter = 'Alpha(Opacity=70)';
			//border = '1px solid red';
			opacity = '0.8';
			//left = '-20px';
			//top = '-14px';
		}

		var obj = document.createElement('div');
		with (obj.style){
			position = 'relative';
			zIndex = 100;
		}
		obj.id = 'select_update_loadingbar';

		obj.innerHTML = \"<table width=100% height=100%><tr><td valign=middle align=center><img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 상품정보를 변경중입니다. 잠시만 기다려주세요.</td></tr></table>\";

		document.getElementById('select_update_save_loading').appendChild(obj);

		document.getElementById('select_update_save_loading').style.display = 'block';
	}

	function select_update_unloading(){

		parent.document.getElementById('select_update_parent_save_loading').style.zIndex = '-1';
		parent.document.getElementById('select_update_loadingbar').innerHTML ='';
		parent.document.getElementById('select_update_save_loading').innerHTML ='';
		parent.document.getElementById('select_update_save_loading').style.display = 'none';
	}

	function ChangeUpdateForm(selected_id){
		var area = new Array('batch_update_price');

		for(var i=0; i<area.length; ++i){
			if(area[i]==selected_id){
				document.getElementById(selected_id).style.display = 'block';
			}else{
				document.getElementById(area[i]).style.display = 'none';
			}
		}
	}

	function clearAll(frm){
		for(i=0;i < frm.gu_ix.length;i++){
				frm.gu_ix[i].checked = false;
		}
	}

	function checkAll(frm){
		for(i=0;i < frm.gu_ix.length;i++){
				frm.gu_ix[i].checked = true;
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
	
	function filterNum(str) {
		if(str){
			return str.replace(/[^0-9]/g, '');
		}else{
			return '';
		}
	}

	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var depth = sel.getAttribute('depth');

		//빈값일 경우에는 카테고리 정보 불러오는 파일에서 처리함 kbk 13/08/08
		//if(sel.selectedIndex!=0) {
			window.frames['act'].location.href = 'inventory_category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//}

	}
	</script>";

	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 일괄품목수정리스트";
	$P->title = "일괄품목수정리스트";
	$P->strContents = $Contents;



	$P->PrintLayOut();
}

function sell_select_box ($name){
	return "
		<select name='".$name."' />
			<option vlaue='R'>율(%)</option>
			<option vlaue='M'>원</option>
		</select>
	";
}

?>