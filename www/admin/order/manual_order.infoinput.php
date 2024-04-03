<?
include("../class/layout.class");

if(!checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
    echo "<script>alert('해당메뉴에 대한 접근 권한이 없습니다');history.back();</script>";
}

$db = new Database;
$db1 = new Database;
$db2 = new Database;
$db3 = new Database;
$odb = new Database;
$tdb = new Database;


$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("주문서 작성하기", "주문관리 > 주문서 작성하기 ")."</td>
</tr>
<tr >
	<td width='100%' valign=top style='padding-top:3px;'>

	<table cellpadding=0 cellspacing=0 width=100% align=center class='list_table_box'>

				<col width='*' >
				<col width=8% >
				<col width=10% >
				<col width=10% >
				<col width=10% >
				<col width=7% >
				<col width=10% >
				<col width=10% >
				<tr align=center height=30 style='font-weight:bold;'>
					<td class=s_td>제 품 명</td>
					<td class=m_td>수량</td>
					<td class=m_td>정가</td>
					<td class=m_td>판 매 가</td>
					<td class=m_td>공급가</td>
					<td class=m_td>합계</td>
					<td class=m_td nowrap>공급율</td>
					<td class=e_td>취소</td>
				</tr>";
if($ESTIMATE_INTRA){

	for ( reset($ESTIMATE_INTRA); $key = key($ESTIMATE_INTRA); next($ESTIMATE_INTRA) ){

			$value = pos($ESTIMATE_INTRA);
			$pid = $value[id];
			$pname = $value[pname];
			$pname_str .= $value[pname];
			$pcount    = $value[pcount];
			$options    = $value[options];
			$option_serial    = $value[option_serial];
			$coprice = $value[coprice];
			$sellprice = $value[sellprice];
			$totalprice = $value[totalprice];
			$cart_totalprice = $cart_totalprice + $totalprice;
			$coper = $coprice / $sellprice * 100;

			$db->query("SELECT listprice, sellprice,  admin,company, state FROM ".TBL_SHOP_PRODUCT." WHERE id='$pid'");
			$db->fetch();


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $pid, "s"))) {
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $pid, "s");
			}else{
				$img_str = "../image/no_img.gif";
			}

$Contents .="
				<tr height=55>
					<td height='55' style='padding:5px;'>
					<table>
					<tr>
						<td><a href='goods_view.php?id=$pid'><img src='$img_str' border=0 width=50 align=left></a></td>
						<td>
						$pname ".($db->dt[state] == "0" ? "<img src ='/data/sigong/templet/basic/sigong_img/btn_soldout.jpg' align='absmiddle'>" :"")."
						";
					for($o=0; $o<count($options); $o++){
					$Contents .= getOptionName($options[$o]);
					}
$Contents .="
						</td>
					</tr>
					</table>


					</td>
					<td height='55' nowrap>
						<div align='center'><input type=text class='textbox' name=quantity id='quantity_".$option_serial."' value='$pcount' size=5 class=input2 style='text-align:right;padding:0 5px 0 0' >  개</div>
					</td>
					<td height='55' align=center>".number_format($db->dt[listprice])."</td>
					<td height='55' align=center><input type=text class='textbox' name='sellprice' id='sellprice_".$option_serial."' value='$sellprice' size=5 class=input2 style='text-align:right;padding:0 5px 0 0;' >

					</td>
					<td height='55' align=center>".number_format($coprice)."</td>
					<td height='55' align=center>".number_format($totalprice)."</td>
					<td height='55' align=center>".number_format($coper)."%</td>
					<td height='55' align=center>
					<A href=\"javascript:num_apply('".$option_serial."','".$pid."');\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' align=absmiddle border=0></a>
					<a href='manual_order.cart.php?act=del&option_serial=".$option_serial."'><img src='../images/".$admininfo["language"]."/btn_del.gif' border='0' align=absmiddle></a>
					</td>
				</tr>";
	}
}else{
$Contents .="
				<tr height=50><td colspan=8 align=center>견적상품 내역이  존재 하지 않습니다.</td></tr>
				";

}


