<?
include("../class/layout.class");

$db = new Database;
$db2 = new Database;

$sql = 	"SELECT di.*, dg.group_depth , case when dg.group_depth = 1 then dg.group_ix else parent_group_ix end as parent_group_ix
				FROM delivery_info di, delivery_group dg
				where di_ix ='$di_ix' and di.group_ix = dg.group_ix ";

//echo $sql;
$db->query($sql);
$db->fetch();
$addressee_zip = split("-",$db->dt[addressee_zip]);
$certification_no = split("-",$db->dt[addressee_certification_no ]);

if($db->total){
	$act = "update";
}else{
	$act = "insert";
}


$Contents01 = "
	<table width='100%' cellpadding=0cellspacing='0' border='0' align='left' >
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("택배접수", "택배관리 > 택배접수 ")."</td>
	  </tr>
	  <tr >

	    <td align='left' colspan=4>
		";
if($mmode == ""){
$Contents01 .= "
	    <div class='tab' style='margin-bottom:10px;'>
			<table class='s_org_tab'>
			<tr>
				<td>
					<div style='width:100%;float:left;'>
						<table id='tab_01' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='application.php'\">택배접수</td>
								<th class='box_03'></th>
							</tr>
						</table>
						<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='application_excel.php'\">택배송장등록</td>
								<th class='box_03'></th>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			</table>
		</div>";

}
$Contents01 .= "
				<form name='div_form' action='application.act.php' method='post' onsubmit='return CheckForm(this)' target='act' ><!---->
				<input name='act' type='hidden' value='$act'>
				<input name='di_ix' type='hidden' value='$di_ix'>
					<table width=100% cellpadding=5 cellspacing=0 border=0 style='clear:both; margin-bottom:3px;'>
					<tr bgcolor='#efefef'>
						<td>
							<div style='padding:5px 5px 5px 15px;'>
								<IMG id=SM114641I src='../images/dot_org.gif' border=0 align=absmiddle>&nbsp;<b >Shipper (보내는 사람)</b>
							</div>
						</td>
					</tr>
					</table>
					<table width=100% cellpadding=0 cellspacing=0 border=0 class='input_table_box'>
					";
