<?
include("../class/layout.class");
include("./inventory.lib.php");

$db = new Database;
$db->query("SELECT * FROM common_company_detail where com_type = 'A'");
$db->fetch();
$a_company_id = $db->dt[company_id];

//본사 직원이 아닐경우! 사업장은 자기 사업장만!
if($_SESSION["admininfo"]["company_id"]!=$a_company_id){
	$company_id=$_SESSION["admininfo"]["company_id"];
}

$where="";

if($company_id!=""){
	$where.=" and odt.company_id='".$company_id."' ";
}

if($search_company_id){
	$where.=" and odt.company_id='".$search_company_id."' ";
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
			$where .= "and (odt.gname LIKE '%".$search_text."%' or odt.gid LIKE '%".$search_text."%') ";
		}else{
			$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
		}
	}
}

$sql="select
			odt.*, 
			ifnull(sum(ips.stock),0) as stock, 
			ifnull(sum(igs.safestock),0) as safestock, 
			gu.buying_price as buy_price  
		from 
			inventory_order_detail_tmp odt 
			left join inventory_goods_unit gu on (gu.gid=odt.gid and gu.unit=odt.unit)
			left join inventory_product_stockinfo ips on (ips.company_id=odt.company_id and ips.gid=odt.gid and ips.unit=odt.unit)
			left join inventory_goods_safestock igs on (igs.company_id=odt.company_id and igs.gid=odt.gid and igs.unit=odt.unit)
		where
			odt.order_yn ='N' 
			$where
			group by odt.iodt_ix ";
//echo nl2br($sql);
//exit;
$db->query($sql);
$odt_infos = $db->fetchall("object");

$Contents ="
<form name='search_form' id='search_form' method='get' enctype='multipart/form-data'>
<input type='hidden' name='' value='' />
<input type='hidden' name='list_mode' value='".$_GET['list_mode']."' />
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("청구요청 품목리스트", "발주관리 > 청구요청 품목리스트")."</td>
</tr>
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
							<td class='input_box_title'>사업장</td>
							<td class='input_box_item' colspan='3'>
								".SelectEstablishment($search_company_id,"search_company_id","select","false","")."
							</td>
						</tr>
						<tr>
							<td class='input_box_title'><b>검색어</b>
								<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
								<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> (다중검색 체크)
							</td>
							<td class='input_box_item' colspan='3'>
								<table cellpadding=0 cellspacing=0>
									<tr>
										<td valign='top'>
											<div style='padding-top:5px;'>
											<select name='search_type' id='search_type'  style=\"font-size:12px;height:22px;\">
												<option value='gname_gid' ".CompareReturnValue("gname_gid",$search_type).">품목명+품목코드</option>
												<option value='odt.gid' ".CompareReturnValue("odt.gid",$search_type).">품목코드</option>
												<option value='odt.gname' ".CompareReturnValue("odt.gname",$search_type).">품목명</option>
												<option value='odt.charger' ".CompareReturnValue("odt.charger",$search_type).">요청자</option>
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
						<tr>
							<td class='input_box_title'>엑셀파일 입력</td>
							<td class='input_box_item' colspan='3'>
								<input type='file' class='textbox' name='excel_file' id='excel_file' style='height: 22px; width: 200px; border: 1px solid rgb(204, 204, 204);' validation='true' title='엑셀파일 입력'>
                                <a href='/admin/product/purchase_apply_excel_2016.xlsx' onfocus='this.blur();'><img align='absmiddle' src='../images/korea/btn_sample_excel_save.gif'></a>
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
</form>


<form  name='input_frm' id='input_frm' method='post' onsubmit='return purchaseSubmit(this)' action='./purchase.act.php' target='act' enctype='multipart/form-data'>
<input type=hidden name='act' value='order_applay_complete'>
<input type=hidden name=mmode value='".$mmode."'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td height='25' style='padding:10px 0px;'>
		<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>청구요청 품목</b>
	</td>
	<td align=right>
		<a href=\"javascript:PopGoodsSelect();\"><img src='../images/".$admininfo["language"]."/btc_goods_add.gif' border='0' align='absmiddle'  style='cursor:pointer;'></a>
		<input type=text class='textbox number' value='바코드 입력&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' id='barcode' onclick=\"Submit_bool=false;$(this).val('')\">
	</td>
</tr>
<tr>
	<td colspan='2' height=300 style='vertical-align:top;'>";

