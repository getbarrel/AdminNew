<? 
include("../class/layout.work.class");
include("../webedit/webedit.lib.php");
include("work.lib.php");
if($admininfo[admin_id] == "sigi1074" && false){
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<head>
	<meta http-equiv=\"content-type\" content=\"text/html; charset=ISO-8859-1\">
	<title>Dynatree - Example</title>
<script src='./js/jquery-1.4.4.min.js' type='text/javascript'></script>
<script src='./js/jquery-ui-1.8.6.custom.min.js' type='text/javascript'></script>
<script src='./js/jquery.cookie.js' type='text/javascript'></script>
<link href='./dynatree/skin/ui.dynatree.css' rel='stylesheet' type='text/css' id='skinSheet'>
<script src='./dynatree/jquery.dynatree.js' type='text/javascript'></script>

<script type='text/javascript' src='work.tree.js'></script>
<body class=\"example\">
<div id='tree3'> </div>
<div>Selected keys: <span id='echoSelection3'>-</span></div>
<div>Selected root keys: <span id='echoSelectionRootKeys3'>-</span></div>
<div>Selected root nodes: <span id='echoSelectionRoots3'>-</span></div>
</body>
</html>
";
exit;
}

$db = new Database;
$mdb = new Database;
//print_r($_GET);

WorkHistory($mdb, $wl_ix, $admininfo[charger_ix], "R", "업무읽기");

$sql = "select count(*) as total from work_report wr, common_member_detail cmd where wl_ix ='$wl_ix' and wr.charger_ix = cmd.code   ";
$db->query($sql);
$db->fetch();
$report_total = $db->dt[total];



$sql = "select count(*) as total from work_comment wc where  wl_ix ='$wl_ix'    ";
$db->query($sql);
$db->fetch();
$comment_total = $db->dt[total];

$sql = "select count(*) as total from work_issue wi where  wl_ix ='$wl_ix'    ";
$db->query($sql);
$db->fetch();
$issue_total = $db->dt[total];


$sql = 	"SELECT wl.*, wg.group_depth , wg.group_name, 
		case when wg.group_depth = 1 then wg.group_ix else parent_group_ix end as parent_group_ix , AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name	, cmd.sex_div	
		FROM work_list wl, work_group wg, common_member_detail cmd   
		where wl_ix ='$wl_ix' and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code  ";
		//wl_ix, wl.group_ix, wl.company_id, wl.charger_ix, wl.work_title, wl.work_detail, wl.status, wl.complete_rate, wl.is_schedule, wl.is_hidden, wl.is_report, wl.importance, wl.sdate, wl.stime, wl.dday, wl.dtime, wl.work_where, AES_DECRYPT(UNHEX(wl.reg_name),'".$db->ase_encrypt_key."') as reg_name, wl.reg_charger_ix, wl.co_charger_yn, wl.co_charger_cnt,wl.depth

//echo $sql;
$db->query($sql);
$db->fetch();
//print_r($db->dt);


if($db->total){
	$act = "update";
	$sdate = $db->dt[sdate];
	$dday = $db->dt[dday];
	$parent_group_ix = $db->dt[parent_group_ix];
	$charger_ix = $db->dt[charger_ix];
	$stime = $db->dt[stime];
	$dtime = $db->dt[dtime];
	//echo $db->dt[group_depth];

	if($db->dt[group_depth] == 2){
		$sql = "SELECT group_name FROM work_group WHERE group_ix  = '".$db->dt[parent_group_ix]."' ";
		//echo $sql;
		$mdb->query($sql);
		$mdb->fetch(0);
		$group_name = $mdb->dt[group_name]." > ".$db->dt[group_name];
	}else{
		$group_name = $db->dt[group_name];
	}

	$sql = 	"SELECT  AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cr.charger_ix, cmd.sex_div
				FROM work_charger_relation cr, common_member_detail cmd  
				where cr.charger_ix = cmd.code and wl_ix ='$wl_ix'  ";

	//echo $sql;
	$db->query($sql);
	$co_charger_data_rows = $db->getrows();
	$co_charger_name = $co_charger_data_rows[0];
	$co_charger_ix = $co_charger_data_rows[1];
	if($co_charger_ix == ""){
		$co_charger_ix = array();
	}
	//print_r($co_charger_name);
}else{
	$act = "insert";
	$sdate = $sdate;
	if($dday){
		$dday = $dday;
	}else{
		$dday = $sdate;
	}
	$parent_group_ix = "11";
	$charger_ix = $admininfo[charger_ix];
}

//print_r($admininfo);

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("업무 상세보기", "업무관리 > 업무 상세보기 ")."</td>
	  </tr>
	  <tr >
	    <td align='left' colspan=4>";

