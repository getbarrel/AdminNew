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
//SEARCH condition
$div = $_REQUEST['div'];
$code = $_REQUEST['code'];
$domain = $_REQUEST['domain'];
$server_ip = $_REQUEST['server_ip'];

if($year != ""){
	$table_name = "log_service_error_".$year;
}else{
	$table_name = "log_service_error";
}

if(!$status){
	$status = "N";
}

$search_query_array = array();
$search_query = "";
if(! empty($div) AND $div != 'all' )	$search_query_array[] = " error_div = '".$div."' ";
if(! empty($status))						$search_query_array[] = " status = '".$status."' ";	
if(! empty($error_code))						$search_query_array[] = " error_code = '".$error_code."' ";	
if(! empty($domain)) 					$search_query_array[] = " site_domain LIKE '".$domain."%' ";
if(! empty($server_ip)) 					$search_query_array[] = " server_ip LIKE '".$server_ip."%' ";
if(! empty($file_path)) 					$search_query_array[] = " file_path LIKE '%".$file_path."%' ";

if(! empty($search_query_array)) 		$search_query = " WHERE ". implode($search_query_array, " AND");

//TOTAL COUNT QUERY
$sql = "SELECT count(*) as total FROM ".$table_name." " . $search_query;
//echo $sql."<br><br>";
$script_time[count_start] = time();
$db->query($sql);
$db->fetch();
$script_time[count_end] = time();
$total = $db->dt['total'];

$Contents02 = "
	<div style='width:105%;padding-bottom:10px;'> 
		".colorCirCleBox("#efefef","105%","<div style='padding:5px 5px 5px 10px;'><b>수집된 에러 리스트</b> ( ".$total."개 )</div>")."
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
						<th bgcolor=#efefef>서버 IP</th>
						<td>
							<input type='text' name='server_ip' value='".$server_ip."' style='width:200px;'/>
						</td>
						<th bgcolor=#efefef>도메인 검색</th>
						<td>
							<input type='text' name='domain' value='".$domain."' style='width:200px;'/>
						</td>
					</tr>
					<tr height=30>
						<th bgcolor=#efefef>파일</th>
						<td >
							<input type='text' name='file_path' value='".$file_path."' style='width:200px;'/>
						</td>
						<th bgcolor=#efefef>처리여부</th>
						<td >
							<input type='radio' name='status' value='N' id='status_n' ".($status == "N" ? "checked":"")."/><label for='status_n'>미처리</label>
							<input type='radio' name='status' value='Y' id='status_y' ".($status == "Y" ? "checked":"")." /><label for='status_y'>처리</label>
						</td>
					</tr>
					<tr height=30>
						<th bgcolor=#efefef>에러구분</th>
						<td >
							<input type='radio' name='div' value='all' id='div_all' ".CompareReturnValue('all',$div,' checked').">
								<label for='div_all'>전체</label>

							<input type='radio' name='div' value='database' id='div_database' ".CompareReturnValue('database',$div,' checked').">
								<label for='div_database'>Database</label>

							<input type='radio' name='div' value='fatal' id='div_fatal' ".CompareReturnValue('fatal',$div,' checked').">
								<label for='div_fatal'>Fatal</label>

							<input type='radio' name='div' value='warning' id='div_warning' ".CompareReturnValue('warning',$div,' checked').">
								<label for='div_warning'>Warning</label>

							<input type='radio' name='div' value='unknown' id='div_unknown' ".CompareReturnValue('unknown',$div,' checked').">
								<label for='div_unknown'>Unknown</label>

							<input type='radio' name='div' value='parse' id='div_parse' ".CompareReturnValue('parse',$div,' checked').">
								<label for='div_parse'>parse</label>
						</td>
						<th bgcolor=#efefef>에러코드</th>
						<td >
							<input type='text' name='error_code' value='".$error_code."' style='width:200px;'/>
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

	<table width='110%' cellpadding=3 cellspacing=0 border='0' align='left'  class='list_table_box' >
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td' style='width:4%;'>NO.</td>
	    <td class='m_td' style='width:5%;'>서버</td>
	    <td class='m_td' style='width:7%;'>도메인</td>
	    <td class='m_td' style='width:9%;'>요청주소</td>	    
		<td class='m_td' style='width:8%;'>파일</td>
		<td class='m_td' style='width:5%;'>에러구분</td>
		<td class='m_td' style='width:5%;'>에러코드</td>
		<td class='m_td' style='width:20%;'>에러메시지</td>
		<td class='m_td' style='width:6%;'>접속IP</td>
	    <td class='e_td' style='width:9%;'>수집일</td>
	    <td class='e_td' style='width:7%;'>처리결과</td>
	    <td class='e_td' style='width:7%;'>작업자</td>
	    <td class='e_td' style='width:12%;'>관리</td>
	  </tr>";

