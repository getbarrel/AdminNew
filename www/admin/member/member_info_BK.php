<?
include("../class/layout.class");

$Script = " <script language='JavaScript' src='member.js'></Script>
			<style>
				input {border:1px solid #c6c6c6;padding:3px;}
				.member_table td {text-align:left;}
			</style>";

$db = new Database;
$mdb = new Database;

$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");

/////////////////////////////////////////////////////////////////////////////////////////////
// csrf Token 발행
$csrfToken = getCsrfToken();
setCsrfTokenInSess($csrfToken);
/////////////////////////////////////////////////////////////////////////////////////////////

if($info_type == ""){
	$info_type = 'member';
}

if($act == "insert" || $code == ""){
	$act = "insert";
}else{
	if($info_type == "member" || $info_type == "c_member"){
		$act = "update";

		if($db->dbms_type == "oracle"){
			$sql = "SELECT cmd.code,AES_DECRYPT(jumin,'".$db->ase_encrypt_key."') as jumin
						 , AES_DECRYPT(name,'".$db->ase_encrypt_key."') as name
						 , AES_DECRYPT(mail,'".$db->ase_encrypt_key."') as mail
						 , AES_DECRYPT(zip,'".$db->ase_encrypt_key."') as zip
						 , AES_DECRYPT(addr1,'".$db->ase_encrypt_key."') as addr1
						 , AES_DECRYPT(addr2,'".$db->ase_encrypt_key."') as addr2
						 , AES_DECRYPT(doro_zip,'".$db->ase_encrypt_key."') as doro_zip
						 , AES_DECRYPT(doro_addr1,'".$db->ase_encrypt_key."') as doro_addr1
						 , AES_DECRYPT(doro_addr2,'".$db->ase_encrypt_key."') as doro_addr2
						 , AES_DECRYPT(tel,'".$db->ase_encrypt_key."') as tel
						 , AES_DECRYPT(pcs,'".$db->ase_encrypt_key."') as pcs
						 , AES_DECRYPT(UNHEX(resign_msg),'".$db->ase_encrypt_key."') as resign_msg
						 , cmd.mem_card, ccd.company_id, cmd.level_ix, ccd.com_mobile, cmd.level_msg, cmd.customs_clearance_number
						 , birthday, birthday_div, tel_div, info, sms, nick_name, job, cmd.date_ as regdate2, cmd.file_, recent_order_date
						 , recom_id, gp_ix, sex_div, mem_level, branch, team, department, position, black_list
						 , add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6
						 , cu.*, ccd.*
					  FROM ".TBL_COMMON_MEMBER_DETAIL." cmd
					     , ".TBL_COMMON_USER." cu
			     LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
				     WHERE cu.code = cmd.code 
				       AND cu.code = '$code'
				  ORDER BY cu.date_ DESC";
		}else{
			$sql = "SELECT cmd.code
						 , AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
						 , AES_DECRYPT(UNHEX(cmd.first_name),'".$db->ase_encrypt_key."') as first_name
						 , AES_DECRYPT(UNHEX(cmd.last_name),'".$db->ase_encrypt_key."') as last_name
						 , AES_DECRYPT(UNHEX(cmd.first_kana),'".$db->ase_encrypt_key."') as first_kana
						 , AES_DECRYPT(UNHEX(cmd.last_kana),'".$db->ase_encrypt_key."') as last_kana
						 , AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail
						 , AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip
						 , AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1
						 , AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2
						 , AES_DECRYPT(UNHEX(cmd.doro_zip),'".$db->ase_encrypt_key."') as doro_zip
						 , AES_DECRYPT(UNHEX(cmd.doro_addr1),'".$db->ase_encrypt_key."') as doro_addr1
						 , AES_DECRYPT(UNHEX(cmd.doro_addr2),'".$db->ase_encrypt_key."') as doro_addr2
						 , AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel
						 , AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs
						 , AES_DECRYPT(UNHEX(cmd.resign_msg),'".$db->ase_encrypt_key."') as resign_msg
						 , cmd.mem_card, ccd.company_id, cmd.level_ix, ccd.com_mobile, cmd.level_msg, cmd.customs_clearance_number
						 , birthday, birthday_div, tel_div, info, sms, nick_name, job, cmd.date as regdate2, cmd.file, recent_order_date
						 , recom_id, gp_ix, sex_div, mem_level, branch, team, department, position, black_list
						 , add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6
						 , cmd.smsdate, cmd.agree_infodate, cmd.country,cmd.city,cmd.state
						 , cu.*, ccd.*
					  FROM ".TBL_COMMON_MEMBER_DETAIL." cmd 
					  	 , ".TBL_COMMON_USER." cu
				 LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
					 WHERE cu.code = cmd.code
					   AND cu.code = '$code'
				  ORDER BY cu.date DESC";

		}

		$db->query($sql);
		$db->fetch();

		//회원타입별로 추가정보 불러올때 회원테이블과 join info  테이블의 타입 정보가 서로 달라서 구분 변경 작업 jk130506
		switch($db->dt[mem_type]){
			case "M":
				$mem_type = "B";
				break;
			case "F":
				$mem_type = "F1";
				break;
			case "C":
				$mem_type = "C";
				break;
			default:
				$mem_type = "B";
				break;
		}

		$tel   			  = explode("-", $db->dt[tel]);
		$pcs   			  = explode("-", $db->dt[pcs]);
		$zip   			  = explode("-", $db->dt[zip]);
        $com_zip		  = explode("-", $db->dt[com_zip]);
        $corporate_number = explode("-", $db->dt[corporate_number]);

		if($db->dt["birthday"] != ""){
			$birthday = explode("-",$db->dt["birthday"]);
		}

        list($com_fax1, $com_fax2, $com_fax3) = split("-",$db->dt[com_fax]);
        list($com_phone1, $com_phone2, $com_phone3) = split("-",$db->dt[com_phone]);
        list($com_mobile1, $com_mobile2, $com_mobile3) = split("-",$db->dt[com_mobile]);

        if(strpos($db->dt[com_number],"-")){
            list($com_num1, $com_num2, $com_num3) = split("-",$db->dt[com_number]);
        }else{
            $com_num1 = substr($db->dt[com_number],0,3);
            $com_num2 = substr($db->dt[com_number],3,2);
            $com_num3 = substr($db->dt[com_number],5,5);
        }

		//if ($db->dt[info]) $info_y = " checked"; else $info_n = " checked";
		$sql = "SELECT ccf.sheet_value as business_file
					 , cu.company_id
				  FROM common_company_file as ccf
			INNER JOIN common_user as cu on (ccf.company_id = cu.company_id)
				 WHERE ccf.sheet_name = 'business_file'
				   AND cu.code  = '".$code."'";

		$mdb->query($sql);
		$mdb->fetch();

        $company_id = $mdb->dt[company_id];
        $business_file = $mdb->dt[business_file];

        $path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;
	}else if($info_type == "file_member"){
		$act = "update";

		if($db->dbms_type == "oracle"){
			$sql = "SELECT cmd.code,AES_DECRYPT(jumin,'".$db->ase_encrypt_key."') as jumin
						 , AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name
						 , AES_DECRYPT(mail,'".$db->ase_encrypt_key."') as mail
						 , AES_DECRYPT(zip,'".$db->ase_encrypt_key."') as zip
						 , cmd.date_ as regdate2
						 , cmd.file_
						 , recent_order_date
						 , cu.*  
						 , ccd.*
						 , cmd.mem_card
						 , cmd.voucher_div
						 , cmd.voucher_num_div
						 , cmd.voucher_phone
						 , cmd.phone_voucher_name
						 , cmd.voucher_card
						 , cmd.card_voucher_name
						 , cmd.expense_num
						 , cmd.certificate_yn
					  FROM ".TBL_COMMON_MEMBER_DETAIL." cmd 
					  	 , ".TBL_COMMON_USER." cu
				 LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
					 WHERE cu.code = cmd.code  and cu.code = '$code'
				  ORDER BY cu.date_ DESC";
		}else{
			$sql = "SELECT cmd.code
						 , AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name
						 , AES_DECRYPT(UNHEX(first_name),'".$db->ase_encrypt_key."') as first_name
						 , AES_DECRYPT(UNHEX(last_name),'".$db->ase_encrypt_key."') as last_name
						 , AES_DECRYPT(UNHEX(first_kana),'".$db->ase_encrypt_key."') as first_kana
						 , AES_DECRYPT(UNHEX(last_kana),'".$db->ase_encrypt_key."') as last_kana
						 , AES_DECRYPT(mail,'".$db->ase_encrypt_key."') as mail
						 , AES_DECRYPT(zip,'".$db->ase_encrypt_key."') as zip
						 , cmd.date as regdate2
						 , cmd.file
						 , recent_order_date
						 , cu.*  
						 , ccd.*
						 , cmd.mem_card
						 , cmd.voucher_div
						 , cmd.voucher_num_div
						 , cmd.voucher_phone
						 , cmd.phone_voucher_name
						 , cmd.voucher_card
						 , cmd.card_voucher_name
						 , cmd.expense_num
						 , cmd.certificate_yn
	 				  FROM ".TBL_COMMON_MEMBER_DETAIL." cmd 
	 				  	 , ".TBL_COMMON_USER." cu
				 LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
					 WHERE cu.code = cmd.code
					   AND cu.code = '$code'
				  ORDER BY cu.date DESC";
		}

		$db->query($sql);
		$db->fetch();

        list($expense_num1, $expense_num2, $expense_num3) = split("-",$db->dt[expense_num]);						//지출증빙 번호
        list($voucher_phone1, $voucher_phone2, $voucher_phone3) = split("-",$db->dt[voucher_phone]);				//핸드폰번호
        list($voucher_card1, $voucher_card2, $voucher_card3,$voucher_card4) = split("-",$db->dt[voucher_card]);	//현금영수증카드
    }else if($info_type == "delivery_info"){
		//배송지관리
	}else if($info_type == "return_bank"){
		//환급통장정보
	}else if($info_type == "my_pet"){
		//마이펫 정보
		$act = "update";
	}
}

$db2 = new Database;

//회원타입별로 추가정보 불러올때 회원테이블과 join info  테이블의 타입 정보가 서로 달라서 구분 변경 작업 jk130506
$db2->query("select * from shop_join_info where disp = 'Y' and field like 'add_etc%' and join_type = '".$mem_type."' order by vieworder ");
$join_info = $db2->fetchall();

if(is_array($join_info)){
	foreach ($join_info as $key => $sub_array) {
		$select_ = array("field_value_text"=>explode("|",$sub_array[field_value]));
		array_insert($sub_array,14,$select_);
		$join_info[$key] = $sub_array;
	}
}
if($act == 'insert'){
    if($mem_type != ''){
        $db->dt[mem_type] = $mem_type;
    }else{
        $db->dt[mem_type] = 'M';
    }
}

$Contents .= "
<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
	<tr >
		<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "회원관리 > 회원정보 수정", false)."</td>
	</tr>";