$Contents01 .= "		
		<div class='t_no' style='margin: 10px 0px; padding:5px 0px;border-top: solid 3px #c6c6c6; '>
			<!-- my_movie start -->
			<div class='my_box' style='padding:0px;'>
				<div style='padding:10px 0px;font-size:15px;'>
				<b class='blk'>".$group_name." : </b> <b class='blk'>".$db->dt[work_title]."</b>
				</div>
				<table width=100% cellpadding=0  border=0 cellspacing='1' class='input_table_box'>
					<col width=20%>
					<col width=30%>
					<col width=20%>
					<col width=30%>
					
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 담당자 : </b></td>
				    <td class='input_box_item' style='padding:5px;' > ";
					if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/profile/profile_".$db->dt[charger_ix].".jpg")){
						$Contents01 .= "<img src='".$admin_config[mall_data_root]."/work/profile/profile_".$db->dt[charger_ix].".jpg' align=absmiddle  width=30 height=30>";
					}else{
						$Contents01 .= "<img src='../images/".($db->dt[sex_div] == "M" ? "man.jpg":"women.jpg")."' align=absmiddle width=30 height=30>";
					}
					$Contents01 .= "
					<b>".$db->dt[name]."</b>
									
				    </td>
				  
				    <td class='input_box_title'><b> 협력자 : </b></td>
				    <td class='input_box_item' style='padding:5px;'>";
					
		for($i=0;$i < count($co_charger_name);$i++){
			if($i==0){
				$Contents01 .= "<b>".$co_charger_name[$i]."</b>";
			}else{
				$Contents01 .= ", <b>".$co_charger_name[$i]."</b>";
			}
		}

		$Contents01 .= "
									
				    </td>
				  </tr>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 시작날짜 : </b> </td>
				    <td class='input_box_item point'>".ChangeDate($sdate,"Y년 m월 d일")." ".$db->dt[stime]."</td>				 
				    <td class='input_box_title'><b> 완료기한 : </b> </td>
				    <td class='input_box_item point'>".ChangeDate($dday,"Y년 m월 d일")." ".$db->dt[dtime]."</td>
				  </tr>
				  
				  
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'> <b>업무상세 :</b> </td>
				    <td class='input_box_item' colspan=3 style='height:100px;vertical-align:top;padding:10px 0 10px 10px;line-height:150%'>".($db->dt[is_html] == 1 ? $db->dt[work_detail]:nl2br($db->dt[work_detail]))."</td>				    
				  </tr>
				   <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 장소 : </b></td>
					<td class='input_box_item' style='line-height:170%;'>
					".($db->dt[work_where] == "" ? "":$db->dt[work_where])."
					</td>
				  	<td class='input_box_title'> 공개여부</td>
				    <td class='input_box_item' >
				    ".(($db->dt[is_hidden] == "1") ? "비공개":"공개")."<br>
				    </td>
				  </tr>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'> 등록담당자 : </td>
				    <td class='input_box_item' >".$db->dt[reg_name]."</td>
				
				    <td class='input_box_title'> 업무상태/진행율 : </td>
				    <td class='input_box_item' style='padding:10px;'>
					<table width='98%' cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
						<col width='".($db->dt[complete_rate] == 0 ? 1:$db->dt[complete_rate])."%'>
						<col width='".(100-$db->dt[complete_rate])."%'>
						<tr height=8><td bgcolor='#ff7200' id='graph_".$db->dt[wl_ix]."'></td><td></td></tr>
					</table>
					<div  style='position:relative;cursor:pointer;padding:0px 0px 3px 10px'>
						<div id='work_status_text_".$db->dt[wl_ix]."' onclick=\"$('#quick_complate_rate_".$db->dt[wl_ix]."').toggle();\">".$work_status[$db->dt[status]]."(".$db->dt[complete_rate]."%)</div>";

			$Contents01 .= "<div id='quick_complate_rate_".$db->dt[wl_ix]."' style='position:absolute;z-index:100;background-color:#efefef;padding:3px;border:1px solid silver;width:105px;display:none;' >";
					foreach($work_complet_rate  as $key => $value){
						if($key == "100"){
							$status_str = "작업완료".($value)."";
						}else if($key == "0"){
							$status_str = "작업대기".($value)."";
						}else if($key == "-1"){
							$status_str = "업무취소";
						}else{
							$status_str = "작업중".($value)."";
						}
						$Contents01 .= "<div onmouseover=\"$(this).css('background-color','#ffffff')\" onmouseout=\"$(this).css('background-color','')\" onclick=\"updateWorkStatus('".$db->dt[wl_ix]."','".$key."');\" style='padding:3px;text-align:left;'>".$status_str." </div>";
					}

			$Contents01 .= "</div>
					</div>
				    
				  </tr>";
