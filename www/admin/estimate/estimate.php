<?
include("../class/layout.class");
include_once("../store/md.lib.php");
include("../webedit/webedit.lib.php");
//include("./company.lib.php");
include ("../inventory/inventory.lib.php");

if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "견적서 작성";
}else{
	$menu_name = "견적서 작성";
}

if($info_type == ""){
	$info_type = "basic";
}

// current time


$db = new Database;
$db2 = new Database;
$cdb = new Database;
$pldb = new Database;
$mdb = new Database;

$sql = "SELECT 
			COUNT(*) as total
		FROM 
			common_seller_detail csd , common_seller_delivery csdv , common_company_detail ccd
		where
			com_type = 'S' 
			and csd.company_id = csdv.company_id
			and csd.company_id = ccd.company_id";

$db->query($sql);
$db->fetch();
$vendor_total = $db->dt[total];

if($est_ix){
	if($oid){
	$sql = "select
				e.*,
				ed.*,
				ccd.*,
				ccd.com_name as company,
				cu.id as buser_id,
				AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as bname,
				AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as bmail,
				AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as bzip,
				AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as baddr1,
				AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as baddr2,
				AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as btel,
				AES_DECRYPT(UNHEX(cmd.com_tel),'".$db->ase_encrypt_key."') as com_tel,
				AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as bmobile,
				g.gp_name as bmem_group,
				o.order_date as open_date
			from
					shop_estimates as e
					inner join shop_estimates_detail as ed on (e.est_ix = ed.est_ix)
					inner join shop_order as o on (e.est_ix = o.est_ix)
					inner join common_user as cu on (o.user_code = cu.code)
					inner join common_member_detail as cmd on (cu.code = cmd.code)
					left join common_company_detail as ccd on (cu.company_id = ccd.company_id)
					left join shop_groupinfo as g on (cmd.gp_ix = g.gp_ix)
				where
					e.est_ix = '".$est_ix."'";

	}else{
	$sql = "select
				e.*,
				ed.*
			from
				shop_estimates as e
				left join shop_estimates_detail as ed on (e.est_ix = ed.est_ix)
			where
				e.est_ix = '".$est_ix."'";
	}
	$act = 'update';
}else{
	$act = 'insert';
}

$db->query($sql);
$db->fetch();

$com_zip = explode("-",$db->dt[com_zip]);
$com_number = explode ("-",$db->dt[com_number]);				//사업자번호
$corporate_number = explode ("-",$db->dt[corporate_number]);	//법인번호
$com_phone = explode ("-",$db->dt[com_phone]);					//대표번호
$com_mobile = explode ("-",$db->dt[com_mobile]);				//대표 핸드폰번호
$com_fax = explode ("-",$db->dt[com_fax]);						//대표 팩스번호
$open_date = explode(" ",$db->dt[open_date]);					//설립일
$tel = explode ("-",$db->dt[btel]);	//의뢰인 전화번호
$mobile = explode ("-",$db->dt[bmobile]);	//의뢰인 전화번호
$bzip = explode ("-",$db->dt[bzip]);	//의뢰인 전화번호
$plan_date = explode (" ",$db->dt[plan_date]);	//의뢰인 전화번호
$mall_ix = $admininfo[mall_ix];
$v15ago = date("Y-m-d", time()+86400*15);
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "견적서 관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 견적정보</b>  </div>")."</td>
	  </tr>
	  </table>";