$Contents .="
				<tr bgcolor=#ffffff height=35 >
					<td align='center' class=s_td><b><font color='#333333'>총합계</font></b></td>
					<td colspan='5' class=m_td></td>
					<td align=center class=m_td><b> <font color='FF4E00'>".number_format($cart_totalprice)." </font></b><font color='FF4E00'> 원</font></td>
					<td class=e_td></td>
				</tr>
				</table>
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
				<tr>
					<td colspan=4 align=left>
						<table cellpadding=5>
							<tr height=40>
								<td><b><a href='manual_order.cart.php'>이전</a></b></td>
							</tr>
						</table>
					</td>
					<td colspan=4>
						<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
							<tr>
								<td align='right'>상품금액  : <span class='red'>".number_format($cart_totalprice)."원</span>+  배송비 : <span class='red'>".number_format($total_delivery_price)."원</span> = 총 주문금액 : <span class='red'>".number_format($cart_totalprice + $total_delivery_price)."원</span></td>
							</tr>
						</table><!--f:buttonSection-->
					</td>
				</tr>
			</table><br><br>
";


$Contents .= "

	";

	$est_delivery_zip = explode("-", $db1->dt[est_delivery_zip]);
	$est_tel = explode("-", $db1->dt[est_tel]);
	$est_mobile = explode("-", $db1->dt[est_mobile]);
$Contents .= "
<form  name='order_form' method='post' onsubmit='return CheckFormValue(this)' action='./estimate.cart.act.php'>
<input type=hidden name=carttype value='$order_cart_type'>
<input type=hidden name='myreserve_price' value='$total_reserve'>
<input type=hidden name='cart_totalprice' value='$total_cart_price'>
<input type=hidden name='delivery_total_price' value='$total_delivery_price'>
<input type=hidden name='cart_key' value='".$db1->dt[est_id]."'>
<input type='hidden' name='delivery_method' value='TE'>
<input type='hidden' id='code' value=''>
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center height='300px'>
		<col width='50%'>
		<col width='50%'>
		<tr>
			<td valign='top' style='padding:0px;'>
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0' class='input_table_box'><!---mbox04-->
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'></th>
						<td class='box_05 align=center' style='padding:10px 15px 10px 0px;' valign='top'>
							<table width=100% cellpadding=0 cellspacing=0 >
								<tr height=25>
								<td>
										<img src='../images/dot_org.gif' align=absmiddle> <b>주문자 정보</b>
								</td>
								<td align=right>
										<img src='../images/".$admininfo["language"]."/bts_search_id.gif' align=absmiddle border=0 onclick='idsearch();' style='cursor:pointer;'><!--input type='button' value='아이디 검색' onclick='idsearch();'  class='box' --><input type='hidden' name='ucode'>
								</td>
								</tr>
							</table>
							<table width=100% cellpadding=0 cellspacing=0 class='input_table_box'>
								<tr height=30 class='border'>
									<td class='input_box_title' width='100'>이름</td>
									<td class='input_box_item'> <input type='text' name='name_a' class='textbox' id='name_a' size='27' maxlength='20' class='textbox' value='".$db1->dt[est_charger]."' validation='true'  title='이름'  ></td>
								</tr>
								<tr>
									<td class='list input_box_title'>주소</td>
									<td class='input_box_item'style='padding:5px 5px;'><input type='text' name='zipcode1' class='textbox' id='zipcode1'  size='10' maxlength='3' class='textbox' value='$est_delivery_zip[0]' validation='true' title='우편번호' >		-
										<input type='text' name='zipcode2' class='textbox' id='zipcode2'  size='10' maxlength='3' class='textbox' value='$est_delivery_zip[1]' validation='true' title='우편번호' >
										<img src='../images/".$admininfo["language"]."/btn_search_address.gif' align=absmiddle border=0 onClick=\"zipcode('2')\"  style='cursor:pointer;'><!--input type='button' value='주소 찾기'  class='box' onClick=\"zipcode('2')\"  alt='주소 찾기'--><br />
										<input type='text' name='addr1' id='addr1' size='40' maxlength='80' class='textbox' style='margin-top:3px; margin-bottom:3px;' value='".$db1->dt[est_delivery_postion]."' validation='true' title='주소' ><br />
										<input type='text' name='addr2' id='addr2' size='40' maxlength='80' class='textbox' value='".$db1->dt[est_delivery_postion2]."' validation='true' title='세부주소'> 세부주소</td>
								</tr>
								<tr>
									<td class='list input_box_title'>전화번호</td>
									<td class='input_box_item'><input type='text' name='tel1_a' id='tel1_a' size='10' maxlength='3' class='textbox' value='$est_tel[0]' validation='true' title='전화번호' numeric='true'> -
									<input type='text' name='tel2_a' id='tel2_a' size='12' maxlength='4' class='textbox' value='$est_tel[1]' validation='true' title='전화번호' numeric='true'> -
									<input type='text' name='tel3_a' id='tel3_a' size='12' maxlength='4' class='textbox' value='$est_tel[2]' validation='true' title='전화번호' numeric='true'>
									</td>
								</tr>
								<tr>
									<td class='list input_box_title'>핸드폰</td>
									<td class='input_box_item'><input type='text' name='pcs1_a' id='pcs1_a' size='10' maxlength='3' class='textbox' value='$est_mobile[0]' validation='true' title='핸드폰번호' numeric='true'> -
									<input type='text' name='pcs2_a' id='pcs2_a' size='12' maxlength='4' class='textbox' value='$est_mobile[1]' validation='true' title='핸드폰번호' numeric='true'> -
									<input type='text' name='pcs3_a' id='pcs3_a' size='12' maxlength='4' class='textbox' value='$est_mobile[2]' validation='true' title='핸드폰번호' numeric='true'></td>
								</tr>
								<tr>
									<td class='input_box_title'>이메일</td>

									<td class='input_box_item'><input type='text' name='mail_a' id='mail_a' size='45' maxlength='100' class='textbox' value='".$db1->dt[est_email]."' validation='true' title='이메일' email='true'>
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

