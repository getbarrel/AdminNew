<?php 
/**
 * 수집된 에러 목록 페이지
 * @author bgh
 * @date 2014.3.14
 */
include $_SERVER['DOCUMENT_ROOT'].'/admin/class/layout.class';

$db = new MySQL;

$max = 15; //페이지당 갯수

$page = $_REQUEST['page'];

if($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$search_query = array();

if(! empty($duration_time1) && ! empty($duration_time2)){
	$search_query[] = " ap.duration_time between '".$duration_time1."' and '".$duration_time2."' ";
}else{
	if(! empty ($duration_time1) && empty ($duration_time2)){
		$search_query[] = " ap.duration_time > ".$duration_time1." ";
	}elseif(empty ($duration_time1) && ! empty ($duration_time2)){
		$search_query[] = " ap.duration_time between '0' and '".$duration_time2."' ";
	}else{
		$search_query[] = " ap.duration_time > 0 ";
	}
}

if($status != ''){
	$search_query[] = " ap.status = '".$status."' ";
}

if(! empty($charger_div)){
	$search_query[] = " ap.charger_div ='".$charger_div."' ";
}

if(! empty($charger_name)){
	$search_query[] = " ap.charger_name like '%".$charger_name."%' ";
}

if(! empty($php_self)){
	$search_query[] = " ap.php_self like '%".$php_self."%' ";
}

if(! empty($menu_name)){
	$search_query[] = " am.menu_name like '%".$menu_name."%' ";
}

if(! empty($work_sdate) && ! empty($work_edate)){
	$search_query[] = " ap.regdate between '".$work_sdate." 00:00:00' and '".$work_edate." 23:59:59' ";
}


if(is_array($search_query)){
	$search_query = implode($search_query, 'and');
}


if(! empty($search_query)){
	$where = " where ".$search_query;
}

//TOTAL COUNT QUERY
$sql = "SELECT count(*) as total FROM logstory_admin_page_log ap left join admin_menus am on (ap.page_code=am.menu_code) " . $where;
//echo $sql."<br><br>";
$script_time[count_start] = time();
$db->query($sql);
$db->fetch();
$script_time[count_end] = time();
$total = $db->dt['total'];

if($mode == 'excel'){


}


//$exce_down_str = "<a href='?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";

$Contents02 = "
	<div style='width:105%;padding-bottom:10px;'> 
		".colorCirCleBox("#efefef","105%","<div style='padding:5px 5px 5px 10px;'><b>관리자 페이지 로그 관리</b></div>")."
	</div>

	<table width='105%'  cellpadding=0 cellspacing=0 border='0'>
		<form name='search_form'>
		<tr>
			<td>
				<table width='105%' cellpadding=3 cellspacing=0 border='0' align='left'  class='list_table_box'>
					<col width='20%'>
					<col width='30%'>
					<col width='20%'>
					<col width='30%'>
					<tr>
						<th bgcolor=#efefef><label for='work_date'>작업일</label><input type='checkbox' name='work_date' id='work_date' value='1' onclick='ChangeRegistDate(document.search_form);' ".CompareReturnValue("1",$work_date,"checked")."></th>
						<td colspan=3>
							".search_date('work_sdate','work_edate',$work_sdate,$work_edate)."
						</td>
					</tr>
					<tr>
						<th bgcolor=#efefef>지속 시간</th>
						<td>
							<input type='text' name='duration_time1' value='".$duration_time1."' style='width:100px;'/> ~
							<input type='text' name='duration_time2' value='".$duration_time2."' style='width:100px;'/>
						</td>
						<th bgcolor=#efefef>처리상태</th>
						<td>
							<input type='radio' name='status' value='' id='status_' ".($status == "" ? "checked":"")."/><label for='status_'>전체</label>
							<input type='radio' name='status' value='0' id='status_0' ".($status == "0" ? "checked":"")."/><label for='status_0'>미처리</label>
							<input type='radio' name='status' value='1' id='status_1' ".($status == "1" ? "checked":"")." /><label for='status_1'>처리완료</label>
							<input type='radio' name='status' value='2' id='status_2' ".($status == "2" ? "checked":"")." /><label for='status_2'>대기</label>
						</td>
					</tr>
					<tr height=30>
						<th bgcolor=#efefef>작업자 구분</th>
						<td >
							<input type='radio' name='charger_div' value='' id='charger_div_' ".($charger_div == "" ? "checked":"")."/><label for='charger_div_'>전체</label>
							<input type='radio' name='charger_div' value='A' id='charger_div_A' ".($charger_div == "A" ? "checked":"")."/><label for='charger_div_A'>(직원)관리자</label>
							<input type='radio' name='charger_div' value='C' id='charger_div_C' ".($charger_div == "C" ? "checked":"")."/><label for='charger_div_C'>사업자 회원</label>
							<!--input type='radio' name='charger_div' value='M' id='charger_div_M' ".($charger_div == "M" ? "checked":"")."/><label for='charger_div_M'>일반 회원</label-->
						</td>
						<th bgcolor=#efefef>작업자</th>
						<td >
							<input type='text' name='charger_name' value='".$charger_name."' style='width:200px;'/>
						</td>
					</tr>
					<tr height=30>
						<th bgcolor=#efefef>페이지 URL</th>
						<td>
							<input type='text' name='php_self' value='".$php_self."' style='width:400px;'/>
						</td>
						<th bgcolor=#efefef>페이지명</th>
						<td>
							<input type='text' name='menu_name' value='".$menu_name."' style='width:400px;'/>
						</td>
					</tr>
					
				</table>
			</td>
		</tr>
		<tr>
			<td align=center style='padding:10px 0px;'>
				<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
			</td>
		</tr>
		</form>
	</table>
	<span><b>검색 : ".$total." 개</b></span>
	<span>".$exce_down_str."</span>
	<table width='110%' cellpadding=3 cellspacing=0 border='0' align='left'  class='list_table_box' >
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td' style='width:10%;'>페이지명</td>
	    <td class='m_td' style='width:7%;'>페이지 URL</td>
	    <td class='m_td' style='width:3%;'>지속시간</td>	    
		<td class='m_td' style='width:8%;'>Query_string</td>
		<td class='m_td' style='width:6%;'>서버</br>아이피</td>
		<td class='m_td' style='width:5%;'>작업자</td>
		<td class='m_td' style='width:5%;'>작업자<br>구분</td>
		<td class='m_td' style='width:6%;'>작업자</br>아이피</td>
	    <td class='e_td' style='width:9%;'>작업일</td>
		<td class='m_td' style='width:5%;'>처리상태</td>
		<td class='e_td' style='width:12%;'>관리</td>
	  </tr>";

//LIST QUERY
$sql = "SELECT *, ap.regdate as work_date FROM logstory_admin_page_log ap left join admin_menus am on (ap.page_code=am.menu_code) ".$where." ORDER BY ap.regdate desc LIMIT $start, $max";
//echo $sql;
$script_time[query_start] = time();
$db->query($sql);
$script_time[query_end] = time();


if($db->total){
	$result = $db->fetchall();
	foreach( $result as $rt ):
		$file_path_array = explode("/",$rt['file_path']);
		$name_index = count($file_path_array);

		switch($rt['charger_div']){
			case 'A' : $charger_div='관리자';
				break;
			case 'C' : $charger_div='사업자';
				break;
			case 'M' : $charger_div='일반';
				break;
			default : $charger_div='기타';
				break;
		}
		
		$Contents02 .= "
			<tr style='background:".$bg_color.";' height=30 align=center>	  
				<td><span>".$rt['menu_name']."</span></td>
				<td >
					<span>".$rt['php_self']."</span>
				</td>
				<td>
					<span>".$rt['duration_time']."</span>
				</td>
				<td>
					<span>".my_str_cut($rt['query_string'], 30)."</span>
				</td>
				<td ><span>".$rt['server_ip']."</span></td>
				<td><span>".$rt['charger_name']."</span></td>
				<td><span>".$charger_div."</span></td>
				<td><span>".$rt['client_ip_address']."</span></td>			
				<td><span>".$rt['work_date']."</span></td>
				<td><span>".($rt['status'] == 0 ? "미처리":"처리")."</span></td>
				<td>";
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents02.="
					<a href=\"javascript:PoPWindow('page_timelog_detail.php?mode=pop&ix=".$rt['apl_ix']."',800,550,'contact_info')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
					}else{
						$Contents02.="
						<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
					}    
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$Contents02.="    
						<a href=\"javascript:go_con_del('delete','".$rt['apl_ix']."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
					}else{
					 $Contents02.="
						<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
					}
					$Contents02.="
				</td>
			</tr>";

	endforeach;
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=13>수집된 로그 정보가 없습니다. </td>
		  </tr>
		  <tr height=1><td colspan=13 class=dot-x></td></tr>	  ";
}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}
$Contents02 .= "
	  </table>
	  <table> 
		<tr height=40 bgcolor=#ffffff><td colspan=8 align=center>".page_bar($total, $page, $max,  $query_string,"")."</td></tr>
	  </table>
	  ";