if($act == "update"){
	$Contents .= " 
		<tr height=30>
			<td class='p11 ls1' style='padding:0 0 0 0px;text-align:left;' > <b>".Black_list_check($db->dt[code],$db->dt[name])."</b> 님의 회원정보 입니다.</td>
		</tr>        ";
}else{
	$Contents .= " 
		<tr height=30>
			<td class='p11 ls1' style='padding:0 0 0 0px;text-align:left;' > 수동으로 회원정보를 등록 하실수 있습니다.</td>
		</tr>        ";
}

$Contents .= "
</table>

<table width='100%' cellpadding=0 cellspacing=0 border='0' >
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
							<table id='tab_01' ".(($info_type == "member" || $info_type == "") ? "class='on' ":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02'>
										<a href='member_info.php?info_type=member&mmode=".$mmode."&code=".$code."'>개인정보</a> 
										<span style='padding-left:2px' class='helpcloud' help_width='410' help_height='70' help_html='사업자 정보를 작성하여 등록한 후에 해당 사업자에 관리자화면 로그인 정보를 발급해 줄 수 있습니다.<br />사업자 정보 추가 후 [목록관리] - [사용자 추가]를 통해 관리자에 계정을 발급해 주시기 바랍니다.'>
											<img src='/admin/images/icon_q.gif' />
										</span>
									</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_02' ".($info_type == "c_member" ? "class='on' ":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02'>";
										if($code == ""){
											$Contents .= "<a href=\"javascript:alert('개인정보를 먼저 입력하십시오.');\">사업자정보</a>";
										}else{
											$Contents .= "<a href='member_info.php?info_type=c_member&mmode=".$mmode."&code=".$code."'>사업자정보</a>";
										}
									$Contents .= "
									</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_03' ".($info_type == "file_member" ? "class='on' ":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02'>";
										if($code == ""){
											$Contents .= "<a href=\"javascript:alert('개인정보를 먼저 입력하십시오.');\">증빙서류정보</a>";
										}else{
											$Contents .= "<a href='member_info.php?info_type=file_member&mmode=".$mmode."&code=".$code."'>증빙서류정보</a>";
										}
									$Contents .= "
									</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<!--
								<table id='tab_04' ".($info_type == "delivery_info" ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02'>";
											if($code == ""){
												$Contents .= "<a href=\"javascript:alert('개인정보를 먼저 입력하십시오.');\">배송지정보</a>";
											}else{
												$Contents .= "<a href='member_info.php?info_type=delivery_info&mmode=".$mmode."&code=".$code."'>배송지정보</a>";
											}
										$Contents .= "
										</td>
										<th class='box_03'></th>
									</tr>
								</table>
							-->
							<table id='tab_05' ".($info_type == "return_bank" ? "class='on' ":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02'>";
										if($code == ""){
											$Contents .= "<a href=\"javascript:alert('개인정보를 먼저 입력하십시오.');\">환급통장정보</a>";
										}else{
											$Contents .= "<a href='member_info.php?info_type=return_bank&mmode=".$mmode."&code=".$code."'>환급통장정보</a>";
										}
									$Contents .= "
									</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_06' ".($info_type == "shipping_addr" ? "class='on' ":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02'>";
										if($code == ""){
											$Contents .= "<a href=\"javascript:alert('개인정보를 먼저 입력하십시오.');\">배송지관리</a>";
										}else{
											$Contents .= "<a href='member_info.php?info_type=shipping_addr&mmode=".$mmode."&code=".$code."'>배송지관리</a>";
										}
									$Contents .= "
									</td>
									<th class='box_03'></th>
								</tr>
							</table>";
if($gp_ix == 1) {
    $Contents .= "				<table id='tab_07' " . ($info_type == "my_pet" ? "class='on' " : "") . ">
								<tr>
									<th class='box_01'></th>
									<td class='box_02'>";
    if ($code == "") {
        $Contents .= "					<a href=\"javascript:alert('개인정보를 먼저 입력하십시오.');\">마이펫 정보</a>";
    } else {
        $Contents .= "					<a href='member_info.php?info_type=my_pet&mmode=" . $mmode . "&code=" . $code . "'>마이펫 정보</a>";
    }
    $Contents .= "
									</td>
									<th class='box_03'></th>
								</tr>
							</table>";
}
$Contents .= "							
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>";

