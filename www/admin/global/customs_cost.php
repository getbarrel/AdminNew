<?
include("../class/layout.class");
//include_once("buyingService.lib.php");

$db = new MySQL;


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("통관사비용 관리", "국제코드 관리 > 통관사비용 관리 ")."</td>
	  </tr>"; 
$Contents01 .= "
 <tr>
			<td align='left' colspan=4 style='padding-bottom:11px;'>
				<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>"; 
$Contents01 .= "
								<table id='tab_01' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='shipping_transport_cost.php'\">해상 운송비</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_02' class=on >
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

$Contents01 .= "
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>통관사비용 관리 등록/수정</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='15%'>
	  <col width='35%'>
	  <col width='15'>
	  <col width='35%'>";
 /*
 
CREATE TABLE `global_customs` (
  `customs_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_transport_costs` varchar(20)  DEFAULT NULL,
  `domestic_delivery_costs` varchar(10)  DEFAULT NULL,
  `bond_warehouse_costs` varchar(10)  DEFAULT NULL,
  `port_costs` varchar(10)  DEFAULT NULL,
  `storage_costs` varchar(10)  DEFAULT NULL,
  `is_use` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`customs_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 comment '관세비용 정보 '
 */
$Contents01 .= " 	   
	  <tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>해상 운송비</b> </td>
		<td class='input_box_item'><input type=text class='textbox numeric' name='shipping_transport_costs' value='".$db->dt[shipping_transport_costs]."' style='width:70%;' validation=true title='해상 운송비'> $ </td>
		<td class='input_box_title'> <b>국내 운송비</b> </td>
		<td class='input_box_item'><input type=text class='textbox numeric' name='domestic_delivery_costs' value='".$db->dt[domestic_delivery_costs]."' style='width:70%;' validation=true title='국내 운송비'> $ </td>
	  </tr>
	  <tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>보세 창고비</b> </td>
		<td class='input_box_item'><input type=text class='textbox numeric' name='bond_warehouse_costs' value='".$db->dt[bond_warehouse_costs]."' style='width:70%;' validation=true title='보세 창고비'> $ </td>
		<td class='input_box_title'> <b>항구 비용</b> </td>
		<td class='input_box_item'><input type=text class='textbox numeric' name='port_costs' value='".$db->dt[port_costs]."' style='width:70%;' validation=true title='항구 비용'> $ </td>
	  </tr>
	  <tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>창고비</b> </td>
		<td class='input_box_item'><input type=text class='textbox numeric' name='storage_costs' value='".$db->dt[storage_costs]."' style='width:70%;' validation=true title='창고비'> $ </td>
		<td class='input_box_title'> 사용여부</td> 
		 <td class='input_box_item'> 
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
		  <u>$board_name </u> 에 이용할 통관사비용 관리명을  입력해주세요
	</td>
</tr>
</table>
";*/
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
/*$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr>
			<td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  통관사비용 관리 목록</b></div>")."</td>
		</tr>
	</table>";*/
$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>통관사비용 관리  목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
		 
	    
		<col width=4%>
	    <col width=10%>
		<col width=10%>
		<col width=6%>
	    <col width=6%>
		<col width=6%>
	    <col width=6%>
		<col width=6%>
		<col width=10%>
		<col width=10%>
		<col width=10%>
		<col width=10%>
	  <tr bgcolor=#ffffff  height=30>
	    <td class='s_td ctr'> <b>번호 </b> </td> 
		<td class='m_td ctr' > <b>해상운송비</b> </td>
		<td class='m_td ctr' > <b>국내 운송비</b> </td>
		<td class='m_td ctr' > <b>보세 창고비</b> </td>
		<td class='m_td ctr' > <b>항구비용</b> </td>
		<td class='m_td ctr' > <b>창고비</b> </td>
		<td class='m_td ctr' > <b>사용여부</b> </td>
		<td class='m_td ctr' > <b>등록일자</b> </td>
		<td class='e_td ctr' > <b>관리</b> </td>
	  </tr> 
	  ";

$sql = "select *  from global_customs where 1  order by  regdate desc ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then customs_ix  else parent_customs_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.customs_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by customs_ix
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
				".$i."
			</td> 
		     <td class='list_box_td point' >
				".$db->dt[shipping_transport_costs]." $
			</td>
			<td class='list_box_td' >".$db->dt[domestic_delivery_costs]." $</td>
			<td class='list_box_td' >".$db->dt[bond_warehouse_costs]." $</td>
			<td class='list_box_td' >".$db->dt[port_costs]." $</td>
			<td class='list_box_td' >".$db->dt[storage_costs]." $</td>
		    <td class='list_box_td '>".($db->dt[is_use] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>
		    	<a  href=\"javascript:updateCustomsInfo('".$db->dt[customs_ix]."','".$db->dt[shipping_transport_costs]."','".$db->dt[domestic_delivery_costs]."','".$db->dt[bond_warehouse_costs]."','".$db->dt[port_costs]."','".$db->dt[storage_costs]."','".$db->dt[is_use]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteCustomsInfo('delete','".$db->dt[customs_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=9>등록된 통관사 비용 정보가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "

	  </table>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";



$Contents = "<form name='customs_form' action='customs_cost.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='insert'> 
<input name='customs_ix' type='hidden' value=''>";
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
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >운송비 관리 통관사비용 관리 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>통관사비용 관리명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("통관사비용 관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 
 function updateCustomsInfo(customs_ix,shipping_transport_costs,domestic_delivery_costs, bond_warehouse_costs, port_costs, storage_costs, is_use){
 	var frm = document.customs_form;

 	frm.act.value = 'update';
 	frm.customs_ix.value = customs_ix; 
	frm.shipping_transport_costs.value = shipping_transport_costs;
	frm.domestic_delivery_costs.value = domestic_delivery_costs;
	frm.bond_warehouse_costs.value = bond_warehouse_costs;
	frm.port_costs.value = port_costs;
	frm.storage_costs.value = storage_costs;
	frm.customs_desc.value = customs_desc; 
	if(is_use=='1') {
		frm.is_use[0].checked = true;
	} else {
		frm.is_use[1].checked = true;
	}
 

}

 function deleteCustomsInfo(act, customs_ix){
 	if(confirm('해당NCMCODE  정보를 정말로 삭제하시겠습니까?')){//'해당통관사비용 관리 정보를 정말로 삭제하시겠습니까?'
 		var frm = document.customs_form;
 		frm.act.value = act;
 		frm.customs_ix.value = customs_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = global_menu();
	$P->Navigation = "운송비 관리 > 통관사비용 관리";
	$P->title = "통관사비용 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = global_menu();
	$P->Navigation = "운송비 관리 > 통관사비용 관리";
	$P->title = "통관사비용 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE `global_customs` (
  `customs_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_transport_costs` varchar(20)  DEFAULT NULL,
  `domestic_delivery_costs` varchar(10)  DEFAULT NULL,
  `bond_warehouse_costs` varchar(10)  DEFAULT NULL,
  `port_costs` varchar(10)  DEFAULT NULL,
  `storage_costs` varchar(10)  DEFAULT NULL,
  `is_use` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`customs_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 comment '관세비용 정보 '
*/
?>