if($admininfo[master] == "Y"){
$work_historys = WorkHistoryResult($mdb, $wl_ix);

$work_history_str = "<table>";
for($i=0;$i < count($work_historys);$i++){
	$work_history_str .= "<tr><td>".$work_historys[$i][regdate]."</td><td>".$work_historys[$i][charger]."</td><td>".$work_crud[$work_historys[$i][crud]]."</td><td>".$work_historys[$i][crud_desc]."</td></tr>";
}
$work_history_str .= "</table>";
			$Contents01 .= "
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'> 상태변경내역 : </td>
				    <td class='input_box_item' colspan=3>".$work_history_str."</td>
				  </tr>";
}
		

$Contents01 .= " 
				  </table>
				</div>
				
		</div>
	    </td>
	  </tr>
	  <tr>
		<td align=center>
			<table>
				<tr>
					";
if($charger_ix == $admininfo[charger_ix] || $db->dt[reg_charger_ix] == $admininfo[charger_ix] || in_array($admininfo[charger_ix],$co_charger_ix)){
	
	$Contents01 .= "<td><a href='work_add.php?mmode=".$mmode."&wl_ix=".$wl_ix."'><img src='../image/b_edit.gif' border=0 style='cursor:pointer;' align=absmiddle></a></td>";
	if($db->dt[reg_charger_ix] == $admininfo[charger_ix] || $charger_ix == $admininfo[charger_ix]){
	$Contents01 .= " <td><img src='../image/b_del.gif' border=0 onclick=\"DeleteWorkList('".$wl_ix."','".$mmode."','".$list_view_type."')\" style='cursor:pointer;' align=absmiddle></td>";
	}
}

$Contents01 .= "
				</tr>
			</table>
		</td>
	  </tr>
	  </table> ";
	  


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=1><td></td></tr>";
//$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";

$Contents = $Contents."</table >";