<script>
    // 입력내용 체크 *************************************************************
	function memEdit(){

		$.post('./infoinput.mem.act.php', {
		  name_a: $('#name_a').val(),
		  zipcode1: $('#zipcode1').val(),
		  zipcode2: $('#zipcode2').val(),
		  addr1: $('#addr1').val(),
		  addr2: $('#addr2').val(),
		  tel1_a: $('#tel1_a').val(),
		  tel2_a: $('#tel2_a').val(),
		  tel3_a: $('#tel3_a').val(),
		  pcs1_a: $('#pcs1_a').val(),
		  pcs2_a: $('#pcs2_a').val(),
		  pcs3_a: $('#pcs3_a').val(),
		  mail_a: $('#mail_a').val(),
		  code: $('#code').val(),
		  act: 'memEdit'
		}, function(data){
			if(data == 'Y') {
				alert('성공적으로 변경이 되었습니다.');

			} else {
				alert('정상적으로 처리가 되지 않았습니다 고객센터에 문의하세요');

				return false;
			}
		});

	}

$(document).ready(function(){
	$('#submit_btn').click(function(){
		input_text();
	});
});


</script>
		<!--toss:주문자 정보 테이블-->
			<td valign='top' style='padding:0px'>
				<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' ><!---mbox04-->
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'></th>
						<td class='box_05 align=center' style='padding:10px 0px 10px 15px' valign='top'>
							<table width='100%' cellpadding=0 cellspacing=0 border=0>
								<tr height=25>
									<td>
									<img src='../images/dot_org.gif' > <b>받는분 정보</b>
									</td>
									<td align=right>
									<input type='radio' name='same' value='1' id='same_1' onClick='isEQ()'><label for='same_1'>주문자 정보와 동일</label>
									<input type='radio' name='same' id='same_0' value='0' onClick='isEQ()'><label for='same_0'>신규 입력</label>
									</td>
								</tr>
							</table>
							<table width=100% cellpadding=0 cellspacing=0 class='input_table_box' stlye='verticol-align:top;'>
								<tr>
									<td class='input_box_title' width='100'>이름</td>
									<td class='input_box_item'><input type='text' name='name_b' size='27' maxlength='20' class='textbox' value='$mem_name' validation='true'  title='이름' ></td>
								</tr>
								<tr>
									<td class='input_box_title'>주소</td>
									<td class='input_box_item' style='padding:5px 5px;'><input type='text' name='zipcode1_b' id='zipcode1_b'  size='10' maxlength='3' class='textbox' value='' validation='true' title='사업장 우편번호' readonly> -
									<input type='text' name='zipcode2_b' id='zipcode2_b'  size='10' maxlength='3' class='textbox' value='' validation='true' title='사업장 우편번호' readonly>
										<img src='../images/".$admininfo["language"]."/btn_search_address.gif' align=absmiddle border=0 onClick=\"zipcode('3')\"  style='cursor:pointer;'><!--input type='button' value='주소 찾기'  class='box' onClick=\"zipcode('3')\" alt='주소 찾기'--></br>

										<input type='text' name='addr1_b' id='addr1_b' size='40' maxlength='80' class='textbox' style='margin-top:3px; margin-bottom:3px;' value='' validation='true' title='사업장주소' readonly><br>
										<input type='text' name='addr2_b' id='addr2_b' size='40' maxlength='80' class='textbox' value='' validation='true' title='사업장 세부주소'> 세부주소</td>
								</tr>
								<tr>
									<td class='input_box_title'>전화번호</td>
									<td class='input_box_item'><input type='text' name='tel1_b' size='10' maxlength='3' class='textbox' value='' validation='true' title='전화번호' numeric='true'> -
									<input type='text' name='tel2_b' size='12' maxlength='4' class='textbox' value='' validation='true' title='전화번호' numeric='true'> -
									<input type='text' name='tel3_b' size='12' maxlength='4' class='textbox' value='' validation='true' title='전화번호' numeric='true'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title'>핸드폰</td>
									<td class='input_box_item'>
										<input type='text' name='pcs1_b' size='10' maxlength='3' class='textbox' value='' validation='true' title='핸드폰번호' numeric='true'> -
										<input type='text' name='pcs2_b' size='12' maxlength='4' class='textbox' value='' validation='true' title='핸드폰번호' numeric='true'> -
										<input type='text' name='pcs3_b' size='12' maxlength='4' class='textbox' value='' validation='true' title='핸드폰번호' numeric='true'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title'>기타 요구사항</td>
									<td class='input_box_item'><input type='text' class='textbox' name='msg1' size='40' value='' /></td>
								</tr>
								<tr>
									<td class='input_box_title'>배송시 유의사항</td>
									<td class='input_box_item'><input type='text' class='textbox'  name='msg2' size='40' value='EX) 부재시 행정실 배송요망 등' rel='first' id='msg2' onclick='input_text()' /></td>
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
		<tr>
			<td colspan='2' height='25' style='padding:0px 0px;'>
				<img src='../images/dot_org.gif' align=absmiddle> <b>결제정보입력</b>
			</td>
		</tr>

		<tr>
			<td colspan='2' style='padding:5px 0px;'>
				<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
						<col width='15%'>
						<col width='*'>
						<tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>상품 총 금액</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($cart_totalprice)."원</span></td>
						</tr>
						<tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>배송료</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($total_delivery_price)."원</span></td>
						</tr>
						<tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>최종 결재 금액</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($cart_totalprice+$total_delivery_price)."원 (무료배송!, 배송비".number_format($total_delivery_price)."원 할인)</span></td>
						</tr>
				</table>";
		if($total_cart_price >= 500000){

		$Contents .= "
				<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
				<tr>
					<td colspan='2' height='10'></td>
				</tr>
				<tr>
					<td colspan='2' height='25'><img src='../images/dot_org.gif' align=absmiddle> <b>사은품 선택</b></td>
				</tr>
				<tr height=1><td colspan=2 class='dot-x'></td></tr>
						<tr>
						<td colspan='2'>
							<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
								<tr>
								";
		$giftRelation = giftRelation($total_cart_price);
		for($g=0; $g<count($giftRelation); $g++){
		$Contents .= "
						<td>
							<img src='".$admin_config[mall_data_root]."/images/product/s_".$giftRelation[$g][id].".gif' alt='예시 이미지' width='95px' height='95px'><br / >
							<input type='radio' class='check bonusck' name='gift_id' value='".$giftRelation[$g][id]."' title='사은품' ".($g == 0 ? " checked" : "")."/>
							".$giftRelation[$g][pname]."
						</td>";
		}
		$Contents .= "
								</tr>
							</table>
						</td>
					</tr>
				</table>";
		}