$Contents .= "
	<table cellpadding=3 cellspacing=0 border=0 bgcolor=#ffffff width=100% onselect='return false;' align=center class='list_table_box' id='regist_item_list'>
				<col width='3%' >
				<col width=7% >
				<col width=6% >
				<col width='15%' >
				<col width=7% >
				<col width=5% >
				<col width=6% >
				<col width=6% >
				<col width=5% >
				<col width=7% >
				<col width=5% >
				<col width=5% >
				<col width=7% >
				<col width=5% >
				<tr align=center height=30 style='font-weight:bold;' >
					<td class=s_td rowspan='2'><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.input_frm)'></td>
					<td class=m_td rowspan='2'>매입처</td>
					<td class=m_td rowspan='2'>품목코드</td>
					<td class=m_td rowspan='2'>품목정보</td>
					<td class=m_td rowspan='2'>규격</td>
					<td class=m_td rowspan='2'>단위</td>
					<td class=m_td rowspan='2'>환산수량</td>
					<td class=m_td rowspan='2' nowrap>매입가</td>
					<td class=m_td rowspan='2' nowrap>발주수량</td>
					<td class=m_td rowspan='2'>요청사업장</td>
					<td class=m_td colspan=2>재고현황</td>
					<td class=m_td rowspan='2' nowrap>합계</td>
					<td class=e_td rowspan='2'>요청자</td>
				</tr>
				<tr align=center height=30>
					<td class=m_td>재고</td>
					<td class=m_td>부족재고</td>
				</tr>";
				
				for($i=0; $i<count($odt_infos) || $i==0 ;$i++){
					if($list_mode == 'excel_upload'){
						$Contents .= "
						<tr height=30 depth=1 lack_bool=".($odt_infos[$i]["iodt_ix"]!="" ? "true" : "false")." style='cursor:pointer;' >
							<td align=center>
								<input type=checkbox class='nonborder select_iodt_ix' id='iodt_ix'  name='iodt_ix[]' value='".$odt_infos[$i]["iodt_ix"]."'>
								<input type='hidden' id='com_id_gid_unit' value='".$odt_infos[$i]["company_id"]."|".$odt_infos[$i]["gid"]."|".$odt_infos[$i]["unit"]."'>
							</td>
							<td style='padding:3px;' id='tr_ci_name'>".$odt_infos[$i]["ci_name"]."</td>
							<td align=center id='gid'>".$odt_infos[$i]["gid"]."</td>
							<td style='padding:3px;'><b id='gname'>".$odt_infos[$i]["gname"]."</b></td>
							<td align=center id='standard'>".$odt_infos[$i]["standard"]."</td>
							<td align=center id='unit'>".$ITEM_UNIT[$odt_infos[$i]["unit"]]."</td>
							<td style='text-align:center;' id='change_amount'>".$odt_infos[$i]["change_amount"]."</td>
							<td align=center class='point'><input type=text class='textbox numeric' name='buy_price[".$odt_infos[$i]["iodt_ix"]."]' validation=true  id='buy_price' value='".$odt_infos[$i]["buy_price"]."' size=8 title='매입가' onkeyup=\"$(this).closest('tr').find('#total_price').html(FormatNumber($(this).val()*$(this).closest('tr').find('#cnt').val()))\" ></td>
							<td style='text-align:center;' class='point'>
								<input type=text class='textbox numeric' name='cnt[".$odt_infos[$i]["iodt_ix"]."]'  id='cnt' value='".$odt_infos[$i]["cnt"]."' size=8 title='수량' validation=true  style='width:80%;text-align:right;padding:0 5px 0 0' onkeyup=\"if($(this).val()=='0'){alert('수량 0을 입력하실수 없습니다.');$(this).val('1')}$(this).closest('tr').find('#total_price').html(FormatNumber($(this).val()*$(this).closest('tr').find('#buy_price').val()))\"> 
							</td>
							<td style='text-align:center;' id='com_name2'>".$odt_infos[$i]["com_name"]."</td>
							<td style='text-align:center;' id='stock'>".($odt_infos[$i]["stock"])."</td>
							<td style='text-align:center;' id='lack_stock'>".($odt_infos[$i]["safestock"] > 0 ? $odt_infos[$i]["safestock"]-$odt_infos[$i]["stock"] : $odt_infos[$i]["stock"])."</td>
							<td align=center id='total_price'>".number_format($odt_infos[$i]["buy_price"]*$odt_infos[$i]["cnt"])."</td>
							<td align=center id='charger'>".$odt_infos[$i]["charger"]."</td>
						</tr>";
					}else{
						$Contents .= "
						<tr height=30 depth=1 lack_bool=".($odt_infos[$i]["iodt_ix"]!="" ? "true" : "false")." style='cursor:pointer;' >
							<td align=center>
								<input type=checkbox class='nonborder select_iodt_ix' id='iodt_ix'  name='iodt_ix[]' value='".$odt_infos[$i]["iodt_ix"]."'>
								<input type='hidden' id='com_id_gid_unit' value='".$odt_infos[$i]["company_id"]."|".$odt_infos[$i]["gid"]."|".$odt_infos[$i]["unit"]."'>
							</td>
							<td style='padding:3px;' id='tr_ci_name'>".$odt_infos[$i]["ci_name"]."</td>
							<td align=center id='gid'>".$odt_infos[$i]["gid"]."</td>
							<td style='padding:3px;'><b id='gname'>".$odt_infos[$i]["gname"]."</b></td>
							<td align=center id='standard'>".$odt_infos[$i]["standard"]."</td>
							<td align=center id='unit'>".$ITEM_UNIT[$odt_infos[$i]["unit"]]."</td>
							<td style='text-align:center;' id='change_amount'>".$odt_infos[$i]["change_amount"]."</td>
							<td align=center class='point'><input type=text class='textbox numeric' name='buy_price[".$odt_infos[$i]["iodt_ix"]."]' validation=true  id='buy_price' value='".$odt_infos[$i]["buy_price"]."' size=8 title='매입가' onkeyup=\"$(this).closest('tr').find('#total_price').html(FormatNumber($(this).val()*$(this).closest('tr').find('#cnt').val()))\" ></td>
							<td style='text-align:center;' class='point'>
								<input type=text class='textbox numeric' name='cnt[".$odt_infos[$i]["iodt_ix"]."]'  id='cnt' value='".$odt_infos[$i]["cnt"]."' size=8 title='수량' validation=true  style='width:80%;text-align:right;padding:0 5px 0 0' onkeyup=\"if($(this).val()=='0'){alert('수량 0을 입력하실수 없습니다.');$(this).val('1')}$(this).closest('tr').find('#total_price').html(FormatNumber($(this).val()*$(this).closest('tr').find('#buy_price').val()))\"> 
							</td>
							<td style='text-align:center;' id='com_name2'>".$odt_infos[$i]["com_name"]."</td>
							<td style='text-align:center;' id='stock'>".($odt_infos[$i]["stock"])."</td>
							<td style='text-align:center;' id='lack_stock'>".($odt_infos[$i]["safestock"] > 0 ? $odt_infos[$i]["safestock"]-$odt_infos[$i]["stock"] : $odt_infos[$i]["stock"])."</td>
							<td align=center id='total_price'>".number_format($odt_infos[$i]["buy_price"]*$odt_infos[$i]["cnt"])."</td>
							<td align=center id='charger'>".$odt_infos[$i]["charger"]."</td>
						</tr>";
					}
				}

			$Contents .= "
			</table>
			<table cellpadding=0 cellspacing=0 border=0 width=100%>
				<tr>
					<td colspan=4 align=left>
						<table cellpadding=0>
							<tr height=40>
								<td><img src='../images/".$admininfo["language"]."/btc_select_goods_delete.gif' border='0' align='absmiddle' onclick='checkDelete()' style='cursor:pointer;'></td>
							</tr>
						</table>
					</td>
					<td colspan=4>
						<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
							<tr>
								<td align='right' style='font-size:12px;font-weight:bold;'></td>
							</tr>
						</table>
					</td>
				</tr>
			</table> ";