if($mmode != "pop"){
$Contents .= " <table border='0' cellspacing='1' cellpadding='0' width='100%' bgcolor='#ffffff' bordercolor='#ffffff'>
	<col width='50%'>
	<col width='50%'>";
if(false){
$Contents .= " 
	<tr>
		<td  style='padding:0px 0px;' valign=top>      
		<form name=report_frm method=post enctype='multipart/form-data' action='work.act.php' target='iframe_act'><!--target='iframe_act'-->
			<input type='hidden' name='act' value='report_insert'>
			<input type='hidden' name='wl_ix' value='$wl_ix'>
			<input type='hidden' name='mmode' value='$mmode'>

			<table width=100% border=0 >
				<col width='*'>
				<col width='110px'>
				<tr> 
					<td  colspan='2'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'><b> 보고서 작성</b></td>
				</tr>
				<tr>
					<td bgcolor='#ffffff' height='30' colspan='1' style='padding:0 0 0 0'><textarea style='height:70px;width:310px' wrap='off'  basci_message=true name='report_desc' ></textarea></td>
					<td align=right><input type=image src='../images/orange/b_save_square.gif'></td>
				</tr>
				<tr>
					<td bgcolor='' height='30px' colspan='2' style='padding:10px 0 0 0'>보고서 파일 : <input type=file name='report_file' border=0 style='height:20px' align=absmiddle></td>
				</tr>
				<tr> 
				<tr> 
					<td bgcolor='D0D0D0' height='1' colspan='2'></td>
				</tr>
				<!--tr><td colspan=2 align=right style='padding:10px;'> <input type=image src='../images/orange/b_save_square.gif' id='save_btn' border=0 align=absmiddle></td></tr-->
				
			
			</table>
		</form>
		</td>
		<td  style='padding:10px;' valign=top>      
			<form name=commnet_frm method=post enctype='multipart/form-data' action='work.act.php' target='iframe_act'><!--target='iframe_act'-->
				<input type='hidden' name='act' value='comment_insert'>
				<input type='hidden' name='wl_ix' value='$wl_ix'>

				<table width=100% border=0>
					<col width='*'>
					<col width='110px'>
					<tr> 
						<td  colspan='2'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'><b> 컴멘트 작성</b></td>
					</tr>
					<tr>
						<td bgcolor='#ffffff' height='30' colspan='1' style='padding:0 0 0 0'>
						<textarea style='height:70px;width:310px' wrap='off'  basci_message=true name='comment' ></textarea>
						</td>
						<td align=right ><input type=image src='../images/orange/b_save_square.gif'></td>
					</tr>
					<tr>
						<td bgcolor='' height='30' colspan='2' style='padding:10px 0 0 0'>참조 파일 : <input type=file name='comment_file' border=0 style='height:20px' align=absmiddle></td>
					</tr>
					<tr> 
					<tr> 
						<td bgcolor='D0D0D0' height='1' colspan='2'></td>
					</tr>
					<!--tr><td colspan=2 align=right style='padding:10px;'> <input type=image src='../image/btn_counsel_save.gif' id='save_btn' border=0 align=absmiddle></td></tr-->
					<tr> 
						<td colspan=2 align=right style='padding-top:10px;' >
						".PrintComment($wl_ix)."
						</td>
					</tr>
				
				</table>
			</form>
		</td>
	</tr>";
}

if($view_type == ""){$view_type = "comment";}
$Contents .= " 
	<tr>
		<td colspan=2 style='height:0px;padding:0px 0px;'>
		<div class='tab' >
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					
					<table id='tab_02' ".($view_type == "comment" ? "class='on'":"")." onclick=\"WorkViewTab('tab_02')\">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >컴멘트 작성(".$comment_total.")</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($view_type == "issue" ? "class='on'":"")." onclick=\"WorkViewTab('tab_03')\">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >이슈 작성(".$issue_total.")</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_01' ".($view_type == "report" ? "class='on'":"")." onclick=\"WorkViewTab('tab_01')\">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  >보고서 작성(".$report_total.")</td>
						<th class='box_03'></th>
					</tr>
					</table>
					
					
				</td>
				<td style='width:450px;text-align:right;vertical-align:bottom;padding:0 0 10 0'>

				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
	
	<tr>
			<td colspan=2  style='width:100%;padding:10px 0px;".($view_type == "comment" ? "":"display:none;")."' valign=top id='comment_area' >      
			<form name=commnet_frm method=post enctype='multipart/form-data' action='work.act.php' target='iframe_act2' onsubmit='return comment_submit(this);'><!--target='iframe_act'-->
				<input type='hidden' name='act' value='comment_insert'>
				<input type='hidden' name='view_type' value='comment'>
				<input type='hidden' name='wl_ix' value='$wl_ix'>

				<table style='width:100%;' width=100% cellpadding=0 cellspacing=0 border=0>
					<col width='*'>
					<col width='110px'>
					<tr height=30> 
						<td  colspan='2'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'><b> 컴멘트 작성</b></td>
					</tr>
					<tr>
						<td bgcolor='#ffffff' height='30'  style='padding:0 0 0 0'>
						<textarea style='height:50px;width:100%' wrap='off'  basci_message=true name='comment' ></textarea>
						</td>
						<td align=right><input type=image src='../images/orange/b_save_square.gif'></td>
					</tr>
					<tr>
						<td bgcolor='' height='30' colspan='2' style='padding:5px 0 5px 0'><b>참조 파일 : </b><input type=file class='textbox' name='comment_file' border=0 style='height:20px' align=absmiddle></td>
					</tr>
					<tr> 
						<td bgcolor='D0D0D0' height='1' colspan='2'></td>
					</tr>
					<tr> 
						<td colspan=2 align=left style='padding-top:10px;' >
						".PrintComment($wl_ix)."
						</td>
					</tr>
				
				</table>
			</form>
		</td>
	</tr>
	<tr>
			<td colspan=2  style='padding:10px 0px;".($view_type == "issue" ? "":"display:none;")."' valign=top id='issue_area' >      
			<form name=issue_frm method=post enctype='multipart/form-data' action='work.act.php' target='iframe_act' onsubmit='return comment_submit(this);'><!--target='iframe_act'-->
				<input type='hidden' name='act' value='issue_insert'>
				<input type='hidden' name='wl_ix' value='$wl_ix'>
				<input type='hidden' name='view_type' value='issue'>
				<table width=100% cellpadding=0 cellspacing=0 border=0>
					<col width='*'>
					<col width='110px'>
					<tr height=30> 
						<td  colspan='2'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'><b> 이슈 작성</b></td>
					</tr>
					<tr>
						<td bgcolor='#ffffff' height='30'  style='padding:0 0 0 0'>
						<textarea style='height:50px;width:100%' wrap='off'  basci_message=true name='issue' ></textarea>
						</td>
						<td align=right><input type=image src='../images/orange/b_save_square.gif'></td>
					</tr>
					<tr>
						<td bgcolor='' height='20' colspan='2' style='padding:5px 0 5px 0'>
						<input type=radio name='issue_level'  value='H' id='issue_level_h' ".($db->dt[issue_level] == "H" ? "checked":"")."><label for='issue_level_h'>상</label>
						<input type=radio name='issue_level'  value='M' id='issue_level_m' ".($db->dt[issue_level] == "M" ? "checked":"")."><label for='issue_level_m'>중</label>
						<input type=radio name='issue_level'  value='L' id='issue_level_l' ".($db->dt[issue_level] == "L" ? "checked":"")." checked><label for='issue_level_l'>하</label>
						</td>
					</tr>
					<tr> 
						<td bgcolor='D0D0D0' height='1' colspan='2'></td>
					</tr>
					<tr>
						<td bgcolor='' height='20' colspan='2' style='padding:5px 0 5px 0'><b>참조 파일 :</b> <input type=file class=textbox name='issue_file' border=0 style='height:20px' align=absmiddle></td>
					</tr>
					<tr> 
						<td bgcolor='D0D0D0' height='1' colspan='2'></td>
					</tr>
					<tr> 
						<td colspan=2 align=left style='padding-top:10px;' >
						".PrintIssue($wl_ix)."
						</td>
					</tr>
				
				</table>
			</form>
		</td>
	</tr>
	<tr> 
		<td colspan=2 align=right style='padding-top:10px;' >
		".PrintReport($wl_ix)."
		</td>
	</tr>
	<tr>
		<td colspan=2 style='padding:0px 0px;".($view_type == "report" ? "":"display:none;")."' id='report_area'>
		

		<form name=report_form action='work.act.php' method='post' enctype='multipart/form-data' onsubmit='return report_submit(this);' style='display:inline;' target='act'><input type='hidden' name=act value='report_insert'>
		<input type='hidden' name=wl_ix value='$wl_ix'>
		<table cellpadding=3 cellspacing=1  bgcolor=#ffffff border=0 width='100%' style='' class='input_table_box'>
			<tr bgcolor=#efefef>
				<td class='input_box_title'>	<b>보고서 종류 : </b> </td>
				<td class='input_box_item'>
					<table >
						<tr>						
							<td >
								<select name=report_type onchange='ChangeSheet(this.value);'>				
									<option value=''>보고서 종류를 선택해주세요</option>
									<option value=1>기안서</option>
									<option value=0>일반업무보고</option>
									<!--option value=2>휴가계</option-->
									<!--option value=3>일일업무보고서</option-->
									<option value=9>일일업무보고서(신규)</option>
									<option value=4>주간업무보고서</option>
									<option value=5>월업무보고서</option>
									<option value=6>경비신청서</option>
									<option value=7>출장신청서</option>
									<option value=8>회의록</option>
									<option value=99>공지사항</option>
								</select>							
							</td>
							<td class=small> * 일반 내용을 입력 하시고자 하시면 보고서 선택없이 작성하시면 됩니다.</td>
						</tr>
					</table>			
				</td>
			</tr>
			<tr >
				<td class='input_box_title'>	<b>보고서 제목 : </b> </td>
				<td class='input_box_item'> 
				<table >
					<tr>
						<td>
							<input type='text' name='report_title' class='textbox' value='' style='width:380px;text-align:left;' >
						</td>
						<td class=small> </td>
					</tr>
				</table>
				</td>
			</tr>	
		</table>
		<table cellpadding=3 cellspacing=1  bgcolor=#ffffff style='border:1px solid #c0c0c0;border-top:0px;' border=0 width='100%'>
			
			<tr bgcolor=#ffffff>
				<td colspan=2 style='padding:0px;'>
				<textarea style='display:none;'  name='report_desc' ></textarea>
				".WebEdit("..","400px")."</td>
			</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border=0>
			<tr >
				<td colspan=2 align=right valign=top style='padding:0px;padding-right:0px;'>
				<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
					  <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
				</td>
			</tr>
			<tr >
				<td align=right nowrap colspan=2 >
					<table cellpadding=0 cellspacing=0>
					<tr>
						<td style='padding-right:5px;'><img src='../image/btc_del.gif' border=0 id=delete style='cursor:pointer;display:none' onclick=\"BrandSubmit(document.brandform,'delete')\"></td>
						<td><img src='../image/btc_modify.gif' border=0 id=modify style='cursor:pointer;display:none' onclick=\"BrandSubmit(document.brandform,'update')\"></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align=center colspan=2 ><input type=image src='../image/b_save.gif'></td>
			</tr>
		</table>
		</form>
		</td>
	</tr>
</table>   ";
}
$Contents .= " <table border='1' cellspacing='1' cellpadding='15' width='100%' bgcolor='#F8F9FA' bordercolor='#ffffff'>
	<tr>
      
	</tr>
