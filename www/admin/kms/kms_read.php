<? 
include("../class/layout.work.class");
include("kms.lib.php");


$db = new Database;
$mdb = new Database;
//print_r($_GET);

$sql = "select mk.*, mc.depth, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."')  as charger from kms_data mk, kms_mycategory mc, common_member_detail cmd 
		where mc.cid = mk.mycid and mk.uid = '".$admininfo[charger_ix]."' and mk.idx ='$idx' and mk.uid = mc.uid and mk.uid = cmd.code
		order by idx desc ";

//echo $sql;
$db->query($sql);
$db->fetch();

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
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("지식 등록관리", "지식관리 > 지식 등록관리 ")."</td>
	  </tr>
	  <tr >
	    <td align='left' colspan=4>";
if($mmode == "" && false){
$Contents01 .= " 
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
							
							<table id='tab_02' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_list.php'\">지식  관리</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_01' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_group.php'\" >지식 그룹관리</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_add.php'\">지식 등록관리</td>
								<th class='box_03'></th>
							</tr>
							</table>
							
				<td class='btn'>						
					
				</td>
			</tr>
			</table>	
		</div>";
}
$Contents01 .= "		
		<div class='t_no' style='margin: 10px 5px 20px; padding:5px 0px;border-top: solid 3px #c6c6c6; '>
			<!-- my_movie start -->
			<div class='my_box' >
				<form name='div_form' action='work.act.php' method='post' onsubmit='return CheckForm(this)' target='act' style='display:inline;'>
				<input name='act' type='hidden' value='$act'>
				<input name='mmode' type='hidden' value='$mmode'>
				<input name='wl_ix' type='hidden' value='$wl_ix'>
				<input name='company_id' type='hidden' value='".$_SESSION['admininfo']['company_id']."'>
				<div style='padding:10px 0 10px 0;font-size:15px;'>
				".(trim($group_name) == "" ? "<b class='middle_title'>".$group_name."</b>":"")." <b class='middle_title'>".$db->dt[data_name]."</b>
				</div>
				<table width=100% cellpadding=2 cellspacing=1 border=0 cellspacing='1' bgcolor='#c0c0c0' class='input_table_box'>
					<col width=15%>
					<col width=35%>
					<col width=15%>
					<col width=35%>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 담당자 : </b></td>
				    <td colspan=3 class='input_box_item'> <b>".$db->dt[charger]."</b>
									
				    </td>
				  </tr>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 등록일자 : </b> </td>
				    <td class='input_box_item'>".$db->dt[regdate]."</td>				 
				    <td class='input_box_title'><b> 추천수 : </b> </td>
				    <td class='input_box_item' align=left>".($db->dt[recommend] == "" ? "0":"")."</td>
				  </tr>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 지식제목 : </b></td>
					<td class='input_box_item' colspan=3 style='line-height:170%;'>
					".(trim($group_name) == "" ? "<b>".$group_name."</b>":"")."
					".$db->dt[data_name]."
					</td>
				    
				  </tr-->
				  <!--tr height=1><td colspan=4 background='../image/dot.gif'></td></tr-->
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b>지식상세 :</b> </td>
				    <td  class='input_box_item'colspan=3 style='height:150px;vertical-align:top;padding:10 0 0 10;line-height:150%'>".nl2br($db->dt[data_text])."</td>				    
				  </tr>
				  <tr bgcolor=#ffffff >
				  	<td class='input_box_title'>공개여부</td>
				    <td class='input_box_item' colspan=3>
				    ".(($db->dt[open] == "1") ? "비공개":"공개")."<br>
				    </td>
				  </tr>
				 
				  <tr bgcolor=#ffffff >
				  	<td class='input_box_title'>관련링크</td>
				    <td class='input_box_item' colspan=3>
				    <a href='".$db->dt[data_link]."' target='_blank'>".$db->dt[data_link]."</a><br>
				    </td>
				  </tr>
				  <tr bgcolor=#ffffff >
				  	<td class='input_box_title'>검색어</td>
				    <td class='input_box_item' colspan=3>
				    ".$db->dt[keyword]."
				    </td>
				  </tr>";
				  
