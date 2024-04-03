<? 
include("../class/layout.class");

$db = new MySQL;
$mdb = new MySQL;
$sdb = new MySQL;

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

//검색 1주일단위 디폴트
if ($startDate == ""){
	$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

	$startDate = date("Y-m-d", $before7day);
	$endDate = date("Y-m-d");
}

if($mode!="search"){
	$orderdate=1;
}

//날짜검색
if($orderdate){
	if($date_type == "YMD"){
		$where = "AND lc.$date_type between  $startDate and $endDate ";
	}elseif($date_type == "YMD"){
		$where = "AND lc.$date_type between  $startDate and $endDate ";
	}
}

//담당자 검색
if($ACODE){
	$where .= "AND lc.ACODE = '$ACODE'";
}

//통화결과 검색
if(is_array($ANS)){
	for($i=0;$i < count($ANS);$i++){
		if($ANS[$i] != ""){
			if($ANS_str == ""){
				$ANS_str .= "'".$ANS[$i]."'";
			}else{
				$ANS_str .= ",'".$ANS[$i]."' ";
			}
		}
	}

	if($ANS_str != ""){
		$where .= " AND lc.ANS in ($ANS_str) ";
	}
}else{
	if($ANS){
		$where .= " AND lc.ANS = '$ANS' ";
	}
}

$sql = "SELECT 
			lc.* , AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , cu.id
		FROM 
			LOG_CALL lc
		LEFT JOIN
			common_member_detail cmd
		ON
		 (lc.ACODE = cmd.cti_num)
		LEFT JOIN
			common_user cu
		ON
		 (cmd.code = cu.code)
		where 1 $where AND lc.CTYPE IN (10,11)
		GROUP BY ACODE
		";