if($info_type == "member" || $info_type == ""){	//개인정보

$Contents .= "
<table cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr>
				<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "회원관리 > 회원정보 수정", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 0px 0 0px;height:569px;vertical-align:top'>
				<form name='EDIT_".$db->dt[code]."' method='post' onsubmit='return CheckFormValue(this)' action='member.act.php'  target='act'>
				<input type='hidden' name='act' value='".$act."'>
				<input type='hidden' name='code' value='".$db->dt[code]."'>
				<input type='hidden' name='company_id' value='".$db->dt[company_id]."'>
				<input type='hidden' name='info_type' value='".$info_type."'>
				<input type='hidden' name='csrfToken' value='".$csrfToken."'>
				<table border='0' width='100%' cellspacing='1' cellpadding='0'>
					<tr>
						<td >
							<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
								<col width=15%>
								<col width=35%>
								<col width=15%>
								<col width=35%>";
				if($act == 'insert'){
				$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 회원구분 <img src='".$required3_path."'></td>
									<td class='input_box_item'>
										<select name='mem_type' onchange='select_mem_type()' validation=true title='회원구분'>
											<option value='' ".($db->dt[mem_type] == "" ? "selected":"").">회원구분</option>
											<option value='M' ".($db->dt[mem_type] == "M" ? "selected":"").">개인회원</option>
											<option value='F' ".($db->dt[mem_type] == "F" ? "selected":"").">글로벌회원</option>
											<option value='C' ".($db->dt[mem_type] == "C" ? "selected":"").">사업자회원</option>
											<!--<option value='A' ".($db->dt[mem_type] == "A" ? "selected":"").">직원(관리자)</option>-->
										</select>
									</td>
									<td class='input_box_title' nowrap> 사용자그룹 <img src='".$required3_path."'></td>
									<td class='input_box_item'>".makeGroupSelectBox($mdb,"gp_ix",$db->dt[gp_ix],"validation=true title='사용자 그룹'")."</td>
								</tr>";
				}
$Contents .= "					<tr>
									<td class='input_box_title' nowrap> 등록일</td>
									<td class='input_box_item'>&nbsp;".$db->dt[regdate2]."</td>
									<td class='input_box_title' nowrap> 최근 방문일</td>
									<td class='input_box_item'>&nbsp;".$db->dt[last]."</td>
								</tr>
								<tr height='50px'>
									<td class='input_box_title' nowrap> 이름 <img src='".$required3_path."'></td>
									<td class='input_box_item'>";
										//if($admininfo["language"] == 'korea') {
                                            $Contents .= "<input type='text' class='textbox' name='name' size='17' maxlength='20' value='".$db->dt[name]."' validation=true title='이름'>";
                                        //}else{
	/*
                                            $Contents .= "<input type='text' class='textbox' name='first_name' size='5' maxlength='20' value='".$db->dt[first_name]."' validation=true title='이름'>
														<input type='text' class='textbox' name='last_name' size='15' maxlength='20' value='".$db->dt[last_name]."' validation=true title='이름'>
														</br>
														<input type='text' class='textbox' name='first_kana' size='5' maxlength='20' value='".$db->dt[first_kana]."' validation=true title='이름'>
														<input type='text' class='textbox' name='last_kana' size='15' maxlength='20' value='".$db->dt[last_kana]."' validation=true title='이름'>";
										}*/
$Contents .= "						</td>
									<td class='input_box_title' nowrap> 아이디 <img src='".$required3_path."'></td>
									<td class='input_box_item' > <input type='hidden' name='user_id_hide' value='".$db->dt[id]."' > ";
									if($act == "update"){
										$Contents .= "&nbsp;".$db->dt[id]."";
									}else{
										$Contents .= "<input type='text' class='textbox' id='user_id' name='id' size='17' maxlength='20' value='' validation=true title='아이디'><span id='idCheckText'></span>";
									}
									$Contents .= "
									</td>
								</tr>";

		if($act == 'insert'){
$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 비밀번호 <img src='".$required3_path."'></td>
									<td class='input_box_item'><input type='password' class='textbox' name='pass' id='compare_a' pwtype=true size='16' maxlength='30' validation=true title='비밀번호' autocomplete='off'  value=''>
									</td>
									<td class='input_box_title' nowrap> 비번확인 <img src='".$required3_path."'></td>
									<td class='input_box_item'><input type='password' class='textbox' name='again' id='compare_b' pwtype=true compare='true' size='16' maxlength='30' validation=true title='비번확인' autocomplete='off' value=''></td>
								</tr>";
		}else{
$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 비밀번호</td>
									<td class='input_box_item'><input type='password' class='textbox' name='pass' size='16' maxlength='30' autocomplete='off'  value=''>
									<input type=checkbox name='change_pass' id='change_pass' value='1' checked style='vertical-align:middle;'>
									<div style='font-size: 11px;color:red;'>※SNS 회원의 경우 비번 변경 시 로그인 불가 합니다.</div>
									<!--<label for='change_pass' style='vertical-align:middle;'> 비밀번호수정</label>-->
									</td>
									<td class='input_box_title' nowrap> 비번확인</td>
									<td class='input_box_item'><input type='password' class='textbox' name='again' size='16' maxlength='30' autocomplete='off' value=''>
									</td>
								</tr>";
		}

		if($act == 'update'){
$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 사용자그룹</td>
									<td class='input_box_item'>".makeGroupSelectBox($mdb,"gp_ix",$db->dt[gp_ix],"validation=true title='사용자 그룹'")."</td>
									<td class='input_box_title' nowrap> 회원구분</td>
									<td class='input_box_item'>
										<select name='mem_type'>
											<option value='M' ".($db->dt[mem_type] == "M" ? "selected":"").">개인회원</option>
											<option value='F' ".($db->dt[mem_type] == "F" ? "selected":"").">글로벌회원</option>
											<option value='C' ".($db->dt[mem_type] == "C" ? "selected":"").">기업회원</option>
											<option value='A' ".($db->dt[mem_type] == "A" ? "selected":"").">직원(관리자)</option>
										</select>
									</td>
								</tr>";
		}

$Contents .= "
								<tr>
									<td class='input_box_title' nowrap>
										<label for='black_list'>회원레벨 <!--img src='".$required3_path."'--></label>
									</td>
									<td class='input_box_item' colspan='3'>
									".getMemberLevel($db->dt[level_ix],'false')."
										<input type='text' class='textbox' name='level_msg' id='level_msg' size='66' value='".$db->dt[level_msg]."' style='margin:10px 0px;width:300px;'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 성별</td>
									<td class='input_box_item' colspan='3'>
										<input type='radio' name='sex_div' id='sex_div_m' style='border:0px;' value='M' ".($db->dt[sex_div] == "M" ? "checked":"")."><label for='sex_div_m'> 남성 </label>
										<input type='radio' name='sex_div' id='sex_div_w' style='border:0px;' value='W' ".($db->dt[sex_div] == "W" ? "checked":"")."><label for='sex_div_w'> 여성 </label>
										<input type='radio' name='sex_div' id='sex_div_d' style='border:0px;' value='D' ".($db->dt[sex_div] == "D" ? "checked":"")."><label for='sex_div_d'> 기타 </label>
									</td>
									<!--
									<td class='input_box_title' nowrap> 오프라인 카드정보</td>
									<td class='input_box_item'><input type='text' class='textbox' name='mem_card' size='30' maxlength='30' value='".$db->dt[mem_card]."'></td>
									-->
								</tr>";

					$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 생년월일</td>
									<td class='input_box_item' nowrap colspan='3'>
										<input type='text' class='textbox' name='birthday_yyyy' size='4' maxlength='4' value='".$birthday[0]."'> -
										<input type='text' class='textbox' name='birthday_mm' size='2' maxlength='2' value='".$birthday[1]."'> -
										<input type='text' class='textbox' name='birthday_dd' size='2' maxlength='2' value='".$birthday[2]."'>
										<input type='radio' name='birthday_div' id='birthday_div_1' style='border:0px;' value='1' ".($db->dt[birthday_div] == "1" ? "checked":"")."><label for='birthday_div_1'>양력</label>
										<input type='radio' name='birthday_div' id='birthday_div_0' style='border:0px;' value='0' ".($db->dt[birthday_div] == "0" ? "checked":"")."><label for='birthday_div_0'>음력</label>
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 휴대폰</td>";
					if($db->dt[mem_type] == "F"){
					$Contents .= "	<td class='input_box_item'>
										<input type='text' class='textbox' name='pcs1' size='20' maxlength='20' value='".$db->dt[pcs]."'>
									</td>";
					}else{
					$Contents .= "	<td class='input_box_item'>
										<input type='text' class='textbox' name='pcs1' size='3' maxlength='3' value='".$pcs[0]."'> -
										<input type='text' class='textbox' name='pcs2' size='4' maxlength='4' value='".$pcs[1]."'> -
										<input type='text' class='textbox' name='pcs3' size='4' maxlength='4' value='".$pcs[2]."'>
									</td>";
					}
					if($db->dt[smsdate] != '0000-00-00 00:00:00' && $db->dt[smsdate] !=''){
						$changeSms = $db->dt[smsdate];
					}
					$Contents .= "	<td class='input_box_title' nowrap> SMS 수신여부 <img src='".$required3_path."'></td>
									<td class='input_box_item'>
										<input type='radio' name='sms' id='sms_1' value='1' ".($db->dt[sms] == "1" ? "checked":"")." style='border:0px;'>
										<label for='sms_1'>수신함</label>
										<input type='radio' name='sms' id='sms_0' value='0' ".($db->dt[sms] == "0" ? "checked":"")." style='border:0px;'>
										<label for='sms_0'>수신안함</label>
										".(! empty($changeSms) ? "<br><label>".$db->dt[smsdate]."</label>&nbsp; <label>".($db->dt[sms] == '1' ? "수신동의로 변경" : "수신미동의로 변경")." </label>": "")."
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 이메일</td>
									<td class='input_box_item'><input type='text' class='textbox' name='mail' size='30' maxlength='80' value='".$db->dt[mail]."'>";
									if($db->dt[is_id_auth] == 'N'){
										$Contents .="<input type='checkbox' name='mail_auth' id='mail_auth' value='Y' style='border:0px;'><label for='mail_auth'>강제인증</label>";
									}else{
										$Contents .="<input type='hidden' name='mail_auth' value ='Y'>";
									}
					if($db->dt[agree_infodate] != '0000-00-00 00:00:00' && $db->dt[agree_infodate] !=''){
						$changeInfo = $db->dt[agree_infodate];
					}
					$Contents .= "	</td>
									<td class='input_box_title' nowrap> 정보수신 <img src='".$required3_path."'></td>
									<td class='input_box_item'>
										<input type='radio' name='info' id='info_1' value='1' ".($db->dt[info] == "1" ? "checked":"")." style='border:0px;'>
										<label for='info_1'>수신함</label>
										<input type='radio' name='info' id='info_0' value='0' ".($db->dt[info] == "0" ? "checked":"")."  ".$info_n." style='border:0px;'>
										<label for='info_0'>수신안함</label>
										".(! empty($changeInfo) ? "<br><label>".$db->dt[agree_infodate]."</label>&nbsp; <label>".($db->dt[info] == '1' ? "수신동의로 변경" : "수신미동의로 변경")." </label>" : "")."
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 우편번호</td>
									<td class='input_box_item' >

										<table border='0' cellpadding='0' cellspacing='0' >";
						if($db->dt[mem_type] == "F"){
						  $Contents .= "<tr>
											<td style='border:0px;'>
												<input type='text' class='textbox' name='zip1' id='zipcode1' size='10' maxlength='10' value='".$db->dt[zip]."'>
											</td>
										</tr>";
						}else{
						  $Contents .= "<tr>
											<td style='border:0px;'>
												<input type='text' class='textbox' name='zip1' id='zipcode1' size='8' maxlength='8' value='".$db->dt[zip]."' readonly>
											</td>
											<td style='border:0px;padding:0px 0 0 5px;'>
												<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1');\" style='cursor:pointer;' align=absmiddle>
											</td>
										</tr>";
						}
						  $Contents .= "</table>
									</td>
									<td class='input_box_title' nowrap> 집전화</td>
									<td class='input_box_item'>
										<input type='text' class='textbox' name='tel1' size='3' maxlength='3' value='".$tel[0]."'> -
										<input type='text' class='textbox' name='tel2' size='4' maxlength='4' value='".$tel[1]."'> -
										<input type='text' class='textbox' name='tel3' size='4' maxlength='4' value='".$tel[2]."'>
									</td>
								</tr>
								<tr height=50>
									<td class='input_box_title'2 nowrap> 주소</td>
									<td bgcolor='#ffffff' colspan=3 style='padding:10px;'>";
						if($db->dt[mem_type] == "F"){
							$Contents .= "<input type='text' class='textbox' name='addr1' id='addr1' size='66' maxlength='80' value=\"".$db->dt[addr1]."\" style='margin:2px 0px'><br>
							<input type='text' class='textbox' name='addr2' id='addr2' size='66' maxlength='80' value=\"".$db->dt[addr2]."\" style='margin:2px 0px'> 세부주소
							";

						}else{
							$Contents .= "<input type='text' class='textbox' name='addr1' id='addr1' size='66' maxlength='80' value=\"".$db->dt[addr1]."\" style='margin:2px 0px' readonly><br>
										<input type='text' class='textbox' name='addr2' id='addr2' size='66' maxlength='80' value=\"".$db->dt[addr2]."\" style='margin:2px 0px'> 세부주소

										<input type='text' name='doro_addr' id = 'doro_addr' value='".$db->dt[doro_addr1]."' style='width:391px; color:red; border:0px;padding:0px; margin:0px;' readonly>
										<input type='hidden' name='doro_addr1' id='doro_addr1' value='".$db->dt[doro_addr1]."' >
										<input type='hidden' name='doro_addr2' id='doro_addr2' value='".$db->dt[doro_addr2]."' >";
						}
						$Contents .= "</td>
								</tr>";
if($db->dt[mem_type] == "F"){


		$sql = "select nation_name from global_nation_code where nation_code = '".$db->dt['country']."' ";
		$db2->query($sql);
		$db2->fetch();

		$nation_name = $db2->dt['nation_name'];
						$Contents .="
								<tr>
									<td class='input_box_title' nowrap> Country</td>
									<td class='input_box_item'>".$nation_name."</td>
									
									<td class='input_box_title' nowrap> city</td>
									<td class='input_box_item'>".$db->dt['city']."</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> state</td>
									<td class='input_box_item' colspan='3'>".$db->dt['state']."</td>
									
								</tr>
						";
}
$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 최근방문주소</td>
									<td class='input_box_item'>&nbsp;".($db->dt[ip]?$db->dt[ip]:$_SERVER[SERVER_ADDR])."</td>
									<td class='input_box_title' nowrap> 승인여부</td>
									<td class='input_box_item'>
										<select name='authorized'>
											<option value='Y' ".($db->dt[authorized] == "Y" ? "selected":"").">승인</option>
											<option value='N' ".($db->dt[authorized] == "N" ? "selected":"").">승인대기</option>
											<option value='X' ".($db->dt[authorized] == "X" ? "selected":"").">승인거부</option>
										</select>
									</td>
								</tr>
								<!--
								<tr>
									<td class='input_box_title' nowrap>별명</td>
									<td class='input_box_item'>
									<input type='text' class='textbox' id='nick_name' name='nick_name' size='46' maxlength='80' value='".$db->dt[nick_name]."'>
									</td>
									<td class='input_box_title' nowrap> 직업</td>
									<td class='input_box_item'>
									<input type='text' class='textbox' id='job' name='job' size='46' maxlength='80' value='".$db->dt[job]."'>
									</td>
								</tr>
								-->
								<tr>
									<td class='input_box_title' nowrap>통관고유번호</td>
									<td class='input_box_item' colspan='3'>
									<input type='text' class='textbox' id='customs_clearance_number' name='customs_clearance_number' size='46' maxlength='80' value='".$db->dt[customs_clearance_number]."'>
									</td>
								</td>
								<!-- 항목 설정에 따라서 항목을 뿌려줌 시작 kbk -->";

								$cnt_join_info=count($join_info);

								for($i=0;$i<$cnt_join_info;$i++) {
									if($join_info[$i]["field_type"]=="text" || $join_info[$i]["field_type"]=="password") {
$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> ".$join_info[$i]["field_name"]." </td>
									<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'>
									<input type='text' class='textbox' name='".$join_info[$i]["field"]."' size='36' maxlength='80' value='".$db->dt[$join_info[$i]["field"]]."'>
									</td>
								</tr>";

									} else if($join_info[$i]["field_type"]=="radio" || $join_info[$i]["field_type"]=="checkbox") {

$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> ".$join_info[$i]["field_name"]." </td>
									<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'>";

										$cnt_field_value_text=count($join_info[$i]["field_value_text"]);
										for($j=0;$j<$cnt_field_value_text;$j++) {

$Contents .= "
										<input type='".$join_info[$i]["field_type"]."' style='border:0px;' name='".$join_info[$i]["field"]."' value='".$join_info[$i]["field_value_text"][$j]."' ".(($db->dt[$join_info[$i]["field"]]==$join_info[$i]["field_value_text"][$j]) ? "checked":"")." /> ".$join_info[$i]["field_value_text"][$j]."";
										}
$Contents .= "
									</td>
								</tr>";

									} else if($join_info[$i]["field_type"]=="select") {
$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> ".$join_info[$i]["field_name"]." asdf</td>
									<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'>
										<select name='".$join_info[$i]["field"]."'>";

										$cnt_field_value_text=count($join_info[$i]["field_value_text"]);
										for($j=0;$j<$cnt_field_value_text;$j++) {
$Contents .= "
											<option value='".$join_info[$i]["field_value_text"][$j]."' ".(($db->dt[$join_info[$i]["field"]]==$join_info[$i]["field_value_text"][$j]) ? "selected":"").">".$join_info[$i]["field_value_text"][$j]."</option>";
										}
$Contents .= "
									</td>
								</tr>";

									} else if($join_info[$i]["field_type"]=="textarea") {
$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> ".$join_info[$i]["field_name"]."</td>
									<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'>
									<textarea class='textbox' name='".$join_info[$i]["field"]."' >".$db->dt[$join_info[$i]["field"]]."</textarea>
									</td>
								</tr>";
									}
								}
$Contents .= "
							</table>

							<table width='100%' border='0'>
								<tr>
									<td align='left'>
										※ <span class='small'>  비밀번호 변경을 원치않을 경우 [비밀번호], [비번확인]을 공백으로 유지</span> ";
										if(!$db->dt[name]){
                                            $Contents.="
                                            <br>
                                            <span class='small' style='color:red;'>  ※ 필수 회원정보를 받아 수동 등록이 필요합니다.</span> ";

										}
    									$Contents.="
									</td>
									<td align='right'>";
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
										$Contents.="
										<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle>";
									}else{
										$Contents.="
										<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle></a>";
									}
									$Contents.="
									</td>
								</tr>
							</table>
				<!-- 수정마침 -->
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		</form>
	</td>