$Contents02 .= "";

	    
	  
$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script language='javascript'>

 $(document).ready(function(){
	if($('input[name=work_date]').is(':checked') == true){
		$('input[name=work_sdate]').attr('disabled',false);
		$('input[name=work_edate]').attr('disabled',false);
	}else{
		$('input[name=work_sdate]').attr('disabled',true);
		$('input[name=work_edate]').attr('disabled',true);
	}
});

<!--
function go_con_del(act,apl_ix){
	yes = confirm('정말로 삭제하시 겠습니까?');
	if(yes){
		window.frames['iframe_act'].location.href = 'page_timelog_detail.act.php?act=delete&apl_ix='+apl_ix;
	}else{
		return;
	}
}
//-->

function ChangeRegistDate(frm){
	if(frm.work_date.checked){
		$('input[name=work_sdate]').attr('disabled',false);
		$('input[name=work_edate]').attr('disabled',false);

	}else{
		$('input[name=work_sdate]').attr('disabled',true);
		$('input[name=work_edate]').attr('disabled',true);
	}
}
 
 </script>
 ";
	

$P = new LayOut();
$P->addScript = "<script type='text/javascript' src='../../js/calendar.js'></script>".$Script;
$P->strLeftMenu = service_manage_menu();
$P->title = "관리자 페이지 로그 관리";
$P->Navigation = " 시스템 관리 >관리자 페이지 로그 관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();
$script_time[page_end] = time();

function my_str_cut($str, $limit){
    if (strlen($str) > $limit){
      return substr($str, 0, $limit) . '...';
    }else{
      return $str;
    }
}


?>