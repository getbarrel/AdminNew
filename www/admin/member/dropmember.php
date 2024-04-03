<?
include("../class/layout.class");


$db = new Database;
$mdb= new Database;

$Script .="
<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view'></script>
<script language='JavaScript' >
function DeleteDropmember(code){
	if(confirm('정말로 삭제하시겠습니까?')){
		window.frames['iframe_act'].location.href='member.act.php?act=dropmember_delete&code='+code;
		//document.getElementById('iframe_act').src='member.act.php?act=dropmember_delete&code='+code;//kbk
	}
}

function SelectDelete(frm){
	frm.act.value = 'point_select_delete';
	if(CheckDelete(frm)){
		frm.submit();
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
	onLoad('$sDate','$eDate');";

$Script .="
}

function init_date(FromDate,ToDate) {
	var frm = document.searchmember;


	for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate.substring(8,10))
			frm.FromDD.options[i].selected=true
	}


	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate.substring(8,10))
			frm.ToDD.options[i].selected=true
	}
}



function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	if(dType == 1){
		for(i=0; i<frm.FromYY.length; i++) {
			if(frm.FromYY.options[i].value == FromDate.substring(0,4))
				frm.FromYY.options[i].selected=true
		}
		for(i=0; i<frm.FromMM.length; i++) {
			if(frm.FromMM.options[i].value == FromDate.substring(5,7))
				frm.FromMM.options[i].selected=true
		}
		for(i=0; i<frm.FromDD.length; i++) {
			if(frm.FromDD.options[i].value == FromDate.substring(8,10))
				frm.FromDD.options[i].selected=true
		}


		for(i=0; i<frm.ToYY.length; i++) {
			if(frm.ToYY.options[i].value == ToDate.substring(0,4))
				frm.ToYY.options[i].selected=true
		}
		for(i=0; i<frm.ToMM.length; i++) {
			if(frm.ToMM.options[i].value == ToDate.substring(5,7))
				frm.ToMM.options[i].selected=true
		}
		for(i=0; i<frm.ToDD.length; i++) {
			if(frm.ToDD.options[i].value == ToDate.substring(8,10))
				frm.ToDD.options[i].selected=true
		}
	}

}




function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);

	//init_date(FromDate,ToDate);

}
</Script>";

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("탈퇴회원관리", "회원관리 > 탈퇴회원관리 ")."</td>
	  </tr>
	</table>";

$sql = "select
			count(code) as dropmember_total,
			IFNULL(sum(case when (date_format(dropdate,'%Y%m%d') =  '".date("Ymd")."')  then 1 else 0 end),0) as drop_today,
			IFNULL(sum(case when ('".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."'  <=  date_format(dropdate,'%Y%m%d')
					and date_format(dropdate,'%Y%m%d') <= '".date("Ymd")."')  then 1 else 0 end),0) as drop_week,
			IFNULL(sum(case when (date_format(dropdate,'%Y%m') =  '".date("Ym")."')  then 1 else 0 end),0) as drop_thismonth,
			IFNULL(sum(case when (date_format(dropdate,'%Y%m') <=  '".date("Ym")."' and date_format(dropdate,'%Y%m%d') >= '".$v3monthago."')  then 1 else 0 end),0) as drop_threemonth
		from
			common_dropmember
		where
			1
		";

$mdb->query($sql);
$mdb->fetch();

$dropmember_total = $mdb->dt[dropmember_total];
$drop_today = $mdb->dt[drop_today];
$drop_week = $mdb->dt[drop_week];
$drop_thismonth = $mdb->dt[drop_thismonth];
$drop_threemonth = $mdb->dt[drop_threemonth];

