<?
include("../inventory/inventory_goods_query.php");

$Contents =	"
<script  id='dynamic'></script>
<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
			    <td align='left' colspan=4 > ".GetTitleNavigation("재고현황", "재고관리 > 재고현황")."</td>
			</tr>
			<tr>
			    <td align='left' colspan=4 > ".ItemSummary()."</td>
			</tr>
			
			<form name='search_form' id='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='max' id='max' value='$max'>
			<!--input type='hidden' name='sprice' value='0' />
			<input type='hidden' name='eprice' value='1000000' /-->
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
										<td class='input_box_title'>판매상태</td>
										<td class='input_box_item'>
											<input type=checkbox name='status[]' class=nonborder value='1' id='status_1' validation=false title='사용유무' ".CompareReturnValue("1",$status," checked")."><label for='status_1'>판매중</label>
											<input type=checkbox name='status[]' class=nonborder value='0' id='status_0' validation=false title='사용유무' ".CompareReturnValue("0",$status," checked")."><label for='status_0'>일시품절</label>
											<input type=checkbox name='status[]' class=nonborder value='2' id='status_2' validation=false title='사용유무' ".CompareReturnValue("2",$status," checked")."><label for='status_2'>단종(품절)</label>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>품목계정</td>
										<td class='input_box_item' >
											".getItemAccount($item_account)."
										</td>
										<td class='input_box_title'>사용여부</td>
										<td class='input_box_item'>
											<input type=radio name=is_use class=nonborder value='' id='is_use_' validation=true title='사용유무' ".($is_use == "" ? "checked":"")."><label for='is_use_'>전체</label>
											<input type=radio name=is_use class=nonborder value='Y' id='is_use_1' validation=true title='사용유무' ".($is_use == "Y" ? "checked":"")."><label for='is_use_1'>사용</label>
											<input type=radio name=is_use class=nonborder value='N' id='is_use_0' validation=true title='사용유무' ".($is_use == "N" ? "checked":"")."><label for='is_use_0'>사용하지않음</label>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>  <b>검색어</b>  
											<br/>
											<label for='mult_search_use'>(다중검색 체크)</label> <input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
										</td>
										<td class='input_box_item' valign='middle' colspan='3'>
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td>
														<select name='search_type'  style=\"font-size:12px;height:22px;min-width:140px;\">
															<option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type).">대표코드</option>
															<option value='g.gid' ".CompareReturnValue("g.gid",$search_type).">품목코드</option>
															<option value='g.gname' ".CompareReturnValue("g.gname",$search_type).">품목명</option>
															<option value='gu.gu_ix' ".CompareReturnValue("gu.gu_ix",$search_type).">시스템코드</option>
															<option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type).">바코드</option>
														</select>
													</td>
													<td style='padding-left:5px;'>
														<div id='search_text_input_div'>
															<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
														</div>
														<div id='search_text_area_div' style='display:none;'>
															<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
														</div>
													</td>
													<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
												</tr>
											</table>
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
				</form>
			</tr>
			<tr>
				<td style='display:none;'>
					<input type='checkbox' name='view_goods_unit' id='view_goods_unit' value='1' onclick=\"reloadView();\" ".($_COOKIE[view_goods_unit] == 1 ? "checked":"")." >
					<label for='view_goods_unit'> 품목단위별리스트 보기</label>
				</td>
			    <td align='right'  style='padding:5px 0 5px 0;'>
					<span >
					목록수 : <select name='search_max' id='search_max' style=''>
							<option value='5' ".($_GET[max] == '5'?'selected':'').">5</option>
							<option value='10' ".($_GET[max] == '10'?'selected':'').">10</option>
							<option value='20' ".($_GET[max] == '20'?'selected':'').">20</option>
							<option value='30' ".($_GET[max] == '30'?'selected':'').">30</option>
							<option value='50' ".($_GET[max] == '50'?'selected':'').">50</option>
							<option value='100' ".($_GET[max] == '100'?'selected':'').">100</option>
							<option value='500' ".($_GET[max] == '500'?'selected':'').">500</option>
							</select>
					</span>
				
				";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	//$Contents .= "<a href='inventory_goods_list.php?".$_SERVER["QUERY_STRING"]."&mode=update_excel'><img src='../images/".$admininfo["language"]."/btn_excel_modify_save.gif' border=0 align='absmiddle'></a> ";
	//$Contents .= "<a href='inventory_goods_list.php?".$_SERVER["QUERY_STRING"]."&mode=excel'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle'></a>";
}else{
	$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}