if($charger_ix == $admininfo[charger_ix] || $db->dt[reg_charger_ix] == $admininfo[charger_ix] || $act == "insert" || false){
	//$Contents01 .= "<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>";
}
$Contents01 .= " 
				  </table>
				  </form>
				</div>
				
		</div>
	    </td>
	  </tr>
	  
	  </table> ";
	  


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
//$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";

$Contents = $Contents."</table >";

$Contents .= " <table border='1' cellspacing='1' cellpadding='15' width='100%' bgcolor='#F8F9FA' bordercolor='#ffffff'>
	<col width='50%'>
	<col width='50%'>
	<tr>
      
	<td  style='padding:10px;' valign=top>      
		<form name=commnet_frm method=post enctype='multipart/form-data' action='kms.act.php' ><!--target='iframe_act'-->
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
					<textarea style='height:50px;width:100%' wrap='off'  basci_message=true name='comment' ></textarea>
					</td>
					<td align=right><input type=image src='../images/orange/b_save_square.gif'></td>
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
					<td colspan=2 align=right style='padding-top:10px;' id='report_area'>
					".PrintKMSComment($wl_ix)."
					</td>
				</tr>
			
			</table>
		</form>
	</td>
	</tr>
</table>   ";

$Contents .= " <table border='1' cellspacing='1' cellpadding='15' width='100%' bgcolor='#F8F9FA' bordercolor='#ffffff'>
	<tr>
      
	</tr>
</table>   ";
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >지식을 원활히 관리하기 위해서는 그룹을 선택하셔야 합니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>비공개</u>지식는  본인 이외의 리스트에 노출되지 않습니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >부가적으로 회사정보등을 입력해서 관리 하실 수 있습니다.</td></tr>
	</table>
	";


$help_text = HelpBox("지식 등록 관리", $help_text);			
	
$Contents = $Contents.$help_text;	
 $Script = "

 <script language='javascript'>

function DeleteWorkComment(wl_ix, wc_ix){
	if(confirm('해당 지식 컴멘트를 정말로 삭제하시겠습니까?')){
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
 </script>
 ";
	
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = "<!--script type='text/javascript' src='work.js'></script-->".$Script;
	$P->strLeftMenu = kms_menu();
	$P->Navigation = "지식관리 > 지식 등록관리";
	$P->strContents = $Contents;
	$P->NaviTitle = "지식 등록관리";
	$P->prototype_use = false;
	
	echo $P->PrintLayOut();	
}else{
	$P = new LayOut();
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	$P->strLeftMenu = kms_menu();
	$P->Navigation = "지식관리 > 지식 등록관리";
	$P->title = "지식 등록관리";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	
	echo $P->PrintLayOut();
}



							
function PrintOrderMemo($oid){
	global $admininfo, $page, $nset, $QUERY_STRING;
	$mdb = new Database;
	
	$sql = "select count(*) as total from shop_order_memo where oid ='$oid'    ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];
	
	
	$max = 10;
	
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
		
	
	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";	
	$mString = $mString."
				<form name=listform method=post action='orders.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
				<input type='hidden' name='act' value='memo_insert'>
				<input type='hidden' name='oid' value='$oid'>		
				<col width='15%'>
				<col width='10%'>
				<col width='*'>
				<col width='10%'>
				<tr align=center bgcolor=#efefef height=25>	
					<td class=s_td>상담일자</td>
					<td class=m_td>상담자</td>
					<td class=m_td>상담내용</td>
					<td class=e_td>관리</td>
				</tr>";	
	if ($total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=9 align=center>컴맨트 내역이  존재 하지 않습니다.</td></tr>";
	}else{
		
		$mdb->query("select * from shop_order_memo where oid ='$oid' order by regdate desc   limit $start , $max");		
			
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
					
			$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>			
			<!--td bgcolor='#ffffff'><input type=checkbox class=nonborder id='om_ix' name='om_ix[]' value='".$mdb->dt[om_ix]."'></td-->			
			<td bgcolor='#efefef'>".$mdb->dt[regdate]."</td>
			<td >".$mdb->dt[counselor]."</td>
			<td bgcolor='#efefef' align=left style='padding-left:10px;' style='word-break:break-all'>".nl2br($mdb->dt[memo])."</td>
			<td bgcolor='#ffffff' align=center nowrap>
				
				<a href=JavaScript:memoDelete('".$mdb->dt[oid]."','".$mdb->dt[om_ix]."')><img  src='../image/btc_del.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=6 background='../image/dot.gif'></td></tr>
			";
		}
		
		//$mString .= "<tr bgcolor=#ffffff height=40><td colspan=8 align=left><a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../image/bt_all_del.gif' border=0 align=absmiddle ></a></td></tr>";
	}
	
	//$query_string = str_replace("nset=$nset&page=$page&","",$QUERY_STRING) ;
	//echo $query_string;
	$mString .= "</form>";
	//$mString .= "<tr height=50 bgcolor=#ffffff><td colspan=6 align=left>".page_bar($total, $page, $max,"&".$query_string,"")."</td></tr>
	$mString .= "</table>";
	
	return $mString;
}


