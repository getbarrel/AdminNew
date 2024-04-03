<?php
include("../class/layout.class");
include("./company.lib.php");
//auth(9);

$db = new Database;
$mdb = new Database;
//echo "<pre>";
//print_r ($_REQUEST);
//EXIT;
//$db->debug = true;//페이지에서 오류가 없는데 쿼리를 찍기에 주석처리함 kbk 13/02/04
$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "잠금관리자 관리";
}else{
	$menu_name = "잠금관리자 관리";
}

$info_type = "sleep_member";
$work_devision = 'O'; //퇴사사원 리스트 

$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}



		//	================================ 데이터 불러오는 쿼리부분 ================================
			//include "member_query.php";

			if($max == ""){
				$max = '20';
			}
			if ($page == ''){
				$start = 0;
				$page  = 1;
			}else{
				$start = ($page - 1) * $max;
			}





				$search_text = trim($search_text);
				if($db->dbms_type == "oracle"){
					if($search_type != "" && $search_text != ""){
						if($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.pcs" || $search_type == "cmd.tel"  || $search_type == "cu.id" || $search_type == "cmd.addr1"){
							$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
							$count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
						}else{
							$where .= " and $search_type LIKE  '%$search_text%' ";
							$count_where .= " and $search_type LIKE  '%$search_text%' ";
						}
					}
				}else{
					if($search_type != "" && $search_text != ""){
						$search_text = trim($search_text);
						if($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.addr1" || $search_type == "cmd.com_tel" || $search_type == "cmd.pcs" || $search_type == "cmd.tel"){
							if($search_type == "cmd.pcs" || $search_type == "cmd.tel" || $search_type == "cmd.com_tel"){
								$where .= " and ( AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' OR replace(AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."'),'-','') LIKE  '%$search_text%' ) ";
								$count_where .= " and ( AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' OR replace(AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."'),'-','') LIKE  '%$search_text%') ";
							}else{
								$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
								$count_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
							}
						}else if($search_type == "id"){

							if(strpos($search_text,",") !== false){
								$search_array = explode(",",$search_text);
								$where .= "and ( ";
								$count_where .= "and ( ";
								for($i=0;$i<count($search_array);$i++){

									if($i == count($search_array) - 1){
										$where .= $search_type." = '".trim($search_array[$i])."'";
										$count_where .= $search_type." = '".trim($search_array[$i])."'";
									}else{
										$where .= $search_type." = '".trim($search_array[$i])."' or ";
										$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
									}
								}
								$where .= ")";
								$count_where .= ")";
							}else if(strpos($search_text,"\n") !== false){//\n

								$search_array = explode("\n",$search_text);

								$where .= "and ( ";
								$count_where .= "and ( ";
								for($i=0;$i<count($search_array);$i++){

									if($i == count($search_array) - 1){
										$where .= $search_type." = '".trim($search_array[$i])."'";
										$count_where .= $search_type." = '".trim($search_array[$i])."'";
									}else{
										$where .= $search_type." = '".trim($search_array[$i])."' or ";
										$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
									}
								}
								$where .= ")";
								$count_where .= ")";
							}else{
								$where .= "and ".$search_type." like '%".trim($search_text)."%'";
								$count_where .= "and ".$search_type." like '%".trim($search_text)."%'";
							}
						}else{
							$where .= " and $search_type LIKE  '%$search_text%' ";
							$count_where .= " and $search_type LIKE  '%$search_text%' ";
						}
					}
				}




				$mem_type = "A";
				if($mem_type != ""){
					$where .= " and cu.mem_type =  '$mem_type' ";
					$count_where .= " and cu.mem_type =  '$mem_type' ";
					$cmd_where .= " and cu.mem_type =  '$mem_type' ";
				}


					/*회원 관련 휴면 TABLE 이 존재하지 않을 경우 기존 회원테이블과 동일하게 생성함 JK*/
					$sql = "SHOW TABLES LIKE 'common_user_sleep'";
					$db->query($sql);
					
					if(!is_array($db->fetchall())){
						$sql = "CREATE TABLE IF NOT EXISTS common_user_sleep LIKE ".TBL_COMMON_USER."";
						$db->query($sql);
					}
					
					$sql = "SHOW TABLES LIKE 'common_member_detail_sleep'";
					$db->query($sql);
					
					if(!is_array($db->fetchall())){
						$sql = "CREATE TABLE IF NOT EXISTS common_member_detail_sleep LIKE ".TBL_COMMON_MEMBER_DETAIL."";
						$db->query($sql);
					}
					
					$sql = "SHOW TABLES LIKE 'common_company_detail_sleep'";
					$db->query($sql);
					
					if(!is_array($db->fetchall())){
						$sql = "CREATE TABLE IF NOT EXISTS common_company_detail_sleep LIKE ".TBL_COMMON_COMPANY_DETAIL."";
						$db->query($sql);
					}
					/*끝*/

					$sql = "SELECT count(*) as total FROM common_user_sleep cu
						inner join common_member_detail_sleep cmd on cu.code = cmd.code
						left join common_company_detail_sleep as ccd on (ccd.company_id = cu.company_id)
						where 
							1
						$count_where ";

				$db->query($sql);
				$db->fetch();
				$total = $db->dt[total];
				
					$limit_where = "LIMIT $start, $max";


				if($info_type == 'sleep_member'){
					if($db->dbms_type == "oracle"){//ccd.com_name, mg.gp_level,mg.gp_name, 

						$sql = "select cu.mall_ix,cu.code, cu.id,  cu.company_id, ccd.com_name,cu.mileage,cu.point,cu.mem_div,
							AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, mg.gp_level,
							AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,cu.ip,
							cu.visit, date_format(cu.date,'%Y.%m.%d') as regdate, mg.gp_name,  UNIX_TIMESTAMP(cu.last) AS last, cu.last AS last, cmd.gp_ix,cmd.nationality,cmd.mem_code,
							cmd.join_date,
							cmd.com_group,
							cmd.department,
							cmd.duty,
							cmd.position,
							cr.relation_code,
							(select cul.status from common_user_sleep_log as cul where cu.code= cul.code order by cul.regdate desc limit 1) as status,
							(select cul.regdate from common_user_sleep_log as cul where cu.code= cul.code order by cul.regdate desc limit 1) as sleep_date,
							(select cul.charger_ix from common_user_sleep_log as cul where cu.code= cul.code order by cul.regdate desc limit 1) as charger_ix
							from 
								common_user_sleep as cu 
								inner join common_member_detail_sleep as cmd on (cu.code = cmd.code)
								left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
								left join common_company_detail_sleep as ccd on (ccd.company_id = cu.company_id)
								LEFT JOIN common_company_relation AS cr ON (cu.company_id = cr.company_id)
							$where 
							ORDER BY cu.date DESC
							$limit_where";

					}else{
						$sql = "select cu.mall_ix,cu.code, cu.id,  cu.company_id, ccd.com_name,cu.mileage,cu.point,cu.mem_div,
							AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, mg.gp_level,
							AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,cu.ip,
							cu.visit, date_format(cu.date,'%Y.%m.%d') as regdate, mg.gp_name,  UNIX_TIMESTAMP(cu.last) AS last, cu.last AS last, cmd.gp_ix,cmd.nationality,cmd.mem_code,
							cmd.join_date,
							cmd.com_group,
							cmd.department,
							cmd.duty,
							cmd.position,
							cr.relation_code,
							(select cul.status from common_user_sleep_log as cul where cu.code= cul.code order by cul.regdate desc limit 1) as status,
							(select cul.regdate from common_user_sleep_log as cul where cu.code= cul.code order by cul.regdate desc limit 1) as sleep_date,
							(select cul.charger_ix from common_user_sleep_log as cul where cu.code= cul.code order by cul.regdate desc limit 1) as charger_ix
							from 
								common_user_sleep as cu 
								inner join common_member_detail_sleep as cmd on (cu.code = cmd.code)
								left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
								left join common_company_detail_sleep as ccd on (ccd.company_id = cu.company_id)
								LEFT JOIN common_company_relation AS cr ON (cu.company_id = cr.company_id)
							where 
								1
							$where 
							ORDER BY cu.date DESC
							$limit_where";
					}
				}


				$script_time[query_start] = time();
				//echo nl2br($sql);exit;
				$db->query($sql);

				$script_time[query_end] = time();
				
				$str_page_bar = page_bar($total, $page,$max, "","view");//gp_level 을 사용하지 않아서 gp_ix 로 바꿈 kbk 13/02/19
		//	//================================ 데이터 불러오는 쿼리부분 ================================









