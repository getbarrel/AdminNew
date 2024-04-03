<?
include("../class/layout.class");


$db = new Database;
$cdb = new Database;
/*
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

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
*/

$max = 20; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}
$where = " where ep.place_ix <> '0' ";

if($gift_change_state != ""){
	$where .= " and ep.gift_change_state = $gift_change_state ";
}

$gift_type = $_GET["gift_type"];
if(is_array($gift_type)){
	for($i=0;$i < count($gift_type);$i++){
		if($gift_type[$i] != ""){
			if($gift_type_str == ""){
				$gift_type_str .= "'".$gift_type[$i]."'";
			}else{
				$gift_type_str .= ",'".$gift_type[$i]."' ";
			}
		}
	}

	if($gift_type_str != ""){
		$where .= " AND ep.gift_type in (".$gift_type_str.") ";
	}
}else{
	if($gift_type){
		$where .= " AND ep.gift_type = '".$gift_type."' ";
	}else{
		$gift_type = array();
	}
}

if($search_type != "" && $search_text != ""){
	if($search_type == "ep.gift_code"){
		 $search_text = str_replace("-","",$search_text);
	}
	$where .= " and $search_type LIKE '%".trim($search_text)."%' ";
}

//$startDate = $FromYY.$FromMM.$FromDD;
//$endDate = $ToYY.$ToMM.$ToDD;

if($reg_sdate != "" && $reg_edate != ""){
	$where .= " and  date_format(ep.regdate, '%Y%m%d') between  '$reg_sdate' and '$reg_edate' ";
}

if($gift_start_date != "" && $gift_end_date != ""){
	$where .= " and  (ep.gift_start_date between  '$gift_start_date' and '$gift_end_date' or ep.gift_start_date between  '$gift_start_date' and '$gift_end_date' )";
}


$sql = "select * from shop_event_place ep 
			$where ";
//echo $sql ;

$db->query($sql);

$db->fetch();
$total = $db->total;


$sql = "select *
			from shop_event_place ep 
			$where
			order by place_ix desc
			LIMIT $start, $max";

//echo nl2br($sql);
$db->query($sql); //where place_ix = '$code'


$Script ="
<script language='JavaScript' >
/*
function BaymoneyReset(){
	var frm = document.forms['baymoney_list'];

	frm.reset();
	frm.act.value = 'baymoney_insert';
}
*/
function DeleteEventPlace(place_ix){
	if(confirm('상품권 정보를 정말로 삭제하시겠습니까?')){
		window.frames['iframe_act'].location.href='event_place.act.php?act=delete&place_ix='+place_ix;
	}
}
 
function clearAll(frm){
		for(i=0;i < frm.place_ix.length;i++){
				frm.place_ix[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.place_ix.length;i++){
				frm.place_ix[i].checked = true;
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
}


function CheckDelete(frm){
	if(confirm('선택하신 상품권을 정말로 삭제하시겠습니까? 삭제하신 적립은은 복원되지 않습니다')){
		for(i=0;i < frm.place_ix.length;i++){
			if(frm.place_ix[i].checked){
				return true
			}
		}
		alert('삭제하실 목록을 한개이상 선택하셔야 합니다.');
	}
	return false;

}

function SelectDelete(frm){
	frm.act.value = 'baymoney_select_delete';
	if(CheckDelete(frm)){
		frm.submit();
	}

}

function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		$('#reg_sdate').attr('disabled',false);
		$('#reg_edate').attr('disabled',false);
	}else{
		$('#reg_sdate').attr('disabled',true);
		$('#reg_edate').attr('disabled',true);
	}
}

function ChangeGiftDate(frm){
	if(frm.gift_use_date.checked){
		$('#gift_start_date').attr('disabled',false);
		$('#gift_end_date').attr('disabled',false);
	}else{
		$('#gift_start_date').attr('disabled',true);
		$('#gift_end_date').attr('disabled',true);
	}
}


function init(){

	//var frm = document.searchmember;
	//onLoad('$sDate','$eDate');

";

if($regdate != "1"){
$Script .="";
}


$Script .="
}
/*
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

	init_date(FromDate,ToDate);

}
*/

$(document).ready(function (){

		if($('#gift_use_date').attr('checked') == 'checked'){
			$('#gift_start_date').attr('disabled',false);
			$('#gift_end_date').attr('disabled',false);
		}else{
			$('#gift_start_date').removeClass('point_color').val('').attr('disabled',true);
			$('#gift_end_date').removeClass('point_color').val('').attr('disabled',true);
		}

		if($('#regdate').attr('checked') == 'checked'){
			$('#reg_sdate').attr('disabled',false);
			$('#reg_edate').attr('disabled',false);
		}else{
			$('#reg_sdate').removeClass('point_color').val('').attr('disabled',true);
			$('#reg_edate').removeClass('point_color').val('').attr('disabled',true);
		}

});