$Contents .= "

			</td>
		</tr>
		<tr>
			<td colspan='2' height='25' style='padding:0px 10px;'>
	<!--s:지훈: 신용카드-->

			<div>
				<p class='bold'><input	type='radio' class='check' name='payment_div' value='after_bank' title='결제방법' validation='false' checked/>후불제
			</div>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
				<tr>
					<td colspan='2' height='10'></td>
				</tr>
				<tr>
					<td colspan='2' height='25' style='padding:0px 0px;'><img src='../images/dot_org.gif' > <b>증빙문서 정보 입력</b>
					</td>
				</tr>
				<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center class='list_table_box'>
				<tr height='25'>
					<td class='list_box_td list_bg_gray' width='100'  style='padding:0px 10px; text-align:left;'><b>증빙문서</b></td>
					<td class='list_box_td' style='padding:0px 10px; text-align:left;'>
						<span id='receipt_type1'><input type='radio' class='check receipt_type' name='receipt_type' id='receipt_type_1'  value='1' validation='true' title='증빙문서' onclick=\"receiptChoice('1')\" /><label for='receipt_type_1'>(전자)세금 계산서</label></span>
						<span id='receipt_type2'><input type='radio' class='check receipt_type' name='receipt_type' id='receipt_type_2' value='2' validation='true' title='증빙문서' onclick=\"receiptChoice('2')\" /><label for='receipt_type_2'>지출증빙용 현금영수증</label></span>
						<span id='receipt_type3' style='display:none'><input type='radio' class='check receipt_type' id='receipt_type_3'  name='receipt_type'  value='3' validation='true' title='증빙문서' onclick=\"receiptChoice('2')\" /><label for='receipt_type_3'>소득공제용 현금영수증</label></span>
						<span id='receipt_type4'><input type='radio' class='check receipt_type' name='receipt_type'  value='4' validation='true' title='증빙문서' onclick=\"receiptChoice('3')\" checked />미발급</span>
					</td>
				</tr>
				<tr id='receipt_result1' style='display:none'>
					<td></td>
					<td height='25'>
						결재 창에서 바로 발급 받으실 수 있습니다.

					</td>
				</tr>

				<tr id='receipt_result_non' style='display:none'>
					<td></td>
					<td height='25'>
						주문 완료 후 재신청을 원하실 경우에는 고객센터로 문의해주세요 TEL 1544-6040
					</td>
				</tr>

				
			</tbody>
		</table>
			</td>
		</tr>
		<tr height=1><td colspan=2 class='dot-x'></td></tr>
		<tr height=40>
			<td colspan='2' style='padding:10px;' align=center>
				".($db2->dt[state] == "0" ? "품절중인 상품이 있습니다. 확인 후 진행 바랍니다." :"<input type='submit' value='다음단계'  class='box' id='submit_btn' style='padding:15px;font-weight:bold;'>")."
			</td>
		</tr>
	</table>
