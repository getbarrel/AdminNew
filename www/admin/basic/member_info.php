<?
include("../class/layout.class");


$Script = "
<script language='JavaScript' src='member.js'></Script>
<style>

input {border:1px solid #c6c6c6;padding:3px;}
.member_table td {text-align:left;}
</style>";

$db = new Database;
$mdb = new Database;

if($act == "insert" || $code == ""){
	$act = "insert";
}else{
	$act = "update";

	if($db->dbms_type == "oracle"){
		$sql = "SELECT cmd.code,
				AES_DECRYPT(name,'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(mail,'".$db->ase_encrypt_key."') as mail,
				AES_DECRYPT(zip,'".$db->ase_encrypt_key."') as zip,
				AES_DECRYPT(addr1,'".$db->ase_encrypt_key."') as addr1,
				AES_DECRYPT(addr2,'".$db->ase_encrypt_key."') as addr2,
				AES_DECRYPT(tel,'".$db->ase_encrypt_key."') as tel,
				AES_DECRYPT(pcs,'".$db->ase_encrypt_key."') as pcs,
				birthday,birthday_div,tel_div,info,sms,nick_name,job,cmd.date_ as regdate2,cmd.file_,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,black_list,
				add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*  , ccd.*
				FROM  ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_USER." cu
				left join ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
			where cu.code = cmd.code  and cu.code = '$code'
			ORDER BY cu.date_ DESC";
	}else{
		$sql = "SELECT cmd.code,
				AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail,
				AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,
				AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
				AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,
				AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,
				AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
				birthday,birthday_div,tel_div,info,sms,nick_name,job,cmd.date as regdate2,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,black_list,
				add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*  , ccd.*
				FROM  ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_USER." cu
				left join ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
			where cu.code = cmd.code  and cu.code = '$code'
			ORDER BY cu.date DESC";
	}
	//echo $sql;
	$db->query($sql);
	$db->fetch();

	$com_zip   = explode("-", $db->dt[com_zip]);

	$tel   = explode("-", $db->dt[tel]);
	$pcs   = explode("-", $db->dt[pcs]);
	$zip   = explode("-", $db->dt[zip]);
	if($db->dt["birthday"] != ""){
		$birthday = explode("-",$db->dt["birthday"]);
	}
	list($com_phone1, $com_phone2, $com_phone3) = split("-",$db->dt[com_phone]);
	list($com_fax1, $com_fax2, $com_fax3) = split("-",$db->dt[com_fax]);
	list($com_num1, $com_num2, $com_num3) = split("-",$db->dt[com_number]);


	//if ($db->dt[info]) $info_y = " checked"; else $info_n = " checked";
}

$db2 = new Database;
$db2->query("select * from shop_join_info where disp = 'Y' and field like 'add_etc%' order by vieworder ");
$join_info = $db2->fetchall();
//print_r($join_info);
if(is_array($join_info))
{
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
        $db->dt[mem_type] = 'C';
    }
}

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "회원관리 > 회원정보 수정", false)."</td>
			</tr>";
			if($act == "update"){
				$Contents .= " <tr height=30><td class='p11 ls1' style='padding:0 0 0 20px;text-align:left;' > <b>".Black_list_check($db->dt[code],$db->dt[name])."</b> 님의 회원정보 입니다.</td></tr>";
			}else{
				$Contents .= " <tr height=30><td class='p11 ls1' style='padding:0 0 0 20px;text-align:left;' > 수동으로 회원정보를 등록 하실수 있습니다.</td></tr>";
			}
			$Contents .= "
			<tr>
				<td align=center style='padding: 0 10px 0 10px;height:569px;vertical-align:top'>

				      <form name='EDIT_".$db->dt[code]."' method='post' onsubmit='return CheckFormValue(this)' action='member.act.php'  target='iframe_act'><!---->
					  <input type='hidden' name='act' value='".$act."'>
					  <input type='hidden' name='code' value='".$db->dt[code]."'>
					  <input type='hidden' name='company_id' value='".$db->dt[company_id]."'>
					  <table border='0' cellspacing='1' cellpadding='5' width='100%'>
						<tr>
						  <td bgcolor='#F8F9FA'>

				<table border='0' width='100%' cellspacing='1' cellpadding='0'>
					<tr>
						<td >
							<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
								<col width=15%>
								<col width=35%>
								<col width=15%>
								<col width=35%>
                                ";
