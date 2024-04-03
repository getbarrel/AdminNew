<?
include("../class/layout.class");
include_once("../inventory/inventory.lib.php");
require($_SERVER["DOCUMENT_ROOT"]."/include/barcode/php-barcode-0.4/php-barcode.php");
include_once("../product/goods_input.lib.php");

$db = new Database;
$idb = new Database;
$bdb = new Database;

$Script = "
<style>

ul {
	LIST-STYLE-IMAGE: none; LIST-STYLE-TYPE: none;padding:0px;
}
li{
	list-style-tyle:none;
	margin:0px;
	padding:0px;
}
  #sortlist {
      list-style-type:none;
      margin:0;
      padding:0;
   }
   #sortlist li {
     font:13px Verdana;
     margin:0;
     padding:0px;
     cursor:move;
   }
  .ctr_1 {text-align:center};
 
</style>

<script language='JavaScript'>
function sendMessage(msg){
        window.HybridApp.callAndroid(msg);
}

function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var depth = sel.getAttribute('depth');
	
	$('select[class=cid]').each(function(){
		if(parseInt($(this).attr('depth')) > parseInt(depth)){
			$(this).find('option').not(':first').remove();
		}
	});

	if(sel.selectedIndex!=0) {
		window.frames['act'].location.href = 'inventory_category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

}

function ChnageImg(img_path)
{
	document.getElementById('viewimg').innerHTML = '<img src=\"'+img_path+'\" id=chimg>'

}

$(document).ready(function (){
    $('input[name=admin_type]').click(function (){
        if($(this).val() == 'A'){
            $('#com_name').attr('validation', false);
        } else {
            $('#com_name').attr('validation', true)
        }
    })
})
</script>";

if ($gid != ""){

	/*기본창고 구조가 바뀜에 따라서 삭제
	$sql = "SELECT g.*, pi.company_id FROM inventory_goods g
				left join  inventory_place_info pi on g.pi_ix = pi.pi_ix
				where g.gid = '$gid'";
	*/

	$sql = "SELECT g.* FROM inventory_goods g where g.gid = '$gid'";
	$idb->query($sql);
	if($idb->total){
		$idb->fetch();
		
		$admin= $idb->dt[admin];

		if($mode == "copy"){
			$act = "insert";
		}else{
			$act = "update";
		}

		$item_account = $idb->dt[item_account];
		$basic_unit = $idb->dt[basic_unit];
		$order_basic_unit = $idb->dt[order_basic_unit];
		$surtax_div = $idb->dt[surtax_div];
		
		
		
	}else{
		$act = "insert";
	}
}else{
	$act = "insert";
	$admin = $_SESSION["admininfo"]["company_id"];
	$item_account = 1; //원재료
	$basic_unit = 1; //EA
	$order_basic_unit = 1; //EA
	$surtax_div = 1; //부가세 포함
}

$Contents = "
	<table cellpadding=0 width=100%>
		<tr>
		    <td align='left' colspan=4 > ".GetTitleNavigation("재고품목등록", "재고관리 > 재고품목등록")."</td>
		</tr>
	</table>";

$Contents .= "
			<form name='inventory_goods_input' action='./inventory_goods_input.act.php' method='post' enctype='multipart/form-data' onsubmit='return SubmitX(this);' target='iframe_act'><!-- target='iframe_act'-->
			<input type='hidden' name=act value='".$act."'>
			<input type='hidden' name=bgid value='".$gid."'>
			<input type='hidden' name=mmode value='".$mmode."'>
			<input type='hidden' name=mode value='".$mode."'>
			<input type='hidden' name=selected_cid value=''>
			<input type='hidden' name=selected_depth value=''>

			<table width=100%>
			<tr align=left>
				<td width=500>";
if($gid){
$Contents .= "
				<!--a href=\"/shop/goods_view.php?id=".$gid."\" target=_blank><img src='../images/".$admininfo["language"]."/btn_preview.gif' border=0 align=absmiddle style='cursor:pointer'></a-->";
} else {
	if(substr_count($_SERVER["HTTP_USER_AGENT"],"Mobile")){
	$Contents .= "<input type=\"button\" value=\"업로드하기\" onclick=\"sendMessage('upload')\"/>";
	}
}

$Contents .= "<!--a href=\"JavaScript:PoPWindow('/shop/goods_view.php?id=".$gid."',980,800,'comparewindow');\">보기</a--></td>";

