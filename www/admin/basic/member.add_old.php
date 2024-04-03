<?
include("../class/layout.class");
include("../basic/company.lib.php");
include_once("../store/md.lib.php");
//auth(9);

$db = new Database;
$mdb = new Database;



		// 임의의 문자열 생성 ( 특수문자 포함 )
		function wel_passwordGenerator( $length=12 ){

			$counter = ceil($length/4);
			// 0보다 작으면 안된다.
			$counter = $counter > 0 ? $counter : 1;            

			$charList = array( 
							array("0", "1", "2", "3", "4", "5","6", "7", "8", "9", "0"),
							array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z")
							//,array("!", "@", "#", "%", "^", "&", "*") 
						);
			$password = "";
			for($i = 0; $i < $counter; $i++)
			{
				$strArr = array();
				for($j = 0; $j < count($charList); $j++)
				{
					$list = $charList[$j];

					$char = $list[array_rand($list)];
					$pattern = '/^[a-z]$/';
					// a-z 일 경우에는 새로운 문자를 하나 선택 후 배열에 넣는다.
					if( preg_match($pattern, $char) ) array_push($strArr, strtoupper($list[array_rand($list)]));
					array_push($strArr, $char);
				} 
				// 배열의 순서를 바꿔준다.
				shuffle( $strArr );

				// password에 붙인다.
				for($j = 0; $j < count($strArr); $j++) $password .= $strArr[$j];
			}
			// 길이 조정
			return substr($password, 0, $length);
		}



$menu_name = "사원등록";

if($info_type == ""){
	$info_type = "basic";
}

if($code){	
	$act = "update";
}else{
	$act = "insert";
}

if($admininfo[admin_level] == 8){
	$where = " and ccd.company_id = '".$admininfo['company_id']."'";
}
if($info_type == "basic"){

	if($db->dbms_type == "oracle"){
		$sql = "SELECT 
				cmd.*,
				AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail,
				AES_DECRYPT(cmd.zip,'".$db->ase_encrypt_key."') as zip,
				AES_DECRYPT(cmd.addr1,'".$db->ase_encrypt_key."') as addr1,
				AES_DECRYPT(cmd.addr2,'".$db->ase_encrypt_key."') as addr2,
				AES_DECRYPT(cmd.tel,'".$db->ase_encrypt_key."') as tel,
				AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs,
				cu.*,
				ccd.*
				FROM  ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_USER." cu
				left join ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
			where
				cu.code = cmd.code
				and cu.code = '$code'
				$where
				ORDER BY cu.date_ DESC";
	}else{
		$sql = "SELECT
				cmd.*,
				AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail,
				AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,
				AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
				AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,
				AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,
				AES_DECRYPT(UNHEX(com_tel),'".$db->ase_encrypt_key."') as com_tel,
				AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
				cu.*,
				cr.relation_code
				FROM  
				".TBL_COMMON_MEMBER_DETAIL." as cmd  
				inner join ".TBL_COMMON_USER." as cu on (cmd.code = cu.code)
				inner join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
				inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (ccd.company_id = cr.company_id)
			where 
				cu.code = '$code'
				$where
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
	$cti_num  = $db->dt[cti_num];

}else if($info_type == "f_info"){

	$sql = "SELECT 
				cmd.*,				
				AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
				cu.*,
				cr.relation_code
				FROM  
				".TBL_COMMON_MEMBER_DETAIL." as cmd  
				inner join ".TBL_COMMON_USER." as cu on (cmd.code = cu.code)
				inner join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
				inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (ccd.company_id = cr.company_id)
			where 
				cu.code = '$code'
				$where
				ORDER BY cu.date DESC";
	
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
	$company_id = $db->dt[company_id];
	$join_date = explode(" ", $db->dt[join_date]);
	
	$f_sql = "
		select
			*
		from
			common_worker_family
		where
			code = '".$code."'
			order by family_ix asc";
	$db->query($f_sql);
	$f_array = $db->fetchall();

}else if($info_type == "s_info"){
	$sql = "SELECT 
				cmd.*,
				AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
				cr.relation_code
			FROM
				".TBL_COMMON_MEMBER_DETAIL." as cmd  
				inner join ".TBL_COMMON_USER." as cu on (cmd.code = cu.code)
				inner join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
				inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (ccd.company_id = cr.company_id)
			where 
				cu.code = '$code'
				$where
				ORDER BY cu.date DESC";

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
	$company_id = $db->dt[company_id];
	$join_date = explode(" ", $db->dt[join_date]);
	
	$s_sql = "
		select
			*
		from
			common_worker_school
		where
			code = '".$code."'
			order by school_ix asc
	";
	
	$db->query($s_sql);
	$s_array = $db->fetchall();
	
	$r_sql = "
		select
			*
		from
			common_worker_resume
		where
			code = '".$code."'
			order by resume_ix asc
	";
	
	$db->query($r_sql);
	$r_array = $db->fetchall();

}else if($info_type == "j_info"){
	$sql = "SELECT 
				cmd.*,
				AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
				cu.*,
				cr.relation_code
			FROM 
				".TBL_COMMON_MEMBER_DETAIL." as cmd  
				inner join ".TBL_COMMON_USER." as cu on (cmd.code = cu.code)
				inner join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
				inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (ccd.company_id = cr.company_id)
			where 
				cu.code = '$code'
				$where
				ORDER BY cu.date DESC";
	
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
	$company_id = $db->dt[company_id];
	$join_date = explode(" ", $db->dt[join_date]);

	$j_sql = "
		select
			*
		from
			common_worker_project
		where
			code = '".$code."'
			order by project_ix asc
	";
	
	$db->query($j_sql);
	$p_array = $db->fetchall();

}else if($info_type == "z_info"){
	$sql = "SELECT 
				cmd.*,
				AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
				cu.*,
				cr.relation_code
				FROM  
				".TBL_COMMON_MEMBER_DETAIL." as cmd  
				inner join ".TBL_COMMON_USER." as cu on (cmd.code = cu.code)
				inner join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
				inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (ccd.company_id = cr.company_id)
			where 
				cu.code = '$code'
				$where
				ORDER BY cu.date DESC";
	
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
	$company_id = $db->dt[company_id];
	$join_date = explode(" ", $db->dt[join_date]);
	$z_sql = "
		select
			*
		from
			common_worker_certificate
		where
			code = '".$code."'
			order by certificate_ix asc
	";
	
	$db->query($z_sql);
	$z_array = $db->fetchall();
}




$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;

$Script = "
<script language='javascript'>
function deleteMemberInfo(act, code){
	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
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

if($page_type != 'store'){
$Contents = "

<input type='hidden' id='wel_password' value='".wel_passwordGenerator(15)."'>

<!--<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>-->
<table width='100%' border='0'  cellpadding=0 cellspacing=0 align='center'>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("전체사원리스트", "기초정보관리 > 본사관리 ")."</td>
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
						<td class='box_02'  ><a href='?info_type=basic&code=".$code."&mmode=$mmode'>사원정보</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "f_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($code == ""){
							$Contents .= "<a href=\"javascript:alert('사원정보를 먼저 입력하십시오.');\">가족사항</a>";
						}else{
							$Contents .= "<a href='?info_type=f_info&code=".$code."&mmode=$mmode'>가족사항</a>";
						}
						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($info_type == "s_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($code == ""){
							$Contents .= "<a href=\"javascript:alert('사원정보를 먼저 입력하십시오.');\">학력/이력</a>";
						}else{
							$Contents .= "<a href='?info_type=s_info&code=".$code."&mmode=$mmode'>학력/이력</a>";
						}
						$Contents .= "

						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_04' ".($info_type == "j_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($code == ""){
							$Contents .= "<a href=\"javascript:alert('사원정보를 먼저 입력하십시오.');\">경력사항</a>";
						}else{
							$Contents .= "<a href='?info_type=j_info&code=".$code."&mmode=$mmode'>경력사항</a>";
						}
						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_05' ".($info_type == "z_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($code == ""){
							$Contents .= "<a href=\"javascript:alert('사원정보를 먼저 입력하십시오.');\">자격/면허</a>";
						}else{
							$Contents .= "<a href='?info_type=z_info&code=".$code."&mmode=$mmode'>자격/면허</a>";
						}
						$Contents .= "

						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'></td>
			</tr>
		</table>
		</div>
	</td>
