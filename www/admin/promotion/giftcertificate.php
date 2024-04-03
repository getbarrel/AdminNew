<?
include("../class/layout.class");


$db = new Database;
$cdb = new Database;

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
$where = " where gc.gc_ix <> '0' ";

if($gift_change_state != ""){
	$where .= " and gc.gift_change_state = $gift_change_state ";
}

$gift_type = $_GET["gift_type"];
if($gift_type){
	$where .= " AND gc.gift_type = '".$gift_type."' ";
}

if($search_text != ""){
	if($search_type != ""){
		$where .= " and $search_type LIKE '%".trim($search_text)."%' ";
	}else{
		$where .= " and (gift_certificate_name LIKE '%".trim($search_text)."%' or memo LIKE '%".trim($search_text)."%') ";
	}
}

if($reg_sdate != "" && $reg_edate != ""){
	$where .= " and gc.regdate between '$reg_sdate 00:00:00' and '$reg_edate 23:59:59' ";
}

if($gift_start_date != "" && $gift_end_date != ""){
	$where .= " and  (gc.gift_start_date between  '$gift_start_date' and '$gift_end_date' or gc.gift_start_date between  '$gift_start_date' and '$gift_end_date' )";
}

$sql = "select gc.gc_ix from shop_gift_certificate gc
			left join common_member_detail m on gc.reg_mem_ix = m.code ";
$db->query($sql);
$db->fetch();
$real_total = $db->total;

$sql = "select gc.gc_ix from shop_gift_certificate gc
			left join common_member_detail m on gc.reg_mem_ix = m.code
			$where ";
$db->query($sql);
$db->fetch();
$total = $db->total;


$sql = "select gc.* , m.code,  IFNULL(m.name,'-') as name
			from shop_gift_certificate gc
			left join common_member_detail m on gc.reg_mem_ix = m.code
			$where
			order by gc_ix desc
			LIMIT $start, $max";

$db->query($sql); //where gc_ix = '$code'


$Script ="
<script language='JavaScript' >
/*
function BaymoneyReset(){
	var frm = document.forms['baymoney_list'];

	frm.reset();
	frm.act.value = 'baymoney_insert';
}
*/
/*
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
*/

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

function changeMax(obj){
	var max = $(obj).val();
	$('form[name=searchmember]').find('input[name=max]').val(max);
	$('form[name=searchmember]').submit();
}

function checkIx(){
	if($('#check_all').is(':checked')){
		$('input[name^=ix]').prop('checked', true);
	}else{
		$('input[name^=ix]').prop('checked', false);
	}
}

function submitToChange(){
	var update_type = $('#update_type').val();
	var detail = $('input[name=detail]:checked').val();

	$('input[name=update_type]').val(update_type);
	$('input[name=act_detail]').val(detail);

	$('form[name=list_frm]').submit();
}

function ModifyGiftCertificate(gc_ix){
	var str = '상품권의 내용을 수정하시겠습니까?\\n\\n*수정된 정보는, 수정완료 이후 발급되는 쿠폰부터 적용됩니다.';
	if(confirm(str)){
		window.document.location.href='giftcertificate.write.php?gc_ix='+gc_ix;
	}
}

function DeleteGiftCertificate(gc_ix){
	if(confirm('상품권을 삭제하시겠습니까?\\n*삭제 시, 발행된 모든 시리얼넘버를 사용할 수 없습니다.')){
		window.frames['iframe_act'].location.href='giftcertificate.act.php?act=delete&gc_ix='+gc_ix;
	}
}