if ($gid == "" || $mode == "copy"){
	$Contents .= "<td align=right>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") && false){
		$Contents .= "<img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'tmp_insert');\"> ";
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'insert');\">";
	}
	$Contents .= "</td>";
}else{
	$Contents .= "<td align=right>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") && false){
		$Contents .= "<img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'tmp_update');\"> ";
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'update')\"> ";
	}
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D") && false){
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'delete')\">";
	}
	$Contents .= "</td>";
}
$Contents = $Contents."
			</tr>
			</table>";


$Contents .= "
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%>
			<tr>
				<td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 기본정보 </b><span class=small></td></tr></table>")."</td>
			</tr>
			</table>
			<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
				<col width=15%>
				<col width=35%>
				<col width=15%>
				<col width=35%>
				<tr>
					<td class='search_box_title' >품목분류 선택 <br/><a href=\"javascript:PoPWindow3('../inventory/inventory_category.php?mmode=pop',960,600,'inventory_category')\"'><img src='../images/".$admininfo["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a></td>
					<td class='search_box_item' colspan=3>
						<table border=0 cellpadding=3 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>".getInventoryCategoryListMultiple("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $idb->dt[cid])."</td>
								<td style='padding-right:5px;'>".getInventoryCategoryListMultiple("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $idb->dt[cid])."</td>
								<td style='padding-right:5px;'>".getInventoryCategoryListMultiple("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $idb->dt[cid])."</td>
								<td style='padding-right:5px;'>".getInventoryCategoryListMultiple("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $idb->dt[cid])."</td>
								<td>

								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> <b>품목계정 </b><img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%'>
						".getItemAccount($item_account," validation=true title='품목계정'")."
					</td>
					<td class='input_box_title'> <b>사용여부 </b><img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%'>
						<input type='radio' name='is_use' id='is_use_y' value='Y' ".($idb->dt[is_use] == "Y" || $idb->dt[is_use] == "" ? "checked":"")." /> <label for='is_use_y'>사용</label>
						<input type='radio' name='is_use' id='is_use_n' value='N' ".($idb->dt[is_use] == "N" ? "checked":"")." /> <label for='is_use_n'>미사용</label>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> <b>대표코드 <img src='".$required3_path."'> </b>  </td>
					<td class='input_box_item' style='line-height:150%'>
						<input type=text class='textbox point_color' name=gcode size=28 style='width:140px' value='".$idb->dt[gcode]."' ".($act == "update" ? "readonly" : "")." validation=true title='대표코드'>
					</td>
					<td class='input_box_title'> 판매상태 <img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%'>
						<input type='radio' name='status' id='status_1' value='1' ".($idb->dt[status] == "1" || $idb->dt[status] == "" ? "checked":"")." /> <label for='status_1'>판매중</label>
						<input type='radio' name='status' id='status_0' value='0' ".($idb->dt[status] == "0" ? "checked":"")." /> <label for='status_0'>일시품절</label>
						<input type='radio' name='status' id='status_2' value='2' ".($idb->dt[status] == "2" ? "checked":"")." /> <label for='status_2'>단종(품절)</label>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' nowrap> <b>품목명</b> <img src='".$required3_path."'></td>
					<td class='input_box_item'>
						<input type=text class='textbox' name=gname size=28 style='width:90%' value='".str_replace("'","&#39;",trim($idb->dt[gname]))."' validation=true title='품목명'>
					</td>
					<td class='input_box_title'> 모델명 </td>
					<td class='input_box_item' style='line-height:150%'>
						<input type=text class='textbox' name='model' style='width:140px;' value='".$idb->dt[model]."'>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> <b>기본단위 </b> <img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%' >
						".getUnit($basic_unit, "basic_unit", " id='basic_unit' onchange=\"ChangeUnit(this)\" ")."
					</td>
				    <td class='input_box_title'> <b>매입단위</b> <img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%'>
					".getUnit($order_basic_unit, "order_basic_unit", " id='order_basic_unit'  ")."
				    </td>
				</tr>
				<tr>
					<td class='input_box_title'> 부가세 적용 <img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%' >
						".getSurTaxDiv($surtax_div, "surtax_div","","selectbox")."
					</td>
					<td class='input_box_title'> 품목구분</td>
					<td class='input_box_item' >";
						if($_SESSION["admininfo"]["admin_level"]==9){
							$Contents .= "
								<table cellpadding=0 cellspacing=0 >
									<tr>
										<td>
											<input type='radio' name='admin_type' id='admin_type_a' value='A' ".($idb->dt[admin_type] == "A" || $idb->dt[admin_type] == "" ? "checked":"")." onclick=\"$('#admin_list_div').hide();\" /> <label for='admin_type_a'>본사품목</label>
											<input type='radio' name='admin_type' id='admin_type_s' value='S' ".($idb->dt[admin_type] == "S" ? "checked":"")." onclick=\"$('#admin_list_div').show();\" /> <label for='admin_type_s'>위탁품목</label>
										</td>
										<td style='padding-left:5px;'>
											<div id='admin_list_div' ".($idb->dt[admin_type] == "A" || $idb->dt[admin_type] == "" ? "style='display:none;' ":"").">
												".companyAuthList($admin , "validation=".($idb->dt[admin_type] == "A" || $idb->dt[admin_type] == "" ? "false":"true")." title='셀러업체' ")."
											<div>
										</td>
									</tr>
								</table>";
						}else{
							$Contents .= $_SESSION["admininfo"]["company_name"]."<input type='hidden' name='company_id' value='".$admin."'><input type='hidden' name='admin_type' value='S'>";
						}
					$Contents .="
					</td>
				<tr>
					<td class='input_box_title'> 주매입처</td>
					<td class='input_box_item' >".SelectSupplyCompany($idb->dt[ci_ix],"ci_ix","select", "false",'1')."</td>
					<td class='input_box_title'> 원산지 </td>
					<td class='input_box_item' style='line-height:150%'>";
				if($_SESSION["admininfo"]["mallstory_version"] == "service"){
					$Contents .= "
						<input type=text class='textbox' name='origin' style='width:140px;' value='$origin'><br>";
				}else{
					$Contents .= OriginSelect($idb->dt[origin]);
				}
				$Contents .= "
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 제조사 </td>
					<td class='input_box_item' style='line-height:150%'>
						<table cellpadding=0 cellspacing=0 >
							<tr>
								<td><div id='company_select_area'>".MakerList($idb->dt[company],'','',$idb->dt[c_ix])."</div></td>
								<td style='padding-left:5px;'><a href=\"javascript:PoPWindow3('../product/company.php?mmode=pop',960,600,'company')\"'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a></td>
							</tr>
						</table>
					</td>
					<td class='input_box_title'> 브랜드 </td>
					<td class='input_box_item'>
						".BrandListSelect($idb->dt[b_ix],'')."
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 유효기간 </td>
					<td class='input_box_item' style='line-height:150%'>
						<input type=text class='textbox' name='available_priod' style='width:140px;' value='".$idb->dt[available_priod]."'> 일
					</td>
					<td class='input_box_title'> 소재/재질 </td>
					<td class='input_box_item'>
						<input type=text class='textbox' name='material' style='width:140px;' value='".$idb->dt[material]."'>
					</td>
				</tr>
				
				<tr>
					<td class='input_box_title'> 품목등급 </td>
					<td class='input_box_item' colspan='3'>
						<input type=text class='textbox number' name='glevel' style='width:50px;' value='".$idb->dt[glevel]."'>
					</td>
					<!--td class='input_box_title'> 기본보관장소  <img src='".$required3_path."'></td>
					<td class='input_box_item'>
						".SelectEstablishment($idb->dt[company_id],"company_id","select","true","onChange=\"loadPlace(this,'pi_ix')\" ")."
						".SelectInventoryInfo($idb->dt[company_id], $idb->dt[pi_ix],'pi_ix','select','true', "onChange=\"loadPlaceSection(this,'ps_ix')\" ")."
						".SelectSectionInfo($idb->dt[pi_ix],$idb->dt[ps_ix],'ps_ix',"select","true" )."
					</td-->
				</tr>
				<tr>
					<td class='input_box_title'> KC인증여부 </td>
					<td class='input_box_item' style='line-height:150%'>
						<input type='radio' name='kc_mark' id='kc_mark_y' value='Y' ".($idb->dt[kc_mark] == "Y" ? "checked":"")." /> <label for='kc_mark_y'>인증</label>
						<input type='radio' name='kc_mark' id='kc_mark_n' value='N' ".($idb->dt[kc_mark] == "N" || $idb->dt[kc_mark] == "" ? "checked":"")." /> <label for='kc_mark_n'>미인증</label>
					</td>
					<td class='input_box_title'> HC코드 </td>
					<td class='input_box_item' style='line-height:150%'>
						<input type=text class='textbox' name='hc_code' style='width:140px;' value='".$idb->dt[hc_code]."'>
					</td>
					
				</tr>
				<tr>
					<td class='input_box_title'> <span class='helpcloud' help_height='30' help_width='200' help_html='온라인 구매 가능품목의 경우 입력관리 하실수 있습니다. '>품목구매URL</span> </td>
					<td class='input_box_item' style=line-height:150%' colspan=3>
						<input type=text class='textbox' name='bs_goods_url' style='width:90%' value='".$idb->dt[bs_goods_url]."'>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 검색키워드 </td>
					<td class='input_box_item' colspan=3 style='line-height:150%' >
						<input type=text class='textbox' name='search_keyword' style='width:90%' value='".str_replace("'","&#39;",$idb->dt[search_keyword])."'>
					</td>
				</tr>
			</table><br>";