</tr>
</table>";

}


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
							
									if(is_file($path."/member_".$code.".gif")){
										$Contents .= "<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/member_".$code.".gif' width=170 height=200>&nbsp;&nbsp;&nbsp;";
									}else{
										$Contents .= "<img src='http://dev.forbiz.co.kr/admin/images/noimage_152_148.gif' border='0'>";
									}

		 $Contents .= "				</td>
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
								<input type=text name='mem_code' value='".$db->dt[mem_code]."' class='textbox'  style='width:100px' validation='true' title='사원코드'>
								</td>
							</tr>

							<tr>
								<td class='input_box_title'> <b>입사일</b> <img src='".$required3_path."'></td>
								<td class='input_box_item'>
								<input type=text  id='join_date' name='join_date' value='".$join_date[0]."' class='textbox' style='width:100px' validation='true' title='입사일'></td>
							</tr>
								
							<tr>
								<td class='input_box_title'> <b>외국인여부 <img src='".$required3_path."'></b></td>
								<td class='input_box_item'>
									<select name='nationality'>
										<option value='I' ".($db->dt[nationality] == "I" ? "selected":"").">내국인</option>
										<option value='O' ".($db->dt[nationality] == "O" ? "selected":"").">외국인</option>
									</select>
								</td>
							</tr>
							
							<tr>
								<td class='input_box_title'> <b>이름 <img src='".$required3_path."'></b></td>
								<td class='input_box_item'>
								<input type=text name='mem_name' value='".$db->dt[name]."' class='textbox'  style='width:100px' validation='true' title='이름'>
								</td>
							</tr>
							<tr>
								<td class='input_box_title'> <b>생년월일 </b></td>
								<td class='input_box_item'>
								<input type='radio' name='birthday_div' id='birthday_div_0' value='0' ".($db->dt[birthday_div] == "0"? "checked":"")."> <label for='birthday_div_0'>음력</label>&nbsp;&nbsp; 
								<input type='radio' name='birthday_div' id='birthday_div_1' value='1' ".($db->dt[birthday_div] == "1" || $db->dt[birthday_div] == "" ? "checked":"")."> <label for='birthday_div_1'>양력</label>&nbsp;&nbsp;
								<input type=text name='birthday' id='birthday' value='".$db->dt[birthday]."' class='textbox'  style='width:100px' validation='false' title='생년월일'>
								</td>
							</tr>
							<tr>
								<td class='input_box_title'> <b>기혼여부 </b></td>
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
</table><br>";

