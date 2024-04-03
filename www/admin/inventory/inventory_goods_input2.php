<?
include("../class/layout.class");
include_once("../inventory/inventory.lib.php");

//include_once("../product/goods_input.lib.php");
//include_once("buyingService.lib.php");

//print_r($admininfo);
//exit;
//echo exif_imagetype("/home/simpleline/www/data/simpleline/images/product/basic_1705.gif");

$db = new Database;

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

	if(sel.selectedIndex!=0) {
		window.frames['act'].location.href = 'inventory_category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

}

function ChnageImg(img_path)
{
	document.getElementById('viewimg').innerHTML = '<img src=\"'+img_path+'\" id=chimg>'

}

</script>";

if ($gid != ""){
	$db->query("SELECT * FROM inventory_goods where gid = $gid");

	if($db->total != 0)
	{
	$db->fetch(0);

	$act = "update";
	$gname = str_replace("'","&#39;",trim($db->dt[gname]));
	$gcode = $db->dt[gcode];
	$admin = $db->dt[admin];
	$cid = $db->dt[cid];
	$goods_div = $db->dt[goods_div];
	$model = $db->dt[model];
	$orgin = $db->dt[orgin];
	$maker = $db->dt[maker];

	$ci_ix = $db->dt[ci_ix];
	$pi_ix = $db->dt[pi_ix];

	$bs_goods_url = $db->dt[bs_goods_url];
	$search_keyword = str_replace("'","&#39;",$db->dt[search_keyword]);
	$etc = $db->dt[etc];

	$bimg_text = $db->dt[bimg];
	}
}else{
	$act = "insert";
	$admin = $admininfo[company_id];
}

$Contents = "
	<table cellpadding=0 width=100%>
		<tr>
		    <td align='left' colspan=4 > ".GetTitleNavigation("재고상품등록", "재고관리 > 재고상품등록")."</td>
		</tr>";

$Contents .= "
	</table>";

