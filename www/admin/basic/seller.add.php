<?
include("../class/layout.class");
include_once("../store/md.lib.php");
include("../webedit/webedit.lib.php");
include ("../basic/company.lib.php");
@include ("../inventory/inventory.lib.php");
include("../buyingservice/buying.lib.php");

if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "거래처 등록";
}else{
	$menu_name = "거래처 등록";
}

if($info_type == ""){
	$info_type = "basic";
}

$db = new Database;
$db2 = new Database;
$cdb = new Database;
$indb = new Database;

if($company_id == ""){
	$act = "insert";
}else{
	if($info_type == "basic"){

		$sql = "SELECT 
					ccd.*,sd.*, cr.*,
					ccw.commercial_disp,ccw.ca_country,ccw.ca_code,ccw.sc_code,ccw.floor,ccw.line,ccw.no,
					ccw.tel as ws_tel ,ccw.charge_phone as ws_charge_phone,ccw.kakao_phone,ccw.kakao_id,ccw.facebook,ccw.twitter,ccw.qq,ccw.wechat
				FROM 
					".TBL_COMMON_COMPANY_DETAIL." as  ccd 
					inner join ".TBL_COMMON_SELLER_DETAIL." as sd on (ccd.company_id = sd.company_id)
					left join common_company_wholesale ccw on (ccd.company_id=ccw.company_id) 
					left join ".TBL_COMMON_COMPANY_RELATION."	as cr on (ccd.company_id = cr.company_id)
				where 
					ccd.company_id = '".$company_id."'";
			
		$db->query($sql);
		$seller_array = $db->fetch();

		$com_phone = explode("-",$db->dt[com_phone]);
		$com_fax = explode("-",$db->dt[com_fax]);
		$com_mobile = explode("-",$db->dt[com_mobile]);
		
		$com_group = $db->dt[com_group];	//부서그룹
		$department = $db->dt[department];	//부서
		$duty = $db->dt[duty];	//직책
		$position = $db->dt[position];	//직위

		if(strpos($db->dt[com_number],'-')){
			$com_number = explode("-",$db->dt[com_number]);		//사업자 번호 ERP에서 받는건 - 구분이 없으므로 없을경우 앞3자리 가운데 2자리 나머지로 처리해줘야함
		}else{
			$com_number[0] = substr($db->dt[com_number],0,3);		//사업자 번호 ERP에서 받는건 - 구분이 없으므로 없을경우 앞3자리 가운데 2자리 나머지로 처리해줘야함
			$com_number[1] = substr($db->dt[com_number],3,2);
			$com_number[2] = substr($db->dt[com_number],5,5);
		}
		$corporate_number = explode("-",$db->dt[corporate_number]);

		$seller_type	= $db->dt[seller_type];
		$seller_type_array = explode("|",$seller_type);
		
		if(is_array($seller_type_array)){
			$checked_1 = (in_array('1',$seller_type_array) ? "checked":"");
		}
		if(is_array($seller_type_array)){
			$checked_2 = (in_array('2',$seller_type_array) ? "checked":"");
		}
		if(is_array($seller_type_array)){
			$checked_3 = (in_array('3',$seller_type_array) ? "checked":"");
		}
		if(is_array($seller_type_array)){
			$checked_4 = (in_array('4',$seller_type_array) ? "checked":"");
		}
		if(is_array($seller_type_array)){
			$checked_5 = (in_array('5',$seller_type_array) ? "checked":"");
		}

		if(count($seller_array) >= 1){
			$act = "update";
		}else{
			$act = "insert";
		}
		$cid2 =$db->dt[relation_code]; 

	}else if($info_type == "order_info"){

		$sql = "SELECT 
					* 
				FROM 
					".TBL_COMMON_COMPANY_DETAIL." as  ccd 
					inner join ".TBL_COMMON_SELLER_DETAIL." as sd on (ccd.company_id = sd.company_id)	
				where 
					ccd.company_id = '".$company_id."'";
		$db->query($sql);
		$seller_array = $db->fetch();
		$act = "update";

	}else if($info_type == "seller_info"){
		$sql = "SELECT * FROM ".TBL_COMMON_COMPANY_FILE." ccd where ccd.company_id = '".$company_id."'";
		$row = $db->query($sql);
		$seller_info_array = $db->fetchall();
		
		if(count($seller_info_array) >= 1){
			$act = "update";
		}else{
			$act = "insert";
		}
	}else if($info_type == "delivery_info"){
			$sql = "SELECT * FROM ".TBL_COMMON_POS_INFO." ccd where ccd.company_id = '".$company_id."' ";
		//echo $sql;
		$db->query($sql);
		$pos_info = $db->fetchall();
		
		$sql = "SELECT * FROM ".TBL_COMMON_POS_DETAIL." ccd where ccd.company_id = '".$company_id."' order by pos_detail_ix asc";
		//echo $sql;
		$db->query($sql);
		$pos_detail = $db->fetchall();
		
		if(count($pos_info) >= 1 and count($pos_detail) >= 1 ){
			$act = "update";
		}else{
			$act = "insert";
		}
	}else {

		if($info_type == "factory_info"){
		$type = "F";
		}else if($info_type == "exchange_info"){
		$type = "E";
		}else if($info_type == "visit_info"){
		$type = "V";
		}

		if($_GET[addr_ix]){
			$where = "  and addr_ix = '".$_GET[addr_ix]."' "; 

			$sql = "select
						*
					from
						shop_delivery_address
					where
						delivery_type = '".$type."'
						and company_id = '".$company_id."'
						$where";
			$db->query($sql);
			$db->fetch();

			$addr_phone = explode("-",$db->dt[addr_phone]);
			$addr_mobile = explode("-",$db->dt[addr_mobile]);

			$act = "update";
		}else{
			$act = "insert";
		}

		$sql = "
			select
				*
			from
				shop_delivery_address
			where
				delivery_type = '".$type."'
				and company_id = '".$company_id."'";
		$db2->query($sql);
		$delivery_array = $db2->fetchall();

	}
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='30%' />
	<tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "거래처 관리 > $menu_name")."</td>
	</tr>";
	if($_SESSION["admininfo"]["mallstory_version"] != "service"){
	$Contents01 .= "
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='800px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "basic" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  >
						<a href='?info_type=basic&company_id=".$company_id."&mmode=$mmode'>사업자 정보</a>
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "order_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">담당자 정보</a>";
						}else{
							$Contents01 .= "<a href='?info_type=order_info&company_id=".$company_id."&mmode=$mmode'>담당자 정보</a>";
						}
						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($info_type == "seller_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">관련파일</a>";
						}else{
							$Contents01 .= "<a href='?info_type=seller_info&company_id=".$company_id."&mmode=$mmode'>관련파일</a>";
						}
						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<!--
					<table id='tab_04' ".($info_type == "delivery_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">외부 솔루션/POS연결</a>";
						}else{
							$Contents01 .= "<a href='?info_type=delivery_info&company_id=".$company_id."&mmode=$mmode'>외부 솔루션/POS연결</a>";
						}
						$Contents01 .= "

						</td>
						<th class='box_03'></th>
					</tr>
					</table>-->

					<table id='tab_05' ".($info_type == "factory_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
							
						if($company_id == ""){
							$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">출고지 관리</a>";
						}else{
							$Contents01 .= "<a href='?info_type=factory_info&company_id=".$company_id."'>출고지 관리</a>";
						}
						$Contents01 .= "

						</td>
						<th class='box_03'></th>
					</tr>
					</table>

					<table id='tab_06' ".($info_type == "exchange_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">교환/반품지 관리</a>";
						}else{
							$Contents01 .= "<a href='?info_type=exchange_info&company_id=".$company_id."'>교환/반품지 관리</a>";
						}
						$Contents01 .= "

						</td>
						<th class='box_03'></th>
					</tr>
					</table>

					<table id='tab_07' ".($info_type == "visit_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">방문수령지 관리</a>";
						}else{
							$Contents01 .= "<a href='?info_type=visit_info&company_id=".$company_id."'>방문수령지 관리</a>";
						}

						$Contents01 .= "
						</td>
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
	  ";
	}