$Contents01 .= "
	  <table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	  <colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	  </colgroup>

	  <tr>
		<td class='input_box_title'> <b>견적서 제목 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type=text name='estimate_title' value='".$db->dt[estimate_title]."' class='textbox'  style='width:300px' validation='true' title='견적서 제목'>
		</td>
		<td class='input_box_title'> <b>처리상태 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<select name='status' validation='true'>
				<option value=''>선택</option>
				<option value='2' ".($db->dt[status] == '2'?"selected":"").">견적대기</option>
				<option value='3' ".($db->dt[status] == '3'?"selected":"").">견적취소</option>
				<option value='4' ".($db->dt[status] == '4'?"selected":"").">견적진행중</option>
				<option value='5' ".($db->dt[status] == '5'?"selected":"").">견적기간만료</option>
				<option value='7' ".($db->dt[status] == '7'?"selected":"").">견적확정</option>
		</td>
	  </tr>

	  <tr>
	    <td class='input_box_title'> <b>견적서 번호 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type=text name='estimate_code' value='".$db->dt[estimate_code]."' class='textbox'  style='width:200px' validation='true' title='견적서 번호'>
		</td>
	    <td class='input_box_title'> <b>견적의뢰일자</b>   </td>
		<td class='input_box_item'><input type=text  id='open_date' name='open_date' value='".$open_date[0]."' class='textbox'  style='width:80px' validation='false' title='견적의뢰일자' ></td>
	  </tr>
	  	<tr height='30' id='move_status_tr'>
			<td class='input_box_title'>견적서 분류 </td>
			<td class='input_box_item'>
				<!--<input type=radio name='estimate_type' id='estimate_type_1' value='1' ".($db->dt[estimate_type] == '1' ?"checked":"")."><label for='estimate_type_1'>맞춤 견적서</label>
				<input type=radio name='estimate_type' id='estimate_type_2' value='2' ".($db->dt[estimate_type] == '2'?"checked":"")."><label for='estimate_type_2'>선택 견적서</label>-->
				<input type=radio name='estimate_type' id='estimate_type_3' value='3' ".($db->dt[estimate_type] == '3'  || $db->dt[estimate_type] == ''?"checked":"")."><label for='estimate_type_3'>전문가 견적서</label>
				<input type=radio name='estimate_type' id='estimate_type_4' value='4' ".($db->dt[estimate_type] == '4'?"checked":"")."><label for='estimate_type_4'>자유 견적서</label>
			</td>
			<td class='search_box_title' > 프론트 전시 구분</td>
			<td class='search_box_item' colspan=1 style='padding:10px;'><div style='margin-bottom:3px;'>".GetDisplayDivision($mall_ix, "select")."</div><div class=small>전시 구분을 선택하실경우 해당 사이트에 노출됩니다.</div></td>
		</tr>
		<tr height='30' id='move_status_tr'>
			<td class='input_box_title'>견적서 유형 </td>
			<td class='input_box_item' colspan=3> 
				<input type=radio name='estimate_div' id='estimate_div_2' value='2' ".($db->dt[estimate_div] == '2'  || $db->dt[estimate_div] == ''?"checked":"")."><label for='estimate_div_2'>대량구매문의</label>
			</td>
		</tr>";

	if($est_ix){
	$Contents01 .= "
		<tr height='30' id='move_status_tr'>
			<td class='input_box_title'>의뢰인 성명 </td>
			<td class='input_box_item'>
				<input type='hidden' name='ucode' value='".$db->dt[ucode]."'>
				<input type='text' name='bname' class='textbox' id='name_a' size='27' maxlength='20' class='textbox' value='".$db->dt[bname]."' validation='true'  title='의뢰인 성명'  >
			</td>
			<td class='input_box_title' width='100'>아이디/회원등급</td>
				<td class='input_box_item'>
				<input type='text' name='buser_id' class='textbox' id='buser_id' size='15' maxlength='30' class='textbox' value='".$db->dt[buser_id]."' validation='true'  title='의뢰인 아이디'  > /
				<input type='text' name='bmem_group' class='textbox' id='bmem_group' size='10' maxlength='20' class='textbox' value='".$db->dt[bmem_group]."' validation='true'  title='의뢰인 회원등급'  >
				</td>
		</tr>";
	}else{
	$Contents01 .= "
			<tr height=30 class='border'>
				<td class='input_box_title' width='100'>의뢰인 성명</td>
				<td class='input_box_item'> 
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
							<input type='hidden' name='ucode'>
							<input type='text' name='bname' class='textbox' id='name_a' size='27' maxlength='20' class='textbox' value='".$db->dt[bname]."' validation='true'  title='의뢰인 성명'  >
							</td>
							<td style='padding:0px 4px;'>
							<img src='../images/".$admininfo["language"]."/bts_search_id.gif' align=absmiddle border=0 onclick='idsearch();' style='cursor:pointer;'>
							</td>
						</tr>
					</table>
				</td>
				<td class='input_box_title' width='100'>아이디/회원등급</td>
				<td class='input_box_item'>
				<input type='text' name='buser_id' class='textbox' id='buser_id' size='10' maxlength='20' class='textbox' value='".$db->dt[buser_id]."' validation='true'  title='의뢰인 아이디'  > /
				<input type='text' name='bmem_group' class='textbox' id='bmem_group' size='10' maxlength='20' class='textbox' value='".$db->dt[bmem_group]."' validation='true'  title='의뢰인 회원등급'  >
				</td>
			</tr>";
	}