</tr>
</TABLE>";
}

if($info_type == "c_member"){	//사업자 정보

$Contents .= "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "회원관리 > 회원정보 수정", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 0px 0 0px;height:569px;vertical-align:top'>

				    <form name='EDIT_".$db->dt[code]."' method='post' onsubmit='return CheckFormValue(this)' action='member.act.php'  target='act' enctype='multipart/form-data'><!---->
					<input type='hidden' name='act' value='".$act."'>
					<input type='hidden' name='code' value='".$db->dt[code]."'>
					<input type='hidden' name='company_id' value='".$db->dt[company_id]."'>
					<input type='hidden' name='info_type' value='c_member'>
					<input type='hidden' name='csrfToken' value='".$csrfToken."'>
						<table border='0' width='100%' cellspacing='1' cellpadding='0'>
						<tr>
							<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
									<col width=15%>
									<col width=35%>
									<col width=15%>
									<col width=35%>";

	$Contents .= "					<tr>
										<td class='input_box_title' nowrap> 등록일</td>
										<td class='input_box_item'>&nbsp;".$db->dt[regdate2]."</td>
										<td class='input_box_title' nowrap> 방문일</td>
										<td class='input_box_item'>&nbsp;".$db->dt[last]."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 이름 <img src='".$required3_path."'></td>
										<td class='input_box_item'><input type='text' class='textbox' name='name' size='17' maxlength='20' value='".$db->dt[name]."' validation=true title='이름'></td>
										<td class='input_box_title' nowrap> 아이디 <img src='".$required3_path."'></td>
										<td class='input_box_item' >";
										if($act == "update"){
											$Contents .= "&nbsp;".$db->dt[id]."";
										}else{
											$Contents .= "<input type='text' class='textbox' id='user_id' name='id' size='17' maxlength='20' value='' validation=true title='아이디'><span id='idCheckText'></span>";
										}
										$Contents .= "
										</td>
									</tr>";

									//회원구분이 기업회원일때만 회사정보 노출
	if($db->dt[mem_type] == "C"){

	$Contents .= "
									<tr >
										<td class='input_box_title' nowrap> 거래처 코드</td>
										<td class='input_box_item'>
										<table cellpadding=0 cellspacing=0 border=0>
											<tr>
												<td width=250>
												".SearchCompany($company_id ,$apply_charger_ix,"",'select','1')."
												</td>
												<td width=250 style='display:none'>
												<input type='text' name='charger_name' value='' style='width: 130px; border: 1px solid rgb(204, 204, 204);'>
												</td>
											</tr>
										</table>
										</td>

										<td class='input_box_title' nowrap> 사업자번호</td>
										<td class='input_box_item'>
											<input type='text' class='textbox' name='com_num1' id='com_number_1' size='3' maxlength='3' value='".$com_num1."'> -
											<input type='text' class='textbox' name='com_num2' id='com_number_2' size='2' maxlength='4' value='".$com_num2."'> -
											<input type='text' class='textbox' name='com_num3' id='com_number_3' size='5' maxlength='5' value='".$com_num3."'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 회사명</td>
										<td class='input_box_item'>
											<input type='text' class='textbox' name='com_name' id='com_name'  value='".$db->dt[com_name]."'>
										</td>
										<td class='input_box_title' nowrap> 대표전화</td>
										<td class='input_box_item'>
											<input type='text' class='textbox' name='com_phone1' id='com_phone_1' size='3' maxlength='3' value='".$com_phone1."'> -
											<input type='text' class='textbox' name='com_phone2' id='com_phone_2' size='4' maxlength='4' value='".$com_phone2."'> -
											<input type='text' class='textbox' name='com_phone3' id='com_phone_3' size='4' maxlength='4' value='".$com_phone3."'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 핸드폰번호</td>
										<td class='input_box_item'>
											<input type='text' class='textbox' name='com_mobile1' id='com_mobile_1' size='3' maxlength='3' value='".$com_mobile1."'> -
											<input type='text' class='textbox' name='com_mobile2' id='com_mobile_2' size='4' maxlength='4' value='".$com_mobile2."'> -
											<input type='text' class='textbox' name='com_mobile3' id='com_mobile_3' size='4' maxlength='4' value='".$com_mobile3."'>
										</td>
										<td class='input_box_title' nowrap> 이메일</td>
										<td class='input_box_item'>
											<input type='text' class='textbox' id='com_email' name='com_email' size='66' maxlength='80' style='width:200px;' value='".$db->dt[com_email]."' style='margin:2px 0px'><br>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 업태</td>
										<td class='input_box_item'><input type='text' class='textbox' name='com_business_status' id='com_business_status' maxlength='60' value='".$db->dt[com_business_status]."'></td>
										<td class='input_box_title' nowrap> 업종</td>
										<td class='input_box_item'><input type='text' class='textbox' name='com_business_category' id='com_business_category' maxlength='60' value='".$db->dt[com_business_category]."'></td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 기업형태 <img src='".$required3_path."'></td>
										<td class='input_box_item'>
											<input type='radio' name='com_div' id='com_div_R' value='R' ".CompareReturnValue("R",$db->dt[com_div],"checked")."><label for='com_div_R'>법인사업자</label> &nbsp;
											<input type='radio' name='com_div' id='com_div_P' value='P' ".CompareReturnValue("P",$db->dt[com_div],"checked")."><label for='com_div_P'>개인사업자</label> &nbsp;<br>
											<input type='radio' name='com_div' id='com_div_S' value='S' ".CompareReturnValue("S",$db->dt[com_div],"checked")."><label for='com_div_S'>간이과세자</label> &nbsp;
											<input type='radio' name='com_div' id='com_div_E' value='E' ".CompareReturnValue("E",$db->dt[com_div],"checked")."><label for='com_div_E'>면세사업자</label>
										</td>
										<td class='input_box_title' nowrap> 대표자</td>
										<td class='input_box_item'>
											<input type='text' class='textbox' name='com_ceo' id='com_ceo' size='10' maxlength='10' value='".$db->dt[com_ceo]."'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 법인번호</td>
										<td class='input_box_item'>
											<input type=text name='corporate_number_1' id='corporate_number_1' value='".$corporate_number[0]."' maxlength=5 size=5  class='textbox' com_numeric=true validation='false' title='법인번호'> -
											<input type=text name='corporate_number_2' id='corporate_number_2' value='".$corporate_number[1]."' maxlength=5 size=5 class='textbox' com_numeric=true validation='false' title='법인번호'> -
											<input type=text name='corporate_number_3' id='corporate_number_3' value='".$corporate_number[2]."' maxlength=5 size=5 class='textbox' com_numeric=true validation='false' title='법인번호'>
										</td>
										<td class='input_box_title' nowrap>회사 홈페이지</td>
										<td class='input_box_item'>
											<input type='text' class='textbox' id='com_homepage' name='com_homepage'  size='66' maxlength='80'  style='width:200px;' value='".$db->dt[com_homepage]."'>
										</td>
									</tr>
									<tr height=50>
										<td class='input_box_title'2 nowrap> 회사주소</td>
										<td bgcolor='#ffffff' colspan=3 style='padding:5px 0px 5px 5px;'>
											<table border='0' width=100% cellpadding='0' cellspacing='0' >
											<col width='30px'>
											<col width='*'>
											<tr>
												<td colspan =2>
													<input type='text' class='textbox' name='com_zip' id='zip_b_1' value='".$db->dt[com_zip]."' readonly>
													<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;' align=absmiddle>
												</td>
												<td style='padding:0px 0 0 5px;text-align:left;'>
												</td>
											</tr>
											<tr>
												<td colspan=2>
													<input type='text' class='textbox' id='addr_b_1' name='com_addr1' size='66' maxlength='80' value='".$db->dt[com_addr1]."' style='margin:2px 0px'><br>
													<input type='text' class='textbox' id='addr_b_2' name='com_addr2' size='66' maxlength='80' value='".$db->dt[com_addr2]."' style='margin:2px 0px'> 세부주소
												</td>
											</tr>
											</table>
										</td>
									</tr>";
	$Contents .= "
									<tr bgcolor=#ffffff height=34>
										<td class='input_box_title'> <b>통신 판매업 신고서 사본</b>  </td>
										<td class='input_box_item' colspan=3 style='padding:5px 5px;'>
											<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
											<tr>
												<td width='250'>
													<input type=file name='business_file' size=30 class='textbox'  style='width:200px;'>
												</td>
											";

											if(is_file($path."/".$business_file)){
											$Contents .= "
											<td width='70'>
											<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$business_file."' width=50 height=50>&nbsp;&nbsp;&nbsp;
											</td>";
											$Contents .= "
													<td width='140'>
													<input type='button' name='file_down' value='다운로드 받기' onclick=\"download_img('".$path."/".$business_file."')\" class='textbox' style='cursor: pointer;'>
													<a href='javascript:' onclick=\"del('".$business_file."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
													</td>";
											}
										$Contents .= "
												<td>
												<b>&nbsp;&nbsp;2M 이하만 등록가능</b>
												</td>
											</tr>
											</table>
										</td>
									</tr>
			";
	}

								$cnt_join_info=count($join_info);

									for($i=0;$i<$cnt_join_info;$i++) {
										if($join_info[$i]["field_type"]=="text" || $join_info[$i]["field_type"]=="password") {
	$Contents .= "
									<tr>
										<td class='input_box_title' nowrap> ".$join_info[$i]["field_name"]." </td>
										<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'>
										<input type='text' class='textbox' name='".$join_info[$i]["field"]."' size='36' maxlength='80' value='".$db->dt[$join_info[$i]["field"]]."'>
										</td>
									</tr>";

										} else if($join_info[$i]["field_type"]=="radio" || $join_info[$i]["field_type"]=="checkbox") {

	$Contents .= "
									<tr>
										<td class='input_box_title' nowrap> ".$join_info[$i]["field_name"]." </td>
										<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'>";


											$cnt_field_value_text=count($join_info[$i]["field_value_text"]);
											for($j=0;$j<$cnt_field_value_text;$j++) {

	$Contents .= "
											<input type='".$join_info[$i]["field_type"]."' style='border:0px;' name='".$join_info[$i]["field"]."' value='".$join_info[$i]["field_value_text"][$j]."' ".(($db->dt[$join_info[$i]["field"]]==$join_info[$i]["field_value_text"][$j]) ? "checked":"")." /> ".$join_info[$i]["field_value_text"][$j]."";

											}
	$Contents .= "
										</td>
									</tr>";

										} else if($join_info[$i]["field_type"]=="select") {
	$Contents .= "
									<tr>
										<td class='input_box_title' nowrap> ".$join_info[$i]["field_name"]." asdf</td>
										<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'>
											<select name='".$join_info[$i]["field"]."'>";

											$cnt_field_value_text=count($join_info[$i]["field_value_text"]);
											for($j=0;$j<$cnt_field_value_text;$j++) {
	$Contents .= "
												<option value='".$join_info[$i]["field_value_text"][$j]."' ".(($db->dt[$join_info[$i]["field"]]==$join_info[$i]["field_value_text"][$j]) ? "selected":"").">".$join_info[$i]["field_value_text"][$j]."</option>";


											}
	$Contents .= "
										</td>
									</tr>";

										} else if($join_info[$i]["field_type"]=="textarea") {
	$Contents .= "
									<tr>
										<td class='input_box_title' nowrap> ".$join_info[$i]["field_name"]."</td>
										<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'>
										<textarea class='textbox' name='".$join_info[$i]["field"]."' >".$db->dt[$join_info[$i]["field"]]."</textarea>
										</td>
									</tr>";
										}
									}
	$Contents .= "
								</table>

					<table width='100%' border='0'>
						<tr>
							<td align='left'>
								※ <span class='small'>  비밀번호 변경을 원치않을 경우 [비밀번호], [비번확인]을 공백으로 유지</span>
							</td>
							<td align='right'>
							<a href='../basic/seller.add.php?company_id=$company_id&info_type=basic' target='parent'> 사업자정보 수정하기 </a>
							";
							if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){	//사업자정보는 수정할수 없으므로 저장버튼 숨김
								//$Contents.="
								//<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle>";
							}else{
								//$Contents.="
								//<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle></a>";
							}
							$Contents.="
							</td>
						</tr>
					</table>
					<!-- 수정마침 -->

					</td>
				</tr>
				</table>
			</td>
		  </tr>
		</table>
		</form>
		</td>
	</tr>