if($info_type == "basic" || $info_type == ""){
	$com_zip = explode("-",$db->dt[com_zip]);

	$seller_date = explode(" ",$db->dt[seller_date]);
$Contents01 .= "
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 거래처 등록</b> ".($company_id != "" ? "[ ".$db->dt[com_name]." : ".$company_id." ]" : "")." </div>")."</td>
	</tr>
	</table>
	<table width='100%' border='0' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='18%' />
		<col width='32%' style='padding:0px 0px 0px 10px'/>
		<col width='18%' />
		<col width='32%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr>
		<td class='input_box_title'> <b>거래처 코드</b></td>
		<td class='input_box_item'>
		<input type=text name='company_code' value='".$db->dt[company_code]."' class='textbox'  style='width:80px' validation='false' title='거래처 코드'>
		</td>
		<td class='input_box_title'> <b>거래 시작일</b></td>
		<td class='input_box_item'><input type=text  id='seller_date' name='seller_date' value='".$seller_date[0]."' class='textbox' style='width:80px' validation='false' title='거래 시작일'></td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>거래처 유형 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item' colspan='3'>
			<input type='checkbox' id = 'sales_vendor' name='sell_type[]' value='1' $checked_1 checked> <label for='sales_vendor'>국내매출</label> &nbsp;
			<input type='checkbox' id = 'supply_vendor' name='sell_type[]' value='2' $checked_2> <label for='supply_vendor'>국내매입</label> &nbsp;
			<!--input type='checkbox' id = 'oversea_sales' name='sell_type[]' value='3' $checked_3> <label for='oversea_sales'>해외수출</label> &nbsp;
			<input type='checkbox' id = 'oversea_supply' name='sell_type[]' value='4' $checked_4> <label for='oversea_supply'>해외수입</label> &nbsp;
			<input type='checkbox' id = 'outsourcing' name='sell_type[]' value='5' $checked_5> <label for='outsourcing'>외주물류창고</label-->
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>거래처 구분</b></td>
		<td class='input_box_item'>
			<input type='radio' name='seller_division' id='seller_division_1' value='1' ".($db->dt[seller_division] == "1" || $db->dt[seller_division] == "" ? "checked":"")."> <label for='seller_division_1'>일반</label> &nbsp;&nbsp;
			<input type='radio' name='seller_division' id='seller_division_2' value='2' ".($db->dt[seller_division] == "2" ? "checked":"")."> <label for='seller_division_2'>가맹점</label> &nbsp;&nbsp;
		</td>
		<td class='input_box_title'> <b>국내외 구분</b>   </td>
		<td class='input_box_item'>
			<input type='radio' name='nationality' id='nationality_1' value='I' ".($db->dt[nationality] == "I" || $db->dt[nationality] == "" ? "checked":"")."> <label for='nationality_1'>국내</label> &nbsp;&nbsp;
			<input type='radio' name='nationality' id='nationality_2' value='O' ".($db->dt[nationality] == "O" ? "checked":"")."> <label for='nationality_2'>해외</label> &nbsp;&nbsp;
			<input type='radio' name='nationality' id='nationality_3' value='D' ".($db->dt[nationality] == "D" ? "checked":"")."> <label for='nationality_3'>기타</label> &nbsp;&nbsp;
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>거래처 등급 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<select name=seller_level style='height:23px;'  validation='true' title='거래처 등급'>	
				<option value='' ".($db->dt[seller_level] == "" ? "selected":"")."> 선택 </option>
				<option value='1' ".($db->dt[seller_level] == "1" ? "selected":"")."> 우호 </option>
				<option value='2' ".($db->dt[seller_level] == "2" ? "selected":"")."> 양호 </option>
				<option value='3' ".($db->dt[seller_level] == "3" ? "selected":"")."> 보통 </option>
				<option value='4' ".($db->dt[seller_level] == "4" ? "selected":"")."> 위험 </option>
				<option value='5' ".($db->dt[seller_level] == "5" ? "selected":"")."> 블랙리스트 </option>
			</select>
		</td>

		<td class='input_box_title'> <b>물류창고사용여부</b></td>
		<td class='input_box_item'>
			<input type='radio' id='is_wharehouse_11' name='is_wharehouse' value='1'  ".($db->dt[is_wharehouse] == "1"? "checked":"")."> <label for='is_wharehouse_11'>사용</label> &nbsp;&nbsp;
			<input type='radio' id='is_wharehouse_22' name='is_wharehouse' value='0' ".($db->dt[is_wharehouse] == "0" || $db->dt[is_wharehouse] == "" ? "checked":"")." > <label for='is_wharehouse_22'>미사용</label> &nbsp;&nbsp;
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>상호명 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
		<input type=text name='com_name' id='com_name' value='".$db->dt[com_name]."' class='textbox'  style='width:200px' validation='true' title='상호명'>
		</td>
	    <td class='input_box_title'> <b>대표자명 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'><input type=text  id='com_ceo' name='com_ceo' value='".$db->dt[com_ceo]."' class='textbox' style='width:80px' validation='true' title='대표자명'></td>
	</tr>
	<tr>
		<td class='input_box_title'><b>사업자번호  <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='com_number_1' id='com_number_1' value='".$com_number[0]."' maxlength=3 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='사업자번호'> -
			<input type=text name='com_number_2' id='com_number_2' value='".$com_number[1]."' maxlength=2 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='사업자번호'> -
			<input type=text name='com_number_3' id='com_number_3' value='".$com_number[2]."' maxlength=5 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='사업자번호'>
			<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
		</td>
		<td class='input_box_title'><b>법인번호</b></td>
		<td class='input_box_item'>
			<input type=text name='corporate_number_1' id='corporate_number_1' value='".$corporate_number[0]."' maxlength=6 style='width:60px;' class='textbox numeric' com_numeric=true validation='false' title='법인번호'> -
			<input type=text name='corporate_number_2' id='corporate_number_2' value='".$corporate_number[1]."' maxlength=7 style='width:60px;' class='textbox numeric' com_numeric=true validation='false' title='법인번호'>
			<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>업태 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'><input type=text name='com_business_status' value='".$db->dt[com_business_status]."' class='textbox' style='width:200px' validation='true' title='업태'></td>
	    <td class='input_box_title' > <b>업종 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'><input type=text name='com_business_category' value='".$db->dt[com_business_category]."' class='textbox' style='width:200px' validation='true' title='업종'></td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>사업자 유형 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item' colspan='3'>
			<input type='radio' name='com_div' id='com_div_R' value='R' ".($db->dt[com_div] == "R" || $db->dt[com_div] == "" ? "checked":"")."> <label for='com_div_R'>법인사업자</label> &nbsp;&nbsp;
			<input type='radio' name='com_div' id='com_div_P' value='P' ".($db->dt[com_div] == "P" ? "checked":"")."> <label for='com_div_P'>개인사업자</label> &nbsp;&nbsp;
			<input type='radio' name='com_div' id='com_div_S' value='S' ".($db->dt[com_div] == "S" ? "checked":"")."> <label for='com_div_S'>간이과세자</label> &nbsp;&nbsp;
			<input type='radio' name='com_div' id='com_div_E' value='E' ".($db->dt[com_div] == "E" ? "checked":"")."> <label for='com_div_E'>면세사업자</label> &nbsp;&nbsp;
			<!--<input type='radio' name='com_div' id='com_div_I' value='I' ".($db->dt[com_div] == "I" ? "checked":"")."> <label for='com_div_I'>수출입업체</label> &nbsp;&nbsp;-->
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>전화번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='com_phone_1' id='com_phone_1' value='".$com_phone[0]."' maxlength=3 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='전화'> -
			<input type=text name='com_phone_2' id='com_phone_2' value='".$com_phone[1]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='전화'> -
			<input type=text name='com_phone_3' id='com_phone_3' value='".$com_phone[2]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='전화'>
		</td>
		<td class='input_box_title'> <b>핸드폰</b></td>
		<td class='input_box_item'>
			<input type=text name='com_mobile_1' id='com_mobile_1' value='".$com_mobile[0]."' maxlength=3 style='width:35px;' class='textbox numeric' com_numeric=true validation='false' title='핸드폰'> -
			<input type=text name='com_mobile_2' id='com_mobile_2' value='".$com_mobile[1]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='false' title='핸드폰'> -
			<input type=text name='com_mobile_3' id='com_mobile_3' value='".$com_mobile[2]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='false' title='핸드폰'>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>이메일</b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='false' title='이메일' email=true>
		</td>
		<td class='input_box_title'> <b>홈페이지</b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='com_homepage' value='".$db->dt[com_homepage]."'  style='width:200px'>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>주소  <img src='".$required3_path."'> </b>    </td>
		<td class='input_box_item' colspan=3>
		<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
	    	<div id='input_address_area' ><!--style='display:none;'-->
	    	<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='80px'>
				<col width='*'>
				<tr>
					<td height=26>
						<input type='text' class='textbox' name='com_zip' id='zip_b_1' style='width:60px;' maxlength='15' value='".$db->dt[com_zip]."' validation='true' title='우편번호' readonly>
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[com_addr1]."' style='width:300px;' class='textbox' style='width:75%' validation='true' title='주소'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[com_addr2]."' style='width:300px;' class='textbox'  style='width:450px' validation='true'> (상세주소)
					</td>
				</tr>
				</table>
	    	</div>
	    </td>
	  </tr>";

	$ws_tel = explode("-",$db->dt[ws_tel]);
	$ws_charge_phone = explode("-",$db->dt[ws_charge_phone]);
	$kakao_phone = explode("-",$db->dt[kakao_phone]);

$Contents01 .= "
	  <tr>
		<td class='input_box_title'> <b>도매상권 사용 <img src='".$required3_path."'> </b>    </td>
		<td class='input_box_item' colspan=3>
			<input type='radio' id='commercial_disp_n' name='commercial_disp' value='N'  ".($db->dt[commercial_disp] == "N" || $db->dt[commercial_disp] == "" ? "checked":"")."> <label for='commercial_disp_n'>미사용</label> &nbsp;&nbsp;
			<input type='radio' id='commercial_disp_y' name='commercial_disp' value='Y' ".($db->dt[commercial_disp] == 'Y' ? "checked":"")." > <label for='commercial_disp_y'>사용</label> 
		</td>
	</tr>
	<tr class='wholesale_tr wholesale_validation' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>상권선택 <img src='".$required3_path."'> </b>    </td>
		<td class='input_box_item'>
			".getCommercialCountry($db->dt[ca_country],"select","onchange=\"window.frames['act'].location.href='../buyingservice/commercial_area.soapload.php?trigger='+this.value+'&target=ca_code&form=edit_form'\"")." ".getSoapCommercialAreaInfo($db->dt[ca_country],$db->dt[ca_code],"onchange=\"window.frames['act'].location.href='../buyingservice/shopping_center.soapload.php?trigger='+this.value+'&target=sc_code&form=edit_form'\"")."
		</td>
		<td class='input_box_title'> <b>상가선택 <img src='".$required3_path."'> </b>    </td>
		<td class='input_box_item'>
			".getSoapShoppingCenter($db->dt[sc_code],"select","onchange=\"window.frames['act'].location.href='../buyingservice/shopping_center_info.soapload.php?trigger='+this.value+'&target=floor&form=edit_form'\"")."
		</td>
	</tr>
	<tr class='wholesale_tr wholesale_validation' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>층/라인/호수 선택 <img src='".$required3_path."'> </b>    </td>
		<td class='input_box_item' colspan='3'>
			".getSoapShoppingCenterFloorInfo($db->dt[sc_code],$db->dt[floor])." 
			".getSoapShoppingCenterLineInfo($db->dt[sc_code],$db->dt[line])." 
			".getSoapShoppingCenterNoInfo($db->dt[sc_code],$db->dt[no])."
		</td>
	</tr>
	<tr class='wholesale_tr wholesale_validation' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>매장 전화번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='ws_tel_1' id='ws_tel_1' value='".$ws_tel[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='true' title='매장 전화번호'> -
			<input type=text name='ws_tel_2' id='ws_tel_2' value='".$ws_tel[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='매장 전화번호'> -
			<input type=text name='ws_tel_3' id='ws_tel_3' value='".$ws_tel[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='매장 전화번호'>
		</td>
		<td class='input_box_title'> <b>담당자 핸드폰 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='ws_charge_phone_1' id='ws_charge_phone_1' value='".$ws_charge_phone[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='true' title='담당자 핸드폰'> -
			<input type=text name='ws_charge_phone_2' id='ws_charge_phone_2' value='".$ws_charge_phone[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='담당자 핸드폰'> -
			<input type=text name='ws_charge_phone_3' id='ws_charge_phone_3' value='".$ws_charge_phone[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='담당자 핸드폰'>
		</td>
	</tr>
	<tr class='wholesale_tr' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>카카오톡 핸드폰번호 </b></td>
		<td class='input_box_item'>
			<input type=text name='kakao_phone_1' id='kakao_phone_1' value='".$kakao_phone[0]."' maxlength=3 style='width:20px' class='textbox' com_numeric=true validation='false' title='카카오톡 핸드폰번호'> -
			<input type=text name='kakao_phone_2' id='kakao_phone_2' value='".$kakao_phone[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='false' title='카카오톡 핸드폰번호'> -
			<input type=text name='kakao_phone_3' id='kakao_phone_3' value='".$kakao_phone[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='false' title='카카오톡 핸드폰번호'>
		</td>
		<td class='input_box_title'> <b>카카오톡 ID </b></td>
		<td class='input_box_item'>
			<input type=text name='kakao_id' id='kakao_id' value='".$db->dt[kakao_id]."' style='width:130px' class='textbox' validation='false' title='카카오톡 ID'>
		</td>
	</tr>
	<tr class='wholesale_tr' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>페이스북 </b></td>
		<td class='input_box_item'>
			<input type=text name='facebook' id='facebook' value='".$db->dt[facebook]."' style='width:130px' class='textbox' validation='false' title='페이스북'>
		</td>
		<td class='input_box_title'> <b>트위터 </b></td>
		<td class='input_box_item'>
			<input type=text name='twitter' id='twitter' value='".$db->dt[twitter]."' style='width:130px' class='textbox' validation='false' title='트위터'>
		</td>
	</tr>
	<tr class='wholesale_tr' ".($db->dt[commercial_disp] == 'Y' ? "" :"style='display:none;'").">
		<td class='input_box_title'> <b>QQ(중국) </b></td>
		<td class='input_box_item'>
			<input type=text name='qq' id='qq' value='".$db->dt[qq]."' style='width:130px' class='textbox' validation='false' title='QQ(중국)'>
		</td>
		<td class='input_box_title'> <b>WeChat </b></td>
		<td class='input_box_item'>
			<input type=text name='wechat' id='wechat' value='".$db->dt[wechat]."' style='width:130px' class='textbox' validation='false' title='WeChat'>
		</td>
	</tr>";
	if($_SESSION["admininfo"]["mallstory_version"] != "service"){
		$Contents01 .= "
		<tr>
			<td class='input_box_title'> <b>본사 담당사업장  <img src='".$required3_path."'> </b></td>
			<td class='search_box_item' colspan='3'>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td style='padding-right:5px;'>
						".getCompanyList("본사", "cid0_1", "onChange=\"loadCategory('cid0_1','cid1_1','company')\" title='선택' ", '5', $cid2,'company')."</td>
						<td style='padding-right:5px;'>
						".getCompanyList("선택", "cid1_1", "onChange=\"loadCategory('cid1_1','cid2_1','company')\" title='선택'", '15', $cid2,'company')."</td>
						<td style='padding-right:5px;'>
						".getCompanyList("선택", "cid2_1", "onChange=\"loadCategory('cid2_1','cid3_1','company')\" title='선택'", '25', $cid2,'company')."</td>
						<td>".getCompanyList("선택", "cid3_1", "onChange=\"loadCategory('cid3_1','','company')\" title='선택'", '35', $cid2,'company')."</td>
					</tr>
				</table>
			</td>
		</tr>";
	}
	$Contents01 .= "
	<tr>
		<td class='input_box_title'> <b>구매 담당자</b></td>
		<td class='search_box_item' colspan='3'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>";
					if($_SESSION["admininfo"]["mallstory_version"] == "service"){
						$Contents01 .= "
						<td style='padding-right:5px;'>".get_person($com_group,$department,$position,$duty,$company_id)."</td>";
					}else{
						$Contents01 .= "
						<td style='padding-right:5px;'>
						".getgroup1($group_ix)."</td>
						<td style='padding-right:5px;'>
						".getdepartment($dp_ix)."</td>
						<td style='padding-right:5px;'>
						".getposition($ps_ix)."</td>
						<td style='padding-right:5px;'>".getduty($cu_ix)."</td>
						<td style='padding-right:5px;'>".get_person($com_group,$department,$position,$duty,$company_id)."</td>";
					}
				$Contents01 .= "
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td class='input_box_title'> <b> 여신한도</b></td>
		<td class='input_box_item'>
			<input type=text name='loan_price' value='".$db->dt[loan_price]."' class='textbox numeric' style='width:100px' validation='false' title='여신한도'>
		</td>
		<td class='input_box_title'><b>보증금</b></td>
		<td class='input_box_item'>
			<input type=text name='deposit_price' value='".$db->dt[deposit_price]."' class='textbox numeric' style='width:100px' validation='false' title='보증금'>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'><b>사업자 인감도장 </b></td>";
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."/company_stamp_".$company_id.".gif")){
			$stamp_bool="false";
		}else{
			$stamp_bool="false";
		}

