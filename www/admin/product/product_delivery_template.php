<?
include("../class/layout.class");
include_once("brand.lib.php");
//include("../webedit/webedit.lib.php");
$db = new Database;
$db2 = new Database;
$db3 = new Database;

if($info_type == ""){
	$info_type = "retail";
}

if($company_id){
	$company_id = $company_id;
}else{
	$company_id = $admininfo[company_id];
}

if($dt_ix){

	$sql = "select * from shop_delivery_template where company_id = '".$company_id."' and dt_ix = '".$dt_ix."'";
	$db->query($sql);
	$db->fetch();
	
	$dt_ix = $db->dt[dt_ix];
	$mall_ix = $db->dt[mall_ix];

	if($db->total > 0){
		$act = 'template_update';
	}else{
		$act = 'template_insert';
	}

}else{
	
	if($info_type == "whole"){	//도매
		$product_sell_type = "W";
	}else{	//소매
		$product_sell_type = "R";
	}
//echo "$product_sell_type";
	$sql = "select 
				*
			from
				common_company_detail as ccd
				inner join shop_delivery_template as dt on (ccd.company_id = dt.company_id)
			where
				ccd.com_type = 'A'
				and dt.product_sell_type = '".$product_sell_type."' ";
	//echo nl2br($sql);
	$db->query($sql);
	$db->fetch();
	$act = 'template_insert';
}

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 >
	<tr>
		<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
			<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>배송 정책</b>
		</td>
	</tr>
	</table>";

$Contents01 .= "
	<form name='edit_form' action='product_delivery.act.php' method='post' onsubmit='return SubmitX(this)' enctype='multipart/form-data' style='display:inline;' target='act'>
	<input name='act' type='hidden' value='$act'>
	<input name='info_type' type='hidden' value='$info_type'>
	<input name='company_id' type='hidden' value='".$company_id."'>
	<input name='pid' type='hidden' value='".$pid."'>
	<input name='dt_ix' type='hidden' value='".$dt_ix."'>
	<input name='product_sell_type' type='hidden' value='".$product_sell_type."'>
	<input name='page_type' type='hidden' value='".$page_type."'>
	<input name='mmode' type='hidden' value='".$mmode."'>
	
	<!--개별상품에서 템플릿 추가시 기본설정템플릿 사용안됨,셀러설정에서는 사용유무 체크(기본이 없을경우 기본으로 지정)-->

	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
	<col width='20%' />
	<col width='80' />";
	if($_SESSION["admin_config"][front_multiview] == "Y"){
	$Contents01 .= "
	<tr>
		<td class='search_box_title' > 프론트 전시 구분</td>
		<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
	</tr>";
	}
	$Contents01 .= "";

$Contents01 .= "
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>배송정책 명 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='text' name='template_name' id='template_name' class='textbox' value='".$db->dt[template_name]."' style='width:60%;' title='배송정책명' validation=true>
		</td>
	</tr>";

if($page_type == "seller"){
$Contents01 .= "
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>기본 배송정책 지정 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item'>
			<input type=radio name='is_basic_template' value='1' id='is_basic_template_1' ".($db->dt[is_basic_template] == '1' || $db->dt[is_basic_template] == ''?'checked':'')." ><label for='is_basic_template_1'>사용</label>
			&nbsp;
			<input type=radio name='is_basic_template' value='0' id='is_basic_template_0' ".CompareReturnValue("0",$db->dt[is_basic_template],"checked")."><label for='is_basic_template_0'>사용안함</label>
		</td>
	</tr>";
}else if($page_type == "goods_input"){
	$Contents01 .= "";
}else{
	$Contents01 .= "
	<input name='is_basic_template' type='hidden' value='0'>";
}

$Contents01 .= "
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>묶음배송 설정 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item'>
			<input type=radio name='delivery_package' value='N' id='delivery_package_N' ".($db->dt[delivery_package] == 'N' || $db->dt[delivery_package] == ''?'checked':'')." ><label for='delivery_package_N'>묶음배송</label>
			&nbsp;
			<input type=radio name='delivery_package' value='Y' id='delivery_package_Y' ".CompareReturnValue("Y",$db->dt[delivery_package],"checked")."><label for='delivery_package_Y'>개별배송</label>
		</td>
	</tr>";

$Contents01 .= "
	<tr bgcolor=#ffffff style='display: none;'>
		<td class='input_box_title'> <b>소매/도매 선택 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type=radio name='product_sell_type' value='R' id='product_sell_type_R' ".($db->dt[product_sell_type] == 'R' || $db->dt[product_sell_type] == ""?'checked':'')."><label for='product_sell_type_R'>소매</label>
			&nbsp;";
			
			if($_SESSION["admininfo"]["admin_level"] > 8){
				$Contents01 .= "
				<input type=radio name='product_sell_type' value='W' id='product_sell_type_W' ".CompareReturnValue("W",$db->dt[product_sell_type],"checked")."><label for='product_sell_type_W'>도매</label>";
			}

		$Contents01 .= "
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>배송 방법 선택 <img src='".$required3_path."'></b>   </td>
		<td class=''>
			<table width='100%' cellpadding=3 cellspacing=0 border='0' class='input_table_box'>
			<tr bgcolor=#ffffff>
				<td>
					<input type='radio' name='delivery_div' value='1' id='delivery_div_1' ".($db->dt[delivery_div] == "1" || $db->dt[delivery_div]== ""?'checked':'')."><label for='delivery_div_1'>택배/방문수령</label>
					<!--input type='radio' name='delivery_div' value='2' id='delivery_div_2' ".CompareReturnValue("2",$db->dt[delivery_div],"checked")."><label for='delivery_div_2'>퀵서비스/화물/용달(다마스)</label-->
					<!--<input type='radio' name='delivery_div' value='3' id='delivery_div_3' ".CompareReturnValue("3",$db->dt[delivery_div],"checked")."><label for='delivery_div_3'>직배송차량</label>-->
				</td>
			</tr>";