</table>   ";
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' onclick='report_submit(document.report_form)'>
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >업무을 원활히 관리하기 위해서는 그룹을 선택하셔야 합니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>비공개</u>업무는  본인 이외의 리스트에 노출되지 않습니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >부가적으로 회사정보등을 입력해서 관리 하실 수 있습니다.</td></tr>
	</table>
	";


$help_text = HelpBox("업무 상세 보기", $help_text);			
	
$Contents = $Contents.$help_text;	
 $Script = "
<script  id='dynamic'></script>


<script type='text/javascript'>
function comment_submit(frm){
	if(frm.comment.value.length < 1){
		alert('컴멘트를 입력해주세요');
		return false;
	}

	return true;
}

function report_submit(frm){
	
	if(frm.report_title.value.length < 1){
		if($('#iView').contents().find('#sheet_title').html()){
			frm.report_title.value = $('#iView').contents().find('#sheet_title').html();	
		}
	}
	if(frm.report_title.value.length < 1){
		alert('보고서 제목을 입력해주세요');
		frm.report_title.focus();
		return false;
	}
	
	if($('#iView').contents().find('#sheet_contents').html()){
		if($('#iView').contents().find('#sheet_contents').html().length < 1){
			alert('보고서 내용을 입력해 주세요');
			frm.report_desc.focus();
			return false;	
		}
	}
	
	if(frm.report_desc.value.length < 1){
		//alert($('#iView').contents().find('body').html());
		if($('#iView').contents().find('body').html().replace('<P>&nbsp;</P>','')){
			frm.report_desc.value = $('#iView').contents().find('body').html();
		}
	}
	//alert(frm.report_desc.value.length);
	if(frm.report_desc.value.length < 1){
		alert('보고서 내용을 입력해주세요');
		//frm.report_desc.focus();
		return false;
	}
	return true;
}
function DeleteWorkReport(wl_ix, wr_ix){
	if(confirm('해당 업무 보고서를 정말로 삭제하시겠습니까?')){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'report_delete', 'wl_ix':wl_ix, 'wr_ix':wr_ix},
			url: './work.act.php',  
			dataType: 'html', 
			async: false, 
			beforeSend: function(){ 
				
			},  
			success: function(calevents){ 
				//alert(calevents);
				//alert('#report_'+wl_ix+'_'+wr_ix);
				$('#report_'+wl_ix+'_'+wr_ix).slideUp(500);
			} 
		}); 
		//document.frames['act'].location.href='work.act.php?act=delete&wl_ix='+wl_ix;
	}
}