$Contents01 .= "
		<td class='input_box_item' colspan='3'>
			<table cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<td width='320'><input type=file name='stamp_file' size=70 class='textbox'  style='width:300px' validation='$stamp_bool' title='사업자 인감도장'></td>";
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."/company_stamp_".$company_id.".gif")){
	$Contents01 .= "<td width='*' style='padding:5px 0px;'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/company_stamp_".$company_id.".gif' width=50>
					</td>
					<td>
						<span style='padding-left:20px;'><b>&nbsp;&nbsp;2M 이하만 등록가능</b></span>
						<a href='javascript:' onclick=\"del_img('company_stamp_".$company_id.".gif','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;'></a>
					</td>";
			}

$Contents01 .= "</tr>
			</table>
		</td>

	</tr>
	<tr>
		<td class='input_box_title'><b>지정가상계좌번호</b></td>
		<td class='input_box_item' colspan='3'>
			<select name='virtual_bank'><!--코드를 어떻게 해야 할지 몰라 임시로 해놓음-->
				<option value=''>은행선택</option>
				<option value='KB' ".($db->dt[virtual_bank] == "KB" ? "selected":"").">국민은행</option>
				<option value='SH' ".($db->dt[virtual_bank] == "SH" ? "selected":"").">신한은행</option>
			</select>
			<input type=text name='virtual_bank_number' value='".$db->dt[virtual_bank_number]."' class='textbox'  style='width:150px' validation='false' title='가상계좌번호'>
			예) XXX-XX-XXXXX-XXX
		</td>
	</tr>
	<tr bgcolor=#ffffff height=110>
		<td class='input_box_title'><b>기타사항</b></td>
		<td class='input_box_item' colspan=3>
		<textarea type=text class='textbox' name='seller_message' value='".$db->dt[seller_message]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[seller_message]."</textarea>
		</td>
	</tr>";

	if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
		if($_SESSION["admininfo"]["mallstory_version"] == "service"){
			$Contents01 .= "
			<tr style='display:none;'>
				<td class='input_box_title'> <b>거래처승인</b></td>
				<td class='input_box_item' colspan=3>
					<input type=hidden name='seller_auth' value='Y'>
				</td>
			</tr>";
		}else{
			$Contents01 .= "
			<tr>
				<td class='input_box_title'> <b>거래처승인</b></td>
				<td class='input_box_item' colspan=3>
					<input type=radio name='seller_auth' id='seller_auth_N' value='N' ".CompareReturnValue("N",$db->dt[seller_auth],"checked")." ".CompareReturnValue("",$db->dt[seller_auth],"checked")."><label for='seller_auth_N'>승인대기</label>
					<input type=radio name='seller_auth' id='seller_auth_Y' value='Y'  ".CompareReturnValue("Y",$db->dt[seller_auth],"checked")."><label for='seller_auth_Y'>승인</label>
					<input type=radio name='seller_auth' id='seller_auth_X' value='X' ".CompareReturnValue("X",$db->dt[seller_auth],"checked")."><label for='seller_auth_X'>승인거부</label>
					 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'><!--입점업체 승인후에 사용자 등록이 가능합니다. --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>
				</td>
			</tr>";
		}
	}

