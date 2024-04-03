<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] < 9){
	header("Location:/admin/store/company.add.php");
}

$shmop = new Shared("b2c_coupon_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$coupon_data = $shmop->getObjectForKey("b2c_coupon_rule");
$coupon_data = unserialize(urldecode($coupon_data));
//print_r($coupon_data);

$db = new Database;
$db2 = new Database;

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' valign=top>
	<tr>
		<td align='left'> ".GetTitleNavigation("쿠폰 정책", "상점관리 > 쿠폰 정책")."</td>
	</tr>
	<tr>
		<td align='left' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b>기본 설정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' class='input_table_box'>
	<col width=18%>
	<col width=*>
	
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰 사용 여부 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='coupon_use_yn' value='Y' id='coupon_use_yn_y'  ".($coupon_data[coupon_use_yn] == "Y" ? "checked":"")."><label for='coupon_use_yn_y' >사용</label>
			<input type='radio' name='coupon_use_yn' value='N' id='coupon_use_yn_n'  ".($coupon_data[coupon_use_yn] =="N" ? "checked":"")."><label for='coupon_use_yn_n' >사용안함</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰 종류 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='checkbox' name='default_coupon_kind[]' value='G' id='default_g' validation='true' title='쿠폰 종류' ".(in_array("G", $coupon_data[default_coupon_kind]) ? "checked":"")."><label for='default_g' >상품 쿠폰</label>
			<input type='checkbox' name='default_coupon_kind[]' value='C' id='default_c' validation='true' title='쿠폰 종류' ".(in_array("C", $coupon_data[default_coupon_kind]) ? "checked":"")."><label for='default_c' >장바구니 쿠폰</label>
            <input type='checkbox' name='default_coupon_kind[]' value='D' id='default_d' validation='true' title='쿠폰 종류' ".(in_array("D", $coupon_data[default_coupon_kind]) ? "checked":"")."><label for='default_d' >배송비 쿠폰</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰 적용기준 금액 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='cp_standard_price' value='D' id='cp_standard_price_d' ".($coupon_data[cp_standard_price] == "D" ? "checked":"checked")."><label for='cp_standard_price_d' >할인가(기획, 특별할인 반영가격)</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰복원 설정(자동) <img src='".$required3_path."'></b></td>
		<td class='input_box_item' nowrap>
			<table cellpadding=0 cellspacing=0 class='input_table_box' style='margin: 5px;'>
				<tr bgcolor='#ffffff'>
					<td class='input_box_title' style='padding-right: 15px;'>취소 (결제 전)</td>
					<td class='input_box_item'>
						<input type='radio' name='restore_cc1' value='N' id='restore_cc1_y' ".($coupon_data[restore_cc1] =="N" ? "checked":"")."><label for='restore_cc1_y' >복원 안함</label>
						<input type='radio' name='restore_cc1' value='Y' id='restore_cc1_n' ".($coupon_data[restore_cc1] =="Y" ? "checked":"")."><label for='restore_cc1_n' style='padding-right: 15px;'>복원함 (취소완료시) </label>
					</td>
				</tr>
				<tr bgcolor='#ffffff'>
					<td class='input_box_title' style='padding-right: 15px;'>취소 (결제 후)</td>
					<td class='input_box_item'>
						<input type='radio' name='restore_cc2' value='N' id='restore_cc2_y' ".($coupon_data[restore_cc2] =="N" ? "checked":"")."><label for='restore_cc2_y' >복원 안함</label>
						<input type='radio' name='restore_cc2' value='Y' id='restore_cc2_n' ".($coupon_data[restore_cc2] =="Y" ? "checked":"")."><label for='restore_cc2_n' style='padding-right: 15px;'>복원함 (취소완료시) </label>
					</td>
				</tr>
				<tr bgcolor='#ffffff'>
					<td class='input_box_title' style='padding-right: 15px;'>반품</td>
					<td class='input_box_item'>
						<input type='radio' name='restore_bf' value='N' id='restore_bf_y' ".($coupon_data[restore_bf] =="N" ? "checked":"")."><label for='restore_bf_y' >복원 안함</label>
						<input type='radio' name='restore_bf' value='Y' id='restore_bf_n' ".($coupon_data[restore_bf] =="Y" ? "checked":"")."><label for='restore_bf_n' style='padding-right: 15px;'>복원함 (반품확정시) </label>
					</td>
				</tr>
			</table>
			</br>
			<div>
				<img src='../image/emo_3_15.gif' align=absmiddle ><b>쿠폰 자동 복원 설정 가이드</b></br>
				-쿠폰 종류 및 유형에 상관없이 모든 쿠폰에 동일하게 적용됩니다.</br>
				-자동 복원 시, 처음 설정된 쿠폰의 설정 값 그대로 복원됩니다. </br>
				-자동 복원 시점에 사용기간이 만료되었거나, 적용 가능한 상품/이벤트 등이 종료되었을 수 있습니다. </br>
			</div>
			</br>
		</td>
	</tr>
	<!--
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰 기본 이미지 (협의중)<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='' value='Y' id='goods_coupon_use_yn_y'><label for='goods_coupon_use_yn_y' >기본 이미지</label>
			<input type='radio' name='' value='N' id='goods_coupon_use_yn_n'><label for='goods_coupon_use_yn_n' >커스텀 이미지 (직접 등록)</label>
			</br></br>
			<b>* 업로드 이미지 가이드 [ 파일명: 영문 / 파일 형식 : png, gif, jpeg / 권장 사이즈 : N px * N px  / 파일 용량 : 최대 N  MB ]</b>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>다운로드 이미지 (협의중)<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='' value='Y' id='goods_coupon_use_yn_y'><label for='goods_coupon_use_yn_y' >기본 이미지</label>
			<input type='radio' name='' value='N' id='goods_coupon_use_yn_n'><label for='goods_coupon_use_yn_n' >커스텀 이미지 (직접 등록)</label>
			</br></br>
			<b>* 업로드 이미지 가이드 [ 파일명: 영문 / 파일 형식 : png, gif, jpeg / 권장 사이즈 : N px * N px  / 파일 용량 : 최대 N  MB ]</b>
		</td>
	</tr>
	-->
	</table><br><br>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff >
	<td colspan=4 align=center style='padding:10px 0px;'>
	<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' >
	</td>
</tr>
</table>";
}