if($admininfo[admin_level] == '9'){
$Contents01 .= "
			<tr bgcolor=#ffffff>
				<td>
					<input type=hidden name='use_delivery_div_tekbae' value='1' id='use_delivery_div_tekbae' checked onclick='this.checked=!this.checked'>
					<!--
					<input type=checkbox name='use_delivery_div_quick' value='1' id='use_delivery_div_quick' ".CompareReturnValue("1",$db->dt[use_delivery_div_quick],"checked").">
					<label for='use_delivery_div_quick'>퀵서비스(착불)</label>-->

					<input type=hidden name='use_delivery_div_truck' value='1' id='use_delivery_div_truck'  checked onclick='this.checked=!this.checked'>

					<input type=hidden name='use_delivery_div_direct' value='1' id='use_delivery_div_direct' checked onclick='this.checked=!this.checked'>

					<!--input type=checkbox name='use_delivery_div_self' value='1' id='use_delivery_div_self' ".CompareReturnValue("1",$db->dt[use_delivery_div_self],"checked").">
					<label for='use_delivery_div_self'>방문수령</label-->
				</td>
			</tr>";
}else{
$Contents01 .= "
			<tr bgcolor=#ffffff>
				<td>
					<input type=hidden name='use_delivery_div_tekbae' value='1' id='use_delivery_div_tekbae' checked onclick='this.checked=!this.checked'>
					<input type=hidden name='use_delivery_div_truck' value='1' id='use_delivery_div_truck'  checked onclick='this.checked=!this.checked'>
					<input type=hidden name='use_delivery_div_direct' value='1' id='use_delivery_div_direct' checked onclick='this.checked=!this.checked'>
				</td>
			</tr>";
}
$Contents01 .= "
			<tr bgcolor='#ffffff' id='takbae_use' style='display:none;'>
				<td>
					<table width='100%' cellpadding=3 cellspacing=0 border='0'>
					<col width='15%' />
					<col width='*' />
					<tr bgcolor=#ffffff>
						<td align='center'> <b>택배업체</b></td>
						<td >
							<select name='tekbae_ix' id='tekbae_ix' style='width:100px;'>
							    <option value=''>선택해주세요</option>
							";
							$sql = "select company_id,delivery_company from common_seller_delivery where company_id = '".$company_id."'";
							$db3->query($sql);
							$db3->fetch();
							$code_ix = $db3->dt[delivery_company];
							
							$code_array = explode(",",$code_ix);
							$where = implode("','",$code_array);
							$sql = "select code_name,code_ix FROM ".TBL_SHOP_CODE." where code_gubun ='02' and code_ix in ('".$where."')";

							$db3->query($sql);
							$tekbae_ix_array = $db3->fetchall();
							for($i=0;$i<count($tekbae_ix_array);$i++){
				$Contents01 .= "<option value='".$tekbae_ix_array[$i][code_ix]."' ".($tekbae_ix_array[$i][code_ix] == $db->dt[tekbae_ix]?'selected':'').">".$tekbae_ix_array[$i][code_name]."</option>";
							}
$Contents01 .= "
							</select>
						</td>
					<tr>
					</table>
				</td>
			</tr>
			<tr bgcolor='#ffffff' id='quick_use' style='display:none;'>
				<td>
					<table width='100%' cellpadding=3 cellspacing=0 border='0'>
					<col width='15%' />
					<col width='*' />
					<tr bgcolor=#ffffff>
						<td align='center'> <b>퀵서비스</b></td>
						<td >
							퀵서비스 업체명 
							<input type='text' class='textbox' name='quick_company' id='quick_company' value='".$db->dt[quick_company]."' style='width:150px;' title='퀵서비스 업체명'>
							연락처 
							<input type='text' class='textbox' name='quick_phone' id='quick_phone' value='".$db->dt[quick_phone]."' style='width:90px;' title='퀵서비스 연락처'>
							<input type='button' name='delivery_addr_setup' id='delivery_addr_setup' value='설정' style='cursor:pointer;'>
						</td>
					<tr>
					<tr bgcolor=#ffffff>
						<td ></td>
						<td >
							<span class='small blu'>배송가능 지역 설정 귁서비스를 착불이며 지역마다 가격이 다를수 있습니다.</span>
						</td>
					<tr>
					</table>
				</td>
			</tr>
			<tr bgcolor='#ffffff' id='truck_use' style='display:none;'>
				<td>
					<table width='100%' cellpadding=3 cellspacing=0 border='0'>
					<col width='15%' />
					<col width='*' />
					<tr bgcolor=#ffffff>
						<td align='center'> <b>화물배달</b></td>
						<td >
							화물배달 업체명 
							<input type='text' class='textbox' name='truck_company' id='truck_company' value='".$db->dt[truck_company]."' style='width:100px;' title='화물배달 업체명'>
							연락처 
							<input type='text' class='textbox' name='truck_phone' id='truck_phone' value='".$db->dt[truck_phone]."' style='width:100px;'  title='화물배달 연락처'>
							<input type='button' name='delivery_addr_setup' id='delivery_addr_setup' value='설정' style='cursor:pointer;'>
						</td>
					<tr>
					<tr bgcolor=#ffffff>
						<td ></td>
						<td >
							<span class='small blu'>배송가능 지역 설정 귁서비스를 착불이며 지역마다 가격이 다를수 있습니다.</span>
						</td>
					<tr>
					<tr bgcolor=#ffffff>
						<td ></td>
						<td >
							추가: 담당자명 
							<input type='text' class='textbox' name='truck_person' id='truck_person' value='".$db->dt[truck_person]."' style='width:100px;'  title='화물배달 추가 담당자'>
							연락처 
							<input type='text' class='textbox' name='truck_person_phone' id='truck_person_phone' value='".$db->dt[truck_person_phone]."' style='width:100px;'  title='화물배달 추가 담당자 연락처'>
						</td>
					<tr>
					</table>
				</td>
			</tr>
			<tr bgcolor='#ffffff' id='direct_use' style='display:none;'>
				<td>
					<table width='100%' cellpadding=3 cellspacing=0 border='0'>
					<col width='15%' />
					<col width='*' />
					<tr bgcolor=#ffffff>
						<td align='center'> <b>직배송차량 선택</b></td>
						<td >
							<input type='radio' name='is_basic_direct' id='is_basic_direct_1' value='1' ".CompareReturnValue("1",$db->dt[is_basic_direct],"checked").">
							<label for='is_basic_direct_1'>기본 직배송 차량</label>
							<input type='button' name='delivery_addr_setup' id='delivery_addr_setup' value='기본 설정' style='cursor:pointer;'>
						</td>
					<tr>
					<tr bgcolor=#ffffff>
						<td ></td>
						<td >
							<input type='radio' name='is_basic_direct' id='is_basic_direct_0' value='0' ".CompareReturnValue("0",$db->dt[is_basic_direct],"checked").">
							<label for='is_basic_direct_0'>지정 차량 </label>
							<select name='direct_ddc_ix' id='direct_ddc_ix'>";
							$sql = "select * from shop_delivery_address where delivery_type = 'F' and company_id = '".$company_id."'";
							$db3->query($sql);
							$visit_info_array = $db3->fetchall();
							for($i=0;$i<count($visit_info_array);$i++){
			$Contents01 .= "<option value='".$visit_info_array[$i][addr_ix]."'>".$visit_info_array[$i][addr_name]."</option>";
							}
