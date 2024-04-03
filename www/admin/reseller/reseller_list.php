<?
include("../class/layout.class");

//[S] 리셀러 데이터 로드
@include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
$reseller_data = resellerShared("select");
//[E] 리셀러 데이터 로드

$db = new Database;
$mdb = new Database;

$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

if ($FromYY == ""){
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY."-".$FromMM."-".$FromDD;
	$endDate = $ToYY."-".$ToMM."-".$ToDD;
}

$Script = "
<script language='javascript'>

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

<script language='javascript' src='/admin/reseller/reseller.js'></script>
<script language='javascript' src='inflow_detail.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("리셀러 회원관리", "리셀러관리 >  회원관리 > 리셀러 회원관리")."</td>
  </tr>

  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='request.php'>전체회원</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' class='on'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "리셀러 회원";

						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

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
            <input type='hidden' name=mc_ix value='".$mc_ix." '>
		    <col width='12%'>
			<col width='*'>
		    <tr height=27>
		      <td class='search_box_title' >조건검색 </td>
		      <td class='search_box_item' colspan='3'>
				<table cellpadding=0 cellspacing=0 width=500>
					<col width='100'>
					<col width='*'>
					<tr>
						<td>
						  <select name=search_type style='width:95%;'>
							<option value='name' ".CompareReturnValue("name",$search_type,"selected").">이름</option>
							<option value='id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
							<!--option value='manager' ".CompareReturnValue("manager",$search_type,"selected").">Manager</option-->
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
		      <td class='search_box_title' ><label for='regdate'>리셀러 신청일</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
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
				  <td class='search_box_item' colspan='3'>
				    <label><input type='radio' name='rsl_div' value='' checked>전체</label>
					<label><input type='radio' name='rsl_div' value='R' ".CompareReturnValue("R",$rsl_div,"checked").">Reseller</label>
					<label><input type='radio' name='rsl_div' value='M' ".CompareReturnValue("M",$rsl_div,"checked").">Manager</label>
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

<form name='list_frm' action='reseller.act.php' onsubmit='return costChange($(this))'>
<input type='hidden' name='search_type' value='".$search_type."'/>
<input type='hidden' name='search_text' value='".$search_text."'/>
<input type='hidden' name='FromYY' value='".$FromYY."'/>
<input type='hidden' name='FromMM' value='".$FromMM."'/>
<input type='hidden' name='FromDD' value='".$FromDD."'/>
<input type='hidden' name='ToYY' value='".$ToYY."'/>
<input type='hidden' name='ToMM' value='".$ToMM."'/>
<input type='hidden' name='ToDD' value='".$ToDD."'/>
<input type='hidden' name='rsl_div' value='".$rsl_div."'/>
<input type='hidden' name='act' value='costChange'/>

