<?
include("../class/layout.class");

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=2 style='padding-bottom:0px;'> ".GetTitleNavigation("모델관리", "상품관리 > 상품분류관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>모델수정하기</b><span class='small'> </span></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	  <col width=15%>
	  <col width=35%>
	  <col width=*>
	  <tr bgcolor=#ffffff  height='35'>
	    <td class='search_box_title' width='16%'> <b>모델명 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item'>
		<input type=text class='textbox' name='model_name' value='".$db->dt[model_name]."' style='width:200px;' validation='true' title='모델명'> <span class=small></span>
		</td>
		
		<td class='search_box_title'> 모델 이미지 </td>
	  </tr>

	  <tr bgcolor=#ffffff height='35'>
	    <td class='search_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>미사용</label>
	    </td>
	    <td class='search_box_item' rowspan=10>
	    <input type=file class='textbox' name='organization_img'> <span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
	    <div id='organization_img_area' ></div>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff height='35'>
	    <td class='search_box_title'> <b>모델 키 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item' >
		<input type=text class='textbox' name='model_height' value='".$db->dt[model_height]."' style='width:60px;' validation='true' title='모델 키'> kg 
	    </td>
	  </tr>
	   <tr bgcolor=#ffffff height='35'>
	    <td class='search_box_title'> <b>모델 사이즈 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item' >
		<input type=text class='textbox' name='model_size' value='".$db->dt[model_size]."' style='width:60px;' validation='true' title='모델 사이즈'> cm	 
	    </td>
	  </tr> 
	  <tr bgcolor=#ffffff height='35'>
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
		  <u>회원모델</u>으로 이용하실 모델를 입력해주세요
	</td>
</tr>
</table>
";
*/

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents02 =colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>모델목록</b></div>");
$Contents02 .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='margin-top:3px;'>
	  <tr bgcolor=#efefef style='font-weight:bold' align='center' height=30>
	    <td class='s_td' width='60' height=25>번호</td>
	    <td class='m_td' width='120'>모델명</td>
	    <td class='m_td' width='*'>모델이미지</td>
	    <td class='m_td' width='100'>모델등급</td>
		<td class='m_td' width='100'>회원수</td>
	    <td class='m_td' width='100'>할인율</td>
	    <td class='m_td' width='120'>사용여부</td>
	    <!--td style='width:170px;'> 등록일자</td-->
	    <td class='e_td' width='130'>관리</td>
	  </tr>";
$db = new MySQL;


//$db->query("SELECT gi.*,COUNT(md.model_ix) AS cnt FROM ".TBL_SHOP_GROUPINFO." gi LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." md ON gi.model_ix=md.model_ix GROUP BY gi.model_ix order by gi.model_level asc ");


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff align='center' height=30>
		    <td class='list_box_td'>".($i+1)."</td>
		    <td class='list_box_td point'>".$db->dt[model_name]."</td>
		    <td class='list_box_td' align=center style='padding:10px;'>";

		    if ($db->dt[organization_img] != '' && file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img])){
			    $Contents02 .= "<img src='".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img]."' width=109>";
			  }
		    $Contents02 .= "
		    </td>
		    <td class='list_box_td'>".$db->dt[model_level]." </td>
			<td class='list_box_td'>".$db->dt[cnt]." </td>
		    <td class='list_box_td'>".$db->dt[sale_rate]." %</td>
		    <td class='list_box_td'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>

		    <td class='list_box_td'>
		    	<a href=\"javascript:updateGroupInfo('".$db->dt[model_ix]."','".$db->dt[sale_rate]."','".$db->dt[model_name]."','".$db->dt[organization_name]."','".$db->dt[organization_id]."','".$db->dt[organization_img]."','".$db->dt[model_level]."','".$db->dt[cnt]."','".$db->dt[sale_rate]."','".$db->dt[memberreg_baymoney]."','".$db->dt[use_mall_yn]."','".$db->dt[disp]."','".$db->dt[basic]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
		    	if($db->dt[basic] =="N"){
		    	$Contents02 .= " <a href=\"javascript:deleteGroupInfo('delete','".$db->dt[model_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
	    		}
	    		$Contents02 .= "
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td class='list_box_td' align=center colspan=8>등록된 모델이 없습니다. </td>
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
$Contents = $Contents."<form name='group_frm' action='group.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)'>
<input name='act' type='hidden' value='insert'>
<input name='model_ix' type='hidden' value=''>
<input name='basic' type='hidden' value=''>";
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
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >회원 모델정보는 위와 같이 9단계의 등급으로 이루어져 있으며 '모델등급' 은 수정이 불가능하며 각 등급에 맞는 명칭을 변경해서 사용하셔야 합니다</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >사용하지 않으실 회원모델정보는 수정버튼을 클릭하신후 사용하지 않음으로 선택하신후 저장 버튼을 누르시면 됩니다</td></tr>
	<tr><td><img src='/admin/image/icon_list.gif' align='absmiddle'></td><td class='small' >사용유무가 사용으로 되어 있는 회원모델만 사용하실수 있게 됩니다</td></tr>
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$Contents .= HelpBox("모델관리", $help_text,'100');

 $Script = "
 <script language='javascript'>
 function updateGroupInfo(model_ix,sale_rate,model_name,organization_name, organization_id,organization_img, model_level,mem_cnt,sale_rate,memberreg_baymoney,use_mall_yn, disp, basic){
 	var frm = document.group_frm;

 	frm.act.value = 'update';
 	frm.model_ix.value = model_ix;
 	frm.sale_rate.value = sale_rate;
 	frm.model_name.value = model_name;
 	frm.basic.value = basic;
 	frm.model_level.value = model_level;
 	if(organization_img != ''){
 		document.getElementById('organization_img_area').innerHTML =\"<img src='".$admin_config[mall_data_root]."/images/member_group/\"+organization_img+\"' width='109'>\";
 	}else{
 		document.getElementById('organization_img_area').innerHTML =\"\";
 	}
//	alert(document.getElementById('organization_img_area').innerHTML);
/*

 	for(i=0;i < frm.model_level.length;i++){
 		if(frm.model_level[i].value == model_level){
 			frm.model_level[i].selected = true;
 		}
 	}
*/

    if(mem_cnt > 0){
        frm.disp[0].disabled = false;
        frm.disp[1].disabled = false;

        if(disp == '1'){
     		frm.disp[0].checked = true;
     	}else{
     		frm.disp[1].checked = true;
     	}
    }else{
        frm.disp[0].disabled = false;
        frm.disp[1].disabled = false;

     	if(disp == '1'){
     		frm.disp[0].checked = true;
     	}else{
     		frm.disp[1].checked = true;
     	}
    }

}

 function deleteGroupInfo(act, model_ix){
 	if(confirm(language_data['group.php']['A'][language])){
		//'해당모델 정보를 정말로 삭제하시겠습니까?'
 		var frm = document.group_frm;
 		frm.act.value = act;
 		frm.model_ix.value = model_ix;
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
$P->strLeftMenu = product_menu();
$P->Navigation = "상품관리 > 상품분류관리 > 모델관리";
$P->title = "모델관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table ".TBL_SHOP_GROUPINFO." (
model_ix int(4) unsigned not null auto_increment  ,
model_name varchar(20) null default null,
model_level int(2)  default '9' ,
sale_rate varchar(20) null default null,

disp char(1) default '1' ,
regdate datetime not null,
primary key(model_ix));
*/
?>