$Contents01 .= "
		<tr>
			<td class='input_box_title'>의뢰인 이메일</td>

			<td class='input_box_item'><input type='text' name='bmail' id='mail_a' size='45' maxlength='100' class='textbox' value='".$db->dt[bmail]."' validation='true' title='이메일' email='true'>
			</td>
			<td class='list input_box_title'>의뢰인 전화번호</td>
			<td class='input_box_item'>
				<input type='text' name='btel_1' id='tel1_a' size='5' maxlength='3' class='textbox numeric' value='".$tel[0]."' validation='false' title='전화번호'> -
				<input type='text' name='btel_2' id='tel2_a' size='5' maxlength='4' class='textbox numeric' value='".$tel[1]."' validation='false' title='전화번호'> -
				<input type='text' name='btel_3' id='tel3_a' size='5' maxlength='4' class='textbox numeric' value='".$tel[2]."' validation='false' title='전화번호'>
			</td>
		</tr>
		<tr>
			<td class='list input_box_title'>의뢰인 핸드폰</td>
			<td class='input_box_item'>
				<input type='text' name='bmobile_1' id='pcs1_a' size='5' maxlength='3' class='textbox numeric' value='".$mobile[0]."' validation='true' title='핸드폰번호' numeric='true'> -
				<input type='text' name='bmobile_2' id='pcs2_a' size='5' maxlength='4' class='textbox numeric' value='".$mobile[1]."' validation='true' title='핸드폰번호' numeric='true'> -
				<input type='text' name='bmobile_3' id='pcs3_a' size='5' maxlength='4' class='textbox numeric' value='".$mobile[2]."' validation='true' title='핸드폰번호' numeric='true'></td>
			<td class='input_box_title'> <b>견적만료일</b>   </td>
			<td class='input_box_item'><input type=text  id='plan_date' name='plan_date' value='".($plan_date[0]?$plan_date[0]:$v15ago)."' class='textbox'  style='width:80px' validation='false' title='견적만료일'></td>
		</tr>
		<tr>
			<td class='list input_box_title'>주문자 주소</td>
			<td class='input_box_item'style='padding:5px 5px;' colspan=3>
			<input type='text' name='zipcode1' class='textbox' id='zipcode1_b'  size='10' maxlength='7' class='textbox' value='".$db->dt[bzip]."' validation='true' title='우편번호' >
			<img src='../images/".$admininfo["language"]."/btn_search_address.gif' align=absmiddle border=0 onClick=\"zipcode('2')\"  style='cursor:pointer;'><!--input type='button' value='주소 찾기'  class='box' onClick=\"zipcode('2')\"  alt='주소 찾기'--><br />
			<input type='text' name='addr1' id='addr1_b' size='80' maxlength='80' class='textbox' style='margin-top:3px; margin-bottom:3px;' value='".$db->dt[baddr1]."' validation='true' title='주소' ><br />
			<input type='text' name='addr2' id='addr2_b' size='80' maxlength='80' class='textbox' value='".$db->dt[baddr2]."' validation='true' title='세부주소'> 세부주소</td>
		</tr>
		<tr>
		<td class='input_box_title'><b>첨부파일 </b></td>";
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/".$db->dt[est_ix]."/".$db->dt[bfile])) $stamp_bool="false";
		else $stamp_bool="false";

		//echo "$stamp_bool";
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/".$db->dt[est_ix]."/";
		$Contents01 .= "<td class='input_box_item' colspan=3>
			<table cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<td width='320'><input type=file name='estimates_file' size=70 class='textbox'  style='width:300px' value='".$db->dt[bfile]."' validation='$stamp_bool' title='첨부파일'></td>";
				if(is_file($path.$db->dt[bfile])){
					$Contents01 .= "
							<td width='100%'>&nbsp;&nbsp;
							[ <a onclick=\"download_img('".$path.$db->dt[bfile]."')\" class='textbox' style='cursor: pointer;'>
							 ".$db->dt[bfile]." </a> ]
							<a href='javascript:' onclick=\"del('".$db->dt[bfile]."','".$est_ix."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
							</td>";
				}

$Contents01 .= "</tr>
			</table>
		</td>
	  </tr>
	</table><br>
		";
$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "사업자 정보 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 사업자 정보</b>  </div>")."</td>
	  </tr>
	  </table>";