$Contents .= "

			<form name=inventory_goods_input action='./inventory_goods_input.act.php' method='post' enctype='multipart/form-data' onsubmit='return SubmitX(this);' target='act'>
			<input type='hidden' name=act value='".$act."'>
			<input type='hidden' name=admin value='".$admin."'>
			<input type='hidden' name=bgid value='".$gid."'>
			<input type='hidden' name=gid value='".$gid."'>
			<input type='hidden' name=mmode value='".$mmode."'>
			<input type='hidden' name=mode value='".$mode."'>
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
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 기본정보 : </b><span class=small></td></tr></table>")."</td></tr></table>
			<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
				<col width=15%>
				<col width=35%>
				<col width=15%>
				<col width=35%>
				<tr>
					<td class='search_box_title' >상품분류 선택 <img src='".$required3_path."'></td>
					<td class='search_box_item' colspan=3>
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>".getInventoryCategoryList("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid ,'true')."</td>
								<td style='padding-right:5px;'>".getInventoryCategoryList("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid)."</td>
								<td style='padding-right:5px;'>".getInventoryCategoryList("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid)."</td>
								<td style='padding-right:5px;'>".getInventoryCategoryList("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid)."</td>
								<td>
									<a href=\"javascript:PoPWindow3('../inventory/inventory_category.php?mmode=pop',960,600,'inventory_category')\"'><img src='../images/".$admininfo["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>";

if($admininfo[admin_level] == 9){
$Contents .= "
				<tr>
					<td class='input_box_title' nowrap> <b>상품명</b> <img src='".$required3_path."'></td>
					<td class='input_box_item'><input type=text class='textbox' name=gname size=28 style='width:90%' value='".$gname."' validation=true title='상품명'></td>
					<td class='input_box_title' nowrap> <b>입점업체 <img src='".$required3_path."'></b></td>
					<td class='input_box_item'>".companyAuthList($admin , "validation=true title='입점업체' ")."</td>
				</tr>";
}else{
$Contents .= "
				<tr>
					<td class='input_box_title' nowrap> <b>상품명</b> <img src='".$required3_path."'></td>
					<td class='input_box_item' colspan='3'><input type=text class='textbox' name=gname size=28 style='width:90%' value='".$gname."' validation=true title='상품명'></td>
				</tr>";
}
$Contents .= "
				<tr>
					<td class='input_box_title'> <b>상품코드 </b> <img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%'>
						<input type=text class='textbox' name=gcode size=28 style='width:150px' value='".$gcode."' validation=false title='상품코드'> 오프라인관리코드
					</td>
					<td class='input_box_title'> 바코드 </td>
					<td class='input_box_item' style='line-height:150%'>
					<input type=text class='textbox' name=barcode size=28 style='width:150px' value='".$barcode."' validation=false title='바코드'> 바코드생성
					<span>* 바코드 생성 규칙 필요</span>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> <b>기본단위 </b><img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%'>
						<select name='basic_unit'>
							<option value=''>기본단위</option>
							<option value='g'>g</option>
							<option value='kg'>kg</option>
							<option value='EA'>EA</option>
							<option value='SET'>SET</option>
							<option value='BOX'>BOX</option>
						</select>
					</td>
					<td class='input_box_title'> <b>매입/매출 기본단위 </b><img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%'>
						<table  >
							<col width='50px'>
							<col width='100px'>
							<col width='50px'>
							<col width='100px'>
							<tr>
								<td align=center><b>매입 :</b></td>
								<td>
								<select name='order_basic_unit'>
									<option value=''>매입 기본단위</option>
									<option value='g'>g</option>
									<option value='kg'>kg</option>
									<option value='EA'>EA</option>
									<option value='SET'>SET</option>
									<option value='BOX'>BOX</option>
								</select>
								</td>
								<td align=center><b>매출 :</b></td>
								<td>
								<select name='sell_basic_unit'>
									<option value=''>매출 기본단위</option>
									<option value='g'>g</option>
									<option value='kg'>kg</option>
									<option value='EA'>EA</option>
									<option value='SET'>SET</option>
									<option value='BOX'>BOX</option>
								</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>";
if(false){
$Contents .= "
				<tr>
					<td class='input_box_title'> <b>매출 기본단위 </b><img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%'>
						<select name='sell_basic_unit'>
							<option value=''>매출 기본단위</option>
							<option value='EA'>EA</option>
							<option value='SET'>SET</option>
							<option value='BOX'>BOX</option>
						</select>
					</td>
					<td class='input_box_title'> <!--b>단종여부 </b><img src='".$required3_path."'--></td>
					<td class='input_box_item' style='line-height:150%'>
						<!--select name=''>
							<option value=''>단종여부</option>
							<option value='Y'>판매중</option>
							<option value='N'>판매중지</option>
						</select-->
					</td>
				</tr>";
}
$Contents .= "
				<tr>
					<td class='input_box_title'> <b>품목계정 </b><img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%'>
						<select name='item_acccount'>
							<option value=''>상품분류</option>
							<option value='1'>원재로</option>
							<option value='2'>부재료</option>
							<option value='3'>반제품</option>
							<option value='4'>완제품(상품)</option>
							<option value='5'>용역</option>
							<option value='6'>저장품</option>
						</select>
					</td>
					<td class='input_box_title' nowrap> <b>모델명</b></td>
					<td class='input_box_item'>
						<input type=text class='textbox' name='model' size=28 style='width:150px' value='".$model."' validation=false title='모델명'>
					</td>
				</tr>";
				

				$Contents .= "<tr>
					<td class='input_box_title'> 입고처</td>
					<td class='input_box_item'>".SelectSupplyCompany($ci_ix,"ci_ix","select", "false")."</td>

					<td class='input_box_title'> 기본 입고창고 </td>
					<td class='input_box_item'>".SelectInventoryInfo($pi_ix,'pi_ix','select','false')."</td>
				</tr>";
				$Contents .= "
				<tr>
					<td class='input_box_title'> 원산지 </td>
					<td class='input_box_item' style='line-height:150%'>
					<input type=text class='textbox' name='orgin' style='width:150px;' value='".$orgin."'><br></td>
					<td class='input_box_title'> 제조사 </td>
					<td class='input_box_item' style='line-height:150%'>
					<input type=text class='textbox' name='maker' style='width:150px;' value='".$maker."'><br></td>
				</tr>
				";
$Contents .=	"
				<tr>
					<td class='input_box_title'> <span class='helpcloud' help_height='30' help_width='200' help_html='온라인 구매 가능상품의 경우 입력관리 하실수 있습니다. '>상품구매URL</span> </td>
					<td class='input_box_item' style='padding:5px 5px;line-height:150%' >
						<input type=text class='textbox' name='bs_goods_url' style='width:90%' value='".$bs_goods_url."'>
					</td>
					<td class='input_box_title'> 검색키워드 </td>
					<td class='input_box_item' style='padding:5px 5px;line-height:150%' >
						<input type=text class='textbox' name='search_keyword' style='width:90%' value='".$search_keyword."'>
					</td>
				</tr>";

$Contents .=	"
				
				
				<tr>
					<td class='input_box_title'> 기타</td>
					<td class='input_box_item' style='padding:5px 5px;line-height:150%' style='' colspan='3'>
						<input type=text class='textbox' name='etc' style='width:96%' value='".$etc."'>
					</td>
				</tr>";

$Contents .=	"
			</table><br>";
		
$Contents .="
<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 기본가격 정보  </b><span class=small></td></tr></table>")."</td></tr></table>
			<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box'>
				<col width=15%>
				<col width=35%>
				<col width=15%>
				<col width=35%>
				<tr>
					<td class='input_box_title'> 매입가 <img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%' colspan=3>
					<input type=text class='textbox' name='input_price' style='width:100px;' value='".$input_price."'> 원</td>
				</tr>
				<tr>
					<td class='input_box_title'> 기본도매가 <img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%' colspan=3>
					<input type=text class='textbox' name='basic_wholesale_price' style='width:100px;' value='".$basic_wholesale_price."'> 원 &nbsp;&nbsp;&nbsp;&nbsp; 마진율 <input type=text class='textbox' name='basic_wholesale_margin' style='width:100px;' value='".$basic_wholesale_margin."'> %</td>
				</tr>
				<tr>
					<td class='input_box_title'> 기본소매가 <img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%' colspan=3>
					<input type=text class='textbox' name='basic_sellprice' style='width:100px;' value='".$basic_sellprice."'> 원  &nbsp;&nbsp;&nbsp;&nbsp; 마진율 <input type=text class='textbox' name='basic_sellprice_margin' style='width:100px;' value='".$basic_sellprice_margin."'> %</td>
				</tr>
				<tr>
					<td class='input_box_title'> 부가세 적용 <img src='".$required3_path."'></td>
					<td class='input_box_item' style='line-height:150%' colspan=3>
					<select name='tax_div' validation=true title='부가세 적용'>
							<option value=''>부가세 적용</option>
							<option value='1'>부과세포함</option>
							<option value='2'>부과세별도</option>
							<option value='3'>영세율적용</option>
							<option value='4'>면세율적용</option>
							<option value='5'>부가세없음</option>
						</select>
					</td>
				</tr>
			</table><br>";

$Contents .="
<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> SET, BOX 정보  </b><span class=small></td></tr></table>")."</td></tr></table>
			<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box'>
				<col width=20%>
				<col width=25%>
				<col width=25%>
				<col width=30%>
				<tr align=center height=27>
					<td class='s_td'> </td>
					<td class='m_td'> 단위당 수량</td>			
					<td class='m_td'> 매입가</td>
					<td class='e_td'> 바코드</td>
				</tr>
				<tr align=center height=30>
					<td class='input_box_title'> <label for='box_use'>BOX  설정</label> <input type=checkbox name='box_use' id='box_use'  value='Y' ".(($db->dt[box_use] == 'Y') ? "checked":"")."></td>
					<td class='list_box_item' >
					BOX 당 <input type=text class='textbox number' name='box_cnt' style='width:70px;' value='".$box_cnt."'> 개 
					</td>
					<td class='list_box_item' >					
					<input type=text class='textbox number' name='box_order_price' style='width:100px;' value='".$box_order_price."'> 원
					</td>
					<td class='list_box_item' >
					<input type=text class='textbox' name='box_barcode' style='width:130px;' value='".$box_barcode."'> 바코드생성 </td>					
				</tr>
				<tr align=center height=30>
					<td class='input_box_title'> <label for='set_use'>SET 설정</label>  <input type=checkbox name='set_use' id='set_use'  value='Y' ".(($db->dt[set_use] == 'Y') ? "checked":"")."> </td>
					<td class='list_box_item' >
					SET 당 <input type=text class='textbox number' name='set_cnt' style='width:70px;' value='".$set_cnt."'> 개 
					</td>
					<td class='list_set_item' >					
					<input type=text class='textbox number' name='set_order_price' style='width:100px;' value='".$set_order_price."'> 원
					</td>
					<td class='list_set_item' >
					<input type=text class='textbox' name='set_barcode' style='width:130px;' value='".$set_barcode."'> 바코드생성 </td>					
				</tr>
			</table><br>";

		$Contents .="
				<table width='100%' cellpadding=0 cellspacing=0 style='position:relative'>
						<tr height=30>
						<td style='padding-bottom:5px;'>".colorCirCleBox("#efefef","100%","<div style='padding:0px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b class=blk> 단품 정보(연결 상품정보)</b><a onclick=\"newCopyOptions('inventory_goods_items_input');showMessage('options_basic_item_input_status_area','단품정보가 추가 되었습니다.');\" ><img src='../images/".$admininfo["language"]."/btn_add.gif' border=0 style='margin:2px 0 3px 5px; vertical-align:middle;'></a> </div>")."</td>
						</tr>
				</table>";


		$Contents .= "
							<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='options_basic_input' class='options_basic_input' style='margin-bottom:10px'>
								<col width='25%'>
								<!--col width='13%'>
								<col width='13%'-->
								<col width='15%'>
								<col width='15%'>
								<col width='10%'>
								<col width='10%'>
								<col width='27%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" > 규격 <img src='".$required3_path."'></td>
									<!--td bgcolor=\"#efefef\" > 단위 </td>
									<td bgcolor=\"#efefef\" > 입고예정단가 <img src='".$required3_path."'></td>
									<td bgcolor=\"#efefef\" > 출고예정단가 <img src='".$required3_path."'></td-->
									<td bgcolor=\"#efefef\" > 단품별재고 <img src='".$required3_path."'></td>
									<td bgcolor=\"#efefef\" > 안전재고 </td>
									<td bgcolor=\"#efefef\" > BOX 사용 <img src='".$required3_path."'></td>
									<td bgcolor=\"#efefef\" > SET 사용 <img src='".$required3_path."'></td>
									<td bgcolor=\"#efefef\" > 단종여부 </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td colspan=6 id='inventory_basic_item_input_table'>";

		$sql = "select * from inventory_goods_item where gid = '".$gid."' and gi_ix != '0' order by gi_ix ";

		$db->query($sql);

		if($db->total){
			for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

		$Contents .= "
										<table width=100% id='inventory_goods_items_input' class='inventory_goods_items_input' cellspacing=0 cellpadding=0 >
										<col width='25%'>
										<!--col width='13%'>
										<col width='13%'-->
										<col width='15%'>
										<col width='15%'>
										<col width='10%'>
										<col width='10%'>
										<col width='27%'>
											<tr >
												<td height='30'>
													<input type=hidden name='goods_items[".$i."][gi_ix]' id='gi_ix' value='".$db->dt[gi_ix]."'>
													<input type=text class='textbox' name='goods_items[".$i."][item_name]' id='item_name' style='width:90%;vertical-align:middle' validation=true title='규격'  value='".$db->dt[item_name]."'>
												</td>
												<!--td><input type=text class='textbox' name='goods_items[".$i."][unit]' id='unit'  style='width:90%;' value='".$db->dt[unit]."' validation=false title='단위' ></td>
												<td><input type=text class='textbox number' name='goods_items[".$i."][input_price]' id='input_price'  style='width:90%;' value='".$db->dt[input_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' validation=true title='입고예정단가'></td>
												<td><input type=text class='textbox number' name='goods_items[".$i."][output_price]' id='output_price'  style='width:90%;' value='".$db->dt[output_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' validation=true title='출고예정단가' ></td-->
												<td><input type=text class='textbox' name='goods_items[".$i."][item_sell_ing_cnt]' id='item_sell_ing_cnt' style='width:30px;margin:0px 3px;'  value='".$db->dt[item_sell_ing_cnt]."' title='판매진행중 재고' readonly><input type=text class='textbox number' name='goods_items[".$i."][item_stock]' id='item_stock' style='width:50px;".($layout_config["mall_use_inventory"] == "Y" ? "background:#efefef;":"")."' value='".$db->dt[item_stock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ".($layout_config["mall_use_inventory"] == "Y" ? "readonly onclick=\"alert('재고관리 사용시는 재고 수정을 임의로 하실 수 없습니다. 재고관리 입고, 출고 하기를 이용하시기 바랍니다.');\"":"")."></td>
												<td><input type=text class='textbox number' name='goods_items[".$i."][item_safestock]' id='item_safestock' style='width:90%;' value='".$db->dt[item_safestock]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=checkbox name='goods_items[".$i."][box_yn]' id='box_yn'  value='Y' ".(($db->dt[box_yn] == 'Y') ? "checked":"")."></td>
												<td><input type=checkbox name='goods_items[".$i."][set_yn]' id='set_yn'  value='Y' ".(($db->dt[set_yn] == 'Y') ? "checked":"")."></td>
												<td>
												<!--input type=text class='textbox' name='goods_items[".$i."][item_code]' id='item_code' style='width:108px' value='".$db->dt[item_code]."'-->
												<select name='goods_items[0][discontinued]'>
													<option value=''>단종여부</option>
													<option value='Y' ".($db->dt[discontinued] == "Y" ? "selected":"").">판매중</option>
													<option value='N' ".($db->dt[discontinued] == "N" ? "selected":"").">판매중지</option>
												</select>

												<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' title='더블클릭시 해당 라인이 삭제 됩니다.' ondblclick=\"if($('.inventory_goods_items_input').length > 1){document.getElementById('options_basic_item_input_table').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('options_basic_item_input_status_area','단품정보가 삭제 되었습니다.');}else{clearInputBox('options_basic_input');$('goods_items_option_name').value='';showMessage('options_basic_item_input_status_area','단품정보가 삭제 되었습니다.');}\"></td>";

		$Contents .= "
													</tr>
												</table>";
			}
		}else{

			$Contents .= "
										<table width=100% border=0 id='inventory_goods_items_input' class='inventory_goods_items_input' cellspacing=0 cellpadding=0 >
											<col width='25%'>
											<!--col width='13%'>
											<col width='13%'-->
											<col width='15%'>
											<col width='15%'>
											<col width='10%'>
											<col width='10%'>
											<col width='27%'>
											<tr align='center'>
												<td height='30'>
													<input type=hidden name='goods_items[0][gi_ix]' id='gi_ix'  value=''>
													<input type=text class='textbox' name='goods_items[0][item_name]' id='item_name' inputid='inventory_item_name' validation=true title='규격' style='width:90%;vertical-align:middle' value=''>
												</td>
												<!--td><input type=text class='textbox' name='goods_items[0][unit]' id='unit'  style='width:90%;' value='".$db->dt[unit]."' validation=false title='단위' ></td>
												<td><input type=text class='textbox number' name='goods_items[0][input_price]' id='input_price'  style='width:90%;' value='".$db->dt[input_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' validation=true title='입고예정단가'></td>
												<td><input type=text class='textbox number' name='goods_items[0][output_price]' id='output_price'  style='width:90%;' value='".$db->dt[output_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' validation=true title='출고예정단가'></td-->
												<td><input type=text class='textbox' name='goods_items[0][item_sell_ing_cnt]' id='item_sell_ing_cnt' style='width:30px;margin:0px 3px;'  value='' title='판매진행중 재고' readonly><input type=text class='textbox number' name='goods_items[0][item_stock]' id='item_stock' style='width:50px;".($layout_config["mall_use_inventory"] == "Y" ? "background:#efefef;":"")."' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)' ".($layout_config["mall_use_inventory"] == "Y" ? "readonly onclick=\"alert('재고관리 사용시는 재고 수정을 임의로 하실 수 없습니다. 재고관리 입고, 출고 하기를 이용하시기 바랍니다.');\"":"")." validation=false title='단품별재고' ></td>
												<td><input type=text class='textbox number' name='goods_items[0][item_safestock]' id='item_safestock' style='width:90%;' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=checkbox name='goods_items[0][box_yn]' id='box_yn'  value='Y' ></td>
												<td><input type=checkbox name='goods_items[0][set_yn]' id='set_yn'  value='Y' ></td>
												<td>
												<!--input type=text class='textbox' name='goods_items[0][item_code]' id='item_code' style='width:108px' value=''-->
												<select name='goods_items[0][use_yn]'>
													<option value=''>단종여부</option>
													<option value='Y'>판매중</option>
													<option value='N'>판매중지</option>
												</select>
												
												<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' title='더블클릭시 해당 라인이 삭제 됩니다.' ondblclick=\"if($('.inventory_goods_items_input').length > 1){document.getElementById('options_basic_item_input_table').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('options_basic_item_input_status_area','단품정보가 삭제 되었습니다.');}else{clearInputBox('options_basic_input');$('goods_items_option_name').value='';showMessage('options_basic_item_input_status_area','단품정보가 삭제 되었습니다.');}\"> </td>
											</tr>
										</table>";
		}
		$Contents .= "
									</td>
								</tr>
							</table>
							<div style='height:10px;text-align:right;color:gray;line-height:220%;' id='options_basic_item_input_status_area'></div><br>";
		/*	
			$Contents .="	<div style='line-height:130%;padding:0px 0px 20px 0px'>
									예) 규격 : RED / 95size, RED / 100size
								</div>";
*/