$Contents .="
<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 단위정보  </b></td></tr></table>")."</td></tr></table>
			<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box' id='unit_table'>
				<col width=5%>
				<col width=12%>
				<col width=12%>
				<col width=12%>
				<col width=12%>
				<col width=15%>
				<col width=*>
				<col width=5%>
				<tr align=center height=27>
					<td class='s_td'> 단위 </td>
					<td class='m_td'> 단위당 수량(환산)</td>
					<td class='m_td'> 기본매입가</td>
					<td class='m_td'> 기본도매가</td>
					<td class='m_td'> 기본소매가</td>
					<td class='m_td'> 무게</td>
					<td class='m_td ' style='display:none;'> 부피 (cm)</td>
					<td class='m_td'><a onclick=\"AddUnit('unit_table');\" ><img src='../images/".$admininfo["language"]."/btn_add.gif' border=0 style='margin:2px 0 3px 5px; vertical-align:middle;'></a></td>
				</tr>";

		if($gid){
			$sql = "select * from inventory_goods_unit where gid = '".$gid."' order by gu_ix ASC";
			//echo $sql;
			$db->query($sql);
		}
		if($db->total){
			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);

	$Contents .="
					<tr align=center height=30 class='unit_tr' depth='".$i."'>
						<td class='input_box_title' style='padding:2px;text-align:center;'>
							<div id='unit_text' style='display:".($i == 0 ? "inline":"none").";'>
								".getUnit($db->dt[unit], "order_basic_unit", " id='order_basic_unit'  ","text")."
							</div>
							".getUnit($db->dt[unit], "goods_unit[".$i."][unit]", " onchange=\"compositionBarcode()\" id='unit' style='display:".($i == 0 ? "none":"inline").";width:80px;'  ","selectbox")."
							<input type=hidden  name='goods_unit[".$i."][b_unit]' id='b_unit'   value='".$db->dt[unit]."'>
						</td>
						<td class='list_box_item' >
							<input type=text class='textbox point_color number' name='goods_unit[".$i."][change_amount]' id='change_amount' style='width:70px;' value='".$db->dt[change_amount]."'>
						</td>
						<td class='list_box_item' >
							<input type=text class='textbox point_color number' name='goods_unit[".$i."][buying_price]' id='buying_price' style='width:80px;' value='".$db->dt[buying_price]."'> 원
						</td>
						<td class='list_box_item' >
							<input type=text class='textbox point_color number' name='goods_unit[".$i."][wholesale_price]' id='wholesale_price' style='width:90px;' value='".$db->dt[wholesale_price]."'> 원
						</td>
						<td class='list_box_item' >
							<input type=text class='textbox point_color number' name='goods_unit[".$i."][sellprice]' id='sellprice' style='width:90px;' value='".$db->dt[sellprice]."'> 원
						</td>
						<td class='list_box_item' >
							<input type=text class='textbox' name='goods_unit[".$i."][weight]' id='weight' style='width:150px;' value='".$db->dt[weight]."'>
						</td>
						<td class='list_box_item' style='display:none;' >
							<input type=text class='textbox number' name='goods_unit[".$i."][width_length]' id='width_length' style='width:30px;' value='".$db->dt[width_length]."'>(W) *
							<input type=text class='textbox number' name='goods_unit[".$i."][depth_length]' id='depth_length' style='width:30px;' value='".$db->dt[depth_length]."'>(D) *
							<input type=text class='textbox number' name='goods_unit[".$i."][height_length]' id='height_length' style='width:30px;' value='".$db->dt[height_length]."'>(H)
						</td>
						<td class='list_box_item' >
							<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"DeleteUnit($(this));\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
						</td>
					</tr>";
				}
			}else{
				$i=0;

$Contents .="
				<tr align=center height=30 class='unit_tr' depth='".$i."'>
					<td class='input_box_title' style='padding:2px;text-align:center;'>
						<div id='unit_text' style='display:".($i == 0 ? "inline":"none").";'>
							".getUnit($db->dt[unit], "order_basic_unit", " id='order_basic_unit'  ","text")."
						</div>
						".getUnit($db->dt[unit], "goods_unit[".$i."][unit]", " onchange=\"compositionBarcode()\" id='unit' style='display:".($i == 0 ? "none":"inline").";width:80px;'  ","selectbox")."
						<input type=hidden  name='goods_unit[".$i."][b_unit]' id='b_unit'   value='".$db->dt[unit]."'>
					</td>
					<td class='list_box_item' >
						<input type=text class='textbox point_color number' name='goods_unit[".$i."][change_amount]' id='change_amount' style='width:70px;' value='".$db->dt[change_amount]."'>
					</td>
					<td class='list_box_item' >
						<input type=text class='textbox point_color number' name='goods_unit[".$i."][buying_price]' id='buying_price' style='width:80px;' value='".$db->dt[buying_price]."'> 원
					</td>
					<td class='list_box_item' >
						<input type=text class='textbox point_color number' name='goods_unit[".$i."][wholesale_price]' id='wholesale_price' style='width:90px;' value='".$db->dt[wholesale_price]."'> 원
					</td>
					<td class='list_box_item' >
						<input type=text class='textbox point_color number' name='goods_unit[".$i."][sellprice]' id='sellprice' style='width:90px;' value='".$db->dt[sellprice]."'> 원
					</td>
					<td class='list_box_item' >
						<input type=text class='textbox number' name='goods_unit[".$i."][weight]' id='weight' style='width:150px;' value='".$db->dt[weight]."'>
					</td>
					<td class='list_box_item' style='display:none;' >
						<input type=text class='textbox number' name='goods_unit[".$i."][width_length]' id='width_length' style='width:30px;' value='".$db->dt[width_length]."'>(W) *
						<input type=text class='textbox number' name='goods_unit[".$i."][depth_length]' id='depth_length' style='width:30px;' value='".$db->dt[depth_length]."'>(D) *
						<input type=text class='textbox number' name='goods_unit[".$i."][height_length]' id='height_length' style='width:30px;' value='".$db->dt[height_length]."'>(H)
					</td>
					<td class='list_box_item' >
						<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"DeleteUnit($(this));\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
					</td>
				</tr>";
			}

	$Contents .="
			</table><br>
			<div style='padding-bottom:10px;' class=small>기본단위를 선택하시면 기본단위에 대한 정보가 자동으로 매칭되며 <u>추가</u>를 선택하실경우 부가단위를 입력하실 수 있습니다.</div>";