</form>
	";

$Contents .= "
	</td>
</tr>
</table>

<form name='sc_info'>
<input type='hidden' name='sc_damdang' value='$sc_damdang'>
<input type='hidden' name='sc_damdang_tel1' value='$sc_damdang_tel1'>
<input type='hidden' name='sc_damdang_tel2' value='$sc_damdang_tel2'>
<input type='hidden' name='sc_damdang_tel3' value='$sc_damdang_tel3'>
<input type='hidden' name='sc_damdang_pcs1' value='$sc_damdang_pcs1'>
<input type='hidden' name='sc_damdang_pcs2' value='$sc_damdang_pcs2'>
<input type='hidden' name='sc_damdang_pcs3' value='$sc_damdang_pcs3'>
<input type='hidden' name='sc_mail' value='$sc_damdang_email'>
</form>
		";

$Script = "
<script language='JavaScript' src='/admin/js/admin.js'></Script>
<script language='JavaScript' >
function num_apply(cart_key, pid) {
	var quantity = parseInt($('#quantity_'+cart_key).val()) ;
	var sellprice = parseInt($('#sellprice_'+cart_key).val()) ;
	//alert('#sellprice_'+cart_key);
	//document.write('manual_order.countadd.php?cart_key='+cart_key+'&PID='+pid+'&act=mod&count='+quantity+'&sellprice='+sellprice+'&mode=$mode&est_ix=$est_ix');
	window.frames['act'].location.href='manual_order.countadd.php?cart_key='+cart_key+'&PID='+pid+'&act=mod&count='+quantity+'&sellprice='+sellprice+'&mode=$mode&est_ix=$est_ix';
}

