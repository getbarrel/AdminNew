<?
include("../class/layout.class");

$db = new Database;

$sql="CREATE TABLE IF NOT EXISTS `logstory_admin_page_log_comment` (
  `ec_ix` int(11) NOT NULL AUTO_INCREMENT COMMENT '코멘트 idx',
  `apl_ix` int(11) DEFAULT NULL COMMENT '페이지타임 로그 ix',
  `mem_ix` varchar(32) DEFAULT NULL COMMENT '회원코드',
  `charger_name` varchar(30) DEFAULT NULL COMMENT '등록자이름',
  `comment` mediumtext COMMENT '코멘트내용',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`ec_ix`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='서비스에러 코멘트';";

$db->query($sql);

$sql = "SELECT * FROM logstory_admin_page_log ap left join admin_menus am on (ap.page_code=am.menu_code) where apl_ix = '".$ix."'";
$db->query($sql);
$db->fetch();

switch($db->dt['charger_div']){
	case 'A' : $charger_div='관리자';
		break;
	case 'C' : $charger_div='사업자';
		break;
	case 'M' : $charger_div='일반';
		break;
	default : $charger_div='기타';
		break;
}

$addScript = "
<SCRIPT LANGUAGE='JavaScript'>
<!--
function go_con_del(apl_ix){
	yes = confirm('정말로 삭제하시 겠습니까?');
	if(yes){
		location.href = 'page_timelog_detail.act.php?mode=pop&act=delete&apl_ix='+apl_ix;
	}else{
		return;
	}
}

<!--
function go_comment_del(comment_ix,mem_code){
	yes = confirm('정말로 삭제하시 겠습니까?');
	if(yes){
		location.href = 'page_timelog_detail.act.php?act=comment_delete&ec_ix='+ comment_ix;
	}else{
		return;
	}
}
//-->
</SCRIPT>";

$Contents01 = "
<form name='error_form' action='page_timelog_detail.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'>
<input type='hidden' name='act' value='update' >
<input type='hidden' name='apl_ix' value='".$ix."' >
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
							<td class='input_box_title'> <b>서버 아이피</b></td>
							<td class='input_box_item' width='20%'>
								".$db->dt['server_ip']."
							</td>
							<td class='input_box_title'> <b>작업자 아이피 </b></td>
							<td class='input_box_item' width='*'>
								".$db->dt['client_ip_address']."
							</td>
						</tr>
						<tr height='' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>페이지 URL</b></td>
							<td class='input_box_item' width='20%'>
								".$db->dt['php_self']."
							</td>							
							<td class='input_box_title'> <b>작업자</b></td>
							<td class='input_box_item' width='20%'>
								".$db->dt['charger_name']."
							</td>	
						</tr>
						<tr>
							<td class='input_box_title'> <b>페이지명</b></td>
							<td class='input_box_item' width='20%'>
								".$db->dt['menu_name']."
							</td>
							<td class='input_box_title'> <b>지속시간 </b></td>
							<td class='input_box_item' width='*'>
								".$db->dt['duration_time']."
							</td>
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>작업자 구분</b></td>
							<td class='input_box_item' width='20%'>
								".$charger_div."
							</td>
							<td class='input_box_title'> <b>작업일 </b></td>
							<td class='input_box_item' width='*'>
								".$db->dt['regdate']."
							</td>
						</tr>
						<tr valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>Query_string</b></td>
							<td colspan=3 style='padding:5px'><textarea rows=30 style='height:180px;width:95%;' readonly>".$db->dt['query_string']."</textarea></td>
                        
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>처리상태</b></td>
							<td class='input_box_item' width='20%'>
								<input type='radio' name='status' id='status1' value='0' ".($db->dt['status'] == '0' ? "checked" : "")." /><label for='status1'>미처리</label>
								<input type='radio' name='status' id='status2' value='1' ".($db->dt['status'] == '1' ? "checked" : "")."/><label for='status2'>처리완료</label>
								<input type='radio' name='status' id='status3' value='2' ".($db->dt['status'] == '2' ? "checked" : "")."/><label for='status3'>대기중</label>
							</td>
							<td class='input_box_title'> <b>처리자 </b></td>
							<td class='input_box_item' width='*'>
								<input type='text' name='worker_name' title='처리자' value='".$db->dt['worker_name']."' validation='true'/>
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
<form name='comment_form' action='page_timelog_detail.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'>
	<input type='hidden' name='act' value='comment_insert'>
	<input type='hidden' name='apl_ix' value='".$ix."'>
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
			logstory_admin_page_log_comment
		WHERE 
			apl_ix = '".$ix."'
		";
$db->query($sql);
$total = $db->total;

$sql = "SELECT 
			* 
		FROM 
			logstory_admin_page_log_comment
		WHERE 
			apl_ix = '".$ix."'
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
$P->Navigation = "관리자 페이지 로그 관리 > 페이지 로그 정보";
$P->NaviTitle = "페이지 로그 정보 ";
$P->strContents = $Contents01;
echo $P->PrintLayOut();
?>





