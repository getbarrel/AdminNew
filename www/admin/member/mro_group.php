<?
include("../class/layout.class");

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=2 style='padding-bottom:0px;'> ".GetTitleNavigation("고정단가 회원그룹", "회원관리 > 고정단가 회원그룹 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>고정단가 그룹수정하기</b> </div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	  <col width='20%'>
	  <col width='30%'>
	  <col width='20%'>
	  <col width='35%'>
	  <tr bgcolor=#ffffff  height='35'>
	    <td class='search_box_title' width='16%'> <b>고정단가 회원그룹명 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item' colspan=3>
		<input type=text class='textbox' name='gp_name' value='".$db->dt[gp_name]."' style='width:200px;' validation='true' title='그룹명'> <span class=small></span></td>
		
	  </tr>

	  <tr bgcolor=#ffffff  height='35' >
		<td class='search_box_title'> <b>쿠폰사용 가능여부 <img src='".$required3_path."'> </b> </td>
		<td class='search_box_item'>
			<label for='use_coupon_y'><input type='radio' name='use_coupon_yn' id='use_coupon_y' value='Y' ".($db->dt[use_coupon_yn] == 'Y' || $db->dt[use_coupon_yn] == '' ?'checked':'')."> 사용 </label> 
			<label for='use_coupon_n'><input type='radio' name='use_coupon_yn' id='use_coupon_n' value='N' ".($db->dt[use_coupon_yn] == 'N' ?'checked':'')."> 사용안함 </label> 
		</td>
		<td class='search_box_title'> <b>마일리지 사용/적립 가능여부 <img src='".$required3_path."'> </b> </td>
		<td class='search_box_item'>
			<label for='use_reserve_y'><input type='radio' name='use_reserve_yn' id='use_reserve_y' value='Y' ".($db->dt[use_reserve_yn] == 'Y' || $db->dt[use_reserve_yn] == '' ?'checked':'')."> 사용 </label> 
			<label for='use_reserve_n'><input type='radio' name='use_reserve_yn' id='use_reserve_n' value='N' ".($db->dt[use_reserve_yn] == 'N' ?'checked':'')."> 사용안함 </label> 
		</td>
	  </tr>
	   
	  <tr bgcolor=#ffffff height='30'>
	    <td class='search_box_title'> 회원추가할인  </td>
		<td class='search_box_item'>상품구매시 구매액의 <input type=text class='textbox' name='sale_rate' value='".$db->dt[sale_rate]."' style='width:30px;'> % 할인합니다. <!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D' ,$db)."--><span class=small></span></td>
	   
	    <td class='search_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>미사용</label>
	    </td>
	  </tr>
	  </table>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>회원그룹</u>으로 이용하실 그룹를 입력해주세요
	</td>
</tr>
</table>
";
*/

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents02 =colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>고정단가 그룹목록</b></div>");
$Contents02 .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='margin-top:3px;'>
	  <tr bgcolor=#efefef style='font-weight:bold' align='center' height=30>
	    <td class='s_td' width='5%' height=25>번호</td>
	    <td class='m_td' width='10%'>그룹명</td> 
		<td class='m_td' width='10%'>회원수</td>
		<td class='m_td' width='10%'>상품수</td>
	    <td class='m_td' width='10%'>할인율</td>
		<td class='m_td' width='10%'>쿠폰 사용여부</td>
		<td class='m_td' width='10%'>마일리지 사용여부</td>
	    <td class='m_td' width='12%'>사용여부</td>
	    <td class='e_td' width='13%'>관리</td>
	  </tr>";
$db = new MySQL;

