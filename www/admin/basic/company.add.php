<?
include("../class/layout.class");
include_once("../store/md.lib.php");
include("../webedit/webedit.lib.php");
include ("../inventory/inventory.lib.php");

if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "본사등록";
}else{
	$menu_name = "본사등록";
}

if($info_type == ""){
	$info_type = "basic";
}

$db = new Database;
$db2 = new Database;
$cdb = new Database;
$pldb = new Database;

if($company_id == ""){
	$db->query("select * from ".TBL_COMMON_COMPANY_DETAIL." where com_type = 'A'");
	$db->fetch();
	$company_id = $db->dt[company_id];
	$company_code = $db->dt[company_id];
}

if($info_type == "basic"){

	$sql = "SELECT
				*
			FROM
				".TBL_COMMON_COMPANY_DETAIL." ccd 
				inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (ccd.company_id = cr.company_id)
			where 
				ccd.com_type = 'A'
				and ccd.company_id = '".$company_id."'";

	$db->query($sql);
	$db->fetch();
	$cid2 = $db->dt[relation_code];
	$pi_ix = $db->dt[pi_ix];
	
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

	if($cid2){
		$act = "update";
	}else{
		$act = "insert";
	}

}else if($info_type == "seller_info"){
	$sql = "SELECT * FROM ".TBL_COMMON_COMPANY_FILE." ccd where ccd.company_id = '".$company_id."' order by file_ix asc";
	$row = $db->query($sql);
	$seller_info_array = $db->fetchall();
	
	if(count($seller_info_array) >= 1){
		$act = "update";
	}else{
		$act = "insert";
	}
}else if($info_type == "delivery_info"){
	$sql = "SELECT * FROM ".TBL_COMMON_POS_INFO." ccd where ccd.company_id = '".$company_id."' ";
	$db->query($sql);
	$pos_info = $db->fetchall();
	
	$sql = "SELECT * FROM ".TBL_COMMON_POS_DETAIL." ccd where ccd.company_id = '".$company_id."' order by pos_detail_ix asc";
	$db->query($sql);
	$pos_detail = $db->fetchall();
	
	if(count($pos_info) >= 1 and count($pos_detail) >= 1 ){
		$act = "update";
	}else{
		$act = "insert";
	}
}

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
					<table id='tab_01' ".(($info_type == "basic" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=basic&company_id=".$company_id."&mmode=$mmode'>기본정보 입력</a> <span style='padding-left:2px' class='helpcloud' help_width='410' help_height='70' help_html='사업자 정보를 작성하여 등록한 후에 해당 사업자에 관리자화면 로그인 정보를 발급해 줄 수 있습니다.<br />사업자 정보 추가 후 [목록관리] - [사용자 추가]를 통해 관리자에 계정을 발급해 주시기 바랍니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "seller_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href=\"javascript:alert('사업자 정보를 먼저 입력하십시오.');\">관련 첨부파일 자료</a>";
						}else{
							$Contents01 .= "<a href='?info_type=seller_info&company_id=".$company_id."&mmode=$mmode'>관련 첨부파일 자료</a>";
						}
						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<!--
					<table id='tab_03' ".($info_type == "delivery_info" ? "class='on' ":"").">
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
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>";