if($info_type == "basic" || $info_type == ""){

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
			<td class='input_box_item' style>
				<div style='padding-top:3px;'>
					<input type=hidden name='b_id' value='".$db->dt[id]."' >
					<input type='text' class='textbox' id='user_id' name='id' validation='true' idtype=true duplicate=true  dup_check='".($act == "update" ? "true":"false")."' title='아이디'  value='".$db->dt[id]."'  style='width:100px;ime-mode:disabled;'>
				</div>
				<div style='padding-top:3px;'>
					<span id='idCheckText' style='color:#FF5A00;'>아이디는 4~16자리의 영문, 숫자와 특수기호(_)만 사용하실 수 있습니다.</span>
				</div>
			</td>
			<td class='input_box_title'><b>사용자 권한 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				".getAuthTemplet($db->dt[auth])."
				<input type='radio' name='mem_div' id='mem_div_MD' value='MD' ".($db->dt[mem_div] == "MD" ? "checked":"")."> <label for='mem_div_MD'>MD담당자</label> &nbsp;
				<input type='radio' name='mem_div' id='mem_div_D' value='D' ".($db->dt[mem_div] == "D" ? "checked":"")."> <label for='mem_div_D'>기타</label>
			</td>
		</tr>
		<tr bgcolor=#ffffff>
			<td class='input_box_title'>비밀번호  <img src='".$required3_path."'></td>
			<td class='input_box_item' nowrap>
				<input type=password name='pw' value='' style='width:100px;background-color:#eee;' class='textbox' title='비밀번호' ".($act == "insert"?"validation=true":'')." readonly> ".($act == "update" ? "<input type=checkbox name=change_pass id=change_pass value=1 style='vertical-align:middle;'><label for='change_pass' style='vertical-align:middle;'> 비밀번호수정</label><br> <span id='idCheckText' style='color:#FF5A00;'>비밀번호수정 체크시 임시패스워드가 이메일로 발송됩니다.</span>":"<br><span id='idCheckText' style='color:#FF5A00;'>기입하신 이메일로 임시패스워드가 발송됩니다.</span>")."
			</td>
			<td class='input_box_title'>비밀번호 확인  <img src='".$required3_path."'></td>
			<td class='input_box_item' nowrap><input type=password name='pw_confirm' value='' class='textbox' title='비밀번호 확인'  style='width:100px;background-color:#eee;' ".($act == "insert"?"validation=true":'')." readonly></td>
		</tr>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'><b>사용자 언어 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' >
				".getLanguage($db->dt[language]," validation=true title='사용자 언어' ")."
			</td>
			<td class='input_box_title'><b>사용자 승인</b></td>
			<td class='input_box_item'>
				<select name='authorized' style='width:100px;font-size:12px;'>
					<option value='N' ".CompareReturnValue("N",$db->dt[authorized],"selected").">승인대기</option>
					<option value='Y' ".CompareReturnValue("Y",$db->dt[authorized],"selected").">승인</option>
					<option value='X' ".CompareReturnValue("X",$db->dt[authorized],"selected").">승인거부</option>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'>입점업체 로그인은 관리자 승인후에만 가능합니다 </span>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>근무사업장 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' colspan='3'>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td style='padding-right:5px;'>
						".getCompanyList("본사", "cid0_1", "onChange=\"loadCategory('cid0_1','cid1_1','member')\" title='사업장선택' ", '5', $cid2,'member')."</td>
						<td style='padding-right:5px;'>
						".getCompanyList("선택", "cid1_1", "onChange=\"loadCategory('cid1_1','cid2_1','member')\" title='선택'", '15', $cid2,'member')."</td>
						<td style='padding-right:5px;'>
						".getCompanyList("선택", "cid2_1", "onChange=\"loadCategory('cid2_1','cid3_1','member')\" title='선택'", '25', $cid2,'member')."</td>
						<td>".getCompanyList("선택", "cid3_1", "onChange=\"loadCategory('cid3_1','','member')\" title='선택'", '35', $cid2,'member')."</td>
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
					".getgroup1($group_ix, "onChange=\"loadDepartment('com_group','department')\" title='본부선택' ",'true')."</td>
					<td style='padding-right:5px;'>
					".getdepartment($dp_ix,"title='부서선택'",'true')."</td>
					<td style='padding-right:5px;'>
					".getposition($ps_ix,"title='직위선택'",'true')."</td>
					<td>".getduty($cu_ix,"title='직책선택'",'true')."</td>
				</tr>
			</table>
			</td>
		</tr>
		<!--
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>지역/지사/팀 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan='3'>
				".getRegionInfoSelect('parent_rg_ix', '1차 지역',$parent_rg_ix, $parent_rg_ix, 1, " onChange=\"loadRegion(this,'rg_ix')\" ")."
				".getRegionInfoSelect('rg_ix', '2차 지역',$parent_rg_ix, $rg_ix, 2, "validation=false title='지역' onChange=\"loadBranch(this,'branch')\" ")."
				".makeBranchSelectBox($cdb,'branch', $rg_ix, $branch, '지사', "validation=false title='지사' onChange=\"loadTeam(this,'team')\" ")."
				".makeTeamSelectBox($cdb,'team', $branch,$team,  '팀', "".($db->dt[mem_level] == "11" ? " style='display:none' validation=false":"validation=false")." title='팀'  ")."
			</td>
		</tr>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>MD 레벨  <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan='3'>
				<input type='radio' name='mem_level' id='mem_level_11' value='11' validation=false title='지사장' ".($db->dt[mem_level] == "11" ? "checked":"")." onclick=\"$('#team').attr('validation','false');$('#team').hide();\"><label for='mem_level_11'>지사장</label> &nbsp;&nbsp;
				<input type='radio' name='mem_level' id='mem_level_12' value='12' validation=false title='MD 팀장' ".($db->dt[mem_level] == "12" ? "checked":"")." onclick=\"$('#team').attr('validation','true');$('#team').show();\"><label for='mem_level_12'>MD 팀장</label>
				<input type='radio' name='mem_level' id='mem_level_13' value='13' validation=false title='MD' ".($db->dt[mem_level] == "13" ? "checked":"")." onclick=\"$('#team').attr('validation','true');$('#team').show();\"><label for='mem_level_13'>MD</label>
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
			<td class='input_box_title'><b>전화번호 </b></td>
			<td class='input_box_item'>
				<input type=text name='tel_1' id='tel_1' value='".$tel[0]."' maxlength=3 class='textbox numeric' style='width:30px' com_numeric=true validation='false' title='전화'> -
				<input type=text name='tel_2' id='tel_2' value='".$tel[1]."' maxlength=4 class='textbox numeric' style='width:40px' com_numeric=true validation='false' title='전화'> -
				<input type=text name='tel_3' id='tel_3' value='".$tel[2]."' maxlength=4 class='textbox numeric' style='width:40px' com_numeric=true validation='false' title='전화'>
			</td>
			<td class='input_box_title'><b>핸드폰 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text name='pcs_1' id='pcs_1' value='".$pcs[0]."' maxlength=3 class='textbox numeric' style='width:30px' com_numeric=true validation='true' title='핸드폰'> -
				<input type=text name='pcs_2' id='pcs_2' value='".$pcs[1]."' maxlength=4 class='textbox numeric' style='width:40px' com_numeric=true validation='true' title='핸드폰'> -
				<input type=text name='pcs_3' id='pcs_3' value='".$pcs[2]."' maxlength=4 class='textbox numeric' style='width:40px' com_numeric=true validation='true' title='핸드폰'>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'><b>회사전화번호</b></td>
			<td class='input_box_item'>
				<input type=text name='com_tel_1' id='com_tel_1' value='".$com_tel[0]."' maxlength=3 class='textbox numeric' style='width:30px' com_numeric=true validation='false' title='회사전화'> -
				<input type=text name='com_tel_2' id='com_tel_2' value='".$com_tel[1]."' maxlength=4 class='textbox numeric' style='width:40px' com_numeric=true validation='false' title='회사전화'> -
				<input type=text name='com_tel_3' id='com_tel_3' value='".$com_tel[2]."' maxlength=4 class='textbox numeric' style='width:40px' com_numeric=true validation='false' title='전화'>
				&nbsp;&nbsp;
				내선번호 
				<input type=text name='com_tel_4' id='com_tel_4' value='".$com_tel[3]."' maxlength=4 class='textbox numeric' style='width:40px' com_numeric=true validation='false' title='내선번호'>
			</td>
			<td class='input_box_title'><b>CTI 내선번호</b></td>
			<td class='input_box_item'>
				<input type=text name='cti_num' id='cti_num' value='".$cti_num."' maxlength=4 class='textbox numeric' style='width:40px' com_numeric=true title='CTI 내선번호'>
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
			<td class='input_box_title'><b>취미</b></td>
			<td class='input_box_item'>
				<input type=text name='interest' value='".$db->dt[interest]."' class='textbox'  style='width:200px' validation='false' title='취미'>
			</td>
			<td class='input_box_title' ><b>특기</b></td>
			<td class='input_box_item'>
				<input type=text name='specialty' value='".$db->dt[specialty]."' class='textbox'  style='width:200px' validation='false' title='특기'>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>주소</b></td>
			<td class='input_box_item'>
				<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
					<col width='70px'>
					<col width='*'>
					<tr>
						<td height=26>
							<input type='text' validation='false' class='textbox' name='zip' id='zipcode1' style='width:60px;' maxlength='15' value='".$db->dt[zip]."' readonly title='우편코드'>
						</td>
						<td style='padding:1px 0 0 5px;'>
							<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1');\" style='cursor:pointer;'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='addr1' id='addr1' value='".$db->dt[addr1]."' class='textbox' style='width:300px;' validation='false' title='주소1'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='addr2' id='addr2'  value='".$db->dt[addr2]."' class='textbox' style='width:300px' validation='false' title='주소2'>
						</td>
					</tr>
				</table>
			</td>
			<td class='input_box_title'> <b>실거주지 주소</b></td>
			<td class='input_box_item'>
				<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
					<col width='80px'>
					<col width='*'>
					<tr>
						<td height=26>
							<input type='text' validation='false' class='textbox' name='r_zipcode' id='return_zip1' style='width:60px;' maxlength='15' value='".$db->dt[r_zipcode]."' readonly  title='실거주지 우편번호'>
						</td>
						<td height=26 style=position:relative;top:2px;>
							<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('5');\" style='cursor:pointer;'>
							&nbsp;<input type='checkbox' name='change_address' id='change_address' onclick='addAddress(this)'><label for='change_address'> 주소와 동일 적용</label><br>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='r_addr1' id='return_addr1' value='".$db->dt[r_addr1]."' class='textbox' style='width:300px' validation='false' title='실거주지 주소1'>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=26>
							<input type=text name='r_addr2' id='return_addr2'  value='".$db->dt[r_addr2]."' class='textbox' style='width:300px' validation='false' title='실거주지 주소2'>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>급여계좌</b></td>
			<td class='input_box_item' colspan='3'>
			<select name='bank_name' style='width:100px;'>
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
				예금주 : &nbsp; <input type=text class='textbox' name='holder_name' value='".$db->dt[holder_name]."'  style='width:100px' validation='false' title='예금주' >&nbsp;&nbsp;&nbsp;
				계좌번호 : &nbsp; <input type=text class='textbox' name='bank_num' value='".$db->dt[bank_num]."'  style='width:200px' validation='false' title='계좌번호' >
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>퇴직일</b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' id='resign_date' name='resign_date' value='".$resign_date[0]."'  style='width:100px' validation='false' title='퇴직일'>
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
		<!--
		<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>변경/수정일자</b></td>
			<td class='input_box_item' colspan=3>
				<textarea type=text class='textbox' name='edit_data' value='".$db->dt[edit_data]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[edit_data]."</textarea>
			</td>
		</tr>-->
		</table>
	</td>
</tr>
</table><br>";

}