if($dropmember_total > 0){
	$drop_today_rate = $drop_today / $dropmember_total * 100;
	$drop_week_rate = $drop_week / $dropmember_total * 100 ;
	$drop_thismonth_rate = $drop_thismonth / $dropmember_total * 100 ;
	$drop_threemonth_rate = $drop_threemonth / $dropmember_total * 100 ;
}else{
	$drop_today_rate = '0' ;
	$drop_week_rate = '0' ;
	$drop_thismonth_rate = '0' ;
	$drop_threemonth_rate = '0' ;
}

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
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
					<table id='tab_01' ".(($info_type == "list" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=list'>탈퇴회원리스트</a> <span style='padding-left:2px' class='helpcloud' help_width='410' help_height='70' help_html='사업자 정보를 작성하여 등록한 후에 해당 사업자에 관리자화면 로그인 정보를 발급해 줄 수 있습니다.<br />사업자 정보 추가 후 [목록관리] - [사용자 추가]를 통해 관리자에 계정을 발급해 주시기 바랍니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "add" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents02 .= "<a href='dropmember_setup.php?info_type=add'>탈퇴사유분류설정</a>";

						$Contents02 .= "
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
	  </table>
";

$Contents02 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
<tr>
	<td colspan=8>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 블랙 회원 인원</b> ".($company_id != "" ? "[ ".$db->dt[com_name]." : ".$company_id." ]" : "")." </div>")."</td>
		  </tr>
		</table>
	</td>
</tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
<tr height='28' bgcolor='#ffffff'>
    <td width='25%' align='center' class=s_td>오늘</td>
    <td width='25%' align='center' class='m_td'><font color='#000000'><b>최근1주일</b></font></td>
    <td width='25%' align='center' class='m_td'><font color='#000000'><b>최근1달</b></font></td>
	<td width='25%' align='center' class='m_td' nowrap><font color='#000000'><b>최근3달</b></font></td>
</tr>
<tr height='28'>
	<td class='list_box_td' >".$drop_today."(".round($drop_today_rate,2)."%)</td>
	<td class='list_box_td'>".$drop_week."(".round($drop_week_rate,2)."%)</td>
	<td class='list_box_td'>".$drop_thismonth."(".round($drop_thismonth_rate,2)."%)</td>
	<td class='list_box_td'>".$drop_threemonth."(".round($drop_threemonth_rate,2)."%)</td>
</tr>
</table>
<br><br>
";


$Contents02 .= "

	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td colspan=8>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 탈퇴회원 검색</b> ".($company_id != "" ? "[ ".$db->dt[com_name]." : ".$company_id." ]" : "")." </div>")."</td>
			  </tr>
			</table>
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
		<td colspan=7 style='padding-bottom:10px;'>
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top >
					<form name=searchmember method='get'><!--SubmitX(this);'-->
					<table width='100%' border='0' cellspacing='0' cellpadding='0' class='search_table_box'>
						";

if($_SESSION["admin_config"][front_multiview] == "Y"){
    $Contents02 .= "
                        <tr>
                            <td class='search_box_title' > 글로벌 회원 구분</td>
                            <td class='search_box_item' >".GetDisplayDivision($mall_ix, "select")." </td>
                        </tr>";
}
$Contents02 .= "
						<tr height=30>
							<td width='15%' class='search_box_title' >이름</td>
							<td class='search_box_item'>
								<input type='text' class='textbox point_color' name='search_name' value='$search_name'>
								<!--<input type='image' src='../images/".$admininfo["language"]."/btn_search.gif' align='absmiddle'>-->
							</td>
						</tr>
						<tr height=27>
							<td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>탈퇴일</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);'".CompareReturnValue("1",$regdate,"checked")."></td>
							<td class='search_box_item' align=left style='padding-left:5px;'>
							".search_date('sdropdate','edropdate',$sdropdate,$edropdate)."
							</td>
						</tr>
						</table>
						</td>
					</tr>
				</table>
				<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
				<tr height=50>
					<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
				</tr>
				</table>
				</form>
				</td>
				<th class='box_06'></th>
			</tr>
			<tr>
				<th class='box_07'></th>
				<td class='box_08'></td>
				<th class='box_09'></th>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<tr bgcolor=#efefef align=center height=28>
		<td class='s_td' width=5%><font color='#000000'><b>번호</b></font></td>
		<td class='m_td' width=10%><font color='#000000'><b>이름</b></font></td>
		<td class='m_td' width=20%><font color='#000000'><b>이메일</b></font></td>
		<td class='m_td' width=15%><font color='#000000'><b>탈퇴사유</b></font></td>
		<td class='m_td' width=20%><font color='#000000'><b>남긴말</b></font></td>
		<td class='m_td' width=15% ><font color='#000000'><b>탈퇴일</b></font></td>
		<td class='e_td' width=15% ><font color='#000000'><b>관리</b></font></td>
	</tr>";