$Contents .= "
	</td>
</tr>
<tr height=20>
	<td colspan='2' style='padding:3px;' align=center>
		
	</td>
</tr>
</table>";



$select = "
	<select name='update_type' >
		<option value='2' selected>선택한 상품 전체에</option>
		<option value='1' >검색한 상품 전체에</option>
	</select>

	<input type='radio' name='update_kind' id='applay_complete' value='applay_complete' onclick=\"ChangeUpdateForm('applay_complete');\" checked><label for='applay_complete'> 청구요청 확정 </label>
	<input type='radio' name='update_kind' id='update_ci_ix' value='update_ci_ix' onclick=\"ChangeUpdateForm('update_ci_ix');\"><label for='update_ci_ix'> 매입처 변경 </label>";

$help_text = "
	<div class='update_kind_div' id='applay_complete' >
		<div style='padding:4px 0 4px 0'>
			<img src='../images/dot_org.gif'> <b>청구 요청 확정</b> <span class=small style='color:gray'> 청구 요청 확정하고자 하는 요청건을 선택한후 발주에 필요한 정보를 입력한 후 저장 버튼을 눌러 주세요.</span>
		</div>
		<div style='padding:0 0 4px 0'>
			<img src='../images/dot_org.gif'> <span class=small style='color:gray'>요청시 매입처 별로 자동으로 발주서가 작성이 됩니다.</span>
		</div>
		<div style='padding:0 0 4px 0'>
			<img src='../images/dot_org.gif'> <span class=small style='color:gray'>매입처 미지정시 발주서가 작성되지 않습니다.</span>
		</div>
		<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
		<col width='18%'>
		<col width='*'>
		<col width='18%'>
		<col width='32%'>
		<tr height='30'>
			<td class='input_box_title' ><b>발주일자</b> <img src='".$required3_path."'></td>
			<td class='input_box_item' >
				".date("Y-m-d")."
			</td>
			<td class='input_box_title'> 작성자 <img src='".$required3_path."'></td>
			<td class='input_box_item'>
				<input type='hidden' name='charger' value='".$_SESSION["admininfo"]["charger"]."'>
				<input type='hidden' name='charger_ix' value='".$_SESSION["admininfo"]["charger_ix"]."'>
				".$_SESSION["admininfo"]["charger"]."
			</td>
		</tr>
		<tr height='30'>
			<td class='input_box_title'>배송비 조건 <img src='".$required3_path."'> </td>
			<td class='input_box_item'><input type='text' class='textbox numeric' name='delivery_price' value='' validation='true' title='배송비' style='width:70px;'> 원 추가</td>
			<td class='input_box_title'> 납기일자 <img src='".$required3_path."'></td>
			<td class='input_box_item'>
				<img src='../images/".$admininfo["language"]."/calendar_icon.gif' align='absmiddle'>
				<input type='text' class='textbox point_color' name='limit_date' value='' style='height:20px;width:70px;text-align:center;' id='limit_datepicker' validation='true' title='납기일'> 까지
			</td>
		</tr>
		<tr height='30'>
			<td class='input_box_title'>납품장소 <img src='".$required3_path."'></td>
			<td class='input_box_item'>
				<input type='radio' name='delivery_type' value='A' id='delivery_type_a' checked/><label for='delivery_type_a'>본사</label>
				<input type='radio' name='delivery_type' value='O' id='delivery_type_o' /><label for='delivery_type_o'>외부직배송</label>
			</td>
			<td class='input_box_title'>납품처 <img src='".$required3_path."'></td>
			<td class='input_box_item' id='td_delivery_type'>
				<div id='div_delivery_type_a'>
					".SelectEstablishment("","company_id","select","true","onChange=\"loadPlace(this,'pi_ix')\" ")."
					".SelectInventoryInfo("","",'pi_ix','select','true', "title='창고' onChange=\"getPlaceAddr($(this))\" ")."
				</div>
				<div id='div_delivery_type_o' style='display:none;'>
					<input type='text' class='textbox helpcloud' help_width='70' help_height='15' help_html='납품처명' name='delivery_name' value='' validation='false' title='납품처명' style='width:185px;'>
				</div>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'>납품지 주소 <img src='".$required3_path."'></td>
			<td class='input_box_item' colspan=3 style='padding:5px 10px;'>
				<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='80px'>
				<col width='100px'>
				<col width='*'>
				<tr>
					<td height=26>
						<input type='text' class='textbox' name='delivery_zip1' id='zipcode1' size='7' maxlength='7' value='' validation='true' title='배달주소 우편번호' readonly>
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1');\" style='cursor:pointer;'>
					</td>
					<td></td>
				</tr>
				<tr>
					<td height=26 colspan='3'>
						<input type=text name='delivery_addr1'  id='addr1' value='' size=50 class='textbox' validation='true' title='배달주소' style='width:450px'>
					</td>
				</tr>
				<tr>
					<td height=26 colspan='3'>
						<input type=text name='delivery_addr2'  id='addr2'  value='' size=70 class='textbox' validation='false' title='배달주소' style='width:450px'> (상세주소)
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr height='30'>
			<td class='input_box_title'>기타사항</td>
			<td class='input_box_item' colspan=3><input type=text class='textbox' name='msg' value='' id='msg' style='width:90%'></td>
		</tr>
		</table>
	</div>";

