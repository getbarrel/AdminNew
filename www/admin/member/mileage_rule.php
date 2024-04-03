<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] < 9){
	header("Location:/admin/store/company.add.php");
}

$shmop = new Shared("b2c_mileage_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$reserve_data = $shmop->getObjectForKey("b2c_mileage_rule");
$reserve_data = unserialize(urldecode($reserve_data));

$mileage_term_sdate = $reserve_data[mileage_term_sdate]." ".$reserve_data[mileage_term_sdate_h].":".$reserve_data[mileage_term_sdate_i].":".$reserve_data[mileage_term_sdate_s];
$mileage_term_edate = $reserve_data[mileage_term_edate]." ".$reserve_data[mileage_term_edate_h].":".$reserve_data[mileage_term_edate_i].":".$reserve_data[mileage_term_edate_s];

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "basic_mileage"  || $info_type == "" ) ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='mileage_rule.php?info_type=basic_mileage'>국내 마일리지 설정</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".(($info_type == "global_mileage" ) ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='global_mileage_rule.php?info_type=global_mileage'>해외 마일리지 설정</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	  </tr>
	</table>
";
$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>기본 설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>사용여부 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='mileage_use_yn' id='mileage_use_n' value='N' ".($reserve_data[mileage_use_yn] =="N" ? "checked":"")."> <label for='mileage_use_n'>사용안함</label>
			<input type='radio' name='mileage_use_yn' id='mileage_use_y' value='Y' ".($reserve_data[mileage_use_yn] == "Y" || $reserve_data[mileage_use_yn] == "" ? "checked":"")."> <label for='mileage_use_y'>사용</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>마일리지 노출 명칭 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='text' name='mileage_name' value='".$reserve_data[mileage_name]."'>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>마일리지 노출 단위 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='text' name='mileage_unit_txt' value='".$reserve_data[mileage_unit_txt]."'>
		</td>
	</tr>
	</table>";

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>적립 설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>적립 범위 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='scale' id='scale_a' value='A' ".($reserve_data[scale] == "A" || $reserve_data[scale] == "" ? "checked":"")."> <label for='scale_a'>전체 상품 </label>
		<!--<input type='radio' name='scale' id='scale_h' value='H' ".($reserve_data[scale] =="H" ? "checked":"")."> <label for='scale_h'>본사 상품(셀러 상품 제외)</label>-->
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>적립 기준 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='standard' id='sellprice' value='S' ".($reserve_data[standard] == "S" || $reserve_data[standard] == "" ? "checked":"")."> <label for='sellprice'>판매가(기획, 특별할인 미포함)</label>
		<input type='radio' name='standard' id='discount_price' value='D' ".($reserve_data[standard] =="D" ? "checked":"")."> <label for='discount_price'>할인가(기획, 특별할인 포함)</label>
		<input type='radio' name='standard' id='payment_price' value='P' ".($reserve_data[standard] =="P" ? "checked":"")."> <label for='payment_price'>최종 결제가(기획, 특별할인, 상품쿠폰할인 포함)</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>사용 적립금 제외설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
            <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed; padding:5px;'>
			<col width='100%' />
			<tr height=30>
				<td > 
					<input type='radio' name='excluding_use_reserve' id='excluding_use_reserve_Y' value='Y' ".($reserve_data[excluding_use_reserve] == "Y" ? "checked":"")."> <label for='excluding_use_reserve_Y'>적립시 사용 적립금 차감</label>
                    <input type='radio' name='excluding_use_reserve' id='excluding_use_reserve_N' value='N' ".($reserve_data[excluding_use_reserve] =="N" || $reserve_data[excluding_use_reserve] == "" ? "checked":"")."> <label for='excluding_use_reserve_N'>적립시 사용 적립금 미차감</label> 
				</td>
			</tr>
			<tr>
				<td style='padding:5px;'>
					<div style='background-color: #efefef;padding: 10px;'>
						<img src='../image/emo_3_15.gif' align=absmiddle > <b>사용 적립금 제외설정 가이드</b></br></br>
							<span>-적립금 사용하여 결제 시 사용한 적립금과 실 결제금액의 비율만큼 적립금액을 차감하여 적립하는 기능 입니다. </span></br>
							<span>-배송비 사용 제한 설정에 따라 배송비가 포함된 금액 또는 포함되지 않는 금액을 기준으로 비율 산정하여 계산합니다.</span></br>
							<span>-적립 비율을 제외한 금액은 반올림 처리하여 계산 합니다.</span>
					</div>
				</td>
			</tr>
			</table>            
		</td>
	</tr>
	
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>적립 비율<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed; padding:0px;'>
			<tr height=27>
				<td>
					<input type='radio' name='mileage_info_use' id='use_same' value='S'  ".($reserve_data[mileage_info_use] == "S" || $reserve_data[mileage_info_use] == ""  ? "checked":"")." onclick=\"showInfoArea();\"> <label for='use_same'> 공통 적립</label>
					<input type='radio' name='mileage_info_use' id='use_platform' value='P'  ".($reserve_data[mileage_info_use] == "P"  ? "checked":"")." onclick=\"showInfoArea();\"> <label for='use_platform'> 플랫폼별 차등 적립</label>
					<input type='radio' name='mileage_info_use' id='use_group' value='G'  ".($reserve_data[mileage_info_use] == "G"? "checked":"")." onclick=\"showInfoArea();\"> 
					<label for='use_group'> 회원그룹별 차등 적립</label>
				
				</td>
			</tr>
			<tr height=27>
				<td style='padding:5px;'>
					<div class='same_rate' ".($reserve_data[mileage_info_use] == "S" || $reserve_data[mileage_info_use] == ""  ? "":"style='display:none;'").">
						<table width='50%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
						<col width='20%' />
						<col width='80%' />
						<tr height='30'>
							<td class='s_td'>상품 금액</td>
							<td style='padding-left: 5px;'><input type='text' class='textbox integer numeric' name='mileage_rate[common]' value='".($reserve_data[mileage_info_use] == "S" || $reserve_data[mileage_info_use] == ""  ? $reserve_data[mileage_rate]['common']:0)."' style='width:10%;'> % 적립</td>
						</tr>
						</table>
					</div>

					<div class='plat_rate' ".($reserve_data[mileage_info_use] == "P" ? "":"style='display:none;'").">
						<table width='50%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
						<col width='30%' />
						<col width='70%' />
						<tr height='30'>
							<td class='s_td' style='padding-left: 5px;'>PC 상품금액</td>
							<td style='padding-left: 5px;'><input type=text class='textbox' name='mileage_rate[p]' value='".($reserve_data[mileage_info_use] == "P" ? $reserve_data[mileage_rate][p]:0)."' style='width:60px;' title='웹사이트 상품적립금 기본설정'> % 적립</td>
						</tr>
						<tr height='30'>
							<td class='s_td' style='padding-left: 5px;'>Mobile 상품금액</td>
							<td style='padding-left: 5px;'><input type=text class='textbox' name='mileage_rate[m]' value='".($reserve_data[mileage_info_use] == "P" ? $reserve_data[mileage_rate][p]:0)."' style='width:60px;' title='모바일 상품적립금 기본설정'> % 적립</td>
						</tr>
						</table>
					</div>";

				$sql = "SELECT gi.gp_ix, gi.gp_name 
							FROM shop_groupinfo gi
							where disp='1' and use_reserve_yn='Y' and mall_ix = '20bd04dac38084b2bafdd6d78cd596b1'";
				$db->query($sql);
				$basic_groups = $db->fetchall("object");
				
				$group_detail = "";
				$group_detail2 = "";
				foreach($basic_groups as $k => $v){
					$group_detail .= "<td class='s_td'>".$v[gp_name]."</td>";
					$group_detail2 .= "<td class='list_box_td'><input type='text' class='textbox integer numeric' name='mileage_rate[".$v[gp_ix]."]' value='".($reserve_data[mileage_info_use] == "G" ? $reserve_data[mileage_rate][$v[gp_ix]]:0)."' style='width:40%;'> % </td>";
				}

