<?
include("../class/layout.class");
//include_once("buyingService.lib.php");

$db = new MySQL;


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("".$transport_type_title." 운송비관리", "운송비 관리 > ".$transport_type_title." 운송비관리 ")."</td>
	  </tr>";
if($transport_type == "S"){
$Contents01 .= "
 <tr>
			<td align='left' colspan=4 style='padding-bottom:11px;'>
				<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>"; 
$Contents01 .= "
								<table id='tab_01' class=on >
								<tr>
									<th class='box_01'></th>
									<td class='box_02'  >".$transport_type_title." 운송비</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_02' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='customs_cost.php'\">통관사 비용</td>
									<th class='box_03'></th>
								</tr>
								</table>";
 
$Contents01 .= "
							</td>
							<td class='btn' style='padding:10px 0px 0px 10px;'>
								 
							</td>
						</tr>
						</table>
					</div>
			</td>
		</tr>";
}
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>".$transport_type_title." 운송비 등록/수정</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='25%'>
	  <col width='25%'>
	  <col width='25'>
	  <col width='25%'>";
 
$Contents01 .= "
	  <tr bgcolor=#ffffff  >
		<td class='input_box_title ctr'> <b>국가 </b> </td>
	    <td class='input_box_title ctr'> <b>".$transport_type_title." 화물 무게 </b> </td>
	    <td class='input_box_title ctr'> <b>운송비용(kg 당)</b> </td>
		<td class='input_box_title ctr'> <b>사용여부</b> </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_item ctr'>
			".getNationInfo()."
		</td>
		<td class='input_box_item ctr'><input type=text class='textbox' name='end_weight' value='".$db->dt[end_weight]."' style='width:130px;' validation=true title='항공화물 무게'> kg </td>
	 
	    <td class='input_box_item ctr'> <input type=text class='textbox' name='cost' value='".$db->dt[cost]."' style='width:130px;' validation=true title='항공화물 운송비용'> 원 </td> 
		 <td class='input_box_item ctr'> 
			<input type=radio name='is_use' id='is_use_1' value='1' ".($db->dt[is_use] == "1" || $db->dt[is_use] == "" ? "checked":"")." ><label for='is_use_1'>사용</label> 
			<input type=radio name='is_use' id='is_use_0' value='0'  ".($db->dt[is_use] == "0" ? "checked":"")."><label for='is_use_0'>사용안함</label> 
		</td> 
	  </tr>
	  </table>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u> 에 이용할 ".$transport_type_title." 운송비관리명을  입력해주세요
	</td>
</tr>
</table>
";*/
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
/*$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr>
			<td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  ".$transport_type_title." 운송비관리 목록</b></div>")."</td>
		</tr>
	</table>";*/

$nation = getNationInfo("","","array");

$nation_search_select = "<select onchange=\"location.href='?search_nation_code='+this.value\" >
	<option>국가 선택</option>";
	
	for($i=0; $i < count($nation); $i++){
		$nation_search_select .= "<option value='".$nation[$i]['nation_code']."'>".$nation[$i]['nation_name']."</option>";
	}

$nation_search_select .= "
</select>";

$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>".$transport_type_title." 운송비  목록</b> ".$nation_search_select."</div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
		 
	    
		<col width=10%>
	    <col width=10%>
		<col width=10%>
		<col width=10%>
	    <col width=15%>
	    <col width=15%>
	  <tr bgcolor=#ffffff  >
		<td class='input_box_title ctr' > <b>국가</b> </td>
	    <td class='input_box_title ctr' > <b>".$transport_type_title." 화물 무게 (KG)</b> </td>
	    <td class='input_box_title ctr' > <b>운송비용(원)</b> </td>
		<td class='input_box_title ctr' > <b>사용여부</b> </td>
		<td class='input_box_title ctr' > <b>등록일자</b> </td>
		<td class='input_box_title ctr' > <b>관리</b> </td>
	  </tr>
	  ";