$help_text .= "
	<div class='update_kind_div' id='update_ci_ix' style='display:none'>
		<div style='padding:4px 0 4px 0'>
			<img src='../images/dot_org.gif'> <b>매입처 변경</b> <span class=small style='color:gray'> 매입처를 변경하고자 하는 요청건을 선택한후 저장 버튼을 눌러 주세요.</span>
		</div>
		<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
		<col width='18%'>
		<col width='*'>
		<col width='18%'>
		<col width='32%'>
		<tr>
			<td class='input_box_title'> 매입처 <img src='".$required3_path."'></td>
			<td class='input_box_item' colspan='3'>".SelectSupplyCompany("","ci_ix","select", "false", "1")."</td>
		</tr>
		</table>
	</div>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td colspan=4 align=center><img src='../images/".$admininfo["language"]."/b_save.gif' onclick=\"Submit_bool=true;$(this).closest('form').submit();\" border=0 align=absmiddle style='cursor:pointer;'></td></tr>
	</table>";
}else{
	$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>";
}

$Contents .= HelpBox($select, $help_text,'350');
$Contents .= "</form>";

$Script = "
<!--link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' /-->
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
<script language='javascript' src='/admin/js/jquery.form.js'></script>
<script language='javascript' src='/admin/js/jquery.form.min.js'></script>
<script language='JavaScript' >