$sdb->query($sql);
$account = $sdb->fetchall();
	
	$mstring .="<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-top:10px;'>
			<tr>
				<td align='left' colspan=6 >".GetTitleNavigation("회원관리", "콜백요청")."</td>
			</tr>
			<tr>
				<td align='left' colspan=4 style='padding-bottom:15px;'>
					 <div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='member_incall.php'>IN</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='member_outcall.php'>OUT</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='member_cscall.php'>C/S상담내역</a></td>
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
				<td>
				<form name='searchmember' method='GET'>
				<input type='hidden' name='mode' value='search' />
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  border=0 cellpadding='0' cellspacing='0'>
							<tr>
								<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
										<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
										<TR>
											<TD bgColor=#ffffff style='padding:0 0 0 0;'>
											<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
												<col width='18%'>
												<col width='32%'>
												<col width='18%'>
												<col width='32%'>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>
														<select name='date_type'>
															<option value='YMD' ".CompareReturnValue('YMD',$date_type,' selected').">인입일</option>
															<option value='ANS_DATE' ".CompareReturnValue('ANS_DATE',$date_type,' selected').">통화시간</option>
														</select>
														<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.searchmember);' ".CompareReturnValue('1',$orderdate,' checked').">
													</th>
													<td class='search_box_item' colspan='3'>
														".search_date('startDate','endDate',$startDate,$endDate)."
													</td>
												 </tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>유입분류</th>
													<td class='search_box_item'>
														<input type='checkbox' name='send_type[]' id='send_auto' value='A' ".CompareReturnValue("A",$send_type,"checked")."><label for='send_auto'>1단계</label>
														<input type='checkbox' name='send_type[]' id='send_hand' value='M' ".CompareReturnValue("M",$send_type,"checked")."><label for='send_hand'>2단계</label>
													</td>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>녹취유무</th>
													<td class='search_box_item'>
														<input type='checkbox' name='recode[]' id='recode_all' value='Y' ".CompareReturnValue("Y",$recode,"checked")."><label for='recode_all'>전체</label>
														<input type='checkbox' name='recode[]' id='recode_ok' value='N' ".CompareReturnValue("N",$recode,"checked")."><label for='recode_ok'>존재</label>
													</td>
												 </tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>담당자</th>
													<td class='search_box_item'>
														<select name='ACODE'>
															";
															if($account){
																foreach($account as $val){
																	$mstring .=($val['name'] ? "<option value=".$val['ACODE'].">".$val['name']."</option>" : '');
																}
															}else{
																$mstring .=($val['name'] ? "<option value=".$val['ACODE'].">없음</option>" : '');
															}

															$mstring .="
														</select>
													</td>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>결과</th>
													<td class='search_box_item'>
														<input type='checkbox' name='ANS[]' id='call_ok' value='A' ".CompareReturnValue("A",$ANS,"checked")."><label for='call_ok'>성공</label>
														<input type='checkbox' name='ANS[]' id='call_ing' value='B' ".CompareReturnValue("B",$ANS,"checked")."><label for='call_ing'>통화중</label>
														<input type='checkbox' name='ANS[]' id='call_giveup' value='C' ".CompareReturnValue("C",$ANS,"checked")."><label for='call_giveup'>연결포기</label>
														<input type='checkbox' name='ANS[]' id='call_fail' value='N' ".CompareReturnValue("N",$ANS,"checked")."><label for='call_fail'>부재중</label>
													</td>
												 </tr>
												<tr height=27>
													<td class='search_box_title'>조건검색
													<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
													
													<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> (다중검색 체크)
													</td>
													<td class='search_box_item' colspan='3'>
														<table cellpadding=0 cellspacing=0 border='0'>
														<tr>
															<td valign='top'>
																<div style='padding-top:5px;'>
																<select name='search_type' id='search_type'  style=\"font-size:12px;\">
																	<option value='opt_name' ".CompareReturnValue("opt_name",$search_type).">유입번호</option>
																	<option value='mallstory_id' ".CompareReturnValue("mallstory_id",$search_type).">ID</option>
																	<option value='dstaddr' ".CompareReturnValue("dstaddr",$search_type).">전화번호</option>
																</select>
																</div>
															</td>
															<td style='padding:5px;'>
																<div id='search_text_input_div'>
																	<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
																</div>
																<div id='search_text_area_div' style='display:none;'>
																	<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
																</div>
															</td>
															<td>
																<div>
																	<span class='small blu' > * 다중 검색은 다중 아이디로 검색 지원이 가능합니다. 구분값은 ',' 혹은 'Enter'로 사용 가능합니다. </span>
																</div>
															</td>
														</tr>
														</table>
													</td>
												</tr>
											</table>
											</TD>
										</TR>
										</TABLE>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr >
					<td colspan=3 align=center style='padding:10px 0 20px 0'>
						<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
					</td>
				</tr>
				</table>
				</form>
				</td>
			</tr>";
	$mstring .="</table>";
	
	$sql = "SELECT 
				lc.* , AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , cu.id
			FROM 
				LOG_CALL lc
			LEFT JOIN
				common_member_detail cmd
			ON
			 (lc.ACODE = cmd.cti_num)
			LEFT JOIN
				common_user cu
			ON
			 (cmd.code = cu.code)
			where 1 $where AND lc.CTYPE IN (10,11)
			";

	$db->query($sql);
	$total = $db->total;
	
	$sql = "SELECT 
				lc.* , AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , cu.id
			FROM 
				LOG_CALL lc
			LEFT JOIN
				common_member_detail cmd
			ON
			 (lc.ACODE = cmd.cti_num)
			LEFT JOIN
				common_user cu
			ON
			 (cmd.code = cu.code)
			where 1 $where AND lc.CTYPE IN (10,11)
			LIMIT $start,$max
			";

	$db->query($sql);

	$mstring .="<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='27' bgcolor='#ffffff'>
	<td width='8%' align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
    <td width='10%' align='center' class='m_td'><font color='#000000'><b>인입시간</font></td>
    <td width='5%' align='center' class='m_td'><font color='#000000'><b>유입구분</b></font></td>
	<td width='10%' align='center' class='m_td' nowrap><font color='#000000'><b>전화번호</b></font></td>
    <td width='8%' align='center' class='m_td' nowrap><font color='#000000'><b>예상회원</b></font></td>
	 <td width='8%' align='center' class='m_td' nowrap><font color='#000000'><b>누적</b></font></td>
    <td width='8%' align='center' class=m_td nowrap><font color='#000000'><b>통화시간<br />통화종료</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>통화시간</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>대기시간</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>상담원</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>결과</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>녹취</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>관리</b></font></td>
  </tr>";
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$successType	= ctiSuccessType($db->dt['ANS']);
		$phoneNum		= align_tel($db->dt['CID']);

		$sql = "
				SELECT 
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name ,
					cu.id
				FROM
					common_member_detail cmd 
				LEFT JOIN 
					common_user cu
				ON
				 (cmd.code = cu.code)
				WHERE 
				 AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') = '".$phoneNum."'
			";

		$mdb->query($sql);
		$mdb->fetch();
		$no = $total - ($page - 1) * $max - $i;

        $mstring = $mstring."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
			<td class='list_box_td' >".$no."</td>
            <td class='list_box_td' >".$db->dt['YMD']."<br />".$db->dt['HMS']."</td>
			<td class='list_box_td' >".($db->dt['KEY_CODE'] ? $db->dt['KEY_CODE'] : '-')."</td>
            <td class='list_box_td' style='padding:0px 5px;' nowrap>".align_tel($db->dt['CID'])."</td>
			<td class='list_box_td' nowrap>".($mdb->dt['id'] ? $mdb->dt['name']."(".$mdb->dt['id'].")" : '비회원')."</td>
            <td class='list_box_td' nowrap></td>
			<td class='list_box_td' nowrap>".$db->dt['ANS_DATE']."<br />".$db->dt['END_DATE']."</td>
            <td class='list_box_td' >".($db->dt['CALL_TM'] ? $db->dt['CALL_TM'] : '-' )."</td>
            <td class='list_box_td' >".($db->dt['RING_TM'] ? $db->dt['RING_TM'] : '-' )."</td>
            <td class='list_box_td' >" .($db->dt['id'] ? $db->dt['name']."(" .$db->dt['id']. ")<br />(".$db->dt['ACODE'].")" : '-' )."</font></td>
            <td class='list_box_td' >".$successType."</td>
			<td class='list_box_td' >".($db->dt['FILE_NM'] ? '<a href="#">듣기</a>' : '-')."</td>
			";
			$mstring .= "
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";
			if($update_auth){
				$mstring .= "<img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow2('member_cti.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',1280,800,'member_view')\" style='cursor:pointer;' alt='고객상담' title='고객상담'/> ";
			}else{
				$mstring .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle alt='고객상담' title='고객상담' ></a> ";
			}

			if($update_auth){
				$mstring .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" style='cursor:pointer;' alt='수정' title='수정'/> ";
			}else{
				$mstring .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정' ></a> ";
			}

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if($delete_auth){
				$mstring .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" style='cursor:pointer;' alt='삭제' title='삭제'/> ";
			}else{
				//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
			}
			if($create_auth){
				 $mstring .= "
				 <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\" style='cursor:pointer;' alt='문자발송' title='문자발송'>
				 <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\" style='cursor:pointer;' alt='이메일발송' title='이메일발송'>
				 ";
			}else{
				$mstring .= "
				 <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle alt='문자발송' title='문자발송'></a>
				 <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle alt='이메일발송' title='이메일발송'></a>
				 ";
			}
            $mstring .= "
    </td>
  </tr>";

	}