$Contents01 .= "
							</select>
							<input type='button' name='delivery_addr_setup' id='delivery_addr_setup' value='차량등록' style='cursor:pointer;'>
							<span class='small blu'>* WMS를 사용자만 사용 가능합니다.</span>
						</td>
					<tr>
					</table>
				</td>
			</tr>";

if($admininfo[admin_level] == '9'){
$Contents01 .= "
			<tr bgcolor='#ffffff' id='self_use' style='display:none;'>
				<td>
					<table width='100%' cellpadding=3 cellspacing=0 border='0'>
					<col width='15%' />
					<col width='*' />
					<tr bgcolor=#ffffff>
						<td align='center'> <b>방문수령</b></td>
						<td >
							<select name='visit_info_addr_ix' id='visit_info_addr_ix'>";
							$sql = "select * from shop_delivery_address where delivery_type = 'V' and company_id = '".$company_id."'";
							$db3->query($sql);
							$visit_info_array = $db3->fetchall();
							for($i=0;$i<count($visit_info_array);$i++){
			$Contents01 .= "<option value='".$visit_info_array[$i][addr_ix]."'>".$visit_info_array[$i][addr_name]."</option>";
							}
$Contents01 .= "
							</select>
							<input type='button' id='delivery_addr_setup' value='방문수령지 설정' onclick=\"javascript:PoPWindow3('../seller/company.add.php?info_type=visit_info&company_id=$company_id&mmode=pop',960,700,'delivery_info')\" style='cursor:pointer;'>
						</td>
					<tr>
					<tr bgcolor=#ffffff>
						<td align='center'> 
							<b><label for='direct'>사은품</label></b>
							<input type=checkbox name='use_free_gift' value='1' id='use_free_gift' ".CompareReturnValue("1",$db->dt[use_free_gift],"checked").">
						</td>
						<td>
							<input type='text' class='textbox' style='width:220px;' name='free_gift_name' id='free_gift_name' value='".$db->dt[free_gift_name]."' title='방문수령 사은품명'>
						</td>
					<tr>
					</table>
				</td>
			</tr>";
}
$Contents01 .= "
			</table>
		</td>
	</tr>";

$Contents01 .= "
	<tr bgcolor=#ffffff style='display:none;'>
		<td class='input_box_title'> <b>출고지별 배송비 설정 <img src='".$required3_path."'></b>   </td>
		<td>
		<input type=radio name='delivery_addr_use' value='0' id='delivery_addr_use_0' ".($db->dt[delivery_addr_use] == "0" || $db->dt[delivery_addr_use] == "" ?"checked":"").">
		<label for='delivery_addr_use_0'><b>미사용</b>
		
		<input type=radio name='delivery_addr_use' value='1' id='delivery_addr_use_1' ".CompareReturnValue("1",$db->dt[delivery_addr_use],"checked").">
		<label for='delivery_addr_use_1'><b>사용</b>

		<select name='factory_info_addr_ix' id='factory_info_addr_ix'>";
		$sql = "select * from shop_delivery_address where delivery_type = 'F' and company_id = '".$company_id."' and is_delivery_use = 'Y'";
		$db3->query($sql);
		$visit_info_array = $db3->fetchall();

$Contents01 .= "<option value='0'>출고지 선택</option>";
		for($i=0;$i<count($visit_info_array);$i++){
$Contents01 .= "<option value='".$visit_info_array[$i][addr_ix]."' ".($db->dt[factory_info_addr_ix] == $visit_info_array[$i][addr_ix]?"selected":"").">".$visit_info_array[$i][addr_name]."</option>";
		}
