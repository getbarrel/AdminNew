<? 
include("../class/layout.work.class");
include("../webedit/webedit.lib.php");
include("work.lib.php");


$db = new Database;
$mdb = new Database;
//print_r($_GET);

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


$Contents = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("보고서 목록", "보고서 > 보고서 목록 ")."</td>
	  </tr>
	  <tr >
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='tab_02' ".($report_type == "" ? "class='on'":"")." >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='work_report_list.php'\">전체 보고서</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_01' ".($report_type == "0" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='work_report_list.php?report_type=0'\" >일반업무보고</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($report_type == "8" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='work_report_list.php?report_type=8'\" >희의록</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($report_type == "3" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='work_report_list.php?report_type=3'\">일일업무보고</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_04' ".($report_type == "4" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='work_report_list.php?report_type=4'\">주간업무보고</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_04' ".($report_type == "5" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='work_report_list.php?report_type=5'\">월간업무보고</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td class='btn' style='padding-left:10px;'>						
					<a href=\"javascript:PopSWindow('work_report_edit.php?mmode=pop',940,750,'work_report_edit')\"><img src='../images/btn_report_write.gif' align=absmiddle></a>
				</td>
			</tr>
			</table>	
		</div>
		</td>
	</tr>
	<tr> 
		<td colspan=2 align=right >";

	$where .= " where wr.charger_ix = cmd.code and wr.company_id = '".$_SESSION['admininfo']['company_id']."' ";
	if($report_type != ""){
		$where .= " and wr.report_type = '".$report_type."' ";
 	}

	$sql = "select count(*) as total from work_report wr, common_member_detail cmd $where   ";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];

	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text","view");
		


	$sql = "select wr.*, wl.wl_ix, wl.work_title, wg.group_name, wg.parent_group_ix,  AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , cmd.sex_div
			from work_report wr 
			left join work_list wl on wr.wl_ix = wl.wl_ix 
			left join work_group wg on wl.group_ix = wg.group_ix , common_member_detail cmd 
			$where
			order by wr.regdate desc limit $start , $max  ";
	$mdb->query($sql);		
	$reports = $mdb->fetchall();

	if(count($reports) == 0){
		$Contents .= "<div id='report_".$wl_ix."_".$reports[$i][wr_ix]."' style='height:80px;width:100%;background-color:#ffffff;border:1px solid silver;margin-bottom:6px;padding:4px 2px;'>
					<table width=100% height=100%><tr><td align=center>등록된 보고서가 없습니다.</td></tr></table>
					</div>";
	}else{
		for($i=0;$i < count($reports);$i++){
			

			if($db->dt[group_depth] == 2){
				$mdb->query("SELECT group_name FROM work_group WHERE group_ix  = '".$reports[$i][parent_group_ix]."' ");
				$mdb->fetch(0);
				$group_name = $mdb->dt[group_name]." > ".$reports[$i][group_name];
			}else{
				$group_name = $reports[$i][group_name];
			}

			$Contents .= "<div id='report_".$wl_ix."_".$reports[$i][wr_ix]."' style='width:99%;background-color:#ffffff;border:1px solid silver;margin-bottom:6px;padding:4px 2px;'>
						<table width='100%' border=0>
							<col width='70px'>
							<col width='*'>
							<tr>
								
								<td align='left' colspan=2 style='line-height:150%'>
									<table cellpadding=0 cellspacing=0 border=0 width=100%>
									<tr>
										<td rowspan=3 width=80 align=center>";
					if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/profile/profile_".$reports[$i][charger_ix].".jpg")){
						$Contents .= "<img src='".$admin_config[mall_data_root]."/work/profile/profile_".$reports[$i][charger_ix].".jpg' width=70 height=70>";
					}else{
						$Contents .= "<img src='../images/".($reports[$i][sex_div] == "M" ? "man.jpg":"women.jpg")."' width=70 height=70>";
					}

					$Contents .= "
										</td>
										<td><span >".($group_name ? $group_name." > <a href='work_view.php?wl_ix=".$reports[$i][wl_ix]."' style='font-weight:bold' class='blue small'>".$reports[$i][work_title]."</a>":"")."</span></td>
										<td align=right style='padding-right:10px;'>
										<b>".$reports[$i][charger]."</b> ".$reports[$i][regdate]."<br>";
					if($admininfo[charger_ix] == $mdb->dt[charger_ix] || $admininfo[charger_id] == "sigi1074"){
							$Contents .= "		<a href=\"javascript:DeleteWorkReport('".$reports[$i][wl_ix]."', '".$reports[$i][wr_ix]."')\"><img src='../images/x.gif'></a>";
					}
										
					$Contents .= "</td>
									</tr>
									<tr><td colspan=2><b>".($reports[$i][report_title] ? $reports[$i][report_title]:"-")."</b> <br></td></tr>
									<tr >
										<td align='right' colspan=3 style='padding:5px 5px'>";
										if($reports[$i][charger_ix] == $admininfo["charger_ix"] || $admininfo[master] == "Y"){
											$Contents .= "<a href=\"javascript:PopSWindow('work_report_edit.php?mmode=pop&wl_ix=".$reports[$i][wl_ix]."&wr_ix=".$reports[$i][wr_ix]."',840,750,'work_report_edit')\"><img src='../images/orange/btn_report_edit.gif' align=absmiddle></a>";
										}
										
										$Contents .= "
										".($reports[$i][report_desc] ? "<div onclick=\"javascript:$('#report_desc_".$reports[$i][wr_ix]."').toggle()\" style='display:inline;cursor:hand;'><img src='../images/orange/btn_report_view.jpg' align=absmiddle style='margin:0px 4px'></div> ":"")."
										 <a href=\"javascript:PopSWindow('work_mail.pop.php?mmode=pop&wl_ix=".$reports[$i][wl_ix]."&wr_ix=".$reports[$i][wr_ix]."',840,750,'work_info')\"><img src='../images/orange/btn_send_email.jpg' align=absmiddle></a> <a href=\"javascript:PopSWindow('work_report_print.php?mmode=pop&wl_ix=".$reports[$i][wl_ix]."&wr_ix=".$reports[$i][wr_ix]."',800,750,'work_print')\"><img src='../images/orange/btn_print.jpg' align=absmiddle></a>
										</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align='left' colspan=2><a href='download.php?wr_ix=".$reports[$i][wr_ix]."&data_type=report&data_file=".urlencode($reports[$i][report_file])."'>".$reports[$i][report_file]."</a> </td>
							</tr>
							<tr style='display:none;' id='report_desc_".$reports[$i][wr_ix]."'>
								<td align='center' colspan=2 style='padding:10px 0px'>".$reports[$i][report_desc]." </td>
							</tr>
						</table>
						</div>";	
		}
	}