<table width='100%' border='0' cellpadding='0' cellspacing='0'class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'  align='center' >
    <td width='5%' rowspan='2' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
	<td width='5%' rowspan='2' class='m_td'><font color='#000000'><b>순서</b></font></td>
    <td width='7%' rowspan='2' class='m_td'><font color='#000000'><b>구분</b></font></td>
    <td width='10%' rowspan='2' class='m_td'><font color='#000000'><b>이름</b></font></td>
    <td width='10%' rowspan='2' class=m_td><font color='#000000'><b>ID</b></font></td>
    <td width='10%' rowspan='2' class=m_td><font color='#000000' ><b>Manager</b></font></td>
    <td width='14%' rowspan='2' class=m_td><font color='#000000'><b>가입일</b></font></td>
    <td colspan='2' class=m_td><font color='#000000'><b>수수료(%)</b></font></td>
	<td width='*' rowspan='2' class=m_td><font color='#000000'><b>계좌정보</b></font></td>
	<td width='6%' rowspan='2' class=m_td><font color='#000000'><b>관리</b></font></td>
  </tr>
  <tr height='28' bgcolor='#ffffff'  align='center' >
    <td width='8%' class=m_td><font color='#000000'><b>Manager</b></font></td>
	<td width='8%' class=m_td><font color='#000000'><b>Reseller</b></font></td>
  </tr>";

    include "reseller_query.php";

	//[S] 페이징
	$str_page_bar = page_bar($total, $page,$max, "&search_type=".$search_type."&search_text=".$search_text."&FromYY=".$FromYY."&FromMM=".$FromMM."&FromDD=".$FromDD."&ToYY=".$ToYY."&ToMM=".$ToMM."&ToDD=".$ToDD."&rsl_div=".$rsl_div,"view");
	//[E] 페이징

	$sql .= " ORDER BY regdate DESC LIMIT ".$start." , ".$max;

	$db->query($sql);

	if($total > 0){

		for($i=0; $i<$db->total; $i++){

			$db->fetch($i);
			$no = $total - ($page - 1) * $max - $i;

			if($db->dt[rsl_div] == "R"){
				//리셀러 회원일때
				$rsl_div = "Reseller";

				//[S] 매니저 이름
				$sql = "SELECT rsl_code, incentive_rate FROM reseller_policy WHERE rsl_ix = '".$db->dt[rsl_manager]."' LIMIT 1";
				$mdb->query($sql);
				$mdb->fetch();
				$mcode = $mdb->dt[rsl_code];
				$managerCost = number_format($reseller_data["incentive_rate"] - $db->dt[incentive_rate])."%";
				$resellerCost = number_format($db->dt[incentive_rate])."%";

				$sql = "SELECT AES_DECRYPT(UNHEX(name),'".$mdb->ase_encrypt_key."') as name FROM common_member_detail WHERE code = '".$mcode."' LIMIT 1";
				$mdb->query($sql);
				$mdb->fetch();
				$manager = $mdb->dt[name];
				//[E] 매니저 이름

			}else{
				//매니저 회원일때
				$rsl_div = "Manager";
				$manager = "-";
				$managerCost = number_format($db->dt[incentive_rate])."%";
				$resellerCost = "-";
			}

			//[S] 계좌정보
			$sql = "SELECT bank_name, bank_owner, bank_number FROM reseller_bank WHERE rsl_code = '".$db->dt[rsl_code]."' LIMIT 1";
			$mdb->query($sql);
			$mdb->fetch();
			//[E] 계좌정보
		
			$Contents.= "
			
				<tr height='28'>
					<td class='list_box_td'><input type=checkbox name=rsl_ix[] id='code' value='".$db->dt[rsl_ix]."'></td>
					<td class='list_box_td'>".$no."</td>
					<td class='list_box_td point' >".$rsl_div."</td>
					<td class='list_box_td' >".$db->dt[name]."</td>
					<td class='list_box_td' >".$db->dt[id]."</td>
					<td class='list_box_td' >".$manager."</td>
					<td class='list_box_td' >".$db->dt[regdate]."</td>
					<td class='list_box_td' >".$managerCost."</td>
					<td class='list_box_td' >".$resellerCost."</td>
					<td class='list_box_td' style='padding:3px 0;line-height:130%;'>".$mdb->dt[bank_number]."<br/>".$mdb->dt[bank_name]." ".($mdb->dt[bank_owner] ? "(".$mdb->dt[bank_owner].")" : "")."</td>
					<td class='list_box_td ctr point' onMouseOver=\"this.style.backgroundColor='#E8ECF1';\" onMouseOut=\"this.style.backgroundColor=''\"  ><img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$db->dt[rsl_code]."&mmode=pop',1280,800,'member_view')\" style='cursor:pointer;' alt='고객상담' title='고객상담'/></td>
				</tr>
			
			";
		
		}
	}else{

		$Contents = $Contents."
		  <tr height=50>
			<td colspan='11' align='center'>신청한 회원 데이타가 없습니다.</td>
		  </tr>";
	}

$Contents .= " 
</table>
<div style='width:100%;text-align:right;padding:10px 0px;'>".$str_page_bar."</div>
";

$Contents .= "
	<table width='100%' border='0'>
		<tbody>
			<tr>
				<td colspan='3' nowrap=''>
					<div style='z-index:0;vertical-align:bottom;position:relative;top:12px;left:10px;width:165px;height:15px;font-weight:bold;padding:0px 10px 0px 55px;background-color:#ffffff' class='help_title' nowrap=''> 
						<nobr>
							<select name='update_type' style='width:150px;'>
								<option value='1'>검색한회원</option>
								<option value='2'>선택한회원</option>
							</select>
						</nobr>
					</div>
				</td>
			</tr>
			<tr height='2'><td class='help_col' colspan='3'></td></tr>
			<tr height='60'>
				<td width='1' class='help_row1'></td>
				<td class='top p10 lh160'>
					<div style='padding:12px 0 4px;'>
						<img src='../images/dot_org.gif'> <b>수수료 변경</b> <span class='small' style='color:gray'>변경하고자 하는 리셀러 구분을 선택하시고 수수료율을 입력해주세요.</span>
					</div>
					<div id='help_text_level0'>
						<table cellpadding='3' cellspacing='0' width='100%' class='input_table_box'>
							<colgroup><col width='170'>
								<col width='*'>
							</colgroup>
							<tbody>
								<tr id='ht_level0_reason' class='ht_level0_reason_EY ht_level0_reason_EF ht_level0_reason_EM' style='display: table-row;'>
									<td class='input_box_title'> <b>수수료율</b></td>
									<td class='input_box_item'>
										<!--select name='level0_reason_code' class='level0_select_reason_EY' style='width:80px;font-size: 12px; display: inline-block;'>
											<option value='R'>리셀러</option>
											<option value='M'>매니저</option>
										</select-->
										<input type='text' name='incentive_rate' class='textbox' onkeyup='incentiveTotal($(this).val(), ".$reseller_data["incentive_rate"].");' title='수수료율' style='width:35px;position:relative;left:2px;top:-2px;'> %
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<table cellpadding='3' cellspacing='0' width='100%' style='border:0px solid silver;'>
						<tbody>
							<tr height='50'>
								<td colspan='4' align='center'>
									<input type='image' src='../images/korea/b_save.gif' border='0' style='cursor:pointer;border:0px;'>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td width='1' class='help_row2'></td>
			</tr>
		</tbody>
	</table>
</form>
";

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

$Contents .= HelpBox("리셀러 회원관리", $help_text,'70');


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = reseller_menu();
$P->Navigation = "리셀러관리 > 회원관리 > 리셀러 회원관리";
$P->title = "리셀러 회원관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>