<?php
include("../class/layout.class");


$Script = "
<script language='javascript' src='/admin/member/member.js'></script>

<script type='text/javascript'>
	function cid_del(code){
		$('#row_'+code).remove();
	}
	$(function(){

		$('#push_form').submit(function(){
			if($('input[name=title]').val() == ''){
				alert('제목을 입력해주세요');
				$(this).focus();
				return false;
			}

			// $('#submit_image').hide(); ###
		});
		$('#push_text').click(function(){
			$('#contents').show();
			$('#upfile').hide();
			$('#notifile').hide();
		});
	   $('#push_img').click(function(){
			$('#contents').hide();
			$('#notifile').hide();
			$('#upfile').show();
		});
		$('#noti_img').click(function(){
			$('#contents').show();
			$('#upfile').hide();
			$('#notifile').show();
		});


		 $('input[name=send_time_sms]').attr('disabled',true).css('background-color','#fff');
		 $('select[name=send_time_hour]').attr('disabled',true).css('color','#ccc');
		 $('select[name=send_time_minite]').attr('disabled',true).css('color','#ccc');

		$('.send_time_now').click(function(){
			if(this.checked){
				 $('input[name=send_time_sms]').attr('disabled',true).css('background-color','#fff');
				 $('select[name=send_time_hour]').attr('disabled',true).css('color','#ccc');
				 $('select[name=send_time_minite]').attr('disabled',true).css('color','#ccc');

				 $('input[name=send_time_sms]').val('');
				$('select[name=send_time_hour]').val('');
				$('select[name=send_time_minite]').val('');
			}
		});

		$('.send_time_reserve').click(function(){
			if(this.checked){
				$('input[name=send_time_sms]').attr('disabled',false).css('background-color','#fff7da');
				$('select[name=send_time_hour]').attr('disabled',false).css('color','#666');
				$('select[name=send_time_minite]').attr('disabled',false).css('color','#666');

				//오늘 날짜를 넣어준다.
				$('input[name=send_time_sms]').val('".date("Y-m-d")."');
				$('select[name=send_time_hour]').val();
				$('select[name=send_time_minite]').val();
			}
		});

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

	})


	function checkMember(id){

		var str_code_list = '';
		$('#'+id).find('input[name^=code]').each(function(){
			if( $(this).is(':checked') ){
				str_code_list = str_code_list + $(this).val() + ',';
			}
		});

		$('#str_code_list').val(str_code_list);

		return true;
	}
</script>";


