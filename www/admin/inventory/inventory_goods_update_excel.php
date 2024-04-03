<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");

$page_type = "update_download";
if($_COOKIE["inventory_update_limit"]){
	$max = $_COOKIE["inventory_update_limit"]; //페이지당 갯수
}else{
	$max = 10;
}

if ($page == '')
{
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;
$db2 = new Database;

if(!$up_mode){
	$up_mode="new_upload";
}

$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
				<td align='left' colspan=4 > ".GetTitleNavigation("대량상품등록", "상품관리 > 대량엑셀수정")."</td>
			</tr>
			<tr>
				<td align='left' colspan=4 style='padding-bottom:15px;'>
					<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_02' ".($up_mode=="new_upload" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?up_mode=new_upload'\">품목 엑셀수정</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($up_mode=="download" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?up_mode=download'\">품목 엑셀다운로드 하기</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td class='btn'>
						</td>
					</tr>
					</table>
					</div>
				</td>
			</tr>
			</table>";

if($up_mode == "download"){		//품목리스트 및 엑셀 다운 

include("../inventory/inventory_goods_query.php");

$Contents .="
	<table cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td align='left' colspan=4 > ".GetTitleNavigation("재고현황", "재고관리 > 재고현황")."</td>
	</tr>

	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='up_mode' value='download'>
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
									<input type=radio name=disp class=nonborder value='' id='disp_' validation=true title='사용유무' ".($disp == "" ? "checked":"")."><label for='disp_'>전체</label>
									<input type=radio name=disp class=nonborder value=1 id='disp_1' validation=true title='사용유무' ".($disp == "1" ? "checked":"")."><label for='disp_1'>사용</label>
									<input type=radio name=disp class=nonborder value=0 id='disp_0' validation=true title='사용유무' ".($disp == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
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
	</table>

			<div style='overflow-x:hidden;width:100%;'>
			<form name='listform' method='post' action='inventory_goods_update_exceldown.php' onsubmit='return SelectUpdate(this)' target='act'>
			<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" || $_GET[mode] == "excel_search" ? urlencode(serialize($_GET)):"")."'>
			<input type='hidden' name='act' value='update'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='depth' value='$depth'>

			<table cellpadding=0 cellspacing=1 border=0 width=100% align='center'>
			<tr>
				<td valign=top style='padding:0px;padding-top:0px;' >
				<table width='100%' cellpadding=0 cellspacing=0 border=0>
				<col width=15%>
				<col width=69%>
				<col width=7%>
				<col width=9%>
				<tr>
					<td>
						상품수 : ".number_format($total)." 개
					</td>
					<td align='right'>
						<select name='update_type'>
							<option value='2' selected>선택한 상품 전체에</option>
							<option value='1'>검색한 상품 전체에</option>
						</select>
					</td>
					<td align='right'>";

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= "<input type='image' src='../images/".$admininfo["language"]."/btn_excel_save.gif' style='cursor:pointer;'>";
			}else{
			$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
			}

		$Contents .= "
					</td>
					<td align='right'  style='padding:5px 0 5px 0;'>
						<span >
						목록수 : <select name='max' id='max' style=''>
								<option value='5' ".($_COOKIE[inventory_goods_max_limit] == '5'?'selected':'').">5</option>
								<option value='10' ".($_COOKIE[inventory_goods_max_limit] == '10'?'selected':'').">10</option>
								<option value='20' ".($_COOKIE[inventory_goods_max_limit] == '20'?'selected':'').">20</option>
								<option value='30' ".($_COOKIE[inventory_goods_max_limit] == '30'?'selected':'').">30</option>
								<option value='50' ".($_COOKIE[inventory_goods_max_limit] == '50'?'selected':'').">50</option>
								<option value='100' ".($_COOKIE[inventory_goods_max_limit] == '100'?'selected':'').">100</option>
								<option value='500' ".($_COOKIE[inventory_goods_max_limit] == '500'?'selected':'').">500</option>
								<option value='1000' ".($_COOKIE[inventory_goods_max_limit] == '1000'?'selected':'').">1000</option>
								<option value='1500' ".($_COOKIE[inventory_goods_max_limit] == '1500'?'selected':'').">1500</option>
								</select>
						</span>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>

			<table cellpadding=0 cellspacing=0  width='100%' class='list_table_box' style='min-width:1100px;'>
			<col width='3%'>
			<col width='4%'>
			<col width='7%'>
			<col width='7%'>
			<col width='*'>

			<col width='7%'>
			<col width='8%'>
			<col width='6%'>
			<col width='7%'>

			<col width='7%'>
			<col width='6%'>
			<col width='7%'>
			<col width='10%'>

			<tr align=center height=35>
				<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
				<td class=s_td>순번</td>
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
			</tr>";

if(count($goods_infos) == 0){
	if($mode=="search"){
		$Contents .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 해당되는 품목이 없습니다.</td></tr>";
	}else{
		$Contents .= "<tr bgcolor=#ffffff height=50><td colspan=14 align=center> 원하시는 품목을 검색해주세요.</td></tr>";
	}
}else{

	for ($i = 0; $i < count($goods_infos); $i++)
	{
		$no = $total - ($page - 1) * $max - $i;

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
			$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c");
		}else{
			$img_str = "../image/no_img.gif";
		}

	$Contents .= "<tr height=35 align=center>
					<td bgcolor=#ffffff><input type=checkbox class=nonborder id='cpid' name=select_pid[] value='".$goods_infos[$i][gid]."'></td>
					<td bgcolor=#ffffff>".$no."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][gcode]."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][gid]."</td>
					<td class='list_box_td point' style='padding:2px 2px;' >
						<table cellpadding=0 cellspacing=0>
							<tr>";

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
			$Contents .= "		<td width='40' align=center style='padding:0px 2px;'><img src='".$img_str."' width=30 height=30 style='border:1px solid #eaeaea' align=absmiddle></td>";
		}

		
		$Contents .= "			<td  class='list_box_td'style='text-align:left; padding-right:10px;line-height:150%;'>
								<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$goods_infos[$i][gid]."',970,800,'inventory_goods_info')\"><b>".$goods_infos[$i][gname]."</b></a>
								</td>
							</tr>
						</table>
					</td>
					<td bgcolor=#ffffff >".$goods_infos[$i][standard]."</td>
					<td bgcolor=#ffffff>".$ITEM_ACCOUNT[$goods_infos[$i][item_account]]."</td>
					<td bgcolor=#ffffff>".getUnit($goods_infos[$i][basic_unit], "basic_unit","","text")."</td>
					<td bgcolor=#ffffff  style='padding:0px 5px;' >".$goods_infos[$i][regdate]."</td>
					<td bgcolor=#ffffff  style='padding:0px 5px;' >".$goods_infos[$i][company_name]."</td>
					<td bgcolor=#ffffff  style='padding:0px 5px;' >".$INVENTORY_GOODS_STATUS[$goods_infos[$i][status]]."</td>
					<td bgcolor=#ffffff>".($goods_infos[$i][is_use] == "1" || $goods_infos[$i][is_use] == "Y" ? "사용":"사용안함")."</td>
					<td class='list_box_td' align=center style='padding:5px;' >
						<table border=0 cellpadding=0 cellspacing=0 align=center>
							<tr>
								<td>";

					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						$Contents .= "	<a href=\"inventory_goods_input.php?gid=".$goods_infos[$i][gid]."\" target='_blank'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
					}else{
						$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
					}
					$Contents .= "&nbsp;";
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						$Contents .= "<a href=\"javascript:InventoryDelete('".$goods_infos[$i][gid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";	
					}else{
						$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
					}

			$Contents .= "
								</td>
							</tr>
						</table>
					</td>
				</tr>
				";
	}

}

