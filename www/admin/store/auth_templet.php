<?
include("../class/layout.class");

$db = new Database;

//print_r($admininfo);

if($admininfo["charger_id"] != "forbiz"){
	$forbiz_where = " and auth_templet_ix <> '1' ";
}

$sql = "SELECT * FROM admin_auth_templet where auth_templet_ix = '".$auth_templet_ix."' ";

$db->query($sql);
$db->fetch();

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='25%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("권한 템플릿관리", "상점관리 >권한 템플릿관리 ")."</td>
	  </tr>

	  ";


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") || true){
$Contents01 .= "
	  <tr height=30 >
	    <td align='left' colspan=2 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>권한 템플릿</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-bottom:15px;' class='input_table_box'> 
	  <col width='25%' />
	  <col width='*' />
	  <tr  height=30 bgcolor=#ffffff >
	    <td class='input_box_title'> <b>권한 템플릿명 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='auth_templet_name' value='".$db->dt[auth_templet_name]."' style='width:230px;' validation='true' title='권한 템플릿명'> <span class=small></span></td>	  
	    <td class='input_box_title'><b>권한 템플릿등급 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='auth_templet_level' value='".$db->dt[auth_templet_level]."' style='width:60px;' validation='true' title='권한 템플릿등급'>
	    </td>
	  </tr>
	  <tr  height=30 bgcolor=#ffffff >
		<!--td class='input_box_title'><b>솔루션 타입 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
			<select name='solution_type'>
				<option value=''>솔루션타입</option>
				<option value=''>홈빌더</option>
				<option value=''>소호형</option>
				<option value=''>비즈형</option>
				<option value=''>도매형</option>
				<option value=''>오픈마켓형(스탠다드)</option>
				<option value=''>오픈마켓형(Enterprise)</option>
			</select>
		</td-->
	    <td class='input_box_title'><b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>";
}
$Contents01 .= "
	  </table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){

/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>회원권한 템플릿</u>으로 이용하실권한 템플릿를 입력해주세요
	</td>
</tr>
</table>";*/

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding-bottom:20px;'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ><!--img src='../image/b_save.gif' border='0'  style='cursor:pointer;border:0px;' onClick='CheckFormValue(document.auth_frm)' /--> </td></tr>
</table>
";
}

$Contents02 =colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>권한 템플릿목록</b></div>");
$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' style='margin-top:5px;' class='list_table_box'>
		<col width='5%' >
	    <col width='20%'>
	    <col width='*'>
	    <col width='15%'>
	    <col width='10%'>
		<col width='20%'>
	    <col width='20%'>

	  <tr bgcolor=#efefef style='font-weight:bold' align='center'>
	    <td class='s_td' height=25>번호</td>
	    <td class='m_td' >권한 템플릿명</td>
	    <td class='m_td'>등록자수</td>
	    <td class='m_td'>권한 템플릿등급</td>
	    <td class='m_td'>사용여부</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'>관리</td>
	  </tr>";

