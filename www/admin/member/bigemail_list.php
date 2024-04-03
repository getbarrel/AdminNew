<?
include("../class/layout.class");

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

	$where	=	'';

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
		$where .= "and smh.regdate between '$startDate 00:00:00' and '$endDate 23:59:59' ";
	}
		
	
	//사용여부 검색 0:사용안함 1:사용함
	if($disp == "0"){
		$where .= " and disp =  '0' ";
	}else if($disp == "1"){
		$where .= " and disp =  '1' ";
	}
	
	//발송구분 검색 0:마케팅 1:프로모션 (임의로 바뀔수있음)
	if(is_array($mail_sendtype)){
		for($i=0;$i < count($mail_sendtype);$i++){
			if($mail_sendtype[$i] != ""){
				if($mail_sendtype_str == ""){
					$mail_sendtype_str .= "'".$mail_sendtype[$i]."'";
				}else{
					$mail_sendtype_str .= ",'".$mail_sendtype[$i]."' ";
				}
			}
		}

		if($mail_sendtype_str != ""){
			$where .= " AND mail_sendtype in ($mail_sendtype_str) ";
		}
	}else{
		if($mail_sendtype){
			$where .= " AND mail_sendtype = '$mail_sendtype' ";
		}
	}
	
	//이메일 제목
	if($email_title){
		$where .= " and smb.mail_title LIKE '$email_title' ";
	}
	
	// 전체 갯수 불러오는 부분
	$sql	=	"SELECT 
					smh.* , smb.mail_title
				FROM
					shop_mailling_history smh 
				LEFT JOIN 
					shop_mail_box smb 
				ON (smh.mail_ix = smb.mail_ix)
				WHERE 1 $where 
				GROUP BY smh.mail_code
				ORDER BY regdate DESC 
				";
	$mdb->query($sql);
	$mdb->fetch();
	
	$total = $mdb->total;
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&startDate=$startDate&endDate=$endDate&mail_type=$mail_type&disp=$disp&mail_send_type=$mail_send_type","view");
	
	//실제 데이터
	$sql	=	"SELECT 
					smh.* , smb.mail_title ,
					count(*) as send_cnt, sum(case when mail_open = '1' then 1 else 0 end) as mail_open, sum(case when mail_click = '1' then 1 else 0 end) as mail_click ,
					sum(case when location_type = 'P' then 1 else 0 end) as isPc , sum(case when location_type = 'M' then 1 else 0 end) as isMobile
				FROM
					shop_mailling_history smh 
				LEFT JOIN 
					shop_mail_box smb 
				ON (smh.mail_ix = smb.mail_ix)
				WHERE 1 $where 
				GROUP BY smh.mail_code
				ORDER BY regdate DESC 
				LIMIT $start , $max";
	$mdb->query($sql);
	print($where);
$Script = "
<script type='text/javascript'>

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
		<form name=searchmember method='get' style='display:inline;'><!--SubmitX(this);'-->
			<input type='hidden' name='mode' value='search' />
			<tr>
				<td class='search_box_title'>발송일자
					<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.searchmember);' ".CompareReturnValue('1',$orderdate,' checked').">
				</td>
				<td class='search_box_item'  colspan=3>
					".search_date('startDate','endDate',$startDate,$endDate)."
				</td>
		    <tr>
		      <td class='search_box_title'>발송구분</td>
		      <td class='search_box_item'>
				<input type='checkbox' name='mail_sendtype' value='0' id='mail_type_body' ".CompareReturnValue("0",$mail_sendtype,"checked")."><label for='mail_type_body'>즉시발송</label>
				 <input type='checkbox' name='mail_sendtype' value='1' id='mail_type_sign'  ".CompareReturnValue("1",$mail_sendtype,"checked")."><label for='mail_type_sign'>예약발송</label>
		      </td>
		      <td class='search_box_title'>진행상태</td>
		      <td class='search_box_item'>
				 <input type=checkbox name='disp' value='1' id='disp_use' ".CompareReturnValue("1",$disp,"checked")."><label for='disp_use'>발송완료</label>
				 <input type=checkbox name='disp' value='0' id='disp_unuse'  ".CompareReturnValue("0",$disp,"checked")."><label for='disp_unuse'>발송전</label>
			  </td>
		    </tr>
			<tr>
			<td class='input_box_title'> <b>이메일 제목</b></td>
			<td class='input_box_item' colspan=3>
				<input type=text name='email_title' id='email_title'   class=textbox value='".$email_title."'  style='width:250px;height:21px;padding:0px;margin:0px;' >					
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
  <col width='80px'>
  <col width='100px'>
  <col width='350px'>
  <col width='100px'>
  <col width='100px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <col width='60px'>
  <tr height='28' bgcolor='#ffffff'>
    <td align='center' class='s_td' rowspan='2'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>순번</b></font></td>
	<td align='center' class='m_td' rowspan='2'><font color='#000000'><b>예약일<br />발송일</b></font></td>
    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>발송구분<br />진행상태</b></font></td>
    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>메일명</b></font></td>
    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>발송요청수</b></font></td>
    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>실패건수</b></font></td>
    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>성공건수</b></font></td>
	<td align='center' class='m_td' colspan='3'><font color='#000000'><b>오픈건수</b></font></td>
	<td align='center' class='m_td' colspan='3'><font color='#000000'><b>유입건수</b></font></td>
    <td align='center' class='e_td' rowspan='2'><font color='#000000'><b>관리</b></font></td>
  </tr>
  <tr height='28' bgcolor='#ffffff'>
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


$Contents = $Contents."
  <tr height='30' align=center onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
    <td class='list_box_td'><input type=checkbox name=ab_ix[] id='ab_ix' value='".$mdb->dt['mail_ix']."'></td>
    <td class='list_box_td list_bg_gray' style='padding:5px;'>".$no."</td>
	<td class='list_box_td' nowrap>".substr($mdb->dt['regdate'],0,10)."</td>
    <td class='list_box_td point' style='text-align:center;padding:5px;'><span>".($mdb->dt['mail_sendtype'] == "0" ? "즉시발송":"예약발송")."</span></td>
    <td class='list_box_td  list_bg_gray' >".$mdb->dt['mail_title']."<a href='bigemail_detail_list.php?code=".$mdb->dt['mail_code']."'><input type='button' value='상세내역' /></a></td>
	<td class='list_box_td point' >".$mdb->dt['send_cnt']."</td>
    <td class='list_box_td' nowrap></td>
    <td class='list_box_td point' ></td>
    <td class='list_box_td' nowrap>".$mdb->dt['mail_open']."</td>
    <td class='list_box_td list_bg_gray' nowrap>".$mdb->dt['isPc']."</td>
	<td class='list_box_td point' >".$mdb->dt['isMobile']."</td>
    <td class='list_box_td' nowrap>".$mdb->dt['mail_click']."</td>
    <td class='list_box_td point' ></td>
    <td class='list_box_td' nowrap></td>
    <td class='list_box_td list_bg_gray' nowrap><a href='http://v330.mymailer.co.kr:9090/report/report.jsp' target='_blank'><input type='button' value='메일통계' /></a></td>
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

    </td>
    <td  colspan='5' align='right' >&nbsp;".$str_page_bar."&nbsp;</td>
  </tr>
</table>
</div>";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='addressbook.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->strLeftMenu = member_menu();
$P->Navigation = $page_navigation;
$P->title = "대량메일 발송리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>