$Contents .="
<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 규격/옵션 등록  </b><span class=small> <input type='button' value='자동생성' onclick=\"alert('준비중');\" /> </td></tr></table>")."</td></tr></table>
			<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box' id='standard_table'>
				<col width=20%>
				<col width=12%>
				<col width=20%>
				<col width=*>
				<col width=5%>
				<tr align=center height=27>
					<td class='s_td'> 규격(옵션) </td>
					<td class='m_td'> 품목코드번호</td>
					<td class='m_td'> (단위)바코드</td>
					<td class='m_td'> 비고</td>
					<td class='m_td'> <a onclick=\"AddStandard('standard_table')\" ><img src='../images/".$admininfo["language"]."/btn_add.gif' border=0 style='margin:2px 0 3px 5px; vertical-align:middle;'></a></td>
				</tr>";

		if ($idb->dt[gcode] != ""){
			$sql = "select g.standard,g.etc,gu.gid from inventory_goods g , inventory_goods_unit gu where g.gid=gu.gid and g.gcode = '".$idb->dt[gcode]."' group by gid ";
			//echo $sql;
			$db->query($sql);
		}
		if($db->total){
			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);

				/*
				<td class='input_box_title'> <b>품목코드  </b> <img src='".$required3_path."'></td>
				<td class='input_box_item' style='line-height:150%'>";
			if(!$_REQUEST[gid]){
					$Contents .= "
						<input type=text class='textbox point_color' name=gid id='gid' size=28 style='width:140px' onKeyup=\"javascript:valueCheck(this,'품목코드')\" onblur=\"javascript:valueCheck(this,'품목코드')\" value='".($mode == "copy" ? "":$gid)."' validation=false title='품목코드 ' duplicate=false dup_check=".($act == "insert" ? "false":"true")." >
						<div style='display:inline;' id='gid_check_text' >품목코드를입력해주세요</div>";
			}else{
					$Contents .= "
						<input type=text class='textbox point_color' name=gid id='gid' size=28 style='width:140px' value='".($mode == "copy" ? "":$gid)."' validation=false title='품목코드 ' duplicate=false dup_check=".($act == "insert" ? "false":"true")." readonly >
						<div style='display:inline;' id='gid_check_text' >품목코드를입력해주세요</div>";
			}

		$Contents .= "
			</td>

				*/
	$Contents .="
					<tr align=center height=30 class='standard_tr' depth='".$i."'>
						<td class='list_box_item' >
							<input type=text class='textbox point_color number' name='standard[".$i."][standard]' id='standard' style='width:80%;text-align:left;' value='".$db->dt[standard]."'>
						</td>
						<td class='list_box_item' >
							<input type=text class='textbox point_color number' name='standard[".$i."][gid]' id='gid' style='width:80%;' value='".$db->dt[gid]."' validation='true' title='품목코드' ".($act == "update" ? "readonly" : "").">
						</td>
						<td class='barcode_td list_box_item' style='padding:3px;text-align:right;'>";

							$sql = "select gu.unit,gu.barcode from inventory_goods_unit gu where gid = '".$db->dt[gid]."' order by gu_ix ASC ";
							$bdb->query($sql);

							if($bdb->total){
								for($j=0;$j < $bdb->total;$j++){
									$bdb->fetch($j);
									$Contents .="
									".(getUnit($bdb->dt[unit],"","","text")?getUnit($bdb->dt[unit],"","","text"):"<span class='red'>미지정</span>")." : <input type=text class='textbox point_color number' name='standard[".$i."][barcode][".$bdb->dt[unit]."]' id='barcode' style='width:70%;margin-bottom:3px;' value='".$bdb->dt[barcode]."'><br/>";
								}
							}else{
								echo "111";
								$Contents .="
								".(getUnit($db->dt[unit],"","","text")?getUnit($db->dt[unit],"","","text"):"<span class='red'>미지정</span>")." : <input type=text class='textbox point_color number' name='standard[".$i."][barcode][".$db->dt[unit]."]' id='barcode' style='width:70%;margin-bottom:3px;' value='".$db->dt[barcode]."'><br/>";
							}

						$Contents .="
						</td>
						<td class='list_box_item' >
							<input type=text class='textbox point_color number' name='standard[".$i."][etc]' id='etc' style='width:80%;text-align:left;' value='".$db->dt[etc]."'>
						</td>
						<td class='list_box_item' >
							<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"deleteStandardRow($(this));\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
						</td>
					</tr>";
				}
			}else{
				$i=0;
				$Contents .="
					<tr align=center height=30 class='standard_tr' depth='".$i."'>
						<td class='list_box_item' >
							<input type=text class='textbox point_color' name='standard[".$i."][standard]' id='standard' style='width:80%;text-align:left;' value='".$db->dt[standard]."'>
						</td>
						<td class='list_box_item' >
							<input type=text class='textbox point_color number' name='standard[".$i."][gid]' id='gid' style='width:80%;' value='".$db->dt[gid]."' validation='true' title='품목코드' ".($act == "update" ? "readonly" : "").">
						</td>
						<td class='barcode_td list_box_item' style='padding:3px;text-align:right;'>
							".(getUnit($db->dt[unit],"","","text")?getUnit($db->dt[unit],"","","text"):"<span class='red'>미지정</span>")." : <input type=text class='textbox point_color number' name='standard[".$i."][barcode][".$db->dt[unit]."]' id='barcode' style='width:70%;margin-bottom:3px;' value='".$db->dt[barcode]."'><br/>
						</td>
						<td class='list_box_item' >
							<input type=text class='textbox' name='standard[".$i."][etc]' id='etc' style='width:80%;text-align:left;' value='".$db->dt[etc]."'>
						</td>
						<td class='list_box_item' >
							<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"deleteStandardRow($(this));\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
						</td>
					</tr>";
			}

	$Contents .="
			</table><br>
			<div style='padding-bottom:10px;' class=small>기본단위를 선택하시면 기본단위에 대한 정보가 자동으로 매칭되며 <u>추가</u>를 선택하실경우 부가단위를 입력하실 수 있습니다.</div>";


