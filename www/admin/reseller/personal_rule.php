<?
include("../class/layout.class");
//auth(9);

$db = new Database;
//$mdb = new Database;


$Script = "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'personal_rule.php?view='+sort;
}

function listAction(frm){
		
						PoPWindow('../sms.pop.php',450,370,'sendsms');
						frm.action = '../sms.pop.php';
						frm.target = 'sendsms';
						frm.submit();
					}

function clearAll(frm){
		for(i=0;i < frm.code.length;i++){
				frm.code[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.code.length;i++){
				frm.code[i].checked = true;
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
	input_check_num();
}

</script>";

$Contents = "

<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("개별인센티브설정", "리셀러관리 > 환경설정 > 개별인센티브설정")."</td>
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
            <input type='hidden' name=mc_ix value='".$mc_ix." '>
		    <col width='12%'>
			<col width='*'>
			<tr height=30>
		      <td class='search_box_title' >조건 </td>
		      <td class='search_box_item' >
					<table cellpadding=0 cellspacing=0 width=100%>
						<col width='80'>
						<col width='*'>
						<tr>
							<td>
							  <select name=search_type>
									<option value='name' ".CompareReturnValue("name",$search_type,"selected").">고객명</option>
									<option value='id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
									<option value='mail' ".CompareReturnValue("mail",$search_type,"selected").">이메일</option>
									<option value='tel' ".CompareReturnValue("tel",$search_type,"selected").">전화번호</option>
									<option value='pcs' ".CompareReturnValue("pcs",$search_type,"selected").">휴대전화</option>
									<!--option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">회사명</option>
									<option value='com_phone' ".CompareReturnValue("com_phone",$search_type,"selected").">회사전화</option>
									<option value='com_fax' ".CompareReturnValue("com_fax",$search_type,"selected").">회사팩스</option>
									<option value='jumin' ".CompareReturnValue("jumin",$search_type,"selected").">주민번호</option>
									<option value='addr1' ".CompareReturnValue("addr1",$search_type,"selected").">주소</option-->
									
							  </select>
							</td>
							<td>
								<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:70%;font-size:12px;padding:1px;' >
							</td>
						</tr>
					</table>
		      </td>
		    </tr>
		    <tr height=30>
		      <td class='search_box_title' > 리셀러 승인 </td>
		      <td class='search_box_item'  >
			   <input type=radio name='rsl_ok' value='A' id='rsl_ok_a'  ".CompareReturnValue("A",$rsl_ok,"checked")." checked>
			   <label for='rsl_ok_a'>전체</label>
		       <input type=radio name='rsl_ok' value='y' id='rsl_ok_y'  ".CompareReturnValue("y",$rsl_ok,"checked").">
			   <label for='rsl_ok_y'>승인</label>
			   <input type=radio name='rsl_ok' value='n' id='rsl_ok_n' ".CompareReturnValue("n",$rsl_ok,"checked").">
			   <label for='rsl_ok_n'>미승인</label>
		      </td>
		    </tr>
		    ";

 $Contents .= "
		    </table>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>			";

$Contents .= "
    </td>
  </tr>
  <tr height=50>
    	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
   </tr>
</table><br></form>
<form name='list_frm'>
<input type='hidden' name='code[]' id='code'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >

  <tr height=30 >
  	<td colspan=5><a href='javascript:listAction(document.list_frm);'><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle  ></a></td>
  	<td colspan=5 align=right>";
	$Contents .= "
	</td>
  </tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='5%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='5%' align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>
    <td width='15%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
	<td width='14%' align='center' class='m_td'><font color='#000000'><b>전화번호</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>리셀러 등록일</b></font></td>
    <td width='7%' align='center' class=m_td><font color='#000000'><b>승인여부</b></font></td>
    <td width='20%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
  </tr>";


	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

	
	$where = " where cu.code != '' and cu.code = cmd.code and cu.code = rp.rsl_code";

	
	if($rsl_ok != "A" && $rsl_ok != ""){
		$where .= " and rsl_ok =  '$rsl_ok' ";
	}


	if($search_type != "" && $search_text != ""){
		 if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
			$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else{
			$where .= " and $search_type LIKE  '%$search_text%' ";
		}
	}
	
	// 전체 갯수 불러오는 부분
	$db->query("SELECT count(*) as total FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd , reseller_policy rp  $where ");
	$db->fetch();
	$total = $db->dt[total];
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&rsl_ok=$rsl_ok","view");
/*
	$sql = "SELECT cu.*, date_format(cu.date,'%Y.%m.%d') as regdate, UNIX_TIMESTAMP(last) AS last, sum(case when mr.state in (1,2,5,6,7) then reserve else 0 end) as reserve
	FROM  ".TBL_COMMON_USER." mm left outer join ".TBL_SHOP_RESERVE_INFO." mr on cu.code = mr.uid
	$where
	group by com_name, com_number, com_phone, com_fax, com_business_status, com_business_category, code, name, mail, visit, perm,gp_level, last
	ORDER BY date DESC LIMIT $start, $max";
*/
	$sql = "select cu.code, cu.id,  AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
			AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."')as pcs,
			AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."')as tel,
			date_format(rp.regdate,'%Y.%m.%d') as regdate, rp.rsl_ok,  UNIX_TIMESTAMP(cu.last) AS last
			from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , reseller_policy rp
			$where
			ORDER BY rp.regdate DESC
			LIMIT $start, $max";

	//echo nl2br($sql);

	$db->query($sql);

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
	
	/*
		$mdb->query("SELECT IFNULL(sum(reserve),'-') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in (1,2,5,6,7)");
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[0]);
	*/
	

$Contents = $Contents."
  <tr height='28'  onMouseOut=\"this.style.backgroundColor=''\">
    <td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
    <td class='list_box_td' >".$no."</td>
    
    <td class='list_box_td point' nowrap>".$db->dt[name]."</td>
    <td class='list_box_td' >".$db->dt[id]."</td>
    <td class='list_box_td' >".$db->dt[mail]."</td>";
	if (isset($db->dt[pcs])){
	$Contents = $Contents." <td class='list_box_td' >".$db->dt[pcs]."</td> ";
	}
	else{
	$Contents = $Contents." <td class='list_box_td' >".$db->dt[tel]."</td> ";
	}
$Contents = $Contents."
    <td class='list_box_td' >".$db->dt[regdate]."</td>
    <td class='list_box_td ctr point' >".$db->dt[rsl_ok]."</td>
    <td class='list_box_td ctr'  style='padding:5px;' nowrap>
		<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('personal_rule_info.php?code=".$db->dt[code]."',700,410,'personal_rule_info')\" onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='pointer'\" /> 
	    <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\" onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='pointer'\">
	     <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\" onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='pointer'\">
    </td>
  </tr>";

	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='9' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>";
}



$Contents .= "
	</form>
 
</table>
<div style='width:100%;text-align:right;padding:10px 0px;'>".$str_page_bar."</div>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >각자 개인별 인센티브 설정을 다르게 사용하시고자 할떄 수정버튼을 누르시면 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다.</td></tr>
</table>
";
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("개별인센티브설정", $help_text,'70');


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = reseller_menu();
$P->Navigation = "리셀러관리 > 환경설정 > 개별인센티브설정";
$P->title = "개별인센티브설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>