$Contents .= "
		</td>
	</tr>
	<tr height=50><td colspan=4 align=center>".$str_page_bar."</td></tr>
	<tr> 
		<td colspan=2 align=right style='padding-top:10px;' >";
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' onclick='report_submit(document.report_form)'>
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >업무에서 담당자 들이 등록한 보고서가 리스팅 됩니다..</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >해당 보고서들을 확인하고 메일을 보내거나 인쇄를 하실 수 있습니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >업무명을 클릭하시면 해당업무로 이동 하실수 있습니다.</td></tr>
	</table>
	";


$help_text = HelpBox("보고서 목록", $help_text);			
	
$Contents = $Contents.$help_text;	
$Contents .= "
		</td>
	</tr>
	</table>";

$Script = "<script type='text/javascript' >
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
</script>
";
	
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = "<!--script type='text/javascript' src='work.js'></script-->".$Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 보고서 목록";
	$P->strContents = $Contents;
	$P->NaviTitle = "보고서 목록";
	$P->title = "보고서 목록";
	$P->prototype_use = false;
	
	echo $P->PrintLayOut();	
}else{
	$P = new LayOut();
	$P->OnloadFunction = "";
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 보고서 목록";
	$P->title = "보고서 목록";
	$P->footer_menu = footMenu()."".footAddContents();
	$P->strContents = $Contents;
	$P->prototype_use = false;
	
	echo $P->PrintLayOut();
}


/*

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