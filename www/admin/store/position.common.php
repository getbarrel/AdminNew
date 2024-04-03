<?


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width='15%' />
		<col width='30%' />
		<col width='*' />
	  <tr >
		<td align='left' colspan=3 > ".GetTitleNavigation("직급관리", "관리자설정 > 직급관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3 style='padding-bottom:12px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='tab_01'  >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  >";
						if($use_type == "unimind"){
							$Contents01 .= "<a href='user.php'>사용자목록</a>";
						}else{
							$Contents01 .= "<a href='admin_manage.php'>관리자목록</a>";
						}
						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='department.php'>부서관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' class='on'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='position.php?recommend=0'>직급관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>
	  <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>직급수정하기</b></div>")."</td>
	  </tr>
		</table>
		<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
		<col width='20%' />
		<col width='*' />
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>직급명 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='ps_name' value='".$db->dt[ps_name]."' style='width:230px;' validation='true' title='직급명'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 직급 이미지 </td>
	    <td class='input_box_item'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td><input type=file class='textbox' name='ps_img' value='' style='width:230px;'></td>
					<td align=left style='padding-left:10px;'>
						<span class=small><!--직급이미지가 있으실 때 입력해주시기 바랍니다. 109* 27-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
						<div id='ps_img_area' ></div>
					</td>
				</tr>
			</table>
	    </td>

	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>직급등급 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='ps_level' value='".$db->dt[ps_level]."' style='width:60px;' validation='true' title='직급등급'>";
/*
$Contents01 .= "<select name='ps_level' style='font-size:12px;'>";
	for($i=0;$i < 10;$i++){
		$Contents01 .= "<option value='$i' ".($db->dt[ps_level] == $i ? "checked":"").">$i</option>";
	}
$Contents01 .= "</select>";
*/
$Contents01 .= "
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
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
		  <u>직급</u>으로 이용하실 직급를 입력해주세요
	</td>
</tr>
</table>
";*/
 $ContentsDesc01 =getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents02 =colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>직급목록</b></div>");
$Contents02 .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
	   <col style='width:80px;'>
	    <col style='width:190px;'>
	    <col style='width:*;'>
	    <col style='width:100px;'>
	    <!--col style='width:60px;'-->
	    <col style='width:100px;'>
	    <!--col style='width:170px;'-->
	    <col style='width:150px;'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 번호</td>
	    <td class='m_td'> 직급명</td>
	    <td class='m_td'> 직급이미지</td>
	    <td class='m_td'> 직급등급</td>
	    <!--td class='m_td'> 할인율</td-->
	    <td class='m_td'> 사용여부</td>
	    <!--td class='m_td'> 등록일자</td-->
	    <td class='e_td'> 관리</td>
	  </tr>";

$db = new Database;


$db->query("SELECT * FROM shop_company_position where company_id = '".$admininfo[company_id]."' order by ps_level asc ");


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".($i+1)."</td>
		    <td class='list_box_td point'>".$db->dt[ps_name]."</td>
		    <td class='list_box_td list_bg_gray' align=left>";

		    if ($db->dt[ps_img] != '' && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/".$db->dt[ps_img])){
			    $Contents02 .= "<img src='".$admin_config[mall_data_root]."/images/position/".$db->dt[ps_img]."' width=109>";
			  }
		    $Contents02 .= "
		    </td>
		    <td class='list_box_td '>".$db->dt[ps_level]."  </td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>

		    <td class='list_box_td '>";

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents02 .= "
					<a href=\"javascript:updatepositionInfo('".$db->dt[ps_ix]."','".$db->dt[sale_rate]."','".$db->dt[ps_name]."','".$db->dt[ps_name]."','".$db->dt[ps_id]."','".trim($db->dt[ps_img])."','".$db->dt[ps_level]."','".$db->dt[sale_rate]."','".$db->dt[memberreg_baymoney]."','".$db->dt[use_mall_yn]."','".$db->dt[disp]."','".$db->dt[basic]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
				}else{
					$Contents02 .= "
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
				}
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents02 .= "
					<a href=\"javascript:deletepositionInfo('delete','".$db->dt[ps_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}else{
					//$Contents02 .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}

	    		$Contents02 .= "
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 직급이 없습니다. </td>
		  </tr>
		  <tr height=1><td colspan=7 class='dot-x'></td></tr>	  ";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=7 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

	  </table>";



if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table>
";
}


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='position_frm' action='position.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data'>
<input name='act' type='hidden' value='insert'>
<input name='ps_ix' type='hidden' value=''>
<input name='basic' type='hidden' value=''>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원 직급정보는 위와 같이 9단계의 등급으로 이루어져 있으며 '직급등급' 은 수정이 불가능하며 각 등급에 맞는 명칭을 변경해서 사용하셔야 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용하지 않으실 직급정보는 수정버튼을 클릭하신후 사용하지 않음으로 선택하신후 저장 버튼을 누르시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용유무가 사용으로 되어 있는 직급만 사용하실수 있게 됩니다</td></tr>
</table>
";*/
 $help_text =getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= HelpBox("직급관리", $help_text, 60);

 $Script = "
 <script language='javascript'>
 function updatepositionInfo(ps_ix,sale_rate,ps_name,ps_name, ps_id,ps_img, ps_level,sale_rate,memberreg_baymoney,use_mall_yn, disp, basic){
 	var frm = document.position_frm;

 	frm.act.value = 'update';
 	frm.ps_ix.value = ps_ix;
 	//frm.sale_rate.value = sale_rate;
 	frm.ps_name.value = ps_name;
 	frm.basic.value = basic;
 	frm.ps_level.value = ps_level;
 	if(ps_img != ''){
 		document.getElementById('ps_img_area').innerHTML =\"<img src='".$admin_config[mall_data_root]."/images/position/\"+ps_img+\"' width='109'>\";
 	}else{
 		document.getElementById('ps_img_area').innerHTML =\"\";
 	}
//	alert(document.getElementById('ps_img_area').innerHTML);
/*

 	for(i=0;i < frm.ps_level.length;i++){
 		if(frm.ps_level[i].value == ps_level){
 			frm.ps_level[i].selected = true;
 		}
 	}
*/
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

 function deletepositionInfo(act, ps_ix){
 	if(confirm('해당직급 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.position_frm;
 		frm.act.value = act;
 		frm.ps_ix.value = ps_ix;
 		frm.submit();
 	}
}
 </script>
 ";


?>