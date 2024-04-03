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

/*if($mode =="search"){
	$orderdate=1;
}*/

//날짜검색
if($orderdate){
	$where = "AND date_format($date_type,'%Y-%m-%d') between  '$startDate' and '$endDate' ";
}

//담당자 검색
if($ta_charger){
	$where .= "AND ta_charger_ix = '$ta_charger'";
}

//업무상태 검색
if(is_array($qa_state)){
    for($i=0;$i < count($qa_state);$i++){
        if($qa_state[$i] != ""){
            if($qa_state_str == ""){
                $qa_state_str .= "'".$qa_state[$i]."'";
            }else{
                $qa_state_str .= ",'".$qa_state[$i]."' ";
            }
        }
    }

    if($qa_state_str != ""){
        $where .= " AND qa_state in ($qa_state_str) ";
    }
}else{
    if($qa_state){
        $where .= " AND qa_state = '$qa_state' ";
    }
}

//안내수단 검색
if(is_array($aw_type)){
    for($i=0;$i < count($aw_type);$i++){
        if($aw_type[$i] != ""){
            if($aw_type_str == ""){
                $aw_type_str .= "'".$aw_type[$i]."'";
            }else{
                $aw_type_str .= ",'".$aw_type[$i]."' ";
            }
        }
    }

    if($aw_type_str != ""){
        $where .= " AND aw_type in ($aw_type_str) ";
    }
}else{
    if($aw_type){
        $where .= " AND aw_type = '$aw_type' ";
    }
}

//조건검색
if($search_text){
	$where .= "AND $search_type = '$search_text'";
}

if($mem_ix){
	$where .= " and ucode = '$mem_ix' ";
}
//담당자 검색쿼리
$sql = "SELECT 
			ta_charger , ta_charger_ix 
		FROM 
			shop_member_talk_history
		WHERE 1 $where
		GROUP BY ta_charger_ix
		";