$(document).ready(function (){

	//다중검색어 시작 2014-04-10 이학봉
	//다중검색어 수정 2014-05-29 홍진영
	mult_search_use_check();
	$('input[name=mult_search_use]').click(function (){
		mult_search_use_check();
	});
	//다중검색어 끝 2014-04-10 이학봉
	

	$('#limit_datepicker').datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'
	});

	$('input[name=delivery_type]').click(function(){
		delivery_type_click($(this));
	})
	
	$('#barcode').keypress(function(e){
		if(e.keyCode==13){
			BarcodeGoodsSelect($(this));
		}
	})
});

function ChangeUpdateForm(selected_id){

	$('.update_kind_div').hide();
	$('.update_kind_div input[validation=true]').attr('validation',false);
	$('.update_kind_div select[validation=true]').attr('validation',false);
	$('.update_kind_div#'+selected_id).show();
	$('.update_kind_div#'+selected_id+' input[validation=false]').attr('validation',true);
	$('.update_kind_div#'+selected_id+' select[validation=false]').attr('validation',true);
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

function delivery_type_click(obj){
	var id_str = obj.attr('id');
	$('#td_delivery_type div').hide();
	$('#td_delivery_type div [validation=true]').attr('validation','false');

	$('#div_'+id_str).show();
	$('#div_'+id_str+' [validation=false]').attr('validation','true');
}

function zipcode(type) {
	var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function getPlaceAddr(obj){
	//getPlaceData 함수는 placesection.js에~
	json_data = getPlaceData(obj);

	zip=json_data.place_zip.split('-');

	//$('#zip1').val(zip[0]);
    $('#zipcode1').val(zip[0]);
	$('#zip2').val(zip[1]);
	$('#addr1').val(json_data.place_addr1);
	$('#addr2').val(json_data.place_addr2);
}

function clearAll(frm){
	$('.select_iodt_ix').each(function(){
		$(this).attr('checked',false);
	});
}

function checkAll(frm){
	$('.select_iodt_ix').each(function(){
		$(this).attr('checked','checked');
	});
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

function PopGoodsSelect(){
	ShowModalWindow('goods_select.php?page_type=stocked&stock_company_id='+$('#company_id').val(),1000,800,'goods_select');
	get_company_safestock();
}

function GoodsSelect(gid,gname,unit,unit_text,standard, buying_price, company_name, place_name, section_name, pi_ix, ps_ix, vdate, expiry_date, stock,wholesale_price,sellprice,surtax_div,surtax_text,change_amount){
	var data = {};
	data['act']='order_detail_tmp_insert';
	data['gid']=gid;
	data['gname']=gname;
	data['unit']=unit;
	data['unit_text']=unit_text;
	data['standard']=standard;
	data['change_amount']=change_amount;
	data['buying_price']=buying_price;
	data['stock']=stock;

	order_detail_tmp_insert(data);
}

function BarcodeGoodsSelect(obj){
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'get_goods_barcode', 'company_id':$('#company_id').val(), 'barcode':obj.val()},
		url: './purchase.act.php',  
		dataType: 'json', 
		error: function(x, o, e){
			 alert(x.status + ' : '+ o +' : '+e);
		},
		success: function(data){
			if(data !=null && data.gid!=null){
				data['act']='order_detail_tmp_insert';
				order_detail_tmp_insert(data);
			}else{
				alert('검색된 품목이 없습니다.');
			}
		}
	});
	obj.val('');
}