//echo $admininfo[mall_type];
if($admininfo[mall_type] == "F"){
	$sql = "SELECT * FROM admin_auth_templet where use_soho = 'Y'  $forbiz_where order by auth_templet_level asc ";
}else if($admininfo[mall_type] == "B"){
	$sql = "SELECT * FROM admin_auth_templet where use_biz = 'Y' $forbiz_where order by auth_templet_level asc  ";
}else if($admininfo[mall_type] == "O"){
	$sql = "SELECT * FROM admin_auth_templet where use_openmarket = 'Y' $forbiz_where order by auth_templet_level asc  ";
}else{
	$sql = "SELECT * FROM admin_auth_templet where 1  $forbiz_where order by auth_templet_level asc ";
}
//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff align='center' height=30>
		    <td class='list_box_td list_bg_gray'>".($i+1)."</td>
		    <td class='list_box_td point'>".$db->dt[auth_templet_name]."</td>
		    <td class='list_box_td list_bg_gray'>";

		    if ($db->dt[organization_img] != '' && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img])){
			    $Contents02 .= "<img src='".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img]."' width=109>";
			  }
		    $Contents02 .= "
		    </td>
		    <td class='list_box_td'>".$db->dt[auth_templet_level]."  </td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
			<td class='list_box_td'>".$db->dt[regdate]."  </td>
		    <td class='list_box_td list_bg_gray'>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $Contents02 .= "
		    	<a href=\"javascript:updateAuthTempletInfo('".$db->dt[auth_templet_ix]."','".$db->dt[auth_templet_name]."','".$db->dt[auth_templet_level]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 style='vertical-align:middle;'></a>
                <a href='auth_templet_detail.php?auth_templet_ix=".$db->dt[auth_templet_ix]."'><img src='../images/".$admininfo["language"]."/btn_authority_control.gif' style='vertical-align:middle;'></a>
				<a href='auth_templet.php?mode=copy&auth_templet_ix=".$db->dt[auth_templet_ix]."'><img src='../images/".$admininfo["language"]."/btn_authority_copy.gif' style='vertical-align:middle;'></a>
                ";
            }else{
                $Contents02 .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 style='vertical-align:middle;'></a>
                ";
            }
		    	if($db->dt[basic] =="N"){
		    	//$Contents02 .= " <a href=\"javascript:deleteGroupInfo('delete','".$db->dt[auth_templet_ix]."')\"><img src='../image/btc_del.gif' border=0></a>";
	    		}
	    		$Contents02 .= "
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된권한 템플릿이 없습니다. </td>
		  </tr> ";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=7 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

	  </table>";


$Contents = "<form name='auth_frm' action='auth_templet.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)'>
<input name='act' type='hidden' value='insert'>
<input name='mode' type='hidden' value='".$mode."'>
<input name='auth_templet_ix' type='hidden' value='".$auth_templet_ix."'>
<input name='basic' type='hidden' value=''>";
$Contents = $Contents."<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<tr><td width='100%'>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >회원권한 템플릿정보는 위와 같이 9단계의 등급으로 이루어져 있으며 '권한 템플릿등급' 은 수정이 불가능하며 각 등급에 맞는 명칭을 변경해서 사용하셔야 합니다</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >사용하지 않으실 회원권한 템플릿정보는 수정버튼을 클릭하신후 사용하지 않음으로 선택하신후 저장 버튼을 누르시면 됩니다</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >사용유무가 사용으로 되어 있는 회원권한 템플릿만 사용하실수 있게 됩니다</td></tr>
</table>
";*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents .= HelpBox("권한 템플릿", $help_text,'100');

 $Script = "
 <script language='javascript'>
 function updateAuthTempletInfo(auth_templet_ix,auth_templet_name, auth_templet_level, disp){
 	var frm = document.auth_frm;

 	frm.act.value = 'update';
 	frm.auth_templet_ix.value = auth_templet_ix;
 	frm.auth_templet_name.value = auth_templet_name;
 	frm.auth_templet_level.value = auth_templet_level;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

 function deleteGroupInfo(act, auth_templet_ix){
 	if(confirm('해당권한 템플릿 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.auth_frm;
 		frm.act.value = act;
 		frm.auth_templet_ix.value = auth_templet_ix;
 		frm.submit();
 	}
}
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 메뉴관리 >권한 템플릿관리";
$P->title = "권한 템플릿관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table admin_auth_templet (
auth_templet_ix int(4) unsigned not null auto_increment  ,
auth_templet_name varchar(20) null default null,
auth_templet_level int(2)  default '9' ,
disp char(1) default '1' ,
regdate datetime not null,
primary key(auth_templet_ix)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='권한 템플릿';


CREATE TABLE IF NOT EXISTS admin_auth_templet_detail (
  menu_code varchar(32) NOT NULL,
  auth_templet_ix int(4) unsigned not null ,
  `auth_read` enum('Y','N') default 'Y',
  `auth_write_update` enum('Y','N') default 'Y',
  `auth_delete` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`menu_code`,'auth_templet_ix')
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


*/
?>