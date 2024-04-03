<?
include("../class/layout.class");

$db = new Database;
$sql = "SELECT * FROM log_service_error where log_idx = '".$ix."'";
$db->query($sql);
$db->fetch();

$addScript = "
<SCRIPT LANGUAGE='JavaScript'>
<!--
function go_con_del(log_idx){
	yes = confirm('정말로 삭제하시 겠습니까?');
	if(yes){
		location.href = 'errorlist_detail.act.php?mode=pop&act=delete&log_idx='+log_idx;
	}else{
		return;
	}
}

<!--
function go_comment_del(comment_ix,mem_code){
	yes = confirm('정말로 삭제하시 겠습니까?');
	if(yes){
		location.href = 'errorlist_detail.act.php?act=comment_delete&ec_ix='+ comment_ix;
	}else{
		return;
	}
}
//-->
</SCRIPT>";

$Contents01 = "
<form name='error_form' action='errorlist_detail.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'>
<input type='hidden' name='act' value='update' >
<input type='hidden' name='log_idx' value='".$ix."' >
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("제휴문의", "고객센타 > 제휴문의", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 10px 0px'>
					<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
						 <colgroup>
							<col width='16%' />
							<col width='34%' style='padding:0px 0px 0px 10px'/>
							<col width='16%' />
							<col width='34%' style='padding:0px 0px 0px 10px'/>
						  </colgroup>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>서버</b></td>
							<td class='input_box_item' width='20%'>
								".$db->dt['server_ip']."
							</td>
							<td class='input_box_title'> <b>도메인 </b></td>
							<td class='input_box_item' width='*'>
								".$db->dt['site_domain']."
							</td>
						</tr>
						<tr height='' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>요청주소</b></td>
							<td colspan=3 class='input_box_item' width='20%'>
								".$db->dt['request_uri']."
							</td>							
						</tr>
						<tr>
							<td class='input_box_title'> <b>이전 페이지주소</b></td>
							<td class='input_box_item' width='20%'>
								".$db->dt['referer_uri']."
							</td>
							<td class='input_box_title'> <b>파일 </b></td>
							<td class='input_box_item' width='*'>
								".$db->dt['file_path']."
							</td>
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>에러구분</b></td>
							<td class='input_box_item' width='20%'>
								".$db->dt['error_div']."
							</td>
							<td class='input_box_title'> <b>에러코드 </b></td>
							<td class='input_box_item' width='*'>
								".$db->dt['error_code']."
							</td>
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>접속IP</b></td>
							<td class='input_box_item' width='20%'>
								".$db->dt['remote_ip']."
							</td>
							<td class='input_box_title'> <b>수집일 </b></td>
							<td class='input_box_item' width='*'>
								".$db->dt['insert_date']."
							</td>
						</tr>
						<tr valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>에러메시지</b></td>
							<td colspan=3 style='padding:5px'><textarea rows=30 style='height:180px;width:95%;'>".$db->dt['error_msg']."</textarea></td>
                        
						</tr>
						<tr valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>관련쿼리</b></td>
							<td colspan=3 style='padding:5px'><textarea rows=30 style='height:180px;width:95%;'>".$db->dt['error_sql']."</textarea></td>
                        
						</tr>
						
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>처리상태</b></td>
							<td class='input_box_item' width='20%'>
								<input type='radio' name='status' id='status1' value='N' ".($db->dt['status'] == 'N' ? "checked" : "")." /><label for='status1'>미처리</label>
								<input type='radio' name='status' id='status2' value='Y' ".($db->dt['status'] == 'Y' ? "checked" : "")."/><label for='status2'>처리완료</label>
								<input type='radio' name='status' id='status3' value='R' ".($db->dt['status'] == 'R' ? "checked" : "")."/><label for='status3'>대기중</label>
							</td>
							<td class='input_box_title'> <b>작업자 </b></td>
							<td class='input_box_item' width='*'>
								<input type='text' name='worker_name' title='작업자' value='".$db->dt['worker_name']."' validation='true'/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10px 0px' colspan=2>
			 <a href='javascript:self.close();'><img src='../images/".$admininfo['language']."/btn_close.gif' border=0 style='vertical-align:middle;'></a>";
             if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $Contents01 .= "
			     <a href=\"javascript:go_con_del('".$ix."');\"><img src='../images/".$admininfo['language']."/btn_del.gif' border=0 style='vertical-align:middle;'></a>";
             }else{
                $Contents01 .= "
                <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo['language']."/btn_del.gif' border=0 style='vertical-align:middle;'></a>";
             }
             $Contents01 .= "
			 <input type='image' src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 style='vertical-align:middle;'>
		</td>
	</tr>
