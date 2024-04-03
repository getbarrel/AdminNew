<?
include("../class/layout.class");


$db = new Database;
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
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


if(empty($max)){
	$max = 50; //페이지당 갯수
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
$where = " where gcd.gc_ix <> '0' and gcd.gc_ix = '".$gc_ix."' and gc.gc_ix = gcd.gc_ix  ";

if($gift_change_state != ""){
	$where .= " and gcd.gift_change_state = $gift_change_state ";
}

if($search_text != ""){
	if($search_type != ""){
		if($search_type == "gcd.gift_code"){
			 $search_text = str_replace("-","",$search_text);
			 $where .= " and $search_type LIKE '%".trim($search_text)."%' ";

		}else if($search_type == "m.name"){
			$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else{
			$where .= " and $search_type LIKE '%".trim($search_text)."%' ";
		}
	}else{
		$where .= " and (gcd.gift_code LIKE '%".str_replace("-","",trim($search_text))."%' or AES_DECRYPT(UNHEX(m.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' or cu.id LIKE '%$search_text%') ";
	}
}

$startDate = $FromYY.$FromMM.$FromDD;
$endDate = $ToYY.$ToMM.$ToDD;

if($startDate != "" && $endDate != ""){
	$where .= " and  gcd.use_date between $startDate and $endDate ";
}

$sql = "select 
			gc.gc_ix 
		from 
			shop_gift_certificate_detail gcd 
			left join common_user as cu on (gcd.member_id = cu.id)
			left join common_member_detail m on cu.code = m.code
			,shop_gift_certificate gc where gcd.gc_ix <> '0' and gcd.gc_ix = '".$gc_ix."' and gc.gc_ix = gcd.gc_ix ";
$db->query($sql);
$real_total = $db->total;

$sql = "select 
			gc.gc_ix 
		from 
			shop_gift_certificate_detail gcd 
			left join common_user as cu on (gcd.member_id = cu.id)
			left join common_member_detail m on cu.code = m.code
			,shop_gift_certificate gc
		$where ";
$db->query($sql);

$db->fetch();
$total = $db->total;

if($mode == "excel"){
//	header( "Content-type: application/vnd.ms-excel" );
//	header( "Content-Disposition: attachment; filename=giftcertificate_list.xls" );
//	header( "Content-Description: Generated Data" );

    header( "Content-type: application/vnd.ms-excel; charset=utf-8" );
    header( "Content-Disposition: attachment; filename=giftcertificate_list.xls" );
    header( "Content-Description: PHP4 Generated Data" );
    print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");

	$sql = "select 
				gcd.* ,
				gc.gift_start_date, 
				gc.gift_end_date,
				gc.regdate,
				gc.gift_amount,
				m.code, 
				IFNULL(AES_DECRYPT(UNHEX(m.name),'".$db->ase_encrypt_key."'),'-') as name
			from 
				shop_gift_certificate_detail gcd
				left join common_user as cu on (gcd.member_id = cu.id)
				left join common_member_detail m on cu.code = m.code
				,shop_gift_certificate gc
			
			$where
				order by gc_ix desc  ";

	//echo $sql;
	$db->query($sql); //where gc_ix = '$code'

	$mstring = "<table border=1>";
	$mstring .= "<tr><td>번호</td><td>시리얼</td><td>사용유효기간</td><td>상태</td><td>사용회원명</td><td>등록ID</td><td>사용일자</td></tr>";
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);

		if($db->dt[gift_type]=="G"){
			$gift_type_str = "구매상품권";
		}else if($db->dt[gift_type]=="M"){
			$gift_type_str = "정회원 상품권";
		}else{
			$gift_type_str = "";
		}

        if($db->dt[gift_change_state]==0){
            $gift_change_state_str = "사용전";
        }else if($db->dt[gift_change_state]==1){
            $gift_change_state_str = "사용완료";
        }else{
            $gift_change_state_str = "";
        }

		if ($db->dt[event_gift]=="G") {
		  $event_gift = "상품권";
		} else {
		  $event_gift = "이벤트";
		}
		if ($db->dt[chagne_request_date] == "0000-00-00 00:00:00") $db->dt[chagne_request_date]="";

		$gift_code_str = substr($db->dt[gift_code],0,4)."-".substr($db->dt[gift_code],4,4)."-".substr($db->dt[gift_code],8,4)."-".substr($db->dt[gift_code],12,4);


		$mstring .= "<tr><td>".($i+1)."</td>
			<td>".$gift_code_str."</td>			
			<td>".$db->dt[gift_start_date]."~".$db->dt[gift_end_date]."</td>
			<td>".$gift_change_state_str."</td>
			<td>".$db->dt[name]."</td>
			<td>".$db->dt[member_id]."</td>
			<td>".($db->dt[member_id] ? $db->dt[use_date]:"-")."</td>
			</tr>";
			//<td>".$db->dt[reg_member_id]."</td><td>".$db->dt[reg_ip]."</td>
	}
	$mstring .= "<table>";

	echo $mstring;
	//echo iconv("utf-8","CP949",$mstring);
	exit;
}else{

	$sql = "select 
				gcd.* ,
				gc.gift_start_date, 
				gc.gift_end_date,
				gc.regdate,
				m.code,
				IFNULL(AES_DECRYPT(UNHEX(m.name),'".$db->ase_encrypt_key."'),'-') as name
			from 
				shop_gift_certificate_detail gcd 
				left join common_user as cu on (gcd.member_id = cu.id)
				left join common_member_detail m on cu.code = m.code
				,shop_gift_certificate gc
				
			$where
				order by gc_ix desc
				LIMIT $start, $max";
	$db->query($sql); //where gc_ix = '$code'
}

