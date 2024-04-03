<?
include("../class/layout.class");
//auth(9);
$script_times[page_start] = time();
$db = new Database;
$mdb = new Database;
$mdb2 = new Database;
$mdb3 = new Database;

//$db->debug = true;//페이지에서 오류가 없는데 쿼리를 찍기에 주석처리함 kbk 13/02/04
$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

$startDate = $cmd_sdate;
$endDate = $cmd_edate;

if($regdate == '1'){	//가입일자
	if($startDate != "" && $endDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cu.date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			$count_where .= " and  to_char(cu.date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
		}else{
			$where .= " and date_format(cu.date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			$count_where .= " and date_format(cu.date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
		}
	}
}

$vstartDate = $slast;
$vendDate = $elast;

if($visitdate == '1'){
	if($vstartDate != "" && $vendDate != ""){	//최근방문일
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cu.last , 'YYYY-MM-DD') between  '".$vstartDate."' and '".$vendDate."' ";
			$count_where .= " and  to_char(cu.last , 'YYYY-MM-DD') between '".$vstartDate."' and '".$vendDate."' ";
		}else{
			$where .= " and date_format(cu.last,'%Y-%m-%d') between  '".$vstartDate."' and '".$vendDate."' ";
			$count_where .= " and date_format(cu.last,'%Y-%m-%d') between  '".$vstartDate."' and '".$vendDate."' ";
		}
	}
}


if($max == ""){
	$max = 15; //페이지당 갯수
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

//include "member_query.php";

$Script = "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'member.php?view='+sort;
}

function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		$('input[name=cmd_sdate]').attr('disabled',false);
		$('input[name=cmd_edate]').attr('disabled',false);

	}else{
		$('#cmd_sdate').attr('disabled',true);
		$('#cmd_edate').attr('disabled',true);
	}
}

function ChangeVisitDate(frm){
	if(frm.visitdate.checked){
		$('input[name=slast]').attr('disabled',false);
		$('input[name=elast]').attr('disabled',false);
	}else{
		$('input[name=slast]').attr('disabled',true);
		$('input[name=elast]').attr('disabled',true);
	}
}

function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate','$sDate2','$eDate2','$sDate3');
}


function deleteMemberInfo(act, code){
 	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
 	}
}
</script>";

