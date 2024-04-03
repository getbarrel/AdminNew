<?
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."../include/admin.util.php");
//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database();

$db->query("Select * from ".TBL_SHOP_CUPON." where cupon_ix ='$cupon_ix'");
$db->fetch();
//$total = $db->total;
if($db->total){
	$mall_ix = $db->dt[mall_ix];
	$cupon_ix = $db->dt[cupon_ix];
	$cupon_kind = $db->dt[cupon_kind];
	$cupon_sale_value = $db->dt[cupon_sale_value];
	$cupon_sale_type = $db->dt[cupon_sale_type];
	$cupon_div = $db->dt[cupon_div]; 
	$cupon_use_div = $db->dt[cupon_use_div]; 
	$haddoffice_rate = $db->dt[haddoffice_rate]; 
	$seller_rate = $db->dt[seller_rate]; 
	$round_position = $db->dt[round_position]; 
	$round_type = $db->dt[round_type]; 
	 
	$is_use = $db->dt[is_use]; 
	$disp = $db->dt[disp];

    if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/cupon/cupon_".$db->dt[cupon_ix].".gif")){
		$cupon_img = "<img src='".$admin_config[mall_data_root]."/images/cupon/cupon_".$db->dt[cupon_ix].".gif' border='1' style='border-color: #A5A5A5' name='preview_cupon'  id='preview_cupon' width='350' height='230' align='absmiddle'>";
	}else{
		$cupon_img = "<img src='../image/0.gif'  style='border:1px solid silver' name='preview_cupon' id='preview_cupon' width='350' height='230' align='absmiddle'>";
	}

	$act = "update";
}else{
	$cupon_ix = "";
	$cupon_kind = "";
	$cupon_sale_value = "";
	$cupon_sale_type = "1";
	$cupon_div = "G"; 
	$cupon_use_div = "G"; 
	//$haddoffice_rate = 100; 
	//$seller_rate = 0;
	$round_position = 1; 
	$round_type = 1; 
	$is_use = 1; 
	$disp = 1; 

	$cupon_img = "<img src='../image/0.gif'  style='border:1px solid silver' name='preview_cupon' id='preview_cupon' width='350' height='230' align='absmiddle'>";
	$act = "insert";
}