if($di_ix){
$Contents01 .= "
				  <tr bgcolor=#ffffff height='50'>
				    <td class='input_box_title'><b>  접수번호 : </b></td>
				    <td class='input_box_item' colspan=3 >
				    	<b style='font-size:20px;'>".$db->dt[application_no]."</b>

				    </td>
				  </tr>
				  	";
}
$Contents01 .= "
					<tr bgcolor=#ffffff >
				    <td class='input_box_title'>  Group : </td>
				    <td class='input_box_item' colspan=3  style='padding:10px 0 10px 5px;;'>
				    	".getGroupInfoSelect('parent_group_ix', '1 차그룹',$db->dt[parent_group_ix], $db->dt[parent_group_ix], 1, " onChange=\"loadCampaignGroup(this,'group_ix')\" ")."
				    	".getGroupInfoSelect('group_ix', '2 차그룹',$db->dt[parent_group_ix], $db->dt[group_ix], 2)."<br>
							<div class=small style='margin-top:4px;'>택배 관리 그룹 : 택배 성격에 맞게 분류해서 관리 하실수 있습니다. 선택하지 않을시 기본그룹으로 자동 저장됩니다.</div>
				    </td>
				  </tr>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title' style='padding:10px 0 10px 5px;'>
					<input type=radio name='name_type' id='name_type_n'  value='N' onclick=\"$('#name_area').show();$('#company_name_area').hide();\" ".($db->dt["name_type"] == "N" ? "checked":"")." ><label for='name_type_n'>Name</label><br>
					<input type=radio name='name_type' id='name_type_c' value='C' onclick=\"$('#name_area').hide();$('#company_name_area').show();\" ".($db->dt["name_type"] == "C" ? "checked":"")."><label for='name_type_c'>Company Name</label>
					</td>
					<td class='input_box_item' colspan=3>
					<div id='name_area'>
						First Name : <input type='text' class='textbox' name='shipper_first_name' value='".$db->dt['shipper_first_name']."' title='First Name' style='width:124px;ime-mode:ime-mode:inactive'>
						Last Name : <input type='text' class='textbox' name='shipper_last_name' value='".$db->dt['shipper_last_name']."' title='Name' style='width:100px;ime-mode:inactive'>
					</div>
					<div id='company_name_area' style='display:none;'>
						<input type='text' class='textbox'  name='shipper_company_name' value='".$db->dt['shipper_company_name']."' title='Company Name' style='width:224px;ime-mode:inactive'>
					</div>

					<!--input type=text class='textbox' name='shipper_name' value='".$db->dt[shipper_name]."' style='width:200px;'--> <span class=small></span></td>

				  </tr>
				  <tr bgcolor=#ffffff >
				     <td class='input_box_title' ><b> Tel : </b></td><td class='input_box_item'><input type=text class='textbox' name='shipper_phone' value='".$db->dt[shipper_phone]."' style='width:200px;'> <span class=small></span></td>
					<td class='input_box_title'><b> Mobile : </b></td><td class='input_box_item'><input type=text class='textbox' name='shipper_mobile' value='".$db->dt[shipper_mobile]."' style='width:200px;'> <span class=small></span></td>

				  </tr>
				  <tr bgcolor=#ffffff >
					 <td class='input_box_title'><b> Email : </b></td><td class='input_box_item' colspan=3><input type=text class='textbox' name='shipper_email' value='".$db->dt[shipper_email]."' style='width:200px;'> <span class=small></span></td>
				    <!--td><b> FAX : </b></td><td><input type=text class='textbox' name='shipper_fax' value='".$db->dt[shipper_fax]."' style='width:200px;'> <span class=small></span></td-->

				  </tr>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'>Address : </td>
				    <td class='input_box_item' colspan=3>
				    	<input type=text class='textbox' name='shipper_address1' value='".$db->dt[shipper_address1]."' style='width:430px;'>
				    	<input type=text class='textbox' name='shipper_address2' value='".$db->dt[shipper_address2]."' style='width:430px;margin:2px 0px;'> <span class=small></span>
				    </td>
				  </tr>
				  </table>
				<table width=100% cellpadding=5 cellspacing=0 border=0 style='padding-top:50px; padding-bottom:3px;'>
					<tr bgcolor='#efefef'>
						<td>
							<div style='padding:5px 5px 5px 15px;'>
								<IMG id=SM114641I src='../images/dot_org.gif' border=0 align=absmiddle>&nbsp;<b >받는사람</b>
							</div>
						</td>
					</tr>
				</table>
				<table width=100% cellpadding=0 cellspacing=0 border=0 class='input_table_box'>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'> 받는사람 성명 : </td>
				    <td class='input_box_item' colspan=3><input type=text class='textbox' name='addressee_name' value='".$db->dt[addressee_name]."' style='width:200px;'> <span class=small></span></td>
				    <!--td><b> 받는사람 영문성명 : </b></td>
				    <td><input type=text class='textbox' name='addressee_eng_name' value='".$db->dt[addressee_eng_name]."' style='width:200px;'> <span class=small></span></td-->

				  </tr>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 받는사람 전화번호 : </b></td>
				    <td class='input_box_item'><input type=text class='textbox' name='addressee_phone' value='".$db->dt[addressee_phone]."' style='width:200px;'> <span class=small></span></td>
				    <td class='input_box_title'>받는사람 핸드폰 : </td>
				    <td class='input_box_item'><input type=text class='textbox' name='addressee_mobile' value='".$db->dt[addressee_mobile]."' style='width:200px;'> <span class=small></span></td>

				  </tr>
				  <!--tr bgcolor=#ffffff >
				  	<td class='input_box_title'> 받는사람 이메일 :</td>
				    <td class='input_box_item'><input type=text class='textbox' name='addressee_email' value='".$db->dt[addressee_email]."' style='width:200px;'> <span class=small></span></td>
				    <td class='input_box_title'>주민/사업자번호 :</td>
				    <td class='input_box_item'><input type=text class='textbox' name='certification_no' value='".$db->dt[certification_no]."' style='width:200px;'> <span class=small></span></td>

				  </tr>
				  <tr><td colspan=4 class=dot-x></td></tr-->
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title' > 배송주소 : </td>
				    <td class='input_box_item' style='padding:5px 0 5px 5px;' colspan=3>
				    	<table cellpadding=0 cellspacing=0 >
				    	<tr>
				    		<td>
					    	<input type='text' name='addressee_zip1' size='3' maxlength='3' readonly value='".$addressee_zip[0]."' class='textbox' align=absmiddle > -
					    	<input type='text' name='addressee_zip2' size='3' maxlength='3' readonly value='".$addressee_zip[1]."' class='textbox' align=absmiddle >
					    	</td>
					    	<td >
				    		<input type=button value='주소검색' class='button' align=absmiddle onClick=\"zipcode('2')\" style='maring:0px 0 0 0'><br>
								</td>
							</tr>
							</table>
				    	<input type=text class='textbox' name='addressee_address1' value='".$db->dt[addressee_address1]."' style='width:430px;margin:2px 0px;'>
				    	<input type=text class='textbox' name='addressee_address2' value='".$db->dt[addressee_address2]."' style='width:400px;'> <span class=small>상세주소 </span>
				    </td>
				  </tr>

				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'> 배송 메모 : </td>
				    <td class='input_box_item' style='padding:10px 0 10px 5px;;' colspan=3>
						<textarea  name='memo'  style='width:90%;height:70px;'>".$db->dt[order_memo]."</textarea>
					</td>

				  </tr>

				  </table>
			</table>
						<table width='100%' border='0' cellpadding='5' cellspacing='0' style='padding:50px 0 3px 0;'>
							<tr bgcolor='#efefef'>
								<td>
									<div style='padding:5px 5px 5px 15px;'>
										<IMG id=SM114641I src='../images/dot_org.gif' border=0 align=absmiddle>&nbsp;<b >배송품목-영문</b>
									</div>
								</td>
							</tr>
						</table>
						<table width='100%' border='0' cellpadding='0' cellspacing='0' class='list_table_box'>
							<tr height='25' bgcolor='#efefef' align=center>
								<td width='5%' class='s_td'><b>No</b></td>
								<td width='5%' class='m_td' ><b>선택</b></td>
								<td width='10%' class='m_td' ><b>Item div</b></td>
								<td width='20%' class='m_td' ><b>Item Name</b></td>
								<td width='10%' class='m_td'><b>Brand</b></td>
								<td width='15%' class='m_td'><b>단가</b></td>
								<td width='10%' class='m_td'><b>수량</b></td>
								<td width='10%' class='e_td'><b>합계</b></td>
							</tr>";


		$sql = "SELECT * from delivery_detail_info WHERE di_ix = '".$di_ix."' order by ddi_ix asc ";


	$db2->query($sql);