$Script = "<script language='javascript' src='../include/DateSelect.js'></script>\n
<script language='javascript' src='../basic/company.add.js'></script>\n";
$Script .= "
<script language='javascript'>
function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.sdate.disabled = false;
		frm.edate.disabled = false;
	}else{
		frm.sdate.disabled = true;
		frm.edate.disabled = true;
	}
}

function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
	//input_check_num();
}

function checkAll(frm){
	for(i=0;i < frm.code.length;i++){
			frm.code[i].checked = true;
	}
}

function clearAll(frm){
	for(i=0;i < frm.code.length;i++){
			frm.code[i].checked = false;
	}
}

function init(){
	var frm = document.searchmember;
//	onLoad('$sDate','$eDate');";

if($regdate != "2"){
	$Script .= "
	frm.sdate.disabled = true;
	frm.edate.disabled = true;";
}

$Script .= "
}

";
$Script .= "

function deleteMemberInfo(act, code){
	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
	}
}
</script>";

$Contents = "

<table width='100%' border='0' align='center'>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("전체사원리스트", "기초정보관리 > 본사관리 ")."</td>
</tr>
	<tr>
	<td align='left' colspan=4 style='padding-bottom:20px;'> 
		<div class='tab'>
		<table class='s_org_tab'>
		<tr>
			<td class='tab'>
				<table id='tab_01' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02' onclick=\"document.location.href='member.list.php'\">전체사원 리스트</td>
					<th class='box_03'></th>
				</tr>
				</table>
				<table id='tab_02'>
				<tr>
					<th class='box_01'></th>
					<td class='box_02' onclick=\"document.location.href='member.resign.php'\">퇴사관리자 관리</td>
					<th class='box_03'></th>
				</tr>
				</table>
				<table id='tab_03' class='on' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02' onclick=\"document.location.href='member.sleep.php'\">잠금관리자 관리</td>
					<th class='box_03'></th>
				</tr>
				</table>
				<!--<table id='tab_03' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02' onclick=\"document.location.href='member.lump.php'\">일괄등록하기</td>
					<th class='box_03'></th>
				</tr>
				</table>
				<table id='tab_04' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02' >탭 메뉴 4</td>
					<th class='box_03'></th>
				</tr>
				</table>
				<table id='tab_05' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02' >탭 메뉴 5</td>
					<th class='box_03'></th>
				</tr>
				</table>
				<table id='tab_06' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02' >탭 메뉴 6</td>
					<th class='box_03'></th>
				</tr>
				</table-->
			</td>
			<td class='btn'>
			</td>
		</tr>
		</table>
	</div>
	</td>