if($act == 'insert'){
$Contents .= "
								<tr>
                                    <td class='input_box_title' nowrap> 회원구분 <img src='".$required3_path."'></td>
									<td class='input_box_item'>
										<select name='mem_type' onchange='select_mem_type()' validation=true title='회원구분'>
											<option value='' ".($db->dt[mem_type] == "" ? "selected":"").">회원구분</option>
											<option value='C' ".($db->dt[mem_type] == "C" ? "selected":"").">기업회원</option>
                                            <option value='M' ".($db->dt[mem_type] == "M" ? "selected":"").">개인회원</option>
											<option value='F' ".($db->dt[mem_type] == "F" ? "selected":"").">외국인회원</option>
										</select>
									</td>
                                    <td class='input_box_title' nowrap> 사용자그룹 <img src='".$required3_path."'></td>
									<td class='input_box_item'>".makeGroupSelectBox($mdb,"gp_ix",$db->dt[gp_ix],"validation=true title='사용자 그룹'")."</td>
								</tr>";
}
$Contents .= "
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
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 생년월일</td>
									<td class='input_box_item' nowrap>
										<input type='text' class='textbox' name='birthday_yyyy' size='4' maxlength='4' value='".$birthday[0]."'> -
										<input type='text' class='textbox' name='birthday_mm' size='2' maxlength='2' value='".$birthday[1]."'> -
										<input type='text' class='textbox' name='birthday_dd' size='2' maxlength='2' value='".$birthday[2]."'>
										<input type='radio' name='birthday_div' id='birthday_div_1' style='border:0px;' value='1' ".($db->dt[birthday_div] == "1" ? "checked":"")."><label for='birthday_div_1'>양력</label>
										<input type='radio' name='birthday_div' id='birthday_div_0' style='border:0px;' value='0' ".($db->dt[birthday_div] == "0" ? "checked":"")."><label for='birthday_div_0'>음력</label>
									</td>
									<td class='input_box_title' nowrap> 주민번호</td>
									<td class='input_box_item'>회원정보에는 주민번호를 받을수 없습니다.";


									$Contents .= "
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 휴대폰</td>";
					  if($db->dt[mem_type] == "F"){
                      $Contents .= "<td class='input_box_item'>
										<input type='text' class='textbox' name='pcs1' size='20' maxlength='20' value='".$db->dt[pcs]."'>
									</td>";
                      }else{
                      $Contents .= "<td class='input_box_item'>
										<input type='text' class='textbox' name='pcs1' size='3' maxlength='3' value='".$pcs[0]."'> -
										<input type='text' class='textbox' name='pcs2' size='4' maxlength='4' value='".$pcs[1]."'> -
										<input type='text' class='textbox' name='pcs3' size='4' maxlength='4' value='".$pcs[2]."'>
									</td>";
                      }
				      $Contents .= "<td class='input_box_title' nowrap> SMS 수신여부 <img src='".$required3_path."'></td>
									<td class='input_box_item'>
										<input type='radio' name='sms' value='1' ".($db->dt[sms] == "1" || $db->dt[sms] == "" ? "checked":"")." style='border:0px;'>수신함
										<input type='radio' name='sms' value='0' ".($db->dt[sms] == "0" ? "checked":"")." style='border:0px;'>수신안함
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
                      $Contents .= "</td>
									<td class='input_box_title' nowrap> 정보수신 <img src='".$required3_path."'></td>
									<td class='input_box_item'>
										<input type='radio' name='info' value='1' ".($db->dt[info] == "1" || $db->dt[info] == "" ? "checked":"")." style='border:0px;'>수신함
										<input type='radio' name='info' value='0' ".($db->dt[info] == "0" ? "checked":"")."  ".$info_n." style='border:0px;'>수신안함

									</td>

								</tr>
								<tr>
									<td class='input_box_title' nowrap> 우편번호</td>
									<td class='input_box_item' >

										<table border='0' cellpadding='0' cellspacing='0' >";
                        if($db->dt[mem_type] == "F"){
                          $Contents .= "<tr>
											<td style='border:0px;'>
												<input type='text' class='textbox' name='zip1' id='zip1' size='10' maxlength='10' value='".$db->dt[zip]."'>
											</td>
										</tr>";
                        }else{
                          $Contents .= "<tr>
											<td style='border:0px;'>
												<input type='text' class='textbox' name='zip1' id='zip1' size='3' maxlength='3' value='".$zip[0]."' readonly> -
												<input type='text' class='textbox' name='zip2' id='zip2' size='3' maxlength='3' value='".$zip[1]."' readonly>
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
									<td bgcolor='#ffffff' colspan=3 style='padding:5px 0px 5px 5px;'>";
                        if($db->dt[mem_type] == "F"){
                          $Contents .= "<input type='text' class='textbox' name='addr1' id='addr1' size='66' maxlength='80' value='".$db->dt[addr1]."' style='margin:2px 0px'>";

                        }else{
						  $Contents .= "<input type='text' class='textbox' name='addr1' id='addr1' size='66' maxlength='80' value='".$db->dt[addr1]."' style='margin:2px 0px' readonly><br>
										<input type='text' class='textbox' name='addr2' id='addr2' size='66' maxlength='80' value='".$db->dt[addr2]."' style='margin:2px 0px'> 세부주소";
                        }
						$Contents .= "</td>
								</tr>";

								//회원구분이 기업회원일때만 회사정보 노출
								if($db->dt[mem_type] == "C"){
$Contents .= "
								<tr >
									<td class='input_box_title' nowrap> 회사명</td>
									<td class='input_box_item'><input type='text' class='textbox' name='com_name' maxlength='60' value='".$db->dt[com_name]."'></td>
									<td class='input_box_title' nowrap> 사업자번호</td>
									<td class='input_box_item'>
										<input type='text' class='textbox' name='com_num1' size='3' maxlength='3' value='".$com_num1."'> -
										<input type='text' class='textbox' name='com_num2' size='2' maxlength='4' value='".$com_num2."'> -
										<input type='text' class='textbox' name='com_num3' size='5' maxlength='5' value='".$com_num3."'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 대표전화</td>
									<td class='input_box_item'>
										<input type='text' class='textbox' name='com_phone1' size='3' maxlength='3' value='".$com_phone1."'> -
										<input type='text' class='textbox' name='com_phone2' size='4' maxlength='4' value='".$com_phone2."'> -
										<input type='text' class='textbox' name='com_phone3' size='4' maxlength='4' value='".$com_phone3."'>
									</td>
									<td class='input_box_title' nowrap> fax</td>
									<td class='input_box_item'>
										<input type='text' class='textbox' name='com_fax1' size='3' maxlength='3' value='".$com_fax1."'> -
										<input type='text' class='textbox' name='com_fax2' size='4' maxlength='4' value='".$com_fax2."'> -
										<input type='text' class='textbox' name='com_fax3' size='4' maxlength='4' value='".$com_fax3."'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 종목</td>
									<td class='input_box_item'><input type='text' class='textbox' name='com_business_status' maxlength='60' value='".$db->dt[com_business_status]."'></td>
									<td class='input_box_title' nowrap> 업태</td>
									<td class='input_box_item'><input type='text' class='textbox' name='com_business_category' maxlength='60' value='".$db->dt["com_business_category"]."'></td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 기업형태 <img src='".$required3_path."'></td>
									<td class='input_box_item'>
										<input type='radio' name='com_div' id='com_div_p' value='P' validation=true title='기업형태' ".($db->dt[com_div] == "P" || $db->dt[com_div] == "" ? "checked":"")." style='border:0px;'><label for='com_div_p'>개인</label> &nbsp;&nbsp;
										<input type='radio' name='com_div' id='com_div_r' value='R' validation=true title='거래처형태' ".($db->dt[com_div] == "R" ? "checked":"")." style='border:0px;'><label for='com_div_r'>법인</label>
									</td>
									<td class='input_box_title' nowrap> 대표자</td>
									<td class='input_box_item'>
										<input type='text' class='textbox' name='com_ceo' size='10' maxlength='10' value='".$db->dt["com_ceo"]."'>
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 통신판매업신고번호</td>
									<td class='input_box_item'>
										<input type='text' class='textbox' name='online_business_number' size='30' maxlength='30' value='".$db->dt["online_business_number"]."'>
									</td>
									<td class='input_box_title' nowrap></td>
									<td class='input_box_item'>

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
												<input type='text' class='textbox' name='com_zip1' id='com_zip1' size='3' maxlength='3' value='".$com_zip[0]."' readonly> -
												<input type='text' class='textbox' name='com_zip2' id='com_zip2' size='3' maxlength='3' value='".$com_zip[1]."' readonly>
												<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('3');\" style='cursor:pointer;' align=absmiddle>
											</td>
											<td style='padding:0px 0 0 5px;text-align:left;'>

											</td>
										</tr>
										<tr>
											<td colspan=2>
												<input type='text' class='textbox' id='com_addr1' name='com_addr1' size='66' maxlength='80' value='".$db->dt[com_addr1]."' style='margin:2px 0px'><br>
												<input type='text' class='textbox' id='com_addr2' name='com_addr2' size='66' maxlength='80' value='".$db->dt[com_addr2]."' style='margin:2px 0px'> 세부주소
											</td>
										</tr>
										</table>
									</td>
								</tr>
                                <tr height=23 bgcolor='#ffffff'>
									<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 회사 홈페이지</td>
									<td class='input_box_item' colspan='3'>
                                        <input type='text' class='textbox' id='com_homepage' name='com_homepage' size='66' maxlength='80' value='".$db->dt[com_homepage]."'>
                                    </td>
								</tr>";

							}

$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 등록일</td>
									<td class='input_box_item'>&nbsp;".$db->dt[regdate2]."</td>
									<td class='input_box_title' nowrap> 방문일</td>
									<td class='input_box_item'>&nbsp;".$db->dt[last]."</td>
								</tr>";
if($act == 'update'){
$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 사용자그룹</td>
									<td class='input_box_item'>".makeGroupSelectBox($mdb,"gp_ix",$db->dt[gp_ix],"validation=true title='사용자 그룹'")."</td>
									<td class='input_box_title' nowrap> 회원구분</td>
									<td class='input_box_item'>
										<select name='mem_type'>
											<option value='M' ".($db->dt[mem_type] == "M" ? "selected":"").">개인회원</option>
											<option value='C' ".($db->dt[mem_type] == "C" ? "selected":"").">기업회원</option>
											<option value='F' ".($db->dt[mem_type] == "F" ? "selected":"").">외국인회원</option>
										</select>
									</td>
								</tr>";
}

if($act == 'insert'){
$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 비밀번호 <img src='".$required3_path."'></td>
									<td class='input_box_item'><input type='password' class='textbox' name='pass' size='16' maxlength='30' validation=true title='비밀번호'></td>
									<td class='input_box_title' nowrap> 비번확인 <img src='".$required3_path."'></td>
									<td class='input_box_item'><input type='password' class='textbox' name='again' size='16' maxlength='30' validation=true title='비번확인'></td>
								</tr>";
}else{
$Contents .= "
								<tr>
									<td class='input_box_title' nowrap> 비밀번호</td>
									<td class='input_box_item'><input type='password' class='textbox' name='pass' size='16' maxlength='30' ></td>
									<td class='input_box_title' nowrap> 비번확인</td>
									<td class='input_box_item'><input type='password' class='textbox' name='again' size='16' maxlength='30' ></td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap>
										<label for='black_list'>불량회원여부</label><input type='checkbox' name='black_list' id='black_list' value='Y' ".CompareReturnValue("Y",$db->dt[black_list],"checked").">
										<input type='hidden' name='befor_black_list' id='befor_black_list' value='".$db->dt[black_list]."'>
									</td>
									<td class='input_box_item' colspan='3'>
										<input type='text' class='textbox' name='msg' id='msg' size='66' value='".($db->dt[black_list]=='Y' ? '해지' : '등록')."사유를 입력해 주세요' onclick=\"$(this).val('')\" style='margin:10px 0px;width:95%'>
									</td>
								</tr>";
}
$Contents .= "
								<tr>
									<!--td class='input_box_title' nowrap> 후불결제 </td>
									<td bgcolor='#ffffff' style='color:red;font-weight:bold;' style='padding-left:5px;'>
										<input type='radio' name='afterpayment_yn' style='border:0px;' value='Y' id='afterpayment_yn_y_".$db->dt[code]."' ".CompareReturnValue($db->dt[afterpayment_yn] ,"Y"," checked")."><label for='afterpayment_yn_y_".$db->dt[code]."'>후불 가능</label>
										<input type='radio' name='afterpayment_yn' style='border:0px;' value='N' id='afterpayment_yn_n_".$db->dt[code]."' ".CompareReturnValue($db->dt[afterpayment_yn] ,"N"," checked")."><label for='afterpayment_yn_n_".$db->dt[code]."'>후불 불가능</label>  </td-->
									<td class='input_box_title' nowrap> 방문주소</td>
									<td class='input_box_item'>&nbsp;".$db->dt[ip]."</td>
									<td class='input_box_title' nowrap> 승인여부</td>
									<td class='input_box_item'>
										<select name='authorized'>
											<option value='Y' ".($db->dt[authorized] == "Y" ? "selected":"").">승인</option>
											<option value='N' ".($db->dt[authorized] == "N" ? "selected":"").">승인대기</option>
											<option value='X' ".($db->dt[authorized] == "X" ? "selected":"").">승인거부</option>
										</select>
									</td>
								</tr>
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
								<!-- 항목 설정에 따라서 항목을 뿌려줌 끝 kbk -->
								<!--tr>
									<td class='input_box_title' nowrap> 추가정보1</td>
									<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'><input type='text' class='textbox' name='add_etc1' size='36' maxlength='80' value='".$db->dt[add_etc1]."'></td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 추가정보2</td>
									<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'><input type='text' class='textbox' name='add_etc2' size='50' maxlength='100' value='".$db->dt[add_etc2]."'></td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 추가정보3</td>
									<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'><input type='text' class='textbox' name='add_etc3' size='50' maxlength='100' value='".$db->dt[add_etc3]."'></td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 추가정보4</td>
									<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'><input type='text' class='textbox' name='add_etc4' size='50' maxlength='100' value='".$db->dt[add_etc4]."'></td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 추가정보5</td>
									<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'><input type='text' class='textbox' name='add_etc5' size='50' maxlength='100' value='".$db->dt[add_etc5]."'></td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 추가정보6</td>
									<td bgcolor='#ffffff' colspan=3 style='padding-left:5px;'><input type='text' class='textbox' name='add_etc6' size='50' maxlength='100' value='".$db->dt[add_etc6]."'></td>
								</tr-->
							</table>
						</td>
					</tr>
				</table>
				<table width='100%' border='0'>
					<tr>
						<td align='left'>
							※ <span class='small'>  비밀번호 변경을 원치않을 경우 [비밀번호], [비번확인]을 공백으로 유지</span>
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
								</tr>
				              </table>
				            </td>
				          </tr>
				        </table>
				        </form>


		</td>
	</tr>

</TABLE>";


if($mmode == "pop"){
    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->Navigation = "회원관리 > 회원정보수정";
    $P->NaviTitle = "회원정보수정";
    $P->title = "회원정보수정";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}else{
    $P = new LayOut();
    $P->addScript = $Script;
    $P->Navigation = "회원관리 > 수동회원등록";
    $P->NaviTitle = "수동회원등록";
    $P->title = "수동회원등록";
    $P->strLeftMenu = member_menu();
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}

?>