$Contents01 .= "
		</select>

		<input type='button' value='출고지 설정' onclick=\"javascript:PoPWindow3('../seller/company.add.php?info_type=factory_info&company_id=$company_id&mmode=pop',960,700,'delivery_info')\" style='cursor:pointer;'><br>
		<span class='small blu' style='padding-left:15px;'> * 출고지별 배송비 입니다.</span>
		</td>
	</tr>

	<tr bgcolor=#ffffff style='display: none;'>
		<td class='input_box_title'> <b>배송비 결제 수단 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item'>
			<input type=radio name='delivery_basic_policy' value='1' id='delivery_basic_policy_1' ".($db->dt[delivery_basic_policy] == "1" || $db->dt[delivery_basic_policy] == ""?'checked':'')."><label for='delivery_basic_policy_1'>선불</label>
			&nbsp;
			<!--input type=radio name='delivery_basic_policy' value='5' id='delivery_basic_policy_5'  ".CompareReturnValue("5",$db->dt[delivery_basic_policy],"checked")."><label for='delivery_basic_policy_5'>선불/착불 선택</label>
			&nbsp;-->
			<input type=radio name='delivery_basic_policy' value='2' id='delivery_basic_policy_2'  ".CompareReturnValue("2",$db->dt[delivery_basic_policy],"checked")."><label for='delivery_basic_policy_2'>착불</label>

			<div id='pay_method_text' style='padding:5px 5px 5px 5px; display:none'>
				착불명칭 : <input type='text' class='textbox' name='delivery_pay_metho_text' id='delivery_pay_metho_text' maxlen = '15' value='".$db->dt[delivery_pay_metho_text]."'>
				<span class='small blu'> * 착불명칭은 최대 15자리 까지 가능합니다. </span>
			</div>
		</td>
	</tr>

	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>조건 배송비 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:3px;'>
			<table border=0 cellpadding=4 width='100%' cellspacing=0 class='input_table_box'>
				<col width='10%' />
				<col width='*' />
				<tr bgcolor=#ffffff>
					<td>
					<input type=radio name='delivery_policy' value='1' id='delivery_policy_1'  ".CompareReturnValue("1",$db->dt[delivery_policy],"checked")." checked><label for='delivery_policy_1'><b>무료배송 </b> <span class='small blu'> <br/> * 묶음 배송에서 무료배송 선택시 묶음배송 배송비는 무료로 적용됩니다. <br/> * 착불 인 경우에 `(상품 수령 후 지불) / 상품 상세페이지 내 착불 배송료 확인` 텍스트 노출됩니다.</span></label>
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td>
						<input type=radio name='delivery_policy' value='2' id='delivery_policy_2' ".CompareReturnValue("2",$db->dt[delivery_policy],"checked")."><label for='delivery_policy_2'><b>고정 배송비</b>
						<input type='text' class='textbox numeric' value='".$db->dt[delivery_price]."' name='delivery_price' id='delivery_price' style='width:50px' title='고정 배송비'> 원 
						<span class='small blu'> * 주문/결제시 판매자의 묶음상품의 배송비 입니다.</span>
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td>
						<table border='0' cellpadding=0 width='100%' cellspacing=0 >
						<col width='25%' />
						<col width='*' />
						<tr>
							<td >
							<input type=radio name='delivery_policy' value='3' id='delivery_policy_3'  ".CompareReturnValue("3",$db->dt[delivery_policy],"checked").">
							<label for='delivery_policy_3'><b>결제금액당 배송비</b>
							</td>
							<td>
								<table border='0' cellpadding=3 width='100%' cellspacing=0 id='delivery_policy_3_terms'>
								<col width='23%' />
								<col width='*' />";
								if($dt_ix){
								$sql = "select
											dts.*
										from
											shop_delivery_template as dt
											inner join shop_delivery_terms as dts on (dt.dt_ix = dts.dt_ix)
										where
											dt.dt_ix = '".$dt_ix."'
											and dts.delivery_policy_type = '3'
											order by seq ASC limit 1";
								}else{
								$sql = "select
											dts.*
										from
											shop_delivery_template as dt
											inner join shop_delivery_terms as dts on (dt.dt_ix = dts.dt_ix)
										where
											dt.is_basic_template = '1'
											and company_id = '".$company_id."'
											and dts.product_sell_type = '".$product_sell_type."'
											and dts.delivery_policy_type = '3'
											and dt.company_id = '".$company_id."'
											order by seq ASC limit 1";
								}
								$db2->query($sql);
								$delivery_terms_array = $db2->fetchall();

								for($i=0;$i<count($delivery_terms_array) || $i==0;$i++){
$Contents01 .= "
								<tr bgcolor='#ffffff' id='add_table_price' aaaaaaaa>
									<td>
										<input type='hidden' name='option_length' id='option_length' value='".$i."'>
										<input type='text' class='textbox numeric' value='".$delivery_terms_array[$i][delivery_price]."' id='delivery_price' name='delivery_price_terms[".$i."][delivery_price]' style='width:50px'> 원, 
									</td>
									<td>
										주문금액
										<input type='text' class='textbox numeric' value='".$delivery_terms_array[$i][delivery_basic_terms]."' id='delivery_basic_terms' name='delivery_price_terms[".$i."][delivery_basic_terms]' style='width:70px'> 원 미만 일 경우
									</td>
									<!--td>
										<input type='button' id='delivery_price_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('delivery_policy_3_terms','delivery_price_terms','4')\">
										<input type='button' id='delivery_price_del' value='삭제' title='삭제' style='cursor:pointer;' >
									</td-->
								</tr>";
								}

$Contents01 .= "
								</table>
							</td>
						</tr>
						<tr>
							<td style='padding-left:10px;' colspan='2'>
								<span class='blu'>※ 최종 설정 금액 이상의 금액의 주문일 경우 배송비는 0원(무료)으로 계산 됩니다.</span>
							</td>
						</tr>
						</table>
					</td>
				</tr>

				<tr bgcolor=#ffffff>
					<td>
						<table border='0' cellpadding=0 width='100%' cellspacing=0 >
						<col width='100%' />
						<tr>
							<td style='padding-top:5px;'>
								<input type=radio name='delivery_policy' value='4' id='delivery_policy_4'  ".CompareReturnValue("4",$db->dt[delivery_policy],"checked").">
								<label for='delivery_policy_4'><b>수량별 할인/할증적용(상품단위)</b>
							</td>
						</tr>
						<!--tr>
							<td style='padding-left:25px; padding-top:5px;'>
								<input type='radio' name='extra_charge' value='1' id='extra_charge_1' ".($db->dt[extra_charge] == "1" || $db->dt[extra_charge] == ""?'checked':'').">
								<label for='extra_charge_1'>할인율 적용</label>&nbsp;&nbsp;&nbsp;
								<input type='radio' name='extra_charge' value='2' id='extra_charge_2' ".CompareReturnValue("2",$db->dt[extra_charge],"checked").">
								<label for='extra_charge_2'>할증율 적용</label>
							</td>
						</tr-->
						<tr>
							<td style='padding-left:25px;'>
								<table border='0' cellpadding=4 width='100%' height='100%' cellspacing=0>
								<tr bgcolor=#ffffff>
									<td >
										기본 배송비 
										<input type='text' class='textbox numeric' value='".$db->dt[delivery_cnt_price]."' name='delivery_cnt_price' id='delivery_cnt_price' style='width:50px' title='기본배송비'> 원, 
									</td>
									<td >
										<table border='0' cellpadding=0 width='100%' cellspacing=0 id='delivery_policy_4_terms'>
										<col width='35%' />
										<col width='*' />
										<col width='25%' />";
								if($dt_ix){
								$sql = "select
											dts.*
										from
											shop_delivery_template as dt
											inner join shop_delivery_terms as dts on (dt.dt_ix = dts.dt_ix)
										where
											dt.dt_ix = '".$dt_ix."'
											and dts.delivery_policy_type = '4'
											order by seq ASC";
								}else{
								$sql = "select
											dts.*
										from
											shop_delivery_template as dt
											inner join shop_delivery_terms as dts on (dt.dt_ix = dts.dt_ix)
										where
											dt.is_basic_template = '1'
											and company_id = '".$company_id."'
											and dts.product_sell_type = '".$product_sell_type."'
											and dts.delivery_policy_type = '4'
											and dt.company_id = '".$company_id."'
											order by seq ASC";
								}
								$db2->query($sql);
								$delivery_terms_array = $db2->fetchall();

								for($i=0;$i < count($delivery_terms_array) || $i==0;$i++){
$Contents01 .= "
										<tr id='add_table_cnt'>
											<td>
												<input type='hidden' name='option_length' id='option_length' value='".$i."'>
												<input type='text' class='textbox numeric' name='delivery_cnt_terms[".$i."][delivery_basic_terms]' value='".$delivery_terms_array[$i][delivery_basic_terms]."' id='delivery_basic_terms' style='width:70px'> 개 이상,
											</td>
											<td>
												<input type='text' class='textbox numeric' name='delivery_cnt_terms[".$i."][delivery_price]' value='".$delivery_terms_array[$i][delivery_price]."' id='delivery_price' style='width:70px'> 원 배송비 적용
											</td>
											<td>
												<input type='button' id='delivery_cnt_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('delivery_policy_4_terms','delivery_cnt_terms','4')\">
												<input type='button' id='delivery_cnt_del' value='삭제' title='삭제' style='cursor:pointer;' >
											</td>
										</tr>";
								}
