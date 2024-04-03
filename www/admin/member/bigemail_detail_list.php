<?
include("../class/layout.class");
include("../campaign/mail.config.php");

$db = new Database;
$mdb = new Database;
$sdb = new Database;


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

	if($code){
		$where .="and smh.mail_code = '$code'";
	}
	
	// 전체 갯수 불러오는 부분
	$sql	=	"SELECT 
					smh.* , 
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail ,cu.id  
				FROM
					shop_mailling_history smh 
				LEFT JOIN
					shop_mail_box smb
				ON (smh.mail_ix = smb.mail_ix)
				LEFT JOIN 
					common_member_detail cmd
				ON (smh.ucode = cmd.code)
				LEFT JOIN
					common_user cu
				ON (smh.ucode = cu.code)
				WHERE 1 $where 
				ORDER BY regdate DESC ";
	$mdb->query($sql);
	$mdb->fetch();
		
	
	$total = $mdb->total;
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&startDate=$startDate&endDate=$endDate&mail_type=$mail_type&disp=$disp&mail_send_type=$mail_send_type","view");
	
	//실제 데이터
	$sql	=	"SELECT 
					smh.* , smb.mail_title ,
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail ,cu.id 
				FROM
					shop_mailling_history smh 
				LEFT JOIN
					shop_mail_box smb
				ON (smh.mail_ix = smb.mail_ix)
				LEFT JOIN 
					common_member_detail cmd
				ON (smh.ucode = cmd.code)
				LEFT JOIN
					common_user cu
				ON (smh.ucode = cu.code)
				WHERE 1 $where 
				ORDER BY regdate DESC 
				LIMIT $start , $max";
	$mdb->query($sql);
	$mdb->fetch();
	$mail_title = $mdb->dt['mail_title'];
	$send_time_type = $mdb->dt['mail_sendtype'];

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
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	<div class='tab'>
				<table class='s_org_tab' width=100%>
				<col width='600'>
				<col width='*'>
				<tr>
					<td class='tab'>
						<table id='tab_02'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='member_batch.php?update_kind=bigemail'\">메일발송</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_03' class='on'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='bigemail_list.php'\">발송리스트</td>
							<th class='box_03'></th>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
	    </td>
	</tr>
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top >
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
		<col width=15%>
		<col width=35%>
		<col width=15%>
		<col width=35%>
		<form name=searchmember method='get' style='display:inline;'><!--SubmitX(this);'-->
		<input type='hidden' name='mode' value='search' />
		<tr height=22>
			<td class='input_box_title'> <b>총 발송 예정수 </b> </td>
			<td class='input_box_item'><b id='sended_bigemail_cnt' class=blu style='display:none'>0 건 / </b><b id='remainder_bigemail_cnt'>$total</b> 명</td>
			<td class='input_box_title'> <b>이메일 코드(시스템코드) </b> </td>
			<td class='input_box_item'><input type='text' value='".$code."' /></td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>발송구분</b></td>
			<td class='input_box_item'>
					<input type='radio' name='email_send_time_type' checked value='0' ".CompareReturnValue("O",$send_time_type,"checked")." class='email_send_time_now' id='email_send_time_now' /><label for='email_send_time_now'>즉시발송</label>
					<input type='radio' name='email_send_time_type' value='1' ".CompareReturnValue("1",$send_time_type,"checked")." class='email_send_time_reserve' id='email_send_time_reserve' /><label for='email_send_time_reserve'>예약발송</label>
					".select_date('send_time_email')."
			</td>
			<td class='input_box_title'><b>이메일수신거부 하단삽입</b></td>
			<td class='input_box_item'>
				<input type='radio' name='sendno_yn' value='Y' id='sendno_y' /><label for='sendno_y'>사용함</label>
				<input type='radio' name='sendno_yn' value='N' id='sendno_n' /><label for='sendno_n'>사용안함</label>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>발송일</b></td>
			<td class='input_box_item' colspan=3>
				
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>이메일 발송구분</b></td>
			<td class='input_box_item' colspan=3>
				<table cellpadding=0>
					<tr>
						<td>
						<input type='radio' name='email_type_select' id='bigemail_type_new' value='new' ".($email_type == "new" || $email_type == "" ? "checked":"")." onclick=\"LoadEmail2('new');\">
						<label for='bigemail_type_new'>새로작성</label>
						<input type='radio' name='email_type_select' id='bigemail_type_box' value='box' ".CompareReturnValue("box",$update_kind,"checked")." onclick=\"LoadEmail2('box');\">
						<label for='bigemail_type_box'>기존이메일선택</label>
						<div id='email_list' style='display:none'>
						".getMailList("","","width:250px;")."
						</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>이메일 제목</b></td>
			<td class='input_box_item' colspan=3>
				<input type=text name='email_title' id='email_title'   class=textbox value='".$mail_title."'  style='width:250px;height:21px;padding:0px;margin:0px;' >					
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>참조</b></td>
			<td class='input_box_item' colspan=3>
				<input type=text name='mail_cc'  class=textbox value='' style='width:350px' > <span class='small blu'><!--콤마(,) 구분으로 이메일을 입력해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K' )."</span>
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
</table>
<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='addressbook_list.act.php' target='act'>
<input type='hidden' name='ab_ix[]' id='ab_ix'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<div id='result_area' style='clear:both;'>
<table width='100%' border='0' cellpadding='0' cellspacing='0'  class='list_table_box'>
  <col width='40px'>
  <col width='40px'>
  <col width='100px'>
  <col width='200px'>
  <col width='120px'>
  <col width='100px'>
  <col width='100px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='100px'>
  <tr height='28' bgcolor='#ffffff'>
    <td align='center' class='s_td' rowspan='2'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>순번</b></font></td>
	<td align='center' class='m_td' rowspan='2'><font color='#000000'><b>서명/ID</b></font></td>
    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>Email</b></font></td>
    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>성공</b></font></td>
    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>수신거부</b></font></td>
	<td align='center' class='m_td' colspan='3'><font color='#000000'><b>오픈건수</b></font></td>
	<td align='center' class='m_td' colspan='3'><font color='#000000'><b>유입건수</b></font></td>
	<td align='center' class='m_td' colspan='3'><font color='#000000'><b>구매건</b></font></td>
    <td align='center' class='e_td' rowspan='2'><font color='#000000'><b>관리</b></font></td>
  </tr>
  <tr height='28' bgcolor='#ffffff'>
    <td align='center' class='m_td'><font color='#000000'><b>계</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>P</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>M</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>계</b></font></td>
	<td align='center' class='m_td'><font color='#000000'><b>P</b></font></td>
	<td align='center' class='m_td'><font color='#000000'><b>M</b></font></td>
	<td align='center' class='m_td'><font color='#000000'><b>계</b></font></td>
	<td align='center' class='m_td'><font color='#000000'><b>P</b></font></td>
	<td align='center' class='m_td'><font color='#000000'><b>M</b></font></td>
  </tr>
  ";
	

	for($i=0;$i < $mdb->total;$i++){
		
		$mdb->fetch($i);		
		$no = $total - ($page - 1) * $max - $i;

		if($mdb->dt['location_type'] == 'P'){
			$isPc = 1;
			$isMobile = 0;
		}else if($mdb->dt['location_type'] == 'M'){
			$isMobile = 1;
			$isPc = 0;
		}


$Contents = $Contents."
  <tr height='30' align=center onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
    <td class='list_box_td'><input type=checkbox name=ab_ix[] id='ab_ix' value='".$mdb->dt['mail_ix']."'></td>
    <td class='list_box_td list_bg_gray' style='padding:5px;'>".$no."</td>
	 <td class='list_box_td' nowrap>".$mdb->dt['name']."/".$mdb->dt['id']."</td>
    <td class='list_box_td point' style='text-align:center;padding:5px;'><span>".$mdb->dt['mail']."</span></td>
    <td class='list_box_td  list_bg_gray' ></td>
	<td class='list_box_td point' ></td>
    <td class='list_box_td' nowrap>".$mdb->dt['mail_open']."</td>
    <td class='list_box_td point' >".$isPc."</td>
    <td class='list_box_td' nowrap>".$isMobile."</td>
    <td class='list_box_td list_bg_gray' nowrap>".$mdb->dt['mail_click']."</td>
	<td class='list_box_td point' ></td>
    <td class='list_box_td' nowrap></td>
    <td class='list_box_td list_bg_gray' nowrap></td>
	<td class='list_box_td point' ></td>
    <td class='list_box_td' nowrap></td>
    <td class='list_box_td list_bg_gray' nowrap>
	";
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
    <td colspan='15' align='center'>등록된 데이터가 없습니다.</td>
  </tr>";

}

$Contents .= "
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
  <tr height='40'>
    <td colspan=5 align=left>
		<input type='checkbox' name='all'><input type='button' value='선택삭제' /> <input type='button' value='재전송' />
    </td>
    <td  colspan='5' align='right' >&nbsp;".$str_page_bar."&nbsp;</td>
  </tr>
</table>
</div>";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='addressbook.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->strLeftMenu = member_menu();
$P->Navigation = $page_navigation;
$P->title = "자주쓰는 메일관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>