$Script ="
<script language='JavaScript' >
function BaymoneyReset(){
	var frm = document.forms['baymoney_list'];

	frm.reset();
	frm.act.value = 'baymoney_insert';
}

function DeleteGiftCertificateDetail(gcd_ix){
	if(confirm('상품권 정보를 정말로 삭제하시겠습니까?')){
		window.frames['iframe_act'].location.href='giftcertificate.act.php?act=detail_delete&gcd_ix='+gcd_ix;
	}
}

function UpdateBaymoney(id, etc, baymoney, gift_change_state){
	var frm = document.forms['baymoney_list'];

	frm.id.value = id;
	frm.etc.value = etc;
	frm.baymoney.value = baymoney;

	//frm.gift_change_state[frm.gift_change_state.selectedIndex].selected = true;
	for(i=0;i<frm.gift_change_state.length;i++){
		if(frm.gift_change_state[i].value == gift_change_state){
			frm.gift_change_state[i].selected = true;
		}
	}
	frm.act.value = 'baymoney_update';
}

function CheckBaymoney(frm){
	if(frm.etc.value.length < 1){
		alert('적립내용을 입력해주세요');
		//frm.etc.focus();
		return false;
	}

	if(frm.baymoney.value.length < 1){
		alert('마일리지를 입력해주세요');
		//frm.baymoney.focus();
		return false;
	}

	return true;
}