function receiptChoice(clickType){

	if(clickType == '1'){
		document.getElementById('receipt_result1').style.display = 'none';
		document.getElementById('receipt_result2').style.display = '';
		document.getElementById('receipt_result3').style.display = '';
		document.getElementById('receipt_result1_1').style.display = '';
		document.getElementById('com_num_info').style.display = '';
			document.getElementById('receipt_result_non').style.display = 'none';
	}

	if(clickType == '2'){
		//alert(document.order_form.payment_div[0].checked);
			document.getElementById('receipt_result1').style.display = 'none';
			document.getElementById('receipt_result2').style.display = 'none';
			document.getElementById('receipt_result3').style.display = 'none';
			document.getElementById('receipt_result1_1').style.display = '';
			document.getElementById('com_num_info').style.display = '';
			document.getElementById('receipt_result_non').style.display = 'none';

		$('.valid').each(function(){
			$(this).attr('validation','false');
		})
	}

	if(clickType == '3'){

		document.getElementById('receipt_result1').style.display = 'none';
		document.getElementById('receipt_result2').style.display = 'none';
		document.getElementById('receipt_result3').style.display = 'none';
		document.getElementById('receipt_result1_1').style.display = 'none';
		document.getElementById('com_num_info').style.display = 'none';
		document.getElementById('receipt_result_non').style.display = '';
		$('.valid').each(function(){
			$(this).attr('validation','false');
		})
	}
}

function isEQ()	{

		var form = document.order_form;

		if (form.same[0].checked)
		{
			form.name_b.value = form.name_a.value;
			//form.mail_b.value = form.mail_a.value;
			form.zipcode1_b.value = form.zipcode1.value;
			form.zipcode2_b.value = form.zipcode2.value;
			form.addr1_b.value = form.addr1.value;
			form.addr2_b.value = form.addr2.value;
			form.tel1_b.value = form.tel1_a.value;
			form.tel2_b.value = form.tel2_a.value;
			form.tel3_b.value = form.tel3_a.value;
			form.pcs1_b.value = form.pcs1_a.value;
			form.pcs2_b.value = form.pcs2_a.value;
			form.pcs3_b.value = form.pcs3_a.value;

		}
		else
		{
			form.name_b.value = '';
			//form.mail_b.value = '';
			form.zipcode1_b.value = '';
			form.zipcode2_b.value = '';
			form.addr1_b.value = '';
			form.addr2_b.value = '';
			form.tel1_b.value = '';
			form.tel2_b.value = '';
			form.tel3_b.value = '';
			form.pcs1_b.value = '';
			form.pcs2_b.value = '';
			form.pcs3_b.value = '';
		}
	}