$Contents01 .= "
	  <table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	  <colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	  </colgroup>
	<tr class='br'>
	    <td class='input_box_title'> <b>사업자명(상호)</b></td>
		<td class='input_box_item'>
		<input type=text name='company' id='company' value='".$db->dt[company]."' class='textbox'  style='width:200px' validation='false' title='사업자명(상호)'>
		</td>
		<td class='input_box_title'><b>사업자번호 </b></td>
		<td class='input_box_item'>
			<input type=text name='com_number_1' id='com_number_1' value='".$com_number[0]."' maxlength=3 size=5  class='textbox numeric' com_numeric=true validation='false' title='사업자번호'> -
			<input type=text name='com_number_2' id='com_number_2' value='".$com_number[1]."' maxlength=2 size=5 class='textbox numeric' com_numeric=true validation='false' title='사업자번호'> -
			<input type=text name='com_number_3' id='com_number_3' value='".$com_number[2]."' maxlength=5 size=5 class='textbox numeric' com_numeric=true validation='false' title='사업자번호'>
			<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
		</td>
	    
	  </tr> 
	  <tr class='br'>
		<td class='input_box_title'> <b>대표자명</b>   </td>
		<td class='input_box_item'><input type=text name='com_ceo' id='com_ceo' value='".$db->dt[com_ceo]."' class='textbox'  style='width:200px' validation='false' title='대표자명'></td>
	    <td class='input_box_title'> <b>사업자 유형</b></td>
		<td class='input_box_item'>
			<input type='radio' name='com_div' id='com_div_R' value='R'  ".($db->dt[com_div] == "R" || $db->dt[com_div] == "" ? "checked":"")."> <label for='com_div_R'>법인사업자</label> &nbsp;&nbsp;
			<input type='radio' name='com_div' id='com_div_P' value='P'  ".($db->dt[com_div] == "P" ? "checked":"")."> <label for='com_div_P'> 개인사업자</label> &nbsp;&nbsp;
			<input type='radio' name='com_div' id='com_div_S' value='S'  ".($db->dt[com_div] == "S" ? "checked":"")."> <label for='com_div_S'>개인 ( 간이과세자 )</label> &nbsp;&nbsp;
		</td>
	  </tr>
	  <tr class='br'>
	    <td class='input_box_title'> <b>업태</b>   </td>
		<td class='input_box_item'><input type=text name='com_business_status' value='".$db->dt[com_business_status]."' class='textbox'  style='width:200px' validation='false' title='업태'></td>
	     <td class='input_box_title'> <b>업종</b>   </td>
		<td class='input_box_item'><input type=text name='com_business_category' value='".$db->dt[com_business_category]."' class='textbox'  style='width:200px' validation='false' title='업종'></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>대표번호</b></td>
		<td class='input_box_item'>
			<input type=text name='com_phone_1' id='com_phone_1' value='".$com_phone[0]."' maxlength=4 size=4  class='textbox numeric' com_numeric=true validation='false' title='대표번호'> -
			<input type=text name='com_phone_2' id='com_phone_2' value='".$com_phone[1]."' maxlength=4 size=4 class='textbox numeric' com_numeric=true validation='false' title='대표번호'> -
			<input type=text name='com_phone_3' id='com_phone_3' value='".$com_phone[2]."' maxlength=4 size=4 class='textbox numeric' com_numeric=true validation='false' title='대표번호'>
		</td>
	    <td class='input_box_title'> <b>팩스번호</b></td>
		<td class='input_box_item'>
			<input type=text name='com_fax_1' id='com_mobile_1' value='".$com_fax[0]."' maxlength=4 size=4  class='textbox numeric' com_numeric=true validation='false' title='팩스'> -
			<input type=text name='com_fax_2' id='com_mobile_2' value='".$com_fax[1]."' maxlength=4 size=4 class='textbox numeric' com_numeric=true validation='false' title='팩스'> -
			<input type=text name='com_fax_3' id='com_mobile_3' value='".$com_fax[2]."' maxlength=4 size=4 class='textbox numeric' com_numeric=true validation='false' title='팩스'>
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>주소</b></td>
	    <td class='input_box_item' colspan=3>
	    	<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
	    	<div id='input_address_area' ><!--style='display:none;'-->
	    	<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='120px'>
				<col width='*'>
				<tr>
					<td height=26>
						<input type='text' class='textbox' name='com_zip' id='zip_b_1' size='15' maxlength='15' value='".$db->dt[com_zip]."' readonly>
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[com_addr1]."' size=50 class='textbox'  style='width:75%'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[com_addr2]."' size=70 class='textbox'  style='width:450px'> (상세주소)
					</td>
				</tr>
				</table>
	    	</div>
	    	</td>
	  </tr>
	</table><br>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "견적서 관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 견적정보</b>  </div>")."</td>
	  </tr>
	  </table>";

$Contents01 .= "
	  <table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	  <colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	  </colgroup>
	  <tr height='30'>
		<td class='input_box_title' ><b>비고(견적요청사항)</b></td>
		<td class='input_box_item'  colspan=3 style='padding:5px;'>";
			if($info_type == "text" && false){
				$Contents01 .= "".$db->dt[etc]."<input type=hidden class='textbox' name='etc' value='".$db->dt[etc]." ' id=order_etc style='width:98%'>";
			}else{
				$Contents01 .= "<textarea  class='textbox' name='etc'  id=order_etc style='width:97%;height:50px;'>".$db->dt[etc]."</textarea>";
			}
			$Contents01 .= "
		</td>
	</tr>
	</table><br>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("관리자 상담내용", "견적서 관리 > 관리자 상담내용")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 관리자 상담내용</b>  </div>")."</td>
	  </tr>
	  </table>";

$Contents01 .= "
	  <table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	  <colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	  </colgroup>
	  <tr height='30'>
		<td class='input_box_title' ><b>관리자 상담내용</b></td>
		<td class='input_box_item'  colspan=3 style='padding:5px;'>";
			$Contents01 .= "<textarea  class='textbox' name='admin_etc'  id=order_etc style='width:97%;height:50px;'>".$db->dt[admin_etc]."</textarea>";
			$Contents01 .= "
		</td>
	</tr>
	</table><br>";

/*
$Contents01 .="<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=30>
					<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='vertical-align:middle;'><b style='vertical-align:middle;' class=blk> 상품리스트</b> <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,1,'productList_1','list2','estimate');\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a></div>")."</td>
					</tr>
					</table>";
*/
$Contents01 .="<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=30>
					<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='vertical-align:middle;'><b style='vertical-align:middle;' class=blk> 상품리스트</b> <a href=\"javascript:\" onclick=\"product_search('".$est_ix."');\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a></div>")."</td>
					</tr>
					</table>";