//$inventory_image_info 는 inventory.lib.php
$Contents = $Contents."	<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-1px;'><b class=blk> 이미지 추가</b> ")." </span></td></tr></table>
			<table width=100% >
				<col width=60%>
				<col width=40%>
				<tr>
					<td>
					<table border=0 cellpadding=5 cellspacing=0 bgcolor=#ffffff width=100% class='input_table_box'>
						<col width=13%>
						<col width=55%>
						<col width=*>
						<tr bgcolor='#ffffff' height=50>
							<td class='input_box_title'  nowrap >
								 ".$inventory_image_info[0][width]."*".$inventory_image_info[0][height]." *
							</td>
							<td class='input_box_item'>
								<input type=file name='allimg' class='textbox' size=25 style='font-size:8pt'>
							</td>
							<td rowspan=2 class='input_box_item' style='padding:20px;border:0px solid silver;text-align:center;' align=center valign=middle id=viewimg>";

									if($gid && file_exists($DOCUMENT_ROOT.InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "b"))){
										$Contents = $Contents."<img src='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "b")."' onerror=\"this.src='../images/noimage_152_148.gif'\" style='border:1px solid silver' id=chimg>";
									}else{
										$Contents = $Contents."<img src='../images/noimage_152_148.gif' style='border:1px solid silver' id=chimg>";
									}

										$Contents = $Contents."
							</td>
						</tr>
						<tr bgcolor='#ffffff'>
							<td class='input_box_title'  nowrap>
							 이미지 URL <br>
							</td>
							<td class='input_box_item' style='padding:10px 5px;'>
							<input type=checkbox name='img_url_copy' id='img_url_copy' value=1 > <label for='img_url_copy' >URL 이미지복사</label> <img src='../images/".$admininfo["language"]."/btn_view_img.gif' onclick=\"ChnageImg(document.getElementById('bimg_text').value);\" style='cursor:pointer' align=absmiddle>
							<!--img src='../v3/images/".$admininfo["language"]."/btn_mobile_upload_image.gif' align=absmiddle onclick=\"PoPWindow('./mobile_upload.php',900,880,'btn_mobile_upload_image')\"  style='cursor:pointer;'-->
							<br>
							<input type=text name='bimg_text' id='bimg_text' class='textbox' style='width:90%;font-size:8pt;margin:3px;' value='".$idb->dt[bimg]."'>
							<div class=small> <!--URL 이미지복사를 체크하시면 입력된 이미지 URL 정보를 바탕으로 이미지가 복사됩니다. 단 해당이미지 서버에서 이미지 복사를 차단한 경우는 이미지 복사가 거부될 수 있습니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'Q')." </div>
							</td>
						</tr>
						<!--tr>
							<td class='input_box_item' style='padding:10px 5px;' colspan='2' id=viewimg>";
									if($gid && file_exists($DOCUMENT_ROOT.InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "b"))){
										$Contents = $Contents."<img src='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "b")."' onerror=\"this.src='../images/noimage_152_148.gif'\" style='border:1px solid silver' id=chimg>";
									}else{
										$Contents = $Contents."<img src='../images/noimage_152_148.gif' style='border:1px solid silver' id=chimg>";
									}
										$Contents = $Contents."</td>
							</td>
						</tr-->
					</table><br>
					</td>
					<!--td style='vertical-align:top;'>
						<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box'>
							<col width=30%>
							<col width=70%>
							<tr align=center height=31>
								<td class='s_td'> 분류</td>
								<td class='e_td'> 바코드</td>
							</tr>
							<tr align=center height=158>
								<td class='input_box_title'> 기본바코드</td>
								<td class='list_box_item' >";
								if($barcode){
									$Contents .= "<img src='/include/barcode/php-barcode-0.4/barcode.php?code=00".$barcode."&encoding=EAN&scale=2&mode=png' style='margin:10px 0px 0px 0px'>";
								}
								$Contents .= "
								</td>
							</tr>

						</table>
					</td-->
				</tr>
			</table>";