$Contents01 .= "
	</table><br><br>";
}

if($info_type == "order_info"){

$Contents01 .= "
	
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 거래처 담당자</b> ".($company_id != "" ? "[ ".$db->dt[com_name]." ]" : "")." </div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='20%' />
		<col width='30%' style='padding:0px 0px 0px 10px'/>
		<col width='20%' />
		<col width='30%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr>
		<td class='input_box_title'> <b>담당자명</b></td>
		<td class='input_box_item' colspan='3'>
		<input type=text name='customer_name' value='".$db->dt[customer_name]."' class='textbox'  style='width:200px' validation='false' title='담당자명'>
		</td>
	</tr>

	<tr>
	    <td class='input_box_title'> <b>전화번호</b></td>
		<td class='input_box_item'>
		<input type=text name='customer_phone' value='".$db->dt[customer_phone]."' class='textbox'  style='width:200px' validation='false' title='전화번호'>
		</td>
	    <td class='input_box_title'> <b>핸드폰번호</b>   </td>
		<td class='input_box_item'><input type=text name='customer_mobile' value='".$db->dt[customer_mobile]."' class='textbox'  style='width:200px' validation='false' title='핸드폰번호'></td>
	</tr>

	<tr>
	    <td class='input_box_title'> <b>이메일</b></td>
		<td class='input_box_item'>
		<input type=text name='customer_mail' value='".$db->dt[customer_mail]."' class='textbox'  style='width:200px' email='true' title='이메일'>
		</td>
	    <td class='input_box_title'> <b>직급/직책</b>   </td>
		<td class='input_box_item'><input type=text  id='' name='customer_position' value='".$db->dt[customer_position]."' class='textbox'  style='width:200px' validation='false' title='직급/직책'></td>
	</tr>
		<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>기타사항</b></td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='customer_message' value='".$db->dt[customer_message]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[customer_message]."</textarea>
			</td>
	</tr>
	</table>";


$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td height='20' colspan='4'></td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 세무 담당자</b> ".($company_id != "" ? "[ ".$db->dt[com_name]."  ]" : "")." </div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='20%' />
		<col width='30%' style='padding:0px 0px 0px 10px'/>
		<col width='20%' />
		<col width='30%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr>
	    <td class='input_box_title'> <b>담당자명</b></td>
		<td class='input_box_item'>
		<input type=text name='tax_person_name' value='".$db->dt[tax_person_name]."' class='textbox'  style='width:200px' validation='false' title='담당자명'>
		</td>
		 <td class='input_box_item' colspan='2'> <input type='checkbox' id='check_all' value='all'> <label for='check_all'>상기내용과 동일시 체크박스에 클릭하세요.</label> </td>
	</tr>

	<tr>
	    <td class='input_box_title'> <b>전화번호</b></td>
		<td class='input_box_item'>
		<input type=text name='tax_person_phone' value='".$db->dt[tax_person_phone]."' class='textbox'  style='width:200px' validation='false' title='전화번호'>
		</td>
	    <td class='input_box_title'> <b>핸드폰번호</b>   </td>
		<td class='input_box_item'><input type=text name='tax_person_mobile' value='".$db->dt[tax_person_mobile]."' class='textbox'  style='width:200px' validation='false' title='핸드폰번호'></td>
	</tr>

	<tr>
	    <td class='input_box_title'> <b>이메일</b></td>
		<td class='input_box_item'>
		<input type=text name='tax_person_mail' value='".$db->dt[tax_person_mail]."' class='textbox'  style='width:200px' email=true title='이메일'>
		</td>
	    <td class='input_box_title'> <b>직급/직책</b>   </td>
		<td class='input_box_item'><input type=text  id='' name='tax_person_position' value='".$db->dt[tax_person_position]."' class='textbox'  style='width:200px' validation='false' title='직급/직책'></td>
	</tr>
	<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>기타사항</b></td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='tax_person_message' value='".$db->dt[tax_person_message]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[tax_person_message]."</textarea>
			</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr><td height='20' colspan='4'></td></tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b>세금계산서 및 통장관리</b> ".($company_id != "" ? "[ ".$db->dt[com_name]."  ]" : "")." </div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='20%' />
		<col width='30%' style='padding:0px 0px 0px 10px'/>
		<col width='20%' />
		<col width='30%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr>
	    <td class='input_box_title'> <b>세금계산서이메일 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item' colspan='3'>
		<input type=text name='tax_mail' value='".$db->dt[tax_mail]."' class='textbox'  style='width:200px' validation='true' title='세금계산서이메일'>
		</td>
	</tr>

	<tr>
	    <td class='input_box_title'> <b>거래처 은행정보 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item' colspan='3' style='line-height:200%;padding:3px 5px'>
		<select name='basic_bank'>
				<option value='0' ".($db->dt[bank_name] == "0" ? "selected":"").">은행선택</option>
				<option value='1' ".($db->dt[bank_name] == "1" ? "selected":"").">신한은행</option>
				<option value='2' ".($db->dt[bank_name] == "2" ? "selected":"").">국민은행</option>
				<option value='3' ".($db->dt[bank_name] == "3" ? "selected":"").">하나은행</option>
				<option value='4' ".($db->dt[bank_name] == "4" ? "selected":"").">씨티은행</option>
				<option value='5' ".($db->dt[bank_name] == "5" ? "selected":"").">외환은행</option>
				<option value='6' ".($db->dt[bank_name] == "6" ? "selected":"").">농협은행</option>
				<option value='7' ".($db->dt[bank_name] == "7" ? "selected":"").">기업은행</option>
				<option value='8' ".($db->dt[bank_name] == "8" ? "selected":"").">수협은행</option>
				<option value='9' ".($db->dt[bank_name] == "9" ? "selected":"").">우체국은행</option>
				<option value='10' ".($db->dt[bank_name] == "10" ? "selected":"").">신협</option>
				<option value='11' ".($db->dt[bank_name] == "11" ? "selected":"").">새마을금고</option>
				<option value='12' ".($db->dt[bank_name] == "12" ? "selected":"").">상호저축은행</option>
			</select>&nbsp;&nbsp;&nbsp;
		예금주 &nbsp;&nbsp;<input type=text name='holder_name' value='".$db->dt[holder_name]."' class='textbox'  style='width:150px' validation='true' title='예금주'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		계좌번호 &nbsp;&nbsp;<input type=text name='bank_num' value='".$db->dt[bank_num]."' class='textbox'  style='width:200px' validation='true' title='계좌번호'>
		</td>
	</tr>
	</table>";

}