$Contents01 .="<table border=0 cellpadding=0 cellspacing=0 width='100%'>
				<tr bgcolor='#ffffff'>
					<td style='height:300px;vertical-align:top;' id='group_product_area_1'>
						".relationProductList($est_ix, "clipart",$act)."
						<div style='clear:both;width:100%;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> </span></div>
					</td>
				</tr>
				<tr bgcolor='#F8F9FA'>
				<td colspan=2>";
$Contents01 .= "</td>
				</tr>
			</table><br>";


if(!$oid){
$ButtonString .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'>";
}
$Contents = "<form name='order_form' action='./estimate.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target=''>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."
<input name='act' type='hidden' value='$act'>
<input name='mmode' type='hidden' value='$mmode'>
<input type='hidden' name='depth' value='$depth'>
<input type='hidden' name='est_ix' value='$est_ix'>
<input name='info_type' type='hidden' value='$info_type'>";

//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td align=center>".$ButtonString."</td></tr>";
$Contents = $Contents."</table></form><br><br>";


$Script = "<script type='text/javascript' src='../js/ms_productSearch.js'></script>
<script language='javascript' src='../basic/company.add.js'></script>
<script type='text/javascript' src='../estimate/estimate.js'></script>
<script language='javascript'>
function zipcode(type) {
	var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function idsearch() {
	var zip = window.open('../order/manual_order.searchuser.php?page_type=estimates','','width=440,height=400,scrollbars=yes,status=no');
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

function loadperson(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var key = sel.getAttribute('name');

	window.frames['act'].location.href = './person.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&key='+key;

}

function del(name,company_id){

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

	$('#open_date').datepicker({
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: 'Kalender',
	});

	$('#plan_date').datepicker({
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: 'Kalender',
	});

});

function product_search(est_ix){

	if(!est_ix){
		alert('견적서 기본정보 저장후 상품추가해 주세요.');
		return false;
	}
	ShowModalWindow('../estimate/product_search.php?company_id=&surtax_yorn=',800,700,'product_search',true);

}

function calcurate_maginrate(product_id){

	var dcprice = $('#dcprice_'+product_id).val();		//에누리액
	
	var unit_price;									//에누리단가
	var dc_unit_price;								//에누리견적가 단가
	var dc_tax;										//에누리견적가 세액
	var total_price;								//에누리견적가 공급가
	var discount_rate;								//할인율
	var amount = $('#amount_'+product_id).val();
	var sellprice;

	var sellprice  = $('#sellprice_'+product_id).val();	//에누리단가
	unit_price = sellprice - dcprice;

	$('#unit_price_'+product_id).val(unit_price);				//에누리단가
	$('#td_unit_price_'+product_id).html(FormatNumber(unit_price));				//에누리단가

	dc_unit_price = Math.round(unit_price/11*10*amount);		//에누리견적가 - 단가		td_dc_unit_price_0000002507

	$('#dc_unit_price_'+product_id).val(dc_unit_price);	
	$('#td_dc_unit_price_'+product_id).html(FormatNumber(dc_unit_price));	

	dc_tax = Math.floor(unit_price/11*amount);					//에누리견적가 - 세액
	$('#dc_tax_'+product_id).val(dc_tax);
	$('#td_dc_tax_'+product_id).html(FormatNumber(dc_tax));	
	
	total_price = unit_price * amount;							//에누리 견적가 - 공급가
	$('#total_price_'+product_id).val(total_price);
	$('#td_total_price_'+product_id).html(FormatNumber(total_price));
	
																	//할인률
	discount_rate = unit_price/sellprice*100;
	$('#discount_rate_'+product_id).val(Math.round(discount_rate));
	$('#td_discount_rate_'+product_id).html(Math.round(discount_rate)+'%');
	
}

function product_option(product_id){

	var product_id;
	var sell_type = $('#sell_type').val();
	var option_id = $('select[id=opn_ix_'+product_id+']').children('option:selected').val();
	
	if(product_id){
	
		$.ajax({
		    url : './estimate.act.php',
		    type : 'get',
		    data : {pid:product_id,
					option_id:option_id,
					act:'search_productinfo',
					type:sell_type
					},
		    dataType: 'json',
		    error: function(data,error){// 실패시 실행함수 
		        alert(error);
			},
		    success: function(data){
				var listprice = data['listprice'];
				var sellprice = data['sellprice'];
				var coprice = data['coprice'];
				var unit_price ;
				var dcprice = $('#dcprice_'+product_id).val();
					
					$('#td_coprice_'+product_id).html(FormatNumber(coprice));
					$('#coprice_'+product_id).val(coprice);

					$('#td_listprice_'+product_id).html(FormatNumber(listprice));
					$('#listprice_'+product_id).val(listprice);

					$('#td_sellprice_'+product_id).html(FormatNumber(sellprice));
					$('#sellprice_'+product_id).val(sellprice);

					$('#dcprice_'+product_id).val('0');

					$('#td_unitprice_'+product_id).html(FormatNumber(sellprice));
					$('#unitprice_'+product_id).val(sellprice);

					$('#amount_'+product_id).val('1');
					unit_price = sellprice;	//에누리단가
					$('#unit_price_'+product_id).val(unit_price);				//에누리단가
					$('#td_unit_price_'+product_id).html(FormatNumber(unit_price));				//에누리단가
		
					dc_unit_price = Math.round(unit_price/11*10);
					$('#dc_unit_price_'+product_id).val(dc_unit_price);
					$('#td_dc_unit_price_'+product_id).html(FormatNumber(dc_unit_price));	

					dc_tax = Math.floor(unit_price/11);
					$('#dc_tax_'+product_id).val(dc_tax);
					$('#td_dc_tax_'+product_id).html(FormatNumber(dc_tax));	
					
					total_price = unit_price;
					$('#total_price_'+product_id).val(total_price);
					$('#td_total_price_'+product_id).html(FormatNumber(total_price));
					
					sellprice = $(this).find('#sellprice').val();
					discount_rate = unit_price/sellprice*100;
					$('#discount_rate_'+product_id).val(Math.round(discount_rate));
					$('#td_discount_rate'+product_id).html(Math.round(discount_rate)+'%');

            }
        });
	}
}


$('.different_option_click').click(function(){
	NOW_SELECT_OD_IX = $(this).attr('od_ix');
	var result = ShowModalWindow('../goods_option_select.php?pid='+$(this).attr('pid')+'&delivery_package='+$(this).attr('delivery_package'),800,700,'product_search',true);
});

$('.different_product_click').click(function(){
	NOW_SELECT_OD_IX = $(this).attr('od_ix');
	var result = ShowModalWindow('../product_search.php?company_id='+$(this).attr('company_id')+'&surtax_yorn='+$(this).attr('surtax_yorn'),800,700,'product_search',true);

	json_result = JSON.parse(result);

	select_different_product($(this),json_result);
});

$('.same_product_click').click(function(){
	NOW_SELECT_OD_IX = $(this).attr('od_ix');
	select_same_product($(this));
});

$('#total_apply_delivery_price').keyup(function (){
	change_total_apply_delivery_price($(this));
});


</script>

";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = estimate_menu();
	$P->strContents = $Contents;
	$P->Navigation = "견적서 관리 > 견적서 관리 > $menu_name";
	$P->NaviTitle = " $menu_name";
	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = estimate_menu();
	$P->strContents = $Contents;
	$P->Navigation = "견적서 관리 > 견적서 관리 > $menu_name";
	$P->title = "$menu_name";
	echo $P->PrintLayOut();
}