//device_type의 값이 없으면 A(안드로이드로)
if(!$device_type){
	$device_type = "A";
}


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
			<tr>
				<td align='left' colspan=6 > ".GetTitleNavigation("메시지 발송 (회원전용)", "모바일 푸시 메세지 관리 > 메시지 발송 (회원전용)")."</td>
			</tr>
			<tr>
				<td>";


				$mstring .= "
				<form name=searchmember method='get'><!--SubmitX(this);'-->
				<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'></th>
						<td class='box_05'  valign=top style='padding:0px'>
						<table width='100%' border='0' cellspacing='0' cellpadding='0' class='search_table_box'>
							<input type='hidden' name=mode value='search'>
							<input type='hidden' name=act value='".$act."'>
							<input type='hidden' name=before_update_kind value='".$update_kind."'>
							<input type='hidden' name=update_kind value='".$update_kind."'>
							<input type='hidden' name='device_type' value='".$device_type."'>
							<colgroup>
								<col style='width:18%' />
								<col style='width:32%' />
								<col style='width:18%' />
								<col style='width:32%' />
							</colgroup>
							<tr height=27>
								<td class='search_box_title' bgcolor='#efefef' align=center>회원그룹
								<img src='../images/icon/search_icon.gif' value='검색' onclick=\"PopSWindow2('../member/search_category.php?group_code=member',600,600,'add_brand_category')\" align=absmiddle style='cursor:pointer;' />
								</td>
								<td class='search_box_item' align=left style='padding-left:5px;'>
									<table width='98%' cellpadding='0' cellspacing='0' id='objMember'>
									<colgroup>
										<col width='*'>
										<col width='600'>
									</colgroup>
									<tbody>";
										if(count($gid) > 0){
											for($k=0;$k<count($gid);$k++){
												$re_gid = $gid[$k];
												$sql = "SELECT * FROM ".TBL_SHOP_GROUPINFO." where disp=1 and gp_ix	=  '".$re_gid."'";
												$db->query($sql);
												$db->fetch();

								$mstring .= "	<tr style='height:26px;' id='row_".$re_gid."'>
													<td style='width:75%;'>
													<input type='hidden' name='gid[]' id='cid_".$re_gid."' value='".$re_gid."'>".$db->dt['gp_name']."</td><td><a href='javascript:void(0)' onclick=\"cid_del('".$re_gid."')\"><img src='../images/korea/btc_del.gif' border='0'></a>
													</td>
												</tr>";
											}
										}
										$mstring .= "
									</tbody>
									</table>
								</td>
								<td class='search_box_title' bgcolor='#efefef' align=center>회원타입</td>
								<td class='search_box_item' align=left style='padding-left:5px;'>
									<input type=checkbox name='mem_type[]' value='M' id='mem'  ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem'>일반회원</label>
									<input type=checkbox name='mem_type[]' value='C' id='seller' ".CompareReturnValue("C",$mem_type,"checked")."><label for='seller'>사업자회원</label>
									<input type=checkbox name='mem_type[]' value='A' id='manager'  ".CompareReturnValue("A",$mem_type,"checked")."><label for='manager'>직원(관리자)</label>
								</td>
							</tr>
							<!--<tr height=27>
								<td class='search_box_title' bgcolor='#efefef' align=center>이벤트당첨자
									<img src='../images/icon/search_icon.gif' value='검색' onclick=\"ShowModalWindow('./search_category.php?group_code=event',600,600,'add_brand_category')\" align=absmiddle  style='cursor:pointer;'>
								</td>
								<td class='search_box_item' align=left style='padding-left:5px;' colspan='3'>
								<table width='98%' cellpadding='0' cellspacing='0' id='objEvent'>
												<colgroup>
													<col width='*'>
													<col width='600'>
												</colgroup>
												<tbody>";

													if(count($eid) > 0){

														for($k=0;$k<count($eid);$k++){

															$re_eid = $eid[$k];
															$sql = "select se.* from ".TBL_SHOP_EVENT." se left join shop_event_relation ser on (se.er_ix=ser.er_ix) where event_ix = '".$re_eid."'";
															$db->query($sql);
															$db->fetch();

											$mstring .= "	<tr style='height:26px;' id='row_".$re_eid."'>
																<td>
																<input type='hidden' name='eid[]' id='cid_".$re_eid."' value='".$re_eid."'>".$db->dt['event_title']."(".date("Y.m.d",$db->dt[event_use_sdate])."~".date("Y.m.d",$db->dt[event_use_edate]).")</td><td><a href='javascript:void(0)' onclick=\"cid_del('".$re_eid."')\"><img src='../images/korea/btc_del.gif' border='0'></a>
																</td>
															</tr>";
														}
													}
										$mstring .= "
													</tbody>
													</table>
								</td>
							</tr>-->
							<tr height=27>
								<td class='search_box_title' bgcolor='#efefef' align=center>SMS 수신여부 </td>
								<td class='search_box_item' align=left >
									<input type=checkbox name='smssend_yn[]' value='1' id='smssend_y'  ".CompareReturnValue("1",$smssend_yn,"checked")."><label for='smssend_y'>수신(O)</label>
									<input type=checkbox name='smssend_yn[]' value='0' id='smssend_n' ".CompareReturnValue("0",$smssend_yn,"checked")."><label for='smssend_n'>수신거부(X)</label>
								</td>
								<td class='search_box_title' bgcolor='#efefef' align=center>메일 수신여부 </td>
								<td class='search_box_item' align=left >
									<input type=checkbox name='mailsend_yn[]' value='1' id='mailsend_y'  ".CompareReturnValue("1",$mailsend_yn,"checked")."><label for='mailsend_y'>수신(O)</label>
									<input type=checkbox name='mailsend_yn[]' value='0' id='mailsend_n' ".CompareReturnValue("0",$mailsend_yn,"checked")."><label for='mailsend_n'>수신거부(X)</label>
								</td>
							</tr>

							<tr height=27>
								<td class='search_box_title' bgcolor='#efefef' align=center>푸쉬 수신여부 </td>
								<td class='search_box_item' align=left >
									<input type=checkbox name='is_allowable_yn[]' value='1' id='push_y'  ".CompareReturnValue("1",$is_allowable_yn,"checked")."><label for='push_y'>수신(O)</label>
									<input type=checkbox name='is_allowable_yn[]' value='0' id='push_n' ".CompareReturnValue("0",$is_allowable_yn,"checked")."><label for='push_n'>수신거부(X)</label>
								</td>
								<td class='search_box_title' bgcolor='#efefef' align=center> </td>
								<td class='search_box_item' align=left >
								</td>
							</tr>
							<tr height=27>
								<td class='search_box_title' bgcolor='#efefef' align=center>회원구분 </td>
								<td class='search_box_item' align=left >
									<input type=checkbox name='mem_div[]' value='D' id='buyer'  ".CompareReturnValue("D",$mem_div,"checked")."><label for='buyer'>구매회원</label>
									<input type=checkbox name='mem_div[]' value='S' id='cooper' ".CompareReturnValue("S",$mem_div,"checked")."><label for='cooper'>협력사</label>
									<input type=checkbox name='mem_div[]' value='MD' id='staff' ".CompareReturnValue("MD",$mem_div,"checked")."><label for='staff'>직원</label>
								</td>
								<td class='search_box_title' bgcolor='#efefef' align=center>성별</td>
								<td class='search_box_item' align=left style='padding-left:5px;'>
									<input type=checkbox name='sex[]' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
									<input type=checkbox name='sex[]' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
									<input type=checkbox name='sex[]' value='D' id='sex_all'  ".CompareReturnValue("D",$sex,"checked")."><label for='sex_all'>기타</label>
								</td>
							</tr>
							<!--tr  height=27>
								<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>가입일
								<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.searchmember);' ".CompareReturnValue('1',$orderdate,' checked').">
								</th>
								<td class='search_box_item' colspan='3'>
									".search_date('startDate','endDate',$startDate,$endDate)."
								</td>
							</tr-->
							<tr  height=27>
								<th class='search_box_title'>
									<select name='date_type' style='width:80px;'>
										<option value='cu.date' ".CompareReturnValue('cu.date',$date_type,' selected').">가입일</option>
										<option value='cu.last' ".CompareReturnValue('cu.last',$date_type,' selected').">최근로그인</option>
									</select>
									<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked').">
								</th>
								<td class='search_box_item'  colspan=3>
									".search_date('startDate','endDate',$startDate,$endDate)."
								</td>
							</tr>
							<tr height=27>
								<td class='search_box_title' style='padding-top:5px;'>  검색어
									<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' /></span><br>
									<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> (다중검색 체크)
								</td>
								<td class='search_box_item' colspan='3'>
									<table cellpadding=0 cellspacing=0 border='0'>
									<tr>
										<td valign='top'>
											<div style='padding-top:5px;'>
											<select name='search_type' id='search_type'  style=\"font-size:12px;\">
												<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type).">회원명</option>
												<option value='cu.id' ".CompareReturnValue("cu.id",$search_type).">회원ID</option>
												<option value='cmd.pcs' ".CompareReturnValue("cmd.pcs",$search_type).">핸드폰번호</option>
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
						</td>
						<th class='box_06'></th>
					</tr>
					<tr>
						<th class='box_07'></th>
						<td class='box_08'></td>
						<th class='box_09'></th>
					</tr>
				</table>";

				$mstring .= "
						</td>
					</tr>
					<tr height=50>
						<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
					</tr>
				<br></form>";

			if($mode == "search"){
				include "./push_request_member_query.php";
			}

			$mstring .= "
			<tr>
				<td>
				<form id='list_form' name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='member_batch.act.php'  enctype='multipart/form-data' target='test_act'>
				<input type='hidden' name='confirm_bool' id='confirm_bool' value='1'>
				<input type='hidden' name='code[]' id='code'>
				<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
				<div id='result_area'>
				<div style='padding:4px;'>회원수 : ".number_format($total)." 명</div>
				<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
				<tr height='34' style='font-weight:bold' bgcolor='#ffffff'>
					<td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
					<td width='4%' align='center' class='m_td' ><font color='#000000'><b>번호</b></font></td>
					<td width='5%' align='center' class='m_td'><font color='#000000'><b>가입일</b></font></td>
					<td width='7%' align='center' class='m_td'><font color='#000000'><b>최근로그인</b></font></td>
					<td width='8%' align='center' class=m_td><font color='#000000'><b>그룹</b></font></td>
					<td width='12%' align='center' class=m_td><font color='#000000'><b>성명(ID)</b></font></td>
					<td width='10%' align='center' class=m_td><font color='#000000'><b>핸드폰</b></font></td>
					<td width='10%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
					<td width='7%' align='center' class=m_td><font color='#000000'><b>기기타입</b></font></td>
					<td width='4%' align='center' class=m_td><font color='#000000'><b>SMS<br>수신여부</b></font></td>
					<td width='4%' align='center' class=m_td><font color='#000000'><b>이메일<br>수신여부</b></font></td>
					<td width='4%' align='center' class=m_td><font color='#000000'><b>푸시<br>수신여부</b></font></td>
					<td width='5%' align='center' class=m_td><font color='#000000'><b>관리</b></font></td>";
					/*
					<td width='7%' align='center' class=m_td><font color='#000000'><b>로긴수</b></font></td>
					if($admininfo[mall_type] != "H"){
			$mstring .= "
					<td width='7%' align='center' class=m_td><font color='#000000'><b>적립금</b></font></td>";
					}
					$mstring .= "
						<td width='10%' align='center' class=m_td><font color='#000000'><b>최종로그인</b></font></td>
					<td width='10%' align='center' class=e_td><font color='#000000'><b>메일링</b></font></td>
				  </tr>";*/

					for ($i = 0; $i < $db->total; $i++)
					{
						$db->fetch($i);

						$no = $total - ($page - 1) * $max - $i;

						if($db->dbms_type == "oracle"){
							$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid_ = '".$db->dt[code]."' and state in (1,2,5,6,7)");
						}else{
							$mdb->query("SELECT IFNULL(sum(reserve),'-') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in (1,2,5,6,7)");
						}

						$mdb->fetch(0);
						$reserve_sum = number_format($mdb->dt[reserve_sum]);

						if($db->dt[sex_div] == "M"){
							$sex_div_str = "남";
						}else if($db->dt[sex_div] == "W"){
							$sex_div_str = "여";
						}else{
							$sex_div_str = "-";
						}
				$mstring .= "
				  <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
					<td class='list_box_td list_bg_gray' align='center' ><input type=checkbox name=code[] id='code' value='".$db->dt[device_id]."' onClick='input_check_num()'></td>
					<td class='list_box_td' align='center' >".$no."</td>
					<td class='list_box_td list_bg_gray' align='center'><span title=''>".$db->dt[regdate]."</span></td>
					<td class='list_box_td list_bg_gray' align='center'><span title=''>".$db->dt[last]."</span></td>
					<td class='list_box_td point' align='center' nowrap>".$db->dt[gp_name]."</td>
					<td class='list_box_td list_bg_gray' align='center' ><a href=\"javascript:PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',1280,800,'member_view')\"><b>".$db->dt[name]."(".$db->dt[id].")</b></a></td>
					<td class='list_box_td list_bg_gray' align='center' ><b>".$db->dt[pcs]."</b></td>
					<td class='list_box_td list_bg_gray' align='center' ><b>".$db->dt[mail]."</b></td>
					<td class='list_box_td list_bg_gray' align='center' ><b>".($db->dt[os]=="i"?"IOS":"안드로이드")."</b></td>
					<td class='list_box_td' align='center' >".($db->dt[sms]=='1'?"O":"X")."</td>
					<td class='list_box_td' align='center' >".($db->dt[info]=='1'?"O":"X")."</td>
					<td class='list_box_td' align='center' >".($db->dt[is_allowable]=='1'?"O":"X")."</td>
					";
						$mstring .= "
						<td class='list_box_td ctr'  style='padding:5px;' nowrap>";
						if($update_auth){
							$mstring .= "<img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',1280,800,'member_view')\" style='cursor:pointer;' alt='고객상담' title='고객상담'/> ";
						}else{
							$mstring .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle alt='고객상담' title='고객상담' ></a> ";
						}

						if($update_auth){
							$mstring .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('/admin/member/member_info.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" style='cursor:pointer;' alt='수정' title='수정'/> ";
						}else{
							$mstring .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정' ></a> ";
						}

						//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
						if($delete_auth){
							$mstring .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" style='cursor:pointer;' alt='삭제' title='삭제'/> ";
						}else{
							//$mstring .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
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
					";/*<td class='list_box_td' align='center' >".$db->dt[visit]."</td>
					if($admininfo[mall_type] != "H"){
					$mstring .= "
					<td class='list_box_td list_bg_gray' align='center' ><a href=\"javascript:PoPWindow('reserve.pop.php?code=".$db->dt[code]."',650,700,'reserve_pop')\">".$reserve_sum."</a></td>
					<td class='list_box_td' align='center' >".$db->dt[last]."</td>
					<td class='list_box_td list_bg_gray' align='center' >".($db->dt[info] == "1" ? "수신":"비수신")."</td>";
					}else{
					$mstring .= "
					<td class='list_box_td list_bg_gray' align='center' >".$db->dt[last]."</td>
					<td class='list_box_td ' align='center' >".($db->dt[info] == "1" ? "수신":"비수신")."</td>";
					}
					$mstring .= "*/
				";
				  </tr>
					";
					}

				if (!$db->total){

				$mstring .= "
				  <tr height=50>
					<td class='list_box_td' colspan='13' align='center'>";
					if($mode == "search"){
						if($search_false){
							$mstring .= "조회하시고자 하는 검색조건을 선택후 검색해주세요";
						}else{
							$mstring .= "검색결과에 맞는 회원 데이타가 없습니다.";
						}
					}else{
						$mstring .= "조회하시고자 하는 검색조건을 선택후 검색해주세요";
					}
					$mstring .= "
					</td>
				  </tr>";

				}

				$mstring .= "
				</table>
				</form>
				<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
				  <tr height='40'>
					<td colspan=5 align=left>

					</td>
					<td  colspan='6' align='right' >&nbsp;".$str_page_bar."&nbsp;</td>
				  </tr>
				</table>
				</div>
				</td>
				</tr>";


		//아래는 푸쉬 보내기 위한 화면

		$query_string = str_replace("&device_type=I","",str_replace("&device_type=A","",$_SERVER[QUERY_STRING]));

		$mstring .= "
			<tr>
				<td>";
					$mstring .= "
					<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05'  valign=top style='padding:5px'>
							<form name=push_form method='post' id='push_form' onSubmit='return checkMember(\"list_form\");' action='/admin/mobile/appapi/pushService/request.php' enctype='multipart/form-data' target='act' >
							<input type='hidden' name='query_string' value='".urlencode($_SERVER[QUERY_STRING])."'> <!--### 검색한회원한테 보내기 위해 쿼리스트링저장. -->
							<input type='hidden' name='str_code_list' id='str_code_list' value=''> <!--### 검색한회원한테 보내기 위해 쿼리스트링저장. -->
							<input type='hidden' name='device_type' value='".$device_type."'>
							<div style='padding: 5px; 0;'>
								<select name='update_type' onChange='view_member_num(this,\"$total\")'>
									<option value='1'>검색한 회원 전체에게</option>
									<option value='2'>선택한회원 전체에게</option>
								</select>
							</div>

							<div class='tab' style='width:100%;height:32px;margin:0px;'>
								<table width='100%' class='s_org_tab'>
								<tr>
									<td class='tab' >
										<table id='tab_1' ".($device_type == 'A' ? "class='on'" : "").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='push_request_member.php?".$query_string."&device_type=A'\" style='width:100px; text-align:center;'>Android</td>
											<th class='box_03'></th>
										</tr>
										</table>
										<table id='tab_2' ".($device_type == 'I' ? "class='on'" : "").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='push_request_member.php?".$query_string."&device_type=I'\" style='width:100px; text-align:center;'>IOS</td>
											<th class='box_03'></th>
										</tr>
										</table>
									</td>
								</tr>
								</table>
							</div>

							<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
								<col width='15%'>
								<col width='35%'>
								<col width='15%'>
								<col width='35%'>
								<tr height=30>
									<td class='search_box_title'> <b>발송구분</b></td>
									<td class='search_box_item' style='padding-left:5px;' colspan='3'>
										<table cellpadding=0 width='100%'>
												<col width='15%'>
												<col width='10%'>
												<col width='*'>
											<tr>
												<td>
													<input type='radio' name='send_time_type' checked value='0' ".CompareReturnValue("O",$send_time_type,"checked")." class='send_time_now' id='send_time_now' /><label for='send_time_now'>즉시발송</label>
													<input type='radio' name='send_time_type' value='1' ".CompareReturnValue("1",$send_time_type,"checked")." class='send_time_reserve' id='send_time_reserve' /><label for='send_time_reserve'>예약발송</label>
												</td>
												<td>
													".select_date('send_time_sms')."
												</td>
												<td>
													<select name='send_time_hour'>";
													for($i=0;$i < 24;$i++){
														$mstring.= "<option value='".sprintf("%02d", $i)."' ".($sTime == sprintf("%02d", $i) ? "selected":"").">".sprintf("%02d", $i)."</option>";
													}
													$mstring.= "
													</select> 시
													<select name='send_time_minite'>";
													for($i=0;$i < 60;$i+=5){
														$mstring.= "<option value='".sprintf("%02d", $i)."' ".($sMinute == sprintf("%02d", $i) ? "selected":"").">".sprintf("%02d", $i)."</option>";
													}
													$mstring.= "
													</select>분
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr height=30>
								  <td class='search_box_title'>관리용제목</td>
								  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>
									  <input type=text name='title' class='textbox' value='".$title."' style='width:30% ; vertical-align:top;' >
									  <b>* 제목은 발송한 목록에서 확인하기 위한 텍스트로 실제 푸시메시지에는 발송되지 않습니다.</b>
								  </td>
								</tr>
								";

								if($device_type == 'A' || $device_type == '' || $device_type == 'I'){
								$mstring .= "
									<tr height=30>
									  <td class='search_box_title'>푸시제목</td>
									  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>
										  <input type=text name='push_title' class='textbox' value='".$push_title."' style='width:30% ; vertical-align:top;' >
										  <b>* 푸시제목을 입력 안하시면 [".$_SESSION["admininfo"]["company_name"]."]로 보내지게 됩니다.</b>
									  </td>
									</tr>
									<tr height=30>
										<td class='search_box_title'>
											푸시발송타입
										</td>
										<td class='search_box_item' colspan='3'>
											<input type='radio' value='txt' id='push_text' name='contents_type' checked /><label for='push_text'>텍스트</label>
											<!--input type='radio' value='img' id='push_img' name='contents_type' /><label for='push_img'>이미지</label-->
											<input type='radio' value='noti_img' id='noti_img' name='contents_type' /><label for='noti_img'>노티이미지</label>
										</td>
									</tr>";
								}
								else{
									//아이폰일때는 해당 정보를 넘겨준다.
									$mstring .= "<input type='hidden' value='txt' id='push_text' name='contents_type'>";
								}
								$mstring .= "
								<tr height=630>
									<td class='search_box_title'>
										푸시내용
									</td>
									<td class='search_box_item' colspan='3' style='padding:15px;'>
										<div id='contents'>
											<textarea name='contents' class='textbox' style='width:150px;vertical-align:top;resize:none;margin:10px 0' onkeyup=\"fc_chk_byte(this,200, this.form.sms_text_count,'sms');\" ></textarea>
											<br />
											<input type=text name='sms_text_count' style='display:inline;border:0px;text-align:right;width:50px;padding-left:50px' maxlength=4 value=0> byte/<span id='byte'>200byte</span><span id='lms_type'></span><br />
											<b>* 푸시 메시지 내용은 최대 200byte(한글 100자)까지 입력 하실 수 있습니다.</b> <br/> <br/>
										</div>
										<div id='upfile' style='display:none'>
											<input type='file' name='push_img' />
											<b>* 권장사이즈 : 578*987 사이즈의 이미지를 권장합니다.</b>
										</div>
										<div id='notifile' style='display:none'>
											<input type='file' name='noti_img' />
											<b>* 권장사이즈 : 892*393 사이즈의 이미지를 권장합니다.</b>
										</div>
									</td>
								</tr>
								<tr height=30>
								  <td class='search_box_title'>푸시링크</td>
								  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>
									  <input type=text name='link' class='textbox' value='' style='width:97% ; vertical-align:top;' >
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
					</table>";
					$mstring .= "
				</td>
			</tr>
			<tr >
				<td style='padding:10px 0px;' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/btn_send.gif' align=absmiddle id='submit_image' ></td>
			</tr>
		</form>
	</table>";


$help_text = '앱에서 푸시알림 허용한 사용자에게 메시지를 발송합니다.';
$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>푸시메시지 발송</b></td></tr></table>", $help_text,220);

$Contents = $mstring.$help_text;

$P = new LayOut();
$P->addScript = "".$Script;
//$P->OnloadFunction = "init();";
$P->Navigation = "모바일 푸시 메세지 관리 > 메시지 발송 (회원전용)";
$P->title = "메시지 발송 (회원전용)";
$P->strLeftMenu = mshop_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();