if($info_type == "seller_info"){

$i = 0;
while(count($seller_info_array)> $i){
	
	if($seller_info_array[$i][sheet_name] == "business_file")	$business_file = $seller_info_array[$i][sheet_value];
	if($seller_info_array[$i][sheet_name] == "registration_file")	$registration_file = $seller_info_array[$i][sheet_value];
	if($seller_info_array[$i][sheet_name] == "telemarke_file")	$telemarke_file = $seller_info_array[$i][sheet_value];
	if($seller_info_array[$i][sheet_name] == "bank_file")	$bank_file = $seller_info_array[$i][sheet_value];

	if(strstr($seller_info_array[$i][sheet_name],"sheet_name")){
		
		$deail_array[$seller_info_array[$i][sheet_name]][sheet_value] = $seller_info_array[$i][sheet_value];
		$deail_array[$seller_info_array[$i][sheet_name]][text] = $seller_info_array[$i][text];

	}
	
$i++;

}

$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;
$file_name = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$registration_file;
$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
		<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 관련 첨부파일 자료</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  id='report_table'>
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />";

$Contents01 .= "
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>사업자 등록증 사본</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
			<tr>
				<td width='250'>
					<input type=file name='business_file' size=30 class='textbox'  style='width:200px;'>
				</td>
			";
			if(is_file($path."/".$business_file)){
			$Contents01 .= "
			<td width='70'>
			<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$business_file."' width=50 height=50>&nbsp;&nbsp;&nbsp;
			</td>";
			$Contents01 .= "
					<td width='140'>
					<input type='button' name='file_down' value='다운로드 받기' onclick=\"download_img('".$path."/".$business_file."')\" class='textbox' style='cursor: pointer;'>
					<a href='javascript:' onclick=\"del_img('".$business_file."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";
			}
		$Contents01 .= "
				<td>
				<b>&nbsp;&nbsp;2M 이하만 등록가능</b>
				</td>
			</tr>
			</table>
		  </td>
	  </tr>
		";

$Contents01 .= "
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>법인등기부등본</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
			<tr>
				<td width='250'>
					<input type=file name='registration_file' size=30 class='textbox'  style='width:200px;'>
				</td>";

if(is_file($path."/".$registration_file)){
$Contents01 .= "
				<td width='70'>
				<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$registration_file."' width=50 height=50>&nbsp;&nbsp;&nbsp;
				</td>";
$Contents01 .= "<td width='140'>
				<input type='button' name='file_down' value='다운로드 받기' onclick=\"download_img('".$path."/".$registration_file."')\" class='textbox' style=' cursor: pointer;'>
				<a href='javascript:' onclick=\"del_img('".$registration_file."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
				</td>";
}
$Contents01 .= "
				<td>
				<b>&nbsp;&nbsp;2M 이하만 등록가능</b>
				</td>
			</tr>
			</table>
		</td>
	  </tr>
		";
		
$Contents01 .= "
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>통신판매등록증 사본</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
		<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
			<tr>
				<td width='250'>
					<input type=file name='telemarke_file' size=30 class='textbox'  style='width:200px;'> 
				</td>";

if(is_file($path."/".$telemarke_file)){
	$Contents01 .= "
				<td width='70'>
				<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$telemarke_file."' width=50 height=50>&nbsp;&nbsp;&nbsp;
				</td>";
	$Contents01 .= "
				<td width='140'>
				<input type='button' name='file_down' value='다운로드 받기' onclick=\"download_img('".$path."/".$telemarke_file."')\" class='textbox' style=' cursor: pointer;'>
				<a href='javascript:' onclick=\"del_img('".$telemarke_file."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
				</td>";
}
$Contents01 .= "<td>
					<b>&nbsp;&nbsp;2M 이하만 등록가능</b>
					</td>
			</tr>
			</table>
		</td>
	</tr>
		";
$Contents01 .= "
	   <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>주거래은행 사본</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
		<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
			<tr>
				<td width='250'>
					<input type=file name='bank_file' size=30 class='textbox'  style='width:200px;'>
			</td>";

if(is_file($path."/".$bank_file)){
	$Contents01 .= "
			<td width='70'>
			<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$bank_file."' width=50 height=50>&nbsp;&nbsp;&nbsp;
			</td>";
	$Contents01 .= "
			<td width='140'>
			<input type='button' name='file_down' value='다운로드 받기' onclick=\"download_img('".$path."/".$bank_file."')\" class='textbox' style='padding-left: 10px; cursor: pointer;'>
			<a href='javascript:' onclick=\"del_img('".$bank_file."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
			</td>";
}
$Contents01 .= "<td>
					<b>&nbsp;&nbsp;2M 이하만 등록가능</b>
				</td>
			</tr>
			</table>
		</td>
	  </tr>";

if(is_array($deail_array)){

	foreach($deail_array as $key => $value){
	
	$Contents01 .= "
		   <tr bgcolor=#ffffff height=34 id='add_table'>
			<td class='input_box_title'>
				<input type=text name='sheet_name[]' value='".$value[text]."' size=30 class='textbox'  style='width:180px;'>
			</td>
			<td class='input_box_item' colspan=3 style='padding:5px 5px;'>
			 <input type=file name='sheet_value[]' size=30 class='textbox'  style='width:200px;'>
			 <input type='hidden' name='sheet_value[][image_name]' value='".$value[sheet_value]."'>
			 <span style='padding-left:35px;'>";

	if(is_file($path."/".$value[sheet_value])){
		$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$value[sheet_value]."' width=50 height=50>&nbsp;&nbsp;&nbsp;&nbsp;";
		$Contents01 .= "

				<input type='button' name='file_down' value='다운로드 받기' onclick=\"download_img('".$path."/".$value[sheet_value]."')\" class='textbox' style='padding-left: 10px; cursor: pointer;'>
				<a href='javascript:' onclick=\"del_img('".$value[sheet_value]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>&nbsp;
				";
	}

	$Contents01 .= "
			<b>&nbsp;&nbsp;2M 이하만 등록가능</b>
			</span>
			 <span style='padding-left:50px;'> <img id='table_close' src='../images/".$admininfo["language"]."/btn_del.gif'  style='cursor:pointer' align='absmiddle'></span> 
			</td>
		  </tr>";
	}

}else{
		$Contents01 .= "
		   <tr bgcolor=#ffffff height=34 id='add_table'>
			<td class='input_box_title'>
				<input type=text name='sheet_name[]' value='".$value[text]."' size=30 class='textbox'  style='width:180px;'>
			</td>
			<td class='input_box_item' colspan=3 style='padding:5px 5px;'>
			 <input type=file name='sheet_value[]' size=30 class='textbox'  style='width:200px;'>
			 <input type='hidden' name='sheet_value[][image_name]' value='".$value[sheet_value]."'>
			 <span style='padding-left:100px;'>";
		$Contents01 .= "
				<b>&nbsp;&nbsp;2M 이하만 등록가능</b>
				</span>
				 <span style='padding-left:50px;'>
				 <img id='table_close' src='../images/".$admininfo["language"]."/btn_del.gif'  style='cursor:pointer' align='absmiddle'>
				 </span> 
				</td>
			  </tr>";
}

$Contents01 .= "
	  </table>
		<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr>
			<td align=left style='padding:10px;' class=small>
			<span id='add' style='margin-left:10px;'>
			<img id='add'  src='../images/".$admininfo["language"]."/btn_add2.gif' border=0  style='cursor:pointer;border:0px;' ></span> 
			<!--<span style='padding-left:10px; id='delete'><input type='button' id='delete' value='삭제'  class='textbox'></span>--></td>
		</tr>
	  </table>";
}
$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;' class=small>
		<img src='../image/emo_3_15.gif' align=absmiddle> 해당거래처와의 기본 계좌 정보를 입력합니다.
	</td>
</tr>
</table>
";