$sdb->query($sql);
$account = $sdb->fetchall();

	$mstring .="<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-top:10px;'>
			<tr>
				<td align='left' colspan=6 >".GetTitleNavigation("회원관리", "콜백요청")."</td>
			</tr>";

		if($menu != 'non'){

			$mstring .="
			<tr>
				<td align='left' colspan=4 style='padding-bottom:15px;'>
					 <div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>							
							<table id='tab_03' class='on'>
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
			";
		}
			$mstring .=	"
			<tr>
				<td>
				<form name='searchmember' method='GET'>
				<input type='hidden' name='mode' value='search' />
				<input type='hidden' name='mem_ix' value='$mem_ix'/>
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
															<option value='regdate' ".CompareReturnValue('regdate',$date_type,' selected').">등록일자</option>
															<option value='moddate' ".CompareReturnValue('moddate',$date_type,' selected').">수정일자</option>
														</select>
														<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.searchmember);' ".CompareReturnValue('1',$orderdate,' checked').">
													</th>
													<td class='search_box_item' colspan='3'>
														".search_date('startDate','endDate',$startDate,$endDate)."
													</td>
												 </tr>
												 <tr height='27'>
													<!--<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>유입분류</th>
													<td class='search_box_item'>
														<input type='checkbox' name='send_type[]' id='send_auto' value='A' ".CompareReturnValue("A",$send_type,"checked")."><label for='send_auto'>1단계</label>
														<input type='checkbox' name='send_type[]' id='send_hand' value='M' ".CompareReturnValue("M",$send_type,"checked")."><label for='send_hand'>2단계</label>
													</td>-->
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>안내수단</th>
													<td class='search_box_item' colspan='3'>
														<input type='checkbox' name='aw_type[]' id='aw_sms' value='S' ".CompareReturnValue("S",$aw_type,"checked")."><label for='aw_sms'>SMS</label>
														<input type='checkbox' name='aw_type[]' id='aw_email' value='E' ".CompareReturnValue("E",$aw_type,"checked")."><label for='aw_email'>이메일</label>
														<input type='checkbox' name='aw_type[]' id='aw_call' value='T' ".CompareReturnValue("T",$aw_type,"checked")."><label for='aw_call'>콜백</label>
													</td>
												 </tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>담당자</th>
													<td class='search_box_item'>
														<select name='ta_charger'>
															
															";
															if($account){
																$mstring .= "<option value=''>선택</option>";
																foreach($account as $val){
																	$mstring .=($val['ta_charger'] ? "<option value=".$val['ta_charger_ix']." ".CompareReturnValue($val['ta_charger_ix'],$ta_charger,"selected").">".$val['ta_charger']."</option>" : '');
																}
															}else{
																$mstring .=($val['ta_charger'] ? "<option value=''>없음</option>" : '');
															}

															$mstring .="
														</select>
													</td>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>업무상태</th>
													<td class='search_box_item'>
														<input type='checkbox' name='qa_state[]' id='aw_wait' value='W' ".CompareReturnValue("W",$qa_state,"checked")."><label for='aw_wait'>접수중</label>
														<input type='checkbox' name='qa_state[]' id='aw_ing' value='I' ".CompareReturnValue("I",$qa_state,"checked")."><label for='aw_ing'>처리중</label>
														<input type='checkbox' name='qa_state[]' id='aw_delay' value='D' ".CompareReturnValue("D",$qa_state,"checked")."><label for='aw_delay'>처리완료</label>
														<input type='checkbox' name='qa_state[]' id='aw_finish' value='F' ".CompareReturnValue("F",$qa_state,"checked")."><label for='aw_finish'>처리완료</label>
														<input type='checkbox' name='qa_state[]' id='aw_cancel' value='C' ".CompareReturnValue("C",$qa_state,"checked")."><label for='aw_cancel'>처리완료</label>
													</td>
												 </tr>
												<tr height=27>
													<td class='search_box_title'>조건검색</td>
													<td class='search_box_item' colspan='3'>
														<table cellpadding=0 cellspacing=0 border='0'>
														<tr>
															<td valign='top'>
																<div style='padding-top:5px;'>
																<select name='search_type' id='search_type'  style=\"font-size:12px;\">
																	<option value='user_name' ".CompareReturnValue("user_name",$search_type).">회원이름</option>
																	<option value='user_phone' ".CompareReturnValue("user_phone",$search_type).">연락처</option>
																	<option value='ta_counselor' ".CompareReturnValue("ta_counselor",$search_type).">상담자</option>
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
				*
			FROM
				shop_member_talk_history
			WHERE 1 $where
			ORDER BY regdate DESC
			";

	$db->query($sql);
	$total = $db->total;
	
	$sql = "SELECT 
				*
			FROM
				shop_member_talk_history
			WHERE 1 $where
			ORDER BY regdate DESC
			LIMIT $start,$max
			";

	$db->query($sql);

	$mstring .="<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='27' bgcolor='#ffffff'>
	<td width='8%' align='center' class='m_td'><font color='#000000'><b>일시</b></font></td>
    <td width='10%' align='center' class='m_td'><font color='#000000'><b>상담번호<br />고객명(아이디)<br />주문번호</font></td>
    <td width='5%' align='center' class='m_td'><font color='#000000'><b>작성자</b></font></td>
	<td width='10%' align='center' class='m_td' nowrap><font color='#000000'><b>안내수단</b></font></td>
    <td width='8%' align='center' class='m_td' nowrap><font color='#000000'><b>부서<br />처리자</b></font></td>
    <td width='8%' align='center' class=m_td nowrap><font color='#000000'><b>업무내용</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>업무상태</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>처리상태<br />처리시간</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>관리</b></font></td>
  </tr>";
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
		$regdate = explode(" " , $db->dt['regdate']);

		if($db->dt['aw_type']){
			$ans_type	=	explode(',' , $db->dt['aw_type']);
            $answer = "";
			for($j=0;$j< count($ans_type);$j++){
				$answer .= ",".$ans_type[$j];
			}
			$answer = str_replace("S","SMS",$answer);
			$answer = str_replace("E","이메일",$answer);
			$answer = str_replace("T","콜백",$answer);
			$answer = substr($answer, 1); 
		}else{
			$answer	= '미요청';
		}


		switch($db->dt['qa_state']){
			case "W" :  $qa_state = "접수중";
				break;
			case "I" :  $qa_state = "처리중";
				break;
			case "D" :  $qa_state = "처리지연";
				break;
			case "F" :  $qa_state = "처리완료";
				break;
			case "C" :  $qa_state = "처리취소";
				break;
			default  :  $qa_state = "-";
				break;

			return $qa_state;
		}

		switch($db->dt['aw_state']){
			case "W" :  $aw_state = "접수중";
				break;
			case "I" :  $aw_state = "처리중";
				break;
			case "D" :  $aw_state = "처리지연";
				break;
			case "F" :  $aw_state = "처리완료";
				break;
			case "C" :  $aw_state = "처리취소";
				break;
			default  :  $aw_state = "-";
				break;

			return $aw_state;
		}		
		
		$no = $total - ($page - 1) * $max - $i;

        $mstring = $mstring."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td' >".$regdate[0]."<br />".$regdate[1]."</td>
			<td class='list_box_td' >".$db->dt['ta_code']."<br />".$db->dt['user_name']."(".$db->dt['user_id'].")<br />
			<a href=\"javascript:PopSWindow('../order/orders.edit.php?oid=".$db->dt['oid']."&mmode=".$mmode."&mem_ix=".$mem_ix."',960,800);\" >".$db->dt['oid']."</a>
			</td>
            <td class='list_box_td' style='padding:0px 5px;' nowrap>".$db->dt['ta_counselor']."</td>
			<td class='list_box_td' nowrap>".$answer."</td>
			<td class='list_box_td' nowrap>".$db->dt['ta_charger']."</td>
            <td class='list_box_td' >".($db->dt['ta_memo'] ? $db->dt['ta_memo'] : '-' )."</td>
            <td class='list_box_td' >".$qa_state."</font></td>
            <td class='list_box_td' >".($aw_state ? $aw_state : '')."<br />".($aw_state == "처리완료" ? $db->dt['moddate'] : '' )."</td>
			";
			$mstring .= "
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";
				$mstring .= "<img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow2('member_cti.php?mmode=pop&code=".$db->dt[ucode]."&mmode=pop',1280,800,'member_view')\" style='cursor:pointer;' alt='고객상담' title='고객상담'/> ";

				/*
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
				*/
			 $mstring .= "
			 <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[ucode]."',500,380,'sendsms')\" style='cursor:pointer;' alt='문자발송' title='문자발송'>
			 <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[ucode]."',550,535,'sendmail')\" style='cursor:pointer;' alt='이메일발송' title='이메일발송'>
			 ";
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

if($mmode == "personalization"){

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

}else{
	
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
	/*
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = member_menu();
	$P->Navigation = "회원관리 > 콜백요청";
	$P->title = "전체회원";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
	*/
}
?>