$Contents .= "</table>
			</form>
			</div>

			<table width='100%' cellpadding=0 cellspacing=0>
				<td align=right nowrap>".$str_page_bar."</td></tr>
			</table>";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr>
		<td valign=top>
			<img src='/admin/image/icon_list.gif' ></td><td   > 각 품목별 및 규격(옵션)별로 재고현황을 보실 수 있습니다
		</td>
	</tr>
</table>";

$Contents .= HelpBox("품목리스트", $help_text);


}else{	//상품 엑셀 등록 수정 시작

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || true){

		if($admininfo[mall_type] == "BW"){
			$download_excel_file = "batch_product_upload_example_wholesale.xls";
		}else{
			$download_excel_file = "batch_product_upload_example.xls";
		}

$Contents .="
			<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
				<td colspan=3>

					<form name='excel_input_form' method='post' action='inventory_goods_input_excel.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''><!--iframe_act-->
					<input type='hidden' name='act' value='".($up_mode == "new_upload" ? "new_excel_input":"excel_input")."'>
					<input type='hidden' name='cid' value=''>
					<input type='hidden' name='depth' value=''>
					<input type='hidden' name='page_type' value='update'>

					<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
					<col width=18%>
					<col width=*>
					<tr height=30 align=center>
						<td class='input_box_title' ><b>엑셀파일 입력</b></td>
						<td class='input_box_item'>
							<input type=file class='textbox' name='excel_file' style='height:22px;width:200px;' validation=true title='엑셀파일 입력'>
							※ 	batch_goods_upload_example.xls ( 엑셀 저장시 97~03년 양식으로 저장하시고 등록하세요.)
						</td>
					</tr>
					<tr height=30 align=center>
						<td class='input_box_title' ><b>품목 이미지 입력</b></td>
						<td class='input_box_item'>
							<input type=file class='textbox' name='goods_img_file' style='height:22px;width:200px;' validation=false filetype='zip' title='상품이미지 입력'>
							※ batch_goods_image.zip ( zip 파일로 압축하여 저장하세요.)
						</td>
					</tr>
					</table>

					<table width='100%' border=0 cellpadding=0 cellspacing=1>
					<tr height=20>
						<td style='padding:6px;line-height:140%;' colspan=2>
							<div>
							<ol>
								<li>
									<img src='../image/emo_3_15.gif' border=0 align=absmiddle>
									엑셀정보에는 <b>' (따옴표)</b>는 사용하실수 없습니다. <b> 엑셀정보내에 카테고리 정보를 등록해 놓으면 해당 카테고리로 상품이 자동등록됩니다.</b><!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."-->
								</li>
								<li>
									<img src='../image/emo_3_15.gif' border=0 align=absmiddle>
									<span class='red'>주의사항</span>
								</li>
								<li style='padding-left:20px;'>
									<span class='red'>
									1)	대량엑셀수정은 검색하신 품목을 엑셀로 다운로드 받으시고 수정이 필요한 항목을 수정을 하시고 수정 불필요한 항목은 다운로드 받으신 내용을 수정하지 마시고 다시 엑셀로 업로드 하시면됩니다. 
									<br/>2)	만약 필수 항목에 빈 값으로 엑셀로 다운로드 되어 있을 경우는 필수값을 입력해주셔야 합니다.</span>
								</li>
							</ol>
							</div>

						</td>
					</tr>
					<tr height=30>
						<td colspan=2 style='padding:10px 0px;' align=center>
							<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>
						</td>
					</tr>
					</table>

					</form>
				</td>
			</tr>";
}