if($info_type == "basic" || $info_type == ""){

	$com_zip = explode("-",$db->dt[com_zip]);
	$com_number = explode ("-",$db->dt[com_number]);	//사업자번호
	$corporate_number = explode ("-",$db->dt[corporate_number]);	//법인번호
	$com_phone = explode ("-",$db->dt[com_phone]);	//대표번호
	$com_mobile = explode ("-",$db->dt[com_mobile]);	//대표 핸드폰번호
	$com_fax = explode ("-",$db->dt[com_fax]);	//대표 팩스번호
	$open_date = explode (" ",$db->dt[open_date]);

$Contents01 .= "
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 본사등록</b> ".($company_id != "" ? "[ ".$db->dt[com_name]." : ".$company_id." ]" : "")." </div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />
	</colgroup>
	<tr>
	    <td class='input_box_title'> <b>본사코드</b> <img src='".$required3_path."'></td>
		<td class='input_box_item'>
		<input type=text name='company_code' value='".$db->dt[company_code]."' class='textbox point_color'  style='width:80px' validation='true' title='본사코드'>
		</td>
	    <td class='input_box_title'> <b>설립일</b></td>
		<td class='input_box_item'><input type=text id='open_date' name='open_date' value='".$open_date[0]."' class='textbox point_color'  style='width:80px' validation='false' title='설립일'></td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>사업자명(상호) <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type=text name='com_name' value='".$db->dt[com_name]."' class='textbox point_color'  style='width:200px' validation='true' title='사업자명(상호)'>
		</td>
		<td class='input_box_title'> <b>대표자명<img src='".$required3_path."'>  </b>   </td>
		<td class='input_box_item'><input type=text name='com_ceo' value='".$db->dt[com_ceo]."' class='textbox point_color'  style='width:80px' validation='true' title='대표자명'></td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>사업자 유형</b></td>
		<td class='input_box_item' colspan=3>
			<input type='radio' name='com_div' id='com_div_R' value='R' ".($db->dt[com_div] == "R" || $db->dt[com_div] == "" ? "checked":"")."> <label for='com_div_R'>법인사업자</label> &nbsp;&nbsp;
			<input type='radio' name='com_div' id='com_div_P' value='P' ".($db->dt[com_div] == "P" ? "checked":"")."> <label for='com_div_P'>일반사업자</label> &nbsp;&nbsp;
			<input type='radio' name='com_div' id='com_div_S' value='S' ".($db->dt[com_div] == "S" ? "checked":"")."> <label for='com_div_S'>간이과세자</label> &nbsp;&nbsp;
			<input type='radio' name='com_div' id='com_div_E' value='E' ".($db->dt[com_div] == "E" ? "checked":"")."> <label for='com_div_E'>면세사업자</label> &nbsp;&nbsp;
		</td>
	  <tr>
		<td class='input_box_title'> <b>거래처 유형 <img src='".$required3_path."'>  </b></td>
		<td class='input_box_item' colspan=3>
			<input type='checkbox' id = 'sales_vendor' name='sell_type[]' value='1' $checked_1> <label for='sales_vendor'>국내매출</label> &nbsp;
			<input type='checkbox' id = 'supply_vendor' name='sell_type[]' value='2' $checked_2> <label for='supply_vendor'>국내매입</label> &nbsp;
			<input type='checkbox' id = 'oversea_sales' name='sell_type[]' value='3' $checked_3> <label for='oversea_sales'>해외수출</label> &nbsp;
			<input type='checkbox' id = 'oversea_supply' name='sell_type[]' value='4' $checked_4> <label for='oversea_supply'>해외수입</label> &nbsp;
			<input type='checkbox' id = 'outsourcing' name='sell_type[]' value='5' $checked_5> <label for='outsourcing'>외주물류창고</label>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>사업 유형</b></td>
		<td class='input_box_item' >
			<input type='radio' name='business_type' id='business_O' value='O' ".($db->dt[business_type] == "O" ? "checked":"")."> <label for='business_O'>온라인</label> &nbsp;&nbsp;
			<input type='radio' name='business_type' id='business_F' value='F' ".($db->dt[business_type] == "F" ? "checked":"")."> <label for='business_F'>오프라인</label> &nbsp;&nbsp;
			<input type='radio' name='business_type' id='business_A' value='A' ".($db->dt[business_type] == "A" || $db->dt[business_type] == "" ? "checked":"")."> <label for='business_A'>온+오프라인</label> &nbsp;&nbsp;
		</td>
		<td class='input_box_title'> <b>물류창고사용여부 </b></td>
		<td class='input_box_item'>
			<input type='radio' id='is_wharehouse_11' name='is_wharehouse' value='1'  ".($db->dt[is_wharehouse] == "1" || $db->dt[is_wharehouse] == "" ? "checked":"")."> <label for='is_wharehouse_11'>사용</label> &nbsp;&nbsp;
			<input type='radio' id='is_wharehouse_22' name='is_wharehouse' value='0' ".($db->dt[is_wharehouse] == "0" ? "checked":"")." > <label for='is_wharehouse_22'>미사용</label> &nbsp;&nbsp;
		</td>
	</tr>
	<tr>
		<td class='input_box_title'><b>사업자등록번호 <img src='".$required3_path."'>  </b></td>
		<td class='input_box_item'>
			<input type=text name='com_number_1' id='com_number_1' value='".$com_number[0]."' maxlength=3 class='textbox numeric point_color' style='width:35px;' com_numeric=true validation='true' title='사업자등록번호'> -
			<input type=text name='com_number_2' id='com_number_2' value='".$com_number[1]."' maxlength=2 class='textbox numeric point_color' style='width:35px;' com_numeric=true validation='true' title='사업자등록번호'> -
			<input type=text name='com_number_3' id='com_number_3' value='".$com_number[2]."' maxlength=5 class='textbox numeric point_color' style='width:35px;' com_numeric=true validation='true' title='사업자등록번호'>
			<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
		</td>

		<td class='input_box_title'><b>법인번호</b></td>
		<td class='input_box_item'>
			<input type=text name='corporate_number_1' id='corporate_number_1' value='".$corporate_number[0]."' maxlength=6 style='width:50px;' class='textbox numeric' com_numeric=true validation='false' title='법인번호'> -
			<input type=text name='corporate_number_2' id='corporate_number_2' value='".$corporate_number[1]."' maxlength=7 style='width:60px;' class='textbox numeric' com_numeric=true validation='false' title='법인번호'> 
			<div style='display:inline;padding:2px;' class=small>예) XXXXXX-XXXXXXX</div>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>업태 <img src='".$required3_path."'>  </b>   </td>
		<td class='input_box_item'>
			<input type=text name='com_business_status' value='".$db->dt[com_business_status]."' class='textbox point_color'  style='width:200px' validation='true' title='업태'>
		</td>
		<td class='input_box_title' > <b>업종 <img src='".$required3_path."'>  </b>   </td>
		<td class='input_box_item'>
			<input type=text name='com_business_category' value='".$db->dt[com_business_category]."' class='textbox point_color'  style='width:200px' validation='true' title='업종'>
		</td>
	</tr>
	<tr height=90>
		<td class='input_box_title'> <b>주소 <img src='".$required3_path."'>  </b></td>
		<td class='input_box_item' colspan=3>
		<div id='input_address_area' >
			<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
			<col width='80px'>
			<col width='*'>
			<tr>
				<td height=26>
					<input type='text' class='textbox ' name='com_zip' id='zip_b_1' style='width:60px;' maxlength='15' value='".$db->dt[com_zip]."' readonly>
				</td>
				<td style='padding:1px 0 0 5px;'>
					<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
				</td>
			</tr>
			<tr>
				<td colspan=2 height=26>
					<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[com_addr1]."' size=50 class='textbox point_color'  style='width:450px'>
				</td>
			</tr>
			<tr>
				<td colspan=2 height=26>
					<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[com_addr2]."' size=70 class='textbox point_color'  style='width:450px'> (상세주소)
				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>대표번호 <img src='".$required3_path."'>  </b></td>
		<td class='input_box_item'>
			<input type=text name='com_phone_1' id='com_phone_1' value='".$com_phone[0]."' maxlength=3 style='width:35px;' class='textbox numeric point_color' com_numeric=true validation='false' title='전화'> -
			<input type=text name='com_phone_2' id='com_phone_2' value='".$com_phone[1]."' maxlength=4 style='width:35px;' class='textbox numeric point_color' com_numeric=true validation='true' title='전화'> -
			<input type=text name='com_phone_3' id='com_phone_3' value='".$com_phone[2]."' maxlength=4 style='width:35px;' class='textbox numeric point_color' com_numeric=true validation='true' title='전화'>
		</td>
		<td class='input_box_title'> <b>팩스</b></td>
		<td class='input_box_item'>
			<input type=text name='com_fax_1' id='com_mobile_1' value='".$com_fax[0]."' maxlength=3 style='width:35px;' class='textbox numeric' com_numeric=true validation='false' title='팩스'> -
			<input type=text name='com_fax_2' id='com_mobile_2' value='".$com_fax[1]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='false' title='팩스'> -
			<input type=text name='com_fax_3' id='com_mobile_3' value='".$com_fax[2]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='false' title='팩스'>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>대표 핸드폰번호</b></td>
		<td class='input_box_item'>
			<input type=text name='com_mobile_1' id='com_mobile_1' value='".$com_mobile[0]."' maxlength=3 style='width:35px;' class='textbox numeric' com_numeric=true validation='false' title='핸드폰'> -
			<input type=text name='com_mobile_2' id='com_mobile_2' value='".$com_mobile[1]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='false' title='핸드폰'> -
			<input type=text name='com_mobile_3' id='com_mobile_3' value='".$com_mobile[2]."' maxlength=4 style='width:35px;' class='textbox numeric' com_numeric=true validation='false' title='핸드폰'>
		</td>
		<td class='input_box_title'> <b>홈페이지</b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='com_homepage' value='".$db->dt[com_homepage]."'  style='width:250px'>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>대표이메일</b></td>
		<td class='input_box_item'>
			<input type=text name='com_email' value='".$db->dt[com_email]."' class='textbox'  style='width:200px' validation='false' title='대표이메일' email=true>
		</td>
		<td class='input_box_title'><b>통신판매업 번호</b></td>
		<td class='input_box_item'>
			<input type=text name='online_business_number' value='".$db->dt[online_business_number]."' class='textbox'  style='width:250px' validation='false' title='통신판매업 번호'>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'><b>사업자 인감도장 </b></td>";
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."/company_stamp_".$company_id.".gif")){
			$stamp_bool="false";
		}else{
			$stamp_bool="false";
		}
		$Contents01 .= "<td class='input_box_item' colspan=3>
			<table cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<td width='320'><input type=file name='stamp_file' size=70 class='textbox'  style='width:300px' validation='$stamp_bool' title='사업자 인감도장'></td>";
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id."/company_stamp_".$company_id.".gif")){
	$Contents01 .= "<td width='*' style='padding:5px 0px;'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/company_stamp_".$company_id.".gif' width=50>
					</td>
					<td>
						<span style='padding-left:20px;'><b>&nbsp;&nbsp;2M 이하만 등록가능</b></span>
						<a href='javascript:' onclick=\"del_img('stamp_img','".$company_id."');\"><img src='../images/korea/btn_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:20px;' title='삭제'></a>
					</td>";
			}