function DeleteWorkComment(wl_ix, wc_ix){
	if(confirm('해당 업무 컴멘트를 정말로 삭제하시겠습니까?')){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'comment_delete', 'wl_ix':wl_ix, 'wc_ix':wc_ix},
			url: './work.act.php',  
			dataType: 'html', 
			async: false, 
			beforeSend: function(){ 
				
			},  
			success: function(calevents){ 
				//alert(calevents);
				//alert('#report_'+wl_ix+'_'+wc_ix);
				$('#comment_'+wl_ix+'_'+wc_ix).slideUp(500);
			} 
		}); 
		//document.frames['act'].location.href='work.act.php?act=delete&wl_ix='+wl_ix;
	}
}


function DeleteWorkIssue(wl_ix, wi_ix){
	if(confirm('해당 업무 이슈를 정말로 삭제하시겠습니까?')){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'issue_delete', 'wl_ix':wl_ix, 'wi_ix':wi_ix},
			url: './work.act.php',  
			dataType: 'html', 
			async: false, 
			beforeSend: function(){ 
				
			},  
			success: function(calevents){ 
				//alert(calevents);
				//alert('#report_'+wl_ix+'_'+wc_ix);
				$('#issue_'+wl_ix+'_'+wi_ix).slideUp(500);
			} 
		}); 
		//document.frames['act'].location.href='work.act.php?act=delete&wl_ix='+wl_ix;
	}
}
 </script>
 ";
	
if($mmode == "pop"  || $mmode == "weelky_pop"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 업무 상세 보기";
	$P->strContents = $Contents;
	$P->NaviTitle = "업무 상세 보기";
	$P->prototype_use = false;
	
	echo $P->PrintLayOut();	
}else{
	$P = new LayOut();
	$P->OnloadFunction = "Init(document.report_form);";
	$P->addScript = "<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script type='text/javascript' src='work.js'></script>".$Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 업무 상세 보기";
	$P->title = "업무 상세 보기";
	$P->footer_menu = footMenu()."".footAddContents();
	$P->strContents = $Contents;
	$P->prototype_use = false;
	
	echo $P->PrintLayOut();
}