function order_detail_tmp_insert(_data_){
	
	$.ajax({ 
		type: 'POST', 
		data: _data_,
		url: './purchase.act.php',  
		dataType: 'json', 
		error: function(x, o, e){
			 alert(x.status + ' : '+ o +' : '+e);
		},
		success: function(data){ 
			GoodsInsert(data);
			get_company_safestock();
		}
	});
}

function GoodsInsert(data){

	var iodt_ix=data.iodt_ix;
	var ci_name=data.ci_name;
	var gid=data.gid;
	var gname=data.gname;
	var standard=data.standard;
	var unit=data.unit;
	var unit_text=data.unit_text;
	var change_amount=data.change_amount;
	var buying_price=data.buying_price;
	var cnt=data.cnt;
	var company_id=data.company_id;
	var com_name=data.com_name;
	var stock=data.stock;
	var com_id_gid_unit = company_id+'|'+gid+'|'+unit;
	if(data.stock!=null){
		var stock=data.stock;
	}else{
		var stock='0';
	}

	var tbody = $('#regist_item_list tbody');  	
	var thisRow = tbody.find('tr[depth^=1]:last');
	var safestock=0;
	
	if(thisRow.find('#iodt_ix').val() == ''){
		thisRow.attr('lack_bool',false);
		thisRow.find('#iodt_ix').val(iodt_ix);
		thisRow.find('#com_id_gid_unit').val(com_id_gid_unit);
		thisRow.find('#tr_ci_name').html(ci_name);   
		thisRow.find('#gid').html(gid);
		thisRow.find('#gname').html(gname);
		thisRow.find('#standard').html(standard);
		thisRow.find('#unit').html(unit_text);
		thisRow.find('#change_amount').html(change_amount);
		thisRow.find('#buy_price').attr('name','buy_price['+iodt_ix+']').val(buying_price);
		thisRow.find('#cnt').attr('name','cnt['+iodt_ix+']').val(cnt);
		thisRow.find('#com_name2').html(com_name);
		thisRow.find('#stock').html(stock);
		thisRow.find('#total_price').html('0');
	}else{

		var newRow = tbody.find('tr[depth^=1]:first').clone(true).appendTo(tbody);  

		newRow.attr('lack_bool',false);
		newRow.find('#iodt_ix').val(iodt_ix);
		newRow.find('#com_id_gid_unit').val(com_id_gid_unit);
		newRow.find('#tr_ci_name').html(ci_name);   
		newRow.find('#gid').html(gid);
		newRow.find('#gname').html(gname);
		newRow.find('#standard').html(standard);
		newRow.find('#unit').html(unit_text);
		newRow.find('#change_amount').html(change_amount);
		newRow.find('#buy_price').attr('name','buy_price['+iodt_ix+']').val(buying_price);
		newRow.find('#cnt').attr('name','cnt['+iodt_ix+']').val(cnt);
		newRow.find('#com_name2').html(com_name);
		newRow.find('#stock').html(stock);
		newRow.find('#total_price').html('0');
	}
}