if (!$db->total){

$mstring = $mstring."
  <tr height=50>
    <td colspan='15' align='center'>등록된 데이터가 없습니다.</td>
  </tr>";
}
$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text","view");

$mstring .= "
</table>
<table width=100%>
	<tr>
		<td><div style='width:100%;text-align:right;padding:5px 0px;'>".$str_page_bar."</div></td>
		<td align=right>
		<!--img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\" /-->
		<!--div style='cursor:pointer' onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\">회원수동등록</div-->
	</td>
</tr>
</table>
";

	
	$Contents = $mstring;
	
	$Script = "
<script type='text/javascript' >
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


$(document).ready(function (){

//다중검색어 시작 2014-04-10 이학봉

	$('input[name=mult_search_use]').click(function (){
		var value = $(this).attr('checked');

		if(value == 'checked'){
			$('#search_text_input_div').css('display','none');
			$('#search_text_area_div').css('display','');
			
			$('#search_text_area').attr('disabled',false);
			$('#search_texts').attr('disabled',true);
		}else{
			$('#search_text_input_div').css('display','');
			$('#search_text_area_div').css('display','none');

			$('#search_text_area').attr('disabled',true);
			$('#search_texts').attr('disabled',false);
		}
	});

	var mult_search_use = $('input[name=mult_search_use]:checked').val();
		
	if(mult_search_use == '1'){
		$('#search_text_input_div').css('display','none');
		$('#search_text_area_div').css('display','');

		$('#search_text_area').attr('disabled',false);
		$('#search_texts').attr('disabled',true);
	}else{
		$('#search_text_input_div').css('display','');
		$('#search_text_area_div').css('display','none');

		$('#search_text_area').attr('disabled',true);
		$('#search_texts').attr('disabled',false);
	}

//다중검색어 끝 2014-04-10 이학봉

});
</script>";

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = member_menu();
	$P->Navigation = "회원관리 > 콜백요청";
	$P->title = "전체회원";
	$P->NaviTitle =  "C/S 상담내역";
	$P->strContents =  $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
?>