$sql = "select tf.*,n.nation_name from global_transport_fee tf left join global_nation n on (tf.nation_code=n.nation_code) where tf.transport_type = '$transport_type' and tf.nation_code='".$search_nation_code."'  order by  tf.start_weight , tf.end_weight ";
$db->query($sql);


/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then tf_ix  else parent_tf_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.tf_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by tf_ix
		order by group_order asc, depth asc";



//echo $sql;
$db->query($sql);
*/

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td' style='padding-left:20px;'>
				".$db->dt[nation_name]."
			</td>
		     <td class='list_box_td point' style='padding-left:20px;'>
				".$db->dt[end_weight]."
			</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[cost]."</td>
		    <td class='list_box_td '>".($db->dt[is_use] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>
		    	<a href=\"javascript:updateTransportCostInfo('".$db->dt[tf_ix]."','".$db->dt[nation_ix]."','".$db->dt[end_weight]."','".$db->dt[cost]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[is_use]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteTransportCostInfo('delete','".$db->dt[tf_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 운송비 정보가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "

	  </table>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";



$Contents = "<form name='transport_cost_form' action='transport_cost.act.php' method='post' onsubmit='return CheckFormValue(this)' target=act>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='insert'>
<input name='transport_type' type='hidden' value='$transport_type'>
<input name='tf_ix' type='hidden' value=''>";
$Contents .= "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents .= "<tr><td width='100%'>";
$Contents .= $Contents01."<br>";
$Contents .= "</td></tr>";
$Contents .= "<tr><td>".$ContentsDesc01."</td></tr>";
$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr><td>".$ButtonString."</td></tr>";

$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr><td>".$Contents02."<br></td></tr>";
$Contents .= "<tr height=30><td></td></tr>";
$Contents .= "</table >";
$Contents .= "</form>";
/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >운송비 관리 ".$transport_type_title." 운송비관리 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>".$transport_type_title." 운송비관리명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("".$transport_type_title." 운송비관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 function CheckValue(frm){
 	if(frm.depth[1].checked){
 		if(frm.parent_tf_ix.value == ''){
	 		alert(language_data['site.php']['A'][language]);
			//'2차 항공 운송비관리을 등록하기 위해서는 1차항공 운송비관리을 반드시 선택하셔야 합니다.'
	 		return false;
 		}
 	}

 	if(frm.start_weight.value.length < 1){
 		alert(language_data['site.php']['B'][language]);
		//'등록하시고자 하는 운송비 관리 항공 운송비관리명을 입력해주세요'
 		frm.start_weight.focus();
 		return false;
 	}

 }
 function updateTransportCostInfo(tf_ix,nation_ix,end_weight,cost,depth, vieworder,group_order,is_use){
 	var frm = document.transport_cost_form;

 	frm.act.value = 'update';
 	frm.tf_ix.value = tf_ix;
 	frm.nation_ix.value = nation_ix;
	frm.end_weight.value = end_weight;
	frm.cost.value = cost; 
	if(is_use=='1') {
		frm.is_use[0].checked = true;
	} else {
		frm.is_use[1].checked = true;
	}
 

}

 function deleteTransportCostInfo(act, tf_ix){
 	if(confirm('해당항공 운송비관리 정보를 정말로 삭제하시겠습니까?')){//'해당항공 운송비관리 정보를 정말로 삭제하시겠습니까?'
 		var frm = document.transport_cost_form;
 		frm.act.value = act;
 		frm.tf_ix.value = tf_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = global_menu();
	$P->Navigation = "운송비 관리 > 항공 운송비관리";
	$P->title = "항공 운송비관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = global_menu();
	$P->Navigation = "운송비 관리 > 항공 운송비관리";
	$P->title = "항공 운송비관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE `global_transport_fee` (
  `tf_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `transport_type` enum('A','S','E')  DEFAULT 'A' comment '운송타입 A:항공운성, S:해상운송, E:기타 ',
  `start_weight` int(10) unsigned DEFAULT NULL,
  `end_weight` int(10) unsigned DEFAULT NULL,
  `cost` int(10) unsigned DEFAULT '0',
  `is_use` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`tf_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/
?>