$Contents = "<form name='edit_form' action='coupon_rule.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>";
$Contents = $Contents. "<table width='100%'  border=0>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

  $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .=  HelpBox("쿠폰 정책 관리", $help_text, 100);

//$Contents = "<div style=height:1000px;'></div>";
$Script = "
<script type='text/javascript'>
<!--
	function insertInputBox(obj){

		var objs=$('table.'+obj).find('tr');

		if(objs.length > 0 ){
			var obj_table = objs[0].cloneNode(true);
			var target_obj = objs[objs.length-1];	
		}else{
			
			var obj_table = objs[0].cloneNode(true);
			var target_obj = objs[0];
		}
		var newRow = objs.clone(true).appendTo($('#goods_coupon_table_add'));
	}

	function AddCopyRow(target_id, option_var_name, seq){
	
		var table_target_obj = $('table[id='+target_id+']');
		var option_obj = $('#'+target_id);
		
		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
		var option_length = 0;
		table_target_obj.find('tr:last').each(function(){
			 option_length = $(this).find('#option_length').val();
		});
		rows_total = parseInt(option_length) + 1;
		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */

		var newRow = option_obj.find('tr:first').clone(true).wrapAll('<table/>').appendTo('#'+option_obj.attr('id'));  //

		newRow.find('input[id=option_length]').val(rows_total);
		newRow.find('select[id=member_publish_ix]').attr('name',option_var_name+'['+rows_total+'][publish_ix]');

	}
	/*[Start] 모바일용 2015-01-20  */
	function AddCopyRowMobile(target_id, option_var_name, seq){
	
		var table_target_obj = $('table[id='+target_id+']');
		var option_obj = $('#'+target_id);
		
		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
		var option_length_mobile = 0;
		table_target_obj.find('tr:last').each(function(){
			 option_length_mobile = $(this).find('#option_length_mobile').val();
		});
		rows_total = parseInt(option_length_mobile) + 1;

		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */

		var newRow = option_obj.find('tr:first').clone(true).wrapAll('<table/>').appendTo('#'+option_obj.attr('id'));  //

		newRow.find('input[id=option_length_mobile]').val(rows_total);
		newRow.find('select[id=mobile_member_publish_ix]').attr('name',option_var_name+'['+rows_total+'][publish_ix]');

	}
	/*[End] 2015-01-20  */
	
	/*[Start] app다운 2015-11-05 문정길 */
	function AddCopyRowAppDown(target_id, option_var_name, seq){
	
		var table_target_obj = $('table[id='+target_id+']');
		var option_obj = $('#'+target_id);
		
		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
		var app_down_length = 0;
		table_target_obj.find('tr:last').each(function(){
			 app_down_length = $(this).find('#app_down_length').val();
		});
		rows_total = parseInt(app_down_length) + 1;

		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */

		var newRow = option_obj.find('tr:first').clone(true).wrapAll('<table/>').appendTo('#'+option_obj.attr('id'));  //

		newRow.find('input[id=app_down_length]').val(rows_total);
		newRow.find('select[id=app_down_ix]').attr('name',option_var_name+'['+rows_total+'][publish_ix]');

	}
	/*[End] 2015-11-05 문정길 */

	/*[Start] app 첫구매 2015-11-05 문정길 */
	function AddCopyRowAppOrder(target_id, option_var_name, seq){
	
		var table_target_obj = $('table[id='+target_id+']');
		var option_obj = $('#'+target_id);
		
		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */
		var app_order_length = 0;
		table_target_obj.find('tr:last').each(function(){
			 app_order_length = $(this).find('#app_order_length').val();
		});
		rows_total = parseInt(app_order_length) + 1;

		/*배열값이 중복땜에 제일마지막 tr의 value 값을 가져온다 2014-02-07 이학봉 */

		var newRow = option_obj.find('tr:first').clone(true).wrapAll('<table/>').appendTo('#'+option_obj.attr('id'));  //

		newRow.find('input[id=app_order_length]').val(rows_total);
		newRow.find('select[id=app_order_ix]').attr('name',option_var_name+'['+rows_total+'][publish_ix]');

	}
	/*[End] 2015-11-05 문정길 */

	$(document).ready(function(){
		
		$('#delivery_price_del').live('click',function() {
			if($('#delivery_policy_3_terms tr').size() > 1) $(this).parents('#add_table_price').remove();
		});

		/*[Start] 2015-01-20  */
		$('#delivery_price_del_m').live('click',function() {
			if($('#delivery_policy_4_terms tr').size() > 1) $(this).parents('#add_table_price_m').remove();
		});
		/*[End] 2015-01-20  */

		$('#delivery_price_del_app').live('click',function() {
			if($('#delivery_policy_5_terms tr').size() > 1) $(this).parents('#add_table_price_app').remove();
		});

		$('#delivery_price_del_app_order').live('click',function() {
			if($('#delivery_policy_6_terms tr').size() > 1) $(this).parents('#add_app_order').remove();
		});
		

	});
//-->
</script>";

$Script = "<script language='javascript' src='basicinfo.js'></script>\n$Script";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = promotion_menu();
$P->Navigation = "프로모션(마케팅)(마케팅) > 쿠폰관리 > 쿠폰 기본설정";
$P->title = "쿠폰 기본설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>