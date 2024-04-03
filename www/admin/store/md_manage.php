<?
include("../class/layout.class");
include_once("md.lib.php");
include("../basic/company.lib.php");
//print_r($admininfo);

if(!$admininfo[company_id]){
	echo("<script>alert('업체가 선택되지 않았습니다 확인후 다시 시도해주세요');history.back();</script>");
	exit;
}

$cdb = new Database;
$db = new Database;


if($admininfo[admin_level] == 9){

			if($db->dbms_type == "oracle"){
				$sql = "SELECT 
						cmd.code,
						AES_DECRYPT(name,'".$db->ase_encrypt_key."') as name,
						AES_DECRYPT(mail,'".$db->ase_encrypt_key."') as mail,
						AES_DECRYPT(zip,'".$db->ase_encrypt_key."') as zip,
						AES_DECRYPT(addr1,'".$db->ase_encrypt_key."') as addr1,
						AES_DECRYPT(addr2,'".$db->ase_encrypt_key."') as addr2,
						AES_DECRYPT(tel,'".$db->ase_encrypt_key."') as tel,
						AES_DECRYPT(pcs,'".$db->ase_encrypt_key."') as pcs,
						birthday,
						birthday_div,
						tel_div,info,sms,nick_name,job,cmd.date_ as regdate2,cmd.file_,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,black_list,
						add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*  , ccd.*
						FROM  ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_USER." cu
						left join ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
					where cu.code = cmd.code  and cu.code = '$code'
					ORDER BY cu.date_ DESC";
			}else{
				$sql = "SELECT 
						cmd.code,
						AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
						AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail,
						AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,
						AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
						AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,
						AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,
						AES_DECRYPT(UNHEX(com_tel),'".$db->ase_encrypt_key."') as com_tel,
						AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
						cmd.birthday,
						cmd.birthday_div,
						cmd.date as regdate2,
						cmd.file,
						cmd.recent_order_date,
						cmd.recom_id,
						cmd.gp_ix,
						cmd.sex_div,
						cmd.mem_level,
						cmd.branch,
						cmd.team,
						cmd.department,
						cmd.position,
						cmd.black_list,
						cmd.mem_code,
						cmd.join_date,
						cmd.nationality,
						cmd.married,
						cmd.duty,
						cmd.position,
						cmd.com_group,
						cmd.department,
						cmd.interest,
						cmd.specialty,
						cmd.r_zipcode,
						cmd.r_addr1,
						cmd.r_addr2,
						cmd.work_devision,
						cmd.join_devision,
						cmd.bank_name,
						cmd.holder_name,
						cmd.bank_num,
						cmd.resign_msg,
						cmd.resign_date,
						cmd.worker_message,
						cu.*,
						cr.relation_code
						FROM  
						".TBL_COMMON_MEMBER_DETAIL." as cmd  
						inner join ".TBL_COMMON_USER." as cu on (cmd.code = cu.code)
						inner join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
						inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (ccd.company_id = cr.company_id)
					where 
						cu.code = '$code'
						ORDER BY cu.date DESC";
			}

			$db->query($sql);
			$db->fetch();

			$cid2 = $db->dt[relation_code];
			$group_ix = $db->dt[com_group];	//부서그룹
			$dp_ix = $db->dt[department];	//부서
			$cu_ix = $db->dt[duty];	//직책
			$ps_ix = $db->dt[position];	//직위
			$cid2 = $db->dt[relation_code];

			$resign_date = explode(" ",$db->dt[resign_date]);
			$tel= explode("-",$db->dt[tel]);
			$pcs= explode("-",$db->dt[pcs]);
			$com_tel= explode("-",$db->dt[com_tel]);
			$company_id = $db->dt[company_id];
			$join_date = explode(" ", $db->dt[join_date]);

			if($code){	
				$act = "update";
			}else{
				$act = "insert";
			}

		
	
}else if($admininfo[admin_level] == 8){
		$company_id = $admininfo[company_id];

		if($info_type == "basic"){
			$sql = "SELECT * FROM common_user cu , common_company_detail ccd where  cu.company_id = ccd.company_id and ccd.company_id = '".$company_id."'";
		}else if($info_type == "seller_info"){
			$sql = "SELECT * FROM common_company_detail csd where csd.company_id = '".$company_id."'";
		}else if($info_type == "delivery_info"){
			$sql = "SELECT * FROM common_company_detail csd where csd.company_id = '".$company_id."'";
		}

		$db->query($sql);
		$db->fetch();
		$act = "update";

}