$Contents .= "
			<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'>
					".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 업로드 엑셀정보 </b>&nbsp;</div>")."
				</td>
			</tr>
			<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'>
					<div style='width:1400px;height:300px;overflow:auto;'>".MakeUploadExcelData()."</div>
				</td>
			</tr>
			<tr>
				<td colspan=3 align=center style='padding-bottom:10px;'>
					<img src='../image/goods_d_btn1.gif' alt='상품등록하기' onclick=\"UploadExcelGoodsReg('update');\" style='cursor:pointer;'/></div></div>
				</td>
			</tr>";


$Contents .= "
			<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 상품 리스트 :</b>&nbsp;<b id='select_category_path2'>전체(".number_format($total)."개)</b></div>")."</td>
			</tr>
			<tr>
				<td valign=top style='padding-top:33px;'></td>
				<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

				$innerview = "
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td>
						<table cellpadding=0 cellspacing=0>
							<tr>
								<td>
								<table cellpadding=0 cellspacing=0>
									<tr>
										<td>".CompanyList($company_id,"","max",$max."&up_mode=".$up_mode)."</td>
										<td style='padding-left:5px;'>
											<select style='height:20px;' name=max onchange=\"window.frames['act'].location.href='".$HTTP_URL."?up_mode=".$up_mode."&view=innerview&company_id=".$company_id."&max='+this.value\">
											<option value='10' ".CompareReturnValue(10,$max).">10</option>
											<option value='20' ".CompareReturnValue(20,$max).">20</option>
											<option value='50' ".CompareReturnValue(50,$max).">50</option>
											<option value='100' ".CompareReturnValue(100,$max).">100</option>
											</select> 씩 보기
										</td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
					</td>
					<td align=right></td>
				</tr>
				</table>";