$Contents .="
<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 재고/생산/배송 정보  </b><span class=small></td></tr></table>")."</td></tr></table>
			<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
				<col width=20%>
				<col width=25%>
				<col width=25%>
				<col width=30%>
				<tr align=center height=30>
					<td class='input_box_title'>  <span class='helpcloud' help_height='50' help_width='270' help_html='<b>lead time ? </b> 기본 납기일 즉 발주후 배송가능 일자를 체크하여 관리할 수 있음 대략적 입고일을 알아 고객대응을 바로 할 수 있음 '>LEAD TIME</span> </td>
					<td class='input_box_item' >
					 <input type=text class='textbox number' name='leadtime' style='width:70px;' value='".$idb->dt[leadtime]."'> 일(days)
					</td>
					<td class='input_box_title' >
					일별생산량/구매가능량
					</td>
					<td class='input_box_item' >
					<input type=text class='textbox' name='available_amountperday' style='width:130px;' value='".$idb->dt[available_amountperday]."'>  </td>
				</tr>
				<tr align=center height=30>
					<td class='input_box_title'> 재고평가 </td>
					<td class='input_box_item' >
					<select name='valuation' validation=true title='재고평가'>
						<option value=''>재고평가</option>
						<option value='1' ".($idb->dt[valuation] == 1 || $idb->dt[valuation] == ""? "selected":"").">이동식 평균법</option>
						<!--option value='2' ".($idb->dt[valuation] == 2 ? "selected":"").">선입선출산출</option>
						<option value='3' ".($idb->dt[valuation] == 3 ? "selected":"").">후입선출산출</option>
						<option value='4' ".($idb->dt[valuation] == 4 ? "selected":"").">평행</option-->
					</select>
					</td>
					<td class='input_box_title' >
					 <span class='helpcloud' help_height='55' help_html=' Lot NO(Part NO), 혹은 시리얼이라고도 말하며, 생산라인번호를 등록관리함, (생상공정에서 필요) '>생산라인번호</span>
					</td>
					<td class='input_box_item' >
					<input type=text class='textbox' name='lotno' style='width:130px;' value='".$idb->dt[lotno]."'>  </td>
				</tr>
			</table><br>";



