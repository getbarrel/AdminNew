<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("./mail.config.php");

$db = new Database;
$mdb = new Database;
$sdb = new Database;
$sms_design = new SMS;


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

	$where	=	'AND m.mail_history = "0"';

	if($mode != 'search'){
		
	}

	//검색 1주일단위 디폴트
	if ($startDate == ""){
		$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

		$startDate = date("Y-m-d", $before7day);
		$endDate = date("Y-m-d");
	}
	
	//가입/등록일 검색 
	if($orderdate && $mode=='search'){
		$where .= "and regdate between '$startDate 00:00:00' and '$endDate 23:59:59' ";
	}
		
	//메일구분 검색 0:본문 1:서명
	if($mail_type == "0"){
		$where .= " and mail_type =  '0' ";
	}else if($mail_type == "1"){
		$where .= " and mail_type =  '1' ";
	}
	
	//사용여부 검색 0:사용안함 1:사용함
	if($disp == "0"){
		$where .= " and disp =  '0' ";
	}else if($disp == "1"){
		$where .= " and disp =  '1' ";
	}
	
	//발송구분 검색 0:마케팅 1:프로모션 (임의로 바뀔수있음)
	if($mail_send_type == "0"){
		$where .= " and mail_send_type =  '0' ";
	}else if($mail_send_type == "1"){
		$where .= " and mail_send_type =  '1' ";
	}
	
	
	//조건검색
	if($search_type != "" && $search_text != ""){
		$where .= " and $search_type LIKE  '%$search_text%' ";
	}
	
	// 전체 갯수 불러오는 부분
	$sql = "select count(*) as total from shop_mail_box m where 1 $where";
	$mdb->query($sql);
	$mdb->fetch();
	
	$total = $mdb->dt[total];

	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&startDate=$startDate&endDate=$endDate&mail_type=$mail_type&disp=$disp&mail_send_type=$mail_send_type","view");
	
	//실제 데이터
	/*
	$sql	=	"select * from shop_mail_box m where 1 $where order by regdate desc limit $start , $max";
	$mdb->query($sql);
	*/

	if($mdb->dbms_type == "oracle"){
		$sql = "select m.mail_ix,m.mail_code,m.mail_type,m.mail_send_type, m.mail_title, m.regdate, m.disp, count(*) as send_cnt, 
			sum(case when mail_open = '1' then 1 else 0 end) as mail_open, sum(case when mail_click = '1' then 1 else 0 end) as mail_click
			from shop_mail_box m
			left join shop_mailling_history mh on m.mail_ix = mh.mail_ix
			group by m.mail_ix, m.mail_title, m.regdate, m.disp order by m.regdate desc where 1 $where limit $start, $max ";
	}else{
		$sql = "select 
				m.mail_ix,m.mail_code,m.mail_type,m.mail_send_type, m.mail_title, m.regdate, m.disp
			from shop_mail_box m
			where 1 $where order by m.regdate desc limit $start, $max";
	}
	$mdb->query($sql);

$Script = "
<script language='javascript'>
function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
		$('#endDate').attr('disabled',false);
		$('#startDate').attr('disabled',false);
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
		$('#endDate').attr('disabled',true);
		$('#startDate').attr('disabled',true);
	}
}
function mailDelete(mail_ix){
	if(confirm('해당 메일를 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'))
	{
		window.frames['act'].location.href= 'mail.act.php?act=delete&mail_ix='+mail_ix;
	}
}

function LoadEmail(email_type){
	if(email_type == 'new'){
		//$('#email_subject_text').css('display','inline');
		$('#email_select_area').css('display','none');
	}else if(email_type == 'box'){
		//$('#email_subject_text').css('display','none');
		$('#email_select_area').css('display','inline');
	}
}
$(document).ready(function() {
	$('select#email_subject_select').change(function(){
		if($(this).val() != ''){
			$.ajax({
				type: 'GET',
				data: {'act': 'mail_info', 'mail_ix': $(this).val()},
				url: './mail.act.php',
				dataType: 'json',
				async: true,
				beforeSend: function(){

				},
				success: function(mail_info){
					document.getElementById('iView').contentWindow.document.body.innerHTML = mail_info.mail_text;
					$('#email_subject_text').val(mail_info.mail_title);
					//alert(mail_info);
					//$('#row_'+wl_ix).slideRow('up',500);
				}
			});
		}
	});
});
</script>";

$Contents = "<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
  <tr>
    <td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation($page_title, $page_navigation)."</td>
  </tr>
  <tr>
  	<td>