$Contents .= "
				</td>
			</tr>
			<tr>";
$Contents .=	"
			<td valign=top colspan='2' style='padding:0px;padding-top:0px;' id=product_stock>
			";
if($_COOKIE[view_goods_unit]==1){

	$innerview = "<div style='overflow-x:hidden;width:100%;'>
			<table cellpadding=0 cellspacing=0  width='100%' class='list_table_box' style='min-width:1100px;'>
			<col width='4%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='*%'>
			<col width='7%'>
			<col width='7%'>
			<col width='10%'>
			<col width='4%'>
			<col width='5%'>
			<col width='7%'>
			<col width='5%'>
			<tr align=center height=30>
				<td class=s_td >순</td>
				<td class=m_td >대표코드</td>
				<td class=s_td >품목코드</td>
				<td class=m_td >시스템코드</td>
				<td class=m_td >이미지/품목명</td>
				<td class=m_td >품목 규격</td>
				<td class=m_td >품목계정</td>
				<td class=m_td >주거래처</td>
				<td class=m_td >단위</td>
				<!--td class=m_td colspan=3 nwrap>주보관장소</td-->
				<td class=m_td >판매상태</td>
				<td class=m_td >등록일</td>
				<td class=e_td >사용여부</td>
			</tr>
			<!--tr align=center height=30>
				<td class=m_td >사업장</td>
				<td class=m_td >창고</td>
				<td class=m_td >보관장소</td>
			</tr-->
			";

if(count($goods_infos) == 0){
	if($mode=="search"){
		$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 해당되는 품목이 없습니다.</td></tr>";
	}else{
		$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 원하시는 품목을 검색해주세요.</td></tr>";
	}
}else{

	$before_pid = "";
	//echo $total;
	//print_r($goods_infos);
	for ($i = 0; $i < count($goods_infos); $i++){
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
					<!--td class='list_box_td list_bg_gray' style='padding:0px 7px;' nowrap>
						".($goods_infos[$i][gcode] ? $goods_infos[$i][gid]:$goods_infos[$i][gid])."<input type=hidden name='gid[]' value='".$goods_infos[$i][gid]."'>
					</td-->
					<td bgcolor=#ffffff>".$no."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][gcode]."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][gid]."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][gu_ix]."</td>
					<td class='list_box_td point' style='padding:2px 2px;' nowrap>
						<table cellpadding=0 cellspacing=0>
							<tr>
								";
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."images/inventory", $goods_infos[$i][gid], "c"))){
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
					<td bgcolor=#ffffff>".$goods_infos[$i][company_name]."</td>
					
					<!--td bgcolor=#ffffff nowrap>".$goods_infos[$i][company_name]."</td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][place_name]."</td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][section_name]."</td-->

					<td bgcolor=#ffffff>".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
					<td bgcolor=#ffffff>".$INVENTORY_GOODS_STATUS[$goods_infos[$i][status]]."</td>
					<td bgcolor=#ffffff  style='padding:0px 5px;' nowrap>".$goods_infos[$i][g_regdate]."</td>
					<td bgcolor=#ffffff>".($goods_infos[$i][is_use] == "1" || $goods_infos[$i][is_use] == "Y" ? "사용":"사용안함")."</td>
				</tr>
				";
	}

}
	$innerview .= "</table>
				</div>
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=40><td></td>
					<td align=right nowrap>".$str_page_bar."</td></tr>

				</table>";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
			";
}else{
$innerview = "<div style='overflow-x:hidden;width:100%;'>
			<form name=stockfrm method=post action='product_stock.act.php' target='act'>
			<input type='hidden' name='act' value='update'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='depth' value='$depth'>
			<table cellpadding=0 cellspacing=0  width='100%' class='list_table_box' style='min-width:1100px;'>
			<col width='4%'>
			<col width='7%'>
			<col width='7%'>
			<col width='*%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='7%'>
			<col width='10%'>
			<tr align=center height=30>
				<td class=s_td>순</td>
				<td class=m_td>대표코드</td>	
				<td class=m_td>품목코드</td>
				<td class=m_td>이미지/품목명</td>
				<td class=m_td>품목 규격</td>
				<td class=m_td>품목계정</td> 
				<td class=m_td>기본단위</td>
				<td class=m_td>등록일</td>
				<td class=m_td>주거래처</td>
				<td class=m_td>판매상태</td>
				<td class=m_td>사용여부</td>
				<td class=e_td>관리</td>
			</tr>

			";

if(count($goods_infos) == 0){
	if($mode=="search"){
		$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 해당되는 품목이 없습니다.</td></tr>";
	}else{
		$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 원하시는 품목을 검색해주세요.</td></tr>";
	}
}else{

	$before_pid = "";
	//echo $total;
	//print_r($goods_infos);
	for ($i = 0; $i < count($goods_infos); $i++)
	{
		$no = $total - ($page - 1) * $max - $i;

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."images/inventory", $goods_infos[$i][gid], "c"))){
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
					<!--td class='list_box_td list_bg_gray' style='padding:0px 7px;' nowrap>
						".($goods_infos[$i][gcode] ? $goods_infos[$i][gid]:$goods_infos[$i][gid])."<input type=hidden name='gid[]' value='".$goods_infos[$i][gid]."'>
					</td-->
					<td bgcolor=#ffffff>".$no."</td>
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
					<td bgcolor=#ffffff>".getUnit($goods_infos[$i][basic_unit], "basic_unit","","text")."</td>
					<td bgcolor=#ffffff  style='padding:0px 5px;' nowrap>".$goods_infos[$i][g_regdate]."</td>
					<td bgcolor=#ffffff  style='padding:0px 5px;' nowrap>".$goods_infos[$i][company_name]."</td>
					<td bgcolor=#ffffff  style='padding:0px 5px;' nowrap>".$INVENTORY_GOODS_STATUS[$goods_infos[$i][status]]."</td>
					<td bgcolor=#ffffff>".($goods_infos[$i][is_use] == "1" || $goods_infos[$i][is_use] == "Y" ? "사용":"사용안함")."</td>
					<td class='list_box_td' align=center style='padding:5px;' nowrap>
						<table border=0 cellpadding=0 cellspacing=0 align=center>
							<tr>
								<td>";
								if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
									$innerview .= "
									<a href='inventory_goods_input.php?mode=copy&gid=".$goods_infos[$i][gid]."'><img src='../images/".$admininfo["language"]."/btc_copy.gif'></a>
									<!--a href=\"javascript:PoPWindow3('../inventory/input_pop.php?gid=".$goods_infos[$i][gid]."',800,700,'input_pop')\"><img src='../images/".$admininfo["language"]."/btn_input.gif'></a>
									<a href=\"javascript:PoPWindow3('../inventory/delivery_pop.php?gid=".$goods_infos[$i][gid]."',900,700,'output_pop')\"><img src='../images/".$admininfo["language"]."/btn_output.gif'></a>
									<a href=\"javascript:PoPWindow3('../inventory/order_pop.php?gid=".$goods_infos[$i][gid]."',800,700,'order_pop')\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a> 
									<a href=\"javascript:PoPWindow3('../inventory/inventory_order.php?gid=".$goods_infos[$i][gid]."&mmode=pop',800,700,'order_pop')\"><img src='../images/".$admininfo["language"]."/btn_depot_move.gif'></a-->
									";
								}else{
									$innerview .= "
									<!--a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_input.gif'></a>
									<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_output.gif'></a>
									<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a><br>
									<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_depot_move.gif'></a-->
									";
								}
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$innerview .= "
		    	<a href=\"inventory_goods_input.php?gid=".$goods_infos[$i][gid]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
}else{
	$innerview .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
}