$Contents01 .= "
										</table>
									</td>
								</tr>
								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<!--tr bgcolor=#ffffff>
					<td>
						<table border='0' cellpadding=0 width='100%' cellspacing=0 >
						<col width='100%' />
						<tr>
						    <td style='padding-top:5px;'>
                                <input type=radio name='delivery_policy' value='7' id='delivery_policy_7'  ".CompareReturnValue("7",$db->dt[delivery_policy],"checked").">
                                <label for='delivery_policy_7'><b>무게당 배송비 적용</b>
                            </td>
                        </tr>					
						<tr>
							<td style='padding-left:25px;'>
								<table border='0' cellpadding=4 width='100%' height='100%' cellspacing=0>
								<tr bgcolor=#ffffff>									
									<td >
									    주문금액  
										<input type='text' class='textbox numeric' value='".$db->dt[free_shipping_term]."' name='free_shipping_term' id='free_shipping_term' style='width:50px' title='기본배송비'>원 이상 무료배송 
									</td>
									<td >
										<table border='0' cellpadding=0 width='100%' cellspacing=0 id='delivery_policy_7_terms'>
										<col width='35%' />
										<col width='*' />
										<col width='25%' />";
								if($dt_ix){
								$sql = "select
											dts.*
										from
											shop_delivery_template as dt
											inner join shop_delivery_terms as dts on (dt.dt_ix = dts.dt_ix)
										where
											dt.dt_ix = '".$dt_ix."'
											and dts.delivery_policy_type = '7'
											order by seq ASC";
								}else{
								$sql = "select
											dts.*
										from
											shop_delivery_template as dt
											inner join shop_delivery_terms as dts on (dt.dt_ix = dts.dt_ix)
										where
											dt.is_basic_template = '1'
											and company_id = '".$company_id."'
											and dts.product_sell_type = '".$product_sell_type."'
											and dts.delivery_policy_type = '7'
											and dt.company_id = '".$company_id."'
											order by seq ASC";
								}
								$db2->query($sql);
								$delivery_terms_array = $db2->fetchall();

								for($i=0;$i < count($delivery_terms_array) || $i==0;$i++){
                                    $Contents01 .= "
										<tr id='add_table_cnt'>
											<td>
												<input type='hidden' name='option_length' id='option_length' value='".$i."'>
												<input type='text' class='textbox numeric' name='delivery_weight_terms[".$i."][delivery_basic_terms]' value='".$delivery_terms_array[$i][delivery_basic_terms]."' id='delivery_basic_terms' style='width:70px'> Kg 이하
											</td>
											<td>
												<input type='text' class='textbox numeric' name='delivery_weight_terms[".$i."][delivery_price]' value='".$delivery_terms_array[$i][delivery_price]."' id='delivery_price' style='width:70px'> 원 배송비 적용
											</td>
											<td>
												<input type='button' id='delivery_w_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('delivery_policy_7_terms', 'delivery_weight_terms', '5')\">
												<input type='button' id='delivery_w_del' value='삭제' title='삭제' style='cursor:pointer;' >
											</td>
										</tr>";
								}
                                    $Contents01 .= "
										</table>
									</td>
								</tr>
								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr-->
				<tr bgcolor=#ffffff>
					<td>
					<input type=radio name='delivery_policy' value='6' id='delivery_policy_6'  ".CompareReturnValue("6",$db->dt[delivery_policy],"checked")."><label for='delivery_policy_6'><b>상품 1개단위 배송비</b>
					<input type='text' class='textbox numeric' value='".$db->dt[delivery_unit_price]."' name='delivery_unit_price' id='delivery_unit_price' style='width:50px'> 원 
					<span class='small blu' style='padding-left:15px;'> * 본상품의 수량에 따라 배송비가 추가됩니다.</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=75>
		<td class='input_box_title'> <b>반품/교환 배송비 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table border=0 cellpadding=5 cellspacing=0>
				<tr>
					<td><b>반품 배송비</b> : 편도 
					<input type='text' name='return_shipping_price' class='textbox' value='".$db->dt[return_shipping_price]."' id='return_shipping_price'  style='width:50px' dir='rtl'>
					원 -> 무료배송시 부과방법 
					<input type=radio name='return_shipping_cnt' value='2' id='return_shipping_cnt_1' ".($db->dt[return_shipping_cnt] == '2' || $db->dt[product_sell_type] == ""?'checked':'')."> 
					<label for='return_shipping_cnt_1'>왕복 ( 편도 * 2 )</label> 
					<input type=radio name='return_shipping_cnt' value='1' id='return_shipping_cnt_2' ".CompareReturnValue("1",$db->dt[return_shipping_cnt],"checked")."> 
					<label for='return_shipping_cnt_2'>편도</label>
					<div style='line-height:130%;color:#F37361;padding-top:5px;'>
						&nbsp;1) 구매자 귀책 시 반품 배송비가 적용되며, 판매자 귀책 시 반품 배송비가 적용되지 않습니다.
						<br>&nbsp;2) 상품 구매 시 배송비를 결제한 반품 요청 주문건은 편도*1 가 적용됩니다.
						<br>&nbsp;3) 상품 구매 시 무료 배송일 경우 왕복을 선택하면 편도*2 가 적용되며, 편도를 선택하면 편도*1 로 적용됩니다.
						<br>&nbsp;4) 상품 구매 시 배송비 쿠폰을 이용한 주문건은 편도*1 가 적용됩니다.
					</div>
					</td>
				</tr>
				<tr>
					<td><b>교환 배송비</b> : 편도 <input type='text' name='exchange_shipping_price' class='textbox' value='".$db->dt[exchange_shipping_price]."' id='exchange_shipping_price'  style='width:50px' dir='rtl'> 원 <label for='delivery_product_policy_1'> </label> -> 왕복 ( 편도 * 2 )
					<div style='line-height:130%;color:#F37361;padding-top:5px;'>
						&nbsp;1) 구매자 귀책 시 교환 배송비가 적용되며, 판매자 귀책 시 교환 배송비가 적용되지 않습니다.
						<br>&nbsp;2) 상품 구매 시 배송비를 결제한 교환 요청 주문건은 편도*2 가 적용됩니다.
						<br>&nbsp;3) 상품 구매 시 무료 배송일 경우에도 편도*2 가 적용됩니다.
					</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>반품/교환 수령지 설정</b> <img src='".$required3_path."'>   </td>
		<td class='input_box_item'>
		<b>반품/교환 수령지 지정</b>
			<select name='exchange_info_addr_ix' id='exchange_info_addr_ix' validation=true title='반품/교환 수령지'>
			<option value=''>반품/교환 수령지 선택</option>
			";

			$sql = "select * from shop_delivery_address where delivery_type = 'E' and company_id = '".$company_id."'";
			$db3->query($sql);
			$visit_info_array = $db3->fetchall();
			for($i=0;$i<count($visit_info_array);$i++){
$Contents01 .= "<option value='".$visit_info_array[$i][addr_ix]."' ".($visit_info_array[$i][addr_ix] == $db->dt[exchange_info_addr_ix]?'selected':'').">".$visit_info_array[$i][addr_name]."</option>";
			}
