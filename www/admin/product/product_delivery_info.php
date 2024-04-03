<?
include("../class/layout.class");
include_once("brand.lib.php");

$db = new Database;
$db2 = new Database;


if($pid){

	if($info_type == "whole"){	//도매
		$product_delivery_type = "W";
	}else{	//소매
		$product_delivery_type = "R";
	}
	$sql = "select * from shop_product_delivery where pid = '".$pid."' and product_delivery_type = '".$product_delivery_type."'";

	$db->query($sql);
	$db->fetch();
	
	if($db->total > 0){
		$act = 'insert';
	}else{
		//신규일경우 사이트 기본설정값으로 노출 시킴
		$sql = "SELECT 
			*
			FROM ".TBL_COMMON_SELLER_DELIVERY."
		where 
			company_id = '".$admininfo[company_id]."'";

		$db->query($sql);
		$db->fetch();

		$act = 'insert';
	}

}else{
	
	echo "<script language='javascript'>
			alert('입점업체 배송정책은 상품등록 후에 확인/변경 하실 수 있습니다');
			self.close();
	</script>";
}

if($info_type == ""){
	$info_type = "retail";
}

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 >
	<tr>
		<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
			<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>배송 정책</b>
		</td>
	</tr>
	<tr>
	    <td align='left' colspan=2 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "retail" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=retail&pid=".$pid."&mmode=pop'>소매 배송정책</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "whole" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						$Contents01 .= "<a href='?info_type=whole&pid=".$pid."&mmode=pop'>도매 배송정책</a>";
					$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</div>
	    </td>
	  </tr>
	</table>";