$Contents = "
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <!-- // 쿠폰등록 -->
  <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("쿠폰 생성 / 수정", "전시관리 > 쿠폰 생성/수정 ")."</td>
  </tr>
  <!--tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='cupon_publish.php'\" >쿠폰발행</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='cupon_regist_modify.php'\">쿠폰생성</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='cupon_regist_list.php'\">쿠폰목록</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td style='width:600px;text-align:right;vertical-align:bottom;padding:0 0 10 0'>

						</td>
					</tr>
					</table>
				</div>
	    </td>
	</tr-->
  <tr>
    <td height='2'></td>
  </tr>
  <tr>
    <td height='10'></td>
  </tr>
  <tr>
    <td height='10'>쿠폰 생성/수정</font>&nbsp;(쿠폰종류, 쿠폰이미지 확인)</td>
  </tr>
  <tr>
    <td height='10'></td>
  </tr>
  <tr>
    <td valign='top'>
	<form name='form_cupon' onsubmit='return CheckFormValue(this)' method='post' enctype='multipart/form-data'  action='cupon.act.php'>
	<input type=hidden name='act' value='".$act."'>
	<input type=hidden name='cupon_ix' value='".$cupon_ix."'>
      <table width='100%' border='0' cellpadding='0' cellspacing='0' class='input_table_box'>
		<col width='15%'>
		<col width='35%'>
		<col width='15%'>
		<col width='35%'>";
		if($_SESSION["admin_config"][front_multiview] == "Y"){
		$Contents .= "
		<tr>
			<td class='input_box_title' > 프론트 전시 구분</td>
			<td class='input_box_item' style='padding-left:10px;' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
		</tr>";
		}
		$Contents .= "
        <tr height=30>
          <td class='input_box_title'  > 쿠폰명</td>
          <td class='input_box_item' style='padding-left:10px;' colspan=3>
		  <input type='text' validation='true' title='쿠폰명' id='fcupon_type' name='cupon_kind' class='textbox' maxlength='50' style='height: 20px; width: 300px; filter: blendTrans(duration=0.5)' align='absmiddle' value='".$cupon_kind."'></td>
        </tr>
		<tr height=30>
          <td class='input_box_title'  > 쿠폰종류</td>
          <td class='input_box_item'  style='padding-left:10px;' colspan=3>";
		  foreach($_COUPON_KIND as $key => $value){
			$Contents .= "<input type='radio' name='cupon_div' id='cupon_div_".$key."' value='".$key."' ".CompareReturnValue($key,$cupon_div,"checked")." validation=true title='쿠폰종류' ".($act == "update" ? ($key!=$cupon_div ? "disabled" :""):"")."> <label for='cupon_div_".$key."' >".$value."</label> ";
		  }
		  $Contents .= "
			 <!--input type='radio' name='cupon_div' id='cupon_div_g' value='G' ".CompareReturnValue("G",$cupon_div,"checked")." validation=true title='쿠폰종류'> <label for='cupon_div_g' >상품쿠폰</label> 
			 <input type='radio' name='cupon_div' id='cupon_div_d' value='D' ".CompareReturnValue("D",$cupon_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_div_d' >배송비쿠폰</label>
			 <input type='radio' name='cupon_div' id='cupon_div_r' value='R' ".CompareReturnValue("R",$cupon_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_div_r' >적립금쿠폰</label>
			 <input type='radio' name='cupon_div' id='cupon_div_p' value='P' ".CompareReturnValue("P",$cupon_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_div_p' >포인트쿠폰</label-->
          </td>
        </tr>
        <tr height=30>
          <td class='input_box_title'  > 쿠폰적용가격</td>
          <td class='input_box_item'  style='padding:10px;' colspan=3>
				
				<div>
                    <input type=radio id='cupon_sale_type1' name='cupon_sale_type' value=1 ".CompareReturnValue('1', $cupon_sale_type, ' checked')." ".($act == "update" ? ("1"!=$cupon_sale_type ? "disabled" :""):"")."  onclick=\" $(this).closest('td').find('div[id^=round_type_area_]').css('display','inline')\"><label for='cupon_sale_type1'>할인율(%)</label> 
                    <input type=radio id='cupon_sale_type2' name='cupon_sale_type' value=2 ".CompareReturnValue('2', $cupon_sale_type, ' checked')." ".($act == "update" ? ("2"!=$cupon_sale_type ? "disabled" :""):"")."  onclick=\" $(this).closest('td').find('div[id^=round_type_area_]').css('display','none')\"><label for='cupon_sale_type2'>금액할인(원)</label>
                </div>
        		
				<div style='margin-top:10px;'>
                    본사부담 : <input type=text class='textbox numeric' validation='true' title='본사부담' name='haddoffice_rate' id='haddoffice_rate' maxlength='10' style='width: 70px; filter: blendTrans(duration=0.5)'  align='absmiddle' value='".$haddoffice_rate."' ".($act == "update" ? "readonly onclick=\"alert('수정하실 수 없는 항목입니다.');\"":"")." onkeyup=\"checkCouponPriceRate($(this));\">
                    + 셀러부담 : <input type=text class='textbox numeric' validation='true' title='셀러부담' name='seller_rate' id='seller_rate' maxlength='10' style='width: 70px; filter: blendTrans(duration=0.5)'  align='absmiddle' value='".$seller_rate."' ".($act == "update" ? "readonly onclick=\"alert('수정하실 수 없는 항목입니다.');\"":"")." onkeyup=\"checkCouponPriceRate($(this));\">
                    = 합계 :
                    <input type=text class='textbox numeric point_color' validation='true' title='쿠폰적용가격 합계' name='cupon_sale_value' id='cupon_sale_value' maxlength='20' style='width: 70px; filter: blendTrans(duration=0.5)'  align='absmiddle' value='".$cupon_sale_value."' ".($act == "update" ? "readonly onclick=\"alert('수정하실 수 없는 항목입니다.');\"":"")." readonly /> 
                    
                    <div id='round_type_area_".($i+1)."'  ".($cupon_sale_type == "1" || $cupon_sale_type == "" ? "style='display:inline;'":"style='display:none;'").">
                    <select name='round_position' id='round_position' > 
                        <option value='1' ".($round_position == 1 ? "selected":"").">일 자리</option>
                        <option value='2' ".($round_position == 2 ? "selected":"").">십 자리</option>
                        <option value='3' ".($round_position == 3 ? "selected":"").">백 자리</option>
                    </select>
                    <select name='round_type' id='round_type'  > 
                        <option value='1' ".($round_type == 1 ? "selected":"").">반올림</option>
                        <!--option value='2' ".($round_type == 2 ? "selected":"").">반내림</option-->
                        <option value='3' ".($round_type == 3 ? "selected":"").">내림</option>
                        <option value='4' ".($round_type == 4 ? "selected":"").">올림</option>
                    </select>
                    </div>
				</div>
				<div id='cart_coupon_text' style='display:none;margin-top:10px;color:red;'>
                    ※ 장바구니 쿠폰으로 발생한 할인부담금은 모두 본사가 부담합니다.<br/>
                    ※ 장바구니 쿠폰은 본사만 발급 가능합니다.
                </div>
				<div id='delivery_coupon_text' style='display:none;margin-top:10px;color:red;'>
                    ※ 배송비 쿠폰으로 발생한 할인부담금은 모두 본사가 부담합니다.<br/>
                    ※ 배송비 쿠폰은 본사만 발급 가능합니다.<br/>
                    ※ 배송비 쿠폰은 할인율 발급은 불가능 합니다.
                </div>
                <div id='cupon_sale_value_validation_text'></div>
          </td>
        </tr>
		<tr >
		  <td class='input_box_title' >  <b>쿠폰사용 구분</b></td>
		  <td class='input_box_item' colspan=3>";
		  foreach($_COUPON_USE_DIV as $key => $value){
			$Contents .= "<input type='radio' name='cupon_use_div' id='cupon_use_div_".$key."' value='".$key."' ".CompareReturnValue($key,$cupon_use_div,"checked")." validation=true title='쿠폰사용' ".($act == "update" ? ($key!=$cupon_use_div ? "disabled" :""):"")."> <label for='cupon_use_div_".$key."' >".$value."</label> ";
		  }
		  $Contents .= "
			 <!--input type='radio' name='cupon_use_div' id='cupon_use_div_g' value='G' ".CompareReturnValue("G",$cupon_use_div,"checked")." validation=true title='쿠폰종류'> <label for='cupon_use_div_G' >일반쿠폰</label> 
			 <input type='radio' name='cupon_use_div' id='cupon_use_div_d' value='D' ".CompareReturnValue("D",$cupon_use_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_use_div_d' >중복쿠폰</label>
			 <input type='radio' name='cupon_use_div' id='cupon_use_div_d' value='C' ".CompareReturnValue("R",$cupon_use_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_use_div_r' >C/S쿠폰</label>
			 <input type='radio' name='cupon_use_div' id='cupon_use_div_p' value='M' ".CompareReturnValue("P",$cupon_use_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_use_div_p' >모바일쿠폰</label>
			 <input type='radio' name='cupon_use_div' id='cupon_use_div_p' value='P' ".CompareReturnValue("P",$cupon_use_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_use_div_p' >회원패키지쿠폰</label-->
		  </td>
		  <!--td class='search_box_title' >  <b>C/S 쿠폰여부</b></td>
		  <td class='search_box_item'>
				<input type='checkbox' name='is_cs' id='is_cs_1'  align='middle' value='1' ".($is_cs == '1' ? "checked":"")."><label for='is_cs_1' class='green'>CS쿠폰</label>
		  </td-->
		</tr>
		<tr >
		  <td class='input_box_title' >  <b>사용여부</b></td>
		  <td class='input_box_item'>
				<input type='radio' name='is_use' id='is_use_1'  align='middle' value='1' ".($is_use == '1' || $is_use == '' ? "checked":"")."><label for='is_use_1' class='green'>사용함</label> 
				<input type='radio' name='is_use' id='is_use_0'  align='middle' value='0' ".($is_use == '0' ? "checked":"")."><label for='is_use_0' class='green'>미사용</label> 
		  </td>
		  <td class='input_box_title' >  <b>노출여부</b></td>
		  <td class='input_box_item'>
				<input type='radio' name='disp' id='disp_1'  align='middle' value='1' ".($disp == '1' || $disp == '' ? "checked":"")."><label for='disp_1' class='green'>노출함</label> 
				<input type='radio' name='disp' id='disp_0'  align='middle' value='0' ".($disp == '0' ? "checked":"")."><label for='disp_0' class='green'>노출안함</label> 
		  </td> 
		</tr>
        <tr  height=300>
          <td class='input_box_title' > 쿠폰이미지</td>
          <td class='input_box_item'  style='padding-left:10px;' colspan=3>".$cupon_img."<br><img src='../image/0.gif' width='1' height='10'><br><input type='file' validation='false' title='쿠폰이미지' id='fcupon_img' name='cupon_img' class='textbox' style='filter: blendTrans(duration=0.5); height: 20px; width: 442px' align='absmiddle'>
          <img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' align='absmiddle' style='cursor:pointer;display:none;' id='coupon_file_cancel_img'/>
		  </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height='20'></td>
  </tr>
  <tr>
    <td align='center'>";
if($_GET["cupon_ix"] == ""){
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>";
	}else{
		$Contents .= "<a href=\"".$auth_write_msg."\"><img  src='../images/".$admininfo["language"]."/b_save.gif' border=0></a>";
	}
}else{
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>";
	}else{
		$Contents .= "<a href=\"".$auth_update_msg."\"><img  src='../images/".$admininfo["language"]."/b_save.gif' border=0></a>";
	}
}
$Contents .= "
	</td>
  </tr></form>
</table>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='cupon.js'></script>";
$P->strLeftMenu = promotion_menu();
$P->strContents = $Contents;
$P->Navigation = "프로모션(마케팅)/전시관리 > 쿠폰관리 > 쿠폰등록";
$P->title = "쿠폰등록";
echo $P->PrintLayOut();

?>