$Contents = $Contents."
			<table width='100%' style='margin:10px 0px '>
			<tr height=30 align=left><td width=500></td>";

if ($gid == "" || $mode == "copy"){
	$Contents .= "<td align=right>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$Contents .= "<img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'tmp_insert');\"> ";
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'insert');\">";
	}else{
		$Contents .= "<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle /></a>";
		$Contents .= "<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle /></a>";
	}
	$Contents .= "</td>";
}else{
	$Contents .= "<td align=right >";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$Contents .= "<img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'tmp_update');\"> ";
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'update')\"> ";
	}else{
		$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_save_tmp.gif' border=0 align=absmiddle ></a>";
		$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a>";
	}
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
		//$Contents .= "<img src='../images/".$admininfo["language"]."/b_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'delete')\">";
	}else{
		//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/b_del.gif' border=0 align=absmiddle></a>";
	}
	$Contents .= "</td>";
}
$Contents .= "

			</td></tr>
			</table>
			</form>";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td   > 연결 품목은 디자인, 색상 등은 같으나 사이트가 다르고, 매입가가 같을 경우 사용하시면 유용하게 사용하실 수 있습니다.
</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 매입가나 , 기본 도소매가가 다른 경우는 별도의 품목으로 등록합니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 바코드가 다른 품목은 별도의 품목으로 등록한다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > SET 나 BOX 의 경우는 단위수량의 품목코드를 재고코드에 입력하고 환산수량을 입력하시면 실재 재고 차감은 재고코드의 품목이 차감되게 됩니다. </td></tr>
</table>
";