$Contents .= "<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	  <col width=25%>
	  <col width=*>
	  <col width=10%>
	  <col width=20%>
	  <tr>
	     <td align='left' colspan=4> ".GetTitleNavigation("MD 수정/등록", "상점관리 > MD 수정/등록 ")."</td>
	  </tr>
	  <tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'>
			".md_tab("md")."
		</td>
	  </tr>
	  </table>";

$Contents .= "
<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>";
 $Contents .= "
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
	<tr>
		<td>
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='1' border='0' >
			<tr>
				<td width='15%'>
					<table  style='width:100%; height:100%' cellpadding='0' cellspacing='0' border='0'>
						<tr>
							<td>
								<table style='width:100%;' cellpadding='5' cellspacing='1' border='0' bgcolor='#BDBDBD'>
								<tr>
									<td width='100%' height='190' bgcolor='#ffffff' align='center'>";
							
									if(is_file($path."/member_".$company_id.".gif")){
									
									$Contents .= "<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/member_".$company_id.".gif' width=170 height=200>&nbsp;&nbsp;&nbsp;";
									}else{
									$Contents .= "<img src='http://dev.forbiz.co.kr/admin/images/noimage_152_148.gif' border='0'>";
									}
 $Contents .= "
										
									</td>
								</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td height='5'></td></tr>
						<tr>
							<td align='left'>
							<input type='file' name='fine_file' value='찾아보기'>
							<!--<input type='button' name='delete_file' value='삭제'>-->
							</td>
						</tr>
					</table>
				</td>
				<td width='1%'></td>
				<td width='60%' valign='top'>
					<table  style='width:100%;' height='100%' cellspacing='0' border='0''>
						<tr>
							<td>
								<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
								  <colgroup>
									<col width='20%' />
									<col width='30%' style='padding:0px 0px 0px 10px'/>
								  </colgroup>
								 <tr>
									<td class='input_box_title'> <b>사원코드 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
									<input type=text name='mem_code' value='".$db->dt[mem_code]."' class='textbox'  style='width:200px' validation='true' title='본사코드'>
									</td>
								  </tr>
								  <tr>
									<td class='input_box_title'> <b>입사일</b> <img src='".$required3_path."'></td>
									<td class='input_box_item'>
									<input type=text  id='join_date' name='join_date' value='".$join_date[0]."' class='textbox'  style='width:200px' validation='true' title='입사일'></td>
								  </tr>
									
								  <tr>
									<td class='input_box_title'> <b>외국인여부 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
										<select name='nationality'>
											<option value='I' ".($db->dt[nationality] == "I" ? "selected":"").">내국인</option>
											<option value='O' ".($db->dt[nationality] == "O" ? "selected":"").">외국인</option>
											<option value='D' ".($db->dt[nationality] == "D" ? "selected":"").">기타</option>
										</select>
									</td>
								  </tr>
								  <tr>
									<td class='input_box_title'> <b>이름 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
									<input type=text name='mem_name' value='".$db->dt[name]."' class='textbox'  style='width:200px' validation='true' title='이름'>
									</td>
								  </tr>
								   <tr>
									<td class='input_box_title'> <b>생년월일 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
									<input type='radio' name='birthday_div' id='birthday_div_0' value='0' ".($db->dt[birthday_div] == "0" || $db->dt[birthday_div] == "" ? "checked":"")."> <label for='birthday_div_0'>음력</label>&nbsp;&nbsp; 
									<input type='radio' name='birthday_div' id='birthday_div_1' value='1' ".($db->dt[birthday_div] == "1" ? "checked":"")."> <label for='birthday_div_1'>양력</label>&nbsp;&nbsp;
									<input type=text name='birthday' id='birthday' value='".$db->dt[birthday]."' class='textbox'  style='width:200px' validation='true' title='생년월일'>
									</td>
								  </tr>
								  <tr>
									<td class='input_box_title'> <b>기혼여부 <img src='".$required3_path."'></b></td>
									<td class='input_box_item'>
									<input type='radio' name='married' id='married_N' value='N' ".($db->dt[married] == "N" || $db->dt[married] == "" ? "checked":"")."> <label for='married_N'>미혼</label>&nbsp;&nbsp; 
									<input type='radio' name='married' id='married_Y' value='Y' ".($db->dt[married] == "Y" ? "checked":"")."> <label for='married_Y'>기혼</label>&nbsp;&nbsp;
									</td>
								  </tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table></td>
	</tr>
