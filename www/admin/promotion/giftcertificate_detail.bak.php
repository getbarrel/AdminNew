<?
include("../class/layout.class");


$db = new Database;
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
$where = " where gcd.gc_ix <> '0' and gcd.gc_ix = '".$gc_ix."' and gc.gc_ix = gcd.gc_ix  ";

if($gift_change_state != ""){
	$where .= " and gcd.gift_change_state = $gift_change_state ";
}

if($search_type != "" && $search_text != ""){
	if($search_type == "gc.gift_code"){
		 $search_text = str_replace("-","",$search_text);
		 $where .= " and $search_type LIKE '%".trim($search_text)."%' ";

	}else if($search_type == "m.name"){

		$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";

	}else{
		$where .= " and $search_type LIKE '%".trim($search_text)."%' ";
	}
}

$startDate = $FromYY.$FromMM.$FromDD;
$endDate = $ToYY.$ToMM.$ToDD;

if($startDate != "" && $endDate != ""){
	$where .= " and  gc.regdate between  $startDate and $endDate ";
}

$sql = "select 
			gc.gc_ix 
		from 
			shop_gift_certificate_detail gcd 
			left join common_user as cu on (gcd.member_id = cu.id)
			left join common_member_detail m on cu.code = m.code
			,shop_gift_certificate gc
		$where ";
//echo $sql ;

$db->query($sql);

$db->fetch();
$total = $db->total;