if(!$db2->total){
	for($i=0;$i < 20;$i++){
	$Contents01 .= "
							<tr height='33' align='center'>
								<td class='list_box_td list_bg_gray' align=center>".($i+1)."</td>
								<td class='list_box_td'><input type='checkbox' name='goodsinfo[".$i."][select]' value='1'  ></td>
								<td class='list_box_td list_bg_gray'><select name='goodsinfo[".$i."][item_div]' value='' id='item_div_".$i."' class='item_div' onchange=\"CheckItemDiv()\">
								<option value='' selected='selected'>물품분류</option>
								<option value='1' ssno_bool=false>의류</option>
								<option value='2' ssno_bool=true>식품류</option>
								<option value='3' ssno_bool=true>약품류</option>
								<option value='4' ssno_bool=true>비타민류</option>
								<option value='5' ssno_bool=false>장난감류</option>
								<option value='6' ssno_bool=false>가구류</option>
								<option value='7' ssno_bool=false>도서</option>
								<option value='8' ssno_bool=false>전자제품</option>
								<option value='9' ssno_bool=false>문서</option>
								<option value='10' ssno_bool=false>가방/지갑</option>
								<option value='11' ssno_bool=false>액세서리</option>
								<option value='12' ssno_bool=false>보석류</option>
								<option value='13' ssno_bool=false>기타</option>
							</select></td>
								<td class='list_box_td'><input type='text' class='textbox' name='goodsinfo[".$i."][item_name]' value='' style='width:90%;' ></td>
								<td class='list_box_td list_bg_gray' align=center><input type='text' class='textbox' name='goodsinfo[".$i."][brand]' value='' style='width:80px;' ></td>
								<td class='list_box_td' align=center>
									<input type='text' class='textbox' name='goodsinfo[".$i."][price]' id='price_".$i."' value='' style='width:80px;' onkeyup='CalcuRateSum(".$i.")'>
									<select name='goodsinfo[".$i."][unit]'>
										<option value='USD'>USD</option>
										<option value='KRW'>KRW</option>
									</select>
								</td>
								<td class='list_box_td list_bg_gray' align='center'><input type='text' class='textbox' name='goodsinfo[".$i."][amount]' id='amount_".$i."' value='1' style='width:80px;' onkeyup='CalcuRateSum(".$i.")'></td>
								<td class='list_box_td'><input type='text' class='textbox' name='goodsinfo[".$i."][sum]' value='0' id='sum_".$i."' class='sum' style='width:88%;text-align:right' readonly></td>
							</tr>";
	}
}else{
	for($i = 0; $i < $db2->total; $i++)
	{
		$db2->fetch($i);
		$shipping_sum = $shipping_sum + $db2->dt[amount]*$db2->dt[price];
			$Contents01 .= "
						<tr height='33' align='center'>
							<td align=center>".($i+1)."</td>
							<td ><input type='checkbox' name='goodsinfo[".$i."][select]' value='goodsinfo[".$i."][ddi_ix]' checked ></td>
							<td><select name='goodsinfo[".$i."][item_div]' value='' id='item_div_".$i."' class='item_div' onchange=\"CheckItemDiv()\">
								<option value='' selected='selected'>물품분류</option>
								<option value='1' ".($db2->dt[item_div] == "1" ? "selected":"")." ssno_bool=false>의류</option>
								<option value='2' ".($db2->dt[item_div] == "2" ? "selected":"")." ssno_bool=true>식품류</option>
								<option value='3' ".($db2->dt[item_div] == "3" ? "selected":"")." ssno_bool=true>약품류</option>
								<option value='4' ".($db2->dt[item_div] == "4" ? "selected":"")." ssno_bool=true>비타민류</option>
								<option value='5' ".($db2->dt[item_div] == "5" ? "selected":"")." ssno_bool=false>장난감류</option>
								<option value='6' ".($db2->dt[item_div] == "6" ? "selected":"")." ssno_bool=false>가구류</option>
								<option value='7' ".($db2->dt[item_div] == "7" ? "selected":"")." ssno_bool=false>도서</option>
								<option value='8' ".($db2->dt[item_div] == "8" ? "selected":"")." ssno_bool=false>전자제품</option>
								<option value='9' ".($db2->dt[item_div] == "9" ? "selected":"")." ssno_bool=false>문서</option>
								<option value='10' ".($db2->dt[item_div] == "10" ? "selected":"")." ssno_bool=false>가방/지갑</option>
								<option value='11' ".($db2->dt[item_div] == "11" ? "selected":"")." ssno_bool=false>액세서리</option>
								<option value='12' ".($db2->dt[item_div] == "12" ? "selected":"")." ssno_bool=false>보석류</option>
								<option value='13' ".($db2->dt[item_div] == "13" ? "selected":"")." ssno_bool=false>기타</option>
							</select></td>
							<td><input type='text' name='goodsinfo[".$i."][item_name]' value='".$db2->dt[item_name]."' style='width:280px;' ></td>
							<td align=center><input type='text' name='goodsinfo[".$i."][brand]' value='".$db2->dt[brand]."' style='width:80px;' ></td>
							<td align=center>
								<input type='text' name='goodsinfo[".$i."][price]' id='price_".$i."' value='".$db2->dt[price]."' style='width:80px;' onkeyup='CalcuRateSum(".$i.")'>
								<select name='goodsinfo[".$i."][unit]'>
									<option value='USD' ".($db2->dt[unit] == "USD" ? "selected":"").">USD</option>
									<option value='KRW' ".($db2->dt[unit] == "KRW" ? "selected":"").">KRW</option>
								</select>
							</td>
							<td align='center'><input type='text' name='goodsinfo[".$i."][amount]' id='amount_".$i."' value='".$db2->dt[amount]."' style='width:80px;'onkeyup='CalcuRateSum(".$i.")' ></td>
							<td class='td_text td_underline'><input type='text' name='goodsinfo[".$i."][sum]' value='".($db2->dt[price]*$db2->dt[amount])."' id='sum_".$i."' class='sum' style='width:98%;text-align:right' readonly></td>
						</tr>
						<tr><td colspan=11 class=dot-x></td></tr>";
	}
	for($i=$i;$i < 20;$i++){
	$Contents01 .= "
						<tr height='33' align='center'>
							<td align=center>".($i+1)."</td>
							<td ><input type='checkbox' name='goodsinfo[".$i."][select]' value='Y'  ></td>
							<td><select name='goodsinfo[".$i."][item_div]' value='' id='item_div_".$i."' class='item_div' onchange=\"CheckItemDiv()\">
								<option value='' selected='selected'>물품분류</option>
								<option value='1' ssno_bool=false>의류</option>
								<option value='2' ssno_bool=true>식품류</option>
								<option value='3' ssno_bool=true>약품류</option>
								<option value='4' ssno_bool=true>비타민류</option>
								<option value='5' ssno_bool=false>장난감류</option>
								<option value='6' ssno_bool=false>가구류</option>
								<option value='7' ssno_bool=false>도서</option>
								<option value='8' ssno_bool=false>전자제품</option>
								<option value='9' ssno_bool=false>문서</option>
								<option value='10' ssno_bool=false>가방/지갑</option>
								<option value='11' ssno_bool=false>액세서리</option>
								<option value='12' ssno_bool=false>보석류</option>
								<option value='13' ssno_bool=false>기타</option>
							</select></td>
							<td><input type='text' name='goodsinfo[".$i."][item_name]' value='' style='width:280px;' ></td>
							<td align=center><input type='text' name='goodsinfo[".$i."][brand]' value='' style='width:80px;' ></td>
							<td align=center>
								<input type='text' name='goodsinfo[".$i."][price]' id='price_".$i."' value='' style='width:80px;' onkeyup='CalcuRateSum(".$i.")'>
								<select name='goodsinfo[".$i."][unit]'>
									<option value='USD'>USD</option>
									<option value='KRW'>KRW</option>
								</select>
							</td>
							<td align='center'><input type='text' name='goodsinfo[".$i."][amount]' id='amount_".$i."' value='1' style='width:80px;' onkeyup='CalcuRateSum(".$i.")'></td>
							<td class='td_text td_underline'><input type='text' name='goodsinfo[".$i."][sum]' value='' id='sum_".$i."' class='sum' style='width:98%;text-align:right' readonly></td>
						</tr>";
	}
}
$Contents01 = $Contents01."
						<tr>
							<td colspan='6' style='line-height:140%; padding:10px 0 10px 5px'>* 총금액이 당일 환율로 15만원이 넘을경우 추가 세금이 붙습니다.<br> * 총금액은 : <b>물품가격 + 배송비</b>의 함계 입니다.</td>
							<td style='font-weight:bold;text-align:center;' class='list_box_td list_bg_gray'>총합계</td>
							<td class='list_box_td'><input type='text' class='textbox' name='shipping_sum' value='".$shipping_sum."' id='shipping_sum' style='width:88%;text-align:center' ></td>
						</tr>
						</table>
						<table class='input_table_box' cellpadding='0' cellspacing='0' border='0' style='margin-top:30px;' width=100%;>
							<tr height='25px'>
								<td width='180' class='input_box_title'>* 총무게 (파운드)</td>
								<td class='input_box_item'>
									<input type='text' class='textbox' name='weight'  value='".$db->dt[weight]."' style='width:100px;text-align:right;'> LB
								</td>
							</tr>
							<tr height='25px'>
								<td width='180' class='input_box_title'><b>* 배송비</b></td>
								<td class='input_box_item'>
									<input type='text' class='textbox' name='delivery_fee'  value='".$db->dt[delivery_fee]."' style='width:100px;text-align:right;'> USD
									<input type='checkbox' class='textbox' name='delivery_payment_yn' id='delivery_payment_yn' value='1' style='text-align:right;' ".($db->dt[delivery_payment_yn] == "1" ? "checked":"")."><label for='delivery_payment_yn'>결제완료</label>
								</td>
							</tr>
							<tr id='certification_no_area' ><!--style='".($shipping_sum > 120 ? "display:block;":"display:none;")."'-->
								<td width='180'class='input_box_title'>* 주민 등록번호</td>
									<td class='input_box_item'><input type='text' class='textbox' name='certification_no1' id='certification_no1' value='".$certification_no[0]."' title='주민 등록번호' style='width:105px;'> - <input type='text' class='textbox' name='certification_no2' id='certification_no2' value='".$certification_no[1]."' title='주민 등록번호' style='width:105px;'></td>
							</tr>
							<tr height='25px'>
								<td width='180' class='input_box_title'>* 배송타입</td>
								<td class='input_box_item'>
									<input type='radio' name='delivery_type' id='delivery_type_1' value='1' onclick=\"$('#delivery_service_addinfo').css('display','')\" ".($db->dt[delivery_type] == '1' ? "checked":"" )."><label for='delivery_type_1'>배송대행</label>
									<input type='radio' name='delivery_type' id='delivery_type_2' value='2' onclick=\"$('#delivery_service_addinfo').css('display','none')\" ".($db->dt[delivery_type] == '2' ? "checked":"" )."><label for='delivery_type_2'>일반택배</label>
								</td>
							</tr>
							<tr ".($db->dt[delivery_type] == '1' ? "":"style='display:none;'" )." id='delivery_service_addinfo'>
								<td width='180' class='input_box_title'>* 배송대행 추가정보</td>
								<td class='input_box_item' >
									<table cellpadding='0' cellspacing=0 border=0 width='100%' style='padding:10px 0;'>
									<tr>
										<td colspan=2 style='line-height:140%;padding-top:10px'>
										<b>배송대행 고책 숙지사항</b><br>
										사이트에서 주문한 물품에 대한 배송대행 요청시<br>
										아래주소로 주문하셔야 합니다.<br>
										1410 Sullyfield Circle #340 <br>
										Box _________  <br>
										chantilly VA 20151<br>
										Box Number에는 고객님의 회원 아이디 (우체국 택배 아이디)를 넣어 주십시오<br>
										아이디 미 입력수 box가 분실 될 수 있습니다.<br>
										예) 14101 Sullyfield Circle #340 Box <u>Mrgentle</u> chantilly VA 20151
										</td>
									</tr>
									<tr height=25><td>주문자명(영문)  : </td><td><input type='text' class='textbox' name='order_name'  value='".$db->dt[order_name]."' style='width:150px;text-align:left;'></td></tr>
									<tr height=25><td>주문한 URL  : </td><td><input type='text' class='textbox' name='order_url'  value='".$db->dt[order_url]."' style='width:200px;'></td></tr>
									<tr height=25><td>Order Confirm #  : </td><td><input type='text' class='textbox' name='order_confirm_no'  value='".$db->dt[order_confirm_no]."' style='width:200px;'></td></tr>
									<tr height=25><td>Tracking #  : </td><td><input type='text' class='textbox' name='tracking_no'  value='".$db->dt[tracking_no]."' style='width:200px;'> (optional)</td></tr>
									<tr height=25><td>note / 메모 : </td><td><input type='text' class='textbox' name='order_memo'  value='".$db->dt[order_memo]."' style='width:400px;text-align:left;'> </td></tr>
									</table>
								</td>
							</tr>
							<tr bgcolor=#ffffff height='30'>
								<td class='input_box_title'>* <b>  접수상태 : </b></td>
								<td class='input_box_item' >
									<select name='status'>
										<option value='AC' ".($db->dt[status] == "AC" ? "selected":"").">고객 신청완료</option>
										<option value='TC' ".($db->dt[status] == "TC" ? "selected":"").">관리자 접수완료</option>
										<option value='IC' ".($db->dt[status] == "IC" ? "selected":"").">출고완료</option>
										<option value='AD' ".($db->dt[status] == "AD" ? "selected":"").">접수취소</option>
									</select>

								</td>
							  </tr>
						 </table>
						<table cellpadding='0' cellspacing='0' border='0' width='100%'>
						<tr bgcolor=#ffffff >
							<td align=center style='padding-top:30px;'>
								<input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' >
							</td>
						</tr>
						</table>
				  </form>

	    </td>
	  </tr>

	  </table>";