function relationProductList($est_ix, $disp_type="",$act ='insert'){

	global $start,$page, $orderby, $admin_config, $erpidj, $pldb;

	$max = 105;
	$group_code = 1;
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;
	$ddb = new Database;
	$od_db = new Database;
	$db3  = new Database;
	$sql = "select
				count(*) as total
			from
				".TBL_SHOP_ESTIMATES." as e
				inner join ".TBL_SHOP_ESTIMATES_DETAIL." as ed on (e.est_ix = ed.est_ix)
			where
				e.est_ix = '".$est_ix."'";

	$db->query($sql);
	$total = $db->total;
	
	$sql = "select
				ed.*,
				p.coprice,
				ed.pname as product_name
			from
				".TBL_SHOP_ESTIMATES." as e
				inner join ".TBL_SHOP_ESTIMATES_DETAIL." as ed on (e.est_ix = ed.est_ix)
				inner join ".TBL_SHOP_PRODUCT." as p on (ed.pid = p.id)
			where
				e.est_ix = '".$est_ix."'
				order by ed.estd_ix ASC";

	$db->query($sql);

	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<table width="100%" id="productList_1" name="productList" class="list_table_box" >
								<tr height="25">
									<td class="s_td" width="15%" rowspan="2">상품명</td>
									<td class="m_td" width="10%" rowspan="2">옵션</td>
									<td class="m_td" width="7%" rowspan="2">매입가</td>
									<td class="m_td" width="7%" rowspan="2">정가</td>
									<td class="m_td" width="7%" rowspan="2">판매가(할인가)</td>
									<td class="m_td" width="7%" rowspan="2">에누리액</td>
									<td class="m_td" width="7%" rowspan="2">에누리단가</td>
									<td class="m_td"  width="7%"rowspan="2">수량</td>
									<td class="m_td" width="20%" colspan="3">에누리견적가</td>
									<td class="e_td"width="7%" rowspan="2">할인율%</td>
								</tr>
								<tr height="25">
									<td class="s_td"  width="5%">단가</td>
									<td class="m_td" width="5%">세액</td>
									<td class="e_td" width="5%">공급가</td>
								</tr>
								<tr id="non_result_area" height=50><td colspan=12 style="text-align:center">견적상품을 선택해주세요</td></tr>
							</table>';
		}
	}else{

		if($disp_type == "clipart"){
			$mString = '<table width="100%" id="productList_1" name="productList" class="list_table_box" >
							<tr height="25">
								<td class="s_td" width="18%" rowspan="2">상품명</td>
						
								<td class="m_td" width="5%" rowspan="2">매입가</td>
								<td class="m_td" width="5%" rowspan="2">정가</td>
								<td class="m_td" width="7%" rowspan="2">판매가(할인가)</td>
								<td class="m_td" width="5%" rowspan="2">에누리액</td>
								<td class="m_td" width="5%" rowspan="2">에누리단가</td>
								<td class="m_td"  width="4%"rowspan="2">수량</td>
			
								<td class="m_td" width="20%" colspan="3">에누리견적가</td>
								<td class="m_td"width="4%" rowspan="2">할인율%</td>
								<td class="e_td"width="3%" rowspan="2">관리</td>
							</tr>
							<tr height="25">
								<td class="s_td"  width="5%">단가</td>
								<td class="m_td" width="5%">세액</td>
								<td class="e_td" width="5%">공급가</td>
							</tr>';

			$sql = "SELECT
						e.*,
						ed.*,
						p.coprice,
						ccd.com_name
					from 
						shop_estimates as e
						left join shop_estimates_detail as ed on (e.est_ix = ed.est_ix)
						left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (ed.opnd_ix = pod.id)
						left join ".TBL_SHOP_PRODUCT." p on (p.id=ed.pid)
						left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (p.admin = ccd.company_id)
					where 
						e.est_ix = '".$est_ix."' 
						$addWhere
						ORDER BY ed.delivery_type, ed.delivery_package, ed.delivery_method, ed.ori_company_id, ed.pid ASC, ed.set_group asc, ed.company_id DESC";

			$ddb->query($sql);
			
			$bcompany_id = '';

			for($j=0;$j < $ddb->total;$j++){

				$ddb->fetch($j);

				$delivery_method = getDeliveryMethod($ddb->dt[delivery_method]);			//배송방법 텍스트 리턴
				$delivery_pay_type = getDeliveryPayType($ddb->dt[delivery_pay_method]);
				$unit_price = $ddb->dt[sellprice] - $ddb->dt[discountprice];
				
				$sql = "select dt_ix from shop_product_delivery where pid = '".$ddb->dt[pid]."' and is_wholesale = 'R' order by delivery_div limit 0,1";
				$db3->query($sql);
				$db3->fetch();
				$dt_ix = $db3->dt[dt_ix];

				$mString .= "
								<tr>
									<td>
										<table width='100%' style='padding-left:2px;' border='0'>
											<col width='60px'>
											<col width='5px'>
											<col width='*'>
										
											<tr>";

						$mString .= "			<td align='left'>
												<a  href='/shop/goods_view.php?id=".$ddb->dt[pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], 'm',$ddb->dt)."'  target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], 'm',$ddb->dt)."'  width=50 style='margin:5px;'></a><br/>";
																	
												if($bcompany_id != $ddb->dt[company_id]){
													$seller_info_str= GET_SELLER_INFO($ddb->dt[company_id]);
												}

							$mString .= "
												</td>
												<td width='5' align='left'></td>
												<td style='line-height:140%'  align='left'>
												<table width='100%' border='0'>
													<tr>
														<td style='font-weight:bold;'>
															<a href='../product/goods_input.php?id=".$ddb->dt[pid]."' target='_blank' style='color:#0054FF;'>
															<span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".GET_SELLER_INFO($ddb->dt[company_id])."'>[".$ddb->dt[com_name]."]</span>
															".$db->dt[com_name]."
															</a>
														</td>
													</tr>
													<tr>
														<td> 상품시스템코드 : ".$ddb->dt[pid]."</td>
													</tr>
													<tr>
														<td >
														<a href='../product/goods_input.php?id=".$ddb->dt[pid]."' target='_blank' >
														".$ddb->dt[pname]."
														</a>
													";

											if(strip_tags($ddb->dt[options_text])){
												$mString .= "<br/> ▶ ".strip_tags($ddb->dt[options_text]);
											}

								$mString .="			</td>
													</tr>
													<tr>
														<td>배송정책 : ".(product_list_policy_text($dt_ix) == ''?'<b>미지정</b>':product_list_policy_text($dt_ix))."</td>
													</tr>
												</table>
												</td>
											</TR>
										</TABLE>
									</td>
	
									<td align=center id='td_coprice_".$ddb->dt[estd_ix]."'>
										".number_format($ddb->dt[coprice])."
									</td>
							
									<td align=center id='td_listprice_".$ddb->dt[estd_ix]."'>
										".number_format($ddb->dt[listprice])."
										
									</td>
	
									<td align=center id='td_sellprice_".$ddb->dt[estd_ix]."'>
										".number_format($ddb->dt[sellprice])."
									</td>

									<td align=center id='td_dcprice_".$ddb->dt[estd_ix]."'>
										<input type='text' class='textbox number' name='goods_infos[".$ddb->dt[estd_ix]."][dcprice]' id='dcprice_".$ddb->dt[estd_ix]."' onkeyup=calcurate_maginrate('".$ddb->dt[estd_ix]."') value='".$ddb->dt[discountprice]."' style='width:60px;'>
									</td>

									<td align=center id='td_unit_price_".$ddb->dt[estd_ix]."'>
										".number_format($unit_price)."
									</td>

									<td align=center id='td_amount_".$ddb->dt[estd_ix]."'>
										<input type='text' class='textbox number' name='goods_infos[".$ddb->dt[estd_ix]."][amount]' id='amount_".$ddb->dt[estd_ix]."' onkeyup=calcurate_maginrate('".$ddb->dt[estd_ix]."') value='".$ddb->dt[pcount]."' style='width:30px;'>
									</td>
									
									<td align=center id='td_dc_unit_price_".$ddb->dt[estd_ix]."'>
										".number_format(round($ddb->dt[sellprice]/11*10*$ddb->dt[pcount]))."
									</td>
					
									<td align=center id='td_dc_tax_".$ddb->dt[estd_ix]."'>
										".number_format(round($ddb->dt[sellprice]/11*$ddb->dt[pcount]))."
									</td>

									<td class='point' align='center' id='td_total_price_".$ddb->dt[estd_ix]."'>
										".number_format($ddb->dt[totalprice])."
									</td>

									<td class='point' align='center' id='td_discount_rate_".$ddb->dt[estd_ix]."'>
										".($ddb->dt[rate]?$ddb->dt[rate]:'0')." %
									</td>
									
									<td class='point' align='center'>
										<a href='javascript:' onclick=\"del_estd_ix('".$ddb->dt[estd_ix]."');\">
											<img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer;'>
										</a>
									</td>
									<input type='hidden' name='goods_infos[".$ddb->dt[estd_ix]."][pid]' id='pid_".$ddb->dt[estd_ix]."' value='".$ddb->dt[pid]."'>
									<input type='hidden' name='goods_infos[".$ddb->dt[estd_ix]."][coprice]' id='coprice_".$ddb->dt[estd_ix]."' value='".$ddb->dt[coprice]."'>
									<input type='hidden' name='goods_infos[".$ddb->dt[estd_ix]."][estd_ix]' id='estd_ix_".$ddb->dt[estd_ix]."' value='".$ddb->dt[estd_ix]."'>
									<input type='hidden' name='goods_infos[".$ddb->dt[estd_ix]."][listprice]' id='listprice_".$ddb->dt[estd_ix]."' value='".$ddb->dt[listprice]."'>
									<input type='hidden' name='goods_infos[".$ddb->dt[estd_ix]."][sellprice]' id='sellprice_".$ddb->dt[estd_ix]."' value='".$ddb->dt[sellprice]."'>
									<input type='hidden' name='goods_infos[".$ddb->dt[estd_ix]."][unit_price]' id='unit_price_".$ddb->dt[estd_ix]."' value='".$unit_price."'>
									<input type='hidden'  name='goods_infos[".$ddb->dt[estd_ix]."][dc_unit_price]' id='dc_unit_price_".$ddb->dt[estd_ix]."' value='".round($ddb->dt[sellprice]/11*10*$ddb->dt[pcount])."' >
									<input type='hidden'  name='goods_infos[".$ddb->dt[estd_ix]."][dc_tax]' id='dc_tax_".$ddb->dt[estd_ix]."' value='".round($ddb->dt[sellprice]/11*$ddb->dt[pcount])."'>
									<input type='hidden' name='goods_infos[".$ddb->dt[estd_ix]."][total_price]' id='total_price_".$ddb->dt[estd_ix]."' value='".$ddb->dt[totalprice]."'>
									<input type='hidden' name='goods_infos[".$ddb->dt[estd_ix]."][discount_rate]' id='discount_rate_".$ddb->dt[estd_ix]."' value='".($ddb->dt[rate]?$ddb->dt[rate]:'0')."'>
									
								</tr>";
				
				$b_oid = $ddb->dt[oid];
				$bcompany_id = $ddb->dt[company_id];
				$bproduct_id = $ddb->dt[pid];
				$bset_group = $ddb->dt[set_group];
				$b_product_type = $ddb->dt[product_type];
				$b_factory_info_addr_ix  = $ddb->dt[factory_info_addr_ix];
				$b_delivery_type = $ddb->dt[delivery_type];
				$b_ode_ix = $ddb->dt[ode_ix];

				$dt_ix = '';
			}

			$mString .= "</table>";
		}
	}


	return $mString;

}

?>