<?
include("../class/layout.class");
//auth(9);

$db = new Database;


$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($FromYY == ""){

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

}
/*
if ($vFromYY == ""){

	$sDate2 = date("Y/m/d", $before10day);
	$eDate2 = date("Y/m/d");

	$startDate2 = date("Ymd", $before10day);
	$endDate2 = date("Ymd");

}else{

	$sDate2 = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate2 = $vToYY."/".$vToMM."/".$vToDD;
	$startDate2 = $vFromYY.$vFromMM.$vFromDD;
	$endDate2 = $vToYY.$vToMM.$vToDD;

}

if ($birYY == ""){

	$sDate3 = date("Y/m/d");
	$eDate3 = date("Y/m/d");

	$startDate3 = date("Ymd");
	$endDate3 = date("Ymd");
}else{

	$sDate3 = $birYY."/".$birMM."/".$birDD;
	$eDate3 = "none";
	$startDate3 = $birYY.$birMM.$birDD;
	$endDate3 = "none";
	$birDate = $birYY.$birMM.$birDD;
}
*/
$Script = "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'inflow_detail.php?view='+sort;
}


function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
	}else{
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	}
}
	/*
function ChangeVisitDate(frm){
	if(frm.visitdate.checked){
		frm.vFromYY.disabled = false;
		frm.vFromMM.disabled = false;
		frm.vFromDD.disabled = false;
		frm.vToYY.disabled = false;
		frm.vToMM.disabled = false;
		frm.vToDD.disabled = false;
	}else{
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;
	}
}
	*/

function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate','$sDate2','$eDate2','$sDate3');";

	if($regdate != "1"){
	$Script .= "
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;";
	}
	/*if($visitdate != "1"){
	$Script .= "
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;	";
	}
	if($bir != "1"){
	$Script .= "
		frm.birYY.disabled = true;
		frm.birMM.disabled = true;
		frm.birDD.disabled = true;";
	}*/
$Script .= "
}

</script>";

$Contents = "


<script language='javascript' src='inflow_detail.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("유입회원상세리스트", "리셀러관리 > 통계분석 > 유입회원상세리스트")."</td>
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
		    <tr height=27>
		      <td class='search_box_title' >조건검색 </td>
		      <td class='search_box_item' colspan='3'>
					<table cellpadding=0 cellspacing=0 width=100%>
						<col width='80'>
						<col width='*'>
						<tr>
							<td>
							  <select name=search_type>
									<option value='name' ".CompareReturnValue("name",$search_type,"selected").">회원명</option>
									<option value='id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
									<option value='mail' ".CompareReturnValue("mail",$search_type,"selected").">이메일</option>
									<option value='tel' ".CompareReturnValue("tel",$search_type,"selected").">전화번호</option>
									<option value='pcs' ".CompareReturnValue("pcs",$search_type,"selected").">휴대전화</option>
									<option value='rsl_id' ".CompareReturnValue("rsl_id",$search_type,"selected").">리셀러ID</option>
									<!--option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">회사명</option>
									<option value='com_phone' ".CompareReturnValue("com_phone",$search_type,"selected").">회사전화</option>
									<option value='com_fax' ".CompareReturnValue("com_fax",$search_type,"selected").">회사팩스</option>
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
		    ";

$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

 $Contents .= "
		    <tr height=27>
		      <td class='search_box_title' ><label for='regdate'>회원가입일</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
		      <td class='search_box_item'  colspan=3 >
		      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
					<tr>
						<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY  style='width:57px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM style='width:43px;'></SELECT> 월 <SELECT name=FromDD style='width:43px;'></SELECT> 일 </TD>
						<TD style='padding:0 5px;' align=center>~</TD>
						<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY style='width:57px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM style='width:43px;'></SELECT> 월 <SELECT name=ToDD style='width:43px;'></SELECT> 일</TD>
						<TD style='padding-left:10px;' >
							<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
							<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
							<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
							<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
							<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
							<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
							<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
						</TD>
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
</table>			
";

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
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	$Contents .= "<a href='member_excel2003.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}
	$Contents .= "
	</td>
  </tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='5%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
	<td width='5%' align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
    <td width='7%' align='center' class='m_td'><font color='#000000'><b>이름</b></font></td>
    <td width='7%' align='center' class='m_td'><font color='#000000'><b>ID</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>회원가입일</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
    <td width='5%' align='center' class=m_td><font color='#000000'><b>로그인수</b></font></td>
    <td width='7%' align='center' class=m_td><font color='#000000'><b>리셀러ID</b></font></td>
    <td width='15%' align='center' class=m_td><font color='#000000'><b>유입유형/URL</b></font></td>
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



	$where = " where rfd.rsl_code != '' and rfd.flowin_code !='' and rfd.flowin_code = cu.code and cu.code = cmd.code " ;
	
	
	if($search_type != "" && $search_text != ""){
		
		if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
			$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}elseif($search_type == "rsl_id"){
			$sql="select code from ".TBL_COMMON_USER." where  id=$search_text";
			$db->query($sql);
			$db->fetch();
			$rsl_code = $db->dt[code];
			$where .= " and flowin_code LIKE  '$rsl_code' ";
		}else{
			$where .= " and $search_type LIKE  '%$search_text%' ";
		}
	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  MID(replace(cu.date,'-',''),1,8) between  $startDate and $endDate ";
	}


	// 전체 갯수 불러오는 부분

	$db->query("SELECT count(*) as total FROM reseller_flowin_detail rfd, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd $where ");
	$db->fetch();
	$total = $db->dt[total];
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","view");