function isEQ_sc()	{

	var form1 = document.order_form;
	var form2 = document.sc_info;

	if (form.same_sc[0].checked)
	{
		form.sc_damdang.value = form.name_a.value;
		form.sc_mail.value = form.mail_a.value;
		form.sc_tel1.value = form.tel1_a.value;
		form.sc_tel2.value = form.tel2_a.value;
		form.sc_tel3.value = form.tel3_a.value;
		form.sc_pcs1.value = form.pcs1_a.value;
		form.sc_pcs2.value = form.pcs2_a.value;
		form.sc_pcs3.value = form.pcs3_a.value;

	}
	else
	{
		form.sc_damdang.value = form2.sc_damdang.value;
		form.sc_mail.value = form2.sc_mail.value;
		form.sc_tel1.value = form2.sc_damdang_tel1.value;
		form.sc_tel2.value = form2.sc_damdang_tel2.value;
		form.sc_tel3.value = form2.sc_damdang_tel3.value;
		form.sc_pcs1.value = form2.sc_damdang_pcs1.value;
		form.sc_pcs2.value = form2.sc_damdang_pcs2.value;
		form.sc_pcs3.value = form2.sc_damdang_pcs3.value;
	}
}

function idsearch() {
	var zip = window.open('./manual_order.searchuser.php','','width=440,height=400,scrollbars=yes,status=no');
}

function input_text(){
	if($('#msg2').attr('rel') == 'first'){
		$('#msg2').val('');
		$('#msg2').attr('rel','');
	}
}

</Script>
";

if($view == "innerview"){

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>".EstimateApplyList($cid,$depth)."</body></html>";

	echo "
	<Script>
	parent.document.getElementById('estimate_product_list').innerHTML = document.body.innerHTML;
	</Script>";
}else{
	$P = new LayOut;
	$P->addScript = $Script;
	$P->OnloadFunction = "";//MenuHidden(false);
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->strLeftMenu = order_menu();
	$P->strContents = $Contents;
	$P->Navigation = "주문관리 > 수동주문 > 수동주문정보입력";
	$P->title = "수동주문정보입력";
	$P->PrintLayOut();
}
function getCompanyCartAdmin($company_id,$delivery_company, $cart_key){
	global $user;
	$where = " cart_key = '$cart_key'";
	if($delivery_company == "MI"){
		$delivery_company_where = " and (c.delivery_company ='MI' or c.delivery_company = '') ";
	}else{
		$delivery_company_where = " and c.delivery_company = '$delivery_company' ";
	}
	$mdb = new Database;
	$admin_delievery_policy = getTopDeliveryPolicy($mdb);

	$sql = "select c.*,
			p.delivery_package,
			if(p.delivery_policy =1,
				(select if(delivery_policy = 1,'".$admin_delievery_policy[delivery_price]."',delivery_price) from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '$company_id' )
			,delivery_price) as delivery_price,
			(select if(delivery_policy = 1,'".$admin_delievery_policy[delivery_basic_policy]."',delivery_basic_policy) from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '".$company_id."') as delivery_basic_policy
			from shop_cart c,shop_product p
			where $where and c.id = p.id and company_id = '".$company_id."'
			and c.delivery_company='$delivery_company'
			order by c.regdate desc ";//정렬이 delivery_price 인 것을 regdate 로 바꿈 kbk 11.10.10

	$mdb->query($sql);
	return $mdb->fetchall();
}
function giftRelation($total_price){
	global $db;

	$sql = "select * from shop_product where $total_price >= startprice and $total_price < endprice and product_type = '6' limit 4";
	$db->query($sql);

	$gift_product = $db->fetchall();

	return $gift_product;
}

function getScName($sc_code){
	global $db;

	$db->query("select sc_nm from shop_comm_sc where sc_code = '$sc_code' ");
	$db->fetch();

	return $db->dt[sc_nm];
}



?>