if($info_type == "f_info" || $info_type == ""){

$com_zip = explode("-",$db->dt[com_zip]);

$Contents .= "
		<table>
		<tr>
			<td height='20'></td>
		</tr>
		</table>

		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width=16%>
		<col width=16%>
		<col width=16%>
		<col width=16%>
		<col width=16%>
		<col width=16%>

		<tr bgcolor=#efefef align=center height=27>
			<td class='s_td'>관계</td>
			<td class='m_td'>이름</td>
			<td class='m_td'>생일</td>
			<td class='m_td'>직업</td>
			<td class='m_td'>연락처</td>
			<td class='e_td'>비고</td>
		</tr>";

$Contents .= "
		<tr align=center height=27>
			<input type='hidden' name ='f_info[0][family_ix]' value='".$f_array[0][family_ix]."' >
			<td class='input_box_item'><input type=text name='f_info[0][f_connection]' value='".$f_array[0][f_connection]."' class='textbox'  style='width:90%' validation='false' title='관계' ></td>
			<td class='input_box_item'><input type=text name='f_info[0][f_name]' value='".$f_array[0][f_name]."' class='textbox'  style='width:90%' validation='false' title='이름' ></td>
			<td class='input_box_item'><input type=text name='f_info[0][f_brithday]' value='".$f_array[0][f_brithday]."' class='textbox'  style='width:90%' validation='false' title='생일' ></td>
			<td class='input_box_item'><input type=text name='f_info[0][f_job]' value='".$f_array[0][f_job]."' class='textbox'  style='width:90%' validation='false' title='직업'></td>
			<td class='input_box_item'><input type=text name='f_info[0][f_contact]' value='".$f_array[0][f_contact]."' class='textbox'  style='width:90%' validation='false' title='연락처'></td>
			<td class='input_box_item'><input type=text name='f_info[0][f_note]' value='".$f_array[0][f_note]."' class='textbox'  style='width:90%' validation='false' title='비고'></td>
		</tr>
		<tr align=center height=27>
			<input type='hidden' name ='f_info[1][family_ix]' value='".$f_array[1][family_ix]."' >
			<td class='input_box_item'><input type=text name='f_info[1][f_connection]' value='".$f_array[1][f_connection]."' class='textbox'  style='width:90%' validation='false' title='관계'></td>
			<td class='input_box_item'><input type=text name='f_info[1][f_name]' value='".$f_array[1][f_name]."' class='textbox'  style='width:90%' validation='false' title='이름'></td>
			<td class='input_box_item'><input type=text name='f_info[1][f_brithday]' value='".$f_array[1][f_brithday]."' class='textbox'  style='width:90%' validation='false' title='생일'></td>
			<td class='input_box_item'><input type=text name='f_info[1][f_job]' value='".$f_array[1][f_job]."' class='textbox'  style='width:90%' validation='false' title='직업'></td>
			<td class='input_box_item'><input type=text name='f_info[1][f_contact]' value='".$f_array[1][f_contact]."' class='textbox'  style='width:90%' validation='false' title='연락처'></td>
			<td class='input_box_item'><input type=text name='f_info[1][f_note]' value='".$f_array[1][f_note]."' class='textbox'  style='width:90%' validation='false' title='비고'></td>
		</tr>
		<tr align=center height=27>
		<input type='hidden' name ='f_info[2][family_ix]' value='".$f_array[2][family_ix]."' >
			<td class='input_box_item'><input type=text name='f_info[2][f_connection]' value='".$f_array[2][f_connection]."' class='textbox'  style='width:90%' validation='false' title='관계'></td>
			<td class='input_box_item'><input type=text name='f_info[2][f_name]' value='".$f_array[2][f_name]."' class='textbox'  style='width:90%' validation='false' title='이름'></td>
			<td class='input_box_item'><input type=text name='f_info[2][f_brithday]' value='".$f_array[2][f_brithday]."' class='textbox'  style='width:90%' validation='false' title='생일'></td>
			<td class='input_box_item'><input type=text name='f_info[2][f_job]' value='".$f_array[2][f_job]."' class='textbox'  style='width:90%' validation='false' title='직업'></td>
			<td class='input_box_item'><input type=text name='f_info[2][f_contact]' value='".$f_array[2][f_contact]."' class='textbox'  style='width:90%' validation='false' title='연락처'></td>
			<td class='input_box_item'><input type=text name='f_info[2][f_note]' value='".$f_array[2][f_note]."' class='textbox'  style='width:90%' validation='false' title='비고'></td>
		</tr>
		<tr align=center height=27>
		<input type='hidden' name ='f_info[3][family_ix]' value='".$f_array[3][family_ix]."' >
			<td class='input_box_item'><input type=text name='f_info[3][f_connection]' value='".$f_array[3][f_connection]."' class='textbox'  style='width:90%' validation='false' title='관계'></td>
			<td class='input_box_item'><input type=text name='f_info[3][f_name]' value='".$f_array[3][f_name]."' class='textbox'  style='width:90%' validation='false' title='이름' ></td>
			<td class='input_box_item'><input type=text name='f_info[3][f_brithday]' value='".$f_array[3][f_brithday]."' class='textbox'  style='width:90%' validation='false' title='생일'></td>
			<td class='input_box_item'><input type=text name='f_info[3][f_job]' value='".$f_array[3][f_job]."' class='textbox'  style='width:90%' validation='false' title='직업'></td>
			<td class='input_box_item'><input type=text name='f_info[3][f_contact]' value='".$f_array[3][f_contact]."' class='textbox'  style='width:90%' validation='false' title='연락처'></td>
			<td class='input_box_item'><input type=text name='f_info[3][f_note]' value='".$f_array[3][f_note]."' class='textbox'  style='width:90%' validation='false' title='비고'></td>
		</tr>
		<tr align=center height=27>
		<input type='hidden' name ='f_info[4][family_ix]' value='".$f_array[4][family_ix]."' >
			<td class='input_box_item'><input type=text name='f_info[4][f_connection]' value='".$f_array[4][f_connection]."' class='textbox'  style='width:90%' validation='false' title='관계'></td>
			<td class='input_box_item'><input type=text name='f_info[4][f_name]' value='".$f_array[4][f_name]."' class='textbox'  style='width:90%' validation='false' title='이름'></td>
			<td class='input_box_item'><input type=text name='f_info[4][f_brithday]' value='".$f_array[4][f_brithday]."' class='textbox'  style='width:90%' validation='false' title='생일'></td>
			<td class='input_box_item'><input type=text name='f_info[4][f_job]' value='".$f_array[4][f_job]."' class='textbox'  style='width:90%' validation='false' title='직업'></td>
			<td class='input_box_item'><input type=text name='f_info[4][f_contact]' value='".$f_array[4][f_contact]."' class='textbox'  style='width:90%' validation='false' title='연락처'></td>
			<td class='input_box_item'><input type=text name='f_info[4][f_note]' value='".$f_array[4][f_note]."' class='textbox'  style='width:90%' validation='false' title='비고'></td>
		</tr>
		<tr align=center height=27>
		<input type='hidden' name ='f_info[5][family_ix]' value='".$f_array[5][family_ix]."' >
			<td class='input_box_item'><input type=text name='f_info[5][f_connection]' value='".$f_array[5][f_connection]."' class='textbox'  style='width:90%' validation='false' title='관계'></td>
			<td class='input_box_item'><input type=text name='f_info[5][f_name]' value='".$f_array[5][f_name]."' class='textbox'  style='width:90%' validation='false' title='이름'></td>
			<td class='input_box_item'><input type=text name='f_info[5][f_brithday]' value='".$f_array[5][f_brithday]."' class='textbox'  style='width:90%' validation='false' title='생일'></td>
			<td class='input_box_item'><input type=text name='f_info[5][f_job]' value='".$f_array[5][f_job]."' class='textbox'  style='width:90%' validation='false' title='직업'></td>
			<td class='input_box_item'><input type=text name='f_info[5][f_contact]' value='".$f_array[5][f_contact]."' class='textbox'  style='width:90%' validation='false' title='연락처'></td>
			<td class='input_box_item'><input type=text name='f_info[5][f_note]' value='".$f_array[5][f_note]."' class='textbox'  style='width:90%' validation='false' title='비고'></td>
		</tr>
		<!--
		<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>변경/수정일자</b></td>
			<td class='input_box_item' colspan=5>
			<textarea type=text class='textbox' name='edit_data' value='".$db->dt[edit_data]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[edit_data]."</textarea>
			</td>
		</tr>-->
		";
$Contents .= "
		</table><br>";
}