</tr>
<tr>
	<td>";
$Contents .= "
		<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:5px 0px 5px 0px'>
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
				<form name=searchmember method='get'><!--SubmitX(this);'-->
					<input type='hidden' name=mc_ix value='".$mc_ix."'>
					<input type='hidden' name='cid2' value='$cid2'>
					<input type='hidden' name='depth' value='$depth'>
					<col width='15%'>
					<col width='*'>

					<tr height=27>
						<td class='search_box_title' >검색 </td>
						<td class='search_box_item' colspan=3>
							<table cellpadding=0 cellspacing=0 width=100%>
								<col width='80'>
								<col width='*'>
								<tr>
									<td>
										<select name=search_type style='width:100px;'>
												<option value='cmd.name' ".CompareReturnValue('cmd.name',$search_type,"selected").">사원명</option>
												<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">아이디</option>
										</select>
									</td>
									<td>&nbsp;
										<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:300px;font-size:12px;padding:1px;' >
									</td>
								</tr>
							</table>
						</td>
					</tr>
					</table>
				</td>
				<th class='box_06'></th>
			</tr>
			<tr>
				<th class='box_07'></th>
				<td class='box_08'></td>
				<th class='box_09'></th>
			</tr>
		</table>";

$Contents .= "
    </td>
  </tr>
  <tr height=50>
    	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
    </tr>
</table><br>
</form>";