$Contents = "
<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
<tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("전체회원관리", "회원관리 > 전체회원관리 ")."</td>
</tr>
<tr>
	<td>";

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	<tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "member" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='member.php?info_type=member'>전체회원리스트</a> <span style='padding-left:2px' class='helpcloud' help_width='410' help_height='70' help_html='사업자 정보를 작성하여 등록한 후에 해당 사업자에 관리자화면 로그인 정보를 발급해 줄 수 있습니다.<br />사업자 정보 추가 후 [목록관리] - [사용자 추가]를 통해 관리자에 계정을 발급해 주시기 바랍니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "basic_member" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "<a href='basic_member.php?info_type=basic_member'>회원가입/매출기초분석</a>";

						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($info_type == "cust_member" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "<a href='cust_member.php?info_type=cust_member'>회원별상세매출분석</a>";

						$Contents .= "

						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>
	</table>";


$Contents .= "
	<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
		<tr>
			<th class='box_01'></th>
			<td class='box_02'></td>
			<th class='box_03'></th>
		</tr>
		<tr>
			<th class='box_04'></th>
			<td class='box_05'  valign=top style='padding:5px 0px 5px 0px'>
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
				<form name=searchmember method='get'><!--SubmitX(this);'-->
				<input type='hidden' name=mc_ix value='".$mc_ix." '>
				<input type='hidden' name=info_type value='basic_member'>
				<input type='hidden' name=info_type_detail value='$info_type_detail'>
				<col width='18%'>
				<col width='*'>";

	 $Contents .= "
					<tr height=27>
						<td class='search_box_title' ><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
						<td class='search_box_item'  colspan=3 >
							".search_date('cmd_sdate','cmd_edate',$cmd_sdate,$cmd_edate)."
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' ><label for='visitdate'>최근방문일</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);' ".CompareReturnValue("1",$visitdate,"checked")."></td>
						<td class='search_box_item'  colspan=3  >
							".search_date('slast','elast',$slast,$elast)."
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

$Contents .= "
	</td>
	</tr>
	<tr height=50>
		<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
	</tr>
</table><br></form>";


$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='800px'>
			<col width='*'>
			<tr>
				<td class=''>
					<table id='tab_01' ".(($info_type_detail == "group" || $info_type_detail == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='?info_type=basic_member&info_type_detail=group'>회원/매출분석<b>(그룹별)</b></a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
					<!--<table id='tab_02' ".($info_type_detail == "addr" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
							$Contents .= "<a href='?info_type_detail=addr&info_type=basic_member'>회원/매출분석<b>(지역별)</b></a>";
							//$Contents .= "<a href='javascript:alert(/'DB 부화로 인하여 수정이 필요합니다.')'>회원/매출분석<b>(지역별)</b></a>";
						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($info_type_detail == "member_type" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
							$Contents .= "<a href='?info_type_detail=member_type&info_type=basic_member'>회원/매출분석<b>(회원타입별)</b></a>";
						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				
					<table id='tab_04' ".($info_type_detail == "join" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
							$Contents .= "<a href='?info_type_detail=join&info_type=basic_member'>가입/탈퇴분석<b>(일자별)</b></a>";
						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>-->
				</td>
			
			</tr>
			</table>
		</div>
	    </td>
	</tr>
	</table>";

switch($info_type_detail){
	case 'group':
		$name1 = "그룹등급";
		$name2 = "그룹명";
		break;
	case 'addr':
		$name1 = "지역우편번호";
		$name2 = "지역명";
		break;
	case 'member_type':
		$name1 = "회원타입";
		$name2 = "회원타입";
		break;
	case 'join':
		$name1 = "그룹등급";
		$name2 = "그룹명";
		break;
	case '':
		$name1 = "그룹등급";
		$name2 = "그룹명";
		break;
}
if($info_type_detail == "group" || $info_type_detail == ""){
	$colspan = '3';
}else{
	$colspan = '2';
}

$Contents .= "
<form name='list_frm'>
<input type='hidden' name='code[]' id='code'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
<tr height='27' bgcolor='#ffffff'>
	<!--td width='10' align='center' class=s_td rowspan=2><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td-->
	<td width='5%' align='center' class='m_td' rowspan=2><font color='#000000'><b>순번</b></font></td>";
	if($info_type_detail == "group" || $info_type_detail == ""){
		$Contents .= "  <td width='6%' align='center' class='m_td' rowspan=2><font color='#000000'><b>".$name1."</b></font></td>";
	}

$Contents .= "
	<td width='8%' align='center' class='m_td' nowrap rowspan=2><font color='#000000'><b>".$name2."</b></font></td>
	<td width='5%' align='center' class='m_td' colspan=2 nowrap><font color='#000000'><b>회원</b></font></td>
	<td width='20%' align='center' class=m_td  colspan=2><font color='#000000'><b>매출금액</b></font></td>
	<td width='20%' align='center' class=m_td  colspan=2><font color='#000000'><b>마일리지(사용)</b></font></td>
	<td width='12%' align='center' class=m_td colspan=2><font color='#000000'><b>포인트(사용)</b></font></td>
</tr>
<tr height='27' bgcolor='#ffffff'>
	<td width='5%' align='center' class='m_td'><font color='#000000'><b>회원수</b></font></td>
	<td width='40' align='center' class=m_td ><font color='#000000'><b>비율(%)</b></font></td>
	<td width='12%' align='center' class=m_td><font color='#000000'><b>매출액(원)</b></font></td>
	<td width='40' align='center' class=m_td><font color='#000000'><b>비율(%)</b></font></td>
	<td width='12%' align='center' class=m_td><font color='#000000'><b>마일리지(%)</b></font></td>
	<td width='40' align='center' class=m_td><font color='#000000'><b>비율(%)</b></font></td>
	<td width='12%' align='center' class=m_td><font color='#000000'><b>포인트(%)</b></font></td>
	<td width='40' align='center' class=e_td><font color='#000000'><b>비율(%)</b></font></td>
</tr>";


if($info_type_detail == "group" or $info_type_detail == ""){
 
$sql = "select gp_ix, gp_level, gp_name from shop_groupinfo order by gp_ix asc";
$db->query($sql);
$total =  $db->total;

//$str_page_bar = page_bar($total, $page,$max, "$into_type=member&info_type_detail=group&max=$max&update_kind=$update_kind&search_type=$search_type&search_text=$search_text&region=$region&gp_ix=$gp_ix&age=$age&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&mem_type=$mem_type&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");//gp_level 을 사용하지 않아서 gp_ix 로 바꿈 kbk 13/02/19

$Contents .= "
		<tr bgcolor=#ffffff height=27 >
			<td colspan='".$colspan."' class=m_td><b><font color='#333333'>총합계</font></b></td>
			<td class='m_td inner_warehouse_move'><b>{member_cnt_sum}</b></td>
			<td class='m_td inner_warehouse_move'><b>{group_member_rate_sum}%</b></td>
			<td class='m_td inner_warehouse_move'><b>{group_price_sum}원</b></td>
			<td class='m_td inner_warehouse_move'><b>{group_price_rate_sum}%</b></td>
			<td class='m_td inner_warehouse_move'><b>{group_reserve_sum}원</b></td>
			<td class='m_td inner_warehouse_move'><b>{group_reserve_rat_sum}%</b></td>
			<td class='m_td inner_warehouse_move'><b>{group_point_sum}원</b></td>
			<td class='m_td inner_warehouse_move'><b>{group_point_rate_sum}%</b></td>
		</tr>";
$script_times[total_member_start] = time();
		///////////////// 토탈 회원//////////////////////
		$cnt_sql = "select count(*) as total from common_user as cu inner join common_member_detail as cmd on (cu.code = cmd.code) where 1";
		$mdb->query($cnt_sql);
		$mdb->fetch();
		$member_total = $mdb->dt[total];
		///////////////// 토탈 회원//////////////////////
$script_times[total_member_end] = time();
$script_times[loop_start] = time();
	//////////////////////매출액 비율 시작/////////////////////////
	$price_total = "select 
						sum(pt_dcprice) as total_price 
					from 
						shop_order_detail
					where 
						status in ('IC','IR','DR','DI','DC')";
	//echo nl2br($price_total);
	//exit;
	$mdb->query($price_total);
	$mdb->fetch();
	$total_price = $mdb->dt[total_price];

	for ($i = 0; $i < $db->total; $i++){
		$db->fetch($i);
		$script_times["loop_start".$i] = time();
		$no = $total - ($page - 1) * $max - $i;
		
		$gp_level = $db->dt[gp_level];
		$gp_name = $db->dt[gp_name];

		$sql = "select 
				count(distinct cmd.code) as member_cnt,
				sum(distinct if(od.status in ('IC','IR','DR','DI','DC') ,od.pt_dcprice,0)) as group_price
			from common_member_detail as cmd 				
				left join shop_order as o on (cmd.code = o.user_code)
				left join shop_order_detail as od on (od.oid = o.oid)
			where
				1
				$where
				and cmd.gp_ix = '".$db->dt[gp_ix]."'
				group by cmd.gp_ix order by cmd.gp_ix asc";
		//left join common_user as cu on (cmd.code = cu.code)
		if($no == 1){
		//	echo nl2br($sql);
		//	exit;
		}
		
		$mdb->query($sql);
		$mdb->fetch();


		$member_cnt = $mdb->dt[member_cnt];
		$group_price = $mdb->dt[group_price];
		
		$sql = "select
					sum( distinct if(r.state in ('2'),r.reserve,0)) as group_reserve
				from
					shop_reserve as r
					inner join common_user as cu on (r.uid = cu.code)
					inner join common_member_detail as cmd on (cu.code = cmd.code)
				where
					1
					$where
					and cmd.gp_ix = '".$db->dt[gp_ix]."'";
		$mdb2->query($sql);
		$mdb2->fetch();
		
		$group_reserve = $mdb2->dt[group_reserve];	//그룹별 마일리지 사용금액

		$sql = "select
					sum( distinct if(p.state in ('2'),p.reserve,0)) as group_point
				from
					shop_point as p
					inner join common_user as cu on (p.uid = cu.code)
					inner join common_member_detail as cmd on (cu.code = cmd.code)
				where
					1
					$where
					and cmd.gp_ix = '".$db->dt[gp_ix]."'";
		
		$mdb3->query($sql);
		$mdb3->fetch();
		$group_point = $mdb3->dt[group_point];		//그룹별 포인트 사용금액

		//////////////////////회원비율 시작/////////////////////////
		
		if($member_total > 0){
			$group_member_rate = round($member_cnt/$member_total *100);	//회원비욜
		}
		//////////////////////회원비율 끝/////////////////////////
		
		
		if($total_price > 0){
			$group_price_rate = round($group_price/$total_price *100);	//매출액비율
		}
		//////////////////////매출액 비율 끝/////////////////////////

		//////////////////////마일리지 비율 시작/////////////////////////
		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT sum(r.reserve) as reserve_sum FROM shop_reserve as r inner join ".TBL_SHOP_RESERVE_INFO." as ri on (r.reserve_id = ri.reserve_id) WHERE  r.state in ('2')");
		}else{
			$sql = "SELECT IFNULL(sum(r.reserve),'0') as reserve_sum FROM shop_reserve as r inner join ".TBL_SHOP_RESERVE_INFO." as ri on (r.reserve_id = ri.reserve_id) WHERE r.state in ('2')";
			$mdb->query($sql);
		}
		
		$mdb->fetch(0);
		$reserve_sum = $mdb->dt[reserve_sum];
		if($reserve_sum > 0){
		$group_reserve_rate = round($group_reserve/$reserve_sum *100);	//마일리지 비율
		}		//echo "$group_reserve"."/"."$reserve_sum"."<br>";
		//////////////////////마일리지 비율 끝/////////////////////////

		//////////////////////포인트 비율 시작/////////////////////////
		$sql = "SELECT IFNULL(sum(r.reserve),'0') as reserve_sum FROM shop_point as r
				inner join ".TBL_SHOP_POINT_INFO." as ri on (r.reserve_id = ri.reserve_id) WHERE r.state in ('2')";
		$mdb->query($sql);
		$mdb->fetch(0);

		$point_sum = $mdb->dt[reserve_sum];
		if($point_sum > 0){
		$group_point_rate = round($group_point/$point_sum *100);	//마일리지 비율
		}		//echo "$group_point"."/"."$reserve_sum"."<br>";
		//////////////////////포인트 비율 끝/////////////////////////

		$Contents = $Contents."
		<tr height='27' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
			<!--td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td-->
			<td class='list_box_td' >".$no."</td>
			<td class='list_box_td' style='padding:0px 5px;' nowrap><span title='".$db->dt[organization_name]."'>".$gp_level."</span></td>
			<td class='list_box_td' nowrap>".$gp_name."</td>
			<td class='list_box_td' nowrap>".$member_cnt."</td>
			<td class='list_box_td' >".$group_member_rate."%</td>
			<td class='list_box_td point' nowrap>".number_format($group_price)."원</td>
			<td class='list_box_td' >".$group_price_rate."%</td>
			<td class='list_box_td' >".number_format($group_reserve)."원</td>
			<td class='list_box_td' >".$group_reserve_rate."%</td>
			<td class='list_box_td' >".number_format($group_point)."원</td>
			<td class='list_box_td ctr'  style='padding:5px;' nowrap>".$group_point_rate."%</td>
		</tr>";

		$member_cnt_sum += $member_cnt;
		$group_member_rate_sum += $group_member_rate;
		$group_price_sum += $group_price;
		$group_price_rate_sum += $group_price_rate;
		$group_reserve_sum += $group_reserve;
		$group_reserve_rat_sum += $group_reserve_rate;
		$group_point_sum += $group_point;
		$group_point_rate_sum += $group_point_rate;

		unset($group_member_rate,$group_reserve_rate,$group_point_rate);
		$script_times["loop_end".$i] = time();
	}
$script_times[loop_end] = time();

	$Contents = str_replace("{member_cnt_sum}",$member_cnt_sum,$Contents);
	$Contents = str_replace("{group_member_rate_sum}",round($group_member_rate_sum,2),$Contents);
	$Contents = str_replace("{group_price_sum}",number_format($group_price_sum),$Contents);
	$Contents = str_replace("{group_price_rate_sum}",round($group_price_rate_sum,2),$Contents);
	$Contents = str_replace("{group_reserve_sum}",number_format($group_reserve_sum),$Contents);
	$Contents = str_replace("{group_reserve_rat_sum}",round($group_reserve_rat_sum,2),$Contents);
	$Contents = str_replace("{group_point_sum}",number_format($group_point_sum),$Contents);
	$Contents = str_replace("{group_point_rate_sum}",round($group_point_rate_sum,2),$Contents);


if (!$db->total){

$Contents = $Contents."
<tr height=50>
    <td colspan='11' align='center'>등록된 회원 데이타가 없습니다.</td>
</tr>";
}

$Contents .= "
</table>
</form>";

}


$Contents .= "
<table width=100%>
<tr>
	<!--td>	";
	    if($create_auth){
        $Contents .= "
        <a href='javascript:listAction(document.list_frm);'><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle  ></a>";
    }else{
        $Contents .= "
        <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle ></a>";
    }
    $Contents .= "
    </td-->
  	<td colspan=5 align=right>";
	if($excel_auth){
	$Contents .= "<a href='member_excel2003.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}
	$Contents .= "
	</td>
	<td align='right'>".$str_page_bar."</div></td>
	<td align=right>
	<!--img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\" /-->
	<!--div style='cursor:pointer' onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\">회원수동등록</div-->
	</td>
</tr>
</table>";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원정보를 백업하기 위해서는 회원정보 검색후 엑셀로 저장 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("회원관리", $help_text,'70');


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

//print_r($script_times);
$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";	//init();
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 전체회원";
$P->title = "전체회원";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>