if($info_type == "s_info" || $info_type == ""){
$sql = "select * from common_user where code = '".$code."'";
$db->query($sql);
$db->fetch();
$company_id = $db->dt[company_id];

$com_zip = explode("-",$db->dt[com_zip]);
$Contents .= "
		<table><tr>
		<td height='20'></td></tr>
	  <tr></table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width=17%>
		<col width=20%>
		<col width=20%>
		<col width=10%>
		<col width=30%>

		<tr bgcolor=#efefef align=center height=27>
			<td class='s_td'>기간</td>
			<td class='m_td'>학교명</td>
			<td class='m_td'>학과/분야</td>
			<td class='m_td'>구분</td>
			<td class='m_td'>증명</td>
			</tr>";
$Contents .= "
		<tr align=center height=27 >
			<input type='hidden' name ='s_info[0][school_ix]' value='".$s_array[0][school_ix]."' >
			<td class='input_box_item'>
			<input type=text name='s_info[0][ac_st_date]' value='".$s_array[0][ac_st_date]."' class='textbox' style='width:70px;' validation='false' title='기간 시작'> ~ <input type=text name='s_info[0][ac_end_date]' value='".$s_array[0][ac_end_date]."' class='textbox'  style='width:70px;' validation='false' title='기간 끝'>
			</td>
			<td class='input_box_item'><input type=text name='s_info[0][ac_school]' value='".$s_array[0][ac_school]."' class='textbox'  style='width:90%' validation='false' title='학교'></td>
			<td class='input_box_item'><input type=text name='s_info[0][ac_department]' value='".$s_array[0][ac_department]."' class='textbox'  style='width:90%' validation='false' title='학과/분야'></td>
			<td class='input_box_item'>
				<select name='s_info[0][ac_division]' style='width:90%;'>
				<option value='0' >구분선택</option>
					<option value='1' ".($s_array[0][ac_division] == '1'?'selected':'').">졸업</option>
					<option value='2' ".($s_array[0][ac_division] == '2'?'selected':'').">휴학</option>
					<option value='3' ".($s_array[0][ac_division] == '3'?'selected':'').">자퇴</option>
					<option value='4' ".($s_array[0][ac_division] == '4'?'selected':'').">재학중</option>
			</td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='s_info_0' value='".$s_array[0][ac_proof_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$company_id."/".$s_array[0][ac_proof_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$s_array[0][ac_proof_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$s_array[0][ac_proof_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>";
	
$Contents .= "
		</tr>

		<tr align=center height=27 >
			<input type='hidden' name ='s_info[1][school_ix]' value='".$s_array[1][school_ix]."' >
			<td class='input_box_item'>
				<input type=text name='s_info[1][ac_st_date]' value='".$s_array[1][ac_st_date]."' class='textbox' style='width:70px;' validation='false' title='기간 시작'> ~ <input type=text name='s_info[1][ac_end_date]' value='".$s_array[1][ac_end_date]."' class='textbox'  style='width:70px;' validation='false' title='기간 끝'></td>
			<td class='input_box_item'><input type=text name='s_info[1][ac_school]' value='".$s_array[1][ac_school]."' class='textbox'  style='width:90%' validation='false' title='학교'></td>
			<td class='input_box_item'><input type=text name='s_info[1][ac_department]' value='".$s_array[1][ac_department]."' class='textbox'  style='width:90%' validation='false' title='학과/분야'></td>
			<td class='input_box_item'>
				<select name='s_info[1][ac_division]' style='width:90%;'>
				<option value='0' >구분선택</option>
					<option value='1' ".($s_array[1][ac_division] == '1'?'selected':'').">졸업</option>
					<option value='2' ".($s_array[1][ac_division] == '2'?'selected':'').">휴학</option>
					<option value='3' ".($s_array[1][ac_division] == '3'?'selected':'').">자퇴</option>
					<option value='4' ".($s_array[1][ac_division] == '4'?'selected':'').">재학중</option>
			</td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='s_info_1' value='".$s_array[1][ac_proof_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$company_id."/".$s_array[1][ac_proof_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$s_array[1][ac_proof_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$s_array[1][ac_proof_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>";
	
$Contents .= "
		</tr>

		<tr align=center height=27 >
			<input type='hidden' name ='s_info[2][school_ix]' value='".$s_array[2][school_ix]."' >
			<td class='input_box_item'>
				<input type=text name='s_info[2][ac_st_date]' value='".$s_array[2][ac_st_date]."' class='textbox' style='width:70px;' validation='false' title='기간 시작'>
				~ 
				<input type=text name='s_info[2][ac_end_date]' value='".$s_array[2][ac_end_date]."' class='textbox'  style='width:70px;' validation='false' title='기간 끝'>
			</td>
			<td class='input_box_item'>
				<input type=text name='s_info[2][ac_school]' value='".$s_array[2][ac_school]."' class='textbox'  style='width:90%' validation='false' title='학교'>
			</td>
			<td class='input_box_item'>
				<input type=text name='s_info[2][ac_department]' value='".$s_array[2][ac_department]."' class='textbox'  style='width:90%' validation='false' title='학과/분야'>
			</td>
			<td class='input_box_item'>
				<select name='s_info[2][ac_division]' style='width:90%;'>
					<option value='0' >구분선택</option>
					<option value='1' ".($s_array[2][ac_division] == '1'?'selected':'').">졸업</option>
					<option value='2' ".($s_array[2][ac_division] == '2'?'selected':'').">휴학</option>
					<option value='3' ".($s_array[2][ac_division] == '3'?'selected':'').">자퇴</option>
					<option value='4' ".($s_array[2][ac_division] == '4'?'selected':'').">재학중</option>
			</td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='s_info_2' value='".$s_array[2][ac_proof_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$company_id."/".$s_array[2][ac_proof_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$s_array[2][ac_proof_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$s_array[2][ac_proof_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
		</tr>";

$Contents .= "
		<tr align=center height=27 >
			<input type='hidden' name ='s_info[3][school_ix]' value='".$s_array[3][school_ix]."' >
			<td class='input_box_item'>
				<input type=text name='s_info[3][ac_st_date]' value='".$s_array[3][ac_st_date]."' class='textbox' style='width:70px;' validation='false' title='기간 시작'> ~ <input type=text name='s_info[3][ac_end_date]' value='".$s_array[3][ac_end_date]."' class='textbox'  style='width:70px;' validation='false' title='기간 끝'></td>
			<td class='input_box_item'><input type=text name='s_info[3][ac_school]' value='".$s_array[3][ac_school]."' class='textbox'  style='width:90%' validation='false' title='학교'></td>
			<td class='input_box_item'><input type=text name='s_info[3][ac_department]' value='".$s_array[3][ac_department]."' class='textbox'  style='width:90%' validation='false' title='학과/분야'></td>
			<td class='input_box_item'>
				<select name='s_info[3][ac_division]' style='width:90%;'>
				<option value='0' >구분선택</option>
					<option value='1' ".($s_array[3][ac_division] == '1'?'selected':'').">졸업</option>
					<option value='2' ".($s_array[3][ac_division] == '2'?'selected':'').">휴학</option>
					<option value='3' ".($s_array[3][ac_division] == '3'?'selected':'').">자퇴</option>
					<option value='4' ".($s_array[3][ac_division] == '4'?'selected':'').">재학중</option>
			</td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='s_info_3' value='".$s_array[3][ac_proof_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$company_id."/".$s_array[3][ac_proof_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$s_array[3][ac_proof_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$s_array[3][ac_proof_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
		</tr>";

$Contents .= "
		<tr align=center height=27 >
			<input type='hidden' name ='s_info[4][school_ix]' value='".$s_array[4][school_ix]."' >
			<td class='input_box_item'>
				<input type=text name='s_info[4][ac_st_date]' value='".$s_array[4][ac_st_date]."' class='textbox' style='width:70px;' validation='false' title='기간 시작'> ~ <input type=text name='s_info[4][ac_end_date]' value='".$s_array[4][ac_end_date]."' class='textbox'  style='width:70px;' validation='false' title='기간 끝'></td>
			<td class='input_box_item'><input type=text name='s_info[4][ac_school]' value='".$s_array[4][ac_school]."' class='textbox'  style='width:90%' validation='false' title='학교'></td>
			<td class='input_box_item'><input type=text name='s_info[4][ac_department]' value='".$s_array[4][ac_department]."' class='textbox'  style='width:90%' validation='false' title='학과/분야'></td>
			<td class='input_box_item'>
				<select name='s_info[4][ac_division]' style='width:90%;'>
				<option value='0' >구분선택</option>
					<option value='1' ".($s_array[4][ac_division] == '1'?'selected':'').">졸업</option>
					<option value='2' ".($s_array[4][ac_division] == '2'?'selected':'').">휴학</option>
					<option value='3' ".($s_array[4][ac_division] == '3'?'selected':'').">자퇴</option>
					<option value='4' ".($s_array[4][ac_division] == '4'?'selected':'').">재학중</option>
			</td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='s_info_4' value='".$s_array[4][ac_proof_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$company_id."/".$s_array[4][ac_proof_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$s_array[4][ac_proof_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$s_array[4][ac_proof_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
		</tr>";

$Contents .= "
		</table>";

$Contents .= "
		<table><tr>
		<td height='20'></td></tr>
	<tr>
	</table>


	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width=17%>
		<col width=20%>
		<col width=20%>
		<col width=10%>
		<col width=30%>
		<tr bgcolor=#efefef align=center height=27>
			<td class='s_td'>기간</td>
			<td class='m_td'>회사명</td>
			<td class='m_td'>부서</td>
			<td class='m_td'>직책</td>
			<td class='e_td'>증명</td>
		</tr>";

$Contents .= "
		<tr align=center height=27>
			<input type='hidden' name ='r_info[0][resume_ix]' value='".$r_array[0][resume_ix]."'>
			<td class='input_box_item' align='center'>
			<input type=text name='r_info[0][record_st_date]' value='".$r_array[0][record_st_date]."' class='textbox' style='width:70px;' validation='false' title='기간 시작'> ~ <input type=text name='r_info[0][record_end_date]' value='".$r_array[0][record_end_date]."' class='textbox'  style='width:70px;' validation='false' title='기간 끝'>
			</td>
			<td class='input_box_item'><input type=text name='r_info[0][company_name]' value='".$r_array[0][company_name]."' class='textbox'  style='width:90%'' validation='false' title='회사명'></td>
			<td class='input_box_item'><input type=text name='r_info[0][department]' value='".$r_array[0][department]."' class='textbox'  style='width:90%'' validation='false' title='부서'></td>
			<td class='input_box_item'><input type=text name='r_info[0][duty]' value='".$r_array[0][duty]."' class='textbox'  style='width:85%'' validation='false' title='직책'></td>

			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='r_info_0' value='".$r_array[0][record_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$company_id."/".$r_array[0][record_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$r_array[0][record_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$r_array[0][record_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
		</tr>

		<tr align=center height=27>
			<input type='hidden' name ='r_info[1][resume_ix]' value='".$r_array[1][resume_ix]."'>
			<td class='input_box_item' align='center'>
			<input type=text name='r_info[1][record_st_date]' value='".$r_array[1][record_st_date]."' class='textbox' style='width:70px;' validation='false' title='기간 시작'> ~ <input type=text name='r_info[1][record_end_date]' value='".$r_array[1][record_end_date]."' class='textbox'  style='width:70px;' validation='false' title='기간 끝'>
			</td>
			<td class='input_box_item'><input type=text name='r_info[1][company_name]' value='".$r_array[1][company_name]."' class='textbox'  style='width:90%'' validation='false' title='회사명'></td>
			<td class='input_box_item'><input type=text name='r_info[1][department]' value='".$r_array[1][department]."' class='textbox'  style='width:90%'' validation='false' title='부서'></td>
			<td class='input_box_item'><input type=text name='r_info[1][duty]' value='".$r_array[1][duty]."' class='textbox'  style='width:85%'' validation='false' title='직책'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='r_info_1' value='".$r_array[1][record_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$company_id."/".$r_array[1][record_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$r_array[1][record_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$r_array[1][record_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
		</tr>
		<tr align=center height=27>
			<input type='hidden' name ='r_info[2][resume_ix]' value='".$r_array[2][resume_ix]."'>
			<td class='input_box_item' align='center'>
			<input type=text name='r_info[2][record_st_date]' value='".$r_array[2][record_st_date]."' class='textbox' style='width:70px;' validation='false' title='기간 시작'> ~ <input type=text name='r_info[2][record_end_date]' value='".$r_array[2][record_end_date]."' class='textbox'  style='width:70px;' validation='false' title='기간 끝'>
			</td>
			<td class='input_box_item'><input type=text name='r_info[2][company_name]' value='".$r_array[2][company_name]."' class='textbox'  style='width:90%'' validation='false' title='회사명'></td>
			<td class='input_box_item'><input type=text name='r_info[2][department]' value='".$r_array[2][department]."' class='textbox'  style='width:90%'' validation='false' title='부서'></td>
			<td class='input_box_item'><input type=text name='r_info[2][duty]' value='".$r_array[2][duty]."' class='textbox'  style='width:85%'' validation='false' title='직책'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='r_info_2' value='".$r_array[2][record_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$company_id."/".$r_array[2][record_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$r_array[2][record_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$r_array[2][record_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}
	$Contents .= "
				</tr>
				</table>
			</td>
		</tr>
		<tr align=center height=27>
			<input type='hidden' name ='r_info[3][resume_ix]' value='".$r_array[3][resume_ix]."'>
			<td class='input_box_item' align='center'>
			<input type=text name='r_info[3][record_st_date]' value='".$r_array[3][record_st_date]."' class='textbox' style='width:70px;' validation='false' title='기간 시작'> ~ <input type=text name='r_info[3][record_end_date]' value='".$r_array[3][record_end_date]."' class='textbox'  style='width:70px;' validation='false' title='기간 끝'>
			</td>
			<td class='input_box_item'><input type=text name='r_info[3][company_name]' value='".$r_array[3][company_name]."' class='textbox'  style='width:90%'' validation='false' title='회사명'></td>
			<td class='input_box_item'><input type=text name='r_info[3][department]' value='".$r_array[3][department]."' class='textbox'  style='width:90%'' validation='false' title='부서'></td>
			<td class='input_box_item'><input type=text name='r_info[3][duty]' value='".$r_array[3][duty]."' class='textbox'  style='width:85%'' validation='false' title='직책'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='r_info_3' value='".$r_array[3][record_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$company_id."/".$r_array[3][record_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$r_array[3][record_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$r_array[3][record_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
		</tr>
		<tr align=center height=27>
			<input type='hidden' name ='r_info[4][resume_ix]' value='".$r_array[4][resume_ix]."'>
			<td class='input_box_item' align='center'>
			<input type=text name='r_info[4][record_st_date]' value='".$r_array[4][record_st_date]."' class='textbox' style='width:70px;' validation='false' title='기간 시작'> ~ <input type=text name='r_info[4][record_end_date]' value='".$r_array[4][record_end_date]."' class='textbox'  style='width:70px;' validation='false' title='기간 끝'>
			</td>
			<td class='input_box_item'><input type=text name='r_info[4][company_name]' value='".$r_array[4][company_name]."' class='textbox'  style='width:90%'' validation='false' title='회사명'></td>
			<td class='input_box_item'><input type=text name='r_info[4][department]' value='".$r_array[4][department]."' class='textbox'  style='width:90%'' validation='false' title='부서'></td>
			<td class='input_box_item'><input type=text name='r_info[4][duty]' value='".$r_array[4][duty]."' class='textbox'  style='width:85%'' validation='false' title='직책'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='r_info_4' value='".$r_array[4][record_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$company_id."/".$r_array[4][record_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$r_array[4][record_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$r_array[4][record_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
		</tr>
		<!--
			<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>변경/수정일자</b></td>
			<td class='input_box_item' colspan=4>
			<textarea type=text class='textbox' name='edit_data' value='".$r_array[0][edit_data]."' style='width:98%;height:85px;padding:2px;'>".$r_array[0][edit_data]."</textarea>
			</td>
		</tr>-->
		";
$Contents .= "
		</table><br>";
}