<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' >
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top >
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
		<form name=searchmember method='get' style='display:inline;'><!--SubmitX(this);'-->
			<input type='hidden' name='mode' value='search' />
			<tr>
				<td class='search_box_title'>등록일자
					<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.searchmember);' ".CompareReturnValue('1',$orderdate,' checked').">
				</td>
				<td class='search_box_item'  colspan=3>
					".search_date('startDate','endDate',$startDate,$endDate)."
				</td>
		    <tr>
		      <td class='search_box_title'>메일구분</td>
		      <td class='search_box_item'>
				<input type='checkbox' name='mail_type' value='0' id='mail_type_body' ".CompareReturnValue("0",$mail_type,"checked")."><label for='mail_type_body'>본문</label>
				 <input type='checkbox' name='mail_type' value='1' id='mail_type_sign'  ".CompareReturnValue("1",$mail_type,"checked")."><label for='mail_type_sign'>서명</label>
		      </td>
		      <td class='search_box_title'>사용여부</td>
		      <td class='search_box_item'>
				 <input type=checkbox name='disp' value='1' id='disp_use' ".CompareReturnValue("1",$disp,"checked")."><label for='disp_use'>사용(O)</label>
				 <input type=checkbox name='disp' value='0' id='disp_unuse'  ".CompareReturnValue("0",$disp,"checked")."><label for='disp_unuse'>사용(X)</label>
			  </td>
		    </tr>
			<tr>
				<td class='search_box_title'>발송구분</td>
				<td class='search_box_item'  colspan=3>
					<select name='mail_send_type'>
						<option value=''>선택</option>
						<option value='0'>마케팅</option>
						<option value='1'>프로모션</option>
					</select>
				</td>
		    <tr>
			<td class='search_box_title'>조건검색 </td>
		      <td class='search_box_item' colspan='3'>
					<table>
						<tr>
							<td>
							  <select name=search_type>
										<option value='user_name' ".CompareReturnValue("user_name",$search_type,"selected").">성명</option>
										<option value='mobile' ".CompareReturnValue("mobile",$search_type,"selected").">핸드폰번호</option>
										<option value='email' ".CompareReturnValue("email",$search_type,"selected").">이메일</option>
										<optiongroup >=========================</optiongroup>
										<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">회사명</option>
										<option value='phone' ".CompareReturnValue("phone",$search_type,"selected").">회사전화</option>
										<option value='fax' ".CompareReturnValue("fax",$search_type,"selected").">회사팩스</option>
										<option value='com_address' ".CompareReturnValue("com_address",$search_type,"selected").">주소</option>
							  </select>
							 </td>
							 <td><input type=text name='search_text' class=textbox value='".$search_text."' style='width:100%' ></td>
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
    </td>
  </tr>
  <tr height=50>
    	<td style='padding:10px 20px 20px 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  > <!--a href=\"javascript:mybox.service('addressbook_add.php?code_ix=','10','450','600', 4, [], Prototype.emptyFunction, [], '회원관리 > 메일링/SMS대상추가');\">메일링/SMS 대상추가</a--></td>
  </tr></form>
</table>
<table style='width:100%'>
	<td colspan=0 align=left>전체 : ".$total."개</td>
	<td colspan=0 align=right><a href=\"javascript:PopSWindow('mail_write.php?mmode=pop',880,600,'member_info')\"><img src='../images/".$admininfo["language"]."/btn_mailadd.gif' border=0 ></a></td>
</table>
<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='addressbook_list.act.php' target='act'>
<input type='hidden' name='ab_ix[]' id='ab_ix'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<div id='result_area' style='clear:both;'>
<table width='100%' border='0' cellpadding='0' cellspacing='0'  class='list_table_box'>
  <col width='40px'>
  <col width='40px'>
  <col width='80px'>
  <col width='200px'>
  <col width='120px'>
  <col width='100px'>
  <col width='125px'>
  <col width='60px'>
  <col width='60px'>
  <col width='100px'>
  <tr height='28' bgcolor='#ffffff'>
    <td align='center' class='s_td'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td align='center' class='m_td'><font color='#000000'><b>순번</b></font></td>
	<td align='center' class='m_td'><font color='#000000'><b>등록일자</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>메일구분</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>발송구분</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>메일코드</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>제목</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>참조설명</b></font></td>
	<td align='center' class='m_td' small'><font color='#000000'><b>사용여부</b></font></td>
    <td align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
  </tr>";
	

	for($i=0;$i < $mdb->total;$i++){
		
		$mdb->fetch($i);		
		$no = $total - ($page - 1) * $max - $i;

		//print("<meta charset=utf-8><pre>");
		//print_r($mdb->dt);
		//exit;

$Contents = $Contents."
  <tr height='30' align=center onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
    <td class='list_box_td'><input type=checkbox name=ab_ix[] id='ab_ix' value='".$mdb->dt['mail_ix']."'></td>
    <td class='list_box_td list_bg_gray' style='padding:5px;'>".$no."</td>
	 <td class='list_box_td' nowrap>".substr($mdb->dt['regdate'],0,10)."</td>
    <td class='list_box_td point' style='text-align:center;padding:5px;'><span >".($mdb->dt['mail_type'] == "0" ? "본문":"서명")."</span></td>
    <td class='list_box_td  list_bg_gray' >".($mdb->dt['mail_send_type'] == "0" ? "마케팅":"프로모션")."</td>
	<td class='list_box_td point' >".$mdb->dt['mail_code']."</td>
    <td class='list_box_td' nowrap>".$mdb->dt['mail_title']."</td>
    <td class='list_box_td point' ></td>
    <td class='list_box_td' nowrap>".($mdb->dt['disp'] == "1" ? "O":"X")."</td>
    <td class='list_box_td list_bg_gray' nowrap>";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
        $Contents.="
    	<a href=\"javascript:PopSWindow('mail_write.php?mmode=pop&mail_ix=".$mdb->dt['mail_ix']."',880,600,'member_info')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
    }else{
        $Contents.="
    	<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
    }
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $Contents.="
	    <a href=\"JavaScript:mailDelete('".$mdb->dt[mail_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
    }else{
        $Contents.="
	    <a href=\"JavaScript:mailDelete('".$mdb->dt[mail_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
    }
    $Contents.="
	  </td>
  </tr> ";

	}

if (!$mdb->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='11' align='center'>등록된 데이터가 없습니다.</td>
  </tr>";

}

$Contents .= "
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
  <tr height='40'>
    <td colspan=5 align=left>

    </td>
    <td  colspan='5' align='right' >&nbsp;".$str_page_bar."&nbsp;</td>
  </tr>
</table>
</div>";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='addressbook.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->strLeftMenu = campaign_menu();
$P->Navigation = $page_navigation;
$P->title = "자주쓰는 메일관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>