function clearAll(frm){
		for(i=0;i < frm.giftcertificate_id.length;i++){
				frm.giftcertificate_id[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.giftcertificate_id.length;i++){
				frm.giftcertificate_id[i].checked = true;
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
		for(i=0;i < frm.giftcertificate_id.length;i++){
			if(frm.giftcertificate_id[i].checked){
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
	onLoad('$sDate','$eDate');

	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;


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

	init_date(FromDate,ToDate);

}

function changeMax(obj){
	var max = $(obj).val();
	$('form[name=searchmember]').find('input[name=max]').val(max);
	$('form[name=searchmember]').submit();
}

function submitToChange(){
	var update_type = $('#update_type').val();

	if(confirm('상품권을 삭제하시겠습니까?\\n(삭제시 주의사항을 다시 한 번 확인해주세요)')){
		$('input[name=update_type]').val(update_type);
		$('form[name=list_frm]').submit();
	}
}
</Script>";
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
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("오프라인 상품권 발행 내역", "마케팅관리 > 오프라인 상품권 발행 내역")."</td>
	  </tr>
	  <tr>
			<td>
				<form name='searchmember' style='display:inline;'>
				<input type='hidden' name='gc_ix' value='".$gc_ix."' />
				<input type='hidden' name='max' value='".$max."' />
				<table width=100%  border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td align='left' colspan=2 width='100%' valign=top >
							<table cellpadding=0 cellspacing=1 width='100%' class='search_table_box'>
								<tr>
									<th class='search_box_title' width='150'>조건검색 : </th>
									<td class='search_box_item' colspan=3>
									<select name=search_type>
										<option value=''>통합검색</option>
										<option value='m.name' ".CompareReturnValue("m.name",$search_type,"selected").">사용자 이름</option>
										<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">사용자 ID</option>
										<option value='gcd.gift_code' ".CompareReturnValue("gcd.gift_code",$search_type,"selected").">시리얼넘버</option>
									</select>
									<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:50%' >
									</td>
								</tr>
								<tr>
									<th class='search_box_title' width='150'>상태 : </th>
									<td class='search_box_item' colspan=3>
										<input type='radio' name='gift_change_state' id='gift_change_state_' class='gift_change_state' value='' ".($gift_change_state == "" ? "checked":"")."> <label for='gift_change_state_' >전체</label>
										<input type='radio' name='gift_change_state' id='gift_change_state_0' class='gift_change_state' value='0' ".($gift_change_state == "0" ? "checked":"")."> <label for='gift_change_state_0' >사용전</label>
										<input type='radio' name='gift_change_state' id='gift_change_state_1' class='gift_change_state' value='1' ".($gift_change_state == "1" ? "checked":"")."> <label for='gift_change_state_1' >사용완료</label>
									</td>
								</tr>
								 <tr height=27>
								  <td class='search_box_title' align=center>
									<label for='regdate'><b>사용일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);'>
								  </td>
								  <td class='search_box_item' colspan=3 style='padding-left:3px;'>
									<table cellpadding=0 cellspacing=2 border=0 width='100%'  bgcolor=#ffffff>
									<tr>
										<TD width=220 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
										<TD width=20 align=center> ~ </TD>
										<TD width=220 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
										<TD>
											<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../image/b_btn_s_1week01.gif'></a>
											<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../image/b_btn_s_15day01.gif'></a>
											<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../image/b_btn_s_1month01.gif'></a>
											<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../image/b_btn_s_2month01.gif'></a>
											<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../image/b_btn_s_3month01.gif'></a>
										</TD>
									</tr>
								</table>
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
	  <td colspan=2>검색 결과 : ".$total." 건 / 전체 : ".$real_total." 건</td>
	  <td colspan=10 align=right style='padding-bottom:5px;'>";      
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
    $Contents02 .="    
	  <a href='?mode=excel&".$QUERY_STRING."'><img src='../image/btn_excel_save.gif' border=0></a>";
}else{
    $Contents02 .="    
      <a href=\"".$auth_excel_msg."\"><img src='../image/btn_excel_save.gif' border=0></a>";
}
$Contents02 .= "   
		<select id='max' onchange='changeMax(this);'>
			<option value='10' ".($max == 10 ? "selected" : "").">10개씩 보기</option>
			<option value='50' ".($max == 50 ? "selected" : "").">50개씩 보기</option>
		</select>
	  </td></tr>
	</table>
	<form id='list_form' name='list_frm' method='POST' action='giftcertificate.act.php' enctype='multipart/form-data'>
	<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
	<input type='hidden' name='act' value='delete_detail_selected'>
	<input type='hidden' name='update_type' value=''>
	<input type='hidden' name='gc_ix' value='".$gc_ix."'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <tr bgcolor=#efefef align=center height=28>
			<td class='s_td' width=3%><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.baymoney_list)'></td>
			<td class='m_td' width=18%>시리얼 넘버 </td>
			<td class='m_td' width=15%>사용가능기간</td>
			<td class='m_td' width=8% >상태</td>
			<td class='m_td' width=10% >사용 회원명</td>
			<td class='m_td' width=10% >사용자 ID</td>
			<td class='m_td' width=13% >사용일자</td>
			<td class='e_td' width=7% >관리 </td>
		</tr>";

if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		if($db->dt[gift_change_state]==0){
			$gift_change_state_str = "사용전";
		}else if($db->dt[gift_change_state]==1){
			$gift_change_state_str = "사용완료";
		}else{
			$gift_change_state_str = "";
		}

		$gift_code_str = substr($db->dt[gift_code],0,4)."-".substr($db->dt[gift_code],4,4)."-".substr($db->dt[gift_code],8,4)."-".substr($db->dt[gift_code],12,4);

		$Contents02 .= "<tr height=28 align=center>
				<td bgcolor='#ffffff'><input type=checkbox class=nonborder id='giftcertificate_id' name=ix[] value='".$db->dt[gcd_ix]."'></td>
				<td bgcolor='#efefef'>".$gift_code_str."</td>
				<td bgcolor='#ffffff'>".$db->dt[gift_start_date]." ~ ".$db->dt[gift_end_date]."</td>
				<td bgcolor='#efefef'>".$gift_change_state_str."</td>
				<td bgcolor='#efefef'>".$db->dt[name]."</td>
				<td bgcolor='#ffffff'>".$db->dt[member_id]."</td>
				<td bgcolor='#efefef'>".($db->dt[member_id] ? $db->dt[use_date]:"-")."</td>";
        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
            $Contents02.="
				<td bgcolor='#ffffff'><a href=\"javascript:DeleteGiftCertificateDetail('".$db->dt[gcd_ix]."')\"><img src='../image/btc_del.gif' border=0></a></td>
                ";
        }else{
            $Contents02.="
				<td bgcolor='#ffffff'><a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0></a></td>
                ";
                }
        $Contents02.="
			</tr>";
	}
	$Contents02 .= "</form>";

}else{
		$Contents02 .= "
			<tr height=60><td colspan=9 align=center>상품권 내용이 없습니다.</td></tr>";
}