if($mode == "excel"){
	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-Disposition: attachment; filename=giftcertificate_list.xls" );
	header( "Content-Description: Generated Data" );

	if($db->dbms_type == "oracle"){
		$sql = "select gcd.gcd_ix,
					gcd.gc_ix,
					gcd.gift_code,
					gcd.gift_change_state,
					gcd.member_id,
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
	}else{
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
	}

	//echo $sql;
	$db->query($sql); //where gc_ix = '$code'

	$mstring = "<table border=1>";
	$mstring .= "<tr><td>번호</td><td>시리얼</td><td>금액</td><td>사용유효기간</td><td>등록ID</td><td>등록자아이피</td><td>등록일</td><td>생성일</td></tr>";
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);

		if($db->dt[gift_type]=="G"){
			$gift_type_str = "구매상품권";
		}else if($db->dt[gift_type]=="M"){
			$gift_type_str = "정회원 상품권";
		}else{
			$gift_type_str = "";
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
			<td>".number_format($db->dt[gift_amount])."원</td><td>".$db->dt[gift_start_date]."~".$db->dt[gift_end_date]."</td><td>".$db->dt[member_id]."</td><td>".$db->dt[member_ip]."</td><td>".$db->dt[chagne_request_date]."</td><td>".$db->dt[reg_date]."</td></tr>";
			//<td>".$db->dt[reg_member_id]."</td><td>".$db->dt[reg_ip]."</td>
	}
	$mstring .= "<table>";

	echo $mstring;
	//echo iconv("utf-8","CP949",$mstring);
	exit;
}else{

	if($db->dbms_type == "oracle"){
		$sql = "select 
					gcd.gcd_ix,
					gcd.gc_ix,
					gcd.gift_code,
					gcd.gift_change_state, 
					gcd.member_id,
					gc.gift_start_date,
					gc.gift_end_date,
					gc.regdate, 
					m.code, 
					IFNULL(AES_DECRYPT(UNHEX(m.name),'".$db->ase_encrypt_key."'),'-') as name
				from 
					shop_gift_certificate_detail gcd
					left join common_user cu on gcd.member_id = cu.id
				left join common_member_detail m on cu.code = m.code
					,shop_gift_certificate gc
				
				$where
					order by gc_ix desc
					LIMIT $start, $max";
	}else{
	/*
		$sql = "select gc.*, m.code, IFNULL(AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."'),'-') as name
					from (select gc.* from shop_gift_certificate_detail gc left join common_member_detail m on gc.member_id = m.code $where order by gc_ix desc LIMIT $start, $max ) gc left join ".TBL_COMMON_MEMBER_DETAIL." m on gc.member_id = m.code
					";//, DATE_FORMAT(m.regdate, '%Y.%m.%d %H:%i;%s') as disp_regdate

					$sql = "select gc.*, m.code,  IFNULL(m.name,'-') as name
					from (select gc.* from shop_gift_certificate_detail gc left join common_member_detail m on gc.member_id = m.code $where order by gc_ix desc LIMIT $start, $max ) gc left join ".TBL_COMMON_MEMBER_DETAIL." m on gc.member_id = m.code
					";//, DATE_FORMAT(m.regdate, '%Y.%m.%d %H:%i;%s') as disp_regdate
	*/
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

	}
	//echo $sql;
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
		<td align='left' colspan=6 > ".GetTitleNavigation("오프라인 상품권관리", "마케팅관리 > 오프라인 상품권관리 ")."</td>
	  </tr>
	  <tr>
			<td>
				<form name='searchmember' style='display:inline;'>
				<input type='hidden' name='gc_ix' value='".$gc_ix."' />
				<table width=100%  border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td align='left' colspan=2 width='100%' valign=top >
							<table cellpadding=0 cellspacing=1 width='100%' class='search_table_box'>
								<tr>
									<th class='search_box_title' width='150'>상태 : </th>
									<td class='search_box_item' colspan=3>
									<select name='gift_change_state'>
										<option value=''>상태값을 선택해 주세요</option>
										<option value='0' ".CompareReturnValue("0",$gift_change_state,"selected").">사용전</option>
										<option value='1' ".CompareReturnValue("1",$gift_change_state,"selected").">사용완료</option>
									</select>
									</td>
								</tr>
								<tr>
									<th class='search_box_title' width='150'>조건검색 : </th>
									<td class='search_box_item' colspan=3>
									<select name=search_type>
										<option value=''>검색항목</option>
										<option value='m.name' ".CompareReturnValue("m.name",$search_type,"selected").">회원명</option>
										<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">아이디</option>
										<option value='gc.gift_code' ".CompareReturnValue("gc.gift_code",$search_type,"selected").">상품권번호</option>
										<option value='gc.memo' ".CompareReturnValue("gc.memo",$search_type,"selected").">메모</option>
										<option value='gc.reg_date' ".CompareReturnValue("gc.reg_date",$search_type,"selected").">등록일자</option>
									</select>
									<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:50%' >
									</td>
								</tr>
								 <tr height=27>
								  <td class='search_box_title' align=center>
									<label for='regdate'><b>사용가능일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);'>
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
	  <td colspan=2>".$total." 개</td>
	  <td colspan=10 align=right style='padding-bottom:5px;'>";      
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
    $Contents02 .="    
	  <a href='?mode=excel&".$QUERY_STRING."'><img src='../image/btn_excel_save.gif' border=0></a>";
}else{
    $Contents02 .="    
      <a href=\"".$auth_excel_msg."\"><img src='../image/btn_excel_save.gif' border=0></a>";
}
$Contents02 .= "                  
	  </td></tr>
	</table>
	  <form name=baymoney_list method=post action='member.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
		<input type='hidden' name='act' value='baymoney_select_delete'>
		<input type='hidden' name='id' value=''>
		<input type='hidden' name='etc' value=''>
		<input type='hidden' name='baymoney' value=''>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <tr bgcolor=#efefef align=center height=28>
			<td class='s_td' width=3%><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.baymoney_list)'></td>
			<td class='m_td' width=18%>상품권번호 </td>
			<td class='m_td' width=15%>사용가능기간</td>
			<td class='m_td' width=8% >상태</td>
			<td class='m_td' width=14% >등록일자</td>
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
				<td bgcolor='#ffffff'><input type=checkbox class=nonborder id='giftcertificate_id' name=gc_ix[] value='".$db->dt[gc_ix]."'></td>
				<td bgcolor='#efefef'>".$gift_code_str."</td>
				<td bgcolor='#ffffff'>".$db->dt[gift_start_date]." ~ ".$db->dt[gift_end_date]."</td>
				<td bgcolor='#efefef'>".$gift_change_state_str."</td>
				<td bgcolor='#ffffff'>".$db->dt[regdate]."</td>
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

$Contents02 .= "</table>";

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
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >고객들에게 지급됐거나 고객들이 사용한 상품권 내역입니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원명을 클릭하시면 해당 회원에 대한 상품권을 확인하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >삭제를 원하시는 상품권 내역을 선택하신후 일괄정보 삭제를 클릭하시면 상품권이 삭제됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품권를 직접 지급 하고자 하실 경우 회원 이름을 클릭하여 입력하시면 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >적립내역이나 적립금 사용내역이 주문취소 시 적립금 산출에 적용이 되지 않게 됩니다.</td></tr>
</table>
";

$Contents .= HelpBox("오프라인 상품권관리", $help_text);

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = promotion_menu();
$P->Navigation = "프로모션(마케팅)전시 > 오프라인 상품권관리";
$P->title = "프로모션(마케팅)전시 > 오프라인 상품권관리";
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