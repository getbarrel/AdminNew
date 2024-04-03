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

$Script = "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'inflow_detail.php?view='+sort;
}
	
 function common_reseller(act, rq_ix){
	if(confirm('회원을 탈퇴 하시겠습니까?')){
		document.location.href = 'request.act.php?act='+act+'&rq_ix='+rq_ix;
	}
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
$Script .= "
}

</script>";

$Contents = "


<script language='javascript' src='inflow_detail.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("리셀러탈퇴신청관리", "리셀러관리 >  회원관리 > 리셀러탈퇴신청관리")."</td>
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
									<option value='name' ".CompareReturnValue("name",$search_type,"selected").">이름</option>
									<option value='mail' ".CompareReturnValue("mail",$search_type,"selected").">이메일</option>
									<option value='tel' ".CompareReturnValue("tel",$search_type,"selected").">전화번호</option>
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
		      <td class='search_box_title' ><label for='regdate'>리셀러 탈퇴신청일</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
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
			<tr height=30>
			  <td class='search_box_title' > 리셀러 관리</td>
				  <td class='search_box_item'  colspan='3'>
				   <input type=radio name='rsl_serch' value='A' id='rsl_serch_a'  ".CompareReturnValue("A",$rsl_serch,"checked")." checked>
				   <label for='rsl_serch_a'>전체</label>
				   <input type=radio name='rsl_serch' value='4' id='rsl_serch_y'  ".CompareReturnValue("4",$rsl_serch,"checked").">
				   <label for='rsl_serch_y'>탈퇴완료</label>
				   <input type=radio name='rsl_serch' value='3' id='rsl_serch_n' ".CompareReturnValue("3",$rsl_serch,"checked").">
				   <label for='rsl_serch_n'>탈퇴대기</label>
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
<form name='list_frm' act='act'>
<input type='hidden' name='code[]' id='code'>
<table width='100%' border='0' cellpadding='0' cellspacing='0'class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'  align='center' >
    <!--td width='5%' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td-->
	<td width='5%' class='m_td'><font color='#000000'><b>순서</b></font></td>
    <td width='7%' class='m_td'><font color='#000000'><b>이름</b></font></td>
    <td width='15%' class='m_td'><font color='#000000'><b>이메일</b></font></td>
    <td width='10%' class=m_td><font color='#000000'><b>전화번호</b></font></td>
    <td width='*' class=m_td><font color='#000000' ><b>남긴 말</b></font></td>
    <td width='20%' class=m_td><font color='#000000'><b>신청날짜</b></font></td>
    <td width='10%' class=m_td><font color='#000000'><b>리셀러 관리</b></font></td>
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



	$where = " where code != '' and state in ('3','4')";
	
	
	if($search_type != "" && $search_text != ""){
		
			$where .= " and $search_type LIKE  '%$search_text%' ";
	}


	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  MID(replace(regdate,'-',''),1,8) between  $startDate and $endDate ";
	}

	if($rsl_serch != "A" && $rsl_serch != ""){
		$where .= "and state =  '$rsl_serch' ";
	}

	// 전체 갯수 불러오는 부분

	$db->query("select * from reseller_request $where");
	$db->fetch();
	$total = $db->total;
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","view");


	$sql = "select * from reseller_request $where ORDER BY regdate DESC	LIMIT $start, $max";
	
	$db->query($sql);
	

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;

		if($db->dt[state]==3){
			$btn="<img src='../images/".$admininfo["language"]."/btn_drop.gif' border=0 align=absmiddle onClick=\"common_reseller('reseller_drop','".$db->dt[rq_ix]."')\" onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='pointer'\" />";
		}elseif($db->dt[state]==4){
			$btn="<span style='color:red'>탈퇴완료</span>";
		}



$Contents = $Contents."
  <tr height='28'>
    <!--td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td-->
    <td class='list_box_td'>".$no."</td>
    <td class='list_box_td point' >".$db->dt[name]."</td>
    <td class='list_box_td' >".$db->dt[email]."</td>
	<td class='list_box_td' >".$db->dt[tel]."</td>
    <td class='list_box_td' style='text-align:left;padding-left:20px;' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='pointer'\" onMouseOut=\"this.style.backgroundColor=''\" onClick=\"PopSWindow('request_view.php?rq_ix=".$db->dt[rq_ix]."',700,420,'request_view')\" >".cut_str($db->dt[content],60)."</td>
    <td class='list_box_td' >".$db->dt[regdate]."</td>
    <td class='list_box_td ctr point' onMouseOver=\"this.style.backgroundColor='#E8ECF1';\" onMouseOut=\"this.style.backgroundColor=''\"  >".$btn."</td>
  </tr>";

	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='7' align='center'>신청한 회원 데이타가 없습니다.</td>
  </tr>";
}



$Contents .= "
	</form>
 
</table>
<div style='width:100%;text-align:right;padding:10px 0px;'>".$str_page_bar."</div>";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >리셀러ID검색은 정확한 리셀러의ID를 입력하셔야 합니다.</td></tr>
</table>
";
*/
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("리셀러탈퇴신청관리", $help_text,'70');


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = reseller_menu();
$P->Navigation = "리셀러관리 > 회원관리 > 리셀러탈퇴신청관리";
$P->title = "리셀러탈퇴신청관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>