if ($gid != ""){
	$img_view_style = " style='display:block;'";
}else{
	$img_view_style = " style='display:none;''"	;
}

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

									if(file_exists($DOCUMENT_ROOT.InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "b"))){
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
							<input type=text name='bimg_text' id='bimg_text' class='textbox' style='width:90%;font-size:8pt;margin:3px;' value='".$bimg_text."'>
							<div class=small> <!--URL 이미지복사를 체크하시면 입력된 이미지 URL 정보를 바탕으로 이미지가 복사됩니다. 단 해당이미지 서버에서 이미지 복사를 차단한 경우는 이미지 복사가 거부될 수 있습니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'Q')." </div>
							</td>
						</tr>
						<!--tr>
							<td class='input_box_item' style='padding:10px 5px;' colspan='2' id=viewimg>";
									if(file_exists($DOCUMENT_ROOT.InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "b"))){
										$Contents = $Contents."<img src='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "b")."' onerror=\"this.src='../images/noimage_152_148.gif'\" style='border:1px solid silver' id=chimg>";
									}else{
										$Contents = $Contents."<img src='../images/noimage_152_148.gif' style='border:1px solid silver' id=chimg>";
									}
										$Contents = $Contents."</td>
							</td>
						</tr-->
					</table><br>
					</td>
					<td style='vertical-align:top;'>
						<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box'>
							<col width=30%>
							<col width=70%>
							<tr align=center height=31>
								<td class='s_td'> 분류</td>
								<td class='e_td'> 바코드</td>
							</tr>
							<tr align=center height=52>
								<td class='input_box_title'> 기본바코드</td>
								<td class='list_box_item' >
								등록후 생성
								</td>		
							</tr>
							<tr align=center height=52>
								<td class='input_box_title'> SET 상품 바코드</td>
								<td class='list_box_item' >
								등록후 생성
								</td>		
							</tr>
							<tr align=center height=52>
								<td class='input_box_title'> 기본바코드</td>
								<td class='list_box_item' >
								등록후 생성
								</td>		
							</tr>
						</table>
					</td>
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
					 <input type=text class='textbox number' name='leadtime' style='width:70px;' value='".$leadtime."'> 일(days)
					</td>
					<td class='input_box_title' >					
					일별생산량/구매가능량
					</td>
					<td class='input_box_item' >
					<input type=text class='textbox' name='available_amountperday' style='width:130px;' value='".$available_amountperday."'>  </td>					
				</tr>
				<tr align=center height=30>
					<td class='input_box_title'> 재고평가 </td>
					<td class='input_box_item' >
					<select name='valuation' validation=true title='재고평가'>
						<option value=''>재고평가</option>
						<option value='1'>이동식 평균법</option>
						<option value='2'>선입선출산출</option>
						<option value='3'>후입선출산출</option>
						<option value='4'>평행</option>
					</select>
					</td>
					<td class='input_box_title' >					
					 <span class='helpcloud' help_height='55' help_html=' Lot NO(Part NO), 혹은 시리얼이라고도 말하며, 생산라인번호를 등록관리함, (생상공정에서 필요) '>생산라인번호</span>
					</td>
					<td class='input_box_item' >
					<input type=text class='textbox' name='lotno' style='width:130px;' value='".$lotno."'>  </td>					
				</tr>
			</table><br>";