$Contents01 .="
					<div class='group_rate' ".($reserve_data[mileage_info_use] == "G" ? "":"style='display:none;'").">
						<table width='99%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
						<col width='7%' />
						<col width='7%' />
						<col width='7%' />
						<col width='7%' />
						<col width='7%' />
						<col width='7%' />
						<col width='7%' />
						<col width='7%' />
						<col width='7%' />
						<col width='7%' />
						<col width='7%' />
						<tr align='center' height='30'>".$group_detail."</tr>
						<tr align='center' height='30'>".$group_detail2."</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr height=27>
				<td>
					<input type='radio' name='mileage_term_yn' id='mileage_term_n' value='N' ".($reserve_data[mileage_term_yn] =="N" || $reserve_data[mileage_term_yn] == "" ? "checked":"")."> <label for='mileage_term_n'>기간사용안함</label>
					<input type='radio' name='mileage_term_yn' id='mileage_term_y' value='Y' ".($reserve_data[mileage_term_yn] == "Y" ? "checked":"")."> <label for='mileage_term_y'>기간사용</label>
				</td>
			</tr>
			<tr height=27 id=div_termDate>
				<td>
					".search_date('mileage_term_sdate','mileage_term_edate',$mileage_term_sdate,$mileage_term_edate,'Y',"")."
					/ 추가적립비율 : <input type=text class='textbox number' name='mileage_add' value='".$reserve_data[mileage_add]."' style='width:60px;'>%
				</td>
			</tr>
			<tr>
				<td style='padding:5px;'>
					<div style='background-color: #efefef;padding: 10px;'>
						<img src='../image/emo_3_15.gif' align=absmiddle > <b>적립 비율 가이드</b></br></br>
							<span><font color='red'>-신규상품 등록 시 기본으로 적용되며,</font> 상품별로 개별 적립금 설정 시에는 해당 설정 값이 적용되지 않습니다. </span></br>
							<span>-기간사용 사용시 <font color='red'>등록된 추가적립비율만큼 각 그룹(서퍼)등급에 포함</font>하여 <font color='red'>해당 기간에 상품구매시 반영된 적립비율이 적용</font>됩니다. </span></br>
							<span>(예. 기간사용 <font color='red'>2021년 09월 01일 09시00분00초 ~ 2021년 09월 03일 23시59분59초</font>, 추가적립비율 <font color='red'>10%</font>. 설정된 기간에 상품구매시 상품금액의 <font color='red'>11%(엘로서퍼),  15%(골드서퍼)의 적립금이 적용</font>됩니다.)</span></br>
					</div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'> <b>적립일 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px 0px 5px 5px;'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed; '>
			<tr height=30>
				<td>
					<input type='radio' name='mileage_add_setup' id='mileage_add_setup' value='C'  ".($reserve_data[mileage_add_setup] == "C" || $reserve_data[mileage_add_setup] == ""  ? "checked":"")." > 구매확정일
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'>
			<b>추가 적립<img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			<div class='same_rate'>
				<table width='50%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
				<col width='20%' />
				<col width='80%' />
				<tr height='30'>
					<td class='s_td'>회원가입 시</td>
					<td style='padding-left: 5px;'>
						<input type='radio' name='join_use' id='join_use_n' value='N' ".($reserve_data[join_use] =="N" ? "checked":"")."> <label for='join_use_n'>사용안함</label>
						<input type='radio' name='join_use' id='join_use_y' value='Y' ".($reserve_data[join_use] == "Y" || $reserve_data[join_use] == "" ? "checked":"")."> <label for='join_use_y'>사용</label>
						<input type=text class='textbox' name='join_rate' value='".$reserve_data[join_rate]."' style='width:60px;' title='회원가입 적립금 설정'> 마일리지 적립
					</td>
				</tr>
				</table>
			</div>
		</td>
	</tr>

	</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='20%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>사용 설정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>사용 단위 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<select name='use_unit'>
				<option value='1' ".($reserve_data[use_unit] == "1" ? "selected":"").">1</option>
				<option value='10' ".($reserve_data[use_unit] == "10" ? "selected":"").">10</option>
				<option value='100' ".($reserve_data[use_unit] == "100" ? "selected":"").">100</option>
			</select>
			마일리지 단위로 사용 가능 
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>보유 마일리지 제한 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type=text class='textbox number' name='min_mileage_price' value='".$reserve_data[min_mileage_price]."' style='width:60px;' title='Mileage In-Use제한 설정'>
			마일리지 이상 보유한 경우 사용 가능
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>배송비 사용 제한 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='deliveryprice' id='deliveryprice_n' value='N' ".($reserve_data[deliveryprice] == "N" || $reserve_data[deliveryprice] == "" ? "checked":"")."> <label for='deliveryprice_n'>배송비 결제 불가 </label>
			<input type='radio' name='deliveryprice' id='deliveryprice_y' value='Y' ".($reserve_data[deliveryprice] =="Y" ? "checked":"")."> <label for='deliveryprice_y'>배송비 결제 가능 </label> <span>(* 도서산간 배송비 결제불가)</span>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>상품 구매금액 제한 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed; padding:5px;'>
			<col width='100%' />
			<tr height=30>
				<td style='padding-left:5px;'> 
					- 상품 구매금액 합계액이 <input type=text class='textbox number' name='total_order_price' value='".$reserve_data[total_order_price]."' style='width:60px;' title='Mileage In-Use제한 설정'> 원 이상일 경우 마일리지 사용 가능 
				</td>
			</tr>
			<tr>
				<td style='padding:5px;'>
					<div style='background-color: #efefef;padding: 10px;'>
						<img src='../image/emo_3_15.gif' align=absmiddle > <b>구매금액 제한 가이드</b></br></br>
							<span>-신규상품 등록 시 기본으로 적용되며, 상품별로 개별 적립금 설정 시에는 해당 설정 값이 적용되지 않습니다. </span></br>
							<span>-상품 구매금액 합계 : 배송비 결제 불가 시 상품합계금액, 배송비 결제 가능 시 총결제금액으로 적용됩니다. </span>
					</div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>최대 사용한도 제한 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed; padding:5px;'>
			<col width='100%' />
			<tr height=30>
				<td > 
					<input type=radio name='mileage_one_use_type'  id='once_mileage_one_use_type_3' value='3' ".($reserve_data[mileage_one_use_type] == "3" ? "checked":"")." > 
					<label for='once_mileage_one_use_type_3'>한도 제한 없음 (전액 마일리지 사용 가능) </label>
				</td>
			</tr>
			<tr height=30>
				<td > 
					<input type=radio name='mileage_one_use_type'  id='once_mileage_one_use_type_1' value='1' ".($reserve_data[mileage_one_use_type] == "1" ? "checked":"")." > 
					<label for='once_mileage_one_use_type_1'>정액(원) 제한</label> | 1회 결제 시 최대
					<input type=text class='textbox number' name='use_mileage_max' value='".$reserve_data[use_mileage_max]."' style='width:60px;' id='mileage__one_use_type' title='Mileage 1회 In-Use 한도'> 마일리지까지 사용 가능
				</td>
			</tr>
			<tr height=30>
				<td>
					<input type=radio name='mileage_one_use_type' id='once_mileage_one_use_type_2' value='2' ".($reserve_data[mileage_one_use_type] == "2" ? "checked":"")." >
					<label for='once_mileage_one_use_type_2'>정액(%) 제한</label> | 1회 결제 시 상품구매 합계액의 
					<input type=text class='textbox number' name='max_goods_sum_rate' value='".$reserve_data[max_goods_sum_rate]."' style='width:60px;' id='max_goods_sum_rate' title='Mileage 1회 In-Use 한도'>% 까지 사용 가능
				</td>
			</tr>
			</table>
		</td>
	</tr>