$innerview .= "	
				<form name=listform method=post action='inventory_goods_update_excel.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
				<input type='hidden' name='act' value='select_delete'>
				<table cellpadding=2 cellspacing=0 bgcolor=gray width=100%  class='list_table_box'>
				<col width='3%' >
				<col width='7%' >
				<col width='7%'>
				<col width='*'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='7%'>
				<col width='7%'>
				<tr bgcolor='#ffffff' align=center height=30>
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>대표코드</td>
					<td class=m_td>품목코드</td>
					<td class=m_td>이미지/품목명</td>
					<td class=m_td>품목 규격</td>
					<td class=m_td>품목계정</td>
					<td class=m_td>기본단위</td>
					<td class=m_td>등록일</td>
					<td class=m_td>사용여부</td>
					<td class=e_td>관리</td>
				</tr>";

		$mode = "search";
		include("../inventory/inventory_goods_query.php");

		if($mode == "search"){
			$str_page_bar = page_bar_search($total, $page, $max, "&max=$max&company_id=$company_id&cid=$cid&depth=$depth");
		}else{
			$str_page_bar = page_bar($total, $page,$max, "&max=$max&company_id=$company_id&cid=$cid&depth=$depth");
		}

		if($db->total == 0){
			$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=10 align=center> 등록된 제품이 없습니다.</td></tr>";
		}else{
			for ($i = 0; $i < count($goods_infos); $i++){

			//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$db->dt[id].".gif")){
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c", $goods_infos[$i])) || $image_hosting_type=='ftp') {
				$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c", $goods_infos[$i]);
			}else{
				$img_str = "../image/no_img.gif";
			}

$innerview .= "	<tr bgcolor='#ffffff'>
					<td class='list_box_td list_bg_gray'><input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$goods_infos[$i][gid]."'></td>
					<td class='list_box_td list_bg_gray' nowrap>".$goods_infos[$i][gcode]."</td>
					<td class='list_box_td list_bg_gray'>".$goods_infos[$i][gid]."</td>
					<td class='list_box_td point' style='text-align:left;line-height:140%;'>
						<table>
							<tr>
								<td><a href='inventory_goods_input.php?gid=".$goods_infos[$i][gid]."' class='screenshot'  rel='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], 'b', $goods_infos[$i])."'  ><img src='".$img_str ."' width=50 height=50></a></td>
								<td>
									".getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4)."<br>
									<a href='inventory_goods_input.php?gid=".$goods_infos[$i][gid]."'><b>".$goods_infos[$i][gname]."</b></a>
								</td>
							</tr>
						</table>
					</td>
					<td class='list_box_td list_bg_gray'>".$goods_infos[$i][standard]."</td>
					<td class='list_box_td list_bg_gray'>".$ITEM_ACCOUNT[$goods_infos[$i][item_account]]."</td>
					<td class='list_box_td list_bg_gray'>".getUnit($goods_infos[$i][basic_unit], "basic_unit","","text")."</td>
					<td class='list_box_td list_bg_gray'  style='padding:0px 5px;' nowrap>".$goods_infos[$i][regdate]."</td>
					<td class='list_box_td list_bg_gray'>".($goods_infos[$i][is_use] == "1" || $goods_infos[$i][is_use] == "Y" ? "사용":"사용안함")."</td>
					<td class='list_box_td ' >";
							if(checkMenuAuth(md5("/admin/inventory/inventory_goods_input_excel.php"),"U")){
								$innerview .= "
								<a href='inventory_goods_input_excel.php?id=".$db->dt[id]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>";
							}else{
								$innerview .= "
								<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>";
							}
							if(checkMenuAuth(md5("/admin/inventory/inventory_goods_input_excel.php"),"D")){
								$innerview .= "
								<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"deleteProduct('delete_excel','".$db->dt[id]."')\">";
							}else{
								$innerview .= "
								<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle ></a>";
							}
			$innerview .= "
					</td>
				</tr>";

			}
		}

