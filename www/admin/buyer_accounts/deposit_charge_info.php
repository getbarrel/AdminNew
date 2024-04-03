<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

if ($sdate == "" || $edate == "" ){		//기본설정 시간
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sdate = date("Y-m-d", $before10day);
	$edate = date("Y-m-d");

}

$Script ="
<script language='JavaScript' >
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
</script>
";
//처리상태 history_type (1:입금대기 2:입금취소 3:입금완료 4:사용완료 5:출금요청 6:출금취소 7:출금확정 8:송금완료)
$sql = "select 
				SUM(case when history_type = 1 then deposit else 0 end) as deposit_ready,
				SUM(case when history_type = 2 then deposit else 0 end) as deposit_cancel,
				SUM(case when history_type = 3 then deposit else 0 end) as deposit_complete,
				SUM(case when history_type = 5 then deposit else 0 end) as deposit_return_request,
				SUM(case when history_type = 6 then deposit else 0 end) as deposit_return_cancel,
				SUM(case when history_type = 7 then deposit else 0 end) as deposit_return_complete,
				SUM(case when history_type = 8 then deposit else 0 end) as deposit_remittance_complete
			from 
				shop_deposit_charge_info ";
$db->query($sql);
$db->fetch();
$deposit_ready = $db->dt[deposit_ready];
$deposit_cancel = $db->dt[deposit_cancel];
$deposit_complete = $db->dt[deposit_complete];
$deposit_return_request = $db->dt[deposit_return_request];
$deposit_return_cancel = $db->dt[deposit_return_cancel];
$deposit_return_complete = $db->dt[deposit_return_complete];
$deposit_remittance_complete = $db->dt[deposit_remittance_complete];

$sql = "select sum(deposit) all_deposit from ".TBL_COMMON_USER." where deposit > 0 ";
$db->query($sql);
$db->fetch();
$all_deposit = $db->dt[all_deposit];




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

include "./deposit_where.php";



$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>예치금 관리</b></td></tr>
</table>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width='12%' />
	<col width='12%' />
	<col width='12%' />
	<col width='12%' />
	<col width='12%' />
	<col width='12%' />
	<col width='12%' />
	<col width='*' />

	<tr height='30' bgcolor='#ffffff'>
		<td align='center' class=s_td colspan='3'><font color='#000000'><b>입금처리상태</b></font></td>
		<td align='center' class=m_td colspan='4'><font color='#000000'><b>출금처리상태</b></font></td>
		<td align='center' class=e_td rowspan='2'><font color='#000000'><b>현재 총 보유</b></font></td>
	</tr>
	<tr height='30' bgcolor='#ffffff'>
		<td  align='center' class=s_td><font color='#000000'><b>입금대기</b></font></td>
		<td  align='center' class='m_td'><font color='#000000'><b>입금취소</b></font></td>
		<td  align='center' class='m_td'><font color='#000000'><b>입금확정</b></font></td>
		<td  align='center' class='m_td'><font color='#000000'><b>출금요청</b></font></td>
		<td  align='center' class='m_td'><font color='#000000'><b>출금취소</b></font></td>
		<td  align='center' class='m_td'><font color='#000000'><b>출금확정</b></font></td>
		<td  align='center' class='m_td'><font color='#000000'><b>송금확정</b></font></td>
	</tr>
	<tr height='30'>
		<td class='list_box_td' >".number_format($deposit_ready)."</td>
		<td class='list_box_td'>".number_format($deposit_cancel)."</td>
		<td class='list_box_td'>".number_format($deposit_complete)."</td>
		<td class='list_box_td'>".number_format($deposit_return_request)."</td>
		<td class='list_box_td'>".number_format($deposit_return_cancel)."</td>
		<td class='list_box_td'>".number_format($deposit_return_complete)."</td>
		<td class='list_box_td'>".number_format($deposit_remittance_complete)."</td>
		<td class='list_box_td'>".number_format($all_deposit)."</td>
	</tr>
</table>
<br><br>";