function get_company_safestock(){
	
	var safestock=0;
	var lack_stock=0;
	var bool=false;
	
	obj=$('#regist_item_list tr[lack_bool=false]:first');
	
	if(obj.length){
		com_id_gid_unit=obj.find('#com_id_gid_unit').val().split('|');
		com_id=com_id_gid_unit[0];
		gid=com_id_gid_unit[1];
		unit=com_id_gid_unit[2];
		
		stock=obj.find('#stock').html();
		bool=true;
	}

	if(bool){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'get_company_safestock', 'company_id':com_id, 'gid':gid, 'unit':unit},
			url: './purchase.act.php',  
			dataType: 'json', 
			error: function(x, o, e){
				 alert(x.status + ' : '+ o +' : '+e);
			},
			success: function(data){ 
				safestock=data.safestock;
				if(safestock==0){
					lack_stock=stock;
				}else{
					lack_stock=safestock-stock
				}
				obj.find('#lack_stock').html(lack_stock);
				obj.attr('lack_bool',true);
				get_company_safestock();
			}
		});
	}
}

function checkDelete(){
	if(confirm('선택하신 청구요청품목을 정말로 삭제 하시겠습니까?')){
		var tbody = $('#regist_item_list tbody');
		var thisRow = tbody.find('tr[depth^=1]:last');

		var push = Array.prototype.push;
		var iodt_ix = new Array();
		var i=0;
		$('.select_iodt_ix').each(function(){
			if($(this).attr('checked') == 'checked'){

				iodt_ix[i]=$(this).closest('tr').find('#iodt_ix').val();
				i++;
			
				var total_rows = tbody.find('tr[depth^=1]').length;  
				if(total_rows > 1){
					$(this).closest('tr').remove();
				}else{
					
					thisRow = $(this).closest('tr');
					thisRow.attr('lack_bool',false);
					
					thisRow.attr('lack_bool',false);
					thisRow.find('#iodt_ix').val('');
					thisRow.find('#com_id_gid_unit').val('');
					thisRow.find('#tr_ci_name').html('');   
					thisRow.find('#gid').html('');
					thisRow.find('#gname').html('');
					thisRow.find('#standard').html('');
					thisRow.find('#unit').html('');
					thisRow.find('#change_amount').html('');
					thisRow.find('#buy_price').val('');
					thisRow.find('#cnt').val('');
					thisRow.find('#com_name2').html('');
					thisRow.find('#stock').html('');
					thisRow.find('#total_price').html('');
				}
			}
		});

		order_detail_tmp_delete(iodt_ix);
	}
}

function order_detail_tmp_delete(iodt_ix){

	$.ajax({ 
		type: 'POST', 
		data: {'act': 'order_detail_tmp_delete', 'iodt_ix':iodt_ix},
		url: './purchase.act.php',  
		dataType: 'json', 
		error: function(x, o, e){
			 alert(x.status + ' : '+ o +' : '+e);
		},
		success: function(data){ 

		}
	});
}

Submit_bool=true;
function purchaseSubmit(frm) {
	if(!Submit_bool){
		return false;
	}

	if(frm.update_type.value == 1){
		if(parseInt(frm.search_searialize_value.value.length) <= 58){
			alert(language_data['product_list.js']['K'][language]);//'검색상품 전체에 대한 적용은 검색후 가능합니다.'
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

		var iodt_ix_checked_bool = false;

		$('input[name^=iodt_ix]').each(function (){
			var checked = $(this).is(':checked');
			if(checked == true){
				iodt_ix_checked_bool = true;
			}
		})

		if(!iodt_ix_checked_bool){
			alert(language_data['product_list.js']['H'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
			//select_update_unloading();
			return false;
			
		}
	}

	if(!CheckFormValue(frm)){
		return false;
	}
}

$(function () { 

	//$('#excel_file').wrapAll('<form id='myupload' action='product_list.act.php' method='post' enctype='multipart/form-data></form>'); 
	$('#excel_file').change(function(){

		$('#search_form').ajaxSubmit({
			type:'POST',
			url:'purchase.act.php?act=upload_excel',
			dataType:  'html', 
			success: function(data) { 
				$('#excel_file').val('');
				document.location.href='./purchase_apply.php?list_mode=excel_upload';
			},
			error:function(xhr){ 
				//alert('error');
			}
		}); 
	}); 

});
</Script>
";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 발주관리 > 청구요청 품목리스트 ";
	$P->NaviTitle = "청구요청 품목리스트";
	$P->strContents = $Contents;
	$P->PrintLayOut();
}else{
	$P = new LayOut;
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = inventory_menu();
	$P->strContents = $Contents;
	$P->Navigation = "재고관리 > 발주관리 > 청구요청 품목리스트 ";
	$P->title = "청구요청 품목리스트";
	$P->PrintLayOut();
}

?>