$sql = "SELECT mgi.*,COUNT(md.mgp_ix) AS cnt 
			FROM shop_mro_groupinfo mgi 
			LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." md ON mgi.mgp_ix=md.mgp_ix 
			GROUP BY mgi.mgp_ix  ";
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff align='center' height=30>
		    <td class='list_box_td'>".($i+1)."</td>
		    <td class='list_box_td point'>".$db->dt[gp_name]."</td> 
			<td class='list_box_td'><a href=\"javascript:PopSWindow('mrogroup_register_user.php?mgp_ix=".$db->dt[mgp_ix]."&mmode=".$mmode."&mem_ix=".$mem_ix."',900,800,'cupon_detail_pop');\" ><b>".number_format($db->dt[cnt])." 명</b></a></td>
			<td class='list_box_td'>".number_format($db->dt[product_cnt])." 개</td>
		    <td class='list_box_td'>".$db->dt[sale_rate]." %</td>
		    <td class='list_box_td'>".($db->dt[use_coupon_yn] == "Y" ?  "사용":"사용하지않음")."</td>
			<td class='list_box_td'>".($db->dt[use_reserve_yn] == "Y" ?  "사용":"사용하지않음")."</td>
			<td class='list_box_td'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>

		    <td class='list_box_td'>
		    	<a href=\"javascript:updateGroupInfo('".$db->dt[mgp_ix]."','".$db->dt[sale_rate]."','".$db->dt[gp_name]."','".$db->dt[use_coupon_yn]."','".$db->dt[use_reserve_yn]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
		    	if($db->dt[basic] =="N"){
		    	$Contents02 .= " <a href=\"javascript:deleteGroupInfo('delete','".$db->dt[gp_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
	    		}
	    		$Contents02 .= "
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td class='list_box_td' align=center colspan=9>등록된 고정단가 그룹이 없습니다. </td>
		  </tr>";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=8 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

	  </table>";




$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ><!--img src='../images/".$admininfo["language"]."/b_save.gif' border='0'  style='cursor:pointer;border:0px;' onClick='CheckFormValue(document.group_frm)' /--> </td></tr>
</table>
";


$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='group_frm' action='mro_group.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target='iframe_act'>
<input name='act' type='hidden' value='insert'>
<input name='mgp_ix' type='hidden' value=''>";
$Contents = $Contents."<tr><td width='100%'>";
$Contents = $Contents.$Contents01;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >회원 그룹정보는 위와 같이 9단계의 등급으로 이루어져 있으며 '그룹등급' 은 수정이 불가능하며 각 등급에 맞는 명칭을 변경해서 사용하셔야 합니다</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >사용하지 않으실 회원그룹정보는 수정버튼을 클릭하신후 사용하지 않음으로 선택하신후 저장 버튼을 누르시면 됩니다</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >사용유무가 사용으로 되어 있는 회원그룹만 사용하실수 있게 됩니다</td></tr>
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$Contents .= HelpBox("고정단가 회원그룹", $help_text,'100');

 $Script = "
 <script language='javascript'>

 function updateGroupInfo(mgp_ix,sale_rate,gp_name,use_coupon_yn,use_reserve_yn, disp){
 	var frm = document.group_frm;

 	frm.act.value = 'update';
 	frm.mgp_ix.value = mgp_ix;
 	frm.sale_rate.value = sale_rate;
 	frm.gp_name.value = gp_name;
 	//frm.gp_level.value = gp_level;
 	if(use_coupon_yn == 'Y'){
		frm.use_coupon_yn[0].checked = true;
	}else{
		frm.use_coupon_yn[1].checked = true;
	}
	if(use_reserve_yn == 'Y'){
		frm.use_reserve_yn[0].checked = true;
	}else{
		frm.use_reserve_yn[1].checked = true;
	}
    frm.disp[0].disabled = false;
    frm.disp[1].disabled = false;

    if(disp == '1'){
        frm.disp[0].checked = true;
    }else{
        frm.disp[1].checked = true;
    }
}

 function deleteGroupInfo(act, gp_ix){
 	if(confirm(language_data['group.php']['A'][language])){
		//'해당그룹 정보를 정말로 삭제하시겠습니까?'
 		var frm = document.group_frm;
 		frm.act.value = act;
 		frm.gp_ix.value = gp_ix;
 		frm.submit();
 	}
}

function MemberGroupChange(){
 	if(confirm('아래설정에 따라 회원등급을 변동시키겠습니까?')){
		alert('회원수에따라 오래걸릴수 있습니다. 잠시만 기다려주세요.');
 		location.href='./group.act.php?act=membergroupchange'
 	}
}
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 고정단가 회원그룹";
$P->title = "고정단가 회원그룹";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table ".TBL_SHOP_GROUPINFO." (
gp_ix int(4) unsigned not null auto_increment  ,
gp_name varchar(20) null default null,
gp_level int(2)  default '9' ,
sale_rate varchar(20) null default null,

disp char(1) default '1' ,
regdate datetime not null,
primary key(gp_ix));



CREATE TABLE IF NOT EXISTS `shop_mro_groupinfo` (
  `mgp_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `mall_ix` varchar(32) DEFAULT NULL COMMENT '프론트전시구분',
  `gp_name` varchar(20) DEFAULT NULL COMMENT '그룹명',
  `sale_rate` varchar(20) DEFAULT NULL COMMENT '할인율',
  `gp_level` int(2) DEFAULT '9' COMMENT '그룹레벨',
  `use_coupon_yn` varchar(1) NOT NULL DEFAULT 'Y' COMMENT '쿠폰사용 가능여부',
  `use_reserve_yn` varchar(1) NOT NULL DEFAULT 'Y' COMMENT '마일리지 사용/적립 가능여부',
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `editdate` datetime NOT NULL COMMENT '수정일',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`mgp_ix`),
  KEY `disp` (`disp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='회원그룹'

*/
?>