</TABLE>";
}

if($info_type == "file_member"){
	$Contents .= "
	<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "회원관리 > 회원정보 수정", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 0px 0 0px;height:569px;vertical-align:top'>

				<form name='EDIT_".$db->dt[code]."' method='post' onsubmit='return CheckFormValue(this)' action='member.act.php' target='act'><!---->
				<input type='hidden' name='act' value='".$act."'>
				<input type='hidden' name='code' value='".$db->dt[code]."'>
				<input type='hidden' name='company_id' value='".$db->dt[company_id]."'>
				<input type='hidden' name='info_type' value='".$info_type."'>
				<input type='hidden' name='csrfToken' value='".$csrfToken."'>
				<table border='0' width='100%' cellspacing='1' cellpadding='0'>
				<tr>
					<td >
						<table border='0' width='100%' cellspacing='5' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
							<col width=15%>
							<col width=35%>
							<col width=15%>
							<col width=35%>";
$Contents .= "					<tr>
								<td class='input_box_title' nowrap> 등록일</td>
								<td class='input_box_item'>&nbsp;".$db->dt[regdate2]."</td>
								<td class='input_box_title' nowrap> 최근 방문일</td>
								<td class='input_box_item'>&nbsp;".$db->dt[last]."</td>
							</tr>
							<tr>
								<td class='input_box_title' nowrap> 이름 <img src='".$required3_path."'></td>
								<td class='input_box_item'><input type='text' class='textbox' name='name' size='17' maxlength='20' value='".$db->dt[name]."' validation=true title='이름'></td>
								<td class='input_box_title' nowrap> 아이디 <img src='".$required3_path."'></td>
								<td class='input_box_item' >";
								if($act == "update"){
									$Contents .= "&nbsp;".$db->dt[id]."";
								}else{
									$Contents .= "<input type='text' class='textbox' id='user_id' name='id' size='17' maxlength='20' value='' validation=true title='아이디'><span id='idCheckText'></span>";
								}
								$Contents .= "
								</td>
							</tr>";

				$Contents .= "
							<tr height=210>
								<td class='input_box_title' nowrap> 증빙서류 정보</td>
								<td class='input_box_item' nowrap colspan='3'>
									<table width='100%' border='0' >
										<tr height=28>
											<td><input type='radio' name='voucher_div' id='voucher_div_1' style='border:0px;' value='1' ".($db->dt[voucher_div] == "1" ? "checked":"")."><label for='voucher_div_1'> 개인소득공제</label>
											</td>
										</tr>
										<tr  >
											<td  style='padding-left:20px;'>
												<table width='100%' border='0'>
												<tr height=28>
													<td width='20%'>
														<input type='radio' name='voucher_num_div' id='voucher_num_div_1' style='border:0px;' value='1' ".($db->dt[voucher_num_div] == "1" || $db->dt[voucher_num_div] == ""? "checked":"")."><label for='voucher_num_div_1'> 휴대폰번호입력</label>
													</td>
													<td width='*'>
														<input type='text' class='textbox' name='voucher_phone1' size='3' maxlength='3' value='".$voucher_phone1."' style='width:30px;'> -
														<input type='text' class='textbox' name='voucher_phone2' size='4' maxlength='4' value='".$voucher_phone2."' style='width:40px;'> -
														<input type='text' class='textbox' name='voucher_phone3' size=4' maxlength='4' value='".$voucher_phone3."' style='width:40px;'>
													</td>
													<td width='30%'>
														사용자명 <input type='text' class='textbox' name='phone_voucher_name'  value='".$db->dt[phone_voucher_name]."'  style='width:60px;'>
													</td>
												</tr>
												<tr height=28>
													<td>
														<input type='radio' name='voucher_num_div' id='voucher_num_div_2' style='border:0px;' value='2' ".($db->dt[voucher_num_div] == "2" ? "checked":"")."><label for='voucher_num_div_2'> 현금영수증 카드번호</label>
													</td>
													<td>
														<input type='text' class='textbox' name='voucher_card1' size='5' maxlength='5' value='".$voucher_card1."' style='width:30px;'> -
														<input type='text' class='textbox' name='voucher_card2' size='5' maxlength='5' value='".$voucher_card2."' style='width:40px;'> -
														<input type='text' class='textbox' name='voucher_card3' size='5' maxlength='5' value='".$voucher_card3."' style='width:40px;'> -
														<input type='text' class='textbox' name='voucher_card4' size='5' maxlength='5' value='".$voucher_card4."' style='width:40px;'>
													</td>
													<td>
														사용자명 <input type='text' class='textbox' name='card_voucher_name'  value='".$db->dt[card_voucher_name]."'  style='width:60px;'>
													</td>
												</tr>
												</table>
											</td>
										</tr>
										<tr height=28>
											<td>
												<input type='radio' name='voucher_div' id='voucher_div_2' style='border:0px;' value='2' ".($db->dt[voucher_div] == "2" ? "checked":"")."><label for='voucher_div_2'> 지출증빙</label>
												<input type='text' class='textbox' name='expense_num1' size='5' maxlength='5' value='".$expense_num1."' style='width:40px;'> -
												<input type='text' class='textbox' name='expense_num2' size='5' maxlength='5' value='".$expense_num2."' style='width:40px;'> -
												<input type='text' class='textbox' name='expense_num3' size='5' maxlength='5' value='".$expense_num3."' style='width:40px;'>
											</td>
										</tr>
										<tr height=28>
											<td>
												<input type='radio' name='voucher_div' id='voucher_div_3' style='border:0px;' value='3' ".($db->dt[voucher_div] == "3" ? "checked":"")."><label for='voucher_div_3'> 세금계산서 발급(등록되어 있는 사업자로 발급)</label>
											</td>
										</tr>
										<tr>
											<td style='padding-left:20px;'>
												<table width='100%' border='0'>
												<tr height=28>
													<td>
														<input type='radio' name='certificate_yn' id='certificate_yn_1' style='border:0px;' value='1' ".($db->dt[certificate_yn] == "1" || $db->dt[certificate_yn] == "" ? "checked":"")."><label for='certificate_yn_1'> 결제완료 후 즉시 발급</label>
													</td>
												</tr>
												<tr height=28>
													<td>
														<input type='radio' name='certificate_yn' id='certificate_yn_2' style='border:0px;' value='2' ".($db->dt[certificate_yn] == "2" ? "checked":"")."><label for='certificate_yn_2'> 기간별 발급(관리자 수동발급)</label>
													</td>
												</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				</table>
				<table width='100%' border='0'>
				<tr>
					<td align='left'>
						<!--※ <span class='small'>  비밀번호 변경을 원치않을 경우 [비밀번호], [비번확인]을 공백으로 유지</span>-->
					</td>
					<td align='right'>";
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
						$Contents.="
						<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle>";
					}else{
						$Contents.="
						<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle></a>";
					}
					$Contents.="
					</td>
				</tr>
				</table>
				<!-- 수정마침 -->
			</td>
		</tr>
		</table>
		</form>
		</td>
	</tr>
	</TABLE>";

} else if($info_type=="return_bank") {
	$Contents .= "
	<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "회원관리 > 회원정보 수정", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 0px 0 0px;height:569px;vertical-align:top'>

					<form name='bank_info_form' method='post' onsubmit='return submit_refund(this)' action='../member/member.act.php'  target='act'><!---->
						<input type='hidden' name='act' id='mode' value='insert'>
						<input type='hidden' name='ucode' id='ucode' value='".$code."'>
						<input type='hidden' name='bank_ix' id='bank_ix' value=''>
						<input type='hidden' name='info_type' id='info_type' value='".$info_type."'>
						<input type='hidden' name='admin_type' id='admin_type' value='Y'>
						<input type='hidden' name='csrfToken' value='".$csrfToken."'>
					<table border='0' width='100%' cellspacing='1' cellpadding='0'>
					<tr>
						<td >
							<table border='0' width='100%' cellspacing='5' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
								<col width=15%>
								<col width=35%>
								<col width=15%>
								<col width=35%>";
$Contents .= "					<tr>
									<td class='input_box_title' nowrap> 은행명 <img src='".$required3_path."'></td>
									<td class='input_box_item'>
										<select id='bank_code' name='bank_code' value='' validation=true title='은행명'>
											<option value=''>선택해 주세요.</option>";
											foreach($arr_banks_name AS $key => $val) {//constants.php에서 정보 불러옴 kbk 13/07/05
												$Contents .= "<option value='".$key."'>".$val."</option>";
											}
										$Contents .= "</select>
									</td>
									<td class='input_box_title' nowrap>예금주  <img src='".$required3_path."'></td>
									<td class='input_box_item'>
										<input type='text' class='textbox' id='bank_owner' name='bank_owner' size='17' maxlength='20' value='' validation='true' title='예금주'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap>계좌번호 <img src='".$required3_path."'></td>
									<td class='input_box_item'>
										<input type='text' class='textbox' id='bank_number' name='bank_number' size='17' value='' validation='true' title='계좌번호'>
									</td>
									<td class='input_box_title' nowrap> 사용여부</td>
									<td class='input_box_item'>
										<input type='radio' id='use_yn_y' name='use_yn' value='Y' validation=true title='사용여부' checked> <label for='use_yn_y'>사용</label>
										<input type='radio' id='use_yn_n' name='use_yn' value='N' validation=true title='사용여부'> <label for='use_yn_n'>미사용</label>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					</table>
					<table width='100%' border='0'>
					<tr  >
						<td align='left'>
							<!--※ <span class='small'>  비밀번호 변경을 원치않을 경우 [비밀번호], [비번확인]을 공백으로 유지</span>-->
						</td>
						<td align='center' style='padding:10px;'>";
                        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                            $Contents.="
							<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle>";
                        }else{
                            $Contents.="
                            <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle></a>";
                        }
                        $Contents.="
						</td>
					</tr>
					</table>
				<!-- 수정마침 -->
			</form>

			<table cellspacing=0 cellpadding=0 width=100% class='list_table_box'>
				<col width='48' />
				<col width='160' />
				<col width='194' />
				<col width='186' />
				<col width='170' />
				<col width='*' />
				<tr bgcolor=#efefef align=center height=28>
					<td class='s_td'>No.</td>
					<td class='m_td'>은행명</td>
					<td class='m_td'>예금주</td>
					<td class='m_td'>계좌번호</td>
					<td class='m_td'>처리상태</td>
					<td class='e_td'>관리</td>
				</tr>";

