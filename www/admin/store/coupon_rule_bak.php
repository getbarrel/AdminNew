<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] < 9){
	header("Location:/admin/store/company.add.php");
}

if($admininfo[admin_id] == "forbiz"){
	//print_r($admininfo);
	//echo $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
}


$shmop = new Shared("b2c_coupon_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$coupon_data = $shmop->getObjectForKey("b2c_coupon_rule");
$coupon_data = unserialize(urldecode($coupon_data));
//print_r($coupon_data);
/*
$shmop = new Shared("reserve_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$reserve_data = $shmop->getObjectForKey("reserve_rule");
$reserve_data = unserialize(urldecode($reserve_data));
//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);
*/

/**
		차후 B2C , B2B 쿠폰 으로 구분 예정
**/

$db = new Database;
$db2 = new Database;


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' valign=top>
	<tr>
		<td align='left'> ".GetTitleNavigation("쿠폰 정책", "상점관리 > 쿠폰 정책")."</td>
	</tr>
	<tr>
		<td align='left' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b>쿠폰 지급 정책_상품구매시 발급</b></div>")."</td>
	</tr>
	</table>";
$Contents01 .="
	<!--table width='100%' cellpadding=3 cellspacing=0 border='0' class='input_table_box'>
		<col width=25%>
		<col width=*>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>쿠폰 사용 여부 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type='radio' name='goods_coupon_use_yn' value='Y' id='goods_coupon_use_yn_y'  ".($coupon_data[goods_coupon_use_yn] == "Y" ? "checked":"")."><label for='goods_coupon_use_yn_y' >사용</label>
				<input type='radio' name='goods_coupon_use_yn' value='N' id='goods_coupon_use_yn_n'  ".($coupon_data[goods_coupon_use_yn] =="N" ? "checked":"")."><label for='goods_coupon_use_yn_n' >사용안함</label>
			</td>
		</tr>
		<tr bgcolor=#ffffff height=50>
			<td class='input_box_title'> <b>상품쿠폰 기본 설정 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' style='line-height:150%;padding:10px;'>
				<table border=0 cellpadding=0 cellspacing=2 width='100%' id='goods_coupon_table_add' class='goods_coupon_table_add list_table_box'>
				<col width='80%'>
				<col width='*'>";
			//getTransDiscription(md5($_SERVER["PHP_SELF"]),'D' ,$coupon_data, "coupon_data")
			//print_r($coupon_data);
			//exit;

			if($coupon_data[goods_coupon_price_low]){
				for($i=0;$i<count($coupon_data[goods_coupon_price_low]);$i++){
					$Contents01 .="
					<tr>
						<td class='list_box_td list_bg_gray' height=30 style='padding:5px;'>
							상품 구매 금액이 <input type=text class='textbox' name='goods_coupon_price_low[]' value='".$coupon_data[goods_coupon_price_low][$i]."' style='width:60px;' validation='false' title='상품쿠폰 적용금액'> ~ <input type=text class='textbox' name='goods_coupon_price_high[]' value='".$coupon_data[goods_coupon_price_high][$i]."' style='width:60px;' validation='false' title='상품쿠폰 적용금액'> 일경우
							".CouponRuleSelectBox($coupon_data[goods_publish_ix][$i],"goods_publish_ix[]")." 을 발급합니다.
						</td>
						<td class='list_box_td point' align=center>";
							if($i==0)		$Contents01 .="<img src='../images/".$admininfo["language"]."/btn_add2.gif' onclick=\"insertInputBox('goods_coupon_table')\" style='cursor:pointer' align=absmiddle>";
							else				$Contents01 .="<img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"$(this).parent().parent().remove();\" style='cursor:pointer' align=absmiddle>";
					$Contents01 .="
						</td>
					</tr>";
				}
			}else{
				$Contents01 .="
					<tr>
						<td class='list_box_td list_bg_gray' height=30 style='padding:5px;'>
							상품 구매 금액이 <input type=text class='textbox' name='goods_coupon_price_low[]' value='' style='width:60px;' validation='false' title='상품쿠폰 적용금액'> ~ <input type=text class='textbox' name='goods_coupon_price_high[]' value='' style='width:60px;' validation='false' title='상품쿠폰 적용금액'> 일경우
							".CouponRuleSelectBox('',"goods_publish_ix[]")." 을 발급합니다.
						</td>
						<td class='list_box_td point' align=center>
							<img src='../images/".$admininfo["language"]."/btn_add2.gif' onclick=\"insertInputBox('goods_coupon_table')\" style='cursor:pointer' align=absmiddle>
						</td>
					</tr>";
			}
			$Contents01 .="
				</table>
				<table border=0 cellpadding=0 cellspacing=2 width='100%' id='goods_coupon_table' style='display:none;' class='goods_coupon_table list_table_box' disabled='disabled'>
				<col width='80%'>
				<col width='*'>
				<tr height=30>
					<td class='list_box_td list_bg_gray' height=30>
						상품 구매 금액이 <input type=text class='textbox' name='goods_coupon_price_low[]' value='' style='width:60px;' validation='false' title='상품쿠폰 적용금액'> ~ <input type=text class='textbox' name='goods_coupon_price_high[]' value='' style='width:60px;' validation='false' title='상품쿠폰 적용금액'> 일경우 ".CouponRuleSelectBox($coupon_data[goods_publish_ix],"goods_publish_ix[]")." 을 발급합니다.
					</td>
					<td class='list_box_td point' align=center>
						<img src='../images/".$admininfo["language"]."/btn_del.gif' onclick=\"$(this).parent().parent().remove();\" style='cursor:pointer' align=absmiddle>
					</td>
				</tr>
				</table>
			<br>
					".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."
			</td>
		</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' valign=top style='margin-top:20px;'>
		<tr>
			<td align='left' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b>쿠폰 지급 정책_회원가입 쿠폰 발급</b></div>")."</td>
		</tr>
	</table-->
	<table width='100%' cellpadding=3 cellspacing=0 border='0' class='input_table_box'>
	<col width=18%>
	<col width=*>
	
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>상품 쿠폰 사용 여부 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='goods_coupon_use_yn' value='Y' id='goods_coupon_use_yn_y'  ".($coupon_data[goods_coupon_use_yn] == "Y" ? "checked":"")."><label for='goods_coupon_use_yn_y' >사용</label>
			<input type='radio' name='goods_coupon_use_yn' value='N' id='goods_coupon_use_yn_n'  ".($coupon_data[goods_coupon_use_yn] =="N" ? "checked":"")."><label for='goods_coupon_use_yn_n' >사용안함</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>회원가입 시 쿠폰발행 사용여부<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='member_coupon_use_yn' value='Y' id='member_coupon_use_yn_y'  ".($coupon_data[member_coupon_use_yn] == "Y" ? "checked":"")."><label for='member_coupon_use_yn_y' >사용</label>
			<input type='radio' name='member_coupon_use_yn' value='N' id='member_coupon_use_yn_n'  ".($coupon_data[member_coupon_use_yn] =="N" ? "checked":"")."><label for='member_coupon_use_yn_n' >사용안함</label>
			
		</td>
	</tr>
	<tr bgcolor=#ffffff height=50 style='line-height:170%'>
		<td class='input_box_title'> <b>회원가입 쿠폰 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px;padding-left:10px;'>
			<table width='500px' cellpadding=0 cellspacing=3 border='0'  >
			<col width=25%>
			<col width=*>
			<tr>
				<td>
					<table border='0' cellpadding=3 width='100%' cellspacing=0 id='delivery_policy_3_terms'>
					<col width='23%' />
					<col width='*' />";

				if(count($coupon_data[member_publish_ix]) > 0){

					for($i=0;$i<count($coupon_data[member_publish_ix]);$i++){
$Contents01 .= "
					<tr bgcolor='#ffffff' id='add_table_price'>
						<td>
							<input type='hidden' name='option_length' id='option_length' value='".$i."'>
							".CouponRuleSelectBox($coupon_data[member_publish_ix][$i][publish_ix],"member_publish_ix[".$i."][publish_ix]","","member_publish_ix")."
						</td>
						<td>
							<input type='button' id='delivery_price_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('delivery_policy_3_terms','member_publish_ix','4')\">
							<input type='button' id='delivery_price_del' value='삭제' title='삭제' style='cursor:pointer;' >
						</td>
					</tr>";

					}
				}else{
	
$Contents01 .= "
					<tr bgcolor='#ffffff' id='add_table_price'>
						<td>
							<input type='hidden' name='option_length' id='option_length' value='0'>
							".CouponRuleSelectBox($coupon_data[member_publish_ix],"member_publish_ix[0][publish_ix]","","member_publish_ix")."
						</td>
						<td>
							<input type='button' id='delivery_price_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRow('delivery_policy_3_terms','member_publish_ix','4')\">
							<input type='button' id='delivery_price_del' value='삭제' title='삭제' style='cursor:pointer;' >
						</td>
					</tr>";
				}

$Contents01 .= "
					</table>
				</td>
			</tr>
			</table>
			<span class='blue small'> ※ 회원가입 쿠폰을 지급하지 않는 경우는 아무것도 선택하지 않으시면 됩니다.</span>
		</td>
	</tr>";

//[Start] 모바일 회원 가입시 전용 쿠폰을 주기 위해서 새로 셋팅해준다. 2015-01-20 
$Contents01 .= "
		
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> <b>모바일 회원가입 시 쿠폰발행 <br/> 사용여부<img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
						<input type='radio' name='mobile_member_coupon_use_yn' value='Y' id='mobile_member_coupon_use_y'  ".($coupon_data[mobile_member_coupon_use_yn] == "Y" ? "checked":"")."><label for='mobile_member_coupon_use_y' >사용</label>
						<input type='radio' name='mobile_member_coupon_use_yn' value='N' id='mobile_member_coupon_use_n'  ".($coupon_data[mobile_member_coupon_use_yn] =="N" ? "checked":"")."><label for='mobile_member_coupon_use_n' >사용안함</label>
						
					</td>
				</tr>
				<tr bgcolor=#ffffff height=50 style='line-height:170%'>
		<td class='input_box_title'> <b>모바일 회원가입 쿠폰 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px;padding-left:10px;'>
			<table width='500px' cellpadding=0 cellspacing=3 border='0'  >
			<col width=25%>
			<col width=*>
			<tr>
				<td>
					<table border='0' cellpadding=3 width='100%' cellspacing=0 id='delivery_policy_4_terms'>
					<col width='23%' />
					<col width='*' />";

				if(count($coupon_data[mobile_member_publish_ix]) > 0){

					for($i=0;$i<count($coupon_data[mobile_member_publish_ix]);$i++){
$Contents01 .= "
					<tr bgcolor='#ffffff' id='add_table_price_m'>
						<td>
							<input type='hidden' name='option_length_mobile' id='option_length_mobile' value='".$i."'>
							".CouponRuleSelectBox($coupon_data[mobile_member_publish_ix][$i][publish_ix],"mobile_member_publish_ix[".$i."][publish_ix]","","mobile_member_publish_ix")."
						</td>
						<td>
							<input type='button' id='delivery_price_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRowMobile('delivery_policy_4_terms','mobile_member_publish_ix','4')\">
							<input type='button' id='delivery_price_del_m' value='삭제' title='삭제' style='cursor:pointer;' >
						</td>
					</tr>";

					}
				}else{
	
$Contents01 .= "
					<tr bgcolor='#ffffff' id='add_table_price_m'>
						<td>
							<input type='hidden' name='option_length_mobile' id='option_length_mobile' value='0'>
							".CouponRuleSelectBox($coupon_data[mobile_member_publish_ix],"mobile_member_publish_ix[0][publish_ix]","","mobile_member_publish_ix")."
						</td>
						<td>
							<input type='button' id='delivery_price_add' value='추가' title='추가' style='cursor:pointer;' onclick=\"AddCopyRowMobile('delivery_policy_4_terms','mobile_member_publish_ix','4')\">
							<input type='button' id='delivery_price_del_m' value='삭제' title='삭제' style='cursor:pointer;' >
						</td>
					</tr>";
				}

$Contents01 .= "
					</table>
				</td>
			</tr>
			</table>
			<span class='blue small'> ※ 회원가입 쿠폰을 지급하지 않는 경우는 아무것도 선택하지 않으시면 됩니다.</span>
		</td>
	</tr>";
//[End] 2015-01-20

$Contents01 .=	"
	<tr>
		<td class='input_box_title'> <b>할인율 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px;padding-left:10px;'>
			 할인시
			 <select name='round_position' style='width:80px;'>
			<option value='1' ".($coupon_data[round_position] == "1" ? "selected":"").">일 자리</option>
			<option value='2' ".($coupon_data[round_position] == "2" ? "selected":"").">십 자리</option>
			<option value='3' ".($coupon_data[round_position] == "3" ? "selected":"").">백 자리</option>
			</select>
			자리
			&nbsp;&nbsp;&nbsp;
			<select name='round_type' style='width:80px;'>
			 
			<option value='1' ".($coupon_data[round_type] == "1" ? "selected":"").">반올림</option>
			<!--option value='2' ".($coupon_data[round_type] == "2" ? "selected":"").">반내림</option-->
			<option value='3' ".($coupon_data[round_type] == "3" ? "selected":"").">내림</option>
			<option value='4' ".($coupon_data[round_type] == "4" ? "selected":"").">올림</option>
			</select>	
			&nbsp;&nbsp;
			
		</td>
	</tr>

	<!--tr bgcolor=#ffffff height=50 style='line-height:150%'>
		<td rowspan=1> <b>쿠폰 사용 제한 설정 <img src='".$required3_path."'></b></td>
		<td>
			상품 구매 합계액이 <input type=text class='textbox' name='total_order_price' value='".$coupon_data[total_order_price]."' style='width:60px;' validation='true' title='쿠폰 사용제한 설정'> 원 이상 상품 구매시
			".CouponRuleSelectBox($mdb,"publish_ix2")." 쿠폰을 발급합니다.
		</td>
	</tr>
	<tr height=1><td colspan=4 class=dot-x></td></tr-->
	<!--tr bgcolor=#ffffff height=50 style='line-height:150%'>
		<td rowspan=3> <b>쿠폰 1회 사용 한도 <img src='".$required3_path."'></b></td>
		<td colspan=2>
			<input type=radio name='coupon_one_use_type' value='1' ".($coupon_data[coupon_one_use_type] == "1" ? "checked":"")." onclick=\"document.getElementById('max_goods_sum_rate').validation=false;document.getElementById('use_reseve_max').validation=true\"> 최대  <input type=text class='textbox' name='use_reseve_max' value='".$coupon_data[use_reseve_max]."' style='width:60px;' id='use_reseve_max' ".($coupon_data[coupon_one_use_type] == "1" ? "validation='true'":"validation='false'")." title='쿠폰 1회 사용 한도'>원 까지만 사용 가능
			<span class='blue small'>* 쿠폰으로 전액 결제 가능하게 하시려면 0원을 입력하시면 됩니다.</span>
		</td>
		<td align=left colspan=1></td>
	</tr>
	<tr height=1><td colspan=4 class=dot-x></td></tr>
	<tr bgcolor=#ffffff height=50 style='line-height:150%'>
		<td colspan=2>
			<input type=radio name='coupon_one_use_type' value='2' ".($coupon_data[coupon_one_use_type] == "2" ? "checked":"")." onclick=\"document.getElementById('max_goods_sum_rate').validation=true;document.getElementById('use_reseve_max').validation=false\">  상품 구매 합계액의  <input type=text class='textbox' name='max_goods_sum_rate' value='".$coupon_data[max_goods_sum_rate]."' style='width:60px;' id='max_goods_sum_rate' ".($coupon_data[coupon_one_use_type] == "2" ? "validation='true'":"validation='false'")." title='쿠폰 1회 사용 한도'>% 까지만 사용가능(최대 100%)<br>
		</td>
	</tr>
	<tr height=1><td colspan=2 class=dot-x></td></tr-->
	</table><br><br>";

/*
$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2''> ".GetTitleNavigation("적립금 정책", "상점관리 > 적립금 정책 ")."</td>
	</tr>
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>적립금 지급 정책</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>적립금 지급 여부 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type='radio' name='reserve_use_yn' value='Y' ".($reserve_data[reserve_use_yn] == "Y" ? "checked":"")."> 사용 <input type='radio' name='reserve_use_yn' value='N' ".($reserve_data[reserve_use_yn] =="N" ? "checked":"")."> 사용안함</td>
	</tr>
	<tr bgcolor='#ffffff' height='50'>
		<td class='input_box_title'> <b>상품적립금 기본 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='line-height:150%'>
			<!--상품 금액의  <input type=text class='textbox' name='goods_reserve_rate' value='".$reserve_data[goods_reserve_rate]."' style='width:60px;' validation='true' title='상품적립금 기본설정'>  % 를 적립합니다.-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H', $reserve_data, "reserve_data")." <br><span class=blue><!--* 신규 상품 등록시 기본으로 적용되며, 상품별로 개별 설정시 본 설정은 적용되지 않습니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'>
			<b>회원가입 적립금 설정 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			<!--신규 회원 가입시  <input type=text class='textbox' name='join_reserve_rate' value='".$reserve_data[join_reserve_rate]."' style='width:60px;' validation='true' title='회원가입 적립금 설정'> 원을 적립합니다.</span--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I' ,$reserve_data ,"reserve_data")."<br>
			<span class=blue><!--* 회원가입 적립금을 지급하시지 않을 경우 0원을 입력 하시면 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='25%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>적립금 사용 정책</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title' rowspan='2'> <b>적립금 사용 제한 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<!--상품 구매 합계액이  <input type=text class='textbox' name='total_order_price' value='".$reserve_data[total_order_price]."' style='width:60px;' validation='true' title='적립금 사용제한 설정'> 원 이상 상품 구매시 사용 가능(제한이 없을경우 0입력)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J',$reserve_data,"reserve_data")."
		</td>
	</tr>
	<tr bgcolor='#ffffff' height=;'0' style='line-height:150%'>
		<td class='input_box_item'>
			<!--보유적립금이<input type=text class='textbox' name='min_reserve_price' value='".$reserve_data[min_reserve_price]."' style='width:60px;' validation='true' title='적립금 사용제한 설정'> 원 이상일때 상품 구매시 사용 가능(제한이 없을경우 0입력)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'L' ,$reserve_data,"reserve_data")."
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title' rowspan='3'>
			<b>적립금 1회 사용 한도  <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			".getTransDiscription(md5($_SERVER["PHP_SELF"]),'M',$reserve_data,"reserve_data")." <br>
			<span class='blue small'><!--* 적립금으로 전액 결제 가능하게 하시려면 0원을 입력하시면 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'Z')."</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=50 style='line-height:150%'>
		<td class='input_box_item'>
			".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O' ,$reserve_data,"reserve_data")." <br>
		</td>
	</tr>
</table>

	";
	*/
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  도메인, 도메인 아이디, 도메인 key 등은 몰스토리에서 발급해드리는 사항이므로 변경이 불가능합니다.<br>
		  <u>상업적인 목적으로 상점을 운영</u>하기 위해서는 정식 <b>도메인 key</b>를 발급 받아 사용하셔야만 상점을 정상적으로 운영하실수 있습니다.
	</td>
</tr>
</table>
";
*/
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

/*
$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' >사이트 쿠폰 정책을 관리합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >쿠폰 정책 수정 즉시 반영되게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품의 구매 금액에 따른 쿠폰을 발급 할 수 있습니다. </td></tr>
	<tr><td valign=top></td><td class='small' style='line-height:120%' >예) 상품 금액이 30000만원 이상일때 쿠폰 발급 설정을 하시면 30000만원 이상인 상품들에 한해서 미리 설정한 쿠폰이 나열 됩니다. 상품 개별로 설정 시에는 마케팅지원 쿠폰발행 페이지를 참고 하시면 됩니다. </td></tr>
</table>
";*/
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
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 쇼핑몰 환경설정 > 쿠폰정책";
$P->title = "쿠폰정책";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>