function CopyGiftCertificate(gc_ix){
	var str = '해당 상품권의 내용을 복사하시겠습니까?';
	if(confirm(str)){
		window.frames['act'].location.href='giftcertificate.act.php?act=copy&gc_ix='+gc_ix;
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

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("오프라인 상품권 리스트", "마케팅관리 > 오프라인 상품권 리스트")."</td>
	  </tr>
	  <tr>
			<td>
				<form name='searchmember' style='display:inline;'>
				<input type=hidden name=max value='".$max."'>
				<table width=100%  border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td align='left' colspan=2 width='100%' valign=top >
							<table cellpadding=0 cellspacing=1 width='100%' class='search_table_box'>
								<tr>
									<th class='search_box_title' width='150'>조건검색 : </th>
									<td class='search_box_item' colspan=3>
									<table width=100% cellpadding=0 cellspacing=0>
										<col width='75px;'>
										<col width='*'>
										<tr>
											<td>
											<select name=search_type>
												<option value='' >통합 검색</option>
												<option value='gift_certificate_name' ".CompareReturnValue("gift_certificate_name",$search_type,"selected").">상품권명</option>
												<option value='memo' ".CompareReturnValue("memo",$search_type,"selected").">상품권 설명</option>
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
									<td class='input_box_title'> <label for='gift_type_' >상품권 구분</label></td>
									<td class='input_box_item' colspan=3>";
										$Contents01 .= "<input type='radio' name='gift_type' id='gift_type_' class='gift_type' value='' ".($gift_type == "" ? "checked":"")." validation=true title='".$value."' > <label for='gift_type_' >전체</label> ";
									  foreach($_GIFT_TYPE as $key => $value){
										$Contents01 .= "<input type='radio' name='gift_type' id='gift_type_".$key."' class='gift_type' value='".$key."' ".($gift_type == $key ? "checked":"")." validation=true title='".$value."'> <label for='gift_type_".$key."' >".$value."</label> ";
									  }
							$Contents01 .= "
									</td>
								  </tr>
								<tr height=27>
									<td class='search_box_title' align=center><label for='gift_use_date'><b>사용가능기간</b></label><input type='checkbox' name='gift_use_date' id='gift_use_date' value='1' onclick='ChangeGiftDate(document.searchmember);' ".($gift_use_date == "1" ? "checked":"")."></td>
									<td class='search_box_item' colspan=3 style='padding-left:5px;'>
									".search_date('gift_start_date','gift_end_date',$gift_start_date,$gift_end_date,'N','D')."									
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
	  <td colspan=2 style='padding-bottom:5px;'>검색 결과 : ".$total." 건 / 전체 : ".$real_total." 건</td>
	  <td colspan=10  align=right>
		<select id='max' onchange='changeMax(this);'>
			<option value='10' ".($max == 10 ? "selected" : "").">10개씩 보기</option>
			<option value='50' ".($max == 50 ? "selected" : "").">50개씩 보기</option>
		</select>
	  </td></tr>
	</table>
	<form id='list_form' name='list_frm' method='POST' action='giftcertificate.act.php' enctype='multipart/form-data'>
	<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
	<input type='hidden' name='act' value='delete_selected'>
	<input type='hidden' name='update_type' value=''>
	<input type='hidden' name='act_detail' value=''>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <tr bgcolor=#efefef align=center height=32>
			<!--td class='s_td' width=3%><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.baymoney_list)'></td-->
			<td class=s_td width=3%><input type='checkbox' id='check_all' onclick='checkIx();'></td>
			<td class='m_td' width=7% >등록일자</td>
			<td class='m_td' width=*>상품권명 </td>
			<td class='m_td' width=8%>사용기간</td>
			<td class='m_td' width=12%>상품권 유형</td>
			<td class='m_td' width=12%>상품권 헤택</td>
			<td class='m_td' width=5%>발행수</td>
			<td class='m_td' width=6%>사용수</br>(사용율)</td>
			<td class='m_td' width=5%>발행내역</td>
			<td class='e_td' width=7% >관리 </td>
		</tr>";



if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		if($db->dt[gift_type]=="G"){
			$gift_type_str = "구매상품권";
		}else if($db->dt[gift_type]=="M"){
			$gift_type_str = "정회원 상품권";
		}else if($db->dt[gift_type]=="R"){
			$gift_type_str = "마일리지 지급 상품권";

			$sale_detail = number_format($db->dt[gift_amount])." 마일리지";
		}else if($db->dt[gift_type]=="C" || $db->dt[gift_type]=="U"){
			if($db->dt[gift_type]=="C"){
				$gift_type_str = "쿠폰 지급 상품권</br>(랜덤 시리얼 넘버) ";
			}else{
				$gift_type_str = "쿠폰 지급 상품권</br>(동일 시리얼 넘버)";
			}

			$cp_sql = "select c.cupon_ix, c.cupon_sale_type, c.cupon_acnt, c.cupon_sale_value from 
							shop_gift_certificate_cupon gcc 
							left join shop_cupon_publish cp on gcc.gift_cupon_ix=cp.publish_ix 
							inner join shop_cupon c on c.cupon_ix=cp.cupon_ix
							where gc_ix='".$db->dt[gc_ix]."' order by publish_ix asc";
			$cdb->query($cp_sql);
			$coupon_infos = $cdb->fetchall("object");

			if(count($coupon_infos) > 0){
				$gift_type_str .="</br><input type='button' value='첫번째 쿠폰' style='margin: 10px;' onclick=\"javascript:PoPWindow3('coupon_detail.php?cupon_ix=".$coupon_infos[0][cupon_ix]."',900,800,'cupon_detail_pop')\"> 포함 ".count($coupon_infos)."건";

				if($coupon_infos[0][cupon_sale_type] == 1){
					$sale_detail = '정률할인(%)';
					$sale_unit = '%';
				}else if($coupon_infos[0][cupon_sale_type] == 2){
					$sale_detail = '정액할인(원)';
					$sale_unit = '원';
				}

				if($coupon_infos[0][cupon_acnt] == 1){
					$sale_detail .= '</br>본사</br>('.number_format($coupon_infos[0][cupon_sale_value]).$sale_unit.') </br></br>포함 '.count($coupon_infos).'건';
				}else if($coupon_infos[0][cupon_acnt] == 2){
					$sale_detail .= '</br>본사 + 셀러</br>('.number_format($coupon_infos[0][cupon_sale_value]).$sale_unit.') </br></br>포함 '.count($coupon_infos).'건';
				}
			}
		}else{
			$gift_type_str = "";
		}

		$gift_code_str = substr($db->dt[gift_code],0,4)."-".substr($db->dt[gift_code],4,4)."-".substr($db->dt[gift_code],8,4)."-".substr($db->dt[gift_code],12,4);
		

		$cdb->query("SELECT count(*) as cnt FROM shop_gift_certificate_detail where gc_ix = '".$db->dt[gc_ix]."' and gift_change_state ='1' ");
		$cdb->fetch();
		$use_cnt = $cdb->dt[cnt];

		$Contents02 .= "<tr height=28 align=center>
				<td class='list_box_td '><input type='checkbox' name='ix[]' value='".$db->dt[gc_ix]."'></td>
				<td >".$db->dt[regdate]."</td>
				<td  style='line-height:120%;padding:10px;' nowrap>
					".$db->dt[gift_certificate_name] ."</b></br><input type='button' value='상품권 상세' style='margin: 10px;' onclick=\"javascript:PoPWindow3('coupon_detail.php?page_type=off&gc_ix=".$db->dt[gc_ix]."',900,800,'cupon_detail_pop')\">
				</td>
				<td >".$db->dt[gift_start_date]." ~ ".$db->dt[gift_end_date]."</td>
				<td  style='line-height:120%;padding:10px;' nowrap>".$gift_type_str."</td>
				<td >".$sale_detail."</td>
				<td >".($db->dt[gift_type]=="U" ? "무제한" : number_format($db->dt[create_cnt]))."</td>
				<td >".number_format($use_cnt).($db->dt[gift_type]=="U" ? "" : " (".number_format($use_cnt/$db->dt[create_cnt]*100,2)."%)" )."</td
				>
				<td >
					".($db->dt[gift_type]=="U" ? "-" : "<a href='giftcertificate_detail.php?gc_ix=".$db->dt[gc_ix]."'><input type='button' value='발행내역' style='margin: 10px;'></a>")."
				</td>
				<td class='list_box_td ' nowrap><input type='button' value='상품권 복사' style='margin: 3px;' onclick=\"CopyGiftCertificate('".$db->dt[gc_ix]."');\"></br>
				<input type='button' value='수정' style='margin: 3px;' onclick=\"ModifyGiftCertificate('".$db->dt[gc_ix]."');\"><input type='button' value='삭제' style='margin: 3px;' onclick=\"DeleteGiftCertificate('".$db->dt[gc_ix]."');\">
				</td>
			</tr>";
	}
	$Contents02 .= "</form>";

}else{
		$Contents02 .= "
			<tr height=60><td colspan=11 align=center>상품권 내용이 없습니다.</td></tr>";

}
$Contents02 .= "</table></form>";

$Contents02 .= "<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >	 ";
//if(substr_count ($admininfo[permit], "06-16-01")){
	$Contents02 .= "<tr height=40><td colspan=8 align=left style='text-align:left;'>".page_bar($total, $page, $max,"&code=$code&gift_change_state=$gift_change_state&search_type=$search_type&search_text=".urlencode($search_text)."&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","")."</td><td colspan=2 align=right>
                ";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
    $Contents02 .= "
    <a href='giftcertificate.write.php'><img  src='../images/btm_reg.gif' border=0 align=absmiddle ></a></td></tr>";
}else{
    $Contents02 .= "
    <a href=\"".$auth_write_msg."\"><img  src='../images/btm_reg.gif' border=0 align=absmiddle ></a></td></tr>"; 
}
//}

$Contents02 .= "</table>";



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
$P->Navigation = "프로모션(마케팅)전시 > 오프라인 상품권 리스트";
$P->title = "오프라인 상품권 리스트";
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