$Contents01 .= "
	<form name='edit_form' action='product_delivery.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='act'><!--target='iframe_act' -->
	<input name='act' type='hidden' value='$act'>
	<input name='info_type' type='hidden' value='$info_type'>
	<input name='company_id' type='hidden' value='".$admininfo[company_id]."'>
	<input name='delivery_type' type='hidden' value='F'>
	<input name='mall_ix' type='hidden' value='".$admininfo[mall_ix]."'>
	<input name='pid' type='hidden' value='".$pid."'>
	<input name='product_delivery_type' type='hidden' value='".$product_delivery_type."'>
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
	<col width='20%' />
	<col width='80' />

	<tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>배송 방법 선택 <img src='".$required3_path."'></b>   </td>
	    <td class='input_box_item'>
	     	<input type=checkbox name='mall_send_tekbae_use' value='1' id='takbae'  ".CompareReturnValue("1",$db->dt[mall_send_tekbae_use],"checked")."><label for='takbae'>택배</label>
			<input type=checkbox name='mall_send_quick_use' value='1' id='quick'  ".CompareReturnValue("1",$db->dt[mall_send_quick_use],"checked")."><label for='quick'>퀵서비스(오토바이)</label>
			<input type=checkbox name='mall_send_truck_use' value='1' id='truck' ".CompareReturnValue("1",$db->dt[mall_send_truck_use],"checked")."><label for='truck'>용달(개인 트럭)</label>
			<input type=checkbox name='mall_send_self_use' value='1' id='self' ".CompareReturnValue("1",$db->dt[mall_send_self_use],"checked")."><label for='self'>방문수령</label>
			<input type=checkbox name='mall_send_direct_use' value='1' id='direct' ".CompareReturnValue("1",$db->dt[mall_send_direct_use],"checked")."><label for='direct'>직배송</label>
	    </td>
	</tr><!--
	<tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>상품별 개별정책 설정 <img src='".$required3_path."'></b>   </td>
	    <td class='input_box_item'>
		
	     	<input type=radio name='order_price_shipping_use' class=textbox value='N' id='order_price_shipping_use_1'  ".CompareReturnValue("N",$db->dt[order_price_shipping_use],"checked")."><label for='order_price_shipping_use_1'> 미사용</label>&nbsp;
			<input type=radio name='order_price_shipping_use' class=textbox value='Y' id='order_price_shipping_use_2'  ".CompareReturnValue("Y",$db->dt[order_price_shipping_use],"checked")."><label for='order_price_shipping_use_2'> 사용</label>&nbsp;&nbsp;&nbsp;
		
			<input type='text' name='order_price' class=textbox  value='".$db->dt[order_price]."' size='6'> 원 이상 구매시 배송비 
			<input type='text' name='order_price_shipping' class=textbox value='".$db->dt[order_price_shipping]."' size='6'> 원
	    </td>
	</tr>-->

	<tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>구매 수량당 무료배송 <img src='".$required3_path."'></b>   </td>
	    <td class='input_box_item'>
	     	<input type=radio name='order_cnt_free_shipping_use' value='N' id='order_cnt_free_shipping_use_1'  ".CompareReturnValue("N",$db->dt[order_cnt_free_shipping_use],"checked")."><label for='order_cnt_free_shipping_use_1'> 미사용</label>&nbsp;
			<input type=radio name='order_cnt_free_shipping_use' value='Y' id='order_cnt_free_shipping_use_2'  ".CompareReturnValue("Y",$db->dt[order_cnt_free_shipping_use],"checked")."><label for='order_cnt_free_shipping_use_2'> 사용</label>&nbsp;&nbsp;&nbsp;
			<input type='text' class='textbox' name='free_shipping_order_cnt' value='".$db->dt[free_shipping_order_cnt]."' size='6'> 개 이상
	    </td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>묶음 배송 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type=radio name='dump_shipping_use' value='N' id='dump_shipping_use_1'  ".CompareReturnValue("N",$db->dt[dump_shipping_use],"checked")."><label for='dump_shipping_use_1'> 미사용</label>
			&nbsp;<input type=radio name='dump_shipping_use' value='Y' id='dump_shipping_use_2'  ".CompareReturnValue("Y",$db->dt[dump_shipping_use],"checked")."><label for='dump_shipping_use_2'> 사용 </label>
		</td>
	</tr>
	<!--
	<tr>
		<td class='input_box_title' nowrap> <b>구매수량 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' id='DP' nowrap> <input style='width:40px' type='text' class=textbox size='30' name='free_shipping_order_cnt' value='".$free_shipping_order_cnt."' id='free_shipping_order_cnt' company_commission='".$company_commission."' style='TEXT-ALIGN:right' validation=false title='구매수량' > 이상 구매시 무료배송됩니다.</td>
	</tr>-->
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>기본 배송비용 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item'>
			<table cellpadding=2 cellspacing=0 border='0' width='100%'>
				<col width='40px'>
				<col width='*'>
				<tr>
					<td >택배 :</td>
					<td >
						<input type=text class='textbox number' name='free_cost_price' value='".$db->dt[free_cost_price]."' size=8 validation='true' title='기본택배비용'  style='width:50px' dir='rtl'> 원 미만 일때
						<input type=text class='textbox number' name='basic_send_cost_tekbae' value='".$db->dt[basic_send_cost_tekbae]."' size=8 validation='true' title='기본택배비용'  style='width:50px' dir='rtl'> 원 부과 
						<span class='small blu'> 지역에따른 배송 비용이 정해 지지 않은 모든 제품에 적용됩니다.</span>
						<!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'R' ,$db, "db")." <span class='small blu'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'S')."</span>-->
					</td>
				</tr>
				<!--tr>
					<td>퀵서비스 :</td>
					<td> ".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class='textbox number' name='basic_send_cost_quick' value='".$db->dt[basic_send_cost_quick]."' size=10> ".$currency_display[$admin_config["currency_unit"]]["back"]." <span class='small blu'> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'T')."</span>
					</td>
				</tr>
				<tr>
					<td>용달 :</td>
					<td>
						".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class='textbox number' name='basic_send_cost_truck' value='".$db->dt[basic_send_cost_truck]."' size=10> ".$currency_display[$admin_config["currency_unit"]]["back"]."  <span class='small blu'> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'U')."</span>
					</td>
				</tr-->
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>배송비 결제 수단 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item'>
			<input type=radio name='delivery_basic_policy' value='1' id='delivery_basic_policy_1'  ".CompareReturnValue("1",$db->dt[delivery_basic_policy],"checked")."><label for='delivery_basic_policy_1'>선불</label>
			&nbsp;
			<!--
			<input type=radio name='delivery_basic_policy' value='2' id='delivery_basic_policy_2'  ".CompareReturnValue("2",$db->dt[delivery_basic_policy],"checked")."><label for='delivery_basic_policy_2'>착불</label>
			 &nbsp;-->
			<input type=radio name='delivery_basic_policy' value='3' id='delivery_basic_policy_3'  ".CompareReturnValue("3",$db->dt[delivery_basic_policy],"checked")."><label for='delivery_basic_policy_3'>무료</label>
			&nbsp;
			<!--input type=radio name='delivery_basic_policy' value='4' id='delivery_basic_policy_4'  ".CompareReturnValue("4",$db->dt[delivery_basic_policy],"checked")."><label for='delivery_basic_policy_4'>방문수령</label-->
		</td>
	</tr>
	<tr bgcolor=#ffffff height=50>
		<td class='input_box_title'> <b>무료배송상품 정책 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item' >
			<input type=radio name='delivery_free_policy' value='2' id='delivery_free_policy_2'  ".CompareReturnValue("2",$db->dt[delivery_free_policy],"checked")."><label for='delivery_free_policy_2'>구매상품들중 무료배송 상품이 있을 때 '무료배송 상품'만 배송비 무료로 설정</label><br>
			<input type=radio name='delivery_free_policy' value='1' id='delivery_free_policy_1'  ".CompareReturnValue("1",$db->dt[delivery_free_policy],"checked")."><label for='delivery_free_policy_1'>구매상품들중 무료배송 상품이 있을 때 전체 배송비 무료로 설정</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=60>
		<td class='input_box_title'> <b>상품별 배송비 정책 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td><input type=radio name='delivery_product_policy' value='2' id='delivery_product_policy_2'  ".CompareReturnValue("2",$db->dt[delivery_product_policy],"checked")."><label for='delivery_product_policy_2'>상품을 2가지 이상 주문했을때 '기본배송비'와 '상품개별 설정 배송비'중 큰금액으로 설정</label></td>
				</tr>
				<tr>
					<td><input type=radio name='delivery_product_policy' value='1' id='delivery_product_policy_1'  ".CompareReturnValue("1",$db->dt[delivery_product_policy],"checked")."><label for='delivery_product_policy_1'>상품을 2가지 이상 주문했을때 '기본배송비'와 '상품개별 설정 배송비'를 합쳐서 설정</label></td>
				</tr>
				<Tr>
					<td><input type=radio name='delivery_product_policy' value='3' id='delivery_product_policy_3'  ".CompareReturnValue("3",$db->dt[delivery_product_policy],"checked")."><label for='delivery_product_policy_3'>상품을 2가지 이상 주문했을때 '기본배송비'와 '상품개별 설정 배송비'중 작은금액으로 설정</label></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=50>
	    <td class='input_box_title'> <b>도서산간 배송비 정책 <img src='".$required3_path."'></b></td>
	    <td class='input_box_item'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td><input type=radio name='delivery_region_policy' value='1' id='delivery_region_policy_1' ".($db->dt[delivery_region_policy] == '1' || $db->dt[delivery_region_policy]==""?"checked":"")." ><label for='delivery_region_policy_1'> 배송비가 무료배송 일때, 수취자 배송지 정보가 도서산간인 경우 설정된 배송비를 부과한다.".getTransDiscription(md5($_SERVER["PHP_SELF"]),'X')."</label></td>
				</tr>
				<tr>
					<td><input type=radio name='delivery_region_policy' value='2' id='delivery_region_policy_2'  ".CompareReturnValue("2",$db->dt[delivery_region_policy],"checked")."><label for='delivery_region_policy_2'> 배송비가 무료배송이면, 수취자 배송지 정보가 도서산간 이어도 배송비를 부과하지 않는다.".getTransDiscription(md5($_SERVER["PHP_SELF"]),'Z')."</label></td>
				</tr>
			</table>
	    </td>
	</tr>
	<tr bgcolor=#ffffff height=75>
		<td class='input_box_title'> <b>반품/교환 배송비 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item'>
			<table border=0 cellpadding=5 cellspacing=0>
				<tr>
					<td>반품 배송비 : 편도 <input type='text' name='return_shipping_price' class='textbox' value='".$db->dt[return_shipping_price]."' id='return_shipping_price'  style='width:50px' dir='rtl'> 원 -> 무료배송시 부과방법 <input type=radio name='return_shipping_cnt' value='2' id='return_shipping_cnt_1' ".CompareReturnValue("2",$db->dt[return_shipping_cnt],"checked")."> <label for='return_shipping_cnt_1'>왕복 ( 편도 * 2 )</label> 
					<input type=radio name='return_shipping_cnt' value='1' id='return_shipping_cnt_2' ".CompareReturnValue("1",$db->dt[return_shipping_cnt],"checked")."> <label for='return_shipping_cnt_2'>편도</label>
					<div style='line-height:130%;color:#F37361'>
						&nbsp;1) 구매자 귀책 시 반품 배송비가 적용되며, 판매자 귀책 시 반품 배송비가 적용되지 않습니다.
						<br />&nbsp;2) 상품 구매 시 배송비를 결제한 반품 요청 주문건은 편도*2 가 적용됩니다.
						<br />&nbsp;3) 상품 구매 시 무료 배송일 경우 왕복을 선택하면 편도*2 가 적용되며, 편도를 선택하면 편도*1 로 적용됩니다.
					</div>
					</td>
				</tr>
				<tr>
					<td>교환 배송비 : 편도 <input type='text' name='exchange_shipping_price' class='textbox' value='".$db->dt[exchange_shipping_price]."' id='exchange_shipping_price'  style='width:50px' dir='rtl'> 원 <label for='delivery_product_policy_1'> </label> -> 왕복 ( 편도 * 2 )
					<div style='line-height:130%;color:#F37361'>
						&nbsp;1) 구매자 귀책 시 교환 배송비가 적용되며, 판매자 귀책 시 교환 배송비가 적용되지 않습니다.
						<br />&nbsp;2) 상품 구매 시 배송비를 결제한 교환 요청 주문건은 편도*2 가 적용됩니다.
						<br />&nbsp;3) 상품 구매 시 무료 배송일 경우에도 편도*2 가 적용됩니다.
					</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
	    <td class='input_box_title' rowspan=2> <b>추가지역 배송비 설정</b> </td>
	    <td class='input_box_item'>
		<input type='radio' id='delivery_region_use_1' name='delivery_region_use' value='1' ".CompareReturnValue("1",$db->dt[delivery_region_use],"checked")."><label for='delivery_region_use_1'>사용</label>
		<input type='radio' id='delivery_region_use_0' name='delivery_region_use' value='0' ".CompareReturnValue("0",$db->dt[delivery_region_use],"checked")."><label for='delivery_region_use_0'>사용안함</label></td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_item'>
			<table border=0 cellpadding=0 cellspacing=0 width='100%' >
				<tr>
					<td>
						<input type='hidden' id='region_delivery_type_1' name='region_delivery_type' value='1' ><!--label for='region_delivery_type_1'>지역명</label-->
					</td>
				</tr>
				<tr height=50>
					<td id='region_name' style='padding:5px 5px 5px 0px;'>
						";
						$sql = "select * from shop_product_region_delivery where region_delivery_type = 1 and pid='".$pid."' and product_delivery_type = '".$product_delivery_type."'";
						$db2->query($sql);
						if(!$db2->total){
						$Contents01 .= "<table border=0 cellpadding=0 cellspacing=2 width='100%' id='region_name_table_add' class='region_name_table_add list_table_box'>
							<col width='70%'>
							<col width='*'>
							<tr align=center height=30>
								<td class=s_td>배송지역</td>
								<td class=e_td>추가배송비</td>
							</tr>
							<tr>
								<td class='list_box_td list_bg_gray' height=30 style='padding:5px;'>
									<input type='text' class='textbox' name='region_name_text[]' style='width:90%;'> <br>
									<!--span class='small'>  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G')." </span-->
								</td>
								<td class='list_box_td point' align=center>
									".$currency_display[$admin_config["currency_unit"]]["front"]." <input type='text' class='textbox number' name='region_name_price[]' style='width:55%;'> ".$currency_display[$admin_config["currency_unit"]]["back"]."
									<img src='../images/".$admininfo["language"]."/btn_add2.gif' onclick=\"insertInputBox('region_name_table')\" style='cursor:pointer' align=absmiddle>
									<!--br><span class='small'> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H')."  </span--></td>
							</tr>
							</table>
							";
						}else{
							$Contents01 .= "<table border=0 cellpadding=0 cellspacing=2 width='100%' id='region_name_table_add' class='region_name_table_add list_table_box'>
									<col width='70%'>
									<col width='*'>";
							for($i=0;$i<$db2->total;$i++){
								$db2->fetch($i);
								if($i==0){
									$Contents01 .= "
									<tr align=center height=30><td class='s_td'>배송지역</td><td class='e_td'>추가배송비</td></tr>
									<tr height=30>
										<td class='list_box_td list_bg_gray'  >
											<input type='hidden' class='textbox' name='prd_ix[]' value='".$db2->dt[prd_ix]."'>
											<input type='text' class='textbox' name='region_name_text[]' style='width:90%;' value='".$db2->dt[region_name_text]."'>
										</td>
										<td class='list_box_td point'  align=center>
											".$currency_display[$admin_config["currency_unit"]]["front"]." <input type='text' class='textbox number' name='region_name_price[]' style='width:55%;' value='".$db2->dt[region_name_price]."'> ".$currency_display[$admin_config["currency_unit"]]["back"]."
											<img src='../images/".$admininfo["language"]."/btn_add2.gif' onclick=\"insertInputBox('region_name_table')\" style='cursor:pointer' align=absmiddle>
										</td>
									</tr>";
								}else{
									$Contents01 .= "<tr height=30>
										<td class='list_box_td list_bg_gray'>
											<input type='hidden' class='textbox' name='prd_ix[]' value='".$db2->dt[prd_ix]."'>
											<input type='text' class='textbox' name='region_name_text[]' style='width:90%;' value='".$db2->dt[region_name_text]."'>
										</td>
										<td class='list_box_td point' align=center>
											".$currency_display[$admin_config["currency_unit"]]["front"]." <input type='text' class='textbox number' name='region_name_price[]' value='".$db2->dt[region_name_price]."' style='width:55%;'> ".$currency_display[$admin_config["currency_unit"]]["back"]."
											<img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"$(this).parent().parent().remove();/*del_table('region_name',event)this.parentNode.parentNode.parentNode.removeNode(true);*/\" style='cursor:pointer' align=absmiddle>
										</td>
									</tr>";
								}
							}

							$Contents01 .= "</table>";
						}
						$Contents01 .= "
						<table border=0 cellpadding=0 cellspacing=2 width='100%' id='region_name_table' style='display:none;' class='region_name_table list_table_box' disabled='disabled'>
							<col width='70%'>
							<col width='*'>
							<tr height=30>
								<td class='list_box_td list_bg_gray' height=30><input type='text' class='textbox' name='region_name_text[]' style='width:90%;'></td>
								<td class='list_box_td point' align=center>
									".$currency_display[$admin_config["currency_unit"]]["front"]." <input type='text' class='textbox number' name='region_name_price[]' style='width:55%;'> ".$currency_display[$admin_config["currency_unit"]]["back"]."
									<img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"$(this).parent().parent().remove();/*del_table('region_name',event);this.parentNode.parentNode.parentNode.removeNode(true);*/\" style='cursor:pointer' align=absmiddle>
								</td>
							</tr>
						</table>
						<table border=0 cellpadding=0 cellspacing=0 width='100%' >
							<tr>
								<td height=25 style='padding:5px 0px;'><span class='small'>지역명 입력시 콤마(,)로 구분하여 지역을 추가 하실 수 있습니다. (입력예. 제주,울릉,거제... 처럼 '시', '도'등은 빼고 입력하세요)</span> </td>
								<td style='padding:5px 0px;'><span class='small'>기본 배송비에 추가될 금액을 입력하세요.</span></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style='padding:5px 0px;'><span class='small blu'> 지역별 배송비는 '기본 배송정책', '상품별 배송정책'에 의해 배송비가 산정된 이후 금액이 추가 됩니다.</span></td>
				</tr>
			</table>
		</td>
	</tr>
	</table>

	<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
		</td>
	</tr>
	</table><br><br>
	</form>
";


$Contents01 .= "
<iframe name='extand' id='extand' src='' width=0 height=0></iframe>";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents01 .= HelpBox("상품별 배송 정책 설정", $help_text);

$Script = "
<script language='javascript'>
function insertInputBox(obj){
	var objs=$('table.'+obj).find('tr');
	if(objs.length > 0 ){
		//alert(objs[0]);
		var obj_table = objs[0].cloneNode(true);
		var target_obj = objs[objs.length-1];	
	}else{
		
		var obj_table = objs[0].cloneNode(true);
		var target_obj = objs[0];
	}
	var newRow = objs.clone(true).appendTo($('#region_name_table_add'));  

}

</script>
";

if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='brand.js'></script>\n".$Script;
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "Init(document.brandform);";
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