$Contents01 .= "
			</select>
			<input type='button' value='반품/교환지 설정' onclick=\"javascript:PoPWindow3('../seller/company.add.php?info_type=exchange_info&company_id=$company_id&mmode=pop',960,700,'delivery_info')\" style='cursor:pointer;'>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title' ".($db->dt[delivery_region_use] == "0" ? "" : "rowspan=2")."> <b class='delivery_area_rowspan' >추가지역 배송비 설정</b> </td>
		<td class='input_box_item'>
		<input type='radio' id='delivery_region_use_1' name='delivery_region_use' value='1' ".CompareReturnValue("1",$db->dt[delivery_region_use],"checked")." onclick='showDeliveryArea(1);'><label for='delivery_region_use_1'>사용</label>
		<input type='radio' id='delivery_region_use_0' name='delivery_region_use' value='0' ".($db->dt[delivery_region_use] == "0" || $db->dt[delivery_region_use] == ""?'checked':'')." onclick='showDeliveryArea(0);'><label for='delivery_region_use_0'>사용안함</label>

		<input type='button' value='추가배송비 등록' onclick=\"javascript:PoPWindow3('/admin/delivery_jeju.php',960,700,'delivery_jeju')\" style='cursor:pointer;'>
		</td>
	</tr>
	<tr bgcolor=#ffffff style='".($db->dt[delivery_region_use] == "0" ? "display:none;" : "")."' class='delivery_area_div'>
		<td class='input_box_item'>
			<table border=0 cellpadding=0 cellspacing=0 width='100%' >
				<tr>
					<td>
						<input type='hidden' id='region_delivery_type_1' name='region_delivery_type' value='1' ><!--label for='region_delivery_type_1'>지역명</label-->
					</td>
				</tr>
				<tr height=50>
					<td id='region_name' style='padding:5px 5px 5px 0px;'>";
						
						/*$Contents01 .= "
						<table border=0 cellpadding=0 cellspacing=2 width='100%' id='region_name_table_add' class='region_name_table_add list_table_box'>
							<col width='30%'>
							<col width='*'>
							<tr height=30>
								<td align=center class=s_td>배송권역 선택</td>
								<td>
									<input type='radio' id='delivery_region_area_2' name='delivery_region_area' value='2' ".($db->dt[delivery_region_area] == "2" || empty($db->dt[delivery_region_area]) ? "checked" : "")." onclick='showDeliveryRegionArea(2);'><label for='delivery_region_area_2'>2권역</label>
									<input type='radio' id='delivery_region_area_3' name='delivery_region_area' value='3' ".CompareReturnValue("3",$db->dt[delivery_region_area],"checked")."><label for='delivery_region_area_3' onclick='showDeliveryRegionArea(3);'>3권역</label></br>
									
									<div>
										<table>
											<tr>
												<td class='jeju_area2'>".($db->dt[delivery_region_area] == "3" ? "제주 지역" : "제주 및 도서산간")."</td>
												<td>
													<input type='text' class='textbox number' ".($db->dt[delivery_region_use] == "1" ? "validation='true'" : "validation='false'")." name='delivery_jeju_price' value='".$db->dt[delivery_jeju_price]."' style='width:55%;' title='추가지역 배송비' > 원 추가됨
												</td>
											</tr>
											<tr class='jeju_area3' ".($db->dt[delivery_region_area] == "3" ? "" : "style='display:none;'").">
												<td>제주 외 도서산간</td>
												<td>
													<input type='text' class='textbox number' ".($db->dt[delivery_region_use] == "1" && $db->dt[delivery_region_area] == "3" ? "validation='true'" : "validation='false'")." name='delivery_except_price' style='width:55%;' value='".$db->dt[delivery_except_price]."' title='추가지역 배송비' > 원 추가됨
												</td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>";*/

						$Contents01 .= "
					</td>
				</tr>
				<tr>
					<td style='padding:5px 0px;'><span class='small blu'> 지역별 배송비는 '기본 배송정책', '상품별 배송정책'에 의해 배송비가 산정된 이후 금액이 추가 됩니다.</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<!--tr bgcolor=#ffffff>
		<td class='input_box_title' rowspan=2> <b>기타 설정</b> </td>
		<td class='input_box_item'>
			<b>평균 배송비 원가 </b>
			<input type='text' class='textbox numeric' name='delivery_corprice' id='delivery_corprice' value='".$db->dt[delivery_corprice]."' style='width:60px;'> 원/개,
			<b>평균 포장비 원가 </b>
			<input type='text' class='textbox numeric' name='packing_corprice' id='packing_corprice' value='".$db->dt[packing_corprice]."' style='width:60px;'> 원/개
		</td>
	</tr-->
	</table>
	<br>

	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<!--tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>C/S 문의</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td class='input_box_item'><textarea type=text class='textbox' name='product_prohibition_text' style='width:98%;height:85px;padding:7px;'>".$db->dt[product_prohibition_text]."</textarea></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>반품/교환 안내</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td class='input_box_item'><textarea type=text class='textbox' name='product_return_text' style='width:98%;height:85px;padding:7px;'>".$db->dt[product_return_text]."</textarea></td>
				</tr>
			</table>
		</td>
	</tr-->
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>PC 배송정책</b> </td>
		<td class='input_box_item' style='padding:5px;'>
			<div id='delivery_text_area' >
				<textarea name='delivery_policy_text' id='delivery_policy_text'>".$db->dt[delivery_policy_text]."</textarea>
			</div>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>모바일 배송정책</b> </td>
		<td class='input_box_item' style='padding:5px;'>
			<div id='delivery_text_area_m'>
				<textarea name='delivery_policy_text_m' id='delivery_policy_text_m'>".$db->dt[delivery_policy_text_m]."</textarea>
			</div>
		</td>
	</tr>
	<tr bgcolor=#ffffff style='display:none;'>
		<td class='input_box_title'> <b>모바일배송정책</b> </td>
		<td class='input_box_item' style='padding:5px;'>
			<input type='file' name='mobile_delivery_policy_img' >";
			if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/delivery/".$company_id.".gif")){
				$Contents01 .= "
				<div>
					<img src = 'http://".$_SERVER["HTTP_HOST"].$admin_config[mall_data_root]."/images/delivery/".$company_id.".gif'>
				</div>
				";
			}