//if($_SESSION["admininfo"]["charger_ix"] =="forbiz"){
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$innerview .= "
					<a href=\"javascript:InventoryDelete('".$goods_infos[$i][gid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";	
	}else{
		$innerview .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
	}
//}
								$innerview .= "
								</td>
							</tr>
						</table>
					</td>
				</tr>
				";
	}

}
	$innerview .= "</table>
				</form>
				</div>
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=40><td>".($stock_status == "shortage" ? "<a href=\"javascript:PrintWindow('./print_stock.php?$QUERY_STRING',700,900,'print_stock')\">재고 내역서 출력</a>":"")."</td>
					<td align=right nowrap>".$str_page_bar."</td></tr>

				</table>";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
			";
}
$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td   > 각 품목별 및 규격(옵션)별로 재고현황을 보실 수 있습니다
</td></tr>
	<!--tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 매입가나 , 기본 도소매가가 다른 경우는 별도의 품목으로 등록합니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 바코드가 다른 품목은 별도의 품목으로 등록한다. </td></tr-->
</table>
";

$Contents .= HelpBox("품목리스트", $help_text);

//$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";


if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_stock').innerHTML = document.body.innerHTML;
	parent.document.getElementById('select_category_path1').innerHTML='".$inner_category3_path."';
	</Script>";
}else{
	$Script = "
	<script Language='JavaScript' type='text/javascript' src='inventory_goods_input.js'></script>
	<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
	<script Language='JavaScript' type='text/javascript'>



	$(document).ready(function (){

	//다중검색어 시작 2014-04-10 이학봉

		$('input[name=mult_search_use]').click(function (){
			var value = $(this).attr('checked');

			if(value == 'checked'){
				$('#search_text_input_div').css('display','none');
				$('#search_text_area_div').css('display','');
				
				$('#search_text_area').attr('disabled',false);
				$('#search_texts').attr('disabled',true);
			}else{
				$('#search_text_input_div').css('display','');
				$('#search_text_area_div').css('display','none');

				$('#search_text_area').attr('disabled',true);
				$('#search_texts').attr('disabled',false);
			}
		});

		var mult_search_use = $('input[name=mult_search_use]:checked').val();
			
		if(mult_search_use == '1'){
			$('#search_text_input_div').css('display','none');
			$('#search_text_area_div').css('display','');

			$('#search_text_area').attr('disabled',false);
			$('#search_texts').attr('disabled',true);
		}else{
			$('#search_text_input_div').css('display','');
			$('#search_text_area_div').css('display','none');

			$('#search_text_area').attr('disabled',true);
			$('#search_texts').attr('disabled',false);
		}

	//다중검색어 끝 2014-04-10 이학봉
	
	    $('#search_max').on('change',function(){
	       $('#max').val($(this).val());
	       $('#search_form').submit();
	    });

	});


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
	$P->Navigation = "재고관리 > 품목리스트";
	$P->title = "품목리스트";
	$P->strContents = $Contents;



	$P->PrintLayOut();
}