//LIST QUERY
$sql = "SELECT * FROM ".$table_name." USE INDEX (insert_date_desc)  ".$search_query." ORDER BY insert_date_desc  LIMIT $start, $max";
//echo $sql;
$script_time[query_start] = time();
$db->query($sql);
$script_time[query_end] = time();


if($db->total){
	$result = $db->fetchall();
	foreach( $result as $rt ):
		$file_path_array = explode("/",$rt['file_path']);
		$name_index = count($file_path_array);
		
		switch($rt['error_div']){
			case 'database':
				$bg_color = "#FBF5E6";
				break;
			case 'fatal':
				$bg_color = "#F5BCA9";
				break;
			default:
				$bg_color = "#ffffff";
				break;
		}

		switch($rt['server_ip']){
			case '222.236.46.149':
				$server_name = 'S1';
				break;
			case '222.236.46.215':
				$server_name = 'S2';
				break;
			default:
				$server_name = $rt['server_ip'];
				break;
		}
		switch($rt['status']){
			case 'Y':
				$status = '처리완료';
				$bg_color = "#87CEFA";
				break;
			case 'N':
				$status = '미처리';
				break;
			case 'R':
				$status = '대기중';
				break;
			default:
				$status = $rt['status'];
				break;
		}
		$Contents02 .= "
			<tr style='background:".$bg_color.";' height=30 align=center>	    
				<td><span>".$rt['log_idx']."</span></span></td>
				<td><span>".$server_name."</span></td>
				<td align=left>
					<span title='".$rt['server_ip']."'>".$rt['site_domain']."</span>
				</td>
				<td align=left>
					<a href='http://".$rt['site_domain'].$rt['request_uri']."' target='_blank'>
						<span title='".$rt['request_uri']."'>".my_str_cut($rt['request_uri'], 40)."</span>
					</a><br><br>
					<a href='".$rt['referer_uri']."' target='_blank'>
						REFERER : <span title='".$rt['referer_uri']."'>".my_str_cut($rt['referer_uri'], 40)."</span>
					</a>
					
				</td>
				<td align=left>
					<span title='".$rt['file_path']."'>".$file_path_array[$name_index-1]."</span>
				</td>
				<td><span>".$rt['error_div']."</span></td>
				<td><span>".$rt['error_code']."</span></td>
				<td align=left><span>".strip_tags(str_replace("\'", "'", $rt['error_msg']))."</span></td>
				<td><span>".$rt['remote_ip']."</span></td>			
				<td><span>".$rt['insert_date']."</span></td>
				<td><span>".$status."</span></td>
				<td><span>".$rt['worker_name']."</span></td>
				<td>";
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents02.="
					<a href=\"javascript:PoPWindow('errorlist_detail.php?ix=".$rt['log_idx']."',800,550,'contact_info')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
					}else{
						$Contents02.="
						<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
					}    
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$Contents02.="    
						<a href=\"javascript:go_con_del('delete','".$rt['log_idx']."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
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

<!--
function go_con_del(act,log_idx){
	yes = confirm('정말로 삭제하시 겠습니까?');
	if(yes){
		window.frames['iframe_act'].location.href = 'errorlist_detail.act.php?act=delete&log_idx='+log_idx;
	}else{
		return;
	}
}
//-->

 
 </script>
 ";
	

$P = new LayOut();
$P->addScript = "<script type='text/javascript' src='../../js/calendar.js'></script>".$Script;
$P->strLeftMenu = service_manage_menu();
$P->title = "에러리스트";
$P->Navigation = " 시스템 관리 > 에러관리";
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