function PrintComment($wl_ix){
	global $admininfo, $page, $nset, $QUERY_STRING;
	$mdb = new Database;
	
	
	$mdb->query("select wc.*, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from work_comment wc , common_member_detail cmd where wc.charger_ix = cmd.code and wl_ix ='$wl_ix' order by regdate desc   ");				
	
	if(!$mdb->total){
		$mString = "<div style='width:100%;height:50px;text-align:center;border:1px solid silver;padding-top:20px;'>등록된 컴멘트가 없습니다.</div>";
	}else{
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mString .= "<div id='comment_".$wl_ix."_".$mdb->dt[wc_ix]."'  style='width:100%;background-color:#ffffff;border:1px solid silver;padding:10px 0px;'>
						<table width='100%' cellpadding=0 cellspacing=0>
							<col width='70px'>
							<col width='*'>
							<tr>
								<td rowspan=2 align=center><img src='../images/thum_pic4.gif'></td>
								<td align='left' style='line-height:150%'>".$mdb->dt[regdate]." <b>".$mdb->dt[name]."</b><br><b>".nl2br($mdb->dt[comment])."</b> </td>
								<td valign=top align=right style='padding-right:10px;'>" ;
					if($admininfo[charger_ix] == $mdb->dt[charger_ix]){
					$mString .= "		
								<a href=\"javascript:DeleteWorkComment('$wl_ix', '".$mdb->dt[wc_ix]."')\"><img src='../images/x.gif'></a>";
					}
					$mString .= "		
								</td>
							</tr>
							<tr>
								<td align='left' colspan=2>
								<a href='download.php?wc_ix=".$mdb->dt[wc_ix]."&data_type=comment&data_file=".$mdb->dt[comment_file]."'>".$mdb->dt[comment_file]."</a>
								</td>
							</tr>
						</table>
						</div>";	
		}
	}

	return $mString;
}



function PrintIssue($wl_ix){
	global $admininfo, $page, $nset, $QUERY_STRING;
	$mdb = new Database;
	
	
	$mdb->query("select wi.*, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from work_issue wi , common_member_detail cmd where wi.charger_ix = cmd.code and wl_ix ='$wl_ix' order by regdate desc   ");				
	
	if(!$mdb->total){
		$mString = "<div style='width:100%;height:50px;text-align:center;border:1px solid silver;padding-top:30px;'>등록된 이슈가 없습니다.</div>";
	}else{
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mString .= "<div id='issue_".$wl_ix."_".$mdb->dt[wi_ix]."'  style='width:100%;background-color:#ffffff;border:1px solid silver;margin-bottom:2px;padding:4px;'>
						<table width='100%'>
							<col width='70px'>
							<col width='*'>
							<tr>
								<td rowspan=2 align=center><img src='../images/thum_pic4.gif'></td>
								<td align='left' style='line-height:150%'>".$mdb->dt[regdate]." <b>".$mdb->dt[charger]."</b><br><b>".nl2br($mdb->dt[issue])."</b> </td>
								<td valign=top align=right>" ;
					if($admininfo[charger_ix] == $mdb->dt[charger_ix]){
					$mString .= "		
								<a href=\"javascript:DeleteWorkIssue('$wl_ix', '".$mdb->dt[wi_ix]."')\"><img src='../images/x.gif'></a>";
					}
					$mString .= "		
								</td>
							</tr>
							<tr>
								<td align='left' colspan=2>
								<a href='download.php?wi_ix=".$mdb->dt[wi_ix]."&data_type=issue&data_file=".$mdb->dt[issue_file]."'>".$mdb->dt[issue_file]."</a>
								</td>
							</tr>
						</table>
						</div>";	
		}
	}

	return $mString;
}