$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";

$Contents = $Contents."</table >";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >택배 접수를 원활히 관리하기 위해서는 택배 관리그룹을 선택하셔야 합니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>핸드폰 번호</u>와 <u>이메일주소</u>는  메일링이나 SMS 발송시 사용되므로 정확히 입력하여 주시기 바랍니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >부가적으로 회사정보등을 입력해서 관리 하실 수 있습니다.</td></tr>
	</table>
	";


$help_text = HelpBox("택배접수", $help_text);

$Contents = $Contents.$help_text."<br><br>";
 $Script = "
 <style>
table .td_underline	{border-bottom:solid 1px #e1dfdf;}
 </style>
 <script  id='dynamic'></script>
 <script language='javascript'>
var item_div_special_cnt = 0;
function CheckItemDiv(){


	$('.item_div').each(function(){
		if($(this).val() != ''){
			if($('#'+$(this).attr('id')+' option:selected').attr('ssno_bool') == 'true'){
				item_div_special_cnt++;
			}

		}
	});
	//alert(checked_cnt);
	/*
	if(item_div_special_cnt > 0){
		$('#certification_no_area').show();
	}else{
		$('#certification_no_area').hide();
	}
	*/

}


function CalcuRateSum(line_num){
	var price = $('#price_'+line_num).val();
	var amount = $('#amount_'+line_num).val();
	//alert(price+':::'+amount);
	$('#sum_'+line_num).val(price*amount);

	var shipping_sum = 0;
	$('.sum').each(function(){
		if(this.value != ''){
			shipping_sum += parseInt(this.value);
		}
	});

	$('#shipping_sum').val(shipping_sum);
	/*
	if(shipping_sum >= 120){
		//alert(1);
		$('#certification_no_area').show();
	}else{
		$('#certification_no_area').hide();
	}
	*/
}

function AutoFill(frm){
//alert(frm);
	for(i=0;i < frm.elements.length;i++){
		//alert(frm.elements.name);
			if(frm.elements[i].type == 'text'){
				frm.elements[i].value = frm.elements[i].name;
			}
		/*
		if(!CheckForm(frm.elements[i])){
			return false;
		}
		*/
	}

}

function AutoFillText(frm){
//alert(frm);
	frm.shipper_name.value = 'hun shick shin';
	frm.shipper_phone.value = '02-2058-2214';
	frm.shipper_mobile.value = '010-5484-5455';
	frm.shipper_fax.value = '02-2058-2215';
	frm.shipper_email.value = 'tech@forbiz.co.kr';
	frm.shipper_address1.value = '서울시 서초구 양재동 16-3 번지 ';
	frm.shipper_address2.value = '윤화빌딩 6층 ';

	frm.addressee_name.value = '신훈식';
	frm.addressee_eng_name.value = '신훈식';
	frm.addressee_phone.value = '02-2058-2214';
	frm.addressee_mobile.value = '010-5484-5455';
	frm.certification_no.value = '750511-1351417';
	frm.addressee_email.value = 'tech@forbiz.co.kr';
	frm.addressee_address1.value = '서울시 서초구 양재동 16-3 번지 ';
	frm.addressee_address2.value = '윤화빌딩 6층 ';
	frm.addressee_zip1.value = '123';
	frm.addressee_zip2.value = '456';
}

 function updateBankInfo(div_ix,div_name,disp){
 	var frm = document.div_form;

 	frm.act.value = 'update';
 	frm.div_ix.value = div_ix;
 	frm.div_name.value = div_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

function CheckForm(frm){

	if(frm.parent_group_ix.value == ''){
	 		alert('1차 그룹은 반드시 선택하셔야 합니다.');
	 		return false;
 	}

	if(frm.shipper_name.value.length < 1){
		alert('이름을 입력해주세요');
		frm.shipper_name.focus();
		return false;
	}

	if(frm.shipper_mobile.value.length < 1){
		alert('핸드폰을 입력해주세요');
		frm.shipper_mobile.focus();
		return false;
	}

	if(frm.email.value.length < 1){
		alert('이메일을 입력해주세요');
		frm.email.focus();
		return false;
	}else{
		var PT_email = /[a-z0-9_]{2,}@[a-z0-9-]{2,}\.[a-z0-9]{2,}/i;  // 이메일
		if (!PT_email.test(frm.email.value)){
			alert('이메일 형식이 아닙니다. 확인후 다시 시도해주세요');
			frm.email.focus();
			return false;
		}
	}

	return true;
}

function deleteMaillingInfo(act, ci_ix){
 	if(confirm('해당메일링 리스트를  정말로 삭제하시겠습니까?')){
 		var frm = document.div_form;
 		frm.act.value = act;
 		frm.ci_ix.value = ci_ix;
 		frm.submit();
 	}
}

function show_mailling_info(obj){
	if(obj.style.display == 'block'){
		obj.style.display = 'none';
	}else{
		obj.style.display = 'block';
	}
}

function loadCampaignGroup(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var depth = sel.depth;
	//document.write('campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	dynamic.src = 'campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';

}

function zipcode(id)
{
	var zip = window.open('./zipcode.php?type='+id,'','width=440,height=350,scrollbars=yes,status=no');
}

$(function() {
	$('.price').numeric();
	$('.price').css('ime-mode', 'disabled');
	$('.amount').numeric();
	$('.amount').css('ime-mode', 'disabled');
});
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = delivery_menu();
	$P->Navigation = "배송대행서비스 > 택배관리 > 택배접수";
	$P->strContents = $Contents;
	$P->NaviTitle = "택배접수";

	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = delivery_menu();
	$P->Navigation = "배송대행서비스 > 택배관리 > 택배접수";
	$P->title = "택배접수하기";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $depth=1, $property=""){
	$mdb = new Database;
	if($depth == 1){
		$sql = 	"SELECT abg.*
				FROM delivery_group abg
				where group_depth = '$depth'
				group by group_ix ";
	}else if($depth == 2){
		$sql = 	"SELECT abg.*
				FROM delivery_group abg
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix'
				group by group_ix ";
	}
	//echo $sql;
	$mdb->query($sql);

	$mstring = "<select name='$obj_id' id='$obj_id' $property>";
	$mstring .= "<option value=''>$obj_txt</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[group_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
			}else{
				if($selected == "" && $i == 0){
					$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
				}
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}
?>