function PrintKMSComment($wl_ix){
	global $admininfo, $page, $nset, $QUERY_STRING;
	$mdb = new Database;
	
	$sql = "select count(*) as total from work_comment where wl_ix ='$wl_ix'    ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];
	
	$mdb->query("select * from work_comment where wl_ix ='$wl_ix' order by regdate desc   ");				
	
	for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);
		$mString .= "<div id='comment_".$wl_ix."_".$mdb->dt[wc_ix]."'  style='width:98%;background-color:#ffffff;border:1px solid silver;margin-bottom:2px;padding:4px;'>
					<table width='100%'>
						<col width='70px'>
						<col width='*'>
						<tr>
							<td rowspan=2 align=center><img src='../images/thum_pic4.gif'></td>
							<td align='left' style='line-height:150%'>".$mdb->dt[regdate]."<br><b>".nl2br($mdb->dt[comment])."</b> </td>
							<td valign=top>" ;
				if($admininfo[charger_ix] == $mdb->dt[charger_ix]){
				$mString .= "		
							<a href=\"javascript:DeleteWorkComment('$wl_ix', '".$mdb->dt[wc_ix]."')\"><img src='../images/x.gif'></a>";
				}
				$mString .= "		
							</td>
						</tr>
						<tr>
							<td align='left' colspan=2>
							<a href='download.php?wr_ix=".$mdb->dt[wc_ix]."&data_type=comment&data_file=".$mdb->dt[comment_file]."'>".$mdb->dt[comment_file]."</a>
							</td>
						</tr>
					</table>
					</div>";	
	}

	return $mString;
}


function PrintReport($wl_ix){
	global $admininfo, $page, $nset, $QUERY_STRING;
	$mdb = new Database;
	
	$sql = "select count(*) as total from work_report where wl_ix ='$wl_ix'    ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];
	
	$mdb->query("select * from work_report where wl_ix ='$wl_ix' order by regdate desc   ");				
	
	for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);
		$mString .= "<div id='report_".$wl_ix."_".$mdb->dt[wr_ix]."' style='width:98%;background-color:#ffffff;border:1px solid silver;margin-bottom:2px;padding:4px;'>
					<table width='100%'>
						<col width='70px'>
						<col width='*'>
						<tr>
							<td rowspan=2 align=center><img src='../images/thum_pic4.gif'></td>
							<td align='left' style='line-height:150%'>".$mdb->dt[regdate]."<br><b>".nl2br($mdb->dt[report_desc])."</b> </td>
							<td valign=top><a href=\"javascript:DeleteWorkReport('$wl_ix', '".$mdb->dt[wr_ix]."')\"><img src='../images/x.gif'></a></td>
						</tr>
						<tr>
							<td align='left' colspan=2><a href='download.php?wr_ix=".$mdb->dt[wr_ix]."&data_type=report&data_file=".urlencode($mdb->dt[report_file])."'>".$mdb->dt[report_file]."</a> </td>
						</tr>
					</table>
					</div>";	
	}

	return $mString;
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


*/
?>