if($max==""){
$max = 20; //페이지당 갯수
}
if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}
$where=" ";

if($search_name!="") {
	$where.="  and name like '%$search_name%' ";
}

$startDate = $sdropdate;
$endDate = $edropdate;

if($regdate == '1'){	//가입일자
	if($startDate != "" && $endDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(dropdate_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			$count_where .= " and  to_char(dropdate_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
		}else{
			$where .= " and date_format(dropdate,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			$count_where .= " and date_format(dropdate,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
		}
	}
}

if($mall_ix){
    $where .=" and mall_ix = '".$mall_ix."' ";
}

//탈퇴시 member 테이블에서 회원데이터 삭제를 위해 아래쿼리 수정
$db->query("select count(*) as total from common_dropmember as cd where 1 $where");
$db->fetch();
$total = $db->dt[total];

//탈퇴시 member테이블에서는 회원데이터 삭제함으로 ... 아래쿼리 수정
$sql = "select 
			cd.*,
			cds.dp_name
		from
			common_dropmember as cd
			left join common_dropmember_setup as cds on (cds.drop_ix = cd.drop_ix)
		where 1
		$where order by dropdate desc limit $start, $max";
$db->query($sql);

if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		$Contents02 .= "<tr height=30 align=center>
				<td class='list_box_td' bgcolor='#fbfbfb'>".$no."</td>
				<td class='list_box_td point' >".wel_masking_seLen($db->dt[name], 1, 1)."</td>
				<td class='list_box_td' >".wel_masking("E", $db->dt[email])."</td>
				<td class='list_box_td'>".$db->dt[dp_name]."</td>
				<td class='list_box_td list_bg_gray' style='padding:10px;text-align:left;' >".$db->dt[message]."</td>
				<td class='list_box_td'>".$db->dt[dropdate]."</td>
				<td class='list_box_td' >";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                    $Contents02.="
					<a href=\"javascript:DeleteDropmember('".$db->dt[code]."')\">
						<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 title='삭제'>
					</a>";
                }else{
                    $Contents02.="
					<a href=\"".$auth_delete_msg."\">
						<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 title='삭제'>
					</a>";
                }
                $Contents02.="
					<a href=\"javascript:PopSWindow('dropmember_view.php?code=".$db->dt[code]."',500,300,'member_info')\">
						<img src='../images/".$admininfo["language"]."/btn_dropmember.gif' border=0>
					</a>
				</td>
			</tr>";
	}
}else{
		$Contents02 .= "
			<tr height=60><td colspan=7 align=center>탈퇴 내역이 없습니다.</td></tr>
			";
}


$Contents02 .= "</table>";
$Contents02 .= "<table width='100%' border='0' cellpadding='0' cellspacing='0'>";
$Contents02 .= "<tr height=40><td colspan=7 align=right>".page_bar($total, $page, $max,"&code=$code","")."</td></tr>";
$Contents02 .= "</table>";



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>$Contents01<br></td></tr>";

$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >탈퇴한 고객들이 작성한 내역입니다. </td></tr>
</table>
";
*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("탈퇴회원관리", $help_text);
$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";	//init();
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 탈퇴회원관리";
$P->title = "탈퇴회원관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



//	웰숲 우클릭 방지
include_once("../order/wel_drag.php");
?>