function PrintReport($wl_ix){
	global $admininfo, $page, $nset, $QUERY_STRING, $admin_config;
	$mdb = new Database;
	
	
	
	$mdb->query("select wr.*, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from work_report wr, common_member_detail cmd where wl_ix ='$wl_ix' and wr.charger_ix = cmd.code order by wr.regdate desc   ");				
	
	for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);
		$mString .= "<div id='report_".$wl_ix."_".$mdb->dt[wr_ix]."' style='width:100%;background-color:#ffffff;border:1px solid silver;margin-bottom:2px;padding:4px 0px;'>
					<table width='100%' border=0>
						<col width='*'>
						<col width='70px'>
						<tr>
							<td align='left' style='line-height:150%'>
								<table cellpadding=0 cellspacing=0 border=0 width=100%>
								<tr>
									<td rowspan=3 width=70 align=center>";
					if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/profile/profile_".$mdb->dt[charger_ix].".jpg")){
						$mString .= "<img src='".$admin_config[mall_data_root]."/work/profile/profile_".$mdb->dt[charger_ix].".jpg' width=50 height=50>";
					}else{
						$mString .= "<img src='../images/thum_pic4.gif' width=50 height=50>";
					}

					$mString .= "
									</td>
									<td><span class=small><b>".($mdb->dt[report_title] ? $mdb->dt[report_title]:nl2br($mdb->dt[report_desc]))."</b></span></td>
									<td align=right><b>".$mdb->dt[charger]."</b> ".$mdb->dt[regdate]."<br></td>
									<td valign=top align=right style='padding-right:5px;'>";
		if($admininfo[charger_ix] == $mdb->dt[charger_ix]){
		$mString .= "		<a href=\"javascript:DeleteWorkReport('$wl_ix', '".$mdb->dt[wr_ix]."')\"><img src='../images/x.gif'></a>";
		}
		$mString .= "
									</td>
								</tr>
								<tr >
									<td align='right' colspan=3 style='padding:5px 5px 2px 5px'>";
									if($mdb->dt[charger_ix] == $admininfo["charger_ix"]){
											$mString .= "<a href=\"javascript:PopSWindow('work_report_edit.php?mmode=pop&wl_ix=".$mdb->dt[wl_ix]."&wr_ix=".$mdb->dt[wr_ix]."',840,750,'work_report_edit')\"><img src='../images/orange/btn_report_edit.gif' align=absmiddle></a>";
										}
										
									$mString .= "
									".($mdb->dt[report_desc] ? "<div onclick=\"javascript:$('#report_desc_".$mdb->dt[wr_ix]."').toggle()\" style='display:inline;cursor:hand;'><img src='../images/orange/btn_report_view.jpg' align=absmiddle style='margin:0px 4px'></div> ":"")."
									 <a href=\"javascript:PopSWindow('work_mail.pop.php?mmode=pop&wl_ix=".$mdb->dt[wl_ix]."&wr_ix=".$mdb->dt[wr_ix]."',840,750,'work_info')\"><img src='../images/orange/btn_send_email.jpg' align=absmiddle></a> <a href=\"javascript:PopSWindow('work_report_print.php?mmode=pop&wl_ix=".$mdb->dt[wl_ix]."&wr_ix=".$mdb->dt[wr_ix]."',800,750,'work_print')\"><img src='../images/orange/btn_print.jpg' align=absmiddle></a>
									</td>
								</tr>
								</table>
							
							</td>
							
						</tr>
						<tr>
							<td align='left' colspan=2><a href='download.php?wr_ix=".$mdb->dt[wr_ix]."&data_type=report&data_file=".urlencode($mdb->dt[report_file])."'>".$mdb->dt[report_file]."</a> </td>
						</tr>
						
						<tr style='display:none;' id='report_desc_".$mdb->dt[wr_ix]."'>
							<td align='center' colspan=3 style='padding:10px 0px'>".$mdb->dt[report_desc]."</a> </td>
						</tr>
					</table>
					</div>";	
	}

	return $mString;
}
/*

CREATE TABLE `work_op_config` (
  woc_ix int(8) unsigned NOT NULL AUTO_INCREMENT,
  conf_name varchar(100) DEFAULT NULL,
  conf_val varchar(255) DEFAULT NULL,
  regdate datetime DEFAULT NULL,
  PRIMARY KEY (woc_ix)
) TYPE=MyISAM DEFAULT CHARSET=utf8

CREATE TABLE `work_report` (
  wr_ix int(8) unsigned NOT NULL AUTO_INCREMENT,
  wl_ix int(8) DEFAULT NULL,
  report_desc text DEFAULT NULL,
  report_file varchar(255) DEFAULT NULL,
  regdate datetime DEFAULT NULL,
  PRIMARY KEY (wr_ix)
) TYPE=MyISAM DEFAULT CHARSET=utf8


CREATE TABLE IF NOT EXISTS `work_comment` (
  `wc_ix` int(8) unsigned NOT NULL auto_increment,
  `wl_ix` int(8) default NULL,
  `comment` text,
  `comment_file` varchar(255) default NULL,
  `regdate` datetime default NULL,
  PRIMARY KEY  (`wc_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;


CREATE TABLE IF NOT EXISTS `work_issue` (
  `wi_ix` int(8) unsigned NOT NULL auto_increment,
  `wl_ix` int(8) default NULL,
  `issue` text,
  `issue_file` varchar(255) default NULL,
  `issue_level` varchar(1) NOT NULL,
  `charger_ix` int(8) NOT NULL,
  `regdate` datetime default NULL,
  PRIMARY KEY  (`wi_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;




CREATE TABLE `work_action_history` (
  wah_ix int(8) unsigned NOT NULL AUTO_INCREMENT,
  wl_ix int(8) DEFAULT NULL,
  crud enum('C','R','U','D') DEFAULT NULL,
  crud_desc text,
  regdate datetime DEFAULT NULL,
  PRIMARY KEY (wah_ix)
) TYPE=MyISAM DEFAULT CHARSET=utf8

*/
?>