</Script>";
/*
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
*/
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("이벤트 플레이스 관리", "이벤트관리 > 이벤트 플레이스 관리 ")."</td>
	  </tr>
	  <tr>
			<td>
				<form name='searchmember' style='display:inline;'>
				<table width=100%  border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td align='left' colspan=2 width='100%' valign=top >
							<table cellpadding=0 cellspacing=1 width='100%' class='search_table_box'> 
								<tr>
									<th class='search_box_title' width='150'>조건검색 : </th>
									<td class='search_box_item' colspan=3>
									<table width=100% cellpadding=0 cellspacing=0>
										<col width='90px;'>
										<col width='*'>
										<tr>
											<td>
											<select name=search_type>
												<option value='' >검색항목</option>
												<option value='place_name' ".CompareReturnValue("place_name",$search_type,"selected").">플레이스명</option>
											</select>
											</td>
											<td>
												<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:50%' >
											</td>
										</tr>
									</table>
									</td>
								</tr>
								 
								<tr height=27>
									<td class='search_box_title' align=center><label for='regdate'><b>등록일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".($regdate == "1" ? "checked":"")."></td>
									<td class='search_box_item' colspan=3 style='padding-left:5px;'>
									".search_date('reg_sdate','reg_edate',$reg_sdate,$reg_edate,'N','D')."									
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr >
						<td colspan=3 align=center style='padding:10px 0px 0px 0px;'>
							<input type='image' src='../image/bt_search.gif' border=0>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>";

$Contents01 .= "
	  </table>";

$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >

	  <tr height=10>
	  <td colspan=2 style='padding-bottom:5px;'>생성된 플레이스 수 : ".$total." 개</td>
	  <td colspan=10  align=right>

	  </td></tr>
	</table>
	  <form name=baymoney_list method=post action='member.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
		<input type='hidden' name='act' value='baymoney_select_delete'>
		<input type='hidden' name='id' value=''>
		<input type='hidden' name='etc' value=''>
		<input type='hidden' name='baymoney' value=''>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <tr bgcolor=#efefef align=center height=32>
			<!--td class='s_td' width=3%><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.baymoney_list)'></td-->
			<td class=s_td width=3%>번호</td> 
			<td class='m_td' width=9%>플레이스명 </td>
			<td class='m_td' width=20%>주소 </td>
			<td class='m_td' width=5%>반경 </td>  
			<td class='m_td' width=7% >등록일자</td>
			<td class='e_td' width=7% >관리 </td>
		</tr>";



if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

	   
		$Contents02 .= "<tr height=28 align=center>
				<td class='list_box_td '>".$no."</td>
				<!--td bgcolor='#efefef'><input type=checkbox class=nonborder id='place_ix' name=place_ix[] value='".$db->dt[place_ix]."'></td-->
				 
				<td bgcolor='#ffffff' style='text-align:left;padding-left:10px;'>".$db->dt[place_name]."</td>
				<td bgcolor='#efefef' style='text-align:left;padding-left:10px;'>".$db->dt[place_addr1]." ".$db->dt[place_addr2]."</td>
				<td bgcolor='#ffffff'>".($db->dt[place_radius])."</td> 
				<td bgcolor='#ffffff'>".$db->dt[regdate]."</td>

				<td bgcolor='#ffffff' style='padding:3px 0px 0px 0px;'>"; 
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents02 .= "<a href='event_place.php?place_ix=".$db->dt[place_ix]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a> ";
				}else{
					$Contents02 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a> ";
				}
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$Contents02 .= "<a href=\"javascript:DeleteEventPlace('".$db->dt[place_ix]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border='0' ></a> ";
				}else{
					$Contents02 .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border='0' ></a> ";
				}
				$Contents02 .= "
					<!--a href=\"javascript:DeleteEventPlace('".$db->dt[place_ix]."')\"><img src='../image/btc_del.gif' border=0></a-->
				</td>
			</tr>";
	}
	$Contents02 .= "</form>";

}else{
		$Contents02 .= "
			<tr height=60><td colspan=6 align=center>상품권 내용이 없습니다.</td></tr>";

}
$Contents02 .= "</table>";

$Contents02 .= "<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >	 ";
//if(substr_count ($admininfo[permit], "06-16-01")){
	$Contents02 .= "<tr height=40><td colspan=8 align=left style='text-align:left;'>".page_bar($total, $page, $max,"&code=$code&gift_change_state=$gift_change_state&search_type=$search_type&search_text=".urlencode($search_text)."&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","")."</td><td colspan=2 align=right>
                ";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
    $Contents02 .= "
    <a href='event_place.php'><img  src='../images/btm_reg.gif' border=0 align=absmiddle ></a></td></tr>";
}else{
    $Contents02 .= "
    <a href=\"".$auth_write_msg."\"><img  src='../images/btm_reg.gif' border=0 align=absmiddle ></a></td></tr>"; 
}
//}

$Contents02 .= "</table>";



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>$Contents01<br></td></tr>";
/*
$Contents = $Contents."<form name='group_frm' action='group.act.php' method='post' onsubmit='return validate(document.edit_form)'>
<input name='act' type='hidden' value='insert'>
<input name='gp_ix' type='hidden' value=''>
<input name='basic' type='hidden' value=''>";

$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=30><td></td></tr>";
*/
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >오프라인 이벤트시 사용하는 플레이스 정보입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >오프라인 이벤트 생성시 해당 플레이스를 지정하여 이벤트를 진행하실 수 있습니다..</td></tr>

</table>
";


$Contents .= HelpBox("이벤트 플레이스 관리", $help_text);




$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = display_menu();
$P->Navigation = "프로모션(마케팅)전시 > 이벤트 플레이스 관리";
$P->title = "프로모션(마케팅)전시 > 이벤트 플레이스 관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table ".TBL_MALLSTORY_GROUPINFO." (
gp_ix int(4) unsigned not null auto_increment  ,
gp_name varchar(20) null default null,
gp_level int(2)  default '9' ,
sale_rate varchar(20) null default null,

disp char(1) default '1' ,
regdate datetime not null,
primary key(gp_ix));
*/
?>