$max = 10; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

if($db->dbms_type == "oracle"){
	$sql="SELECT COUNT(bank_ix) AS cnt FROM shop_user_bankinfo WHERE ucode='".$code."' ";
}else{
	$sql="SELECT COUNT(bank_ix) AS cnt FROM shop_user_bankinfo WHERE ucode='".$code."' ";
}
$db->query($sql);
$db->fetch();

$total = $db->dt["cnt"];

$no = $total - ($page - 1) * $max;
if($db->dbms_type == "oracle"){
	$sql="SELECT bank_ix, ucode, bank_code, use_yn, is_basic, regdate, editdate,
					AES_DECRYPT(UNHEX(bank_name),'".$db->ase_encrypt_key."') as bank_name,
					AES_DECRYPT(UNHEX(bank_number),'".$db->ase_encrypt_key."') as bank_number,
					AES_DECRYPT(UNHEX(bank_owner),'".$db->ase_encrypt_key."') as bank_owner,
					$no AS no 
				FROM shop_user_bankinfo WHERE ucode='".$code."' ORDER BY regdate DESC LIMIT $start, $max ";
}else{
	$sql="SELECT bank_ix, ucode, bank_code, use_yn, is_basic, regdate, editdate,
					AES_DECRYPT(UNHEX(bank_name),'".$db->ase_encrypt_key."') as bank_name,
					AES_DECRYPT(UNHEX(bank_number),'".$db->ase_encrypt_key."') as bank_number,
					AES_DECRYPT(UNHEX(bank_owner),'".$db->ase_encrypt_key."') as bank_owner, 
					$no AS no 
				FROM shop_user_bankinfo WHERE ucode='".$code."' ORDER BY regdate DESC LIMIT $start, $max ";
}