$Contents01 .= "
		</td>
	</tr>
	</table>";

    $Contents01 .= "
	<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='checkbox' name='is_close' id='is_close' value='1'>
			<label for='is_close'>저장후닫기</label>
			<input type='image' src='../images/" . $admininfo["language"] . "/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
		</td>
	</tr>
	</table><br><br>";

$Contents01 .= "
	</form>
";


$Contents01 .= "
<iframe name='extand' id='extand' src='' width=0 height=0></iframe>
<script language='javascript'>

function SubmitX(frm){
	if(!CheckFormValue(frm)){
		return false;
	}

	return true;
}
</script>
";

//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$Contents01 .= HelpBox("상품별 배송 정책 설정", $help_text);

$Script = "
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
<script language='javascript'>
function showDeliveryArea(type){
	if(type == 1){
		$('.delivery_area_div').show();
		$('.delivery_area_rowspan').parent().attr('rowspan', '2');
		$('input[name=delivery_except_price]').attr('validation', 'true');
		$('input[name=delivery_jeju_price]').attr('validation', 'true');
	}else{
		$('.delivery_area_div').hide();
		$('.delivery_area_rowspan').parent().attr('rowspan', '');
		$('input[name=delivery_except_price]').attr('validation', 'false');
		$('input[name=delivery_jeju_price]').attr('validation', 'false');
	}
}

function showDeliveryRegionArea(type){
	if(type == 2){
		$('.jeju_area2').html('제주 및 도서산간');
		$('.jeju_area3').hide();
		$('input[name=delivery_except_price]').attr('validation', 'false');
	}else{
		$('.jeju_area2').html('제주 지역');
		$('.jeju_area3').show();
		$('input[name=delivery_except_price]').attr('validation', 'true');
	}
}

function insertInputBox(obj){
	var objs=$('table.'+obj).find('tr');
	if(objs.length > 0 ){
		var obj_table = objs[0].cloneNode(true);
		var target_obj = objs[objs.length-1];	
	}else{
		
		var obj_table = objs[0].cloneNode(true);
		var target_obj = objs[0];
	}
	var newRow = objs.clone(true).appendTo($('#region_name_table_add'));  
}

function change_delivery_div (delivery_div){

	if(delivery_div == '1'){
		dilplay_typeallnone();
		$('input[name=use_delivery_div_tekbae]').css('display','');
		$('input[name=use_delivery_div_quick]').css('display','');
		$('input[name=use_delivery_div_self]').css('display','');

		$('label[for=use_delivery_div_tekbae]').css('display','');
		$('label[for=use_delivery_div_quick]').css('display','');
		$('label[for=use_delivery_div_self]').css('display','');

		display_allnone();
		if($('input[name=use_delivery_div_tekbae]').attr('checked') == 'checked'){	//기존 체크박스 잇을시 사용햇던 부분 잠시 주석후 hidden 으로 처리
			//$('#takbae_use').css('display','');
		}

		if($('input[name=use_delivery_div_tekbae]').val() == '1'){
			$('#takbae_use').css('display','');
		}

		if($('input[name=use_delivery_div_quick]').attr('checked') == 'checked'){
			$('#quick_use').css('display','');
		}
		if($('input[name=use_delivery_div_self]').attr('checked') == 'checked'){
			$('#self_use').css('display','');
		}
		
	}else if(delivery_div == '2'){

		dilplay_typeallnone();
		$('input[name=use_delivery_div_truck]').css('display','');
		$('input[name=use_delivery_div_self]').css('display','');

		$('label[for=use_delivery_div_truck]').css('display','');
		$('label[for=use_delivery_div_self]').css('display','');

		display_allnone();
		if($('input[name=use_delivery_div_tekbae]').attr('checked') == 'checked'){
			$('#truck_use').css('display','');
		}
		if($('input[name=use_delivery_div_self]').attr('checked') == 'checked'){
			$('#self_use').css('display','');
		}
	}else if(delivery_div == '3'){
		dilplay_typeallnone();
		$('input[name=use_delivery_div_direct]').css('display','');
		$('input[name=use_delivery_div_self]').css('display','');

		$('label[for=use_delivery_div_direct]').css('display','');
		$('label[for=use_delivery_div_self]').css('display','');

		display_allnone();
		if($('input[name=use_delivery_div_direct]').attr('checked') == 'checked'){
			$('#direct_use').css('display','');
		}
		if($('input[name=use_delivery_div_self]').attr('checked') == 'checked'){
			$('#self_use').css('display','');
		}
	}else if(delivery_div == '4'){
		dilplay_typeallnone();
		display_allnone();
	}
}