$Contents01 .= "</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>본사등록승인</b>    </td>
		<td class='input_box_item' colspan=3>
			<input type=radio name='seller_auth' id='seller_auth_N' value='N' ".CompareReturnValue("N",$db->dt[seller_auth],"checked")." ".CompareReturnValue("",$db->dt[seller_auth],"checked")."><label for='seller_auth_N'>승인대기</label>
			<input type=radio name='seller_auth' id='seller_auth_Y' value='Y'  ".CompareReturnValue("Y",$db->dt[seller_auth],"checked")."><label for='seller_auth_Y'>승인</label>
			<input type=radio name='seller_auth' id='seller_auth_X' value='X' ".CompareReturnValue("X",$db->dt[seller_auth],"checked")."><label for='seller_auth_X'>승인거부</label>
			 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'><!--입점업체 승인후에 사용자 등록이 가능합니다. --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>
		</td>
	</tr>
	</table><br><br>";

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
			<table width='100%' cellpadding=0 cellspacing=0 border='0'>
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
		<img src='../image/emo_3_15.gif' align=a	bsmiddle> 해당거래처와의 기본 계좌 정보를 입력합니다.
	</td>
</tr>
</table>
";

if($info_type == "delivery_info"){
$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 입점업체별 기본 배송정책 및 수수료</b></div>")."</td>
	</tr>
	</table>
";
	
$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  >
	<col width='15%' />
	<col width='85%' />
	<tr bgcolor=#ffffff height=34>
		<td class='input_box_title'> <b>POS1</b>  </td>
		<td class='input_box_item'>
		<input type='hidden' name='pos[0][pos_detail_ix]' value='".$pos_detail[0][pos_detail_ix]."'>
		<input type='radio' name='pos[0][pos_use]' id='pos0_N' value='N'  ".($pos_detail[0][pos_use] == "N" || $pos_detail[0][pos_use] == "" ? "checked":"")."> &nbsp;&nbsp;<label for='pos0_N'> 미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='pos[0][pos_use]' id='pos0_Y' value='Y' ".($pos_detail[0][pos_use] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='pos0_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[0][pos_code]' value='".$pos_detail[0][pos_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부POS코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[0][pos_outcode]' value='".$pos_detail[0][pos_outcode]."'>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=34>
		<td class='input_box_title'> <b>POS2</b>  </td>
		<td class='input_box_item'>
		<input type='hidden' name='pos[1][pos_detail_ix]' value='".$pos_detail[1][pos_detail_ix]."'>
		<input type='radio' name='pos[1][pos_use]' id='pos1_N' value='N' ".($pos_detail[1][pos_use] == "N" || $pos_detail[0][pos_use] == "" ? "checked":"")."> &nbsp;&nbsp; <label for='pos1_N'> 미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='pos[1][pos_use]' id='pos1_Y' value='Y' ".($pos_detail[1][pos_use] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='pos1_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[1][pos_code]' value='".$pos_detail[1][pos_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부POS코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[1][pos_outcode]' value='".$pos_detail[1][pos_outcode]."'>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=34>
		<td class='input_box_title'> <b>POS3</b>  </td>
		<td class='input_box_item'>
		<input type='hidden' name='pos[2][pos_detail_ix]' value='".$pos_detail[2][pos_detail_ix]."'>
		<input type='radio' name='pos[2][pos_use]' id='pos2_N' value='N' ".($pos_detail[2][pos_use] == "N" || $pos_detail[0][pos_use] == "" ? "checked":"")."> &nbsp;&nbsp; <label for='pos2_N'>미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='pos[2][pos_use]' id='pos2_Y' value='Y' ".($pos_detail[2][pos_use] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='pos2_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[2][pos_code]' value='".$pos_detail[2][pos_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부POS코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[2][pos_outcode]' value='".$pos_detail[2][pos_outcode]."'>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=34>
		<td class='input_box_title'> <b>POS4</b>  </td>
		<td class='input_box_item'>
		<input type='hidden' name='pos[3][pos_detail_ix]' value='".$pos_detail[3][pos_detail_ix]."'>
		<input type='radio' name='pos[3][pos_use]' id='pos3_N' value='N' ".($pos_detail[3][pos_use] == "N" || $pos_detail[0][pos_use] == "" ? "checked":"")."> &nbsp;&nbsp; <label for='pos3_N'>미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='pos[3][pos_use]' id='pos3_Y' value='Y' ".($pos_detail[3][pos_use] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='pos3_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[3][pos_code]' value='".$pos_detail[3][pos_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부POS코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[3][pos_outcode]' value='".$pos_detail[3][pos_outcode]."'>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=34>
		<td class='input_box_title'> <b>POS5</b>  </td>
		<td class='input_box_item'>
		<input type='hidden' name='pos[4][pos_detail_ix]' value='".$pos_detail[4][pos_detail_ix]."'>
		<input type='radio' name='pos[4][pos_use]' id='pos4_N' value='N' ".($pos_detail[4][pos_use] == "N" || $pos_detail[0][pos_use] == "" ? "checked":"")."> &nbsp;&nbsp; <label for='pos4_N'>미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='pos[4][pos_use]' id='pos4_Y' value='Y'  ".($pos_detail[4][pos_use] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='pos4_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[4][pos_code]' value='".$pos_detail[4][pos_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부POS코드&nbsp;&nbsp;<input class='textbox' type='text' name='pos[4][pos_outcode]' value='".$pos_detail[4][pos_outcode]."'>
		</td>
	</tr>
	</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='85%' />
	<tr><td height='20'></td></tr>
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 외부 솔루션 설정</b></div>")."</td>
	</tr>
	</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  >
	<col width='15%' />
	<col width='85%' />
	<tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>외부 연결 솔루션</b>  </td>
	    <td class='input_box_item'>
	    <input type='radio' name='solution_yn' id='solution_yn_N' value='N' ".($pos_info[0][solution_yn] == "N" || $pos_info[0][solution_yn] == "" ? "checked":"")."> &nbsp;&nbsp;<label for='solution_yn_N'>미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='solution_yn' id='solution_yn_Y' value='Y' ".($pos_info[0][solution_yn] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='solution_yn_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		코드&nbsp;&nbsp;<input class='textbox' type='text' name='solution_code' value='".$pos_info[0][solution_code]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		연동할 외부코드&nbsp;&nbsp;<input class='textbox' type='text' name='solution_coutcode' value='".$pos_info[0][solution_coutcode]."'>
		</td>
	</tr>
	</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='85%' />
	<tr><td height='20'></td></tr>
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 자사 사이트 연동</b></div>")."</td>
	</tr>
	</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  >
	<col width='15%' />
	<col width='85%' />
	<tr bgcolor=#ffffff height=34>
		<td class='input_box_title'> <b>회원간 연동</b>  </td>
		<td class='input_box_item'>
		<input type='radio' name='site_yn' id='site_yn_N' value='N' ".($pos_info[0][site_yn] == "N" || $pos_info[0][site_yn] == ""? "checked":"")."> &nbsp;&nbsp;<label for='site_yn_N'>미사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='radio' name='site_yn' id='site_yn_Y' value='Y' ".($pos_info[0][site_yn] == "Y" ? "checked":"")."> &nbsp;&nbsp;<label for='site_yn_Y'>사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
		ID&nbsp;&nbsp;<input class='textbox' type='text' name='site_id' value='".$pos_info[0][site_id]."'>&nbsp;&nbsp;&nbsp;&nbsp;
		회원코드&nbsp;&nbsp;<input class='textbox' type='text' name='site_pw' value='".$pos_info[0][site_pw]."'>
		</td>
	</tr>
		<input type='hidden' name='pos_info_ix' value='".$pos_info[0][pos_info_ix]."'>
	</table><br><br>";
}

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
 $ButtonString = "
	<table height='40' cellpadding=0 cellspacing=0 border='0' >
		<tr bgcolor=#ffffff >
		<td align=center>
		<a href='javascript:bak_page();'>
		<img src='../images/".$admininfo["language"]."/btn_prevpage.gif' border=0 style='cursor:pointer;border:0px;' ></a></td>
		<td style='padding-left:10px;' align=center>
		<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0  style='cursor:pointer;border:0px;' ></td>
		</tr>
	</table>";
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
	$Contents = "<form name='edit_form' action='../basic/company.act.php' method='post' onsubmit='return SubmitX(this)' enctype='multipart/form-data' style='display:inline;' target='act'>";
} else {
	$Contents = "<form name='edit_form' action='../basic/company.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='act'>";
}

$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."
<input name='act' type='hidden' value='$act'>
<input name='mmode' type='hidden' value='$mmode'>
<input name='info_type' type='hidden' value='$info_type'>
<input name='pi_ix' type ='hidden' value='$pi_ix'>
<input name='company_id' type='hidden' value='".$company_id."'>
<input name='com_type' type='hidden' value='A'>";

//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td align=center>".$ButtonString."</td></tr>";
$Contents = $Contents."</table></form><br><br>";
$Script = "<script language='javascript' src='../basic/company.add.js'></script>
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

$(function() {
	$('#open_date').datepicker({
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: 'Kalender',
	});
});

</script>";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = basic_menu();
	$P->strContents = $Contents;
	$P->Navigation = "기초정보관리 > 본사관리 > $menu_name";
	$P->NaviTitle = "$menu_name";
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = basic_menu();
	$P->strContents = $Contents;
	$P->Navigation = "기초정보관리 > 본사관리 > $menu_name";
	$P->title = "$menu_name";
	echo $P->PrintLayOut();
}

?>