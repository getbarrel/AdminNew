<?
include("../class/layout.class");
include_once("md.lib.php");





$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width='25%' />
		<col width='30%' />
		<col width='*' />
	  <tr >
		<td align='left' colspan=3> ".GetTitleNavigation("지사관리", "MD 관리 > 지사관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3 style='padding-bottom:15px;'>
	   ".md_tab("branch")."
	    </td>
	</tr>

	  <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>지사명수정하기</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	  <col width = 20% >
	  <col width = * >
	  <tr>
	    <td class='search_box_title' > <b>지역 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item' >
		 ".getRegionInfoSelect('parent_rg_ix', '1차 지역',$parent_rg_ix, $parent_rg_ix, 1, " onChange=\"loadRegion(this,'rg_ix')\" ")."
		 ".getRegionInfoSelect('rg_ix', '2차 지역',$parent_rg_ix, $rg_ix, 2, "validation=true title='지역'")."
		</td>
	  </tr>
	  <tr bgcolor=#ffffff height=25>
	    <td class='search_box_title' > <b>지사명 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item' ><input type=text class='textbox' name='branch_name' value='".$db->dt[branch_name]."' style='width:230px;' validation='true' title='지사명'> <span class=small></span></td>
	  </tr>";

$Contents01 .= "
	  <tr bgcolor=#ffffff height=25>
	    <td class='search_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item'>
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td width='20' align='center'><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td width='*' align=left style='padding:10px;' class=small>
		  <u>지사명</u>으로 이용하실 지사명를 입력해주세요
	</td>
</tr>
</table>
";*/
 $ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents02 =colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>지사명목록</b></div>");
$Contents02 .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
		<col style='width:10%;'>
		<col style='width:*;'>
		<col style='width:20%'>
		<col style='width:15%;'>
		<col style='width:15%;'>
	  <tr height=30 align=center>
	    <td class='s_td'> 번호</td>
	    <td class='m_td'> 지역명</td>
		<td class='m_td'> 지사명</td>
	    <td class='m_td'> 사용여부</td>
	    <td class='e_td'> 관리</td>
	  </tr>";
$db = new Database;
$mdb = new Database;

if($db->dbms_type == "oracle"){
	$db->query("SELECT cb.*, cr.region_name, cr.depth, cr.parent_rg_ix FROM common_branch cb, common_region cr where cb.rg_ix = cr.rg_ix order by level_ asc ");
}else{
	$db->query("SELECT cb.*, cr.region_name, cr.depth, cr.parent_rg_ix FROM common_branch cb, common_region cr where cb.rg_ix = cr.rg_ix order by level asc ");
}

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

	if($db->dt[depth] == 2){
		$mdb->query("SELECT region_name FROM common_region WHERE rg_ix  = '".$db->dt[parent_rg_ix]."' ");
		$mdb->fetch(0);
		$region_name = $mdb->dt[region_name]." > ".$db->dt[region_name];
	}else{
		$region_name = $db->dt[region_name];
	}

	if($db->dbms_type == "oracle"){
		$level = $db->dt[level_];
	}else{
		$level = $db->dt[level];
	}

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".($i+1)."</td>
		    <td class='list_box_td'>".$region_name."</td>
			<td class='list_box_td point'>".$db->dt[branch_name]."</td>

		    <td class='list_box_td'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>

		    <td class='list_box_td list_bg_gray'>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $Contents02 .= "
		    	<a href=\"javascript:updatepositionInfo('".$db->dt[cb_ix]."','".$db->dt[sale_rate]."','".$db->dt[branch_name]."','".$db->dt[branch_name]."','".$db->dt[ps_id]."','".$db->dt[ps_img]."','".$level."','".$db->dt[sale_rate]."','".$db->dt[memberreg_baymoney]."','".$db->dt[use_mall_yn]."','".$db->dt[disp]."','".$db->dt[basic]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
                ";
            }else{
                $Contents02 .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
                ";
            }
		    	if($db->dt[basic] =="N"){
		    	//$Contents02 .= " <a href=\"javascript:deletepositionInfo('delete','".$db->dt[cb_ix]."')\"><img src='../image/btc_del.gif' border=0></a>";
	    		}
	    		$Contents02 .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=8>등록된 지사정보가 없습니다. </td>
		  </tr>
		  <tr height=1><td colspan=8 class='dot-x'></td></tr>	  ";
}
$Contents02 .= "

	  </table>";



if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    $ButtonString = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
        <tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
    </table>
    ";
}
$Contents = "<form name='position_frm' action='branch.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>
<input name='act' type='hidden' value='insert'>
<input name='cb_ix' type='hidden' value=''>
<input name='basic' type='hidden' value=''>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";

$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";
$Contents = $Contents."</form>";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >MD 를 등록 관리하기 위해서는 지사정보가 등록되어야 합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용하지 않으실 지사 정보는 수정버튼을 클릭하신후 사용하지 않음으로 선택하신후 저장 버튼을 누르시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용유무가 사용으로 되어 있는 지사만 사용하실수 있게 됩니다</td></tr>
</table>
";*/


$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$Contents .= HelpBox("지사관리", $help_text, 60);

 $Script = "
 <script language='javascript'>
 function updatepositionInfo(cb_ix,sale_rate,branch_name,branch_name, ps_id,ps_img, level,sale_rate,memberreg_baymoney,use_mall_yn, disp, basic){
 	var frm = document.position_frm;

 	frm.act.value = 'update';
 	frm.cb_ix.value = cb_ix;
 	//frm.sale_rate.value = sale_rate;
 	frm.branch_name.value = branch_name;
 	frm.basic.value = basic;
 	frm.level.value = level;
 	if(ps_img != ''){
 		document.getElementById('ps_img_area').innerHTML =\"<img src='".$admin_config[mall_data_root]."/images/position/\"+ps_img+\"' width='109'>\";
 	}else{
 		document.getElementById('ps_img_area').innerHTML =\"\";
 	}
//	alert(document.getElementById('ps_img_area').innerHTML);
/*

 	for(i=0;i < frm.level.length;i++){
 		if(frm.level[i].value == level){
 			frm.level[i].selected = true;
 		}
 	}
*/
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

 function deletepositionInfo(act, cb_ix){
 	if(confirm('해당지사명 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.position_frm;
 		frm.act.value = act;
 		frm.cb_ix.value = cb_ix;
 		frm.submit();
 	}
}

function loadRegion(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = 'region.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';


}
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > MD 관리 > 지사관리";
$P->title = "지사관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table common_branch (
cb_ix int(4) unsigned not null auto_increment  ,
rg_ix int(4)  default '0' ,
branch_name varchar(20) null default null,
level int(2)  default '9' ,
disp char(1) default '1' ,
regdate datetime not null,
primary key(cb_ix));
*/
?>