/*
	$sql = "SELECT cu.*, date_format(cu.date,'%Y.%m.%d') as regdate, UNIX_TIMESTAMP(last) AS last, sum(case when mr.state in (1,2,5,6,7) then reserve else 0 end) as reserve
	FROM  ".TBL_COMMON_USER." mm left outer join ".TBL_SHOP_RESERVE_INFO." mr on cu.code = mr.uid
	$where
	group by com_name, com_number, com_phone, com_fax, com_business_status, com_business_category, code, name, mail, visit, perm,gp_level, last
	ORDER BY date DESC LIMIT $start, $max";


*/
	$sql = "select rfd.flowin_code, cu.id, (SELECT id FROM ".TBL_COMMON_USER." cu2 WHERE cu2.code=rfd.rsl_code) AS rsl_id,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
			cu.visit, date_format(cu.date,'%Y.%m.%d') as regdate, rfd.flowin_url, rfd.flowin_type
			from reseller_flowin_detail rfd, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
			$where
			ORDER BY cu.date DESC
			LIMIT $start, $max";
	
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
	


		$mdb->query("SELECT IFNULL(sum(reserve),'-') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in (1,2,5,6,7)");
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[0]);
	*/


		if($db->dt[flowin_type]==1){
			$db->dt[flowin_type]='메일';
		}else{
			$db->dt[flowin_type]='베너';
		}


$Contents = $Contents."
  <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='pointer'\" onMouseOut=\"this.style.backgroundColor=''\">
    <td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[flowin_code]."'></td>
    <td class='list_box_td' onClick=\"PopSWindow('inflow_detail_view.php?code=".$db->dt[flowin_code]."',985,420,'inflow_detail_view')\">".$no."</td>
    <td class='list_box_td point' onClick=\"PopSWindow('inflow_detail_view.php?code=".$db->dt[flowin_code]."',985,420,'inflow_detail_view')\" nowrap>".$db->dt[name]."</td>
    <td class='list_box_td' onClick=\"PopSWindow('inflow_detail_view.php?code=".$db->dt[flowin_code]."',985,420,'inflow_detail_view')\">".$db->dt[id]."</td>
	<td class='list_box_td' onClick=\"PopSWindow('inflow_detail_view.php?code=".$db->dt[flowin_code]."',985,420,'inflow_detail_view')\">".$db->dt[regdate]."</td>
    <td class='list_box_td' onClick=\"PopSWindow('inflow_detail_view.php?code=".$db->dt[flowin_code]."',985,420,'inflow_detail_view')\">".$db->dt[mail]."</td>
    <td class='list_box_td' onClick=\"PopSWindow('inflow_detail_view.php?code=".$db->dt[flowin_code]."',985,420,'inflow_detail_view')\">".$db->dt[visit]."</td>
    <td class='list_box_td ctr point' onClick=\"PopSWindow('inflow_detail_view.php?code=".$db->dt[flowin_code]."',985,420,'inflow_detail_view')\">".$db->dt[rsl_id]."</td>
	 <td class='list_box_td' onClick=\"PopSWindow('inflow_detail_view.php?code=".$db->dt[flowin_code]."',985,420,'inflow_detail_view')\">".$db->dt[flowin_type].($db->dt[flowin_url] ? '['. $db->dt[flowin_url].']' : '')."</span></td>
  </tr>";

	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='10' align='center'>등록된 회원 데이타가 없습니다.</td>
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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >리셀러ID검색은 정확한 리셀러의ID를 입력하셔야 합니다.</td></tr>
</table>
";
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("유입회원상세리스트", $help_text,'70');


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = reseller_menu();
$P->Navigation = "리셀러관리 > 통계분석 > 유입회원상세리스트";
$P->title = "리셀러관리 > 유입회원상세리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>