//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'R');

//$Contents .= HelpBox("재고상품등록", $help_text);



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
		$Contents .= "<img src='../images/".$admininfo["language"]."/b_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"InventoryGoodsInput(document.inventory_goods_input,'delete')\">";
	}else{
		$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/b_del.gif' border=0 align=absmiddle></a>";
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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td   > 연결 상품은 디자인, 색상 등은 같으나 사이트가 다르고, 매입가가 같을 경우 사용하시면 유용하게 사용하실 수 있습니다. 
</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 매입가나 , 기본 도소매가가 다른 경우는 별도의 상품으로 등록합니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > 바코드가 다른 상품은 별도의 상품으로 등록한다. </td></tr>
</table>
";


$help_text = HelpBox("재고 상품등록", $help_text);
$Contents .= $help_text;

$Script = "
<script Language='JavaScript' src='../product/addoption.js'></script>
<script language='JavaScript' src='../js/dd.js'></script>
<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->
<script type='text/javascript' src='../marketting/relationAjaxForEvent.js'></script>
<script Language='JavaScript' src='../include/DateSelect.js'></script>
<script Language='JavaScript' src='../product/buyingService.js'></script>

<script Language='JavaScript' src='./inventory_goods_input.js'></script>\n$Script";


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
		$P->OnloadFunction = "";
	}

	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	$P->Navigation = "재고관리 > 재고상품등록";
	$P->NaviTitle = "재고상품등록";
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
		$P->OnloadFunction = "";
	}


	$P->strLeftMenu = inventory_menu();
	$P->Navigation = "재고관리 > 재고상품등록";
	$P->title = "재고상품등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