$Contents01 .= "
	<form name='searchmember' style='display:inline;'>
	<input type='hidden' name='info_type' value='$info_type'>
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='mem_ix' value='$mem_ix'>
	<table border='0' cellpadding='0' cellspacing='0' width='100%'>
	<tr height=35><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>예치금 검색하기</b></td></tr>
	<tr>
		<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td class='box_05' valign=top style='padding:0px;'>
					<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
					<col width=20%>
					<col width=30%>
					<col width=18%>
					<col width=32%>
					<!--tr height=27>
						<td class='search_box_title' >회원구분 </td>
						<td class='search_box_item' >
							<input type=checkbox name='nationality[]' value='I' id='nationality_I'  ".CompareReturnValue("I",$nationality,"checked")."><label for='nationality_I'>국내회원</label>&nbsp;&nbsp;
							<input type=checkbox name='nationality[]' value='O' id='nationality_O'  ".CompareReturnValue("O",$nationality,"checked")."><label for='nationality_O'>해외회원</label>
						</td>
						<td class='search_box_title' >회원타입 </td>
						<td class='search_box_item' >
							<input type=checkbox name='mem_type[]' value='M' id='mem_type_m'  ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem_type_m'>일반회원</label>&nbsp;&nbsp;
							<input type=checkbox name='mem_type[]' value='C' id='mem_type_c' ".CompareReturnValue("C",$mem_type,"checked")."><label for='mem_type_c'>사업자회원</label>&nbsp;&nbsp;
							<input type=checkbox name='mem_type[]' value='S' id='mem_type_S' ".CompareReturnValue("F",$mem_type,"checked")."><label for='mem_type_S'>셀러회원</label>
						</td>
					</tr-->
					<tr height=27>
						<td class='search_box_title' bgcolor='#efefef' align=center>
							<label for='search_check'><b>처리일자</b></label>
							<select name='search_history_type'>
								<option value='regdate' ".CompareReturnValue("regdate",$search_history_type,"selected").">입금대기일</option>
								<option value='ic_date' ".CompareReturnValue("ic_date",$search_history_type,"selected").">입금일</option>
								<option value='cc_date' ".CompareReturnValue("cc_date",$search_history_type,"selected").">취소일</option>
							</select>
							<input type='checkbox' name='search_check' id='search_check' value='1' onclick='ChangeRegistDate(document.searchmember);'".CompareReturnValue("1",$search_check,"checked").">
						</td>
						<td class='search_box_item' colspan='3'>
							".search_date('sdate','edate',$sdate,$edate)."
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' bgcolor='#efefef' align=center><b>입금 처리상태</b></td>
						<td class='search_box_item' colspan='3'>
						<input type=checkbox name='history_type[]' value='1' id='state_1'  ".CompareReturnValue('1',$history_type,"checked")." ><label for='state_1'>&nbsp;입금대기</label>&nbsp;
						<input type=checkbox name='history_type[]' value='2' id='state_2' ".CompareReturnValue('2',$history_type,"checked")."><label for='state_2'>&nbsp;입금취소</label>&nbsp;
						<input type=checkbox name='history_type[]' value='3' id='state_3' ".CompareReturnValue('3',$history_type,"checked")."><label for='state_3'>&nbsp;입금완료</label>&nbsp;
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' bgcolor='#efefef' align=center><b>출금 처리상태</b></td>
						<td class='search_box_item' colspan='3'>
						<input type=checkbox name='history_type[]' value='5' id='state_5'  ".CompareReturnValue('5',$history_type,"checked")." ><label for='state_5'>&nbsp;출금요청</label>&nbsp;
						<input type=checkbox name='history_type[]' value='6' id='state_6' ".CompareReturnValue('6',$history_type,"checked")."><label for='state_6'>&nbsp;출금취소</label>&nbsp;
						<input type=checkbox name='history_type[]' value='7' id='state_7' ".CompareReturnValue('7',$history_type,"checked")."><label for='state_7'>&nbsp;출금확정</label>&nbsp;
						<input type=checkbox name='history_type[]' value='8' id='state_8' ".CompareReturnValue('8',$history_type,"checked")."><label for='state_8'>&nbsp;송금확정</label>&nbsp;
						</td>
					</tr>
					<tr>
						<td class='search_box_title'>검색어
						<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'></span>
						
						<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> <label for='mult_search_use'>(다중검색 체크)</label> <img src='/admin/images/icon_q.gif' align=absmiddle/>
						</td>
						<td class='search_box_item' colspan='3'>
							<table cellpadding=0 cellspacing=0 border='0'>
							<tr>
								<td valign='top'>
									<div style='padding-top:5px;'>
									<select name='search_type' id='search_type'  style=\"font-size:12px;\">
									<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type).">회원명</option>
									<option value='cu.id' ".CompareReturnValue("cu.id",$search_type).">아이디</option>
									
									</select>
									</div>
								</td>
								<td style='padding:5px;'>
									<div id='search_text_input_div'>
										<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='width: 150px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
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
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=3 align=center style='padding:10px 0;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr>
	</table>
	</form>";

//쿼리 영역
$sql = "select * from shop_deposit_charge_info $where";
$db->query($sql);
$total = $db->total;

include "./deposit_excel.php";

//쿼리 영역
$sql = "select * from shop_deposit_charge_info $where order by history_ix desc limit $start, $max";
//echo $sql;
$db->query($sql);
$deposit_history = $db->fetchall();


$Contents01 .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr height=30 >
		<td>
			<b>전체 : ".$total." 개</b> 
		</td>
		<td colspan=5 align=right>";

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents01 .= " <a href='?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents01 .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
	