</TABLE>
</form>
<form name='comment_form' action='errorlist_detail.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'>
	<input type='hidden' name='act' value='comment_insert'>
	<input type='hidden' name='error_ix' value='".$ix."'>
	<table style='width:100%;' width='100%' cellpadding='0' cellspacing='0' border='0'>
		<colgroup><col width='*'>
		<col width='110px'>
		</colgroup><tbody><tr height='30'> 
			<td colspan='2'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'><b> 코멘트 작성</b></td>
		</tr>
		<tr>
			<td bgcolor='#ffffff' height='30' style='padding:0 0 0 0'>
			<textarea style='height:50px;width:100%' name='comment' validation='true' title='코멘트'></textarea>
			</td>
		</tr>
		<tr>
			<td align='left' ><input style='margin-top:10px;' type='image' src='../images/".$admininfo["language"]."/b_save.gif'></td>
		</tr>
		</tbody>
	</table>
</form>
";

$max = 30; //페이지당 갯수

$page = $_REQUEST['page'];

if($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$sql = "SELECT 
			* 
		FROM 
			log_service_error_comment
		WHERE 
			error_ix = '".$ix."'
		";
$db->query($sql);
$total = $db->total;

$sql = "SELECT 
			* 
		FROM 
			log_service_error_comment
		WHERE 
			error_ix = '".$ix."'
		ORDER BY regdate DESC
		LIMIT $start, $max
		";


$db->query($sql);
$reply = $db->fetchall();

if($reply){
	foreach($reply as $val){
		$Contents01 .= "
			<table style='width:100%;' width='100%' cellpadding='0' cellspacing='0' border='0'>
				<tr> 
					<td colspan='2' align='left' style='padding-top:10px;'>
					<div id='comment_27279_7635' style='width:100%;background-color:#ffffff;border:1px solid silver;padding:10px 0px;margin-bottom:3px;'>
					<table width='100%' cellpadding='0' cellspacing='0'>
						<colgroup>
							<col width='70px'>
							<col width='*'>
						</colgroup>
						<tbody>
						<tr>
							<td rowspan='2' align='center'></td>
							<td align='left' style='line-height:150%'>".$val['regdate']." <b>".$val['charger_name']."</b> <a href=\"javascript:go_comment_del('".$val['ec_ix']."');\"><img src='../images/".$admininfo['language']."/btn_del.gif' border=0 style='vertical-align:middle;'></a> <br>".$val['comment']."</td>
							<td valign='top' align='right' style='padding-right:10px;'>		
							</td>
						</tr>
					</tbody>
					</table>
					</div>
					</td>
				</tr>
			</tbody>
		</table>
		";
	}
	$Contents01 .= "
	<table> 
		<tr height=40 bgcolor=#ffffff><td colspan=8 align=center>".page_bar($total, $page, $max,  $query_string,"")."</td></tr>
	  </table>";
}

$P = new ManagePopLayOut();
$P->addScript = $addScript;
$P->Navigation = "에러리스트 > 에러정보";
$P->NaviTitle = "에러정보 ";
$P->strContents = $Contents01;
echo $P->PrintLayOut();
?>