/*
CREATE TABLE IF NOT EXISTS `inventory_goods` (
  `gid` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT '재고상품키값',
  `gname` varchar(255) NOT NULL COMMENT '재고상품명',
  `gcode` varchar(50) DEFAULT NULL COMMENT '재고상품 오프라인코드',
  `cid` varchar(??) DEFAULT NULL COMMENT '카테고리',
  `goods_div` varchar(50) DEFAULT NULL COMMENT '분류',
  `model` varchar(100) DEFAULT NULL COMMENT '모델명',
  `orgin` varchar(100) DEFAULT '' COMMENT '원산지',
  `maker` varchar(20) DEFAULT NULL COMMENT '제조사명',
  `ci_ix` int(6) unsigned DEFAULT NULL COMMENT '입고처',
  `pi_ix` int(6) unsigned DEFAULT NULL COMMENT '입고창고',
  `bs_goods_url` mediumtext COMMENT '상품구매url',
  `search_keyword` varchar(255) DEFAULT NULL COMMENT '검색키워드',
  `etc` varchar(255) DEFAULT NULL COMMENT '기타',
  `bimg` varchar(255) DEFAULT NULL COMMENT '이미지',
  `mimg` varchar(255) DEFAULT NULL COMMENT '이미지',
  `msimg` varchar(255) DEFAULT NULL COMMENT '이미지',
  `simg` varchar(255) DEFAULT NULL COMMENT '이미지',
  `cimg` varchar(255) DEFAULT NULL COMMENT '이미지',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`ipid`),
  KEY `IDX_IP_CID` (`cid`),
  KEY `IDX_IP_PCODE` (`pcode`),
  KEY `IDX_IP_CI_IX` (`ci_ix`),
  KEY `IDX_IP_PI_IX` (`pi_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='재고상품정보' AUTO_INCREMENT=1;

//  `admin_memo` varchar(255) DEFAULT NULL COMMENT '관리자메모',

1안!---------------------------------------------------------------------------------------------------
option_detail이 필요??? 옵션 구분값이 필요 없어져서...

CREATE TABLE IF NOT EXISTS `inventory_goods_items` (
  `gi_ix` int(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `gid` int(10) unsigned zerofill NOT NULL COMMENT '재고상품아이디',
  `item_name` varchar(100) NOT NULL COMMENT '단품명',
  `unit` varchar(100) DEFAULT NULL COMMENT '단위',
  `standard` varchar(100) DEFAULT NULL COMMENT '규격',
  `input_price` int(10) unsigned DEFAULT NULL COMMENT '입고단가',
  `output_price` int(10) unsigned DEFAULT NULL COMMENT '출고단가',

  `option_sell_ing_cnt` int(6) DEFAULT NULL COMMENT '옵션 판매진행중 재고',
  `option_stock` int(4) DEFAULT '0' COMMENT '옵션별재고',
  `option_safestock` int(4) DEFAULT '0' COMMENT '안전재고',

  `set_yn` enum('N','Y') DEFAULT 'N' COMMENT '세트상품여부',
  `item_code` varchar(50) DEFAULT '' COMMENT '옵션오프라인관리코드',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '수정시구분값',
  `editdate` datetime DEFAULT NULL COMMENT '수정일자',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`gi_ix`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='재고상품품목정보' AUTO_INCREMENT=1 ;

2안!------------------------------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `inventory_product_options` (
  `opn_ix` int(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `pid` int(10) unsigned zerofill NOT NULL COMMENT '재고상품아이디',

  `option_name` varchar(100) NOT NULL COMMENT '단품명',

  `option_use` char(1) NOT NULL DEFAULT '1' COMMENT '옵션사용여부',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '수정시구분값',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`opn_ix`),
  KEY `pid` (`pid`,`option_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='재고상품옵션정보' AUTO_INCREMENT=29594 ;

CREATE TABLE IF NOT EXISTS ``inventory_product_options_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `pid` int(10) unsigned zerofill DEFAULT NULL COMMENT '재고상품아이디',
  `opn_ix` int(6) DEFAULT NULL COMMENT '재고옵션인덱스값',
  `option_div` varchar(255) DEFAULT NULL COMMENT '옵션구분',

  `unit` varchar(100) DEFAULT NULL COMMENT '단위',
  `standard` varchar(100) DEFAULT NULL COMMENT '규격',
  `input_price` int(10) unsigned DEFAULT NULL COMMENT '입고단가',
  `output_price` int(10) unsigned DEFAULT NULL COMMENT '출고단가',
  `set_yn` enum('N','Y') DEFAULT 'N' COMMENT '세트상품여부',                                      <-???????????????????????

  `option_code` varchar(50) DEFAULT '' COMMENT '옵션오프라인관리코드',
  `option_sell_ing_cnt` int(6) DEFAULT NULL COMMENT '옵션 판매진행중 재고',
  `option_stock` int(4) DEFAULT '0' COMMENT '옵션별재고',
  `option_safestock` int(4) DEFAULT '0' COMMENT '안전재고',
  `option_etc1` varchar(100) DEFAULT '' COMMENT '옵션상세 기타필드',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '수정시구분값',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `pid_2` (`pid`,`option_div`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='상품 옵션상세정보' AUTO_INCREMENT=128528 ;

*/