$Contents .= "
<form name='list_frm'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
<tr height='28' bgcolor='#ffffff'>
	<td width='3%' align='center' class=s_td rowspan='2'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
	<td width='3%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>순번</b></font></td>
	<td width='5%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>사원코드</b></font></td>
	<td width='5%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>입사일</font></td>
	<td width='6%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>잠금일</font></td>
	<td width='6%' align='center' class=m_td rowspan='2'><font color='#000000'><b>ID</b></font></td>
	<td width='6%' align='center' class=m_td rowspan='2'><font color='#000000'><b>이름</b></font></td>
	<td width='10%' align='center' class=m_td rowspan='2'><font color='#000000'><b>근무사업장</b></font></td>
	<td width='13%' align='center' class=m_td colspan='4'><font color='#000000'><b>부서및직책</b></font></td>
	<!--<td width='7%' align='center' class=m_td rowspan='2'><font color='#000000'><b>연락처</b></font></td>-->
	<!--<td width='10%' align='center' class=m_td rowspan='2'><font color='#000000'><b>이메일</b></font></td>-->
	<!--<td width='6%' align='center' class=m_td rowspan='2'><font color='#000000'><b>메신저</b></font></td>-->
	<td width='9%' align='center' class=e_td rowspan='2'><font color='#000000'><b>상태변경</b></font></td>
</tr>";
$Contents .= "
	<tr height='28' bgcolor='#ffffff'>

	<td width='6%' align='center' class=m_td><font color='#000000'><b>부서그룹</b></font></td>
	<td width='6%' align='center' class=m_td><font color='#000000'><b>부서</b></font></td>
	<td width='6%' align='center' class=m_td><font color='#000000'><b>직위</b></font></td>
	<td width='6%' align='center' class=m_td><font color='#000000'><b>직책</b></font></td>
	
	</tr>";

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;
	/*
		if ($db->dt[mem_level] == "E")	{ $perm = "탈퇴회원"; }
		if ($db->dt[mem_level] == "M")	{ $perm = "일반회원"; }
		if ($db->dt[mem_level] == "B")	{ $perm = "입점업체"; }
		if ($db->dt[mem_level] == "C")	{ $perm = "특별회원"; }
	*/
		
		if($db->dt[is_id_auth] != "Y"){
			$is_id_auth = "미인증";
		}else{
			$is_id_auth = "";
		}

		switch($db->dt[authorized]){

		case "Y":
			$authorized = "승인";
			break;
		case "N":
			$authorized = "승인대기";
			break;
		case "X":
			$authorized = "승인거부";
			break;
		default:
			$authorized = "알수없음";
			break;
		}

		switch($db->dt[mem_type]){

		case "M":
			$mem_type = "일반";
			break;
		case "C":
			$mem_type = "기업".($db->dt[com_name] != "" ? "(".$db->dt[com_name].")":"");
			break;
		case "F":
			$mem_type = "외국인";
			break;
		case "S":
			$mem_type = "셀러";
			break;
		case "A":
			$mem_type = "관리자";
			break;
		case "MD":
			$mem_type = "MD";
			break;
		default:
			$mem_type = "일반";
			break;
		}



		switch($db->dt[status]){
			case "A":
			$sleep_status = "관리자 일관변경";
			break;
			case "S":
			$sleep_status = "시스템 자동";
			break;
		}



	if($db->dt[join_date]){
		$join_array = explode(" ",$db->dt[join_date]);
		$join_date = $join_array[0];
	}else{
		$join_date = " - ";
	}

	if($db->dt[resign_date]){
		$resign_array = explode(" ",$db->dt[resign_date]);
		$resign_date = $resign_array[0];
	}else{
		$resign_date = " - ";
	}

	$resign_array = explode("-",$resign_date);