</table>";


$com_zip = explode("-",$db->dt[com_zip]);
$Contents .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />";

$Contents .= "
	  <tr>
		<td height='20'></td></tr>
	  <tr>
		<td>
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
		  <colgroup>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
		  </colgroup>
		  </tr>
			<tr bgcolor=#ffffff >
			<td class='input_box_title'><b>아이디 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<table border=0 cellpadding=0 cellspacing=0>
					<input type=hidden name='b_id' value='".$db->dt[id]."' >
					<tr>
						<td class='input_box_item'>
							<input type='text' class='textbox' id='user_id' name='id' validation='true' idtype=true duplicate=true  dup_check='".($act == "update" ? "true":"false")."' title='아이디'  value='".$db->dt[id]."'  style='width:200px;ime-mode:disabled;'>
						</td>
					</tr>
					<tr><td height='5'></td></tr>
					<tr>
						<td colspan='4'>
							<span id='idCheckText' style='color:#FF5A00;'>아이디는 4~16자리의 영문, 숫자와 특수기호(_)만 사용하실 수 있습니다.</span> 
						</td>
					</tr>
				</table>
			</td>
			<td class='input_box_title'><b>사용자 권한 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<table cellpadding=0 cellspacing=0>
					<tr>
						<td>".getAuthTemplet($db->dt[auth])."</td>
						<td colspan=2 style='padding-left:10px;;'><span class=small><!--* 사용자 권한에 맞는 권한 템플릿 선택--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </spna><br>
					
						</td>
					</tr>
					<tr>
						<td colspan=2 style='padding-top:5px;;'>
						<!--<input type='radio' name='mem_div' id='mem_div_S' value='S' ".($db->dt[mem_div] == "S" ? "checked":"")."> <label for='mem_div_S'>셀러회원</label> &nbsp;&nbsp;-->
						<input type='radio' name='mem_div' id='mem_div_MD' value='MD' ".($db->dt[mem_div] == "MD" ? "checked":"")." checked> <label for='mem_div_MD'>MD담당자</label> &nbsp;&nbsp;
						<!--<input type='radio' name='mem_div' id='mem_div_D' value='D' ".($db->dt[mem_div] == "D" || $db->dt[mem_div] == "" ? "checked":"")."> <label for='mem_div_D'>기타</label> &nbsp;&nbsp;-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor=#ffffff  >
			<td class='input_box_title'>패스워드  <img src='".$required3_path."'></td>
			<td class='input_box_item' nowrap>
				<input type=password name='pw' value='' size=20 style='width:200px' class='textbox' > ".($act == "update" ? "<input type=checkbox name=change_pass id=change_pass value=1 style='vertical-align:middle;'><label for='change_pass' style='vertical-align:middle;'> 비밀번호수정</label>":"")."
			</td>
			<td class='input_box_title'>패스워드 확인  <img src='".$required3_path."'></td>
			<td class='input_box_item' nowrap><input type=password name='pw_confirm' value='' size=20 class='textbox'  style='width:200px' ></td>
		</tr>

		<tr bgcolor=#ffffff >
			<td class='input_box_title'><b>사용자 언어 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' >
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td>	".getLanguage($db->dt[language]," validation=true title='사용자 언어' ")."</td>
					<td style='padding-left:10px;;'>
					  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."
					</td>
				</tr>
			</table>
			</td>
			<td class='input_box_title'><b>사용자 승인</b></td>
			<td class='input_box_item'>
				<select name='authorized' style='width:100px;font-size:12px;'>
				<option value='N' ".CompareReturnValue("N",$db->dt[authorized],"selected").">승인대기</option>
				<option value='Y'  ".CompareReturnValue("Y",$db->dt[authorized],"selected").">승인</option>
				<option value='X' ".CompareReturnValue("X",$db->dt[authorized],"selected").">승인거부</option>
				 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'>입점업체 로그인은 관리자 승인후에만 가능합니다 </span>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>근무사업장 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' colspan='3'>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td style='padding-right:5px;'>
						".getCompanyList("본사", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',3)\" title='선택' ", '5', $cid2,'member')."</td>
						<td style='padding-right:5px;'>
						".getCompanyList("선택", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',3)\" title='선택'", '15', $cid2,'member')."</td>
						<td style='padding-right:5px;'>
						".getCompanyList("선택", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',3)\" title='선택'", '25', $cid2,'member')."</td>
						<td>".getCompanyList("선택", "cid3_1", "onChange=\"loadCategory(this,'cid2',3)\" title='선택'", '35', $cid2,'member')."</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>부서설정 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' colspan='3'>
				<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding-right:5px;'>
					".getgroup1($group_ix,'','true')."</td>
					<td style='padding-right:5px;'>
					".getdepartment($dp_ix,'','true')."</td>
					<td style='padding-right:5px;'>
					".getposition($ps_ix,'','true')."</td>
					<td>".getduty($cu_ix,'','true')."</td>
				</tr>
			</table>
			</td>
		  </tr>
		  <!--
		  <tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>지역 / 지사 / 팀 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			 ".getRegionInfoSelect('parent_rg_ix', '1차 지역',$parent_rg_ix, $parent_rg_ix, 1, " onChange=\"loadRegion(this,'rg_ix')\" ")."
			 ".getRegionInfoSelect('rg_ix', '2차 지역',$parent_rg_ix, $rg_ix, 2, "validation=true title='지역' onChange=\"loadBranch(this,'branch')\" ")."
			 ".makeBranchSelectBox($cdb,'branch', $rg_ix, $branch, '지사', "validation=true title='지사' onChange=\"loadTeam(this,'team')\" ")."
			 ".makeTeamSelectBox($cdb,'team', $branch,$team,  '팀', "".($db->dt[mem_level] == "11" ? " style='display:none' validation=false":"validation=true")." title='팀'  ")."
			</td>
			<td class='input_box_title'> <b>MD 레벨  <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			  <input type='radio' name='mem_level' id='mem_level_11' value='11' validation=true title='지사장' ".($db->dt[mem_level] == "11" ? "checked":"")." onclick=\"$('#team').attr('validation','false');$('#team').hide();\"><label for='mem_level_11'>지사장</label> &nbsp;&nbsp;
			  <input type='radio' name='mem_level' id='mem_level_12' value='12' validation=true title='MD 팀장' ".($db->dt[mem_level] == "12" ? "checked":"")." onclick=\"$('#team').attr('validation','true');$('#team').show();\"><label for='mem_level_12'>MD 팀장</label>
			  <input type='radio' name='mem_level' id='mem_level_13' value='13' validation=true title='MD' ".($db->dt[mem_level] == "13" ? "checked":"")." onclick=\"$('#team').attr('validation','true');$('#team').show();\"><label for='mem_level_13'>MD</label>
			</td>
		  </tr>-->
		  <tr>
			<td class='input_box_title'> <b>재직구분 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' colspan='3'>
				<input type='radio' name='work_devision' id='work_devision_R' value='R' ".($db->dt[work_devision] == "R" || $db->dt[work_devision] == "" ? "checked":"")."> <label for='work_devision_R'>정사원</label> &nbsp;&nbsp;
				<input type='radio' name='work_devision' id='work_devision_I' value='I' ".($db->dt[work_devision] == "I" ? "checked":"")."> <label for='work_devision_I'>인턴사원</label> &nbsp;&nbsp;
				<input type='radio' name='work_devision' id='work_devision_C' value='C' ".($db->dt[work_devision] == "C" ? "checked":"")."> <label for='work_devision_C'>계약직</label> &nbsp;&nbsp;
				<input type='radio' name='work_devision' id='work_devision_D' value='D' ".($db->dt[work_devision] == "D" ? "checked":"")."> <label for='work_devision_D'>일용직</label> &nbsp;&nbsp;
				<input type='radio' name='work_devision' id='work_devision_S' value='S' ".($db->dt[work_devision] == "S" ? "checked":"")."> <label for='work_devision_S'>용역</label> &nbsp;&nbsp;
				<input type='radio' name='work_devision' id='work_devision_O' value='O' ".($db->dt[work_devision] == "O" ? "checked":"")."> <label for='work_devision_O'>퇴사사원</label> &nbsp;&nbsp;
			</td>
		  </tr>
		  <tr>
			<td class='input_box_title'><b>전화번호 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text name='tel_1' id='tel_1' value='".$tel[0]."' maxlength=4 size=4  class='textbox' style='width:45px'  com_numeric=true validation='true' title='전화'> -
				<input type=text name='tel_2' id='tel_2' value='".$tel[1]."' maxlength=4 size=4 class='textbox' style='width:45px'  com_numeric=true validation='true' title='전화'> -
				<input type=text name='tel_3' id='tel_3' value='".$tel[2]."' maxlength=4 size=4 class='textbox' style='width:45px'  com_numeric=true validation='true' title='전화'>
			</td>
			<td class='input_box_title'><b>핸드폰 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text name='pcs_1' id='pcs_1' value='".$pcs[0]."' maxlength=4 size=4  class='textbox' style='width:45px' com_numeric=true validation='true' title='핸드폰'> -
				<input type=text name='pcs_2' id='pcs_2' value='".$pcs[1]."' maxlength=4 size=4 class='textbox' style='width:45px' com_numeric=true validation='true' title='핸드폰'> -
				<input type=text name='pcs_3' id='pcs_3' value='".$pcs[2]."' maxlength=4 size=4 class='textbox' style='width:45px' com_numeric=true validation='true' title='핸드폰'>
			</td>
		  </tr>
		  <tr>
			<td class='input_box_title'><b>회사전화번호 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' colspan='3'>
				<input type=text name='com_tel_1' id='com_tel_1' value='".$com_tel[0]."' maxlength=4 size=4  class='textbox' style='width:45px'  com_numeric=true validation='true' title='전화'> -
				<input type=text name='com_tel_2' id='com_tel_2' value='".$com_tel[1]."' maxlength=4 size=4 class='textbox' style='width:45px'  com_numeric=true validation='true' title='전화'> -
				<input type=text name='com_tel_3' id='com_tel_3' value='".$com_tel[2]."' maxlength=4 size=4 class='textbox' style='width:45px'  com_numeric=true validation='true' title='전화'>
				&nbsp;&nbsp;&nbsp;
				내선번호 
				<input type=text name='com_tel_4' id='com_tel_4' value='".$com_tel[3]."' maxlength=4 size=4 class='textbox' style='width:45px'  com_numeric=true validation='false' title='전화'>
			</td>
		  </tr>
		  <tr>
			<td class='input_box_title'><b>이메일 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text name='mail' style='width:200px' value='".$db->dt[mail]."' class='textbox'  style='width:80px' validation='true' title='이메일' email=true>
			</td>
			<td class='input_box_title'><b>입사구분</b></td>
			<td class='input_box_item' >
				<select name='join_devision' style='width:100px;font-size:12px;'>
				<option value='1'>신입</option>
				<option value='2'>경력</option>
			</select>
			</td>
		  </tr>
		
		  <tr>
			<td class='input_box_title'> <b>취미</b>   </td>
			<td class='input_box_item'><input type=text name='interest' value='".$db->dt[interest]."' class='textbox'  style='width:200px' validation='false' title='취미'></td>
			 <td class='input_box_title' > <b>특기</b>   </td>
			<td class='input_box_item'><input type=text name='specialty' value='".$db->dt[specialty]."' class='textbox'  style='width:200px' validation='false' title='특기'></td>
		  </tr>

		  <tr>
			<td class='input_box_title'> <b>주소 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' colspan=3>
				<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='addAddress(this)'><label for='change_address'>주소변경</label><br>-->
				<div id='input_address_area' ><!--style='display:none;'-->
				<table border='0' cellpadding='0' cellspacing='5' style='table-layout:fixed;width:100%'>
					<col width='120px'>
					<col width='*'>
					<tr>
						<td height=26>
							<input type='text' validation='true' class='textbox' name='zip' id='zip' size='15' maxlength='15' value='".$db->dt[zip]."' readonly title='우편코드'>
						</td>
						<td style='padding:1px 0 0 5px;'>
							<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"md_zipcode('1');\" style='cursor:pointer;'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='addr1'  id='addr1' value='".$db->dt[addr1]."' size=50 class='textbox'  style='width:75%' validation='true' title='주소1'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='addr2'  id='addr2'  value='".$db->dt[addr2]."' size=70 class='textbox'  style='width:450px' title='주소2'> (상세주소)
						</td>
					</tr>
					</table>
				</div>
				</td>
		  </tr>
		  <tr>
			<td class='input_box_title'> <b>실거주지 주소 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' colspan=3>
				<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
				<div id='input_address_area' ><!--style='display:none;'-->
				<table border='0' cellpadding='0' cellspacing='5' style='table-layout:fixed;width:100%'>
					<col width='120px'>
					<col width='*'>
					<tr>
						<td height=26>
							<input type='text' validation='true' class='textbox' name='r_zipcode' id='r_zipcode' size='15' maxlength='15' value='".$db->dt[r_zipcode]."' readonly  title='실거주지 우편번호'>
						</td>
						<td style='padding:1px 0 0 5px;'>
							<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"md_zipcode('4');\" style='cursor:pointer;'>
							&nbsp;<input type='checkbox' name='change_address' id='change_address' onclick='addAddress(this)'><label for='change_address'> 상위주소 동일 적용</label><br>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='r_addr1'  id='r_addr1' value='".$db->dt[r_addr1]."' size=50 class='textbox'  style='width:75%' validation='true'  title='실거주지 주소1'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='r_addr2'  id='r_addr2'  value='".$db->dt[r_addr2]."' size=70 class='textbox'  style='width:450px'  title='실거주지 주소2'> (상세주소)
						</td>
					</tr>
					</table>
				</div>
				</td>
		  </tr>

		  <tr>
			<td class='input_box_title'> <b>급여계좌</b></td>
			<td class='input_box_item' colspan='3'>
			<select name='bank_name' style='width:150px;'>
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
				예금주&nbsp; <input type=text class='textbox' name='holder_name' value='".$db->dt[holder_name]."'  style='width:100px' validation='false' title='예금주' >&nbsp;&nbsp;&nbsp;
				계좌번호&nbsp; <input type=text class='textbox' name='bank_num' value='".$db->dt[bank_num]."'  style='width:200px' validation='false' title='계좌번호' >
			</td>
		  </tr>
		  <tr>
			<td class='input_box_title'> <b>퇴직일</b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' id='resign_date' name='resign_date' value='".$resign_date[0]."'  style='width:200px' validation='false' title='퇴직일'>
			</td>
			<td class='input_box_title'> <b>퇴직사유</b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='resign_msg' value='".$db->dt[resign_msg]."'  style='width:200px'>
			</td>
		  </tr>
		  <tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>기타사항</b></td>
			<td class='input_box_item' colspan=3><textarea type=text class='textbox' name='worker_message' value='".$db->dt[worker_message]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[worker_message]."</textarea></td>
		  </tr>
		  <tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>변경/수정일자</b></td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='edit_data' value='".$db->dt[edit_data]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[edit_data]."</textarea>
			</td>
		  </tr>


		</table>";