$innerview .= "	</table>
				<table width='100%'>
					<tr height=30>";
						if(checkMenuAuth(md5("/admin/inventory/inventory_goods_input_excel.php"),"D")){
							$innerview .= "<td><input type=image src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></td>";
						}else{
							//$innerview .= "<td><a href=\"".$auth_delete_msg."\"><img src='../image/bt_all_del.gif' border=0 align=absmiddle ></a></td>";
						}
$innerview .= "
					<td align=right>".$str_page_bar."</td></tr>
				</table>
				</form>";
}// up_mode == upload 일때


$Contents .= "
				<form name=vieworderform method=post action='./order.act.php'>
				<input type='hidden' name='vieworder'>
				<input type='hidden' name='_vieworder'>
				<input type='hidden' name='pid'>
				<input type='hidden' name='cid' value='$cid'>
				<input type='hidden' name='category_load' value='$category_load'>
				<input type='hidden' name='depth' value='$depth'>
				</form>

				</td>
				</tr>
			</table>";

if($up_mode=='upload'||$up_mode==''){
	$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
}else{
	$category_str ="";
}


$script .= "
<script language='javascript' src='/admin/js/jquery.form.js'></script>
<script Language='JavaScript' type='text/javascript' src='inventory_goods_input.js'></script>
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>