function ItemSummary(){
	return false;
	global $currency_display, $admin_config, $admininfo;
	$mdb = new Database;
//print_r($admininfo["company_id"]);
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));

	if($admininfo[admin_level] == 9){
		$sql = "Select 
				IFNULL(sum(case when g.is_use = '0'  then 1 else 0 end),0) as is_use_N_whole,
				IFNULL(sum(case when g.is_use = '1'  then 1 else 0 end),0) as is_use_Y_whole,
				IFNULL(sum(case when (date_format(g.regdate,'%Y%m%d') =  '".date("Ymd")."' and g.is_use = '1')  then 1 else 0 end),0) as is_use_Y_today,
				IFNULL(sum(case when (date_format(g.regdate,'%Y%m%d') =  '".date("Ymd")."' and g.is_use = '0')  then 1 else 0 end),0) as is_use_N_today,
				IFNULL(sum(case when ('".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."'  <=  date_format(g.regdate,'%Y%m%d')
					and date_format(g.regdate,'%Y%m%d') <= '".date("Ymd")."'  and g.is_use = '1')  then 1 else 0 end),0) as is_use_Y_week,
				IFNULL(sum(case when ('".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."'  <=  date_format(g.regdate,'%Y%m%d')
					and date_format(g.regdate,'%Y%m%d') <= '".date("Ymd")."'  and g.is_use = '0')  then 1 else 0 end),0) as is_use_N_week,
				IFNULL(sum(case when (date_format(g.regdate,'%Y%m') =  '".date("Ym")."' and g.is_use = '1')  then 1 else 0 end),0) as is_use_Y_thismonth,
				IFNULL(sum(case when (date_format(g.regdate,'%Y%m') =  '".date("Ym")."' and g.is_use = '0')  then 1 else 0 end),0) as is_use_N_thismonth
				from inventory_goods g left join inventory_goods_unit gu on gu.gid = g.gid  ";
	/*
					and date_format(od.regdate,'%Y%m%d') <= '".date("Ymd")."' 
				union
				Select 
				IFNULL(sum(case when disp = '1'  then 1 else 0 end),0) as disp_1_whole,
				IFNULL(sum(case when disp = '0'  then 1 else 0 end),0) as disp_0_whole
				from ".TBL_SHOP_ORDER_DETAIL." 
				where date_format(regdate,'%Y%m%d') =  '".date("Ymd")."'
				union
				Select 
				IFNULL(sum(case when disp = '1'  then 1 else 0 end),0) as disp_1_today,
				IFNULL(sum(case when disp = '0'  then 1 else 0 end),0) as disp_0_today
				from ".TBL_SHOP_ORDER_DETAIL." 
				where date_format(regdate,'%Y%m%d') between ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))." and ".date("Ymd")." ";

	$sql = "select g.gid,g.gcode,g.cid,g.gname,g.regdate
			from  inventory_goods g 
			group by g.gid,g.gcode,g.cid,g.gname,g.regdate  ";

	*/
	}else if($admininfo[admin_level] == 8){
		

		$sql = "Select 
				IFNULL(sum(case when g.is_use = 'N'  then 1 else 0 end),0) as is_use_N_whole,
				IFNULL(sum(case when g.is_use = 'Y'  then 1 else 0 end),0) as is_use_Y_whole,
				IFNULL(sum(case when (date_format(g.regdate,'%Y%m%d') =  '".date("Ymd")."' and g.is_use = 'Y')  then 1 else 0 end),0) as is_use_Y_today,
				IFNULL(sum(case when (date_format(g.regdate,'%Y%m%d') =  '".date("Ymd")."' and g.is_use = 'N')  then 1 else 0 end),0) as is_use_N_today,
				IFNULL(sum(case when (date_format(g.regdate,'%Y%m') =  '".date("Ym")."' and g.is_use = 'Y')  then 1 else 0 end),0) as is_use_Y_thismonth,
				IFNULL(sum(case when (date_format(g.regdate,'%Y%m') =  '".date("Ym")."' and g.is_use = 'N')  then 1 else 0 end),0) as is_use_N_thismonth
				from inventory_goods g left join inventory_goods_unit gu on gu.gid = g.gid and company_id = '".$admininfo["company_id"]."'   ";
	}

	$mdb->query($sql);
	$mdb->fetch();
	$item_summary = $mdb->dt;

	$mstring = "<table width=100%  border=0><form name='search_frm' action='".$HTTP_URL."'  method='get' action='act'>
				
				<tr>
					<td align='left' colspan=2 height=100 width='100%' valign=top style='padding-top:5px;'>
					<table cellpadding=3 cellspacing=1 width='100%' border='0' bgcolor=silver>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<col width='8%'>
						<tr height=30  align=center>
							<th bgcolor='#efefef' align='center' colspan=3>총등록품목 </th>
							<th bgcolor='#efefef' colspan=3> 금일 등록 품목수</th>
							<th bgcolor='#efefef' colspan=3> 금주 등록 품목수</th>
							<th bgcolor='#efefef' colspan=3> 금월 등록 품목수</th>
						</tr>
						<tr height=30  bgcolor='#ffffff' align=center>
							<th bgcolor='#efefef' align='center'>총합계 </th>
							<td style='padding-right:15px;'>사용</td>
							<td style='padding-right:15px;'>미사용</td>
							<th bgcolor='#efefef' align='center'>총합계 </th>
							<td style='padding-right:15px;'>사용</td>
							<td style='padding-right:15px;'>미사용</td>
							<th bgcolor='#efefef' align='center'>총합계 </th>
							<td style='padding-right:15px;'>사용</td>
							<td style='padding-right:15px;'>미사용</td>
							<th bgcolor='#efefef' align='center'>총합계 </th>
							<td style='padding-right:15px;'>사용</td>
							<td style='padding-right:15px;'>미사용</td>
						</tr>
						<tr height=30  bgcolor='#ffffff' align=center>
							<th bgcolor='#efefef' align='center'>".number_format($item_summary[is_use_Y_whole]+$item_summary[is_use_N_whole])."</th>
							<td style='padding-right:15px;'>".number_format($item_summary[is_use_Y_whole])."</td>
							<td style='padding-right:15px;'>".number_format($item_summary[is_use_N_whole])."</td>
							<th bgcolor='#efefef' align='center'>".number_format($item_summary[is_use_Y_today]+$item_summary[is_use_N_today])."</th>
							<td style='padding-right:15px;'>".number_format($item_summary[is_use_Y_today])."</td>
							<td style='padding-right:15px;'>".number_format($item_summary[is_use_N_today])."</td>
							<th bgcolor='#efefef' align='center'>".number_format($item_summary[is_use_Y_week]+$item_summary[is_use_N_week])."</th>
							<td style='padding-right:15px;'>".number_format($item_summary[is_use_Y_week])."</td>
							<td style='padding-right:15px;'>".number_format($item_summary[is_use_N_week])."</td>
							<th bgcolor='#efefef' align='center'>".number_format($item_summary[is_use_Y_thismonth]+$item_summary[is_use_N_thismonth])."</th>
							<td style='padding-right:15px;'>".number_format($item_summary[is_use_Y_thismonth])."</td>
							<td style='padding-right:15px;'>".number_format($item_summary[is_use_N_thismonth])."</td>
						</tr>
						";
				/*
				for($i=0;$i<count($datas)+1;$i++){
					
						$z = $i-1;
						$mstring .= "
							<tr height=30  bgcolor='#ffffff' >
								<th bgcolor='#efefef' align='center'>".$datas[$z][day]." </th>
								<td style='padding-right:15px;'> ".number_format($datas[$z][incom_ready_cnt])." 건</td>
								<td style='padding-right:15px;'> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($datas[$z][incom_ready_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
								<td style='padding-right:15px;'> ".number_format($datas[$z][incom_complete_cnt])." 건</td>
								<td style='padding-right:15px;'> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($datas[$z][incom_complete_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							</tr>";
					
				}
				*/
				$mstring .= "
						 
					</table>
					</td>
				</tr>
				<!--tr>
					<td style='padding:5px 0px;text-align:right;'>* 위 통계는 주문일 기준으로 작성 됩니다.</td>
				</tr-->
			</table>";
	return $mstring;
}
?>