/*
* 새로이 개선되는 재고관리 시스템에 상품정보에 추가되는 컬럼정보
* 2013년 3월 23일 신훈식 
ALTER TABLE `inventory_goods` ADD `barcode` VARCHAR( 50 ) NOT NULL COMMENT '기본바코드' AFTER `gcode` ;

ALTER TABLE `inventory_goods` ADD basic_unit VARCHAR( 10 ) NOT NULL COMMENT '기본단위' AFTER `barcode` ;
ALTER TABLE `inventory_goods` ADD order_basic_unit VARCHAR( 10 ) NOT NULL COMMENT '매입기본단위' AFTER basic_unit ;
ALTER TABLE `inventory_goods` ADD sell_basic_unit VARCHAR( 10 ) NOT NULL COMMENT '매출기본단위' AFTER order_basic_unit ;

ALTER TABLE `inventory_goods` CHANGE goods_div item_acccount VARCHAR( 2 ) NOT NULL COMMENT '품목계정'   ;


ALTER TABLE `inventory_goods` ADD `input_price` INT( 10 )  NOT NULL DEFAULT 0 COMMENT '입고단가' after etc;
ALTER TABLE `inventory_goods` ADD basic_wholesale_price INT( 10 )  NOT NULL DEFAULT 0 COMMENT '기본도매가' after `input_price`;
ALTER TABLE `inventory_goods` ADD basic_sellprice INT( 10 )  NOT NULL DEFAULT 0 COMMENT '기본소매가' after basic_wholesale_price;


ALTER TABLE `inventory_goods` ADD amount_perbox INT( 10 )  NOT NULL DEFAULT 0 COMMENT 'box 당 수량' after basic_sellprice;
ALTER TABLE `inventory_goods` ADD input_price_perbox INT( 10 )  NOT NULL DEFAULT 0 COMMENT 'box 매입가' after amount_perbox;
ALTER TABLE `inventory_goods` ADD box_barcode INT( 10 )  NOT NULL DEFAULT 0 COMMENT 'box 바코드' after input_price_perbox;


ALTER TABLE `inventory_goods` ADD amount_perset INT( 10 )  NOT NULL DEFAULT 0 COMMENT 'box 당 수량' after box_barcode;
ALTER TABLE `inventory_goods` ADD input_price_perset INT( 10 )  NOT NULL DEFAULT 0 COMMENT 'box 매입가' after amount_perset;
ALTER TABLE `inventory_goods` ADD set_barcode INT( 10 )  NOT NULL DEFAULT 0 COMMENT 'box 바코드' after input_price_perset;
ALTER TABLE `inventory_goods` ADD leadtime INT( 10 )  NOT NULL DEFAULT 0 COMMENT '발주후 배송가능일자' after set_barcode;
ALTER TABLE `inventory_goods` ADD available_amountperday INT( 10 )  NOT NULL DEFAULT 0 COMMENT '일별생산량/구매가능수량' after leadtime;
ALTER TABLE `inventory_goods` ADD valuation INT( 10 )  NOT NULL DEFAULT 0 COMMENT '재고평가' after available_amountperday;
ALTER TABLE `inventory_goods` ADD lotno INT( 10 )  NOT NULL DEFAULT 0 COMMENT '생산라인번호' after valuation;




//ALTER TABLE `inventory_goods_item` CHANGE `set_yn` `use_set` ENUM( 'N', 'Y' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'N' COMMENT '세트사용여부';
ALTER TABLE `inventory_goods_item` ADD `use_box` ENUM( 'N', 'Y' ) NOT NULL DEFAULT 'N' COMMENT 'BOX 사용여부' AFTER `item_safestock` ;
 ALTER TABLE `inventory_goods_item` ADD `discontinued` ENUM( 'N', 'Y' ) NOT NULL DEFAULT 'N' COMMENT '단종여부' AFTER `use_set`;

*/

?>