<Script language='javascript'>
	$(document).ready(function (){

		$('#max').change(function(){
			var value= $(this).val();
			$.cookie('inventory_update_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
			document.location.reload();
		});

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

	});

function cid_del(code){
	$('#row_'+code).remove();
}


function SelectUpdate(frm){

	//alert(frm.search_searialize_value.value.length);
	
	if(frm.update_type.value == 1){
		if(parseInt(frm.search_searialize_value.value.length) <= 58){
			alert('검색상품 전체에 대한 적용은 검색후 가능합니다.');	//'검색상품 전체에 대한 적용은 검색후 가능합니다.'
			select_update_unloading();
			return false;
		}
		
		if(confirm('검색상품 전체에 정보를 다운받으시겠습니까?')){//'검색상품 전체에 정보변경을 하시겠습니까?'
			return true;
		}else{
			select_update_unloading();
			return false;
		}
	}else if(frm.update_type.value == 2){
		var pid_checked_bool = false;
		var pid_obj=document.getElementsByName('select_pid[]');//kbk
		//for(i=0;i < frm.cpid.length;i++){//kbk
		for(i=0;i < pid_obj.length;i++){
			//if(frm.cpid[i].checked){//kbk
			if(pid_obj[i].checked){
				pid_checked_bool = true;
			}
			//	frm.cpid[i].checked = false;
		}
		if(!pid_checked_bool){
			alert('선택된 제품이 없습니다. 다운받을 상품을 선택하신 후 엑셀저장 버튼을 클릭해주세요');//'선택된 제품이 없습니다. 다운받을 상품을 선택하신 후 엑셀저장 버튼을 클릭해주세요'
			select_update_unloading();
			return false;
		}
	}

	//return false;
	frm.act.value = 'update';
	return true;
	//frm.submit();
	
}

function select_update_unloading(){
	//parent.document.getElementById('select_update_parent_save_loading').style.zIndex = '-1';
	//parent.document.getElementById('select_update_loadingbar').innerHTML ='';
	//parent.document.getElementById('select_update_save_loading').innerHTML ='';
	//parent.document.getElementById('select_update_save_loading').style.display = 'none';
}

$(document).ready(function (){

	$('#max').change(function(){
		var value= $(this).val();
		$.cookie('inventory_goods_max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
		document.location.reload();
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

function reloadView(){
	if($('#view_goods_unit').attr('checked') == true || $('#view_goods_unit').attr('checked') == 'checked'){		
		$.cookie('view_goods_unit', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{		
		$.cookie('view_goods_unit', '0', {expires:1,domain:document.domain, path:'/', secure:0});
	}
	
	document.location.reload();
}

function clearAll(frm){
	for(i=0;i < frm.cpid.length;i++){
		frm.cpid[i].checked = false;
	}
}

function checkAll(frm){
	for(i=0;i < frm.cpid.length;i++){
		frm.cpid[i].checked = true;
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

</script>";

$script .= "<Script Language='JavaScript'>

var UploadExcelGoodsReg_i = 0;
var p_no = new Array();


function UploadExcelGoodsReg(){
	$('.upload_excel_infos').each(function(i){
		p_no[i] = $(this).val();
	});

	UploadExcelGoodsRegAjax(p_no.length,UploadExcelGoodsReg_i);
}

function UploadExcelGoodsRegAjax(total_no,now_no){

	$.ajax({
		type: 'GET', 
		data: {'act': 'single_goods_reg', 'p_no':p_no[now_no]},
		url: './inventory_goods_input_excel.act.php?page_type=update',
		dataType: 'html',
		async: true,
		page_type : 'update',
		beforeSend: function(){
			$('#status_message_'+p_no[now_no]).html('품목등록 진행중...<img src=\'/admin/images/indicator.gif\' border=0 width=20 height=20 align=absmiddle> ')
		},
		success: function(data){
			UploadExcelGoodsReg_i++;
			try{
				if(total_no > now_no){
					$('#status_message_'+p_no[now_no]).html(data);
					UploadExcelGoodsRegAjax(total_no,UploadExcelGoodsReg_i);
				}else{
					if(confirm('수정완료되었습니다. 등록이 실패한 품목정보를 엑셀로 다운받으시겠습니까?')){
						location.href='./inventory_goods_input_excel.act.php?act=bad_goods_info_excel&page_type=update';
					}
				}
			}catch(e){
				alert(e.message);
			}
		},
		error:function(x, o, e){
			alert(x.status + ' : '+ o +' : '+e);
		}
	});

}

</script>";

$P = new LayOut();
$P->strLeftMenu = inventory_menu();
$P->addScript = $script;
$P->Navigation = "재고관리 > 재고품목 대량수정";
$P->title = "재고품목 대량수정";
$P->strContents = $Contents;
if ($category_load == "yes"){
	$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
}
$P->PrintLayOut();


function MakeUploadExcelData(){

	include("../logstory/class/sharedmemory.class");
	//auth(8);
	$shmop = new Shared("upload_goods_excel_data_update_".$_SESSION["admininfo"]["charger_ix"]);
	//	$shmop->clear();
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$upload_excel_data = $shmop->getObjectForKey("upload_goods_excel_data_update_".$_SESSION["admininfo"]["charger_ix"]);

	if($upload_excel_data[session_id()]){

		$mstring = "<table cellpadding=3 cellspacing=0 class='list_table_box' >\n";
		$i = 0;
		$z = 0;
		foreach($upload_excel_data[session_id()] as $key => $value){

			$mstring .= "<tr align=center height=25>\n";
			$mstring .= "\t<td ".($i == 0 ? "class=m_td style='padding:0px 50px;'   nowrap":" class='point ' nowrap")."> ".($i == 0 ? "처리현황":"<span id='status_message_".$value["p_no"]."'>".$value["status_message"]."")."</span></td>\n";
			foreach($value as $_key => $_value){
				if($_key != "status_message" && $_key != "p_no"){
					$mstring .= "\t<td ".($i == 0 ? "class=m_td nowrap":" nowrap").">
											".@htmlspecialchars($_value)."";
					if($_key == "gcode" && $i != 0){
						$mstring .= "<input type=hidden class='upload_excel_infos' id='p_no' name='upload_excel_infos[".$value["p_no"]."][p_no]' value='".$value["p_no"]."' >";
						$z++;
					}
					$mstring .= "</td>\n";
				}
			}
			$mstring .= "</tr>\n";

			$i++;
		}
		$mstring .= "</table>\n";
	}

	return $mstring;

}

?>