$help_text = HelpBox("재고 품목등록", $help_text);
$Contents .= $help_text;

$Script = "
<script Language='JavaScript' src='../product/addoption.js'></script>
<script language='JavaScript' src='../js/dd.js'></script>
<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->
<script Language='JavaScript' src='../include/DateSelect.js'></script>
<script Language='JavaScript' src='../product/buyingService.js'></script>
<script Language='JavaScript' src='./inventory_goods_input.js'></script>
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>

\n$Script";


if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;

	if ($gid != ""){
		if ($admininfo[admin_level] == 9){
			$P->OnloadFunction = "";
		}else{
			$P->OnloadFunction = "";
		}
	}else{
		$P->OnloadFunction = "ChangeUnit( document.getElementById('basic_unit') );";
	}

	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	$P->Navigation = "재고관리 > 재고품목 등록/수정";
	$P->NaviTitle = "재고품목 등록/수정";
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	if ($gid != ""){
		if ($admininfo[admin_level] == 9){
			$P->OnloadFunction = "";
		}else{
			$P->OnloadFunction = "";
		}
	}else{
		$P->OnloadFunction = "ChangeUnit( document.getElementById('basic_unit') );";
	}


	$P->strLeftMenu = inventory_menu();
	$P->Navigation = "재고관리 > 재고품목 등록/수정";
	$P->title = "재고품목 등록/수정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


?>