$db->query($sql);
if($db->total) {
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
	$Contents .= "<tr height=28 align=center>
					<td bgcolor='#fbfbfb'>".($db->dt[no]-$i)."</td>
					<td bgcolor='#fbfbfb'>".$db->dt[bank_name]."</td>
					<td bgcolor='#fbfbfb'>".$db->dt[bank_owner]."</td>
					<td bgcolor='#fbfbfb'>".$db->dt[bank_number]."</td>
					<td bgcolor='#fbfbfb'>".($db->dt[use_yn]=="Y"?"사용":"미사용")."</td>
					<td bgcolor='#fbfbfb'>";
						if($update_auth){
							$Contents .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"edit_bank('".$db->dt[bank_ix]."','".$db->dt[bank_code]."','".$db->dt[bank_owner]."','".$db->dt[bank_number]."','".$db->dt[use_yn]."');\" style='cursor:pointer;' alt='수정' title='수정'/> ";
						}else{
							$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정' ></a> ";
						}

						//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
						if($delete_auth){
							$Contents .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"del_bank('".$db->dt[bank_ix]."','".$code."')\" style='cursor:pointer;' alt='삭제' title='삭제'/> ";
						}else{
							//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
						}
					$Contents .= "</td>
				</tr>";
	}
		$Contents .= "<tr height=40><td colspan=6 align=center>".page_bar($total, $page, $max,"&info_type=$info_type&mmode=$mmode&code=$code","")."</td></tr>";
} else {
		$Contents .= "<tr height=60><td colspan=6 align=center>등록된 정보가 없습니다.</td></tr>";
}
			$Contents .= "</td>
			 </tr>
			</table>
		</td>
	</tr>
	</TABLE>
	</td>
</tr>
</TABLE>";
}


if($info_type=="shipping_addr") {	//배송지관리
	$Contents .= "
	<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "회원관리 > 회원정보 수정", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 0px 0 0px;height:569px;vertical-align:top'>
				<form name='bank_info_form' method='post' onsubmit='return submit_refund(this)' action='/mypage/refund_account.act.php'  target='act'><!---->
				<input type='hidden' name='mode' id='mode' value='insert'>
				<input type='hidden' name='ucode' id='ucode' value='".$code."'>
				<input type='hidden' name='bank_ix' id='bank_ix' value=''>
				<input type='hidden' name='info_type' id='info_type' value='".$info_type."'>
				<input type='hidden' name='admin_type' id='admin_type' value='Y'>
				<input type='hidden' name='csrfToken' value='".$csrfToken."'>
				<table width='100%' border='0'>
					<tr>
						<td align='left' style='padding-left:0px;'>
							※ <span class='small'> 상품을 배송 받으실 배송지를 관리/추가 하실수 있습니다.</span>
						</td>
						<td align='right'>";
	/*
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
							$Contents.="
							<a href='javascript:void(0);' onclick=\"window.open('/admin/member/addressbook_add_pop.php?act=insert&code=".$code."', 'addressbook_add_pop', 'width=730,height=476,scrollbars=yes,resizeble=no');\"><img src='../images/korea/tambahkan_btn01.gif' title='' align='' /></a>";
						}else{
							$Contents.="
							<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle></a>";
						}
	*/
						$Contents.="
						</td>
					</tr>
				</table>
				<!-- 수정마침 -->
			</form>
			<table cellspacing=0 cellpadding=0 width=100% class='list_table_box'>
				<col width='4%' />
				<col width='10%' />
				<col width='8%' />
				<col width='10%' />
				<col width='*' />
				<col width='7%' />
				<col width='9%' />
				<tr bgcolor=#efefef align=center height=28>
					<td class='s_td'>번호</td>
					<td class='m_td'>배송지명</td>
					<td class='m_td'>받는사람</td>
					<td class='m_td'>전화번호</td>
					<td class='m_td'>배송지주소</td>
					<td class='m_td'>상태</td>
					<!--<td class='e_td'>관리</td>-->
				</tr>";

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT count(ix) as cnt FROM shop_shipping_address WHERE mem_ix = '".$code."'";

	$db->query($sql);
	$db->fetch();

	$total = $db->dt["cnt"];
	$no = $total - ($page - 1) * $max;

	$sql = "SELECT *,$no AS no FROM shop_shipping_address WHERE mem_ix = '".$code."' LIMIT $start, $max ";
	$db->query($sql);


if($db->total) {
	for($i=0;$i  < $db->total; $i++){

		$db->fetch($i);

	$Contents .= "<tr height=30 align=center>
					<td bgcolor='#fbfbfb'>".($db->dt[no]-$i)."</td>
					<td bgcolor='#fbfbfb'>".$db->dt[shipping_name]."</td>
					<td bgcolor='#fbfbfb'>".$db->dt[recipient]."</td>
					<td bgcolor='#fbfbfb'>".$db->dt[mobile]."</td>
					<td bgcolor='#fbfbfb' style='text-align:left;padding-left:10px;'>".$db->dt[address1]." ".$db->dt[address2]."</td>
					<td bgcolor='#fbfbfb'>".($db->dt[default_yn]=="Y"?"기본배송지":"-")."</td>
					";
					if(false) {
                        $Contents .= "
					<td bgcolor='#fbfbfb'>";

                        if ($update_auth) {
                            $Contents .= "<a href='javascript:void(0);' onclick=\"oneSubmit('update', '" . $db->dt[ix] . "','Y');\"><img src='../images/" . $admininfo["language"] . "/bts_modify.gif' border=0 align=absmiddle style='cursor:pointer;' alt='수정' title='수정'/></a> ";
                        } else {
                            $Contents .= "<a href=\"" . $auth_update_msg . "\"><img src='../images/" . $admininfo["language"] . "/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정' ></a> ";
                        }

                        //$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
                        if ($delete_auth) {
                            $Contents .= "<a href='javascript:void(0);' onclick=\"oneSubmit('delete', '" . $db->dt[ix] . "', 'Y')\"><img src='../images/" . $admininfo["language"] . "/btn_del.gif' border=0 align=absmiddle style='cursor:pointer;' alt='삭제' title='삭제'/> ";
                        } else {
                            //$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
                        }
                        $Contents .= "</td>";
                    }
        $Contents .= "
				</tr>";
	}
		$Contents .= "<tr height=40><td colspan=6 align=center>".page_bar($total, $page, $max,"&info_type=$info_type&mmode=$mmode&code=$code","")."</td></tr>";
} else {
		$Contents .= "<tr height=60><td colspan=6 align=center>등록된 정보가 없습니다.</td></tr>";
}
			$Contents .= "</td>
			 </tr>
			</table>
			</td>
		</tr>
	</TABLE>
	</td>
	</tr>
</TABLE>";
}