if($info_type == "j_info" || $info_type == ""){	//경력사항

$sql = "select * from common_user where code = '".$code."'";
$db->query($sql);
$db->fetch();
$company_id = $db->dt[company_id];

$com_zip = explode("-",$db->dt[com_zip]);
$Contents .= "
		<table><tr>
		<td height='20'></td></tr>
	  <tr></table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width=17%>
		<col width=15%>
		<col width=20%>
		<col width=10%>
		<col width=30%>

		<tr bgcolor=#efefef align=center height=27>
			<td class='s_td'>프로젝트명</td>
			<td class='m_td'>참여기간</td>
			<td class='m_td'>담당업무</td>
			<td class='m_td'>발주업체</td>
			<td class='m_td'>증명</td>
			</tr>";
$Contents .= "
		<tr align=center height=27>
			<input type='hidden' name ='p_info[0][project_ix]' value='".$p_array[0][project_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='p_info[0][project_name]' value='".$p_array[0][project_name]."' class='textbox' style='width:90%' validation='false' title='프로젝트명'></td>
			<td class='input_box_item'>
			<input type=text name='p_info[0][project_st_date]' value='".$p_array[0][project_st_date]."' class='textbox' style='width:70px;' validation='false' title='참여기간 시작'> ~ <input type=text name='p_info[0][project_end_date]' value='".$p_array[0][project_end_date]."' class='textbox' style='width:70px;' validation='false' title='참여기간 끝'></td>
			<td class='input_box_item'><input type=text name='p_info[0][project_work]' value='".$p_array[0][project_work]."' class='textbox' style='width:90%' validation='false' title='담당업무'></td>
			<td class='input_box_item'><input type=text name='p_info[0][project_order]' value='".$p_array[0][project_order]."' class='textbox' style='width:90%' validation='false' title='발주업체'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='p_info_0' value='".$p_array[0][project_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$p_array[0][project_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$p_array[0][project_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$p_array[0][project_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
			</tr>
		<tr align=center height=27>
			<input type='hidden' name ='p_info[1][project_ix]' value='".$p_array[1][project_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='p_info[1][project_name]' value='".$p_array[1][project_name]."' class='textbox' style='width:90%' validation='false' title='프로젝트명'></td>
			<td class='input_box_item'>
			<input type=text name='p_info[1][project_st_date]' value='".$p_array[1][project_st_date]."' class='textbox' style='width:70px;' validation='false' title='참여기간 시작'> ~ <input type=text name='p_info[1][project_end_date]' value='".$p_array[1][project_end_date]."' class='textbox' style='width:70px;' validation='false' title='참여기간 끝'></td>
			<td class='input_box_item'><input type=text name='p_info[1][project_work]' value='".$p_array[1][project_work]."' class='textbox' style='width:90%' validation='false' title='담당업무'></td>
			<td class='input_box_item'><input type=text name='p_info[1][project_order]' value='".$p_array[1][project_order]."' class='textbox' style='width:90%' validation='false' title='발주업체'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='p_info_1' value='".$p_array[1][project_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$p_array[1][project_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$p_array[1][project_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$p_array[1][project_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
			</tr>
		<tr align=center height=27>
			<input type='hidden' name ='p_info[2][project_ix]' value='".$p_array[2][project_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='p_info[2][project_name]' value='".$p_array[2][project_name]."' class='textbox' style='width:90%' validation='false' title='프로젝트명'></td>
			<td class='input_box_item'>
			<input type=text name='p_info[2][project_st_date]' value='".$p_array[2][project_st_date]."' class='textbox' style='width:70px' validation='false' title='참여기간 시작'> ~ <input type=text name='p_info[2][project_end_date]' value='".$p_array[2][project_end_date]."' class='textbox' style='width:70px' validation='false' title='참여기간 끝'></td>
			<td class='input_box_item'><input type=text name='p_info[2][project_work]' value='".$p_array[2][project_work]."' class='textbox' style='width:90%' validation='false' title='담당업무'></td>
			<td class='input_box_item'><input type=text name='p_info[2][project_order]' value='".$p_array[2][project_order]."' class='textbox' style='width:90%' validation='false' title='발주업체'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='p_info_2' value='".$p_array[2][project_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$p_array[2][project_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$p_array[2][project_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$p_array[2][project_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
			</tr>
		<tr align=center height=27>
			<input type='hidden' name ='p_info[3][project_ix]' value='".$p_array[3][project_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='p_info[3][project_name]' value='".$p_array[3][project_name]."' class='textbox' style='width:90%' validation='false' title='프로젝트명'></td>
			<td class='input_box_item'>
			<input type=text name='p_info[3][project_st_date]' value='".$p_array[3][project_st_date]."' class='textbox' style='width:70px' validation='false' title='참여기간 시작'> ~ <input type=text name='p_info[3][project_end_date]' value='".$p_array[3][project_end_date]."' class='textbox' style='width:70px' validation='false' title='참여기간 끝'></td>
			<td class='input_box_item'><input type=text name='p_info[3][project_work]' value='".$p_array[3][project_work]."' class='textbox' style='width:90%' validation='false' title='담당업무'></td>
			<td class='input_box_item'><input type=text name='p_info[3][project_order]' value='".$p_array[3][project_order]."' class='textbox' style='width:90%' validation='false' title='발주업체'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='p_info_3' value='".$p_array[3][project_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$p_array[3][project_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$p_array[3][project_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$p_array[3][project_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
			</tr>
		<tr align=center height=27>
			<input type='hidden' name ='p_info[4][project_ix]' value='".$p_array[4][project_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='p_info[4][project_name]' value='".$p_array[4][project_name]."' class='textbox' style='width:90%' validation='false' title='프로젝트명'></td>
			<td class='input_box_item'>
			<input type=text name='p_info[4][project_st_date]' value='".$p_array[4][project_st_date]."' class='textbox' style='width:70px' validation='false' title='참여기간 시작'> ~ <input type=text name='p_info[4][project_end_date]' value='".$p_array[4][project_end_date]."' class='textbox' style='width:70px' validation='false' title='참여기간 끝'></td>
			<td class='input_box_item'><input type=text name='p_info[4][project_work]' value='".$p_array[4][project_work]."' class='textbox' style='width:90%' validation='false' title='담당업무'></td>
			<td class='input_box_item'><input type=text name='p_info[4][project_order]' value='".$p_array[4][project_order]."' class='textbox' style='width:90%' validation='false' title='발주업체'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='p_info_4' value='".$p_array[4][project_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$p_array[4][project_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$p_array[4][project_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$p_array[4][project_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
			</tr>
		<tr align=center height=27>
			<input type='hidden' name ='p_info[5][project_ix]' value='".$p_array[5][project_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='p_info[5][project_name]' value='".$p_array[5][project_name]."' class='textbox' style='width:90%' validation='false' title='프로젝트명'></td>
			<td class='input_box_item'>
				<input type=text name='p_info[5][project_st_date]' value='".$p_array[5][project_st_date]."' class='textbox' style='width:70px' validation='false' title='참여기간 시작'>
				~ 
				<input type=text name='p_info[5][project_end_date]' value='".$p_array[5][project_end_date]."' class='textbox' style='width:70px' validation='false' title='참여기간 끝'></td>
			<td class='input_box_item'>
				<input type=text name='p_info[5][project_work]' value='".$p_array[5][project_work]."' class='textbox' style='width:90%' validation='false' title='담당업무'>
			</td>
			<td class='input_box_item'>
				<input type=text name='p_info[5][project_order]' value='".$p_array[5][project_order]."' class='textbox' style='width:90%' validation='false' title='발주업체'>
			</td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='p_info_5' value='".$p_array[5][project_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$p_array[5][project_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$p_array[5][project_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$p_array[5][project_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
		</tr>
		<!--
		<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>변경/수정일자</b></td>
			<td class='input_box_item' colspan=4>
			<textarea type=text class='textbox' name='edit_data' value='".$db->dt[edit_data]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[edit_data]."</textarea>
			</td>
		</tr>-->";
$Contents .= "
		</table><br>";
}