if($info_type == "delivery_info"){
$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	 <col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='30%' />
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 입점업체별 기본 배송정책 및 수수료</b></div>")."</td>
	  </tr>
	  </table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  >
	<col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='30%' />
";
	
$Contents01 .= "
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>POS1</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
		<input type='hidden' name='pos[0][pos_detail_ix]' value='".$pos_detail[0][pos_detail_ix]."'>
	    <input type='radio' name='pos[0][pos_use]' id='pos0_N' value='N'  ".($pos_detail[0][pos_use] == "N" || $pos_detail[0][pos_use] == "" ? "checked":"")."> &nbsp;&nbsp;<label for='pos0_N'> 미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='pos[0][pos_use]' id='pos0_Y' value='Y' ".($pos_detail[0][pos_use] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='pos0_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[0][pos_code]' value='".$pos_detail[0][pos_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부POS코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[0][pos_outcode]' value='".$pos_detail[0][pos_outcode]."'>
		</td></tr>
	<tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>POS2</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
		<input type='hidden' name='pos[1][pos_detail_ix]' value='".$pos_detail[1][pos_detail_ix]."'>
	    <input type='radio' name='pos[1][pos_use]' id='pos1_N' value='N' ".($pos_detail[1][pos_use] == "N" || $pos_detail[0][pos_use] == "" ? "checked":"")."> &nbsp;&nbsp; <label for='pos1_N'> 미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='pos[1][pos_use]' id='pos1_Y' value='Y' ".($pos_detail[1][pos_use] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='pos1_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[1][pos_code]' value='".$pos_detail[1][pos_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부POS코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[1][pos_outcode]' value='".$pos_detail[1][pos_outcode]."'>
		</td></tr>
	<tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>POS3</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
		<input type='hidden' name='pos[2][pos_detail_ix]' value='".$pos_detail[2][pos_detail_ix]."'>
	    <input type='radio' name='pos[2][pos_use]' id='pos2_N' value='N' ".($pos_detail[2][pos_use] == "N" || $pos_detail[0][pos_use] == "" ? "checked":"")."> &nbsp;&nbsp; <label for='pos2_N'>미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='pos[2][pos_use]' id='pos2_Y' value='Y' ".($pos_detail[2][pos_use] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='pos2_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[2][pos_code]' value='".$pos_detail[2][pos_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부POS코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[2][pos_outcode]' value='".$pos_detail[2][pos_outcode]."'>
		</td></tr>
	<tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>POS4</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
		<input type='hidden' name='pos[3][pos_detail_ix]' value='".$pos_detail[3][pos_detail_ix]."'>
	    <input type='radio' name='pos[3][pos_use]' id='pos3_N' value='N' ".($pos_detail[3][pos_use] == "N" || $pos_detail[0][pos_use] == "" ? "checked":"")."> &nbsp;&nbsp; <label for='pos3_N'>미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='pos[3][pos_use]' id='pos3_Y' value='Y' ".($pos_detail[3][pos_use] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='pos3_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[3][pos_code]' value='".$pos_detail[3][pos_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부POS코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[3][pos_outcode]' value='".$pos_detail[3][pos_outcode]."'>
		</td></tr>
	<tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>POS5</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
		<input type='hidden' name='pos[4][pos_detail_ix]' value='".$pos_detail[4][pos_detail_ix]."'>
	    <input type='radio' name='pos[4][pos_use]' id='pos4_N' value='N' ".($pos_detail[4][pos_use] == "N" || $pos_detail[0][pos_use] == "" ? "checked":"")."> &nbsp;&nbsp; <label for='pos4_N'>미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='pos[4][pos_use]' id='pos4_Y' value='Y'  ".($pos_detail[4][pos_use] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='pos4_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[4][pos_code]' value='".$pos_detail[4][pos_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부POS코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[4][pos_outcode]' value='".$pos_detail[4][pos_outcode]."'>
		</td></tr>
</table>
		";

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	 <col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<tr><td height='20'></td></tr>
	<col width='30%' />
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 외부 솔루션 설정</b></div>")."</td>
	  </tr>
	  </table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  >
	<col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='30%' />
		<tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>외부 연결 솔루션</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
	    <input type='radio' name='solution_yn' id='solution_yn_N' value='N' ".($pos_info[0][solution_yn] == "N" || $pos_info[0][solution_yn] == "" ? "checked":"")."> &nbsp;&nbsp;<label for='solution_yn_N'>미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='solution_yn' id='solution_yn_Y' value='Y' ".($pos_info[0][solution_yn] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='solution_yn_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='solution_code' value='".$pos_info[0][solution_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부코드&nbsp;&nbsp;<input class='textbox' type='text' name='solution_coutcode' value='".$pos_info[0][solution_coutcode]."'>
		</td></tr>
	</table>";

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	 <col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<tr><td height='20'></td></tr>
	<col width='30%' />
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 자사 사이트 연동</b></div>")."</td>
	  </tr>
	  </table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  >
	<col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='30%' />
		<tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>회원간 연동</b>  </td>
	    <td class='input_box_item' colspan=3 style='padding:5px 5px;'>
	    <input type='radio' name='site_yn' id='site_yn_N' value='N' ".($pos_info[0][site_yn] == "N" || $pos_info[0][site_yn] == ""? "checked":"")."> &nbsp;&nbsp;<label for='site_yn_N'>미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='site_yn' id='site_yn_Y' value='Y' ".($pos_info[0][site_yn] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='site_yn_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		ID&nbsp;&nbsp;<input class='textbox' type='text' name='site_id' value='".$pos_info[0][site_id]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		회원코드&nbsp;&nbsp;<input class='textbox' type='text' name='site_pw' value='".$pos_info[0][site_pw]."'>
		</td></tr>
		<input type='hidden' name='pos_info_ix' value='".$pos_info[0][pos_info_ix]."'>
	</table>";

}


if($info_type == "factory_info"){

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	<tr>
		<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
			<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>촐고지 관리</b>
		</td>
	</tr>
</table>";

$Contents01 .= "
<form name='edit_form' action='delivery.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='act'><!--target='iframe_act' -->
<input name='act' type='hidden' value='$act'>
<input name='info_type' type='hidden' value='$info_type'>
<input name='company_id' type='hidden' value='".$company_id."'>
<input name='delivery_type' type='hidden' value='F'>
<input name='mall_ix' type='hidden' value=''>
<input name='addr_ix' type='hidden' value='$addr_ix'>

	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='18%' />
		<col width='32%' style='padding:0px 0px 0px 10px'/>
		<col width='18%' />
		<col width='32%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr>
		<td class='input_box_title'> <b>출고지명 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='addr_name' value='".$db->dt[addr_name]."'  style='width:200px' validation='true' title='출고지명'>
		</td>
		<td class='input_box_title'> <b>담당자명 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='person_name' value='".$db->dt[person_name]."'  title='담당자명' validation='true' style='width:200px'>
		</td>
	</tr>

	<tr>
		<td class='input_box_title'> <b>일반 전화번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='addr_phone_1' id='addr_phone_1' value='".$addr_phone[0]."' maxlength=3 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='전화'> -
			<input type=text name='addr_phone_2' id='addr_phone_2' value='".$addr_phone[1]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='전화'> -
			<input type=text name='addr_phone_3' id='addr_phone_3' value='".$addr_phone[2]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='전화'>
		</td>
		<td class='input_box_title'> <b>핸드폰번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='addr_mobile_1' id='addr_mobile_1' value='".$addr_mobile[0]."' maxlength=3 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='핸드폰'> -
			<input type=text name='addr_mobile_2' id='addr_mobile_2' value='".$addr_mobile[1]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='핸드폰'> -
			<input type=text name='addr_mobile_3' id='addr_mobile_3' value='".$addr_mobile[2]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='핸드폰'>
		</td>
	</tr>
	<tr>
	<td class='input_box_title'> <b>출고지 주소 <img src='".$required3_path."'> </b></td>
	<td class='input_box_item' colspan=3>
		<div id='input_address_area' >
		<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
		<col width='80px'>
		<col width='*'>
		<tr>
			<td height=26>
				<input type='text' class='textbox' name='com_zip' id='zip_b_1' style='width:60px;'  title='출고지 주소' validation='true' maxlength='15' value='".$db->dt[zip_code]."' readonly>
			</td>
			<td style='padding:1px 0 0 5px;'>
				<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
			</td>
		</tr>
		<tr>
			<td colspan=2 height=26>
				<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[address_1]."' style='width:300px;' class='textbox'  title='출고지 주소' validation='true' style='width:75%'>
			</td>
		</tr>
		<tr>
			<td colspan=2 height=26>
				<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[address_2]."' style='width:300px;' title='상세주소' class='textbox' validation='true' style='width:450px'> (상세주소)
			</td>
		</tr>
		</table>
		</div>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>기본 출고지 사용  <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=radio class='radio' name='basic_addr_use' value='Y' id='basic_addr_use_1'  validation='false' title='기본 출고지 사용' ".($db->dt[basic_addr_use] == 'Y' || $db->dt[basic_addr_use] == ""?'checked':'')."> <label for='basic_addr_use_1'>사용</lable>
			<input type=radio class='radio' name='basic_addr_use' value='N' id='basic_addr_use_2'  validation='false' title='기본 출고지 사용' ".CompareReturnValue("N",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_2'>미사용</lable>
		</td>
		<td class='input_box_title'> <b>코드</b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='code' value='".$db->dt[code]."'  validation='false' title='코드' style='width:80px'>
		</td>
	</tr>
</table><br><br>

<table width='100%' cellpadding=0 cellspacing=0 border='0' align='center'>
<tr bgcolor=#ffffff >
<td colspan=4 align=center>
		<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;'>
	</td>
</tr>
</table><br><br>
</form>";


$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	<tr>
		<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
			<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>출고지 리스트</b>
		</td>
	</tr>
</table>";

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
	<col width='5%'>
	<col width='6%'>
	<col width='7%'>
	<col width='7%'>
	<col width=8%>
	<col width=8%>
	<col width=8%>
	<col width=*>
	<col width=10%>
	<col width=10%>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>코드</td>
		<td class='m_td'>출고지명</td>
		<td class='m_td'>담당자명</td>
		<td class='m_td'>전화번호</td>
		<td class='m_td'>핸드폰번호</td>
		<td class='m_td'>우편번호</td>
		<td class='m_td'>상세주소</td>
		<td class='m_td'>기본출고지여부</td>
		<td class='e_td'>관리</td>
	</tr>";

if(count($delivery_array) > 0){
	for($i=0;$i<count($delivery_array); $i++){

	$Contents01 .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td '>".$delivery_array[$i][code]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_name]."</td>
					<td class='list_box_td point' style='padding-left:10px; text-align:left;'>".$delivery_array[$i][person_name]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_phone]."</td>
					<td class='list_box_td'>".$delivery_array[$i][addr_mobile]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][zip_code]."</td>
					<td class='list_box_td '>".$delivery_array[$i][address_1]."<br>".$delivery_array[$i][address_2]."</td>
					<td class='list_box_td list_bg_gray'>".($delivery_array[$i][basic_addr_use])."</td>
					<td class='list_box_td ' nowrap>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents01 .="<a href='seller.add.php?addr_ix=".$delivery_array[$i][addr_ix]."&info_type=".$info_type."&company_id=".$company_id."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents01 .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents01 .="<a href='seller.act.php?addr_ix=".$delivery_array[$i][addr_ix]."&act=delivery_delete&company_id=".$company_id."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents01 .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents01 .="
					</td>
				</tr>";
	}
	$Contents01 .=	"</table>";
	$Contents01 .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$Contents01 .= "
				<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 촐고지 정보가 없습니다.</td></tr>";
}
$Contents01 .= "</table>";
$Contents01 .= "<table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}else{
	$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}
$Contents01 .="</table><br>";
$Contents01 = $Contents01;

}

if($info_type == "exchange_info"){

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	<tr>
		<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
			<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>교환/반품지 관리</b>
		</td>
	</tr>
</table>";

$Contents01 .= "
<form name='edit_form' action='delivery.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='iframe_act'><!--target='iframe_act' -->
	<input name='act' type='hidden' value='$act'>
	<input name='info_type' type='hidden' value='$info_type'>
	<input name='company_id' type='hidden' value='".$company_id."'>
	<input name='delivery_type' type='hidden' value='E'>
	<input name='mall_ix' type='hidden' value=''>
	<input name='addr_ix' type='hidden' value='$addr_ix'>
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
		<colgroup>
			<col width='18%' />
			<col width='32%' style='padding:0px 0px 0px 10px'/>
			<col width='18%' />
			<col width='32%' style='padding:0px 0px 0px 10px'/>
		</colgroup>
		<tr>
			<td class='input_box_title'> <b>교환/반품지명  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='addr_name' value='".$db->dt[addr_name]."'  style='width:200px' validation='true' title='교환/반품지명'>
			</td>
			<td class='input_box_title'> <b>담당자명  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='person_name' value='".$db->dt[person_name]."'  title='담당자명' validation='true' style='width:80px'>
			</td>
		</tr>

		<tr>
			<td class='input_box_title'> <b>일반 전화번호  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text name='addr_phone_1' id='addr_phone_1' value='".$addr_phone[0]."' maxlength=3 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='전화'> -
				<input type=text name='addr_phone_2' id='addr_phone_2' value='".$addr_phone[1]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='전화'> -
				<input type=text name='addr_phone_3' id='addr_phone_3' value='".$addr_phone[2]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='전화'>
			</td>
			<td class='input_box_title'> <b>핸드폰번호  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text name='addr_mobile_1' id='addr_mobile_1' value='".$addr_mobile[0]."' maxlength=3 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='핸드폰'> -
				<input type=text name='addr_mobile_2' id='addr_mobile_2' value='".$addr_mobile[1]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='핸드폰'> -
				<input type=text name='addr_mobile_3' id='addr_mobile_3' value='".$addr_mobile[2]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='true' title='핸드폰'>
			</td>
		</tr>
	    <tr>
	    <td class='input_box_title'> <b> 교환/반품 주소 <img src='".$required3_path."'> </b>    </td>
	    <td class='input_box_item' colspan=3>
	    	<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
	    	<div id='input_address_area' ><!--style='display:none;'-->
	    	<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='80px'>
				<col width='*'>
				<tr>
					<td height=26>
						<input type='text' class='textbox' name='com_zip' id='zip_b_1' style='width:60px;' maxlength='15' validation='true' title='교환/반품주소' value='".$db->dt[zip_code]."' readonly>
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[address_1]."' style='width:300px;'  validation='true' title='교환/반품주소' class='textbox'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[address_2]."' style='width:300px;'  validation='true' title='상세주소' class='textbox'> (상세주소)
					</td>
				</tr>
				</table>
			</div>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>기본 교환/반품지 사용  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=radio class='radio' name='basic_addr_use' value='Y' id='basic_addr_use_1'  validation='false' title='기본 교환/반품지 사용' ".($db->dt[basic_addr_use] == 'Y' || $db->dt[basic_addr_use] == ""?'checked':'')."> <label for='basic_addr_use_1'>사용</lable>
				<input type=radio class='radio' name='basic_addr_use' value='N' id='basic_addr_use_2'  validation='false' title='기본 교환/반품지 사용' ".CompareReturnValue("N",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_2'>미사용</lable>
			</td>
			<td class='input_box_title'> <b>코드</b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='code' value='".$db->dt[code]."'  validation='false' title='코드'  style='width:80px'>
			</td>
		</tr>
</table><br><br>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff >
	<td colspan=4 align=center>
		<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
	</td></tr>
</table><br><br>
</form>";

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	<tr>
		<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
			<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>교환/반품지 리스트</b>
		</td>
	</tr>
</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
	<col width='5%'>
	<col width='6%'>
	<col width='8%'>
	<col width='7%'>
	<col width=8%>
	<col width=8%>
	<col width=8%>
	<col width=*>
	<col width=11%>
	<col width=10%>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>코드</td>
		<td class='m_td'>교환/반품지명</td>
		<td class='m_td'>담당자명</td>
		<td class='m_td'>전화번호</td>
		<td class='m_td'>핸드폰번호</td>
		<td class='m_td'>우편번호</td>
		<td class='m_td'>상세주소</td>
		<td class='m_td'>기본 교환/반품지여부</td>
		<td class='e_td'>관리</td>
		</tr>";

if(count($delivery_array) > 0){
	for($i=0;$i<count($delivery_array); $i++){

		$Contents01 .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td '>".$delivery_array[$i][code]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_name]."</td>
					<td class='list_box_td point' style='padding-left:10px; text-align:left;'>".$delivery_array[$i][person_name]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_phone]."</td>
					<td class='list_box_td'>".$delivery_array[$i][addr_mobile]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][zip_code]."</td>
					<td class='list_box_td '>".$delivery_array[$i][address_1]."<br>".$delivery_array[$i][address_2]."</td>
					<td class='list_box_td list_bg_gray'>".($delivery_array[$i][basic_addr_use])."</td>
					<td class='list_box_td ' nowrap>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents01 .="<a href='seller.add.php?addr_ix=".$delivery_array[$i][addr_ix]."&info_type=".$info_type."&company_id=".$company_id."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents01 .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents01 .="<a href='seller.act.php?addr_ix=".$delivery_array[$i][addr_ix]."&act=delivery_delete&company_id=".$company_id."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents01 .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents01 .="
					</td>
				</tr>";
	}
	$Contents01 .=	"</table>";
	$Contents01 .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$Contents01 .= "
				<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 촐고지 정보가 없습니다.</td></tr>";
}
$Contents01 .= "</table>";
$Contents01 .= "<table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}else{
	$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}
$Contents01 .="</table><br>";
$Contents01 = $Contents01;

}

if($info_type == "visit_info"){

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	<tr>
		<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
			<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>방문수령지 관리</b>
		</td>
	</tr>
</table>";

$Contents01 .= "
<form name='edit_form' action='delivery.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='iframe_act'><!--target='iframe_act' -->
<input name='act' type='hidden' value='$act'>
<input name='info_type' type='hidden' value='$info_type'>
<input name='company_id' type='hidden' value='".$company_id."'>
<input name='delivery_type' type='hidden' value='V'>
<input name='mall_ix' type='hidden' value=''>
<input name='addr_ix' type='hidden' value='$addr_ix'>
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
		 <colgroup>
			<col width='18%' />
			<col width='32%' style='padding:0px 0px 0px 10px'/>
			<col width='18%' />
			<col width='32%' style='padding:0px 0px 0px 10px'/>
		  </colgroup>
		  <tr>
			<td class='input_box_title'> <b>방문수령지명  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='addr_name' value='".$db->dt[addr_name]."'  style='width:200px' validation='true' title='방문수령지명'>
			</td>
			<td class='input_box_title'> <b>담당자명  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='person_name' value='".$db->dt[person_name]."' validation=true title='담당자명' style='width:80px'>
			</td>
		  </tr>

		  <tr>
			<td class='input_box_title'> <b>일반 전화번호  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text name='addr_phone_1' id='addr_phone_1' value='".$addr_phone[0]."' maxlength=3 style='width:35px' class='textbox numeric' com_numeric=true validation='true' title='전화'> -
				<input type=text name='addr_phone_2' id='addr_phone_2' value='".$addr_phone[1]."' maxlength=4 style='width:35px' class='textbox numeric' com_numeric=true validation='true' title='전화'> -
				<input type=text name='addr_phone_3' id='addr_phone_3' value='".$addr_phone[2]."' maxlength=4 style='width:35px' class='textbox numeric' com_numeric=true validation='true' title='전화'>
			</td>
			<td class='input_box_title'> <b>핸드폰번호  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=text name='addr_mobile_1' id='addr_mobile_1' value='".$addr_mobile[0]."' maxlength=3 style='width:35px' class='textbox numeric' com_numeric=true validation='true' title='핸드폰'> -
				<input type=text name='addr_mobile_2' id='addr_mobile_2' value='".$addr_mobile[1]."' maxlength=4 style='width:35px' class='textbox numeric' com_numeric=true validation='true' title='핸드폰'> -
				<input type=text name='addr_mobile_3' id='addr_mobile_3' value='".$addr_mobile[2]."' maxlength=4 style='width:35px' class='textbox numeric' com_numeric=true validation='true' title='핸드폰'>
			</td>
		  </tr>
		 
	    <tr>
	    <td class='input_box_title'> <b>방문수령지 주소 <img src='".$required3_path."'> </b></td>
	    <td class='input_box_item' colspan=3>
	    	<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
	    	<div id='input_address_area' ><!--style='display:none;'-->
	    	<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='80px'>
				<col width='*'>
				<tr>
					<td height=26>
						<input type='text' class='textbox' name='com_zip' id='zip_b_1' style='width:60px;' validation='true' title='방문수령지주소'  maxlength='15' value='".$db->dt[zip_code]."' readonly>
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[address_1]."' style='width:300px;' validation='true' title='방문수령지주소' class='textbox'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[address_2]."' style='width:300px;' validation='true' title='상세주소' class='textbox'> (상세주소)
					</td>
				</tr>
				</table>
	    	</div>
	    	</td>
	  </tr>
	  <tr>
			<td class='input_box_title'> <b>기본 방문수령지 사용  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item'>
				<input type=radio class='radio' name='basic_addr_use' value='Y' id='basic_addr_use_1'  validation='false' title='기본 방문수령지 사용' ".($db->dt[basic_addr_use] == 'Y' || $db->dt[basic_addr_use] == ""?'checked':'')."> <label for='basic_addr_use_1'>사용</lable>
				<input type=radio class='radio' name='basic_addr_use' value='N' id='basic_addr_use_2'  validation='false' title='기본 방문수령지 사용' ".CompareReturnValue("N",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_2'>미사용</lable>
			</td>
			<td class='input_box_title'> <b>코드</b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='code' value='".$db->dt[code]."'  validation='false' title='코드'  style='width:80px'>
			</td>
		  </tr>
</table><br><br>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table><br><br>
</form>";

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	<tr>
		<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
			<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>방문수령지 리스트</b>
		</td>
	</tr>
</table>";

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
	<col width='5%'>
	<col width='6%'>
	<col width='7%'>
	<col width='7%'>
	<col width=8%>
	<col width=8%>
	<col width=8%>
	<col width=*>
	<col width=10%>
	<col width=10%>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>코드</td>
		<td class='m_td'> 방문수령지명</td>
		<td class='m_td'>담당자명</td>
		<td class='m_td'>전화번호</td>
		<td class='m_td'>핸드폰번호</td>
		<td class='m_td'>우편번호</td>
		<td class='m_td'>상세주소</td>
		<td class='m_td'>기본방문수령지여부</td>
		<td class='e_td'>관리</td>
		</tr>";

if(count($delivery_array) > 0){
	for($i=0;$i<count($delivery_array); $i++){

		$Contents01 .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td '>".$delivery_array[$i][code]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_name]."</td>
					<td class='list_box_td point' style='padding-left:10px; text-align:left;'>".$delivery_array[$i][person_name]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_phone]."</td>
					<td class='list_box_td'>".$delivery_array[$i][addr_mobile]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][zip_code]."</td>
					<td class='list_box_td '>".$delivery_array[$i][address_1]."<br>".$delivery_array[$i][address_2]."</td>
					<td class='list_box_td list_bg_gray'>".($delivery_array[$i][basic_addr_use])."</td>
					<td class='list_box_td ' nowrap>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents01 .="<a href='seller.add.php?addr_ix=".$delivery_array[$i][addr_ix]."&info_type=".$info_type."&company_id=".$company_id."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents01 .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents01 .="<a href='seller.act.php?addr_ix=".$delivery_array[$i][addr_ix]."&act=delivery_delete&company_id=".$company_id."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents01 .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents01 .="
					</td>
				</tr>";
	}
	$Contents01 .=	"</table>";
	$Contents01 .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$Contents01 .= "
				<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 촐고지 정보가 없습니다.</td></tr>";
}
$Contents01 .= "</table>";
$Contents01 .= "<table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}else{
	$Contents01 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}
$Contents01 .="</table><br>";
$Contents01 = $Contents01;

}

if($info_type != "factory_info" and $info_type != "exchange_info" and $info_type != "visit_info"){

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || $_SESSION["admininfo"]["mallstory_version"] == "service"){
		$ButtonString = "
		<table cellpadding=0 cellspacing=0 border='0' >
			<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
		</table>
		";
	}
}


if($company_id != "" && $info_type == "delivery_info"){
$ButtonString .= "<script type='text/javascript'>
	window.onload = function(){
		deliveryTypeView(".$db->dt[delivery_policy].");
		Content_Input();
		Init(document.edit_form);
	}
	function Content_Input(){
		document.edit_form.content.value = document.edit_form.delivery_policy_text.value;
	}
	function SubmitX(frm){
		if(!CheckFormValue(frm)){
			return false;
		}
		frm.content.value = iView.document.body.innerHTML;
		frm.content.value = frm.content.value.replace('<P>&nbsp;</P>','')
		//alert(frm.content.value);
		return true;
	}
</script>
";
}

if($company_id != "" && $info_type == "delivery_info"){
	$Contents = "<form name='edit_form' action='seller.act.php' method='post' onsubmit='return SubmitX(this)' enctype='multipart/form-data' style='display:inline;' target='act'>";
}else{
	$Contents = "<form name='edit_form' action='seller.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target=''>";
}

$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."
<input name='act' type='hidden' value='$act'>
<input name='mmode' type='hidden' value='$mmode'>
<input name='info_type' type='hidden' value='$info_type'>
<input type='hidden' name='cid2' value='$cid2'>
<input type='hidden' name='depth' value='$depth'>
<input name='company_id' type='hidden' value='".$company_id."'>
<input name='company_id_2' type='hidden' value='".$company_id."'>
<input name='com_type' type='hidden' value='".$db->dt[com_type]."'>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td align=center>".$ButtonString."</td></tr>";
$Contents = $Contents."</table></form><br>";

$Script = "<script language='javascript' src='company.add.js'></script>
<script language='JavaScript' src='../webedit/webedit.js'></script>
<script language='javascript'>
function zipcode(type) {
	var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function ChangeAddress(obj){
	if(obj.checked){
		document.getElementById('input_address_area').style.display = '';
	}else{
		document.getElementById('input_address_area').style.display = 'none';
	}
}

function loadRegion(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/region.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadBranch(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/branch.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadTeam(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/team.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadSellerManager(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/sellermanager.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function del_img(name,company_id){

	var select = confirm('삭제하시겠습니까?');

	if(select){
		$.ajax({
				url: 'company.act.php',
				type: 'get',
				dataType: 'html',
				data: {del : name, company_id : company_id, act : 'image_del'},
				success: function(result){
					document.location.reload();
				}
		});
	}
	else{
		return false;
	}
}

$(document).ready(function() {
	
	if($('input[name=commercial_disp]:checked').val()=='Y'){
		$('.wholesale_validation').each(function(){
			$(this).find('select,input').each(function(){
				$(this).attr('validation','true');
			})
		})
	}else{
		$('.wholesale_validation').each(function(){
			$(this).find('select,input').each(function(){
				$(this).attr('validation','false');
			})
		})
	}

	$('input[name=commercial_disp]').click(function(){
		if($(this).val()=='Y'){
			$('.wholesale_tr').show();
			$('.wholesale_validation').each(function(){
				$(this).find('select,input').each(function(){
					$(this).attr('validation','true');
				})
			})
			
		}else{
			$('.wholesale_tr').hide();
			$('.wholesale_validation').each(function(){
				$(this).find('select,input').each(function(){
					$(this).attr('validation','false');
				})
			})
		}
	})

	$('#seller_date').datepicker({
    dateFormat: 'yy-mm-dd',
    buttonImageOnly: true,
    buttonText: 'Kalender',
	});
/*
	$('#supply_vendor').click(function(){
		if($(this).prop('checked') == true){

		$('.supply_vendor_data').prop('disabled', false); 
		$('.supply_vendor_data').show();
		}else{
		$('.supply_vendor_data').hide();
		}
		
	});

	$('#sales_vendor').click(function(){
		if($(this).prop('checked') == true){
		$('.sales_vendor_data').prop('disabled', false); 
		$('.sales_vendor_data').show();
		}else{
		$('.sales_vendor_data').hide();
		}
	});

	if($('#supply_vendor').prop('checked') == true){
		$('.supply_vendor_data').show();
	}else{
		$('.supply_vendor_data').prop('disabled', true); 
	}

	if($('#sales_vendor').prop('checked') == true){
		$('.sales_vendor_data').show();
	}else{
		$('.sales_vendor_data').prop('disabled', true); 
	}

*/

	$('#check_all').click(function(){

		var value = $('#check_all').attr('checked');
		if(value == 'checked'){
			$('input[name=tax_person_name]').val($('input[name=customer_name]').val());
			$('input[name=tax_person_phone]').val($('input[name=customer_phone]').val());
			$('input[name=tax_person_mobile]').val($('input[name=customer_mobile]').val());
			$('input[name=tax_person_mail]').val($('input[name=customer_mail]').val());
			$('input[name=tax_person_position]').val($('input[name=customer_position]').val());
			$('textarea[name=tax_person_message]').val($('textarea[name=customer_message]').val());
		
		}else{
			$('input[name=tax_person_name]').val('');
			$('input[name=tax_person_phone]').val('');
			$('input[name=tax_person_mobile]').val('');
			$('input[name=tax_person_mail]').val('');
			$('input[name=tax_person_position]').val('');
			$('textarea[name=tax_person_message]').val('');
		}
	
	});

});



</script>

";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strContents = $Contents;
	$P->Navigation = "기초정보관리 > 거래처 관리 > $menu_name";
	$P->NaviTitle = "$menu_name";
	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = basic_menu();
	$P->strContents = $Contents;
	$P->Navigation = "기초정보관리 > 거래처 관리 > $menu_name";
	$P->title = "$menu_name";
	echo $P->PrintLayOut();
}

?>