function display_allnone(){
	$('#takbae_use').css('display','none');
	$('#quick_use').css('display','none');
	$('#self_use').css('display','none');
	$('#truck_use').css('display','none');
	$('#direct_use').css('display','none');
}

function dilplay_typeallnone(){
	$('input[name=use_delivery_div_tekbae]').css('display','none');
	$('input[name=use_delivery_div_quick]').css('display','none');
	$('input[name=use_delivery_div_truck]').css('display','none');
	$('input[name=use_delivery_div_direct]').css('display','none');
	$('input[name=use_delivery_div_self]').css('display','none');

	$('label[for=use_delivery_div_tekbae]').css('display','none');
	$('label[for=use_delivery_div_quick]').css('display','none');
	$('label[for=use_delivery_div_truck]').css('display','none');
	$('label[for=use_delivery_div_direct]').css('display','none');
	$('label[for=use_delivery_div_self]').css('display','none');
}

function diplay_delivery_div(target_id,this_name){
	if($('input[name='+this_name+']').attr('checked') == 'checked'){
		$('#'+target_id).css('display','');
	}else{
		$('#'+target_id).css('display','none');
	}
}

function change_delivery_package(delivery_policy){
	disbale_delivery_package();
	$('#delivery_package_'+delivery_policy).attr('disabled',false);
}

function disbale_delivery_package(){
	return false;
	$('select[name^=delivery_package]').attr('disabled',true);
	$('input[name=delivery_package]').attr('disabled',true);
}

function AddCopyRow(target_id, option_var_name, seq){
	
	var table_target_obj = $('table[id='+target_id+']');
	var option_obj = $('#'+target_id);//target_obj;//target_obj.parent().parent().parent().parent().parent().parent().parent().parent();
	
	/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
	var option_length = 0;
	table_target_obj.find('tr:last').each(function(){
		 option_length = $(this).find('#option_length').val();
	});
	rows_total = parseInt(option_length) + 1;
	/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */

	var newRow = option_obj.find('tr:first').clone(true).wrapAll('<table/>').appendTo('#'+option_obj.attr('id'));  //

	newRow.find('input[id=delivery_price]').val('');
	newRow.find('input[id=delivery_basic_terms]').val('');
	newRow.find('input[id=option_length]').val(rows_total);

	newRow.find('input[id=delivery_price]').attr('name',option_var_name+'['+rows_total+'][delivery_price]');
	newRow.find('input[id=delivery_basic_terms]').attr('name',option_var_name+'['+rows_total+'][delivery_basic_terms]');

}

$(document).ready(function(){
	
	CKEDITOR.replace('delivery_policy_text',{
		startupFocus : false,height:150
	});

	CKEDITOR.replace('delivery_policy_text_m',{
		startupFocus : false,height:150
	});

	$('#delivery_price_del').live('click',function() {
		if($('#delivery_policy_3_terms tr').size() > 1) $(this).parents('#add_table_price').remove();
	});

	$('#delivery_cnt_del').live('click',function() {
		if($('#delivery_policy_4_terms tr').size() > 1) $(this).parents('#add_table_cnt').remove();
	});

	$('#delivery_w_del').live('click',function() {
		if($('#delivery_policy_7_terms tr').size() > 1) $(this).parents('#add_table_cnt').remove();
	});

	var delivery_div = $('input[name^=delivery_div]:checked').val();
	change_delivery_div (delivery_div);
	
	$('input[name=delivery_div]').click(function (){
		var value = $(this).val();
		change_delivery_div (value);
	});

	$('input[name=use_delivery_div_tekbae]').click(function(){
		diplay_delivery_div('takbae_use','use_delivery_div_tekbae');
	});

	$('input[name=use_delivery_div_quick]').click(function(){
		diplay_delivery_div('quick_use','use_delivery_div_quick');
	});

	$('input[name=use_delivery_div_self]').click(function(){
		diplay_delivery_div('self_use','use_delivery_div_self');
	});

	$('input[name=use_delivery_div_truck]').click(function(){
		diplay_delivery_div('truck_use','use_delivery_div_truck');
	});

	$('input[name=use_delivery_div_direct]').click(function(){
		diplay_delivery_div('direct_use','use_delivery_div_direct');
	});

	var delivery_policy = '".$db->dt[delivery_policy]."';
	change_delivery_package(delivery_policy);

	$('input[name^=delivery_policy]').click(function (){
		var value = $(this).val();
		change_delivery_package(value);
	});
	//delivery_cnt_del

	
	//착불명칭 관련 스크립트 시작 2014-09-12 이학봉
	$('input[name=delivery_basic_policy]').click(function (){
		var value = $(this).val();
		if(value != '1'){
			$('#pay_method_text').css('display','');
		}else{
			$('#pay_method_text').css('display','none');
		}
	});

	var delivery_basic_policy = $('input[name=delivery_basic_policy]:checked').val();
	if(delivery_basic_policy != '1'){
		$('#pay_method_text').css('display','');
	}else{
		$('#pay_method_text').css('display','none');
	}
	//착불명칭 관련 스크립트 끝 2014-09-12 이학봉



});

function showDeliveryText(type){
	if(type == 1){
		$('#delivery_text_area').show();
		$('#delivery_text_area_m').hide();
		$('#tab_01').addClass('on');
		$('#tab_02').removeClass('on');
	}else{
		$('#delivery_text_area').hide();
		$('#delivery_text_area_m').show();
		$('#tab_01').removeClass('on');
		$('#tab_02').addClass('on');
	}
}
</script>
";

if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='brand.js'></script>\n".$Script;
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	//$P->OnloadFunction = "Init(document.edit_form);";
	$P->Navigation = "상품관리 > 개별상품등록 > 상품별 배송 정책 설정";
	$P->NaviTitle = "상품별 배송 정책 설정";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents01;
	echo $P->PrintLayOut();
}else{
	$Script = "<script language='JavaScript' src='brand.js'></script>\n".$Script;
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->Navigation = "상품관리 > 개별상품등록 > 상품별 배송 정책 설정";
	$P->title = "상품별 배송 정책 설정";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents01;
	echo $P->PrintLayOut();

}


?>