if($info_type == 'my_pet'){
	$sql = "select cp.pet_id
				 , cp.pet_name
				 , cp.pet_group
				 , cp.pet_option
				 , cp.pet_gender
				 , cp.pet_birth
				 , cp.pet_weight
				 , cp.pet_reg_length
				 , cp.pet_back_length
				 , cp.pet_bust
				 , cp.pet_regist_date
			  from ".TBL_COMMON_PET." cp
				 , ".TBL_COMMON_USER." cu 
			 where cu.code = '$code' 
			   and cp.code = cu.code 
		  order by cp.pet_regist_date ";

	$db->query($sql);

    $Contents .= "
<table cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr>
		<td align=center colspan=2 valign=top>
			<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
				<tr>
					<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "회원관리 > 회원정보 수정", false)."</td>
				</tr>
				<tr>
					<td align=center style='padding: 0 0px 0 0px;height:569px;vertical-align:top'>
						<form name='EDIT_".$db->dt[code]."' method='post' onsubmit='return CheckFormValue(this)' action='member.act.php'  target='act'>
						<input type='hidden' name='act' value='".$act."'>
						<input type='hidden' name='info_type' value='".$info_type."'>
						<input type='hidden' name='csrfToken' value='".$csrfToken."'>
";

if($db->total){
    for($i=0; $i<$db->total; $i++){
        $pet_result = $db->fetch($i);

		//성별
		$pet_gender_w = '';
        $pet_gender_m = '';
		if($pet_result[pet_gender] == "W"){
			$pet_gender_w = 'checked';
		}else{
            $pet_gender_m = 'checked';
		}
		//생일
        $birth_yyyy = '';
        $birth_mm = '';
        $birth_dd = '';

		if($pet_result[pet_birth]){
			if(substr_count($pet_result[pet_birth], '-') == 2){
                list($pet_birth_yyyy, $pet_birth_mm, $pet_birth_dd) = explode('-', trim($pet_result[pet_birth]), 3);
                $pet_birth_yyyy = trim($pet_birth_yyyy);
                $pet_birth_mm = trim($pet_birth_mm);
                $pet_birth_dd = trim($pet_birth_dd);
			}
		}

        $Contents .= "
						<table border='0' width='100%' cellspacing='1' cellpadding='0' style='margin-bottom: 10px;'>
							<tr>
								<td>
									<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
										<col width=15%>
										<col width=35%>
										<col width=15%>
										<col width=35%>
										<tr>
											<td class='input_box_title' nowrap>이름</td>
											<td class='input_box_item'>
												<input type='text' class='textbox' id='' name='pet_name[]' value='".$pet_result[pet_name]."' validation='true' title='마이펫 이름'>
												<input type='hidden' name='pet_id[]' value='".$pet_result[pet_id]."'>
											</td>
											<td class='input_box_title' nowrap>분류/종류</td>
											<td class='input_box_item'>
												<select name='pet_group[]' onchange='' title='분류'>
													<option value=''>분류 선택</option>
													<option value='DOG' ".CompareReturnValue("DOG", $pet_result[pet_group],"selected").">DOG</option>
													<option value='CAT' ".CompareReturnValue("CAT", $pet_result[pet_group],"selected").">CAT</option>
												</select>
												<select name='pet_option[]' onchange='' title='종류'>
													<option value=''>종류 선택</option>
													<option value='1' ".CompareReturnValue("1", $pet_result[pet_option],"selected").">1</option>
													<option value='2' ".CompareReturnValue("2", $pet_result[pet_option],"selected").">2</option>
												</select>
											</td>
										</tr>
										<tr>
											<td class='input_box_title' nowrap>성별</td>
											<td class='input_box_item'>
												<input type='radio' id='male' name='pet_gender_".$i."[]' value='M' validation=false title='성별' ".$pet_gender_m."> <label for='male'><img src='../images/korea/Male.png' style='cursor:pointer;'></label>
												<input type='radio' id='female' name='pet_gender_".$i."[]' value='W' validation=false title='성별' ".$pet_gender_w."> <label for='female'><img src='../images/korea/Female.png' style='cursor:pointer;'></label>
											</td>
											<td class='input_box_title' nowrap>생년월일</td>
											<td class='input_box_item'>
												<input type='text' class='textbox' name='pet_birth_yyyy[]' size='4' maxlength='4' value='".$pet_birth_yyyy."' title='생년월일'>
												<input type='text' class='textbox' name='pet_birth_mm[]' size='2' maxlength='2' value='".$pet_birth_mm."' title='생년월일'>
												<input type='text' class='textbox' name='pet_birth_dd[]' size='2' maxlength='2' value='".$pet_birth_dd."' title='생년월일'>
											</td>
										</tr>
										<tr>
											<td class='input_box_title' nowrap>몸무게</td>
											<td class='input_box_item'>
												<input type='text' class='textbox' id='' name='pet_weight[]' value='".$pet_result[pet_weight]."' validation='false' title='몸무게'>
											</td>
											<td class='input_box_title' nowrap>다리길이</td>
											<td class='input_box_item'>
												<input type='text' class='textbox' id='' name='pet_reg_length[]' value='".$pet_result[pet_reg_length]."' validation='false' title='다리길이'>
											</td>
										</tr>
										<tr>
											<td class='input_box_title' nowrap>등길이</td>
											<td class='input_box_item'>
												<input type='text' class='textbox' id='' name='pet_back_length[]' value='".$pet_result[pet_back_length]."' validation='false' title='등길이'>
											</td>
											<td class='input_box_title' nowrap>가슴둘레</td>
											<td class='input_box_item'>
												<input type='text' class='textbox' id='' name='pet_bust[]' value='".$pet_result[pet_bust]."' validation='false' title='가슴둘레'>
											</td>
										</tr>																				
									</table>
								</td>
							</tr>
						</table>
";

    }
}

$Contents .= "						
						<table width='100%' border='0'>
							<tr>
								<td align='right'>
";
								if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
									$Contents .= "
									<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle>";
								}else{
									$Contents .= "
									<a href=\"".$auth_write_msg."\" ><img src = '../images/".$admininfo["language"]."/b_save.gif' border = 0 style = 'cursor:pointer;border:0px;' align = absmiddle ></a > ";
								}
								$Contents .= "
								</td>
							</tr>
						</table>

						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
";
}

$Script .="
<SCRIPT type='text/javascript'>

function oneSubmit(act, ix, admin_type) {
	if(act == 'update') {
		window.open('/admin/member/addressbook_add_pop.php?act='+act+'&ix='+ix, 'actpop', 'width=700,height=470,resizeble=yes');
	} else if(act == 'delete') {
		if(confirm('삭제하시면 복구할 수 없습니다. 정말로 삭제하시겠습니까?')) {
			window.location.href='/mypage/addressbook.act.php?act='+act+'&ix='+ix+'&admin_type='+admin_type;
		}
	}
}
</SCRIPT>
";

if($mmode == "pop"){
    $P = new ManagePopLayOut();
    $P->addScript = "<script language='javascript' src='../basic/company.add.js'></script>".$Script;
    $P->Navigation = "회원관리 > 회원정보수정";
    $P->NaviTitle = "회원정보수정";
    $P->title = "회원정보수정";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}else if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script language='javascript' src='../basic/company.add.js'></script>".$Script;
    $P->Navigation = "회원관리 > 회원정보수정";
	if($info_type == "shipping_addr"){
	$P->NaviTitle = "배송지관리";
	 $P->title = "배송지관리";
	}else{
    $P->NaviTitle = "회원정보수정";
	 $P->title = "회원정보수정";
	}

	$P->strContents = $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else{
    $P = new LayOut();
    $P->addScript = "<script language='javascript' src='../basic/company.add.js'></script>".$Script;
    $P->Navigation = "회원관리 > 수동회원등록";
    $P->NaviTitle = "수동회원등록";
    $P->title = "수동회원등록";
    $P->strLeftMenu = member_menu();
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}

?>
	<script type="text/javascript">
		//  welshoop pw 추가
		function wel_chkPW(s_act){

			var s_act = document.getElementsByName("act")[0].value;
			var s_change_pass = document.getElementById("change_pass").checked;

			if(s_change_pass == true) {
					if(s_act == "insert") {
						var m_id = document.getElementsByName("id")[0].value;
						var m_pw = document.getElementsByName("pass")[0].value;
						var m_again = document.getElementsByName("again")[0].value;
					} else if(s_act == "update"){
						var m_id = document.getElementsByName("user_id_hide")[0].value;
						var m_pw = document.getElementsByName("pass")[0].value;
						var m_again = document.getElementsByName("again")[0].value;
					} else {
						alert("관리자에게 문의 바랍니다.");
						return false;
					}


					var num = m_pw.search(/[0-9]/g);                          //  숫자
					var eng = m_pw.search(/[a-z]/ig);                         //  영문
					var spe = m_pw.search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);     //  특수문자



					if(m_pw.length < 8 || m_pw.length > 20){
						alert("8자리 ~ 20자리 이내로 입력해주세요.");
						return false;
					}

					if(m_pw.search(/\s/) != -1){
						alert("비밀번호는 공백 없이 입력해주세요.");
						return false;
					}

					if(m_pw.search(m_id) > -1){
						alert("비밀번호에 아이디가 포함되었습니다.");
						return false;

					}

					if(m_pw.length >= 10){
						if((num < 0 && eng < 0) || (eng < 0 && spe < 0) || (spe < 0 && num < 0)) {
							alert("영문, 숫자, 특수문자 중 2가지 이상을 혼합하여 입력해주세요.");
							return false;
						}
					} else if(m_pw.length >= 8) {
						if((num < 0 || eng < 0 || spe < 0)) {
							alert("영문, 숫자, 특수문자 중 3가지를 혼합하여 입력해주세요.");
							return false;
						}
					}



					if(m_pw != m_again){
						alert("비밀번호와 비번확인이 일치하지 않습니다.");
						return false;
					}

					 //console.log("사용가능한 패스워드 입니다.");
			}
		}
	</script>