$Contents01 .= "
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	<form name=reserve_list method=post action='deposit.act.php' onsubmit='return CheckDelete(this)' target=''>
	<input type='hidden' name='act' value='deposit_select_update'>
	<input type='hidden' name='info_type' value='".$info_type."'>
	<!--col width=3%-->
	<col width=12%>

	<col width=10%>
	<col width=10%>
	<col width=10%>
	<col width=10%>
	<col width=12%>
	<col width=*>
	<tr bgcolor=#efefef style='font-weight:bold' align=center height=28>
		<!--td class='s_td'><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.reserve_list)'></td-->
		<td class='m_td'>신청일/변경일</td>
		<td class='m_td'>처리상태</td>
		<td class='m_td'>입/출금 금액</td>		
		<td class='m_td'>내역</td>
		<td class='m_td'>회원명/ID</td>
		<td class='e_td'>관리</td>
	</tr>";
	if(count($deposit_history) > 0){
		foreach($deposit_history  as $val){

			switch($val[history_type]){
				case '1':
					$use_type = '입금대기';
					$change_date = "";
					break;
				case '2':
					$use_type = '입금취소';
					$change_date = "<br>".$val[cc_date];
					break;
				case '3':
					$use_type = '입금완료';
					$change_date = "<br>".$val[ic_date];
					break;
				case '5':
					$use_type = '출금요청';
					break;
				case '6':
					$use_type = '출금취소';
					$change_date = "<br>".$val[change_date];
					break;
				case '7':
					$use_type = '출금확정';
					$change_date = "<br>".$val[change_date];
					break;
				case '8':
					$use_type = '송금확정';
					$change_date = "<br>".$val[change_date];
					break;
			}

			$Contents01 .= "
			<tr height=28 align=center>
				<!--td class='list_box_td list_bg_gray' ><input type=checkbox class=nonborder id='reserve_id' name=rid[] value='".$data_array[$i][history_ix]."'></td-->
				<td class='list_box_td' bgcolor='#ffffff'>".$val[regdate]."".$change_date."</td>
				<td class='list_box_td' bgcolor='#ffffff'>".$use_type."</td>				
				<td class='list_box_td list_bg_gray'>".number_format($val[deposit])."</td>
				<td class='list_box_td' >".$val[etc]."</td>
				<td class='list_box_td list_bg_gray'> ".get_member_name($val[uid])."/".get_member_id($val[uid])."</td>
";

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$Contents01 .= "
					<td class='list_box_td' style='padding:3px;'>
						<img src='../images/".$admininfo[language]."/btn_detail_view.gif' onclick=\"PoPWindow('deposit_status.php?oid=".$val[oid]."',750,550,'reserve_pop')\" align='absmiddle'style='cursor:pointer;' >
						";
						if($val[history_type] == '1'){
							$Contents01 .= "
							<img src='../images/icon/deposit_0722_cc.gif' border=0 align=absmiddle  style='cursor:pointer;' onclick=\"deposit_cancel('".$val[history_ix]."')\";>
							<img src='../images/icon/deposit_0722_ic.gif' border=0 align=absmiddle  style='cursor:pointer;' onclick=\"deposit_in_complete('".$val[history_ix]."')\";>";
						}else if($val[history_type] == '5'){
							$Contents01 .= "
							<img src='../images/icon/deposit_0722_wc.gif' border=0 align=absmiddle  style='cursor:pointer;'  onclick=\"deposit_w_cancel('".$val[history_ix]."')\";>
							<img src='../images/icon/deposit_0722_fixed.gif' border=0 align=absmiddle  style='cursor:pointer;'  onclick=\"deposit_w_complete('".$val[history_ix]."')\";>
							";

						}
						$Contents01 .= "					
						
						<!--img src='../images/icon/deposit_0722_w.gif' border=0 align=absmiddle  style='cursor:pointer;'>
						<img src='../images/icon/deposit_0722_use.gif' border=0 align=absmiddle  style='cursor:pointer;'-->
					</td>";
				}else{
						
$Contents01 .= "<td class='list_box_td' ><a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>";
				}
$Contents01 .= "
			</tr>";

		}
	}else{
		$Contents01 .= "
			<tr height=60><td class='list_box_td' colspan=7 align=center>예치금 내용이 없습니다.</td></tr>";
	}
$Contents01 .= "</form>";




			

$Contents01 .= "
		</table>";

$Contents01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
<col width='10%'>
<col width='*'>
<tr height=40>";
/*
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
	$Contents01 .= "
	<td align=left><a href=\"JavaScript:SelectDelete(document.forms['reserve_list']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a></td>";
}else{
	$Contents01 .= "
	<td align=left><a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a></td>";
}*/
$Contents01 .= "
	
</tr>
</table>";
$Contents = $Contents01;

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("예치금 신청관리", $help_text);
$Script .= "
	<script language='javascript' src='./deposit_charge_info.js'></script>
";

$P = new LayOut();
$P->addScript = "".$Script;//<script language='javascript' src='../include/DateSelect.js'></script>\n
$P->OnloadFunction = "";
$P->strLeftMenu = buyer_accounts_menu();
$P->Navigation = "구매자정산관리 > 예치금 신청관리";
$P->title = "예치금 신청관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>