if($info_type == "z_info" || $info_type == ""){
	$com_zip = explode("-",$db->dt[com_zip]);
$Contents .= "
		<table><tr>
		<td height='20'></td></tr>
	  <tr></table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width=20%>
		<col width=17%>
		<col width=13%>
		<col width=15%>
		<col width=35%>

		<tr bgcolor=#efefef align=center height=27>
			<td class='s_td'>자격증명</td>
			<td class='m_td'>발급기간</td>
			<td class='m_td'>자격증번호</td>
			<td class='m_td'>취득일</td>
			<td class='m_td'>증명</td>
			</tr>";
$Contents .= "
		<tr align=center height=27>
			<input type='hidden' name ='z_info[0][certificate_ix]' value='".$z_array[0][certificate_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='z_info[0][certificate_name]' value='".$z_array[0][certificate_name]."' class='textbox' style='width:90%' validation='false' title='자격증명'></td>
			<td class='input_box_item'>
			<input type=text name='z_info[0][cert_st_date]' value='".$z_array[0][cert_st_date]."' class='textbox' style='width:70px' validation='false' title='발급기간 시작'> ~ <input type=text name='z_info[0][cert_end_date]' value='".$z_array[0][cert_end_date]."' class='textbox' style='width:70px' validation='false' title='발급기간 끝'></td>
			<td class='input_box_item'><input type=text name='z_info[0][cert_num]' value='".$z_array[0][cert_num]."' class='textbox' style='width:90%' validation='false' title='자격증번호'></td>
			<td class='input_box_item'><input type=text name='z_info[0][get_date]' value='".$z_array[0][get_date]."' class='textbox' style='width:90%' validation='false' title='취일일'></td>

			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='z_info_0' value='".$z_array[0][cert_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$z_array[0][cert_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$z_array[0][cert_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$z_array[0][cert_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
			</tr>
		<tr align=center height=27>
			<input type='hidden' name ='z_info[1][certificate_ix]' value='".$z_array[1][certificate_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='z_info[1][certificate_name]' value='".$z_array[1][certificate_name]."' class='textbox' style='width:90%' validation='false' title='자격증명'></td>
			<td class='input_box_item'>
			<input type=text name='z_info[1][cert_st_date]' value='".$z_array[1][cert_st_date]."' class='textbox' style='width:70px' validation='false' title='발급기간 시작'> ~ <input type=text name='z_info[1][cert_end_date]' value='".$z_array[1][cert_end_date]."' class='textbox' style='width:70px' validation='false' title='발급기간 끝'></td>
			<td class='input_box_item'><input type=text name='z_info[1][cert_num]' value='".$z_array[1][cert_num]."' class='textbox' style='width:90%' validation='false' title='자격증번호'></td>
			<td class='input_box_item'><input type=text name='z_info[1][get_date]' value='".$z_array[1][get_date]."' class='textbox' style='width:90%' validation='false' title='취일일'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='z_info_1' value='".$z_array[1][cert_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$z_array[1][cert_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$z_array[1][cert_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$z_array[1][cert_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
			</tr>
		<tr align=center height=27>
			<input type='hidden' name ='z_info[2][certificate_ix]' value='".$z_array[2][certificate_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='z_info[2][certificate_name]' value='".$z_array[2][certificate_name]."' class='textbox' style='width:90%' validation='false' title='자격증명'></td>
			<td class='input_box_item'>
			<input type=text name='z_info[2][cert_st_date]' value='".$z_array[2][cert_st_date]."' class='textbox' style='width:70px' validation='false' title='발급기간 시작'> ~ <input type=text name='z_info[2][cert_end_date]' value='".$z_array[2][cert_end_date]."' class='textbox' style='width:70px' validation='false' title='발급기간 끝'></td>
			<td class='input_box_item'><input type=text name='z_info[2][cert_num]' value='".$z_array[2][cert_num]."' class='textbox' style='width:90%' validation='false' title='자격증번호'></td>
			<td class='input_box_item'><input type=text name='z_info[2][get_date]' value='".$z_array[2][get_date]."' class='textbox' style='width:90%' validation='false' title='취일일'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='z_info_2' value='".$z_array[2][cert_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$z_array[2][cert_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$z_array[2][cert_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$z_array[2][cert_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
			</tr>
		<tr align=center height=27>
			<input type='hidden' name ='z_info[3][certificate_ix]' value='".$z_array[3][certificate_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='z_info[3][certificate_name]' value='".$z_array[3][certificate_name]."' class='textbox' style='width:90%' validation='false' title='자격증명'></td>
			<td class='input_box_item'>
			<input type=text name='z_info[3][cert_st_date]' value='".$z_array[3][cert_st_date]."' class='textbox' style='width:70px' validation='false' title='발급기간 시작'> ~ <input type=text name='z_info[3][cert_end_date]' value='".$z_array[3][cert_end_date]."' class='textbox' style='width:70px' validation='false' title='발급기간 끝'></td>
			<td class='input_box_item'><input type=text name='z_info[3][cert_num]' value='".$z_array[3][cert_num]."' class='textbox' style='width:90%' validation='false' title='자격증번호'></td>
			<td class='input_box_item'><input type=text name='z_info[3][get_date]' value='".$z_array[3][get_date]."' class='textbox' style='width:90%' validation='false' title='취일일'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='z_info_3' value='".$z_array[3][cert_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$z_array[3][cert_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$z_array[3][cert_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$z_array[3][cert_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
			</tr>
		<tr align=center height=27>
			<input type='hidden' name ='z_info[4][certificate_ix]' value='".$z_array[4][certificate_ix]."'>
			<td class='input_box_item' align='center'><input type=text name='z_info[4][certificate_name]' value='".$z_array[4][certificate_name]."' class='textbox' style='width:90%' validation='false' title='자격증명'></td>
			<td class='input_box_item'>
			<input type=text name='z_info[4][cert_st_date]' value='".$z_array[4][cert_st_date]."' class='textbox' style='width:70px' validation='false' title='발급기간 시작'> ~ <input type=text name='z_info[4][cert_end_date]' value='".$z_array[4][cert_end_date]."' class='textbox' style='width:70px' validation='false' title='발급기간 끝'></td>
			<td class='input_box_item'><input type=text name='z_info[4][cert_num]' value='".$z_array[4][cert_num]."' class='textbox' style='width:90%' validation='false' title='자격증번호'></td>
			<td class='input_box_item'><input type=text name='z_info[4][get_date]' value='".$z_array[4][get_date]."' class='textbox' style='width:90%' validation='false' title='취일일'></td>
			<td class='input_box_item' style='padding:5px;'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
				<tr>
					<td>
						<input type=file name='z_info_4' value='".$z_array[4][cert_file]."' class='textbox'  style='width:60%' validation='false' title='파일찾기'>
					</td>";

					if(is_file($path."/".$z_array[4][cert_file])){
	$Contents .= "
					<td height='60' valign='middle'>
						<img src='".$admin_config[mall_data_root]."/images/basic/".$company_id."/".$z_array[4][cert_file]."' width=50 height=50>&nbsp;&nbsp;&nbsp;
					</td>
					<td height='60' valign='middle'>
						<a href='javascript:' onclick=\"del_img('".$z_array[4][cert_file]."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>
					</td>";

					}

	$Contents .= "
				</tr>
				</table>
			</td>
		</tr>
		<!--
		<tr bgcolor=#ffffff height=110>
			<td class='input_box_title'><b>변경/수정일자</b></td>
			<td class='input_box_item' colspan=4>
			<textarea type=text class='textbox' name='edit_data' value='".$db->dt[edit_data]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[edit_data]."</textarea>
			</td>
		</tr>-->";
$Contents .= "
		</table><br>";
}

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원정보를 백업하기 위해서는 회원정보 검색후 엑셀로 저장 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다</td></tr>
</table>
";*/
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$Contents .= HelpBox("회원관리", $help_text,'70');

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