//	$now = date('Y-m');
	$now = $resign_array[0]."-".$resign_array[1];
	$now_array = explode("-",$now);
	$join_array = explode("-",$join_date);

	$resign_year = $now_array[0] - $join_array[0];
	$resign_month = $now_array[1] - $join_array[1];

	if($resign_year > 0){
		$year = $resign_year." 년 ";
	}

	if($year > 0){
		$month = 12 -$join_array[1] + $now_array[1]." 개월 ";
	}else{
		if($resign_month > 0){
			$month = $resign_month." 개월 ";
		}
	}

	$Contents = $Contents."
	<tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
		<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
		<td class='list_box_td' >".$no."</td>
		<td class='list_box_td' style='padding:0px 5px;'>".$db->dt[mem_code]."</td>
		<td class='list_box_td' nowrap>".$join_date."</td>
		 <td class='list_box_td' nowrap>".$db->dt[sleep_date]."</td>
		 <td class='list_box_td' nowrap>".$db->dt[id]."</td>
		<td class='list_box_td' >".Black_list_check($db->dt[code],$db->dt[name])."</td>
		
		<td class='list_box_td point' nowrap>".getCompanyname($db->dt[relation_code])."</td>
		<!--<td class='list_box_td' >".getCompanyname($db->dt[relation_code],9)."</td>
		<td class='list_box_td' >".getCompanyname($db->dt[relation_code],13)."</td>
		<td class='list_box_td' >".getCompanyname($db->dt[relation_code],17)."</td>-->


		<td class='list_box_td ' nowrap>".getGroupname('group',$db->dt[com_group])."</td>
		<td class='list_box_td point' >".getGroupname('department',$db->dt[department])."</td>
		<td class='list_box_td' >".getGroupname('position',$db->dt[position])."</td>
		<td class='list_box_td point' >".getGroupname('duty',$db->dt[duty])."</td>


		<td class='list_box_td ctr'  style='padding:5px;' nowrap>
			".$sleep_status."
		</td>
	</tr>";

	}

if (!$db->total){

$Contents = $Contents."
	<tr height=50>
		<td colspan='14' align='center'>등록된 회원 데이타가 없습니다.</td>
	</tr>";
}


$Contents .= "
</table>
</form>
<table width=100% align='right'>
<tr hegiht=30><td colspan=8 align=right style='padding:10px 0px;'>".$str_page_bar."</td></tr>
</table>";

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
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("사원관리", $help_text,'70');








	//	잠금 사원 기능 추가
			$help_text2 = "

			<table cellpadding=0 cellspacing=0 class='small' width=100% >
				<col width=8>
				<col width=*>
				<tr>
				<td colspan='2'>
				<select name='update_type'>
					<!--<option value=''>전환방식 선택</option>-->
					<option value='2'>선택한회원 전체에게</option>
					<!--<option value='1'>검색한 회원 전체에게</option>-->
				</select>
				</td>
				</tr>";

				$help_text2 .= "
					<tr>
						<td colspan='2' style='padding-top:10px;'>
							<table width='50%' border='0' cellpadding='0' cellspacing='0' align='' class='list_table_box'>
								<col width=20%>
								<col width=*>
								<tr>
									<td class='list_box_td point' >
										일반회원 변경 사유
									</td>
									<td>
										<textarea rows='3' cols='10' name='message' style='margin:0 10px; width:95%'></textarea>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				";

				$help_text2 .= "
				<tr>
					<td align=left colspan='2' style='padding-top:10px;'>
				";
	
				$help_text2 .= "
				<button type='button' onclick=\"ig_sendData('nosleep');\">일반사용자 전환</button>";
				$help_text2 .= "
					</td>
				</tr>


			</table>
			";

			$Contents .= HelpBox("잠금사용자관리", $help_text2,'70')."</form>";
	//	//잠금 사원 기능 추가




$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = basic_menu();
$P->Navigation = "기초정보관리 > 본사관리 > $menu_name";
$P->title = "잠금관리자 관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>





	<script type="text/javascript">
		//	계정 전환
		function ig_sendData(sendType){



			var ig_frm_code = document.getElementsByName("code[]");

			var ig_code_checked_num = 0;
			var code_List = new Array();	//	선택된 회원 리스트


			for(i=0;i < ig_frm_code.length;i++){
				if(ig_frm_code[i].checked){
					code_List.push(ig_frm_code[i].value);
					ig_code_checked_num++;
				}
			}



			if(ig_code_checked_num == "0") {
				alert("사원을 선택해 주세요.");
				return false;
			} else {
				if(confirm('선택한 사원을 잠금계정해지 처리 하시겠습니까?')) {

						//	sendType 타입 : sleep = 잠그기, nosleep = 풀기
						$.ajax({
							type: "post",
							url:"ig_sendData.php",
							data:{
									"sendType":sendType,
									"code_List":code_List
								},
							cache: false,	
							async: false,
							success:function(data) {

								//console.log(data);

								if(data == "sendOK") {
									alert("처리되었습니다.");
									location.reload();
									return false;
								} else {
									alert(data);
									return false;
								}
							}
						});
				} else {
					return false;
				}
			}


		}
	</script>