</table>";

////////////////////////////////////////////////////////////////////////////

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='20%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>자동 소멸 설정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff' >
		<td class='input_box_title'> <b>자동 소멸 사용 여부 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:5px;'>
		<input type='hidden' name='date_asc' value='A'>

		<input type='radio' name='auto_extinction' id='auto_extinction_n' value='N' ".($reserve_data[auto_extinction] =="N" ? "checked":"")."> <label for='auto_extinction_n'>사용안함</label>
		<input type='radio' name='auto_extinction' id='auto_extinction_y' value='Y' ".($reserve_data[auto_extinction] == "Y" || $reserve_data[auto_extinction] == "" ? "checked":"")."> <label for='auto_extinction_y'>사용</label>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'> <b>마일리지 자동 소멸 기간 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:10px;'>
			적립일로 부터 &nbsp;&nbsp;
			<select name='cancel_year' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=10; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$reserve_data[cancel_year],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 년  &nbsp;&nbsp;
			<select name='cancel_month' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=12; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$reserve_data[cancel_month],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 개월 지난 미사용 마일리지 자동 소멸
		</td>
	</tr>
</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px;'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='mileage_rule.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>
<input type='hidden' name='act' value='b2c_mileage'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";


  $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .=  HelpBox("마일리지 관리", $help_text, 100);

$Script = "<script language='javascript'>

function showInfoArea(){
	var mileage_info_use = $('input[name=mileage_info_use]:checked').val();

	if(mileage_info_use == 'S'){
		$('.same_rate').show();
		$('.same_rate').find('input').prop('disabled', false);
		$('.plat_rate').hide();
		$('.plat_rate').find('input').prop('disabled', true);
		$('.group_rate').hide();
		$('.group_rate').find('input').prop('disabled', true);
	}else if(mileage_info_use == 'P'){
		$('.same_rate').hide();
		$('.same_rate').find('input').prop('disabled', true);
		$('.plat_rate').show();
		$('.plat_rate').find('input').prop('disabled', false);
		$('.group_rate').hide();
		$('.group_rate').find('input').prop('disabled', true);
	}else if(mileage_info_use == 'G'){
		$('.same_rate').hide();
		$('.same_rate').find('input').prop('disabled', true);
		$('.plat_rate').hide();
		$('.plat_rate').find('input').prop('disabled', true);
		$('.group_rate').show();
		$('.group_rate').find('input').prop('disabled', false);
	}
}

</script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 마일리지 관리 > 마일리지 설정";
$P->title = "마일리지 설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>