$Script .= "<script language='javascript' src='../basic/company.add.js'></script>
<script language='JavaScript' src='../webedit/webedit.js'></script>
<script language='javascript' src='../include/DateSelect.js'></script>\n
	<Script Language='JavaScript' src='/admin/js/autocomplete.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	
<script language='javascript'>
function zipcode(type) {
	var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function addAddress(obj){

	var edit_form=document.edit_form;

	if(obj.checked){
		$('#return_zip1').val(edit_form.zipcode1.value);
		$('#return_addr1').val(edit_form.addr1.value);
		$('#return_addr2').val(edit_form.addr2.value);

	}else{
		$('#return_zip1').val('');
		$('#return_addr1').val('');
		$('#return_addr2').val('');
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

</script>";

if($company_id != "" && $info_type == "delivery_info"){

	$form = "<form name='edit_form' action='../basic/member.act.php' method='post' onsubmit='return SubmitX(this)' enctype='multipart/form-data'  target='act'>";
}else{//사원정보... 폼

	$form = "<form name='edit_form' action='../basic/member.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>";
}

$Contents01 = $form."";
$Contents01 = $Contents01.$form."
<table width='100%' border=0>
<input name='act' type='hidden' value='$act'>
<input name='mmode' type='hidden' value='$mmode'>
<input type='hidden' name='cid2' value='$cid2'>
<input type='hidden' name='depth' value='$depth'>
<input type='hidden' name='code' value='$code'>
<input name='info_type' type='hidden' value='$info_type'>
<input name='company_id' type='hidden' value='".$company_id."'>";
//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents01 = $Contents01."<tr><td>".$Contents."</td></tr>";
$Contents01 = $Contents01."<tr><td>".$Contents04."</td></tr>";
$Contents01 = $Contents01."<tr><td align=center>".$ButtonString."</td></tr>";
$Contents01 = $Contents01."</table></form><br><br>";


if($mmode == "pop"){
    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->Navigation = "기초정보관리 > 본사관리 > $menu_name";
    $P->NaviTitle = "회원정보수정";
    $P->title = "회원정보수정";
    $P->strContents = $Contents01;
    echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	//$P->OnloadFunction = "init();";
	if($page_type == 'store'){
		$P->strLeftMenu = store_menu();
	}else{
		$P->strLeftMenu = basic_menu();
	}
	$P->Navigation = "기초정보관리 > 본사관리 > $menu_name";
	$P->title = "사원등록";
	$P->strContents = $Contents01;
	echo $P->PrintLayOut();
}

?>



<script type="text/javascript">
	//	임시패스워드
	var new_PW = $("#wel_password").val();

	$("input[name=pw]").val(new_PW);
	$("input[name=pw_confirm]").val(new_PW);
</script>