$Contents .= "</td>
			</tr>
</table>";



if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
    $ButtonString = "
        <table cellpadding=0 cellspacing=0 border='0' >
        <tr bgcolor=#ffffff >
		<td align=center>
		<a href='javascript:bak_page();'>
		<img src='../images/".$admininfo["language"]."/btn_prevpage.gif' border=0 style='cursor:pointer;border:0px;' ></a></td>
		<td style='padding-left:10px;' align=center>
		<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0  style='cursor:pointer;border:0px;' ></td>
		</tr>
    </table>
    ";
}


$Script .= "<script language='javascript' src='../basic/company.add.js'></script>
<script language='JavaScript' src='../webedit/webedit.js'></script>
<script language='javascript' src='../include/DateSelect.js'></script>\n
<Script Language='JavaScript' src='/admin/js/autocomplete.js'></Script>\n
<script Language='JavaScript' src='../include/zoom.js'></script>\n

<script language='javascript'>

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

function md_zipcode(type) {
	var zip = window.open('../basic/zipcode.php?type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function addAddress(obj){

	var edit_form=document.edit_form;

	if(obj.checked){
		$('#r_zipcode').val(edit_form.zip.value);
		$('#r_addr1').val(edit_form.addr1.value);
		$('#r_addr2').val(edit_form.addr2.value);

	}else{
		$('#r_zipcode').val('');
		$('#r_addr1').val('');
		$('#r_addr2').val('');
	}
}

$(function() {
	var edit_form=document.edit_form;
	$('#user_id').keyup(function(){

		//alert('111');
		var PT_idtype =/^[a-zA-Z]{1}[a-zA-Z0-9_]+$/;
		//var PT_idtype =/^[a-z0-9_-]{4,12}$/;

		if(edit_form.user_id.value.length < 4 || edit_form.user_id.value.length > 16 ){

			var alert_text='* 아이디는 4~16자리의 영문, 숫자와 특수기호(_)만 사용하실 수 있습니다.';//개정법안 수정 kbk 13/03/12
			$('#idCheckText').css('color','#FF5A00').html(alert_text);//개정법안 수정 kbk 13/03/12
			edit_form.user_id.focus();
			return false;
		}
		if(edit_form.id.value != ''){
			if(!PT_idtype.test(edit_form.id.value)){
				
				var alert_text='* 아이디는 4~16자리의 영문, 숫자와 특수기호(_)만 사용하실 수 있습니다.';//개정법안 수정 kbk 13/03/12
				
				$('#idCheckText').css('color','#FF5A00').html(alert_text);//개정법안 수정 kbk 13/03/12
				edit_form.user_id.focus();
				return false;
			}
			$.ajax({
				url: '../basic/join_input.act.php',
				type: 'get',
				dataType: 'html',
				data: ({act: 'idcheck',
						id: $('#user_id').val()
				}),
				success: function(result){
				//alert(result);
					//alert(edit_form.id.value);
					if(result == 'Y'){
						var alert_text='* 사용가능한 아이디 입니다.';//개정법안 수정 kbk 13/03/12
						$('#idCheckText').css('color','#00B050').html(alert_text);//개정법안 수정 kbk 13/03/12
						//$('#id_flag').val('Y');
					//	$('#id_check_value').val(edit_form.id.value);//kbk
						$('#user_id').attr('dup_check','true');//kbk
					}else if(result == 'X'){
						var alert_text='* 가입불가 ID입니다. 다른 ID로 입력해주시기 바랍니다.';//개정법안 수정 kbk 13/03/12
						$('#idCheckText').css('color','#FF5A00').html(alert_text);//개정법안 수정 kbk 13/03/12
					//	$('#id_flag').val('');
					//	$('#id_check_value').val('');//kbk
						$('#user_id').attr('dup_check','false');//kbk
						return false;
					}else if(result=='N'){
						var alert_text='* 사용할수 없는 아이디 입니다.';//개정법안 수정 kbk 13/03/12
						$('#idCheckText').css('color','#FF5A00').html(alert_text);//개정법안 수정 kbk 13/03/12
					//	$('#id_flag').val('');
					//	$('#id_check_value').val('');//kbk
						$('#user_id').attr('dup_check','false');//kbk
						return false;
					} else {
						//alert(result);
						return false;
					}
				}

			});
		}else{
			edit_form.user_id.focus();
			var alert_text='* 아이디가 비어있습니다.';//개정법안 수정 kbk 13/03/12
			$('#idCheckText').html(alert_text);//개정법안 수정 kbk 13/03/12
			return false;
		}
	});
}
);
</script>

";

$Contents01 = "<form name='edit_form' action='../basic/member.act.php?url=store' method='post' onsubmit='return SubmitX(this)' enctype='multipart/form-data'  target='act'>";
$Contents01 = $Contents01."<table width='100%' border=0>";
$Contents01 = $Contents01."
<input name='act' type='hidden' value='$act'>
<input name='mmode' type='hidden' value='$mmode'>
<input type='hidden' name='cid2' value='$cid2'>
<input type='hidden' name='depth' value='$depth'>
<input type='hidden' name='code' value='$code'>
<input name='info_type' type='hidden' value='basic'>
<input name='company_id' type='hidden' value='".$company_id."'>";
//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents01 = $Contents01."<tr><td>".$Contents."</td></tr>";
$Contents01 = $Contents01."<tr><td>".$Contents04."</td></tr>";
$Contents01 = $Contents01."<tr><td align=center>".$ButtonString."</td></tr>";
$Contents01 = $Contents01."</table></form><br><br>";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<b>MD 설정</b> ", $help_text,105)."</div>";

$Contents = $Contents.$help_text;

$Script .= "
<script language='javascript'>
function loadRegion(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;
	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = 'region.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadBranch(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;
	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = 'branch.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadTeam(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;
	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = 'team.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function CheckFormUserValue(frm){

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	if(frm.act.value == 'user_insert' || (frm.act.value == 'user_update' && frm.pw.checked)){

		if(frm.pw.value.length < 1){
			alert('비밀번호가 입력되지 않았습니다. ');
			frm.pw.focus();
			return false;
		}

		if(frm.pw_confirm.value.length < 1){
				alert('비밀번호가 확인 정보가 입력되지 않았습니다. ');
			frm.pw_confirm.focus();
			return false;
		}

		if(frm.pw.value != frm.pw_confirm.value){
			alert('비밀번호가 정확하지 않습니다 확인후 다시 입력해주세요');
			return false;
		}
	}
	return true;
}

function updateUserInfo(company_id, code)
{
	document.location.href = '?company_id='+company_id+'&code='+code+'&#user_add'
	//document.frames['act'].location.href='company.act.php?act=admin_log&company_id='+company_id+'&code='+code+'&#user_add';
}

function deleteUserInfo(company_id, code){

	if(confirm('사용자 정보를 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='company.act.php?act=user_delete&company_id='+company_id+'&code='+code
	}
}

$(document).ready(function(){
	$('#join_date').datepicker({
    dateFormat: 'yy-mm-dd',
    buttonImageOnly: true,
    buttonText: 'Kalender',
	});
	
	$('#resign_date').datepicker({
    dateFormat: 'yy-mm-dd',
    buttonImageOnly: true,
    buttonText: 'Kalender',
	});

	$('#birthday').datepicker({
    dateFormat: 'yy-mm-dd',
    buttonImageOnly: true,
    buttonText: 'Kalender',
	});


});

</script>";
if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='origin.js'></script>";
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "상점관리 > MD 관리 > MD 등록/수정";
	$P->NaviTitle = "MD목록";
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents01;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents01;
	$P->Navigation = "상점관리 > MD 관리 > MD 등록/수정";
	$P->title = "MD 등록/수정";
	echo $P->PrintLayOut();
}


?>