$Contents02 .= "</table></form>";

$Contents02 .= "<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >	 ";
//if(substr_count ($admininfo[permit], "06-16-01")){
//	$Contents02 .= "<tr height=40><td colspan=8 align=left><!--a href=\"JavaScript:SelectDelete(document.forms['baymoney_list']);\"><img  src='../image/bt_all_del.gif' border=0 align=absmiddle ></a--></td><td colspan=2 align=right><a href='giftcertificate.write.php'><img  src='../image/btc_giftcard.gif' border=0 align=absmiddle ></a></td></tr>";
//}
$Contents02 .= "<tr height=40><td colspan=8 align=left>".page_bar($total, $page, $max,"&gc_ix=$gc_ix&gift_change_state=$gift_change_state&search_type=$search_type&search_text=".urlencode($search_text)."&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","")."</td></tr>";
$Contents02 .= "</table>";


$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 그룹 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";


$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>$Contents01<br></td></tr>";

$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
	<div id='batch_update_coupon'>
				<table cellpadding=3 cellspacing=0 width=100% style='border:0px; margin-top: 10px;'>
					<tr height=30>
						<td class='input_box_item'>
							<input type='radio' name='detail' value='delete' id='delete' checked><label for='delete'>삭제하기</label>
						</td></tr>
				</table>
			<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
				<tr height=50>
					<td colspan=4 align=center>
						<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' onclick='submitToChange();'>
					</td>
				</tr>
			</table>
	</div>
	";

$select = "
<nobr>
<select id='update_type'>
	<option value='1'>검색한 쿠폰 전체에게</option>
	<option value='2' selected>선택한 쿠폰 전체에게</option>
</select>";

$Contents .= HelpBox($select, $help_text, 700);

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = promotion_menu();
$P->Navigation = "프로모션(마케팅)전시 > 오프라인 상품권 발행 내역";
$P->title = "오프라인 상품권 발행 내역";
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